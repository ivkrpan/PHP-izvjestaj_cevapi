<?php	

if (isset($_GET['page']))
{
	switch ($_GET['page'])
	{
		case 'izvjestaji':
			include "popis_izvjestaja.php";
		break;
		case 'restorani':
			include "popis_restorana.php";
		break;
		case 'postavke':
			include "postavke.php";
		break;
		default:
			echo 'Ne postoji stranica ' . $_GET['page']; 
		break;
	}
}

?>