<!doctype html>
<html>

<?php
include('functions.php');
include('header.inc.php');
$cookieMessage = getCookieMessage();
?>

<body>

  <?php include('navbar.inc.php');
  ?>

  <div id="customer-container">
    <h1 class="list-name">Customer List</h1>

    <?php
    // Connect to the database using our function
    $dbh = connectToDatabase();

    // Prepare and execute the SQL statement to select customer details
    $statement = $dbh->prepare('SELECT Username, FirstName, LastName, Address, City FROM Customers');
    $statement->execute();

    // Fetch all customer data
    $customers = $statement->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!-- Display customer data in a table -->
    <table>
      <thead>
        <tr>
          <th>User Name</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Address</th>
          <th>City</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($customers as $customer): ?>
          <tr>
            <td><?php echo htmlspecialchars($customer['UserName']); ?></td>
            <td><?php echo htmlspecialchars($customer['FirstName']); ?></td>
            <td><?php echo htmlspecialchars($customer['LastName']); ?></td>
            <td><?php echo htmlspecialchars($customer['Address']); ?></td>
            <td><?php echo htmlspecialchars($customer['City']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
<?php
include('footer.inc.php');
?>

</html>