	
	</div><!-- #main -->

	<style>
		#footer {
			background: #6994e7;
			color: #000000;
		}
	</style>
	
	<footer id="footer">
		<div class="container" align="center">
			<p>@<?php echo date('Y')?> Kelompok 3 Kelas Sistem Penunjang Keputusan B</p>
		</div>
	</footer>

	</div><!-- #page -->
</body>
</html>
<?php
if(isset($pdo)) {
	// Tutup Koneksi
	$pdo = null;
}
?>