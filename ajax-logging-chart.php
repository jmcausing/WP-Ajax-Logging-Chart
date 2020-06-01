<?php
	
/*
Plugin Name: Ajax Logging Chart
Plugin URI: http://causingdesignscom.kinsta.cloud/
Description: This logs all ajax transactions in your chart and displays a graph to improve visualization
Author: John Mark Causing
Author URI:  http://causingdesignscom.kinsta.cloud/
*/


add_action( 'init', 'ajax_logging' );

function ajax_logging($switch) {

  $ajax_log_file_json = WP_CONTENT_DIR . "/uploads/ajax-log.json";

  $current_data = file_get_contents($ajax_log_file_json);
 
 // Check if ajax is there. 
   if ( wp_doing_ajax() )
   
   {
     
     ?>

     <?php
 
     // Check which action is using that ajax..
     $ajax_action = $_REQUEST['action'];
   //	echo "It is calling this action: <br>" . $ajax_action . "<br>";
 
 
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
 
   <style>

 .switch_container {
   padding: 15px;
 }
 
   .switch {
     position: relative;
     display: inline-block;
     width: 60px;
     height: 34px;
   }
   
   .switch input { 
     opacity: 0;
     width: 0;
     height: 0;
   }
   
   .slider {
     position: absolute;
     cursor: pointer;
     top: 0;
     left: 0;
     right: 0;
     bottom: 0;
     background-color: #ccc;
     -webkit-transition: .4s;
     transition: .4s;
   }
   
   .slider:before {
     position: absolute;
     content: "";
     height: 26px;
     width: 26px;
     left: 4px;
     bottom: 4px;
     background-color: white;
     -webkit-transition: .4s;
     transition: .4s;
   }
   
   input:checked + .slider {
     background-color: #2196F3;
   }
   
   input:focus + .slider {
     box-shadow: 0 0 1px #2196F3;
   }
   
   input:checked + .slider:before {
     -webkit-transform: translateX(26px);
     -ms-transform: translateX(26px);
     transform: translateX(26px);
   }
   
   /* Rounded sliders */
   .slider.round {
     border-radius: 34px;
   }
   
   .slider.round:before {
     border-radius: 50%;
   }
   </style>
   </head>
   <body>
   
   <div class="switch_container">
   <h2 style="display:inline-block;padding-right: 15px;">Turn on Ajax logging! </h2>
   <label class="switch">
     <input class="ajax_switch" type="checkbox" checked>
     <span class="slider round"></span>
   </label>
   </div>
 
 
 
 <!-- Hour and minutes interval
 #### START
 #### 
 -->
 
 
   <script>
   jQuery(document).ready(function ($) {


    
    console.log("xx' . $ajax_log_file . '");  
 
 
   jQuery("input.ajax_switch").change(function() { 
     
     var ajax_switch_on = jQuery("input.ajax_switch").is(":checked"); 
 
     if  (ajax_switch_on == true) { 

          data = {
            "action": "echo_me",
            "ajax_switch": "ajax_is_on"
          };

          $.ajax({ 
            url : "http://localhost:3000/wp-admin/admin-ajax.php",
            data : data,
            type : "POST",

            success : function( data ){
                console.log(data);
            }

          });

     }
     else {
        data = {
          "action": "echo_me",
          "ajax_switch": "ajax_is_off"
        };

        $.ajax({ 
          url : "http://localhost:3000/wp-admin/admin-ajax.php",
          data : data,
          type : "POST",

          success : function( data ){
              console.log(data);
          }

        });
     }
   
   });
 
 
 
 
 
 // get calculate yesterday as a date
 var start = new Date();
 start.setHours(0,0,0,0);
 
 
     var chart = new CanvasJS.Chart("chartContainer-hourly",{
         title:{
         text:"Ajax action logs - 24 hours"
         },
         axisX: {
           valueFormatString: "HH:mm",
         //  interval: 2,
           intervalType: "hour",
           minimum:  start.setHours(0,0,0,0)
 
         },
         toolTip:  {
             shared: true
         },
         data: []
     });
     $.getJSON("/wp-content/uploads/ajax-log.json", function(data) {
 
         chart.options.data = [];
         var occurrences = data.reduce( (acc, obj) => {
           //  date = (obj.timestamp - (obj.timestamp % (24 * 60 * 60)))*1000; // to group by date
 
             date = (obj.timestamp - (obj.timestamp % (24 * 60 * 60))); // to group by date
 
 
           //  date = obj.timestamp;
 
           //  console.log(obj.timestamp);
 
         acc[obj.ajax_action] = acc[obj.ajax_action] ? acc[obj.ajax_action] : {};
         acc[obj.ajax_action][date] = (acc[obj.ajax_action][date] || 0)+1
         return acc;
         }, {} )
         for(var actions in occurrences) {
             var dataPoints = [];
             for(var key in occurrences[actions]) {
             dataPoints.push({ x: parseInt(key), y: occurrences[actions][key]});
         }
         chart.options.data.push({
             type: "splineArea",
             showInLegend: true,
             name: actions,
             xValueType: "dateTime",
             xValueFormatString: "HH mm",
             dataPoints: dataPoints
         });
         }
         
         chart.render(); 
     });
     
 });
 
 </script>
 
 
 <!-- Hour and minutes interval
 #### END
 #### 
 -->
 
 
 
 
 
 <!-- Hour and minutes interval
 #### START
 #### 
 -->
 
 
   <script>
   jQuery(document).ready(function ($) {

 
     var chart = new CanvasJS.Chart("chartContainer-daily",{
         title:{
         text:"Ajax action logs - Daily"
         },
         axisX: {
           valueFormatString: "DD MMM",
           interval: 1,
           intervalType: "day"
     
         },
         toolTip:  {
             shared: true
         },
         data: []
     });
     $.getJSON("/wp-content/uploads/ajax-log.json", function(data) {

 
         chart.options.data = [];
         var occurrences = data.reduce( (acc, obj) => {
           //  date = (obj.timestamp - (obj.timestamp % (24 * 60 * 60)))*1000; // to group by date
 
             date = (obj.timestamp - (obj.timestamp % (24 * 60 * 60))); // to group by date
 
 
           //  date = obj.timestamp;
 
          //   console.log(obj.timestamp);
 
         acc[obj.ajax_action] = acc[obj.ajax_action] ? acc[obj.ajax_action] : {};
         acc[obj.ajax_action][date] = (acc[obj.ajax_action][date] || 0)+1
         return acc;
         }, {} )
         for(var actions in occurrences) {
             var dataPoints = [];
             for(var key in occurrences[actions]) {
             dataPoints.push({ x: parseInt(key), y: occurrences[actions][key]});
         }
         chart.options.data.push({
             type: "line",
             showInLegend: true,
             name: actions,
             xValueType: "dateTime",
             xValueFormatString: "DD MMM YYYY",
             dataPoints: dataPoints
         });
         }
         
         chart.render(); 
     });
     
 });
 
 </script>
 
 
 <!-- Hour and minutes interval
 #### END
 #### 
 -->
 
 
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