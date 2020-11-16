var pagenum = 2;
var gotAllContent = false;

function journalDateSubmit(){
    // Check every input and disable them if its empty
    // This prevents sending empty parameters via GET request

    // Journal date
    if(!$("#journal-date-input").val()){
        $("#journal-date-input").prop('disabled', true);
    }
    // Journal month
    if(!$("#journal-month-input").val()){
        $("#journal-month-input").prop('disabled', true);
    }
    // Journal year
    if(!$("#journal-year-input").val()){
        $("#journal-year-input").prop('disabled', true);
    }

    return true;
}

// When user reached the bottom of the page call AJAX function
// Check scroll, section (== 1) and gotAllContent
$(window).scroll(function(){
    if ($(window).scrollTop() == $(document).height() - $(window).height() 
        && $("#section1").length
        && !gotAllContent){
        console.log("Reached the bottom of the page, loading new page " + pagenum + "...");
        // Call AJAX request function
        getContent(pagenum);
        // Increase page number
        pagenum++;
    }
});

// AJAX get content
function getContent(pagenum) {
    // jQuery request variable
    var request;

    // Abort any pending request
    if (request) {
    request.abort();
    }

    // Send request to server
    request = $.ajax({
    type: "GET",
    url: "show.php",
    data: { page: pagenum, date: $("#date").text() },
    //dataType: "json",
    beforeSend: function(){
        $('#loader-icon').show();
    },
    complete: function(){
        $('#loader-icon').hide();
    },
    success: function(data){
        // Check if got all the content from DB
        if(data == "Finished"){
            gotAllContent = true;
            $("#journals").append("<div>--- Günlük sonu ---</div>");
        }
        else {
            $("#journals").append(data);
        }
    },
    error: function(){}
    });
}