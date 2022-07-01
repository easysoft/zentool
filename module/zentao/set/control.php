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
        $userSet     = array();
        $tryTime     = 0;
        $fields      = $this->config->os == 'windows' ? $this->config->set->fields->windows : $this->config->set->fields->default;
        $inputFileds = explode(',', $fields);
        foreach($inputFileds as $field)
        {
            $this->output($this->lang->set->{$field . 'Tip'});
            while(true)
            {
                if($tryTime > 2) return $this->output($this->lang->set->tryTimeLimit, 'err');

                $inputValue = $this->readInput();
                $result     = $this->set->checkInput($field, $inputValue);
                if($result)
                {
                    $tryTime  = 0;
                    $saveName = $this->config->set->configNames[$field];
                    $userSet[$saveName] = $result;
                    break;
                }

                $tryTime++;
                $this->output(sprintf($this->lang->set->{$field . 'NotReal'}, $inputValue), 'err');
            }
        }

        if(!$this->setUserConfigs($userSet)) return fwrite(STDOUT, $this->lang->set->noWriteAccess);

        $this->output($this->lang->set->saveSuccess);
    }
}
