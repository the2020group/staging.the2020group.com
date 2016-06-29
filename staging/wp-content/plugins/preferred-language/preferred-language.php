<?php
    /*
        Plugin Name: Preferred Language
        Description: 
        Version: 0.1
        Author: First 10 Digital Ltd
        Author Email: stephan@first10.co.uk
    */
    if (!isset($_SESSION['prefered_language']) || $_SESSION['prefered_language']=='') {
        // if agent string isn't there
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || $_SERVER['HTTP_ACCEPT_LANGUAGE'] == '' ) {
            $_SESSION['prefered_language'] = 'en';
        }
        else {
            preg_match('|([a-z]{2})-[a-z]{2}|i',$_SERVER['HTTP_ACCEPT_LANGUAGE'],$match);
            $_SESSION['prefered_language'] = $match[1];
        }
    }