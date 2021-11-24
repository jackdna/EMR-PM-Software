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

?><script type="text/javascript">
	window.focus();
</script>
<?php
	$cur = curl_init();
	$iolinkUrl 			= $_REQUEST['iolinkUrl'];
	$iolinkUrlUsername 	= $_REQUEST['iolinkUrlUsername'];
	$iolinkUrlPassword 	= $_REQUEST['iolinkUrlPassword'];
	//$url = $iolinkUrl."?userName=$iolinkUrlUsername&password=$iolinkUrlPassword&downloadForm=";
	$url = $iolinkUrl;
	$postArr=array();
	$postArr['userName']		= $iolinkUrlUsername;
	$postArr['password']		= $iolinkUrlPassword;
	$postArr['downloadForm']	= "";
	curl_setopt($cur,CURLOPT_URL,$url);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($cur, CURLOPT_POSTFIELDS,$postArr); 
	$data = curl_exec($cur);
	if($data=='1'){
		echo "Connection established";
	}
	else{
		echo "Connection could not be established";	
		if (curl_errno($cur)){
			echo  "<br>Curl Error iDOC to iOLink: " . curl_error($cur). " ";
		}	
	}
	curl_close($cur);
?>