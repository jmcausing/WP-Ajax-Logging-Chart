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
                "action": "echo_me",
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
            "action": "echo_me",
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
});
 
 
 
 
// 24 hours Ajax Logging Chart -- End
// #####
jQuery(document).ready(function ($) {
 // get calculate yesterday as a date
 var start = new Date();
 start.setHours(0,0,0,0);
 
 

 if (jQuery('#chartContainer-hourly').length == 1)  {

    var chart = new CanvasJS.Chart("chartContainer-hourly",{
        title:{
        text:"Ajax action logs - 24 hours"
        },
        axisX: {

        valueFormatString: "HH:mm",

     //   intervalType: "hour",

        minimum:  start.setHours(0,0,0,0),

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

                // check if timestamp is today
                var inputDate = new Date(parseInt(key));
                var todaysDate = new Date();

                if (inputDate.setHours(0,0,0,0) == todaysDate.setHours(0,0,0,0)) {
                    console.log("TODAY IS TODAY");
                    dataPoints.push({ x: parseInt(key), y: occurrences[actions][key]});

                }

        }
        chart.options.data.push({
            type: "line",
            showInLegend: true,
            name: actions,
            xValueType: "dateTime",
            xValueFormatString: "HH mm",
            dataPoints: dataPoints
        });
        }
        
        chart.render(); 
    });



 }
     
 });
// 24 hours Ajax Logging Chart -- End
// #####


 
 

// #####
// Daily Ajax Logging Chart -- Start

jQuery(document).ready(function ($) {


    
 if (jQuery('#chartContainer-daily').length == 1)  {

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
    }
     

 });

// Daily Ajax Logging Chart -- End
// #####
