<?php
/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 13:10
 */

namespace app\commands;

use app\commands\DataTransfer\Accounts;
use app\models\EscortInfo;
use app\models\EscortPhoto;
use Yii;
use app\commands\DataTransfer\Photos;
use app\models\Escort;
use app\models\Membership;
use app\abstracts\UserEntity;
use app\models\Account;
use yii\base\Exception;
use yii\console\Controller;

class TransferController extends Controller
{
    private $limit = 1000;

    public function actionIndex()
    {
        return false;
    }

    public function actionEscorts()
    {
        $this->transferEscorts();
        return false;
    }

    public function actionTopvips()
    {
        $this->transferTopvips();
        return true;
    }

    public function actionVips()
    {
        $this->transferVips();
        return true;
    }

    public function actionTopankets()
    {
        $this->transferTopAnkets();
        return true;
    }

    public function actionRegions()
    {
        $this->transferRegions();
        $this->transferCountries();
        $this->transferStates();
        $this->transferCities();

        return true;
    }

    public function actionEscortinfo()
    {
        $escorts = Accounts::find()->with('passport')->all();
        $db = Yii::$app->getDb();

        foreach($escorts as $escort){
            if(!$escort->passport)
                continue;

            $escortInfoInsertData = [];

            $escortInfoInsertData[] = [
                $escort->id,
                $escort->passport->first_name,
                $escort->passport->last_name,
                $escort->phone,
                json_encode([
                    'language' => $escort->language,
                    'city' => $escort->city,
                    'region' => $escort->state,
                    'country' => $escort->country,
                    'address' => $escort->passport->address,
                ]),
                $escort->age,
                json_encode([
                    'height' => $escort->height,
                    'weight' => $escort->weight,
                    'eyes' => $escort->eyes,
                    'hair' => $escort->hair,
                    'ethnicity' => $escort->ethnicity,
                    'orientation' => $escort->orientation,
                ]),
                $escort->booking == 'Y' ? 1 : 0,
                $escort->description ? trim(str_replace('\'', '"', $escort->description)) : NULL,
            ];

            $db->createCommand()->batchInsert('escort_info', ['escort_id', 'first_name', 'last_name', 'phone', 'geo', 'age', 'body_params', 'booking', 'description'], $escortInfoInsertData)->execute();
        }

        echo 'OK';
        return true;
    }

    public function actionPhotos()
    {
        $this->transferPhotos();
        echo 'OK';
        return true;
    }

    public function actionAvatars()
    {
        $users = Escort::find()->all();

        foreach($users as $user){
            $photo = Photos::find()->where(['moderated' => 1])->andWhere(['status' => 'A'])->andWhere(['id' => $user->id])->orderBy('created DESC')->one();
            if(!$photo)
                $photo = Photos::find()->where(['id' => $user->id])->orderBy('created DESC')->one();
            if(!$photo)
                continue;

            $date = new \DateTime($photo->created);
            $y = $date->format('Y');
            $m = $date->format('m');
            $d = $date->format('d');

            $user->avatar = $y.'/'.$m.'/'.$d.'/'.$photo->name.'.jpg';
            $user->update(false, ['avatar']);
        }

        echo 'OK';
        return true;
    }

