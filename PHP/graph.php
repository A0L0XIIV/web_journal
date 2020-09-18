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
    $isDatePicked = false;
    // Get name from session
    $name = $_SESSION['name'];
    // Check if name is empty or not and redirect
    if($name == "" || $name == NULL)      
        echo("<script>location.href = './index.php';</script>"); 
  
    // Database connection
    require "./mysqli_connect.php";

    // Check request method for post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!empty($_POST["show-month"])){
            $date = test_input($_POST["show-month"]);
        }
        else if(!empty($_POST["show-year"])){
            $date = test_input($_POST["show-year"]);
        }
        else{
            $error = true;
            $errorText = "Üç tarihte boş!";
        }

        if(!empty($date)){
            $isDatePicked = true;
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

<!-- Main center div-->
<main class="main" style="min-height: 89vh;">  
    
    <!--Error-->
    <div <?php if(!$error) {echo 'style="display: none;"';}?>>
        <p id="dateError" class="error">Hata meydana geldi. <?php echo $errorText;?></p>
    </div>    

    <form
        name="date-form"
        id="date-form"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        method="post"
        <?php if($isDatePicked) echo 'style="display: none;"';?>
    >
        <h1>Görüntüleme tarihi seçiniz:</h1>
        <!--Input for month, type=month-->
        <div class="input-group mb-3 justify-content-center">
            <div class="input-group-prepend">
                <span class="input-group-text" id="month-label">Ay</span>
            </div>
            <input type="month" name="show-month">
        </div>
        <!--Input for year, type=text-->
        <div class="input-group mb-3 justify-content-center">
            <div class="input-group-prepend">
                <span class="input-group-text" id="year-label">Yıl</span>
            </div>
            <input type="number" name="show-year" min="1000" max="9999" title="Sadece 4 rakam">
        </div>

        <hr>

        <!--Input for submitting the form, type=submit-->
        <div>
          <input
            type="submit"
            value="Gönder"
            name="date-picker-submit"
            class="btn btn-primary bg-warning"
            aria-pressed="false"
          />
        </div>

        <br>
    </form> 

    <!-- 2 parts, 1 of them hidden -->

    <div <?php if(!$isDatePicked) echo 'style="display: none;"';?>>
        <h1><?php
            if(isset($_SESSION['name'])){
                echo $_SESSION['name'].', ';
            }
            echo $date.' tarihili mutluluk grafiğin';
        ?></h1>

    <?php 
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
    ?> 
        <div class="row p-3" id="chart-container">
            <canvas id="lineGraphCanvas"></canvas>
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
    </div>

</main>

<?php
    require "footer.php";
?>

