<?php
require_once __DIR__ . '/../vendor/phpqrcode/qrlib.php';

function generateOrderQR($order_id) {
    $order_url = "http://localhost/tamccdeli/staff/order-details.php?id=" . $order_id;
    $tempDir = __DIR__ . '/../assets/qrcodes/';
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }
    $fileName = 'order_' . $order_id . '.png';
    $pngAbsoluteFilePath = $tempDir . $fileName;
    if (!file_exists($pngAbsoluteFilePath)) {
        QRcode::png($order_url, $pngAbsoluteFilePath, QR_ECLEVEL_L, 4);
    }
    return '/tamccdeli/assets/qrcodes/' . $fileName;
}
?>