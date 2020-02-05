<?php

namespace app\models\search\DreamQuery;

class ListCondition extends Condition
{
	/** @var  Condition[] $conditions */
	protected $conditions;

	/**
	 * Adds a condition.
	 *
	 * @param Condition $condition
	 */
	public function addCondition(Condition $condition)
	{
		if(!$this->conditions)
		{
			//The first condition in a list doesn't get an operator
			$condition->setOperator('');
		}
		$this->conditions[] = $condition;
	}

	/**
	 * Gets all of the conditions.
	 *
	 * @return Condition[]
	 */
	public function getConditions(): array
	{
		return $this->conditions;
	}

	public function getSql(): string
	{
		if(!$this->conditions)
		{
			return '';
		}
		else
		{
			$sql = $this->getOperator() . ' (';
			foreach($this->conditions as $condition)
			{
				$sql .= $condition->getSql();
			}
			$sql .= ')';
			return $sql;
		}
	}
}