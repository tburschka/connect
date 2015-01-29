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
     * @param \Net_SSH2|\Net_SFTP $connector
     * @return \Net_SSH2|\Net_SFTP
     * @throws \Exception
     */
    protected function auth($connector)
    {
        switch ($this->auth) {
            case self::AUTH_KEYFILE:
                $password = new \Crypt_RSA();
                if (!is_null($this->password)) {
                    $password->setPassword($this->password);
                }

                // fixes home path notation
                if ($this->keyfile{0} === '~') {
                    $keyfile = getenv('HOME') . substr($this->keyfile, 1);
                } else {
                    $keyfile = $this->keyfile;
                }

                $password->loadKey(file_get_contents($keyfile));
                break;
            case self::AUTH_PASSWORD:
            default:
                $password = $this->password;
                break;
        }
        if (is_null($password)) {
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
