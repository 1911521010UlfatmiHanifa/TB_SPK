<?php require_once('includes/init.php'); ?>

<?php
$errors = array();
$sukses = false;

$id_kriteria = (isset($_GET['id'])) ? trim($_GET['id']) : '';
$query = $pdo->prepare('SELECT * FROM kriteria WHERE kriteria.id_kriteria = :id_kriteria');
$query->execute(array('id_kriteria' => $id_kriteria));
$result = $query->fetch();
$nama_kriteria = (isset($result['nama'])) ? trim($result['nama']) : '';

$id_kriteriaa = (isset($_POST['id_kriteria'])) ? trim($_POST['id_kriteria']) : '';
$nama = (isset($_POST['nama'])) ? trim($_POST['nama']) : '';
$nilai = (isset($_POST['nilai'])) ? trim($_POST['nilai']) : '';

if(isset($_POST['submit'])):	
	
	// Validasi nama Kriteria
	if(!$nama) {
		$errors[] = 'Nama sub kriteria tidak boleh kosong';
	}		
	// Validasi nilai
	if(!$nilai) {
		$errors[] = 'Nilai kriteria tidak boleh kosong';
	}	
	
	// Jika lolos validasi lakukan hal di bawah ini
	if(empty($errors)):

		$query = $pdo->prepare('SELECT * FROM skala_penilaian where nama = :nama and id_kriteria=:id_kriteria');			
		$query->execute(array('nama' => $nama, 'id_kriteria' => $id_kriteriaa));
		// menampilkan berupa nama field
		$query->setFetchMode(PDO::FETCH_ASSOC);
		
		if($query->rowCount() == 0):
		
			$handle = $pdo->prepare('INSERT INTO skala_penilaian (id_kriteria, nama, nilai) VALUES (:id_kriteria, :nama, :nilai)');
			$handle->execute( array(
				'id_kriteria' => $id_kriteriaa,
				'nama' => strtoupper($nama),
				'nilai' => $nilai		
			) );
		
			redirect_to('list-kriteria.php?status=sukses-baru2');
		else:
			redirect_to('list-kriteria.php?status=gagal2');	
		endif;
	
	endif;

endif;
?>

<?php
$judul_page = 'Tambah Sub Kriteria';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-kriteria.php'); ?>
	
		<div class="main-content the-content">
			<h1>Tambah Sub Kriteria</h1>
			
			<?php if(!empty($errors)): ?>
			
				<div class="msg-box warning-box">
					<p><strong>Error:</strong></p>
					<ul>
						<?php foreach($errors as $error): ?>
							<li><?php echo $error; ?></li>
							<?php 
								redirect_to('tambah-sub.php?id='.$id_kriteriaa);
							?>
						<?php endforeach; ?>
					</ul>
				</div>
				
			<?php endif; ?>			
			
				<form action="tambah-sub.php" method="post">
                    <div class="field-wrap clearfix">					
						<label>Nama Kriteria</label>
						<input type="hidden" name="id_kriteria" value="<?php echo $id_kriteria; ?>" readonly>
						<input type="text" value="<?php echo $nama_kriteria; ?>" readonly>
					</div>
                    <div class="field-wrap clearfix">					
						<label>Nama Sub Kriteria <span class="red">*</span></label>
						<input autofocus type="text" name="nama" value="<?php echo $nama; ?>">
					</div>
					<div class="field-wrap clearfix">					
						<label>Nilai Sub Kriteria <span class="red">*</span></label>
						<input type="number" min="0" max="5" name="nilai" value="<?php echo $nilai; ?>" step="0.01">
					</div>
					
					<div class="field-wrap clearfix">
						<a href="list-kriteria.php" class="button" style="background-color:yellow; color:black;"><span class="fa fa-backward"></span> Back</a> &nbsp; 
						<button type="submit" name="submit" value="submit" class="button">Tambah Sub Kriteria</button>
					</div>
				</form>
				
			<?php //endif; ?>			
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');