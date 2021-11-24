<?php
$js_icd_data=json_encode($icd_data);
		$js_icd10_bilateral=json_encode($icd10_bilateral);
		$js_icd10_charts_data=json_encode($icd10_charts_data);
		$js_icd10_charts_all_data=json_encode($icd10_charts_all_data);
		$htm = "<script>var js_icd_data_arr = ".$js_icd_data.";
					  var js_icd10_bilateral_arr = ".$js_icd10_bilateral.";
					  var js_icd10_charts_data_arr = ".$js_icd10_charts_data.";
					  var js_icd10_charts_all_data_arr = ".$js_icd10_charts_all_data.";
				</script>";

/* width */
/*
$width_cpt = " width=\"120\" ";
$width_unit = " width=\"35\" ";
$width_dx = " width=\"275\" ";
$width_mod = " width=\"37\" ";
$width_ed = " width=\"36\" ";
*/

/* width */
$width_cpt = " width=\"25%\" ";
$width_unit = " width=\"7%\" ";
$width_dx = " width=\"45%\" ";
$width_mod = " width=\"5%\" ";
$width_ed = " width=\"7%\" ";
if(!empty($sb_testName) || (isset($_POST["accSB"]) && $_POST["accSB"]=="1") ){
	$width_cpt = " width=\"20%\" ";
	$width_ed = " width=\"15%\" ";
}

?>


<div id="module_buttons" class="row superbillhd">
	<div class="col-sm-2">
		<?php if($sbillbtn == false) { ?>
			<button class="btn btn-success" type="button" <?php echo $enableSB;?> >Super Bill</button>
		<?php } ?>
	</div>
	<div class="col-sm-2 text-center">
		<button class="btn btn-success" type="button" id="btn_dx_ast" >Dx Assist</button>
	</div>
	<div class="col-sm-4 text-center">
		<span class="totalcharges">Total Charges : <?php echo show_currency();  ?><span class="amount"><?php echo (!empty($elem_todaysCharges)) ? $elem_todaysCharges : "0.00";?></span></span>
	</div>
	<div class="col-sm-4 text-right">
		<input type="checkbox" name="vipSuperBill" id="vipSuperBill" value="1" <?php if($vipSuperBill==1) echo "checked";?> onclick="setSBTodayCharges();" class="frcb">
		<label class="btn btn-success frcb" for="vipSuperBill">VIP</label>
		<?php if($printmedsbut!="" && $sbillbtn == false){ ?>
			<button id="printMedBtnId" class="btn btn-success" type="button">Print Meds</button>
		<?php } ?>
	</div>
</div>
<div class="clearfix"></div>

<!--
<div class="clearfix"></div>-->

<div class="dxcodes">
	<div class="row">
	<div class="col-sm-2">
		<h2>Dx Codes</h2>
	</div>
	<div class="col-sm-10">
		<h2 class="pull-right" id="td_clsupplytotal"></h2>
	</div>
	</div>
	<div class="row">

	<?php
	for($i=1;$i<=12;$i++){
		$d=$i;
		$dxDescTmp="";
		if(!isset($all_dx_codes_arr_title["indx"][$i]) || empty($all_dx_codes_arr_title["indx"][$i])){
			$dxDescTmp = (!empty($all_dx_codes_arr[$d])) ? $oDx->getDxTableInfo($all_dx_codes_arr[$d], $enc_icd10) : "" ;
			if($dxDescTmp == false || empty($dxDescTmp)){   $dxDescTmp = "";	}
		}else{
			$dxDescTmp=$all_dx_codes_arr_title["indx"][$i];
		}

	?>

		<div class="col-sm-2">
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-addon diagText_span_<?php echo $d; ?>" onclick="sb_swap_vals(this)"><?php echo $i;?></div>
					<input type="text" class="form-control dxallcodes" id="elem_dxCode_<?php echo $d; ?>" name="elem_dxCode_<?php echo $d; ?>" value="<?php echo $all_dx_codes_arr[$d]; ?>" onblur="checkDXCodesChart(this);" data-toggle="tooltip" title="<?php echo $dxDescTmp;?>" data-dxid="<?php echo $dx_code_id_arr[$d];?>" >
					<input id="lit_diagText_<?php echo $d; ?>"  type="hidden" value="" name="lit_diagText_<?php echo $d; ?>" >
					<input id="dx_oldCode_<?php echo $d; ?>"  type="hidden" value="" name="dx_oldCode_<?php echo $d; ?>" >
				</div>
			</div>
		</div>
	<?php
	}
	?>

	</div>

