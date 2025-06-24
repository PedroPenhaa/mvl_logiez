-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: Logiez_System
-- ------------------------------------------------------
-- Server version	5.5.5-10.11.13-MariaDB-0ubuntu0.24.04.1

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
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL COMMENT 'Tipo de entidade afetada',
  `entity_id` bigint(20) unsigned DEFAULT NULL COMMENT 'ID da entidade afetada',
  `description` text DEFAULT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Alterações realizadas' CHECK (json_valid(`changes`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  KEY `activity_logs_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  KEY `activity_logs_created_at_index` (`created_at`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_logs`
--

DROP TABLE IF EXISTS `api_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `api_service` varchar(50) NOT NULL COMMENT 'Nome do serviço de API',
  `endpoint` varchar(255) NOT NULL,
  `http_method` varchar(10) NOT NULL,
  `request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_data`)),
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_data`)),
  `response_code` int(11) DEFAULT NULL,
  `execution_time` decimal(10,3) DEFAULT NULL COMMENT 'Tempo de execução em segundos',
  `status` varchar(20) NOT NULL DEFAULT 'success',
  `error_message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `api_logs_user_id_foreign` (`user_id`),
  KEY `api_logs_api_service_index` (`api_service`),
  KEY `api_logs_created_at_index` (`created_at`),
  CONSTRAINT `api_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_logs`
--

LOCK TABLES `api_logs` WRITE;
/*!40000 ALTER TABLE `api_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  UNIQUE KEY `cache_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fedex_labels`
--

DROP TABLE IF EXISTS `fedex_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fedex_labels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `label_url` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `api_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`api_response`)),
  `request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_data`)),
  `shipping_cost` decimal(10,2) DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `recipient_address` varchar(255) DEFAULT NULL,
  `recipient_city` varchar(255) DEFAULT NULL,
  `recipient_state` varchar(255) DEFAULT NULL,
  `recipient_country` varchar(255) DEFAULT NULL,
  `recipient_postal_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fedex_labels_user_id_foreign` (`user_id`),
  CONSTRAINT `fedex_labels_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fedex_labels`
--

