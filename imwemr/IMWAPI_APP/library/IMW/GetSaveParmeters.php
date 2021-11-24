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
 * GetSaveParmeters - Return saving profile for the API calls
 */
namespace IMW;

use Exception;

class GetSaveParmeters
{
    /*Return Json Object for parameters accordign to the Parameter Name*/
    public function __get($name) {
	
	$file = dirname(__FILE__).DIRECTORY_SEPARATOR.'saveProfiles'.DIRECTORY_SEPARATOR.$name.'.json';
	
	if(file_exists($file) === false )
	{
	    throw new Exception("Saving profile is not configured.");
	}
	
	$data = file_get_contents($file);
	
	$object = json_decode($data);
	
	return $object;
    }
}