<?php
/*
File: footer.php
Coded in PHP7
Purpose: Footer Information
Access Type: Include File
*/
?>
<div class="page_footer_bar" style="height:40px;padding:0 10px;">
    <img src="images/imedicware_logo_sm.png" style="display:inline-block;vertical-align:top;" align="middle" />
    <span class="btn_cls" id="page_buttons" style="text-align:center;width:911px;margin-left:68px;display:inline-block;padding:5px 10px;vertical-align:top;"></span>
    <span style="display:inline-block;vertical-align:middle;"><?php echo date('m/d/y');?> <span id="tick2" style="margin-left:5px;"></span>
    <span class="release-note" ><?php if(defined('PRODUCT_VERSION'))echo constant("PRODUCT_VERSION"); ?></span></span>
    
</div>
</div>
<div class="div_popup" id="div_version_release_doc"></div>
</body>
</html>