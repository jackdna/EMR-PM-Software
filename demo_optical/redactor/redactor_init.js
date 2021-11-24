
$(function()
{
    $('#content').redactor({
        focus: true,
        buttonSource: true,
        imageUpload: '<?php echo $GLOBALS['webroot']; ?>/redactor/upload.php',
        plugins: ['table','fontsize','fontcolor','imagemanager','fullscreen'],
        minHeight: 500, 
        maxHeight: 500,

    });
});
