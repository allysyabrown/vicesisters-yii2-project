<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;
use app\components\AjaxData;
use yii\helpers\Url;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property int $user_id
 * @property string $service_name
 * @property string $payment_id
 * @property double $amount
 * @property string $user_name
 * @property string $city
 * @property string $country
 * @property string $date
 * @property integer $status
 *
 * @property int $userId
 * @property string $serviceName
 * @property string $paymentId
 * @property string $userName
 * @property string $transferId
 * @property bool $isConfirmed
 * @property string $statusName
 *
 * @property Escort $user
 */
class Payment extends BaseModel
{
    const STATUS_NEW = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_REJECTED = 2;

    private static $_statusesNames = [
        self::STATUS_NEW => 'Новый',
        self::STATUS_CONFIRMED => 'Закрыт',
    ];

    private $_statusesNamesArray;

    public static function tableAttributes()
    {
        return [
            'id' => [
                'value' => Yii::t('back', 'ID'),
                'sort' => true,
                'search' => true,
                'type' => 'int',
            ],
            'paymentId' => [
                'value' => Yii::t('back', 'ID платежа'),
                'search' => [
                    'name' => 'payment_id',
                    'cond' => 'LIKE',
                ],
            ],
            'amount' => [
                'value' => Yii::t('base', 'Сумма'),
                'sort' => true,
                'search' => true,
                'type' => 'int',
            ],
            'userId' => [
                'name' => 'user_id',
                'value' => Yii::t('back', 'ID пользователя'),
                'sort' => true,
                'search' => true,
                'type' => 'int',
            ],
            'loginUrl' => [
                'value' => Yii::t('back', 'Имя пользователя'),
                'sort' => 'user_name',
                'search' => [
                    'name' => 'user_name',
                    'cond' => 'LIKE',
                ],
            ],
            'serviceName' => [
                'name' => 'service_name',
                'value' => Yii::t('back', 'Услуга'),
                'search' => true,
            ],
            'city' => Yii::t('base', 'Город'),
            'country' => Yii::t('base', 'Страна'),
            'date' => [
                'value' => Yii::t('back', 'Время платежа'),
                'sort' => true,
            ],
            'statusName' => [
                'value' => Yii::t('base', 'Статус'),
                'sort' => 'status',
            ],
            'submitButton' => [
                'type' => AjaxData::TABLE_BUTTON,
                'value' => Yii::t('back', 'Подтвердить'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'service_name', 'payment_id', 'amount', 'date'], 'required'],
            [['user_id', 'payment_id', 'status'], 'integer'],
            [['amount'], 'number'],
            [['date'], 'safe'],
            [['service_name'], 'string', 'max' => 16],
            [['user_name'], 'string', 'max' => 256],
            [['city', 'country'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'service_name' => 'Service Name',
            'payment_id' => 'Payment ID',
            'amount' => 'Amount',
            'user_name' => 'User Name',
            'city' => 'City',
            'country' => 'Country',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    public function beforeSave($insert)
    {
        if($insert !== false && $this->getIsNewRecord()){
            if($this->user_id === null)
                $this->user_id = (int)Yii::$app->user->id;
            if($this->date === null)
                $this->date = Yii::$app->local->dateTime();
            if($this->status === null)
                $this->status = self::STATUS_NEW;
        }

        return parent::beforeSave($insert);
    }

    // Relations

    public function setUser($attributes)
    {
        $userInfo = $this->setObjectAttributes(new Escort(), $attributes);
        if($userInfo !== null)
            $this->populateRelation('user', $userInfo);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Escort::className(), ['id' => 'user_id']);
    }

    // END Relation

    // Getters & setters

    public function setUserId($id)
    {
        $this->user_id = $id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setServiceName($name)
    {
        $this->service_name = $name;
    }

    public function getServiceName()
    {
        return $this->service_name;
    }

    public function setPaymentId($id)
    {
        $this->payment_id = $id;
    }

    public function getPaymentId()
    {
        return $this->payment_id;
    }

    public function setUserName($name)
    {
        $this->user_name = $name;
    }

    public function getUserName()
    {
        return $this->user_name;
    }

    public function setTransferId($id)
    {
        $this->payment_id = $id;
    }

    public function getTransferId()
    {
        return $this->payment_id;
    }

    public function getIsConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function getIsRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function getStatusName()
    {
        $names = $this->getStatusesNames();
        return isset($names[$this->status]) ? $names[$this->status] : $names[self::STATUS_NEW];
    }

    // END Getters & setters


    public function getLoginUrl()
    {
        $user = $this->model('Account')->findById($this->getUserId());
        return $user ? $user->getLoginUrl($this->getUserName()) : $this->getUserName();
    }

    /**
     * @return array
     */
    public function getStatusesNames()
    {
        if($this->_statusesNamesArray === null){
            $this->_statusesNamesArray = [];

            foreach(self::$_statusesNames as $name => $value){
                $this->_statusesNamesArray[$name] = Yii::t('back', $value);
            }
        }

        return $this->_statusesNamesArray;
    }

    public function getSubmitButton()
    {
        $buttonConfirm = $this->button(Yii::t('back', 'Подтвердить'), [
            'class' => 'auto-ajax ask-before',
            'data' => [
                'closestdel' => 'tr',
                'ask' => Yii::t('back', 'Вы действительно хотите подтвердить платёж?'),
                'url' => Url::toRoute(['payment/closepayment', 'id' => $this->id]),
            ],
        ]);

        $buttonReject = $this->button(Yii::t('back', 'Отклонить'), [
            'class' => 'auto-ajax ask-before',
            'data' => [
                'closestdel' => 'tr',
                'ask' => Yii::t('back', 'Вы действительно хотите отклонить платёж?'),
                'url' => Url::toRoute(['payment/rejectpayment', 'id' => $this->id]),
            ],
        ]);

        if($this->getIsConfirmed())
            return $buttonReject;

        if($this->getIsRejected())
            return $buttonConfirm;

        return $buttonConfirm.$buttonReject;
    }

    public static function getPaymentListItems($status)
    {
        return [
            [
                'id' => self::STATUS_NEW,
                'name' => Yii::t('back', 'Новые'),
                'url' => Url::to(['payment/allpayments', 'status' => self::STATUS_NEW]),
                'selected' => $status !== null && (int)$status === self::STATUS_NEW,
            ],
            [
                'id' => self::STATUS_CONFIRMED,
                'name' => Yii::t('back', 'Подтверждённые'),
                'url' => Url::to(['payment/allpayments', 'status' => self::STATUS_CONFIRMED]),
                'selected' => (int)$status === self::STATUS_CONFIRMED,
            ],
            [
                'id' => self::STATUS_REJECTED,
                'name' => Yii::t('back', 'Отклонённые'),
                'url' => Url::to(['payment/allpayments', 'status' => self::STATUS_REJECTED]),
                'selected' => (int)$status === self::STATUS_REJECTED,
            ],
            [
                'id' => null,
                'name' => Yii::t('base', 'Все'),
                'url' => Url::to(['payment/allpayments']),
                'selected' => $status === null,
            ],
        ];
    }
}
