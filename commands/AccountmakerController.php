<?php
/**
 * Created by PhpStorm.
 * User: jangolle
 * Date: 22.01.2015
 * Time: 11:44
 */


namespace app\commands;

use app\abstracts\BaseModel;
use app\components\StaticData;
use app\entities\EscortAccountBodyParams;
use app\entities\Geo;
use app\models\Account;
use app\models\City;
use app\models\Escort;
use app\models\EscortGeo;
use app\models\EscortInfo;
use yii\console\Controller;
use yii\console\Exception;

class AccountmakerController extends Controller
{
    const PARSED_DIR = '/ParsedData/';

    const DEFAULT_FILE = 'testData.txt';

    const ACCOUNT_DELIMETER = '[ACCOUNT_DELIMETER]';

    const PARSER_DELIMETER = '[PARSER_DELIMETER]';

    const EMAIL_PREFIX = 'escort';

    const EMAIL_POSTFIX = '@vicesisters.com';

    const DEFAULT_PASSWORD = 'parsed42';

    const EMAIL_COUNT_KEY = 'parsed-email-incr';

    const LOG_FILE = 'accountMaker_log.txt';

    public static $accountKeys = [
        'email',
        'password',
        'sex',
        'cityName',
        'avatar',
        'firstName',
        'lastName',
        'phone',
        'site',
        'age',
        'height',
        'weight',
        'eyes',
        'hair',
        'ethnicity',
        'orientation',
        'bodyParams',
        'description'
    ];

    public static $delaultAccountAttributes = [
        'role' => 'ROLE_ESCORT'
    ];

    public function actionIndex($fileName = null, $city_id = null, $city_name = null)
    {
        if($fileName === null)
            $fileName = self::DEFAULT_FILE;

        if($city_id === null)
            $city_id = 1;

        $accounts = @$this->file2AccountsArray($fileName, $city_name);

        foreach($accounts as $account){
            @$this->createAccount(array_merge($account,['city' => $city_id]));
        }
    }

    private function file2AccountsArray($file, $city_name = null)
    {
        $accounts = [];

        $accountsData = file_get_contents(__DIR__.self::PARSED_DIR.$file);
        $accountsData = explode(self::ACCOUNT_DELIMETER, $accountsData);
        $accountsData = array_diff($accountsData,['']);

        foreach($accountsData as $accountData){
            $account = [];
            $params = explode(self::PARSER_DELIMETER, $accountData);

            if($params[3] != $city_name)
                continue;

            foreach ($params as $key => $param) {
                $account[self::$accountKeys[$key]] = trim($param);
            }

            if(empty($account['password']))
                $account['password'] = self::DEFAULT_PASSWORD;

            $accounts[] = $account;
        }

        return $accounts;
    }

