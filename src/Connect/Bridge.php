<?php


namespace Connect;

use Symfony\Component\Console\Output\OutputInterface;

class Bridge
{

    const AUTH_PASSWORD = 'password';
    const AUTH_KEYFILE = 'keyfile';

    /**
     * @var int
     */
    protected $timeout = null;

    /**
     * @var string
     */
    protected $hostname = null;

    /**
     * @var int
     */
    protected $port = null;

    /**
     * @var string
     */
    protected $auth = null;

    /**
     * @var string
     */
    protected $username = null;

    /**
     * @var string
     */
    protected $password = null;

    /**
     * @var string
     */
    protected $passwordFile = null;

    /**
     * @var string
     */
    protected $keyfile = null;

    /**
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * @param OutputInterface $output
     */
    public function __construct($output)
    {
        $this->output = $output;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @param string $auth
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param string $passwordFile
     */
    public function setPasswordfile($passwordFile)
    {
        $this->passwordFile = $passwordFile;
    }

    /**
     * @param string $keyfile
     */
    public function setKeyfile($keyfile)
    {
        $this->keyfile = $keyfile;
    }

    /**
     * @return \Net_SSH2
     */
    public function ssh()
    {
        $port = isset($this->port) ? $this->port : 22;
        $timeout = isset($this->timeout) ? $this->timeout : 10;
        $connector = new \Net_SSH2($this->hostname, $port, $timeout);
        return $this->auth($connector);
    }

    /**
     * @return \Net_SCP
     */
    public function scp()
    {
        $ssh = $this->ssh();
        return new \Net_SCP($ssh);
    }

    /**
     * @return \Net_SFTP
     */
    public function sftp()
    {
        $port = isset($this->port) ? $this->port : 22;
        $timeout = isset($this->timeout) ? $this->timeout : 10;
        $connector = new \Net_SFTP($this->hostname, $port, $timeout);
        return $this->auth($connector);
    }

    /**
     * @return string
     */
    protected function getKeyfile()
    {
        return file_get_contents($this->getRealPath($this->keyfile));
    }


    /**
     * @return string
     */
    protected function getPassword()
    {
        if ($this->passwordFile) {
            $password = trim(file_get_contents($this->getRealPath($this->passwordFile)), "\t\n\r\0\x0B");
        } else {
            $password = $this->password;
        }
        return $password;
    }

    /**
     * fixes home path notation
     *
     * @param string $file
     * @return string
     */
    protected function getRealPath($file)
    {
        if (isset($file) && '~' === $file{0}) {
            $file = getenv('HOME') . substr($file, 1);
        }
        return $file;
    }

    /**
     * @param \Net_SSH2|\Net_SFTP $connector
     * @return \Net_SSH2|\Net_SFTP
     * @throws \Exception
     */
    protected function auth($connector)
    {
        switch ($this->auth) {
            case self::AUTH_KEYFILE:
                $password = new \Crypt_RSA();
                if (!is_null($this->getPassword())) {
                    $password->setPassword($this->getPassword());
                }
                $password->loadKey($this->getKeyfile());
                break;
            case self::AUTH_PASSWORD:
                $password = $this->getPassword();
                break;
            default:
                break;
        }
        if (!isset($password)) {
            $loggedIn = $connector->login($this->username);
        } else {
            $loggedIn = $connector->login($this->username, $password);
        }
        if (!$loggedIn) {
            throw new \Exception(sprintf(
                'SSH authentication (%s) with %s on %s:%s failed!',
                $this->auth,
                $this->username,
                $this->hostname,
                $this->port
            ));
        }
        return $connector;
    }
}
