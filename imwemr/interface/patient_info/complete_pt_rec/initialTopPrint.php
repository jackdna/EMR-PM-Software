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

?><?php
	$GLOBALDATEFORMAT = $GLOBALS["date_format"];
  //if Past Glucoma Record exists
    $sql = imw_query("SELECT *
            FROM glucoma_main
            WHERE patientId = '".$_SESSION["patient"]."'
            AND activate = '1' ");
    while($row = imw_fetch_array($sql)){
        // Record Exists
        //if(!isset($_POST["elem_dateActivation"]) || empty($_POST["elem_dateActivation"])){
        $elem_dateActivation = $row["dateActivation"];
        //}


		//By Karan
		$elem_glucomaId = $row["glucomaId"];
        $elem_activate = $row["activate"];
		//$elem_dateDiagnosis = $cpr->checkWrongDate($row["dateDiagnosis"]);
		$elem_dateDiagnosis =  get_date_format($cpr->checkWrongDate($row["dateDiagnosis"]),"mm-dd-yyyy");
		$elem_diagnosisOd = nl2br($row["diagnosisOd"]);
        $elem_diagnosisOs = nl2br($row["diagnosisOs"]);
		//$elem_dateHighTaOd = $cpr->checkWrongDate($row["dateHighTaOd"]);
		$elem_dateHighTaOd =  get_date_format($cpr->checkWrongDate($row["dateHighTaOd"]),"mm-dd-yyyy");
		$elem_highTaOdOd = $row["highTaOdOd"];
        $elem_highTaOdOs = $row["highTaOdOs"];
		//$elem_dateHighTaOs = $cpr->checkWrongDate($row["dateHighTaOs"]);
		$elem_dateHighTaOs =  get_date_format($cpr->checkWrongDate($row["dateHighTaOs"]),"mm-dd-yyyy");
		$elem_highTaOsOd = $row["highTaOsOd"];
        $elem_highTaOsOs = $row["highTaOsOs"];
		//$elem_dateHighTpOd = $cpr->checkWrongDate($row["dateHighTpOd"]);
		$elem_dateHighTpOd =  get_date_format($cpr->checkWrongDate($row["dateHighTpOd"]),"mm-dd-yyyy");
		$elem_highTpOdOd = $row["highTpOdOd"];
        $elem_highTpOdOs = $row["highTpOdOs"];
		//$elem_dateHighTpOs = $cpr->checkWrongDate($row["dateHighTpOs"]);
		$elem_dateHighTpOs =  get_date_format($cpr->checkWrongDate($row["dateHighTpOs"]),"mm-dd-yyyy");
		$elem_highTpOsOd = $row["highTpOsOd"];
        $elem_highTpOsOs = $row["highTpOsOs"];
		//$elem_dateVf = $cpr->checkWrongDate($row["dateVf"]);
		$elem_dateVf =  get_date_format($cpr->checkWrongDate($row["dateVf"]),"mm-dd-yyyy");
		$elem_vfOdSummary = $row["vfOdSummary"];
        $elem_vfOsSummary = $row["vfOsSummary"];
		//$elem_dateNfa = $cpr->checkWrongDate($row["dateNfa"]);
		$elem_dateNfa =  get_date_format($cpr->checkWrongDate($row["dateNfa"]),"mm-dd-yyyy");
		$elem_nfaOdSummary = $row["nfaOdSummary"];
        $elem_nfaOsSummary = $row["nfaOsSummary"];
		//$elem_dateGonio = $cpr->checkWrongDate($row["dateGonio"]);
		$elem_dateGonio =  get_date_format($cpr->checkWrongDate($row["dateGonio"]),"mm-dd-yyyy");
		$elem_gonioOd = $row["gonioOd"];
        $elem_gonioOs = $row["gonioOs"];
		//$elem_datePachy = $cpr->checkWrongDate($row["datePachy"]);
		$elem_datePachy =  get_date_format($cpr->checkWrongDate($row["datePachy"]),"mm-dd-yyyy");
		$elem_pachyOdReads = $row["pachyOdReads"];

        $elem_pachyOdAvg = $row["pachyOdAvg"];
        $elem_pachyOdCorr = $row["pachyOdCorr"];
        $elem_pachyOsReads = $row["pachyOsReads"];
        $elem_pachyOsAvg = $row["pachyOsAvg"];
        $elem_pachyOsCorr = $row["pachyOsCorr"];
		//$elem_dateDiskPhoto = $cpr->checkWrongDate($row["dateDiskPhoto"]);
		$elem_dateDiskPhoto =  get_date_format($cpr->checkWrongDate($row["dateDiskPhoto"]),"mm-dd-yyyy");
		$elem_diskPhotoOd = $row["diskPhotoOd"];
        $elem_diskPhotoOs = $row["diskPhotoOs"];
		//$elem_dateCd = $cpr->checkWrongDate($row["dateCd"]);
		$elem_dateCd =  get_date_format($cpr->checkWrongDate($row["dateCd"]),"mm-dd-yyyy");
		$elem_cdOd = $row["cdOd"];
        $elem_cdOs = $row["cdOs"];
        $elem_riskFactors = $row["riskFactors"];
        $elem_warnings = $row["warnings"];
        $elem_cd_app_od = $row["cdAppOd"];
        $elem_cd_app_os = $row["cdAppOs"];
        $elem_notes = $row["notes"];
        $elem_cee = $row["cee"];
		//$elem_dateCee = $cpr->checkWrongDate($row["ceeDate"]);
		$elem_dateCee =  get_date_format($cpr->checkWrongDate($row["ceeDate"]),"mm-dd-yyyy");
        $elem_ceeNotes = $row["ceeNotes"];

				$elem_dateHighTmaxOd = $row["elem_dateHighTmaxOd"];
				$elem_highTmaxOdOd = $row["elem_highTmaxOdOd"];
				$elem_dateHighTmaxOs = $row["elem_dateHighTmaxOs"];
				$elem_highTmaxOsOs = $row["elem_highTmaxOsOs"];

    }
   //Targets
  list($targetOdTa,$targetOsTa,$targetOdTp,$targetOsTp) = $cpr->getIopTargets($_SESSION["patient"]);



?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" >
<tr>
    <td valign="top">
<!-- Past Reading -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" >
<tr align="left" height="10" valign="top">
    <td class="text_gp9b" width="63">Description</td>
    <td class="text_gp9b" width="77" >Date</td>
    <td class="text_gp9b" ><font color="blue">OD</font></td>
    <td class="text_gp9b" ><font color="Green">OS</font></td>
</tr>
<tr valign="top" valign="top"> <!-- Diagnosis -->
    <td class="text_gp9b">Diagnosis</td>
    <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateDiagnosis;?></td>
            </tr>
        </table>
        <!--Date-->
    </td>
    <td class="text_gp9"><?php echo $elem_diagnosisOd;?></td>
    <td class="text_gp9"><?php echo $elem_diagnosisOs;?></td>
</tr>

<tr valign="top"> <!-- High Tmax OD -->
       <td class="text_gp9b">T<sub>max</sub> OD</td>
       <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateHighTmaxOd;?></td>
            </tr>
        </table>
        <!--Date-->
      </td>
    <td class="text_gp9"><?php echo $elem_highTmaxOdOd;?></td>
    <td class="text_gp9"></td>
</tr>
<tr valign="top"> <!-- High Tmax OS -->
       <td class="text_gp9b">T<sub>max</sub> OS</td>
       <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateHighTmaxOs;?></td>
            </tr>
        </table>
        <!--Date-->
      </td>
    <td class="text_gp9"></td>
    <td class="text_gp9"><?php echo $elem_highTmaxOsOs;?></td>
</tr>

<tr valign="top"> <!-- High Ta OD -->
       <td class="text_gp9b">High T<sub>A</sub> OD</td>
       <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateHighTaOd;?></td>
            </tr>
        </table>
        <!--Date-->
      </td>
    <td class="text_gp9"><?php echo $elem_highTaOdOd;?></td>
    <td class="text_gp9"><?php //echo $elem_highTaOdOs;?></td>

</tr>
<tr valign="top"> <!-- High Ta OS -->
    <td class="text_gp9b">High T<sub>A</sub> OS</td>
    <td class="text_gp9b">
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateHighTaOs;?></td>
            </tr>
        </table>
        <!--Date-->
    </td>
    <td class="text_gp9"><?php //echo $elem_highTaOsOd;?></td>
    <td class="text_gp9"><?php echo $elem_highTaOsOs;?></td>
</tr>
<tr valign="top"> <!-- High Tp OD -->
    <td class="text_gp9b">High T<sub>P</sub> OD</td>
    <td class="text_gp9b">
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateHighTpOd;?></td>
            </tr>
        </table>
        <!--Date-->
    </td>
    <td class="text_gp9"><?php echo $elem_highTpOdOd;?></td>
    <td class="text_gp9"><?php //echo $elem_highTpOdOs;?></td>
</tr>
<tr valign="top"> <!-- High Tp Os -->
    <td class="text_gp9b">High T<sub>P</sub> OS</td>
    <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateHighTpOs;?></td>
            </tr>
        </table>
        <!--Date-->
    </td>
    <td class="text_gp9"><?php //echo $elem_highTpOsOd;?></td>
    <td class="text_gp9"><?php echo $elem_highTpOsOs;?></td>
</tr>
<tr valign="top"> <!-- C:D -->
    <td class="text_gp9b">C:D</td>
    <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateCd;?></td>
            </tr>
        </table>
        <!--Date-->
    </td>
    <td class="text_gp9"><?php echo $elem_cdOd;?></td>
    <td class="text_gp9"><?php echo $elem_cdOs;?></td>
</tr>
<tr valign="top"> <!-- Pachy -->
   <td class="text_gp9b">Pachy</td>
    <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_datePachy;?></td>
            </tr>
        </table>
        <!--Date-->
    </td>
    <td >
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_pachyOdReads;?></td>
                <td width="1"></td>
                <td class="text_gp9" id="td_pachy_od_avg" style="display:<?php echo $disAvgOd;?>"><?php echo "&nbsp;&nbsp;".$elem_pachyOdAvg."&nbsp;&nbsp;";?></td>
                <td width="1"></td>
                <td class="text_gp9"><?php echo $elem_pachyOdCorr;?></td>
            </tr>
        </table>
    </td>
    <td >
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_pachyOsReads;?></td>
                <td width="1"></td>
                <td class="text_gp9" id="td_pachy_os_avg" style="display:<?php echo $disAvgOs;?>"><?php echo "&nbsp;&nbsp;".$elem_pachyOsAvg."&nbsp;&nbsp;";?></td>
                <td width="1"></td>
                <td class="text_gp9"><?php echo $elem_pachyOsCorr;?></td>
            </tr>
        </table>
    </td>

</tr>
<tr valign="top"> <!-- Disk Photo -->
   <td class="text_gp9b">Disc Photo</td>
    <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateDiskPhoto;?></td>
            </tr>
        </table>
        <!--Date-->
    </td>
    <td class="text_gp9"><?php echo ($elem_diskPhotoOd);?></td>
    <td class="text_gp9"><?php echo ($elem_diskPhotoOs);?></td>
</tr>

<tr valign="top"> <!-- Gonio -->
    <td class="text_gp9b">Gonio</td>
    <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateGonio;?></td>
            </tr>
        </table>
        <!--Date-->
    </td>
    <td class="text_gp9"><?php echo ($elem_gonioOd);?></td>
    <td class="text_gp9"><?php echo ($elem_gonioOs);?></td>
</tr>
<tr valign="top"> <!-- VF -->
    <td class="text_gp9b">VF</td>
    <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateVf;?></td>
            </tr>
        </table>
        <!--Date-->
    </td>
    <td class="text_gp9"><?php echo ($elem_vfOdSummary);?></td>
    <td class="text_gp9"><?php echo ($elem_vfOsSummary);?></td>
</tr>
<tr valign="top"> <!-- NFA -->
    <td class="text_gp9b">NFA</td>
    <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateNfa;?></td>
            </tr>
        </table>
        <!--Date-->
    </td>
    <td class="text_gp9"><?php echo ($elem_nfaOdSummary);?></td>
    <td class="text_gp9"><?php echo ($elem_nfaOsSummary);?></td>
</tr>
<!-- CEE -->
<tr valign="top">
    <td class="text_gp9b">CEE</td>
    <td >
        <!--Date-->
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="text_gp9"><?php echo $elem_dateCee;?></td>
            </tr>
        </table>
        <!--Date-->
    </td>
    <td class="text_gp9" ><?php echo ($elem_cee);?></td>
    <td class="text_gp9" ><?php echo ($elem_ceeNotes);?></td>
</tr>
<!-- CEE -->

</table>
<!-- Past Reading -->
    </td>
    <td valign="top" height="10">
        <!-- Risk Factors -->
        <table cellpadding="0" cellspacing="0" border="0" height="40%">
            <tr>
                <td class="text_gp9b" height="10" >Risk Factors</td>
            </tr>
            <tr>
                <td class="text_gp9" valign="top" >
                <?php echo(strpos($elem_riskFactors,"Family History") !== false)? "&bull;Family History<br>" : "" ;
                      echo(strpos($elem_riskFactors,"Myopia") !== false)? "&bull;Myopia<br>" : "" ;
                      echo(strpos($elem_riskFactors,"African American") !== false)? "&bull;African American<br>" : "" ;
                      echo(strpos($elem_riskFactors,"Diabetes") !== false)? "&bull;Diabetes<br>" : "" ;
                      echo(strpos($elem_riskFactors,"Steroid Responder") !== false)? "&bull;SteroidResponder<br>" : "" ;
                ?>
                </td>
            </tr>
        </table>
        <!-- Risk Factors -->
        <!-- Warnings -->
        <table cellpadding="0" cellspacing="0" border="0" height="40%">
            <tr>
                <td class="text_gp9b" height="10" >Warnings</td>
            </tr>
            <tr>
                <td class="text_gp9" valign="top">
                <?php
                    echo(strpos($elem_warnings,"Arrhythmia") !== false)? "&bull;Arrhythmia<br>" : "" ;
                    echo(strpos($elem_warnings,"Asthma/COPD") !== false)? "&bull;Asthma/COPD<br>" : "" ;
                    echo(strpos($elem_warnings,"Bradycardia") !== false)? "&bull;Bradycardia<br>" : "" ;
                    echo(strpos($elem_warnings,"Sulfa Allergy") !== false)? "&bull;Sulfa Allergy<br>" : "" ;
                ?>
                </td>
            </tr>
        </table>
        <!-- Warnings -->
	<!--Targets IOP-->
	<?php
	if(!empty($targetOdTa) || !empty($targetOsTa) || !empty($targetOdTp) || !empty($targetOsTp))
	{
	?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" height="20%">
		<tr>
			<td class="text_9b" >IOP Targets</td>
		</tr>
		<?php
		if(!empty($targetOdTa) || !empty($targetOsTa))
		{
		?>
		<tr >
			<td class="text_9b">
				<!-- Ta -->
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr >
						<td class="text_9b" >TA</td>
						<td class="text_9b" ><font color="Blue">OD</font></td>
						<td class="text_9" ><?php echo $targetOdTa;?></td>
						<td class="text_9b" ><font color="Green">OS</font></td>
						<td class="text_9" ><?php echo $targetOsTa;?></td>
					</tr>
				</table>
				<!-- Ta -->
			</td>
		</tr>
		<?php
		}
		if(!empty($targetOdTp) || !empty($targetOsTp))
		{
		?>
		<tr >
			<td class="text_9b">
				<!-- Tp -->
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr >
						<td class="text_9b" >TP</td>
						<td class="text_9b" ><font color="Blue">OD</font></td>
						<td class="text_9" ><?php echo $targetOdTp;?></td>
						<td class="text_9b" ><font color="Green">OS</font></td>
						<td class="text_9" ><?php echo $targetOsTp;?></td>
					</tr>
				</table>
				<!-- Tp -->
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
	}
	?>
	<!--Targets IOP-->
    </td>
</tr>
</table>
<br>
<!-- Other Info -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td >
        <!-- Applets and Textarea -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td valign="top" width="80%">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr valign="top">
                            <td class="text_gp9b">Notes:&nbsp;</td>
                            <td class="text_gp9"><?php echo (!empty($elem_notes)) ? nl2br($elem_notes) : "";?></td>
                        </tr>
                    </table>
                </td>
                <td valign="top" width="20%">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                            <?php
                                //Applet Od
                                $apTable = 'glucoma_main';
                                $apIdName = 'glucomaId';
                                $apOd = 'cdAppOd';
                                $apOdImage = '../../../library/images/circle.gif';
                                $apAlt = 'OD';
                                echo $cpr->getAppletImage($elem_glucomaId,$apTable,$apIdName,$apOd,$apOdImage,$apAlt);
                            ?>
                            </td>
                            <td>
                            <?php
                                //Applet Os
                                $apTable = 'glucoma_main';
                                $apIdName = 'glucomaId';
                                $apOs = 'cdAppOs';
                                $apOsImage = '../../../library/images/circle.gif';
                                $apAlt = 'OS';
                                echo $cpr->getAppletImage($elem_glucomaId,$apTable,$apIdName,$apOs,$apOsImage,$apAlt);
                            ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- Applets and Textarea -->
    </td>
</tr>
</table>
<!-- Other Info -->
