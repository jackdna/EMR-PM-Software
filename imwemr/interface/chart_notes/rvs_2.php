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
File: rvs_2.php
Purpose: This file contains RVS functionality in work view.
Access Type : Include file
*/

//--
/*
function rvs_get_dd_dsc_lvl($id, $fn){
	return "";
	return "<b id=\"".$id."\" onClick=\"".$fn."\" class=\"sm_base rvs_dt_dd\" >&#9660;</b>";//sm_base  //rvs_dt_dd
}
*/
//--

?>
<!-- RVS -->

<div id="div_rvs" onclick="stopClickBubble();"  >
<div id="header" class="handleDrag">
<label id="tabVision_Problem" class="rvsOn" onclick="displayTabRvs('Vision_Problem')">Vision Problem</label>
<label id="tabIrritation" onclick="displayTabRvs('Irritation')">Irritation</label>
<label id="tabPost_Segment" onclick="displayTabRvs('Post_Segment')">Post Segment</label>
<label id="tabNeuro" onclick="displayTabRvs('Neuro')">Neuro</label>
<label id="tabrvs_FollowUp" onclick="displayTabRvs('rvs_FollowUp')">Follow-Up</label>
<span class="btnClose glyphicon glyphicon-remove pull-right" onclick="$('#div_rvs').hide();"></span>
<div class="clearfix"></div>
</div>

<div id="Vision_Problem" class="tabSec">

<input type="hidden" name="elem_rvsIndicator" value="1"><?php //Used to indicate Save?>

<?php $tmp=""; ?>

<div class="row"><div class="subSec col-sm-6" >
<h2>Distance:</h2>
<p>
	<?php
		$str = "viewing TV";
		$tmp= (in_array($str,$arr_vpDis)) ? "checked=\"checked\"" : "";
	?>
	<b>Difficulty in:</b>
	<input id="div1" type="checkbox" name="elem_vpDis[]" value="viewing TV" onclick="setRVS(this,'-Vision Problem');" 
			<?php echo $tmp;?> />			
	<label for="div1" >Viewing TV, reading closed caption, news scrolls on TV</label>
	<?php ////echo ("dispTvText", "describeMe('Difficulty in', this, 'div1');"); ?>	
	
	
	<?php
		$str = "seeing street signs";
		$tmp= (in_array($str,$arr_vpDis)) ? "checked=\"checked\"" : "";
	?>
	<input id="div2" type="checkbox" name="elem_vpDis[]" value="seeing street signs" onclick="setRVS(this,'-Vision Problem')" 
			<?php echo $tmp;?>/>
	<label for="div2" >Seeing street signs</label>
	<?php //echo rvs_get_dd_dsc_lvl("streetSigns", "describeMe('Difficulty in', this, 'div2');"); ?>
	
	
	<?php
		$str = "driving";
		$tmp= (in_array($str,$arr_vpDis)) ? "checked=\"checked\"" : "";
	?>
	<input id="div3" type="checkbox" name="elem_vpDis[]" value="driving" onclick="setRVS(this,'-Vision Problem')" <?php echo $tmp;?>/>
	<label for="div3">Driving</label>
	<?php //echo rvs_get_dd_dsc_lvl("DrivingTd","describeMe('Difficulty in', this, 'div3');"); ?>
	
	<?php
		$str = "driving at night";
		$tmp= (in_array($str,$arr_vpDis)) ? "checked=\"checked\"" : "";
	?>
	<input id="div4" type="checkbox" name="elem_vpDis[]" value="driving at night" onclick="setRVS(this,'-Vision Problem')" <?php echo $tmp;?>>
	<label for="div4">Driving at night</label>
	<?php //echo rvs_get_dd_dsc_lvl("driveNightTd","describeMe('Difficulty in', this, 'div4');"); ?>
	
	<?php
		$str = "driving due to glare from headlights or Sun";
		$tmp= (in_array($str,$arr_vpDis)) ? "checked=\"checked\"" : "";
	?>
	<input id="div5" type="checkbox" name="elem_vpDis[]" value="driving due to glare from headlights or Sun" 
			onclick="setRVS(this,'-Vision Problem')" <?php echo $tmp;?>>
	<label for="div5">Driving due to glare from headlights or Sun</label>
	<?php //echo rvs_get_dd_dsc_lvl("drivingGlare","describeMe('Difficulty in', this, 'div5');"); ?>
	
	<?php
		$str = "reading/viewing blackboard";
		$tmp= (in_array($str,$arr_vpDis)) ? "checked=\"checked\"" : "";
	?>
	<input id="e_rvsBB" type="checkbox" name="elem_vpDis[]" value="reading/viewing blackboard" 
			onclick="setRVS(this,'-Vision Problem')" <?php echo $tmp;?>>
	<label for="e_rvsBB">Reading/Viewing Blackboard</label>
	<?php //echo rvs_get_dd_dsc_lvl("l_rvsBB","describeMe('Difficulty in', this, 'e_rvsBB');"); ?>
	
	<?php
		$str = "recognizing people";
		$tmp= (in_array($str,$arr_vpDis)) ? "checked=\"checked\"" : "";
	?>
	<input id="div6" type="checkbox" name="elem_vpDis[]" value="recognizing people" onclick="setRVS(this,'-Vision Problem')" 
			<?php echo $tmp;?>>
	<label for="div6">Recognizing people</label>
	<?php //echo rvs_get_dd_dsc_lvl("ReceopleTd","describeMe('Difficulty in', this, 'div6');"); ?>
	
	<?php echo $ar_wv_hpi_opts["Vision Problem"]["Distance"]; //Custom HPI ?> 	
	
	<?php
		$str = "Other";
		$tmp= (in_array($str,$arr_vpDis)) ? "checked=\"checked\"" : "";
		$tmp = ($elem_vpDisOther) ? "checked=\"checked\"" : "";
		if($elem_vpDisOther) $otherVal = $elem_vpDisOther; else $otherVal = 'Other';
	?>
	<input id="vpDisChk" type="checkbox" name="elem_vpDis[]" value="Other" onclick="disOth(this,'elem_vpDisOther','elem_vpDisOther');" 
			<?php echo $tmp;?>>
	<label for="vpDisChk">
	<span >Other</span>
	<input type="text" id="elem_vpDisOther" name="elem_vpDisOther" value="<?php echo $elem_vpDisOther; ?>"  
			onchange="setRVS(this,'-Vision Problem', 'vpDisChk')">
	</label>
	<?php //echo rvs_get_dd_dsc_lvl("td_vpDisChk","describeMe('Difficulty in', this, 'elem_vpDisOther');"); ?>
</p>
</div>

<div class="subSec col-sm-6">
<h2>Glare</h2>
<p>
<b>Causing Poor Vision:</b>
<?php
	$str = "Sunlight";
	$tmp= (in_array($str,$arr_vpGlare)) ? "checked=\"checked\"" : "";
?>
<input id="div13" type="checkbox" name="elem_vpGlare[]" value="Sunlight" onclick="setRVS(this,'-Glare Problem')" <?php echo $tmp;?>>
<label for="div13">Sunlight</label>
<?php //echo rvs_get_dd_dsc_lvl("sunlight","describeMe('Glare', this, 'div13');"); ?>

<?php
	$str = "Headlights";
	$tmp= (in_array($str,$arr_vpGlare)) ? "checked=\"checked\"" : "";
?>
<input  id="div14" type="checkbox" name="elem_vpGlare[]" value="Headlights" onclick="setRVS(this,'-Glare Problem')" <?php echo $tmp;?>>
<label  for="div14">Headlights</label>
<?php //echo rvs_get_dd_dsc_lvl("headLights","describeMe('Glare', this, 'div14');"); ?>

<?php
	$str = "Nighttime Car headlights";
	$tmp= (in_array($str,$arr_vpGlare)) ? "checked=\"checked\"" : "";
?>
<input id="div15" type="checkbox" name="elem_vpGlare[]" value="Nighttime Car headlights" onclick="setRVS(this,'-Glare Problem')" 
	<?php echo $tmp;?>>
<label for="div15">Nighttime light - Car headlights, street lamps etc.</label>
<?php //echo rvs_get_dd_dsc_lvl("nightDrive","describeMe('Glare', this, 'div15');"); ?>

<?php echo $ar_wv_hpi_opts["Vision Problem"]["Glare"]; //Custom HPI ?>

</p>
</div >

<div class="subSec col-sm-6">
<h2>Mid Distance:</h2>
<p>
<b>Difficulty in:</b>
<?php
	$str = "reading computer";
	$tmp= (in_array($str,$arr_vpMidDis)) ? "checked=\"checked\"" : "";
?>

<input id="div7" type="checkbox" name="elem_vpMidDis[]" value="reading computer" onclick="setRVS(this,'-Vision Problem')" <?php echo $tmp;?>>
<label for="div7">Reading computer screen</label>
<?php //echo rvs_get_dd_dsc_lvl("readingComputer","describeMe('Difficulty in', this, 'div7');"); ?>

<?php
	$str = "viewing dashboard";
	$tmp= (in_array($str,$arr_vpMidDis)) ? "checked=\"checked\"" : "";
?>
<input id="div8" type="checkbox" name="elem_vpMidDis[]" value="viewing dashboard" onclick="setRVS(this,'-Vision Problem')" <?php echo $tmp;?>>
<label for="div8">Viewing car dashboard</label>
<?php //echo rvs_get_dd_dsc_lvl("dashBordTd","describeMe('Difficulty in', this, 'div8');"); ?>

<?php echo $ar_wv_hpi_opts["Vision Problem"]["Mid Distance"]; //Custom HPI ?>

<?php
	$str = "Other";
	$tmp= (in_array($str,$arr_vpMidDis)) ? "checked=\"checked\"" : "";
	$tmp= ($elem_vpMidDisOther) ? "checked=\"checked\"" : "";
	if($elem_vpMidDisOther) $midDisOther = $elem_vpMidDisOther; else $midDisOther = 'Other';
?>
<input id="vpMidDisChk" type="checkbox" name="elem_vpMidDis[]" value="Other" 
		onclick="showOther(this, 'elem_vpMidDisOther','-Vision Problem', 'elem_vpMidDisOther')" <?php echo $tmp;?>>
<label for="vpMidDisChk">
<span >Other</span>
<input type="text" id="elem_vpMidDisOther" name="elem_vpMidDisOther" value="<?php echo $elem_vpMidDisOther; ?>"  
		onchange="setRVS(this,'-Vision Problem','vpMidDisChk')">
</label>
<?php //echo rvs_get_dd_dsc_lvl("td_vpMidDisChk","describeMe('Difficulty in', this, 'elem_vpMidDisOther');"); ?>
</p>
</div>
<div class="clearfix"></div>
<div class="subSec col-sm-6">
<h2>Near:</h2>
<p>
<b>Difficulty in:</b>
<?php
	$str = "reading books";
	$tmp= (in_array($str,$arr_vpNear)) ? "checked=\"checked\"" : "";
?>
<input id="div10" type="checkbox" name="elem_vpNear[]" value="reading books" onclick="setRVS(this,'-Vision Problem')" <?php echo $tmp;?>>
<label for="div10">Reading fine print, books, newspaper, instructions etc.</label>
<?php //echo rvs_get_dd_dsc_lvl("readFinePrint","describeMe('Difficulty in', this, 'div10');"); ?>

<?php
	$str = "reading fine labels";
	$tmp= (in_array($str,$arr_vpNear)) ? "checked=\"checked\"" : "";
?>
<input id="div11" type="checkbox" name="elem_vpNear[]" value="reading fine labels" 
		onclick="setRVS(this,'-Vision Problem')" <?php echo $tmp;?>>
<label for="div11">Reading fine labels(e.g. medication)</label>
<?php //echo rvs_get_dd_dsc_lvl("readFineLables","describeMe('Difficulty in', this, 'div11');"); ?>

<?php echo $ar_wv_hpi_opts["Vision Problem"]["Near"]; //Custom HPI ?>

<?php
	$str = "Other";
	$tmp= (in_array($str,$arr_vpNear)) ? "checked=\"checked\"" : "";
	$tmp= ($elem_vpNearOther) ? "checked=\"checked\"" : "";
	if($elem_vpNearOther) $nearOther = $elem_vpNearOther; else $nearOther = 'Other';
?>
<input id="vpNearChk" type="checkbox" name="elem_vpNear[]" value="Other" <?php echo $tmp;?> 
		onclick="showOther(this, 'elem_vpNearOther','-Vision Problem', 'elem_vpNearOther')">
<label for="vpNearChk">
<span >Other</span>
<input type="text" id="elem_vpNearOther" name="elem_vpNearOther" value="<?php echo $elem_vpNearOther; ?>"  
		onchange="setRVS(this,'-Vision Problem','vpNearChk')" >
</label>
<?php //echo rvs_get_dd_dsc_lvl("td_vpNearChk","describeMe('Difficulty in', this, 'elem_vpNearOther');"); ?>
</p>
</div>

<div class="subSec col-sm-6">
<h2>Other:</h2>
<p>
<?php
	$str = "Poor color vision";
	$tmp= (in_array($str,$arr_vpOther)) ? "checked=\"checked\"" : "";
?>
<input id="div16" type="checkbox" name="elem_vpOther[]" value="Poor color vision" 
		onclick="setRVS(this,'-Other Vision Problem')" <?php echo $tmp;?>>
<label for="div16">Poor color vision</label>
<?php //echo rvs_get_dd_dsc_lvl("poorCVision","describeMe('Difficulty in Vision', this, 'div16');"); ?>
<?php
	$str = "Poor depth perception";
	$tmp= (in_array($str,$arr_vpOther)) ? "checked=\"checked\"" : "";
?>
<input id="div17" type="checkbox" name="elem_vpOther[]" value="Poor depth perception" 
		onclick="setRVS(this,'-Other Vision Problem')" <?php echo $tmp;?>>
<label for="div17">Poor depth perception</label>
<?php //echo rvs_get_dd_dsc_lvl("poorDepth","describeMe('Difficulty in Vision', this, 'div17');"); ?>
<?php
	$str = "Poor peripheral vision";
	$tmp= (in_array($str,$arr_vpOther)) ? "checked=\"checked\"" : "";
?>
<input id="div18" type="checkbox" name="elem_vpOther[]" value="Poor peripheral vision" 
		onclick="setRVS(this,'-Other Vision Problem')" <?php echo $tmp;?>>
<label for="div18">Poor peripheral vision</label>
<?php //echo rvs_get_dd_dsc_lvl("peripheralVision","describeMe('Difficulty in Vision', this, 'div18');"); ?>
<?php
	$str = "Halos";
	$tmp= (in_array($str,$arr_vpOther)) ? "checked=\"checked\"" : "";
?>
<input id="div19" type="checkbox" name="elem_vpOther[]" value="Halos" onclick="setRVS(this,'-Other Vision Problem')" <?php echo $tmp;?>>
<label for="div19">Halos</label>
<?php //echo rvs_get_dd_dsc_lvl("halos","describeMe('Difficulty in Vision', this, 'div19');"); ?>
<?php
	$str = "Starburst";
	$tmp= (in_array($str,$arr_vpOther)) ? "checked=\"checked\"" : "";
?>
<input id="div20" type="checkbox" name="elem_vpOther[]" value="Starburst" onclick="setRVS(this,'-Other Vision Problem')" <?php echo $tmp;?>>
<label for="div20">Starbursts</label>
<?php //echo rvs_get_dd_dsc_lvl("starburst","describeMe('Difficulty in Vision', this, 'div20');"); ?>
<?php echo $ar_wv_hpi_opts["Vision Problem"]["Other"]; //Custom HPI ?>

<?php
	$str = "Other";
	//$tmp= (in_array($str,$arr_vpOther)) ? "checked=\"checked\"" : "";
	$tmp= ($elem_vpOtherOther) ? "checked=\"checked\"" : "";
	if($elem_vpOtherOther) $vpOther = $elem_vpOtherOther; else $vpOther = 'Other';
