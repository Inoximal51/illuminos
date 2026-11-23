<?php
// <head>, шапка, мобильное меню, флеш-сообщения.
// Вход: $pageTitle, $pageDescription, $activePage, $currentUser (опц.)
if (!isset($currentUser)) {
    $currentUser = null;
    if (!empty($_SESSION['user_id']) && isset($conn) && $conn instanceof mysqli) {
        $stmt = $conn->prepare('SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        $currentUser = $res ? $res->fetch_assoc() : null;
        $stmt->close();
    }
}
$headerQuery = isset($_GET['q']) ? (string)$_GET['q'] : '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($pageDescription ?? '') ?>">
  <title><?= e($pageTitle ?? 'Illuminos') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/palette.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
  <div class="container site-header__inner">
    <a href="index.php" class="logo">Illumi<span>nos</span></a>

    <nav class="main-nav" aria-label="Основная навигация">
      <a href="index.php" class="main-nav__link <?= ($activePage ?? '') === 'home'   ? 'is-active' : '' ?>">Главная</a>
      <a href="movies.php" class="main-nav__link <?= ($activePage ?? '') === 'movies' ? 'is-active' : '' ?>">Фильмы</a>
      <a href="series.php" class="main-nav__link <?= ($activePage ?? '') === 'series' ? 'is-active' : '' ?>">Сериалы</a>
      <?php if (!empty($currentUser)): ?>
        <a href="cabinet.php" class="main-nav__link <?= ($activePage ?? '') === 'cabinet' ? 'is-active' : '' ?>">Кабинет</a>
      <?php endif; ?>
      <a href="subscription.php" class="main-nav__link <?= ($activePage ?? '') === 'subscription' ? 'is-active' : '' ?>">Подписка</a>
    </nav>

    <form class="header-search" role="search" aria-label="Поиск"
          action="search.php" method="get">
      <label for="header-search" class="visually-hidden">Поиск</label>
      <input id="header-search" type="search" name="q"
             class="header-search__input"
             value="<?= e($headerQuery) ?>"
             placeholder="Поиск фильмов и сериалов…"
             autocomplete="off">
      <button type="submit" class="header-search__btn" aria-label="Искать">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
        </svg>
      </button>
    </form>

    <?php if (!empty($currentUser)): ?>
      <a href="cabinet.php" class="user-badge" title="Личный кабинет">
        <span class="user-badge__avatar"><?= e(initials($currentUser['name'])) ?></span>
        <span class="user-badge__name"><?= e($currentUser['name']) ?></span>
      </a>
      <a href="logout.php" class="btn btn--ghost header-login">Выйти</a>
    <?php else: ?>
      <a href="login.php" class="btn btn--accent header-login">Войти</a>
    <?php endif; ?>

    <button class="burger" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#mobileMenu"
            aria-controls="mobileMenu" aria-label="Открыть меню">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
        <path d="M4 7h16M4 12h16M4 17h16"/>
      </svg>
    </button>
  </div>
</header>

<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="mobileMenuLabel">Меню</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
  </div>
  <div class="offcanvas-body">
    <nav class="main-nav" aria-label="Мобильная навигация">
      <a href="index.php" class="main-nav__link <?= ($activePage ?? '') === 'home'   ? 'is-active' : '' ?>">Главная</a>
      <a href="movies.php" class="main-nav__link <?= ($activePage ?? '') === 'movies' ? 'is-active' : '' ?>">Фильмы</a>
      <a href="series.php" class="main-nav__link <?= ($activePage ?? '') === 'series' ? 'is-active' : '' ?>">Сериалы</a>
      <?php if (!empty($currentUser)): ?>
        <a href="cabinet.php" class="main-nav__link <?= ($activePage ?? '') === 'cabinet' ? 'is-active' : '' ?>">Кабинет</a>
      <?php endif; ?>
      <a href="subscription.php" class="main-nav__link <?= ($activePage ?? '') === 'subscription' ? 'is-active' : '' ?>">Подписка</a>
    </nav>
    <form class="header-search mt-3" role="search" action="search.php" method="get">
      <input type="search" name="q" class="header-search__input"
             value="<?= e($headerQuery) ?>" placeholder="Поиск…" aria-label="Поиск">
    </form>
    <?php if (!empty($currentUser)): ?>
      <a href="cabinet.php" class="btn btn--accent btn--block mt-3">Личный кабинет</a>
      <a href="logout.php" class="btn btn--ghost btn--block mt-2">Выйти</a>
    <?php else: ?>
      <a href="login.php" class="btn btn--accent btn--block mt-3">Войти</a>
    <?php endif; ?>
  </div>
</div>

<?php
// Флеш-сообщения под шапкой
$flashSuccess = function_exists('flash_get') ? flash_get('success') : null;
$flashError   = function_exists('flash_get') ? flash_get('error')   : null;
if ($flashSuccess || $flashError):
?>
<div class="container">
  <div class="flash-area">
    <?php if ($flashSuccess): ?>
      <div class="flash flash--success"><?= e($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
      <div class="flash flash--error"><?= e($flashError) ?></div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>