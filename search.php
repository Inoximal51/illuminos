<?php
// Поиск по фильмам и сериалам. URL: search.php?q=...
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';

$q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';

$movies = [];
$series = [];
if ($q !== '') {
    $movies = searchMovies($conn, $q, 50);
    $series = searchSeries($conn, $q, 50);
}
$total = count($movies) + count($series);

$pageTitle       = $q !== '' ? 'Поиск: ' . $q . ' — Illuminos' : 'Поиск — Illuminos';
$pageDescription = 'Поиск фильмов и сериалов на Illuminos';
$activePage      = 'home';

require __DIR__ . '/includes/header.php';
?>
<main>

  <div class="container breadcrumbs" aria-label="Навигационная цепочка">
    <a href="index.php">Главная</a><span>/</span>
    <span aria-current="page">Поиск<?= $q !== '' ? ': ' . e($q) : '' ?></span>
  </div>

  <section class="section container" aria-labelledby="search-title">
    <h1 id="search-title" class="section-title">
      <?php if ($q !== ''): ?>
        Результаты поиска по запросу «<?= e($q) ?>»
        <span class="section-title__hint"><?= $total ?> найдено</span>
      <?php else: ?>
        Поиск по фильмам и сериалам
      <?php endif; ?>
    </h1>

    <form class="search-page-form" role="search" action="search.php" method="get">
      <label for="search-page-input" class="visually-hidden">Поиск</label>
      <input id="search-page-input" type="search" name="q"
             class="search-page-form__input"
             value="<?= e($q) ?>"
             placeholder="Введите название фильма или сериала…"
             autocomplete="off">
      <button type="submit" class="btn btn--accent">Искать</button>
    </form>

    <?php if ($q === ''): ?>
      <div class="empty-state">
        <p class="empty-state__text">Введите название фильма или сериала в строке поиска.</p>
      </div>
    <?php elseif ($total === 0): ?>
      <div class="empty-state">
        <p class="empty-state__text">По запросу «<?= e($q) ?>» ничего не найдено. Попробуйте другое название.</p>
        <a href="index.php" class="btn btn--accent">← На главную</a>
      </div>
    <?php else: ?>
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
    <?php endif; ?>
  </section>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>