#!/usr/bin/php
<?php
if ($argc < 2) {
    echo "Usage: php search_number.php <part_of_name> [phone type]\n";
    exit(1);
}

// Database connection settings
$dir = "/opt/sascha/nextcloud/";
include($dir."config.php");

// Get the part of the name from the command line argument
$partOfName = $argv[1];
if (@$argv[2]) {
  $type = $argv[2];
} else {
  $type = false;
}

try {
    // Create a PDO database connection
    $pdo = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql1 = "SELECT  contacts.full_name
        FROM contacts 
        WHERE contacts.full_name LIKE :name
        UNION
        SELECT alias.full_name
        FROM alias 
        WHERE alias.full_name LIKE :name";

    $stmt1 = $pdo->prepare($sql1);
    $stmt1->bindValue(':name', '%' . $partOfName . '%', PDO::PARAM_STR);
    $stmt1->execute();
    if ($stmt1->rowCount() == 1)
    {
    // SQL query to search for a phone number by part of a name
     if ($type) {
        $sql = "SELECT phone_numbers.phone_number,phone_numbers.phone_type, contacts.full_name,phone_numbers.pref
        FROM phone_numbers
        JOIN contacts ON phone_numbers.contact_id = contacts.id
        WHERE contacts.full_name LIKE :name
        AND phone_numbers.phone_type LIKE :type
        UNION
        SELECT phone_numbers.phone_number,phone_numbers.phone_type, contacts.full_name,phone_numbers.pref
        FROM phone_numbers
        JOIN contacts ON phone_numbers.contact_id = contacts.id
        JOIN alias ON alias.contact_id = contacts.id
        WHERE alias.full_name LIKE :name
        AND phone_numbers.phone_type LIKE :type
        ORDER BY pref DESC
        LIMIT 1"; // Limiting to one phone number

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', '%' . $partOfName . '%', PDO::PARAM_STR);
        $stmt->bindValue(':type', '%' . $type . '%', PDO::PARAM_STR);
        $stmt->execute();
     } else {
        $sql = "SELECT phone_numbers.phone_number,phone_numbers.phone_type, contacts.full_name,phone_numbers.pref
        FROM phone_numbers
        JOIN contacts ON phone_numbers.contact_id = contacts.id
        WHERE contacts.full_name LIKE :name
        UNION 
        SELECT phone_numbers.phone_number,phone_numbers.phone_type, contacts.full_name,phone_numbers.pref
        FROM phone_numbers
        JOIN contacts ON phone_numbers.contact_id = contacts.id
        JOIN alias ON alias.contact_id = contacts.id
        WHERE alias.full_name LIKE :name
        ORDER BY pref DESC
        LIMIT 1"; // Limiting to one phone number
   
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', '%' . $partOfName . '%', PDO::PARAM_STR);
        $stmt->execute();
     }

    // Check if the phone number was found in the database
    if ($stmt->rowCount() > 0) {
        $result = $stmt->fetch();
        $phoneNumber = $result["phone_number"];
        $fullName = $result["full_name"];
        $phoneType = $result["phone_type"];
        $data = array(
          'fullName' => $fullName,
          'phoneNumber' => $phoneNumber,
          'type' => $phoneType
        );

         $jsonData = json_encode($data);

         echo $jsonData;
    } else {
        echo "No phone number found for '$partOfName'\n";
    }
    } else if ($stmt1->rowCount() == 0) {
        echo "No phone number found for '$partOfName'\n";
    } else {
        echo "Multiple results for '$partOfName'\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$pdo = null;
?>
