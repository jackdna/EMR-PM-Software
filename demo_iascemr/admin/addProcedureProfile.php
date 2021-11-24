<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<script>
var preDefineCloseOut;
function preDefineOpenCloseFun() {
	document.getElementById("hiddPreDefineId").value = "preDefineOpenYes";
}
function preCloseFun(Id) {
	if(document.getElementById("hiddPreDefineId")) {
		if(document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
			if(document.getElementById(Id)) {
				if(document.getElementById(Id).style.display == "block"){
					document.getElementById(Id).style.display = "none"; 
					//document.getElementById("hiddPreDefineId").value = "";
				}
			}
			if(top.frames[0].frames[0].document.getElementById(Id)) {
				if(top.frames[0].frames[0].document.getElementById(Id).style.display == "block"){
					//top.frames[0].frames[0].document.getElementById(Id).style.display = "none"; 
					//top.frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
				}
			}
		}
		
	}
	top.frames[0].frames[0].$('.modal.in').modal('hide');
}

function showPreOpMediDiv(name1, name2, name3,mediID, c, posLeft, posTop)
{
	top.frames[0].frames[0].$('.modal.in').modal('hide');
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	document.getElementById("tertiaryValues").value = name3;
	document.getElementById("mediID").value = mediID;
	document.getElementById("mediCatID").value = 'preOpOrdMed_cat';
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	if(document.getElementById("hiddPreDefineId")) {
		document.getElementById("hiddPreDefineId").value = "";
		preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
	}
		
	top.frames[0].frames[0].$('#ProcPrefPreOpMediOrderDiv').modal({
		show: true,
		backdrop: true,
		keyboard: true
	});
}

function showIntraOpPostOpOrderAdminFn(name1, name2, c, posLeft, posTop){	

	top.frames[0].frames[0].$('#intraOpPostOpAdminDiv').modal({
		show: true,
		backdrop: true,
		keyboard: true
	});	
		
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
}

//SHOW POST OP DROP PREDEFINE DIV
function showPostOpDropsAdminFn(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].$('.modal.in').modal('hide');
	top.frames[0].frames[0].$('#evaluationPostOpDropsAdminDiv').modal({
		show: true,
		backdrop: true,
		keyboard: true
	});	
		
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
}

function showotherPreOpOrdersAdminFn(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].$('.modal.in').modal('hide');
	top.frames[0].frames[0].$('#otherPreOpOrdersAdminDiv').modal({
		show: true,
		backdrop: true,
		keyboard: true
		});	
		
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
}

function predifineCloseAdmin(id) {
	if(id=='ProcPrefPreOpMediOrderDiv')
	{
		top.frames[0].frames[0].$('#ProcPrefPreOpMediOrderDiv').modal({
		show: false,
		backdrop: true,
		keyboard: true
		});	
	}
	else
	top.frames[0].frames[0].document.getElementById(id).style.display = 'none';
	//onMouseMove="predifineCloseAdmin('evaluationPostOpDropsAdminDiv');predifineCloseAdmin('ProcPrefPreOpMediOrderDiv');"
}
//END SHOW POST OP DROP PREDEFINE DIV



$(document).ready(function()
{
		$('body').on( 'click' , '.removeMedOrder', function()
		{
				var $this	=	$(this);
				var RID	=	$this.attr('data-record-id');				//	 Record ID
				var RT		=	$this.attr('data-record-type');			//	Record Type
				var PID	=	$this.attr('data-profile-id');				//	Profile ID
				var Div		=	'#spreadSheetAjaxIdpreMed';
				
				if(RT !== '' )
				{
						var Url		=	'procedurePreferenceDelete.php' 	
						//alert(RID + '--'+ RT + '--'+ PID)
						$.ajax({
							url : Url,
							type:'POST',
							data : { 'RID' : RID , 'RT' : RT , 'PID' : PID },
							beforeSend:function(){
								$this.hide(500);
								var TD	=	$this.closest('td');
								var txt	=	"<span class='fa fa-spinner fa-pulse '></span>";
								TD.html(txt);
							},
							complete:function(){
									//$this.show(500);
							},
							success:function(data)
							{ 	
									$(Div).html(data)
							},
							error:function(res)
							{
								consle.log('ERROR');	
							}
						});
				}
		});
	
		$("#cptCode, #cptCodeA, #dxCode").click(function(e) {
            var Id	=	$(this).attr('id');				
			var Pn	=	'common_cpt_dx_profile.php';
			var PId	=	$("#profileId").val() ;
			var Dct	=	$("#DxCodeType").val();
			
			var t	=	'<?=base64_encode($tblName)?>';
			var k	=	'<?=base64_encode('id')?>';
			var url	=	Pn;
			url 	+=	'?t='+ t ;
			url 	+=	'&k='+ k ;
			url 	+=	'&pro_id='+ PId;
			url		+=	'&'+Id+'=yes';
			url		+=	(Id === 'dxCode' ) ? '&diagnosis_code_type='+Dct : '' ;
			
			var SW	=	window.screen.width ;
			var SH	=	window.screen.height;
			
			var	W	=	( SW > 1200 ) ?  1200	: SW ;
			var	H	=	W * 0.65
	
			window.open(url,'Procedure Preference Card - CPT & Dx Code','width='+W+',height='+H+',resizable=1');
		});
		
		$("#procedureConsents").click(function(){
			
			var Pn	=	'common_procedure_consents.php';
			var PId	=	$("#profileId").val() ;
			
			var t	=	'<?=base64_encode($tblName)?>';
			var k=	'<?=base64_encode('id')?>';
			var url	=	Pn;
			url 	+=	'?t='+ t ;
			url 	+=	'&k='+ k ;
			url 	+=	'&pro_id='+ PId;
			
			var SW	=	window.screen.width ;
			var SH	=	window.screen.height;
			
			var	W	=	350;
			var	H	=	450;
	
			window.open(url,'Procedure Preference Card - ProcedureConsents','width='+W+',height='+H+',resizable=1');
		});
	
});
</script>

