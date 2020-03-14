<?php

namespace app\models\dj;

/**
 * This is the ActiveQuery class for [[WordToCategory]].
 *
 * @see WordToCategory
 */
class WordToCategoryQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return WordToCategory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return WordToCategory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
