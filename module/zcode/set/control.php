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
    public function entry($item = '', $value = '')
    {
        $this->fixBasicConfig();
        $userSet = array();
        if(!empty($item))
        {
            $userSet = array($item => $value);
            return $this->setUserConfigs($userSet);
        }

        $this->output($this->lang->set->inputTips->name);
        while(true)
        {
            $input = $this->readInput();

            if($input == ':w') break;

            if(strpos($input, '=') === false)
            {
                $this->output($this->lang->set->inputTips->value);

                $item  = trim($input);
                $value = $this->readInput();
            }
            else
            {
                $this->output("Input: $input");
                list($item, $value) = explode(" = ", $input);
            }

            $userSet[$item] = $value;
        }

        if(!$this->setUserConfigs($userSet)) return fwrite(STDOUT, $this->lang->set->noWriteAccess);

        $this->output($this->lang->set->saveSuccess);
    }

    /**
     * Fix basic config
     *
     * @access public
     * @return mixed
     */
    public function fixBasicConfig()
    {
        $basicItems  = array('project', 'module', 'author', 'email');
        $basicConfig = array();

        foreach($basicItems as $item)
        {
            if(!isset($this->config->$item))
            $basicConfig[$item] = $this->readInput('Please input ' . $item . ' : ');
        }
        $this->setUserConfigs($basicConfig);
    }
}
