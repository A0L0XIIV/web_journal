<?php 
    require "header.php";
    // Check if the session variable name is empty or not and redirect
    if ($_SERVER["REQUEST_METHOD"] === "GET"
        && !isset($_SESSION['name'])) {
        exit("<script>location.href = './index.php';</script>");
    }
    // GET request check and exit if not
    else if($_SERVER["REQUEST_METHOD"] !== "GET"){
        exit();
    }
?>

<?php 
    // define variables and set to empty values
    $journal_id = "";
    $work_happiness = $daily_happiness = $total_happiness = $content = "";
    $date = "";
    $game_id = $game_name = $game_duration = "";
    $series_id =  $series_name = $series_begin_season = $series_begin_episode = $series_end_season = $series_end_episode = "";
    $movie_id = $movie_name = $movie_duration = "";
    $book_id = $book_name = $book_duration = "";
    $gameArray = $seriesArray = $movieArray = $bookArray = [];
    $total_duration = $average_duration = 0;
    $error = false;
    $errorText = "";
    $showSection = 0;

    // Get name from session
    $name = $_SESSION['name'];
    // Check if name is empty or not and redirect
    if($name == "" || $name == NULL || $_SERVER["REQUEST_METHOD"] !== "GET")      
        echo("<script>location.href = './index.php';</script>"); 
  
    // Database connection
    require "./mysqli_connect.php";

    // Date form handler for journal
    if (isset($_GET["journal-date"])
        || isset($_GET["journal-month"])
        || isset($_GET["journal-year"])) {

        if(!empty($_GET["journal-date"])){
            $date = test_input($_GET["journal-date"]);
        }
        else if(!empty($_GET["journal-month"])){
            $date = test_input($_GET["journal-month"]);
        }
        else if(!empty($_GET["journal-year"])){
            $date = test_input($_GET["journal-year"]);
        }
        else{
            $error = true;
            $errorText = "Üç tarihte boş!";
        }

        if(!empty($date)){
            // Show section update
            $showSection = 1;

            // Check DB for picked date
            $sql = "SELECT id, work_happiness, daily_happiness, total_happiness, content, date
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
                mysqli_stmt_bind_result($stmt, $journal_id, $work_happiness, $daily_happiness, $total_happiness, $content, $journal_date);
                // Journal Results fetched below...
            }
        }
        else{
            $error = true;
            $errorText = "Gün, ay ya da yıl seçip gönderin.";
        }
    }

    // Game name form handler for showing game data
    else if(isset($_GET["game-id"])){
        // Get game id from request
        $game_id = test_input($_GET["game-id"]);
        // Check id for emptiness
        if(empty($game_id)){
            $error = true;
            $errorText = "Oyun seçimi boş!";
        }
        // ID is not empty
        else{
            // Check DB for user play
            $sql = "SELECT * FROM daily_game 
                    INNER JOIN gunluk ON gunluk_id=gunluk.id 
                    WHERE name=? AND game_id LIKE ?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sql)){
                $error = true;
                $errorText = mysqli_error($conn);
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "si", $name, $game_id);
                // Execute sql statement
                if(!mysqli_stmt_execute($stmt)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Check if DB returned any result - Did player play this game?
                if(mysqli_stmt_store_result($stmt) 
                    && !(mysqli_stmt_num_rows($stmt) > 0)){
                        $error = true;
                        $errorText = "Bu oyunu hiç oynamamışsın.";
                }
                else{
                    // Show section update
                    $showSection = 2;

                    // Query name, total and average game duration
                    $sql = "SELECT game.name, SUM(duration), AVG(duration) FROM daily_game 
                            INNER JOIN gunluk ON gunluk_id=gunluk.id
                            INNER JOIN game ON game_id=game.id
                            WHERE game_id=".$game_id.
                            " AND gunluk.name='".$name."';";
                    // Get all dates and their duration
                    $sql .= "SELECT DATE(gunluk.date), daily_game.duration FROM daily_game 
                            INNER JOIN game ON game_id=game.id 
                            INNER JOIN gunluk ON gunluk_id=gunluk.id
                            WHERE game_id=".$game_id.
                            " AND gunluk.name='".$name."';";
                    //$stmt = mysqli_stmt_init($conn);
                    if(!mysqli_multi_query($conn, $sql)){
                        $error = true;
                        $errorText = mysqli_error($conn);
                    }
                    else{
                        // Handle both queries results
                        do {
                            // Store result set
                            if ($result = mysqli_store_result($conn)) {
                                while ($row = mysqli_fetch_row($result)) {
                                    //echo print_r($row);
                                    // First query's result, assign them
                                    if (mysqli_more_results($conn)) {
                                        $game_name = $row[0];
                                        $total_duration = $row[1];
                                        $average_duration = $row[2];
                                    }
                                    // Second query's results, put into array
                                    else{
                                        array_push($gameArray, array('date' => $row[0], 'duration' => $row[1]));
                                    }
                                }
                                mysqli_free_result($result);
                            }
                            else
                                $error = true;
                        } while (mysqli_next_result($conn));
                    }
                }
            }
        }
    }
    
    // Series name form handler for showing series data
    else if(isset($_GET["series-id"])){
        // Get series id from request
        $series_id = test_input($_GET["series-id"]);
        // Check id for emptiness
        if(empty($series_id)){
            $error = true;
            $errorText = "Dizi seçimi boş!";
        }
        // ID is not empty
        else{
            // Check DB for user watch
            $sql = "SELECT * FROM daily_series 
                    INNER JOIN gunluk ON gunluk_id=gunluk.id 
                    WHERE name=? AND series_id LIKE ?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sql)){
                $error = true;
                $errorText = mysqli_error($conn);
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "si", $name, $series_id);
                // Execute sql statement
                if(!mysqli_stmt_execute($stmt)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Check if DB returned any result - Did player watch this series?
                if(mysqli_stmt_store_result($stmt) 
                    && !(mysqli_stmt_num_rows($stmt) > 0)){
                        $error = true;
                        $errorText = "Bu diziyi hiç izlememişsin.";
                }
                else{
                    // Show section update
                    $showSection = 3;

                    // Query name, total and average series duration
                    $sql = "SELECT series.name, SUM(end_episode - begin_episode), AVG(end_episode - begin_episode + 1) FROM daily_series 
                            INNER JOIN gunluk ON gunluk_id=gunluk.id
                            INNER JOIN series ON series_id=series.id
                            WHERE series_id=".$series_id.
                            " AND gunluk.name='".$name.
                            "' AND (end_season - begin_season)=0;";
                    // Get all dates and their duration
                    $sql .= "SELECT DATE(gunluk.date), begin_season, begin_episode, end_season, end_episode
                            FROM daily_series 
                            INNER JOIN series ON series_id=series.id 
                            INNER JOIN gunluk ON gunluk_id=gunluk.id
                            WHERE series_id=".$series_id.
                            " AND gunluk.name='".$name."';";
                    //$stmt = mysqli_stmt_init($conn);
                    if(!mysqli_multi_query($conn, $sql)){
                        $error = true;
                        $errorText = mysqli_error($conn);
                    }
                    else{
                        // Handle both queries results
                        do {
                            // Store result set
                            if ($result = mysqli_store_result($conn)) {
                                while ($row = mysqli_fetch_row($result)) {
                                    //echo print_r($row);
                                    // First query's result, assign them
                                    if (mysqli_more_results($conn)) {
                                        $series_name = $row[0];
                                        $total_duration = $row[1];
                                        $average_duration = $row[2];
                                    }
                                    // Second query's results, put into array
                                    else{
                                        $series_begin_season = $row[1];
                                        $series_begin_episode = $row[2];
                                        $series_end_season = $row[3];
                                        $series_end_episode = $row[4];
                                        // If begin and end is in the same season, calculate the watched episode number
                                        if ($series_begin_season === $series_end_season){
                                            array_push($seriesArray, array('date' => $row[0], 
                                                                            'season' => $series_begin_season,
                                                                            // If only one episode print it, if not print "begin - end"
                                                                            'episode' => ($series_begin_episode===$series_end_episode) ? $series_begin_episode : $series_begin_episode.' - '.$series_end_episode,
                                                                            // Substract end - begin and add one
                                                                            'duration' => ($series_end_episode-$series_begin_episode + 1)));
                                        }
                                        // Begin and end seasons different, push both of them
                                        else {
                                            array_push($seriesArray, array('date' => $row[0], 
                                                                            'season' => $series_begin_season.' - '.$series_end_season,
                                                                            // If only one episode print it, if not print "begin - end"
                                                                            'episode' => ($series_begin_episode===$series_end_episode) ? $series_begin_episode : $series_begin_episode.' - '.$series_end_episode,
                                                                            'duration' => 'S'.$series_begin_season.'E'.$series_begin_episode.' - S'.$series_end_season.'E'.$series_end_episode));
                                        }
                                    }
                                }
                                mysqli_free_result($result);
                            }
                            else
                                $error = true;
                        } while (mysqli_next_result($conn));
                    }
                }
            }
        }
    }

    // Movie name form handler for showing movie data
    else if(isset($_GET["movie-id"])){
        // Get movie id from request
        $movie_id = test_input($_GET["movie-id"]);
        // Check id for emptiness
        if(empty($movie_id)){
            $error = true;
            $errorText = "Film seçimi boş!";
        }
        // ID is not empty
        else{
            // Check DB for user watch
            $sql = "SELECT * FROM daily_movie 
                    INNER JOIN gunluk ON gunluk_id=gunluk.id 
                    WHERE name=? AND movie_id LIKE ?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sql)){
                $error = true;
                $errorText = mysqli_error($conn);
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "si", $name, $movie_id);
                // Execute sql statement
                if(!mysqli_stmt_execute($stmt)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Check if DB returned any result - Did player watch this movie?
                if(mysqli_stmt_store_result($stmt) 
                    && !(mysqli_stmt_num_rows($stmt) > 0)){
                        $error = true;
                        $errorText = "Bu filmi hiç izlememişsin.";
                }
                else{
                    // Show section update
                    $showSection = 4;

                    // Query name, total and average movie duration
                    $sql = "SELECT movie.name, SUM(duration) FROM daily_movie 
                            INNER JOIN gunluk ON gunluk_id=gunluk.id
                            INNER JOIN movie ON movie_id=movie.id
                            WHERE movie_id=".$movie_id.
                            " AND gunluk.name='".$name."';";
                    // Get all dates and their duration
                    $sql .= "SELECT DATE(gunluk.date), daily_movie.duration FROM daily_movie 
                            INNER JOIN movie ON movie_id=movie.id 
                            INNER JOIN gunluk ON gunluk_id=gunluk.id
                            WHERE movie_id=".$movie_id.
                            " AND gunluk.name='".$name."';";
                    //$stmt = mysqli_stmt_init($conn);
                    if(!mysqli_multi_query($conn, $sql)){
                        $error = true;
                        $errorText = mysqli_error($conn);
                    }
                    else{
                        // Handle both queries results
                        do {
                            // Store result set
                            if ($result = mysqli_store_result($conn)) {
                                while ($row = mysqli_fetch_row($result)) {
                                    // First query's result, assign them
                                    if (mysqli_more_results($conn)) {
                                        $movie_name = $row[0];
                                        $total_duration = $row[1];
                                    }
                                    // Second query's results, put into array
                                    else{
                                        array_push($movieArray, array('date' => $row[0], 'duration' => $row[1]));
                                    }
                                }
                                mysqli_free_result($result);
                            }
                            else
                                $error = true;
                        } while (mysqli_next_result($conn));
                    }
                }
            }
        }
    }

    // Book name form handler for showing book data
    else if(isset($_GET["book-id"])){
        // Get book id from request
        $book_id = test_input($_GET["book-id"]);
        // Check id for emptiness
        if(empty($book_id)){
            $error = true;
            $errorText = "Kitap seçimi boş!";
        }
        // ID is not empty
        else{
            // Check DB for user read
            $sql = "SELECT * FROM daily_book 
                    INNER JOIN gunluk ON gunluk_id=gunluk.id 
                    WHERE name=? AND book_id LIKE ?";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sql)){
                $error = true;
                $errorText = mysqli_error($conn);
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "si", $name, $book_id);
                // Execute sql statement
                if(!mysqli_stmt_execute($stmt)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Check if DB returned any result - Did player read this book?
                if(mysqli_stmt_store_result($stmt) 
                    && !(mysqli_stmt_num_rows($stmt) > 0)){
                        $error = true;
                        $errorText = "Bu kitabı hiç okumamışsın.";
                }
                else{
                    // Show section update
                    $showSection = 5;

                    // Query name, total and average book duration
                    $sql = "SELECT book.name, SUM(duration), AVG(duration) FROM daily_book 
                            INNER JOIN gunluk ON gunluk_id=gunluk.id
                            INNER JOIN book ON book_id=book.id
                            WHERE book_id=".$book_id.
                            " AND gunluk.name='".$name."';";
                    // Get all dates and their duration
                    $sql .= "SELECT DATE(gunluk.date), daily_book.duration FROM daily_book 
                            INNER JOIN book ON book_id=book.id 
                            INNER JOIN gunluk ON gunluk_id=gunluk.id
                            WHERE book_id=".$book_id.
                            " AND gunluk.name='".$name."';";
                    //$stmt = mysqli_stmt_init($conn);
                    if(!mysqli_multi_query($conn, $sql)){
                        $error = true;
                        $errorText = mysqli_error($conn);
                    }
                    else{
                        // Handle both queries results
                        do {
                            // Store result set
                            if ($result = mysqli_store_result($conn)) {
                                while ($row = mysqli_fetch_row($result)) {
                                    // First query's result, assign them
                                    if (mysqli_more_results($conn)) {
                                        $book_name = $row[0];
                                        $total_duration = $row[1];
                                        $average_duration = $row[2];
                                    }
                                    // Second query's results, put into array
                                    else{
                                        array_push($bookArray, array('date' => $row[0], 'duration' => $row[1]));
                                    }
                                }
                                mysqli_free_result($result);
                            }
                            else
                                $error = true;
                        } while (mysqli_next_result($conn));
                    }
                }
            }
        }
    }

    // Initial GET request to load page: load select-options from DB
    if($_SERVER["REQUEST_METHOD"] === "GET"
        && $showSection === 0) {
        // Show section update
        $showSection = 0;

        // Get game names
        $sql_game = "SELECT name, id FROM game";
        $stmt_game = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt_game, $sql_game)){
            $error = true;
            $errorText = "Hata: ".mysqli_error($conn);
        }
        else{
            // Execute sql statement
            mysqli_stmt_execute($stmt_game);
            // Execute sql statement
            if(!mysqli_stmt_execute($stmt_game)){
                $error = true;
                $errorText = "Oyunları yükleme hatası ".mysqli_error($conn);
            }
            // Bind result variables
            mysqli_stmt_bind_result($stmt_game, $game_name, $game_id);
            // Game name results fetched below...
            if(mysqli_stmt_store_result($stmt_game)){
                // Check if DB returned any result
                if(mysqli_stmt_num_rows($stmt_game) > 0){
                    // Fetch values
                    while (mysqli_stmt_fetch($stmt_game)) {
                        $gameArray [htmlspecialchars($game_id)] =  $game_name;
                    }
                }
            }
        }

        // Get series names
        $sql_series = "SELECT name, id FROM series";
        $stmt_series = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt_series, $sql_series)){
            $error = true;
            $errorText = "Hata: ".mysqli_error($conn);
        }
        else{
            // Execute sql statement
            mysqli_stmt_execute($stmt_series);
            // Execute sql statement
            if(!mysqli_stmt_execute($stmt_series)){
                $error = true;
                $errorText = "Dizileri yükleme hatası ".mysqli_error($conn);
            }
            // Bind result variables
            mysqli_stmt_bind_result($stmt_series, $series_name, $series_id);
            // Series name results fetched below...
            if(mysqli_stmt_store_result($stmt_series)){
                // Check if DB returned any result
                if(mysqli_stmt_num_rows($stmt_series) > 0){
                    // Fetch values
                    while (mysqli_stmt_fetch($stmt_series)) {
                        $seriesArray [htmlspecialchars($series_id)] =  $series_name;
                    }
                }
            }
        }

        // Get movie names
        $sql_movie = "SELECT name, id FROM movie";
        $stmt_movie = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt_movie, $sql_movie)){
            $error = true;
            $errorText = "Hata: ".mysqli_error($conn);
        }
        else{
            // Execute sql statement
            mysqli_stmt_execute($stmt_movie);
            // Execute sql statement
            if(!mysqli_stmt_execute($stmt_movie)){
                $error = true;
                $errorText = "Filmleri yükleme hatası ".mysqli_error($conn);
            }
            // Bind result variables
            mysqli_stmt_bind_result($stmt_movie, $movie_name, $movie_id);
            // Mvoie name results fetched below...
            if(mysqli_stmt_store_result($stmt_movie)){
                // Check if DB returned any result
                if(mysqli_stmt_num_rows($stmt_movie) > 0){
                    // Fetch values
                    while (mysqli_stmt_fetch($stmt_movie)) {
                        $movieArray [htmlspecialchars($movie_id)] =  $movie_name;
                    }
                }
            }
        }

        // Get book names
        $sql_book = "SELECT name, id FROM book";
        $stmt_book = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt_book, $sql_book)){
            $error = true;
            $errorText = "Hata: ".mysqli_error($conn);
        }
        else{
            // Execute sql statement
            mysqli_stmt_execute($stmt_book);
            // Execute sql statement
            if(!mysqli_stmt_execute($stmt_book)){
                $error = true;
                $errorText = "Kitapları yükleme hatası ".mysqli_error($conn);
            }
            // Bind result variables
            mysqli_stmt_bind_result($stmt_book, $book_name, $book_id);
            // Book name results fetched below...
            if(mysqli_stmt_store_result($stmt_book)){
                // Check if DB returned any result
                if(mysqli_stmt_num_rows($stmt_book) > 0){
                    // Fetch values
                    while (mysqli_stmt_fetch($stmt_book)) {
                        $bookArray [htmlspecialchars($book_id)] =  $book_name;
                    }
                }
            }
        }
    }

    // Custom function for SQL injection and other security checks
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>

