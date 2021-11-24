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

require_once(dirname(__FILE__) . '/../../config/globals.php');
include_once($GLOBALS['fileroot'] . '/library/classes/class.cls_hold_document.php');
require_once($GLOBALS['fileroot'] . '/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'] . '/library/classes/functions.smart_tags.php');
require_once($GLOBALS['fileroot'] . '/library/ckeditor/ckeditor.php');

$OBJCommonFunction = new CLSCommonFunction;
$OBJhold_sign = new CLSHoldDocument;
$OBJsmart_tags = new SmartTags;

$case = isset($_REQUEST['case']) ? trim($_REQUEST['case']) : '';
$doc_id = isset($_REQUEST['doc_id']) ? intval($_REQUEST['doc_id']) : 0;
$patient_id = isset($_REQUEST['patient_id']) ? intval($_REQUEST['patient_id']) : 0;
$btnaction = isset($_POST['btnaction']) ? intval($_POST['btnaction']) : false;

if ($btnaction) { //save and close div.
    $fck_contents = isset($_POST['FCKeditor1']) ? $_POST['FCKeditor1'] : false;
    switch ($case) {
        case 'consult_letter':
            $rs1 = get_doc_contents($doc_id, 'consult');
            $id = $rs1['id'];
            $query = "UPDATE patient_consult_letter_tbl SET templateData = '" . addslashes($fck_contents) . "' WHERE patient_consult_id = " . $id;
            break;
        case 'opnote':
            $rs1 = get_doc_contents($doc_id, 'opnote');
            $id = $rs1['id'];
            $query = "UPDATE pn_reports SET txt_data = '" . addslashes($fck_contents) . "' WHERE pn_rep_id = " . $id;
            break;
        default:
            echo 'Invalid Request';
    }//end of switch.

    if ($fck_contents) {
        $result = imw_query($query);
        if ($result) {
            if ($btnaction == 3) {
                $OBJhold_sign->hold_sign_id = $rs1['hold_sign_id'];
                $OBJhold_sign->finalize_holded_doc();
            } else if ($btnaction == 4) {
                $OBJhold_sign->hold_sign_id = $rs1['hold_sign_id'];
                $OBJhold_sign->switch_holdto_physician($hold_sign_id);
            }
            $do = 'hideEditor';
        }
    }
} else {
    $section = '';
    switch ($case) {
        case 'consult_letter':
            $section = 'consult';
            $sectionTitle = 'Un-signed Consult Letter';
            $rs = get_doc_contents($doc_id, $section);
            break;
        case 'opnote':
            $section = 'opnote';
            $sectionTitle = 'Un-signed Operative Notes';
            $rs = get_doc_contents($doc_id, $section);
            break;
        default:
            echo 'Invalid Request';
    }//end of switch.
}

function get_doc_contents($doc_id, $section) {
    $query = '';
    switch ($section) {
        case 'consult':
            $query = "SELECT chs.id as hold_sign_id, chs.consult_id as id, pclt.templateData as templateData,pclt.patient_consult_id as main_letter_id FROM consent_hold_sign chs 
					JOIN patient_consult_letter_tbl pclt ON (pclt.patient_consult_id = chs.consult_id AND chs.consult_id != 0) 
					WHERE chs.id=" . $doc_id . " AND chs.signed=0";
            break;
        case 'opnote':
            $query = "SELECT chs.id as hold_sign_id, chs.opnote_id as id, pnr.txt_data as templateData,pnr.pn_rep_id as main_letter_id FROM consent_hold_sign chs 
					JOIN pn_reports pnr ON (pnr.pn_rep_id = chs.opnote_id AND chs.opnote_id != 0) 
					WHERE chs.id=" . $doc_id . " AND chs.signed=0";
            break;
        default:
            break;
    }
    if ($query != '') {
        $result = imw_query($query);
        if ($result && imw_num_rows($result) == 1) {
            $rs = imw_fetch_assoc($result);
            return $rs;
        }
    }
}

//end of function.

