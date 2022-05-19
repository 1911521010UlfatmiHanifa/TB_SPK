<?php require_once('includes/init.php'); ?>

<?php
$judul_page = 'List Kriteria';
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
	
		<?php include_once('template-parts/sidebar-kriteria.php'); ?>
	
		<div class="main-content the-content">
			
			<?php
			$status = isset($_GET['status']) ? $_GET['status'] : '';
			$msg = '';
			switch($status):
				case 'sukses-baru':
					$msg = 'Kriteria baru berhasil dibuat';
					break;
				case 'sukses-hapus':
					$msg = 'Kriteria behasil dihapus';
					break;
				case 'sukses-edit':
					$msg = 'Kriteria behasil diedit';
					break;
				case 'gagal':
					$msg = 'Kriteria sudah ada';
					break;
				case 'sukses-baru2':
					$msg = 'Sub kriteria baru berhasil dibuat';
					break;
				case 'sukses-hapus2':
					$msg = 'Sub kriteria behasil dihapus';
					break;
				case 'sukses-edit2':
					$msg = 'Sub kriteria behasil diedit';
					break;
				case 'gagal2':
					$msg = 'Sub kriteria sudah ada';
					break;
			endswitch;
			
			if($msg):
				echo '<div class="msg-box msg-box-full">';
				echo '<p><span class="fa fa-bullhorn"></span> &nbsp; '.$msg.'</p>';
				echo '</div>';
			endif;
			?>
			
			<h1>List Kriteria</h1>
			
			<?php
			$query = $pdo->prepare('SELECT * FROM kriteria join tipe_kriteria on kriteria.id_tipe=tipe_kriteria.id_tipe');			
			$query->execute();
			// menampilkan berupa nama field
			$query->setFetchMode(PDO::FETCH_ASSOC);
			
			if($query->rowCount() > 0):
			?>
			
			<table class="pure-table pure-table-striped">
				<thead>
					<tr>
						<th>Nama Kriteria</th>
						<th>Tipe Kriteria</th>
						<th>Bobot</th>
						<th>Tambah Sub Kriteria</th>
						<th>Edit</th>
						<th>Hapus</th>
					</tr>
				</thead>
				<tbody>
					<?php while($hasil = $query->fetch()): ?>
						<tr>
							<td><?php echo $hasil['nama']; ?></td>
							<td><?php echo $hasil['tipe']; ?></td>
							<td><?php echo $hasil['bobot']; ?></td>									
							<td><a href="tambah-sub.php?id=<?php echo $hasil['id_kriteria']; ?>"><span class="fa fa-plus"></span> Tambah</a></td>
							<td><a href="edit-kriteria.php?id=<?php echo $hasil['id_kriteria']; ?>"><span class="fa fa-pencil"></span> Edit</a></td>
							<td><a href="hapus-kriteria.php?id=<?php echo $hasil['id_kriteria']; ?>" class="red yaqin-hapus"><span class="fa fa-times"></span> Hapus</a></td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>

			<?php else: ?>
				<p>Maaf, belum ada data untuk kriteria.</p>
			<?php endif; ?>

			<?php
			$query = $pdo->prepare('SELECT * FROM kriteria');			
			$query->execute();
			// menampilkan berupa nama field
			$query->setFetchMode(PDO::FETCH_ASSOC);
			
			if($query->rowCount() > 0):
			?>
			
			<table class="pure-table pure-table-striped">
				<thead>
					<tr>
						<th>Nama Sub Kriteria</th>
						<th>Nilai</th>
						<th>Edit</th>
						<th>Hapus</th>
					</tr>
				</thead>
				<tbody>
					<?php while($hasil = $query->fetch()): ?>
						<tr>
							<td colspan="5" style="background-color:pink"><?php echo $hasil['nama']; ?></td>
									<?php 
										$query2 = $pdo->prepare('SELECT * FROM skala_penilaian WHERE id_kriteria='.$hasil['id_kriteria']);			
										$query2->execute();
										// menampilkan berupa nama field
										$query2->setFetchMode(PDO::FETCH_ASSOC);
										
										if($query2->rowCount() > 0):
											while($hasil2 = $query2->fetch()):
									?>
							<tr>
								<td><?php echo $hasil2['nama']; ?></td>
								<td><?php echo $hasil2['nilai']; ?></td>									
								<td><a href="edit-sub.php?id_ska=<?php echo $hasil2['id_skala']?>"><span class="fa fa-pencil"></span> Edit</a></td>
								<td><a href="hapus-sub.php?id_ska=<?php echo $hasil2['id_skala']?>" class="red yaqin-hapus"><span class="fa fa-times"></span> Hapus</a></td>
									<?php endwhile; ?>
									<?php endif; ?>
							</tr>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
			<?php endif; ?>
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->

<?php
require_once('template-parts/footer.php');