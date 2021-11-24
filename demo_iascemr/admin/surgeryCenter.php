<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$sqlStr = "SELECT * FROM surgerycenter WHERE surgeryCenterId = '1'";
$sqlQry = imw_query($sqlStr);
$rowsCount = imw_num_rows($sqlQry);
if($rowsCount>0){
	$sqlRows = imw_fetch_array($sqlQry);
	extract($sqlRows);
	$name = stripslashes($name);
	$address = stripslashes($address);
	$address2 = stripslashes($address2);
	$city = stripslashes($city);
	$contactName = stripslashes($contactName);
	$loginLegalNotice = stripslashes($loginLegalNotice);
/////////	
  $surgeryCenterLogo= $sqlRows['surgeryCenterLogo'];
  $logoName= $sqlRows['logoName'];
	
	$oproom	=	$sqlRows['vital_sign_oproom'];
	$macAnes	=	$sqlRows['vital_sign_macAnes'];
	$genAnes	=	$sqlRows['vital_sign_genAnes'];
	$transferFollowup	=	$sqlRows['vital_sign_transferFollowup'];
	$peer_review	=	$sqlRows['peer_review'];
	$fire_risk_analysis	=	$sqlRows['fire_risk_analysis'];
	$vital_time_slot = $sqlRows['vital_time_slot']; 
	$discharge_disclaimer = stripslashes($discharge_disclaimer);
	
}
$sqlStr1 = "SELECT * FROM label_size WHERE l_id = '1'";
$sqlQry1 = imw_query($sqlStr1);
$sqlRows1 = @imw_fetch_array($sqlQry1);
$large_top= $sqlRows1['large_top'];
$large_bottom= $sqlRows1['large_bottom'];
$large_inner= $sqlRows1['large_inner'];
$small_top= $sqlRows1['small_top'];
$small_bottom= $sqlRows1['small_bottom'];
$small_inner= $sqlRows1['small_inner'];



