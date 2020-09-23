<?php
    // Get entertainments AJAX request handler

    // Database connection
    require "./mysqli_connect.php";

    // Variables
    $entertainment_name = $entertainment_id = "";
    
    if ($_SERVER["REQUEST_METHOD"] === "POST"
        && !isset($_POST["write-submit"])
        && !isset($_POST['name'])
        && !isset($_POST['id'])
        && isset($_POST['type'])) {
        // Get entertainment type
        $type = $_POST['type'];

        // Each type has different tables
        // Get game names
        if($type === "game"){
            // Check DB for picked date
            $sql = "SELECT name, id FROM game";
        }
        // Series SQL
        else if($type === "series"){
            // Check DB for picked date
            $sql = "SELECT name, id FROM series";
        }
        // Series SQL
        else if($type === "movie"){
            // Check DB for picked date
            $sql = "SELECT name, id FROM movie";
        }
        // Series SQL
        else if($type === "book"){
            // Check DB for picked date
            $sql = "SELECT name, id FROM book";
        }

        // Start SQL query
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            $error = true;
        }
        else{
            // Bind inputs to query parameters
            //mysqli_stmt_bind_param($stmt, "s", $name);
            // Execute sql statement
            mysqli_stmt_execute($stmt);
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $entertainment_name, $entertainment_id);
            // Results fetched below...
            if(mysqli_stmt_store_result($stmt)){
                // Check if DB returned any result
                if(mysqli_stmt_num_rows($stmt) > 0){
                    // Fetch values
                    //$gameArray = [];
                    while (mysqli_stmt_fetch($stmt)) {
                        //array_push($gameArray, array('value' => htmlspecialchars($work_happiness), 'name' => $work_happiness));
                        $gameArray[] = array(
                            'id' =>htmlspecialchars($entertainment_id),
                            'desc' => $entertainment_name,
                            );
                    }
                    exit(json_encode($gameArray));
                    
                }
            }
        } 
        
    }
?>

<?php
    // AJAX request handler for adding new entertainments to DB

    // define variables and set to empty values
    $new_entertainment_name = "";
    $addEntertainmentErrorText = "";
    $id = -1;

    // Check request type and sumbitted form
    if ($_SERVER["REQUEST_METHOD"] === "POST"
        && !isset($_POST["write-submit"])
        && isset($_POST["name"])
        && isset($_POST["type"])) {

        // Get entertainment type
        $entertainment_type = $_POST["type"];
        // Security operations on text
        $new_entertainment_name = test_input($_POST["name"]);
        // Encoding change
        $new_entertainment_name = mb_convert_encoding($new_entertainment_name, "UTF-8");

        // Save entertainment into DB by types
        switch($entertainment_type){
            case "game":
                $sql = "INSERT INTO game (name) VALUES (?)";
                break;
            case "series":
                $sql = "INSERT INTO series (name) VALUES (?)";
                break;
            case "movie":
                $sql = "INSERT INTO movie (name) VALUES (?)";
                break;
            case "book":
                $sql = "INSERT INTO book (name) VALUES (?)";
                break;
            default:
                $addEntertainmentErrorText = "Undefined entertainment type!";
                http_response_code(400);
                exit($addEntertainmentErrorText);
                break;
        }

        $stmt = mysqli_stmt_init($conn);
        // DB error check
        if(!mysqli_stmt_prepare($stmt, $sql)){
            $addEntertainmentErrorText = mysqli_error($conn);
            http_response_code(400);
            exit($addEntertainmentErrorText);
        }
        else{
            // Bind inputs to query parameters
            mysqli_stmt_bind_param($stmt, "s", $new_entertainment_name);
            // Execute sql statement
            if(mysqli_stmt_execute($stmt)){
                // Get new entertainment's id
                switch($entertainment_type){
                    case "game":
                        $sql = "SELECT id FROM game WHERE name=?";
                        break;
                    case "series":
                        $sql = "SELECT id FROM series WHERE name=?";
                        break;
                    case "movie":
                        $sql = "SELECT id FROM movie WHERE name=?";
                        break;
                    case "book":
                        $sql = "SELECT id FROM book WHERE name=?";
                        break;
                    default:
                        $addEntertainmentErrorText = "Undefined entertainment type!";
                        http_response_code(400);
                        exit($addEntertainmentErrorText);
                        break;
                }
                $stmt = mysqli_stmt_init($conn);
                // Prepare SQL
                if(!mysqli_stmt_prepare($stmt, $sql)){
                    $addEntertainmentErrorText = mysqli_error($conn);
                    http_response_code(400);
                    exit($addEntertainmentErrorText);
                }
                else{
                    // Bind inputs to query parameters
                    mysqli_stmt_bind_param($stmt, "s", $new_entertainment_name);
                    // Execute sql statement
                    if(!mysqli_stmt_execute($stmt)){
                        $addEntertainmentErrorText = mysqli_error($conn);
                        http_response_code(400);
                        exit($addEntertainmentErrorText);
                    }
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id);
                    // Results fetched
                    if(mysqli_stmt_store_result($stmt)){
                        // Check if DB returned any result - Same day entry check
                        if(mysqli_stmt_num_rows($stmt) > 0){
                            // Fetch values
                            while (mysqli_stmt_fetch($stmt)) {
                                $response = array(
                                    'id' => $id,
                                    'desc' => $new_entertainment_name,
                                );
                                // Return id and name in JSON
                                exit(json_encode($response));
                            }
                        }
                        else{
                            $addEntertainmentErrorText = mysqli_error($conn);
                            http_response_code(400);
                            exit($addEntertainmentErrorText);
                        }
                    }
                }
            }
            else{
                $addEntertainmentErrorText = mysqli_error($conn);
                http_response_code(400);
                exit($addEntertainmentErrorText);
            }
        }
    }
