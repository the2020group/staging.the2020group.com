<?php

/* 
 * All the awesome actions here
 * 
 * 
 */

/*
 *  Admin Menu Action
 */
add_action('admin_menu', 'wpaieMenu');

/*
 *  Enqueuing Styles and Script
 */
add_action('admin_enqueue_scripts', 'addCSSJS');

/*
 *  Ajax Actions
 */
add_action('wp_ajax_wpaie_ajax_action', 'wpaie_ajax_action_callback');

/*
 *  plugin setting meu
 */
add_filter("plugin_action_links_".WPAIE_PLUGIN_BASENAME, 'wpaie_plugin_settings_link' );