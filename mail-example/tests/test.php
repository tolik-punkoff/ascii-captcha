<?php
		include('captcha.php'); //подключаем модуль, генерирующий капчу
		session_start(); //открываем сессию
		
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
		
		if ((!isset($_SESSION['mycaptchamd5']))||($_SERVER['REQUEST_METHOD']!='POST')) //каптча не введена или это первый заход на страничку
		{			
			$captchacode=getcaptchacode(6);	//генерируем код капчи
			$_SESSION['mycaptchamd5'] = md5($captchacode); //сохраняем в сессии MD5-хэш кода каптчи
			$pgn=getpgnum($captchacode,$allnum," ",1,true,true); //генерируем псевдографическое изображение капчи по коду
			showform($pgn); //показываем пользователю форму ввода капчи
		}
		else //каптча введена
		{
			$usercodemd5=md5(trim($_POST['captchacode'])); //извлекаем код, введенный пользователем в соотв. поле формы и получаем
			//MD5-хэш
			
			$gencodemd5=$_SESSION['mycaptchamd5']; //вытаскиваем ранее сохраненный хэш
			
			if ($usercodemd5==$gencodemd5) //проверка соответствия
			{
				echo "Код введен верно!"; //OK
				session_destroy(); //осторожнее так, если прикручиваете каптчу к движку. Можно ненароком сессию авторизации убить.
			}
			else //ашыпко
			{
				$captchacode=getcaptchacode(6);	//генерируем код капчи
				$_SESSION['mycaptchamd5'] = md5($captchacode); //сохраняем в сессии MD5-хэш кода каптчи
				$pgn=getpgnum($captchacode,$allnum," ",1,true,true); //генерируем псевдографическое изображение капчи по коду
				echo "<center><font color='red'>Код введен неверно, попробуйте еще</font></center>";
				showform($pgn);	//показываем пользователю форму ввода капчи
			}
		}
?>