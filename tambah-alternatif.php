<?php require_once('includes/init.php'); ?>

<?php
$errors = array();
$sukses = false;

$nama_alternatif = (isset($_POST['nama_alternatif'])) ? trim($_POST['nama_alternatif']) : '';
$keterangan = (isset($_POST['keterangan'])) ? trim($_POST['keterangan']) : '';
$kriteria = (isset($_POST['kriteria'])) ? $_POST['kriteria'] : array();


if(isset($_POST['submit'])):	
	
	// Validasi
	if(!$nama_alternatif) {
		$errors[] = 'Nama alternatif tidak boleh kosong';
	}	
	if(!$kriteria){
		$errors[] = 'Nilai kriteria tidak ada';
	}
	if(!$keterangan){
		$errors[] = 'Keterangan tidak boleh kosong';
	}

	// Jika lolos validasi lakukan hal di bawah ini
	if(empty($errors)):

		$query = $pdo->prepare('SELECT * FROM alternatif where nama_alternatif = :nama_alternatif and keterangan = :keterangan');			
		$query->execute(array('nama_alternatif' => $nama_alternatif, 'keterangan' => $keterangan));
		// menampilkan berupa nama field
		$query->setFetchMode(PDO::FETCH_ASSOC);
		
		if($query->rowCount() == 0 && (!empty($kriteria))):
		
			$handle = $pdo->prepare('INSERT INTO alternatif (nama_alternatif, keterangan, tanggal_input) VALUES (:nama_alternatif, :keterangan, :tanggal_input)');
			$handle->execute( array(
				'nama_alternatif' => strtoupper($nama_alternatif),
				'keterangan' => strtoupper($keterangan),
				'tanggal_input' => date('Y-m-d')
			) );
			$sukses = "alternatif no. <strong>{$nama_alternatif}</strong> berhasil dimasukkan.";
			$id_alternatif = $pdo->lastInsertId();
			
			// Jika ada kriteria yang diinputkan:
			if(!empty($kriteria)):
				foreach($kriteria as $id_kriteria => $id_skala):
					$handle = $pdo->prepare('INSERT INTO nilai_alternatif (id_alternatif, id_kriteria, id_skala) VALUES (:id_alternatif, :id_kriteria, :id_skala)');
					$handle->execute( array(
						'id_alternatif' => $id_alternatif,
						'id_kriteria' => $id_kriteria,
						'id_skala' =>$id_skala
					) );
				endforeach;
			endif;
			
			redirect_to('list-alternatif.php?status=sukses-baru');		
		else:
			redirect_to('list-alternatif.php?status=gagal');
		endif;
	endif;

endif;
?>

<?php
$judul_page = 'Tambah Alternatif';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-alternatif.php'); ?>
	
		<div class="main-content the-content">
			<h1>Tambah Alternatif</h1>
			
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
			
			
				<form action="tambah-alternatif.php" method="post">
					<div class="field-wrap clearfix">					
						<label>Nama Alternatif <span class="red">*</span></label>
						<input type="text" name="nama_alternatif" value="<?php echo $nama_alternatif; ?>">
					</div>					
					<div class="field-wrap clearfix">					
						<label>Keterangan</label>
						<textarea name="keterangan" cols="30" rows="2"><?php echo $keterangan; ?></textarea>
					</div>			
					
					<h3>Nilai Kriteria</h3>
					<?php
					$query = $pdo->prepare('SELECT id_kriteria, nama FROM kriteria');			
					$query->execute();
					// menampilkan berupa nama field
					$query->setFetchMode(PDO::FETCH_ASSOC);
					
					if($query->rowCount() > 0):
					
						while($kriteria = $query->fetch()):							
						?>
						
							<div class="field-wrap clearfix">					
								<label><?php echo $kriteria['nama']; ?></label>
								<select name="kriteria[<?php echo $kriteria['id_kriteria']; ?>]">
									<option selected disabled value="">Pilih Kriteria</option>
										<?php 
											$query33 = $pdo->prepare('SELECT * FROM skala_penilaian where id_kriteria='.$kriteria['id_kriteria']);			
											$query33->execute();
											// menampilkan berupa nama field
											$query33->setFetchMode(PDO::FETCH_ASSOC);
											while($row = $query33->fetch()){
										?>
										<option value="<?php echo $row['id_skala']; ?>"><?php echo $row['nama']; ?></option>
									<?php } ?>					
								</select>
								<!-- <input type="number" step="0.001" name="kriteria[<?php echo $kriteria['id_kriteria']; ?>]">								 -->
							</div>	
						
						<?php
						endwhile;
						
					else:					
						echo '<p>Kriteria masih kosong.</p>';						
					endif;
					?>
					
					<div class="field-wrap clearfix">
					<a href="list-alternatif.php" class="button" style="background-color:yellow; color:black;"><span class="fa fa-backward"></span> Back</a> &nbsp; 
						<button type="submit" name="submit" value="submit" class="button">Tambah Alternatif</button>
					</div>
				</form>
					
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');