<?php

declare(strict_types=1);

namespace Webkul\Software\Services;

use Throwable;

class EmailValidationService
{
    /**
     * @var array<int, string>
     */
    private const DISPOSABLE_DOMAINS = [
        'mailinator.com',
        'tempmail.com',
        '10minutemail.com',
        'guerrillamail.com',
        'sharklasers.com',
        'yopmail.com',
        'trashmail.com',
        'dispostable.com',
        'getnada.com',
        'temp-mail.org',
        'maildrop.cc',
        'fakemailgenerator.com',
    ];

    /**
     * @return array{valid: bool, message: string}
     */
    public static function validate(string $email): array
    {
        $email = strtolower(trim($email));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return static::invalid('format');
        }

        [, $domain] = explode('@', $email, 2);

        if (in_array($domain, self::DISPOSABLE_DOMAINS, true)) {
            return static::invalid('disposable');
        }

        $mxHosts = static::resolveMxHosts($domain);

        if ($mxHosts === []) {
            return static::invalid('domain', ['domain' => $domain]);
        }

        $smtpResult = static::verifyMailboxSmtp($email, $mxHosts[0]);

        if ($smtpResult['checked'] && ! $smtpResult['exists']) {
            return static::invalid('mailbox', ['email' => $email]);
        }

        return [
            'valid'   => true,
            'message' => __('software::filament/customer/license.table.actions.shift_emails.validation.valid'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function resolveMxHosts(string $domain): array
    {
        $hosts = [];
        $weights = [];

        if (function_exists('getmxrr')) {
            getmxrr($domain, $hosts, $weights);
        }

        if ($hosts === [] && function_exists('checkdnsrr') && checkdnsrr($domain, 'MX')) {
            return [$domain];
        }

        if ($weights !== [] && count($weights) === count($hosts)) {
            array_multisort($weights, SORT_ASC, SORT_NUMERIC, $hosts);
        }

        return $hosts;
    }

    /**
     * @return array{checked: bool, exists: bool}
     */
    private static function verifyMailboxSmtp(string $email, string $mxHost): array
    {
        $timeout = 5;
        $connection = @fsockopen($mxHost, 25, $errorCode, $errorMessage, $timeout);

        if ($connection === false) {
            return ['checked' => false, 'exists' => true];
        }

        try {
            stream_set_timeout($connection, $timeout);

            $greeting = static::readSmtpResponse($connection);

            if (! str_starts_with($greeting, '220')) {
                fclose($connection);

                return ['checked' => false, 'exists' => true];
            }

            $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';
            $from = (string) config('mail.from.address', "verify@{$host}");

            fwrite($connection, "EHLO {$host}\r\n");
            static::readSmtpResponse($connection);

            fwrite($connection, "MAIL FROM: <{$from}>\r\n");
            static::readSmtpResponse($connection);

            fwrite($connection, "RCPT TO: <{$email}>\r\n");
            $recipientResponse = static::readSmtpResponse($connection);
            $responseCode = substr($recipientResponse, 0, 3);

            fwrite($connection, "QUIT\r\n");
            fclose($connection);

            if (
                in_array($responseCode, ['501', '550', '551', '552', '553', '554'], true)
                || str_contains(strtolower($recipientResponse), 'user unknown')
                || str_contains(strtolower($recipientResponse), 'does not exist')
            ) {
                return ['checked' => true, 'exists' => false];
            }

            if (in_array($responseCode, ['250', '251'], true)) {
                return ['checked' => true, 'exists' => true];
            }

            return ['checked' => false, 'exists' => true];
        } catch (Throwable) {
            if (is_resource($connection)) {
                fclose($connection);
            }

            return ['checked' => false, 'exists' => true];
        }
    }

    /**
     * @param  resource  $connection
     */
    private static function readSmtpResponse($connection): string
    {
        $response = '';

        while (($line = fgets($connection, 512)) !== false) {
            $response .= $line;

            if (($line[3] ?? null) === ' ') {
                break;
            }
        }

        return $response;
    }

    /**
     * @param  array<string, string>  $replace
     * @return array{valid: false, message: string}
     */
    private static function invalid(string $reason, array $replace = []): array
    {
        return [
            'valid'   => false,
            'message' => __("software::filament/customer/license.table.actions.shift_emails.validation.{$reason}", $replace),
        ];
    }
}
