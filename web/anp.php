<?php
//plays the 583 news 
$xml = simplexml_load_file("https://www.omnycontent.com/d/playlist/56ccbbb7-0ff7-4482-9d99-a88800f49f6c/a49c87f6-d567-4189-8692-a8e2009eaf86/9fea2041-fccd-4fcf-8cec-a8e2009eeca2/podcast.rss");
$url = $xml->channel->item[0]->enclosure['url'][0];

header("Location: $url");


?>
