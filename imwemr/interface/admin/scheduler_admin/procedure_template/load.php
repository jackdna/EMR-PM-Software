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
 * Purpose: load procedure templates
 * Access Type: Direct
 */
//------FILE INCLUSION------
include_once("../../../../config/globals.php");
include_once('../../../../library/classes/admin/scheduler_admin_func.php');

//------ GETTING PROCEDURE TEMPLATE RECORDS------
$strTempQry="SELECT 
				sp1.id,
				sp1.proc,
				sp1.acronym,
				sp2.times,
				sp1.proc_color,
				sp1.proc_type,
				sp1.after_start_time,
				sp1.after_end_time,
				sp1.max_allowed,
				sp1.active_status,
				sp1.intervals,
				sp1.proc_mess, 
				sp1.ref_management,
				sp1.verification_req,
				sp1.non_billable,
				sp1.exp_arrival_time
			FROM 
				slot_procedures sp1 
				LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id 
			WHERE 
				sp1.times = '' AND sp1.proc != ''
			AND 
				sp1.doctor_id = 0 
			AND 
				sp1.active_status!='del' 
			AND 
				sp1.source='' ORDER BY sp1.proc";
$rsTempData = imw_query($strTempQry);
if($rsTempData)
{
    $intCnt = imw_num_rows($rsTempData);
    if($intCnt > 0)
	{
        $strHTML = '
            <table class="table table-striped table-bordered cellBorder3 table_collapse">';
                $j = 0;$arr_proc=$arr_acronym=array();
                while($arrRow = imw_fetch_array($rsTempData)){
                    //------FETCHING PROCEDURE TIMINGS------
                    $strQry="SELECT 
								after_start_time,
								after_end_time 
							FROM 
								`slot_procedures_timings` 
							WHERE 
								procedureId = '".$arrRow['id']."' 
							AND 
								doctor_id = '0' 
							ORDER BY timeCount";
                    $rsTmData = imw_query($strQry); 
                    $strTimings = "NA";
                    if($rsTmData){
                         if(count($rsTmData) > 0)
						 {
                             $strTimings = "";
                             while($arrTmData = imw_fetch_array($rsTmData))
							 {
                                $arrFrom = explode(":",$arrTmData['after_start_time']);
                                $arrTo = explode(":",$arrTmData['after_end_time']);
                               
                                $fromHr = $arrFrom[0];
                                $fromMn = $arrFrom[1];
                                $toHr = $arrTo[0];
                                $toMn = $arrTo[1];
                                
                                if($fromHr > 12)
								{
                                    $fromHr = $fromHr - 12;
                                    $fromAP = "PM";     
                                }
								else if($fromHr == 12)
								{
                                    $fromAP = "PM";
                                }
								else if($fromHr == "00")
								{
                                    $fromHr = 12;
                                    $fromAP = "AM";
                                }
								else
								{
                                    $fromAP = "AM";
                                }
                                $fromHr = (strlen($fromHr) == 1) ? "0".$fromHr : $fromHr;
                                
                                
                                if($toHr > 12)
								{
                                    $toHr = $toHr - 12;
                                    $toAP = "PM";     
                                }
								else if($toHr == 12)
								{
                                    $toAP = "PM";
                                }
								else if($toHr == "00")
								{
                                    $toHr = 12;
                                    $toAP = "AM";
                                }
								else
								{
                                    $toAP = "AM";
                                }
                                $toHr = (strlen($toHr) == 1) ? "0".$toHr : $toHr;
                                
                                $strTimings .= $fromHr.":".$fromMn." ".$fromAP." - ".$toHr.":".$toMn." ".$toAP."<br>";
                             }
                         }
						 else
						 {
                             $strTimings = "NA";
                         }
                    }                   
                    
                        if($arrRow['times'] != "")
						{
							$strHTMLAttr = $arrRow['times']." Min"; 
                        }
						else
						{
                            $strHTMLAttr = "NA";
                        }
					$acronym="";	
					if(strlen($arrRow['acronym'])>33)
					{
						$acronym=substr($arrRow['acronym'],0,31).'...';
					}
					else
					{
						$acronym=$arrRow['acronym'];
					}
					
					$arr_proc[strtolower($arrRow['proc'])]=$arrRow['id'];
					$acronym_val=preg_replace('/[^`~!<>@$?a-zA-Z0-9_{}:; ,#%\[\]\.\(\)%&-+\/\\r\\n\\\\]/s','',($arrRow['acronym']));
					$arr_acronym[$acronym_val]=$arrRow['id'];
					$inactive_class=($arrRow['active_status']=="no")?"inactive hide":"";
				
				$strHTML .= '
                <tr class="'.$inactive_class.'">
                    <!--<td class="text-center">
						<div class="checkbox"><input type="checkbox" name="id" class="chk_sel" id="chk_sel_'.$arrRow['id'].'" value="'.$arrRow['id'].'"><label for="chk_sel_'.$arrRow['id'].'"></label></div>
					</td>-->
					<td class="text-left"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">'.$arrRow['proc'].'</a></td>
                    <td class="" title="'.$arrRow['acronym'].'"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">'.$acronym.'</a></td>
					<td class="" title="'.$arrRow['proc_type'].'"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">'.$arrRow['proc_type'].'</a></td>
                    <td class="text-nowrap"><font class=""><a  class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">'.$strHTMLAttr.'</a></td>';
                    if(trim($arrRow['exp_arrival_time']) != "")
					{
						$strHTML .= '<td class="text-center"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">'.$arrRow['exp_arrival_time'].'</a></td>';
                    }
					else
					{
                       $strHTML .= '<td class="text-center"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">NA</a></td>';   
                    }
					if(trim($arrRow['proc_color']) != "")
					{
                        $strHTML .= '<td class="text-center"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');"><div style="background-color:'.$arrRow['proc_color'].'; ">&nbsp;</div></a></td>';
                    }
					else
					{
                         $strHTML .= '<td class="text-center"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">NA</a></td>';
                    }
                    $strHTML .= '<td class="text-center"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">';
                    
                    $strHTML .= $strTimings;
                    $strHTML .= '</a></td>';
					
                    if(trim($arrRow['max_allowed']) != "")
					{
                    $strHTML .= '<td class="text-center"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">'.$arrRow['max_allowed'].'</a></td>';
                    }
					else
					{
                       $strHTML .= '<td class="text-center">NA</td>';   
                    }
                   
				    $strHTML .= '<td class="text-center"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">';
                            $strHTML .= $arrRow['proc_mess']; 
                    $strHTML .= '</a></td>
										<td class="text-center"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">'.($arrRow['ref_management'] ? 'Yes' : 'No').'</td>
										<td class="text-center"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">'.($arrRow['verification_req'] ? 'Yes' : 'No').'</td>
										<td class="text-center"><a class="" href="javascript:edit(\''.$arrRow['id'].'\',\'inside\');">'.($arrRow['non_billable'] ? 'No' : 'Yes').'</td>
					<td class="text-center text-nowrap" style=""><a class="" href="javascript:activeDeactive(\''.$arrRow['id'].'\',\''.$arrRow['active_status'].'\');">';
                    
                    if($arrRow['active_status'] == "yes")
					{ 
                        $strHTML .= '<span class="glyphicon glyphicon-stop text-success"></span>';
                    }
					else if($arrRow['active_status'] == "no")
					{ 
                        $strHTML .= '<span class="glyphicon glyphicon-stop text-danger"></span>';
                    }
                    
                    $strHTML .= '</a></td></tr>';
                   	$json_procedure_arr=json_encode($arr_proc);
					$json_acronym_arr=json_encode($arr_acronym);
					$j++;
                }
				$strHTML .=  "<div id='json_procedure' style='display:none;'>".$json_procedure_arr."</div>";
				$strHTML .=  "<div id='json_acronym' style='display:none;'>".$json_acronym_arr."</div>";
                $strHTML .= "</table>";
    }
	else
	{
            $strHTML = "<table width=\"100%\"><tr><td class='failureMsg'>".imw_msg('no_rec').". <a class=\"text_12b_purple\" href='javascript:edit(\"\",\"inside\");'>Click here</a> to add New Procedure Template.</td></tr></table>";
    }
}
echo $strHTML;
?>