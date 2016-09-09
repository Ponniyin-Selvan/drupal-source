<?php
interface iMessageHandler {
	public function handleMessage(&$messageDetails);
	public function handleInvalidMessage($sourceHtml);
}
?>