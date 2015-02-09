<?php

namespace app\forms;

use Yii;
use app\abstracts\BaseForm;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 19.01.2015
 * Time: 14:11
 */
class SearchForm extends BaseForm
{
    public $city;
    public $country;
    public $state;
    public $region;
    public $searchText;
    public $advanced = false;
    public $extended;
    public $extendedText;
    public $orientation;
    public $ethnicity;
    public $eyes;
    public $hair;
    public $breast;
    public $waist;
    public $hips;
    public $sex;
    public $age;
    public $height;
    public $weight;
    public $isEscort = true;
    public $isAgency = true;
    public $isVip = true;
    public $isPremium = true;
    public $isStandard = true;

    /**
     * @var array
     */
    private $_geo;

    private $_regions;
    private $_countries;
    private $_states;
    private $_cities;
    private $_extendedItems;
    private $_orientationItems;
    private $_ethnicityItems;
    private $_eyesItems;
    private $_hairItems;
    private $_sexItems;
    private $_extendedTextItems;

    public function rules()
    {
        return [
            [['searchText'], 'string', 'message' => Yii::t('error', 'Это текстовое поле')],
            [[
                'city', 'state', 'country', 'region', 'advanced', 'extended', 'orientation',
                'ethnicity', 'eyes', 'hair', 'sex', 'age', 'height', 'weight', 'isEscort',
                'isAgency', 'isVip', 'isPremium', 'isStandard', 'breast', 'waist', 'hips',
                'extendedText'
            ], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'searchText' => Yii::t('front', 'Имя эскорта'),
            'city' => Yii::t('base', 'Город'),
            'country' => Yii::t('base', 'Страна'),
            'state' => Yii::t('base', 'Штат'),
            'region' => Yii::t('base', 'Регион'),
            'sex' => Yii::t('account', 'Пол'),
            'isEscort' => Yii::t('front', 'Ескорты'),
            'isAgency' => Yii::t('front', 'Агенства'),
            'isVip' => Yii::t('front', 'Випы'),
            'isPremium' => Yii::t('front', 'Премиумы'),
            'isStandard' => Yii::t('front', 'Остальные'),
            'age' => Yii::t('profile', 'Возраст'),
            'ethnicity' => Yii::t('profile', 'Этнос'),
            'orientation' => Yii::t('profile', 'Ориентация'),
            'height' => Yii::t('profile', 'Рост'),
            'weight' => Yii::t('profile', 'Вес'),
            'hair' => Yii::t('profile', 'Цвет волос'),
            'eyes' => Yii::t('profile', 'Цвет глаз'),
            'breast' => Yii::t('profile', 'Размер груди'),
            'waist' => Yii::t('profile', 'Талия (см)'),
            'hips' => Yii::t('profile', 'Бёдра (см)'),
            'extended' => Yii::t('front', 'Услуги'),
        ];
    }

    // Items

    public function getRegionItems()
    {
        return $this->getItems($this->getRegions(), $this->getAttributeLabel('region'));
    }

    public function getCountryItems()
    {
        return $this->getItems($this->getCountries(), $this->getAttributeLabel('country'));
    }

    public function getStateItems()
    {
        return $this->getItems($this->getStates(), $this->getAttributeLabel('state'));
    }

    public function getCityItems()
    {
        return $this->getItems($this->getCities(), $this->getAttributeLabel('city'));
    }

    public function getExtendedItems()
    {
        if($this->_extendedItems === null){
            $this->_extendedItems = [
                '-' => $this->getAttributeLabel('extended'),
            ];
            foreach(EscortAccountForm::getExtendedItemsArray() as $id => $value){
                $this->_extendedItems[$id] = $value;
            }
        }
        return $this->_extendedItems;
    }

    public function getOrientationItems()
    {
        if($this->_orientationItems === null){
            $this->_orientationItems = [
                '-' => $this->getAttributeLabel('orientation'),
            ];
            foreach(EscortAccountForm::getOrientationItemsArray() as $id => $value){
                $this->_orientationItems[$id] = $value;
            }
        }
        return $this->_orientationItems;
    }

    public function getEthnicityItems()
    {
        if($this->_ethnicityItems === null){
            $this->_ethnicityItems = [
                '-' => $this->getAttributeLabel('ethnicity'),
            ];
            foreach(EscortAccountForm::getEthnicityItemsArray() as $id => $value){
                $this->_ethnicityItems[$id] = $value;
            }
        }
        return $this->_ethnicityItems;
    }

    public function getEyesItems()
    {
        if($this->_eyesItems === null){
            $this->_eyesItems = [
                '-' => $this->getAttributeLabel('eyes'),
            ];
            foreach(EscortAccountForm::getEyesItemsArray() as $id => $value){
                $this->_eyesItems[$id] = $value;
            }
        }
        return $this->_eyesItems;
    }

    public function getHairItems()
    {
        if($this->_hairItems === null){
            $this->_hairItems = [
                '-' => $this->getAttributeLabel('hair'),
            ];
            foreach(EscortAccountForm::getHairItemsArray() as $id => $value){
                $this->_hairItems[$id] = $value;
            }
        }
        return $this->_hairItems;
    }

    public function getSexItems()
    {
        if($this->_sexItems === null){
            $this->_sexItems = [
                '-' => $this->getAttributeLabel('sex'),
            ];
            foreach(EscortAccountForm::getSexItemsArray() as $id => $value){
                $this->_sexItems[$id] = $value;
            }
        }
        return $this->_sexItems;
    }

    public function getAgeItems()
    {
        return [
            '-' => $this->getAttributeLabel('age'),
            '18-25' => '18-25',
            '26-30' => '26-30',
            '31-35' => '31-35',
            '36-40' => '36-40',
            '40' => '40+'
        ];
    }

    public function getHeightItems()
    {
        return [
            '-' => $this->getAttributeLabel('height'),
            '150-155' => '150-155',
            '156-160' => '156-160',
            '161-165' => '161-165',
            '166-170' => '166-170',
            '171-175' => '171-175',
            '176-180' => '176-180',
            '181-185' => '181-185',
            '186-190' => '186-190',
            '191' => '190+',
        ];
    }

    public function getWeightItems()
    {
        return [
            '-' => $this->getAttributeLabel('weight'),
            '40-45' => '40-45',
            '46-50' => '46-50',
            '51-55' => '51-55',
            '56-60' => '56-60',
            '61-65' => '61-65',
            '66-70' => '66-70',
            '71-75' => '71-75',
            '76-80' => '76-80',
            '81-85' => '81-85',
            '86-90' => '86-90',
            '91' => '90+',
        ];
    }

    public function getBreastItems()
    {
        return EscortAccountForm::getBreastItemsArray();
    }

    public function getWaistItems()
    {
        return [
            '-' => $this->getAttributeLabel('waist'),
            '40-55' => '40-55',
            '56-70' => '56-70',
            '71-85' => '71-85',
            '86-100' => '86-100',
            '101-115' => '101-115',
            '115-130' => '115-130',
            '131-145' => '131-145',
            '146-160' => '146-160',
            '161' => '160+',
        ];
    }

    public function getHipsItems()
    {
        return [
            '-' => $this->getAttributeLabel('hips'),
            '50-65' => '50-65',
            '66-80' => '66-80',
            '81-95' => '81-95',
            '96-110' => '96-110',
            '111-125' => '111-125',
            '126-140' => '126-140',
            '141-155' => '141-155',
            '156-170' => '156-170',
            '171' => '170+',
        ];
    }

    public function getExtendedTextItems()
    {
        if($this->_extendedTextItems === null){
            $this->_extendedTextItems = [];
            $items = explode('::', (string)$this->extendedText);
            $items = array_filter($items);

            if(!empty($items)){
                $extendedItems = $this->getExtendedItems();

                foreach($items as $item){
                    $item = str_replace(':', '', $item);

                    $this->_extendedTextItems[] = [
                        'value' => ':'.$item.':',
                        'name' => isset($extendedItems[$item]) ? $extendedItems[$item] : $item,
                    ];
                }
            }
        }
        return $this->_extendedTextItems;
    }

    // END Items


    public function setGeo($geo)
    {
        $this->_geo = $geo;
    }

    public function setRegions($regions)
    {
        foreach($regions as $region){
            $this->_regions[$region['id']] = $region['name'];
        }
    }

    public function getRegions()
    {
        if($this->_regions === null){
            $this->_regions = [];
            $geo = $this->getGeo();
            $regions = isset($geo['regions']) ? $geo['regions'] : [];
            $this->setRegions($regions);
        }
        return $this->_regions;
    }

    public function setCountries($regions)
    {
        foreach($regions as $region){
            $this->_countries[$region['id']] = $region['name'];
        }
    }

    public function getCountries()
    {
        if($this->_countries === null){
            $this->_countries = [];
            $geo = $this->getGeo();

            $regions = isset($geo['countries']) ? $geo['countries'] : [];
            $this->setCountries($regions);
        }
        return $this->_countries;
    }

    public function setStates($regions)
    {
        foreach($regions as $region){
            $this->_states[$region['id']] = $region['name'];
        }
    }

    public function getStates()
    {
        if($this->_states === null){
            $this->_states = [];
            $geo = $this->getGeo();
            $regions = isset($geo['states']) ? $geo['states'] : [];
            $this->setStates($regions);
        }
        return $this->_states;
    }

    public function setCities($regions)
    {
        foreach($regions as $region){
            $this->_cities[$region['id']] = $region['name'];
        }
    }

    public function getCities()
    {
        if($this->_cities === null){
            $this->_cities = [];
            $geo = $this->getGeo();
            $regions = isset($geo['cities']) ? $geo['cities'] : [];
            $this->setCities($regions);
        }
        return $this->_cities;
    }

    /**
     * @return array
     */
    public function getGeo()
    {
        if($this->_geo === null){
            $array = $this->model('Region')->getRegionsArray();
            $this->_geo = $array ? $array : [];
        }
        return $this->_geo;
    }

    public function setRegionSettings(array $settings)
    {
        $region = isset($settings['region']) ? (int)$settings['region'] : 0;
        $country = isset($settings['country']) ? (int)$settings['country'] : 0;
        $state = isset($settings['state']) ? (int)$settings['state'] : 0;
        $city = isset($settings['city']) ? (int)$settings['city'] : 0;

        $regionModel = $this->model('Region');

        $this->setRegions($regionModel->getAllRegions());

        if($region === 0)
            return;
        $regionArray = $regionModel->findRegion($region);
        if(isset($regionArray['countries']) && $regionArray['countries'])
            $this->setCountries($regionArray['countries']);
        $this->region = $region;

        if($country === 0)
            return;
        $countryArray = $regionModel->findCountry($country);
        $this->country = $country;

        if(isset($countryArray['states']) && $countryArray['states'])
            $this->setStates($countryArray['states']);

        if($state !== 0){
            $stateArray = $regionModel->findState($state);
            if(isset($stateArray['cities']) && $stateArray['cities'])
                $this->setCities($stateArray['cities']);
            $this->state = $state;
        }else{
            if(isset($countryArray['cities']) && $countryArray['cities'])
                $this->setCities($countryArray['cities']);
        }

        if($city === 0)
            return;

        $this->city = $city;
        return;
    }

    private function getItems($items, $noneText = '---')
    {
        $newItems = [0 => $noneText];
        foreach($items as $key => $item){
            $newItems[$key] = $item;
        }
        return $newItems;
    }
}