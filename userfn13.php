<?php

// Global user functions
// Page Loading event
function Page_Loading() {

	//echo "Page Loading";
}

// Page Rendering event
function Page_Rendering() {

	//echo "Page Rendering";
}

// Page Unloaded event
function Page_Unloaded() {

	//echo "Page Unloaded";
}

function CurrentNama(){
	$CurrentNama=ew_ExecuteScalar("SELECT Nama FROM users WHERE Username='".CurrentUserName()."'");
	return $CurrentNama;
}

function CurrentLevelName(){
	$CurrentNama=ew_ExecuteScalar("SELECT userlevelname FROM userlevels WHERE userlevelid='".CurrentUserLevel()."'");
	return $CurrentNama;
}

function SetTitle(){
	$title = CurrentPage()->TableName;
	if($title=="home.php"){
		$title ="Dashboard | ";
	}
	else if($title=="import_data.php"){
		$title ="Import Data | ";
	}else{
		$title =ucfirst(CurrentPageID())." ".ucfirst(CurrentPage()->TableName)." | ";
	}
	return $title;
}
?>
