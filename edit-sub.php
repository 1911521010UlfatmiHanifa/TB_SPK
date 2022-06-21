<?php 
	require_once('includes/init.php'); 
?>
<?php
$errors = array();
$sukses = false;

$ada_error = false;
$result = '';

$id_skala = (isset($_GET['id_ska'])) ? trim($_GET['id_ska']) : '';

if(!$id_skala) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT id_skala, kriteria.nama as nama_k, skala_penilaian.nama as nama_s, nilai, kriteria.id_kriteria as id
                            FROM skala_penilaian join kriteria on skala_penilaian.id_kriteria=kriteria.id_kriteria 
                            WHERE id_skala = :id_skala');
	$query->execute(array('id_skala' => $id_skala));
	$result = $query->fetch();
	
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	}

	$id_skala = (isset($result['id_skala'])) ? trim($result['id_skala']) : '';
	$nama_kriteria = (isset($result['nama_k'])) ? trim($result['nama_k']) : '';
	$nama_skala = (isset($result['nama_s'])) ? trim($result['nama_s']) : '';
    $nilai = (isset($result['nilai'])) ? trim($result['nilai']) : '';
	$id_kriteria = (isset($result['id'])) ? trim($result['id']) : '';
}

if(isset($_POST['submit'])):	
	
    $id_skala = (isset($_POST['id_skala'])) ? trim($_POST['id_skala']) : '';
    $nama = (isset($_POST['nama'])) ? trim($_POST['nama']) : '';
    $nilai = (isset($_POST['nilai'])) ? trim($_POST['nilai']) : '';
    $id_kriteria = (isset($_POST['id_kriteria'])) ? trim($_POST['id_kriteria']) : '';
	
	// Validasi nama Kriteria
	if(!$nama) {
		$errors[] = 'Nama sub kriteria tidak boleh kosong';
	}
	// Validasi Nilai
	if(!$nilai) {
		$errors[] = 'Nilai kriteria tidak boleh kosong';
	}
	
	// Jika lolos validasi lakukan hal di bawah ini
	if(empty($errors)):
		$query = $pdo->prepare('SELECT * FROM skala_penilaian where nama = :nama and id_kriteria=:id_kriteria and nilai=:nilai');			
		$query->execute(array('nama' => $nama, 'id_kriteria' => $id_kriteria, 'nilai' => $nilai));
		// menampilkan berupa nama field
		$query->setFetchMode(PDO::FETCH_ASSOC);
		
		if($query->rowCount() == 0):
			$prepare_query = 'UPDATE skala_penilaian SET nama = :nama, nilai = :nilai WHERE id_skala = :id_skala';
			$data = array(
				'nama' => strtoupper($nama),
				'nilai' => $nilai,
				'id_skala' => $id_skala		
			);		
			$handle = $pdo->prepare($prepare_query);		
			$sukses = $handle->execute($data);
			
			redirect_to('list-kriteria.php?status=sukses-edit2');
		else:
			redirect_to('list-kriteria.php?status=gagal2');	
		endif;
	endif;

endif;
?>

<?php
$judul_page = 'Edit Sub Kriteria';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-kriteria.php'); ?>
	
		<div class="main-content the-content">
			<h1>Edit Sub Kriteria</h1>
			
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
				
				<form action="edit-sub.php?id=<?php echo $id_skala; ?>" method="post">
					<div class="field-wrap clearfix">					
						<label>Nama Kriteria <span class="red">*</span></label>
						<input type="text" value="<?php echo $nama_kriteria; ?>">
						<input type="hidden" name="id_skala" value="<?php echo $id_skala; ?>">
						<input type="hidden" name="id_kriteria" value="<?php echo $id_kriteria; ?>">
					</div>
                    <div class="field-wrap clearfix">					
						<label>Nama Sub Kriteria <span class="red">*</span></label>
						<input autofocus type="text" name="nama" value="<?php echo $nama_skala; ?>">
					</div>
					<div class="field-wrap clearfix">					
						<label>Nilai Sub Kriteria <span class="red">*</span></label>
						<input type="number" min="0" max="5" name="nilai" value="<?php echo $nilai; ?>" step="0.01">
					</div>										
					<div class="field-wrap clearfix">
						<a href="list-kriteria.php" class="button" style="background-color:yellow; color:black;"><span class="fa fa-backward"></span> Back</a> &nbsp; 
						<button type="submit" name="submit" value="submit" class="button">Simpan Sub Kriteria</button>
					</div>
				</form>
				
			<?php endif; ?>			
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');