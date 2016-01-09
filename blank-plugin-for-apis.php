<?php
/*
Plugin Name: Blank Wordpress Plugin for APIs
Plugin URI:  https://github.com/cdolek/blank-plugin-for-apis-wordpress
Description: This plugin helps you develop your own api
Version: 1.0.0
Author: Cenk Dolek
Text Domain: blankplugin
Domain Path: /language
Author URI: http://cenkdolek.com
Author Email: cdolek@gmail.com
License: Released under the MIT License
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// BlankPlugin Class
class BlankPlugin {

    private $plugin_path;
    private $plugin_url;
    private $action;
    private $keyword;

    function __construct() 
    {   
        // Set up default vars
        $this->plugin_path          = plugin_dir_path( __FILE__ );
        $this->plugin_url           = plugin_dir_url( __FILE__ );

        // Set up activation hooks
        register_activation_hook( __FILE__, array(&$this,   'activate') );
        register_deactivation_hook( __FILE__, array(&$this, 'deactivate') );

        // Set up l10n        
        load_plugin_textdomain( 'blankplugin', false, dirname( plugin_basename( __FILE__ ) ) . '/language' );                

        // actions
        add_action( 'pre_get_posts',  array(  &$this, 'init') );
        add_action( 'init',           array(  &$this, 'custom_rewrite_tag'), 10, 0 );

    } // construct ends

    function init() {

        $this->action          = get_query_var( 'action' );
        $this->keyword         = get_query_var( 'keyword' );

        if( isset( $this->action ) && !empty( $this->action ) ) {

            switch ( $this->action ) {

                /**
                *
                *   API Endpoints

                */              

                case 'example':
                    $response = array (
                        'action'    =>      $this->action,
                        'keyword'   =>      $this->keyword
                    );
                    break;

                default:                
                    break;
            
            } // switch

            header( 'Content-type: application/json' );
            echo json_encode($response);

            exit(0);

        } //if

    }

    function add_rewrite_rules() {
        add_rewrite_rule('^blankplugin_api/([^/]*)/([^/]*)/?','index.php?action=$matches[1]&keyword=$matches[2]','top');        
    }

    function custom_rewrite_tag() {
        add_rewrite_tag('%action%', '([^&]+)');
        add_rewrite_tag('%keyword%', '([^&]+)');
    }

    function activate() {
        global $wp_rewrite;
        $this->add_rewrite_rules();
        $wp_rewrite->flush_rules();
    }

    function deactivate(){        
        flush_rewrite_rules();
    }

} // class ends

$blankplugin = new BlankPlugin();

?>