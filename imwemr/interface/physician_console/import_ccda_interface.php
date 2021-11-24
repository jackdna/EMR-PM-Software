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

?>
<div style="margin:10px;">
	<form action="receive_uploaded_ccd.php" target="iframe_ccd_upload" name="import_CCD" method="post" enctype="multipart/form-data" onsubmit="return checkCCDUpload()">
    <div class="importtop"><div class="row">
        <div class="col-sm-5 form-group form-inline ">
            <div class="slectbrow"><label>Select a file to upload</label>
            <input type="file" class="form-control" name="ccdFile" id="ccdFile" onchange="$('#iframe_ccd_upload').prop('src','about:blank');" /> </div>          
        </div>
       
        <div class="col-sm-5 form-group form-inline">
           <div class="encrypted"><label class="encrpchk"><input type="checkbox" class="form-control" name="cbkEncrip" id="cbkEncrip" onclick="EnableDisable(this,'txtENCKey');"  /> Encrypted</label>
           <label>Encryption Key</label>
            <input type="text" id="txtENCKey" name="txtENCKey" maxlength="16" disabled class="form-control" /></div>
            
        </div>
        <div class="col-sm-2  form-group ">
        	<span class="uplbutpos"><input type="submit" class="btn btn-success" value="UPLOAD" /></span>
        </div>
    </div></div>
    </form>
    
    <div class="row">
    	<div id="div_after_ccd_upload" class="col-sm-12">
        	<iframe name="iframe_ccd_upload" id="iframe_ccd_upload" src="about:blank" style="width:99%; height:520px;" frameborder="0"></iframe>

        </div>
    </div>
    
    
</div>
