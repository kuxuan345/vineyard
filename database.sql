-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2026 at 11:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alcohol_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) NOT NULL,
  `year` int(4) DEFAULT NULL,
  `quantity` int(4) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `product_name`, `year`, `quantity`, `price`, `subtotal`) VALUES
(80, 59, 2, 'Billecart Salmon Brut Nature NV Champagne', 2015, 7, 583.00, 4081.00),
(81, 59, 1, 'Ayala Brut Nature Champagne Magnum Zero Dosage NV', 2012, 2, 538.99, 1077.98),
(82, 59, 3, 'Drappier Brut Nature NV Champagne', 1996, 1, 922.00, 922.00),
(85, 58, 1, 'Ayala Brut Nature Champagne Magnum Zero Dosage NV', 2017, 2, 289.47, 578.94),
(86, 58, 2, 'Billecart Salmon Brut Nature NV Champagne', 2015, 1, 583.00, 583.00);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `type`) VALUES
(1, 'BRUT NATURE', 'Champagne'),
(2, 'DOUX', 'Champagne'),
(3, 'HIPZ PRODUCTION', 'Champagne'),
(4, 'BLANCO TEQUILA', 'Tequila'),
(5, 'CRISTALINO', 'Tequila'),
(6, 'HIPZ PRODUCTION', 'Tequila'),
(7, 'BLENDED WHISKY', 'Whisky'),
(8, 'CANADIAN WHISKY', 'Whisky'),
(9, 'HIPZ PRODUCTION', 'Whisky'),
(10, 'RED WINE', 'Wine'),
(11, 'SPARKING', 'Wine'),
(12, 'HIPZ PRODUCTION', 'Wine');

-- --------------------------------------------------------

--
-- Table structure for table `checkout`
--

