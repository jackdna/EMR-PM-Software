<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>Nurse Profile</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include("adminLinkfile.php"); ?>
<style>
	a.black:hover{ color:"Red";	text-decoration:none; }
	a.white { color:#FFFFFF; text-decoration:none; }
</style>
<script>
	function nurseSelected(obj){
		top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
		top.frames[0].document.getElementById('deleteSelected').style.display = 'none';
		top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';
		top.frames[0].document.getElementById('backButton').style.display = 'none';
		document.nurseListFrm.submit();
		//top.frames[0].document.frameSrc.source.value = 'nurse_profile_save.php';
	}
	
	$(window).load(function()
	{
		var LDL	=	function()
		{
			var H	=	parent.top.$("#div_middle").height()- top.frames[0].$("#div_innr_btn").outerHeight();
			H=H-$("#surgeon-header").height();
			$("iframe").attr('height', H +'px');
			var height_custom_scroll_new=	parent.frames[0].frames[0].$('.scrollable_yes');
			height_custom_scroll_new.css({ 'min-height' : H , 'max-height': H});
			
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
include_once("classObjectFunction.php");
$objManageData = new manageData;
$loginUserId = $_SESSION['loginUserId'];
	$userLogedDetails = $objManageData->getRowRecord('users', 'usersId', $loginUserId);
	$userType = $userLogedDetails->user_type;
	
$nurseList = $_REQUEST['nurseList'];
	

?>


<form name="nurseListFrm" action="nurse_profile.php" method="post">
 <Div class="all_admin_content_agree wrap_inside_admin">      
     <Div class="subtracting-head text-center" id="surgeon-header">
         <div class="head_scheduler new_head_slider padding_head_adjust_admin">
            <span>
                Nurse Preference Card
            </span>
          </div>
		 <Div class="wrap_inside_admin" id="nurseLabelId"> <!-- all_admin_content height_adjust_prefer -->
                      
       <div class="form_outer custom_surgeon_margin" style="">
        <Div class="col-lg-4 visible-lg"></Div>
        <Div class="col-md-4 visible-md"></Div>
        <Div class="col-sm-2 visible-sm"></Div>
        
       <div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
            <div class="form_reg wrap_surgeon border_customize_r" id="hid_nurseth">
                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-left">
                    <select name="nurseList" class="selectpicker" onChange="return nurseSelected(this.value);">
                    <option value="">Select Nurse</option>
                    <?php
                    $userNurseDetails = $objManageData->getArrayRecords('users', 'user_type', 'Nurse','lname','ASC');
                    if($userNurseDetails) {
                        foreach($userNurseDetails as $nurse){
                                
						$deleteStatus = $nurse->deleteStatus;
						if($deleteStatus=="Yes") { //IF THIS USER HAS BEEN COMMITTED AS DELETED(BY SETTING ITS deleteStatus TO Yes)
							//DO NOT SHOW DELETED USER IN DROP DOWN
						}else {
					?>
							<option value="<?php echo $nurse->usersId; ?>" <?php if($nurseList == $nurse->usersId) echo "SELECTED"; ?>><?php echo stripslashes($nurse->lname.', '.$nurse->fname); ?></option>
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
     <div class="clearfix"></div>
      <div class="head_tab_inline text-center" id="selectedNurseNameId">	
        <span> <?php
				if($_REQUEST['nurseList']) {
					$userNurseNameDetails = $objManageData->getArrayRecords('users', 'usersId', $_REQUEST['nurseList']);
					foreach($userNurseNameDetails as $nurseMainName){
						echo "&nbsp;&nbsp;".$nurseMainName->fname.' '.$nurseMainName->lname; 
					}
					
				}
			?></span>                          
      </div>	
   </Div>

    <Div class="wrap_inside_admin" id="nurseList_id">
        <div class="scheduler_table_Complete">		
<?php  $nurseFrmSrc = "nurse_profile_save.php"; ?>

<iframe name="nurseProfileFrame" style="width:100%; display:inline-block" frameborder="0" src="<?php echo $nurseFrmSrc;?>?nurseList=<?php echo $nurseList; ?>&amp;profile=<?php echo $idProfile; ?>&amp;profileId=<?php echo $idProfile; ?>&amp;seqNmbr=<?php echo $tempseqNmbr;?>"></iframe>
			
        </div>
   </Div>
</Div>
</form>
<?php
	if($_REQUEST['nurseList']<>"") {
	?>
	<script>
		//alert(document.getElementById('nurseList_id'));
		document.getElementById('nurseLabelId').style.display = 'none';
		document.getElementById('nurseList_id').style.display = 'inline-block';
		document.getElementById('selectedNurseNameId').style.display = 'inline-block';
		top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
		top.frames[0].document.frameSrc.source.value = 'nurse_profile_save.php';
		
	</script>
	<?php	
	} else {
	?>
	<script>
		document.getElementById('nurseLabelId').style.display = 'inline-block';
		document.getElementById('nurseList_id').style.display = 'none';
		document.getElementById('selectedNurseNameId').style.display = 'none';
		top.frames[0].document.getElementById('saveButton').style.display = 'none';
	</script>
	<?php	
	}
//include("../common/evaluationLocalAnesAdmin_pop.php");
//include("../common/ekgLocalAnesAdmin_pop.php");
//include("../common/post_op_evaluation_admin_pop.php");
include("../common/calculatorAdmin.php");  //FOR CALCULATOR
?>
</body>
</html>