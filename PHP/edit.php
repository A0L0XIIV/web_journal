<?php 
    require "header.php";
?>

<?php 
    // define variables and set to empty values
    $work_happiness = $daily_happiness = $total_happiness = $content = "";
    $date = "";
    $error = false;
    $success = false;
    $isDatePicked = false;
    $errorText = "";
    // Get name from session
    $name = $_SESSION['name'];
  
    // Database connection
    require "./mysqli_connect.php";

    // Check request method for post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Date picker form
        if(isset($_POST['edit-date'])){
            $date = test_input($_POST["edit-date"]);
            if(!empty($date)){
                $isDatePicked = true;

                // Check DB for picked date
                $sql = "SELECT work_happiness, daily_happiness, total_happiness, content 
                        FROM gunluk WHERE name=? AND date LIKE ?";
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sql)){
                    $error = true;
                }
                else{
                    // Preparing the park name for LIKE query 
                    $param = '%'.$date.'%';
                    // Bind inputs to query parameters
                    mysqli_stmt_bind_param($stmt, "ss", $name, $param);
                    // Execute sql statement
                    mysqli_stmt_execute($stmt);
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $work_happiness, $daily_happiness, $total_happiness, $content);
                    // Results fetched below...
                }
            }
        }
        // Edit form
        else if(isset($_POST['content'])){
            // Security operations on text
            $content = test_input($_POST["content"]);
            // Encoding change
            $content = mb_convert_encoding($content, "UTF-8");
            // Get happiness values
            $work_happiness = $_POST["work_happiness"];
            $daily_happiness = $_POST["daily_happiness"];
            $total_happiness = $_POST["total_happiness"];
            $date = $_POST["date"];
            // Update journal in DB
            $sql = "UPDATE gunluk SET work_happiness=?, daily_happiness=?, total_happiness=?, content=? 
                    WHERE name=? AND date LIKE ?";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)){
                $error = true;
            }
            else{
                // Preparing the date for LIKE query - only back % because it starts with year 
                $param = $date.'%';
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "iiisss", $work_happiness, $daily_happiness, 
                                        $total_happiness, $content, $name, $param);
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
    // Check request method for get
    else if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // Date picker form
        if(isset($_GET['date'])){
            $date = test_input($_GET["date"]);
            if(!empty($date)){
                $isDatePicked = true;

                // Check DB for picked date
                $sql = "SELECT work_happiness, daily_happiness, total_happiness, content 
                        FROM gunluk WHERE name=? AND date LIKE ?";
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sql)){
                    $error = true;
                }
                else{
                    // Preparing the park name for LIKE query 
                    $param = '%'.$date.'%';
                    // Bind inputs to query parameters
                    mysqli_stmt_bind_param($stmt, "ss", $name, $param);
                    // Execute sql statement
                    mysqli_stmt_execute($stmt);
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $work_happiness, $daily_happiness, $total_happiness, $content);
                    // Results fetched below...
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
        <p id="updateSuccess" class="success"><?php if($success) {echo "Günlük başarılı bir şekilde güncellendi.";}?></p>
    </div>

    <!--Error-->
    <div <?php if(!$error) echo 'style="display: none;"';?>>
        <p id="editError" class="error"><?php if($error) {echo "Hata meydana geldi. ".$errorText;}?></p>
    </div>

    <form
        name="date-form"
        id="date-form"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        method="post"
        <?php if($isDatePicked) echo 'style="display: none;"';?>
    >
        <h1>Güncelleme tarihi seçiniz:</h1>
        <input type="date" name="edit-date">

        <hr>

        <!--Input for submitting the form, type=submit-->
        <div>
          <input
            type="submit"
            value="Gönder"
            name="date-picker-submit"
            class="btn btn-primary"
            aria-pressed="false"
          />
        </div>

        <br>
    </form> 

    <!-- 2 forms, 1 of them hidden -->

    <form
        name="edit-form"
        id="edit-form"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        method="post"
        <?php if(!$isDatePicked) echo 'style="display: none;"';?>
    >

    <h1><?php
        if(isset($_SESSION['name'])){
            echo $_SESSION['name'].', ';
        }
        echo $date.' tarihili günlüğün'
    ?></h1>

    <hr>

