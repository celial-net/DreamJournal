<?php

use yii\db\Migration;

/**
 * Class m2001_30_030443_add_dj_tables
 */
class m000130_030443_add_dj_tables extends Migration
{
    // Adds the DJ tables which existed prior to migrations.
    public function up()
    {
		$this->execute("
		CREATE TABLE `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_reset_code` char(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `dream` (
  `id` binary(16) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` text,
  `dreamt_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `dream_to_user` (`user_id`),
  CONSTRAINT `dream_to_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `dream_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `dream_to_dream_type` (
  `dream_id` binary(16) NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`dream_id`,`type_id`),
  KEY `dream_to_dream_type_type_fk` (`type_id`),
  CONSTRAINT `dream_to_dream_type_dram_fk` FOREIGN KEY (`dream_id`) REFERENCES `dream` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dream_to_dream_type_type_fk` FOREIGN KEY (`type_id`) REFERENCES `dream_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `dream_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `dream_to_dream_category` (
  `dream_id` binary(16) NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`dream_id`,`category_id`),
  KEY `dream_to_dream_category_cid_fk` (`category_id`),
  CONSTRAINT `dream_to_dream_category_cid_fk` FOREIGN KEY (`category_id`) REFERENCES `dream_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dream_to_dream_category_did_fk` FOREIGN KEY (`dream_id`) REFERENCES `dream` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `dream_topic` (
  `id` binary(16) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`user_id`),
  KEY `dream_topic_to_user_fk` (`user_id`),
  CONSTRAINT `dream_topic_to_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `dream_to_dream_topic` (
  `dream_id` binary(16) NOT NULL,
  `topic_id` binary(16) NOT NULL,
  PRIMARY KEY (`dream_id`,`topic_id`),
  KEY `dream_to_dream_topic_topic_fk` (`topic_id`),
  CONSTRAINT `dream_to_dream_topic_dream_fk` FOREIGN KEY (`dream_id`) REFERENCES `dream` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dream_to_dream_topic_topic_fk` FOREIGN KEY (`topic_id`) REFERENCES `dream_topic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
		");
    }

    public function down()
    {
        echo "m2001_30_030443_add_dj_tables cannot be reverted.\n";
        return false;
    }
}
