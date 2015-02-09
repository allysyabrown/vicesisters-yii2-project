<?php
/**
 * Created by PhpStorm.
 * User: rem
 * Date: 18.12.2014
 * Time: 18:01
 */

namespace app\components;

use Yii;
use app\abstracts\BaseComponent;
use yii\helpers\Url;

class Local extends BaseComponent
{
    public $lang;
    public $languageCookieName = 'vicesisters_language';
    public $dateTimeFormat;
    public $dateFormat;
    public $defaultLanguage = 'en';

    private static $_languages = [
        'ru' => 'Русский',
        'en' => 'English',
    ];

    private $_languagesList;

    public function init()
    {
        $this->lang = $this->getLanguage();
        if($this->dateTimeFormat === null)
            $this->dateTimeFormat = Yii::$app->params['dateTimeFormat'];
        if($this->dateFormat === null)
            $this->dateFormat = Yii::$app->params['dateFormat'];
    }

    public function setLanguage($lang = null)
    {
        if($lang === null || !isset(self::$_languages[$lang]))
            $lang = $this->lang;

        Yii::$app->language = $lang;
        $this->lang = $lang;
        Yii::$app->cookie->set($this->languageCookieName, $lang);
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        if($this->_languagesList === null){
            $this->_languagesList = [];
            foreach(self::$_languages as $code => $name){
                $this->_languagesList[] = [
                    'code' => $code,
                    'name' => Yii::t('base', $name),
                    'url' => Url::toRoute(['index/language', 'lang' => $code]),
                    'active' => $code === $this->lang
                ];
            }
        }

        return $this->_languagesList;
    }

    public function dateTime($format = null)
    {
        if($format === null)
            $format = $this->dateTimeFormat;
        return (new \DateTime())->format($format);
    }

    public function date($format = null)
    {
        if($format === null)
            $format = $this->dateFormat;
        return (new \DateTime())->format($format);
    }

    public function formatDateTime($dateTime, $format = null)
    {
        if($format === null)
            $format = $this->dateTimeFormat;
        return (new \DateTime($dateTime))->format($format);
    }

    public function formatDate($date, $format = null)
    {
        if($format === null)
            $format = $this->dateFormat;
        return (new \DateTime($date))->format($format);
    }

    public function addMonthToDate($date, $month)
    {
        $interval = \DateInterval::createFromDateString($date);
        $interval->m = (int)$month;

        return (new \DateTime($date))->add($interval)->format($this->dateTimeFormat);
    }

    public function addHoursToDate($date, $hours)
    {
        $interval = \DateInterval::createFromDateString($date);
        $interval->h = (int)$hours;

        return (new \DateTime($date))->add($interval)->format($this->dateTimeFormat);
    }

    public function timeDiff($end, $start = null)
    {
        if($start !== null)
            $startDate = new \DateTime($start);
        else
            $startDate = new \DateTime();

        $endDate = new \DateTime($end);

        return $this->formatDateInterval($endDate->diff($startDate, true));
    }

    /**
     * @return \DateTime
     */
    public function getLastDay()
    {
        $date = new \DateTime();
        $date->sub(\DateInterval::createFromDateString('1 day'));
        return $date;
    }

    /**
     * @return \DateTime
     */
    public function getLastWeek()
    {
        $date = new \DateTime();
        $date->sub(\DateInterval::createFromDateString('1 week'));
        return $date;
    }

    /**
     * @return \DateTime
     */
    public function getLastMonth()
    {
        $date = new \DateTime();
        $date->sub(\DateInterval::createFromDateString('1 month'));
        return $date;
    }

    /**
     * @return \DateTime
     */
    public function getLastYear()
    {
        $date = new \DateTime();
        $date->sub(\DateInterval::createFromDateString('1 year'));
        return $date;
    }

    public function getLastDayDate($format = null)
    {
        if($format === null)
            $format = $this->dateFormat;
        return $this->getLastDay()->format($format);
    }

    public function getLastDayDateTime($format = null)
    {
        if($format === null)
            $format = $this->dateTimeFormat;
        return $this->getLastDay()->format($format);
    }

    public function getLastWeekDate($format = null)
    {
        if($format === null)
            $format = $this->dateFormat;
        return $this->getLastWeek()->format($format);
    }

    public function getLastWeekDateTime($format = null)
    {
        if($format === null)
            $format = $this->dateTimeFormat;
        return $this->getLastWeek()->format($format);
    }

    public function getLastMonthDate($format = null)
    {
        if($format === null)
            $format = $this->dateFormat;
        return $this->getLastMonth()->format($format);
    }

    public function getLastMonthDateTime($format = null)
    {
        if($format === null)
            $format = $this->dateTimeFormat;
        return $this->getLastMonth()->format($format);
    }

    public function getLastYearDate($format = null)
    {
        if($format === null)
            $format = $this->dateFormat;
        return $this->getLastYear()->format($format);
    }

    public function getLastYearDateTime($format = null)
    {
        if($format === null)
            $format = $this->dateTimeFormat;
        return $this->getLastYear()->format($format);
    }

    private function formatDateInterval(\DateInterval $interval, $format = null)
    {
        if($format === null)
            $format = '%y:%m:%d (%a '.Yii::t('base', 'Дней').')';

        return $interval->format($format);
    }

    private function getLanguage()
    {
        if(Yii::$app->cookie->has($this->languageCookieName)){
            $lang = Yii::$app->cookie->get($this->languageCookieName);
        }else{
            $lang = $this->getLanguageFromBrowser();
            Yii::$app->cookie->set($this->languageCookieName, $lang);
        }

        return $lang;
    }

    private function getLanguageFromBrowser()
    {
        $language = $this->defaultLanguage;
        $list = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $languages = [];
        $langs = [
            'ru' => ['ru', 'uk'],
            'en' => ['en'],
        ];

        if($list){
            if(preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)){
                $languages = array_combine($list[1], $list[2]);

                foreach($languages as $n => $v){
                    $languages[$n] = $v ? $v : 1;
                }

                arsort($languages, SORT_NUMERIC);
            }
        }

        $tmpLangs = [];

        foreach($langs as $lang => $alias){
            if(is_array($alias)){
                foreach ($alias as $alias_lang) {
                    $tmpLangs[strtolower($alias_lang)] = strtolower($lang);
                }
            }else{
                $tmpLangs[strtolower($alias)] = strtolower($lang);
            }
        }

        foreach($languages as $l => $v){
            $s = strtok($l, '-');

            if(isset($tmpLangs[$s])){
                $language = $tmpLangs[$s];
                break;
            }
        }

        return $language;
    }
}