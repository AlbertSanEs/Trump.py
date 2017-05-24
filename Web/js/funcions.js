 function canviType() {
     if ($("#pass").attr("type") == ("text")) {
         $("#pass").removeAttr("type");
         $("#pass").prop("type", "password");
     } else {
         $("#pass").removeAttr("type");
         $("#pass").prop("type", "text");
     }
 }

function canviType2() {
     if ($("#pass2").attr("type") == ("text")) {
         $("#pass2").removeAttr("type");
         $("#pass2").prop("type", "password");
     } else {
         $("#pass2").removeAttr("type");
         $("#pass2").prop("type", "text");
     }
 }