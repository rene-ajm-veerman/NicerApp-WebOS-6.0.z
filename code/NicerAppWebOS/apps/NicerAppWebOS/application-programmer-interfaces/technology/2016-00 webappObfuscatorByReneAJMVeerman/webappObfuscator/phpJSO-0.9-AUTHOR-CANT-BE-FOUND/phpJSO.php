<?php
/**
 * phpJSO - The Javascript Obfuscator written in PHP. Although
 * it effectively obfuscates Javascript code, it is meant to compress
 * code to save disk space rather than hide code from end-users.
 *
 * @started: Mon, May 23, 2005
 * @copyright: Copyright (c) 2004-2006 Cortex Creations, All Rights Reserved
 * @website: www.cortex-creations.com/phpjso
 * @license: Free, zlib/libpng license - see LICENSE
 * @version: 0.9
 * @subversion: $Id: phpJSO.php 70 2006-10-10 01:35:37Z josh $
 */

/**
 * Main phpJSO compression function. Pass Javascript code to it, and it will
 * return compressed code.
 */
function phpJSO_compress ($code, &$messages, $encoding_type, $fast_decompress, $collapse_blocks, $collapse_math_constants)
{
	// Start timer
	$start_time = phpJSO_microtime_float();
	
	// Array of tokens - alphanumeric
	$tokens = array();
	
	// Array of only numeric tokens, that are only inserted to prevent being
	// wrongly replaced with another token. For example: the integer 0 will
	// be replaced with whatever is at token index 0.
	$numeric_tokens = array();
	
	// Save original code length
	$original_code_length = strlen($code);
	
	// Remove strings and multi-line comments from code before performing operations
	$str_array = array();
	phpJSO_strip_strings_and_comments($code, $str_array, substr(md5(time()), 10, 2));
	
	// Strip junk from JS code
	phpJSO_strip_junk($code, true);
	if ($collapse_blocks)
	{
		$collapsed_blocks = 0;
		$code = phpJSO_collapse_blocks($code, $collapsed_blocks);
		$messages[] = 'Block collapse mode on: ' . $collapsed_blocks . ' blocks were collapsed.';
	}
	phpJSO_strip_junk($code);

	// Compress math constants in code?
	if ($collapse_math_constants)
	{
		$collapsed_math_constants = 0;
		$code = phpJSO_collapse_math($code, $collapsed_math_constants);
		$messages[] = 'Math constant collapse mode on: ' . $collapsed_math_constants . ' math constants were collapsed.';
	}
	
	// Add strings back into code
	phpJSO_restore_strings($code, $str_array);
	
	// Compressed code
	$compressed_code = $code;

	// Should we encode?
	if ($encoding_type == '1')
	{
		// BUG FIX: If a modulus is in the code, it will break obfuscation because the browser treats it as escaping of characters
		$compressed_code = str_replace('%', '% ', $compressed_code);
		
		// Find all tokens in code
		phpJSO_get_tokens($compressed_code, $numeric_tokens, $tokens);
		
		// Insert numeric tokens into token array
		phpJSO_merge_token_arrays($tokens, $numeric_tokens);

		// Replace all tokens with their token index
		phpJSO_replace_tokens($tokens, $compressed_code);
		
		// We have to sort the array because it can end up looking like this:
		// (
		//   [0] => var
		//   ...
		//   [5] => opera
		//   [7] => 
		//   [6] => domLib_isSafari
		//   [8] => domLib_isKonq
		// )
		ksort($tokens);
		reset($tokens);
		
		// Insert decompression code
		$compressed_code_double_slash = '"'.str_replace(array('\\', '"'), array('\\\\', '\\"'), $compressed_code).'"';
		$compressed_code_single_slash = "'".str_replace(array('\\', "'"), array('\\\\', "\\'"), $compressed_code)."'";
		$compressed_code = (strlen($compressed_code_double_slash) < strlen($compressed_code_single_slash) ? $compressed_code_double_slash : $compressed_code_single_slash);
		if ($fast_decompress)
		{
			$messages[] = 'Fast decompression mode.';
			$compressed_code = "eval(function(a,b,c,d,e){if(!''.replace(/^/,String)){d=function(e){return c[e]&&typeof(c[e])=='string'?c[e]:e};b=1}while(b--)if(c[b]||d)a=a.replace(new RegExp(e+(d?'\\\\w+':b)+e,'g'),d||c[b]);return a}($compressed_code,".count($tokens).",'".implode('|',$tokens)."'.split('|'),0,'\\\\b'));";
		}
		else
		{
			$compressed_code = "eval(function(a,b,c,d){while(b--)if(c[b])a=a.replace(new RegExp(d+b+d,'g'),c[b]);return a}($compressed_code,".count($tokens).",'".implode('|',$tokens)."'.split('|'),'\\\\b'));";
		}
		
		// Which is smaller: compressed code or uncompressed code?
		if (strlen($code) < strlen($compressed_code))
		{
			$messages[] = 'The uncompressed code (with only comments and whitespace removed)
				was smaller than the fully compressed code.';
			$compressed_code = $code;
		}
	}
	
	// End timer
	$execution_time = phpJSO_microtime_float() - $start_time;
	
	// Message about how long compression took
	$messages[] = "Compressed code in $execution_time seconds.";
	
	// Message reporting compression sizes
	$compressed_length = strlen($compressed_code);
	$ratio = $compressed_length / $original_code_length;
	$messages[] = "Original code length: $original_code_length.
		Compressed code length: $compressed_length.
		Compression ratio: $ratio.";
	
	return $compressed_code;
}

