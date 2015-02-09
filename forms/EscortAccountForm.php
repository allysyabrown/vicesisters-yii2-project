<?php

namespace app\forms;

use app\models\EscortExtendedInfo;
use app\models\EscortInfo;
use Yii;
use app\abstracts\BaseForm;
use app\models\Account;
use app\models\Escort;
use app\entities\EscortAccountBodyParams;
use app\entities\Geo;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 06.12.2014
 * Time: 12:42
 *
 * @property \app\entities\EscortAccountBodyParams $bodyParams
 * @property \app\entities\Geo $geo
 * @property array $extendedInfo
 */
class EscortAccountForm extends BaseForm
{
    public $id;
    public $user_name;
    public $email;
    public $sex;
    public $avatar;
    public $verification_date;
    public $first_name;
    public $last_name;
    public $phone;
    public $geo;
    public $age;
    public $description;
    public $extended_info;
    public $booking;
    public $country = 0;
    public $state = 0;
    public $city = 0;
    public $height;
    public $weight;
    public $eyes;
    public $hair;
    public $breast;
    public $waist;
    public $hips;
    public $ethnicity;
    public $orientation;
    public $extended;
    public $bodyParams;
    public $webPage;
    public $language1;
    public $language2;
    public $language3;

    /**
     * @var \app\entities\EscortAccountBodyParams
     */
    private $_bodyParamsEntity;

    /**
     * @var \app\entities\Geo
     */
    private $_geoEntity;

    /**
     * @var array
     */
    private $_geography;

    /**
     * @var array
     */
    private $_languagesItems;

    /**
     * @var array
     */
    private $_countryItems;

    /**
     * @var array
     */
    private $_stateItems;

    /**
     * @var array
     */
    private $_cityItems;

