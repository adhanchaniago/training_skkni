<?php 

include_once 'connection.php';

$id = isset($_GET['id'])?$_GET['id']:'';

$sql = mysqli_query($conn, "SELECT GROUP_CONCAT(hobi.hobi) AS hobi, peserta.* FROM peserta INNER JOIN hobi ON hobi.id_peserta=peserta.id_peserta WHERE peserta.id_peserta= $id");
if (mysqli_num_rows($sql) > 0) {
	$data = mysqli_fetch_assoc($sql);
	$hobi = explode(',', $data['hobi']);

	foreach ($hobi as $h) {
		$data_hobi[]['hobi'] = $h;
	}

}
 ?>

<!DOCTYPE html>
<html>
<head>
	<title>Ubah Peserta</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Abril+Fatface&display=swap" rel="stylesheet">
</head>
<body style="background-color: #333">
	<div class="container">
		<div class="row">
			<div class="col-md-6 offset-md-3">
				<div class="card mt-5">
					<div style="background-color: #eee" class="card-header">
						<h3 style="font-family:'Abril Fatface', cursive;" class="text-heading">Ubah Peserta</h3>
					</div>
					<div class="card-body">
						<form method="POST">
							<div class="form-group">
								<input type="text" name="nama" class="form-control" placeholder="Masukan Nama" value="<?=ucwords($data['nama'])?>" >
							</div>
							<div class="form-group">
								<label class="label text-muted">Jenis Kelamin</label>
								<div class="form-check">
								  <input class="form-check-input" type="radio" name="gender" id="male" value="Male" <?php if($data['gender'] == 'Male'){ echo 'checked';} ?>>
								  <label class="form-check-label" for="male">
								    Laki - Laki
								  </label>
								</div>
								<div class="form-check">
								  <input class="form-check-input" type="radio" name="gender" id="female" value="Female" <?php if($data['gender'] == 'Female'){ echo 'checked';} ?>>
								  <label class="form-check-label" for="female">
								    Perempuan
								  </label>
								</div>
							</div>
							<div class="form-group">
								<textarea class="form-control" name="alamat" placeholder="Masukan Alamat"><?=$data['alamat']?></textarea>
							</div>
							<div class="form-group">
								<input type="number" class="form-control" name="no_hp" placeholder="Masukan No HP" value="<?=$data['no_hp']?>">	
							</div>
							<div class="form-group">
								<input type="email" class="form-control" name="email" placeholder="Masukan Email" value="<?=$data['email']?>">	
							</div>
							<div class="form-group">
								<label class="label text-muted">Hobi</label>
								<div class="form-check">
								  <input class="form-check-input" type="checkbox" name="hobi[]" id="baca" value="membaca" <?php if(in_array('membaca', $hobi)){ echo 'checked';} ?>>
								  <label class="form-check-label" for="baca">
								    Membaca
								  </label>
								</div>
								<div class="form-check">
								  <input class="form-check-input" type="checkbox" name="hobi[]" id="nonton" value="menonton" <?php if(in_array('menonton', $hobi)){ echo 'checked';} ?>>
								  <label class="form-check-label" for="nonton">
								    Menonton
								  </label>
								</div>
								<div class="form-check">
								  <input class="form-check-input" type="checkbox" name="hobi[]" id="nulis" value="menulis" <?php if(in_array('menulis', $hobi)){ echo 'checked';} ?>>
								  <label class="form-check-label" for="nulis">
								    Menulis
								  </label>
								</div>
							</div>

							<div class="form-group">
								<input type="submit" class="btn btn-primary float-right" name="simpan" value="Ubah">	
							</div>

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

</body>
</html>

<?php 
if (isset($_POST['simpan'])) {
	$nama 	= htmlspecialchars(strtolower(trim($_POST['nama'])));
	$gender = $_POST['gender'];
	$alamat = htmlspecialchars(strtolower($_POST['alamat']));
	$no_hp 	= htmlspecialchars($_POST['no_hp']);
	$email 	= strtolower(trim($_POST['email']));
	$hobi 	= $_POST['hobi'];

	//cek email
	$sqlCek = mysqli_query($conn, "SELECT email FROM peserta WHERE email = '$email'");
	$cekEmail = mysqli_fetch_assoc($sqlCek);

	if (mysqli_num_rows($sqlCek) > 0 && $cekEmail['email'] !== $email) {
		header('location: http://localhost/jwp_crud/tambah_peserta.php?message=Email sudah digunakan');
	}else{
		$insert = $conn->prepare("UPDATE `peserta` SET `nama`=?,`gender`=?,`alamat`=?,`no_hp`=?,`email`=? WHERE `id_peserta` = ?");
		$insert->bind_param('ssssss', $nama, $gender, $alamat, $no_hp, $email, $id);
		if ($insert->execute()) {
			$insert->close();

			//hapus data hobi
			$deleteHobi = mysqli_query($conn, "DELETE FROM hobi WHERE id_peserta = $id");
			if ($deleteHobi == true) {
				foreach ($hobi as $h) {
				$update_hobi = mysqli_query($conn, "INSERT INTO hobi (`id_peserta`, `hobi`) VALUES ($id, '$h')");
				}
			}

			header('location: index.php?message=Berhasil Ubah Data Peserta');
		}else{
			header('location: index.php?message=Gagal Ubah Data Peserta');
		}
	}
}



 ?>