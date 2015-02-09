<?php

namespace app\abstracts;

use Yii;
use yii\base\Component;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 30.10.2014
 * Time: 21:45
 */
abstract class Repository extends Component
{
    /**
     * @var \app\abstracts\BaseModel
     */
    protected $entity;

    /**
     * @var \app\components\Data
     */
    protected $data;

    /**
     * @var \app\components\FastData
     */
    protected $fastData;

    public function init($entity = null)
    {
        $this->data = Yii::$app->data;
        $this->fastData = Yii::$app->fastData;
        if($entity !== null)
            $this->entity = $entity;
    }

    public function findCashedById($id)
    {
        return $this->entity->findCachedOne($id);
    }

    public function findById($id)
    {
        return $this->entity->findOne($id);
    }

    public function save($runValidation)
    {
        return $this->entity->save($runValidation);
    }

    /**
     * @return array|\app\abstracts\BaseModel[]
     */
    public function getAll()
    {
        return $this->entity->find()->all();
    }

    public function getCachedAll($condition = null)
    {
        return $this->entity->findCachedAll($condition);
    }

    /**
     * @return BaseModel
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param \app\abstracts\BaseModel $object
     * @param null|string $class
     */
    public function addToCache($object = null, $class = null)
    {
        if($object === null)
            $object = $this->entity;

        $object->addToCache();
    }

    /**
     * @param string|null $sql
     * @param array $params
     * @return \yii\db\Command
     */
    public function cmd($sql = null, $params = [])
    {
        return Yii::$app->db->createCommand($sql, $params);
    }

    /**
     * @param $pgArray
     * @return array
     */
    protected function getPgArray($pgArray)
    {
        if($pgArray === null || $pgArray == '{}')
            return [];

        $pgArray = str_replace(['{','}'],['',''],$pgArray);

        return explode(',', $pgArray);
    }

    protected function addToPgArray($pgArray, $element)
    {
        if(strpos($pgArray,$element) !== false)
            return false;

        $array = $this->getPgArray($pgArray);
        $array[] = $element;

        return '{'.implode(',', $array).'}';
    }

    protected function removeFromPgArray($pgArray, $element)
    {
        if(strpos($pgArray,$element) === false)
            return false;

        $array = $this->getPgArray($pgArray);
        $array = array_diff($array, [$element]);

        return '{'.implode(',', $array).'}';
    }

    public function findByAjax($params, $attributes = [], $additional = null)
    {
        if(is_array($attributes)){
            $with = isset($attributes['with']) ? $attributes['with'] : null;
            $limit = isset($attributes['limit']) ? $attributes['limit'] : null;
            $order = isset($attributes['orderBy']) ? $attributes['orderBy'] : null;
            $where = isset($attributes['where']) ? $attributes['where'] : null;
        }else{
            $with = $attributes;
            $limit = null;
            $order = null;
            $where = null;
        }

        if($limit === null){
            $limit = Yii::$app->user->isGuest || !Yii::$app->user->isAdmin ? Yii::$app->params['defaultPageCount'] : Yii::$app->params['defaultAdminPageCount'];
        }

        if($order === null){
            $order = 'id DESC';
        }

        $query = $this->entity->find();

        $params['orderBy'] = $order;
        $params['limit'] = $limit;
        $params['with'] = $with;
        $params['where'] = $where;

        return Yii::$app->ajaxData->find($query, $params, $additional)->all();
    }
}