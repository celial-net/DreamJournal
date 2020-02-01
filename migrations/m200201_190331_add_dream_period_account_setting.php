<?php

use yii\db\Migration;

/**
 * Class m200201_190331_add_dream_period_account_setting
 */
class m200201_190331_add_dream_period_account_setting extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
		$this->addColumn('dj.account_settings', 'default_dream_period', "ENUM('week', 'month', 'year', 'all') NOT NULL DEFAULT 'month'");
    }

    public function down()
    {
		$this->dropColumn('dj.account_settings', 'default_dream_period');
    }
}
