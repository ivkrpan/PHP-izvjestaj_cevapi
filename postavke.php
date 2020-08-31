<?php
include "connect_db.php";

connect_db($conn);

$sql = "SELECT * FROM cevapi.parametri where id=0;";
$result = $conn->query($sql);

if ($result->num_rows > 0) 
{
 while($row = $result->fetch_assoc()) {
?>

	<form method="post" action="spremi_postavke.php">
	
	<div class="input-group mb-1">
	  <div class="input-group-prepend">
		<span class="input-group-text" id="basic-addon1">Prodano velikih porcija dnevno:</span>
	  </div>
	  <input type="text" class="form-control"  aria-label="Porcija" aria-describedby="basic-addon1"  name="prodano_velikih_porcija"  value="<?php echo $row["prodano_velikih_porcija"];?>">
	</div>
	
	<div class="input-group mb-1">
	  <div class="input-group-prepend">
		<span class="input-group-text" id="basic-addon1">Prodano malih porcija dnevno:</span>
	  </div>
	  <input type="text" class="form-control" aria-label="Porcija" aria-describedby="basic-addon1" name="prodano_malih_porcija" value="<?php echo $row["prodano_malih_porcija"];?>">
	</div>
	
	<div class="input-group mb-1">
	  <div class="input-group-prepend">
		<span class="input-group-text" id="basic-addon1">Broj ćevapa u velikoj porciji:</span>
	  </div>
	  <input type="text" class="form-control"  aria-label="Ćevapa" aria-describedby="basic-addon1" name="cevapa_velika_porcija" value="<?php echo $row["cevapa_u_velikoj"];?>" >
	</div>
	
	<div class="input-group mb-1">
	  <div class="input-group-prepend">
		<span class="input-group-text" id="basic-addon1">Broj ćevapa u maloj porciji:</span>
	  </div>
	  <input type="text" class="form-control" aria-label="Ćevapa" aria-describedby="basic-addon1" name="cevapa_mala_porcija" value="<?php echo $row["cevapa_u_maloj"];?>" >
	</div>
	
	<div class="input-group mb-1">
	  <div class="input-group-prepend">
		<span class="input-group-text">Porast prodaje vikendom:</span>
	  </div>
	  <input type="text" class="form-control" aria-label="Posto" name="porast_vikend"  value="<?php echo $row["porast_vikend_posto"];?>" >
	  <div class="input-group-append">
		<span class="input-group-text">%</span>
	  </div>
	</div>
	
		<div class="input-group mb-1">
	  <div class="input-group-prepend">
		<span class="input-group-text">Porast prodaje u centru:</span>
	  </div>
	  <input type="text" class="form-control" aria-label="Posto" name="porast_centar"  value="<?php echo $row["centar_posto"];?>" >
	  <div class="input-group-append">
		<span class="input-group-text">%</span>
	  </div>
	</div>
	
	<div class="input-group mb-1">
	  <div class="input-group-prepend">
		<span class="input-group-text">Prodaja putem dostave:</span>
	  </div>
	  <input type="text" class="form-control" aria-label="Posto" name="dostava"  value="<?php echo $row["dostava_posto"];?>" >
	  <div class="input-group-append">
		<span class="input-group-text">%</span>
	  </div>
	</div>
	
	<input id="myBtn" href="#" onclick="spremi_postavke(this);" value="Spremi postavke" class="btn btn-secondary right">
	</form>


<?php
	}
		} else {
			echo "Ne postoji konfiguracija!";
		}
		$conn->close();

?>