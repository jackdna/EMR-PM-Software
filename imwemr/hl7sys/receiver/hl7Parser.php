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
 *File: hl7Parser.php
 *Puppose: HL7 parser to parse inbound messages. It will return a structured array for all the HL7 segments received in the HL7 message provided.
 *Access Type: Include
 */

// require_once(dirname(__FILE__)."/../hl7GP/hl7Base.php");
require_once(dirname(__FILE__)."/../hl7GP/hl7Create.php");

class hl7Parser extends hl7Create
{
	/*
	 * This function will parse the Hl7 message provided and return structured Array for the particular Hl7 message.
	 * This class will parse only one message at time
	 * @data = HL7 message;
	*/
	public $message_unstructured;
	private $getUnstructured;

	public function parse( $data )
	{
		
		$this->reset();
		
		/*Strip extra blank space from both the ends*/
		$data = trim($data);

		if( empty($data) )
		{
			//throw new Exception('Blank Hl7 message supplied.');
		}

		/*Strip line ending characters*/
		$data = trim($data, PHP_EOL);

		/*Strip File separator Character*/
		$data = trim($data, chr(28));

		/*Strip Vertical Tabs*/
		$data = trim($data, chr(11));
		
		/*Explode Messages Separated by File Separator Character*/
		$message = explode(chr(28), $data);

		/*Use first HL7 message in the list*/
		$message = array_shift($message);
		
		/*Segments Array*/
			/*Trim Carriage Return characters*/
		$message = trim($message, chr(13));
		$message = explode(chr(13), $message);

		// $message = preg_split("/[\n\\" . chr(13) . "]/", $message, -1, PREG_SPLIT_NO_EMPTY);
		
		/*Arrange segments in structured format*/
		foreach( $message as &$segment )
		{
			/*Remove white space from both ends of of segments*/
			$segment = preg_replace('/^\s+|\s+$/', '', $segment);
			$segment = trim($segment, chr(13));

			/*Arrange in fields*/
			$segment = explode($this->fieldSeparator, $segment);

			/*Arrange Repeat Fields*/
			array_walk_recursive($segment, array($this, 'processFields'), array($this->repeatFieldSeperator, 'repeat') );

			/*Arrange Componsents*/
			array_walk_recursive($segment, array($this, 'processFields'), $this->componentSeparator);

			/*Arrange Subcomponents*/
			array_walk_recursive($segment, array($this, 'processFields'), $this->subcomponentSeparator);
		}
		if(isset($segment)) unset($segment);
		/*End segment arrangement in structured format*/

		$this->message_unstructured = array();

		foreach( $message as $key=>$segment )
		{
			if( !array_key_exists($segment[0], $this->message_unstructured) )
			{
				$this->message_unstructured[$segment[0]] = array();
			}
			$tempSegment = $segment;
			array_push($this->message_unstructured[$tempSegment[0]], $tempSegment);
		}

		/*Map to segment Structures*/
		$tempMessage = array();
		foreach( $message as &$segment )
		{
			$segmentName = $segment[0];

			$segmStructure = $this->getSegmentStructure( $segmentName );

			$fieldCounter = 0;
			$tempSegment = array();

			/*Map Fields*/
			foreach ($segmStructure as $segFieldKey => $segField)
			{
				
				/*Slot for Field in Temp. Container*/
				$tempSegment[$segFieldKey] = '';

				/*Map Components*/
				
				if( is_array($segField) )
				{
					$tempSegment[$segFieldKey] = array();
					
					/*Repeat Fields - It can be in added to fields only. We do not accept this in components or subcomponents.*/
					$iterateRepeatFields = 0;
					do
					{
						/*Components*/
						$componentData = array();
						if(
							array_key_exists($fieldCounter, $segment)
							&&
							is_array($segment[$fieldCounter])
							&&
							array_key_exists('repeat', $segment[$fieldCounter])
						)
						{
							$componentData =  ( array_key_exists($iterateRepeatFields, $segment[$fieldCounter]['repeat']) ) ? $segment[$fieldCounter]['repeat'][$iterateRepeatFields] : '';
							$tempField = &$tempSegment[$segFieldKey]['repeat'][$iterateRepeatFields];
							$iterateRepeatFields++;

							if( count( $segment[$fieldCounter]['repeat'] )-1 < $iterateRepeatFields )
							{
								$iterateRepeatFields = false;
							}
						}
						else
						{
							$componentData = ( array_key_exists($fieldCounter, $segment) ) ? $segment[$fieldCounter] : '';
							$tempField = &$tempSegment[$segFieldKey];

							$iterateRepeatFields = false;
						}


						$componentCounter = -1;
						foreach ($segField as $componentKey => $component)
						{
							$componentCounter++;
							/*Subcomponents*/
							if( is_array($component) )
							{
								$subComponentCounter = -1;
								foreach($component as $subComponentKey => $subComponent)
								{
									$subComponentCounter++;
									// print $subComponentKey."\n";

									if( isset($componentData) && !is_array($componentData) )
									{
										$tempField = $segField;
										$tempField[$componentKey] = $component;
										$tempField[$componentKey][$subComponentKey] = $componentData;
										break 2;
									}
									elseif( array_key_exists($componentCounter, $componentData) && !is_array($componentData[$componentCounter]) )
									{
										$tempField[$componentKey] = $component;
										$tempField[$componentKey][$subComponentKey] = ( array_key_exists($componentCounter, $componentData) ) ? $componentData[$componentCounter] : '';
										break;
									}

									if( array_key_exists($componentCounter, $componentData) )
									{
										$tempField[$componentKey][$subComponentKey] = ( array_key_exists($subComponentCounter, $componentData[$componentCounter]) ) ? $componentData[$componentCounter][$subComponentCounter] : '';
									}
									else
									{
										$tempField[$componentKey][$subComponentKey] = '';
									}
									
								}
							}
							/*End Subcomponents*/
							else
							{
								if( !is_array($componentData) )
								{
									$tempField = $segField;
									$tempField[$componentKey] = $componentData;
									break;
								}
								$tempField[$componentKey] = ( array_key_exists($componentCounter, $componentData) )? $componentData[$componentCounter] : '';
							}
						}

						unset($tempField);
					}
					while($iterateRepeatFields !== false);
				}
				else
				{
					$tempSegment[$segFieldKey] = ( array_key_exists($fieldCounter, $segment) )? $segment[$fieldCounter] : '';
				}
				$fieldCounter++;
			}

			if( !array_key_exists($segmentName, $tempMessage) )
			{
				$tempMessage[$segmentName] = array();
			}
			array_push($tempMessage[$segmentName], $tempSegment);
		}
		/*End Mapping to segment structure*/

		$message = $tempMessage;


		/* Reject message without or with more then on MSH */
		if( !array_key_exists('MSH', $message) )
		{
			throw new Exception('No. MSH segment found.');
		}
		elseif( count($message['MSH']) != 1 )
		{
			throw new Exception('Multiple MSH segments detected.');
		}

		/*unset encoding characters field from MSH*/
		$message['MSH'][0]['encoding_characters'] = '';
		$this->message_unstructured['MSH'][0][1] = '';
		
		$this->message = $message;
	}

