<!--Daily Entertainment: Watching movies-->
<div class="daily-movie">
    <button type="button"
            class="ent-btn bg-movie my-4"
            id="add-movie-btn"
            onclick="getEntertainmentNames('movie');">
            Film Ekle
    </button>
    
    <div id="add-movie" class="py-3" style="display:none; background-color:#ff599a1a;">
        <p class="font-weight-bolder">Film Ekle</p>
        <!--Add a movie, name & duration-->
        <div class="row">
            <div class="col-xs-3 col-sm-6">
                <select name="movie-select"
                        id="movie-select" 
                        class="custom-select" 
                        onchange="openNewEntertainmentModal('movie')">
                    <option value="-1" hidden selected>Hangi filmi seyrettin?</option>
                    <option value="" class="opt10">YENI FILM EKLE</option>
                </select>
            </div>
            <div class="col-xs-3 col-sm-6">
                <input 
                    type="number" 
                    name="movie-duration" 
                    placeholder="Süre (Saat)"
                    id="movie-duration"
                    min="0"
                    max="24"
                    step="0.5"
                    minlength="0"
                    maxlength="2"
                    style="width:45%;">
            </div>
        </div>
        <!--Add a movie to list & error messages-->
        <div class="mx-auto" style="width:100%">
            <button type="button"
                    class="add-btn bg-movie mt-2"
                    onclick="addToTheList('movie')">
                    Ekle
            </button>
        </div>
        <div id="movie-add-error" class="error mt-3" style="display:none;">
            <!--movie-add-error-->
            <p>Film adı ya da süresi uygun değil.
                <button type="button"
                    class="fa fa-times-circle btn text-danger" 
                    aria-hidden="true" 
                    onclick="$('#movie-add-error').hide()">
                </button>
            </p> 
        </div>
        <div id="movie-exist-error" class="error mt-3" style="display:none;">
            <!--movie-exist-error-->
            <p>Film zaten var, silip tekrar ekleyebilirsin.
                <button type="button"
                    class="fa fa-times-circle btn text-danger" 
                    aria-hidden="true" 
                    onclick="$('#movie-exist-error').hide()">
                </button>
            </p> 
        </div>
        <!--Movie list-->
        <ul id="movie-list" class="mb-0 px-3"></ul>
    </div>

    <div id="get-movie-names-error" class="error mt-3" style="display:none;">
        <!--get-movie-names-error-->
        <p>AJAX hatası. Film isimlerini sunucudan alamadık. 
            <button type="button"
                class="fa fa-times-circle btn text-danger" 
                aria-hidden="true" 
                onclick="$('#get-movie-names-error').hide()">
            </button>
        </p> 
    </div>
</div>