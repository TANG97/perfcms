<?php
class Captcha
{
	public function __construct()
	{
		# ������������ ��������
		$width          = 90;                 # ������ ��������
		$height         = 40;                 # ������ ��������
		$font_size      = 12;                 # ������ ������
		$let_amount     = 4;                  # ���-�� �������� �� ��������
		$fon_let_amount = 10;                 # ���-�� �������� �� ����
		$path_fonts     = APP_ROOT.'/design/fonts/'; # ���� � �������  
   
		# ������������ �������
		$letters = array('0', '2', '3', '4', '5', '6', '7', '9', 'a', 'b', 'c', 'd', 'e', 'g', 'h', 'm', 'z', 'x', 'w', 'q');                             # ������� �� ��������
		$colors = array('10', '30', '50', '70', '90', '110', '130', '150', '170', '190', '210'); # ����� ��������

		# �������� ��������  
		$src = imagecreatetruecolor($width, $height);
		$fon = imagecolorallocate($src, 255, 255, 255);

		imagefill($src, 0, 0, $fon);

		# ����� ������ 
		$fonts = array();

		$dir = opendir($path_fonts);

		while ($fontName = readdir($dir)) {
  			if ($fontName != '.' && $fontName != '..') $fonts[] = $fontName; 
		}

		closedir($dir);

		# ���������� �������� �� ��� 
		for ($i=0;$i<$fon_let_amount;$i++) {
			$color = imagecolorallocatealpha($src, rand(0, 255), rand(0, 255), rand(0, 255), 100); 
			$font = $path_fonts . $fonts[rand(0, sizeof($fonts) - 1)];
			$letter = $letters[rand(0, sizeof($letters) - 1)];
			$size = rand($font_size - 2, $font_size + 2);
			imagettftext($src, $size, rand(0, 45), rand($width * 0.1, $width - $width * 0.1), rand($height * 0.2, $height), $color, $font, $letter);
		}

		# ���������� �������� �������� 
		for ($i=0;$i<$let_amount;$i++) {
			$color = imagecolorallocatealpha($src, $colors[rand(0, sizeof($colors) - 1)], $colors[rand(0, sizeof($colors) - 1)], $colors[rand(0, sizeof($colors) - 1)], rand(20, 40)); 
			$font = $path_fonts . $fonts[rand(0, sizeof($fonts) - 1)];
			$letter = $letters[rand(0, sizeof($letters) - 1)];
			$size = rand($font_size * 2.1 - 2, $font_size * 2.1 + 2);
			$x = ($i + 1) * $font_size + rand(4, 7);
			$y = (($height * 2) / 3) + rand(0, 5);
			$cod[] = $letter;   
			imagettftext($src, $size, rand(0, 15), $x, $y, $color, $font, $letter);
		}

		# ���������� ���� � ������
		$_SESSION['captcha'] = implode('', $cod);

		# ����� ����������� 
		header('Content-type: image/gif');
		imagegif($src);
	}
}
 