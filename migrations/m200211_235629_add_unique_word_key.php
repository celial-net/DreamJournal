<?php

use yii\db\Migration;

/**
 * Class m200211_235629_add_unique_word_key
 */
class m200211_235629_add_unique_word_key extends Migration
{

    public function up()
    {
		$this->execute("ALTER TABLE freud.word ADD UNIQUE KEY `freud_word_unique` (word);");
    }

    public function down()
    {
        $this->execute('ALTER TABLE freud.word DROP KEY `freud_word_unique`;');
    }
}
