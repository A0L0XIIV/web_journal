<?php 
    require "header.php";
    // Check if the session variable name is empty or not and redirect
    if ($_SERVER["REQUEST_METHOD"] === "GET"
        && !isset($_SESSION['name'])) {
        exit("<script>location.href = './index.php';</script>"); 
    }
?>

<?php
    // New Journal Entry POST Request Handler

    // Database connection
    require "./mysqli_connect.php";

    // define variables and set to empty values
    $work_happiness = $daily_happiness = $total_happiness = $content = "";
    $daily_game_id = $daily_series_id = $daily_movie_id = $daily_book_id = "";
    $daily_game_duration = $daily_series_duration = $daily_movie_duration = $daily_book_duration = "";
    $error = false;
    $success = false;
    $errorText = "";
    $id = -1;

    // Check request method for post
    if ($_SERVER["REQUEST_METHOD"] === "POST"
        && isset($_POST["write-submit"])) {
        // Get name from session
        $name = $_SESSION['name'];
        // Check if name is empty or not and redirect
        if($name == "" || $name == NULL)      
            echo("<script>location.href = './index.php';</script>"); 

        // Check DB for same date entry
        $sql = "SELECT id FROM gunluk WHERE name=? AND date LIKE ?";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            $error = true;
        }
        else{
            // Set timezone as GMT and get current date
            date_default_timezone_set('GMT');
            $date = date('Y-m-d');
            // Preparing the date for LIKE query 
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
                        if(mysqli_stmt_execute($stmt)){
                            // Gunluk successfully added
                            $success = true;

                            // Add entertainment (daily game, series, movie and book in DB)

                            // Get last inserted element's id
                            $last_gunluk_id = mysqli_insert_id($conn);
                            // Check if it return, zero means error
                            if($last_gunluk_id != '0'){
                                // Add new game into daily_game
                                if(isset($_POST["game"])){
                                    if(daily_entertainment("game", $last_gunluk_id, $conn)){
                                        $success = true;
                                    }
                                    else{    
                                        $error = true;
                                        $errorText .= "Günlük film ekleme başarısız.\n";
                                    }
                                }
                                // Add new series into daily_series
                                if(isset($_POST["series"])){
                                    if(daily_entertainment("series", $last_gunluk_id, $conn)){
                                        $success = true;
                                    }
                                    else{    
                                        $error = true;
                                        $errorText .= "Günlük dizi ekleme başarısız.\n";
                                    }
                                }
                                // Add new movie into daily_movie
                                if(isset($_POST["movie"])){
                                    if(daily_entertainment("movie", $last_gunluk_id, $conn)){
                                        $success = true;
                                    }
                                    else{    
                                        $error = true;
                                        $errorText .= "Günlük film ekleme başarısız.\n";
                                    }
                                }
                                // Add new book into daily_book
                                if(isset($_POST["book"])){
                                    if(daily_entertainment("book", $last_gunluk_id, $conn)){
                                        $success = true;
                                    }
                                    else{    
                                        $error = true;
                                        $errorText .= "Günlük kitap ekleme başarısız.\n";
                                    }
                                }
                            }
                        }
                        else{
                            $error = true;
                            $errorText = "Günlük ekleme başarısız.\n" . mysqli_error($conn);
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

  function daily_entertainment($type, $last_gunluk_id, $conn){
    // Get entertainment list from POST request 
    $entertainment_list = $_POST[$type];
    $error = false;
    // Loop over array
    foreach ($entertainment_list as $entertainment)  {
        switch($type){
            case "game":
                $sql = "INSERT INTO daily_game (gunluk_id, game_id, duration) 
                        VALUES (?, ?, ?)";
                break;
            case "series":
                $sql = "INSERT INTO daily_series (gunluk_id, series_id, begin_season, begin_episode, end_season, end_episode) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                break;
            case "movie":
                $sql = "INSERT INTO daily_movie (gunluk_id, movie_id, duration) 
                        VALUES (?, ?, ?)";
                break;
            case "book":
                $sql = "INSERT INTO daily_book (gunluk_id, book_id, duration) 
                        VALUES (?, ?, ?)";
                break;
            default:
                $error = true;
                break;

        }
        // If type is not correct, break the loop
        if($error === true){
            break;
        }
        // SQL statement initialization
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            $error = true;
        }
        else{
            // Extract int from id
            $entertainment_id = (int) filter_var($entertainment['id'], FILTER_SANITIZE_NUMBER_INT);
            // Series has different columns than other 3 entertainment types
            if($type === 'series'){
                // Split the beginning and end season and episode, e.g. S2E3-S2E5
                $begEnd = explode("-",$entertainment['duration']);
                // Remove S from strings
                $begEnd[0] = str_ireplace("S", "", $begEnd[0]);
                $begEnd[1] = str_ireplace("S", "", $begEnd[1]);
                // Split 2 strings from E, season and episode number will be array's elements
                $begin = explode("E", $begEnd[0]);
                $begin_season = $begin[0];      // Season number is the first element
                $begin_episode = $begin[1];     // Episode number is the second element
                $end = explode("E", $begEnd[1]);
                $end_season = $end[0];
                $end_episode = $end[1];
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "iiiiii", $last_gunluk_id, $entertainment_id, $begin_season,
                                        $begin_episode, $end_season, $end_episode);
            }
            // game, movie and book duration
            else{
                // Extract int from duration
                $entertainment_duration = rtrim($entertainment['duration'],'S');
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "iid", $last_gunluk_id, $entertainment_id, $entertainment_duration);
            }
            // Execute sql statement
            if(!mysqli_stmt_execute($stmt)){
                $error = true;
            }
        }
    }
    // Return true or false
    if($error)
        return false;
    else
        return true;
  }
