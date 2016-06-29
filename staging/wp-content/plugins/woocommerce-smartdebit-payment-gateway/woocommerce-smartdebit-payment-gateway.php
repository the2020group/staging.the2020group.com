<?php
/*
Plugin Name: WooCommerce SmartDebit Gateway
Plugin URI: http://woothemes.com/woocommerce
Description: Extends WooCommerce with an SmartDebit gateway.
Version: 1.0
Author: Stephan Gerlach
Author URI: http://www.first10.co.uk/

    Copyright: © 2009-2011 WooThemes.
    License: GNU General Public License v3.0
    License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


class WC_SmartDebit {

        function __construct() {
            add_action('woocommerce_product_write_panel_tabs', array($this,'custom_tab_options_tab'));
            add_action('woocommerce_product_write_panels',     array($this,'custom_tab_options'));
            add_action('woocommerce_process_product_meta',     array($this,'save_custom_tab_options') , 10, 2);
        }

        public function custom_tab_options_tab() { ?>
            <li class="linked_product_options linked_product_tab"><a href="#smartdebit_tab_data">SmartDebit</a></li><?php
        }

        public function custom_tab_options() {
            global $post;
            $instalments = get_post_meta($post->ID, 'smartdebit_instalments', true);
            ?>

            <div id="smartdebit_tab_data" class="panel woocommerce_options_panel">
                <div class="options_group">
                    <p class="form-field">
                        <?php woocommerce_wp_text_input( array( 'id' => 'smartdebit_instalments', 'label' => 'Number of instalments', 'description' => 'Number of monthly instalments if this product purchased via SmartDebit', 'value'=>$instalments) ); ?>
                    </p>
                </div>
            </div>
            <?php
        }

        // Process meta. Processes the custom tab options when a post is saved
        public function save_custom_tab_options( $post_id ) {
            $instalments = $_POST['smartdebit_instalments'];
            if (!empty($instalments)) {
                update_post_meta( $post_id, 'smartdebit_instalments', (int) $instalments );
            }
        }
    }

    $sm = new WC_SmartDebit();



add_action('plugins_loaded', 'woocommerce_smartdebit_init', 0);

function woocommerce_smartdebit_init() {

    if ( !class_exists( 'WC_Payment_Gateway' ) ) return;



    /**
     * Gateway class
     */
    class WC_SmartDebit_Gateway extends WC_Payment_Gateway {

        public function __construct() {

            add_action('woocommerce_product_tabs', array($this, 'product_tab') );

            // The global ID for this Payment method
            $this->id = 'smartdebit';

            // The title to be used for the vertical tabs that can be ordered top to bottom
            $this->title = 'SmartDebit';
            $this->supports = array('products',
                                    'subscriptions',
                                    'gateway_scheduled_payments',
                                    'add_payment_method',
                                    'subscription_cancellation',
                                    'subscription_suspension',
                                    'subscription_amount_changes',
                                    'subscription_date_changes'
                                   );

            // The description for this Payment Gateway, shown on the actual Payment options page on the backend
            $this->method_description = 'SmartDebit';
            $this->default = 'With SmartDebit you can pay via DirectDebit';
            $this->description = 'Pay via DirectDebit';
            $this->has_fields = true;
            $this->order_button_text = "Pay via Direct Debit";

            $this->icon = null;

            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title            = $this->get_option( 'title' );
            $this->description      = $this->get_option( 'description' );

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

            $this->form_fields = array(
                'enabled' => array(
                    'title' => __( 'Enable/Disable', 'woocommerce' ),
                    'type' => 'checkbox',
                    'label' => __( 'Enable SmartDebit Payment', 'woocommerce' ),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __( 'Title', 'woocommerce' ),
                    'type' => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                    'default' => __( 'Cheque Payment', 'woocommerce' ),
                    'desc_tip'      => false,
                ),
                'description' => array(
                    'title'       => __( 'Description', 'woocommerce' ),
                    'type'        => 'text',
                    'desc_tip'    => true,
                    'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
                    'default'     => __( 'Pay via SmartDebit; you can pay by Direct Debit.', 'woocommerce' )
                ),
                'api_user' => array(
                    'title' => __( 'API Username', 'woocommerce' ),
                    'type' => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                    'desc_tip'      => false,
                ),
                'api_password' => array(
                    'title' => __( 'API Password', 'woocommerce' ),
                    'type' => 'password',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                    'desc_tip'      => false,
                ),
                'user_agent' => array(
                    'title' => __( 'API User Agent', 'woocommerce' ),
                    'type' => 'text',
                    'description' => __( 'Tell SmartDebit who make the API Call.', 'woocommerce' ),
                    'desc_tip'      => false,
                ),
                'service_user' => array(
                    'title' => __( 'Service User ID', 'woocommerce' ),
                    'type' => 'text',
                    'description' => __( 'ID of the Service User.', 'woocommerce' ),
                    'desc_tip'      => false,
                ),
                'api_pslid' => array(
                    'title' => __( 'PSLID', 'woocommerce' ),
                    'type' => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                    'desc_tip'      => false,
                )
            );

        }

        public function supports($arg) {
            if (in_array($arg,$this->supports)) {
                //error_log('supports is true');
                return true;
            }
            else {
                //error_log('supports is false');
                return false;
            }
        }

        public function is_available() {
            //error_log('is_available');
            return true;
        }

        //public function get_description() {
            //return $this->description;
        //}

        public function updatePaymentPlan() {

            // FOR THE CRON

            global $wpdb;

            $currentddusers = $wpdb->get_results($wpdb->prepare('SELECT * FROM '. $wpdb->prefix .'usermeta WHERE meta_key = %s','smartdebit_payment_plan'));

            //error_log(print_r($currentddusers,1));

            foreach($currentddusers as $dduser) {
                $curruser = $dduser->user_id;
                $ischecked = get_user_meta($curruser, 'smartdebit_payment_check', true);
                //error_log('message received is: '.print_r($ischecked,1));

                if($ischecked == 'false') {
                    error_log('false');
                    $currentplan = get_user_meta($curruser, 'smartdebit_payment_plan', true);
                    //error_log(print_r($currentplan,1));
                    $updatedplan = array_shift($currentplan);
                    error_log(print_r($currentplan,1));
                    $args = array(
                            //'variable_ddi[reference_number]'     => rand(0,999).$this->getInfusionSoftReference($user_id),
                            'variable_ddi[reference_number]'     => $this->getInfusionSoftReference($curruser)/*.'512'*/,
                            'variable_ddi[default_amount]'       => $currentplan[1]*100,
                            'variable_ddi[first_amount]'         => $currentplan[0]*100
                    );

                    $api_result = $this->makeCall('/api/ddi/variable/'.$this->getInfusionSoftReference($user_id)./*'512'.*/'/update', $args, $order_id);
                    error_log(print_r($api_result,1));
                    if ($api_result !== false) {
                        update_user_meta($curruser, 'smartdebit_payment_check', 'true');
                        update_user_meta($curruser, 'smartdebit_payment_plan', $currentplan);
                    }

                }
                else {
                    error_log('true');
                }
            }
        }



        public function prepareUsersForUpdatePaymentPlan() {
            // FOR THE CRON
            // set everyone to false to allow updates to run
        }

        private function handleErrors($output) {

            // For now let's just read through the xml and put the correct error on the screen for user interpretation

            $errors = simplexml_load_string($output);
            error_log ('Errors : '.$errors);

            $allowed_errors = array('Database is empty.','Other error');

            if ($errors) {
                $error_string = $errors->error[0];

                error_log($error_string);

                if (!in_array($error_string, $allowed_errors)) {
                    wc_add_notice('DD Error : '.$error_string,'error');
                }
                return true;
            } else {
                return false;
            }

        }

        public function makePaymentPlan() {
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;

            global $woocommerce;

            $lines = array();

            foreach ($woocommerce->cart->cart_contents as $item) {
                $instalments = get_post_meta($item['product_id'],'smartdebit_instalments',true);
                if ($instalments == '') {
                    $instalments = 1;
                }
                $line_total  = $item['line_total']+$item['line_tax'];

                if ($instalments > 1) {
                    $monthly = ($line_total/$instalments);

                    $normal = floor($monthly);
                    $tot = $instalments * $normal;
                    $first = $line_total - $tot + $normal;
                }
                else {
                    $normal = 0;
                    $first = $line_total;
                }

                $lines[] = array ('i'=>$instalments,'t'=>$line_total,'m'=>$normal,'f'=>$first);
            }

            $existing_plan = array();
            $existing_plan = get_user_meta($user_id, 'smartdebit_payment_plan', true);

            $payment_plan = array();

            foreach ($lines as $line) {
                for($i=0; $i<$line['i'];$i++) {
                    if ($i == 0 ) {
                        $payment_plan[$i] += $line['f'];
                    }
                    else {
                        $payment_plan[$i] += $line['m'];
                    }
                }
            }

            $add = function($a, $b) { return $a + $b; };

            // die($existing_plan);

            if($existing_plan != '') {
                $payment_plan = array_map($add, $existing_plan, $payment_plan);
            }

            $day = date('d');

            if ($payment_plan && $day > 5 && $day < 18) {

                $new_payment_plan = $payment_plan;
                if (count($payment_plan) > 1) {
                    $new_installments = count($payment_plan)-1;
                    $new_payment_plan[0] = $payment_plan[0]+$payment_plan[1];
                    unset($new_payment_plan[$new_installments]);
                }

                $payment_plan = $new_payment_plan;

            }

            return array($existing_plan,$payment_plan);
        }

        public function payment_fields() {
            $plugin_dir = plugin_dir_url(__FILE__);

            // Description of payment method from settings
            if ($this->description) { ?>
                <p><?php
                echo $this->description; ?>
                </p><?php
            } ?>

            <?php
                global $payment_plan;
                $payment_plan = $this->makePaymentPlan();
                $current_user = wp_get_current_user();
                $user_id = $current_user->ID;

                // error_log('payment plan : '.$user_id);
                // error_log(print_r($payment_plan,1));

                if ($user_id == 0) {
                    //set_transient('unique',);
                    global $woocommerce;
                    error_log('IP HASH '.md5($_SERVER['REMOTE_ADDR']));
                    set_transient(md5($_SERVER['REMOTE_ADDR']),$payment_plan[1],12*60*60);
                } else {
                    update_user_meta($user_id, 'temp_smartdebit_payment_plan', $payment_plan[1]);
                }

                echo '<ol id="dd-table">';

                if($payment_plan[0]) {
                    echo '<li><span class="">Your existing monthly payment</span><span class="">Your updated monthly payment</span></li>';
                    foreach ($payment_plan[1] as $k => $payment) {
                        $payment = number_format ( (float)$payment , 2 );

                        if($payment_plan[0][$k] != '') {
                            echo '<li><span class="">£'.$payment_plan[0][$k].'</span><span class="">£'.$payment.'</span></li>';
                        }
                        else {
                            echo '<li><span class="">£0</span><span class="">£'.$payment.'</span></li>';
                        }

                    }
                }

                else {
                    echo '<li><span class="">Your monthly payments</span></li>';
                    foreach ($payment_plan[1] as $k => $payment) {
                      $payment = number_format ( (float)$payment , 2 );
                       echo '<li><span class="">£'.$payment.'</span></li>';
                    }
                }


                echo '</ol>';



                //$paymentdates = $this->makePaymentPlanDates();
                //error_log(print_r($paymentdates,1));

                $current_month = date('m');
                $current_year = date('Y');
                $day = date('d');
                if ($day > 5) {
                    if ($current_month == 12) {
                        $current_month =1;
                        $current_year = 1 + $current_year;
                    } else {
                        $current_month = 1+$current_month;
                    }
                }
                $start_date = '17 '.$current_month.' '.$current_year;


            ?>

            <p class="form-row form-row-wide address-field validate-required">
                <label for="account_holder">Name(s) of Account Holder(s) <abbr class="required" title="required">*</abbr></label>
                <input id="account_holder" name="account_holder" type="text" maxlength="16" width="20" value="" autocomplete="off" />
            </p>

            <p class="form-row form-row-wide address-field validate-required">
                <label for="account_number">Bank / Building Society Account Number <abbr class="required" title="required">*</abbr></label>
                <input id="account_number" name="account_number" type="text" maxlength="8" width="20" value="" autocomplete="off"  />
            </p>
            <p style="display:none">
                <label for="account_number">Bank / Building Society Address </label>
                <input id="bank_address" name="bank_address" type="text"  />
            </p>
            <p class="form-row form-row-wide address-field validate-required">
                <label for="sort_code">Branch Sort Code <abbr class="required" title="required">*</abbr></label>
                <input id="sort_code" name="sort_code" type="text" maxlength="16" width="20" value="" autocomplete="off"  />
            </p>

            <p>
                <label for="fequency">Frequency</label>
                <input id="frequency" name="frequency" type="text" width="20" value="Monthly" readonly autocomplete="off"  />
            </p>

            <p>
                <label for="first_collection">Date of first collection <abbr class="required" title="required">*</abbr></label>
                <input id="first_collection" name="first_collection" type="text" width="20" value="<?php echo $start_date; ?>" readonly autocomplete="off"  />
            </p>
            <p>
                <label for="account-type-holder">
                    <input id="account-type-holder" name="account-type" type="radio" value="1" checked="checked" />
                    I am the account holder
                </label>
                <label for="account-type-multiple">
                    <input id="account-type-multiple" name="account-type" type="radio" value="2" />
                    Our account requires more than one signature / I am not the account holder
                </label>
            </p>
            <p class="account-type-dd" style="display:block;">If you are not the account holder or your account requires more than one signature, <a href="/wp-content/uploads/2015/06/DDI-Mandate.doc">a paper Direct Debit Instruction</a> will be required to be completed and posted back to us.</p>
            <style type="text/css">
                @media print {
                    html,body {margin:0 !important;padding:0 !important;}
                    body * { visibility: hidden; }
                    #dd-conf { visibility: visible; position: fixed; top: 1px; right:0; bottom: 0; left: 1px; z-index: 99999; font-size: 9pt; display:block;}
                    #dd-conf * { visibility: visible; }
                    #dd-conf .dd-footer {visibility: hidden;}
                    /*#fancybox-content { visibility: visible; position: static; top: 1px; right:0; bottom: 0; left: 1px; z-index: 99999; }*/
                }
            </style>
            <script type="text/javascript">
            // put all the values in the new form
            (function($) {
                $('form.checkout').on( 'click', 'input[name=payment_method]', function(e) {
                  if($(this).attr('id') == 'payment_method_smartdebit') {
                    $('#verify_direct_debit').show();
                    $('#place_order').hide();
                  } else {
                    $('#verify_direct_debit').hide();
                    $('#place_order').show();
                  }
                });

                $('#verify_direct_debit').click(function(e) {
                  if($('#account-type-holder').is(':checked')) {
                    $.fancybox.showLoading();

                    $.ajax({
                        type: "POST",
                        url: ajaxurl+'?action=validate_ddi',
                        data: $('form.checkout').serialize(),
                        success: function(output) {
                          var account_holder, account_number, sort_code, start_date, bank_address, bank

                          bank = $(output).find('success').last();

                          $.fancybox.hideLoading();
                          if(bank.attr('bank_name') !== undefined) {
                            bank_parts = $.grep([
                              bank.attr('bank_name'),
                              bank.attr('branch'),
                              bank.attr('address1'),
                              bank.attr('address2'),
                              bank.attr('address3'),
                              bank.attr('address4'),
                              bank.attr('town'),
                              bank.attr('county'),
                              bank.attr('postcode')
                              ], Boolean
                            );

                            account_holder  = $('#account_holder').val();
                            account_number  = $('#account_number').val();
                            sort_code       = $('#sort_code').val();
                            start_date      = $('#first_collection').val();
                            bank_address    = bank_parts.join(', ');

                            $('#bank-account-holder').val(account_holder);
                            $('#bank-account-number').val(account_number);
                            $('#bank-sort-code').val(sort_code);
                            $('#bank-address').val(bank_address);

                            //$('#start-date').val('Date: ' + start_date);

                            $.fancybox({
                              height: 'auto',
                              maxWidth: '800',
                              //scrolling: 'hidden',
                              helpers:{
                                overlay : {
                                  //locked: false,
                                  css : {
                                    'background' : 'rgba(47, 35, 42, 0.9)'
                                  }
                                }
                              },
                              href: '#dd-conf'
                            });
                          } else {
                            // Remove old errors
              							$( '.woocommerce-error, .woocommerce-message' ).remove();

                            var $form = $('form.checkout');

                            if ( output.indexOf( '<!--WC_START-->' ) >= 0 )
                              output = output.split( '<!--WC_START-->' )[1]; // Strip off before after WC_START

                            if ( output.indexOf( '<!--WC_END-->' ) >= 0 )
                              output = output.split( '<!--WC_END-->' )[0]; // Strip off anything after WC_END

                            $form.prepend( $.parseJSON(output).messages );

                            // Lose focus for all fieldss
                            $form.find( '.input-text, select' ).blur();

                            // Scroll to top
                            $( 'html, body' ).animate( {
                              scrollTop: ( $( 'form.checkout' ).offset().top - 100 )
                            }, 1000 );

                            // Fire updated_checkout e
                            $( 'body' ).trigger( 'updated_checkout' );
                          }
                        },
                        error: function(output) {
                          $.fancybox.hideLoading();
                        }
                    });
                  } else {
                    $('form.checkout').submit();
                  }
                })

                $('#confirm').click( function(e) {
                    e.preventDefault();
                    $.fancybox.close();
                    $.fancybox({
                      height: 'auto',
                      maxWidth: '800',
                      //scrolling: 'hidden',
                      helpers:{
                        overlay : {
                          //locked: false,
                          css : {
                            'background' : 'rgba(47, 35, 42, 0.9)'
                          }
                        }
                      },
                      href: '#dd-submitting'/*,
                      afterLoad : function() {
                        setTimeout(function(){
                            $.fancybox.close()
                        }, 5000);
                      }*/
                    });
                    $('form.checkout').submit();
                });

                $('#amend').click( function(e) {
                    e.preventDefault();
                    $.fancybox.close();
                });

                $('#cancel').click( function(e) {
                    e.preventDefault();
                    $.fancybox.close();

                    // switch back to first payment option
                    $('.payment_methods').find('input[name=payment_method]:first').trigger('click');
                    // we need this to force the form to actually show the PayPal details
                    $( 'body' ).trigger( 'update_checkout' );

                    // clear DD form
                    $('#account_holder').val('');
                    $('#account_number').val('');
                    $('#sort_code').val('');
                    $('[name=account-type]:first').prop('checked', true);
                });

                $('#print').click( function(e) {
                    e.preventDefault();
                    window.print();
                });

            })(jQuery);
            </script>

            <div id="dd-conf" class="dd-popup" style="display:none;">

                <form>
                    <div class="dd-header row">
                        <div class="small-12 medium-6 columns">
                            <img src="/wp-content/themes/2020%20Group/library/images/general/2020-innovation.png" alt="" class="logo">
                        </div>
                        <div class="small-12 medium-6 columns text-right">
                            <img src="/wp-content/themes/2020 Group/library/images/general/dd.png" alt="direct debit logo" />
                        </div>

                    </div>
                    <div class="dd-left">
                        <p>
                            2020 Innovation Training Limited<br/>
                            6110 Knights Court<br/>
                            Solihull Parkway<br/>
                            Birmingham Business Park<br/>
                            Birmingham<br/>
                            West Midlands<br/>
                            B37 7WY
                        </p>

                        <p>
                            <label for="bank-account-holder">
                                Name of Account Holder
                                <input id="bank-account-holder" name="bank-account-holder" type="text" value="New Payer" readonly />
                            </label>
                        </p>
                        <p>
                            <label for="bank-account-number">
                                Bank / Building Society Account Number
                                <input id="bank-account-number" name="bank-account-number" type="text" value="1 2 3 4 5 6 7 8" readonly />
                            </label>
                        </p>
                        <p>
                            <label for="bank-sort-code">
                                Branch Sort Code
                                <input id="bank-sort-code" name="bank-sort-code" type="text" value="00 11 22" readonly />
                            </label>
                        </p>
                        <p>
                            <label for="service-user-number">
                                Name and full postal address of your Bank or Building Society
                                <textarea name="bank-address" id="bank-address" readonly ></textarea>
                            </label>
                        </p>

                    </div>

                    <div class="dd-right">
                        <p>Instruction to your Bank or Building Society to pay by Direct Debit</p>
                        <p>
                            <label for="service-user-number">
                                Service User Number
                                <input id="service-user-number" name="service-user-number" type="text" value="<?= $this->get_option('service_user') ?>" readonly />
                            </label>
                        </p>
                        <p>
                            <label for="reference">
                                Reference
                                <input id="reference" name="reference" type="text" value="<?= $this->getInfusionSoftReference($user_id) ?>" readonly />
                            </label>
                        </p>

                        <p>Instruction to your Bank or Building Society</p>

                        <p>Please pay PSL Re 2020 Innovation Training Ltd Direct Debits from the account detailed in this Instruction subject to the safeguards assured by the Direct Debit Guarantee. I understand that this instruction may remain with PSL Re 2020 Innovation Training Ltd and, if so, details will be passed electronically to my Bank/Building Society</p>

                        <p><textarea width="200" cols="20" name="start-date" id="start-date" readonly>Date: <?php echo date('d m Y'); ?></textarea></p>

                    </div>
                    <div class="dd-footer">
                        <p>
                            Bank and Building Societies may not accept Direct Debit instructions for some types of account.
                        </p>

                        <button id="confirm" class="btn orange" name="confirm">Pay via Direct Debit</button>
                        <button id="amend" class="btn orange" name="amend">Amend</button>
                        <button id="cancel" class="btn orange" name="cancel">Cancel</button>
                        <button id="print" class="btn orange" name= "print">Print</button>

                    </div>


                </form>



            </div>

            <div id="dd-submitting" class="dd-popup" style="display:none;">
                <p>We are currently processing your order and confirmation will be issued shortly.</p>
            </div>

            <?php
        }

        public function process_payment($order_id) {

            //error_log('process_payment');
            global $woocommerce;

            $order = new WC_Order($order_id);

            $current_user = wp_get_current_user();

            $user_id = $current_user->ID;

            $is_contact_id = $this->getInfusionSoftReference($user_id);

            list($account_holder, $account_number, $sort_code) = $this->validatePostedSmartDebitFields();

            $payment_plan = $this->makePaymentPlan();

            if (wc_notice_count('error') === 0) {
                $args = array(
                    'variable_ddi[reference_number]'     => $this->getInfusionSoftReference($user_id)/*.'512'*/,
                    'variable_ddi[sort_code]'            => $sort_code,
                    'variable_ddi[account_number]'       => $account_number,
                    'variable_ddi[account_name]'         => $account_holder,
                    'variable_ddi[first_name]'           => $_POST['billing_first_name'],
                    'variable_ddi[last_name]'            => $_POST['billing_last_name'],
                    'variable_ddi[address_1]'            => $_POST['billing_address_1'],
                    'variable_ddi[address_2]'            => $_POST['billing_address_2'],
                    'variable_ddi[town]'                 => $_POST['billing_city'],
                    'variable_ddi[county]'               => $_POST['billing_state'],
                    'variable_ddi[postcode]'             => $_POST['billing_postcode'],
                    'variable_ddi[country]'              => $_POST['billing_country'],
                    'variable_ddi[service_user][pslid]'  => $this->get_option('api_pslid'),
                    'variable_ddi[frequency_type]'       => 'M',
                    'variable_ddi[company_name]'         => $_POST['billing_company'],
                    'variable_ddi[email_address]'        => $_POST['billing_email'],
                    'variable_ddi[default_amount]'       => $payment_plan[1][1]*100,
                    'variable_ddi[first_amount]'         => $payment_plan[1][0]*100
                );

                if($payment_plan[0] != '') {
                    $api_result = $this->makeCall('/api/ddi/variable/'.$this->getInfusionSoftReference($user_id)./*'512'.*/'/update', $args, $order_id);
                } else {
                    $this->makeCall('/api/ddi/variable/validate', $args);
                    $api_result = $this->makeCall('/api/ddi/variable/create', $args, $order_id);
                }

                if ($api_result !== false && !$notvalidated) {
                    $xml = simplexml_load_string($api_result);


                    $note = "<br />";
                    foreach ($xml as $k =>$ddi) {

                        $note .= $k.': '.$ddi."<br />";

                        if ($k === 'reference_number') {
                            update_post_meta($order_id,'smartdebit_ref',(string)$ddi);
                        }
                        else if ($k === 'start_date') {
                            update_post_meta($order_id,'smartdebit_start',(string)$ddi);
                        }
                        else if ($k === 'first_amount') {
                            error_log('trying to add amount');
                            update_post_meta($order_id,'smartdebit_first_amount',(int)$ddi);
                        }
                    }

                    //check if we had to use the transient storage for new user
                    $trans = get_transient(md5(serialize($woocommerce->cart)));
                    $trans = get_transient(md5($_SERVER['REMOTE_ADDR']));

                    if($trans) {
                        $temp_smartdebit_payment_plan = get_transient(md5($_SERVER['REMOTE_ADDR']));
                        delete_transient(md5($_SERVER['REMOTE_ADDR']));
                    } else {
                        $temp_smartdebit_payment_plan = get_user_meta($user_id, 'temp_smartdebit_payment_plan', true);
                        delete_user_meta($user_id, 'temp_smartdebit_payment_plan');
                    }
                    update_user_meta($user_id, 'smartdebit_payment_plan', $temp_smartdebit_payment_plan);
                    update_user_meta($user_id, 'smartdebit_payment_check', 'false');

                    if(isset($_POST['account-type'])&&($_POST['account-type'])=='1') {
                        // normal DD order
                        $order->add_order_note( __( 'SmartDebit payment completed'.$note, 'woothemes' ) );
                    } else {
                        // this client has suggested that they are not the primary account holder
                        // alter the note accordingly
                        $order->add_order_note( __( 'The client notified that they are not the main account holder and downloaded the paper work. However the SmartDebit payment completed'.$note, 'woothemes' ) );
                    }

                    // ************************************************
                    // ************************************************

                    // Add a note on the account with the new payment plan details

                    $plan_html =  '<ol id="dd-table">';

                    if($payment_plan[0]) {
                        $plan_html .= '<li><span class="">Your existing monthly payment</span><span class="">Your updated monthly payment</span></li>';
                        foreach ($payment_plan[1] as $k => $payment) {
                            $payment = number_format ( (float)$payment , 2 );

                            if($payment_plan[0][$k] != '') {
                                $plan_html .= '<li><span class="">£'.$payment_plan[0][$k].'</span><span class="">£'.$payment.'</span></li>';
                            }
                            else {
                                $plan_html .= '<li><span class="">£0</span><span class="">£'.$payment.'</span></li>';
                            }

                        }
                    }

                    else {
                        $plan_html .= '<li><span class="">Your monthly payments</span></li>';
                        foreach ($payment_plan[1] as $k => $payment) {
                          $payment = number_format ( (float)$payment , 2 );
                           $plan_html .= '<li><span class="">£'.$payment.'</span></li>';
                        }
                    }


                    $plan_html .= '</ol>';




                // ************************************************
                // ************************************************



                    $order->add_order_note( __( 'SmartDebit payment plan  : '.$plan_html, 'woothemes' ) );


                    $order->payment_complete();
                    // Remove cart
                    $woocommerce->cart->empty_cart();
                    // Return thankyou redirect
                    return array(
                        'result' => 'success',
                        'redirect' => $this->get_return_url( $order )
                    );
                }
                else {
                    wc_add_notice( __('Payment error:', 'woothemes') . $error_message, 'error' . ' SmartDebit test mode');
                    return;
                }



            }


            return;
        }

        public function getInfusionSoftReference($user_id) {
            // get infusionsoft id
            // error_log('getInfusionSoftReference u : '.$user_id);
            $is_contact_id = get_usermeta($user_id,'is_contact_id',true);

            if ((int)$is_contact_id>0) {} else {$is_contact_id=999998;};

            $is_contact_id = str_pad($is_contact_id,8,'0',STR_PAD_LEFT);

            // error_log('getInfusionSoftReference : '.$is_contact_id);

            return $is_contact_id;
        }

        private function userHasDDI($user_id,$order_id=0) {
            // get infusionsoft id
            $is_contact_id = $this->getInfusionSoftReference($user_id);

            $args = array(
                            'query[service_user][pslid]' => $this->get_option('api_pslid'),
                            'query[report_format]'       => 'XML',
                            'query[reference_number]'    => $is_contact_id
                         );

            $api_result = $this->makeCall('/api/data/dump',$args,$order_id);

            if ($api_result !== false) {
                $xml = simplexml_load_string($api_result);
                return $xml;
            }
            else {
                return false;
            }
        }

        public function makeCall($request_path, $args, $order_id=0, $return_error = false) {
            $request_host = 'https://secure.ddprocessing.co.uk';

            $options = array(
                CURLOPT_RETURNTRANSFER => true, // return web page
                CURLOPT_HEADER => false, // don't return headers
                CURLOPT_POST => true,
                CURLOPT_USERPWD => $this->get_option('api_user') . ':' . $this->get_option('api_password'),
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_HTTPHEADER => array('Accept: application/XML'),
                CURLOPT_USERAGENT => $this->get_option('user_agent'),
             );

            $session = curl_init( $request_host . $request_path );
            curl_setopt_array( $session, $options );

            // tell cURL to accept an SSL certificate if presented
            if(ereg("^(https)", $request_host)) {
                curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
            }

            $postargs = http_build_query($args);
            //error_log('postargs:'.print_r($postargs,1));

            // Tell curl that this is the body of the POST
            curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);

            // $output contains the output string
            $output = curl_exec($session);
            $header = curl_getinfo( $session );

            //error_log('output:'.print_r($output,1));
            //error_log('header:'.print_r($header,1));

            if(curl_errno($session)) {
                curl_close($session);
                return false;
            }
            else {
                curl_close($session);
                switch ($header['http_code']) {
                    case 200:
                        return $output;
                        break;
                    default:
                        if($return_error) {
                            return $output;
                        } else {
                            $this->handleErrors($output);
                            return false;
                        }
                }
            }
        }

        public function validatePostedSmartDebitFields() {
          $account_holder = trim($_POST['account_holder']);
          if (empty($_POST['account_holder'])) {
              wc_add_notice('DirectDebit Account Holder name(s) is(are) required','error');
          }
          else if (strlen($account_holder)> 32) {
              wc_add_notice('DirectDebit Account Holder name(s) maximum length is 32 characters','error');
          }

          $account_number = trim($_POST['account_number']);
          if (empty($_POST['account_number'])) {
              wc_add_notice('DirectDebit Account Number is required','error');
          }
          else if (!is_numeric($account_number)) {
              wc_add_notice('DirectDebit Account Number should only contain digits','error');
          }
          else if (strlen($account_number) != 8) {
              wc_add_notice('DirectDebit Account Number has to be 8 digits','error');
          }

          $sort_code = trim($_POST['sort_code']);
          if (empty($_POST['sort_code'])) {
              wc_add_notice('DirectDebit Sort Code is required','error');
          }
          else if (!is_numeric($sort_code)) {
              wc_add_notice('DirectDebit Sort Code should only contain digits','error');
          }
          else if (strlen($sort_code) != 6) {
              wc_add_notice('DirectDebit Sort Code has to be 6 digits','error');
          }

          return array($account_holder, $account_number, $sort_code);
        }
    }



    /**
    * Add the Gateway to WooCommerce
    **/
    function woocommerce_add_smartdebit_gateway($methods) {
        //error_log('woocommerce_add_smartdebit_gateway');
        //error_log(print_r($methods,1));
        $methods[] = 'WC_SmartDebit_Gateway';
        //error_log(print_r($methods,1));
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_smartdebit_gateway' );
}
