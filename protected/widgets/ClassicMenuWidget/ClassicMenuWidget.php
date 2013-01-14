<?php
class ClassicMenuWidget extends Widget
{
	public function run()
	{
		$lang = new Lang;
		$user = new User;
		echo System::interfaceMenu() .'
		<div class="menu">'. System::image('users.png') .' <a href="/user/list">'. $lang::get('users') .'</a> '. $user->count() .'</div>
		<div class="menu">'. System::image('help.png') .' <a href="/help">'. $lang::get('help') .'</a></div>';
	}
	
	public function init()
	{
		//
	}
}