--
-- Database: `mexicon`
--
CREATE DATABASE IF NOT EXISTS `mexicon` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `mexicon`;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE IF NOT EXISTS `mexicon`.`person` ( 
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , 
  `surname` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `prename` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `birthday` DATE COLLATE utf8_unicode_ci NOT NULL , 
  `zip` VARCHAR(100) COLLATE utf8_unicode_ci NULL , 
  `email` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `ip` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `browser` VARCHAR(150) COLLATE utf8_unicode_ci NOT NULL , 
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;

--
-- Table structure for table `convention`
--

CREATE TABLE IF NOT EXISTS `mexicon`.`convention` ( 
  `id` INT(10) UNSIGNED NOT NULL , 
  `payed` BOOLEAN NOT NULL DEFAULT FALSE , 
  `boat` BOOLEAN NOT NULL DEFAULT FALSE , 
  `regtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  `arrivaltime` TIMESTAMP NULL DEFAULT NULL ,
  `paycode` VARCHAR(8) COLLATE utf8_unicode_ci NULL , 
  `regcode` VARCHAR(8) COLLATE utf8_unicode_ci NULL , 
  `email_registered` BOOLEAN NOT NULL DEFAULT FALSE , 
  `email_ticket` BOOLEAN NOT NULL DEFAULT FALSE , 
  `active` BOOLEAN NOT NULL DEFAULT TRUE , 
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;

--
-- Table structure for table `galashow`
--

CREATE TABLE IF NOT EXISTS `mexicon`.`galashow` ( 
  `id` INT(10) UNSIGNED NOT NULL , 
  `ticketcount` INT(2) UNSIGNED NOT NULL DEFAULT '1' ,
  `payed` BOOLEAN NOT NULL DEFAULT FALSE , 
  `regtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  `arrivaltime` TIMESTAMP NULL DEFAULT NULL ,
  `paycode` VARCHAR(8) COLLATE utf8_unicode_ci NULL , 
  `regcode` VARCHAR(8) COLLATE utf8_unicode_ci NULL , 
  `email_registered` BOOLEAN NOT NULL DEFAULT FALSE , 
  `email_ticket` BOOLEAN NOT NULL DEFAULT FALSE , 
  `active` BOOLEAN NOT NULL DEFAULT TRUE , 
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;

--
-- Table structure for table `caregiver`
--

CREATE TABLE IF NOT EXISTS `mexicon`.`caregiver` ( 
  `id` INT(10) UNSIGNED NOT NULL , 
  `caregiver_id` INT(10) UNSIGNED NULL DEFAULT NULL, 
  `surname` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `prename` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL , 
  `birthday` DATE COLLATE utf8_unicode_ci NOT NULL , 
  `verified` BOOLEAN NOT NULL DEFAULT FALSE , 
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;

