-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Авг 08 2025 г., 08:02
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `optimus`
--

-- --------------------------------------------------------

--
-- Структура таблицы `accounts`
--

CREATE TABLE `accounts` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(50) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `NEWSLETTER` tinyint(1) NOT NULL,
  `ACTIVE` tinyint(1) NOT NULL DEFAULT 0,
  `ACTIVATION_CODE` varchar(255) NOT NULL,
  `ACTIVATION_EXPIRY` datetime DEFAULT NULL,
  `ACTIVATED_AT` datetime DEFAULT NULL,
  `REGISTERED` timestamp NOT NULL DEFAULT current_timestamp(),
  `CHANGED` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `accounts`
--

INSERT INTO `accounts` (`ID`, `USERNAME`, `PASSWORD`, `EMAIL`, `NEWSLETTER`, `ACTIVE`, `ACTIVATION_CODE`, `ACTIVATION_EXPIRY`, `ACTIVATED_AT`, `REGISTERED`, `CHANGED`) VALUES
(2, 'miketyson', '$2y$10$YuSfRs1ttXf7GVMyY9ZFHOzRDCvhFtosINJFZMSeyeIPeZYPnWRAm', 'miketyson@fuck.you', 0, 1, '', NULL, NULL, '2025-03-12 06:13:14', '2025-08-06 18:57:26'),
(27, 'user3', '$2y$10$4GQ9Vb3nsuBa5j.n92PBJeMmRHqQ6ixWzWeVhDHSzBeb9sLzmF4p2', 'as3@as.one', 0, 1, '$2y$10$p6bLU0bINXdyIJ4VxN9/3O/1t.v/Ir14MRaOSbSZgEoJR7Dlg11PO', '2025-08-08 10:42:33', '2025-08-07 11:44:44', '2025-08-07 08:42:33', '2025-08-07 08:44:44'),
(12, 'user1', '$2y$10$n4CBM73Cl4mM4k3DeSqDS.Vrx9Ia2lryrGSsEza66iNF7N3DXHCd2', 'as@as.one', 0, 1, '918fac740f43561bda6d2e8da307c23e', '2025-08-08 19:13:17', '2025-08-07 08:34:28', '2025-08-07 05:29:59', '2025-08-07 17:13:17');

-- --------------------------------------------------------

--
-- Структура таблицы `competition`
--

CREATE TABLE `competition` (
  `COMPETITION_ID` int(11) NOT NULL,
  `DESCRIPTION` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`DESCRIPTION`)),
  `LICENCE_ID` int(11) NOT NULL,
  `VISIBLE` tinyint(1) DEFAULT 1,
  `CREATED` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `competition`
--

-- --------------------------------------------------------

--
-- Структура таблицы `downloads`
--

CREATE TABLE `downloads` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `event_sequence`
--

CREATE TABLE `event_sequence` (
  `next_not_cached_value` bigint(21) NOT NULL,
  `minimum_value` bigint(21) NOT NULL,
  `maximum_value` bigint(21) NOT NULL,
  `start_value` bigint(21) NOT NULL COMMENT 'start value when sequences is created or value if RESTART is used',
  `increment` bigint(21) NOT NULL COMMENT 'increment value',
  `cache_size` bigint(21) UNSIGNED NOT NULL,
  `cycle_option` tinyint(1) UNSIGNED NOT NULL COMMENT '0 if no cycles are allowed, 1 if the sequence should begin a new cycle when maximum_value is passed',
  `cycle_count` bigint(21) NOT NULL COMMENT 'How many cycles have been done'
) ENGINE=InnoDB;

--
-- Дамп данных таблицы `event_sequence`
--

INSERT INTO `event_sequence` (`next_not_cached_value`, `minimum_value`, `maximum_value`, `start_value`, `increment`, `cache_size`, `cycle_option`, `cycle_count`) VALUES
(1001, 1, 9223372036854775806, 1, 1, 1000, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `licence`
--

CREATE TABLE `licence` (
  `LICENCE_ID` int(11) NOT NULL,
  `NUMBER` varchar(36) NOT NULL,
  `NAME` varchar(127) NOT NULL,
  `EMAIL` varchar(127) NOT NULL,
  `TITLE` varchar(127) DEFAULT NULL,
  `COMPANY` varchar(127) DEFAULT NULL,
  `DATESTART` date NOT NULL,
  `DATEEND` date NOT NULL,
  `LICENCETYPE` tinyint(4) NOT NULL,
  `EVENTTYPES` int(11) NOT NULL,
  `LICENCEHASH` varchar(32) NOT NULL,
  `ACTIVE` tinyint(1) DEFAULT 1,
  `CREATED` timestamp NOT NULL DEFAULT current_timestamp(),
  `ACCOUNT_ID` int(11) NOT NULL,
  `VERSION` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `licence`
--


-- --------------------------------------------------------

--
-- Структура таблицы `maintenance`
--

CREATE TABLE `maintenance` (
  `LICENCE_ID` int(11) NOT NULL,
  `VERSION` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Дамп данных таблицы `maintenance`
--


-- --------------------------------------------------------

--
-- Структура таблицы `resetpasswords`
--

CREATE TABLE `resetpasswords` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `EXPDATE` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `resetpasswords`
--


--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`ID`);

--
-- Индексы таблицы `competition`
--
ALTER TABLE `competition`
  ADD PRIMARY KEY (`COMPETITION_ID`);

--
-- Индексы таблицы `downloads`
--
ALTER TABLE `downloads`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `licence`
--
ALTER TABLE `licence`
  ADD PRIMARY KEY (`LICENCE_ID`);

--
-- Индексы таблицы `maintenance`
--
ALTER TABLE `maintenance`
  ADD KEY `LICENCE_ID` (`LICENCE_ID`);

--
-- Индексы таблицы `resetpasswords`
--
ALTER TABLE `resetpasswords`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `accounts`
--
ALTER TABLE `accounts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT для таблицы `downloads`
--
ALTER TABLE `downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `licence`
--
ALTER TABLE `licence`
  MODIFY `LICENCE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `resetpasswords`
--
ALTER TABLE `resetpasswords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
