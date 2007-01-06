<?php /* _\|/_
         (o o)
+-----oOO-{_}-OOo--------------------------------------------------------+
| toendaCMS - Content Management and Weblogging System with XML and SQL  |
+------------------------------------------------------------------------+
| Copyright (c) Toenda Software Development                              |
| Author: Jonathan Naumann                                               |
+------------------------------------------------------------------------+
|
| Guestbook
|
| File:		ext_guestbook.php
| Version:	0.4.2
|
+
*/


defined('_TCMS_VALID') or die('Restricted access');



if(isset($_GET['page'])){ $page = $_GET['page']; }

if(isset($_POST['action'])){ $action = $_POST['action']; }
if(isset($_POST['guest_name'])){ $guest_name = $_POST['guest_name']; }
if(isset($_POST['guest_mail'])){ $guest_mail = $_POST['guest_mail']; }
if(isset($_POST['guest_msg'])){ $guest_msg = $_POST['guest_msg']; }

if(isset($_POST['comment_captcha'])){ $comment_captcha = $_POST['comment_captcha']; }
if(isset($_POST['check_captcha'])){ $check_captcha = $_POST['check_captcha']; }



echo '<script>
function checkInputs(id){
	var sendOK = false;
	
	strName = document.getElementById(id).guest_name.value;
	if(strName == \'\'){
		sendOK = false;
		alert(\''._MSG_NONAME.'\');
		document.getElementById(id).guest_name.focus();
		return;
	}
	else{ sendOK = true; }
	
	
	strNachricht = document.getElementById(id).guest_msg.value;
	if(strNachricht == \'\') {
		sendOK = false;
		alert(\''._MSG_NOMSG.'\');
		document.getElementById(id).guest_msg.focus();
		return;
	}
	else{ sendOK = true; }
	
	
	if(sendOK){ document.getElementById(id).submit(); }
}
</script>';



if($book_enabled == 1){
	/*
		INIT
	*/
	
	if($choosenDB == 'xml'){
		$gbxml = new xmlparser($tcms_administer_site.'/tcms_global/guestbook.xml', 'r');
		
		$gb_clean_link   = $gbxml->read_section('config', 'clean_link');
		$gb_clean_script = $gbxml->read_section('config', 'clean_script');
		$gb_convert_at   = $gbxml->read_section('config', 'convert_at');
		$gb_show_email   = $gbxml->read_section('config', 'show_email');
		$gb_name_width   = $gbxml->read_section('config', 'name_width');
		$gb_text_width   = $gbxml->read_section('config', 'text_width');
		$arr_color[0]    = $gbxml->read_section('config', 'color_row_1');
		$arr_color[1]    = $gbxml->read_section('config', 'color_row_2');
		
		if($gb_clean_link   == false){ $gb_clean_link   = ''; }
		if($gb_clean_script == false){ $gb_clean_script = ''; }
		if($gb_convert_at   == false){ $gb_convert_at   = ''; }
		if($gb_show_email   == false){ $gb_show_email   = ''; }
		if($gb_name_width   == false){ $gb_name_width   = '140'; }
		if($gb_text_width   == false){ $gb_text_width   = '360'; }
		if($arr_color[0]    == false){ $arr_color[0]    = 'f4f7fd'; }
		if($arr_color[1]    == false){ $arr_color[1]    = 'ffffff'; }
	}
	else{
		$sqlAL = new sqlAbstractionLayer($choosenDB);
		$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
		
		$strQuery = "SELECT clean_link, clean_script, convert_at, show_email, "
		."name_width, text_width, color_row_1, color_row_2 "
		."FROM ".$tcms_db_prefix."guestbook "
		."WHERE uid = 'guestbook'";
		
		$sqlQR = $sqlAL->sqlQuery($strQuery);
		$sqlObj = $sqlAL->sqlFetchObject($sqlQR);
		
		$gb_clean_link   = $sqlObj->clean_link;
		$gb_clean_script = $sqlObj->clean_script;
		$gb_convert_at   = $sqlObj->convert_at;
		$gb_show_email   = $sqlObj->show_email;
		$gb_name_width   = $sqlObj->name_width;
		$gb_text_width   = $sqlObj->text_width;
		$arr_color[0]    = $sqlObj->color_row_1;
		$arr_color[1]    = $sqlObj->color_row_2;
		
		if($gb_clean_link   == NULL){ $gb_clean_link   = '0'; }
		if($gb_clean_script == NULL){ $gb_clean_script = '0'; }
		if($gb_convert_at   == NULL){ $gb_convert_at   = '0'; }
		if($gb_show_email   == NULL){ $gb_show_email   = '0'; }
		if($gb_name_width   == NULL){ $gb_name_width   = '140'; }
		if($gb_text_width   == NULL){ $gb_text_width   = '360'; }
		if($arr_color[0]    == NULL){ $arr_color[0]    = 'f4f7fd'; }
		if($arr_color[1]    == NULL){ $arr_color[1]    = 'ffffff'; }
	}
	
	$arr_color[0] = '#'.$arr_color[0];
	$arr_color[1] = '#'.$arr_color[1];
	
	
	
	if(trim($book_title) != '') echo tcms_html::contentheading($book_title);
	if(trim($book_stamp) != '') echo tcms_html::contentstamp($book_stamp).'<br /><br /><br />';
	
	
	
	if($action != 'submit'){
		if(!isset($page))
			$page = 1;
		
		
		if($choosenDB == 'xml'){
			$arr_guestsfiles = $tcms_main->readdir_ext($tcms_administer_site.'/tcms_guestbook/');
			
			$count = 0;
			
			if(isset($arr_guestsfiles) && !empty($arr_guestsfiles) && $arr_guestsfiles != ''){
				foreach($arr_guestsfiles as $key => $value){
					$aXML = new xmlparser($tcms_administer_site.'/tcms_guestbook/'.$value, 'r');
					
					$arr_guests['uid'][$count]  = substr($value, 0, 32);
					$arr_guests['name'][$count] = $aXML->read_section('entry', 'name');
					$arr_guests['mail'][$count] = $aXML->read_section('entry', 'email');
					$arr_guests['text'][$count] = $aXML->read_section('entry', 'text');
					$arr_guests['date'][$count] = $aXML->read_section('entry', 'date');
					$arr_guests['time'][$count] = $aXML->read_section('entry', 'time');
					
					$arr_guests['name'][$count] = $tcms_main->decodeText($arr_guests['name'][$count], '2', $c_charset);
					$arr_guests['text'][$count] = $tcms_main->decodeText($arr_guests['text'][$count], '2', $c_charset);
					$arr_guests['mail'][$count] = $tcms_main->decodeText($arr_guests['mail'][$count], '2', $c_charset);
					
					$count++;
				}
			}
			
			$sqlNR = $count;
			
			if(isset($arr_guests) && !empty($arr_guests) && $arr_guests != ''){
				array_multisort(
					$arr_guests['date'], SORT_DESC, 
					$arr_guests['time'], SORT_DESC, 
					$arr_guests['name'], SORT_DESC, 
					$arr_guests['mail'], SORT_DESC, 
					$arr_guests['text'], SORT_DESC, 
					$arr_guests['uid'], SORT_DESC
				);
			}
		}
		else{
			$sqlAL = new sqlAbstractionLayer($choosenDB);
			$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
			
			
			$strSQL = "SELECT uid, name, email, text, date, time "
			."FROM ".$tcms_db_prefix."guestbook_items "
			."WHERE NOT (uid IS NULL) "
			."ORDER BY date DESC, time DESC";
			
			
			$sqlQR = $sqlAL->sqlQuery($strSQL);
			$sqlNR = $sqlAL->sqlGetNumber($sqlQR);
			
			$count = 0;
			
			while($sqlARR = $sqlAL->sqlFetchArray($sqlQR)){
				$arr_guests['uid'][$count]  = $sqlARR['uid'];
				$arr_guests['name'][$count] = $sqlARR['name'];
				$arr_guests['mail'][$count] = $sqlARR['email'];
				$arr_guests['text'][$count] = $sqlARR['text'];
				$arr_guests['date'][$count] = $sqlARR['date'];
				$arr_guests['time'][$count] = $sqlARR['time'];
				
				if($arr_guests['name'][$count] == NULL){ $arr_guests['name'][$count] = ''; }
				if($arr_guests['mail'][$count] == NULL){ $arr_guests['mail'][$count] = ''; }
				if($arr_guests['text'][$count] == NULL){ $arr_guests['text'][$count] = ''; }
				if($arr_guests['date'][$count] == NULL){ $arr_guests['date'][$count] = ''; }
				if($arr_guests['time'][$count] == NULL){ $arr_guests['time'][$count] = ''; }
				
				$arr_guests['name'][$count] = $tcms_main->decodeText($arr_guests['name'][$count], '2', $c_charset);
				$arr_guests['text'][$count] = $tcms_main->decodeText($arr_guests['text'][$count], '2', $c_charset);
				$arr_guests['mail'][$count] = $tcms_main->decodeText($arr_guests['mail'][$count], '2', $c_charset);
				
				$count++;
			}
			
			$sqlAL->sqlFreeResult($sqlQR);
		}
		
		
		
		
		echo '<div style="display: block; float: left;" class="text_small">'
		.'<strong>'
		._BOOK_ENTRYS.':&nbsp;'
		.$sqlNR.'&nbsp;'.( $sqlNR == 1 ? _BOOK_ENTRY2 : _BOOK_ENTRY1 )
		.'</strong>'
		.'</div>';
		
		echo '<div style="float: right;" class="text_small">'
		.'<strong>'
		.'<a href="#post">'._BOOK_ADD.'</a>'
		.'</strong>'
		.'</div>';
		
		
		echo '<br /><hr class="hr_line" />';
		
		
		echo '<span class="book_content">';
		echo '<br />';
		
		
		// border: 1px solid #222;
		
		echo tcms_html::table_head_cl('1', '0', '0', '100%', 'book_content');
		
		echo '<tr>'
		.'<th valign="top" class="titleBG" width="'.$gb_name_width.'" align="left">'._TABLE_NAME.'</th>'
		.'<th valign="top" class="titleBG" width="'.$gb_text_width.'" align="left">'._FRONT_COMMENT.'</th>'
		.'</tr>';
		
		if(isset($arr_guests) && !empty($arr_guests) && $arr_guests != ''){
			foreach($arr_guests['uid'] as $key => $val){
				if($key >= ( ($page * 10) - 10 ) && $key < ( $page * 10 )){
					if($gb_clean_link == 1){
						$arr_guests['name'][$key] = $tcms_main->cleanGBLink($arr_guests['name'][$key]);
						$arr_guests['text'][$key] = $tcms_main->cleanGBLink($arr_guests['text'][$key]);
						$arr_guests['mail'][$key] = $tcms_main->cleanGBLink($arr_guests['mail'][$key]);
					}
					
					
					if($gb_clean_script == 1){
						$arr_guests['name'][$key] = $tcms_main->cleanGBScript($arr_guests['name'][$key]);
						$arr_guests['text'][$key] = $tcms_main->cleanGBScript($arr_guests['text'][$key]);
						$arr_guests['mail'][$key] = $tcms_main->cleanGBScript($arr_guests['mail'][$key]);
					}
					
					
					if($gb_convert_at == 1){
						$arr_guests['mail'][$key] = str_replace('@', '__NO_SPAM__', $arr_guests['mail'][$key]);
					}
					
					
					if(is_integer($key/2)){ $wsc = 0; }
					else{ $wsc = 1; }
					
					
					echo '<tr style="height: 5px;"><td></td></tr>';
					
					
					echo '<tr bgcolor="'.$arr_color[$wsc].'">';
					
					
					
					if($cipher_email == 1){
						$mmm = '<script>JSCrypt.displayCryptMailTo(\''.$tcms_main->encodeBase64($arr_guests['mail'][$key]).'\');</script>';
					}
					else{
						$mmm = '<a href="mailto:'.$arr_guests['mail'][$key].'">';
					}
					
					
					
					$tmpDate = $arr_guests['date'][$key];
					$tmpDate = substr($tmpDate, 6, 2).'.'.substr($tmpDate, 4, 2).'.'.substr($tmpDate, 0, 4);
					
					
					
					echo '<td valign="top" width="'.$gb_name_width.'" style="padding: 2px;">'
					.'<strong class="text_big">'
					.( $gb_show_email == 1 ? ( $arr_guests['mail'][$key] == '' ? '' : $mmm ) : '' )
					.$arr_guests['name'][$key]
					.( $gb_show_email == 1 ? ( $arr_guests['mail'][$key] == '' ? '' : '</a>' ) : '' )
					.'</strong><br />'
					.'<span class="text_small">'.( $sqlNR - $key ).'. '._BOOK_ENTRY2.'</span><br />'
					.'<span class="text_small">'.$tmpDate.', '.$arr_guests['time'][$key].'</span><br />'
					.'</td>';
					
					
					echo '<td valign="top" width="'.$gb_text_width.'">'
					.$arr_guests['text'][$key]
					.'</td>';
					
					
					echo '</tr>';
				}
			}
		}
		
		echo tcms_html::table_end();
		
		
		
		echo '</span>';
		
		
		echo '<br />';
		echo '<br />';
		echo '<hr class="hr_line" />';
		
		//$pageAmount = ( round($sqlNR / 10) + 1 );
		$pageAmount = ( substr(($sqlNR / 10), 0, strpos(($sqlNR / 10), '.', 1)) + 1 );
		
		echo '<div style="font-weight: bold; display: block; float: left;" align="left">';
		
		echo _BOOK_PAGE.'&nbsp;'
		.$page.'/'.$pageAmount
		.':&nbsp;&nbsp;';
		
		$showME1 = true;
		$showME2 = true;
		$showLink = false;
		
		
		if($page > 1){
			if($showME1){
				$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'id=guestbook&amp;s='.$s.'&amp;page=1';
				$link = $tcms_main->urlAmpReplace($link);
				
				echo '<a href="'.$link.'" style="font-size: 14px;"><u>&laquo;</u></a>'
				.'&nbsp;&nbsp;';
				
				
				$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'id=guestbook&amp;s='.$s.'&amp;page='.( $page - 1 );
				$link = $tcms_main->urlAmpReplace($link);
				
				echo '<a href="'.$link.'" style="font-size: 14px;"><u>&#8249;</u></a>'
				.'&nbsp;&nbsp;';
			}
			
			$showME1 = false;
		}
		
		
		for($i = 0; $i < $pageAmount; $i++){
			$thisPage = $i + 1;
			
			
			if($page > 0 && $page < 3){
				if($thisPage < 7){
					$showLink = true;
				}
			}
			else{
				if(( $page - 3 ) == $thisPage 
				|| ( $page - 2 ) == $thisPage 
				|| ( $page - 1 ) == $thisPage 
				|| $page == $thisPage 
				|| ( $page + 1 ) == $thisPage 
				|| ( $page + 2 ) == $thisPage 
				|| ( $page + 3 ) == $thisPage){
					$showLink = true;
				}
			}
			
			
			if($showLink){
				$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'id=guestbook&amp;s='.$s.'&amp;page='.$thisPage;
				$link = $tcms_main->urlAmpReplace($link);
				
				
				if($thisPage != $page){
					echo '<a href="'.$link.'"><u>'.$thisPage.'</u></a>';
					//echo '<a href="'.$link.'"><u>['.$thisPage.']</u></a>';
				}
				else{
					echo '<span style="font-weight: bold !important;">'.$thisPage.'</span>';
					//echo '<span style="font-weight: bold !important;">('.$thisPage.')</span>';
				}
				
				echo '&nbsp;&nbsp;';
			}
			
			$showLink = false;
		}
		
		if($page < $pageAmount){
			if($showME2){
				$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'id=guestbook&amp;s='.$s.'&amp;page='.( $page + 1 );
				$link = $tcms_main->urlAmpReplace($link);
				
				echo '<a href="'.$link.'" style="font-size: 14px;"><u>&#8250;</u></a>';
				
				
				echo '&nbsp;&nbsp;';
				
				
				$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'id=guestbook&amp;s='.$s.'&amp;page='.$pageAmount;
				$link = $tcms_main->urlAmpReplace($link);
				
				echo '<a href="'.$link.'" style="font-size: 14px;"><u>&raquo;</u></a>';
			}
			
			$showME2 = false;
		}
		
		echo '</div>';
		
		
		echo '<br />';
		echo '<br />';
		
		
		// form
		echo '<a name="post"></a>'
		.'<form name="book" id="book" method="post" action="'.( $seoEnabled == 1 ? $seoFolder.'/' : '' ).$link_guest.'">'
		.'<input type="hidden" name="action" value="submit" />';
		
		
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
		
		
		// inputs
		echo '<div style="display: block; float: left; width: 60px;">'
		.'<strong>'._FORM_NAME.'</strong></div>';
		
		echo '<div style="display: block; margin: 0 0 3px 1px; width: 400px;">'
		.'<input type="text" class="inputtext bookfield" id="guest_name" name="guest_name" /></div>';
		
		
		// inputs
		echo '<div style="display: block; float: left; width: 60px;">'
		.'<strong>'._FORM_EMAIL.'</strong></div>';
		
		echo '<div style="display: block; margin: 0 0 3px 1px; width: 400px;">'
		.'<input type="text" class="inputtext bookfield" id="guest_mail" name="guest_mail" /></div>';
		
		
		// inputs
		echo '<div style="display: block; float: left; width: 60px;">'
		.'<strong>'._FORM_MESSAGE.'</strong></div>';
		
		echo '<div style="display: block; margin: 0 0 3px 1px; width: 400px;">'
		.'<textarea class="inputtextarea" id="guest_msg" name="guest_msg"></textarea></div>';
		
		
		// inputs
		echo '<br />';
		//echo '<div style="display: block; float: left; width: 60px;">&nbsp;</div>';
		
		echo '<div style="display: block; margin: 0 0 3px 1px; width: 400px;">'
		.'<input class="inputbutton" onclick="javascript:checkInputs(\'book\');" type="button" value="'._BOOK_SEND.'" /></div>';
		
		
		// end form
		echo '</form>';
	}
	
	
	
	
	
	
	
	
	
	if($action == 'submit'){
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
				$link = '?'.( isset($session) ? 'session='.$session.'&' : '' ).'id=guestbook&s='.$s;
				$link = $tcms_main->urlAmpReplace($link);
				
				$error_msg = $captcha_msg;
				$save_now = false;
			}
		}
		
		if($guest_name == '' && $save_now){
			$link = '?'.( isset($session) ? 'session='.$session.'&' : '' ).'id=guestbook&s='.$s;
			$link = $tcms_main->urlAmpReplace($link);
			
			$error_msg = _MSG_NONAME;
			$save_now = false;
		}
		
		if($guest_msg == '' && $save_now){
			$link = '?'.( isset($session) ? 'session='.$session.'&' : '' ).'id=guestbook&s='.$s;
			$link = $tcms_main->urlAmpReplace($link);
			
			$error_msg = _MSG_NOMSG;
			$save_now = false;
		}
		
		
		if($save_now){
			if($gb_clean_link == 1){
				$guest_name = $tcms_main->cleanGBLink($guest_name);
				$guest_mail = $tcms_main->cleanGBLink($guest_mail);
				$guest_msg  = $tcms_main->cleanGBLink($guest_msg);
			}
			
			
			if($gb_clean_script == 1){
				$guest_name = $tcms_main->cleanGBScript($guest_name);
				$guest_mail = $tcms_main->cleanGBScript($guest_mail);
				$guest_msg  = $tcms_main->cleanGBScript($guest_msg);
			}
			
			
			$guest_name = ereg_replace ('>', '&gt;', $guest_name);
			$guest_name = ereg_replace ('<', '&lt;', $guest_name);
			
			$guest_mail = ereg_replace ('<', '&lt;', $guest_mail);
			$guest_mail = ereg_replace ('>', '&gt;', $guest_mail);
			
			$guest_msg  = ereg_replace ('<', '&lt;', $guest_msg);
			$guest_msg  = ereg_replace ('>', '&gt;', $guest_msg);
			$guest_msg  = $tcms_main->nl2br($guest_msg);
			
			
			// CHARSETS
			$guest_name = $tcms_main->decode_text($guest_name, '2', $c_charset);
			$guest_mail = $tcms_main->decode_text($guest_mail, '2', $c_charset);
			$guest_msg  = $tcms_main->decode_text($guest_msg, '2', $c_charset);
			
			
			$guest_date = date('Ymd');
			$guest_time = date('H:i');
			
			
			if($choosenDB == 'xml'){
				while(($maintag = substr(md5(microtime()), 0, 32)) && file_exists($tcms_administer_site.'/tcms_guestbook/'.$maintag.'.xml')){}
				
				
				$xmluser = new xmlparser($tcms_administer_site.'/tcms_guestbook/'.$maintag.'.xml', 'w');
				$xmluser->xml_declaration();
				$xmluser->xml_section('entry');
				
				$xmluser->write_value('name', $guest_name);
				$xmluser->write_value('email', $guest_mail);
				$xmluser->write_value('text', $guest_msg);
				$xmluser->write_value('date', $guest_date);
				$xmluser->write_value('time', $guest_time);
				
				$xmluser->xml_section_buffer();
				$xmluser->xml_section_end('entry');
				$xmluser->_xmlparser();
			}
			else{
				$maintag = $tcms_main->create_uid($choosenDB, $sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort, $tcms_db_prefix.'guestbook_items', 32);
				
				
				$sqlAL = new sqlAbstractionLayer($choosenDB);
				$sqlCN = $sqlAL->sqlConnect($sqlUser, $sqlPass, $sqlHost, $sqlDB, $sqlPort);
				
				
				switch($choosenDB){
					case 'mysql':
						$newSQLColumns = '`name`, `email`, `text`, `date`, `time`';
						break;
					
					case 'pgsql':
						$newSQLColumns = 'name, email, text, "date", "time"';
						break;
					
					case 'mssql':
						$newSQLColumns = '[name], [email], [text], [date], [time]';
						break;
				}
				
				$newSQLData = "'".$guest_name."', '".$guest_mail."', '".$guest_msg."', '".$guest_date."', '".$guest_time."'";
				
				
				$sqlQR = $sqlAL->sqlCreateOne($tcms_db_prefix.'guestbook_items', $newSQLColumns, $newSQLData, $maintag);
			}
			
			
			$link = '?'.( isset($session) ? 'session='.$session.'&' : '' ).'id=guestbook&s='.$s;
			if($seoEnabled == 1) $link = $tcms_main->urlAmpReplace($link);
			
			echo '<script>'
			.'alert(\''._MSG_SAVED.'\');'
			.'document.location=\''.$link.'\';'
			.'</script>';
		}
		else{
			echo '<script>'
			.'alert(\''.$error_msg.'\');'
			.'history.back();'
			.'</script>';
		}
	}
}
else{
	echo '<strong>'._MSG_DISABLED_MODUL.'</strong>';
}

?>