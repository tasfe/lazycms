<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL               LLLL  |
 * | LL                            LL   L  LLL   LL  LL   L             LL  LL |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL         LL  LL      LL |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL      LL  LL     LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL     LL  LL    LL   |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL      LLLL    LL    |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL      LLLL   LL     |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL        LL    LLLLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');
/**
 * 语言包本地化类
 *
 * 修改自WordPress L10n类
 *
 * @author Lukin <my@lukin.cn>
 */
class L10n{
	var $_pos;
	var $_str;
	var $endian  = 'little';
	var $entries = array();
	var $headers = array();


    function L10n() {
        $args = func_get_args();
		return call_user_func_array( array(&$this, '__construct'), $args );
	}

    function __construct($str = ''){
		$this->_str = $str;
		$this->_pos = 0;
		if ($this->_str) $this->load_tables();
	}

	function load_file($file){
		$this->_str = file_get_contents($file);
		if ($this->_str) $this->load_tables();
	}

	function read($bytes) {
		$data = mb_substr($this->_str, $this->_pos, $bytes);
		$this->_pos += $bytes;
		if (mb_strlen($this->_str) < $this->_pos) $this->_pos = mb_strlen($this->_str);
		return $data;
	}

	function seekto($pos) {
		$this->_pos = $pos;
		if (mb_strlen($this->_str) < $this->_pos) $this->_pos = mb_strlen($this->_str);
		return $this->_pos;
	}

	function set_header($header, $value) {
		$this->headers[$header] = $value;
	}

	function set_headers($headers) {
		foreach($headers as $header => $value) {
			$this->set_header($header, $value);
		}
	}

	function get_header($header) {
		return isset($this->headers[$header])? $this->headers[$header] : false;
	}

	function make_headers($translation) {
		$headers = array();
		// sometimes \ns are used instead of real new lines
		$translation = str_replace('\n', "\n", $translation);
		$lines = explode("\n", $translation);
		foreach($lines as $line) {
			$parts = explode(':', $line, 2);
			if (!isset($parts[1])) continue;
			$headers[trim($parts[0])] = trim($parts[1]);
		}
		return $headers;
	}
	/**
	 * Add entry to the PO structure
	 *
	 * @param object &$entry
	 * @return bool true on success, false if the entry doesn't have a key
	 */
	function add_entry($entry) {
		if (is_array($entry)) {
			$key = $this->entry_key($entry);
		} else {
			$key = false;
		}
		if (false === $key) return false;
		$this->entries[$key] = $entry;
		return true;
	}
	/**
	 * @static
	 */
	function make_entry($original, $translation) {
		$args = array();
		// look for context
		$parts = explode(chr(4), $original);
		if (isset($parts[1])) {
			$original = $parts[1];
			$args['context'] = $parts[0];
		}
		// look for plural original
		$parts = explode(chr(0), $original);
		$args['singular'] = $parts[0];
		if (isset($parts[1])) {
			$args['plural'] = $parts[1];
		}
		// plural translations are also separated by \0
		$args['translations'] = explode(chr(0), $translation);
		return $this->entry($args);
	}

	/**
	 * @param array $args associative array, support following keys:
	 * 	- singular (string) -- the string to translate, if omitted and empty entry will be created
	 * 	- plural (string) -- the plural form of the string, setting this will set {@link $is_plural} to true
	 * 	- translations (array) -- translations of the string and possibly -- its plural forms
	 * 	- context (string) -- a string differentiating two equal strings used in different contexts
	 * 	- translator_comments (string) -- comments left by translators
	 * 	- extracted_comments (string) -- comments left by developers
	 * 	- references (array) -- places in the code this strings is used, in relative_to_root_path/file.php:linenum form
	 * 	- flags (array) -- flags like php-format
	 */
	function entry($args=array()) {
		// if no singular -- empty object
		if (!isset($args['singular'])) {
			return;
		}
		$entry = array(
			'is_plural' => false,
			'context' => null,
			'singular' => null,
			'plural' => null,
			'translations' => array(),
			'translator_comments' => '',
			'extracted_comments' => '',
			'references' => array(),
			'flags' => array(),
		);
		// get member variable values from args hash
		foreach ($args as $varname => $value) {
			$entry[$varname] = $value;
		}
		if (isset($args['plural'])) $entry['is_plural'] = true;
		if (!is_array($entry['translations'])) $entry['translations'] = array();
		if (!is_array($entry['references'])) $entry['references'] = array();
		if (!is_array($entry['flags'])) $entry['flags'] = array();
		return $entry;
	}

	function entry_key($entry) {
		if (is_null($entry['singular'])) return false;
		// prepend context and EOT, like in MO files
		return is_null($entry['context'])? $entry['singular'] : $entry['context'].chr(4).$entry['singular'];
	}

	function read_int() {
		$bytes = $this->read(4);
		if (4 != mb_strlen($bytes))
			return false;
		$endian_letter = ('big' == $this->endian)? 'N' : 'V';
		$int = unpack($endian_letter, $bytes);
		return array_shift($int);
	}

	function read_int_array($count) {
		$bytes = $this->read(4 * $count);
		if (4*$count != mb_strlen($bytes))
			return false;
		$endian_letter = ('big' == $this->endian)? 'N' : 'V';
		return unpack($endian_letter.$count, $bytes);
	}

	function get_byteorder($magic) {

		// The magic is 0x950412de

		// bug in PHP 5.0.2, see https://savannah.nongnu.org/bugs/?func=detailitem&item_id=10565
		$magic_little = (int) - 1794895138;
		$magic_little_64 = (int) 2500072158;
		// 0xde120495
		$magic_big = ((int) - 569244523) && 0xFFFFFFFF;

		if ($magic_little == $magic || $magic_little_64 == $magic) {
			return 'little';
		} else if ($magic_big == $magic) {
			return 'big';
		} else {
			return false;
		}
	}

	function load_tables(){
		$endian = $this->get_byteorder($this->read_int());
		if (false === $endian) {
			return false;
		}
		$this->endian = $endian;
		$revision = $this->read_int();
		$total = $this->read_int();

		// get addresses of array of lenghts and offsets for original string and translations
		$originals_lenghts_addr = $this->read_int();
		$translations_lenghts_addr = $this->read_int();

		$this->seekto($originals_lenghts_addr);
		$originals_lenghts = $this->read_int_array($total * 2); // each of
		$this->seekto($translations_lenghts_addr);
		$translations_lenghts = $this->read_int_array($total * 2);

		$length = create_function('$i', 'return $i * 2 + 1;');
		$offset = create_function('$i', 'return $i * 2 + 2;');

		for ($i = 0; $i < $total; ++$i) {
			$this->seekto($originals_lenghts[$offset($i)]);
			$original = $this->read($originals_lenghts[$length($i)]);
			$this->seekto($translations_lenghts[$offset($i)]);
			$translation = $this->read($translations_lenghts[$length($i)]);
			if ('' == $original) {
				$this->set_headers($this->make_headers($translation));
			} else {
				$this->add_entry($this->make_entry($original, $translation));
			}
		}
		return true;
	}

	function translate($singular, $context=null) {
		$entry = $this->entry(array('singular' => $singular, 'context' => $context));
		$translated = $this->translate_entry($entry);
		return ($translated && !empty($translated['translations']))? $translated['translations'][0] : $singular;
	}

	function translate_entry(&$entry) {
		$key = $this->entry_key($entry);
		return isset($this->entries[$key])? $this->entries[$key] : false;
	}
}
