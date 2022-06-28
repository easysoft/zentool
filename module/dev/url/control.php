<?php
/**
 * The control file of url module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class url extends control
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
    public function printHelp($type = 'url')
    {
        return $this->output($this->lang->url->help->$type);
    }

    /**
     * The encode page.
     *
     * @param  array $params
     * @access public
     * @return void
     */
    public function encode($params)
    {
        if(empty($params) or empty($params['str']) or isset($params['help'])) return $this->printHelp('encode');
        return $this->output(urlencode($params['str']));
    }

    /**
     * The decode page.
     *
     * @param  array $params
     * @access public
     * @return void
     */
    public function decode($params)
    {
        if(empty($params) or empty($params['str']) or isset($params['help'])) return $this->printHelp('decode');
        return $this->output(urldecode($params['str']));
    }
}
