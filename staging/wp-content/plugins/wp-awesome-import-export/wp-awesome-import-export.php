<?php
/* Plugin Name: WP Awesome Import & Export
 * Plugin URI: http://demo.digitaldreamstech.com/wp-awesome-import-export-plugin/
 * Description: This plugin allows to import and export of <strong>post, pages, custom posts, categories/tags, comments, users, custom tables, custom taxonomies</strong>. You can also import and export custom plugin data like <strong>woocommerce</strong> or import/export data of <strong>any table</strong> of wordpress database. You can import using csv,excel,xml files and export in <strong>csv,excel,xml,pdf</strong>.
 * Author: ddeveloper
 * Author URI: http://demo.digitaldreamstech.com/wp-awesome-import-export-plugin/
 * Text Domain: wpaie
 * Domain Path: /languages/
 * Version: 1.1.0
 */
error_reporting(0);
define('WPAIE_VERSION', '1.0');

if (!defined('WPAIE_PLUGIN_DIR'))
      define('WPAIE_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));

if (!defined('WPAIE_PLUGIN_URL'))
      define('WPAIE_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__)));

if (!defined('WPAIE_PLUGIN_BASENAME'))
      define('WPAIE_PLUGIN_BASENAME', untrailingslashit(plugin_basename(__FILE__)));

define('WPAIE_SQL_ALLOW',true);

require_once WPAIE_PLUGIN_DIR . '/classes/ACS.php';
require_once WPAIE_PLUGIN_DIR . '/settings.php';
require_once WPAIE_PLUGIN_DIR . '/importFiles.php';
require_once WPAIE_PLUGIN_DIR . '/exportFiles.php';
require_once WPAIE_PLUGIN_DIR . '/ajaxRequests.php';
require_once WPAIE_PLUGIN_DIR . '/classes/renderImport.php';
require_once WPAIE_PLUGIN_DIR . '/classes/renderExport.php';
require_once WPAIE_PLUGIN_DIR . '/wp-awesome-callbacks.php';
require_once WPAIE_PLUGIN_DIR . '/wp-awesome-actions.php';

/*
 * On Plugin Activation
 */
register_activation_hook(__FILE__, 'wpaieInitOptions');
register_activation_hook(__FILE__, 'wpaieLoadPluginTextdomain');

/*
 * Initialization/Default Options
 */
function wpaieInitOptions()
{
    $ACS = new ACS();
    
    $defaultOptions = array(
        'checkFileName' => true,
        'checkFileNameCharacters' => true,
        'rootElement' => 'root',
        'rowTagName' => '',
        'duplicateEntry' => "skip",
        'postFields' => array(
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
            
        ),
        "postMetaFields" => $ACS->getPostMeta(),
        "customTaxonomiesFields" => $ACS->getCustomTaxonomies(),
        "postStatus" => "draft",
        "postDate" => "currentdate",
        "dateval" => date('Y/m/d g:i:s'),
        "authorDetails" => "authorId",
        "sqlExport" => "yes",
        "termSeparator" => "|",
        "categorySeparator" => "|",
        "csvDelimiter" => ",",
        "woocommerceProductMeta" => array(
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
        )
    );
    add_option('wpaieOptions', $defaultOptions, '', 'no');
}
/*
 * Load Plugin Text Domain For Translation
 */

function wpaieLoadPluginTextdomain()
{
    load_plugin_textdomain('wpacs', false, WPAIE_PLUGIN_DIR . '/languages');
}