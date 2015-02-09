<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 27.11.14
 * Time: 12:49
 * To change this template use File | Settings | File Templates.
 */

namespace app\abstracts;


abstract class StatsRepository extends Repository {

    /**
     * @param $escortId
     * @param null | string $startDate
     * @param null | string $endDate
     * @return mixed
     */
    public function getPeriodStats($escortId, $startDate = null, $endDate = null)
    {
        $class = $this->entity->className();
        $query = $class::find()->where(['escort_id' => $escortId]);

        if($startDate !== null)
            $query->andWhere(['>=', 'date', $startDate]);

        if($endDate !== null)
            $query->andWhere(['<=', 'date', $startDate]);

        return \Yii::$app->dbCache->get($query, \Yii::$app->params['escortStatsCacheTime'])->sum('amount');
    }

}