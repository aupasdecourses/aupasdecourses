<?php

class Apdc_Blog_Block_Template extends Mage_Core_Block_Template
{
    public function getArticlesBlog() {
		$articles = array();
		$content = file_get_contents('https://www.aupasdecourses.com/blog/feed/?fsk=5a12fc2347833');
		$x = new SimpleXmlElement($content);
		if($content<>""){
			$i = 0;
			foreach($x->channel->item as $entry) {
				$img = $this->getSkinUrl('/dist/images/img-commercants.jpg');
				if($i > 2) {
					break;
				}
				if($entry->children('media', true)->content->attributes()) {
					$md = $entry->children('media', true)->content->attributes();
					if($md->url) {
						$img = $md->url;
					}
				}
				$articles[] = array(
					'title' => $entry->title,
					'link' => $entry->link,
					'description' => substr(strip_tags($entry->description),0,100).'...',
					'img' => $img
				);
				$i ++;
			}
		}
		return $articles;			
	}
}
