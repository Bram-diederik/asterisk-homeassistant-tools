#!/usr/bin/php
<?php

// Include the Nextcloud config file
include("/opt/sascha/settings.php");

// Create a PDO database connection
$pdo = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);

// Delete entries one by one in a loop
function deleteRecursive($ldapconn, $dn) {
    $entries = ldap_list($ldapconn, $dn, 'objectClass=*');
    if ($entries) { 
      $entries_info = ldap_get_entries($ldapconn, $entries);

      for ($i = 0; $i < $entries_info['count']; $i++) {
        $child_dn = $entries_info[$i]['dn'];
        deleteRecursive($ldapconn, $child_dn);
      }
    }
    if ($dn !== "ou=sascha,dc=asterisk,dc=pi-cloud,dc=gotdns,dc=ch") {
        ldap_delete($ldapconn, $dn);
    }
}


// Create a connection to the LDAP server
$ldapconn = ldap_connect($ldap_server);
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

// Debug: Output LDAP connection result
if (!$ldapconn) {
    echo "LDAP connection failed\n";
    exit;
} else {
    echo "LDAP connection successful\n";
}

$ldap_bind = ldap_bind($ldapconn, $ldap_username, $ldap_password);

// Check if binding was successful
if (!$ldap_bind) {
    echo "LDAP bind failed\n";
    exit;
} else {
    echo "LDAP bind successful\n";
}


// Purge the ou=sascha from any existing contacts
$ldap_purge_dn = "ou=sascha,dc=asterisk,dc=pi-cloud,dc=gotdns,dc=ch";



deleteRecursive($ldapconn, $ldap_purge_dn);



// Fetch data from MySQL phone_numbers table
$phone_numbers_query = "SELECT pn.id, c.full_name, pn.phone_type, pn.phone_number, pn.pref
                        FROM phone_numbers pn
                        JOIN contacts c ON pn.contact_id = c.id";
$phone_numbers_result = $pdo->query($phone_numbers_query);

// Iterate through phone numbers and add/update LDAP entries
foreach ($phone_numbers_result as $phone_number) {
    echo "Processing phone number: " . $phone_number['phone_number'] . " for contact: " . $phone_number['full_name'] . "\n";

    // Construct the DN of the entry in ou=sascha
    if ($phone_number['pref']) {
        $contact_dn = "cn=" . $phone_number['full_name'] . ' ' . $phone_number['phone_type'] . ' pref' . ",ou=sascha,dc=asterisk,dc=pi-cloud,dc=gotdns,dc=ch";
    } else {
       $key = $phone_number['full_name'] . ' ' . $phone_number['phone_type'];

      // Increment the count for the key
      $counts[$key] = isset($counts[$key]) ? $counts[$key] + 1 : 1;
      $i = $counts[$key] -1;
       if ($i == 0) 
       	$contact_dn = "cn=" . $phone_number['full_name'] . ' ' . $phone_number['phone_type'] . ",ou=sascha,dc=asterisk,dc=pi-cloud,dc=gotdns,dc=ch";
      else 
 	$contact_dn = "cn=" . $phone_number['full_name'] . ' ' . $phone_number['phone_type'] .$i. ",ou=sascha,dc=asterisk,dc=pi-cloud,dc=gotdns,dc=ch";
    }

    // Define the changes to be made
    $changes = [
        [
            'attrib' => 'telephoneNumber',
            'modtype' => LDAP_MODIFY_BATCH_ADD,
            'values' => [$phone_number['phone_number']], // use the same phone number for add/update
        ],
    ];
        // If modification failed, try adding the person
        $contact_attrs = [
            'objectClass' => ['top', 'person', 'organizationalPerson'],
            'cn' => $phone_number['full_name'],
            'sn' => explode(" ", $phone_number['full_name'])[count(explode(" ", $phone_number['full_name'])) - 1],
            'telephoneNumber' => [$phone_number['phone_number']],
        ];

        // Add the person to LDAP
        if (ldap_add($ldapconn, $contact_dn, $contact_attrs)) {
            echo "Person added successfully\n";
        } else {
            echo "Error adding/updating person: " . ldap_error($ldapconn) . "\n";
        }
}

// Close the database connection
$pdo = null;

// Close the LDAP connection
ldap_close($ldapconn);
?>
