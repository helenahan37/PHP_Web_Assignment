<?php
include('functions.php');
include('header.inc.php');
include('navbar.inc.php');

$cookieMessage = getCookieMessage();
?>

<!doctype html>
<html>

<body>

  <!-- Sign-up container with form and image -->
  <div class="signup-container">
    <!-- Left: Form -->
    <div class="form-container">
      <h1>Create Account</h1>
      <?php
      if ($cookieMessage) {
        echo "<div class= 'cookie-message'>$cookieMessage</div>";
      }
      ?>
      <p>Enter your personal details and start a journey with us</p>

      <!-- Sign-up form -->
      <form id="sign-up-form" action="AddNewCustomer.php" method="POST">
        <!-- Personal Information -->
        <fieldset>
          <legend>Personal Information</legend>
          <div class="form-group">
            <label for="user-name">User Name</label>
            <input type="text" id="name" name="UserName" placeholder="Enter your user name" required />
          </div>
          <div class="form-group">
            <label for="first-name">First Name</label>
            <input type="text" id="first-name" name="FirstName" placeholder="Enter your first name" required />
          </div>
          <div class="form-group">
            <label for="last-name">Last Name</label>
            <input type="text" id="last-name" name="LastName" placeholder="Enter your last name" required />
          </div>
        </fieldset>

        <!-- Address Information -->
        <fieldset>
          <legend>Address Information</legend>
          <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="Address" placeholder="Enter your address" required />
          </div>
          <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="City" placeholder="Enter your city" required />
          </div>
        </fieldset>

        <button class="control-button" type="submit">Sign Up</button>
      </form>
    </div>

    <!-- Right: Image -->
    <div class="image-container">
      <img src="./Assets/sign-up.png" alt="Sign Up Image" />
    </div>
  </div>
</body>
<?php
include('footer.inc.php');
?>

</html>