CREATE TABLE `checkout` (
  `order_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `item_count` int(4) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `method` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkout`
--

INSERT INTO `checkout` (`order_id`, `user_id`, `datetime`, `item_count`, `total`, `method`) VALUES
(155, 58, '2024-12-31 02:54:39', 1, 1085.30, 'CARD PAYMENT'),
(156, 58, '2024-12-31 02:54:42', 1, 1085.30, 'ONLINE BANKING'),
(157, 58, '2024-12-31 02:57:12', 1, 1085.30, 'ONLINE BANKING'),
(158, 58, '2024-12-31 03:00:12', 1, 357.89, 'ONLINE BANKING'),
(159, 58, '2024-11-30 04:39:09', 3, 1500.00, NULL),
(161, 58, '2024-12-31 05:13:24', 3, 1361.23, 'ONLINE BANKING');

-- --------------------------------------------------------

--
-- Table structure for table `editinfo`
--

CREATE TABLE `editinfo` (
  `name` varchar(100) NOT NULL,
  `contact_number` int(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `state` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `postal_code` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `update_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `editinfo`
--

INSERT INTO `editinfo` (`name`, `contact_number`, `address`, `state`, `city`, `postal_code`, `user_id`, `update_at`) VALUES
('pokpok', 123456888, 'sungai siput', 'perak', 'sunagi siput', 31100, 59, '2024-12-29 16:50:38'),
('peien', 123456789, 'PV15 Platinum Lake Condominium', 'setapak', 'kuala lumpur', 53300, 59, '2024-12-30 00:43:06'),
('pokpok', 123456888, 'sungai siput', 'perak', 'sunagi siput', 31100, 59, '2024-12-29 16:50:38'),
('peien', 123456789, 'PV15 Platinum Lake Condominium', 'setapak', 'kuala lumpur', 53300, 59, '2024-12-30 00:43:06'),
('Roseanne Park', 123456788, 'qwea', 'Perak', 'Ipoh', 31500, 60, '2024-12-30 08:22:44'),
('ivan', 125156029, 'Jalan blabla', 'kl', 'Kl', 53300, 58, '2024-12-30 14:01:29');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(10) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `year` int(4) NOT NULL,
  `quantity` int(4) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_name`, `year`, `quantity`, `price`, `subtotal`) VALUES
(395, 155, 'Drappier Brut Nature NV Champagne', 1996, 1, 922.00, 922.00),
(396, 156, 'Drappier Brut Nature NV Champagne', 1996, 1, 922.00, 922.00),
(397, 157, 'Drappier Brut Nature NV Champagne', 1996, 1, 922.00, 922.00),
(398, 158, 'Ayala Brut Nature Champagne Magnum Zero Dosage NV', 2017, 1, 289.47, 289.47),
(401, 161, 'Ayala Brut Nature Champagne Magnum Zero Dosage NV', 2017, 2, 289.47, 578.94),
(402, 161, 'Billecart Salmon Brut Nature NV Champagne', 2015, 1, 583.00, 583.00);

-- --------------------------------------------------------

--
-- Table structure for table `prices`
--

CREATE TABLE `prices` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prices`
--

INSERT INTO `prices` (`id`, `product_id`, `year`, `price`) VALUES
(1, 1, '2017', 289.47),
(2, 1, '2014', 378.62),
(3, 1, '2012', 538.99),
(4, 1, '2011', 649.13),
(5, 2, '2015', 583.00),
(6, 3, '1996', 922.00),
(7, 3, '1988', 1061.00),
(8, 3, '1996', 1560.00),
(9, 3, '1996', 2655.00),
(10, 4, '1999', 817.01),
(11, 4, '1990', 1156.32),
(12, 5, '2008', 686.00),
(13, 5, '2007', 879.61),
(14, 6, '1995', 1080.09),
(15, 6, '1997', 1035.23),
(16, 6, '2000', 748.00),
(17, 6, '2009', 530.15),
(18, 7, '2002', 1081.00),
(19, 7, '2004', 952.33),
(20, 7, '2005', 863.00),
(21, 7, '2014', 560.00),
(22, 8, '2016', 510.99),
(23, 8, '2010', 724.30),
(24, 8, '2009', 899.99),
(25, 8, '2000', 1048.80),
(26, 9, '2015', 518.00),
(27, 9, '2011', 569.00),
(28, 9, '2008', 625.00),
(29, 9, '2003', 692.00),
(30, 10, '2009', 566.00),
(31, 11, '2015', 563.16),
(32, 11, '2013', 587.17),
(33, 11, '2011', 670.90),
(34, 11, '2008', 833.75),
(35, 12, '2014', 580.00),
(36, 12, '2010', 623.20),
(37, 12, '2006', 732.00),
(38, 12, '2003', 819.20),
(39, 13, '2018', 624.91),
(40, 13, '1971', 2583.45),
(41, 13, '1971', 8031.27),
(42, 13, '1968', 4976.79),
(43, 14, '2020', 579.88),
(44, 15, '2019', 572.00),
(45, 16, '2020', 510.99),
(46, 16, '2018', 559.80),
(47, 16, '2016', 667.00),
(48, 16, '2014', 688.00),
(49, 17, '2019', 502.00),
(50, 17, '2017', 525.00),
(51, 17, '2013', 750.00),
(52, 17, '2012', 785.00),
(53, 18, '2015', 1400.00),
(54, 18, '2014', 1465.00),
(55, 18, '2013', 1567.00),
(56, 18, '2012', 1599.00),
(57, 19, '1988', 590.00),
(58, 19, '1980', 605.00),
(59, 19, '1957', 830.00),
(60, 19, '1910', 1040.00),
(61, 20, '2004', 718.00),
(62, 20, '2002', 779.00),
(63, 20, '1999', 812.00),
(64, 20, '1980', 1035.72),
(65, 21, '1945', 504.00),
(66, 21, '1920', 645.00),
(67, 21, '1903', 797.00),
(68, 21, '0000', 900.00),
(69, 22, '2019', 587.00),
(70, 22, '2017', 611.67),
(71, 22, '2015', 675.29),
(72, 22, '2010', 726.14),
(73, 23, '2017', 572.68),
(74, 23, '2014', 696.53),
(75, 24, '2019', 521.69),
(76, 25, '2020', 559.00),
(77, 25, '2017', 578.00),
(78, 26, '2014', 679.00),
(79, 27, '2016', 500.00),
(80, 28, '1994', 522.92),
(81, 28, '1983', 583.37),
(82, 28, '1978', 761.73),
(83, 28, '1970', 1007.00),
(84, 29, '2021', 549.04),
(85, 30, '2022', 521.00),
(86, 30, '2018', 661.24),
(87, 31, '2013', 566.00),
(88, 31, '2011', 580.89),
(89, 32, '2017', 522.00),
(90, 32, '2002', 830.00),
(91, 33, '2020', 1371.00),
(92, 34, '2017', 590.00),
(93, 35, '2015', 546.00),
(94, 35, '2005', 770.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `cuvee` varchar(50) DEFAULT NULL,
  `alcohol_content` varchar(50) DEFAULT NULL,
  `ingredient` text DEFAULT NULL,
  `quantity` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `image`, `description`, `type`, `cuvee`, `alcohol_content`, `ingredient`, `quantity`) VALUES
(1, 'Ayala Brut Nature Champagne Magnum Zero Dosage NV', 1, 'img/ayala2.png', 'Ayala Brut Nature magnum testifies to the House’s absolute commitment to revealing the very best of the raw material, without artifice. The absence of dosage allows the Brut Nature cuvée to achieve perfect balance, thanks to the exceptional quality of the grapes and the precision of the winemaking process.', 'Brut Nature', 'Assemblage', '12% Vol.', 'Pinot Noir 30%, Chardonnay 55%, Meunier 15%', 29),
(2, 'Billecart Salmon Brut Nature NV Champagne', 1, 'img/billecart4.png', 'Billecart Salmon Brut Nature Champagne is a non-vintage Champagne full of brightness and purity. Expressing complex and seductive aromas and a pale gold intensity on the eye, with no sugar added to the dosage, the innovative style of this Cuvee remains loyal to the discreet and balanced charm of the three Champenois grape varieties.', 'Brut Nature', 'Non-Vintage', '12.5% Vol.', '30% Pinot Noir, 30% Chardonnay, 40% Meunier', 30),
(3, 'Drappier Brut Nature NV Champagne', 1, 'img/drappier2.png', 'Drappier Brut Nature Champagne NV 75cl is a beautiful cuvee, with zero dosage and made from all Pinot Noir. The majority of the Drappier vineyard is located around Urville, where Pinot Noir, the predominant grape variety, finds its best expression and allows the production of very elegant, aromatic wines such as the Drappier Brut Nature cuvee.', 'Brut Nature', 'Non-Vintage', '12% Vol.', '100% Pinot Noir', 29),
(4, 'Andre Beaufort a Ambonnay Grand Cru Doux Rose', 2, 'img/doux1.png', 'The Champagne Rosé by André Beaufort comes from organic vineyards, free of chemicals and pasticides. The Champagne is 100% Pinot Noir, a true Rosé de Saignée, from the village of Ambonnay, classified as 100% Grand Cru. This Rosé is strong and structured, with a nice pink colour and a smell of jam.', 'Doux', 'Rosé', '12% Vol.', 'Pinot Noir, Pinot Meunier, Chardonnay', 30),
(5, 'NV Doyard La Libertine Doux', 2, 'img/doux2.png', 'The Champagne La Libertine by Doyard is the result of a long research (10-year long). Its aim was to obtain the same taste features of the Champagnes produced in the 18th century, which was a Cuvée with high dosage, 65g/l (doux). This Champagne is an assemblage of 4/5 vintages and a 10-year long refinement of yeasts. Balanced Champagne with a good mix of sugar dosage and freshness. Extremely limited production.', 'Doux', 'Non-Vintage', '12% Vol.', 'Chardonnay, Pinot Noir', 30),
(6, 'Fleury Père et Fils Doux Millésime', 2, 'img/doux3.png', 'Fleury Père et Fils is a renowned Champagne producer from the Aube region of France, known for pioneering biodynamic viticulture in Champagne. They began organic practices in 1970 and fully transitioned to biodynamics by 1989. The estate uses low-dosage methods, allowing the natural flavors of the grapes and the region\'s chalky soils to shine, resulting in wines celebrated for their complexity, freshness, and minerality.', 'Doux', 'Single-Vintage', '12% Vol.', 'Pinot Noir, Chardonnay', 30),
(7, 'HIPZ Brut Reserve NV', 3, 'img/hipz1.png', 'Lovely autolysis/lees character in this wine that performs extremely well in blind tastings because of its generosity and round, creamy character. Toasty, biscuity notes with mixed nuts on the palate with a fairly long finish. A fantastic non vintage Champagne.', 'Brut', 'Non-Vintage', '12% Vol.', 'Chardonnay, Pinot Meunier, Pinot Noir', 30),
(8, 'HIPZ Imperial Champagne', 3, 'img/hipz2.png', 'Rich and off-dry, this is pleasant, yet with straightforward citrus and ginger notes. It’s balanced with modest length. Well-balanced, exhibiting flavors of light toast, Gala apple, honey and ginger, with smoke and mineral notes underscoring hints of tropical pineapple and tangerine fruit. There\'s a lovely texture, with a firm backbone of juicy acidity.', 'Brut', 'Non-Vintage', '12% Vol.', 'Pinot Noir, Pinot Meunier and Chardonnay', 30),
(9, 'HIPZ Rose NV', 3, 'img/hipz3.png', 'Pink with amber highlights color. A lively, intense bouquet of red fruits ( wild strawberry, raspberry, cherry). Fleshiness and firmness of peach, juicy, persistent intensity of berries.', 'Brut', 'Rosé', '12% Vol.', 'Chardonnay, Pinot Meunier, Pinot Noir', 30),
(10, 'Blanco Tequila', 4, 'img/blanco1.png', 'This easy-drinking \'Uno\' blanco is produced from 10-year-old agave plants that cook for two days in traditional stone ovens. The juice is then double-distilled for purity and bottled shortly after. Notes of lemon zest, fresh agave, pepper and crushed rocks burst from the spirit’s clean and precise palate. Sip neat, stir into cocktails or do both.', 'Blanco', '-', '40% Vol.', '100% Blue Weber Agave', 30),
(11, 'Herradura Silver Tequila', 4, 'img/blanco3.png', 'This medium-bodied tequila is aged for 45 days, which adds an extra layer of texture to its smooth palate. Pleasant notes of citrus, herbs and wood beautifully complement the simple ingredients used in a Margarita.', 'Blanco', '-', '40% Vol.', '100% Blue Weber Agave', 30),
(12, 'Tequila Ocho Plata', 4, 'img/blanco2.png', 'This sipping tequila is crafted by Carlos Camarena, a third-generation tequilero, and Tomas Estes, who was an official ambassador of tequila to the EU. Agave for Tequila Ocho Plata is harvested from high-altitude growing sites and is distilled with close attention to detail. Flavors of candied limes, citrus rind, almond, starfruit and briny pineapple lead to a soft and lingering finish. No need for mixers here—this spirit is the star of the show. ', 'Blanco', '-', '40% Vol.', '100% Blue Weber Agave, Water & Natural Airborne Yeast', 30),
(13, 'Maestro Dobel 50 Cristalino Extra Añejo Tequila', 5, 'img/cristalino1.png', 'Maestro Dobel 50 Cristalino is handcrafted in the lowlands of Jalisco, Mexico, using 100% Blue Weber Agaves expertly grown and sourced from a single family-owned estate. It is aged in American and Eastern European oak barrels for a minimum of three years. The tequila matures for a minimum of three years in American and Eastern European oak barrels to create a full-bodied and full-flavored tequila.', 'Cristalino', '-', '40% Vol.', '100% Blue Weber Agave\r\n', 30),
(14, 'Tequila Komos Añejo Cristalino', 5, 'img/cristalino2.png', 'Casa Komos Beverage Group launched its añejo cristalino at the end of 2020, only for it to sell out in 60 days. The striking cobalt bottle contains a tequila that producers distill twice in copper pot stills and age for one year in French oak white wine barrels. Then, they aerate (a winemaking technique) it, resulting in a softer and smoother mouthfeel. The flavors of Tequila Komos Añejo Cristalino are expertly balanced with pineapple, miso, and peach notes that finish with a touch of minerality that leaves you wanting another sip.\r\n', 'Spirits', '-', '40% Vol.', '100% Blue Weber Agave', 30),
(15, 'Avión Reserva Cristalino', 5, 'img/cristalino3.png', 'Avión Reserva Cristalino is a blend of the brand’s 12-month-old añejo that matures in American oak with a splash of its extra añejo (a minimum aging period of three years). The brand filters the blend twice with charcoal, adding to the smoothness and clarity of the final product. It’s so clear that it almost glistens in its decanter. Oak and vanilla are prevalent on the nose. But on the palate, spice takes over with subtle pepper, cinnamon, and a hint of brown sugar.', 'Cristalino', '-', '40% Vol.', '100% Hand-Selected Agaves', 30),
(16, 'HIPZ Cristalino Tequila', 6, 'img/hipztequila1.png', 'Hip Z Cristalino Tequila is a sophisticated and intricate spirit, brimming with a symphony of aromas and nuances ranging from dried fruits and vanilla to chocolate, caramel, and tobacco. On the palate, it is silky smooth. Its crystal-clear hue is also notably striking. The finish is enduring.', 'Cristalino', '-', '40% Vol.', '100% Blue Weber Agave\r\n', 30),
(17, 'HIPZ Añejo Tequila', 6, 'img/hipztequila2.png', 'Aged to perfection. Hip Z Añejo has aged in American White Oak barrels for 25 months – an incredible 13 months beyond industry standards. The result is a remarkably smooth, amber-colored liquid that melts on your tongue leaving notes of cooked agave, toasted oak and dried fruit.', 'Añejo', '-', '40% Vol.', '100% Blue Wber Agave \r\n', 30),
(18, 'HIPZ Reposado', 6, 'img/hipztequila3.png', 'Hip Z Tequila Reposado is a symbol of Mexican tradition and culture. Made with slow-cooked 100% Blue Weber Agave, our ultra-premium reposado tequila is unique and incomparable. Hip Z Tequila Reposado is masterfully aged for eight months in American whiskey casks imparting its unique hazelnut and vanilla flavors as well as its exceptionally smooth finish. ​Its decanter is our most recognized icon with its distinctive “feathered” design, painted by hand in cobalt blue.', 'Reposado', '-', '40% Vol.', '100% Blue Agave', 30),
(19, 'Ballantine’s Finest Blended Scotch Whisky', 7, 'img/blend1.png', 'Ballantine’s Finest is an elegant, complex, refined and best scotch. Regarded as a blend to satisfy a modern style, the blended scotch whisky is unmistakable as a blend, sealing itself as one of the world’s favourite blends. This blended scotch whisky is made from more than 50 single malts, particularly single malts from Miltonduff and Glenburgie as well as four single grain whiskies.', 'Blended Scoth Whisky', '-', '40% Vol.', 'Blended wWisky Distillate, Water, Sugar Colorant (E 150a)\r\n', 30),
(20, 'Royal Salute 21 Year Old Signature Blend', 7, 'img/blend2.png', 'ROYAL SALUTE 21 Year Old Signature Blend. Originally created as a gift for the Queen’s Coronation to celebrate the 21-gun salute that honoured her in 1953. It has just had a whopper of a makeover. A new bottle that presented in a keepsake box. It looks very much the same until it’s opened up to reveal a colourful illustration of a royal menagerie created by artist Kristjana S. William.\r\n', 'Blended Scoth Whisky', '-', '40% Vol.', 'Water, Malted Barley, Cereal, Yeast', 30),
(21, 'Dewar’s White Label', 7, 'img/blend3.png', 'Despite the fact that the label is quite clearly a pale yellow colour, Dewar’s White Label this remains a hugely popular blend, especially Stateside. Dewar’s whiskies have won more than 400 awards and medals in over 20 countries.', 'Blended Scoth Whisky', '-', '40% Vol.', 'Malted Barley, Corn, Wheat, Barley, Yeast', 30),
(22, 'Forty Creek Confederation Oak', 8, 'img/canada1.png', 'This is an excellent expression from the Grimsby, Ontario distillery. Confederation Oak was created to commemorate Canada’s 1867 Confederation. It’s a blended whisky that is finished for up to two years in new Canadian oak barrels, which the distillery says have a tighter grain because of the colder climate. Look for notes of praline, honey, and dark fruits on the palate. ', 'Blended Canadian Whisky', '-', '40% Vol.', 'Corn, Rye, Barley, Canadian Oak\r\n', 30),
(23, 'Canadian Club 100% Rye', 8, 'img/canada2.png', 'Canadian Club is a well-known brand in the U.S., mostly for its extremely popular and affordable blended whisky. But this 100 percent rye whisky, which is aged in a few different barrel types, is a great example of Canadian rye and will usually cost around $20 a bottle. This is not the most intense rye whisky experience you\'ll find, but it’s certainly a good deal. Crafted by Alberta Distillers, this spirit has caramel and oak notes.\r\n', 'Canadian rye whisky', '-', '40% Vol.', '100% Rye\r\n', 30),
(24, 'Gooderham & Worts 49 Wellington', 8, 'img/canada3.png', 'The 49 Wellington is a little piece of history, calling for unmalted rye, rye malt, barley malt, wheat, corn, and red fife wheat (the latter is one of Canada’s heritage grains). All is left to mature in red oak outfitted with red oak insets. The resulting dram is complex and loaded with a unique spice, balanced out by old oak, integrated tannins, and sweetness.\r\n', 'Canadian blended whisky', '-', '40% Vol.', 'Rye, Corn, and Barley\r\n', 30),
(25, 'HIPZ Red Oak Whisky', 9, 'img/hipzwhisky1.png', 'This whisky is blended in the Scotch tradition with Japanese precision and offers a smooth, light blend made from Japanese malt whisky and grain whiskies. Pairs exceptionally well with Japanese food such as fresh sushi and salty edamame.', 'Blended Japanese Whisky', '-', '40% Vol.', 'Malted Barley, Japanese Muzinara Oak, American Oak\r\n', 30),
(26, 'HIPZ 18 YEARS HIGHLAND SINGLE MALT WHISKY', 9, 'img/hipzwhisky2.png', 'Introduced in the latter half of 2014, the 18 year old Hip Z single malt Scotch whisky from the Knockdhu distillery was matured in a combination of European oak ex-Sherry and American oak ex-bourbon casks. Following maturation, this handsome Highland whisky is bottled without chill-filtration or additional colours.', 'Single Malt Whisky', '-', '40% Vol.', 'Malted Barley, Liquids of Sherry', 30),
(27, 'HIPZ 16 YEARS SINGLE MALT WHISKY', 9, 'img/hipzwhisky3.png', '16-year-old single malt from the fantastic Hip Z distillery, which has been operating in the foothills of Scotland’s central Highlands since 2000. This expression is finished in oloroso Sherry casks, imparting decadent layers of juicy sweetness, spiced fruitcake, and dark chocolate to the whisky.\r\n', 'Single Malt Whisky', '-', '40% Vol.', 'Malted Barley\r\n', 30),
(28, 'Chateau Ste.Michelle Cabernet Sauvignon', 10, 'img/red1.png', 'Fresh and expressive, deftly balanced and juicy, with raspberry and cherry flavors supported by hints of cinnamon and pepper. The finish persists against nubby tannins.\r\n', 'Red Wine', '-', '13.5% Vol.', '89% Cabernet Sauvignon \r\n', 30),
(29, 'Freakshow Cabernet Sauvignon', 10, 'img/red2.png', 'Freakshow Cabernet spotlights the power and finesse embraced by Michael David\'s Strongman! This Cab is deep garnet in color with aromas of cranberry, dark chocolate, vanilla and exotic spice. He\'s larger-than-life, medium in body, and boasts bold flavors of raspberry, cocoa nib and pepper that stand out from the oak and spice-laden finish.', 'Red Wine', '-', '14.5% Vol.', 'Cabernet Sauvignon\r\n', 30),
(30, 'Joel Gott 815 Cabernet Sauvignon', 10, 'img/red3.png', 'The 2021 815 Cabernet Sauvignon has aromas of raspberries, blackberries, plum, and mocha with notes of cinnamon and cedar. The wine opens with red fruit flavors, followed by velvety tannins on the mid-palate and notes of black pepper on the long, textured finish.\r\n', 'Table Wine ', '-', '13.9% Vol.', 'Cabernet Sauvignon', 30),
(31, 'NV Jacquesson Extra Brut Cuvee 746', 11, 'img/spark1.png', 'Based on the generous 2018 vintage, the 746 fills the glass with a pale pink nuclear explosion of fine bubbles forming columns of visible freshness. A blend of 60% Chardonnay, 30% Pinot Noir and 10% Pinot Meunier, it is perfumed and succulent with slightly oxidized notes of ripe red apple mixed in with lemon confit, rose water, chalk dust and hints of spice. The medium to full-bodied palate is plush with showy red fruit that shimmers amid a vibrant acid energy that brings loads of freshness to the long finish.\r\n', 'Extra Brut', '746', '12.5% Vol.', '60% Chardonnay, 30% Pinot Noir and 10% Pinot Meunier\r\n', 30),
(32, 'NV Gosset Blanc De Blancs', 11, 'img/spark2.png', 'This stellar cuvee has raked in quite a few awards over the years. It’s showcasing a classy pale golden shade when poured it into the glass. On the nose expect a bouquet of flowers, white fruits, apricots, plum, and just a subtle touch of lemon, citrus fruits, quince jelly, and honey. On the palate, white flowers mingle with a hint of toastiness.\r\n', 'Brut', 'Blanc de Blancs ', '12% Vol.', '100% Chardonnay\r\n', 30),
(33, 'NV Billecart-Salmon Brut Sous Bois', 11, 'img/spark3.png', 'This unique cuvée, which is entirely vinified in oak, is composed of the three Champenois grape varieties. It totally masters the art of blending by renewing the ancestral spirit and savoir-faire of the original champagnes.', 'Extra Brut', 'Sous Bois ', '12% Vol.', '43% Grand Cru Chardonnay and 28% Premier ', 30),
(34, 'HIPZ 2020', 12, 'img/hipzwine1.png', 'The year 2020 was exceptionally dry, with 75% less rain than usual between May and September, enabling an earlier harvest to produce Hip Z 2020 with 68% Cabernet Sauvignon, 24% Carménère, 6% Cabernet Franc and 2% Petit Verdot (so no Merlot in this vintage). Aged for 20 months in French oak barrels, 73% of which are new and the remainder Seconde utilisation.', 'Red Wine', '-', '15% Vol.', '68% Cabernet Sauvignon, 24% Carménère, 6% Cabernet Franc and 2% Petit Verdot\r\n', 30),
(35, 'HIPZ Cabernet Franc', 12, 'img/hipzwine2.png', 'WINEMAKING Superb color, a rich garnet with flashes of black ink, Hip Z Cabernet Franc provides an immediate clue to this trendy variety that gives the finished wine its Argentine touch. This red grape enjoys optimal growing conditions in our vineyards above the Uco Valley, in La Arboleda. In the winery, the Cabernet Franc vintages were gently crafted into an exquisite wine.\r\n', 'Red Wine', '-', '15.3% Vol.', 'Cabernet Franc', 30),
(36, 'HIPZ Uno Malbec', 12, 'img/hipzwine3.png', 'Cellar for up to 10 years. Decant for up to an hour before serving. The Uno Malbec from Hip Z is a soft, medium to full-bodied wine, with thick scents of plum, blackberry, purple flowers, baking spice and cocoa. The Malbec grapes were sourced from Uco Valley, the high-elevation-heart of Mendoza, responsible for some of the finest wines in the country of Argentina.', 'Red Wine', '-', '13.9% Vol.', 'Malbec', 30);

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `name` varchar(100) NOT NULL,
  `contact_number` int(20) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` int(10) DEFAULT NULL,
  `photo` varchar(100) DEFAULT NULL,
  `age` int(5) DEFAULT NULL,
  `birth` date DEFAULT NULL,
  `register_time` datetime DEFAULT NULL,
  `userID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`name`, `contact_number`, `address`, `state`, `city`, `postal_code`, `photo`, `age`, `birth`, `register_time`, `userID`) VALUES
('ABC', 123456789, 'qwe', 'Perak', 'Ipoh', 31500, 'photo_67725839200605.67618755.jpg', 22, '2024-12-30', '2024-12-30 15:52:38', 60),
('Bae Suzy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-30 16:48:18', 61),
('ivan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 18:15:56', 63);

-- --------------------------------------------------------

--
-- Table structure for table `slot_rewards`
--

CREATE TABLE `slot_rewards` (
  `id` int(11) NOT NULL,
  `reward_name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slot_rewards`
--

INSERT INTO `slot_rewards` (`id`, `reward_name`, `image`) VALUES
(1, 'Congratulations! You got 1 Free Wine', 'img/freewine.jpg'),
(2, 'Congratulations! You got 1 Gift Voucher', 'img/giftvoucher.jpg'),
(3, 'Oops! Better Luck Next Time', 'img/better_luck.jpg'),
(4, 'Congratulations! You got HIPZ Merchandise', 'img/merchandise.jpg'),
(5, 'Hmmmm! Please Spin Again', 'img/slotmachine2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `stamp_transactions`
--

CREATE TABLE `stamp_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `stamps_changed` int(11) NOT NULL,
  `transaction_type` enum('earned','used') NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(10) NOT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `name`, `role`, `reset_token_hash`, `reset_token_expires_at`) VALUES
(60, '1@gmail.com', '$2y$10$KXQEWJC/g3ar72s4hpQC3e0FhMRLRPouJPB4NhL5Qg0vRjU5OL6Ae', 'ABC', 'Member', NULL, NULL),
(61, '4@gmail.com', '$2y$10$7QL1bfu5GCsUIVDorjnrxuw/D1UPgkBH9y.ukxhbtvQrA0izNtSqW', 'Bae Suzy', 'Member', NULL, NULL),
(62, 'ivanwwr-wp23@student.tarc.edu.my', '$2y$10$ikJzeaiFM2b7bK2Jbe78Q.3CXp15qYxeLZVXhzpA2jjLBAWR55nLy', 'Admin', 'Admin', NULL, NULL),
(63, 'ivanruni345@gmail.com', '$2y$10$v71mO0E5iZRoHVF9pyjdv.cCFEHghDVEgqZuAJIWjnWT63XfJqAHS', 'ivan', 'Member', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_rewards`
--

CREATE TABLE `user_rewards` (
  `id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reward_name` varchar(255) NOT NULL,
  `reward_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_claimed` tinyint(1) DEFAULT 0,
  `spin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_rewards`
--

INSERT INTO `user_rewards` (`id`, `user_id`, `reward_name`, `reward_date`, `is_claimed`, `spin_id`) VALUES
(1, 58, 'Congratulations! You got 1 Gift Voucher', '2024-12-30 05:13:07', 0, 1),
(3, 61, 'Congratulations! You got 1 Free Wine', '2024-12-30 12:15:07', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_spins`
--

CREATE TABLE `user_spins` (
  `id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_transactions` int(11) DEFAULT 0,
  `spins_used` int(11) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_spins`
--

INSERT INTO `user_spins` (`id`, `user_id`, `total_transactions`, `spins_used`, `last_updated`) VALUES
(1, 58, 0, 2, '2024-12-30 05:13:05'),
(3, 61, 0, 1, '2024-12-30 12:15:05');

-- --------------------------------------------------------

--
-- Table structure for table `user_stamps`
--

CREATE TABLE `user_stamps` (
  `user_id` int(10) NOT NULL,
  `total_stamps` int(11) NOT NULL DEFAULT 0,
  `stamps_available` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `year` int(4) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `product_name`, `year`, `price`, `image`) VALUES
(4, 59, 3, 'Drappier Brut Nature NV Champagne', 1996, 922.00, 'img/drappier2.png'),
(5, 58, 1, 'Ayala Brut Nature Champagne Magnum Zero Dosage NV', 2017, 289.47, 'img/ayala2.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `prices`
--
ALTER TABLE `prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`name`),
  ADD UNIQUE KEY `contact_number` (`contact_number`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `slot_rewards`
--
ALTER TABLE `slot_rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stamp_transactions`
--
ALTER TABLE `stamp_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `reset-token-hash` (`reset_token_hash`);

--
-- Indexes for table `user_rewards`
--
ALTER TABLE `user_rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_spins`
--
ALTER TABLE `user_spins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `user_stamps`
--
ALTER TABLE `user_stamps`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `checkout`
--
ALTER TABLE `checkout`
  MODIFY `order_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=403;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `slot_rewards`
--
ALTER TABLE `slot_rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stamp_transactions`
--
ALTER TABLE `stamp_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `user_rewards`
--
ALTER TABLE `user_rewards`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_spins`
--
ALTER TABLE `user_spins`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `checkout` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `prices`
--
ALTER TABLE `prices`
  ADD CONSTRAINT `prices_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stamp_transactions`
--
ALTER TABLE `stamp_transactions`
  ADD CONSTRAINT `stamp_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_stamps`
--
ALTER TABLE `user_stamps`
  ADD CONSTRAINT `user_stamps_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
