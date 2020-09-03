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
  $("#" + divId).css({ display: "inline-block" });
  // Remove the button from display
  $("#" + btnId).css({ display: "none" });
}

function addNewGameToDB(selectElement) {
  //$("select[name=games]").change(function () {
  //if ($(this).val() == "") {
  if (selectElement.value === "") {
    var newThing = prompt("Enter a name for the new thing:");
    var newValue = $("option", this).length;
    $("<option>")
      .text(newThing)
      .attr("value", newValue)
      .insertBefore($("option[value=]", this));
    $(this).val(newValue);
  }
  //});
}

function addToTheList(type) {
  // type can be game, movie, series or book
  var ul = $("#" + type + "-list");
  // Get value from the select
  var selectedItemValue = $("#" + type + "-select")
    .find("option:selected")
    .attr("value");
  // If option's value is empty or 0, do not add to list
  if (selectedItemValue == 0 || selectedItemValue == null) {
    $("#" + type + "-add-error").css({ display: "inline-block" });
  } else {
    // Check for duplication
    if (ul.find("li#" + selectedItemValue)) {
      console.log("BULDUM ");
    } else {
      // If error is visible, hide it
      $("#" + type + "-add-error").css({ display: "none" });
      // Get selected option's text
      var selectedItemName = $("#" + type + "-select")
        .find("option:selected")
        .text();
      // Create a new li element
      var li = $("<li></li>");
      var elementId = type + "-" + selectedItemValue;
      li.attr("id", elementId); // Set ID
      li.text(selectedItemName); // Set text
      li.attr("class", type + "-element"); // Set its class
      // Create remove button for li element
      var removeBtn = $("<button></button>");
      removeBtn.text("X"); // Set text
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
  $("li").remove("#" + liId);
}
