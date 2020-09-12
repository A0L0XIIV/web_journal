// Init function
window.onload = init;

function init() {
  // Set dark theme initial value
  initializeCookieIsDarkTheme();
  var isDarkTheme = getCookie("isDarkTheme");
  if (isDarkTheme == "true") {
    console.log("Cookie Theme: DARK");
  } else if (isDarkTheme == "false") {
    console.log("Cookie Theme: LIGHT");
  } else {
    console.log("Cookie Theme: EMPTY");
  }
}

function switchDarkTheme(isUserChangeTheme) {
  // Toggle class adds or removes the class depending on the  class's presence
  /* Swicht body theme*/
  $("body").toggleClass("dark-body");
  /* Swicht main div theme*/
  $("main").toggleClass("dark-main");
  /* Swicht inputs theme*/
  $("input").toggleClass("dark-input");
  /* Swicht textarea theme*/
  $("textarea").toggleClass("dark-textarea");
  /* Swicht select theme*/
  $("select").toggleClass("dark-select");
  /* Swicht modal theme*/
  $(".modal-content").toggleClass("dark-main");

  if (isUserChangeTheme) {
    // Change isDarkTheme in cookie.
    changeCookieIsDarkTheme();
    console.log("Cookie Theme Changed.");
  }
}

function initializeCookieIsDarkTheme() {
  // Get isDarkTheme cookie.
  var isDarkTheme = getCookie("isDarkTheme");
  // Check if its empty or false
  if (isDarkTheme == "" || isDarkTheme == null) {
    // Set isDarkTheme false in cookie.
    document.cookie = "isDarkTheme=false";
  }
  // If cookie isDarkTheme is true, call switchDarkTheme function
  else if (isDarkTheme == "true") {
    switchDarkTheme(false);
    // If the them is dark, check the checkbox for it
    $("#customSwitches").prop("checked", true);
  }
}

function changeCookieIsDarkTheme() {
  // Get isDarkTheme cookie.
  var isDarkTheme = getCookie("isDarkTheme");
  // Check if its empty or false
  if (isDarkTheme == "" || isDarkTheme == null) {
    // Set isDarkTheme false in cookie.
    document.cookie = "isDarkTheme=false";
  } else if (isDarkTheme == "false") {
    // Set isDarkTheme true in cookie.
    document.cookie = "isDarkTheme=true";
  } else {
    // Set isDarkTheme false in cookie.
    document.cookie = "isDarkTheme=false";
  }
}

function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(";");
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == " ") {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

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
  $("#" + divId).css({ display: "inline" });
  // Remove the button from display
  $("#" + btnId).css({ display: "none" });
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
    var beginSeason = $("#series-season-begin").val();
    var beginEpisode = $("#series-episode-begin").val();
    var endSeason = $("#series-season-end").val();
    var endEpisode = $("#series-episode-end").val();
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
    $("#" + type + "-add-error").css({ display: "inline-block" });
  } else {
    // If add error is visible, hide it
    $("#" + type + "-add-error").css({ display: "none" });

    // Check for duplication in ul for li --> #game-list #game-ID
    if ($("#" + type + "-list li#" + type + "-" + selectedItemValue).length) {
      $("#" + type + "-exist-error").css({ display: "inline-block" });
    } else {
      // If exist error is visible, hide it
      $("#" + type + "-exist-error").css({ display: "none" });

      // Get selected option's text
      var selectedItemName = $("#" + type + "-select")
        .find("option:selected")
        .text();

      // Create a new li element
      var li = $("<li></li>");
      var elementId = type + "-" + selectedItemValue;
      li.attr("id", elementId); // Set ID
      li.text(selectedItemName + " | " + duration); // Set text
      // Set classes - Every type has different color
      if (type === "game") {
        li.attr("class", type + "-element card bg-info mt-2");
      } else if (type === "series") {
        li.attr("class", type + "-element card bg-primary mt-2");
      } else if (type === "movie") {
        li.attr("class", type + "-element card bg-secondary mt-2");
      } else if (type === "book") {
        li.attr("class", type + "-element card bg-warning mt-2");
      } else {
        li.attr("class", type + "-element card bg-danger mt-2");
      }

      // Create remove button for li element
      var removeBtn = $(
        "<button>" +
          '<i class="fa fa-trash" aria-hidden="true"></i>' +
          "</button>"
      );
      removeBtn.attr("type", "button"); // Set type
      removeBtn.attr("class", "btn btn-danger"); // Set class
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
    $("#get-" + type + "-names-error").css({ display: "none" });
  });

  // Server failure response
  request.fail(function (jqXHR, textStatus, errorThrown) {
    console.error("AJAX error: " + textStatus, errorThrown);
    $("#get-" + type + "-names-error").css({ display: "inline" });
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
      $("#add-entertainment-error").css({ display: "none" });
      // Display success message
      $("#add-entertainment-success").css({ display: "inline" });
      // Close the modal after successful operation (1s delay)
      setTimeout(function () {
        $("#add-entertainment-modal").modal("hide");
      }, 1000);
    }
    // Error response
    else {
      $("#add-entertainment-error").css({ display: "inline" });
      $("#add-entertainment-error-text").text("AJAX error! " + response.errMsg);
    }
  });

  // Server failure response
  request.fail(function (jqXHR, textStatus, errorThrown) {
    console.error("AJAX error: " + textStatus, errorThrown);
    $("#add-entertainment-error").css({ display: "inline" });
    $("#add-entertainment-error-text").text(
      "AJAX error, request failed! " + jqXHR.responseText
    );
  });

  // Always promise --> success or fail
  request.always(function () {});
}