if (preg_match('/<div title="replacePhySig">/', $rs['templateData'])) {
    $phy_hold_sig = true;
} else {
    $phy_hold_sig = false;
}
$user_type = $_SESSION['logged_user_type'];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Editor :: <?php echo $sectionTitle; ?></title>
        <meta name="viewport" content="width=device-width, maximum-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <!--	<link rel="stylesheet" href="../themes/default/common.css" type="text/css">
            <link rel="stylesheet" href="../../library/messi/messi.css" type="text/css">
            <script type="text/javascript" src="../../js/jquery.js"></script>
            <script type="text/javascript" src="../../js/common.js"></script>
            <script type="text/javascript" src="../../library/messi/messi.js"></script>-->
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-select.css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/physician_console.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.min.1.12.4.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/bootstrap-select.js"></script>
        <script type="text/javascript">
            function SaveFEditor(action) {
                document.forms.frm_editor.btnaction.value = action;
                document.forms.frm_editor.submit();
            }

            function hideFEditor() {
                //window.opener.update_toolbar();
                window.close();
            }

            function hold_dr_sig() {
                scrol = $(window).scrollTop();
                $('#hold_to_phy_div').css('top', scrol + 310);
                $('#hold_to_phy_div').show();
                $(window).scroll(function () {
                    $('#hold_to_phy_div').css('top', $(window).scrollTop() + 310);
                });
                $('#hold_to_phy_div .hold').click(function () {
                    if ($('#hold_to_physician').val() == '') {
                        alert('Please select a physician');
                    } else {
                        $('#hidd_hold_to_physician').val($('#hold_to_physician').val());
                        document.forms.frm_editor.btnaction.value = 4;
                        document.forms.frm_editor.submit();
                        $('#hold_to_phy_div').hide();
                    }
                });
            }

            function display_tag_options() {
                $('#div_smart_tags_options').html('<div class="section_header"><span class="closeBtn" onClick="$(\'#div_smart_tags_options\').hide();"></span>Smart Tag Options</div><img src="../../images/ajax-loader.gif">');
                $('#div_smart_tags_options').show();
                var parentId = $('#smartTag_parentId').val();
                $.ajax({
                    type: "GET",
                    //url: "../admin/documents/smart_tags/ajax.php?do=getTagOptions&id=" + parentId,
                    url: "../chart_notes/requestHandler.php?elem_formAction=getTagOptions&id="+parentId,
                    success: function (resp) {
                        $('#div_smart_tags_options').html(resp);
                    }
                });
            }

            function replace_tag_with_options() {
                var strToReplace = '';
                var parentId = $('#smartTag_parentId').val();

                var arrSubTags = document.all.chkSmartTagOptions;
                $(arrSubTags).each(function () {
                    if ($(this).attr('checked')) {
                        if (strToReplace == '')
                            strToReplace += $(this).val();
                        else
                            strToReplace += ', ' + $(this).val();
                    }
                });
                if (strToReplace != '') {
                    $('#div_smart_tags_options').hide();
                } else {
                    alert('Select Options');
                }
                //alert(strToReplace);

                /*--GETTING FCK EDITOR TEXT--*/
                fram = 'FCKeditor1___Frame';
                FCKtext = CKEDITOR.instances['FCKeditor1'].getData();//SetData('aaa',true);//xEditingArea.frames[0].src;
                $('#hold_temp_smarttag_data').html(FCKtext);

                if (strToReplace != '') {
                    $('.cls_smart_tags_link[id="' + parentId + '"]').html(strToReplace);
                    //act_id used to replace consult letter smart tag data
                    $('.cls_smart_tags_link[act_id="' + parentId + '"]').html(strToReplace);
                    RemoveString = window.location.protocol + '//' + window.location.host; //.innerHTML BUG adds host url to relative urls.
                    var strippedData = $('#hold_temp_smarttag_data').html();
                    strippedData = strippedData.replace(new RegExp(RemoveString, 'g'), '');

                    CKEDITOR.instances['FCKeditor1'].setData(strippedData, function () {});
                    $('#div_smart_tags_options').hide();
                } else {
                    alert('Select Options');
                }
            }
        </script>
    </head>
    <body class="body_c">

        <div class="row">
            <div class="col-sm-12">

                <div class="row purple_bar">
                    <div class="col-sm-5"><h4 class="pdl_10"><?php echo $sectionTitle; ?></h4></div>
                    <div class="col-sm-7 text-right">
                        <?php
                        /*if (constant('AV_MODULE') == 'YES') {
                            ?>
                            <span>
                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/my_inbox.png" style="margin-left:20px;vertical-align:middle;cursor:pointer;" onClick="top.showRecordingControl('<?php echo $case; ?>', '<?php echo $rs['main_letter_id']; ?>', '<?php echo $_SESSION['patient']; ?>');" title="Record MultiMedia Message" />
                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/my_inbox.png" class="ml10" style="vertical-align:middle;cursor:pointer;" title="Play MultiMedia Messages"  border="0" onClick="top.showMultiMediaMessage('<?php echo $case; ?>', '<?php echo $rs['main_letter_id']; ?>');" />
                            </span>
                        <?php }*/ ?>
                    </div>
                </div>
                <!--                <div class="page_block_heading text12b">
                
                
                                </div>-->
                <?php if (isset($do) && $do == 'hideEditor') { ?>
                    <script type="text/javascript">
                        <?php if ($btnaction == 2) { ?>
                                                window.opener.$('.chk_box').attr('checked', false);
                                                window.opener.$('#chk_select_all').attr('checked', false);
                                                window.opener.$('#chk<?php echo $id; ?>').attr('checked', true);
                            <?php if ($case == 'consult_letter') { ?>
                                                    window.opener.frm_consult_sign.submit();
                            <?php } else if ($case == 'opnote') { ?>
                                                    window.opener.frm_opnote_sign.submit();
                                <?php
                            }
                        } else if ($btnaction == 3 || $btnaction == 4) {
                            if ($case == 'consult_letter') {
                                $getElem = $case . 's';
                            } else {
                                $getElem = 'op_notes';
                            }
                            ?>
                                                window.opener.do_action('forms_letters', window.opener.document.getElementById('<?php echo $getElem; ?>'));
                        <?php } ?>
                        hideFEditor();
                    </script>	
                <?php } else {
                    ?>
                    <form method="POST" name="frm_editor" id="frm_editor" onsubmit="return false;">
                        <input type="hidden" name="case" value="<?php echo $case; ?>">
                        <input type="hidden" name="doc_id" value="<?php echo $doc_id; ?>">
                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                        <input type="hidden" name="btnaction" value="">
                        <input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
                        <?php
                        if ($rs['templateData'] != '') {
                            /* --REPLACING SMART TAGS (IF FOUND) WITH LINKS-- */
                            $arr_smartTags = $OBJsmart_tags->get_smartTags_array();
                            if ($arr_smartTags) {
                                foreach ($arr_smartTags as $key => $val) {
                                    $rs['templateData'] = str_ireplace("[" . $val . "]", '<a id="' . $key . '" href="javascript:;" class="ckeditor_textarea">' . $val . '</a>', $rs['templateData']);
                                }
                            }

                            echo '<textarea id="FCKeditor1" name="FCKeditor1" class="ckeditor_textarea">' . stripslashes($rs["templateData"]) . '</textarea>';

                            $CKEditor = new CKEditor('FCKeditor1');
                            $CKEditor->basePath = $GLOBALS['webroot'] . '/library/ckeditor/';
                            $CKEditor->config['height'] = intval($_REQUEST['height'] - 150);
                            $CKEditor->config['width'] = intval($_REQUEST['width']);
                            // Create a textarea element and attach CKEditor to it.
                            $CKEditor->editor("FCKeditor1", stripslashes($rs["templateData"]));
                        }
                        ?>

                        <div id="hold_to_phy_div" class="modal common_modal_wrapper">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <button type="button" class="close" onClick="$('#hold_to_phy_div').hide();" data-dismiss="modal">x</button><h4 class="modal-title">Select Physician for Hold</h4>
                                    </div>
                                    <div class="modal-body text-center hold_div">
                                        <select name="hold_to_physician" id="hold_to_physician" class="selectpicker" data-size="8" style="width:200px;">
                                            <option value="">--SELECT--</option>
                                            <?php echo $OBJCommonFunction->dropDown_providers('', ''); ?>
                                        </select>
                                        <input type="hidden" name="hidd_hold_to_physician" id="hidd_hold_to_physician">
                                    </div>
                                    <div class="modal-footer" id="page_buttons">
                                        <button type="button" class="btn btn-success dff_button hold">Save &amp; Close</button>
                                        <button type="button" class="btn btn-danger dff_button cancel" data-dismiss="modal" onClick="$('#hold_to_phy_div').hide();">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-sm-12 text-center pt5 pdb5">
                            <div class="" id="page_buttons">
                                <button type="button" class="btn btn-success dff_button" onClick="SaveFEditor(1)">Update</button>
                                <span id="btn_upnsign" class="">
                                    <button type="button" class="btn btn-success dff_button" onClick="SaveFEditor(2)">Update &amp; Sign</button>
                                </span>
                                <span id="btn_finalize" class="">
                                    <button type="button" class="btn btn-success dff_button" onClick="SaveFEditor(3)">Finalize</button>
                                </span>
                                <button type="button" class="btn btn-success dff_button" onClick="hold_dr_sig()" data-toggle="modal" data-target="#hold_to_phy_div">Switch Hold</button> 
                                <button type="button" class="btn btn-danger dff_button" onClick="hideFEditor();">Cancel</button>
                            </div>
                        </div>
                    </div>

                    <div class="div_popup white border" id="div_smart_tags_options" style="display:none; top:200px;left:400px; width:300px; z-index:999;">
                        <div class="section_header"><span class="closeBtn" onClick="$('#div_smart_tags_options').hide();"></span>Smart Tag Options</div>
                        <img src="../../images/ajax-loader.gif">
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                <?php if ($phy_hold_sig && $user_type == 1) { ?>
                    $('#btn_upnsign').show();
                    $('#btn_finalize').hide();
                <?php } else { ?>
                    $('#btn_upnsign').hide();
                    $('#btn_finalize').show();
                <?php } ?>
                //--------BEGIN CONFIGURE CKEDITOR FOR SMART TAGS--------------
                var editor = CKEDITOR.instances['FCKeditor1'];
                
                editor.on('instanceReady', function (e) {
                  /*  editor.addCommand("showTags", {
                        exec: function (editor)
                        {
                            sel = editor.getSelection();
                            var node = editor.document.getBody().getFirst();
                            var parent = node.getParent();
                            sellink = CKEDITOR.plugins.link.getSelectedLink(editor);
                            //=======act_id USED TO REPLACE CONSULT LETTER SMART TAG DATA=======
                            if (sellink.getAttribute("act_id")) {
                                document.getElementById('smartTag_parentId').value = sellink.getAttribute("act_id");
                            } else {
                                document.getElementById('smartTag_parentId').value = sellink.getAttribute("id");
                            }
                            display_tag_options();
                        }
                    });
                    var showTags = {
                        label: "Show Tag Options",
                        command: 'showTags',
                        group: 'anchor'
                    };
                    editor.contextMenu.addListener(function (element, selection) {
                        return {
                            showTags: CKEDITOR.TRISTATE_OFF
                        };
                    });
                    editor.addMenuItems({
                        showTags: {
                            label: "Show Tag Options",
                            command: 'showTags',
                            group: 'anchor',
                            order: 1
                        }
                    });*/
                });
                //--------END CONFIGURE CKEDITOR FOR SMART TAGS--------------
            </script>
            <div id="hold_temp_smarttag_data" class="hide"></div>
        <?php } ?>
    </body>
</html>