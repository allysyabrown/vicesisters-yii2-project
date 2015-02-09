<?php

namespace app\forms;

use Yii;
use app\abstracts\BaseForm;
use app\entities\RegionsPrices;
use app\models\Membership;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 21.01.2015
 * Time: 13:44
 *
 * @property \app\models\Membership[] $membersPricesItems
 * @property array $regionsPricesItems
 * @property array $durationOptionsItems
 */
class EditProlanForm extends BaseForm
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var float
     */
    public $price;

    /**
     * @var array
     */
    public $membersPrices;

    /**
     * @var \app\entities\RegionsPrices[]
     */
    public $regionPrices;

    /**
     * @var string
     */
    public $duration;

    /**
     * @var bool
     */
    public $allowDurationOptions = false;

    /**
     * @var array
     */
    public $durationOptions;

    /**
     * @var bool
     */
    public $allowMembersPrices = false;

    /**
     * @var bool
     */
    public $allowRegionsPrices = false;

    /**
     * @var array
     */
    public $regionsPrices;

    /**
     * @var int
     */
    public $rate;


    /**
     * @var array
     */
    private $_durationItems;

    /**
     * @var \app\models\Membership[]
     */
    private $_membersPrices;

    /**
     * @var array
     */
    private $_regionPricesItems;

    /**
     * @var array
     */
    private $_durationOptionsItems;

    public function rules()
    {
        return [
            [['name', 'price', 'duration'], 'required'],
            [['price', 'rate'], 'number'],
            [['duration'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 128],
            [['allowDurationOptions', 'durationOptions', 'allowMembersPrices', 'membersPrices', 'allowRegionsPrices', 'regionsPrices'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('base', 'ID'),
            'name' => Yii::t('base', 'Имя'),
            'price' => Yii::t('back', 'Базовая цена'),
            'members_prices' => Yii::t('back', 'Цены для пропланов'),
            'regions_prices' => Yii::t('back', 'Региональные цены'),
            'duration' => Yii::t('back', 'Длительность'),
            'allowDurationOptions' => Yii::t('back', 'Разрешить выбор длительности'),
            'durationOptions' => Yii::t('back', 'Варианты'),
            'allowProplansPrices' => Yii::t('back', 'Разрешить скидки для действующих пропланов'),
            'allowRegionsPrices' => Yii::t('back', 'Разрешить региональные цены'),
            'rate' => Yii::t('back', 'Баллов к рейтингу'),
        ];
    }

    public function setEntity(Membership $proplan)
    {
        $this->setAttributes($proplan->getAttributes());
        $this->id = $proplan->id;
        $this->price = (int)$this->price;
        $this->duration = (int)$this->duration;
    }

    public function save()
    {
        if(!$this->validate())
            return false;

        return $this->model('Membership')->editProplan($this);
    }

    public function setMembers_prices($prices)
    {
        $prices = trim($prices);
        $this->membersPrices = $prices ? Json::decode($prices) : null;
        if($this->membersPrices !== null){
            foreach($this->membersPrices as $price){
                if((int)$price['value'] > 0){
                    $this->allowMembersPrices = true;
                    break;
                }
            }
        }
    }

    public function setRegions_prices($prices)
    {
        $prices = trim($prices);

        $prices = $prices ? Json::decode($prices) : null;

        if($prices){
            foreach($prices as $price){
                $regionPrice = new RegionsPrices();
                $regionPrice->setAttributes($price);
                $this->regionsPrices[] = $regionPrice;
            }
            $this->allowRegionsPrices = true;
        }
    }

    public function setDuration_options($options)
    {
        $options = trim($options);
        $this->durationOptions = $options ? Json::decode($options) : null;
        if($this->durationOptions !== null)
            $this->allowDurationOptions = true;
    }

    public function setAllowDurationOptions($allow)
    {
        $this->allowDurationOptions = (bool)$allow;

        if($this->allowDurationOptions === false)
            $this->durationOptions = null;
    }

    public function setAllowMembersPrices($allow)
    {
        $this->allowMembersPrices = (bool)$allow;
        if($this->allowMembersPrices === false)
            $this->membersPrices = null;
    }

    public function setAllowRegionsPrices($allow)
    {
        $this->allowRegionsPrices = (bool)$allow;
        if($this->allowRegionsPrices === false)
            $this->regionsPrices = null;
    }

    /**
     * @return array
     */
    public function getDurationItems()
    {
        if($this->_durationItems === null){
            $this->_durationItems = ProplanDurationOptionsForm::getItems();
        }

        return $this->_durationItems;
    }

    /*/**
     * @return array
     */
    public function getDurationOptionsItems()
    {
        if($this->_durationOptionsItems === null){
            $this->_durationOptionsItems  = [];
            if($this->durationOptions){
                foreach($this->durationOptions as $option){
                    $form = new ProplanDurationOptionsForm();
                    $form->duration = $option['duration'];

                    $this->_durationOptionsItems[] = [
                        'duraionsForm' => $form,
                    ];
                }
            }
        }
        return $this->_durationOptionsItems;
    }

    /**
     * @return \app\models\Membership[]
     */
    public function getMembersPricesItems()
    {
        if($this->_membersPrices === null){
            $list = $this->model('Membership')->getProplansList();

            if($this->membersPrices){
                foreach($list as $item){
                    $item->price = null;

                    foreach($this->membersPrices as $price){
                        if($item->id == $price['id']){
                            $item->price = $price['value'];
                            continue;
                        }
                    }

                    $item->name = $item->getProplanName();
                    $this->_membersPrices[] = $item;
                }
            }else{
                foreach($list as $item){
                    $item->price = null;
                    $item->name = $item->getProplanName();
                    $this->_membersPrices[] = $item;
                }
            }
        }
        return $this->_membersPrices;
    }

    /**
     * @return array
     */
    public function getRegionsPricesItems()
    {
        if($this->_regionPricesItems === null){
            $this->_regionPricesItems = [];
            if($this->regionsPrices){
                foreach($this->regionsPrices as $region){
                    $form = new SearchForm();
                    $region = (array)$region;

                    $form->setRegionSettings($region);

                    $this->_regionPricesItems[] = array_merge($form->getAttributes(), [
                        'price' => $region['price'],
                        'regionsForm' => $form
                    ]);
                }
            }
        }
        return $this->_regionPricesItems;
    }

    public function setDurationOptions($options)
    {
        if($this->allowDurationOptions === true)
            $this->durationOptions = $options;
    }

    public function setMembersPrices($prices)
    {
        if($this->allowMembersPrices)
            $this->membersPrices = $prices;
    }

    public function setRegionsPrices($prices)
    {
        if($this->allowRegionsPrices)
            $this->regionsPrices = $prices;
    }
} 