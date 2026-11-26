-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 26 2026 г., 14:18
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `illuminos`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Драмы', 'drama'),
(2, 'Комедии', 'comedy'),
(3, 'Ужасы', 'horror'),
(4, 'Фантастика', 'sci-fi'),
(5, 'Боевики', 'action'),
(6, 'Триллеры', 'thriller'),
(7, 'Детективы', 'detective'),
(8, 'Приключения', 'adventure'),
(9, 'Мелодрамы', 'romance'),
(10, 'Документальные', 'documentary');

-- --------------------------------------------------------

--
-- Структура таблицы `episodes`
--

CREATE TABLE `episodes` (
  `id` int UNSIGNED NOT NULL,
  `season_id` int UNSIGNED NOT NULL,
  `number` smallint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration_min` smallint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `episodes`
--

INSERT INTO `episodes` (`id`, `season_id`, `number`, `title`, `duration_min`) VALUES
(1, 1, 1, 'Голоса сестёр', 62),
(2, 1, 2, 'Две истины', 58),
(3, 1, 3, 'Сестра и враг', 55),
(4, 1, 4, 'Пророчество', 60),
(5, 1, 5, 'В тени', 57),
(6, 1, 6, 'Начало пути', 65),
(7, 2, 1, 'Зима близко', 62),
(8, 2, 2, 'Королевский тракт', 56),
(9, 2, 3, 'Лорд Сноу', 58),
(10, 2, 4, 'Калеки, бастарды и сломанные вещи', 56),
(11, 3, 1, 'Император', 69),
(12, 3, 2, 'Подготовка', 62),
(13, 3, 3, 'Математик', 58),
(14, 4, 1, 'В тени', 60),
(15, 4, 2, 'Новый порядок', 55);

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE `favorites` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `item_type` enum('movie','series') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `item_type`, `item_id`, `created_at`) VALUES
(3, 4, 'series', 2, '2026-11-22 14:52:00'),
(4, 4, 'movie', 1, '2026-11-22 23:49:46');

-- --------------------------------------------------------

--
-- Структура таблицы `movies`
--

CREATE TABLE `movies` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` smallint UNSIGNED DEFAULT NULL,
  `duration_min` smallint UNSIGNED DEFAULT NULL,
  `age_rating` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `poster_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `backdrop_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trailer_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `is_new` tinyint(1) NOT NULL DEFAULT '0',
  `is_popular` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `movies`
--

INSERT INTO `movies` (`id`, `title`, `slug`, `year`, `duration_min`, `age_rating`, `rating`, `description`, `poster_url`, `backdrop_url`, `trailer_url`, `category_id`, `is_new`, `is_popular`, `created_at`) VALUES
(1, 'Дюна: Часть вторая', 'dune-2', 2024, 166, '18+', '8.7', 'Пол Атрейдес объединяется с Чани и фрименами, чтобы отомстить заговорщикам, уничтожившим его семью. Эпическое продолжение саги о пустынной планете Арракис.', 'https://picsum.photos/seed/dune/600/900', 'https://picsum.photos/seed/dune-hero/1600/900', 'https://www.youtube.com/embed/Way9Dexny3w', 4, 1, 1, '2026-11-22 12:13:31'),
(2, 'Оппенгеймер', 'oppenheimer', 2023, 180, '18+', '8.2', 'История американского физика Роберта Оппенгеймера, руководителя Манхэттенского проекта и создателя атомной бомбы.', 'https://picsum.photos/seed/oppenheimer/600/900', 'https://picsum.photos/seed/oppenheimer-hero/1600/900', 'https://www.youtube.com/embed/uYPbbksJxIg', 1, 1, 0, '2026-11-22 12:13:31'),
(3, 'Бедные-несчастные', 'poor-things', 2023, 141, '18+', '7.9', 'Белла Бакстер, оживлённая безумным учёным, отправляется в путешествие по миру в поисках свободы и знаний.', 'https://picsum.photos/seed/poor-things/600/900', 'https://picsum.photos/seed/poor-things-hero/1600/900', 'https://www.youtube.com/embed/RlbR5N6veqw', 1, 1, 0, '2026-11-22 12:13:31'),
(4, 'Анатомия падения', 'anatomy-of-a-fall', 2023, 151, '18+', '8.5', 'Писательница пытается доказать свою невиновность в смерти мужа, в то время как её сын — единственный свидетель.', 'https://picsum.photos/seed/anatomy/600/900', 'https://picsum.photos/seed/anatomy-hero/1600/900', 'https://www.youtube.com/embed/FUXawkH-ONM', 1, 1, 0, '2026-11-22 12:13:31'),
(5, 'Барби', 'barbie', 2023, 114, '12+', '7.4', 'Барби и Кен отправляются из Барбиленда в реальный мир, где их ждут неожиданные приключения и открытия.', 'https://picsum.photos/seed/barbie/600/900', 'https://picsum.photos/seed/barbie-hero/1600/900', 'https://www.youtube.com/embed/pBk4NYhWNMM', 2, 1, 0, '2026-11-22 12:13:31'),
(6, 'Зона интересов', 'zone-of-interest', 2023, 105, '16+', '7.8', 'Жизнь семьи коменданта лагеря Освенцим — обыденность рядом с ужасом.', 'https://picsum.photos/seed/zone/600/900', 'https://picsum.photos/seed/zone-hero/1600/900', 'https://www.youtube.com/embed/rvXc4W0o6BA', 1, 1, 0, '2026-11-22 12:13:31'),
(7, 'Интерстеллар', 'interstellar', 2014, 169, '12+', '9.0', 'Группа исследователей отправляется в космос, чтобы найти новый дом для человечества.', 'https://picsum.photos/seed/interstellar/600/900', 'https://picsum.photos/seed/interstellar-hero/1600/900', 'https://www.youtube.com/embed/zSWdZVtXT7E', 4, 0, 1, '2026-11-22 12:13:31'),
(8, 'Начало', 'inception', 2010, 148, '12+', '8.8', 'Вор, проникающий в сны, получает задание внедрить идею в сознание человека.', 'https://picsum.photos/seed/inception/600/900', 'https://picsum.photos/seed/inception-hero/1600/900', 'https://www.youtube.com/embed/YoHD9XEInc0', 4, 0, 1, '2026-11-22 12:13:31'),
(9, 'Тёмный рыцарь', 'dark-knight', 2008, 152, '16+', '8.6', 'Бэтмен противостоит Джокеру, который стремится погрузить Готэм в хаос.', 'https://picsum.photos/seed/dark-knight/600/900', 'https://picsum.photos/seed/dark-knight-hero/1600/900', 'https://www.youtube.com/embed/EXeTwQWrcwY', 5, 0, 1, '2026-11-22 12:13:31'),
(10, 'Паразиты', 'parasite', 2019, 132, '18+', '8.4', 'Семья бедняков хитростью проникает в дом богатой семьи, что приводит к неожиданным последствиям.', 'https://picsum.photos/seed/parasite/600/900', 'https://picsum.photos/seed/parasite-hero/1600/900', 'https://www.youtube.com/embed/5xH0HfJHsaY', 6, 0, 1, '2026-11-22 12:13:31'),
(11, 'Титаник', 'titanic', 1997, 194, '12+', '7.9', 'Молодые влюблённые из разных сословий встречаются на борту «Титаника» и переживают катастрофу, которая изменит их судьбы навсегда.', 'https://picsum.photos/seed/titanic/600/900', 'https://picsum.photos/seed/titanic-hero/1600/900', 'https://www.youtube.com/embed/2e-eXJ6HgkQ', 9, 0, 1, '2026-11-22 15:00:37'),
(12, 'Матрица', 'matrix', 1999, 136, '16+', '8.7', 'Хакер Нео узнаёт, что реальность — симуляция, созданная машинами. Он присоединяется к сопротивлению, чтобы освободить человечество.', 'https://picsum.photos/seed/matrix/600/900', 'https://picsum.photos/seed/matrix-hero/1600/900', 'https://www.youtube.com/embed/vKQi3bBA1y8', 4, 0, 1, '2026-11-22 15:00:37'),
(13, 'Форрест Гамп', 'forrest-gump', 1994, 142, '12+', '8.8', 'История простодушного Форреста Гампа, который случайно оказывается участником ключевых событий XX века.', 'https://picsum.photos/seed/forrest-gump/600/900', 'https://picsum.photos/seed/forrest-gump-hero/1600/900', 'https://www.youtube.com/embed/bLvqoHBptjg', 1, 0, 1, '2026-11-22 15:00:37'),
(14, 'Побег из Шоушенка', 'shawshank', 1994, 142, '16+', '9.3', 'Банкир Энди Дюфрейн приговорён к пожизненному заключению за убийство, которого не совершал. История надежды и стойкости в стенах тюрьмы.', 'https://picsum.photos/seed/shawshank/600/900', 'https://picsum.photos/seed/shawshank-hero/1600/900', 'https://www.youtube.com/embed/6hB3S9bIaco', 1, 0, 1, '2026-11-22 15:00:37'),
(15, 'Крёстный отец', 'godfather', 1972, 175, '18+', '9.2', 'История семьи Корлеоне — одной из самых влиятельных мафиозных династий Нью-Йорка. Классика гангстерской саги.', 'https://picsum.photos/seed/godfather/600/900', 'https://picsum.photos/seed/godfather-hero/1600/900', 'https://www.youtube.com/embed/sY1S34973zA', 1, 0, 1, '2026-11-22 15:00:37'),
(16, 'Криминальное чтиво', 'pulp-fiction', 1994, 154, '18+', '8.9', 'Несколько пересекающихся историй о гангстерах, боксёре и наёмных убийцах в Лос-Анджелесе 90-х.', 'https://picsum.photos/seed/pulp-fiction/600/900', 'https://picsum.photos/seed/pulp-fiction-hero/1600/900', 'https://www.youtube.com/embed/s7EdQ4FqbhY', 6, 0, 1, '2026-11-22 15:00:37'),
(17, 'Бойцовский клуб', 'fight-club', 1999, 139, '18+', '8.8', 'Безымянный клерк знакомится с харизматичным Тайлером Дёрденом и вместе с ним основывает подпольный бойцовский клуб.', 'https://picsum.photos/seed/fight-club/600/900', 'https://picsum.photos/seed/fight-club-hero/1600/900', 'https://www.youtube.com/embed/qtRKdVHc-cE', 6, 0, 1, '2026-11-22 15:00:37'),
(18, 'Гладиатор', 'gladiator', 2000, 155, '16+', '8.5', 'Римский генерал Максимус предан императором и продан в рабство. Он становится гладиатором, чтобы отомстить.', 'https://picsum.photos/seed/gladiator/600/900', 'https://picsum.photos/seed/gladiator-hero/1600/900', 'https://www.youtube.com/embed/owK1qxDselE', 5, 0, 1, '2026-11-22 15:00:37'),
(19, 'Властелин колец: Братство Кольца', 'lotr-fellowship', 2001, 178, '12+', '8.8', 'Хоббит Фродо получает в наследство Кольцо Всевластья и вместе с Братством отправляется в опасный поход, чтобы уничтожить его.', 'https://picsum.photos/seed/lotr-1/600/900', 'https://picsum.photos/seed/lotr-1-hero/1600/900', 'https://www.youtube.com/embed/V75dMMIW2B4', 8, 0, 1, '2026-11-22 15:00:37'),
(20, 'Властелин колец: Две крепости', 'lotr-two-towers', 2002, 179, '12+', '8.8', 'Братство распадается. Фродо и Сэм продолжают путь в Мордор, а остальные сражаются у стен Хельмовой Пади.', 'https://picsum.photos/seed/lotr-2/600/900', 'https://picsum.photos/seed/lotr-2-hero/1600/900', 'https://www.youtube.com/embed/LbfMDwc4azU', 8, 0, 1, '2026-11-22 15:00:37'),
(21, 'Властелин колец: Возвращение короля', 'lotr-return-king', 2003, 201, '12+', '9.0', 'Финальная битва за Средиземье. Фродо приближается к Ородруину, а Арагорн предъявляет права на трон Гондора.', 'https://picsum.photos/seed/lotr-3/600/900', 'https://picsum.photos/seed/lotr-3-hero/1600/900', 'https://www.youtube.com/embed/r5X-hFf6Bwo', 8, 0, 1, '2026-11-22 15:00:37'),
(22, 'Мстители: Финал', 'avengers-endgame', 2019, 181, '12+', '8.4', 'Мстители собираются в последний раз, чтобы отменить Щелчок Таноса и восстановить баланс вселенной.', 'https://picsum.photos/seed/avengers-endgame/600/900', 'https://picsum.photos/seed/avengers-endgame-hero/1600/900', 'https://www.youtube.com/embed/TcMBFSGVi1c', 5, 1, 1, '2026-11-22 15:00:37'),
(23, 'Джон Уик', 'john-wick', 2014, 101, '18+', '7.4', 'Бывший киллер возвращается к ремеслу, чтобы отомстить за смерть собаки — последнего подарка покойной жены.', 'https://picsum.photos/seed/john-wick/600/900', 'https://picsum.photos/seed/john-wick-hero/1600/900', 'https://www.youtube.com/embed/C0BMx-qxsP4', 5, 0, 1, '2026-11-22 15:00:37'),
(24, 'Гарри Поттер и философский камень', 'harry-potter-1', 2001, 152, '6+', '7.6', 'Одиннадцатилетний Гарри Поттер узнаёт, что он волшебник, и поступает в школу магии Хогвартс.', 'https://picsum.photos/seed/harry-potter-1/600/900', 'https://picsum.photos/seed/harry-potter-1-hero/1600/900', 'https://www.youtube.com/embed/VyHV0BRtdxo', 8, 0, 1, '2026-11-22 15:00:37'),
(25, 'Оно', 'it-2017', 2017, 135, '18+', '7.3', 'Семеро детей из городка Дерри сталкиваются с древним злом, принимающим облик клоуна Пеннивайза.', 'https://picsum.photos/seed/it-2017/600/900', 'https://picsum.photos/seed/it-2017-hero/1600/900', 'https://www.youtube.com/embed/FnCdOQsX5kc', 3, 1, 0, '2026-11-22 15:00:37');

-- --------------------------------------------------------

--
-- Структура таблицы `movie_cast`
--

CREATE TABLE `movie_cast` (
  `id` int UNSIGNED NOT NULL,
  `movie_id` int UNSIGNED NOT NULL,
  `person_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `movie_cast`
--

INSERT INTO `movie_cast` (`id`, `movie_id`, `person_name`, `role`) VALUES
(1, 1, 'Тимоти Шаламе', 'Пол Атрейдес'),
(2, 1, 'Зендея', 'Чани'),
(3, 1, 'Ребекка Фергюсон', 'Леди Джессика'),
(4, 1, 'Дени Вильнёв', 'Режиссёр'),
(5, 2, 'Киллиан Мёрфи', 'Роберт Оппенгеймер'),
(6, 2, 'Эмили Блант', 'Кэтрин Оппенгеймер'),
(7, 2, 'Роберт Дауни-мл.', 'Льюис Стросс'),
(8, 2, 'Кристофер Нолан', 'Режиссёр'),
(9, 3, 'Эмма Стоун', 'Белла Бакстер'),
(10, 3, 'Марк Руффало', 'Дункан Веддербёрн'),
(11, 3, 'Уиллем Дефо', 'Годвин Бакстер'),
(12, 3, 'Йоргос Лантимос', 'Режиссёр'),
(13, 4, 'Сандра Хюллер', 'Сандра Войтер'),
(14, 4, 'Сванн Арло', 'Сэмюэль Малески'),
(15, 4, 'Мило Машадо-Гранер', 'Даниэль'),
(16, 4, 'Жюстин Трие', 'Режиссёр'),
(17, 5, 'Марго Робби', 'Барби'),
(18, 5, 'Райан Гослинг', 'Кен'),
(19, 5, 'Америка Феррера', 'Глория'),
(20, 5, 'Грета Гервиг', 'Режиссёр'),
(21, 6, 'Кристиан Фридель', 'Рудольф Хёсс'),
(22, 6, 'Сандра Хюллер', 'Хедвиг Хёсс'),
(23, 6, 'Джонатан Глейзер', 'Режиссёр'),
(24, 7, 'Мэттью МакКонахи', 'Купер'),
(25, 7, 'Энн Хэтэуэй', 'Амелия Брэнд'),
(26, 7, 'Джессика Честейн', 'Мёрф'),
(27, 7, 'Кристофер Нолан', 'Режиссёр'),
(28, 8, 'Леонардо ДиКаприо', 'Дом Кобб'),
(29, 8, 'Джозеф Гордон-Левитт', 'Артур'),
(30, 8, 'Эллиот Пейдж', 'Ариадна'),
(31, 8, 'Кристофер Нолан', 'Режиссёр'),
(32, 9, 'Кристиан Бэйл', 'Брюс Уэйн'),
(33, 9, 'Хит Леджер', 'Джокер'),
(34, 9, 'Аарон Экхарт', 'Харви Дент'),
(35, 9, 'Кристофер Нолан', 'Режиссёр'),
(36, 10, 'Сон Кан Хо', 'Ким Ки Тэк'),
(37, 10, 'Чхве У Шик', 'Ким Ки У'),
(38, 10, 'Пак Со Дам', 'Ким Ки Джон'),
(39, 10, 'Пон Джун Хо', 'Режиссёр'),
(40, 11, 'Леонардо ДиКаприо', 'Джек Доусон'),
(41, 11, 'Кейт Уинслет', 'Роза Дьюитт'),
(42, 11, 'Билли Зейн', 'Каледон Хокли'),
(43, 11, 'Джеймс Кэмерон', 'Режиссёр'),
(44, 12, 'Киану Ривз', 'Нео'),
(45, 12, 'Лоренс Фишбёрн', 'Морфеус'),
(46, 12, 'Кэрри-Энн Мосс', 'Тринити'),
(47, 12, 'Братья Вачовски', 'Режиссёры'),
(48, 13, 'Том Хэнкс', 'Форрест Гамп'),
(49, 13, 'Робин Райт', 'Дженни Каррен'),
(50, 13, 'Гэри Синиз', 'Лейтенант Дэн'),
(51, 13, 'Роберт Земекис', 'Режиссёр'),
(52, 14, 'Тим Роббинс', 'Энди Дюфрейн'),
(53, 14, 'Морган Фриман', 'Эллис «Ред» Реддинг'),
(54, 14, 'Боб Гантон', 'Начальник Нортон'),
(55, 14, 'Фрэнк Дарабонт', 'Режиссёр'),
(56, 15, 'Марлон Брандо', 'Дон Вито Корлеоне'),
(57, 15, 'Аль Пачино', 'Майкл Корлеоне'),
(58, 15, 'Джеймс Каан', 'Сонни Корлеоне'),
(59, 15, 'Фрэнсис Форд Коппола', 'Режиссёр'),
(60, 16, 'Джон Траволта', 'Винсент Вега'),
(61, 16, 'Сэмюэл Л. Джексон', 'Джулс Уиннфилд'),
(62, 16, 'Ума Турман', 'Мия Уоллес'),
(63, 16, 'Квентин Тарантино', 'Режиссёр'),
(64, 17, 'Брэд Питт', 'Тайлер Дёрден'),
(65, 17, 'Эдвард Нортон', 'Рассказчик'),
(66, 17, 'Хелена Бонэм Картер', 'Марла Сингер'),
(67, 17, 'Дэвид Финчер', 'Режиссёр'),
(68, 18, 'Рассел Кроу', 'Максимус'),
(69, 18, 'Хоакин Феникс', 'Коммод'),
(70, 18, 'Конни Нильсен', 'Луцилла'),
(71, 18, 'Ридли Скотт', 'Режиссёр'),
(72, 19, 'Элайджа Вуд', 'Фродо Бэггинс'),
(73, 19, 'Иэн Маккеллен', 'Гэндальф'),
(74, 19, 'Вигго Мортенсен', 'Арагорн'),
(75, 19, 'Питер Джексон', 'Режиссёр'),
(76, 20, 'Элайджа Вуд', 'Фродо Бэггинс'),
(77, 20, 'Энди Серкис', 'Голлум'),
(78, 20, 'Вигго Мортенсен', 'Арагорн'),
(79, 20, 'Питер Джексон', 'Режиссёр'),
(80, 21, 'Элайджа Вуд', 'Фродо Бэггинс'),
(81, 21, 'Вигго Мортенсен', 'Арагорн'),
(82, 21, 'Иэн Маккеллен', 'Гэндальф'),
(83, 21, 'Питер Джексон', 'Режиссёр'),
(84, 22, 'Роберт Дауни-мл.', 'Тони Старк'),
(85, 22, 'Крис Эванс', 'Стив Роджерс'),
(86, 22, 'Скарлетт Йоханссон', 'Наташа Романофф'),
(87, 22, 'Братья Руссо', 'Режиссёры'),
(88, 23, 'Киану Ривз', 'Джон Уик'),
(89, 23, 'Микаэл Никвист', 'Вигго Тарасов'),
(90, 23, 'Уиллем Дефо', 'Маркус'),
(91, 23, 'Чад Стахелски', 'Режиссёр'),
(92, 24, 'Дэниэл Рэдклифф', 'Гарри Поттер'),
(93, 24, 'Руперт Гринт', 'Рон Уизли'),
(94, 24, 'Эмма Уотсон', 'Гермиона Грейнджер'),
(95, 24, 'Крис Коламбус', 'Режиссёр'),
(96, 25, 'Билл Скарсгард', 'Пеннивайз'),
(97, 25, 'Джейден Либерер', 'Билл Денбро'),
(98, 25, 'Финн Вулфхард', 'Ричи Тозиер'),
(99, 25, 'Энди Мускетти', 'Режиссёр');

-- --------------------------------------------------------

--
-- Структура таблицы `plans`
--

CREATE TABLE `plans` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_days` smallint UNSIGNED NOT NULL DEFAULT '30',
  `screens` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `quality` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'HD',
  `features` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `plans`
--

INSERT INTO `plans` (`id`, `name`, `price`, `duration_days`, `screens`, `quality`, `features`, `is_active`) VALUES
(1, 'Базовый', '299.00', 30, 1, 'HD', 'Один экран, HD-качество, без рекламы', 1),
(2, 'Стандарт', '499.00', 30, 2, 'Full HD', 'Два экрана, Full HD, скачивание контента', 1),
(3, 'Премиум', '799.00', 30, 4, '4K', 'Четыре экрана, 4K, без рекламы, эксклюзивный контент', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `seasons`
--

CREATE TABLE `seasons` (
  `id` int UNSIGNED NOT NULL,
  `series_id` int UNSIGNED NOT NULL,
  `number` tinyint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `seasons`
--

INSERT INTO `seasons` (`id`, `series_id`, `number`, `title`) VALUES
(1, 1, 1, 'Сезон 1'),
(2, 2, 1, 'Сезон 1'),
(3, 3, 1, 'Сезон 1'),
(4, 3, 2, 'Сезон 2');

-- --------------------------------------------------------

--
-- Структура таблицы `series`
--

CREATE TABLE `series` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year_start` smallint UNSIGNED DEFAULT NULL,
  `year_end` smallint UNSIGNED DEFAULT NULL,
  `age_rating` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `poster_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `backdrop_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trailer_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `is_new` tinyint(1) NOT NULL DEFAULT '0',
  `is_popular` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `series`
--

INSERT INTO `series` (`id`, `title`, `slug`, `year_start`, `year_end`, `age_rating`, `rating`, `description`, `poster_url`, `backdrop_url`, `trailer_url`, `category_id`, `is_new`, `is_popular`, `created_at`) VALUES
(1, 'Дюна: Пророчество', 'dune-prophecy', 2024, NULL, '16+', '7.4', 'За 10 000 лет до Пола Атрейдеса сестричество Бене Гессерит прокладывает путь через интриги Империи, чтобы обрести власть над судьбами человечества.', 'https://picsum.photos/seed/dune-prophecy/600/900', 'https://picsum.photos/seed/dune-prophecy-hero/1600/900', 'https://www.youtube.com/embed/qVnWpw6s0_c', 4, 1, 0, '2026-11-22 12:13:31'),
(2, 'Игра престолов', 'game-of-thrones', 2011, 2019, '18+', '9.2', 'Девять знатных домов Вестероса сражаются за Железный трон, в то время как на севере пробуждается древнее зло.', 'https://picsum.photos/seed/got/600/900', 'https://picsum.photos/seed/got-hero/1600/900', 'https://www.youtube.com/embed/KPLWWIOCOOQ', 1, 0, 1, '2026-11-22 12:13:31'),
(3, 'Основание', 'foundation', 2021, NULL, '16+', '8.5', 'По мотивам цикла Айзека Азимова: математик предсказывает падение Галактической Империи и пытается сохранить знания человечества.', 'https://picsum.photos/seed/foundation/600/900', 'https://picsum.photos/seed/foundation-hero/1600/900', 'https://www.youtube.com/embed/X4QYV5t5Owo', 4, 0, 1, '2026-11-22 12:13:31');

-- --------------------------------------------------------

--
-- Структура таблицы `series_cast`
--

CREATE TABLE `series_cast` (
  `id` int UNSIGNED NOT NULL,
  `series_id` int UNSIGNED NOT NULL,
  `person_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `series_cast`
--

INSERT INTO `series_cast` (`id`, `series_id`, `person_name`, `role`) VALUES
(1, 1, 'Эмили Уотсон', 'Валя Харконнен'),
(2, 1, 'Оливия Уильямс', 'Тула Харконнен'),
(3, 1, 'Трэвис Фиммел', 'Десмонд Харт'),
(4, 1, 'Анна Фёрстер', 'Режиссёр'),
(5, 2, 'Питер Динклэйдж', 'Тирион Ланнистер'),
(6, 2, 'Эмилия Кларк', 'Дейенерис Таргариен'),
(7, 2, 'Кит Харингтон', 'Джон Сноу'),
(8, 2, 'Дэвид Бениофф', 'Режиссёр'),
(9, 3, 'Джаред Харрис', 'Гэри Сэлдон'),
(10, 3, 'Ли Пейс', 'Братья День'),
(11, 3, 'Лу Лобель', 'Братья Рассвет'),
(12, 3, 'Дэвид С. Гойер', 'Режиссёр');

-- --------------------------------------------------------

--
-- Структура таблицы `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL,
  `status` enum('active','expired','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `user_id`, `plan_id`, `status`, `started_at`, `expires_at`) VALUES
(1, 4, 1, 'cancelled', '2026-11-22 14:36:38', '2026-12-22 17:36:38'),
(2, 4, 3, 'active', '2026-11-22 14:37:09', '2026-12-22 17:37:09');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','subscriber','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Администратор', 'admin@illuminos.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-11-22 12:13:31'),
(2, 'Подписчик', 'subscriber@illuminos.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'subscriber', '2026-11-22 12:13:31'),
(3, 'Пользователь', 'user@illuminos.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '2026-11-22 12:13:31'),
(4, 'Игорь', 'pridumann1ymail1@mail.ru', '$2y$10$4s3idZTN7FdqwXv9YvvusuDC41YZrdueqNUExu/JcVuHeHqmEfMXe', 'subscriber', '2026-11-22 14:23:40');

-- --------------------------------------------------------

--
-- Структура таблицы `view_history`
--

CREATE TABLE `view_history` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `movie_id` int UNSIGNED DEFAULT NULL,
  `series_id` int UNSIGNED DEFAULT NULL,
  `watched_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `view_history`
--

INSERT INTO `view_history` (`id`, `user_id`, `movie_id`, `series_id`, `watched_at`) VALUES
(1, 4, NULL, 1, '2026-11-22 14:52:11');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_categories_slug` (`slug`);

--
-- Индексы таблицы `episodes`
--
ALTER TABLE `episodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_episodes_season` (`season_id`);

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_favorites_user_item` (`user_id`,`item_type`,`item_id`),
  ADD KEY `idx_favorites_user` (`user_id`);

--
-- Индексы таблицы `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_movies_slug` (`slug`),
  ADD KEY `idx_movies_category` (`category_id`);

--
-- Индексы таблицы `movie_cast`
--
ALTER TABLE `movie_cast`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_movie_cast_movie` (`movie_id`);

--
-- Индексы таблицы `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `seasons`
--
ALTER TABLE `seasons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_seasons_series` (`series_id`);

--
-- Индексы таблицы `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_series_slug` (`slug`),
  ADD KEY `idx_series_category` (`category_id`);

--
-- Индексы таблицы `series_cast`
--
ALTER TABLE `series_cast`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_series_cast_series` (`series_id`);

--
-- Индексы таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subscriptions_user` (`user_id`),
  ADD KEY `idx_subscriptions_plan` (`plan_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_users_email` (`email`);

--
-- Индексы таблицы `view_history`
--
ALTER TABLE `view_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_history_user` (`user_id`),
  ADD KEY `idx_history_movie` (`movie_id`),
  ADD KEY `idx_history_series` (`series_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `episodes`
--
ALTER TABLE `episodes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT для таблицы `movie_cast`
--
ALTER TABLE `movie_cast`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT для таблицы `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `seasons`
--
ALTER TABLE `seasons`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `series`
--
ALTER TABLE `series`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `series_cast`
--
ALTER TABLE `series_cast`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `view_history`
--
ALTER TABLE `view_history`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `episodes`
--
ALTER TABLE `episodes`
  ADD CONSTRAINT `fk_episodes_season` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `fk_movies_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `movie_cast`
--
ALTER TABLE `movie_cast`
  ADD CONSTRAINT `fk_movie_cast_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `seasons`
--
ALTER TABLE `seasons`
  ADD CONSTRAINT `fk_seasons_series` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `series`
--
ALTER TABLE `series`
  ADD CONSTRAINT `fk_series_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `series_cast`
--
ALTER TABLE `series_cast`
  ADD CONSTRAINT `fk_series_cast_series` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_subscriptions_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`),
  ADD CONSTRAINT `fk_subscriptions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `view_history`
--
ALTER TABLE `view_history`
  ADD CONSTRAINT `fk_history_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_history_series` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
