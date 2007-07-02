<?php /* _\|/_
         (o o)
+-----oOO-{_}-OOo--------------------------------------------------------+
| toendaCMS - Content Management and Weblogging System with XML and SQL  |
+------------------------------------------------------------------------+
| Copyright (c) Toenda Software Development                              |
| Author: Jonathan Naumann                                               |
+------------------------------------------------------------------------+
| 
| JavaScript Window for ImageGallery
|
| File:	media.php
|
+
*/


/**
 * JavaScript Window for ImageGallery
 *
 * This module is used as a image viewer.
 *
 * @version 0.5.0
 * @author	Jonathan Naumann <jonathan@toenda.com>
 * @package toendaCMS
 * @subpackage toendaCMS
 */


if(isset($_GET['album'])){ $album = $_GET['album']; }
if(isset($_GET['key'])){ $key = $_GET['key']; }
if(isset($_GET['cmd'])){ $cmd = $_GET['cmd']; }
if(isset($_GET['uid'])){ $uid = $_GET['uid']; }
if(isset($_GET['defaultSizeX'])){ $defaultSizeX = $_GET['defaultSizeX']; }
if(isset($_GET['defaultSizeY'])){ $defaultSizeY = $_GET['defaultSizeY']; }
if(isset($_GET['session'])){ $session = $_GET['session']; }
if(isset($_GET['XMLplace'])){ $XMLplace = $_GET['XMLplace']; }
if(isset($_GET['XMLfile'])){ $XMLfile = $_GET['XMLfile']; }

if(isset($_POST['session'])){ $session = $_POST['session']; }
if(isset($_POST['album'])){ $album = $_POST['album']; }
if(isset($_POST['key'])){ $key = $_POST['key']; }
if(isset($_POST['cmd'])){ $cmd = $_POST['cmd']; }
if(isset($_POST['uid'])){ $uid = $_POST['uid']; }
if(isset($_POST['comment_name'])){ $comment_name = $_POST['comment_name']; }
if(isset($_POST['comment_email'])){ $comment_email = $_POST['comment_email']; }
if(isset($_POST['comment_web'])){ $comment_web = $_POST['comment_web']; }
if(isset($_POST['comment_text'])){ $comment_text = $_POST['comment_text']; }
if(isset($_POST['comment_captcha'])){ $comment_captcha = $_POST['comment_captcha']; }
if(isset($_POST['check_captcha'])){ $check_captcha = $_POST['check_captcha']; }





//=====================================================
// INIT
//=====================================================

if(!isset($defaultSizeX))
	$defaultSizeX = '640';

if(!isset($defaultSizeY))
	$defaultSizeY = '480';

$sidebarWidth = 250;

if(!isset($dvalue))
	$dvalue = '';


define('_TCMS_VALID', 1);

include_once('engine/tcms_kernel/tcms.lib.php');
include_once('engine/tcms_kernel/tcms_time.lib.php');
include_once('engine/tcms_kernel/tcms_xml.lib.php');
include_once('engine/tcms_kernel/tcms_gd.lib.php');
include_once('engine/tcms_kernel/tcms_html.lib.php');
include_once('engine/tcms_kernel/tcms_sql.lib.php');
include_once('engine/tcms_kernel/tcms_configuration.lib.php');
include_once('engine/tcms_kernel/tcms_version.lib.php');


tcms_time::tcms_load_start();
if($choosenDB != 'xml')
	tcms_time::tcms_query_count_start();
	

$tcms_administer_site = 'data';
require($tcms_administer_site.'/tcms_global/database.php');


// version
$tcms_version = new tcms_version();

$toenda_copy  = $tcms_version->getToendaCopyright();


// config
$tcms_config  = new tcms_configuration($tcms_administer_site);

$use_captcha  = $tcms_config->getCaptchaEnabled();
$tcmsinst     = $tcms_config->getToendaCMSInSitetitle();


// language
$language_stage = 'index';
include_once('engine/language/lang_admin.php');


// main obj
$tcms_main = new tcms_main('data', $choosenDB);

$choosenDB = $tcms_main->secure_password($tcms_db_engine, 'en');
$sqlUser   = $tcms_main->secure_password($tcms_db_user, 'en');
$sqlPass   = $tcms_main->secure_password($tcms_db_password, 'en');
$sqlHost   = $tcms_main->secure_password($tcms_db_host, 'en');
$sqlDB     = $tcms_main->secure_password($tcms_db_database, 'en');
$sqlPort   = $tcms_main->secure_password($tcms_db_port, 'en');
$sqlPrefix = $tcms_main->secure_password($tcms_db_prefix, 'en');
$tcms_db_prefix = $sqlPrefix;

$tcms_main->setDatabaseInfo($choosenDB);


if($choosenDB == 'xml'){
	$album_xml   = new xmlparser($tcms_administer_site.'/tcms_albums/album_'.$album.'.xml', 'r');
	$album_title = $album_xml->read_section('album', 'title');
	
	$album_title  = $tcms_main->decodeText($album_title, '2', $c_charset);
}
else{
	$sqlAL = new sqlAbstractionLayer($choosenDB);
	$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
	
	$sqlQR = $sqlAL->sqlQuery("SELECT * FROM ".$tcms_db_prefix."albums WHERE album_id='".$album."'");
	$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
	
	$album_title = $sqlARR['title'];
	
	$album_title  = $tcms_main->decodeText($album_title, '2', $c_charset);
}


