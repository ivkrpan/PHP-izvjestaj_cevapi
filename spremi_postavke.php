<?php
include "connect_db.php";
connect_db($conn);

if (//ako su poslane postavke
		isset($_POST["prodano_malih_porcija"]) 	&& isset($_POST["prodano_velikih_porcija"]) && 
		isset($_POST["cevapa_velika_porcija"]) 	&& isset($_POST["cevapa_mala_porcija"]) && 
		isset($_POST["porast_vikend"])			&& isset($_POST["porast_centar"]) && 
		isset($_POST["dostava"])
	)
	{
		if(//ako su su numerickog zapisa
			is_numeric($_POST["prodano_malih_porcija"]) && is_numeric($_POST["prodano_velikih_porcija"]) && 
			is_numeric($_POST["cevapa_velika_porcija"]) && is_numeric($_POST["cevapa_mala_porcija"]) && 
			is_numeric($_POST["porast_vikend"]) 		&& is_numeric($_POST["porast_centar"]) && 
			is_numeric($_POST["dostava"])
		)
		{
			//spremanje
			$stmt = $conn->prepare("UPDATE cevapi.parametri SET prodano_velikih_porcija=?, prodano_malih_porcija=?, cevapa_u_velikoj=?, cevapa_u_maloj=?, porast_vikend_posto=?,centar_posto=?,dostava_posto=? WHERE id=0");
			$stmt->bind_param("iiiiddd",$_POST["prodano_velikih_porcija"], $_POST["prodano_malih_porcija"], $_POST["cevapa_velika_porcija"], $_POST["cevapa_mala_porcija"], $_POST["porast_vikend"], $_POST["porast_centar"], $_POST["dostava"]);

			/* execute prepared statement */
			$stmt->execute();
			
			$conn->close();
			echo 'OK';
		}else
		{
			echo "ERROR";
		}
	}else
		{
			echo "ERROR";
		}
	
	
?>