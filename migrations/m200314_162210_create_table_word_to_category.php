<?php

use yii\db\Migration;

/**
 * Class m200314_162210_create_table_word_to_category
 */
class m200314_162210_create_table_word_to_category extends Migration
{
    public function up()
    {
    	//Drop fk constraint
    	$this->execute('ALTER TABLE dj.dream_to_dream_category DROP FOREIGN KEY dream_to_dream_category_cid_fk;');

    	//Alter category ids to bigints.
    	$this->execute('
    		ALTER TABLE dj.dream_category CHANGE id id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;
    	');

		$this->execute('
    		ALTER TABLE dj.dream_to_dream_category CHANGE category_id category_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;
    	');

		//Readd the constraint
		$this->execute('ALTER TABLE dj.dream_to_dream_category ADD CONSTRAINT `dream_to_dream_category_cid_fk` FOREIGN KEY (`category_id`) REFERENCES `dream_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;');

		//Create new table to map words to categories
		$this->execute('
			CREATE TABLE dj.word_to_category (
			  word_id bigint unsigned NOT NULL,
			  category_id bigint unsigned NOT NULL,
			  certainty float DEFAULT 1,
			  KEY wc_to_word_id_fk (word_id),
			  KEY wc_to_category_id_fk (category_id),
			  CONSTRAINT wc_to_category_id_fk FOREIGN KEY (category_id) REFERENCES dj.dream_category (id) ON DELETE CASCADE ON UPDATE CASCADE,
			  CONSTRAINT wc_to_word_id_fk FOREIGN KEY (word_id) REFERENCES freud.word (id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
		');
    }

    public function down()
    {
       $this->execute('DROP TABLE dj.word_to_category;');
    }
}
