<?php

namespace app\models\dj;

/**
 * This is the ActiveQuery class for [[AccountSettings]].
 *
 * @see AccountSettings
 */
class AccountSettingsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AccountSettings[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AccountSettings|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
