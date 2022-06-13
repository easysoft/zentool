<?php
/**
 * The control file of index module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class devops extends control
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
        if(empty($params)) return $this->printHelp();

        $method = key($params);
        if(method_exists($this, $method))
        {
            if(isset($this->config->devops->paramKey[$method])) $params = array($this->config->devops->paramKey[$method] => $param);
            return $this->$method($params);
        }
        return $this->printHelp();
    }

    /**
     * Print help.
     *
     * @param  string $type
     * @access public
     * @return void
     */
    public function printHelp($type = 'devops')
    {
        return $this->output($this->lang->devops->help->$type);
    }

    public function mr($params)
    {
        if(empty($params) or empty($params['branch']) or isset($params['help'])) return $this->printHelp('mr');
    }
}
