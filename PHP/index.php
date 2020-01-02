<?php 
    require "head.php";
?>
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
    if(
      $name === "X" || 
      $name === "Y" ||
      $name === "Z" ||
      $name === "T"){
        if($password === "PW")
          return true;
        else
          return false;
    } 
    else
      return false;
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
            <span class="input-group-text" id="basic-addon1">İsim</span>
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
            <span class="input-group-text" id="basic-addon1">Şifre</span>
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