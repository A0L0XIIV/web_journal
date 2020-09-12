<?php
    // Entertainment AJAX request handler

    // Database connection
    require "./mysqli_connect.php";

    // Variables
    $entertainment_name = $entertainment_id = "";
    
    if ($_SERVER["REQUEST_METHOD"] === "POST"
        && !isset($_POST["write-submit"])
        && !isset($_POST['name'])
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
    require "header.php";
    // Check if the session variable name is empty or not and redirect
    if ($_SERVER["REQUEST_METHOD"] === "GET"
        && !isset($_SESSION['name'])) {
        exit("<script>location.href = './index.php';</script>"); 
    }
?>

<?php
    // New Journal Entry POST Request Handler

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
            placeholder="En fazla 1000 harf"
        ></textarea>

        <hr>

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
                            <option value="">YENi OYUN EKLE</option>
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
                    <option value="">YENi DİZİ EKLE</option>
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
                            <option value="">YENI FILM EKLE</option>
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
                            <option value="">YENI KITAP EKLE</option>
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

        <hr>

        <!--Input for submitting the form, type=submit-->
        <input type="text" value="" name="date" id="date-input" hidden />

        <!--Input for submitting the form, type=submit-->
        <div>
          <input
            type="submit"
            value="Gönder"
            name="write-submit"
            class="btn btn-success bg-success"
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