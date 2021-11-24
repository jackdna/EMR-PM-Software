<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

require_once("../admin_header.php");
require_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/ChartTemp.php');

//Array Procedure and Elems
$arrLabel_1 = array("Objective Notes"=>"elem_objNote","Vision"=>"elem_vision","Distance"=>"elem_visDistance",
					"Near"=>"elem_visNear","AR"=>"elem_visAr","AK"=>"elem_visAk",
					"PC 1"=>"elem_visPc1","PC 2"=>"elem_visPc2","PC 3"=>"elem_visPc3",
					"MR 1"=>"elem_visMr1","MR 2"=>"elem_visMr2","MR 3"=>"elem_visMr3",
					"BAT"=>"elem_visBat","Contact Lens"=>"elem_visContLens", "PAM"=>"elem_visPam", "LASIK"=>"elem_visLasik"); //

$arrLabel_2 = array("CVF"=>"elem_cvf","Amsler Grid"=>"elem_amsler_grid","ICP Color Plate"=>"elem_icpClr",
					"Stereopsis"=>"elem_stereo","Diplopia"=>"elem_diplopia","W4Dot"=>"elem_w4dot",
					"Comments"=>"elem_comments",
					"Retinoscopy"=>"elem_retino","Exophthalmometer"=>"elem_exophth",
					"Cycloplegic Retinoscopy"=>"elem_cyc_ret",
					"Pupil"=>"elem_pupil",
					"EOM"=>"elem_eom","External"=>"elem_external","L&A"=>"elem_La","Plastic"=>"elem_plastic",
					"IOP/Gonio"=>"elem_iop","SLE"=>"elem_sle","Fundus Exam"=>"elem_fundus",
					"Refractive Surgery"=>"elem_refSurgery", "VF/OCT - GL"=>"elem_vfOctGL");
//Obj
$oChartTemp = new ChartTemp();
//get All records
$getTemplates = $oChartTemp->getAll();
//Edit Record info
if((isset($_GET["id"]) && !empty($_GET["id"])) ||
   (isset($_GET["sid"]) && !empty($_GET["sid"]))
	){
    $tId = !empty($_GET["id"]) ? $_GET["id"] : $_GET["sid"];
	list($elem_template_id,$elem_templateName,$strTemp,$elem_ccda_cpt_code,$strTemp_tech) = $oChartTemp->getTempInfo($tId);
	$arrEdTemp = (!empty($strTemp)) ? explode(",",stripslashes($strTemp)) : array();
	$arrEdTemp_tech = (!empty($strTemp_tech)) ? explode(",",stripslashes($strTemp_tech)) : array();
}

//chart_admin_settings --
list($elem_settingPlastic, $elem_settingVFOCT) = $oChartTemp->getCompPlasticSetting(2);

?>
<script type="text/javascript">
	var onloadclick_flg=1;
	function delTemplate(id,msg){
		var of = document.chartTemplateFrm;
		var o =	of.elem_delId;
		if(of && o && (typeof id != "undefined")){
			if(typeof(msg)!='boolean'){msg = true;}
			if(msg){
				top.fancyConfirm("Are you sure to delete the template?","", "window.top.fmain.delTemplate('"+id+"',false)");
			}else{
				o.value = id;
				parent.parent.show_loading_image('block');
				of.submit();
			}
		}
	}

	function checkFields(val){
		if(val == 1){
			//Save
			if(document.chartTemplateFrm.elem_templateName.value == ''){
				fAlert ('Please Enter template name to save.');
				parent.parent.show_loading_image('none');
				document.chartTemplateFrm.elem_templateName.className = 'form-control mandatory';
				document.chartTemplateFrm.elem_templateName.focus();
				return false;
			}
			document.chartTemplateFrm.saveBtn.value = 'submit';
			parent.parent.show_loading_image('block');
			document.chartTemplateFrm.submit();
		}else{
			//Cancel
			window.location.replace("<?php echo $rootdir."/admin/chart_notes";?>/chart_template.php");
		}
	}

	function selectTemplate(id, name){
		if(typeof id != "undefined"){
			parent.parent.show_loading_image('block');
			window.location.href = '?id='+id;
		}
	}

