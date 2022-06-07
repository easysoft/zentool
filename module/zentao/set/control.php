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
        $userSet = array();

        /* Check path. */
        $this->output($this->lang->set->dirTip);
        $tryTimes = 0;
        while(true)
        {
            if($tryTime > 2) return $this->output($this->lang->set->tryTimeLimit, 'err');
            $dir  = $this->readInput();
            $path = rtrim(trim($dir), DS);
            if(!$path) continue;

            $configPath = $path . DS . 'config' . DS . 'my.php';
            $realPath   = helper::getRealPath($configPath);

            if($realPath)
            {
                $userSet['zt_webDir'] = dirname(dirname($realPath));
                if(!$this->setUserConfigs($userSet)) return fwrite(STDOUT, $this->lang->set->noWriteAccess);
                break;
            }

            $tryTime++;
            $this->output(sprintf($this->lang->set->dirNotExists, $path), 'err');
        }

        $this->output($this->lang->set->saveSuccess);
    }
}
