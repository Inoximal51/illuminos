-- ====================================================================
-- Illuminos — installation SQL script
-- Импорт через phpMyAdmin (OpenServer localhost, пользователь root).
-- Создаёт БД `illuminos`, таблицы и демо-данные.
--
-- Демо-пользователи (пароль у всех: "password"):
--   admin@illuminos.local       — роль admin
--   subscriber@illuminos.local  — роль subscriber
--   user@illuminos.local        — роль user
-- Сменить пароли можно через форму регистрации на этапе 5.
-- ====================================================================

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `illuminos`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `illuminos`;

-- Отключаем FK-проверки, чтобы DROP прошёл в любом порядке (идемпотентность).
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `view_history`;
DROP TABLE IF EXISTS `favorites`;
DROP TABLE IF EXISTS `subscriptions`;
DROP TABLE IF EXISTS `plans`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `series_cast`;
DROP TABLE IF EXISTS `movie_cast`;
DROP TABLE IF EXISTS `episodes`;
DROP TABLE IF EXISTS `seasons`;
DROP TABLE IF EXISTS `series`;
DROP TABLE IF EXISTS `movies`;
DROP TABLE IF EXISTS `categories`;

SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- 1. categories
-- ====================================================================
CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`name`, `slug`) VALUES
  ('Драмы', 'drama'),
  ('Комедии', 'comedy'),
  ('Ужасы', 'horror'),
  ('Фантастика', 'sci-fi'),
  ('Боевики', 'action'),
  ('Триллеры', 'thriller'),
  ('Детективы', 'detective'),
  ('Приключения', 'adventure'),
  ('Мелодрамы', 'romance'),
  ('Документальные', 'documentary');

