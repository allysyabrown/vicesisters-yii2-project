<?php

namespace app\models;

use app\entities\EscortAccountBodyParams;
use app\entities\Geo;
use app\forms\EscortAccountForm;
use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "escort_info".
 *
 * @property string $id
 * @property string $escort_id
 * @property string $first_name
 * @property string $last_name
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $geo
 * @property integer $age
 * @property string $body_params
 * @property string $description
 * @property integer $extended_info
 *
 * @property Escort $escort
 * @property string $hairColor
 * @property string $orientationName
 * @property string $eyesColor
 * @property string $ethnicityName
 * @property array $languages
 * @property string $languagesString
 * @property string $breast
 * @property string $waist
 * @property string $hips
 */
class EscortInfo extends BaseModel
{
    private $_geo;

    private $_bodyParams;

    private $_languages;

    /**
     * @var EscortAccountForm
     */
    private $_form;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'escort_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['escort_id'], 'required'],
            [['escort_id', 'age', 'extended_info'], 'integer'],
            [['geo', 'body_params', 'description'], 'string'],
            [['first_name', 'last_name', 'phone'], 'string', 'max' => 64]
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
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'geo' => 'Geo',
            'age' => 'Age',
            'body_params' => 'Body Params',
            'description' => 'Description',
            'extended_info' => 'Extended Info',
        ];
    }

    public function setEscort($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Escort(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('escort', $escortInfo);
    }

    /**
     * @return Geo
     */
    public function getGeoEntity()
    {
        if($this->_geo === null){
            $this->_geo = new Geo();
            $this->_geo->setAttributes(json_decode($this->geo), true);
        }

        return $this->_geo;
    }

    /**
     * @return EscortAccountBodyParams
     */
    public function getBodyParams()
    {
        if($this->_bodyParams === null){
            $this->_bodyParams = new EscortAccountBodyParams();
            $this->_bodyParams->setAttributes(json_decode($this->body_params),true);
        }

        return $this->_bodyParams;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscort()
    {
        return $this->hasOne(Escort::className(), ['id' => 'escort_id']);
    }

    public function getFirstName()
    {
        return $this->first_name ? $this->first_name : null;
    }

    public function getLastName()
    {
        return $this->last_name ? $this->last_name : null;
    }

    public static function getAllServices()
    {
        return [
            // Service
            'models' => Yii::t('account', 'Модели'),
            'fashionModels' => Yii::t('account', 'Модели'),
            'travalingCompanion' => Yii::t('account', 'Спутник в поездке'),
            'girlfriendExpirience' => Yii::t('account', 'Девушка по вызову'),
            'partyServices' => Yii::t('account', 'Вечеринки'),
            'weekendCompanion' => Yii::t('account', 'Компания на выходные'),
            'holidayCompanion' => Yii::t('account', 'Компания на праздники'),
            'companionForExecutive' => Yii::t('account', 'Спутник для руководителей'),
            'businessTripCompanion' => Yii::t('account', 'Спутник в деловой поездке'),
            'ExcitingEncouters' => Yii::t('account', 'Секс встречи'),
            'duo' => Yii::t('account', 'Дуэт (Секс втроем с клиентом)'),
            'massage' => Yii::t('account', 'Массаж'),
            'stripTease' => Yii::t('account', 'Стриптиз'),

            // Sex
            'fingering' => Yii::t('account', 'Фингеринг'),
            'fisting' => Yii::t('account', 'Фистинг'),
            'goldenShower' => Yii::t('account', 'Золотой дождь'),
            'bukakee' => Yii::t('account', 'Буккакэ'),
            'gothicMakeap' => Yii::t('account', 'Готический макияж'),
            'crossDressing' => Yii::t('account', 'Переодевание'),
            'blindfolds' => Yii::t('account', 'Повязка на глаза'),
            'piercing' => Yii::t('account', 'Пирсинг'),
            'tatoo' => Yii::t('account', 'Тату'),
            'shaved' => Yii::t('account', 'Бритые'),
            'lingerie' => Yii::t('account', 'Белье'),
            'stokings' => Yii::t('account', 'Чулки'),
            'feet' => Yii::t('account', 'Фут-фетиш'),
            'shoes' => Yii::t('account', 'Обувь-фетиш'),
            'smoke' => Yii::t('account', 'Дым'),

            // BDSM
            'chains' => Yii::t('account', 'Цепи'),
            'confinment' => Yii::t('account', 'Удержание в клетке'),
            'breathePlay' => Yii::t('account', 'Удушение'),
            'dominatrix' => Yii::t('account', 'Доминация'),
            'slavory' => Yii::t('account', 'Рабство'),
            'CTB' => Yii::t('account', 'Пытка гениталий'),
            'brests' => Yii::t('account', 'Пытка груди/сосков'),
            'chineseBalls' => Yii::t('account', 'Вагинальные шарики'),
            'collari' => Yii::t('account', 'Ошейник'),
            'chastityDevices' => Yii::t('account', 'Пояс верности'),

            // Other
            'dildos' => Yii::t('account', 'Фаллоимитаторы'),
            'toying' => Yii::t('account', 'Игрушки'),
            'strapon' => Yii::t('account', 'Страпон'),
            'plugs' => Yii::t('account', 'Анальная пробка'),
            'Gag' => Yii::t('account', 'Кляп'),
        ];
    }

    /**
     * @return array
     */
    public function getServiceTags()
    {
        $tags = [];
        $services = $this->parseStringToArray($this->extended_info);

        foreach($services as $tag){
            if($tag === '' || $tag === null)
                continue;

            $tags[] = static::getAllServices()[trim($tag)];
        }
        return $tags;
    }

    private function parseStringToArray($string)
    {
        $array = explode(',', str_replace('{', '', str_replace('}', '', $string)));
        foreach($array as $num => $val){
            $array[$num] = trim(str_replace('"', '', $val));
        }
        return $array;
    }

    /**
     * @return string
     */
    public function getWebPage()
    {
        return urlencode(str_replace('index.php', '', $this->getGeoEntity()->webPage));
    }

    /**
     * @return string
     */
    public function getHair()
    {
        return $this->getBodyParams()->hair;
    }

    /**
     * @return string
     */
    public function getOrientation()
    {
        $orientation = (int)$this->getBodyParams()->orientation;
        if($orientation === 0)
            return null;

        $items = Account::getOrientationItems();

        return isset($items[$orientation]) ? $items[$orientation] : null;
    }

    /**
     * @return string
     */
    public function getEyes()
    {
        return $this->getBodyParams()->eyes;
    }

    /**
     * @return string
     */
    public function getEthnicity()
    {
        return $this->getBodyParams()->ethnicity;
    }

    /**
     * @return string
     */
    public function getHeight()
    {
        return $this->getBodyParams()->height;
    }

    /**
     * @return string
     */
    public function getWeight()
    {
        return $this->getBodyParams()->weight;
    }

    /**
     * @return string
     */
    public function getParams()
    {
        return $this->getBodyParams()->bodyParams;
    }

    public function getHairColor()
    {
        $items = $this->getForm()->getHairItems();
        return isset($items[$this->getHair()]) ? $items[$this->getHair()] : null;
    }

    public function getOrientationName()
    {
        return $this->getOrientation();
    }

    public function getEyesColor()
    {
        $items = $this->getForm()->getEyesItems();
        return isset($items[$this->getEyes()]) ? $items[$this->getEyes()] : null;
    }

    public function getEthnicityName()
    {
        $items = $this->getForm()->getEthnicityItems();
        return isset($items[$this->getEthnicity()]) ? $items[$this->getEthnicity()] : null;
    }

    public function setBreast($breast)
    {
        $this->getBodyParams()->breast = $breast;
    }

    public function getBreast()
    {
        return $this->getBodyParams()->breast;
    }

    public function setWaist($waist)
    {
        $this->getBodyParams()->waist = $waist;
    }

    public function getWaist()
    {
        return $this->getBodyParams()->waist;
    }

    public function setHips($hips)
    {
        $this->getBodyParams()->hips = $hips;
    }

    public function getHips()
    {
        return $this->getBodyParams()->hips;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        if($this->_languages === null){
            $this->_languages = [];

            $geo = $this->getGeoEntity();

            if($geo->language1){
                $lang = $this->model('Language')->findById($geo->language1);
                if($lang){
                    $this->_languages[$lang['id']] = $lang['name'];
                }
            }
            if($geo->language2){
                $lang = $this->model('Language')->findById($geo->language2);
                if($lang){
                    $this->_languages[$lang['id']] = $lang['name'];
                }
            }
            if($geo->language3){
                $lang = $this->model('Language')->findById($geo->language3);
                if($lang){
                    $this->_languages[$lang['id']] = $lang['name'];
                }
            }
        }

        return $this->_languages;
    }

    public function getLanguagesString()
    {
        return implode(', ', $this->getLanguages());
    }

    /**
     * @return EscortAccountForm
     */
    public function getForm()
    {
        if($this->_form === null){
            $this->_form = new EscortAccountForm();
        }

        return $this->_form;
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }
}
