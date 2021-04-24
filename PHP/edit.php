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

    // Database connection
    require "./mysqli_connect.php";

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
    $successVerb = "güncellendi";

    // Get name from session
    $name = $_SESSION['name'];
    // Check if name is empty or not and redirect
    if($name == "" || $name == NULL)      
        echo("<script>location.href = './index.php';</script>"); 

    // Check request method for GET for date picker form
    if ( $_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['edit-date'])){
        // Get date parameter from request
        $date = test_input($_GET["edit-date"]);
        // Check date emptiness
        if(!empty($date)){
            // This will activate second section of the page: show&edit part
            $isDatePicked = true;
        }
    }

    // Check request method for GET for not found error
    else if ( $_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["error"])){
        $isDatePicked = false;
        $error = true;
        if($_GET["error"] === "not-found")
            $errorText = "Bu tarihli günlük bulunamadı.";
    }

    // Check request method for POST: form submit
    else if($_SERVER["REQUEST_METHOD"] === "POST"
            && isset($_POST['journal_id']) 
            && !empty($_POST['journal_id'])){

        // Get hidden journal id & date values
        $journal_id = test_input($_POST['journal_id']);
        $date = test_input($_POST['date']);

        // Edit journal form
        if(isset($_POST['content'])){
            // Do not return to date form
            $isDatePicked = true;
            // Security operations on text
            $content = test_input($_POST["content"]);
            // Encoding change
            $content = mb_convert_encoding($content, "UTF-8");
            // Get happiness values
            $work_happiness = $_POST["work_happiness"];
            $daily_happiness = $_POST["daily_happiness"];
            $total_happiness = $_POST["total_happiness"];

            // Update journal in DB
            $sql = "UPDATE gunluk 
                    SET work_happiness=?, daily_happiness=?, total_happiness=?, content=? 
                    WHERE id=?";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)){
                $error = true;
                $errorText = mysqli_error($conn);
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "iiisi", $work_happiness, $daily_happiness, 
                                        $total_happiness, $content, $journal_id);
                // Execute sql statement
                if(mysqli_stmt_execute($stmt))
                    $success = true;
                else{
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
            }
        }

        // Edit entertainment forms
        else if(isset($_POST['game']) 
            || isset($_POST['series']) 
            || isset($_POST['movie']) 
            || isset($_POST['book'])){
            // Do not return to date form
            $isDatePicked = true; 
            
            // Check hidden journal id input's value
            if(!empty($_POST['journal_id'])){

                // Add new game into daily_game
                if(isset($_POST["game"])){
                    if(daily_entertainment("game", $journal_id, $conn)){
                        $success = true;
                    }
                    else{    
                        $error = true;
                        $errorText .= "Günlük film ekleme başarısız.\n";
                    }
                }
                // Add new series into daily_series
                if(isset($_POST["series"])){
                    if(daily_entertainment("series", $journal_id, $conn)){
                        $success = true;
                    }
                    else{    
                        $error = true;
                        $errorText .= "Günlük dizi ekleme başarısız.\n";
                    }
                }
                // Add new movie into daily_movie
                if(isset($_POST["movie"])){
                    if(daily_entertainment("movie", $journal_id, $conn)){
                        $success = true;
                    }
                    else{    
                        $error = true;
                        $errorText .= "Günlük film ekleme başarısız.\n";
                    }
                }
                // Add new book into daily_book
                if(isset($_POST["book"])){
                    if(daily_entertainment("book", $journal_id, $conn)){
                        $success = true;
                    }
                    else{    
                        $error = true;
                        $errorText .= "Günlük kitap ekleme başarısız.\n";
                    }
                }
            }
            // Empty journal_id, show error
            else{    
                $error = true;
                $errorText .= "Günlük hobi ekleme başarısız.\n";
            }
        }

        // Fill journal
        $sql = "SELECT work_happiness, daily_happiness, total_happiness, content, gunluk.date 
                FROM gunluk WHERE id=?";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            $error = true;
        }
        else{
            // Bind inputs to query parameters
            mysqli_stmt_bind_param($stmt, "i", $journal_id);
            // Execute sql statement
            mysqli_stmt_execute($stmt);
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $work_happiness, $daily_happiness, $total_happiness, $content, $date);
            // Results fetched below...
        }
    }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function daily_entertainment($type, $gunluk_id, $conn){
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
                mysqli_stmt_bind_param($stmt, "iiiiii", $gunluk_id, $entertainment_id, $begin_season,
                                        $begin_episode, $end_season, $end_episode);
            }
            // game, movie and book duration
            else{
                // Extract int from duration
                $entertainment_duration = rtrim($entertainment['duration'],'S');
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "iid", $gunluk_id, $entertainment_id, $entertainment_duration);
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
<main class="main" style="min-height: 91vh;"> 

    <br>
    <?php
    // Success
    if($success) {
        // Print success message
        require "./msg/success.php";
    }

    // Error
    if($error) {
        // Print error message
        require "./msg/error.php";
    }

    // First (section) form: Date selection
    if(!$isDatePicked){
        echo'
        <form name="date-form"
            id="date-form"
            action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'"
            method="get">

            <h1>Güncelleme tarihi seçiniz:</h1>

            <div class="input-group mb-3 justify-content-center">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="day-label">Gün</span>
                </div>
                <input type="date" name="edit-date" required>
            </div>

            <hr>

            <!--Button for submitting the form-->
            <div>
            <button
                type="submit"
                class="sbmt-btn bg-edit"
                aria-pressed="false">
                Gönder
            </button>
            </div>

            <br>
        </form>';
    }


    // Second (section) form cluster: journal and entertainment update forms
    else{
        echo'<div>
            <form
                name="edit-form"
                id="edit-form"
                action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'"
                method="post">

                <h1 class="orange-text">';
                    if(isset($_SESSION['name'])){
                        echo $_SESSION['name'].', ';
                    }
                    echo $date.' tarihili günlüğün
                </h1>

                <hr>';

                // Get journal data from DB
                $sql = "SELECT id, work_happiness, daily_happiness, total_happiness, content 
                        FROM gunluk WHERE name=? AND date LIKE ?";
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sql)){
                    $error = true;
                    $errorText = mysqli_error($conn);
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
                    // Store results
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
                                        placeholder="max 1000 karakter"
                                    >'.(!empty($content) ? $content : "").'</textarea>
                                    <p id="content-count" class="text-right" style="width: 90%;"></p>
                                    <script>
                                        $("#content").keyup(function(){
                                            var count = $(this).val().length;
                                            var remain = 1000 - count;

                                            $("#content-count").text("Kalan karakter: " + remain);
                                            if(window.matchMedia(\'(prefers-color-scheme: dark)\').matches)
                                                $("#content-count").css("color", "rgb(255," + remain/4 + "," + remain/4 + ")");
                                            else
                                                $("#content-count").css("color", "rgb(" + count/4 + ",0,0)");
                                        });
                                    </script>
                                    
                                    <input type="number" name="journal_id" value="'.$journal_id.'" hidden/>
                                    <input type="text" name="date" value="'.$date.'" hidden/>';
                            }
                        }
                        else{
                            exit("<script>location.href = './edit.php?error=not-found';</script>");
                        }
                    }
                    else {
                        echo'<!--Error-->
                            <div class="error" id="dbError">
                                <p>Veritabanı \'store\' hatası. 
                                    <button type="button"
                                        class="fa fa-times-circle btn text-danger" 
                                        aria-hidden="true" 
                                        onclick="$(\'#dbError\').hide()">
                                    </button>
                                </p> 
                            </div>';
                    }
                }

                echo'
                <!--Button for submitting the form-->
                <div>
                <button
                    type="submit"
                    class="sbmt-btn bg-edit"
                    aria-pressed="false">
                    Gönder
                </button>
                </div>

            </form>

            <br>
            <hr>

            <h2 class="orange-text">Aynı tarihli ('.$date.') hobilerin</h2>

            <br>';

            // Get daily game data from DB
            $sql_game = "SELECT daily_game.id, name, duration
                        FROM daily_game
                        INNER JOIN game ON daily_game.game_id=game.id 
                        WHERE gunluk_id=? ORDER BY daily_game.id ASC";
            $stmt_game = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt_game, $sql_game)){
                $error = true;
                $errorText = mysqli_error($conn);
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
                        echo '<tr class="bg-game"><th>Oyun</th><th>Süre</th><th>Sil</th></tr>';
                        // Fetch values
                        while (mysqli_stmt_fetch($stmt_game)) {
                            echo '<tr id="game-row-'.$game_id.'">
                                    <td>'.$game_name.'</td>
                                    <td>'.$game_duration.' Saat</td>
                                    <td style="width: fit-content;">
                                        <div class="remove-button">
                                            <button onclick="deleteEntertaimmentFromDB(\'game\', '.$game_id.')" 
                                                    type="button" 
                                                    class="btn bg-logout mx-auto" 
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
                        <div class="error" id="dbError">
                            <p>Oyunlar için veritabanı \'store\' hatası. 
                                <button type="button"
                                    class="fa fa-times-circle btn text-danger" 
                                    aria-hidden="true" 
                                    onclick="$(\'#dbError\').hide()">
                                </button>
                            </p> 
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
                $errorText = mysqli_error($conn);
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
                        echo '<tr class="bg-series">
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
                                                    class="btn bg-logout mx-auto" 
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
                $errorText = mysqli_error($conn);
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
                        echo '<tr class="bg-movie"><th>Film</th><th>Süre</th><th>Sil</th></tr>';
                        // Fetch values
                        while (mysqli_stmt_fetch($stmt_movie)) {
                            echo '<tr id="movie-row-'.$movie_id.'">
                                    <td>'.$movie_name.'</td>
                                    <td>'.$movie_duration.' Saat</td>
                                    <td style="width: fit-content;">
                                        <div class="remove-button">
                                            <button onclick="deleteEntertaimmentFromDB(\'movie\', '.$movie_id.')" 
                                                    type="button" 
                                                    class="btn bg-logout mx-auto" 
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
                $errorText = mysqli_error($conn);
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
                        echo '<tr class="bg-book"><th>Kitap</th><th>Süre</th><th>Sil</th></tr>';
                        // Fetch values
                        while (mysqli_stmt_fetch($stmt_book)) {
                            echo '<tr id="book-row-'.$book_id.'">
                                    <td>'.$book_name.'</td>
                                    <td>'.$book_duration.' Saat</td>
                                    <td style="width: fit-content;">
                                        <div class="remove-button">
                                            <button onclick="deleteEntertaimmentFromDB(\'book\', '.$book_id.')" 
                                                    type="button" 
                                                    class="btn bg-logout mx-auto" 
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

            echo '
            <hr>
            <button 
                type="button"
                class="ent-btn my-4"
                id="newEntertainmentMenuButton"
                onclick="$(\'#newEntertainmentMenu\').show();
                        $(\'#newEntertainmentMenuButton\').hide();">
                Yeni Hobi Ekle
            </button>

            <div id="newEntertainmentMenu" style="display: none;">
                <h3 class="orange-text">Eklemek için aşağıdan yeni hobi seç</h3>

                <!--Daily Entertainment: Playing Games-->
                <div class="daily-game">
                    <button type="button"
                            class="ent-btn bg-game my-4"
                            id="add-game-btn"
                            onclick="getEntertainmentNames(\'game\');">
                            Oyun Ekle
                    </button>
                    
                    <div id="add-game" class="py-3" style="display:none; background-color:#2bc5001a;">
                        <form
                            name="edit-game-form"
                            id="edit-game-form"
                            action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'"
                            method="post">

                            <p class="font-weight-bolder">Oyun Ekle</p>
                            <!--Add a game, name & duration-->
                            <div class="row">
                                <div class="col-xs-3 col-sm-6">
                                    <select name="game-select"
                                            id="game-select" 
                                            class="custom-select"
                                            onchange="openNewEntertainmentModal(\'game\')">
                                        <option value="-1" hidden selected>Hangi oyunu oynadın?</option>
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
                                        class="add-btn bg-game mt-2"
                                        id="asd"
                                        onclick="addToTheList(\'game\'); 
                                                $(\'#edit-game-form-submit\').show();
                                                highlight(\'#edit-game-form-submit\');">
                                        Ekle
                                </button>
                            </div>

                            <div id="game-add-error" class="error mt-3" style="display:none;">
                                <!--game-add-error-->
                                <p>Oyun adı ya da süresi uygun değil. 
                                    <button type="button"
                                        class="fa fa-times-circle btn text-danger" 
                                        aria-hidden="true" 
                                        onclick="$(\'#game-add-error\').hide()">
                                    </button>
                                </p> 
                            </div>
                            <div id="game-exist-error" class="error mt-3" style="display:none;">
                                <!--game-exist-error-->
                                <p>Oyun zaten var, silip tekrar ekleyebilirsin. 
                                    <button type="button"
                                        class="fa fa-times-circle btn text-danger" 
                                        aria-hidden="true" 
                                        onclick="$(\'#game-exist-error\').hide()">
                                    </button>
                                </p> 
                            </div>

                            <!--Game list-->
                            <ul id="game-list" class="mb-0 px-3"></ul>

                            <input type="number" name="journal_id" value="'.$journal_id.'" hidden/>
                            <input type="text" name="date" value="'.$date.'" hidden/>
                            
                            <!--Button for submitting the form-->
                            <div id="edit-game-form-submit"style="display:none;">
                                <br>
                                <button
                                    type="submit"
                                    class="sbmt-btn bg-edit"
                                    aria-pressed="false">
                                    Gönder
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="get-game-names-error" class="error mt-3" style="display:none;">
                        <!--get-game-names-error-->
                        <p>AJAX hatası. Oyun isimlerini sunucudan alamadık.  
                            <button type="button"
                                class="fa fa-times-circle btn text-danger" 
                                aria-hidden="true" 
                                onclick="$(\'#get-game-names-error\').hide()">
                            </button>
                        </p> 
                    </div>
                </div>

                <!--Daily Entertainment: Watching Series-->
                <div class="daily-series">
                    <button type="button"
                            class="ent-btn bg-series my-4"
                            id="add-series-btn"
                            onclick="getEntertainmentNames(\'series\');">
                            Dizi Ekle
                    </button>
                    
                    <div id="add-series" class="py-3" style="display:none; background-color:#5da2d81a;">
                        <form
                            name="edit-series-form"
                            id="edit-series-form"
                            action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'"
                            method="post">

                            <p class="font-weight-bolder">Dizi Ekle</p>
                            <!--Add a series, name & episodes-->
                            <div class="row">
                                <div class="col-xs-3 col-sm-6 mx-auto">
                                        <select name="series-select"
                                                id="series-select" 
                                                class="custom-select" 
                                                onchange="openNewEntertainmentModal(\'series\')">
                                            <option value="-1" hidden selected>Hangi diziyi seyrettin?</option>
                                            <option value="" class="opt10">YENi DİZİ EKLE</option>
                                        </select>
                                    </div>
                                <div id="last-episode-btn" class="col-xs-3 col-sm-6" style="display: none;">
                                    <button type="button" class="bg-series" onclick="getLastWatchedSeriesEpisode()">Son bölüm +1</button>
                                </div>
                            </div>

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
                                <!-- Watched only one season, the first and the last episodes are in the same season -->
                                <div class="col-xs-3 col-sm-6" id="series-episode-number">
                                    <p>Bölüm Sayısı:</p>
                                    <input 
                                        type="number" 
                                        name="series-watched-number" 
                                        placeholder="İzlenen bölüm sayısı"
                                        id="series-watched-number"
                                        min="0"
                                        max="50"
                                        step="1"
                                        minlength="0"
                                        maxlength="2"
                                        style="width:45%;">
                                    <button type="button" class="bg-series" style="width:45%" onclick="openLastEpisodeSeasonInputs()">Farklı sezon bölümleri</button>
                                </div>
                                <!-- Watched more than one season, the first and the last episodes are  not in the same season -->
                                <div class="col-xs-3 col-sm-6" id="series-last-episode" style="display: none;">
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
                                        class="add-btn bg-series mt-2"
                                        onclick="addToTheList(\'series\');
                                                $(\'#edit-series-form-submit\').show();
                                                highlight(\'#edit-series-form-submit\');">
                                        Ekle
                                </button>
                            </div>

                            <div id="series-add-error" class="error mt-3" style="display:none;">
                                <!--series-add-error-->
                                <p>Dizi adı ya da bölümleri uygun değil. <br>
                                    Başlangıç sezon ve/veya bölüm sayısı bitiş sayılarından büyük olamaz.
                                    <button type="button"
                                        class="fa fa-times-circle btn text-danger" 
                                        aria-hidden="true" 
                                        onclick="$(\'#series-add-error\').hide()">
                                    </button>
                                </p> 
                            </div>
                            <div id="series-exist-error" class="error mt-3" style="display:none;">
                                <!--series-exist-error-->
                                <p>Dizi zaten var, silip tekrar ekleyebilirsin. 
                                    <button type="button"
                                        class="fa fa-times-circle btn text-danger" 
                                        aria-hidden="true" 
                                        onclick="$(\'#series-exist-error\').hide()">
                                    </button>
                                </p> 
                            </div>

                            <!--Series list-->
                            <ul id="series-list" class="mb-0 px-3"></ul>

                            <input type="number" name="journal_id" value="'.$journal_id.'" hidden/>
                            <input type="text" name="date" value="'.$date.'" hidden/>

                            <!--Button for submitting the form-->
                            <div id="edit-series-form-submit"style="display:none;">
                                <br>
                                <button
                                    type="submit"
                                    class="sbmt-btn bg-edit"
                                    aria-pressed="false">
                                    Gönder
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="get-series-names-error" class="error mt-3" style="display:none;">
                        <!--get-series-names-error-->
                        <p>AJAX hatası. Dizi isimlerini sunucudan alamadık.  
                            <button type="button"
                                class="fa fa-times-circle btn text-danger" 
                                aria-hidden="true" 
                                onclick="$(\'#get-series-names-error\').hide()">
                            </button>
                        </p> 
                    </div>
                </div>

                <!--Daily Entertainment: Watching movies-->
                <div class="daily-movie">
                    <button type="button"
                            class="ent-btn bg-movie my-4"
                            id="add-movie-btn"
                            onclick="getEntertainmentNames(\'movie\');">
                            Film Ekle
                    </button>
                    
                    <div id="add-movie" class="py-3" style="display:none; background-color:#ff599a1a;">
                        <form
                            name="edit-movie-form"
                            id="edit-movie-form"
                            action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'"
                            method="post">

                            <p class="font-weight-bolder">Film Ekle</p>
                            <!--Add a movie, name & duration-->
                            <div class="row">
                                <div class="col-xs-3 col-sm-6">
                                    <select name="movie-select"
                                            id="movie-select" 
                                            class="custom-select" 
                                            onchange="openNewEntertainmentModal(\'movie\')">
                                        <option value="-1" hidden selected>Hangi filmi seyrettin?</option>
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
                                        class="add-btn bg-movie mt-2"
                                        onclick="addToTheList(\'movie\')
                                                $(\'#edit-movie-form-submit\').show();
                                                highlight(\'#edit-movie-form-submit\');">
                                        Ekle
                                </button>
                            </div>

                            <div id="movie-add-error" class="error mt-3" style="display:none;">
                                <!--movie-add-error-->
                                <p>Film adı ya da süresi uygun değil.
                                    <button type="button"
                                        class="fa fa-times-circle btn text-danger" 
                                        aria-hidden="true" 
                                        onclick="$(\'#movie-add-error\').hide()">
                                    </button>
                                </p> 
                            </div>
                            <div id="movie-exist-error" class="error mt-3" style="display:none;">
                                <!--movie-exist-error-->
                                <p>Film zaten var, silip tekrar ekleyebilirsin.
                                    <button type="button"
                                        class="fa fa-times-circle btn text-danger" 
                                        aria-hidden="true" 
                                        onclick="$(\'#movie-exist-error\').hide()">
                                    </button>
                                </p> 
                            </div>

                            <!--Movie list-->
                            <ul id="movie-list" class="mb-0 px-3"></ul>

                            <input type="number" name="journal_id" value="'.$journal_id.'" hidden/>
                            <input type="text" name="date" value="'.$date.'" hidden/>

                            <!--Button for submitting the form-->
                            <div id="edit-movie-form-submit"style="display:none;">
                                <br>
                                <button
                                    type="submit"
                                    class="sbmt-btn bg-edit"
                                    aria-pressed="false">
                                    Gönder
                                </button>
                            </div>
                        </form>

                    </div>

                    <div id="get-movie-names-error" class="error mt-3" style="display:none;">
                        <!--get-movie-names-error-->
                        <p>AJAX hatası. Film isimlerini sunucudan alamadık. 
                            <button type="button"
                                class="fa fa-times-circle btn text-danger" 
                                aria-hidden="true" 
                                onclick="$(\'#get-movie-names-error\').hide()">
                            </button>
                        </p> 
                    </div>
                </div>

                <!--Daily Entertainment: Book Reading-->
                <div class="daily-book">
                    <button type="button"
                            class="ent-btn bg-book my-4"
                            id="add-book-btn"
                            onclick="getEntertainmentNames(\'book\');">
                            Kitap Ekle
                    </button>
                    
                    <div id="add-book" class="py-3" style="display:none; background-color:#f7ee431a;">
                        <form
                            name="edit-book-form"
                            id="edit-book-form"
                            action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'"
                            method="post">

                            <p class="font-weight-bolder">Kitap Ekle</p>
                            <!--Add a book, name & duration-->
                            <div class="row">
                                <div class="col-xs-3 col-sm-6">
                                    <select name="book-select"
                                            id="book-select" 
                                            class="custom-select" 
                                            onchange="openNewEntertainmentModal(\'book\')">
                                        <option value="-1" hidden selected>Hangi kitabi okudun?</option>
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
                                        class="add-btn bg-book mt-2"
                                        onclick="addToTheList(\'book\')
                                                $(\'#edit-book-form-submit\').show();
                                                highlight(\'#edit-book-form-submit\');">
                                        Ekle
                                </button>
                            </div>
                            <div id="book-add-error" class="error mt-3" style="display:none;">
                                <!--book-add-error-->
                                <p>Kitap adı ya da süresi uygun değil. 
                                    <button type="button"
                                        class="fa fa-times-circle btn text-danger" 
                                        aria-hidden="true" 
                                        onclick="$(\'#book-add-error\').hide()">
                                    </button>
                                </p> 
                            </div>
                            <div id="book-exist-error" class="error mt-3" style="display:none;">
                                <!--book-exist-error-->
                                <p>Kitap zaten var, silip tekrar ekleyebilirsin. 
                                    <button type="button"
                                        class="fa fa-times-circle btn text-danger" 
                                        aria-hidden="true" 
                                        onclick="$(\'#book-exist-error\').hide()">
                                    </button>
                                </p> 
                            </div>

                            <!--Book list-->
                            <ul id="book-list" class="mb-0 px-3"></ul>

                            <input type="number" name="journal_id" value="'.$journal_id.'" hidden/>
                            <input type="text" name="date" value="'.$date.'" hidden/>

                            <!--Button for submitting the form-->
                            <div id="edit-book-form-submit"style="display:none;">
                                <br>
                                <button
                                    type="submit"
                                    class="sbmt-btn bg-edit"
                                    aria-pressed="false">
                                    Gönder
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="get-book-names-error" class="error mt-3" style="display:none;">
                        <!--get-book-names-error-->
                        <p>AJAX hatası. Film isimlerini sunucudan alamadık. 
                            <button type="button"
                                class="fa fa-times-circle btn text-danger" 
                                aria-hidden="true" 
                                onclick="$(\'#get-book-names-error\').hide()">
                            </button>
                        </p> 
                    </div>
                </div>

            </div>
        </div>';
    }
    ?>

    <?php 
        // Modal: Add new entertainment into database
        require "./inputs/ent-new-name-modal.php";
    ?>

</main>

<?php
    require "footer.php";
?>