<?php
// CSS and JS files path
$cssDir = "./CSS";
$jsDir = "./JS";

// Individual stylesheets
$styles = [
    'index.php' => 'index.css',
    'write.php' => 'write.css',
    'graph.php' => 'graph.css'
];
// Individual script files
$scripts = [
    'index.php' => 'index.js',
    'write.php' => 'write.js',
    'graph.php' => 'graph.js'
];
// Individual page titles
$titles = [
    'index.php' => 'Giriş yap',
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
