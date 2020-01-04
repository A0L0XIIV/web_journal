<?php 
    require "header.php";
?>

<?php 
  // define variables and set to empty values
  $work_happiness = $daily_happiness = $total_happiness = $content = "";
  $error = false;
  $success = false;
  $errorText = "";
  $id = 0;

  // Check request method for post
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Database connection
    require "./mysqli_connect.php";

    // Check DB for same date entry
    $sql = "SELECT id FROM gunluk WHERE name=? AND date LIKE ?";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        $error = true;
    }
    else{
        // Get name from session
        $name = $_SESSION['name'];
        // Set timezone as GMT and get current date
        date_default_timezone_set('GMT');
        $date = date('Y-m-d');
        // Preparing the park name for LIKE query 
        $param = $date.'%';
        // Bind inputs to query parameters
        mysqli_stmt_bind_param($stmt, "ss", $name, $param);
        // Execute sql statement
        if(!mysqli_stmt_execute($stmt)){
            $error = true;
            $errorText = mysqli_error($conn);
        }
        // Bind result variables
        mysqli_stmt_bind_result($stmt, $id);
        // Results fetched
        if(mysqli_stmt_store_result($stmt)){
            // Check if DB returned any result - Same day entry check
            if(mysqli_stmt_num_rows($stmt) > 0){
                $error = true;
                $errorText = "Günde sadece 1 tane günlük eklenebilir.";
            }
            // Not found any same day entry - Add it into DB
            else{
                // Security operations on text
                $content = test_input($_POST["content"]);
                // Encoding change
                $content = mb_convert_encoding($content, "UTF-8");
                // Get happiness values
                $work_happiness = $_POST["work_happiness"];
                $daily_happiness = $_POST["daily_happiness"];
                $total_happiness = $_POST["total_happiness"];
                // Set timezone as GMT and get current date
                //date_default_timezone_set('GMT');
                //$date = date('Y-m-d H:i:s'); --> Server time but changed date to client's time
                $date = $_POST["date"];
                // Save journal into DB
                $sql = "INSERT INTO gunluk (name, work_happiness, daily_happiness, total_happiness, content, date) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sql)){
                    $error = true;
                }
                else{
                    // Bind inputs to query parameters
                    mysqli_stmt_bind_param($stmt, "siiiss", $name, $work_happiness, $daily_happiness, 
                                            $total_happiness, $content, $date);
                    // Execute sql statement
                    if(mysqli_stmt_execute($stmt))
                        $success = true;
                    else{
                        $error = true;
                        $errorText = mysqli_error($conn);
                    }
                }
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

<!-- Main center div-->
<main class="main">

    <!--Success-->
    <div <?php if(!$success) echo 'style="display: none;"';?>>
        <p id="updateSuccess" class="success"><?php if($success) {echo "Günlük başarılı bir şekilde eklendi.";}?></p>
    </div>

    <!--Error-->
    <div <?php if(!$error) echo 'style="display: none;"';?>>
        <p id="dbError" class="error"><?php if($error) {echo "Hata meydana geldi. ".$errorText;}?></p>
    </div>

    <form
        name="write-form"
        id="write-form"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        method="post"
        onsubmit="return getDate()"
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
            <option value="10" class="opt10">&#xf118; Muhteşem</option>
            <option value="9" class="opt9">&#xf118; Şahane</option>
            <option value="8" class="opt8">&#xf118; Baya iyi</option>
            <option value="7" class="opt7">&#xf118; Gayet iyi</option>
            <option value="6" class="opt6">&#xf11a; Fena değil</option>
            <option value="5" class="opt5">&#xf11a; Normal</option>
            <option value="4" class="opt4">&#xf11a; Biraz kötü</option>
            <option value="3" class="opt3">&#xf119; Kötü</option>
            <option value="2" class="opt2">&#xf119; Berbat</option>
            <option value="1" class="opt1">&#xf119; Berbat ötesi</option>
            <option value="0" class="opt0">&#xf1db; Yorum Yok</option>
        </select>

        <hr>
        
        <p>İş/okul dışında</p>
        <select name="daily_happiness" class="custom-select">
            <option value="" hidden selected>günün nasıl geçti?</option>
            <option value="10" class="opt10">&#xf118; Muhteşem</option>
            <option value="9" class="opt9">&#xf118; Şahane</option>
            <option value="8" class="opt8">&#xf118; Baya iyi</option>
            <option value="7" class="opt7">&#xf118; Gayet iyi</option>
            <option value="6" class="opt6">&#xf11a; Fena değil</option>
            <option value="5" class="opt5">&#xf11a; Normal</option>
            <option value="4" class="opt4">&#xf11a; Biraz kötü</option>
            <option value="3" class="opt3">&#xf119; Kötü</option>
            <option value="2" class="opt2">&#xf119; Berbat</option>
            <option value="1" class="opt1">&#xf119; Berbat ötesi</option>
            <option value="0" class="opt0">&#xf1db; Yorum Yok</option>
        </select>

        <hr>
        
        <p>Genelde</p>
        <select name="total_happiness" class="custom-select">
            <option value="" hidden selected>günün nasıl geçti?</option>
            <option value="10" class="opt10">&#xf118; Muhteşem</option>
            <option value="9" class="opt9">&#xf118; Şahane</option>
            <option value="8" class="opt8">&#xf118; Baya iyi</option>
            <option value="7" class="opt7">&#xf118; Gayet iyi</option>
            <option value="6" class="opt6">&#xf11a; Fena değil</option>
            <option value="5" class="opt5">&#xf11a; Normal</option>
            <option value="4" class="opt4">&#xf11a; Biraz kötü</option>
            <option value="3" class="opt3">&#xf119; Kötü</option>
            <option value="2" class="opt2">&#xf119; Berbat</option>
            <option value="1" class="opt1">&#xf119; Berbat ötesi</option>
            <option value="0" class="opt0">&#xf1db; Yorum Yok</option>
        </select>

        <hr>

        <p>Günlük alanı</p>
        <textarea 
            name="content" 
            id="content" 
            cols="30" 
            rows="10" 
            maxlength="1000" 
            placeholder="max 1000 harf"
        ></textarea>

        <hr>

        <!--Input for submitting the form, type=submit-->
        <input type="text" value="" name="date" id="date-input" hidden />

        <!--Input for submitting the form, type=submit-->
        <div>
          <input
            type="submit"
            value="Gönder"
            name="write-submit"
            class="btn btn-primary"
            aria-pressed="false"
          />
        </div>

        <br>
    </form>

</main>

<?php
    require "footer.php";
?>