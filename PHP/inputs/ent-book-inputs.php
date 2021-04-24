<!--Daily Entertainment: Book Reading-->
<div class="daily-book">
    <button type="button"
            class="ent-btn bg-book my-4"
            id="add-book-btn"
            onclick="getEntertainmentNames('book');">
            Kitap Ekle
    </button>
    
    <div id="add-book" class="py-3" style="display:none; background-color:#f7ee431a;">
        <p class="font-weight-bolder">Kitap Ekle</p>
        <!--Add a book, name & duration-->
        <div class="row">
            <div class="col-xs-3 col-sm-6">
                <select name="book-select"
                        id="book-select" 
                        class="custom-select" 
                        onchange="openNewEntertainmentModal('book')">
                    <option value="-1" hidden selected>Hangi kitabi okudun?</option>
                    <option value="" class="opt10">YENI KITAP EKLE</option>
                </select>
            </div>
            <div class="col-xs-3 col-sm-6">
                <input 
                    type="number" 
                    name="book-duration" 
                    placeholder="Süre (Saat)"
                    id="book-duration"
                    min="0"
                    max="24"
                    step="0.5"
                    minlength="0"
                    maxlength="2"
                    style="width:45%;">
            </div>
        </div>
        <!--Add a book to list & error messages-->
        <div class="mx-auto" style="width:100%">
            <button type="button"
                    class="add-btn bg-book mt-2"
                    onclick="addToTheList('book')">
                    Ekle
            </button>
        </div>
        <div id="book-add-error" class="error mt-3" style="display:none;">
            <!--book-add-error-->
            <p>Kitap adı ya da süresi uygun değil. 
                <button type="button"
                    class="fa fa-times-circle btn text-danger" 
                    aria-hidden="true" 
                    onclick="$('#book-add-error').hide()">
                </button>
            </p> 
        </div>
        <div id="book-exist-error" class="error mt-3" style="display:none;">
            <!--book-exist-error-->
            <p>Kitap zaten var, silip tekrar ekleyebilirsin. 
                <button type="button"
                    class="fa fa-times-circle btn text-danger" 
                    aria-hidden="true" 
                    onclick="$('#book-exist-error').hide()">
                </button>
            </p> 
        </div>
        <!--Book list-->
        <ul id="book-list" class="mb-0 px-3"></ul>
    </div>

    <div id="get-book-names-error" class="error mt-3" style="display:none;">
        <!--get-book-names-error-->
        <p>AJAX hatası. Film isimlerini sunucudan alamadık. 
            <button type="button"
                class="fa fa-times-circle btn text-danger" 
                aria-hidden="true" 
                onclick="$('#get-book-names-error').hide()">
            </button>
        </p> 
    </div>
</div>