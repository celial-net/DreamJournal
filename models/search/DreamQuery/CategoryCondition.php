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
		$cat = trim($this->getValue());
		if($cat)
		{
			$not = "";
			if($this->getSqlQueryOperator() == self::OPERATOR_NOT_EQUALS)
			{
				return $not = "NOT ";
			}

			$catParam = $this->addParam($cat);
			return $this->getOperator() . " " . $not . "EXISTS(
	SELECT
		cat.id 
	FROM
		dj.dream_category cat
	INNER JOIN
		dj.dream_to_dream_category dream2cat ON dream2cat.category_id = cat.id
	WHERE
		dream2cat.dream_id = dream.id
		AND cat.id = {$catParam}
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