?>

<!-- Main center div-->
<main class="main">
    <br>
    <?php
    // Success
    if($success) {
        echo '<!--Success-->
            <div id="main-success" class="success">
                <p>Günlük başarılı bir şekilde eklendi.
                    <button type="button"
                        class="fa fa-times-circle btn text-danger" 
                        aria-hidden="true" 
                        onclick="$(\'#main-success\').hide()">
                    </button>
                </p> 
            </div>';
    }

    // Error
    if($error) {
        echo '<!--Error-->
                <div class="error" id="main-error">
                    <p>Hata meydana geldi. '.$errorText.'
                        <button type="button"
                            class="fa fa-times-circle btn text-danger" 
                            aria-hidden="true" 
                            onclick="$(\'#main-error\').hide()">
                        </button>
                    </p> 
                </div>';
    }
    ?>


    <form
        name="write-form"
        id="write-form"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        method="post"
        onsubmit="return getDate()">
    
        <h1>Günlüğe hoşgeldin
            <?php
                if(isset($_SESSION['name'])){
                    echo ' '.$_SESSION['name'];
                }
            ?>!
        </h1>

        <?php 
            // Journal happiness and text inputs
            require "inputs/journal-inputs.php";
            // Daily Entertainment Inputs: Playing Games
            require "inputs/ent-game-inputs.php";
            // Daily Entertainment Inputs: Watching Series
            require "inputs/ent-series-inputs.php";
            // Daily Entertainment Inputs: Watching movies
            require "inputs/ent-movie-inputs.php";
            // Daily Entertainment Inputs: Reading Books
            require "inputs/ent-book-inputs.php";
        ?>

        <hr>

        <!--Input for submitting the form, type=submit-->
        <input type="text" value="" name="date" id="date-input" hidden />

        <!--Button for submitting the form-->
        <div>
            <button
                type="submit"
                name="write-submit"
                class="sbmt-btn bg-write"
                aria-pressed="false">
                Gönder
            </button>
        </div>

        <br>
    </form>

    <!-- Modal: Add new entertainment into database -->
    <div class="modal fade" id="add-entertainment-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yeni <span class="entertaintment-type"></span> Ekle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" 
                            name="new-entertainment-name" 
                            id="new-entertainment-name" 
                            placeholder="Ad (En fazla 50 harf)" 
                            maxlength="50" 
                            required>
                    
                    <div id="add-entertainment-success" class="success" style="display:none;">
                        <!--Success-->
                        <p><span class="entertaintment-type"></span> başarılı bir şekilde eklendi. Lütfen bekleyin... 
                            <button type="button"
                                class="fa fa-times-circle btn text-danger" 
                                aria-hidden="true" 
                                onclick="$('#add-entertainment-success').hide()">
                            </button>
                        </p> 
                    </div>

                    <div id="add-entertainment-error" class="error" style="display:none;">
                        <!--Error-->
                        <p>Hata meydana geldi. <span id="add-entertainment-error-text"></span> 
                            <button type="button"
                                class="fa fa-times-circle btn text-danger" 
                                aria-hidden="true" 
                                onclick="$('#add-entertainment-error').hide()">
                            </button>
                        </p> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="bg-logout" data-dismiss="modal">Kapat</button>
                    <button type="button" class="sbmt-btn bg-login" id="add-entertainment-btn" onclick="addNewEntertainment('game');">Ekle</button>
                </div>
            </div>
        </div>
    </div>

</main>

<?php
    require "footer.php";
?>