-- MySQL dump 10.13  Distrib 8.0.40, for Linux (x86_64)
--
-- Host: localhost    Database: crud
-- ------------------------------------------------------
-- Server version	8.0.40-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `usuario_nombre` varchar(70) DEFAULT NULL,
  `usuario_apellido` varchar(70) DEFAULT NULL,
  `usuario_email` varchar(100) DEFAULT NULL,
  `usuario_usuario` varchar(30) DEFAULT NULL,
  `usuario_clave` varchar(200) DEFAULT NULL,
  `usuario_foto` varchar(535) DEFAULT NULL,
  `usuario_creado` timestamp NULL DEFAULT NULL,
  `usuario_actualizado` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'VICTOR','HUALLPA','victor@gmail.com','vech','$2y$10$RMtjAM4VY1G15SBqgUNLIOe4shMm3z8U5.VLggnbwEE1rjVCxcvqS','victor_54.jpg','2025-01-26 02:47:48','2025-01-26 05:06:41'),(3,'DIANA','HUALLPA','','diana','$2y$10$2gbpt/O3VQuh3nS6xfVYoOw51NzHz58mk81g9Rr1eYZmDJGP8QR42','diana_49.jpg','2025-01-26 03:44:51','2025-01-26 05:03:05'),(5,'pol','quispe','','polcito','$2y$10$FXKWogEI67Pgy.oAqAN9YOh6Aq/iwlg61bQ30WR3EVi/FkKddQrXi','pol_22.jpg','2025-01-26 04:09:25','2025-01-26 04:36:16'),(6,'JAZMIN','GIMENEZ','','jazz','$2y$10$Pwp7NTPkCqoYQXQejSmdneYDmdjCHIZDwpWAF9h7BdkZ58bC15Zyi','JAZMIN_68.jpg','2025-01-26 05:08:20','2025-01-26 05:08:20'),(7,'DANIEL','QUISPE','','daniel','$2y$10$8gncD9O8oJ8r8fLmIisDEuwUGlEJtcx8dSBj/OqOJWoGtJUIk7yEq','DANIEL_34.jpg','2025-01-26 05:08:50','2025-01-26 05:08:50');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-26 12:20:38
