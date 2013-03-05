<?php

namespace Riak;

/**
 * Private class used to accumulate a CURL response.
 * @package Riak\StringIO
 */
class StringIO {
	function __construct() {
		$this->contents = '';
	}

	function write($ch, $data) {
		$this->contents .= $data;
		return strlen($data);
	}

	function contents() {
		return $this->contents;
	}
}