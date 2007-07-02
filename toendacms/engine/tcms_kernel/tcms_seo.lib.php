<?php /* _\|/_
         (o o)
+-----oOO-{_}-OOo--------------------------------------------------------+
| toendaCMS - Content Management and Weblogging System with XML and SQL  |
+------------------------------------------------------------------------+
| Copyright (c) Toenda Software Development                              |
| Author: Jonathan Naumann                                               |
+------------------------------------------------------------------------+
|
| toendaCMS Search Engine Optimization
|
| File:	tcms_seo.lib.php
|
+
*/


defined('_TCMS_VALID') or die('Restricted access');


/**
 * toendaCMS Search Engine Optimization
 *
 * This class is used for the search engine
 * optimization.
 *
 * @version 0.3.5
 * @author	Jonathan Naumann <jonathan@toenda.com>
 * @package toendaCMS
 * @subpackage tcms_kernel
 */


class tcms_seo {
	var $m_urlArray;
	var $m_idArray;
	var $m_RequestUri;
	
	
	
	/**
	 * PHP5 Constructor
	 */
	function __construct(){
		$this->m_urlArray = explode('/', $_SERVER['REQUEST_URI']);
		$this->m_RequestUri = $_SERVER['REQUEST_URI'];
		
		$this->m_idArray = explode('&', $this->m_urlArray[2]);
	}
	
	
	
	/**
	 * PHP4 Constructor
	 */
	function tcms_seo(){
		$this->__construct();
	}
	
	
	
	/**
	 * PHP5 Destructor
	 */
	function __destruct(){
	}
	
	
	
	/**
	 * PHP4 Destructor
	 */
	function _tcms_seo(){
		$this->__destruct();
	}
	
	
	
	/**
	 * Explode the url in colon format
	 */
	function explodeUrlColonFormat(){
		$arrSEO = '';
		
		foreach($this->m_urlArray as $urlKey => $urlValue){
			if(substr($urlValue, 0, 8) == 'session:'){
				$arrSEO['session'] = substr($urlValue, 8, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 8 ));
			}
			
			if(substr($urlValue, 0, 8) == 'section:'){
				$arrSEO['id'] = substr($urlValue, 8, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 8 ));
			}
			
