<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "state".
 *
 * @property string $id
 * @property string $code
 * @property string $name
 *
 * @property \app\models\Country $country
 * @property \app\models\Region $region
 * @property \app\models\City[] $cities
 */
class State extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'state';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'code', 'name'], 'required'],
            [['id'], 'integer'],
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
        ];
    }

    public function setCities($attributes)
    {
        $records = $this->setObjectsAttributes(City::className(), $attributes);
        if($records !== null)
            $this->populateRelation('cities', $records);
    }

    public function getCities()
    {
        return $this->hasMany(City::className(), ['state_code' => 'code']);
    }

    public function setCountry($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Country(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('country', $escortInfo);
    }

    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    public function setRegion($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Region(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('region', $escortInfo);
    }

    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id'])->viaTable(Country::tableName(), ['id' => 'country_id']);
    }
}
