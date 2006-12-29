<?php /* _\|/_
         (o o)
+-----oOO-{_}-OOo--------------------------------------------------------+
| toendaCMS - Content Management and Weblogging System with XML and SQL  |
+------------------------------------------------------------------------+
| Copyright (c) Toenda Software Development                              |
| Author: Jonathan Naumann                                               |
+------------------------------------------------------------------------+
| 
| Linkbrowser for intern links
|
| File:		node.php
| Version:	0.3.0
|
+
*/





if(isset($_GET['v'])){ $v = $_GET['v']; }
if(isset($_GET['n'])){ $n = $_GET['n']; }
if(isset($_GET['faq'])){ $faq = $_GET['faq']; }
if(isset($_GET['url'])){ $url = $_GET['url']; }
if(isset($_GET['id_user'])){ $id_user = $_GET['id_user']; }

if(isset($_POST['faq'])){ $faq = $_POST['faq']; }
if(isset($_POST['mediaImage'])){ $mediaImage = $_POST['mediaImage']; }
if(isset($_POST['saveMedia'])){ $saveMedia = $_POST['saveMedia']; }
if(isset($_POST['todo'])){ $todo = $_POST['todo']; }
if(isset($_POST['id_user'])){ $id_user = $_POST['id_user']; }





/*****************
* INI
*/

define('_TCMS_VALID', 1);

$language_stage = 'admin';
include_once('../language/lang_admin.php');

$tcms_administer_site = 'data';

include_once('../tcms_kernel/tcms_time.lib.php');
include_once('../tcms_kernel/tcms.lib.php');
include_once('../tcms_kernel/tcms_html.lib.php');
include_once('../tcms_kernel/tcms_gd.lib.php');
include_once('../tcms_kernel/tcms_sql.lib.php');
include('../../'.$tcms_administer_site.'/tcms_global/database.php');


$tcms_main = new tcms_main('../../'.$tcms_administer_site, $choosenDB);

// database
$choosenDB = $tcms_main->secure_password($tcms_db_engine, 'en');
$sqlUser   = $tcms_main->secure_password($tcms_db_user, 'en');
$sqlPass   = $tcms_main->secure_password($tcms_db_password, 'en');
$sqlHost   = $tcms_main->secure_password($tcms_db_host, 'en');
$sqlDB     = $tcms_main->secure_password($tcms_db_database, 'en');
$sqlPort   = $tcms_main->secure_password($tcms_db_port, 'en');
$sqlPrefix = $tcms_main->secure_password($tcms_db_prefix, 'en');
$tcms_db_prefix = $sqlPrefix;

$tcms_main->setDatabaseInfo($choosenDB);



if(isset($faq) && $faq != '') $arr_dir = $tcms_main->readdir_ext('../../'.$tcms_administer_site.'/images/knowledgebase/');
else $arr_dir = $tcms_main->readdir_ext('../../'.$tcms_administer_site.'/images/Image/');


$c_xml        = new xmlparser('../../'.$tcms_administer_site.'/tcms_global/var.xml', 'r');
$show_wysiwyg = $c_xml->read_section('global', 'wysiwyg');
$seoEnabled   = $c_xml->read_section('global', 'seo_enabled');
$seoFolder    = $c_xml->read_section('global', 'server_folder');
$seoFormat    = $c_xml->read_section('global', 'seo_format');


$tcms_main->setGlobalFolder($seoFolder, $seoEnabled);
if($seoFormat == 0){ $tcms_main->setURLSEO('colon'); }
else{ $tcms_main->setURLSEO('slash'); }


$xmlURL = new xmlparser('../../'.$tcms_administer_site.'/tcms_global/footer.xml','r');
$webURL = $xmlURL->read_section('footer', 'owner_url');



$version_xml  = new xmlparser('../../engine/tcms_kernel/tcms_version.xml','r');
$cms_name     = $version_xml->read_section('version', 'name');
$cms_tagline  = $version_xml->read_section('version', 'tagline');
$toenda_copyr = $version_xml->read_section('version', 'toenda_copyright');
$version_xml->flush();
$version_xml->_xmlparser();
unset($version_xml);





