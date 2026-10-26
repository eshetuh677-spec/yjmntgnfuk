<?php
// ============================================================
// EFPT_AMS - Calendar Switcher Handler
// ============================================================
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/ethiopian_calendar.php';
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

if (session_status() !== PHP_SESSION_ACTIVE) {
    init_secure_session();
}

$_SESSION['ethiopian_calendar'] = true;
echo json_encode(['ok' => true, 'calendar' => 'ethiopian']);
