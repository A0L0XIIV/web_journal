<?php
    // Entertainment AJAX request handler

    // Database connection
    require "./mysqli_connect.php";

    // Variables
    $entertainment_name = $entertainment_id = "";
    
    if ($_SERVER["REQUEST_METHOD"] === "POST"
        && isset($_POST['type'])
        && !isset($_POST["write-submit"])) {
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
?>

<?php 
    require "header.php";
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
    $id = 0;

    // Database connection
    //require "./mysqli_connect.php";

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
                            $success = true;
/*
                            // Add entertainment (daily game, series, movie and book in DB)
                            // Get today's gunluk id
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
                                    // Check if DB returned any result - Got today's id
                                    if(mysqli_stmt_num_rows($stmt) > 0){
                                        echo $_POST["game"];
                                        $daily_game_length = sizeof($_POST["game"]);
                                        echo $daily_game_length;
                                        // Get daily game id and duration
                                        $daily_game_id = $_POST["game"];
                                    }
                                    // Not found any same day entry - Error
                                    else{
                                        $error = true;
                                        $errorText = "Günlük oyun, dizi, film ve kitap ekleme başarısız.";
                                    }
                                }
                            }*/
                        }
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
                                onchange="addNewEntertainmentToDB('game')">
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

            <!-- Modal: Add new game into database -->
            <div class="modal fade" id="add-game-modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Yeni oyun ekle</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="add-game-form"
                                id="add-game-form"
                                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
                                method="post">
                                <input type="text" name="add-new-game-name" id="add-new-game-name">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Kapat</button>
                            <button type="submit" class="btn btn-success">Ekle</button>
                        </div>
                    </div>
                </div>
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
                        onchange="addNewEntertainmentToDB('series')">
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
                        Dizi adı ya da bölümleri uygun değil.
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
                                onchange="addNewEntertainmentToDB('movie')">
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
                                onchange="addNewEntertainmentToDB('book')">
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

</main>

<?php
    require "footer.php";
?>