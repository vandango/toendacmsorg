<?php /* _\|/_
         (o o)
+-----oOO-{_}-OOo--------------------------------------------------------+
| toendaCMS - Content Management and Weblogging System with XML and SQL  |
+------------------------------------------------------------------------+
| Copyright (c) Toenda Software Development                              |
| Author: Jonathan Naumann                                               |
+------------------------------------------------------------------------+
| 
| toendaCMS Load Modules Configuration
|
| File:		tcms_modconfig.lib.php
| Version:	0.6.5
|
+
*/


defined('_TCMS_VALID') or die('Restricted access');



/****************************************
* Load Modules Configuration Functions
*
* tcms_modconfig             -> toendaCMS webpage path
* getSyndicationConfig       -> Return a array with all syndication configuration data
* getFrontpageConfig         -> Return a array with all frontpage configuration data
* getNewsmanagerConfig       -> Return a array with all newsmanager configuration data
* getDownloadConfig          -> Return a array with all download configuration data
* getContactformConfig       -> Return a array with all contactform configuration data
* getGuestbookConfig         -> Return a array with all guestbook configuration data
* getProductsConfig          -> Return a array with all products configuration data
* getImagegalleryConfig      -> Return a array with all imagegallery configuration data
* getLinkConfig              -> Return a array with all link configuration data
* getFAQConfig               -> Return a array with all FAQ configuration data
*
*/


class tcms_modconfig {
	var $tcms_main_path;
	var $tcms_image_path;
	var $tcms_db_prefix;
	
	
	
	
	
	/***
	* @return unknown
	* @desc Set the global var to front or admin
	*/
	function tcms_modconfig($administer, $imagePath){
		global $tcms_main;
		
		$this->tcms_main_path = $administer;
		$this->tcms_image_path = $imagePath;
		
		require($administer.'/tcms_global/database.php');
		$this->tcms_db_prefix = $tcms_main->secure_password($tcms_db_prefix, 'en');
	}
	
	
	
	
	
	/***
	* @return Return a array with all frontpage configuration data
	* @desc ...
	*/
	function getSyndicationConfig($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort){
		global $tcms_main;
		
		if($choosenDB == 'xml'){
			$news_xml = new xmlparser(''.$this->tcms_main_path.'/tcms_global/newsmanager.xml','r');
			$arrFP['use_rss091']         = $news_xml->read_section('config', 'use_rss091');
			$arrFP['use_rss10']          = $news_xml->read_section('config', 'use_rss10');
			$arrFP['use_rss20']          = $news_xml->read_section('config', 'use_rss20');
			$arrFP['use_atom03']         = $news_xml->read_section('config', 'use_atom03');
			$arrFP['use_opml']           = $news_xml->read_section('config', 'use_opml');
			$arrFP['syn_amount']         = $news_xml->read_section('config', 'syn_amount');
			$arrFP['use_syn_title']      = $news_xml->read_section('config', 'use_syn_title');
			$arrFP['def_feed']           = $news_xml->read_section('config', 'def_feed');
			
			if(!$arrFP['use_rss091'])      { $arrFP['use_rss091']       = ''; }
			if(!$arrFP['use_rss10'])       { $arrFP['use_rss10']        = ''; }
			if(!$arrFP['use_rss20'])       { $arrFP['use_rss20']        = ''; }
			if(!$arrFP['use_atom03'])      { $arrFP['use_atom03']       = ''; }
			if(!$arrFP['use_opml'])        { $arrFP['use_opml']         = ''; }
			if(!$arrFP['syn_amount'])      { $arrFP['syn_amount']       = ''; }
			if(!$arrFP['use_syn_title'])   { $arrFP['use_syn_title']    = ''; }
			if(!$arrFP['def_feed'])        { $arrFP['def_feed']         = ''; }
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$strQuery = "SELECT use_rss091, use_rss10, use_rss20, use_atom03, use_opml, syn_amount, use_syn_title, def_feed "
			."FROM ".$this->tcms_db_prefix."newsmanager "
			."WHERE uid = 'newsmanager'";
			
			$sqlQR = $sqlAL->sqlQuery($strQuery);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$arrFP['use_rss091']         = $sqlARR['use_rss091'];
			$arrFP['use_rss10']          = $sqlARR['use_rss10'];
			$arrFP['use_rss20']          = $sqlARR['use_rss20'];
			$arrFP['use_atom03']         = $sqlARR['use_atom03'];
			$arrFP['use_opml']           = $sqlARR['use_opml'];
			$arrFP['syn_amount']         = $sqlARR['syn_amount'];
			$arrFP['use_syn_title']      = $sqlARR['use_syn_title'];
			$arrFP['def_feed']           = $sqlARR['def_feed'];
			
			$sqlAL->_sqlAbstractionLayer();
			
			if($arrFP['use_rss091']         == NULL){ $arrFP['use_rss091']         = ''; }
			if($arrFP['use_rss10']          == NULL){ $arrFP['use_rss10']          = ''; }
			if($arrFP['use_rss20']          == NULL){ $arrFP['use_rss20']          = ''; }
			if($arrFP['use_atom03']         == NULL){ $arrFP['use_atom03']         = ''; }
			if($arrFP['use_opml']           == NULL){ $arrFP['use_opml']           = ''; }
			if($arrFP['syn_amount']         == NULL){ $arrFP['syn_amount']         = ''; }
			if($arrFP['use_syn_title']      == NULL){ $arrFP['use_syn_title']      = ''; }
			if($arrFP['def_feed']           == NULL){ $arrFP['def_feed']           = 'RSS0.91'; }
		}
		
		return $arrFP;
	}
	
	
	
	
	
