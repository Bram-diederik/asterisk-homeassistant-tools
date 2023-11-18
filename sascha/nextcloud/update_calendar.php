#!/usr/bin/php
<?php
$dir = "/opt/sascha/nextcloud/";
require_once $dir.'/vendor/autoload.php';
require_once $dir.'/config.php';
// Create a PDO database connection
$pdo = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);


$files = glob($calDir.'/*');
foreach($files as $file) 
{
    if(is_file($file))
        unlink($file);
} 
exec("curl -X PROPFIND -u '".$username.":".$password."' -H 'Content-Type: text/xml; charset=\"utf-8\"' --data '<?xml version=\"1.0\" encoding=\"utf-8\" ?><D:propfind xmlns:D=\"DAV:\" xmlns:C=\"urn:ietf:params:xml:ns:caldav\"><D:prop><D:getetag /><D:displayname /><D:getcontenttype /></D:prop></D:propfind>' '"
.$host."/remote.php/dav/calendars/".$username."/".$calname."/' > ".$calDir."calendar_listing.xml ");

exec("xmlstarlet sel -N D=\"DAV:\" -N C=\"urn:ietf:params:xml:ns:carddav\" -t -m \"//D:href\" -v . -n ".$calDir."calendar_listing.xml > ".$calDir."caldav_urls.txt");


$command = "#!/bin/bash\n" .
    "while IFS= read -r url;\n" .
    "do\n" .
    "  if [ \"\$url\" != \"remote.php/dav/calendars/".$username."/".$calname."/\" ]; then\n" .
    "    filename=$calDir\$(basename \"\$url\");\n" .
    "    curl -u '".$username.":".$password."' -o \"\$filename\" \"$host\$url\" > /dev/null 2>&1 \n" .
    "  fi;\n" .
    "done < ".$calDir."caldav_urls.txt\n";
// Execute the modified command
echo "download ics files\n";

exec($command);


// Get a list of ICS files in the directory
$icsFiles = glob($calDir . '*.ics');

// Sort the files by date
usort($icsFiles, function ($a, $b) {
    return filemtime($a) - filemtime($b);
});
//get current time
$currentTime = time();
//make an empty attendees email array.
$aAttendees = array();
// Iterate through ICS files
foreach ($icsFiles as $icsFile) {
    // Parse the ICS file
    $ical = new ICal\ICal($icsFile);

  foreach ($ical->events() as $event) {
    // Check if the event is in the future
$eventEndTime = $event->dtend_array[2];
$eventStartTime = $event->dtstart_array[2];
$summary = $event->summary;


    if (($eventStartTime - $timeBeforeMeeting <= $currentTime) && ($eventEndTime >= $currentTime)) {
        echo "$summary  $eventStartTime $eventEndTime  $currentTime \n";
        // Get the attendees for the event
	$organizer = $event->organizer;
	$attendees = explode(',', $event->attendee);

	// Remove "mailto:" prefix
	$organizerEmail = substr($organizer, 7);
	$attendeeEmails = array_map(function ($attendee) {
	    return substr($attendee, 7);
	}, $attendees);

	// Print or use the email addresses as needed
	//echo "Organizer Email: $organizerEmail\n";
	//echo "Attendee Emails: " . implode(', ', $attendeeEmails) . "\n";
       $aAttendees = array_merge($aAttendees, [$organizerEmail], $attendeeEmails);
    }
  }

}

$emailString = "";
if (count($aAttendees) > 0) {
	$sqlSub = false;
	foreach($aAttendees as $email) {
                if ($sqlSub) {
  		     $sqlSub = $sqlSub. ",'$email'";
                } else {
                    $sqlSub = "'$email'";
                }
	}
        $emailSql = "SELECT phone_numbers.phone_number FROM contacts JOIN phone_numbers ON contacts.id = phone_numbers.contact_id WHERE contacts.id IN ( SELECT contact_id FROM emails WHERE email_address IN ($sqlSub) ); ";
        $emailStmt = $pdo->prepare($emailSql);
        $emailStmt->execute();
        while( $emailResult = $emailStmt->fetch(PDO::FETCH_ASSOC)) {

        	$emailString .= $emailResult['phone_number'] . "\n";
	}
}
	file_put_contents($dir."calendar_calls.txt", $emailString );

?>