//Set ON Elem
function setONElem(ov){

	if((ov.checked == true)){
		var arr = new Array( "elem_vision","elem_visDistance","elem_visNear",
							 "elem_visAr","elem_visAk",
							 "elem_visPc1","elem_visPc2","elem_visPc3",
							 "elem_visMr1","elem_visMr2","elem_visMr3",
							 "elem_visBat","elem_visContLens","elem_visPam","elem_visLasik",
							 "elem_cvf","elem_amsler_grid","elem_icpClr",
							 "elem_stereo","elem_diplopia","elem_w4dot","elem_comments","elem_cyc_ret",
							 "elem_retino","elem_exophth","elem_pupil",
							 "elem_eom","elem_external","elem_La","elem_plastic",
							 "elem_iop","elem_sle","elem_fundus","elem_refSurgery",
							 "elem_conj","elem_corn","elem_antChm","elem_IrisPupil","elem_lens","elem_drawSle",
							 "elem_opNrv","elem_macula","elem_vit","elem_peri","elem_bv","elem_drawRv","elem_vfOctGL","elem_reti");
		var ln = arr.length;
		var temp="";
		for(var i=0;i<ln;i++){
			temp=arr[i];
			if(ov.name.indexOf("_tech")!=-1){	 temp=temp+"_tech"; }
			var o = document.getElementsByName(temp)[0];
			var o1 = document.getElementsByName(temp)[1];

			if(o && o1){
				if((ov.value != "0") && (ov.value != "")){
					o1.disabled = false;
					o1.checked = true;
					o.disabled = true; //
				}else{
					o.disabled = false;
				}
			}
		}

		//tech
		if(ov.name.indexOf("_tech")==-1&&onloadclick_flg==0){
			$("input[name^='"+ov.name+"'][value^='"+ov.value+"']").get(0).click();
		}
		//
	}
}

//Set Vision Elem
function setVisElem(ov){
	if((ov.checked == true)){
		var arr = new Array( "elem_visDistance",
							 "elem_visNear","elem_visAr","elem_visAk",
							 "elem_visPc1","elem_visPc2","elem_visPc3",
							 "elem_visMr1","elem_visMr2","elem_visMr3",
							 "elem_visBat","elem_visContLens", "elem_visPam",
							 "elem_visLasik");
		var ln = arr.length;
		var temp="";
		for(var i=0;i<ln;i++){
			temp=arr[i];
			if(ov.name.indexOf("_tech")!=-1){	 temp=temp+"_tech"; }
			var o = document.getElementsByName(temp)[0];
			var o1 = document.getElementsByName(temp)[1];
			if(o && o1){
				if((ov.value != "0") && (ov.value != "")){
					o.checked = true;
					o1.disabled = true;
				}else{
					o1.disabled = false;
				}
			}
		}

		//tech
		if(ov.name.indexOf("_tech")==-1&&onloadclick_flg==0){
			$("input[name^='"+ov.name+"'][value^='"+ov.value+"']").get(0).click();
		}
		//
	}
}

function setSLElem(ov){
	if((ov.checked == true)){
		var arr = new Array("elem_conj","elem_corn","elem_antChm","elem_IrisPupil","elem_lens","elem_drawSle");
		var ln = arr.length;
		var temp="";
		for(var i=0;i<ln;i++){
			temp=arr[i];
			if(ov.name.indexOf("_tech")!=-1){	 temp=temp+"_tech"; }
			var o = document.getElementsByName(temp)[0];
			var o1 = document.getElementsByName(temp)[1];
			if(o && o1){
				if((ov.value != "0") && (ov.value != "")){
					o.checked = true;
					o1.disabled = true;
				}else{
					o1.disabled = false;
				}
			}
		}

		//tech
		if(ov.name.indexOf("_tech")==-1&&onloadclick_flg==0){
			$("input[name^='"+ov.name+"'][value^='"+ov.value+"']").get(0).click();
		}
		//
	}

}

