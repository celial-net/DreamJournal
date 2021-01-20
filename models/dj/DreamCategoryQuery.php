<?php

namespace app\models\dj;

/**
 * This is the ActiveQuery class for [[DreamCategory]].
 *
 * @see DreamCategory
 */
class DreamCategoryQuery extends \yii\db\ActiveQuery
{
    public function hidden(bool $hidden = true)
    {
        return $this->andWhere('[[hidden]]=:hidden', [':hidden' => $hidden]);
    }

    /**
     * {@inheritdoc}
     * @return DreamCategory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return DreamCategory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
