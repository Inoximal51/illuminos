<?php
// Каталог сериалов: поиск, категория, пагинация. URL: series.php?q=&cat=&page=N
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';

$q       = trim((string)($_GET['q'] ?? ''));
$catId   = (int)($_GET['cat'] ?? 0);
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;

$total      = countSeries($conn, $q, $catId);
$totalPages = max(1, (int)ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$series     = getSeriesPaged($conn, $q, $catId, $perPage, $offset);
$categories = getCategories($conn);
$activeCat  = $catId > 0 ? getCategoryById($conn, $catId) : null;

$queryParams = [];
if ($q !== '')  $queryParams['q']   = $q;
if ($catId > 0) $queryParams['cat'] = $catId;

$pageTitle = $activeCat
    ? 'Сериалы — ' . $activeCat['name'] . ' — Illuminos'
    : 'Сериалы — Illuminos';
$pageDescription = 'Каталог сериалов Illuminos с поиском и фильтрами по жанрам.';
$activePage      = 'series';

require __DIR__ . '/includes/header.php';
?>
<main>

  <div class="container breadcrumbs" aria-label="Навигационная цепочка">
    <a href="index.php">Главная</a><span>/</span>
    <span aria-current="page">Сериалы<?= $activeCat ? ': ' . e($activeCat['name']) : '' ?></span>
  </div>

  <section class="section container">
    <h1 class="section-title">
      Сериалы
      <span class="section-title__hint">
        <?= $total ?> <?= $total === 1 ? 'сериал' : ($total >= 2 && $total <= 4 ? 'сериала' : 'сериалов') ?>
      </span>
    </h1>

    <form class="catalog-toolbar" action="series.php" method="get" role="search">
      <div class="catalog-toolbar__field">
        <label class="catalog-toolbar__label" for="catalog-q">Поиск по названию</label>
        <input class="catalog-toolbar__input" id="catalog-q" type="search" name="q"
               value="<?= e($q) ?>" placeholder="Например, Дюна" autocomplete="off">
      </div>

      <div class="catalog-toolbar__field">
        <label class="catalog-toolbar__label" for="catalog-cat">Категория</label>
        <select class="catalog-toolbar__select" id="catalog-cat" name="cat">
          <option value="0">Все категории</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= (int)$cat['id'] === $catId ? 'selected' : '' ?>>
              <?= e($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="catalog-toolbar__actions">
        <button type="submit" class="btn btn--accent">Применить</button>
        <?php if ($q !== '' || $catId > 0): ?>
          <a href="series.php" class="btn btn--ghost">Сбросить</a>
        <?php endif; ?>
      </div>
    </form>

    <?php if (!$series): ?>
      <div class="empty-state">
        <p class="empty-state__text">
          <?php if ($q !== '' || $catId > 0): ?>
            По заданным фильтрам сериалы не найдены. Попробуйте изменить параметры.
          <?php else: ?>
            В каталоге пока нет сериалов.
          <?php endif; ?>
        </p>
        <a href="series.php" class="btn btn--accent">Сбросить фильтры</a>
      </div>
    <?php else: ?>
      <div class="movies-grid">
        <?php $cardType = 'series'; foreach ($series as $m): ?>
          <?php include __DIR__ . '/includes/movie-card.php'; ?>
        <?php endforeach; ?>
      </div>

      <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Пагинация каталога сериалов">
          <?php if ($page > 1): ?>
            <a class="pagination__link" href="<?= e(pageUrl($queryParams, $page - 1)) ?>" aria-label="Предыдущая">← Назад</a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === 1 || $i === $totalPages || abs($i - $page) <= 2): ?>
              <a class="pagination__link <?= $i === $page ? 'is-active' : '' ?>"
                 href="<?= e(pageUrl($queryParams, $i)) ?>"
                 <?= $i === $page ? 'aria-current="page"' : '' ?>><?= $i ?></a>
            <?php elseif (abs($i - $page) === 3): ?>
              <span class="pagination__dots">…</span>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <a class="pagination__link" href="<?= e(pageUrl($queryParams, $page + 1)) ?>" aria-label="Следующая">Вперёд →</a>
          <?php endif; ?>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </section>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>