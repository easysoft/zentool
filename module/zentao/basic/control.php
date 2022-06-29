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
        if(empty($params['new']) or empty($params['diff'])) return $this->output($this->lang->basic->diff->help);

        $source = helper::getRealPath($params['new']);
        $target = helper::getRealPath($params['diff']);
        if(!$source) return $this->output(sprintf($this->lang->basic->diff->pathNotReal, $params['new']), 'err');
        if(!$target) return $this->output(sprintf($this->lang->basic->diff->pathNotReal, $params['diff']), 'err');

        $zfile = $this->app->loadClass('zfile');
        $files = $zfile->readDir($source);
        if($files) $this->basic->backupAndCover($target);

        $changeFiles = array();
        $diffCmds    = array();
        $sourceLen   = mb_strlen($source);
        $filesLen    = count($files);
        $fileIndex   = 0;
        while($fileIndex <= $filesLen)
        {
            $file        = $files[$fileIndex];
            $compareFile = $target . substr($file, $sourceLen);
            if(empty($file) or is_dir($file) or !file_exists($compareFile))
            {
                if($file) $changeFiles[] = '+ ' . $file;
                array_splice($files, $fileIndex, 1);
                $filesLen--;
                continue;
            }

            $cmd = 'diff -y --suppress-common-lines -W 200 ' . $file . ' ' . $compareFile;
            if(isset($params['view'])) $this->output($cmd);

            $diff = shell_exec($cmd);
            if(empty($diff))
            {
                array_splice($files, $fileIndex, 1);
                $filesLen--;
                continue;
            }
            $changeFiles[] = '* ' . $file;
            $diffCmds[]    = 'vimdiff ' . $file . ' ' . $compareFile;

            if(isset($params['view']))
            {
                $this->output($diff);

                while(true)
                {
                    $input = $this->readInput('Use p and n to change file, q to quit. (default: n):');
                    if($input == 'p')
                    {
                        if($fileIndex < 1)
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
            else
            {
                $fileIndex++;
            }
        }

        if(!isset($params['view']))
        {
            $txt = '';
            foreach($changeFiles as $file) $txt .= '# ' . $file . PHP_EOL;

            if($diffCmds) $txt .= '# Diff commands:' . PHP_EOL;
            foreach($diffCmds as $cmd)     $txt .= $cmd . PHP_EOL;

            if($txt)
            {
                $txt      = '#!/bin/bash' . PHP_EOL . $txt;
                $diffPath = dirname($this->config->userConfigFile, 2) . DS . 'diff' . date('YmdH') . '.sh';
                $diffFile = @fopen($diffPath, "w");
                fwrite($diffFile, $txt);
                fclose($diffFile);

                $this->output('The changes saved to ' . $diffPath);
            }
        }
    }
}