?>
<input id="vpOtherChk" type="checkbox" name="elem_vpOther[]" value="Other" 
	onclick="disOth(this,'elem_vpOtherOther','elem_vpOtherOther')" <?php echo $tmp;?>>
<label for="vpOtherChk">
<span >Other</span>
<input type="text" id="elem_vpOtherOther" name="elem_vpOtherOther" value="<?php echo $elem_vpOtherOther; ?>"  
		onchange="setRVS(this,'-Other Vision Problem','vpOtherChk')">
</label>
<?php //echo rvs_get_dd_dsc_lvl("td_vpOtherChk","describeMe('Difficulty in Vision', this, 'elem_vpOtherOther');"); ?>
</p>
</div>

<div class="subSec col-sm-12 patincomt">
<h2>Patient Comments:</h2>
<p>
<textarea id="vpComment" name="vpComment"  class="txt_10 all_black_border scrol_div" onchange="setRVS(this,'-Patient Comments','');"><?php echo $vpComment;?></textarea>
</p>
</div>
</div>
</div>

<div id="Irritation" class="tabSec">
<div class="row">
<div class="subSec col-sm-5 ">
<h2>Lids - External:</h2>
<p>
<input type="checkbox" name="lidsItchingYesNo" 
<?php if(($lidsYesNo == 'No') || ($lidsYesNo == "No External Lids Irritation")) {echo "checked";} ?> 
	value="No External Lids Irritation" id="itchingLisNo" onClick="itchYesNoFn(this);"><label for="itchingLisNo" style="float:left;">No</label>
<input type="checkbox" name="lidsItchingYesNo" 
	<?php if($lidsYesNo == 'Yes' || $lidsYesNo == 'External Lids Irritation') echo "checked"; ?> value="External Lids Irritation" id="itchingLisYes" onClick="return itchYesNoFn(this);">
	<label for="itchingLisYes" style="float:left;margin-left:15px;clear:none;">Yes</label>
<?php
	$str = "Itching";
	$tmp= (in_array($str,$arr_irrLidsExt)) ? "checked=\"checked\"" : "";
?>
<input id="div21" type="checkbox" <?php if($lidsYesNo == 'No') echo 'Disabled=Disabled'; ?> name="elem_irrLidsExt[]" value="Itching"
	onclick="setRVS(this,'-Irritation Lids')" <?php echo $tmp;?>>
<label for="div21">Itching</label>
<?php //echo rvs_get_dd_dsc_lvl("Itching","describeMe('Lids', this, 'div21');"); ?>
<?php
	$str = "Burning";
	$tmp= (in_array($str,$arr_irrLidsExt)) ? "checked=\"checked\"" : "";
?>
<input <?php if($lidsYesNo == 'No') echo 'Disabled=Disabled'; ?> id="div22" type="checkbox" name="elem_irrLidsExt[]" value="Burning"
	onclick="setRVS(this,'-Irritation Lids')" <?php echo $tmp;?>>
<label for="div22">Burning</label>
<?php //echo rvs_get_dd_dsc_lvl("BurningLids","describeMe('Lids', this, 'div22');"); ?>
<?php
	$str = "Red";
	$tmp= (in_array($str,$arr_irrLidsExt)) ? "checked=\"checked\"" : "";
?>
<input <?php if($lidsYesNo == 'No') echo 'Disabled=Disabled'; ?> id="div23" type="checkbox" name="elem_irrLidsExt[]" value="Red"
	onclick="setRVS(this,'-Irritation Lids')" <?php echo $tmp;?>>
<label for="div23">Red</label>
<?php //echo rvs_get_dd_dsc_lvl("Red","describeMe('Lids', this, 'div23');"); ?>
<?php
	$str = "Swelling";
	$tmp= (in_array($str,$arr_irrLidsExt)) ? "checked=\"checked\"" : "";
?>
<input <?php if($lidsYesNo == 'No') echo 'Disabled=Disabled'; ?> id="div24" type="checkbox" name="elem_irrLidsExt[]" value="Swelling"
	onclick="setRVS(this,'-Irritation Lids')" <?php echo $tmp;?>>
<label for="div24">Swelling</label>
<?php //echo rvs_get_dd_dsc_lvl("Swelling","describeMe('Lids', this, 'div24');"); ?>
<?php echo $ar_wv_hpi_opts["Irritation"]["Lids - External"]; //Custom HPI ?>
<?php
	$str = "Other";
	//$tmp= (in_array($str,$arr_irrLidsExt)) ? "checked=\"checked\"" : "";
	$tmp= ($elem_irrLidsExtOther) ? "checked=\"checked\"" : "";
	if($elem_irrLidsExtOther) $irrLidsExtOther = $elem_irrLidsExtOther; else $irrLidsExtOther = 'Other';
?>
<input id="irrLidsExtChk" <?php if($lidsYesNo == 'No') echo 'Disabled=Disabled'; ?> type="checkbox" name="elem_irrLidsExt[]" value="Other"
	onclick="disOth(this,'elem_irrLidsExtOther','elem_irrLidsExtOther')" <?php echo $tmp;?>>
<label for="irrLidsExtChk">
<span >Other</span>
<input type="text" id="elem_irrLidsExtOther" name="elem_irrLidsExtOther" value="<?php echo $elem_irrLidsExtOther; ?>"  onchange="setRVS(this,'-Irritation Lids','irrLidsExtChk')">
</label>
<?php //echo rvs_get_dd_dsc_lvl("td_irrLidsExtChk","describeMe('Lids', this, 'elem_irrLidsExtOther');"); ?>
</p>
</div>
 
<div class="subSec col-sm-6 col-sm-offset-1">
<h2>Ocular:</h2>
<p>
<?php
	$str = "Dry Eyes";
	$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
?>
<input id="div25" type="checkbox" name="elem_irrOcu[]" value="Dry Eyes" onclick="setRVS(this,'-Irritation Ocular')" <?php echo $tmp;?>>
<label for="div25">Dry Eyes</label>
<?php //echo rvs_get_dd_dsc_lvl("DryEyes","describeMe('Ocular', this, 'div25');"); ?>
<?php
	$str = "Tearing";
	$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
?>
<input id="div26" type="checkbox" name="elem_irrOcu[]" value="Tearing" onclick="setRVS(this,'-Irritation Ocular')" <?php echo $tmp;?>>
<label for="div26">Tearing</label>
<?php //echo rvs_get_dd_dsc_lvl("Tearing","describeMe('Ocular', this, 'div26');"); ?>
<?php
	$str = "Episodic sharp pain";
	$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
?>
<input id="div27" type="checkbox" name="elem_irrOcu[]" value="Episodic sharp pain" 
	onclick="setRVS(this,'-Irritation Ocular')" <?php echo $tmp;?>>
<label for="div27">Episodic sharp pain (lasting seconds)</label>
<?php //echo rvs_get_dd_dsc_lvl("EpisodicsharpPain","describeMe('Ocular', this, 'div27');"); ?>
<?php
	$str = "Gritty eyes Mild FB Sensation";
	$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
?>
<input id="div28" type="checkbox" name="elem_irrOcu[]" value="Gritty eyes Mild FB Sensation" onclick="setRVS(this,'-Irritation Ocular')" 
	<?php echo $tmp;?>>
<label for="div28">Gritty eyes/ Mild FB Sensation</label>
<?php //echo rvs_get_dd_dsc_lvl("FBSensation","describeMe('Ocular', this, 'div28');"); ?>
<?php
	$str = "Burning";
	$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
?>
<input id="div29" type="checkbox" name="elem_irrOcu[]" value="Burning" onclick="setRVS(this,'-Irritation Ocular')" <?php echo $tmp;?>>
<label for="div29">Burning</label>
<?php //echo rvs_get_dd_dsc_lvl("BurningOcular","describeMe('Ocular', this, 'div29');"); ?>
<?php
	$str = "Itching";
	$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
	/*$tmp= ($elem_irrOcuItchingType) ? "checked=\"checked\"" : "";
	if($elem_irrOcuItchingType) $irrOcuItchingType = $elem_irrOcuItchingType; else $irrOcuItchingType = 'Other';
	*/

?>
<input id="irrOcuChk" type="checkbox" name="elem_irrOcu[]" value="Itching" 
		onclick="disOth(this,'tdItchOcu'); setRVS(this,'-Irritation Ocular');" <?php echo $tmp;?>>
<label for="irrOcuChk">Itching</label>
<?php //echo rvs_get_dd_dsc_lvl("ItchingOcularTd","describeMe('Ocular', this, 'irrOcuChk');"); ?>


<input id="itching1" type="checkbox" class="radiotype" name="elem_irrOcuItchingType" value="Mild Itching" onclick="setRVS_radio(this,this,'-Irritation Ocular','irrOcuChk')" 
	<?php echo ($elem_irrOcuItchingType == "Mild Itching") ? "checked=\"checked\"" : "" ; ?>>
<label for="itching1" class="noclr" >Mild</label>

<input id="itching2" type="checkbox" class="radiotype" name="elem_irrOcuItchingType" value="Severe Itching" onclick="setRVS_radio(this,this,'-Irritation Ocular','irrOcuChk')" <?php echo ($elem_irrOcuItchingType == "Severe Itching") ? "checked=\"checked\"" : "" ; ?>>
<label for="itching2" class="noclr" >Severe</label>

<input id="itching3" type="checkbox" class="radiotype" name="elem_irrOcuItchingType" value="Seasonal allergies Itching" 
onclick="setRVS_radio(this,'-Irritation Ocular','irrOcuChk')" <?php echo ($elem_irrOcuItchingType == "Seasonal allergies Itching") ? "checked=\"checked\"" : "" ; ?>>
<label for="itching3" class="noclr" >Seasonal allergies</label>

<?php
	$str = "Redness";
	$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
?>
<input id="div30" type="checkbox" name="elem_irrOcu[]" value="Redness" onclick="setRVS(this,'-Irritation Ocular')" <?php echo $tmp;?>>
<label for="div30">Redness</label>
<?php //echo rvs_get_dd_dsc_lvl("Redness","describeMe('Ocular', this, 'div30');"); ?>
<?php
	$str = "Severe FB sensation";
	$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
?>
<input id="div31" type="checkbox" name="elem_irrOcu[]" value="Severe FB sensation" 
		onclick="setRVS(this,'-Irritation Ocular')" <?php echo $tmp;?>>
<label for="div31">Severe FB sensation</label>
<?php //echo rvs_get_dd_dsc_lvl("SevereFBsensation","describeMe('Ocular', this, 'div31');"); ?>
<?php
	$str = "Discharge";
	$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
?>
<input id="div32" type="checkbox" name="elem_irrOcu[]" value="Discharge" onclick="setRVS(this,'-Irritation Ocular')" <?php echo $tmp;?>>
<label for="div32">Discharge</label>
<?php //echo rvs_get_dd_dsc_lvl("Discharge","describeMe('Ocular', this, 'div32');"); ?>
<?php
	$str = "Photophobia";
	$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
?>
<input id="div33" type="checkbox" name="elem_irrOcu[]" value="Photophobia" onclick="setRVS(this,'-Irritation Ocular')" <?php echo $tmp;?>>
<label for="div33">Photophobia</label>
<?php //echo rvs_get_dd_dsc_lvl("Photophobia","describeMe('Ocular', this, 'div33');"); ?>
<?php
	$str = "Pressure sensation";
	$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
?>
<input id="div34" type="checkbox" name="elem_irrOcu[]" value="Pressure sensation" 
	onclick="disOth(this,'tdPsOcu'); setRVS(this,'-Irritation Ocular');" <?php echo $tmp;?>>
<label for="div34">Pressure sensation</label>
<?php //echo rvs_get_dd_dsc_lvl("PressureSensation","describeMe('Ocular', this, 'div34');"); ?>

<input type="checkbox" class="radiotype" id="elem_irrOcuPresSensType1" name="elem_irrOcuPresSensType" value="No Sinusitis" onclick="setRVS_radio(this, 'tdPsOcu','elem_irrOcuPresSensTypeHidden','elem_irrOcuPresSensType')" <?php echo ($elem_irrOcuPresSensType == "No Sinusitis"||$elem_irrOcuPresSensType == "ho Sinusitis") ? "checked=\"checked\"" : "" ; ?>>
<label for="elem_irrOcuPresSensType1" class="noclr">No Sinusitis</label>

<input type="checkbox" class="radiotype" id="elem_irrOcuPresSensType2" name="elem_irrOcuPresSensType" value="post nasal drip" onclick="setRVS_radio(this, 'tdPsOcu','elem_irrOcuPresSensTypeHidden','elem_irrOcuPresSensType')" <?php echo ($elem_irrOcuPresSensType == "post nasal drip") ? "checked=\"checked\"" : "" ; ?>>
<label for="elem_irrOcuPresSensType2" class="noclr" >Post nasal drip</label>

<input type="hidden" name="elem_irrOcuPresSensTypeHidden" value="" onclick="setRVS(this,'-Irritation Ocular','Pressure sensation Type');" class="radiotype">

<?php echo $ar_wv_hpi_opts["Irritation"]["Ocular"]; //Custom HPI ?>

<?php
	$str = "Other";
	//$tmp= (in_array($str,$arr_irrOcu)) ? "checked=\"checked\"" : "";
	$tmp= ($elem_irrOcuOther) ? "checked=\"checked\"" : "";
	if($elem_irrOcuOther) $irrOcuOther = $elem_irrOcuOther; else $irrOcuOther = 'Other';

?>
<input id="irrOcuOtherChk" type="checkbox" name="elem_irrOcu[]" value="Other" 
			onclick="disOth(this,'elem_irrOcuOther','elem_irrOcuOther')" <?php echo $tmp;?>>
<label for="irrOcuOtherChk">
<span >Other</span>
<input type="text" id="elem_irrOcuOther" name="elem_irrOcuOther" value="<?php echo $elem_irrOcuOther; ?>" 
		onchange="setRVS(this,'-Irritation Ocular','irrOcuOtherChk');">
</label>
<?php //echo rvs_get_dd_dsc_lvl("td_irrOcuOtherChk","describeMe('Ocular', this, 'elem_irrOcuOther');"); ?>
</p>
</div>
</div>
</div>

<?php //Post_Segment ?>
<div id="Post_Segment" class="tabSec">
<div class="row">

<div class="col-sm-5">
<div class="subSec col-sm-12">
<p>
<input id="div35" type="checkbox" name="elem_postSegSpots" value="Spots" onclick="setRVS(this,'-Post Segment')" 
			<?php echo ($elem_postSegSpots == "Spots") ? "checked=\"checked\"" : "" ;?>>
<label for="div35">Spots</label>
<?php //echo rvs_get_dd_dsc_lvl("Spots","describeMe('Post Segment', this, 'div35');"); ?>
</p>
</div>

<div class="subSec col-sm-12">
<h2>Flashing Lights:</h2>
<p>
<?php
if(empty($noFlashing)){
	$noFlashing = (in_array("No Flashing Lights", $arr_postSegFL)) ? "Yes" : "";
}
?>
<input id="div81" name="elem_postSegFL[]" value="No Flashing Lights" 
<?php if(($noFlashing == 'Yes') || ($noFlashing == "No Flashing Lights")) echo 'CHECKED'; ?> 
onClick="noFlashFn(this);" type="checkbox">
<label for="div81">No Flashing Lights</label>
<?php
	$str = "Increasing";
	$tmp= (in_array($str,$arr_postSegFL)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFlashing == 'Yes') || ($noFlashing == "No Flashing Lights")) echo 'disabled=disabled'; ?> id="div76" type="checkbox" name="elem_postSegFL[]" value="Increasing" onclick="setRVS(this,'-Flashing Lights')" <?php echo $tmp; ?>>
<label for="div76">Increasing</label>
<?php //echo rvs_get_dd_dsc_lvl("Increasing","describeMe('Flashing Lights', this, 'div76');"); ?>
<?php
	$str = "Decreasing";
	$tmp= (in_array($str,$arr_postSegFL)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFlashing == 'Yes') || ($noFlashing == "No Flashing Lights")) echo 'disabled=disabled'; ?> id="div77" type="checkbox" name="elem_postSegFL[]" value="Decreasing" onclick="setRVS(this,'-Flashing Lights')" <?php echo $tmp;?>>
