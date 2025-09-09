<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

function generateQrBase64(string $text): string {
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($text)
        ->size(200)
        ->margin(10)
        ->build();
    return 'data:image/png;base64,' . base64_encode($result->getString());
}
