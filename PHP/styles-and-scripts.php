<?php
// CSS and JS files path
$cssDir = "./CSS";
$jsDir = "./JS";

// Individual stylesheets
$styles = [
];
// Individual script files
$scripts = [
    'write.php' => 'write-edit.js',
    'edit.php' => 'write-edit.js',
    'show.php' => 'show.js',
    'graph.php' => 'graph.js',
];
// Individual page titles
$titles = [
    'index.php' => 'Giriş yap',
    'password.php' => 'Şifre değiştir',
    'write.php' => 'Günlük yaz',
    'edit.php' => 'Günlük Güncelleme',
    'show.php' => 'Günlük Görüntüleme',
    'graph.php' => 'Günlük Grafileri',
];
// Get PHP file name
$this_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Common stylesheets -->
<link rel="stylesheet" type="text/css" href="<?="$cssDir/main.css"?>">
<!-- Animation stylesheets -->
<link rel="stylesheet" type="text/css" media="only screen and (min-device-width: 991px)" href="<?="$cssDir/animation.css"?>">
<!-- CSS, specific to the current page -->
<link rel="stylesheet" type="text/css" href="<?="$cssDir/$styles[$this_page]"?>">

<!-- Common scripts -->
<script type="text/javascript" src="<?="$jsDir/main.js"?>"></script>
<!-- JS, specific to the current page -->
<script type="text/javascript" src="<?="$jsDir/$scripts[$this_page]"?>"></script>

<!-- Title of the page -->
<?php
     echo '<title>'.$titles[$this_page].'</title>';
?>
