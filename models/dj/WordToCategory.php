<?php

namespace app\models\dj;

use Yii;

/**
 * This is the model class for table "dj.word_to_category".
 *
 * @property int $word_id
 * @property int $category_id
 * @property float|null $certainty
 */
class WordToCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dj.word_to_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['word_id', 'category_id'], 'required'],
            [['word_id', 'category_id'], 'integer'],
            [['certainty'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'word_id' => 'Word ID',
            'category_id' => 'Category ID',
            'certainty' => 'Certainty',
        ];
    }

    /**
     * {@inheritdoc}
     * @return WordToCategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WordToCategoryQuery(get_called_class());
    }
}
