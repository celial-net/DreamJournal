<?php

namespace app\models\dj;

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

    public function getFormattedDate(): string
	{
		return Yii::$app->getFormatter()->asDate($this->dreamt_at);
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
}
