<?php 
    require "head.php";
?>

<body onload="init()">
<?php
    if(isset($_GET['action'])){
        session_start();
        session_unset();
        session_destroy();
        header("Location: ./index.php");
        exit();
    }
?>
    <header class="sticky-top">
        <!-- Website name and Navbar -->
        <nav class="navbar navbar-expand-md navbar-dark mx-auto py-md-0">

            <a class="navbar-brand py-0" id="websiteName" href="./index.php">
                <span><img src="./g.png" alt="" style="width: 1.4em;"></span>
                <span>Günlük</span>
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                <span style="color:#ffffff;">Menu</span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <?php
                if(isset($_SESSION['name'])){
                    echo '<ul class="navbar-nav mx-auto row w-75 h-100">  
                            <li class="nav-item mx-auto mt-3 mt-md-0 pl-0 pr-1 col-xs-0 col-md-2 active">
                                <a href="write.php" 
                                    id="nav-write"
                                    class="btn btn-block nav-btn hvr-bounce-to-top w-100 h-100 p-0 py-sm-3 py-md-4"">
                                    Yeni Yaz
                                </a>
                            </li>
                            <li class="nav-item mx-auto mt-1 mt-md-0 pl-0 pr-1 col-xs-0 col-md-2">
                                <a href="edit.php" 
                                    id="nav-edit"
                                    class="btn btn-block nav-btn hvr-bounce-to-top w-100 h-100 p-0 py-sm-3 py-md-4">
                                    Güncelle
                                </a>
                            </li>
                            <li class="nav-item mx-auto mt-1 mt-md-0 pl-0 pr-1 col-xs-0 col-md-2">
                                <a href="show.php" 
                                    id="nav-show"
                                    class="btn btn-block nav-btn hvr-bounce-to-top w-100 h-100 p-0 py-sm-3 py-md-4">
                                    Görüntüle
                                </a>
                            </li>
                            <li class="nav-item mx-auto mt-1 mt-md-0 pl-0 pr-1 col-xs-0 col-md-2">
                                <a href="graph.php" 
                                    id="nav-graph"
                                    class="btn btn-block nav-btn hvr-bounce-to-top w-100 h-100 p-0 py-sm-3 py-md-4">
                                    Grafik
                                </a>
                            </li>
                            <li class="nav-item mx-auto mt-1 mt-md-0 pl-0 pr-1 col-xs-0 col-md-2">
                                <a href="password.php" 
                                    id="nav-password"
                                    class="btn btn-block nav-btn hvr-bounce-to-top w-100 h-100 p-0 py-sm-3 py-md-4">
                                    Şifre
                                </a>
                            </li>
                            <li class="nav-item mx-auto mt-1 mt-md-0 pl-0 pr-1 col-xs-0 col-md-2">
                                <a href="header.php?action=logout" 
                                    id="nav-logout"
                                    class="btn btn-block nav-btn hvr-bounce-to-top w-100 h-100 p-0 py-sm-3 py-md-4"">
                                    Çıkış Yap
                                </a>
                            </li>';
                }
                else{
                    echo '<ul class="navbar-nav ml-auto h-100">
                            <li class="nav-item mx-auto">
                                <a href="index.php" 
                                    id="nav-login"
                                    class="btn btn-block nav-btn hvr-bounce-to-top w-100 h-100 p-3 p-md-4 mx-auto mx-md-5">
                                    Giriş yap
                                </a>
                            </li>';
                }
                ?>
                </ul>
                
                <!-- Dark theme switch -->
                <div class="custom-control custom-switch float-right" id="dark-theme-selection">
                    <input type="checkbox" class="custom-control-input" id="customSwitches" onclick="switchDarkTheme(true)">
                    <label class="custom-control-label text-white" for="customSwitches">Karanlık</label>
                </div>
                    
            </div>
        </nav>
    </header>