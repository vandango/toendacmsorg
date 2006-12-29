<?php /* _\|/_
         (o o)
+-----oOO-{_}-OOo--------------------------------------------------------+
| toendaCMS - Content Management and Weblogging System with XML and SQL  |
+------------------------------------------------------------------------+
| Copyright (c) Toenda Software Development                              |
| Author: Jonathan Naumann                                               |
+------------------------------------------------------------------------+
| 
| Sidebar
|
| File:		ext_sidebar_news.php
| Version:	0.1.6
|
+
*/


defined('_TCMS_VALID') or die('Restricted access');



//$id == 'frontpage'
if($blah = 1){
	if($sb_news_enabled == 1){
		echo tcms_html::subtitle($front_s_title);
		//echo '<br />';
		
		$arrNewsDC = new tcms_dc_news();
		$arrNewsDC = $tcms_dcp->getNewsDCList($is_admin, $how_many);
		
		
		if(!empty($arrNewsDC) && $arrNewsDC != '' && isset($arrNewsDC)){
			foreach($arrNewsDC as $n_key => $n_value){
				$dcNews = new tcms_dc_news();
				$dcNews = $arrNewsDC[$n_key];
				
				$link = '?'.( isset($session) ? 'session='.$session.'&amp;' : '' ).'id=newsmanager&amp;s='.$s.'&amp;news='.$dcNews->GetID();
				$link = $tcms_main->urlAmpReplace($link);
				
				if($sb_news_display == 1 || $sb_news_display == 2 || $sb_news_display == 3){
					echo ( $sb_news_display == 3 ? '<span class="newsCategories" style="padding-left: 6px;">&raquo;</span>' : '' )
					.'<strong class="newsCategories" style="padding-left: 6px;">'
					.'<a href="'.$link.'">'
					.$dcNews->GetTitle()
					.'</a>'
					.'</strong>'
					.'<br />';
				}
				
				
				if($sb_news_display == 1 || $sb_news_display == 2){
					echo '<div class="text_small" style="padding-left: 6px;">'
					.lang_date(
						substr($dcNews->GetDate(), 0, 2), 
						substr($dcNews->GetDate(), 3, 2), 
						substr($dcNews->GetDate(), 6, 4), 
						substr($dcNews->GetTime(), 0, 2), 
						substr($dcNews->GetTime(), 3, 2), 
						''
					)
					.'</div>';
				}
				
				
				//if($sb_news_display != 3)
					//echo '<br />';
				//echo '<br />';
				
				
				/*
					display the news
				*/
				if($sb_news_display == 1){
					echo '<div class="sidemain" style="padding-left: 6px;">';
					
					$news_content = $tcms_main->cleanAllImagesFromString($dcNews->GetText());
					
					$toendaScript = new toendaScript($news_content);
					$news_content = $toendaScript->toendaScript_trigger();
					$news_content = $toendaScript->checkSEO($news_content, $imagePath);
					
					$news_content = str_replace('{tcms_more}', '', $news_content);
					
					if(strlen($news_content) > $sb_cut_news){
						$str_off = strpos($news_content, ' ', $sb_cut_news);
						$news = substr($news_content, 0, $str_off);
						$news = trim($news);
						echo $news.' ...';
					}
					elseif(strlen($news_content) < $sb_cut_news){
						$news_content = trim($news_content);
						echo $news_content;
					}
					
					echo '</div>'
					.'<br />';
				}
			}
		}
		
		echo '<br />';
	}
}

?>