<?php
include "statistika.php";

if (isset($_GET['id']) && is_integer(intval($_GET['id'])))
{
	$id=intval($_GET['id']);
	$html = napravi_izvjestaje($id);
	
	echo $html;
}
else
{
	echo "Pogresan zahtjev!";
}
