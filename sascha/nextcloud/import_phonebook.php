#!/usr/bin/php
<?php
include("/opt/sascha/nextcloud/vCard.php");
include("/opt/sascha/nextcloud/config.php");

// Create a PDO database connection
$pdo = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);



mkdir($vcardDir);

$files = glob($vcardDir.'/*');
foreach($files as $file) 
{
    if(is_file($file))
        unlink($file);
} 

exec("curl -X PROPFIND -u '".$username.":".$password."' -H 'Content-Type: text/xml; charset=\"utf-8\"' --data '<?xml version=\"1.0\" encoding=\"utf-8\" ?><D:propfind xmlns:D=\"DAV:\" xmlns:C=\"urn:ietf:params:xml:ns:carddav\"><D:prop><D:getetag /><D:displayname /><D:getcontenttype /></D:prop></D:propfind>' '".$host."/remote.php/dav/addressbooks/users/".$username."/contacts/' > ".$vcardDir."directory_listing.xml  ");
exec("xmlstarlet sel -N D=\"DAV:\" -N C=\"urn:ietf:params:xml:ns:carddav\" -t -m \"//D:href\" -v . -n ".$vcardDir."directory_listing.xml > ".$vcardDir."vcard_urls.txt");
echo "downloading contacts\n";

$command = "#!/bin/bash\n" .
    "while IFS= read -r url;\n" .
    "do\n" .
    "  if [ \"\$url\" != \"/remote.php/dav/addressbooks/users/$username/contacts/\" ]; then\n" .
    "    filename=$vcardDir\$(basename \"\$url\");\n" .
    "    curl -u '".$username.":".$password."' -o \"\$filename\" \"$host\$url\" > /dev/null 2>&1 \n" .
    "  fi;\n" .
    "done < ".$vcardDir."vcard_urls.txt\n";
// Execute the modified command
exec($command);

// Optionally, you can wait for all background processes to finish
while (pcntl_waitpid(0, $status) != -1) {
    // Process the status of each background process if needed
}
echo "updating contacts\n";

// Set PDO to throw exceptions on errors
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$checkTableSQL = "SHOW TABLES LIKE 'contacts'";
$result = $pdo->query($checkTableSQL);

if ($result->rowCount() == 0) {
// Create the phonebook table
    $createTableSQL1 = '
-- Create contacts table
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL
); ';

    $createTableSQL2 = '
-- Create phone_numbers table
CREATE TABLE phone_numbers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT NOT NULL,
    phone_type VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    pref BOOLEAN NOT NULL,
    FOREIGN KEY (contact_id) REFERENCES contacts(id)
); ';


    $createTableSQL3 = '
-- Create emails table
CREATE TABLE emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT NOT NULL,
    email_type VARCHAR(50) NOT NULL,
    email_address VARCHAR(200) NOT NULL,
    pref BOOLEAN NOT NULL,
    FOREIGN KEY (contact_id) REFERENCES contacts(id)
); ';
    $pdo->exec($createTableSQL1);
    $pdo->exec($createTableSQL2);
    $pdo->exec($createTableSQL3);
}



// Loop through all vCard files in the directory
$vcardFiles = glob("/opt/sascha/nextcloud/vcards/*.vcf");

