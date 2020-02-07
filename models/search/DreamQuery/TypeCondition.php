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
		$type = trim($this->getValue());
		if($type)
		{
			$not = "";
			if($this->getQueryOperator() == self::OPERATOR_NOT_EQUALS)
			{
				$not = "NOT ";
			}

			$typeParam = $this->addParam($type);
			return $this->getOperator() . " " . $not . "EXISTS(
	SELECT
		1
	FROM
		dj.dream_to_dream_type dream2type
	WHERE
		dream2type.dream_id = dream.id
		AND dream2type.type_id = {$typeParam}
)
			";
		}
		else
		{
			return '';
		}
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