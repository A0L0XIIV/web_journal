<body onload="init()">
<?php
    if(isset($_POST['action'])){
        session_start();
        session_unset();
        session_destroy();
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
                            echo '<li class="nav-item active">
                                <button class="btn btn-primary p-0">
                                    <a href="write.php" class="btn text-white">Yeni Yaz</a>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="btn btn-warning p-0">
                                    <a href="edit.php" class="btn text-white">Güncelle</a>
                                </button>
                            </li>
                            <li class="nav-item">
                                <input type="submit" class="btn btn-danger" id="logout-btn" name="logout" value="Çıkış yap" />
                            </li>';
                        }
                        else{
                            echo '<li class="nav-item">
                                <button class="btn p-0">
                                    <a href="index.php" class="btn btn-success">Giriş yap</a>
                                </button>
                            </li>';
                        }
                    ?>
                    <li class="nav-item">
                        <!-- Dark theme switch -->
                        <div class="custom-control custom-switch float-right" id="dark-theme-selection">
                            <input type="checkbox" class="custom-control-input" id="customSwitches" onclick="switchDarkTheme()">
                            <label class="custom-control-label" for="customSwitches">Karanlık</label>
                        </div>
                    </li>
                </ul> 
            </div>
        </nav>
    </header>