/**
 * Strip strings and comments from code
 */
function phpJSO_strip_strings_and_comments (&$str, &$strings, $comment_delim)
{
	// Find all occurances of comments and quotes. Then loop through them and parse.
	$quotes_and_comments = phpJSO_sort_occurances($str, array('/', '//', '/*', '*/', '"', "'"));

	// Loop through occurances of quotes and comments
	$in_string = $last_quote_pos = $in_comment = $in_regex = false;
	$removed = 0;
	$num_strings = count($strings);
	$invalid = array();
	foreach ($quotes_and_comments as $location => $token)
	{
		// Parse strings
		if ($in_string !== false)
		{
			if ($token == $in_string)
			{
				// First, we'll pull out the string and save it, and replace it with a number.
				$replacement = '`' . $num_strings . '`';
				$string_start_index = $last_quote_pos - $removed;
				$string_length = ($location - $last_quote_pos) + 1;
				$strings[$num_strings] = substr($str, $string_start_index, $string_length);
				++$num_strings;

				// Remove the string completely
				$str = substr_replace($str, $replacement, $string_start_index, $string_length);

				// Clean up time...
				$removed += $string_length - strlen($replacement);
				$in_string = $last_quote_pos = false;
			}
		}
		// Parse multi-line comments
		else if ($in_comment !== false)
		{
			// If it's the end of a comment, replace it with a single space
			// We replace it with a space in case a comment is between two tokens: test/**/test
			if ($token == '*/')
			{
				$comment_start_index = $in_comment - $removed;
				$comment_length = ($location - $in_comment) + 2;
				$str = substr_replace($str, ' ', $comment_start_index, $comment_length);
				$removed += $comment_length - 1;
				$in_comment = false;
			}
		}
		// Parse regex
		else if ($in_regex !== false)
		{
			// Should be end of the regex, unless it's escaped
			// If it is the end... don't do anything except stop parsing
			// We just don't want strings inside of regex to be removed,
			// like: /["']*/ -- VERY bad when mistaken as a string
			if ($token == '/')
			{
				$string_start_index = $in_regex - $removed;
				$string_length = ($location - $in_regex) + 1;
				$in_regex = false;
			}
		}
		else
		{
			// Make sure string hasn't been extracted by another operation...
			if (substr($str, $location - $removed, strlen($token)) != $token)
			{
				continue;
			}
			
			// This string shouldn't have been escaped...
			if ($location && $str[$location - $removed - 1] == '\\')
			{
				continue;
			}
			
			// See what this token is ...
			// Start of multi-line comment?
			if ($token == '/*')
			{
				$in_comment = $location;
			}
			// Start of a string?
			else if ($token == '"' || $token == "'")
			{
				$in_string = $token;
				$last_quote_pos = $location;
			}
			// A single-line comment?
			else if ($token == '//')
			{
				$comment_start_position = $location - $removed;
				$newline_pos = strpos($str, "\n", $comment_start_position);
				$comment_length = ($newline_pos !== false ? $newline_pos - $comment_start_position : $comment_start_position);
				$str = substr_replace($str, '', $comment_start_position, $comment_length);
				$removed += $comment_length;
			}
			// Start of a regex expression?
			// Note that the second part of this conditional fixes a bug: if there
			// is a regex sequence followed by a comment of the EXACT SAME length,
			// it will try to parse the regex sequence a second time...
			else if ($token == '/' && (!isset($quotes_and_comments[$location - 1]) || ($quotes_and_comments[$location - 1] != '//' && $quotes_and_comments[$location - 1] != '*/')))
			{
				// Only start a regex sequence if there was NOT
				// an alphanumeric sequence before.
				// var regex = /pattern/
				// string.match(/pattern/)
				if (preg_match('#[(=]#', $str[$location - $removed - 1]))
				{
					$in_regex = $location;
				}
			}
		}
	}
}