    public function rules()
    {
        return [
            [['email', 'phone'], 'required', 'message' => Yii::t('error', 'Это поле не может быть пустым')],
            [['email'], 'string', 'max' => 256, 'message' => Yii::t('error', 'Это поле не может превышать {count} символов', ['count' => 256])],
            [['first_name', 'last_name', 'user_name'], 'string', 'max' => 16, 'message' => Yii::t('error', 'Это поле не может превышать {count} символов', ['count' => 16])],
            [['phone'], 'string', 'max' => 26, 'message' => Yii::t('error', 'Это поле не может превышать {count} символов', ['count' => 26])],
            [['phone'], 'match', 'pattern' => '^[a-zA-Zа-яА-Я=\|*~!@#$№:;%&?<>,.{}\[\]_"\'\\\]*', 'not' => true, 'message' => Yii::t('error', 'Номер телефона введён неправильно')],
            [['first_name', 'last_name'], 'match', 'pattern' => '^[0-9+=\|*~!@#$№:;%&\(\)/?<>,.{}\[\]_"\\\]*', 'not' => true, 'message' => Yii::t('error', 'Это поле может содержать только буквы')],
            [['email'], 'email', 'message' => Yii::t('error', 'E-mail введён неправильно')],
            [['description'], 'string', 'max' => 6400, 'message' => Yii::t('error', 'Это поле не может превышать {count} символов', ['count' => 6400])],
            [['id', 'sex', 'city_id', 'avatar', 'verification_date', 'geo', 'age', 'body_params', 'extended_info', 'booking', 'country', 'state', 'city', 'extended'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('front', 'ID'),
            'user_name' => Yii::t('base', 'Имя пользователя'),
            'email' => Yii::t('base', 'E-mail'),
            'sex' => Yii::t('base', 'Пол'),
            'avatar' => Yii::t('base', 'Аватар'),
            'first_name' => Yii::t('base', 'Имя'),
            'last_name' => Yii::t('base', 'Фамилия'),
            'phone' => Yii::t('base', 'Телефон'),
            'age' => Yii::t('base', 'Возраст'),
            'description' => Yii::t('base', 'Описание'),
            'country' => Yii::t('base', 'Страна'),
            'state' => Yii::t('base', 'Штат'),
            'city' => Yii::t('base', 'Город'),
        ];
    }

    /**
     * @param Escort $account
     */
    public function setAccount($account)
    {
        $prams = array_merge($account->getAttributes(), $account->escortInfo->getAttributes());
        $this->setAttributes($prams);
    }

    public function save()
    {
        $this->setGeo($this->getAttributes());
        $this->setBody_params($this->getAttributes());
        return $this->model('Account')->saveEscortAccount($this);
    }

    public function setGeo($geo)
    {
        $entity = $this->getGeoEntity();
        $entity->setAttributes($geo);

        $geoParams = $this->model('EscortGeo')->getGeoParams((array)$entity);

        $entity->region = $geoParams['region_id'];
        $entity->country = $geoParams['country_id'];
        $this->country = $geoParams['country_id'];
        $entity->state = $geoParams['state_id'];
        $entity->state = $geoParams['state_id'];
        $entity->city = $geoParams['city_id'];
        $this->city = $geoParams['city_id'];

        if($entity->webPage !== null)
            $this->webPage = str_replace('http://', '', $entity->webPage);
        if($entity->language1 !== null)
            $this->language1 = $entity->language1;
        if($entity->language2 !== null)
            $this->language2 = $entity->language2;
        if($entity->language1 !== null)
            $this->language3 = $entity->language3;
    }

    public function setBody_params($params)
    {
        $this->getBodyParamsEntity()->setAttributes($params);
        $params = $this->getBodyParamsEntity();

        if($params->height !== null)
            $this->height = $params->height;
        if($params->weight !== null)
            $this->weight = $params->weight;
        if($params->eyes !== null)
            $this->eyes = $params->eyes;
        if($params->hair !== null)
            $this->hair = $params->hair;
        if($params->breast !== null)
            $this->breast = $params->breast;
        if($params->waist !== null)
            $this->waist = $params->waist;
        if($params->hips !== null)
            $this->hips = $params->hips;
        if($params->ethnicity !== null)
            $this->ethnicity = $params->ethnicity;
        if($params->orientation !== null)
            $this->orientation = $params->orientation;
        if($params->bodyParams !== null)
            $this->bodyParams = $params->bodyParams;
    }

    public function setEscort_id($id)
    {
        $this->id = $id;
    }

    public function setCountry_id($id)
    {
        $this->country = $id;
        $this->getGeoEntity()->country = $id;
    }

    public function setCity_id($id)
    {
        $this->city = $id;
        $this->getGeoEntity()->city = $id;
    }

    public function setState_id($id)
    {
        $this->state = $id;
        $this->getGeoEntity()->state = $id;
    }

    public function getSexItems()
    {
        return self::getSexItemsArray();
    }

    public function getCountryItems()
    {
        if($this->_countryItems === null){
            $this->_countryItems = [0 => '---'];
            foreach($this->getGeoParams()['countries'] as $id => $name){
                $this->_countryItems[$id] = $name;
            }
        }
        return $this->_countryItems;
    }

    public function getStateItems()
    {
        if($this->_stateItems === null){
            if(empty($this->getGeoParams()['states'])){
                $this->_stateItems = [];
            }else{
                $this->_stateItems = [0 => '---'];
                foreach($this->getGeoParams()['states'] as $id => $name){
                    $this->_stateItems[$id] = $name;
                }
            }
        }
        return $this->_stateItems;
    }

    public function getCityItems()
    {
        if($this->_cityItems === null){
            if(empty($this->getGeoParams()['cities'])){
                $this->_cityItems = [];
            }else{
                $this->_cityItems = [0 => '---'];
                foreach($this->getGeoParams()['cities'] as $id => $name){
                    $this->_cityItems[$id] = $name;
                }
            }
        }
        return $this->_cityItems;
    }

    public function setExtended_info($info)
    {
        $this->extended = $info ? $this->parseStringToArray($info) : [];
    }

    public function getExtendedItems()
    {
        return self::getExtendedItemsArray();
    }

    public function getOrientationItems()
    {
        return self::getOrientationItemsArray();
    }

    public function getEthnicityItems()
    {
        return self::getEthnicityItemsArray();
    }

    public function getEyesItems()
    {
        return self::getEyesItemsArray();
    }

    public function getHairItems()
    {
        return self::getHairItemsArray();
    }

    public function getLanguageItems()
    {
        if($this->_languagesItems === null){
            $this->_languagesItems = [];

            $languages = $this->model('Language')->all();
            if($languages){
                $this->_languagesItems[0] = '-';

                foreach($languages as $lang){
                    $this->_languagesItems[$lang['id']] = $lang['name'];
                }
            }
        }

        return $this->_languagesItems;
    }

    public function getLanguage1Items()
    {
        return $this->getLanguageItems();
    }

    public function getLanguage2Items()
    {
        return $this->getLanguageItems();
    }

    public function getLanguage3Items()
    {
        return $this->getLanguageItems();
    }

    public function getBreastItems()
    {
        return self::getBreastItemsArray();
    }

    public function getExtendedInfo()
    {
        return $this->extended ? $this->parseArrayToString($this->extended) : null;
    }

    public function isOn($name)
    {
        return $this->extended && in_array($name, $this->extended);
    }

    /**
     * @return \app\entities\Geo
     */
    public function getGeoEntity()
    {
        if($this->_geoEntity === null){
            $this->_geoEntity = new Geo();
        }

        return $this->_geoEntity;
    }

    /**
     * @return \app\entities\EscortAccountBodyParams
     */
    public function getBodyParamsEntity()
    {
        if($this->_bodyParamsEntity === null){
            $this->_bodyParamsEntity = new EscortAccountBodyParams();
        }
        return $this->_bodyParamsEntity;
    }

    /**
     * @return array
     */
    public function getGeoParams()
    {
        if($this->_geography === null){
            $this->_geography = [
                'regions' => [],
                'countries' => [],
                'states' => [],
                'cities' => [],
            ];

            $countries = $this->model('Region')->getAllCountries();

            if($countries){
                foreach($countries as $country){
                    $this->_geography['countries'][$country['id']] = $country['name'];
                }
            }

            $entity = $this->getGeoEntity();

            if($entity->city){
                $city = $this->model('Region')->findCity($entity->city);
                if($city){
                    if(isset($city['state']) && $city['state']){
                        $entity->state = $city['state']['id'];
                    }
                    if(isset($city['country']) && $city['country']){
                        $entity->country = $city['country']['id'];

                        $states = $this->model('Region')->findStatesByCountryId($entity->country);
                        if($states){
                            foreach($states as $state){
                                $this->_geography['states'][$state['id']] = $state['name'];
                            }
                        }
                    }
                    if($city['region'])
                        $entity->region = $city['region']['id'];
                }
            }

            if($entity->country){
                $country = $city = $this->model('Region')->findCountry($entity->country);

                if($country){
                    if(isset($country['states']) && $country['states']){
                        foreach($country['states'] as $state){
                            $this->_geography['states'][$state['id']] = $state['name'];
                        }
                    }
                    if(isset($country['cities']) && $country['cities'] && (!isset($country['states']) || !$country['states'])){
                        foreach($country['cities'] as $city){
                            $this->_geography['cities'][$city['id']] = $city['name'];
                        }
                    }elseif($country['cities'] && $country['states']){
                        $state = $entity->state ? $this->model('Region')->findState($entity->state) : $this->model('Region')->findFirstState($entity->country);
                        if($state){
                            $entity->state = $state['id'];
                            $this->state = $state['id'];
                            if(isset($state['cities']) && $state['cities']){
                                foreach($state['cities'] as $city){
                                    $this->_geography['cities'][$city['id']] = $city['name'];
                                }
                            }
                        }
                    }
                }
            }

            if($entity->state && !$this->_geography['states']){
                $state = $this->model('Region')->findState($entity->state);

                if($state){
                    if(isset($state['country']) && $state['country'])
                        $entity->country = $state['country']['id'];
                    if(isset($state['cities']) && $state['cities'] && !$this->_geography['cities']){
                        foreach($state['cities'] as $city){
                            $this->_geography['cities'][$city['id']] = $city['name'];
                        }
                    }
                }
            }
        }

        return $this->_geography;
    }


    public static function getExtendedItemsArray()
    {
        return EscortInfo::getAllServices();
    }

    public static function getOrientationItemsArray()
    {
        return Account::getOrientationItems();
    }

    public static function getEthnicityItemsArray()
    {
        return [
            Account::ETHNICITY_ASIAN => Yii::t('account', 'Азиат'),
            Account::ETHNICITY_CAUCASIAN => Yii::t('account', 'Кавказец'),
            Account::ETHNICITY_BLACK => Yii::t('account', 'Темнокожий'),
            Account::ETHNICITY_INDIAN => Yii::t('account', 'Индиец'),
            Account::ETHNICITY_LATIN => Yii::t('account', 'Латиноамериканец'),
            Account::ETHNICITY_MIDDLE_EAST => Yii::t('account', 'Житель Ближнего Востока'),
            Account::ETHNICITY_NATIVE_AMERICAN => Yii::t('account', 'Коренной американец'),
            Account::ETHNICITY_WHITE => Yii::t('account', 'Белый'),
            Account::ETHNICITY_OTHER => Yii::t('account', 'Другое'),
        ];
    }

    public static function getEyesItemsArray()
    {
        return [
            Account::EYES_BROWN => Yii::t('account', 'Карие'),
            Account::EYES_BLUE => Yii::t('account', 'Голубые'),
            Account::EYES_GREEN => Yii::t('account', 'Зеленые'),
            Account::EYES_GREY => Yii::t('account', 'Серые'),
            Account::EYES_HAZEL => Yii::t('account', 'Каштановые'),
            Account::EYES_OTHER => Yii::t('account', 'Другое'),
        ];
    }

    public static function getHairItemsArray()
    {
        return [
            Account::HAIR_AUBURN => Yii::t('account', 'Каштановые'),
            Account::HAIR_BLONDE => Yii::t('account', 'Блонд'),
            Account::HAIR_BLACK => Yii::t('account', 'Чёрные'),
            Account::HAIR_BROWN => Yii::t('account', 'Карие'),
            Account::HAIR_GREY => Yii::t('account', 'Серые'),
            Account::HAIR_RED => Yii::t('account', 'Рыжие'),
            Account::HAIR_OTHER => Yii::t('account', 'Другое'),
        ];
    }

    public static function getSexItemsArray()
    {
        return Account::getSexItems();
    }

    public static function getBreastItemsArray()
    {
        return [
            '-' => Yii::t('profile', 'Размер груди'),
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '8+'
        ];
    }
}