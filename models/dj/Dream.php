<?php

namespace app\models\dj;

use app\models\freud\Concept;
use Rhumsaa\Uuid\Uuid;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "dj.dream".
 *
 * @property string $id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property string $dreamt_at
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property DreamCategory[] $categories
 * @property DreamType[] $types
 * @property DreamComment[] $comments
 */
class Dream extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dj.dream';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'title', 'dreamt_at'], 'required'],
            [['user_id'], 'integer'],
            [['description'], 'string'],
            [['dreamt_at', 'created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 256],
            [['id'], 'unique'],
			[['id'], 'app\models\validators\UuidValidator', 'allowEmpty' => false, 'generateOnEmpty' => true],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'title' => 'Title',
            'description' => 'Description',
            'dreamt_at' => 'Date',
            'created_at' => 'Created Date',
            'updated_at' => 'Updated Date',
        ];
    }

    /**
     * {@inheritdoc}
     * @return DreamQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DreamQuery(get_called_class());
    }

	/**
	 * Gets the UUID formatted as a string.
	 *
	 * @return string
	 */
    public function getId(): ?string
	{
		if(!$this->id)
		{
			return NULL;
		}
		else
		{
			return Uuid::fromBytes($this->id)->toString();
		}
	}

	/**
	 * Sets the formatted UUID.
	 *
	 * @param null|string $id
	 */
	public function setId(?string $id)
	{
		if(!$id)
		{
			$this->id = $id;
		}
		else
		{
			$this->id = Uuid::fromString($id)->getBytes();
		}
	}

	/**
	 * @return null|string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * Dream category relation.
	 *
	 * @return DreamCategoryQuery
	 */
	public function getCategories(): DreamCategoryQuery
	{
		return $this->hasMany(DreamCategory::class, ['id' => 'category_id'])->viaTable('dream_to_dream_category', ['dream_id' => 'id']);
	}

	/**
	 * Dream type relation.
	 *
	 * @return DreamTypeQuery
	 */
	public function getTypes(): DreamTypeQuery
	{
		return $this->hasMany(DreamType::class, ['id' => 'type_id'])->viaTable('dream_to_dream_type', ['dream_id' => 'id']);
	}

	/**
	 * Dream comment relationship.
	 *
	 * @return DreamCommentQuery
	 */
	public function getComments(): DreamCommentQuery
	{
		return $this->hasMany(DreamComment::class, ['dream_id' => 'id']);
	}

    public function getFormattedDate(): string
	{
		return Yii::$app->getFormatter()->asDate(new \DateTime($this->dreamt_at));
	}

	/**
	 * Checks if a dream has a type associated.
	 *
	 * @param DreamType $type
	 * @return bool
	 */
	public function hasType(DreamType $type): bool
	{
		foreach($this->types as $myType)
		{
			if($type->id == $myType->id)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Gets all of the concepts that the dream contains ordered by relevance.
	 *
	 * @return Concept[]
	 */
	public function getConcepts(): array
	{
		$sql = "
			select
				z.*
			from
			(
				select
					concept.*,
					SUM(dwf.frequency) as 'freq'
				from
					freud.concept
				inner join
					freud.word_to_concept w2c on w2c.concept_id = concept.id
				inner join
					freud.dream_word_freq dwf on(
						dwf.dream_id = :dream_id
						and w2c.word_id = dwf.word_id
					)
				group by
					concept.id
			) z
			order by
				z.freq DESC
			;
		";

		return Concept::findBySql($sql, [':dream_id' => $this->id])->all();
	}

	/**
	 * Finds related dreams based on the concepts in this dream.
	 *
	 * @param bool $sameUser
	 * @return Dream[]
	 */
	public function findRelated(bool $sameUser = true): array
	{
		$params = [
			':dreamId' => $this->id
		];

		$userCondition = '';
		if($sameUser)
		{
			$params[':dreamUserId'] = $this->user_id;
			$userCondition = 'AND related_dream.user_id = :dreamUserId';
		}

		$sql = "
SELECT
    related_dream.*
FROM
(
	SELECT
		z.related_id AS 'related_id',
		SUM(z.weight) AS 'weight'
	FROM
	-- Dream Category Relatedness Weight
	(
		(
			SELECT
				related.id as 'related_id',
				related.title AS 'title',
				COUNT(d2c_related.category_id) * 10 AS 'weight'
			FROM
				dj.dream related
			INNER JOIN
				dj.dream_to_dream_category d2c_related ON d2c_related.dream_id = related.id
			INNER JOIN
				dj.dream_to_dream_category d2c ON d2c.dream_id = :dreamId
			WHERE
				related.id != :dreamId
				and d2c_related.category_id = d2c.category_id
			GROUP BY
				related.id
		)
		UNION
		-- Dream Type Relatedness Weight
		(
			SELECT
				related.id as 'related_id',
				related.title AS 'title',
				COUNT(d2t_related.type_id) * 10 AS 'weight'
			FROM
				dj.dream related
			INNER JOIN
				dj.dream_to_dream_type d2t_related ON d2t_related.dream_id = related.id
			INNER JOIN
				dj.dream_to_dream_type d2t ON d2t.dream_id = :dreamId
			WHERE
				related.id != :dreamId
				and d2t_related.type_id = d2t.type_id
			GROUP BY
				related.id
		)
		UNION
		(
			SELECT
				related_dream_weight.dream_id as 'related_id',
				related_dream_weight.dream_title 'title',
				(SUM(dwf.frequency * w2c.certainty) * total_words.total)
				* related_dream_weight.weight * 10 as 'weight'
			FROM
				dj.dream dream
			INNER JOIN
				freud.dream_word_freq dwf ON dwf.dream_id = dream.id
			INNER JOIN
				freud.word_to_concept w2c ON w2c.word_id = dwf.word_id
			INNER JOIN
				freud.concept concept ON concept.id = w2c.concept_id
			INNER JOIN
			(
				SELECT
					twf.dream_id AS 'dream_id',
					COUNT(twf.word_id) AS 'total'
				FROM
					freud.dream_word_freq twf
				GROUP BY
					twf.dream_id
			) total_words ON total_words.dream_id = dream.id
			INNER JOIN
			(
				SELECT
					dream.id AS 'dream_id',
					dream.title AS 'dream_title',
					w2c.concept_id AS 'concept_id',
					(SUM(dwf.frequency * w2c.certainty) * total_words.total) AS 'weight'
				FROM
					dj.dream dream
				INNER JOIN
					freud.dream_word_freq dwf ON dwf.dream_id = dream.id
				INNER JOIN
					freud.word_to_concept w2c ON w2c.word_id = dwf.word_id
				INNER JOIN
					freud.concept concept ON concept.id = w2c.concept_id
				INNER JOIN
				(
					SELECT
						twf.dream_id as 'dream_id',
						COUNT(twf.word_id) AS 'total'
					FROM
						freud.dream_word_freq twf
					GROUP BY
						twf.dream_id
				) total_words ON total_words.dream_id = dream.id
				GROUP BY
					dream.id,
					w2c.concept_id
			) related_dream_weight ON (
				related_dream_weight.concept_id = w2c.concept_id
				AND related_dream_weight.dream_id != dream.id
			)
			WHERE
				dream.id = :dreamId
			GROUP BY
				related_dream_weight.dream_id
		)
	) z
    GROUP BY
		z.related_id
) z2
INNER JOIN
	dj.dream related_dream ON related_dream.id = z2.related_id
WHERE
    weight > 0 -- must have a match (should always be the case)
	{$userCondition} -- horribly inefficient but simple, should be moved to each sub query
ORDER BY
	weight DESC
;";

		/*$paramSql = str_replace(':dreamUserId', $this->user_id, $sql);
		$paramSql = str_replace(':dreamId', "UUID_TO_BIN('" . $this->getId() . "')", $paramSql);
		print_r($paramSql);
		print "\n";*/

		return self::findBySql($sql, $params)->all();

		/*$concepts = $this->getConcepts();
		return self::findByConcepts($concepts)->all();*/
	}

	/**
	 * Finds dreams by multiple concepts.
	 * This is useful for related dreams.
	 *
	 * @param Concept[] $concepts
	 * @return DreamQuery
	 */
	public static function findByConcepts(array $concepts): DreamQuery
	{
		$conceptIds = array_column($concepts, 'id');

		//If there aren't any concepts, use -1 so that we usage is consistent
		if(!$conceptIds)
		{
			$conceptIds = [-1];
		}

		$conceptIdsSql = implode(', ', $conceptIds);

		$sql = "
			select
				z.*
			from
			(
				select
					dream.*,
					SUM(dwf.frequency) as 'freq'
				from
					dj.dream
				inner join
					freud.dream_word_freq dwf on dwf.dream_id = dream.id
				inner join
					freud.word_to_concept d2c on(
						d2c.concept_id IN($conceptIdsSql)
						and d2c.word_id = dwf.word_id
					)
				group by
					dream.id
			) z
			order by
				z.freq DESC
			;
		";

		return Dream::findBySql($sql);
	}
}