<label for="div77">Decreasing</label>
<?php //echo rvs_get_dd_dsc_lvl("Decreasing","describeMe('Flashing Lights', this, 'div77');"); ?>
<?php
	$str = "Same";
	$tmp= (in_array($str,$arr_postSegFL)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFlashing == 'Yes') || ($noFlashing == "No Flashing Lights")) echo 'disabled=disabled'; ?> id="div78" type="checkbox" name="elem_postSegFL[]" value="Same" onclick="setRVS(this,'-Flashing Lights')" <?php echo $tmp;?>>
<label for="div78">Same</label>
<?php //echo rvs_get_dd_dsc_lvl("Same","describeMe('Flashing Lights', this, 'div78');"); ?>
<?php
	$str = "New onset";
	$tmp= (in_array($str,$arr_postSegFL)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFlashing == 'Yes') || ($noFlashing == "No Flashing Lights")) echo 'disabled=disabled'; ?> id="div36" type="checkbox" name="elem_postSegFL[]" value="New onset" onclick="setRVS(this,'-Flashing Lights')" <?php echo $tmp;?>>
<label for="div36">New onset</label>
<?php //echo rvs_get_dd_dsc_lvl("NewOnset","describeMe('Flashing Lights', this, 'div36');"); ?>
<?php
	$str = "Sparks";
	$tmp= (in_array($str,$arr_postSegFL)) ? "checked=\"checked\"" : "";
	//$tmp= ($elem_postSegFLSparks) ? "checked=\"checked\"" : "";
	if($elem_postSegFLSparks) $segFLSparks = $elem_postSegFLSparks; else $segFLSparks = 'Other';

?>
<input <?php if(($noFlashing == 'Yes') || ($noFlashing == "No Flashing Lights")) echo 'disabled=disabled'; ?> id="div38" type="checkbox"
	name="elem_postSegFL[]" value="Sparks" 
	onclick="showOther(this, 'elem_postSegFLSparks','-Flashing Lights', 'elem_postSegFLSparks');" <?php echo $tmp;?>>
<label for="div38" >Sparks</label><?php//class="fullwidth"?><?php // ?>
<?php //echo rvs_get_dd_dsc_lvl("Sparks","describeMe('Flashing Lights', this, 'div38');"); ?>

<input type="text" id="elem_postSegFLSparks" name="elem_postSegFLSparks" value="<?php echo $elem_postSegFLSparks; ?>"  onchange="setRVS(this,'-Flashing Lights','div38')" class="txt_right">


<?php
	$str = "Lightning Bolts";
	//$tmp= (in_array($str,$arr_postSegFL)) ? "checked=\"checked\"" : "";
	$tmp= ($elem_postSegFLBolts) ? "checked=\"checked\"" : "";
	if($elem_postSegFLBolts) $segFLBolts = $elem_postSegFLBolts; else $segFLBolts = 'Other';
?>
<input <?php if(($noFlashing == 'Yes') || ($noFlashing == "No Flashing Lights")) echo 'disabled=disabled'; ?> id="div39" type="checkbox" 
	name="elem_postSegFL[]" value="Lightning Bolts" 
	onclick="showOther(this, 'elem_postSegFLBolts','-Flashing Lights', 'elem_postSegFLBolts');" <?php echo $tmp;?>>
<label  for="div39" >Lightning Bolts</label><?php //class="fullwidth"?>
<?php //echo rvs_get_dd_dsc_lvl("LighteningBolts","describeMe('Flashing Lights', this, 'div39');"); ?>

<input type="text" id="elem_postSegFLBolts" name="elem_postSegFLBolts" value="<?php echo $elem_postSegFLBolts; ?>"  onchange="setRVS(this,'-Flashing Lights', 'div39')" class="txt_right">


<?php
	$str = "Arcs lasting seconds";
	//$tmp= (in_array($str,$arr_postSegFL)) ? "checked=\"checked\"" : "";
	$tmp= ($elem_postSegFLArcs) ? "checked=\"checked\"" : "";
	if($elem_postSegFLArcs) $segFLArcs = $elem_postSegFLArcs; else $segFLArcs = 'Other';
?>
<input <?php if(($noFlashing == 'Yes') || ($noFlashing == "No Flashing Lights")) echo 'disabled=disabled'; ?> id="div40" type="checkbox" name="elem_postSegFL[]" value="Arcs lasting seconds" 
		onclick="showOther(this, 'elem_postSegFLArcs','-Flashing Lights', 'elem_postSegFLArcs');" <?php echo $tmp;?>>
<label  for="div40">Arcs - lasting seconds</label><?php //class="fullwidth"?>
<?php //echo rvs_get_dd_dsc_lvl("ArcsLastingSeconds","describeMe('Flashing Lights', this, 'div40');"); ?>

<input type="text" id="elem_postSegFLArcs" name="elem_postSegFLArcs" value="<?php echo $elem_postSegFLArcs; ?>"  	onchange="setRVS(this,'-Flashing Lights','div40');" class="txt_right" >


<?php
	$str = "Strobe lights many minutes or longer";
	$tmp= (in_array($str,$arr_postSegFL)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFlashing == 'Yes') || ($noFlashing == "No Flashing Lights")) echo 'disabled=disabled'; ?> id="div41" type="checkbox" name="elem_postSegFL[]" value="Strobe lights many minutes or longer" onclick="setRVS(this,'-Flashing Lights');" <?php echo $tmp;?>>

<label  for="div41">Strobe lights lasting many minutes or longer</label>
<?php //echo rvs_get_dd_dsc_lvl("StrobeLights","describeMe('Flashing Lights', this, 'div41');"); ?>
<?php
//<span ></span>
/*
<input type="text" name="elem_postSegFLStrobe" value="<?php echo $elem_postSegFLStrobe;?>" 
		onchange="setRVS(this,'-Flashing Lights','Strobe lights Other');">
*/
?>

<?php
		$str = "Visual Distortion";
		$tmp= (in_array($str,$arr_postSegFL)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFlashing == 'Yes') || ($noFlashing == "No Flashing Lights")) echo 'disabled=disabled'; ?> id="div42" type="checkbox" name="elem_postSegFL[]" value="Visual Distortion" onclick="setRVS(this,'-Flashing Lights')" <?php echo $tmp;?>>
<label for="div42">Visual Distortion</label>
<?php //echo rvs_get_dd_dsc_lvl("VisualDistortion","describeMe('Flashing Lights', this, 'div42');"); ?>
<?php echo $ar_wv_hpi_opts["Post Segment"]["Flashing Lights"]; //Custom HPI ?>
</p>
</div>
</div>


<div class="subSec col-sm-6 col-sm-offset-1" id="dv_floaters">
<h2>Floaters:</h2>
<p>
<?php
if(empty($noFloaters)){
	$noFloaters = (in_array("No Floaters", $arr_postSegFloat)) ? "Yes" : "";
}
?>
<input id="div82" name="elem_postSegFloat[]" value="No Floaters" <?php if(($noFloaters == 'Yes') || ($noFloaters == "No Floaters")) echo 'CHECKED'; ?> onClick="noFloatersFn(this);" type="checkbox">
<label for="div82">No Floaters</label>
<?php
	$str = "Increasing";
	$tmp= (in_array($str,$arr_postSegFloat)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFloaters == 'Yes') || ($noFloaters == "No Floaters")) echo 'disabled=disabled'; ?> id="div46" type="checkbox" name="elem_postSegFloat[]" value="Increasing" onclick="setRVS(this,'-Floaters')" <?php echo $tmp;?>>
<label for="div46">Increasing</label>
<?php //echo rvs_get_dd_dsc_lvl("IncreasingFloaters","describeMe('Floaters', this, 'div46');"); ?>
<?php
	$str = "Decreasing";
	$tmp= (in_array($str,$arr_postSegFloat)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFloaters == 'Yes') || ($noFloaters == "No Floaters")) echo 'disabled=disabled'; ?> id="div79" type="checkbox" name="elem_postSegFloat[]" value="Decreasing" onclick="setRVS(this,'-Floaters')" <?php echo $tmp;?>>
<label for="div79">Decreasing</label>
<?php //echo rvs_get_dd_dsc_lvl("DecreasingFloaters","describeMe('Floaters', this, 'div79');"); ?>
<?php
	$str = "Same";
	$tmp= (in_array($str,$arr_postSegFloat)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFloaters == 'Yes') || ($noFloaters == "No Floaters")) echo 'disabled=disabled'; ?> id="div80" type="checkbox" name="elem_postSegFloat[]" value="Same" onclick="setRVS(this,'-Floaters')" <?php echo $tmp;?>>
<label for="div80">Same</label>
<?php //echo rvs_get_dd_dsc_lvl("SameFloaters","describeMe('Floaters', this, 'div80');"); ?>
<?php
	$str = "New onset";
	$tmp= (in_array($str,$arr_postSegFloat)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFloaters == 'Yes') || ($noFloaters == "No Floaters")) echo 'disabled=disabled'; ?> id="div45" type="checkbox" name="elem_postSegFloat[]" value="New onset" onclick="setRVS(this,'-Floaters')" <?php echo $tmp;?>>

<label for="div45">New onset</label>
<?php //echo rvs_get_dd_dsc_lvl("NewOnsetFloaters","describeMe('Floaters', this, 'div45');"); ?>
<?php
	$str = "Cobwebs";
	$tmp= (in_array($str,$arr_postSegFloat)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFloaters == 'Yes') || ($noFloaters == "No Floaters")) echo 'disabled=disabled'; ?> id="div47" type="checkbox"
	name="elem_postSegFloat[]" value="Cobwebs"
	onclick="showOther(this, 'elem_postSegFloatCobwebs','-Floaters', 'elem_postSegFloatCobwebs');"
	 <?php echo $tmp;?>>
<label for="div47" >Cobwebs</label>
<?php //echo rvs_get_dd_dsc_lvl("Cobwebs","describeMe('Floaters', this, 'div47');"); //class="fullwidth"?>

<input type="text" id="elem_postSegFloatCobwebs" name="elem_postSegFloatCobwebs" value="<?php echo $elem_postSegFloatCobwebs;?>"  onchange="setRVS(this,'-Floaters','div47')" class="txt_right">


<?php
	$str = "Black spots";
	$tmp= (in_array($str,$arr_postSegFloat)) ? "checked=\"checked\"" : "";
?>
<input <?php if(($noFloaters == 'Yes') || ($noFloaters == "No Floaters")) echo 'disabled=disabled'; ?> id="div48" type="checkbox" 
	name="elem_postSegFloat[]" value="Black spots" 
	onclick="showOther(this, 'elem_postSegFloatBlackSpots','-Floaters', 'elem_postSegFloatBlackSpots');" <?php echo $tmp;?>>
<label for="div48" >Black spots</label>
<?php //echo rvs_get_dd_dsc_lvl("Blackspots","describeMe('Floaters', this, 'div48');"); //class="fullwidth"?>

<input type="text" id="elem_postSegFloatBlackSpots" name="elem_postSegFloatBlackSpots" value="<?php echo $elem_postSegFloatBlackSpots;?>"  onchange="setRVS(this,'-Floaters','div48');" class="txt_right">

<?php echo $ar_wv_hpi_opts["Post Segment"]["Floaters"]; //Custom HPI ?>
</p>
</div>


<div class="subSec col-sm-6 col-sm-offset-1">
<h2>Amsler Grid:</h2>
<p>
<?php
	$str = "Normal";
	$tmp= (in_array($str,$arr_postSegAmsler)) ? "checked=\"checked\"" : "";
?>
<input id="div43" type="checkbox" name="elem_postSegAmsler[]" value="Normal" onclick="setRVS(this,'-Amsler Grid')" <?php echo $tmp;?>>
<label for="div43">Normal</label>
<?php //echo rvs_get_dd_dsc_lvl("Normal","describeMe('Amsler Grid', this, 'div43');"); ?>
<?php
	$str = "Abnormal";
	$tmp= (in_array($str,$arr_postSegAmsler)) ? "checked=\"checked\"" : "";
?>
<input id="div44" type="checkbox" name="elem_postSegAmsler[]" value="Abnormal" onclick="setRVS(this,'-Amsler Grid')" <?php echo $tmp;?>>
<label for="div44">Abnormal</label>
<?php //echo rvs_get_dd_dsc_lvl("Abnormal","describeMe('Amsler Grid', this, 'div44');"); ?>
<?php echo $ar_wv_hpi_opts["Post Segment"]["Amsler Grid"]; //Custom HPI ?>
</p>
</div>

</div>
</div>

<?php //Neuro ?>
<div id="Neuro" class="tabSec">
<div class="row">

<div class="col-sm-5">
<div class="subSec col-sm-12">
<h2>Double vision:</h2>
<p>
<?php
	$str = "Near";
	$tmp= (in_array($str,$arr_neuroDblVis)) ? "checked=\"checked\"" : "";
?>
<input id="div49" type="checkbox" name="elem_neuroDblVis[]" value="Near" onclick="setRVS(this,'-Double vision')" <?php echo $tmp;?>>
<label for="div49">Near</label>
<?php //echo rvs_get_dd_dsc_lvl("NearDoubleVision","describeMe('Double vision', this, 'div49');"); ?>
<?php
	$str = "Far";
	$tmp= (in_array($str,$arr_neuroDblVis)) ? "checked=\"checked\"" : "";
?>
<input id="div50" type="checkbox" name="elem_neuroDblVis[]" value="Far" onclick="setRVS(this,'-Double vision')" <?php echo $tmp;?>>
<label for="div50">Far</label>
<?php //echo rvs_get_dd_dsc_lvl("FarDoubleVision","describeMe('Double vision', this, 'div50');"); ?>
<?php
	$str = "Horizontal";
	$tmp= (in_array($str,$arr_neuroDblVis)) ? "checked=\"checked\"" : "";
?>
<input id="div51" type="checkbox" name="elem_neuroDblVis[]" value="Horizontal" onclick="setRVS(this,'-Double vision')" <?php echo $tmp;?>>
<label for="div51">Horizontal</label>
<?php //echo rvs_get_dd_dsc_lvl("horizontalTd","describeMe('Double vision', this, 'div51');"); ?>
<?php
	$str = "Vertical";
	$tmp= (in_array($str,$arr_neuroDblVis)) ? "checked=\"checked\"" : "";
?>
<input id="div52" type="checkbox" name="elem_neuroDblVis[]" value="Vertical" onclick="setRVS(this,'-Double vision')" <?php echo $tmp;?>>
<label for="div52">Vertical</label>
<?php //echo rvs_get_dd_dsc_lvl("Vertical","describeMe('Double vision', this, 'div52');"); ?>
<?php
	$str = "Diagonal";
	$tmp= (in_array($str,$arr_neuroDblVis)) ? "checked=\"checked\"" : "";
?>
<input id="div53" type="checkbox" name="elem_neuroDblVis[]" value="Diagonal" onclick="setRVS(this,'-Double vision')" <?php echo $tmp;?>>
<label for="div53">Diagonal</label>
<?php //echo rvs_get_dd_dsc_lvl("Diagonal","describeMe('Double vision', this, 'div53');"); ?>
<?php
		$str = "Visual field defect";
		$tmp= (in_array($str,$arr_neuroDblVis)) ? "checked=\"checked\"" : "";
?>
<input id="div54" type="checkbox" name="elem_neuroDblVis[]" value="Visual field defect" 
		onclick="setRVS(this,'-Double vision')" <?php echo $tmp;?>>
<label for="div54">Visual field defect, Scotoma</label>
<?php //echo rvs_get_dd_dsc_lvl("Scotoma","describeMe('Double vision', this, 'div54');"); ?>
<?php echo $ar_wv_hpi_opts["Neuro"]["Double Vision"]; //Custom HPI ?>
</p>
</div>