</div>
<div class="clearfix"></div>


<div class="table-responsive pt10">

	<table class="table table-striped table-bordered">
		<thead>
			<tr class="grythead">
				<th <?php echo $width_cpt; ?> >CPT</th>
				<th <?php echo $width_unit; ?> >Units</th>
				<th <?php echo $width_dx; ?> >Dx Codes</th>
				<th <?php echo $width_mod; ?> >Mod1</th>
				<th <?php echo $width_mod; ?> >Mod2</th>
				<th <?php echo $width_mod; ?> >Mod3</th>
				<th <?php echo $width_mod; ?> >Mod4</th>
				<th <?php echo $width_ed; ?> >&nbsp;</th>
			</tr>
		</thead>
		<tbody id="tblSuperbill">
			<?php
		if(!isset($superLen) || empty($superLen)){

			if(!empty($sb_testName)){
				$superLen = 2;
			}else{
				$superLen = 3;//4
			}

		}else{
			$superLen = $superLen+1;
		}

		for($i=1;$i<=$superLen;$i++){

			$elem_cptCodeName = "elem_cptCode_".$i;
			$elem_cptCode = $$elem_cptCodeName;
			$elem_procedureIdName = "elem_procedureId_".$i;
			$elem_procedureId = $$elem_procedureIdName;
			$elem_procUnitsName = "elem_procUnits_".$i;
			$elem_procUnits = (!empty($$elem_procUnitsName)) ? $$elem_procUnitsName : "1" ;
			$elem_procDescVar = "elem_procedureDesc_".$i;
			$elem_procDesc = !empty($$elem_procDescVar) ? $$elem_procDescVar : "";
			$elem_procedureIdOrder = "elem_procedureOrder_".$i;
			$elem_procedureOrder = (!empty($$elem_procedureIdOrder)) ? $$elem_procedureIdOrder : $i ;
			$elem_valid_dx_code4cpt = "valid_dx_code4cpt".$i;
			$valid_dx_code4cpt=$$elem_valid_dx_code4cpt; //getValidDxCodes4Cpt($elem_cptCode);

			$tmpId = $elem_cptCode."_".$elem_procedureId;

			//dx
			$elem_dxCodeName = "elem_dxCodeAssoc_".$i;
			$str_dxCodeOpts = $$elem_dxCodeName;

			//dx title
			$elem_dxCodeName_title = "elem_dxCodeAssoc_".$i."_title";
			$elem_dxCodeName_title_val = $$elem_dxCodeName_title;

			if(empty($str_dxCodeOpts) && !empty($strCurDxCodesOpts_default)){ //set default
				$str_dxCodeOpts = $strCurDxCodesOpts_default;
			}

		?>

		<tr id="elem_trSB<?php echo $i; ?>">
			<td class="cpt_td" <?php echo $width_cpt; ?> >
				<div class="input-group">
					<label class="pointer input-group-addon text-center contno" onclick="sb_swap_vals(this)">
						<?php echo $i; ?>
					</label>
					<input type="text" id="<?php echo $elem_cptCodeName; ?>" name="<?php echo $elem_cptCodeName; ?>" value="<?php echo $elem_cptCode; ?>" data-toggle="tooltip" title="<?php echo $elem_procDesc; ?>" class="cptcode form-control" onblur="checkCptCodesChart(this);"  valid_dxcodes="<?php echo $valid_dx_code4cpt; ?>" >
				</div>
				<input type="hidden" id="<?php echo $elem_procedureIdName; ?>" name="<?php echo $elem_procedureIdName; ?>" value="<?php echo $elem_procedureId; ?>">
				<input type="hidden" id="<?php echo $elem_procedureIdOrder; ?>" name="<?php echo $elem_procedureIdOrder; ?>" value="<?php echo $elem_procedureOrder; ?>">

			</td>
			<td class="unit" <?php echo $width_unit; ?> >

				<input type="text" id="<?php echo $elem_procUnitsName; ?>" name="<?php echo $elem_procUnitsName; ?>" value="<?php echo $elem_procUnits; ?>"
				class="cptunit form-control" onchange="checkProcUnitsChart(this);">

			</td>
			<td class="dx" <?php echo $width_dx; ?> >

				<select  id="<?php echo $elem_dxCodeName; ?>" name="<?php echo $elem_dxCodeName; ?>[]" multiple="multiple" data-width="100%" class="diagText_all_css minimal selectpicker dropupalways " data-toggle="tooltip" title="<?php echo $elem_dxCodeName_title_val; ?>" data-actions-box="true" >
				<?php echo $str_dxCodeOpts; ?>
				</select>

			</td>
			<?php
			  for($j=1;$j<=4;$j++){
				$elem_modCodeName = "elem_modCode_".$i."_".$j;
				$elem_modCode = $$elem_modCodeName;
			  ?>
			<td class="md" <?php echo $width_mod; ?> >
				<div class="input-group">
				<input type="text" id="<?php echo $elem_modCodeName; ?>" name="<?php echo $elem_modCodeName; ?>" value="<?php echo $elem_modCode; ?>"
							class="form-control modcode" onblur="checkModCodesChart(this);" >
				</div>
			</td>
			 <?php
			  }
			  ?>

			<td valign="middle" <?php echo $width_ed; ?> >

				<span class="glyphicon glyphicon-remove" onclick="Save_form('<?php echo $i; ?>');" data-toggle="tooltip" title="Delete"></span>

				<?php
				if($i==1){
				?>

					<span class="glyphicon glyphicon-file" alt="Copy First Dx Code" onclick="sb_copy_dx_codes(1);" data-toggle="tooltip" title="Copy First Dx Code"></span>
					<span class="glyphicon glyphicon-duplicate" alt="Copy All Dx Codes" onclick="sb_copy_dx_codes();" data-toggle="tooltip" title="Copy All Dx Codes"></span>

				<?php
				}else{
				?>

				<span class="glyphicon glyphicon-plus" onclick="opAddCptRow('<?php echo $i; ?>')" data-toggle="tooltip" title="Insert"></span>
				<?php
				}
				?>
			</td>
		</tr>

		<?php

		} //end loop

		?>
		</tbody>

	</table>

