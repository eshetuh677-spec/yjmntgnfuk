<?php
// ============================================================
// EFPT_AMS - Login Page
// ============================================================
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
require_once 'includes/totp.php';
require_once 'includes/rate_limit.php';
require_once 'includes/notifications.php';
require_once 'includes/security_scanner.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    require_login();
    redirect_to_dashboard($_SESSION['role']);
}

$error = '';
$msg   = '';

// Handle messages
$msg_map = [
    'logged_out'     => ['type' => 'info',    'text' => 'You have been logged out successfully.'],
    'session_expired'=> ['type' => 'warning', 'text' => 'Your session has expired. Please log in again.'],
    'access_denied'  => ['type' => 'danger',  'text' => 'Access denied. You do not have permission for that page.'],
];
if (isset($_GET['msg']) && isset($msg_map[$_GET['msg']])) {
    $msg = $msg_map[$_GET['msg']];
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_enforce();

    // Short IP rate limiting remains in place to protect the login endpoint.
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    enforce_rate_limit($ip_address, 'login', 10, 60);

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (is_ip_blocked($conn, $ip_address)) {
        $error = 'Access from this IP address has been blocked by an administrator. Contact your system administrator for assistance.';
        log_audit_event($conn, null, 'system', 'ip_blocked_attempt', "Blocked IP {$ip_address} attempted login", null, null, null);
    } elseif (!$username || !$password) {
        $error = 'Please enter both username and password.';
    } else {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Lock the submitted username after four failed attempts. The
        // username lock is independent of the requesting IP address.
        $lockout_check = db_query($conn, "SELECT COUNT(*) AS cnt, MAX(attempted_at) AS last_attempt
            FROM login_attempts
            WHERE username=?
              AND attempted_at > (NOW() - INTERVAL " . (int)LOGIN_LOCKOUT_MINUTES . " MINUTE)",
            's', [$username]);
        $lockout_row = $lockout_check ? $lockout_check->fetch_assoc() : null;
        if ($lockout_row && $lockout_row['cnt'] >= MAX_LOGIN_ATTEMPTS) {
            $unlock_at = strtotime((string)$lockout_row['last_attempt']) + (LOGIN_LOCKOUT_MINUTES * 60);
            $remaining_minutes = max(1, (int)ceil(max(1, $unlock_at - time()) / 60));
            $error = 'This username is temporarily locked after ' . MAX_LOGIN_ATTEMPTS
                . ' failed attempts. Please try again in about ' . $remaining_minutes . ' minute'
                . ($remaining_minutes === 1 ? '' : 's') . '.';
            log_audit_event($conn, null, 'system', 'login_lockout',
                "Username '{$username}' locked after {$lockout_row['cnt']} failed attempts from IP {$ip_address}",
                null, null, null);
            notify_admins_account_lockout($conn, $username, $ip_address, (int)$lockout_row['cnt']);
        } else {
            $res = db_query($conn, "SELECT * FROM users WHERE username=? AND status='active' AND deleted_at IS NULL LIMIT 1", 's', [$username]);
            if ($res && $row = $res->fetch_assoc()) {
                if (password_verify($password, $row['password'])) {
                    db_execute($conn, "DELETE FROM login_attempts WHERE username=?", 's', [$username]);

                    // Check if 2FA is enabled
                    if (!empty($row['totp_enabled']) && !empty($row['totp_secret'])) {
                        // Store user data in session for 2FA verification
                        $_SESSION['pending_2fa_user'] = [
                            'id' => $row['id'],
                            'username' => $row['username'],
                            'full_name' => $row['full_name'],
                            'role' => $row['role'],
                            'department' => $row['department'],
                            'organization_id' => $row['organization_id'] ?? null,
                            'main_department_id' => $row['main_department_id'] ?? null,
                            'department_id' => $row['department_id'] ?? null,
                            'class_id' => $row['class_id'] ?? null,
                            'office_number' => $row['office_number'] ?? null,
                            'is_super_admin' => $row['is_super_admin'] ?? 0,
                            'badge_number' => $row['badge_number'] ?? null,
                            'budget_code' => $row['budget_code'] ?? null,
                            'rank_title' => $row['rank_title'] ?? null,
                            'email' => $row['email'] ?? null,
                            'phone' => $row['phone'] ?? null,
                            'profile_photo' => $row['profile_photo'] ?? null,
                            'store_id' => $row['store_id'] ?? null,
                            'force_password_change' => $row['force_password_change'] ?? 0,
                            'totp_secret' => $row['totp_secret'],
                            'totp_backup_codes' => $row['totp_backup_codes'],
                            'user_agent_hash' => substr(hash('sha256', $user_agent), 0, 32),
                            'login_ip' => $ip_address,
                        ];
                        header('Location: ' . SITE_URL . '/shared/verify_2fa.php');
                        exit;
                    }

                    // No 2FA - complete login
                    complete_login($row, $ip_address, $user_agent);
                    log_audit_event($conn, $row['id'], $row['role'], 'login_success', "User logged in successfully", $row['id']);
                    redirect_to_dashboard($row['role']);
                } else {
                    db_execute($conn, "INSERT INTO login_attempts (username, ip_address, user_agent) VALUES (?,?,?)", 'sss', [$username, $ip_address, substr($user_agent, 0, 500)]);
                    record_rate_limit_hit($ip_address, 'login');
                    $error = 'Invalid username or password.';
                    log_audit_event($conn, null, 'unknown', 'login_failed', "Failed login attempt for username '{$username}' from IP {$ip_address}", null, null, null);
                }
            } else {
                db_execute($conn, "INSERT INTO login_attempts (username, ip_address, user_agent) VALUES (?,?,?)", 'sss', [$username, $ip_address, substr($user_agent, 0, 500)]);
                record_rate_limit_hit($ip_address, 'login');
                $error = 'Invalid username or password.';
                log_audit_event($conn, null, 'unknown', 'login_failed', "Failed login attempt for username '{$username}' from IP {$ip_address}", null, null, null);
            }
        }
    }
}

