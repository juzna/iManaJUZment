-- MySQL dump 10.13  Distrib 5.1.41, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: servisweb3
-- ------------------------------------------------------
-- Server version	5.1.41-3ubuntu12.7

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
-- Table structure for table `AP`
--

DROP TABLE IF EXISTS `AP`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AP` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `mode` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `IP` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `netmask` int(11) DEFAULT NULL,
  `pvid` int(11) DEFAULT NULL,
  `snmpAllowed` tinyint(1) DEFAULT NULL,
  `snmpCommunity` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `snmpPassword` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `snmpVersion` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `realm` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `username` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `pass` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `os` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `osVersion` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `sshFingerprint` varchar(60) COLLATE utf8_czech_ci DEFAULT NULL,
  `l3parent` int(11) DEFAULT NULL,
  `l3parentIf` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `posX` int(11) DEFAULT NULL,
  `posY` int(11) DEFAULT NULL,
  `ulice` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `cisloPopisne` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `mesto` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `PSC` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `stat` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `uir_obec` int(11) DEFAULT NULL,
  `uir_cobce` int(11) DEFAULT NULL,
  `uir_ulice` int(11) DEFAULT NULL,
  `uir_objekt` int(11) DEFAULT NULL,
  `uir_special` tinyint(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `isDeleted` tinyint(1) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `APNetwork_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `AP_name_uniq` (`name`),
  KEY `AP_APNetwork_ID_idx` (`APNetwork_ID`),
  CONSTRAINT `AP_ibfk_1` FOREIGN KEY (`APNetwork_ID`) REFERENCES `APNetwork` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AP`
--

LOCK TABLES `AP` WRITE;
/*!40000 ALTER TABLE `AP` DISABLE KEYS */;
INSERT INTO `AP` VALUES (1,'prvni','prvni masina v siti','nat','192.168.1.1',24,1,0,'','','','','','','','','',NULL,'',NULL,NULL,'','','','','',NULL,NULL,NULL,NULL,0,NULL,'2010-12-20 00:22:01',0,NULL,1),(2,'druha','druha masina v siti',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(3,'mario','','','',0,0,NULL,'','','','','','','','','',0,'',0,0,'','','','','',0,0,0,0,NULL,'2010-12-14 21:27:45','2010-12-14 21:27:45',NULL,NULL,1),(4,'lol','','','',NULL,NULL,NULL,'','','','','','','','','',NULL,'',NULL,NULL,'','','','','',NULL,NULL,NULL,NULL,NULL,'2010-12-14 21:31:51','2010-12-14 21:31:51',NULL,NULL,1),(5,'prvni druha','prvni masina v siti','nat','192.168.1.2',24,NULL,NULL,'','','','','','','','','',NULL,'',NULL,NULL,'','','','','',NULL,NULL,NULL,NULL,NULL,'2010-12-15 01:47:35','2010-12-15 01:47:35',NULL,NULL,1);
/*!40000 ALTER TABLE `AP` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APAntenna`
--

DROP TABLE IF EXISTS `APAntenna`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APAntenna` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `interface` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `smer` int(11) NOT NULL,
  `rozsah` int(11) NOT NULL,
  `dosah` int(11) NOT NULL,
  `polarita` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `pasmo` int(11) DEFAULT NULL,
  `poznamka` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `AP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APAntenna_AP_idx` (`AP`),
  CONSTRAINT `APAntenna_ibfk_1` FOREIGN KEY (`AP`) REFERENCES `AP` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APAntenna`
--

