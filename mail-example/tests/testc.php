<?php

		//вариант тестового скрипта с использованием отдельных cookie 
		//будет работать пока не завершится текущая сессия
		//довольно безглючный способ, если вы встраиваете самописную каптчу в готовый движок
		
		include('captcha.php'); //подключаем модуль, генерирующий капчу		
		
		function showform($pgn) //функция, которая рисует форму с каптчей
		{
			echo "<center>";
			
			echo ("<pre><code>".$pgn."</code></pre>");
			
			echo "<form action='".$_SERVER['PHP_SELF']."' method='POST'>
				<p><b>Введите проверочный код</b></br>
				<p><input type='text' name='captchacode'></p>
				<p><input type='submit' value='Проверить'></p>
			</form>";
			echo "</center>";
		}
		
		if ((!isset($_COOKIE['mycaptchamd5']))||($_SERVER['REQUEST_METHOD']!='POST')) //каптча не введена или это первый заход на страничку
		{			
			$captchacode=getcaptchacode(6);	//генерируем код капчи
			setcookie('mycaptchamd5',md5($captchacode),time()+300); //сохраняем в cookie MD5-хэш кода каптчи, устанавливаем время действия в 5 минут
			$pgn=getpgnum($captchacode,$allnum," ",1,false,false); //генерируем псевдографическое изображение капчи по коду
			showform($pgn); //показываем пользователю форму ввода капчи
		}
		else //каптча введена
		{
			$usercodemd5=md5(trim($_POST['captchacode'])); //извлекаем код, введенный пользователем в соотв. поле формы и получаем
			//MD5-хэш
			
			$gencodemd5=$_COOKIE['mycaptchamd5']; //вытаскиваем ранее сохраненный хэш
			
			if ($usercodemd5==$gencodemd5) //проверка соответствия
			{
				//все отлично, далее удаляем cookie и можно делать любые другие действия
				setcookie("mycaptchamd5","",time()-300); 
				echo "Код введен верно!"; //OK								
			}
			else //ашыпко
			{
				$captchacode=getcaptchacode(6);	//генерируем код капчи
				setcookie('mycaptchamd5',md5($captchacode),time()+300); //сохраняем в cookie MD5-хэш кода каптчи, устанавливаем время действия в 5 минут
				$pgn=getpgnum($captchacode,$allnum," ",1,true,true); //генерируем псевдографическое изображение капчи по коду
				echo "<center><font color='red'>Код введен неверно, попробуйте еще</font></center>";
				showform($pgn);	//показываем пользователю форму ввода капчи
			}
		}
?>