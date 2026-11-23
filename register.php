<?php
// Регистрация. Валидация всех полей + CSRF + password_hash.
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: cabinet.php');
    exit;
}

$errors = [];
$name   = '';
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify((string)($_POST['csrf_token'] ?? ''))) {
        $errors['csrf'] = 'Неверный токен формы. Обновите страницу.';
    }

    $name  = trim((string)($_POST['name']  ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $pass  = (string)($_POST['password'] ?? '');
    $pass2 = (string)($_POST['password_confirm'] ?? '');

    $len = mb_strlen($name, 'UTF-8');
    if ($len < 2) {
        $errors['name'] = 'Имя должно содержать не менее 2 символов.';
    } elseif ($len > 100) {
        $errors['name'] = 'Имя слишком длинное (максимум 100 символов).';
    }

    if ($email === '') {
        $errors['email'] = 'Введите email.';
    } elseif (mb_strlen($email, 'UTF-8') > 255) {
        $errors['email'] = 'Email слишком длинный.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный формат email.';
    } else {
        $existing = fetchOne($conn, 'SELECT id FROM users WHERE email = ? LIMIT 1', [$email], 's');
        if ($existing) $errors['email'] = 'Пользователь с таким email уже зарегистрирован.';
    }

    if (strlen($pass) < 8)      $errors['password'] = 'Пароль должен содержать не менее 8 символов.';
    if ($pass !== $pass2)       $errors['password_confirm'] = 'Пароли не совпадают.';

    if (!$errors) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, "user")'
        );
        $stmt->bind_param('sss', $name, $email, $hash);
        $stmt->execute();
        $newId = (int)$stmt->insert_id;
        $stmt->close();

        session_regenerate_id(true);
        $_SESSION['user_id'] = $newId;

        flash_set('success', 'Добро пожаловать в Illuminos, ' . $name . '!');
        header('Location: cabinet.php');
        exit;
    }
}

$pageTitle       = 'Регистрация — Illuminos';
$pageDescription = 'Создайте аккаунт Illuminos.';
$activePage      = '';
$currentUser     = null;

require __DIR__ . '/includes/header.php';
?>
<main>
  <section class="auth-page container">
    <div class="auth-card">
      <h1 class="auth-card__title">Регистрация</h1>
      <p class="auth-card__subtitle">Создайте аккаунт, чтобы сохранять избранное и получать персональные рекомендации.</p>

      <?php if (!empty($errors['csrf'])): ?>
        <div class="flash flash--error"><?= e($errors['csrf']) ?></div>
      <?php endif; ?>

      <form method="post" action="register.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="form-group">
          <label class="form-label" for="reg-name">Имя</label>
          <input class="form-input <?= isset($errors['name']) ? 'form-input--error' : '' ?>"
                 id="reg-name" type="text" name="name"
                 value="<?= e($name) ?>" required maxlength="100"
                 autocomplete="name">
          <?php if (isset($errors['name'])): ?>
            <p class="form-error"><?= e($errors['name']) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label" for="reg-email">Email</label>
          <input class="form-input <?= isset($errors['email']) ? 'form-input--error' : '' ?>"
                 id="reg-email" type="email" name="email"
                 value="<?= e($email) ?>" required maxlength="255"
                 autocomplete="email">
          <?php if (isset($errors['email'])): ?>
            <p class="form-error"><?= e($errors['email']) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label" for="reg-pass">Пароль</label>
          <input class="form-input <?= isset($errors['password']) ? 'form-input--error' : '' ?>"
                 id="reg-pass" type="password" name="password"
                 required minlength="8"
                 autocomplete="new-password">
          <p class="form-help">Минимум 8 символов.</p>
          <?php if (isset($errors['password'])): ?>
            <p class="form-error"><?= e($errors['password']) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label" for="reg-pass2">Повторите пароль</label>
          <input class="form-input <?= isset($errors['password_confirm']) ? 'form-input--error' : '' ?>"
                 id="reg-pass2" type="password" name="password_confirm"
                 required minlength="8"
                 autocomplete="new-password">
          <?php if (isset($errors['password_confirm'])): ?>
            <p class="form-error"><?= e($errors['password_confirm']) ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn--accent btn--block">Зарегистрироваться</button>
      </form>

      <div class="auth-card__footer">
        Уже есть аккаунт? <a href="login.php">Войти</a>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>