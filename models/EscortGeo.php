<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "escort_geo".
 *
 * @property int $id
 * @property int $escort_id
 * @property int $region_id
 * @property int $country_id
 * @property int $state_id
 * @property int $city_id
 *
 * @property Escort $escort
 * @property Region $region
 * @property Country $country
 * @property State $state
 * @property City $city
 *
 * @property int $escortId
 * @property int $regionId
 * @property int $countryId
 * @property int $stateId
 * @property int $cityId
 */
class EscortGeo extends BaseModel
{
    const US_ID = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'escort_geo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['escort_id'], 'required'],
            [['escort_id', 'region_id', 'country_id', 'state_id', 'city_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'escort_id' => 'Escort ID',
            'region_id' => 'Region ID',
            'country_id' => 'Country ID',
            'state_id' => 'State ID',
            'city_id' => 'City ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscort()
    {
        return $this->hasOne(Escort::className(), ['id' => 'escort_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['id' => 'state_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }


    // Getters & setters

    public function setEscortId($id)
    {
        $this->escort_id = $id;
    }

    public function getEscortId()
    {
        return $this->escort_id;
    }

    public function setRegionId($id)
    {
        $this->region_id = $id;
    }

    public function getRegionId()
    {
        return $this->region_id;
    }

    public function setCountryId($id)
    {
        $this->country_id = $id;
    }

    public function getCountryId()
    {
        return $this->country_id;
    }

    public function setStateId($id)
    {
        $this->state_id = $id;
    }

    public function getStateId()
    {
        return $this->state_id;
    }

    public function setCityId($id)
    {
        $this->city_id = $id;
    }

    public function getCityId()
    {
        return $this->city_id;
    }

    // END Getters & setters

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }
}
