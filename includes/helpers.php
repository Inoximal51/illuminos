<?php
// Утилиты и выборки из БД (prepared statements).
declare(strict_types=1);

// --- Базовые ---

function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function fetchAll(mysqli $conn, string $sql, array $params = [], string $types = ''): array
{
    $stmt = $conn->prepare($sql);
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res  = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $rows;
}

function fetchOne(mysqli $conn, string $sql, array $params = [], string $types = ''): ?array
{
    $rows = fetchAll($conn, $sql, $params, $types);
    return $rows[0] ?? null;
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/u', trim($name)) ?: [];
    $out = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $out .= mb_strtoupper(mb_substr($p, 0, 1, 'UTF-8'), 'UTF-8');
    }
    return $out !== '' ? $out : '?';
}

// Экранирование % и _ для LIKE
function escapeLike(string $s): string
{
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $s);
}

// URL пагинации с сохранением остальных параметров
function pageUrl(array $params, int $page): string
{
    $params['page'] = $page > 1 ? $page : null;
    $params = array_filter($params, static fn($v) => $v !== null && $v !== '');
    $qs = http_build_query($params);
    return $qs === '' ? '?' : '?' . $qs;
}

// --- Фильмы ---

function getFeaturedMovie(mysqli $conn): ?array
{
    return fetchOne($conn,
        'SELECT m.*, c.name AS category_name
           FROM movies m
           LEFT JOIN categories c ON c.id = m.category_id
          WHERE m.is_new = 1
          ORDER BY m.year DESC, m.id ASC
          LIMIT 1');
}

function getNewMovies(mysqli $conn, int $limit = 10): array
{
    return fetchAll($conn,
        'SELECT m.*, c.name AS category_name
           FROM movies m
           LEFT JOIN categories c ON c.id = m.category_id
          WHERE m.is_new = 1
          ORDER BY m.year DESC, m.id ASC
          LIMIT ?',
        [$limit], 'i');
}

function getPopularMovies(mysqli $conn, int $limit = 10): array
{
    return fetchAll($conn,
        'SELECT m.*, c.name AS category_name
           FROM movies m
           LEFT JOIN categories c ON c.id = m.category_id
          WHERE m.is_popular = 1
          ORDER BY m.rating DESC, m.id ASC
          LIMIT ?',
        [$limit], 'i');
}

function getMovieBySlug(mysqli $conn, string $slug): ?array
{
    return fetchOne($conn,
        'SELECT m.*, c.name AS category_name
           FROM movies m
           LEFT JOIN categories c ON c.id = m.category_id
          WHERE m.slug = ?
          LIMIT 1', [$slug], 's');
}

function getFirstMovieSlug(mysqli $conn): ?string
{
    $row = fetchOne($conn, 'SELECT slug FROM movies ORDER BY id LIMIT 1');
    return $row['slug'] ?? null;
}

function getMovieCast(mysqli $conn, int $movieId): array
{
    return fetchAll($conn,
        'SELECT person_name, role FROM movie_cast WHERE movie_id = ? ORDER BY id',
        [$movieId], 'i');
}

function getSimilarMovies(mysqli $conn, int $currentId, int $categoryId, int $limit = 6): array
{
    return fetchAll($conn,
        'SELECT m.*, c.name AS category_name
           FROM movies m
           LEFT JOIN categories c ON c.id = m.category_id
          WHERE m.id <> ? AND m.category_id = ?
          ORDER BY m.rating DESC, m.id ASC
          LIMIT ?',
        [$currentId, $categoryId, $limit], 'iii');
}

// --- Сериалы ---

function getSeriesBySlug(mysqli $conn, string $slug): ?array
{
    return fetchOne($conn,
        'SELECT s.*, c.name AS category_name
           FROM series s
           LEFT JOIN categories c ON c.id = s.category_id
          WHERE s.slug = ?
          LIMIT 1', [$slug], 's');
}

function getFirstSeriesSlug(mysqli $conn): ?string
{
    $row = fetchOne($conn, 'SELECT slug FROM series ORDER BY id LIMIT 1');
    return $row['slug'] ?? null;
}

function getSeriesCast(mysqli $conn, int $seriesId): array
{
    return fetchAll($conn,
        'SELECT person_name, role FROM series_cast WHERE series_id = ? ORDER BY id',
        [$seriesId], 'i');
}

function getSeriesSeasons(mysqli $conn, int $seriesId): array
{
    $seasons = fetchAll($conn,
        'SELECT * FROM seasons WHERE series_id = ? ORDER BY number',
        [$seriesId], 'i');
    foreach ($seasons as &$s) {
        $s['episodes'] = fetchAll($conn,
            'SELECT * FROM episodes WHERE season_id = ? ORDER BY number',
            [(int)$s['id']], 'i');
    }
    return $seasons;
}

function getSimilarSeries(mysqli $conn, int $currentId, int $categoryId, int $limit = 6): array
{
    return fetchAll($conn,
        'SELECT s.*, c.name AS category_name
           FROM series s
           LEFT JOIN categories c ON c.id = s.category_id
          WHERE s.id <> ? AND s.category_id = ?
          ORDER BY s.rating DESC, s.id ASC
          LIMIT ?',
        [$currentId, $categoryId, $limit], 'iii');
}

function getSeries(mysqli $conn, int $limit = 10): array
{
    return fetchAll($conn,
        'SELECT s.*, c.name AS category_name
           FROM series s
           LEFT JOIN categories c ON c.id = s.category_id
          ORDER BY s.rating DESC, s.id ASC
          LIMIT ?',
        [$limit], 'i');
}

// --- Категории ---

function getCategories(mysqli $conn): array
{
    return fetchAll($conn, 'SELECT * FROM categories ORDER BY id');
}

function getCategoryById(mysqli $conn, int $id): ?array
{
    return fetchOne($conn,
        'SELECT * FROM categories WHERE id = ? LIMIT 1',
        [$id], 'i');
}

function getMoviesByCategory(mysqli $conn, int $categoryId, int $limit = 50): array
{
    return fetchAll($conn,
        'SELECT m.*, c.name AS category_name
           FROM movies m
           LEFT JOIN categories c ON c.id = m.category_id
          WHERE m.category_id = ?
          ORDER BY m.rating DESC, m.year DESC, m.id ASC
          LIMIT ?',
        [$categoryId, $limit], 'ii');
}

function getSeriesByCategory(mysqli $conn, int $categoryId, int $limit = 50): array
{
    return fetchAll($conn,
        'SELECT s.*, c.name AS category_name
           FROM series s
           LEFT JOIN categories c ON c.id = s.category_id
          WHERE s.category_id = ?
          ORDER BY s.rating DESC, s.year_start DESC, s.id ASC
          LIMIT ?',
        [$categoryId, $limit], 'ii');
}

// --- Поиск ---

function searchMovies(mysqli $conn, string $q, int $limit = 50): array
{
    $like = '%' . escapeLike($q) . '%';
    return fetchAll($conn,
        'SELECT m.*, c.name AS category_name
           FROM movies m
           LEFT JOIN categories c ON c.id = m.category_id
          WHERE m.title LIKE ?
          ORDER BY m.rating DESC, m.year DESC, m.id ASC
          LIMIT ?',
        [$like, $limit], 'si');
}

function searchSeries(mysqli $conn, string $q, int $limit = 50): array
{
    $like = '%' . escapeLike($q) . '%';
    return fetchAll($conn,
        'SELECT s.*, c.name AS category_name
           FROM series s
           LEFT JOIN categories c ON c.id = s.category_id
          WHERE s.title LIKE ?
          ORDER BY s.rating DESC, s.year_start DESC, s.id ASC
          LIMIT ?',
        [$like, $limit], 'si');
}

// --- Каталоги с пагинацией ---

function _catalogWhere(string $q, int $categoryId, array &$params, string &$types): string
{
    $conds = [];
    if ($q !== '') {
        $conds[] = 'm.title LIKE ?';
        $params[] = '%' . escapeLike($q) . '%';
        $types .= 's';
    }
    if ($categoryId > 0) {
        $conds[] = 'm.category_id = ?';
        $params[] = $categoryId;
        $types .= 'i';
    }
    return $conds ? ' WHERE ' . implode(' AND ', $conds) : '';
}

function countMovies(mysqli $conn, string $q = '', int $categoryId = 0): int
{
    $params = [];
    $types  = '';
    $where  = _catalogWhere($q, $categoryId, $params, $types);
    $row = fetchOne($conn, 'SELECT COUNT(*) AS c FROM movies m' . $where, $params, $types);
    return (int)($row['c'] ?? 0);
}

function getMoviesPaged(mysqli $conn, string $q, int $categoryId, int $limit, int $offset): array
{
    $params = [];
    $types  = '';
    $where  = _catalogWhere($q, $categoryId, $params, $types);
    $params[] = $limit;
    $params[] = $offset;
    $types   .= 'ii';
    return fetchAll($conn,
        'SELECT m.*, c.name AS category_name
           FROM movies m
           LEFT JOIN categories c ON c.id = m.category_id'
        . $where .
        ' ORDER BY m.rating DESC, m.year DESC, m.id ASC
          LIMIT ? OFFSET ?',
        $params, $types);
}

function _catalogWhereSeries(string $q, int $categoryId, array &$params, string &$types): string
{
    $conds = [];
    if ($q !== '') {
        $conds[] = 's.title LIKE ?';
        $params[] = '%' . escapeLike($q) . '%';
        $types .= 's';
    }
    if ($categoryId > 0) {
        $conds[] = 's.category_id = ?';
        $params[] = $categoryId;
        $types .= 'i';
    }
    return $conds ? ' WHERE ' . implode(' AND ', $conds) : '';
}

function countSeries(mysqli $conn, string $q = '', int $categoryId = 0): int
{
    $params = [];
    $types  = '';
    $where  = _catalogWhereSeries($q, $categoryId, $params, $types);
    $row = fetchOne($conn, 'SELECT COUNT(*) AS c FROM series s' . $where, $params, $types);
    return (int)($row['c'] ?? 0);
}

function getSeriesPaged(mysqli $conn, string $q, int $categoryId, int $limit, int $offset): array
{
    $params = [];
    $types  = '';
    $where  = _catalogWhereSeries($q, $categoryId, $params, $types);
    $params[] = $limit;
    $params[] = $offset;
    $types   .= 'ii';
    return fetchAll($conn,
        'SELECT s.*, c.name AS category_name
           FROM series s
           LEFT JOIN categories c ON c.id = s.category_id'
        . $where .
        ' ORDER BY s.rating DESC, s.year_start DESC, s.id ASC
          LIMIT ? OFFSET ?',
        $params, $types);
}

// --- Избранное / история / подписки ---

function isFavorite(mysqli $conn, int $userId, string $type, int $itemId): bool
{
    if ($userId <= 0 || $itemId <= 0) return false;
    $row = fetchOne($conn,
        'SELECT 1 FROM favorites WHERE user_id = ? AND item_type = ? AND item_id = ? LIMIT 1',
        [$userId, $type, $itemId], 'isi');
    return $row !== null;
}

// Переключить избранное: true — добавлено, false — удалено
function toggleFavorite(mysqli $conn, int $userId, string $type, int $itemId): bool
{
    $stmt = $conn->prepare(
        'DELETE FROM favorites WHERE user_id = ? AND item_type = ? AND item_id = ?'
    );
    $stmt->bind_param('isi', $userId, $type, $itemId);
    $stmt->execute();
    $deleted = $stmt->affected_rows > 0;
    $stmt->close();

    if ($deleted) return false;

    $ins = $conn->prepare(
        'INSERT INTO favorites (user_id, item_type, item_id) VALUES (?, ?, ?)'
    );
    $ins->bind_param('isi', $userId, $type, $itemId);
    $ins->execute();
    $ins->close();
    return true;
}

function getUserFavorites(mysqli $conn, int $userId): array
{
    return fetchAll($conn,
        'SELECT f.id, f.item_type, f.item_id, f.created_at,
                COALESCE(m.title,      s.title)      AS title,
                COALESCE(m.slug,       s.slug)       AS slug,
                COALESCE(m.poster_url, s.poster_url) AS poster_url,
                COALESCE(m.rating,     s.rating)     AS rating,
                COALESCE(m.year,       s.year_start) AS year
           FROM favorites f
           LEFT JOIN movies m ON m.id = f.item_id AND f.item_type = "movie"
           LEFT JOIN series s ON s.id = f.item_id AND f.item_type = "series"
          WHERE f.user_id = ?
          ORDER BY f.created_at DESC',
        [$userId], 'i');
}

function addViewHistory(mysqli $conn, int $userId, string $type, int $itemId): void
{
    if ($type === 'movie') {
        $existing = fetchOne($conn,
            'SELECT id FROM view_history WHERE user_id = ? AND movie_id = ? LIMIT 1',
            [$userId, $itemId], 'ii');
        if ($existing) {
            $upd = $conn->prepare('UPDATE view_history SET watched_at = NOW() WHERE id = ?');
            $upd->bind_param('i', $existing['id']);
            $upd->execute();
            $upd->close();
            return;
        }
        $stmt = $conn->prepare('INSERT INTO view_history (user_id, movie_id) VALUES (?, ?)');
    } else {
        $existing = fetchOne($conn,
            'SELECT id FROM view_history WHERE user_id = ? AND series_id = ? LIMIT 1',
            [$userId, $itemId], 'ii');
        if ($existing) {
            $upd = $conn->prepare('UPDATE view_history SET watched_at = NOW() WHERE id = ?');
            $upd->bind_param('i', $existing['id']);
            $upd->execute();
            $upd->close();
            return;
        }
        $stmt = $conn->prepare('INSERT INTO view_history (user_id, series_id) VALUES (?, ?)');
    }
    $stmt->bind_param('ii', $userId, $itemId);
    $stmt->execute();
    $stmt->close();
}

function getUserHistory(mysqli $conn, int $userId, int $limit = 50): array
{
    return fetchAll($conn,
        'SELECT vh.id, vh.movie_id, vh.series_id, vh.watched_at,
                CASE WHEN vh.movie_id IS NOT NULL THEN "movie" ELSE "series" END AS item_type,
                COALESCE(m.title,      s.title)      AS title,
                COALESCE(m.slug,       s.slug)       AS slug,
                COALESCE(m.poster_url, s.poster_url) AS poster_url,
                COALESCE(m.rating,     s.rating)     AS rating
           FROM view_history vh
           LEFT JOIN movies m ON m.id = vh.movie_id
           LEFT JOIN series s ON s.id = vh.series_id
          WHERE vh.user_id = ?
          ORDER BY vh.watched_at DESC
          LIMIT ?',
        [$userId, $limit], 'ii');
}

function getActiveSubscription(mysqli $conn, int $userId): ?array
{
    return fetchOne($conn,
        'SELECT s.*, p.name AS plan_name, p.price, p.quality, p.screens
           FROM subscriptions s
           JOIN plans p ON p.id = s.plan_id
          WHERE s.user_id = ?
            AND s.status = "active"
            AND s.expires_at > NOW()
          ORDER BY s.expires_at DESC
          LIMIT 1',
        [$userId], 'i');
}

// --- Тарифы ---

function getAllPlans(mysqli $conn, bool $onlyActive = true): array
{
    $sql = 'SELECT * FROM plans';
    if ($onlyActive) $sql .= ' WHERE is_active = 1';
    $sql .= ' ORDER BY price ASC, id ASC';
    return fetchAll($conn, $sql);
}

function getPlanById(mysqli $conn, int $id): ?array
{
    return fetchOne($conn,
        'SELECT * FROM plans WHERE id = ? LIMIT 1',
        [$id], 'i');
}

// Отменить все активные подписки пользователя
function cancelActiveSubscriptions(mysqli $conn, int $userId): void
{
    $stmt = $conn->prepare(
        'UPDATE subscriptions SET status = "cancelled"
          WHERE user_id = ? AND status = "active"'
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();
}

// Создать подписку, вернуть id
function createSubscription(mysqli $conn, int $userId, int $planId, int $durationDays): int
{
    $expires = date('Y-m-d H:i:s', strtotime("+{$durationDays} days"));
    $stmt = $conn->prepare(
        'INSERT INTO subscriptions (user_id, plan_id, status, expires_at)
         VALUES (?, ?, "active", ?)'
    );
    $stmt->bind_param('iis', $userId, $planId, $expires);
    $stmt->execute();
    $id = (int)$stmt->insert_id;
    $stmt->close();
    return $id;
}

// Продлить подписку на N дней от текущей даты окончания
function extendSubscription(mysqli $conn, int $subscriptionId, int $durationDays): void
{
    $stmt = $conn->prepare(
        'UPDATE subscriptions
            SET expires_at = DATE_ADD(GREATEST(expires_at, NOW()), INTERVAL ? DAY),
                status = "active"
          WHERE id = ?'
    );
    $stmt->bind_param('ii', $durationDays, $subscriptionId);
    $stmt->execute();
    $stmt->close();
}

// user → subscriber (admin/subscriber не трогаем)
function promoteToSubscriber(mysqli $conn, int $userId): void
{
    $stmt = $conn->prepare(
        'UPDATE users SET role = "subscriber" WHERE id = ? AND role = "user"'
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();
}