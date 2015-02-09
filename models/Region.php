<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "region".
 *
 * @property string $id
 * @property string $name
 *
 * @property \app\models\Country[] $countries
 */
class Region extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['name'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function setCountries($attributes)
    {
        $records = $this->setObjectsAttributes(Country::className(), $attributes);
        if($records !== null)
            $this->populateRelation('countries', $records);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(Country::className(), ['region_id' => 'id']);
    }
}
