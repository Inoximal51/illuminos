<?php
// Детальная страница сериала. URL: series-detail.php?slug=dune-prophecy
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

$slug = isset($_GET['slug']) ? trim((string)$_GET['slug']) : '';
if ($slug === '') { header('Location: series.php'); exit; }

$series = getSeriesBySlug($conn, $slug);

if ($series === null) {
    http_response_code(404);
    $pageTitle       = 'Сериал не найден — Illuminos';
    $pageDescription = '';
    $activePage      = 'series';
    require __DIR__ . '/includes/header.php';
    ?>
    <main><div class="container section">
      <h1 class="section-title">404 — сериал не найден</h1>
      <p><a href="series.php" class="btn btn--accent">← Все сериалы</a></p>
    </div></main>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$seriesId    = (int)$series['id'];
$cast        = getSeriesCast($conn, $seriesId);
$seasons     = getSeriesSeasons($conn, $seriesId);
$similar     = getSimilarSeries($conn, $seriesId, (int)($series['category_id'] ?? 0), 6);
$currentUser = current_user($conn);
$isFav       = $currentUser !== null
    ? isFavorite($conn, (int)$currentUser['id'], 'series', $seriesId)
    : false;

$pageTitle       = $series['title'] . ' — Illuminos';
$pageDescription = mb_substr($series['description'] ?? '', 0, 160);
$activePage      = 'series';

require __DIR__ . '/includes/header.php';
?>
<main>

  <div class="container breadcrumbs" aria-label="Навигационная цепочка">
    <a href="index.php">Главная</a><span>/</span>
    <a href="series.php">Сериалы</a><span>/</span>
    <span aria-current="page"><?= e($series['title']) ?></span>
  </div>

  <section class="detail-hero">
    <img class="detail-hero__bg" src="<?= e($series['backdrop_url']) ?>" alt="" aria-hidden="true">
    <div class="detail-hero__overlay"></div>
    <div class="container detail-hero__grid">

      <div class="detail-hero__poster">
        <img src="<?= e($series['poster_url']) ?>" alt="Постер: <?= e($series['title']) ?>">
      </div>

      <div>
        <h1 class="detail-hero__title"><?= e($series['title']) ?></h1>
        <div class="detail-hero__meta">
          <span><?= (int)$series['year_start'] ?><?= $series['year_end'] ? ' — ' . (int)$series['year_end'] : ' — н.в.' ?></span>
          <span><?= count($seasons) ?> сезон<?= count($seasons) === 1 ? '' : 'а' ?></span>
          <span><?= e($series['age_rating']) ?></span>
          <span class="rating">★ <?= number_format((float)$series['rating'], 1) ?></span>
        </div>
        <div class="detail-hero__genres">
          <span><?= e($series['category_name']) ?></span>
        </div>

        <div class="detail-hero__actions">
          <?php if ($currentUser): ?>
            <a href="watch.php?type=series&slug=<?= e($series['slug']) ?>" class="btn btn--accent">Смотреть</a>
          <?php else: ?>
            <a href="login.php?redirect=<?= e(urlencode('watch.php?type=series&slug=' . $series['slug'])) ?>"
               class="btn btn--accent">Смотреть</a>
          <?php endif; ?>

          <?php if ($currentUser): ?>
            <form action="favorite-toggle.php" method="post" class="fav-form">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="item_type" value="series">
              <input type="hidden" name="item_id" value="<?= $seriesId ?>">
              <input type="hidden" name="slug" value="<?= e($series['slug']) ?>">
              <input type="hidden" name="redirect" value="series-detail.php?slug=<?= e($series['slug']) ?>">
              <button type="submit" class="btn btn--ghost" aria-pressed="<?= $isFav ? 'true' : 'false' ?>">
                <?= $isFav ? '✓ В избранном' : '＋ В избранное' ?>
              </button>
            </form>
          <?php else: ?>
            <a href="login.php?redirect=<?= e(urlencode('series-detail.php?slug=' . $series['slug'])) ?>"
               class="btn btn--ghost">＋ В избранное</a>
          <?php endif; ?>
        </div>

        <p class="detail-hero__desc"><?= e($series['description']) ?></p>
      </div>
    </div>
  </section>

  <?php if ($seasons): ?>
  <section class="section container" aria-labelledby="seasons-title">
    <h2 id="seasons-title" class="section-title">Сезоны и эпизоды</h2>
    <div class="accordion season-accordion" id="seasonsAccordion">
      <?php foreach ($seasons as $idx => $season): ?>
        <?php
          $collapseId = 'season-' . (int)$season['id'];
          $isFirst    = $idx === 0;
        ?>
        <div class="accordion-item">
          <h3 class="accordion-header">
            <button class="accordion-button <?= $isFirst ? '' : 'collapsed' ?>"
                    type="button" data-bs-toggle="collapse"
                    data-bs-target="#<?= $collapseId ?>"
                    aria-expanded="<?= $isFirst ? 'true' : 'false' ?>"
                    aria-controls="<?= $collapseId ?>">
              Сезон <?= (int)$season['number'] ?> · <?= count($season['episodes']) ?> эпизод<?= count($season['episodes']) === 1 ? '' : 'ов' ?>
            </button>
          </h3>
          <div id="<?= $collapseId ?>"
               class="accordion-collapse collapse <?= $isFirst ? 'show' : '' ?>"
               data-bs-parent="#seasonsAccordion">
            <div class="accordion-body">
              <?php if (!$season['episodes']): ?>
                <p>Эпизоды появятся после премьеры.</p>
              <?php else: ?>
                <?php foreach ($season['episodes'] as $ep): ?>
                  <div class="episode">
                    <span><?= (int)$season['number'] ?>×<?= sprintf('%02d', (int)$ep['number']) ?> · «<?= e($ep['title']) ?>»</span>
                    <span><?= (int)$ep['duration_min'] ?> мин</span>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

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

  <?php if (!empty($series['trailer_url'])): ?>
  <section class="section container" aria-labelledby="trailer-title">
    <h2 id="trailer-title" class="section-title">Трейлер</h2>
    <div class="trailer">
      <iframe src="<?= e($series['trailer_url']) ?>"
              title="Трейлер сериала <?= e($series['title']) ?>"
              allowfullscreen
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($similar): ?>
  <section class="section container" aria-labelledby="similar-title">
    <h2 id="similar-title" class="section-title">Похожие сериалы</h2>
    <div class="movies-row">
      <?php $cardType = 'series'; foreach ($similar as $m): ?>
        <?php include __DIR__ . '/includes/movie-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>