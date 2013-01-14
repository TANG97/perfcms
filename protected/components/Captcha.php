<?php
class Captcha
{
	public function __construct()
	{
		# Конфигурация картинки
		$width          = 90;                 # Ширина картинки
		$height         = 40;                 # Высота картинки
		$font_size      = 12;                 # Размер шрифта
		$let_amount     = 4;                  # Кол-во символов на картинке
		$fon_let_amount = 10;                 # Кол-во символов на фоне
		$path_fonts     = APP_ROOT.'/design/fonts/'; # Путь к шрифтам  
   
		# Используемые массивы
		$letters = array('0', '2', '3', '4', '5', '6', '7', '9', 'a', 'b', 'c', 'd', 'e', 'g', 'h', 'm', 'z', 'x', 'w', 'q');                             # Символы на картинке
		$colors = array('10', '30', '50', '70', '90', '110', '130', '150', '170', '190', '210'); # Цвета картинки

		# Создание картинки  
		$src = imagecreatetruecolor($width, $height);
		$fon = imagecolorallocate($src, 255, 255, 255);

		imagefill($src, 0, 0, $fon);

		# Выбор шрифта 
		$fonts = array();

		$dir = opendir($path_fonts);

		while ($fontName = readdir($dir)) {
  			if ($fontName != '.' && $fontName != '..') $fonts[] = $fontName; 
		}

		closedir($dir);

		# Добавление символов на фон 
		for ($i=0;$i<$fon_let_amount;$i++) {
			$color = imagecolorallocatealpha($src, rand(0, 255), rand(0, 255), rand(0, 255), 100); 
			$font = $path_fonts . $fonts[rand(0, sizeof($fonts) - 1)];
			$letter = $letters[rand(0, sizeof($letters) - 1)];
			$size = rand($font_size - 2, $font_size + 2);
			imagettftext($src, $size, rand(0, 45), rand($width * 0.1, $width - $width * 0.1), rand($height * 0.2, $height), $color, $font, $letter);
		}

		# Добавление основных символов 
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

		# Сохранение кода в сессию
		$_SESSION['captcha'] = implode('', $cod);

		# Вывод изображения 
		header('Content-type: image/gif');
		imagegif($src);
	}
}
 