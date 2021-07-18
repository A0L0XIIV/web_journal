<!-- Modal: Add new entertainment into database -->
<div class="modal fade" id="add-entertainment-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Yeni <span class="entertaintment-type"></span> Ekle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" 
                        name="new-entertainment-name" 
                        id="new-entertainment-name" 
                        placeholder="Ad (En fazla 50 harf)" 
                        maxlength="50" 
                        required>
                <div style="margin: 1rem;"></div>
                <input type="text" 
                        name="new-entertainment-image-src" 
                        id="new-entertainment-image-src" 
                        placeholder="Resim URL (200 harf)" 
                        maxlength="200" 
                        required>
                
                <div id="add-entertainment-success" class="success" style="display:none;">
                    <!--Success-->
                    <p><span class="entertaintment-type"></span> başarılı bir şekilde eklendi. Lütfen bekleyin... 
                        <button type="button"
                            class="fas fa-times-circle btn text-danger" 
                            aria-hidden="true" 
                            onclick="$('#add-entertainment-success').hide()">
                        </button>
                    </p> 
                </div>

                <div id="add-entertainment-error" class="error" style="display:none;">
                    <!--Error-->
                    <p>Hata meydana geldi. <span id="add-entertainment-error-text"></span> 
                        <button type="button"
                            class="fas fa-times-circle btn text-danger" 
                            aria-hidden="true" 
                            onclick="$('#add-entertainment-error').hide()">
                        </button>
                    </p> 
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="bg-logout" data-dismiss="modal">Kapat</button>
                <button type="button" class="sbmt-btn bg-login" id="add-entertainment-btn" onclick="addNewEntertainment('game');">Ekle</button>
            </div>
        </div>
    </div>
</div>