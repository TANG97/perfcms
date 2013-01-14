<?php
class Paginator 
{
	
	public function __construct($var, $limit)
	{
		global $am_pages, $page, $start;
		$am_pages = $this->am_pages($var, $limit);
		$page = $this->page($am_pages);
		$start = $limit * $page - $limit;
	}

	public function page($am_pages = 1)
	{
		$page = 1;
		if (isset($_GET['page']))
		{
			if ($_GET['page'] == 'end') $page = intval($am_pages);
			else if (is_numeric($_GET['page'])) $page = intval($_GET['page']);
		}			
		if ($page < 1) $page = 1;
		if ($page > $am_pages) $page = $am_pages;
		return $page;
	}

	public function am_pages($am_posts = 0, $am_p_pages = 10)
	{
		if ($am_posts != 0)
		{
			$v_pages = ceil($am_posts / $am_p_pages);
			return $v_pages;
		}
		else return 1;
	}

	public function pages($link = '?', $am_pages = 1, $page = 1)
	{
		if ($page < 1) $page = 1;
		echo '<div class="top">';
		if ($page != 1) echo '<a class="pnav" href="'. $link .'page=1">&laquo;</a> ';
		if ($page != 1) echo '<a class="pnav" href="'. $link .'page=1">1</a>';
		else echo '<b class="cpage">1</b>';

		for ($ot=-3; $ot<=3; $ot++)
		{
			if ($page + $ot > 1 && $page + $ot < $am_pages)
			{
				if ($ot == -3 && $page + $ot > 2) echo ' .. ';
					if ($ot != 0) echo ' <a class="pnav" href="'. $link .'page='. ($page + $ot) .'">'. ($page + $ot) .'</a>';
					else echo ' <b class="cpage">'. ($page + $ot) .'</b>';
							if ($ot == 3 && $page + $ot < $am_pages - 1) echo ' .. ';
			}
		}
		if ($page != $am_pages) echo ' <a class="pnav" href="'. $link .'page=end">'. $am_pages .'</a>';
		else if ($am_pages > 1) echo ' <b class="cpage">'. $am_pages .'</b>';
		if ($page!=$am_pages) echo ' <a class="pnav" href="'. $link .'page=end">&raquo;</a>';
		echo '</div>';
	}

	public function view()
	{
		$link = preg_replace('/\?page=(.+)/i', '', $_SERVER['REQUEST_URI']).'?';
		global $am_pages, $page;
		if ($am_pages > 1) $this->pages($link, $am_pages, $page);
	}
}
