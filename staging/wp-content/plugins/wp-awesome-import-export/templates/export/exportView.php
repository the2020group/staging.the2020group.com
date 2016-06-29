<?php
/*
 *  (View file)
 * 
 *  ALL Variables available from the called script
 *  
 *  Export View
 */
?>
<div id="awesome-content" class="export">
<div id="tabs" class="tabs">
  <nav>
    <ul class="tabElements">
      <li id="tabPost"><a href="#tabs-1" class="icon-shop">Post</a></li>
      <li id="tabPage"><a href="#tabs-2"  class="icon-shop">Pages</a></li>
      <li id="tabCategory"><a href="#tabs-3" class="icon-shop tab-current">Categories/Tags</a></li>
      <li id="tabComment"><a href="#tabs-4" class="icon-shop">Comments</a></li>
      <li id="tabUser"><a href="#tabs-5" class="icon-shop">User/Roles</a></li>
      <li id="tabTaxonomy"><a href="#tabs-6" class="icon-shop">Custom Taxo.</a></li>
     <li id="tabCustomPost"><a href="#tabs-7" class="icon-shop">Custom Post</a></li>
      <li id="tabWPTable"><a href="#tabs-8" class="icon-shop">WP Tables</a></li>
      <?php if(WPAIE_SQL_ALLOW) {?>
      <li id="tabSQL"><a href="#tabs-9" class="icon-shop">SQL</a></li>
      <?php }?>
      <li id="tabPlugins"><a href="#tabs-10" class="icon-shop">Plugins</a></li>
    </ul>
  </nav>
  <div class="wp-awesome-content">
    <section id="tabs-1">
      <?php getExportPostForm("POST");?>
    </section>
    <section id="tabs-2">
      <?php getExportPostForm("PAGE");?>
    </section>
    <section id="tabs-3">
      <?php getTaxonomyForm("Category");?>
    </section>
    <section id="tabs-4"> 
      <?php getCommentForm("Comment");?>
    </section>
    <section id="tabs-5"> 
    	<?php getUserForm("User");?>
    </section>
    <section id="tabs-6">
      <?php getTaxonomyForm("Taxonomy");?>
    </section>
    <section id="tabs-7">
      <?php getExportPostForm("CustomPost");?>
    </section>
    <section id="tabs-8">
      <?php getWPTableForm("WPTable");?>
    </section>
      <?php if(WPAIE_SQL_ALLOW) { ?>
    <section id="tabs-9">
      <?php getSQLForm("SQL");?>
    </section>
      <?php }?>
    <section id="tabs-10"> 
      <?php getExportPluginForm("Plugins");?>
    </section>
  </div>
</div>
</div>
<script>
  jQuery(function($) {
    $( "#tabs" ).tabs().addClass( "tab-current" );
    $('#lastActivateTabId').val(0);
    $('.ui-tabs-active').addClass('tab-current');
    $( "#tabs li" ).click(function(){
      $( "#tabs li" ).removeClass('tab-current');
      $(this).addClass('tab-current');
    });
    
    <?php if(isset($_POST['lastActivateTabId'])) { ?>
        $( "#tabs li" ).removeClass('tab-current');
        $( "#tabs li" ).eq(<?php echo $_POST['lastActivateTabId'];?>).addClass('tab-current');
        $( "#tabs" ).tabs({ active: <?php echo $_POST['lastActivateTabId'];?> });
    <?php } ?>
  });
</script>

