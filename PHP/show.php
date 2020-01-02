<?php 
    require "head.php";
?>

<?php 
    require "header.php";
?>

<?php 
    // define variables and set to empty values
    $work_happiness = $daily_happiness = $total_happiness = $content = "";
    $date = "";
    $error = false;
    $isDatePicked = false;
    // Get name from session
    $name = $_SESSION['name'];
  
    // Database connection
    require "./mysqli_connect.php";

    // Check request method for post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

?>

<!-- Main center div-->
<main class="main">    

    <form
        name="date-form"
        id="date-form"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        method="post"
        <?php if($isDatePicked) echo 'style="display: none;"';?>
    >
        <h1>Görüntüleme tarihi seçiniz:</h1>
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

        <!--Error-->
        <div>
            <p id="dateError" class="error"><?php if($error) {echo "Hata meydana geldi.";}?></p>
        </div>
        <br>
    </form> 

    <!-- 2 forms, 1 of them hidden -->

    <h1><?php
        if(isset($_SESSION['name'])){
            echo $_SESSION['name'].', ';
        }
        echo $date.' tarihili günlüklerin'
    ?></h1>

    <hr>

<?php 
    if(mysqli_stmt_store_result($stmt)){
        // Check if DB returned any result
        if(mysqli_stmt_num_rows($stmt) > 0){
            // Fetch values
            while (mysqli_stmt_fetch($stmt)) {
                echo '';}
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

</main>

<?php
    require "footer.php";
?>