	/*This Function is used to process the segment fields with separator passed */
	public function processFields(&$data, $index, $userData)
	{
		if( is_array($userData) )
		{
			$separatorType = trim(array_pop($userData));
			$separator = array_pop($userData);
		}
		else
		{
			$separator = $userData;
			$separatorType = '';
		}
		
		if( strpos($data, $separator) !== false )
		{
			$regex = '/(?<!\\\)\\'.$separator.'/';
			$tempData = preg_split($regex, $data);
		
			if( $separatorType !== '' )
			{
				$data = array();
				$data[$separatorType] = $tempData;
			}
			else
			{
				$data = $tempData;
			}
		}
		if( isset($tempData) )
			unset($tempData);
	}

	protected function getSegmentValue($segName, $field, $component = false, $subComponent = false, $returnRepeat = false, $setId = 0)
	{
		if($this->getUnstructured === true)
		{
			$segment = $this->message_unstructured[$segName][$setId];

			if( $component !== false && $component != 0 )
			{
				$component--;
			}
			if( $subComponent !== false && $subComponent != 0 )
			{
				$subComponent--;
			}
		}
		else
		{
			$segment = $this->message[$segName][$setId];
		}
		
		$data = $segment[$field];

		$returnData = array();
		$returnValue = &$returnData;
		$isRepeat = false;
		if( is_array($data) && array_key_exists('repeat', $data))
		{
			$returnData = array('repeat'=>array());
			$returnValue = &$returnData['repeat'];
			$isRepeat = true;
		}

		if( is_array($data) )
		{
			
			$repeatFlag = false;
			do{
				if( $isRepeat === true )
				{
					$tempData = array_shift($data['repeat']);
					if( count($data['repeat']) == 0 )
					{
						$repeatFlag = false;
					}
					else
					{
						$repeatFlag = true;
					}
				}
				else
				{
					$tempData = $data;
				}


				if( $component === false )
				{
					$tempData = array_shift($tempData);
				}
				else
				{
					$tempData = $tempData[$component];
				}
				
				if( is_array($tempData) )
				{
					if( $subComponent === false )
					{
						$tempData = array_shift($tempData);
					}
					else
					{
						$tempData = $tempData[$subComponent];
					}
				}
				array_push($returnValue, $tempData);

			}
			while($repeatFlag === true);
		}
		else
		{
			array_push($returnValue, $data);
		}

		if( !array_key_exists('repeat', $returnData) )
		{
			$returnData = array_shift($returnData);
		}

		if( is_null($returnData) )
		{
			$returnData = '';
		}

		if( $returnRepeat !== true && is_array($returnData) && array_key_exists('repeat', $returnData) )
		{
			throw new Exception("Repeat fields not supported in `".$segName."`.`".$field."`");
		}

		return $returnData;
	}

	public function __call($segmentIdentifier, $arguments)
	{
		$segmentIdentifier = trim($segmentIdentifier);
		$segmentCount = 0;

		/*First three characters represents segment name and presceding character(s) represents segment count*/
		$segmentName = substr($segmentIdentifier, 0, 3);
		if( strlen($segmentIdentifier) > 3)
		{
			$segmentCount = substr($segmentIdentifier, 3);
			$segmentCount = (int)$segmentCount;
		}

		/*Validate Segment Name*/
		if( !array_key_exists($segmentName, $this->message) )
		{
			throw new Exception($segmentName.' segment does not exists');
		}
		
		$this->getUnstructured = false;

		$arguments = array_chunk($arguments, 4);
		$arguments = $arguments[0];


		/*Return Data if Called by Numeric indexes*/
		if( is_integer($arguments[0]) )
		{
			$this->getUnstructured = true;
		}

		$arguments = array_pad($arguments, 4, false);
		array_push($arguments, $segmentCount);

		$data = $this->getSegmentValue($segmentName, $arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);

		return $data;
	}


}
