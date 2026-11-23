<?php
// Эмуляция оплаты подписки. Данные карты не сохраняются в БД.
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
$userId = (int)$currentUser['id'];

$planId = (int)($_GET['plan_id'] ?? $_POST['plan_id'] ?? 0);
$plan   = $planId > 0 ? getPlanById($conn, $planId) : null;

if ($plan === null || (int)$plan['is_active'] !== 1) {
    http_response_code(404);
    $pageTitle       = 'Тариф не найден — Illuminos';
    $pageDescription = '';
    $activePage      = 'subscription';
    require __DIR__ . '/includes/header.php';
    ?>
    <main><div class="container section">
      <h1 class="section-title">404 — тариф не найден</h1>
      <p><a href="subscription.php" class="btn btn--accent">К тарифам</a></p>
    </div></main>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$errors = [];
$cardHolder = $currentUser['name'];
$cardNumber = '';
$cardExpiry = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify((string)($_POST['csrf_token'] ?? ''))) {
        $errors['csrf'] = 'Неверный токен формы. Обновите страницу.';
    }

    $cardHolder = trim((string)($_POST['card_holder'] ?? ''));
    $cardNumber = preg_replace('/\s+/', '', (string)($_POST['card_number'] ?? ''));
    $cardExpiry = trim((string)($_POST['card_expiry'] ?? ''));
    $cardCvv    = (string)($_POST['card_cvv'] ?? '');

    if (mb_strlen($cardHolder, 'UTF-8') < 2 || mb_strlen($cardHolder, 'UTF-8') > 100) {
        $errors['card_holder'] = 'Укажите имя владельца карты (2–100 символов).';
    }
    if (!preg_match('/^\d{16}$/', $cardNumber)) {
        $errors['card_number'] = 'Номер карты — 16 цифр.';
    }
    if (!preg_match('#^(\d{2})/(\d{2})$#', $cardExpiry, $m)) {
        $errors['card_expiry'] = 'Срок действия в формате ММ/ГГ.';
    } else {
        $month = (int)$m[1];
        $year  = 2000 + (int)$m[2];
        $expiryEnd = mktime(23, 59, 59, $month, (int)date('t', mktime(0, 0, 0, $month, 1, $year)), $year);
        if ($month < 1 || $month > 12) {
            $errors['card_expiry'] = 'Месяц должен быть от 01 до 12.';
        } elseif ($expiryEnd === false || $expiryEnd < time()) {
            $errors['card_expiry'] = 'Срок действия карты истёк.';
        }
    }
    if (!preg_match('/^\d{3}$/', $cardCvv)) {
        $errors['card_cvv'] = 'CVV — 3 цифры.';
    }

    if (!$errors) {
        $active = getActiveSubscription($conn, $userId);

        if ($active !== null && (int)$active['plan_id'] === (int)$plan['id']) {
            extendSubscription($conn, (int)$active['id'], (int)$plan['duration_days']);
            $message = 'Подписка «' . $plan['name'] . '» продлена на ' . (int)$plan['duration_days'] . ' дней.';
        } else {
            if ($active !== null) cancelActiveSubscriptions($conn, $userId);
            createSubscription($conn, $userId, (int)$plan['id'], (int)$plan['duration_days']);
            $message = 'Тариф «' . $plan['name'] . '» оформлен на ' . (int)$plan['duration_days'] . ' дней.';
        }

        promoteToSubscriber($conn, $userId);

        flash_set('success', $message);
        header('Location: cabinet.php');
        exit;
    }
}

$pageTitle       = 'Оплата — Illuminos';
$pageDescription = 'Оформление подписки ' . $plan['name'];
$activePage      = 'subscription';

