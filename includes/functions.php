<?php
function ambil_template($nama_template = '') {
	if($nama_template) {
		require_once('template-parts/'.$nama_template.'.php');
	}	
}

function selected($param1='', $param2='') {
	if($param1 == $param2) {
		echo 'selected="selected"';
	}
}

function redirect_to($url = '') {
	header('Location: '.$url);
	exit();
}