<?php

namespace app\models;

use app\components\FastData;
use Yii;
use app\forms\ProplanDurationOptionsForm;
use app\abstracts\BaseModel;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * This is the model class for table "membership".
 *
 * @property integer $id
 * @property string $name
 * @property double $price
 * @property string $members_prices
 * @property string $regions_prices
 * @property string $duration
 * @property string $duration_options
 * @property int $rate
 *
 * @property MembershipDuration[] $membershipDurations
 * @property \app\models\Escort $members
 *
 * @property string $proplanName
 * @property string $img
 * @property string $membersPrices
 * @property string $regionsPrices
 * @property string $durationOptions
 * @property string $durationName
 * @property array $membersPricesArray
 */
class Membership extends BaseModel
{
    const VIP_ACCOUNT = 1;
    const PREMIUM_ACCOUNT = 2;
    const PURPLE_PLACE = 3;
    const HOT_MESSAGE = 4;

    private static $_membershipCodes = [
        self::VIP_ACCOUNT => 'Вип',
        self::PREMIUM_ACCOUNT => 'Премиум',
        self::PURPLE_PLACE => 'Повышенный',
        self::HOT_MESSAGE => 'Горячее сообщение',
    ];

    private static $_proplansImages = [
        self::VIP_ACCOUNT => '',
        self::PREMIUM_ACCOUNT => '',
        self::PURPLE_PLACE => '/frontend/img/arrow_down.png',
    ];

    private static $_proplansClasses = [
        self::VIP_ACCOUNT => 'main_button_vip proplan-get-cost',
        self::PREMIUM_ACCOUNT => 'main_button_premium proplan-get-cost',
        self::PURPLE_PLACE => 'main_button_free_down',
    ];

    private static $_proplansTextClasses = [
        self::VIP_ACCOUNT => 'vip_text',
        self::PREMIUM_ACCOUNT => 'premium',
        self::PURPLE_PLACE => 'free',
    ];

    private static $_proplansArray;

    private $_proplanNames;

