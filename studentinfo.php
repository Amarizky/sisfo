<?php

// Global variable for table object
$student = NULL;

//
// Table class for student
//
class cstudent extends cTable {
	var $AuditTrailOnAdd = TRUE;
	var $AuditTrailOnEdit = TRUE;
	var $AuditTrailOnDelete = TRUE;
	var $AuditTrailOnView = FALSE;
	var $AuditTrailOnViewData = FALSE;
	var $AuditTrailOnSearch = FALSE;
	var $StudentID;
	var $Nama;
	var $LevelID;
	var $Password;
	var $KampusID;
	var $ProdiID;
	var $StudentStatusID;
	var $TahunID;
	var $Foto;
	var $NIK;
	var $WargaNegara;
	var $Kelamin;
	var $TempatLahir;
	var $TanggalLahir;
	var $AgamaID;
	var $Darah;
	var $StatusSipil;
	var $AlamatDomisili;
	var $RT;
	var $RW;
	var $KodePos;
	var $ProvinsiID;
	var $KabupatenKotaID;
	var $KecamatanID;
	var $DesaID;
	var $AnakKe;
	var $JumlahSaudara;
	var $Telepon;
	var $Handphone;
	var $_Email;
	var $NamaAyah;
	var $AgamaAyah;
	var $PendidikanAyah;
	var $PekerjaanAyah;
	var $HidupAyah;
	var $NamaIbu;
	var $AgamaIbu;
	var $PendidikanIbu;
	var $PekerjaanIbu;
	var $HidupIbu;
	var $AlamatOrtu;
	var $RTOrtu;
	var $RWOrtu;
	var $KodePosOrtu;
	var $ProvinsiIDOrtu;
	var $KabupatenIDOrtu;
	var $KecamatanIDOrtu;
	var $DesaIDOrtu;
	var $NegaraIDOrtu;
	var $TeleponOrtu;
	var $HandphoneOrtu;
	var $EmailOrtu;
	var $AsalSekolah;
	var $AlamatSekolah;
	var $ProvinsiIDSekolah;
	var $KabupatenIDSekolah;
	var $KecamatanIDSekolah;
	var $DesaIDSekolah;
	var $NilaiSekolah;
	var $TahunLulus;
	var $IjazahSekolah;
	var $TglIjazah;
	var $Creator;
	var $CreateDate;
	var $Editor;
	var $EditDate;
	var $LockStatus;
	var $LockDate;
	var $VerifiedBy;
	var $NA;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'student';
		$this->TableName = 'student';
		$this->TableType = 'TABLE';

