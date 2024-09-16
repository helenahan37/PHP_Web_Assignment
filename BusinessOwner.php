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
    <h1 class="list-name">Business Owner Page</h1>

    <?php
    // Connect to the database using our function
    $dbh = connectToDatabase();

    // Prepare and execute the SQL statement to select orders and customer details
    $statement = $dbh->prepare('
   SELECT Products.ProductID, Products.Description, Products.Price, Brands.BrandName, 
           MAX(Orders.TimeStamp) AS RecentOrderTime, 
           MAX(Orders.OrderID) AS LatestOrderID,
           SUM(OrderProducts.Quantity) AS TotalQuantity, 
           SUM(OrderProducts.Quantity * Products.Price) AS TotalRevenue, 
           COUNT(OrderProducts.ProductID) AS Popularity  
    FROM Products 
    INNER JOIN OrderProducts ON Products.ProductID = OrderProducts.ProductID
    INNER JOIN Orders ON OrderProducts.OrderID = Orders.OrderID
    LEFT JOIN Brands ON Products.BrandID = Brands.BrandID
    GROUP BY Products.ProductID
    ORDER BY TotalRevenue DESC
');
    $statement->execute();

    // Fetch all orders data
    $products = $statement->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <!-- Display products and customer data in a table -->
    <table>
      <thead>
        <tr>
          <th>Latest Order Date</th>
          <th>Latest Order</th>
          <th>Product ID</th>
          <th>Description</th>
          <th>Price</th>
          <th>Brand Name</th>
          <th>Popularity</th>
          <th>Total Quantity</th>
          <th>Total Revenue</th>
          </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $product): ?>
          <?php
          $formattedTime = date("Y-m-d H:i:s", $product['RecentOrderTime']);
          ?>
          <tr>
          <tr>
            <td><?php echo htmlspecialchars($formattedTime); ?></td>
            <td><a href="ViewOrderDetails.php?OrderID=<?php echo urlencode($product['LatestOrderID']); ?>"><?php echo htmlspecialchars($product['LatestOrderID']); ?></a></td>
            <td><a href="ViewProduct.php?ProductID=<?php echo urlencode($product['ProductID']); ?>"><?php echo htmlspecialchars($product['ProductID']); ?></a></td>
            <td><?php echo htmlspecialchars($product['Description']); ?></td>
            <td><?php echo htmlspecialchars(number_format($product['Price'], 2)); ?></td>
            <td><?php echo htmlspecialchars($product['BrandName']); ?></td>
            <td><?php echo htmlspecialchars($product['Popularity']); ?></td>
            <td><?php echo htmlspecialchars($product['TotalQuantity']); ?></td>
            <td><?php echo htmlspecialchars(number_format($product['TotalRevenue'], 2)); ?></td>
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