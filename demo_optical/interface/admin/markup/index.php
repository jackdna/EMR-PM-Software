<?php
/*
File: index.php
Coded in PHP7
Purpose: Oprical Retail price markup
Access Type: Direct access
*/
require_once("../../../config/config.php");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH']; ?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" type="text/css" />
<style type="text/css">
.icon{
	width: 69px;
	float: left;
	border: 1px solid #CCC;
	height: 69px;
	border-radius: 4px;
	box-shadow: 1px 1px 0px #999;
	padding: 5px;
	margin: 0 0 5px 4px;
}
</style>
</head>
<body>
<div class="tab_container" style="float:left; width:100%;">
  <div id="tab1" class="tab_content">
    <div class="icons">
      <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">
        <li>
			<a href="frames.php" class="text_purpule">
				<img border="0" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/noimage_icon.jpg" class="icon" />
				<br />Frames
			</a>
		</li>
        <li>
			<a href="cont_lens.php" class="text_purpule">
				<img border="0" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/noimage_icon.jpg" class="icon" />
				<br />Contact Lens
			</a>
		</li>
		<li>
			<a href="supplies.php" class="text_purpule">
				<img border="0" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/noimage_icon.jpg" class="icon" />
				<br />Supplies
			</a>
		</li>
		<li>
			<a href="medicine.php" class="text_purpule">
				<img border="0" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/noimage_icon.jpg" class="icon" />
				<br />Medicine
			</a>
		</li>
      </ul>
    </div>
  </div>
</div>
</body>
</html>