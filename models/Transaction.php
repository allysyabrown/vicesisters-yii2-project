<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property string $time
 * @property int $escort_id
 * @property string $service_name
 * @property string $description
 * @property double $sum
 * @property double $escort_balance
 *
 * @property Escort $escort
 *
 * @property string $escortId
 * @property string $serviceName
 * @property string $escortBalance
 * @property string $userLoginUrl
 */
class Transaction extends BaseModel
{
    const HOT_MESSAGE_SERVICE = 'hot_message';

    public static function tableAttributes()
    {
        return [
            'id' => [
                'value' => Yii::t('back', 'ID'),
                'sort' => true,
                'search' => true,
            ],
            'time' => [
                'value' => Yii::t('back', 'Время транзакции'),
                'sort' => true,
            ],
            'escortId' => [
                'name' => 'escort_id',
                'type' => 'int',
                'value' => Yii::t('back', 'ID эскорта'),
                'sort' => true,
                'search' => true,
            ],
            'userLoginUrl' => Yii::t('base', 'Эскорт'),
            'serviceName' => [
                'name' => 'service_name',
                'value' => Yii::t('back', 'Услуга'),
                'search' => true,
            ],
            'sum' => [
                'value' => Yii::t('back', 'Сумма'),
                'sort' => true,
            ],
            'escortBalance' => [
                'value' => Yii::t('back', 'Баланс эскорта'),
                'sort' => 'escort_balance',
            ],
            'description' => Yii::t('base', 'Описание'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time', 'escort_id', 'sum', 'escort_balance'], 'required'],
            [['time'], 'safe'],
            [['escort_id'], 'integer'],
            [['sum', 'escort_balance'], 'number'],
            [['service_name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time' => 'Time',
            'escort_id' => 'Escort ID',
            'service_name' => 'Service Name',
            'description' => 'Description',
            'sum' => 'Sum',
            'escort_balance' => 'Escort Balance',
        ];
    }

    public function setEscort($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Escort(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('escort', $escortInfo);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscort()
    {
        return $this->hasOne(Escort::className(), ['id' => 'escort_id']);
    }

    public function setEscortId($id)
    {
        $this->escort_id = $id;
    }

    public function getEscortId()
    {
        return $this->escort_id;
    }

    public function setServiceName($name)
    {
        $this->service_name = $name;
    }

    public function getServiceName()
    {
        return $this->service_name;
    }

    public function setEscortBalance($sum)
    {
        $this->escort_balance = $sum;
    }

    public function getEscortBalance()
    {
        return $this->escort_balance;
    }

    public function beforeSave($insert = true)
    {
        if($this->time === null)
            $this->time = Yii::$app->local->dateTime();
        if($this->escort_id === null)
            $this->escort_id = Yii::$app->user->id;
        if($this->escort_balance === null)
            $this->escort_balance = Yii::$app->user->getBalance();

        return parent::beforeSave($insert);
    }

    public function getUserLoginUrl()
    {
        return $this->escort ? $this->escort->getLoginUrl($this->escort->getUserName()) : '';
    }
}
