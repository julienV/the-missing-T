<?php
/**
* @version    $Id$ 
* @package    Missingt
* @copyright  Copyright (C) 2008 Julien Vonthron. All rights reserved.
*/

class MissingTLine {	
	public $line;
	public $key;
	public $value;
	public $is_section = false;
	public $added = false;
	public $foundin = array();
		 
  /**
	 * Parse an INI formatted string and convert it into an object.
	 *
	 * @param   string   INI formatted string to convert.
	 * @param   mixed    An array of options used by the formatter, or a boolean setting to process sections.
	 *
	 * @return  object   Data object.
	 * @since   11.1
	 *
	 */
	static public function iniStringToArray($data)
	{
		// If no lines present just return the object.
		if (empty($data)) {
			return array();
		}

		// Initialize variables.
		$res = array();
		$section = false;
		$lines = explode("\n", $data);

		// Process the lines.
		foreach ($lines as $k => $line) 
		{
			$obj = new MissingTLine;
			$obj->line = $k;
			
			// Trim any unnecessary whitespace.
			$line = trim($line);

			// Ignore empty lines and comments.
			if (empty($line) || ($line{0} == ';')) {
				$obj->value = $line;
				$res[] = $obj;
				continue;
			}
			else if ($line{0} == '#') {
				// rewrite with a ;
				$line{0} = ';';
				$obj->value = $line;
				$res[] = $obj;
				continue;
			}

			$length = strlen($line);
			
			// If we are processing sections and the line is a section add the object and continue.
			if (($line[0] == '[') && ($line[$length-1] == ']')) {
				$section = substr($line, 1, $length-2);
				$obj->is_section = true;
				$obj->value = $section;
				$res[] = $obj;
				continue;
			}

			// Check that an equal sign exists and is not the first character of the line.
			if (!strpos($line, '=')) {
				// Maybe throw exception? discard line anyway !
				continue;
			}

			// Get the key and value for the line.
			list($key, $value) = explode('=', $line, 2);

			// Validate the key.
			if (preg_match('/[^A-Z0-9_]/i', $key)) {
				// Maybe throw exception? discard line anyway !
				continue;
			}
			
			$length = strlen($value);
			
			// If the value is quoted then we assume it is a string.
			if ($length && ($value[0] == '"') && ($value[$length-1] == '"')) {
				// Strip the quotes and Convert the new line characters.
				$value = stripcslashes(substr($value, 1, ($length-2)));
				$value = str_replace('\n', "\n", $value);
			} else {
				// If the value is not quoted, we assume it is not a string.

				// If the value is 'false' assume boolean false.
				if ($value == 'false') {
					$value = false;
				}
				// If the value is 'true' assume boolean true.
				elseif ($value == 'true') {
					$value = true;
				}
				// If the value is numeric than it is either a float or int.
				elseif (is_numeric($value)) {
					// If there is a period then we assume a float.
					if (strpos($value, '.') !== false) {
						$value = (float) $value;
					}
					else {
						$value = (int) $value;
					}
				}
			}

			$obj->key   = $key;
			$obj->value = $value;
			$res[] = $obj;		
		}

		return $res;
	}
}