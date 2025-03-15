<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// ✅ ڕێڕەوی ڕاستی autoload بۆ Cloudinary
require __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// ✅ ڕێکخستنی Cloudinary
Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dy9bzsux3',
        'api_key'    => '121194676628732',
        'api_secret' => 'DEqYgUH86qGwo2myI8VFpqFamoI'
    ],
    'url' => ['secure' => true]
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ دڵنیابە لە فایل نێردراوە
    if (!isset($_FILES['file'])) {
        echo json_encode(['success' => false, 'error' => 'هیچ فایلێک نەنێردراوە!']);
        exit;
    }

    $tmpFilePath = $_FILES['file']['tmp_name'];

    // ✅ ڕێکخستنی ڕێڕەوی فۆڵدەری ساڵ / مانگ / ڕۆژ
    $year  = date('Y');
    $month = date('m');
    $day   = date('d');
    $folderPath = "o_data_uploads/$year/$month/$day";

    // ✅ دروستکردنی ناوی تایبەتی بۆ فایلەکە (تکرار نەبێت)
    $timestamp = time();
    $uniqueName = 'file_' . $timestamp . '_' . uniqid();

    // ✅ بارکردنی فایل بۆ Cloudinary
    try {
        $uploadResult = (new UploadApi())->upload($tmpFilePath, [
            'folder'     => $folderPath,
            'public_id'  => $uniqueName,  // ناوی تایبەتی فایل
            'overwrite'  => true,          // ئەگەر هاوپێچی هەبوو، نوێبکرێتەوە
            'resource_type' => 'image'
        ]);

        echo json_encode([
            'success' => true,
            'url'     => $uploadResult['secure_url'],
            'folder'  => $folderPath,
            'name'    => $uniqueName
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error'   => $e->getMessage()
        ]);
    }

} else {
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid request method!'
    ]);
}