if(!isset($s)){
	$layout_xml = new xmlparser($tcms_administer_site.'/tcms_global/layout.xml','r');
	$s = $layout_xml->read_section('layout', 'select');
}

$namen_xml = new xmlparser($tcms_administer_site.'/tcms_global/namen.xml','r');
$sitetitle = $namen_xml->read_section('namen', 'title');
$sitename  = $namen_xml->read_section('namen', 'name');
$sitekey   = $namen_xml->read_section('namen', 'key');

// CHARSETS
$sitetitle  = $tcms_main->decodeText($sitetitle, '2', $c_charset);
$sitename   = $tcms_main->decodeText($sitename, '2', $c_charset);
$sitekey    = $tcms_main->decodeText($sitekey, '2', $c_charset);





//=====================================================
// HTML HEADER
//=====================================================

$arrCSS = $tcms_main->getPathContentCSSFilesRecursivly('theme/'.$s);

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>'.( $tcmsinst == 1 ? 'toendaCMS ' : '' ).'Imagebrowser | '.$sitetitle.'</title>
<meta http-equiv="Content-Type" content="text/html; charset='.$c_charset.'" />
<meta name="generator" content="'.$tcms_version->getName().' - '.$tcms_version->getTagline().'! - Version '.$tcms_version->getVersion().' '.$tcms_version->getBuild().' | Copyright '.$tcms_version->getToendaCopyright().' Toenda Software Development. '._TCMS_ADMIN_RIGHT.'" />

<!-- CSS files from current theme -->';

if($tcms_main->isArray($arrCSS)) {
	foreach($arrCSS['files'] as $cssKey => $cssVal){
		echo '
<link href="theme/'.$s.'/'.$arrCSS['dir'][0].'/'.$arrCSS['files'][$cssKey].'" rel="stylesheet" type="text/css" />
		';
	}
}

echo '
<!--
 This website is powered by '.$tcms_version->getName().' - '.$tcms_version->getTagline().'!
 Version '.$tcms_version->getVersion().' - '.$tcms_version->getBuild().'
 '.$tcms_version->getName().' is a free open source Content Management Framework created by Jonathan Naumann and licensed under the GNU/GPL license.
 '.$tcms_version->getName().' is copyright (c) '.$tcms_version->getToendaCopyright().' of Toenda Software Development.
 Components are copyright (c) of their respective owners.
 Information and contribution at http://www.toendacms.com
-->

<style>

body{
	margin: 0;
	padding: 0;
	background: #fff !important;
	font-size: 12px;
	font-family: Verdana, Arial, Helvetica, Sans;
}

#media{
	margin: 0 auto 0 auto;
	width: 100%;
	padding: 0;
	background: #fff !important;
}

a.media { text-decoration: none; }
a.media:hover { text-decoration: underline; }

.text{
	color: #555;
	font-size: 12px;
}

.topcorner{
	 margin: 0 auto 0 auto;
	 padding: 4px 0 4px 0;
	 background: #efefef;
	 border: 1px solid #ccc;
	 border-top: 0px;
}

.t1{
	font-family: "Century Gothic", "Lucida Grande", Verdana, Arial, Sans-Serif;
	font-size: 15px;
}

.t2{
	font-family: "Century Gothic", "Lucida Grande", Verdana, Arial, Sans-Serif;
	font-size: 12px;
}

</style>
</head>

<body>

<div id="media">
';





//=====================================================
// LOAD ALBUMS
//=====================================================

$arr_dir = $tcms_main->readdir_ext($tcms_administer_site.'/images/albums/'.$album.'/');

if(is_array($arr_dir) && !empty($arr_dir)){
	if($choosenDB == 'xml'){
		$timecc = 0;
		foreach($arr_dir as $akey => $aval){
			$des_xml = new xmlparser($tcms_administer_site.'/tcms_imagegallery/'.$album.'/'.$aval.'.xml','r');
			$arr_tc['time'][$timecc] = $des_xml->read_section('image', 'timecode');
			$arr_tc['desc'][$timecc] = $des_xml->read_section('image', 'text');
			$arr_tc['file'][$timecc] = $aval;
			$arr_tc['uid'][$timecc]  = $aval;
			
			$arr_tc['desc'][$timecc]  = $tcms_main->decodeText($arr_tc['desc'][$timecc], '2', $c_charset);
			
			$timecc++;
		}
	}
	else{
		$sqlAL = new sqlAbstractionLayer($choosenDB);
		$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
		
		$sqlQR = $sqlAL->sqlGetAll($tcms_db_prefix."imagegallery WHERE album='".$album."'");
		
		$timecc = 0;
		
		while($sqlARR = $sqlAL->sqlFetchArray($sqlQR)){
			$arr_tc['uid'][$timecc]  = $sqlARR['uid'];
			$arr_tc['time'][$timecc] = $sqlARR['date'];
			$arr_tc['desc'][$timecc] = $sqlARR['text'];
			$arr_tc['file'][$timecc] = $sqlARR['image'];
			
			if($arr_tc['time'][$timecc] == NULL){ $arr_tc['time'][$timecc] = ''; }
			if($arr_tc['desc'][$timecc] == NULL){ $arr_tc['desc'][$timecc] = ''; }
			if($arr_tc['file'][$timecc] == NULL){ $arr_tc['file'][$timecc] = ''; }
			
			$arr_tc['desc'][$timecc]  = $tcms_main->decodeText($arr_tc['desc'][$timecc], '2', $c_charset);
			
			$timecc++;
		}
	}
	
	if(is_array($arr_tc)){
		array_multisort(
			$arr_tc['time'], SORT_DESC, 
			$arr_tc['desc'], SORT_DESC, 
			$arr_tc['file'], SORT_DESC, 
			$arr_tc['uid'], SORT_DESC
		);
	}
}





