<?php


namespace app\models\search\DreamQuery;
use app\api\DreamAnalysis\AddWordRequest;
use app\api\DreamAnalysis\DreamAnalysisApi;
use app\models\freud\Word;

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
class DreamTextCondition extends QueryCondition
{
	public function getSql(): string
	{
		$word = trim($this->getValue());
		if(strlen($word))
		{
			if($word[0] == '"' || $word[0] == '\'')
			{
				//Search for an exact phrase
				$word = trim($word, '"\'');
				return $this->getOperator() . " CONCAT(dream.title, ' ', dream.description) " . $this->getQueryOperator() . $this->addParam('%' . $word . '%');
			}
			else
			{
				//Search for specific word
				$dreamWordApi = new DreamAnalysisApi();
				$addWordRequest = new AddWordRequest();
				$addWordRequest->word = $word;
				$dreamWordResponse = $dreamWordApi->addWord($addWordRequest);
				if($dreamWordResponse->isSuccess())
				{
					$word = $dreamWordResponse->word;
					$wordModel = Word::find()->word($word)->one();

					if($wordModel)
					{
						$not = "";
						if($this->getSqlQueryOperator() == self::OPERATOR_NOT_LIKE)
						{
							return $not = "NOT ";
						}

						$wordParam = $this->addParam($wordModel->id);
						return $this->getOperator() . " " . $not . "EXISTS(
	SELECT
		1
	FROM
		freud.dream_word_freq dwf
	WHERE
		dwf.word_id = {$wordParam}
		and dwf.dream_id = dream.id
)
						";
					}
				}
				else
				{
					return '';
				}
			}
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
			self::OPERATOR_LIKE,
			self::OPERATOR_NOT_LIKE
		];
	}
}