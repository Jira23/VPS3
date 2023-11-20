<?php

    class Logging {

        public $start_time;
        public $logEcho;
        public $logFile;
        public $logDB;
        
        public function setLogging($logEcho = true, $logFile = true, $logDB = false, $time = true, $logFileName = 'log.txt') {
            $this->logEcho = $logEcho;
            $this->logFile = $logFile;
            $this->logDB = $logDB;
            $this->time = $time;
            $this->start_time = microtime(true);
            if(!isset($this->logFileName)) $this->logFileName = LOG_PATH .date('Y-m-d_H:i:s') .'_' .$logFileName;            
        }


        public function doLog($message, $type = '') {
            $this->message = $message;
            $this->type = $type;
            
            if($this->logEcho) $this->writeEcho();
            if($this->logFile) $this->writeFile();
            if($this->logDB) $this->writeDB();
        }

        public function writeEcho() {
            if($this->time) echo 'time:' .round((microtime(true) - $this->start_time) * 1000) .' - ';
            if($this->type !== '') echo $this->type .': ';
            echo $this->message;
            echo '<br>';
        }        

        public function writeFile() {
            $time = '';
            if($this->time) $time = 'time:' .round((microtime(true) - $this->start_time) * 1000) .' - ';
            file_put_contents($this->logFileName, $time .$this->message .PHP_EOL, FILE_APPEND);
        }        

        public function writeDB() {

        }                

    }

?>

