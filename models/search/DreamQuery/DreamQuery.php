<?php


namespace app\models\search\DreamQuery;


use app\models\dj\Dream;

class DreamQuery
{
	/** @var  ListCondition|null $condition */
	protected $condition;

	/**
	 * Adds conditions.
	 *
	 * @param Condition $condition
	 */
	public function addCondition(Condition $condition)
	{
		if(!$this->condition)
		{
			$listCondition = new ListCondition();
			$listCondition->addCondition($condition);
			$this->condition = $listCondition;
		}
		else
		{
			$this->condition->addCondition($condition);
		}
	}

	/**
	 * Queries for dreams.
	 *
	 * @return Dream[]
	 */
	public function query(): array
	{
		$this->condition->setOperator('');
		$conditionSql  = $this->condition->getSql();

		if($conditionSql)
		{
			$sql = "
				SELECT
					dream.*
				FROM
					dj.dream
				WHERE
					{$conditionSql}
			";
			print "<pre>";
			print $sql;
			print "</pre>";
			return Dream::findBySql($sql, $this->condition->getParams())->all();
		}
		else
		{
			return [];
		}
	}
}