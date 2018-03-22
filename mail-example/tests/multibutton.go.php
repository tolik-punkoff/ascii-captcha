<?php
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
?>