<div class="clearfix"></div>
<div class="subSec col-sm-12 " >
<h2>Temporal Arteritis symptoms:</h2>
<p>

<input type="checkbox" id="selectNoTHA" <?php if($THAYesNo == 'No') echo 'CHECKED'; ?> name="THAYesNo" value="No" onClick="return unselectSelect(this);"><label for="selectNoTHA">No</label>

<input type="checkbox" id="selectYesTHA" <?php if($THAYesNo == 'Yes' || $THAYesNo == 'Temporal Arteritis Symptoms') echo 'CHECKED'; ?> name="THAYesNo" value="Temporal Arteritis Symptoms" onClick="return unselectSelect(this);"><label for="selectYesTHA" style="clear:none;margin-left:15px;">Yes</label>
<?php
	$str = "Temporal HA";
	$tmp= (in_array($str,$arr_neuroTAS)) ? "checked=\"checked\"" : "";
?>
<input id="div55" <?php if($THAYesNo == 'No') echo 'disabled=disabled'; ?> type="checkbox" name="elem_neuroTAS[]" value="Temporal HA" onclick="setRVS(this,'-Temporal Arteritis symptoms')" <?php echo $tmp;?>>
<label for="div55">Temporal HA</label>
<?php //echo rvs_get_dd_dsc_lvl("TemporalHA","describeMe('Temporal Arteritis symptoms', this, 'div55');"); ?>
<?php
	$str = "Jaw claudication";
	$tmp= (in_array($str,$arr_neuroTAS)) ? "checked=\"checked\"" : "";
?>
<input <?php if($THAYesNo == 'No') echo 'disabled=disabled'; ?> id="div56" type="checkbox" name="elem_neuroTAS[]" value="Jaw claudication" onclick="setRVS(this,'-Temporal Arteritis symptoms')" <?php echo $tmp;?>>
<label for="div56">Jaw claudication</label>
<?php //echo rvs_get_dd_dsc_lvl("JawClaudication","describeMe('Temporal Arteritis symptoms', this, 'div56');"); ?>
<?php
	$str = "amaurosis fugax loss of vision";
	$tmp= (in_array($str,$arr_neuroTAS)) ? "checked=\"checked\"" : "";
?>
<input  <?php if($THAYesNo == 'No') echo 'disabled=disabled'; ?> id="div57" type="checkbox" name="elem_neuroTAS[]" value="amaurosis fugax loss of vision" onclick="setRVS(this,'-Temporal Arteritis symptoms')" <?php echo $tmp;?>>
<label for="div57">amaurosis fugax/loss of vision</label>
<?php //echo rvs_get_dd_dsc_lvl("amaurosisFugaxVision","describeMe('Temporal Arteritis symptoms', this, 'div57');"); ?>
<?php
	$str = "VF cuts";
	$tmp= (in_array($str,$arr_neuroTAS)) ? "checked=\"checked\"" : "";
?>
<input <?php if($THAYesNo == 'No') echo 'disabled=disabled'; ?> id="div58" type="checkbox" name="elem_neuroTAS[]" value="VF cuts" onclick="setRVS(this,'-Temporal Arteritis symptoms')" <?php echo $tmp;?>>
<label for="div58">VF cuts</label>
<?php //echo rvs_get_dd_dsc_lvl("VFCuts","describeMe('Temporal Arteritis symptoms', this, 'div58');"); ?>
<?php
	$str = "Fever";
	$tmp= (in_array($str,$arr_neuroTAS)) ? "checked=\"checked\"" : "";
?>
<input <?php if($THAYesNo == 'No') echo 'disabled=disabled'; ?> id="div59" type="checkbox" name="elem_neuroTAS[]" value="Fever" onclick="setRVS(this,'-Temporal Arteritis symptoms')" <?php echo $tmp;?>>
<label for="div59">Fever</label>
<?php //echo rvs_get_dd_dsc_lvl("Fever","describeMe('Temporal Arteritis symptoms', this, 'div59');"); ?>
<?php
	$str = "Chills";
	$tmp= (in_array($str,$arr_neuroTAS)) ? "checked=\"checked\"" : "";
?>
<input <?php if($THAYesNo == 'No') echo 'disabled=disabled'; ?> id="div60" type="checkbox" name="elem_neuroTAS[]" value="Chills" onclick="setRVS(this,'-Temporal Arteritis symptoms')" <?php echo $tmp;?>>
<label for="div60">Chills</label>
<?php //echo rvs_get_dd_dsc_lvl("Chills","describeMe('Temporal Arteritis symptoms', this, 'div60');"); ?>
<?php
	$str = "New onset of joint or muscle aches";
	$tmp= (in_array($str,$arr_neuroTAS)) ? "checked=\"checked\"" : "";
?>
<input id="div61" <?php if($THAYesNo == 'No') echo 'disabled=disabled'; ?> type="checkbox" name="elem_neuroTAS[]" value="New onset of joint or muscle aches" onclick="setRVS(this,'-Temporal Arteritis symptoms')" <?php echo $tmp;?>>
<label for="div61">New onset of joint or muscle aches</label>
<?php //echo rvs_get_dd_dsc_lvl("jointMuscleAches","describeMe('Temporal Arteritis symptoms', this, 'div61');"); ?>
<?php echo $ar_wv_hpi_opts["Neuro"]["Temporal Arteritis Symptoms"]; //Custom HPI ?>
<?php
	$str = "Other";
	//$tmp= (in_array($str,$arr_neuroTAS)) ? "checked=\"checked\"" : "";
	$tmp= ($elem_neuroTASOther) ? "checked=\"checked\"" : "";
	if($elem_neuroTASOther) $neuroTASOther = $elem_neuroTASOther; else $neuroTASOther = 'Other';
?>
<input <?php if($THAYesNo == 'No') echo 'disabled=disabled'; ?> id="neuroTASChk" type="checkbox" name="elem_neuroTAS[]" value="Other" onclick="disOth(this,'elem_neuroTASOther','elem_neuroTASOther')" <?php echo $tmp;?>>
<label for="neuroTASChk">
<span >Other</span>
<input type="text" id="elem_neuroTASOther" name="elem_neuroTASOther" value="<?php echo $elem_neuroTASOther; ?>"  onChange="setRVS(this,'-Temporal Arteritis symptoms','neuroTASChk');">
</label>
<?php //echo rvs_get_dd_dsc_lvl("td_neuroTASChk","describeMe('Temporal Arteritis symptoms', this, 'elem_neuroTASOther');"); ?>
</p>
</div>

</div>


<div class="subSec col-sm-6 col-sm-offset-1">
<h2>Headaches:</h2>
<p>
<?php
	$str = "Associated with visual tasks";
	$tmp= (in_array($str,$arr_neuroHeadaches)) ? "checked=\"checked\"" : "";
?>
<input id="div62" type="checkbox" name="elem_neuroHeadaches[]" value="Associated with visual tasks" 
		onclick="setRVS(this,'-Headaches')" <?php echo $tmp;?>>
<label for="div62">Associated with visual tasks</label>
<?php //echo rvs_get_dd_dsc_lvl("associatedvisualTasks","describeMe('Headaches', this, 'div62');"); ?>
<?php
	$str = "Wake up with headaches";
	$tmp= (in_array($str,$arr_neuroHeadaches)) ? "checked=\"checked\"" : "";
?>
<input id="div63" type="checkbox" name="elem_neuroHeadaches[]" value="Wake up with headaches" 
	onclick="setRVS(this,'-Headaches')" <?php echo $tmp;?>>
<label for="div63">Wake up with headaches</label>
<?php //echo rvs_get_dd_dsc_lvl("WakeUpWithHeadaches","describeMe('Headaches', this, 'div63');"); ?>
<?php echo $ar_wv_hpi_opts["Neuro"]["Headaches"]; //Custom HPI ?>
</p>
</div>

<div class="subSec col-sm-6 col-sm-offset-1" id="dv_mig_hdac"  >
<h2>Migraine Headaches:</h2>
<p>
<?php
	$str = "Headache preceded by aura such as";
	$tmp= (in_array($str,$arr_neuroMigHead)) ? "checked=\"checked\"" : "";
?>
<input id="div64" type="checkbox" name="elem_neuroMigHead[]" value="Headache preceded by aura such as" 
		onclick="setRVS(this,'-Migraine Headaches');"<?php echo $tmp;?>>
<label for="div64">Headache preceded by aura</label>
<?php //echo rvs_get_dd_dsc_lvl("HeadachePrecededByAura","describeMe('Migraine Headaches', this, 'div64');"); ?>
<?php
	$str = "Blurred vision";
	$tmp= (in_array($str,$arr_neuroMigHeadAura)) ? "checked=\"checked\"" : "";
?>
<input id="div65" type="checkbox" name="elem_neuroMigHeadAura[]" value="Blurred vision" 
	onclick="setRVS(this,'-Migraine Headaches')" <?php echo $tmp;?>>
<label for="div65">Blurred vision</label>
<?php //echo rvs_get_dd_dsc_lvl("BlurredVision","describeMe('Migraine Headaches', this, 'div65');"); ?>
<?php
	$str = "Loss of part of vision";
	$tmp= (in_array($str,$arr_neuroMigHeadAura)) ? "checked=\"checked\"" : "";
?>
<input id="div66" type="checkbox" name="elem_neuroMigHeadAura[]" value="Loss of part of vision" 
	onclick="setRVS(this,'-Migraine Headaches')" <?php echo $tmp;?>>
<label for="div66">Loss of part of vision</label>
<?php //echo rvs_get_dd_dsc_lvl("LossOfPartOfVision","describeMe('Migraine Headaches', this, 'div66');"); ?>
<?php
	$str = "Jagged Zig Zag lines";
	$tmp= (in_array($str,$arr_neuroMigHeadAura)) ? "checked=\"checked\"" : "";
?>
<input id="div67" type="checkbox" name="elem_neuroMigHeadAura[]" value="Jagged Zig Zag lines" 
		onclick="setRVS(this,'-Migraine Headaches')" <?php echo $tmp;?>>
<label for="div67">Jagged/Zig Zag lines</label>
<?php //echo rvs_get_dd_dsc_lvl("ZigZagLines","describeMe('Migraine Headaches', this, 'div67');"); ?>
<?php
	$str = "Constriction of vision";
	$tmp= (in_array($str,$arr_neuroMigHeadAura)) ? "checked=\"checked\"" : "";
?>
<input id="div68" type="checkbox" name="elem_neuroMigHeadAura[]" value="Constriction of vision" 
		onclick="setRVS(this,'-Migraine Headaches')" <?php echo $tmp;?>>
<label for="div68">Constriction of vision</label>
<?php //echo rvs_get_dd_dsc_lvl("ConstrictionOfVision","describeMe('Migraine Headaches', this, 'div68');"); ?>
<?php
	$str = "Flashing Lights";
	$tmp= (in_array($str,$arr_neuroMigHeadAura)) ? "checked=\"checked\"" : "";
?>
<input id="div69" type="checkbox" name="elem_neuroMigHeadAura[]" value="Flashing Lights" 
		onclick="setRVS(this,'-Migraine Headaches')" <?php echo $tmp;?>>
<label for="div69">Flashing Lights</label>
<?php //echo rvs_get_dd_dsc_lvl("FlashingLights","describeMe('Migraine Headaches', this, 'div69');"); ?>
<?php
	$str = "HA not relieved by acetaminophen or NSAIDS";
	$tmp= (in_array($str,$arr_neuroMigHeadAura)) ? "checked=\"checked\"" : "";
?>
<input id="div70" type="checkbox" name="elem_neuroMigHeadAura[]" value="HA not relieved by acetaminophen or NSAIDS" 
		onclick="setRVS(this,'-Migraine Headaches')" <?php echo $tmp;?>>
<label for="div70">HA not relieved by acetaminophen or NSAIDS</label>
<?php //echo rvs_get_dd_dsc_lvl("tyelenolOrNSAIDS","describeMe('Migraine Headaches', this, 'div70');"); ?>
<?php
	$str = "HA lasting for half hours or longer";
	$tmp= (in_array($str,$arr_neuroMigHeadAura)) ? "checked=\"checked\"" : "";
?>
<input id="div71" type="checkbox" name="elem_neuroMigHeadAura[]" value="HA lasting for half hours or longer" 
		onclick="setRVS(this,'-Migraine Headaches')" <?php echo $tmp;?>>
<label for="div71">HA lasting for 1/2 hours or longer</label>
<?php //echo rvs_get_dd_dsc_lvl("HALasting","describeMe('Migraine Headaches', this, 'div71');"); ?>
<?php
$str = "photosensitive";
$tmp= (in_array($str,$arr_neuroMigHeadAura)) ? "checked=\"checked\"" : "";
?>
<input id="div72" type="checkbox" name="elem_neuroMigHeadAura[]" value="photosensitive" 
		onclick="setRVS(this,'-Migraine Headaches')" <?php echo $tmp;?>>
<label for="div72">photosensitive</label>
<?php //echo rvs_get_dd_dsc_lvl("photosensitive","describeMe('Migraine Headaches', this, 'div72');"); ?>
<?php
	$str = "Family history of migraines";
	$tmp= (in_array($str,$arr_neuroMigHeadAura)) ? "checked=\"checked\"" : "";
?>
<input id="div73" type="checkbox" name="elem_neuroMigHeadAura[]" value="Family history of migraines" 
		onclick="setRVS(this,'-Migraine Headaches');" <?php echo $tmp;?>>
<label for="div73">Family history of migraines</label>
<?php //echo rvs_get_dd_dsc_lvl("migraines","describeMe('Migraine Headaches', this, 'div73');"); ?>
<?php echo $ar_wv_hpi_opts["Neuro"]["Migraine Headaches"]; //Custom HPI ?>
<?php
	$str = "Other";
	$tmp= (in_array($str,$arr_neuroMigHeadAura)) ? "checked=\"checked\"" : "";
?>
<input id="neuroMigHeadAuraChk" type="checkbox" name="elem_neuroMigHeadAura[]" value="Other" 
		onclick="disOth(this,'elem_neuroMigHeadAuraOther','elem_neuroMigHeadAuraOther')" <?php echo $tmp;?>>
<label for="neuroMigHeadAuraChk">
<span >Other</span>
<input type="text" id="elem_neuroMigHeadAuraOther" name="elem_neuroMigHeadAuraOther" value="<?php echo $elem_neuroMigHeadAuraOther;?>" 
		onchange="setRVS(this,'-Migraine Headaches','neuroMigHeadAuraChk');">
</label>
<?php //echo rvs_get_dd_dsc_lvl("td_neuroMigHeadAuraChk","describeMe('Migraine Headaches', this, 'elem_neuroMigHeadAuraOther');"); ?>
</p>
</div>

<div id="rvs_lossofvis" class="subSec col-sm-6 col-sm-offset-1">
<h2>Loss of vision:</h2>
<p>
<?php
	$str = "Amaurosis fugax";
	$tmp= (in_array($str,$arr_neuroVisLoss)) ? "checked=\"checked\"" : "";
	//onclick="showOther(this, 'tdOthfugax','-Loss of vision', '');"
?>
<input id="div74" type="checkbox" name="elem_neuroVisLoss[]" value="Amaurosis fugax" 
	onclick="setRVS(this,'-Loss of vision')"  <?php echo $tmp;?>>
<label for="div74">Amaurosis fugax</label>
<?php //echo rvs_get_dd_dsc_lvl("AmaurosisFugax","describeMe('Loss of vision', this, 'div74');"); ?>

<?php
/*
	$str = "Last Minutes";
	$tmp= (in_array($str,$arr_neuroVisLoss)) ? "checked=\"checked\"" : "";
?* >
<input type="checkbox" id="elem_fugaxLastMinutes" name="elem_neuroVisLoss[]" value="Last Minutes" 
		onclick="setRVS(this,'-Loss of vision')" <?php echo $tmp;?>>
<label for="elem_fugaxLastMinutes">Last Minutes</label>
<*?*php ////echo rvs_get_dd_dsc_lvl("LastMinutes","describeMe('Last Minutes', this, 'elem_fugaxLastMinutes');"); 
*/
?>

