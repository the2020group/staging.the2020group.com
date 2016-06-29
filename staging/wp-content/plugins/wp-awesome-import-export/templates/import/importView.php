<?php
/*
 *  (View file)
 * 
 *  ALL Variables available from the called script
 *  
 *  Import View
 */
?>
<div id="awesome-content" class="import">
<div id="tabs" class="tabs">
  <nav>
    <ul class="tabElements">
      <li id="tabPost"><a href="#tabs-1" class="icon-shop">Post</a></li>
      <li id="tabPage"><a href="#tabs-2"  class="icon-shop">Pages</a></li>
      <li id="tabCategory"><a href="#tabs-3" class="icon-shop tab-current">Categories/Tags</a></li>
      <li id="tabComment"><a href="#tabs-4" class="icon-shop">Comments</a></li>
      <li id="tabUser"><a href="#tabs-5" class="icon-shop">User/Roles</a></li>
      <li id="tabTaxonomy"><a href="#tabs-6" class="icon-shop">Custom Taxonomies</a></li>
      <li id="tabCustomPost"><a href="#tabs-7" class="icon-shop">Custom Post</a></li>
      <li id="tabWPTable"><a href="#tabs-8" class="icon-shop">Any WP Table</a></li>
      <li id="tabPlugins"><a href="#tabs-9" class="icon-shop">Plugins</a></li>
    </ul>
  </nav>
  <div class="wp-awesome-content">
    <section id="tabs-1">
      <?php getUploadFileControl("Post");  
            if(isset($_POST["uploadFileSubmitPost"]))
              {
                  $data=renderUploadedFile();
                  mapFields($data,"Post");
              }
              ?>
    </section>
    <section id="tabs-2">
      <?php getUploadFileControl("Page");
            if(isset($_POST["uploadFileSubmitPage"]))
              {
                  $data=renderUploadedFile();
                  mapFields($data,"Page");
              }
              ?>
    </section>
    <section id="tabs-3">
      <?php getUploadFileControl("Category");
            if(isset($_POST["uploadFileSubmitCategory"]))
              {
                  $data=renderUploadedFile();
                  mapFields($data,"Category");
              }?>
    </section>
    <section id="tabs-4">
      <?php getUploadFileControl("Comment");
            if(isset($_POST["uploadFileSubmitComment"]))
              {
                  $data=renderUploadedFile();
                  mapFields($data,"Comment");
              }?>
    </section>
    <section id="tabs-5">
      <?php getUploadFileControl("User");
             if(isset($_POST["uploadFileSubmitUser"]))
              {
                  $data=renderUploadedFile();
                  mapFields($data,"User");
              }
              ?>
    </section>
    <section id="tabs-6">
      <?php getUploadFileControl("Taxonomy");
            if(isset($_POST["uploadFileSubmitTaxonomy"]))
              {
                  $data=renderUploadedFile();
                  mapFields($data,"Taxonomy");
              }?>
    </section>
    <section id="tabs-7">
      <?php getUploadFileControl("CustomPost");
            if(isset($_POST["uploadFileSubmitCustomPost"]))
              {
                  $data=renderUploadedFile();
                  mapFields($data,"CustomPost");
              }?>
    </section>
    <section id="tabs-8">
      <?php getUploadFileControl("WPTable");
            if(isset($_POST["uploadFileSubmitWPTable"]))
              {
                  $data=renderUploadedFile();
                  mapFields($data,"WPTable");
              }?>
    </section>
    <section id="tabs-9">  
			<?php getUploadFileControl("Plugins");
            if(isset($_POST["uploadFileSubmitPlugins"]))
              {
                  $data=renderUploadedFile();
                  mapFields($data,"Plugins");
              }?>
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