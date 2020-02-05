<?php


namespace app\models\search\DreamQuery;

/**
 * Class TypeCondition
 *
 * Searches for or excluded dream types.
 *
 * @package app\models\search\DreamQuery
 */
class TypeCondition extends QueryCondition
{
	/**
	 * Generates the SQL to include or exclude specific dream types.
	 *
	 * Examples:
	 * AND dreamType.id = 10
	 * OR dreamType.id != 5
	 * @return string
	 */
	public function getSql(): string
	{
		return $this->getOperator() . ' dreamType.id ' . $this->getSqlQueryOperator() . $this->getValue();
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