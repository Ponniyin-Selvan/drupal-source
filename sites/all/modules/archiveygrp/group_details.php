<?php
class GroupDetails {

	private $details = array();
	/*= array("groupName" => "", 
	                         "groupDescription" => "",
							 "userId" => "",
							 "password" => "",
							 "lastArchivedMessageNo" => 0,
							 "tablePrefix" => ""); */
	
    public function __get($name) {
        if (isset($this->details[$name])) {
            return $this->details[$name];
        } else {
            return null;
        }
    }

    public function __set($name, $value) {
        $this->details[$name] = $value;
    }

    public function set($name, $value) {
        $this->__set($name, $value);
    }}
?>