<?php /* _\|/_
         (o o)
+-----oOO-{_}-OOo--------------------------------------------------------+
| toendaCMS - Content Management and Weblogging System with XML and SQL  |
+------------------------------------------------------------------------+
| Copyright (c) Toenda Software Development                              |
| Author: Jonathan Naumann                                               |
+------------------------------------------------------------------------+
|
| Contentcentral
|
| File:	ext_content.php
|
+
*/


defined('_TCMS_VALID') or die('Restricted access');


/**
 * toendaCMS Contentcentral
 *
 * This module is used as a base content loader.
 *
 * @version 0.8.0
 * @author	Jonathan Naumann <jonathan@toenda.com>
 * @package toendaCMS
 * @subpackage Content Modules
 */


using('toendacms.datacontainer.content');

if(!isset($ws_auth)) {
	$ws_auth = 0;
}

if(!isset($page)) {
	$page = 1;
}



/*
	ACCESS LEVEL
*/

$arrContentAccess = $tcms_dcp->getContentAccess($id);
$authorized = $arrContentAccess['authorized'];
$content_published = $arrContentAccess['content_published'];




if($content_published == 1) {
	/*
		check for public access
	*/
	
	$ws_auth = $tcms_main->checkAccess($authorized, $is_admin);
	
	
	
	/*
		read data
	*/
	if($ws_auth) {
		switch($id) {
			case 'register'    : include(_REGISTER); break;
			case 'profile'     : include(_PROFILE); break;
			case 'search'      : include(_SEARCH_RESULT); break;
			case 'polls'       : include(_ALL_POLLS); break;
			case 'components'  : include(_COMPONENTS); break;
			case $faq_id       : include(_KNOWLEDGEBASE); break;
			case $download_id  : include(_DOWNLOAD); break;
			case $products_id  : include(_PRODUCTS); break;
			case 'contactform' : include(_SEND); break;
			case $link_id      : include(_MAIN_LINKS); break;
			case $book_id      : include(_GUESTBOOK); break;
			case $image_id     : include(_IMAGEGALLERY); break;
			case $news_id      : include(_NEWSMANAGER); break;
			case $front_id     : include(_FRONTPAGE); break;
			case $imp_id       : include(_IMPRESSUM); break;
			default:
				/*
					CONTENT
				*/
				$dcContent = new tcms_dc_content();
				
				//$defLang = $tcms_config->getLanguageCode(true);
				
				//echo $defLang.' - '.$lang.'<br>';
				
				$getLang = $tcms_config->getLanguageCodeForTCMS($lang);
				
				$dcContent = $tcms_dcp->getContentDC(
					$id, 
					true, 
					$getLang
				);
				
				$key       = $dcContent->getKeynote();
				$content00 = $dcContent->getText();
				$content01 = $dcContent->getSecondContent();
				$foot      = $dcContent->getFootText();
				$layout_id = $dcContent->getTextLayout();
				$docAutor  = $dcContent->getAutor();
				
				if($dcContent->getInWorkState() == 1){
					if($check_session && $canEdit){
						$link = $seoFolder.'/engine/admin/admin.php?id_user='.$session.'&amp;site=mod_content&amp;todo=edit&amp;maintag='.$id;
						
						echo '<div style="display: block; float: right;">';
						
						echo '<a href="'.$link.'" />';
						
						echo '<script>if(browser == \'ie\'){
						document.write(\'<img alt="'._NEWS_EDIT_CURRENT.'" title="'._NEWS_EDIT_CURRENT.'" style="" src="'.$imagePath.'engine/images/pencil.gif" border="0" />\');
						}else{
						document.write(\'<img alt="'._NEWS_EDIT_CURRENT.'" title="'._NEWS_EDIT_CURRENT.'" style="padding-bottom: 0px; padding-top: 1px;" src="'.$imagePath.'engine/images/pencil.png" border="0" />\');
						}</script>';
						
						echo '<noscript>'
						.'<img alt="'._NEWS_EDIT_CURRENT.'" title="'._NEWS_EDIT_CURRENT.'" style="" src="'.$imagePath.'engine/images/pencil.gif" border="0" />'
						.'</noscript>';
						
						echo '</a>';
						
						echo '</div>';
					}
					
					
					$arr_content = explode('{tcms_more}', $content00);
					$content00 = $arr_content[$page - 1];
					
					
					if($show_pages == 1){
						if(count($arr_content) > 1){
							$pageAmount = count($arr_content);
							
							echo _BOOK_PAGE.'&nbsp;'.$page.'/'.$pageAmount.':&nbsp;&nbsp;';
							
							$showME1 = true;
							$showME2 = true;
							$showLink = false;
							
							if($page > 1){
								if($showME1){
									$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' )
									.'id='.$id.'&amp;s='.$s.'&amp;page=1'
									.( isset($lang) ? '&amp;lang='.$lang : '' );
									$link = $tcms_main->urlAmpReplace($link);
									
									echo '<a href="'.$link.'" title="'._CONTENT_FIRST_PAGE.'"><u>&laquo;</u></a>&nbsp;&nbsp;';
									
									$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' )
									.'id='.$id.'&amp;s='.$s.'&amp;page='.( $page - 1 )
									.( isset($lang) ? '&amp;lang='.$lang : '' );
									$link = $tcms_main->urlAmpReplace($link);
									
									echo '<a href="'.$link.'" title="'._CONTENT_BACK_PAGE.'"><u>&#8249;</u></a>&nbsp;&nbsp;';
								}
								
								$showME1 = false;
							}
							
							for($i = 0; $i < $pageAmount; $i++){
								$thisPage = $i + 1;
								
								if($page > 0 && $page < 3){
									if($thisPage < 7) $showLink = true;
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
									$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' )
									.'id='.$id.'&amp;s='.$s.'&amp;page='.$thisPage
									.( isset($lang) ? '&amp;lang='.$lang : '' );
									$link = $tcms_main->urlAmpReplace($link);
									
									if($thisPage != $page) echo '<a href="'.$link.'"><u>'.$thisPage.'</u></a>';
									else echo '<span style="font-weight: bold !important;">'.$thisPage.'</span>';
									
									echo '&nbsp;&nbsp;';
								}
								
								$showLink = false;
							}
							
							if($page < $pageAmount){
								if($showME2){
									$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' )
									.'id='.$id.'&amp;s='.$s.'&amp;page='.( $page + 1 )
									.( isset($lang) ? '&amp;lang='.$lang : '' );
									$link = $tcms_main->urlAmpReplace($link);
									
									echo '<a href="'.$link.'" title="'._CONTENT_NEXT_PAGE.'"><u>&#8250;</u></a>'
									.'&nbsp;&nbsp;';
									
									$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' )
									.'id='.$id.'&amp;s='.$s.'&amp;page='.$pageAmount
									.( isset($lang) ? '&amp;lang='.$lang : '' );
									$link = $tcms_main->urlAmpReplace($link);
									
									echo '<a href="'.$link.'" title="'._CONTENT_LAST_PAGE.'"><u>&raquo;</u></a>';
								}
								
								$showME2 = false;
							}
							
							echo '<hr class="hr_line" /><br /><br />';
						}
					}
					
					
					// TCMS SCRIPT
					$toendaScript = new toendaScript($key);
					$key = $toendaScript->doParse();
					
					$toendaScript = new toendaScript($content00);
					$content00 = $toendaScript->doParse();
					$content00 = $toendaScript->checkSEO($content00, $imagePath);
					
					$toendaScript = new toendaScript($content01);
					$content01 = $toendaScript->doParse();
					$content01 = $toendaScript->checkSEO($content01, $imagePath);
					
					$toendaScript = new toendaScript($foot);
					$foot = $toendaScript->doParse();
					$foot = $toendaScript->checkSEO($foot, $imagePath);
					
					
					/*
						Load Layout ID for Content Templates
					*/
					
					echo '<div style="width: 99%; display: block;">'
					.'<div class="contentheading">'.$dcContent->getTitle().'</div>'
					.'<span class="contentstamp">'.$key.'</span><br /><br />'
					.'<div class="contentmain"><br />';
					
					$toendaScript->doParsePHP($content00);
					
					echo '<br />'.$content01.'<br />'
					.$foot.'</div>'
					.'</div>';
					
					
					// view page links
					if(count($arr_content) > 1){
						$pageAmount = count($arr_content);
						
						echo '<hr class="hr_line" />';
						
						echo _BOOK_PAGE.'&nbsp;'.$page.'/'.$pageAmount.':&nbsp;&nbsp;';
						
						$showME1 = true;
						$showME2 = true;
						$showLink = false;
						
						if($page > 1){
							if($showME1){
								$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' )
								.'id='.$id.'&amp;s='.$s.'&amp;page=1'
								.( isset($lang) ? '&amp;lang='.$lang : '' );
								$link = $tcms_main->urlAmpReplace($link);
								
								echo '<a href="'.$link.'" title="'._CONTENT_FIRST_PAGE.'"><u>&laquo;</u></a>&nbsp;&nbsp;';
								
								$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' )
								.'id='.$id.'&amp;s='.$s.'&amp;page='.( $page - 1 )
								.( isset($lang) ? '&amp;lang='.$lang : '' );
								$link = $tcms_main->urlAmpReplace($link);
								
								echo '<a href="'.$link.'" title="'._CONTENT_BACK_PAGE.'"><u>&#8249;</u></a>&nbsp;&nbsp;';
							}
							
							$showME1 = false;
						}
						
						for($i = 0; $i < $pageAmount; $i++){
							$thisPage = $i + 1;
							
							if($page > 0 && $page < 3){
								if($thisPage < 7) $showLink = true;
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
								$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' )
								.'id='.$id.'&amp;s='.$s.'&amp;page='.$thisPage
								.( isset($lang) ? '&amp;lang='.$lang : '' );
								$link = $tcms_main->urlAmpReplace($link);
								
								if($thisPage != $page) echo '<a href="'.$link.'"><u>'.$thisPage.'</u></a>';
								else echo '<span style="font-weight: bold !important;">'.$thisPage.'</span>';
								
								echo '&nbsp;&nbsp;';
							}
							
							$showLink = false;
						}
						
						if($page < $pageAmount){
							if($showME2){
								$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' )
								.'id='.$id.'&amp;s='.$s.'&amp;page='.( $page + 1 )
								.( isset($lang) ? '&amp;lang='.$lang : '' );
								$link = $tcms_main->urlAmpReplace($link);
								
								echo '<a href="'.$link.'" title="'._CONTENT_NEXT_PAGE.'"><u>&#8250;</u></a>'
								.'&nbsp;&nbsp;';
								
								$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' )
								.'id='.$id.'&amp;s='.$s.'&amp;page='.$pageAmount
								.( isset($lang) ? '&amp;lang='.$lang : '' );
								$link = $tcms_main->urlAmpReplace($link);
								
								echo '<a href="'.$link.'" title="'._CONTENT_LAST_PAGE.'"><u>&raquo;</u></a>';
							}
							
							$showME2 = false;
						}
					}
				}
				else{
					echo '<strong>'._MSG_NOT_PUBLISHED.'</strong>';
				}
				
				break;
		}
	}
	else{
		include_once(_ERROR_401);
	}
}
else{
	echo '<strong>'._MSG_NOT_PUBLISHED.'</strong>';
}


?>
