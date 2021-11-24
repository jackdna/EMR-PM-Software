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
require_once("../../../config/globals.php");
require_once('../admin_header.php');
//require_once("../../main/Functions.php");
$physicianId = $_REQUEST['pId'];

// DEFINE TYPES
$scriptMsg = '';
if ($_REQUEST['defineLenseTo']) {
    $iolIdChkBxArr = $_REQUEST['iolIdChkBx'];
    if (count($iolIdChkBxArr) > 0) {
        // DELETE PRESENT DEFINES
        $delDefinesStr = "DELETE FROM lensesdefined WHERE physician_id = '$physicianId'";
        $delDefinesQry = imw_query($delDefinesStr);
        // DELETE PRESENT DEFINES

        foreach ($iolIdChkBxArr as $iolTypeId) {
            $insertDefinedTypesStr = "INSERT INTO lensesdefined SET
									physician_id = '$physicianId',
									iol_type_id = '$iolTypeId'";
            $insertDefinedTypesQry = imw_query($insertDefinedTypesStr);
        }
    } else {
        // DELETE PRESENT DEFINES
        $delDefinesStr = "DELETE FROM lensesdefined WHERE physician_id = '$physicianId'";
        $delDefinesQry = imw_query($delDefinesStr);
        // DELETE PRESENT DEFINES
    }
    $scriptMsg .= '<script type="text/javascript">top.alert_notification_show("Saved Successfully."); var pId = ' . $physicianId . '; top.fmain.frames[0].frames[0].location.href = "providersDefined.php?pId="+pId;</script>';
}
// DEFINE TYPES
// GETTING LENSES DETAILS
$getLensesListStr = "SELECT * FROM lenses_iol_type ORDER BY lenses_category";
$getLensesListQry = imw_query($getLensesListStr);
while ($getLensesListRow = imw_fetch_array($getLensesListQry)) {
    $iol_type_id = $getLensesListRow['iol_type_id'];
    $lenses_iol_type = $getLensesListRow['lenses_iol_type'];
    $lensesArr[$iol_type_id] = $lenses_iol_type;
}

// GETTING LENSES DETAILS
function getCategory($typeIolId) {
    $getLensesCatStr = "SELECT * FROM lenses_iol_type
						WHERE iol_type_id = '$typeIolId'";
    $getLensesCatQry = imw_query($getLensesCatStr);
    $getLensesCatRow = imw_fetch_array($getLensesCatQry);
    return $getLensesCatRow['lenses_category'];
}

// GETTING DEFINED TYPES
$getDefinedStr = "SELECT * FROM lensesdefined 
					WHERE physician_id = '$physicianId'";
$getDefinedQry = imw_query($getDefinedStr);
while ($getDefinedRows = imw_fetch_array($getDefinedQry)) {
    $definedTypeToPhyArr[] = $getDefinedRows['iol_type_id'];
}

//Getting Physician Name
$gehysicianStr = "SELECT id,username FROM users WHERE user_type = '1' AND delete_status = '0' AND id='$physicianId' ";
$gehysicianQry = imw_query($gehysicianStr);
$getphysicianRow = imw_fetch_assoc($gehysicianQry);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>imwemr</title>
        <meta name="viewport" content="width=device-width, maximum-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <?php echo $scriptMsg; ?>
        <script type="text/javascript">

            //Btn --
            var ar = [["lense_users", "Save", "top.fmain.frm_submit();"]];
            top.btn_show("ADMN", ar);
            $(document).ready(function () {
                set_header_title('<?php echo ucwords($getphysicianRow['username']); ?>');
            });

            function chkStatus() {
                var flag = 0;
                var eleLength = document.lenseDefineFrm.elements.length;
                for (i = 0; i < eleLength; i++) {
                    var typeid = document.lenseDefineFrm.elements[i].id;
                    if (typeid.indexOf("typeId") != -1) {
                        if (document.lenseDefineFrm.elements[i].checked == true) {
                            var flag = flag + 1;
                        }
                    }
                }
                if (flag > 4) {
                    top.fAlert("Only four types can be selected.")
                    return false;
                }
            }

            function frm_submit() {
                lenseDefineFrm.submit();
            }
        </script>
    </head>
    <body class="body_c">
        <div class="whtbox">
            <form action="providersDefined.php" method="post" name="lenseDefineFrm" id="lenseDefineFrm">
                <input type="hidden" name="pId" id="pId" value="<?php echo $physicianId; ?>">
                <input type="hidden" name="defineLenseTo" id="defineLenseTo" value="yes">
<!--                <div class="text_10b">*Can Select any four Lense Types</div>-->
                <div style="height:<?php print ($_SESSION['wn_height'] - 320); ?>px; overflow:auto; overflow-x:hidden;">
                    <table class="table table-bordered adminnw tbl_fixed" width="100%">
                        <thead>
                            <tr class="section_header">
                                <th width="5%" class="text-center">Define</th>
                                <th>Lense Category</th>
                                <th>Iol Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (count($lensesArr) > 0) {
                                $i = 0;
                                foreach ($lensesArr as $iolId => $iolType) {
                                    $catType = getCategory($iolId);
                                    $bgcolor = (($i % 2) == 0) ? 'alt3' : '';
                                    ?>
                                    <tr class="<?php echo $bgcolor; ?>">
                                        <td class="text-center">
                                            <div class="checkbox">
                                                <input id="typeId<?php echo $iolId; ?>" type="checkbox" name="iolIdChkBx[]" <?php
                                                if (count($definedTypeToPhyArr) > 0) {
                                                    if (in_array($iolId, $definedTypeToPhyArr))
                                                        echo "CHECKED";
                                                }
                                                ?> class="input_text_10" value="<?php echo $iolId; ?>" onClick="return chkStatus();">
                                                <label for="typeId<?php echo $iolId; ?>">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td><?php echo $catType; ?></td>
                                        <td><?php echo $iolType; ?></td>				
                                    </tr>
                                    <?php
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        <script type="text/javascript">
            parent.parent.show_loading_image('none');
        </script>
    </body>
</html>