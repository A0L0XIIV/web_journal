<!--Daily Entertainment: Watching Series-->
<div class="daily-series">
    <button type="button"
            class="ent-btn bg-series my-4"
            id="add-series-btn"
            onclick="getEntertainmentNames('series');">
            Dizi Ekle
    </button>
    
    <div id="add-series" class="py-3" style="display:none; background-color:#5da2d81a;">
        <p class="font-weight-bolder">Dizi Ekle</p>
        <!--Add a series, name & episodes-->
        <div class="row">
            <div class="col-xs-3 col-sm-6 mx-auto">
                <select name="series-select"
                        id="series-select" 
                        class="custom-select" 
                        onchange="openNewEntertainmentModal('series')">
                    <option value="-1" hidden selected>Hangi diziyi seyrettin?</option>
                    <option value="" class="opt10">YENi DİZİ EKLE</option>
                </select>
            </div>
            <div id="last-episode-btn" class="col-xs-3 col-sm-6" style="display: none;">
                <button type="button" class="bg-series" onclick="getLastWatchedSeriesEpisode()">Son bölüm +1</button>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3 col-sm-6">
                <p>Başlangıç:</p>
                <input 
                    type="number" 
                    name="series-season-begin" 
                    placeholder="Sezon (İlk izlenen)"
                    id="series-season-begin"
                    min="0"
                    max="50"
                    step="1"
                    minlength="0"
                    maxlength="2"
                    style="width:45%;">
                <input 
                    type="number" 
                    name="series-episode-begin" 
                    placeholder="Bölüm (İlk izlenen)"
                    id="series-episode-begin"
                    min="0"
                    max="50"
                    step="1"
                    minlength="0"
                    maxlength="2"
                    style="width:45%;">
            </div>
            <!-- Watched only one season, the first and the last episodes are in the same season -->
            <div class="col-xs-3 col-sm-6" id="series-episode-number">
                <p>Bölüm Sayısı:</p>
                <input 
                    type="number" 
                    name="series-watched-number" 
                    placeholder="İzlenen bölüm sayısı"
                    id="series-watched-number"
                    min="0"
                    max="50"
                    step="1"
                    minlength="0"
                    maxlength="2"
                    style="width:45%;">
                <button type="button" class="bg-series" style="width:45%" onclick="openLastEpisodeSeasonInputs()">Farklı sezon bölümleri</button>
            </div>
            <!-- Watched more than one season, the first and the last episodes are  not in the same season -->
            <div class="col-xs-3 col-sm-6" id="series-last-episode" style="display: none;">
                <p>Bitiş:</p>
                <input 
                    type="number" 
                    name="series-season-end" 
                    placeholder="Sezon (Son izlenen)"
                    id="series-season-end"
                    min="0"
                    max="50"
                    step="1"
                    minlength="0"
                    maxlength="2"
                    style="width:45%;">
                <input 
                    type="number" 
                    name="series-episode-end" 
                    placeholder="Bölüm (Son izlenen)"
                    id="series-episode-end"
                    min="0"
                    max="50"
                    step="1"
                    minlength="0"
                    maxlength="2"
                    style="width:45%;">
            </div>
        </div>
        <!--Add a series to list & error messages-->
        <div class="mx-auto" style="width:100%">
            <button type="button"
                    class="add-btn bg-series mt-2"
                    onclick="addToTheList('series')">
                    Ekle
            </button>
        </div>
        <div id="series-add-error" class="error mt-3" style="display:none;">
            <!--series-add-error-->
            <p>Dizi adı ya da bölümleri uygun değil. <br>
                Başlangıç sezon ve/veya bölüm sayısı bitiş sayılarından büyük olamaz.
                <button type="button"
                    class="fa fa-times-circle btn text-danger" 
                    aria-hidden="true" 
                    onclick="$('#series-add-error').hide()">
                </button>
            </p> 
        </div>
        <div id="series-exist-error" class="error mt-3" style="display:none;">
            <!--series-exist-error-->
            <p>Dizi zaten var, silip tekrar ekleyebilirsin. 
                <button type="button"
                    class="fa fa-times-circle btn text-danger" 
                    aria-hidden="true" 
                    onclick="$('#series-exist-error').hide()">
                </button>
            </p> 
        </div>
        <!--Series list-->
        <ul id="series-list" class="mb-0 px-3 entertainment-list"></ul>
    </div>

    <div id="get-series-names-error" class="error mt-3" style="display:none;">
        <!--get-series-names-error-->
        <p>AJAX hatası. Dizi isimlerini sunucudan alamadık.  
            <button type="button"
                class="fa fa-times-circle btn text-danger" 
                aria-hidden="true" 
                onclick="$('#get-series-names-error').hide()">
            </button>
        </p> 
    </div>
</div>