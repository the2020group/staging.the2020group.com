<?php
/*
 *  (View file)
 * 
 *  ALL Variables available from the called script
 *  
 *  File Upload Form 
 */
?>
<div id="uploadDetails">
    <div class="heading">
        <span>Upload <?php echo $operationCategory;?> File</span>
    </div>
    <div class="result import_result" style="display:none" id="result<?php echo $operationCategory; ?>">
        <strong class='red'><?php if(isset($error)) echo $error; ?></strong>
        <table class='widefat'>
            <thead>
                <tr><th colspan='2'><strong><?php _e('Result', 'wpacs'); ?></strong></th></tr>
            </thead>
            <tbody>
                <tr><th><?php _e('Records Read:', 'wpacs'); ?></th>
                    <td class="recordsRead" id="recordsRead<?php echo $operationCategory; ?>">
                        <strong><?php echo $output["recordsRead"]; ?></strong></td></tr>
                <tr><th><?php _e('Records Added/Updated:', 'wpacs'); ?></th>
                    <td class="recordsAdded" id="recordsAdded<?php echo $operationCategory; ?>">
                        <strong><?php echo $output["recordsInserted"]; ?></strong></td></tr>
                <tr><th><?php _e('Records Skipped:', 'wpacs'); ?></th>
                    <td class="recordsSkipped" id="recordsSkipped<?php echo $operationCategory; ?>">
                        <strong><?php echo $output["recordsSkipped"]; ?></strong></td></tr>
            </tbody>
        </table>
    </div> 
    <form method="post" enctype="multipart/form-data" class="uploadForm submitWPAIEForm" data-uploadtype="<?php echo $operationCategory; ?>">
        <div class="formControls">
            <?php
            if ($operationCategory === "Category") {
                ?>
                <div class="control-group">
                    <label class="control-label">Select Category/Tags<span class="star">*</span></label>
                    <div class="controls">
                        <select class="selectTaxoData" name="taxonomyType"  id="taxonomyType">
                            <option value="post_tag">Tags</option>
                            <option value="category">Category</option>                
                        </select>
                    </div>
                </div> 
            <?php } ?> 

            <?php
            if ($operationCategory === "WPTable") {
                $ACS = new ACS();
                $tables = $ACS->getDBTables();
                ?>
                <div class="control-group">
                    <label class="control-label">Select Table<span class="star">*</span></label>
                    <div class="controls">
                        <select name="wpTables" id="wpTables" class="selectData" >
                            <option value="0">--Select--</option>
                            <?php foreach ($tables as $table) { ?>
                                <option value="<?php echo $table; ?>"><?php echo $table; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div> 
            <?php
            } else if ($operationCategory === "CustomPost") {
                $ACS = new ACS();
                $customPostTypes = $ACS->getCustomPostType();
                ?>
                <div class="control-group">
                    <label class="control-label">Select Custom Post Type<span class="star">*</span></label>
                    <div class="controls">
                        <select name="customPostType" id="customPostType" class="selectData">
                            <option value="0">--Select--</option>
                            <?php foreach ($customPostTypes as $customPost) { ?>
                                <option value="<?php echo $customPost; ?>"><?php echo $customPost; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div> 
            <?php
            } else if ($operationCategory === "Taxonomy") {
                $ACS = new ACS();
                $customTaxonomies = $ACS->getCustomTaxonomies();
                ?>
                <div class="control-group">
                    <label class="control-label">Select Custom Taxonomy<span class="star">*</span></label>
                    <div class="controls">
                        <select name="customTaxonomy" id="customTaxonomy" class="selectData">
                            <option value="0">--Select--</option>
                                 <?php foreach ($customTaxonomies as $customTaxonomy) { ?>
                                <option value="<?php echo $customTaxonomy; ?>"><?php echo $customTaxonomy; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div> 
                <?php
                } else if ($operationCategory === "Plugins") {
                    ?>
                <div class="control-group">
                    <label class="control-label">Select Plugin<span class="star">*</span></label>
                    <div class="controls">
                        <select name="thirdpartyplugins" id="thirdpartyplugins" class="selectData">
                            <option value="0">--Select--</option>
                            <option value="woocommerce_product">Woocommerce-Products</option>
                        </select>
                    </div>
                </div> 
                <?php } ?>  
            <div class="control-group">
                <label class="control-label">Upload File<span class="star">*</span></label>
                <div class="controls">
                    <input type="file" name="uploadFile" id="uploadFile<?php echo $operationCategory; ?>"/>      
                </div>
            </div> 
            
            <div class="control-group">
                 <label class="control-label"></label>
                 <div class="controls">
                     <span class="span_or">OR</span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">Upload from URL<span class="star">*</span></label>
                <div class="controls">
                    <input type="text" name="uploadFileUrl" placeholder="Enter complete path of the file" id="uploadFileUrl<?php echo $operationCategory; ?>"/>      
                </div>
            </div> 
            
            <div class="control-group">
                <label class="control-label"></label>
                <div class="controls" style="text-align:left;">
                    <input type="hidden" value="<?php echo $operationCategory; ?>" name="operationCategory" />
                    <input style="margin-left:54px;" type="submit" name="uploadFileSubmit<?php echo $operationCategory; ?>" value="Submit" class="submit"/>
                    <?php 
                    if($operationCategory=='Post')
                       echo '<input type="hidden" id="lastActivateTabId" name="lastActivateTabId" value="0"/>';
                    if($operationCategory=='Page')
                       echo '<input type="hidden" id="lastActivateTabId" name="lastActivateTabId" value="1"/>';
                    if($operationCategory=='Category')
                       echo '<input type="hidden" id="lastActivateTabId" name="lastActivateTabId" value="2"/>';
                    if($operationCategory=='Comment')
                       echo '<input type="hidden" id="lastActivateTabId" name="lastActivateTabId" value="3"/>';
                    if($operationCategory=='User')
                       echo '<input type="hidden" id="lastActivateTabId" name="lastActivateTabId" value="4"/>';
                    if($operationCategory=='Taxonomy')
                       echo '<input type="hidden" id="lastActivateTabId" name="lastActivateTabId" value="5"/>';
                    if($operationCategory=='CustomPost')
                       echo '<input type="hidden" id="lastActivateTabId" name="lastActivateTabId" value="6"/>';
                    if($operationCategory=='WPTable')
                       echo '<input type="hidden" id="lastActivateTabId" name="lastActivateTabId" value="7"/>';
                    if($operationCategory=='Plugins')
                       echo '<input type="hidden" id="lastActivateTabId" name="lastActivateTabId" value="8"/>';
                    ?>
                </div>
            </div>
        </div> 
    </form>
</div>