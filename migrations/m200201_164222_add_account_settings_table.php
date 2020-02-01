<?php

use yii\db\Migration;

/**
 * Class m200201_164222_add_account_settings_table
 */
class m200201_164222_add_account_settings_table extends Migration
{
    public function up()
    {
		$this->createTable('dj.account_settings', [
			'id' => 'BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT',
			'user_id' => 'BIGINT UNSIGNED NOT NULL',
			'results_per_page' => 'INT UNSIGNED NOT NULL DEFAULT 10',
			'default_dream_date' => "ENUM('current_day', 'previous_day', 'blank') NOT NULL DEFAULT 'blank'",
			"
				CONSTRAINT account_settings_to_user_fk
					FOREIGN KEY (user_id)
					REFERENCES dj.user(id)
					ON UPDATE CASCADE
					ON DELETE CASCADE
			"
		]);
    }

    public function down()
    {
        $this->dropTable('dj.account_settings');
    }
}
