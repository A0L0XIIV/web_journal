<?php 
    require "head.php";
?>

<?php 
    require "header.php";
?>

<!-- Main center div-->
<main class="main">
    
    <h1>Günlüğe hoşgeldin
        <?php
            if(isset($_SESSION['name'])){
                echo ' '.$_SESSION['name'];
            }
        ?>!
    </h1>
    <p>İşte/okulda</p>
    <select>
        <option value="" hidden selected>günün nasıl geçti?</option>
        <option value="10" id="opt10">&#xf118; Muhteşem</option>
        <option value="9" id="opt9">&#xf118; Şahane</option>
        <option value="8" id="opt8">&#xf118; Baya iyi</option>
        <option value="7" id="opt7">&#xf118; Gayet iyi</option>
        <option value="6" id="opt6">&#xf11a; Fena değil</option>
        <option value="5" id="opt5">&#xf11a; Normal</option>
        <option value="4" id="opt4">&#xf11a; Biraz kötü</option>
        <option value="3" id="opt3">&#xf119; Kötü</option>
        <option value="2" id="opt2">&#xf119; Berbat</option>
        <option value="1" id="opt1">&#xf119; Berbat ötesi</option>
        <option value="0" id="opt0">&#xf1db; Yorum Yok</option>
    </select>

    <br>
    
    <p>İş/okul dışında</p>
    <select>
        <option value="" hidden selected>günün nasıl geçti?</option>
        <option value="10" <?php if($status=="10") { echo "selected"; } ?> >Muhteşem</option>
        <option value="9" <?php if($status=="9") { echo "selected"; } ?> >Süper</option>
        <option value="8" <?php if($status=="8") { echo "selected"; } ?> >Baya iyi</option>
        <option value="7" <?php if($status=="7") { echo "selected"; } ?> >Gayet iyi</option>
        <option value="6" <?php if($status=="6") { echo "selected"; } ?> >Fena değil</option>
        <option value="5" <?php if($status=="5") { echo "selected"; } ?> >Normal</option>
        <option value="4" <?php if($status=="4") { echo "selected"; } ?> >Biraz kötü</option>
        <option value="3" <?php if($status=="3") { echo "selected"; } ?> >Kötü</option>
        <option value="2" <?php if($status=="2") { echo "selected"; } ?> >Berbat</option>
        <option value="1" <?php if($status=="1") { echo "selected"; } ?> >Berbat ötesi</option>
        <option value="0" <?php if($status=="0") { echo "selected"; } ?> >Yorum Yok</option>
    </select>

    <br>
    
    <p>Genelde</p>
    <select>
        <option value="" hidden selected>günün nasıl geçti?</option>
        <option value="10" <?php if($status=="10") { echo "selected"; } ?> >Muhteşem</option>
        <option value="9" <?php if($status=="9") { echo "selected"; } ?> >Süper</option>
        <option value="8" <?php if($status=="8") { echo "selected"; } ?> >Baya iyi</option>
        <option value="7" <?php if($status=="7") { echo "selected"; } ?> >Gayet iyi</option>
        <option value="6" <?php if($status=="6") { echo "selected"; } ?> >Fena değil</option>
        <option value="5" <?php if($status=="5") { echo "selected"; } ?> >Normal</option>
        <option value="4" <?php if($status=="4") { echo "selected"; } ?> >Biraz kötü</option>
        <option value="3" <?php if($status=="3") { echo "selected"; } ?> >Kötü</option>
        <option value="2" <?php if($status=="2") { echo "selected"; } ?> >Berbat</option>
        <option value="1" <?php if($status=="1") { echo "selected"; } ?> >Berbat ötesi</option>
        <option value="0" <?php if($status=="0") { echo "selected"; } ?> >Yorum Yok</option>
    </select>

    <br>

    <p>Günlük alanı</p>
    <textarea 
        name="content" 
        id="content" 
        cols="30" 
        rows="10" 
        maxlength="500" 
        placeholder="max 500 harf"
        value="<?php if(isset($_REQUEST['parkDescription'])) echo $_REQUEST['parkDescription'];?>"
    ></textarea>

</main>

<?php
    require "footer.php";
?>