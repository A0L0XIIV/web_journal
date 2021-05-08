<!--Daily Entertainment: Playing Games-->
<div class="daily-game">
    <button type="button"
            class="ent-btn bg-game my-4"
            id="add-game-btn"
            onclick="getEntertainmentNames('game');">
            Oyun Ekle
    </button>
    
    <div id="add-game" class="py-3" style="display:none; background-color:#2bc5001a;">
        <p class="font-weight-bolder">Oyun Ekle</p>
        <!--Add a game, name & duration-->
        <div class="row">
            <div class="col-xs-3 col-sm-6">
                <select name="game-select"
                        id="game-select" 
                        class="custom-select"
                        onchange="openNewEntertainmentModal('game')">
                    <option value="-1" hidden selected>Hangi oyunu oynadın?</option>
                    <option value="" class="opt10">YENi OYUN EKLE</option>
                </select>
            </div>
            <div class="col-xs-3 col-sm-6">
                <input 
                    type="number" 
                    name="game-duration" 
                    placeholder="Süre (Saat)"
                    id="game-duration"
                    min="0"
                    max="24"
                    step="0.5"
                    minlength="0"
                    maxlength="2"
                    style="width:45%;">
            </div>
        </div>
        <!--Add a game to list & error messages-->
        <div class="mx-auto" style="width:100%">
            <button type="button"
                    class="add-btn bg-game mt-2"
                    onclick="addToTheList('game')">
                    Ekle
            </button>
        </div>
        <div id="game-add-error" class="error mt-3" style="display:none;">
            <!--game-add-error-->
            <p>Oyun adı ya da süresi uygun değil. 
                <button type="button"
                    class="fa fa-times-circle btn text-danger" 
                    aria-hidden="true" 
                    onclick="$('#game-add-error').hide()">
                </button>
            </p> 
        </div>
        <div id="game-exist-error" class="error mt-3" style="display:none;">
            <!--game-exist-error-->
            <p>Oyun zaten var, silip tekrar ekleyebilirsin. 
                <button type="button"
                    class="fa fa-times-circle btn text-danger" 
                    aria-hidden="true" 
                    onclick="$('#game-exist-error').hide()">
                </button>
            </p> 
        </div>
        <!--Game list-->
        <ul id="game-list" class="mb-0 px-3 entertainment-list"></ul>
    </div>

    <div id="get-game-names-error" class="error mt-3" style="display:none;">
        <!--get-game-names-error-->
        <p>AJAX hatası. Oyun isimlerini sunucudan alamadık.  
            <button type="button"
                class="fa fa-times-circle btn text-danger" 
                aria-hidden="true" 
                onclick="$('#get-game-names-error').hide()">
            </button>
        </p> 
    </div>
</div>