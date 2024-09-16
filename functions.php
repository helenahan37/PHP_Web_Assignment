<?php
// in PHP we can create our own functions to do whatever we need.
// the benefit of using a function is that we can reduce duplicate code.

// here is a function that will connect the Database
// wherever we need to connect to the database we just call this function.
function connectToDatabase()
{
  // connect to our SQLITE database 
  $dbh = new PDO("sqlite:./database/OnlineShop.db");

  // if you had a MYSQL server you could use this instead:
  // $dbh = new PDO("mysql:host=localhost;dbname=myDatabase", "username", "password");

  // enable errors
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  //Turn OFF emulated prepared statements.
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  // return the database handle.
  return $dbh;
}

function showErrorMessage($errorMessage)
{
  echo "<h4 class = 'errormsg'>Error: $errorMessage </h4>";
}

// run this function on untrusted data before we echo it on the page.
function makeOutputSafe($unsafeString)
{
  $safeOutput = htmlspecialchars($unsafeString, ENT_QUOTES, "UTF-8");
  return $safeOutput;
}

// this function lets you redirect the user to a different web page.
function redirect($newURL)
{
  // the header location function will send a user to the specified URL.
  // please note that this function MUST be called before any HTML is displayed on the page or it wont work.
  header("Location: $newURL");

  // we just redirected the user, that means there is no one around to view this page.
  // so we can just stop processing this page.
  die();
}

// please note that this function MUST be called before any HTML is displayed on the page or it wont work.
function setCookieMessage($errorMessage)
{
  setcookie("ErrorMessage", $errorMessage);
}

// please note that this function MUST be called before any HTML is displayed on the page or it wont work.
function getCookieMessage()
{
  if (isset($_COOKIE['ErrorMessage'])) {
    $message = $_COOKIE['ErrorMessage'];
    deleteCookie("ErrorMessage");
    return makeOutputSafe($message);
  } else return "";
}

// please note that this function MUST be called before any HTML is displayed on the page or it wont work.
function deleteCookie($cookieName)
{
  // to delete a cookie, you set the expiry date to a date in the past.
  // in this case set the expiry date to 1 second past midnight 1st of Jan 1970
  setcookie($cookieName, "", 1);
}

// this function will return true if $needle is found inside $haystack.
function stringContains($haystack, $needle)
{
  return strpos($haystack, $needle) !== false;
}


function DoesCartContainProduct($ProductID)
{
  if (isset($_COOKIE['ShoppingCart'])) {
    $productIDs = explode(",", $_COOKIE['ShoppingCart']);
    return in_array($ProductID, $productIDs);
  }
  return false;
}

//check if user input is valid
function isValidLength($input, $maxLength)
{
  return strlen($input) <= $maxLength;
}


function removeItemFromCart($productID)
{
  if (isset($_COOKIE['ShoppingCart']) && $_COOKIE['ShoppingCart'] != '') {
    // get all items in cart
    $cartItems = explode(",", $_COOKIE['ShoppingCart']);

    //delete specific item from cart
    $updateCarts = array_filter($cartItems, function ($item) use ($productID) {
      return $item !== $productID;
    });
    //reset the cookie
    setcookie("ShoppingCart", implode(",", $updateCarts), time() + 3600, "/");
  } else {
    setCookieMessage("Your cart is already empty.");
  }
}

//delete order function
function deleteOrder($orderID)
{
  try {
    $dbh = connectToDatabase();
    $dbh->beginTransaction();

    // delete order from OrderProducts
    $statement = $dbh->prepare('DELETE FROM OrderProducts WHERE OrderID = ?');
    $statement->bindValue(1, $orderID);
    $statement->execute();

    // delete order from Orders
    $statement = $dbh->prepare('DELETE FROM Orders WHERE OrderID = ?');
    $statement->bindValue(1, $orderID);
    $statement->execute();

    $dbh->commit();
  } catch (Exception $e) {
    $dbh->rollBack();
    setCookieMessage("Failed to delete order: " . $e->getMessage());
  }
}
