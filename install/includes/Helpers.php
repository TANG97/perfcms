<?php
/*
* Installation
* @package: PerfCMS
*/
function install_header($title = 'Installation')
{
	echo '<!DOCTYPE html "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="uk">
	<head>
		<title>'.$title.'</title>
		<link rel="shortcut icon" href="/design/themes/touch/default/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="/design/themes/touch/default/style.css" type="text/css">
	</head>
	<body>
	<div class="main">
	<div class="logo"><img src="/design/themes/touch/default/images/logo.png" alt="logo" /></div>';
}
	
function install_footer()
{
	echo '<div class="footer">
		&copy; <a href="http://perfcms.org.ua/">'.date('Y').' PerfCMS '.file_get_contents(root.'/system/data/info/version.txt').'</a> by Artas Black (Taras Chornyi)
		</div>
		</div>
		</body>
		</html>';
}
	
function install_img($url = '', $alt = 'Image', $html = '')
{
	if(!empty($url))
	{
		return '<img src="'.$url.'" alt="'.$alt.'" '.(!empty($html) ? ' '.$html : null).' />';
	}
}

function install_link($url = '', $linkName = '', $html = false)
{
	if(!empty($url) && !empty($linkName))
	{
		return '<a href="'.$url.'"'.($html !=false ? ' '.$html: null).'>'.$linkName.'</a>';
	}
}