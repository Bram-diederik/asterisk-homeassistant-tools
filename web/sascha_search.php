<?php
$dir = "/opt/vcf2asterisk/";
$key = '123456';
if (@$_GET['key'] != $key) {

die();

}
$dbservername = "192.168.5.1";
$dbusername = "sascha";
$dbpassword = "password";
$dbname = "asterisk";


$clientIP = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

if (isset($_GET['search'])) {
    $search = $_GET['search'];

    $pdo = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL query to search for the name by phone number
    $sql = "SELECT contacts.full_name,contacts.category,phone_numbers.phone_number,phone_numbers.pref,phone_numbers.phone_type  
        FROM phone_numbers
        JOIN contacts ON phone_numbers.contact_id = contacts.id 
        WHERE full_name LIKE :name";
    $stmt = $pdo->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->bindParam(':name', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch()) {
        $name = $row['full_name'];
        $phone = $row['phone_number'];
        $pref = $row['pref'];
        $type = $row['phone_type'];
        $contacts[] = ['name' => $name, 'phone' => $phone,'pref' => $pref,'type' => $type];
    }

}
?>

<!DOCTYPE html>
<html>
<head>
  <style>
        body {
         background-color: white;
        }
        .light-blue-row {
            background-color: #e0e0ff; /* Light blue color */
        }
    </style>
</head>
<body>
<?php 
?>
    <form action="" method="GET">
        <label for="search">Search by Name:</label>
        <input type="text" name="search" id="search" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
        <input type="hidden" name="key" id="key" value="<?php echo isset($key) ? htmlspecialchars($key) : ''; ?>">
        <input type="submit"  value="Search">
    </form>

    <?php if (isset($contacts) && !empty($contacts)) { ?>
        <h2>Contact Search Results</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Phone</th>
            </tr>
            <?php foreach ($contacts as $contact) { ?>
                <tr <?php if ($contact['pref'] == 1) { echo 'class="light-blue-row"'; } ?>>
                    <td><?php echo htmlspecialchars($contact['name']); ?></td>
                    <td><?php echo htmlspecialchars($contact['type']); ?></td>
                    <td><a href="tel:<?php echo htmlspecialchars($contact['phone']); ?>"><?php echo htmlspecialchars($contact['phone']); ?></a></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
</body>
</html>