<input type="hidden" name="elem_neuroVisLossHidden" value="" onclick="setRVS(this,'-Loss of vision','Amaurosis fugax Other')">
<?php
	$str = "Complete Loss of vision";
	$tmp= (in_array($str,$arr_neuroVisLoss)) ? "checked=\"checked\"" : "";
?>
<input type="checkbox" id="elem_fugaxCompleteLoss" name="elem_neuroVisLoss[]" value="Complete Loss of vision" 
	onclick="setRVS(this,'-Loss of vision')" <?php echo $tmp;?>>
<label for="elem_fugaxCompleteLoss">Complete Loss of vision</label>
<?php //echo rvs_get_dd_dsc_lvl("CompleteLossOfVision","describeMe('Complete Loss of vision', this, 'elem_fugaxCompleteLoss');"); ?>

<?php
	$str = "Loss of color vision";
	$tmp= (in_array($str,$arr_neuroVisLoss)) ? "checked=\"checked\"" : "";
?>
<input id="div75" type="checkbox" name="elem_neuroVisLoss[]" value="Loss of color vision" 
		onclick="setRVS(this,'-Loss of vision')" <?php echo $tmp;?>>
<label for="div75">Loss of color vision</label>
<?php //echo rvs_get_dd_dsc_lvl("LossOfColorVision","describeMe('Loss of vision', this, 'div75');"); ?>
<?php echo $ar_wv_hpi_opts["Neuro"]["Loss of Vision"]; //Custom HPI ?>
<?php
	$str = "Other";
	//$tmp= (in_array($str,$arr_neuroVisLoss)) ? "checked=\"checked\"" : "";
	$tmp= ($elem_neuroVisLossOther) ? "checked=\"checked\"" : "";
	if($elem_neuroVisLossOther) $neuroVisLossOther = $elem_neuroVisLossOther; else $neuroVisLossOther = 'Other';

?>
<input id="neuroVisLossChk" type="checkbox" name="elem_neuroVisLoss[]" value="Other" 
		onclick="showOther(this, 'elem_neuroVisLossOther','-Loss of vision', 'elem_neuroVisLossOther');" <?php echo $tmp;?>>

<label for="neuroVisLossChk">
<span >Other</span>
<input type="text" id="elem_neuroVisLossOther" name="elem_neuroVisLossOther" value="<?php echo $elem_neuroVisLossOther; ?>" 
		onchange="setRVS(this,'-Loss of vision','neuroVisLossChk');">
</label>
<?php //echo rvs_get_dd_dsc_lvl("td_neuroVisLossChk","describeMe('Loss of vision', this, 'elem_neuroVisLossOther');"); ?>

</p>
</div>

</div>

</div>

<?php //Follow-Up --- ?>
<div id="rvs_FollowUp" class="tabSec">
<div class="row" >
<div class="subSec col-sm-5" >
<h2>Post op:</h2>
<p>
<?php

$arr_post_op = array("Cataract surgery"=>array("elem_fuPostOp_catsur", "lbl_fuPostOp_catsur"), 
				"Glaucoma surgery"=>array("elem_fuPostOp_glasur", "Glaucomasurgery"), 
				"Glaucoma laser"=>array("elem_fuPostOp_glalas", "lbl_fuPostOp_glalas"), 
				"Retina laser"=>array("elem_fuPostOp_retlas", "lbl_fuPostOp_retlas"), 
				"YAG capsulotomy"=>array("elem_fuPostOp_yaglaser", "YAGlasercapsulotomy", "YAG laser capsulotomy"), 
				"Cornea surgery"=>array("elem_fuPostOp_corsur", "lbl_fuPostOp_corsur"), 
				"VitreoRetinal surgery"=>array("elem_fuPostOp_vitsur", "lbl_fuPostOp_vitsur"), 
				"Strabismus surgery"=>array("elem_fuPostOp_strsurg", "Strabismussurgery"), 
				"Oculoplastics surgery"=>array("elem_fuPostOp_ocusur", "lbl_fuPostOp_ocusur"),
				"Lasik"=>array("elem_fuPostOp_Lasik", "postop_lasik"), 
				"Other"=>array("elem_fuPostOp_otherchk", "td_elem_fuPostOp_otherchk", "elem_fuPostOp_other"));
ksort($arr_post_op);
foreach($arr_post_op as $apo_k => $apo_v){
	
	$str = $apo_k;	
	$tmp= (in_array($str,$arr_fuPostOp)) ? "checked=\"checked\"" : "";
	$elem_id = $apo_v[0];
	$lbl_id = $apo_v[1];
	
	if($str!="Other"){	
	
		//check with old values
		if(empty($tmp) && isset($apo_v[2]) && in_array($apo_v[2],$arr_fuPostOp) ){ $tmp="checked=\"checked\""; }	
	
	echo "<input id=\"".$elem_id."\" type=\"checkbox\" name=\"elem_fuPostOp[]\" value=\"".$str."\" 
		onclick=\"setRVS(this,'-Post op');\" ".$tmp.">
		<label for=\"".$elem_id."\" >".$str."</label>";
		 //echo rvs_get_dd_dsc_lvl($lbl_id,"describeMe('Post op', this, '".$elem_id."')"); 
	}else{
		echo $ar_wv_hpi_opts["Follow-up"]["Post-op"]; //Custom HPI 
		$el_txt_id = $apo_v[2];
	
		$echo_other =  "<input id=\"".$elem_id."\" type=\"checkbox\" name=\"elem_fuPostOp[]\" value=\"".$str."\" 
				onclick=\"disOth(this,'".$el_txt_id."','".$el_txt_id."')\" ".$tmp.">
		<label for=\"".$elem_id."\">".	
		"
		<span >".$str."</span>
		<input type=\"text\" id=\"".$el_txt_id."\" name=\"".$el_txt_id."\" value=\"".$elem_fuPostOp_other."\" 
				onchange=\"setRVS(this,'-Post op','".$elem_id."');\" >
		</label>
		".	
		//rvs_get_dd_dsc_lvl($lbl_id, "describeMe('Post op', this, '".$el_txt_id."');").
		"";
	}
}

echo $echo_other; //show last

/*?>
<?php
$str = "Cataract extraction";
$tmp= (in_array($str,$arr_fuPostOp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuPostOp_catextract" type="checkbox" name="elem_fuPostOp[]" value="Cataract extraction" 
		onclick="setRVS(this,'-Post op');"<?php echo $tmp;?>>
<label id="Cataractextraction" onClick="describeMe('Post op', this, 'elem_fuPostOp_catextract');">Cataract extraction</label>

<?php
$str = "Glaucoma surgery";
$tmp= (in_array($str,$arr_fuPostOp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuPostOp_glasur" type="checkbox" name="elem_fuPostOp[]" value="Glaucoma surgery" 
		onclick="setRVS(this,'-Post op');"<?php echo $tmp;?>>
<label id="Glaucomasurgery" onClick="describeMe('Post op', this, 'elem_fuPostOp_glasur');">Glaucoma surgery</label>

<?php
$str = "Lasik";
$tmp= (in_array($str,$arr_fuPostOp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuPostOp_Lasik" type="checkbox" name="elem_fuPostOp[]" value="Lasik" 
		onclick="setRVS(this,'-Post op');"<?php echo $tmp;?>>
<label id="postop_lasik" onClick="describeMe('Post op', this, 'elem_fuPostOp_Lasik');">Lasik</label>

<?php
$str = "Peripheral iridotomy";
$tmp= (in_array($str,$arr_fuPostOp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuPostOp_periirido" type="checkbox" name="elem_fuPostOp[]" value="Peripheral iridotomy" 
		onclick="setRVS(this,'-Post op');"<?php echo $tmp;?>>
<label id="Peripheraliridotomy" onClick="describeMe('Post op', this, 'elem_fuPostOp_periirido');">Peripheral iridotomy</label>

<?php
$str = "Strabismus surgery";
$tmp= (in_array($str,$arr_fuPostOp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuPostOp_strsurg" type="checkbox" name="elem_fuPostOp[]" value="Strabismus surgery" 
		onclick="setRVS(this,'-Post op');"<?php echo $tmp;?>>
<label id="Strabismussurgery" onClick="describeMe('Post op', this, 'elem_fuPostOp_strsurg');">Strabismus surgery</label>

<?php
$str = "Trabeculectomy";
$tmp= (in_array($str,$arr_fuPostOp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuPostOp_trab" type="checkbox" name="elem_fuPostOp[]" value="Trabeculectomy" 
		onclick="setRVS(this,'-Post op');"<?php echo $tmp;?>>
<label id="Trabeculectomy" onClick="describeMe('Post op', this, 'elem_fuPostOp_trab');">Trabeculectomy</label>

<?php
$str = "YAG laser capsulotomy";
$tmp= (in_array($str,$arr_fuPostOp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuPostOp_yaglaser" type="checkbox" name="elem_fuPostOp[]" value="YAG laser capsulotomy" 
		onclick="setRVS(this,'-Post op');"<?php echo $tmp;?>>
<label id="YAGlasercapsulotomy" onClick="describeMe('Post op', this, 'elem_fuPostOp_yaglaser');">YAG laser capsulotomy</label>

<?php
	$str = "Other";
	$tmp= (in_array($str,$arr_fuPostOp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuPostOp_otherchk" type="checkbox" name="elem_fuPostOp[]" value="Other" 
		onclick="disOth(this,'elem_fuPostOp_other','elem_fuPostOp_other')" <?php echo $tmp;?>>
<label>
<span id="td_elem_fuPostOp_otherchk" onClick="describeMe('Post op', this, 'elem_fuPostOp_other');">Other</span>
<input type="text" name="elem_fuPostOp_other" value="<?php echo $elem_fuPostOp_other;?>" 
		onchange="setRVS(this,'-Post op','elem_fuPostOp_otherchk');">
</label>
<?php */ ?>

</p>
</div>

<?php
//End POST OP
//FollowUp-Sub Section
?>

<div class="subSec col-sm-6 col-sm-offset-1" >
<h2>Follow-up:</h2>
<p>
<?php 

$arr_fu_followup = array(  "AMD"=>array("elem_fuFollowUp_amd", "lbl_fuFollowUp_amd"),
					"Cataract"=>array("elem_fuFollowUp_cataracts",	"fuCataracts","Cataracts"),
					"Glaucoma"=>array("elem_fuFollowUp_glaucoma", 	"fuGlaucoma"),
					"Elevated intraocular pressure"=>array("elem_fuFollowUp_eip",	"Elevatedintraocularpressure"),
					"Blepharitis"=>array("elem_fuFollowUp_bleph",	"fuBlepharitis"),
					"Diabetes"=>array("elem_fuFollowUp_diab", "lbl_fuFollowUp_diab"),
					"Dry Eye"=>array("elem_fuFollowUp_de", "lbl_fuFollowUp_de"),
					"Retina condition"=>array("elem_fuFollowUp_rc", "lbl_fuFollowUp_rc"),
					"Glasses check"=>array("elem_fuFollowUp_glass",	"fuGlasses", "Glasses"),
					"Contact lens check"=>array("elem_fuFollowUp_cl",	"fuContactLenses", "Contact Lenses"),
					"Red eye"=>array("elem_fuFollowUp_re", "lbl_fuFollowUp_re"),
					"Infection"=>array("elem_fuFollowUp_einf",	"fuEyeinfection", "Eye infection"),
					"Vision problem"=>array("elem_fuFollowUp_vis",	"fuVision", "Vision"),
					"Intraocular Pressure Check"=>array("elem_fuFollowUp_ipc", "lbl_intra_press_chk"), 
					"Vision Check"=>array("elem_fuFollowUp_vischk", "lbl_vis_chk"),
					"Other"=>array("elem_fuFollowUp_otherchk",	"td_elem_fuFollowUp_otherchk",	"elem_fuFollowUp_other"));
ksort($arr_fu_followup);
$echo_other ="";
foreach($arr_fu_followup as $aff_k => $aff_v){
	
	$str = $aff_k;
	$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
	$elem_id = $aff_v[0];
	$lbl_id = $aff_v[1];
		
	if($str!="Other"){		
		
		//check with old values
		if(empty($tmp) && isset($aff_v[2]) && in_array($aff_v[2],$arr_fuFollowUp) ){ $tmp="checked=\"checked\""; }	
		
		echo  "<input id=\"".$elem_id."\" type=\"checkbox\" name=\"elem_fuFollowUp[]\" value=\"".$str."\" 
			onclick=\"setRVS(this,'-Follow Up');\" ".$tmp.">
			<label for=\"".$elem_id."\" >".$str."</label>";
		//echo rvs_get_dd_dsc_lvl($lbl_id, "describeMe('Follow Up', this, '".$elem_id."');"); 
	}else{
		echo $ar_wv_hpi_opts["Follow-up"]["Follow-up"]; //Custom HPI 
		$el_txt_id = $aff_v[2];
		
		$echo_other = "<input id=\"".$elem_id."\" type=\"checkbox\" name=\"elem_fuFollowUp[]\" value=\"".$str."\" 
							onclick=\"disOth(this,'".$el_txt_id."','".$el_txt_id."')\" ".$tmp.">
					<label for=\"".$elem_id."\">".					
					"
					<span >".$str."</span>
					<input type=\"text\" id=\"".$el_txt_id."\" name=\"".$el_txt_id."\" value=\"".$elem_fuFollowUp_other."\" 
							onchange=\"setRVS(this,'-Follow Up','".$elem_id."');\"  >
					</label>
					".
					//rvs_get_dd_dsc_lvl($lbl_id, "describeMe('Follow Up', this, '".$el_txt_id."');").
					"";	
	}
}

echo $echo_other; //show last

/*?>
<?php
$str = "Elevated intraocular pressure";
$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_eip" type="checkbox" name="elem_fuFollowUp[]" value="Elevated intraocular pressure" 
		onclick="setRVS(this,'-Follow Up');"<?php echo $tmp;?>>
<label id="Elevatedintraocularpressure" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_eip');">Elevated intraocular pressure</label>

<?php
$str = "Intraocular pressure";
$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_ip" type="checkbox" name="elem_fuFollowUp[]" value="Intraocular pressure" 
		onclick="setRVS(this,'-Follow Up');"<?php echo $tmp;?>>
<label id="Intraocularpressure" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_ip');">Intraocular pressure</label>

<?php
$str = "Glaucoma";
$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_glaucoma" type="checkbox" name="elem_fuFollowUp[]" value="Glaucoma" 
		onclick="setRVS(this,'-Follow Up');"<?php echo $tmp;?>>
<label id="fuGlaucoma" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_glaucoma');">Glaucoma</label>

<?php
$str = "Decreased vision";
$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_dv" type="checkbox" name="elem_fuFollowUp[]" value="Decreased vision" 
		onclick="setRVS(this,'-Follow Up');"<?php echo $tmp;?>>
<label id="Decreasedvision" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_dv');">Decreased vision</label>

<?php
$str = "Vision";
$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_vis" type="checkbox" name="elem_fuFollowUp[]" value="Vision" 
		onclick="setRVS(this,'-Follow Up');"<?php echo $tmp;?>>
<label id="fuVision" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_vis');">Vision</label>

<?php
$str = "Glasses";
$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_glass" type="checkbox" name="elem_fuFollowUp[]" value="Glasses" 
		onclick="setRVS(this,'-Follow Up');"<?php echo $tmp;?>>
<label id="fuGlasses" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_glass');">Glasses</label>

<?php
$str = "Contact Lenses";
$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_cl" type="checkbox" name="elem_fuFollowUp[]" value="Contact Lenses" 
		onclick="setRVS(this,'-Follow Up');"<?php echo $tmp;?>>
<label id="fuContactLenses" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_cl');">Contact Lenses</label>

<?php
$str = "Cataracts";
$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_cataracts" type="checkbox" name="elem_fuFollowUp[]" value="Cataracts" 
		onclick="setRVS(this,'-Follow Up');"<?php echo $tmp;?>>
<label id="fuCataracts" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_cataracts');">Cataracts</label>

<?php
$str = "Red eye";
$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_redeye" type="checkbox" name="elem_fuFollowUp[]" value="Red eye" 
		onclick="setRVS(this,'-Follow Up');"<?php echo $tmp;?>>
<label id="fuRedeye" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_redeye');">Red eye</label>

<?php
$str = "Eye infection";
$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_einf" type="checkbox" name="elem_fuFollowUp[]" value="Eye infection" 
		onclick="setRVS(this,'-Follow Up');"<?php echo $tmp;?>>
<label id="fuEyeinfection" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_einf');">Eye infection</label>

<?php
$str = "Blepharitis";
$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_bleph" type="checkbox" name="elem_fuFollowUp[]" value="Blepharitis" 
		onclick="setRVS(this,'-Follow Up');"<?php echo $tmp;?>>
<label id="fuBlepharitis" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_bleph');">Blepharitis</label>

<?php
	$str = "Other";
	$tmp= (in_array($str,$arr_fuFollowUp)) ? "checked=\"checked\"" : "";
?>
<input id="elem_fuFollowUp_otherchk" type="checkbox" name="elem_fuFollowUp[]" value="Other" 
		onclick="disOth(this,'elem_fuFollowUp_other','elem_fuFollowUp_other')" <?php echo $tmp;?>>
<label>
<span id="td_elem_fuFollowUp_otherchk" onClick="describeMe('Follow Up', this, 'elem_fuFollowUp_other');">Other</span>
<input type="text" name="elem_fuFollowUp_other" value="<?php echo $elem_fuFollowUp_other;?>" 
		onchange="setRVS(this,'-Follow Up','elem_fuFollowUp_otherchk');">
</label>
<?php */?>
</p>
</div>