    private $_membershipPrices;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'membership';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'price', 'duration'], 'required'],
            [['price', 'rate'], 'number'],
            [['duration'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 128],
            [['members_prices', 'regions_prices', 'duration_options'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('base', 'ID'),
            'name' => Yii::t('base', 'Имя'),
            'price' => Yii::t('back', 'Базовая цена'),
            'members_prices' => Yii::t('back', 'Цены для пропланов'),
            'regions_prices' => Yii::t('back', 'Региональные цены'),
            'duration' => Yii::t('back', 'Длительность'),
            'rate' => Yii::t('back', 'Баллов к рейтингу'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->price = (int)$this->price;
        $this->duration = (int)$this->duration;
    }

    public function setMembershipDurations($attributes)
    {
        $records = $this->setObjectsAttributes(MembershipDuration::className(), $attributes);
        if($records !== null)
            $this->populateRelation('membershipDurations', $records);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembershipDurations()
    {
        return $this->hasMany(MembershipDuration::className(), ['membership_id' => 'id']);
    }

    public function setMembers($attributes)
    {
        $records = $this->setObjectsAttributes(Escort::className(), $attributes);
        if($records !== null)
            $this->populateRelation('members', $records);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        return $this->hasMany(Escort::className(), ['id' => 'escort_id'])->viaTable('escort_membership', ['membership_id' => 'id']);
    }

    public function setMembersInfo($attributes)
    {
        $records = $this->setObjectsAttributes(EscortInfo::className(), $attributes);
        if($records !== null)
            $this->populateRelation('membersInfo', $records);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembersInfo()
    {
        return $this->hasMany(EscortInfo::className(), ['escort_id' => 'escort_id'])->viaTable('escort_membership', ['membership_id' => 'id']);
    }

    /**
     * @return $this
     */
    public static function topVips()
    {
        $query = static::find()
            ->where(['id' => self::VIP_ACCOUNT]);

        return Yii::$app->dbCache->getOne($query, Yii::$app->params['findOneCacheTime']);
    }

    /**
     * @return $this
     */
    public static function topAnkets()
    {
        $query = static::find()
                    ->where(['id' => self::PURPLE_PLACE]);

        return Yii::$app->dbCache->getOne($query, Yii::$app->params['findOneCacheTime']);
    }

    /**
     * @return $this
     */
    public static function premiums()
    {
        $query = static::find()
            ->where(['id' => self::PREMIUM_ACCOUNT]);

        return Yii::$app->dbCache->getOne($query, Yii::$app->params['vipsCacheTime']);
    }

    /**
     * @return array
     */
    public function getProplanNamesList()
    {
        if($this->_proplanNames === null){
            $this->_proplanNames = [];

            foreach(self::$_membershipCodes as $code => $name){
                $this->_proplanNames[$code] = Yii::t('front', $name);
            }
        }

        return $this->_proplanNames;
    }

    public function getProplanName($id = null)
    {
        if($id === null)
            $id = $this->id;

        $names = $this->getProplanNamesList();

        return isset($names[$id]) ? $names[$id] : $this->name;
    }

    public function getImg()
    {
        return self::$_proplansImages[$this->id];
    }

    public function getClass()
    {
        return self::$_proplansClasses[$this->id];
    }

    public function getTextClass()
    {
        return self::$_proplansTextClasses[$this->id];
    }

    public function setMembersPrices($prices)
    {
        if(is_string($prices))
            $this->members_prices = $prices;
        else
            $this->members_prices = Json::encode($prices);
    }

    public function getMembersPrices()
    {
        if($this->members_prices === 'null')
            $this->members_prices = null;
        return $this->members_prices;
    }

    public function setRegionsPrices($prices)
    {
        if(is_string($prices))
            $this->regions_prices = $prices;
        else
            $this->regions_prices = Json::encode($prices);
    }

    public function getRegionsPrices()
    {
        if($this->regions_prices === 'null')
            $this->regions_prices = null;
        return $this->regions_prices;
    }

    public function setDurationOptions($options)
    {
        if(is_string($options))
            $this->duration_options = $options;
        else
            $this->duration_options = Json::encode($options);
    }

    public function getDurationOptions()
    {
        if($this->duration_options === 'null')
            $this->duration_options = null;
        return $this->duration_options;
    }

    public function getDurationName()
    {
        $items = ProplanDurationOptionsForm::getItems();
        $duration = (int)$this->duration;
        return isset($items[$duration]) ? $items[$duration] : $duration;
    }

    public function getMembersPricesArray()
    {
        if($this->_membershipPrices === null){
            $this->_membershipPrices = Json::decode($this->getMembersPrices());
        }

        return $this->_membershipPrices;
    }

    public function getVipPrice()
    {
        $result = $this->price;

        foreach($this->getMembersPricesArray() as $price){
            if($price['id'] == self::VIP_ACCOUNT){
                $result = $price['value'];
            }
        }

        return $result;
    }

    public function getPremiumPrice()
    {
        $result = $this->price;

        foreach($this->getMembersPricesArray() as $price){
            if($price['id'] == self::PREMIUM_ACCOUNT){
                $result = $price['value'];
            }
        }

        return $result;
    }

    public function getDiscountPrice($userId = null)
    {
        $price = $this->price;
        $proplanDiscount = 0;
        $regionDiscount = 0;

        if($userId === null && (Yii::$app->user->getIsGuest() === true || Yii::$app->user->getIsAdmin() === true))
            return $price;

        if($userId === null)
            $userId = Yii::$app->user->id;

        $key = FastData::USER_PROPLAN_DISCOUNT_KEY.':'.$userId.':'.$this->id;
        $discountPrice = (int)Yii::$app->cache->get($key);

        if($discountPrice === 0){
            $proplans = $this->model('Membership')->findByUserId($userId);

            if($proplans && $this->getMembersPrices()){
                $prices = Json::decode($this->getMembersPrices());

                $propnasIds = [];
                foreach($proplans as $proplan){
                    $propnasIds[] = $proplan->membership_id;
                }

                $discounts = [];

                foreach($prices as $discountPrice){
                    if(in_array($discountPrice['id'], $propnasIds)){
                        $discount = (int)$discountPrice['value'];
                        if($discount > 0)
                            $discounts[] = $discount;
                    }
                }

                $proplanDiscount = (empty($discounts)) ? $price : min($discounts);
            }

            if($this->getRegionsPrices()){
                $region = $this->model('EscortGeo')->findRegions($userId);

                if($region){
                    $regionsPrices = (array)Json::decode($this->getRegionsPrices());

                    if(!empty($regionsPrices)){
                        if($cityId = $region->getCityId()){
                            foreach($regionsPrices as $regionPrice){
                                if(isset($regionPrice['city']) && $regionPrice['city'] == $cityId){
                                    $regionDiscount = (int)$regionPrice['price'];
                                    break;
                                }
                            }
                        }elseif($stateId = $region->getStateId()){
                            foreach($regionsPrices as $regionPrice){
                                if(isset($regionPrice['state']) && $regionPrice['state'] == $stateId){
                                    $regionDiscount = (int)$regionPrice['price'];
                                    break;
                                }
                            }
                        }elseif($countryId = $region->getCountryId()){
                            foreach($regionsPrices as $regionPrice){
                                if(isset($regionPrice['country']) && $regionPrice['country'] == $countryId){
                                    $regionDiscount = (int)$regionPrice['price'];
                                    break;
                                }
                            }
                        }elseif($regionId = $region->getRegionId()){
                            foreach($regionsPrices as $regionPrice){
                                if(isset($regionPrice['region']) && $regionPrice['region'] == $regionId){
                                    $regionDiscount = (int)$regionPrice['price'];
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            if($proplanDiscount !== 0 && $regionDiscount === 0)
                $discountPrice = $proplanDiscount;
            elseif($proplanDiscount === 0 && $regionDiscount !== 0)
                $discountPrice = $regionDiscount;
            elseif($proplanDiscount !== 0 && $regionDiscount !== 0)
                $discountPrice = min([$proplanDiscount, $regionDiscount]);
            else
                $discountPrice = $price;

            Yii::$app->cache->set($key, $discountPrice, Yii::$app->params['proplanDiscountCacheTime']);
        }

        return $discountPrice;
    }

    public static function getProplansArray($id = null, $url = null)
    {
        if(self::$_proplansArray === null){
            if($url === null)
                $url = '/backend/proplan/list';

            self::$_proplansArray = [
                (object)[
                    'id' => 0,
                    'name' => '---',
                    'url' => Url::to([$url]),
                    'selected' => $id === null,
                ]
            ];

            $proplans = Yii::$app->data->getRepository('Membership')->getProplans();

            foreach($proplans as $proplan){
                self::$_proplansArray[] = (object)[
                    'id' => $proplan->id,
                    'name' => $proplan->getProplanName(),
                    'url' => Url::to([$url, 'id' => $proplan->id]),
                    'selected' => $proplan->id == $id,
                ];
            }
        }

        return self::$_proplansArray;
    }
}
