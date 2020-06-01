<?php
	
/*
Plugin Name: Ajax Logging Chart
Plugin URI: http://causingdesignscom.kinsta.cloud/
Description: This logs all ajax transactions in your chart and displays a graph to improve visualization
Author: John Mark Causing
Author URI:  http://causingdesignscom.kinsta.cloud/
*/



// Call js scripts
wp_register_script( 'ajax_logging', plugin_dir_url(__FILE__).'js/ajax_logging.js', array('jquery') );
wp_enqueue_script( 'ajax_logging' );



function ajax_log_scripts() {
	wp_enqueue_style( 'style', get_stylesheet_uri() );
	wp_enqueue_style( 'ajax_log_css',  plugin_dir_url(__FILE__) . 'css/ajax_logging.css', array(), '1.1', 'all');
   
//	wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array ( 'jquery' ), 1.1, true);
   

}
add_action( 'admin_enqueue_scripts', 'ajax_log_scripts' );




add_action( 'init', 'ajax_logging' );

function ajax_logging($switch) {

  $ajax_log_file_json = WP_CONTENT_DIR . "/uploads/ajax-log.json";

  $current_data = file_get_contents($ajax_log_file_json);
 
 // Check if ajax is there. 
   if ( wp_doing_ajax() ){
   
    // Check which action is using that ajax..
     $ajax_action = $_REQUEST['action'];

     // Is this called in admin?
     $is_ajax_in_admin =  is_admin();
 
     $ajax_in_admin = 'Called in Admin: NO';
 
     if ($is_ajax_in_admin == 1) { $ajax_in_admin = "Called in Admin: YES";  }
 
 
         // check url
        $ajax_request_url =  $_SERVER['REQUEST_URI'];
 
        $ajax_current_time = current_time( 'timestamp' );
         
        $ajax_current_time = round(microtime(true) * 1000);
 
 
        $data_to_write = $ajax_action . "," . $ajax_in_admin . "," . $ajax_request_url . "," . $ajax_current_time  . "\n";
 
        // $ajax_log_file = WP_CONTENT_DIR . "/uploads/ajax-log.txt";
 
        // file_put_contents($ajax_log_file, $data_to_write, FILE_APPEND);
   
        $array_data = json_decode($current_data, true);
 
        $ajax_json_array = array(
             'ajax_action' => $ajax_action,
           //  'ajax_path_url' => $ajax_current_url, // Divi conflict using cookie
           //  'called_in_admin' => $ajax_in_admin,
           // 'ajax_request_url' => $ajax_request_url,
             'timestamp' => $ajax_current_time
        );
 
         $array_data[] = $ajax_json_array;
                 
         $data_proccesed = json_encode($array_data, JSON_PRETTY_PRINT);
 
         file_put_contents($ajax_log_file_json, $data_proccesed); 

     } 

 }
 

function myplugin_register_options_page() {
 
  add_menu_page('Ajax Loggin Chart', 'Ajax Logs', 'manage_options', 'theme-options', 'ajax_log_admin_page');

}

add_action('admin_menu', 'myplugin_register_options_page');


function ajax_log_admin_page() {
 

 $html_output = '
   <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script
   <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>

   </head>
   <body>
   
   <div class="switch_container">
   <h2 style="display:inline-block;padding-right: 15px;">Turn on Ajax logging! </h2>
   <label class="switch">
     <input class="ajax_switch" type="checkbox" checked>
     <span class="slider round"></span>
   </label>
   </div>
 
 

 
 </head>
 <body>
 <div id="chartContainer-hourly" style="height: 300px; width: 100%;"> Hourly Ajax Logs </div>
 <div id="chartContainer-daily" style="height: 300px; width: 100%;"> Daily Ajax Logs </div>

 ';
     echo $html_output;
}


/// This is your ajax handler called by your jquery file
function my_ajax_handler22() {
  // Retrieve data from ajax
  if( isset( $_POST[ "ajax_switch" ] ) ) {
    
      $ajax_status = $_POST[ "ajax_switch" ];
      echo $ajax_status;

     // ajax_logging($ajax_status);
    
      die;
    
  }
}
add_action('wp_ajax_echo_me', 'my_ajax_handler22'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_echo_me', 'my_ajax_handler22'); // wp_ajax_nopriv_{action}