<?php
require_once(dirname(__FILE__).'/../../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
$OBJCommonFunction = new CLSCommonFunction;
$nurow=0;
if($btn_sub || $btn_enter){
	$txt_for_arr = preg_split("/,/",$txt_for);
	$txt_for_val = trim($txt_for_arr[0]);
	$getResultArr = $OBJCommonFunction->getRefferPhysician($sel_by,$txt_for_val);
	$nurow=count($getResultArr);
	foreach($getResultArr as $getResult){
		$title = $getResult['Title'];
		$patientname = $getResult['LastName'];
		$patientname .= trim($getResult['FirstName']) == '' ? '' : ', ';
		$patientname .= ucfirst($getResult['FirstName']).' ';
		if($getResult['MiddleName'] != '')
		$patientname .= $getResult['MiddleName']." ";
		$patientname .= $title;
		$patientname = $OBJCommonFunction->getRefPhyName($getResult['physician_Reffer_id']);
		$address = $getResult['Address1'].' '.$getResult['Address2'];
		$id = $getResult['id'];
		$physician_Reffer_id = $getResult['physician_Reffer_id'];
		$anchor = " onClick=\"selpidtt('".core_refine_user_input($patientname)."',$physician_Reffer_id)\" style=\"cursor:pointer\"";		
		$data .=<<<DATA
		    <tr>
			   <td class="text-left" $anchor>$patientname</td>
			   <td class="text-left" $anchor>$address</td>
			   <td class="text-left" $anchor>$physician_Reffer_id</td>
		   </tr>
DATA;
	}
}
?>
<!DOCTYPE html>
<head>
<title>imwemr: Select Physician</title>

<!-- Bootstrap -->
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/html5shiv.min.js"></script>
  <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/bootstrap-dropdownhover.min.js"></script>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-select.js"></script>
<!--jquery to suport discontinued functions-->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery-migrate-1.2.1.js"></script> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.js"></script> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>

<!--<script type="text/javascript" src="script_function.js"></script>
<script type="text/javascript" src="../../js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../../library/messi/messi.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>-->
<script type="text/javascript">

	function chk(frm)
	{
		if(frm.sel_by.value == '')
		{
			top.fAlert("Please select 'Select By'.", "", "", "250px");
			return false;
		}
		
		if(frm.txt_for.value == '')
		{
			top.fAlert("Please Fill in 'For'.", "", "", "250px");
			return false;
		}
		return true;
	}
	
	function selpidtt(name,id){
		var type = document.getElementById("type").value;
		var name = name.replace('&quot;', '"');
		opener.get_phy_name_from_search(name,id,type)
		window.close();
	}
	
</script>
</head>
<body>
<div class="whtbox">
    <div class="row boxheadertop">
        <div class="col-sm-8 text-right">
        
        	<form name="frm_sel" action="searchPhysician.php" method="post" onSubmit="return chk(this.form)">
            <div class="row">
                <div class="col-sm-5"><input type="text" id="txt_for" name="txt_for" value="<?php echo stripslashes($txt_for)?>" class="form-control"></div>
                <div class="col-sm-5">
                   <select name="sel_by" class="form-control minimal selecicon">
                        <option value="">-Select By-</option>
                        <option value="LastName" selected="selected">Last Name</option>
                        <option value="FirstName">First Name</option>
                        <option value="Address1">Street Address</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <input type='hidden' id="btn_enter" name="btn_enter" value='a' >
		            <input type="hidden" name="type" id="type"  value="<?php print $type; ?>">
                    <button type="submit" value="save" class="btn btn-info" name="save_butt" id="save_butt" >Search</button>
                </div>
            </div>
            </form>
    	</div>
        <div class="col-sm-4 text-right" id="currentResultSpanId"><strong>Total match found: <?php echo $nurow;?></strong></div>         
    </div>
    
    
    <div class="row">
        <div class="col-sm-12">
            <div id="body_area" style="height:370px; overflow:auto">
           	<?php
				if($data != "")
				{
					?>
					<table class="table table-striped table-bordered table-hover adminnw">
					<thead>
						<tr>
							<th>Name</th>
							<th>Address</th>
							<th>ID</th>
						</tr>
					 </thead>
					 <tbody>
						<?php print $data; ?>
					 </tbody>
					</table>
					<?php
						}else{
				?>
					<table class="table table-striped table-bordered table-hover">
						<tr>
							<th class="warning" >No record found !</th>
						</tr>
					</table>                                
				<?php
					}					
				?>	
            </div>
        </div>        
    </div>
</div>

<div class="container-fluid text-right">
	<button class="btn btn-danger" name="btn_find" id="btn_find" onclick="javascript:window.close();">Close</button>
</div>



</body>
</html>