<?php

namespace app\commands;

use app\models\City;
use app\models\Country;
use app\models\Escort;
use app\models\EscortGeo;
use app\models\EscortInfo;
use app\models\State;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 26.01.2015
 * Time: 9:44
 */
class EscortgeoController extends Controller
{
    private $limit = 5000;

    public function actionIndex()
    {
        $this->actionNocity();
        $this->city();

        echo 'OK';
        return true;
    }

    private function actionNocity()
    {
        $limit = $this->limit;
        $offset = 0;
        $db = Yii::$app->getDb();

        $escorts = true;

        while($escorts){
            $escorts = Escort::find()
                            ->select(['id'])
                            ->where(['city_id' => NULL])
                            ->limit($limit)
                            ->offset($offset)
                            ->all();

            $offset += $limit;

            if($escorts){
                $ids = [];

                foreach($escorts as $escort){
                    $ids[] = $escort->id;
                }

                $infos = EscortInfo::find()
                            ->select(['geo', 'escort_id'])
                            ->where(['escort_id' => $ids])
                            ->all();

                if($infos){
                    $columns = [];

                    foreach($infos as $info){
                        $geo = Json::decode($info->geo);
                        if(!$geo)
                            $geo = [];

                        $cityID = isset($geo['city']) ? (int)$geo['city'] : 0;
                        $stateID = isset($geo['state']) ? (int)$geo['state'] : 0;
                        $countryID = isset($geo['country']) ? (int)$geo['country'] : 0;
                        $regionID = isset($geo['region']) ? (int)$geo['region'] : 0;

                        if($cityID !== 0){
                            $city = City::find()
                                        ->with(['state', 'country', 'region'])
                                        ->where(['id' => $cityID])
                                        ->one();

                            if($city){
                                if($city->state)
                                    $stateID = (int)$city->state->id;
                                if($city->country)
                                    $countryID = (int)$city->country->id;
                                if($city->region)
                                    $regionID = (int)$city->region->id;
                            }
                        }elseif($stateID !== null){
                            $state = State::find()
                                        ->with(['country', 'region'])
                                        ->where(['id' => $stateID])
                                        ->one();

                            if($state){
                                if($state->country)
                                    $countryID = (int)$state->country->id;
                                if($state->region)
                                    $regionID = (int)$state->region->id;
                            }
                        }elseif($countryID){
                            $country = Country::find()
                                            ->with('region')
                                            ->where(['id' => $countryID])
                                            ->one();

                            if($country){
                                if($country->region)
                                    $regionID = (int)$country->region->id;
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

                        $columns[] = [
                            $info->escort_id,
                            $regionID,
                            $countryID,
                            $stateID,
                            $cityID,
                        ];
                    }

                    $db->createCommand()->batchInsert(EscortGeo::tableName(), ['escort_id', 'region_id', 'country_id', 'state_id', 'city_id'], $columns)->execute();
                }
            }
        }
    }

    private function city()
    {
        $limit = $this->limit;
        $offset = 0;
        $db = Yii::$app->getDb();

        $escorts = true;

        while($escorts){
            $escorts = Escort::find()
                            ->select(['id', 'city_id'])
                            ->where(['IS NOT', 'city_id', NULL])
                            ->limit($limit)
                            ->offset($offset)
                            ->all();

            $offset += $limit;

            if($escorts){
                $columns = [];

                foreach($escorts as $escort){
                    $cityID = (int)$escort->city_id;
                    $stateID = 0;
                    $countryID = 0;
                    $regionID = 0;

                    if($cityID !== 0){
                        $city = City::find()
                            ->with(['state', 'country', 'region'])
                            ->where(['id' => $cityID])
                            ->one();

                        if($city){
                            if($city->state)
                                $stateID = (int)$city->state->id;
                            if($city->country)
                                $countryID = (int)$city->country->id;
                            if($city->region)
                                $regionID = (int)$city->region->id;
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

                    $columns[] = [
                        $escort->id,
                        $regionID,
                        $countryID,
                        $stateID,
                        $cityID,
                    ];
                }

                $db->createCommand()->batchInsert(EscortGeo::tableName(), ['escort_id', 'region_id', 'country_id', 'state_id', 'city_id'], $columns)->execute();
            }
        }
    }
} 