-- ====================================================================
-- 2. movies
-- ====================================================================
CREATE TABLE `movies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `year` SMALLINT UNSIGNED DEFAULT NULL,
  `duration_min` SMALLINT UNSIGNED DEFAULT NULL,
  `age_rating` VARCHAR(10) DEFAULT NULL,
  `rating` DECIMAL(3,1) DEFAULT NULL,
  `description` TEXT,
  `poster_url` VARCHAR(500) DEFAULT NULL,
  `backdrop_url` VARCHAR(500) DEFAULT NULL,
  `trailer_url` VARCHAR(500) DEFAULT NULL,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `is_new` TINYINT(1) NOT NULL DEFAULT 0,
  `is_popular` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_movies_slug` (`slug`),
  KEY `idx_movies_category` (`category_id`),
  CONSTRAINT `fk_movies_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `movies`
(`title`,`slug`,`year`,`duration_min`,`age_rating`,`rating`,`description`,`poster_url`,`backdrop_url`,`trailer_url`,`category_id`,`is_new`,`is_popular`) VALUES

('Дюна: Часть вторая','dune-2',2024,166,'18+',8.7,
 'Пол Атрейдес объединяется с Чани и фрименами, чтобы отомстить заговорщикам, уничтожившим его семью. Эпическое продолжение саги о пустынной планете Арракис.',
 'https://picsum.photos/seed/dune/600/900',
 'https://picsum.photos/seed/dune-hero/1600/900',
 'https://www.youtube.com/embed/Way9Dexny3w',4,1,1),

('Оппенгеймер','oppenheimer',2023,180,'18+',8.2,
 'История американского физика Роберта Оппенгеймера, руководителя Манхэттенского проекта и создателя атомной бомбы.',
 'https://picsum.photos/seed/oppenheimer/600/900',
 'https://picsum.photos/seed/oppenheimer-hero/1600/900',
 'https://www.youtube.com/embed/uYPbbksJxIg',1,1,0),

('Бедные-несчастные','poor-things',2023,141,'18+',7.9,
 'Белла Бакстер, оживлённая безумным учёным, отправляется в путешествие по миру в поисках свободы и знаний.',
 'https://picsum.photos/seed/poor-things/600/900',
 'https://picsum.photos/seed/poor-things-hero/1600/900',
 'https://www.youtube.com/embed/RlbR5N6veqw',1,1,0),

('Анатомия падения','anatomy-of-a-fall',2023,151,'18+',8.5,
 'Писательница пытается доказать свою невиновность в смерти мужа, в то время как её сын — единственный свидетель.',
 'https://picsum.photos/seed/anatomy/600/900',
 'https://picsum.photos/seed/anatomy-hero/1600/900',
 'https://www.youtube.com/embed/FUXawkH-ONM',1,1,0),

('Барби','barbie',2023,114,'12+',7.4,
 'Барби и Кен отправляются из Барбиленда в реальный мир, где их ждут неожиданные приключения и открытия.',
 'https://picsum.photos/seed/barbie/600/900',
 'https://picsum.photos/seed/barbie-hero/1600/900',
 'https://www.youtube.com/embed/pBk4NYhWNMM',2,1,0),

('Зона интересов','zone-of-interest',2023,105,'16+',7.8,
 'Жизнь семьи коменданта лагеря Освенцим — обыденность рядом с ужасом.',
 'https://picsum.photos/seed/zone/600/900',
 'https://picsum.photos/seed/zone-hero/1600/900',
 'https://www.youtube.com/embed/rvXc4W0o6BA',1,1,0),

('Интерстеллар','interstellar',2014,169,'12+',9.0,
 'Группа исследователей отправляется в космос, чтобы найти новый дом для человечества.',
 'https://picsum.photos/seed/interstellar/600/900',
 'https://picsum.photos/seed/interstellar-hero/1600/900',
 'https://www.youtube.com/embed/zSWdZVtXT7E',4,0,1),

('Начало','inception',2010,148,'12+',8.8,
 'Вор, проникающий в сны, получает задание внедрить идею в сознание человека.',
 'https://picsum.photos/seed/inception/600/900',
 'https://picsum.photos/seed/inception-hero/1600/900',
 'https://www.youtube.com/embed/YoHD9XEInc0',4,0,1),

('Тёмный рыцарь','dark-knight',2008,152,'16+',8.6,
 'Бэтмен противостоит Джокеру, который стремится погрузить Готэм в хаос.',
 'https://picsum.photos/seed/dark-knight/600/900',
 'https://picsum.photos/seed/dark-knight-hero/1600/900',
 'https://www.youtube.com/embed/EXeTwQWrcwY',5,0,1),

('Паразиты','parasite',2019,132,'18+',8.4,
 'Семья бедняков хитростью проникает в дом богатой семьи, что приводит к неожиданным последствиям.',
 'https://picsum.photos/seed/parasite/600/900',
 'https://picsum.photos/seed/parasite-hero/1600/900',
 'https://www.youtube.com/embed/5xH0HfJHsaY',6,0,1);

-- ====================================================================
-- 3. series
-- ====================================================================
CREATE TABLE `series` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `year_start` SMALLINT UNSIGNED DEFAULT NULL,
  `year_end` SMALLINT UNSIGNED DEFAULT NULL,
  `age_rating` VARCHAR(10) DEFAULT NULL,
  `rating` DECIMAL(3,1) DEFAULT NULL,
  `description` TEXT,
  `poster_url` VARCHAR(500) DEFAULT NULL,
  `backdrop_url` VARCHAR(500) DEFAULT NULL,
  `trailer_url` VARCHAR(500) DEFAULT NULL,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `is_new` TINYINT(1) NOT NULL DEFAULT 0,
  `is_popular` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_series_slug` (`slug`),
  KEY `idx_series_category` (`category_id`),
  CONSTRAINT `fk_series_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `series`
(`title`,`slug`,`year_start`,`year_end`,`age_rating`,`rating`,`description`,`poster_url`,`backdrop_url`,`trailer_url`,`category_id`,`is_new`,`is_popular`) VALUES

('Дюна: Пророчество','dune-prophecy',2024,NULL,'16+',7.4,
 'За 10 000 лет до Пола Атрейдеса сестричество Бене Гессерит прокладывает путь через интриги Империи, чтобы обрести власть над судьбами человечества.',
 'https://picsum.photos/seed/dune-prophecy/600/900',
 'https://picsum.photos/seed/dune-prophecy-hero/1600/900',
 'https://www.youtube.com/embed/qVnWpw6s0_c',4,1,0),

('Игра престолов','game-of-thrones',2011,2019,'18+',9.2,
 'Девять знатных домов Вестероса сражаются за Железный трон, в то время как на севере пробуждается древнее зло.',
 'https://picsum.photos/seed/got/600/900',
 'https://picsum.photos/seed/got-hero/1600/900',
 'https://www.youtube.com/embed/KPLWWIOCOOQ',1,0,1),

('Основание','foundation',2021,NULL,'16+',8.5,
 'По мотивам цикла Айзека Азимова: математик предсказывает падение Галактической Империи и пытается сохранить знания человечества.',
 'https://picsum.photos/seed/foundation/600/900',
 'https://picsum.photos/seed/foundation-hero/1600/900',
 'https://www.youtube.com/embed/X4QYV5t5Owo',4,0,1);

-- ====================================================================
-- 4. seasons
-- ====================================================================
CREATE TABLE `seasons` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `series_id` INT UNSIGNED NOT NULL,
  `number` TINYINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_seasons_series` (`series_id`),
  CONSTRAINT `fk_seasons_series` FOREIGN KEY (`series_id`) REFERENCES `series`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `seasons` (`series_id`,`number`,`title`) VALUES
  (1, 1, 'Сезон 1'),
  (2, 1, 'Сезон 1'),
  (3, 1, 'Сезон 1'),
  (3, 2, 'Сезон 2');