<?php //END FollowUp-Sub Section ?>
</div>

</div>
<?php //End Follow-Up --- ?>

</div>

<!-- RVS -->

<!-- Desc detail -->
<div id="detailDesc1" >
<div class="header handleDrag" >
	<label class="pull-left hidden" id="lbl_hpi_cntr" >HPI Element : <span class="badge hpi_cntr"></span></label>
	<label id="detailDescription1" ></label>
	<span class="btnClose pull-right glyphicon glyphicon-remove" onclick="closeWindow();"></span>
</div>
<div class="table-responsive">
<table class="table table-striped table-hover table-bordered tstlst">
<tr>
<td class="tstlftpan"><label class="title">Location:</label></td>
<td ><ul class="ul_location">
<li><input id="dd_loc_be" value="Both Eyes" type="checkbox"><label for="dd_loc_be">Both&nbsp;Eyes</label>	</li>	
<li><input id="dd_loc_le" value="Left Eye" type="checkbox"><label for="dd_loc_le">Left&nbsp;Eye</label>	</li>
<li><input id="dd_loc_re" value="Right Eye" type="checkbox"><label for="dd_loc_re">Right&nbsp;Eye</label></li>
<li><input value="Central vision" type="checkbox" id="dd_loc_cv"><label for="dd_loc_cv">Central vision</label></li>
<li><input value="Peripheral vision" type="checkbox" id="dd_loc_pv"><label for="dd_loc_pv" >Peripheral vision</label></li>
<li><input value="Paraxial vision" type="checkbox" id="dd_loc_pxv"><label for="dd_loc_pxv">Paraxial vision</label></li>
<li>
	<select name="selLocOpt" id="selLocOpt"  class="form-control">
		<option value=""></option>
		<option value="head">head</option>
		<option value="left side of head">left side of head</option>
		<option value="right side of head">right side of head</option>
		<option value="left side of face">left side of face</option>
		<option value="right side of face">right side of face</option>
		<option value="fore head">fore head</option>
		<option value="scalp">scalp</option>		
	</select></li>	
	
<li><input id="dd_loc_lel" value="Left Eyelids" type="checkbox"><label for="dd_loc_lel">Left&nbsp;Eyelids</label>	</li>
<li><input id="dd_loc_lul" value="LUL" type="checkbox"><label for="dd_loc_lul">LUL</label></li>
<li><input id="dd_loc_lll" value="LLL" type="checkbox"><label for="dd_loc_lll">LLL</label>	</li>
<li><input id="dd_loc_rel" value="Right Eyelids" type="checkbox"><label for="dd_loc_rel">Right&nbsp;Eyelids</label></li>
<li><input id="dd_loc_rul" value="RUL" type="checkbox"><label for="dd_loc_rul">RUL</label></li>
<li><input id="dd_loc_rll" value="RLL" type="checkbox"><label for="dd_loc_rll">RLL</label></li>	
<li class="pull-right"><textarea class="right form-control" name="selLocOther" id="selLocOther" rows="1" placeholder="Other" title="Other Location" ></textarea></li>
</ul></td>

</tr>

<tr>
  <td class="tstlftpan"><label class="title">Onset:</label>	</td>
  <td><ul class="ul_onset">
		<li>
			<div class="input-group" id="elem_os_othr_date_grp">
			<input type="text" name="elem_os_othr_date" id="elem_os_othr_date"  alt="mm-dd-yyyy" placeholder="Date" title="Date: mm-dd-yyyy" class="form-control" >
			<span class="input-group-addon"><span class="glyphicon glyphicon-remove" title="Close date"  onclick="hpi_chk_entr_dt(this,1)"></span></span>
			</div>
		</li>
	<li>
	<select id="selectNo" name="selectNo" onchange="hpi_chk_entr_dt(this,1)" class="form-control">
		<option value=""></option>
		<option value="Less than 1">Less than 1</option>
		<?php
		for($i=1; $i<=12;$i++){
			?>
			<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
			<?php
		}
		?>
		<option value="15">15</option>
		<option value="20">20</option>
		<option value="30">30</option>
		<option value="45">45</option>
		<option value="Date">Date</option>
	</select></li>
	<li><select id="selectDate" name="selectDate" onchange="hpi_chk_entr_dt(this,1)" class="form-control">
		<option value=""></option>
		<option value="Days">Days</option>
		<option value="Weeks">Weeks</option>
		<option value="Months">Months</option>
		<option value="Years">Years</option>		
		<option value="Day">Day</option>
		<option value="Week">Week</option>
		<option value="Month">Month</option>
		<option value="Year">Year</option>
		<option value="Date">Date</option>
	</select></li>
	
	<li><input type="checkbox" value="many years" id="dd_qly_my" ><label for="dd_qly_my">Many years</label></li>
	<li><input type="checkbox" value="Quality~patient unsure" id="dd_qly_pu" ><label for="dd_qly_pu">Patient unsure</label></li>
	<li><input type="checkbox" value="since surgery" id="dd_qly_ss" ><label for="dd_qly_ss">Since surgery</label></li>
	<li><input type="checkbox" value="since birth" id="dd_qly_sb" ><label for="dd_qly_sb">Since birth</label></li>
	<li><input type="checkbox" value="since childhood" id="dd_qly_sc" ><label for="dd_qly_sc">Since childhood</label></li>
	<li><input type="checkbox" value="since last visit" id="dd_qly_slv" ><label for="dd_qly_slv">Since last visit</label></li>	

</ul></td>
</tr>

<tr>
  <td class="tstlftpan"><label class="title">Duration of Episodes:</label></td>
  <td><ul class="ul_doe">
	
	<li><input type="checkbox" value="Constant" id="dd_to_co" ><label for="dd_to_co">Constant</label></li>
	<li><input type="checkbox" value="Gradual" id="dd_to_gr" ><label for="dd_to_gr">Gradual</label></li>
	<li><input type="checkbox" value="DoE~Intermittent" id="dd_doe_in" ><label for="dd_doe_in">Intermittent</label></li>
	<li><input type="checkbox" value="Sudden" id="dd_to_su" ><label for="dd_to_su">Sudden</label></li>
	<li><input type="checkbox" value="DoE~Uncertain" id="dd_doe_un" ><label for="dd_doe_un">Uncertain</label></li>
	<li class="form-group">	
	<label for="selectDoE" class="nobdr" >Lasts Several</label>
	<select id="selectDoE" name="selectDoE" class="form-control">
		<option value=""></option>
		<option value="Seconds">Seconds</option>
		<option value="Minutes">Minutes</option>
		<option value="Hours">Hours</option>
		<option value="Days">Days</option>
		<option value="Weeks">Weeks</option>
		<option value="Months">Months</option>
		<option value="Years">Years</option>	
	</select></li>
	<li><label class="rvs_lbl_1">Date</label><input type="text" alt="mm-dd-yyyy" name="onSetDate" id="onSetDate" class="form-control"  ></li>
	<li class="form-group form-inline pull-right"><label class="nobdr" for="otherDoe">Other</label><textarea  id="otherDoe" name="otherDoe" class="form-control" rows="1" ></textarea></li>

</ul></td>
</tr>
<tr>
  <td class="tstlftpan"><label class="title">Quality:</label></td>
  <td><ul class="ul_quality">
	
	<li><input type="checkbox" value="Aching" id="dd_qly_ac"><label for="dd_qly_ac">Aching</label></li>	
	<li><input type="checkbox" value="Quality~Burning" id="dd_qly_bu"><label for="dd_qly_bu">Burning</label></li>
	<li><input type="checkbox" value="dry" id="dd_qly_dr"><label for="dd_qly_dr">Dry</label></li>
	<li><input type="checkbox" value="Quality~itching" id="dd_qly_it"><label for="dd_qly_it">Itching</label></li>	
	<li><input type="checkbox" value="pressure" id="dd_qly_pr" ><label for="dd_qly_pr">Pressure</label></li>
	<li><input type="checkbox" value="scratchy" id="dd_qly_scra" ><label for="dd_qly_scra">Scratchy</label></li>
	<li><input type="checkbox" value="sharp" id="dd_qly_sh" ><label for="dd_qly_sh">Sharp</label></li>
	<li><input type="checkbox" value="Stabbing" id="dd_qly_st"><label for="dd_qly_st">Stabbing</label></li>	
	<li><input type="checkbox" value="throbbing" id="dd_qly_th" ><label for="dd_qly_th">Throbbing</label></li>
	<li><input type="checkbox" value="watery" id="dd_qly_wa" ><label for="dd_qly_wa">Watery</label></li>
	<li><input type="checkbox" value="distorted" id="dd_qly_di"><label for="dd_qly_di">Distorted</label></li>	
	<li><input type="checkbox" value="Dull" id="dd_qly_du"><label for="dd_qly_du">Dull</label></li>
	<li><input type="checkbox" value="foggy" id="dd_qly_fo"><label for="dd_qly_fo">Foggy</label></li>
	<li><input type="checkbox" value="ghosting" id="dd_qly_gh"><label for="dd_qly_gh">Ghosting</label></li>
	<li><input type="checkbox" value="Quality~glare" id="dd_qly_gl"><label for="dd_qly_gl">Glare</label></li>	
	<li><input type="checkbox" value="hazy" id="dd_qly_ha" ><label for="dd_qly_ha">Hazy</label></li>	
	<li class="pull-right"><textarea  id="otherQty" name="otherQty" rows="1" placeholder="Other" title="Other Quality" class="form-control" ></textarea></li>

</ul></td>
</tr>

<tr>
  <td class="tstlftpan"><label class="title">Severity:</label></td>
  <td><ul class="ul_severity">
	
	<li><input type="checkbox" value="Decreased" id="dd_sev_de" ><label for="dd_sev_de">Decreased</label></li>
	<li><input type="checkbox" value="Increased" id="dd_sev_in" ><label for="dd_sev_in">Increased</label></li>
	<li><input type="checkbox" value="Mild" id="dd_sev_mi" ><label for="dd_sev_mi">Mild</label></li>
	<li><input type="checkbox" value="Moderate" id="dd_sev_mo" ><label for="dd_sev_mo">Moderate</label></li>
	<li><input type="checkbox" value="Severe" id="dd_sev_se" ><label for="dd_sev_se">Severe</label></li>	
	<li><input type="checkbox" value="Worsening" id="dd_sev_wo" ><label for="dd_sev_wo">Worsening</label></li>
	<li class="form-group form-inline"><label class="nobdr" for="scale1To10" >Scale of 1-10</label><input type="text" id="scale1To10" name="scale1To10"  size="12" class="form-control"></li>
</ul></td>
</tr>

<tr>
  <td class="tstlftpan"><label id="contxt" class="title">Context:<br><span>Happens&nbsp;when</span></label></td>
  <td><ul class="ul_context">
  
	<li><input type="checkbox" value="driving" id="dd_chw_dr" ><label for="dd_chw_dr">Driving</label></li>
	<li><input type="checkbox" value="inside" id="dd_chw_in" ><label for="dd_chw_in">Inside</label></li>
	<li><input type="checkbox" value="outside" id="dd_chw_ou" ><label for="dd_chw_ou">Outside</label></li>
	<li><input type="checkbox" value="Reading" id="dd_chw_re" ><label for="dd_chw_re">Reading</label></li>
	<li class="form-group form-inline pull-right"><label class="nobdr" for="otherContext" >Other</label><textarea class="form-control" id="otherContext" name="otherContext" rows="1" ></textarea></li>

</ul></td>
</tr>

<tr>
  <td class="tstlftpan"><label class="title">Modifying&nbsp;Factors:</label></td>
  <td><ul class="ul_modi_fac">
	
	<li><label ><b>Worse:</b></label></li>	
	<li><input type="checkbox" value="distance vision than near" id="dd_mf_dvtn" ><label for="dd_mf_dvtn">Distance vision than near</label></li>
	<li><input type="checkbox" value="MF~inside" id="dd_mf_in" ><label for="dd_mf_in">Inside</label></li>	
	<li><input type="checkbox" value="night" id="dd_mf_ni" ><label for="dd_mf_ni">Night</label></li>
	<li><input type="checkbox" value="near vision than distance" id="dd_mf_nvtd" ><label for="dd_mf_nvtd">Near vision than distance</label></li>
	<li><input type="checkbox" value="outdoors" id="dd_mf_ou" ><label for="dd_mf_ou">Outdoors</label></li>
	<li><input type="checkbox" value="with daily activities" id="dd_mf_wda" ><label for="dd_mf_wda">With daily activities</label></li>
	<li><input type="checkbox" value="with intensive visual activity (readingcomma computer)" id="dd_mf_wiva" ><label for="dd_mf_wiva">With intensive visual activity (reading, computer)</label></li>
	<!--
	<input  size="15" name="makesBetter" type="text"> 
	<label>Makes it better,&nbsp;</label>
	<input name="makesWorse"  size="20" type="text">
	<label>Makes it worse.</label>
	-->

</ul></td>
</tr>

<tr>
  <td class="tstlftpan"><label class="title">&nbsp;</label></td>
  <td>
  <ul class="ul_modi_fac">
  
	<li><label ><b>Better:</b></label></li>
	<li><input type="checkbox" value="early in the day" id="dd_mf_eid" ><label for="dd_mf_eid">Early in the day</label></li>
	<li><input type="checkbox" value="in dim light" id="dd_mf_idl" ><label for="dd_mf_idl">In dim light</label></li>
	<li><input type="checkbox" value="late in the day" id="dd_mf_lid" ><label for="dd_mf_lid">Late in the day</label></li>
	<li><input type="checkbox" value="when transition from dark to light" id="dd_mf_dtl" ><label for="dd_mf_dtl">When transition from dark to light</label></li>
	<li><input type="checkbox" value="when transition from light to dark" id="dd_mf_ltd" ><label for="dd_mf_ltd">When transition from light to dark</label></li>	
	<li class="form-group form-inline pull-right"><label for="otherFactors" class="nobdr">Other</label><textarea class="form-control" id="otherFactors" name="otherFactors" rows="1"></textarea></li>

  </ul>
  
  </td>
</tr>

