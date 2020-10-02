<?php 
    require "header.php";
?>

<?php 
  // define variables and set to empty values
  $nameError = $pwError = $authError = false;
  $successAuth = false;
  $name = "";
  $password = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Security operations
    $name = test_input($_POST["name"]);
    $password = test_input($_POST["password"]);

    // Empty check
    if(empty($name)){
      $nameError = true;
    } 
    else if(empty($password)){
      $pwError = true;
    }
    else{
      if(basic_auth($name, $password)){
        $_SESSION['name'] = $name;
        $successAuth = true;
        echo("<script>location.href = './write.php';</script>");
        exit();
      }
      else{
        $authError = true;
      }
    }
  }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function basic_auth($name, $password){
    // Database connection
    require "./mysqli_connect.php";
    // Save journal into DB
    $sql = "SELECT password FROM user WHERE name=?";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        $error = true;
    }
    else{
        // Bind inputs to query parameters
        mysqli_stmt_bind_param($stmt, "s", $name);
        // Execute sql statement
        if(mysqli_stmt_execute($stmt))
          // Bind result variables
          mysqli_stmt_bind_result($stmt, $dbPassword);
          // Store results
          if(mysqli_stmt_store_result($stmt)){
            // Check DB if user exist 
            if(mysqli_stmt_num_rows($stmt) > 0){
                // User exist
                while (mysqli_stmt_fetch($stmt)) {
                    // Password control
                    $passwordCheck = password_verify($password, $dbPassword);
                    // Wrong password --> Auth error
                    if($passwordCheck == false){
                      return false;
                    }
                    // Correct password --> redirect to index page
                    else{
                      return true;
                    }
                }
            }
        }
        else{
            return false;
            $errorText = mysqli_error($conn);
        }
    }
  }
?>

<!-- Centered main-->
<main class="main" style="height: 90vh;">
  <div class="mx-auto py-3">
    <form
      name="login-form"
      id="login-form"
      action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
      method="post"
    >
      <!--Input for name, type=text-->
      <div class="input-group mb-3 justify-content-center">
        <div class="input-group-prepend">
          <span class="input-group-text" id="name-label">İsim</span>
        </div>
        <input
          type="text"
          name="name"
          id="name-input"
          title="Max uzunluk 50."
          maxlength="50"
          placeholder="..."
          required
        />
      </div>
      <?php if($nameError) {echo '<p id="usernameError" class="error">İsim boş olamaz!</p>';}?>

      <!--Input for user password, type=password-->
      <div class="input-group mb-3 justify-content-center">
        <div class="input-group-prepend">
          <span class="input-group-text" id="pw-label">Şifre</span>
        </div>
        <input
          type="password"
          name="password"
          id="password-input"
          title="Max uzunluk 50."
          maxlength="50"
          placeholder="..."
          required
        />
      </div>
     <?php if($pwError) {echo ' <p id="passwordError" class="error">Şifre boş olamaz!</p>';}?>

      <!--Login error-->
      <div>
          <?php if($authError) {echo '<p id="authError" class="error">Hatalı giriş!</p>';}?>
          <?php if($successAuth) {echo '<p id="successAuth" class="success">Giriş başarılı!</p>';}?>
      </div>

      <!--Input for submitting the form, type=submit-->
      <div>
        <input
          type="submit"
          value="Gönder"
          name="login-submit"
          class="btn btn-primary bg-dark"
          aria-pressed="false"
        />
      </div>
    </form>
  </div>
</main>

<?php
    require "footer.php";
?>