-- MySQL schema dump for DB to be used with WoW_Armory_Scan

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `AuctionUS`
--

DROP TABLE IF EXISTS `AuctionUS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuctionUS` (
  `pkid` smallint(6) NOT NULL AUTO_INCREMENT,
  `realm` varchar(30) NOT NULL,
  `checktime` datetime NOT NULL,
  PRIMARY KEY (`pkid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CharactersUS`
--

DROP TABLE IF EXISTS `CharactersUS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CharactersUS` (
  `pkid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(13) NOT NULL DEFAULT '',
  `realm` varchar(30) NOT NULL DEFAULT '',
  `guild` varchar(50) NOT NULL DEFAULT '',
  `class` tinyint(2) NOT NULL DEFAULT '0',
  `race` tinyint(1) NOT NULL DEFAULT '0',
  `weekincrement` tinyint(2) NOT NULL DEFAULT '1',
  `modified` int(11) NOT NULL DEFAULT '0',
  `points` smallint(5) NOT NULL DEFAULT '0',
  `checktime` datetime NOT NULL,
  PRIMARY KEY (`pkid`)
) ENGINE=MyISAM AUTO_INCREMENT=138852 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GuildUS`
--

DROP TABLE IF EXISTS `GuildUS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GuildUS` (
  `pkid` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `realm` varchar(30) NOT NULL,
  `guild` varchar(50) NOT NULL,
  `checktime` datetime NOT NULL,
  PRIMARY KEY (`pkid`)
) ENGINE=MyISAM AUTO_INCREMENT=2371 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


/*!40000 ALTER TABLE `GuildUS` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;