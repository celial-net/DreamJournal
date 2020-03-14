<?php

namespace app\components\graph;

use app\models\dj\User;

class DreamGraphData
{
	/** @var  User|null $user */
	protected $user;

	/**
	 * Pass in a user to limit to his dream data.
	 * Pass in NULL to query all users.
	 *
	 * @param User|null $user
	 */
	public function __construct(?User $user)
	{
		$this->user = $user;
	}

	/**
	 * Gets all dream counts on each day of the week for analysis/charts.
	 *
	 * @return array
	 */
	public function getDreamCountByDayOfWeek(): array
	{
		$sql = "
			SELECT
				DAYNAME(dream.dreamt_at) AS 'day_of_week',
				COUNT(dream.id) AS 'count'
			FROM
				dj.dream dream
		  	WHERE
		  		{$this->getUserCondition()}
			GROUP BY
				DAYNAME(dream.dreamt_at)
			ORDER BY
				DAYOFWEEK(dream.dreamt_at) ASC
			;
		";
		$dreamCountData = \Yii::$app->getDb()->createCommand($sql)->queryAll();
		$dreamCountDataByDay = [];
		foreach($dreamCountData as $row)
		{
			$dreamCountDataByDay[$row['day_of_week']] = $row['count'];
		}
		return $dreamCountDataByDay;
	}

	/**
	 * Gets all dream counts on each day of the week for analysis/charts.
	 *
	 * @return array
	 */
	public function getDreamCountByMonth(): array
	{
		$sql = "
			SELECT
				CONCAT(MONTHNAME(dream.dreamt_at), ', ', YEAR(dream.dreamt_at)) AS 'year-month',
				COUNT(dream.id) AS 'count'
			FROM
				dj.dream dream
			WHERE
		  		{$this->getUserCondition()}
			GROUP BY
				YEAR(dream.dreamt_at), MONTHNAME(dream.dreamt_at)
			ORDER BY
				YEAR(dream.dreamt_at), MONTH(dream.dreamt_at) ASC
			;
		";
		$dreamCountData = \Yii::$app->getDb()->createCommand($sql)->queryAll();
		$dreamCountDataByDay = [];
		foreach($dreamCountData as $row)
		{
			$dreamCountDataByDay[$row['year-month']] = $row['count'];
		}
		return $dreamCountDataByDay;
	}

	public function getDreamCountByCategory(): array
	{
		$sql = "
			SELECT
				cat.name AS 'name',
				COUNT(dream.id) AS 'count'
			FROM
				dj.dream dream
			INNER JOIN
				dj.dream_to_dream_category d2cat ON d2cat.dream_id = dream.id
			INNER JOIN
				dj.dream_category cat ON cat.id = d2cat.category_id
			WHERE
		  		{$this->getUserCondition()}
			GROUP BY
				cat.id
			ORDER BY
				cat.name ASC
			;
		";
		$dreamCountData = \Yii::$app->getDb()->createCommand($sql)->queryAll();
		$dreamCountDataByDay = [];
		foreach($dreamCountData as $row)
		{
			$dreamCountDataByDay[$row['name']] = $row['count'];
		}
		return $dreamCountDataByDay;
	}

	/**
	 * Doesn't actually return dreams by concept
	 * but dreams by category using words instead of direct tagging.
	 *
	 * @return array
	 * @throws \yii\db\Exception
	 */
	public function getDreamCountByConcept(): array
	{
		$sql = "
			SELECT
				category.name AS 'name',
				COUNT(dream.id) AS 'count'
			FROM
				dj.dream dream
			INNER JOIN
				freud.dream_word_freq dwf ON dwf.dream_id = dream.id
			INNER JOIN
				freud.word_to_category w2c ON w2c.word_id = dwf.word_id
			INNER JOIN
				dj.dream_category category ON category.id = w2c.category_id
			WHERE
		  		{$this->getUserCondition()}
			GROUP BY
				category.id
			ORDER BY
				category.name ASC
			;
		";
		$dreamCountData = \Yii::$app->getDb()->createCommand($sql)->queryAll();
		$dreamCountDataByDay = [];
		foreach($dreamCountData as $row)
		{
			$dreamCountDataByDay[$row['name']] = $row['count'];
		}
		return $dreamCountDataByDay;
	}

	/**
	 * Gets the condition to use in the SQL to limit to a specific user's dreams.
	 *
	 * @return string
	 */
	protected function getUserCondition(): string
	{
		if($this->user)
		{
			return 'dream.user_id = ' . $this->user->getId();
		}
		else
		{
			return '1';
		}
	}
}