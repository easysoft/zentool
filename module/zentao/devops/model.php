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
    private function createApiUrl($entry = ''i, $url = '')
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
}
