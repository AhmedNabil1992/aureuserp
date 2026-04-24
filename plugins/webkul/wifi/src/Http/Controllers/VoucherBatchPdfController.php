<?php

namespace Webkul\Wifi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use TCPDF;
use Webkul\Wifi\Models\DynamicClient;
use Webkul\Wifi\Models\Voucher;
use Webkul\Wifi\Models\WifiVoucherBatch;

class VoucherBatchPdfController extends Controller
{
    public function download(string $batchCode): Response
    {
        $batch = WifiVoucherBatch::query()
            ->with(['purchase.package.product'])
            ->where('batch_code', $batchCode)
            ->first();

        if (! $batch) {
            return $this->notFoundResponse('Voucher batch not found.');
        }

        $voucherCodes = Voucher::query()
            ->where('batch', $batchCode)
            ->orderBy('name')
            ->pluck('name')
            ->all();

        if ($voucherCodes === []) {
            return $this->notFoundResponse('Generated vouchers were not found for this batch.');
        }

        $dynamicClient = null;

        if (filled($batch->nasidentifier)) {
            $dynamicClient = DynamicClient::query()
                ->where('nasidentifier', $batch->nasidentifier)
                ->first();
        }

        $caption = $batch->caption ?: ($batch->purchase?->package?->product?->name ?? 'Wi-Fi Voucher');
        $imagePath = $this->resolveImagePath($dynamicClient?->Picture);

        $cacheDirectory = storage_path('app/tcpdf_temp/');

        if (! is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0755, true);
        }

        if (! defined('K_PATH_CACHE')) {
            define('K_PATH_CACHE', $cacheDirectory);
        }

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AureusERP');
        $pdf->SetAuthor('AureusERP');
        $pdf->SetTitle('WiFi Vouchers');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(true, 5);
        $pdf->setImageScale(1.25);
        $pdf->setLanguageArray([
            'a_meta_charset'  => 'UTF-8',
            'a_meta_language' => 'ar',
            'w_page'          => 'page',
        ]);
        $pdf->SetFont('dejavusans', '', 12);

        $leftMargin = 7;
        $topMargin = 3;
        $rightMargin = 7;
        $padding = 2;
        $cols = 4;
        $rows = 10;
        $cardHeight = 25;
        $imageHeight = 19;
        $cellHeight = 6;
        $cardWidth = ($pdf->getPageWidth() - $leftMargin - $rightMargin - ($padding * ($cols + 1))) / $cols;
        $leftCellWidth = $cardWidth / 2;
        $rightCellWidth = $cardWidth - $leftCellWidth;

        $pdf->SetMargins($leftMargin, $topMargin, $rightMargin);

        foreach ($voucherCodes as $index => $code) {
            if ($index % ($cols * $rows) === 0) {
                $pdf->AddPage();
            }

            $position = $index % ($cols * $rows);
            $column = $position % $cols;
            $row = (int) floor($position / $cols);

            $x = $leftMargin + $padding + ($column * ($cardWidth + $padding));
            $y = $topMargin + $padding + ($row * ($cardHeight + $padding));

            $pdf->SetLineWidth(0.2);
            $pdf->Rect($x, $y, $cardWidth, $cardHeight);
            $pdf->Rect($x, $y, $cardWidth, $imageHeight);

            if ($imagePath !== null) {
                $pdf->Image($imagePath, $x, $y, $cardWidth, $imageHeight, '', '', '', false);
            }

            $pdf->Line($x, $y + $imageHeight, $x + $cardWidth, $y + $imageHeight);

            $pdf->SetXY($x, $y + $imageHeight);
            $pdf->SetFont('times', '', 12);
            $pdf->Cell($leftCellWidth, $cellHeight, $code, 1, 0, 'C', false, '', 1);

            $pdf->SetXY($x + $leftCellWidth, $y + $imageHeight);
            $pdf->SetFont('dejavusans', '', 12);
            $pdf->Cell($rightCellWidth, $cellHeight, $caption, 1, 1, 'C', false, '', 1);
        }

        $content = $pdf->Output($batchCode.'.pdf', 'S');

        return response($content, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$batchCode.'.pdf"',
        ]);
    }

    private function notFoundResponse(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 404);
    }

    private function resolveImagePath(?string $picturePath): ?string
    {
        if (blank($picturePath)) {
            return null;
        }

        $absolutePath = public_path('storage/'.ltrim($picturePath, '/'));

        if (! is_file($absolutePath)) {
            return null;
        }

        return $absolutePath;
    }
}