<style>
	.btn { height:32px !important; } 
</style>



	<input type="hidden" name="profileId" id="profileId" value="<?php echo $procedureProfileId; ?>">
	<input type="hidden" name="frmName" id="frmName" value="frmProcedureProfile">
	<input type="hidden" name="procedureId" id="procedureId" value="<?php echo $procedureId; ?>">
	<input type="hidden" name="sbtSaveProcedureProfile" id="sbtSaveProcedureProfile" value="true">
	
    <input type="hidden" name="selected_frame_name_id" id="selected_frame_name_id" value="">
	<input type="hidden" name="divId" id="divId">
	<input type="hidden" name="counter" id="counter">
	<input type="hidden" name="secondaryValues" id="secondaryValues">
	<input type="hidden" name="tertiaryValues" id="tertiaryValues">
	<input type="hidden" name="mediID" id="mediID">
    <input type="hidden" name="mediCatID" id="mediCatID">
	<input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
	<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
    
    <input type="hidden" name="DxCodeType" id="DxCodeType" value="<?=$DxCodeType?>">	
    <input type="hidden" name="cpt_id" id="cpt_id" value="">	
    <input type="hidden" name="cpt_id_default" id="cpt_id_default" value="">	
    <input type="hidden" name="dx_id" id="dx_id" value="">	
    <input type="hidden" name="dx_id_default" id="dx_id_default" value="">
    <input type="hidden" name="dx_id_icd10" id="dx_id_icd10" value="">	
    <input type="hidden" name="dx_id_default_icd10" id="dx_id_default_icd10" value="">
    
    <div class="scheduler_table_Complete">
    	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
    		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
            	<div class="head_tab_inline text-center" id="selectedSurgeonNameId" style="display: block;">	
      			  <span><?php echo $procedureNameShow;?></span>                          
      			</div>
      
                <div class="caption" style="background-color:#FFF; ">
                	<span style="color:#800080;cursor:pointer; font-weight:bold; text-align:left; font-size:1.2em;" onClick="return showPreOpMediDiv('preOpOrdMed_med', 'preOpOrdMed_sgt', 'preOpOrdMed_dir','preOpOrdMed_id', '20', '5', '0'),document.getElementById('selected_frame_name_id').value='spreadSheetAjaxIdpreMed';"> Pre Op Orders  </span>
              	</div>
                
                <div class="container-fluid padding_0">
                	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12  table-condensed cf  width_table table-striped" onClick="preCloseFun('ProcPrefPreOpMediOrderDiv');">
                    	<thead>
                        	<Tr>
                            		<th style="width:33%;"> Medication  </th>
                                    <th style="width:33%;"> Strength</th>
                                    <th style="width:auto;"> Direction  </th>
                         	</Tr>
                      	</thead>
                        <tbody>
                        	<tr>
                            	<td colspan="3" class="over_wrap" style="float:none; padding:1px 0">
                                	<div class="over_wrap " id="spreadSheetAjaxIdpreMed" style="max-height:260px; ">	
                                    <?PHP
										include_once 'preop_order_sheet.php';
									?>
                             		</div>
                             	</td>
                       		</tr>
                    	</tbody>
               		</table>
            	</div>
                
                <div class="clearfix margin_adjustment_only"></div>
                <div class="clearfix margin_adjustment_only border-dashed"></div>
                <div class="clearfix margin_adjustment_only"></div>
                
                <Div class="row col-md-12 col-sm-12 col-xs-12 col-lg-12" style="background-color:#F1F1F1;">
                	
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 ">	
                        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 padding_0">
                        	<label class="caption_span" style="color:#800080;cursor:pointer; font-weight:bold; margin-top:20px;" onClick="return showIntraOpPostOpOrderAdminFn('intraOpPostOpOrderId', '', 'no', '50', '320'),document.getElementById('selected_frame_name_id').value='';">Intra Op Post Op Order</label>
                      	</div>
                        
                        <div class="col-lg-9 col-md-9 col-sm-8 col-xs-8">
                        	<textarea  id="intraOpPostOpOrderId" name="intraOpPostOpOrder"  class="form-control" style="border:1px solid #cccccc; height:60px;" tabindex="6"  ><?php echo $intraOpPostOpOrder;?></textarea>
                      	</div>
                   	</div>
                    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 ">	
                        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 padding_0">
                        	<label class="caption_span" style="color:#800080;cursor:pointer; font-weight:bold; margin-top:20px;" onClick="return showPostOpDropsAdminFn('postOpDropId', '', 'no', '50', '320'),document.getElementById('selected_frame_name_id').value='';">Post-Op Drops</label>
                      	</div>
                        
                        <div class="col-lg-9 col-md-9 col-sm-8 col-xs-8">
                        	<textarea  id="postOpDropId" name="postOpDrop"  class="form-control" style="border:1px solid #cccccc; height:60px;" tabindex="6"  ><?php echo $postOpDrop;?></textarea>
                      	</div>
                   	</div>
                    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 ">	
                    	
                        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 padding_0" >
                        	<label class="caption_span" style="color:#800080;cursor:pointer; font-weight:bold; margin-top:20px;" onClick="return showotherPreOpOrdersAdminFn('otherPreOpOrdersId', '', 'no', '50', '320'),document.getElementById('selected_frame_name_id').value='';">Other Pre-Op Orders</label>
                      	</div>
                        <div class="col-lg-9 col-md-9 col-sm-8 col-xs-8 ">
                        	<textarea  id="otherPreOpOrdersId" name="otherPreOpOrders"  class="form-control" style="border:1px solid #cccccc;  height:60px; "  tabindex="6"  ><?php echo $otherPreOpOrders;?></textarea>
                      	</div>
                    
                	</div>     
              	
                </Div>
                
                <div class="clearfix margin_adjustment_only"></div>
                <div class="clearfix margin_adjustment_only border-dashed"></div>
              	<div class="clearfix margin_adjustment_only"></div>         
                        
       			<Div class="row col-md-12 col-sm-12 col-xs-12 col-lg-12 padding_0" style="background-color:#F1F1F1;" onClick="preCloseFun('evaluationPostOpDropsAdminDiv');preCloseFun('ProcPrefPreOpMediOrderDiv');preCloseFun('otherPreOpOrdersAdminDiv');">
                	<div class=" col-md-6 col-lg-8 col-sm-6 col-xs-12 "><br>
                    
                    		<span  id="cptCode" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 btn-success" style="border-radius:0; padding:6px; text-align:center; cursor:pointer; ">CPT Codes (Surgeon &amp; Facility)</span>
							<Div class="col-xs-12 visible-xs"></Div>
                            <Div class="col-xs-12 visible-xs"></Div>
                            <span id="cptCodeA" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 btn-warning" style="border-radius:0; padding:6px; text-align:center; cursor:pointer;  ">CPT Codes (Anesthesia)</span>
                            <Div class="col-xs-12 visible-xs"></Div>
                            <Div class="col-xs-12 visible-xs"></Div>
                            <span id="dxCode" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 btn-primary" style="border-radius:0; padding:6px; text-align:center; cursor:pointer;  ">Dx Codes</span>
                            <Div class="col-xs-12 visible-xs"></Div>
                            <Div class="col-xs-12 visible-xs"></Div>
                            <span  id="procedureConsents" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 btn-info" style="border-radius:0; padding:6px; text-align:center; cursor:pointer; ">Procedure Consents</span>
                  	</div> 
                    <!--
                    <div class=" row col-md-3 col-lg-3 col-sm-6 col-xs-12 ">
                   			<select class="selectpicker" name="consentTemplateId[]" id="consentTemplateId" title=" Procedure Consents" data-header=" Procedure Consents" data-size="10" multiple >
								<?php
									$consentFormTemplates = $objManageData->getArrayRecords('consent_forms_template','','','consent_name','ASC');
									$seq="";
									foreach($consentFormTemplates as $templates)
									{
                           				$deletedConsentFormDisplay = true;
										$consentTemplateIdExplode=array();
										if($consentTemplateId) { 
											$consentTemplateIdExplode = explode(',',$consentTemplateId);
										}
                           				if(!in_array($templates->consent_id,$consentTemplateIdExplode) && $templates->consent_delete_status=='true') { $deletedConsentFormDisplay=false;}
                            			
										if($deletedConsentFormDisplay == true)
										{
											++$seq;
							?>
                            			<option 
                                        		value="<?=$templates->consent_id?>" 
												<?=(in_array($templates->consent_id,$consentTemplateIdExplode) ? 'selected' : '')?>
                                        >
                                        	<?php echo stripslashes($templates->consent_name); ?>
                                        </option>
                            
                            <?php
                            			}
                       				}
                                ?>
                 			</select>
                   </div>
                   -->
                    <Div class="col-xs-12 visible-xs"></Div>
                    <Div class="col-xs-12 visible-xs"></Div>
                    
                    <div class=" col-md-6 col-lg-4 col-sm-6 col-xs-12 padding_0 ">
                    	<span class="row col-md-6 col-lg-6 col-sm-6 col-xs-12 "><strong>Operative Report Template</strong>
                    		<select class="selectpicker dropup" name="opTemplateId" id="opTemplateId" title="Operative Report Template" data-header=" Operative Report Template" data-size="10">
								<?php
                                        
										//$preOpTemplates = $objManageData->getArrayRecords('operative_template','surgeonId',$surgeonId,'template_name','asc');
										$qryStr = "SELECT * FROM operative_template WHERE surgeonId = 0 " ;
										$qryQry = imw_query($qryStr);
										if($qryQry){
											while($qryRow = imw_fetch_object($qryQry)){
												$preOpTemplates[] = $qryRow;
											}
										}
										if(is_array($preOpTemplates) && count($preOpTemplates) > 0)
										{
											echo '<optgroup label="Community Templates" data-icon="glyphicon glyphicon-hand-right" class="optgroup">';
											foreach($preOpTemplates as $templates)
											{
								?>
                                				<option value="<?php echo $templates->template_id; ?>" <?=($operativeTemplateId == $templates->template_id  ? 'selected' : '')?>>
                                                <?php echo stripslashes($templates->template_name); ?>
                                            	</option>
                                <?php
											}
											echo"</optgroup>";
											
											
										}
                                ?>
                                <?php 
									/*$query	=	"Select OT.*, Concat(U.lname, ', ', U.fname, ' ', U.mname) As surgeonName From operative_template OT Left Join users U On OT.surgeonId = U.usersId
Where (OT.surgeonId = 0 and U.usersId is null ) or (OT.surgeonId > 0 and U.usersId is not null)
ORDER BY `surgeonName` ASC";
									$sql = imw_query($query);
									
									if($sql)
									{
											$currentUserId	=	'';$c = 0;
											while($row	=	imw_fetch_object($sql))
											{$c++ ;
												if($currentUserId <> $row->surgeonId)
												{
													$currentUserId	=	$row->surgeonId ;
													$title		=	(($currentUserId > 0 ) ? $row->surgeonName.'\'s' : 'Community') . ' Templates';
													echo '<option disabled data-content="<span class=\'col-lg-12 col-md-12 col-sm-12 col-xs-12 btn-primary \' style=\'border-radius:0; padding:6px; \'><i class=\' glyphicon glyphicon-hand-right\'></i>&nbsp;'.$title.'</span>" >'.$title.'</option>';
													
												}
									?>
                                    			<option value="<?php echo $row->template_id; ?>" <?=($operativeTemplateId == $row->template_id  ? 'selected' : '')?> >
                                                <?php echo stripslashes($row->template_name); ?>
                                            	</option>
                                    <?php				
											}
										}*/
								
								?>
                                
                 			</select>
                 		</span>       
                    	
                        <span  class="col-xs-12 visible-xs"></span>
                        <span  class="col-xs-12 visible-xs"></span>
                    	
                        <span class=" col-lg-6 col-md-6 col-sm-6 col-xs-12 "><strong>Instruction Sheet Template</strong>
                            <select name="instructionTemplateId" id="instructionTemplateId" class="selectpicker dropup" title=" Instruction Sheet Template" data-header=" Instruction Sheet Template" data-size="10"  >
                            
								<?php
                                    $insSheetTemplates = $objManageData->getArrayRecords('instruction_template','','','instruction_name','asc');
                                    foreach($insSheetTemplates as $templates)
                                    {
                                ?>
                                        <option value="<?php echo $templates->instruction_id; ?>" <?=($instructionSheetId == $templates->instruction_id ? 'selected' : '')?> >
                                            <?php echo stripslashes($templates->instruction_name); ?>
                                        </option>
                                <?php
                                    }
                                ?>
                            </select>
                		</span>
                    
                    
                                                                                                  
                </Div>
                
            	
            </div>
        </Div>
    	</div>
     </div>       
    
   