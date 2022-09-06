<?php
/**
 * The control file of mysql module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class mysql extends control
{
    public function entry($params)
    {
        $mysqlConfig = array();
        $tryTime     = 0;
        $fields      = $this->config->set->fields->default;
        $inputFileds = explode(',', $fields);
        foreach($inputFileds as $field)
        {
            foreach($this->config->zcode->mysql->dataSource as $field)
            {
                while(true)
                {
                    if($tryTime > 2) return $this->output($this->lang->set->tryTimeLimit, 'err');

                    $this->output($field . ':');
                    $inputValue = $this->readInput();
                    $result     = $this->mysql->checkInput($field, $inputValue);
                    if($inputValue and $result === true)
                    {
                        $tryTime = 0;
                        $mysqlConfig[$field] = $inputValue;
                        break;
                    }
                    $tryTime++;
                }
            }
        }

        $dbh = $this->mysql->connectDB($mysqlConfig['host'], $mysqlConfig['dbname'], $mysqlConfig['port'], $mysqlConfig['user'], $mysqlConfig['password']);

        if(!$this->setUserConfigs($mysqlConfig)) return fwrite(STDOUT, $this->lang->set->noWriteAccess);

        $this->output($this->lang->set->saveSuccess);
    }
}
