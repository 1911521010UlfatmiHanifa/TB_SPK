<?php require_once('includes/init.php'); ?>

<?php
$judul_page = 'List Alternatif';
require_once('template-parts/header.php');
?>

<style>
	.pure-table thead {
		background-color: #4379e1;
	}
	.pure-table thead {
		background-color: #4379e1;
	}
	.pure-table tr.super-top th {
		background: #315caf;
	}
</style>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-alternatif.php'); ?>
	
		<div class="main-content the-content">
			
			<?php
			$status = isset($_GET['status']) ? $_GET['status'] : '';
			$msg = '';
			switch($status):
				case 'sukses-baru':
					$msg = 'Alternatif baru berhasil ditambahkan';
					break;
				case 'sukses-hapus':
					$msg = 'Alternatif behasil dihapus';
					break;
				case 'sukses-edit':
					$msg = 'Alternatif behasil diedit';
					break;
				case 'gagal':
					$msg = 'Alternatif sudah ada';
					break;
			endswitch;
			
			if($msg):
				echo '<div class="msg-box msg-box-full">';
				echo '<p><span class="fa fa-bullhorn"></span> &nbsp; '.$msg.'</p>';
				echo '</div>';
			endif;
			?>
		
			<h1>List Alternatif</h1>
			
			<?php
			$query = $pdo->prepare('SELECT * FROM alternatif');			
			$query->execute();
			// menampilkan berupa nama field
			$query->setFetchMode(PDO::FETCH_ASSOC);
			
			if($query->rowCount() > 0):
			?>
			
			<table class="pure-table pure-table-striped">
				<thead>
					<tr>
						<th>Nama Alternatif</th>
						<th>Keterangan</th>
						<th>Detail</th>						
						<th>Edit</th>
						<th>Hapus</th>
					</tr>
				</thead>
				<tbody>
					<?php while($hasil = $query->fetch()): ?>
						<tr>
							<td><?php echo $hasil['nama_alternatif']; ?></td>							
							<td><?php echo $hasil['keterangan']; ?></td>							
							<td><a href="single-alternatif.php?id=<?php echo $hasil['id_alternatif']; ?>"><span class="fa fa-eye"></span> Detail</a></td>
							<td><a href="edit-alternatif.php?id=<?php echo $hasil['id_alternatif']; ?>"><span class="fa fa-pencil"></span> Edit</a></td>
							<td><a href="hapus-alternatif.php?id=<?php echo $hasil['id_alternatif']; ?>" class="red yaqin-hapus"><span class="fa fa-times"></span> Hapus</a></td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
			
			<?php
			// Fetch semua kriteria
			$query = $pdo->prepare('SELECT id_kriteria, nama, tipe, bobot FROM kriteria join tipe_kriteria on kriteria.id_tipe=tipe_kriteria.id_tipe');
			$query->execute();			
			$kriterias = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
			
			// Fetch semua alternatif
			$query2 = $pdo->prepare('SELECT id_alternatif, nama_alternatif FROM alternatif');
			$query2->execute();			
			$query2->setFetchMode(PDO::FETCH_ASSOC);
			$alternatifs = $query2->fetchAll();			
			?>
			
			<h3>Nilai Kriteria Setiap Alternatif</h3>
			<table class="pure-table pure-table-striped">
				<thead>
					<tr class="super-top">
						<th rowspan="2" class="super-top-left" style="background-color: #1b4dad;">Nama alternatif</th>
						<th colspan="<?php echo count($kriterias); ?>">Kriteria</th>
					</tr>
					<tr>
						<?php foreach($kriterias as $kriteria ): ?>
							<th><?php echo $kriteria['nama']; ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach($alternatifs as $alternatif): ?>
						<tr>
							<td><?php echo $alternatif['nama_alternatif']; ?></td>
							<?php
							// Ambil Nilai
							$query3 = $pdo->prepare('SELECT nilai_alternatif.id_kriteria as id_kriteria, nama FROM nilai_alternatif join skala_penilaian on skala_penilaian.id_skala=nilai_alternatif.id_skala
								WHERE id_alternatif = :id_alternatif');
							$query3->execute(array(
								'id_alternatif' => $alternatif['id_alternatif']
							));			
							$query3->setFetchMode(PDO::FETCH_ASSOC);
							$nilais = $query3->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
							
							foreach($kriterias as $id_kriteria => $values):
								echo '<td>';
								if(isset($nilais[$id_kriteria])) {
									echo $nilais[$id_kriteria]['nama'];
									// $kriterias[$id_kriteria]['nama'][$alternatif['id_alternatif']] = $nilais[$id_kriteria]['nama'];
								} 
								else {
									echo 0;
								// 	$kriterias[$id_kriteria]['nama'][$alternatif['id_alternatif']] = 0;
								}
								
								if(isset($kriterias[$id_kriteria]['tn_kuadrat'])){
									$kriterias[$id_kriteria]['tn_kuadrat'] += pow($kriterias[$id_kriteria]['nama'][$alternatif['id_alternatif']], 2);
								} 
								// else {
								// 	$kriterias[$id_kriteria]['tn_kuadrat'] = pow($kriterias[$id_kriteria]['nama'][$alternatif['id_alternatif']], 2);
								// }
								echo '</td>';
							endforeach;
							?>
							</pre>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php else: ?>
				<p>Maaf, belum ada data untuk alternatif.</p>
			<?php endif; ?>
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->

<?php
require_once('template-parts/footer.php');