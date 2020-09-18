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
    <header>
        <!-- Website name and Navbar -->
        <nav class="navbar navbar-expand-sm navbar-dark mx-auto">

            <a class="navbar-brand" id="websiteName" href="./index.php">Günlük</a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                <span style="color:#ffffff;">Menu</span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <?php
                if(isset($_SESSION['name'])){
                    echo '<ul class="navbar-nav mx-auto row" style="width:75%;">     
                            <li class="nav-item mx-auto mt-3 mt-sm-0 col-xs-0 col-sm-2 active">
                                <button class="btn btn-success btn-block p-0">
                                    <a href="write.php" class="btn text-white">Yeni Yaz</a>
                                </button>
                            </li>
                            <li class="nav-item mx-auto mt-1 mt-sm-0 col-xs-0 col-sm-2">
                                <button class="btn btn-info btn-block p-0">
                                    <a href="edit.php" class="btn text-white">Güncelle</a>
                                </button>
                            </li>
                            <li class="nav-item mx-auto mt-1 mt-sm-0 col-xs-0 col-sm-2">
                                <button class="btn btn-primary btn-block p-0">
                                    <a href="show.php" class="btn text-white">Görüntüle</a>
                                </button>
                            </li>
                            <li class="nav-item mx-auto mt-1 mt-sm-0 col-xs-0 col-sm-2">
                                <button class="btn btn-warning btn-block p-0">
                                    <a href="graph.php" class="btn text-white">Grafik</a>
                                </button>
                            </li>
                            <li class="nav-item mx-auto mt-1 mt-sm-0 col-xs-0 col-sm-2">
                                <button class="btn btn-secondary btn-block p-0">
                                    <a href="password.php" class="btn text-white">Şifre</a>
                                </button>
                            </li>
                            <li class="nav-item mx-auto mt-1 mt-sm-0 col-xs-0 col-sm-2">
                                <button class="btn btn-danger btn-block p-0">
                                    <a href="header.php?action=logout" class="btn text-white">Çıkış yap</a>
                                </button>
                            </li>';
                }
                else{
                    echo '<ul class="navbar-nav mr-auto">
                            <li class="nav-item mx-auto">
                                <button class="btn btn-dark btn-block p-0">
                                    <a href="index.php" class="btn btn-dark">Giriş yap</a>
                                </button>
                            </li>';
                }
                ?>
                </ul>
                
                <!-- Dark theme switch -->
                <div class="custom-control custom-switch float-right" id="dark-theme-selection">
                    <input type="checkbox" class="custom-control-input" id="customSwitches" onclick="switchDarkTheme(true)">
                    <label class="custom-control-label" for="customSwitches">Karanlık</label>
                </div>
                    
            </div>
        </nav>
    </header>