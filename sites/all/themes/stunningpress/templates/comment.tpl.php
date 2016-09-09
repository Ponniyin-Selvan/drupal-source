<?php
// $Id$
?>
<!-- start comment.tpl.php -->
<div class="comment<?php print ($comment->new) ? ' comment-new' : ''; print ' '. $status; print ' '. $zebra; ?>">
  <div class="comment-author clearfix">
	  <?php print $picture ?>
      <div class="submitted">
	  	<?php print $author; ?>
        <?php print t(' say:');?>
        <div class="commentmetadata"><?php  print format_date($comment->timestamp, 'custom', 'F d, Y'); ?> at <?php  print format_date($comment->timestamp, 'custom', 'g:i a'); ?></div>
      </div>
  </div>
  
    <div class="content">
      <?php print $content ?>
      <?php if ($signature): ?>
      <div class="clear-block">
        <div>—</div>
        <?php print $signature ?>
      </div>
      <?php endif; ?>
    </div>

  <?php if ($links): ?>
    <div class="links"><?php print $links ?></div>
  <?php endif; ?>
</div>
<!-- end comment.tpl.php -->