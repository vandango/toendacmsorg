<?php /* _\|/_
         (o o)
+-----oOO-{_}-OOo--------------------------------------------------------+
| toendaCMS - Content Management and Weblogging System with XML and SQL  |
+------------------------------------------------------------------------+
| Copyright (c) Toenda Software Development                              |
| Author: Jonathan Naumann                                               |
+------------------------------------------------------------------------+
|
| toendaCMS Version
|
| File:	tcms_version.lib.php
|
+
*/


defined('_TCMS_VALID') or die('Restricted access');


/**
 * toendaCMS Version
 *
 * This class is used to provide the cms
 * version information.
 *
 * @version 0.1.0
 * @author	Jonathan Naumann <jonathan@toenda.com>
 * @package toendaCMS
 * @subpackage tcms_kernel
 *
 * <code>
 * 
 * Methods
 *
 * __construct                 -> PHP5 Constructor
 * tcms_version                -> PHP4 Constructor
 * __destruct                  -> PHP5 Destructor
 * _tcms_version               -> PHP4 Destructor
 *
 * getName                     -> Get the name of toendaCMS
 * getTagline                  -> Get the tagline of toendaCMS
 * getCodename                 -> Get the codeline of toendaCMS
 * getVersion                  -> Get the version of toendaCMS
 * getBuild                    -> Get the build of toendaCMS
 * getState                    -> Get the state of toendaCMS
 * getReleaseDate              -> Get the release date of toendaCMS
 * getToendaCopyright          -> Get the toenda copyright of toendaCMS
 * 
 * </code>
 * 
 */


class tcms_version {
	private $m_name;
	private $m_tagline;
	private $m_codename;
	private $m_version;
	private $m_build;
	private $m_status;
	private $m_release_date;
	private $m_toenda_copy;
	
	
	/**
	 * PHP5 Constructor
	 *
	 * @param String $relativePath = ''
	 */
	function __construct($relativePath = '') {
		$this->o_xml = simplexml_load_file($relativePath.'engine/tcms_kernel/tcms_version.xml');
		
		$this->m_name         = $this->o_xml->name;
		$this->m_tagline      = $this->o_xml->tagline;
		$this->m_codename     = $this->o_xml->codename;
		$this->m_version      = $this->o_xml->release;
		$this->m_build        = $this->o_xml->build;
		$this->m_status       = $this->o_xml->status;
		$this->m_release_date = $this->o_xml->release_date;
		$this->m_toenda_copy  = $this->o_xml->toenda_copyright;
	}
	
	
	
	/**
	 * PHP4 Constructor
	 *
	 * @param String $relativePath = ''
	 */
	function tcms_version($relativePath = ''){
		$this->__construct($relativePath);
	}
	
	
	
	/**
	 * PHP5 Destructor
	 */
	function __destruct(){
	}
	
	
	
	/**
	 * PHP4 Destructor
	 */
	function _tcms_version(){
		$this->__destruct();
	}
	
	
	
	/**
	 * Get the name of toendaCMS
	 *
	 * @return String
	 */
	function getName(){
		return $this->m_name;
	}
	
	
	
	/**
	 * Get the tagline of toendaCMS
	 *
	 * @return String
	 */
	function getTagline(){
		return $this->m_tagline;
	}
	
	
	
	/**
	 * Get the codename of toendaCMS
	 *
	 * @return String
	 */
	function getCodename(){
		return $this->m_codename;
	}
	
	
	
	/**
	 * Get the version of toendaCMS
	 *
	 * @return String
	 */
	function getVersion(){
		return $this->m_version;
	}
	
	
	
	/**
	 * Get the build of toendaCMS
	 *
	 * @return String
	 */
	function getBuild(){
		return $this->m_build;
	}
	
	
	
	/**
	 * Get the state of toendaCMS
	 *
	 * @return String
	 */
	function getState(){
		return $this->m_status;
	}
	
	
	
	/**
	 * Get the release date of toendaCMS
	 *
	 * @return String
	 */
	function getReleaseDate(){
		return $this->m_release_date;
	}
	
	
	
	/**
	 * Get the toenda copyright of toendaCMS
	 *
	 * @return String
	 */
	function getToendaCopyright(){
		return $this->m_toenda_copy;
	}
}
