<?php
/**
 * phpJSO unit tests. Run like so on the command line:
 *
 * phpunit Tests
 *
 * phpunit2 must be installed. Use the PEAR installer.
 * http://www.phpunit.de/en/phpunit2_install.php
 *
 * @started: Wednesday, September 28, 2005
 * @copyright: Copyright (c) 2004, 2005 JPortal, All Rights Reserved
 * @website: www.jportalhome.com
 * @license: Free, zlib/libpng license - see LICENSE
 * @subversion: $Id: tests.php 67 2006-08-29 01:42:55Z josh $
 */

require_once("PHPUnit/Framework/TestCase.php");
define('UNIT_TEST', true);
require("phpjso.php");

class Tests extends PHPUnit_Framework_TestCase
{
	/**
	 * Test string and multi-line comment removal.
	 */
	public function test_string_comment_removal ()
	{
		// This code is full of multi-line comments and strings that should be parsed out
		$start_code = "/** test\ntest\ntest\n. Hopefully phpJSO won\'t get confused\"! */\n"
			. "// -- Create global items container stylesheet object ' \" --\n"
			. "/* ' * \" ''  */"
			. "document.write('\\'/*<style>'+/**/\n"
			. "'div.WebDDM_items_container {*/'+/**/test/**/\n"
			. "'position: /*absolute; top: 0px;'+\n"
			. "'*/}' + '</style>/*\"*/');\n\n"
			. "'\*/string/*'.match(/\/*/).match(/\/*\//).match(/.*/);"
			. "/**\n"
			. " * Specially escapes a string so that it can be put inside of another string to\n"
			. " * be evaluated. The quoteType should be either ' or \".\n"
			. " * NOTE: This function is not used in this source file but it is used in some\n"
			. " * plugins and is still very useful'"
			. " */alert('');// test";

		$expected_result =  " \n\n document.write(`0`+ \n"
			. "`1`+ test \n"
			. "`2`+\n"
			. "`3` + `4`);\n\n"
			. "`5`.match(/\/*/).match(/\/*\//).match(/.*/); alert(`6`);";
		
		// Get real result and verify it
		$str_array = array();
		$real_result = $start_code;
		phpJSO_strip_strings_and_comments($real_result, $str_array, substr(md5(time()), 10, 2));
		self::assertTrue($expected_result == $real_result);

		// Check that the correct strings are in the array
		self::assertTrue(count($str_array) == 7);
		self::assertTrue($str_array[0] == "'\'/*<style>'");
		self::assertTrue($str_array[1] == "'div.WebDDM_items_container {*/'");
		self::assertTrue($str_array[2] == "'position: /*absolute; top: 0px;'");
		self::assertTrue($str_array[3] == "'*/}'");
		self::assertTrue($str_array[4] == "'</style>/*\"*/'");
		self::assertTrue($str_array[5] == "'\*/string/*'");

		// Test string characters in regex
		$start_code = 'var a = 5/2; return ("\""+o.replace(/(["\\])/g,"\\$1")+"\"").replace(/[\f]/g,"\\f").replace(/[\b]/g,"\\b").replace(/[\n]/g,"\\n").replace(/[\t]/g,"\\t").replace(/[\r]/g,"\\r");';
		$expected_result = 'var a = 5/2; return (`0`+o.replace(/(["\])/g,`1`)+`2`).replace(/[\f]/g,`3`).replace(/[\b]/g,`4`).replace(/[\n]/g,`5`).replace(/[\t]/g,`6`).replace(/[\r]/g,`7`);';
		$str_array = array();
		$real_result = $start_code;
		phpJSO_strip_strings_and_comments($real_result, $str_array, substr(md5(time()), 10, 2));
		self::assertTrue($expected_result == $real_result);

		// Test weird comments, strings etc
		$str_array = array();
		$real_result = "// \n/*\*/ \"\\\"\" '\\'' /\// /'/ /.*/";
		$expected_result = "\n  `0` `1` /\// /'/ /.*/";
		phpJSO_strip_strings_and_comments($real_result, $str_array, substr(md5(time()), 10, 2));
		self::assertTrue($expected_result == $real_result);

		// Test string with an URL in it
		$str_array = array();
		$real_result = "var test = 'http://www.w3.org/2000/svg'; alert(test);";
		$expected_result = "var test = `0`; alert(test);";
		phpJSO_strip_strings_and_comments($real_result, $str_array, substr(md5(time()), 10, 2));
		self::assertTrue($expected_result == $real_result);
	}

