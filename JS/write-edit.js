// Get current date and set it to input
function getDate() {
  var currentdate = new Date();

  // 2 AM check, ask user for saving journal for yesterday or today
  if (currentdate.getHours() <= 2) {
    if (
      confirm(
        "Saat 12 ile 2 arasında olduğu için bu günlüğü dün tarihli kaydetmek ister misin?"
      )
    ) {
      // Set date to yesterday
      currentdate.setDate(currentdate.getDate() - 1);
      alert("Tarih " + currentdate + " olarak kaydedildi.");
    } else {
      alert("Tarih " + currentdate + " olarak kaydedildi.");
    }
  }

  var datetime =
    currentdate.getFullYear() +
    "-" +
    (currentdate.getMonth() + 1) +
    "-" +
    currentdate.getDate() +
    " " +
    currentdate.getHours() +
    ":" +
    currentdate.getMinutes() +
    ":" +
    currentdate.getSeconds();
  //console.log(datetime);
  $("#date-input").val(datetime);
  console.log(datetime);
  // Return true
  return true;
}

/*Game, movie, book section functions*/

// Initial button to display content
function sectionDisplay(type) {
  var btnId = "add-" + type + "-btn";
  var divId = "add-" + type;
  // Show new content
  $("#" + divId).show();
  // Hide the button
  $("#" + btnId).hide();
}

// Add new entertainment elements into list to show to user
function addToTheList(type) {
  // type can be game, movie, series or book
  var ul = $("#" + type + "-list");

  // Get name value from the select
  var selectedItemValue = $("#" + type + "-select")
    .find("option:selected")
    .attr("value");

  // Get duration value
  var duration;
  var seriesError = false;
  if (type === "series") {
    // Get begin and end episode number
    var beginSeason = Number($("#series-season-begin").val());
    var beginEpisode = Number($("#series-episode-begin").val());
    var endSeason = Number($("#series-season-end").val());
    var endEpisode = Number($("#series-episode-end").val());
    // Check season and episode errors
    if (
      beginSeason == null ||
      beginSeason == 0 ||
      beginEpisode == null ||
      beginEpisode == 0 ||
      endSeason == null ||
      endSeason == 0 ||
      endEpisode == null ||
      endEpisode == 0 ||
      beginSeason > endSeason ||
      beginEpisode > endEpisode
    ) {
      seriesError = true;
    } else {
      // Series have episodes
      duration =
        "S" +
        beginSeason +
        "E" +
        beginEpisode +
        "-S" +
        endSeason +
        "E" +
        endEpisode;
    }
  } else {
    // Game, movie and books have duration (hour)
    duration = $("#" + type + "-duration").val() + "S";
  }

  // If option's or duration's value is empty or 0, do not add to list
  if (
    selectedItemValue == 0 ||
    selectedItemValue == null ||
    duration == "0S" ||
    duration == "S" ||
    seriesError
  ) {
    $("#" + type + "-add-error").show();
  } else {
    // If add error is visible, hide it
    $("#" + type + "-add-error").hide();

    // Check for duplication in ul for li --> #game-list #game-ID
    if ($("#" + type + "-list li#" + type + "-" + selectedItemValue).length) {
      $("#" + type + "-exist-error").show();
    } else {
      // If exist error is visible, hide it
      $("#" + type + "-exist-error").hide();

      // Get selected option's text
      var selectedItemName = $("#" + type + "-select")
        .find("option:selected")
        .text();

      // Create a new li element
      var li = $("<li></li>");
      var elementId = type + "-" + selectedItemValue;
      li.attr("id", elementId); // Set ID
      li.text(selectedItemName + " | " + duration); // Set text
      li.attr("style", "width: fit-content;"); // Set width
      // Set classes - Every type has different color
      if (type === "game") {
        li.attr("class", type + "-element card bg-info mt-2 px-3 py-2 mx-auto");
      } else if (type === "series") {
        li.attr(
          "class",
          type + "-element card bg-primary mt-2 px-3 py-2 mx-auto"
        );
      } else if (type === "movie") {
        li.attr(
          "class",
          type + "-element card bg-secondary mt-2 px-3 py-2 mx-auto"
        );
      } else if (type === "book") {
        li.attr(
          "class",
          type + "-element card bg-warning mt-2 px-3 py-2 mx-auto"
        );
      } else {
        li.attr(
          "class",
          type + "-element card bg-danger mt-2 px-3 py-2 mx-auto"
        );
      }

      // Create remove button for li element
      var removeBtn = $(
        "<button>" +
          '<i class="fa fa-trash" aria-hidden="true"></i>' +
          "</button>"
      );
      removeBtn.attr("type", "button"); // Set type
      removeBtn.attr("class", "btn btn-danger mx-auto"); // Set class
      removeBtn.attr("style", "width: fit-content;"); // Set width
      removeBtn.attr("onclick", "removeFromTheList('" + elementId + "')"); // Set function

      // Append button to li element
      li.append(removeBtn);

      // Hidden input for entertainment name (form request)
      var hiddenInputName = $('<input type="hidden" />');
      var listSize = $("#" + type + "-list li").length;

      hiddenInputName.attr("name", type + "[" + listSize + "][id]"); // Set name
      hiddenInputName.attr("value", selectedItemValue); // Set value

      // Append input to li element
      li.append(hiddenInputName);

      // Hidden input for entertainment duration (form request)
      var hiddenInputDuration = $('<input type="hidden" />');

      hiddenInputDuration.attr("name", type + "[" + listSize + "][duration]"); // Set name
      hiddenInputDuration.attr("value", duration); // Set value

      // Append input to li element
      li.append(hiddenInputDuration);

      // Append li element to list
      ul.append(li);
    }
  }
}

