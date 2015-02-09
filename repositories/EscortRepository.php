<?php

namespace app\repositories;

use Yii;
use app\forms\SearchForm;
use app\components\FastData;
use app\models\City;
use app\models\EscortInfo;
use app\models\EscortPhoto;
use app\models\Host;
use app\models\EscortGeo;
use app\entities\Geo;
use app\models\MembershipDuration;
use app\models\Membership;
use app\models\Account;
use app\models\Escort;
use yii\db\mssql\PDO;
use yii\db\Query;
use yii\debug\models\search\Debug;
use yii\web\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 30.10.2014
 * Time: 21:44
 */
class EscortRepository extends AccountRepository
{
    const FULL_NAME_SQL =
                'CASE
                    WHEN "info"."first_name" IS NOT NULL
                    THEN "info"."first_name"||\' \'||"info"."last_name"
                    ELSE "escort"."user_name"
                END';

    /**
     * @return array
     */
    public function getVips()
    {
        $key = FastData::VIPS_LIST_KEY;
        $vips = Yii::$app->cache->get($key);

        if(!$vips){
            $photoSQL = (new Query())->select([
                    '\'http://\'||"host"."name"||\'/img/\'||"photo"."path"',
            ])
                ->from(EscortPhoto::tableName().' photo')
                ->leftJoin(Host::tableName(), '"host"."id" = "photo"."host_id"')
                ->where('"photo"."escort_id" = "escort"."id"')
                //->andWhere('"photo"."verified" = '.EscortPhoto::VERIFIED)
                ->limit(1)
                ->orderBy('RANDOM()')
                ->createCommand()
                ->sql;

            $query = (new Query())->select([
                'escort.id',
                'escort.avatar',
                self::FULL_NAME_SQL.' AS "fullName"',
                'city_table.name AS city',
                '('.$photoSQL.') AS ava',
            ])
                ->from(Escort::tableName())
                ->leftJoin(MembershipDuration::tableName().' membership', '"membership"."escort_id" = "escort"."id"')
                ->leftJoin(EscortInfo::tableName().' info', '"info"."escort_id" = "escort"."id"')
                ->leftJoin(City::tableName().' city_table', '"city_table"."id" = "escort"."city_id"')
                ->where(['membership.membership_id' => Membership::VIP_ACCOUNT])
                ->andWhere(['>=', 'membership.end_date', 'now'])
                ->limit(Yii::$app->params['topVipsLimit'])
                ->orderBy('RANDOM()');

            $vips = $query->all();

            if($vips){
                $escortModel = new Escort();
                $photoRepository = Yii::$app->data->getRepository('EscortPhoto');

                foreach($vips as $num => $vip){
                    $vip['isOnline'] = $escortModel->getIsOnline($vip['id']);
                    if(!$vip['ava']){
                        $vips['ava'] = $vip['avatar'] ? $photoRepository->getUserPhoto($vip['avatar']) : $escortModel->getDefaultAvatar();
                    }
                    unset($vip['avatar']);

                    $vips[$num] = $vip;
                }

                Yii::$app->cache->set($key, $vips, Yii::$app->params['topVipsCacheTime']);
            }
        }

        return $vips;
    }

    /**
     * @return \app\models\Escort[]
     */
    public function getPremiums()
    {
        $query = Escort::find()
                    ->leftJoin(MembershipDuration::tableName().' proplan', 'proplan.escort_id = escort.id')
                    ->where(['proplan.membership_id' => Membership::PREMIUM_ACCOUNT])
                    ->andWhere(['>=', 'proplan.end_date', 'now'])
                    ->limit(Yii::$app->params['premiumAccountsLimit'])
                    ->with('escortInfo')
                    ->orderBy('RANDOM()');

        $escorts = Yii::$app->dbCache->getAll($this->setGeoParams($query), Yii::$app->params['topVipsCacheTime']);

        if(!$escorts){
            $query = Escort::find()
                ->leftJoin(MembershipDuration::tableName().' proplan', 'proplan.escort_id = escort.id')
                ->where(['proplan.membership_id' => Membership::PREMIUM_ACCOUNT])
                ->andWhere(['>=', 'proplan.end_date', 'now'])
                ->limit(Yii::$app->params['premiumAccountsLimit'])
                ->with('escortInfo')
                ->orderBy('RANDOM()');

            $escorts = Yii::$app->dbCache->getAll($query, Yii::$app->params['topVipsCacheTime']);
        }

        return $escorts;
    }

