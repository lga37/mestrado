-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 24-Maio-2016 às 05:41
-- Versão do servidor: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mestrado`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `edus`
--

CREATE TABLE IF NOT EXISTS `edus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumni_id` int(11) NOT NULL,
  `edu_id` bigint(11) NOT NULL,
  `edu_nome` varchar(100) NOT NULL,
  `school_id` int(11) NOT NULL,
  `degree` varchar(50) NOT NULL,
  `major` varchar(50) NOT NULL,
  `major_id` int(11) NOT NULL,
  `ano1` int(11) NOT NULL,
  `ano2` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Extraindo dados da tabela `edus`
--

INSERT INTO `edus` (`id`, `alumni_id`, `edu_id`, `edu_nome`, `school_id`, `degree`, `major`, `major_id`, `ano1`, `ano2`) VALUES
(10, 1234567, 171836909, 'Universidade Federal do Rio de Janeiro / UFRJ', 10693, 'Pos-Doctoral, ', 'Bioinformatics', 100672, 2005, 2007),
(11, 1234567, 171836571, 'PontifÃƒÂ­cia Universidade CatÃƒÂ³lica do Rio de Janeiro / PUC-RJ', 10582, 'PhD, ', 'Computer Science', 100189, 2000, 2004),
(12, 1234567, 171834658, 'PontifÃƒÂ­cia Universidade CatÃƒÂ³lica do Rio de Janeiro / PUC-RJ', 10582, 'Master, ', 'Computer Science', 100189, 1998, 2000),
(13, 1234567, 171834335, 'PontifÃƒÂ­cia Universidade CatÃƒÂ³lica do Rio de Janeiro / PUC-RJ', 10582, 'Computer Science Engineering', 'Computer Science', 100189, 1993, 1998);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