// Remove entertainment elements from lists
function removeFromTheList(liId) {
  // Remove element from the list
  $("li").remove("#" + liId);
}

// AJAX get and showing the entertainment results
function getEntertainmentNames(type) {
  // jQuery request variable
  var request;

  // Abort any pending request
  if (request) {
    request.abort();
  }

  // Send request to server
  request = $.ajax({
    type: "POST",
    //url: "write.php",
    data: { type: type },
    dataType: "json",
  });

  // Get server's response and handle it
  request.done(function (response, textStatus, jqXHR) {
    // Show section
    sectionDisplay(type);
    // Success response
    if (textStatus == "success") {
      //console.log(response + response.length);
      var sel = $("#" + type + "-select");
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
    // If AJAX error is displayed, hide it
    $("#get-" + type + "-names-error").hide();
  });

  // Server failure response
  request.fail(function (jqXHR, textStatus, errorThrown) {
    console.error("AJAX error: " + textStatus, errorThrown);
    $("#get-" + type + "-names-error").show();
  });

  // Always promise --> success or fail
  request.always(function () {});
}

// Open new entertainment modal
function openNewEntertainmentModal(type) {
  // Get name value from the select
  var selectedItemValue = $("#" + type + "-select")
    .find("option:selected")
    .attr("value");
  // Option's value is empty, open modal
  if (selectedItemValue === "") {
    // Change text based on type
    switch (type) {
      case "game":
        $(".entertaintment-type").text("Oyun");
        break;
      case "series":
        $(".entertaintment-type").text("Dizi");
        break;
      case "movie":
        $(".entertaintment-type").text("Film");
        break;
      case "book":
        $(".entertaintment-type").text("Kitap");
        break;
      default:
        $(".entertaintment-type").text("Eğlence ürünü");
        break;
    }
    // Change onclick function variable
    $("#add-entertainment-btn").attr(
      "onclick",
      "addNewEntertainment('" + type + "')"
    );
    // Open modal
    $("#add-entertainment-modal").modal();
  }
}

// Add new entertainment elements in to DB
function addNewEntertainment(type) {
  // jQuery request variable
  var request;
  var newEntertainmentName = $("#new-entertainment-name").val();

  // Abort any pending request
  if (request) {
    request.abort();
  }

  // Send request to server
  request = $.ajax({
    type: "POST",
    data: { type: type, name: newEntertainmentName },
    dataType: "json",
  });

  // Get server's response and handle it
  request.done(function (response, textStatus, jqXHR) {
    // Success response
    if (textStatus == "success") {
      //console.log(response + response.length);
      var sel = $("#" + type + "-select");
      sel.append(
        '<option value="' + response.id + '">' + response.desc + "</option>"
      );

      // If AJAX error is displayed, hide it
      $("#add-entertainment-error").hide();
      // Display success message
      $("#add-entertainment-success").show();
      // Close the modal after successful operation (1s delay)
      setTimeout(function () {
        $("#add-entertainment-modal").modal("hide");
        // Clear input value for next submission
        $("#new-entertainment-name").val("");
        // Hide success message for next submission
        $("#add-entertainment-success").hide();
      }, 1000);
    }
    // Error response
    else {
      $("#add-entertainment-error").show();
      $("#add-entertainment-error-text").text("AJAX error! " + response.errMsg);
    }
  });

  // Server failure response
  request.fail(function (jqXHR, textStatus, errorThrown) {
    console.error("AJAX error: " + textStatus, errorThrown);
    $("#add-entertainment-error").show();
    $("#add-entertainment-error-text").text(
      "AJAX error, request failed! " + jqXHR.responseText
    );
  });

  // Always promise --> success or fail
  request.always(function () {});
}

// Remove entertainment elements from DB
function deleteEntertaimmentFromDB(type, daily_id) {
  // jQuery request variable
  var request;

  // Abort any pending request
  if (request) {
    request.abort();
  }

  // Send request to server
  request = $.ajax({
    type: "POST",
    data: { type: type, id: daily_id },
  });

  // Get row id
  var rowId = type + "-row-" + daily_id;

  // Get server's response and handle it
  request.done(function (response, textStatus, jqXHR) {
    // Success response
    if (textStatus == "success") {
      // Hide delete button
      $("#" + rowId + " .remove-button").hide();
      // If AJAX error is displayed, hide it
      $("#" + rowId + " .error").hide();
      // Show successfully deleted message
      $("#" + rowId + " .success").show();

      // After a second, delete the entire row or table (1s delay)
      setTimeout(function () {
        var rowCount = $("#" + type + "-table tr").length;
        // Check table row count and remove either row or table (1 row)
        if (rowCount === 1) {
          $("#" + type + "-table").remove();
        } else {
          $("#" + rowId).remove();
        }
      }, 1000);
    }
    // Error response
    else {
      // Hide delete button
      $("#" + rowId + " .remove-button").hide();
      // If AJAX there is an error, display it
      $("#" + rowId + " .error").show();
      // Error message
      $("#" + rowId + " .error-msg").text("AJAX error! " + response.errMsg);
    }
  });

  // Server failure response
  request.fail(function (jqXHR, textStatus, errorThrown) {
    console.error("AJAX error: " + textStatus, errorThrown);
    // Hide delete button
    $("#" + rowId + " .remove-button").hide();
    // If AJAX there is an error, display it
    $("#" + rowId + " .error").show();
    // Error message
    $("#" + rowId + " .error-msg").text(
      "AJAX error, request failed! " + jqXHR.responseText
    );
  });

  // Always promise --> success or fail
  request.always(function () {});
}
