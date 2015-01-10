<?php
# *** LICENSE ***
# Photon V.1
# 2015- Timo Van Neerden http://lehollandaisvolant.net/contact

# Photon is free software, under the WTFPL licence:
# - 0. Just do what you the fuck you want to.
# - 1. The author can’t be hold for responsible for any form of harm this script may provoque.

error_reporting(-1);

$GLOBALS['main_media_dir'] = 'img';
$GLOBALS['request_folder'] = isset($_GET['fol']) ? htmlspecialchars($_GET['fol']) : '';
$GLOBALS['start_list_count'] = isset($_GET['page']) ? (is_numeric($_GET['page']) ? $_GET['page'] : 0) : 0;
$GLOBALS['image_per_page'] = 50;

?>

<!DOCTYPE html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=yes" />
<title>Photon, portfolio</title>
<style type="text/css">
body {
	background: #212121;
	background: linear-gradient(#212121 500px, #111);
	color: #eee;
	font-family: sans-serif;
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
	text-align: center;
	padding: 20px 2%;
	border-radius: 20px;
}

#list-images .image_bloc {
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
	color: black;
	background: rgba(255,255,255,.8);
	width: 100%;
	height: 0;
	word-wrap: break-word;
	top: 0;
	border-radius: 3px 3px 0 0;
	border-bottom: #888 1px solid;
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
	background: url('slide.png') no-repeat 0 -192px;
}

#list-images .image_bloc .spantop .bouton-slide {
	background: url('slide.png') no-repeat;
}


#list-images .image_bloc:hover .spantop {
	opacity: 1;
}

#list-images .image_bloc:hover .spantop {
	opacity: 1;
	-webkit-transition: ALL .15s ease-out;
	   -moz-transition: ALL .15s ease-out;
	    -ms-transition: ALL .15s ease-out;
	     -o-transition: ALL .15s ease-out;
	        transition: ALL .15s ease-out;
	line-height: 35px; height: 35px;
	top: 0px;
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
	background: rgba(0, 0, 0, .8);
	position: fixed;
	top: 0; left: 0; right: 0; bottom: 0;
	z-index: 9;
}

#slider-box {
	box-shadow: 8px 8px 12px black;
	border: 7px solid rgba(70, 70, 70, 1);
	border-radius: 8px;
	background: rgba(0, 0, 0, .8);
	position: absolute;
	top: 20px; right: 20px; left: 20px; bottom: 20px;
	text-align: left;
}

#slider-box-cnt {
	position: absolute;
	top: 0; right: 0; bottom: 48px; left: 0;
}

#slider-box-img-wrap {
	width: 100%;
	height: 100%;
	display: inline-block;
	position: absolute;
	text-align: center;
}

#slider-img {
	max-width: 100%;
	max-height: 100%;
	width: auto;
	height: auto;
	vertical-align: middle;
}

#slider-img-a {
	display:block;
	position: absolute;
	top:0; bottom:0; left:0px; right:50px;
}

#slider-box-inf {
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	height: 48px;
	bottom: 0; left: 0; right: 0;
	border-top: 2px solid #464646;
	position: absolute;
}

#slider-box-inf a, #slider-box .slider-quit {
	background: url(slide.png) no-repeat;
	display: inline-block;
	height: 32px; width: 32px;
	vertical-align: middle;
	position: relative;
}

#slider-box-inf a:active, #slider-box .slider-quit:active {
	top: 1px;
}

#slider-box-inf .slider-first {
	background-position: 0 -96px;
}
#slider-box-inf .slider-prev {
	background-position: 0 -32px;
}
#slider-box-inf .slider-next {
	background-position: 0 -160px;
}
#slider-box-inf .slider-last {
	background-position: 0 -128px;
}
#slider-box .slider-quit {
	background-position: 0 -64px;
	position: absolute;
	top: 0; right: 0;
	z-index: 99;
}

.slider-buttons {
	margin: 0;
	line-height: 48px;
	text-align: center;
}

#pager {
	text-align: center;
	padding: 20px;
}

#pager a, #pager span {
	text-decoration: none;
	color: white;
	display: inline-block;
	width: 30px;
	height: 30px;
	line-height: 30px;
}

#pager a {
	font-weight: bold;
}
</style>
</head>

<body id="body">

<div id="top">
<h1>Photon, Portfolio</h1>
</div>

<div id="axe">

<?php

$fichiers = array();

// remove the folders "." and ".." from the list of files returned by "scandir".
// $array must be previously sorted in order to get "." and ".." in the top of the array.
function rm_dots_dir($array) {
	array_shift($array);
	array_shift($array);
	return ($array);
}