	/**
	 * Test phpJSO_sort_occurances
	 */
	public function test_sort_occurances ()
	{
		$str = "// lawl\n/**/\nreplace(/test/,'aha')";
		$occurs = phpJSO_sort_occurances($str, array('/', '//', '/*', '*/', '"', "'"));
		self::assertTrue($occurs[0] == '//');
		self::assertTrue($occurs[8] == '/*');
		self::assertTrue($occurs[10] == '*/');
		self::assertTrue($occurs[21] == '/');
		self::assertTrue($occurs[26] == '/');
		self::assertTrue($occurs[28] == '\'');
		self::assertTrue($occurs[32] == '\'');

		$str = "// /*\*/ \"\\\"\" '\\'' /\// /'/ /.*/";
		$occurs = phpJSO_sort_occurances($str, array('/', '//', '/*', '*/', '"', "'"));
		//print_r($occurs);
		self::assertTrue($occurs[0] == '//');
		self::assertTrue($occurs[3] == '/*');
		self::assertTrue($occurs[6] == '*/');
		self::assertTrue($occurs[9] == '"');
		self::assertTrue($occurs[12] == '"');
		self::assertTrue($occurs[14] == '\'');
		self::assertTrue($occurs[17] == '\'');
		self::assertTrue($occurs[19] == '/');
		self::assertTrue($occurs[22] == '/');
		self::assertTrue($occurs[24] == '/');
		self::assertTrue($occurs[25] == '\'');
		self::assertTrue($occurs[26] == '/');
		self::assertTrue($occurs[28] == '/');
		self::assertTrue($occurs[30] == '*/');
		self::assertTrue($occurs[31] == '/');
	}
	
	/**
	 * Test whitespace and single-line comment removal
	 * Bugs to look out for: under the old phpJSO code, pre-0.5,
	 * not all whitespace would be removed. Specifically, whitespace
	 * between two closing brackets, preceded by a non-word character.
	 * IE:
	 * if (1) {
	 *    if (2) {
	 *      alert(3);
	 *    }
	 * }
	 *
	 * Would become: if(1){if(2){alert(3)}
	 * }
	 *
	 * Extra regex has been addded. In the phpJSO_strip_junk function,
	 * look at the six similar regexes; one of them excludes whitespace
	 * from the search (\s) and the other includes it. This is the fix
	 * as of right now.
	 */
	public function test_junk_removal_removal ()
	{
		// Code, full of random whitespace that should be removed
		$start_code =  "var domLib_userAgent = navigator.userAgent.toLowerCase();"
			. "\t\n\r\t  var domLib_isMac\t=\tnavigator.appVersion.indexOf('Mac') != -1; \n 	\n\n 	   "
			. "if (domLib_isMac) {\n"
			. "\t\talert(1)\n\t\t\t"
			. "if (1){alert(1.1)\r\n}\r"
			. "\t}\n"
			. "else\n{\t\talert(2); \tvar\ttest; \r\n\r\n}\t\n";
			
		// Should look like this after parsing
		$expected_result = "var domLib_userAgent=navigator.userAgent.toLowerCase();var domLib_isMac=navigator.appVersion.indexOf('Mac')!=-1;if(domLib_isMac){alert(1)if(1){alert(1.1)}}else{alert(2);var test}";

		// Get the real result
		$real_result = $start_code;
		phpJSO_strip_junk($real_result);

		// Check for errors
		self::assertTrue($expected_result == $real_result);	

		// Test bugs found in old system while compressing MochiKit
		// return _17, etc...
		$real_result = 'return _17_ _lolz_ aha $test $var';
		$expected_result = 'return _17_ _lolz_ aha $test $var';
		phpJSO_strip_junk($real_result);
		self::assertTrue($real_result == $expected_result);
	}

	/**
	 * Test getting tokens from code.
	 */
	public function test_get_tokens ()
	{
		// Code with all the tokens
		$code = 'container.innerHTML = \'<div style="width: 5; visibility: visible;" id="WebDDM_loading_\' + container.id + \'">A WebDDM menu is loading; please wait!</div>\';'
			. 'var test = 5 + 7 + 7; alert(container.innerHTML); container.innerHTML = id;';

		// Expected results
		$expected_tokens = array
		(
			0 => 'container',
			1 => 'innerHTML',
			2 => 'div',
			3 => 'id'
		);
		$expected_numeric_tokens = array
		(
			5 => 1,
			7 => 1
		);

		// Get actual results
		$real_tokens = array();
		$real_numeric_tokens = array();
		phpJSO_get_tokens($code, $real_numeric_tokens, $real_tokens);
		
		// Verify results
		foreach (array('tokens', 'numeric_tokens') as $token_type)
		{
			self::assertTrue(count(${"expected_$token_type"}) == count(${"real_$token_type"}));

			foreach (${"expected_$token_type"} as $k=>$v)
			{
				self::assertTrue($v == ${"real_$token_type"}[$k]);
			}
		}
	}

