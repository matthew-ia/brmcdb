<?php
// Marathon - Lap 6
// Author: Matthew Alicea

$eventID = 1;
include "components/dbconnect.php";
include "components/head.php";
include "components/contentheader.php";

/*EVENT NAME SECTION
Gets Event Name to display:
        + Event Name
        + Start/End timedate
        + Season Ticket Income */
function eventName($eventID, $db ) {
    //Variables
    $d = "";
    $head_id = 0;
    $head_name = "";
    $startDate = "";
    $startTime = "";
    $endDate = "";
    $endTime = "";
    $seasonTicAlloc = 0;
    $eventName = "";    // eventname: date

    //Retrieval for Headliner band_id
    $getHeadlinerBand = "select band_id from performances where event_id = $eventID AND performance_type = \"Headline\"";
    if ($result = $db->query($getHeadlinerBand)) {
        while ($d = $result->fetch_assoc()) {
            $head_id = $d['band_id'];
        }
    }

    //Retrieval for Headliner name
    $getName = "select band_name from bands where band_id = $head_id";
    if ($result = $db->query($getName)) {
        while ($d = $result->fetch_assoc()) {
            $head_name = $d["band_name"];
        }
    } 

    //Retrieval for Date/Time variables, season ticket allocation
    $getStuff = "select DATE_FORMAT(begin_datetime,'%Y-%m-%d'), DATE_FORMAT(end_datetime, '%Y-%m-%d'), TIME_FORMAT(begin_datetime, '%T'), TIME_FORMAT(end_datetime, '%T'), season_ticket_allocation from events where event_id = $eventID";
    if ($result = $db->query($getStuff)) {
        while ($d = $result->fetch_assoc()) {
            $startDate = $d["DATE_FORMAT(begin_datetime,'%Y-%m-%d')"];
            $endDate = $d["DATE_FORMAT(end_datetime, '%Y-%m-%d')"];
            $startTime = $d["TIME_FORMAT(begin_datetime, '%T')"];
            $endTime = $d["TIME_FORMAT(end_datetime, '%T')"];
            $seasonTicAlloc = $d["season_ticket_allocation"];
        }
    }
    
    //create event name
    $eventName = $head_name . " : " . $startDate;
    echo "$eventName";   
    //Create HTML and put variables in table
    print '<!-- EVENT NAME SECTION -->
        <div id="form-container" class="section">
            <form method="get" action="event/update">
                <input type="hidden" name="event_id">
                <table id="event-information">
                    <tr>
                        <td><label for="begin_date">Starting Date/Time</label></td>';
                       echo "<td><input type=\"date\" name=\"begin_date\" value=$startDate> <input type=\"time\" name=\"begin_time\" value=$startTime></td>";
    print '
                    </tr>
                    <tr>
                        <td><label for="end_date">Ending Date/Time</label></td>';
                        echo "<td><input type=\"date\" name=\"end_date\" value=$endDate> <input type=\"time\" name=\"end_time\" value=$endTime></td>";
    print '

                    </tr>
                    <tr>
                        <td><label for="season_ticket_income">Season Ticket<br>Income Allocation</label></td>';                   
                        echo "<td><input type=\"text\" name=\"season_ticket_income\" value=$seasonTicAlloc></td>";
    print '

                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Update" id="form-submit"></td>
                    </tr>
                </table>
            </form>
        </div>';
}

/* WEATHER SECTION
Gets all Weather entries for event*/
function getWeather($eventID, $db) {
    //Variables
    $timeDate = "";
    $recTime = "";
    $recDate = "";
    $weatherID = 0;
    $weatherNotID = 0;
    $skyCondID = 0;
    $precipCatID = 0;
    
    $humidity = 0;
    $temperature = 0;
    $weatherNot = "";
    $skyCond = "";
    $precip = "";

    $getWeather = "select weather_id, humidity, temperature, subjective_weather_notation_id, sky_condition_id, precipitation_category_id from weather where event_id = $eventID";
    $getDT = "select DATE_FORMAT(datetime_recorded,'%m-%d-%Y'), TIME_FORMAT(datetime_recorded, '%r') from weather where event_id = $eventID";
    if ($result = $db->query($getDT)) {
        echo "Event Datetime: "; 
        while ($d = $result->fetch_assoc()) {
            $recDate = $d["DATE_FORMAT(datetime_recorded,'%m-%d-%Y')"];
            $recTime = $d["TIME_FORMAT(datetime_recorded, '%r')"];
            echo "$recDate";
            echo " $recTime";
        }
    }
    $dateTime = $recDate . " " . $recTime;

    print '<!-- WEATHER SECTION -->
        <div id="weather-container" class="section">
            <h4>Weather Information</h4>
                <a href="event/weather/add?event_id=n" class="update">Add Weather Entry</a>
                <table>';
    echo "<thead><th>Time/Date Recorded</th><th>Humidity</th><th>Temperature</th><th>Weather Notation</th><th>Sky Conditions</th><th>Precipitation</th></thead>";
                    /*<thead>
                        <th>Time/Date Recorded</th>
                        <th>Humidity</th>
                        <th>Temperature</th>
                        <th>Weather Notation</th>
                        <th>Sky Conditions</th>
                        <th>Precipitation</th>
                    </thead>*/
        if ($result = $db->query($getWeather)) {
            while ($d = $result->fetch_assoc()) {
                $weatherID = $d["weather_id"];
                $humidity = $d["humidity"];
                $temperature = $d["temperature"];
                $weatherNotID = $d["subjective_weather_notation_id"];
                $skyCondID = $d["sky_condition_id"];
                $precipCatID = $d["precipitation_category_id"];
            }
        }
    $getWeatherNotText = "select subjective_weather_text from subjective_weather_notations where subjective_weather_notation_id = $weatherNotID";
    if ($result = $db->query($getWeatherNotText)) {
         while ($d = $result->fetch_assoc()) {
            $weatherNot = $d["subjective_weather_text"];
        }
    } 
    $getSkyCondText = "select sky_condition_text from sky_conditions where sky_condition_id = $skyCondID";
    if ($result = $db->query($getSkyCondText)) {
         while ($d = $result->fetch_assoc()) {
            $skyCond = $d["sky_condition_text"];
        }
    } 
    $getPrecipText = "select precipitation_category_text from precipitation_categories where precipitation_category_id = $precipCatID";
    if ($result = $db->query($getPrecipText)) {
         while ($d = $result->fetch_assoc()) {
            $precip = $d["precipitation_category_text"];;
        }
    } 
    echo "<tr weather-id=$weatherID><td>$dateTime</td><td>$humidity%</td><td>$temperature&deg</td><td>$weatherNot</td><td>$skyCond</td><td>$precip</td>
            <td><a href=\"event/weather?weather_id=$weatherID\" class=\"update\">Update</a></td></tr>";
                    
    echo "</table></div>";
}
eventName($eventID, $db);
getWeather($eventID, $db);

include "components/footer.php";
?>
