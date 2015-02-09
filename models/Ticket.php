<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;
use app\components\AjaxData;
use app\components\FastData;
use yii\helpers\Url;

/**
 * This is the model class for table "ticket".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $date
 * @property integer $account_id
 * @property integer $status
 * @property string $name
 *
 * @property TicketMessage[] $ticketMessages
 * @property Account $account
 * @property Object $category
 *
 * @property string $text
 * @property string $loginUrl
 * @property string $ticketId
 * @property string $closeButton is HTML of "close ticket" button
 */
class Ticket extends BaseModel
{
    const STATUS_OPENED = 1;
    const STATUS_CLOSED = 0;

    const KEY_NEW_ADMIN_TICKET = 'new-admin-ticket:';
    const KEY_NEW_USER_TICKET = 'new-user-ticket:';

    public static $category = [
        [
            'id' => 1,
            'name' => 'Технический',
        ],
        [
            'id' => 2,
            'name' => 'Пожелания',
        ],
        [
            'id' => 3,
            'name' => 'Финансы',
        ],
        [
            'id' => 4,
            'name' => 'Сотрудничество',
        ],
        [
            'id' => 5,
            'name' => 'Мошенничество',
        ]
    ];

    public static function tableAttributes()
    {
        return [
            'ticketId' => [
                'value' => Yii::t('back', 'ID'),
                'search' => [
                    'name' => 'id',
                ],
                'sort' => 'id',
            ],
            'name' => Yii::t('back', 'Имя'),
            'text' => Yii::t('back', 'Текст'),
            'loginUrl' => Yii::t('back', 'Email пользователя'),
            'date' => [
                'value' => Yii::t('back', 'Дата'),
                'sort' => true,
            ],
            'closeButton' => [
                'type' => AjaxData::TABLE_BUTTON,
                'value' => Yii::t('back', 'Закрыть'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'date', 'account_id', 'status', 'name'], 'required'],
            [['category_id', 'account_id', 'status'], 'integer'],
            [['date'], 'safe'],
            [['name'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => Yii::t('base','Категория'),
            'date' => Yii::t('base','Дата'),
            'account_id' => Yii::t('base','Аккаунт'),
            'status' => Yii::t('base','Статус'),
            'name' => Yii::t('base','Заголовок'),
        ];
    }

    public function setTicketMessages($attributes)
    {
        $records = $this->setObjectsAttributes(TicketMessage::className(), $attributes);
        if($records !== null)
            $this->populateRelation('ticketMessages', $records);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketMessages()
    {
        return $this->hasMany(TicketMessage::className(), ['ticket_id' => 'id']);
    }

    public function setAccount($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Account(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('account', $escortInfo);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    /**
     * @return Object $category
     */
    public function getCategory()
    {
        return (object)self::$category[$this->category_id - 1];
    }

    public function beforeSave($insert)
    {
        if($this->date === null)
            $this->date = 'now';

        if($this->status === null)
            $this->status = self::STATUS_OPENED;

        if($this->account_id === null)
            $this->account_id = Yii::$app->user->id;

        return parent::beforeSave($insert);
    }

    public function haveNewUserMessage()
    {
        return Yii::$app->fastData->haveInString(FastData::KEY_NEW_ADMIN_TICKET, $this->id);
    }

    public function haveNewAdminMessage()
    {
        return Yii::$app->fastData->haveInString(FastData::KEY_NEW_USER_TICKET.':'.Yii::$app->user->id, $this->id);
    }

    public function getText()
    {
        return $this->ticketMessages ? $this->ticketMessages[0]->text : '';
    }

    public function getLoginUrl()
    {
        return $this->account ? $this->account->getLoginUrl() : '';
    }

    public function getTicketId()
    {
        return $this->id;
    }

    public function getCloseButton()
    {
        return $this->button(Yii::t('back', 'Закрыть'), [
            'class' => 'auto-ajax ask-before',
            'data' => [
                'closestdel' => 'tr',
                'ask' => Yii::t('back', 'Вы действительно хотите зкрыть тикет?'),
                'url' => Url::toRoute(['ticket/close', 'id' => $this->id]),
            ],
        ]);
    }
}
