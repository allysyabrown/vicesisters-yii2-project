<?php

namespace app\repositories;

use app\models\City;
use app\models\Country;
use app\models\State;
use Yii;
use app\abstracts\Repository;
use app\models\EscortGeo;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 26.01.2015
 * Time: 11:03
 */
class EscortGeoRepository extends Repository
{
    public function findRegions($id)
    {
        $query = EscortGeo::find()
                    ->where(['escort_id' => $id]);

        return Yii::$app->dbCache->get($query, Yii::$app->params['userGeoCacheTime'])->one();
    }

    public function findRegionsFoolInfo($id)
    {
        $query = EscortGeo::find()
                    ->with(['region', 'country', 'state', 'city'])
                    ->where(['escort_id' => $id]);

        return Yii::$app->dbCache->get($query, Yii::$app->params['userGeoCacheTime'])->one();
    }

    public function getGeoParams(array $geo)
    {
        $cityID = isset($geo['city']) ? (int)$geo['city'] : 0;
        $stateID = isset($geo['state']) ? (int)$geo['state'] : 0;
        $countryID = isset($geo['country']) ? (int)$geo['country'] : 0;
        $regionID = isset($geo['region']) ? (int)$geo['region'] : 0;

        if($cityID !== 0){
            $query = City::find()
                ->with(['state', 'country', 'region'])
                ->where(['id' => $cityID]);

            $city = Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();

            if($city){
                if($city['state'])
                    $stateID = (int)$city['state']['id'];
                if($city['country'])
                    $countryID = (int)$city['country']['id'];
                if($city['region'])
                    $regionID = (int)$city['region']['id'];
            }
        }elseif($stateID !== null){
            $query = State::find()
                ->with(['country', 'region'])
                ->where(['id' => $stateID]);

            $state = Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();

            if($state){
                if($state['country'])
                    $countryID = (int)$state['country']['id'];
                if($state['region'])
                    $regionID = (int)$state['region']['id'];
            }
        }elseif($countryID){
            $query = Country::find()
                ->with('region')
                ->where(['id' => $countryID]);

            $country = Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();

            if($country){
                if($country['region'])
                    $regionID = (int)$country['region']['id'];
            }
        }

        if($cityID === 0)
            $cityID = null;
        if($stateID === 0)
            $stateID = null;
        if($countryID === 0)
            $countryID = null;
        if($regionID === 0)
            $regionID = null;

        return [
            'region_id' => $regionID,
            'country_id' => $countryID,
            'state_id' => $stateID,
            'city_id' => $cityID,
        ];
    }
} 