//=====================================================
// DISPLAY IMAGE
//=====================================================

if(is_array($arr_tc['time']) && !empty($arr_tc['time'])){
	foreach($arr_tc['time'] as $iKey => $iVal){
		if($key == $arr_tc['file'][$iKey]){
			echo '<div align="center">';
			
			$img_size = getimagesize($tcms_administer_site.'/images/albums/'.$album.'/'.$arr_tc['file'][$iKey]);
			$img_o_width  = $img_size[0];
			$img_o_height = $img_size[1];
			
			
			echo '<div style="width: '.( $defaultSizeX + $sidebarWidth ).'px;" align="left" class="topcorner">';
			echo '<strong class="t1" style="padding: 0 0 0 10px; color: #333;">'.$sitename.'</strong><br />';
			echo '<strong class="t2" style="padding: 0 0 0 10px; color: #333;">'.$sitekey.'</strong>';
			echo '</div>';
			
			
			echo '<br /><div style="width: '.( $defaultSizeX + $sidebarWidth ).'px; margin: 0 auto 0 auto; background: #fff; padding: 7px;" align="left">';
			echo '<h1 style="color: #4477aa;">'._TABLE_IMAGE.' <a style="color: #4477aa; font-size: 11px;" href="javascript:self.close();" title="'._TCMS_ADMIN_CLOSE.'">('._TCMS_ADMIN_CLOSE.')</a></h1>';	
			echo '<hr class="hr_line" noshade="noshade" />';
			echo '</div>';
			
			
			/**********************
			*
			* HTML Thumbnail list
			*/
			
			echo tcms_html::table_head_CL(0, 0, 0, ( $defaultSizeX + $sidebarWidth ), 'text_small');
			echo '<tr>';
			echo '<td align="center" width="'.( $defaultSizeX ).'">';
			
			if($img_o_width > $defaultSizeX){
				if(!is_dir($tcms_administer_site.'/thumbnails/'.$album.'/')){ mkdir($tcms_administer_site.'/thumbnails/'.$album.'/', 0777); }
				
				if(!file_exists($tcms_administer_site.'/thumbnails/'.$album.'/medium_thumb_'.$arr_tc['file'][$iKey])){
					tcms_gd::gd_thumbnail($tcms_administer_site.'/images/albums/'.$album.'/', $tcms_administer_site.'/thumbnails/'.$album.'/medium_', $arr_tc['file'][$iKey], $defaultSizeX, 'create');
				}
				
				echo '<img alt="" style="border: 1px solid #333; background: #eee; margin: 0 0 10px 0;" src="'.$tcms_administer_site.'/thumbnails/'.$album.'/medium_thumb_'.$arr_tc['file'][$iKey].'" border="0" /><br />'
				.'</span>';
			}
			else{
				echo '<img alt="" style="border: 1px solid #333; background: #eee; margin: 0 0 10px 0;" src="'.$tcms_administer_site.'/images/albums/'.$album.'/'.$arr_tc['file'][$iKey].'" border="0" /><br />'
				.'</span>';
			}
			echo '</td>';
			
			
			
			echo '<td valign="top" width="20">';
			echo '</td>';
			
			
			
			echo '<td valign="top" align="center" width="'.$sidebarWidth.'" style="background: #efefef; border: 1px solid #ccc;"><br />';
			
			echo '<div style="display: block; height: 90px;">';
			
			if(!empty($arr_tc['file'][$iKey - 1])){
				echo '<a href="?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'album='.$album.'&amp;key='.($arr_tc['file'][$iKey - 1]).'">'
				.'<img style="border: 1px solid #ccc; margin-right: 3px; background: #eee;" src="'.$tcms_administer_site.'/thumbnails/'.$album.'/thumb_'.$arr_tc['file'][$iKey - 1].'" border="0" />'
				.'</a>';
			}
			else{ echo '<img style="border: 1px solid #ccc; margin-right: 3px; background: #eee; width: 100px; height: 75px;" src="engine/images/no_picture.gif" border="0" />'; }
			
			if(!empty($arr_tc['file'][$iKey + 1])){
				echo '<a href="?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'album='.$album.'&amp;key='.($arr_tc['file'][$iKey + 1]).'">'
				.'<img style="border: 1px solid #ccc; background: #eee;" src="'.$tcms_administer_site.'/thumbnails/'.$album.'/thumb_'.$arr_tc['file'][$iKey + 1].'" border="0" />'
				.'</a>';
			}
			else{ echo '<img style="border: 1px solid #ccc; background: #eee; width: 100px; height: 75px;" src="engine/images/no_picture.gif" border="0" />'; }
			
			echo '</div>';
			
			//echo '<br />';
			
			if(!empty($arr_tc['file'][$iKey - 1])){
				echo '<a href="?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'album='.$album.'&amp;key='.($arr_tc['file'][$iKey - 1]).'">'
				.'<img src="engine/images/left.png" border="0" alt="'._TCMS_ADMIN_BACK.'" title="'._TCMS_ADMIN_BACK.'" />'
				.'</a>';
			}
			else{ echo '<img style="width: 16px; height: 16px;" alt="" src="engine/images/px.png" border="0" />'; }
			
			echo '&nbsp;&nbsp;';
			
			if(!empty($arr_tc['file'][$iKey + 1])){
				echo '<a href="?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'album='.$album.'&amp;key='.($arr_tc['file'][$iKey + 1]).'">'
				.'<img src="engine/images/right.png" border="0" alt="'._TCMS_ADMIN_FORWARD.'" title="'._TCMS_ADMIN_FORWARD.'" />'
				.'</a>';
			}
			else{ echo '<img style="width: 16px; height: 16px;" alt="" src="engine/images/px.png" border="0" />'; }
			
			
			echo '<div align="left" style="margin: 10px 0 0 10px;"><span class="text"><strong>'._TABLE_ALBUM.':</strong> '.$album_title.'<br />';
			echo '<strong>'._GALLERY_IMGTITLE.':</strong> '.$arr_tc['file'][$iKey].'<br />'
			.'<strong>'._GALLERY_AMOUNT.':</strong> '.( $iKey + 1 ).'/'.$timecc.'<br />'
			.'<strong>'._GALLERY_IMGRESOLUTION.':</strong> <a class="media" href="?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'album='.$album.'&amp;key='.$arr_tc['file'][$iKey].'&amp;defaultSizeX='.$img_o_width.'">'.$img_o_width.'x'.$img_o_height.'</a><br />'
			.'<strong>'._GALLERY_POSTED.':</strong> '.lang_date(substr($iVal, 6, 2), substr($iVal, 4, 2), substr($iVal, 0, 4), substr($iVal, 8, 2), substr($iVal, 10, 2), substr($iVal, 12, 2)).'<br /><br />';
			echo '<strong>'._GALLERY_DESCRIPTION.'</strong><br />';
			echo $arr_tc['desc'][$iKey];
			echo '</span></div>';
			
			echo '</td>';
			
			echo '</tr>';
			echo tcms_html::table_end();
			
			/*
			* End list
			*
			*********************/
			
			echo '</div>';
			
			$takeME = $iKey;
		}
	}
}





