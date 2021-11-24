<?php 
/*
File: index.php
Coded in PHP7
Purpose: Main Screen Frame
Access Type: Direct access
*/
?>
<div style="overflow:auto; height:100%; width:100%;">
<?php require("library/header.php"); ?>
<div class="pagecontent" id="main_page_area" style="height:<?php echo $_SESSION['wn_height']-292;?>px;">
	<?php require("library/left.php"); ?>
        <div class="right_content container-fluid">
            <iframe src="interface/<?php echo $default_tab; ?>" name="main_iframe" id="main_iframe" style="width:100%; height:<?php echo $_SESSION['wn_height']-300; ?>px; margin:0px 3px 0px 3px;" onload="top.loading.style.display='none';" frameborder="0" scrolling="auto" framespacing=0></iframe>
        </div>
</div>
<?php require("library/footer.php"); ?>
</div>