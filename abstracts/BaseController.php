<?php
/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 06.10.14
 * Time: 19:38
 */

namespace app\abstracts;

use app\models\Account;
use Yii;
use yii\base\InvalidCallException;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

abstract class BaseController extends Controller
{
    /**
     * @var \app\abstracts\Repository[]
     */
    private $_repositories = [];

    public function beforeAction($action)
    {
        Yii::$app->layout = false;

        Yii::$app->local->setLanguage();

        if(strpos(Yii::$app->request->url, '?r=') !== false)
            throw new NotFoundHttpException();

        return parent::beforeAction($action);
    }

    public function render($view = null, $params = [])
    {
        if(is_array($view)){
            $params = $view;
            $view = null;
        }

        return parent::render($this->parseViewString($view), $params);
    }

    public function renderPartial($view, $params = [])
    {
        return parent::renderPartial($this->parseViewString($view), $params);
    }

    public function get($name = null)
    {
        return Yii::$app->request->get($name);
    }

    public function getPost($paramName = null)
    {
        return Yii::$app->request->post($paramName);
    }

    /**
     * @param string $access
     * @return bool
     */
    protected function checkAccess($access)
    {
        if(Yii::$app->user->isAdmin)
            return true;

        $result = false;

        $roles = Yii::$app->user->getRoles();

        foreach($roles as $role){
            $role = trim($role);

            if($role == Account::ROLE_ADMIN)
                return true;

            if(!isset($access[$role]))
                continue;

            $controllers = $access[$role];

            if(!is_array($controllers) || count($controllers) == 0){
                continue;
            }

            if(!array_key_exists($this->id, $controllers)){
                continue;
            }

            foreach($controllers as $name => $controller){
                if($name != $this->id)
                    continue;

                if($controller === '*'){
                    $result = true;
                    break;
                }

                if(in_array($this->action->id, $controller)){
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    protected function checkManageAccess($id)
    {
        if(Yii::$app->user->id != $id && !Yii::$app->user->getIsAdmin())
            throw new ForbiddenHttpException(Yii::t('error', 'В доступе отказано'));
    }

    /**
     * @param $modelName
     * @return \app\abstracts\Repository
     */
    protected function model($modelName)
    {
        if(!isset($this->_repositories[$modelName])){
            $this->_repositories[$modelName] = Yii::$app->data->getRepository($modelName);
        }

        return $this->_repositories[$modelName];
    }

    protected function ajax($json, $view = null, $params = [])
    {
        if(is_string($json)){
            if(is_array($view)){
                $params = $view;
                $view = $json;
                $json = [];
            }else{
                $message = $json;
                $json = [
                    'message' => Yii::t('ajax', $message),
                ];
            }
        }

        if(!$json)
            $json = [];

        if(!isset($json['message']))
            $json['message'] = '';
        if(!isset($json['error']))
            $json['error'] = '';
        if(!isset($json['html']))
            $json['html'] = '';

        if($view !== null && !$json['html']){
            $json['html'] = $this->renderPartial($view, $params);
        }

        echo Json::encode($json);
        Yii::$app->end();
    }

    protected function ajaxError($json)
    {
        if(is_string($json)){
            $error = $json;
            $json = [
                'error' => $this->parseError($error)
            ];
        }

        $this->ajax($json);
    }

    protected function ajaxNotFound($json = 'Ничего не найдено')
    {
        if(is_string($json)){
            $error = $json;
            $json = [
                'html' => $this->parseError($error)
            ];
        }

        $this->ajax($json);
    }

    protected function validateAjax()
    {
        if(!$this->isAjax())
            throw new InvalidCallException(Yii::t('error', 'Неверный запрос'));
    }

    protected function partial($view = '', $params = [])
    {
        if(is_array($view)){
            $params = $view;
            $view = null;
        }

        return parent::renderPartial($this->parseViewString($view), $params);
    }

    protected function parseError($error)
    {
        if(is_array($error)){
            $errorText = '';

            foreach($error as $text){
                $errorText .= Yii::t('error', (string)$text).'; ';
            }

            if($errorText)
                $errorText = substr($errorText, 0, strlen($errorText)-2);

            return $errorText;
        }

        return Yii::t('error', (string)$error);
    }

    protected function isPost()
    {
        return Yii::$app->request->getIsPost();
    }

    protected function isAjax()
    {
        return Yii::$app->request->getIsAjax();
    }

    private function parseViewString($view = null)
    {
        if(!$view)
            $view = $this->action->id;
        elseif(strpos($view, '/') !== false)
            $view = '/'.$view;

        if(strpos($view, '.') === false)
            $view .= '.twig';

        return $view;
    }
}