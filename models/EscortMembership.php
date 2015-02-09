<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "escort_membership".
 *
 * @property string $escort_id
 * @property string $membership_id
 */
class EscortMembership extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'escort_membership';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['escort_id', 'membership_id'], 'required'],
            [['escort_id', 'membership_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'escort_id' => 'Escort ID',
            'membership_id' => 'Membership ID',
        ];
    }
}
