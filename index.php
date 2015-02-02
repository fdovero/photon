<?php
# *** LICENSE ***
# Photon Photo Gallery V.0.1
# 2015- Timo Van Neerden http://lehollandaisvolant.net/contact

# Photon is free software, under the WTFPL licence:
# - 0. Just do what you the fuck you want to.
# - 1. The author can’t be hold for responsible for any form of harm this script may provoque.

error_reporting(-1);

$GLOBALS['main_media_dir'] = 'img';
$GLOBALS['request_folder'] = isset($_GET['fol']) ? htmlspecialchars($_GET['fol']) : '';
$GLOBALS['start_list_count'] = (isset($_GET['page']) and (is_numeric($_GET['page']) or $_GET['page'] == 'all')) ? $_GET['page'] : '0';
$GLOBALS['image_per_page'] = 50;

?>

<!DOCTYPE html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=yes" />
<title>Photon, photo gallery</title>
<style type="text/css">
html {
	background: #212121;
	color: #eee;
	font-size: 62.5%;
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
}
body { 
	font-size: 14px;
	width: 80%;
	margin: 0 auto;
}

a {
	text-decoration: none;
	color: inherit;
}
a:hover {
	text-decoration: underline;
}

header h1 {
	font-weight: normal;
	font-size: 1.7em;
	text-shadow: 2px 2px 2px #000;
}

#axe {
	background: linear-gradient(to right, #212121, #212121) 0 2px no-repeat, linear-gradient(to right, #646464, #212121 ) 0 1px no-repeat, linear-gradient(to right, #010101, #212121 ) 0 0px no-repeat;
	padding-top: 1px;
}


#list-folders {
	margin: 0 auto;
	color: #eee
}
#list-folders a {
	color: white;
}

/* Liste des images : sous la forme d’un mur d’images */
#list-images {
	text-align: left;
	padding: 20px 0;
}

#list-images .image_bloc {
	text-align: center;
	background: white;
	display:inline-block;
	margin: 1px;
	line-height: 160px;
	height: 160px;
	width: 160px;
	border: 1px solid #666;
	border-radius: 3px;
	border-color: black gray;
	padding: 5px;
	position: relative;
}

#list-images .image_bloc .spantop {
	overflow: hidden;
	opacity: 0;
	text-shadow: 0 0 3px white, 0 0 3px white;
	left: 0; /* counters the padding */
	position: absolute;
	background: rgba(255,255,255,.8);
	width: 100%;
	height: 0;
	word-wrap: break-word;
	top: 0;
	border-radius: 3px;
	-webkit-transition: ALL .15s ease-out;
	   -moz-transition: ALL .15s ease-out;
	    -ms-transition: ALL .15s ease-out;
	     -o-transition: ALL .15s ease-out;
	        transition: ALL .15s ease-out;
}

#list-images .image_bloc .spantop .bouton {
	line-height: 32px;
	height: 32px; width: 32px;
	vertical-align: middle;
	display: inline-block;
	cursor: pointer;
	text-decoration: none;
}

#list-images .image_bloc .spantop .bouton-lien {
	background: url('slide.png') no-repeat 0 -224px;
}

#list-images .image_bloc .spantop .bouton-lien[download] {
	background: url('slide.png') no-repeat 0 -192px;
}

#list-images .image_bloc .spantop .bouton-slide {
	background: url('slide.png') no-repeat;
}

#list-images .image_bloc:hover .spantop {
	opacity: 1;
	line-height: 160px; height: 100%;
}

#list-images .image_bloc img {
	border: 1px solid gray;
	vertical-align: middle;
	max-width: 160px;
	max-height: 160px;
}


/* JS slideshow */

#slider {
	display: none;
	background: black;
	position: fixed;
	top: 0; left: 0; right: 0; bottom: 0;
	z-index: 9;
}

#slider-box {
	border: none;
	position: absolute;
	top: 0px; right: 0px; left: 0px; bottom: 0px;
	text-align: left;
}

