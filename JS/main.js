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

function addNewEntertainmentToDB(type) {
  // Get name value from the select
  var selectedItemValue = $("#" + type + "-select")
    .find("option:selected")
    .attr("value");
  //$("select[name=games]").change(function () {
  //if ($(this).val() == "") {
  if (selectedItemValue === "") {
    $("#add-" + type + "-modal").modal();
    /*var newThing = prompt("Enter a name for the new thing:");
    var newValue = $("option", this).length;
    $("<option>")
      .text(newThing)
      .attr("value", newValue)
      .insertBefore($("option[value=]", this));
    $(this).val(newValue);*/
  }
  //});
}

function addToTheList(type) {
  // type can be game, movie, series or book
  var ul = $("#" + type + "-list");

  // Get name value from the select
  var selectedItemValue = $("#" + type + "-select")
    .find("option:selected")
    .attr("value");

  // Get duration value
  var duration;
  if (type === "series") {
    // Series have episodes
    duration =
      "S" +
      $("#series-season-begin").val() +
      "E" +
      $("#series-episode-begin").val() +
      "-S" +
      $("#series-season-end").val() +
      "E" +
      $("#series-episode-end").val();
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
    duration == "SE-SE"
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

      // Append li element to list
      ul.append(li);
    }
  }
}

function removeFromTheList(liId) {
  // Remove element from the list
  $("li").remove("#" + liId);
}
