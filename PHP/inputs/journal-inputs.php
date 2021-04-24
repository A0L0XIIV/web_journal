<hr>

<p>İşte/okulda</p>
<select name="work_happiness" class="custom-select">
    <option value="" hidden selected>günün nasıl geçti?</option>
    <option value="10" class="opt10">&#xf118; Muhteşem</option>
    <option value="9" class="opt9">&#xf118; Şahane</option>
    <option value="8" class="opt8">&#xf118; Baya iyi</option>
    <option value="7" class="opt7">&#xf118; Gayet iyi</option>
    <option value="6" class="opt6">&#xf11a; Fena değil</option>
    <option value="5" class="opt5">&#xf11a; Normal</option>
    <option value="4" class="opt4">&#xf11a; Biraz kötü</option>
    <option value="3" class="opt3">&#xf119; Kötü</option>
    <option value="2" class="opt2">&#xf119; Berbat</option>
    <option value="1" class="opt1">&#xf119; Berbat ötesi</option>
    <option value="0" class="opt0">&#xf1db; Yorum Yok</option>
</select>

<hr>

<p>İş/okul dışında</p>
<select name="daily_happiness" class="custom-select">
    <option value="" hidden selected>günün nasıl geçti?</option>
    <option value="10" class="opt10">&#xf118; Muhteşem</option>
    <option value="9" class="opt9">&#xf118; Şahane</option>
    <option value="8" class="opt8">&#xf118; Baya iyi</option>
    <option value="7" class="opt7">&#xf118; Gayet iyi</option>
    <option value="6" class="opt6">&#xf11a; Fena değil</option>
    <option value="5" class="opt5">&#xf11a; Normal</option>
    <option value="4" class="opt4">&#xf11a; Biraz kötü</option>
    <option value="3" class="opt3">&#xf119; Kötü</option>
    <option value="2" class="opt2">&#xf119; Berbat</option>
    <option value="1" class="opt1">&#xf119; Berbat ötesi</option>
    <option value="0" class="opt0">&#xf1db; Yorum Yok</option>
</select>

<hr>

<p>Genelde</p>
<select name="total_happiness" class="custom-select">
    <option value="" hidden selected>günün nasıl geçti?</option>
    <option value="10" class="opt10">&#xf118; Muhteşem</option>
    <option value="9" class="opt9">&#xf118; Şahane</option>
    <option value="8" class="opt8">&#xf118; Baya iyi</option>
    <option value="7" class="opt7">&#xf118; Gayet iyi</option>
    <option value="6" class="opt6">&#xf11a; Fena değil</option>
    <option value="5" class="opt5">&#xf11a; Normal</option>
    <option value="4" class="opt4">&#xf11a; Biraz kötü</option>
    <option value="3" class="opt3">&#xf119; Kötü</option>
    <option value="2" class="opt2">&#xf119; Berbat</option>
    <option value="1" class="opt1">&#xf119; Berbat ötesi</option>
    <option value="0" class="opt0">&#xf1db; Yorum Yok</option>
</select>

<hr>

<p>Günlük alanı</p>
<textarea 
    name="content" 
    id="content" 
    cols="30" 
    rows="10" 
    maxlength="1000" 
    placeholder="En fazla 1000 karakter"
></textarea>
<p id="content-count" class="text-right" style="width: 90%;"></p>
<script>
    $("#content").keyup(function(){
        var count = $(this).val().length;
        var remain = 1000 - count;

        $("#content-count").text("Kalan karakter: " + remain);
        if(window.matchMedia('(prefers-color-scheme: dark)').matches)
            $("#content-count").css("color", "rgb(255," + remain/4 + "," + remain/4 + ")");
        else
            $("#content-count").css("color", "rgb(" + count/4 + ",0,0)");
    });
</script>

<hr>