<?php

$sql = "SELECT dst, ps.cid ".
       "  FROM dps_url_alias,". 
       "      (SELECT nid, cid ". 
       "         FROM dps_ponniyinselvan_log ps".
       "        WHERE ps.message_no = ?) ps".
       " WHERE src = concat('node/', ps.nid)";
       
$db = new PDO("mysql:host=mysql-prod.vittal.dreamhosters.com;dbname=prodpsvp", 
        "vittal", "Vittal\$ms7002");

$db->exec('SET CHARACTER SET utf8');
$url = '';       
$cid = 0;
//echo 'id = '. $_REQUEST['id'];

$stmt = $db->prepare($sql);
$stmt->bindParam(1, $_REQUEST['id'], PDO::PARAM_INT);
$stmt->bindColumn(1, $url);
$stmt->bindColumn(2, $cid);
$stmt->execute();
$stmt->fetch(PDO::FETCH_BOUND);
$stmt->closeCursor();

if ($cid != 0) {
    $url = $url . "#" . $cid;
}

//echo "/$url";

header ("Location: $url");
return;
?>