-- MySQL dump 10.13  Distrib 8.0.31, for Win64 (x86_64)
--
-- Host: localhost    Database: berrygood
-- ------------------------------------------------------
-- Server version	8.0.31

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tuser`
--

DROP TABLE IF EXISTS `tuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tuser` (
  `_id` int NOT NULL AUTO_INCREMENT,
  `id` varchar(30) NOT NULL,
  `birthday` varchar(20) NOT NULL,
  `address` varchar(120) NOT NULL,
  `created_at` int NOT NULL,
  `grade` int NOT NULL,
  `password` varchar(300) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(30) NOT NULL,
  `home_number` varchar(30) NOT NULL,
  `name` varchar(10) NOT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tuser`
--

LOCK TABLES `tuser` WRITE;
/*!40000 ALTER TABLE `tuser` DISABLE KEYS */;
INSERT INTO `tuser` VALUES (3,'k','1111-111-111','여기안삼',1669479352,4,'$2y$10$x4UzHQT619JKU2GFl5q2GuNQIGCc5DYutWhFwX0opNPkl7BBMCESK','구구','010-4555-5555','111-111-11111','이상재'),(4,'sj7699','1997-02-11','영등포역',1669808120,4,'$2y$10$4G0u.Fb/nnO.q4SXxBGb1.2grGOPwFLv5t72wqW/Y99lLZmoqXyA2','tkdwo7699@gmail.com','010-4576-3563','02-2658-3563','이상재'),(6,'berrygood_admin','1970-01-01','한국항공대',1669977377,0,'$2y$10$dht4zyRs2uW5EzlhYbhhC.wDzKjfSISr6CMm.iugW1.XoaYe1Lrqi','sj_1333@naver.com','010-4576-3563','02-2323-2323','관리자'),(7,'koosaga','1997-02-11','우리집',1671173530,4,'$2y$10$5raYJ9hgIHlgxGIZBlpH9OhwIqvUUfKvtDDU03vbjNLJINdjmOKJK','address','010-4576-3563','02-2658-3563','koosaga');
/*!40000 ALTER TABLE `tuser` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-12-17 16:05:46
