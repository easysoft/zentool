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
        fwrite(STDOUT, $this->lang->set->dirTip);
        $tryTimes = 0;
        while(true)
        {
            if($tryTime > 2) return fwrite(STDERR, $this->lang->set->tryTimeLimit);
            $dir  = $this->readInput();
            $path = rtrim(trim($dir), DS);
            if(!$path) continue;

            $configPath = $path . DS . 'config' . DS . 'my.php';
            $realPath   = realpath($configPath);
            if(!$realPath) $realPath = realpath($this->config->runDir . DS . $configPath);

            if($realPath)
            {
                $userSet['zt_webDir'] = $realPath;
                break;
            }

            $tryTime++;
            fwrite(STDERR, sprintf($this->lang->set->dirNotExists, $path));
        }

        fwrite(STDOUT, $this->lang->set->saveSuccess);
    }
}