<tr>
  <td class="tstlftpan"><label id="asso_sign" class="title">Associated Signs and Symptoms:</label></td>
  <td>
  <ul class="ul_asas">
	
	<li><input type="checkbox" value="Blurry Vision" id="dd_asas_bv" ><label for="dd_asas_bv">Blurry Vision</label></li>
	<li><input type="checkbox" value="Burning" id="dd_asas_bu" ><label for="dd_asas_bu">Burning</label></li>
	<li><input type="checkbox" value="double vision" id="dd_asas_dv" ><label for="dd_asas_dv">Double vision</label></li>
	<li><input type="checkbox" value="dizziness or light-headedness" id="dd_asas_dl" ><label for="dd_asas_dl">Dizziness or light-headedness</label></li>
	<li><input type="checkbox" value="Eye pain" id="dd_asas_ep" ><label for="dd_asas_ep">Eye pain</label></li>
	<li><input type="checkbox" value="flashes" id="dd_asas_fla" ><label for="dd_asas_fla">Flashes</label></li>
	<li><input type="checkbox" value="floater" id="dd_asas_flo" ><label for="dd_asas_flo">Floater</label></li>	
	<li><input type="checkbox" value="glare" id="dd_asas_gl" ><label for="dd_asas_gl">Glare</label></li>
	<li><input type="checkbox" value="Halos" id="dd_asas_ha" ><label for="dd_asas_ha">Halos</label></li>
	<li><input type="checkbox" value="Headache" id="dd_asas_he" ><label for="dd_asas_he">Headache</label></li>
	<li><input type="checkbox" value="high ocular pressure" id="dd_asas_hop" ><label for="dd_asas_hop">High ocular pressure</label></li>
	<li><input type="checkbox" value="Irritation" id="dd_asas_ir" ><label for="dd_asas_ir">Irritation</label></li>	

  </ul>  
  </td>
</tr>

<tr>
  <td class="tstlftpan">&nbsp;</td>
  <td>
  <ul class="ul_asas">
	<li><input type="checkbox" value="Itching" id="dd_asas_it" ><label for="dd_asas_it">Itching</label></li>
	<li><input type="checkbox" value="Light sensitivity" id="dd_asas_ls" ><label for="dd_asas_ls">Light sensitivity </label></li>	
	<li><input type="checkbox" value="ocular redness" id="dd_asas_or" ><label for="dd_asas_or">Ocular redness</label></li>	
	<li><input type="checkbox" value="Pain" id="dd_asas_pa" ><label for="dd_asas_pa">Pain</label></li>	
	<li><input type="checkbox" value="Redness" id="dd_asas_re" ><label for="dd_asas_re">Redness</label></li>
	<li><input type="checkbox" value="tearing" id="dd_asas_te" ><label for="dd_asas_te">Tearing</label></li>
	<li><input type="checkbox" value="weakness" id="dd_asas_we" ><label for="dd_asas_we">Weakness</label></li>
	<li><input type="checkbox" value="none" id="dd_asas_no" ><label for="dd_asas_no">None</label></li>
	<li class="form-group form-inline pull-right"><label class="nobdr" for="otherSymptoms" >Other</label><textarea class="form-control" id="otherSymptoms" name="otherSymptoms" rows="1"></textarea></li>
  </ul>  
  </td>
</tr>

<tr class="warning">
  <td class="tstlftpan"><label class="title">Pertinent Negatives:</label></td>
  <td>
  <ul class="ul_pn">

	<li><input type="checkbox" value="no Change in Amsler Grid" id="dd_pn_ncag" ><label for="dd_pn_ncag">No Change in Amsler Grid</label></li>
	<li><input type="checkbox" value="no Distortion" id="dd_pn_ndi" ><label for="dd_pn_ndi">No Distortion</label></li>
	<li><input type="checkbox" value="no Dryness" id="dd_pn_ndr" ><label for="dd_pn_ndr">No Dryness</label></li>		
	<li><input type="checkbox" value="no eye Pain" id="dd_pn_nep" ><label for="dd_pn_nep">No Eye Pain</label></li>
	<li><input type="checkbox" value="no FevercommaWeight LosscommaScalp TendernesscommaHeadache or Jaw Claudication" id="dd_pn_nfev" ><label for="dd_pn_nfev">No Fever, Weight Loss, Scalp Tenderness, Headache or Jaw Claudication</label></li>	
	<li><input type="checkbox" value="no Flashes" id="dd_pn_nfla" ><label for="dd_pn_nfla">No Flashes</label></li>	

</ul>  
  </td>
</tr>

<tr class="warning">
  <td class="tstlftpan">&nbsp;</td>
  <td>
  <ul class="ul_pn">
  
	<li><input type="checkbox" value="no Flashescommafloatercommashadowcommacurtain or Veil" id="dd_pn_nflaveil" ><label for="dd_pn_nflaveil">No Flashes, Floater, Shadow, Curtain or Veil</label></li>
	<li><input type="checkbox" value="no floaters" id="dd_pn_nflo" ><label for="dd_pn_nflo">No Floaters</label></li>	
	<li><input type="checkbox" value="no Glare" id="dd_pn_ng" ><label for="dd_pn_ng">No Glare</label></li>
	<li><input type="checkbox" value="no Headache" id="dd_pn_nh" ><label for="dd_pn_nh">No Headache</label></li>	
	<li><input type="checkbox" value="no Itching" id="dd_pn_ni" ><label for="dd_pn_ni">No Itching</label></li>
	<li><input type="checkbox" value="no Loss of Consciousness" id="dd_pn_nlc" ><label for="dd_pn_nlc">No Loss of Consciousness</label></li>
	<li><input type="checkbox" value="no Ocular Trauma" id="dd_pn_not" ><label for="dd_pn_not">No Ocular Trauma</label></li>
	<li><input type="checkbox" value="no Pain or Tearing" id="dd_pn_npt" ><label for="dd_pn_npt">No Pain or Tearing</label></li>

</ul>
  
  </td>
</tr>

<tr class="warning">
  <td class="tstlftpan">&nbsp;</td>
  <td>
  <ul class="ul_pn">

	<li><input type="checkbox" value="no Pain with Eye Movement" id="dd_pn_npem" ><label for="dd_pn_npem">No Pain with Eye Movement</label></li>	
	<li><input type="checkbox" value="no Red Eye" id="dd_pn_nre" ><label for="dd_pn_nre">No Red Eye</label></li>
	<li><input type="checkbox" value="no ShadowcommaCurtain or Veil" id="dd_pn_nscv" ><label for="dd_pn_nscv">No Shadow, Curtain or Veil</label></li>
	<li><input type="checkbox" value="no Tearing" id="dd_pn_nt" ><label for="dd_pn_nt">No Tearing</label></li>	
	<li><input type="checkbox" value="no Visual Phenomenon" id="dd_pn_nvp" ><label for="dd_pn_nvp">No Visual Phenomenon</label></li>
	<li class="form-group form-inline pull-right"><label class="nobdr" for="other_par_neg">Other</label><textarea class="form-control" id="other_par_neg" name="other_par_neg" rows="1"></textarea></li>

</ul>
  
  </td>
</tr>

</table>
</div>
<ul class="list-unstyled">
<li class="btns">
	<input onClick="return fillArray();" type="button"  class="btn btn-success" id="btn_dd1_done" value="Done" />
	<input onClick="return clearAll('detailDesc1');"  type="button"  class="btn btn-danger" id="btn_dd1_reset" value="Reset" />
	<input onClick="return closeWindow();"  type="button"  class="btn btn-danger" id="btn_dd1_cancel" value="Cancel" />
</li>

</ul>

</div>
<!-- Desc detail -->

<!-- Desc detail 2 -->
<div id="detailDesc2" >
<div class="header handleDrag" >
	<label class="pull-left hidden" id="lbl_hpi_cntr" >HPI Element : <span class="badge hpi_cntr"></span></label>
	<label id="detailDescription2" ></label>
	<span class="btnClose pull-right glyphicon glyphicon-remove" onclick="closeWindow();"></span>
</div>
<div class="table-responsive">
<table class="table table-striped table-hover table-bordered tstlst">
<tr>
<td class="tstlftpan"><label class="title">Location:</label></td>
<td ><ul class="ul_location">
	
	<li><input id="dd2_loc_be" value="Both Eyes" type="checkbox"><label for="dd2_loc_be">Both&nbsp;Eyes</label></li>
	<li><input id="dd2_loc_le" value="Left Eye" type="checkbox"><label for="dd2_loc_le">Left&nbsp;Eye</label></li>
	<li><input id="dd2_loc_re" value="Right Eye" type="checkbox"><label for="dd2_loc_re">Right&nbsp;Eye</label></li>	
	
	<li><input value="Central vision" type="checkbox" id="dd2_loc_cv"><label for="dd2_loc_cv">Central vision</label></li>
	<li><input value="Peripheral vision" type="checkbox" id="dd2_loc_pv"><label for="dd2_loc_pv" >Peripheral vision</label></li>
	<li><input value="Paraxial vision" type="checkbox" id="dd2_loc_pxv"><label for="dd2_loc_pxv">Paraxial vision</label></li>		
	
	<li><select id="selLocOpt" name="selLocOpt" class="form-control">
		<option value=""></option>
		<option value="head">head</option>
		<option value="left side of head">left side of head</option>
		<option value="right side of head">right side of head</option>
		<option value="left side of face">left side of face</option>
		<option value="right side of face">right side of face</option>
		<option value="fore head">fore head</option>
		<option value="scalp">scalp</option>		
	</select></li>	
	
	<li><input id="dd2_loc_lel" value="Left Eyelids" type="checkbox"><label for="dd2_loc_lel">Left&nbsp;Eyelids</label></li>
	<li><input id="dd2_loc_lul" value="LUL" type="checkbox"><label for="dd2_loc_lul">LUL</label></li>
	<li><input id="dd2_loc_lll" value="LLL" type="checkbox"><label for="dd2_loc_lll">LLL</label></li>	
	
	<li><input id="dd2_loc_rel" value="Right Eyelids" type="checkbox"><label for="dd2_loc_rel">Right&nbsp;Eyelids</label></li>	
	<li><input id="dd2_loc_rul" value="RUL" type="checkbox"><label for="dd2_loc_rul">RUL</label></li>
	<li><input id="dd2_loc_rll" value="RLL" type="checkbox"><label for="dd2_loc_rll">RLL</label></li>
	<li id="dd2selLocOther"><textarea id="selLocOther" name="selLocOther"  rows="1" placeholder="Other" title="Other Location" class="form-control"  ></textarea></li>

</ul></td>
</tr>

<tr>
<td class="tstlftpan"><label class="title">Onset:</label></td>
<td ><ul class="ul_onset">
	
	<li><label id="lbl_hpi_srgry_type" class="nobdr">S/P (Surgery type:) </label></li>
	<li><input type="text" id="elem_sp_surtype" name="elem_sp_surtype"  size="12" class="form-control"></li>
	<li>
		<div class="input-group" id="elem_sp_surdate_grp">
		<input type="text" name="elem_sp_surdate" id="elem_sp_surdate"  alt="mm-dd-yyyy" placeholder="Date" title="Date" class="form-control">
		<span class="input-group-addon"><span class="glyphicon glyphicon-remove" title="Close date"  onclick="hpi_chk_entr_dt(this,2)"></span></span>
		</div>
	</li>
	<li><select id="selectNo" name="selectNo" onchange="hpi_chk_entr_dt(this,2)" class="form-control">
		<option value=""></option>
		<option value="Less than 1">Less than 1</option>	
		<?php
		for($i=1; $i<=12;$i++){
			?>
			<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
			<?php
		}
		?>
		<option value="15">15</option>
		<option value="20">20</option>
		<option value="30">30</option>
		<option value="45">45</option>
		<option value="Date">Date</option>
	</select></li>
	<li><select id="selectDate" name="selectDate" onchange="hpi_chk_entr_dt(this,2)" class="form-control">
		<option value=""></option>
		<option value="Days">Days</option>
		<option value="Weeks">Weeks</option>
		<option value="Months">Months</option>
		<option value="Years">Years</option>		
		<option value="Day">Day</option>
		<option value="Week">Week</option>
		<option value="Month">Month</option>
		<option value="Year">Year</option>
		<option value="Date">Date</option>
	</select></li>
	<li class="form-group form-inline pull-right"><label class="nobdr" for="other_onset">Other</label>
	<textarea id="other_onset" name="other_onset"  rows="1" class="form-control"></textarea></li>		

</ul></td>
</tr>

<tr>
<td class="tstlftpan"><label class="title">Duration of Episodes:</label></td>
<td ><ul class="ul_doe">
	
	<li><input type="checkbox" value="DoE~Intermittent" id="dd2_doe_in" ><label for="dd2_doe_in">Intermittent</label></li>
	<li><input type="checkbox" value="DoE~Uncertain" id="dd2_doe_un" ><label for="dd2_doe_un">Uncertain</label></li>
	<li class="form-group form-inline"><label for="selectDoE" class="nobdr">Lasts Several</label>
	<select id="selectDoE" name="selectDoE" class="form-control">
		<option value=""></option>
		<option value="Seconds">Seconds</option>
		<option value="Minutes">Minutes</option>
		<option value="Hours">Hours</option>
		<option value="Days">Days</option>
		<option value="Weeks">Weeks</option>
		<option value="Months">Months</option>
		<option value="Years">Years</option>	
	</select></li>

</ul></td>
</tr>

<tr>
<td class="tstlftpan"><label class="title">Quality:</label></td>
<td ><ul class="ul_quality">
	
	<li><input type="checkbox" value="Quality~Decreased" id="dd2_qly_de" ><label for="dd2_qly_de">Decreased</label></li>
	<li><input type="checkbox" value="Doing well" id="dd2_qly_dw" ><label for="dd2_qly_dw">Doing well</label></li>
	<li><input type="checkbox" value="Feels improvement" id="dd2_qly_fi" ><label for="dd2_qly_fi">Feels improvement</label></li>
	<li><input type="checkbox" value="Quality~Increased" id="dd2_qly_in" ><label for="dd2_qly_in">Increased</label></li>
	<li><input type="checkbox" value="Initially improved then worsened" id="dd2_qly_iitw" ><label for="dd2_qly_iitw">Initially improved then worsened</label></li>	
	<li><input type="checkbox" value="New" id="dd2_qly_ne" ><label for="dd2_qly_ne">New</label></li>
	<li><input type="checkbox" value="No changes noted" id="dd2_qly_ncn" ><label for="dd2_qly_ncn">No changes noted</label></li>
	<li><input type="checkbox" value="Resolved" id="dd2_qly_re" ><label for="dd2_qly_re">Resolved</label></li>
	<li><input type="checkbox" value="Quality~Stable" id="dd2_qly_st" ><label for="dd2_qly_st">Stable</label></li>	

</ul></td>
</tr>

<tr>
<td class="tstlftpan"><label class="title">&nbsp;</label></td>
<td ><ul class="ul_quality">
	
	<li><input type="checkbox" value="Tolerating" id="dd2_qly_to" ><label for="dd2_qly_to">Tolerating</label></li>
	<li><input type="checkbox" value="Quality~Worsening" id="dd2_qly_wo" ><label for="dd2_qly_wo">Worsening</label></li>
	<li class="form-group form-inline pull-right"><label for="otherQty" class="nobdr">Other</label>
	<textarea id="otherQty" name="otherQty" rows="1" class="form-control"></textarea></li>	

</ul></td>
</tr>

<tr class="warning">
<td class="tstlftpan"><label class="title">Vision:</label></td>
<td ><ul class="ul_vision">

	<li><input type="checkbox" value="Blurry" id="dd2_vis_bl" ><label for="dd2_vis_bl">Blurry</label></li>
	<li><input type="checkbox" value="Improved" id="dd2_vis_im" ><label for="dd2_vis_im">Improved</label></li>	
	<li><input type="checkbox" value="Stable" id="dd2_vis_st" ><label for="dd2_vis_st">Stable</label></li>	
	<li><input type="checkbox" value="Worse" id="dd2_vis_wo" ><label for="dd2_vis_wo">Worse</label></li>
	<li class="form-group form-inline pull-right"><label for="rvsdet_otherVision" class="nobdr">Other</label>
	<textarea id="rvsdet_otherVision" name="rvsdet_otherVision" class="form-control" rows="1"></textarea></li>	

