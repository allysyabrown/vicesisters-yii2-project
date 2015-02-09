<?php

namespace app\components;

use yii\db\Connection;
use yii\db\mssql\PDO;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 30.10.2014
 * Time: 22:10
 */
class DataBase extends Connection
{
    /**
     * @param null $sql
     * @param array $params
     * @param $fetchType
     * @return array
     */
    public function runQuery($sql = null,$params = [],$fetchType = PDO::FETCH_ASSOC)
    {
        $preparedParams = [];

        foreach($params as $key => $value){
            $preparedParams[$key] = $params[$key];
        }

        $this->pdo = $this->getMasterPdo();
        $query = $this->pdo->prepare($sql);
        $query->execute($preparedParams);

        return $query->fetchAll($fetchType);
    }
} 