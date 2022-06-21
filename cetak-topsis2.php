<?php
/* ---------------------------------------------
 * Konek ke database & load fungsi-fungsi
 * ------------------------------------------- */
require_once('includes/init.php');

/* ---------------------------------------------
 * Load Header
 * ------------------------------------------- */
$judul_page = 'Perankingan Menggunakan Metode TOPSIS';

/* ---------------------------------------------
 * Set jumlah digit di belakang koma
 * ------------------------------------------- */
$digit = 4;

/* ---------------------------------------------
 * Fetch semua kriteria
 * ------------------------------------------- */
$query = $pdo->prepare('SELECT id_kriteria, nama, tipe, bobot
	FROM kriteria join tipe_kriteria on kriteria.id_tipe=tipe_kriteria.id_tipe');
$query->execute();
$query->setFetchMode(PDO::FETCH_ASSOC);
$kriterias = $query->fetchAll();

/* ---------------------------------------------
 * Fetch semua alternatif (alternatif)
 * ------------------------------------------- */
$query2 = $pdo->prepare('SELECT id_alternatif, nama_alternatif FROM alternatif');
$query2->execute();			
$query2->setFetchMode(PDO::FETCH_ASSOC);
$alternatifs = $query2->fetchAll();


/* >>> STEP 1 ===================================
 * Matrix Keputusan (X)
 * ------------------------------------------- */
$matriks_x = array();
foreach($kriterias as $kriteria):
	foreach($alternatifs as $alternatif):
		
		$id_alternatif = $alternatif['id_alternatif'];
		$id_kriteria = $kriteria['id_kriteria'];
		
		// Fetch nilai dari db
		$query3 = $pdo->prepare('SELECT nilai FROM nilai_alternatif join skala_penilaian on nilai_alternatif.id_skala=skala_penilaian.id_skala
			WHERE nilai_alternatif.id_alternatif = :id_alternatif AND nilai_alternatif.id_kriteria = :id_kriteria');
		$query3->execute(array(
			'id_alternatif' => $id_alternatif,
			'id_kriteria' => $id_kriteria,
		));			
		$query3->setFetchMode(PDO::FETCH_ASSOC);
		if($nilai_alternatif = $query3->fetch()) {
			// Jika ada nilai kriterianya
			$matriks_x[$id_kriteria][$id_alternatif] = $nilai_alternatif['nilai'];
		} else {			
			$matriks_x[$id_kriteria][$id_alternatif] = 0;
		}

	endforeach;
endforeach;

/* >>> STEP 3 ===================================
 * Matriks Ternormalisasi (R)
 * ------------------------------------------- */
$matriks_r = array();
foreach($matriks_x as $id_kriteria => $nilai_alternatifs):
	
	// Mencari akar dari penjumlahan kuadrat
	$jumlah_kuadrat = 0;
	foreach($nilai_alternatifs as $nilai_alternatif):
		$jumlah_kuadrat += pow($nilai_alternatif, 2);
	endforeach;
	$akar_kuadrat = sqrt($jumlah_kuadrat);
	
	// Mencari hasil bagi akar kuadrat
	// Lalu dimasukkan ke array $matriks_r
	foreach($nilai_alternatifs as $id_alternatif => $nilai_alternatif):
		$matriks_r[$id_kriteria][$id_alternatif] = $nilai_alternatif / $akar_kuadrat;
	endforeach;
	
endforeach;


/* >>> STEP 4 ===================================
 * Matriks Y
 * ------------------------------------------- */
$matriks_y = array();
foreach($kriterias as $kriteria):
	foreach($alternatifs as $alternatif):
		
		$bobot = $kriteria['bobot'];
		$id_alternatif = $alternatif['id_alternatif'];
		$id_kriteria = $kriteria['id_kriteria'];
		
		$nilai_r = $matriks_r[$id_kriteria][$id_alternatif];
		$matriks_y[$id_kriteria][$id_alternatif] = $bobot * $nilai_r;

	endforeach;
endforeach;


/* >>> STEP 5 ================================
 * Solusi Ideal Positif & Negarif
 * ------------------------------------------- */
$solusi_ideal_positif = array();
$solusi_ideal_negatif = array();
foreach($kriterias as $kriteria):

	$id_kriteria = $kriteria['id_kriteria'];
	$type_kriteria = $kriteria['tipe'];
	
	$nilai_max = max($matriks_y[$id_kriteria]);
	$nilai_min = min($matriks_y[$id_kriteria]);
	
	if($type_kriteria == 'Benefit'):
		$s_i_p = $nilai_max;
		$s_i_n = $nilai_min;
	elseif($type_kriteria == 'Cost'):
		$s_i_p = $nilai_min;
		$s_i_n = $nilai_max;
	endif;
	
	$solusi_ideal_positif[$id_kriteria] = $s_i_p;
	$solusi_ideal_negatif[$id_kriteria] = $s_i_n;

endforeach;


/* >>> STEP 6 ================================
 * Jarak Ideal Positif & Negatif
 * ------------------------------------------- */
$jarak_ideal_positif = array();
$jarak_ideal_negatif = array();
foreach($alternatifs as $alternatif):

	$id_alternatif = $alternatif['id_alternatif'];		
	$jumlah_kuadrat_jip = 0;
	$jumlah_kuadrat_jin = 0;
	
	// Mencari penjumlahan kuadrat
	foreach($matriks_y as $id_kriteria => $nilai_alternatifs):
		
		$hsl_pengurangan_jip = $nilai_alternatifs[$id_alternatif] - $solusi_ideal_positif[$id_kriteria];
		$hsl_pengurangan_jin = $nilai_alternatifs[$id_alternatif] - $solusi_ideal_negatif[$id_kriteria];
		
		$jumlah_kuadrat_jip += pow($hsl_pengurangan_jip, 2);
		$jumlah_kuadrat_jin += pow($hsl_pengurangan_jin, 2);
	
	endforeach;
	
	// Mengakarkan hasil penjumlahan kuadrat
	$akar_kuadrat_jip = sqrt($jumlah_kuadrat_jip);
	$akar_kuadrat_jin = sqrt($jumlah_kuadrat_jin);
	
	// Memasukkan ke array matriks jip & jin
	$jarak_ideal_positif[$id_alternatif] = $akar_kuadrat_jip;
	$jarak_ideal_negatif[$id_alternatif] = $akar_kuadrat_jin;
	
endforeach;


/* >>> STEP 7 ================================
 * Perangkingan
 * ------------------------------------------- */
$ranks = array();
foreach($alternatifs as $alternatif):

	$s_negatif = $jarak_ideal_negatif[$alternatif['id_alternatif']];
	$s_positif = $jarak_ideal_positif[$alternatif['id_alternatif']];	
	
	$nilai_v = $s_negatif / ($s_positif + $s_negatif);
	
	$ranks[$alternatif['id_alternatif']]['id_alternatif'] = $alternatif['id_alternatif'];
	$ranks[$alternatif['id_alternatif']]['nama_alternatif'] = $alternatif['nama_alternatif'];
	$ranks[$alternatif['id_alternatif']]['nilai'] = $nilai_v;
	
endforeach;
	$waktu = date("Y-m-d H:i:s");
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=Data TOPSIS pada ".$waktu.".xls"); 
?>

<!DOCTYPE html>
<head>
	<meta http-equiv="x-ua-compatible" content="ie=edge" />
	<meta charset="UTF-8" />
	<title><?php
		if(isset($judul_page)) {
			echo $judul_page;
		}
	?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />
	<link rel="stylesheet" href="stylesheets/style.css">
	<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>	
	<script type="text/javascript" src="js/superfish.min.js"></script>	
	<script type="text/javascript" src="js/main.js"></script>	
</head>
<body>
	<div id="page">

	<style>
		#header {
			background: #6994e7;
			color: #000000;
		}
		#nav .sf-menu li a {
			color: #000000;
		}
		#header-content .button {
			background: #0b4854;
			color: #b2e89d;
		}
		#nav .sf-menu li ul {
			background: #6994e7;
		}
		.pure-table thead {
			background-color: #4379e1;
			border: 2px solid black;
			color : black;
		}
		.pure-table tr.super-top th {
			background: #315caf;
			border: 2px solid black;
			color : black;
		}
		table, thead, tbody, tr, td, th{
			color : black;
			border: 2px solid black;
		}
	</style>

	<div id="main">

