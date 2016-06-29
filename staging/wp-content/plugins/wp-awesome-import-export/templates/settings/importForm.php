<?php
/*
 *  (View file)
 * 
 *  ALL Variables available from the called script
 *  
 *  Import Form
 */
?>
<form method="post" id="importSettingForm" class="submitWPAIEForm">
    <div class="formControls">
        <div class="heading">
            <span>Import Settings</span>
        </div>    
        <div class="control-group">
            <label class="control-label">Select Post Columns</label>
            <div class="controls">
                <select class="small w-wrap" id="postColumns" name="postColumns[]" multiple="multiple">
                    <?php
                    foreach ($postColums as $postColumn) {
                        $selected = "";
                        if (in_array($postColumn, $selectedPostCols))
                            $selected = "selected=selected";
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $postColumn; ?>"><?php echo $postColumn; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <span title="Only selected post status will be showed at the time of import for mapping post columns." class="help-inline"></span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Default Post Status</label>
            <div class="controls">
                <select class="small w-wrap" id="postStatus" name="postStatus">
                    <?php
                    foreach ($postStatus as $status) {
                        ?>
                        <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
                        <?php }
                    ?></select>
                <span class="help-inline" title="If you don't enter any information for post status in import, then selected post status will be saved."></span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Select Post Meta</label>
            <div class="controls">
                <select class="small w-wrap" id="postMeta" name="postMeta[]" multiple="multiple"
                        title="Only selected post meta will be showed at the time of import for mapping post meta.">
                            <?php
                            foreach ($metaFields as $meta) {
                                $selected = "";
                                if (in_array($meta, $selectedPostMetaCols))
                                    $selected = "selected=selected";
                                ?>
                        <option <?php echo $selected; ?> value="<?php echo $meta; ?>"><?php echo $meta; ?></option>
                                <?php
                            }
                            ?></select>
                <span class="help-inline" title="Only selected post meta will be showed at the time of import for mapping post meta."></span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Select Custom Taxonmoies</label>
            <div class="controls">
                <select class="small w-wrap" id="customTaxonomies" name="customTaxonomies[]" multiple="multiple">
                    <?php
                    foreach ($customTaxonomies as $taxonomies) {
                        $selected = "";
                        if (in_array($taxonomies, $selectedCustomTaxCols))
                            $selected = "selected=selected";
                        ?>
                        <option  <?php echo $selected; ?> value="<?php echo $taxonomies; ?>"><?php echo $taxonomies; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <span class="help-inline" title="Only selected custom taxonmoies will be showed at the time of import for mapping custom taxonmoies.">
                </span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Default Post Date</label>
            <div class="controls">
                <select class="small w-wrap" id="postDate" name="postDate">
                    <option value="currentdate">Current Date</option>
                    <option value="setdate">Set Date</option>
                </select>
                <input type="text" placeholder="yyyy-mm-dd format" name="setDate" id="setDate" class="datepicker" style="display:none" />
                <span class="help-inline" title="If you don't enter any information for post date in import, then selected post date information will be saved."></span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Duplicate Post Title</label>
            <div class="controls">
                <select class="small w-wrap" id="duplicateEntry" name="duplicateEntry">
                    <option value="skip" <?php if ($option["duplicateEntry"] == "skip") echo "selected=selected"; ?> >Skip Post</option>
                    <option value="update" <?php if ($option["duplicateEntry"] == "update") echo "selected=selected"; ?>>update Post</option>
                </select>
                <span class="help-inline" title="What to do when import contains duplicate post title? Skip that post or Update that post."></span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"> Category Separator</label>
            <div class="controls">
                <input type="text" value="<?php echo $option["categorySeparator"]; ?>" name="categorySeparator" id="categorySeparator" title="Category separator for post import" />
                <span class="help-inline" title="Category separator for post import"></span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"> Custom Taxo. Separator</label>
            <div class="controls">
                <input type="text" value="<?php echo $option["termSeparator"]; ?>" name="termSeparator" id="termSeparator" />
                <span class="help-inline" title="Custom Taxonomy separator for importing post"></span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"> Woocommerce Product Meta</label>
            <div class="controls">
                <select class="small w-wrap" id="wooMeta" name="wooMeta[]" multiple="multiple">
                    <?php
                    foreach ($allWooMeta as $meta) {
                        $selected = "";
                        if (in_array($meta, $selectedWooMeta))
                            $selected = "selected=selected";
                        ?>
                                            <option  <?php echo $selected; ?> value="<?php echo $meta; ?>"><?php echo $meta; ?></option>
                                            <?php
                                        }
                                        ?>
                </select>
                <span class="help-inline" title="List of woocommerce product meta fields "></span>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">        
                <input type="submit" value="Save" name="submitImportSettings" id="submitImportSettings" class="submit" />
            </div>
        </div>
    </div>
</form>