    public function actionUsers()
    {
        $users = Accounts::find()->with('passport')->all();
        $db = Yii::$app->getDb();

        foreach($users as $user){
            $passport = $user->passport;
            if(!$passport || !$user->email)
                continue;

            $escort = Account::find()->where(['id' => $user->id])->one();

            if(!$escort){
                $accountInsertData = [];
                $escortInsertData = [];
                $escortInfoInsertData = [];

                $accountInsertData[] = [
                    $user->id,
                    $user->email,
                    UserEntity::passwordHash($escort->password),
                    Account::ROLE_ESCORT
                ];

                switch($user->sex){
                    case 'M':
                        $sex = Account::SEX_MALE;
                        break;
                    case 'F':
                        $sex = Account::SEX_FEMALE;
                        break;
                    default:
                        $sex = Account::SEX_SHEMALE;
                        break;
                }

                $escortInsertData[] = [
                    $user->id,
                    $user->name ? $user->name : $user->email,
                    $user->email,
                    $sex,
                    $user->city,
                    (new \DateTime($user->created))->format('Y-m-d H:i:s'),
                    (new \DateTime($user->lastAccess))->format('Y-m-d H:i:s'),
                    $user->credits,
                    $user->popularity,
                    $passport->id_image
                ];

                $escortInfoInsertData[] = [
                    $user->id,
                    $passport->first_name,
                    $passport->last_name,
                    $user->phone,
                    json_encode([
                        'language' => $user->language,
                        'city' => $user->city,
                        'region' => $user->state,
                        'country' => $user->country,
                        'address' => $passport->address,
                    ]),
                    $user->age,
                    json_encode([
                        'height' => $user->height,
                        'weight' => $user->weight,
                        'eyes' => $user->eyes,
                        'hair' => $user->hair,
                        'ethnicity' => $user->ethnicity,
                        'orientation' => $user->orientation,
                    ]),
                    $user->booking == 'Y' ? 1 : 0,
                    $user->description ? trim(str_replace('\'', '"', $user->description)) : NULL,
                ];

                try{
                    $db->createCommand()->batchInsert('account', ['id', 'user_name', 'password', 'role'], $accountInsertData)->execute();
                    $db->createCommand()->batchInsert('escort', ['id', 'user_name', 'email', 'sex', 'city_id', 'registration_date', 'last_login', 'balance', 'rating', 'avatar'], $escortInsertData)->execute();
                    $db->createCommand()->batchInsert('escort_info', ['escort_id', 'first_name', 'last_name', 'phone', 'geo', 'age', 'body_params', 'booking', 'description'], $escortInfoInsertData)->execute();
                }catch(Exception $e){
                    echo "Error account {$user->id}:\n";
                    echo $e->getMessage()."\n\n";
                }
            }else{
                $escort->balance = $user->credits;
                $escort->update(false, ['balance']);
            }
        }

        echo 'OK';
        return true;
    }

    public function actionDublicateinfo()
    {
        $limit = $this->limit;
        $offset = 0;
        $escortInfos = true;
        $escortIds = [];
        $ids = [];
        $fuckIds = [];

        while($escortInfos){
            $escortInfos = EscortInfo::find()->limit($limit)->offset($offset)->all();
            if(!$escortInfos){
                echo implode(',', $fuckIds);
                return true;
            }

            foreach($escortInfos as $info){
                if(isset($escortIds[$info->escort_id]) || isset($ids[$info->id])){
                    $info->delete();
                    $fuckIds[] = $info->id;
                }else{
                    $ids[$info->id] = $info->id.'_id';
                    $escortIds[$info->escort_id] = $info->escort_id.'_id';
                }

                $offset += $limit;
                echo "{$limit} records!\r\n";
            }
        }

        echo implode(',', $fuckIds);
        return false;
    }

    private function transferVips()
    {
        $db = Yii::$app->getDb();
        $escorts = [];

        for($i = 0; $i <= 37; $i++){
            $query = $this->model('Accounts')->find();
            $query->offset = rand(1, 1998);
            $escort = $query->one();

            if($escort)
                $escorts[] = $escort;
        }

        $insertData = [];

        foreach($escorts as $escort){
            $insertData[] = [
                $escort->id,
                Membership::VIPS,
            ];
        }

        $count = count($escorts);
        $db->createCommand()->batchInsert('escort_membership', ['escort_id', 'membership_id'], $insertData)->execute();
        echo "{$count} Vips added!\r\n";
    }

    private function transferTopAnkets()
    {
        $limit = $this->limit;
        $offset = 0;
        $db = Yii::$app->getDb();

        $vips = true;

        while($vips){
            $query = $this->model('Vips')->find();
            $query->offset = $offset;
            $query->limit = $limit;
            $vips = $query->all();

            if(!$vips)
                return true;

            $offset += $limit;

            $insertData = [];
            $escorts = [];

            foreach($vips as $vip){
                $insertData[] = [
                    $vip->account_id,
                    Membership::TOP_ANKETS,
                ];
            }

            $count = count($vips);
            $db->createCommand()->batchInsert('escort_membership', ['escort_id', 'membership_id'], $insertData)->execute();
            echo "{$count} Top vips added!\r\n";
        }

        return true;
    }