//***********************************
// IF NOT LOGED IN
//
if(isset($id_user)){
	//***********************************************
	// IF THE FILE TO OLD, UNLINK IT
	//
	if($choosenDB == 'xml'){ $tcms_main->check_session($id_user, 'admin'); }
	else{ $tcms_main->check_sql_session($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort, $id_user); }
	
	
	
	
	if($choosenDB == 'xml'){
		if(isset($id_user) && $id_user != '' && file_exists('session/'.$id_user) && filesize('session/'.$id_user) != 0){ $check_session = true; }
		else{ $check_session = false; }
	}
	else{
		$check_session = $tcms_main->check_session_exists($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort, $id_user);
	}
	
	if($check_session){
		if($choosenDB == 'xml'){
			$m_tag = $tcms_main->create_admin($id_user);
			
			
			$xml = new xmlparser('../../'.$tcms_administer_site.'/tcms_user/'.$m_tag.'.xml','r');
			$id_name     = $xml->read_section('user', 'name');
			$id_username = $xml->read_section('user', 'username');
			$id_group    = $xml->read_section('user', 'group');
			$id_uid      = $m_tag;
			
			$id_name     = $tcms_main->decodeText($id_name, '2', $c_charset);
			$id_username = $tcms_main->decodeText($id_username, '2', $c_charset);
		}
		else{
			$arr_ws = $tcms_main->create_sql_username($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort, $id_user);
			$m_tag = $arr_ws['id'];
			
			
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$sqlQR = $sqlAL->sqlGetOne($tcms_db_prefix.'user', $m_tag);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			
			$id_name     = $sqlARR['name'];
			$id_username = $sqlARR['username'];
			$id_group    = $sqlARR['group'];
			$id_uid      = $sqlARR['uid'];
			
			$id_name     = $tcms_main->decodeText($id_name, '2', $c_charset);
			$id_username = $tcms_main->decodeText($id_username, '2', $c_charset);
			
			if($id_name     == NULL){ $id_name     = ''; }
			if($id_username == NULL){ $id_username = ''; }
			if($id_group    == NULL){ $id_group    = ''; }
		}
		
		
		
		
		
		// layout
		$c_xml      = new xmlparser('../../'.$tcms_administer_site.'/tcms_global/layout.xml','r');
		$adminTheme = $c_xml->read_section('layout', 'admin');
		
		
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<title>toendaCMS Imagebrowser | '.$sitetitle.'</title>
		<meta http-equiv="Content-Type" content="text/html; charset='.$c_charset.'" />
		<meta name="generator" content="'.$cms_name.' - '.$cms_tagline.' | Copyright '.$toenda_copy.' Toenda Software Development.  All rights reserved." />
		<script language="javascript" type="text/javascript" src="../js/tinymce/tiny_mce.js"></script>
		<script language="JavaScript" type="text/javascript" src="../js/dhtml.js"></script>
		<link href="theme/'.$adminTheme.'/tcms_main.css" rel="stylesheet" type="text/css" />
		</style>
		</head>
		<body>';
		
		
		echo '<div class="tcms_help_window_top">'
		.'<strong class="tcms_help_window_top_font">&nbsp;'._TABLE_LINKBROWSER.'</strong>'
		.'</div>';
		
		echo '<div style="margin: 7px; padding: 0px;">'
		._TABLE_LINKBROWSER_TEXT
		.'</div>';
		
		echo '<hr />';
		
		
		$xml = new xmlparser('../../'.$tcms_administer_site.'/tcms_global/layout.xml','r');
		$lb_layout = $xml->read_section('layout', 'select');
		
		$iKey = 0;
		
		
		echo '<div style="margin: 5px !important;">';
		
		echo tcms_html::table_head('5', '0', '0', '100%');
		
		echo '<tr>';
		
		echo '<td class="tcms_browser_bottom" colspan="3">'
		.'<h3>'._SIDEEXT_MODUL.'</h3>'
		.'</td>';
		
		echo '</tr>';
		
		
		//*********************************************************************************
		
		
		/*
			search
			module
		*/
		$dvalue = '?id=search'; //&amp;s='.$lb_layout;
		
		if($seoEnabled == 1){
			$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
			$dvalue = '/'.$dvalue;
		}
		else{
			//$dvalue = '/'.$dvalue;
		}
		
		echo '<tr>';
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<strong>'._TCMS_MENU_SEARCH.'</strong>'
		.'</td>'
		.'<td class="tcms_browser_bottom" width="200">'
		.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'._TCMS_MENU_SEARCH.'" />'
		.'</td>';
		
		$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
		.'</td>';
		
		echo '</tr>';
		
		$iKey++;
		
		
		//*********************************************************************************
		
		
		/*
			poll
			module
		*/
		$dvalue = '?id=polls'; //&amp;s='.$lb_layout;
		
		if($seoEnabled == 1){
			$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
			$dvalue = '/'.$dvalue;
		}
		
		echo '<tr>';
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<strong>'._TCMS_MENU_POLL.'</strong>'
		.'</td>'
		.'<td class="tcms_browser_bottom" width="200">'
		.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'._TCMS_MENU_POLL.'" />'
		.'</td>';
		
		$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
		.'</td>';
		
		echo '</tr>';
		
		$iKey++;
		
		
		//*********************************************************************************
		
		
		/*
			impressum
			module
		*/
		$dvalue = '?id=impressum'; //&amp;s='.$lb_layout;
		
		if($seoEnabled == 1){
			$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
			$dvalue = '/'.$dvalue;
		}
		
		echo '<tr>';
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<strong>'._TCMS_MENU_IMP.'</strong>'
		.'</td>'
		.'<td class="tcms_browser_bottom" width="200">'
		.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'._TCMS_MENU_IMP.'" />'
		.'</td>';
		
		$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
		.'</td>';
		
		echo '</tr>';
		
		$iKey++;
		
		
		//*********************************************************************************
		
		
		/*
			contactform
			module
		*/
		$dvalue = '?id=contactform'; //&amp;s='.$lb_layout;
		
		if($seoEnabled == 1){
			$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
			$dvalue = '/'.$dvalue;
		}
		
		echo '<tr>';
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<strong>'._TCMS_MENU_CFORM.'</strong>'
		.'</td>'
		.'<td class="tcms_browser_bottom" width="200">'
		.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'._TCMS_MENU_CFORM.'" />'
		.'</td>';
		
		$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
		.'</td>';
		
		echo '</tr>';
		
		$iKey++;
		
		
		//*********************************************************************************
		
		
		/*
			frontpage
			module
		*/
		$dvalue = '?id=frontpage'; //&amp;s='.$lb_layout;
		
		if($seoEnabled == 1){
			$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
			$dvalue = '/'.$dvalue;
		}
		
		echo '<tr>';
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<strong>'._TCMS_MENU_FRONT.'</strong>'
		.'</td>'
		.'<td class="tcms_browser_bottom" width="200">'
		.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'._TCMS_MENU_FRONT.'" />'
		.'</td>';
		
		$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
		.'</td>';
		
		echo '</tr>';
		
		$iKey++;
		
		
		//*********************************************************************************
		
		
		/*
			guestbook
			module
		*/
		$dvalue = '?id=guestbook'; //&amp;s='.$lb_layout;
		
		if($seoEnabled == 1){
			$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
			$dvalue = '/'.$dvalue;
		}
		
		echo '<tr>';
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<strong>'._TCMS_MENU_QBOOK.'</strong>'
		.'</td>'
		.'<td class="tcms_browser_bottom" width="200">'
		.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'._TCMS_MENU_QBOOK.'" />'
		.'</td>';
		
		$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
		.'</td>';
		
		echo '</tr>';
		
		$iKey++;
		
		
		//*********************************************************************************
		
		
		/*
			links
			module
		*/
		$dvalue = '?id=links'; //&amp;s='.$lb_layout;
		
		if($seoEnabled == 1){
			$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
			$dvalue = '/'.$dvalue;
		}
		
		echo '<tr>';
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<strong>'._TCMS_MENU_LINK.'</strong>'
		.'</td>'
		.'<td class="tcms_browser_bottom" width="200">'
		.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'._TCMS_MENU_LINK.'" />'
		.'</td>';
		
		$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
		.'</td>';
		
		echo '</tr>';
		
		$iKey++;
		
		
		//*********************************************************************************
		
		
		/*
			newsmanager
			module
		*/
		$dvalue = '?id=newsmanager'; //&amp;s='.$lb_layout;
		
		if($seoEnabled == 1){
			$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
			$dvalue = '/'.$dvalue;
		}
		
		echo '<tr>';
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<strong>'._TCMS_MENU_NEWS.'</strong>'
		.'</td>'
		.'<td class="tcms_browser_bottom" width="200">'
		.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'._TCMS_MENU_NEWS.'" />'
		.'</td>';
		
		$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
		
		echo '<td class="tcms_browser_bottom" width="100">'
		.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
		.'</td>';
		
		echo '</tr>';
		
		$iKey++;
		
		
		//*********************************************************************************
		
		
		/*
			documents
			module
		*/
		if($id_group == 'Developer' || $id_group == 'Administrator'){
			echo '<tr>';
			
			echo '<td class="tcms_browser_bottom" colspan="3">'
			.'<h3>'._TCMS_MENU_CONTENT.'</h3>'
			.'</td>';
			
			echo '</tr>';
			
			if($choosenDB == 'xml'){
				$arrDocuments = $tcms_main->readdir_ext('../../'.$tcms_administer_site.'/tcms_content/');
				
				if(is_array($arrDocuments)){
					foreach($arrDocuments as $key => $val){
						if($val != 'index.html'){
							$xml = new xmlparser('../../'.$tcms_administer_site.'/tcms_content/'.$val,'r');
							$xmlID = $xml->read_section('main', 'id');
							$xmlAccess = $xml->read_section('main', 'access');
							
							$xmlTitle = $xml->read_section('main', 'title');
							$xmlTitle = $tcms_main->decodeText($xmlTitle, '2', $c_charset);
							
							
							$dvalue = '?id='.$xmlID.''; //&amp;s='.$lb_layout;
							
							if($seoEnabled == 1){
								$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
								$dvalue = '/'.$dvalue;
							}
							
							echo '<tr>'
							.'<td class="tcms_browser_bottom" width="100">'
							.'<strong>'._TCMS_MENU_CONTENT.': '
							.( $xmlAccess == 'Public' ? '' : '*' )
							.' </strong>'.$xmlTitle
							.'</td>'
							.'<td class="tcms_browser_bottom" width="200">'
							.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'.$xmlTitle.'" />'
							.'</td>';
							
							$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
							
							echo '<td class="tcms_browser_bottom" width="100">'
							.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
							.'</td>';
							
							echo '</tr>';
							
							$iKey++;
						}
					}
				}
			}
			else{
				$sqlAL = new sqlAbstractionLayer($choosenDB);
				$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
				
				$sqlQR = $sqlAL->sqlGetAll($tcms_db_prefix.'content');
				
				$count = 0;
				
				while($sqlARR = $sqlAL->sqlFetchArray($sqlQR)){
					$xmlID = $sqlARR['uid'];
					$xmlAccess = $sqlARR['access'];
					
					if($xmlID == NULL){ $xmlID = ''; }
					if($xmlAccess == NULL){ $xmlAccess = ''; }
					
					$xmlTitle = $sqlARR['title'];
					$xmlTitle = $tcms_main->decodeText($xmlTitle, '2', $c_charset);
					
					
					$dvalue = '?id='.$xmlID.''; //&amp;s='.$lb_layout;
					
					if($seoEnabled == 1){
						$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
						$dvalue = '/'.$dvalue;
					}
					
					echo '<tr>'
					.'<td class="tcms_browser_bottom" width="100">'
					.'<strong>'._TCMS_MENU_CONTENT.':'
					.( $xmlAccess == 'Public' ? '' : '*' )
					.' </strong>'.$xmlTitle
					.'</td>'
					.'<td class="tcms_browser_bottom" width="200">'
					.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'.$xmlTitle.'" />'
					.'</td>';
					
					$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
					
					echo '<td class="tcms_browser_bottom" width="100">'
					.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
					.'</td>';
					
					echo '</tr>';
					
					$iKey++;
				}
				
				$sqlAL->sqlFreeResult($sqlQR);
			}
		}
		
		
		//*********************************************************************************
		
		
		/*
			documents
			module
		*/
		echo '<tr>';
		
		echo '<td class="tcms_browser_bottom" colspan="3">'
		.'<h3>'._TCMS_MENU_GALLERY.'</h3>'
		.'</td>';
		
		echo '</tr>';
		
		if($choosenDB == 'xml'){
			$arrAlbums = $tcms_main->readdir_ext('../../'.$tcms_administer_site.'/tcms_imagegallery/');
			
			if(is_array($arrAlbums)){
				foreach($arrAlbums as $key => $val){
					if($val != 'index.html'){
						$arrImages = $tcms_main->readdir_ext('../../'.$tcms_administer_site.'/tcms_imagegallery/'.$val.'/');
						
						$xml = new xmlparser('../../'.$tcms_administer_site.'/tcms_albums/album_'.$val.'.xml','r');
						$xmlAlbum = $xml->read_section('album', 'title');
						$xmlPath = $xml->read_section('album', 'path');
						
						$xmlAlbum = $tcms_main->decodeText($xmlAlbum, '2', $c_charset);
						
						$dvalue = '?id=imagegallery&amp;s='.$lb_layout.'&amp;album='.$xmlPath;
						
						if($seoEnabled == 1){
							$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
							$dvalue = '/'.$dvalue;
						}
						
						echo '<tr>'
						.'<td class="tcms_browser_bottom" width="100">'
						.'<strong>'._TABLE_ALBUM.': </strong>'.$xmlAlbum
						.'</td>'
						.'<td class="tcms_browser_bottom" width="200">'
						.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'.$xmlAlbum.'" />'
						.'</td>';
						
						$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
						
						echo '<td class="tcms_browser_bottom" width="100">'
						.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
						.'</td>';
						
						echo '</tr>';
						
						$iKey++;
						
						if(is_array($arrImages)){
							foreach($arrImages as $key2 => $val2){
								if($val2 != 'index.html'){
									$xml = new xmlparser('../../'.$tcms_administer_site.'/tcms_imagegallery/'.$val.'/'.$val2,'r');
									$xmlTitle = $xml->read_section('image', 'text');
									
									$xmlTitle = $tcms_main->decodeText($xmlTitle, '2', $c_charset);
									
									$val2 = substr($val2, 0, strrpos($val2, '.xml'));
									
									$dvalue = 'media.php?album='.$val.'&amp;key='.$val2;
									
									if($seoEnabled == 1) $dvalue = '/'.$seoFolder.'/'.$dvalue;
									
									echo '<tr>'
									.'<td class="tcms_browser_bottom" width="100">'
									.'<strong>'._TCMS_MENU_CONTENT.': </strong>'.$xmlTitle
									.'</td>'
									.'<td class="tcms_browser_bottom" width="200">'
									.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'.$xmlTitle.'" />'
									.'</td>';
									
									$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, $val2, $tcms_administer_site, $val, $url);
									
									echo '<td class="tcms_browser_bottom" width="100">'
									.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
									.'</td>';
									
									echo '</tr>';
									
									$iKey++;
								}
							}
						}
					}
				}
			}
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			$sqlQR = $sqlAL->sqlGetAll($tcms_db_prefix.'imagegallery ORDER BY album, date');
			
			$count = 0;
			$xmlAlbum2 = '';
			
			while($sqlARR = $sqlAL->sqlFetchArray($sqlQR)){
				$xmlID    = $sqlARR['image'];
				$xmlAlbum = $sqlARR['album'];
				
				$xmlTitle = $sqlARR['text'];
				$xmlTitle = $tcms_main->decodeText($xmlTitle, '2', $c_charset);
				
				
				if($xmlAlbum != $xmlAlbum2){
					$dvalue = '?id=imagegallery&amp;s='.$lb_layout.'&amp;album='.$xmlAlbum;
					
					if($seoEnabled == 1){
						$dvalue = $tcms_main->urlAmpReplace($dvalue, $seoFormat);
						$dvalue = '/'.$dvalue;
					}
					
					echo '<tr>'
					.'<td class="tcms_browser_bottom" width="100">'
					.'<strong>'._TABLE_ALBUM.': </strong>'.$xmlAlbum
					.'</td>'
					.'<td class="tcms_browser_bottom" width="200">'
					.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'.$xmlAlbum.'" />'
					.'</td>';
					
					$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, 1, '', '', $url);
					
					echo '<td class="tcms_browser_bottom" width="100">'
					.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
					.'</td>';
					
					echo '</tr>';
					
					$iKey++;
				}
				
				$dvalue = 'media.php?album='.$xmlAlbum.'&amp;key='.$xmlID;
				
				if($seoEnabled == 1) $dvalue = '/'.$seoFolder.'/'.$dvalue;
				
				echo '<tr>'
				.'<td class="tcms_browser_bottom" width="100">'
				.'<strong>'._TCMS_MENU_CONTENT.': </strong>'.$xmlTitle
				.'</td>'
				.'<td class="tcms_browser_bottom" width="200">'
				.'<input type="text" name="lb_title" id="lb_title_'.$iKey.'" class="tcms_input_small" value="'.$xmlTitle.'" />'
				.'</td>';
				
				$cmdImage = $tcms_main->returnInsertCommand($n, $show_wysiwyg, $dvalue, $v, $iKey, $xmlID, $tcms_administer_site, $xmlAlbum, $url);
				
				echo '<td class="tcms_browser_bottom" width="100">'
				.'<a class="tcms_edit" href="javascript:'.$cmdImage.';">'._TABLE_ACCEPTBUTTON.'</a>'
				.'</td>';
				
				echo '</tr>';
				
				
				$xmlAlbum2 = $xmlAlbum;
				
				$iKey++;
			}
			
			$sqlAL->sqlFreeResult($sqlQR);
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		/*
		if(!in_array('knowledgebase', $arrXMLID)){
			$arr_linkcom['name'][$i] = _TCMS_MENU_FAQ;
			$arr_linkcom['link'][$i] = 'knowledgebase';
			$i++;
		}
		
		if(!in_array('download', $arrXMLID)){
			$arr_linkcom['name'][$i] = _TCMS_MENU_DOWN;
			$arr_linkcom['link'][$i] = 'download';
			$i++;
		}
		
		
		if(!in_array('products', $arrXMLID)){
			$arr_linkcom['name'][$i] = _TCMS_MENU_PRODUCTS;
			$arr_linkcom['link'][$i] = 'products';
			$i++;
		}
		*/
		
		
		echo tcms_html::table_end();
		
		echo '</div>';
	}
	else{
		echo '<div align="center" style=" padding: 100px 10px 100px 10px; border: 1px solid #333; background-color: #f8f8f8; font-family: Georgia, \'Lucida Grande\', \'Lucida Sans\', Serif;">'
		.'<h1>'._MSG_SESSION_EXPIRED.'</h2>'
		.'</div>';
	}
}
else{
	echo '<div align="center" style=" padding: 100px 10px 100px 10px; border: 1px solid #333; background-color: #f8f8f8; font-family: Georgia, \'Lucida Grande\', \'Lucida Sans\', Serif;">'
	.'<h1>'._DIE_LOGIN.'</h2>'
	.'</div>';
}


echo '</body></html>';

?>
