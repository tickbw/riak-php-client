<?php

namespace Riak;

class Client {
	
	/**
	 * Construct a new Riak\Client object.
	 *
	 * @param string $host
	 *        	- Hostname or IP address (default '127.0.0.1')
	 * @param int $port
	 *        	- Port number (default 8098)
	 * @param string $prefix
	 *        	- Interface prefix (default "riak")
	 * @param string $mapred_prefix
	 *        	- MapReduce prefix (default "mapred")
	 */
	function __construct($host = '127.0.0.1', $port = 8098, $prefix = 'riak', $mapred_prefix = 'mapred') {
		$this->host = $host;
		$this->port = $port;
		$this->prefix = $prefix;
		$this->mapred_prefix = $mapred_prefix;
		$this->indexPrefix = 'buckets';
		$this->clientid = 'php_' . base_convert ( mt_rand (), 10, 36 );
		$this->r = 2;
		$this->w = 2;
		$this->dw = 2;
	}
	
	/**
	 * Get the R-value setting for this Riak\Client.
	 * (default 2)
	 *
	 * @return integer
	 */
	function getR() {
		return $this->r;
	}
	
	/**
	 * Set the R-value for this Riak\Client.
	 * This value will be used
	 * for any calls to get(...) or getBinary(...) where where 1) no
	 * R-value is specified in the method call and 2) no R-value has
	 * been set in the Riak\Bucket.
	 *
	 * @param integer $r
	 *        	- The R value.
	 * @return $this
	 */
	function setR($r) {
		$this->r = $r;
		return $this;
	}
	
	/**
	 * Get the W-value setting for this Riak\Client.
	 * (default 2)
	 *
	 * @return integer
	 */
	function getW() {
		return $this->w;
	}
	
	/**
	 * Set the W-value for this Riak\Client.
	 * See setR(...) for a
	 * description of how these values are used.
	 *
	 * @param integer $w
	 *        	- The W value.
	 * @return $this
	 */
	function setW($w) {
		$this->w = $w;
		return $this;
	}
	
	/**
	 * Get the DW-value for this ClientOBject.
	 * (default 2)
	 *
	 * @return integer
	 */
	function getDW() {
		return $this->dw;
	}
	
	/**
	 * Set the DW-value for this Riak\Client.
	 * See setR(...) for a
	 * description of how these values are used.
	 *
	 * @param integer $dw
	 *        	- The DW value.
	 * @return $this
	 */
	function setDW($dw) {
		$this->dw = $dw;
		return $this;
	}
	
	/**
	 * Get the clientID for this Riak\Client.
	 *
	 * @return string
	 */
	function getClientID() {
		return $this->clientid;
	}
	
	/**
	 * Set the clientID for this Riak\Client.
	 * Should not be called
	 * unless you know what you are doing.
	 *
	 * @param string $clientID
	 *        	- The new clientID.
	 * @return $this
	 */
	function setClientID($clientid) {
		$this->clientid = $clientid;
		return $this;
	}
	
	/**
	 * Get the bucket by the specified name.
	 * Since buckets always exist,
	 * this will always return a Riak\Bucket.
	 *
	 * @return Riak\Bucket
	 */
	function bucket($name) {
		return new Riak\Bucket ( $this, $name );
	}
	
	/**
	 * Get all buckets.
	 *
	 * @return array() of Riak\Bucket objects
	 */
	function buckets() {
		$url = Riak\Utils::buildRestPath ( $this );
		$response = Riak\Utils::httpRequest ( 'GET', $url . '?buckets=true' );
		$response_obj = json_decode ( $response [1] );
		$buckets = array ();
		foreach ( $response_obj->buckets as $name ) {
			$buckets [] = $this->bucket ( $name );
		}
		return $buckets;
	}
	
	/**
	 * Check if the Riak server for this RiakClient is alive.
	 *
	 * @return boolean
	 */
	function isAlive() {
		$url = 'http://' . $this->host . ':' . $this->port . '/ping';
		$response = Riak\Utils::httpRequest ( 'GET', $url );
		return ($response != NULL) && ($response [1] == 'OK');
	}
	
	// MAP/REDUCE/LINK FUNCTIONS
	
	/**
	 * Start assembling a Map/Reduce operation.
	 *
	 * @see Riak\MapReduce::add()
	 * @return Riak\MapReduce
	 */
	function add($params) {
		$mr = new Riak\MapReduce ( $this );
		$args = func_get_args ();
		return call_user_func_array ( array (
				&$mr,
				"add" 
		), $args );
	}
	
	/**
	 * Start assembling a Map/Reduce operation.
	 * This command will
	 * return an error unless executed against a Riak Search cluster.
	 *
	 * @see Riak\MapReduce::search()
	 * @return Riak\MapReduce
	 */
	function search($params) {
		$mr = new Riak\MapReduce ( $this );
		$args = func_get_args ();
		return call_user_func_array ( array (
				&$mr,
				"search" 
		), $args );
	}
	
	/**
	 * Start assembling a Map/Reduce operation.
	 *
	 * @see Riak\MapReduce::link()
	 */
	function link($params) {
		$mr = new Riak\MapReduce ( $this );
		$args = func_get_args ();
		return call_user_func_array ( array (
				&$mr,
				"link" 
		), $args );
	}
	
	/**
	 * Start assembling a Map/Reduce operation.
	 *
	 * @see Riak\MapReduce::map()
	 */
	function map($params) {
		$mr = new Riak\MapReduce ( $this );
		$args = func_get_args ();
		return call_user_func_array ( array (
				&$mr,
				"map" 
		), $args );
	}
	
	/**
	 * Start assembling a Map/Reduce operation.
	 *
	 * @see Riak\MapReduce::reduce()
	 */
	function reduce($params) {
		$mr = new Riak\MapReduce ( $this );
		$args = func_get_args ();
		return call_user_func_array ( array (
				&$mr,
				"reduce" 
		), $args );
	}
}
?>