// Loop through all vCard files
foreach ($vcardFiles as $vcardFile) {
    // Parse the vCard file
    $vcard = new vCard($vcardFile);

    // Check if vCard data is valid before accessing it
    if (!empty($vcard->fn[0])) {
        // Extract full name
        $fullName = (string)$vcard->fn[0];

        // Extract categories
        if (@$vcard->CATEGORIES[0]) {
            $categories = $vcard->CATEGORIES[0];

            // Check if "very close" exists in $vcard->CATEGORIES[0]
            if (in_array("very close", $categories)) {
                $category = "very close";
            } elseif (in_array("close", $categories)) {
                $category = "close";
            } else {
                $category = "all";
            }
        } else {
            $category = "all";
        }

        // Check if contact already exists
        $existingContactSql = "SELECT id FROM contacts WHERE full_name = :full_name";
        $existingContactStmt = $pdo->prepare($existingContactSql);
        $existingContactStmt->bindParam(':full_name', $fullName, PDO::PARAM_STR);
        $existingContactStmt->execute();
        $existingContactResult = $existingContactStmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingContactResult) {
            // Insert data into contacts table
            $contactSql = "INSERT INTO contacts (full_name, category) VALUES (:full_name, :category)";
            $contactStmt = $pdo->prepare($contactSql);
            $contactStmt->bindParam(':full_name', $fullName, PDO::PARAM_STR);
            $contactStmt->bindParam(':category', $category, PDO::PARAM_STR);
            $contactStmt->execute();

            $contactId = $pdo->lastInsertId();
        } else {
            // Contact already exists, get contact ID
            $contactId = $existingContactResult['id'];
        }
	if ($contactId == "160") {
}

//	print_r($vcard);
        // Extract phone numbers
        foreach ($vcard->tel as $tel) {
            $pref = false;
            $telType = false;
            if (is_array($tel)) {
	        if (!empty($tel['Value'])) {
        	      foreach ($tel['Type'] as $type) {
                	   if ($type == 'pref') {
                        	$pref = true;
	                        // If a preferred phone number has been found for this contact,
        	                // update all other phone numbers to have pref = false
                	        $updatePrefSql = "UPDATE phone_numbers SET pref = false WHERE contact_id = :contact_id";
                        	$updatePrefStmt = $pdo->prepare($updatePrefSql);
	                        $updatePrefStmt->bindParam(':contact_id', $contactId, PDO::PARAM_INT);
        	                $updatePrefStmt->execute();
                	    } else {
                        	$telType = $type;
                    	   }
                          $phone = (string)$tel['Value'];
                     }
		}
           }  else {
                $phone = $tel;
           }
           if (!$telType) {
		$telType = "Phone";
           }

           // Remove dashes and spaces
           $phone = str_replace(['-', ' '], '', $phone);

           // Check if the phone number starts with '00'
           if (strpos($phone, '00') === 0) {
              // Replace '00' with '+'
              $phone = '+' . substr($phone, 2);
           } elseif (strpos($phone, '0') === 0) {
                    // Replace '0' with $phoneCode
                    $phone = $phoneCode . substr($phone, 1);
           }

           // Check if phone number already exists for this contact
           $existingPhoneNumberSql = "SELECT id FROM phone_numbers WHERE contact_id = :contact_id AND phone_number = :phone_number";
           $existingPhoneNumberStmt = $pdo->prepare($existingPhoneNumberSql);
           $existingPhoneNumberStmt->bindParam(':contact_id', $contactId, PDO::PARAM_INT);
           $existingPhoneNumberStmt->bindParam(':phone_number', $phone, PDO::PARAM_STR);
           $existingPhoneNumberStmt->execute();
           $existingPhoneNumberResult = $existingPhoneNumberStmt->fetch(PDO::FETCH_ASSOC);

           if (!$existingPhoneNumberResult) {
                   // Insert data into phone_numbers table
                    $phoneNumberSql = "INSERT INTO phone_numbers (contact_id, phone_type, phone_number, pref) VALUES (:contact_id, :phone_type, :phone_number, :pref)";
                    $phoneNumberStmt = $pdo->prepare($phoneNumberSql);
                    $phoneNumberStmt->bindParam(':contact_id', $contactId, PDO::PARAM_INT);
                    $phoneNumberStmt->bindParam(':phone_type', $telType, PDO::PARAM_STR);
                    $phoneNumberStmt->bindParam(':phone_number', $phone, PDO::PARAM_STR);
                    $phoneNumberStmt->bindParam(':pref', $pref, PDO::PARAM_INT);
                    $phoneNumberStmt->execute();

           } elseif ($pref) {
		    $phoneUpdateSql = "UPDATE phone_numbers SET pref = true WHERE id = :id";
                    $phoneUpdateStmt = $pdo->prepare($phoneUpdateSql);
                    $phoneUpdateStmt->bindParam(':id',$existingPhoneNumberResult['id'],PDO::PARAM_INT);
                    $phoneUpdateStmt->execute(); 
           }
        }


        foreach ($vcard->email as $email) {
//            print_r($email);
            $pref = false;
            $emailType = false;
            if (is_array($email)) {
	            if (!empty($email['Value'])) {
        	        foreach ($email['Type'] as $type) {
	                    if ($type == 'pref') {
        	                $pref = true;

                	        // If a preferred email has been found for this contact,
                        	$updatePrefSql = "UPDATE emails SET pref = false WHERE contact_id = :contact_id";
	                        $updatePrefStmt = $pdo->prepare($updatePrefSql);
        	                $updatePrefStmt->bindParam(':contact_id', $contactId, PDO::PARAM_INT);
                	        $updatePrefStmt->execute();
	                    } else {
        	                $emailType = $type;
                	    }
			}
	                $sEmail = (string)$email['Value'];
                }
           } else { 
               $sEmail = $email;
           }
           if (!$emailType) {
                $emailType = "email";
           } 

           // Check if email already exists for this contact
           $existingEmailSql = "SELECT id FROM emails WHERE contact_id = :contact_id AND email_address = :email";
           $existingEmailStmt = $pdo->prepare($existingEmailSql);
           $existingEmailStmt->bindParam(':contact_id', $contactId, PDO::PARAM_INT);
           $existingEmailStmt->bindParam(':email', $sEmail, PDO::PARAM_STR);
           $existingEmailStmt->execute();
           $existingEmailResult = $existingEmailStmt->fetch(PDO::FETCH_ASSOC);

           if (!$existingEmailResult) {
                   // Insert data into phone_numbers table
                    $EmailSql = "INSERT INTO emails (contact_id, email_type, email_address, pref) VALUES (:contact_id, :email_type, :email, :pref)";
                    $EmailStmt = $pdo->prepare($EmailSql);
                    $EmailStmt->bindParam(':contact_id', $contactId, PDO::PARAM_INT);
                    $EmailStmt->bindParam(':email_type', $emailType, PDO::PARAM_STR);
                    $EmailStmt->bindParam(':email', $sEmail, PDO::PARAM_STR);
                    $EmailStmt->bindParam(':pref', $pref, PDO::PARAM_INT);
                    $EmailStmt->execute();
           } else if ($pref) {
                    $updateSql = "UPDATE emails set pref = true WHERE id = :id";
                    $updateStmt = $pdo->prepare($updateSql);
                    $updateStmt->bindParam(':id', $existingEmailResult['id'], PDO::PARAM_INT);
                    $updateStmt->execute();
           }
        }
    }
}
?>
