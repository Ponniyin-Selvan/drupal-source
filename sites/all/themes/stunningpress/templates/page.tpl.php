<?php
// $Id$
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php print $head ?>
    <title><?php print $head_title ?></title>
    <?php print $styles ?>
    <?php print $scripts ?>
<?php print phptemplate_get_ie_fix(); ?>
</head>
<body class="<?php print $body_classes; ?>">
<div id="wrapper">
  <div id="container" class="container">  
    <div class="span-24 top">
      <div class="span-17">
        <?php if (isset($primary_links)) : ?>
        <div id="pagemenucontainer">
          <?php print theme('links', $primary_links, array('class' => 'links primary-links', 'id' => 'pagemenu')) ?>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($search_box): ?><div id="topsearch" class="span-7 last"><?php print $search_box ?></div><?php endif; ?>
    </div>
    <div id="header" class="span-24">
      <?php
            // Prepare header
            $site_fields = array();
            if ($site_name) {
            $site_fields[] = check_plain($site_name);
            }
            if ($site_slogan) {
            $site_fields[] = check_plain($site_slogan);
            }
            $site_title = implode(' ', $site_fields);
          ?>
            <div id="logo" class="span-12"><a href="<?php print check_url($front_page);?>" title="<?php print $site_title;?>">
                <?php 
                if ($logo) {
                    print '<img class="logoimg" src="'. check_url($logo) .'" alt="'. $site_title .'" />';
                } else {
                    print '<img class="logoimg" src="'. $theme_basepath .'images/logo.png" alt="'. $site_title .'" />';
                }?>
            </a>
            </div><!-- logo -->
            
			<?php if ($header_ads_code_468x60): ?>
      <div class="ads468x60 span-12 last">
          <?php print $header_ads_code_468x60;?>
      </div><!-- //ads -->
      <?php endif; ?>
    </div>
    
    <div class="span-24">
      <?php if (isset($secondary_links)) : ?>
      <div class="navcontainer">
          <?php print theme('links', $secondary_links, array('class' => 'links secondary-links', 'id' => 'nav')); ?>
      </div>
      <?php endif; ?><!-- // secondary links -->
    </div>
    
    <div class="span-24" id="contentwrap">
    	<!-- content -->
			<div class="span-16">
				<div id="content">	
						<?php print $breadcrumb; ?>
            <?php if ($mission): print '<div id="mission">'. $mission .'</div>'; endif; ?>
            <?php if ($tabs): print '<div id="tabs-wrapper" class="clear-block">'; endif; ?>
            <?php if ($title): print '<h2'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h2>'; endif; ?>
            <?php if ($tabs): print '<ul class="tabs primary">'. $tabs .'</ul></div>'; endif; ?>
            <?php if ($tabs2): print '<ul class="tabs secondary">'. $tabs2 .'</ul>'; endif; ?>
            <?php if ($show_messages && $messages): print $messages; endif; ?>
            <?php print $help; ?>
            
            <?php if ($slideshow): ?>
            <div class="slideshow">
                <?php print $slideshow;?>
            </div><!-- //slideshow -->
            <?php endif; ?>
            
            <?php if ($contenttop): ?>
            <div class="contenttop">
                <?php print $contenttop;?>
            </div><!-- //contenttop -->
            <?php endif; ?>
            
            <?php print $content ?>
            
            <?php if ($contentbt): ?>
            <div class="contentbt">
                <?php print $contentbt;?>
            </div><!-- //content bottom -->
            <?php endif; ?>
				</div>
			</div>
      <!-- // content -->
			<!-- sidebar -->
			<div class="span-8 last">
        <div class="sidebar">
          <div class="addthis_toolbox">   
              <div class="custom_images">
                    <a class="addthis_button_twitter"><img src="<?php print $theme_basepath; ?>images/socialicons/twitter.png" width="32" height="32" alt="Twitter" /></a>
                    <a class="addthis_button_delicious"><img src="<?php print $theme_basepath; ?>images/socialicons/delicious.png" width="32" height="32" alt="Delicious" /></a>
                    <a class="addthis_button_facebook"><img src="<?php print $theme_basepath; ?>images/socialicons/facebook.png" width="32" height="32" alt="Facebook" /></a>
                    <a class="addthis_button_digg"><img src="<?php print $theme_basepath; ?>images/socialicons/digg.png" width="32" height="32" alt="Digg" /></a>
                    <a class="addthis_button_stumbleupon"><img src="<?php print $theme_basepath; ?>images/socialicons/stumbleupon.png" width="32" height="32" alt="Stumbleupon" /></a>
                    <a class="addthis_button_favorites"><img src="<?php print $theme_basepath; ?>images/socialicons/favorites.png" width="32" height="32" alt="Favorites" /></a>
                    <a class="addthis_button_more"><img src="<?php print $theme_basepath; ?>images/socialicons/more.png" width="32" height="32" alt="More" /></a>
            </div>
              <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js?pub=xa-4a65e1d93cd75e94"></script>
          </div>
      
      		<?php if ($rss_link): ?>
          <div class="rssbox">
            <a href="<?php print $rss_link; ?>"><img src="<?php print $theme_basepath; ?>images/rss.png"  alt="RSS Feed" title="RSS Feed" style="vertical-align:middle; margin-right: 5px;"  /></a><a href="<?php print $rss_link; ?>"><?php print $rss_text; ?></a>
          </div>
          <?php endif; ?>
					<?php if ($twitter_username): ?>
          <div class="twitterbox">
            <a href="http://twitter.com/<?php print $twitter_username; ?>"><img src="<?php print $theme_basepath; ?>images/twitter.png"  alt="<?php print $twitter_text; ?>" style="vertical-align:middle; margin-right: 5px;"  /></a><a href="http://twitter.com/<?php print $twitter_username; ?>"><?php print $twitter_text; ?></a>
          </div>
          <?php endif; ?>
      
					<?php if ($sidebar_top_ads): ?>
          <div class="sidebaradbox clearfix">
              <?php print $sidebar_top_ads;?>
          </div><!-- // ads 125 -->
          <?php endif; ?>
    			
          <?php if ($video_code):?>
          <div class="block sidebarvideo">
            <h3>Featured Video</h3>
            <div class="blcontent">
            	<?php print $video_code;?>
            </div>
          </div>
          <?php endif; ?>
          
          <?php if ($right): ?>
      		<?php print $right;?>
      		<?php endif; ?>
          
          <?php if ($sidebar_bt_ads): ?>
          <div class="sidebaradbox clearfix">
              <?php print $sidebar_bt_ads;?>
          </div><!-- // ads -->
          <?php endif; ?>
      
        </div>
      </div>
<!-- //sidebar -->
	</div>
</div>
    <div id="footer">Copyright &copy; <a href="http://newwpthemes.com/"><strong>New WP Themes</strong></a>  - Drupal Themes by <a href="http://abthemes.com/"><strong>Abthemes</strong></a>
    <?php if ($footer): ?>
		<?php print $footer;?>
    <?php endif; ?>
    </div>
		<?php // This theme is released free for use under creative commons licence. http://creativecommons.org/licenses/by/3.0/
        // All links in the footer should remain intact.  ?>
    <div id="footer2">Powered by <a href="http://drupal.org/"><strong>Drupal</strong></a></div>
</div>
<?php print $closure; ?>
</body>
</html>