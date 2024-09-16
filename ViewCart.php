<?php // <--- do NOT put anything before this PHP tag

include('functions.php');
include('header.inc.php');
$cookieMessage = getCookieMessage();

?>
<!doctype html>

<html>


<body>
  <?php
  include('navbar.inc.php');
  ?>
  <div class="cart-container">

    <?php

    // does the user have items in the shopping cart?
    if (isset($_COOKIE['ShoppingCart']) && $_COOKIE['ShoppingCart'] != '') {
      // the shopping cart cookie contains a list of productIDs separated by commas
      // we need to split this string into an array by exploding it.
      $productID_list = explode(",", $_COOKIE['ShoppingCart']);

      // remove any duplicate items from the cart. although this should never happen we 
      // must make absolutely sure because if we don't we might get a primary key violation.
      $productID_list = array_unique($productID_list);

      $dbh = connectToDatabase();

      // create a SQL statement to select the product and brand info about a given ProductID
      // this SQL statement will be very similar to the one in ViewProduct.php
      $statement = $dbh->prepare('
			
			--TODO PUT YOUR SQL HERE--
      SELECT * FROM Products INNER JOIN Brands ON Brands.BrandID = Products.BrandID WHERE Products.ProductID = ?
			
		');

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // check if the user has clicked the remove button
        if (isset($_POST['action']) && $_POST['action'] === 'removeItem') {
          $productID = $_POST['ProductID'];
          removeItemFromCart($productID);
          redirect("ViewCart.php");
        }
      }

      $totalPrice = 0;
      echo '<div class="products-section">';

      // loop over the productIDs that were in the shopping cart.
      foreach ($productID_list as $productID) {
        // great thing about prepared statements is that we can use them multiple times.
        // bind the first question mark to the productID in the shopping cart.
        $statement->bindValue(1, $productID);
        $statement->execute();

        // did we find a match?
        if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
          $Price = htmlspecialchars($row['Price'], ENT_QUOTES, 'UTF-8');
          $Description = htmlspecialchars($row['Description'], ENT_QUOTES, 'UTF-8');
          $BrandName = htmlspecialchars($row['BrandName'], ENT_QUOTES, 'UTF-8');
          $BrandID = htmlspecialchars($row['BrandID'], ENT_QUOTES, 'UTF-8');
          $ProductID = htmlspecialchars($row['ProductID'], ENT_QUOTES, 'UTF-8');

          echo "<div class='cart-product-info'>";
          echo "<div class='cart-brand-info'><img src='./IFU_Assets/BrandPictures/$BrandID.jpg' alt='Brand Logo' class='cart-brand-logo'>";
          echo "<span class='cart-brand-name'> Brand Name: $BrandName</span></div>";
          echo "<div class='cart-description'>$Description</div>";
          echo "<img src='./IFU_Assets/ProductPictures/$ProductID.jpg' alt='Product Image' class='cart-product-image'>";
          echo "<span class='cart-price'>Price: $$Price</span>";
          echo "</div>"; // Closing tags for product-info

          //delete button for each product
          echo "<form method='POST' action='ViewCart.php' style='display:inline;'>";
          echo "<input type='hidden' name='action' value='removeItem'>";
          echo "<input type='hidden' name='ProductID' value='$ProductID'>";
          echo "<button class='remove-button' type='submit'>Remove</button>";
          echo "</form>";
          $totalPrice += $Price;
        }
      }
      echo "</div>";
      echo '<div class="checkout-section">';

      // TODO: output the $totalPrice.
      echo "<span class='total-price'>Total Price: $$totalPrice</span>";
      // if we have any error messages echo them now. TODO style this message so that it is noticeable.  <?php

    ?>
      <div class="order-container">
        <form action='ProcessOrder.php' method='POST'>
          <label class="username-label" for="UserName">Process your Order!</label>
          <?php if ($cookieMessage) {
            echo "<div class='cookie-message'>$cookieMessage</div>";
          } ?>
          <input class="user-input" type="text" id="UserName" name="UserName" required placeholder="Enter your username">
          <button class="confirm-button" type="submit">Confirm Order</button>
        </form>
      </div>

      <form action='EmptyCart.php' method='POST'>
        <button class="empty-cart-button" type="submit" name="EmptyCart" id="EmptyCart">Empty Shopping Cart</button>
      </form>
  </div>
  </div>
  </div>

<?php
    } else {
      // if we have any error messages echo them now. TODO style this message so that it is noticeable.
      echo "$cookieMessage <br/>";

      echo "You Have no items in your cart!";
    }
?>
</div>


</body>
<?php
include('footer.inc.php');
?>

</html>