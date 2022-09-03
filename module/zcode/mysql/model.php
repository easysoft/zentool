<?php
/**
 * The model file of mysql module of Z.
 *
 * @copyright   Copyright 2009-2022 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Xin Zhou <zhouxin@easycorp.ltd>
 * @package     mysql
 * @version     $Id: model.php 5028 2022-05-18 10:30:41Z caoyanyi@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class mysqlModel extends model
{
    /**
     * connect DB by PDO.
     *
     * @param  string  $host
     * @param  string  $dbname
     * @param  string  $port
     * @param  string  $user
     * @param  string  $password
     * @access public
     * @return object
     */
    public function connectDB($host, $dbname, $port, $user, $password)
    {
        $dsn = "mysql:host={$host}; port={$port}; dbname={$dbname}";
        $dbh = new PDO($dsn, $user, $password);
        return $dbh;
    }

    /**
     * Check user input.
     *
     * @param  string $field
     * @param  string $value
     * @access public
     * @return bool
     */
    public function checkInput($field = '', $value = '')
    {
        if(empty($value)) return false;

        if(method_exists($this, 'check' . $field)) return $this->{'check' . $field}($value);
        return true;
    }

    public function desc($table)
    {
        return $fields;
    }

    public function parseLang($fields)
    {
        return $fields;
    }

    public function getFormConfig($fields)
    {
        return $fields;
    }
}

