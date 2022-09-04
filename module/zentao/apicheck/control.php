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
            while(true)
            {
                $inputValue = $this->readInput();
                $result     = $this->apicheck->checkInput($inputValue);
                if(!is_bool($result))
                {
                    $this->output(sprintf($this->lang->apicheck->checkFail, $inputValue), 'err');
                    break;
                }
                elseif($result) {
                    $this->output($this->lang->apicheck->checkSuccess);
                    break;
                }

                $this->output(sprintf($this->lang->apicheck->webDirNotReal, $inputValue), 'err');
            }

    }
}
