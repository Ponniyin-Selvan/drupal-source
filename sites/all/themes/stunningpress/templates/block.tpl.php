<?php
// $Id$
?>
<!-- start block.tpl.php -->
<div id="block-<?php print $block->module .'-'. $block->delta; ?>" class="block block-<?php print $block->module ?>">

<?php if (!empty($block->subject)): ?>
  <h3><?php print $block->subject ?></h3>
<?php endif;?>

  <div class="blcontent"><?php print $block->content ?></div>
</div>
<!-- end block.tpl.php -->