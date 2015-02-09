<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "country".
 *
 * @property string $id
 * @property string $code
 * @property string $name
 * @property string $region_id
 * @property integer $pref
 * @property integer $sms
 *
 * @property \app\models\Region $region
 * @property \app\models\State[] $states
 * @property \app\models\City[] $cities
 */
class Country extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'code', 'name'], 'required'],
            [['id', 'region_id', 'pref', 'sms'], 'integer'],
            [['code'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 128]
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
            'region_id' => 'Region ID',
            'pref' => 'Pref',
            'sms' => 'Sms',
        ];
    }

    public function setStates($attributes)
    {
        $records = $this->setObjectsAttributes(State::className(), $attributes);
        if($records !== null)
            $this->populateRelation('states', $records);
    }

    public function getStates()
    {
        return $this->hasMany(State::className(), ['country_id' => 'id']);
    }

    public function setCities($attributes)
    {
        $records = $this->setObjectsAttributes(City::className(), $attributes);

        if($records !== null)
            $this->populateRelation('cities', $records);
    }

    public function getCities()
    {
        return $this->hasMany(City::className(), ['country_id' => 'id']);
    }

    public function setRegion($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Region(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('region', $escortInfo);
    }

    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }
}