    /**
     * @param array $params
     * @return \app\models\Escort[]|null
     */
    public function getTopProfiles($params = [])
    {
        $sex = isset($params['sex']) ? $params['sex'] : null;
        $offset = isset($params['offset']) ? $params['offset'] : null;
        $limit = isset($params['limit']) ? $params['limit'] : null;
        $regions = isset($params['regions']) ? $params['regions'] : null;
        $sortByDate = isset($params['sortByDate']) ? (bool)$params['sortByDate'] : false;

        $query = Escort::find()
            ->with(['escortInfo', 'membershipDurations'])
            ->leftJoin(MembershipDuration::tableName().' proplan', 'escort.id = proplan.escort_id')
            ->where(['proplan.membership_id' => Membership::PURPLE_PLACE])
            ->andWhere(['>=', 'proplan.end_date', 'now'])
            ->andWhere('escort.id IN(proplan.escort_id)');

        if($sortByDate === true)
            $query->orderBy('proplan.end_date DESC');
        else
            $query->orderBy('escort.rating DESC');

        if($sex !== null){
            $query->andWhere(['escort.sex' => $sex]);
            $limit = $limit !== null ? $limit : Yii::$app->params['topProfilesListLimit'];
        }else{
            $limit = $limit !== null ? $limit : Yii::$app->params['topProfilesLimit'];
        }

        $query->limit($limit);

        if($offset !== null){
            $query->offset($offset);
        }

        $ankets = Yii::$app->dbCache->getAll($this->setGeoParams($query, $regions), Yii::$app->params['topVipsCacheTime']);

        foreach($ankets as $num => $anket){
            $ankets[$num]->isTopAnket = true;
        }

        return $ankets;
    }

    /**
     * @param array $params
     * @return \app\models\Escort[]|null
     */
    public function getProfiles($params = [])
    {
        $sex = isset($params['sex']) ? $params['sex'] : null;
        $offset = isset($params['offset']) ? $params['offset'] : null;
        $topLimit = isset($params['topLimit']) ? $params['topLimit'] : null;
        $regions = isset($params['regions']) ? $params['regions'] : null;
        $sortByDate = isset($params['sortByDate']) ? (bool)$params['sortByDate'] : false;

        if($sex !== null){
            $limit = Yii::$app->params['topProfilesListLimit'];
        }else{
            $limit = Yii::$app->params['topProfilesLimit'];
        }

        if($sex === 'escorts')
            $sex = null;

        $topProfilesParams = [
            'sex' => $sex,
            'offset' => $offset,
            'limit' => $topLimit,
            'regions' => $regions,
            'sortByDate' => $sortByDate,
        ];
        $topProfiles = (array)$this->getTopProfiles($topProfilesParams);

        if(!empty($topProfiles)){
            $limit -= count($topProfiles);
        }

        if($limit > 0){
            $simpleProfilesQuery = Escort::find()
                ->with(['escortInfo', 'membershipDurations'])
                ->orderBy('escort.rating DESC')
                ->limit($limit);

            if(!empty($topProfiles)){
                $offset -= count($topProfiles);
                $ids = [];
                foreach($topProfiles as $profile){
                    $ids[] = $profile->id;
                }
                $simpleProfilesQuery->where(['NOT IN', 'escort.id', $ids]);
            }
            if($offset !== null)
                $simpleProfilesQuery->offset($offset);
            if($sex !== null)
                $simpleProfilesQuery->andWhere(['escort.sex' => $sex]);

            $simpleProfiles = Yii::$app->dbCache->getAll($this->setGeoParams($simpleProfilesQuery, $regions), Yii::$app->params['escortAccountCacheTime']);

            $cache = array_merge($topProfiles, $simpleProfiles);
        }else{
            $cache = $topProfiles;
        }

        unset($profiles, $topProfiles, $simpleProfiles);

        return $cache;
    }

