<?php

class Apdc_Blog_Block_Template extends Mage_Core_Block_Template
{
    public function getArticlesBlog() {
		$content = file_get_contents('https://www.aupasdecourses.com/blog/feed/');
		$x = new SimpleXmlElement($content);
		$i = 0;
		$articles = array();
		foreach($x->channel->item as $entry) {
			if($i > 2) {
				break;
			}
			$articles[] = array(
				'title' => $entry->title,
				'link' => $entry->link,
				'description' => substr($entry->description,0,100).'...',
				'img' => $this->getSkinUrl('/dist/images/img-commercants.jpg')
			);
			$i ++;
		}
		return $articles;
	}
}
