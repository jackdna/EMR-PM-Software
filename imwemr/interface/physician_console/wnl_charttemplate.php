<?php
//wnl_charttemplate.php
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/ChartTemp.php');
//function --

function getChartTempSelect($sel=""){
	global $oChartTemp;
	$arr = $oChartTemp->getAll();

	$htm = "";
	if(count($arr)>0){
		foreach($arr as $key => $arval){
			$tmp = ($sel == $arval["id"]) ? "SELECTED" : "";

			$htm .= "<option value=\"".$arval["id"]."\" ".$tmp.">".$arval["name"]."</option>";

		}
	}

	$htm = "<select id=\"elem_chart_temp\" name=\"elem_chart_temp\" onchange=\"loadWnl(this.value)\" class=\"selectpicker minimal selecicon\"><option value=\"\"></option>".$htm."</select>";
	echo $htm;

}

function getExamWnl($exam, $cti=""){
	if(empty($exam)){return "";}
	$ret="";

	$pid = $_SESSION["authId"];

	$sql = imw_query("SELECT wnl FROM chart_admin_wnl WHERE UPPER(exam) = '".strtoupper($exam)."' AND chart_template_id='".$cti."' AND phyid='".$pid."' AND deleted='0' ");
	$row = imw_fetch_assoc($sql);
	if($row  != false){
		$ret=trim($row["wnl"]);
	}

	//Show facility WNL if Physician WNL is Empty
	if(empty($ret)){

		$sql = imw_query("SELECT wnl FROM chart_admin_wnl WHERE UPPER(exam) = '".strtoupper($exam)."' AND chart_template_id='".$cti."' AND phyid='0' AND deleted='0' ");
		$row = imw_fetch_assoc($sql);
		if($row  != false){
			$ret=trim($row["wnl"]);
		}

	}

	return $ret;
}

//function --

//
$oChartTemp = new ChartTemp();
if(!empty($_GET["cti"])){
	$chart_temp_id = $_GET["cti"];
}else{
	$def_comp_id = $oChartTemp->getIdFromName("Comprehensive");
	$chart_temp_id = $def_comp_id;
}

$arrExamNames = array("CVF", "Amsler Grid", "Pupil",  "EOM", "External",
					"L&A" => array("Lids", "Lesion", "Lid Position", "Lacrimal System"),
					"Gonio",
					"SLE" => array("Conjunctiva", "Cornea", "Ant. Chamber", "Iris & Pupil", "Lens"),
					"Fundus"=> array("Optic Nerve", "Vitreous", "Macula", "Blood Vessels", "Periphery", "Retinal Exam")
					 );



//--
if(isset($_POST["elem_edit_wnl"]) && !empty($_POST["elem_edit_wnl"])){

	$len = count($_POST["elem_wnl"]);
	$elem_chart_temp = $_POST["elem_chart_temp"];
	if(!empty($elem_chart_temp)){

	for($i=0; $i<$len; $i++){

		$tmp_exam = imw_real_escape_string($_POST["elem_exam"][$i]);
		$tmp_wnl = imw_real_escape_string($_POST["elem_wnl"][$i]);

		$sql =imw_query( "SELECT id FROM chart_admin_wnl WHERE UPPER(exam) = '".strtoupper($tmp_exam)."' AND chart_template_id='".$elem_chart_temp."' AND phyid='".$_SESSION["authId"]."' AND deleted='0' ");
		$row = imw_fetch_assoc($sql);
		if($row != false){
			$tmp_id = $row["id"];

			$sql = "UPDATE chart_admin_wnl SET wnl = '".$tmp_wnl."' WHERE id = '".$tmp_id."'  ";
			$rr = imw_query($sql);

		}else{

			$sql = "INSERT INTO chart_admin_wnl (id, wnl, exam, phyid, chart_template_id) VALUES (NULL, '".$tmp_wnl."', '".$tmp_exam."', '".$_SESSION["authId"]."', '".$elem_chart_temp."')  ";
			$rr = imw_query($sql);
		}
	}

	//Add Defualt  Chart template id --
	if(isset($_POST["elem_def_template_id"])){
		$sql = "UPDATE users SET chart_template_id = '".$_POST["elem_def_template_id"]."' WHERE id = '".$_SESSION["authId"]."'   ";
		$row = imw_query($sql);
	}

	//Add Defualt  Chart template id --
	}

	echo 1;
	exit;
}
//--