	/***
	* @return Return a array with all frontpage configuration data
	* @desc ...
	*/
	function getFrontpageConfig($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort){
		global $tcms_main;
		
		if($choosenDB == 'xml'){
			$front_xml = new xmlparser(''.$this->tcms_main_path.'/tcms_global/frontpage.xml','r');
			$arrFP['front_id']         = $front_xml->read_section('front', 'front_id');
			$arrFP['front_title']      = $front_xml->read_section('front', 'front_title');
			$arrFP['front_stamp']      = $front_xml->read_section('front', 'front_stamp');
			$arrFP['front_text']       = $front_xml->read_section('front', 'front_text');
			$arrFP['front_news_title'] = $front_xml->read_section('front', 'news_title');
			$arrFP['cut_news']         = $front_xml->read_section('front', 'news_cut');
			$arrFP['how_many']         = $front_xml->read_section('front', 'module_use_0');
			$arrFP['front_s_title']    = $front_xml->read_section('front', 'sb_news_title');
			$arrFP['front_s_how_many'] = $front_xml->read_section('front', 'sb_news_amount');
			$arrFP['front_s_cut_news'] = $front_xml->read_section('front', 'sb_news_chars');
			$arrFP['front_s_enabled']  = $front_xml->read_section('front', 'sb_news_enabled');
			$arrFP['front_s_display']  = $front_xml->read_section('front', 'sb_news_display');
			
			$news_xml = new xmlparser(''.$this->tcms_main_path.'/tcms_global/newsmanager.xml','r');
			$arrFP['use_news_comments']  = $news_xml->read_section('config', 'use_comments');
			$arrFP['show_autor']         = $news_xml->read_section('config', 'show_autor');
			$arrFP['show_autor_as_link'] = $news_xml->read_section('config', 'show_autor_as_link');
			$arrFP['use_gravatar']       = $news_xml->read_section('config', 'use_gravatar');
			$arrFP['use_emoticons']      = $news_xml->read_section('config', 'use_emoticons');
			$arrFP['use_trackback']      = $news_xml->read_section('config', 'use_trackback');
			$arrFP['trackback_url']      = $news_xml->read_section('config', 'trackback_url');
			$arrFP['use_timesince']      = $news_xml->read_section('config', 'use_timesince');
			$arrFP['readmore_link']      = $news_xml->read_section('config', 'readmore_link');
			$arrFP['news_spacing']       = $news_xml->read_section('config', 'news_spacing');
			
			if($arrFP['front_id']         == false){ $arrFP['front_id']         = ''; }
			if($arrFP['front_title']      == false){ $arrFP['front_title']      = ''; }
			if($arrFP['front_stamp']      == false){ $arrFP['front_stamp']      = ''; }
			if($arrFP['front_text']       == false){ $arrFP['front_text']       = ''; }
			if($arrFP['front_news_title'] == false){ $arrFP['front_news_title'] = ''; }
			if($arrFP['cut_news']         == false){ $arrFP['cut_news']         = ''; }
			if($arrFP['how_many']         == false){ $arrFP['how_many']         = ''; }
			if($arrFP['use_gravatar']     == false){ $arrFP['use_gravatar']     = 0; }
			if($arrFP['use_emoticons']    == false){ $arrFP['use_emoticons']    = 0; }
			if($arrFP['use_trackback']    == false){ $arrFP['use_trackback']    = 0; }
			if($arrFP['trackback_url']    == false){ $arrFP['trackback_url']    = ''; }
			if($arrFP['front_s_title']    == false){ $arrFP['front_s_title']    = ''; }
			if($arrFP['front_s_how_many'] == false){ $arrFP['front_s_how_many'] = ''; }
			if($arrFP['front_s_cut_news'] == false){ $arrFP['front_s_cut_news'] = ''; }
			if($arrFP['front_s_enabled']  == false){ $arrFP['front_s_enabled']  = 0; }
			if($arrFP['front_s_display']  == false){ $arrFP['front_s_display']  = ''; }
			if($arrFP['use_timesince']    == false){ $arrFP['use_timesince']    = 0; }
			if($arrFP['readmore_link']    == false){ $arrFP['readmore_link']    = 0; }
			if($arrFP['news_spacing']     == false){ $arrFP['news_spacing']     = 0; }
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$strQuery = "SELECT front_title, front_stamp, front_text, news_title, news_cut, "
			."module_use_0, sb_news_title, sb_news_amount, sb_news_chars, sb_news_enabled, "
			."sb_news_display "
			."FROM ".$this->tcms_db_prefix."frontpage "
			."WHERE uid = 'frontpage'";
			
			$sqlQR = $sqlAL->sqlQuery($strQuery);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$arrFP['front_id']         = 'frontpage';
			$arrFP['front_title']      = $sqlARR['front_title'];
			$arrFP['front_stamp']      = $sqlARR['front_stamp'];
			$arrFP['front_text']       = $sqlARR['front_text'];
			$arrFP['front_news_title'] = $sqlARR['news_title'];
			$arrFP['cut_news']         = $sqlARR['news_cut'];
			$arrFP['how_many']         = $sqlARR['module_use_0'];
			$arrFP['front_s_title']    = $sqlARR['sb_news_title'];
			$arrFP['front_s_how_many'] = $sqlARR['sb_news_amount'];
			$arrFP['front_s_cut_news'] = $sqlARR['sb_news_chars'];
			$arrFP['front_s_enabled']  = $sqlARR['sb_news_enabled'];
			$arrFP['front_s_display']  = $sqlARR['sb_news_display'];
			
			$strQuery = "SELECT use_comments, show_autor, show_autor_as_link, use_gravatar, "
			."use_emoticons, use_trackback, use_timesince, readmore_link, news_spacing "
			."FROM ".$this->tcms_db_prefix."newsmanager "
			."WHERE uid = 'newsmanager'";
			
			$sqlQR = $sqlAL->sqlQuery($strQuery);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$arrFP['use_news_comments']  = $sqlARR['use_comments'];
			$arrFP['show_autor']         = $sqlARR['show_autor'];
			$arrFP['show_autor_as_link'] = $sqlARR['show_autor_as_link'];
			$arrFP['use_gravatar']       = $sqlARR['use_gravatar'];
			$arrFP['use_emoticons']      = $sqlARR['use_emoticons'];
			$arrFP['use_trackback']      = $sqlARR['use_trackback'];
			$arrFP['use_timesince']      = $sqlARR['use_timesince'];
			$arrFP['readmore_link']      = $sqlARR['readmore_link'];
			$arrFP['news_spacing']       = $sqlARR['news_spacing'];
			
			$sqlAL->_sqlAbstractionLayer();
			
			if($arrFP['front_id']         == NULL){ $arrFP['front_id']         = ''; }
			if($arrFP['front_title']      == NULL){ $arrFP['front_title']      = ''; }
			if($arrFP['front_stamp']      == NULL){ $arrFP['front_stamp']      = ''; }
			if($arrFP['front_text']       == NULL){ $arrFP['front_text']       = ''; }
			if($arrFP['front_news_title'] == NULL){ $arrFP['front_news_title'] = ''; }
			if($arrFP['cut_news']         == NULL){ $arrFP['cut_news']         = ''; }
			if($arrFP['how_many']         == NULL){ $arrFP['how_many']         = ''; }
			if($arrFP['front_s_title']    == NULL){ $arrFP['front_s_title']    = ''; }
			if($arrFP['front_s_how_many'] == NULL){ $arrFP['front_s_how_many'] = ''; }
			if($arrFP['front_s_cut_news'] == NULL){ $arrFP['front_s_cut_news'] = ''; }
			if($arrFP['front_s_enabled']  == NULL){ $arrFP['front_s_enabled']  = ''; }
			if($arrFP['front_s_display']  == NULL){ $arrFP['front_s_display']  = ''; }
			
			if($arrFP['use_news_comments']  == NULL){ $arrFP['use_news_comments']  = 0; }
			if($arrFP['show_autor']         == NULL){ $arrFP['show_autor']         = 0; }
			if($arrFP['show_autor_as_link'] == NULL){ $arrFP['show_autor_as_link'] = 0; }
			if($arrFP['use_gravatar']       == NULL){ $arrFP['use_gravatar']       = 0; }
			if($arrFP['use_emoticons']      == NULL){ $arrFP['use_emoticons']      = 0; }
			if($arrFP['use_trackback']      == NULL){ $arrFP['use_trackback']      = 0; }
			if($arrFP['use_timesince']      == NULL){ $arrFP['use_timesince']      = 0; }
			if($arrFP['readmore_link']      == NULL){ $arrFP['readmore_link']      = 0; }
			if($arrFP['news_spacing']       == NULL){ $arrFP['news_spacing']       = 0; }
		}
		
		$arrFP['front_title']      = $tcms_main->decodeText($arrFP['front_title'], '2', $c_charset);
		$arrFP['front_stamp']      = $tcms_main->decodeText($arrFP['front_stamp'], '2', $c_charset);
		$arrFP['front_text']       = $tcms_main->decodeText($arrFP['front_text'], '2', $c_charset);
		$arrFP['front_news_title'] = $tcms_main->decodeText($arrFP['front_news_title'], '2', $c_charset);
		$arrFP['front_s_title']    = $tcms_main->decodeText($arrFP['front_s_title'], '2', $c_charset);
		
		// toendaScript
		$toendaScript = new toendaScript($arrFP['front_text']);
		$arrFP['front_text'] = $toendaScript->toendaScript_trigger();
		$arrFP['front_text'] = $toendaScript->checkSEO($arrFP['front_text'], $this->tcms_image_path);
		
		return $arrFP;
	}
	
	
	
	
	
