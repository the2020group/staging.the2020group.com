<?php

function remove_user_groups_when_subscription_expired($user_id=0,$subscription_key='') {
    global $wpdb;

    if ($user_id > 0) {
        $sql = 'DELETE FROM wp_groups_user_group WHERE user_id=%d ';
        $wpdb->query( $wpdb->prepare($sql, $user_id) );
    }
}

add_action('subscription_expired','remove_user_groups_when_subscription_expired');