	/**
	 * Test merging of numeric token array into token array.
	 */
	public function test_merge_token_arrays ()
	{
		// Beginning token array and numeric token array
		$begin_tokens = array
		(
			0 => 'container',
			1 => 'innerHTML',
			2 => 'div',
			3 => 'id',
			4 => 'yo',
			5 => 'mama'
		);
		$begin_numeric_tokens = array
		(
			10 => 1,
			13 => 1,
			5 => 1,
			4 => 1,
			3 => 1,
			2 => 1,
			1 => 1
		);

		// Get actual results of merging and verify them
		$real_tokens = $begin_tokens;
		$real_numeric_tokens = $begin_numeric_tokens;
		phpJSO_merge_token_arrays($real_tokens, $real_numeric_tokens);
		self::assertTrue($real_tokens[0] == $begin_tokens[0]);
		self::assertTrue($real_tokens[1] == '');
		self::assertTrue($real_tokens[2] == '');
		self::assertTrue($real_tokens[3] == '');
		self::assertTrue($real_tokens[4] == '');
		self::assertTrue($real_tokens[5] == '');
		self::assertTrue($real_tokens[6] == $begin_tokens[1]);
		self::assertTrue($real_tokens[7] == $begin_tokens[2]);
		self::assertTrue($real_tokens[8] == $begin_tokens[3]);
		self::assertTrue($real_tokens[9] == $begin_tokens[4]);
		self::assertTrue($real_tokens[10] == '');
		self::assertTrue($real_tokens[11] == $begin_tokens[5]);
		self::assertTrue(!isset($real_tokens[12]));

		// Beginning token array and numeric token array
		$begin_tokens = array
		(
			0 => 'container',
			1 => 'innerHTML',
			2 => 'div',
			3 => 'id',
			4 => 'yo',
			5 => 'mama',
			6 => 'eqq',
			7 => 'que',
			8 => 'whatthe',
			9 => 'hah',
			10 => 'iron',
		);
		$begin_numeric_tokens = array
		(
			0,
			1,
			2,
			3,
			4,
			5,
			6,
			7,
			8,
			9,
			10,
			11,
			12
		);

		// Get actual results of merging and verify them
		$real_tokens = $begin_tokens;
		$real_numeric_tokens = $begin_numeric_tokens;
		phpJSO_merge_token_arrays($real_tokens, $real_numeric_tokens);
		self::assertTrue($real_tokens[0] == '');
		self::assertTrue($real_tokens[1] == '');
		self::assertTrue($real_tokens[2] == '');
		self::assertTrue($real_tokens[3] == '');
		self::assertTrue($real_tokens[4] == '');
		self::assertTrue($real_tokens[5] == '');
		self::assertTrue($real_tokens[6] == '');
		self::assertTrue($real_tokens[7] == '');
		self::assertTrue($real_tokens[8] == '');
		self::assertTrue($real_tokens[9] == '');
		self::assertTrue($real_tokens[10] == '');
		self::assertTrue($real_tokens[11] == '');
		self::assertTrue($real_tokens[12] == '');
		self::assertTrue($real_tokens[13] == $begin_tokens[0]);
		self::assertTrue($real_tokens[14] == $begin_tokens[1]);
		self::assertTrue($real_tokens[15] == $begin_tokens[2]);
		self::assertTrue($real_tokens[16] == $begin_tokens[3]);
		self::assertTrue($real_tokens[17] == $begin_tokens[4]);
		self::assertTrue($real_tokens[18] == $begin_tokens[5]);
		self::assertTrue($real_tokens[19] == $begin_tokens[6]);
	}

	/**
	 * Test function that inserts tokens into the token array
	 */
	public function test_insert_token ()
	{
		$token_array = array();

		$token_array[] = 'test1';
		$token_array[] = 'test2';
		
		phpJSO_insert_token($token_array, 'test3', 0);
		self::assertTrue($token_array[0] == 'test3');
		self::assertTrue($token_array[1] == 'test1');
		self::assertTrue($token_array[2] == 'test2');
		
		phpJSO_insert_token($token_array, 'test4', 2);
		self::assertTrue($token_array[0] == 'test3');
		self::assertTrue($token_array[1] == 'test1');
		self::assertTrue($token_array[2] == 'test4');
		self::assertTrue($token_array[3] == 'test2');
		
		phpJSO_insert_token($token_array, 'test5', 1);
		self::assertTrue($token_array[0] == 'test3');
		self::assertTrue($token_array[1] == 'test5');
		self::assertTrue($token_array[2] == 'test1');
		self::assertTrue($token_array[3] == 'test4');
		self::assertTrue($token_array[4] == 'test2');
	}

