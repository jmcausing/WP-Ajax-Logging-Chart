jQuery(document).ready(function ($) {

    // check ajax status from get post meta
    console.log(ajax_logging_status.ajax_status);  
    if (ajax_logging_status.ajax_status == 'off') {
        jQuery('input.ajax_switch').prop("checked", false);
    }
    else {
        jQuery('input.ajax_switch').prop("checked", true);
    }
 
 
    jQuery("input.ajax_switch").change(function() { 
     
        var ajax_switch_on = jQuery("input.ajax_switch").is(":checked"); 
 
        if  (ajax_switch_on == true) { 

            data = {
                "action": "ajax_switcher",
                "ajax_switch": "ajax_is_on"
            };

            $.ajax({ 
                url : "/wp-admin/admin-ajax.php",
                data : data,
                type : "POST",

                success : function( data ){
                   console.log(data);
                }

            });

        }
        else {
            data = {
            "action": "ajax_switcher",
            "ajax_switch": "ajax_is_off"
            };

            $.ajax({ 
            url : "/wp-admin/admin-ajax.php",
            data : data,
            type : "POST",

            success : function( data ){
               console.log(data);
            }

            });
        }
   
   });

    $( ".delet_ajax" ).click(function() {
        confirm("Do you wish to delete the ajax log file?");
        
         data = {
            "action": "ajax_delete",
            "delete_confirm": "delete_me"
        };

        $.ajax({ 
            url : "/wp-admin/admin-ajax.php",
            data : data,
            type : "POST",

            success : function( data ){
                location.reload(true);

               console.log(data);
            }   
        });
    });

     
 });
