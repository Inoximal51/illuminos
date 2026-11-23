<?php
// Сессия, CSRF-токены, флеш-сообщения.
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// CSRF-токен (один на сессию)
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Проверка CSRF-токена
function csrf_verify(string $token): bool
{
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Установить флеш-сообщение
function flash_set(string $type, string $message): void
{
    $_SESSION['flash_' . $type] = $message;
}

// Получить и удалить флеш-сообщение
function flash_get(string $type): ?string
{
    $key = 'flash_' . $type;
    if (!empty($_SESSION[$key])) {
        $msg = (string)$_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}

// Безопасный редирект: только относительные пути внутри сайта
function safe_redirect_target(string $target): string
{
    $target = trim($target);
    if ($target === '') return 'cabinet.php';
    if ($target[0] === '/'
        || preg_match('#^[a-z]+:#i', $target)
        || str_contains($target, '://')) {
        return 'cabinet.php';
    }
    return $target;
}