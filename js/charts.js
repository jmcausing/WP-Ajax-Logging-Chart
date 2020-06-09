jQuery(document).ready(function ($) {

    // get calculate yesterday as a date
    var start = new Date();
    start.setHours(0,0,0,0);
    

    // Hourly chart
    if (jQuery('#chartContainer-hourly').length == 1)  {


        var chart = new CanvasJS.Chart("chartContainer-hourly",{
            title:{
            text:"Ajax action logs - 24 hours"
            },
            axisX: {
            valueFormatString: "HH:mm",
            interval: 1,
            intervalType: "hour",
            minimum:  start.setHours(0,0,0,0), // you can use this ti display data from the first hour.

            },
            toolTip:  {
                shared: true
            },
            data: []
        });
        $.getJSON("/wp-content/uploads/ajax-log.json", function(data) {

            chart.options.data = [];
            var occurrences = data.reduce( (acc, obj) => {

            date = (obj.timestamp - (obj.timestamp % (24 * 60 * 60))); // to group by date

            acc[obj.ajax_action] = acc[obj.ajax_action] ? acc[obj.ajax_action] : {};
            acc[obj.ajax_action][date] = (acc[obj.ajax_action][date] || 0)+1
            return acc;
            }, {} )
            for(var actions in occurrences) {
                var dataPoints = [];
                for(var key in occurrences[actions]) {

                    // check if timestamp it's today's date
                    var inputDate = new Date(parseInt(key));
                    var todaysDate = new Date();

                    if (inputDate.setHours(0,0,0,0) == todaysDate.setHours(0,0,0,0)) {
                    //  console.log("TODAY IS TODAY");
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


    // Daily chart
    if (jQuery('#chartContainer-daily').length == 1)  {

        var chart_daily = new CanvasJS.Chart("chartContainer-daily",{
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

    
            chart_daily.options.data = [];
            var occurrences = data.reduce( (acc, obj) => {

            date = (obj.timestamp - (obj.timestamp % (24 * 60 * 60))); // to group by date

            acc[obj.ajax_action] = acc[obj.ajax_action] ? acc[obj.ajax_action] : {};
            acc[obj.ajax_action][date] = (acc[obj.ajax_action][date] || 0)+1
            return acc;
            }, {} )
            for(var actions in occurrences) {
                var dataPoints = [];
                for(var key in occurrences[actions]) {

                // check if timestamp months aer current month
                var timestamp_Month = new Date(parseInt(key)).getMonth();
                var current_month = new Date().getMonth();

                if (timestamp_Month == current_month) {
                  //  console.log("Get current month data");
                    dataPoints.push({ x: parseInt(key), y: occurrences[actions][key]});
                }
                
            }
            chart_daily.options.data.push({
                type: "line",
                showInLegend: true,
                name: actions,
                xValueType: "dateTime",
                xValueFormatString: "DD MMM YYYY",
                dataPoints: dataPoints
            });
            }
            
            chart_daily.render(); 
        });
    }



});