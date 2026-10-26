<?php
// ============================================================
// EFPT_AMS - Language Switcher Handler
// ============================================================
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/security.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Invalid method']);
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!csrf_verify()) {
    echo json_encode(['ok' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

$lang = strtolower(trim($_POST['lang'] ?? ''));
$allowed = ['en', 'am', 'om'];

if (!in_array($lang, $allowed, true)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid language']);
    exit;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    init_secure_session();
}

$_SESSION['lang'] = $lang;

if (isset($_SESSION['user_id']) && isset($conn)) {
    $stmt = $conn->prepare("UPDATE users SET language = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('si', $lang, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    }
}

$translations = load_translations($lang);
if (!is_array($translations)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Unable to load the selected language.']);
    exit;
}

echo json_encode([
    'ok' => true,
    'lang' => $lang,
    'translations' => $translations
]);