</div>
<div class="clearfix"></div>


<div>
</div><div class="clearfix"></div>
<!--
<div><img src="<?php echo $GLOBALS['webroot'];?>/library/images/add_but.png" alt="add" onclick="opAddCptRow('new')"  /></div>
<div class="clearfix"></div>
-->

<div id="divChooseDxCodes" onClick="stopClickBubble();" ></div>
<div id="divChoosePqriCodes" <?php echo $sup_div_left; ?> onClick="stopClickBubble();" ></div>




<?php
//Hidden ---
	$arr_hidden_vals=array();
	$arr_hidden_vals["elem_mxSBId"]=array($superLen);
	$arr_hidden_vals["elem_procOrder"]=array($elem_procOrder);
	$arr_hidden_vals["elem_procUnitOrder"]=array($elem_procUnitOrder);
	$arr_hidden_vals["elem_sb_insuranceCaseId"]=array($elem_sb_insuranceCaseId);
	$arr_hidden_vals["elem_sb_tsbIds"]=array();
	$arr_hidden_vals["enc_icd10"]=array(1);
	$arr_hidden_vals["elem_proc_del_id"]=array();
	$arr_hidden_vals["elem_practiceBillCode"]=array($practiceBillCode, "ev"=>" onChange=\"setVisitCode()\" ");
	$arr_hidden_vals["elem_category"]=array($patientCategory);
	$arr_hidden_vals["elem_levelOfService"]=array($patientLevelofService); //, "ev"=>" onChange=\"confirmVisitCode()\" "
	$arr_hidden_vals["elem_visitCode"]=array($patientVisitCode, "ev"=>" onChange=\"setVisitCodeFinal(this)\" ");
	$arr_hidden_vals["elem_ptcategory_consult"]=array();
	$arr_hidden_vals["elem_qualifyMsg"]=array();
	$arr_hidden_vals["elem_levelHistory"]=array(); //"ev"=>" onChange=\"setServiceLevel()\" "
	$arr_hidden_vals["elem_levelExam"]=array(); //"ev"=>" onChange=\"setServiceLevel()\" "
	$arr_hidden_vals["elem_levelComplexity"]=array(); //"ev"=>" onChange=\"setServiceLevel()\" "
	$arr_hidden_vals["elem_strExmDone"]=array();
	$arr_hidden_vals["elem_strExmNotDone"]=array();
	$arr_hidden_vals["elem_pls_sel_nq_em"]=array();
	$arr_hidden_vals["elem_pls_sel_nq_eye"]=array();
	$arr_hidden_vals["elem_proc_only_visit"]=array();
	$arr_hidden_vals["elem_post_op_visit"]=array();
	$arr_hidden_vals["elem_levelOfServiceEye"]=array();
	$arr_hidden_vals["elem_levelOfServiceEM"]=array();
	$arr_hidden_vals["elem_strExmDoneEye"]=array();
	$arr_hidden_vals["elem_strExmNotDoneEye"]=array();
	$arr_hidden_vals["elem_strExmDoneEM"]=array();
	$arr_hidden_vals["elem_strExmNotDoneEM"]=array();
	$arr_hidden_vals["elem_poe_visit_code"]=array();
	$arr_hidden_vals["elem_flgIsPtNew"]=array($flgIsPtNew);
	$arr_hidden_vals["sb_dxids"]=array();

	if(isset($sb_testName) && !empty($sb_testName)){
		$arr_hidden_vals["elem_masterCaseId"]=array($caseId);
		$arr_hidden_vals["elem_dos"]=array($elem_examDate);
		$arr_hidden_vals["elem_masterEncounterId"]=array($encounterId);
		$arr_hidden_vals["hid_icd10"]=array($hid_icd10);
	}

	$str_hidden_vals=wv_getHtmlHiddenFields($arr_hidden_vals);
	//Hidden ---
	echo $str_hidden_vals;
