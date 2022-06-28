<?php
/**
 * The control file of basic module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class basic extends control
{
    /**
     * The entry page.
     *
     * @param  array $params
     * @access public
     * @return void
     */
    public function entry($params)
    {
        $this->diff($params);
    }

    public function diff($params)
    {
        $userSet     = array();
        $inputFileds = explode(',', $this->config->basic->diff->fields);
        foreach($inputFileds as $field)
        {
            $this->output($this->lang->basic->diff->{$field . 'Tip'});
            while(true)
            {
                $inputValue = $this->readInput();
                $result     = $this->basic->checkInput($field, $inputValue);
                if($result)
                {
                    $userSet[$field] = $result;
                    break;
                }

                $this->output(sprintf($this->lang->basic->diff->pathNotReal, $inputValue), 'err');
            }
        }

        $source  = $userSet['source'];
        $target  = $userSet['target'];
        $tmpPath = $target;

        $zfile = $this->app->loadClass('zfile');
        $files = $zfile->readDir($source);
        if($files) $tmpPath = $this->basic->backupAndCover($target, $source);

        $sourceLen = mb_strlen($source);
        $filesLen  = count($files);
        $fileIndex = 0;
        while($fileIndex <= $filesLen)
        {
            $file        = $files[$fileIndex];
            $compareFile = $tmpPath . substr($file, $sourceLen);
            if(!file_exists($compareFile))
            {
                array_splice($files, $fileIndex, 1);
                $filesLen--;
                continue;
            }

            $cmd = 'sdiff -s ' . $file . ' ' . $compareFile . ' | colordiff';
            $this->output($cmd);

            $diff = shell_exec($cmd);
            if(empty($diff))
            {
                array_splice($files, $fileIndex, 1);
                $filesLen--;
                continue;
            }
            $this->output($diff);

            while(true)
            {
                $input = $this->readInput('Use p and n to change file, q to quit. (default: n):');
                if($input == 'p')
                {
                    if($fileIndex < 2)
                    {
                        $this->output('This is the first file!', 'err');
                        continue;
                    }
                    $fileIndex--;
                    break;
                }
                elseif($input == 'q')
                {
                    $this->output('User quit.');
                    die;
                }
                elseif($fileIndex >= $filesLen)
                {
                    $this->output('This is the last file!', 'err');
                    continue;
                }
                $fileIndex++;
                break;
            }
        }

        $zfile->removeDir($tmpPath);
    }
}