//=====================================================
// DISPLAY COMMENT
//=====================================================

if($choosenDB == 'xml'){
	$pro_xml = new xmlparser($tcms_administer_site.'/tcms_global/imagegallery.xml','r');
	$show_comments = $pro_xml->read_section('config', 'image_comments');
}
else{
	$sqlAL = new sqlAbstractionLayer($choosenDB);
	$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
	
	$sqlQR = $sqlAL->sqlGetOne($tcms_db_prefix.'imagegallery_config', 'imagegallery');
	$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
	
	$show_comments = $sqlARR['use_comments'];
	
	if($show_comments == NULL){ $show_comments = ''; }
}


if($choosenDB == 'xml'){
	$news_xml = new xmlparser($tcms_administer_site.'/tcms_global/newsmanager.xml','r');
	$use_gravatar  = $news_xml->read_section('config', 'use_gravatar');
	$use_emoticons = $news_xml->read_section('config', 'use_emoticons');
}
else{
	$sqlAL = new sqlAbstractionLayer($choosenDB);
	$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
	
	$sqlQR = $sqlAL->sqlGetOne($tcms_db_prefix.'newsmanager', 'newsmanager');
	$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
	
	$use_gravatar  = $sqlARR['use_gravatar'];
	$use_emoticons = $sqlARR['use_emoticons'];
	
	if($use_gravatar  == NULL){ $use_gravatar  = ''; }
	if($use_emoticons == NULL){ $use_emoticons = ''; }
}

