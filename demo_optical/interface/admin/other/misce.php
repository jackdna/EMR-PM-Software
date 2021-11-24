<?php
	/*
	File: misce.php
	Coded in PHP7
	Purpose: View/Update: Alternating Setting
	Access Type: Direct access
	*/
	require_once("../../../config/config.php");
	$msg_stat = "none";
	$operator_id = $_SESSION['authId'];
	$entered_date = date("Y-m-d");
	$entered_time = date("h:i:s");
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST)){
		
		//---- BEGIN UPDATE RELATED ITEMS PRICE RANGE -----------
		$msg = '';
		$qry = "UPDATE in_alternative_settings SET price_min_range = '".$_REQUEST['price_min_range']."',
		price_max_range='".$_REQUEST['price_max_range']."',a_min_range='".$_REQUEST['a_min_range']."',
		a_max_range='".$_REQUEST['a_max_range']."',ed_min_range='".$_REQUEST['ed_min_range']."',
		ed_max_range='".$_REQUEST['ed_max_range']."',fpd_min_range='".$_REQUEST['fpd_min_range']."',
		fpd_max_range='".$_REQUEST['fpd_max_range']."',frame_shape='".$_REQUEST['frame_shape']."',
		gender='".$_REQUEST['gender']."',frame_style='".$_REQUEST['frame_style']."',frame_color='".$_REQUEST['frame_color']."',
		frame_brand='".$_REQUEST['frame_brand']."', modified_date='$entered_date', modified_time='$entered_time', 
		modified_by='$operator_id' WHERE  id = '1'";

		imw_query($qry);
		$msgCode = "1";
	}
switch($msgCode){
	case "1":
	$msg = "Records updated successfully";
	break;
	default:
	$msg = '';
	break;
}
$sql = "SELECT * FROM in_alternative_settings";
$res = imw_query($sql);
$row = imw_fetch_assoc($res);

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script>
$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});
</script>
</head>
<body>
<div>
    <form name="frm" id="frm" action="" method="post" class="mt10">
      <div id="listing_record" style="height:370px; overflow:auto;">
        <table class="table_collapse">
          <tr class="listheading">
            <td width="560"><div class="fl">Alternative Settings</div>
            <?php if(isset($msg) && $msg != ""){
					echo '<div class="success_msg" style="text-align:center">'.$msg.'</div>';
					}
				?>
             </td>
          </tr>
          
          <tr>
            <td><table width="100%" border="0">
              <tr class="even">
                <td width="38%" class="even">Related items price range </td>
                <td width="61%" class="even">
               
                -&nbsp;<input type="text" name="price_min_range" class="textbox" style="width:40px;" value="<?php echo isset($row['price_min_range'])?$row['price_min_range']:'';?>"> &nbsp; 
                +&nbsp;<input type="text" name="price_max_range" class="textbox" style="width:40px;" value="<?php echo isset($row['price_max_range'])?$row['price_max_range']:'';?>"></td>
              </tr>
			  <tr class="odd">
                <td width="39%">Related items A measurement range </td>
                <td width="61%">
               
                -&nbsp;<input type="text" name="a_min_range" class="textbox" style="width:40px;" value="<?php echo isset($row['a_min_range'])?$row['a_min_range']:'';?>"> &nbsp; 
                +&nbsp;<input type="text" name="a_max_range" class="textbox" style="width:40px;" value="<?php echo isset($row['a_max_range'])?$row['a_max_range']:'';?>"></td>
              </tr>
              <tr class="even">
                <td width="39%">Related items ED measurement range </td>
                <td width="61%">
               
                -&nbsp;<input type="text" name="ed_min_range" class="textbox" style="width:40px;" value="<?php echo isset($row['ed_min_range'])?$row['ed_min_range']:'';?>"> &nbsp; 
                +&nbsp;<input type="text" name="ed_max_range" class="textbox" style="width:40px;" value="<?php echo isset($row['ed_max_range'])?$row['ed_max_range']:'';?>"></td>
              </tr>
			  <tr class="odd">
                <td width="39%">Related items FPD measurement range </td>
                <td width="61%">
               
                -&nbsp;<input type="text" name="fpd_min_range" class="textbox" style="width:40px;" value="<?php echo isset($row['fpd_min_range'])?$row['fpd_min_range']:'';?>"> &nbsp; 
                +&nbsp;<input type="text" name="fpd_max_range" class="textbox" style="width:40px;" value="<?php echo isset($row['fpd_max_range'])?$row['fpd_max_range']:'';?>"></td>
              </tr>
			  <tr class="even">
                <td width="39%">Related items frame shape </td>
                <td width="61%">
               		<input type="checkbox" name="frame_shape" class="textbox" style="width:25px;" value="1" <?php if($row['frame_shape']=="1") { echo "checked"; } ?>>
                </td>
              </tr>
			  <tr class="odd">
                <td width="38%">Related items gender </td>
                <td width="61%">
               		<input type="checkbox" name="gender" class="textbox" style="width:25px;" value="1" <?php if($row['gender']=="1") { echo "checked"; } ?>>
                </td>
              </tr>
               <tr class="even">
                <td width="38%">Related items style </td>
                <td width="61%">
               		<input type="checkbox" name="frame_style" class="textbox" style="width:25px;" value="1" <?php if($row['frame_style']=="1") { echo "checked"; } ?>>
                </td>
              </tr>
               <tr class="odd">
                <td width="38%">Related items color </td>
                <td width="61%">
               		<input type="checkbox" name="frame_color" class="textbox" style="width:25px;" value="1" <?php if($row['frame_color']=="1") { echo "checked"; } ?>>
                </td>
              </tr>
              <tr class="even">
                <td width="38%">Related items brand </td>
                <td width="61%">
               		<input type="checkbox" name="frame_brand" class="textbox" style="width:25px;" value="1" <?php if($row['frame_brand']=="1") { echo "checked"; } ?>>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
         
        </table>
      </div>
    </form>

</div>
<script type="text/javascript">
function submitFrom(){
	document.frm.submit();
}
$(document).ready(function()
{
//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Save","top.main_iframe.admin_iframe.submitFrom();");
	top.btn_show("admin",mainBtnArr);
});
</script>
</body>
</html>