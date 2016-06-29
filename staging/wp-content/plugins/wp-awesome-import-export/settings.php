<?php
function wpaie_setting()
{
    if (isset($_POST["submitImportSettings"])) {
        $updateOptions = array(
            'duplicateEntry' => $_POST["duplicateEntry"],
            'postFields' => $_POST["postColumns"],
            "postMetaFields" => $_POST["postMeta"],
            "postStatus" => $_POST["postStatus"],
            "customTaxonomiesFields" => $_POST["customTaxonomies"],
            "postDate" => $_POST["postDate"],
            "dateval" => $_POST["dateval"],
            "termSeparator" => $_POST["termSeparator"],
            "categorySeparator" => $_POST["categorySeparator"],
            "woocommerceProductMeta" => $_POST["wooMeta"]
        );
        
        update_option('wpaieOptions', $updateOptions);
    } else if (isset($_POST["submitExportSettings"])) {
        $updateOptions = array(
            'authorDetails' => $_POST["authorDetails"]
        );
        
        update_option('wpaieOptions', $updateOptions);
    } else if (isset($_POST["submitGeneralSettings"])) {
        $updateOptions = array(
            'csvDelimiter' => $_POST["csvDelimiter"],
            'rootElement' => $_POST["rootElement"]
        );
        
        update_option('wpaieOptions', $updateOptions);
    }
    
    include('templates/settings/settingsView.php');
}

function getSettingForm($settingType)
{
    switch ($settingType) {
        case "IMPORT":
            getImportSettingForm();
            break;
        case "EXPORT":
            getExportSettingForm();
            break;
        case "GENERAL":
            getGeneralSettingForm();
            break;
    }
}
function getGeneralSettingForm()
{
    include('templates/settings/generalForm.php');
}
function getExportSettingForm()
{
    include('templates/settings/exportForm.php');
}
function getImportSettingForm()
{
    $ACS = new ACS();
    
    $postColums = array(
        'post_title',
        'post_content',
        'post_excerpt',
        'post_date',
        'post_name',
        'post_author',
        'post_parent',
        'post_status',
        'post_tag',
        'post_category',        
        'featured_image',
    );
    
    $postStatus = array(
        'publish',
        'future',
        'draft',
        'pending',
        'private',
        'auto-draft',
        'inherit',
        'trash'
    );
    
    $metaFields = $ACS->getPostMeta();
    
    $customTaxonomies = $ACS->getCustomTaxonomies();
    
    $option = get_option('wpaieOptions');
    
    $selectedPostCols = $option["postFields"];
    
    $selectedPostMetaCols = $option["postMetaFields"];
    
    $selectedCustomTaxCols = $option["customTaxonomiesFields"];
    
    $selectedWooMeta = $option["woocommerceProductMeta"];
    
    $allWooMeta = array(
        "_product_attributes",
        "_visibility",
        "_stock_status",
        "total_sales",
        "_downloadable",
        "_virtual",
        "_regular_price",
        "_sale_price",
        "_purchase_note",
        "_featured",
        "_weight",
        "_length",
        "_width",
        "_height",
        "_sku",
        "_sale_price_dates_from",
        "_sale_price_dates_to",
        "_price",
        "_sold_individually",
        "_stock",
        "_backorders",
        "_manage_stock",
        "post_views_count"
    );
    
    include('templates/settings/importForm.php');
}