<?php
/**
 * Virtual Tigerair Taiwan - 最新消息 API
 * 讀取 data/news.json，並依 data/tags.json 解析公告標籤後輸出 JSON 給前端渲染
 */

require_once __DIR__ . '/../common.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

$newsData = vttwReadJson(VTTW_NEWS_FILE);
$tagMap = vttwGetTagMap();

$output = array_map(function ($item) use ($tagMap) {
    return vttwResolveNewsTag($item, $tagMap);
}, $newsData);

echo json_encode([
    'status' => 'success',
    'count' => count($output),
    'data' => array_values($output)
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
