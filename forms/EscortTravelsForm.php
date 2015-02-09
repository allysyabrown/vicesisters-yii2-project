<?php

namespace app\forms;

use Yii;
use app\entities\Geo;
use app\models\Escort;
use app\abstracts\BaseForm;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 18.12.2014
 * Time: 11:53
 *
 * @property array $travelsListArray
 */
class EscortTravelsForm extends BaseForm
{
    public $id;
    public $escortId;
    public $travels;
    public $travelsList;
    public $listDelimiter = ':';

    private $_countries;
    private $_geo;
    private $_list;

    public function rules()
    {
        return [
            [['travelsList'], 'required', 'message' => Yii::t('error', 'Это поле не может быть пустым')],
            [['travels', 'travelsList'], 'string', 'max' => 1024, 'message' => Yii::t('error', 'Это поле не может превышать {count} символов', ['count' => 1024])],
        ];
    }

    public function attributeLabels()
    {
        return [
            'travels' => Yii::t('account', 'Путешествия'),
        ];
    }

    public function setAccount(Escort $account)
    {
        $this->setGeo($account->escortInfo->geo);
        $this->id = $account->escortInfo->id;
        $this->escortId = $account->id;
    }

    public function save()
    {
        if(!$this->validate())
            return false;

        return $this->model('EscortInfo')->setTravelList($this);
    }

    /**
     * @return array
     */
    public function getTravelsItems()
    {
        return $this->getCountries();
    }

    /**
     * @return array
     */
    public function getCountries()
    {
        if($this->_countries === null){
            $this->_countries = [];
            $countries = $this->model('Region')->getAllCountries();
            if($countries){
                foreach($countries as $country){
                    $this->_countries[$country['id']] = $country['name'];
                }
            }
        }

        return $this->_countries;
    }

    public function setGeo($geo)
    {
        $this->getGeoEntity()->setAttributes($geo);
        $this->travelsList = $this->getGeoEntity()->travels;
    }

    public function setTravelsList($list)
    {
        $this->getGeoEntity()->travels = $list;
        $this->travelsList = $list;
    }

    /**
     * @return array
     */
    public function getTravelsListArray()
    {
        if($this->_list === null){
            $this->_list = [];
            $del = $this->listDelimiter;

            $listString = $this->getGeoEntity()->travels;
            if($listString){
                $listString = str_replace($del.$del, ',', $listString);
                $listString = str_replace($del, '', $listString);
                $listArray = explode(',', $listString);

                $countries = $this->model('Region')->findCountriesByIdsList($listArray);
                if($countries)
                    $this->_list = $countries;
            }
        }

        return $this->_list;
    }

    /**
     * @return \app\entities\Geo
     */
    public function getGeoEntity()
    {
        if($this->_geo === null){
            $this->_geo = new Geo();
        }

        return $this->_geo;
    }
}