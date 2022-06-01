<?php
// +---------------------------------------------------------------+
// | CLI Table Class                                               |
// +---------------------------------------------------------------+
// | Nice output for PHP scripts on the command line               |
// +---------------------------------------------------------------+
// | Licence: MIT                                                  |
// +---------------------------------------------------------------+
// | Copyright: Jamie Curnow  <jc@jc21.com>                        |
// +---------------------------------------------------------------+
//

class cliTable {

    /**
     * Table Data
     *
     * @var    object
     * @access protected
     *
     **/
    protected $injectedData = null;

    /**
     * Table Item name
     *
     * @var    string
     * @access protected
     *
     **/
    protected $itemName = 'Row';

    /**
     * Table fields
     *
     * @var    array
     * @access protected
     *
     **/
    protected $fields = array();

    /**
     * Show column headers?
     *
     * @var    bool
     * @access protected
     *
     **/
    protected $showHeaders = true;

    /**
     * Use colors?
     *
     * @var    bool
     * @access protected
     *
     **/
    protected $useColors = true;

    /**
     * Table Border Color
     *
     * @var    string
     * @access protected
     *
     **/
    protected $tableColor = 'reset';

    /**
     * Header Color
     *
     * @var    string
     * @access protected
     *
     **/
    protected $headerColor = 'reset';

    /**
     * Colors, will be populated after instantiation
     *
     * @var    array
     * @access protected
     *
     **/
    protected $colors = array();

    /**
     * Show border, hide or show table border.
     *
     * @var    bool
     * @access protected
     *
     **/
    protected $showBorder = true;

    /**
     * OS.
     *
     * @var    string
     * @access protected
     *
     **/
    protected $os = '';

    /**
     * Border Characters
     *
     * @var    array
     * @access protected
     *
     **/
    protected $chars = array(
        'top'          => '═',
        'top-mid'      => '╤',
        'top-left'     => '╔',
        'top-right'    => '╗',
        'bottom'       => '═',
        'bottom-mid'   => '╧',
        'bottom-left'  => '╚',
        'bottom-right' => '╝',
        'left'         => '║',
        'left-mid'     => '╟',
        'mid'          => '─',
        'mid-mid'      => '┼',
        'right'        => '║',
        'right-mid'    => '╢',
        'middle'       => '│ ',
    );


    /**
     * Constructor
     *
     * @access public
     * @param  string $itemName
     * @param  bool   $useColors
     */
    public function __construct($itemName = 'Row', $useColors = true) {
        $this->setOS();
        $this->setItemName($itemName);
        $this->setUseColors($useColors);
        $this->defineColors();
    }


    /**
     * 声明魔术方法需要两个参数，真接为私有属性赋值时自动调用，并可以屏蔽一些非法赋值
     * @param $property
     * @param $value
     */
    public function __set($property, $value) {
        $this->$property = $value;
    }


    /**
     * setUseColors
     *
     * @access public
     * @param  bool  $bool
     * @return void
     */
    public function setUseColors($bool) {
        $this->useColors = (bool) $bool;
    }

    /**
     * 设置操作系统。
     * Set OS.
     *
     * @access public
     * @return void
     */
    public function setOS()
    {
        $os = strtolower(PHP_OS);
        if(strpos($os, 'win') !== false) $os = 'windows';

        $this->os = $os;
    }

    /**
     * getUseColors
     *
     * @access public
     * @return bool
     */
    public function getUseColors() {
        return $this->useColors;
    }


    /**
     * setTableColor
     *
     * @access public
     * @param  string  $color
     * @return void
     */
    public function setTableColor($color) {
        $this->tableColor = $color;
    }


    /**
     * getTableColor
     *
     * @access public
     * @return string
     */
    public function getTableColor() {
        return $this->tableColor;
    }


    /**
     * setChars
     *
     * @access public
     * @param  array  $chars
     * @return void
     */
    public function setChars($chars) {
        $this->chars = $chars;
    }


    /**
     * setHeaderColor
     *
     * @access public
     * @param  string  $color
     * @return void
     */
    public function setHeaderColor($color) {
        $this->headerColor = $color;
    }


    /**
     * getHeaderColor
     *
     * @access public
     * @return string
     */
    public function getHeaderColor() {
        return $this->headerColor;
    }