	/***
	* @return Return a array with all newsmanager configuration data
	* @desc ...
	*/
	function getNewsmanagerConfig($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort){
		global $tcms_main;
		
		if($choosenDB == 'xml'){
			$news_xml = new xmlparser(''.$this->tcms_main_path.'/tcms_global/newsmanager.xml', 'r');
			$arrNM['news_id']            = $news_xml->read_section('config', 'news_id');
			$arrNM['news_title']         = $news_xml->read_section('config', 'news_title');
			$arrNM['news_stamp']         = $news_xml->read_section('config', 'news_stamp');
			$arrNM['news_text']          = $news_xml->read_section('config', 'news_text');
			$arrNM['news_image']         = $news_xml->read_section('config', 'news_image');
			$arrNM['use_news_comments']  = $news_xml->read_section('config', 'use_comments');
			$arrNM['show_autor']         = $news_xml->read_section('config', 'show_autor');
			$arrNM['show_autor_as_link'] = $news_xml->read_section('config', 'show_autor_as_link');
			$arrNM['news_amount']        = $news_xml->read_section('config', 'news_amount');
			$arrNM['cut_news']           = $news_xml->read_section('config', 'news_cut');
			$arrNM['access']             = $news_xml->read_section('config', 'access');
			$arrNM['use_gravatar']       = $news_xml->read_section('config', 'use_gravatar');
			$arrNM['use_emoticons']      = $news_xml->read_section('config', 'use_emoticons');
			$arrNM['use_trackback']      = $news_xml->read_section('config', 'use_trackback');
			$arrNM['trackback_url']      = $news_xml->read_section('config', 'trackback_url');
			$arrNM['use_timesince']      = $news_xml->read_section('config', 'use_timesince');
			$arrNM['readmore_link']      = $news_xml->read_section('config', 'readmore_link');
			$arrNM['news_spacing']       = $news_xml->read_section('config', 'news_spacing');
			
			if($arrNM['news_id']            == false){ $arrNM['news_id']            = ''; }
			if($arrNM['news_title']         == false){ $arrNM['news_title']         = ''; }
			if($arrNM['news_stamp']         == false){ $arrNM['news_stamp']         = ''; }
			if($arrNM['news_text']          == false){ $arrNM['news_text']          = ''; }
			if($arrNM['news_image']         == false){ $arrNM['news_image']         = ''; }
			if($arrNM['use_news_comments']  == false){ $arrNM['use_news_comments']  = 0; }
			if($arrNM['show_autor']         == false){ $arrNM['show_autor']         = 0; }
			if($arrNM['show_autor_as_link'] == false){ $arrNM['show_autor_as_link'] = 0; }
			if($arrNM['news_amount']        == false){ $arrNM['news_amount']        = ''; }
			if($arrNM['cut_news']           == false){ $arrNM['cut_news']           = ''; }
			if($arrNM['access']             == false){ $arrNM['access']             = ''; }
			if($arrNM['use_gravatar']       == false){ $arrNM['use_gravatar']       = 0; }
			if($arrNM['use_emoticons']      == false){ $arrNM['use_emoticons']      = 0; }
			if($arrNM['use_trackback']      == false){ $arrNM['use_trackback']      = 0; }
			if($arrNM['trackback_url']      == false){ $arrNM['trackback_url']      = 0; }
			if($arrNM['use_timesince']      == false){ $arrNM['use_timesince']      = 0; }
			if($arrNM['readmore_link']      == false){ $arrNM['readmore_link']      = 0; }
			if($arrNM['news_spacing']      == false){ $arrNM['news_spacing']      = 0; }
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$strQuery = "SELECT news_title, news_stamp, news_image, use_comments, show_autor, "
			."show_autor_as_link, news_amount, news_cut, access, use_gravatar, use_emoticons, "
			."use_trackback, use_timesince, news_text, readmore_link, news_spacing "
			."FROM ".$this->tcms_db_prefix."newsmanager "
			."WHERE uid = 'newsmanager'";
			
			$sqlQR = $sqlAL->sqlQuery($strQuery);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$arrNM['news_id']            = 'newsmanager';
			$arrNM['news_title']         = $sqlARR['news_title'];
			$arrNM['news_stamp']         = $sqlARR['news_stamp'];
			$arrNM['news_text']          = $sqlARR['news_text'];
			$arrNM['news_image']         = $sqlARR['news_image'];
			$arrNM['use_news_comments']  = $sqlARR['use_comments'];
			$arrNM['show_autor']         = $sqlARR['show_autor'];
			$arrNM['show_autor_as_link'] = $sqlARR['show_autor_as_link'];
			$arrNM['news_amount']        = $sqlARR['news_amount'];
			$arrNM['cut_news']           = $sqlARR['news_cut'];
			$arrNM['access']             = $sqlARR['access'];
			$arrNM['use_gravatar']       = $sqlARR['use_gravatar'];
			$arrNM['use_emoticons']      = $sqlARR['use_emoticons'];
			$arrNM['use_trackback']      = $sqlARR['use_trackback'];
			$arrNM['use_timesince']      = $sqlARR['use_timesince'];
			$arrNM['readmore_link']      = $sqlARR['readmore_link'];
			$arrNM['news_spacing']       = $sqlARR['news_spacing'];
			
			$sqlAL->_sqlAbstractionLayer();
			unset($sqlAL);
			
			if($arrNM['news_id']            == NULL){ $arrNM['news_id']            = ''; }
			if($arrNM['news_title']         == NULL){ $arrNM['news_title']         = ''; }
			if($arrNM['news_stamp']         == NULL){ $arrNM['news_stamp']         = ''; }
			if($arrNM['news_text']          == NULL){ $arrNM['news_text']          = ''; }
			if($arrNM['news_image']         == NULL){ $arrNM['news_image']         = ''; }
			if($arrNM['use_news_comments']  == NULL){ $arrNM['use_news_comments']  = 0; }
			if($arrNM['show_autor']         == NULL){ $arrNM['show_autor']         = 0; }
			if($arrNM['show_autor_as_link'] == NULL){ $arrNM['show_autor_as_link'] = 0; }
			if($arrNM['news_amount']        == NULL){ $arrNM['news_amount']        = ''; }
			if($arrNM['news_cut']           == NULL){ $arrNM['news_cut']           = ''; }
			if($arrNM['access']             == NULL){ $arrNM['access']             = ''; }
			if($arrNM['use_gravatar']       == NULL){ $arrNM['use_gravatar']       = 0; }
			if($arrNM['use_emoticons']      == NULL){ $arrNM['use_emoticons']      = 0; }
			if($arrNM['use_trackback']      == NULL){ $arrNM['use_trackback']      = 0; }
			if($arrNM['use_timesince']      == NULL){ $arrNM['use_timesince']      = 0; }
			if($arrNM['readmore_link']      == NULL){ $arrNM['readmore_link']      = 0; }
			if($arrNM['news_spacing']       == NULL){ $arrNM['news_spacing']       = 0; }
		}
		
		$arrNM['news_title'] = $tcms_main->decodeText($arrNM['news_title'], '2', $c_charset);
		$arrNM['news_stamp'] = $tcms_main->decodeText($arrNM['news_stamp'], '2', $c_charset);
		$arrNM['news_text']  = $tcms_main->decodeText($arrNM['news_text'], '2', $c_charset);
		
		return $arrNM;
	}
	
	
	
	
	