function setFundusElem(ov){
	if((ov.checked == true)){
		var arr = new Array("elem_opNrv","elem_vit","elem_macula","elem_peri","elem_bv","elem_reti","elem_drawRv");
		var ln = arr.length;
		var temp="";
		for(var i=0;i<ln;i++){
			temp=arr[i];
			if(ov.name.indexOf("_tech")!=-1){	 temp=temp+"_tech"; }
			var o = document.getElementsByName(temp)[0];
			var o1 = document.getElementsByName(temp)[1];
			if(o && o1){
				if((ov.value != "0") && (ov.value != "")){
					o.checked = true;
					o1.disabled = true;
				}else{
					o1.disabled = false;
				}
			}
		}

		//tech
		if(ov.name.indexOf("_tech")==-1&&onloadclick_flg==0){
			$("input[name^='"+ov.name+"'][value^='"+ov.value+"']").get(0).click();
		}
		//
	}
}

function setPlastic(ov){

	if((ov.checked == true)){
		var arr = new Array("elem_La");
		var ln = arr.length;
		var temp="";
		for(var i=0;i<ln;i++){
			temp=arr[i];
			if(ov.name.indexOf("_tech")!=-1){	 temp=temp+"_tech"; }
			var o = document.getElementsByName(temp)[0];
			var o1 = document.getElementsByName(temp)[1];
			if(o && o1){
				if((ov.value != "0") && (ov.value != "")){
					o.checked = true;
					//o1.disabled = true;
					o1.onclick=function(){ o.checked = true;  alert("Plastic is Yes so L&A cann't be NO. ");return false; }
				}else{
					//o1.disabled = false;
					o1.onclick=function(){ }
				}
			}
		}

		//tech
		if(ov.name.indexOf("_tech")==-1&&onloadclick_flg==0){
			$("input[name^='"+ov.name+"'][value^='"+ov.value+"']").get(0).click();
		}
		//
	}
}

function savePlastic(){
	var z=z1=0;
	if($("#elem_settingPlastic_1").attr("checked") == true){
		z=1;
	}

	if($("#elem_settingVFOCT_1").attr("checked") == true){
		z1=1;
	}

	$.get("saveChartTemplate.php?saveComprehensizePlastic="+z+"&saveVFOCT="+z1, function(data){ window.status="Comprehensive settings are saved!"; });
}