			if(substr($urlValue, 0, 9) == 'template:'){
				$arrSEO['s'] = substr($urlValue, 9, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 9 ));
			}
			
			if(substr($urlValue, 0, 5) == 'news:' && $urlKey > 2){
				$arrSEO['news'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 5) == 'feed:' && $urlKey > 2){
				$arrSEO['feed'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 5) == 'save:' && $urlKey > 2){
				$arrSEO['save'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 10) == 'reg_login:' && $urlKey > 2){
				$arrSEO['reg_login'] = substr($urlValue, 10, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 10 ));
			}
			
			if(substr($urlValue, 0, 5) == 'todo:' && $urlKey > 2){
				$arrSEO['todo'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 5) == 'user:' && $urlKey > 2){
				$arrSEO['u'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 5) == 'file:' && $urlKey > 2){
				$pos = strpos($this->m_RequestUri, '/file:') + 6;
				$len = strlen($this->m_RequestUri) - $pos;
				$arrSEO['file'] = substr($this->m_RequestUri, $pos, $len);
				//$arrSEO['file'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 9) == 'category:' && $urlKey > 2){
				$arrSEO['category'] = substr($urlValue, 9, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 9 ));
			}
			
			if(substr($urlValue, 0, 4) == 'cat:' && $urlKey > 2){
				$arrSEO['cat'] = substr($urlValue, 4, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 4 ));
			}
			
			if(substr($urlValue, 0, 8) == 'article:' && $urlKey > 2){
				$arrSEO['article'] = substr($urlValue, 8, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 8 ));
			}
			
			if(substr($urlValue, 0, 7) == 'action:' && $urlKey > 2){
				$arrSEO['action'] = substr($urlValue, 7, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 7 ));
			}
			
			if(substr($urlValue, 0, 7) == 'albums:' && $urlKey > 2){
				$arrSEO['albums'] = substr($urlValue, 7, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 7 ));
			}
			
			if(substr($urlValue, 0, 8) == 'command:' && $urlKey > 2){
				$arrSEO['command'] = substr($urlValue, 8, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 8 ));
			}
			
			if(substr($urlValue, 0, 5) == 'poll:' && $urlKey > 2){
				$arrSEO['poll'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 3) == 'ps:'){
				$arrSEO['ps'] = substr($urlValue, 3, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 3 ));
			}
			
			if(substr($urlValue, 0, 5) == 'vote:' && $urlKey > 2){
				$arrSEO['vote'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 9) == 'XMLplace:' && $urlKey > 2){
				$arrSEO['XMLplace'] = substr($urlValue, 9, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 9 ));
			}
			
			if(substr($urlValue, 0, 8) == 'XMLfile:' && $urlKey > 2){
				$arrSEO['XMLfile'] = substr($urlValue, 8, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 8 ));
			}
			
			if(substr($urlValue, 0, 5) == 'page:' && $urlKey > 2){
				$arrSEO['page'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 5) == 'item:' && $urlKey > 2){
				$arrSEO['item'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 14) == 'contact_email:' && $urlKey > 2){
				$arrSEO['contact_email'] = substr($urlValue, 14, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 14 ));
			}
			
			if(substr($urlValue, 0, 5) == 'date:' && $urlKey > 2){
				$arrSEO['date'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 5) == 'code:' && $urlKey > 2){
				$arrSEO['code'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
			
			if(substr($urlValue, 0, 2) == 'c:' && $urlKey > 2){
				$arrSEO['c'] = substr($urlValue, 2, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 2 ));
			}
			
			if(substr($urlValue, 0, 5) == 'lang:' && $urlKey > 2){
				$arrSEO['lang'] = substr($urlValue, 5, ( strpos($urlValue, '/') != false ? strpos($urlValue, '/') : strlen($urlValue) - 5 ));
			}
		}
		
		return ( $arrSEO == '' ? null : $arrSEO );
	}
	
	
	
	/**
	 * Explode the url in slash format
	 */
	function explodeUrlSlashFormat(){
		$arrSEO = '';
		
		foreach($this->m_urlArray as $urlKey => $urlValue){
			//echo $urlValue.'<br>';
			
			switch($urlValue){
				case 'session':
					$arrSEO['session'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'section':
					$arrSEO['id'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'template':
					$arrSEO['s'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'news':
					$arrSEO['news'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'feed':
					$arrSEO['feed'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'save':
					$arrSEO['save'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'reg_login':
					$arrSEO['reg_login'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'todo':
					$arrSEO['todo'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'user':
					$arrSEO['u'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'file':
					$pos = strpos($this->m_RequestUri, '/file/') + 6;
					$len = strlen($this->m_RequestUri) - $pos;
					$arrSEO['file'] = substr($this->m_RequestUri, $pos, $len);
					//$arrSEO['file'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'category':
					$arrSEO['category'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'cat':
					$arrSEO['cat'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'article':
					$arrSEO['article'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'action':
					$arrSEO['action'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'albums':
					$arrSEO['albums'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'command':
					$arrSEO['command'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'poll':
					$arrSEO['poll'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'ps':
					$arrSEO['ps'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'vote':
					$arrSEO['vote'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'XMLplace':
					$arrSEO['XMLplace'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'XMLfile':
					$arrSEO['XMLfile'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'page':
					$arrSEO['page'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'item':
					$arrSEO['item'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'contact_email':
					$arrSEO['contact_email'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'date':
					$arrSEO['date'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'code':
					$arrSEO['code'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'c':
					$arrSEO['c'] = $this->m_urlArray[$urlKey + 1];
					break;
				
				case 'lang':
					$arrSEO['lang'] = $this->m_urlArray[$urlKey + 1];
					break;
			}
		}
		
		return ( $arrSEO == '' ? null : $arrSEO );
	}
	
	
	
	/**
	 * Explode the url in slash format
	 */
	function explodeHTMLFormat(){
		foreach($this->m_urlArray as $urlKey => $urlValue){
			//echo '$urlValue:'.$urlValue.'<br>';
			
			$val = '';
			$end = '';
			
			if(strlen($urlValue) > 0) {
				if(strpos($urlValue, '.', 1) > 0) {
					// found point
					$val = substr($urlValue, 0, strpos($urlValue, '.', 1));
					$end = substr($urlValue, strpos($urlValue, '.', 1) + 5);
				}
				else {
					$val = $urlValue;
				}
			}
			else {
				$val = $urlValue;
			}
			
			//echo '<span style="color:#fff;">$val:'.$val.'</span><br>';
			//echo '<span style="color:#fff;">$end:'.$end.'</span><br>';
			//echo '<span style="color:#fff;">arr id: '.$end.' -> '.substr($end, 1, strpos($end, '=') - 1).'</span><br>';
			//echo '<span style="color:#fff;">$arr val: '.$end.' -> '.substr($end, strpos($end, '=') + 1).'</span><br>';
			
			switch($val) {
				/*
					language's
				*/
				case 'de': $arrSEO['lang'] = 'de'; break;
				case 'en': $arrSEO['lang'] = 'en'; break;
				case 'bg': $arrSEO['lang'] = 'bg'; break;
				case 'nl': $arrSEO['lang'] = 'nl'; break;
				case 'fi': $arrSEO['lang'] = 'fi'; break;
				case 'it': $arrSEO['lang'] = 'it'; break;
				case 'kr': $arrSEO['lang'] = 'kr'; break;
				case 'no': $arrSEO['lang'] = 'no'; break;
				case 'br': $arrSEO['lang'] = 'br'; break;
				case 'ro': $arrSEO['lang'] = 'ro'; break;
				case 'sk': $arrSEO['lang'] = 'sk'; break;
				case 'es': $arrSEO['lang'] = 'es'; break;
				case 'se': $arrSEO['lang'] = 'se'; break;
				
				/*
					id's
				*/
				case 'frontpage': $arrSEO['id'] = 'frontpage'; break;
				case 'news':      $arrSEO['id'] = 'newsmanager'; break;
				case 'download':  $arrSEO['id'] = 'download'; break;
				case 'contact':   $arrSEO['id'] = 'contactform'; break;
				case 'register':  $arrSEO['id'] = 'register'; break;
				case 'profile':   $arrSEO['id'] = 'profile'; break;
				case 'polls':     $arrSEO['id'] = 'polls'; break;
				case 'legal':     $arrSEO['id'] = 'impressum'; break;
				case 'gallery':   $arrSEO['id'] = 'imagegallery'; break;
				case 'guestbook': $arrSEO['id'] = 'guestbook'; break;
				case 'articles':  $arrSEO['id'] = 'knowledgebase'; break;
				case 'products':  $arrSEO['id'] = 'products'; break;
				case 'search':    $arrSEO['id'] = 'search'; break;
				case 'links':     $arrSEO['id'] = 'links'; break;
				case 'cs':        $arrSEO['id'] = 'components'; break;
				
				/*
					only content id's
				*/
				default:
					if(strlen($val) == 5 
					&& trim($val) != 'index') {
						if(trim($arrSEO['id']) == '') {
							$arrSEO['id'] = $val;
						}
					}
					break;
			}
			
			if(trim($end) != '') {
				//echo '$arrSEO['.substr($end, 1, strpos($end, '=') - 1).'='.substr($end, strpos($end, '=') + 1).'<br>';
				
				$arrParams = explode('&', $end);
				
				foreach($arrParams as $key => $val){
					$val = str_replace('?', '', $val);
					
					//echo 'key='.$key.'<br>';
					//echo 'val='.$val.'<br><br>';
					
					//echo '$arrSEO['.substr($val, 0, strpos($val, '=')).']='.substr($val, strpos($val, '=') + 1).'<br>';
					
					$keyV = substr($val, 0, strpos($val, '='));
					
					if($keyV == 'id') {
						//if(trim($arrSEO['id']) == '') {
						//	$arrSEO[$keyV] = substr($val, strpos($val, '=') + 1);
						//}
					}
					else {
						$arrSEO[$keyV] = substr($val, strpos($val, '=') + 1);
					}
				}
			}
			
			//echo 'arrSEO[id]='.$arrSEO['id'].'<br>';
			//echo 'arrSEO[lang]='.$arrSEO['lang'].'<br><br>';
		}
		
		return ( $arrSEO == '' ? null : $arrSEO );
	}
}

?>