    public function findProfiles(SearchForm $form, $offset = 0)
    {
        if(!$form->isVip && !$form->isPremium && !$form->isStandard)
            return [];

        $limit                          = Yii::$app->params['topProfilesListLimit'];
        $membershipIdKey                = '"membership"."membership_id"';
        $endDateKey                     = '"membership"."end_date"';
        $endDateDescKey                 = $endDateKey.' DESC';
        $endDateCondition               = ['>=', $endDateKey, 'now'];
        $joinMembershipCondition        = '"membership"."escort_id" = "escort"."id"';
        $topCondition                   = [$membershipIdKey => Membership::PURPLE_PLACE];
        $topStringCondition             = $membershipIdKey.'='.Membership::PURPLE_PLACE;
        $joinMembershipStringCondition  = '';

        if($form->isVip){
            $joinMembershipStringCondition .= $membershipIdKey.'='.Membership::VIP_ACCOUNT.' AND "membership"."end_date" >= \'now\'';
        }
        if($form->isPremium){
            if($form->isVip)
                $joinMembershipStringCondition .= ' OR ';
            $joinMembershipStringCondition .= $membershipIdKey.'='.Membership::PREMIUM_ACCOUNT.' AND "membership"."end_date" >= \'now\'';
        }

        if($joinMembershipStringCondition !== '' && !$form->isStandard)
            $topCondition = $topStringCondition.' AND ('.$joinMembershipStringCondition.')';

        $topQuery = Escort::find()
                    ->leftJoin(MembershipDuration::tableName().' membership', $joinMembershipCondition)
                    ->where($topCondition)
                    ->andWhere($endDateCondition)
                    ->orderBy($endDateDescKey)
                    ->limit($limit)
                    ->offset($offset)
                    ->with('escortInfo');

        /*$topQuery = $this->setSearchParams($topQuery, $form);
        \Debug::show([$topQuery->createCommand()->sql, $topQuery->createCommand()->params]);*/

        $topList = Yii::$app->dbCache->getAll($this->setSearchParams($topQuery, $form), Yii::$app->params['searchCacheTime']);

        $notInArray = [];

        if($topList){
            $limit -= count($topList);
            foreach($topList as $top){
                $notInArray[] = $top->id;
            }
        }

        if($limit > 0){
            $query = Escort::find();

            if($joinMembershipStringCondition !== '' && !$form->isStandard){
                $query->leftJoin(MembershipDuration::tableName().' membership', $joinMembershipCondition);
                $query->where($joinMembershipStringCondition);
            }

            if(!empty($notInArray))
                $query->andWhere(['NOT IN', 'escort.id', $notInArray]);

            $query
                ->orderBy('escort.rating DESC')
                ->limit($limit)
                ->offset($offset)
                ->with('escortInfo');

            /*$query = $this->setSearchParams($query, $form);
            \Debug::show([$query->createCommand()->sql, $query->createCommand()->params]);*/

            $escortsList = Yii::$app->dbCache->getAll($this->setSearchParams($query, $form), Yii::$app->params['searchCacheTime']);
        }else{
            $escortsList = [];
        }

        return array_merge($topList, $escortsList);
    }

    public function getLastVerified()
    {
        $query = Escort::find()
            ->with('escortInfo')
            ->where(['IS NOT', 'verification_date', NULL])
            ->orderBy('rating')
            ->limit(Yii::$app->params['lastVerifiedLimit']);

        return Yii::$app->dbCache->getAll($query, Yii::$app->params['lastVerifiedCacheTime']);
    }

    public function getProfile($id)
    {
        $query = Escort::find()
            ->where(['id' => $id])
            ->with('escortInfo');

        return Yii::$app->dbCache->getOne($query, Yii::$app->params['escortAccountCacheTime']);
    }

    public function getIdAll()
    {
        return Yii::$app->db->runQuery('SELECT id FROM '.$this->entity->tableName(), [], PDO::FETCH_COLUMN);
    }

    public function getIsOnline($id)
    {
        return (bool)Yii::$app->fastData->get(Escort::ONLINE_KEY.$id);
    }

    public function findEntityById($id)
    {
        if(!$account = $this->findCashedById($id, Account::className()))
            return null;

        $userClass = $this->entity->className();

        return $userClass::findCachedOne($id, $userClass);
    }