-- ====================================================================
-- 5. episodes
-- ====================================================================
CREATE TABLE `episodes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `season_id` INT UNSIGNED NOT NULL,
  `number` SMALLINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `duration_min` SMALLINT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_episodes_season` (`season_id`),
  CONSTRAINT `fk_episodes_season` FOREIGN KEY (`season_id`) REFERENCES `seasons`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Season 1 = Дюна: Пророчество S1 (series_id=1)
INSERT INTO `episodes` (`season_id`,`number`,`title`,`duration_min`) VALUES
  (1, 1, 'Голоса сестёр', 62),
  (1, 2, 'Две истины', 58),
  (1, 3, 'Сестра и враг', 55),
  (1, 4, 'Пророчество', 60),
  (1, 5, 'В тени', 57),
  (1, 6, 'Начало пути', 65);

-- Season 2 = Игра престолов S1 (series_id=2)
INSERT INTO `episodes` (`season_id`,`number`,`title`,`duration_min`) VALUES
  (2, 1, 'Зима близко', 62),
  (2, 2, 'Королевский тракт', 56),
  (2, 3, 'Лорд Сноу', 58),
  (2, 4, 'Калеки, бастарды и сломанные вещи', 56);

-- Season 3 = Основание S1 (series_id=3)
INSERT INTO `episodes` (`season_id`,`number`,`title`,`duration_min`) VALUES
  (3, 1, 'Император', 69),
  (3, 2, 'Подготовка', 62),
  (3, 3, 'Математик', 58);

-- Season 4 = Основание S2 (series_id=3)
INSERT INTO `episodes` (`season_id`,`number`,`title`,`duration_min`) VALUES
  (4, 1, 'В тени', 60),
  (4, 2, 'Новый порядок', 55);

-- ====================================================================
-- 6. movie_cast
-- ====================================================================
CREATE TABLE `movie_cast` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `movie_id` INT UNSIGNED NOT NULL,
  `person_name` VARCHAR(255) NOT NULL,
  `role` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_movie_cast_movie` (`movie_id`),
  CONSTRAINT `fk_movie_cast_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `movie_cast` (`movie_id`,`person_name`,`role`) VALUES
  -- 1: Дюна: Часть вторая
  (1, 'Тимоти Шаламе', 'Пол Атрейдес'),
  (1, 'Зендея', 'Чани'),
  (1, 'Ребекка Фергюсон', 'Леди Джессика'),
  (1, 'Дени Вильнёв', 'Режиссёр'),
  -- 2: Оппенгеймер
  (2, 'Киллиан Мёрфи', 'Роберт Оппенгеймер'),
  (2, 'Эмили Блант', 'Кэтрин Оппенгеймер'),
  (2, 'Роберт Дауни-мл.', 'Льюис Стросс'),
  (2, 'Кристофер Нолан', 'Режиссёр'),
  -- 3: Бедные-несчастные
  (3, 'Эмма Стоун', 'Белла Бакстер'),
  (3, 'Марк Руффало', 'Дункан Веддербёрн'),
  (3, 'Уиллем Дефо', 'Годвин Бакстер'),
  (3, 'Йоргос Лантимос', 'Режиссёр'),
  -- 4: Анатомия падения
  (4, 'Сандра Хюллер', 'Сандра Войтер'),
  (4, 'Сванн Арло', 'Сэмюэль Малески'),
  (4, 'Мило Машадо-Гранер', 'Даниэль'),
  (4, 'Жюстин Трие', 'Режиссёр'),
  -- 5: Барби
  (5, 'Марго Робби', 'Барби'),
  (5, 'Райан Гослинг', 'Кен'),
  (5, 'Америка Феррера', 'Глория'),
  (5, 'Грета Гервиг', 'Режиссёр'),
  -- 6: Зона интересов
  (6, 'Кристиан Фридель', 'Рудольф Хёсс'),
  (6, 'Сандра Хюллер', 'Хедвиг Хёсс'),
  (6, 'Джонатан Глейзер', 'Режиссёр'),
  -- 7: Интерстеллар
  (7, 'Мэттью МакКонахи', 'Купер'),
  (7, 'Энн Хэтэуэй', 'Амелия Брэнд'),
  (7, 'Джессика Честейн', 'Мёрф'),
  (7, 'Кристофер Нолан', 'Режиссёр'),
  -- 8: Начало
  (8, 'Леонардо ДиКаприо', 'Дом Кобб'),
  (8, 'Джозеф Гордон-Левитт', 'Артур'),
  (8, 'Эллиот Пейдж', 'Ариадна'),
  (8, 'Кристофер Нолан', 'Режиссёр'),
  -- 9: Тёмный рыцарь
  (9, 'Кристиан Бэйл', 'Брюс Уэйн'),
  (9, 'Хит Леджер', 'Джокер'),
  (9, 'Аарон Экхарт', 'Харви Дент'),
  (9, 'Кристофер Нолан', 'Режиссёр'),
  -- 10: Паразиты
  (10, 'Сон Кан Хо', 'Ким Ки Тэк'),
  (10, 'Чхве У Шик', 'Ким Ки У'),
  (10, 'Пак Со Дам', 'Ким Ки Джон'),
  (10, 'Пон Джун Хо', 'Режиссёр');

