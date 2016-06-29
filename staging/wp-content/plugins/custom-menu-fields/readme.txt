=== Custom Menu Fields ===
Contributors: diddledan
Tags: menu, field, fields, api
Requires at least: 3.5
Tested up to: 3.7
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides an API to add custom fields to the menu editor.

== Description ==

This plugin provides an API which allows the developer of a site to add custom fields on the default menu editor.

= Example Initialisation =

`<?php
add_action('init', 'menu_excerpt__add_menu_field');
function menu_excerpt__add_menu_field() {
	if (!is_callable('bh_add_custom_menu_fields'))
		return;

	bh_add_custom_menu_fields(array(
		'excerpt' => array(
			'description' => 'Excerpt',
			'type' => 'textarea',
			)));
}
?>`

= Accessing the fields =

The easiest way to access the field(s) you've added is to use something along the lines of:

`$menu = 'menuName';
$posts = wp_get_nav_menu_items($menu);
foreach ($posts as $p) {
    $myitem = get_post_meta($p->ID, '_menu_item_youritem', true);
    // do with $myitem what you like - it should be a string,
    // so the simplest thing is to "echo" it
}`

you can use menu locations to get the menu name if you prefer - replace the first line above with:

`$locations = get_nav_menu_locations();
$menu = $locations['locationName'];`

menu locations are useful if you like to swap your menus about.

Unfortunately these examples don't allow the use of wordpress' inbuilt menu walkers. To use those you will need to create a custom walker_nav_menu class and access the custom fields with something along the lines of (very stripped down example will need fleshing out for full walker functionality - there are tutorials on the net for custom nav walkers):

`class mywalker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth, $args) {
        echo $item->custom_field
    }
}`

The important bit here is that the field is placed on the second variable which behaves like an object. the custom_field part is the name you gave your field with dashes replaced with underscores (to allow the name to be used in an accessor).

= NOTE =
This plugin does nothing by itself. It provides an API only.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the folder from the zip to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Once activated the API is available in the ‘init’ and later phases of the WordPress lifecycle.

== Changelog ==

= 0.2 =
version bump to attempt to rectify auto-update mechanism

= 0.1 =
first release