/**
 * Strips junk from code
 */
function phpJSO_strip_junk (&$str, $whitespace_only = false)
{
	// Remove unneeded spaces and semicolons
	$find = array
	(
		'/([^a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/([^a-zA-Z0-9_$]|^)\s+([a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/([a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/([^a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/([^a-zA-Z0-9_$]|^)\s+([a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/([a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/[\r\n]/s', // Unneeded newlines
		"/\t+/" // replace tabs with spaces
	);
	// Unneeded semicolons
	if (!$whitespace_only)
	{
		$find[] = '/;(\}|$)/si';
	}
	$replace = array
	(
		'$1$2',
		'$1$2',
		'$1$2',
		'$1$2',
		'$1$2',
		'$1$2',
		'',
		' ',
		'$1',
	);
	$str = preg_replace($find, $replace, $str);
}

/**
 * Collapses code blocks.
 */
function phpJSO_collapse_blocks ($code, &$collapse_count)
{
	
	// The :parenthetical: is replaced dynamically in the loop below.
	// The key values mean this: the first and second values in the array are the indexes
	// of the parenthetical subscripts, and the third value is the replace value
	// for the regex.
	$regex = array
	(
		// When there is one command inside a block, remove brackets
		'#((if|for|while)\(:paren0:\))\{([^;{}]*;)\}#si' => array(3, 0, '$1$5', 5, 0),
		'#((if|for|while)\(:paren0:\))\{([^;{}]*)\}(?!;)#si' => array(3, 0, '$1$5;', 5, 0),
		'#((if|for|while)\(:paren0:\))\{([^;{}]*)\}(?=;)#si' => array(3, 0, '$1$5', 5, 0),
		// Collapse brackets with else and do statements
		'#(do|else)\{([^;{}]*)\}#si' => array(0, 0, '$1 $2;', 2, 0),
		'#(do|else)\{([^;{}]*;)\}#si' => array(0, 0, '$1 $2', 2, 0),
		// Remove brackets when a block is inside a block, EG if(1){if(2){}}
		'#((if|for|while)\(:paren0:\))\{((if|for|while|function [a-zA-Z_$][a-zA-Z0-9_$]*)\(:paren1:\))\{([^{}]*)\}\}(?!else)#si' => array(3, 7, '$1$5{$9}', 0, 0),
		'#((if|for|while)\(:paren0:\))\{((if|for|while|function [a-zA-Z_$][a-zA-Z0-9_$]*)\(:paren1:\))([^{};]*);?\}(?!else)#si' => array(3, 7, '$1$5$9;', 0, 0),
		'#((if|for|while)\(:paren0:\))\{([^;{]*)\{([^{}]*)\};?\}(?!else)#siU' => array(3, 0, '$1$5{$6};$7', 0, 0),
		// Remove brackets when a block is inside a block with no parentheticals, EG else{if(2){}}
		'#(else|do)\{((if|for|while|function [a-zA-Z_$][a-zA-Z0-9_$]*)\(:paren0:\))\{([^{}]*)\}\}#si' => array(4, 0, '$1 $2{$6}', 0, 0),
		'#(else|do)\{((if|for|while|function [a-zA-Z_$][a-zA-Z0-9_$]*)\(:paren0:\))([^{};]*);?\}#si' => array(4, 0, '$1 $2$6;', 0, 0),
		'#(else|do)\{([^;{}]*)\{([^{}]*)\};?\}#si' => array(0, 0, '$1 $2{$3};', 0, 0)
	);

	// Collapse all blocks when possible
	while (1)
	{
		$original_code = $code;

		// Loop through all patterns
		foreach ($regex as $find => $regex_data)
		{
			// Match all occurences of pattern
			$matches = array();
			$find_all = str_replace(':paren0:', '([^{}()]*(\([^{}]*)?)', $find);
			$find_all = str_replace(':paren1:', '([^{}()]*(\([^{}]*)?)', $find_all);
			preg_match_all($find_all, $code, $matches);
			
			// Loop through all matches, and if the number of opening and closing
			// parentheses is even, collapse the block
			for ($i = 0; isset($matches[0][$i]); ++$i)
			{
				// Don't find nested loops in some patterns
				if ($regex_data[3] && preg_match('#^if#si', $matches[$regex_data[3]][$i]))
				{
					continue;
				}
				
				// If loops are immediately followed by "else", don't continue
				if ($regex_data[4] && strtolower($matches[$regex_data[4]][$i]) == 'else')
				{
					continue;
				}
				
				$complete_match = true;
				$find_complete = $find;
				for ($j = 0; $j != 2; ++$j)
				{
					if ($regex_data[$j])
					{
						$parenthetical = &$matches[$regex_data[$j]][$i];
						if (!($parenthetical = phpJSO_is_valid_parenthetical($parenthetical)))
						{
							$complete_match = false;
						}
						$find_complete = str_replace(':paren'.$j.':', '((' . preg_quote($parenthetical) . '))', $find_complete);
					}
				}
				if ($complete_match)
				{
					$code = preg_replace($find_complete, $regex_data[2], $code);
					++$collapse_count;
				}
			}
		}
		break;

		if ($original_code === $code)
		{
			break;
		}
	}
	return $code;
}

