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
    $date = "";
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
        }
        else if(!empty($_GET["year"])){
            $date = test_input($_GET["year"]);
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
    else if(isset($_GET["game-id"])){
        $sql = 'SELECT DATE(date), duration FROM daily_game
                RIGHT JOIN gunluk ON gunluk_id=gunluk.id
                WHERE name=?
                AND game_id=?
                ORDER BY date'
        $sql = 'SELECT date, duration FROM (SELECT * FROM daily_game WHERE game_id=?) AS g
                RIGHT JOIN gunluk ON g.gunluk_id=gunluk.id
                WHERE name=?
                AND DATE(date) >= DATE((SELECT date_created FROM daily_game WHERE game_id=? ORDER BY date_created ASC LIMIT 1)) - 1
                AND DATE(date) <= DATE((SELECT date_created FROM daily_game WHERE game_id=? ORDER BY date_created DESC LIMIT 1)) + 1
                ORDER BY date';
    }

    // Initial GET request to load page: load select-options from DB
    else {
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
    
    <!--Error--> 
    <?php
    if($error) {
        echo '<div>
                <p id="dateError" class="error">Hata meydana geldi. '.$errorText.'</p>
            </div>';
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
                    <input type="number" name="year" id="journal-year-input" min="1000" max="9999" title="Sadece 4 rakam">
                </div>

                <br>

                <!--Button for submitting the form-->
                <div>
                    <button
                        type="submit"
                        id="date-picker-submit"
                        class="btn btn-success bg-warning"
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
                        class="btn btn-info bg-warning"
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
                        class="btn btn-primary bg-warning"
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
                        class="btn btn-warning bg-warning"
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

                    var linechartdata = {
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
                    };

                    var work_happiness_count = count(work_happiness_array);
                    var daily_happiness_count = count(daily_happiness_array);
                    var total_happiness_count = count(total_happiness_array);

                    var workpiechartdata = {
                        labels: happiness_labels,
                        datasets: [
                            {
                                hoverBorderColor: '#ff00ff',
                                hoverBackgroundColor: '#CCCCCC',
                                data: work_happiness_count,
                                backgroundColor: happiness_label_colors
                            }
                        ]
                    };

                    var dailypiechartdata = {
                        labels: happiness_labels,
                        datasets: [
                            {
                                hoverBorderColor: '#ff00ff',
                                data: daily_happiness_count,
                                backgroundColor: happiness_label_colors
                            }
                        ]
                    };

                    var totalpiechartdata = {
                        labels: happiness_labels,
                        datasets: [
                            {
                                hoverBorderColor: '#ff00ff',
                                data: total_happiness_count,
                                backgroundColor: happiness_label_colors
                            }
                        ]
                    };

                    var lineGraphTarget = $(\"#lineGraphCanvas\");
                    var workPieGraphTarget = $(\"#workPieGraphCanvas\");
                    var dailyPieGraphTarget = $(\"#dailyPieGraphCanvas\");
                    var totalPieGraphTarget = $(\"#totalPieGraphCanvas\");

                    var lineGraph = new Chart(lineGraphTarget, {
                        type: 'line',
                        data: linechartdata,
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
                                    fontColor: fontColor
                                }
                            },
                            title: {
                                display: true,
                                text: 'Zamana bağlı 3 farklı mutluluk grafiği',
                                fontColor: fontColor
                            }
                        }
                    });

                    var workPieGraph = new Chart(workPieGraphTarget, {
                        type: 'pie',
                        data: workpiechartdata,
                        options: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: {
                                    fontColor: fontColor
                                }
                            },
                            title: {
                                display: true,
                                text: 'İşte/okulda mutluluk sayısı',
                                fontColor: fontColor
                            }
                        }
                    });
                    
                    var dailyPieGraph = new Chart(dailyPieGraphTarget, {
                        type: 'doughnut',
                        data: dailypiechartdata,
                        options: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: {
                                    fontColor: fontColor
                                }
                            },
                            title: {
                                display: true,
                                text: 'İş/okul dışında mutluluk sayısı',
                                fontColor: fontColor
                            }
                        }
                    });

                    var totalPieGraph = new Chart(totalPieGraphTarget, {
                        type: 'polarArea',
                        data: totalpiechartdata,
                        options: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: {
                                    fontColor: fontColor
                                }
                            },
                            title: {
                                display: true,
                                text: 'Genelde mutluluk sayısı',
                                fontColor: fontColor
                            }
                        }
                    });
                }
                </script>";
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

        echo '
            <div class="row p-3" id="chart-container">
                <canvas id="lineGraphCanvas"></canvas>

                <br>

                <div class="col-xs-12 col-sm-4 px-xs-3 px-sm-0 pl-sm-2 my-3">
                    <canvas id="workPieGraphCanvas"></canvas>
                </div>

                <div class="col-xs-12 col-sm-4 px-xs-3 px-sm-0 my-3">
                    <canvas id="dailyPieGraphCanvas"></canvas>
                </div>

                <div class="col-xs-12 col-sm-4 px-xs-3 px-sm-0 pr-sm-2 my-3">
                    <canvas id="totalPieGraphCanvas"></canvas>
                </div>
            </div>

            <br>
        </div>';
    }

    // Section 2 of 5, show game graph
    else if($showSection === 2){
        echo '
        <div>
        </div>';
    }

    // Section 3 of 5, show game graph
    else if($showSection === 3){
        echo '
        <div>
        </div>';
    }

    // Section 4 of 5, show game graph
    else if($showSection === 4){
        echo '
        <div>
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

