<?php 
    require "header.php";
?>

<?php 
    // define variables and set to empty values
    $work_happiness = $daily_happiness = $total_happiness = $content = "";
    $date = "";
    $error = false;
    $errorText = "";
    $isDatePicked = false;
    // Get name from session
    $name = $_SESSION['name'];
    // Check if name is empty or not and redirect
    if($name == "" || $name == NULL)      
        echo("<script>location.href = './index.php';</script>"); 
  
    // Database connection
    require "./mysqli_connect.php";

    // Check request method for post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!empty($_POST["show-date"])){
            $date = test_input($_POST["show-date"]);
        }
        else if(!empty($_POST["show-month"])){
            $date = test_input($_POST["show-month"]);
        }
        else if(!empty($_POST["show-year"])){
            $date = test_input($_POST["show-year"]);
        }
        else{
            $error = true;
            $errorText = "Üç tarihte boş!";
        }

        if(!empty($date)){
            $isDatePicked = true;
            // Check DB for picked date
            $sql = "SELECT work_happiness, daily_happiness, total_happiness, content, date 
                    FROM gunluk WHERE name=? AND date LIKE ? ORDER BY date DESC";
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
                if(!mysqli_stmt_execute($stmt)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $work_happiness, $daily_happiness, $total_happiness, $content, $journal_date);
                // Results fetched below...
            }
        }
        else{
            $error = true;
            $errorText = "Gün, ay ya da yıl seçip gönderin.";
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
    
    <!--Error-->
    <div>
        <p id="dateError" class="error"><?php if($error) {echo "Hata meydana geldi. ".$errorText;}?></p>
    </div>  

    <form
        name="date-form"
        id="date-form"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        method="post"
        <?php if($isDatePicked) echo 'style="display: none;"';?>
    >
        <h1>Görüntüleme tarihi seçiniz:</h1>
        <!--Input for date, type=date-->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="day-label">Gün</span>
            </div>
            <input type="date" name="show-date">
        </div>
        <!--Input for month, type=month-->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="month-label">Ay</span>
            </div>
            <input type="month" name="show-month">
        </div>
        <!--Input for year, type=text-->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="year-label">Yıl</span>
            </div>
            <input type="number" name="show-year" min="1000" max="9999" title="Sadece 4 rakam">
        </div>

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

    <!-- 2 parts, 1 of them hidden -->

    <div <?php if(!$isDatePicked) echo 'style="display: none;"';?>>
        <h1><?php
            if(isset($_SESSION['name'])){
                echo $_SESSION['name'].', ';
            }
            echo $date.' tarihili günlüklerin'
        ?></h1>

    <?php 
        if(mysqli_stmt_store_result($stmt)){
            // Check if DB returned any result
            if(mysqli_stmt_num_rows($stmt) > 0){
                // Fetch values
                while (mysqli_stmt_fetch($stmt)) {
                    echo '
                        <div class="row" style="border-top: solid 1px #ff7700; padding-top:5px;">
                            <div class="col-xs-6 col-sm-2 px-0">
                                <button class="btn btn-primary btn-sm p-0">
                                    <a href="edit.php?date='.explode(" ",$journal_date)[0].'" class="btn text-white">Güncelle</a>
                                </button>
                            </div>
                            <div class="col-xs-6 col-sm-3 px-0">
                                <p class="orangeText">Tarih:</p>
                                <p>'.$journal_date.'</p>
                            </div>
                            <div class="col-xs-4 col-sm-2 px-0">
                                <p class="orangeText">İş/Okul:</p>';
                                switch($work_happiness){
                                    case 10:
                                        echo '<p>Muhteşem</p>';
                                        break;
                                    case 9:
                                        echo '<p>Şahane</p>';
                                        break;
                                    case 8:
                                        echo '<p>Baya iyi</p>';
                                        break;
                                    case 7:
                                        echo '<p>Gayet iyi</p>';
                                        break;
                                    case 6:
                                        echo '<p>Fena değil</p>';
                                        break;
                                    case 5:
                                        echo '<p>Normal</p>';
                                        break;
                                    case 4:
                                        echo '<p>Biraz kötü</p>';
                                        break;
                                    case 3:
                                        echo '<p>Kötü</p>';
                                        break;
                                    case 2:
                                        echo '<p>Berbat</p>';
                                        break;
                                    case 1:
                                        echo '<p>Berbat ötesi</p>';
                                        break;
                                    case 0:
                                        echo '<p>Yorum Yok</p>';
                                        break;
                                }
                            echo
                            '</div>
                            <div class="col-xs-4 col-sm-3 px-0">
                                <p class="orangeText">İş/Okul dışı:</p>';
                                switch($daily_happiness){
                                    case 10:
                                        echo '<p>Muhteşem</p>';
                                        break;
                                    case 9:
                                        echo '<p>Şahane</p>';
                                        break;
                                    case 8:
                                        echo '<p>Baya iyi</p>';
                                        break;
                                    case 7:
                                        echo '<p>Gayet iyi</p>';
                                        break;
                                    case 6:
                                        echo '<p>Fena değil</p>';
                                        break;
                                    case 5:
                                        echo '<p>Normal</p>';
                                        break;
                                    case 4:
                                        echo '<p>Biraz kötü</p>';
                                        break;
                                    case 3:
                                        echo '<p>Kötü</p>';
                                        break;
                                    case 2:
                                        echo '<p>Berbat</p>';
                                        break;
                                    case 1:
                                        echo '<p>Berbat ötesi</p>';
                                        break;
                                    case 0:
                                        echo '<p>Yorum Yok</p>';
                                        break;
                                }
                            echo
                            '</div>
                            <div class="col-xs-4 col-sm-2 px-0">
                                <p class="orangeText">Genel:</p>';
                                switch($total_happiness){
                                    case 10:
                                        echo '<p>Muhteşem</p>';
                                        break;
                                    case 9:
                                        echo '<p>Şahane</p>';
                                        break;
                                    case 8:
                                        echo '<p>Baya iyi</p>';
                                        break;
                                    case 7:
                                        echo '<p>Gayet iyi</p>';
                                        break;
                                    case 6:
                                        echo '<p>Fena değil</p>';
                                        break;
                                    case 5:
                                        echo '<p>Normal</p>';
                                        break;
                                    case 4:
                                        echo '<p>Biraz kötü</p>';
                                        break;
                                    case 3:
                                        echo '<p>Kötü</p>';
                                        break;
                                    case 2:
                                        echo '<p>Berbat</p>';
                                        break;
                                    case 1:
                                        echo '<p>Berbat ötesi</p>';
                                        break;
                                    case 0:
                                        echo '<p>Yorum Yok</p>';
                                        break;
                                }
                            echo
                            '</div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <p class="mb-0 pb-3">'.(!empty($content) ? $content : "").'</p>
                        </div>
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
    </div>

</main>

<?php
    require "footer.php";
?>