/**
 * Collapse math constants in code.
 */
function phpJSO_collapse_math ($code, &$collapsed)
{
	preg_match_all('#(^|[^a-zA-Z0-9_\$])(([()]|([\+\-\/\*\%])?(\-)?(0x[0-9a-fA-F]+|[0-9]+(\.[0-9]+)?))+)([^a-zA-Z0-9_\$]|$)#s', $code, $matches);

	// Loop through all matches
	for ($i = 0; isset($matches[0][$i]); ++$i)
	{
		$match = $matches[2][$i];

		// Make sure it is a valid math block
		if (!($match = phpJSO_is_valid_parenthetical($match)))
		{
			continue;
		}

		// Must end and begin with parentheses or numbers
		if ($match{0} != '(' && !is_numeric($match{0}))
		{
			continue;
		}
		$last_index = strlen($match) - 1;
		if ($match{$last_index} != ')' && !is_numeric($match{strlen($match) - 1}) && !ctype_alnum($match{$last_index}))
		{
			continue;
		}

		// Must be more than just symbols or just numbers
		//if (!preg_match('#[0-9]#', $match) || preg_match('#^[0-9]+$#', $match))
		//{
		//	continue;
		//}
		if (preg_match('#\(\)#', $match))
		{
			continue;
		}

		// Convert hex to dec if the dec is smaller
		preg_match_all('#0x[0-9a-fA-F]+#', $code, $hex_matches);
		foreach ($hex_matches[0] as $hex_match)
		{
			$dec = hexdec($hex_match);
			if (strlen($dec) <= strlen($hex_match))
			{
				$code = str_replace($hex_match, $dec, $code);
				$match = str_replace($hex_match, $dec, $match);
			}
		}

		// Parse it, replace it
		$code = @preg_replace('#'.preg_quote($match).'#e', $match, $code);
		++$collapsed;
	}
	
	return $code;
}

/**
 * Get all the tokens in code and put them in two arrays - one array
 * for just numeric tokens, and another array for all the rest.
 */
function phpJSO_get_tokens ($code, &$numeric_tokens, &$tokens)
{
	preg_match_all('#([a-zA-Z0-9\_\$]+)#s', $code, $match);
	$matched_tokens = array_values(array_unique($match[0]));
	phpJSO_count_duplicates($duplicates, $match[0]);
	foreach ($matched_tokens as $token)
	{
		// If token is an integer, we do replacements differently
		if (preg_match('#^([1-9][0-9]*|0)$#', $token))
		{
			$numeric_tokens[$token] = 1;
		}
		// We can place token in the array normally (but it's only worth doing
		// a replacement if the token isn't just one character).
		// It's also only worth doing a replacement if the token appears more than once in code.
		else if (isset($token{1}) && $duplicates[$token] > 1)
		{
			$tokens[] = $token;
		}
	}
}

/**
 * Merges the two token arrays: numeric tokens and regular tokens.
 * Specifically this function will take all the numeric tokens and
 * POSSIBLY put them in the token array if that's necessary.
 */
