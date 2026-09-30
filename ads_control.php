<?php
/**
 * ads_control.php - ایڈ موب ریموٹ کنٹرولر اور ڈائنامک ایڈ یونٹ مینیجر
 */

// لاگ ان پاس ورڈ (آپ جب چاہیں یہاں سے بدل سکتے ہیں)
define('SECRET_PASSWORD', 'admin786');
define('DATA_FILE', __DIR__ . '/ads_data.json');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

// موبائل ایپ کے لیے لائیو ڈیٹا (JSON API)
if (isset($_GET['api']) || strpos($_SERVER['REQUEST_URI'], 'ads_data.json') !== false || !empty($_GET['json'])) {
    header('Content-Type: application/json; charset=utf-8');
    if (file_exists(DATA_FILE)) {
        echo file_get_contents(DATA_FILE);
    } else {
        echo json_encode([
            "status" => "success",
            "master_ads_enabled" => true,
            "app_open_ad_enabled" => true,
            "songs_list_ad_enabled" => true,
            "videos_list_ad_enabled" => true,
            "settings_click_ad_enabled" => true,
            "vault_reward_ad_enabled" => true,
            "admob_app_id" => "ca-app-pub-7375534389683057~7020773641",
            "app_open_ad_unit_id" => "ca-app-pub-7375534389683057/3906515984",
            "songs_list_ad_unit_id" => "ca-app-pub-7375534389683057/8388686475",
            "videos_list_ad_unit_id" => "ca-app-pub-7375534389683057/8388686475",
            "settings_click_ad_unit_id" => "ca-app-pub-7375534389683057/1823278127",
            "vault_reward_ad_unit_id" => "ca-app-pub-7375534389683057/7678240038"
        ]);
    }
    exit;
}

session_start();
$msg = "";
$msg_type = "";

