<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "host".
 *
 * @property integer $id
 * @property string $name
 * @property string $ip
 *
 * @property EscortPhoto[] $escortPhotos
 */
class Host extends BaseModel
{
    const PRIMARY_HOST_ID = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'host';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'ip'], 'required'],
            [['ip'], 'string'],
            [['name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'ip' => 'Ip',
        ];
    }

    public function setEscortPhotos($attributes)
    {
        $records = $this->setObjectsAttributes(EscortPhoto::className(), $attributes);
        if($records !== null)
            $this->populateRelation('escortPhotos', $records);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscortPhotos()
    {
        return $this->hasMany(EscortPhoto::className(), ['host_id' => 'id']);
    }
}