?>
<!DOCTYPE html>
<html>
<head>
<title>Surgery Center</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include("adminLinkfile.php");?>
<script src="../js/jscript.js"></script>
<script>
function checkRecentlyUsedPass(obj){
	if(isNaN(obj.value)){
		alert("Please enter only numeric value.")
		obj.value = '5';
	}
	if((obj.value>10) || (obj.value<5)){
		alert("Please enter with in the limit 5-10.")
		obj.value = '5';
	}
}
function checkLoginAttempt(obj){
	if(isNaN(obj.value)){
		alert("Please enter only numeric value.")
		obj.value = '3';
	}
}
function checkPassExpiresDays(obj){
	if(isNaN(obj.value)){
		alert("Please enter only numeric value.")
		obj.value = '90';
	}
}
function checkDocumentsExpireDays(obj){
	if(isNaN(obj.value)){
		alert("Please enter only numeric value.")
		obj.value = '90';
	}
}
function changeColor(obj){	
	if(obj == 'addressTable'){
		for(i=1;i<=3;i++){			
			document.getElementById('nameTr'+i).style.background = "#FFFFFF";
			document.getElementById('addressTr'+i).style.background = "#FFFFCC";
			document.getElementById('contactTr'+i).style.background = "#FFFFFF";
		}
		for(i=4;i<=7;i++){
			document.getElementById('addressTr'+i).style.background = "#FFFFCC";
			document.getElementById('nameTr'+i).style.background = "#F4F9EE";
			document.getElementById('contactTr'+i).style.background = "#F4F9EE";
		}
		document.getElementById('hipaaTr1').style.background = "#FFFFFF";
		document.getElementById('hipaaTr2').style.background = "#FFFFFF";
		document.getElementById('billingLoc').style.background = "#FFFFFF";
	}
	if(obj == 'nameTable'){		
		for(i=1;i<=3;i++){
			document.getElementById('addressTr'+i).style.background = "#FFFFFF";
			document.getElementById('nameTr'+i).style.background = "#FFFFCC";
			document.getElementById('contactTr'+i).style.background = "#FFFFFF";
		}
		for(i=4;i<=7;i++){
			document.getElementById('addressTr'+i).style.background = "#F4F9EE";
			document.getElementById('nameTr'+i).style.background = "#FFFFCC";
			document.getElementById('contactTr'+i).style.background = "#F4F9EE";
		}
		document.getElementById('hipaaTr1').style.background = "#FFFFFF";
		document.getElementById('hipaaTr2').style.background = "#FFFFFF";
		document.getElementById('billingLoc').style.background = "#FFFFFF";
	}
	if(obj == 'contactTable'){
		for(i=1;i<=3;i++){
			document.getElementById('addressTr'+i).style.background = "#FFFFFF";
			document.getElementById('nameTr'+i).style.background = "#FFFFFF";
			document.getElementById('contactTr'+i).style.background = "#FFFFCC";
		}
		for(i=4;i<=7;i++){
			document.getElementById('addressTr'+i).style.background = "#F4F9EE";
			document.getElementById('nameTr'+i).style.background = "#F4F9EE";
			document.getElementById('contactTr'+i).style.background = "#FFFFCC";
		}
		document.getElementById('hipaaTr1').style.background = "#FFFFFF";
		document.getElementById('hipaaTr2').style.background = "#FFFFFF";
		document.getElementById('billingLoc').style.background = "#FFFFFF";
	}
	
	if((obj == 'BillingY') || (obj == 'BillingN') || (obj == 'AcceptY') || (obj == 'AcceptN') || (obj == 'billingLoc')){
		if(obj == 'BillingY'){
			checkSingle('elem_billLocation_Y','elem_billLocation');
		}else if(obj == 'BillingN'){
			checkSingle('elem_billLocation_N','elem_billLocation');
		}else if(obj == 'AcceptY'){
			checkSingle('elem_acceptAssignment_Y','elem_acceptAssignment');			
		}else if(obj == 'AcceptN'){
			checkSingle('elem_acceptAssignment_N','elem_acceptAssignment');
		}
		document.getElementById('billingLoc').style.background = "#FFFFCC";
		for(i=1;i<=3;i++){
			document.getElementById('addressTr'+i).style.background = "#FFFFFF";
			document.getElementById('nameTr'+i).style.background = "#FFFFFF";
			document.getElementById('contactTr'+i).style.background = "#FFFFFF";
		}
		for(i=4;i<=7;i++){
			document.getElementById('addressTr'+i).style.background = "#F4F9EE";
			document.getElementById('nameTr'+i).style.background = "#F4F9EE";
			document.getElementById('contactTr'+i).style.background = "#F4F9EE";
		}
		document.getElementById('hipaaTr1').style.background = "#FFFFFF";
		document.getElementById('hipaaTr2').style.background = "#FFFFFF";
	}
	
	if((obj == 'hipaaTr1') || (obj == 'hipaaTr2')){
		if(obj == 'hipaaTr1'){
			document.getElementById('hipaaTr1').style.background = "#FFFFCC";
			document.getElementById('hipaaTr2').style.background = "#FFFFFF";
		}else{
			document.getElementById('hipaaTr2').style.background = "#FFFFCC";
			document.getElementById('hipaaTr1').style.background = "#FFFFFF";
		}
		document.getElementById('billingLoc').style.background = "#FFFFFF";
		for(i=1;i<=3;i++){
			document.getElementById('addressTr'+i).style.background = "#FFFFFF";
			document.getElementById('nameTr'+i).style.background = "#FFFFFF";
			document.getElementById('contactTr'+i).style.background = "#FFFFFF";
		}
		for(i=4;i<=7;i++){
			document.getElementById('addressTr'+i).style.background = "#F4F9EE";
			document.getElementById('nameTr'+i).style.background = "#F4F9EE";
			document.getElementById('contactTr'+i).style.background = "#F4F9EE";
		}
	}
}
//AJAX
function getCityStateFn(obj){
	var z = obj.value;
	if(z){
		var xmlHttp;
		try{		
			xmlHttp=new XMLHttpRequest();
		}
		catch (e){
			try{
				xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e){
				try{
					xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (e){
					alert("Your browser does not support AJAX!");
					return false;
				}
			}
		}
		xmlHttp.onreadystatechange=function(){
			if(xmlHttp.readyState==4){
				var val = xmlHttp.responseText;
				if(val!=''){
					var i = val.indexOf(",");
					var city = val.substr(0,i);
					var state = val.substr(i+1);
					document.frmSurgeryCenter.elem_city.value=city;
					document.frmSurgeryCenter.elem_state.value=state;
				}else{
					alert('Please enter correct zip code.')
					document.frmSurgeryCenter.elem_city.value='';
					document.frmSurgeryCenter.elem_state.value='';
					document.frmSurgeryCenter.elem_zip.value='';
				}
			}
		}
		xmlHttp.open("GET","getStateZip.php?zip="+z,true);
		xmlHttp.send(null);
	}
}
//AJAX
function popUpScan(){
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	
	var	W	=	( SW > 1200 ) ?  1200	: SW ;
	var	H	=	W * 0.65
	
	var L	=	(SW - W ) / 2  ;
    var T	= 	(SH - H ) / 2 - 50 ; 
	//window.open('scanPopUp.php?admin=true','scanWin', 'width=775, height=650');
	window.open('scanPopUp.php?admin=true','scanWin', 'width='+W+', height='+ H);
}
function ShowDiv(obj){
	window.open('showImg.php','SurgerycenterLogoImage','scrollbars=yes,width=900,height=400,resizable=yes,location=yes,status=yes');
	/*
	document.getElementById('imgDiv').style.display = 'block';
	var frmObj = document.frames[0];
	var imgFullHeight = frmObj.document.getElementById('imageTD').height;
	var imgFullWidth = frmObj.document.getElementById('imageTD').width;	
	document.getElementById('imgDiv').style.height = imgFullHeight;
	document.getElementById('imgDiv').style.width = imgFullWidth;
	document.getElementById('imageFrame').height = parseInt(imgFullHeight) + 50;
	document.getElementById('imageFrame').width = parseInt(imgFullWidth) + 50;
	*/
}
function deleteSurgeryCenterLogo() {
	if(confirm("Do you want to delete the logo?")){
		document.frmSurgeryCenter.logoMode.value='delete';
		document.frmSurgeryCenter.submit();
	}
}

var LOD_ASC	=	function()
{
		var BH	=	$(window).height() - $(".subtracting-head").outerHeight(true);
		//console.log(' Body ' + BH)
		$("#dataBody").css({ 'height' : BH + 'px', 'max-height': BH+'px','overflow':'hidden','overflow-y':'auto'});
};

$(window).load(function(){ LOD_ASC(); });
$(window).resize(function(){ LOD_ASC(); });
</script>
</head>
<body >
      

<div class=" wrap_inside_admin">
    <form name="frmSurgeryCenter" action="addEditCenter.php" method="post"  style="margin:0px;" onSubmit="return checkForm(this)" enctype="multipart/form-data" >
        <input type="hidden" name="surgeryCenterId" id="surgeryCenterId" value="<?php echo $surgeryCenterId; ?>">
        <input type="hidden" name="logoMode" id="logoMode" value="">
    
        <div class="subtracting-head">	
            <div class="head_scheduler new_head_slider padding_head_adjust_admin ">
                <span>Settings</span>
            </div>
        </div>
        <div class=" wrap_inside_admin " id="dataBody">
          <Div class="">
                
                <!-- First ROw Ends -->
                <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 for_sm_font">
                	<div class="row">
                		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                                <div class="form_inner_m">
                                      <div class="row">
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                <label for="yes" class="text-left"> 
                                                     Billing Address		
                                                </label>
                                            </div>
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                <label for="elem_billLocation_Y" class="f_size" > 
                                                     <input type="checkbox" name="elem_billLocation" id="elem_billLocation_Y" onClick="javascript:checkSingleAdmin('elem_billLocation_Y','elem_billLocation');"  value="Y"  <?php echo (($billLocation == "Y") || empty($billLocation))  ? "checked" : "" ;?> />  Yes		
                                                </label>
                                                &nbsp;
                                                
                                                <label for="elem_billLocation_N" class="f_size"> 
                                                     <input type="checkbox" name="elem_billLocation" id="elem_billLocation_N" onClick="javascript:checkSingleAdmin('elem_billLocation_N','elem_billLocation');" value="N" <?php echo ($billLocation == "N") ? "checked" : "" ;?> />  No	
                                                </label>
                                            </div>
                                            
                                       </div> 		
                                </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                                <div class="form_inner_m">
                                      <div class="row">
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                <label for="DX Code" class="text-left"> 
                                                    DX Code
                                                </label>
                                            </div>
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                <label for="elem_diagnosis_code_type_icd9" class="f_size"> 
                                                     <input type="checkbox" name="elem_diagnosis_code_type" id="elem_diagnosis_code_type_icd9" onClick="javascript:checkSingleAdmin('elem_diagnosis_code_type_icd9','elem_diagnosis_code_type');" value="icd9" <?php echo (($diagnosis_code_type == "icd9") || empty($diagnosis_code_type)) ? "checked" : "" ;?>/> ICD9		
                                                </label>
                                                &nbsp; 
                                                
                                                <label for="elem_diagnosis_code_type_icd10" class="f_size"> 
                                                     <input type="checkbox" name="elem_diagnosis_code_type" id="elem_diagnosis_code_type_icd10" onClick="javascript:checkSingleAdmin('elem_diagnosis_code_type_icd10','elem_diagnosis_code_type');" value="icd10" <?php echo (($diagnosis_code_type == "icd10")) ? "checked" : "" ;?>/> ICD10		
                                                </label>
                                            </div>
                                            
                                       </div> 		
                                </div>
                        </div>
                  
                        <div class="col-lg-6 col-md-4 col-sm-4 col-xs-4">
                                <div class="form_inner_m">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4 col-xs-6 col-sm-6">
                                            <label for="Accept Assignment" class="text-left"> 
                                            Accept Assignment
                                            </label>
                                        </div>
                                        <div class="col-md-8 col-lg-8 col-xs-6 col-sm-6">
                                            <label for="elem_documentsExpireDays" class="text-left"> 
                                            Documents Expire Days
                                            </label>
                                        </div>
                                        
                                        <div class="col-md-4 col-lg-4 col-xs-6 col-sm-6">
                                            <label for="elem_acceptAssignment_Y" class="f_size"> 
                                            <input type="checkbox" name="elem_acceptAssignment" id="elem_acceptAssignment_Y" onClick="javascript:checkSingleAdmin('elem_acceptAssignment_Y','elem_acceptAssignment');" value="Y" <?php echo (($acceptAssignment == "Y") || empty($acceptAssignment)) ? "checked" : "" ;?>/>  Yes		
                                            </label>
                                            &nbsp; 
                                            
                                            <label for="elem_acceptAssignment_N" class="f_size"> 
                                            <input type="checkbox" name="elem_acceptAssignment" id="elem_acceptAssignment_N" onClick="javascript:checkSingleAdmin('elem_acceptAssignment_N','elem_acceptAssignment');" value="N" <?php echo ($acceptAssignment == "N") ? "checked" : "" ;?> />  No	
                                            </label>
                                        </div>
                                        <div class="col-md-8 col-lg-8 col-xs-6 col-sm-6">
                                            <input class="form-control" type="text" id="elem_documentsExpireDays" name="elem_documentsExpireDays" value="<?php if($documentsExpireDays) echo $documentsExpireDays;?>" onBlur="checkDocumentsExpireDays(this)"/>
                                        </div>
                                    </div> 		
                                </div>
                        </div>	    	
                    </div>
                </div>
                         
                
                  
                <!-- ICD -->       
                           
                
                <!-- ICD -->
                 <div class="clearfix hidden-lg"></div>
                  <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <div class="form_inner_m">
                              <div class="row">
                              	<div class="col-lg-6 col-md-12 col-xs-12 col-sm-12">
                                	<div class="row">
                                   	 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                        <label for="u1" class="text-left"> 
                                            Upload File
                                        </label> &nbsp;
                                          <label style="margin-top:0px; padding-top:0px;"> Logo/<a href="javascript:void(0)" onClick="popUpScan();">  Scan </a> </label>
                                    </div>	
                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-left">
                                        <input class="form-control" type="file" name="elem_surgeryCenterLogo" id="elem_surgeryCenterLogo"/> 
                                    </div>	 
                                    </div>	
                                </div>
                                <div class="col-lg-6 col-md-12 col-xs-12 col-sm-12">
                                	 <div class="row">
                                         <div class="col-md-6 col-lg-12 col-xs-12 col-sm-12">
                                          <div class="upload_inner"  style="padding-top:0px;">
     
                                             
                                               <?php if($surgeryCenterLogo && $logoName<>'no-file.jpg'){?>
                                                   <div class="media well well-sm" style="padding:1px; margin-bottom:0px">
                                                         <div class="media-object pull-left">
                                                             <?php 
                                                                if($logoName<>'no-file.jpg') {?>
                                                                    <img class="thumbnail" id="imgThumbNail" style="margin-bottom:0px;cursor:pointer;" src="logoImg.php?from=surgery_center" onClick="return ShowDiv(this);">
                                                            <?php	
                                                                }?>
                                                         </div>
                                                         <?php 
                                                         if($logoName<>'no-file.jpg'){?>
                                                             <div class="media-body">
                                                                <div class="">
                                                                    <a  style="margin-top:10px;" href="javascript:void(0)" onClick="deleteSurgeryCenterLogo();" class="btn btn-danger btn-sm"> <i class="fa fa-trash"> Delete </i>	</a>
                                                                </div>
                                                             </div>
                                                         <?php
                                                         }
                                                         ?>
                                                    </div>
                                                <?php
                                               }
                                                ?>
                                          </div>
                                       </div>
                                     </div>
                                </div>
                                   
                                    
                                    
                               </div> 		
                        </div>
                </div>         
                
                <div class="clearfix "></div>
                <div class="clearfix border-dashed"></div>
                
                <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12 for_sm_font">
                  <div class="row">
                  	
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  		<label >Enable Vital Sign Grid</label>
                   	</div>
                    
                  	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    	<div class="row">
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                        	Operating Room Record
                       	</div>
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                          <label for="elem_oproom_Y" class="f_size" > 
                            <input type="checkbox" name="elem_oproom" id="elem_oproom_Y" onClick="javascript:checkSingleAdmin('elem_oproom_Y','elem_oproom');"  value="Y"  <?php echo (($oproom == "Y"))  ? "checked" : "" ;?> />  Yes		
                          </label>
                          &nbsp;
                          <label for="elem_oproom_N" class="f_size"> 
                            <input type="checkbox" name="elem_oproom" id="elem_oproom_N" onClick="javascript:checkSingleAdmin('elem_oproom_N','elem_oproom');" value="N" <?php echo ($oproom == "N") ? "checked" : "" ;?> />  No	
                          </label>
                        </div>
                     	</div>   
                    </div>
                      
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    	<div class="row">
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                          Mac/Regional Anesthesia
                       	</div>
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                          <label for="elem_macAnes_Y" class="f_size" > 
                            <input type="checkbox" name="elem_macAnes" id="elem_macAnes_Y" onClick="javascript:checkSingleAdmin('elem_macAnes_Y','elem_macAnes');"  value="Y"  <?php echo (($macAnes == "Y"))  ? "checked" : "" ;?> />  Yes		
                          </label>
                          &nbsp;
                          <label for="elem_macAnes_N" class="f_size"> 
                            <input type="checkbox" name="elem_macAnes" id="elem_macAnes_N" onClick="javascript:checkSingleAdmin('elem_macAnes_N','elem_macAnes');" value="N" <?php echo ($macAnes == "N") ? "checked" : "" ;?> />  No	
                          </label>
                        </div>
                      </div>
                   	</div>
                      
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    	<div class="row">
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                            General Anesthesia
                        </div>
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                          <label for="elem_macAnes_Y" class="f_size" > 
                            <input type="checkbox" name="elem_genAnes" id="elem_genAnes_Y" onClick="javascript:checkSingleAdmin('elem_genAnes_Y','elem_genAnes');"  value="Y"  <?php echo (($genAnes == "Y"))  ? "checked" : "" ;?> />  Yes		
                          </label>
                          &nbsp;
                          <label for="elem_genAnes_N" class="f_size"> 
                            <input type="checkbox" name="elem_genAnes" id="elem_genAnes_N" onClick="javascript:checkSingleAdmin('elem_genAnes_N','elem_genAnes');" value="N" <?php echo ($genAnes == "N") ? "checked" : "" ;?> />  No	
                          </label>
                        </div>
                     	</div>  
                    </div>
                      
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    	<div class="row">
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                            Transfer & Followups
                        </div>
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                          <label for="elem_transferFollowup_Y" class="f_size" > 
                            <input type="checkbox" name="elem_transferFollowup" id="elem_transferFollowup_Y" onClick="javascript:checkSingleAdmin('elem_transferFollowup_Y','elem_transferFollowup');"  value="Y"  <?php echo (($transferFollowup == "Y"))  ? "checked" : "" ;?> />  Yes		
                          </label>
                          &nbsp;
                          <label for="elem_transferFollowup_N" class="f_size"> 
                            <input type="checkbox" name="elem_transferFollowup" id="elem_transferFollowup_N" onClick="javascript:checkSingleAdmin('elem_transferFollowup_N','elem_transferFollowup');" value="N" <?php echo ($transferFollowup == "N") ? "checked" : "" ;?> />  No	
                          </label>
                        </div>
                      </div> 
                    </div>
                
                  </div>
                </div>
              	
                
                <div class="clearfix hidden-lg hidden-md"></div>
                
                <div class="col-lg-4 col-md-2 col-sm-12 col-xs-12">

                  <div class="row" style="border-left:1px dashed #333; min-height:70px;">
                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 " >
                      <label for="yes" class="text-left"> 
                        Vital Sign Time Slot
                      </label>
                    </div>
                    
                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 " >
                      <label for="yes" class="text-left"> 
                        Peer Review
                      </label>
                    </div>
										<div class="col-md-4 col-lg-4 col-xs-4 col-sm-4  " >
                      <label for="yes" class="text-left"> 
                        Fire Risk Analysis
                      </label>
                    </div>
                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 ">
                    	<select name="elem_vital_time_slot" id="elem_vital_time_slot" class="selectpicker" data-width="100%">
                      	<option value="0" <?php echo ($vital_time_slot == 0 ? 'selected':'');?>  >Current Time</option>
                        <option value="5" <?php echo ($vital_time_slot == 5 ? 'selected':'');?>>5&nbsp;Minutes&nbsp;Interval</option>
                     	</select>  
                    </div>
                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 ">
                      <label for="elem_peer_review_Y" class="f_size" > 
                        <input type="checkbox" name="elem_peer_review" id="elem_peer_review_Y" onClick="javascript:checkSingleAdmin('elem_peer_review_Y','elem_peer_review');"  value="Y"  <?php echo (($peer_review == "Y") )  ? "checked" : "" ;?> />  Yes		
                      </label>
                      &nbsp;
                      <label for="elem_peer_review_N" class="f_size"> 
                        <input type="checkbox" name="elem_peer_review" id="elem_peer_review_N" onClick="javascript:checkSingleAdmin('elem_peer_review_N','elem_peer_review');" value="N" <?php echo ($peer_review == "N") ? "checked" : "" ;?> />  No	
                      </label>
                    </div>
										<div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 ">
                      <label for="elem_fire_risk_analysis_Y" class="f_size" > 
                        <input type="checkbox" name="elem_fire_risk_analysis" id="elem_fire_risk_analysis_Y" onClick="javascript:checkSingleAdmin('elem_fire_risk_analysis_Y','elem_fire_risk_analysis');"  value="Y"  <?php echo (($fire_risk_analysis == "Y") )  ? "checked" : "" ;?> />  Yes		
                      </label>
                      &nbsp;
                      <label for="elem_fire_risk_analysis_N" class="f_size"> 
                        <input type="checkbox" name="elem_fire_risk_analysis" id="elem_fire_risk_analysis_N" onClick="javascript:checkSingleAdmin('elem_fire_risk_analysis_N','elem_fire_risk_analysis');" value="N" <?php echo ($fire_risk_analysis == "N") ? "checked" : "" ;?> />  No	
                      </label>
                    </div>
                 	</div>   
               	</div>
                
                <div class="clearfix " ></div>
                <div class="clearfix margin_top_5" ></div>
                <div class="clearfix border-dashed"></div>
                <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 for_sm_font">
                	<div class="row">
                		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <div class="form_inner_m">
                                  <div class="row">
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="yes" class="text-left"> 
                                                 ASA IV(Mac/Regional)		
                                            </label>
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="elem_asa_4_Y" class="f_size"> 
                                            	<input type="checkbox" name="elem_asa_4" id="elem_asa_4_Y" onClick="javascript:checkSingleAdmin('elem_asa_4_Y','elem_asa_4');" value="1" <?php echo ($asa_4 ? "checked" : "") ;?>/>  Yes		
                                            </label>
                                            &nbsp; 
                                            
                                            <label for="elem_asa_4_N" class="f_size"> 
                                            <input type="checkbox" name="elem_asa_4" id="elem_asa_4_N" onClick="javascript:checkSingleAdmin('elem_asa_4_N','elem_asa_4');" value="0" <?php echo ($asa_4) ? "" : "checked";?> />  No	
                                            </label>
                                        </div>
                                        
                                   </div> 		
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <div class="form_inner_m">
                                  <div class="row">
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="yes" class="text-left"> 
                                                 Mallampetti Score(Mac/Regional)		
                                            </label>
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="elem_anes_mallampetti_score_Y" class="f_size"> 
                                            	<input type="checkbox" name="elem_anes_mallampetti_score" id="elem_anes_mallampetti_score_Y" onClick="javascript:checkSingleAdmin('elem_anes_mallampetti_score_Y','elem_anes_mallampetti_score');" value="1" <?php echo ($anes_mallampetti_score ? "checked" : "") ;?>/>  Yes		
                                            </label>
                                            &nbsp; 
                                            <label for="elem_anes_mallampetti_score_N" class="f_size"> 
                                            <input type="checkbox" name="elem_anes_mallampetti_score" id="elem_anes_mallampetti_score_N" onClick="javascript:checkSingleAdmin('elem_anes_mallampetti_score_N','elem_anes_mallampetti_score');" value="0" <?php echo ($anes_mallampetti_score) ? "" : "checked";?> />  No	
                                            </label>
                                        </div>
                                        
                                   </div> 		
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <div class="form_inner_m">
                                  <div class="row">
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="yes" class="text-left"> 
                                                 Show Religion <small class="pull-right" style="width:auto;margin-top:5px;" >In Check in screen</small>
                                            </label>
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="elem_show_religion_Y" class="f_size"> 
                                            	<input type="checkbox" name="elem_show_religion" id="elem_show_religion_Y" onClick="javascript:checkSingleAdmin('elem_show_religion_Y','elem_show_religion');" value="1" <?php echo ($show_religion ? "checked" : "") ;?>/>  Yes		
                                            </label>
                                            &nbsp; 
                                            <label for="elem_show_religion_N" class="f_size"> 
                                            <input type="checkbox" name="elem_show_religion" id="elem_show_religion_N" onClick="javascript:checkSingleAdmin('elem_show_religion_N','elem_show_religion');" value="0" <?php echo ($show_religion) ? "" : "checked";?> />  No	
                                            </label>
                                        </div>
                                        
                                   </div> 		
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <div class="form_inner_m">
                                  <div class="row">
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="yes" class="text-left"> 
                                                 Safety Check List
                                            </label>
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="elem_safety_check_list_Y" class="f_size"> 
                                            	<input type="checkbox" name="elem_safety_check_list" id="elem_safety_check_list_Y" onClick="javascript:checkSingleAdmin('elem_safety_check_list_Y','elem_safety_check_list');" value="1" <?php echo ($safety_check_list ? "checked" : "") ;?>/>  Yes		
                                            </label>
                                            &nbsp; 
                                            <label for="elem_safety_check_list_N" class="f_size"> 
                                            <input type="checkbox" name="elem_safety_check_list" id="elem_safety_check_list_N" onClick="javascript:checkSingleAdmin('elem_safety_check_list_N','elem_safety_check_list');" value="0" <?php echo ($safety_check_list) ? "" : "checked";?> />  No	
                                            </label>
                                        </div>
                                        
                                   </div> 		
                            </div>
                        </div>
					</div>                
                </div>
                <div class="clearfix hidden-lg hidden-md"></div>
                <div class="col-lg-4 col-md-2 col-sm-12 col-xs-12">
                	<div class="row">
                		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="form_inner_m">
                                  <div class="row">
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="yes" class="text-left"> 
                                                 Autofill Modifiers	<small class="pull-right" style="width:auto;margin-top:5px;" >In Discharge Summary</small>
                                            </label>
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="elem_autofill_modifiers_Y" class="f_size"> 
                                            	<input type="checkbox" name="elem_autofill_modifiers" id="elem_autofill_modifiers_Y" onClick="javascript:checkSingleAdmin('elem_autofill_modifiers_Y','elem_autofill_modifiers');" value="1" <?php echo ($autofill_modifiers ? "checked" : "") ;?>/>  Yes		
                                            </label>
                                            &nbsp; 
                                            
                                            <label for="elem_autofill_modifiers_N" class="f_size"> 
                                            <input type="checkbox" name="elem_autofill_modifiers" id="elem_autofill_modifiers_N" onClick="javascript:checkSingleAdmin('elem_autofill_modifiers_N','elem_autofill_modifiers');" value="0" <?php echo ($autofill_modifiers) ? "" : "checked";?> />  No	
                                            </label>
                                        </div>
                                        
                                   </div> 		
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        </div>
                	</div>	
                </div>

                <div class="clearfix " ></div>
                <div class="clearfix margin_top_5" ></div>
                <div class="clearfix border-dashed"></div>
                <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 for_sm_font">
                	<div class="row">
                		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <div class="form_inner_m">
                                  <div class="row">
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="yes" class="text-left"> 
                                                 Sx Plan Sheet Review	<small class="pull-right" style="width:auto;margin-top:5px;" >In Operating Room</small>
                                            </label>
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <label for="elem_sx_plan_sheet_review_Y" class="f_size"> 
                                                <input type="checkbox" name="elem_sx_plan_sheet_review" id="elem_sx_plan_sheet_review_Y" onClick="javascript:checkSingleAdmin('elem_sx_plan_sheet_review_Y','elem_sx_plan_sheet_review');" value="1" <?php echo ($sx_plan_sheet_review ? "checked" : "") ;?>/>  Yes		
                                            </label>
                                            &nbsp; 
                                            
                                            <label for="elem_sx_plan_sheet_review_N" class="f_size"> 
                                            <input type="checkbox" name="elem_sx_plan_sheet_review" id="elem_sx_plan_sheet_review_N" onClick="javascript:checkSingleAdmin('elem_sx_plan_sheet_review_N','elem_sx_plan_sheet_review');" value="0" <?php echo ($sx_plan_sheet_review) ? "" : "checked";?> />  No	
                                            </label>
                                        </div>
                                        
                                   </div> 		
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                        </div>
					</div>                
                </div>
                <div class="clearfix hidden-lg hidden-md"></div>
                <div class="col-lg-4 col-md-2 col-sm-12 col-xs-12">
                </div>

                <div class="clearfix "></div>
                <div class="clearfix border-dashed"></div>
                
                <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12 for_sm_font">
                  <div class="row">
                  	
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  		<label >Hybrent SFTP Credentials</label>
                   	</div>
                    
                  	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    	<div class="row">
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                        	Host Name
                       	</div>
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                          <input autocomplete="off" class="form-control" type="text" id="elem_suppliesHostName" name="elem_suppliesHostName" value="<?php echo $suppliesHostName; ?>" />
                        </div>
                     	</div>   
                    </div>
                      
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    	<div class="row">
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                          Port Number
                       	</div>
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                          <input autocomplete="off" class="form-control" type="text" id="elem_suppliesPortNumber" name="elem_suppliesPortNumber" value="<?php echo $suppliesPortNumber; ?>" />
                        </div>
                      </div>
                   	</div>
                      
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    	<div class="row">
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                            Username
                        </div>
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                          <input autocomplete="off" class="form-control" type="text" id="elem_suppliesUsername" name="elem_suppliesUsername" value="<?php echo $suppliesUsername; ?>" />
                        </div>
                     	</div>  
                    </div>
                      
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    	<div class="row">
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                            Password
                        </div>
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                          <input autocomplete="off" class="form-control" type="password" id="elem_suppliesPassword" name="elem_suppliesPassword" value="<?php echo $suppliesPassword; ?>" />
                        </div>
                      </div> 
                    </div>
                
                  </div>
                </div>
                <div class="clearfix hidden-lg hidden-md"></div>
                <div class="col-lg-4 col-md-2 col-sm-12 col-xs-12">
                	<div class="row">
                    	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            &nbsp;
                        </div>
                		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="form_inner_m">
                                  <div class="row">
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            File Path From SFTP
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <input autocomplete="off" class="form-control" type="text" id="elem_suppliesPathFromSftp" name="elem_suppliesPathFromSftp" value="<?php echo $suppliesPathFromSftp; ?>" />
                                        </div>
                                        
                                   </div> 		
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="form_inner_m">
                                  <div class="row">
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            Directory Path To SFTP
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            <input autocomplete="off" class="form-control" type="text" id="elem_suppliesPathToSftp" name="elem_suppliesPathToSftp" value="<?php echo $suppliesPathToSftp; ?>" />
                                        </div>
                                        
                                   </div> 		
                            </div>
                        </div>
                	</div>	
                </div>
                
                <div class="clearfix " ></div>
                <div class="clearfix margin_top_5" ></div>
                <div class="clearfix border-dashed"></div>
                <!-- -->
               <div class="wrap_2_row"> 
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label for="HIPAA – Compliancy" class="text-left"> 
                                         HIPAA – Compliancy		
                                    </label>
                                </div>
                                
                                  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                 
                                    <div class="row">                            
                                      
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      <Div class="page_form padding_0">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="elem_maxRecentlyUsedPass" class="sub_label">
                                                    Maximum recently used password
                                              </label>
                                            </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                                <input class="form-control" type="text" id="elem_maxRecentlyUsedPass" name="elem_maxRecentlyUsedPass" value="<?php if($maxRecentlyUsedPass) echo $maxRecentlyUsedPass; else echo "5"; ?>" onChange="checkRecentlyUsedPass(this)"/>
                                          </Div>							                                                  
                                        </Div>	
                                     
                                       </Div>  	
                                      </div>
                                      
                                      <!-- Col ends -->                                                  
                                      
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      <Div class="page_form padding_0">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="elem_maxPassExpiresDays" class="sub_label">
                                                    Maximum days before password expires
                                              </label>
                                        
                                          </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                                <input class="form-control" type="text" id="elem_maxPassExpiresDays" name="elem_maxPassExpiresDays" value="<?php if($maxPassExpiresDays) echo $maxPassExpiresDays; else echo "90"; ?>" onBlur="checkPassExpiresDays(this)"/>
                                                
                                          </Div>							                                                  
                                        </Div>	
                                      </div>
                                      </div>
                                      <!-- Col ends -->
                                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                      <Div class="page_form padding_0">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="elem_ascId" class="sub_label">
                                                    ASC#
                                              </label>
                                        
                                          </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12 text-left">    
                                                <input class="form-control" type="text" id="elem_ascId" name="elem_ascId" value="<?php echo $ascId_present; ?>" />
                                          </Div>							                                                  
                                        </Div>	
                                      </div>
                                     </div> 
                                      <Div class="clearfix"></Div>
                                      <!-- Col ends -->
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      <Div class="page_form">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="elem_maxLoginAttempts" class="sub_label">
                                                    Maximum login attempts:
                                              </label>
                                        
                                          </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                                <input class="form-control" type="text" id="elem_maxLoginAttempts" name="elem_maxLoginAttempts" value="<?php if($maxLoginAttempts) echo $maxLoginAttempts; else echo "3"; ?>" onChange="checkLoginAttempt(this);"/>
                                          </Div>							                                                  
                                        </Div>	
                                      </div>
                                     </div>
                                     
                                     <!--  Col Ends--> 
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      <Div class="page_form">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="elem_finalizeWarningDays" class="sub_label">
                                                   Finalize Warning Days:
                                              </label>
                                        
                                          </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                                <select class="selectpicker form-control" id="elem_finalizeWarningDays" name="elem_finalizeWarningDays">
                                                  <option value="" <?php echo ($finalizeWarningDays == "") ? "selected" : "" ;?>> Select</option>
                                                  <option value="1" <?php echo ($finalizeWarningDays == "1") ? "selected" : "" ;?> >1</option>
                                                  <option value="2" <?php echo ($finalizeWarningDays == "2") ? "selected" : "" ;?> >2</option>
                                                </select>
                                          </Div>							                                                  
                                        </Div>	
                                      </div>
                                     </div>
                                     
                                     <!--  Col Ends--> 
                                      
                                     <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      <Div class="page_form">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="elem_finalizeDays" class="sub_label">
                                                   Finalize Days:
                                              </label>
                                        
                                          </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12 text-left">    
                                                <select name="elem_finalizeDays" class="selectpicker form-control">
                                                  
                                                <?php
                                                for($i=1;$i<=30;$i++) {
                                                ?>
                                                    <option value="<?php echo $i;?>" <?php if($finalizeDays==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                <?php
                                                }
                                                ?>
                                                </select>
                                          </Div>							                                                  
                                        </Div>	
                                      </div>
                                     </div>
                                     
                                     <!--  Col Ends--> 
                                      
                                  </div>   
                                </div>
                                
                            </div>
                        </div>
                    </div>	
                </Div>	 <!-- wrpa row 2-->	
                 <div class="clearfix "></div><!--border-dashed-->
                 <div class="wrap_2_row"> 
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label for="HIPAA – Compliancy" class="text-left"> 
                                         Labels Size		
                                    </label>
                                </div>
                                
                                  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                 
                                    <div class="row">                            
                                      
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      <Div class="page_form padding_0">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="large_top" class="sub_label">
                                                    Large label top margin:
                                              </label>
                                        
                                          </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                                <input class="form-control" type="text" id="large_top" name="large_top" value="<?php echo $large_top;?>" />
                                          </Div>							                                                  
                                        </Div>	
                                     
                                       </Div>  	
                                      </div>
                                      
                                      <!-- Col ends -->  
                                      
                                       <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      <Div class="page_form padding_0">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="large_bottom" class="sub_label">
                                                    Large label between margin:
                                              </label>
                                        
                                          </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                                <input class="form-control" type="text" id="large_bottom" name="large_bottom" value="<?php echo $large_bottom;?>" />
                                          </Div>							                                                  
                                        </Div>	
                                     
                                       </Div>  	
                                      </div>
                                      
                                      <!-- Col ends -->  
                                     <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      <Div class="page_form padding_0">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="large_inner" class="sub_label">
                                                    Large label line margin:
                                              </label>
                                            </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                                <input class="form-control" type="text" id="large_inner" name="large_inner" value="<?php echo $large_inner;?>" />
                                          </Div>							                                                  
                                        </Div>	
                                     
                                       </Div>  	
                                      </div>
                                      
                                      <!-- Col ends -->                                                
                                     
                                     
                                     
                                    
                                      
                                  </div>   
                                </div>
                                 <Div class="clearfix"></Div>
                               
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                 
                                    <div class="row">                            
                                      
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      <Div class="page_form ">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="small_top" class="sub_label">
                                                    Small label top margin:
                                              </label>
                                        
                                           </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                                <input class="form-control" type="text" id="small_top" name="small_top" value="<?php echo $small_top;?>" />
                                          </Div>							                                                  
                                        </Div>	
                                     
                                       </Div>  	
                                      </div>
                                      
                                      <!-- Col ends -->  
                                      
                                       <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      <Div class="page_form ">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="small_bottom" class="sub_label">
                                                    Small label between margin:
                                              </label>
                                        
                                          </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                                <input class="form-control" type="text" id="small_bottom" name="small_bottom" value="<?php echo $small_bottom;?>" />
                                          </Div>							                                                  
                                        </Div>	
                                     
                                       </Div>  	
                                      </div>
                                      
                                      <!-- Col ends -->  
                                     <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      <Div class="page_form ">
                                        <Div class="row">
                                           <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                              <label for="small_inner" class="sub_label">
                                                    Small label line margin:
                                              </label>
                                            </Div>	
                                            <Div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">    
                                                <input class="form-control" type="text" id="small_inner" name="small_inner" value="<?php echo $small_inner;?>" />
                                          </Div>							                                                  
                                        </Div>	
                                     
                                       </Div>  	
                                      </div>
                                      
                                      <!-- Col ends -->                                                
                                     
                                     
                                     
                                     <Div class="clearfix"></Div>
                                    
                                      
                                  </div>   
                                </div>
                                
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label >Enable Labels</label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                 
                                    <div class="row">                            
                                      
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                                Small label show surgeon
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                                Small label show procedure
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                                Small label show site
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                              <label for="elem_small_label_enable_surgeon_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_small_label_enable_surgeon" id="elem_small_label_enable_surgeon_Y" onClick="javascript:checkSingleAdmin('elem_small_label_enable_surgeon_Y','elem_small_label_enable_surgeon');"  value="Y"  <?php echo (($small_label_enable_surgeon == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_small_label_enable_surgeon_N" class="f_size"> 
                                                <input type="checkbox" name="elem_small_label_enable_surgeon" id="elem_small_label_enable_surgeon_N" onClick="javascript:checkSingleAdmin('elem_small_label_enable_surgeon_N','elem_small_label_enable_surgeon');" value="N" <?php echo ($small_label_enable_surgeon == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                              <label for="elem_small_label_enable_procedure_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_small_label_enable_procedure" id="elem_small_label_enable_procedure_Y" onClick="javascript:checkSingleAdmin('elem_small_label_enable_procedure_Y','elem_small_label_enable_procedure');"  value="Y"  <?php echo (($small_label_enable_procedure == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_small_label_enable_procedure_N" class="f_size"> 
                                                <input type="checkbox" name="elem_small_label_enable_procedure" id="elem_small_label_enable_procedure_N" onClick="javascript:checkSingleAdmin('elem_small_label_enable_procedure_N','elem_small_label_enable_procedure');" value="N" <?php echo ($small_label_enable_procedure == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                              <label for="elem_small_label_enable_site_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_small_label_enable_site" id="elem_small_label_enable_site_Y" onClick="javascript:checkSingleAdmin('elem_small_label_enable_site_Y','elem_small_label_enable_site');"  value="Y"  <?php echo (($small_label_enable_site == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_small_label_enable_site_N" class="f_size"> 
                                                <input type="checkbox" name="elem_small_label_enable_site" id="elem_small_label_enable_site_N" onClick="javascript:checkSingleAdmin('elem_small_label_enable_site_N','elem_small_label_enable_site');" value="N" <?php echo ($small_label_enable_site == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                        </div>   
                                      </div>
                                      
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                Small label show patient MRN
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                 Small label show patient gender
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                              <label for="elem_small_label_enable_patient_mrn_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_small_label_enable_patient_mrn" id="elem_small_label_enable_patient_mrn_Y" onClick="javascript:checkSingleAdmin('elem_small_label_enable_patient_mrn_Y','elem_small_label_enable_patient_mrn');"  value="Y"  <?php echo (($small_label_enable_patient_mrn == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_small_label_enable_patient_mrn_N" class="f_size"> 
                                                <input type="checkbox" name="elem_small_label_enable_patient_mrn" id="elem_small_label_enable_patient_mrn_N" onClick="javascript:checkSingleAdmin('elem_small_label_enable_patient_mrn_N','elem_small_label_enable_patient_mrn');" value="N" <?php echo ($small_label_enable_patient_mrn == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                              <label for="elem_small_label_enable_patient_gender_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_small_label_enable_patient_gender" id="elem_small_label_enable_patient_gender_Y" onClick="javascript:checkSingleAdmin('elem_small_label_enable_patient_gender_Y','elem_small_label_enable_patient_gender');"  value="Y"  <?php echo (($small_label_enable_patient_gender == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_small_label_enable_patient_gender_N" class="f_size"> 
                                                <input type="checkbox" name="elem_small_label_enable_patient_gender" id="elem_small_label_enable_patient_gender_N" onClick="javascript:checkSingleAdmin('elem_small_label_enable_patient_gender_N','elem_small_label_enable_patient_gender');" value="N" <?php echo ($small_label_enable_patient_gender == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                        </div>   
                                      </div>
                                      
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                Small label show patient DOS
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                Small label show patient DOB
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                              <label for="elem_small_label_enable_patient_dos_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_small_label_enable_patient_dos" id="elem_small_label_enable_patient_dos_Y" onClick="javascript:checkSingleAdmin('elem_small_label_enable_patient_dos_Y','elem_small_label_enable_patient_dos');"  value="Y"  <?php echo (($small_label_enable_patient_dos == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_small_label_enable_patient_dos_N" class="f_size"> 
                                                <input type="checkbox" name="elem_small_label_enable_patient_dos" id="elem_small_label_enable_patient_dos_N" onClick="javascript:checkSingleAdmin('elem_small_label_enable_patient_dos_N','elem_small_label_enable_patient_dos');" value="N" <?php echo ($small_label_enable_patient_dos == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                              <label for="elem_small_label_enable_patient_dob_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_small_label_enable_patient_dob" id="elem_small_label_enable_patient_dob_Y" onClick="javascript:checkSingleAdmin('elem_small_label_enable_patient_dob_Y','elem_small_label_enable_patient_dob');"  value="Y"  <?php echo (($small_label_enable_patient_dob == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_small_label_enable_patient_dob_N" class="f_size"> 
                                                <input type="checkbox" name="elem_small_label_enable_patient_dob" id="elem_small_label_enable_patient_dob_N" onClick="javascript:checkSingleAdmin('elem_small_label_enable_patient_dob_N','elem_small_label_enable_patient_dob');" value="N" <?php echo ($small_label_enable_patient_dob == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                        </div>   
                                      </div>                                               
                                     
                                     
                                     
                                     <Div class="clearfix"></Div>
                                    
                                      
                                  </div>   
                                </div>
                                <Div class="clearfix"></Div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">&nbsp;</div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                 
                                    <div class="row">                            
                                      
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                                Large label show surgeon
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                                Large label show procedure
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                                Large label show site
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                              <label for="elem_large_label_enable_surgeon_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_large_label_enable_surgeon" id="elem_large_label_enable_surgeon_Y" onClick="javascript:checkSingleAdmin('elem_large_label_enable_surgeon_Y','elem_large_label_enable_surgeon');"  value="Y"  <?php echo (($large_label_enable_surgeon == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_large_label_enable_surgeon_N" class="f_size"> 
                                                <input type="checkbox" name="elem_large_label_enable_surgeon" id="elem_large_label_enable_surgeon_N" onClick="javascript:checkSingleAdmin('elem_large_label_enable_surgeon_N','elem_large_label_enable_surgeon');" value="N" <?php echo ($large_label_enable_surgeon == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                              <label for="elem_large_label_enable_procedure_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_large_label_enable_procedure" id="elem_large_label_enable_procedure_Y" onClick="javascript:checkSingleAdmin('elem_large_label_enable_procedure_Y','elem_large_label_enable_procedure');"  value="Y"  <?php echo (($large_label_enable_procedure == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_large_label_enable_procedure_N" class="f_size"> 
                                                <input type="checkbox" name="elem_large_label_enable_procedure" id="elem_large_label_enable_procedure_N" onClick="javascript:checkSingleAdmin('elem_large_label_enable_procedure_N','elem_large_label_enable_procedure');" value="N" <?php echo ($large_label_enable_procedure == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                            <div class="col-md-12 col-lg-4 col-xs-12 col-sm-12">
                                              <label for="elem_large_label_enable_site_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_large_label_enable_site" id="elem_large_label_enable_site_Y" onClick="javascript:checkSingleAdmin('elem_large_label_enable_site_Y','elem_large_label_enable_site');"  value="Y"  <?php echo (($large_label_enable_site == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_large_label_enable_site_N" class="f_size"> 
                                                <input type="checkbox" name="elem_large_label_enable_site" id="elem_large_label_enable_site_N" onClick="javascript:checkSingleAdmin('elem_large_label_enable_site_N','elem_large_label_enable_site');" value="N" <?php echo ($large_label_enable_site == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                        </div>   
                                      </div>
                                      
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                Large label show patient MRN
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                Large label show patient gender
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                              <label for="elem_large_label_enable_patient_mrn_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_large_label_enable_patient_mrn" id="elem_large_label_enable_patient_mrn_Y" onClick="javascript:checkSingleAdmin('elem_large_label_enable_patient_mrn_Y','elem_large_label_enable_patient_mrn');"  value="Y"  <?php echo (($large_label_enable_patient_mrn == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_large_label_enable_patient_mrn_N" class="f_size"> 
                                                <input type="checkbox" name="elem_large_label_enable_patient_mrn" id="elem_large_label_enable_patient_mrn_N" onClick="javascript:checkSingleAdmin('elem_large_label_enable_patient_mrn_N','elem_large_label_enable_patient_mrn');" value="N" <?php echo ($large_label_enable_patient_mrn == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                              <label for="elem_large_label_enable_patient_gender_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_large_label_enable_patient_gender" id="elem_large_label_enable_patient_gender_Y" onClick="javascript:checkSingleAdmin('elem_large_label_enable_patient_gender_Y','elem_large_label_enable_patient_gender');"  value="Y"  <?php echo (($large_label_enable_patient_gender == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_large_label_enable_patient_gender_N" class="f_size"> 
                                                <input type="checkbox" name="elem_large_label_enable_patient_gender" id="elem_large_label_enable_patient_gender_N" onClick="javascript:checkSingleAdmin('elem_large_label_enable_patient_gender_N','elem_large_label_enable_patient_gender');" value="N" <?php echo ($large_label_enable_patient_gender == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                        </div>   
                                      </div>
                                      
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                Large label show patient DOS
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                Large label show patient DOB
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                              <label for="elem_large_label_enable_patient_dos_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_large_label_enable_patient_dos" id="elem_large_label_enable_patient_dos_Y" onClick="javascript:checkSingleAdmin('elem_large_label_enable_patient_dos_Y','elem_large_label_enable_patient_dos');"  value="Y"  <?php echo (($large_label_enable_patient_dos == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_large_label_enable_patient_dos_N" class="f_size"> 
                                                <input type="checkbox" name="elem_large_label_enable_patient_dos" id="elem_large_label_enable_patient_dos_N" onClick="javascript:checkSingleAdmin('elem_large_label_enable_patient_dos_N','elem_large_label_enable_patient_dos');" value="N" <?php echo ($large_label_enable_patient_dos == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                              <label for="elem_large_label_enable_patient_dob_Y" class="f_size" > 
                                                <input type="checkbox" name="elem_large_label_enable_patient_dob" id="elem_large_label_enable_patient_dob_Y" onClick="javascript:checkSingleAdmin('elem_large_label_enable_patient_dob_Y','elem_large_label_enable_patient_dob');"  value="Y"  <?php echo (($large_label_enable_patient_dob == "Y"))  ? "checked" : "" ;?> />  Yes		
                                              </label>
                                              &nbsp;
                                              <label for="elem_large_label_enable_patient_dob_N" class="f_size"> 
                                                <input type="checkbox" name="elem_large_label_enable_patient_dob" id="elem_large_label_enable_patient_dob_N" onClick="javascript:checkSingleAdmin('elem_large_label_enable_patient_dob_N','elem_large_label_enable_patient_dob');" value="N" <?php echo ($large_label_enable_patient_dob == "N") ? "checked" : "" ;?> />  No	
                                              </label>
                                            </div>
                                        </div>   
                                      </div>                                               
                                     
                                     
                                     
                                     <Div class="clearfix"></Div>
                                    
                                      
                                  </div>   
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="clearfix margin_top_5" ></div>
                    <div class="clearfix border-dashed"></div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label>Discharge Summary Disclaimer</label>
                        <textarea class="form-control" name="elem_discharge_disclaimer" id="elem_discharge_disclaimer" ><?php echo $discharge_disclaimer;?></textarea>
                    </div>
                    <Div class="clearfix">&nbsp;</Div>
                   
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="op_right_main">
                            <textarea name="editor1" id="editor1" rows="" cols="" ><?php echo $loginLegalNotice;?></textarea>
                            <script>CKEDITOR.replace( 'editor1' );</script>
                        </div>
                    </div>	
                </Div>	 <!-- wrpa row 2-->		     
          </Div>
          <!-- NEcessary PUSH     -->	 
          <Div class="push"></Div>
          <!-- NEcessary PUSH     -->
        </div>
	</form>
</div>

<script>
setTimeout('imageZizeManage()', 1000);
function imageZizeManage(){
   if(document.getElementById('imgThumbNail')){
	var target = 100;
	var imgHeight = document.getElementById('imgThumbNail').height;
	var imgWidth = document.getElementById('imgThumbNail').width;
	
	if((imgHeight>=250) || (imgWidth>=250)){
		if (imgWidth > imgHeight) { 
			percentage = (target/imgWidth); 
		} else { 
			percentage = (target/imgHeight);
		} 
		widthNew = imgWidth*percentage; 
		heightNew = imgHeight*percentage; 	
		document.getElementById('imgThumbNail').height = heightNew;
		document.getElementById('imgThumbNail').width = widthNew;
	}
  } 
}

</script>
</body>
</html>