<?php
// Главная: hero, новинки, популярное, сериалы, категории, рекомендации.
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';

$featured   = getFeaturedMovie($conn);
$newMovies  = getNewMovies($conn, 10);
$popular    = getPopularMovies($conn, 10);
$allSeries  = getSeries($conn, 6);
$categories = getCategories($conn);

$pageTitle       = 'Illuminos — онлайн-кинотеатр';
$pageDescription = 'Смотрите фильмы и сериалы онлайн. Новинки, популярное, рекомендации.';
$activePage      = 'home';

require __DIR__ . '/includes/header.php';
?>
<main>

  <?php if ($featured): ?>
  <section class="hero">
    <img class="hero__bg" src="<?= e($featured['backdrop_url']) ?>" alt="" aria-hidden="true">
    <div class="hero__overlay"></div>
    <div class="container hero__content">
      <span class="hero__badge">Новинка</span>
      <h1 class="hero__title"><?= e($featured['title']) ?></h1>
      <div class="hero__meta">
        <span><?= (int)$featured['year'] ?></span>
        <span><?= (int)$featured['duration_min'] ?> мин</span>
        <span><?= e($featured['category_name']) ?></span>
        <span>★ <?= number_format((float)$featured['rating'], 1) ?></span>
      </div>
      <p class="hero__desc"><?= e($featured['description']) ?></p>
      <div class="hero__actions">
        <a href="movie-detail.php?slug=<?= e($featured['slug']) ?>" class="btn btn--accent">Смотреть</a>
        <a href="movie-detail.php?slug=<?= e($featured['slug']) ?>" class="btn btn--ghost">Подробнее</a>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="section container" aria-labelledby="new-title">
    <h2 id="new-title" class="section-title">Топ новинки <span class="section-title__hint">Свежие релизы</span></h2>
    <div class="movies-row">
      <?php $cardType = 'movie'; foreach ($newMovies as $m): ?>
        <?php include __DIR__ . '/includes/movie-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="section container" aria-labelledby="popular-title">
    <h2 id="popular-title" class="section-title">Популярное <span class="section-title__hint">Смотрит весь мир</span></h2>
    <div class="movies-grid">
      <?php $cardType = 'movie'; foreach ($popular as $m): ?>
        <?php include __DIR__ . '/includes/movie-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </section>

  <?php if ($allSeries): ?>
  <section class="section container" aria-labelledby="series-title">
    <h2 id="series-title" class="section-title">Сериалы <span class="section-title__hint">Многосерийное</span></h2>
    <div class="movies-grid">
      <?php $cardType = 'series'; foreach ($allSeries as $m): ?>
        <?php include __DIR__ . '/includes/movie-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="section container" aria-labelledby="cats-title">
    <h2 id="cats-title" class="section-title">Категории <span class="section-title__hint">Выберите жанр</span></h2>
    <nav class="categories" aria-label="Категории контента">
      <a href="index.php" class="category-chip is-active">Все</a>
      <?php foreach ($categories as $cat): ?>
        <a href="category.php?id=<?= (int)$cat['id'] ?>" class="category-chip">
          <?= e($cat['name']) ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </section>

  <section class="section container" aria-labelledby="rec-title">
    <h2 id="rec-title" class="section-title">Рекомендации для вас <span class="section-title__hint">Подобрано по вашим интересам</span></h2>
    <div class="movies-row">
      <?php $cardType = 'movie'; foreach (array_slice($popular, 0, 5) as $m): ?>
        <?php include __DIR__ . '/includes/movie-card.php'; ?>
      <?php endforeach; ?>
    </div>
    <div class="rec-cta">
      <p class="rec-cta__text">
        Войдите в аккаунт, чтобы получать персональные рекомендации на основе истории просмотров.
      </p>
      <a href="login.php" class="btn btn--accent">Войти в аккаунт</a>
    </div>
  </section>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>