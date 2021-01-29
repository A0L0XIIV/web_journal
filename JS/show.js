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
        getContentCall();
    }
});

// Either button or end of page scroll calls this function to call getContent for AJAX load
function getContentCall(){
    // Call AJAX request function
    getContent(pagenum);
    // Increase page number
    pagenum++;
}

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
            // Remove load more button
            $("#load-more-btn").hide();
            // Add end of the content message
            $("#journals").append("<div>--- Günlük sonu ---</div>");
        }
        else {
            $("#journals").append(data);
        }
    },
    error: function(){}
    });
}

// Next day button call
function goToNextDay(getDate, journalDate){
    // Split the date for day, month and year values
    var dateType = getDate.split('-');
    var address = "#" + journalDate;

    // If it is day, need to PHP call
    if(dateType.length == 3){
        // Find the next day
        var nextDay = theNextPrevDay(journalDate, true);
        // Go to the next day search page, PHP will handle
        address = "show.php?next=1&date=" + nextDay;
    }

    // If it is month or year, go in page
    else if(dateType.length == 2 || dateType.length == 1){
        // Variable to checking dates in the page
        var dateCheck = journalDate;
        // Create a new date for today
        var today = new Date();
        // Today to ISO string
        today = today.toISOString();
        // Split and remove time
        today = today.split('T')[0];
        // Max 1000 checks counter
        var counter = 0;
        // Loop until find the next day OR max 1000 times
        while(counter < 1000){
            // Get the given date's next day
            dateCheck = theNextPrevDay(dateCheck, true);
            // If it is in the page, break the loop (Find its ID in the page)
            if($("#" + dateCheck).length != 0)
                break;
            // Couldn't find it but reached today's date, break the loop
            else if(dateCheck == today)
                break;
            // Increment the counter
            counter++;
        }
        // Update the address variable for the next day's ID
        address = "#" + dateCheck;
    }
    // Go to given address
    window.location.href = address;
}

// Previous day button call
function goToPreviousDay(getDate, journalDate){
    // Split the date for day, month and year values
    var dateType = getDate.split('-');
    // Address for location update
    var address = "#" + journalDate;

    // If it is day, need to PHP call
    if(dateType.length == 3){
        // Find the previos day
        var prevDay = theNextPrevDay(journalDate, false);
        // Go to the next day search page, PHP will handle
        address = "show.php?next=0&date=" + prevDay;
    }

    // If it is month or year, go in page
    else if(dateType.length == 2 || dateType.length == 1){
        // Variable to checking dates in the page
        var dateCheck = journalDate;
        // Max 1000 checks counter
        var counter = 0;
        // Loop until find the previous day OR max 1000 times
        while(counter < 1000){
            // Get the given date's previous day
            dateCheck = theNextPrevDay(dateCheck, false);
            // If it is in the page, break the loop (Find its ID in the page)
            if($("#" + dateCheck).length != 0)
                break;
            // Increment the counter
            counter++;
        }
        // Update the address variable for the previous day's ID
        address = "#" + dateCheck;
    }
    // Go to given address
    window.location.href = address;
}

// Calculate the next or previos day with the given date string and return it as string 
function theNextPrevDay(date, isNext){
    // Create a new date object from string
    var d = new Date(date);
    if(isNext) {
        // Get the next day
        d.setDate(d.getDate() + 1);
    }
    else {
        // Get the previos day
        d.setDate(d.getDate() - 1);
    }
    // Convert to ISO formatted string
    d = d.toISOString();
    // Split it from T (time) and get only date part
    d = d.split('T')[0];
    // Return the next/previous day as string
    return d;
}