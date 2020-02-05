<?php

namespace app\models\search\DreamQuery;

abstract class Condition
{
	// Main condition operators of OR or AND.
	const CONDITION_OR = 'or';
	const CONDITION_AND = 'and';
	const CONDITION_OPERATORS = [self::CONDITION_OR, self::CONDITION_AND];

	/** @var  string $operator 'AND'|'OR'|'' */
	private $operator = '';

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
	 * Takes the condition and represents in SQL for querying.
	 *
	 * @return string
	 */
	abstract public function getSql(): string;
}