<!DOCTYPE html>
<html>
    <head>
        <title>Redactor</title>
        <meta charset="utf-8">

        <script type="text/javascript" src="../js/jquery-2.1.1.min.js"></script>

        <!-- Redactor is here -->
	    <link rel="stylesheet" href="redactor.css" />
	    <script src="redactor.min.js"></script>

	    <!-- Plugins -->
	    <script src="plugins/imagemanager.js"></script>
	    <script src="plugins/table.js"></script>
	    <script src="plugins/fontsize.js"></script>
	    <!--<script src="plugins/fontfamily.js"></script>-->
	    <script src="plugins/fullscreen.js"></script>
	    <script src="plugins/fontcolor.js"></script>

        <script type="text/javascript">
        $(function()
        {
            $('#content').redactor({
	            focus: true,
	            imageUpload: 'image_upload.php',
	            linebreaks: true,
	            plugins: ['table','imagemanager','fontsize','fontfamily','fontcolor','fullscreen'],
            });
        });
        </script>
    </head>
 

    <body style="margin: 0;">
    	<div style="width: 70%; margin: auto; padding: 50px;">
            <textarea id="content" name="content">
            <h2>Hello and Welcome</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </textarea>
    	</div>
    </body>
</html>


<?php
 print_r($_FILES['file']);

?>