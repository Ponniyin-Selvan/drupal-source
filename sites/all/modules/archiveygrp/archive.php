<?php
require('plugin_manager.php');
require('plugin.php');
require('group_details.php');
require('message_handler.php');
require('grab_messages.php');

class Archive implements iMessageHandler {

    var $logger;
    var $groupDetails = null;
    var $settings = null;
    var $debugLogFile = null;
    var $pluginManager = null;
    var $LOGIN_STRING = 'login=$userId&passwd=$password&.src=ygrp&.done=http://groups.yahoo.com';
    var $LOGIN_URL = "https://login.yahoo.com/config/login";
    var $LOGOUT_URL = "http://login.yahoo.com/config/login?logout=1&.partner=&.intl=us&.done=http%3a%2f%2fmy.yahoo.com%2findex.html&.src=my";
    
    function Archive(){
        $this->logger =& LoggerManager::getLogger('Archive');
        //error_reporting(0);
        //set_error_handler (array (&$this, 'handleError'));
        //set_exception_handler(array (&$this, 'exceptionHandler'));
    }
    
    function initialize($groupName) {
        $this->settings = parse_ini_file(dirname(__FILE__).'/settings.ini', true);
        
        $groupDetails = parse_ini_file(dirname(__FILE__)."/".$groupName.".ini", true);
        $this->groupDetails = new GroupDetails();
        foreach($groupDetails as $key => $value) {
            $this->groupDetails->set($key, $value);
        }
        
        $this->settings = array_merge($this->settings, $groupDetails);
        
        $this->logger->debug("Settings ".print_r($this->settings, true));
        $this->logger->debug("Group Details ".print_r($this->groupDetails, true));
        
        $this->initializePlugins();
    }

    function initializePlugins() {
        $this->pluginManager = new PluginManager();
        $this->logger->debug("Initializing Plugin Manager & Loading Plugins from plugins folder");
        $this->pluginManager->loadPlugins(dirname(__FILE__).'/plugins/', $this->settings);
    }

    private function getLogFile($fileName) {
        // @TODO - path separtor
        return "logs/".$this->groupDetails->groupName."/".$fileName;
    }
    
    function isTrue($setting) {
        return (isset($this->settings[$setting]) && $this->settings[$setting]);
    }
    
    function getSetting($setting, $defaultValue) {
        return (isset($this->settings[$setting]) ? $this->settings[$setting] : $defaultValue);
    }
    
