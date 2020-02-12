<?php


namespace app\models\search\DreamQuery;


class ConceptCondition extends QueryCondition
{
	/**
	 * Generates the SQL to include or exclude specific dream concepts.
	 *
	 * @return string
	 */
	public function getSql(): string
	{
		$conceptId = trim($this->getValue());
		if($conceptId)
		{
			$not = "";
			if($this->getQueryOperator() == self::OPERATOR_NOT_EQUALS)
			{
				$not = "NOT ";
			}

			$conceptParam = $this->addParam($conceptId);
			return $this->getOperator() . " " . $not . "EXISTS(
	SELECT
		1
	FROM
		freud.concept
	INNER JOIN
		freud.word_to_concept w2c ON w2c.concept_id = concept.id
	INNER JOIN
		freud.dream_word_freq dwf ON dwf.word_id = w2c.word_id
	WHERE
		dwf.dream_id = dream.id
		AND concept.id = {$conceptParam}
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