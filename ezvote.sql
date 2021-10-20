-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `candidates`;
CREATE TABLE `candidates` (
  `candidate_id` char(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `session_id` char(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`candidate_id`),
  KEY `session_id` (`session_id`),
  CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `session_id` char(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `title` varchar(100) NOT NULL,
  `password` char(72) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` varchar(500) NOT NULL,
  `participants` int(11) NOT NULL,
  `tagline` varchar(100) NOT NULL,
  `locked` tinyint(4) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tokens`;
CREATE TABLE `tokens` (
  `token` char(18) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `tokenset_id` char(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `candidate_id` char(8) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`token`),
  KEY `candidate_id` (`candidate_id`),
  KEY `tokenset_id` (`tokenset_id`),
  CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`),
  CONSTRAINT `tokens_ibfk_2` FOREIGN KEY (`tokenset_id`) REFERENCES `tokensets` (`tokenset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tokensets`;
CREATE TABLE `tokensets` (
  `tokenset_id` char(4) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `session_id` char(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`tokenset_id`),
  KEY `session_id` (`session_id`),
  CONSTRAINT `tokensets_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2020-10-05 03:15:35
