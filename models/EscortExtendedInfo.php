<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "escort_extended_info".
 *
 * @property integer $id
 * @property string $info
 *
 * Attributes:
        // Service
        'models' => Yii::t('account', 'Модели'),
        'fashionModels' => Yii::t('account', 'Модели'),
        'travalingCompanion' => Yii::t('account', 'Попутчик'),
        'girlfriendExpirience' => Yii::t('account', 'Girlfriend Experience'),
        'partyServices' => Yii::t('account', 'Вечеринки'),
        'weekendCompanion' => Yii::t('account', 'Компания на выходные'),
        'holidayCompanion' => Yii::t('account', 'Компания на праздники'),
        'companionForExecutive' => Yii::t('account', 'Спутник для руководителей'),
        'businessTripCompanion' => Yii::t('account', 'Спутник в деловой поездке'),
        'ExcitingEncouters' => Yii::t('account', 'Захватывающие встречи'),
        'duo' => Yii::t('account', 'Duo (Тройка с Клиентом)'),
        'massage' => Yii::t('account', 'Массаж'),
        'stripTease' => Yii::t('account', 'Стриптиз'),

        // Sex
        'fingering' => Yii::t('account', 'Аппликатура'),
        'fisting' => Yii::t('account', 'Фистинг'),
        'goldenShower' => Yii::t('account', 'Золотой дождь'),
        'bukakee' => Yii::t('account', 'Bukakee'),
        'gothicMakeap' => Yii::t('account', 'Готический макияж'),
        'crossDressing' => Yii::t('account', 'Кроссдрессинг'),
        'blindfolds' => Yii::t('account', 'Клипсы'),
        'piercing' => Yii::t('account', 'Пирсинг'),
        'tatoo' => Yii::t('account', 'Тату'),
        'shaved' => Yii::t('account', 'Бтитый'),
        'lingerie' => Yii::t('account', 'Дамское белье'),
        'stokings' => Yii::t('account', 'Чулки'),
        'feet' => Yii::t('account', 'Ноги'),
        'shoes' => Yii::t('account', 'Обувь'),
        'smoke' => Yii::t('account', 'Дым'),

        // BDSM
        'chains' => Yii::t('account', 'Цепи'),
        'confinment' => Yii::t('account', 'Удержание/Доступ'),
        'breathePlay' => Yii::t('account', 'Игры с дыханием'),
        'dominatrix' => Yii::t('account', 'Господтсво'),
        'slavory' => Yii::t('account', 'Рабство'),
        'CTB' => Yii::t('account', 'CBT (пытки мошонки и яиц)'),
        'brests' => Yii::t('account', 'Brests/Nipp Пытки'),
        'chineseBalls' => Yii::t('account', 'Китайские шары'),
        'collari' => Yii::t('account', 'Collari'),
        'chastityDevices' => Yii::t('account', 'Кляпы'),

        // Other
        'dildos' => Yii::t('account', 'Фаллоимитаторы'),
        'toying' => Yii::t('account', 'Игрушки'),
        'strapon' => Yii::t('account', 'Страпон'),
        'plugs' => Yii::t('account', 'Вилки'),
        'gag' => Yii::t('account', 'Кляп'),
 */
class EscortExtendedInfo extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'escort_extended_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'info' => 'Info',
        ];
    }

    public function getAllItems()
    {
        return [
            // Service
            'models' => 0,
            'fashionModels' => 0,
            'travalingCompanion' => 0,
            'girlfriendExpirience' => 0,
            'partyServices' => 0,
            'weekendCompanion' => 0,
            'holidayCompanion' => 0,
            'companionForExecutive' => 0,
            'businessTripCompanion' => 0,
            'ExcitingEncouters' => 0,
            'duo' => 0,
            'massage' => 0,
            'stripTease' => 0,

            // Sex
            'fingering' => 0,
            'fisting' => 0,
            'goldenShower' => 0,
            'bukakee' => 0,
            'gothicMakeap' => 0,
            'crossDressing' => 0,
            'blindfolds' => 0,
            'piercing' => 0,
            'tatoo' => 0,
            'shaved' => 0,
            'lingerie' => 0,
            'stokings' => 0,
            'feet' => 0,
            'shoes' => 0,
            'smoke' => 0,

            // BDSM
            'chains' => 0,
            'confinment' => 0,
            'breathePlay' => 0,
            'dominatrix' => 0,
            'slavory' => 0,
            'CTB' => 0,
            'brests' => 0,
            'chineseBalls' => 0,
            'collari' => 0,
            'chastityDevices' => 0,

            // Other
            'dildos' => 0,
            'toying' => 0,
            'strapon' => 0,
            'plugs' => 0,
            'gag' => 0,
        ];
    }
}
