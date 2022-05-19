<?php require_once('includes/init.php'); ?>

<?php
$errors = array();
$sukses = false;

$ada_error = false;
$result = '';

$id_alternatif = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(!$id_alternatif) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT * FROM alternatif WHERE id_alternatif = :id_alternatif');
	$query->execute(array('id_alternatif' => $id_alternatif));
	$result = $query->fetch();
	
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	}

	$id_alternatif = (isset($result['id_alternatif'])) ? trim($result['id_alternatif']) : '';
	$nama_alternatif = (isset($result['nama_alternatif'])) ? trim($result['nama_alternatif']) : '';
	$keterangan = (isset($result['keterangan'])) ? trim($result['keterangan']) : '';
	$tanggal_input = (isset($result['tanggal_input'])) ? trim($result['tanggal_input']) : '';
}

if(isset($_POST['submit'])):	
	
	$nama_alternatif = (isset($_POST['nama_alternatif'])) ? trim($_POST['nama_alternatif']) : '';
	$keterangan = (isset($_POST['keterangan'])) ? trim($_POST['keterangan']) : '';
	$tanggal_input = (isset($_POST['tanggal_input'])) ? trim($_POST['tanggal_input']) : '';
	$kriteria = (isset($_POST['kriteria'])) ? $_POST['kriteria'] : array();
	
	// Validasi ID alternatif
	if(!$id_alternatif) {
		$errors[] = 'Id alternatif tidak ada';
	}
	// Validasi
	if(!$nama_alternatif) {
		$errors[] = 'Nomor alternatif tidak boleh kosong';
	}
	if(!$tanggal_input) {
		$errors[] = 'Tanggal input tidak boleh kosong';
	}
	
	// Jika lolos validasi lakukan hal di bawah ini
	if(empty($errors)):

		// $query = $pdo->prepare('SELECT * FROM alternatif where nama_alternatif = :nama_alternatif and keterangan = :keterangan');			
		// $query->execute(array('nama_alternatif' => $nama_alternatif, 'keterangan' => $keterangan));
		// // menampilkan berupa nama field
		// $query->setFetchMode(PDO::FETCH_ASSOC);
		
		if(!empty($kriteria)):
		
			// $prepare_query = 'UPDATE alternatif SET nama_alternatif = :nama_alternatif, keterangan = :keterangan, tanggal_input = :tanggal_input WHERE id_alternatif = :id_alternatif';
			// $data = array(
			// 	'nama_alternatif' => strtoupper($nama_alternatif),
			// 	'keterangan' => strtoupper($keterangan),
			// 	'tanggal_input' => $tanggal_input,
			// 	'id_alternatif' => $id_alternatif,
			// );		
			// $handle = $pdo->prepare($prepare_query);		
			// $sukses = $handle->execute($data);
			
			if(!empty($kriteria)):
				foreach($kriteria as $id_kriteria => $id_skala):
					$handle = $pdo->prepare('INSERT INTO nilai_alternatif (id_alternatif, id_kriteria, id_skala) 
					VALUES (:id_alternatif, :id_kriteria, :id_skala)
					ON DUPLICATE KEY UPDATE id_skala = :id_skala');
					$handle->execute( array(
						'id_alternatif' => $id_alternatif,
						'id_kriteria' => $id_kriteria,
						'id_skala' =>$id_skala
					) );
				endforeach;
			endif;
			
			redirect_to('list-alternatif.php?status=sukses-edit');
		else:
			redirect_to('list-alternatif.php?status=gagal');
		endif;
	endif;

endif;
?>

<?php
$judul_page = 'Edit Alternatif';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-alternatif.php'); ?>
	
		<div class="main-content the-content">
			<h1>Edit Alternatif</h1>
			
			<?php if(!empty($errors)): ?>
			
				<div class="msg-box warning-box">
					<p><strong>Error:</strong></p>
					<ul>
						<?php foreach($errors as $error): ?>
							<li><?php echo $error; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				
			<?php endif; ?>
			
			<?php if($sukses): ?>
			
				<div class="msg-box">
					<p>Data berhasil disimpan</p>
				</div>	
				
			<?php elseif($ada_error): ?>
				
				<p><?php echo $ada_error; ?></p>
			
			<?php else: ?>				
				
				<form action="edit-alternatif.php?id=<?php echo $id_alternatif; ?>" method="post">
					<div class="field-wrap clearfix">					
						<label>Nama Alternatif <span class="red">*</span></label>
						<input type="text" name="nama_alternatif" readonly value="<?php echo $nama_alternatif; ?>">
					</div>					
					<div class="field-wrap clearfix">					
						<label>Keterangan</label>
						<textarea name="keterangan" cols="30" rows="2" readonly><?php echo $keterangan; ?></textarea>
					</div>
					<div class="field-wrap clearfix">					
						<label>Tanggal Input <span class="red">*</span></label>
						<input type="text" name="tanggal_input" value="<?php echo $tanggal_input; ?>" class="datepicker" readonly>
					</div>	
					
					<h3>Nilai Kriteria</h3>
					<?php
					$query2 = $pdo->prepare('SELECT kriteria.nama AS nama, nilai_alternatif.id_kriteria as id_kriteria, nilai 
					FROM nilai_alternatif join kriteria on kriteria.id_kriteria=nilai_alternatif.id_kriteria 
					join skala_penilaian on skala_penilaian.id_skala=nilai_alternatif.id_skala WHERE 
					nilai_alternatif.id_alternatif = :id_alternatif');
					$query2->execute(array(
						'id_alternatif' => $id_alternatif
					));
					$query2->setFetchMode(PDO::FETCH_ASSOC);
					
					if($query2->rowCount() > 0):
					
						while($kriteria = $query2->fetch()):
						?>
							<div class="field-wrap clearfix">					
								<label><?php echo $kriteria['nama']; ?></label>
								<select name="kriteria[<?php echo $kriteria['id_kriteria']; ?>]">
									<option selected disabled value="">Pilih Kriteria</option>
									<?php 
										$query = $pdo->prepare('SELECT * FROM skala_penilaian where id_kriteria='.$kriteria['id_kriteria']);			
										$query->execute();
										// menampilkan berupa nama field
										$query->setFetchMode(PDO::FETCH_ASSOC);
										while($row = $query->fetch()){
									?>
									<option value="<?php echo $row['id_skala']; ?>"
										<?php if($kriteria['nilai'] == $row['nilai']) {
											echo 'selected';
										}?>>
										<?php echo $row['nama']; ?>
									</option>
									<?php } ?>					
								</select>
								<!-- <input type="number" step="0.001" name="kriteria[<?php echo $kriteria['id_kriteria']; ?>]" value="<?php echo ($kriteria['nilai']) ? $kriteria['nilai'] : 0; ?>">								 -->
							</div>		
						<?php
						endwhile;
						
					else:					
						echo '<p>Kriteria masih kosong.</p>';						
					endif;
					?>
					
					<div class="field-wrap clearfix">
					<a href="list-alternatif.php" class="button" style="background-color:yellow; color:black;"><span class="fa fa-backward"></span> Back</a> &nbsp; 
						<button type="submit" name="submit" value="submit" class="button">Simpan Alternatif</button>
					</div>
				</form>
				
			<?php endif; ?>			
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');