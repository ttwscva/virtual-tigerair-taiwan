<?php
/**
 * Virtual Tigerair Taiwan - 共用資料存取函式
 * 由 admin/index.php、admin/tags.php、api/news.php 共同引用
 */

define('VTTW_DATA_PATH', __DIR__ . '/data');
define('VTTW_NEWS_FILE', VTTW_DATA_PATH . '/news.json');
define('VTTW_TAGS_FILE', VTTW_DATA_PATH . '/tags.json');
define('VTTW_CONFIG_FILE', VTTW_DATA_PATH . '/config.json');

// 標籤可用顏色（紅色 / 黃色）
const VTTW_TAG_COLORS = [
    'red'    => '紅色',
    'yellow' => '黃色',
];

function vttwReadJson($file) {
    if (!file_exists($file)) {
        return [];
    }
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function vttwWriteJson($file, $data) {
    if (!is_dir(dirname($file))) {
        mkdir(dirname($file), 0755, true);
    }
    return file_put_contents($file, json_encode(array_values($data), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}

function vttwGetTags() {
    return vttwReadJson(VTTW_TAGS_FILE);
}

// 以 id 為 key 的標籤對照表
function vttwGetTagMap() {
    $map = [];
    foreach (vttwGetTags() as $tag) {
        $map[$tag['id']] = $tag;
    }
    return $map;
}

// 下拉選單顯示文字：重要公告 | Notice | 重要告知
function vttwTagLabel($tag) {
    return ($tag['zh'] ?? '') . ' | ' . ($tag['en'] ?? '') . ' | ' . ($tag['ja'] ?? '');
}

/**
 * 將公告的 tag_id 解析為前台使用的 badge / badge_type
 * 標籤若已被刪除，則退回公告內的舊 badge 資料（相容舊格式）
 */
function vttwResolveNewsTag($item, $tagMap) {
    $tagId = $item['tag_id'] ?? '';
    if ($tagId !== '' && isset($tagMap[$tagId])) {
        $tag = $tagMap[$tagId];
        $item['badge_type'] = $tag['color'];
        $item['badge'] = [
            'zh' => $tag['zh'] ?? '',
            'en' => $tag['en'] ?? '',
            'ja' => $tag['ja'] ?? '',
        ];
    } else {
        $item['badge_type'] = $item['badge_type'] ?? 'red';
        $item['badge'] = $item['badge'] ?? ['zh' => '公告', 'en' => 'News', 'ja' => 'お知らせ'];
    }
    return $item;
}