<?php 
    if(mysqli_stmt_store_result($stmt)){
        // Check if DB returned any result
        if(mysqli_stmt_num_rows($stmt) > 0){
            // Fetch values
            while (mysqli_stmt_fetch($stmt)) {
                echo '
                    <p>İşte/okulda</p>
                    <select name="work_happiness" class="custom-select">
                        <option value="" hidden selected>günün nasıl geçti?</option>
                        <option value="10" class="opt10"'.($work_happiness==10 ? "selected" : "").'>&#xf118; Muhteşem</option>
                        <option value="9" class="opt9"'.($work_happiness==9 ? "selected" : "").'>&#xf118; Şahane</option>
                        <option value="8" class="opt8"'.($work_happiness==8 ? "selected" : "").'>&#xf118; Baya iyi</option>
                        <option value="7" class="opt7"'.($work_happiness==7 ? "selected" : "").'>&#xf118; Gayet iyi</option>
                        <option value="6" class="opt6"'.($work_happiness==6 ? "selected" : "").'>&#xf11a; Fena değil</option>
                        <option value="5" class="opt5"'.($work_happiness==5 ? "selected" : "").'>&#xf11a; Normal</option>
                        <option value="4" class="opt4"'.($work_happiness==4 ? "selected" : "").'>&#xf11a; Biraz kötü</option>
                        <option value="3" class="opt3"'.($work_happiness==3 ? "selected" : "").'>&#xf119; Kötü</option>
                        <option value="2" class="opt2"'.($work_happiness==2 ? "selected" : "").'>&#xf119; Berbat</option>
                        <option value="1" class="opt1"'.($work_happiness==1 ? "selected" : "").'>&#xf119; Berbat ötesi</option>
                        <option value="0" class="opt0"'.($work_happiness==0 ? "selected" : "").'>&#xf1db; Yorum Yok</option>
                    </select>

                    <hr>
                    
                    <p>İş/okul dışında</p>
                    <select name="daily_happiness" class="custom-select">
                        <option value="" hidden selected>günün nasıl geçti?</option>
                        <option value="10" class="opt10"'.($daily_happiness==10 ? "selected" : "").'>&#xf118; Muhteşem</option>
                        <option value="9" class="opt9"'.($daily_happiness==9 ? "selected" : "").'>&#xf118; Şahane</option>
                        <option value="8" class="opt8"'.($daily_happiness==8 ? "selected" : "").'>&#xf118; Baya iyi</option>
                        <option value="7" class="opt7"'.($daily_happiness==7 ? "selected" : "").'>&#xf118; Gayet iyi</option>
                        <option value="6" class="opt6"'.($daily_happiness==6 ? "selected" : "").'>&#xf11a; Fena değil</option>
                        <option value="5" class="opt5"'.($daily_happiness==5 ? "selected" : "").'>&#xf11a; Normal</option>
                        <option value="4" class="opt4"'.($daily_happiness==4 ? "selected" : "").'>&#xf11a; Biraz kötü</option>
                        <option value="3" class="opt3"'.($daily_happiness==3 ? "selected" : "").'>&#xf119; Kötü</option>
                        <option value="2" class="opt2"'.($daily_happiness==2 ? "selected" : "").'>&#xf119; Berbat</option>
                        <option value="1" class="opt1"'.($daily_happiness==1 ? "selected" : "").'>&#xf119; Berbat ötesi</option>
                        <option value="0" class="opt0"'.($daily_happiness==0 ? "selected" : "").'>&#xf1db; Yorum Yok</option>
                    </select>

                    <hr>
                    
                    <p>Genelde</p>
                    <select name="total_happiness" class="custom-select">
                        <option value="" hidden selected>günün nasıl geçti?</option>
                        <option value="10" class="opt10"'.($total_happiness==10 ? "selected" : "").'>&#xf118; Muhteşem</option>
                        <option value="9" class="opt9"'.($total_happiness==9 ? "selected" : "").'>&#xf118; Şahane</option>
                        <option value="8" class="opt8"'.($total_happiness==8 ? "selected" : "").'>&#xf118; Baya iyi</option>
                        <option value="7" class="opt7"'.($total_happiness==7 ? "selected" : "").'>&#xf118; Gayet iyi</option>
                        <option value="6" class="opt6"'.($total_happiness==6 ? "selected" : "").'>&#xf11a; Fena değil</option>
                        <option value="5" class="opt5"'.($total_happiness==5 ? "selected" : "").'>&#xf11a; Normal</option>
                        <option value="4" class="opt4"'.($total_happiness==4 ? "selected" : "").'>&#xf11a; Biraz kötü</option>
                        <option value="3" class="opt3"'.($total_happiness==3 ? "selected" : "").'>&#xf119; Kötü</option>
                        <option value="2" class="opt2"'.($total_happiness==2 ? "selected" : "").'>&#xf119; Berbat</option>
                        <option value="1" class="opt1"'.($total_happiness==1 ? "selected" : "").'>&#xf119; Berbat ötesi</option>
                        <option value="0" class="opt0"'.($total_happiness==0 ? "selected" : "").'>&#xf1db; Yorum Yok</option>
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
                    >'.(!empty($content) ? $content : "").'</textarea>
                    <input type="text" name="date" value="'.$date.'" hidden/>
            ';}
        }
        else{
            echo'<!--Error-->
                <div>
                    <p id="notFoundError" class="error">Bu tarihli günlük bulunamadı.</p>
                </div>';
        }
    }
    else{
        echo'<!--Error-->
            <div>
                <p id="dbError" class="error">Veritabanı \'store\' hatası.</p>
            </div>';
    }
?>
        <hr>

        <!--Input for submitting the form, type=submit-->
        <div>
          <input
            type="submit"
            value="Gönder"
            name="edit-submit"
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