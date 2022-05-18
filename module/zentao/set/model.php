<?php
/**
 * The model file of set module of Z.
 *
 * @copyright   Copyright 2009-2022 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yanyi Cao <caoyanyi@easycorp.ltd>
 * @package     set
 * @version     $Id: model.php 5028 2022-05-18 14:30:41Z caoyanyi@easycorp.ltd $
 * @link        http://www.zentao.net
 */
?>
<?php
class setModel extends model
{
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
        $user = $this->http($url . '/api.php/v1/tokens', $data);
        if(!isset($user->token)) return false;

        return $user->token;
    }
}
