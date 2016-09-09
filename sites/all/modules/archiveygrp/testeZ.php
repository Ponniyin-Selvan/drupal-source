<?php
require dirname(__FILE__).'/plugins/eZ/Base/src/ezc_bootstrap.php';
$parser = new ezcMailParser();
$set = new ezcMailVariableSet( $email );
$mail = $parser->parseMail( $set );

?>
