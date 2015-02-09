<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 01.12.14
 * Time: 14:14
 * To change this template use File | Settings | File Templates.
 */

namespace app\repositories;

use Yii;
use app\abstracts\Repository;
use app\models\Ticket;

class TicketRepository extends Repository
{
    public function getAllByUser()
    {
        $query = Ticket::find()
                    ->where(['account_id' => Yii::$app->user->id])
                    ->orderBy('date DESC');

        return Yii::$app->dbCache->getAll($query, Yii::$app->params['findAllCacheTime']);
    }

    public function changeStatus($id, $status)
    {
        $ticket = Ticket::find()->where(['id' => $id])->one();
        if(!$ticket)
            return false;

        $ticket->status = $status;
        return $ticket->update(false, ['status']);
    }
}