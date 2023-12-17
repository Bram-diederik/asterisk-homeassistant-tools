#!/usr/bin/php
<?php
$debug = false;
//READ INFO FOR TASKER SCRIPT


//HARDCODED VARIBLES
//the addressbook switches are hard coded
//Very_close is set to always calls. 
//close = close friends and family
//all the global address list
//none unknown number

//Lots of variables are bind to these 4(3) settings


//torch is my phone. and it will be checked if its charching in the bedroom

//read status from home assistant cached values for optimal speed for asterisk
$asterisk_dir = "/opt/sascha/asterisk/";

require_once("/opt/sascha/common.php");


if ($argc != 2) {
    echo "Usage: php search_number.php <phone_number>\n";
    exit(1);
}

include("/opt/sascha/nextcloud/config.php");



$result = shell_exec('/opt/sascha/homeassistant/sensorget.php sensor.bool_asterisk_up_for_torch');

// Check the result and perform actions accordingly
if (trim($result) === 'off') {
    //phone system to torch down dont pickup
    echo "false";
    exit(0);

} 


// Get the phone number from the command line argument
$searchNumber = $argv[1];

if ($searchNumber == "anonymous") {
   $addressbook = "anonymous";
}  else {
//check callendar 
$output = shell_exec("grep -q $searchNumber /opt/sascha/nextcloud/calendar_calls.txt && echo -n true || echo -n false");

// Check the output and set the PHP variable accordingly
if (trim($output) === "true") {
    if ($debug) echo "Caller in meeting\n";
    echo "false";
    exit(0); 
}  

$pdo = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);

$updatePhonebookSql = "insert into `call_history` (`number`) VALUES (:number)";
$updatePhonebookStmt = $pdo->prepare($updatePhonebookSql);
$updatePhonebookStmt->bindParam(':number', $searchNumber, PDO::PARAM_STR);
$updatePhonebookStmt->execute();

try {
    // Create a PDO database connection
    $pdo = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL query to search for the name by phone number
    $sql = "SELECT contacts.full_name, contacts.category
        FROM phone_numbers
        JOIN contacts ON phone_numbers.contact_id = contacts.id
        WHERE phone_numbers.phone_number = :number";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':number', $searchNumber, PDO::PARAM_STR);
    $stmt->execute();

    // Check if the phone number was found in the database
    if ($stmt->rowCount() > 0) {
        $result = $stmt->fetch();
        $addressbook = $result["category"];
    } else {
        $addressbook = "none";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
}

if  ($addressbook == "very close")
{
    echo "false";
    exit(0);
}


$sBeschikbaar = file_get_contents($asterisk_dir."available.txt");

if ($debug)
  echo $sBeschikbaar;

$sBusy = file_get_contents($asterisk_dir."busy.txt");
if ($debug)
  echo $sBusy;
$sCalendar = file_get_contents($asterisk_dir."calendar_item.txt");
if ($debug)
  echo $sCalendar;
$sBlock = file_get_contents($asterisk_dir."block_unknown.txt");
$sTochBedroomCharing =  file_get_contents($asterisk_dir."phone_change.txt");
if ($addressbook == "anonymous" ) {
if ($sBeschikbaar == "on") {
   $bKarinPickup = false;
}  else $bKarinPickup = true;

} elseif ($sBlock == "on" && $addressbook == "none" ) {
    $bKarinPickup = true;
} elseif ($sBeschikbaar == "on") {
   $bKarinPickup = false;
}  
else if ($sCalendar == "on") {
    $bKarinPickup = true;
 } else if ($sBusy == "on") {
    $bKarinPickup = true;
 } else {

  $now =  new DateTime();
  $sleep_none = DateTime::createFromFormat('H:i', "21:00");
  $wakeup_none = DateTime::createFromFormat('H:i', "08:00");
  $sleep_all = DateTime::createFromFormat('H:i', "21:30");
  $wakeup_all = DateTime::createFromFormat('H:i', "08:00");
  $sleep_close = DateTime::createFromFormat('H:i', "23:30");
  $wakeup_close = DateTime::createFromFormat('H:i', "07:30");
  $sleep_torch_close = DateTime::createFromFormat('H:i', "21:30");
  $wakeup_torch_close = DateTime::createFromFormat('H:i', "07:30");
 

   if ($addressbook  == "none" && ($sleep_none < $now || $wakeup_none > $now)) {
     $bKarinPickup = true;
   } else if ($addressbook  == "all" && ($sleep_all < $now || $wakeup_all > $now)) {

    $bKarinPickup = true;
   } else if ($addressbook  == "close" ) {
     if ($sTochBedroomCharing == "on" &&  ($sleep_torch_close < $now || $wakeup_torch_close >$now)) 
     {
       if ($debug) {
       echo "close sleeping torch";
       }
       $bKarinPickup = true;
     } else if ($sleep_close < $now || $wakeup_close > $now )
     {
       if ($debug) {
       echo "close sleeping";
       }
       $bKarinPickup = true;
     }
  }
}

$name_number = file_get_contents("/opt/sascha/nextcloud/name_number.txt"); 
system("/opt/sascha/homeassistant/scriptrun.php phonebook_update '{ \"name\": \"$name_number\" }' > /dev/null 2>&1");


if (@$bKarinPickup) {
  echo "true";
  exit(1);
} else {
  //no pickup check blacklists 
  //Use Asterisk CLI to query the blacklist database
  // Execute the Asterisk command and capture the output
  exec("asterisk -rx \"database get denylist $searchNumber\"", $output, $return_var);

  // Check if the command was successful
  if ($return_var === 0) {
      // Check if the output contains "Value: 1"
      if (in_array("Value: 1", $output)) {
          // Caller number is denied
          echo "true";
          exit(1);
      } else {
          // Caller number is not denied
          echo "false";
          exit(0);
      }
  } else {
      // Handle the case where the Asterisk command failed
      echo "false";
      exit(0);
  }
}
?>