	/***
	* @return Return a array with all download configuration data
	* @desc ...
	*/
	function getDownloadConfig($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort){
		global $tcms_main;
		
		if($choosenDB == 'xml'){
			$down_xml     = new xmlparser(''.$this->tcms_main_path.'/tcms_global/download.xml','r');
			
			$arrDW['download_id']    = $down_xml->read_section('config', 'download_id');
			$arrDW['download_title'] = $down_xml->read_section('config', 'download_title');
			$arrDW['download_stamp'] = $down_xml->read_section('config', 'download_stamp');
			$arrDW['download_text']  = $down_xml->read_section('config', 'download_text');
			
			if(!$arrDW['download_id'])   { $arrDW['download_id']    = ''; }
			if(!$arrDW['download_title']){ $arrDW['download_title'] = ''; }
			if(!$arrDW['download_stamp']){ $arrDW['download_stamp'] = ''; }
			if(!$arrDW['download_text']) { $arrDW['download_text']  = ''; }
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$strQuery = "SELECT download_title, download_stamp, download_text "
			."FROM ".$this->tcms_db_prefix."downloads_config "
			."WHERE uid = 'download'";
			
			$sqlQR = $sqlAL->sqlQuery($strQuery);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$arrDW['download_id']    = 'download';
			$arrDW['download_title'] = $sqlARR['download_title'];
			$arrDW['download_stamp'] = $sqlARR['download_stamp'];
			$arrDW['download_text']  = $sqlARR['download_text'];
			
			$sqlAL->_sqlAbstractionLayer();
			
			if($arrDW['download_id']    == NULL){ $arrDW['download_id']    = ''; }
			if($arrDW['download_title'] == NULL){ $arrDW['download_title'] = ''; }
			if($arrDW['download_stamp'] == NULL){ $arrDW['download_stamp'] = ''; }
			if($arrDW['download_text']  == NULL){ $arrDW['download_text']  = ''; }
		}
		
		$arrDW['download_title'] = $tcms_main->decodeText($arrDW['download_title'], '2', $c_charset);
		$arrDW['download_stamp'] = $tcms_main->decodeText($arrDW['download_stamp'], '2', $c_charset);
		$arrDW['download_text']  = $tcms_main->decodeText($arrDW['download_text'], '2', $c_charset);
		
		return $arrDW;
	}
	
	
	
	
	
