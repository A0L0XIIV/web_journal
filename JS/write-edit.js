$(document).ready(function() {
  setJournalDate();
});

// Get current date and set it to input
function setJournalDate() {
  var currentdate = new Date();

  var year = currentdate.getFullYear();
  var month = currentdate.getMonth() + 1;
  var day = currentdate.getDate();

  var hour = currentdate.getHours();
  var minute = currentdate.getMinutes();
  var second = currentdate.getSeconds();

  // 2 AM check, change date input to yesterday
  if (hour <= 2) {
      // Set day to yesterday
      day--;
  }

  var datetime = 
    year +
    "-" +
    (month < 10 ? "0"+month : month) +
    "-" +
    (day < 10 ? "0"+day : day) +
    "T" +
    (hour < 10 ? "0"+hour : hour) +
    ":" +
    (minute < 10 ? "0"+minute : minute) +
    ":" +
    (second < 10 ? "0"+second : second);

  $("#date-input").val(datetime);
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
    // Get begin, end episode and total watched episode numbers
    var beginSeason = Number($("#series-season-begin").val());
    var beginEpisode = Number($("#series-episode-begin").val());
    var endSeason = Number($("#series-season-end").val());
    var endEpisode = Number($("#series-episode-end").val());
    var numOfEpisodes = Number($("#series-watched-number").val());
    // Series episode input used
    var isWatchedNumberVisible = $("#series-episode-number").is(":visible");
    // Check season and episode errors
    if (
      beginSeason == null
      || beginSeason <= 0
      || beginEpisode == null
      || beginEpisode <= 0
      || (
        (isWatchedNumberVisible
          && (numOfEpisodes == null
          || numOfEpisodes <= 0)
        )
        || 
        (!isWatchedNumberVisible
          && (endSeason == null
          || endSeason <= 0
          || endEpisode == null
          || endEpisode <= 0 
          || beginSeason > endSeason
          || (beginSeason == endSeason
          && beginEpisode > endEpisode))
        )
      )
    ) {
      seriesError = true;
    } else {
      // Calculate the last season and episode based on watched episode numbers: User used #watchedEpisodes input
      if (isWatchedNumberVisible) {
        // The same season, it did not change
        endSeason = beginSeason;
        // #episodes
        endEpisode = beginEpisode + numOfEpisodes - 1;
      }
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

      // Get selected option's image URL data
      var selectedItemImage = $("#" + type + "-select")
        .find("option:selected")
        .attr("img-src");

      // If image source URL is empty use the default image
      if(selectedItemImage === ""){
        selectedItemImage = "./default_entertainment.png";
      }

      // Create a new li card element
      var li = $("<li></li>");
      var elementId = type + "-" + selectedItemValue;
      li.attr("id", elementId); // Set ID
      // Set classes - Every type has different color
      li.attr("class", "card entertainment-list-item " + type + "-element bg-" + type + " mt-2 mx-auto");

      // Create card image
      var img = $("<img>");
      img.attr("class", "card-img-top entertainment-card-image");
      img.attr("src", selectedItemImage);
      //img.attr("alt", selectedItemName);

      // Create card body
      var cardBody = $("<div></div>");
      cardBody.attr("class", "card-body entertainment-card-body p-2");

      // Create card title
      var cardTitle = $("<h3></h3>");
      cardTitle.attr("class", "card-title entertainment-card-title");
      cardTitle.text(selectedItemName);

      // Create card text
      var cardText = $("<p></p>");
      cardText.attr("class", "card-text entertainment-card-text");
      cardText.text(duration);

      // Create remove button for li element
      var removeBtn = $(
        "<button>" +
          '<span class="fa-stack fa-lg">' +
            '<i class="fas fa-circle fa-stack-1x" style="color:white;"></i>' +
            '<i class="fas fa-times-circle fa-stack-1x" style="color:red;"></i>' +
          '</span>' +
        "</button>"
      );
      removeBtn.attr("type", "button"); // Set type
      removeBtn.attr("class", "btn entertainment-card-remove-btn"); // Set class
      removeBtn.attr("onclick", "removeFromTheList('" + elementId + "')"); // Set function

      // Append button to li element
      li.append(img);
      cardBody.append(cardTitle);
      cardBody.append(cardText);
      cardBody.append(removeBtn);
      li.append(cardBody);

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
    type: "GET",
    url: "entertainment.php",
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
            '" img-src="' +
            response[i].img_url +
            '">' +
            response[i].name +
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
  // Show last watched series episode button
  else if (type === "series"
      && selectedItemValue !== ""
      && selectedItemValue !== 0 ) {
    $("#last-episode-btn").show();
  }
}

// AJAX add new entertainment elements in to DB
function addNewEntertainment(type) {
  // jQuery request variable
  var request;
  var newEntertainmentName = $("#new-entertainment-name").val();
  var newEntertainmentImgSrc = $("#new-entertainment-image-src").val();

  // Abort any pending request
  if (request) {
    request.abort();
  }

  // Send request to server
  request = $.ajax({
    type: "POST",
    url: "entertainment.php",
    data: { type: type, name: newEntertainmentName, img_url: newEntertainmentImgSrc },
    dataType: "json",
  });

  // Get server's response and handle it
  request.done(function (response, textStatus, jqXHR) {
    // Success response
    if (textStatus == "success") {
      //console.log(response + response.length);
      var sel = $("#" + type + "-select");
      sel.append(
        '<option value="' +
        response.id +
        '" img-src="' +
        response.img_url +
        '">' +
        response.name +
        "</option>"
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
    url: "entertainment.php",
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
        // Check table row count and remove either row or table (2 rows: 1 header)
        if (rowCount === 2) {
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

function getLastWatchedSeriesEpisode(){
  // Get name value from the select
  var selectedItemValue = $("#series-select")
    .find("option:selected")
    .attr("value");

  // AJAX request to get last watched episode
  if(selectedItemValue !==0
    || selectedItemValue !== ""){
    // jQuery request variable
    var request;

    // Abort any pending request
    if (request) {
      request.abort();
    }

    // Send request to server
    request = $.ajax({
      type: "GET",
      url: "entertainment.php",
      data: { lastWatchedSeries: selectedItemValue },
      dataType: "json",
    });

    // Get server's response and handle it
    request.done(function (response, textStatus, jqXHR) {
      // Success response
      if (textStatus == "success") {
        $("#last-episode-btn button").text('Son bölüm +1');
        //console.log(response + response.length);
        var beginSeason = $("#series-season-begin");
        var beginEpisode = $("#series-episode-begin");
        for (var i = 0; i < response.length; i++) {
          beginSeason.val(response[i].season);
          beginEpisode.val(response[i].episode + 1);
        }
      }
      // Response error
      else {
        $("#last-episode-btn button").text('Hata!');
      }
    });

    // Server failure response
    request.fail(function (jqXHR, textStatus, errorThrown) {
      console.error("AJAX error: " + textStatus, errorThrown);
      $("#last-episode-btn button").text('Bulamadık!');
    });

    // Always promise --> success or fail
    request.always(function () {});
  }
}

function openLastEpisodeSeasonInputs() {
  // Hide series number of episode watched input area
  $('#series-episode-number').hide();
  // Show series watched last season and episode input area
  $('#series-last-episode').show();
}