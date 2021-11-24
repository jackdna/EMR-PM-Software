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
?>
<?php
/*
File: pupil_2.php
Purpose: This file provides Pupil exam in work view.
Access Type : Direct
*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>

<!-- Bootstrap -->

<!-- Bootstrap -->
<link type="text/css" href="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvexmcss" rel="stylesheet">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
<script>
var zPath = "<?php echo $GLOBALS['rootdir'];?>";
var elem_per_vo = "<?php echo $elem_per_vo;?>";
var sess_pt = "<?php echo $patient_id; ?>";
var rootdir = "<?php echo $rootdir;?>";
var finalize_flag = "<?php echo $finalize_flag;?>";
var isReviewable = "<?php echo $isReviewable;?>";
var logged_user_type = "<?php echo $logged_user_type;?>";
var examName = "Pupil";
var arrSubExams = new Array("Pupil");
var ProClr=<?php echo $ProClr;?>;
//var drawCntlNum=<?php echo $drawCntlNum; ?>;

</script>    
    
    
</head>
<body class="exam_pop_up">
<div id="dvloading">Loading! Please wait..</div>
<!-- AJAX -->
<div id="img_load" class="process_loader"></div>
<!-- AJAX -->
<form id="pupil" name="pupil" action="saveCharts.php" method="post" onSubmit="freezeElemAll('0')" enctype="multipart/form-data" class="frcb"> <!-- saveCharts.php -->
<input type="hidden" id="elem_saveForm" name="elem_saveForm" value="Pupil">
<input type="hidden" name="elem_editMode_load" value="<?php echo $elem_editMode;?>">
<input type="hidden" name="elem_pupilId" value="<?php echo $elem_pupilId;?>">
<input type="hidden" name="elem_examDate" value="<?php echo $elem_examDate;?>">
<input name="firstAlert" type="hidden"/>
<input type="hidden" name="elem_descPupil" value="<?php echo $elem_descPupil;?>">
<input type="hidden" name="elem_purged" value="<?php echo $elem_purged;?>">

<!-- hidden -->
<input type="hidden" id="elem_perrla" name="elem_perrla" value="<?php echo $elem_perrla;?>">
<input type="hidden" name="elem_wnl" value="<?php echo $elem_wnl;?>">
<input type="hidden" name="elem_isPositive" value="<?php echo $elem_isPositive;?>">
<input type="hidden" name="elem_wnlPupilOd" value="<?php echo $elem_wnlPupilOd;?>">
<input type="hidden" name="elem_wnlPupilOs" value="<?php echo $elem_wnlPupilOs;?>">
<input type="hidden" name="elem_wnlPupil" value="<?php echo $elem_wnl;?>">
<input type="hidden" name="elem_posPupil" value="<?php echo $elem_isPositive;?>">
<input type="hidden" name="elem_ncPupil" value="<?php echo $elem_noChange;?>">
<input type="hidden" name="elem_examined_no_change" value="<?php echo $elem_noChange;?>">
<input type="hidden" id="elem_utElems" name="elem_utElems" value="<?php echo $elem_utElems;?>">
<input type="hidden" id="elem_utElems_cur" name="elem_utElems_cur" value="<?php echo $elem_utElems_cur;?>">
<!-- hidden -->	

<!-- newET_changeIndctr -->
<input type="hidden" name="elem_chng_divPupil_Od" id="elem_chng_divPupil_Od" value="<?php echo $elem_chng_divPupil_Od;?>">
<input type="hidden" name="elem_chng_divPupil_Os" id="elem_chng_divPupil_Os" value="<?php echo $elem_chng_divPupil_Os;?>">
<!-- newET_changeIndctr -->

<div class=" container-fluid">
<div class="whtbox exammain ">

	<div class="clearfix"></div>
<div><!-- divnoname -->

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
	<?php	
	foreach($arrTabs as $key => $val){
	$tmp2=$key;
	$tmp = ($key == "Pupil") ? "active" : "";
	?>
	<li role="presentation" class="<?php echo $tmp;?>"><a href="#div<?php echo $key;?>" aria-controls="div<?php echo $key;?>" role="tab" data-toggle="tab" onclick="changeTab('<?php echo $key;?>')" id="tab<?php echo $key;?>" > <span id="flagimage_<?php echo $tmp2;?>" class=" flagPos"></span> <?php echo $val;?></a></li>
	<?php
	}
	?>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="divPupil">
	<div class="examhd">
		<div class="row">
			<div class="col-sm-1">
				<?php if($finalize_flag == 1){?>
				<label id="chart_status" class="chart_status label label-danger pull-left">Finalized</label>
				<?php }?>
			</div>
			<div class="col-sm-7 form-inline pharmo">
				<input type="checkbox" id="elem_pharmadilated" name="elem_pharmadilated" value="Pharmacologically dilated on arrival" <?php echo $chkPhrma;?> >
				<label id="lblPharmaDilate" for="elem_pharmadilated">Pharmacologically dilated on arrival</label>
				
				<select id="elem_pharmadilated_eye" name="elem_pharmadilated_eye" class="form-control minimal" onclick="clickPhrma(this)">
				<option value=""></option>
				<option value="OU" <?php if($elem_pharmadilated_eye=="OU") echo "selected"; ?> >OU</option>
				<option value="OD" <?php if($elem_pharmadilated_eye=="OD") echo "selected"; ?> >OD</option>
				<option value="OS" <?php if($elem_pharmadilated_eye=="OS") echo "selected"; ?> >OS</option>
				</select>
			</div>
			<div class="col-sm-4">
				<span id="examFlag" class="glyphicon flagWnl "></span>
				<button class="wnl_btn" type="button" onClick="setwnl();" onmouseover="showEyeDD(1)" onmouseout="showEyeDD(0)">WNL</button>
				
				<input type="checkbox" id="elem_noChange"  name="elem_noChange" value="1" onClick="setNC2();" 
				<?php echo ($examined_no_change == "1") ? "checked=\"checked\"" : "" ;?> class="frcb"  >
				<label class="lbl_nochange frcb" for="elem_noChange">NO Change</label>
				
				<?php /*if (constant('AV_MODULE')=='YES'){?>
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/video_play.png" alt=""  onclick="record_MultiMedia_Message()" title="Record MultiMedia Message" /> 
				<img src="<?php echo $GLOBALS['webroot'];?>/library/images/play-button.png" alt="" onclick="play_MultiMedia_Messages()" title="Play MultiMedia Messages" />
				<?php }*/ ?>
				
			</div>
		</div>
	</div>
	<div class="clearfix"> </div>

   <div class="table-responsive">
   <table class="table table-bordered table-striped" >
	   <tr>
	     <td colspan="7" align="center">
			<span class="flgWnl_2" id="flagWnlOd" ></span>
			<!--<img src="../../library/images/tstod.png" alt=""/>-->
			<div class="checkboxO"><label class="od cbold">OD</label></div>
		
			<input type="checkbox" id="elem_Od_surgiAlt" name="elem_Od_surgiAlt" value="Surgically Altered" 
			<?php echo ($elem_Od_surgiAlt == "Surgically Altered") ? "checked=\"checked\"" : "" ;?> 
			onClick="checkwnls()" ><label id="d_surgiAlt_od" for="elem_Od_surgiAlt">Surgically Altered</label>
		</td>
	     <td align="center" class="bilat link_BL_All" onclick="check_bl_strt('r2,r3,r4,r5,r6,r7,r8,r9,r10,r11,surgiAlt')"><strong >Bilateral</strong></td>
	     <td colspan="7" align="center">
			<span class="flgWnl_2" id="flagWnlOs"></span>
			<!--<img src="../../library/images/tstos.png" alt=""/>-->
			<div class="checkboxO"><label class="os cbold">OS</label></div>
			
			<input type="checkbox" id="elem_Os_surgiAlt" name="elem_Os_surgiAlt" value="Surgically Altered" 
			<?php echo ($elem_Os_surgiAlt == "Surgically Altered") ? "checked=\"checked\"" : "" ;?> 
			onClick="checkwnls()" ><label id="d_surgiAlt_os" for="elem_Os_surgiAlt" >Surgically Altered</label>
		</td>
	   </tr>
	   
	   <tr>
	     <td colspan="7" align="center" width="48%" id="od_pr" class="surgicalhd"></td>
	     <td width="100" align="center" class="bilat perbg" id="lbl_pr" onClick="chk_Perrla();">PERRLA</td>
	     <td colspan="7" align="center" width="48%" id="os_pr" class="surgicalhd"></td>
	   </tr>
	   
	   <tr>
	     <td align="left"><strong>Scotopic</strong></td>
	     <td align="left"><strong>Photopic</strong></td>
	     <td align="left"><strong>Dilated</strong></td>
	     <td align="left"><strong>Shape</strong></td>
	     <td align="left"><strong>RL</strong></td>
	     <td align="left"><strong>RA</strong></td>
	     <td align="left"><strong>APD</strong></td>
	     <td rowspan="11" align="center" class="bilat" onclick="check_bl_strt('r2,r3,r4,r5,r6,r7,r8,r9,r10,r11,surgiAlt')"><strong>BL</strong></td>
	     <td align="left"><strong>Scotopic</strong></td>
	     <td align="left"><strong>Photopic</strong></td>
	     <td align="left"><strong>Dilated</strong></td>
	     <td align="left"><strong>Shape</strong></td>
	     <td align="left"><strong>RL</strong></td>
	     <td align="left"><strong>RA</strong></td>
	     <td align="left"><strong>APD</strong></td>
	     </tr>
	     
	   <tr id="d_r2">
	     <td align="left"><input type="checkbox" onClick="checkwnls()"  id="od_1" name="elem_sizeOd_mm1" value="1mm"  <?php echo ($elem_sizeOd_mm1 == "1mm") ? "checked=\"checked\"" : "" ;?>><label for="od_1">1mm</label></td>
	     <td align="left"><input type="checkbox" onClick="checkwnls()"  id="od_11" name="elem_size2Od_mm1" value="1mm"  <?php echo ($elem_size2Od_mm1 == "1mm") ? "checked=\"checked\"" : "" ;?>><label for="od_11">1mm</label></td>
	     <td align="left"><input type="checkbox" onClick="checkwnls()"  id="od_35" name="elem_dilatedOd_mm1" value="1mm"  <?php echo ($elem_dilatedOd_mm1 == "1mm") ? "checked=\"checked\"" : "" ;?>><label for="od_35">1mm</label></td>
	     <td align="left"><input type="checkbox" onClick="checkwnls()"  id="od_9" name="elem_shapeOd_round" value="Round" <?php echo ($elem_shapeOd_round == "Round") ? "checked=\"checked\"" : "" ;?>><label for="od_9">Round</label></td>
	     <td align="left"><input type="checkbox" onClick="checkwnls()"  id="od_12" name="elem_rlOd_trace" value="Yes" <?php echo ($elem_rlOd_trace == "Yes") ? "checked=\"checked\"" : "" ;?>><label for="od_12">Yes</label></td>
	     <td align="left"><input type="checkbox"  onclick="checkwnls()" id="od_18" name="elem_raOd_trace" value="Yes" <?php echo ($elem_raOd_trace == "Yes") ? "checked=\"checked\"" : "" ;?>><label for="od_18">Yes</label></td>
	     <td align="left"><input type="checkbox" onClick="checkAbsent(this)"  id="od_23" name="elem_apdOd_neg" value="Absent" <?php echo ($elem_apdOd_neg == "-ve" || $elem_apdOd_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="od_23" class="LC">Absent</label></td>
	     <td align="left"><input type="checkbox" onClick="checkwnls()" id="os_1" name="elem_sizeOs_mm1" value="1mm" <?php echo ($elem_sizeOs_mm1 == "1mm") ? "checked=\"checked\"" : "" ;?>><label for="os_1">1mm</label></td>
	     <td align="left"><input type="checkbox" onClick="checkwnls()" id="os_11" name="elem_size2Os_mm1" value="1mm" <?php echo ($elem_size2Os_mm1 == "1mm") ? "checked=\"checked\"" : "" ;?>><label for="os_11">1mm</label></td>
	     <td align="left"><input type="checkbox" onClick="checkwnls()" id="os_35" name="elem_dilatedOs_mm1" value="1mm" <?php echo ($elem_dilatedOs_mm1 == "1mm") ? "checked=\"checked\"" : "" ;?>><label for="os_35">1mm</label></td>
	     <td align="left"><input type="checkbox"  onclick="checkwnls()" id="os_9" name="elem_shapeOs_round" value="Round" <?php echo ($elem_shapeOs_round == "Round") ? "checked=\"checked\"" : "" ;?>><label for="os_9">Round</label></td>
	     <td align="left"><input type="checkbox" onClick="checkwnls()"  id="os_12" name="elem_rlOs_trace" value="Yes" <?php echo ($elem_rlOs_trace == "Yes") ? "checked=\"checked\"" : "" ;?>><label for="os_12">Yes</label></td>
	     <td align="left"><input type="checkbox" onClick="checkwnls()"  id="os_18" name="elem_raOs_trace" value="Yes" <?php echo ($elem_raOs_trace == "Yes") ? "checked=\"checked\"" : "" ;?>><label for="os_18">Yes</label></td>
	     <td align="left"><input type="checkbox" onClick="checkAbsent(this)"  id="os_23" name="elem_apdOs_neg" value="Absent" <?php echo ($elem_apdOs_neg == "-ve" || $elem_apdOs_neg == "Absent") ? "checked=\"checked\"" : "" ;?>><label for="os_23" class="LC">Absent</label></td>
	     </tr>
	     
	   <tr id="d_r3">
		<td><input type="checkbox" onClick="checkwnls()"  id="od_2" name="elem_sizeOd_mm2" value="2mm" <?php echo ($elem_sizeOd_mm2 == "2mm") ? "checked=\"checked\"" : "" ;?>><label for="od_2">2mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()"  id="od_17" name="elem_size2Od_mm2" value="2mm" <?php echo ($elem_size2Od_mm2 == "2mm") ? "checked=\"checked\"" : "" ;?>><label for="od_17">2mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()"  id="od_36" name="elem_dilatedOd_mm2" value="2mm" <?php echo ($elem_dilatedOd_mm2 == "2mm") ? "checked=\"checked\"" : "" ;?>><label for="od_36">2mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_10" name="elem_shapeOd_irregular" value="Irregular" <?php echo ($elem_shapeOd_irregular == "Irregular") ? "checked=\"checked\"" : "" ;?>><label for="od_10" >Irregular</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_13" name="elem_rlOd_pos1" value="Sluggish" <?php echo ($elem_rlOd_pos1 == "Sluggish") ? "checked=\"checked\"" : "" ;?>><label for="od_13" >Sluggish</label></td>
		<td><input type="checkbox"  onclick="checkwnls()" id="od_19" name="elem_raOd_pos1" value="Sluggish" <?php echo ($elem_raOd_pos1 == "Sluggish") ? "checked=\"checked\"" : "" ;?>><label for="od_19" >Sluggish</label></td>
		<td><input type="checkbox" onClick="checkAbsent(this)"  id="od_24" name="elem_apdOd_trace" value="Trace" <?php echo ($elem_apdOd_trace == "Trace") ? "checked=\"checked\"" : "" ;?>><label for="od_24" class="LC">Trace</label></td>			     
		<td><input type="checkbox" onClick="checkwnls()" id="os_2" name="elem_sizeOs_mm2" value="2mm" <?php echo ($elem_sizeOs_mm2 == "2mm") ? "checked=\"checked\"" : "" ;?>><label for="os_2">2mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_17" name="elem_size2Os_mm2" value="2mm" <?php echo ($elem_size2Os_mm2 == "2mm") ? "checked=\"checked\"" : "" ;?>><label for="os_17">2mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_36" name="elem_dilatedOs_mm2" value="2mm" <?php echo ($elem_dilatedOs_mm2 == "2mm") ? "checked=\"checked\"" : "" ;?>><label for="os_36">2mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_10" name="elem_shapeOs_irregular" value="Irregular" <?php echo ($elem_shapeOs_irregular == "Irregular") ? "checked=\"checked\"" : "" ;?>><label for="os_10">Irregular</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_13" name="elem_rlOs_pos1" value="Sluggish" <?php echo ($elem_rlOs_pos1 == "Sluggish") ? "checked=\"checked\"" : "" ;?>><label for="os_13">Sluggish</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_19" name="elem_raOs_pos1" value="Sluggish" <?php echo ($elem_raOs_pos1 == "Sluggish") ? "checked=\"checked\"" : "" ;?>><label for="os_19">Sluggish</label></td>
		<td><input type="checkbox" onClick="checkAbsent(this)" id="os_24" name="elem_apdOs_trace" value="Trace" <?php echo ($elem_apdOs_trace == "Trace") ? "checked=\"checked\"" : "" ;?>><label for="os_24" class="LC">Trace</label></td>

	     
	     </tr>
	     
	   <tr id="d_r4">
	   
		<td><input type="checkbox" onClick="checkwnls()"  id="od_3" name="elem_sizeOd_mm3" value="3mm" <?php echo ($elem_sizeOd_mm3 == "3mm") ? "checked=\"checked\"" : "" ;?>><label for="od_3">3mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()"  id="od_29" name="elem_size2Od_mm3" value="3mm" <?php echo ($elem_size2Od_mm3 == "3mm") ? "checked=\"checked\"" : "" ;?>><label for="od_29">3mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()"  id="od_37" name="elem_dilatedOd_mm3" value="3mm" <?php echo ($elem_dilatedOd_mm3 == "3mm") ? "checked=\"checked\"" : "" ;?>><label for="od_37">3mm</label></td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkwnls()"  id="od_14" name="elem_rlOd_pos2" value="Brisk" <?php echo ($elem_rlOd_pos2 == "Brisk") ? "checked=\"checked\"" : "" ;?>><label for="od_14">Brisk</label></td>
		<td><input type="checkbox"  onclick="checkwnls()" id="od_20" name="elem_raOd_pos2" value="Brisk" <?php echo ($elem_raOd_pos2 == "Brisk") ? "checked=\"checked\"" : "" ;?>><label for="od_20">Brisk</label></td>
		<td><input type="checkbox" onClick="checkAbsent(this)" id="od_25" name="elem_apdOd_pos1" value="1+" <?php echo ($elem_apdOd_pos1 == "+1" || $elem_apdOd_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="od_25" class="LC">1+</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_3" name="elem_sizeOs_mm3" value="3mm" <?php echo ($elem_sizeOs_mm3 == "3mm") ? "checked=\"checked\"" : "" ;?>><label for="os_3">3mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_29" name="elem_size2Os_mm3" value="3mm" <?php echo ($elem_size2Os_mm3 == "3mm") ? "checked=\"checked\"" : "" ;?>><label for="os_29">3mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_37" name="elem_dilatedOs_mm3" value="3mm" <?php echo ($elem_dilatedOs_mm3 == "3mm") ? "checked=\"checked\"" : "" ;?>><label for="os_37">3mm</label></td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_14" name="elem_rlOs_pos2" value="Brisk" <?php echo ($elem_rlOs_pos2 == "Brisk") ? "checked=\"checked\"" : "" ;?>><label for="os_14">Brisk</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_20" name="elem_raOs_pos2" value="Brisk" <?php echo ($elem_raOs_pos2 == "Brisk") ? "checked=\"checked\"" : "" ;?>><label for="os_20">Brisk</label></td>
		<td><input type="checkbox" onClick="checkAbsent(this)" id="os_25" name="elem_apdOs_pos1" value="1+" <?php echo ($elem_apdOs_pos1 == "+1" || $elem_apdOs_pos1 == "1+") ? "checked=\"checked\"" : "" ;?>><label for="os_25" class="LC">1+</label></td>
		
	     </tr>
	     
	   <tr id="d_r5">
		<td><input type="checkbox" onClick="checkwnls()" id="od_4" name="elem_sizeOd_mm4" value="4mm" <?php echo ($elem_sizeOd_mm4 == "4mm") ? "checked=\"checked\"" : "" ;?>><label for="od_4">4mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_30" name="elem_size2Od_mm4" value="4mm" <?php echo ($elem_size2Od_mm4 == "4mm") ? "checked=\"checked\"" : "" ;?>><label for="od_30">4mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_38" name="elem_dilatedOd_mm4" value="4mm" <?php echo ($elem_dilatedOd_mm4 == "4mm") ? "checked=\"checked\"" : "" ;?>><label for="od_38">4mm</label></td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_15" name="elem_rlOd_none" value="None" <?php echo ($elem_rlOd_none == "None") ? "checked=\"checked\"" : "" ;?>><label for="od_15">None</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_21" name="elem_raOd_none" value="None" <?php echo ($elem_raOd_none == "None") ? "checked=\"checked\"" : "" ;?>><label for="od_21">None</label></td>
		<td><input type="checkbox" onClick="checkAbsent(this)" id="od_26" name="elem_apdOd_pos2" value="2+" <?php echo ($elem_apdOd_pos2 == "+2" || $elem_apdOd_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="od_26" class="LC">2+</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_4" name="elem_sizeOs_mm4" value="4mm" <?php echo ($elem_sizeOs_mm4 == "4mm") ? "checked=\"checked\"" : "" ;?>><label for="os_4">4mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_30" name="elem_size2Os_mm4" value="4mm" <?php echo ($elem_size2Os_mm4 == "4mm") ? "checked=\"checked\"" : "" ;?>><label for="os_30">4mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_38" name="elem_dilatedOs_mm4" value="4mm" <?php echo ($elem_dilatedOs_mm4 == "4mm") ? "checked=\"checked\"" : "" ;?>><label for="os_38">4mm</label></td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_15" name="elem_rlOs_none" value="None" <?php echo ($elem_rlOs_none == "None") ? "checked=\"checked\"" : "" ;?>><label for="os_15">None</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_21" name="elem_raOs_none" value="None" <?php echo ($elem_raOs_none == "None") ? "checked=\"checked\"" : "" ;?>><label for="os_21">None</label></td>
		<td><input type="checkbox" onClick="checkAbsent(this)" id="os_26" name="elem_apdOs_pos2" value="2+" <?php echo ($elem_apdOs_pos2 == "+2" || $elem_apdOs_pos2 == "2+") ? "checked=\"checked\"" : "" ;?>><label for="os_26" class="LC">2+</label></td>
	     </tr>
	     
	   <tr id="d_r6">
		<td><input type="checkbox" onClick="checkwnls()" id="od_5" name="elem_sizeOd_mm5" value="5mm" <?php echo ($elem_sizeOd_mm5 == "5mm") ? "checked=\"checked\"" : "" ;?>><label for="od_5">5mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_31" name="elem_size2Od_mm5" value="5mm" <?php echo ($elem_size2Od_mm5 == "5mm") ? "checked=\"checked\"" : "" ;?>><label for="od_31">5mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_39" name="elem_dilatedOd_mm5" value="5mm" <?php echo ($elem_dilatedOd_mm5 == "5mm") ? "checked=\"checked\"" : "" ;?>><label for="od_39">5mm</label></td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkAbsent(this)" id="od_27" name="elem_apdOd_pos3" value="3+" <?php echo ($elem_apdOd_pos3 == "+3" || $elem_apdOd_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="od_27" class="LC">3+</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_5" name="elem_sizeOs_mm5" value="5mm" <?php echo ($elem_sizeOs_mm5 == "5mm") ? "checked=\"checked\"" : "" ;?>><label for="os_5">5mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_31" name="elem_size2Os_mm5" value="5mm" <?php echo ($elem_size2Os_mm5 == "5mm") ? "checked=\"checked\"" : "" ;?>><label for="os_31">5mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_39" name="elem_dilatedOs_mm5" value="5mm" <?php echo ($elem_dilatedOs_mm5 == "5mm") ? "checked=\"checked\"" : "" ;?>><label for="os_39">5mm</label></td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkAbsent(this)" id="os_27" name="elem_apdOs_pos3" value="3+" <?php echo ($elem_apdOs_pos3 == "+3" || $elem_apdOs_pos3 == "3+") ? "checked=\"checked\"" : "" ;?>><label for="os_27" class="LC">3+</label></td>
	     </tr>
	     
	   <tr id="d_r7">
		<td><input type="checkbox" onClick="checkwnls()" id="od_6" name="elem_sizeOd_mm6" value="6mm" <?php echo ($elem_sizeOd_mm6 == "6mm") ? "checked=\"checked\"" : "" ;?>><label for="od_6">6mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_32" name="elem_size2Od_mm6" value="6mm" <?php echo ($elem_size2Od_mm6 == "6mm") ? "checked=\"checked\"" : "" ;?>><label for="od_32">6mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_40" name="elem_dilatedOd_mm6" value="6mm" <?php echo ($elem_dilatedOd_mm6 == "6mm") ? "checked=\"checked\"" : "" ;?>><label for="od_40">6mm</label></td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkAbsent(this)" id="od_28" name="elem_apdOd_pos4" value="4+" <?php echo ($elem_apdOd_pos4 == "+4" || $elem_apdOd_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="od_28" class="LC">4+</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_6" name="elem_sizeOs_mm6" value="6mm" <?php echo ($elem_sizeOs_mm6 == "6mm") ? "checked=\"checked\"" : "" ;?>><label for="os_6">6mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_32" name="elem_size2Os_mm6" value="6mm" <?php echo ($elem_size2Os_mm6 == "6mm") ? "checked=\"checked\"" : "" ;?>><label for="os_32">6mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_40" name="elem_dilatedOs_mm6" value="6mm" <?php echo ($elem_dilatedOs_mm6 == "6mm") ? "checked=\"checked\"" : "" ;?>><label for="os_40">6mm</label></td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkAbsent(this)" id="os_28" name="elem_apdOs_pos4" value="4+" <?php echo ($elem_apdOs_pos4 == "+4" || $elem_apdOs_pos4 == "4+") ? "checked=\"checked\"" : "" ;?>><label for="os_28" class="LC">4+</label></td>
	     </tr>
	     
	   <tr id="d_r8">
		<td><input type="checkbox" onClick="checkwnls()" id="od_7" name="elem_sizeOd_mm7" value="7mm" <?php echo ($elem_sizeOd_mm7 == "7mm") ? "checked=\"checked\"" : "" ;?>><label for="od_7">7mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_33" name="elem_size2Od_mm7" value="7mm" <?php echo ($elem_size2Od_mm7 == "7mm") ? "checked=\"checked\"" : "" ;?>><label for="od_33">7mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_41" name="elem_dilatedOd_mm7" value="7mm" <?php echo ($elem_dilatedOd_mm7 == "7mm") ? "checked=\"checked\"" : "" ;?>><label for="od_41">7mm</label></td>

		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkAbsent(this)" id="od_43" name="elem_apdOd_rapd" value="RAPD" <?php echo ($elem_apdOd_rapd == "RAPD") ? "checked=\"checked\"" : "" ;?>><label for="od_43" class="LC">RAPD</label></td>	
		<td><input type="checkbox" onClick="checkwnls()" id="os_7" name="elem_sizeOs_mm7" value="7mm" <?php echo ($elem_sizeOs_mm7 == "7mm") ? "checked=\"checked\"" : "" ;?>><label for="os_7">7mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_33" name="elem_size2Os_mm7" value="7mm" <?php echo ($elem_size2Os_mm7 == "7mm") ? "checked=\"checked\"" : "" ;?>><label for="os_33">7mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_41" name="elem_dilatedOs_mm7" value="7mm" <?php echo ($elem_dilatedOs_mm7 == "7mm") ? "checked=\"checked\"" : "" ;?>><label for="os_41">7mm</label></td>

		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkAbsent(this)" id="os_43"	name="elem_apdOs_rapd" value="RAPD" <?php echo ($elem_apdOs_rapd == "RAPD") ? "checked=\"checked\"" : "" ;?>><label for="os_43" class="LC">RAPD</label></td>
	     </tr>
	     
	   <tr id="d_r9">
		<td><input type="checkbox" onClick="checkwnls()" id="od_8" name="elem_sizeOd_mm8" value="8mm" <?php echo ($elem_sizeOd_mm8 == "8mm") ? "checked=\"checked\"" : "" ;?>><label for="od_8">8mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_34" name="elem_size2Od_mm8" value="8mm" <?php echo ($elem_size2Od_mm8 == "8mm") ? "checked=\"checked\"" : "" ;?>><label for="od_34">8mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_42" name="elem_dilatedOd_mm8" value="8mm" <?php echo ($elem_dilatedOd_mm8 == "8mm") ? "checked=\"checked\"" : "" ;?>><label for="od_42">8mm</label></td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_8" name="elem_sizeOs_mm8" value="8mm" <?php echo ($elem_sizeOs_mm8 == "8mm") ? "checked=\"checked\"" : "" ;?>><label for="os_8">8mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_34" name="elem_size2Os_mm8" value="8mm" <?php echo ($elem_size2Os_mm8 == "8mm") ? "checked=\"checked\"" : "" ;?>><label for="os_34">8mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_42" name="elem_dilatedOs_mm8" value="8mm" <?php echo ($elem_dilatedOs_mm8 == "8mm") ? "checked=\"checked\"" : "" ;?>><label for="os_42">8mm</label></td>

		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
	     </tr>
	     
	   <tr id="d_r11">
		<td><input type="checkbox" onClick="checkwnls()" id="od_44" name="elem_sizeOd_mm9" value="9mm" <?php echo ($elem_sizeOd_mm9 == "9mm") ? "checked=\"checked\"" : "" ;?>><label for="od_44">9mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_45" name="elem_size2Od_mm9" value="9mm" <?php echo ($elem_size2Od_mm9 == "9mm") ? "checked=\"checked\"" : "" ;?>><label for="od_45">9mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="od_46" name="elem_dilatedOd_mm9" value="9mm" <?php echo ($elem_dilatedOd_mm9 == "9mm") ? "checked=\"checked\"" : "" ;?>><label for="od_46">9mm</label></td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_44" name="elem_sizeOs_mm9" value="9mm" <?php echo ($elem_sizeOs_mm9 == "9mm") ? "checked=\"checked\"" : "" ;?>><label for="os_44">9mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_45" name="elem_size2Os_mm9" value="9mm" <?php echo ($elem_size2Os_mm9 == "9mm") ? "checked=\"checked\"" : "" ;?>><label for="os_45">9mm</label></td>
		<td><input type="checkbox" onClick="checkwnls()" id="os_46" name="elem_dilatedOs_mm9" value="9mm" <?php echo ($elem_dilatedOs_mm9 == "9mm") ? "checked=\"checked\"" : "" ;?>><label for="os_46">9mm</label></td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
	     </tr>
	     
	   <tr id="d_r10">
		<td colspan="7" align="left"><textarea onBlur="checkwnls()"  id="od_des" name="elem_pupilDescOd"  class="form-control" rows="3" placeholder="Comments"><?php echo ($elem_pupilDescOd);?></textarea></td>
		<td colspan="7" align="left"><textarea  onBlur="checkwnls()"  id="os_des" name="elem_pupilDescOs"  class="form-control" rows="3" placeholder="Comments"><?php echo ($elem_pupilDescOs);?></textarea></td>
	     </tr>
   
   </table>   
   </div>
   
    </div><!-- tab-panel -->
    
  </div><!-- tab-content -->

</div><!-- divnoname -->
<div class="clearfix"></div>

</div><!-- whtbox -->
</div><!-- container-fluid -->

<nav class="navbar navbar-inverse navbar-fixed-bottom btnbar">
	<div class="container center-block text-center">
		
		<?php if(($elem_per_vo != "1") && (($finalize_flag != 1) || ($isReviewable))){?> 
		<button type="button" class="btn btn-success navbar-btn" onclick="save_exam()">Done</button>
		<?php }?>
		
		<?php if(($elem_per_vo != "1") && (($finalize_flag != 1) || ($isReviewable))){?>
		<button type="button" class="btn btn-success navbar-btn" onclick="reset_exam()">Reset</button>
		<button type="button" class="btn btn-success navbar-btn" onclick="previous_exam()">Previous</button>
		<?php if(!empty($elem_editMode)){?>
		<button type="button" class="btn btn-success navbar-btn" onclick="purg_exam()">Purge</button>
		<?php }?>
		<?php }?>	
		<button type="button" class="btn btn-danger navbar-btn pull-right" onclick="cancel_exam()">Cancel</button>		
		
	</div>
</nav>
</form>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsexm"></script>

</body>
</html>