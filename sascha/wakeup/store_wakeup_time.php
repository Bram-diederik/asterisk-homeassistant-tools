#!/usr/bin/php
<?php
include("/opt/sascha/common.php");

// Get the caller ID and wake-up time from Asterisk
$caller_id = $argv[1];
$hours = $argv[2];
$minutes = $argv[3];

$conn = mysqli_connect($dbservername,$dbusername, $dbpassword, $dbname);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}


if (!($caller_id && $hours  && $minutes )) 
  die("store_wakeup_time.php caller_id hours minutes");

// Get the current time
$current_time = time();

// Create a DateTime object for today's date
$today = new DateTime('today');

// Create a DateTime object for the wake-up time
$wakeup_time = new DateTime();
$wakeup_time->setTime($hours, $minutes);

// If the wake-up time has already passed today, add one day
if ($wakeup_time->getTimestamp() < $current_time) {
  $today->modify('+1 day');
}

// Set the wake-up time to the correct date and time
$wakeup_time->setDate($today->format('Y'), $today->format('m'), $today->format('d'));

// Convert the wake-up time to a datetime string
$wakeup_time_string = $wakeup_time->format('Y-m-d H:i:s');

// Open the SQLite database file
//$db = new SQLite3('/opt/sascha/db/wakeup.sqlite');

// Create the wakeups table if it doesn't already exist
//$conn->exec('CREATE TABLE IF NOT EXISTS wakeups (caller_id TEXT PRIMARY KEY, wakeup_time DATETIME)');
$sql = "CREATE TABLE IF NOT EXISTS wakeups (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    caller_id TEXT,
    wakeup_time DATETIME
)";

mysqli_query($conn, $sql);

if (mysqli_query($conn, $sql)) {
    echo "Table created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}


// Insert or replace the wake-up time in the database
//$stmt = $conn->prepare('INSERT OR REPLACE INTO wakeups (caller_id, wakeup_time) VALUES (:caller_id, :wakeup_time)');
//$stmt->bindValue(':caller_id', $caller_id, SQLITE3_TEXT);
//$stmt->bindValue(':wakeup_time', $wakeup_time_string, SQLITE3_TEXT);
//$result = $stmt->execute();
$stmt = $conn->prepare('INSERT INTO wakeups (caller_id, wakeup_time) VALUES (?, ?) ON DUPLICATE KEY UPDATE wakeup_time = ?');
if (!$stmt) {
    die("Error preparing statement: " . mysqli_error($conn));
}
$stmt->bind_param('sss', $caller_id, $wakeup_time_string, $wakeup_time_string);
$stmt->execute();

// Close the database connection
$conn->close();
?>
