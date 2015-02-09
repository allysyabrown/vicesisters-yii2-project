<?php

namespace app\commands;

use app\models\EscortInfo;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 03.02.2015
 * Time: 14:20
 */
class BodyparamsController extends Controller
{
    public $limit = 1000;
    public $offset = 0;

    public function actionIndex()
    {
        $escorts = true;
        $limit = $this->limit;
        $offset = $this->offset;
        $db = Yii::$app->getDb();

        while($escorts){
            $escorts = EscortInfo::find()
                            ->select(['id', 'body_params'])
                            ->asArray()
                            ->orderBy('id')
                            ->limit($limit)
                            ->offset($offset)
                            ->all();

            $offset += $limit;

            if($escorts){
                foreach($escorts as $escort){
                    $params = trim($escort['body_params']);
                    if(!$params)
                        continue;

                    $params = Json::decode($params);
                    $body = isset($params['bodyParams']) ? trim($params['bodyParams']) : '';
                    if(!$body)
                        continue;

                    $body = explode('-', $body);
                    $body = array_filter($body);

                    if(empty($body))
                        continue;

                    if(count($body) < 3){
                        $body = explode('/', $body[0]);
                        $body = array_filter($body);
                    }

                    if(count($body) < 3)
                        continue;

                    $bodyParams = [];
                    $bodyParams[] = array_shift($body);
                    $bodyParams[] = array_shift($body);
                    $bodyParams[] = end($body);

                    $breast = intval($bodyParams[0]);
                    $waist = intval($bodyParams[1]);
                    $hips = intval($bodyParams[2]);

                    if($waist < 50){
                        $breast *= 2.54;
                        $waist *= 2.54;
                        $hips *= 2.54;
                    }

                    $params['breast'] = round($breast/30);
                    $params['waist'] = round($waist);
                    $params['hips'] = round($hips);

                    $db->createCommand()->update(EscortInfo::tableName(), ['body_params' => Json::encode($params)], ['id' => (int)$escort['id']])->execute();
                }

                echo "Changed {$offset} records\n";
            }
        }

        echo "OK!\n";
        return true;
    }
} 