LOCK TABLES `fedex_labels` WRITE;
/*!40000 ALTER TABLE `fedex_labels` DISABLE KEYS */;
/*!40000 ALTER TABLE `fedex_labels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2024_03_18_000001_create_users_table',1),(2,'2024_03_18_000002_create_user_profiles_table',1),(3,'2024_03_18_000003_create_quotes_table',1),(4,'2024_03_18_000004_create_shipments_table',1),(5,'2024_03_18_000005_create_sender_addresses_table',1),(6,'2024_03_18_000006_create_recipient_addresses_table',1),(7,'2024_03_18_000007_create_shipment_items_table',1),(8,'2024_03_18_000008_create_tracking_events_table',1),(9,'2024_03_18_000009_create_payments_table',1),(10,'2024_03_18_000010_create_proof_of_delivery_table',1),(11,'2024_03_18_000011_create_user_settings_table',1),(12,'2024_03_18_000012_create_saved_addresses_table',1),(13,'2024_03_18_000013_create_api_logs_table',1),(14,'2024_03_18_000014_create_notifications_table',1),(15,'2024_03_18_000015_create_activity_logs_table',1),(16,'2024_03_18_000016_create_shipping_rates_table',1),(17,'2024_03_18_000017_create_cache_table',2),(18,'2024_03_18_000018_add_missing_columns_to_shipments_table',3),(19,'2024_03_18_000019_add_missing_columns_to_payments_table',4),(20,'2025_06_17_170959_add_fedex_columns_to_shipments_table',5),(21,'2025_06_17_172651_add_profile_columns_to_user_profiles_table',6),(22,'2024_03_19_000000_create_fedex_labels_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `shipment_id` bigint(20) unsigned DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL COMMENT 'ID da transação no gateway',
  `payment_id` varchar(255) DEFAULT NULL COMMENT 'ID do pagamento no gateway',
  `payment_method` varchar(50) NOT NULL COMMENT 'Método de pagamento',
  `payment_gateway` varchar(50) NOT NULL DEFAULT 'asaas' COMMENT 'Gateway de pagamento',
  `amount` decimal(10,2) NOT NULL COMMENT 'Valor do pagamento',
  `net_value` decimal(10,2) DEFAULT NULL COMMENT 'Valor líquido após taxas',
  `currency` varchar(3) NOT NULL DEFAULT 'BRL',
  `status` varchar(50) NOT NULL DEFAULT 'pending' COMMENT 'Status do pagamento',
  `was_refunded` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Se foi reembolsado',
  `refund_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Dados do reembolso' CHECK (json_valid(`refund_data`)),
  `decline_reason` text DEFAULT NULL COMMENT 'Motivo de recusa',
  `payment_date` timestamp NULL DEFAULT NULL COMMENT 'Data do pagamento',
  `confirmed_date` timestamp NULL DEFAULT NULL COMMENT 'Data de confirmação',
  `credit_date` timestamp NULL DEFAULT NULL COMMENT 'Data de crédito',
  `due_date` timestamp NULL DEFAULT NULL COMMENT 'Data de vencimento',
  `payer_name` varchar(255) DEFAULT NULL,
  `payer_document` varchar(20) DEFAULT NULL COMMENT 'CPF/CNPJ do pagador',
  `payer_email` varchar(255) DEFAULT NULL,
  `invoice_url` varchar(512) DEFAULT NULL COMMENT 'URL da fatura',
  `invoice_number` varchar(255) DEFAULT NULL COMMENT 'Número da fatura/NF',
  `external_reference` varchar(255) DEFAULT NULL COMMENT 'Referência externa',
  `credit_card_number` varchar(255) DEFAULT NULL COMMENT 'Últimos dígitos do cartão',
  `credit_card_brand` varchar(255) DEFAULT NULL COMMENT 'Bandeira do cartão',
  `barcode` varchar(255) DEFAULT NULL COMMENT 'Código de barras para pagamento',
  `qrcode` varchar(255) DEFAULT NULL COMMENT 'QR Code para pagamento PIX',
  `payment_link` varchar(512) DEFAULT NULL COMMENT 'Link de pagamento',
  `transaction_receipt_url` varchar(255) DEFAULT NULL COMMENT 'URL do comprovante',
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Resposta completa do gateway' CHECK (json_valid(`gateway_response`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_shipment_id_foreign` (`shipment_id`),
  KEY `payments_transaction_id_index` (`transaction_id`),
  KEY `payments_status_index` (`status`),
  CONSTRAINT `payments_shipment_id_foreign` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proof_of_delivery`
--

DROP TABLE IF EXISTS `proof_of_delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proof_of_delivery` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipment_id` bigint(20) unsigned NOT NULL,
  `document_url` varchar(512) DEFAULT NULL COMMENT 'URL do comprovante',
  `document_type` varchar(10) NOT NULL DEFAULT 'PDF',
  `signed_by` varchar(255) DEFAULT NULL COMMENT 'Nome de quem assinou',
  `delivery_date` timestamp NULL DEFAULT NULL,
  `request_date` timestamp NULL DEFAULT NULL COMMENT 'Data da solicitação',
  `expiration_date` timestamp NULL DEFAULT NULL COMMENT 'Data de expiração do link',
  `request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_data`)),
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proof_of_delivery_shipment_id_foreign` (`shipment_id`),
  CONSTRAINT `proof_of_delivery_shipment_id_foreign` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proof_of_delivery`
--

LOCK TABLES `proof_of_delivery` WRITE;
/*!40000 ALTER TABLE `proof_of_delivery` DISABLE KEYS */;
/*!40000 ALTER TABLE `proof_of_delivery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quotes`
--

DROP TABLE IF EXISTS `quotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `origin_postal_code` varchar(20) NOT NULL,
  `origin_country` varchar(2) NOT NULL DEFAULT 'BR',
  `destination_postal_code` varchar(20) NOT NULL,
  `destination_country` varchar(2) NOT NULL,
  `package_height` decimal(10,2) NOT NULL COMMENT 'Altura em cm',
  `package_width` decimal(10,2) NOT NULL COMMENT 'Largura em cm',
  `package_length` decimal(10,2) NOT NULL COMMENT 'Comprimento em cm',
  `package_weight` decimal(10,2) NOT NULL COMMENT 'Peso em kg',
  `cubic_weight` decimal(10,2) DEFAULT NULL COMMENT 'Peso cúbico calculado',
  `carrier` varchar(100) NOT NULL DEFAULT 'FEDEX' COMMENT 'Transportadora',
  `service_code` varchar(100) DEFAULT NULL COMMENT 'Código do serviço cotado',
  `service_name` varchar(255) DEFAULT NULL COMMENT 'Nome do serviço',
  `delivery_time_min` int(11) DEFAULT NULL COMMENT 'Tempo mínimo de entrega em dias',
  `delivery_time_max` int(11) DEFAULT NULL COMMENT 'Tempo máximo de entrega em dias',
  `total_price` decimal(10,2) DEFAULT NULL COMMENT 'Valor total do frete em USD',
  `base_price` decimal(10,2) DEFAULT NULL COMMENT 'Preço base do frete',
  `tax_amount` decimal(10,2) DEFAULT NULL COMMENT 'Valor de impostos',
  `additional_fee` decimal(10,2) DEFAULT NULL COMMENT 'Taxas adicionais',
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `exchange_rate` decimal(10,4) DEFAULT NULL COMMENT 'Taxa de câmbio para BRL',
  `total_price_brl` decimal(10,2) DEFAULT NULL COMMENT 'Valor total em BRL',
  `request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Dados enviados para a API' CHECK (json_valid(`request_data`)),
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Resposta completa da API' CHECK (json_valid(`response_data`)),
  `is_simulation` tinyint(1) NOT NULL DEFAULT 0,
  `quote_reference` varchar(100) DEFAULT NULL COMMENT 'Referência da cotação',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Data de expiração da cotação',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotes_user_id_foreign` (`user_id`),
  KEY `quotes_origin_postal_code_index` (`origin_postal_code`),
  KEY `quotes_destination_postal_code_index` (`destination_postal_code`),
  CONSTRAINT `quotes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotes`
--

LOCK TABLES `quotes` WRITE;
/*!40000 ALTER TABLE `quotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipient_addresses`
--

DROP TABLE IF EXISTS `recipient_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipient_addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipment_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `address_complement` varchar(100) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(50) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(2) NOT NULL,
  `is_residential` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_addresses_shipment_id_foreign` (`shipment_id`),
  CONSTRAINT `recipient_addresses_shipment_id_foreign` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipient_addresses`
--

LOCK TABLES `recipient_addresses` WRITE;
/*!40000 ALTER TABLE `recipient_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `recipient_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saved_addresses`
--

DROP TABLE IF EXISTS `saved_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saved_addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `address_type` enum('sender','recipient') NOT NULL,
  `nickname` varchar(100) DEFAULT NULL COMMENT 'Nome amigável para o endereço',
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `address_complement` varchar(100) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(50) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(2) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_residential` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `saved_addresses_user_id_address_type_index` (`user_id`,`address_type`),
  CONSTRAINT `saved_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saved_addresses`
--

LOCK TABLES `saved_addresses` WRITE;
/*!40000 ALTER TABLE `saved_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `saved_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sender_addresses`
--

DROP TABLE IF EXISTS `sender_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sender_addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipment_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `address_complement` varchar(100) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(50) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'BR',
  `is_residential` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sender_addresses_shipment_id_foreign` (`shipment_id`),
  CONSTRAINT `sender_addresses_shipment_id_foreign` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sender_addresses`
--

LOCK TABLES `sender_addresses` WRITE;
/*!40000 ALTER TABLE `sender_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `sender_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipment_items`
--

DROP TABLE IF EXISTS `shipment_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipment_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipment_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `weight` decimal(10,2) DEFAULT NULL COMMENT 'Peso em kg',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `country_of_origin` varchar(2) NOT NULL DEFAULT 'BR' COMMENT 'País de origem do produto',
  `harmonized_code` varchar(20) DEFAULT NULL COMMENT 'Código NCM/Harmonizado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipment_items_shipment_id_foreign` (`shipment_id`),
  CONSTRAINT `shipment_items_shipment_id_foreign` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipment_items`
--

LOCK TABLES `shipment_items` WRITE;
/*!40000 ALTER TABLE `shipment_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipment_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipments`
--

DROP TABLE IF EXISTS `shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `quote_id` bigint(20) unsigned DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipment_id` varchar(100) DEFAULT NULL COMMENT 'ID de referência do transportador',
  `carrier` varchar(100) NOT NULL DEFAULT 'FEDEX',
  `tipo_envio` varchar(255) NOT NULL COMMENT 'Tipo do envio (venda, devolução, etc)',
  `tipo_pessoa` varchar(255) NOT NULL COMMENT 'Tipo de pessoa (pf, pj)',
  `invoice_number` varchar(255) DEFAULT NULL COMMENT 'Número da nota fiscal',
  `invoice_value` decimal(10,2) DEFAULT NULL COMMENT 'Valor da nota fiscal',
  `invoice_key` varchar(255) DEFAULT NULL COMMENT 'Chave da nota fiscal',
  `service_code` varchar(100) DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `label_url` varchar(512) DEFAULT NULL COMMENT 'URL da etiqueta',
  `label_format` varchar(10) NOT NULL DEFAULT 'PDF',
  `status` varchar(50) NOT NULL DEFAULT 'created' COMMENT 'Status do envio',
  `status_description` varchar(255) DEFAULT NULL,
  `last_status_update` timestamp NULL DEFAULT NULL,
  `package_height` decimal(10,2) NOT NULL COMMENT 'Altura em cm',
  `package_width` decimal(10,2) NOT NULL COMMENT 'Largura em cm',
  `package_length` decimal(10,2) NOT NULL COMMENT 'Comprimento em cm',
  `package_weight` decimal(10,2) NOT NULL COMMENT 'Peso em kg',
  `total_price` decimal(10,2) DEFAULT NULL COMMENT 'Valor total do frete',
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `total_price_brl` decimal(10,2) DEFAULT NULL COMMENT 'Valor total em BRL',
  `ship_date` date DEFAULT NULL COMMENT 'Data do envio',
  `estimated_delivery_date` date DEFAULT NULL COMMENT 'Data estimada de entrega',
  `delivery_date` date DEFAULT NULL COMMENT 'Data efetiva de entrega',
  `is_simulation` tinyint(1) NOT NULL DEFAULT 0,
  `was_delivered` tinyint(1) NOT NULL DEFAULT 0,
  `has_issues` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL COMMENT 'Observações do envio',
  `additional_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Dados adicionais em formato JSON' CHECK (json_valid(`additional_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shipping_label_url` text DEFAULT NULL COMMENT 'URL do arquivo da etiqueta de envio',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipments_tracking_number_unique` (`tracking_number`),
  KEY `shipments_user_id_foreign` (`user_id`),
  KEY `shipments_quote_id_foreign` (`quote_id`),
  KEY `shipments_tracking_number_index` (`tracking_number`),
  KEY `shipments_status_index` (`status`),
  CONSTRAINT `shipments_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipments`
--

LOCK TABLES `shipments` WRITE;
/*!40000 ALTER TABLE `shipments` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_rates`
--

DROP TABLE IF EXISTS `shipping_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `carrier` varchar(100) NOT NULL DEFAULT 'FEDEX',
  `service_code` varchar(100) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `origin_country` varchar(2) NOT NULL,
  `destination_country` varchar(2) NOT NULL,
  `min_weight` decimal(10,2) DEFAULT NULL COMMENT 'Peso mínimo em kg',
  `max_weight` decimal(10,2) DEFAULT NULL COMMENT 'Peso máximo em kg',
  `base_price` decimal(10,2) DEFAULT NULL,
  `price_per_kg` decimal(10,2) DEFAULT NULL,
  `handling_fee` decimal(10,2) DEFAULT NULL COMMENT 'Taxa de manuseio',
  `fuel_surcharge` decimal(10,2) DEFAULT NULL COMMENT 'Sobretaxa de combustível',
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `delivery_time_min` int(11) DEFAULT NULL COMMENT 'Tempo mínimo de entrega em dias',
  `delivery_time_max` int(11) DEFAULT NULL COMMENT 'Tempo máximo de entrega em dias',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipping_rates_carrier_service_code_index` (`carrier`,`service_code`),
  KEY `shipping_rates_origin_country_destination_country_index` (`origin_country`,`destination_country`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_rates`
--

LOCK TABLES `shipping_rates` WRITE;
/*!40000 ALTER TABLE `shipping_rates` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipping_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tracking_events`
--

DROP TABLE IF EXISTS `tracking_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tracking_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipment_id` bigint(20) unsigned NOT NULL,
  `event_date` timestamp NULL DEFAULT NULL,
  `event_type` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `is_exception` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica se é um evento de exceção',
  `raw_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Dados brutos do evento' CHECK (json_valid(`raw_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tracking_events_shipment_id_foreign` (`shipment_id`),
  KEY `tracking_events_event_date_index` (`event_date`),
  KEY `tracking_events_event_type_index` (`event_type`),
  CONSTRAINT `tracking_events_shipment_id_foreign` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tracking_events`
--

LOCK TABLES `tracking_events` WRITE;
/*!40000 ALTER TABLE `tracking_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `tracking_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_document` varchar(20) DEFAULT NULL COMMENT 'CNPJ da empresa',
  `state_registration` varchar(20) DEFAULT NULL COMMENT 'Inscrição estadual',
  `business_area` varchar(100) DEFAULT NULL,
  `shipping_volume` varchar(50) DEFAULT NULL COMMENT 'Volume estimado de envios',
  `default_sender_name` varchar(255) DEFAULT NULL,
  `default_sender_address` varchar(255) DEFAULT NULL,
  `default_sender_complement` varchar(100) DEFAULT NULL,
  `default_sender_city` varchar(100) DEFAULT NULL,
  `default_sender_state` varchar(50) DEFAULT NULL,
  `default_sender_postal_code` varchar(20) DEFAULT NULL,
  `default_sender_country` varchar(2) NOT NULL DEFAULT 'BR',
  `default_sender_phone` varchar(20) DEFAULT NULL,
  `default_sender_email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL COMMENT 'Número de telefone do usuário',
  `address` varchar(255) DEFAULT NULL COMMENT 'Endereço completo',
  `city` varchar(255) DEFAULT NULL COMMENT 'Cidade',
  `state` varchar(2) DEFAULT NULL COMMENT 'Estado (UF)',
  `zip_code` varchar(255) DEFAULT NULL COMMENT 'CEP',
  `country` varchar(2) DEFAULT NULL COMMENT 'País (código ISO)',
  PRIMARY KEY (`id`),
  KEY `user_profiles_user_id_foreign` (`user_id`),
  CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profiles`
--

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;
INSERT INTO `user_profiles` VALUES (1,1,'CPF:125.568.985-78',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'BR',NULL,NULL,'2025-06-17 20:27:42','2025-06-17 20:27:42','(35) 95986-5656','Rua Jardim Almeida, 50 - Apto 35','Poços de Caldas','MG','37589-685','BR');
/*!40000 ALTER TABLE `user_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_settings`
--

DROP TABLE IF EXISTS `user_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `notification_email` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Receber notificações por email',
  `notification_sms` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Receber notificações por SMS',
  `default_currency` varchar(3) NOT NULL DEFAULT 'BRL',
  `language` varchar(5) NOT NULL DEFAULT 'pt-BR',
  `timezone` varchar(50) NOT NULL DEFAULT 'America/Sao_Paulo',
  `dashboard_view` varchar(20) NOT NULL DEFAULT 'summary',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_settings_user_id_foreign` (`user_id`),
  CONSTRAINT `user_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_settings`
--

LOCK TABLES `user_settings` WRITE;
/*!40000 ALTER TABLE `user_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_type` enum('individual','business') NOT NULL DEFAULT 'individual',
  `document_number` varchar(20) DEFAULT NULL COMMENT 'CPF/CNPJ do usuário',
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `address_complement` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'BR',
  `remember_token` varchar(100) DEFAULT NULL,
  `api_token` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_api_token_unique` (`api_token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Pedro','pedro.eng98@gmail.com',NULL,'$2y$12$d.5nk6axjI9wmmX9t0zeoOQI4MH6oEzVK8w8wIvEdAqMrzt.lgdpe','individual',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'BR',NULL,NULL,1,NULL,'2025-06-17 19:52:49','2025-06-17 19:52:49');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-24  7:37:54
