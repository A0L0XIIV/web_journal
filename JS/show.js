function journalDateSubmit(){
    // Check every input and disable them if its empty
    // This prevents sending empty parameters via GET request

    // Journal date
    if(!$("#journal-date-input").val()){
        $("#journal-date-input").prop('disabled', true);
        console.log("1");
    }
    // Journal month
    if(!$("#journal-month-input").val()){
        $("#journal-month-input").prop('disabled', true);
        console.log("21");
    }
    // Journal year
    if(!$("#journal-year-input").val()){
        $("#journal-year-input").prop('disabled', true);
        console.log("31");
    }

    // Disable submit
    $("#date-picker-submit").prop('disabled', true);

    return true;
}

// AJAX get and showing the entertainment results
function getEntertainmentNames(type) {
    // jQuery request variable
    var request;

    // Get select element via its id
    var sel = $("#" + type + "-select");
    var length = sel.children('option').length;
    // Check option count, if its filled do not send AJAX request
    if(length <= 1){
        sel.append('<option value="">Yükleniyor...</option>');
        //sel.blur();
        //sel.hide();
    
        // Abort any pending request
        if (request) {
            request.abort();
        }
    
        // Send request to server
        request = $.ajax({
        type: "POST",
        url: "write.php",
        data: { type: type },
        dataType: "json",
        });
    
        // Get server's response and handle it
        request.done(function (response, textStatus, jqXHR) {
            // Success response
            if (textStatus == "success") {
                //console.log(response + response.length);
                $("#" + type + "-select option[value='']").remove();
                //sel.empty();
                for (var i = 0; i < response.length; i++) {
                    sel.append(
                        '<option value="' +
                        response[i].id +
                        '">' +
                        response[i].desc +
                        "</option>"
                    );
                }
            }
            // Response error
            else {
                $("#" + type + "-select").append(
                '<option value="ERR" class="error">AJAX ERROR</option>'
                );
            }
            sel.show();
        });
    
        // Server failure response
        request.fail(function (jqXHR, textStatus, errorThrown) {
            console.error("AJAX error: " + textStatus, errorThrown);
            $("#get-" + type + "-names-error").css({ display: "inline" });
        });
    
        // Always promise --> success or fail
        request.always(function () {});
        
    }
}
