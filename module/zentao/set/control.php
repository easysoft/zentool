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
        fwrite(STDOUT, $this->lang->set->inputDir);

        $realPath = false;
        $tryTime  = 1;
        while(!$realPath)
        {
            if($tryTime > 3) return fwrite(STDERR, $this->lang->set->tryTimeLimit);

            $path = rtrim(trim(fgets(STDIN)), DS);
            if(!$path) continue;

            $filePath   = $path . DS . 'config' . DS . 'my.php';
            $fileExists = file_exists($filePath) ? 1 : (file_exists($this->config->runDir . DS . $path) ? 2 : 0);
            if($fileExists)
            {
                if($this->setUserConfigs(array('zt_webDir' => $fileExists == 2 ? realpath($this->config->runDir . DS . $path) : $path)))
                {
                    $realPath = true;
                    return fwrite(STDOUT, $this->lang->saveSuccess);
                }
                else
                {
                    return fwrite(STDERR, $this->lang->set->noWriteAccess);
                }
            }
            else
            {
                $tryTime++;
                fwrite(STDERR, sprintf($this->lang->set->dirNotExists, $path));
            }
        }
    }
}