if($show_comments == 1){
	/* Create userrights */
	$check_session = false;
	if($choosenDB == 'xml'){
		if(isset($session) && $session != '' && file_exists($tcms_administer_site.'/tcms_session/'.$session) && filesize($tcms_administer_site.'/tcms_session/'.$session) != 0){ $check_session = true; }
		else{ $check_session = false; }
	}
	else{ $check_session = $tcms_main->check_session_exists($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort, $session); }
	
	if($check_session){
		if($choosenDB == 'xml'){
			$arr_ws = $tcms_main->create_username($session);
			
			$ws_user = $arr_ws['user'];
			$ws_id   = $arr_ws['id'];
		}
		else{
			$arr_ws = $tcms_main->create_sql_username($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort, $session);
			
			$ws_user = $arr_ws['user'];
			$ws_id   = $arr_ws['id'];
		}
		
		if($choosenDB == 'xml'){
			$authXML  = new xmlparser($tcms_administer_site.'/tcms_user/'.$ws_id.'.xml', 'r');
			$is_admin = $authXML->read_section('user', 'group');
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			$sqlQR = $sqlAL->sqlGetOne($tcms_db_prefix.'user', $ws_id);
			$sqlARR = $sqlAL->sqlFetchArray($sqlQR);
			$is_admin     = $sqlARR['group'];
			if($is_admin == NULL){ $is_admin = ''; }
		}
	}
	
	
	echo '<br /><div style="width: '.( $defaultSizeX + $sidebarWidth ).'px; margin: 0 auto 0 auto; background: #fff; padding: 7px; text-align: left;" align="left">';
	echo '<h1 style="color: #4477aa;">'._FRONT_COMMENTS.'</h1>';	
	echo '<hr class="hr_line" noshade="noshade" /><br />';
	echo '<a name="comments"></a>';
	
	//
	// JavaScript for checking inputs
	//
	?><script language="JavaScript">
	function checkinputs(id){
		sendOK = true;
		
		if(document.getElementById(id).comment_name.value == ''){
			alert('<? echo _MSG_NONAME; ?>');
			document.getElementById(id).comment_name.focus();
			sendOK = false;
			return false;
		}
		
		if(document.getElementById(id).comment_email.value == ''){
			alert('<? echo _MSG_NOEMAIL; ?>');
			document.getElementById(id).comment_email.focus();
			sendOK = false;
			return false;
		}
		
		strEmail = document.getElementById(id).comment_email.value;
		if(strEmail.indexOf('@') == -1){
			alert('<? echo _MSG_EMAILVALID; ?>');
			document.getElementById(id).comment_email.focus();
			sendOK = false;
			return false;
		}
		
		if(document.getElementById(id).comment_text.value == ''){
			alert('<? echo _MSG_NOMSG; ?>');
			document.getElementById(id).comment_text.focus();
			sendOK = false;
			return false;
		}
		
		if(sendOK){ document.getElementById(id).submit(); }
	}
	</script><?
	
	
	
	if($choosenDB == 'xml'){
		$arr_comments = $tcms_main->readdir_ext($tcms_administer_site.'/tcms_imagegallery/'.$album.'/comments_'.$key.'/');
		
		if(!empty($arr_comments) && $arr_comments != '' && isset($arr_comments)){
			foreach($arr_comments as $ackey => $acvalue){
				$c_xml = new xmlparser($tcms_administer_site.'/tcms_imagegallery/'.$album.'/comments_'.$key.'/'.$acvalue,'r');
				$arr_c['value'][$ackey] = $acvalue;
				$arr_c['name'][$ackey]  = $c_xml->read_section('comment', 'name');
				$arr_c['email'][$ackey] = $c_xml->read_section('comment', 'email');
				$arr_c['web'][$ackey]   = $c_xml->read_section('comment', 'web');
				$arr_c['msg'][$ackey]   = $c_xml->read_section('comment', 'msg');
				$arr_c['time'][$ackey]  = $c_xml->read_section('comment', 'time');
				
				$arr_c['msg'][$ackey] = $tcms_main->decodeText($arr_c['msg'][$ackey], '2', $c_charset);
			}
			
			array_multisort(
				$arr_c['time'], SORT_ASC, 
				$arr_c['name'], SORT_ASC, 
				$arr_c['email'], SORT_ASC, 
				$arr_c['msg'], SORT_ASC, 
				$arr_c['web'], SORT_ASC, 
				$arr_c['value'], SORT_ASC
			);
			
			$count = 1;
			
			foreach($arr_c['time'] as $cKey => $cVal){
				if($use_gravatar == 1){
					$grav_url = 'http://www.gravatar.com/avatar.php?gravatar_id='.md5($arr_c['email'][$cKey]).'&amp;default='.urlencode('http://www.somewhere.com/homestar.jpg').'&amp;size=32';
					echo '<a href="http://www.gravatar.com" title="What is this?" target="_blank"><img align="right" border="1" src="'.$grav_url.'" alt="?" /></a><br />';
				}
				
				echo '<strong class="comment_title">'.$count.'. ';
				if($arr_c['web'][$cKey] != ''){ echo '<a href="'.$arr_c['web'][$cKey].'">'.$arr_c['name'][$cKey].'</a>'; }
				else{ echo $arr_c['name'][$cKey]; }
				echo '</strong>';
				
				$time = $arr_c['time'][$cKey];
				echo '<span class="text_small" style="padding: 3px 0 0 3px;">'.lang_date(substr($time, 6, 2), substr($time, 4, 2), substr($time, 0, 4), substr($time, 8, 2), substr($time, 10, 2), substr($time, 12, 2)).'</span>';
				
				echo '<hr class="hr_line" noshade="noshade" />';
				
				
				$msg = $arr_c['msg'][$cKey];
				if($use_emoticons == 1)
					$msg = $tcms_main->replaceSmilyTags($msg, $imagePath);
				
				echo '<div class="comment_text" style="color: #222 !important;">';
				echo $msg;
				echo '</div>';
				
				if(isset($session) && file_exists($tcms_administer_site.'/tcms_session/'.$session) && filesize($tcms_administer_site.'/tcms_session/'.$session) != 0){
					if($is_admin == 'Administrator' || $is_admin == 'Developer' || $is_admin == 'Tester'){
						echo '<a class="main" href="?'.( isset($session) ? 'session='.$session.'&' : '' ).'id='.$id.'&s='.$s.'&cmd=delete&XMLplace='.$news.'&XMLfile='.$arr_c['value'][$cKey].'"><strong>'._TCMS_ADMIN_DELETE.'</strong></a>';
					}
				}
				
				echo '<br /><br />';
				
				$count++;
			}
		}
		else{
			echo '<span style="color: #222 !important;">'._FRONT_NOCOMMENT.'</span><br />';
		}
	}
	else{
		$sqlAL = new sqlAbstractionLayer($choosenDB);
		$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
		
		$strSQL = "SELECT * FROM ".$tcms_db_prefix."comments INNER JOIN ".$tcms_db_prefix."imagegallery ON ".$tcms_db_prefix."imagegallery.uid = ".$tcms_db_prefix."comments.uid "
		."WHERE ".$tcms_db_prefix."imagegallery.image = '".$key."' ORDER BY time ASC";
		
		$sqlQR = $sqlAL->sqlQuery($strSQL);
		$sqlNR = $sqlAL->sqlGetNumber($sqlQR);
		
		if($sqlNR != 0){
			$count = 1;
			
			while($sqlARR = $sqlAL->sqlFetchArray($sqlQR)){
				if($use_gravatar == 1){
					$grav_url = 'http://www.gravatar.com/avatar.php?gravatar_id='.md5($sqlARR['email']).'&amp;default='.urlencode('http://www.somewhere.com/homestar.jpg').'&amp;size=32';
					echo '<a href="http://www.gravatar.com" title="What is this?" target="_blank"><img align="right" border="0" src="'.$grav_url.'" alt="?" /></a><br />';
				}
				
				echo '<strong class="comment_title">'.$count.'. ';
				if($sqlARR['web'] != ''){ echo '<a href="'.$sqlARR['web'].'">'.$sqlARR['name'].'</a>'; }
				else{ echo $sqlARR['name']; }
				echo '</strong>';
				
				echo '<span class="text_small" style="padding: 3px 0 0 3px;">'.lang_date(substr($sqlARR['time'], 6, 2), substr($sqlARR['time'], 4, 2), substr($sqlARR['time'], 0, 4), substr($sqlARR['time'], 8, 2), substr($sqlARR['time'], 10, 2), substr($sqlARR['time'], 12, 2)).'</span>';
				
				echo '<hr class="hr_line" noshade="noshade" />';
				
				
				$msg = $tcms_main->decodeText($sqlARR['msg'], '2', $c_charset);
				if($use_emoticons == 1)
					$msg = $tcms_main->replaceSmilyTags($msg, $imagePath);
				
				echo '<div class="comment_text" style="color: #222 !important;">';
				echo $msg;
				echo '</div>';
				
				$check_session = false;
				$check_session = $tcms_main->check_session_exists($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort, $session);
				
				if($check_session){
					if($is_admin == 'Administrator' || $is_admin == 'Developer' || $is_admin == 'Tester'){
						echo '<a class="main" href="?'.( isset($session) ? 'session='.$session.'&' : '' ).'id='.$id.'&s='.$s.'&cmd=delete&XMLplace='.$sqlARR['uid'].'&XMLfile='.$sqlARR['timestamp'].'"><strong>'._TCMS_ADMIN_DELETE.'</strong></a>';
					}
				}
				
				echo '<br /><br />';
				
				$count++;
			}
		}
		else{
			echo '<span style="color: #222 !important;">'._FRONT_NOCOMMENT.'</span><br />';
		}
	}
	
	echo '<hr class="hr_line" noshade="noshade" />';
	
	echo '<br />';
	echo tcms_html::contentheading(_FRONT_COMMENT_TITLE);
	echo '<br />';
	
	echo '<form name="comment" id="comment" action="media.php?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'album='.$album.'&amp;key='.$key.'" method="post">';
	
	//captcha
	if($use_captcha == 1){
		$captchaImage = tcms_gd::createCaptchaImage('cache/captcha/', $captcha_clean);
		
		echo '<div id="captcha"><strong>'._FRONT_CAPTCHA.'</strong>'
		.'</div><br />';
		
		echo '<div style="display: block; float: left; width: 60px;">'
		.'<img src="'.$imagePath.'cache/captcha/'.$captchaImage.'.png" /></div>';
		
		echo '<div style="display: block; margin: 0 0 3px 1px; width: 400px;">'
		.'<input name="comment_captcha" id="comment_captcha" class="inputtext bookfield" type="text" />'
		.'<input name="check_captcha" id="check_captcha" value="'.$captchaImage.'" type="hidden" /></div>';
		
		echo '<br /><br />';
	}
	
	echo '<strong>'._FRONT_COMMENT_NAME.'</strong><br /><input name="comment_name" id="comment_name" class="inputtext" value="" /><br />';
	echo '<strong>'._FRONT_COMMENT_EMAIL.'</strong><br /><input name="comment_email" id="comment_email" class="inputtext" /><br />';
	echo '<strong>'._FRONT_COMMENT_WEB.'</strong><br /><input name="comment_web" id="comment_web" class="inputtext" /><br />';
	echo '<strong>'._FRONT_COMMENT_TEXT.'</strong><br /><textarea name="comment_text" id="comment_text" class="inputtextarea"></textarea><br />';
	
	echo '<input type="hidden" name="cmd" value="comment_save" />';
	echo '<input type="hidden" name="uid" value="'.$arr_tc['uid'][$takeME].'" />';
	echo '<input type="hidden" name="album" value="'.$album.'" />';
	echo '<input type="hidden" name="key" value="'.$key.'" />';
	
	echo '<br /><input class="inputbutton" type="button" onclick="javascript:checkinputs(\'comment\');" value="'._FORM_SUBMIT.'" />';
	
	echo '</form>';
	
	echo '</div>';
}





