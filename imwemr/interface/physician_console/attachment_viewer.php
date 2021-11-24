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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'] . '/library/classes/msgConsole.php');
//$msgConsoleObj = new msgConsole();

$dir_path				= $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
$curr_user_directory 	= '/users';

$time = date('mdyHis');
$valid_extensions = array('pdf','html','htm','txt','xml','xsl','zip','jpg','gif','png');

$full_path 	= $dir_path.$curr_user_directory.trim($_GET['full_path']); //full path of attachment file.
$web_path_root = '/'.constant('PRACTICE_PATH')."/data/".constant('PRACTICE_PATH').$curr_user_directory;
$web_path	= $web_path_root.trim($_GET['full_path']);

$a_id		= trim($_GET['a_id']); //attachemnt id.
$m_id		= trim($_GET['m_id']); //mail id.
$h			= trim($_GET['h']); //height of parent modal.
$sec = trim($_GET['sec']);
$dbtbl = $sec=='pt_msg' ? "patient_messages_attachment" : "direct_messages_attachment";

/****Getting attachemtn resultset **********/
$a_res = imw_query("SELECT * FROM ".$dbtbl." WHERE id='$a_id' LIMIT 0,1");
if($a_res && imw_num_rows($a_res)>0){$a_rs = imw_fetch_assoc($a_res);}


$valid_flag = false;
if(file_exists($full_path) && is_file($full_path)){
	$extension = pathinfo($full_path, PATHINFO_EXTENSION);	
	if(in_array(strtolower($extension),$valid_extensions) && $a_rs){
		$valid_flag = true;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>:: imwemr ::</title>
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/physician_console.css" rel="stylesheet" type="text/css">

    <!--[if lt IE 9]>
        <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/html5shiv.min.js"></script>
        <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/respond.min.js"></script>
    <![endif]-->
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>

        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/console.js?<?php echo filemtime('../../library/js/console.js');?>"></script>
<style type="text/css">
body{background-color:#FFF;}
#div_file_contents{height:<?php echo intval($h)-25;?>px; border:1px solid #efefef;}
iframe#div_file_contents{width:99%;}
#left_zip_tree{overflow:auto;}
a{color:#00C;}
a:active{font-weight:bold !important; text-decoration:underline !;}
p{margin:2px 0px; white-space:nowrap;}
</style>
</head>
<body>
<div class="bg-info"><b>ATTACHMENT FILE NAME:</b> <?php echo $a_rs['file_name'];?></div>
<?php if($valid_flag){?>
	<?php
    switch(strtolower($extension)){
        case 'txt':
        case 'html':
        case 'htm':{
            echo '<div id="div_file_contents">'.file_get_contents($full_path).'</div>';
            break;
        }
		case 'pdf':
		case 'jpg':
		case 'png':
		case 'gif':{
		    echo '<iframe id="div_file_contents" src="'.$web_path.'" frameborder=0></iframe>';
            break;
		}
        case 'xml':{
            //PROGRAM WILL NOT COME IN THIS CASE. XML FILE CONTROL REDIRECTS TO ccda VIEWER BEFORE REACHING THIS POINT.
            break;
        }
        case 'zip':{
            $zip = new ZipArchive;
			if($zip->open($full_path) == TRUE){
				// get the absolute path to $file
				$abspath 	 = $dir_path.$curr_user_directory.'/UserId_'.$_SESSION['authId'].'/mails/tempunzipped';
				$abspath_WEB = $web_path_root.'/UserId_'.$_SESSION['authId'].'/mails/tempunzipped';
				if(is_dir($abspath) == false){mkdir($abspath, 0755, true);}
				MakeDirectoryEmpty($abspath);
				$zip->extractTo($abspath);
				$zip->close();
				$zip_files = getDirContents($abspath);
			?>
				<div class="row">
                	<div class="col-sm-2">
                    	<big><div class="mt10" id="left_zip_tree">
							<?php
                            $zip->open($full_path);
							$xdm_ccda_xml_file = 'about:blank';
                            for($i=0; $i<$zip->numFiles; $i++){
                                $entry = $zip->getNameIndex($i); //name of current index in zip file.
                                $slash_count = substr_count($entry,'/'); //counting slashes.
                                    $entry_arr = preg_split('@/@', $entry, NULL, PREG_SPLIT_NO_EMPTY);
                                $lastDirFile = end($entry_arr); //last element of zip current index name.
                                if($slash_count>1)
                                    $indent_val = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',count($entry_arr));

                                if (substr($entry, -1)=='/'){ //DIRECTORY.
                                    echo '<p><b>'.$indent_val.'<span class="glyphicon glyphicon-folder-open"></span>&nbsp; '.$lastDirFile.'</b></p>';
                                }else{//FILE
									$file_extension = pathinfo($abspath.DIRECTORY_SEPARATOR.$entry, PATHINFO_EXTENSION);
                                    if(strtolower($file_extension)=='xml'){
										echo '<p>'.$indent_val.'<span class="glyphicon glyphicon-file"></span>&nbsp; <a href="cda_viewer.php?ccda_file='.$entry.'&source=tempunzipped&check_xsl=check_xsl" target="attachment_details">'.$lastDirFile.'</a></p>';
										$xml = simplexml_load_string(file_get_contents($abspath.DIRECTORY_SEPARATOR.$entry));
//										if(isset($xml->templateId['root'][0]) && $xml->templateId['root'][0]=='2.16.840.1.113883.10.20.22.1.1'){
										if(isset($xml->templateId['root'][0]) && in_array($xml->templateId['root'][0],array('2.16.840.1.113883.10.20.22.1.1','1.2.840.114350.1.72.1.51693'))){
											$xdm_ccda_xml_file = 'cda_viewer.php?ccda_file='.$entry.'&source=tempunzipped&check_xsl=check_xsl';
										}
									}else{
										echo '<p>'.$indent_val.'<span class="glyphicon glyphicon-file"></span>&nbsp; <a href="'.$abspath_WEB.DIRECTORY_SEPARATOR.$entry.'" target="attachment_details">'.$lastDirFile.'</a></p>';
									}
                                }
                                $indent_val = '';
                            }?>
						</div></big>
					</div>
                    <div class="col-sm-10 leftborder">
                    	<iframe frameborder="0" name="attachment_details" id="attachment_details" src="<?php echo $xdm_ccda_xml_file;?>" width="100%" height="<?php echo intval($h)-40;?>px"></iframe>
                    </div>
				</div>
           <?php
			}else{
				echo '<div class="alert alert-warning">Unable to open zip archive.</div>';
			}
            break;
        }
    }
}else{?>
	<div class="alert alert-warning">Invalid file type.</div>
<?php }?>
</div>
</body>
</html>
