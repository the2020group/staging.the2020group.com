<?php
/*
 *  (View file)
 * 
 *  ALL Variables available from the called script
 *  
 *  General Form
 */
?>
<form method="post" id="generalSettingForm" class="submitWPAIEForm">
  <div class="formControls">
 	<div class="heading">
  		<span>General Settings</span>
 	</div>    
    <div class="control-group">
      <label class="control-label">CSV Delimiter</label>
      <div class="controls">
        <input type="text" name="csvDelimiter" id="csvDelimiter" value="," />
        <span title="CSV File Delimiter" class="help-inline"></span>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label"> XMl Root Element</label>
      <div class="controls">
        <input type="text" name="xmlRootElement" id="xmlRootElement" value="root" />
        <span title="xml root element to be exported." class="help-inline"></span>
      </div>
    </div>
    <div class="control-group">
      <div class="controls">        
      	<input type="submit" value="Save" name="submitGeneralSettings" id="submitGeneralSettings" class="submit" />
      </div>
    </div>	
   </div> 
</form>  