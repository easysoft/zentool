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

            $inputValue = $this->readInput();
            $path = rtrim(trim($inputValue), DS);
            if(!$path) continue;

            $runPath    = $this->config->runDir . DS . $path;
            $path       = realpath($path);
            $filePath   = $path . DS . 'config' . DS . 'my.php';
            $fileExists = file_exists($filePath) ? 1 : (file_exists($runPath) ? 2 : 0);
            if($fileExists)
            {
                if($this->setUserConfigs(array($this->config->set->dirParam => $fileExists == 2 ? realpath($runPath) : $path)))
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
