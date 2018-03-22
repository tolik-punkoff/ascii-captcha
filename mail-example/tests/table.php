<?php
	include ('captcha.php');
	$testcode=getcaptchacode(6);
	$pgn=getpgnum($testcode,$allnum," ",3,false,false);
	
	function createform ($email, $subject,$message,$captcha)
	{
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
			<tr><td>E-mail:</td><td>$email</td></tr>
			<tr><td>Subject:</td><td>$subject</td></tr>
			<tr><td colspan='2'><center>Message</center></td></tr>
			<tr><td colspan='2'>$message</td></tr>
			<tr><td colspan='2'><center>Security code</center></td></tr>
			<tr><td colspan='2'><center><code><font color='lime'><pre>".$captcha."</pre></font></code></center></td></tr>
			<tr><td colspan='2'><center></center></td></tr>".
			"<tr><td colspan='2'><center><form action='".$_SERVER['PHP_SELF']."' method='POST'>
				<p><b>Введите проверочный код</b></br>
				<p><input type='text' name='captchacode'></p>
				<p><input type='submit' value='Проверить'></p>
				<input type='hidden' name='email' value='$email'> 
				<input type='hidden' name='subject' value='$subject'>
				<input type='hidden' name='message' value='$message'>
			</form></center></td></tr>
			</table></body></html>";
	}
	
	//отладка
	if ($_SERVER['REQUEST_METHOD']=='POST')
	{
		echo $_POST['email']."<br>".$_POST['subject']."<br>".$_POST['message']."<br>".$_POST['captchacode']."<br>";		
	}
	else
	{
		$frm=createform("admin@example.org", "Test Subject", "Test Message",$pgn);
		echo $frm;
	}	
?>