// no dir specified in URL, list all dirs and files
if (empty($GLOBALS['request_folder'])) {

	// List all files that are on disk ; FIXME: maybe create a plain-text-DB with file list to enhance speed?
	if (!is_dir($GLOBALS['main_media_dir'])) mkdir($GLOBALS['main_media_dir']);
	$main_dir = rm_dots_dir(scandir($GLOBALS['main_media_dir']));

	foreach ($main_dir as $i => $collection_dir) {
		if (is_dir($GLOBALS['main_media_dir'].'/'.$collection_dir)) {
			$fichiers[$collection_dir] = rm_dots_dir(scandir($GLOBALS['main_media_dir'].'/'.$collection_dir));
		}
	}

	// echos the list of folders
	echo '<div id="list-folders">';
	echo 'Liste des dossiers publics&nbsp;:';
	echo '<ul>';
	foreach ($fichiers as $i => $dossier) {
		echo '<li><a href="?fol='.urlencode($i).'">'.$i.'</a></li>';
	}
	echo '</ul>';
	echo '</div>';

}

else {
	// a dir is specified in URL.
	$sub_dir = $GLOBALS['main_media_dir'].'/'.$GLOBALS['request_folder'];

	// avoid requests of type  "../../../../dir", that might scan system-dirs.
	// compares stings of realpath(main_dir/requested) and realpath(main_dir)/requested.
	if (realpath($sub_dir) !== realpath($GLOBALS['main_media_dir']).'/'.$GLOBALS['request_folder']) {
		echo 'forbidden';
		die;
	}

	// Tests if dir exists and scans it.
	if (is_dir($sub_dir)) {
		$img_list = rm_dots_dir(scandir($sub_dir));
	
		// show images
		echo '<div id="list-images">';
		if (!empty($img_list)) {
			$collection_count = count($img_list);
			$img_list = array_slice($img_list, $GLOBALS['start_list_count']*$GLOBALS['image_per_page'], $GLOBALS['image_per_page'], false);

			foreach ($img_list as $i => $image) {
				echo '<div id="bloc_'.$i.'" data-img-url="'.$sub_dir.'/'.$image.'" class="image_bloc">
					<span class="spantop black">
						<span title="Ouvrir Slideshow" class="bouton bouton-slide" onclick="slideshow(\'start\', '.$i.');"></span>
						<a title="Voir" class="bouton bouton-lien" href="'.$sub_dir.'/'.$image.'">&nbsp;</a>
					</span>
					<img id="img_'.$i.'" src="'.$sub_dir.'/'.$image.'" alt="'.htmlspecialchars($image).'">
				</div>'."\n";
			}
			echo '</div>';
			$nb_pages = ceil($collection_count / $GLOBALS['image_per_page']) -1;
			echo '<div id="pager"> page ';
			for ($i = 0; $i <= $nb_pages; $i++) {
				if ($i == $GLOBALS['start_list_count']) {
					echo '<span>'.$i.'</span>';
				}
				else {
					echo '<a href="?fol='.$GLOBALS['request_folder'].'&page='.$i.'">'.$i.'</a>';
				}
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

}


?>


<div id="slider">
	<div id="slider-box">
		<div id="slider-box-cnt">
			<div id="slider-box-img-wrap"><a id="slider-img-a" href="#"></a><img id="slider-img" src="" alt="#"/></div>
			<a href="#" onclick="slideshow('close'); return false;" class="slider-quit"></a>
		</div>
		<div id="slider-box-inf">
			<p class="slider-buttons">
	<a href="#" onclick="slideshow('first'); return false;" class="slider-first"></a><a href="#" onclick="slideshow('prev'); return false;" class="slider-prev" id="slider-prev"></a><a href="#" onclick="slideshow('next'); return false;" class="slider-next" id="slider-next"></a><a href="#" onclick="slideshow('last'); return false;" class="slider-last"></a>
			</p>
		</div>
	</div>
</div>

<script type="text/javascript">
var curr_max = <?php echo count($img_list)-1; ?>;
var counter = 0;
document.onkeydown = checkKey;

function slideshow(action, imageIndex) {
	if (action == 'close') {
		document.getElementById('slider').style.display = 'none';
	}

	var ElemImg = document.getElementById('slider-img');

	var newImg = new Image();
	if (action == 'start') { document.getElementById('slider').style.display = 'block'; counter = imageIndex; }
	if (action == 'first') counter = 0;
	if (action == 'prev') counter = Math.max(--counter, 0);
	if (action == 'next') counter = Math.min(++counter, curr_max);
	if (action == 'last') counter = curr_max;

	var box_height = document.getElementById('slider-box-img-wrap').clientHeight;
	var box_width = document.getElementById('slider-box-img-wrap').clientWidth;
	var img_height = document.getElementById('img_'+counter).naturalHeight;
	var img_width = document.getElementById('img_'+counter).naturalWidth;
	var ratio_w = Math.max(1, img_width/box_width);

	newImg.onload = function() {
		ElemImg.src = newImg.src;
		//var im = curr_img[counter];
		//document.getElementById('slider-img-a').href = '?file_id='+im.id;

		ElemImg.style.marginTop = (Math.round((box_height - Math.min(img_height/ratio_w, box_height))/2))+'px';
	};

	newImg.onerror = function() {
		ElemImg.src = '';
		ElemImg.alt = 'Error Loading File';
		document.getElementById('slider-img-a').href = '#';
		ElemImg.style.marginTop = '0';
	};
	newImg.src = document.getElementById('bloc_'+counter).dataset.imgUrl;
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

</body>
</html>
