<?php


namespace app\models\search\DreamQuery;

/**
 * Class BodyCondition
 *
 * Searches for dream contents which either contain or do not contain specific words.
 * Words are split at spaces, sent to Python for analysis, and only dreams
 * which contain all or do not contain all words will be included.
 *
 * For exact text searching, double quotes can be used.
 *
 * @package app\models\search\DreamQuery
 */
class BodyCondition extends QueryCondition
{
	public function getSql(): string
	{
		//Fairly complicated logic here...
		return '';
		//return $this->getOperator() . ' dreamCat.id ' . $this->getSqlQueryOperator() . ' ' . $this->addParam($this->getValue());
	}

	/**
	 * We only allow for the basic equals and not equals.
	 *
	 * @return string[]
	 */
	protected function getAllowedOperators(): array
	{
		return [
			self::OPERATOR_LIKE,
			self::OPERATOR_NOT_LIKE
		];
	}
}