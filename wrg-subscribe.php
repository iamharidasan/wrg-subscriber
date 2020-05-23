<?php
/**
 * Plugin Name: WRG Subscribe
 * Plugin URI: https://example.com/plugins/the-basics/
 * Description: Manage subsribe form
 * Version: 1.0
 * Author: Haridasan
 * Author URI: https://fb.com/iamharidasan/
 */

function wrg_activate_function(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $prefix = $wpdb->prefix;
    $createQuery = "CREATE TABLE IF NOT EXISTS `".$prefix."wrg_subscribe_entries` ( `id` INT NOT NULL AUTO_INCREMENT , `fname` TEXT NOT NULL , `lname` TEXT NOT NULL , `email` TEXT NOT NULL , `dob` TEXT NOT NULL , `phone` TEXT NOT NULL , `website` TEXT NOT NULL , `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ".$charset_collate.";";
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    dbDelta($createQuery);
}
register_activation_hook( __FILE__, 'wrg_activate_function' );

function wrg_table_menu(){
    add_menu_page( 
        'WRG Subscribers',
        'WRG Subscribers',
        'manage_options',
        'wrg-subscribers',
        'wrg_table_display',
        "dashicons-welcome-widgets-menus",
        6
    ); 
}

add_action( 'admin_menu', 'wrg_table_menu' );

function wrg_table_display(){
    echo '<div class="wrap">
    <h1 class="wp-heading-inline">
        WRG Subscribers</h1>
    <hr class="wp-header-end"><form method="get">';
    include("admin-table.php");
    echo '</form></div>';
}

function wrg_form_display(){
    $form = '<form action="" id="wrg-subcribe-form" method="post">
    <div class="form-group">
        <label for="fname">First Name</label>
        <input type="text" name="fname" id="fname" class="form-control" placeholder="Enter your first name here" />
    </div>
    <div class="form-group">
        <label for="lname">Last Name</label>
        <input type="text" name="lname" id="lname" class="form-control" placeholder="Enter your last name here" />
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email id here" />
    </div>
    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter your phone number" />
    </div>
    <div class="form-group">
        <label for="dob">Date of Birth</label>
        <input type="text" name="dob" id="dob" class="form-control" placeholder="Pick your date of birth" />
    </div>
    <div class="form-group">
        <label for="website">Website</label>
        <input type="text" name="website" id="website" class="form-control" placeholder="Website" />
    </div>
    <input type="hidden" name="action" value="wrg_action">
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Subscribe</button>
    </div>
    </form>';
    echo $form;
}
add_shortcode( "wrg-subscribe", "wrg_form_display" );

function wrg_scripts(){
    wp_dequeue_script('jquery');
    wp_enqueue_script('jquery');
    wp_enqueue_style('jquery-ui','https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css',array(),"1.12.1");
    wp_enqueue_script('jquery-ui','https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',array(),"1.12.1");
    wp_enqueue_script('wrg-script',plugins_url().'/wrg-subscribe/js/wrg.js',array(),"1.0");
    wp_localize_script( 'wrg-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
}
add_action( "wp_enqueue_scripts", "wrg_scripts" );

function wrg_action() {
    global $wpdb;
    $table = $wpdb->prefix.'wrg_subscribe_entries';

    $response = array('error' => true);

    $data = array();
    foreach($_POST as $name => $value){
        if($name!=="action"){
            $data[$name] = $value;
        }
    }

    $wpdb->insert($table,$data,$format);
    $response['insert_id'] = $wpdb->insert_id;

    $crmdata = array();
    foreach($_POST as $name => $value){
        if($name!="action"){
            if($name!="dob"){
                $crmdata[strtoupper($name)] = $value;
            }
        }
    }
    $dob = explode("/",$_POST['dob']);
    $crmdata['BIRTHDAY[day]'] = $dob[0];
    $crmdata['BIRTHDAY[month]'] = $dob[1];

    $apiKey = '4bfa55c209e9ee163afd9372d32f776e-us18';
    $listId = 'ba64ddd09c';

    $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/';

    $json = json_encode([
        'email_address' => $crmdata['EMAIL'],
        'status'        => "subscribed",
        'merge_fields'  => [
            'FNAME' => $crmdata['FNAME'],
            'LNAME' => $crmdata['LNAME'],
            'PHONE' => $crmdata['PHONE'],
            'WEBSITE' => $crmdata['WEBSITE'],
            'BIRTHDAY' => $dob[0]."/".$dob[1]
        ]
    ]);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                                                                 

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $response['mailchimp'] = $httpCode;
    
    $response['error'] = false;
    curl_close($curl);
    
    exit(json_encode($response));
}

add_action('wp_ajax_wrg_action', 'wrg_action');
add_action('wp_ajax_nopriv_wrg_action', 'wrg_action');