//=====================================================
// SAVE COMMENT
//=====================================================

if($cmd == 'comment_save' && $show_comments == 1){
	$save_now = true;
	$save_entry = true;
	
	if($use_captcha == 1){
		if($comment_captcha == ''){
			$captcha_msg = _MSG_NOCAPTCHA;
			$save_entry = false;
		}
		
		if($save_entry){
			if($comment_captcha != $check_captcha){
				$captcha_msg = _MSG_CAPTCHA_NOT_VALID;
				$save_entry = false;
			}
		}
		
		if(!$save_entry){
			$link = '?'.( isset($session) ? 'session='.$session.'&' : '' ).'id=guestbook&s='.$s
			.( isset($lang) ? '&amp;lang='.$lang : '' );
			$link = $tcms_main->urlAmpReplace($link);
			
			$error_msg = $captcha_msg;
			$save_now = false;
		}
	}
	
	if($save_now){
		// Timestamp
		$cur_c_date = date('YmdHis');
		$save_comment = false;
		
		if($comment_name == ''){
			echo _MSG_NONAME.'<br />';
			$save_comment = false;
		}
		else{
			$save_comment = true;
		}
		
		if($comment_email == ''){ echo _MSG_NOEMAIL.'<br />'; $save_comment = false; }else{ $save_comment = true; }
		if(strpos($comment_email, '@') == false){ echo _MSG_NOEMAIL.'<br />'; $save_comment = false; }else{ $save_comment = true; }
		if($comment_text == ''){ echo _MSG_NOMSG.'<br />'; $save_comment = false; }   else{ $save_comment = true; }
		if($comment_web != ''){ if(substr($comment_web, 0, 7) != 'http://'){ $comment_web = 'http://'.$comment_web; } }
		
		if($save_comment == true){
			// linebreak
			$comment_text = $tcms_main->nl2br($comment_text);
			
			
			$comment_name = strip_tags($comment_name);
			$comment_email = strip_tags($comment_email);
			$comment_text = strip_tags($comment_text);
			
			
			// CHARSETS
			$comment_text = $tcms_main->decode_text($comment_text, '2', $c_charset);
			
			
			$comment_ip = getenv('REMOTE_ADDR');
			if($comment_ip == ''){
				$comment_remote = 'localhost';
				$comment_ip = '127.0.0.1';
			}
			else{
				$comment_remote = getHostByAddr($comment_ip);
			}
			
			
			if($choosenDB == 'xml'){
				$xmluser = new xmlparser($tcms_administer_site.'/tcms_imagegallery/'.$album.'/comments_'.$key.'/'.$cur_c_date.'.xml', 'w');
				$xmluser->xml_declaration();
				$xmluser->xml_section('comment');
				
				$xmluser->write_value('name', $comment_name);
				$xmluser->write_value('email', $comment_email);
				$xmluser->write_value('web', $comment_web);
				$xmluser->write_value('msg', $comment_text);
				$xmluser->write_value('time', $cur_c_date);
				$xmluser->write_value('ip', $comment_ip);
				$xmluser->write_value('domain', $comment_remote);
				
				$xmluser->xml_section_buffer();
				$xmluser->xml_section_end('comment');
				$xmluser->_xmlparser();
			}
			else{
				$sqlAL = new sqlAbstractionLayer($choosenDB);
				$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
				
				switch($choosenDB){
					case 'mysql':
						$newSQLColumns = '`module`, `timestamp`, `name`, `email`, `web`, `msg`, `time`, `ip`, `domain`';
						break;
					
					case 'pgsql':
						$newSQLColumns = 'module, timestamp, name, email, web, msg, "time", ip, "domain"';
						break;
					
					case 'mssql':
						$newSQLColumns = '[module], [timestamp], [name], [email], [web], [msg], [time], [ip], [domain]';
						break;
				}
				
				$newSQLData = "'image', '".$cur_c_date."', '".$comment_name."', '".$comment_email."', '".$comment_web."', '".$comment_text."', '".$cur_c_date."', '".$comment_ip."', '".$comment_remote."'";
				
				$sqlQR = $sqlAL->sqlCreateOne($tcms_db_prefix.'comments', $newSQLColumns, $newSQLData, $uid);
			}
		}
	}
	else{
		echo '<script>'
		.'alert(\''.$error_msg.'\');'
		.'history.back();'
		.'</script>';
	}
	
	echo '<script>document.location=\'media.php?album='.$album.'&key='.$key.'\';</script>';
}





