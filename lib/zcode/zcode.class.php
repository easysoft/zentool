<?php
class zcode
{
    /**
     * Create one file with content.
     *
     * @param  string    $file
     * @param  string    $content
     * @access public
     * @return int|false
     */
    public function create($file, $content)
    {
        if(!is_dir(dirname($file))) var_dump(mkdir(dirname($file), 0777, true));
        return !file_put_contents($file, $content) ? false : $file;
    }

    /**
     * Read file content to a string or an array.
     *
     * @param  string $file
     * @param  string $mode
     * @access public
     * @return string|array
     */
    public function read($file, $mode = 'string')
    {
        return $mode == 'string' ? file_get_contents($file) : file($file);
    }

    /**
     * Append content to a file.
     *
     * @param  string $file
     * @param  string $content
     * @param  int    $line
     * @access public
     * @return void
     */
    public function appendTo($file, $content, $line)
    {
        $codes = $this->read($file);
        $codes[$line] .=  "\n" . $content;
        file_put_contents($file, join("\n", $codes));
    }

    /**
     * Update function's body.
     *
     * @param  string  $file
     * @param  string  $class
     * @param  string  $function
     * @param  string  $functionBody
     * @access public
     * @return void
     */
    public function updateFunction($file, $class, $function, $functionBody)
    {
        $position = $this->getFuncPosition($file, $class, $function);
        $this->appendTo($file, $functionBody, $position->endLine);
        $this->eraseLines($file, $position->startLine, $position->endLine);
    }

    /**
     * Erase specified function.
     *
     * @param  string  $file
     * @param  string  $class
     * @param  string  $function
     * @access public
     * @return void
     */
    public function eraseFunction($file, $class, $function)
    {
        $position = $this->getFuncPosition($file, $class, $function);
        $this->eraseLines($file, $position->startLine, $position->endLine);
    }

    /**
     * Erase the specified lines of a file.
     *
     * @param  string $file
     * @param  int    $startLine
     * @param  int    $endLine
     * @access public
     * @return void
     */
    public function eraseLines($file, $startLine, $endLine)
    {
        $codes = file($file);
        for($i = $startLine - 1; $i < $endLine; $i ++) unset($codes[$i]);
        file_put_contents($file, join("\n", $codes));
    }

    /**
     * Get function's first line and last line.
     *
     * @param  string  $file
     * @param  string  $class
     * @param  string  $function
     * @access public
     * @return object
     */
    public function getFuncPosition($file, $class, $function)
    {
        if(!class_exists($class)) include $file;

        $reflection = new ReflectionMethod("$class::$function");

        $position = new stdclass;
        $position->startLine = $reflection->getStartLine();
        $position->endLine   = $reflection->getEndLine();
        return $position;
    }

    public function test($file)
    {
        /* Check synatex by 'php -l' or sonarqube */
    }

    public function commit($file) {}

    public function log($file) {}

    public function checkout($file, $version) {}

}
