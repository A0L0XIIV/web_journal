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
   <main class="main">

      <form
        name="login-form"
        id="login-form"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        method="post"
      >
        <!--Input for name, type=text-->
        <div class="input-group mb-3">
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
          <p id="usernameError" class="error"><?php if($nameError) {echo "İsim boş olamaz!";}?></p>
        </div>

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
            placeholder="..."
            required
          />
          <p id="passwordError" class="error"><?php if($pwError) {echo "Şifre boş olamaz!";}?></p>
        </div>

        <!--Login error-->
        <div>
            <p id="authError" class="error"><?php if($authError) {echo "Hatalı giriş!";}?></p>
            <p id="successAuth" class="success"><?php if($successAuth) {echo "Giriş başarılı!";}?></p>
        </div>

        <!--Input for submitting the form, type=submit-->
        <div>
          <input
            type="submit"
            value="Gönder"
            name="login-submit"
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