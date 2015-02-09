<?php

namespace app\components;

use Yii;
use yii\base\Component;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 30.10.2014
 * Time: 21:34
 */
class Data extends Component
{
    /**
     * @var \app\components\FastData;
     */
    private $fastData;

    /**
     * @var \app\components\Search
     */
    private $searchData;

    public function init()
    {
        $this->fastData = Yii::$app->fastData;
        $this->searchData = Yii::$app->search;
    }

    /**
     * @param $modelName
     * @return null|\app\abstracts\Repository
     */
    public function getRepository($modelName)
    {
        $repositoryClass = '\\app\\repositories\\'.$modelName.'Repository';
        if(class_exists($repositoryClass)){
            $repository = new $repositoryClass();
            $modelClass = '\\app\\models\\'.$modelName;
            $model = null;
            if(class_exists($modelClass))
                $model = new $modelClass();

            $repository->init($model);

            return $repository;
        }
        else
            return null;
    }

    /**
     * @return array
     *
     * Возвращает массив айдишников активных юзеров (теоретически)
     */
    public function getActiveUsers()
    {
        return $this->fastData->getActiveUsers();
    }
}