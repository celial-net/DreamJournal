<?php

use yii\db\Migration;

/**
 * Class m200314_192512_add_column_hidden_category
 */
class m200314_192512_add_column_hidden_category extends Migration
{
    public function up()
    {
		$this->addColumn('dj.dream_category', 'hidden', 'BOOL NOT NULL DEFAULT FALSE');
    }

    public function down()
    {
		$this->dropColumn('dj.dream_category', 'hidden');
    }
}
