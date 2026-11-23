<?php
// Авторизация и проверка ролей.
declare(strict_types=1);

// Текущий пользователь или null
function current_user(mysqli $conn): ?array
{
    if (empty($_SESSION['user_id'])) return null;
    $stmt = $conn->prepare(
        'SELECT id, name, email, role, created_at FROM users WHERE id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $res  = $stmt->get_result();
    $user = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    return $user ?: null;
}

// Авторизован ли пользователь
function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

// Требовать авторизацию (редирект на login с возвратом)
function require_login(): void
{
    if (is_logged_in()) return;
    $current = $_SERVER['REQUEST_URI'] ?? 'cabinet.php';
    $target  = ltrim(parse_url($current, PHP_URL_PATH) ?? 'cabinet.php', '/');
    $query   = parse_url($current, PHP_URL_QUERY);
    if ($query) $target .= '?' . $query;
    flash_set('error', 'Для доступа необходимо войти в аккаунт.');
    header('Location: login.php?redirect=' . urlencode($target));
    exit;
}

// Требовать одну из ролей
function require_role(mysqli $conn, array $roles): void
{
    require_login();
    $user = current_user($conn);
    if ($user === null || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('Доступ запрещён.');
    }
}