	/**
	 * Test the function that restores all removed strings
	 * back into the code.
	 */
	public function test_restore_strings ()
	{
		// This is what we'll work from
		$strings = array('"test1"', "'str2'", "'s1'", "'s2'", "'s3'", '"s4"');
		$start_code = 'var test = `5` + `1`; alert(`2`,`3`,`4`,`0`);';

		// Get expected result
		$expected_result = 'var test = "s4" + \'str2\'; alert(\'s1\',\'s2\',\'s3\',"test1");';
		
		// Get actual result
		$real_result = $start_code;
		phpJSO_restore_strings($real_result, $strings);

		// Make sure everything is ok....
		if ($real_result != $expected_result)
		{
			print("$real_result\n-----\n$expected_result");
		}
		self::assertTrue($real_result == $expected_result);
	}

	/**
	 * And finally, check the dupe counter.
	 */
	public function test_dupe_counter ()
	{
		// Get the token array to count for dupes
		$dupe_array = array
		(
			'webddm_',
			'webddm',
			'webddm',
			'webddm_',
			'webddm',
			'webddm_',
			'webddm_',
			'_webddm',
			'webddm',
			'_webddm',
			'webddm',
			'webddm',
			'webddm_',
			'webddm',
			'_webddm',
			'webddm',
			'webddm_',
			'_webddm'
		);

		// Expected array to be returned
		$expected_dupe_counts = array
		(
			'webddm' => 8,
			'_webddm' => 4,
			'webddm_' => 6
		);
		
		// Get actual result
		$actual_dupe_counts = array();
		phpJSO_count_duplicates($actual_dupe_counts, $dupe_array);

		// Check if results are okay!
		self::assertTrue(count($actual_dupe_counts) == 3);
		self::assertTrue($actual_dupe_counts['webddm'] == $expected_dupe_counts['webddm']);
		self::assertTrue($actual_dupe_counts['_webddm'] == $expected_dupe_counts['_webddm']);
		self::assertTrue($actual_dupe_counts['webddm_'] == $expected_dupe_counts['webddm_']);
	}

	/**
	 * Test parenthetical error-checking
	 */
	public function test_parenthetical_testing ()
	{
		self::assertTrue(phpJSO_is_valid_parenthetical('()') == '()');
		self::assertTrue(phpJSO_is_valid_parenthetical('(())') == '(())');
		self::assertTrue(phpJSO_is_valid_parenthetical('((()))') == '((()))');
		self::assertTrue(phpJSO_is_valid_parenthetical(')(') == false);
		self::assertTrue(phpJSO_is_valid_parenthetical('())') == '()');
		self::assertTrue(phpJSO_is_valid_parenthetical('(()') == false);
		self::assertTrue(phpJSO_is_valid_parenthetical(')') == false);
		self::assertTrue(phpJSO_is_valid_parenthetical('(') == false);
	}

