<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "membership_duration".
 *
 * @property int $id
 * @property int $escort_id
 * @property int $membership_id
 * @property string $start_date
 * @property string $end_date
 *
 * @property Membership $membership
 * @property Escort $escort
 *
 * @property string $proplanName
 * @property int $escortId
 * @property string $escortName
 * @property string $escortLink
 * @property string $startDate
 * @property string $endDate
 * @property string $timeLeft
 * @property float $membershipPrice
 */
class MembershipDuration extends BaseModel
{
    public static function tableAttributes()
    {
        return [
            'id' => [
                'value' => Yii::t('back', 'ID'),
                'sort' => true,
                'search' => true,
            ],
            'proplanName' => Yii::t('back', 'Название'),
            'escortId' => [
                'value' => Yii::t('back', 'ID эскорта'),
                'name' => 'escort_id',
                'search' => true,
            ],
            'escortLink' => Yii::t('back', 'Имя эскорта'),
            'membershipPrice' => Yii::t('back', 'Стоимость услуги'),
            'startDate' => [
                'value' => Yii::t('back', 'Начало'),
                'sort' => 'start_date',
            ],
            'endDate' => [
                'value' => Yii::t('back', 'Конец'),
                'sort' => 'end_date',
            ],
            'timeLeft' => Yii::t('back', 'Осталось'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'membership_duration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['escort_id', 'membership_id', 'start_date'], 'required'],
            [['escort_id', 'membership_id'], 'integer'],
            [['start_date', 'end_date'], 'safe']
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
            'membership_id' => 'Membership ID',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
        ];
    }

    public function setMembership($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Membership(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('membership', $escortInfo);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembership()
    {
        return $this->hasOne(Membership::className(), ['id' => 'membership_id']);
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

    public function setStartDate($date)
    {
        $this->start_date = $date;
    }

    public function getStartDate()
    {
        return $this->start_date;
    }

    public function setEndDate($date)
    {
        $this->end_date = $date;
    }

    public function getEndDate()
    {
        return $this->end_date;
    }

    public function getProplanName()
    {
        return $this->membership->getProplanName();
    }

    public function setEscortId($id)
    {
        $this->escort_id = $id;
    }

    public function getEscortId()
    {
        return $this->escort_id;
    }

    public function getEscortName()
    {
        return $this->escort->getUserName();
    }

    public function setMembershipId($id)
    {
        $this->membership_id = $id;
    }

    public function getMembershipId()
    {
        return $this->membership_id;
    }

    public function getTimeLeft()
    {
        return Yii::$app->local->timeDiff($this->getEndDate());
    }

    public function getMembershipPrice()
    {
        return $this->membership->price;
    }

    public function getEscortLink()
    {
        return $this->escort ? $this->escort->getLoginUrl($this->getEscortName()) : '';
    }
}