    public function getProfileFoolInfo($id)
    {
        $query = Escort::find()
            ->with(['escortInfo', 'photos'])
            ->where(['id' => $id]);

        return Yii::$app->dbCache->getOne($query, Yii::$app->params['escortAccountCacheTime']);
    }

    public function getNewMembers($limit)
    {
        $query = Escort::find()
            ->limit($limit)
            ->with('escortInfo')
            ->orderBy('id DESC');

        return Yii::$app->dbCache->getAll($query, Yii::$app->params['lastVerifiedCacheTime']);
    }

    public function getProplansIdArray($userId)
    {
        $res = [];
        $query = MembershipDuration::find()
                            ->select(['membership_id'])
                            ->where(['escort_id' => $userId]);

        $array = Yii::$app->dbCache->get($query, Yii::$app->params['membershipDurationCacheTime'])->asArray()->all();
        if($array){
            foreach($array as $ar){
                $res[] = $ar['membership_id'];
            }
        }

        return $res;
    }

    public function updateRating($id, $rating)
    {
        $escort = $this->findEntityById($id);
        if(!$escort)
            return new NotFoundHttpException(Yii::t('error', 'Не удалось найти эскорт ID {id}', ['id' => $id]));

        return $this->cmd()->update($this->entity->tableName(), ['rating' => $rating], ['id' => $id])->execute();
    }

    /**
     * @param \yii\db\ActiveQuery $qb
     * @param Geo $regions
     * @return \yii\db\ActiveQuery
     */
    private function setGeoParams($qb, $regions = null)
    {
        if($regions === null)
            $regions = Yii::$app->session->getSearchSettings();

        $city = $regions->city;
        $state = $regions->state;
        $country = $regions->country;
        $region = $regions->region;

        if(!$city && !$state && !$country && !$region)
            return $qb;

        if(!$city)
            $qb->leftJoin(EscortGeo::tableName().' geo', 'geo.escort_id = escort.id');

        if($city){
            $qb->andWhere(['escort.city_id' => $city]);
        }elseif($state){
            $qb->andWhere(['geo.state_id' => $state]);
        }elseif($country){
            $qb->andWhere(['geo.country_id' => $country]);
        }elseif($region){
            $qb->andWhere(['geo.region_id' => $region]);
        }

        return $qb;
    }