	/***
	* @return Return a array with all contactform configuration data
	* @desc ...
	*/
	function getContactformConfig($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort){
		global $tcms_main;
		
		if($choosenDB == 'xml'){
			$contactform_xml = new xmlparser(''.$this->tcms_main_path.'/tcms_global/contactform.xml','r');
			
			$arrCF['cf_contact'] = $contactform_xml->read_section('email', 'contact');
			$arrCF['cf_id']      = $contactform_xml->read_section('email', 'send_id');
			$arrCF['cf_title']   = $contactform_xml->read_section('email', 'contacttitle');
			$arrCF['cf_stamp']   = $contactform_xml->read_section('email', 'contactstamp');
			$arrCF['cf_text']    = $contactform_xml->read_section('email', 'contacttext');
			$arrCF['cf_access']  = $contactform_xml->read_section('email', 'access');
			$arrCF['cf_scisb']   = $contactform_xml->read_section('email', 'show_contacts_in_sidebar');
			$arrCF['cf_enabled'] = $contactform_xml->read_section('email', 'enabled');
			$arrCF['cf_adbook']  = $contactform_xml->read_section('email', 'use_adressbook');
			$arrCF['cf_usecon']  = $contactform_xml->read_section('email', 'use_contact');
			$arrCF['cf_showce']  = $contactform_xml->read_section('email', 'show_contactemail');
			
			if(!$arrCF['cf_contact']){ $arrCF['cf_contact'] = ''; }
			if(!$arrCF['cf_id'])     { $arrCF['cf_id']      = ''; }
			if(!$arrCF['cf_title'])  { $arrCF['cf_title']   = ''; }
			if(!$arrCF['cf_stamp'])  { $arrCF['cf_stamp']   = ''; }
			if(!$arrCF['cf_text'])   { $arrCF['cf_text']    = ''; }
			if(!$arrCF['cf_access']) { $arrCF['cf_access']  = 'Public'; }
			if(!$arrCF['cf_scisb'])  { $arrCF['cf_scisb']   = 0; }
			if(!$arrCF['cf_enabled']){ $arrCF['cf_enabled'] = 0; }
			if(!$arrCF['cf_adbook']) { $arrCF['cf_adbook']  = 0; }
			if(!$arrCF['cf_usecon']) { $arrCF['cf_usecon']  = 0; }
			if(!$arrCF['cf_showce']) { $arrCF['cf_showce']  = 0; }
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$strQuery = "SELECT contact, send_id, contacttitle, contactstamp, "
			."access, show_contacts_in_sidebar, enabled, use_adressbook, use_contact, "
			."show_contactemail, contacttext "
			."FROM ".$this->tcms_db_prefix."contactform "
			."WHERE uid = 'contactform'";
			
			$sqlQR = $sqlAL->sqlQuery($strQuery);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$arrCF['cf_contact'] = $sqlARR['contact'];
			$arrCF['cf_id']      = $sqlARR['send_id'];
			$arrCF['cf_title']   = $sqlARR['contacttitle'];
			$arrCF['cf_stamp']   = $sqlARR['contactstamp'];
			$arrCF['cf_text']    = $sqlARR['contacttext'];
			$arrCF['cf_access']  = $sqlARR['access'];
			$arrCF['cf_scisb']   = $sqlARR['show_contacts_in_sidebar'];
			$arrCF['cf_enabled'] = $sqlARR['enabled'];
			$arrCF['cf_adbook']  = $sqlARR['use_adressbook'];
			$arrCF['cf_usecon']  = $sqlARR['use_contact'];
			$arrCF['cf_showce']  = $sqlARR['show_contactemail'];
			
			$sqlAL->_sqlAbstractionLayer();
			
			if($arrCF['cf_contact'] == NULL){ $arrCF['cf_contact'] = ''; }
			if($arrCF['cf_id']      == NULL){ $arrCF['cf_id']      = ''; }
			if($arrCF['cf_title']   == NULL){ $arrCF['cf_title']   = ''; }
			if($arrCF['cf_stamp']   == NULL){ $arrCF['cf_stamp']   = ''; }
			if($arrCF['cf_text']    == NULL){ $arrCF['cf_text']    = ''; }
			if($arrCF['cf_access']  == NULL){ $arrCF['cf_access']  = ''; }
			if($arrCF['cf_scisb']   == NULL){ $arrCF['cf_scisb']   = ''; }
			if($arrCF['cf_enabled'] == NULL){ $arrCF['cf_enabled'] = ''; }
			if($arrCF['cf_adbook']  == NULL){ $arrCF['cf_adbook']  = ''; }
			if($arrCF['cf_usecon']  == NULL){ $arrCF['cf_usecon']  = ''; }
			if($arrCF['cf_showce']  == NULL){ $arrCF['cf_showce']  = ''; }
		}
		
		$arrCF['cf_contact'] = $tcms_main->decodeText($arrCF['cf_contact'], '2', $c_charset);
		$arrCF['cf_title']   = $tcms_main->decodeText($arrCF['cf_title'], '2', $c_charset);
		$arrCF['cf_stamp']   = $tcms_main->decodeText($arrCF['cf_stamp'], '2', $c_charset);
		$arrCF['cf_text']    = $tcms_main->decodeText($arrCF['cf_text'], '2', $c_charset);
		
		return $arrCF;
	}
	
	
	
	
	
