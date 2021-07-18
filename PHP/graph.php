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
    $work_happiness = $daily_happiness = $total_happiness = "";
    $work_happiness_array = array();
    $daily_happiness_array = array();
    $total_happiness_array = array();
    $date_array = array();
    $duration_array = array();
    $date = $duration = $date_type = "";
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

    // Journal graph request handler
    if (isset($_GET["month"])
        || isset($_GET["year"])) {

        if(!empty($_GET["month"])){
            $date = test_input($_GET["month"]);
            $date_type = "month";
        }
        else if(!empty($_GET["year"])){
            $date = test_input($_GET["year"]);
            $date_type = "year";
        }
        else{
            $error = true;
            $errorText = "İki tarihte boş!";
        }

        if(!empty($date)){
            // Show section update
            $showSection = 1;

            // Check DB for picked date
            $sql = "SELECT work_happiness, daily_happiness, total_happiness, date 
                    FROM gunluk WHERE name=? AND date LIKE ? ORDER BY date ASC";
            $stmt = mysqli_stmt_init($conn);
            // Prepare SQL statement
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
                if(!mysqli_stmt_execute($stmt)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $work_happiness, $daily_happiness, $total_happiness, $journal_date);
                // Results fetched below...
            }
        }
        else{
            $error = true;
            $errorText = "Ay ya da yıl seçin.";
        }
    }

    // Game name form handler for showing game data
    else if(isset($_GET["game"])){
        // Get game id from request
        $game_id = test_input($_GET["game"]);

        // Check id for emptiness
        if(empty($game_id)){
            $error = true;
            $errorText = "Oyun seçimi boş!";
        }
        // ID is not empty
        else{
            // DB query
            /*// Query for getting all dates including empty game dates.
            $sql = 'SELECT date, duration FROM (SELECT * FROM daily_game WHERE game_id=?) AS g
                        RIGHT JOIN gunluk ON g.gunluk_id=gunluk.id
                        WHERE name=?
                        AND DATE(date) >= DATE((SELECT date_created FROM daily_game WHERE game_id=? ORDER BY date_created ASC LIMIT 1)) - 1
                        AND DATE(date) <= DATE((SELECT date_created FROM daily_game WHERE game_id=? ORDER BY date_created DESC LIMIT 1)) + 1
                        ORDER BY date';*/
            $sql = "SELECT DATE(date), duration FROM daily_game
                    INNER JOIN gunluk ON gunluk_id=gunluk.id
                    WHERE name=? AND game_id=?
                    ORDER BY date";

            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)){
                $error = true;
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "si", $name, $game_id);
                // Execute sql statement
                if(!mysqli_stmt_execute($stmt)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $date, $duration);
                // Store SQL results
                if(mysqli_stmt_store_result($stmt)){
                    // Check if DB returned any result
                    if(mysqli_stmt_num_rows($stmt) > 0){
                        // Show section update
                        $showSection = 2;
                        // Fetch values
                        while (mysqli_stmt_fetch($stmt)) {
                            $date_array[] = $date;
                            $duration_array[] = $duration;
                        }
                        $duration_JSON = json_encode($duration_array);
                        $date_JSON = json_encode($date_array);
                        // Query game name
                        $sql = "SELECT name FROM daily_game
                                INNER JOIN game ON game_id=game.id
                                WHERE game_id=?";
                        $stmt = mysqli_stmt_init($conn);
                        // Prepare statement
                        if(!mysqli_stmt_prepare($stmt, $sql)){
                            $error = true;
                            $errorText = mysqli_error($conn);
                        }
                        else{
                            // Bind inputs to query parameters
                            mysqli_stmt_bind_param($stmt, "i", $game_id);
                            // Execute sql statement
                            mysqli_stmt_execute($stmt);
                            // Bind results
                            mysqli_stmt_bind_result($stmt, $game_name);
                            // Store results
                            mysqli_stmt_store_result($stmt);
                            // Fetch results
                            mysqli_stmt_fetch($stmt);
                        }
                    }
                    else{
                        $error = true;
                        $errorText = "Bu oyunu hiç oynamamışsın.";
                    }
                }
                else{
                    $error = true;
                    $errorText = "Veritabanı saklama hatası. ".mysqli_error($conn);
                }
            }
        }
    }

    // Series name form handler for showing series data
    else if(isset($_GET["series"])){
        // Get series id from request
        $series_id = test_input($_GET["series"]);
        // Check id for emptiness
        if(empty($series_id)){
            $error = true;
            $errorText = "Dizi seçimi boş!";
        }
        // ID is not empty
        else{
            // DB query
            $sql = "SELECT DATE(date), (end_episode-begin_episode +1) FROM daily_series
                    INNER JOIN gunluk ON gunluk_id=gunluk.id
                    WHERE name=? AND series_id=?
                    AND begin_season=end_season
                    ORDER BY date";

            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)){
                $error = true;
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "si", $name, $series_id);
                // Execute sql statement
                if(!mysqli_stmt_execute($stmt)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $date, $duration);
                // Store SQL results
                if(mysqli_stmt_store_result($stmt)){
                    // Check if DB returned any result
                    if(mysqli_stmt_num_rows($stmt) > 0){
                        // Show section update
                        $showSection = 3;
                        // Fetch values
                        while (mysqli_stmt_fetch($stmt)) {
                            $date_array[] = $date;
                            $duration_array[] = $duration;
                        }
                        $duration_JSON = json_encode($duration_array);
                        $date_JSON = json_encode($date_array);
                        // Query series name
                        $sql = "SELECT name FROM daily_series
                                INNER JOIN series ON series_id=series.id
                                WHERE series_id=?";
                        $stmt = mysqli_stmt_init($conn);
                        // Prepare statement
                        if(!mysqli_stmt_prepare($stmt, $sql)){
                            $error = true;
                            $errorText = mysqli_error($conn);
                        }
                        else{
                            // Bind inputs to query parameters
                            mysqli_stmt_bind_param($stmt, "i", $series_id);
                            // Execute sql statement
                            mysqli_stmt_execute($stmt);
                            // Bind results
                            mysqli_stmt_bind_result($stmt, $series_name);
                            // Store results
                            mysqli_stmt_store_result($stmt);
                            // Fetch results
                            mysqli_stmt_fetch($stmt);
                        }
                    }
                    else{
                        $error = true;
                        $errorText = "Bu diziyi hiç izlememişsin.";
                    }
                }
                else{
                    $error = true;
                    $errorText = "Veritabanı saklama hatası. ".mysqli_error($conn);
                }
            }
        }
    }

    // Book name form handler for showing book data
    else if(isset($_GET["book"])){
        // Get book id from request
        $book_id = test_input($_GET["book"]);
        // Check id for emptiness
        if(empty($book_id)){
            $error = true;
            $errorText = "Kitap seçimi boş!";
        }
        // ID is not empty
        else{
            // DB query
            $sql = "SELECT DATE(date), duration FROM daily_book
                    INNER JOIN gunluk ON gunluk_id=gunluk.id
                    WHERE name=? AND book_id=?
                    ORDER BY date";

            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)){
                $error = true;
            }
            else{
                // Bind inputs to query parameters
                mysqli_stmt_bind_param($stmt, "si", $name, $book_id);
                // Execute sql statement
                if(!mysqli_stmt_execute($stmt)){
                    $error = true;
                    $errorText = mysqli_error($conn);
                }
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $date, $duration);
                // Store SQL results
                if(mysqli_stmt_store_result($stmt)){
                    // Check if DB returned any result
                    if(mysqli_stmt_num_rows($stmt) > 0){
                        // Show section update
                        $showSection = 4;
                        // Fetch values
                        while (mysqli_stmt_fetch($stmt)) {
                            $date_array[] = $date;
                            $duration_array[] = $duration;
                        }
                        $duration_JSON = json_encode($duration_array);
                        $date_JSON = json_encode($date_array);
                        // Query book name
                        $sql = "SELECT name FROM daily_book
                                INNER JOIN book ON book_id=book.id
                                WHERE book_id=?";
                        $stmt = mysqli_stmt_init($conn);
                        // Prepare statement
                        if(!mysqli_stmt_prepare($stmt, $sql)){
                            $error = true;
                            $errorText = mysqli_error($conn);
                        }
                        else{
                            // Bind inputs to query parameters
                            mysqli_stmt_bind_param($stmt, "i", $book_id);
                            // Execute sql statement
                            mysqli_stmt_execute($stmt);
                            // Bind results
                            mysqli_stmt_bind_result($stmt, $book_name);
                            // Store results
                            mysqli_stmt_store_result($stmt);
                            // Fetch results
                            mysqli_stmt_fetch($stmt);
                        }
                    }
                    else{
                        $error = true;
                        $errorText = "Bu kitabı hiç okumamışsın.";
                    }
                }
                else{
                    $error = true;
                    $errorText = "Veritabanı saklama hatası. ".mysqli_error($conn);
                }
            }
        }
    }

    // Not found error GET request handler
    else if (isset($_GET["error"])){
        $showSection = 0;
        $error = true;
        if($_GET["error"] === "not-found")
            $errorText = "Bu tarihli günlük bulunamadı.";
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

    
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

?>

<!-- Main center div-->
<main class="main" style="min-height: 89vh;">  
    <br> 
    <?php
    if($error) {
        // Print error message
        require "./msg/error.php";
    }

    // Section 0 of 5, submit forms
    if($showSection === 0){
        echo'
        <div>

            <!-- Get journal by date  -->
            <form
                name="date-form"
                id="date-form"
                action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'"
                method="get"
                onsubmit="return journalDateSubmit()">

                <h1>Günlük grafiği görüntüleme tarihi seçiniz:</h1>

                <!--Input for month, type=month-->
                <div class="input-group mb-3 justify-content-center">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="month-label">Ay</span>
                    </div>
                    <input type="month" name="month" id="journal-month-input">
                </div>

                <!--Input for year, type=text-->
                <div class="input-group mb-3 justify-content-center">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="year-label">Yıl</span>
                    </div>
                    <input type="number" name="year" id="journal-year-input" placeholder="yyyy" min="1000" max="9999" title="Sadece 4 rakam">
                </div>

                <br>

                <!--Button for submitting the form-->
                <div>
                    <button
                        type="submit"
                        id="date-picker-submit"
                        class="sbmt-btn bg-graph"
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
                    <select name="game"
                            id="game-select" 
                            class="custom-select"
                            required>
                        <option value="-1" hidden selected>Oyun seç</option>';
                        
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
                        class="sbmt-btn bg-game"
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
                    <select name="series"
                            id="series-select" 
                            class="custom-select"
                            required>
                        <option value="-1" hidden selected>Dizi seç</option>';
                        
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
                        class="sbmt-btn bg-series"
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
                    <select name="book"
                            id="book-select" 
                            class="custom-select"
                            required>
                        <option value="-1" hidden selected>Kitap seç</option>';
                        
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
                        class="sbmt-btn bg-book"
                        aria-pressed="false"
                    >
                    Göster
                    </button>
                </div>
            </form>

            <br>
        </div>';
    }

    // Section 1 of 5, show journal graph
    else if($showSection === 1){
        echo'<div>
            <h1>';
                if(isset($_SESSION['name'])){
                    echo $_SESSION['name'].', ';
                }
                echo $date.' tarihili mutluluk grafiğin';
        echo '</h1>';
        echo '
        <div class="mx-auto">
            <button type="button" class="btn bg-password" onclick="goToPreviousDay(\''.$date.'\', \''.$date.'\')">
                <i class="fas fa-arrow-alt-circle-left"></i>
            </button>
            <button type="button" class="ent-btn bg-show p-0">
                <a href="show.php?journal-'.$date_type.'='.$date.'" class="btn">Görüntüle</a>
            </button>
            <button type="button" class="btn bg-password" onclick="goToNextDay(\''.$date.'\', \''.$date.'\')">
                <i class="fas fa-arrow-alt-circle-right"></i>
            </button>
        </div>';

        // Store SQL results
        if(mysqli_stmt_store_result($stmt)){
            // Check if DB returned any result
            if(mysqli_stmt_num_rows($stmt) > 0){
                // Fetch values
                while (mysqli_stmt_fetch($stmt)) {
                    $work_happiness_array[] = $work_happiness;
                    $daily_happiness_array[] = $daily_happiness;
                    $total_happiness_array[] = $total_happiness;
                    $date_array[] = explode(" ", $journal_date)[0];
                }
                $work_happiness_JSON = json_encode($work_happiness_array);
                $daily_happiness_JSON = json_encode($daily_happiness_array);
                $total_happiness_JSON = json_encode($total_happiness_array);
                $date_JSON = json_encode($date_array);
                echo "<script>
                $(document).ready(function () {
                    showGraph();
                });
        
        
                function showGraph()
                {
                    var work_happiness_array = ".$work_happiness_JSON.";
                    var daily_happiness_array = ".$daily_happiness_JSON.";
                    var total_happiness_array = ".$total_happiness_JSON.";
                    var date_array = ".$date_JSON.";

                    // This is necessary. With out this php JSON array would be sorted.
                    var work_happiness = JSON.stringify(work_happiness_array);
                    var work_happiness = JSON.parse(work_happiness);
                    var daily_happiness = JSON.stringify(daily_happiness_array);
                    var daily_happiness = JSON.parse(daily_happiness);
                    var total_happiness = JSON.stringify(total_happiness_array);
                    var total_happiness = JSON.parse(total_happiness);

                    // Pie charts' data
                    var work_happiness_count = count(work_happiness_array);
                    var daily_happiness_count = count(daily_happiness_array);
                    var total_happiness_count = count(total_happiness_array);

                    // Line Chart
                    var lineGraph = new Chart($('#lineGraphCanvas'), {
                        type: 'line',
                        data: {
                            labels: date_array,
                            datasets: [
                                {
                                    label: 'İşte/okulda',
                                    borderColor: '#ff0000',
                                    backgroundColor: 'rgba(255,0,0,0.2)',
                                    hoverBackgroundColor: '#ff0000',
                                    data: work_happiness
                                },
                                {
                                    label: 'İş/okul dışında',
                                    borderColor: '#00ff00',
                                    backgroundColor: 'rgba(0,255,0,0.2)',
                                    hoverBackgroundColor: '#00ff00',
                                    data: daily_happiness
                                },
                                {
                                    label: 'Genelde',
                                    borderColor: '#0000ff',
                                    backgroundColor: 'rgba(0,0,255,0.2)',
                                    hoverBackgroundColor: '#0000ff',
                                    data: total_happiness
                                }
                            ]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        suggestedMin: 1,
                                        suggestedMax: 10
                                    }
                                }]
                            },
                            legend: {
                                display: true,
                                labels: {
                                    fontColor: dynamicThemeColor
                                }
                            },
                            title: {
                                display: true,
                                text: 'Zamana bağlı 3 farklı mutluluk grafiği',
                                fontColor: dynamicThemeColor
                            }
                        }
                    }); 

                    // First Pie Chart for work happiness
                    var workPieGraph = new Chart($('#workPieGraphCanvas'), {
                        type: 'pie',
                        data: {
                            labels: happiness_labels,
                            datasets: [
                                {
                                    hoverBorderColor: '#ff00ff',
                                    hoverBackgroundColor: '#CCCCCC',
                                    data: work_happiness_count,
                                    backgroundColor: happiness_label_colors
                                }
                            ]
                        },
                        options: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: {
                                    fontColor: dynamicThemeColor,
                                    usePointStyle: true
                                }
                            },
                            title: {
                                display: true,
                                text: 'İşte/okulda mutluluk sayısı',
                                fontColor: dynamicThemeColor
                            }
                        }
                    });
                    
                    // Second Doughnut Chart for daily happiness
                    var dailyPieGraph = new Chart($('#dailyPieGraphCanvas'), {
                        type: 'doughnut',
                        data: {
                            labels: happiness_labels,
                            datasets: [
                                {
                                    hoverBorderColor: '#ff00ff',
                                    data: daily_happiness_count,
                                    backgroundColor: happiness_label_colors
                                }
                            ]
                        },
                        options: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: {
                                    fontColor: dynamicThemeColor,
                                    usePointStyle: true
                                }
                            },
                            title: {
                                display: true,
                                text: 'İş/okul dışında mutluluk sayısı',
                                fontColor: dynamicThemeColor
                            }
                        }
                    });

                    // Third Polar Chart for total happiness
                    var totalPieGraph = new Chart($('#totalPieGraphCanvas'), {
                        type: 'polarArea',
                        data: {
                            labels: happiness_labels,
                            datasets: [
                                {
                                    hoverBorderColor: '#ff00ff',
                                    data: total_happiness_count,
                                    backgroundColor: happiness_label_colors
                                }
                            ]
                        },
                        options: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: {
                                    fontColor: dynamicThemeColor,
                                    usePointStyle: true
                                }
                            },
                            title: {
                                display: true,
                                text: 'Genelde mutluluk sayısı',
                                fontColor: dynamicThemeColor
                            }
                        }
                    });
                }
                </script>";
            }
            else{
                exit("<script>location.href = './graph.php?error=not-found';</script>");
            }
        }
        else{
            echo'<!--Error-->
                <div class="error" id="dbError">
                    <p>Veritabanı \'store\' hatası.
                        <button type="button"
                            class="fas fa-times-circle btn text-danger" 
                            aria-hidden="true" 
                            onclick="$(\'#dbError\').hide()">
                        </button>
                    </p> 
                </div>';
        }

        echo '
            <div class="row p-3" id="chart-container">
                <canvas id="lineGraphCanvas"></canvas>

                <br>

                <div id="pie-chart-legend" style="width: 100%;"></div>
                
                <div class="col-12 col-xl-4 px-xs-3 px-xl-0 pl-xl-2 my-3">
                    <canvas id="workPieGraphCanvas"></canvas>
                </div>

                <div class="col-12 col-xl-4 px-xs-3 px-xl-0 my-3">
                    <canvas id="dailyPieGraphCanvas"></canvas>
                </div>

                <div class="col-12 col-xl-4 px-xs-3 px-xl-0 pr-xl-2 my-3">
                    <canvas id="totalPieGraphCanvas"></canvas>
                </div>
            </div>

            <br>
        </div>';
    }

    // Section 2 of 5, show game graph
    else if($showSection === 2){
        echo'<div>
            <h1>';
                if(isset($_SESSION['name'])){
                    echo $_SESSION['name'].', ';
                }
                echo $game_name.' oynama grafiğin';
        echo '</h1>
            <hr>
            <button type="button" class="bg-show p-0">
                <a href="./show.php?game='.$game_id.'" class="bg-show p-3">Görüntüle</a>
            </button>
            <hr>';

        echo "<script>
        $(document).ready(function () {
            showGraph();
        });


        function showGraph()
        {
            var duration_array = ".$duration_JSON.";
            var date_array = ".$date_JSON.";
            
            // This is necessary. With out this php JSON array would be sorted.
            var duration = JSON.stringify(duration_array);
            var duration = JSON.parse(duration);

            var linechartdata = {
                labels: date_array,
                datasets: [
                    {
                        label: 'Süre (Saat)',
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184,0.2)',
                        hoverBackgroundColor: '#17a2b8',
                        data: duration
                    }
                ]
            };

            var maxDuruation = Math.max(...duration_array) + 1;

            
            var lineGraphTarget = $(\"#lineGraphCanvas\");

            var lineGraph = new Chart(lineGraphTarget, {
                type: 'line',
                data: linechartdata,
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                suggestedMin: 0,
                                suggestedMax: maxDuruation,
                                fontColor: dynamicThemeColor
                            },
                            scaleLabel: {
                                display: window.innerWidth > 600,
                                labelString: 'Süre (Saat)',
                                fontColor: dynamicThemeColor
                            },
                            gridLines: { 
                                color: dynamicThemeColor 
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: dynamicThemeColor
                            },
                            scaleLabel: {
                                display: window.innerWidth > 600,
                                labelString: 'Günlük tarihi',
                                fontColor: dynamicThemeColor
                            },
                            gridLines: { 
                                color: dynamicThemeColor 
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        labels: {
                            fontColor: dynamicThemeColor
                        }
                    },
                    title: {
                        display: true,
                        text: 'Zamana bağlı oyun oynanma süre grafiği',
                        fontColor: dynamicThemeColor
                    }
                }
            });
        
        }
        </script>";

        echo '
        <div class="p-3" id="chart-container">
            <canvas id="lineGraphCanvas"></canvas>

            <br>

        </div>

        <br>
    </div>';
    }

    // Section 3 of 5, show series graph
    else if($showSection === 3){
        echo'<div>
            <h1>';
                if(isset($_SESSION['name'])){
                    echo $_SESSION['name'].', ';
                }
                echo ' dizi izleme grafiğin';
        echo '</h1>
            <hr>
            <button type="button" class="bg-show p-0">
                <a href="./show.php?series='.$series_id.'" class="bg-show p-3">Görüntüle</a>
            </button>
            <hr>';

        echo "<script>
        $(document).ready(function () {
            showGraph();
        });


        function showGraph()
        {
            var duration_array = ".$duration_JSON.";
            var date_array = ".$date_JSON.";
            
            // This is necessary. With out this php JSON array would be sorted.
            var duration = JSON.stringify(duration_array);
            var duration = JSON.parse(duration);

            var linechartdata = {
                labels: date_array,
                datasets: [
                    {
                        label: 'Süre (Bölüm)',
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255,0.2)',
                        hoverBackgroundColor: '#007bff',
                        data: duration
                    }
                ]
            };

            var maxDuruation = Math.max(...duration_array) + 1;

            
            var lineGraphTarget = $(\"#lineGraphCanvas\");

            var lineGraph = new Chart(lineGraphTarget, {
                type: 'line',
                data: linechartdata,
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                suggestedMin: 0,
                                suggestedMax: maxDuruation,
                                fontColor: dynamicThemeColor
                            },
                            scaleLabel: {
                                display: window.innerWidth > 600,
                                labelString: 'Süre (Saat)',
                                fontColor: dynamicThemeColor
                            },
                            gridLines: { 
                                color: dynamicThemeColor 
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: dynamicThemeColor
                            },
                            scaleLabel: {
                                display: window.innerWidth > 600,
                                labelString: 'Günlük tarihi',
                                fontColor: dynamicThemeColor
                            },
                            gridLines: { 
                                color: dynamicThemeColor 
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        labels: {
                            fontColor: dynamicThemeColor
                        }
                    },
                    title: {
                        display: true,
                        text: 'Zamana bağlı dizi izleme süre grafiği',
                        fontColor: dynamicThemeColor
                    }
                }
            });
        
        }
        </script>";

        echo '
        <div class="p-3" id="chart-container">
            <canvas id="lineGraphCanvas"></canvas>

            <br>

        </div>

        <br>
    </div>';
    }

    // Section 4 of 5, show book graph
    else if($showSection === 4){
        echo'<div>
            <h1>';
                if(isset($_SESSION['name'])){
                    echo $_SESSION['name'].', ';
                }
                echo ' kitap okuma grafiğin';
        echo '</h1>
            <hr>
            <button type="button" class="bg-show p-0">
                <a href="./show.php?book='.$book_id.'" class="bg-show p-3">Görüntüle</a>
            </button>
            <hr>';

        echo "<script>
        $(document).ready(function () {
            showGraph();
        });


        function showGraph()
        {
            var duration_array = ".$duration_JSON.";
            var date_array = ".$date_JSON.";
            
            // This is necessary. With out this php JSON array would be sorted.
            var duration = JSON.stringify(duration_array);
            var duration = JSON.parse(duration);

            var linechartdata = {
                labels: date_array,
                datasets: [
                    {
                        label: 'Süre (Saat)',
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7,0.2)',
                        hoverBackgroundColor: '#ffc107',
                        data: duration
                    }
                ]
            };

            var maxDuruation = Math.max(...duration_array) + 1;

            
            var lineGraphTarget = $(\"#lineGraphCanvas\");

            var lineGraph = new Chart(lineGraphTarget, {
                type: 'line',
                data: linechartdata,
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                suggestedMin: 0,
                                suggestedMax: maxDuruation,
                                fontColor: dynamicThemeColor
                            },
                            scaleLabel: {
                                display: window.innerWidth > 600,
                                labelString: 'Süre (Saat)',
                                fontColor: dynamicThemeColor
                            },
                            gridLines: { 
                                color: dynamicThemeColor 
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: dynamicThemeColor
                            },
                            scaleLabel: {
                                display: window.innerWidth > 600,
                                labelString: 'Günlük tarihi',
                                fontColor: dynamicThemeColor
                            },
                            gridLines: { 
                                color: dynamicThemeColor 
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        labels: {
                            fontColor: dynamicThemeColor
                        }
                    },
                    title: {
                        display: true,
                        text: 'Zamana bağlı kitap okuma süre grafiği',
                        fontColor: dynamicThemeColor
                    }
                }
            });
        
        }
        </script>";

        echo '
        <div class="p-3" id="chart-container">
            <canvas id="lineGraphCanvas"></canvas>

            <br>

        </div>

        <br>
    </div>';
    }

    // Date Error
    else{
        // Print error message
        require "./msg/date-error.php";
    }
    ?>

</main>

<?php
    require "footer.php";
?>

