
function doValidate() {
    console.log("validating....");
    try {
      // addr = document.getElementById("email").value;
      addr = $("#email").val();
      // pword = document.getElementById("pword").value;
      pword = $("#pword").val();
      console.log("validating addr= " + addr + "pword= " + pword);
      if (addr == null || addr == "" || pword == null || pword == "") {
        alert("Both fields must be filled out");
        return false;
      }
      if (addr.indexOf("@") == -1) {
        alert("Invalid email address");
        return false;
      }
      return true;
    } catch (e) {
      return false;
    }
    return false;
  }
  
  //if countPos is not set from edit.php then assign it as 0.
  countPos = typeof countPos === "undefined" ? 0 : countPos;
  
  //insert position fields upon click of Position:+ button. uses jquery to insert html inside a div
  $(document).ready(function () {
    window.console && console.log("document ready called");
    $("#addPos").click(function (event) {
      event.preventDefault();
      if (countPos >= 9) {
        alert("Maximum of nine position entries exceeded");
        return;
      }
      countPos++;
      $("#position_fields").append(
        '<div id="position' +
          countPos +
          '"> \
          <p>Year: <input type="text" name="year' +
          countPos +
          '" value=""/> \
            <input type="button" value="-" \
             onclick="$(\'#position' +
          countPos +
          '\').remove(); return false;"></p> \
            <textarea name="desc' +
          countPos +
          '" rows="8" cols="80"></textarea>\
        </div>'
      );
    });
  });
  
  countEdu = typeof countEdu === "undefined" ? 0 : countEdu;
  
  $(document).ready(function () {
    window.console && console.log("document ready called");
    $("#addEdu").click(function (event) {
      event.preventDefault();
      if (countEdu >= 9) {
        alert("Maximum of nine education entries exceeded");
        return;
      }
      countEdu++;
      $("#edu_fields").append(
        '<div id="edu' +
          countEdu +
          '"> \
          <p>Year: <input type="text" name="edu_year' +
          countEdu +
          '" value=""/> \
            <input type="button" value="-" \
             onclick="$(\'#edu' +
          countEdu +
          '\').remove(); return false;"></p> \
            <p>School: <input type="text" size="80" name="edu_school' +
          countEdu +
          '" class="school" value=""/></p>\
        </div>'
      );
      $(".school").autocomplete({ source: "school.php" });
    });
    $(".school").autocomplete({ source: "school.php" });
  });