LOCK TABLES `APAntenna` WRITE;
/*!40000 ALTER TABLE `APAntenna` DISABLE KEYS */;
/*!40000 ALTER TABLE `APAntenna` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APCoverage`
--

DROP TABLE IF EXISTS `APCoverage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APCoverage` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `interface` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `vlan` int(11) DEFAULT NULL,
  `adresa` int(11) NOT NULL,
  `poznamka` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `doporuceni` int(11) NOT NULL,
  `AP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APCoverage_AP_idx` (`AP`),
  CONSTRAINT `APCoverage_ibfk_1` FOREIGN KEY (`AP`) REFERENCES `AP` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APCoverage`
--

LOCK TABLES `APCoverage` WRITE;
/*!40000 ALTER TABLE `APCoverage` DISABLE KEYS */;
/*!40000 ALTER TABLE `APCoverage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APCoverageSubnet`
--

DROP TABLE IF EXISTS `APCoverageSubnet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APCoverageSubnet` (
  `coverage` int(11) DEFAULT NULL,
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `netmask` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `APCoverageSubnet_coverage_idx` (`coverage`),
  CONSTRAINT `APCoverageSubnet_ibfk_1` FOREIGN KEY (`coverage`) REFERENCES `APCoverage` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APCoverageSubnet`
--

LOCK TABLES `APCoverageSubnet` WRITE;
/*!40000 ALTER TABLE `APCoverageSubnet` DISABLE KEYS */;
/*!40000 ALTER TABLE `APCoverageSubnet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APHw`
--

DROP TABLE IF EXISTS `APHw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APHw` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `serial` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `AP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APHw_AP_idx` (`AP`),
  CONSTRAINT `APHw_ibfk_1` FOREIGN KEY (`AP`) REFERENCES `AP` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APHw`
--

LOCK TABLES `APHw` WRITE;
/*!40000 ALTER TABLE `APHw` DISABLE KEYS */;
/*!40000 ALTER TABLE `APHw` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APHwIf`
--

DROP TABLE IF EXISTS `APHwIf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APHwIf` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `interface` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `mac` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `typ` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `APHw` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APHwIf_APHw_idx` (`APHw`),
  CONSTRAINT `APHwIf_ibfk_1` FOREIGN KEY (`APHw`) REFERENCES `APHw` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APHwIf`
--

LOCK TABLES `APHwIf` WRITE;
/*!40000 ALTER TABLE `APHwIf` DISABLE KEYS */;
/*!40000 ALTER TABLE `APHwIf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APIP`
--

DROP TABLE IF EXISTS `APIP`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APIP` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `interface` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `ip` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `netmask` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL,
  `AP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APIP_AP_idx` (`AP`),
  CONSTRAINT `APIP_ibfk_1` FOREIGN KEY (`AP`) REFERENCES `AP` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APIP`
--

LOCK TABLES `APIP` WRITE;
/*!40000 ALTER TABLE `APIP` DISABLE KEYS */;
INSERT INTO `APIP` VALUES (1,'ether1','192.168.10.1',24,'',0,1);
/*!40000 ALTER TABLE `APIP` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APNetwork`
--

DROP TABLE IF EXISTS `APNetwork`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APNetwork` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APNetwork`
--

LOCK TABLES `APNetwork` WRITE;
/*!40000 ALTER TABLE `APNetwork` DISABLE KEYS */;
INSERT INTO `APNetwork` VALUES (1,'Kromeriz','Hlavni sit');
/*!40000 ALTER TABLE `APNetwork` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APParams`
--

DROP TABLE IF EXISTS `APParams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APParams` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `value` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `AP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APParams_AP_idx` (`AP`),
  CONSTRAINT `APParams_ibfk_1` FOREIGN KEY (`AP`) REFERENCES `AP` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APParams`
--

LOCK TABLES `APParams` WRITE;
/*!40000 ALTER TABLE `APParams` DISABLE KEYS */;
/*!40000 ALTER TABLE `APParams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APParent`
--

DROP TABLE IF EXISTS `APParent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APParent` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `parentInterface` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `parentPort` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `parentVlan` int(11) NOT NULL,
  `childInterface` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `childPort` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `childVlan` int(11) NOT NULL,
  `comment` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `parentAP` int(11) DEFAULT NULL,
  `childAP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APParent_parentAP_idx` (`parentAP`),
  KEY `APParent_childAP_idx` (`childAP`),
  CONSTRAINT `APParent_ibfk_1` FOREIGN KEY (`parentAP`) REFERENCES `AP` (`ID`),
  CONSTRAINT `APParent_ibfk_2` FOREIGN KEY (`childAP`) REFERENCES `AP` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APParent`
--

LOCK TABLES `APParent` WRITE;
/*!40000 ALTER TABLE `APParent` DISABLE KEYS */;
/*!40000 ALTER TABLE `APParent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APPort`
--

DROP TABLE IF EXISTS `APPort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APPort` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `port` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `typ` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `PorCis` int(11) DEFAULT NULL,
  `odbernaAdresa` int(11) DEFAULT NULL,
  `cisloVchodu` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `cisloBytu` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `connectedTo` int(11) DEFAULT NULL,
  `connectedInterface` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `connectedPort` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `isUplink` tinyint(1) NOT NULL,
  `popis` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `AP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APPort_AP_idx` (`AP`),
  CONSTRAINT `APPort_ibfk_1` FOREIGN KEY (`AP`) REFERENCES `AP` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APPort`
--

LOCK TABLES `APPort` WRITE;
/*!40000 ALTER TABLE `APPort` DISABLE KEYS */;
/*!40000 ALTER TABLE `APPort` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APPortVlan`
--

DROP TABLE IF EXISTS `APPortVlan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APPortVlan` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `port` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `vlan` int(11) NOT NULL,
  `tagged` tinyint(1) NOT NULL,
  `pvid` tinyint(1) NOT NULL,
  `AP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APPortVlan_AP_idx` (`AP`),
  CONSTRAINT `APPortVlan_ibfk_1` FOREIGN KEY (`AP`) REFERENCES `AP` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APPortVlan`
--

LOCK TABLES `APPortVlan` WRITE;
/*!40000 ALTER TABLE `APPortVlan` DISABLE KEYS */;
/*!40000 ALTER TABLE `APPortVlan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APRoute`
--

DROP TABLE IF EXISTS `APRoute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APRoute` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `netmask` int(11) NOT NULL,
  `gateway` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `preferredSource` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `distance` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL,
  `AP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APRoute_AP_idx` (`AP`),
  CONSTRAINT `APRoute_ibfk_1` FOREIGN KEY (`AP`) REFERENCES `AP` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APRoute`
--

LOCK TABLES `APRoute` WRITE;
/*!40000 ALTER TABLE `APRoute` DISABLE KEYS */;
/*!40000 ALTER TABLE `APRoute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APService`
--

DROP TABLE IF EXISTS `APService`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APService` (
  `service` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `state` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `stateText` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `lastCheck` datetime DEFAULT NULL,
  `AP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APService_AP_idx` (`AP`),
  KEY `APService_service_idx` (`service`),
  CONSTRAINT `APService_ibfk_1` FOREIGN KEY (`AP`) REFERENCES `AP` (`ID`),
  CONSTRAINT `APService_ibfk_2` FOREIGN KEY (`service`) REFERENCES `APServiceDefinition` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APService`
--

LOCK TABLES `APService` WRITE;
/*!40000 ALTER TABLE `APService` DISABLE KEYS */;
/*!40000 ALTER TABLE `APService` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APServiceDefinition`
--

DROP TABLE IF EXISTS `APServiceDefinition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APServiceDefinition` (
  `code` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `nazev` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `popis` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APServiceDefinition`
--

LOCK TABLES `APServiceDefinition` WRITE;
/*!40000 ALTER TABLE `APServiceDefinition` DISABLE KEYS */;
/*!40000 ALTER TABLE `APServiceDefinition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APServiceOSList`
--

DROP TABLE IF EXISTS `APServiceOSList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APServiceOSList` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `os` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `version` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APServiceOSList`
--

LOCK TABLES `APServiceOSList` WRITE;
/*!40000 ALTER TABLE `APServiceOSList` DISABLE KEYS */;
/*!40000 ALTER TABLE `APServiceOSList` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APSwIf`
--

DROP TABLE IF EXISTS `APSwIf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APSwIf` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `interface` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `masterInterface` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `isNet` tinyint(1) NOT NULL,
  `bssid` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `essid` varchar(30) COLLATE utf8_czech_ci DEFAULT NULL,
  `frequency` int(11) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL,
  `encType` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `encKey` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `txmin` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `rxmin` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `txmax` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `rxmax` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `txburst` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `rxburst` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `txtresh` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `rxtresh` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `txtime` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `rxtime` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `txpriority` int(11) DEFAULT NULL,
  `rxpriority` int(11) DEFAULT NULL,
  `AP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APSwIf_AP_idx` (`AP`),
  CONSTRAINT `APSwIf_ibfk_1` FOREIGN KEY (`AP`) REFERENCES `AP` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APSwIf`
--

LOCK TABLES `APSwIf` WRITE;
/*!40000 ALTER TABLE `APSwIf` DISABLE KEYS */;
/*!40000 ALTER TABLE `APSwIf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APTag`
--

DROP TABLE IF EXISTS `APTag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APTag` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `color` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `APTag_name_uniq` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APTag`
--

LOCK TABLES `APTag` WRITE;
/*!40000 ALTER TABLE `APTag` DISABLE KEYS */;
INSERT INTO `APTag` VALUES (1,'optika',NULL),(2,'backbone',NULL),(3,'aaa',NULL),(4,'bbb',NULL);
/*!40000 ALTER TABLE `APTag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APTagMapping`
--

DROP TABLE IF EXISTS `APTagMapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APTagMapping` (
  `AP_id` int(11) NOT NULL,
  `APTag_id` int(11) NOT NULL,
  PRIMARY KEY (`AP_id`,`APTag_id`),
  KEY `APTagMapping_AP_id_idx` (`AP_id`),
  KEY `APTagMapping_APTag_id_idx` (`APTag_id`),
  CONSTRAINT `APTagMapping_ibfk_1` FOREIGN KEY (`AP_id`) REFERENCES `AP` (`ID`),
  CONSTRAINT `APTagMapping_ibfk_2` FOREIGN KEY (`APTag_id`) REFERENCES `APTag` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APTagMapping`
--

LOCK TABLES `APTagMapping` WRITE;
/*!40000 ALTER TABLE `APTagMapping` DISABLE KEYS */;
INSERT INTO `APTagMapping` VALUES (1,1),(1,2),(1,3),(1,4),(2,1),(2,2),(5,1);
/*!40000 ALTER TABLE `APTagMapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APVlan`
--

DROP TABLE IF EXISTS `APVlan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APVlan` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `vlan` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `AP` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `APVlan_AP_idx` (`AP`),
  CONSTRAINT `APVlan_ibfk_1` FOREIGN KEY (`AP`) REFERENCES `AP` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APVlan`
--

LOCK TABLES `APVlan` WRITE;
/*!40000 ALTER TABLE `APVlan` DISABLE KEYS */;
/*!40000 ALTER TABLE `APVlan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Adresar`
--

DROP TABLE IF EXISTS `Adresar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Adresar` (
  `ID` int(11) NOT NULL,
  `nazev` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `jePlatceDph` tinyint(1) NOT NULL,
  `zobrazit` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Adresar`
--

LOCK TABLES `Adresar` WRITE;
/*!40000 ALTER TABLE `Adresar` DISABLE KEYS */;
/*!40000 ALTER TABLE `Adresar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AdresarAdresa`
--

DROP TABLE IF EXISTS `AdresarAdresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AdresarAdresa` (
  `ID` int(11) NOT NULL,
  `isOdberna` tinyint(1) NOT NULL,
  `isFakturacni` tinyint(1) NOT NULL,
  `isKorespondencni` tinyint(1) NOT NULL,
  `popis` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `firma` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `firma2` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `titulPred` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `jmeno` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `druheJmeno` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `prijmeni` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `druhePrijmeni` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `titulZa` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `ulice` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `cisloPopisne` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `mesto` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `PSC` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `uir_objekt` int(11) DEFAULT NULL,
  `ICO` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `DIC` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `poznamka` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `rodneCislo` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `datumNarozeni` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AdresarAdresa`
--

LOCK TABLES `AdresarAdresa` WRITE;
/*!40000 ALTER TABLE `AdresarAdresa` DISABLE KEYS */;
/*!40000 ALTER TABLE `AdresarAdresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AdresarKontakt`
--

DROP TABLE IF EXISTS `AdresarKontakt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AdresarKontakt` (
  `ID` int(11) NOT NULL,
  `typ` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `hodnota` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `popis` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AdresarKontakt`
--

LOCK TABLES `AdresarKontakt` WRITE;
/*!40000 ALTER TABLE `AdresarKontakt` DISABLE KEYS */;
/*!40000 ALTER TABLE `AdresarKontakt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AdresarUcet`
--

DROP TABLE IF EXISTS `AdresarUcet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AdresarUcet` (
  `ID` int(11) NOT NULL,
  `predcisli` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `cislo` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `kodBanky` varchar(4) COLLATE utf8_czech_ci NOT NULL,
  `poznamka` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AdresarUcet`
--

LOCK TABLES `AdresarUcet` WRITE;
/*!40000 ALTER TABLE `AdresarUcet` DISABLE KEYS */;
/*!40000 ALTER TABLE `AdresarUcet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AdresarZalohovyUcet`
--

DROP TABLE IF EXISTS `AdresarZalohovyUcet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AdresarZalohovyUcet` (
  `ID` int(11) NOT NULL,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `kod` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AdresarZalohovyUcet`
--

LOCK TABLES `AdresarZalohovyUcet` WRITE;
/*!40000 ALTER TABLE `AdresarZalohovyUcet` DISABLE KEYS */;
/*!40000 ALTER TABLE `AdresarZalohovyUcet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Platba`
--

DROP TABLE IF EXISTS `Platba`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Platba` (
  `ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Platba`
--

LOCK TABLES `Platba` WRITE;
/*!40000 ALTER TABLE `Platba` DISABLE KEYS */;
/*!40000 ALTER TABLE `Platba` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Tarif`
--

DROP TABLE IF EXISTS `Tarif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Tarif` (
  `ID` int(11) NOT NULL,
  `nazev` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `zakladni` tinyint(1) NOT NULL,
  `mesicniPausal` double NOT NULL,
  `ctvrtletniPausal` double NOT NULL,
  `pololetniPausal` double NOT NULL,
  `rocniPausal` double NOT NULL,
  `popis` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `posilatFaktury` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Tarif_nazev_uniq` (`nazev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Tarif`
--

LOCK TABLES `Tarif` WRITE;
/*!40000 ALTER TABLE `Tarif` DISABLE KEYS */;
/*!40000 ALTER TABLE `Tarif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TarifFlag`
--

DROP TABLE IF EXISTS `TarifFlag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TarifFlag` (
  `ID` int(11) NOT NULL,
  `nazev` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `TarifFlag_nazev_uniq` (`nazev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TarifFlag`
--

LOCK TABLES `TarifFlag` WRITE;
/*!40000 ALTER TABLE `TarifFlag` DISABLE KEYS */;
/*!40000 ALTER TABLE `TarifFlag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TarifRychlost`
--

DROP TABLE IF EXISTS `TarifRychlost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TarifRychlost` (
  `tarif` int(11) NOT NULL,
  `flag` int(11) NOT NULL,
  PRIMARY KEY (`tarif`,`flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TarifRychlost`
--

LOCK TABLES `TarifRychlost` WRITE;
/*!40000 ALTER TABLE `TarifRychlost` DISABLE KEYS */;
/*!40000 ALTER TABLE `TarifRychlost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Uhrada`
--

DROP TABLE IF EXISTS `Uhrada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Uhrada` (
  `ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Uhrada`
--

LOCK TABLES `Uhrada` WRITE;
/*!40000 ALTER TABLE `Uhrada` DISABLE KEYS */;
/*!40000 ALTER TABLE `Uhrada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ZakanzikTarifUhrada`
--

DROP TABLE IF EXISTS `ZakanzikTarifUhrada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZakanzikTarifUhrada` (
  `ID` int(11) NOT NULL,
  `mesicu` int(11) NOT NULL,
  `datumOd` date NOT NULL,
  `datumDo` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ZakanzikTarifUhrada`
--

LOCK TABLES `ZakanzikTarifUhrada` WRITE;
/*!40000 ALTER TABLE `ZakanzikTarifUhrada` DISABLE KEYS */;
/*!40000 ALTER TABLE `ZakanzikTarifUhrada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Zakaznik`
--

DROP TABLE IF EXISTS `Zakaznik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Zakaznik` (
  `PorCis` int(11) NOT NULL,
  `cisloSmlouvy` int(11) DEFAULT NULL,
  `heslo` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `aktivniOd` date DEFAULT NULL,
  `accepted` tinyint(1) NOT NULL,
  `acceptedUser` int(11) DEFAULT NULL,
  `acceptedTime` datetime DEFAULT NULL,
  `predplaceno` date DEFAULT NULL,
  `aktivni` tinyint(1) DEFAULT NULL,
  `staryDluh` int(11) DEFAULT NULL,
  `nepocitatPredplatne` tinyint(1) NOT NULL,
  `nepocitatPredplatneDuvod` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `instalacniPoplatek` double DEFAULT NULL,
  `doporucitel` int(11) DEFAULT NULL,
  `sepsaniSmlouvy` date DEFAULT NULL,
  `neplaticSkupina` int(11) DEFAULT NULL,
  `neplaticTolerance` int(11) DEFAULT NULL,
  `neplaticNeresitDo` date DEFAULT NULL,
  PRIMARY KEY (`PorCis`),
  UNIQUE KEY `Zakaznik_cisloSmlouvy_uniq` (`cisloSmlouvy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Zakaznik`
--

LOCK TABLES `Zakaznik` WRITE;
/*!40000 ALTER TABLE `Zakaznik` DISABLE KEYS */;
/*!40000 ALTER TABLE `Zakaznik` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ZakaznikAdresa`
--

DROP TABLE IF EXISTS `ZakaznikAdresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZakaznikAdresa` (
  `ID` int(11) NOT NULL,
  `isOdberna` tinyint(1) NOT NULL,
  `isFakturacni` tinyint(1) NOT NULL,
  `isKorespondencni` tinyint(1) NOT NULL,
  `popis` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `firma` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `firma2` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `titulPred` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `jmeno` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `druheJmeno` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `prijmeni` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `druhePrijmeni` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `titulZa` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `ICO` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `DIC` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `poznamka` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `rodneCislo` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `datumNarozeni` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ZakaznikAdresa`
--

LOCK TABLES `ZakaznikAdresa` WRITE;
/*!40000 ALTER TABLE `ZakaznikAdresa` DISABLE KEYS */;
/*!40000 ALTER TABLE `ZakaznikAdresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ZakaznikIP`
--

DROP TABLE IF EXISTS `ZakaznikIP`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZakaznikIP` (
  `ID` int(11) NOT NULL,
  `IP` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `netmask` int(11) NOT NULL,
  `IPold` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `IPverej` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `MAC` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `visibleMAC` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `adresa` int(11) NOT NULL,
  `l2parent` int(11) DEFAULT NULL,
  `l2parentIf` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `l3parent` int(11) DEFAULT NULL,
  `l3parentIf` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `poznamka` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `encType` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `encKey` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `router` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `routerVlastni` tinyint(1) DEFAULT NULL,
  `voip` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `vlastniRychlost` tinyint(1) NOT NULL,
  `APIP` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `APMAC` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ZakaznikIP`
--

LOCK TABLES `ZakaznikIP` WRITE;
/*!40000 ALTER TABLE `ZakaznikIP` DISABLE KEYS */;
/*!40000 ALTER TABLE `ZakaznikIP` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ZakaznikKontakt`
--

DROP TABLE IF EXISTS `ZakaznikKontakt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZakaznikKontakt` (
  `ID` int(11) NOT NULL,
  `typ` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `hodnota` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `popis` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ZakaznikKontakt`
--

LOCK TABLES `ZakaznikKontakt` WRITE;
/*!40000 ALTER TABLE `ZakaznikKontakt` DISABLE KEYS */;
/*!40000 ALTER TABLE `ZakaznikKontakt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ZakaznikNeaktivni`
--

DROP TABLE IF EXISTS `ZakaznikNeaktivni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZakaznikNeaktivni` (
  `ID` int(11) NOT NULL,
  `datumOd` date NOT NULL,
  `datumDo` date DEFAULT NULL,
  `duvod` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ZakaznikNeaktivni`
--

LOCK TABLES `ZakaznikNeaktivni` WRITE;
/*!40000 ALTER TABLE `ZakaznikNeaktivni` DISABLE KEYS */;
/*!40000 ALTER TABLE `ZakaznikNeaktivni` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ZakaznikNeaktivniTarif`
--

DROP TABLE IF EXISTS `ZakaznikNeaktivniTarif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZakaznikNeaktivniTarif` (
  `ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ZakaznikNeaktivniTarif`
--

LOCK TABLES `ZakaznikNeaktivniTarif` WRITE;
/*!40000 ALTER TABLE `ZakaznikNeaktivniTarif` DISABLE KEYS */;
/*!40000 ALTER TABLE `ZakaznikNeaktivniTarif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ZakaznikTarif`
--

DROP TABLE IF EXISTS `ZakaznikTarif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZakaznikTarif` (
  `ID` int(11) NOT NULL,
  `popis` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `zakladni` tinyint(1) NOT NULL,
  `specialniCeny` tinyint(1) NOT NULL,
  `mesicniPausal` double DEFAULT NULL,
  `ctvrtletniPausal` double DEFAULT NULL,
  `pololetniPausal` double DEFAULT NULL,
  `rocniPausal` double DEFAULT NULL,
  `datumOd` date NOT NULL,
  `datumDo` date DEFAULT NULL,
  `predplaceno` date DEFAULT NULL,
  `aktivni` tinyint(1) NOT NULL,
  `zaplacenoCele` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ZakaznikTarif`
--

LOCK TABLES `ZakaznikTarif` WRITE;
/*!40000 ALTER TABLE `ZakaznikTarif` DISABLE KEYS */;
/*!40000 ALTER TABLE `ZakaznikTarif` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-12-20 13:43:29