?>

<?php
    // AJAX request handler for deleting entertainments from DB

    // define variables and set to empty values
    $addEntertainmentErrorText = "";

    // Check request type and sumbitted form
    if ($_SERVER["REQUEST_METHOD"] === "POST"
        && !isset($_POST["write-submit"])
        && isset($_POST["id"])
        && isset($_POST["type"])) {

        // Get entertainment type
        $entertainment_type = $_POST["type"];
        // Security operations on text
        $daily_entertainment_id = test_input($_POST["id"]);

        // Save entertainment into DB by types
        switch($entertainment_type){
            case "game":
                $sql = "DELETE FROM daily_game WHERE id=(?)";
                break;
            case "series":
                $sql = "DELETE FROM daily_series WHERE id=(?)";
                break;
            case "movie":
                $sql = "DELETE FROM daily_movie WHERE id=(?)";
                break;
            case "book":
                $sql = "DELETE FROM daily_book WHERE id=(?)";
                break;
            default:
                $addEntertainmentErrorText = "Undefined entertainment type!";
                http_response_code(400);
                exit($addEntertainmentErrorText);
                break;
        }

        $stmt = mysqli_stmt_init($conn);
        // DB error check
        if(!mysqli_stmt_prepare($stmt, $sql)){
            $addEntertainmentErrorText = mysqli_error($conn);
            http_response_code(400);
            exit($addEntertainmentErrorText);
        }
        else{
            // Bind inputs to query parameters
            mysqli_stmt_bind_param($stmt, "i", $daily_entertainment_id);
            // Execute sql statement
            if(mysqli_stmt_execute($stmt)){
                // Return success
                http_response_code(200);
                exit("success"); 
            }
            else{
                $addEntertainmentErrorText = mysqli_error($conn);
                http_response_code(400);
                exit($addEntertainmentErrorText);
            }
        }
    }
?>

<?php 
    require "header.php";
    // Check if the session variable name is empty or not and redirect
    if ($_SERVER["REQUEST_METHOD"] === "GET"
        && !isset($_SESSION['name'])) {
        exit("<script>location.href = './index.php';</script>"); 
    }
?>