<div class="main-content-row">
<div class="container clearfix">	

<div class="main-content main-content-full the-content">
		
		<h1><?php echo $judul_page; ?></h1>

		<!-- Nilai Kriteria dan Bobot ==================== -->
		<h3>Data Nilai Kriteria dan Bobot (W)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>
				<tr>
					<th>Nama Kriteria</th>
					<th>Tipe Kriteria</th>
					<th>Bobot (W)</th>						
				</tr>
			</thead>
			<tbody>
				<?php foreach($kriterias as $hasil): ?>
					<tr>
						<td><?php echo $hasil['nama']; ?></td>
						<td><?php echo $hasil['tipe']; ?></td>
						<td><?php echo $hasil['bobot']; ?></td>							
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h3>Skala Penilaian Kriteria</h3>
		<?php
			$query6 = $pdo->prepare('SELECT * FROM kriteria');			
			$query6->execute();
			// menampilkan berupa nama field
			$query6->setFetchMode(PDO::FETCH_ASSOC);
			
			if($query6->rowCount() > 0):
		?>
		
		<table class="pure-table pure-table-striped">
			<thead>
				<tr>
					<th>Nama Sub Kriteria</th>
					<th>Nilai</th>
				</tr>
			</thead>
			<tbody>
				<?php while($hasill = $query6->fetch()): ?>
					<tr>
						<td colspan="2" style="background-color:pink"><?php echo $hasill['nama']; ?></td>
								<?php 
									$query22 = $pdo->prepare('SELECT * FROM skala_penilaian WHERE id_kriteria='.$hasill['id_kriteria']);			
									$query22->execute();
									// menampilkan berupa nama field
									$query22->setFetchMode(PDO::FETCH_ASSOC);
									
									if($query2->rowCount() > 0):
										while($hasil22 = $query22->fetch()):
								?>
						<tr>
							<td><?php echo $hasil22['nama']; ?></td>
							<td><?php echo $hasil22['nilai']; ?></td>
								<?php endwhile; ?>
								<?php endif; ?>
						</tr>
					</tr>
				<?php endwhile; ?>
			</tbody>
		</table>
		<?php endif; ?>
		
		<h3>Data Nilai Kriteria dan Alternatif</h3>
		<?php
			// Fetch semua kriteria
			$query = $pdo->prepare('SELECT id_kriteria, nama, tipe, bobot FROM kriteria join tipe_kriteria on kriteria.id_tipe=tipe_kriteria.id_tipe');
			$query->execute();			
			$kriteriass = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
			
			// Fetch semua alternatif
			$query2 = $pdo->prepare('SELECT id_alternatif, nama_alternatif FROM alternatif');
			$query2->execute();			
			$query2->setFetchMode(PDO::FETCH_ASSOC);
			$alternatifs = $query2->fetchAll();			
		?>
		
		<table class="pure-table pure-table-striped">
			<thead>
				<tr class="super-top">
					<th rowspan="2" class="super-top-left" style="background-color: #1b4dad;">Nama alternatif</th>
					<th colspan="<?php echo count($kriteriass); ?>">Kriteria</th>
				</tr>
				<tr>
					<?php foreach($kriteriass as $kriteria ): ?>
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
						
						foreach($kriteriass as $id_kriteria => $values):
							echo '<td>';
							if(isset($nilais[$id_kriteria])) {
								echo $nilais[$id_kriteria]['nama'];
							} 
							
							if(isset($kriteriass[$id_kriteria]['tn_kuadrat'])){
								$kriterisas[$id_kriteria]['tn_kuadrat'] += pow($kriteriass[$id_kriteria]['nama'][$alternatif['id_alternatif']], 2);
							} 
							echo '</td>';
						endforeach;
						?>
						</pre>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
						
		<h2 align="center">P E R H I T U N G A N</h2>

		<!-- STEP 1. Matriks Keputusan(X) ==================== -->		
		<h3>Step 1: Matriks Keputusan (X)</h3>
		<table class="pure-table pure-table-striped">
			<thead>
				<tr class="super-top">
					<th rowspan="2" class="super-top-left" style="background-color: #1b4dad;">Nama Alternatif</th>
					<th colspan="<?php echo count($kriterias); ?>" style="background-color: ##3967bf;">Kriteria</th>
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
						foreach($kriterias as $kriteria):
							$id_alternatif = $alternatif['id_alternatif'];
							$id_kriteria = $kriteria['id_kriteria'];
							echo '<td>';
							echo $matriks_x[$id_kriteria][$id_alternatif];
							echo '</td>';
						endforeach;
						?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<!-- Step 2: Matriks Ternormalisasi (R) ==================== -->
		<h3>Step 2: Matriks Ternormalisasi (R)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>
				<tr class="super-top">
					<th rowspan="2" class="super-top-left"  style="background-color: #1b4dad;">Nama Alternatif</th>
					<th colspan="<?php echo count($kriterias); ?>" style="background-color: #2f60bd;">Kriteria</th>
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
						foreach($kriterias as $kriteria):
							$id_alternatif = $alternatif['id_alternatif'];
							$id_kriteria = $kriteria['id_kriteria'];
							echo '<td>';
							echo round($matriks_r[$id_kriteria][$id_alternatif], $digit);
							echo '</td>';
						endforeach;
						?>
					</tr>
				<?php endforeach; ?>				
			</tbody>
		</table>
		
		
		<!-- Step 3: Matriks Y ==================== -->
		<h3>Step 3: Matriks Y</h3>			
		<table class="pure-table pure-table-striped">
			<thead>
				<tr class="super-top">
					<th rowspan="2" class="super-top-left"  style="background-color: #1b4dad;">Nama Alternatif</th>
					<th colspan="<?php echo count($kriterias); ?>" style="background-color: #2f60bd;">Kriteria</th>
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
						foreach($kriterias as $kriteria):
							$id_alternatif = $alternatif['id_alternatif'];
							$id_kriteria = $kriteria['id_kriteria'];
							echo '<td>';
							echo round($matriks_y[$id_kriteria][$id_alternatif], $digit);
							echo '</td>';
						endforeach;
						?>
					</tr>
				<?php endforeach; ?>	
			</tbody>
		</table>	
		
		
		<!-- Step 4.1: Solusi Ideal Positif ==================== -->
		<h3>Step 4.1: Solusi Ideal Positif (A<sup>+</sup>)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<th><?php echo $kriteria['nama']; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<td>
							<?php
							$id_kriteria = $kriteria['id_kriteria'];							
							echo round($solusi_ideal_positif[$id_kriteria], $digit);
							?>
						</td>
					<?php endforeach; ?>
				</tr>					
			</tbody>
		</table>
		
		<!-- Step 4.2: Solusi Ideal negative ==================== -->
		<h3>Step 4.2: Solusi Ideal Negatif (A<sup>-</sup>)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<th><?php echo $kriteria['nama']; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<td>
							<?php
							$id_kriteria = $kriteria['id_kriteria'];							
							echo round($solusi_ideal_negatif[$id_kriteria], $digit);
							?>
						</td>
					<?php endforeach; ?>
				</tr>					
			</tbody>
		</table>		
		
		<!-- Step 5.1: Jarak Ideal Positif ==================== -->
		<h3>Step 5.1: Jarak Ideal Positif (S<sub>i</sub>+)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<th class="super-top-left"  style="background-color: #1b4dad;">Nama Alternatif</th>
					<th>Jarak Ideal Positif</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($alternatifs as $alternatif ): ?>
					<tr>
						<td><?php echo $alternatif['nama_alternatif']; ?></td>
						<td>
							<?php								
							$id_alternatif = $alternatif['id_alternatif'];
							echo round($jarak_ideal_positif[$id_alternatif], $digit);
							?>
						</td>						
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<!-- Step 5.2: Jarak Ideal Negatif ==================== -->
		<h3>Step 5.2: Jarak Ideal Negatif (S<sub>i</sub>-)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<th class="super-top-left"  style="background-color: #1b4dad;">Nama Alternatif</th>
					<th>Jarak Ideal Negatif</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($alternatifs as $alternatif ): ?>
					<tr>
						<td><?php echo $alternatif['nama_alternatif']; ?></td>
						<td>
							<?php								
							$id_alternatif = $alternatif['id_alternatif'];
							echo round($jarak_ideal_negatif[$id_alternatif], $digit);
							?>
						</td>						
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<!-- Step 6: Nilai Preverensi (V) ==================== -->	
		<h3>Step 6: Nilai Preverensi (V)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<th class="super-top-left" style="background-color: #1b4dad;">Nama Alternatif</th>
					<th>Ranking</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($ranks as $alternatif ): ?>
					<tr>
						<td><?php echo $alternatif['nama_alternatif']; ?></td>
						<td><?php echo round($alternatif['nilai'], $digit); ?></td>											
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<!-- Step 7: Perangkingan ==================== -->
		<?php		
		$sorted_ranks = $ranks;	
		
		// Sorting
		if(function_exists('array_multisort')):
			foreach ($sorted_ranks as $key => $row) {
				$nama_alternatif[$key]  = $row['nama_alternatif'];
				$nilai[$key] = $row['nilai'];
			}
			array_multisort($nilai, SORT_DESC, $nama_alternatif, SORT_ASC, $sorted_ranks);
		endif;
		?>		
		<h3>Step 7: Perangkingan</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<th class="super-top-left" style="background-color: #1b4dad;">Nama Alternatif</th>
					<th>Ranking</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($sorted_ranks as $alternatif ): ?>
					<tr>
						<td><?php echo $alternatif['nama_alternatif']; ?></td>
						<td><?php echo round($alternatif['nilai'], $digit); ?></td>											
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<h3>
			Jadi, dapat disimpulkan bahwa urutan nilai alternatif terbaik untuk penerima bantuan perbaikan rumah akibat gempa di Pasaman Barat adalah 
			<?php 
				foreach($sorted_ranks as $alternatif ):
					echo $alternatif['nama_alternatif'], " .. ";
				endforeach;
			?>
		</h3>			
		
	</div>
	</div>
		<script>
			window.print();
		</script>
	</div>

</div><!-- .container -->
</div><!-- .main-content-row -->

<?php
require_once('template-parts/footer.php');