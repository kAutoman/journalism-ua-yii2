<?php

namespace common\models;

use common\components\model\ActiveRecord;

/**
 * Class UserToMemberItem
 * @package common\models
 *
 * @property integer $user_id
 * @property integer $member_item_id
 */
class UserToMemberItem extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_to_member_item}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMemberItem()
    {
        return $this->hasOne(MemberItem::class, ['id' => 'member_item_id']);
    }
}
