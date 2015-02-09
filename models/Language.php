<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "language".
 *
 * @property string $id
 * @property string $code
 * @property string $name
 * @property integer $onsite
 */
class Language extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'code', 'name'], 'required'],
            [['id', 'onsite'], 'integer'],
            [['code'], 'string', 'max' => 2],
            [['name'], 'string', 'max' => 25]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'onsite' => 'Onsite',
        ];
    }
}
