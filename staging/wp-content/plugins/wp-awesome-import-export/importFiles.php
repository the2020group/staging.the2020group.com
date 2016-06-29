<?php
function wpaie_import()
{
    global $message, $uploadedFilePath;
    include('templates/import/importView.php');
}
function mapFields($data, $operationCategory)
{
    global $uploadedFilePath;
    $mapFields = array();
    $postType  = "";
    switch (strtoupper($operationCategory)) {
        case "POST":
            $mapFields = getPostFields();
            $postType  = "POST";
            break;
        case "PAGE":
            $mapFields = getPageFields();
            $postType  = "PAGE";
            break;
        case "CATEGORY":
            $mapFields    = getCategoryFields();
            $taxonomyType = $_POST["taxonomyType"];
            break;
        case "COMMENT":
            $mapFields = getCommentFields();
            break;
        case "USER":
            $mapFields = getUserFields();
            break;
        case "TAXONOMY":
            $mapFields    = getCategoryFields();
            $taxonomyType = $_POST["customTaxonomy"];
            break;
        case "CUSTOMPOST":
            $mapFields = getPostFields();
            $postType  = $_POST["customPostType"];
            break;
        case "WPTABLE":
            $mapFields = getDBTableColumns();
            break;
        case "PLUGINS":
            $mapFields = getPluginFields();
            if ($_POST["thirdpartyplugins"] == "woocommerce_product")
                $postType = "product";
            break;
        default:
            break;
    }
    include('templates/import/importMapFields.php');
}
function getPostFields()
{
    $ACS         = new ACS();
    $option      = get_option('wpaieOptions');
    $postColumns = $option["postFields"];
    
    $customTaxonomies = $option["customTaxonomiesFields"];
    if (is_array($customTaxonomies)) {
        array_walk($customTaxonomies, 'addPrefix', 'CT');
        $postColumns = array_merge($postColumns, $customTaxonomies);
    }
    
    $metaFields = $option["postMetaFields"];
    if (is_array($metaFields)) {
        array_walk($metaFields, 'addPrefix', 'PM');
        $postColumns = array_merge($postColumns, $metaFields);
    }
    
    return $postColumns;
}

function getPageFields()
{
    $ACS         = new ACS();
    $option      = get_option('wpaieOptions');
    $postColumns = $option["postFields"];
    
    if(($key = array_search("post_tag", $postColumns)) !== false) {
        unset($postColumns[$key]);
    }
    
    if(($key = array_search("post_category", $postColumns)) !== false) {
        unset($postColumns[$key]);
    }
    
    $metaFields = $option["postMetaFields"];
    if (is_array($metaFields)) {
        array_walk($metaFields, 'addPrefix', 'PM');
        $postColumns = array_merge($postColumns, $metaFields);
    }
    return $postColumns;
}

function getPluginFields()
{
    $ACS = new ACS();
    
    $option = get_option('wpaieOptions');
    
    $postColumns = array(
        'post_title'        => "product_name",
        'post_content'      => "product_content",
        'post_excerpt'      => "product_short_desc",
        'post_date'         => "publish_date",
        'post_name'         => "product_slug",
        'featured_image'    => "featured_image",
        'post_parent'       => "product_parent",
        'post_status'       => "product_status"
    );
    
    if ($_POST["thirdpartyplugins"] == "woocommerce_product") {
        $customTaxonomies = get_object_taxonomies("product");
        if (is_array($customTaxonomies)) {
            array_walk($customTaxonomies, 'addPrefix', 'CT');
            $postColumns = array_merge($postColumns, $customTaxonomies);
        }
        
        $metaFields = $option["woocommerceProductMeta"];
        if (is_array($metaFields)) {
            array_walk($metaFields, 'addPrefix', 'PM');
            $postColumns = array_merge($postColumns, $metaFields);
        }
    }
    return $postColumns;
}
function addPrefix(&$item, $key, $prefix)
{
    $item = "$prefix: $item";
}

function getCategoryFields()
{
    $categories = array(
        "name",
        "slug",
        "description",
        "parent"
    );
    return $categories;
}

function getCommentFields()
{
    $comments = array(
        "comment_post_ID",
        "comment_author",
        "comment_author_email",
        "comment_author_url",
        "comment_author_IP",
        "comment_date",
        "comment_content",
        "comment_approved",
        "comment_type",
        "user_id",
        "comment_parent",                
        "comment_agent",        
        "comment_karma"
    );
    return $comments;
}

function getUserFields()
{
    $users = array(
        "user_login",
        "user_nicename",
        "user_email",
        "user_url",
        "user_registered",
        "user_activation_key",
        "user_status",
        "display_name",
        "role",
        "first_name",
        "last_name",
        "nickname",
        "jabber",
        "aim",
        "yim",
        "user_pass",
        "description",
    );
    return $users;
}

function getDBTableColumns()
{
    $ACS = new ACS();
    return $ACS->getDBTableColumns($_POST["wpTables"]);
}