	/**
	 * Test block collapsing
	 *
	 * EG:
	 *    if(1){alert(1);}
	 * becomes
	 *    if(1)alert(1);
	 */
	public function test_block_collapsing ()
	{
		$b = 0;

		self::assertTrue(phpJSO_collapse_blocks('if(1){alert(1)}', $b) == 'if(1)alert(1);');
		self::assertTrue(phpJSO_collapse_blocks('if(1){alert(1);}', $b) == 'if(1)alert(1);');
		self::assertTrue(phpJSO_collapse_blocks('if(1)alert(1);', $b) == 'if(1)alert(1);');
		self::assertTrue(phpJSO_collapse_blocks('if(1){alert(1);alert(2);}', $b) == 'if(1){alert(1);alert(2);}');
		self::assertTrue(phpJSO_collapse_blocks('if((1)&&test(123)){alert(3)}', $b) == 'if((1)&&test(123))alert(3);');

		// Functions should not be collapsed (yep, Javascript is inconsistent)
		self::assertTrue(phpJSO_collapse_blocks('function abc(a,b,c){alert(3);}', $b) == 'function abc(a,b,c){alert(3);}');

		// Test blocks with no parentheticals
		self::assertTrue(phpJSO_collapse_blocks('if(a){alert(1);}else{alert(0);}', $b) == 'if(a)alert(1);else alert(0);');
		self::assertTrue(phpJSO_collapse_blocks('if(a){alert(1)}else{alert(0)}', $b) == 'if(a)alert(1);else alert(0);');
		self::assertTrue(phpJSO_collapse_blocks('if(a){alert(1);}else{alert(0)}', $b) == 'if(a)alert(1);else alert(0);');
		self::assertTrue(phpJSO_collapse_blocks('if(a){alert(1)}else{alert(0);}', $b) == 'if(a)alert(1);else alert(0);');
		self::assertTrue(phpJSO_collapse_blocks('do{alert(1)}while(2);', $b) == 'do alert(1);while(2);');

		// Testing collapsing nested blocks inside of blocks with no parentheticals
		self::assertTrue(phpJSO_collapse_blocks('else{function(a,b,c){alert(1);alert(2);}}', $b) == 'else function(a,b,c){alert(1);alert(2);};');

		// Testing collapsing nested blocks
		self::assertTrue(phpJSO_collapse_blocks('if(1){if(2){alert(1);alert(2);}}', $b) == 'if(1)if(2){alert(1);alert(2);}');
		self::assertTrue(phpJSO_collapse_blocks('if(1){if(2){alert(3);}}', $b) == 'if(1)if(2)alert(3);');
		self::assertTrue(phpJSO_collapse_blocks('if(1){if(2){alert(3)}}', $b) == 'if(1)if(2)alert(3);');
		self::assertTrue(phpJSO_collapse_blocks('else{if(2){alert(3);}}', $b) == 'else if(2)alert(3);');
		self::assertTrue(phpJSO_collapse_blocks('else{if(2){alert(3)}}', $b) == 'else if(2)alert(3);');
		self::assertTrue(phpJSO_collapse_blocks('if(1){function abc(a,b,c){alert(3)}}', $b) == 'if(1)function abc(a,b,c){alert(3)}');
		self::assertTrue(phpJSO_collapse_blocks('if((1)){function abc(a,b,c){alert(3)}}', $b) == 'if((1))function abc(a,b,c){alert(3)}');
		self::assertTrue(phpJSO_collapse_blocks('else{function abc(a,b,c){alert(3)}}', $b) == 'else function abc(a,b,c){alert(3)}');
		self::assertTrue(phpJSO_collapse_blocks('else{function abc(a,b,c){alert(3)}}', $b) == 'else function abc(a,b,c){alert(3)}');
		self::assertTrue(phpJSO_collapse_blocks('if(1){for(var i = 0; i < 5; i++){alert(3)}}', $b) == 'if(1)for(var i = 0; i < 5; i++)alert(3);');
		self::assertTrue(phpJSO_collapse_blocks('if((1)&&test(123)){function abc(a,b,c){alert(3)}}', $b) == 'if((1)&&test(123))function abc(a,b,c){alert(3)}');
		self::assertTrue(phpJSO_collapse_blocks('if(1){var eq=function(a){alert(1);};}', $b) == 'if(1)var eq=function(a){alert(1);};');
		self::assertTrue(phpJSO_collapse_blocks('else{var eq=function(a){alert(1);};}', $b) == 'else var eq=function(a){alert(1);};');

		// Make sure that not ALL blocks are collapsed...
		self::assertTrue(phpJSO_collapse_blocks('if(1){alert(1);return}', $b) == 'if(1){alert(1);return}');
		self::assertTrue(phpJSO_collapse_blocks('if(1){if(2)alert(3);return}', $b) == 'if(1){if(2)alert(3);return}');
		self::assertTrue(phpJSO_collapse_blocks('else{if(2)alert(3);return}', $b) == 'else{if(2)alert(3);return}');
		self::assertTrue(phpJSO_collapse_blocks('else{if(2)alert(3);if(3){alert(3)}}', $b) == 'else{if(2)alert(3);if(3)alert(3);}');
		self::assertTrue(phpJSO_collapse_blocks('if(1){if(2)alert(3);if(3){alert(3)}}', $b) == 'if(1){if(2)alert(3);if(3)alert(3);}');
		self::assertTrue(phpJSO_collapse_blocks('else{if(2)alert(3);if(3)alert(3)}', $b) == 'else{if(2)alert(3);if(3)alert(3)}');
		self::assertTrue(phpJSO_collapse_blocks('if(1){if(2)alert(3);if(3)alert(3)}', $b) == 'if(1){if(2)alert(3);if(3)alert(3)}');
		self::assertTrue(phpJSO_collapse_blocks('else{if(2)alert(3);if(3)alert(3);}', $b) == 'else{if(2)alert(3);if(3)alert(3);}');
		self::assertTrue(phpJSO_collapse_blocks('if(1){if(2)alert(3);if(3)alert(3);}', $b) == 'if(1){if(2)alert(3);if(3)alert(3);}');

		// Make sure that blocks aren't mixed up
		self::assertTrue(phpJSO_collapse_blocks('if(1){if(2)alert(3);else alert(4);}', $b) == 'if(1){if(2)alert(3);else alert(4);}');
		self::assertTrue(phpJSO_collapse_blocks('if(1){if(2){alert(3);}}else alert(4);', $b) == 'if(1){if(2)alert(3);}else alert(4);');
		self::assertTrue(phpJSO_collapse_blocks('if(1){if(2)alert(3);}else alert(4);', $b) == 'if(1){if(2)alert(3);}else alert(4);');
		self::assertTrue(phpJSO_collapse_blocks('if(1){while(1){alert(1)};}else alert(4);', $b) == 'if(1){while(1)alert(1);}else alert(4);');
		self::assertTrue(phpJSO_collapse_blocks('if(1){while(1){_32+=obj.offsetLeft;obj=obj.offsetParent;};}else{alert(4);}', $b) == 'if(1){while(1){_32+=obj.offsetLeft;obj=obj.offsetParent;};}else alert(4);');
		self::assertTrue(phpJSO_collapse_blocks('if(1){while(1){_32+=obj.offsetLeft;obj=obj.offsetParent}}else{alert(4)}', $b) == 'if(1){while(1){_32+=obj.offsetLeft;obj=obj.offsetParent}}else alert(4);');
	}