#slider-box-cnt {
	position: absolute;
	top: 0; right: 0; bottom: 48px; left: 0;
}

#slider-box-img-bg,
#slider-box-img-bg-blur-fallback {
	background-color: black;
	width: 100%;
	height: 100%;
	position: absolute;
}

#slider-box-img-bg-blur {
	background-size: 100% 100%;
	width: 100%;
	height: 100%;
	filter: blur(25px) opacity(.2);
	-webkit-filter: blur(25px) opacity(.2);
	-ms-filter: blur(25px) opacity(.2);
}

#slider-box-img-bg-blur-fallback {
	background: black;
	filter: opacity(0);
	-webkit-filter: opacity(0);
	-ms-filter: opacity(0);
}

#slider-box-img-wrap {
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
}

#slider-img {
	max-width: 100%;
	max-height: 100%;
	margin: auto;
	z-index: 1;
}

#slider-img-a {
	display:block;
	position: absolute;
	top:0; bottom:0; left:0; right:0;
	z-index: 2;
}

#slider-box-bottom {
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	height: 48px;
	bottom: 0; left: 10px; right: 10px;
	position: absolute;
	display: flex;
	align-items: center;
}

#slider-img-info {
	flex: 1 1 0%;
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	height: 48px;
	line-height: 48px;
}

#slider-buttons {
	flex: 1 1 0%;
	line-height: 48px;
	text-align: right;
}

#slider-buttons a,
#slider-box .slider-quit {
	background: url(slide.png) no-repeat;
	display: inline-block;
	height: 32px; width: 32px;
	vertical-align: middle;
	position: relative;
	box-shadow: 0 0 5px white;
	border-radius: 4px;

}

#slider-buttons a:active {
	top: 1px;
}

#slider-buttons .slider-first {
	background-position: 0 -96px;
}
#slider-buttons .slider-prev {
	background-position: 0 -32px;
}
#slider-buttons .slider-next {
	background-position: 0 -160px;
}
#slider-buttons .slider-last {
	background-position: 0 -128px;
}
#slider-box .slider-quit {
	background-position: 0 -64px;
	position: absolute;
	top: 10px; right: 10px;
	z-index: 99;
}

#pager {
	text-align: center;
	padding: 20px;
}

#pager a, #pager span {
	color: #eee;
	display: inline-block;
	padding: 5px;
	vertical-align: center;
}

#pager a {
	text-shadow: 0px 0px 3px gray;
}

