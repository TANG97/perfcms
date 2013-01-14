<?php
$db = PerfDb::init();
$posts = $db->query("SELECT * FROM `forum_pt`")->rowCount();
$new_posts = $db->query("SELECT * FROM `forum_pt` WHERE `time` > '".(time()-60*60*24)."'")->rowCount();
$topics = $db->query("SELECT * FROM `forum_t`")->rowCount();
return ' ('.$topics.'/'.$posts.')'.($new_posts > 0 ? ' <a href="/forum/index/new"><span class="green">+'.$new_posts.'</span></a>' : null);