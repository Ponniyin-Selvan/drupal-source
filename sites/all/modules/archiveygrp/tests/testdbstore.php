<?php
//$text = file_get_contents("test.dat");
//echo gzuncompress(gzcompress($text));

$content = '';
$db = new PDO("mysql:host=localhost;dbname=psvp", "root", "");
$db->exec('SET CHARACTER SET utf8');
$sql = 'SELECT UNCOMPRESS(original_source) FROM ygrp_messages WHERE message_no = ?';
$stmt = $db->prepare($sql);
$stmt->bindColumn(1, $content, PDO::PARAM_STR);
$stmt->bindParam(1, $argv[1], PDO::PARAM_INT);
if ($stmt->execute() === false) {
	echo "Error Code ".$stmt->errorCode()." ==> ".print_r($stmt->errorInfo(), true);
}
$stmt->fetch(PDO::FETCH_BOUND);
echo $content;
?>
