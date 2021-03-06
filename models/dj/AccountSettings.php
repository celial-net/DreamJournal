<?php

namespace app\models\dj;

use Yii;

/**
 * This is the model class for table "dj.account_settings".
 *
 * @property int $id
 * @property int $user_id
 * @property int $results_per_page
 * @property string $default_dream_date
 * @property string $default_dream_period
 *
 * @property User $user
 */
class AccountSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dj.account_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'results_per_page'], 'integer'],
            [['default_dream_date', 'default_dream_period'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'results_per_page' => 'Results Per Page',
            'default_dream_date' => 'Default Dream Date',
        ];
    }

    public function getDreamDateOptions(): array
	{
		return [
			'blank' => 'Blank',
			'current_day' => 'Current Day',
			'previous_day' => 'Previous Day'
		];
	}

	public function getDreamPeriodOptions(): array
	{
		return [
			'all' => 'All',
			'week' => 'Week',
			'month' => 'Month',
			'year' => 'Year',
		];
	}

	/**
	 * @return UserQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}
}
