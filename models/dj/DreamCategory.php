<?php

namespace app\models\dj;

use app\models\freud\Word;
use app\models\freud\WordQuery;
use Yii;

/**
 * This is the model class for table "dj.dream_category".
 *
 * @property int $id
 * @property string|null $name
 * @property Word[] $words
 */
class DreamCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dj.dream_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @return null|string
	 */
	public function getName()
	{
		return $this->name;
	}

    /**
     * {@inheritdoc}
     * @return DreamCategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DreamCategoryQuery(get_called_class());
    }

	/**
	 * Dream word relation.
	 *
	 * @return WordQuery
	 */
	public function getWords(): WordQuery
	{
		return $this->hasMany(Word::class, ['id' => 'word_id'])->viaTable('dj.word_to_category', ['category_id' => 'id']);
	}
}
