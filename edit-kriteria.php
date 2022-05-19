<?php 
	require_once('includes/init.php'); 
?>

<?php
$errors = array();
$sukses = false;

$ada_error = false;
$result = '';

$id_kriteria = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(!$id_kriteria) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT * FROM kriteria join tipe_kriteria on kriteria.id_tipe=tipe_kriteria.id_tipe WHERE kriteria.id_kriteria = :id_kriteria');
	$query->execute(array('id_kriteria' => $id_kriteria));
	$result = $query->fetch();
	
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	}

	$id_kriteria = (isset($result['id_kriteria'])) ? trim($result['id_kriteria']) : '';
	$nama = (isset($result['nama'])) ? trim($result['nama']) : '';
	$id_tipe = (isset($result['id_tipe'])) ? trim($result['id_tipe']) : '';
	$bobot = (isset($result['bobot'])) ? trim($result['bobot']) : '';
}

if(isset($_POST['submit'])):	
	
	$id_tipe = (isset($_POST['id_tipe'])) ? trim($_POST['id_tipe']) : '';
	$bobot = (isset($_POST['bobot'])) ? trim($_POST['bobot']) : '';
	
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
		$query = $pdo->prepare('SELECT * FROM kriteria where nama = :nama and id_tipe = :id_tipe and bobot = :bobot');			
		$query->execute(array('nama' => $nama, 'id_tipe' => $id_tipe, 'bobot' => $bobot));
		// menampilkan berupa nama field
		$query->setFetchMode(PDO::FETCH_ASSOC);
		
		if($query->rowCount() == 0):
			$prepare_query = 'UPDATE kriteria SET nama = :nama, id_tipe = :id_tipe, bobot = :bobot WHERE id_kriteria = :id_kriteria';
			$data = array(
				'nama' => strtoupper($nama),
				'id_tipe' => $id_tipe,
				'bobot' => $bobot,
				'id_kriteria' => $id_kriteria		
			);		
			$handle = $pdo->prepare($prepare_query);		
			$sukses = $handle->execute($data);
			
			redirect_to('list-kriteria.php?status=sukses-edit');
		else:
			redirect_to('list-kriteria.php?status=gagal');	
		endif;
	endif;

endif;
?>

<?php
$judul_page = 'Edit Kriteria';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-kriteria.php'); ?>
	
		<div class="main-content the-content">
			<h1>Edit Kriteria</h1>
			
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
				
				<form action="edit-kriteria.php?id=<?php echo $id_kriteria; ?>" method="post">
					<div class="field-wrap clearfix">					
						<label>Nama Kriteria <span class="red">*</span></label>
						<input autofocus type="text" name="nama" value="<?php echo $nama; ?>">
					</div>
					<div class="field-wrap clearfix">					
						<label>Tipe Kriteria <span class="red">*</span></label>
						<select name="id_tipe">
							<option selected disabled value="">Pilih Tipe Kriteria</option>
                            <?php 
								$query = $pdo->prepare('SELECT * FROM tipe_kriteria');			
								$query->execute();
								// menampilkan berupa nama field
								$query->setFetchMode(PDO::FETCH_ASSOC);
								while($row = $query->fetch()){
							?>
							<option value="<?php echo $row['id_tipe']; ?>"
								<?php if($id_tipe == $row['id_tipe']) {
									echo 'selected';
								}?>>
								<?php echo $row['tipe']; ?>
							</option>
                            <?php } ?>					
						</select>
					</div>
					<div class="field-wrap clearfix">					
						<label>Bobot Kriteria <span class="red">*</span></label>
						<input type="number" min="0" name="bobot" value="<?php echo $bobot; ?>" step="0.01">
					</div>										
					<div class="field-wrap clearfix">
						<a href="list-kriteria.php" class="button" style="background-color:yellow; color:black;"><span class="fa fa-backward"></span> Back</a> &nbsp; 
						<button type="submit" name="submit" value="submit" class="button">Simpan Kriteria</button>
					</div>
				</form>
				
			<?php endif; ?>			
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');