<!-- Main center div-->
<main class="main" style="min-height: 89vh;">
    
    <?php
    // Error
    if($error) {
        echo '<div>
                <p id="dateError" class="error">Hata meydana geldi. '.$errorText.'</p>
            </div>';
    }

    // Section 0 of 6, submit forms
    if($showSection === 0){ 
        echo '<div>

                <!-- Get journal by date  -->
                <form
                    name="date-form"
                    id="date-form"
                    action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'"
                    method="get"
                    onsubmit="return journalDateSubmit()">

                    <h1>Günlük görüntüleme tarihi seçiniz:</h1>
                    <!--Input for date, type=date-->
                    <div class="input-group mb-3 justify-content-center">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="day-label">Gün</span>
                        </div>
                        <input type="date" name="journal-date" id="journal-date-input">
                    </div>
                    <!--Input for month, type=month-->
                    <div class="input-group mb-3 justify-content-center">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="month-label">Ay</span>
                        </div>
                        <input type="month" name="journal-month" id="journal-month-input">
                    </div>
                    <!--Input for year, type=number-->
                    <div class="input-group mb-3 justify-content-center">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="year-label">Yıl</span>
                        </div>
                        <input type="number" name="journal-year" id="journal-year-input" min="1000" max="9999" title="Sadece 4 rakam">
                    </div>

                    <br>

                    <!--Button for submitting the form-->
                    <div>
                        <button
                            type="submit"
                            id="date-picker-submit"
                            class="btn btn-success bg-primary"
                            aria-pressed="false"
                        >
                        Göster
                        </button>
                    </div>

                    <hr>
                </form> 

                <!-- Get game by name -->
                <form 
                    name="game-form"
                    id="game-form"
                    action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" 
                    method="get">
                    <h3>Oyun seçiniz:</h3>

                    <!--Select for game, hidden input for request-->
                    <div class="mb-3 justify-content-center">
                        <select name="game-id"
                                id="game-select" 
                                class="custom-select">
                            <option value="" hidden selected>Oyun seç</option>';

                            if(empty($gameArray)){
                                echo '<option value="" class="error">Oyun bulunamadı</option>';
                            }
                            else{
                                foreach($gameArray as $key => $value)
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                        echo '
                        </select>
                    </div>

                    <!--Button for submitting the form-->
                    <div>
                        <button
                            type="submit"
                            id="game-submit"
                            class="btn btn-info bg-primary"
                            aria-pressed="false"
                        >
                        Göster
                        </button>
                    </div>

                    <hr>
                </form>

                <!-- Get series by name -->
                <form 
                    name="series-form"
                    id="series-form"
                    action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" 
                    method="get">
                    <h3>Dizi seçiniz:</h3>

                    <!--Select for series, hidden input for request-->
                    <div class="mb-3 justify-content-center">
                        <select name="series-id"
                                id="series-select" 
                                class="custom-select">
                            <option value="" hidden selected>Dizi seç</option>';
                            
                            if(empty($seriesArray)){
                                echo '<option value="" class="error">Dizi bulunamadı</option>';
                            }
                            else{
                                foreach($seriesArray as $key => $value)
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                        echo '
                        </select>
                    </div>

                    <!--Button for submitting the form-->
                    <div>
                        <button
                            type="submit"
                            id="series-submit"
                            class="btn btn-primary bg-primary"
                            aria-pressed="false"
                        >
                        Göster
                        </button>
                    </div>

                    <hr>
                </form>

                <!-- Get movie by name -->
                <form 
                    name="movie-form"
                    id="movie-form"
                    action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" 
                    method="get">
                    <h3>Film seçiniz:</h3>

                    <!--Select for movie, hidden input for request-->
                    <div class="mb-3 justify-content-center">
                        <select name="movie-id"
                                id="movie-select" 
                                class="custom-select">
                            <option value="" hidden selected>Film seç</option>';
                            
                            if(empty($movieArray)){
                                echo '<option value="" class="error">Dizi bulunamadı</option>';
                            }
                            else{
                                foreach($movieArray as $key => $value)
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                        echo '
                        </select>
                    </div>

                    <!--Button for submitting the form-->
                    <div>
                        <button
                            type="submit"
                            id="movie-submit"
                            class="btn btn-secondary bg-primary"
                            aria-pressed="false"
                        >
                        Göster
                        </button>
                    </div>

                    <hr>
                </form>

                <!-- Get book by name -->
                <form 
                    name="book-form"
                    id="book-form"
                    action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" 
                    method="get">
                    <h3>Kitap seçiniz:</h3>

                    <!--Select for book, hidden input for request-->
                    <div class="mb-3 justify-content-center">
                        <select name="book-id"
                                id="book-select" 
                                class="custom-select">
                            <option value="" hidden selected>Kitap seç</option>';
                            
                            if(empty($bookArray)){
                                echo '<option value="" class="error">Kitap bulunamadı</option>';
                            }
                            else{
                                foreach($bookArray as $key => $value)
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                        echo '
                        </select>
                    </div>

                    <!--Button for submitting the form-->
                    <div>
                        <button
                            type="submit"
                            id="book-submit"
                            class="btn btn-warning bg-primary"
                            aria-pressed="false"
                        >
                        Göster
                        </button>
                    </div>
                </form>

                <br>

            </div>';
    }

    // Section 1 of 6, show journal data
    else if($showSection === 1){ 
        echo '<div>
                <h1>';
                if(isset($_SESSION['name'])){
                    echo $_SESSION['name'].', ';
                }
                echo $date.' tarihili günlüklerin';
            echo'</h1>';

        if(mysqli_stmt_store_result($stmt)){
            // Check if DB returned any result
            if(mysqli_stmt_num_rows($stmt) > 0){
                // Fetch values
                while (mysqli_stmt_fetch($stmt)) {
                    echo '<div class="row" style="border-top: solid 1px #ff7700; padding-top:5px;">
                            <div class="col-xs-6 col-sm-2 px-0">
                                <button class="btn btn-primary bg-primary btn-sm p-0">
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
                                        echo '<p class="opt10"><i class="fa fa-smile-o"></i> Muhteşem</p>';
                                        break;
                                    case 9:
                                        echo '<p class="opt9"><i class="fa fa-smile-o"></i> Şahane</p>';
                                        break;
                                    case 8:
                                        echo '<p class="opt8"><i class="fa fa-smile-o"></i> Baya iyi</p>';
                                        break;
                                    case 7:
                                        echo '<p class="opt7"><i class="fa fa-smile-o"></i> Gayet iyi</p>';
                                        break;
                                    case 6:
                                        echo '<p class="opt6"><i class="fa fa-meh-o"></i> Fena değil</p>';
                                        break;
                                    case 5:
                                        echo '<p class="opt5"><i class="fa fa-meh-o"></i> Normal</p>';
                                        break;
                                    case 4:
                                        echo '<p class="opt4"><i class="fa fa-meh-o"></i> Biraz kötü</p>';
                                        break;
                                    case 3:
                                        echo '<p class="opt3"><i class="fa fa-frown-o"></i> Kötü</p>';
                                        break;
                                    case 2:
                                        echo '<p class="opt2"><i class="fa fa-frown-o"></i> Berbat</p>';
                                        break;
                                    case 1:
                                        echo '<p class="opt1"><i class="fa fa-frown-o"></i> Berbat ötesi</p>';
                                        break;
                                    case 0:
                                    default:
                                        echo '<p class="opt0"><i class="fa fa-circle-o"></i> Yorum Yok</p>';
                                        break;
                                }
                            echo
                            '</div>
                            <div class="col-xs-4 col-sm-3 px-0">
                                <p class="orangeText">İş/Okul dışı:</p>';
                                switch($daily_happiness){
                                    case 10:
                                        echo '<p class="opt10"><i class="fa fa-smile-o"></i> Muhteşem</p>';
                                        break;
                                    case 9:
                                        echo '<p class="opt9"><i class="fa fa-smile-o"></i> Şahane</p>';
                                        break;
                                    case 8:
                                        echo '<p class="opt8"><i class="fa fa-smile-o"></i> Baya iyi</p>';
                                        break;
                                    case 7:
                                        echo '<p class="opt7"><i class="fa fa-smile-o"></i> Gayet iyi</p>';
                                        break;
                                    case 6:
                                        echo '<p class="opt6"><i class="fa fa-meh-o"></i> Fena değil</p>';
                                        break;
                                    case 5:
                                        echo '<p class="opt5"><i class="fa fa-meh-o"></i> Normal</p>';
                                        break;
                                    case 4:
                                        echo '<p class="opt4"><i class="fa fa-meh-o"></i> Biraz kötü</p>';
                                        break;
                                    case 3:
                                        echo '<p class="opt3"><i class="fa fa-frown-o"></i> Kötü</p>';
                                        break;
                                    case 2:
                                        echo '<p class="opt2"><i class="fa fa-frown-o"></i> Berbat</p>';
                                        break;
                                    case 1:
                                        echo '<p class="opt1"><i class="fa fa-frown-o"></i> Berbat ötesi</p>';
                                        break;
                                    case 0:
                                    default:
                                        echo '<p class="opt0"><i class="fa fa-circle-o"></i> Yorum Yok</p>';
                                        break;
                                }
                            echo
                            '</div>
                            <div class="col-xs-4 col-sm-2 px-0">
                                <p class="orangeText">Genel:</p>';
                                switch($total_happiness){
                                    case 10:
                                        echo '<p class="opt10"><i class="fa fa-smile-o"></i> Muhteşem</p>';
                                        break;
                                    case 9:
                                        echo '<p class="opt9"><i class="fa fa-smile-o"></i> Şahane</p>';
                                        break;
                                    case 8:
                                        echo '<p class="opt8"><i class="fa fa-smile-o"></i> Baya iyi</p>';
                                        break;
                                    case 7:
                                        echo '<p class="opt7"><i class="fa fa-smile-o"></i> Gayet iyi</p>';
                                        break;
                                    case 6:
                                        echo '<p class="opt6"><i class="fa fa-meh-o"></i> Fena değil</p>';
                                        break;
                                    case 5:
                                        echo '<p class="opt5"><i class="fa fa-meh-o"></i> Normal</p>';
                                        break;
                                    case 4:
                                        echo '<p class="opt4"><i class="fa fa-meh-o"></i> Biraz kötü</p>';
                                        break;
                                    case 3:
                                        echo '<p class="opt3"><i class="fa fa-frown-o"></i> Kötü</p>';
                                        break;
                                    case 2:
                                        echo '<p class="opt2"><i class="fa fa-frown-o"></i> Berbat</p>';
                                        break;
                                    case 1:
                                        echo '<p class="opt1"><i class="fa fa-frown-o"></i> Berbat ötesi</p>';
                                        break;
                                    case 0:
                                    default:
                                        echo '<p class="opt0"><i class="fa fa-circle-o"></i> Yorum Yok</p>';
                                        break;
                                }
                            echo
                            '</div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <p class="mb-0 pb-3">'.(!empty($content) ? $content : "").'</p>
                        </div>
                    ';

                    // Get daily game data from DB
                    $sql_game = "SELECT name, duration
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
                        mysqli_stmt_bind_result($stmt_game, $game_name, $game_duration);
                        // Game Results fetched below...
                        if(mysqli_stmt_store_result($stmt_game)){
                            // Check if DB returned any result
                            if(mysqli_stmt_num_rows($stmt_game) > 0){
                                echo '<table class="table table-bordered table-hover table-sm table-striped">';
                                echo '<tr class="table-info"><th>Oyun</th><th>Süre</th></tr>';
                                // Fetch values
                                while (mysqli_stmt_fetch($stmt_game)) {
                                    echo '<tr><td>'.$game_name.'</td><td>'.$game_duration.' Saat</td></tr>';
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
                    $sql_series = "SELECT name, begin_season, begin_episode, end_season, end_episode
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
                        mysqli_stmt_bind_result($stmt_series, $series_name, $series_begin_season, $series_begin_episode, $series_end_season, $series_end_episode);
                        // Series Results fetched below...
                        if(mysqli_stmt_store_result($stmt_series)){
                            // Check if DB returned any result
                            if(mysqli_stmt_num_rows($stmt_series) > 0){
                                echo '<table class="table table-bordered table-hover table-sm table-striped">';
                                echo '<tr class="table-primary">
                                        <th>Dizi</th>
                                        <th>İlk sezon</th>
                                        <th>İlk bölüm</th>
                                        <th>Son sezon</th>
                                        <th>Son bölüm</th></tr>';
                                // Fetch values
                                while (mysqli_stmt_fetch($stmt_series)) {
                                    echo '<tr><td>'.$series_name.'</td>
                                            <td>'.$series_begin_season.'</td>
                                            <td>'.$series_begin_episode.'</td>
                                            <td>'.$series_end_season.'</td>
                                            <td>'.$series_end_episode.'</td></tr>';
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
                    $sql_movie = "SELECT name, duration
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
                        mysqli_stmt_bind_result($stmt_movie, $movie_name, $movie_duration);
                        // Movie Results fetched below...
                        if(mysqli_stmt_store_result($stmt_movie)){
                            // Check if DB returned any result
                            if(mysqli_stmt_num_rows($stmt_movie) > 0){
                                echo '<table class="table table-bordered table-hover table-sm table-striped">';
                                echo '<tr class="table-success"><th>Film</th><th>Süre</th></tr>';
                                // Fetch values
                                while (mysqli_stmt_fetch($stmt_movie)) {
                                    echo '<tr><td>'.$movie_name.'</td><td>'.$movie_duration.' Saat</td></tr>';
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
                    $sql_book = "SELECT name, duration
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
                        mysqli_stmt_bind_result($stmt_book, $book_name, $book_duration);
                        // Book Results fetched below...
                        if(mysqli_stmt_store_result($stmt_book)){
                            // Check if DB returned any result
                            if(mysqli_stmt_num_rows($stmt_book) > 0){
                                echo '<table class="table table-bordered table-hover table-sm table-striped">';
                                echo '<tr class="table-warning"><th>Kitap</th><th>Süre</th></tr>';
                                // Fetch values
                                while (mysqli_stmt_fetch($stmt_book)) {
                                    echo '<tr><td>'.$book_name.'</td><td>'.$book_duration.' Saat</td></tr>';
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
        echo '</div>';
    }

    // Section 2 of 6, show game data
    else if($showSection === 2){
        echo '<div id="section2">
                <h2>'.$game_name.'</h2>
                <p>Toplam oyun oynama süresi: '.$total_duration.' Saat</p>
                <p>Ortalama oyun oynama süresi: '.$average_duration.' Saat</p>
                <table id="game-table" class="table table-bordered table-hover table-sm table-striped">
                    <tr class="table-info">
                        <th>Tarih</th>
                        <th>Süre</th>
                    </tr>';
                    foreach($gameArray as $game){
                        echo '<tr>';
                        echo '<td>'.$game['date'].'</td>';
                        echo '<td>'.$game['duration'].' Saat</td>';
                        echo '</tr>';
                    }
                    echo '
                </table>
            </div>';
    }

    // Section 3 of 6, show series data
    else if($showSection === 3){
        echo '<div id="section3">
                <h2>'.$series_name.'</h2>
                <p>Toplam dizi izleme süresi: '.$total_duration.' Bölüm</p>
                <p>Ortalama dizi izleme süresi: '.$average_duration.' Bölüm</p>
                <table id="game-table" class="table table-bordered table-hover table-sm table-striped">
                    <tr class="table-primary">
                        <th>Tarih</th>
                        <th>Sezon</th>
                        <th>Bölümler</th>
                        <th>Bölüm Sayısı</th>
                    </tr>';
                    foreach($seriesArray as $series){
                        echo '<tr>';
                        echo '<td>'.$series['date'].'</td>';
                        echo '<td>'.$series['season'].'</td>';
                        echo '<td>'.$series['episode'].'</td>';
                        echo '<td>'.$series['duration'].'</td>';
                        echo '</tr>';
                    }
                    echo '
                </table>
            </div>';
    }

    // Section 4 of 6, show movie data
    else if($showSection === 4){
        echo '<div id="section4">
                <h2>'.$movie_name.'</h2>
                <p>Toplam film izleme süresi: '.$total_duration.' Saat</p>
                <table id="game-table" class="table table-bordered table-hover table-sm table-striped">
                    <tr class="table-secondary">
                        <th>Tarih</th>
                        <th>Süre</th>
                    </tr>';
                    foreach($movieArray as $movie){
                        echo '<tr>';
                        echo '<td>'.$movie['date'].'</td>';
                        echo '<td>'.$movie['duration'].' Saat</td>';
                        echo '</tr>';
                    }
                    echo '
                </table>
            </div>';
    }

    // Section 5 of 6, show book data
    else if($showSection === 5){
        echo '<div id="section5">
                <h2>'.$book_name.'</h2>
                <p>Toplam kitap okuma süresi: '.$total_duration.' Saat</p>
                <p>Ortalama kitap okuma süresi: '.$average_duration.' Saat</p>
                <table id="game-table" class="table table-bordered table-hover table-sm table-striped">
                    <tr class="table-warning">
                        <th>Tarih</th>
                        <th>Süre</th>
                    </tr>';
                    foreach($bookArray as $book){
                        echo '<tr>';
                        echo '<td>'.$book['date'].'</td>';
                        echo '<td>'.$book['duration'].' Saat</td>';
                        echo '</tr>';
                    }
                    echo '
                </table>
            </div>';
    }

    // Error V2
    else{
        echo '<div>
            <p id="dateError" class="error">Hata meydana geldi.</p>
            <p id="dateError" class="error">Burada olmaman gerek!.</p>
            <br/>
            <p id="dateError" class="error">There is an error about something.</p>
            <p id="dateError" class="error">You were not suppose to be here!</p>
        </div>';
    }
    ?>

</main>

<?php
    require "footer.php";
?>