-- ====================================================================
-- 7. series_cast
-- ====================================================================
CREATE TABLE `series_cast` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `series_id` INT UNSIGNED NOT NULL,
  `person_name` VARCHAR(255) NOT NULL,
  `role` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_series_cast_series` (`series_id`),
  CONSTRAINT `fk_series_cast_series` FOREIGN KEY (`series_id`) REFERENCES `series`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `series_cast` (`series_id`,`person_name`,`role`) VALUES
  -- 1: Дюна: Пророчество
  (1, 'Эмили Уотсон', 'Валя Харконнен'),
  (1, 'Оливия Уильямс', 'Тула Харконнен'),
  (1, 'Трэвис Фиммел', 'Десмонд Харт'),
  (1, 'Анна Фёрстер', 'Режиссёр'),
  -- 2: Игра престолов
  (2, 'Питер Динклэйдж', 'Тирион Ланнистер'),
  (2, 'Эмилия Кларк', 'Дейенерис Таргариен'),
  (2, 'Кит Харингтон', 'Джон Сноу'),
  (2, 'Дэвид Бениофф', 'Режиссёр'),
  -- 3: Основание
  (3, 'Джаред Харрис', 'Гэри Сэлдон'),
  (3, 'Ли Пейс', 'Братья День'),
  (3, 'Лу Лобель', 'Братья Рассвет'),
  (3, 'Дэвид С. Гойер', 'Режиссёр');

-- ====================================================================
-- 8. users (для этапа 5 — регистрация/авторизация)
-- ====================================================================
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('user','subscriber','admin') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Пароль у всех трёх демо-аккаунтов: "password"
INSERT INTO `users` (`name`,`email`,`password_hash`,`role`) VALUES
  ('Администратор', 'admin@illuminos.local',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
  ('Подписчик', 'subscriber@illuminos.local',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'subscriber'),
  ('Пользователь', 'user@illuminos.local',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- ====================================================================
-- 9. plans (тарифы подписки для этапа 6)
-- ====================================================================
CREATE TABLE `plans` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `duration_days` SMALLINT UNSIGNED NOT NULL DEFAULT 30,
  `screens` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `quality` VARCHAR(20) NOT NULL DEFAULT 'HD',
  `features` TEXT,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `plans` (`name`,`price`,`duration_days`,`screens`,`quality`,`features`) VALUES
  ('Базовый',   299.00, 30, 1, 'HD',      'Один экран, HD-качество, без рекламы'),
  ('Стандарт',  499.00, 30, 2, 'Full HD', 'Два экрана, Full HD, скачивание контента'),
  ('Премиум',   799.00, 30, 4, '4K',      'Четыре экрана, 4K, без рекламы, эксклюзивный контент');

-- ====================================================================
-- 10. subscriptions (активные подписки; пока пусто — заполняется при оплате)
-- ====================================================================
CREATE TABLE `subscriptions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `plan_id` INT UNSIGNED NOT NULL,
  `status` ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active',
  `started_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_subscriptions_user` (`user_id`),
  KEY `idx_subscriptions_plan` (`plan_id`),
  CONSTRAINT `fk_subscriptions_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_subscriptions_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 11. favorites (избранное; заполняется на этапе 5)
-- ====================================================================
CREATE TABLE `favorites` (
  `user_id` INT UNSIGNED NOT NULL,
  `movie_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `movie_id`),
  KEY `idx_favorites_movie` (`movie_id`),
  CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_favorites_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- 12. view_history (история просмотров; заполняется на этапе 5)
-- ====================================================================
CREATE TABLE `view_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `movie_id` INT UNSIGNED DEFAULT NULL,
  `series_id` INT UNSIGNED DEFAULT NULL,
  `watched_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_history_user` (`user_id`),
  KEY `idx_history_movie` (`movie_id`),
  KEY `idx_history_series` (`series_id`),
  CONSTRAINT `fk_history_user`   FOREIGN KEY (`user_id`)   REFERENCES `users`(`id`)   ON DELETE CASCADE,
  CONSTRAINT `fk_history_movie`  FOREIGN KEY (`movie_id`)  REFERENCES `movies`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `fk_history_series` FOREIGN KEY (`series_id`) REFERENCES `series`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;