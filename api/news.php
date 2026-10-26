<?php
/**
 * Virtual Tigerair Taiwan - 最新消息 API
 * 讀取 data/news.json 並輸出 JSON 給前端渲染
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

$dataFile = __DIR__ . '/../data/news.json';

if (!file_exists($dataFile)) {
    echo json_encode([
        'status' => 'success',
        'count' => 0,
        'data' => []
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

$jsonContent = file_get_contents($dataFile);
$newsData = json_decode($jsonContent, true);

if (!is_array($newsData)) {
    $newsData = [];
}

echo json_encode([
    'status' => 'success',
    'count' => count($newsData),
    'data' => $newsData
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

