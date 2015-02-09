<?php

namespace app\components;

use Yii;
use app\models\Membership;
use app\models\Account;
use app\models\Escort;
use app\models\Transaction;
use yii\web\User as YiiUser;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 28.10.2014
 * Time: 23:57
 *
 * @property bool $isAdmin
 * @property bool $isEscort
 *
 * @property string $name
 * @property string $ava
 * @property float $balance
 * @property bool $hasMoneyToHotMessage
 */
class User extends YiiUser
{
    private $roles;

    private $adminAva = '';

    public function getUsername()
    {
        return $this->getModel() ? $this->getModel()->getUserName() : null;
    }

    public function getFullName()
    {
        return $this->getModel() ? $this->getModel()->getFullName() : null;
    }

    public function getAva()
    {
        if($this->getIsAdmin())
            return $this->adminAva;

        return $this->getEntity() ? $this->getEntity()->getAva() : '';
    }

    public function getBigAva()
    {
        return $this->getEntity()->getAva();
    }

    /**
     * @return \app\models\Account|null
     */
    private function getModel(){
        return  $this->getIdentity();
    }

    /**
     * @return \app\models\Escort|\app\models\User|null
     */
    public function getEntity(){
        return $this->getModel() ? $this->getModel()->getEntity() : null;
    }

    public function setActivity(){
        if($this->getIsAdmin())
            return null;

        return $this->getEntity() ? $this->getEntity()->setActivity() : null;
    }

    public function setNonActive(){
        if($this->getIsAdmin())
            return null;

        return $this->getEntity() ? $this->getEntity()->setNonActive() : null;
    }

    public function checkOnlineTime(){
        if($this->getIsAdmin())
            return null;

        $entity = $this->getEntity();

        if($entity !== null){
            if($entity->className() != Escort::className()){
                return false;
            }
        }

        return $entity ? $entity->checkOnlineTime() : null;
    }

    public function logout($destroySession = true){
        $this->setNonActive();
        return parent::logout($destroySession);
    }

    public function getRole()
    {
        return $this->identity ? trim($this->identity->role) : Account::ROLE_GUEST;
    }

    public function getName()
    {
        return $this->getEntity()->getUserName();
    }

    public function getBalance()
    {
        if($this->getIsGuest())
            return 0;
        return $this->getIdentity()->balance ? $this->getIdentity()->balance : 0;
    }

    public function canUploadPhotos()
    {
        if($this->getIsEscort()){
            return $this->getEntity()->canUploadPhotos();
        }

        return false;
    }

    public function getRoles()
    {
        if($this->roles === null){
            $this->roles = [];
            $role = $this->getRole();
            $ierarchy = Account::getRolesIerarchy();

            $this->roles = $this->getRolesArray($role, $ierarchy);
        }

        return $this->roles;
    }

    /**
     * @param \yii\web\IdentityInterface $identity
     * @param bool $cookieBased
     * @param int $duration
     */
    public function afterLogin($identity, $cookieBased, $duration)
    {
        $this->getEntity()->afterLogin();
        parent::afterLogin($identity, $cookieBased, $duration);
    }

    /**
     * @param \yii\web\IdentityInterface $identity
     */
    protected function afterLogout($identity)
    {
        Yii::$app->chat->removeUser($identity->getId());
        parent::afterLogout($identity);
    }

    public function getIsAdmin()
    {
        return $this->getIsGuest() === true ? false : $this->getEntity()->getIsAdmin();
    }

    public function getIsEscort()
    {
        return $this->getIsGuest() === true ? false : $this->getEntity()->getIsEscort();
    }

    public function getIsUser()
    {
        return $this->getIsGuest() === true ? false : $this->getEntity()->getIsUser();
    }

    public function addToBalance($sum, $serviceName = 'system', $userId = null, $description = 'Пополнение счёта')
    {
        $transaction = new Transaction();
        $transaction->service_name = $serviceName;
        $transaction->sum = $sum;
        $transaction->escort_id = $userId;
        $transaction->description = Yii::t('base', $description);

        $result = Yii::$app->data->getRepository('Account')->addMoneyToBalance($userId, $transaction);
        if($result !== false && $this->getIdentity())
            $this->getIdentity()->balance = $result;

        return $result !== false;
    }

    public function payForHotMessage($messageId)
    {
        if(!$this->getIsEscort())
            return false;

        $membership = Yii::$app->data->getRepository('Membership')->findById(Membership::HOT_MESSAGE);
        $transaction = new Transaction();

        $transaction->service_name = Transaction::HOT_MESSAGE_SERVICE;
        $transaction->sum = $membership->getDiscountPrice();
        $transaction->description = Yii::t('base', 'Оплата горячего сообщения ID {messageId}', ['messageId' => $messageId]);

        return $this->balanceReduction($transaction->sum, $transaction);
    }

    public function balanceReduction($amount, Transaction $transaction)
    {
        $amount = $this->getBalance() - $amount;
        if($amount < 0)
            return false;

        $transaction->escort_balance = $amount;

        $result = Yii::$app->data->getRepository('Account')->setAmount($this->id, $amount, $transaction);
        if($result)
            $this->getIdentity()->balance = $amount;
        return $result;
    }

    public function getHasMoneyToHotMessage()
    {
        $membership = Yii::$app->data->getRepository('Membership')->findById(Membership::HOT_MESSAGE);

        $price = $membership->getDiscountPrice();

        return $this->getBalance() >= $price;
    }

    private function getRolesArray($role, $ierarchy, $roles = [])
    {
        if(!isset($ierarchy[$role])){
            return array_merge($roles, [Account::ROLE_GUEST]);
        }else{
            $tmpRoles = [];

            foreach($ierarchy[$role] as $r){
                if(!in_array($r, $tmpRoles))
                    $tmpRoles = array_merge($tmpRoles, $this->getRolesArray($r, $ierarchy, array_merge($roles, [$role])));
            }

            return $tmpRoles;
        }

    }
}