?>

<script>
/** Super Bill */

/*
var arrTHDesc = new Array(<?php //echo $strTHDesc;?>);
var arrTHPracCode = new Array(<?php //echo $strTHPracCode;?>);
var arrTHDesc2 = new Array(<?php //echo $strTHDesc2;?>);

var arrCptCodeAndDesc = new Array(<?php //echo $strCptCodeAndDesc;?>);
var arrDxCodeAndDesc = new Array(<?php //echo $strDxCodesAndDesc;?>);
var arrMdCodesTypeAhead = new Array(<?php //echo $strMdCodesTypeAhead;?>);
*/
var sup_enc = "<?php echo (isset($_POST["accSB"]) && $_POST["accSB"]=="1") ? 1 : "" ; ?>";
var arrCptCodeDescActive = new Array(<?php echo $strCptCodeDescActive;?>);

var sb_warnVisCd = "<?php echo $sbwarnVisCd; ?>";
var sb_cpt_code_poe="<?php echo $sb_cpt_code_poe; ?>";
var sb_refGivenOnly = "<?php echo $refSetting[1];?>";
var sb_multi_vst_cd_noalert = "<?php echo $GLOBALS['MULTIPLE_VISIT_CODES_NOALERT'];?>";

/*Test Info*/
var sb_pdiv = "<?php echo $thisPDiv; ?>";

var sb_testName = "<?php echo $sb_testName;?>";

var elem_testCptDesc="<?php echo $testCptDesc;?>";
var elem_testCptCode="<?php echo $testCptCode;?>";
var del_proc_noti="<?php echo $del_proc_noti;?>";


