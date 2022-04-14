<?php
/**
 * Plugin Name: wc-location
 * Plugin URI: wc-location.com
 * Description: override wc and put location
 * Author: imbharat420
 * Author URI: https://github.com/imbharat420
 * Version: 1.0
 * Text Domain: wc-location
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
    wp_redirect( home_url() );
    exit;
}

require_once( __DIR__ . '/inc/wc-custom.php');


function my_plugin_activation(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'postal_location';
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        postal_code int(20) NOT NULL,
        address varchar(500) NOT NULL,
        description varchar(500) NOT NULL,
        status varchar(50) NOT NULL,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

register_activation_hook(__FILE__,"my_plugin_activation");

function my_plugin_deactivation(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'postal_location';

    $q="TRUNCATE `$table_name`";
    $wpdb->query($q);
}


register_deactivation_hook(__FILE__,"my_plugin_deactivation");

function wc_location_scripts(){
   $path_js = plugins_url('js/main.js', __FILE__);
    $path_style = plugins_url('js/main.js', __FILE__);
    $dep = array('jquery');
    $ver = filemtime(plugin_dir_path(__FILE__).'js/main.js');
    $ver_style = filemtime(plugin_dir_path(__FILE__).'js/style.css');
    $is_login = is_user_logged_in() ? 1 : 0;

    wp_enqueue_style('my-custom-style', $path_style, '', $ver_style);

    wp_enqueue_script('my-custom-js', $path_js, $dep, $ver, true);
    wp_add_inline_script('my-custom-js', 'var ajaxUrl = "'.admin_url('admin-ajax.php').'";', 'before');
 
}
add_action('wp_enqueue_scripts', 'wc_location_scripts');
add_action('admin_enqueue_scripts', 'wc_location_scripts');

function wc_service_page(){
    include("admin/all-service-point.php");
}

function wc_add_service(){
    include("admin/add-service-point.php");
}

function wc_register_my_custom_menu_page() {
    $menu_slug='service-points';
    add_menu_page(
        __( 'Service Points', 'textdomain' ),
        'Service Points',
        'manage_options',
        $menu_slug,
        'wc_service_page',
        "",
        10
    );

    add_submenu_page(
        __( $menu_slug, 'Service Points' ),
        'Service Points',
        'Service Points',
        "manage_options",
        $menu_slug,
        'wc_service_page',
    );

    add_submenu_page(
        __( $menu_slug, 'Add Service Point' ),
        'Add Service Point',
        'Add Service Point',
        "manage_options",
        'add-service-points',
        'wc_add_service',
    );
}
add_action( 'admin_menu', 'wc_register_my_custom_menu_page' );


add_action('wp_ajax_my_search_func','my_search_func');
function my_search_func(){
    global $wpdb,$table_prefix;
    $wp_emp = $table_prefix."postal_location";
    $search_term = $_POST['search_term'];
    if(empty($search_term )){
        $q="SELECT * from `$wp_emp`" ;
    }else{
         $q = "SELECT * FROM `$wp_emp` WHERE 
        `postal_code` LIKE '%".$search_term."%'
        OR `address` LIKE '%".$search_term."%'
        OR `status` LIKE '%".$search_term."%';";
    }
    $results = $wpdb->get_results($q);
    foreach($results as $row):
        ?>
        <tr>
            <th class="title column-title has-row-actions column-primary page-title" scope="row"><?php echo $row->id;?></th>
            <td class="column-columnname"><?php echo $row->postal_code; ?></td>
            <td class="column-columnname"><?php echo $row->address; ?> </td>
            <td class="column-columnname"><?php echo $row->description;?></td>
            <td class="column-columnname"><?php echo $row->status;?></td>
        </tr>
        <?php
    endforeach;
    echo ob_get_clean();
    wp_die();
}


