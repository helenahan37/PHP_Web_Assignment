<!doctype html>
<html>

<?php
include('functions.php');
include('header.inc.php');
include('navbar.inc.php');
$cookieMessage = getCookieMessage();
?>

<body>

  <div class="product-details">
    <h1 class="list-name">Product Detail</h1>

    <?php
    if (isset($_GET['ProductID'])) {
      $productIDURL = $_GET['ProductID'];
      $dbh = connectToDatabase();
      $statement = $dbh->prepare('SELECT * FROM Products INNER JOIN Brands ON Brands.BrandID = Products.BrandID WHERE Products.ProductID = ?');
      $statement->bindValue(1, $productIDURL);
      $statement->execute();

      if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $Price = htmlspecialchars($row['Price'], ENT_QUOTES, 'UTF-8');
        $Description = htmlspecialchars($row['Description'], ENT_QUOTES, 'UTF-8');
        $BrandName = htmlspecialchars($row['BrandName'], ENT_QUOTES, 'UTF-8');
        $BrandID = htmlspecialchars($row['BrandID'], ENT_QUOTES, 'UTF-8');
        $Website = htmlspecialchars($row['Website'], ENT_QUOTES, 'UTF-8');

        echo "<div class='product-info'>";
        echo "<div class='brand-info'><img src='./IFU_Assets/BrandPictures/$BrandID.jpg' alt='Brand Logo' class='brand-logo'/>";
        echo "<span class='brand-name'>Brand Name: <a href='$Website' target='_blank'>$BrandName</a></span></div>";
        echo "<div class='description'>$Description</div>";
        echo "<img src='./IFU_Assets/ProductPictures/$productIDURL.jpg' alt='Product Image' class='product-image'/>";
        echo "<div class='purchase-info'>";
        echo "<span class='price'>Price: $$Price</span>";
        if (!DoesCartContainProduct($productIDURL)) {
          echo "<form action='AddToCart.php?ProductID=$productIDURL' method='POST'>";
          echo "<input type='submit' name='BuyButton' value='Add To Cart' class='add-to-cart'>";
          echo "</form>";
        } else {
          echo "<a href='ProductList.php' class='alreay-exist-button'>Product already in cart</a>";
        }

        echo "</div>";
      } else {
        echo "<p>Unknown Product ID</p>";
      }
    } else {
      echo "<p>No ProductID provided!</p>";
    }
    ?>
  </div>
</body>
<?php
include('footer.inc.php');
?>

</html>