?>
<div id="dv_wnl_ct_cvr" class="pdl_10 pt10 pdr_10">
    <form method="post" name="frm_wnl" action="javascript:void(0);" id="frm_wnl">
		<div id="dv_wnl_ct">
			<input type="hidden" name="elem_edit_wnl" value="1">
			<div class="row wndHead">
				<div class="col-sm-2 headTitle pt10">
					Default Template for New Chart Note
				</div>
				<div class="col-sm-10">
					<div class="row">
						<div class="col-sm-2">
							<label for="elem_def_template_id" class="pt10">Select Template</label>
						</div>
						<div class="col-sm-4 pt5">
							<select name="elem_def_template_id" id="elem_def_template_id" class="selectpicker">
								<option value="0">Comprehensive</option>
								<?php

									$sql = "SELECT chart_template_id FROM users WHERE id = '".$_SESSION["authId"]."' ";
									$row = sqlQuery($sql);
									if($row != false){
										$elem_def_template_id = $row["chart_template_id"];
									}

									$sql = "SELECT * FROM chart_template order by temp_name";
									$rez = imw_query($sql);
									while($row=imw_fetch_assoc($rez)){
										$tmp_id = $row["id"];
										$temp_name = $row["temp_name"];

										if(!empty($temp_name)){

											$tmp_sel="";
											if(!empty($elem_def_template_id) && $elem_def_template_id == $tmp_id){
												$tmp_sel = " SELECTED ";
											}
											echo "<option value=\"".$tmp_id."\" ".$tmp_sel.">".$temp_name."</option>";
										}
									}

								?>
							</select>
						</div>
						<div class="col-sm-6"></div>
					</div>
				</div>
			</div>

			<div class="row pt10">
				<div class="col-sm-12">
					<h3 style="margin-top: 10px;"><strong>WNL Values for Exams</strong></h3>
				</div>
			</div>


			<div class="row wndHead">
				<div class="col-sm-2 pt10 pb10 text-left brdrRight"><label style="padding: 0;">Exam</label></div>
				<div class="col-sm-6 pt10 pb10 text-left"><label style="padding: 0;">WNL</label></div>
			</div>

			<div class="scroll-content mCustomScrollbar" style="height:<?php echo $_SESSION['wn_height']-496; ?>px">
				<div class="row">
					<div class="col-sm-2 wnlRight brdrRight">
						<div class="row">
							<div class="col-sm-1"></div>
							<div class="col-sm-11 pd5">
								<label>Chart Template</label>
							</div>
						</div>
					</div>
					<div class="col-sm-4 pd5">
						<?php getChartTempSelect($chart_temp_id); ?>
					</div>
					<div class="col-sm-6"></div>
				</div>

		<?php
		foreach($arrExamNames as $key => $val){

			if(is_array($val) ){
				$subExm =$val;
echo '<div class="row">
					<div class="col-sm-2 wnlRightGreen brdrRight">
						<div class="row">
							<div class="col-sm-12">
								<label>'.$key.'</label>
							</div>
						</div>
					</div>
					<div class="col-sm-4"></div>
					<div class="col-sm-6"></div>
				</div>';
				foreach($subExm as $key2 => $val2){

					$exam = $val2;

					$elem_wnl_name="elem_wnl[]";
					$elem_wnl_id="elem_wnl_".$key."_".$key2;
					$elem_wnl_val=getExamWnl($exam, $chart_temp_id);
					$exam_show = $exam;
					if($exam_show=="Blood Vessels"){ $exam_show="Vessels"; }

echo '<div class="row">
					<div class="col-sm-2 wnlRight brdrRight">
						<div class="row">
							<div class="col-sm-1"></div>
							<div class="col-sm-11">
								<label>'.$exam_show.'</label>
								<input type="hidden" name="elem_exam[]" value="'.$exam.'" />
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<input type="text" id="'.$elem_wnl_id.'" name="'.$elem_wnl_name.'" value="'.$elem_wnl_val.'" class="form-control" />
					</div>
					<div class="col-sm-6"></div>
				</div>';
				}

			}else{
				$exam=$val;

				$elem_wnl_name="elem_wnl[]";
				$elem_wnl_id="elem_wnl_".$key;
				$elem_wnl_val=getExamWnl($exam, $chart_temp_id);

echo '<div class="row">
					<div class="col-sm-2 wnlRight brdrRight">
						<div class="row">
							<div class="col-sm-1"></div>
							<div class="col-sm-11">
								<label>'.$exam.'</label>
								<input type="hidden" name="elem_exam[]" value="'.$exam.'" />
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<input type="text" id="'.$elem_wnl_id.'" name="'.$elem_wnl_name.'" value="'.$elem_wnl_val.'" class="form-control" />
					</div>
					<div class="col-sm-6"></div>
				</div>';
			}
		}
		?>
			</div>
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-1 messagform pt5 pdb5">
					<button class="btn btn-success" type="submit">Save</button>
				</div>
				<div class="col-sm-9"></div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">

$('#frm_wnl').on('submit', function(){

	var formData = $(this).serialize();

	$.ajax({
		url: URL+'/interface/physician_console/wnl_charttemplate.php',
		data: formData,
		method: 'POST',
		success: function(resp)
		{
			resp = parseInt(resp);
			if(resp===1)
				$('.lftpanel #wnl_charttemplate').trigger('click');
			else
				alert('Data cannot be saved');
		}
	});

});
</script>
