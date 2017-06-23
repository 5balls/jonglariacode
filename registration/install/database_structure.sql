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
  `birthday` DATE COLLATE utf8_unicode_ci NOT NULL , 
  `zip` VARCHAR(100) COLLATE utf8_unicode_ci NULL , 
  `email` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `payed` BOOLEAN NOT NULL DEFAULT FALSE , 
  `boat` BOOLEAN NOT NULL DEFAULT FALSE , 
  `regtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  `arrivaltime` TIMESTAMP NULL DEFAULT NULL ,
  `ip` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `browser` VARCHAR(150) COLLATE utf8_unicode_ci NOT NULL , 
  `email_registered` BOOLEAN NOT NULL DEFAULT FALSE , 
  `email_ticket` BOOLEAN NOT NULL DEFAULT FALSE , 
  `active` BOOLEAN NOT NULL DEFAULT TRUE , 
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;


--
-- Table structure for table `galashow`
--

CREATE TABLE IF NOT EXISTS `mexicon`.`galashow` ( 
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , 
  `ticketcount` INT(2) UNSIGNED NOT NULL DEFAULT '1' ,
  `participant_id` INT(10) UNSIGNED NULL , 
  `surname` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `prename` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `birthday` DATE COLLATE utf8_unicode_ci NOT NULL , 
  `zip` VARCHAR(100) COLLATE utf8_unicode_ci NULL , 
  `email` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `payed` BOOLEAN NOT NULL DEFAULT FALSE , 
  `regtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  `arrivaltime` TIMESTAMP NULL DEFAULT NULL ,
  `ip` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `browser` VARCHAR(150) COLLATE utf8_unicode_ci NOT NULL , 
  `email_registered` BOOLEAN NOT NULL DEFAULT FALSE , 
  `email_ticket` BOOLEAN NOT NULL DEFAULT FALSE , 
  `active` BOOLEAN NOT NULL DEFAULT TRUE , 
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;
