<?php

	# api.andreybolanos.com/bandcamp/tag/search?name=<tag>

	if(isset($_GET["key"]) && !empty($_GET["key"])){
		define("BANDCAMP_DEV_KEY",$_GET["key"]);
	}else{
		/* Valid key: veidihundr */
		define("BANDCAMP_DEV_KEY",$_GET["key"]);
	}

	$response = array();
	$response["version"] = 1.2;
	$response["error"] = true;

	if(isset($_GET["m"]) && !empty($_GET["m"])){
		if(isset($_GET["f"]) && !empty($_GET["f"])){
			$response["error"] = false;
			$response ["results"] = array();
			$response ["count"] = 0;
		}else{
			$response["error_message"] = "Modulo " . strtoupper($_GET["m"]) . " :: Funcion no especificada";
		}
	}else{
		$response["error_message"] = "Modulo no especificado";
	}
	
	if(isset($response ["results"])){
		switch($_GET["m"]){
			case "tag":
				switch($_GET["f"]){
					case "search":
						$_GET["name"] = trim(urldecode($_GET["name"]));
						if(isset($_GET["name"]) && !empty($_GET["name"])){
							$q = 'http://bandcamp.com/tag/' . str_replace(" ","-",$_GET["name"]);
							$getsCounter = 0;
							$response["source"] = $q;
							$dom = new DOMDocument();  
   							$html = @$dom->loadHTMLFile($q);
							$getsCounter++;
							if(empty($html)){
								$response["error"] = true;
								$response["error_message"] = "Modulo TAG :: Funcion SEARCH :: Error al cargar los datos de BandCamp";
							}else{
								$response["tag"] = $_GET["name"];
								$dom->preserveWhiteSpace = false;
								$ul = $dom->getElementsByTagName("ul");
								$items_list = NULL;
								for($i = 0; $i < $ul->length; $i++){
									if($ul->item($i)->getAttribute('class') == "item_list" && $ul->item($i)->parentNode->getAttribute("class") == "results"){
										// podria darsele soporte a resutlados featured
										$items_list = $ul->item($i);
										break;
									}
								}
								if(is_null($items_list)){
									$response["error"] = true;
									$response["error_message"] = "Modulo TAG :: Funcion SEARCH :: Error al cargar la lista de BandCamp";
								}else{
									$items = $items_list->getElementsByTagName("li");
									$itemsData = array();
									$itemCount = 0;
									if(isset($_GET['limit']) && !is_nan($_GET['limit'])){
										$itemCount = $_GET['limit'];
									}else{
										$itemCount = ($items->length > 10) ? 10 : $items->length;
									}
									$response["count"] = (int)$itemCount;
									for($i = 0; $i < $itemCount; $i++){
										$link = array();
										$refElement = $items->item($i)->getElementsByTagName("a")->item(0);
										$link["href"] = $refElement->getAttribute("href");
										if(strpos($link["href"],"/album/") !== FALSE){
											$link["type"] = "album";
											$link["title"] = $refElement->getAttribute("title");
											$link["image"] = $refElement->getElementsByTagName("img")->item(0)->getAttribute("src");
										}
										if(strpos($link["href"],"/track/") !== FALSE){
											$link["type"] = "track";
											$link["title"] = $refElement->getAttribute("title");
											$link["image"] = $refElement->getElementsByTagName("img")->item(0)->getAttribute("src");
										}
										$otherData = $refElement->getElementsByTagName("div");
										for($j = 0; $j < $otherData->length; $j++){
											if($otherData->item($j)->getAttribute('class') == "itemtext"){
												$link["item_text"] = $otherData->item($j)->nodeValue;
											}
											if($otherData->item($j)->getAttribute('class') == "itemsubtext"){
												$link["item_subtext"] = $otherData->item($j)->nodeValue;
											}
										}
										if(isset($_GET['incBandData']) || isset($_GET['incAlbumData']) || isset($_GET['incTrackData'])){
											$url = "http://api.bandcamp.com/api/url/1/info?key=" . BANDCAMP_DEV_KEY . "&url=" . $link["href"];
											$link["data"] = json_decode(file_get_contents($url));
											$getsCounter++;
										}
										if(isset($_GET['incBandData']) && (bool)$_GET['incBandData']){
											if(isset($link["data"]->band_id)){
												$url = "http://api.bandcamp.com/api/band/3/info?key=" . BANDCAMP_DEV_KEY . "&band_id=" . $link["data"]->band_id;
												$link["data"]->band_data = json_decode(file_get_contents($url));
												$getsCounter++;
											}
										}
										if(isset($_GET['incAlbumData']) && (bool)$_GET['incAlbumData']){
											if(isset($link["data"]->album_id)){
												$url = "http://api.bandcamp.com/api/album/2/info?key=" . BANDCAMP_DEV_KEY . "&album_id=" . $link["data"]->album_id;
												$link["data"]->album_data = json_decode(file_get_contents($url));
												$getsCounter++;
											}
										}
										if(isset($_GET['incTrackData']) && (bool)$_GET['incTrackData']){
											if(isset($link["data"]->track_id)){
												$url = "http://api.bandcamp.com/api/track/3/info?key=" . BANDCAMP_DEV_KEY . "&track_id=" . $link["data"]->track_id;
												$link["data"]->track_data = json_decode(file_get_contents($url));
												$getsCounter++;
											}
										}
										array_push($response["results"],$link);
										unset($link,$refElement,$otherData);
									}
								}
							}
							$response["calls"] = $getsCounter;
						}else{
							$response["error"] = true;
							$response["error_message"] = "Modulo TAG :: Funcion SEARCH :: Tag no especificado (NAME)";
						}
					break;
					default:
						$response["error"] = true;
						$response["error_message"] = "Modulo TAG :: Funcion " . strtoupper($_GET["f"]) . " desconocida";
					break;
				}
			break;
			default:
				$response["error"] = true;
				$response["error_message"] = "Modulo " . strtoupper($_GET["m"]) . " desconocido";
			break;
		}
	}
	
	if($response["error"] && isset($response["results"])){
		unset($response["results"]);
		unset($response["count"]);
	}

	header('Cache-Control: no-cache, must-revalidate');
	header('Content-type: application/json; charset=utf-8');
	
	if(isset($_GET['callback']) && !empty($_GET['callback'])){
		echo $_GET['callback'] . "(" . json_encode($response) . ");";
	}else{
		echo json_encode($response);
	}

?>