    private function transferTopvips()
    {
        $limit = $this->limit;
        $offset = 0;
        $db = Yii::$app->getDb();

        $vips = true;

        while($vips){
            $query = $this->model('TopVips')->find();
            $query->offset = $offset;
            $query->limit = $limit;
            $vips = $query->all();

            if(!$vips)
                return true;

            $offset += $limit;

            $insertData = [];
            $insetDurationData = [];

            foreach($vips as $vip){
                $insertData[] = [
                    $vip->account_id,
                    Membership::VIP_ACCOUNT,
                ];
                $insetDurationData[] = [
                    $vip->account_id,
                    Membership::VIP_ACCOUNT,
                    $vip->ts,
                    Yii::$app->local->addMonthToDate($vip->ts, 1),
                ];
            }

            $count = count($vips);
            $db->createCommand()->batchInsert('escort_membership', ['escort_id', 'membership_id'], $insertData)->execute();
            $db->createCommand()->batchInsert('membership_duration', ['escort_id', 'membership_id', 'start_date', 'end_date'], $insetDurationData)->execute();
            echo "{$count} Top vips added!\r\n";
        }

        return true;
    }

    private function transferEscorts()
    {
        $limit = $this->limit;
        $offset = 0;
        $i = 0;
        $escorts = true;

        while($escorts){
            if($i == 2)
                return true;

            $query = $this->model('Accounts')->find();
            $query->offset = $offset;
            $query->limit = $limit;
            $query->with('passport');
            $escorts = $query->all();

            if(!$escorts)
                return true;

            $this->addEscorts($escorts);

            $offset += $limit;
            $i++;
            echo "1000 records!\r\n";
        }

        return true;
    }

    private function addEscorts($escorts)
    {
        $db = Yii::$app->getDb();

        $accountInsertData = [];
        $escortInsertData= [];
        $escortInfoInsertData = [];

        foreach($escorts as $escort){
            if(!$escort->email)
                continue;

            $accountInsertData[] = [
                $escort->id,
                $escort->email,
                UserEntity::passwordHash($escort->password),
                Account::ROLE_ESCORT
            ];

            switch($escort->sex){
                case 'M':
                    $sex = Account::SEX_MALE;
                    break;
                case 'F':
                    $sex = Account::SEX_FEMALE;
                    break;
                default:
                    $sex = Account::SEX_SHEMALE;
                    break;
            }

            $escortInsertData[] = [
                $escort->id,
                $escort->name ? $escort->name : $escort->email,
                $escort->email,
                $sex,
                $escort->city,
                Account::ROLE_ESCORT,
                $escort->created,
                $escort->lastAccess,
                $escort->status == 'I' ? 1 : 0,
                $escort->credits,
                $escort->popularity,
                $escort->passport->id_image
            ];

            $escortInfoInsertData[] = [
                $escort->id,
                $escort->passport->first_name,
                $escort->passport->last_name,
                $escort->phone,
                json_encode([
                    'language' => $escort->language,
                    'city' => $escort->city,
                    'region' => $escort->state,
                    'country' => $escort->country,
                    'address' => $escort->passport->address,
                ]),
                $escort->age,
                json_encode([
                    'height' => $escort->height,
                    'weight' => $escort->weight,
                    'eyes' => $escort->eyes,
                    'hair' => $escort->hair,
                    'ethnicity' => $escort->ethnicity,
                    'orientation' => $escort->orientation,
                ]),
                $escort->booking == 'Y' ? 1 : 0,
                $escort->description ? trim(str_replace('\'', '"', $escort->description)) : NULL,
            ];
        }

        $db->createCommand()->batchInsert('account', ['id', 'user_name', 'password', 'role'], $accountInsertData)->execute();
        $db->createCommand()->batchInsert('escort', ['id', 'user_name', 'email', 'sex', 'city_id', 'role', 'registration_date', 'last_login', 'status', 'balance', 'rating', 'avatar'], $escortInsertData)->execute();
        $db->createCommand()->batchInsert('escort_info', ['escort_id', 'first_name', 'last_name', 'phone', 'geo', 'age', 'body_params', 'booking', 'description'], $escortInfoInsertData)->execute();
    }

