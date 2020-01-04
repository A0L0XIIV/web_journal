<?php 
    require "header.php";
?>

<?php 
  // define variables and set to empty values
  $nameError = $pwError = $updateError = false;
  $successUpdate = false;
  $errorText = "";
  $name = "";
  $password = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Security operations
    $name = $_SESSION['name'];
    $password = test_input($_POST["password"]);

    // Empty check
    if(empty($password)){
      $pwError = true;
    }
    else{
      // Database connection
      require "./mysqli_connect.php";
      // Save journal into DB
      $sql = "UPDATE user SET password=? WHERE name=?";
      $stmt = mysqli_stmt_init($conn);
      if(!mysqli_stmt_prepare($stmt, $sql)){
          $error = true;
      }
      else{
          // Hash the password
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
          // Bind inputs to query parameters
          mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $name);
          // Execute sql statement
          if(mysqli_stmt_execute($stmt))
              $successUpdate = true;
          else{
              $updateError = true;
              $errorText = mysqli_error($conn);
          }
      }
    }
  }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

?>

   <!-- Centered main-->
   <main class="main">

      <form
        name="login-form"
        id="login-form"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        method="post"
      >
    
        <h1>
          Şifreni buradan değiştirebilirsin
          <?php
            if(isset($_SESSION['name'])){
                echo ' '.$_SESSION['name'];
            }
          ?>
        </h1>

        <!--Input for user password, type=password-->
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text" id="pw-label">Şifre</span>
          </div>
          <input
            type="password"
            name="password"
            id="password-input"
            title="Max uzunluk 50."
            maxlength="50"
            minlength="3"
            placeholder="..."
            required
          />
          <p id="passwordError" class="error"><?php if($pwError) {echo "Şifre boş olamaz!";}?></p>
        </div>

        <!--Password update error-->
        <div>
            <p id="authError" class="error"><?php if($updateError) {echo "Şifre değiştirme başarısız!";}?></p>
            <p id="successAuth" class="success"><?php if($successUpdate) {echo "Şifre değiştirme başarılı! ".$errorText;}?></p>
        </div>

        <!--Input for submitting the form, type=submit-->
        <div>
          <input
            type="submit"
            value="Gönder"
            name="pw-update-submit"
            class="btn btn-primary"
            aria-pressed="false"
          />
        </div>
      </form>
      <br /><br />
    </main>

<?php
    require "footer.php";
?>