if (isset($_POST['login'])) {
    if ($_POST['password'] === SECRET_PASSWORD) {
        $_SESSION['is_logged_in'] = true;
    } else {
        $msg = "غلط پاس ورڈ درج کیا ہے!";
        $msg_type = "error";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ads_control.php");
    exit;
}

if (isset($_POST['save_settings']) && !empty($_SESSION['is_logged_in'])) {
    $settings = [
        "status" => "success",
        "last_updated" => date('Y-m-d H:i:s'),
        "master_ads_enabled" => isset($_POST['master_ads_enabled']),
        "app_open_ad_enabled" => isset($_POST['app_open_ad_enabled']),
        "app_open_ad_unit_id" => trim($_POST['app_open_ad_unit_id']),
        "songs_list_ad_enabled" => isset($_POST['songs_list_ad_enabled']),
        "songs_list_ad_unit_id" => trim($_POST['songs_list_ad_unit_id']),
        "videos_list_ad_enabled" => isset($_POST['videos_list_ad_enabled']),
        "videos_list_ad_unit_id" => trim($_POST['videos_list_ad_unit_id']),
        "settings_click_ad_enabled" => isset($_POST['settings_click_ad_enabled']),
        "settings_click_ad_unit_id" => trim($_POST['settings_click_ad_unit_id']),
        "vault_reward_ad_enabled" => isset($_POST['vault_reward_ad_enabled']),
        "vault_reward_ad_unit_id" => trim($_POST['vault_reward_ad_unit_id']),
        "admob_app_id" => trim($_POST['admob_app_id'] ?? 'ca-app-pub-7375534389683057~7020773641'),
        "song_intervals" => [4, 10, 7, 11, 23],
        "video_intervals" => [4, 11, 9]
    ];
    
    file_put_contents(DATA_FILE, json_encode($settings, JSON_PRETTY_PRINT));
    $msg = "سیٹنگز اور ایڈ آئی ڈیز کامیابی سے محفوظ ہو گئیں!";
    $msg_type = "success";
}

$config = file_exists(DATA_FILE) ? json_decode(file_get_contents(DATA_FILE), true) : [
    "master_ads_enabled" => true,
    "admob_app_id" => "ca-app-pub-7375534389683057~7020773641",
    "app_open_ad_enabled" => true,
    "app_open_ad_unit_id" => "ca-app-pub-7375534389683057/3906515984",
    "songs_list_ad_enabled" => true,
    "songs_list_ad_unit_id" => "ca-app-pub-7375534389683057/8388686475",
    "videos_list_ad_enabled" => true,
    "videos_list_ad_unit_id" => "ca-app-pub-7375534389683057/8388686475",
    "settings_click_ad_enabled" => true,
    "settings_click_ad_unit_id" => "ca-app-pub-7375534389683057/1823278127",
    "vault_reward_ad_enabled" => true,
    "vault_reward_ad_unit_id" => "ca-app-pub-7375534389683057/7678240038"
];
?>
<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>میڈیا پلیئر ریموٹ ایڈز مینیجر</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background: #090d16; color: #f1f5f9; margin: 0; padding: 20px; }
        .container { max-width: 680px; margin: 0 auto; background: #131c2e; border-radius: 18px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.6); border: 1px solid #1e293b; }
        h1 { margin-top: 0; font-size: 22px; color: #38bdf8; text-align: center; }
        .card { background: #0c1322; border-radius: 12px; padding: 18px; margin-bottom: 16px; border: 1px solid #1e293b; }
        .row { display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid #1e293b; margin-bottom: 10px; }
        .switch { position: relative; display: inline-block; width: 52px; height: 28px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #475569; transition: .3s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 22px; width: 22px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
        input:checked + .slider { background-color: #10b981; }
        input:checked + .slider:before { transform: translateX(24px); }
        .input-box { width: 100%; padding: 10px 14px; margin-top: 6px; background: #030712; border: 1px solid #334155; border-radius: 8px; color: #38bdf8; font-family: monospace; font-size: 13px; box-sizing: border-box; }
        .btn-save { width: 100%; background: #10b981; color: #022c22; font-weight: bold; padding: 16px; font-size: 17px; border: none; border-radius: 12px; cursor: pointer; margin-top: 15px; }
        .alert { padding: 14px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-size: 14px; font-weight: bold; }
        .alert-success { background: #064e3b; color: #6ee7b7; border: 1px solid #059669; }
        .alert-error { background: #7f1d1d; color: #fca5a5; }
        .master-card { background: linear-gradient(135deg, #172554, #1e1b4b); border: 1px solid #3b82f6; }
        .label-id { font-size: 12px; color: #94a3b8; display: block; margin-top: 6px; }
    </style>
</head>
<body>

<div class="container">
    <?php if (empty($_SESSION['is_logged_in'])): ?>
        <h1>ایڈمن لاگ ان</h1>
        <?php if (!empty($msg)) echo "<div class='alert alert-{$msg_type}'>{$msg}</div>"; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="پاس ورڈ درج کریں" class="input-box" style="margin-bottom: 15px; padding: 14px;">
            <button type="submit" name="login" class="btn-save">لاگ ان کریں</button>
        </form>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h1>میڈیا پلیئر ایڈز اور آئی ڈی کنٹرولر</h1>
            <a href="?logout=1" style="color:#ef4444; text-decoration:none; font-size:13px; font-weight:bold;">لاگ آؤٹ</a>
        </div>

        <?php if (!empty($msg)) echo "<div class='alert alert-{$msg_type}'>{$msg}</div>"; ?>

        <form method="POST">
            <!-- 1. ماسٹر سوئچ -->
            <div class="card master-card">
                <div class="row" style="border:none; margin:0; padding:0;">
                    <div>
                        <strong style="font-size:16px;">تمام ایڈز کا ماسٹر سوئچ (Master ON/OFF)</strong>
                        <div style="font-size:11px; color:#93c5fd; margin-top:4px;">اس کو بند کرنے پر پوری ایپ کے تمام اشتہارات فوری بند ہو جائیں گے۔</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="master_ads_enabled" <?= !empty($config['master_ads_enabled']) ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <!-- 2. ایپ اوپن ایڈ -->
            <div class="card">
                <div class="row">
                    <div>
                        <strong style="color:#38bdf8;">1. ایپ کھلنے والا ایڈ (App Open Ad)</strong>
                        <div style="font-size:11px; color:#94a3b8;">5 سیکنڈ اسپلش اشتہار</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="app_open_ad_enabled" <?= !empty($config['app_open_ad_enabled']) ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <label class="label-id">ایڈ یونٹ آئی ڈی:</label>
                <input type="text" name="app_open_ad_unit_id" value="<?= htmlspecialchars($config['app_open_ad_unit_id'] ?? 'ca-app-pub-7375534389683057/3906515984') ?>" class="input-box">
            </div>

            <!-- 3. گانوں کی لسٹ کا ایڈ -->
            <div class="card">
                <div class="row">
                    <div>
                        <strong style="color:#38bdf8;">2. گانوں کی لسٹ کا ایڈ (Songs Feed Ad)</strong>
                        <div style="font-size:11px; color:#94a3b8;">4، 10، 7، 11، 23 گانوں کے بعد آنے والا بڑا ایڈ</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="songs_list_ad_enabled" <?= !empty($config['songs_list_ad_enabled']) ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <label class="label-id">ایڈ یونٹ آئی ڈی:</label>
                <input type="text" name="songs_list_ad_unit_id" value="<?= htmlspecialchars($config['songs_list_ad_unit_id'] ?? 'ca-app-pub-7375534389683057/8388686475') ?>" class="input-box">
            </div>

            <!-- 4. ویڈیوز کی لسٹ کا ایڈ -->
            <div class="card">
                <div class="row">
                    <div>
                        <strong style="color:#38bdf8;">3. ویڈیوز کی لسٹ کا ایڈ (Videos Feed Ad)</strong>
                        <div style="font-size:11px; color:#94a3b8;">4، 11، 9 ویڈیوز کے بعد آنے والا بڑا ایڈ</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="videos_list_ad_enabled" <?= !empty($config['videos_list_ad_enabled']) ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <label class="label-id">ایڈ یونٹ آئی ڈی:</label>
                <input type="text" name="videos_list_ad_unit_id" value="<?= htmlspecialchars($config['videos_list_ad_unit_id'] ?? 'ca-app-pub-7375534389683057/8388686475') ?>" class="input-box">
            </div>

            <!-- 5. سیٹنگز پر کلک والا ایڈ -->
            <div class="card">
                <div class="row">
                    <div>
                        <strong style="color:#38bdf8;">4. سیٹنگز پر کلک والا ایڈ (Settings Click Ad)</strong>
                        <div style="font-size:11px; color:#94a3b8;">انٹرسٹیشل فل اسکرین ایڈ</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="settings_click_ad_enabled" <?= !empty($config['settings_click_ad_enabled']) ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <label class="label-id">ایڈ یونٹ آئی ڈی:</label>
                <input type="text" name="settings_click_ad_unit_id" value="<?= htmlspecialchars($config['settings_click_ad_unit_id'] ?? 'ca-app-pub-7375534389683057/1823278127') ?>" class="input-box">
            </div>

            <!-- 6. والٹ پاسورڈ ریوارڈ ایڈ -->
            <div class="card">
                <div class="row">
                    <div>
                        <strong style="color:#38bdf8;">5. والٹ پاسورڈ ریوارڈ ایڈ (Vault Forgot Password)</strong>
                        <div style="font-size:11px; color:#94a3b8;">15 سے 30 سیکنڈ کی انعامی ویڈیو دیکھ کر پاسورڈ ری سیٹ</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="vault_reward_ad_enabled" <?= !empty($config['vault_reward_ad_enabled']) ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <label class="label-id">ایڈ یونٹ آئی ڈی:</label>
                <input type="text" name="vault_reward_ad_unit_id" value="<?= htmlspecialchars($config['vault_reward_ad_unit_id'] ?? 'ca-app-pub-7375534389683057/7678240038') ?>" class="input-box">
            </div>

            <button type="submit" name="save_settings" class="btn-save">سیٹنگز اور نئی آئی ڈیز محفوظ کریں (Save Changes)</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
