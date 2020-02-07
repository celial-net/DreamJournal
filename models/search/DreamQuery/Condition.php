<?php

namespace app\models\search\DreamQuery;

abstract class Condition
{
	// Main condition operators of OR or AND.
	const CONDITION_OR = 'or';
	const CONDITION_AND = 'and';
	const CONDITION_OPERATORS = [self::CONDITION_OR, self::CONDITION_AND];

	/** @var  string $operator 'AND'|'OR'|'' */
	private $operator = self::CONDITION_AND;

	/**
	 * Sets the operator to AND, OR, or the empty string.
	 *
	 * @param string $operator
	 * @throws Exception
	 */
	public function setOperator(string $operator)
	{
		if(in_array($operator, self::CONDITION_OPERATORS) || $operator === '')
		{
			$this->operator = $operator;
		}
		else
		{
			throw new Exception('Invalid condition operator ' . $operator . '.');
		}
	}

	/**
	 * Gets the operator.
	 *
	 * @return string
	 */
	public function getOperator(): string
	{
		return $this->operator;
	}

	/**
	 * Gets the condition(s) within a new list.
	 *
	 * @return ListCondition
	 */
	public function toList(): ListCondition
	{
		$listCondition = new ListCondition();
		$listCondition->setOperator($this->getOperator());
		$this->setOperator('');
		$listCondition->addCondition($this);
		return $listCondition;
	}

	/**
	 * Takes the condition and represents in SQL for querying.
	 *
	 * @return string
	 */
	abstract public function getSql(): string;

	/**
	 * Keeps track of all parameters used in the query.
	 *
	 * @return array
	 */
	abstract public function getParams(): array;
}