/** Super Bill */
$(document).ready(function(){

	$("#btn_dx_ast").bind("click", function(){ dx_assist(); });

	//if test
	if(sb_testName!=""){
		var hid_icd10 = (window.opener && window.opener.$("#hid_icd10").length>0) ? ""+window.opener.$("#hid_icd10").val() : "" ;
		if(typeof(hid_icd10)!="undefined"&&hid_icd10!=""){$("#hid_icd10").val(hid_icd10);}else{hid_icd10 = $("#hid_icd10").val();}
		//if(sb_testName=="VF_GL" || sb_testName=="OCT-RNFL"){ vfgl_addDxcodes(); } 
	};

	//$(".cptcode").each(function(){new actb(this,arrCptCodeAndDesc,'',1)});
	//$(".dxCode").each(function(){new actb(this,arrDxCodeAndDesc,'',1)});
	//$(".modcode").each(function(){new actb(this,arrMdCodesTypeAhead,'',1)});
	//*
	if(typeof(sb_addTypeAhead)!="undefined"){sb_addTypeAhead();}


	//alert($(".diagText_all_css").length);
	/*
	$(".dxallcodes").blur(sb_crt_dx_dropdown);
	$(".diagText_all_css").multiselect({
			multiple:true,
			selectedList: 12,
			noneSelectedText:'Select Dx Codes',
			minWidth:100,
			position: {
				my: 'left bottom',
				at: 'left top'
			},
			/*close:function(event, ui){ $(this).triggerHandler("blur");  }*-/
			checkAll: function(){
				$(this).triggerHandler("blur");
			},
			click:function(event, ui){
				//alert($(this).val());
				var v_icd_10 = $("#hid_icd10").val();
				if(v_icd_10!="1"&&v_icd_10!="10"){ //works for icd 9 only
					var arcurdx = ""+$(this).multiselect("getChecked").map(function(){ return this.value; }).get();
					if(arcurdx && arcurdx!="null" && arcurdx !=""){
						var tmp_arcurdx=arcurdx.split(",");
						var lm =4;
						if(tmp_arcurdx.length > lm){
							alert("You cannot select more than 4 Dx codes for a procedure.");
							return false;
						}
					}
				}
				$(this).triggerHandler("blur");

			}
		});
	$(".diagText_all_css").bind("blur", function(){ sb_checknwarn4WrongDxcode(this);  });
	$('#superbill .ui-multiselect, #superbill .diagText_all_css').css({'width': '170px'});



	$(".ui-widget-header").parent().hide(); //hack to hide header on load
	*/




		//.blur(function(){ sb_checknwarn4WrongDxcode(this); });

	//sb_set_dx_typeahead_icd10('');
	//*/
	//For Main Sheet Only--
	//if(typeof(arrTHAssess_2)!="undefined"){
	//	arrTHAssess_2 = arrTHAssess_2.concat(arrDxCodeAndDesc);
	//	$("textarea[name^='elem_assessment']").each(function(){new actb(this,arrTHAssess_2);});
	//}
	//For Main Sheet Only--
	$("#printMedBtnId").bind("click",function(){
		printPlanFun();
	});

	//$("#staging_code_info").draggable({ handle: "#staging_code_header" });


	//Drop Downs
	//$('.selectpicker').selectpicker('render');
	fun_mselect('.selectpicker', 'render');
	fun_mselect('.selectpicker', 'onchange', function(){ dx_assoc_cpt(this); });
	fun_mselect('.selectpicker','width');

	// Add menues in superbill
	sb_add_menu();


});

<?php
//if in Accounting Superbill
if(isset($_POST["accSB"]) && $_POST["accSB"]=="1" && $isSuperBill != 1){
?>
//
opSuperBill();
<?php
}else{
?>
//Allow to click and reload superbill
$("#idProcDtl").css({"color":"#663366","font-size":"13px","cursor":"pointer"}).bind("click",function(){opSuperBill();});
<?php } ?>
function staging_div_view(status)
{
	var sTop =30;
	if($("#divWorkView").scrollTop()>0){
		sTop = $("#divWorkView").scrollTop();
	}

	var top = parseInt(sTop)+30;

	status = $.trim(status);
	if(status == "show")
	{
		$('#staging_code_info').css({'display':'block','top':top+'px'});
	}
	else
	{
		$('#staging_code_info').css({'display':'none'});
	}
}
</script>
<?php echo $htm; ?>