    private function transferRegions()
    {
        $limit = $this->limit;
        $offset = 0;
        $db = \Yii::$app->getDb();

        $regions = true;

        while($regions){
            $query = $this->model('Regions')->find();
            $query->offset = $offset;
            $query->limit = $limit;
            $regions = $query->all();

            if(!$regions)
                return true;

            $offset += $limit;

            $insertData = [];

            foreach($regions as $region){
                $insertData[] = [
                    $region->id,
                    $region->name,
                ];
            }

            $db->createCommand()->batchInsert('region', ['id', 'name'], $insertData)->execute();
        }

        return true;
    }

    private function transferCountries()
    {
        $limit = $this->limit;
        $offset = 0;
        $db = \Yii::$app->getDb();

        $countries = true;

        while($countries){
            $query = $this->model('Countries')->find();
            $query->offset = $offset;
            $query->limit = $limit;
            $countries = $query->all();

            if(!$countries)
                return true;

            $offset += $limit;

            $insertData = [];

            foreach($countries as $country){
                $insertData[] = [
                    $country->id,
                    $country->sign,
                    $country->name,
                    $country->region,
                    $country->pref,
                    $country->sms,
                ];
            }

            $db->createCommand()->batchInsert('country', ['id', 'code', 'name', 'region_id', 'pref', 'sms'], $insertData)->execute();
        }

        return true;
    }

    private function transferStates()
    {
        $limit = $this->limit;
        $offset = 0;
        $db = \Yii::$app->getDb();

        $states = true;

        while($states){
            $query = $this->model('States')->find();
            $query->offset = $offset;
            $query->limit = $limit;
            $states = $query->all();

            if(!$states)
                return true;

            $offset += $limit;

            $insertData = [];

            foreach($states as $state){
                $insertData[] = [
                    $state->id,
                    $state->sign,
                    $state->name,
                ];
            }

            $db->createCommand()->batchInsert('state', ['id', 'code', 'name'], $insertData)->execute();
        }

        return true;
    }

    private function transferCities()
    {
        $limit = $this->limit;
        $offset = 0;
        $db = \Yii::$app->getDb();

        $cities = true;

        while($cities){
            $citiesQuery = $this->model('Cities')->find();
            $citiesQuery->offset = $offset;
            $citiesQuery->limit = $limit;
            $cities = $citiesQuery->all();

            if(!$cities)
                return true;

            $offset += $limit;

            $insertData = [];

            foreach($cities as $city){
                $insertData[] = [
                    $city->id,
                    $city->name,
                    $city->country ? $city->country : 1,
                    $city->state ? $city->state : null,
                ];
            }

            $db->createCommand()->batchInsert('city', ['id', 'name', 'country_id', 'state_code'], $insertData)->execute();
        }

        return true;
    }

    private function transferPhotos()
    {
        $photos = $this->model('Photos')->find()->all();

        foreach($photos as $photo){
            $oldPhotosCount = EscortPhoto::find()->where(['escort_id' => $photo->id])->count();
            if($oldPhotosCount > 0)
                continue;

            $date = new \DateTime($photo->created);
            $y = $date->format('Y');
            $m = $date->format('m');
            $d = $date->format('d');

            $newPhoto = new EscortPhoto();
            $newPhoto->escort_id = $photo->id;
            $newPhoto->host_id = 1;
            $newPhoto->path = $y.'/'.$m.'/'.$d.'/'.$photo->name.'.jpg';

            $newPhoto->save(false);
        }

        return true;
    }

    /**
     * @param $modelName
     * @return \app\commands\DataTransfer\Model
     */
    private function model($modelName)
    {
        $className = '\\app\commands\\DataTransfer\\'.$modelName;
        return new $className();
    }
}