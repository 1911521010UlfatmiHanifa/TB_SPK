<?php require_once('includes/init.php'); ?>

<?php
$ada_error = false;
$result = '';

$id_skala = (isset($_GET['id_ska'])) ? trim($_GET['id_ska']) : '';

if(!$id_skala) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT id_skala FROM skala_penilaian WHERE id_skala = :id_skala');
	$query->execute(array('id_skala' => $id_skala));
	$result = $query->fetch();
	
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	} else {
		$handle = $pdo->prepare('DELETE FROM skala_penilaian WHERE id_skala = :id_skala');				
		$handle->execute(array(
			'id_skala' => $result['id_skala']
		));
		redirect_to('list-kriteria.php?status=sukses-hapus2');
	}
}
?>

<?php
$judul_page = 'Hapus Sub Kriteria';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-kriteria.php'); ?>
	
		<div class="main-content the-content">
			<h1><?php echo $judul_page; ?></h1>
			
			<?php if($ada_error): ?>
			
				<?php echo '<p>'.$ada_error.'</p>'; ?>
			
			<?php endif; ?>
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');