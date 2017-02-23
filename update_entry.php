<?php
// Marathon - Lap 7
// Authors: Matthew Alicea and Caleb Grant

//HTML includes
$title = "Update Entry Locations";
include "components/dbconnect.php";
include "components/head.php";
include "components/contentheader.php";

//Start table and form
print <<<HTML_START
<main>
    <h2>Update/Add Entry Locations</h2>
    <form method="get" action="">
        <table id="entry-location-table">
            <tr>
                <th>Entry Location</th>
            </tr>
HTML_START;

//run function to check for changes
update($db);

$entryID = 0;
$entryName = "";
$d = "";
$sql = "select entry_location_id, entry_name from entry_locations";

//Populate table
if ($result = $db->query($sql)) {
    while ($d = $result->fetch_assoc()) {
        $entryID = $d ['entry_location_id'];
        $entryName = $d ['entry_name'];
        $entryLocation = "entry_location_".$entryID;
        echo "<tr><td><input type=\"text\" name='$entryLocation' value='$entryName'></td></tr>";
    }
}

print "</table>";

//Add and Save buttons
print <<<HTML_START
        <button type="button" id="addLocation" class="update">Add Entry Location</button>
        <br>
        <button type="submit" id="saveChanges" class="update">Save Entry Location Changes</button>
    </form>
</main>
HTML_START;

//TO DO: Update entries if they are changed
function update($db) {
    $mainCount = 0;
    if (isset($_GET['entry_location_1'])) {
       $count = 1;
       while (isset($_GET['entry_location_'.$count])) {         
            $newName1 = $_GET['entry_location_'.$count];
            $update1 = "update entry_locations set entry_name='$newName1' where entry_location_id=$count;";
            $result = $db->query($update1);
            $count++;
            $mainCount++;
        }
    }
    if  (isset($_GET['entry_location_new1'])) {
        $count = 1;
        while (isset($_GET['entry_location_new'.$count])) {
            $newEntry = $_GET['entry_location_new'.$count];
            $newID = $count + $mainCount;
            $addEntry = "insert into entry_locations (entry_location_id, entry_name) values($newID, '$newEntry');";
            $result = $db->query($addEntry);
            $count++;
        }
    }
    $db->commit();
}

print <<<HTML_START
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/attendanceentry.css">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="js/attendanceentry.js"></script>
HTML_START;

include "components/footer.php";
$db->close();
?>

