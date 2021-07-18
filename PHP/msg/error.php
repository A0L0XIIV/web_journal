<!--Error-->
<div class="error" id="main-error">
    <p>Hata meydana geldi. <?=$errorText?>
        <button type="button"
            class="fas fa-times-circle btn text-danger" 
            aria-hidden="true" 
            onclick="$('#main-error').hide()">
        </button>
    </p> 
</div>