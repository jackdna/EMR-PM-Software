<?php
include_once("../../config/globals.php");

?>
<!-- blueimp Gallery styles -->
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/upload/css/blueimp-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/upload/css/jquery.fileupload.css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/upload/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/upload/css/jquery.fileupload-noscript.css"></noscript>
<noscript><link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/upload/css/jquery.fileupload-ui-noscript.css"></noscript>

<div class="row">
		<!-- The file upload form used as target for the file upload widget -->
  	<form id="fileupload" action="" method="POST" >

      <input type="hidden" name="imwemr" value="<?php echo session_id();?>" />
      <input type="hidden" name="formName" value="<?php echo $formName;?>" />
    	<input type="hidden" name="form_id" value="<?php echo $form_id;?>" />
     	<input type="hidden" name="testId" value="<?php echo $testId;?>" />
      <input type="hidden" name="folder_id" value="<?php echo $folder_id;?>" />
      <input type="hidden" name="upload_from[]" value="<?php echo $upload_from;?>" />


      <!--HIDDEN FIELD FOR PDF SPLITTER-->
      <input type="hidden" name="hiddPdfSplit" value="yes" />
      <input type="hidden" name="facId" value="<?php echo $facId;?>" />
      <input type="hidden" name="opt_frame_id" value="<?php echo $opt_frame_id;?>" />


      	<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="col-xs-7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start upload</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel upload</span>
                </button>

                <!-- The global file processing state -->
                <span class="fileupload-process"></span>
            </div>
            <!-- The global progress state -->
            <div class="col-xs-5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <!-- The extended global progress state -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>



        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>


    </form>
    <div class="clearfix"></div>

</div>

<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name.substr(0,30)%}....</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
				<?php if(strstr($upload_url,"upload_test.php") && isset($_REQUEST['testId'])){?>
					<td>
						<select name="site[]" required>
						<option value="0">-Site-</option>
						<option value="1">OS</option>
						<option value="2">OD</option>
						<option value="3">OU</option>
						</select>
					</td>
				<?php  }?>
    </tr>
{% } %}
</script>


<?php
	if($upload_from=='pdfsplitter')
	{
?>
		<script id="template-download" type="text/x-tmpl">
			{% for (var i=0, file; file=o.files[i]; i++) { %}
					<tr class="template-download fade">
                        <td class="preview">{% if (file.thumbnail_url) { %}
                            <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
                        {% } %}</td>
                        <td class="name">{%=file.name%}
                        {% if (file.error) { %}
                            <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                        {% } %}
                        </td>
                        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
                        <td colspan="2"></td>
                    </tr>
			{% } %}
		</script>
<?php
	}
	else
	{
?>
		<!-- The template to display files available for download -->
		<script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade">
            <td>
                <span class="preview">
                    {% if (file.thumbnailUrl) { %}
                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                    {% } %}
                </span>
            </td>
            <td>
                <p class="name">
                    {% if (file.url) { %}
                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                    {% } else { %}
                        <span>{%=file.name%}</span>
                    {% } %}
                </p>
                {% if (file.error) { %}
                    <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                {% } %}
            </td>
            <td>
                <span class="size">{%=o.formatFileSize(file.size)%}</span>
            </td>
        </tr>
    {% } %}
    </script>
<?php
	}
?>

<?php if(!isset($zStopJqueryInc)||empty($zStopJqueryInc)){ ?>
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<?php } ?>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/canvas-to-blob.min.js"></script>
<!-- blueimp Gallery script -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="<?php echo $GLOBALS['webroot'];?>/library/upload/js/cors/jquery.xdr-transport.js"></script>
<![endif]-->
<script>
	<?php if(strstr($upload_url,"upload_test.php") && isset($_REQUEST['testId'])){?>
	$('#fileupload').bind('fileuploadsubmit', function (e, data) {
			var inputs = data.context.find(':input');
			if (inputs.filter(function () {
							return !this.value && $(this).prop('required');
					}).first().focus().length) {
					data.context.find('button').prop('disabled', false);
					return false;
			}
			data.formData = inputs.serializeArray();
	});
	<?php }?>
</script>
<!--
</body>
</html>
-->
