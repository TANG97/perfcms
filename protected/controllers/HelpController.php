<?php
/*
* Mainpage controller
* @package: PerfCMS
*/
class HelpController extends Controller
	{
		public function actionIndex()
		{
			$lang = new Lang;
			$this->getHeader(array('title' => $lang::get('help')));
			$this->render('main');
			$this->getFooter();
		}
		
		public function actionRules()
		{
			$lang = new Lang;
			$this->getHeader(array('title' => $lang::get('rules')));
			$this->render('rules');
			$this->getFooter();
		}
		
		public function actionSmiles()
		{
			$lang = new Lang;
			$this->getHeader(array('title' => $lang::get('smiles')));
			$this->render('smiles');
			$this->getFooter();
		}
		
		public function actionCodes()
		{
			$lang = new Lang;
			$this->getHeader(array('title' => $lang::get('bb_codes')));
			$this->render('bbcodes');
			$this->getFooter();
		}
	}