<?php 
    // GET selected date data & UPDATE gunluk database with new data

    // define variables and set to empty values
    $journal_id = "";
    $work_happiness = $daily_happiness = $total_happiness = $content = "";
    $date = "";
    $game_id = $game_name = $game_duration = "";
    $series_id = $series_name = $series_begin_season = $series_begin_episode = $series_end_season = $series_end_episode = "";
    $movie_id = $movie_name = $movie_duration = "";
    $book_id = $book_name = $book_duration = "";
    $error = false;
    $success = false;
    $isDatePicked = false;
    $errorText = "";
    // Get name from session
    $name = $_SESSION['name'];
    // Check if name is empty or not and redirect
    if($name == "" || $name == NULL)      
        echo("<script>location.href = './index.php';</script>"); 


    // Check request method for post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Date picker form
        if(isset($_POST['edit-date'])){
            $date = test_input($_POST["edit-date"]);
            if(!empty($date)){
                $isDatePicked = true;

                // Check DB for picked date
                $sql = "SELECT id, work_happiness, daily_happiness, total_happiness, content 
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
                    mysqli_stmt_bind_result($stmt, $journal_id, $work_happiness, $daily_happiness, $total_happiness, $content);
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
                $sql = "SELECT id, work_happiness, daily_happiness, total_happiness, content 
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
                    mysqli_stmt_bind_result($stmt, $journal_id, $work_happiness, $daily_happiness, $total_happiness, $content);
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
<main class="main" style="min-height: 91vh;">    

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
            class="btn btn-primary bg-info"
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
                    <input type="text" name="date" value="'.$date.'" hidden/>';
            }

            ?>
            <hr>
            <button 
                type="button" 
                class="btn btn-success"
                id="newEntertainmentMenuButton"
                onclick="$('#newEntertainmentMenu').css({ display: 'inline' });
                $('#newEntertainmentMenuButton').css({ display: 'none' });">
                Yeni Eğlence Ekle
            </button>
            <div id="newEntertainmentMenu" style="display: none;">

                <!--Daily Entertainment: Playing Games-->
                <div class="daily-game">
                    <button type="button"
                            class="btn btn-info"
                            id="add-game-btn"
                            onclick="getEntertainmentNames('game');">
                            Oyun Ekle
                    </button>
                    
                    <div id="add-game" style="display:none;">
                        <!--Add a game, name & duration-->
                        <div class="row">
                            <div class="col-xs-3 col-sm-6">
                                <select name="game-select"
                                        id="game-select" 
                                        class="custom-select"
                                        onchange="openNewEntertainmentModal('game')">
                                    <option value="0" hidden selected>Hangi oyunu oynadın?</option>
                                    <option value="" class="opt10">YENi OYUN EKLE</option>
                                </select>
                            </div>
                            <div class="col-xs-3 col-sm-6">
                                <input 
                                    type="number" 
                                    name="game-duration" 
                                    placeholder="Süre (Saat)"
                                    id="game-duration"
                                    min="0"
                                    max="24"
                                    step="0.5"
                                    minlength="0"
                                    maxlength="2"
                                    style="width:45%;">
                            </div>
                        </div>
                        <!--Add a game to list & error messages-->
                        <div class="mx-auto" style="width:100%">
                            <button type="button"
                                    class="btn btn-info mt-2 mx-auto"
                                    onclick="addToTheList('game')">
                                    Ekle
                            </button>
                        </div>
                        <p id="game-add-error" 
                                class="error mx-auto" 
                                style="display:none;">
                                Oyun adı ya da süresi uygun değil.
                        </p>
                        <p id="game-exist-error" 
                                class="error mx-auto" 
                                style="display:none;">
                                Oyun zaten var, silip tekrar ekleyebilirsin.
                        </p>
                        <!--Game list-->
                        <ul id="game-list" class="mb-0 px-3"></ul>
                    </div>

                    <div id="get-game-names-error" style="display: none;">
                        <p class="error">ERROR (AJAX): Couldn't get game names from server.</p>
                    </div>
                </div>

                <hr>

                <!--Daily Entertainment: Watching Series-->
                <div class="daily-series">
                    <button type="button"
                            class="btn btn-primary"
                            id="add-series-btn"
                            onclick="getEntertainmentNames('series');">
                            Dizi Ekle
                    </button>
                    
                    <div id="add-series" style="display:none;">
                        <!--Add a series, name & episodes-->
                        <select name="series-select"
                                id="series-select" 
                                class="custom-select" 
                                onchange="openNewEntertainmentModal('series')">
                            <option value="0" hidden selected>Hangi diziyi seyrettin?</option>
                            <option value="" class="opt10">YENi DİZİ EKLE</option>
                        </select>
                        <div class="row">
                            <div class="col-xs-3 col-sm-6">
                                <p>Başlangıç:</p>
                                <input 
                                    type="number" 
                                    name="series-season-begin" 
                                    placeholder="Sezon (İlk izlenen)"
                                    id="series-season-begin"
                                    min="0"
                                    max="50"
                                    step="1"
                                    minlength="0"
                                    maxlength="2"
                                    style="width:45%;">
                                <input 
                                    type="number" 
                                    name="series-episode-begin" 
                                    placeholder="Bölüm (İlk izlenen)"
                                    id="series-episode-begin"
                                    min="0"
                                    max="50"
                                    step="1"
                                    minlength="0"
                                    maxlength="2"
                                    style="width:45%;">
                            </div>
                            <div class="col-xs-3 col-sm-6">
                                <p>Bitiş:</p>
                                <input 
                                    type="number" 
                                    name="series-season-end" 
                                    placeholder="Sezon (Son izlenen)"
                                    id="series-season-end"
                                    min="0"
                                    max="50"
                                    step="1"
                                    minlength="0"
                                    maxlength="2"
                                    style="width:45%;">
                                <input 
                                    type="number" 
                                    name="series-episode-end" 
                                    placeholder="Bölüm (Son izlenen)"
                                    id="series-episode-end"
                                    min="0"
                                    max="50"
                                    step="1"
                                    minlength="0"
                                    maxlength="2"
                                    style="width:45%;">
                            </div>
                        </div>
                        <!--Add a series to list & error messages-->
                        <div class="mx-auto" style="width:100%">
                            <button type="button"
                                    class="btn btn-primary mt-2 mx-auto"
                                    onclick="addToTheList('series')">
                                    Ekle
                            </button>
                        </div>
                        <p id="series-add-error" 
                                class="error mx-auto" 
                                style="display:none;">
                                Dizi adı ya da bölümleri uygun değil. <br>
                                Başlangıç sezon ve/veya bölüm sayısı bitiş sayılarından büyük olamaz.
                        </p>
                        <p id="series-exist-error" 
                                class="error mx-auto" 
                                style="display:none;">
                                Dizi zaten var, silip tekrar ekleyebilirsin.
                        </p>
                        <!--Series list-->
                        <ul id="series-list" class="mb-0 px-3"></ul>
                    </div>

                    <div id="get-series-names-error" style="display: none;">
                        <p class="error">ERROR (AJAX): Couldn't get series names from server.</p>
                    </div>
                </div>

                <hr>

                <!--Daily Entertainment: Watching movies-->
                <div class="daily-movie">
                    <button type="button"
                            class="btn btn-secondary"
                            id="add-movie-btn"
                            onclick="getEntertainmentNames('movie');">
                            Film Ekle
                    </button>
                    
                    <div id="add-movie" style="display:none;">
                        <!--Add a movie, name & duration-->
                        <div class="row">
                            <div class="col-xs-3 col-sm-6">
                                <select name="movie-select"
                                        id="movie-select" 
                                        class="custom-select" 
                                        onchange="openNewEntertainmentModal('movie')">
                                    <option value="0" hidden selected>Hangi filmi seyrettin?</option>
                                    <option value="" class="opt10">YENI FILM EKLE</option>
                                </select>
                            </div>
                            <div class="col-xs-3 col-sm-6">
                                <input 
                                    type="number" 
                                    name="movie-duration" 
                                    placeholder="Süre (Saat)"
                                    id="movie-duration"
                                    min="0"
                                    max="24"
                                    step="0.5"
                                    minlength="0"
                                    maxlength="2"
                                    style="width:45%;">
                            </div>
                        </div>
                        <!--Add a movie to list & error messages-->
                        <div class="mx-auto" style="width:100%">
                            <button type="button"
                                    class="btn btn-secondary mt-2 mx-auto"
                                    onclick="addToTheList('movie')">
                                    Ekle
                            </button>
                        </div>
                        <p id="movie-add-error" 
                                class="error mx-auto" 
                                style="display:none;">
                                Film adı ya da süresi uygun değil.
                        </p>
                        <p id="movie-exist-error" 
                                class="error mx-auto" 
                                style="display:none;">
                                Film zaten var, silip tekrar ekleyebilirsin.
                        </p>
                        <!--Movie list-->
                        <ul id="movie-list" class="mb-0 px-3"></ul>
                    </div>

                    <div id="get-movie-names-error" style="display: none;">
                        <p class="error">ERROR (AJAX): Couldn't get movie names from server.</p>
                    </div>
                </div>

                <hr>

                <!--Daily Entertainment: Book Reading-->
                <div class="daily-book">
                    <button type="button"
                            class="btn btn-warning"
                            id="add-book-btn"
                            onclick="getEntertainmentNames('book');">
                            Kitap Ekle
                    </button>
                    
                    <div id="add-book" style="display:none;">
                        <!--Add a book, name & duration-->
                        <div class="row">
                            <div class="col-xs-3 col-sm-6">
                                <select name="book-select"
                                        id="book-select" 
                                        class="custom-select" 
                                        onchange="openNewEntertainmentModal('book')">
                                    <option value="0" hidden selected>Hangi kitabi okudun?</option>
                                    <option value="" class="opt10">YENI KITAP EKLE</option>
                                </select>
                            </div>
                            <div class="col-xs-3 col-sm-6">
                                <input 
                                    type="number" 
                                    name="book-duration" 
                                    placeholder="Süre (Saat)"
                                    id="book-duration"
                                    min="0"
                                    max="24"
                                    step="0.5"
                                    minlength="0"
                                    maxlength="2"
                                    style="width:45%;">
                            </div>
                        </div>
                        <!--Add a book to list & error messages-->
                        <div class="mx-auto" style="width:100%">
                            <button type="button"
                                    class="btn btn-warning mt-2 mx-auto"
                                    onclick="addToTheList('book')">
                                    Ekle
                            </button>
                        </div>
                        <p id="book-add-error" 
                                class="error mx-auto" 
                                style="display:none;">
                                Kitap adı ya da süresi uygun değil.
                        </p>
                        <p id="book-exist-error" 
                                class="error mx-auto" 
                                style="display:none;">
                                Kitap zaten var, silip tekrar ekleyebilirsin.
                        </p>
                        <!--Book list-->
                        <ul id="book-list" class="mb-0 px-3"></ul>
                    </div>

                    <div id="get-book-names-error" style="display: none;">
                        <p class="error">ERROR (AJAX): Couldn't get book names from server.</p>
                    </div>
                </div>

            </div>
            <hr>
            <?php

            // Get daily game data from DB
            $sql_game = "SELECT daily_game.id, name, duration
                        FROM daily_game
                        INNER JOIN game ON daily_game.game_id=game.id 
                        WHERE gunluk_id=? ORDER BY daily_game.id ASC";
            $stmt_game = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt_game, $sql_game)){
                $error = true;
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt_game, "i", $journal_id);
                // Execute sql statement
                if(!mysqli_stmt_execute($stmt_game)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Bind result variables
                mysqli_stmt_bind_result($stmt_game, $game_id, $game_name, $game_duration);
                // Game Results fetched below...
                if(mysqli_stmt_store_result($stmt_game)){
                    // Check if DB returned any result
                    if(mysqli_stmt_num_rows($stmt_game) > 0){
                        echo '<table id="game-table" class="table table-bordered table-hover table-sm table-striped">';
                        echo '<tr class="table-info"><th>Oyun</th><th>Süre</th><th>Sil</th></tr>';
                        // Fetch values
                        while (mysqli_stmt_fetch($stmt_game)) {
                            echo '<tr id="game-row-'.$game_id.'">
                                    <td>'.$game_name.'</td>
                                    <td>'.$game_duration.' Saat</td>
                                    <td style="width: fit-content;">
                                        <div class="remove-button">
                                            <button onclick="deleteEntertaimmentFromDB(\'game\', '.$game_id.')" 
                                                    type="button" 
                                                    class="btn btn-danger mx-auto" 
                                                    style="width: fit-content;">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div class="success" style="display:none;">
                                            <i class="fa fa-check-circle-o" aria-hidden="true"></i>     
                                            <span>Silindi</span>
                                        </div>
                                        <div class="error" style="display:none;">
                                            <i class="fa fa-times-circle-o" aria-hidden="true"></i>     
                                            <span>Silinemedi!</span>
                                            <p class="error-msg"></p>
                                        </div>
                                    </td>
                                </tr>';
                        }
                        echo '</table>';
                    }
                }
                else{
                    echo'<!--Error-->
                    <div>
                    <p id="dbError" class="error">Oyunlar için veritabanı \'store\' hatası.</p>
                    </div>';
                }
            }

            // Get daily series data from DB
            $sql_series = "SELECT daily_series.id, name, begin_season, begin_episode, end_season, end_episode
                        FROM daily_series
                        INNER JOIN series ON daily_series.series_id=series.id 
                        WHERE gunluk_id=? ORDER BY daily_series.id ASC";
            $stmt_series = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt_series, $sql_series)){
                $error = true;
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt_series, "i", $journal_id);
                // Execute sql statement
                if(!mysqli_stmt_execute($stmt_series)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Bind result variables
                mysqli_stmt_bind_result($stmt_series, $series_id, $series_name, $series_begin_season, $series_begin_episode, $series_end_season, $series_end_episode);
                // Series Results fetched below...
                if(mysqli_stmt_store_result($stmt_series)){
                    // Check if DB returned any result
                    if(mysqli_stmt_num_rows($stmt_series) > 0){
                        echo '<table id="series-table" class="table table-bordered table-hover table-sm table-striped">';
                        echo '<tr class="table-primary">
                                <th>Dizi</th>
                                <th>İlk sezon</th>
                                <th>İlk bölüm</th>
                                <th>Son sezon</th>
                                <th>Son bölüm</th>
                                <th>Sil</th></tr>';
                        // Fetch values
                        while (mysqli_stmt_fetch($stmt_series)) {
                            echo '<tr id="series-row-'.$series_id.'">
                                    <td>'.$series_name.'</td>
                                    <td>'.$series_begin_season.'</td>
                                    <td>'.$series_begin_episode.'</td>
                                    <td>'.$series_end_season.'</td>
                                    <td>'.$series_end_episode.'</td>
                                    <td style="width: fit-content;">
                                        <div class="remove-button">
                                            <button onclick="deleteEntertaimmentFromDB(\'series\', '.$series_id.')" 
                                                    type="button" 
                                                    class="btn btn-danger mx-auto" 
                                                    style="width: fit-content;">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div class="success" style="display:none;">
                                            <i class="fa fa-check-circle-o" aria-hidden="true"></i>     
                                            <span>Silindi</span>
                                        </div>
                                        <div class="error" style="display:none;">
                                            <i class="fa fa-times-circle-o" aria-hidden="true"></i>     
                                            <span>Silinemedi!</span>
                                            <p class="error-msg"></p>
                                        </div>
                                    </td>
                                </tr>';
                        }
                        echo '</table>';
                    }
                }
                else{
                    echo'<!--Error-->
                    <div>
                    <p id="dbError" class="error">Diziler için veritabanı \'store\' hatası.</p>
                    </div>';
                }
            }

            // Get daily movie data from DB
            $sql_movie = "SELECT daily_movie.id, name, duration
                        FROM daily_movie
                        INNER JOIN movie ON daily_movie.movie_id=movie.id 
                        WHERE gunluk_id=? ORDER BY daily_movie.id ASC";
            $stmt_movie = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt_movie, $sql_movie)){
                $error = true;
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt_movie, "i", $journal_id);
                // Execute sql statement
                if(!mysqli_stmt_execute($stmt_movie)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Bind result variables
                mysqli_stmt_bind_result($stmt_movie, $movie_id, $movie_name, $movie_duration);
                // Movie Results fetched below...
                if(mysqli_stmt_store_result($stmt_movie)){
                    // Check if DB returned any result
                    if(mysqli_stmt_num_rows($stmt_movie) > 0){
                        echo '<table id="movie-table" class="table table-bordered table-hover table-sm table-striped">';
                        echo '<tr class="table-success"><th>Film</th><th>Süre</th><th>Sil</th></tr>';
                        // Fetch values
                        while (mysqli_stmt_fetch($stmt_movie)) {
                            echo '<tr id="movie-row-'.$movie_id.'">
                                    <td>'.$movie_name.'</td>
                                    <td>'.$movie_duration.' Saat</td>
                                    <td style="width: fit-content;">
                                        <div class="remove-button">
                                            <button onclick="deleteEntertaimmentFromDB(\'movie\', '.$movie_id.')" 
                                                    type="button" 
                                                    class="btn btn-danger mx-auto" 
                                                    style="width: fit-content;">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div class="success" style="display:none;">
                                            <i class="fa fa-check-circle-o" aria-hidden="true"></i>     
                                            <span>Silindi</span>
                                        </div>
                                        <div class="error" style="display:none;">
                                            <i class="fa fa-times-circle-o" aria-hidden="true"></i>     
                                            <span>Silinemedi!</span>
                                            <p class="error-msg"></p>
                                        </div>
                                    </td>
                                </tr>';
                        }
                        echo '</table>';
                    }
                }
                else{
                    echo'<!--Error-->
                    <div>
                    <p id="dbError" class="error">Filmler için veritabanı \'store\' hatası.</p>
                    </div>';
                }
            }

            // Get daily book data from DB
            $sql_book = "SELECT daily_book.id, name, duration
                        FROM daily_book
                        INNER JOIN book ON daily_book.book_id=book.id 
                        WHERE gunluk_id=? ORDER BY daily_book.id ASC";
            $stmt_book = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt_book, $sql_book)){
                $error = true;
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt_book, "i", $journal_id);
                // Execute sql statement
                if(!mysqli_stmt_execute($stmt_book)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Bind result variables
                mysqli_stmt_bind_result($stmt_book, $book_id, $book_name, $book_duration);
                // Book Results fetched below...
                if(mysqli_stmt_store_result($stmt_book)){
                    // Check if DB returned any result
                    if(mysqli_stmt_num_rows($stmt_book) > 0){
                        echo '<table id="book-table" class="table table-bordered table-hover table-sm table-striped">';
                        echo '<tr class="table-warning"><th>Kitap</th><th>Süre</th><th>Sil</th></tr>';
                        // Fetch values
                        while (mysqli_stmt_fetch($stmt_book)) {
                            echo '<tr id="book-row-'.$book_id.'">
                                    <td>'.$book_name.'</td>
                                    <td>'.$book_duration.' Saat</td>
                                    <td style="width: fit-content;">
                                        <div class="remove-button">
                                            <button onclick="deleteEntertaimmentFromDB(\'book\', '.$book_id.')" 
                                                    type="button" 
                                                    class="btn btn-danger mx-auto" 
                                                    style="width: fit-content;">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div class="success" style="display:none;">
                                            <i class="fa fa-check-circle-o" aria-hidden="true"></i>     
                                            <span>Silindi</span>
                                        </div>
                                        <div class="error" style="display:none;">
                                            <i class="fa fa-times-circle-o" aria-hidden="true"></i>     
                                            <span>Silinemedi!</span>
                                            <p class="error-msg"></p>
                                        </div>
                                    </td>
                                </tr>';
                        }
                        echo '</table>';
                    }
                }
                else{
                    echo'<!--Error-->
                    <div>
                    <p id="dbError" class="error">Kitaplar için veritabanı \'store\' hatası.</p>
                    </div>';
                }
            }

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
            class="btn btn-primary bg-info"
            aria-pressed="false"
          />
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
                    
                    <!--Success-->
                    <div id="add-entertainment-success" style="display: none;">
                        <p class="success"><span class="entertaintment-type"></span> başarılı bir şekilde eklendi. Lütfen bekleyin...</p>
                    </div>

                    <!--Error-->
                    <div id="add-entertainment-error" style="display: none;">
                        <p class="error">Hata meydana geldi. <span id="add-entertainment-error-text"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-success" id="add-entertainment-btn" onclick="addNewEntertainment('game');">Ekle</button>
                </div>
            </div>
        </div>
    </div>

</main>

<?php
    require "footer.php";
?>