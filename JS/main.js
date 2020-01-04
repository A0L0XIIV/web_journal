function init() {}

function switchDarkTheme() {
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
}

// $(document).ready(function() {
//   $("#logout-btn").click(function() {
//     var clickBtnValue = $(this).val();
//     var ajaxurl = "header.php",
//       data = { action: clickBtnValue };
//     $.post(ajaxurl, data, function(response) {
//       // Response div goes here.
//       alert("action performed successfully");
//     });
//   });
// });

// AJAX post and showing the new review
function logout() {
  // jQuery request variable
  var request;

  // Abort any pending request
  if (request) {
    request.abort();
  }

  // Send request to server w/ seriliazed data
  request = $.ajax({
    url: "./header.php",
    type: "get",
    data: "action=logout"
  });

  // Get server's response and handle it
  request.done(function(response, textStatus, jqXHR) {
    // Success response
    if (textStatus == "success") {
      location.href = "./index.php";
      console.log("AJAX success");
    }
    // Response error
    else {
      $("#logout-btn").value = "Nope";
      console.log("AJAX failed");
    }
  });

  // Server failure response
  request.fail(function(jqXHR, textStatus, errorThrown) {
    console.error("AJAX error: " + textStatus, errorThrown);
    $("#logout-btn").value = "Nope";
    console.log("AJAX failed 2");
  });
}
