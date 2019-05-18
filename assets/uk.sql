-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 18 2019 г., 20:29
-- Версия сервера: 10.1.37-MariaDB
-- Версия PHP: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `uk`
--

-- --------------------------------------------------------

--
-- Структура таблицы `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `state` tinyint(4) NOT NULL DEFAULT 0,
  `vote` tinyint(4) NOT NULL DEFAULT 0,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Запросы людей';

--
-- Дамп данных таблицы `requests`
--

INSERT INTO `requests` (`id`, `title`, `description`, `state`, `vote`, `uid`) VALUES
(1, 'Заявка 1', 'Описание 1', 0, 0, 1),
(2, 'Заявка 2', 'Описание 2', 0, 0, 1),
(4, 'foobar', 'Описание 3', 0, 0, 4),
(5, 'Заявка 4', 'Описание 4', 0, 0, 5),
(9, 'bar', 'foo', 0, 1, 1),
(25, 'title 5', 'description 5', 1, 2, 1),
(26, 'title 6', 'description 6', 1, 2, 1),
(27, 'title 7', 'description 7', 1, 2, 1),
(28, 'title 8', 'description 8', 1, 2, 1),
(29, 'title 9', 'description 9', 1, 2, 1),
(30, 'title 10', 'description 10', 1, 2, 1),
(31, 'title 22', 'description 20', 0, 0, 1),
(32, 'title 22', 'description 20', 0, 0, 1),
(33, 'title 122', 'description 122', 0, 0, 1),
(34, 'title 123', 'description 123', 0, 0, 1),
(35, 'title 123', 'description 123', 0, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `tokens`
--

CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `token` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `tokens`
--

INSERT INTO `tokens` (`id`, `uid`, `token`) VALUES
(1, 1, 'token_for_user_1'),
(2, 4, 'token_for_user_4');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` tinyint(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `type`) VALUES
(1, 'testlogin', 'testpass', 0),
(4, 'test2', 'test2', 1),
(5, 'test3', 'test3', 0),
(7, 'test4', 'test4', 1),
(8, 'test5', 'e3d704f3542b44a621ebed70dc0efe13', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `vote` tinyint(4) NOT NULL,
  `vid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `votings`
--

CREATE TABLE `votings` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Голосование';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Индексы таблицы `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `vid` (`vid`);

--
-- Индексы таблицы `votings`
--
ALTER TABLE `votings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT для таблицы `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `votings`
--
ALTER TABLE `votings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`vid`) REFERENCES `votings` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
