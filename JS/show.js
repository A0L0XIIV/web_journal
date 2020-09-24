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

    return true;
}
