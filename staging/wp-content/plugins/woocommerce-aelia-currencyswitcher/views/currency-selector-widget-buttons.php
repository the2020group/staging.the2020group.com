<?php if(!defined('ABSPATH')) exit; // Exit if accessed directly

// $widget_args is passed when widget is initialised
echo get_value('before_widget', $widget_args);

// This wrapper is needed for widget JavaScript to work correctly
echo '<div class="widget_wc_aelia_currencyswitcher_widget">';

// Title is set in WC_Aelia_CurrencySwitcher_Widget::widget()
$currency_switcher_widget_title = get_value('title', $widget_args);
if(!empty($currency_switcher_widget_title)) {
	echo get_value('before_title', $widget_args);
	echo apply_filters('widget_title', __($currency_switcher_widget_title, $this->text_domain));
	echo get_value('after_title', $widget_args);
}

// If one or more Currencies are misconfigured, inform the Administrators of
// such issue
if((get_value('misconfigured_currencies', $this, false) === true) && current_user_can('manage_options')) {
	$error_message = WC_Aelia_CurrencySwitcher::instance()->get_error_message(AELIA_CS_ERR_MISCONFIGURED_CURRENCIES);
	echo '<div class="error">';
	echo '<h5 class="title">' . __('Error', $this->text_domain) . '</h5>';
	echo $error_message;
	echo '</div>';
}

echo '<!-- Currency Switcher v.' . WC_Aelia_CurrencySwitcher::VERSION . ' - Currency Selector Widget -->';
echo '<form method="post" class="currency_switch_form">';
foreach($widget_args['currency_options'] as $currency_code => $currency_name) {
	$button_css_class = 'currency_button ' . $currency_code;
  $currency_amend = substr($currency_code, 0, -1);
  $currency_short = strtolower($currency_amend);
	if($currency_code === $widget_args['selected_currency']) {
		$button_css_class .= ' active';
	}

	echo '<button type="submit" name="aelia_cs_currency" value="' . $currency_code . '" ' .
       'class="' . $button_css_class . '" style="background:url('.get_template_directory_uri().'/assets/flags/' . $currency_short . '.png) no-repeat left center ;">';
	echo $currency_name;
	echo '</button>';
}
echo '</form>';
echo '</div>';

echo get_value('after_widget', $widget_args);
