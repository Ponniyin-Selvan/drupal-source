<?php
class GrabMessages {

    var $logger;

    var $MESSAGE_URL = 'http://groups.yahoo.com/group/$groupName/message/$messageNo/?source=1&var=1&l=1';
    var $MESSAGE_NO_DETAILS_XPATH_QUERY = '//table[@class="footaction"]//td[@align="right"]';
    var $REG_EXP_SPECIAL_CHARS = '/[^(\\x0A|\\x0D|\\x20-\\x7F)]*/';
    var $LAST_UNDELETED_MSG_URL = 'http://groups.yahoo.com/group/$groupName/messages/$messageNo?o=1&xm=1&l=1';
    var $MESSAGE_NOS_XPATH_QUERY = '//table[@class="datatable"]//td[@class="msgnum smalltype"]';
    
    function GrabMessages(){
        $this->logger =& LoggerManager::getLogger('GrabMessages');
    }

    function downloadPage($client, $uri) {

        $this->logger->debug("Getting page ".$uri);
        curl_setopt ($client, CURLOPT_URL, $uri);
        $result = curl_exec($client);
        $status = curl_errno($client);
        if ($status != CURLE_OK) {
            $this->logger->error("Couldn't get Uri ".$uri." ==> ".curl_errno($client)." ".curl_error ($client));
            $result = null;
        }
        return $result;
    }

    function downloadMessage($client, $messageNo, $groupDetails) {
        $this->logger->info("Getting Message ".$messageNo);
        $groupName = $groupDetails->groupName;
        $messageUri = eval("return \"$this->MESSAGE_URL\";");
        return $this->downloadPage($client, $messageUri);
    }
    
    function getMessageNoDetails($sourceHtml) {

        $dom = new DOMDocument();
        @$dom->loadHTML($sourceHtml);
        $xpath = new DOMXPath($dom);
        $message_no = 0;
        $prev_message_no = 0;
        $next_message_no = 0;

        $msg_td = $xpath->query($this->MESSAGE_NO_DETAILS_XPATH_QUERY);
        if ($msg_td->length > 0) {
           $td_element = $msg_td->item(0);
    
           $doc = new DOMDocument();
           $doc->preserveWhiteSpace = true;
           $doc->appendChild($doc->importNode($td_element,true));
            
           preg_match("/[0-9]+/", $td_element->textContent, $matches, PREG_OFFSET_CAPTURE);
           if (count($matches) > 0) {
                  $message_no = intval($matches[0][0]);
           } else {
               $this->logger->error("Couldn't find Message No through regular expression");
           }

           // Next Prev
           $nav_hrefs = $td_element->getElementsByTagName("a");
           for ($j = 0 ; $j < $nav_hrefs->length ; $j++) {
               preg_match("/[0-9]+/", $nav_hrefs->item($j)->getAttribute("href"), $matches, PREG_OFFSET_CAPTURE);
               if (strstr($nav_hrefs->item($j)->textContent, "Prev")) {
                   $prev_message_no = intval($matches[0][0]);
               } else if (strstr($nav_hrefs->item($j)->textContent, "Next")) {
                   $next_message_no = intval($matches[0][0]);
               }
           }
         } else {
           $this->logger->error("Couldn't get Message No through xpath query");
           $this->logger->debug($sourceHtml);
         }
         $messageNoDetails = array('message_no' => $message_no, 
                                   'prev_message_no' => $prev_message_no, 
                                   'next_message_no' => $next_message_no);
         return $messageNoDetails;
    }
    
    function grabMessage($sourceHtml) {

        try {
            $messageNoDetails = $this->getMessageNoDetails($sourceHtml);

            if ($messageNoDetails['message_no'] == 0) {
                $this->logger->warn("Couldn't get message no details, special characters may be preventing xpath from working - clean them up and try again");
                // Could be special characters preventing xpath from working - clean them up and try again
                $sourceHtml = preg_replace($this->REG_EXP_SPECIAL_CHARS,'', $sourceHtml);
                $messageNoDetails = $this->getMessageNoDetails($sourceHtml);
                $this->logger->debug("Message details ".print_r($messageNoDetails, true));
            }
            if ($messageNoDetails['message_no'] > 0) {
                $messageDetails['original_source'] = $sourceHtml;
                $messageDetails = array_merge($messageDetails, $messageNoDetails);
            } else {
                $this->logger->error("Couldn't get message no, stopping");
            }
        } catch(Exception $exception) {
            $exceptionString = $exception->getMessage()."\n";
            $exceptionString = $exceptionString.$exception->getTraceAsString();
            $exceptionString = "message Details ".print_r($messageDetails, true);
            $this->logger->error("Exception Occrred ".$exceptionString);
        }

        unset($messageNoDetails);
        return $messageDetails;
    }
    
