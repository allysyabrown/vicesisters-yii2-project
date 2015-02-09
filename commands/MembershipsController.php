<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 18.11.14
 * Time: 16:21
 * To change this template use File | Settings | File Templates.
 */

namespace app\commands;

use Yii;
use app\models\MembershipDuration;
use yii\console\Controller;

class MembershipsController extends Controller {

    public function actionIndex()
    {
//        $deleted = MembershipDuration::deleteAll('end_date <= \'now\'');
//        echo "{$deleted} records deleted\n";
        return true;
    }
}