<?php


namespace app\models\search\DreamQuery;

/**
 * Class CategoryCondition
 *
 * Searches for or excluded dream categories.
 *
 * @package app\models\search\DreamQuery
 */
class CategoryCondition extends QueryCondition
{
	/**
	 * Generates the SQL to include or exclude specific dream categories.
	 *
	 * Examples:
	 * AND dreamCat.id = 10
	 * OR dreamCat.id != 5
	 * @return string
	 */
	public function getSql(): string
	{
		return $this->getOperator() . ' dreamCat.id ' . $this->getSqlQueryOperator() . ' ' . $this->addParam($this->getValue());
	}

	/**
	 * We only allow for the basic equals and not equals.
	 *
	 * @return string[]
	 */
	protected function getAllowedOperators(): array
	{
		return [
			self::OPERATOR_EQUALS,
			self::OPERATOR_NOT_EQUALS
		];
	}
}