	/**
	 * Test math collapsing.
	 */
	public function test_math_collapsing ()
	{
		$collapsed = 0;

		self::assertTrue(phpJSO_collapse_math('1+1', $collapsed) == '2');
		self::assertTrue(phpJSO_collapse_math('1-1', $collapsed) == '0');
		
		self::assertTrue(phpJSO_collapse_math('1+5', $collapsed) == '6');
		self::assertTrue(phpJSO_collapse_math('1+-10', $collapsed) == '-9');

		self::assertTrue(phpJSO_collapse_math('1-5', $collapsed) == '-4');

		self::assertTrue(phpJSO_collapse_math('10/2', $collapsed) == '5');
		self::assertTrue(phpJSO_collapse_math('10/-2', $collapsed) == '-5');

		self::assertTrue(phpJSO_collapse_math('10*2', $collapsed) == '20');
		self::assertTrue(phpJSO_collapse_math('10*-2', $collapsed) == '-20');

		self::assertTrue(phpJSO_collapse_math('10%2', $collapsed) == '0');
		self::assertTrue(phpJSO_collapse_math('10%2', $collapsed) == '0');

		self::assertTrue(phpJSO_collapse_math('11%-2', $collapsed) == '1');
		self::assertTrue(phpJSO_collapse_math('11%-2', $collapsed) == '1');

		self::assertTrue(phpJSO_collapse_math('(10+5)-2', $collapsed) == '13');

		self::assertTrue(phpJSO_collapse_math('(10)', $collapsed) == '10');
		self::assertTrue(phpJSO_collapse_math('(10', $collapsed) == '(10');
		self::assertTrue(phpJSO_collapse_math('10)', $collapsed) == '10)');
		self::assertTrue(phpJSO_collapse_math('()', $collapsed) == '()');
		self::assertTrue(phpJSO_collapse_math('()-1', $collapsed) == '()-1');
		self::assertTrue(phpJSO_collapse_math('+-', $collapsed) == '+-');

		// Test collapsing hex
		self::assertTrue(phpJSO_collapse_math('0x00000000', $collapsed) == '0');
		self::assertTrue(phpJSO_collapse_math('0x0000000a', $collapsed) == '10');
		self::assertTrue(phpJSO_collapse_math('0x000000aa', $collapsed) == '170');
		self::assertTrue(phpJSO_collapse_math('0x0000a0a0', $collapsed) == '41120');
		self::assertTrue(phpJSO_collapse_math('0xf000a0a0', $collapsed) == '4026572960');
		self::assertTrue(phpJSO_collapse_math('0x0000000a+0x0000000a', $collapsed) == '20');
		self::assertTrue(phpJSO_collapse_math('0xffffffff', $collapsed) == '4294967295');
		self::assertTrue(phpJSO_collapse_math('0xffffffffff', $collapsed) == '2147483647');

		// Test potential parse errors
		self::assertTrue(phpJSO_collapse_math('_16+15', $collapsed) == '_16+15');
	}

