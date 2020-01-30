<?php

use yii\db\Migration;

/**
 * Class m0002_200130_030708_add_freud_tables
 */
class m0002_200130_030708_add_freud_tables extends Migration
{
    // Adds all of the freud tables which were added prior to migrations
    public function up()
    {
		$this->execute("
	CREATE TABLE `word` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Long enough for all normal real words.',
  `search` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Stemmatized version of word for searching',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39410 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `concept` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `word_to_concept` (
  `word_id` bigint(20) unsigned NOT NULL,
  `concept_id` bigint(20) unsigned NOT NULL,
  `certainty` float DEFAULT '1',
  KEY `wc_to_word_id_fk` (`word_id`),
  KEY `wc_to_concept_id_fk` (`concept_id`),
  CONSTRAINT `wc_to_concept_id_fk` FOREIGN KEY (`concept_id`) REFERENCES `concept` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wc_to_word_id_fk` FOREIGN KEY (`word_id`) REFERENCES `word` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `dream_word_freq` (
  `dream_id` binary(16) NOT NULL,
  `word_id` int(10) unsigned NOT NULL,
  `frequency` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `word_normalization` (
  `matched_word` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Word literal or regular expression to match.',
  `is_regexp` tinyint(1) NOT NULL DEFAULT '0',
  `word_id` int(10) unsigned DEFAULT NULL COMMENT 'Word to substitute. Null means the word is to be ignored.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
	
		");
    }

    public function down()
    {
        echo "m0002_200130_030708_add_freud_tables cannot be reverted.\n";

        return false;
    }
}
