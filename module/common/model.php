<?php
/**
 * The model file of common module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class commonModel extends model
{
    /**
     * The construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if(!defined('FIRST_RUN'))
        {
            $this->sendHeader();
            define('FIRST_RUN', true);
        }
    }

    /**
     * Set the header info.
     *
     * @access public
     * @return void
     */
    public function sendHeader()
    {
        header("Content-Type: text/html; Language={$this->config->charset}");
        header("Cache-control: private");
    }
}
