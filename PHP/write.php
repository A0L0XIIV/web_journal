<?php 
    require "head.php";
?>

<?php 
    require "header.php";
?>

<?php 
  // define variables and set to empty values
  $work_happiness = $daily_happiness = $total_happiness = $content = "";
  $error = "";

  // Check request method for post
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Security operations on text
    $name = $_SESSION['name'];
    $content = test_input($_POST["content"]);
    $work_happiness = $_POST["work_happiness"];
    $daily_happiness = $_POST["daily_happiness"];
    $total_happiness = $_POST["total_happiness"];
    date_default_timezone_set('GMT');
    $date = date('Y-m-d H:i:s');
    // Database connection
    require "../mysqli_connect.php";
    // Save user into DB
    $sql = "INSERT INTO gunluk (name, work_happiness, daily_happiness, total_happiness, content, date) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        $error = "Database error!";
    }
    else{
        // Bind inputs to query parameters
        mysqli_stmt_bind_param($stmt, "siiiss", $name, $work_happiness, $daily_happiness, 
                                $total_happiness, $content, $date);
        // Execute sql statement
        mysqli_stmt_execute($stmt);
    }
  }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
?>

<!-- Main center div-->
<main class="main">

    <form
        name="write-form"
        id="write-form"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        method="post"
      >
    
        <h1>Günlüğe hoşgeldin
            <?php
                if(isset($_SESSION['name'])){
                    echo ' '.$_SESSION['name'];
                }
            ?>!
        </h1>

        <hr>

        <p>İşte/okulda</p>
        <select name="work_happiness" class="custom-select">
            <option value="" hidden selected>günün nasıl geçti?</option>
            <option value="10" id="opt10">&#xf118; Muhteşem</option>
            <option value="9" id="opt9">&#xf118; Şahane</option>
            <option value="8" id="opt8">&#xf118; Baya iyi</option>
            <option value="7" id="opt7">&#xf118; Gayet iyi</option>
            <option value="6" id="opt6">&#xf11a; Fena değil</option>
            <option value="5" id="opt5">&#xf11a; Normal</option>
            <option value="4" id="opt4">&#xf11a; Biraz kötü</option>
            <option value="3" id="opt3">&#xf119; Kötü</option>
            <option value="2" id="opt2">&#xf119; Berbat</option>
            <option value="1" id="opt1">&#xf119; Berbat ötesi</option>
            <option value="0" id="opt0">&#xf1db; Yorum Yok</option>
        </select>

        <hr>
        
        <p>İş/okul dışında</p>
        <select name="daily_happiness" class="custom-select">
            <option value="" hidden selected>günün nasıl geçti?</option>
            <option value="10" id="opt10">&#xf118; Muhteşem</option>
            <option value="9" id="opt9">&#xf118; Şahane</option>
            <option value="8" id="opt8">&#xf118; Baya iyi</option>
            <option value="7" id="opt7">&#xf118; Gayet iyi</option>
            <option value="6" id="opt6">&#xf11a; Fena değil</option>
            <option value="5" id="opt5">&#xf11a; Normal</option>
            <option value="4" id="opt4">&#xf11a; Biraz kötü</option>
            <option value="3" id="opt3">&#xf119; Kötü</option>
            <option value="2" id="opt2">&#xf119; Berbat</option>
            <option value="1" id="opt1">&#xf119; Berbat ötesi</option>
            <option value="0" id="opt0">&#xf1db; Yorum Yok</option>
        </select>

        <hr>
        
        <p>Genelde</p>
        <select name="total_happiness" class="custom-select">
            <option value="" hidden selected>günün nasıl geçti?</option>
            <option value="10" id="opt10">&#xf118; Muhteşem</option>
            <option value="9" id="opt9">&#xf118; Şahane</option>
            <option value="8" id="opt8">&#xf118; Baya iyi</option>
            <option value="7" id="opt7">&#xf118; Gayet iyi</option>
            <option value="6" id="opt6">&#xf11a; Fena değil</option>
            <option value="5" id="opt5">&#xf11a; Normal</option>
            <option value="4" id="opt4">&#xf11a; Biraz kötü</option>
            <option value="3" id="opt3">&#xf119; Kötü</option>
            <option value="2" id="opt2">&#xf119; Berbat</option>
            <option value="1" id="opt1">&#xf119; Berbat ötesi</option>
            <option value="0" id="opt0">&#xf1db; Yorum Yok</option>
        </select>

        <hr>

        <p>Günlük alanı</p>
        <textarea 
            name="content" 
            id="content" 
            cols="30" 
            rows="10" 
            maxlength="500" 
            placeholder="max 500 harf"
        ></textarea>

        <hr>

        <!--Input for submitting the form, type=submit-->
        <div>
          <input
            type="submit"
            value="Submit"
            name="login-submit"
            class="btn btn-primary"
            aria-pressed="false"
          />
        </div>

        <!--Error-->
        <div>
            <p id="dbError" class="error"><?php echo $error;?></p>
        </div>

        <br>
    </form>

</main>

<?php
    require "footer.php";
?>