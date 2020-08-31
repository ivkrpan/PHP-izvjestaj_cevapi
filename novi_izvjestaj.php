<?php

include "statistika.php";


if (isset($_POST['naziv']) && isset($_POST['godina']))
{
	if (is_numeric($_POST["godina"]))
	{
		$godina = $_POST["godina"];
		$naziv = $_POST["naziv"];
		if ($godina >1900 && $godina < 2200)
		{
			napravi_izvjestaje(generiraj_podatke($naziv,$godina),true);
			echo 'OK';
			
		}else
		{
			echo 'Neispravna godina';
		}
		return;
	}
}

echo 'ERROR';
?>