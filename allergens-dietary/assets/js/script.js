console.log('asdasd')
var button = document.getElementById("ictoria-filter-dropdown-button");

function checkElementExists(id) {
  var element = document.getElementById(id);
  if (element) {
    return true;
  }else{
    return false;
  }
}

window.addEventListener("load", function () {
  const url = new URL(window.location.href);
  if (url.searchParams.has("messaged")) {
      url.searchParams.delete("messaged");
      window.history.pushState({}, document.title, url);
  }
});

function confirmResetInput() {
  if (confirm("Are you sure you want to reset the form?")) {
    location.reload(true);
  }
  return;
}

jQuery(document).ready(function ($) {
  if (checkElementExists("ictoria-filter-dropdown")) {
    $(document).on("click", "#ictoria-filter-dropdown-button", dropdown_form);
  }
});

  function dropdown_form() {
    if (
      document.getElementById("ictoria-filter-dropdown").style.display == "none"
    ) {
      document.getElementById("ictoria-filter-dropdown").style.display =
        "block";
    } else {
      document.getElementById("ictoria-filter-dropdown").style.display = "none";
    }
  }

  function readURL(input, imgElement) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        imgElement.attr("src", e.target.result);
      };

      reader.readAsDataURL(input.files[0]);
    }
  }

