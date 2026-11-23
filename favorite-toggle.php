<?php
// Добавление/удаление из избранного (POST).
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Метод не поддерживается.');
}

if (empty($_SESSION['user_id'])) {
    flash_set('error', 'Войдите, чтобы управлять избранным.');
    header('Location: login.php');
    exit;
}

if (!csrf_verify((string)($_POST['csrf_token'] ?? ''))) {
    flash_set('error', 'Неверный CSRF-токен. Обновите страницу.');
    header('Location: index.php');
    exit;
}

$type     = (string)($_POST['item_type'] ?? '');
$itemId   = (int)($_POST['item_id'] ?? 0);
$slug     = (string)($_POST['slug'] ?? '');
$redirect = safe_redirect_target((string)($_POST['redirect'] ?? 'cabinet.php'));

if (!in_array($type, ['movie', 'series'], true) || $itemId <= 0 || $slug === '') {
    flash_set('error', 'Некорректные данные.');
    header('Location: ' . $redirect);
    exit;
}

// Проверка существования элемента
$item = $type === 'movie'
    ? getMovieBySlug($conn, $slug)
    : getSeriesBySlug($conn, $slug);

if ($item === null || (int)$item['id'] !== $itemId) {
    flash_set('error', 'Элемент не найден.');
    header('Location: ' . $redirect);
    exit;
}

$added = toggleFavorite($conn, (int)$_SESSION['user_id'], $type, $itemId);
flash_set('success', $added
    ? '«' . $item['title'] . '» добавлено в избранное.'
    : '«' . $item['title'] . '» удалено из избранного.');

header('Location: ' . $redirect);
exit;