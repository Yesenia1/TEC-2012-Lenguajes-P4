function searchInitialState(){
	$("#search_query").val("ej: costa rica");
	var mark = $("<li />").attr("id","no_results");
	mark.append($("<p />").text("Que buscas?"));
	$("ul#results").html(mark);
	$("ul#results").removeClass("loading");
}

function searchNoResultsState(){
	var mark = $("<li />").attr("id","no_results");
	mark.append($("<p />").text("No se encontraron resultados"));
	$("ul#results").html(mark);
	$("ul#results").removeClass("loading");
}

function createSearchItemResult(counter,obj){
	var mark = $("<li />").addClass("result_item").addClass("item_" + obj.type).attr("id","result_" + counter);
	
	var imgContainer = $("<div />").addClass("result_image");
	var img = $("<img />").attr("src",obj.image).height(80).width(80).attr("alt",obj.title);
	imgContainer.append(img);
	
	var infoContainer = $("<div />").addClass("result_info");
	var itemText = $("<h3 />").addClass("itemText");
	if(obj.item_text.length > 30){
		itemText.text(obj.item_text.substring(0,30) + "...");
	}else{
		itemText.text(obj.item_text);
	}
	var itemSubText = $("<p />").addClass("itemSubText").text("");
	if(typeof(obj.item_subtext) != "undefined"){
		if(typeof(obj.data) != "undefined" && typeof(obj.data.error) == "undefined" && typeof(obj.data.band_data) != "undefined"){
			itemSubText.html('<a href="' + obj.data.band_data.url + '" title="Ver la pagina de ' + obj.item_subtext + '" target="_blank"">' + obj.item_subtext + '</a>');
		}else{
			itemSubText.text(obj.item_subtext);
		}
	}
	var itemUrl = $("<p />").addClass("url").html('<a href="' + obj.href + '" title="Ver ' + obj.item_text + ' en BandCamp" target="_blank">+ informaci&oacuten del <strong>' + obj.type + '</strong></a>');
	var itemTweet = $("<p />").addClass("tweetBtn").html('<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-url="' + obj.href + '" data-via="TweetMyBand" data-text="Musica de la buena :)">Tweet</a>');
	infoContainer.append(itemText).append(itemSubText).append(itemUrl).append(itemTweet);
	
	var actionContainer = $("<div />").addClass("result_action");
	var itemMoney = $("<p />").addClass("value").text("");
	if(typeof(obj.data) != "undefined" && typeof(obj.data.error) == "undefined" && 
	  (typeof(obj.data.album_data) != "undefined" || typeof(obj.data.track_data) != "undefined")){
	  	var downloadable = (typeof(obj.data.album_data) == "undefined") ? obj.data.track_data.downloadable :  obj.data.album_data.downloadable;
	  	if(downloadable >= 2){
	  		itemMoney.html('<a href="' + obj.href + '?action=buy" title="Comprar ' + obj.title + '" target="_blank">Comprar</a>');
	  	}else{
	  		itemMoney.html('<a href="' + obj.href + '" title="Bajar ' + obj.title + '" target="_blank">Bajar GRATIS</a>');
	  	}
	}
	
	actionContainer.append(itemMoney);
	
	mark.append(imgContainer).append(infoContainer).append(actionContainer).hide();
	
	return mark;
}

$(document).ready(function(){
	
  $("#search_query").focus(function(){
    if($(this).val().toLowerCase() == "ej: costa rica"){
      $(this).val("");
    }
  }).blur(function(){
  	if($.trim($(this).val().toLowerCase()) == ""){
      searchInitialState();
    }
  });
  searchInitialState();
  
  $("#searchForm").on("submit",function(e){
  	e.preventDefault();
  	var searchValue = $("#search_query").val();
  	$.ajax({
  		url: "http://api.andreybolanos.com/bandcamp/",
  		data: {"m":"tag","f":"search","name":searchValue,"incAlbumData":1,"incTrackData":1,"incBandData":1,"key":"veidihundr"},
  		crossDomain: true,
  		type: "GET",
  		dataType: "jsonp",
  		cache: true,	
  		jsonpCallback: "search",
  		beforeSend: function(jqXHR,settings){
  			// console.log(settings);
  			$("ul#results").addClass("loading");
  		},
  		error: function(jqXHR, textStatus, errorThrown){
  			console.log("AJAX_ERROR > " + textStatus + "|" + errorThrown)
  		},
  		success: function(data,textStatus,jqXHR){
  			if(data.error || data.count == 0){
  				searchNoResultsState();
  			}else{
  				var mark = null;
  				$("ul#results").html("");
  				$("ul#results").removeClass("loading");
  				for(i=0;i<data.count;i++){
  					mark = createSearchItemResult(i,data.results[i]);
  					$("ul#results").append(mark);
  					mark.fadeIn();
  					mark = null;
  				}
  				twttr.widgets.load();
  			}
  		}
  	});
  });
});