<?php
/**
 * The control file of index module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class devops extends control
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
        if(empty($params)) return $this->printHelp();

        $method = key($params);
        if(method_exists($this, $method))
        {
            if(isset($this->config->devops->paramKey[$method])) $params = array($this->config->devops->paramKey[$method] => $param);
            return $this->$method($params);
        }
        return $this->printHelp();
    }

    /**
     * Print help.
     *
     * @param  string $type
     * @access public
     * @return void
     */
    public function printHelp($type = 'devops')
    {
        return $this->output($this->lang->devops->help->$type);
    }

    /**
     * Merge request.
     *
     * @param  int    $params
     * @access public
     * @return void
     */
    public function mr($params)
    {
        if(empty($params) or empty($params['branch']) or isset($params['help'])) return $this->printHelp('mr');

        /* Get repo url. */
        $response = $this->devops->getRepoUrl();
        if(!$response['result']) return $this->output($response['message'], 'err');
        $repoUrl = $response['url'];

        $remoteBranch = $this->devops->getRemoteBranch($params['branch']);
        if(!$remoteBranch['result']) return $this->output($remoteBranch['message'], 'err');

        $this->login();

        /* Check pipeline. */
        $job = $this->checkPipeline($repoUrl);

        /* Get diffs. */
        chdir($this->config->runDir);
        $diffs = shell_exec('git diff ' . $remoteBranch['targetBranch']);
        if(!$diffs) return $this->output($this->lang->devops->noChanges, 'err');

        /* Create MR to zentao. */
        $response = $this->devops->createMR($job->repo, $job->id, $remoteBranch['sourceBranch'], $remoteBranch['targetBranch'], $diffs);
        if(isset($response->error)) return $this->output($this->lang->devops->createFail, 'err');
        return $this->output($this->lang->devops->createSuccess);
    }

    /**
     * Check pipeline.
     *
     * @param  string $repoUrl
     * @access public
     * @return void
     */
    public function checkPipeline($repoUrl = '')
    {
        $needPipeline = false;

        if(empty($this->config->zt_pipeline)) $needPipeline = true;
        if(!$needPipeline)
        {
            $job = $this->devops->apiCheckPipeline($repoUrl, $this->config->zt_pipeline);
            if(!$job) $needPipeline = true;
        }

        if($needPipeline)
        {
            $this->output($this->lang->devops->pipelineTip);
            while(true)
            {
                $pipeline = $this->readInput();
                if(!$pipeline) continue;

                $this->output($this->lang->devops->checking);
                $job = $this->devops->apiCheckPipeline($repoUrl, $pipeline);
                if($job)
                {
                    if(!$this->setUserConfigs(array('zt_pipeline' => $pipeline))) return $this->output($this->lang->devops->noWriteAccess);
                    break;
                }

                $this->output(sprintf($this->lang->devops->pipelineFail, $pipeline), 'err');
            }
        }

        return $job;
    }

    /**
     *  Check user logged.
     *
     * @access public
     * @return void
     */
    public function login()
    {
        $needLogin = false;
        if(empty($this->config->zt_url) or empty($this->config->zt_account) or empty($this->config->zt_password)) $needLogin = true;
        if(!$needLogin)
        {
            $config = $this->devops->checkUrl($this->config->zt_url);
            if($config)
            {
                $token = $this->devops->login($this->config->zt_url, $this->config->zt_account, $this->config->zt_password);
                if($token)
                {
                    if($this->devops->checkToeknAccess($token))
                    {
                        $userSet['zt_token']        = $token;
                        $userSet['zt_tokenExpired'] = time() + $config->expiredTime - 100;
                        if(!$this->setUserConfigs($userSet)) return $this->output($this->lang->devops->noWriteAccess);
                    }
                    else
                    {
                        $needLogin = true;
                        $this->output($this->lang->devops->noAccess, 'err');
                    }
                }
                else
                {
                    $needLogin = true;
                }
            }
            else
            {
                $needLogin = true;
            }
        }

        /* Check official website account. */
        if($needLogin)
        {
            /* Check url. */
            $this->output($this->lang->devops->urlTip);
            while(true)
            {
                $url = $this->readInput();
                $url = rtrim(trim($url), '/');
                if(!$url) continue;

                $this->output($this->lang->devops->checking);
                $config = $this->devops->checkUrl($url);
                if($config)
                {
                    $userSet['zt_url'] = $url;
                    break;
                }

                $this->output(sprintf($this->lang->devops->urlInvalid, $url), 'err');
            }

            /* Check account and password. */
            while(true)
            {
                $showError = true;
                $this->output($this->lang->devops->accountTip);
                $account = $this->readInput();
                if(!$account) continue;

                $this->output($this->lang->devops->pwdTip);
                $password = $this->readInput();
                if(!$password) continue;

                $this->output($this->lang->devops->logging);
                $token = $this->devops->login($url, $account, $password);
                if($token)
                {
                    if($this->devops->checkToeknAccess($token))
                    {
                        $this->output(sprintf($this->lang->devops->dirNotExists, $path), 'err');
                        $userSet['zt_account']      = $account;
                        $userSet['zt_password']     = md5($password);
                        $userSet['zt_token']        = $token;
                        $userSet['zt_tokenExpired'] = time() + $config->expiredTime - 100;
                        if(!$this->setUserConfigs($userSet)) return $this->output($this->lang->devops->noWriteAccess);
                        break;
                    }
                    $showError = false;
                    $this->output($this->lang->devops->noAccess, 'err');
                }

                if($showError) $this->output($this->lang->devops->loginFailed, 'err');
            }
        }
    }
}
