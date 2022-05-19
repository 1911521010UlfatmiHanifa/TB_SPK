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
	<link rel="shortcut icon" type="image/aku.png" href="images/favicon.ico" />
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
		body{
			background: #6994e7;
		}
	</style>
	
	<header id="header">
		<div class="container clearfix">
			<div id="logo-wrap">
				<h1 id="logo"><a href="dashboard.php"><img src="images/aku.png" alt="" width="100"></a></h1>
			</div>
			
			<div id="header-content" class="clearfix">
				<nav id="nav">
					<ul class="sf-menu">						
						<li><a href="list-kriteria.php">Kriteria</a>
							<ul>
								<li><a href="list-kriteria.php">List Kriteria</a></li>
								<li><a href="tambah-kriteria.php">Tambah Kriteria</a></li>
							</ul>
						</li>
						<li><a href="list-alternatif.php">Alternatif</a>
							<ul>
								<li><a href="list-alternatif.php">List Alternatif</a></li>
								<li><a href="tambah-alternatif.php">Tambah Alternatif</a></li>
							</ul>
						</li>
						<li><a href="ranking-topsis.php">Ranking TOPSIS</a></li>
					</ul>
				</nav>
			</div>
		</div>
	</header>
	
	<div id="main">