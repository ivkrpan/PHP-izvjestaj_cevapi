CREATE DATABASE  IF NOT EXISTS `cevapi`;
USE `cevapi`;

DROP TABLE IF EXISTS `izvjestaj_redci`;

CREATE TABLE `izvjestaj_redci` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `izvjestaj_id` int(11) NOT NULL,
  `opis` varchar(100) DEFAULT NULL,
  `velikih_porcija` int(11) NOT NULL,
  `malih_porcija` int(11) NOT NULL,
  `dostavljeno_velikih_porcija` int(11) NOT NULL,
  `dostavljeno_malih_porcija` int(11) NOT NULL,
  `velikih_porcija_vikend` int(11) NOT NULL,
  `malih_porcija_vikend` int(11) NOT NULL,
  `centar` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `izvjestaji`;

CREATE TABLE `izvjestaji` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naziv` varchar(100) NOT NULL,
  `godina` int(11) NOT NULL,
  `cevapa_velika_porcija` int(11) NOT NULL,
  `cevapa_mala_porcija` int(11) NOT NULL,
  `dnevno_velikih` int(11) NOT NULL,
  `dnevno_malih` int(11) NOT NULL,
  `dostava_posto` decimal(5,2) NOT NULL,
  `vikend_posto` decimal(5,2) NOT NULL,
  `centar_posto` decimal(5,2) NOT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `parametri`;

CREATE TABLE `parametri` (
  `id` int(11) NOT NULL,
  `prodano_velikih_porcija` int(11) NOT NULL,
  `prodano_malih_porcija` int(11) NOT NULL,
  `cevapa_u_velikoj` int(11) NOT NULL,
  `cevapa_u_maloj` int(11) NOT NULL,
  `porast_vikend_posto` decimal(5,2) NOT NULL,
  `dostava_posto` decimal(5,2) NOT NULL,
  `centar_posto` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `poslovnice`;

CREATE TABLE `poslovnice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naziv` varchar(100) DEFAULT NULL,
  `adresa` varchar(200) DEFAULT NULL,
  `grad` varchar(100) DEFAULT NULL,
  `centar` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


INSERT INTO `cevapi`.`parametri` (`id`, `prodano_velikih_porcija`, `prodano_malih_porcija`, `cevapa_u_velikoj`, `cevapa_u_maloj`, `porast_vikend_posto`, `dostava_posto`, `centar_posto`) VALUES ('0', '15', '5', '10', '5', '30', '40', '14');