/**
 * Complete the login process by setting session variables
 */
function complete_login(array $user, string $ip_address, string $user_agent): void {
    // Regenerate session ID to prevent session fixation
    secure_session_regenerate();

    $_SESSION['user_id']            = $user['id'];
    $_SESSION['username']           = $user['username'];
    $_SESSION['full_name']          = $user['full_name'];
    $_SESSION['role']               = $user['role'];
    $_SESSION['roles']              = [$user['role']];
    $_SESSION['department']         = $user['department'];
    $_SESSION['organization_id']    = $user['organization_id'] ?? null;
    $_SESSION['main_department_id'] = $user['main_department_id'] ?? null;
    $_SESSION['department_id']      = $user['department_id'] ?? null;
    $_SESSION['class_id']           = $user['class_id'] ?? null;
    $_SESSION['office_number']      = $user['office_number'] ?? null;
    $_SESSION['is_super_admin']     = $user['is_super_admin'] ?? 0;
    $_SESSION['badge_number']       = $user['badge_number'] ?? null;
    $_SESSION['budget_code']        = $user['budget_code'] ?? null;
    $_SESSION['rank_title']         = $user['rank_title'] ?? null;
    $_SESSION['email']              = $user['email'] ?? null;
    $_SESSION['phone']              = $user['phone'] ?? null;
    $_SESSION['profile_photo']      = $user['profile_photo'] ?? null;
    $_SESSION['store_id']           = $user['store_id'] ?? null;
    $_SESSION['last_activity']      = time();
    $_SESSION['user_agent_hash']    = substr(hash('sha256', $user_agent), 0, 32);
    $_SESSION['login_ip']           = $ip_address;
    $_SESSION['force_password_change'] = $user['force_password_change'] ?? 0;
    $_SESSION['last_regeneration']  = time();

    // Apply any active role delegations to the session so the user immediately
    // inherits delegated permissions without needing a separate page load.
    global $conn;
    if ($conn) {
        $_SESSION['roles'] = load_user_roles($conn, (int)$user['id'], (string)$user['role']);
        expire_stale_delegations($conn);
        apply_session_delegations($conn, (int)$user['id']);
        start_login_session_tracking($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — EFPT Asset Management System</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= (int)@filemtime(__DIR__ . '/assets/css/style.css') ?>">
    <style>
        .login-footer-note {
            text-align: center;
            padding: 16px 0 10px;
            font-size: 0.8rem;
            color: #0a6aca;
            border-top: 1px solid #e8eef6;
            margin-top: 10px;
        }
        .default-creds {
            background: #f0f6ff;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.8rem;
            color: #3a5a8a;
            margin-top: 14px;
            border: 1px solid #ccdaef;
        }
        .default-creds strong { display: block; margin-bottom: 4px; color: #1a3a6a; }
        .default-creds code   { background: #dce8ff; padding: 1px 5px; border-radius: 3px; font-size: 0.82rem; }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap .icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            font-size: 1rem; color: #7a9aaa; pointer-events: none;
        }
        .input-icon-wrap .form-control { padding-left: 36px; }
        .pw-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #7a9aaa; font-size: 0.9rem;
        }

        .login-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            margin: 0 0 4px;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .login-subtitle {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.65);
            margin: 0;
            line-height: 1.5;
            text-align: center;
        }

        .login-info {
            background: #f1f6fd;
            border-radius: 10px;
            padding: 13px 16px;
            margin-top: 16px;
            border: 1px solid #cbdcf1;
            box-shadow: 0 4px 14px rgba(10, 35, 65, 0.08);
            font-size: 0.82rem;
        }
        .info-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 7px 0;
            border-bottom: 1px solid #d9e4f1;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            flex: 0 0 auto;
            color: #40536b;
            font-weight: 700;
        }
        .info-value {
            color: #142b49;
            font-weight: 700;
            text-align: right;
            overflow-wrap: anywhere;
        }
        @media (max-width: 420px) {
            .login-info { padding: 11px 12px; }
            .info-row { flex-direction: column; gap: 2px; }
            .info-value { text-align: left; }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <div class="efpt-logo-img-wrap">
                <img src="assets/images/efplogo.png" alt="EFPT Logo" class="efpt-logo-img">
            </div>
            <h1 class="login-title">Asset Management System</h1>
            <p class="login-subtitle">Ethiopian Federal Police Technology Department</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <?php if ($msg): ?>
            <div class="alert alert-<?= $msg['type'] ?> auto-dismiss">
                <?= esc($msg['text']) ?>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="alert alert-danger auto-dismiss">
                &#9888; <?= esc($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="index.php" autocomplete="off">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label for="username">Username / Badge Number</label>
                    <div class="input-icon-wrap">
                        <span class="icon">&#128100;</span>
                        <input type="text" id="username" name="username"
                               class="form-control"
                               placeholder="Enter your username or badge number"
                               value="<?= esc($_POST['username'] ?? '') ?>"
                               required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon-wrap">
                        <span class="icon">&#128274;</span>
                        <input type="password" id="password" name="password"
                               class="form-control"
                               placeholder="Enter your password"
                               required autocomplete="current-password">
                        <button type="button" class="pw-toggle" onclick="togglePw()" aria-label="Toggle password visibility">&#128065;</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 6px;">
                    &#128275; Secure Login
                </button>
            </form>

            <!-- System Info -->
            <div class="login-info">
                <div class="info-row">
                    <span class="info-label">System:</span>
                    <span class="info-value">EFPT-AMS v1.0.0</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Organization:</span>
                    <span class="info-value">Ethiopian Federal Police</span>
                </div>
            </div>
        </div>

        <div class="login-footer-note">
            &copy; <?= date('Y') ?> Ethiopian Federal Police Technology Department<br>
            <small>Authorized Access Only &middot; All Activities Are Monitored &middot; Asset Management System</small>
        </div>
    </div>
</div>

<script>
function togglePw() {
    const pw = document.getElementById('password');
    pw.type = pw.type === 'password' ? 'text' : 'password';
}
setTimeout(function() {
    document.querySelectorAll('.alert.auto-dismiss').forEach(function(el) {
        el.style.transition = 'opacity 0.5s';
        el.style.opacity = '0';
        setTimeout(function() { el.remove(); }, 500);
    });
}, 4500);
</script>
</body>
</html>
