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
    public function index($params)
    {
        fwrite(STDOUT, $this->lang->set->inputDir);

        $realPath = false;
        while(!$realPath)
        {
            $path = trim(fgets(STDIN));
            if($path)
            {
                $realPath = true;
            }
            else
            {
                fwrite(STDOUT, sprintf($this->lang->set->dirNotExists, $path));
            }
        }
    }
}
