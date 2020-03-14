<?php


namespace app\models\search\DreamQuery;

/**
 * Class ConceptCondition
 *
 * The concept condition is only going to search for a category by the words instead of direct tagging.
 * Needs to be refactored.
 *
 * @package app\models\search\DreamQuery
 */
class ConceptCondition extends QueryCondition
{
	/**
	 * Generates the SQL to include or exclude specific dream concepts.
	 *
	 * @return string
	 */
	public function getSql(): string
	{
		$categoryId = trim($this->getValue());
		if($categoryId)
		{
			$not = "";
			if($this->getQueryOperator() == self::OPERATOR_NOT_EQUALS)
			{
				$not = "NOT ";
			}

			$categoryParam = $this->addParam($categoryId);
			return $this->getOperator() . " " . $not . "EXISTS(
	SELECT
		1
	FROM
		dj.dream_category
	INNER JOIN
		dj.word_to_category w2c ON w2c.category_id = category.id
	INNER JOIN
		freud.dream_word_freq dwf ON dwf.word_id = w2c.word_id
	WHERE
		dwf.dream_id = dream.id
		AND category.id = {$categoryParam}
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