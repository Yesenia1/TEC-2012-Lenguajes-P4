$(document).ready(function(){
  $("#search_query").focus(function(){
    if($(this).val().toLowerCase() == "ej: costa rica"){
      $(this).val("");
    }
  }).blur(function(){
  	if($.trim($(this).val().toLowerCase()) == ""){
      $(this).val("ej: costa rica");
    }
  });
});