<?php
/**
 * The control file of apicheck module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class apicheck extends control
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
            $this->output($this->lang->apicheck->webDirTip);
            $checkRes = true;
            while(true)
            {
                $inputValue = $this->readInput();
                $result     = $this->apicheck->checkInput($inputValue);
                if(!is_bool($result))
                {
                    $checkRes = false;
                    foreach($result as $line) $this->output(sprintf($this->lang->apicheck->checkFail, $line['filePath'], $line['line']), 'err');
                    break;
                }

                if(!$result)
                {
                    $checkRes = false;
                    $this->output(sprintf($this->lang->apicheck->webDirNotReal, $inputValue), 'err');
                }
            }

            if($checkRes)
            {
                $this->output($this->lang->apicheck->checkSuccess);
            }
            elseif(is_array($result))
            {
                $logFile = '/tmp/apicheckResult.txt';
                $content = '';
                foreach($result as $key => $line) $content .= sprintf($this->lang->apicheck->saveContent, ++$key, $line['filePath'], $line['line'], $line['moduleName'], $line['methodName'], $line['controlFile'], $line['apiCode'], $line['controlCode']) . PHP_EOL;
                @file_put_contents($logFile, $content);

                $this->output(PHP_EOL . sprintf($this->lang->apicheck->errorSaved, $logFile), 'err');
            }
    }
}
