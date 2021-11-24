<?php
include("../../../../config/globals.php");
set_time_limit(0);

$sections_arr = array();
// All Editors Images. Consult Letter, (redactor/images/)
/*
  hippa_setting
  collection_letter_templates
  consent_form
  consulttemplate
  document
  document
  pn_template
  recalltemplate
  pt_docs_template
  statement_template
  document_panels
  prescription_template
  surgery_center_consent_form_template
  order_template
  iportal_autoresponder_templates
  patient_consult_letter_tbl
 */
//
//$sections_arr['editor'] = 'Editor Images';
//
////Group             (hippa_setting)
////$sections_arr['groups'] = 'Groups Images';
////collection_letter_templates           (collection_letter_template)
////$sections_arr['cl_templates'] = 'Collection Letter Template';
////Facility
//$sections_arr['facility'] = 'Facility Images';
//
//providers
$sections_arr['providers'] = 'Providers Images';
//
//Documents=>Logos
$sections_arr['doc_logos'] = 'Doc Logos Images';
//
////Chart Notes => Drawings
//$sections_arr['chart_drawicon'] = 'Chart Drawicon';
//
////Site Care Plan
//$sections_arr['alert_tbl'] = 'Site Care Plan';
//
////iPortal => Preferred Images
//$sections_arr['preferred_images'] = 'iPortal Preferred Images';
//
////Demographic               (patient_data)
//$sections_arr['demographic'] = 'Demographic Images';
//
//Responsible party         (resp_party)
$sections_arr['resp_party'] = 'Resp Party Images';
//
////Insurance                 (insurance_scan_documents)
//$sections_arr['ins_scan_docs'] = 'Insurance Scan Documents';
//
////Surgery Consent           (surgery_center_patient_scan_docs)
//$sections_arr['sc_scan_docs'] = 'Surgery Center Patient Scan Documents';
//
//Surgery Consent           (surgery_consent_filled_form)
$sections_arr['surgery_consent_filled_form'] = 'Surgery Consent Filled Form';
//
//Surgery Consent           (surgery_consent_form_signature)
$sections_arr['surgery_consent_form_signature'] = 'Surgery Consent Form Signature';
//
//Surgery Consent           (surgery_center_pre_op_health_ques)
$sections_arr['surgery_center_pre_op_health_ques'] = 'Surgery Center Health Ques';
//
//Consult Letter
$sections_arr['patient_consult_letter_tbl'] = 'Patient Consult Letter';
//
////Medical HX General Health tab         (scans)
//$sections_arr['mh_scans'] = 'Medical HX Scans';
//
////Work View, Tests         (scans)
////Scan/Upload Icon
////All Tests
////EOM Exam
////External Exam
////L&A
////IOP/Gonio
////SLE
////Fundus
//
////chart_signatures
//$sections_arr['chart_signatures'] = 'Chart Signatures';
//
////idoc_drawing
//$sections_arr['idoc_drawing'] = 'iDoc-Drawing';
//
//$sections_arr['tests'] = 'Tests';
//
////Work View   AR Image      (chart_ar_scan)
//$sections_arr['ar_scan'] = 'AR Images';
//
////Icon bar       Pt-doc -Scan doc tab //Work View   scan_doc_tbl
//$sections_arr['scan_doc_tbl'] = 'Scan Doc Table';
//
//patient_consent_form_information
$sections_arr['pt_consent_form_info'] = 'Patient Consent Form Information';
//
//consent_form_signature
$sections_arr['consent_form_signature'] = 'Consent Form Signature';
//
//previous_statement
$sections_arr['previous_statement'] = 'Previous Statement';
//
//pt_docs_template
$sections_arr['pt_docs_template'] = 'Patient Document Template';
//
//pt_docs_patient_templates
$sections_arr['pt_docs_patient_templates'] = 'Pt Document Patient Template';
//
////document_patient_rel
//$sections_arr['document_patient_rel'] = 'Document Patient Rel';
//
//consent_form
$sections_arr['consent_form'] = 'Consent Form Templates';
//
//consulttemplate
$sections_arr['consulttemplate'] = 'Consult Templates';
//
//pn_reports
$sections_arr['pn_reports'] = 'Operative Notes';

// Batch processing  upload_lab_rad_data table
$sections_arr['upload_lab_rad_data'] = 'Upload Lab Rad Data/Batch processing';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Copy R6 Images</title>

        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/core.css" rel="stylesheet">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet"> 

        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>

        <script>
            var sections_arr = '<?php echo json_encode($sections_arr); ?>';
        </script>

    </head>
    <body>

        <div class="container-fluid">
            <!--            <h2>Click on link to copy images from R6 for particular section</h2>-->
            <div class="whtbox">
                <?php foreach ($sections_arr as $key => $value) { ?>
                    <div class="row m10 pdl_10 pd5">
                        <div class="col-sm-3">
                            <span class="copy_images"><a href="javascript:viod(0);" data-key="<?php echo $key; ?>"><?php echo $value; ?></a></span>
                        </div>
                        <div class="col-sm-9"><span class="result_<?php echo $key; ?>">&nbsp;</span></div>
                    </div>
                <?php } ?>
                <div class="row m10 pdl_10 pd5">
<!--                    <button class="btn btn-success copy_all">Copy All</button>-->
                </div>
                <div class="row m10 pdl_10 pd5">
<!--                    <button class="btn btn-success  update_paths" data-key="update_paths_in_editor">Update paths for editor</button>-->
                    <span class="result_update_paths_in_editor">&nbsp;</span>
                </div>
            </div>
        </div>

        <!--HTML Loader-->
        <div id="div_loading_image" class="text-center" style="z-index: 1051;">
            <div class="loading_container">
                <div class="process_loader"></div>
                <div id="process_loader"></div>
                <div id="div_loading_text" class="text-info">Please wait...</div>
            </div>
        </div>


        <script>
            function updateImages(key) {
                $("#div_loading_image").show();
                $('.result_' + key).html('');
                $.ajax({
                    url: 'copy_images.php?fun=' + key,
                    type: 'POST',
                    //async: false,
                    beforeSend: function () {
                        $("#div_loading_image").show();
                    },
                    success: function (result)
                    {
                        $('.result_' + key).html(result);
                    },
                    complete: function () {
                        $("#div_loading_image").hide();
                    }

                });
            }

            jQuery(document).ready(function ($) {
                $("#div_loading_image").hide();
                $('body').on('click', '.copy_all', function () {
                    $.each(JSON.parse(sections_arr), function (key, value) {
                        updateImages(key);
                    });
                });

                $('body').on('click', '.copy_images a', function (e) {
                    e.preventDefault();
                    var key = $(this).data('key');

                    updateImages(key);
                });

                $('body').on('click', '.update_paths', function (e) {
                    e.preventDefault();
                    var key = $(this).data('key');

                    updateImages(key);
                });

                top.$('#acc_page_name').html('Copy R6 Images');
            });
        </script>

    </body>
</html>


