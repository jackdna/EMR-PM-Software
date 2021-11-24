<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
set_time_limit(900);
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;

if($_REQUEST['delRecords']=='true'){
	
	$counter	=	0;
	$chkBoxArray = $_REQUEST['chkBox'];
	
	foreach($chkBoxArray as $procSuppId){
			$updateArray['deleted']	=	1 ;
			$del_rec=$objManageData->UpdateRecord($updateArray, 'procedure_supplies', 'proc_supp_id', $procSuppId);
			if($del_rec)$counter++;
	}
	if($del_rec)
	{
		echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
	}
}

if($_REQUEST['sbtForm'])
{
	$procSuppIdArr	=	$_REQUEST['procSuppId'];
	$procListArr		=	$_REQUEST['procList'];
	//echo '<pre>';
	//print_r($_REQUEST);
	if( is_array($procListArr)  && count($procListArr) >  0)
	{
		foreach($procListArr as $key => $procedureId)
		{
			if($procedureId != '' )
			{
				
					$procSuppId	=	$procSuppIdArr[$key];
					if($procSuppId)
					{
						$chkRows=	$objManageData->getRowCount('procedure_supplies', array('procedure_id = '=>$procedureId, 'proc_supp_id <> ' => $procSuppId));
						
						if($chkRows == 0)
						{
							$arrayUpdateRecord['procedure_id']	=	$procedureId;
							$arrayUpdateRecord['supplies'] 			=	implode(',',$_REQUEST['suppList'.$procSuppId.'']);
							
							//print_r($arrayUpdateRecord); echo '<br>';
							$b = $objManageData->UpdateRecord($arrayUpdateRecord, 'procedure_supplies', 'proc_supp_id', $procSuppId);
						}
						else
						{
							$error	=	true;
							$message	=	'Procedure template already exist';	
						}
					}
					else
					{
						$chkRows=	$objManageData->getRowCount('procedure_supplies', array('procedure_id = '=>$procedureId));
						if($chkRows == 0)
						{
							$arrayAddRecord['procedure_id']	=	$procedureId;
							$arrayAddRecord['supplies'] 			=	implode(',',$_REQUEST['suppList']);
							$a=$objManageData->addRecords($arrayAddRecord, 'procedure_supplies');	
							//print_r($arrayAddRecord); echo '<br>';					
						}
						else
						{
							$error	=	true;
							$message	=	'Procedure template already exist';	
						}
							
					}
			}
		}
		
		if($error)		echo "<script>top.frames[0].alert_msg('error','".$message."')</script>";
		elseif($a)		echo "<script>top.frames[0].alert_msg('success')</script>";
		elseif($b)		echo "<script>top.frames[0].alert_msg('update')</script>";
		
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Procedure Supplies</title>
<?php include("adminLinkfile.php");?>
<script>
$(window).load(function()
{
	var LDL	=	function()
	{
		var WH	=	$(window).height();
		var SH		=	$(".subtracting-head").outerHeight(true);
		var H		=	WH - SH ;
		
		var height_custom_scroll_new=	top.frames[0].frames[0].$('.scrollable_yes');
		height_custom_scroll_new.css({ 'min-height' : H , 'max-height': H});
		
	}
	LDL();
	$(window).resize(function(e) {
	   LDL();
	});
});

$(document).ready(function(){
	$(".my_table_Checkall table #checkall").click(function () {
			if ($(".my_table_Checkall #checkall").is(':checked')) {
				$(".my_table_Checkall input[type=checkbox]").each(function () {
					$(this).prop("checked", true);
				});
	
			} else {
				$(".my_table_Checkall input[type=checkbox]").each(function () {
					$(this).prop("checked", false);
				});
			}
		});
	
});

function validateFields()
{
		var P	=	$('select[name="procList[]"]'); 
		var S	=	$('select[name^="suppList"]');
		var PL	=	(P.length -1) ;
		var SL	=	(S.length -1) ;
			
		if($('#add_procedure_supplies').hasClass('in'))
		{ 			
				var VP	=	P.eq(PL).val();
				var VS	=	S.eq(SL).val()
					
					if( !VP)
					{
							alert("Select Procedure"); 
							P[PL].focus();
							return false;
					}
					else if( !VS)
					{
							alert("Select Supplies"); 
							S[SL].focus();
							return false;
					}	
			}
			else
			{ 
					for (var i = 0 ; i < PL ; i++)
					{
						var VP	=	P.eq(i).val();
						var VS	=	S.eq(i).val()
						
						if( !VP)
						{
								alert("Select Procedure"); 
								P[i].focus();
								return false;
						}
						else if( !VS)
						{
								alert("Select Supplies"); 
								S[i].focus();
								return false;
						}	
					}
			}
			
			return true;
				
}
function add_btn_click() {
	
	$('#add_procedure_supplies').modal({
		show: true,
		backdrop: 'static',
		keyboard: false
	});
}

$(function()
{
	$('.active-record, .inactive-record').click(function(){
			
			var $this		=	$(this);
			var UField	=	'deleted';
			var UValue	=	$this.hasClass('active-record')	?	1 	:	0;
			var Tbl			=	$this.attr('data-table-name');
			var IField		=	$this.attr('data-unique-field');
			var ID			=	$this.attr('data-record-id');
			
			$.ajax({
				url 	:	'updateStatus.php',
				type	:	'POST',
				dataType	:	"json",
				data : { 'UF' : UField ,'UV' : UValue, 'TN' : Tbl, 'UO' : IField, 'UOV': ID },
				beforeSend: function()
				{
					top.$(".loader").fadeIn(500);
				},
				complete: function()
				{
					top.$(".loader").fadeOut(500);
				},
				success :function (data)
				{
					if(data.success == 1)
					{
							if($this.hasClass('active-record'))
								$this.removeClass('active-record').addClass('inactive-record');
							else
								$this.removeClass('inactive-record').addClass('active-record');
					}
					else
					{
							top.frames[0].alert_msg('error',data.error_msg)
					}
					
				}
				
			});
			
	});
	
});
</script>
</head>
<body>
	<form name="suppliesFrm" action="supplies.php" method="post" style="margin:0px;" class="alignCenter">
	<input type="hidden" name="delRecords" value="">
	<input type="hidden" name="sbtForm" value="">

	<Div class="all_admin_content_agree wrap_inside_admin">
    	<Div class="subtracting-head">
        	
            <div class="head_scheduler new_head_slider padding_head_adjust_admin">
            	<span>Procedure Supplies</span>
          	</div>
            
       	</Div>
        
        <Div class="wrap_inside_admin scrollable_yes" >
        	<div class="scheduler_table_Complete ">
            
            	<div class="my_table_Checkall adj_tp_table">
                	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                    	<thead class="cf">
                        	<tr>
                            		<th style="width:3%">&nbsp;</th>
									<th style="width:3%">&nbsp;</th>
                                    <th style="width:47%" class="text-left">Procedure</th>
                                    <th style="width:47%" class="text-left">Supplies</th>
                         	</tr>
                      	</thead>
                    	<Tbody>
                        <?php 
						
						$proceduresData	=	$objManageData->getAllRecords('procedures','',array('del_status ='=> ''),'',array('name'=>'Asc'));
						$suppliesData		=	$objManageData->getAllRecords('predefine_suppliesused','',array('deleted ='=> 0),'',array('name'=>'Asc'));
						//echo '<pre>'; print_r($suppliesData);
						$table		=		"procedure_supplies" ;
						$ProcSuppData = $objManageData->getArrayRecords('procedure_supplies', '1','1');
						
						if( is_array($ProcSuppData) && count($ProcSuppData)>0 )
						{
								foreach($ProcSuppData	as $row)
								{ 
									$ProcSuppId	= $row->proc_supp_id;
                            		$ProcedureId	=	$row->procedure_id;
                           			$Supplies			=	$row->supplies;
									$Supplies			=	explode(",",$Supplies);
									$deleted			=	$row->deleted;
									
					?>
                            <tr>
                                <input type="hidden" name="procSuppId[]" value="<?php echo $ProcSuppId; ?>">
                                <td class="<?=($deleted == 1 ? 'inactive-record' : 'active-record')?>" data-record-id = "<?=$ProcSuppId?>" data-table-name="<?=$table?>" data-unique-field="proc_supp_id" >&nbsp;</td>
                                <Td class="text-center"><input type="checkbox" name="chkBox[]" value="<?php echo $ProcSuppId; ?>">	</Td>
                                <Td class="text-left low_width_t" >
                                		<select name="procList[]" class="selectpicker show-tick dropup" data-container="body" >
                                        	<?php	foreach($proceduresData as $proc) : ?>
                                            <option value="<?=$proc->procedureId?>" <?php if($ProcedureId==$proc->procedureId) echo "Selected"; ?>><?=$proc->name?></option>
                                        	<?php endforeach; ?>
                                		</select>
                                </Td>
                                <Td class="text-left low_width_t" >
                                		<select name="suppList<?=$ProcSuppId?>[]" class="selectpicker" data-container="body"  multiple title="Select Supplies">
                                        	<?php	foreach($suppliesData as $supp) : ?>
                                            <option value="<?=$supp->suppliesUsedId?>" <?php if(in_array($supp->suppliesUsedId, $Supplies)) echo "Selected"; ?> ><?=$supp->name?></option>
                                            <?php endforeach; ?>
                                		</select>
                                </Td>
                                
                                
                            </tr>
                    <?php		
						}
						}
						
						else
						{
								echo '<tr height="60"><td colspan="4"><center><i><b>No record found</b></i></center></td></tr>';	
							
						}
						
                    ?>							
                    
                    
                    </Tbody>
            </table>
         </div>                
      
      </div>	
     </Div>
      </Div>	        
    
    <div class="modal fade " id="add_procedure_supplies">
    		<div class="modal-dialog modal-lg ">
            		<div class="modal-content">
                    		
                            <div class="modal-header text-center">
                            	<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="top.frames[0].document.getElementById('cancelButton').click();"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title rob">ADD NEW  </h4>  
                          	</div>
                            
                            <div class="modal-body">
                            	
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                	<div class="form_inner_m">
                                    	<div class="row">
                                        	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            		<label for="ps" class="text-left"> Procedure</label>
                                          	</div>
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            		<select name="procList[]" class="selectpicker show-tick dropup" title="Select Procedure" >
														<?php	foreach($proceduresData as $proc) : ?>
                                                        <option value="<?=$proc->procedureId?>" ><?=$proc->name?></option>
                                                        <?php endforeach; ?>
                                        			</select>
                                    		</div>
                                    	</div>
                            		</div>
                        		</div>
                                
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form_inner_m">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                <label for="ps2" class="text-left"> 
                                                     Supplies Used 
                                                </label>
                                            </div>
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                    <select name="suppList[]" class="selectpicker"  title="Select Supplies" multiple >
                                                    <?php	foreach($suppliesData as $supp) : ?>
                                           			<option value="<?=$supp->suppliesUsedId?>"><?=$supp->name?></option>
                                            		<?php endforeach; ?>
                                                    </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    
                				<Div class="clearfix"></Div>
                                  
          					</div>
            				
                            <div class="modal-footer">
                					<a class="btn btn-primary" href="javascript:void(0);" onClick="top.frames[0].document.getElementById('saveButton').click();">  <b class="fa fa-save"></b>  Save </a>
                					<a class="btn btn-danger" href="javascript:void(0)" onClick="top.frames[0].document.getElementById('cancelButton').click();" data-dismiss="modal"><b class="fa fa-times"></b>	Cancel  </a>
            				</div>
         
        			</div>
     		</div>
    </div>
    
</form>
</body>
</html>