	/***
	* @return Return a array with all newsmanager configuration data
	* @desc ...
	*/
	function getGuestbookConfig($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort){
		global $tcms_main;
		
		if($choosenDB == 'xml'){
			$gbxml = new xmlparser(''.$this->tcms_main_path.'/tcms_global/guestbook.xml','r');
			
			$arrGB['guest_id']  = $gbxml->read_section('config', 'guest_id');
			$arrGB['booktitle'] = $gbxml->read_section('config', 'booktitle');
			$arrGB['bookstamp'] = $gbxml->read_section('config', 'bookstamp');
			$arrGB['access']    = $gbxml->read_section('config', 'access');
			$arrGB['enabled']   = $gbxml->read_section('config', 'enabled');
			
			if(!$arrGB['guest_id']) { $arrGB['guest_id']  = ''; }
			if(!$arrGB['booktitle']){ $arrGB['booktitle'] = ''; }
			if(!$arrGB['bookstamp']){ $arrGB['bookstamp'] = ''; }
			if(!$arrGB['access'])   { $arrGB['access']    = ''; }
			if(!$arrGB['enabled'])  { $arrGB['enabled']   = ''; }
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$strQuery = "SELECT booktitle, bookstamp, access, enabled "
			."FROM ".$this->tcms_db_prefix."guestbook "
			."WHERE uid = 'guestbook'";
			
			$sqlQR = $sqlAL->sqlQuery($strQuery);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$arrGB['guest_id']  = 'guestbook';
			$arrGB['booktitle'] = $sqlARR['booktitle'];
			$arrGB['bookstamp'] = $sqlARR['bookstamp'];
			$arrGB['access']    = $sqlARR['access'];
			$arrGB['enabled']   = $sqlARR['enabled'];
			
			$sqlAL->_sqlAbstractionLayer();
			
			if($arrGB['guest_id']  == NULL){ $arrGB['guest_id']  = ''; }
			if($arrGB['booktitle'] == NULL){ $arrGB['booktitle'] = ''; }
			if($arrGB['bookstamp'] == NULL){ $arrGB['bookstamp'] = ''; }
			if($arrGB['access']    == NULL){ $arrGB['access']    = ''; }
			if($arrGB['enabled']   == NULL){ $arrGB['enabled']   = ''; }
		}
		
		$arrGB['booktitle'] = $tcms_main->decodeText($arrGB['booktitle'], '2', $c_charset);
		$arrGB['bookstamp'] = $tcms_main->decodeText($arrGB['bookstamp'], '2', $c_charset);
		
		return $arrGB;
	}
	
	
	
	
	
	/***
	* @return Return a array with all download configuration data
	* @desc ...
	*/
	function getProductsConfig($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort){
		global $tcms_main;
		
		if($choosenDB == 'xml'){
			$pro_xml = new xmlparser(''.$this->tcms_main_path.'/tcms_global/products.xml','r');
			
			$arrP['products_id']        = $pro_xml->read_section('config', 'products_id');
			$arrP['products_title']     = $pro_xml->read_section('config', 'products_title');
			$arrP['products_stamp']     = $pro_xml->read_section('config', 'products_stamp');
			$arrP['products_text']      = $pro_xml->read_section('config', 'products_text');
			$arrP['category_state']     = $pro_xml->read_section('config', 'category_state');
			$arrP['category_title']     = $pro_xml->read_section('config', 'category_title');
			$arrP['use_category_title'] = $pro_xml->read_section('config', 'use_category_title');
			
			if(!$arrP['products_id'])       { $arrP['products_id']        = ''; }
			if(!$arrP['products_title'])    { $arrP['products_title']     = ''; }
			if(!$arrP['products_stamp'])    { $arrP['products_stamp']     = ''; }
			if(!$arrP['products_text'])     { $arrP['products_text']      = ''; }
			if(!$arrP['category_state'])    { $arrP['category_state']     = ''; }
			if(!$arrP['category_title'])    { $arrP['category_title']     = ''; }
			if(!$arrP['use_category_title']){ $arrP['use_category_title'] = ''; }
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$strQuery = "SELECT products_title, products_stamp, products_text, category_state, "
			."category_title, use_category_title "
			."FROM ".$this->tcms_db_prefix."products_config "
			."WHERE uid = 'products'";
			
			$sqlQR = $sqlAL->sqlQuery($strQuery);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$arrP['products_id']        = 'products';
			$arrP['products_title']     = $sqlARR['products_title'];
			$arrP['products_stamp']     = $sqlARR['products_stamp'];
			$arrP['products_text']      = $sqlARR['products_text'];
			$arrP['category_state']     = $sqlARR['category_state'];
			$arrP['category_title']     = $sqlARR['category_title'];
			$arrP['use_category_title'] = $sqlARR['use_category_title'];
			
			$sqlAL->_sqlAbstractionLayer();
			
			if($arrP['products_id']        == NULL){ $arrP['products_id']        = ''; }
			if($arrP['products_title']     == NULL){ $arrP['products_title']     = ''; }
			if($arrP['products_stamp']     == NULL){ $arrP['products_stamp']     = ''; }
			if($arrP['products_text']      == NULL){ $arrP['products_text']      = ''; }
			if($arrP['category_state']     == NULL){ $arrP['category_state']     = ''; }
			if($arrP['category_title']     == NULL){ $arrP['category_title']     = ''; }
			if($arrP['use_category_title'] == NULL){ $arrP['use_category_title'] = ''; }
		}
		
		$arrP['products_title'] = $tcms_main->decodeText($arrP['products_title'], '2', $c_charset);
		$arrP['products_stamp'] = $tcms_main->decodeText($arrP['products_stamp'], '2', $c_charset);
		$arrP['products_text']  = $tcms_main->decodeText($arrP['products_text'], '2', $c_charset);
		
		return $arrP;
	}
	
	
	
	
	
