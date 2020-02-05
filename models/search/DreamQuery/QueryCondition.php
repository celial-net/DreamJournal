<?php

namespace app\models\search\DreamQuery;

/**
 * Class QueryCondition
 *
 * Base class for all single conditions used to build queries
 * and which can have custom values and operators set.
 *
 * @TODO: Use paramaterization.
 *
 * @package app\models\search\DreamQuery
 */
abstract class QueryCondition extends Condition
{
	/** @var int $paramCount Keeps track of all SQL parameters used. */
	private static $paramCount = 0;

	// Query operators
	const OPERATOR_EQUALS = 'is';
	const OPERATOR_NOT_EQUALS = 'is not';
	const OPERATOR_LIKE = 'contains';
	const OPERATOR_NOT_LIKE = 'does not contain';
	const OPERATORS = [self::OPERATOR_EQUALS, self::OPERATOR_NOT_EQUALS, self::OPERATOR_LIKE, self::OPERATOR_NOT_LIKE];

	/** @var string $value The value to search for. */
	private $value;

	/** @var  string $queryOperator The operator used in the query condition. */
	private $queryOperator;

	/** @var  array $params The SQL params */
	private $params = [];

	public function __construct(string $value, string $queryOperator)
	{
		$this->setValue($value);
		$this->setQueryOperator($queryOperator);
	}

	/**
	 * Sets the value.
	 *
	 * @param string $value
	 */
	public function setValue(string $value)
	{
		$this->value = $value;
	}

	/**
	 * Gets the value.
	 *
	 * @return string
	 */
	public function getValue(): string
	{
		return $this->value;
	}

	/**
	 * Sets the query operator.
	 *
	 * @param string $queryOperator
	 * @throws Exception
	 */
	public function setQueryOperator(string $queryOperator)
	{
		if(in_array($queryOperator, $this->getAllowedOperators()))
		{
			$this->queryOperator = $queryOperator;
		}
		else
		{
			throw new Exception('Invalid query operator ' . $queryOperator . '.');
		}
	}

	/**
	 * Gets the query operator.
	 *
	 * @return string
	 */
	public function getQueryOperator(): string
	{
		return $this->queryOperator;
	}

	/**
	 * Gets the query operator to be used in SQL.
	 *
	 * @return string
	 */
	public function getSqlQueryOperator(): string
	{
		$operatorMappings = [
			self::OPERATOR_EQUALS => '=',
			self::OPERATOR_NOT_EQUALS => '!=',
			self::OPERATOR_LIKE => 'LIKE',
			self::OPERATOR_NOT_LIKE => 'NOT LIKE'
		];
		return $operatorMappings[$this->getQueryOperator()];
	}

	/**
	 * Adds a SQL paramaterized value.
	 *
	 * @param string $value
	 * @return string The param name for use in SQL.
	 */
	protected function addParam(string $value): string
	{
		$param = 'dreamQueryParam_' . self::$paramCount++;
		$this->params[$param] = $value;
		return $param;
	}

	/**
	 * Gets the SQL params.
	 *
	 * @return array
	 */
	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * Gets the allowed operators for this query condition.
	 *
	 * @return string[]
	 */
	abstract protected function getAllowedOperators(): array;
}