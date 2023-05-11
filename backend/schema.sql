SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `stations` (
  `id` int(11) NOT NULL,
  `name_fi` varchar(255) NOT NULL,
  `name_sv` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `address_fi` text NOT NULL,
  `address_sv` text NOT NULL,
  `city_fi` varchar(255) DEFAULT NULL,
  `city_sv` varchar(255) DEFAULT NULL,
  `operator` varchar(255) DEFAULT NULL,
  `capacity` int(11) NOT NULL,
  `coordinate_x` float NOT NULL,
  `coordinate_y` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `trips` (
  `id` int(11) NOT NULL,
  `departure` datetime NOT NULL,
  `return` datetime NOT NULL,
  `departure_station_id` int(11) NOT NULL,
  `departure_station_name` varchar(255) NOT NULL,
  `return_station_id` int(11) NOT NULL,
  `return_station_name` varchar(255) NOT NULL,
  `distance` int(11) NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `stations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_trips_unique_key` (`departure`,`return`,`departure_station_id`,`return_station_id`,`distance`,`duration`) USING BTREE;


ALTER TABLE `trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
