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
                <ul class="navbar-nav mr-auto">     
                    <?php
                        if(isset($_SESSION['name'])){
                            echo '<li class="nav-item mx-auto active">
                                <button class="btn btn-success p-0">
                                    <a href="write.php" class="btn text-white">Yeni Yaz</a>
                                </button>
                            </li>
                            <li class="nav-item mx-auto">
                                <button class="btn btn-info p-0">
                                    <a href="edit.php" class="btn text-white">Güncelle</a>
                                </button>
                            </li>
                            <li class="nav-item mx-auto">
                                <button class="btn btn-primary p-0">
                                    <a href="show.php" class="btn text-white">Görüntüle</a>
                                </button>
                            </li>
                            <li class="nav-item mx-auto">
                                <button class="btn btn-warning p-0">
                                    <a href="graph.php" class="btn text-white">Grafik</a>
                                </button>
                            </li>
                            <li class="nav-item mx-auto">
                                <button class="btn btn-secondary p-0">
                                    <a href="password.php" class="btn text-white">Şifre</a>
                                </button>
                            </li>
                            <li class="nav-item mx-auto">
                                <button class="btn btn-danger p-0">
                                    <a href="header.php?action=logout" class="btn text-white">Çıkış yap</a>
                                </button>
                            </li>';
                        }
                        else{
                            echo '<li class="nav-item mx-auto">
                                <button class="btn p-0">
                                    <a href="index.php" class="btn btn-dark">Giriş yap</a>
                                </button>
                            </li>';
                        }
                    ?>
                    <li class="nav-item">
                        <!-- Dark theme switch -->
                        <div class="custom-control custom-switch float-right" id="dark-theme-selection">
                            <input type="checkbox" class="custom-control-input" id="customSwitches" onclick="switchDarkTheme(true)">
                            <label class="custom-control-label" for="customSwitches">Karanlık</label>
                        </div>
                    </li>
                </ul> 
            </div>
        </nav>
    </header>