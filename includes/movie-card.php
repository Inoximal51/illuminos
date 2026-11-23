<?php
// Карточка фильма/сериала. Вход: $m, $cardType ('movie'|'series').
$link = ($cardType ?? 'movie') === 'series' ? 'series-detail.php' : 'movie-detail.php';
$year = isset($m['year']) ? (int)$m['year']
      : (isset($m['year_start']) ? (int)$m['year_start'] : 0);
?>
<article class="movie-card">
  <a class="movie-card__poster" href="<?= $link ?>?slug=<?= e($m['slug']) ?>">
    <span class="movie-card__rating">★ <?= number_format((float)$m['rating'], 1) ?></span>
    <img src="<?= e($m['poster_url']) ?>" alt="Постер: <?= e($m['title']) ?>" loading="lazy">
  </a>
  <h3 class="movie-card__title"><?= e($m['title']) ?></h3>
  <p class="movie-card__year"><?= $year ?></p>
</article>