    function initializeClient() {

        $client = curl_init();

        if ($this->isTrue('grab.debug')) {
            $debugLogFileName = $this->getLogFile("grab.debug.log");
            $this->debuglogFile = fopen($debugLogFileName, "w");

            if (!$this->debuglogFile) {
                $this->logger->error("Unable to open ".$debugLogFileName. " for writing.\n");
            }
            curl_setopt($client, CURLOPT_STDERR, $this->debuglogFile);
            curl_setopt($client, CURLOPT_FILE, $this->debuglogFile);
        }

        $cookieFile = $this->getLogFile("cookie.txt");
        curl_setopt ($client, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt ($client, CURLOPT_COOKIEFILE, $cookieFile);

        curl_setopt($client, CURLOPT_FAILONERROR, TRUE);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($client, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($client, CURLOPT_HEADER, FALSE);
        curl_setopt($client, CURLOPT_CONNECTTIMEOUT, $this->getSetting("grab.timeout", 30)); //30 seconds

        if ($this->isTrue("grab.verbose")) {
            curl_setopt($client, CURLOPT_VERBOSE, TRUE);
        }

        if ($this->isTrue("grab.proxy")) {
            curl_setopt($client, CURLOPT_PROXY, $this->getSetting("grab.proxy.ip.port",""));
            curl_setopt($client, CURLOPT_PROXYUSERPWD, $this->getSetting("grab.proxy.id.pwd",""));
        }
        
        if ($this->getSetting("grab.user.agent", "") != "") {
            curl_setopt($client, CURLOPT_USERAGENT, $this->getSetting("grab.user.agent", "Wget"));
        }
        return $client;
    }

    function closeClient($client) {
        // close CURL resource, and free up system resources
        curl_close($client);
        if ($this->debugLogFile != null) {
            fclose($this->debugLogFile);
        }
    }

    function logon($client, $groupDetails) {
        $this->logger->info("Logging into Yahoo Groups for ".$groupDetails->groupName);
        
        $userId = $groupDetails->userId;
        $password = $groupDetails->password;
        
        $login_string = eval("return \"$this->LOGIN_STRING\";");

        curl_setopt($client, CURLOPT_URL, $this->LOGIN_URL);
        curl_setopt($client, CURLOPT_POST, TRUE);
        curl_setopt($client, CURLOPT_POSTFIELDS, $login_string);
        // grab URL and pass it to the browser
        $result = curl_exec($client);
        $status = curl_errno($client);
        if ($status != CURLE_OK) {
            $this->logger->fatal("Couldn't login into Yahoo Groups ");
            $this->logger->fatal("Error ".curl_errno($client)." ".curl_error ($client));
        } else {
            $this->logger->info("Successfully logged into Yahoo Groups for ".$groupDetails->groupName);
        }
        return ($status == CURLE_OK);
    }

    function logout($client, $groupDetails) {
        $this->logger->info("Logging out from Yahoo Groups");
        curl_setopt ($client, CURLOPT_URL, $this->LOGOUT_URL);
        $result = curl_exec($client);
        $status = curl_errno($client);
        if ($status != CURLE_OK) {
            $this->logger->fatal("Couldn't Logout ".curl_errno($client)." ".curl_error ($client));
        } else {
            $this->logger->info("Successfully logged out of Yahoo Groups for ".$groupDetails->groupName);
        }
        return ($status == CURLE_OK);
    }
    
    function shutdown() {
        $this->logger->debug("Shutting down Plugin Manager & Plugins");
        $this->pluginManager->shutdown();
        unset($this->pluginManager);
        unset($this->settings);
        restore_error_handler();
        restore_exception_handler();
    }

    function exceptionHandler($exception) {
        $exceptionString = $exception->getMessage()."\n";
        $exceptionString = $exceptionString.$exception->getTraceAsString();
        echo $exceptionString;
        $this->logger->error($exceptionString);
        $this->shutdown();
        die("Exception occurred, stopping archive process");
    }	

    function handleError($errno, $errmsg, $filename, $linenum, $vars) {
        // timestamp for the error entry
        $dt = date("Y-m-d H:i:s (T)");

        // define an assoc array of error string
        // in reality the only entries we should
        // consider are E_WARNING, E_NOTICE, E_USER_ERROR,
        // E_USER_WARNING and E_USER_NOTICE
        $errortype = array (
                    E_ERROR              => 'Error',
                    E_WARNING            => 'Warning',
                    E_PARSE              => 'Parsing Error',
                    E_NOTICE             => 'Notice',
                    E_CORE_ERROR         => 'Core Error',
                    E_CORE_WARNING       => 'Core Warning',
                    E_COMPILE_ERROR      => 'Compile Error',
                    E_COMPILE_WARNING    => 'Compile Warning',
                    E_USER_ERROR         => 'User Error',
                    E_USER_WARNING       => 'User Warning',
                    E_USER_NOTICE        => 'User Notice',
                    E_STRICT             => 'Runtime Notice',
                    E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
                    );
        // set of errors for which a var trace will be saved
        $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
        
        $err = "<errorentry>\n";
        $err .= "\t<datetime>" . $dt . "</datetime>\n";
        $err .= "\t<errornum>" . $errno . "</errornum>\n";
        $err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";
        $err .= "\t<errormsg>" . $errmsg . "</errormsg>\n";
        $err .= "\t<scriptname>" . $filename . "</scriptname>\n";
        $err .= "\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";

        if (in_array($errno, $user_errors)) {
            $err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
        }
        $err .= "</errorentry>\n\n";
        echo $err;
        //$this->logger->error($err);

        // save to the error log, and e-mail me if there is a critical user error
        //error_log($err, 3, "/usr/local/php4/error.log");
        if ($errno == E_USER_ERROR) {
            $this->shutdown();
            die("Fatal error occurred, stopping the script");
        }
    }
    
    function testArchive($messageNo) {
        $messageDetails = array();
        $grabMessage = new GrabMessages();
        $grabMessage->testGrab($messageNo, $this);
    }

    function handleMessage(&$messageDetails) {
        $this->logger->debug(print_r($messageDetails, true));
        $this->pluginManager->executePlugins($messageDetails);
        // Sleep to avoid eating yahoo bandwidth too quickly
        sleep(12);
    }
    
    function handleInvalidMessage($sourceHtml) {
        $this->logger->debug("Invalid Message");
    }

    function getLastArchivedMessage() {
        $messageNo = 0;
        $this->db = new PDO($this->settings['db.datasource'], 
                $this->settings['db.userid'], $this->settings['db.password']);
        $this->db->exec('SET CHARACTER SET utf8');
        $sql = "SELECT IFNULL(MAX(message_no), 1) FROM ".$this->settings['table.prefix']."ponniyinselvan_log";
        $stmt = $this->db->prepare($sql);
        $stmt->bindColumn(1, $messageNo);
        $stmt->execute();
        $stmt->fetch(PDO::FETCH_BOUND);
		$stmt->closeCursor();
		$stmt = null;
		$this->db = null;
        $this->logger->debug("Last Archived Message $messageNo");
        return $messageNo;
    }
    
    function archiveMessages($startMessageNo) {
        $client = $this->initializeClient();
        if ($this->logon($client, $this->groupDetails)) {
            $grabMessage = new GrabMessages();
			if ($startMessageNo === 0) {
				$grabMessage->grabFrom($this->groupDetails, $this, $client, $startMessageNo);
			} else {
				$grabMessage->grabFromNext($this->groupDetails, $this, $client, $startMessageNo);
			}
            $this->logout($client, $this>groupDetails);
        }
        $this->closeClient($client);
    }

    function archiveNewMessages() {
        $this->archiveMessages($this->getLastArchivedMessage());
    }
}
?>
