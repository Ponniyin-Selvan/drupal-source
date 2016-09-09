<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language; ?>" xml:lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>">

<head>
  <title><?php print $head_title ?></title>
  
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <meta property="fb:page_id" content="157251800966935" />
  <?php /* Setting the default style sheet language for validation */  ?>
  
  <?php print $head ?>


  <?php print $styles ?>
  
  <!--[if IE 6]>
    <link rel="stylesheet" type="text/css" href="<?php print base_path(). path_to_theme(); ?>/iestyles/ie6.css" />
<![endif]-->

  <!--[if IE 7]>
    <link rel="stylesheet" type="text/css" href="<?php print base_path(). path_to_theme(); ?>/iestyles/ie7.css" />
<![endif]-->
  
  
  <?php print $scripts ?>
</head>


<body>

  <div id="utilities">
  <?php print $search_box ?>
  
  <?php if (isset($primary_links)) : ?>
  <?php print '<div id="plinks">'; ?>
     
             <?php
		
		if(theme_get_setting('menutype')== '0'){ 
		 
			print theme('links', $primary_links, array('class' => 'links primary-links'));	
		}
	
	   else {
	    
			print phptemplate_get_primary_links(); 
		}
	   
	   ?>
     
               <?php print '</div>'; ?>
        <?php endif; ?>
  </div>


<div id="page">

  <div id="header" style="<?php print "background: #000033 url($logo) no-repeat 0px 0px;"?>">
  </div>

 
         <?php if (($secondary_links)) : ?>
      <?php print '<div id="submenu">' ?>
          <?php print theme('links', $secondary_links, array('class' => 'links secondary-links')) ?>
          <?php print '</div><div class="stopfloat"></div>' ?>
        <?php endif; ?>

       
   

  <div class="wrapper"><!--wrapper:defines whole content margins-->
  
  
   <div id="primary" class=<?php print '"'.marinelli_width( $left, $right).'">' ?>
               <div class="singlepage">
	  <?php print $breadcrumb; ?> 
	  
   <?php if ($mission): print '<div id="sitemission"><p>'. $mission .'</p></div>'; endif; ?>
         
		 <?php 
		 if ($title): 
		 
		 if ($is_front){/* if we are on the front page use <h2> for title */
		 
		 print '<h2'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h2>'; 
		 
		 }
		else {print '<h1'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h1>';  /* otherwise use <h1> for node title */
		 }
		 
		 endif; ?>
		 
          <?php if ($tabs): print '<div class="tabs">'.$tabs.'</div>'; endif; ?>
        <?php if ($help) { ?><div class="help"><?php print $help ?></div><?php } ?>
	  <?php if (user_is_logged_in() && $show_messages && $messages): print $messages; endif; ?>
<div class="drdot">
<hr />
</div>
         
          
 <?php print $content ?>
      </div>

    </div>


   
   
   
        	<!-- left -->
        <?php if ($left) { ?>
          <div class="lsidebar">

          
            <?php print $left ?>
            
          </div><!-- end left -->
        <?php } ?>

   	<!-- right -->
        <?php if ($right) { ?>
          <div class="rsidebar">
 
            <?php print $right ?>
         
          </div><!-- end right -->
        <?php } ?>



 <div class="clear"></div>

  </div>
</div>
<!-- Close Page -->
<div id="footer">
<?php print $footer ?>
<?php print $footer_message ?>
</div>
<?php print $closure ?>
</body>
</html>
