<!doctype html>
<html>

<?php
include('functions.php');
include('header.inc.php');
$cookieMessage = getCookieMessage();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteOrder') {
  $orderID = $_POST['OrderID'];
  var_dump($orderID);
  deleteOrder($orderID);
  header("Location: OrderList.php");
  exit;
}
?>

<body>

  <?php include('navbar.inc.php');
  ?>

  <div id="customer-container">
    <h1 class="list-name">Order List</h1>

    <?php
    // Connect to the database using our function
    $dbh = connectToDatabase();

    // Prepare and execute the SQL statement to select orders and customer details
    $statement = $dbh->prepare('SELECT Orders.OrderID AS "OrderID", Orders.TimeStamp AS "OrderDate", 
                            Customers.CustomerID AS "CustomerID", Customers.UserName AS "UserName", 
                            Customers.FirstName AS "FirstName", Customers.LastName AS "LastName", 
                            Customers.Address AS "Address", Customers.City AS "City"
                            FROM Orders 
                            INNER JOIN Customers ON Customers.CustomerID = Orders.CustomerID');
    $statement->execute();

    // Fetch all orders data
    $orders = $statement->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <!-- Display order and customer data in a table -->
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Order Date</th>
          <th>Customer ID</th>
          <th>User Name</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Address</th>
          <th>City</th>
          <th>Action</th>
          </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order):
          // Format the date from timestamp
          $formattedTime = date("Y-m-d H:i:s", $order['OrderDate']);
        ?>
          <tr>
            <!-- orderid as a hyper link to vieworder.php -->
            <td><a href="ViewOrderDetails.php?OrderID=<?php echo urlencode($order['OrderID']); ?>"><?php echo htmlspecialchars($order['OrderID']); ?></a></td>
            <td><?php echo htmlspecialchars($formattedTime); ?></td>
            <td><?php echo htmlspecialchars($order['CustomerID']); ?></td>
            <td><?php echo htmlspecialchars($order['UserName']); ?></td>
            <td><?php echo htmlspecialchars($order['FirstName']); ?></td>
            <td><?php echo htmlspecialchars($order['LastName']); ?></td>
            <td><?php echo htmlspecialchars($order['Address']); ?></td>
            <td><?php echo htmlspecialchars($order['City']); ?></td>
            <td>
              <form method="POST" action="OrderList.php" onsubmit="return confirm('Are you sure you want to delete this order?');">
                <input type="hidden" name="action" value="deleteOrder">
                <input type="hidden" name="OrderID" value="<?php echo htmlspecialchars($order['OrderID']); ?>">
                <button class='order-delete-button' type="submit">Delete</button>
              </form>
            </td>
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