    /**
     * setItemName
     *
     * @access public
     * @param  string  $name
     * @return void
     */
    public function setItemName($name) {
        $this->itemName = $name;
    }


    /**
     * getItemName
     *
     * @access public
     * @return string
     */
    public function getItemName() {
        return $this->itemName;
    }


    /**
     * injectData
     *
     * @access public
     * @param  array  $data
     * @return void
     */
    public function injectData($data) {
        $this->injectedData = $data;
    }


    /**
     * setShowHeaders
     *
     * @access public
     * @param  bool  $bool
     * @return void
     */
    public function setShowHeaders($bool) {
        $this->showHeaders = $bool;
    }


    /**
     * getShowHeaders
     *
     * @access public
     * @return bool
     */
    public function getShowHeaders() {
        return $this->showHeaders;
    }


    /**
     * getPluralItemName
     *
     * @access protected
     * @return string
     */
    protected function getPluralItemName() {
        if (count($this->injectedData) == 1) {
            return $this->getItemName();
        } else {
            $lastChar = strtolower(substr($this->getItemName(), strlen($this->getItemName()) -1, 1));
            if ($lastChar == 's') {
                return $this->getItemName() . 'es';
            } else if ($lastChar == 'y') {
                return substr($this->getItemName(), 0, strlen($this->getItemName()) - 1) . 'ies';
            } else {
                return $this->getItemName().'s';
            }
        }
    }


    /**
     * addField
     *
     * @access public
     * @param  string      $fieldName
     * @param  string      $fieldKey
     * @param  bool|object $manipulator
     * @param  string      $color
     * @return void
     */
    public function addField($fieldName, $fieldKey, $manipulator = false, $color = 'reset') {
        $this->fields[$fieldKey] = array(
            'name'        => $fieldName,
            'key'         => $fieldKey,
            'manipulator' => $manipulator,
            'color'       => $color,
        );
    }


    /**
     * get
     *
     * @access public
     * @return string
     */
    public function get() {
        $rowCount      = 0;
        $columnLengths = array();
        $headerData    = array();
        $cellData      = array();

        // Headers
        if ($this->getShowHeaders()) {
            foreach ($this->fields as $field) {
                $headerData[$field['key']] = trim($field['name']);

                // Column Lengths
                if (!isset($columnLengths[$field['key']])) {
                    $columnLengths[$field['key']] = 0;
                }
                $columnLengths[$field['key']] = max($columnLengths[$field['key']], strlen(trim($field['name'])));
            }
        }

        // Data
        if ($this->injectedData !== null) {
            if (count($this->injectedData)) {
                foreach ($this->injectedData as $row) {
                    // Row
                    $cellData[$rowCount] = array();
                    foreach ($this->fields as $field) {
                        $key   = $field['key'];
                        $value = $row[$key];
                        if ($field['manipulator'] instanceof cliTableManipulator) {
                            $value = trim($field['manipulator']->manipulate($value, $row, $field['name']));
                        }

                        $cellData[$rowCount][$key] = $value;

                        // Column Lengths
                        if (!isset($columnLengths[$key])) {
                            $columnLengths[$key] = 0;
                        }
                        $columnLengths[$key] = max($columnLengths[$key], strlen($value));
                    }
                    $rowCount++;
                }
            } else {
                return 'There are no '.$this->getPluralItemName() . PHP_EOL;
            }
        } else {
            return 'There is no injected data for the table!' . PHP_EOL;
        }

        $response = '';

        // Now draw the table!
        if($this->showBorder) $response .= $this->getTableTop($columnLengths);
        if ($this->getShowHeaders()) {
            $response .= $this->getFormattedRow($headerData, $columnLengths, true);
            if($this->showBorder) $response .= $this->getTableSeperator($columnLengths);
        }

        foreach ($cellData as $row) {
            $response .= $this->getFormattedRow($row, $columnLengths);
        }

        $response .= $this->getTableBottom($columnLengths);

        return $response;
    }