    private function createAccount(array $params)
    {
        if(empty($params['email']) || strpos($params['email'],'@') === false)
            $params['email'] = $this->getEmail();

        if(Account::findOne(['user_name' => $params['email']]) != null) {
            echo 'Duplicate'.PHP_EOL;
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        $account = $this->getAccount($params);
        $escort = $this->getEscort($account, $params);
        $escortInfo = $this->getEscortInfo($account, $escort, $params);

        $transaction->commit();

        \Yii::$app->fastData->incr(self::EMAIL_COUNT_KEY);
        $this->saveToLog($account, $escort, $escortInfo, $params);

        echo 'ID: '. $account->id .PHP_EOL;

        return true;
    }

    private function getAccount(array $params)
    {
        $attributes = [
            'user_name' => $params['email'],
            'password' => $params['password'],
            'role' => self::$delaultAccountAttributes['role']
        ];

        $account = new Account();

        $account->setAttributes($attributes);

        if(!$account->save(false))
            throw new Exception('Can not create account');

        return $account;
    }

    private function getEscort(Account $account, array $params)
    {
        $user = $account->getModel();

        $user->id = $account->id;
        $user->email = $account->user_name;
        $user->user_name = explode('@', $account->user_name)[0];

        switch($params['sex']){
            case 'shemale':
                $user->sex = Account::SEX_SHEMALE;
                break;
            case 'female':
                $user->sex = Account::SEX_FEMALE;
                break;
            case 'male':
                $user->sex = Account::SEX_MALE;
                break;
            default:
                $user->sex = Account::SEX_SHEMALE;
                break;
        }

        $city = City::findOne(['id' => $params['city']]);

        if(!$city)
            $city = City::findOne(['name' => 'Hong Kong']);

        $user->city_id = $city->id;
        $user->registration_date = $this->getRandomDateBetween();
        $user->avatar = $this->createEscortAvatar($params['avatar']);
        $user->is_parsed = true;

        if(!$user->save(false))
            throw new Exception('Can not save escort profile');

        return $user;
    }

    private function createEscortAvatar($file)
    {
        if(!$file)
            return null;

        $fileName = BaseModel::randomWord().StaticData::EXTENSION_IMAGE;

        $remoteAvatar = \Yii::$app->static->uploadImg($fileName, __DIR__.self::PARSED_DIR.'photos/'.$file);

        if(!$remoteAvatar)
            throw new Exception('Can not upload avatar');

        return $remoteAvatar.'/'.$fileName;
    }

    private function getEscortInfo(Account $account, Escort $escort, array $params)
    {
        $info = new EscortInfo();

        $info->escort_id = $account->id;
        $info->first_name = $params['firstName'];
        $info->last_name = $params['lastName'];
        $info->phone = $params['phone'];
        $info->description = $params['description'];
        $info->age = $params['age'];

        $geoParams = \Yii::$app->data->getRepository('EscortGeo')->getGeoParams(['city' => $escort->city_id]);
        $geoParams['escort_id'] = $escort->id;

        $escortGeo = new EscortGeo();
        $escortGeo->setAttributes($geoParams);
        $escortGeo->save(false);

        $info->geo = new Geo();
        $info->geo->city = $escort->city_id;
        $info->geo->country = $geoParams['country_id'];
        $info->geo->state = $geoParams['state_id'];
        $info->geo->region = $geoParams['region_id'];
        $info->geo->webPage = $params['site'];
        $info->geo = json_encode($info->geo);

        $info->body_params = new EscortAccountBodyParams();
        $info->body_params->height = $params['height'];
        $info->body_params->weight = $params['weight'];
        $info->body_params->eyes = $this->getEyes($params['eyes']);
        $info->body_params->hair = $this->getHair($params['hair']);
        $info->body_params->ethnicity = $this->getEthnicity($params['ethnicity']);
        $info->body_params->orientation = $this->getOrientation($params['orientation']);

        $info->body_params->bodyParams = $params['bodyParams'];
        $info->body_params = json_encode($info->body_params);

        if(!$info->save(false))
            throw new Exception('Can not save escort info');

        return $info;
    }

    private function getEyes($eyes)
    {
        $eyesCode = Account::EYES_BROWN;

        switch($eyes){
            case 'Brown':
            case 'Карие':
                $eyesCode = Account::EYES_BROWN;
                break;
            case 'Blue':
            case 'Голубые':
                $eyesCode = Account::EYES_BLUE;
                break;
            case 'Green':
            case 'Зеленые':
                $eyesCode = Account::EYES_GREEN;
                break;
            case 'Grey':
            case 'Серые':
                $eyesCode = Account::EYES_GREY;
                break;
            case 'Hazel':
            case 'Каштановые':
                $eyesCode = Account::EYES_HAZEL;
                break;
        }

        return $eyesCode;
    }

    private function getHair($hair)
    {
        $hairCode = Account::HAIR_BLACK;

        switch($hair){
            case 'Auburn':
            case 'Каштановые':
                $hairCode = Account::HAIR_AUBURN;
                break;
            case 'Blonde':
            case 'Блонд':
                $hairCode = Account::HAIR_BLONDE;
                break;
            case 'Black':
            case 'Чёрные':
                $hairCode = Account::HAIR_BLACK;
                break;
            case 'Brown':
            case 'Карие':
                $hairCode = Account::HAIR_BROWN;
                break;
            case 'Grey':
            case 'Серые':
                $hairCode = Account::HAIR_GREY;
                break;
            case 'Red':
            case 'Рыжие':
                $hairCode = Account::HAIR_RED;
                break;
            case 'Other':
            case 'Другое':
                $hairCode = Account::HAIR_OTHER;
                break;
        }

        return $hairCode;
    }

    private function getEthnicity($ethnicity)
    {
        $ethnicityCode = Account::ETHNICITY_WHITE;

        switch($ethnicity){
            case 'Asian':
            case 'Азиат':
                $ethnicityCode = Account::ETHNICITY_ASIAN;
                break;
            case 'Caucasian':
            case 'Кавказец':
                $ethnicityCode = Account::ETHNICITY_CAUCASIAN;
                break;
            case 'Black':
            case 'Темнокожий':
                $ethnicityCode = Account::ETHNICITY_BLACK;
                break;
            case 'Indian':
            case 'Индиец':
                $ethnicityCode = Account::ETHNICITY_INDIAN;
                break;
            case 'Latin':
            case 'Латиноамериканец':
                $ethnicityCode = Account::ETHNICITY_LATIN;
                break;
            case 'Житель Ближнего Востока':
            case 'Middle East':
                $ethnicityCode = Account::ETHNICITY_MIDDLE_EAST;
                break;
            case 'Коренной американец':
            case 'Native American':
                $ethnicityCode = Account::ETHNICITY_NATIVE_AMERICAN;
                break;
            case 'Белый':
            case 'White':
                $ethnicityCode = Account::ETHNICITY_WHITE;
                break;
            case 'Other':
            case 'Другое':
                $ethnicityCode = Account::ETHNICITY_OTHER;
                break;
        }

        return $ethnicityCode;
    }

    private function getOrientation($orientation)
    {
        $orientationCode = Account::ORIENTATION_STRAIGHT;

        switch($orientation){
            case 'Straight':
            case 'Стандартная':
                $orientationCode = Account::ORIENTATION_STRAIGHT;
                break;
            case 'Gay':
            case 'Lesbian':
            case 'Гей':
            case 'Лесби':
                $orientationCode = Account::ORIENTATION_GEY;
                break;
            case 'Bi':
            case 'Би':
            case 'Bisexual':
            case 'Бисексуал':
                $orientationCode = Account::ORIENTATION_BI;
                break;
        }

        return $orientationCode;
    }

    private function getEmail()
    {
        $number = (integer)\Yii::$app->fastData->get(self::EMAIL_COUNT_KEY);

        return self::EMAIL_PREFIX . $number . self::EMAIL_POSTFIX;
    }

    private function getRandomDateBetween($first = null, $second = null)
    {
        if($first == null)
            $first = strtotime('2007-07-30 12:00:00');

        if($second == null)
            $second = strtotime('2014-07-30 12:00:00');

        $randomStamp = mt_rand($first, $second);

        return date("Y-m-d H:i:s", $randomStamp);
    }

    private function saveToLog(Account $account, Escort $escort, EscortInfo $escortInfo, array $params)
    {
        $log  = 'Email: '. $escort->email .PHP_EOL;
        $log .= 'Password: '. $params['password'] .PHP_EOL;
        $log .= 'Name: '. $params['firstName'].' '.$params['lastName'] .PHP_EOL;
        $log .= 'Sex: '. $params['sex'] .PHP_EOL;
        $log .= 'Age: '. $params['age'] .PHP_EOL;
        $log .= 'Link: '. '/profile-'.$escort->id .PHP_EOL;
        $log .= '--------------------------------'.PHP_EOL;

        return file_put_contents(__DIR__.self::PARSED_DIR.self::LOG_FILE, $log, FILE_APPEND);
    }
}