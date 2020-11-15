// Init function
window.onload = init;

function init() {
  // If there is an error and it is visible, highlight it
  if($(".error").length && $(".error").is(':visible')){
    $(".error:visible").ready(highlight(".error:visible"));
  }
  // If there is a success message and it is visible, highlight it
  if($(".success").length && $(".success").is(':visible')){
    $(".success:visible").ready(highlight(".success:visible"));
  }

  // Start select2 dropdowns
  $('#game-select').select2({
    placeholder: {
      id: '-1', // the value of the option
      text: 'Oyun Seç',
    },
    selectOnClose: true,
  });
  $('#series-select').select2({
    placeholder: {
      id: '-1',
      text: 'Dizi Seç',
    },
    selectOnClose: true,
  });
  $('#movie-select').select2({
    placeholder: {
      id: '-1',
      text: 'Film Seç',
    },
    selectOnClose: true,
  });
  $('#book-select').select2({
    placeholder: {
      id: '-1',
      text: 'Kitap Seç',
    },
    selectOnClose: true,
  });

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
  /* Swicht select2 theme*/
  $(".select2-results").toggleClass("dark-select");
  $(".select2-dropdown").toggleClass("dark-select");
  $(".select2-selection").toggleClass("dark-select");
  $(".select2-selection__rendered").toggleClass("dark-select");
  /* Swicht modal theme*/
  $(".modal-content").toggleClass("dark-main");
  /* Swicht modal theme*/
  $(".table").toggleClass("table-dark");
  /* Swicht card theme*/
  $(".card").toggleClass("dark-card");
  $(".card-header").toggleClass("dark-card-header");
  $(".card-footer").toggleClass("dark-card-footer");

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

function highlight(target){
  // Highlight an element via blinking it twice
  $(target).delay(100).fadeOut().fadeIn('slow').delay(100).fadeOut().fadeIn('slow');
}