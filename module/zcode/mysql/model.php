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
     * Generate config according to table.
     *
     * @param  string  $table
     * @access public
     * @return string
     */
    public function parseConfigByTable($table)
    {
        $fields = $this->dbh->query("desc $table")->fetchAll();

        $config = "<?php\n";
        $configTypes = array('common', 'action', 'required', 'search');

        foreach($configTypes as $type)
        {
            $config .= "/* Create by zcode : $type items start. */\n";
            if(is_callable(array($this, "parse{$type}Config")))$config .= call_user_func(array($this, "parse{$type}Config"), $fields);
            $config .= "/* Create by zcode : $type items end. */\n\n";
        }

        return $config;
    }

    /**
     * Parse common config.
     *
     * @param  array    $fields
     * @access public
     * @return string
     */
    public function parseCommonConfig($fields)
    {
        $config = '';
        foreach($fields as $field)
        {
            $control = 'input';
            if(strpos($field->Type, 'enum') !== false) $control = 'select';
            if(strpos($field->Type, 'date') !== false) $control = 'date';
            if(strpos($field->Type, 'text') !== false) $control = 'text';

            $config .= "\$config->fields['$field->Field']['name']    = '$field->Field';\n";
            $config .= "\$config->fields['$field->Field']['label']   = '$field->Field';\n";
            $config .= "\$config->fields['$field->Field']['control'] = '$control';\n";
            $config .= "\$config->fields['$field->Field']['options'] = '';\n";
            $config .= "\$config->fields['$field->Field']['default'] = '';\n\n";
        }

        return $config;
    }

    /**
     * Parse action config from fields.
     *
     * @param  array    $fields
     * @access public
     * @return string
     */
    public function parseActionConfig($fields)
    {
        $actions = array('view', 'create');
        $config = '';

        foreach($actions as $action)
        {
            $config .= "\$config->actions = new stdclass;\n";
            $config .= "/* Items for {$action} action .*/\n";
            $config .= "\$config->actions->{$action} = array();\n";
            foreach($fields as $field)
            {
                $control = 'input';
                if(strpos($field->Type, 'enum') !== false) $control = 'select';
                if(strpos($field->Type, 'date') !== false) $control = 'date';
                if(strpos($field->Type, 'text') !== false) $control = 'text';

                $config .= "\$config->actions->{$action}['{$field->Field}']['name']         = '$field->Field';\n";
                $config .= "\$config->actions->{$action}['{$field->Field}']['label']        = '$field->Field';\n";
                $config .= "\$config->actions->{$action}['{$field->Field}']['control']      = '$control';\n";
                $config .= "\$config->actions->{$action}['{$field->Field}']['options']      = '';\n";
                $config .= "\$config->actions->{$action}['{$field->Field}']['show']         = 1;\n";
                $config .= "\$config->actions->{$action}['{$field->Field}']['width']        = 'auto';\n";
                $config .= "\$config->actions->{$action}['{$field->Field}']['position']     = 1;\n";
                $config .= "\$config->actions->{$action}['{$field->Field}']['defaultValue'] = '';\n";
                $config .= "\$config->actions->{$action}['{$field->Field}']['rules']        = '';\n\n";
            }
            $config .= "/* Items for {$action} end.*/\n";
        }
        return $config;
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

    public function parseLang($table)
    {
        $fields = $this->getFields($table);
    }

    public function getFormConfig($fields)
    {
        return $fields;
    }

    public function getFields($table)
    {
        return  $this->dbh->query("desc $table")->fetchAll();
    }
}

