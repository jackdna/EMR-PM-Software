<?php 
require_once("../../../config/config.php");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" type="text/css" />
</head>

<body>
<div class="tab_container" style="float:left; width:100%;">
  <div id="tab1" class="tab_content">
    <div class="icons">
      <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">
        <li><a href="cont_lens_usage.php?alpha=az" class="text_purpule"><img border="0" src="../../../images/usage-new.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />
          Usage</a></li>
        <li><a href="cont_lens_usage_type.php?alpha=az" class="text_purpule"><img border="0" src="../../../images/type-lens.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />
          Type</a></li>
      </ul>
    </div>
  </div>
</div>
</body>
</html>