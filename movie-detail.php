<?php
// Детальная страница фильма. URL: movie-detail.php?slug=dune-2
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

$slug = isset($_GET['slug']) ? trim((string)$_GET['slug']) : '';
if ($slug === '') { header('Location: movies.php'); exit; }

$movie = getMovieBySlug($conn, $slug);

if ($movie === null) {
    http_response_code(404);
    $pageTitle       = 'Фильм не найден — Illuminos';
    $pageDescription = '';
    $activePage      = 'movies';
    require __DIR__ . '/includes/header.php';
    ?>
    <main><div class="container section">
      <h1 class="section-title">404 — фильм не найден</h1>
      <p><a href="movies.php" class="btn btn--accent">← Все фильмы</a></p>
    </div></main>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$movieId     = (int)$movie['id'];
$cast        = getMovieCast($conn, $movieId);
$similar     = getSimilarMovies($conn, $movieId, (int)($movie['category_id'] ?? 0), 6);
$currentUser = current_user($conn);
$isFav       = $currentUser !== null
    ? isFavorite($conn, (int)$currentUser['id'], 'movie', $movieId)
    : false;

$pageTitle       = $movie['title'] . ' — Illuminos';
$pageDescription = mb_substr($movie['description'] ?? '', 0, 160);
$activePage      = 'movies';

require __DIR__ . '/includes/header.php';
?>
<main>

  <div class="container breadcrumbs" aria-label="Навигационная цепочка">
    <a href="index.php">Главная</a><span>/</span>
    <a href="movies.php">Фильмы</a><span>/</span>
    <span aria-current="page"><?= e($movie['title']) ?></span>
  </div>

  <section class="detail-hero">
    <img class="detail-hero__bg" src="<?= e($movie['backdrop_url']) ?>" alt="" aria-hidden="true">
    <div class="detail-hero__overlay"></div>
    <div class="container detail-hero__grid">

      <div class="detail-hero__poster">
        <img src="<?= e($movie['poster_url']) ?>" alt="Постер: <?= e($movie['title']) ?>">
      </div>

      <div>
        <h1 class="detail-hero__title"><?= e($movie['title']) ?></h1>
        <div class="detail-hero__meta">
          <span><?= (int)$movie['year'] ?></span>
          <span><?= (int)$movie['duration_min'] ?> мин</span>
          <span><?= e($movie['age_rating']) ?></span>
          <span class="rating">★ <?= number_format((float)$movie['rating'], 1) ?></span>
        </div>
        <div class="detail-hero__genres">
          <span><?= e($movie['category_name']) ?></span>
        </div>

        <div class="detail-hero__actions">
          <?php if ($currentUser): ?>
            <a href="watch.php?type=movie&slug=<?= e($movie['slug']) ?>" class="btn btn--accent">Смотреть</a>
          <?php else: ?>
            <a href="login.php?redirect=<?= e(urlencode('watch.php?type=movie&slug=' . $movie['slug'])) ?>"
               class="btn btn--accent">Смотреть</a>
          <?php endif; ?>

          <?php if ($currentUser): ?>
            <form action="favorite-toggle.php" method="post" class="fav-form">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="item_type" value="movie">
              <input type="hidden" name="item_id" value="<?= $movieId ?>">
              <input type="hidden" name="slug" value="<?= e($movie['slug']) ?>">
              <input type="hidden" name="redirect" value="movie-detail.php?slug=<?= e($movie['slug']) ?>">
              <button type="submit" class="btn btn--ghost" aria-pressed="<?= $isFav ? 'true' : 'false' ?>">
                <?= $isFav ? '✓ В избранном' : '＋ В избранное' ?>
              </button>
            </form>
          <?php else: ?>
            <a href="login.php?redirect=<?= e(urlencode('movie-detail.php?slug=' . $movie['slug'])) ?>"
               class="btn btn--ghost">＋ В избранное</a>
          <?php endif; ?>
        </div>

        <p class="detail-hero__desc"><?= e($movie['description']) ?></p>
      </div>
    </div>
  </section>

  <?php if ($cast): ?>
  <section class="section container" aria-labelledby="cast-title">
    <h2 id="cast-title" class="section-title">Актёры и режиссёры</h2>
    <div class="cast-grid">
      <?php foreach ($cast as $person): ?>
        <div class="cast-card">
          <div class="cast-card__avatar"><?= e(initials($person['person_name'])) ?></div>
          <div>
            <p class="cast-card__name"><?= e($person['person_name']) ?></p>
            <p class="cast-card__role"><?= e($person['role']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($movie['trailer_url'])): ?>
  <section class="section container" aria-labelledby="trailer-title">
    <h2 id="trailer-title" class="section-title">Трейлер</h2>
    <div class="trailer">
      <iframe src="<?= e($movie['trailer_url']) ?>"
              title="Трейлер фильма <?= e($movie['title']) ?>"
              allowfullscreen
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($similar): ?>
  <section class="section container" aria-labelledby="similar-title">
    <h2 id="similar-title" class="section-title">Похожие фильмы</h2>
    <div class="movies-row">
      <?php $cardType = 'movie'; foreach ($similar as $m): ?>
        <?php include __DIR__ . '/includes/movie-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>