//=====================================================
// FOOTER
//=====================================================

echo '<hr class="hr_line" noshade="noshade" />';

$cms_name         = $tcms_version->getName();
$toenda_copyright = $tcms_version->getToendaCopyright();

$footer_xml   = new xmlparser($tcms_administer_site.'/tcms_global/footer.xml','r');
$websiteowner = $footer_xml->read_section('footer', 'websiteowner');
$owner_url    = $footer_xml->read_section('footer', 'owner_url');
$copyright    = $footer_xml->read_section('footer', 'copyright');
$show_tcms    = $footer_xml->read_section('footer', 'show_tcmslogo');
$show_default = $footer_xml->read_section('footer', 'show_defaultfooter');
$show_plt     = $footer_xml->read_section('footer', 'show_page_loading_time');
$footer_text  = $footer_xml->read_section('footer', 'footer_text');

if($websiteowner != ''){ $websiteowner = $tcms_main->decodeText($websiteowner, '2', $c_charset); }
if($owner_url    != ''){ $owner_url    = $tcms_main->decodeText($owner_url, '2', $c_charset); }
if($copyright    != ''){ $copyright    = $tcms_main->decodeText($copyright, '2', $c_charset); }
if($footer_text  != ''){ $footer_text  = $tcms_main->decodeText($footer_text, '2', $c_charset); }


