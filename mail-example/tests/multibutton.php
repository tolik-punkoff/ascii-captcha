<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<title>Multibutton 'Send' Test</title>
	</head>
	<body>
		<?php
			//two and more buttons 'submit' in HTML form and PHP example
			
			if ((empty($_POST))||($_SERVER['REQUEST_METHOD']!='POST')) //зашли на страничку, вывод формы
			{
				echo"<center><form action='".$_SERVER['PHP_SELF']."' method='POST'>
				<b>Press a button</b></br>
				<input type='text' name='testtext' value='Test Text'></br>
				<input type='submit' name='button1' value='Button #1'>
				<input type='submit' name='button2' value='Button #2'>
				<input type='submit' name='button3' value='Button #3'>
				</form></center>";
			}
			else //нажаты кнопки
			{
				$buttonmessage="<center><b>Perssed button:</br>#";
				
				//Проверка кнопок
				if (isset($_POST['button1']))
				{
					$buttonmessage.="1";
				}
				
				if (isset($_POST['button2']))
				{
					$buttonmessage.="2";
				}
				
				if (isset($_POST['button3']))
				{
					$buttonmessage.="3";
				}
				
				echo $buttonmessage."</br>";
				echo "Test text value: '".$_POST['testtext']."'</b></br></br>";
				echo "<b>----- Output &#36;_POST array: ----</b></br><code><pre>";
				print_r ($_POST); echo "</br></pre></code>";
				echo "<b>-------------------------------</b></center>";
			}
		?>
	</body>
</html>
