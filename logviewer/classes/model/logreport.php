<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Create report from a generic Kohana Log file
 *
 * Author: Anis uddin Ahmad <anisniit@gmail.com>
 * Created On: 11/10/11 8:44 PM
 */
class Model_Logreport{

    protected $_rawContent;
    protected $_logEntries = array();

    // Copy of Kohana_log_file log levels
    public static $levels = array(
		LOG_EMERG   => 'EMERGENCY',
		LOG_CRIT    => 'CRITICAL',
		LOG_ERR     => 'ERROR',
		LOG_WARNING => 'WARNING',
		LOG_NOTICE  => 'NOTICE',
		LOG_INFO    => 'INFO',
		LOG_DEBUG   => 'DEBUG',
	);

    function __construct($filepath)
    {
        // Read lines as array. Skip first 2 lines - SYSPATH checking and blank line
        $this->_rawContent = array_slice(file($filepath), 2);
        $this->_createLogEntries();
    }

    public function getLogsEntries($level = null){
        return $this->_logEntries;
    }

    protected function _createLogEntries()
    {
        $pattern = "/(.*) --- ([A-Z]*): ([^:]*): ([^~]*)~ (.*)/";
        $custom_log = "/(\d{4}-\d{2}-\d{2}.*) --- ([A-Z]*): (.*)/";
        
        foreach($this->_rawContent as $logRaw) {
            if (preg_match($pattern, $logRaw, $matches)) {

                $log = array();
                $log['raw'] = $logRaw;
                $log['time'] = strtotime($matches[1]);
                $log['level'] = $matches[2];    // Notice, Error etc.
                $log['style'] = $this->_getStyle($matches[2]);    // CSS class for styling
                $log['type'] = $matches[3];     // Exception name
                $log['message'] = $matches[4];
                $log['file'] = $matches[5];

                $this->_logEntries[] = $log;
            }
            else if (preg_match($custom_log, $logRaw, $matches)) {

                $log = array();
                $log['raw'] = $logRaw;
                $log['time'] = strtotime($matches[1]);
                $log['level'] = $matches[2];    // Notice, Error etc.
                $log['style'] = $this->_getStyle($matches[2]);    // CSS class for styling
                $log['message'] = $matches[3];
                $log['type'] = '';
                $log['file'] = '';

                $this->_logEntries[] = $log;
            }
            else {
                $last = count($this->_logEntries) - 1;
                $this->_logEntries[$last]['message'] .= $logRaw;
            }
        }
    }

    private function _getStyle($level)
    {
        switch($level){
            case self::$levels[LOG_WARNING]:
            case self::$levels[LOG_DEBUG]:
                return 'warning';
                break;
            case self::$levels[LOG_ERR]:
            case self::$levels[LOG_CRIT]:
            case self::$levels[LOG_EMERG]:
                return 'important';
            break;
            case self::$levels[LOG_NOTICE]:
                return 'notice';
            break;
            case self::$levels[LOG_INFO]:
                return 'success';
            break;
            default: '';
        }
    }

}


 
