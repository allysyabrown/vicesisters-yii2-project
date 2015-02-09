<?php

namespace app\models;

use app\abstracts\BaseModel;
use Yii;

/**
 * This is the model class for table "stats_escort_online".
 *
 * @property string $id
 * @property string $date
 * @property integer $amount
 * @property string $escort_id
 *
 * @property Escort $escort
 */
class StatsEscortOnline extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stats_escort_online';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'amount'], 'required'],
            [['date'], 'safe'],
            [['amount', 'escort_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'amount' => 'Amount',
            'escort_id' => 'Escort ID',
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
}
