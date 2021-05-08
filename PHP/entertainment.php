<?php 
    session_start();
    // Check if the session variable name is empty or not and redirect
    if (!isset($_SESSION['name'])) {
        $addEntertainmentErrorText = "You cannot access this page!";
        http_response_code(401);
        exit($addEntertainmentErrorText); 
    }
?>

<?php
    // Database connection
    require "./mysqli_connect.php";

    // Get entertainments AJAX request handler
    if ($_SERVER["REQUEST_METHOD"] === "GET"
        && !isset($_GET['name'])
        && isset($_GET['type'])) {

        // Variables
        $entertainment_name = $entertainment_id = "";
        $entertainment_image_url = "";

        // Get entertainment type
        $type = test_input($_GET['type']);

        // Each type has different tables
        // Get game names
        if($type === "game"){
            // Check DB for picked date
            $sql = "SELECT name, id, image_url FROM game ORDER BY name";
        }
        // Series SQL
        else if($type === "series"){
            // Check DB for picked date
            $sql = "SELECT name, id, image_url FROM series ORDER BY name";
        }
        // Series SQL
        else if($type === "movie"){
            // Check DB for picked date
            $sql = "SELECT name, id, image_url FROM movie ORDER BY name";
        }
        // Series SQL
        else if($type === "book"){
            // Check DB for picked date
            $sql = "SELECT name, id, image_url FROM book ORDER BY name";
        }

        // Start SQL query
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            $addEntertainmentErrorText = mysqli_error($conn);
            http_response_code(400);
            exit($addEntertainmentErrorText);
        }
        else{
            // Execute sql statement
            mysqli_stmt_execute($stmt);
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $entertainment_name, $entertainment_id, $entertainment_image_url);
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
                            'name' => htmlspecialchars($entertainment_name),
                            'img_url' => htmlspecialchars($entertainment_image_url),
                            );
                    }
                    // Return result array as JSON
                    exit(json_encode($gameArray)); 
                }
                else {
                    $addEntertainmentErrorText = mysqli_error($conn);
                    http_response_code(400);
                    exit($addEntertainmentErrorText);
                }
            }
            else {
                $addEntertainmentErrorText = mysqli_error($conn);
                http_response_code(400);
                exit($addEntertainmentErrorText);
            }
        }  
    }

    // Get last watched entertainment season and episode values
    else if($_SERVER["REQUEST_METHOD"] === "GET"
            && isset($_GET["lastWatchedSeries"])){

        // Get series id
        $series_id = test_input($_GET["lastWatchedSeries"]);

        // Get name from session
        $name = $_SESSION['name'];

        // Check if name & series ID is empty or not and redirect
        if($name == "" || $name == NULL || $series_id =="" || $series_id == NULL){
            $addEntertainmentErrorText = "Error, somethings went wrong!";
            http_response_code(400);
            exit($addEntertainmentErrorText);
        }


        // Check DB for picked date
        $sql = "SELECT end_season, end_episode FROM daily_series
                INNER JOIN gunluk ON gunluk_id=gunluk.id
                WHERE name=? AND series_id=?
                ORDER BY gunluk.date DESC
                LIMIT 1";
        // Start SQL query
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            $addEntertainmentErrorText = mysqli_error($conn);
            http_response_code(400);
            exit($addEntertainmentErrorText);
        }
        else{
            // Bind inputs to query parameters
            mysqli_stmt_bind_param($stmt, "ii", $name, $series_id);
            // Execute sql statement
            mysqli_stmt_execute($stmt);
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $end_season, $end_episode);
            // Results fetched below...
            if(mysqli_stmt_store_result($stmt)){
                // Check if DB returned any result
                if(mysqli_stmt_num_rows($stmt) > 0){
                    // Fetch values
                    //$gameArray = [];
                    while (mysqli_stmt_fetch($stmt)) {
                        //array_push($gameArray, array('value' => htmlspecialchars($work_happiness), 'name' => $work_happiness));
                        $gameArray[] = array(
                            'season' => $end_season,
                            'episode' => $end_episode,
                            );
                    }
                    // Return result array as JSON
                    exit(json_encode($gameArray)); 
                }
                else {
                    $addEntertainmentErrorText = mysqli_error($conn);
                    http_response_code(404);
                    exit($addEntertainmentErrorText);
                }
            }
            else {
                $addEntertainmentErrorText = mysqli_error($conn);
                http_response_code(400);
                exit($addEntertainmentErrorText);
            }
        }  

    }

    // AJAX request handler for adding new entertainments to DB
    else if ($_SERVER["REQUEST_METHOD"] === "POST"
        && isset($_POST["type"])
        && isset($_POST["name"])
        && isset($_POST["img_url"])) {

        // define variables and set to empty values
        $new_entertainment_name = "";
        $new_entertainment_image_url = "";
        $addEntertainmentErrorText = "";
        $id = -1;

        // Get entertainment type
        $entertainment_type = $_POST["type"];
        // Security operations on text
        $new_entertainment_name = test_input($_POST["name"]);
        $new_entertainment_image_url = test_input($_POST["img_url"]);
        // Encoding change
        $new_entertainment_name = mb_convert_encoding($new_entertainment_name, "UTF-8");
        $new_entertainment_image_url = mb_convert_encoding($new_entertainment_image_url, "UTF-8");

        // Save entertainment into DB by types
        switch($entertainment_type){
            case "game":
                $sql = "INSERT INTO game (name, image_url) VALUES (?,?)";
                break;
            case "series":
                $sql = "INSERT INTO series (name, image_url) VALUES (?,?)";
                break;
            case "movie":
                $sql = "INSERT INTO movie (name, image_url) VALUES (?,?)";
                break;
            case "book":
                $sql = "INSERT INTO book (name, image_url) VALUES (?,?)";
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
            mysqli_stmt_bind_param($stmt, "ss", $new_entertainment_name, $new_entertainment_image_url);
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
                                    'name' => $new_entertainment_name,
                                    'img_url' => $new_entertainment_image_url,
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
                    else {
                        $addEntertainmentErrorText = mysqli_error($conn);
                        http_response_code(400);
                        exit($addEntertainmentErrorText);
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

    // AJAX request handler for deleting entertainments from DB
    else if ($_SERVER["REQUEST_METHOD"] === "POST"
        && !isset($_POST["name"])
        && isset($_POST["id"])
        && isset($_POST["type"])) {

        // define variables and set to empty values
        $addEntertainmentErrorText = "";

        // Get entertainment type
        $entertainment_type = test_input($_POST["type"]);
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

    // Not get entertainment or add new entertainment request
    // Cannot access
    else {
        $addEntertainmentErrorText = "You cannot access this page.";
        http_response_code(401);
        exit($addEntertainmentErrorText);
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
      }
?>