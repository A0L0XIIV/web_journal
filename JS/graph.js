var happiness_labels = [
  "Yorum Yok",
  "Berbat ötesi",
  "Berbat",
  "Kötü",
  "Biraz kötü",
  "Normal",
  "Fena değil",
  "Gayet iyi",
  "Baya iyi",
  "Şahane",
  "Muhteşem"
];
var happiness_label_colors = [
  "#ff0077",
  "#770000",
  "#ff0000",
  "#ff7700",
  "#ffbb00",
  "#ffff00",
  "#00dd00",
  "#007777",
  "#00ffff",
  "#0077ff",
  "#7700ff"
];
var fontColor;

function init() {
  // Get dark theme initial value
  var isDarkTheme = getCookie("isDarkTheme");
  if (isDarkTheme == "true") {
    fontColor = "#ffffff";
  } else if (isDarkTheme == "false") {
    fontColor = "#000000";
  } else {
    fontColor = "#7f7f7f";
  }
}

function count(array) {
  array.sort();

  var current = null;
  var count = 0;
  var result = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

  for (var i = 0; i < array.length; i++) {
    if (array[i] != current) {
      if (count > 0) {
        // Array elements are 0-10 so change current element index's count
        result[current] = count;
      }
      current = array[i];
      count = 1;
    } else {
      count++;
    }
  }
  // For the last element
  if (count > 0) {
    result[current] = count;
  }

  return result;
}

function journalDateSubmit(){
  // Check every input and disable them if its empty
  // This prevents sending empty parameters via GET request

  // Journal month
  if(!$("#journal-month-input").val()){
      $("#journal-month-input").prop('disabled', true);
  }
  // Journal year
  if(!$("#journal-year-input").val()){
      $("#journal-year-input").prop('disabled', true);
  }

  return true;
}