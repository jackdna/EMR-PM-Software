<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$andProfileDelCond = "  AND del_status ='' ";
$loginUserId = $_SESSION['loginUserId'];
	$userLogedDetails = $objManageData->getRowRecord('users', 'usersId', $loginUserId);
	$userType = $userLogedDetails->user_type;
	$surgeonsList = $_REQUEST['surgeonsList'];
	
if($_REQUEST['frmName']){
	$idProfile = $_REQUEST['profile'];
	$surgeonsList = $_REQUEST['surgeonId'];
	$defaultProfile = $_REQUEST['defaultProfile'];	
	if($_REQUEST['sbtSaveProfile']){
		$elem_procedures = $_REQUEST['elem_procedures'];
			if(count($elem_procedures)>1){
				$elem_procedures = implode(", ", $elem_procedures);
			}else{
				$elem_procedures = $elem_procedures[0];
			}
			
		$elem_profileName = $_REQUEST['elem_profileName'];
		$elem_preOpOrders = $_REQUEST['elem_preOpOrders'];
			if(count($elem_preOpOrders)>1){
				$elem_preOpOrders = implode(", ", $elem_preOpOrders);
			}else{
				$elem_preOpOrders = $elem_preOpOrders[0];
			}
		$elem_operativeReportTemplate = $_REQUEST['elem_operativeReportTemplate'];
		$elem_instructions = $_REQUEST['elem_instructions'];
		$elem_defaultProfile = $_REQUEST['elem_defaultProfile'];
		
		if($idProfile){
			$updateStr = "UPDATE surgeonprofile SET
							profileName = '$elem_profileName',
							procedures = '$elem_procedures',
							preOpOrders = '$elem_preOpOrders',
							operativeReportTemplate = '$elem_operativeReportTemplate',
							instructionSheet = '$elem_instructions',						
							defaultProfile = '$elem_defaultProfile'
							WHERE surgeonProfileId = '$idProfile' ".$andProfileDelCond;
			$updateQry = imw_query($updateStr);
		}else{
			$insertStr = "INSERT INTO surgeonprofile SET
							profileName = '$elem_profileName',
							procedures = '$elem_procedures',
							preOpOrders = '$elem_preOpOrders',
							operativeReportTemplate = '$elem_operativeReportTemplate',
							instructionSheet = '$elem_instructions',
							surgeonId = '$surgeonsList',
							defaultProfile = '$elem_defaultProfile'";
			$insertQry = imw_query($insertStr);
			$idProfile = imw_insert_id();
		}
		if($defaultProfile == 'yes'){
			$arrayUpdateRecord['defaultProfile'] = '0';
			$objManageData->updateRecords($arrayUpdateRecord, 'surgeonprofile', 'surgeonId', $surgeonsList, $andProfileDelCond);
			//$arrayUpdateRecord['defaultProfile'] = '1';
			//$objManageData->updateRecords($arrayUpdateRecord, 'surgeonprofile', 'surgeonProfileId', $idProfile, $andProfileDelCond);
		}
	}else if($_REQUEST['sbtDelProfile']){
		//$objManageData->delRecord('surgeonprofile', 'surgeonProfileId', $idProfile);
		unset($arrayUpdateRecord);
		$arrayUpdateRecord['del_status'] = 'yes';
		$objManageData->updateRecords($arrayUpdateRecord, 'surgeonprofile', 'surgeonProfileId', $idProfile);
		$idProfile = '';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgeon Profile</title>
<?php include("adminLinkfile.php");?>
<style>
	a.black:hover{ color:"Red";	text-decoration:none; }
	a.white { color:#FFFFFF; text-decoration:none; }
</style>
<script>
function selectProfile(proId, s, total){
	for(var i=1; i<=total; i++){
		if(document.getElementById('link'+i)){
			//document.getElementById('td'+i).style= "text-decoration:none;color:#fff; background:#333; -webkit-border-radius: 20px;-moz-border-radius: 20px;border-radius: 20px; ";
			$('#link'+i).removeAttr("style");
		}
	}
	$('#link'+s).css({"text-decoration":"none","color":"#fff","background":"#333","-webkit-border-radius":"20px","-moz-border-radius":"20px","border-radius":"20px"});
	$('#newLink').removeAttr("style");
	//alert(document.getElementById('surgeonsList'));
	var surgeonsList = document.getElementById('surgeonsList').value;	
	//document.frames[0].location.href = 'listSurgeonProfile.php?profile='+proId+'&surgeonsList='+surgeonsList;
	var frame=document.getElementById('surgeonProfileFrame');
	frame.src = 'addSurgeonProfile.php?surgeonId='+surgeonsList+'&profileId='+proId+'&seqNmbr='+s;
	
	top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
	top.frames[0].document.getElementById('deleteSelected').style.display = 'inline-block';
	top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';
	top.frames[0].document.getElementById('backButton').style.display = 'inline-block';
	
}

function showSurgeonForm(surgeonsList, total){
	for(var i=1; i<=total; i++){
		if(document.getElementById('link'+i)){
			//document.getElementById('td'+i).style= "text-decoration:none;color:#fff; background:#333; -webkit-border-radius: 20px;-moz-border-radius: 20px;border-radius: 20px; ";
			$('#link'+i).removeAttr("style");
		}
	}
	<!-- Select New Profile -->
	$('#newLink').css({"text-decoration":"none","color":"#fff","background":"#333","-webkit-border-radius":"20px","-moz-border-radius":"20px","border-radius":"20px"});
	<!-- Select New Profile -->
	//document.frames[0].location.href = 'listSurgeonProfile.php?surgeonsList='+surgeonsList+'&addNew=true';
	var frame=document.getElementById('surgeonProfileFrame');
	frame.src = 'addSurgeonProcedure.php?surgeonsList='+surgeonsList;
	//document.frames[0].frameSrc.source.value = 'addSurgeonProcedure.php';	
	top.frames[0].document.getElementById('saveButton').style.display = 'none';
	top.frames[0].document.getElementById('deleteSelected').style.display = 'none';
	//alert(top.frames[0].document.getElementById('cancelButton'));
	top.frames[0].document.getElementById('cancelButton').style.display = 'none';
	top.frames[0].document.getElementById('backButton').style.display = 'none';
}

function surgeonSelected(obj){
	top.frames[0].document.getElementById('saveButton').style.display = 'none';
	top.frames[0].document.getElementById('deleteSelected').style.display = 'none';
	top.frames[0].document.getElementById('cancelButton').style.display = 'none';
	top.frames[0].document.getElementById('backButton').style.display = 'none';
	document.surgeonListFrm.submit();
}

function show_hideButtons(profileId) {
	if(profileId == '') {
		top.frames[0].document.getElementById('saveButton').style.display = 'none';
		top.frames[0].document.getElementById('deleteSelected').style.display = 'none';
		top.frames[0].document.getElementById('cancelButton').style.display = 'none';
		top.frames[0].document.getElementById('backButton').style.display = 'none';
	}else {
		top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
		top.frames[0].document.getElementById('deleteSelected').style.display = 'inline-block';
		top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';
		top.frames[0].document.getElementById('backButton').style.display = 'inline-block';
	
	}
}
	$(window).load(function()
	{
		var LDL	=	function()
		{
			//var H	=	parent.top.$("#div_middle").height();
			//H=H-$("#surgeon-header").height();
			var WH	=	$(window).height();
			var SH		=	$(".head_tab_inline").outerHeight(true);
			var HH		=	$(".tab-slider").outerHeight(true);
		
			var AH		=	WH	-	(SH + HH);
		
			$("iframe").attr('height', AH );
		}
		LDL();
		$(window).resize(function(e) {
         	LDL();
        });
	});

</script>

</head>
<body>
<?php
	$user_id = $_REQUEST['user_id'];
	$selectedSurgeonDetails = $objManageData->getRowRecord('users', 'usersId', $user_id);	
	$suegeonName = ucfirst($userLogedDetails->fname).' '.ucfirst(substr($userLogedDetails->mname, 0, 1)).' '.ucfirst($userLogedDetails->lname);
?>
<form name="surgeonListFrm" action="surgeonprofile.php" method="post" class="alignCenter">
 <Div class="all_admin_content_agree wrap_inside_admin">      
     <Div class="subtracting-head" id="surgeon-header">
         <div class="head_scheduler new_head_slider padding_head_adjust_admin">
            <span>
                Surgeon Preference Card 
            </span>
          </div>
		 <Div class="wrap_inside_admin" id="surgeonsList_id"> <!-- all_admin_content height_adjust_prefer -->
                      
       <div class="form_outer custom_surgeon_margin" style="">
                        <Div class="col-lg-4 visible-lg"></Div>
                        <Div class="col-md-4 visible-md"></Div>
                        <Div class="col-sm-2 visible-sm"></Div>
                        
                       <div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
                                    <div class="form_reg wrap_surgeon border_customize_r" id="hid_surgeon">	 
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                <select name="surgeonsList" id="surgeonsList" onChange="return surgeonSelected(this.value);" class="selectpicker">
        <option value="">Select Surgeon</option>
        <?php
        $userSurgeonsDetails = $objManageData->getArrayRecords('users', 'user_type', 'Surgeon','lname','ASC');
        if($userSurgeonsDetails) {
            foreach($userSurgeonsDetails as $surgeon){
                    
                    $deleteStatus = $surgeon->deleteStatus;
                    if($deleteStatus=="Yes") { //IF THIS USER HAS BEEN COMMITTED AS DELETED(BY SETTING ITS deleteStatus TO Yes)
                        //DO NOT SHOW DELETED USER IN DROP DOWN
                    }else {
                ?>
                        <option value="<?php echo $surgeon->usersId; ?>" <?php if($surgeonsList == $surgeon->usersId) echo "SELECTED"; ?>><?php echo $surgeon->lname.', '.$surgeon->fname; ?></option>
                <?php
                    }
            }
        }	
        ?>
    </select>
                                            </div>
                                        
                                    </div><!-------------------Form Reg-----------------------------> 	
                             </div>	
                        
     </div>
     </Div>
     
      <div class="head_tab_inline text-center" id="selectedSurgeonNameId">	
        <span> <?php
			if($_REQUEST['surgeonsList']) {
				$userSurgeonsNameDetails = $objManageData->getArrayRecords('users', 'usersId', $_REQUEST['surgeonsList']);
				foreach($userSurgeonsNameDetails as $surgeonMainName){
					echo $surgeonMainName->fname.' '.$surgeonMainName->lname; 
				}
				
			}
		?> </span>                          
      </div>	
      
        <div class="tab-slider">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left" style="color:#3232f0">
             <div class="bot-links text-left">
                <?php
        if($surgeonsList)
		{    
                    
    $surgeonsProfilesDetails = $objManageData->getArrayRecords('surgeonprofile', 'surgeonId', $surgeonsList,'profileName','ASC', $andProfileDelCond);
    $totalTds = count($surgeonsProfilesDetails);
    if(count($surgeonsProfilesDetails)>0){
        foreach($surgeonsProfilesDetails as $key => $surgeonProfiles){
            $totalTds = count($surgeonsProfilesDetails);									
            $profile_id = $surgeonProfiles->surgeonProfileId;									
            $defaultProfile = $surgeonProfiles->defaultProfile;
            if($idProfile==''){ 
                if($defaultProfile=='1'){
                    $idProfile = $profile_id; 
                }
            }
            $profileName = $surgeonProfiles->profileName;
            ++$counter;
            ++$seq;
            $clkStylDefault = '';
            if($profile_id==$idProfile)$clkStylDefault = ' text-decoration:none;color:#fff; background:#333; -webkit-border-radius: 20px;-moz-border-radius: 20px;border-radius: 20px; ';		
            
            ?>
             <a id="link<?php echo $seq; ?>" style="<?php echo $clkStylDefault; ?>" href="javascript:selectProfile('<?php echo $profile_id; ?>', '<?php echo $seq; ?>', '<?php echo $totalTds; ?>');"><?php echo $profileName; ?></a>
                        
            <?php if($profile_id==$idProfile) {  $tempseqNmbr = $seq;  ?>
               <input type="hidden" name="seqNmbr" value="<?php echo $seq;?>">
            <?php }
        }
    }else{
        ?>
        <div style="float:left; font-size:14px; color:#333; font-weight:bold">  No Profile Exists &nbsp;</div>
      
        <?php
    }?><a id="newLink" href="javascript:showSurgeonForm('<?php echo $surgeonsList; ?>', '<?php echo $totalTds; ?>');" class="btn btn-info"><b class="fa fa-plus"></b> Add New</a>
    
       <?php
	}
    ?>          </div>
           </div>
            </div>
    </Div>
        
     <Div class="wrap_inside_admin" id="surgeonProfile">
       <div class="scheduler_table_Complete">
    
        <div style="width:100%" class="row padding_o clear">
        <?php 
					if($idProfile) {
						$surgeonFrmSrc = "addSurgeonProfile.php";
					} else {
						$surgeonFrmSrc = "addSurgeonProcedure.php";
					}
					
				//echo $tempseqNmbr."helo";
				?>
				<iframe name="surgeonProfileFrame" id="surgeonProfileFrame" frameborder="0" src="<?php echo $surgeonFrmSrc;?>?surgeonsList=<?php echo $surgeonsList; ?>&amp;profile=<?php echo $idProfile; ?>&amp;profileId=<?php echo $idProfile; ?>&amp;seqNmbr=<?php echo $tempseqNmbr;?>"></iframe>
         </div>                
      
     </div>	
     </Div>
      </Div>	 
    
</form>
<?php
	if($_REQUEST['surgeonsList']<>"") {
	?>
	<script>
		//alert(document.getElementById('surgeonsList_id'));
		document.getElementById('surgeonsList_id').style.display = 'none';
		document.getElementById('selectedSurgeonNameId').style.display = 'block';
	</script>
	<?php	
	} else {
	?>
	<script>
		document.getElementById('surgeonsList_id').style.display = 'block';
		document.getElementById('selectedSurgeonNameId').style.display = 'none';
	</script>
	<?php	
	}
if($_REQUEST['sbtloc']=='yes') {
?>
	<script>
		var surgeonsListID = '<?php echo $_REQUEST["surgeonId"];?>';
		var profId = '<?php echo $_REQUEST["profileId"];?>';
		var seqNmbr = '<?php echo $_REQUEST["seqNmbr"];?>';
		var totalTds = '<?php echo $totalTds;?>';
		//alert(top.frames[0].name);
		var surgeonProfileFrame = eval(surgeonProfileFrame);
		if(seqNmbr) {
			selectProfile(profId, seqNmbr, totalTds);
		}else {
			document.frames[0].location.href = 'addSurgeonProfile.php?surgeonId='+surgeonsListID+'&surgeonsList='+surgeonsListID+'&profileId='+profId+'&sbtloc=yes';
		}	
	</script>
<?php	
}
if($_REQUEST['sbtloc']=='del') {
?>
	<script>
		var surgeonsListID = '<?php echo $_REQUEST["surgeonId"];?>';
		var totalTds = '<?php echo $totalTds;?>';
		showSurgeonForm(surgeonsListID,totalTds);
		//document.frames[0].location.href = 'addSurgeonProfile.php?surgeonId='+surgeonsListID+'&surgeonsList='+surgeonsListID+'&sbtloc=del';
	</script>
<?php	
}
include("../common/preOpMediOrderPopUp.php");
include("../common/intraOpPostOpPopAdmin.php");
include("../common/post_op_drops_popAdmin.php");
include("../common/other_preop_orders_pop_admin.php");

?>
</body>
</html>