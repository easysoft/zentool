<?php
/**
 * The control file of md5 module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class md5 extends control
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
        if(empty($params)) return $this->printHelp();

        $method = key($params);
        if(method_exists($this, $method))
        {
            if(isset($this->config->url->paramKey[$method])) $params = array($this->config->url->paramKey[$method] => $params[$method]);
            return $this->$method($params);
        }
        return $this->printHelp();
    }

    /**
     * Print help.
     *
     * @access public
     * @return void
     */
    public function printHelp($type = 'md5')
    {
        return $this->output($this->lang->md5->help->$type);
    }

    /**
     * Calculate.
     *
     * @param  array $params
     * @access public
     * @return void
     */
    public function calculate($params)
    {
        if(empty($params) or isset($params['help']) or empty($params['calculate'])) return $this->printHelp('calculate');

        $filePath = helper::getRealPath($params['calculate']);
        if($filePath)
        {
            return $this->output(md5_file($filePath));
        }
        else
        {
            return $this->output(md5($params['str']));
        }
    }
}