	/***
	* @return Return a array with all imagegallery configuration data
	* @desc ...
	*/
	function getImagegalleryConfig($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort){
		global $tcms_main;
		
		if($choosenDB == 'xml'){
			$pro_xml = new xmlparser(''.$this->tcms_main_path.'/tcms_global/imagegallery.xml','r');
			
			$arrP['image_id']      = $pro_xml->read_section('config', 'image_id');
			$arrP['image_title']   = $pro_xml->read_section('config', 'image_title');
			$arrP['image_stamp']   = $pro_xml->read_section('config', 'image_stamp');
			$arrP['image_details'] = $pro_xml->read_section('config', 'image_details');
			$arrP['use_comments']  = $pro_xml->read_section('config', 'image_comments');
			$arrP['image_sort']    = $pro_xml->read_section('config', 'image_sort');
			$arrP['access']        = $pro_xml->read_section('config', 'access');
			$arrP['list_option']   = $pro_xml->read_section('config', 'list_option');
			
			if($arrP['image_id']      == false){ $arrP['image_id']      = ''; }
			if($arrP['image_title']   == false){ $arrP['image_title']   = ''; }
			if($arrP['image_stamp']   == false){ $arrP['image_stamp']   = ''; }
			if($arrP['image_details'] == false){ $arrP['image_details'] = ''; }
			if($arrP['image_sort']    == false){ $arrP['image_sort']    = ''; }
			if($arrP['use_comments']  == false){ $arrP['use_comments']  = ''; }
			if($arrP['access']        == false){ $arrP['access']        = ''; }
			if($arrP['list_option']   == false){ $arrP['list_option']   = ''; }
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$strQuery = "SELECT image_title, image_stamp, image_details, image_sort, "
			."use_comments, access, list_option "
			."FROM ".$this->tcms_db_prefix."imagegallery_config "
			."WHERE uid = 'imagegallery'";
			
			$sqlQR = $sqlAL->sqlQuery($strQuery);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$arrP['image_id']      = 'imagegallery';
			$arrP['image_title']   = $sqlARR['image_title'];
			$arrP['image_stamp']   = $sqlARR['image_stamp'];
			$arrP['image_details'] = $sqlARR['image_details'];
			$arrP['image_sort']    = $sqlARR['image_sort'];
			$arrP['use_comments']  = $sqlARR['use_comments'];
			$arrP['access']        = $sqlARR['access'];
			$arrP['list_option']   = $sqlARR['list_option'];
			
			$sqlAL->_sqlAbstractionLayer();
			
			if($arrP['image_id']      == NULL){ $arrP['image_id']      = ''; }
			if($arrP['image_title']   == NULL){ $arrP['image_title']   = ''; }
			if($arrP['image_stamp']   == NULL){ $arrP['image_stamp']   = ''; }
			if($arrP['image_details'] == NULL){ $arrP['image_details'] = ''; }
			if($arrP['image_sort']    == NULL){ $arrP['image_sort']    = ''; }
			if($arrP['use_comments']  == NULL){ $arrP['use_comments']  = ''; }
			if($arrP['access']        == NULL){ $arrP['access']        = ''; }
			if($arrP['list_option']   == NULL){ $arrP['list_option']   = ''; }
		}
		
		$arrP['image_title'] = $tcms_main->decodeText($arrP['image_title'], '2', $c_charset);
		$arrP['image_stamp'] = $tcms_main->decodeText($arrP['image_stamp'], '2', $c_charset);
		
		return $arrP;
	}
	
	
	
	
	
