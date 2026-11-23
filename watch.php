<?php
// Эмуляция просмотра: пишет в историю и возвращает назад. URL: watch.php?type=movie&slug=...
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';

$type = (string)($_GET['type'] ?? '');
$slug = trim((string)($_GET['slug'] ?? ''));

if (!in_array($type, ['movie', 'series'], true) || $slug === '') {
    http_response_code(400);
    exit('Некорректный запрос.');
}

$item = $type === 'movie'
    ? getMovieBySlug($conn, $slug)
    : getSeriesBySlug($conn, $slug);

if ($item === null) {
    http_response_code(404);
    exit('Элемент не найден.');
}

$backUrl = ($type === 'movie' ? 'movie-detail.php' : 'series-detail.php')
         . '?slug=' . urlencode($slug);

if (empty($_SESSION['user_id'])) {
    flash_set('error', 'Войдите в аккаунт, чтобы смотреть.');
    header('Location: login.php?redirect=' . urlencode('watch.php?type=' . $type . '&slug=' . urlencode($slug)));
    exit;
}

addViewHistory($conn, (int)$_SESSION['user_id'], $type, (int)$item['id']);

flash_set('success', 'Просмотр «' . $item['title'] . '» добавлен в историю.');
header('Location: ' . $backUrl);
exit;