    /**
     * getFormattedRow
     *
     * @access protected
     * @param  array   $rowData
     * @param  array   $columnLengths
     * @param  bool    $header
     * @return string
     */
    protected function getFormattedRow($rowData, $columnLengths, $header = false) {
        $response = $this->getChar('left');

        foreach ($rowData as $key => $field) {

            if ($header) {
                $color = $this->getHeaderColor();
            } else {
                $color = $this->fields[$key]['color'];
            }

            $fieldLength  = mb_strwidth($field) + 1;
            $field        = ' '.($this->getUseColors() ? $this->getColorFromName($color) : '').$field;
            if($this->os == 'windows') $field = iconv("UTF-8", "GB2312", $field);
            $response    .= $field;

            for ($x = $fieldLength; $x < ($columnLengths[$key] + ($this->showBorder ? 2 : 4)); $x++) {
                $response .= ' ';
            }
            $response .= $this->getChar('middle');
        }

        $response = substr($response, 0, strlen($response) - 3) . $this->getChar('right') . PHP_EOL;
        return $response;
    }


    /**
     * getTableTop
     *
     * @access protected
     * @param  array   $columnLengths
     * @return string
     */
    protected function getTableTop($columnLengths) {
        $response = $this->getChar('top-left');
        foreach ($columnLengths as $length) {
            $response .= $this->getChar('top', $length + 2);
            $response .= $this->getChar('top-mid');
        }
        $response = substr($response, 0, strlen($response) - 3) . $this->getChar('top-right') . PHP_EOL;
        return $response;
    }


    /**
     * getTableBottom
     *
     * @access protected
     * @param  array   $columnLengths
     * @return string
     */
    protected function getTableBottom($columnLengths) {
        $response = $this->getChar('bottom-left');
        foreach ($columnLengths as $length) {
            $response .= $this->getChar('bottom', $length + 2);
            $response .= $this->getChar('bottom-mid');
        }
        $response = substr($response, 0, strlen($response) - 3) . $this->getChar('bottom-right') . PHP_EOL;
        return $response;
    }


    /**
     * getTableSeperator
     *
     * @access protected
     * @param  array   $columnLengths
     * @return string
     */
    protected function getTableSeperator($columnLengths) {
        $response = $this->getChar('left-mid');
        foreach ($columnLengths as $length) {
            $response .= $this->getChar('mid', $length + 2);
            $response .= $this->getChar('mid-mid');
        }
        $response = substr($response, 0, strlen($response) - 3) . $this->getChar('right-mid') . PHP_EOL;
        return $response;
    }


    /**
     * getChar
     *
     * @access protected
     * @param  string  $type
     * @param  int     $length
     * @return string
     */
    protected function getChar($type, $length = 1) {
        $response = '';
        if ($this->showBorder && isset($this->chars[$type])) {
            if ($this->getUseColors()) {
                $response .= $this->getColorFromName($this->getTableColor());
            }
            $char = trim($this->chars[$type]);
            for ($x = 0; $x < $length; $x++) {
                $response .= $char;
            }
        }
        return $response;
    }


    /**
     * defineColors
     *
     * @access protected
     * @return void
     */
    protected function defineColors()
    {
        $this->colors = array(
            'blue'    => chr(27).'[1;34m',
            'red'     => chr(27).'[1;31m',
            'green'   => chr(27).'[1;32m',
            'yellow'  => chr(27).'[1;33m',
            'black'   => chr(27).'[1;30m',
            'magenta' => chr(27).'[1;35m',
            'cyan'    => chr(27).'[1;36m',
            'white'   => chr(27).'[1;37m',
            'grey'    => chr(27).'[0;37m',
            'reset'   => chr(27).'[0m',
        );
    }


    /**
     * getColorFromName
     *
     * @access protected
     * @param  string  $colorName
     * @return string
     */
    protected function getColorFromName($colorName)
    {
        if (isset($this->colors[$colorName])) {
            return $this->colors[$colorName];
        }
        return $this->colors['reset'];
    }


    /**
     * display
     *
     * @access public
     * @return void
     */
    public function display() {
        print $this->get();
    }

}

class cliTableManipulator {

    /**
     * Stores the type of manipulation to perform
     *
     * @var    string
     * @access protected
     *
     **/
    protected $type = '';


    /**
     * Constructor
     *
     * @access public
     * @param  string $type
     */
    public function __construct($type) {
        $this->type = $type;
    }


    /**
     * manipulate
     * This is used by the Table class to manipulate the data passed in and returns the formatted data.
     *
     * @access public
     * @param  mixed   $value
     * @param  array   $row
     * @param  string  $fieldName
     * @return string
     */
    public function manipulate($value, $row = array(), $fieldName = '') {
        $type = $this->type;
        if ($type && is_callable(array($this, $type))) {
            return $this->$type($value, $row, $fieldName);
        } else {
            error_log('Invalid Data Manipulator type: "' . $type . '"');
            return $value . ' (Invalid Type: "' . $type . '")';
        }
    }


