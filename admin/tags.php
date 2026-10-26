<?php
/**
 * Virtual Tigerair Taiwan - 公告標籤管理 (Tag Management)
 * 標籤顏色：紅色 / 黃色；標籤文字支援中 / 英 / 日三語手動輸入
 */

session_start();

require_once __DIR__ . '/../common.php';

// 未登入則導回登入頁
if (empty($_SESSION['vttw_admin'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$messageType = 'info';

// 計算各標籤被多少則公告使用
function countTagUsage() {
    $usage = [];
    foreach (vttwReadJson(VTTW_NEWS_FILE) as $news) {
        $tagId = $news['tag_id'] ?? '';
        if ($tagId !== '') {
            $usage[$tagId] = ($usage[$tagId] ?? 0) + 1;
        }
    }
    return $usage;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $tags = vttwGetTags();

    $color = $_POST['color'] ?? 'red';
    if (!array_key_exists($color, VTTW_TAG_COLORS)) {
        $color = 'red';
    }
    $zh = trim($_POST['zh'] ?? '');
    $en = trim($_POST['en'] ?? '');
    $ja = trim($_POST['ja'] ?? '');

    // 新增標籤
    if ($action === 'create') {
        if ($zh === '' || $en === '' || $ja === '') {
            $message = '中、英、日三種標籤文字皆為必填！';
            $messageType = 'danger';
        } else {
            $tags[] = [
                'id' => 'tag_' . round(microtime(true) * 1000),
                'color' => $color,
                'zh' => $zh,
                'en' => $en,
                'ja' => $ja,
            ];
            vttwWriteJson(VTTW_TAGS_FILE, $tags);
            $message = '標籤已成功新增！';
            $messageType = 'success';
        }
    }

    // 編輯標籤
    elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        if ($zh === '' || $en === '' || $ja === '') {
            $message = '中、英、日三種標籤文字皆為必填！';
            $messageType = 'danger';
        } else {
            foreach ($tags as &$tag) {
                if ($tag['id'] === $id) {
                    $tag['color'] = $color;
                    $tag['zh'] = $zh;
                    $tag['en'] = $en;
                    $tag['ja'] = $ja;
                    break;
                }
            }
            unset($tag);
            vttwWriteJson(VTTW_TAGS_FILE, $tags);
            $message = '標籤已成功更新！使用此標籤的公告將同步顯示新內容。';
            $messageType = 'success';
        }
    }

    // 刪除標籤（若仍有公告使用則禁止刪除）
    elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $usage = countTagUsage();
        if (!empty($usage[$id])) {
            $message = '無法刪除：仍有 ' . $usage[$id] . ' 則公告正在使用此標籤，請先更換這些公告的標籤。';
            $messageType = 'danger';
        } else {
            $tags = array_filter($tags, function ($tag) use ($id) {
                return $tag['id'] !== $id;
            });
            vttwWriteJson(VTTW_TAGS_FILE, $tags);
            $message = '標籤已成功刪除！';
            $messageType = 'success';
        }
    }

    // 排序調整（影響公告下拉選單的順序）
    elseif ($action === 'move') {
        $id = $_POST['id'] ?? '';
        $direction = $_POST['direction'] ?? '';
        $tags = array_values($tags);
        foreach ($tags as $i => $tag) {
            if ($tag['id'] === $id) {
                $target = $direction === 'up' ? $i - 1 : $i + 1;
                if ($target >= 0 && $target < count($tags)) {
                    [$tags[$i], $tags[$target]] = [$tags[$target], $tags[$i]];
                    vttwWriteJson(VTTW_TAGS_FILE, $tags);
                    $message = '標籤排序已更新！';
                    $messageType = 'success';
                }
                break;
            }
        }
    }
}

$tagList = array_values(vttwGetTags());
$usage = countTagUsage();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>標籤管理 | Virtual Tigerair Taiwan</title>
    <link rel="icon" type="image/png" href="../assets/favicon.png?v=3">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700;800&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
    <style>
        .color-options {
            display: flex;
            gap: 12px;
        }

        .color-option {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1.5px solid var(--tiger-border);
            border-radius: 8px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.9rem;
            transition: border-color 0.2s, background 0.2s;
        }

        .color-option:has(input:checked) {
            border-color: var(--tiger-orange);
            background: #fff7ed;
        }

        .color-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            display: inline-block;
        }
        .color-dot.red { background: #dc2626; }
        .color-dot.yellow { background: #eab308; }

        .tag-preview-box {
            margin-top: 6px;
            padding: 12px 14px;
            background: #f8fafc;
            border: 1px dashed var(--tiger-border);
            border-radius: 8px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
            font-size: 0.8rem;
            color: var(--tiger-muted);
        }

        .usage-count {
            font-size: 0.82rem;
            color: var(--tiger-muted);
            font-weight: 700;
        }
    </style>
</head>
<body>

    <!-- 頂部導航列 -->
    <header class="admin-nav">
        <a href="index.php" class="brand-box">
            <img src="../assets/Virtual_Tigerair_Taiwan_LOGO_cht_02.png" alt="Logo" class="brand-logo">
            <span class="brand-title">最新消息管理系統</span>
            <span class="brand-badge">ADMIN</span>
        </a>
        <div class="nav-actions">
            <a href="../index.html" target="_blank" class="btn btn-outline btn-sm">🌐 查看官網首頁</a>
            <a href="index.php" class="btn btn-outline btn-sm">📰 公告管理</a>
            <a href="tags.php" class="btn btn-outline btn-sm">🏷️ 標籤管理</a>
            <a href="index.php?action=logout" class="btn btn-danger btn-sm">登出</a>
        </div>
    </header>

    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <div>
                <h2 class="page-title">公告標籤管理</h2>
                <p style="font-size: 0.85rem; color: var(--tiger-muted); margin-top: 4px;">
                    標籤分為紅色與黃色兩種，文字可自訂中 / 英 / 日三語。修改標籤後，使用此標籤的公告會自動同步更新。
                </p>
            </div>
            <button class="btn btn-primary" onclick="openCreateModal()">
                ➕ 新增標籤
            </button>
        </div>

        <div class="news-table-wrap">
            <table class="news-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">排序</th>
                        <th style="width: 90px;">顏色</th>
                        <th>標籤文字（中 | 英 | 日）</th>
                        <th style="width: 110px;">使用公告數</th>
                        <th style="width: 120px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tagList)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">
                                目前尚無任何標籤，點選右上角「新增標籤」開始建立。
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tagList as $index => $tag): ?>
                            <?php $count = $usage[$tag['id']] ?? 0; ?>
                            <tr>
                                <td>
                                    <div style="display: flex; gap: 3px;">
                                        <form method="POST" action="tags.php" style="display:inline;">
                                            <input type="hidden" name="action" value="move">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($tag['id']) ?>">
                                            <input type="hidden" name="direction" value="up">
                                            <button type="submit" class="btn btn-outline btn-sm" style="padding: 2px 6px; font-size: 0.75rem; color: #64748b; border-color: #cbd5e1;<?= $index === 0 ? ' opacity:0.3; cursor:not-allowed;' : '' ?>" title="上移" <?= $index === 0 ? 'disabled' : '' ?>>▲</button>
                                        </form>
                                        <form method="POST" action="tags.php" style="display:inline;">
                                            <input type="hidden" name="action" value="move">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($tag['id']) ?>">
                                            <input type="hidden" name="direction" value="down">
                                            <button type="submit" class="btn btn-outline btn-sm" style="padding: 2px 6px; font-size: 0.75rem; color: #64748b; border-color: #cbd5e1;<?= $index === count($tagList) - 1 ? ' opacity:0.3; cursor:not-allowed;' : '' ?>" title="下移" <?= $index === count($tagList) - 1 ? 'disabled' : '' ?>>▼</button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; font-size: 0.85rem;">
                                        <span class="color-dot <?= htmlspecialchars($tag['color']) ?>"></span>
                                        <?= htmlspecialchars(VTTW_TAG_COLORS[$tag['color']] ?? $tag['color']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <span class="badge-tag <?= htmlspecialchars($tag['color']) ?>"><?= htmlspecialchars($tag['zh']) ?></span>
                                        <span class="badge-tag <?= htmlspecialchars($tag['color']) ?>"><?= htmlspecialchars($tag['en']) ?></span>
                                        <span class="badge-tag <?= htmlspecialchars($tag['color']) ?>"><?= htmlspecialchars($tag['ja']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="usage-count"><?= $count ?> 則</span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button type="button" class="btn btn-yellow btn-sm" onclick='openEditModal(<?= json_encode($tag, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) ?>)'>編輯</button>
                                        <?php if ($count > 0): ?>
                                            <button type="button" class="btn btn-danger btn-sm" style="opacity: 0.4; cursor: not-allowed;" title="仍有公告使用此標籤，無法刪除" disabled>刪除</button>
                                        <?php else: ?>
                                            <form method="POST" action="tags.php" onsubmit="return confirm('確定要刪除此標籤嗎？');" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($tag['id']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">刪除</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 新增 / 編輯標籤 Modal -->
    <div class="modal" id="tagModal">
        <div class="modal-content" style="max-width: 560px;">
            <div class="modal-header">
                <h3 id="modalTitle">新增標籤</h3>
                <button type="button" class="modal-close" onclick="closeTagModal()">&times;</button>
            </div>
            <form method="POST" action="tags.php" id="tagForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="tagIdField" value="">

                <div class="modal-body">
                    <div class="form-group">
                        <label>標籤顏色</label>
                        <div class="color-options">
                            <?php foreach (VTTW_TAG_COLORS as $colorKey => $colorName): ?>
                                <label class="color-option">
                                    <input type="radio" name="color" value="<?= $colorKey ?>" <?= $colorKey === 'red' ? 'checked' : '' ?> onchange="updatePreview()">
                                    <span class="color-dot <?= $colorKey ?>"></span>
                                    <?= htmlspecialchars($colorName) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tag_zh">🇹🇼 標籤文字（繁體中文）</label>
                        <input type="text" id="tag_zh" name="zh" class="form-control" placeholder="例如：重要公告" required maxlength="20" oninput="updatePreview()">
                    </div>
                    <div class="form-group">
                        <label for="tag_en">🇺🇸 標籤文字（English）</label>
                        <input type="text" id="tag_en" name="en" class="form-control" placeholder="e.g. Notice" required maxlength="30" oninput="updatePreview()">
                    </div>
                    <div class="form-group">
                        <label for="tag_ja">🇯🇵 標籤文字（日本語）</label>
                        <input type="text" id="tag_ja" name="ja" class="form-control" placeholder="例：重要告知" required maxlength="20" oninput="updatePreview()">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label>預覽</label>
                        <div class="tag-preview-box" id="tagPreview"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeTagModal()" style="color: #64748b; border-color: #cbd5e1;">取消</button>
                    <button type="submit" class="btn btn-primary">儲存標籤</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        function getSelectedColor() {
            const checked = document.querySelector('input[name="color"]:checked');
            return checked ? checked.value : 'red';
        }

        function setSelectedColor(color) {
            document.querySelectorAll('input[name="color"]').forEach(r => {
                r.checked = (r.value === color);
            });
        }

        // 即時預覽三語標籤樣式
        function updatePreview() {
            const color = getSelectedColor();
            const texts = ['tag_zh', 'tag_en', 'tag_ja'].map(id => document.getElementById(id).value.trim());
            const box = document.getElementById('tagPreview');
            if (texts.every(t => t === '')) {
                box.innerHTML = '輸入標籤文字後即可預覽';
                return;
            }
            box.innerHTML = texts
                .filter(t => t !== '')
                .map(t => `<span class="badge-tag ${color}">${escapeHtml(t)}</span>`)
                .join('');
        }

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = '新增標籤';
            document.getElementById('formAction').value = 'create';
            document.getElementById('tagIdField').value = '';
            document.getElementById('tagForm').reset();
            setSelectedColor('red');
            updatePreview();
            document.getElementById('tagModal').classList.add('active');
        }

        function openEditModal(tag) {
            document.getElementById('modalTitle').textContent = '編輯標籤';
            document.getElementById('formAction').value = 'update';
            document.getElementById('tagIdField').value = tag.id;
            setSelectedColor(tag.color || 'red');
            document.getElementById('tag_zh').value = tag.zh || '';
            document.getElementById('tag_en').value = tag.en || '';
            document.getElementById('tag_ja').value = tag.ja || '';
            updatePreview();
            document.getElementById('tagModal').classList.add('active');
        }

        // 與公告視窗一致：只能透過 × 或「取消」關閉，點擊背景不會關閉
        function closeTagModal() {
            document.getElementById('tagModal').classList.remove('active');
        }
    </script>
</body>
</html>

