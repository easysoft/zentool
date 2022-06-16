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
    private function createApiUrl($entry = '', $url = '')
    {
        if(!$url) $url = $this->config->zt_url;
        return $url . '/api.php/v1/' . $entry;
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
        $user = $this->http($this->createApiUrl('tokens', $url), $data);
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
     * Api get pipelines.
     *
     * @param  string $repoUrl
     * @access public
     * @return void
     */
    public function apiGetPipelines($repoUrl = '')
    {
        $result = $this->http($this->createApiUrl('pipelines'));
        if(!isset($result->pipelines)) return false;

        return $result->pipelines;
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
}