function phpJSO_merge_token_arrays (&$tokens, &$numeric_tokens)
{
	// Sort numeric token array
	ksort($numeric_tokens);

	// Loop through all numeric tokens
	$num_tokens = count($tokens);
	foreach ($numeric_tokens as $int=>$void)
	{
		if ($num_tokens < $int)
		{
			// We may not need to consider ANY more numeric tokens, if this
			// one is lower than the number of tokens, since the numeric tokens
			// are sorted already. This can potentially save a lot of time.
			if (strlen(strval($num_tokens)) >= strlen(strval($int)))
			{
				break;
			}
			else
			{
				$tokens[] = $int;
				continue;
			}
		}
		phpJSO_insert_token($tokens, '', $int);
		++$num_tokens;
	}
}

/**
 * Inserts a token into the token array. Shifts all the other tokens
 * and puts it somewhere in the middle, based on token_index.
 */
function phpJSO_insert_token (&$token_array, $token, $token_index)
{
	// Loop through array and shift all indexes up one spot until we reach the
	// index we are inserting at
	$jump = 1;
	$token_index_count = $token_index - 1;
	for ($i = count($token_array) - 1; $i > $token_index_count; --$i)
	{
		if ($token_array[$i] == '')
		{
			++$jump;
			continue;
		}
		$token_array[$i+$jump] = $token_array[$i];
		$jump = 1;
	}
	$token_array[$token_index] = $token;
}

/**
 * Place stripped strings back into code
 */
function phpJSO_restore_strings (&$str, &$strings)
{
	//do
	//{
		$str = preg_replace('#`([0-9]+)`#e', 'isset($strings[\'$1\']) ? $strings[\'$1\'] : \'`$1`\'', $str);
	//}
	//while (preg_match('#`([0-9]+)`#', $str));
}

/**
 * Count duplicate values in an array
 */
function phpJSO_count_duplicates (&$dupes, $ary)
{
	foreach ($ary as $v)
	{
		//$dupes[$v] = (isset($dupes[$v]) ? $dupes[$v] : 0) + 1;
		if (isset($dupes[$v]))
		{
			++$dupes[$v];
		}
		else
		{
			$dupes[$v] = 1;
		}
	}	
}

/**
 * Replaces tokens in code with the corresponding token index.
 */
function phpJSO_replace_tokens (&$tokens, &$code)
{
	$tokens_flipped = array_flip($tokens);
	unset($tokens_flipped['']);
	$find = '#\b('.implode('|', array_flip($tokens_flipped)).')\b#e';
	$code = preg_replace($find, '(isset($tokens_flipped[\'$1\']) ? $tokens_flipped[\'$1\'] : \'$1\')', $code);
}

/**
 * Check whether a parenthetical is valid or not.
 */
function phpJSO_is_valid_parenthetical ($parenthetical)
{
	$open_parentheses = 0;
	
	// Get all parentheses in the string
	$parentheses = phpJSO_sort_occurances($parenthetical, array('(', ')'));

	// Loop through parentheses
	foreach ($parentheses as $index => $parenthesis)
	{
		if ($parenthesis == ')')
		{
			if (!$open_parentheses)
			{
				return ($index ? substr($parenthetical, 0, $index) : false);
			}

			--$open_parentheses;
		}
		else
		{
			++$open_parentheses;
		}
	}

	if ($open_parentheses != 0)
	{
		return false;
	}

	return $parenthetical;
}

/**
 * Finds all occurances of different strings in the first passed string and sorts
 * them by location. Returns array of locations. The key of each array element is the string
 * index (location) where the string was found; the value is the actual string, as seen below.
 *
 * [18] => "
 * [34] => "
 * [56] => /*
 * [100] => '
 */
function phpJSO_sort_occurances (&$haystack, $needles)
{
	$locations = array();
	
	foreach ($needles as $needle)
	{
		$pos = -1;
		//$needle_length = strlen($needle);
		while (($pos = @strpos($haystack, $needle, $pos+1)) !== false)
		{
			// Don't save location if string length is 1, and the needle is escaped
			if ($pos && $haystack[$pos - 1] == '\\' && $needle != '*/')
			{
				continue;
			}

			// Save location of needle
			$locations[$pos] = $needle;
		}
	}
	
	ksort($locations);
	
	return $locations;
}

/**
 * For timing compression
 */
function phpJSO_microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
?>
