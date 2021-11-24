<?php
$ignoreAuth = true;
set_time_limit(0);

include("../../../../config/globals.php");


//Iterate Frm Source deatination - R7
$scrPath = $webServerRootDirectoryName.$GLOBALS['r7_directory_name']."/interface/common/new_html2pdf/bar_code_images/";
pre($scrPath);die;

$dir = new DirectoryIterator($scrPath);
foreach ($dir as $fileinfo) {
    if ($fileinfo->isDir() && !$fileinfo->isDot()) {
        //Getting Folder name
        $folderName = $fileinfo->getFilename();

        //If is Directory
        if(is_dir($scrPath.$folderName)){
            $ptId = preg_replace("/[^0-9,.]/", "", $folderName);
            if(empty($ptId) == false){
                //If PtId is there, thn iterate further for files
                $directoryCsv = $scrPath.$folderName;
                
                //Destination Path R8
                $dstPath = data_path().'PatientId_'.$ptId.'/consent_forms/bar_code_images/';
                if (!file_exists($dstPath)) mkdir($dstPath, 0777, true);    //if no folder there, create one

                //If destination exists
                if (is_dir($directoryCsv)){
                    //Open current src patient directory
                    if ($dh = opendir($directoryCsv)) {
                        while (($file = readdir($dh)) !== false) {
                            if(is_file($directoryCsv.'/'.$file)){
                                pre('Copying in progress from'.$scrPath.$folderName.'/'.$file);
                                if(!file_exists($dstPath.$file)) copy($directoryCsv.'/'.$file, $dstPath.$file);
                            }
                        }
                        closedir($dh);
                    }
                }
            }
        }
    }
}


die('Loop Ended');

?>