<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// ✅ ڕێڕەوی ڕاستی autoload
require __DIR__ . '/../vendor/autoload.php';


use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// ✅ کۆنفیگێکردنی Cloudinary
Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dy9bzsux3',
        'api_key'    => '121194676628732',
        'api_secret' => 'DEqYgUH86qGwo2myI8VFpqFamoI'
    ],
    'url' => ['secure' => true]
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ دڵنیابە لە فایلەکان نێردراون
    if (!isset($_FILES['file'])) {
        echo json_encode(['success' => false, 'error' => 'No file uploaded']);
        exit;
    }

    $tmpFilePath = $_FILES['file']['tmp_name'];

    // ✅ بارکردنی فایل بۆ Cloudinary
    try {
        $uploadResult = (new UploadApi())->upload($tmpFilePath, [
            'folder' => 'o_data_uploads'
        ]);

        echo json_encode([
            'success' => true,
            'url' => $uploadResult['secure_url']
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }

} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
}
