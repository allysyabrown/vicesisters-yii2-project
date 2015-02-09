<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\abstracts\Notification;
use app\models\Account;
use app\models\Escort;
use app\models\User;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 20.12.2014
 * Time: 12:22
 */
class AnswersController extends Controller
{
    private static $_userIds = [
        1533, 1529, 1534, 1537, 1540, 1575, 1616, 1641, 1667, 1685, 71315, 71316, 71325, 71360, 71362, 71363, 71364
    ];

    public function actionCreate($userId, $type = null)
    {
        if($type === null)
            $type = Notification::ANSWER_TYPE_LIKE;

        switch($type){
            case Notification::ANSWER_TYPE_NEW_RECORD:
                $this->createRecord($userId);
                break;
            case Notification::ANSWER_TYPE_FAV:
                $this->createFav($userId);
                break;
            default:
                $this->createLike($userId);
                break;
        }

        echo "New answer {$type} created!";
    }

    private function createLike($userId)
    {
        $user = $this->getRandomUser();

        $content = [
            'id' => rand(236, 800016),
            'type' => Notification::LIKE_TYPE_PHOTO,
            'user_id' => $user->id,
            'user_role' => $user->role,
        ];

        Notification::addAnswerLike($userId, $content);
    }

    private function createRecord($userId)
    {
        $user = $this->getRandomUser();

        $content = [
            'id' => rand(236, 800016),
            'type' => Notification::RECORD_TYPE_FEED,
            'user_id' => $user->id,
            'user_role' => $user->role,
        ];

        Notification::addAnswerNewRecord($userId, $content);
    }

    private function createFav($userId)
    {
        $user = $this->getRandomUser();

        $content = [
            'id' => rand(236, 800016),
            'user_id' => $user->id,
            'user_role' => $user->role,
        ];

        Notification::addAnswerFavorite($userId, $content);
    }

    /**
     * @return null|\app\models\Escort|\app\models\User
     */
    private function getRandomUser()
    {
        $id = self::$_userIds[rand(0, count(self::$_userIds)-1)];
        $account = Account::find()
            ->where(['id' => $id])
            ->one();

        if(!$account)
            return null;

        $role = trim($account->role);

        if($role == Account::ROLE_ESCORT){
            $user = Escort::find()
                        ->with('escortInfo')
                        ->where(['id' => $id])
                        ->one();
        }else{
            $user = User::find()
                        ->where(['id' => $id])
                        ->one();
        }

        if(!$user)
            return null;

        return $user;
    }

    private function show($something, $dump = false)
    {
        if($dump)
            var_dump($something);
        else
            print_r($something);

        echo "\r\n";
        exit();
    }
}