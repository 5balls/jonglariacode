--
-- Database: `mexicon`
--
CREATE DATABASE IF NOT EXISTS `mexicon` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `mexicon`;

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE IF NOT EXISTS `mexicon`.`participants` ( 
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , 
  `surname` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `prename` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `email` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `payed` BOOLEAN NOT NULL DEFAULT FALSE , 
  `regtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  `arrivaltime` TIMESTAMP NULL DEFAULT NULL ,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;
