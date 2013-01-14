<?php
$db = PerfDb::init();
$minichat = $db->query("SELECT * FROM `minichat` WHERE `time` > '".(time()-60*60*24)."'")->rowCount();
return ($minichat > 0 ? ' <span class="green">+'.$minichat.'</span>' : false);