<?php
/**
 * The control file of set module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class set extends control
{
    /**
     * The index page.
     *
     * @param  array $params
     * @access public
     * @return void
     */
    public function entry($params)
    {
        $userSet = array();

        /* Check path. */
        fwrite(STDOUT, $this->lang->set->dirTip);
        while(true)
        {
            $dir  = $this->readInput();
            $path = rtrim(trim($dir), DS);
            if(!$path) continue;

            $configPath = $path . DS . 'config' . DS . 'my.php';
            $realPath   = realpath($configPath);
            if(!$realPath) $realPath = realpath($this->config->runDir . DS . $configPath);

            if($realPath)
            {
                $userSet['zt_webDir'] = $realPath;
                break;
            }

            fwrite(STDERR, sprintf($this->lang->set->dirNotExists, $path));
        }

        /* Check url. */
        fwrite(STDOUT, $this->lang->set->urlTip);
        while(true)
        {
            $url = $this->readInput();
            $url = rtrim(trim($url), '/');
            if(!$url) continue;

            fwrite(STDOUT, $this->lang->set->checking);
            $config = $this->set->checkUrl($url);
            if($config)
            {
                $userSet['zt_host'] = $url;
                break;
            }

            fwrite(STDERR, sprintf($this->lang->set->urlInvalid, $url));
        }

        /* Check account and password. */
        while(true)
        {
            fwrite(STDOUT, $this->lang->set->accountTip);
            $account = $this->readInput();
            if(!$account) continue;

            fwrite(STDOUT, $this->lang->set->pwdTip);
            $password = $this->readInput();
            if(!$password) continue;

            fwrite(STDOUT, $this->lang->set->logging);
            $token = $this->set->login($url, $account, $password);
            if($token)
            {
                $userSet['zt_account']      = $account;
                $userSet['zt_password']     = md5($account);
                $userSet['zt_token']        = $token;
                $userSet['zt_tokenExpired'] = time() + $config->expiredTime - 100;
                if(!$this->setUserConfigs($userSet)) return fwrite(STDOUT, $this->lang->set->noWriteAccess);
                break;
            }

            fwrite(STDERR, $this->lang->set->loginFailed);
        }

        fwrite(STDOUT, $this->lang->set->saveSuccess);
    }
}