    /**
     * @param \yii\db\ActiveQuery $qb
     * @param SearchForm $form
     * @return \yii\db\ActiveQuery
     */
    private function setSearchParams($qb, SearchForm $form)
    {
        if(!$form->city)
            $qb->leftJoin(EscortGeo::tableName().' geo', '"geo"."escort_id" = "escort"."id"');

        $qb->leftJoin(EscortInfo::tableName().' info', '"info"."escort_id" = "escort"."id"');

        $conditions = '';

        if($form->city)
            $conditions .= 'escort.city_id='.$form->city;
        elseif($form->state)
            $conditions .= 'geo.state_id='.$form->state;
        elseif($form->country)
            $conditions .= 'geo.country_id='.$form->country;
        elseif($form->region)
            $conditions .= 'geo.region_id='.$form->region;

        if(!$form->advanced){
            $conditions = $this->addCondition($conditions, $this->createTextContition($form->searchText));
            $qb->andWhere($conditions);
            return $qb;
        }

        $sex = $form->sex;
        if($sex !== '-'){
            $conditions = $this->addCondition($conditions, '"escort"."sex"='.$sex);
        }

        $age = $form->age;
        if($age !== '-'){
            $age = explode('-', $age);
            $age = array_filter($age);
            if(count($age) === 2)
                $cond = "\"info\".\"age\" BETWEEN {$age[0]} AND {$age[1]}";
            else
                $cond = '"info"."age">='.$age[0];

            $conditions = $this->addCondition($conditions, $cond);
        }

        $ethnicity = $form->ethnicity;
        if($ethnicity !== '-'){
            $conditions = $this->addCondition($conditions, "info.body_params->>'ethnicity'='{$ethnicity}'");
        }

        $eyes = $form->eyes;
        if($eyes !== '-'){
            $conditions = $this->addCondition($conditions, "info.body_params->>'eyes'='{$eyes}'");
        }

        $hair = $form->hair;
        if($hair !== '-'){
            $conditions = $this->addCondition($conditions, "info.body_params->>'hair'='{$hair}'");
        }

        $orientation = $form->orientation;
        if($orientation !== '-'){
            $conditions = $this->addCondition($conditions, "info.body_params->>'orientation'='{$orientation}'");
        }

        $height = $form->height;
        if($height !== '-'){
            $height = explode('-', $height);
            $height = array_filter($height);
            if(count($height) === 2)
                $cond = "(\"info\".\"body_params\"->>'height')::int BETWEEN {$height[0]} AND {$height[1]}";
            else
                $cond = "info.body_params->>'height'>='{$height[0]}'";

            $conditions = $this->addCondition($conditions, $cond);
        }

        $weight = $form->weight;
        if($weight !== '-'){
            $weight = explode('-', $weight);
            $weight = array_filter($weight);
            if(count($weight) === 2)
                $cond = "(\"info\".\"body_params\"->>'weight')::int BETWEEN {$weight[0]} AND {$weight[1]}";
            else
                $cond = "info.body_params->>'weight'>='{$weight[0]}'";

            $conditions = $this->addCondition($conditions, $cond);
        }

        $breast = $form->breast;
        if($breast !== '-'){
            $breast = explode('-', $breast);
            $breast = array_filter($breast);
            if(count($breast) === 2)
                $cond = "(\"info\".\"body_params\"->>'breast')::int BETWEEN {$breast[0]} AND {$breast[1]}";
            else
                $cond = "info.body_params->>'breast'='{$breast[0]}'";

            $conditions = $this->addCondition($conditions, $cond);
        }

        $waist = $form->waist;
        if($waist !== '-'){
            $waist = explode('-', $waist);
            $waist = array_filter($waist);
            if(count($waist) === 2)
                $cond = "(\"info\".\"body_params\"->>'waist')::int BETWEEN {$waist[0]} AND {$waist[1]}";
            else
                $cond = "info.body_params->>'waist'>='{$waist[0]}'";

            $conditions = $this->addCondition($conditions, $cond);
        }

        $hips = $form->hips;
        if($hips !== '-'){
            $hips = explode('-', $hips);
            $hips = array_filter($hips);
            if(count($hips) === 2)
                $cond = "(\"info\".\"body_params\"->>'hips')::int BETWEEN {$hips[0]} AND {$hips[1]}";
            else
                $cond = "info.body_params->>'hips'>='{$hips[0]}'";

            $conditions = $this->addCondition($conditions, $cond);
        }

        if($form->extendedText){
            $services = $form->getExtendedTextItems();
            if(!empty($services)){
                $ext = '';

                foreach($services as $service){
                    $service = str_replace(':', '', $service['value']);
                    $ext .= '"'.$service.'",';
                }

                $ext = substr($ext, 0, strlen($ext)-1);
                $cond = '\'{'.$ext.'}\' <@ "info"."extended_info"';
                $conditions = $this->addCondition($conditions, $cond);
            }
        }

        $conditions = $this->addCondition($conditions, $this->createTextContition($form->searchText));

        $qb->andWhere($conditions);
        return $qb;
    }

    private function addCondition($conditions, $cond)
    {
        if($cond === '')
            return $conditions;
        return $conditions === '' ? $cond : "{$conditions} AND ({$cond})";
    }

    private function createTextContition($text)
    {
        $cond = '';

        if($text && $text !== '-'){
            $userName = '"escort"."user_name"';
            $firstName = '"info"."first_name"';
            $lastName = '"info"."last_name"';

            $words = explode(' ', $text);
            $words = array_filter($words);

            if(count($words) == 2)
                $cond = "(LOWER({$firstName}) LIKE LOWER('%{$words[0]}%') AND LOWER({$lastName}) LIKE LOWER('%{$words[1]}%')) OR LOWER({$userName}) LIKE LOWER('%{$text}%')";
            else
                $cond = "LOWER({$firstName}) LIKE LOWER('%{$text}%') OR LOWER({$lastName}) LIKE LOWER('%{$text}%') OR LOWER({$userName}) LIKE LOWER('%{$text}%')";
        }

        return $cond;
    }
}