<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 15.11.14
 * Time: 12:22
 * To change this template use File | Settings | File Templates.
 */

namespace app\components;

use Yii;
use app\models\Host;
use yii\base\Component;

class FtpManager extends Component {

    private static $__instance = null;

    public $hostId = 1;

    public $user = 'vice_ftp';

    public $password = '051571';

    private $_host;

    private $_stream;

    /**
     * @return FtpManager|null
     */
    public static function getInstance()
    {
        if(self::$__instance === null)
            self::$__instance = new self();

        return self::$__instance;
    }

    /**
     * @param Host $host
     * @param string | null $user
     * @param string | null $password
     * @return FtpManager self::getInstance()|bool
     */
    public function connect(Host $host = null,$user = null,$password = null)
    {
        if($host){
            self::getInstance()->_host = $host;
        }else{
            self::getInstance()->_host = Host::findOne(['id' => self::getInstance()->hostId]);
        }

        self::getInstance()->_stream = ftp_connect(self::getInstance()->_host->name);

        if($user)
            self::getInstance()->user = $user;
        if($password)
            self::getInstance()->password = $password;

        if(ftp_login(self::getInstance()->_stream, self::getInstance()->user, self::getInstance()->password)){
            self::getInstance()->home();
            return self::getInstance();
        }

        return false;
    }

    /**
     * @param $remote
     * @param $local
     * @param int $mode
     * @return bool
     */
    public function put($remote, $local, $mode = FTP_BINARY)
    {
//        ftp_pasv(self::getInstance()->_stream, true);
        return ftp_put(self::getInstance()->_stream, $remote, $local, $mode);
    }

    /**
     * @param $path
     * @throws \Exception
     */
    public function cd($path)
    {
        if(!ftp_chdir(self::getInstance()->_stream, $path))
            throw new \Exception('Can not change directory');
    }

    public function home()
    {
        if(self::getInstance()->dir() == '/')
            self::getInstance()->cd(self::getInstance()->_host->name);
    }

    /**
     * @param $path
     * @throws \Exception
     */
    public function mkdir($path)
    {
        if(!ftp_mkdir(self::getInstance()->_stream, $path))
            throw new \Exception('Can not create directory');
    }

    /**
     * @param $path
     * @return bool
     * @throws \Exception
     */
    public function del($path)
    {
        if(!ftp_delete(self::getInstance()->_stream, $path))
            throw new \Exception('Can not delete file');

        return true;
    }

    /**
     * @return bool
     */
    public function close()
    {
        return ftp_close(self::getInstance()->_stream);
    }

    /**
     * @return string
     */
    public function dir()
    {
        return ftp_pwd(self::getInstance()->_stream);
    }

    /**
     * @param string $dir
     * @return bool
     */
    public function isDir($dir)
    {
        return ftp_pwd($this->_stream) !== false && @ftp_chdir($this->_stream, $dir);
    }
}