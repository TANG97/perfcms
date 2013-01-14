<?php
$db = PerfDb::init();
$news = $db->query("SELECT * FROM `news`")->rowCount();
$newNews = $db->query("SELECT * FROM `news` WHERE `time` > '".(time()-60*60*24)."'")->rowCount();
return ' ('.$news.($newNews > 0 ? '/ <span class="green">+'.$newNews.'</span>' : false).')';