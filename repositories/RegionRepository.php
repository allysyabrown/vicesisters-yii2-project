<?php

namespace app\repositories;

use Yii;
use app\components\FastData;
use app\models\City;
use app\models\Country;
use app\models\Region;
use app\models\State;
use app\abstracts\Repository;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 20.11.2014
 * Time: 13:05
 */
class RegionRepository extends Repository
{
    private $_regionsArray;

    public function findRegion($id)
    {
        $id = (int)$id;
        if(!$id)
            return null;

        $query = Region::find()
                    ->where(['id' => $id])
                    ->with('countries');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();
    }

    public function findCountry($id)
    {
        $query = Country::find()
                    ->where(['id' => $id])
                    ->with(['states', 'cities']);

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();
    }

    public function findCity($id)
    {
        $query = City::find()
                    ->where(['id' => $id])
                    ->with(['state', 'country', 'region']);

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();
    }

    public function findCityById($id)
    {
        $query = City::find()
            ->where(['id' => $id]);

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();
    }

    public function findState($id)
    {
        $id = (int)$id;
        if(!$id)
            return null;

        $query = State::find()
                    ->where(['id' => $id])
                    ->with(['country', 'cities']);

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();
    }

    public function findCountryWithStates($id)
    {
        $id = (int)$id;
        if(!$id)
            return null;

        $query = Country::find()
            ->where(['id' => $id])
            ->with('states');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();
    }

    public function findCountryWithCities($id)
    {
        $id = (int)$id;
        if(!$id)
            return null;

        $query = Country::find()
            ->where(['id' => $id])
            ->with('cities');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();
    }

    public function getAllRegions()
    {
        $query = Region::find()
                    ->orderBy('name');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->all();
    }

    public function getAllCountries()
    {
        $query = Country::find()
                    ->orderBy('name');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->all();
    }

    public function getAllStates()
    {
        $query = State::find()
                    ->orderBy('name');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->all();
    }

    public function getAllCities()
    {
        $query = City::find()
            ->orderBy('name');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->all();
    }

    public function findStatesByCountryId($countryId)
    {
        $query = State::find()
                    ->where(['country_id' => $countryId])
                    ->orderBy('name');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->all();
    }

    public function firstCountry()
    {
        $query = Country::find();

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();
    }

    public function findFirstState($countryId)
    {
        $query = State::find()
                    ->where(['country_id' => $countryId])
                    ->with('cities');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();
    }

    public function findCountriesByIdsList(array $idsList)
    {
        $query = Country::find()
                    ->where(['id' => $idsList])
                    ->orderBy('name');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->all();
    }

    public function getRegionsArray()
    {
        if($this->_regionsArray !== null)
            return $this->_regionsArray;

        $id = Yii::$app->user->isGuest ? Yii::$app->session->getId() : Yii::$app->user->id;

        if($id){
            $key = FastData::USER_REGION_SETTINGS_KEY.':'.$id;
            $params = Json::decode(Yii::$app->fastData->get($key));
        }else{
            $params = [];
            $key = null;
        }

        if(!$id || !$params){
            $settings = Yii::$app->session->getSearchSettings();

            $regions = $this->getAllRegions();
            $countries = [];
            $states = [];
            $cities = [];

            if($settings->region){
                $region = $this->findRegion($settings->region);
                if($region && $region['countries'])
                    $countries = $region['countries'];
            }

            if($settings->country){
                $country = $this->findCountry($settings->country);

                if($country){
                    if($country['states'])
                        $states = $country['states'];
                    if($country['cities'])
                        $cities = $country['cities'];
                }
            }

            if($settings->state){
                $state = $this->findState($settings->state);
                if($state && $state['cities'])
                    $cities = $state['cities'];
            }

            $params = [
                'regions' => $regions,
                'countries' => $countries,
                'states' => $states,
                'cities' => $cities,
            ];

            if($id){
                Yii::$app->fastData->set($key, Json::encode($params));
                Yii::$app->fastData->expire($key, Yii::$app->params['userSearchSettingsCacheTime']);
            }
        }

        $this->_regionsArray = $params;
        return $this->_regionsArray;
    }
}