    /**
     * dollar
     * Changes 12300.23 to $12,300.23
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function dollar($value) {
        return '$' . number_format($value, 2);
    }


    /**
     * date
     * Changes 1372132121 to 25-06-2013
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function date($value) {
        if (!$value) {
            return 'Not Recorded';
        }
        return date('d-m-Y', $value);
    }


    /**
     * datelong
     * Changes 1372132121 to 25th June 2013
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function datelong($value) {
        if (!$value) {
            return 'Not Recorded';
        }
        return date('jS F Y', $value);
    }


    /**
     * time
     * Changes 1372132121 to 1:48 pm
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function time($value) {
        if (!$value) {
            return 'Not Recorded';
        }
        return date('g:i a', $value);
    }


    /**
     * datetime
     * Changes 1372132121 to 25th June 2013, 1:48 pm
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function datetime($value) {
        if (!$value) {
            return 'Not Recorded';
        }
        return date('jS F Y, g:i a', $value);
    }


    /**
     * nicetime
     * Changes 1372132121 to 25th June 2013, 1:48 pm
     * Changes 1372132121 to Today, 1:48 pm
     * Changes 1372132121 to Yesterday, 1:48 pm
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function nicetime($value) {
        if (!$value) {
            return '';
        } else if ($value > mktime(0, 0, 0, date('m'), date('d'), date('Y'))) {
            return 'Today ' . date('g:i a', $value);
        } else if ($value > mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))) {
            return 'Yesterday ' . date('g:i a', $value);
        } else {
            return date('jS F Y, g:i a', $value);
        }
    }


    /**
     * duetime
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function duetime($value) {
        if (!$value) {
            return '';
        } else {
            $isPast = false;
            if ($value > time()) {
                $seconds = $value - time();
            } else {
                $isPast = true;
                $seconds = time() - $value;
            }

            $text = $seconds . ' second' . ($seconds == 1 ? '' : 's');
            if ($seconds >= 60) {
                $minutes  = floor($seconds / 60);
                $seconds -= ($minutes * 60);
                $text     = $minutes . ' minute' . ($minutes == 1 ? '' : 's');
                if ($minutes >= 60) {
                    $hours    = floor($minutes / 60);
                    $minutes -= ($hours * 60);
                    $text     = $hours . ' hours, ' . $minutes . ' minute' . ($hours == 1 ? '' : 's');
                    if ($hours >= 24) {
                        $days   = floor($hours / 24);
                        $hours -= ($days * 24);
                        $text   = $days . ' day' . ($days == 1 ? '' : 's');
                        if ($days >= 365) {
                            $years = floor($days / 365);
                            $days -= ($years * 365);
                            $text  = $years . ' year' . ($years == 1 ? '' : 's');
                        }
                    }
                }
            }

            return $text . ($isPast ? ' ago' : '');
        }
    }


    /**
     * nicenumber
     *
     * @access protected
     * @param  int    $value
     * @return string
     */
    protected function nicenumber($value) {
        return number_format($value, 0);
    }


    /**
     * month
     * Changes 1372132121 to June
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function month($value) {
        if (!$value) {
            return 'Not Recorded';
        }
        return date('F', $value);
    }


    /**
     * year
     * Changes 1372132121 to 2013
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function year($value) {
        if (!$value) {
            return 'Not Recorded';
        }
        return date('Y', $value);
    }


    /**
     * monthyear
     * Changes 1372132121 to June 2013
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function monthyear($value) {
        if (!$value) {
            return 'Not Recorded';
        }
        return date('F Y', $value);
    }


    /**
     * percent
     * Changes 50.2 to 50%
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function percent($value) {
        return intval($value) . '%';
    }


    /**
     * yesno
     * Changes 0/false and 1/true to No and Yes respectively
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function yesno($value) {
        return ($value ? 'Yes' : 'No');
    }


    /**
     * text
     * Strips input of any html
     *
     * @access protected
     * @param  mixed   $value
     * @return string
     */
    protected function text($value) {
        return strip_tags($value);
    }
}
