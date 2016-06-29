<?php

/*
 *  All the Awesome Callbacks Here
 *  
 */
/*
 * Create Menu Callback
 */

function wpaieMenu() {
    add_menu_page('WP Awesome Import & Export', 'WP Awesome Import & Export', 'manage_options', 'wpaie-main', 'wpaie_import');
    add_submenu_page('wpaie-main', 'WP Awesome Import', 'WP Import', 'manage_options', 'wpaie-main', 'wpaie_import');
    add_submenu_page('wpaie-main', 'WP Awesome Export', 'WP Export', 'manage_options', 'wpaie_export', 'wpaie_export');
    add_submenu_page('wpaie-main', 'WP Awesome Import/Export Settings', 'Settings', 'manage_options', 'wpaie-setting', 'wpaie_setting');
}

/*
 * Enqueing Css and Js Callback
 */

function addCSSJS() {
    wp_enqueue_style('wpaie-style', WPAIE_PLUGIN_URL . '/css/style.css');
    wp_enqueue_style('jquery-ul', WPAIE_PLUGIN_URL . '/css/jquery-ui.css');
    wp_enqueue_style('tabs', WPAIE_PLUGIN_URL . '/css/component.css');
    wp_enqueue_style('tooltip', WPAIE_PLUGIN_URL . '/css/jquery.qtip.min.css');
    wp_enqueue_style('select', WPAIE_PLUGIN_URL . '/css/multiple-select.css');

    wp_enqueue_script('jQuery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('wpaie-tabs', plugins_url('/js/jquery-ui.js', __FILE__));
    wp_enqueue_script('wpaie-script', plugins_url('/js/script.js', __FILE__));
    wp_enqueue_script('wpaie-ajax-js', plugins_url('/js/ajax.js', __FILE__));
    wp_enqueue_script('wpaie-validation-js', plugins_url('/js/validations.js', __FILE__));
    wp_enqueue_script('wpaie-tooltip-js', plugins_url('/js/jquery.qtip.min.js', __FILE__));
    wp_enqueue_script('select-js', plugins_url('/js/jquery.multiple.select.js', __FILE__));
}

/*
 * Upload File Form
 */

function getUploadFileControl($operationCategory) {
    if (isset($_POST["submitMapping"])) {
        $WPAIEImport = new WPAIEImport();
        $output = $WPAIEImport->renderImport();
    }
    include(WPAIE_PLUGIN_DIR . '/templates/import/fileUploadView.php');
}

/*
 *  Renders the uploaded File
 */

function renderUploadedFile() {
    global $message, $uploadedFilePath;
    $data = array();

    if (isset($_POST["operationCategory"])) {

        $ACS = new ACS();
        $fileuploaded = uploadFile();
        if ($_POST['uploadFileUrl']) {
            $fileName = $uploadedFilePath;
        } else {
            $fileName = $fileuploaded["file"];
            $uploadedFilePath = $fileName;
        }

        $fileExtension = $ACS->getFileExtension($fileName);

        if ($fileExtension == "csv")
            $data = $ACS->csvToArray($fileName);
        else if ($fileExtension == "xls" || $fileExtension == "xlsx")
            $data = $ACS->excelToArray($fileName);
        else if ($fileExtension == "xml") {
            $data = $ACS->xmlToArray($fileName);
            $data = $ACS->formatInputData("xml", $data);
        }
    }
    return $data;
}

function uploadFile() {
    global $uploadedFilePath;
    if (!function_exists('wp_handle_upload')) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    if (!empty($_FILES['uploadFile']['name'])) {
        $upload_overrides = array('test_form' => false, 'mimes' => array('csv' => 'text/csv', 'xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'xml' => 'application/xml'));

        $uploadedfile = $_FILES['uploadFile'];
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

        
    } else if ($_POST['uploadFileUrl']) {
       
        if (is_valid_url($_POST['uploadFileUrl'])) {
            $url = $_POST['uploadFileUrl'];
            $uploads = wp_upload_dir();
            $filename = substr($url, (strrpos($url, '/')) + 1);
            $uploads = wp_upload_dir(current_time('mysql'));
            if (!is_dir($uploads['basedir'])) {
                return false;
            }

            $uniqueFileName = wp_unique_filename($uploads['path'], $filename);
            $uploadedFilePath = $uploads['path'] . "/$uniqueFileName";
            $uploaded = copy($url, $uploadedFilePath);
        }
    }
    
    if ($movefile && !isset($movefile['error'])) {
        return $movefile;
    } else {
        echo $movefile['error'];
    }
}

function is_valid_url($url) {
    // alternative way to check for a valid url
    if (filter_var($url, FILTER_VALIDATE_URL) === FALSE)
        return false;
    else
        return true;
}

function wpaie_plugin_settings_link($links) {
    $settings_link = '<a href="admin.php?page=wpaie-setting">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