if(!isset($show_default) || $show_default == ''){
	$show_default = 1;
}


$page_load_time = tcms_time::tcms_load_end();
if($choosenDB != 'xml'){ $page_query_count = tcms_time::tcms_query_count_end_out(); }



echo '<div class="legal" style="display: block; margin: 0 auto 0 auto; width: 890px; padding: 2px 10px 10px 10px;">';


/*
* FOOTER TOP
*/
echo '<a class="legal" href="'.$owner_url.'" target="_blank">'.$websiteowner.'</a>&nbsp;'
.'<span class="legal">&copy; '.$copyright.' '._TCMS_ADMIN_RIGHT.'</span><br />';


if($show_tcms == 1){
	/*
	* SHOW LOGO IN FOOTER
	*/
	if($show_default == 1){
		echo '<span class="legal">'
		.'<a title="'._ABOUT_POWERED_BY.' '.$tcms_version->getName().'" class="legal" href="http://www.toenda.com" target="_blank">'.$tcms_version->getName().'</a>'
		.'&nbsp;&copy; '.$tcms_version->getToendaCopyright().'&nbsp;'
		.'<a title="'._ABOUT_POWERED_BY.' '.$tcms_version->getName().'" class="legal" href="http://www.toenda.com" target="_blank">Toenda Software Development</a>. '
		._TCMS_ADMIN_RIGHT.'<br />toendaCMS '._ABOUT_FREE_SOFTWARE.'</span><br />';
		echo '<br /><a title="'._ABOUT_POWERED_BY.' '.$tcms_version->getName().'" class="legal" href="http://www.toenda.com" target="_blank"><img align="center" alt="'._ABOUT_POWERED_BY.' '.$tcms_version->getName().'" title="'._ABOUT_POWERED_BY.' '.$tcms_version->getName().'" src="engine/images/logos/toendaCMS_button_02.png" border="0" /></a><br />';
	}
	
	echo '<span class="legal">'.$footer_text.'</span>';
}
else{
	/*
	* SHOW ONLY TEXT IN FOOTER
	*/
	if($show_default == 1){
		echo '<span class="legal">'._ABOUT_POWERED_BY.'&nbsp;'
		.'<a title="'._ABOUT_POWERED_BY.' '.$tcms_version->getName().'" class="legal" href="http://www.toenda.com" target="_blank">'.$tcms_version->getName().'</a>&nbsp;&copy;&nbsp;'.$tcms_version->getToendaCopyright().'&nbsp;'
		.'<a title="'._ABOUT_POWERED_BY.' '.$tcms_version->getName().'" class="legal" href="http://www.toenda.com" target="_blank">Toenda Software Development</a>.&nbsp;'
		._TCMS_ADMIN_RIGHT.'<br />toendaCMS '._ABOUT_FREE_SOFTWARE.'</span><br />';
	}
	
	echo '<span class="legal">'.$footer_text.'</span>';
}


/*
* SHOW PAGE LOADING TIME
*/
if($show_plt == 1){
	echo '<br /><span class="legal">'.$page_load_time.'<br />'.$page_query_count.'</span>';
}


echo '
</div>
';





//=====================================================
// DELETE COMMENT
//=====================================================

if($choosenDB == 'xml'){
	if(isset($session) && $session != '' && file_exists($tcms_administer_site.'/tcms_session/'.$session) && filesize($tcms_administer_site.'/tcms_session/'.$session) != 0){ $check_session = true; }
	else{ $check_session = false; }
}
else{ $check_session = $tcms_main->check_session_exists($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort, $session); }

if($check_session){
	if($is_admin == 'Administrator' || $is_admin == 'Developer' || $is_admin == 'Presenter'){
		if($cmd == 'delete'){
			if($choosenDB == 'xml'){ unlink($tcms_administer_site.'/tcms_imagegallery/'.$album.'/comments_'.$key.'/'.$XMLfile); }
			else{
				$sqlAL = new sqlAbstractionLayer($choosenDB);
				$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
				
				$strSQL = "DELETE FROM ".$tcms_db_prefix."comments WHERE module='image' uid = '".$XMLplace."' AND timestamp = '".$XMLfile."'";
				
				$sqlAL->sqlQuery($strSQL);
			}
			
			echo '<script>'
			.'document.location=\'media.php?'.( isset($session) ? 'session='.$session.'&' : '' ).'album='.$album.'&key='.$key.'\';'
			.'alert(\''._MSG_DELETE.'\');'
			.'</script>';
		}
	}
}





//=====================================================
// END OF FILE
//=====================================================

echo '
</div>

</body>

</html>
';

?>
