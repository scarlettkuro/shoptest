--
-- База даних: `shoptest`
--

-- --------------------------------------------------------

--
-- Структура таблиці `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` text,
  `price` int(11) NOT NULL,
  `img` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Дамп даних таблиці `products`
--

INSERT INTO `products` (`id`, `title`, `description`, `price`, `img`) VALUES
(1, 'A', 'product A', 100, 'img/pic_A.png'),
(2, 'B', 'product B', 200, 'img/pic_B.png'),
(3, 'C', 'product C', 300, 'img/pic_C.png'),
(4, 'D', 'product D', 400, 'img/pic_D.png'),
(5, 'E', 'product E', 500, 'img/pic_E.png'),
(6, 'F', 'product F', 600, 'img/pic_F.png'),
(7, 'G', 'product G', 700, 'img/pic_G.png'),
(8, 'H', 'product H', 100, 'img/pic_H.png'),
(9, 'I', 'product I', 200, 'img/pic_I.png'),
(10, 'J', 'product J', 300, 'img/pic_J.png'),
(11, 'K', 'product K', 400, 'img/pic_K.png'),
(12, 'L', 'product L', 500, 'img/pic_L.png'),
(13, 'M', 'product M', 600, 'img/pic_M.png');
