<?php
$topic_sql = "SELECT psl.nid, psl.cid ".
		     "  FROM dps_node nd, ps_posts ps, dps_ponniyinselvan_log psl".
		     " WHERE ps.phpbb_topic_id = ?".
		     "   AND psl.message_no = ps.id".
		     "   AND nd.nid = psl.nid";

$post_sql = "SELECT psl.nid, psl.cid ".
		     "  FROM dps_node nd, ps_posts ps, dps_ponniyinselvan_log psl".
		     " WHERE ps.phpbb_post_id = ?".
		     "   AND psl.message_no = ps.id".
		     "   AND nd.nid = psl.nid";
			 
$msg_sql   = "SELECT nid, cid ". 
             "  FROM dps_ponniyinselvan_log ps".
             " WHERE ps.message_no = ?";
             
$type = $_REQUEST['type'];
$from_clause = "";

if ($type === "topic") {
    $from_clause = $topic_sql;
} else if ($type === "post") {
           $from_clause = $post_sql;
} else if ($type === "msg") {
           $from_clause = $msg_sql;
} else {
    return;
}

$sql = "SELECT dst, ps.cid ".
       "  FROM dps_url_alias, (". 
       $from_clause .") ps".
       " WHERE src = concat('node/', ps.nid)";
       
$db = new PDO("mysql:host=localhost;dbname=prodpsvp", 
        "root", "SECRETREDACTED");

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
    $url = $url . "#comment-" . $cid;
}

//echo "/$url";

header ("Location: /$url", TRUE, 301);
return;
?>
