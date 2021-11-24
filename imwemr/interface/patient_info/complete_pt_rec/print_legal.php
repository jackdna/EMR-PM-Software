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

//------SESSION PATIENT ID------------------ 
$pid = $_SESSION['patient'];	

//------GET SIGNATURE SIGNATURE IN CONSENT FORM
function getLegalSignature($form_information_id,$patient_id,$consent_form_id,$consent_form_content)
{
	//--START CODE TO SET NEW CLASS TO CONSENT FORMS
	$htmlFolder = "html2pdf";
	$htmlV2Class = false;
	$htmlFilePth = "html2pdf/index.php";
	
	if(constant("CONSENT_FORM_VERSION")=="consent_v2")
	{
		$htmlFolder = "new_html2pdf";
		$htmlV2Class=true;	
		$htmlFilePth = "new_html2pdf/createPdf.php";
	}
	//--END CODE TO SET NEW CLASS TO CONSENT FORMS
	
	//--MYSQL QUERY TO GET SIGNATURE IMAGE PATHS
	$qry = imw_query("
					SELECT 
						signature_image_path,
						signature_count 
					FROM
						`consent_form_signature`
					WHERE
						form_information_id = '$form_information_id' 
					AND
						patient_id = '$patient_id'
					AND
						consent_form_id = '$consent_form_id' 
					AND
						signature_status = 'Active' 
					ORDER BY
						signature_count
					");
					
	while($row = imw_fetch_array($qry))
	{
		$sigDetail[] = $row;
	}
	if(count($sigDetail) > 0)
	{
		$sig_con = array();
		for($s=0;$s<count($sigDetail);$s++)
		{
			$sig_con[$s] = $sigDetail[$s]['signature_image_path'];
			$signature_count[$s] = $sigDetail[$s]['signature_count'];
		}
			
		$deletePath=array();
		for($ps=0;$ps<count($sig_con);$ps++)
		{
		$row_arr = explode('{START APPLET ROW}',$consent_form_content);
		$sig_arr = explode('{SIGNATURE}',$row_arr[0]);	
		$sig_data = '';
		$ds=0;
		$coun=0;
		
		for($s=1;$s<count($sig_arr);$s++)
		{
			if($s==$signature_count[$ds])
			{
				$postData = $sig_con[$coun];
				$path1 = split("/",$postData);
					if(isset($path1[1]) && !empty($path1[1]))
					{
						if($htmlV2Class==true && file_exists($postData))
						{
							$sig_data = '<table>
							<tr>
								<td>
									<img src="'.$postData.'" height="80" width="240">
								</td>
							</tr></table>';
							}else{
							$sig_data = '<table>
							<tr>
								<td>
									<img src="'.$path1[1].'" height="80" width="240">
								</td>
							</tr></table>';
						}
						$str_data = $sig_arr[$s];
						$sig_arr[$s] = $sig_data;
						$sig_arr[$s] .= $str_data;
						$hiddenFields[] = true;
					}
					$coun++;
				$ds++;
			}
		}
		
		$consent_form_content = implode(' ',$sig_arr);
		$content_row = '';
		for($ro=1;$ro<count($row_arr);$ro++)
		{
			if($row_arr[$ro])
			{
				$sig_arr1 = explode('{SIGNATURE}',$row_arr[$ro]);
				$td_sign = '';
				for($t=0;$t<count($sig_arr1)-1;$t++,$ds++)
				{
					$sig_arr1[$t] = str_ireplace('&nbsp;','',$sig_arr1[$t]);
					$td_sign .= '
						<td align="left">
							<table border="0">
								<tr><td>'.$sig_arr1[$t].'</td></tr>
								<tr>
									<td style="border:solid 1px" bordercolor="#FF9900">
										{SIGNATURE}
									</td>
								</tr>
							</table>
						</td>	
					';
					$s++;
					$hiddenFields[] = true;
				}
				$content_row .= '
					<table width="145" border="1" align="center">
						<tr>
							'.$td_sign.'						
						</tr>
					</table>
				';
			}
		}
			$jh = 1;
			$consent_form_content .= $content_row;
		}
	}
	else
	{
		$consent_form_content = str_ireplace('{SIGNATURE}',"",$consent_form_content);
	}
	return $consent_form_content;
}
//------END GET SIGNED SIGNATURES IN CONSENTS FORMS--------

//------START FUNCTION TO REPLACE TO TEMPLATE TEXTBOXES WITH THEIR VALUES
function formatTextBoxesAndTextArea($consent_form_content)
{
	
	//--START CODE TO SET NEW CLASS TO CONSENT FORMS
	$htmlFolder = "html2pdf";
	$htmlV2Class = false;
	$htmlFilePth = "html2pdf/index.php";
	
	if(constant("CONSENT_FORM_VERSION")=="consent_v2")
	{
		$htmlFolder = "new_html2pdf";
		$htmlV2Class=true;	
		$htmlFilePth = "new_html2pdf/createPdf.php";
	}
	//--END CODE TO SET NEW CLASS TO CONSENT FORMS
	
	$consent_form_content = str_ireplace('&nbsp;',' ',$consent_form_content);
	if($htmlV2Class==false)
	{
		$consent_form_content = str_ireplace('</div>','<br>',$consent_form_content);
	}
	
	$consent_form_content = str_ireplace("text' name='medium' size='60' maxlength='60'>",'',$consent_form_content);
	$inputVal = explode('<input',$consent_form_content);
	$consent_form_content = $inputVal[0];
	
	for($i=1;$i<count($inputVal);$i++)
	{
		$pos = strpos($inputVal[$i],'value="');
		$str = substr($inputVal[$i],$pos+7);
		$pos1 = strpos($str,'"');
		$inputVals = substr($str,0,$pos1);
		$pos2 = strpos($str,'>');
		$lastVal = substr($str,$pos2+1);
		$consent_form_content .= $inputVals.' '.$lastVal;
	}
	$inputValTextarea=explode('<textarea rows="2" cols="100" name="large',$consent_form_content);
	
	if(is_array($inputValTextarea))
	{
		for($i=1;$i<count($inputValTextarea);$i++)
		{
			$consent_form_content = str_ireplace('<textarea rows="2" cols="100" name="large">','',$consent_form_content);
			$consent_form_content = str_ireplace('<textarea rows="2" cols="100" name="large'.$i.'">','',$consent_form_content);
			$consent_form_content = str_ireplace('</textarea>','',$consent_form_content);
		}
	}
		
		return $consent_form_content;
}
//------END CODE TO REPLACE TEXTBOX VALUES--------
if($pid=="")
{
?>

<script>
	alert('Please select Patient to Proceed');
	window.close();
</script>
<?php 
}	
//------DATA FORMATTING WORK STARTS HERE----------
?>
<table style="width:750px;" border="0" cellpadding="2" cellspacing="0">
	<tr>
		<td align="left">
			<?php 	
			$sqlLegalForm="	SELECT 
								* 
							FROM 
								`patient_consent_form_information` 
							WHERE
								patient_id='".$_SESSION['patient']."' 
							AND
								movedToTrash='0' 
							ORDER BY 
								`form_information_id` 
							DESC
						  ";
			$resLegalForm=imw_query($sqlLegalForm) or print(imw_error());
			if($resLegalForm)
			{
				if(imw_num_rows($resLegalForm)>0)
				{
					while($resRowLegalForms=imw_fetch_array($resLegalForm))
					{
			?>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="middle" class="text_10b"  height="1" style="border-bottom:1px solid #012778!important;height:1px;">&nbsp;</td>
						</tr>
						<tr>
							<td valign="middle" class="text_10b" height="2" ></td>
						</tr>
						<tr height="25px">
							<td valign="middle" class="text_10b"  style="width:700px;font-size:14px;color:#012778;font-weight:bold;">	  
								<?php echo (strtoupper($resRowLegalForms["consent_form_name"]));?>
							</td>
						</tr>
					</table>	
					<?php	
					
					$form_information_id = $resRowLegalForms["form_information_id"];  //PRIMARY ID
					$consent_form_content= stripslashes(html_entity_decode($resRowLegalForms["consent_form_content_data"]));
					$consent_form_content= formatTextBoxesAndTextArea($consent_form_content);
					$consent_form_id	 = $resRowLegalForms["consent_form_id"];  //TEMPLATE ID
					$patient_id			 = $resRowLegalForms["patient_id"];
					$legalData			 = getLegalSignature($form_information_id,$patient_id,$consent_form_id,$consent_form_content); //GET SIGNATURE IMAGES PATHS
					
					//-----IMAGES REPLACEMENT WORK STARTS HERE--------
					$legalData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/',$legalData);
					
					$sigPlusimgReplace=$GLOBALS["include_root"].'/SigPlus_images/';
					$legalData= str_ireplace($GLOBALS['webroot'].'/interface/SigPlus_images/',$sigPlusimgReplace,$legalData);

					$printableData = '';
					if(substr( $legalData, 0, 4 ) === "&lt;")
					{
						$printableData = html_entity_decode($legalData);						
					} 
					else 
					{
						$printableData = $legalData;
					}

					$consultTemplateDataPage = preg_replace('/font-family.+?;/', "", $printableData);

					echo("<p>".$consultTemplateDataPage."</p>");

					}
				}
			}

			?>
		</td>
	</tr>
</table>