#!/usr/bin/php
<?php
require_once("/opt/sascha/common.php");

if ($argc != 2) {
    die("Usage: php check_number.php <phone_number>\n");
}

$searchNumber = $argv[1]; // Get phone number from command-line argument

$pdo = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);

$timer = time() - 30;
// Query to check if the phone number exists with a timestamp within the last 20 seconds
$checkNumberSql = "SELECT COUNT(*) FROM `call_history` WHERE `number` = :number AND UNIX_TIMESTAMP(`timestamp`) > :timer";
$checkNumberStmt = $pdo->prepare($checkNumberSql);
$checkNumberStmt->bindParam(':number', $searchNumber, PDO::PARAM_STR);
$checkNumberStmt->bindParam(':timer', $timer, PDO::PARAM_INT);
$checkNumberStmt->execute();
// Fetch the result
$result = $checkNumberStmt->fetchColumn();

if ($result > 0) {
    echo "true";
} else {
    echo "false";
}
?>
