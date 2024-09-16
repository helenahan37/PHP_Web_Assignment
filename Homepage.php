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

  <h1 class="welcome-words">Welcome to Helena's Shop</h1>
  <!-- display any cookie messages. TODO style this message so that it is noticeable. -->
  <?php
  if ($cookieMessage) {
    echo "<div class= 'cookie-message'>$cookieMessage</div>";
  }
  ?>
  <div class="search-box">
    <?php
    $searchString = $_GET['search'] ?? "";
    $safeSearchString = htmlspecialchars($searchString, ENT_QUOTES, "UTF-8");
    ?>
    <form method="GET" action="ProductList.php">
      <input class="search-input" name="search" type="text" placeholder="Explore some new..." value="<?php echo $safeSearchString; ?>" />
      <input class="search-button" type="submit" value="Search" />
    </form>
  </div>
  </div>
  <!-- // TODO the rest of this page is your choice, but you must not leave it blank. -->
  <div class="popular-item-container">
    <h1 class="popular-list">Top 10 Most Purchased Products</h1>
    <div id="popular-products-container">
      <?php
      $dbh = connectToDatabase();

      $statement = $dbh->prepare("SELECT Products.ProductID, Products.Description, Products.Price, COUNT(OrderProducts.OrderID) AS OrderCount
            FROM Products
            JOIN OrderProducts ON Products.ProductID = OrderProducts.ProductID
            GROUP BY Products.ProductID
            ORDER BY OrderCount DESC
            LIMIT 10;
            OFFSET ? * 10");
      $statement->execute();

      while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        echo "<div class='home-productBox'>";
        echo "<img src='./IFU_Assets/ProductPictures/{$row['ProductID']}.jpg' alt='Product Image'>";
        echo "<div class='home-description'>{$row['Description']}</div>";
        echo "<div class='home-price'>$ {$row['Price']}</div>";
        echo "<div class='order-count'>Ordered: {$row['OrderCount']} times</div>";
        echo "<a href='ViewProduct.php?ProductID={$row['ProductID']}' class='add-to-cart'>View Product Details</a>";
        echo "</div>";
      }
      ?>
    </div>
  </div>
</body>
<?php
include('footer.inc.php');
?>


</html>
