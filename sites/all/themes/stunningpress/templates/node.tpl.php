<?php
// $Id$
?>
<!-- start node.tpl.php -->
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">
<?php if ($page == 0): ?>
  <h2 class="title"><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
<?php endif; ?>

  <?php if ($submitted): ?>
    <div class="postdate"><?php print $submitted; ?></div>
  <?php endif; ?>

  <div class="entry">
    <?php print $content ?>
  </div>

<?php if ($page != 0): ?>
    <div class="meta">
    <?php if ($taxonomy): ?>
      <div class="terms"><?php print $terms ?></div>
    <?php endif;?>
    </div>
<?php endif; ?>
    <?php if ($links): ?>
      <div class="morelinks"><?php print $links; ?></div>
    <?php endif; ?>

<?php print $node_content_block;?>
</div>
<!-- end node.tpl.php -->