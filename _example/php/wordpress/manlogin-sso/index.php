<?php
/*
    Plugin Name: ManLogin.com SSO
    Plugin URI: http://wordpress.org/plugins/manlogin-sso/
    Description: Add a ManLogin.com SSO to the login and registration 
    Author: Manlogin.com
    Version: 1.0.0
    Author URI: https://manlogin.com
    Text Domain: manlogin-com-sso
 */


require('core/jwt.php');
class ssoManlogin{

    public static function init() {
        add_action('login_form',array('ssoManlogin', 'ssoManlogin_form'),99); //Add ManLogin Buttom to Login Form
        add_action('register_form',array('ssoManlogin', 'ssoManlogin_form'),99); //Add ManLogin Buttom to Register Form

        add_action( 'admin_menu', array('ssoManlogin', 'register_menu_page' ));  //Add ManLogin Menu For Admin
        add_action( 'admin_init', array('ssoManlogin', 'register_settings' ));  //Register Setting Of ManLogin SSO Plugin


        add_action('init',array('ssoManlogin', 'checkIfIssetTicket' )); // check ticket sent From ManLogin SSO as Url Params

    }

    function checkIfIssetTicket() { 
        if (isset($_GET['ticket'])) {
            $jwt = new JWT();
            $jwt = $jwt->decode($_GET['ticket'],get_option('manLogin_sso_publicKey')); // decode ticket
            if (is_object($jwt) && array_key_exists("mobile",$jwt)){
                $user_id = username_exists( $jwt->mobile ); // check user exists
                if ( ! $user_id) {
                    $random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
                    $user_id = wp_create_user( $jwt->mobile, $random_password );
                    
                    ssoManlogin::getUserData($jwt->uid,$user_id); // get More Data Of User From Manlogin SSO 
                    ssoManlogin::loginUser($user_id);
                } else {
                    
                    ssoManlogin::getUserData($jwt->uid,$user_id); // get More Data Of User From Manlogin SSO 
                    ssoManlogin::loginUser($user_id);
                }
            }
        }
    }

    public static function getUserData($sso_user_uid = "",$user_id="") // get More Data Of User From Manlogin SSO 
    {
        if($sso_user_uid != "" && $user_id != ""){
            $response = wp_remote_get( "https://manlogin.com/api/person/$sso_user_uid/data" ,
                array(
                    'headers' => array(
                        'X-App' => get_option('manLogin_sso_uid'),
                        'X-S2SToken'=> get_option('manLogin_sso_S2SToken')
                    ) 
                )
            );
            $g_response = json_decode( wp_remote_retrieve_body($response), true );
            if(is_array($g_response)){
                if($g_response["Code"] == 200 && is_array($g_response["Data"])){
                    if(array_key_exists("name",$g_response["Data"])){
                        update_user_meta( $user_id, 'first_name', $g_response["Data"]["name"] );
                    }
                    if(array_key_exists("familyName",$g_response["Data"])){
                        update_user_meta( $user_id, 'last_name', $g_response["Data"]["familyName"] );
                    }
                }
            }

            $outcome = trim(get_user_meta($user_id, 'first_name', true) . " " . get_user_meta($user_id, 'last_name', true));
            if (!empty($outcome)) {
                wp_update_user( array ('ID' => $user_id, 'display_name' => $outcome));    
            }
        }

        
    }


    public static function loginUser($user_id = "") // Login User After CallBack From SSO
    {
        if($user_id != ""){
            wp_clear_auth_cookie();
            wp_set_current_user ( $user_id );
            wp_set_auth_cookie  ( $user_id );
            $redirect_to = user_admin_url();
            wp_redirect($redirect_to);
            exit();
        }else{
            $redirect_to = site_url();
            wp_redirect($redirect_to);
            exit();
        }
    }
    public static function ssoManlogin_form() {  //Add ManLogin Buttom to Login And Register Form if Admin Complete Data Of Your App In ManLogin
        if(strlen(get_option('manLogin_sso_uid')) > 0 && strlen(get_option('manLogin_sso_publicKey')) > 0 && strlen(get_option('manLogin_sso_S2SToken')) > 0 && strlen(get_option('manLogin_sso_token')) > 0):
            $jwt = new JWT();
            $jwt = $jwt->encode( array('exp'=>time()+360 ),get_option('manLogin_sso_token') );
            echo "<hr style='margin: 10px;'/><div style='display: flex;justify-content: center;'><a href='https://manlogin.com/cas/login?service=".site_url()."&hash=".$jwt."'><img title='Login By ManLogin' src='".plugins_url('/images/login-by-manlogin-sso.svg',__FILE__)."'></a></div><hr style='margin: 10px;'/>";
        endif;
    }

    public static function register_menu_page(){ //Add ManLogin Menu For Admin
        add_options_page( 'تنظیمات ورود یکپارچه من‌لاگین','ورود یکپارچه من‌لاگین', 'manage_options', plugin_dir_path(  __FILE__ ).'admin.php');
    }

    public static function register_settings() { //Register Setting Of ManLogin SSO Plugin
        add_option('manLogin_sso_uid', '');
        add_option('manLogin_sso_publicKey', '');
        add_option('manLogin_sso_S2SToken', '');
        add_option('manLogin_sso_token', '');
        
        
        register_setting( 'manlogin_sso', 'manLogin_sso_uid', 'ssoManlogin::filter_string' );
        register_setting( 'manlogin_sso', 'manLogin_sso_publicKey', 'ssoManlogin::filter_string' );
        register_setting( 'manlogin_sso', 'manLogin_sso_S2SToken', 'ssoManlogin::filter_string' );
        register_setting( 'manlogin_sso', 'manLogin_sso_token', 'ssoManlogin::filter_string' );
    }
}

ssoManlogin::init();
?>