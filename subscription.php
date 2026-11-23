<?php
// Витрина тарифов. URL: subscription.php
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

$plans        = getAllPlans($conn);
$currentUser  = current_user($conn);
$subscription = $currentUser !== null
    ? getActiveSubscription($conn, (int)$currentUser['id'])
    : null;

$pageTitle       = 'Подписка — Illuminos';
$pageDescription = 'Выберите тариф Illuminos: HD, Full HD или 4K.';
$activePage      = 'subscription';

require __DIR__ . '/includes/header.php';
?>
<main>

  <div class="container breadcrumbs" aria-label="Навигационная цепочка">
    <a href="index.php">Главная</a><span>/</span>
    <span aria-current="page">Подписка</span>
  </div>

  <section class="section container">
    <h1 class="section-title">Тарифы подписки</h1>
    <p class="section-subtitle">
      Смотрите фильмы и сериалы без рекламы в любом качестве. Оформите подписку — отмена в любой момент.
    </p>

    <?php if ($subscription): ?>
      <div class="subscription-banner">
        <div>
          <p class="subscription-banner__title">Ваш активный тариф: <?= e($subscription['plan_name']) ?></p>
          <p class="subscription-banner__meta">
            Действует до <?= e(date('d.m.Y', strtotime($subscription['expires_at']))) ?>
            · <?= e($subscription['quality']) ?> · <?= (int)$subscription['screens'] ?> экран(ов)
          </p>
        </div>
        <a href="cabinet.php" class="btn btn--ghost">В личный кабинет</a>
      </div>
    <?php endif; ?>

    <?php if (!$plans): ?>
      <div class="empty-state">
        <p class="empty-state__text">Тарифы временно недоступны. Загляните позже.</p>
      </div>
    <?php else: ?>
      <div class="plans-grid">
        <?php foreach ($plans as $idx => $plan):
          $isCurrent  = $subscription && (int)$subscription['plan_id'] === (int)$plan['id'];
          $isFeatured = $idx === 1;
          $features   = array_filter(array_map('trim', explode(',', (string)$plan['features'])));
        ?>
          <article class="plan-card <?= $isFeatured ? 'plan-card--featured' : '' ?>">
            <?php if ($isFeatured): ?>
              <span class="plan-card__badge">Популярный</span>
            <?php endif; ?>

            <h2 class="plan-card__name"><?= e($plan['name']) ?></h2>
            <p class="plan-card__price">
              <span class="plan-card__price-value"><?= number_format((float)$plan['price'], 0, '.', ' ') ?> ₽</span>
              <span class="plan-card__price-period">/ <?= (int)$plan['duration_days'] ?> дней</span>
            </p>

            <ul class="plan-card__features">
              <li><?= e($plan['quality']) ?>-качество</li>
              <li><?= (int)$plan['screens'] ?> экран(ов) одновременно</li>
              <?php foreach ($features as $feature): ?>
                <li><?= e($feature) ?></li>
              <?php endforeach; ?>
            </ul>

            <?php if ($isCurrent): ?>
              <p class="plan-card__current">✓ Ваш текущий тариф</p>
              <a href="checkout.php?plan_id=<?= (int)$plan['id'] ?>" class="btn btn--accent btn--block">Продлить</a>
            <?php else: ?>
              <a href="checkout.php?plan_id=<?= (int)$plan['id'] ?>" class="btn btn--accent btn--block">
                <?= $subscription ? 'Сменить тариф' : 'Выбрать тариф' ?>
              </a>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <p class="section-subtitle mt-4">
      Оплата эмулируется — реальные транзакции не выполняются. Данные карты не сохраняются.
    </p>
  </section>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>