footer {
	font-size: .80em;
	color: silver;
	text-align: center;
	margin: 20px 0 20px;
	padding-top: 20px;

	background: linear-gradient(to right, #212121, #212121) 0 2px no-repeat, linear-gradient(to right, #212121, #646464, #212121 ) 0 1px no-repeat, linear-gradient(to right, #212121, #010101, #212121 ) 0 0px no-repeat;
}


</style>
</head>

<body id="body">

<header>
	<h1><a href="?">Photon, photo gallery</a></h1>
</header>

<div id="axe">

<?php

$fichiers = array();

// remove the folders "." and ".." from the list of files returned by "scandir".
function rm_dots_dir($array) {
	if (($key = array_search('..', $array)) !== FALSE) { unset($array[$key]); }
	if (($key = array_search('.', $array)) !== FALSE) { unset($array[$key]); }
	return ($array);
}

// detects if folder contains ".private" file; where $dir = scandir('dir/').
function is_private_dir($dir) {
	if ((array_search('.private', $dir)) !== FALSE) {
		return TRUE;
	}
	return FALSE;
}


// no dir specified in URL, list all dirs and files
if (empty($GLOBALS['request_folder'])) {

	// List all files that are on disk ; FIXME: maybe create a plain-text-DB with file list to enhance speed?
	if (!is_dir($GLOBALS['main_media_dir'])) mkdir($GLOBALS['main_media_dir']);
	$main_dir = rm_dots_dir(scandir($GLOBALS['main_media_dir']));

	foreach ($main_dir as $i => $collection_dir) {
		if (is_dir($GLOBALS['main_media_dir'].DIRECTORY_SEPARATOR.$collection_dir)) {
			// ignore dir if it contains ".private" file.
			if (!is_private_dir($dir = scandir($GLOBALS['main_media_dir'].'/'.$collection_dir)) ) {
				$fichiers[$collection_dir] = rm_dots_dir($dir);
			}
		}
	}

	// echos the list of folders
	echo '<div id="list-folders">'."\n";
	echo '<p id="media-path">Liste des dossiers publics&nbsp;:</p>';
	echo '<ul>'."\n";
	foreach ($fichiers as $i => $dossier) {
		echo "\t".'<li><a href="?fol='.urlencode($i).'">'.$i.'</a></li>'."\n";
	}
	echo '</ul>'."\n";
	echo '</div>'."\n";

}

else {
	// a dir is specified in URL.
	$sub_dir = $GLOBALS['main_media_dir'].'/'.$GLOBALS['request_folder'];

	// avoid requests of type  "../../../../dir", that might scan system-dirs.
	// compares stings of realpath(main_dir/requested) and realpath(main_dir)/requested.
	if (realpath($sub_dir) !== realpath($GLOBALS['main_media_dir']).DIRECTORY_SEPARATOR.$GLOBALS['request_folder']) {
		echo '<p id="media-path">Path forbidden.</p>'."\n";
		die;
	}
	// else, path is good: go on.
	else {
		echo '<p id="media-path"><a href="?">home</a> &gt; '.$GLOBALS['request_folder'].'</p>'."\n";
	}

	// Tests if dir exists and scans it.
	if (is_dir($sub_dir)) {
		// verify if it’s private
		if (is_private_dir($sub_dir_contains = scandir($sub_dir)) ) {
			// private: remove ".private" file
			$key = array_search('.private', $sub_dir_contains);
			unset($sub_dir_contains[$key]);
		}

		$img_list = rm_dots_dir($sub_dir_contains);
	
		// show images
		echo '<div id="list-images">';
		if (!empty($img_list)) {
			$collection_count = count($img_list);
			if (is_numeric($GLOBALS['start_list_count'])) {
				$img_list = array_slice($img_list, $GLOBALS['start_list_count']*$GLOBALS['image_per_page'], $GLOBALS['image_per_page'], false);
			}

			foreach ($img_list as $i => $image) {
				echo '<div id="bloc_'.$i.'" data-img-url="'.$sub_dir.'/'.$image.'" data-img-name="'.$image.'" class="image_bloc">
					<span class="spantop black">
						<span title="Ouvrir Slideshow" class="bouton bouton-slide" onclick="slideshow(\'start\', '.$i.');"></span>
						<a title="Voir" class="bouton bouton-lien" href="'.$sub_dir.'/'.$image.'">&nbsp;</a>
						<a title="Télécharger" class="bouton bouton-lien" download href="'.$sub_dir.'/'.$image.'">&nbsp;</a>
					</span>
					<img id="img_'.$i.'" src="'.$sub_dir.'/'.$image.'" alt="'.htmlspecialchars($image).'">
				</div>'."\n";
			}
			echo '</div>';
			$nb_pages = ceil($collection_count / $GLOBALS['image_per_page']) -1;
			echo '<div id="pager">Page ';
			for ($i = 0; $i <= $nb_pages; $i++) {
				if ((string)$i == $GLOBALS['start_list_count']) {

					echo '<span>'.$i.'</span>|';
				}
				else {
					echo '<a href="?fol='.$GLOBALS['request_folder'].'&amp;page='.$i.'">'.$i.'</a>|';
				}
			}
			if ($GLOBALS['start_list_count'] == 'all') {
				echo '<span>Tout</span>';
			} else {	
				echo '<a href="?fol='.$GLOBALS['request_folder'].'&amp;page=all">Tout</a>';
			}
			echo '</div>';

		}
		else {
			echo 'no images.';
			echo '</div>';
		}

	}
	else {
		echo "no such dir"; die;
	}

?>


<div id="slider">
	<div id="slider-box">
		<div id="slider-box-cnt">
			<div id="slider-box-img-bg">
				<div id="slider-box-img-bg-blur-fallback"></div>
				<div id="slider-box-img-bg-blur"></div>
			</div>
			<div id="slider-box-img-wrap"><a id="slider-img-a" href="#"></a><img id="slider-img" src="image-loading.png" alt="#"/></div>
			<a href="#" onclick="slideshow('close'); return false;" class="slider-quit"></a>
		</div>
		<div id="slider-box-bottom">
			<p id="slider-img-info"></p>
			<p id="slider-buttons">
				<a href="#" onclick="slideshow('first'); return false;" class="slider-first"></a>
				<a href="#" onclick="slideshow('prev'); return false;" class="slider-prev" id="slider-prev"></a>
				<a href="#" onclick="slideshow('next'); return false;" class="slider-next" id="slider-next"></a>
				<a href="#" onclick="slideshow('last'); return false;" class="slider-last"></a>
			</p>
		</div>
	</div>
</div>

<script type="text/javascript">
var curr_max = <?php echo count($img_list)-1; ?>;
var counter = 0;
document.onkeydown = checkKey;

function slideshow(action, imageIndex) {
	
	switch(action) {
		case 'start' : document.getElementById('slider').style.display = 'block'; counter = imageIndex; break;
		case 'first' : counter = 0; break;
		case 'prev'  : counter = Math.max(--counter, 0); break;
		case 'next'  : counter = Math.min(++counter, curr_max); break;
		case 'last'  : counter = curr_max; break;
		case 'close' : document.getElementById('slider').style.display = 'none'; break;
		default      : console.log('action not supported');
    }

	var ElemImg = document.getElementById('slider-img');

	var newImg = new Image();
	
	var box_height = document.getElementById('slider-box-img-wrap').clientHeight;
	var box_width = document.getElementById('slider-box-img-wrap').clientWidth;
	var img_height = document.getElementById('img_'+counter).naturalHeight;
	var img_width = document.getElementById('img_'+counter).naturalWidth;
	var ratio_w = Math.max(1, img_width/box_width);

	newImg.onload = function() {
		ElemImg.src = newImg.src;
		document.getElementById('slider-img-a').href = newImg.src;
	};

	newImg.onerror = function() {
		ElemImg.src = '';
		ElemImg.alt = 'Error Loading File';
		document.getElementById('slider-img-a').href = '#';
		ElemImg.style.marginTop = '0';
	};
	newImg.src = document.getElementById('bloc_'+counter).dataset.imgUrl;
	var imgName = document.getElementById('bloc_'+counter).dataset.imgName;
	document.getElementById('slider-img-info').innerHTML = '('+(counter+1)+'/'+(curr_max+1)+')'+' <b>'+imgName+'</b>';
	document.getElementById('slider-box-img-bg-blur').style.backgroundImage = 'url('+newImg.src+')';
}

function checkKey(e) {
	if (document.getElementById('slider').style.display != 'block') return true;

	e = e || window.event;

	var evt = document.createEvent("MouseEvents"); // créer un évennement souris
	evt.initMouseEvent("click", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);

	if (e.keyCode == '37') {
		// left
		var button = document.getElementById('slider-prev');
		button.dispatchEvent(evt);
		e.preventDefault();
	}
	else if (e.keyCode == '39') {
		// right
		var button = document.getElementById('slider-next');
		button.dispatchEvent(evt);
		e.preventDefault();
	}


}

</script>

<?php 
}
?>

</div> <!-- end #axe -->

<footer>Made with Photon, <a href="http://lehollandaisvolant.net/">Timo's</a> Gallery.</footer>

</body>
</html>