//On Page Load
window.onload = function() {
	//Add Objective Notes and Vision function
	var ar = new Array("elem_objNote","elem_vision","elem_sle","elem_fundus","elem_plastic");
	for(var j=0;j<5;j++){
		var oV = document.getElementsByName(ar[j]);
		var oV_tech = document.getElementsByName(ar[j]+"_tech");

		if(oV){
			var ln = oV.length;
			for(var i=0;i<ln;i++){

				if(ar[j] == "elem_vision"){
					oV[i].onclick = oV_tech[i].onclick = function(){setVisElem(this);}
				}else if(ar[j] == "elem_objNote"){
					oV[i].onclick = oV_tech[i].onclick = function(){setONElem(this);}
				}else if(ar[j] == "elem_sle"){
					oV[i].onclick = oV_tech[i].onclick = function(){setSLElem(this);}
				}else if(ar[j] == "elem_fundus"){
					oV[i].onclick = oV_tech[i].onclick = function(){setFundusElem(this);}
				}else if(ar[j] == "elem_plastic"){
					oV[i].onclick = oV_tech[i].onclick = function(){setPlastic(this);}
				}

				if(oV[i].checked == true){
					oV[i].onclick();
				}
				//tech
				if(oV_tech[i].checked == true){
					oV_tech[i].onclick();
				}
				onloadclick_flg=0;
			}
		}
	}

	//--
	$("input[type=radio][value!=0]").each(function(inx){
			if(this.name.indexOf("_tech")==-1 && this.name!="elem_vision" && this.name!="elem_objNote" && this.name!="elem_sle" && this.name!="elem_fundus" && this.name!="elem_plastic" &&
					this.name!="elem_settingPlastic" && this.name!="elem_settingVFOCT"){
				this.onclick = function(){ $("input[type=radio][name="+this.name+"_tech][value!=0]").trigger("click"); }
			}
		});
	//--

}
</script>
</head>
<body>
<form name="chartTemplateFrm" action="saveChartTemplate.php" method="post">
<input type="hidden" name="elem_template_id" id="elem_template_id" value="<?php echo $elem_template_id; ?>">
<input type="hidden" name="preObjBack" id="preObjBack"/>
<input type="hidden" name="saveBtn" id="saveBtn">
<input type="hidden" name="elem_delId" id="elem_delId" value="">
	<div class="whtbox">
		<div class="row">
			<div class="col-sm-3">
				<div class="newtemplate">
					<h2>New Template</h2>
					<div class="clearfix"></div>
					<div class="row plr10" id="newTemplate">
						<div class="col-sm-7">
							<div class="form-group">
								<label for="elem_templateName">Template Name</label>
								<input class="form-control" id="elem_templateName" name="elem_templateName" value="<?php echo $elem_templateName; ?>" type="text" <?php echo ($elem_templateName=="Comprehensive") ? " readonly=\"readonly\" onfocus=\"this.blur()\" " : "" ; ?> >
							</div>
						</div>
						<div class="col-sm-5">
							<div class="form-group">
								<label for="elem_ccda_cpt_code">CPT For Visit</label>
								<input type="" class="form-control" name="elem_ccda_cpt_code" id="elem_ccda_cpt_code" value="<?php echo $elem_ccda_cpt_code; ?>">
							</div>
						</div>
					</div>
				</div>
			<div class="clearfix"></div>
			<div class="savedchatnt" id="savedTemplate">
				<h2>Saved Chart Notes Template</h2>
				<div class="clearfix"></div>
				<div class="table-responsive">
					<?php
					if(count($getTemplates)>0){
					$the_index = 0;
					foreach($getTemplates as $templateDetail=>$val){
					$tmp_id = $val["id"];
					$tmp_name = $val["name"];
					$tmp_ccda_cpt_code= $val["ccda_cpt_code"];
					if($templateDetail % 2 == 0){ $bgClr = 'alt3'; } else{ $bgClr = ''; }
					?>
					<table class="table table-bordered">
						<tr>
							<td style="width:10%;" align="center"><?php echo $templateDetail+1; ?></td>
							<td style="width:60%;"><a href="javascript:selectTemplate('<?php echo $tmp_id; ?>', '<?php echo $tmp_name; ?>');"><?php echo $tmp_name; ?></a></td>
							<td style="width:20%;"><a href="javascript:selectTemplate('<?php echo $tmp_id; ?>', '<?php echo $tmp_name; ?>');"><?php echo $tmp_ccda_cpt_code; ?></a></td>
							<td style="width:10%;"><a href="javascript:delTemplate('<?php echo $tmp_id; ?>');" title="Delete" <?php echo ($tmp_name=="Comprehensive") ? " style=\"visibility:hidden;\" " : "" ; ?>><img src="../../../library/images/smclose.png" /></a></td>
						</tr>
						<?php } }else{ ?>
						<tr>
							<td colspan="3">No Record Found.</td>
						</tr><?php } ?>
					</table>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
			<div class="compar hidden">
			<h2>Save For Comprehensive Exam</h2>
			<div class="clearfix"></div>
			<div class="table-responsive">
				<table class="table table-bordered">
					<tr>
						<td style="width:60%;">Plastic</td>
						<td style="width:40%;">
							<div class="radio radio-inline">
								<input  id="elem_settingPlastic_1" name="elem_settingPlastic" value="1" type="radio" <?php if($elem_settingPlastic=="1") echo "checked" ; ?> >
								<label for="elem_settingPlastic_1" > Yes</label>
							</div>
							<div class="radio radio-inline">
								<input  id="elem_settingPlastic_0" name="elem_settingPlastic" value="0" type="radio" <?php if($elem_settingPlastic=="0") echo "checked" ; ?> >
								<label for="elem_settingPlastic_0" > No</label>
							</div>
						</td>
					</tr>
					<tr>
						<td>VF-GL, OCT-RNFL</td>
						<td>
							<div class="radio radio-inline">
								<input  id="elem_settingVFOCT_1" name="elem_settingVFOCT" value="1" type="radio" <?php if($elem_settingVFOCT=="1") echo "checked" ; ?> >
								<label for="elem_settingVFOCT_1" > Yes</label>
							</div>
							<div class="radio radio-inline">
								<input  id="elem_settingVFOCT_0" name="elem_settingVFOCT" value="0" type="radio" <?php if($elem_settingVFOCT=="0") echo "checked" ; ?> >
								<label for="elem_settingVFOCT_0" > No</label>
							</div>
						</td>

					</tr>
					<tr>
						<td colspan="2" class="text-center">
							<input type="button" name="getField" id="getField" value="Get Field" class="btn btn-success" onClick="getcustomField(document.getElementById('custCategory').value,document.getElementById('custSubCategory'));" />
						</td>
					</tr>
				</table>
			</div>
			</div>
			<div class="clearfix"></div>

			</div>
			<div class="col-sm-9" id="pracfields">
				<div class="plr10">
					<div class="headinghd pd10">
						<h2>Template</h2>
					</div>
					<div class="row pd10" id="templateData">
						<div class="col-sm-6">
							<div class="table-responsive provtab adminnw">
								<table class="table table-bordered table-hover " >
								<tr class="lightgray">
								  <th>Data Of Template</th>
								  <th colspan="2" align="center" >Physician</th>
								  <th colspan="2" align="center" >Technician</th>
								</tr>
								<tr class="lightgray">
								<th width="58%" >Procedure Name </th>
								<th width="11%" align="center" >Yes</th>
								<th width="11%" align="center" >No</th>
								<th width="10%" align="center" >Yes</th>
								<th width="10%" align="center" >No</th>
								</tr>
								<?php
								foreach($arrLabel_1 as $key => $val){
									$sel1 = $sel0 = "";
									if((is_array($arrEdTemp)) && (in_array($key,$arrEdTemp))){
										$sel1 = "checked";
									}else{
										$sel0 = "checked";
									}

									$pdd = (($key != "Objective Notes") && ($key != "Vision")) ? "style=\"padding-left:20px!important;\"" : "";

									//Technician --
									$val_tech = $val."_tech";

									$sel1_tech = $sel0_tech = "";
									if((is_array($arrEdTemp_tech)) && (in_array($key,$arrEdTemp_tech))){
										$sel1_tech = "checked";
									}else{
										$sel0_tech = "checked";
									}
									//Technician --

									echo "<tr>
										  <td ".$pdd.">".htmlentities($key)."</td>
										  <td class=\"text-center\"><div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val.$key."\" name=\"".$val."\" value=\"".htmlentities($key)."\" ".$sel1." ><label for=\"".$val.$key."\">&nbsp;</label></div></td>
										  <td class=\"text-center\"><div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val."\" name=\"".$val."\" value=\"0\" ".$sel0." ><label for=\"".$val."\">&nbsp;</label></div></td>
										  <td class=\"text-center\"><div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val_tech.$key."\" name=\"".$val_tech."\" value=\"".htmlentities($key)."\" ".$sel1_tech." ><label for=\"".$val_tech.$key."\">&nbsp;</label></div></td>
										  <td class=\"text-center\"><div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val_tech."\" name=\"".$val_tech."\" value=\"0\" ".$sel0_tech." ><label for=\"".$val_tech."\">&nbsp;</label></div></td>
										  </tr>";
								}
								?>
								</table>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="table-responsive provtab adminnw">
							<table class="table table-bordered table-hover " >
							<tr class="lightgray">
								<th>Data Of Template</th>
								<th colspan="2" align="center" >Physician</th>
								<th colspan="2" align="center" >Technician</th>
							</tr>
							<tr class="lightgray">
								<th width="58%" >Procedure Name </th>
								<th width="11%" align="center" >Yes</th>
								<th width="11%" align="center" >No</th>
								<th width="10%" align="center" >Yes</th>
								<th width="10%" align="center" >No</th>
							</tr>
							<?php
								foreach($arrLabel_2 as $key => $val)
								{
									$sel1 = $sel0 = "";
									if((is_array($arrEdTemp)) && in_array($key,$arrEdTemp)){
										$sel1 = "checked";
									}else{
										$sel0 = "checked";
									}

									//Technician --
									$val_tech = $val."_tech";
									$sel1_tech = $sel0_tech = "";
									if((is_array($arrEdTemp_tech)) && in_array($key,$arrEdTemp_tech)){
										$sel1_tech = "checked";
									}else{
										$sel0_tech = "checked";
									}
									//Technician --
									echo "<tr>
											<td>".htmlentities($key)."</td>
											<td class=\"text-center\"><div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val.$key."\"  name=\"".$val."\" value=\"".htmlentities($key)."\" ".$sel1." ><label for=\"".$val.$key."\"></label></div></td>
											<td class=\"text-center\"><div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val."\" name=\"".$val."\" value=\"0\" ".$sel0." ><label for=\"".$val."\"></label></div></td>
											<td class=\"text-center\"><div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val_tech.$key."\" name=\"".$val_tech."\" value=\"".htmlentities($key)."\" ".$sel1_tech." ><label for=\"".$val_tech.$key."\"></label></div></td>
											<td class=\"text-center\"><div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val_tech."\" name=\"".$val_tech."\" value=\"0\" ".$sel0_tech." ><label for=\"".$val_tech."\"></label></div></td>
										</tr>";
									//----
									if( $key == "SLE" || $key == "Fundus Exam"){

										if($key == "SLE"){
											$arr_3 = array("Conjunctiva"=>"elem_conj","Cornea"=>"elem_corn","Ant. Chamber"=>"elem_antChm",
														"Iris & Pupil"=>"elem_IrisPupil","Lens"=>"elem_lens", "Drawing"=>"elem_drawSle");
										}else if($key == "Fundus Exam"){
											$arr_3 = array("Opt. Nev"=>"elem_opNrv","Vitreous"=>"elem_vit","Macula"=>"elem_macula","Blood Vessels"=>"elem_bv","Periphery"=>"elem_peri",
														"Retinal Exam"=>"elem_reti","Drawing"=>"elem_drawRv");
										}
										foreach($arr_3 as $key2=>$val2){
											$sv = htmlentities($key2);

											if($key2=="Drawing"&&$key=="SLE"){ $sv = "DrawSLE";
											}else if($key2=="Drawing"&&$key=="Fundus Exam"){ $sv = "DrawFundus";
											}

											$sel1 = $sel0 = "";
											if((is_array($arrEdTemp)) && (in_array($sv,$arrEdTemp)||in_array($key2,$arrEdTemp))){
												$sel1 = "checked";
											}else{
												$sel0 = "checked";
											}

											//Technician --
											$val2_tech = $val2."_tech";
											$sel1_tech2 = $sel0_tech2 = "";
											if((is_array($arrEdTemp_tech)) && (in_array($sv,$arrEdTemp_tech)||in_array($key2,$arrEdTemp_tech))){
												$sel1_tech2 = "checked";
											}else{
												$sel0_tech2 = "checked";
											}
											//Technician --

											if($key2 == "Opt. Nev"){$key2="Optic Nerve";}
											else if($key2 == "Blood Vessels"){$key2="Vessels";}

											echo "<tr>
														<td style=\"padding-left:20px!important;\" >".htmlentities($key2)."</td>
														<td class=\"text-center\"> <div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val2.$sv."\" name=\"".$val2."\" value=\"".$sv."\" ".$sel1." ><label for=\"".$val2.$sv."\"></label></div></td>
														<td class=\"text-center\"> <div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val2."\" name=\"".$val2."\" value=\"0\" ".$sel0." ><label for=\"".$val2."\"></label></div></td>
														<td class=\"text-center\"> <div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val2_tech.$sv."\" name=\"".$val2_tech."\" value=\"".$sv."\" ".$sel1_tech2." ><label for=\"".$val2_tech.$sv."\"></label></div></td>
														<td class=\"text-center\"> <div class=\"radio radio-inline\"><input type=\"radio\" id=\"".$val2_tech."\" name=\"".$val2_tech."\" value=\"0\" ".$sel0_tech2." ><label for=\"".$val2_tech."\" ></label></div></td>
												  </tr>";
										}
									}
								}
							?>
							</table>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
<?php
	if(isset($_GET["sid"]) && !empty($_GET["sid"])){
		echo 'fAlert("Template is saved");';
	}
?>
var ar = [["saveBtn_template","Save","top.fmain.checkFields(1);"],["cancelBtn_template","Add New","top.fmain.checkFields(0);"]];
	top.btn_show("ADMN",ar);
	set_header_title('Template');
</script>
<?php
	require_once('../admin_footer.php');
?>
