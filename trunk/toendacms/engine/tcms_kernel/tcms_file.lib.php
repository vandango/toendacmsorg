<?php /* _\|/_
         (o o)
+-----oOO-{_}-OOo--------------------------------------------------------+
| toendaCMS - Content Management and Weblogging System with XML and SQL  |
+------------------------------------------------------------------------+
| Copyright (c) Toenda Software Development                              |
| Author: Jonathan Naumann                                               |
+------------------------------------------------------------------------+
|
| toendaCMS File Handling
|
| File:	tcms_file.lib.php
|
+
*/


defined('_TCMS_VALID') or die('Restricted access');


/**
 * toendaCMS File Handling
 *
 * This class is used to provide a small file
 * handler.
 *
 * @version 0.1.6
 * @author	Jonathan Naumann <jonathan@toenda.com>
 * @package toendaCMS
 * @subpackage tcms_kernel
 */


/**
 * Methods
 *
 * __construct                -> PHP5 Constructor
 * tcms_file                  -> PHP4 Constructor
 * __destruct                 -> PHP5 Destructor
 * _tcms_file                 -> PHP4 Destructor
 * 
 * isEOF                      -> Checks if the end of the file is reched
 * read                       -> Read
 * readLine                   -> Read a line from the active file
 * write                      -> Write
 * close                      -> Close
 * backup                     -> Backup
 * changeFile                 -> Change the active file
 * delete                     -> Close and delete the active file
 * deleteCustom               -> Delete a custom file
 *
 */


class tcms_file{
	private $m_file;
	private $m_openMode;
	private $m_fp;
	
	
	
	/**
	 * PHP5: Default constructor
	 * r  - open file for reading
	 * r+ - open file for reading and writing
	 * w  - open file for reading, reduce content to 0, if it not exist, create it
	 * w+ - open file for reading and writing, reduce content to 0, if it not exist, create it
	 * a  - open file for reading, char pointer at the end
	 * a+ - open file for reading and writing, char pointer at the end
	 * 
	 * @param String $openFile
	 * @param String $openMode
	 */
	function __construct($openFile, $openMode){
		$this->m_file = $openFile;
		$this->m_openMode = $openMode;
		
		$this->m_fp = fopen($this->m_file, $this->m_openMode);
	}
	
	
	
	/**
	 * PHP4: Default constructor
	 * r  - open file for reading
	 * r+ - open file for reading and writing
	 * w  - open file for reading, reduce content to 0, if it not exist, create it
	 * w+ - open file for reading and writing, reduce content to 0, if it not exist, create it
	 * a  - open file for reading, char pointer at the end
	 * a+ - open file for reading and writing, char pointer at the end
	 * 
	 * @param String $openFile
	 * @param String $openMode
	 */
	function tcms_file($openFile, $openMode){
		$this->__construct($openFile, $openMode);
	}
	
	
	
	/**
	 * PHP5 Destructor
	 */
	function __destruct(){
	}
	
	
	
	/**
	 * PHP4 Destructor
	 */
	function _tcms_file(){
		$this->__destruct();
	}
	
	
	
	/**
	 * Checks if the end of the file is reched
	 * 
	 * @return Boolean
	 */
	function isEOF(){
		return feof($this->m_fp);
	}
	
	
	
	/**
	 * Read the active file
	 * 
	 * @return String
	 */
	function read(){
		return fread($this->m_fp, filesize($this->m_file));
	}
	
	
	
	/**
	 * Read a line from the active file
	 * 
	 * @return String
	 */
	function readLine(){
		return fgets($this->m_fp);
	}
	
	
	
	/**
	 * Write the active file
	 * 
	 * @param String $content
	 */
	function write($content){
		fwrite($this->m_fp, $content);
	}
	
	
	
	/**
	 * Close the active file
	 */
	function close(){
		fclose($this->m_fp);
	}
	
	
	
	/**
	 * Backup the active file
	 */
	function backup(){
		/*copy(
			$this->m_file, 
			$this->m_file.'.bak'
		);*/
		
		$tmp = $this->Read();
		
		$fp = fopen($this->m_file.'.bak', 'w+');
		fwrite($fp, $tmp);
		fclose($fp);
	}
	
	
	
	/**
	 * Close the active file and change it
	 * r  - open file for reading
	 * r+ - open file for reading and writing
	 * w  - open file for reading, reduce content to 0, if it not exist, create it
	 * w+ - open file for reading and writing, reduce content to 0, if it not exist, create it
	 * a  - open file for reading, char pointer at the end
	 * a+ - open file for reading and writing, char pointer at the end
	 * 
	 * @param String $openFile
	 * @param String $openMode
	*/
	function changeFile($openFile, $openMode){
		fclose($this->m_fp);
		
		$this->m_file = $openFile;
		$this->m_openMode = $openMode;
		
		$this->m_fp = fopen($this->m_file, $this->m_openMode);
	}
	
	
	
	/**
	 * Close and delete the active file
	 */
	function delete(){
		$this->fileClose();
		unlink($this->m_fp);
	}
	
	
	
	/**
	 * Delete a custom file
	 * 
	 * @param String $file
	 */
	function deleteCustom($file){
		unlink($file);
	}
}

?>
