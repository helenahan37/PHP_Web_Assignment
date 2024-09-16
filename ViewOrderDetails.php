<!DOCTYPE HTML>
<html>
<?php // <--- do NOT put anything before this PHP tag
include('header.inc.php');
include('navbar.inc.php');
?>

<body>
  <div class=order-list-container>
    <?php

    // did the user provided an OrderID via the URL?
    if (isset($_GET['OrderID'])) {
      $UnsafeOrderID = $_GET['OrderID'];

      include('functions.php');
      $dbh = connectToDatabase();

      // select the order details and customer details. (you need to use an INNER JOIN)
      // but only show the row WHERE the OrderID is equal to $UnsafeOrderID.
      $statement = $dbh->prepare('
		SELECT * 
		FROM Orders 
		INNER JOIN Customers ON Customers.CustomerID = Orders.CustomerID 
		WHERE OrderID = ? ; 
	');
      $statement->bindValue(1, $UnsafeOrderID);
      $statement->execute();

      // did we get any results?
      if ($row1 = $statement->fetch(PDO::FETCH_ASSOC)) {
        // Output the Order Details.
        $FirstName = makeOutputSafe($row1['FirstName']);
        $LastName = makeOutputSafe($row1['LastName']);
        $OrderID = makeOutputSafe($row1['OrderID']);
        $UserName = makeOutputSafe($row1['UserName']);
        $Address = makeOutputSafe($row1['Address']);
        $City = makeOutputSafe($row1['City']);
        $Time = makeOutputSafe($row1['TimeStamp']);
        $formattedTime = date("Y-m-d H:i:s", $Time);


        // display the OrderID
        echo "<h2>OrderID: $OrderID</h2>";

        // its up to you how the data is displayed on the page. I have used a table as an example.
        // the first two are done for you.
        echo "<table>";
        echo "<tr><th>UserName:</th><td>$UserName</td></tr>";
        echo "<tr><th>Customer Name:</th><td>$FirstName $LastName </td></tr>";

        //TODO show the Customers Address and City.
        echo "<tr><th>Address:</th><td>$Address</td></tr>";
        echo "<tr><th>City:</th><td>$City</td></tr>";
        //TODO show the date and time of the order.
        echo "<tr><th>Date and Time:</th><td>$formattedTime</td></tr>";

        echo "</table>";

        // TODO: select all the products that are in this order (you need to use INNER JOIN)
        // this will involve three tables: OrderProducts, Products and Brands.
        $statement2 = $dbh->prepare('
			
			--TODO PUT YOUR SQL CODE HERE--
			SELECT * FROM Products 
		  INNER JOIN OrderProducts ON OrderProducts.ProductID = Products.ProductID 
      INNER JOIN Brands ON Brands.BrandID = Products.BrandID
		  WHERE OrderID = ? ; 
		');
        $statement2->bindValue(1, $UnsafeOrderID);
        $statement2->execute();

        $totalPrice = 0;
        echo "<h2>Order Details:</h2>";

        // loop over the products in this order. 
        while ($row2 = $statement2->fetch(PDO::FETCH_ASSOC)) {
          //NOTE: pay close attention to the variable names.
          $ProductID = makeOutputSafe($row2['ProductID']);
          $Description = makeOutputSafe($row2['Description']);
          $Price = makeOutputSafe($row2['Price']);
          $BrandID = htmlspecialchars($row2['BrandID'], ENT_QUOTES, 'UTF-8');
          $BrandName = htmlspecialchars($row2['BrandName'], ENT_QUOTES, 'UTF-8');
          $Quantity = makeOutputSafe($row2['Quantity']);
          $totalPrice += $Price * $Quantity;



          // TODO show the Products Description, Brand, Price, Picture of the Product and a picture of the Brand.
          echo "<table>";
          // Table header row for product details
          echo "<tr><th>Brand Logo</th><th>Brand Name</th><th>Product Image</th><th>Description</th><th>Price</th><th>Quantity</th></tr>";

          // Table row for product details
          echo "<tr>";
          echo "<td><img src='./IFU_Assets/BrandPictures/$BrandID.jpg' alt='Brand Logo' class='cart-brand-logo'></td>";

          echo "<td>$BrandName</td>";
          // TODO The product Picture must also be a link to ViewProduct.php.

          echo "<td><a href='ViewProduct.php?ProductID=$ProductID'><img src='./IFU_Assets/ProductPictures/$ProductID.jpg' alt='Product Image' class='cart-product-image'></td>";
          echo "<td>$Description</td>";
          echo "<td>$$Price</td>";
          echo "<td>$Quantity</td>";
          echo "</tr>";
          echo "</table>";
        }
        // TODO add the price to the $totalPrice variable.
        echo "<span class='total-price'>Total Price: $$totalPrice</span>";
        //TODO display the $totalPrice .
      } else {
        echo "System Error: OrderID not found";
      }
    } else {
      echo "System Error: OrderID was not provided";
    }
    ?>
  </div>
</body>
<?php
include('footer.inc.php');
?>

</html>