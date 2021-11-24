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
File: test_vf_gl_print.php
Purpose: This file provides print version of VF-GL Test.
Access Type : Include file
*/
?>
<?php
ob_start();

$yes = ' (y)'; 	$no = ' (n)';	
$trHeight = "15";
$td1Width = "56";

if($finalize_flag == 1){
	$finalize = "&nbsp;&nbsp;&nbsp;&nbsp;(Finalized)";
}

	$testCSS = '
		<style>
			table{ font-size:12px; font-weight:normal;}
			.tb_heading{
				font-size:12px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000000;
				background-color:#BCD5E1;
			}
			.text_b{
				font-size:12px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000;
				background-color:#BCD5E1;
			}
			.text{
				font-size:12px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#FFFFFF;
			}
			.txt_11b { font-size:12px; font-weight:bold; }
			.alignLeft{ text-align:left; }
			.alignCenter{ text-align:center; }
			.alignRight{ text-align:right; }
			.valignTop { vertical-align:top; }
			.alignMiddle { vertical-align:middle; }
			.table_collapse { padding:0px; border-collapse:collapse; margin:0px; }
			.drak_purple_color {  color: #990099; font-weight:bold; }
			.blue_color { color: #0000FE; font-weight:bold;}
			.green_color { color: #008000; font-weight:bold;}
			.borderTop{ border-top:1px solid #C6CCE8;}
			.borderBottom{ border-bottom:1px solid #C6CCE8;}
			.borderLeft{ border-left:1px solid #C6CCE8;}
			.borderRight { border-right:1px solid #C6CCE8;}
			
		</style>'; 

		$testHeadFoot='
		<page backtop="9mm" backbottom="0.5mm">		
		<page_footer>
			<table style="width:100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>	
		<page_header>
			<table border="0" cellpadding="0" cellspacing="0">				
					<tr>
						<td colspan="3" align="center" style="width:1080px" class="text_b"><b>VF-GL TEST'.$finalize.'</b>
						</td>
					</tr>
					<tr>
						<td class="text_b" align="left" style="width:350px">Ordered By '.$order_name_p.' on '.$elem_opidTestOrderedDate.'</td>
						<td style="width:450px;" class="text_b" align="center">Patient Name:&nbsp;&nbsp;'.$patientName.' - ('.$patient_id.')</td>
						<td class="text_b" align="right" style="width:280px">DOS:&nbsp;&nbsp;'.FormatDate_show($elem_examDate).'</td>
					</tr>
			</table>
		</page_header>
		';
?>
  
                                                
<table style="width:100%" >
	<tr>
      <td colspan="3" style="width:100%;">
          <table class="table_collapse" border="0">
              <tr style="height:20px">
                  <td class="txt_11b" style="width:250px;">
                  <!-- VF-GL & Eye -->
                  <span >
                  VF-GL
                  </span>
                  <span style="padding-left:20px;">
                          <span class="drak_purple_color">OU</span>&nbsp; <?php echo ($elem_vfEye == "OU") ? $yes : $no; "" ;?>&nbsp;&nbsp; <span class="blue_color">OD</span>&nbsp;<?php echo ($elem_vfEye == "OD") ? $yes : $no; "" ;?>
                 &nbsp;&nbsp;<span class="green_color ">OS</span>&nbsp;<?php echo ($elem_vfEye == "OS") ? $yes : $no; "" ;?>
                  </span>
                  <!-- VF-GL & Eye -->
                  </td>
                  <td class="txt_11b alignRight valignTop" r1owspan="2" style="width:100px;">
                      &nbsp;&nbsp;<?php echo $elem_gla_mac_od_print; ?>
                      </td>
                  <td class="txt_11b alignRight valignTop" r1owspan="2" style="width:115px;">
                  &nbsp;&nbsp; <?php echo $elem_gla_mac_os_print; ?>   								                                                 </td>	
                  <td  r1owspan="2" style="width:450px;">
                      
                  </td>
              </tr>
        </table>
      </td>
     </tr> 
    <tr>
      <td colspan="3" style="width:100%;">
              <table class="table_collapse" border="0">
               <tr>
                <td  style="width:135px;" class="txt_11b valignTop"  >Technician Comments:</td>
                  <td  style="width:450px;" class="txt_11 valignTop"  ><?php if($elem_techComments != '' &&   $elem_techComments != 'Technician Comments' ){ echo $elem_techComments; } ?></td>
                   <td  style="width:400px;" class="pat_cop valignTop " >Patient Understanding & Cooperation&nbsp;&nbsp;Good:<?php echo ($elem_ptUnderstanding == '' || $elem_ptUnderstanding == "Good") ? $yes : $no; "" ;?>&nbsp;
            Fair:<?php echo ($elem_ptUnderstanding == '' || $elem_ptUnderstanding == "Fair") ? $yes : $no; "" ;?>                  &nbsp;                                                 
            Poor:<?php echo ($elem_ptUnderstanding == '' || $elem_ptUnderstanding == "Poor") ? $yes : $no; "" ;?>                           
               </td>	
              </tr>
            </table>
       </td>
     </tr>
     <tr>
      <td colspan="3" style="width:100%;">
              <table class="table_collapse" border="0">
             <tr>
                <td style="width:100px;" ><strong>Performed By</strong></td>
                <td style="width:250px;"> <?php echo $objTests->getPersonnal3($elem_performedBy);?>&nbsp;&nbsp;<?php echo $elem_performedByCurr;?>
                </td>							
          
                <td  style="width:350px" >&nbsp;
             </td>	
            </tr>
            <tr>
                <td style="width:100px;"><strong>Diagnosis</strong></td>
                <td   id="td_diagnosis" style="width:250px">
                <span class="blue_color txt_11b ">OD</span>&nbsp;
                 <?php  echo ($elem_diagnosis_od != "" && $elem_diagnosis_od != "Other" &&  $elem_diagnosis_od != "--Select--") ? $elem_diagnosis_od : inline-block; ?></td>							
          
                <td id="td_diagnosis" style="width:350px" ><span class="green_color txt_11b ">OS</span>&nbsp;
         	    <?php  echo ($elem_diagnosis_os != "" && $elem_diagnosis_os != "Other") ? $elem_diagnosis_os : inline-block; ?>	
             </td>	
            </tr>
       </table>                                                                	
      </td>
    </tr>
    <tr class="alignLeft alignMiddle">
        <td  style="height:10px;width:100%;" colspan="3" class="txt_11b">Physician Interpretation </td>
    </tr>
    
    <tr class="alignLeft alignMiddle"> 
       <td colspan="3" style="width:100%;height:10px;" class="txt_11b"  >Test Results</td>
    </tr>
    <tr>
	    <td colspan="3" style="width:100%;">
       	<table cellpadding="0" cellspacing="0" style="width:100%">
            <tr>
                <td colspan="4" style="width:405px;vertical-align:top;"   class="alignCenter borderTop borderLeft"><span  class="txt_11b blue_color ">OD</span></td>
                <td style="width:30px;vertical-align:middle;" rowspan="21" class="left_pupil_border right_pupil_border drak_purple_color txt_11b alignCenter alignMiddle borderTop borderLeft  borderRight ">BL</td>
               <td  colspan="4" style="width:350px;"   class="alignCenter borderTop borderRight"><span style="margin-left:10px;" class="txt_11b green_color">OS</span></td>
            </tr>
            <tr>
                <td colspan="4" style="width:405px;"  class="borderLeft borderTop" > <strong>Reliability</strong> &nbsp;&nbsp;&nbsp;&nbsp;Good&nbsp;<?php echo ($elem_reliabilityOd == "Good") ? $yes : $no; ""; ?>
            
            &nbsp;&nbsp;&nbsp;Fair&nbsp;<?php echo ($elem_reliabilityOd == "Fair") ? $yes : $no; ""; ?>
            &nbsp;&nbsp;&nbsp;Poor&nbsp;<?php echo ($elem_reliabilityOd == "Poor") ? $yes : $no; ""; ?>
                </td>
                <td  colspan="4" style="width:350px;"  class="borderRight borderTop" >
                <strong>Reliability</strong> &nbsp;&nbsp;&nbsp;&nbsp;Good&nbsp;<?php echo ($elem_reliabilityOs == "Good") ? $yes : $no; ""; ?>
                &nbsp;&nbsp;&nbsp;Fair&nbsp;<?php echo ($elem_reliabilityOs == "Fair") ? $yes : $no; ""; ?>
                &nbsp;&nbsp;&nbsp;Poor&nbsp;<?php echo ($elem_reliabilityOs == "Poor") ? $yes : $no; ""; ?>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="width:405px;" class="valignTop borderLeft" >
                <strong>MD</strong>&nbsp; <?php echo $elem_mdOd;?>&nbsp;dB &nbsp;&nbsp;&nbsp;&nbsp;<strong>PSD</strong>&nbsp; <?php echo $elem_psdOd;?>&nbsp;dB&nbsp;&nbsp;&nbsp;<strong>&nbsp;VFI</strong>&nbsp; <?php echo $elem_psdOd;?>&nbsp; %
                </td>
                <td colspan="4"  class="valignTop borderRight"  >
                <strong>MD</strong>&nbsp; <?php echo $elem_mdOs;?>&nbsp;dB &nbsp;&nbsp;&nbsp;&nbsp;<strong>PSD</strong>&nbsp; <?php echo $elem_psdOs;?>&nbsp;dB&nbsp;&nbsp;&nbsp;<strong>&nbsp;VFI</strong>&nbsp; <?php echo $elem_vfiOs;?>&nbsp; %      
                </td>
            </tr>
            <tr>
                <td colspan="4" style="width:405px;padding-top:10px;"  class="txt_11b valignTop testlbl borderLeft">Details: </td>
                <td colspan="4"  style="width:350px;padding-top:10px;"  class="txt_11b valignTop testlbl borderRight ">Details: </td>
            </tr>
            <tr>
                  <td colspan="4" style="width:405px;" class="txt_11b valignTop testlbl borderLeft">
                  &nbsp;&nbsp;High Fixation Loss&nbsp;<?php if(strpos($details_high_od,"High Fixation Loss")!==false) {  echo $yes;} else{$no;} ?> <br/>
                 &nbsp; High False Positive&nbsp;<?php if(strpos($details_high_od,"High False Positive")!==false) { echo $yes;} else{$no;} ?> <br/>
                  &nbsp;&nbsp;High False Negatives&nbsp;<?php if(strpos($details_high_od,"High False Negatives")!==false) {echo $yes;} else{$no;} ?>
                  </td>
                  <td colspan="4"  style="width:350px;"  class="txt_11b valignTop testlbl borderRight">&nbsp;&nbsp;High Fixation Loss&nbsp;<?php if(strpos($details_high_os,"High Fixation Loss")!==false) { echo $yes;} else{$no;} ?> <br/>
                  &nbsp;&nbsp;High False Positive&nbsp;<?php if(strpos($details_high_os,"High False Positive")!==false) { echo $yes;} else{$no;}?> <br/>
                 &nbsp;&nbsp;High False Negatives<?php if(strpos($details_high_os,"High False Negatives")!==false) { echo $yes;} else{$no;} ?> </td>
              </tr>
             <tr>
                <td colspan="4" style="width:405px;" class="borderTop borderLeft" ><strong>Poor Study</strong>&nbsp; <?php if(strpos($poor_study_od,"Poor Study")!==false) {echo $yes;}else{echo $no;}?> &nbsp;&nbsp;<?php if ($elem_poorStudyOd_desc!=""){echo $elem_poorStudyOd_desc;}   ?>  </td>
                <td colspan="4" style="width:350px;" class="borderTop borderRight"><strong>Poor Study</strong>&nbsp; <?php if(strpos($poor_study_os,"Poor Study")!==false) {echo $yes;}else{echo $no;}?> &nbsp;&nbsp;<?php if ($elem_poorStudyOs_desc!=""){echo $elem_poorStudyOs_desc;}   ?> </td>
    </tr>
         	<tr>
                <td colspan="4" style="width:405px;" class="txt_11b valignTop testlbl borderLeft">Intratest Fluctuation&nbsp;<?php if(strpos($intratest_fluctuation_od,"Intratest Fluctuation")!==false) {echo $yes;}else{$no;} ?> </td>
                <td colspan="4" style="width:350px;" class="txt_11b valignTop testlbl borderRight">Intratest Fluctuation&nbsp;<?php if(strpos($intratest_fluctuation_os,"Intratest Fluctuation")!==false) {echo $yes;}else{$no;} ?></td>
   			 </tr>
              <tr>
                  <td colspan="4" style="width:405px;" class="borderLeft"><strong>Artifact</strong>&nbsp;<?php if(strpos($artifact_od,"Artifact")!==false) {echo $yes;}else{echo $no;} ?> </td>
                  <td colspan="4" style="width:350px;" class="borderRight"><strong>Artifact</strong>&nbsp;<?php if(strpos($artifact_os,"Artifact")!==false) {echo $yes;}else{echo $no;} ?></td>
  			  </tr>
             <tr>
                    <td style="width:50px;" class="borderLeft" ></td>
                    <td style="width:100px;" class="txt_11b valignTop testlbl" >Details:</td>
                    <td style="width:155px;">Lid&nbsp;<?php if(strpos($details_lids_od,"Lid")!==false) {echo $yes;}else{echo $no;} ?><br/>Lens Power&nbsp;<?php if(strpos($details_lids_od,"Lens Power")!==false) {echo $yes;}else{echo $no;} ?>
                    </td>
                    <td style="width:215px;">Lens Rim&nbsp;<?php if(strpos($details_lids_od,"Lens Rim")!==false) {echo $yes;}else{echo $no;} ?><br/>Cloverleaf&nbsp;<?php if(strpos($details_lids_od,"Cloverleaf")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:50px;" ></td>
                    <td style="width:100px;" class="txt_11b valignTop testlbl" >Details:</td>
                    <td style="width:100px;" >Lid&nbsp;<?php if(strpos($details_lids_os,"Lid")!==false) {echo $yes;}else{echo $no;} ?><br/>Lens Power&nbsp;<?php if(strpos($details_lids_os,"Lens Power")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:200px;" class="borderRight">Lens Rim&nbsp;<?php if(strpos($details_lids_os,"Lens Rim")!==false) {echo $yes;}else{echo $no;} ?><br/>Cloverleaf&nbsp;<?php if(strpos($details_lids_os,"Cloverleaf")!==false) {echo $yes;}else{echo $no;} ?></td>
         	</tr>
           <tr>
                <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl  borderTop borderLeft">Normal / Full</td>
                <td colspan="2" style="width:255px;" class="borderTop"><?php if(strpos($normal_od,"Normal / Full")!==false) {echo $yes;}else{$no;} ?></td>
                <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderTop  ">Normal / Full</td>
                <td colspan="2" style="width:260px;"  class="borderTop borderRight"><?php if(strpos($normal_os,"Normal / Full")!==false) {echo $yes;}else{echo $no;} ?></td>
            </tr>
           <tr>
                <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderTop borderLeft">Nonspecific Defects</td>
                <td colspan="2" style="width:255px;" class="borderTop"> <?php if(strpos($nonspecific_od,"Nonspecific Details")!==false){echo $yes;}else{echo $no;}?></td>
                <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderTop ">Nonspecific Defects</td>
                 <td colspan="2" style="width:200px; " class="borderTop borderRight"> <?php if(strpos($nonspecific_os,"Nonspecific Details")!==false){echo $yes;}else{echo $no;}?></td>
               </tr>
            	<tr>
                    <td colspan="2" style="width:150px;" class="borderTop borderLeft" ></td>
                    <td style="width:100px;" class="txt_11b valignTop testlbl borderTop">Superior</td>
                    <td style="width:155px;" class="txt_11b valignTop testlbl borderTop borderRight">Inferior</td>
                    <td colspan="2" style="width:150px;" class="borderTop borderLeft" ></td>
                    <td style="width:100px;" class="txt_11b valignTop testlbl borderTop">Superior</td>
                    <td style="width:100px;" class="txt_11b valignTop testlbl borderTop borderRight">Inferior</td>
         	</tr>
            <tr>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderLeft" >Nasal Step</td>
                    <td style="width:100px;" ><?php if(strpos($nasal_step_od,"Superior")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:155px;" ><?php if(strpos($nasal_step_od,"Inferior")!==false) {echo $yes;}else{echo $no;} ?> </td>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl" >Nasal Step</td>
                    <td style="width:100px;" ><?php if(strpos($nasal_step_os,"Superior")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:100px;" class="borderRight" ><?php if(strpos($nasal_step_os,"Inferior")!==false) {echo $yes;}else{echo $no;} ?> </td>
         	</tr>
             <tr>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderLeft" >Arcuate</td>
                    <td style="width:100px;" ><?php if(strpos($arcuate_od,"Superior")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:155px;" ><?php if(strpos($arcuate_od,"Inferior")!==false) {echo $yes;}else{echo $no;} ?> </td>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl" >Arcuate</td>
                    <td style="width:100px;" ><?php if(strpos($arcuate_os,"Superior")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:100px;" class="borderRight" ><?php if(strpos($arcuate_os,"Inferior")!==false) {echo $yes;}else{echo $no;} ?> </td>
         	</tr>
            <tr>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderLeft" >Hemifield</td>
                    <td style="width:100px;" ><?php if(strpos($hemifield_od,"Superior")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:155px;" ><?php if(strpos($hemifield_od,"Inferior")!==false) {echo $yes;}else{echo $no;} ?> </td>
                    <td colspan="2" style="width:150px;"  class="txt_11b valignTop testlbl">Hemifield</td>
                    <td style="width:100px;" ><?php if(strpos($hemifield_os,"Superior")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:100px;" class="borderRight" ><?php if(strpos($hemifield_os,"Inferior")!==false) {echo $yes;}else{echo $no;} ?> </td>
         	</tr>
            <tr>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderLeft" >Paracentral</td>
                    <td style="width:100px;" ><?php if(strpos($paracentral_od,"Superior")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:155px;" ><?php if(strpos($paracentral_od,"Inferior")!==false) {echo $yes;}else{echo $no;} ?> </td>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl" >Paracentral</td>
                    <td style="width:100px;" ><?php if(strpos($paracentral_os,"Superior")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:100px;" class="borderRight" ><?php if(strpos($paracentral_os,"Inferior")!==false) {echo $yes;}else{echo $no;} ?> </td>
         	</tr>
            <tr>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderLeft" >Into Fixation</td>
                    <td style="width:100px;" ><?php if(strpos($into_fixation_od,"Superior")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:155px;" ><?php if(strpos($into_fixation_od,"Inferior")!==false) {echo $yes;}else{echo $no;} ?> </td>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl" >Into Fixation</td>
                    <td style="width:100px;" ><?php if(strpos($into_fixation_os,"Superior")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td style="width:100px;" class="borderRight" ><?php if(strpos($into_fixation_os,"Inferior")!==false) {echo $yes;}else{echo $no;} ?> </td>
         	</tr>
            <tr>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderLeft" >Central Island</td>
                    <td colspan="2" style="width:255px;"  >Remaining&nbsp;<?php echo $central_island_od;?>&nbsp;degrees </td>
                    
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl" >Central Island</td>
                    <td colspan="2" style="width:200px;" class="borderRight" >Remaining&nbsp;<?php echo $central_island_os;?>&nbsp;degrees </td>
         	</tr>
            <tr>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderTop borderLeft" >Enlarged Blind Spot</td>
                    <td colspan="2" style="width:255px;" class="borderTop" ><?php if(strpos($enlarged_blind_spot_od,"Enlarged Blind Spot")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderTop" >Enlarged Blind Spot</td>
                    <td colspan="2" style="width:255px;" class="borderTop borderRight" ><?php if(strpos($enlarged_blind_spot_os,"Enlarged Blind Spot")!==false) {echo $yes;}else{echo $no;} ?></td>
         	</tr>
            <tr>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderLeft" >Cecocentral Scotoma</td>
                    <td colspan="2" style="width:255px;" ><?php if(strpos($cecocentral_scotone_od,"Cecocentral Scotone")!==false) {echo $yes;}else{echo $no;} ?></td>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl" >Cecocentral Scotoma</td>
                    <td colspan="2" style="width:255px;" class="borderRight" ><?php if(strpos($cecocentral_scotone_os,"Cecocentral Scotone")!==false) {echo $yes;}else{echo $no;} ?></td>
         	</tr>
            <tr>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl borderLeft" >Central Scotoma</td>
                    <td colspan="2" style="width:255px;" ><?php if(strpos($central_scotoma_od,"Central Scotoma")!==false) {echo $yes;}else{ echo $no;} ?></td>
                    <td colspan="2" style="width:150px;" class="txt_11b valignTop testlbl" >Central Scotoma</td>
                    <td colspan="2" style="width:255px;" class="borderRight" ><?php if(strpos($central_scotoma_os,"Central Scotoma")!==false) {echo $yes;}else{ echo $no;} ?></td>
         	</tr>
             <tr>
                    <td style="width:50px;" class="borderTop"></td>
                    <td style="width:100px;" class="txt_11b valignTop testlbl borderTop " > Hemianopsis</td>
                    <td colspan="2" style="width:255px;" class="borderTop">Right&nbsp;<?php if(strpos($hemianopsia_od,"Right")!==false) {echo $yes;}else{echo $no;} ?>&nbsp;&nbsp;&nbsp; Left&nbsp;<?php if(strpos($hemianopsia_od,"Left")!==false) {echo $yes;}else{echo $no;} ?>&nbsp;&nbsp;&nbsp; Bitemporal&nbsp;<?php if(strpos($hemianopsia_od,"Bitemporal")!==false) {echo $yes;}else{echo $no;} ?></td>
                   <td style="width:30px;" class="borderTop">&nbsp;</td>
                    <td colspan="4" style="width:350px;"  class="borderTop">&nbsp;</td>
           </tr>
            
            
            
            
             <tr>
                    <td style="width:50px;"></td>
                    <td style="width:100px;" class="txt_11b valignTop testlbl " > Quadranopsia</td>
                    <td colspan="7" style="width:635px;"> Right Superior &nbsp;<?php if(strpos($quadrantanopsia_od,"Right Superior")!==false) {echo $yes;}else{echo $no;} ?>&nbsp;&nbsp;&nbsp; Left Superior&nbsp;<?php if(strpos($quadrantanopsia_od,"Left Superior")!==false) {echo $yes;}else{echo $no;} ?>&nbsp;&nbsp;&nbsp; Right Inferior&nbsp;<?php if(strpos($quadrantanopsia_od,"Right Inferior")!==false) {echo $yes;}else{echo $no;} ?>&nbsp;&nbsp;&nbsp; Left Inferior&nbsp;<?php if(strpos($quadrantanopsia_od,"Left Inferior")!==false) {echo $yes;}else{echo $no;} ?></td>
         	</tr>
            <tr>
                    <td style="width:50px;"></td>
                    <td style="width:100px;"  class="txt_11b valignTop testlbl"  > Congruity</td>
                    <td colspan="7" style="width:635px;"> Congruent &nbsp;<?php if(strpos($homonomous_od,"Congruent")!==false) {echo $yes;}else{echo $no;} ?>&nbsp;&nbsp;&nbsp; Incongruent&nbsp;<?php if(strpos($homonomous_od,"Incongruent")!==false) {echo $yes;}else{echo $no;} ?></td>
         	</tr>
             <tr>
                    <td style="width:50px;" class="txt_11b valignTop testlbl">Synthesis</td>
                    <td colspan="3" style="width:355px;" >  &nbsp;<?php echo $synthesis_od; ?></td>
                    <td style="width:30px;" ></td>
                    <td style="width:50px;" class="txt_11b valignTop testlbl">Synthesis</td>
                    <td colspan="3" style="width:300px;" > &nbsp; <?php echo $synthesis_os; ?></td>
                  
         	</tr>
          </table>
          </td>
     </tr>
 </table>
<table cellpadding="0" cellspacing="0" style="width:100%">
  <tr>
      <td colspan="4" style="width:405px;"> <?php if(strpos($elem_interpretation_OD,"Stable")!==false) {echo $yes;}else{echo $no;}?>Stable &nbsp;&nbsp;&nbsp;<?php if(strpos($elem_interpretation_OD,"Not Improve")!==false) {echo $yes;}else{echo $no;} ?> Not Improve&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_interpretation_OD,"Worse")!==false) {echo $yes;}else{echo $no;}?> Worse <br/><?php if(strpos($elem_interpretation_OD,"Likely progression")!==false) {  echo $yes;}else{echo $no;}?> Likely progression &nbsp;&nbsp;&nbsp;<?php if(strpos($elem_interpretation_OD,"Possible progression")!==false) {echo $yes;}else{echo $no;}?>  Possible progression </td>
      <td style="width:30px;"></td>
      <td colspan="4" style="width:350px;"><?php if(strpos($elem_interpretation_OS,"Stable")!==false) {echo $yes;}else{echo $no;}?>Stable &nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_interpretation_OS,"Not Improve")!==false) {echo $yes;}else{echo $no;} ?> Not Improve&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_interpretation_OS,"Worse")!==false) {echo $yes;}else{echo $no;}?> Worse<br/><?php if(strpos($elem_interpretation_OS,"Likely progression")!==false) {  echo $yes;}else{echo $no;}?> Likely progression &nbsp;&nbsp;&nbsp;<?php if(strpos($elem_interpretation_OS,"Possible progression")!==false) {echo $yes;}else{echo $no;}?>  Possible progression</td>
  </tr>
  <tr>
      <td colspan="4" style="width:405px;"><?php echo $elem_comments_interp; ?> <br/><?php /*if(strpos($elem_interpretation,"Glaucoma Stage")!==false) {  echo $yes;}else{echo $no;}*/ ?> Glaucoma Stage</td>
      <td style="width:30px;"></td>
     <td style="width:50px;"></td>
      <td style="width:100px;"></td>
      <td style="width:100px;"></td>
      <td style="width:100px;"></td>
  </tr>
  <tr>
      <td colspan="4" style="width:405px;"><!--<?php if(strpos($elem_glaucoma_stage_opt_OD,"Unspecified")!==false || (empty($elem_glaucoma_stage_opt_OD))){echo $yes;}else{echo $no;}?> Unspecified&nbsp;&nbsp;&nbsp;&nbsp;--><?php if(strpos($elem_glaucoma_stage_opt_OD,"Mild")!==false) {echo $yes;}else{echo $no;}?> Mild&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_glaucoma_stage_opt_OD,"Moderate")!==false) {echo $yes;}else{echo $no;} ?>Moderate
      <br/>
      <?php if(strpos($elem_glaucoma_stage_opt_OD,"Severe")!==false) {echo $yes;}else{echo $no;} ?> Severe(not mild and moderate)&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_glaucoma_stage_opt_OD,"Indeterminate")!==false) {echo $yes;}else{echo $no;} ?> Indeterminate 
</td>
      <td style="width:30px;"></td>
      <td colspan="4" style="width:305px;"><!--<?php if(strpos($elem_glaucoma_stage_opt_OS,"Unspecified")!==false || (empty($elem_glaucoma_stage_opt_OS))){echo $yes;}else{echo $no;}?> Unspecified&nbsp;&nbsp;&nbsp;&nbsp;--><?php if(strpos($elem_glaucoma_stage_opt_OS,"Mild")!==false) {echo $yes;}else{echo $no;}?> Mild&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_glaucoma_stage_opt_OS,"Moderate")!==false) {echo $yes;}else{echo $no;} ?>Moderate
      <br/>
      <?php if(strpos($elem_glaucoma_stage_opt_OS,"Severe")!==false) {echo $yes;}else{echo $no;} ?> Severe(not mild and moderate)&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_glaucoma_stage_opt_OS,"Indeterminate")!==false) {echo $yes;}else{echo $no;} ?> Indeterminate </td>
  </tr>
  <tr>
      <td colspan="9" style="width:100%px;" class="txt_11b" >Plan</td>
    
  </tr>
  <tr>
      <td colspan="9" style="width:100%px;" ><?php if(strpos($elem_plan,"Pt informed of results by physician today")!==false) {echo $yes;}else{echo $no;} ?> Pt informed of results by physician today&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_plan,"to be called by technician")!==false) {echo $yes;}else{echo $no;} ?> to be called by technician&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_plan,"by letter")!==false) {echo $yes;}else{echo $no;} ?> by letter&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_plan,"will inform next visit")!==false) {echo $yes;}else{echo $no;} ?> will inform next visit </td>
   </tr>
     <tr>
      <td colspan="9" style="width:100%px;" ><?php if(strpos($elem_plan,"Continue meds")!==false) {echo $yes;}else{echo $no;} ?> Continue meds
      &nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_plan,"Monitor findings")!==false) {echo $yes;}else{echo $no;} ?> Monitor findings
      &nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_plan,"Repeat test time")!==false) {  echo $yes;}else{echo $no;}?> Repeat test
      &nbsp;&nbsp;&nbsp;&nbsp;<?php echo (empty($elem_repeatTestVal1) || $elem_repeatTestVal1 == "Next visit") ? $elem_repeatTestVal1 : "";?>
      &nbsp;&nbsp;<?php echo ($elem_repeatTestVal2);?>
      &nbsp;&nbsp;<?php echo ($elem_repeatTestEye == "OU") ? $yes :  $no; "";?><span class="drak_purple_color" > OU</span> &nbsp;
       <?php echo ($elem_repeatTestEye == "OD") ? $yes : $no; "";?><span class="blue_color"> OD</span>&nbsp;&nbsp;
      <?php echo ($elem_repeatTestEye == "OS") ? $yes : $no; "";?> <span class="green_color">OS</span> </td>
   </tr>
    <tr>
      <td colspan="9" style="width:100%px;" ><?php echo (!empty($elem_comments)) ? $elem_comments : "Comments" ; ?></td>
  </tr>
  <tr>
      <td colspan="2" style="width:150px;"class="txt_11b alignRight"> Interpreted By:</td>
      
      <td colspan="2" style="width:255px;"><?php echo $getPersonnal3 ; ?></td>
      <td style="width:30px;"></td>
      <td colspan="2" style="width:150px;"class="txt_11b alignRight"> Future Appointments:</td>
      
      <td colspan="2" style="width:200px;"><?php $data = $objTests->getFutureApp($patient_id);echo $data;?></td>
  </tr>
  <tr>
      <td style="width:50px;"></td>
      <td style="width:100px;"></td>
      <td style="width:100px;"></td>
      <td style="width:155px;"></td>
      <td style="width:30px;"></td>
      <td style="width:50px;"></td>
      <td style="width:100px;"></td>
      <td style="width:100px;"></td>
      <td style="width:100px;"></td>
  </tr>
  <tr>
      <td colspan="9" style="width:100%;border:none;"><?php echo $sign_path_print; ?>     </td>
  </tr> 
</table>
 </page>

<?php
$strPrint = $STRPRINT;
if($strPrint!='') {
echo $testHeadFoot;
echo $strPrint;
echo "</page>";
}
 

$testPrint = $testCSS.$testHeadFoot;
$testPrint.= ob_get_contents();
file_put_contents($final_html_file_name_path.".html",$testPrint);

ob_end_clean(); 
?>

 