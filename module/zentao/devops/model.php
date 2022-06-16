<?php
/**
 * The model file of devops module of Z.
 *
 * @copyright   Copyright 2009-2022 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yanyi Cao <caoyanyi@easycorp.ltd>
 * @package     devops
 * @version     $Id: model.php 5028 2022-05-18 10:30:41Z caoyanyi@easycorp.ltd $
 * @link        http://www.zentao.net
 */
?>
<?php
class devopsModel extends model
{
    /**
     * Create api url.
     *
     * @param  string $entry
     * @access private
     * @return string
     */
    private function createApiUrl($entry = '', $params = array(), $url = '')
    {
        if(!$url) $url = $this->config->zt_url;
        return $url . '/api.php/v1/' . $entry . '?' . http_build_query($params);
    }

    /**
     * Check url.
     *
     * @param  string $url
     * @access public
     * @return void
     */
    public function checkUrl($url = '')
    {
        if(!preg_match('/^(?=^.{3,255}$)(http(s)?:\/\/)?(www\.)?[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+(:\d+)*(\/\w+)*$/', $url)) return false;

        $config = $this->http($url . '/index.php?mode=getconfig');
        if(!isset($config->version)) return false;

        return $config;
    }

    /**
     * Login
     *
     * @param  string $url
     * @param  string $account
     * @param  string $password
     * @access public
     * @return void
     */
    public function login($url = '', $account = '', $password = '')
    {
        $data = array(
            'account'  => $account,
            'password' => $password
        );
        $user = $this->http($this->createApiUrl('tokens', array(), $url), $data);
        if(isset($user->token)) return $user->token;

        if(isset($user->error) and (mb_substr_count($user->error, '解锁') > 0 or mb_substr_count($user->error, 'unlock') > 0))
        {
            $message = $this->lang->devops->loginLimit . PHP_EOL;
            if($this->config->os == 'windows') $message = iconv("UTF-8", "GB2312", $message);
            fwrite(STDERR, $message);
            die;
        }
        return false;
    }

    /**
     * Api check pipeline.
     *
     * @param  string $repoUrl
     * @param  string $pipeline
     * @access public
     * @return void
     */
    public function apiCheckPipeline($repoUrl = '', $pipeline = '')
    {
        /* Get repos. */
        $params = array('repoUrl' => $repoUrl);
        $header = array('token:' . $this->config->zt_token);
        $repos  = $this->http($this->createApiUrl('repos', $params), null, array(), $header);
        if(!isset($repos->repos) or empty($repos->repos))
        {
            $message = $this->lang->devops->repoNotFound . PHP_EOL;
            if($this->config->os == 'windows') $message = iconv("UTF-8", "GB2312", $message);
            fwrite(STDERR, $message);
            die;
        }

        /* Get jobs. */
        $params = array('pipeline' => $pipeline, 'engine' => 'jenkins');
        $header = array('token:' . $this->config->zt_token);
        $jobs   = $this->http($this->createApiUrl('jobs', $params), null, array(), $header);
        if(!isset($jobs->jobs) or empty($jobs->jobs)) return false;

        $repoIDs = array_column($repos->repos, 'id');
        foreach($jobs->jobs as $job)
        {
            if(in_array($job->repo, $repoIDs)) return true;
        }

        return false;
    }

    /**
     * Get repo url.
     *
     * @access public
     * @return array
     */
    public function getRepoUrl()
    {
        $command = "git tag 2>&1";
        chdir($this->config->runDir);
        exec($command, $output, $result);
        if($result) return array('result' => false, 'message' => $this->lang->devops->notRepository);

        $command = "git remote -v";
        exec($command, $remote);
        preg_match('/(http(s)?:\/\/)?(www\.)?[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+(:\d+)*(\/\w+)*\.git/', $remote[1], $matches);

        return array('result' => true, 'url' => $matches[0]);
    }

    /**
     * Get remote branch.
     *
     * @param  string $branch
     * @access public
     * @return array
     */
    public function getRemoteBranch($branch)
    {
        /* Get source branch. */
        $cmdStatus = 'git status';
        chdir($this->config->runDir);
        exec($cmdStatus, $status);
        $sourceBranch = preg_replace("/[a-zA-Z\s]+'([a-zA-z\w\/]+)'[a-zA-Z\s0-9-,\.]+/", '$1', $status[1]);
        if(!$sourceBranch) return array('result' => false, 'message' => $this->lang->devops->noTracking);

        /* Get target branch. */
        $targetBranch = '';
        $cmdBranch    = 'git branch -r';
        exec($cmdBranch, $allBranch);
        foreach($allBranch as $remoteBranch)
        {
            $remoteBranch = '/' . trim($remoteBranch);
            $branchLength = strlen($branch) + 1;
            if(substr($remoteBranch, 0 - $branchLength) == '/' . $branch)
            {
                $targetBranch = trim($remoteBranch, '/');
                break;
            }
        }
        if(!$targetBranch) return array('result' => false, 'message' => sprintf($this->lang->devops->noTargetBranch, $branch));

        return array('result' => true, 'sourceBranch' => $sourceBranch, 'targetBranch' => $targetBranch);
    }

    /**
     * Check toekn access.
     *
     * @param  string $token
     * @access public
     * @return bool
     */
    public function checkToeknAccess($token = '')
    {
        /* Get rights. */
        $params = array('fields' => 'rights');
        $header = array('token:' . $token);
        $user   = $this->http($this->createApiUrl('user', $params), null, array(), $header);
        if(empty($user) or !isset($user->rights)) return false;

        $rights = $user->rights;
        if(isset($rights->admin) and $rights->admin) return true;
        if(!isset($rights->rights->job->exec) or !$rights->rights->job->exec) return false;
        if(!isset($rights->rights->job->browse) or !$rights->rights->job->browse) return false;
        if(!isset($rights->rights->repo->maintain) or !$rights->rights->repo->maintain) return false;
        if(!isset($rights->rights->mr->create) or !$rights->rights->mr->create) return false;

        return true;
    }
}
