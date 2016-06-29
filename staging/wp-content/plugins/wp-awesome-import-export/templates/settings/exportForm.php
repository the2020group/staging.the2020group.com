<?php
/*
 *  (View file)
 * 
 *  ALL Variables available from the called script
 *  
 *  Export Form
 */
?>
<form method="post" id="exportSettingForm" class="submitWPAIEForm">
  <div class="formControls">
 	<div class="heading">
  		<span>Export Settings</span>
 	</div>    
    <div class="control-group">
      <label class="control-label"> Author Details</label>
      <div class="controls">
        <select class="small w-wrap" id="authorDetails" name="authorDetails">
			<option value="authorId">Show Author Id</option>
            <option value="authorName">Show Author Name</option>
        </select>
        <span title="Whether to show authorId or authorName while exporting Posts" class="help-inline"></span>
      </div>
    </div>
    <div class="control-group">
      <div class="controls">        
      	<input type="submit" value="Save" name="submitExportSettings" id="submitExportSettings" class="submit" />
      </div>
    </div>	
   </div> 
</form>