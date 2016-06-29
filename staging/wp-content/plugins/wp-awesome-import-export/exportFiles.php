<?php
function wpaie_export()
{
    global $message;
    include('templates/export/exportView.php');
}
function showOutputResult($operationCategory)
{
    include('templates/export/showResult.php');
}
function getSQLForm($operationType)
{
    include('templates/export/sqlForm.php');
}
function getWPTableForm($operationType)
{
    $ACS = new ACS();
    
    $tables = $ACS->getDBTables();
    
    include('templates/export/wpTableForm.php');
    
}
function getUserForm($operationType)
{
    $ACS = new ACS();
    
    $userFields = array(
        "ID",
        "user_login",
        "user_nicename",
        "user_email",
        "user_url",
        "user_registered",
        "user_activation_key",
        "user_status",
        "display_name",
        
    );
    
    $metaFields = $ACS->getUserMeta();
    
    include('templates/export/userForm.php');
}
function getCommentForm($operationType)
{
    $ACS           = new ACS();
    $commentFields = array(
        "comment_ID",
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
    
    $commentStatus = array(
        "approve",
        "hold",
        "spam",
        "trash",
        "post-trashed"
    );
    $postStatus    = array(
        'publish',
        'future',
        'draft',
        'pending',
        'private',
        'inherit'
    );
    
    include('templates/export/commentForm.php');
    
}
function getTaxonomyForm($taxonomyType)
{
    $ACS = new ACS();
    
    $customFields = $ACS->getCustomTaxonomies();
    
    include('templates/export/taxonomyForm.php');
}

function getExportPostForm($postType)
{
    global $postStatus;
    
    $ACS = new ACS();
    
    $postStatus = array(
        'publish',
        'future',
        'draft',
        'pending',
        'private',
        'inherit'
    );
    
    $option = get_option('wpaieOptions');
    
    $postColumns = array(
        'ID',
        'post_title',
        'post_content',
        'post_excerpt',
        'post_date',
        'post_name',
        'post_author',
        'post_parent',
        'post_status'
    );
    
    $customFields = $ACS->getCustomTaxonomies();
    
    $metaFields = $ACS->getPostMeta();
    
    $customPostTypes = $ACS->getCustomPostType();
    
    include('templates/export/postForm.php');
    
}

function getExportPluginForm($pluginName)
{
    global $pluginName;
    
    $ACS = new ACS();
    
    $postStatus = array(
        'publish',
        'future',
        'draft',
        'pending',
        'private',
        'inherit'
    );
    
    $option = get_option('wpaieOptions');
    
    $postColumns = array(
        'post_title' => "product_name",
        'post_content' => "product_content",
        'post_excerpt' => "product_short_desc",
        'post_date' => "publish_date",
        'post_name' => "product_slug",
        'post_parent' => "product_parent",
        'post_status' => "product_status"
    );
    
    $customFields = get_object_taxonomies("product");
    
    $metaFields = $option["woocommerceProductMeta"];
    
    include('templates/export/pluginForm.php');
    
}