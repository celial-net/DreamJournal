<?php


namespace app\models\search\DreamQuery;


use app\models\dj\Dream;
use app\utilities\SqlFormatter;

class DreamQuery
{
	/** @var  Condition|null $condition */
	protected $condition;

	/**
	 * Adds a condition.
	 *
	 * @param Condition $condition
	 */
	public function addCondition(Condition $condition)
	{
		if(!$this->getCondition())
		{
			$this->setCondition($condition);
		}
		else if($this->getCondition() instanceof ListCondition)
		{
			$this->getCondition()->addCondition($condition);
		}
		else
		{
			$listCondition = new ListCondition();
			$listCondition->addCondition($this->getCondition());
			$listCondition->addCondition($condition);
			$this->setCondition($listCondition);
		}
	}

	/**
	 * Sets/overwrites the condition.
	 *
	 * @param Condition $condition
	 */
	public function setCondition(Condition $condition)
	{
		$this->condition = $condition;
	}

	/**
	 * Gets the condition.
	 *
	 * @return Condition|null
	 */
	public function getCondition(): ?Condition
	{
		return $this->condition;
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
			print SqlFormatter::format($sql);
			print_r($this->condition->getParams());
			print "</pre>";
			return Dream::findBySql($sql, $this->condition->getParams())->all();
		}
		else
		{
			return [];
		}
	}
}