	/***
	* @return Return a array with all link configuration data
	* @desc ...
	*/
	function getLinkConfig($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort){
		global $tcms_main;
		
		if($choosenDB == 'xml'){
			$pro_xml = new xmlparser(''.$this->tcms_main_path.'/tcms_global/linkmanager.xml','r');
			
			$arrL['link_title']    = $pro_xml->read_section('config', 'link_main_title');
			$arrL['link_subtitle'] = $pro_xml->read_section('config', 'link_main_subtitle');
			$arrL['link_text']     = $pro_xml->read_section('config', 'link_main_text');
			$arrL['link_use_desc'] = $pro_xml->read_section('config', 'link_use_main_desc');
			$arrL['access']        = $pro_xml->read_section('config', 'link_main_access');
			
			if(!$arrL['link_id'])      { $arrL['link_id']       = ''; }
			if(!$arrL['link_title'])   { $arrL['link_title']    = ''; }
			if(!$arrL['link_subtitle']){ $arrL['link_subtitle'] = ''; }
			if(!$arrL['link_text'])    { $arrL['link_text']     = ''; }
			if(!$arrL['link_use_desc']){ $arrL['link_use_desc'] = ''; }
			if(!$arrL['access'])       { $arrL['access']        = ''; }
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$strQuery = "SELECT link_main_title, link_main_subtitle, link_main_text, link_use_main_desc, "
			."link_main_access "
			."FROM ".$this->tcms_db_prefix."links_config "
			."WHERE uid = 'links_config_main'";
			
			$sqlQR = $sqlAL->sqlQuery($strQuery);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$arrL['link_title']    = $sqlARR['link_main_title'];
			$arrL['link_subtitle'] = $sqlARR['link_main_subtitle'];
			$arrL['link_text']     = $sqlARR['link_main_text'];
			$arrL['link_use_desc'] = $sqlARR['link_use_main_desc'];
			$arrL['access']        = $sqlARR['link_main_access'];
			
			$sqlAL->_sqlAbstractionLayer();
			
			if($arrL['link_title']    == NULL){ $arrL['link_title']    = ''; }
			if($arrL['link_subtitle'] == NULL){ $arrL['link_subtitle'] = ''; }
			if($arrL['link_text']     == NULL){ $arrL['link_text']     = ''; }
			if($arrL['link_use_desc'] == NULL){ $arrL['link_use_desc'] = ''; }
			if($arrL['access']        == NULL){ $arrL['access']        = ''; }
		}
		
		$arrL['link_title']    = $tcms_main->decodeText($arrL['link_title'], '2', $c_charset);
		$arrL['link_subtitle'] = $tcms_main->decodeText($arrL['link_subtitle'], '2', $c_charset);
		$arrL['link_text']     = $tcms_main->decodeText($arrL['link_text'], '2', $c_charset);
		
		return $arrL;
	}
	
	
	
	
	
	/***
	* @return Return a array with all link configuration data
	* @desc ...
	*/
	function getFAQConfig($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort){
		global $tcms_main;
		
		if($choosenDB == 'xml'){
			$pro_xml = new xmlparser(''.$this->tcms_main_path.'/tcms_global/knowledgebase.xml','r');
			
			$arrFAQ['faq_uid']       = 'knowledgebase';
			$arrFAQ['faq_title']     = $pro_xml->read_section('config', 'title');
			$arrFAQ['faq_subtitle']  = $pro_xml->read_section('config', 'subtitle');
			$arrFAQ['faq_text']      = $pro_xml->read_section('config', 'text');
			$arrFAQ['faq_enabled']   = $pro_xml->read_section('config', 'enabled');
			$arrFAQ['faq_a_enabled'] = $pro_xml->read_section('config', 'autor_enabled');
			$arrFAQ['access']        = $pro_xml->read_section('config', 'access');
			
			if($arrFAQ['faq_uid']       == false){ $arrFAQ['faq_uid']       = ''; }
			if($arrFAQ['faq_title']     == false){ $arrFAQ['faq_title']     = ''; }
			if($arrFAQ['faq_subtitle']  == false){ $arrFAQ['faq_subtitle']  = ''; }
			if($arrFAQ['faq_text']      == false){ $arrFAQ['faq_text']      = ''; }
			if($arrFAQ['faq_enabled']   == false){ $arrFAQ['faq_enabled']   = ''; }
			if($arrFAQ['faq_a_enabled'] == false){ $arrFAQ['faq_a_enabled'] = ''; }
			if($arrFAQ['access']        == false){ $arrFAQ['access']        = ''; }
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$strQuery = "SELECT uid, title, subtitle, text, "
			."enabled, autor_enabled, access "
			."FROM ".$this->tcms_db_prefix."knowledgebase_config "
			."WHERE uid = 'knowledgebase'";
			
			$sqlQR = $sqlAL->sqlQuery($strQuery);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$arrFAQ['faq_uid']       = 'knowledgebase';
			$arrFAQ['faq_title']     = $sqlARR['title'];
			$arrFAQ['faq_subtitle']  = $sqlARR['subtitle'];
			$arrFAQ['faq_text']      = $sqlARR['text'];
			$arrFAQ['faq_enabled']   = $sqlARR['enabled'];
			$arrFAQ['faq_a_enabled'] = $sqlARR['autor_enabled'];
			$arrFAQ['access']        = $sqlARR['access'];
			
			$sqlAL->_sqlAbstractionLayer();
			
			if($arrFAQ['faq_uid']       == NULL){ $arrFAQ['faq_uid']       = ''; }
			if($arrFAQ['faq_title']     == NULL){ $arrFAQ['faq_title']     = ''; }
			if($arrFAQ['faq_subtitle']  == NULL){ $arrFAQ['faq_subtitle']  = ''; }
			if($arrFAQ['faq_text']      == NULL){ $arrFAQ['faq_text']      = ''; }
			if($arrFAQ['faq_enabled']   == NULL){ $arrFAQ['faq_enabled']   = ''; }
			if($arrFAQ['faq_a_enabled'] == NULL){ $arrFAQ['faq_a_enabled'] = ''; }
			if($arrFAQ['access']        == NULL){ $arrFAQ['access']        = ''; }
		}
		
		$arrFAQ['faq_title']    = $tcms_main->decodeText($arrFAQ['faq_title'], '2', $c_charset);
		$arrFAQ['faq_subtitle'] = $tcms_main->decodeText($arrFAQ['faq_subtitle'], '2', $c_charset);
		$arrFAQ['faq_text']     = $tcms_main->decodeText($arrFAQ['faq_text'], '2', $c_charset);
		
		return $arrFAQ;
	}
}

?>