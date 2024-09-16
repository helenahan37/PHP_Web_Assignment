<!doctype html>
<html>

<?php
include('functions.php');
include('header.inc.php');

$cookieMessage = getCookieMessage();
?>

<body>
  <?php
  include('navbar.inc.php');
  ?>

  <div id="productlist-container">
    <h1 class="list-name">Product List</h1>
    <?php
    if (isset($_GET['search'])) {
      $searchString = $_GET['search'];
    } else {
      $searchString = "";
    }

    $safeSearchString = htmlspecialchars($searchString, ENT_QUOTES, "UTF-8");
    $SqlSearchString = "%$safeSearchString%";
    ?>
    <!-- search box -->
    <div class="product-search-box">
      <form method="GET" action="ProductList.php" class="search-form">
        <!-- search input -->
        <div class="search-field">
          <input class="product-search-input" name="search" type="text" value="<?php echo $safeSearchString; ?>" />
        </div>

        <!-- dropdown list -->
        <div class="sort-field">
          <select name="sort" class="sort-dropdown">
            <option value="popularity">Popularity</option>
            <option value="name_asc">Brand Name: A to Z</option>
            <option value="name_desc">Brand Name: Z to A</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
          </select>
        </div>

        <!-- submit button -->
        <div class="submit-field">
          <input class="product-search-button" type="submit" value="Search" />
        </div>
      </form>
    </div>

    <?php
    // sort function
    if (isset($_GET['sort'])) {
      $sort = $_GET['sort'];
    } else {
      $sort = "popularity";
    }

    // swith case for sort
    switch ($sort) {
      case "name_asc":
        $sqlsort = "
            SELECT Products.ProductID, Products.Price, Products.Description, Brands.BrandName
            FROM Products
            LEFT JOIN Brands ON Brands.BrandID = Products.BrandID
            LEFT JOIN OrderProducts ON OrderProducts.ProductID = Products.ProductID
            WHERE Products.Description LIKE ?
            GROUP BY Products.ProductID, Brands.BrandName, Products.Price, Products.Description
            ORDER BY Brands.BrandName ASC
            LIMIT 10 OFFSET ?
        ";
        break;
      case "name_desc":
        $sqlsort = "
            SELECT Products.ProductID, Products.Price, Products.Description, Brands.BrandName
            FROM Products
            LEFT JOIN Brands ON Brands.BrandID = Products.BrandID
            LEFT JOIN OrderProducts ON OrderProducts.ProductID = Products.ProductID
            WHERE Products.Description LIKE ?
            GROUP BY Products.ProductID, Brands.BrandName, Products.Price, Products.Description
            ORDER BY Brands.BrandName DESC
            LIMIT 10 OFFSET ?
        ";
        break;
      case "price_asc":
        $sqlsort = "
            SELECT Products.ProductID, Products.Price, Products.Description, Brands.BrandName
            FROM Products
            LEFT JOIN Brands ON Brands.BrandID = Products.BrandID
            LEFT JOIN OrderProducts ON OrderProducts.ProductID = Products.ProductID
            WHERE Products.Description LIKE ?
            GROUP BY Products.ProductID, Brands.BrandName, Products.Price, Products.Description
            ORDER BY Products.Price ASC
            LIMIT 10 OFFSET ?
        ";
        break;
      case "price_desc":
        $sqlsort = "
            SELECT Products.ProductID, Products.Price, Products.Description, Brands.BrandName
            FROM Products
            LEFT JOIN Brands ON Brands.BrandID = Products.BrandID
            LEFT JOIN OrderProducts ON OrderProducts.ProductID = Products.ProductID
            WHERE Products.Description LIKE ?
            GROUP BY Products.ProductID, Brands.BrandName, Products.Price, Products.Description
            ORDER BY Products.Price DESC
            LIMIT 10 OFFSET ?
        ";
        break;
      default:
        $sqlsort = "
            SELECT Products.ProductID, Products.Price, Products.Description, Brands.BrandName
            FROM Products
            LEFT JOIN Brands ON Brands.BrandID = Products.BrandID
            LEFT JOIN OrderProducts ON OrderProducts.ProductID = Products.ProductID
            WHERE Products.Description LIKE ?
            GROUP BY Products.ProductID, Brands.BrandName, Products.Price, Products.Description
            ORDER BY COUNT(OrderProducts.OrderID) DESC
            LIMIT 10 OFFSET ?
        ";
        break;
    }


    if (isset($_GET['page'])) {
      $currentPage = intval($_GET['page']);
    } else {
      $currentPage = 0;
    }

    // connect to the database using our function (and enable errors, etc)
    $dbh = connectToDatabase();

    $statement = $dbh->prepare($sqlsort);
    $statement->bindValue(1, $SqlSearchString);
    $statement->bindValue(2, $currentPage);
    //execute the SQL.
    $statement->execute();

    // get the results
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      // Remember that the data in the database could be untrusted data. 
      // so we need to escape the data to make sure its free of evil XSS code.
      $ProductID = htmlspecialchars($row['ProductID'], ENT_QUOTES, 'UTF-8');
      $Price = htmlspecialchars($row['Price'], ENT_QUOTES, 'UTF-8');
      $Description = htmlspecialchars($row['Description'], ENT_QUOTES, 'UTF-8');
      $BrandName = htmlspecialchars($row['BrandName'], ENT_QUOTES, 'UTF-8');
      echo "<div class='productBox'>";
      echo "<img src='./IFU_Assets/ProductPictures/$ProductID.jpg' alt='Product Image' />";
      echo "<div class = 'products-des'>";
      echo "<div class='description'>$Description</div>";
      echo "<div class='brand-name'>Brand: $BrandName</div>";
      echo "</div>";
      echo "<div class = 'products-add'>";
      echo "<div class='price'>$$Price</div>";
      echo "</div>";
      echo "<form action='AddToCart.php' method='POST'>";
      echo "<a href='ViewProduct.php?ProductID=$ProductID' class='add-to-cart'>View Product Details</a>";
      echo "</div> \n";
    }  ?>
    <div class="page-buttons-container">
      <?php
      echo "<br />";
      $previousPage = $currentPage - 1;
      if ($previousPage >= 0) {
        echo "<a href='ProductList.php?page=$previousPage&search=$safeSearchString' class='page-button'>Previous Page</a><br />";
      }
      $nextPage = $currentPage + 1;
      echo "<a href='ProductList.php?page=$nextPage&search=$safeSearchString' class='page-button'>Next Page</a>"; //Task 8B 
      ?>
    </div>
  </div>
</body>
<?php
include('footer.inc.php');
?>


</html>