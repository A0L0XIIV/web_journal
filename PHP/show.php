<?php 
    require "header.php";
    // Check if the session variable name is empty or not and redirect
    if ($_SERVER["REQUEST_METHOD"] === "GET"
        && !isset($_SESSION['name'])) {
        exit("<script>location.href = './index.php';</script>");
    }
?>

<?php 
    // define variables and set to empty values
    $journal_id = "";
    $work_happiness = $daily_happiness = $total_happiness = $content = "";
    $date = "";
    $game_name = $game_duration = "";
    $series_name = $series_begin_season = $series_begin_episode = $series_end_season = $series_end_episode = "";
    $movie_name = $movie_duration = "";
    $book_name = $book_duration = "";
    $error = false;
    $errorText = "";
    $isSubmitted = false;

    // Get name from session
    $name = $_SESSION['name'];
    // Check if name is empty or not and redirect
    if($name == "" || $name == NULL)      
        echo("<script>location.href = './index.php';</script>"); 
  
    // Database connection
    require "./mysqli_connect.php";

    // Check request method for post
    if ($_SERVER["REQUEST_METHOD"] == "GET" 
        && (isset($_GET["journal-date"])
        || isset($_GET["journal-month"])
        || isset($_GET["journal-year"]))) {

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
            $isSubmitted = true;
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

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
      }
?>

<?php/*
    // define variables and set to empty values
    $journal_id = "";
    $work_happiness = $daily_happiness = $total_happiness = $content = "";
    $date = "";
    $game_name = $game_duration = "";
    $series_name = $series_begin_season = $series_begin_episode = $series_end_season = $series_end_episode = "";
    $movie_name = $movie_duration = "";
    $book_name = $book_duration = "";
    $error = false;
    $errorText = "";
    $isSubmitted = false;

    // Check request method for post
    if ($_SERVER["REQUEST_METHOD"] == "POST" 
        && isset($_POST["date-picker-submit"])) {*/
?>

<!-- Main center div-->
<main class="main" style="min-height: 89vh;">
    
    <!--Error-->
    <div <?php if(!$error) {echo 'style="display: none;"';}?>>
        <p id="dateError" class="error">Hata meydana geldi. <?php echo $errorText;?></p>
    </div>  

    <div <?php if($isSubmitted) echo 'style="display: none;"';?>>

        <!-- Get journal by date  -->
        <form
            name="date-form"
            id="date-form"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
            method="get"
            onsubmit="return journalDateSubmit()"
        >
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
            <!--Input for year, type=text-->
            <div class="input-group mb-3 justify-content-center">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="year-label">Yıl</span>
                </div>
                <input type="number" name="journal-year" id="journal-year-input" min="1000" max="9999" title="Sadece 4 rakam">
            </div>

            <br>

            <!--Input for submitting the form, type=submit-->
            <div>
            <input
                type="submit"
                value="Göster"
                name="date-picker-submit"
                id="date-picker-submit"
                class="btn btn-success bg-primary"
                aria-pressed="false"
            />
            </div>

            <hr>
        </form> 

        <!-- Get game by name -->
        <form 
            name="game-form"
            id="game-form"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" 
            method="get">
            <h3>Oyun seçiniz:</h3>

            <!--Input for date, type=date-->
            <div class="input-group mb-3 justify-content-center">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="game-label">Oyun ismi</span>
                </div>
                <select name="game-select"
                        id="game-select" 
                        class="custom-select"
                        onclick="getEntertainmentNames('game')">
                    <option value="0" hidden selected>Oyun seç</option>
                </select>
                <input type="hidden" name="game-name" id="game-name-input">
            </div>

            <!--Input for submitting the form, type=submit-->
            <div>
                <input
                    type="submit"
                    value="Göster"
                    name="game-submit"
                    id="game-submit"
                    class="btn btn-info bg-primary"
                    aria-pressed="false"
                />
            </div>

            <hr>
        </form>

        <!-- Get series by name -->
        <form 
            name="series-form"
            id="series-form"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" 
            method="get">
            <h3>Dizi seçiniz:</h3>

            <!--Input for date, type=date-->
            <div class="input-group mb-3 justify-content-center">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="day-label">Gün</span>
                </div>
                <input type="date" name="journal-date" id="journal-date-input">
            </div>

            <!--Input for submitting the form, type=submit-->
            <div>
                <input
                    type="submit"
                    value="Göster"
                    name="series-submit"
                    id="series-submit"
                    class="btn btn-primary bg-primary"
                    aria-pressed="false"
                />
            </div>

            <hr>
        </form>

        <!-- Get movie by name -->
        <form 
            name="movie-form"
            id="movie-form"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" 
            method="get">
            <h3>Film seçiniz:</h3>

            <!--Input for date, type=date-->
            <div class="input-group mb-3 justify-content-center">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="day-label">Gün</span>
                </div>
                <input type="date" name="journal-date" id="journal-date-input">
            </div>

            <!--Input for submitting the form, type=submit-->
            <div>
                <input
                    type="submit"
                    value="Göster"
                    name="movie-submit"
                    id="movie-submit"
                    class="btn btn-secondary bg-primary"
                    aria-pressed="false"
                />
            </div>

            <hr>
        </form>

        <!-- Get book by name -->
        <form 
            name="book-form"
            id="book-form"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" 
            method="get">
            <h3>Kitap seçiniz:</h3>

            <!--Input for date, type=date-->
            <div class="input-group mb-3 justify-content-center">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="day-label">Gün</span>
                </div>
                <input type="date" name="journal-date" id="journal-date-input">
            </div>

            <!--Input for submitting the form, type=submit-->
            <div>
                <input
                    type="submit"
                    value="Göster"
                    name="book-submit"
                    id="book-submit"
                    class="btn btn-warning bg-primary"
                    aria-pressed="false"
                />
            </div>
        </form>

    </div>

    <!-- 2 parts, 1 of them hidden -->

    <div <?php if(!$isSubmitted) echo 'style="display: none;"';?>>
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
    ?>
    </div>

</main>

<?php
    require "footer.php";
?>