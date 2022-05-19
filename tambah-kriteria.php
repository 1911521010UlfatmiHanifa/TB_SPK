<?php require_once('includes/init.php'); ?>

<?php
$errors = array();
$sukses = false;

$nama = (isset($_POST['nama'])) ? trim($_POST['nama']) : '';
$id_tipe = (isset($_POST['id_tipe'])) ? trim($_POST['id_tipe']) : '';
$bobot = (isset($_POST['bobot'])) ? trim($_POST['bobot']) : '';

if(isset($_POST['submit'])):	
	
	// Validasi nama Kriteria
	if(!$nama) {
		$errors[] = 'Nama kriteria tidak boleh kosong';
	}		
	// Validasi Tipe
	if(!$id_tipe) {
		$errors[] = 'Tipe kriteria tidak boleh kosong';
	}
	// Validasi Bobot
	if(!$bobot) {
		$errors[] = 'Bobot kriteria tidak boleh kosong';
	}	
	
	// Jika lolos validasi lakukan hal di bawah ini
	if(empty($errors)):

		$query = $pdo->prepare('SELECT * FROM kriteria where nama = :nama');			
		$query->execute(array('nama' => $nama));
		// menampilkan berupa nama field
		$query->setFetchMode(PDO::FETCH_ASSOC);
		
		if($query->rowCount() == 0):
			$handle = $pdo->prepare('INSERT INTO kriteria (nama, id_tipe, bobot) VALUES (:nama, :id_tipe, :bobot)');
			$handle->execute( array(
				'nama' => strtoupper($nama),
				'id_tipe' => $id_tipe,
				'bobot' => $bobot		
			) );
			$id_kriteria = $pdo->lastInsertId();
			redirect_to('list-kriteria.php?status=sukses-baru');	
		else:
			redirect_to('list-kriteria.php?status=gagal');	
		endif;
	endif;

endif;
?>

<?php
$judul_page = 'Tambah Kriteria';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-kriteria.php'); ?>
	
		<div class="main-content the-content">
			<h1>Tambah Kriteria</h1>
			
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
			
				<form action="tambah-kriteria.php" method="post">
					<div class="field-wrap clearfix">					
						<label>Nama Kriteria <span class="red">*</span></label>
						<input autofocus type="text" name="nama" value="<?php echo $nama; ?>">
					</div>
					<div class="field-wrap clearfix">					
						<label>Tipe Kriteria <span class="red">*</span></label>

						<select name="id_tipe">
							<option selected disabled value="">Pilih Kriteria</option>
                                <?php 
									$query = $pdo->prepare('SELECT * FROM tipe_kriteria');			
									$query->execute();
									// menampilkan berupa nama field
									$query->setFetchMode(PDO::FETCH_ASSOC);
                                    while($row = $query->fetch()){
                                ?>
                                <option value="<?php echo $row['id_tipe']; ?>"><?php echo $row['tipe']; ?></option>
                            <?php } ?>					
						</select>
					</div>
					<div class="field-wrap clearfix">					
						<label>Bobot Kriteria <span class="red">*</span></label>
						<input type="number" min="0" name="bobot" value="<?php echo $bobot; ?>" step="0.01">
					</div>
					
					<div class="field-wrap clearfix">
						<a href="list-kriteria.php" class="button" style="background-color:yellow; color:black;"><span class="fa fa-backward"></span> Back</a> &nbsp; 
						<button type="submit" name="submit" value="submit" class="button">Tambah Kriteria</button>
					</div>
				</form>
				
			<?php //endif; ?>			
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');