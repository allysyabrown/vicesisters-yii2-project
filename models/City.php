<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "city".
 *
 * @property string $id
 * @property string $name
 * @property string $country_id
 * @property string $state_code
 *
 * @property Country $country
 * @property State $state
 * @property Region $region
 */
class City extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id', 'country_id'], 'integer'],
            [['state_code'], 'string', 'max' => 16],
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
            'country_id' => 'Country ID',
            'state_id' => 'State ID',
            'name' => 'Name',
        ];
    }

    public function setCountry($attributes)
    {
        $info = $this->setObjectAttributes(new Country(), $attributes);
        if($info !== null)
            $this->populateRelation('country', $info);
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    public function setState($attributes)
    {
        $info = $this->setObjectAttributes(new State(), $attributes);
        if($info !== null)
            $this->populateRelation('state', $info);
    }

    /**
     * @return State
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['code' => 'state_code']);
    }

    public function setRegion($attributes)
    {
        $info = $this->setObjectAttributes(new Region(), $attributes);
        if($info !== null)
            $this->populateRelation('region', $info);
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id'])->viaTable('country', ['id' => 'country_id']);
    }

}
