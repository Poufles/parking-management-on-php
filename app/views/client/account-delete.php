<!-- Frankly could be done with edit -->

<!-- REMOVE THIS PART LATER -->
<!-- REMOVE THIS PART LATER -->
<!-- REMOVE THIS PART LATER -->
<?php 
// THIS IS TO SIMPLY SHOW AS EXAMPLE
$rows = AccountModel::getInstance()->searchAccounts('', '');
echo "<table border='1' cellpadding='5'>";
echo "<tr>
        <th>UID</th>
        <th>Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Phone</th>
      </tr>";

foreach ($rows['results']['rows'] as $row) {
    echo "<tr>";
    echo "<td>{$row['UID']}</td>";
    echo "<td>{$row['NAME']}</td>";
    echo "<td>{$row['USERNAME']}</td>";
    echo "<td>{$row['EMAIL_ADDRESS']}</td>";
    echo "<td>{$row['GENDER']}</td>";
    echo "<td>{$row['phone']}</td>";
    echo "</tr>";
}

echo "</table>";
?>
<!-- REMOVE THIS PART LATER -->
<!-- REMOVE THIS PART LATER -->
<!-- REMOVE THIS PART LATER -->
<h1>
    Account Delete
</h1>
<form action="<?= APP_URL . "client/account/delete" ?>" method="POST">
    <label for="" class="field">
        <span>UID: </span>
        <input type="text" name="uid" id="">
    </label>
    <button type="submit" name="delete">Delete</button>
</form>