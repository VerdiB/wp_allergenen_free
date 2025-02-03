var button = document.getElementById("ictoria-filter-dropdown-button");
var quickEdit = document.querySelectorAll(".quick_edit");

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

function quickedit(form) {
  var field = form.querySelector(".update_form");
  var selectdropdown = field.querySelector(".type");
  var allselectdropdownoptions = selectdropdown.querySelectorAll(".option");
  var allinputs = field.querySelectorAll(".update_");
  var counter = 0;
  var thelist = document.getElementById("the-list");
  var tablerow = field.parentElement.parentElement.parentElement;
  var allergy_name = tablerow.querySelector(".allergy_name");
  var togglerow = allergy_name.querySelector(".toggle-row");
  var allergy_name_text = allergy_name.querySelector(".allergen_name");
  var allergy_description = tablerow.querySelector(".allergy_description");
  var is_allergy = tablerow.querySelector(".is_allergy");
  var is_active = tablerow.querySelector(".is_active");

  if (document.getElementsByTagName("body")[0].classList.contains("mobile")){
    is_allergy.classList.add("hidden");
    allergy_description.classList.add("hidden");
    is_active.classList.add("hidden");
  }

  if (form.style.display == "none") {
    if (thelist) {
      field.parentElement.parentElement.parentElement.classList.add("inline-edit-row");
      var trs = thelist.querySelectorAll("tr");

      trs.forEach((tr) => {
        var closeforms = tr.querySelector(".allergens_table_form");

        if (closeforms) {
          closeforms.style.display = "none";
          var closefield = closeforms.querySelector(".update_form");
          var closeselectdropdown = closefield.querySelector(".type");
          var closeallselectdropdownoptions =
            closeselectdropdown.querySelectorAll(".option");
          var closeallinputs = closefield.querySelectorAll(".update_");
          var closetablerow = closefield.parentElement.parentElement.parentElement;
          var closeallergy_name = closetablerow.querySelector(".allergy_name");
          var closeallergy_name_text = closeallergy_name.querySelector(".allergen_name");
          var closeallergy_description = closetablerow.querySelector(".allergy_description");
          var closeis_allergy = closetablerow.querySelector(".is_allergy");
          var closeis_active = closetablerow.querySelector(".is_active");

          if (closeforms.style.display == "none") {
            closeallinputs.forEach((closeinput) => {
              closeinput.disabled = true;
              if (!document.getElementsByTagName("body")[0].classList.contains("mobile")){
                closeallergy_name.colSpan = 1;
                closeallergy_description.style.display = "table-cell";
                closeis_allergy.style.display = "table-cell";
                closeis_active.style.display = "table-cell";
                closeallergy_name_text.style.display = "block";
              }else{
                if (document.getElementsByTagName("body")[0].classList.contains("mobile")){
                  is_allergy.classList.add("hidden");
                  allergy_description.classList.add("hidden");
                  is_active.classList.add("hidden");
                  togglerow.style.display = "none";
                }
              }
            });

            closeallselectdropdownoptions.forEach((closeoption) => {
              closeoption.disabled = true;
            });
          }
        }
      });
    }

    if (counter > 0) {
      closeforms.forEach((form) => {
        form.style.display = false;
      });
    }

    allinputs.forEach((input) => {
      input.disabled = false;
    });

    allselectdropdownoptions.forEach((option) => {
      option.disabled = false;
    });

  if (!document.getElementsByTagName("body")[0].classList.contains("mobile")){
    allergy_name.colSpan = 3;
    allergy_description.style.display = "none";
    is_allergy.style.display = "none";
    is_active.style.display = "table-cell";
    allergy_name_text.style.display = "none";
  }
  form.style.display = "block";
  } else {
    field.parentElement.parentElement.parentElement.classList.remove("inline-edit-row");
    if (!document.getElementsByTagName("body")[0].classList.contains("mobile")){
      allergy_name.colSpan = 1;
      allergy_description.style.display = "table-cell";
      is_allergy.style.display = "table-cell";
      is_active.style.display = "table-cell";
      allergy_name_text.style.display = "block";
    }else{
      if (document.getElementsByTagName("body")[0].classList.contains("mobile")){
        is_allergy.classList.remove("hidden");
        allergy_description.classList.remove("hidden");
        is_active.classList.remove("hidden");
        togglerow.style.display = "block";
      }
    }

    allinputs.forEach((input) => {
      input.disabled = true;
    });

    allselectdropdownoptions.forEach((option) => {
      option.disabled = true;
    });

    form.style.display = "none";
  }
}

if (checkElementExists("the-list")) {
  document
    .getElementById("the-list")
    .addEventListener("click", function (event) {
      if (event.target.classList.contains("quick_edit")) {
        var quickEditing = event.target;
        if (quickEditing.style.pointerEvents == "auto") {
          var form = event.target.id + "_form";
          var get_form = document.getElementById(form);
          quickedit(get_form);
        }
      }
    });
}

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
        $(imgElement).attr("src", e.target.result);
      };

      reader.readAsDataURL(input.files[0]);
    }
  }

  $(document).on("change", ".allergen_icon_file_input", function () {
    // Identify the closest '.item' container to get the corresponding image
    const itemContainer = $(this).closest(".item"); // Adjust to match the row/container class
    const imgElement = itemContainer.find(".allergen_icon_img");

    if (imgElement.length === 0) {
      return;
    }
    readURL(this, imgElement);
  });

  $(document).on("change", ".allergen_icon_file_input", function () {
    console.log($(this));
    const itemRow = $(this).closest('.item-row');
    const itemHeader = itemRow.find('.item-header');
    const addImgElement = itemHeader.find('.add_allergen_icon_img');

    if (addImgElement.length === 0) {
      return;
    }
    readURL(this, addImgElement);
  });
});

var formTouched = false;

/*Detects if an input in the form is being edited*/
if (checkElementExists("show_allergens_form") == true) {
document.getElementById('show_allergens_form').addEventListener('input', () => {
  formTouched = true;
});
}
if (checkElementExists("add_allergens_form") == true) {
document.getElementById('add_allergens_form').addEventListener('input', () => {
  formTouched = true;
});
}

/*Gives a notification if the form is edited without being submitted*/
window.addEventListener('beforeunload', (event) => {
  if (formTouched) {
    event.preventDefault();
    event.returnValue = '';
  }
});

/*This is to avoid that the notification is shown when the form was already submitted*/
if (checkElementExists("show_allergens_form") == true) {
document.getElementById('show_allergens_form').addEventListener('submit', () => {
  formTouched = false;
});
}
if (checkElementExists("add_allergens_form") == true) {
document.getElementById('add_allergens_form').addEventListener('submit', () => {
  formTouched = false;
});
}
