#!/usr/bin/php
<?php
if ($argc != 2) {
    echo "Usage: php search_name.php <phone_number>\n";
    exit(1);
}

// Database connection settings
$dir = "/opt/sascha/nextcloud/";
include($dir."config.php");
// Get the phone number from the command line argument
$searchNumber = $argv[1];

$putnumber = str_replace("+", "00",$argv[1]);


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
#        print_r($result);
        $name = $result["full_name"];
        $group = $result["category"];
        echo $name;
    file_put_contents($dir."name.txt", $name);
    file_put_contents($dir."group.txt", $group);
    file_put_contents($dir."name_number.txt", $name . " " . $searchNumber);
    file_put_contents($dir."number.txt",$putnumber);
    } else {
        echo $searchNumber;
    file_put_contents($dir."name.txt", $searchNumber);
    file_put_contents($dir."name_number.txt", $searchNumber);
    file_put_contents($dir."group.txt", "none");
    file_put_contents($dir."number.txt",$putnumber);
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$pdo = null;

?>