	/**
	 * Tests actual replacement of tokens with token identifiers in the code.
	 */
	public function test_token_replacement ()
	{
		// Code that will be modified
		$begin_code = "function Hash()"
			. "{"
				. "this.length=0;"
				. "this.numericLength=0;"
				. "this.elementData=[];"
				. "if(arguments[0]&&arguments[0].elementData)"
				. "{"
					. "this.length=arguments[0].length;"
					. "this.numericLength=arguments[0].numericLength;"
					. "this.elementData=arguments[0].elementData;"
					. "return"
				. "}"
				. "if(typeof(arguments[0])=='object')"
				. "{"
					. "for (var i in arguments[0])"
					. "{"
						. "this.set(i,arguments[0][i])"
					. "}"
					. "return"
				. "}"
				. "for(var i=0;i<arguments.length;i+=2)"
				. "{"
					. "if (typeof(arguments[i+1])!='undefined')"
					. "{"
						. "this.set(arguments[i],arguments[i+1])"
					. "}"
				. "}"
			. "}";

		// Array of tokens in this code
		$tokens = array
		(
			0 => '',
			1 => '',
			2 => '',
			3 => 'this',
			4 => 'length',
			5 => 'numericLength',
			6 => 'elementData',
			7 => 'if',
			8 => 'arguments',
			9 => 'return',
			10 => 'typeof',
			11 => 'for',
			12 => 'var',
			13 => 'set'
		);
		
		// Expected result
		$expected_result = 'function Hash(){3.4=0;3.5=0;3.6=[];7(8[0]&&8[0].6){3.4=8[0].4;3.5=8[0].5;3.6=8[0].6;9}7(10(8[0])==\'object\'){11 (12 i in 8[0]){3.13(i,8[0][i])}9}11(12 i=0;i<8.4;i+=2){7 (10(8[i+1])!=\'undefined\'){3.13(8[i],8[i+1])}}}';

		// Get real results and verify them
		$real_result = $begin_code;
		phpJSO_replace_tokens($tokens, $real_result);
		if ($real_result != $expected_result)
		{
			print("\n$real_result\n-----\n$expected_result\n");
		}
		self::assertTrue($expected_result == $real_result);
	}

	/**
	 * Test compression... indirectly tests all methods
	 */
	function test_all_compression ()
	{
		$begin_code = "/* asdf test \" '*/function/*'*/Hash()"
			. "{"
				. "this.length = 0;\n"
				. "this.numericLength = 0 ; \r\n"
				. "this.elementData = [ ] ; \r\n\n\n"
				. "if(arguments\t[\n0\t] &&\t\n\r\narguments\t[\t0\t]\t.\telementData)"
				. "{"
					. "this.length\t=\narguments[0].length;\t\n\n\n\n\r\n\n\n\n"
					. "this.numericLength=arguments[0].numericLength;\t\t\t\t\n"
					. "\n\t    this.elementData=arguments[0].elementData;"
					. "return"
				. "} // \n"
				. "if(typeof(arguments[0])=='object')"
				. "{"
					. "\t\tfor (var\ti\tin\targuments[0])"
					. "\t\t{"
						. "\t\t\tthis.set(i,arguments[0][i])"
					. "\t\t}"
					. "\t\treturn"
				. "\t}"
				. "\t\t\t\t\tfor(var i=0;i<arguments.length;i+=2)"
				. "{"
					. "if (typeof(arguments[i+1])!='undefined')"
					. "{"
						. "this.set(arguments[i],arguments[i+1])"
					. "}"
				. "}"
			. "}";

		$expected_code = 'eval(function(a,b,c,d){while(b--)if(c[b])a=a.replace(new RegExp(d+b+d,\'g\'),c[b]);return a}("function Hash(){3.4=0;3.5=0;3.6=[];7(8[0]&&8[0].6){3.4=8[0].4;3.5=8[0].5;3.6=8[0].6;9}7(10(8[0])==\'object\'){11(12 i in 8[0])3.13(i,8[0][i]);9}11(12 i=0;i<8.4;i+=2)7(10(8[i+1])!=\'undefined\')3.13(8[i],8[i+1])}",14,\'|||this|length|numericLength|elementData|if|arguments|return|typeof|for|var|set\'.split(\'|\'),\'\\\\b\'))';

		self::assertTrue(phpJSO_compress($begin_code, $messages, '1', false, true, false) == $expected_code);
	}

