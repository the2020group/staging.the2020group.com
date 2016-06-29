<?php
/*
 *  (View file)
 * 
 *  ALL Variables available from the called script
 *  
 *  Settings View
 */
?>
<div id="awesome-content" class="settings">
<div id="tabs" class="tabs">
  <nav>
    <ul class="tabElements">
      <li id="tabImport"><a href="#tab-1" class="icon-shop">Import Settings</a></li>
      <li id="tabExport"><a href="#tab-2" class="icon-cup">Export Settings</a></li>
      <li id="tabGeneral"><a href="#tab-3" class="icon-food">General Settings</a></li>
    </ul>
  </nav>
  <div class="wp-awesome-content">
    <section id="tab-1">
      <?php getSettingForm("IMPORT");?>
    </section>
    <section id="tab-2">
      <?php getSettingForm("EXPORT");?>
    </section>
    <section id="tab-3">
      <?php getSettingForm("GENERAL");?>
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