</ul></td>
</tr>

<tr class="warning">
<td class="tstlftpan"><label class="title">Diplopia:</label></td>
<td ><ul class="ul_diplopia">	
	<li><input type="checkbox" value="Dip~Improved" id="dd2_dip_im" ><label for="dd2_dip_im">Improved</label></li>
	<li><input type="checkbox" value="Dip~Mild" id="dd2_dip_mi" ><label for="dd2_dip_mi">Mild</label></li>	
	<li><input type="checkbox" value="Dip~Worse" id="dd2_dip_wo" ><label for="dd2_dip_wo">Worse</label></li>	
	<li><input type="checkbox" value="Dip~None" id="dd2_dip_no" ><label for="dd2_dip_no">None</label></li>
</ul></td>
</tr>

<tr>
<td class="tstlftpan"><label class="title">Severity:</label></td>
<td ><ul class="ul_severity">
	
	<li><input type="checkbox" value="Severity~Constant" id="dd2_sev_co" ><label for="dd2_sev_co">Constant</label></li>
	<li><input type="checkbox" value="Severity~Intermittent" id="dd2_sev_in" ><label for="dd2_sev_in">Intermittent </label></li>	
	<li><input type="checkbox" value="Mild" id="dd2_sev_mi" ><label for="dd2_sev_mi">Mild</label></li>
	<li><input type="checkbox" value="Moderate" id="dd2_sev_mo" ><label for="dd2_sev_mo">Moderate</label>	</li>
	<li><input type="checkbox" value="Severe" id="dd2_sev_se" ><label for="dd2_sev_se">Severe</label></li>
	<li><input type="checkbox" value="None" id="dd2_sev_no" ><label for="dd2_sev_no">None</label></li>
	<li class="form-group form-inline "><label for="scale1To10" class="nobdr">Scale of 1-10</label>
	<input type="text" id="scale1To10" name="scale1To10"  size="12" class="form-control"></li>
	<li class="form-group form-inline pull-right"><label for="rvs_other_svrity" class="nobdr">Other</label>
	<textarea  id="rvs_other_svrity" name="rvs_other_svrity" class="form-control" rows="1"></textarea></li>	

</ul></td>
</tr>

<tr>
<td class="tstlftpan"><label id="asso_sign" class="title">Associated Signs and Symptoms : </label></td>
<td ><ul class="ul_asas">
	
	<li><input type="checkbox" value="Ache" id="dd2_asas_ac" ><label for="dd2_asas_ac">Ache</label></li>
	<li><input type="checkbox" value="Burning" id="dd2_asas_bu" ><label for="dd2_asas_bu">Burning</label></li>
	<li><input type="checkbox" value="ASAS~Dull" id="dd2_asas_du" ><label for="dd2_asas_du">Dull</label></li>
	<li><input type="checkbox" value="Foreign body sensation" id="dd2_asas_fbs" ><label for="dd2_asas_fbs">Foreign body sensation</label></li>
	<li><input type="checkbox" value="Irritation" id="dd2_asas_ir" ><label for="dd2_asas_ir">Irritation</label></li>
	<li><input type="checkbox" value="Itching" id="dd2_asas_it" ><label for="dd2_asas_it">Itching</label></li>
	<li><input type="checkbox" value="Light sensitivity" id="dd2_asas_ls" ><label for="dd2_asas_ls">Light sensitivity</label></li>	
	<li><input type="checkbox" value="Redness" id="dd2_asas_re" ><label for="dd2_asas_re">Redness</label></li>
	<li><input type="checkbox" value="Sharp" id="dd2_asas_sh" ><label for="dd2_asas_sh">Sharp</label></li>

</ul></td>
</tr>

<tr>
<td class="tstlftpan"><label class="title">Modifying Factors:</label></td>
<td class="ul_modi_fac">
	
	<label for="rvs_painrelievedby" class="pull-left">Relieved by </label>	
	<textarea id="rvs_painrelievedby" name="rvs_painrelievedby" class="form-control pull-left" rows="1"></textarea>

</td>
</tr>

<tr class="warning">
<td class="tstlftpan"><label class="title">Care instructions:</label></td>
<td class="ul_care_int">

	<textarea id="otherFollowCareInstruct" name="otherFollowCareInstruct" class="form-control"  rows="1"></textarea>	

</td>
</tr>

<tr>
<td class="tstlftpan"><label class="title">Medication:</label></td>
<td ><ul class="ul_medi">	
	<li><input type="checkbox" value="is following medication instructions" id="dd2_med_ifmi" ><label for="dd2_med_ifmi">Following medication instructions</label></li>
	<li><input type="checkbox" value="has finished meds as instructed" id="dd2_med_hfm" ><label for="dd2_med_hfm">Finished meds as instructed</label></li>	
	<li><input type="checkbox" value="is not taking medications" id="dd2_med_intm" ><label for="dd2_med_intm">Not taking medications</label></li>
</ul></td>
</tr>

<tr>
<td class="tstlftpan"><label class="title">&nbsp;</label></td>
<td ><ul class="ul_medi">

	<li><input type="checkbox" value="needs med refill" id="dd2_med_nmr" ><label for="dd2_med_nmr">Needs med refill:</label></li>	
	<li class="li_needs"><textarea name="rvs_needs_med_refill"  id="rvs_needs_med_refill" class="form-control" rows="1"></textarea>	</li>
	<li><input type="checkbox" value="ran out of meds" id="dd2_med_rom" ><label for="dd2_med_rom">Ran out of meds:</label></li>
	<li class="li_needs"><textarea name="rvs_ranoutmeds" id="rvs_ranoutmeds" class="form-control" rows="1"></textarea></li>

</ul></td>
</tr>

<tr class="warning">
<td class="tstlftpan"><label class="title">Pertinent Negatives:</label></td>
<td ><ul class="ul_pt_neg">
	
	<li><input type="checkbox" value="no Change in Amsler Grid" id="dd2_pn_ncag" ><label for="dd2_pn_ncag">No Change in Amsler Grid</label></li>
	<li><input type="checkbox" value="no Distortion" id="dd2_pn_ndi" ><label for="dd2_pn_ndi">No Distortion</label></li>
	<li><input type="checkbox" value="no Dryness" id="dd2_pn_ndr" ><label for="dd2_pn_ndr">No Dryness</label></li>		
	<li><input type="checkbox" value="no eye Pain" id="dd2_pn_nep" ><label for="dd2_pn_nep">No Eye Pain</label></li>
	<li><input type="checkbox" value="no FevercommaWeight LosscommaScalp TendernesscommaHeadache or Jaw Claudication" id="dd2_pn_nfev" ><label for="dd2_pn_nfev">No Fever, Weight Loss, Scalp Tenderness, Headache or Jaw Claudication</label></li>	
	<li><input type="checkbox" value="no Flashes" id="dd2_pn_nfla" ><label for="dd2_pn_nfla">No Flashes</label></li>	

</ul></td>
</tr>

<tr class="warning">
<td class="tstlftpan"><label class="title">&nbsp;</label></td>
<td ><ul class="ul_pt_neg">
	
	<li><input type="checkbox" value="no Flashescommafloatercommashadowcommacurtain or Veil" id="dd2_pn_nflaveil" ><label for="dd2_pn_nflaveil">No Flashes, Floater, Shadow, Curtain or Veil</label></li>
	<li><input type="checkbox" value="no floaters" id="dd2_pn_nflo" ><label for="dd2_pn_nflo">No Floaters</label></li>	
	<li><input type="checkbox" value="no Glare" id="dd2_pn_ng" ><label for="dd2_pn_ng">No Glare</label></li>
	<li><input type="checkbox" value="no Headache" id="dd2_pn_nh" ><label for="dd2_pn_nh">No Headache</label></li>	
	<li><input type="checkbox" value="no Itching" id="dd2_pn_ni" ><label for="dd2_pn_ni">No Itching</label></li>
	<li><input type="checkbox" value="no Loss of Consciousness" id="dd2_pn_nlc" ><label for="dd2_pn_nlc">No Loss of Consciousness</label></li>
	<li><input type="checkbox" value="no Ocular Trauma" id="dd2_pn_not" ><label for="dd2_pn_not">No Ocular Trauma</label></li>
	<li><input type="checkbox" value="no Pain or Tearing" id="dd2_pn_npt" ><label for="dd2_pn_npt">No Pain or Tearing</label></li>

</ul></td>
</tr>

<tr class="warning">
<td class="tstlftpan"><label class="title">&nbsp;</label></td>
<td ><ul class="ul_pt_neg">
	
	<li><input type="checkbox" value="no Pain with Eye Movement" id="dd2_pn_npem" ><label for="dd2_pn_npem">No Pain with Eye Movement</label></li>	
	<li><input type="checkbox" value="no Red Eye" id="dd2_pn_nre" ><label for="dd2_pn_nre">No Red Eye</label></li>
	<li><input type="checkbox" value="no ShadowcommaCurtain or Veil" id="dd2_pn_nscv" ><label for="dd2_pn_nscv">No Shadow, Curtain or Veil</label></li>
	<li><input type="checkbox" value="no Tearing" id="dd2_pn_nt" ><label for="dd2_pn_nt">No Tearing</label></li>	
	<li><input type="checkbox" value="no Visual Phenomenon" id="dd2_pn_nvp" ><label for="dd2_pn_nvp">No Visual Phenomenon</label></li>
	<li class="form-group form-inline pull-right"><label for="other_par_neg" class="nobdr">Other</label><textarea id="other_par_neg" name="other_par_neg" class="form-control" rows="1"></textarea></li>	

</ul></td>
</tr>

<tr class="warning">
<td class="tstlftpan"><label class="title">Other: </label></td>
<td  class="ul_other">

	<textarea id="rvs_followup_detail_other" name="rvs_followup_detail_other" rows="1" class="form-control"></textarea>

</td>
</tr>
</table>
</div>
<ul class="list-unstyled">
<li class="btns">
	<input onClick="return fillArray('detailDesc2');"  type="button"  class="btn btn-success" id="btn_dd1_done" value="Done" />
	<input onClick="return clearAll('detailDesc2');"  type="button"  class="btn btn-danger" id="btn_dd1_reset" value="Reset" />
	<input onClick="return closeWindow();"  type="button"  class="btn btn-danger" id="btn_dd1_cancel" value="Cancel" />
</li>

</ul>
</div>
<!-- Desc detail 2 -->


<input type="hidden" name="complaint1Text" value="<?php echo $complaint1StrDB; ?>">
<input type="hidden" name="complaint2Text" value="<?php echo $complaint2StrDB; ?>">
<input type="hidden" name="complaint3Text" value="<?php echo $complaint3StrDB; ?>">
<input type="hidden" name="complaintHeadText" value="<?php echo $complaintHeadDB; ?>">
<input type="hidden" name="selectedHeadText" value="<?php echo $selectedHeadDB; ?>">
<input type="hidden" name="titleHeadText" value="<?php echo $titleHeadDB; ?>">

<input type="hidden" name="divSelected" value="">
<input type="hidden" name="tdSelected" value="">

<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/rvs.js"></script>
<script>
var showDiv, globTitle;
<?php 
/*
var complaintHead = new Array(<?php echo ($complaintHeadDB)? "'".str_replace(",","','",addslashes($complaintHeadDB))."'" : "" ;?>);
var selectedHead = new Array(<?php echo ($selectedHeadDB)? "'".str_replace(",","','",addslashes($selectedHeadDB))."'" : "" ;?>);
var complaint1 = new Array(<?php echo ($complaint1StrDB)? "'".str_replace(",","','",addslashes($complaint1StrDB))."'" : "" ;?>);
var complaint2 = new Array(<?php echo ($complaint2StrDB)? "'".str_replace(",","','",addslashes($complaint2StrDB))."'" : "" ;?>);
var complaint3 = new Array(<?php echo ($complaint3StrDB)? "'".str_replace(",","','",addslashes($complaint3StrDB))."'" : "" ;?>);
var titleHeadArr = new Array(<?php echo "'".str_replace(",","','",addslashes($titleHeadDB))."'";?>);
*/
?>

var strAllConcat = new Array();

var str1Array = new Array("Both Eyes","Left Eye","Right Eye","Central vision","Peripheral vision","Paraxial vision","Left Eyelids","LUL","LLL","Right Eyelids","RUL","RLL",
				"Head","left side of head","right side of head","left side of face","right side of face","fore head","scalp");//location
var str2Array = new Array("Aching","Quality~Burning","dry","Quality~itching","pressure","scratchy","sharp","throbbing","watery",
					"distorted","foggy","ghosting","Quality~glare","hazy",
					"Quality~Decreased","Doing well","Feels improvement","Quality~Increased","Initially improved then worsened","New","No changes noted",
					"Resolved","Quality~Stable","Tolerating","Quality~Worsening","Stabbing","Dull");//quality
var str3Array = new Array("Decreased","Increased","Severity~Intermittent","Severity~Constant","Mild","Moderate","Severe","Worsening","None");//,"ScaleOf10" //severity
var str4Array = new Array("1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","20","30","45","Days","Weeks","Months","Years","Day","Week","Month","Year");//duration
//var str5Array = new Array("Sudden","Gradual","Constant","Comes and Goes","Patient unsure");//timing onset
var str5Array = new Array("many years","Quality~patient unsure", "since surgery","since birth","since childhood","since last visit");//timing onset
var str6Array = new Array("Reading","driving","inside","outside");//context
var str7Array = new Array("Ache","Blurry Vision","Burning","double vision","dizziness or light-headedness","ASAS~Dull","Eye pain","flashes","floater","Foreign body sensation","glare",
					"Halos","Headaches","Headache","high ocular pressure",
					"Irritation","Itching","Light sensitivity","ocular redness","Pain","Redness","Sharp","tearing","weakness","none");//associated signs

var str9Array = ["Constant","Gradual", "DoE~Intermittent","Sudden","DoE~Uncertain"];//doe
var str10Array = ["Blurry","Improved","Stable","Worse"];//vis
var str11Array = ['Dip~Improved','Dip~Mild','Dip~Worse','Dip~None'];//dip
var str12Array = ["is following medication instructions","has finished meds as instructed","is not taking medications","needs med refill","ran out of meds"];//med

var str13Array = [ "distance vision than near","MF~inside","night","near vision than distance","outdoors","with daily activities","with intensive visual activity (readingcomma computer)",
			"early in the day","in dim light","late in the day","when transition from dark to light","when transition from light to dark"]; //Modifying factors
			
var str14Array = [ "no Change in Amsler Grid","no Distortion","no Dryness","no eye Pain",
			"no FevercommaWeight LosscommaScalp TendernesscommaHeadache or Jaw Claudication",
			"no Flashes","no Flashescommafloatercommashadowcommacurtain or Veil","no floaters",
			"no Glare","no Headache","no Itching","no Loss of Consciousness","no Ocular Trauma","no Pain or Tearing","no Pain with Eye Movement",			
			"no Red Eye","no ShadowcommaCurtain or Veil",	"no Tearing",	"no Visual Phenomenon"]; //Pertinent Negatives

<?php
if($selectedHeadDB){
	echo "$('#".str_replace(",",",#",$selectedHeadDB)."').addClass('rvs_dt_dd_hgt');";
}
?>
$("#onSetDate, #elem_sp_surdate, #elem_os_othr_date").datepicker({"onChangeMonthYear":function(){ stopClickBubble();  } }).datepicker( "option", "dateFormat", "mm-dd-yy" );
$(".tabSec input[type=text], .tabSec textarea").bind("focus", function(){ var v=$.trim(this.value); if(v!=""){ $(this).bind("blur",function(){ $(this).trigger("change");  });  } });
</script>