	/**
	 * Test bug reported by Jack Wu
	 */
	function test_bug1 ()
	{
		$begin_code = "
			var DATA_TYPE_OHLC    = 1;
			var DATA_TYPE_LINEAR  = 2;
			var DATA_TYPE_HYBRID  = 3;
			var DATA_TYPE_X_LABEL = 4;

			var DATA_TIME_FRAME_1M	= 1;
			var DATA_TIME_FRAME_2M	= 2;
			var DATA_TIME_FRAME_5M	= 3;
			var DATA_TIME_FRAME_15M	= 4;
			var DATA_TIME_FRAME_1H	= 5;
			var DATA_TIME_FRAME_1D	= 6;
			var DATA_TIME_FRAME_5D	= 7;
			var DATA_TIME_FRAME_1O	= 8;
			var DATA_TIME_FRAME_3O	= 9;
			var DATA_TIME_FRAME_6O	= 10;
			var DATA_TIME_FRAME_1Y	= 11;
			var DATA_TIME_FRAME_5Y	= 12;

			var DATA_CANDLE_PIXELWIDTH 		= 6;
			var DATA_CANDLE_XEDGE_SPACING 	= 2;
			var DATA_LINE_PIXELWIDTH 	= 1;
			var DATA_LINE_XEDGE_SPACING = 0;//9 for candles, 0 for lines
			var DATA_PRECISION			= 2;

			var DATA_TYPE_OHLC    = 1;
			var DATA_TYPE_LINEAR  = 2;
			var DATA_TYPE_HYBRID  = 3;
			var DATA_TYPE_X_LABEL = 4;

			var DATA_TIME_FRAME_1M	= 1;
			var DATA_TIME_FRAME_2M	= 2;
			var DATA_TIME_FRAME_5M	= 3;
			var DATA_TIME_FRAME_15M	= 4;
			var DATA_TIME_FRAME_1H	= 5;
			var DATA_TIME_FRAME_1D	= 6;
			var DATA_TIME_FRAME_5D	= 7;
			var DATA_TIME_FRAME_1O	= 8;
			var DATA_TIME_FRAME_3O	= 9;
			var DATA_TIME_FRAME_6O	= 10;
			var DATA_TIME_FRAME_1Y	= 11;
			var DATA_TIME_FRAME_5Y	= 12;

			var DATA_CANDLE_PIXELWIDTH 		= 6;
			var DATA_CANDLE_XEDGE_SPACING 	= 2;
			var DATA_LINE_PIXELWIDTH 	= 1;
			var DATA_LINE_XEDGE_SPACING = 0;//9 for candles, 0 for lines
			var DATA_PRECISION			= 5%DATA_LINE_XEDGE_SPACING;
		";

		$expected_code = "eval(function(a,b,c,d){while(b--)if(c[b])a=a.replace(new RegExp(d+b+d,'g'),c[b]);return a}('13 14=1;13 15=2;13 16=3;13 17=4;13 18=1;13 19=2;13 20=3;13 21=4;13 22=5;13 23=6;13 24=7;13 25=8;13 26=9;13 27=10;13 28=11;13 29=12;13 30=6;13 31=2;13 32=1;13 33=0;13 34=2;13 14=1;13 15=2;13 16=3;13 17=4;13 18=1;13 19=2;13 20=3;13 21=4;13 22=5;13 23=6;13 24=7;13 25=8;13 26=9;13 27=10;13 28=11;13 29=12;13 30=6;13 31=2;13 32=1;13 33=0;13 34=5% 33',35,'|||||||||||||var|DATA_TYPE_OHLC|DATA_TYPE_LINEAR|DATA_TYPE_HYBRID|DATA_TYPE_X_LABEL|DATA_TIME_FRAME_1M|DATA_TIME_FRAME_2M|DATA_TIME_FRAME_5M|DATA_TIME_FRAME_15M|DATA_TIME_FRAME_1H|DATA_TIME_FRAME_1D|DATA_TIME_FRAME_5D|DATA_TIME_FRAME_1O|DATA_TIME_FRAME_3O|DATA_TIME_FRAME_6O|DATA_TIME_FRAME_1Y|DATA_TIME_FRAME_5Y|DATA_CANDLE_PIXELWIDTH|DATA_CANDLE_XEDGE_SPACING|DATA_LINE_PIXELWIDTH|DATA_LINE_XEDGE_SPACING|DATA_PRECISION'.split('|'),'\\\\b'))";

		self::assertTrue(phpJSO_compress($begin_code, $messages, '1', false, true, false) == $expected_code);
	}

	/**
	 * Test bug reported by Minuro Toda, has to do with hex numbers
	 */
	function test_bug2 ()
	{
		$begin_code = "new Array(0x00000000, 0xd76aa478, 0xe8c7b756, 0x242070db);";

		$expected_code = "new Array(0,3614090360,3905402710,606105819)";

		self::assertTrue(phpJSO_compress($begin_code, $messages, '1', false, false, true) == $expected_code);
	}
};
?>
