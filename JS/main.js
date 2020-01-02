function init() {}

$(document).ready(function() {
  $("#logout-btn").click(function() {
    var clickBtnValue = $(this).val();
    var ajaxurl = "header.php",
      data = { action: clickBtnValue };
    $.post(ajaxurl, data, function(response) {
      // Response div goes here.
      alert("action performed successfully");
    });
  });
});

function switchDarkTheme() {
  // Toggle class adds or removes the class depending on the  class's presence
  /* Swicht body theme*/
  $("body").toggleClass("dark-body");
  /* Swicht main div theme*/
  $("main").toggleClass("dark-main");
  /* Swicht text inputs theme*/
  $("input[type='text']").toggleClass("dark-input");
  /* Swicht password inputs theme*/
  $("input[type='password']").toggleClass("dark-input");
  /* Swicht textarea theme*/
  $("textarea").toggleClass("dark-textarea");
  /* Swicht select theme*/
  $("select").toggleClass("dark-select");
}
