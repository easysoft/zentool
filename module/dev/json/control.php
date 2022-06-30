<?php
/**
 * The control file of json module of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class json extends control
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
        if(empty($params) or isset($params['help'])) return $this->printHelp();

        foreach($params as $key => $param)
        {
            if(isset($this->config->json->paramKey[$key])) $params = array($this->config->json->paramKey[$key] => $param);
        }

        return $this->decode($params);
    }

    /**
     * Print help.
     *
     * @access public
     * @return void
     */
    public function printHelp($type = 'json')
    {
        return $this->output($this->lang->json->help->$type);
    }

    /**
     * Decode.
     *
     * @param  array $params
     * @access public
     * @return void
     */
    public function decode($params)
    {
        if(empty($params) or isset($params['help']) or empty($params['param'])) return $this->printHelp('decode');

        $json = trim($params['param'], "'\"");
        $file = helper::getRealPath($json);
        if($file) $json = file_get_contents($file);

        var_dump($json);
        $json = json_decode($json, isset($params['associative']) ? true : false);
        return print_r($json);
    }
}
