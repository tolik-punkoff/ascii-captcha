<?php
	/*Скрипт отправляющий e-mail.
	Скрипт использует ASCII-каптчу
	ASCII-captcha: https://pastebin.com/LPKgY3WE
	Статья о ASCII-каптче: http://tolik-punkoff.com/2017/04/13/ascii-kaptcha-kaptcha-psevdografikoj-chast-i/
	Предыдущая версия скрипта: https://pastebin.com/PGv30rnH
	Статья о контактной форме для Wordpress: http://tolik-punkoff.com/2017/03/02/forma-obratnoj-svyazi-dlya-wp-bez-plagina/

	Script sending e-mail.
	The script uses ASCII-captcha
	ASCII-captcha: https://pastebin.com/LPKgY3WE
	Article about ASCII-captcha: http://tolik-punkoff.com/2017/04/13/ascii-kaptcha-kaptcha-psevdografikoj-chast-i/
	Previous version of the script: https://pastebin.com/PGv30rnH
	Article about the contact form for Wordpress: http://tolik-punkoff.com/2017/03/02/forma-obratnoj-svyazi-dlya-wp-bez-plagina/

	(L) Hex_Laden aka Tolik Punkoff, 2017
*/

	include ('captcha.php'); //подключаем модуль генерирующий каптчу
	
	function br_repl($str)
	{
		return str_replace("\r\n","<br>",$str);
	}
	
	//функция, формирующая форму с каптчей и отправленными данными
	function createform ($name, $email, $sub, $message, $allnum, $errcaptcha)
	{	
		//получаем код каптчи, псевдографическое изображение
		//и устанавливаем cookie
		$captchacode=getcaptchacode(6); //генерируем код капчи
		setcookie('mycaptchamd5',md5($captchacode),time()+300); //сохраняем в cookie MD5-хэш кода каптчи, устанавливаем время действия в 5 минут
		$captcha=getpgnum($captchacode,$allnum," ",1,false,false); //генерируем псевдографическое изображение капчи по коду
		
		//если установлен флаг ошибки ввода кода каптчи
		$emess="";		
		if ($errcaptcha)
		{
			$emess="<b><font color='red'>Проверочный код введен неверно</font></b>";
		}
		
		return "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'><style type='text/css'>
			TABLE {
			width: 300px; /* Ширина таблицы */
			border-collapse: collapse; /* Убираем двойные линии между ячейками */
			}
			TD, TH {
			padding: 3px; /* Поля вокруг содержимого таблицы */
			border: 1px solid gold; /* Параметры рамки */
			font: 12pt/10pt monospace;
			}
			#footer {
			position: fixed; /* Фиксированное положение */
			left: 0; bottom: 0; /* Левый нижний угол */
			padding: 10px; /* Поля вокруг текста */	
			width: 100%; /* Ширина слоя */
			}
			</style><title>Отправка сообщения</title></head><body bgcolor='black' text='silver'><center><h2>Отправка сообщения</h2><br>
			<b>Проверьте ваши данные и подтвердите, что вы не робот</b></center>
			
			<table align='center'>
			<tr><td>Name:</td><td>$name</td></tr>
			<tr><td>E-mail:</td><td>$email</td></tr>
			<tr><td>Subject:</td><td>$sub</td></tr>
			<tr><td colspan='2'><center>Message</center></td></tr>
			<tr><td colspan='2'>".br_repl($message)."</td></tr>
			<tr><td colspan='2'><center>Security code</center></td></tr>
			<tr><td colspan='2'><center><code><font color='lime'><pre>".$captcha."</pre></font></code></center></td></tr>
			<tr><td colspan='2'><center>$emess</center></td></tr>".
			"<tr><td colspan='2'><center><form action='".$_SERVER['PHP_SELF']."' method='POST'>
				<p><b>Введите проверочный код</b></br>
				<p><input type='text' name='captchacode'></p>
				<p>
					<input type='submit' name='checkcode' value='Проверить код'>
					<input type='submit' name='updcode' value='Обновить код'>
				</p>
				<input type='hidden' name='name' value='$name'>
				<input type='hidden' name='email' value='$email'> 
				<input type='hidden' name='sub' value='$sub'>
				<input type='hidden' name='message' value='$message'>
				<input type='hidden' name='myself' value='true'>
			</form></center></td></tr>
			<tr><td colspan='2'><center><font color='#0099FF'>Для изменения данных вернитесь в 
								форму отправки с помощью кнопки 'Назад' браузера</font></center></td></tr>
			</table></body></html>";
	}
	
	//переменные
	$err=false; //статус ошибки
	$usermessage=""; //сообщение, выводимое пользователю
	
	$address="admin@example.org"; //адрес для отправки
	$name=""; //имя пользователя
	$email=""; //обратный адрес
	$sub=""; //тема
	$message=""; //сообщение
	
	//если пользователь сделает назад, кука останется
	//тогда будет глюк 
	//надо проверить, с какой формы пришел запрос
	//если со странички - почистить куку
	//переменная для контроля этого дела
	$myself=false; 
	
	$redir=false; //статус редиректа
	$rediraddr="http://mail.loc/mail.html"; //адрес редиректа пользователя после отправки сообщения
	$redirtime=3; //время до редиректа (сек)
	
	//проверка на ошибки и установка переменных
	if ((empty($_POST))||($_SERVER['REQUEST_METHOD']!='POST')) //запрос кривой или кто-то просто с адресом балуется
	{
		$usermessage="Ошибка запроса POST! ";
		$err=true;
	}
	else //устанавливаем переменные
	{		
		$email = (isset($_POST['email'])) ? $_POST['email'] : false;
		$name = (isset($_POST['name'])) ? $_POST['name'] : false;
		$sub = (isset($_POST['sub'])) ? $_POST['sub'] : false;
		$message = (isset($_POST['message'])) ? $_POST['message'] : false;
		$myself = (isset($_POST['myself'])) ? true : false;
		
		//проверяем заполнение полей
		if (!$name || strlen($name) < 1) //поле имени
		{
			$usermessage.="Укажите свое имя.<br> ";
			$err=true;
		}
		if (!$email || strlen($email) < 3) //поле e-mail
		{
			$usermessage.="Укажите корректный адрес электронной почты.<br> ";
			$err=true;
		}
		if (!$sub || strlen($sub) < 1) //поле темы
		{
			$usermessage.="Укажите тему обращения.<br> ";
			$err=true;
		}
		if(!$message || strlen($message) < 1) //поле сообщения
		{
			$usermessage.="Введите сообщение.<br> ";
			$err=true;
		}
	}
	
	
	//основная работа	
	if (!$err) //делаем, если нет ошибок
	{			
		if ( (!isset($_COOKIE['mycaptchamd5'])) || !$myself ) //cookie не установлен, каптча не введена
		{			
			$usermessage=createform ($name,$email, $sub,$message,$allnum,false); //генерируем форму с каптчей и данными
		}
		else //каптча введена или нажата кнопка 'Обновить код'
		{
			if (isset($_POST['updcode'])) //обновить код
			{
				$usermessage=createform ($name,$email, $sub,$message,$allnum,false); //генерируем форму с каптчей и данными
			}
			
			if (isset($_POST['checkcode'])) //проверить код
			{
				$usercodemd5=md5(trim($_POST['captchacode'])); 	//извлекаем код, введенный пользователем в соотв. поле формы и получаем
																//MD5-хэш
				$gencodemd5=$_COOKIE['mycaptchamd5']; 			//вытаскиваем ранее сохраненный хэш
			
				//проверка каптчи
				if ($usercodemd5==$gencodemd5) //код введен верно
				{
					setcookie("mycaptchamd5","",time()-300); //удаляем cookie
				
					//отправка сообщения
					//формируем сообщение
					$mes = "Имя: ".$name."\n\nТема: " .$sub."\n\nСообщение: ".$message."\n\n"."E-mail to answer: $email\n\n";
					//отправляем
					$send = mail ($address,$sub,$mes,"Content-type:text/plain; charset = UTF-8\r\nFrom:$address");
					if ($send) //сообщение успешно отправлено
					{
						//сообщение пользователю об успехе
						$usermessage="<center><b><font color='blue'>Сообщение успешно отправлено!<br>
								Форма обратной связи будет открыта через ".$redirtime." секунд(ы) </center></b></font>";
						$redir=true; //устанавливаем статус редиректа
					}
					else //ошибка функции mail()
					{
						$usermessage="Внутренняя ошибка при отправке сообщения! :(";
						$err=true;						
					}
				}
				else //код неправильный
				{
					$usermessage=createform ($name,$email, $sub,$message,$allnum,true); //генерируем форму с каптчей и данными
				}
			}
		}
	}	
	
	//если ранее произошла ошибка
	//уcтанавливаем флаг редиректа
	if ($err)
	{
		$redir = true;
	}
	
	//редирект
	if ($redir)
	{
		header( 'Refresh: '.$redirtime.'; url='.$rediraddr );
	}
	
	//сообщение пользователю
	if ($err) //об ошибке
	{
		echo "<b><font color='red'>".$usermessage."</font></b></br>
			<font color='blue'>Вы будете перенаправлены обратно через ".$redirtime." секунд(ы)</font>";
	}
	else //какое-то другое
	{
		echo $usermessage;
	}
	
?>