require __DIR__ . '/includes/header.php';
?>
<main>

  <div class="container breadcrumbs" aria-label="Навигационная цепочка">
    <a href="index.php">Главная</a><span>/</span>
    <a href="subscription.php">Подписка</a><span>/</span>
    <span aria-current="page">Оплата</span>
  </div>

  <section class="section container">
    <h1 class="section-title">Оформление подписки</h1>

    <div class="checkout-layout">

      <form method="post" action="checkout.php" class="checkout-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="plan_id" value="<?= (int)$plan['id'] ?>">

        <?php if (!empty($errors['csrf'])): ?>
          <div class="flash flash--error"><?= e($errors['csrf']) ?></div>
        <?php endif; ?>

        <h2 class="checkout-form__title">Данные карты</h2>
        <p class="checkout-form__note">
          Это эмуляция оплаты — введите любые 16 цифр, срок в будущем и любые 3 цифры CVV.
        </p>

        <div class="form-group">
          <label class="form-label" for="co-holder">Владелец карты</label>
          <input class="form-input <?= isset($errors['card_holder']) ? 'form-input--error' : '' ?>"
                 id="co-holder" type="text" name="card_holder"
                 value="<?= e($cardHolder) ?>" required maxlength="100"
                 autocomplete="cc-name">
          <?php if (isset($errors['card_holder'])): ?>
            <p class="form-error"><?= e($errors['card_holder']) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label" for="co-number">Номер карты</label>
          <input class="form-input <?= isset($errors['card_number']) ? 'form-input--error' : '' ?>"
                 id="co-number" type="text" name="card_number"
                 value="<?= e($cardNumber) ?>"
                 placeholder="0000 0000 0000 0000"
                 required inputmode="numeric" maxlength="19"
                 autocomplete="cc-number">
          <?php if (isset($errors['card_number'])): ?>
            <p class="form-error"><?= e($errors['card_number']) ?></p>
          <?php endif; ?>
        </div>

        <div class="checkout-form__row">
          <div class="form-group">
            <label class="form-label" for="co-expiry">Срок действия</label>
            <input class="form-input <?= isset($errors['card_expiry']) ? 'form-input--error' : '' ?>"
                   id="co-expiry" type="text" name="card_expiry"
                   value="<?= e($cardExpiry) ?>"
                   placeholder="ММ/ГГ"
                   required maxlength="5"
                   autocomplete="cc-exp">
            <?php if (isset($errors['card_expiry'])): ?>
              <p class="form-error"><?= e($errors['card_expiry']) ?></p>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <label class="form-label" for="co-cvv">CVV</label>
            <input class="form-input <?= isset($errors['card_cvv']) ? 'form-input--error' : '' ?>"
                   id="co-cvv" type="password" name="card_cvv"
                   placeholder="123"
                   required inputmode="numeric" maxlength="3"
                   autocomplete="cc-csc">
            <?php if (isset($errors['card_cvv'])): ?>
              <p class="form-error"><?= e($errors['card_cvv']) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <button type="submit" class="btn btn--accent btn--block">
          Оплатить <?= number_format((float)$plan['price'], 0, '.', ' ') ?> ₽
        </button>

        <p class="checkout-form__note">
          Нажимая «Оплатить», вы соглашаетесь с условиями сервиса. Данные не передаются реальной платёжной системе.
        </p>
      </form>

      <aside class="checkout-summary">
        <h2 class="checkout-summary__title">Ваш заказ</h2>

        <div class="checkout-summary__plan">
          <p class="checkout-summary__plan-name"><?= e($plan['name']) ?></p>
          <p class="checkout-summary__plan-meta">
            <?= e($plan['quality']) ?> · <?= (int)$plan['screens'] ?> экран(ов)
          </p>
        </div>

        <ul class="checkout-summary__features">
          <?php
            $features = array_filter(array_map('trim', explode(',', (string)$plan['features'])));
            foreach ($features as $feature):
          ?>
            <li><?= e($feature) ?></li>
          <?php endforeach; ?>
        </ul>

        <div class="checkout-summary__total">
          <span>Итого</span>
          <strong><?= number_format((float)$plan['price'], 0, '.', ' ') ?> ₽</strong>
        </div>

        <p class="checkout-summary__duration">
          Срок действия: <?= (int)$plan['duration_days'] ?> дней
        </p>

        <a href="subscription.php" class="btn btn--ghost btn--block">← Другой тариф</a>
      </aside>

    </div>
  </section>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>