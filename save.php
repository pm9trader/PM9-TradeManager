<?php
/* ============================================================
   PM9 Site Config Saver — v1.0
   ⚠️ قبل از آپلود روی هاست، رمز زیر را حتماً عوض کنید!
   ============================================================ */
define('PM9_PASSWORD', 'pm9admin');
define('CONFIG_FILE', __DIR__ . '/config.js');
define('BACKUP_FILE', __DIR__ . '/config.backup.js');

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
@ini_set('display_errors', '0');

$action = isset($_GET['action']) ? $_GET['action'] : 'save';

if ($action === 'check') {
    echo json_encode(array(
        'ok'       => true,
        'php'      => PHP_VERSION,
        'writable' => is_writable(__DIR__),
        'existing' => file_exists(CONFIG_FILE),
    ));
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(array('ok' => false, 'error' => 'POST only'));
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'error' => 'bad JSON'));
    exit;
}

$pw = isset($data['password']) ? (string)$data['password'] : '';
if ($pw === '' || !hash_equals(PM9_PASSWORD, $pw)) {
    http_response_code(403);
    echo json_encode(array('ok' => false, 'error' => 'رمز مدیریت اشتباه است'));
    exit;
}

$cfg = isset($data['config']) && is_array($data['config']) ? $data['config'] : null;
if ($cfg === null || !isset($cfg['texts']['fa']) || !is_array($cfg['texts']['fa'])) {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'error' => 'config structure invalid'));
    exit;
}

$json = json_encode($cfg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if ($json === false || strlen($json) > 800000) {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'error' => 'config too large / invalid'));
    exit;
}

$js = "/* PM9 Site Content Config — saved " . date('Y-m-d H:i:s') . " via admin panel */\n"
    . "window.PM9_CONFIG = " . $json . ";\n";

$tmp = CONFIG_FILE . '.tmp';
if (@file_put_contents($tmp, $js) === false) {
    http_response_code(500);
    echo json_encode(array('ok' => false, 'error' => 'نوشتن فایل ممکن نیست — دسترسی نوشتن پوشه را چک کنید (۶۴۴/۷۵۵)'));
    exit;
}
if (file_exists(CONFIG_FILE)) { @copy(CONFIG_FILE, BACKUP_FILE); }
if (!@rename($tmp, CONFIG_FILE)) {
    http_response_code(500);
    echo json_encode(array('ok' => false, 'error' => 'rename failed'));
    exit;
}

echo json_encode(array('ok' => true, 'bytes' => strlen($js), 'time' => date('H:i:s')));
