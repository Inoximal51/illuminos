<?php
// Страница категории. URL: category.php?id=N
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Без id — редирект на первую категорию
if ($id <= 0) {
    $categories = getCategories($conn);
    if (empty($categories)) { http_response_code(404); exit('В базе нет категорий.'); }
    header('Location: category.php?id=' . (int)$categories[0]['id']);
    exit;
}

$category = getCategoryById($conn, $id);

if ($category === null) {
    http_response_code(404);
    $pageTitle       = 'Категория не найдена — Illuminos';
    $pageDescription = '';
    $activePage      = 'home';
    require __DIR__ . '/includes/header.php';
    ?>
    <main><div class="container section">
      <h1 class="section-title">404 — категория не найдена</h1>
      <p><a href="index.php" class="btn btn--accent">← На главную</a></p>
    </div></main>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$movies     = getMoviesByCategory($conn, $id, 50);
$series     = getSeriesByCategory($conn, $id, 50);
$categories = getCategories($conn);

$pageTitle       = $category['name'] . ' — Illuminos';
$pageDescription = 'Фильмы и сериалы в категории «' . $category['name'] . '» на Illuminos.';
$activePage      = 'home';

require __DIR__ . '/includes/header.php';
?>
<main>

  <div class="container breadcrumbs" aria-label="Навигационная цепочка">
    <a href="index.php">Главная</a><span>/</span>
    <span aria-current="page">Категория: <?= e($category['name']) ?></span>
  </div>

  <section class="section container" aria-labelledby="cat-title">
    <h1 id="cat-title" class="section-title"><?= e($category['name']) ?></h1>

    <nav class="categories mb-4" aria-label="Все категории">
      <a href="index.php" class="category-chip">← Все</a>
      <?php foreach ($categories as $cat): ?>
        <a href="category.php?id=<?= (int)$cat['id'] ?>"
           class="category-chip <?= (int)$cat['id'] === $id ? 'is-active' : '' ?>">
          <?= e($cat['name']) ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <?php if ($movies): ?>
      <h2 class="h4 mt-4 mb-3">Фильмы (<?= count($movies) ?>)</h2>
      <div class="movies-grid">
        <?php $cardType = 'movie'; foreach ($movies as $m): ?>
          <?php include __DIR__ . '/includes/movie-card.php'; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($series): ?>
      <h2 class="h4 mt-4 mb-3">Сериалы (<?= count($series) ?>)</h2>
      <div class="movies-grid">
        <?php $cardType = 'series'; foreach ($series as $m): ?>
          <?php include __DIR__ . '/includes/movie-card.php'; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!$movies && !$series): ?>
      <div class="empty-state">
        <p class="empty-state__text">В категории «<?= e($category['name']) ?>» пока нет фильмов или сериалов.</p>
        <a href="index.php" class="btn btn--accent">← На главную</a>
      </div>
    <?php endif; ?>
  </section>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>