<?php
// Личный кабинет. Только для авторизованных.
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$currentUser = current_user($conn);
if ($currentUser === null) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$userId       = (int)$currentUser['id'];
$favorites    = getUserFavorites($conn, $userId);
$history      = getUserHistory($conn, $userId, 20);
$subscription = getActiveSubscription($conn, $userId);

$pageTitle       = 'Личный кабинет — Illuminos';
$pageDescription = 'Управляйте избранным, историей просмотров и подпиской.';
$activePage      = 'cabinet';

require __DIR__ . '/includes/header.php';
?>
<main>

  <div class="container breadcrumbs" aria-label="Навигационная цепочка">
    <a href="index.php">Главная</a><span>/</span>
    <span aria-current="page">Личный кабинет</span>
  </div>

  <section class="cabinet container">

    <header class="cabinet-header">
      <div class="cabinet-header__avatar"><?= e(initials($currentUser['name'])) ?></div>
      <div class="cabinet-header__info">
        <h1 class="cabinet-header__name"><?= e($currentUser['name']) ?></h1>
        <p class="cabinet-header__email"><?= e($currentUser['email']) ?></p>
        <p class="cabinet-header__email">
          Аккаунт создан: <?= e(date('d.m.Y', strtotime($currentUser['created_at']))) ?>
        </p>
      </div>
      <span class="cabinet-header__role cabinet-header__role--<?= e($currentUser['role']) ?>">
        <?= e($currentUser['role']) ?>
      </span>
    </header>

    <div class="cabinet-grid">

      <div>
        <section class="cabinet-section">
          <h2 class="cabinet-section__title">История просмотров</h2>
          <?php if (!$history): ?>
            <p class="cabinet-empty">
              История пуста. Нажмите «Смотреть» на любой странице фильма или сериала —
              просмотр появится здесь.
            </p>
          <?php else: ?>
            <div class="history-list">
              <?php foreach ($history as $h): ?>
                <?php
                  $link = ($h['item_type'] === 'movie' ? 'movie-detail.php' : 'series-detail.php')
                        . '?slug=' . urlencode($h['slug']);
                ?>
                <a class="history-item" href="<?= e($link) ?>">
                  <div class="history-item__poster">
                    <img src="<?= e($h['poster_url']) ?>" alt="" loading="lazy">
                  </div>
                  <div class="history-item__info">
                    <p class="history-item__title"><?= e($h['title']) ?></p>
                    <p class="history-item__meta">
                      <?= $h['item_type'] === 'movie' ? 'Фильм' : 'Сериал' ?>
                      · <?= e(date('d.m.Y H:i', strtotime($h['watched_at']))) ?>
                    </p>
                  </div>
                  <span class="history-item__rating">★ <?= number_format((float)$h['rating'], 1) ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>

        <section class="cabinet-section">
          <h2 class="cabinet-section__title">Избранное (<?= count($favorites) ?>)</h2>
          <?php if (!$favorites): ?>
            <p class="cabinet-empty">
              Пока пусто. Откройте страницу фильма или сериала и нажмите «В избранное».
            </p>
          <?php else: ?>
            <div class="movies-grid">
              <?php foreach ($favorites as $m): ?>
                <?php $cardType = $m['item_type']; ?>
                <?php include __DIR__ . '/includes/movie-card.php'; ?>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>
      </div>

      <aside>
        <section class="cabinet-section">
          <h2 class="cabinet-section__title">Подписка</h2>
          <?php if ($subscription): ?>
            <div class="subscription-card">
              <p class="subscription-card__plan"><?= e($subscription['plan_name']) ?></p>
              <p class="subscription-card__meta">
                Качество: <strong><?= e($subscription['quality']) ?></strong><br>
                Экранов: <strong><?= (int)$subscription['screens'] ?></strong><br>
                Действует до: <strong><?= e(date('d.m.Y', strtotime($subscription['expires_at']))) ?></strong>
              </p>
            </div>
            <a href="checkout.php?plan_id=<?= (int)$subscription['plan_id'] ?>" class="btn btn--accent btn--block mb-2">
              Продлить
            </a>
            <a href="subscription.php" class="btn btn--ghost btn--block">
              Сменить тариф
            </a>
          <?php else: ?>
            <p class="cabinet-empty">
              Активной подписки нет. Оформите тариф, чтобы смотреть без ограничений.
            </p>
            <a href="subscription.php" class="btn btn--accent btn--block">Выбрать тариф</a>
          <?php endif; ?>
        </section>

        <?php if ($currentUser['role'] === 'admin'): ?>
          <section class="cabinet-section">
            <h2 class="cabinet-section__title">Админ-панель</h2>
            <p class="cabinet-empty">
              Управление контентом и пользователями появится в следующей версии.
              Сейчас доступна заглушка для демонстрации роли <code>admin</code>.
            </p>
          </section>
        <?php endif; ?>
      </aside>

    </div>

  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>