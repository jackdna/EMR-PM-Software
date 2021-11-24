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

/*
 *File: hl7Base.php
 *Puppose: Base Class for HL7 parser and generator.
 *Access Type: Include
 */

require_once(dirname(__FILE__)."/../../config/globals.php");

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

class hl7Base
{
	/*HL7 structural Elements*/
	protected $fieldSeparator;
	protected $componentSeparator;
	protected $repeatFieldSeperator;
	protected $subcomponentSeparator;
	protected $escapeCharacter;
	public $hl7Version;
	protected $fieldSeparators;

	/*Container to hold parsed hl7 message*/
	public $message;

	/*Container to hlod base file path*/
	private $filePath;

	/*Container to hold segment data*/
	public $segmenData;

	/*Modified Sgments */
	private $modifications = array();

	public function __construct()
	{
		$this->fieldSeparator  = "|";
		$this->componentSeparator  = "^";
		$this->repeatFieldSeperator = "~";
		$this->subcomponentSeparator = "&";
		$this->escapeCharacter  = "\\";

		$this->hl7Version  = "2.4";

		$this->message = array();

		/*Base Path of the File*/
		$this->filePath = dirname(__FILE__);
		$this->filePath = realpath( $this->filePath );

		$this->init();

		$this->fieldSeparators = array($this->subcomponentSeparator, $this->componentSeparator, $this->repeatFieldSeperator, $this->fieldSeparator);
	}

	/*Function to be used by child classed for their constructor elements*/
	public function init()
	{}

	/*
	 * Reset HL7 message parsing/generation
	*/
	public function reset()
	{
		$this->message = array();
		$this->fieldSeparators = array($this->subcomponentSeparator, $this->componentSeparator, $this->repeatFieldSeperator, $this->fieldSeparator);
	}

	/*
	 * This function will provide Array of Hl7 segment Structure queried
	*/
	protected function getSegmentStructure( $segmentName )
	{
		$filePath = $this->filePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'hl7_segment_structure'.DIRECTORY_SEPARATOR.$segmentName.'.json';

		if( !file_exists($filePath) )
		{
			//throw new Exception($segmentName." HL7 segment is not supported");
		}

		$segmentData = file_get_contents( $filePath );

		$segmentData = trim( $segmentData) ;
		$segmentData = json_decode( $segmentData, true );

		/*Modify Segment Structure - Dynamically*/
		if( array_key_exists($segmentName, $this->modifications) )
		{
			$segmentData = array_replace($segmentData, $this->modifications[$segmentName]);
		}

		return $segmentData;
	}

	/*Modify segment structure*/
	public function altersegment($segment, $data)
	{
		$this->modifications[$segment] = $data;
	}

	protected function segmentExists( $segmentName )
	{
		$filePath = $this->filePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'hl7_segment_structure'.DIRECTORY_SEPARATOR.$segmentName.'.json';
		
		if( !file_exists($filePath) )
		{
			//throw new Exception($segmentName." HL7 segment is not supported");
		}
		return true;
	}

	/*
	 * Get absolute path to store HL7 flags.
	*/
	public function hl7FlagPath()
	{
		$flagPath = data_path();
		$flagPath = realPath( $flagPath );
		$flagPath .= DIRECTORY_SEPARATOR.'hl7Flags';
		
		/*Create Directory if not exists*/
		if( !is_dir($flagPath) )
		{
			mkdir( $flagPath, 0755, true );
			chown( $flagPath, 'apache' );
		}
		
		return $flagPath;
	}
}