		// Update Table
		$this->UpdateTable = "`student`";
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->ExportExcelPageOrientation = ""; // Page orientation (PHPExcel only)
		$this->ExportExcelPageSize = ""; // Page size (PHPExcel only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 25;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// StudentID
		$this->StudentID = new cField('student', 'student', 'x_StudentID', 'StudentID', '`StudentID`', '`StudentID`', 200, -1, FALSE, '`StudentID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->StudentID->Sortable = TRUE; // Allow sort
		$this->fields['StudentID'] = &$this->StudentID;

		// Nama
		$this->Nama = new cField('student', 'student', 'x_Nama', 'Nama', '`Nama`', '`Nama`', 200, -1, FALSE, '`Nama`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Nama->Sortable = TRUE; // Allow sort
		$this->fields['Nama'] = &$this->Nama;

		// LevelID
		$this->LevelID = new cField('student', 'student', 'x_LevelID', 'LevelID', '`LevelID`', '`LevelID`', 200, -1, FALSE, '`LevelID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->LevelID->Sortable = TRUE; // Allow sort
		$this->LevelID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['LevelID'] = &$this->LevelID;

		// Password
		$this->Password = new cField('student', 'student', 'x_Password', 'Password', '`Password`', '`Password`', 200, -1, FALSE, '`Password`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'PASSWORD');
		$this->Password->Sortable = TRUE; // Allow sort
		$this->fields['Password'] = &$this->Password;

		// KampusID
		$this->KampusID = new cField('student', 'student', 'x_KampusID', 'KampusID', '`KampusID`', '`KampusID`', 200, -1, FALSE, '`KampusID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KampusID->Sortable = TRUE; // Allow sort
		$this->KampusID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KampusID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['KampusID'] = &$this->KampusID;

		// ProdiID
		$this->ProdiID = new cField('student', 'student', 'x_ProdiID', 'ProdiID', '`ProdiID`', '`ProdiID`', 200, -1, FALSE, '`ProdiID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->ProdiID->Sortable = TRUE; // Allow sort
		$this->ProdiID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->ProdiID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['ProdiID'] = &$this->ProdiID;

		// StudentStatusID
		$this->StudentStatusID = new cField('student', 'student', 'x_StudentStatusID', 'StudentStatusID', '`StudentStatusID`', '`StudentStatusID`', 200, -1, FALSE, '`StudentStatusID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->StudentStatusID->Sortable = TRUE; // Allow sort
		$this->StudentStatusID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->StudentStatusID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['StudentStatusID'] = &$this->StudentStatusID;

		// TahunID
		$this->TahunID = new cField('student', 'student', 'x_TahunID', 'TahunID', '`TahunID`', '`TahunID`', 3, -1, FALSE, '`TahunID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TahunID->Sortable = TRUE; // Allow sort
		$this->TahunID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['TahunID'] = &$this->TahunID;

		// Foto
		$this->Foto = new cField('student', 'student', 'x_Foto', 'Foto', '`Foto`', '`Foto`', 200, -1, TRUE, '`Foto`', FALSE, FALSE, FALSE, 'IMAGE', 'FILE');
		$this->Foto->Sortable = TRUE; // Allow sort
		$this->fields['Foto'] = &$this->Foto;

		// NIK
		$this->NIK = new cField('student', 'student', 'x_NIK', 'NIK', '`NIK`', '`NIK`', 200, -1, FALSE, '`NIK`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NIK->Sortable = TRUE; // Allow sort
		$this->fields['NIK'] = &$this->NIK;

		// WargaNegara
		$this->WargaNegara = new cField('student', 'student', 'x_WargaNegara', 'WargaNegara', '`WargaNegara`', '`WargaNegara`', 202, -1, FALSE, '`WargaNegara`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->WargaNegara->Sortable = TRUE; // Allow sort
		$this->WargaNegara->OptionCount = 2;
		$this->fields['WargaNegara'] = &$this->WargaNegara;

		// Kelamin
		$this->Kelamin = new cField('student', 'student', 'x_Kelamin', 'Kelamin', '`Kelamin`', '`Kelamin`', 202, -1, FALSE, '`Kelamin`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->Kelamin->Sortable = TRUE; // Allow sort
		$this->fields['Kelamin'] = &$this->Kelamin;

		// TempatLahir
		$this->TempatLahir = new cField('student', 'student', 'x_TempatLahir', 'TempatLahir', '`TempatLahir`', '`TempatLahir`', 200, -1, FALSE, '`TempatLahir`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TempatLahir->Sortable = TRUE; // Allow sort
		$this->fields['TempatLahir'] = &$this->TempatLahir;

		// TanggalLahir
		$this->TanggalLahir = new cField('student', 'student', 'x_TanggalLahir', 'TanggalLahir', '`TanggalLahir`', ew_CastDateFieldForLike('`TanggalLahir`', 0, "DB"), 133, 0, FALSE, '`TanggalLahir`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TanggalLahir->Sortable = TRUE; // Allow sort
		$this->TanggalLahir->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['TanggalLahir'] = &$this->TanggalLahir;

		// AgamaID
		$this->AgamaID = new cField('student', 'student', 'x_AgamaID', 'AgamaID', '`AgamaID`', '`AgamaID`', 3, -1, FALSE, '`AgamaID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->AgamaID->Sortable = TRUE; // Allow sort
		$this->AgamaID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->AgamaID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->AgamaID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['AgamaID'] = &$this->AgamaID;

		// Darah
		$this->Darah = new cField('student', 'student', 'x_Darah', 'Darah', '`Darah`', '`Darah`', 202, -1, FALSE, '`Darah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->Darah->Sortable = TRUE; // Allow sort
		$this->fields['Darah'] = &$this->Darah;

		// StatusSipil
		$this->StatusSipil = new cField('student', 'student', 'x_StatusSipil', 'StatusSipil', '`StatusSipil`', '`StatusSipil`', 200, -1, FALSE, '`StatusSipil`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->StatusSipil->Sortable = TRUE; // Allow sort
		$this->StatusSipil->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->StatusSipil->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['StatusSipil'] = &$this->StatusSipil;

		// AlamatDomisili
		$this->AlamatDomisili = new cField('student', 'student', 'x_AlamatDomisili', 'AlamatDomisili', '`AlamatDomisili`', '`AlamatDomisili`', 200, -1, FALSE, '`AlamatDomisili`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXTAREA');
		$this->AlamatDomisili->Sortable = TRUE; // Allow sort
		$this->fields['AlamatDomisili'] = &$this->AlamatDomisili;

		// RT
		$this->RT = new cField('student', 'student', 'x_RT', 'RT', '`RT`', '`RT`', 200, -1, FALSE, '`RT`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->RT->Sortable = TRUE; // Allow sort
		$this->fields['RT'] = &$this->RT;

		// RW
		$this->RW = new cField('student', 'student', 'x_RW', 'RW', '`RW`', '`RW`', 200, -1, FALSE, '`RW`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->RW->Sortable = TRUE; // Allow sort
		$this->fields['RW'] = &$this->RW;

		// KodePos
		$this->KodePos = new cField('student', 'student', 'x_KodePos', 'KodePos', '`KodePos`', '`KodePos`', 200, -1, FALSE, '`KodePos`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->KodePos->Sortable = TRUE; // Allow sort
		$this->fields['KodePos'] = &$this->KodePos;

		// ProvinsiID
		$this->ProvinsiID = new cField('student', 'student', 'x_ProvinsiID', 'ProvinsiID', '`ProvinsiID`', '`ProvinsiID`', 200, -1, FALSE, '`ProvinsiID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->ProvinsiID->Sortable = TRUE; // Allow sort
		$this->ProvinsiID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->ProvinsiID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['ProvinsiID'] = &$this->ProvinsiID;

		// KabupatenKotaID
		$this->KabupatenKotaID = new cField('student', 'student', 'x_KabupatenKotaID', 'KabupatenKotaID', '`KabupatenKotaID`', '`KabupatenKotaID`', 200, -1, FALSE, '`KabupatenKotaID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KabupatenKotaID->Sortable = TRUE; // Allow sort
		$this->KabupatenKotaID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KabupatenKotaID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['KabupatenKotaID'] = &$this->KabupatenKotaID;

		// KecamatanID
		$this->KecamatanID = new cField('student', 'student', 'x_KecamatanID', 'KecamatanID', '`KecamatanID`', '`KecamatanID`', 200, -1, FALSE, '`KecamatanID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KecamatanID->Sortable = TRUE; // Allow sort
		$this->KecamatanID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KecamatanID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['KecamatanID'] = &$this->KecamatanID;

		// DesaID
		$this->DesaID = new cField('student', 'student', 'x_DesaID', 'DesaID', '`DesaID`', '`DesaID`', 200, -1, FALSE, '`DesaID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->DesaID->Sortable = TRUE; // Allow sort
		$this->DesaID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->DesaID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['DesaID'] = &$this->DesaID;

		// AnakKe
		$this->AnakKe = new cField('student', 'student', 'x_AnakKe', 'AnakKe', '`AnakKe`', '`AnakKe`', 3, -1, FALSE, '`AnakKe`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->AnakKe->Sortable = TRUE; // Allow sort
		$this->AnakKe->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['AnakKe'] = &$this->AnakKe;

		// JumlahSaudara
		$this->JumlahSaudara = new cField('student', 'student', 'x_JumlahSaudara', 'JumlahSaudara', '`JumlahSaudara`', '`JumlahSaudara`', 3, -1, FALSE, '`JumlahSaudara`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->JumlahSaudara->Sortable = TRUE; // Allow sort
		$this->JumlahSaudara->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['JumlahSaudara'] = &$this->JumlahSaudara;

		// Telepon
		$this->Telepon = new cField('student', 'student', 'x_Telepon', 'Telepon', '`Telepon`', '`Telepon`', 200, -1, FALSE, '`Telepon`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Telepon->Sortable = TRUE; // Allow sort
		$this->fields['Telepon'] = &$this->Telepon;

		// Handphone
		$this->Handphone = new cField('student', 'student', 'x_Handphone', 'Handphone', '`Handphone`', '`Handphone`', 200, -1, FALSE, '`Handphone`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Handphone->Sortable = TRUE; // Allow sort
		$this->fields['Handphone'] = &$this->Handphone;

		// Email
		$this->_Email = new cField('student', 'student', 'x__Email', 'Email', '`Email`', '`Email`', 200, -1, FALSE, '`Email`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->_Email->Sortable = TRUE; // Allow sort
		$this->fields['Email'] = &$this->_Email;

		// NamaAyah
		$this->NamaAyah = new cField('student', 'student', 'x_NamaAyah', 'NamaAyah', '`NamaAyah`', '`NamaAyah`', 200, -1, FALSE, '`NamaAyah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NamaAyah->Sortable = TRUE; // Allow sort
		$this->fields['NamaAyah'] = &$this->NamaAyah;

		// AgamaAyah
		$this->AgamaAyah = new cField('student', 'student', 'x_AgamaAyah', 'AgamaAyah', '`AgamaAyah`', '`AgamaAyah`', 3, -1, FALSE, '`AgamaAyah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->AgamaAyah->Sortable = TRUE; // Allow sort
		$this->AgamaAyah->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->AgamaAyah->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->AgamaAyah->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['AgamaAyah'] = &$this->AgamaAyah;

		// PendidikanAyah
		$this->PendidikanAyah = new cField('student', 'student', 'x_PendidikanAyah', 'PendidikanAyah', '`PendidikanAyah`', '`PendidikanAyah`', 200, -1, FALSE, '`PendidikanAyah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->PendidikanAyah->Sortable = TRUE; // Allow sort
		$this->PendidikanAyah->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->PendidikanAyah->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['PendidikanAyah'] = &$this->PendidikanAyah;

		// PekerjaanAyah
		$this->PekerjaanAyah = new cField('student', 'student', 'x_PekerjaanAyah', 'PekerjaanAyah', '`PekerjaanAyah`', '`PekerjaanAyah`', 200, -1, FALSE, '`PekerjaanAyah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->PekerjaanAyah->Sortable = TRUE; // Allow sort
		$this->PekerjaanAyah->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->PekerjaanAyah->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['PekerjaanAyah'] = &$this->PekerjaanAyah;

		// HidupAyah
		$this->HidupAyah = new cField('student', 'student', 'x_HidupAyah', 'HidupAyah', '`HidupAyah`', '`HidupAyah`', 200, -1, FALSE, '`HidupAyah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->HidupAyah->Sortable = TRUE; // Allow sort
		$this->HidupAyah->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->HidupAyah->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['HidupAyah'] = &$this->HidupAyah;

		// NamaIbu
		$this->NamaIbu = new cField('student', 'student', 'x_NamaIbu', 'NamaIbu', '`NamaIbu`', '`NamaIbu`', 200, -1, FALSE, '`NamaIbu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NamaIbu->Sortable = TRUE; // Allow sort
		$this->fields['NamaIbu'] = &$this->NamaIbu;

		// AgamaIbu
		$this->AgamaIbu = new cField('student', 'student', 'x_AgamaIbu', 'AgamaIbu', '`AgamaIbu`', '`AgamaIbu`', 3, -1, FALSE, '`AgamaIbu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->AgamaIbu->Sortable = TRUE; // Allow sort
		$this->AgamaIbu->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->AgamaIbu->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->AgamaIbu->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['AgamaIbu'] = &$this->AgamaIbu;

		// PendidikanIbu
		$this->PendidikanIbu = new cField('student', 'student', 'x_PendidikanIbu', 'PendidikanIbu', '`PendidikanIbu`', '`PendidikanIbu`', 200, -1, FALSE, '`PendidikanIbu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->PendidikanIbu->Sortable = TRUE; // Allow sort
		$this->PendidikanIbu->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->PendidikanIbu->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['PendidikanIbu'] = &$this->PendidikanIbu;

		// PekerjaanIbu
		$this->PekerjaanIbu = new cField('student', 'student', 'x_PekerjaanIbu', 'PekerjaanIbu', '`PekerjaanIbu`', '`PekerjaanIbu`', 200, -1, FALSE, '`PekerjaanIbu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->PekerjaanIbu->Sortable = TRUE; // Allow sort
		$this->PekerjaanIbu->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->PekerjaanIbu->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['PekerjaanIbu'] = &$this->PekerjaanIbu;

		// HidupIbu
		$this->HidupIbu = new cField('student', 'student', 'x_HidupIbu', 'HidupIbu', '`HidupIbu`', '`HidupIbu`', 200, -1, FALSE, '`HidupIbu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->HidupIbu->Sortable = TRUE; // Allow sort
		$this->HidupIbu->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->HidupIbu->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['HidupIbu'] = &$this->HidupIbu;

		// AlamatOrtu
		$this->AlamatOrtu = new cField('student', 'student', 'x_AlamatOrtu', 'AlamatOrtu', '`AlamatOrtu`', '`AlamatOrtu`', 200, -1, FALSE, '`AlamatOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXTAREA');
		$this->AlamatOrtu->Sortable = TRUE; // Allow sort
		$this->fields['AlamatOrtu'] = &$this->AlamatOrtu;

		// RTOrtu
		$this->RTOrtu = new cField('student', 'student', 'x_RTOrtu', 'RTOrtu', '`RTOrtu`', '`RTOrtu`', 200, -1, FALSE, '`RTOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->RTOrtu->Sortable = TRUE; // Allow sort
		$this->fields['RTOrtu'] = &$this->RTOrtu;

		// RWOrtu
		$this->RWOrtu = new cField('student', 'student', 'x_RWOrtu', 'RWOrtu', '`RWOrtu`', '`RWOrtu`', 200, -1, FALSE, '`RWOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->RWOrtu->Sortable = TRUE; // Allow sort
		$this->fields['RWOrtu'] = &$this->RWOrtu;

		// KodePosOrtu
		$this->KodePosOrtu = new cField('student', 'student', 'x_KodePosOrtu', 'KodePosOrtu', '`KodePosOrtu`', '`KodePosOrtu`', 200, -1, FALSE, '`KodePosOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->KodePosOrtu->Sortable = TRUE; // Allow sort
		$this->fields['KodePosOrtu'] = &$this->KodePosOrtu;

		// ProvinsiIDOrtu
		$this->ProvinsiIDOrtu = new cField('student', 'student', 'x_ProvinsiIDOrtu', 'ProvinsiIDOrtu', '`ProvinsiIDOrtu`', '`ProvinsiIDOrtu`', 200, -1, FALSE, '`ProvinsiIDOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->ProvinsiIDOrtu->Sortable = TRUE; // Allow sort
		$this->ProvinsiIDOrtu->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->ProvinsiIDOrtu->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['ProvinsiIDOrtu'] = &$this->ProvinsiIDOrtu;

		// KabupatenIDOrtu
		$this->KabupatenIDOrtu = new cField('student', 'student', 'x_KabupatenIDOrtu', 'KabupatenIDOrtu', '`KabupatenIDOrtu`', '`KabupatenIDOrtu`', 200, -1, FALSE, '`KabupatenIDOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KabupatenIDOrtu->Sortable = TRUE; // Allow sort
		$this->KabupatenIDOrtu->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KabupatenIDOrtu->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['KabupatenIDOrtu'] = &$this->KabupatenIDOrtu;

		// KecamatanIDOrtu
		$this->KecamatanIDOrtu = new cField('student', 'student', 'x_KecamatanIDOrtu', 'KecamatanIDOrtu', '`KecamatanIDOrtu`', '`KecamatanIDOrtu`', 200, -1, FALSE, '`KecamatanIDOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KecamatanIDOrtu->Sortable = TRUE; // Allow sort
		$this->KecamatanIDOrtu->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KecamatanIDOrtu->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['KecamatanIDOrtu'] = &$this->KecamatanIDOrtu;

		// DesaIDOrtu
		$this->DesaIDOrtu = new cField('student', 'student', 'x_DesaIDOrtu', 'DesaIDOrtu', '`DesaIDOrtu`', '`DesaIDOrtu`', 200, -1, FALSE, '`DesaIDOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->DesaIDOrtu->Sortable = TRUE; // Allow sort
		$this->DesaIDOrtu->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->DesaIDOrtu->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['DesaIDOrtu'] = &$this->DesaIDOrtu;

		// NegaraIDOrtu
		$this->NegaraIDOrtu = new cField('student', 'student', 'x_NegaraIDOrtu', 'NegaraIDOrtu', '`NegaraIDOrtu`', '`NegaraIDOrtu`', 200, -1, FALSE, '`NegaraIDOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->NegaraIDOrtu->Sortable = TRUE; // Allow sort
		$this->NegaraIDOrtu->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->NegaraIDOrtu->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['NegaraIDOrtu'] = &$this->NegaraIDOrtu;

		// TeleponOrtu
		$this->TeleponOrtu = new cField('student', 'student', 'x_TeleponOrtu', 'TeleponOrtu', '`TeleponOrtu`', '`TeleponOrtu`', 200, -1, FALSE, '`TeleponOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TeleponOrtu->Sortable = TRUE; // Allow sort
		$this->fields['TeleponOrtu'] = &$this->TeleponOrtu;

		// HandphoneOrtu
		$this->HandphoneOrtu = new cField('student', 'student', 'x_HandphoneOrtu', 'HandphoneOrtu', '`HandphoneOrtu`', '`HandphoneOrtu`', 200, -1, FALSE, '`HandphoneOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->HandphoneOrtu->Sortable = TRUE; // Allow sort
		$this->fields['HandphoneOrtu'] = &$this->HandphoneOrtu;

		// EmailOrtu
		$this->EmailOrtu = new cField('student', 'student', 'x_EmailOrtu', 'EmailOrtu', '`EmailOrtu`', '`EmailOrtu`', 200, -1, FALSE, '`EmailOrtu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->EmailOrtu->Sortable = TRUE; // Allow sort
		$this->EmailOrtu->FldDefaultErrMsg = $Language->Phrase("IncorrectEmail");
		$this->fields['EmailOrtu'] = &$this->EmailOrtu;

		// AsalSekolah
		$this->AsalSekolah = new cField('student', 'student', 'x_AsalSekolah', 'AsalSekolah', '`AsalSekolah`', '`AsalSekolah`', 200, -1, FALSE, '`AsalSekolah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->AsalSekolah->Sortable = TRUE; // Allow sort
		$this->fields['AsalSekolah'] = &$this->AsalSekolah;

		// AlamatSekolah
		$this->AlamatSekolah = new cField('student', 'student', 'x_AlamatSekolah', 'AlamatSekolah', '`AlamatSekolah`', '`AlamatSekolah`', 200, -1, FALSE, '`AlamatSekolah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXTAREA');
		$this->AlamatSekolah->Sortable = TRUE; // Allow sort
		$this->fields['AlamatSekolah'] = &$this->AlamatSekolah;

		// ProvinsiIDSekolah
		$this->ProvinsiIDSekolah = new cField('student', 'student', 'x_ProvinsiIDSekolah', 'ProvinsiIDSekolah', '`ProvinsiIDSekolah`', '`ProvinsiIDSekolah`', 200, -1, FALSE, '`ProvinsiIDSekolah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->ProvinsiIDSekolah->Sortable = TRUE; // Allow sort
		$this->ProvinsiIDSekolah->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->ProvinsiIDSekolah->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['ProvinsiIDSekolah'] = &$this->ProvinsiIDSekolah;

		// KabupatenIDSekolah
		$this->KabupatenIDSekolah = new cField('student', 'student', 'x_KabupatenIDSekolah', 'KabupatenIDSekolah', '`KabupatenIDSekolah`', '`KabupatenIDSekolah`', 200, -1, FALSE, '`KabupatenIDSekolah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KabupatenIDSekolah->Sortable = TRUE; // Allow sort
		$this->KabupatenIDSekolah->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KabupatenIDSekolah->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['KabupatenIDSekolah'] = &$this->KabupatenIDSekolah;

		// KecamatanIDSekolah
		$this->KecamatanIDSekolah = new cField('student', 'student', 'x_KecamatanIDSekolah', 'KecamatanIDSekolah', '`KecamatanIDSekolah`', '`KecamatanIDSekolah`', 200, -1, FALSE, '`KecamatanIDSekolah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KecamatanIDSekolah->Sortable = TRUE; // Allow sort
		$this->KecamatanIDSekolah->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KecamatanIDSekolah->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['KecamatanIDSekolah'] = &$this->KecamatanIDSekolah;

		// DesaIDSekolah
		$this->DesaIDSekolah = new cField('student', 'student', 'x_DesaIDSekolah', 'DesaIDSekolah', '`DesaIDSekolah`', '`DesaIDSekolah`', 200, -1, FALSE, '`DesaIDSekolah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->DesaIDSekolah->Sortable = TRUE; // Allow sort
		$this->DesaIDSekolah->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->DesaIDSekolah->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['DesaIDSekolah'] = &$this->DesaIDSekolah;

		// NilaiSekolah
		$this->NilaiSekolah = new cField('student', 'student', 'x_NilaiSekolah', 'NilaiSekolah', '`NilaiSekolah`', '`NilaiSekolah`', 200, -1, FALSE, '`NilaiSekolah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NilaiSekolah->Sortable = TRUE; // Allow sort
		$this->NilaiSekolah->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['NilaiSekolah'] = &$this->NilaiSekolah;

		// TahunLulus
		$this->TahunLulus = new cField('student', 'student', 'x_TahunLulus', 'TahunLulus', '`TahunLulus`', '`TahunLulus`', 200, -1, FALSE, '`TahunLulus`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TahunLulus->Sortable = TRUE; // Allow sort
		$this->TahunLulus->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['TahunLulus'] = &$this->TahunLulus;

		// IjazahSekolah
		$this->IjazahSekolah = new cField('student', 'student', 'x_IjazahSekolah', 'IjazahSekolah', '`IjazahSekolah`', '`IjazahSekolah`', 200, -1, FALSE, '`IjazahSekolah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->IjazahSekolah->Sortable = TRUE; // Allow sort
		$this->fields['IjazahSekolah'] = &$this->IjazahSekolah;

		// TglIjazah
		$this->TglIjazah = new cField('student', 'student', 'x_TglIjazah', 'TglIjazah', '`TglIjazah`', ew_CastDateFieldForLike('`TglIjazah`', 0, "DB"), 133, 0, FALSE, '`TglIjazah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TglIjazah->Sortable = TRUE; // Allow sort
		$this->TglIjazah->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['TglIjazah'] = &$this->TglIjazah;

		// Creator
		$this->Creator = new cField('student', 'student', 'x_Creator', 'Creator', '`Creator`', '`Creator`', 200, -1, FALSE, '`Creator`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Creator->Sortable = TRUE; // Allow sort
		$this->fields['Creator'] = &$this->Creator;

		// CreateDate
		$this->CreateDate = new cField('student', 'student', 'x_CreateDate', 'CreateDate', '`CreateDate`', ew_CastDateFieldForLike('`CreateDate`', 0, "DB"), 135, 0, FALSE, '`CreateDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->CreateDate->Sortable = TRUE; // Allow sort
		$this->CreateDate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['CreateDate'] = &$this->CreateDate;

		// Editor
		$this->Editor = new cField('student', 'student', 'x_Editor', 'Editor', '`Editor`', '`Editor`', 200, -1, FALSE, '`Editor`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Editor->Sortable = TRUE; // Allow sort
		$this->fields['Editor'] = &$this->Editor;

		// EditDate
		$this->EditDate = new cField('student', 'student', 'x_EditDate', 'EditDate', '`EditDate`', ew_CastDateFieldForLike('`EditDate`', 0, "DB"), 135, 0, FALSE, '`EditDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->EditDate->Sortable = TRUE; // Allow sort
		$this->EditDate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['EditDate'] = &$this->EditDate;

		// LockStatus
		$this->LockStatus = new cField('student', 'student', 'x_LockStatus', 'LockStatus', '`LockStatus`', '`LockStatus`', 202, -1, FALSE, '`LockStatus`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->LockStatus->Sortable = TRUE; // Allow sort
		$this->LockStatus->FldDataType = EW_DATATYPE_BOOLEAN;
		$this->LockStatus->OptionCount = 2;
		$this->fields['LockStatus'] = &$this->LockStatus;

		// LockDate
		$this->LockDate = new cField('student', 'student', 'x_LockDate', 'LockDate', '`LockDate`', ew_CastDateFieldForLike('`LockDate`', 0, "DB"), 135, 0, FALSE, '`LockDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->LockDate->Sortable = TRUE; // Allow sort
		$this->LockDate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['LockDate'] = &$this->LockDate;

		// VerifiedBy
		$this->VerifiedBy = new cField('student', 'student', 'x_VerifiedBy', 'VerifiedBy', '`VerifiedBy`', '`VerifiedBy`', 200, -1, FALSE, '`VerifiedBy`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->VerifiedBy->Sortable = TRUE; // Allow sort
		$this->fields['VerifiedBy'] = &$this->VerifiedBy;

		// NA
		$this->NA = new cField('student', 'student', 'x_NA', 'NA', '`NA`', '`NA`', 202, -1, FALSE, '`NA`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->NA->Sortable = TRUE; // Allow sort
		$this->NA->FldDataType = EW_DATATYPE_BOOLEAN;
		$this->NA->TrueValue = 'Y';
		$this->NA->FalseValue = 'N';
		$this->NA->OptionCount = 2;
		$this->fields['NA'] = &$this->NA;
	}

	// Set Field Visibility
	function SetFieldVisibility($fldparm) {
		global $Security;
		return $this->$fldparm->Visible; // Returns original value
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Table level SQL
	var $_SqlFrom = "";

	function getSqlFrom() { // From
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`student`";
	}

	function SqlFrom() { // For backward compatibility
		return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
		$this->_SqlFrom = $v;
	}
	var $_SqlSelect = "";

	function getSqlSelect() { // Select
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}
	var $_SqlWhere = "";

	function getSqlWhere() { // Where
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
		return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
		$this->_SqlWhere = $v;
	}
	var $_SqlGroupBy = "";

	function getSqlGroupBy() { // Group By
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
		return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
		$this->_SqlGroupBy = $v;
	}
	var $_SqlHaving = "";

	function getSqlHaving() { // Having
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
		return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
		$this->_SqlHaving = $v;
	}
	var $_SqlOrderBy = "";

	function getSqlOrderBy() { // Order By
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "";
	}

	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$this->Recordset_Selecting($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(),
			$this->getSqlHaving(), $this->getSqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->getSqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		$cnt = -1;
		if (($this->TableType == 'TABLE' || $this->TableType == 'VIEW' || $this->TableType == 'LINKTABLE') && preg_match("/^SELECT \* FROM/i", $sSql)) {
			$sSql = "SELECT COUNT(*) FROM" . preg_replace('/^SELECT\s([\s\S]+)?\*\sFROM/i', "", $sSql);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		$conn = &$this->Connection();
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			$conn = &$this->Connection();
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// INSERT statement
	function InsertSQL(&$rs) {
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		$conn = &$this->Connection();
		$bInsert = $conn->Execute($this->InsertSQL($rs));
		if ($bInsert) {
			if ($this->AuditTrailOnAdd)
				$this->WriteAuditTrailOnAdd($rs);
		}
		return $bInsert;
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL, $curfilter = TRUE) {
		$conn = &$this->Connection();
		$bUpdate = $conn->Execute($this->UpdateSQL($rs, $where, $curfilter));
		if ($bUpdate && $this->AuditTrailOnEdit) {
			$rsaudit = $rs;
			$fldname = 'StudentID';
			if (!array_key_exists($fldname, $rsaudit)) $rsaudit[$fldname] = $rsold[$fldname];
			$this->WriteAuditTrailOnEdit($rsold, $rsaudit);
		}
		return $bUpdate;
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		if ($rs) {
			if (array_key_exists('StudentID', $rs))
				ew_AddFilter($where, ew_QuotedName('StudentID', $this->DBID) . '=' . ew_QuotedValue($rs['StudentID'], $this->StudentID->FldDataType, $this->DBID));
		}
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "", $curfilter = TRUE) {
		$conn = &$this->Connection();
		$bDelete = $conn->Execute($this->DeleteSQL($rs, $where, $curfilter));
		if ($bDelete && $this->AuditTrailOnDelete)
			$this->WriteAuditTrailOnDelete($rs);
		return $bDelete;
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`StudentID` = '@StudentID@'";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		$sKeyFilter = str_replace("@StudentID@", ew_AdjustSql($this->StudentID->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "studentlist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "studentlist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("studentview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("studentview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "studentadd.php?" . $this->UrlParm($parm);
		else
			$url = "studentadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("studentedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("studentadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("studentdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "StudentID:" . ew_VarToJson($this->StudentID->CurrentValue, "string", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->StudentID->CurrentValue)) {
			$sUrl .= "StudentID=" . urlencode($this->StudentID->CurrentValue);
		} else {
			return "javascript:ew_Alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort());
			return $this->AddMasterUrl(ew_CurrentPage() . "?" . $sUrlParm);
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (!empty($_GET) || !empty($_POST)) {
			$isPost = ew_IsHttpPost();
			if ($isPost && isset($_POST["StudentID"]))
				$arKeys[] = ew_StripSlashes($_POST["StudentID"]);
			elseif (isset($_GET["StudentID"]))
				$arKeys[] = ew_StripSlashes($_GET["StudentID"]);
			else
				$arKeys = NULL; // Do not setup

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		if (is_array($arKeys)) {
			foreach ($arKeys as $key) {
				$ar[] = $key;
			}
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->StudentID->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$conn = &$this->Connection();
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
		$this->StudentID->setDbValue($rs->fields('StudentID'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->LevelID->setDbValue($rs->fields('LevelID'));
		$this->Password->setDbValue($rs->fields('Password'));
		$this->KampusID->setDbValue($rs->fields('KampusID'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->StudentStatusID->setDbValue($rs->fields('StudentStatusID'));
		$this->TahunID->setDbValue($rs->fields('TahunID'));
		$this->Foto->Upload->DbValue = $rs->fields('Foto');
		$this->NIK->setDbValue($rs->fields('NIK'));
		$this->WargaNegara->setDbValue($rs->fields('WargaNegara'));
		$this->Kelamin->setDbValue($rs->fields('Kelamin'));
		$this->TempatLahir->setDbValue($rs->fields('TempatLahir'));
		$this->TanggalLahir->setDbValue($rs->fields('TanggalLahir'));
		$this->AgamaID->setDbValue($rs->fields('AgamaID'));
		$this->Darah->setDbValue($rs->fields('Darah'));
		$this->StatusSipil->setDbValue($rs->fields('StatusSipil'));
		$this->AlamatDomisili->setDbValue($rs->fields('AlamatDomisili'));
		$this->RT->setDbValue($rs->fields('RT'));
		$this->RW->setDbValue($rs->fields('RW'));
		$this->KodePos->setDbValue($rs->fields('KodePos'));
		$this->ProvinsiID->setDbValue($rs->fields('ProvinsiID'));
		$this->KabupatenKotaID->setDbValue($rs->fields('KabupatenKotaID'));
		$this->KecamatanID->setDbValue($rs->fields('KecamatanID'));
		$this->DesaID->setDbValue($rs->fields('DesaID'));
		$this->AnakKe->setDbValue($rs->fields('AnakKe'));
		$this->JumlahSaudara->setDbValue($rs->fields('JumlahSaudara'));
		$this->Telepon->setDbValue($rs->fields('Telepon'));
		$this->Handphone->setDbValue($rs->fields('Handphone'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->NamaAyah->setDbValue($rs->fields('NamaAyah'));
		$this->AgamaAyah->setDbValue($rs->fields('AgamaAyah'));
		$this->PendidikanAyah->setDbValue($rs->fields('PendidikanAyah'));
		$this->PekerjaanAyah->setDbValue($rs->fields('PekerjaanAyah'));
		$this->HidupAyah->setDbValue($rs->fields('HidupAyah'));
		$this->NamaIbu->setDbValue($rs->fields('NamaIbu'));
		$this->AgamaIbu->setDbValue($rs->fields('AgamaIbu'));
		$this->PendidikanIbu->setDbValue($rs->fields('PendidikanIbu'));
		$this->PekerjaanIbu->setDbValue($rs->fields('PekerjaanIbu'));
		$this->HidupIbu->setDbValue($rs->fields('HidupIbu'));
		$this->AlamatOrtu->setDbValue($rs->fields('AlamatOrtu'));
		$this->RTOrtu->setDbValue($rs->fields('RTOrtu'));
		$this->RWOrtu->setDbValue($rs->fields('RWOrtu'));
		$this->KodePosOrtu->setDbValue($rs->fields('KodePosOrtu'));
		$this->ProvinsiIDOrtu->setDbValue($rs->fields('ProvinsiIDOrtu'));
		$this->KabupatenIDOrtu->setDbValue($rs->fields('KabupatenIDOrtu'));
		$this->KecamatanIDOrtu->setDbValue($rs->fields('KecamatanIDOrtu'));
		$this->DesaIDOrtu->setDbValue($rs->fields('DesaIDOrtu'));
		$this->NegaraIDOrtu->setDbValue($rs->fields('NegaraIDOrtu'));
		$this->TeleponOrtu->setDbValue($rs->fields('TeleponOrtu'));
		$this->HandphoneOrtu->setDbValue($rs->fields('HandphoneOrtu'));
		$this->EmailOrtu->setDbValue($rs->fields('EmailOrtu'));
		$this->AsalSekolah->setDbValue($rs->fields('AsalSekolah'));
		$this->AlamatSekolah->setDbValue($rs->fields('AlamatSekolah'));
		$this->ProvinsiIDSekolah->setDbValue($rs->fields('ProvinsiIDSekolah'));
		$this->KabupatenIDSekolah->setDbValue($rs->fields('KabupatenIDSekolah'));
		$this->KecamatanIDSekolah->setDbValue($rs->fields('KecamatanIDSekolah'));
		$this->DesaIDSekolah->setDbValue($rs->fields('DesaIDSekolah'));
		$this->NilaiSekolah->setDbValue($rs->fields('NilaiSekolah'));
		$this->TahunLulus->setDbValue($rs->fields('TahunLulus'));
		$this->IjazahSekolah->setDbValue($rs->fields('IjazahSekolah'));
		$this->TglIjazah->setDbValue($rs->fields('TglIjazah'));
		$this->Creator->setDbValue($rs->fields('Creator'));
		$this->CreateDate->setDbValue($rs->fields('CreateDate'));
		$this->Editor->setDbValue($rs->fields('Editor'));
		$this->EditDate->setDbValue($rs->fields('EditDate'));
		$this->LockStatus->setDbValue($rs->fields('LockStatus'));
		$this->LockDate->setDbValue($rs->fields('LockDate'));
		$this->VerifiedBy->setDbValue($rs->fields('VerifiedBy'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// StudentID
		// Nama
		// LevelID
		// Password
		// KampusID
		// ProdiID
		// StudentStatusID
		// TahunID
		// Foto
		// NIK
		// WargaNegara
		// Kelamin
		// TempatLahir
		// TanggalLahir
		// AgamaID
		// Darah
		// StatusSipil
		// AlamatDomisili
		// RT
		// RW
		// KodePos
		// ProvinsiID
		// KabupatenKotaID
		// KecamatanID
		// DesaID
		// AnakKe
		// JumlahSaudara
		// Telepon
		// Handphone
		// Email
		// NamaAyah
		// AgamaAyah
		// PendidikanAyah
		// PekerjaanAyah
		// HidupAyah
		// NamaIbu
		// AgamaIbu
		// PendidikanIbu
		// PekerjaanIbu
		// HidupIbu
		// AlamatOrtu
		// RTOrtu
		// RWOrtu
		// KodePosOrtu
		// ProvinsiIDOrtu
		// KabupatenIDOrtu
		// KecamatanIDOrtu
		// DesaIDOrtu
		// NegaraIDOrtu
		// TeleponOrtu
		// HandphoneOrtu
		// EmailOrtu
		// AsalSekolah
		// AlamatSekolah
		// ProvinsiIDSekolah
		// KabupatenIDSekolah
		// KecamatanIDSekolah
		// DesaIDSekolah
		// NilaiSekolah
		// TahunLulus
		// IjazahSekolah
		// TglIjazah
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// LockStatus
		// LockDate
		// VerifiedBy
		// NA
		// StudentID

		$this->StudentID->ViewValue = $this->StudentID->CurrentValue;
		$this->StudentID->CssStyle = "font-weight: bold;";
		$this->StudentID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->CssStyle = "font-weight: bold;";
		$this->Nama->ViewCustomAttributes = "";

		// LevelID
		$this->LevelID->ViewValue = $this->LevelID->CurrentValue;
		$this->LevelID->ViewCustomAttributes = "";

		// Password
		$this->Password->ViewValue = $Language->Phrase("PasswordMask");
		$this->Password->ViewCustomAttributes = "";

		// KampusID
		if (strval($this->KampusID->CurrentValue) <> "") {
			$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->KampusID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
		$sWhereWrk = "";
		$this->KampusID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KampusID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KampusID->ViewValue = $this->KampusID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KampusID->ViewValue = $this->KampusID->CurrentValue;
			}
		} else {
			$this->KampusID->ViewValue = NULL;
		}
		$this->KampusID->ViewCustomAttributes = "";

		// ProdiID
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->ProdiID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->ProdiID->ViewValue = $this->ProdiID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
			}
		} else {
			$this->ProdiID->ViewValue = NULL;
		}
		$this->ProdiID->ViewCustomAttributes = "";

		// StudentStatusID
		if (strval($this->StudentStatusID->CurrentValue) <> "") {
			$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StudentStatusID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statusstudent`";
		$sWhereWrk = "";
		$this->StudentStatusID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StudentStatusID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StudentStatusID->ViewValue = $this->StudentStatusID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StudentStatusID->ViewValue = $this->StudentStatusID->CurrentValue;
			}
		} else {
			$this->StudentStatusID->ViewValue = NULL;
		}
		$this->StudentStatusID->ViewCustomAttributes = "";

		// TahunID
		$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
		$this->TahunID->ViewCustomAttributes = "";

		// Foto
		$this->Foto->UploadPath = "upload";
		if (!ew_Empty($this->Foto->Upload->DbValue)) {
			$this->Foto->ImageAlt = $this->Foto->FldAlt();
			$this->Foto->ViewValue = $this->Foto->Upload->DbValue;
		} else {
			$this->Foto->ViewValue = "";
		}
		$this->Foto->ViewCustomAttributes = "";

		// NIK
		$this->NIK->ViewValue = $this->NIK->CurrentValue;
		$this->NIK->ViewCustomAttributes = "";

		// WargaNegara
		if (strval($this->WargaNegara->CurrentValue) <> "") {
			$this->WargaNegara->ViewValue = $this->WargaNegara->OptionCaption($this->WargaNegara->CurrentValue);
		} else {
			$this->WargaNegara->ViewValue = NULL;
		}
		$this->WargaNegara->ViewCustomAttributes = "";

		// Kelamin
		if (strval($this->Kelamin->CurrentValue) <> "") {
			$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->Kelamin->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
		$sWhereWrk = "";
		$this->Kelamin->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Kelamin, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Kelamin->ViewValue = $this->Kelamin->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Kelamin->ViewValue = $this->Kelamin->CurrentValue;
			}
		} else {
			$this->Kelamin->ViewValue = NULL;
		}
		$this->Kelamin->ViewCustomAttributes = "";

		// TempatLahir
		$this->TempatLahir->ViewValue = $this->TempatLahir->CurrentValue;
		$this->TempatLahir->ViewCustomAttributes = "";

		// TanggalLahir
		$this->TanggalLahir->ViewValue = $this->TanggalLahir->CurrentValue;
		$this->TanggalLahir->ViewValue = ew_FormatDateTime($this->TanggalLahir->ViewValue, 0);
		$this->TanggalLahir->ViewCustomAttributes = "";

		// AgamaID
		if (strval($this->AgamaID->CurrentValue) <> "") {
			$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
		$sWhereWrk = "";
		$this->AgamaID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->AgamaID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->AgamaID->ViewValue = $this->AgamaID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->AgamaID->ViewValue = $this->AgamaID->CurrentValue;
			}
		} else {
			$this->AgamaID->ViewValue = NULL;
		}
		$this->AgamaID->ViewCustomAttributes = "";

		// Darah
		if (strval($this->Darah->CurrentValue) <> "") {
			$sFilterWrk = "`DarahID`" . ew_SearchString("=", $this->Darah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `DarahID`, `DarahID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_darah`";
		$sWhereWrk = "";
		$this->Darah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Darah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Darah->ViewValue = $this->Darah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Darah->ViewValue = $this->Darah->CurrentValue;
			}
		} else {
			$this->Darah->ViewValue = NULL;
		}
		$this->Darah->ViewCustomAttributes = "";

		// StatusSipil
		if (strval($this->StatusSipil->CurrentValue) <> "") {
			$sFilterWrk = "`StatusSipil`" . ew_SearchString("=", $this->StatusSipil->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusSipil`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statussipil`";
		$sWhereWrk = "";
		$this->StatusSipil->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StatusSipil, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StatusSipil->ViewValue = $this->StatusSipil->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StatusSipil->ViewValue = $this->StatusSipil->CurrentValue;
			}
		} else {
			$this->StatusSipil->ViewValue = NULL;
		}
		$this->StatusSipil->ViewCustomAttributes = "";

		// AlamatDomisili
		$this->AlamatDomisili->ViewValue = $this->AlamatDomisili->CurrentValue;
		$this->AlamatDomisili->ViewCustomAttributes = "";

		// RT
		$this->RT->ViewValue = $this->RT->CurrentValue;
		$this->RT->ViewCustomAttributes = "";

		// RW
		$this->RW->ViewValue = $this->RW->CurrentValue;
		$this->RW->ViewCustomAttributes = "";

		// KodePos
		$this->KodePos->ViewValue = $this->KodePos->CurrentValue;
		$this->KodePos->ViewCustomAttributes = "";

		// ProvinsiID
		if (strval($this->ProvinsiID->CurrentValue) <> "") {
			$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
		$sWhereWrk = "";
		$this->ProvinsiID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProvinsiID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->ProvinsiID->ViewValue = $this->ProvinsiID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->ProvinsiID->ViewValue = $this->ProvinsiID->CurrentValue;
			}
		} else {
			$this->ProvinsiID->ViewValue = NULL;
		}
		$this->ProvinsiID->ViewCustomAttributes = "";

		// KabupatenKotaID
		if (strval($this->KabupatenKotaID->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenKotaID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->KabupatenKotaID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KabupatenKotaID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KabupatenKotaID->ViewValue = $this->KabupatenKotaID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KabupatenKotaID->ViewValue = $this->KabupatenKotaID->CurrentValue;
			}
		} else {
			$this->KabupatenKotaID->ViewValue = NULL;
		}
		$this->KabupatenKotaID->ViewCustomAttributes = "";

		// KecamatanID
		if (strval($this->KecamatanID->CurrentValue) <> "") {
			$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
		$sWhereWrk = "";
		$this->KecamatanID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KecamatanID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KecamatanID->ViewValue = $this->KecamatanID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KecamatanID->ViewValue = $this->KecamatanID->CurrentValue;
			}
		} else {
			$this->KecamatanID->ViewValue = NULL;
		}
		$this->KecamatanID->ViewCustomAttributes = "";

		// DesaID
		if (strval($this->DesaID->CurrentValue) <> "") {
			$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
		$sWhereWrk = "";
		$this->DesaID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->DesaID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->DesaID->ViewValue = $this->DesaID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->DesaID->ViewValue = $this->DesaID->CurrentValue;
			}
		} else {
			$this->DesaID->ViewValue = NULL;
		}
		$this->DesaID->ViewCustomAttributes = "";

		// AnakKe
		$this->AnakKe->ViewValue = $this->AnakKe->CurrentValue;
		$this->AnakKe->ViewCustomAttributes = "";

		// JumlahSaudara
		$this->JumlahSaudara->ViewValue = $this->JumlahSaudara->CurrentValue;
		$this->JumlahSaudara->ViewCustomAttributes = "";

		// Telepon
		$this->Telepon->ViewValue = $this->Telepon->CurrentValue;
		$this->Telepon->ViewCustomAttributes = "";

		// Handphone
		$this->Handphone->ViewValue = $this->Handphone->CurrentValue;
		$this->Handphone->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// NamaAyah
		$this->NamaAyah->ViewValue = $this->NamaAyah->CurrentValue;
		$this->NamaAyah->ViewCustomAttributes = "";

		// AgamaAyah
		if (strval($this->AgamaAyah->CurrentValue) <> "") {
			$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaAyah->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
		$sWhereWrk = "";
		$this->AgamaAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->AgamaAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->AgamaAyah->ViewValue = $this->AgamaAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->AgamaAyah->ViewValue = $this->AgamaAyah->CurrentValue;
			}
		} else {
			$this->AgamaAyah->ViewValue = NULL;
		}
		$this->AgamaAyah->ViewCustomAttributes = "";

		// PendidikanAyah
		if (strval($this->PendidikanAyah->CurrentValue) <> "") {
			$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanAyah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
		$sWhereWrk = "";
		$this->PendidikanAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PendidikanAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PendidikanAyah->ViewValue = $this->PendidikanAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PendidikanAyah->ViewValue = $this->PendidikanAyah->CurrentValue;
			}
		} else {
			$this->PendidikanAyah->ViewValue = NULL;
		}
		$this->PendidikanAyah->ViewCustomAttributes = "";

		// PekerjaanAyah
		if (strval($this->PekerjaanAyah->CurrentValue) <> "") {
			$sFilterWrk = "`Pekerjaan`" . ew_SearchString("=", $this->PekerjaanAyah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pekerjaan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pekerjaanortu`";
		$sWhereWrk = "";
		$this->PekerjaanAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PekerjaanAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PekerjaanAyah->ViewValue = $this->PekerjaanAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PekerjaanAyah->ViewValue = $this->PekerjaanAyah->CurrentValue;
			}
		} else {
			$this->PekerjaanAyah->ViewValue = NULL;
		}
		$this->PekerjaanAyah->ViewCustomAttributes = "";

		// HidupAyah
		if (strval($this->HidupAyah->CurrentValue) <> "") {
			$sFilterWrk = "`Hidup`" . ew_SearchString("=", $this->HidupAyah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Hidup`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hidup`";
		$sWhereWrk = "";
		$this->HidupAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->HidupAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->HidupAyah->ViewValue = $this->HidupAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->HidupAyah->ViewValue = $this->HidupAyah->CurrentValue;
			}
		} else {
			$this->HidupAyah->ViewValue = NULL;
		}
		$this->HidupAyah->ViewCustomAttributes = "";

		// NamaIbu
		$this->NamaIbu->ViewValue = $this->NamaIbu->CurrentValue;
		$this->NamaIbu->ViewCustomAttributes = "";

		// AgamaIbu
		if (strval($this->AgamaIbu->CurrentValue) <> "") {
			$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaIbu->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
		$sWhereWrk = "";
		$this->AgamaIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->AgamaIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->AgamaIbu->ViewValue = $this->AgamaIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->AgamaIbu->ViewValue = $this->AgamaIbu->CurrentValue;
			}
		} else {
			$this->AgamaIbu->ViewValue = NULL;
		}
		$this->AgamaIbu->ViewCustomAttributes = "";

		// PendidikanIbu
		if (strval($this->PendidikanIbu->CurrentValue) <> "") {
			$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanIbu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
		$sWhereWrk = "";
		$this->PendidikanIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PendidikanIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PendidikanIbu->ViewValue = $this->PendidikanIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PendidikanIbu->ViewValue = $this->PendidikanIbu->CurrentValue;
			}
		} else {
			$this->PendidikanIbu->ViewValue = NULL;
		}
		$this->PendidikanIbu->ViewCustomAttributes = "";

		// PekerjaanIbu
		if (strval($this->PekerjaanIbu->CurrentValue) <> "") {
			$sFilterWrk = "`Pekerjaan`" . ew_SearchString("=", $this->PekerjaanIbu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pekerjaan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pekerjaanortu`";
		$sWhereWrk = "";
		$this->PekerjaanIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PekerjaanIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PekerjaanIbu->ViewValue = $this->PekerjaanIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PekerjaanIbu->ViewValue = $this->PekerjaanIbu->CurrentValue;
			}
		} else {
			$this->PekerjaanIbu->ViewValue = NULL;
		}
		$this->PekerjaanIbu->ViewCustomAttributes = "";

		// HidupIbu
		if (strval($this->HidupIbu->CurrentValue) <> "") {
			$sFilterWrk = "`Hidup`" . ew_SearchString("=", $this->HidupIbu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Hidup`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hidup`";
		$sWhereWrk = "";
		$this->HidupIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->HidupIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->HidupIbu->ViewValue = $this->HidupIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->HidupIbu->ViewValue = $this->HidupIbu->CurrentValue;
			}
		} else {
			$this->HidupIbu->ViewValue = NULL;
		}
		$this->HidupIbu->ViewCustomAttributes = "";

		// AlamatOrtu
		$this->AlamatOrtu->ViewValue = $this->AlamatOrtu->CurrentValue;
		$this->AlamatOrtu->ViewCustomAttributes = "";

		// RTOrtu
		$this->RTOrtu->ViewValue = $this->RTOrtu->CurrentValue;
		$this->RTOrtu->ViewCustomAttributes = "";

		// RWOrtu
		$this->RWOrtu->ViewValue = $this->RWOrtu->CurrentValue;
		$this->RWOrtu->ViewCustomAttributes = "";

		// KodePosOrtu
		$this->KodePosOrtu->ViewValue = $this->KodePosOrtu->CurrentValue;
		$this->KodePosOrtu->ViewCustomAttributes = "";

		// ProvinsiIDOrtu
		if (strval($this->ProvinsiIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
		$sWhereWrk = "";
		$this->ProvinsiIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProvinsiIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->ProvinsiIDOrtu->ViewValue = $this->ProvinsiIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->ProvinsiIDOrtu->ViewValue = $this->ProvinsiIDOrtu->CurrentValue;
			}
		} else {
			$this->ProvinsiIDOrtu->ViewValue = NULL;
		}
		$this->ProvinsiIDOrtu->ViewCustomAttributes = "";

		// KabupatenIDOrtu
		if (strval($this->KabupatenIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->KabupatenIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KabupatenIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KabupatenIDOrtu->ViewValue = $this->KabupatenIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KabupatenIDOrtu->ViewValue = $this->KabupatenIDOrtu->CurrentValue;
			}
		} else {
			$this->KabupatenIDOrtu->ViewValue = NULL;
		}
		$this->KabupatenIDOrtu->ViewCustomAttributes = "";

		// KecamatanIDOrtu
		if (strval($this->KecamatanIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
		$sWhereWrk = "";
		$this->KecamatanIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KecamatanIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KecamatanIDOrtu->ViewValue = $this->KecamatanIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KecamatanIDOrtu->ViewValue = $this->KecamatanIDOrtu->CurrentValue;
			}
		} else {
			$this->KecamatanIDOrtu->ViewValue = NULL;
		}
		$this->KecamatanIDOrtu->ViewCustomAttributes = "";

		// DesaIDOrtu
		if (strval($this->DesaIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
		$sWhereWrk = "";
		$this->DesaIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->DesaIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->DesaIDOrtu->ViewValue = $this->DesaIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->DesaIDOrtu->ViewValue = $this->DesaIDOrtu->CurrentValue;
			}
		} else {
			$this->DesaIDOrtu->ViewValue = NULL;
		}
		$this->DesaIDOrtu->ViewCustomAttributes = "";

		// NegaraIDOrtu
		if (strval($this->NegaraIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`NegaraID`" . ew_SearchString("=", $this->NegaraIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `NegaraID`, `NamaNegara` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_negara`";
		$sWhereWrk = "";
		$this->NegaraIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->NegaraIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->NegaraIDOrtu->ViewValue = $this->NegaraIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->NegaraIDOrtu->ViewValue = $this->NegaraIDOrtu->CurrentValue;
			}
		} else {
			$this->NegaraIDOrtu->ViewValue = NULL;
		}
		$this->NegaraIDOrtu->ViewCustomAttributes = "";

		// TeleponOrtu
		$this->TeleponOrtu->ViewValue = $this->TeleponOrtu->CurrentValue;
		$this->TeleponOrtu->ViewCustomAttributes = "";

		// HandphoneOrtu
		$this->HandphoneOrtu->ViewValue = $this->HandphoneOrtu->CurrentValue;
		$this->HandphoneOrtu->ViewCustomAttributes = "";

		// EmailOrtu
		$this->EmailOrtu->ViewValue = $this->EmailOrtu->CurrentValue;
		$this->EmailOrtu->ViewCustomAttributes = "";

		// AsalSekolah
		$this->AsalSekolah->ViewValue = $this->AsalSekolah->CurrentValue;
		$this->AsalSekolah->ViewCustomAttributes = "";

		// AlamatSekolah
		$this->AlamatSekolah->ViewValue = $this->AlamatSekolah->CurrentValue;
		$this->AlamatSekolah->ViewCustomAttributes = "";

		// ProvinsiIDSekolah
		if (strval($this->ProvinsiIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
		$sWhereWrk = "";
		$this->ProvinsiIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProvinsiIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->ProvinsiIDSekolah->ViewValue = $this->ProvinsiIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->ProvinsiIDSekolah->ViewValue = $this->ProvinsiIDSekolah->CurrentValue;
			}
		} else {
			$this->ProvinsiIDSekolah->ViewValue = NULL;
		}
		$this->ProvinsiIDSekolah->ViewCustomAttributes = "";

		// KabupatenIDSekolah
		if (strval($this->KabupatenIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->KabupatenIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KabupatenIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KabupatenIDSekolah->ViewValue = $this->KabupatenIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KabupatenIDSekolah->ViewValue = $this->KabupatenIDSekolah->CurrentValue;
			}
		} else {
			$this->KabupatenIDSekolah->ViewValue = NULL;
		}
		$this->KabupatenIDSekolah->ViewCustomAttributes = "";

		// KecamatanIDSekolah
		if (strval($this->KecamatanIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
		$sWhereWrk = "";
		$this->KecamatanIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KecamatanIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KecamatanIDSekolah->ViewValue = $this->KecamatanIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KecamatanIDSekolah->ViewValue = $this->KecamatanIDSekolah->CurrentValue;
			}
		} else {
			$this->KecamatanIDSekolah->ViewValue = NULL;
		}
		$this->KecamatanIDSekolah->ViewCustomAttributes = "";

		// DesaIDSekolah
		if (strval($this->DesaIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
		$sWhereWrk = "";
		$this->DesaIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->DesaIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->DesaIDSekolah->ViewValue = $this->DesaIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->DesaIDSekolah->ViewValue = $this->DesaIDSekolah->CurrentValue;
			}
		} else {
			$this->DesaIDSekolah->ViewValue = NULL;
		}
		$this->DesaIDSekolah->ViewCustomAttributes = "";

		// NilaiSekolah
		$this->NilaiSekolah->ViewValue = $this->NilaiSekolah->CurrentValue;
		$this->NilaiSekolah->ViewCustomAttributes = "";

		// TahunLulus
		$this->TahunLulus->ViewValue = $this->TahunLulus->CurrentValue;
		$this->TahunLulus->ViewCustomAttributes = "";

		// IjazahSekolah
		$this->IjazahSekolah->ViewValue = $this->IjazahSekolah->CurrentValue;
		$this->IjazahSekolah->ViewCustomAttributes = "";

		// TglIjazah
		$this->TglIjazah->ViewValue = $this->TglIjazah->CurrentValue;
		$this->TglIjazah->ViewValue = ew_FormatDateTime($this->TglIjazah->ViewValue, 0);
		$this->TglIjazah->ViewCustomAttributes = "";

		// Creator
		$this->Creator->ViewValue = $this->Creator->CurrentValue;
		$this->Creator->ViewCustomAttributes = "";

		// CreateDate
		$this->CreateDate->ViewValue = $this->CreateDate->CurrentValue;
		$this->CreateDate->ViewValue = ew_FormatDateTime($this->CreateDate->ViewValue, 0);
		$this->CreateDate->ViewCustomAttributes = "";

		// Editor
		$this->Editor->ViewValue = $this->Editor->CurrentValue;
		$this->Editor->ViewCustomAttributes = "";

		// EditDate
		$this->EditDate->ViewValue = $this->EditDate->CurrentValue;
		$this->EditDate->ViewValue = ew_FormatDateTime($this->EditDate->ViewValue, 0);
		$this->EditDate->ViewCustomAttributes = "";

		// LockStatus
		if (ew_ConvertToBool($this->LockStatus->CurrentValue)) {
			$this->LockStatus->ViewValue = $this->LockStatus->FldTagCaption(2) <> "" ? $this->LockStatus->FldTagCaption(2) : "Lock";
		} else {
			$this->LockStatus->ViewValue = $this->LockStatus->FldTagCaption(1) <> "" ? $this->LockStatus->FldTagCaption(1) : "Unlock";
		}
		$this->LockStatus->ViewCustomAttributes = "";

		// LockDate
		$this->LockDate->ViewValue = $this->LockDate->CurrentValue;
		$this->LockDate->ViewValue = ew_FormatDateTime($this->LockDate->ViewValue, 0);
		$this->LockDate->ViewCustomAttributes = "";

		// VerifiedBy
		$this->VerifiedBy->ViewValue = $this->VerifiedBy->CurrentValue;
		$this->VerifiedBy->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Ya";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "Tidak";
		}
		$this->NA->ViewCustomAttributes = "";

		// StudentID
		$this->StudentID->LinkCustomAttributes = "";
		$this->StudentID->HrefValue = "";
		$this->StudentID->TooltipValue = "";

		// Nama
		$this->Nama->LinkCustomAttributes = "";
		$this->Nama->HrefValue = "";
		$this->Nama->TooltipValue = "";

		// LevelID
		$this->LevelID->LinkCustomAttributes = "";
		$this->LevelID->HrefValue = "";
		$this->LevelID->TooltipValue = "";

		// Password
		$this->Password->LinkCustomAttributes = "";
		$this->Password->HrefValue = "";
		$this->Password->TooltipValue = "";

		// KampusID
		$this->KampusID->LinkCustomAttributes = "";
		$this->KampusID->HrefValue = "";
		$this->KampusID->TooltipValue = "";

		// ProdiID
		$this->ProdiID->LinkCustomAttributes = "";
		$this->ProdiID->HrefValue = "";
		$this->ProdiID->TooltipValue = "";

		// StudentStatusID
		$this->StudentStatusID->LinkCustomAttributes = "";
		$this->StudentStatusID->HrefValue = "";
		$this->StudentStatusID->TooltipValue = "";

		// TahunID
		$this->TahunID->LinkCustomAttributes = "";
		$this->TahunID->HrefValue = "";
		$this->TahunID->TooltipValue = "";

		// Foto
		$this->Foto->LinkCustomAttributes = "";
		$this->Foto->UploadPath = "upload";
		if (!ew_Empty($this->Foto->Upload->DbValue)) {
			$this->Foto->HrefValue = ew_GetFileUploadUrl($this->Foto, $this->Foto->Upload->DbValue); // Add prefix/suffix
			$this->Foto->LinkAttrs["target"] = ""; // Add target
			if ($this->Export <> "") $this->Foto->HrefValue = ew_ConvertFullUrl($this->Foto->HrefValue);
		} else {
			$this->Foto->HrefValue = "";
		}
		$this->Foto->HrefValue2 = $this->Foto->UploadPath . $this->Foto->Upload->DbValue;
		$this->Foto->TooltipValue = "";
		if ($this->Foto->UseColorbox) {
			if (ew_Empty($this->Foto->TooltipValue))
				$this->Foto->LinkAttrs["title"] = $Language->Phrase("ViewImageGallery");
			$this->Foto->LinkAttrs["data-rel"] = "student_x_Foto";
			ew_AppendClass($this->Foto->LinkAttrs["class"], "ewLightbox");
		}

		// NIK
		$this->NIK->LinkCustomAttributes = "";
		$this->NIK->HrefValue = "";
		$this->NIK->TooltipValue = "";

		// WargaNegara
		$this->WargaNegara->LinkCustomAttributes = "";
		$this->WargaNegara->HrefValue = "";
		$this->WargaNegara->TooltipValue = "";

		// Kelamin
		$this->Kelamin->LinkCustomAttributes = "";
		$this->Kelamin->HrefValue = "";
		$this->Kelamin->TooltipValue = "";

		// TempatLahir
		$this->TempatLahir->LinkCustomAttributes = "";
		$this->TempatLahir->HrefValue = "";
		$this->TempatLahir->TooltipValue = "";

		// TanggalLahir
		$this->TanggalLahir->LinkCustomAttributes = "";
		$this->TanggalLahir->HrefValue = "";
		$this->TanggalLahir->TooltipValue = "";

		// AgamaID
		$this->AgamaID->LinkCustomAttributes = "";
		$this->AgamaID->HrefValue = "";
		$this->AgamaID->TooltipValue = "";

		// Darah
		$this->Darah->LinkCustomAttributes = "";
		$this->Darah->HrefValue = "";
		$this->Darah->TooltipValue = "";

		// StatusSipil
		$this->StatusSipil->LinkCustomAttributes = "";
		$this->StatusSipil->HrefValue = "";
		$this->StatusSipil->TooltipValue = "";

		// AlamatDomisili
		$this->AlamatDomisili->LinkCustomAttributes = "";
		$this->AlamatDomisili->HrefValue = "";
		$this->AlamatDomisili->TooltipValue = "";

		// RT
		$this->RT->LinkCustomAttributes = "";
		$this->RT->HrefValue = "";
		$this->RT->TooltipValue = "";

		// RW
		$this->RW->LinkCustomAttributes = "";
		$this->RW->HrefValue = "";
		$this->RW->TooltipValue = "";

		// KodePos
		$this->KodePos->LinkCustomAttributes = "";
		$this->KodePos->HrefValue = "";
		$this->KodePos->TooltipValue = "";

		// ProvinsiID
		$this->ProvinsiID->LinkCustomAttributes = "";
		$this->ProvinsiID->HrefValue = "";
		$this->ProvinsiID->TooltipValue = "";

		// KabupatenKotaID
		$this->KabupatenKotaID->LinkCustomAttributes = "";
		$this->KabupatenKotaID->HrefValue = "";
		$this->KabupatenKotaID->TooltipValue = "";

		// KecamatanID
		$this->KecamatanID->LinkCustomAttributes = "";
		$this->KecamatanID->HrefValue = "";
		$this->KecamatanID->TooltipValue = "";

		// DesaID
		$this->DesaID->LinkCustomAttributes = "";
		$this->DesaID->HrefValue = "";
		$this->DesaID->TooltipValue = "";

		// AnakKe
		$this->AnakKe->LinkCustomAttributes = "";
		$this->AnakKe->HrefValue = "";
		$this->AnakKe->TooltipValue = "";

		// JumlahSaudara
		$this->JumlahSaudara->LinkCustomAttributes = "";
		$this->JumlahSaudara->HrefValue = "";
		$this->JumlahSaudara->TooltipValue = "";

		// Telepon
		$this->Telepon->LinkCustomAttributes = "";
		$this->Telepon->HrefValue = "";
		$this->Telepon->TooltipValue = "";

		// Handphone
		$this->Handphone->LinkCustomAttributes = "";
		$this->Handphone->HrefValue = "";
		$this->Handphone->TooltipValue = "";

		// Email
		$this->_Email->LinkCustomAttributes = "";
		$this->_Email->HrefValue = "";
		$this->_Email->TooltipValue = "";

		// NamaAyah
		$this->NamaAyah->LinkCustomAttributes = "";
		$this->NamaAyah->HrefValue = "";
		$this->NamaAyah->TooltipValue = "";

		// AgamaAyah
		$this->AgamaAyah->LinkCustomAttributes = "";
		$this->AgamaAyah->HrefValue = "";
		$this->AgamaAyah->TooltipValue = "";

		// PendidikanAyah
		$this->PendidikanAyah->LinkCustomAttributes = "";
		$this->PendidikanAyah->HrefValue = "";
		$this->PendidikanAyah->TooltipValue = "";

		// PekerjaanAyah
		$this->PekerjaanAyah->LinkCustomAttributes = "";
		$this->PekerjaanAyah->HrefValue = "";
		$this->PekerjaanAyah->TooltipValue = "";

		// HidupAyah
		$this->HidupAyah->LinkCustomAttributes = "";
		$this->HidupAyah->HrefValue = "";
		$this->HidupAyah->TooltipValue = "";

		// NamaIbu
		$this->NamaIbu->LinkCustomAttributes = "";
		$this->NamaIbu->HrefValue = "";
		$this->NamaIbu->TooltipValue = "";

		// AgamaIbu
		$this->AgamaIbu->LinkCustomAttributes = "";
		$this->AgamaIbu->HrefValue = "";
		$this->AgamaIbu->TooltipValue = "";

		// PendidikanIbu
		$this->PendidikanIbu->LinkCustomAttributes = "";
		$this->PendidikanIbu->HrefValue = "";
		$this->PendidikanIbu->TooltipValue = "";

		// PekerjaanIbu
		$this->PekerjaanIbu->LinkCustomAttributes = "";
		$this->PekerjaanIbu->HrefValue = "";
		$this->PekerjaanIbu->TooltipValue = "";

		// HidupIbu
		$this->HidupIbu->LinkCustomAttributes = "";
		$this->HidupIbu->HrefValue = "";
		$this->HidupIbu->TooltipValue = "";

		// AlamatOrtu
		$this->AlamatOrtu->LinkCustomAttributes = "";
		$this->AlamatOrtu->HrefValue = "";
		$this->AlamatOrtu->TooltipValue = "";

		// RTOrtu
		$this->RTOrtu->LinkCustomAttributes = "";
		$this->RTOrtu->HrefValue = "";
		$this->RTOrtu->TooltipValue = "";

		// RWOrtu
		$this->RWOrtu->LinkCustomAttributes = "";
		$this->RWOrtu->HrefValue = "";
		$this->RWOrtu->TooltipValue = "";

		// KodePosOrtu
		$this->KodePosOrtu->LinkCustomAttributes = "";
		$this->KodePosOrtu->HrefValue = "";
		$this->KodePosOrtu->TooltipValue = "";

		// ProvinsiIDOrtu
		$this->ProvinsiIDOrtu->LinkCustomAttributes = "";
		$this->ProvinsiIDOrtu->HrefValue = "";
		$this->ProvinsiIDOrtu->TooltipValue = "";

		// KabupatenIDOrtu
		$this->KabupatenIDOrtu->LinkCustomAttributes = "";
		$this->KabupatenIDOrtu->HrefValue = "";
		$this->KabupatenIDOrtu->TooltipValue = "";

		// KecamatanIDOrtu
		$this->KecamatanIDOrtu->LinkCustomAttributes = "";
		$this->KecamatanIDOrtu->HrefValue = "";
		$this->KecamatanIDOrtu->TooltipValue = "";

		// DesaIDOrtu
		$this->DesaIDOrtu->LinkCustomAttributes = "";
		$this->DesaIDOrtu->HrefValue = "";
		$this->DesaIDOrtu->TooltipValue = "";

		// NegaraIDOrtu
		$this->NegaraIDOrtu->LinkCustomAttributes = "";
		$this->NegaraIDOrtu->HrefValue = "";
		$this->NegaraIDOrtu->TooltipValue = "";

		// TeleponOrtu
		$this->TeleponOrtu->LinkCustomAttributes = "";
		$this->TeleponOrtu->HrefValue = "";
		$this->TeleponOrtu->TooltipValue = "";

		// HandphoneOrtu
		$this->HandphoneOrtu->LinkCustomAttributes = "";
		$this->HandphoneOrtu->HrefValue = "";
		$this->HandphoneOrtu->TooltipValue = "";

		// EmailOrtu
		$this->EmailOrtu->LinkCustomAttributes = "";
		$this->EmailOrtu->HrefValue = "";
		$this->EmailOrtu->TooltipValue = "";

		// AsalSekolah
		$this->AsalSekolah->LinkCustomAttributes = "";
		$this->AsalSekolah->HrefValue = "";
		$this->AsalSekolah->TooltipValue = "";

		// AlamatSekolah
		$this->AlamatSekolah->LinkCustomAttributes = "";
		$this->AlamatSekolah->HrefValue = "";
		$this->AlamatSekolah->TooltipValue = "";

		// ProvinsiIDSekolah
		$this->ProvinsiIDSekolah->LinkCustomAttributes = "";
		$this->ProvinsiIDSekolah->HrefValue = "";
		$this->ProvinsiIDSekolah->TooltipValue = "";

		// KabupatenIDSekolah
		$this->KabupatenIDSekolah->LinkCustomAttributes = "";
		$this->KabupatenIDSekolah->HrefValue = "";
		$this->KabupatenIDSekolah->TooltipValue = "";

		// KecamatanIDSekolah
		$this->KecamatanIDSekolah->LinkCustomAttributes = "";
		$this->KecamatanIDSekolah->HrefValue = "";
		$this->KecamatanIDSekolah->TooltipValue = "";

		// DesaIDSekolah
		$this->DesaIDSekolah->LinkCustomAttributes = "";
		$this->DesaIDSekolah->HrefValue = "";
		$this->DesaIDSekolah->TooltipValue = "";

		// NilaiSekolah
		$this->NilaiSekolah->LinkCustomAttributes = "";
		$this->NilaiSekolah->HrefValue = "";
		$this->NilaiSekolah->TooltipValue = "";

		// TahunLulus
		$this->TahunLulus->LinkCustomAttributes = "";
		$this->TahunLulus->HrefValue = "";
		$this->TahunLulus->TooltipValue = "";

		// IjazahSekolah
		$this->IjazahSekolah->LinkCustomAttributes = "";
		$this->IjazahSekolah->HrefValue = "";
		$this->IjazahSekolah->TooltipValue = "";

		// TglIjazah
		$this->TglIjazah->LinkCustomAttributes = "";
		$this->TglIjazah->HrefValue = "";
		$this->TglIjazah->TooltipValue = "";

		// Creator
		$this->Creator->LinkCustomAttributes = "";
		$this->Creator->HrefValue = "";
		$this->Creator->TooltipValue = "";

		// CreateDate
		$this->CreateDate->LinkCustomAttributes = "";
		$this->CreateDate->HrefValue = "";
		$this->CreateDate->TooltipValue = "";

		// Editor
		$this->Editor->LinkCustomAttributes = "";
		$this->Editor->HrefValue = "";
		$this->Editor->TooltipValue = "";

		// EditDate
		$this->EditDate->LinkCustomAttributes = "";
		$this->EditDate->HrefValue = "";
		$this->EditDate->TooltipValue = "";

		// LockStatus
		$this->LockStatus->LinkCustomAttributes = "";
		$this->LockStatus->HrefValue = "";
		$this->LockStatus->TooltipValue = "";

		// LockDate
		$this->LockDate->LinkCustomAttributes = "";
		$this->LockDate->HrefValue = "";
		$this->LockDate->TooltipValue = "";

		// VerifiedBy
		$this->VerifiedBy->LinkCustomAttributes = "";
		$this->VerifiedBy->HrefValue = "";
		$this->VerifiedBy->TooltipValue = "";

		// NA
		$this->NA->LinkCustomAttributes = "";
		$this->NA->HrefValue = "";
		$this->NA->TooltipValue = "";

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Render edit row values
	function RenderEditRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

		// StudentID
		$this->StudentID->EditAttrs["class"] = "form-control";
		$this->StudentID->EditCustomAttributes = "";
		$this->StudentID->EditValue = $this->StudentID->CurrentValue;
		$this->StudentID->CssStyle = "font-weight: bold;";
		$this->StudentID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->EditAttrs["class"] = "form-control";
		$this->Nama->EditCustomAttributes = "";
		$this->Nama->EditValue = $this->Nama->CurrentValue;
		$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

		// LevelID
		$this->LevelID->EditAttrs["class"] = "form-control";
		$this->LevelID->EditCustomAttributes = "";
		$this->LevelID->EditValue = $this->LevelID->CurrentValue;
		$this->LevelID->PlaceHolder = ew_RemoveHtml($this->LevelID->FldCaption());

		// Password
		$this->Password->EditAttrs["class"] = "form-control";
		$this->Password->EditCustomAttributes = "";
		$this->Password->EditValue = $this->Password->CurrentValue;
		$this->Password->PlaceHolder = ew_RemoveHtml($this->Password->FldCaption());

		// KampusID
		$this->KampusID->EditAttrs["class"] = "form-control";
		$this->KampusID->EditCustomAttributes = "";

		// ProdiID
		$this->ProdiID->EditAttrs["class"] = "form-control";
		$this->ProdiID->EditCustomAttributes = "";

		// StudentStatusID
		$this->StudentStatusID->EditAttrs["class"] = "form-control";
		$this->StudentStatusID->EditCustomAttributes = "";

		// TahunID
		$this->TahunID->EditAttrs["class"] = "form-control";
		$this->TahunID->EditCustomAttributes = "";
		$this->TahunID->EditValue = $this->TahunID->CurrentValue;
		$this->TahunID->PlaceHolder = ew_RemoveHtml($this->TahunID->FldCaption());

		// Foto
		$this->Foto->EditAttrs["class"] = "form-control";
		$this->Foto->EditCustomAttributes = "";
		$this->Foto->UploadPath = "upload";
		if (!ew_Empty($this->Foto->Upload->DbValue)) {
			$this->Foto->ImageAlt = $this->Foto->FldAlt();
			$this->Foto->EditValue = $this->Foto->Upload->DbValue;
		} else {
			$this->Foto->EditValue = "";
		}
		if (!ew_Empty($this->Foto->CurrentValue))
			$this->Foto->Upload->FileName = $this->Foto->CurrentValue;

		// NIK
		$this->NIK->EditAttrs["class"] = "form-control";
		$this->NIK->EditCustomAttributes = "";
		$this->NIK->EditValue = $this->NIK->CurrentValue;
		$this->NIK->PlaceHolder = ew_RemoveHtml($this->NIK->FldCaption());

		// WargaNegara
		$this->WargaNegara->EditCustomAttributes = "";
		$this->WargaNegara->EditValue = $this->WargaNegara->Options(FALSE);

		// Kelamin
		$this->Kelamin->EditCustomAttributes = "";

		// TempatLahir
		$this->TempatLahir->EditAttrs["class"] = "form-control";
		$this->TempatLahir->EditCustomAttributes = "";
		$this->TempatLahir->EditValue = $this->TempatLahir->CurrentValue;
		$this->TempatLahir->PlaceHolder = ew_RemoveHtml($this->TempatLahir->FldCaption());

		// TanggalLahir
		$this->TanggalLahir->EditAttrs["class"] = "form-control";
		$this->TanggalLahir->EditCustomAttributes = "";
		$this->TanggalLahir->EditValue = ew_FormatDateTime($this->TanggalLahir->CurrentValue, 8);
		$this->TanggalLahir->PlaceHolder = ew_RemoveHtml($this->TanggalLahir->FldCaption());

		// AgamaID
		$this->AgamaID->EditAttrs["class"] = "form-control";
		$this->AgamaID->EditCustomAttributes = "";

		// Darah
		$this->Darah->EditCustomAttributes = "";

		// StatusSipil
		$this->StatusSipil->EditAttrs["class"] = "form-control";
		$this->StatusSipil->EditCustomAttributes = "";

		// AlamatDomisili
		$this->AlamatDomisili->EditAttrs["class"] = "form-control";
		$this->AlamatDomisili->EditCustomAttributes = "";
		$this->AlamatDomisili->EditValue = $this->AlamatDomisili->CurrentValue;
		$this->AlamatDomisili->PlaceHolder = ew_RemoveHtml($this->AlamatDomisili->FldCaption());

		// RT
		$this->RT->EditAttrs["class"] = "form-control";
		$this->RT->EditCustomAttributes = "";
		$this->RT->EditValue = $this->RT->CurrentValue;
		$this->RT->PlaceHolder = ew_RemoveHtml($this->RT->FldCaption());

		// RW
		$this->RW->EditAttrs["class"] = "form-control";
		$this->RW->EditCustomAttributes = "";
		$this->RW->EditValue = $this->RW->CurrentValue;
		$this->RW->PlaceHolder = ew_RemoveHtml($this->RW->FldCaption());

		// KodePos
		$this->KodePos->EditAttrs["class"] = "form-control";
		$this->KodePos->EditCustomAttributes = "";
		$this->KodePos->EditValue = $this->KodePos->CurrentValue;
		$this->KodePos->PlaceHolder = ew_RemoveHtml($this->KodePos->FldCaption());

		// ProvinsiID
		$this->ProvinsiID->EditAttrs["class"] = "form-control";
		$this->ProvinsiID->EditCustomAttributes = "";

		// KabupatenKotaID
		$this->KabupatenKotaID->EditAttrs["class"] = "form-control";
		$this->KabupatenKotaID->EditCustomAttributes = "";

		// KecamatanID
		$this->KecamatanID->EditAttrs["class"] = "form-control";
		$this->KecamatanID->EditCustomAttributes = "";

		// DesaID
		$this->DesaID->EditAttrs["class"] = "form-control";
		$this->DesaID->EditCustomAttributes = "";

		// AnakKe
		$this->AnakKe->EditAttrs["class"] = "form-control";
		$this->AnakKe->EditCustomAttributes = "";
		$this->AnakKe->EditValue = $this->AnakKe->CurrentValue;
		$this->AnakKe->PlaceHolder = ew_RemoveHtml($this->AnakKe->FldCaption());

		// JumlahSaudara
		$this->JumlahSaudara->EditAttrs["class"] = "form-control";
		$this->JumlahSaudara->EditCustomAttributes = "";
		$this->JumlahSaudara->EditValue = $this->JumlahSaudara->CurrentValue;
		$this->JumlahSaudara->PlaceHolder = ew_RemoveHtml($this->JumlahSaudara->FldCaption());

		// Telepon
		$this->Telepon->EditAttrs["class"] = "form-control";
		$this->Telepon->EditCustomAttributes = "";
		$this->Telepon->EditValue = $this->Telepon->CurrentValue;
		$this->Telepon->PlaceHolder = ew_RemoveHtml($this->Telepon->FldCaption());

		// Handphone
		$this->Handphone->EditAttrs["class"] = "form-control";
		$this->Handphone->EditCustomAttributes = "";
		$this->Handphone->EditValue = $this->Handphone->CurrentValue;
		$this->Handphone->PlaceHolder = ew_RemoveHtml($this->Handphone->FldCaption());

		// Email
		$this->_Email->EditAttrs["class"] = "form-control";
		$this->_Email->EditCustomAttributes = "";
		$this->_Email->EditValue = $this->_Email->CurrentValue;
		$this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

		// NamaAyah
		$this->NamaAyah->EditAttrs["class"] = "form-control";
		$this->NamaAyah->EditCustomAttributes = "";
		$this->NamaAyah->EditValue = $this->NamaAyah->CurrentValue;
		$this->NamaAyah->PlaceHolder = ew_RemoveHtml($this->NamaAyah->FldCaption());

		// AgamaAyah
		$this->AgamaAyah->EditAttrs["class"] = "form-control";
		$this->AgamaAyah->EditCustomAttributes = "";

		// PendidikanAyah
		$this->PendidikanAyah->EditAttrs["class"] = "form-control";
		$this->PendidikanAyah->EditCustomAttributes = "";

		// PekerjaanAyah
		$this->PekerjaanAyah->EditAttrs["class"] = "form-control";
		$this->PekerjaanAyah->EditCustomAttributes = "";

		// HidupAyah
		$this->HidupAyah->EditAttrs["class"] = "form-control";
		$this->HidupAyah->EditCustomAttributes = "";

		// NamaIbu
		$this->NamaIbu->EditAttrs["class"] = "form-control";
		$this->NamaIbu->EditCustomAttributes = "";
		$this->NamaIbu->EditValue = $this->NamaIbu->CurrentValue;
		$this->NamaIbu->PlaceHolder = ew_RemoveHtml($this->NamaIbu->FldCaption());

		// AgamaIbu
		$this->AgamaIbu->EditAttrs["class"] = "form-control";
		$this->AgamaIbu->EditCustomAttributes = "";

		// PendidikanIbu
		$this->PendidikanIbu->EditAttrs["class"] = "form-control";
		$this->PendidikanIbu->EditCustomAttributes = "";

		// PekerjaanIbu
		$this->PekerjaanIbu->EditAttrs["class"] = "form-control";
		$this->PekerjaanIbu->EditCustomAttributes = "";

		// HidupIbu
		$this->HidupIbu->EditAttrs["class"] = "form-control";
		$this->HidupIbu->EditCustomAttributes = "";

		// AlamatOrtu
		$this->AlamatOrtu->EditAttrs["class"] = "form-control";
		$this->AlamatOrtu->EditCustomAttributes = "";
		$this->AlamatOrtu->EditValue = $this->AlamatOrtu->CurrentValue;
		$this->AlamatOrtu->PlaceHolder = ew_RemoveHtml($this->AlamatOrtu->FldCaption());

		// RTOrtu
		$this->RTOrtu->EditAttrs["class"] = "form-control";
		$this->RTOrtu->EditCustomAttributes = "";
		$this->RTOrtu->EditValue = $this->RTOrtu->CurrentValue;
		$this->RTOrtu->PlaceHolder = ew_RemoveHtml($this->RTOrtu->FldCaption());

		// RWOrtu
		$this->RWOrtu->EditAttrs["class"] = "form-control";
		$this->RWOrtu->EditCustomAttributes = "";
		$this->RWOrtu->EditValue = $this->RWOrtu->CurrentValue;
		$this->RWOrtu->PlaceHolder = ew_RemoveHtml($this->RWOrtu->FldCaption());

		// KodePosOrtu
		$this->KodePosOrtu->EditAttrs["class"] = "form-control";
		$this->KodePosOrtu->EditCustomAttributes = "";
		$this->KodePosOrtu->EditValue = $this->KodePosOrtu->CurrentValue;
		$this->KodePosOrtu->PlaceHolder = ew_RemoveHtml($this->KodePosOrtu->FldCaption());

		// ProvinsiIDOrtu
		$this->ProvinsiIDOrtu->EditAttrs["class"] = "form-control";
		$this->ProvinsiIDOrtu->EditCustomAttributes = "";

		// KabupatenIDOrtu
		$this->KabupatenIDOrtu->EditAttrs["class"] = "form-control";
		$this->KabupatenIDOrtu->EditCustomAttributes = "";

		// KecamatanIDOrtu
		$this->KecamatanIDOrtu->EditAttrs["class"] = "form-control";
		$this->KecamatanIDOrtu->EditCustomAttributes = "";

		// DesaIDOrtu
		$this->DesaIDOrtu->EditAttrs["class"] = "form-control";
		$this->DesaIDOrtu->EditCustomAttributes = "";

		// NegaraIDOrtu
		$this->NegaraIDOrtu->EditAttrs["class"] = "form-control";
		$this->NegaraIDOrtu->EditCustomAttributes = "";

		// TeleponOrtu
		$this->TeleponOrtu->EditAttrs["class"] = "form-control";
		$this->TeleponOrtu->EditCustomAttributes = "";
		$this->TeleponOrtu->EditValue = $this->TeleponOrtu->CurrentValue;
		$this->TeleponOrtu->PlaceHolder = ew_RemoveHtml($this->TeleponOrtu->FldCaption());

		// HandphoneOrtu
		$this->HandphoneOrtu->EditAttrs["class"] = "form-control";
		$this->HandphoneOrtu->EditCustomAttributes = "";
		$this->HandphoneOrtu->EditValue = $this->HandphoneOrtu->CurrentValue;
		$this->HandphoneOrtu->PlaceHolder = ew_RemoveHtml($this->HandphoneOrtu->FldCaption());

		// EmailOrtu
		$this->EmailOrtu->EditAttrs["class"] = "form-control";
		$this->EmailOrtu->EditCustomAttributes = "";
		$this->EmailOrtu->EditValue = $this->EmailOrtu->CurrentValue;
		$this->EmailOrtu->PlaceHolder = ew_RemoveHtml($this->EmailOrtu->FldCaption());

		// AsalSekolah
		$this->AsalSekolah->EditAttrs["class"] = "form-control";
		$this->AsalSekolah->EditCustomAttributes = "";
		$this->AsalSekolah->EditValue = $this->AsalSekolah->CurrentValue;
		$this->AsalSekolah->PlaceHolder = ew_RemoveHtml($this->AsalSekolah->FldCaption());

		// AlamatSekolah
		$this->AlamatSekolah->EditAttrs["class"] = "form-control";
		$this->AlamatSekolah->EditCustomAttributes = "";
		$this->AlamatSekolah->EditValue = $this->AlamatSekolah->CurrentValue;
		$this->AlamatSekolah->PlaceHolder = ew_RemoveHtml($this->AlamatSekolah->FldCaption());

		// ProvinsiIDSekolah
		$this->ProvinsiIDSekolah->EditAttrs["class"] = "form-control";
		$this->ProvinsiIDSekolah->EditCustomAttributes = "";

		// KabupatenIDSekolah
		$this->KabupatenIDSekolah->EditAttrs["class"] = "form-control";
		$this->KabupatenIDSekolah->EditCustomAttributes = "";

		// KecamatanIDSekolah
		$this->KecamatanIDSekolah->EditAttrs["class"] = "form-control";
		$this->KecamatanIDSekolah->EditCustomAttributes = "";

		// DesaIDSekolah
		$this->DesaIDSekolah->EditAttrs["class"] = "form-control";
		$this->DesaIDSekolah->EditCustomAttributes = "";

		// NilaiSekolah
		$this->NilaiSekolah->EditAttrs["class"] = "form-control";
		$this->NilaiSekolah->EditCustomAttributes = "";
		$this->NilaiSekolah->EditValue = $this->NilaiSekolah->CurrentValue;
		$this->NilaiSekolah->PlaceHolder = ew_RemoveHtml($this->NilaiSekolah->FldCaption());

		// TahunLulus
		$this->TahunLulus->EditAttrs["class"] = "form-control";
		$this->TahunLulus->EditCustomAttributes = "";
		$this->TahunLulus->EditValue = $this->TahunLulus->CurrentValue;
		$this->TahunLulus->PlaceHolder = ew_RemoveHtml($this->TahunLulus->FldCaption());

		// IjazahSekolah
		$this->IjazahSekolah->EditAttrs["class"] = "form-control";
		$this->IjazahSekolah->EditCustomAttributes = "";
		$this->IjazahSekolah->EditValue = $this->IjazahSekolah->CurrentValue;
		$this->IjazahSekolah->PlaceHolder = ew_RemoveHtml($this->IjazahSekolah->FldCaption());

		// TglIjazah
		$this->TglIjazah->EditAttrs["class"] = "form-control";
		$this->TglIjazah->EditCustomAttributes = "";
		$this->TglIjazah->EditValue = ew_FormatDateTime($this->TglIjazah->CurrentValue, 8);
		$this->TglIjazah->PlaceHolder = ew_RemoveHtml($this->TglIjazah->FldCaption());

		// Creator
		// CreateDate
		// Editor
		// EditDate
		// LockStatus

		$this->LockStatus->EditCustomAttributes = "";
		$this->LockStatus->EditValue = $this->LockStatus->Options(FALSE);

		// LockDate
		// VerifiedBy
		// NA

		$this->NA->EditCustomAttributes = "";
		$this->NA->EditValue = $this->NA->Options(FALSE);

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {

		// Call Row Rendered event
		$this->Row_Rendered();
	}
	var $ExportDoc;

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;
		if (!$Doc->ExportCustom) {

			// Write header
			$Doc->ExportTableHeader();
			if ($Doc->Horizontal) { // Horizontal format, write header
				$Doc->BeginExportRow();
				if ($ExportPageType == "view") {
					if ($this->StudentID->Exportable) $Doc->ExportCaption($this->StudentID);
					if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
					if ($this->LevelID->Exportable) $Doc->ExportCaption($this->LevelID);
					if ($this->KampusID->Exportable) $Doc->ExportCaption($this->KampusID);
					if ($this->ProdiID->Exportable) $Doc->ExportCaption($this->ProdiID);
					if ($this->StudentStatusID->Exportable) $Doc->ExportCaption($this->StudentStatusID);
					if ($this->TahunID->Exportable) $Doc->ExportCaption($this->TahunID);
					if ($this->Foto->Exportable) $Doc->ExportCaption($this->Foto);
					if ($this->NIK->Exportable) $Doc->ExportCaption($this->NIK);
					if ($this->WargaNegara->Exportable) $Doc->ExportCaption($this->WargaNegara);
					if ($this->Kelamin->Exportable) $Doc->ExportCaption($this->Kelamin);
					if ($this->TempatLahir->Exportable) $Doc->ExportCaption($this->TempatLahir);
					if ($this->TanggalLahir->Exportable) $Doc->ExportCaption($this->TanggalLahir);
					if ($this->AgamaID->Exportable) $Doc->ExportCaption($this->AgamaID);
					if ($this->Darah->Exportable) $Doc->ExportCaption($this->Darah);
					if ($this->StatusSipil->Exportable) $Doc->ExportCaption($this->StatusSipil);
					if ($this->AlamatDomisili->Exportable) $Doc->ExportCaption($this->AlamatDomisili);
					if ($this->RT->Exportable) $Doc->ExportCaption($this->RT);
					if ($this->RW->Exportable) $Doc->ExportCaption($this->RW);
					if ($this->KodePos->Exportable) $Doc->ExportCaption($this->KodePos);
					if ($this->ProvinsiID->Exportable) $Doc->ExportCaption($this->ProvinsiID);
					if ($this->KabupatenKotaID->Exportable) $Doc->ExportCaption($this->KabupatenKotaID);
					if ($this->KecamatanID->Exportable) $Doc->ExportCaption($this->KecamatanID);
					if ($this->DesaID->Exportable) $Doc->ExportCaption($this->DesaID);
					if ($this->AnakKe->Exportable) $Doc->ExportCaption($this->AnakKe);
					if ($this->JumlahSaudara->Exportable) $Doc->ExportCaption($this->JumlahSaudara);
					if ($this->Telepon->Exportable) $Doc->ExportCaption($this->Telepon);
					if ($this->_Email->Exportable) $Doc->ExportCaption($this->_Email);
					if ($this->NamaAyah->Exportable) $Doc->ExportCaption($this->NamaAyah);
					if ($this->AgamaAyah->Exportable) $Doc->ExportCaption($this->AgamaAyah);
					if ($this->PendidikanAyah->Exportable) $Doc->ExportCaption($this->PendidikanAyah);
					if ($this->PekerjaanAyah->Exportable) $Doc->ExportCaption($this->PekerjaanAyah);
					if ($this->HidupAyah->Exportable) $Doc->ExportCaption($this->HidupAyah);
					if ($this->NamaIbu->Exportable) $Doc->ExportCaption($this->NamaIbu);
					if ($this->AgamaIbu->Exportable) $Doc->ExportCaption($this->AgamaIbu);
					if ($this->PendidikanIbu->Exportable) $Doc->ExportCaption($this->PendidikanIbu);
					if ($this->PekerjaanIbu->Exportable) $Doc->ExportCaption($this->PekerjaanIbu);
					if ($this->HidupIbu->Exportable) $Doc->ExportCaption($this->HidupIbu);
					if ($this->AlamatOrtu->Exportable) $Doc->ExportCaption($this->AlamatOrtu);
					if ($this->RTOrtu->Exportable) $Doc->ExportCaption($this->RTOrtu);
					if ($this->RWOrtu->Exportable) $Doc->ExportCaption($this->RWOrtu);
					if ($this->KodePosOrtu->Exportable) $Doc->ExportCaption($this->KodePosOrtu);
					if ($this->ProvinsiIDOrtu->Exportable) $Doc->ExportCaption($this->ProvinsiIDOrtu);
					if ($this->KabupatenIDOrtu->Exportable) $Doc->ExportCaption($this->KabupatenIDOrtu);
					if ($this->KecamatanIDOrtu->Exportable) $Doc->ExportCaption($this->KecamatanIDOrtu);
					if ($this->DesaIDOrtu->Exportable) $Doc->ExportCaption($this->DesaIDOrtu);
					if ($this->NegaraIDOrtu->Exportable) $Doc->ExportCaption($this->NegaraIDOrtu);
					if ($this->TeleponOrtu->Exportable) $Doc->ExportCaption($this->TeleponOrtu);
					if ($this->HandphoneOrtu->Exportable) $Doc->ExportCaption($this->HandphoneOrtu);
					if ($this->EmailOrtu->Exportable) $Doc->ExportCaption($this->EmailOrtu);
					if ($this->AsalSekolah->Exportable) $Doc->ExportCaption($this->AsalSekolah);
					if ($this->AlamatSekolah->Exportable) $Doc->ExportCaption($this->AlamatSekolah);
					if ($this->ProvinsiIDSekolah->Exportable) $Doc->ExportCaption($this->ProvinsiIDSekolah);
					if ($this->KabupatenIDSekolah->Exportable) $Doc->ExportCaption($this->KabupatenIDSekolah);
					if ($this->KecamatanIDSekolah->Exportable) $Doc->ExportCaption($this->KecamatanIDSekolah);
					if ($this->DesaIDSekolah->Exportable) $Doc->ExportCaption($this->DesaIDSekolah);
					if ($this->NilaiSekolah->Exportable) $Doc->ExportCaption($this->NilaiSekolah);
					if ($this->TahunLulus->Exportable) $Doc->ExportCaption($this->TahunLulus);
					if ($this->IjazahSekolah->Exportable) $Doc->ExportCaption($this->IjazahSekolah);
					if ($this->TglIjazah->Exportable) $Doc->ExportCaption($this->TglIjazah);
					if ($this->NA->Exportable) $Doc->ExportCaption($this->NA);
				} else {
					if ($this->StudentID->Exportable) $Doc->ExportCaption($this->StudentID);
					if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
					if ($this->LevelID->Exportable) $Doc->ExportCaption($this->LevelID);
					if ($this->KampusID->Exportable) $Doc->ExportCaption($this->KampusID);
					if ($this->ProdiID->Exportable) $Doc->ExportCaption($this->ProdiID);
					if ($this->StudentStatusID->Exportable) $Doc->ExportCaption($this->StudentStatusID);
					if ($this->TahunID->Exportable) $Doc->ExportCaption($this->TahunID);
					if ($this->Foto->Exportable) $Doc->ExportCaption($this->Foto);
					if ($this->NIK->Exportable) $Doc->ExportCaption($this->NIK);
					if ($this->WargaNegara->Exportable) $Doc->ExportCaption($this->WargaNegara);
					if ($this->Kelamin->Exportable) $Doc->ExportCaption($this->Kelamin);
					if ($this->TempatLahir->Exportable) $Doc->ExportCaption($this->TempatLahir);
					if ($this->TanggalLahir->Exportable) $Doc->ExportCaption($this->TanggalLahir);
					if ($this->AgamaID->Exportable) $Doc->ExportCaption($this->AgamaID);
					if ($this->Darah->Exportable) $Doc->ExportCaption($this->Darah);
					if ($this->StatusSipil->Exportable) $Doc->ExportCaption($this->StatusSipil);
					if ($this->AlamatDomisili->Exportable) $Doc->ExportCaption($this->AlamatDomisili);
					if ($this->RT->Exportable) $Doc->ExportCaption($this->RT);
					if ($this->RW->Exportable) $Doc->ExportCaption($this->RW);
					if ($this->KodePos->Exportable) $Doc->ExportCaption($this->KodePos);
					if ($this->ProvinsiID->Exportable) $Doc->ExportCaption($this->ProvinsiID);
					if ($this->KabupatenKotaID->Exportable) $Doc->ExportCaption($this->KabupatenKotaID);
					if ($this->KecamatanID->Exportable) $Doc->ExportCaption($this->KecamatanID);
					if ($this->DesaID->Exportable) $Doc->ExportCaption($this->DesaID);
					if ($this->AnakKe->Exportable) $Doc->ExportCaption($this->AnakKe);
					if ($this->JumlahSaudara->Exportable) $Doc->ExportCaption($this->JumlahSaudara);
					if ($this->Telepon->Exportable) $Doc->ExportCaption($this->Telepon);
					if ($this->_Email->Exportable) $Doc->ExportCaption($this->_Email);
					if ($this->NamaAyah->Exportable) $Doc->ExportCaption($this->NamaAyah);
					if ($this->AgamaAyah->Exportable) $Doc->ExportCaption($this->AgamaAyah);
					if ($this->PendidikanAyah->Exportable) $Doc->ExportCaption($this->PendidikanAyah);
					if ($this->PekerjaanAyah->Exportable) $Doc->ExportCaption($this->PekerjaanAyah);
					if ($this->HidupAyah->Exportable) $Doc->ExportCaption($this->HidupAyah);
					if ($this->NamaIbu->Exportable) $Doc->ExportCaption($this->NamaIbu);
					if ($this->AgamaIbu->Exportable) $Doc->ExportCaption($this->AgamaIbu);
					if ($this->PendidikanIbu->Exportable) $Doc->ExportCaption($this->PendidikanIbu);
					if ($this->PekerjaanIbu->Exportable) $Doc->ExportCaption($this->PekerjaanIbu);
					if ($this->HidupIbu->Exportable) $Doc->ExportCaption($this->HidupIbu);
					if ($this->AlamatOrtu->Exportable) $Doc->ExportCaption($this->AlamatOrtu);
					if ($this->RTOrtu->Exportable) $Doc->ExportCaption($this->RTOrtu);
					if ($this->RWOrtu->Exportable) $Doc->ExportCaption($this->RWOrtu);
					if ($this->KodePosOrtu->Exportable) $Doc->ExportCaption($this->KodePosOrtu);
					if ($this->ProvinsiIDOrtu->Exportable) $Doc->ExportCaption($this->ProvinsiIDOrtu);
					if ($this->KabupatenIDOrtu->Exportable) $Doc->ExportCaption($this->KabupatenIDOrtu);
					if ($this->KecamatanIDOrtu->Exportable) $Doc->ExportCaption($this->KecamatanIDOrtu);
					if ($this->DesaIDOrtu->Exportable) $Doc->ExportCaption($this->DesaIDOrtu);
					if ($this->NegaraIDOrtu->Exportable) $Doc->ExportCaption($this->NegaraIDOrtu);
					if ($this->TeleponOrtu->Exportable) $Doc->ExportCaption($this->TeleponOrtu);
					if ($this->HandphoneOrtu->Exportable) $Doc->ExportCaption($this->HandphoneOrtu);
					if ($this->EmailOrtu->Exportable) $Doc->ExportCaption($this->EmailOrtu);
					if ($this->AsalSekolah->Exportable) $Doc->ExportCaption($this->AsalSekolah);
					if ($this->AlamatSekolah->Exportable) $Doc->ExportCaption($this->AlamatSekolah);
					if ($this->ProvinsiIDSekolah->Exportable) $Doc->ExportCaption($this->ProvinsiIDSekolah);
					if ($this->KabupatenIDSekolah->Exportable) $Doc->ExportCaption($this->KabupatenIDSekolah);
					if ($this->KecamatanIDSekolah->Exportable) $Doc->ExportCaption($this->KecamatanIDSekolah);
					if ($this->DesaIDSekolah->Exportable) $Doc->ExportCaption($this->DesaIDSekolah);
					if ($this->NilaiSekolah->Exportable) $Doc->ExportCaption($this->NilaiSekolah);
					if ($this->TahunLulus->Exportable) $Doc->ExportCaption($this->TahunLulus);
					if ($this->IjazahSekolah->Exportable) $Doc->ExportCaption($this->IjazahSekolah);
					if ($this->TglIjazah->Exportable) $Doc->ExportCaption($this->TglIjazah);
					if ($this->NA->Exportable) $Doc->ExportCaption($this->NA);
				}
				$Doc->EndExportRow();
			}
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				if (!$Doc->ExportCustom) {
					$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
					if ($ExportPageType == "view") {
						if ($this->StudentID->Exportable) $Doc->ExportField($this->StudentID);
						if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
						if ($this->LevelID->Exportable) $Doc->ExportField($this->LevelID);
						if ($this->KampusID->Exportable) $Doc->ExportField($this->KampusID);
						if ($this->ProdiID->Exportable) $Doc->ExportField($this->ProdiID);
						if ($this->StudentStatusID->Exportable) $Doc->ExportField($this->StudentStatusID);
						if ($this->TahunID->Exportable) $Doc->ExportField($this->TahunID);
						if ($this->Foto->Exportable) $Doc->ExportField($this->Foto);
						if ($this->NIK->Exportable) $Doc->ExportField($this->NIK);
						if ($this->WargaNegara->Exportable) $Doc->ExportField($this->WargaNegara);
						if ($this->Kelamin->Exportable) $Doc->ExportField($this->Kelamin);
						if ($this->TempatLahir->Exportable) $Doc->ExportField($this->TempatLahir);
						if ($this->TanggalLahir->Exportable) $Doc->ExportField($this->TanggalLahir);
						if ($this->AgamaID->Exportable) $Doc->ExportField($this->AgamaID);
						if ($this->Darah->Exportable) $Doc->ExportField($this->Darah);
						if ($this->StatusSipil->Exportable) $Doc->ExportField($this->StatusSipil);
						if ($this->AlamatDomisili->Exportable) $Doc->ExportField($this->AlamatDomisili);
						if ($this->RT->Exportable) $Doc->ExportField($this->RT);
						if ($this->RW->Exportable) $Doc->ExportField($this->RW);
						if ($this->KodePos->Exportable) $Doc->ExportField($this->KodePos);
						if ($this->ProvinsiID->Exportable) $Doc->ExportField($this->ProvinsiID);
						if ($this->KabupatenKotaID->Exportable) $Doc->ExportField($this->KabupatenKotaID);
						if ($this->KecamatanID->Exportable) $Doc->ExportField($this->KecamatanID);
						if ($this->DesaID->Exportable) $Doc->ExportField($this->DesaID);
						if ($this->AnakKe->Exportable) $Doc->ExportField($this->AnakKe);
						if ($this->JumlahSaudara->Exportable) $Doc->ExportField($this->JumlahSaudara);
						if ($this->Telepon->Exportable) $Doc->ExportField($this->Telepon);
						if ($this->_Email->Exportable) $Doc->ExportField($this->_Email);
						if ($this->NamaAyah->Exportable) $Doc->ExportField($this->NamaAyah);
						if ($this->AgamaAyah->Exportable) $Doc->ExportField($this->AgamaAyah);
						if ($this->PendidikanAyah->Exportable) $Doc->ExportField($this->PendidikanAyah);
						if ($this->PekerjaanAyah->Exportable) $Doc->ExportField($this->PekerjaanAyah);
						if ($this->HidupAyah->Exportable) $Doc->ExportField($this->HidupAyah);
						if ($this->NamaIbu->Exportable) $Doc->ExportField($this->NamaIbu);
						if ($this->AgamaIbu->Exportable) $Doc->ExportField($this->AgamaIbu);
						if ($this->PendidikanIbu->Exportable) $Doc->ExportField($this->PendidikanIbu);
						if ($this->PekerjaanIbu->Exportable) $Doc->ExportField($this->PekerjaanIbu);
						if ($this->HidupIbu->Exportable) $Doc->ExportField($this->HidupIbu);
						if ($this->AlamatOrtu->Exportable) $Doc->ExportField($this->AlamatOrtu);
						if ($this->RTOrtu->Exportable) $Doc->ExportField($this->RTOrtu);
						if ($this->RWOrtu->Exportable) $Doc->ExportField($this->RWOrtu);
						if ($this->KodePosOrtu->Exportable) $Doc->ExportField($this->KodePosOrtu);
						if ($this->ProvinsiIDOrtu->Exportable) $Doc->ExportField($this->ProvinsiIDOrtu);
						if ($this->KabupatenIDOrtu->Exportable) $Doc->ExportField($this->KabupatenIDOrtu);
						if ($this->KecamatanIDOrtu->Exportable) $Doc->ExportField($this->KecamatanIDOrtu);
						if ($this->DesaIDOrtu->Exportable) $Doc->ExportField($this->DesaIDOrtu);
						if ($this->NegaraIDOrtu->Exportable) $Doc->ExportField($this->NegaraIDOrtu);
						if ($this->TeleponOrtu->Exportable) $Doc->ExportField($this->TeleponOrtu);
						if ($this->HandphoneOrtu->Exportable) $Doc->ExportField($this->HandphoneOrtu);
						if ($this->EmailOrtu->Exportable) $Doc->ExportField($this->EmailOrtu);
						if ($this->AsalSekolah->Exportable) $Doc->ExportField($this->AsalSekolah);
						if ($this->AlamatSekolah->Exportable) $Doc->ExportField($this->AlamatSekolah);
						if ($this->ProvinsiIDSekolah->Exportable) $Doc->ExportField($this->ProvinsiIDSekolah);
						if ($this->KabupatenIDSekolah->Exportable) $Doc->ExportField($this->KabupatenIDSekolah);
						if ($this->KecamatanIDSekolah->Exportable) $Doc->ExportField($this->KecamatanIDSekolah);
						if ($this->DesaIDSekolah->Exportable) $Doc->ExportField($this->DesaIDSekolah);
						if ($this->NilaiSekolah->Exportable) $Doc->ExportField($this->NilaiSekolah);
						if ($this->TahunLulus->Exportable) $Doc->ExportField($this->TahunLulus);
						if ($this->IjazahSekolah->Exportable) $Doc->ExportField($this->IjazahSekolah);
						if ($this->TglIjazah->Exportable) $Doc->ExportField($this->TglIjazah);
						if ($this->NA->Exportable) $Doc->ExportField($this->NA);
					} else {
						if ($this->StudentID->Exportable) $Doc->ExportField($this->StudentID);
						if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
						if ($this->LevelID->Exportable) $Doc->ExportField($this->LevelID);
						if ($this->KampusID->Exportable) $Doc->ExportField($this->KampusID);
						if ($this->ProdiID->Exportable) $Doc->ExportField($this->ProdiID);
						if ($this->StudentStatusID->Exportable) $Doc->ExportField($this->StudentStatusID);
						if ($this->TahunID->Exportable) $Doc->ExportField($this->TahunID);
						if ($this->Foto->Exportable) $Doc->ExportField($this->Foto);
						if ($this->NIK->Exportable) $Doc->ExportField($this->NIK);
						if ($this->WargaNegara->Exportable) $Doc->ExportField($this->WargaNegara);
						if ($this->Kelamin->Exportable) $Doc->ExportField($this->Kelamin);
						if ($this->TempatLahir->Exportable) $Doc->ExportField($this->TempatLahir);
						if ($this->TanggalLahir->Exportable) $Doc->ExportField($this->TanggalLahir);
						if ($this->AgamaID->Exportable) $Doc->ExportField($this->AgamaID);
						if ($this->Darah->Exportable) $Doc->ExportField($this->Darah);
						if ($this->StatusSipil->Exportable) $Doc->ExportField($this->StatusSipil);
						if ($this->AlamatDomisili->Exportable) $Doc->ExportField($this->AlamatDomisili);
						if ($this->RT->Exportable) $Doc->ExportField($this->RT);
						if ($this->RW->Exportable) $Doc->ExportField($this->RW);
						if ($this->KodePos->Exportable) $Doc->ExportField($this->KodePos);
						if ($this->ProvinsiID->Exportable) $Doc->ExportField($this->ProvinsiID);
						if ($this->KabupatenKotaID->Exportable) $Doc->ExportField($this->KabupatenKotaID);
						if ($this->KecamatanID->Exportable) $Doc->ExportField($this->KecamatanID);
						if ($this->DesaID->Exportable) $Doc->ExportField($this->DesaID);
						if ($this->AnakKe->Exportable) $Doc->ExportField($this->AnakKe);
						if ($this->JumlahSaudara->Exportable) $Doc->ExportField($this->JumlahSaudara);
						if ($this->Telepon->Exportable) $Doc->ExportField($this->Telepon);
						if ($this->_Email->Exportable) $Doc->ExportField($this->_Email);
						if ($this->NamaAyah->Exportable) $Doc->ExportField($this->NamaAyah);
						if ($this->AgamaAyah->Exportable) $Doc->ExportField($this->AgamaAyah);
						if ($this->PendidikanAyah->Exportable) $Doc->ExportField($this->PendidikanAyah);
						if ($this->PekerjaanAyah->Exportable) $Doc->ExportField($this->PekerjaanAyah);
						if ($this->HidupAyah->Exportable) $Doc->ExportField($this->HidupAyah);
						if ($this->NamaIbu->Exportable) $Doc->ExportField($this->NamaIbu);
						if ($this->AgamaIbu->Exportable) $Doc->ExportField($this->AgamaIbu);
						if ($this->PendidikanIbu->Exportable) $Doc->ExportField($this->PendidikanIbu);
						if ($this->PekerjaanIbu->Exportable) $Doc->ExportField($this->PekerjaanIbu);
						if ($this->HidupIbu->Exportable) $Doc->ExportField($this->HidupIbu);
						if ($this->AlamatOrtu->Exportable) $Doc->ExportField($this->AlamatOrtu);
						if ($this->RTOrtu->Exportable) $Doc->ExportField($this->RTOrtu);
						if ($this->RWOrtu->Exportable) $Doc->ExportField($this->RWOrtu);
						if ($this->KodePosOrtu->Exportable) $Doc->ExportField($this->KodePosOrtu);
						if ($this->ProvinsiIDOrtu->Exportable) $Doc->ExportField($this->ProvinsiIDOrtu);
						if ($this->KabupatenIDOrtu->Exportable) $Doc->ExportField($this->KabupatenIDOrtu);
						if ($this->KecamatanIDOrtu->Exportable) $Doc->ExportField($this->KecamatanIDOrtu);
						if ($this->DesaIDOrtu->Exportable) $Doc->ExportField($this->DesaIDOrtu);
						if ($this->NegaraIDOrtu->Exportable) $Doc->ExportField($this->NegaraIDOrtu);
						if ($this->TeleponOrtu->Exportable) $Doc->ExportField($this->TeleponOrtu);
						if ($this->HandphoneOrtu->Exportable) $Doc->ExportField($this->HandphoneOrtu);
						if ($this->EmailOrtu->Exportable) $Doc->ExportField($this->EmailOrtu);
						if ($this->AsalSekolah->Exportable) $Doc->ExportField($this->AsalSekolah);
						if ($this->AlamatSekolah->Exportable) $Doc->ExportField($this->AlamatSekolah);
						if ($this->ProvinsiIDSekolah->Exportable) $Doc->ExportField($this->ProvinsiIDSekolah);
						if ($this->KabupatenIDSekolah->Exportable) $Doc->ExportField($this->KabupatenIDSekolah);
						if ($this->KecamatanIDSekolah->Exportable) $Doc->ExportField($this->KecamatanIDSekolah);
						if ($this->DesaIDSekolah->Exportable) $Doc->ExportField($this->DesaIDSekolah);
						if ($this->NilaiSekolah->Exportable) $Doc->ExportField($this->NilaiSekolah);
						if ($this->TahunLulus->Exportable) $Doc->ExportField($this->TahunLulus);
						if ($this->IjazahSekolah->Exportable) $Doc->ExportField($this->IjazahSekolah);
						if ($this->TglIjazah->Exportable) $Doc->ExportField($this->TglIjazah);
						if ($this->NA->Exportable) $Doc->ExportField($this->NA);
					}
					$Doc->EndExportRow();
				}
			}

			// Call Row Export server event
			if ($Doc->ExportCustom)
				$this->Row_Export($Recordset->fields);
			$Recordset->MoveNext();
		}
		if (!$Doc->ExportCustom) {
			$Doc->ExportTableFooter();
		}
	}

	// Get auto fill value
	function GetAutoFill($id, $val) {
		$rsarr = array();
		$rowcnt = 0;

		// Output
		if (is_array($rsarr) && $rowcnt > 0) {
			$fldcnt = count($rsarr[0]);
			for ($i = 0; $i < $rowcnt; $i++) {
				for ($j = 0; $j < $fldcnt; $j++) {
					$str = strval($rsarr[$i][$j]);
					$str = ew_ConvertToUtf8($str);
					if (isset($post["keepCRLF"])) {
						$str = str_replace(array("\r", "\n"), array("\\r", "\\n"), $str);
					} else {
						$str = str_replace(array("\r", "\n"), array(" ", " "), $str);
					}
					$rsarr[$i][$j] = $str;
				}
			}
			return ew_ArrayToJson($rsarr);
		} else {
			return FALSE;
		}
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'student';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		global $Language;
		if (!$this->AuditTrailOnAdd) return;
		$table = 'student';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['StudentID'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
		$usr = CurrentUserName();
		foreach (array_keys($rs) as $fldname) {
			if (array_key_exists($fldname, $this->fields) && $this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldHtmlTag == "PASSWORD") {
					$newvalue = $Language->Phrase("PasswordMask"); // Password Field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$newvalue = $rs[$fldname];
					else
						$newvalue = "[MEMO]"; // Memo Field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$newvalue = "[XML]"; // XML Field
				} else {
					$newvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $usr, "A", $table, $fldname, $key, "", $newvalue);
			}
		}
	}

	// Write Audit Trail (edit page)
	function WriteAuditTrailOnEdit(&$rsold, &$rsnew) {
		global $Language;
		if (!$this->AuditTrailOnEdit) return;
		$table = 'student';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['StudentID'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
		$usr = CurrentUserName();
		foreach (array_keys($rsnew) as $fldname) {
			if (array_key_exists($fldname, $this->fields) && array_key_exists($fldname, $rsold) && $this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_DATE) { // DateTime field
					$modified = (ew_FormatDateTime($rsold[$fldname], 0) <> ew_FormatDateTime($rsnew[$fldname], 0));
				} else {
					$modified = !ew_CompareValue($rsold[$fldname], $rsnew[$fldname]);
				}
				if ($modified) {
					if ($this->fields[$fldname]->FldHtmlTag == "PASSWORD") { // Password Field
						$oldvalue = $Language->Phrase("PasswordMask");
						$newvalue = $Language->Phrase("PasswordMask");
					} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) { // Memo field
						if (EW_AUDIT_TRAIL_TO_DATABASE) {
							$oldvalue = $rsold[$fldname];
							$newvalue = $rsnew[$fldname];
						} else {
							$oldvalue = "[MEMO]";
							$newvalue = "[MEMO]";
						}
					} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) { // XML field
						$oldvalue = "[XML]";
						$newvalue = "[XML]";
					} else {
						$oldvalue = $rsold[$fldname];
						$newvalue = $rsnew[$fldname];
					}
					ew_WriteAuditTrail("log", $dt, $id, $usr, "U", $table, $fldname, $key, $oldvalue, $newvalue);
				}
			}
		}
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		global $Language;
		if (!$this->AuditTrailOnDelete) return;
		$table = 'student';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['StudentID'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
		$curUser = CurrentUserName();
		foreach (array_keys($rs) as $fldname) {
			if (array_key_exists($fldname, $this->fields) && $this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldHtmlTag == "PASSWORD") {
					$oldvalue = $Language->Phrase("PasswordMask"); // Password Field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$oldvalue = $rs[$fldname];
					else
						$oldvalue = "[MEMO]"; // Memo field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$oldvalue = "[XML]"; // XML field
				} else {
					$oldvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $curUser, "D", $table, $fldname, $key, $oldvalue, "");
			}
		}
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Grid Inserting event
	function Grid_Inserting() {

		// Enter your code here
		// To reject grid insert, set return value to FALSE

		return TRUE;
	}

	// Grid Inserted event
	function Grid_Inserted($rsnew) {

		//echo "Grid Inserted";
	}

	// Grid Updating event
	function Grid_Updating($rsold) {

		// Enter your code here
		// To reject grid update, set return value to FALSE

		return TRUE;
	}

	// Grid Updated event
	function Grid_Updated($rsold, $rsnew) {

		//echo "Grid Updated";
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		//var_dump($fld->FldName, $fld->LookupFilters, $filter); // Uncomment to view the filter
		// Enter your code here

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