    function testGrab($messageNo, $messageHandler) {
        $messageDetails = array();

        $sourceHtml = file_get_contents("tests/$messageNo.html");
        if ($this->isInvalidMessage($sourceHtml)) {
            $this->logger->error("Not a valid Message");
        } else {
            $messageDetails = $this->grabMessage($sourceHtml);
            $messageHandler->handleMessage($messageDetails);
        }
    }
    
    function getLastValidMessageNo($client, $messageNo, $groupDetails) {
        $lastValidMessageNo = 0;
        $groupName = $groupDetails->groupName;
        $messageUri = eval("return \"$this->LAST_UNDELETED_MSG_URL\";");
        $sourceHtml = $this->downloadPage($client, $messageUri);
        if ($sourceHtml != null) {
            $dom = new DOMDocument();
            @$dom->loadHTML($sourceHtml);
            $xpath = new DOMXPath($dom);
            $entries = $xpath->query($this->MESSAGE_NOS_XPATH_QUERY);
            if ($entries->length > 0) {
                $lastValidMessageNo = intval($entries->item(0)->nodeValue);
            }
        }
        return $lastValidMessageNo;
    }
    
    function isUnavailable($sourceHtml) {
        return (!(strstr($sourceHtml, "temporarily unavailable") === FALSE));
    }
    
    function isInvalidMessage($sourceHtml) {
        return ($sourceHtml == null ||
                (strstr($sourceHtml, "temporarily unavailable") !== FALSE));
    }
	
    function handleMessage(&$messageHandler, &$messageDetails) {
		$messageHandler->handleMessage($messageDetails);
	}
	
    function grabFrom($groupDetails, $messageHandler, $client, $startMessageNo) {
    
        $this->logger->info("Grabbing Messages from ".$startMessageNo);
        $messageDetails = array();
        $messageNo = $startMessageNo;
        while ($messageNo > 0) {
            $sourceHtml = $this->downloadMessage($client, $messageNo, $groupDetails);
            if ($sourceHtml == null) {
                $messageHandler->handleInvalidMessage($sourceHtml);
                $this->logger->info("Couldn't get Message for $messageNo");
            } else if (!(strstr($sourceHtml, "temporarily unavailable") === FALSE) 
	               || (!(strstr($sourceHtml, "Message  does not exist") === FALSE))) {
                $this->logger->info("The message is unavailable, probably deleted");
                $lastValidMessageNo = $this->getLastValidMessageNo($client, $startMessageNo, $groupDetails);
                if ($lastValidMessageNo > 0 && $lastValidMessageNo < $startMessageNo) {
                    $this->logger->info("Last Valid Message No is $lastValidMessageNo");
					$messageNo = $lastValidMessageNo;
                } else {
                    $this->logger->info("The message $messageNo not deleted, but unavailable");
					sleep(360); // sleep for an hour
                }
            } else {
                $messageDetails = $this->grabMessage($sourceHtml);
                $messageHandler->handleMessage($messageDetails);
                $this->logger->info("Archived $messageNo");
                $messageNo = $messageDetails['next_message_no'];
            }
        }
    }
	
    function grabFromNext($groupDetails, $messageHandler, $client, $startMessageNo) {
    
        $this->logger->info("Grabbing Messages from(next) ".$startMessageNo);
        $messageDetails = array();
        $messageNo = $startMessageNo;
        while ($messageNo > 0) {
            $sourceHtml = $this->downloadMessage($client, $messageNo, $groupDetails);
            if ($sourceHtml == null) {
                $messageHandler->handleInvalidMessage($sourceHtml);
                $this->logger->info("Couldn't get Message for $messageNo");
            } else if (!(strstr($sourceHtml, "temporarily unavailable") === FALSE) 
	               || (!(strstr($sourceHtml, "Message  does not exist") === FALSE))) {
                $this->logger->info("The message is unavailable, probably deleted");
                $lastValidMessageNo = $this->getLastValidMessageNo($client, $startMessageNo, $groupDetails);
                if ($lastValidMessageNo > 0 && $lastValidMessageNo < $startMessageNo) {
                    $this->logger->info("Last Valid Message No is $lastValidMessageNo");
					$messageNo = $lastValidMessageNo;
                } else {
                    $this->logger->info("The message $messageNo not deleted, but unavailable");
					sleep(360); // sleep for an hour
                }
            } else {
                $messageDetails = $this->grabMessage($sourceHtml);
                $messageHandler->handleMessage($messageDetails);
				if ($messageDetails['message_no'] != $startMessageNo) {
					$this->logger->info("Archived $messageNo");
				}
				$messageNo = $messageDetails['next_message_no'];
            }
        }
    }
}
?>
