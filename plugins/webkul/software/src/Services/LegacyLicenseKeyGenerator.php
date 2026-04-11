<?php

namespace Webkul\Software\Services;

use DateTimeInterface;
use RuntimeException;

class LegacyLicenseKeyGenerator
{
    private const BASE32_TABLE = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K',
        'L', 'M', 'N', 'P', 'R', 'S', 'T', 'V', 'W', 'X',
        'Y', 'Z',
    ];

    private const EDITIONS = [
        0 => 'STANDARD',
        1 => 'PROFESSIONAL',
        2 => 'ENTERPRISE',
        3 => 'ULTIMATE',
    ];

    private const LICENSE_TYPES = [
        0 => 'TRIAL',
        1 => 'FULL',
        2 => 'MONTHLY',
        3 => 'ANNUAL',
    ];

    public function generate(
        int $productCode,
        string $type,
        string $edition,
        string $computerId,
        DateTimeInterface $endDate,
        bool $isMain
    ): string {
        $licenseType = $this->normalizeLicenseType($type);
        $editionValue = $this->normalizeEdition($edition);

        $expirationDate = match ($licenseType) {
            1       => $endDate,
            2       => $endDate,
            3       => $endDate,
            0       => now()->addDays(7),
            default => throw new RuntimeException('Unsupported license type.'),
        };

        $payload = $this->buildPayload(
            header: 9,
            productCode: $this->toByte($productCode),
            version: $isMain ? 1 : 0,
            edition: $editionValue,
            type: $licenseType,
            expirationDate: $expirationDate,
            footer: 6,
        );

        $encrypted = $this->encryptPayload($payload, $computerId);
        $finalBytes = substr($encrypted, 0, 8)
            .chr(9)
            .substr($payload, 9, 1)
            .substr($encrypted, 8);

        $base32 = $this->toBase32($finalBytes);

        return implode('-', str_split($base32, 5));
    }

    /**
     * @return array{
     *     product_code:int,
     *     license_type:string,
     *     edition:string,
     *     is_main:int,
     *     expiration:?string,
     *     header:int,
     *     footer:int
     * }
     */
    public function inspect(string $productKey, string $computerId): array
    {
        $encryptedPayload = $this->extractEncryptedPayloadFromKey($productKey);
        $decrypted = $this->decryptPayload($encryptedPayload, $computerId);

        if (strlen($decrypted) < 11) {
            throw new RuntimeException('Invalid license payload.');
        }

        $header = ord($decrypted[0]);
        $productCode = ord($decrypted[1]);
        $version = ord($decrypted[2]);
        $editionCode = ord($decrypted[3]);
        $typeCode = ord($decrypted[4]);
        $expirationRaw = unpack('V', substr($decrypted, 5, 4))[1];
        $footer = ord($decrypted[10]);

        $licenseType = self::LICENSE_TYPES[$typeCode] ?? throw new RuntimeException('Unknown license type in key.');
        $edition = self::EDITIONS[$editionCode] ?? throw new RuntimeException('Unknown edition in key.');

        return [
            'product_code' => $productCode,
            'license_type' => $licenseType,
            'edition'      => $edition,
            'is_main'      => $version,
            'expiration'   => $licenseType === 'FULL' ? null : $this->parseExpirationDate($expirationRaw),
            'header'       => $header,
            'footer'       => $footer,
        ];
    }

    public function isValid(string $productKey, string $computerId): bool
    {
        try {
            $this->inspect($productKey, $computerId);

            return true;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    private function buildPayload(
        int $header,
        int $productCode,
        int $version,
        int $edition,
        int $type,
        DateTimeInterface $expirationDate,
        int $footer
    ): string {
        $day = str_pad((string) $expirationDate->format('d'), 2, '0', STR_PAD_LEFT);
        $month = str_pad((string) $expirationDate->format('m'), 2, '0', STR_PAD_LEFT);
        $year = $expirationDate->format('Y');
        $expirationPacked = (int) ($day.$month.$year);

        return pack('C', $header)
            .pack('C', $productCode)
            .pack('C', $version)
            .pack('C', $edition)
            .pack('C', $type)
            .pack('V', $expirationPacked)
            .pack('C', random_int(0, 254))
            .pack('C', $footer);
    }

    private function encryptPayload(string $payload, string $computerId): string
    {
        [$key, $iv] = $this->deriveLegacyKeyAndIv($computerId);

        $encrypted = openssl_encrypt(
            $payload,
            'aes-256-cbc',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
        );

        if (! is_string($encrypted)) {
            throw new RuntimeException('Legacy encryption failed.');
        }

        return $encrypted;
    }

    private function decryptPayload(string $payload, string $computerId): string
    {
        [$key, $iv] = $this->deriveLegacyKeyAndIv($computerId);

        $decrypted = openssl_decrypt(
            $payload,
            'aes-256-cbc',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
        );

        if (! is_string($decrypted)) {
            throw new RuntimeException('Legacy decryption failed.');
        }

        return $decrypted;
    }

    /**
     * @return array{0:string,1:string}
     */
    private function deriveLegacyKeyAndIv(string $computerId): array
    {
        $secretBytes = array_values(unpack('C*', $computerId));

        if (count($secretBytes) < 32 || count($secretBytes) > 39) {
            throw new RuntimeException('Computer ID length is incompatible with legacy algorithm.');
        }

        $keyBytes = array_slice($secretBytes, 0, 32);
        $ivSlice = array_reverse(array_slice($secretBytes, 23));
        $ivBytes = array_pad($ivSlice, 16, 0);

        return [
            pack('C*', ...$keyBytes),
            pack('C*', ...$ivBytes),
        ];
    }

    private function normalizeLicenseType(string $type): int
    {
        return match (strtoupper($type)) {
            'TRIAL'   => 0,
            'FULL'    => 1,
            'MONTHLY' => 2,
            'ANNUAL'  => 3,
            default   => throw new RuntimeException('Invalid license type.'),
        };
    }

    private function normalizeEdition(string $edition): int
    {
        return match (strtoupper($edition)) {
            'STANDARD'     => 0,
            'PROFESSIONAL' => 1,
            'ENTERPRISE'   => 2,
            'ULTIMATE'     => 3,
            default        => throw new RuntimeException('Invalid edition.'),
        };
    }

    private function toByte(int $value): int
    {
        if ($value < 0 || $value > 255) {
            throw new RuntimeException('Product code must be between 0 and 255.');
        }

        return $value;
    }

    private function toBase32(string $binary): string
    {
        $buffer = array_values(unpack('C*', $binary));

        if ((count($buffer) % 3) !== 0) {
            throw new RuntimeException('Input data incorrect. Required multiple of 3 bytes length.');
        }

        $output = '';

        for ($index = 0; $index < count($buffer); $index += 3) {
            $b0 = $buffer[$index];
            $b1 = $buffer[$index + 1];
            $b2 = $buffer[$index + 2];

            $output .= self::BASE32_TABLE[($b0 & 0b1111_1000) >> 3];
            $output .= self::BASE32_TABLE[(($b0 & 0b0000_0111) << 2) | (($b1 & 0b1100_0000) >> 6)];
            $output .= self::BASE32_TABLE[($b1 & 0b0011_1110) >> 1];
            $output .= self::BASE32_TABLE[(($b1 & 0b0000_0001) << 4) | (($b2 & 0b1111_0000) >> 4)];
            $output .= self::BASE32_TABLE[$b2 & 0b0000_1111];
        }

        return $output;
    }

    private function fromBase32(string $base32): string
    {
        $normalized = strtoupper(str_replace('-', '', trim($base32)));

        if ((strlen($normalized) % 5) !== 0) {
            throw new RuntimeException('Base32 input string incorrect. Required multiple of 5 character length.');
        }

        $output = '';

        for ($index = 0; $index < strlen($normalized); $index += 5) {
            $c0 = $this->base32Value($normalized[$index]);
            $c1 = $this->base32Value($normalized[$index + 1]);
            $c2 = $this->base32Value($normalized[$index + 2]);
            $c3 = $this->base32Value($normalized[$index + 3]);
            $c4 = $this->base32Value($normalized[$index + 4]);

            $packed = ($c0 << 19) | ($c1 << 14) | ($c2 << 9) | ($c3 << 4) | $c4;

            $output .= chr(($packed >> 16) & 0xFF);
            $output .= chr(($packed >> 8) & 0xFF);
            $output .= chr($packed & 0xFF);
        }

        return $output;
    }

    private function base32Value(string $char): int
    {
        $char = strtoupper($char);

        if (in_array($char, ['I', 'O', 'Q', 'U'], true)) {
            throw new RuntimeException('Invalid base32 character.');
        }

        $value = array_search($char, self::BASE32_TABLE, true);

        if ($value === false) {
            throw new RuntimeException('Invalid base32 character.');
        }

        return (int) $value;
    }

    private function extractEncryptedPayloadFromKey(string $productKey): string
    {
        $raw = $this->fromBase32($productKey);

        if (strlen($raw) < 10) {
            throw new RuntimeException('Invalid key length.');
        }

        return substr($raw, 0, 8)
            .substr($raw, 10);
    }

    private function parseExpirationDate(int $packed): string
    {
        $raw = str_pad((string) $packed, 8, '0', STR_PAD_LEFT);

        $day = (int) substr($raw, 0, 2);
        $month = (int) substr($raw, 2, 2);
        $year = (int) substr($raw, 4, 4);

        if (! checkdate($month, $day, $year)) {
            throw new RuntimeException('Invalid expiration date in key.');
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
