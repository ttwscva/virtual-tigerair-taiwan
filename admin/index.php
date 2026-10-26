<?php
/**
 * Virtual Tigerair Taiwan - 最新消息管理後台 (Admin Panel)
 * 支援功能：密碼保護、中/英/日三語系公告 CRUD（新增、編輯、刪除、排序）
 */

session_start();

$dataPath = __DIR__ . '/../data';
$newsFile = $dataPath . '/news.json';
$configFile = $dataPath . '/config.json';

// 確保 data 目錄存在
if (!is_dir($dataPath)) {
    mkdir($dataPath, 0755, true);
}

// 預設管理員密碼（可在後台更改）
$defaultPassword = 'tiger2026';

// 讀取設定檔
function getConfig($configFile, $defaultPassword) {
    if (file_exists($configFile)) {
        $cfg = json_decode(file_get_contents($configFile), true);
        if (is_array($cfg) && !empty($cfg['admin_password'])) {
            return $cfg;
        }
    }
    return ['admin_password' => $defaultPassword];
}

$config = getConfig($configFile, $defaultPassword);

// 讀取新聞資料
function getNewsData($newsFile) {
    if (!file_exists($newsFile)) {
        return [];
    }
    $content = file_get_contents($newsFile);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

// 儲存新聞資料
function saveNewsData($newsFile, $data) {
    return file_put_contents($newsFile, json_encode(array_values($data), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

// 處理登入與登出
$message = '';
$messageType = 'info';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['vttw_admin']);
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // 登入驗證
    if ($action === 'login') {
        $inputPassword = $_POST['password'] ?? '';
        if ($inputPassword === $config['admin_password']) {
            $_SESSION['vttw_admin'] = true;
            header('Location: index.php');
            exit;
        } else {
            $message = '登入失敗：密碼不正確！';
            $messageType = 'danger';
        }
    }

    // 需登入後的管理操作
    if (!empty($_SESSION['vttw_admin'])) {
        $newsList = getNewsData($newsFile);

        // 修改後台密碼
        if ($action === 'change_password') {
            $newPassword = trim($_POST['new_password'] ?? '');
            if (strlen($newPassword) >= 6) {
                $config['admin_password'] = $newPassword;
                file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
                $message = '管理員密碼已成功更新！';
                $messageType = 'success';
            } else {
                $message = '密碼長度至少需 6 個字元！';
                $messageType = 'danger';
            }
        }

        // 新增公告
        elseif ($action === 'create') {
            $newItem = [
                'id' => 'news_' . round(microtime(true) * 1000),
                'date' => trim($_POST['date'] ?? date('Y-m-d')),
                'badge_type' => $_POST['badge_type'] ?? 'primary',
                'badge' => [
                    'zh' => trim($_POST['badge_zh'] ?? '重要公告'),
                    'en' => trim($_POST['badge_en'] ?? 'Notice'),
                    'ja' => trim($_POST['badge_ja'] ?? '重要告知')
                ],
                'title' => [
                    'zh' => trim($_POST['title_zh'] ?? ''),
                    'en' => trim($_POST['title_en'] ?? ''),
                    'ja' => trim($_POST['title_ja'] ?? '')
                ],
                'content' => [
                    'zh' => trim($_POST['content_zh'] ?? ''),
                    'en' => trim($_POST['content_en'] ?? ''),
                    'ja' => trim($_POST['content_ja'] ?? '')
                ],
                'link' => trim($_POST['link'] ?? '')
            ];

            // 預設將最新公告插在最上方
            array_unshift($newsList, $newItem);
            saveNewsData($newsFile, $newsList);
            $message = '公告已成功新增！';
            $messageType = 'success';
        }

        // 編輯公告
        elseif ($action === 'update') {
            $id = $_POST['id'] ?? '';
            foreach ($newsList as &$item) {
                if ($item['id'] === $id) {
                    $item['date'] = trim($_POST['date'] ?? $item['date']);
                    $item['badge_type'] = $_POST['badge_type'] ?? $item['badge_type'];
                    $item['badge'] = [
                        'zh' => trim($_POST['badge_zh'] ?? ''),
                        'en' => trim($_POST['badge_en'] ?? ''),
                        'ja' => trim($_POST['badge_ja'] ?? '')
                    ];
                    $item['title'] = [
                        'zh' => trim($_POST['title_zh'] ?? ''),
                        'en' => trim($_POST['title_en'] ?? ''),
                        'ja' => trim($_POST['title_ja'] ?? '')
                    ];
                    $item['content'] = [
                        'zh' => trim($_POST['content_zh'] ?? ''),
                        'en' => trim($_POST['content_en'] ?? ''),
                        'ja' => trim($_POST['content_ja'] ?? '')
                    ];
                    $item['link'] = trim($_POST['link'] ?? '');
                    break;
                }
            }
            saveNewsData($newsFile, $newsList);
            $message = '公告已成功更新！';
            $messageType = 'success';
        }

        // 刪除公告
        elseif ($action === 'delete') {
            $id = $_POST['id'] ?? '';
            $newsList = array_filter($newsList, function($item) use ($id) {
                return $item['id'] !== $id;
            });
            saveNewsData($newsFile, $newsList);
            $message = '公告已成功刪除！';
            $messageType = 'success';
        }

        // 排序調整 (上移 / 下移)
        elseif ($action === 'move') {
            $id = $_POST['id'] ?? '';
            $direction = $_POST['direction'] ?? '';
            $index = -1;
            foreach ($newsList as $i => $item) {
                if ($item['id'] === $id) {
                    $index = $i;
                    break;
                }
            }

            if ($index !== -1) {
                if ($direction === 'up' && $index > 0) {
                    $temp = $newsList[$index - 1];
                    $newsList[$index - 1] = $newsList[$index];
                    $newsList[$index] = $temp;
                    saveNewsData($newsFile, $newsList);
                    $message = '公告排序已更新！';
                    $messageType = 'success';
                } elseif ($direction === 'down' && $index < count($newsList) - 1) {
                    $temp = $newsList[$index + 1];
                    $newsList[$index + 1] = $newsList[$index];
                    $newsList[$index] = $temp;
                    saveNewsData($newsFile, $newsList);
                    $message = '公告排序已更新！';
                    $messageType = 'success';
                }
            }
        }
    }
}

$isLoggedIn = !empty($_SESSION['vttw_admin']);
$newsData = $isLoggedIn ? getNewsData($newsFile) : [];
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>最新消息後台管理 | Virtual Tigerair Taiwan</title>
    <link rel="icon" type="image/png" href="../assets/favicon.png?v=3">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700;800&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --tiger-orange: #f37021;
            --tiger-yellow: #ffc20e;
            --tiger-black: #1a1a1a;
            --tiger-bg: #f8fafc;
            --tiger-card: #ffffff;
            --tiger-border: #e2e8f0;
            --tiger-text: #1e293b;
            --tiger-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Noto Sans TC', sans-serif;
            background-color: var(--tiger-bg);
            color: var(--tiger-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* 導航列 */
        .admin-nav {
            background-color: var(--tiger-black);
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid var(--tiger-yellow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
        }

        .brand-logo {
            height: 38px;
            width: auto;
        }

        .brand-title {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .brand-badge {
            background: var(--tiger-orange);
            color: #fff;
            font-size: 0.72rem;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 700;
            margin-left: 6px;
        }

        .nav-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--tiger-orange);
            color: #ffffff;
        }
        .btn-primary:hover {
            background: #e05e10;
        }

        .btn-yellow {
            background: var(--tiger-yellow);
            color: var(--tiger-black);
        }
        .btn-yellow:hover {
            background: #e6ac05;
        }

        .btn-outline {
            background: transparent;
            color: #e2e8f0;
            border: 1px solid #475569;
        }
        .btn-outline:hover {
            background: rgba(255,255,255,0.1);
            color: #ffffff;
        }

        .btn-danger {
            background: #ef4444;
            color: #ffffff;
        }
        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 0.8rem;
        }

        /* 內容容器 */
        .container {
            max-width: 1100px;
            width: 100%;
            margin: 30px auto;
            padding: 0 20px;
            flex-grow: 1;
        }

        /* 訊息提示 */
        .alert {
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.92rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-info { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }

        /* 登入卡片 */
        .login-wrap {
            max-width: 420px;
            margin: 80px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border-top: 5px solid var(--tiger-yellow);
            padding: 35px 30px;
            text-align: center;
        }

        .login-wrap img {
            height: 48px;
            margin-bottom: 20px;
        }

        .login-wrap h2 {
            font-size: 1.35rem;
            margin-bottom: 8px;
            font-weight: 800;
        }

        .login-wrap p {
            font-size: 0.88rem;
            color: var(--tiger-muted);
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--tiger-border);
            border-radius: 6px;
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--tiger-orange);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 85px;
        }

        /* 公告列表區塊 */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            background: #ffffff;
            padding: 18px 24px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border-left: 5px solid var(--tiger-orange);
        }

        .page-title {
            font-size: 1.3rem;
            font-weight: 800;
        }

        .news-table-wrap {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            overflow: hidden;
            border: 1px solid var(--tiger-border);
        }

        table.news-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        table.news-table th {
            background: #f1f5f9;
            padding: 14px 16px;
            font-size: 0.82rem;
            color: #475569;
            font-weight: 800;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--tiger-border);
        }

        table.news-table td {
            padding: 16px;
            border-bottom: 1px solid var(--tiger-border);
            font-size: 0.9rem;
            vertical-align: middle;
        }

        table.news-table tr:last-child td {
            border-bottom: none;
        }

        table.news-table tr:hover {
            background-color: #fafbfc;
        }

        .badge-tag {
            font-size: 0.72rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .badge-tag.primary {
            background: #fff4ea;
            color: var(--tiger-orange);
            border: 1px solid rgba(243, 112, 33, 0.25);
        }
        .badge-tag.alert {
            background: #fef08a;
            color: #713f12;
            border: 1px solid rgba(234, 179, 8, 0.4);
        }

        .lang-previews {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .lang-item {
            font-size: 0.82rem;
            color: #475569;
        }

        .lang-item strong {
            display: inline-block;
            width: 28px;
            color: #0f172a;
        }

        .action-btns {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        /* Modal 彈跳視窗 */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(3px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-y: auto;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #ffffff;
            width: 100%;
            max-width: 780px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-header {
            background: var(--tiger-black);
            color: #ffffff;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--tiger-yellow);
        }

        .modal-header h3 {
            font-size: 1.15rem;
            font-weight: 800;
        }

        .modal-close {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
            line-height: 1;
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 14px 24px;
            background: #f8fafc;
            border-top: 1px solid var(--tiger-border);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* 語言分頁標籤 (Tabs) */
        .tabs {
            display: flex;
            border-bottom: 2px solid var(--tiger-border);
            margin-bottom: 18px;
            gap: 8px;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 9px 18px;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--tiger-muted);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }

        .tab-btn.active {
            color: var(--tiger-orange);
            border-bottom-color: var(--tiger-orange);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .page-header {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
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
            <?php if ($isLoggedIn): ?>
                <button type="button" class="btn btn-outline btn-sm" onclick="openPwdModal()">🔑 改密碼</button>
                <a href="index.php?action=logout" class="btn btn-danger btn-sm">登出</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!$isLoggedIn): ?>
            <!-- 登入卡片 -->
            <div class="login-wrap">
                <img src="../assets/Virtual_Tigerair_Taiwan_LOGO_cht_02.png" alt="Logo">
                <h2>管理員登入</h2>
                <p>請輸入管理密碼以維護最新公告內容</p>
                
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="password">後台登入密碼</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="預設密碼：tiger2026" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 11px;">
                        登入管理後台
                    </button>
                </form>
            </div>
        <?php else: ?>
            <!-- 管理介面 -->
            <div class="page-header">
                <div>
                    <h2 class="page-title">公告清單與發布管理</h2>
                    <p style="font-size: 0.85rem; color: var(--tiger-muted); margin-top: 4px;">
                        支援繁中、英文、日文三語即時發布，前台切換語系將無縫切換顯示。
                    </p>
                </div>
                <button class="btn btn-primary" onclick="openCreateModal()">
                    ➕ 新增公告
                </button>
            </div>

            <div class="news-table-wrap">
                <table class="news-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">排序</th>
                            <th style="width: 110px;">發布日期</th>
                            <th style="width: 90px;">徽章樣式</th>
                            <th>三語系標題預覽</th>
                            <th style="width: 120px;">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($newsData)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    目前尚無任何公告，點選右上角「新增公告」開始發布。
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($newsData as $index => $item): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; gap: 3px;">
                                            <form method="POST" action="index.php" style="display:inline;">
                                                <input type="hidden" name="action" value="move">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                                <input type="hidden" name="direction" value="up">
                                                <button type="submit" class="btn btn-outline btn-sm" style="padding: 2px 6px; font-size: 0.75rem;" title="上移" <?= $index === 0 ? 'disabled style="opacity:0.3;cursor:not-allowed;"' : '' ?>>▲</button>
                                            </form>
                                            <form method="POST" action="index.php" style="display:inline;">
                                                <input type="hidden" name="action" value="move">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                                <input type="hidden" name="direction" value="down">
                                                <button type="submit" class="btn btn-outline btn-sm" style="padding: 2px 6px; font-size: 0.75rem;" title="下移" <?= $index === count($newsData) - 1 ? 'disabled style="opacity:0.3;cursor:not-allowed;"' : '' ?>>▼</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td style="font-family: monospace; font-weight: 700;">
                                        <?= htmlspecialchars($item['date']) ?>
                                    </td>
                                    <td>
                                        <span class="badge-tag <?= htmlspecialchars($item['badge_type'] ?? 'primary') ?>">
                                            <?= htmlspecialchars($item['badge']['zh'] ?? '重要公告') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="lang-previews">
                                            <div class="lang-item"><strong>🇹🇼:</strong> <?= htmlspecialchars($item['title']['zh'] ?? '') ?></div>
                                            <div class="lang-item"><strong>🇺🇸:</strong> <?= htmlspecialchars($item['title']['en'] ?? '') ?></div>
                                            <div class="lang-item"><strong>🇯🇵:</strong> <?= htmlspecialchars($item['title']['ja'] ?? '') ?></div>
                                        </div>
                                        <?php if (!empty($item['link'])): ?>
                                            <div style="margin-top: 6px; font-size: 0.75rem; color: #2563eb;">
                                                🔗 連結：<?= htmlspecialchars($item['link']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button type="button" class="btn btn-yellow btn-sm" onclick='openEditModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>編輯</button>
                                            <form method="POST" action="index.php" onsubmit="return confirm('確定要刪除這筆公告嗎？此動作無法復原！');" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">刪除</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- 新增 / 編輯公告 Modal -->
    <div class="modal" id="newsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">新增公告</h3>
                <button type="button" class="modal-close" onclick="closeNewsModal()">&times;</button>
            </div>
            <form method="POST" action="index.php" id="newsForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="newsId" value="">

                <div class="modal-body">
                    <!-- 基本設定 -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="newsDate">發布日期</label>
                            <input type="date" id="newsDate" name="date" class="form-control" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group">
                            <label for="badgeType">標籤樣式 (Badge Style)</label>
                            <select id="badgeType" name="badge_type" class="form-control">
                                <option value="primary">橙色醒目 (重要公告 / 航務資訊)</option>
                                <option value="alert">黃色提示 (招募消息 / 提醒)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="newsLink">超連結網址 (選填，例如 join.html 或外部網址，若無請留空)</label>
                        <input type="text" id="newsLink" name="link" class="form-control" placeholder="例如：join.html 或 routes.html">
                    </div>

                    <!-- 三語系輸入標籤分頁 -->
                    <label style="font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">
                        多國語言內容設定：
                    </label>
                    <div class="tabs">
                        <button type="button" class="tab-btn active" onclick="switchTab('zh')">🇹🇼 繁體中文 (zh)</button>
                        <button type="button" class="tab-btn" onclick="switchTab('en')">🇺🇸 English (en)</button>
                        <button type="button" class="tab-btn" onclick="switchTab('ja')">🇯🇵 日本語 (ja)</button>
                    </div>

                    <!-- 繁體中文 -->
                    <div class="tab-content active" id="tab-zh">
                        <div class="form-group">
                            <label for="badge_zh">標籤文字 (繁中)</label>
                            <input type="text" id="badge_zh" name="badge_zh" class="form-control" placeholder="例如：重要公告、招募消息" required>
                        </div>
                        <div class="form-group">
                            <label for="title_zh">公告標題 (繁中)</label>
                            <input type="text" id="title_zh" name="title_zh" class="form-control" placeholder="請輸入中文標題" required>
                        </div>
                        <div class="form-group">
                            <label for="content_zh">公告內文 (繁中)</label>
                            <textarea id="content_zh" name="content_zh" class="form-control" placeholder="請輸入公告完整中文說明內容..." required></textarea>
                        </div>
                    </div>

                    <!-- English -->
                    <div class="tab-content" id="tab-en">
                        <div class="form-group">
                            <label for="badge_en">Badge Text (English)</label>
                            <input type="text" id="badge_en" name="badge_en" class="form-control" placeholder="e.g. Notice, Recruitment" required>
                        </div>
                        <div class="form-group">
                            <label for="title_en">Title (English)</label>
                            <input type="text" id="title_en" name="title_en" class="form-control" placeholder="Enter English Title" required>
                        </div>
                        <div class="form-group">
                            <label for="content_en">Content (English)</label>
                            <textarea id="content_en" name="content_en" class="form-control" placeholder="Enter English description..." required></textarea>
                        </div>
                    </div>

                    <!-- 日本語 -->
                    <div class="tab-content" id="tab-ja">
                        <div class="form-group">
                            <label for="badge_ja">バッジテキスト (日本語)</label>
                            <input type="text" id="badge_ja" name="badge_ja" class="form-control" placeholder="例：重要告知、採用情報" required>
                        </div>
                        <div class="form-group">
                            <label for="title_ja">タイトル (日本語)</label>
                            <input type="text" id="title_ja" name="title_ja" class="form-control" placeholder="日本語タイトルを入力してください" required>
                        </div>
                        <div class="form-group">
                            <label for="content_ja">内容 (日本語)</label>
                            <textarea id="content_ja" name="content_ja" class="form-control" placeholder="詳細な内容を入力してください..." required></textarea>
                        </div>
                    </div>

                </div>

                <div class="modal-footer" style="justify-content: space-between;">
                    <div>
                        <button type="button" class="btn btn-outline btn-sm" id="clearDraftBtn" onclick="clearCreateDraft()" style="color: #94a3b8; border-color: #cbd5e1;">清空重填</button>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="btn btn-outline" onclick="closeNewsModal()" style="color: #64748b; border-color: #cbd5e1;">取消</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">儲存發布</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 修改密碼 Modal -->
    <div class="modal" id="pwdModal">
        <div class="modal-content" style="max-width: 420px;">
            <div class="modal-header">
                <h3>變更管理員密碼</h3>
                <button type="button" class="modal-close" onclick="closePwdModal()">&times;</button>
            </div>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="change_password">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_password">新管理員密碼 (至少 6 個字元)</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="請輸入新密碼" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closePwdModal()" style="color: #64748b; border-color: #cbd5e1;">取消</button>
                    <button type="submit" class="btn btn-primary">更新密碼</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // 新增公告草稿暫存 (在不 refresh 情況下保留輸入內容)
        let createDraft = null;

        // 即時保存新增草稿
        function saveCreateDraft() {
            if (document.getElementById('formAction').value === 'create') {
                createDraft = {
                    date: document.getElementById('newsDate').value,
                    badge_type: document.getElementById('badgeType').value,
                    link: document.getElementById('newsLink').value,
                    badge_zh: document.getElementById('badge_zh').value,
                    title_zh: document.getElementById('title_zh').value,
                    content_zh: document.getElementById('content_zh').value,
                    badge_en: document.getElementById('badge_en').value,
                    title_en: document.getElementById('title_en').value,
                    content_en: document.getElementById('content_en').value,
                    badge_ja: document.getElementById('badge_ja').value,
                    title_ja: document.getElementById('title_ja').value,
                    content_ja: document.getElementById('content_ja').value
                };
            }
        }

        // 監聽表單輸入與變更，隨時更新草稿
        ['input', 'change'].forEach(evt => {
            document.getElementById('newsForm').addEventListener(evt, function() {
                if (document.getElementById('formAction').value === 'create') {
                    saveCreateDraft();
                }
            });
        });

        function switchTab(lang) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));

            const activeBtn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.textContent.includes(lang));
            if (activeBtn) activeBtn.classList.add('active');

            const activeTab = document.getElementById('tab-' + lang);
            if (activeTab) activeTab.classList.add('active');
        }

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = '新增公告';
            document.getElementById('formAction').value = 'create';
            document.getElementById('newsId').value = '';
            document.getElementById('clearDraftBtn').style.display = 'inline-flex';

            // 若已有暫存草稿則還原草稿，不清空使用者先前的輸入
            if (createDraft) {
                document.getElementById('newsDate').value = createDraft.date || new Date().toISOString().split('T')[0];
                document.getElementById('badgeType').value = createDraft.badge_type || 'primary';
                document.getElementById('newsLink').value = createDraft.link || '';

                document.getElementById('badge_zh').value = createDraft.badge_zh || '';
                document.getElementById('title_zh').value = createDraft.title_zh || '';
                document.getElementById('content_zh').value = createDraft.content_zh || '';

                document.getElementById('badge_en').value = createDraft.badge_en || '';
                document.getElementById('title_en').value = createDraft.title_en || '';
                document.getElementById('content_en').value = createDraft.content_en || '';

                document.getElementById('badge_ja').value = createDraft.badge_ja || '';
                document.getElementById('title_ja').value = createDraft.title_ja || '';
                document.getElementById('content_ja').value = createDraft.content_ja || '';
            } else {
                document.getElementById('newsForm').reset();
                document.getElementById('newsDate').value = new Date().toISOString().split('T')[0];
                document.getElementById('badge_zh').value = '重要公告';
                document.getElementById('badge_en').value = 'Notice';
                document.getElementById('badge_ja').value = '重要告知';
            }

            switchTab('zh');
            document.getElementById('newsModal').classList.add('active');
        }

        function openEditModal(item) {
            // 打開編輯前，先暫存目前的新增草稿（若有）
            saveCreateDraft();

            document.getElementById('modalTitle').textContent = '編輯公告';
            document.getElementById('formAction').value = 'update';
            document.getElementById('newsId').value = item.id;
            document.getElementById('clearDraftBtn').style.display = 'none';
            
            document.getElementById('newsDate').value = item.date || '';
            document.getElementById('badgeType').value = item.badge_type || 'primary';
            document.getElementById('newsLink').value = item.link || '';

            // 繁中
            document.getElementById('badge_zh').value = (item.badge && item.badge.zh) || '';
            document.getElementById('title_zh').value = (item.title && item.title.zh) || '';
            document.getElementById('content_zh').value = (item.content && item.content.zh) || '';

            // English
            document.getElementById('badge_en').value = (item.badge && item.badge.en) || '';
            document.getElementById('title_en').value = (item.title && item.title.en) || '';
            document.getElementById('content_en').value = (item.content && item.content.en) || '';

            // 日本語
            document.getElementById('badge_ja').value = (item.badge && item.badge.ja) || '';
            document.getElementById('title_ja').value = (item.title && item.title.ja) || '';
            document.getElementById('content_ja').value = (item.content && item.content.ja) || '';

            switchTab('zh');
            document.getElementById('newsModal').classList.add('active');
        }

        function closeNewsModal() {
            // 關閉視窗時保存草稿
            saveCreateDraft();
            document.getElementById('newsModal').classList.remove('active');
        }

        // 清空重填功能
        function clearCreateDraft() {
            if (confirm('確定要清空目前填寫的草稿內容重新開始嗎？')) {
                createDraft = null;
                document.getElementById('newsForm').reset();
                document.getElementById('newsDate').value = new Date().toISOString().split('T')[0];
                document.getElementById('badge_zh').value = '重要公告';
                document.getElementById('badge_en').value = 'Notice';
                document.getElementById('badge_ja').value = '重要告知';
                switchTab('zh');
            }
        }

        function openPwdModal() {
            document.getElementById('pwdModal').classList.add('active');
        }

        function closePwdModal() {
            document.getElementById('pwdModal').classList.remove('active');
        }

        // 注意：已移除點擊背景關閉 Modal 的設定，防止誤觸關閉
    </script>
</body>
</html>

