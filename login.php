<?php
// Авторизация. Принимает ?redirect=... для возврата.
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: cabinet.php');
    exit;
}

$redirectRaw = (string)($_GET['redirect'] ?? $_POST['redirect'] ?? '');
$redirect    = safe_redirect_target($redirectRaw);

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify((string)($_POST['csrf_token'] ?? ''))) {
        $errors['csrf'] = 'Неверный токен формы. Обновите страницу.';
    }

    $email = trim((string)($_POST['email'] ?? ''));
    $pass  = (string)($_POST['password'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный email.';
    }
    if ($pass === '') {
        $errors['password'] = 'Введите пароль.';
    }

    if (!$errors) {
        $user = fetchOne($conn,
            'SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1',
            [$email], 's');

        if ($user === null || !password_verify($pass, $user['password_hash'])) {
            $errors['login'] = 'Неверный email или пароль.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            flash_set('success', 'С возвращением, ' . $user['name'] . '!');
            header('Location: ' . $redirect);
            exit;
        }
    }
}

$pageTitle       = 'Вход — Illuminos';
$pageDescription = 'Вход в аккаунт Illuminos.';
$activePage      = '';
$currentUser     = null;

require __DIR__ . '/includes/header.php';
?>
<main>
  <section class="auth-page container">
    <div class="auth-card">
      <h1 class="auth-card__title">Вход</h1>
      <p class="auth-card__subtitle">Войдите, чтобы продолжить просмотр и управлять подпиской.</p>

      <?php if (!empty($errors['csrf'])): ?>
        <div class="flash flash--error"><?= e($errors['csrf']) ?></div>
      <?php endif; ?>
      <?php if (!empty($errors['login'])): ?>
        <div class="flash flash--error"><?= e($errors['login']) ?></div>
      <?php endif; ?>

      <form method="post" action="login.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="redirect" value="<?= e($redirect) ?>">

        <div class="form-group">
          <label class="form-label" for="login-email">Email</label>
          <input class="form-input <?= isset($errors['email']) ? 'form-input--error' : '' ?>"
                 id="login-email" type="email" name="email"
                 value="<?= e($email) ?>" required autocomplete="email">
          <?php if (isset($errors['email'])): ?>
            <p class="form-error"><?= e($errors['email']) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label" for="login-pass">Пароль</label>
          <input class="form-input <?= isset($errors['password']) ? 'form-input--error' : '' ?>"
                 id="login-pass" type="password" name="password"
                 required autocomplete="current-password">
          <?php if (isset($errors['password'])): ?>
            <p class="form-error"><?= e($errors['password']) ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn--accent btn--block">Войти</button>
      </form>

      <div class="auth-card__footer">
        Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
      </div>

      <div class="demo-accounts">
        <p class="demo-accounts__title">Демо-аккаунты (пароль: <code>password</code>)</p>
        <ul class="demo-accounts__list">
          <li><code>admin@illuminos.local</code> — администратор</li>
          <li><code>subscriber@illuminos.local</code> — подписчик</li>
          <li><code>user@illuminos.local</code> — пользователь</li>
        </ul>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>