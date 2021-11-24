<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php include_once("../../common/conDb.php");
$upload_url 			= $_REQUEST['upload_url'];
$upload_from 			= $_REQUEST['upload_from']; 
$method 				= $_REQUEST['method'];
$pconfirmId 			= $_REQUEST['pconfirmId'];
$patient_id 			= $_REQUEST['patient_id'];
$ptStubId 				= $_REQUEST['ptStubId'];
$patient_in_waiting_id 	= $_REQUEST['patient_in_waiting_id'];
$formName 				= $_REQUEST['formName'];
$folderId 				= $_REQUEST['folderId'];
$scanIOL 				= $_REQUEST['scanIOL'];
$IOLScan 				= $_REQUEST['IOLScan'];
$scanDISCHARGE 			= $_REQUEST['scanDISCHARGE'];
$DISCHARGEScan 			= $_REQUEST['DISCHARGEScan'];

$scanPtInfo 			= $_REQUEST['scanPtInfo'];
$scanClinical 			= $_REQUEST['scanClinical'];
$scanIOLFolder 			= $_REQUEST['scanIOLFolder'];
$scanHP 				= $_REQUEST['scanHP'];
$scanEKG 				= $_REQUEST['scanEKG'];
$scanHealthQuest 		= $_REQUEST['scanHealthQuest'];
$scanOcularHx 			= $_REQUEST['scanOcularHx'];
$scanAnesthesiaConsent 	= $_REQUEST['scanAnesthesiaConsent'];
$scaniOLinkConsentId	= $_REQUEST['scaniOLinkConsentId'];
$CONSENTScan 			= $_REQUEST['CONSENTScan'];
$admin 					= $_REQUEST['admin'];
$INSURANCEScan 			= $_REQUEST['INSURANCEScan'];
$insuranceType 			= $_REQUEST['insuranceType'];
?>
<!-- jQuery UI styles -->
<link rel="stylesheet" href="css/jquery-ui.css" id="theme">
<!-- jQuery Image Gallery styles -->
<link rel="stylesheet" href="css/jquery.image-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the jQuery UI progress bars -->
<link rel="stylesheet" href="css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="css/jquery.fileupload-ui-noscript.css"></noscript>
<!-- Generic page styles -->
<link rel="stylesheet" href="css/style.css">
<!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<script> 
upload_url = "../<?php echo urldecode($upload_url);?>";
//upload_from = "<?php echo $upload_from;?>";
</script>
<?php //$formName= 'CellCount';$testId=96;?>
<style>
/*.start,.cancel{
	display:none;
}*/
.delete{
	display:none;
}
</style>
<div class="container"><br>
    <!-- The file upload form used as target for the file upload widget -->
    <form id="fileupload" action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="imwemr" value="<?php echo session_id();?>" />
	  <input type="hidden" name="upload_from[]" value="<?php echo $upload_from;?>" />
      
      <!--HIDDEN FIELD FOR PDF SPLITTER-->
      <input type="hidden" name="method" value="<?php echo $method;?>" />
      <input type="hidden" name="pconfirmId" id="pconfirmId" value="<?php echo $pconfirmId;?>" />
      <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id;?>" />
      <input type="hidden" name="ptStubId" id="ptStubId" value="<?php echo $ptStubId;?>" />
      <input type="hidden" name="patient_in_waiting_id" id="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id;?>" />
      <input type="hidden" name="formName" id="formName" value="<?php echo $formName;?>" />
      <input type="hidden" name="folderId" id="folderId" value="<?php echo $folderId;?>" />
      <input type="hidden" name="scanIOL" id="scanIOL" value="<?php echo $scanIOL;?>" />
      <input type="hidden" name="IOLScan" id="IOLScan" value="<?php echo $IOLScan;?>" />
      <input type="hidden" name="scanDISCHARGE" id="scanDISCHARGE" value="<?php echo $scanDISCHARGE;?>" />
      <input type="hidden" name="DISCHARGEScan" id="DISCHARGEScan" value="<?php echo $DISCHARGEScan;?>" />
      <input type="hidden" name="scanPtInfo" value="<?php echo $scanPtInfo;?>" />
      <input type="hidden" name="scanClinical" id="scanClinical" value="<?php echo $scanClinical;?>" />
      <input type="hidden" name="scanIOLFolder" id="scanIOLFolder" value="<?php echo $scanIOLFolder;?>" />
      <input type="hidden" name="scanHP" id="scanHP" value="<?php echo $scanHP;?>" />
      <input type="hidden" name="scanEKG" id="scanEKG" value="<?php echo $scanEKG;?>" />
      <input type="hidden" name="scanHealthQuest" id="scanHealthQuest" value="<?php echo $scanHealthQuest;?>" />
      <input type="hidden" name="scanOcularHx" id="scanOcularHx" value="<?php echo $scanOcularHx;?>" />
      <input type="hidden" name="scanAnesthesiaConsent" id="scanAnesthesiaConsent" value="<?php echo $scanAnesthesiaConsent;?>" />
      <input type="hidden" name="scaniOLinkConsentId" id="scaniOLinkConsentId" value="<?php echo $scaniOLinkConsentId;?>" />
      <input type="hidden" name="CONSENTScan" id="CONSENTScan" value="<?php echo $CONSENTScan;?>" />
      <input type="hidden" name="admin" id="admin" value="<?php echo $admin;?>" />
      <input type="hidden" name="INSURANCEScan" id="INSURANCEScan" value="<?php echo $INSURANCEScan;?>" />
      <input type="hidden" name="insuranceType" id="insuranceType" value="<?php echo $insuranceType;?>" />
      <input type="hidden" name="hiddPdfSplit" value="yes" />
<!--    
    <input type="hidden" name="formName" value="<?php echo $formName;?>" />
    <input type="hidden" name="form_id" value="<?php echo $form_id;?>" />
    <input type="hidden" name="testId" value="<?php echo $testId;?>" />
    <input type="hidden" name="folder_id" value="<?php echo $folder_id;?>" />
-->      

        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="span7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Start upload</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Cancel upload</span>
                </button>
                <!--<button type="button" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" class="toggle">-->
            </div>
            <!-- The global progress information -->
            <div class="span5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="bar" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>
        <br>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
        
    </form>
    <br>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>Start</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>Cancel</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<?php

if($upload_from=='pdfsplitter' || $upload_from=='patient_scan_docs' || $upload_from=='user_scan_docs') {

?>

<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
    	{% file.error=''; %}
		<td class="preview">{% if (file.thumbnail_url) { %}
			<a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
		{% } %}</td>
		<td class="name">{%=file.name%}</td>
		<td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
		<td colspan="2"></td>
	</tr>
{% } %}
</script>
<?php	
}else {
?>

<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        /*<td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
            <input type="checkbox" name="delete" value="1">
        </td>*/
    </tr>
{% } %}
</script>
<?php
}
?>

<script src="../../js/jquery-1.9.0.min.js"></script>
<script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>-->
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="js/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="js/canvas-to-blob.min.js"></script>
<!-- jQuery Image Gallery -->
<script src="js/jquery.image-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="js/jquery.fileupload.js"></script>
<!-- The File Upload file processing plugin -->
<script src="js/jquery.fileupload-fp.js"></script>
<!-- The File Upload user interface plugin -->
<script src="js/jquery.fileupload-ui.js"></script>
<!-- The File Upload jQuery UI plugin -->
<script src="js/jquery.fileupload-jui.js"></script>
<!-- The main application script -->
<script src="js/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="js/cors/jquery.xdr-transport.js"></script><![endif]-->
