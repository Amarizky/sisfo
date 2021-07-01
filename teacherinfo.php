<?php

// Global variable for table object
$teacher = NULL;

//
// Table class for teacher
//
class cteacher extends cTable {
	var $AuditTrailOnAdd = TRUE;
	var $AuditTrailOnEdit = TRUE;
	var $AuditTrailOnDelete = TRUE;
	var $AuditTrailOnView = FALSE;
	var $AuditTrailOnViewData = FALSE;
	var $AuditTrailOnSearch = FALSE;
	var $TeacherID;
	var $NIPPNS;
	var $Nama;
	var $Gelar;
	var $LevelID;
	var $Password;
	var $AliasCode;
	var $KTP;
	var $TempatLahir;
	var $TanggalLahir;
	var $AgamaID;
	var $KelaminID;
	var $Telephone;
	var $Handphone;
	var $_Email;
	var $Alamat;
	var $KodePos;
	var $ProvinsiID;
	var $KabupatenKotaID;
	var $KecamatanID;
	var $DesaID;
	var $InstitusiInduk;
	var $IkatanID;
	var $GolonganID;
	var $StatusKerjaID;
	var $TglBekerja;
	var $Homebase;
	var $ProdiID;
	var $Keilmuan;
	var $LulusanPT;
	var $NamaBank;
	var $NamaAkun;
	var $NomerAkun;
	var $Creator;
	var $CreateDate;
	var $Editor;
	var $EditDate;
	var $NA;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'teacher';
		$this->TableName = 'teacher';
		$this->TableType = 'TABLE';

		// Update Table
		$this->UpdateTable = "`teacher`";
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

		// TeacherID
		$this->TeacherID = new cField('teacher', 'teacher', 'x_TeacherID', 'TeacherID', '`TeacherID`', '`TeacherID`', 200, -1, FALSE, '`TeacherID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TeacherID->Sortable = TRUE; // Allow sort
		$this->fields['TeacherID'] = &$this->TeacherID;

		// NIPPNS
		$this->NIPPNS = new cField('teacher', 'teacher', 'x_NIPPNS', 'NIPPNS', '`NIPPNS`', '`NIPPNS`', 200, -1, FALSE, '`NIPPNS`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NIPPNS->Sortable = TRUE; // Allow sort
		$this->fields['NIPPNS'] = &$this->NIPPNS;

		// Nama
		$this->Nama = new cField('teacher', 'teacher', 'x_Nama', 'Nama', '`Nama`', '`Nama`', 200, -1, FALSE, '`Nama`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Nama->Sortable = TRUE; // Allow sort
		$this->fields['Nama'] = &$this->Nama;

		// Gelar
		$this->Gelar = new cField('teacher', 'teacher', 'x_Gelar', 'Gelar', '`Gelar`', '`Gelar`', 200, -1, FALSE, '`Gelar`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Gelar->Sortable = TRUE; // Allow sort
		$this->fields['Gelar'] = &$this->Gelar;

		// LevelID
		$this->LevelID = new cField('teacher', 'teacher', 'x_LevelID', 'LevelID', '`LevelID`', '`LevelID`', 200, -1, FALSE, '`LevelID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->LevelID->Sortable = TRUE; // Allow sort
		$this->LevelID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['LevelID'] = &$this->LevelID;

		// Password
		$this->Password = new cField('teacher', 'teacher', 'x_Password', 'Password', '`Password`', '`Password`', 200, -1, FALSE, '`Password`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'PASSWORD');
		$this->Password->Sortable = TRUE; // Allow sort
		$this->fields['Password'] = &$this->Password;

		// AliasCode
		$this->AliasCode = new cField('teacher', 'teacher', 'x_AliasCode', 'AliasCode', '`AliasCode`', '`AliasCode`', 200, -1, FALSE, '`AliasCode`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->AliasCode->Sortable = TRUE; // Allow sort
		$this->fields['AliasCode'] = &$this->AliasCode;

		// KTP
		$this->KTP = new cField('teacher', 'teacher', 'x_KTP', 'KTP', '`KTP`', '`KTP`', 200, -1, FALSE, '`KTP`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->KTP->Sortable = TRUE; // Allow sort
		$this->fields['KTP'] = &$this->KTP;

		// TempatLahir
		$this->TempatLahir = new cField('teacher', 'teacher', 'x_TempatLahir', 'TempatLahir', '`TempatLahir`', '`TempatLahir`', 200, -1, FALSE, '`TempatLahir`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TempatLahir->Sortable = TRUE; // Allow sort
		$this->fields['TempatLahir'] = &$this->TempatLahir;

		// TanggalLahir
		$this->TanggalLahir = new cField('teacher', 'teacher', 'x_TanggalLahir', 'TanggalLahir', '`TanggalLahir`', ew_CastDateFieldForLike('`TanggalLahir`', 0, "DB"), 133, 0, FALSE, '`TanggalLahir`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TanggalLahir->Sortable = TRUE; // Allow sort
		$this->TanggalLahir->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['TanggalLahir'] = &$this->TanggalLahir;

		// AgamaID
		$this->AgamaID = new cField('teacher', 'teacher', 'x_AgamaID', 'AgamaID', '`AgamaID`', '`AgamaID`', 3, -1, FALSE, '`AgamaID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->AgamaID->Sortable = TRUE; // Allow sort
		$this->AgamaID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->AgamaID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->AgamaID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['AgamaID'] = &$this->AgamaID;

		// KelaminID
		$this->KelaminID = new cField('teacher', 'teacher', 'x_KelaminID', 'KelaminID', '`KelaminID`', '`KelaminID`', 202, -1, FALSE, '`KelaminID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->KelaminID->Sortable = TRUE; // Allow sort
		$this->fields['KelaminID'] = &$this->KelaminID;

		// Telephone
		$this->Telephone = new cField('teacher', 'teacher', 'x_Telephone', 'Telephone', '`Telephone`', '`Telephone`', 200, -1, FALSE, '`Telephone`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Telephone->Sortable = TRUE; // Allow sort
		$this->fields['Telephone'] = &$this->Telephone;

		// Handphone
		$this->Handphone = new cField('teacher', 'teacher', 'x_Handphone', 'Handphone', '`Handphone`', '`Handphone`', 200, -1, FALSE, '`Handphone`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Handphone->Sortable = TRUE; // Allow sort
		$this->fields['Handphone'] = &$this->Handphone;

		// Email
		$this->_Email = new cField('teacher', 'teacher', 'x__Email', 'Email', '`Email`', '`Email`', 200, -1, FALSE, '`Email`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->_Email->Sortable = TRUE; // Allow sort
		$this->_Email->FldDefaultErrMsg = $Language->Phrase("IncorrectEmail");
		$this->fields['Email'] = &$this->_Email;

		// Alamat
		$this->Alamat = new cField('teacher', 'teacher', 'x_Alamat', 'Alamat', '`Alamat`', '`Alamat`', 201, -1, FALSE, '`Alamat`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXTAREA');
		$this->Alamat->Sortable = TRUE; // Allow sort
		$this->fields['Alamat'] = &$this->Alamat;

		// KodePos
		$this->KodePos = new cField('teacher', 'teacher', 'x_KodePos', 'KodePos', '`KodePos`', '`KodePos`', 200, -1, FALSE, '`KodePos`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->KodePos->Sortable = TRUE; // Allow sort
		$this->fields['KodePos'] = &$this->KodePos;

		// ProvinsiID
		$this->ProvinsiID = new cField('teacher', 'teacher', 'x_ProvinsiID', 'ProvinsiID', '`ProvinsiID`', '`ProvinsiID`', 200, -1, FALSE, '`ProvinsiID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->ProvinsiID->Sortable = TRUE; // Allow sort
		$this->ProvinsiID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->ProvinsiID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['ProvinsiID'] = &$this->ProvinsiID;

		// KabupatenKotaID
		$this->KabupatenKotaID = new cField('teacher', 'teacher', 'x_KabupatenKotaID', 'KabupatenKotaID', '`KabupatenKotaID`', '`KabupatenKotaID`', 200, -1, FALSE, '`KabupatenKotaID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KabupatenKotaID->Sortable = TRUE; // Allow sort
		$this->KabupatenKotaID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KabupatenKotaID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['KabupatenKotaID'] = &$this->KabupatenKotaID;

		// KecamatanID
		$this->KecamatanID = new cField('teacher', 'teacher', 'x_KecamatanID', 'KecamatanID', '`KecamatanID`', '`KecamatanID`', 200, -1, FALSE, '`KecamatanID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KecamatanID->Sortable = TRUE; // Allow sort
		$this->KecamatanID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KecamatanID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['KecamatanID'] = &$this->KecamatanID;

		// DesaID
		$this->DesaID = new cField('teacher', 'teacher', 'x_DesaID', 'DesaID', '`DesaID`', '`DesaID`', 200, -1, FALSE, '`DesaID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->DesaID->Sortable = TRUE; // Allow sort
		$this->DesaID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->DesaID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['DesaID'] = &$this->DesaID;

		// InstitusiInduk
		$this->InstitusiInduk = new cField('teacher', 'teacher', 'x_InstitusiInduk', 'InstitusiInduk', '`InstitusiInduk`', '`InstitusiInduk`', 200, -1, FALSE, '`InstitusiInduk`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->InstitusiInduk->Sortable = TRUE; // Allow sort
		$this->InstitusiInduk->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->InstitusiInduk->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['InstitusiInduk'] = &$this->InstitusiInduk;

		// IkatanID
		$this->IkatanID = new cField('teacher', 'teacher', 'x_IkatanID', 'IkatanID', '`IkatanID`', '`IkatanID`', 200, -1, FALSE, '`IkatanID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->IkatanID->Sortable = TRUE; // Allow sort
		$this->IkatanID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->IkatanID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['IkatanID'] = &$this->IkatanID;

		// GolonganID
		$this->GolonganID = new cField('teacher', 'teacher', 'x_GolonganID', 'GolonganID', '`GolonganID`', '`GolonganID`', 200, -1, FALSE, '`GolonganID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->GolonganID->Sortable = TRUE; // Allow sort
		$this->GolonganID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->GolonganID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['GolonganID'] = &$this->GolonganID;

		// StatusKerjaID
		$this->StatusKerjaID = new cField('teacher', 'teacher', 'x_StatusKerjaID', 'StatusKerjaID', '`StatusKerjaID`', '`StatusKerjaID`', 200, -1, FALSE, '`StatusKerjaID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->StatusKerjaID->Sortable = TRUE; // Allow sort
		$this->StatusKerjaID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->StatusKerjaID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['StatusKerjaID'] = &$this->StatusKerjaID;

		// TglBekerja
		$this->TglBekerja = new cField('teacher', 'teacher', 'x_TglBekerja', 'TglBekerja', '`TglBekerja`', ew_CastDateFieldForLike('`TglBekerja`', 0, "DB"), 135, 0, FALSE, '`TglBekerja`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TglBekerja->Sortable = TRUE; // Allow sort
		$this->TglBekerja->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['TglBekerja'] = &$this->TglBekerja;

		// Homebase
		$this->Homebase = new cField('teacher', 'teacher', 'x_Homebase', 'Homebase', '`Homebase`', '`Homebase`', 200, -1, FALSE, '`Homebase`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->Homebase->Sortable = TRUE; // Allow sort
		$this->Homebase->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->Homebase->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['Homebase'] = &$this->Homebase;

		// ProdiID
		$this->ProdiID = new cField('teacher', 'teacher', 'x_ProdiID', 'ProdiID', '`ProdiID`', '`ProdiID`', 200, -1, FALSE, '`ProdiID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'CHECKBOX');
		$this->ProdiID->Sortable = TRUE; // Allow sort
		$this->fields['ProdiID'] = &$this->ProdiID;

		// Keilmuan
		$this->Keilmuan = new cField('teacher', 'teacher', 'x_Keilmuan', 'Keilmuan', '`Keilmuan`', '`Keilmuan`', 200, -1, FALSE, '`Keilmuan`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Keilmuan->Sortable = TRUE; // Allow sort
		$this->fields['Keilmuan'] = &$this->Keilmuan;

		// LulusanPT
		$this->LulusanPT = new cField('teacher', 'teacher', 'x_LulusanPT', 'LulusanPT', '`LulusanPT`', '`LulusanPT`', 200, -1, FALSE, '`LulusanPT`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->LulusanPT->Sortable = TRUE; // Allow sort
		$this->fields['LulusanPT'] = &$this->LulusanPT;

		// NamaBank
		$this->NamaBank = new cField('teacher', 'teacher', 'x_NamaBank', 'NamaBank', '`NamaBank`', '`NamaBank`', 200, -1, FALSE, '`NamaBank`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NamaBank->Sortable = TRUE; // Allow sort
		$this->fields['NamaBank'] = &$this->NamaBank;

		// NamaAkun
		$this->NamaAkun = new cField('teacher', 'teacher', 'x_NamaAkun', 'NamaAkun', '`NamaAkun`', '`NamaAkun`', 200, -1, FALSE, '`NamaAkun`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NamaAkun->Sortable = TRUE; // Allow sort
		$this->fields['NamaAkun'] = &$this->NamaAkun;

		// NomerAkun
		$this->NomerAkun = new cField('teacher', 'teacher', 'x_NomerAkun', 'NomerAkun', '`NomerAkun`', '`NomerAkun`', 200, -1, FALSE, '`NomerAkun`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NomerAkun->Sortable = TRUE; // Allow sort
		$this->fields['NomerAkun'] = &$this->NomerAkun;

		// Creator
		$this->Creator = new cField('teacher', 'teacher', 'x_Creator', 'Creator', '`Creator`', '`Creator`', 200, -1, FALSE, '`Creator`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Creator->Sortable = TRUE; // Allow sort
		$this->fields['Creator'] = &$this->Creator;

		// CreateDate
		$this->CreateDate = new cField('teacher', 'teacher', 'x_CreateDate', 'CreateDate', '`CreateDate`', ew_CastDateFieldForLike('`CreateDate`', 0, "DB"), 135, 0, FALSE, '`CreateDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->CreateDate->Sortable = TRUE; // Allow sort
		$this->CreateDate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['CreateDate'] = &$this->CreateDate;

		// Editor
		$this->Editor = new cField('teacher', 'teacher', 'x_Editor', 'Editor', '`Editor`', '`Editor`', 200, -1, FALSE, '`Editor`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Editor->Sortable = TRUE; // Allow sort
		$this->fields['Editor'] = &$this->Editor;

		// EditDate
		$this->EditDate = new cField('teacher', 'teacher', 'x_EditDate', 'EditDate', '`EditDate`', ew_CastDateFieldForLike('`EditDate`', 0, "DB"), 135, 0, FALSE, '`EditDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->EditDate->Sortable = TRUE; // Allow sort
		$this->EditDate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['EditDate'] = &$this->EditDate;

		// NA
		$this->NA = new cField('teacher', 'teacher', 'x_NA', 'NA', '`NA`', '`NA`', 202, -1, FALSE, '`NA`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`teacher`";
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
			$fldname = 'TeacherID';
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
			if (array_key_exists('TeacherID', $rs))
				ew_AddFilter($where, ew_QuotedName('TeacherID', $this->DBID) . '=' . ew_QuotedValue($rs['TeacherID'], $this->TeacherID->FldDataType, $this->DBID));
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
		return "`TeacherID` = '@TeacherID@'";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		$sKeyFilter = str_replace("@TeacherID@", ew_AdjustSql($this->TeacherID->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
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
			return "teacherlist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "teacherlist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("teacherview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("teacherview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "teacheradd.php?" . $this->UrlParm($parm);
		else
			$url = "teacheradd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("teacheredit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("teacheradd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("teacherdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "TeacherID:" . ew_VarToJson($this->TeacherID->CurrentValue, "string", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->TeacherID->CurrentValue)) {
			$sUrl .= "TeacherID=" . urlencode($this->TeacherID->CurrentValue);
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
			if ($isPost && isset($_POST["TeacherID"]))
				$arKeys[] = ew_StripSlashes($_POST["TeacherID"]);
			elseif (isset($_GET["TeacherID"]))
				$arKeys[] = ew_StripSlashes($_GET["TeacherID"]);
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
			$this->TeacherID->CurrentValue = $key;
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
		$this->TeacherID->setDbValue($rs->fields('TeacherID'));
		$this->NIPPNS->setDbValue($rs->fields('NIPPNS'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->Gelar->setDbValue($rs->fields('Gelar'));
		$this->LevelID->setDbValue($rs->fields('LevelID'));
		$this->Password->setDbValue($rs->fields('Password'));
		$this->AliasCode->setDbValue($rs->fields('AliasCode'));
		$this->KTP->setDbValue($rs->fields('KTP'));
		$this->TempatLahir->setDbValue($rs->fields('TempatLahir'));
		$this->TanggalLahir->setDbValue($rs->fields('TanggalLahir'));
		$this->AgamaID->setDbValue($rs->fields('AgamaID'));
		$this->KelaminID->setDbValue($rs->fields('KelaminID'));
		$this->Telephone->setDbValue($rs->fields('Telephone'));
		$this->Handphone->setDbValue($rs->fields('Handphone'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Alamat->setDbValue($rs->fields('Alamat'));
		$this->KodePos->setDbValue($rs->fields('KodePos'));
		$this->ProvinsiID->setDbValue($rs->fields('ProvinsiID'));
		$this->KabupatenKotaID->setDbValue($rs->fields('KabupatenKotaID'));
		$this->KecamatanID->setDbValue($rs->fields('KecamatanID'));
		$this->DesaID->setDbValue($rs->fields('DesaID'));
		$this->InstitusiInduk->setDbValue($rs->fields('InstitusiInduk'));
		$this->IkatanID->setDbValue($rs->fields('IkatanID'));
		$this->GolonganID->setDbValue($rs->fields('GolonganID'));
		$this->StatusKerjaID->setDbValue($rs->fields('StatusKerjaID'));
		$this->TglBekerja->setDbValue($rs->fields('TglBekerja'));
		$this->Homebase->setDbValue($rs->fields('Homebase'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->Keilmuan->setDbValue($rs->fields('Keilmuan'));
		$this->LulusanPT->setDbValue($rs->fields('LulusanPT'));
		$this->NamaBank->setDbValue($rs->fields('NamaBank'));
		$this->NamaAkun->setDbValue($rs->fields('NamaAkun'));
		$this->NomerAkun->setDbValue($rs->fields('NomerAkun'));
		$this->Creator->setDbValue($rs->fields('Creator'));
		$this->CreateDate->setDbValue($rs->fields('CreateDate'));
		$this->Editor->setDbValue($rs->fields('Editor'));
		$this->EditDate->setDbValue($rs->fields('EditDate'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// TeacherID

		$this->TeacherID->CellCssStyle = "white-space: nowrap;";

		// NIPPNS
		$this->NIPPNS->CellCssStyle = "white-space: nowrap;";

		// Nama
		$this->Nama->CellCssStyle = "white-space: nowrap;";

		// Gelar
		// LevelID
		// Password
		// AliasCode
		// KTP

		$this->KTP->CellCssStyle = "white-space: nowrap;";

		// TempatLahir
		// TanggalLahir
		// AgamaID
		// KelaminID
		// Telephone
		// Handphone
		// Email
		// Alamat
		// KodePos
		// ProvinsiID
		// KabupatenKotaID
		// KecamatanID
		// DesaID
		// InstitusiInduk
		// IkatanID
		// GolonganID
		// StatusKerjaID
		// TglBekerja
		// Homebase
		// ProdiID
		// Keilmuan
		// LulusanPT
		// NamaBank
		// NamaAkun
		// NomerAkun
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA
		// TeacherID

		$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
		$this->TeacherID->CssStyle = "font-weight: bold;";
		$this->TeacherID->ViewCustomAttributes = "";

		// NIPPNS
		$this->NIPPNS->ViewValue = $this->NIPPNS->CurrentValue;
		$this->NIPPNS->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->CssStyle = "font-weight: bold;";
		$this->Nama->ViewCustomAttributes = "";

		// Gelar
		$this->Gelar->ViewValue = $this->Gelar->CurrentValue;
		$this->Gelar->ViewCustomAttributes = "";

		// LevelID
		$this->LevelID->ViewValue = $this->LevelID->CurrentValue;
		$this->LevelID->ViewCustomAttributes = "";

		// Password
		$this->Password->ViewValue = $Language->Phrase("PasswordMask");
		$this->Password->ViewCustomAttributes = "";

		// AliasCode
		$this->AliasCode->ViewValue = $this->AliasCode->CurrentValue;
		$this->AliasCode->ViewCustomAttributes = "";

		// KTP
		$this->KTP->ViewValue = $this->KTP->CurrentValue;
		$this->KTP->ViewCustomAttributes = "";

		// TempatLahir
		$this->TempatLahir->ViewValue = $this->TempatLahir->CurrentValue;
		if (strval($this->TempatLahir->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->TempatLahir->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->TempatLahir->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->TempatLahir, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->TempatLahir->ViewValue = $this->TempatLahir->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->TempatLahir->ViewValue = $this->TempatLahir->CurrentValue;
			}
		} else {
			$this->TempatLahir->ViewValue = NULL;
		}
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

		// KelaminID
		if (strval($this->KelaminID->CurrentValue) <> "") {
			$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->KelaminID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
		$sWhereWrk = "";
		$this->KelaminID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KelaminID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KelaminID->ViewValue = $this->KelaminID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KelaminID->ViewValue = $this->KelaminID->CurrentValue;
			}
		} else {
			$this->KelaminID->ViewValue = NULL;
		}
		$this->KelaminID->ViewCustomAttributes = "";

		// Telephone
		$this->Telephone->ViewValue = $this->Telephone->CurrentValue;
		$this->Telephone->ViewCustomAttributes = "";

		// Handphone
		$this->Handphone->ViewValue = $this->Handphone->CurrentValue;
		$this->Handphone->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// Alamat
		$this->Alamat->ViewValue = $this->Alamat->CurrentValue;
		$this->Alamat->ViewCustomAttributes = "";

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

		// InstitusiInduk
		if (strval($this->InstitusiInduk->CurrentValue) <> "") {
			$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->InstitusiInduk->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
		$sWhereWrk = "";
		$this->InstitusiInduk->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->InstitusiInduk, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->InstitusiInduk->ViewValue = $this->InstitusiInduk->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->InstitusiInduk->ViewValue = $this->InstitusiInduk->CurrentValue;
			}
		} else {
			$this->InstitusiInduk->ViewValue = NULL;
		}
		$this->InstitusiInduk->ViewCustomAttributes = "";

		// IkatanID
		if (strval($this->IkatanID->CurrentValue) <> "") {
			$sFilterWrk = "`IkatanID`" . ew_SearchString("=", $this->IkatanID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `IkatanID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_ikatan`";
		$sWhereWrk = "";
		$this->IkatanID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->IkatanID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->IkatanID->ViewValue = $this->IkatanID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->IkatanID->ViewValue = $this->IkatanID->CurrentValue;
			}
		} else {
			$this->IkatanID->ViewValue = NULL;
		}
		$this->IkatanID->ViewCustomAttributes = "";

		// GolonganID
		if (strval($this->GolonganID->CurrentValue) <> "") {
			$sFilterWrk = "`GolonganID`" . ew_SearchString("=", $this->GolonganID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `GolonganID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_golongan`";
		$sWhereWrk = "";
		$this->GolonganID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->GolonganID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->GolonganID->ViewValue = $this->GolonganID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->GolonganID->ViewValue = $this->GolonganID->CurrentValue;
			}
		} else {
			$this->GolonganID->ViewValue = NULL;
		}
		$this->GolonganID->ViewCustomAttributes = "";

		// StatusKerjaID
		if (strval($this->StatusKerjaID->CurrentValue) <> "") {
			$sFilterWrk = "`StatusKerjaID`" . ew_SearchString("=", $this->StatusKerjaID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusKerjaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statuskerja`";
		$sWhereWrk = "";
		$this->StatusKerjaID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StatusKerjaID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StatusKerjaID->ViewValue = $this->StatusKerjaID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StatusKerjaID->ViewValue = $this->StatusKerjaID->CurrentValue;
			}
		} else {
			$this->StatusKerjaID->ViewValue = NULL;
		}
		$this->StatusKerjaID->ViewCustomAttributes = "";

		// TglBekerja
		$this->TglBekerja->ViewValue = $this->TglBekerja->CurrentValue;
		$this->TglBekerja->ViewValue = ew_FormatDateTime($this->TglBekerja->ViewValue, 0);
		$this->TglBekerja->ViewCustomAttributes = "";

		// Homebase
		if (strval($this->Homebase->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->Homebase->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->Homebase->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Homebase, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Homebase->ViewValue = $this->Homebase->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Homebase->ViewValue = $this->Homebase->CurrentValue;
			}
		} else {
			$this->Homebase->ViewValue = NULL;
		}
		$this->Homebase->ViewCustomAttributes = "";

		// ProdiID
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$arwrk = explode(",", $this->ProdiID->CurrentValue);
			$sFilterWrk = "";
			foreach ($arwrk as $wrk) {
				if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
				$sFilterWrk .= "`ProdiID`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING, "");
			}
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->ProdiID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->ProdiID->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->ProdiID->ViewValue .= $this->ProdiID->DisplayValue($arwrk);
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->ProdiID->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
				$rswrk->Close();
			} else {
				$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
			}
		} else {
			$this->ProdiID->ViewValue = NULL;
		}
		$this->ProdiID->ViewCustomAttributes = "";

		// Keilmuan
		$this->Keilmuan->ViewValue = $this->Keilmuan->CurrentValue;
		$this->Keilmuan->ViewCustomAttributes = "";

		// LulusanPT
		$this->LulusanPT->ViewValue = $this->LulusanPT->CurrentValue;
		$this->LulusanPT->ViewCustomAttributes = "";

		// NamaBank
		$this->NamaBank->ViewValue = $this->NamaBank->CurrentValue;
		$this->NamaBank->ViewCustomAttributes = "";

		// NamaAkun
		$this->NamaAkun->ViewValue = $this->NamaAkun->CurrentValue;
		$this->NamaAkun->ViewCustomAttributes = "";

		// NomerAkun
		$this->NomerAkun->ViewValue = $this->NomerAkun->CurrentValue;
		$this->NomerAkun->ViewCustomAttributes = "";

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

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Ya";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "Tidak";
		}
		$this->NA->ViewCustomAttributes = "";

		// TeacherID
		$this->TeacherID->LinkCustomAttributes = "";
		$this->TeacherID->HrefValue = "";
		$this->TeacherID->TooltipValue = "";

		// NIPPNS
		$this->NIPPNS->LinkCustomAttributes = "";
		$this->NIPPNS->HrefValue = "";
		$this->NIPPNS->TooltipValue = "";

		// Nama
		$this->Nama->LinkCustomAttributes = "";
		$this->Nama->HrefValue = "";
		$this->Nama->TooltipValue = "";

		// Gelar
		$this->Gelar->LinkCustomAttributes = "";
		$this->Gelar->HrefValue = "";
		$this->Gelar->TooltipValue = "";

		// LevelID
		$this->LevelID->LinkCustomAttributes = "";
		$this->LevelID->HrefValue = "";
		$this->LevelID->TooltipValue = "";

		// Password
		$this->Password->LinkCustomAttributes = "";
		$this->Password->HrefValue = "";
		$this->Password->TooltipValue = "";

		// AliasCode
		$this->AliasCode->LinkCustomAttributes = "";
		$this->AliasCode->HrefValue = "";
		$this->AliasCode->TooltipValue = "";

		// KTP
		$this->KTP->LinkCustomAttributes = "";
		$this->KTP->HrefValue = "";
		$this->KTP->TooltipValue = "";

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

		// KelaminID
		$this->KelaminID->LinkCustomAttributes = "";
		$this->KelaminID->HrefValue = "";
		$this->KelaminID->TooltipValue = "";

		// Telephone
		$this->Telephone->LinkCustomAttributes = "";
		$this->Telephone->HrefValue = "";
		$this->Telephone->TooltipValue = "";

		// Handphone
		$this->Handphone->LinkCustomAttributes = "";
		$this->Handphone->HrefValue = "";
		$this->Handphone->TooltipValue = "";

		// Email
		$this->_Email->LinkCustomAttributes = "";
		$this->_Email->HrefValue = "";
		$this->_Email->TooltipValue = "";

		// Alamat
		$this->Alamat->LinkCustomAttributes = "";
		$this->Alamat->HrefValue = "";
		$this->Alamat->TooltipValue = "";

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

		// InstitusiInduk
		$this->InstitusiInduk->LinkCustomAttributes = "";
		$this->InstitusiInduk->HrefValue = "";
		$this->InstitusiInduk->TooltipValue = "";

		// IkatanID
		$this->IkatanID->LinkCustomAttributes = "";
		$this->IkatanID->HrefValue = "";
		$this->IkatanID->TooltipValue = "";

		// GolonganID
		$this->GolonganID->LinkCustomAttributes = "";
		$this->GolonganID->HrefValue = "";
		$this->GolonganID->TooltipValue = "";

		// StatusKerjaID
		$this->StatusKerjaID->LinkCustomAttributes = "";
		$this->StatusKerjaID->HrefValue = "";
		$this->StatusKerjaID->TooltipValue = "";

		// TglBekerja
		$this->TglBekerja->LinkCustomAttributes = "";
		$this->TglBekerja->HrefValue = "";
		$this->TglBekerja->TooltipValue = "";

		// Homebase
		$this->Homebase->LinkCustomAttributes = "";
		$this->Homebase->HrefValue = "";
		$this->Homebase->TooltipValue = "";

		// ProdiID
		$this->ProdiID->LinkCustomAttributes = "";
		$this->ProdiID->HrefValue = "";
		$this->ProdiID->TooltipValue = "";

		// Keilmuan
		$this->Keilmuan->LinkCustomAttributes = "";
		$this->Keilmuan->HrefValue = "";
		$this->Keilmuan->TooltipValue = "";

		// LulusanPT
		$this->LulusanPT->LinkCustomAttributes = "";
		$this->LulusanPT->HrefValue = "";
		$this->LulusanPT->TooltipValue = "";

		// NamaBank
		$this->NamaBank->LinkCustomAttributes = "";
		$this->NamaBank->HrefValue = "";
		$this->NamaBank->TooltipValue = "";

		// NamaAkun
		$this->NamaAkun->LinkCustomAttributes = "";
		$this->NamaAkun->HrefValue = "";
		$this->NamaAkun->TooltipValue = "";

		// NomerAkun
		$this->NomerAkun->LinkCustomAttributes = "";
		$this->NomerAkun->HrefValue = "";
		$this->NomerAkun->TooltipValue = "";

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

		// TeacherID
		$this->TeacherID->EditAttrs["class"] = "form-control";
		$this->TeacherID->EditCustomAttributes = "";
		$this->TeacherID->EditValue = $this->TeacherID->CurrentValue;
		$this->TeacherID->CssStyle = "font-weight: bold;";
		$this->TeacherID->ViewCustomAttributes = "";

		// NIPPNS
		$this->NIPPNS->EditAttrs["class"] = "form-control";
		$this->NIPPNS->EditCustomAttributes = "";
		$this->NIPPNS->EditValue = $this->NIPPNS->CurrentValue;
		$this->NIPPNS->PlaceHolder = ew_RemoveHtml($this->NIPPNS->FldCaption());

		// Nama
		$this->Nama->EditAttrs["class"] = "form-control";
		$this->Nama->EditCustomAttributes = "";
		$this->Nama->EditValue = $this->Nama->CurrentValue;
		$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

		// Gelar
		$this->Gelar->EditAttrs["class"] = "form-control";
		$this->Gelar->EditCustomAttributes = "";
		$this->Gelar->EditValue = $this->Gelar->CurrentValue;
		$this->Gelar->PlaceHolder = ew_RemoveHtml($this->Gelar->FldCaption());

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

		// AliasCode
		$this->AliasCode->EditAttrs["class"] = "form-control";
		$this->AliasCode->EditCustomAttributes = "";
		$this->AliasCode->EditValue = $this->AliasCode->CurrentValue;
		$this->AliasCode->PlaceHolder = ew_RemoveHtml($this->AliasCode->FldCaption());

		// KTP
		$this->KTP->EditAttrs["class"] = "form-control";
		$this->KTP->EditCustomAttributes = "";
		$this->KTP->EditValue = $this->KTP->CurrentValue;
		$this->KTP->PlaceHolder = ew_RemoveHtml($this->KTP->FldCaption());

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

		// KelaminID
		$this->KelaminID->EditCustomAttributes = "";

		// Telephone
		$this->Telephone->EditAttrs["class"] = "form-control";
		$this->Telephone->EditCustomAttributes = "";
		$this->Telephone->EditValue = $this->Telephone->CurrentValue;
		$this->Telephone->PlaceHolder = ew_RemoveHtml($this->Telephone->FldCaption());

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

		// Alamat
		$this->Alamat->EditAttrs["class"] = "form-control";
		$this->Alamat->EditCustomAttributes = "";
		$this->Alamat->EditValue = $this->Alamat->CurrentValue;
		$this->Alamat->PlaceHolder = ew_RemoveHtml($this->Alamat->FldCaption());

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

		// InstitusiInduk
		$this->InstitusiInduk->EditAttrs["class"] = "form-control";
		$this->InstitusiInduk->EditCustomAttributes = "";

		// IkatanID
		$this->IkatanID->EditAttrs["class"] = "form-control";
		$this->IkatanID->EditCustomAttributes = "";

		// GolonganID
		$this->GolonganID->EditAttrs["class"] = "form-control";
		$this->GolonganID->EditCustomAttributes = "";

		// StatusKerjaID
		$this->StatusKerjaID->EditAttrs["class"] = "form-control";
		$this->StatusKerjaID->EditCustomAttributes = "";

		// TglBekerja
		$this->TglBekerja->EditAttrs["class"] = "form-control";
		$this->TglBekerja->EditCustomAttributes = "";
		$this->TglBekerja->EditValue = ew_FormatDateTime($this->TglBekerja->CurrentValue, 8);
		$this->TglBekerja->PlaceHolder = ew_RemoveHtml($this->TglBekerja->FldCaption());

		// Homebase
		$this->Homebase->EditAttrs["class"] = "form-control";
		$this->Homebase->EditCustomAttributes = "";

		// ProdiID
		$this->ProdiID->EditCustomAttributes = "";

		// Keilmuan
		$this->Keilmuan->EditAttrs["class"] = "form-control";
		$this->Keilmuan->EditCustomAttributes = "";
		$this->Keilmuan->EditValue = $this->Keilmuan->CurrentValue;
		$this->Keilmuan->PlaceHolder = ew_RemoveHtml($this->Keilmuan->FldCaption());

		// LulusanPT
		$this->LulusanPT->EditAttrs["class"] = "form-control";
		$this->LulusanPT->EditCustomAttributes = "";
		$this->LulusanPT->EditValue = $this->LulusanPT->CurrentValue;
		$this->LulusanPT->PlaceHolder = ew_RemoveHtml($this->LulusanPT->FldCaption());

		// NamaBank
		$this->NamaBank->EditAttrs["class"] = "form-control";
		$this->NamaBank->EditCustomAttributes = "";
		$this->NamaBank->EditValue = $this->NamaBank->CurrentValue;
		$this->NamaBank->PlaceHolder = ew_RemoveHtml($this->NamaBank->FldCaption());

		// NamaAkun
		$this->NamaAkun->EditAttrs["class"] = "form-control";
		$this->NamaAkun->EditCustomAttributes = "";
		$this->NamaAkun->EditValue = $this->NamaAkun->CurrentValue;
		$this->NamaAkun->PlaceHolder = ew_RemoveHtml($this->NamaAkun->FldCaption());

		// NomerAkun
		$this->NomerAkun->EditAttrs["class"] = "form-control";
		$this->NomerAkun->EditCustomAttributes = "";
		$this->NomerAkun->EditValue = $this->NomerAkun->CurrentValue;
		$this->NomerAkun->PlaceHolder = ew_RemoveHtml($this->NomerAkun->FldCaption());

		// Creator
		// CreateDate
		// Editor
		// EditDate
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
					if ($this->TeacherID->Exportable) $Doc->ExportCaption($this->TeacherID);
					if ($this->NIPPNS->Exportable) $Doc->ExportCaption($this->NIPPNS);
					if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
					if ($this->Gelar->Exportable) $Doc->ExportCaption($this->Gelar);
					if ($this->LevelID->Exportable) $Doc->ExportCaption($this->LevelID);
					if ($this->Password->Exportable) $Doc->ExportCaption($this->Password);
					if ($this->AliasCode->Exportable) $Doc->ExportCaption($this->AliasCode);
					if ($this->KTP->Exportable) $Doc->ExportCaption($this->KTP);
					if ($this->TempatLahir->Exportable) $Doc->ExportCaption($this->TempatLahir);
					if ($this->TanggalLahir->Exportable) $Doc->ExportCaption($this->TanggalLahir);
					if ($this->AgamaID->Exportable) $Doc->ExportCaption($this->AgamaID);
					if ($this->KelaminID->Exportable) $Doc->ExportCaption($this->KelaminID);
					if ($this->Telephone->Exportable) $Doc->ExportCaption($this->Telephone);
					if ($this->_Email->Exportable) $Doc->ExportCaption($this->_Email);
					if ($this->Alamat->Exportable) $Doc->ExportCaption($this->Alamat);
					if ($this->KodePos->Exportable) $Doc->ExportCaption($this->KodePos);
					if ($this->ProvinsiID->Exportable) $Doc->ExportCaption($this->ProvinsiID);
					if ($this->KabupatenKotaID->Exportable) $Doc->ExportCaption($this->KabupatenKotaID);
					if ($this->KecamatanID->Exportable) $Doc->ExportCaption($this->KecamatanID);
					if ($this->DesaID->Exportable) $Doc->ExportCaption($this->DesaID);
					if ($this->InstitusiInduk->Exportable) $Doc->ExportCaption($this->InstitusiInduk);
					if ($this->IkatanID->Exportable) $Doc->ExportCaption($this->IkatanID);
					if ($this->GolonganID->Exportable) $Doc->ExportCaption($this->GolonganID);
					if ($this->StatusKerjaID->Exportable) $Doc->ExportCaption($this->StatusKerjaID);
					if ($this->TglBekerja->Exportable) $Doc->ExportCaption($this->TglBekerja);
					if ($this->Homebase->Exportable) $Doc->ExportCaption($this->Homebase);
					if ($this->ProdiID->Exportable) $Doc->ExportCaption($this->ProdiID);
					if ($this->Keilmuan->Exportable) $Doc->ExportCaption($this->Keilmuan);
					if ($this->LulusanPT->Exportable) $Doc->ExportCaption($this->LulusanPT);
					if ($this->NamaBank->Exportable) $Doc->ExportCaption($this->NamaBank);
					if ($this->NamaAkun->Exportable) $Doc->ExportCaption($this->NamaAkun);
					if ($this->NomerAkun->Exportable) $Doc->ExportCaption($this->NomerAkun);
					if ($this->Creator->Exportable) $Doc->ExportCaption($this->Creator);
					if ($this->CreateDate->Exportable) $Doc->ExportCaption($this->CreateDate);
					if ($this->Editor->Exportable) $Doc->ExportCaption($this->Editor);
					if ($this->EditDate->Exportable) $Doc->ExportCaption($this->EditDate);
					if ($this->NA->Exportable) $Doc->ExportCaption($this->NA);
				} else {
					if ($this->TeacherID->Exportable) $Doc->ExportCaption($this->TeacherID);
					if ($this->NIPPNS->Exportable) $Doc->ExportCaption($this->NIPPNS);
					if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
					if ($this->Gelar->Exportable) $Doc->ExportCaption($this->Gelar);
					if ($this->LevelID->Exportable) $Doc->ExportCaption($this->LevelID);
					if ($this->AliasCode->Exportable) $Doc->ExportCaption($this->AliasCode);
					if ($this->KTP->Exportable) $Doc->ExportCaption($this->KTP);
					if ($this->TempatLahir->Exportable) $Doc->ExportCaption($this->TempatLahir);
					if ($this->TanggalLahir->Exportable) $Doc->ExportCaption($this->TanggalLahir);
					if ($this->AgamaID->Exportable) $Doc->ExportCaption($this->AgamaID);
					if ($this->KelaminID->Exportable) $Doc->ExportCaption($this->KelaminID);
					if ($this->Telephone->Exportable) $Doc->ExportCaption($this->Telephone);
					if ($this->_Email->Exportable) $Doc->ExportCaption($this->_Email);
					if ($this->Alamat->Exportable) $Doc->ExportCaption($this->Alamat);
					if ($this->KodePos->Exportable) $Doc->ExportCaption($this->KodePos);
					if ($this->ProvinsiID->Exportable) $Doc->ExportCaption($this->ProvinsiID);
					if ($this->KabupatenKotaID->Exportable) $Doc->ExportCaption($this->KabupatenKotaID);
					if ($this->KecamatanID->Exportable) $Doc->ExportCaption($this->KecamatanID);
					if ($this->DesaID->Exportable) $Doc->ExportCaption($this->DesaID);
					if ($this->InstitusiInduk->Exportable) $Doc->ExportCaption($this->InstitusiInduk);
					if ($this->IkatanID->Exportable) $Doc->ExportCaption($this->IkatanID);
					if ($this->GolonganID->Exportable) $Doc->ExportCaption($this->GolonganID);
					if ($this->StatusKerjaID->Exportable) $Doc->ExportCaption($this->StatusKerjaID);
					if ($this->TglBekerja->Exportable) $Doc->ExportCaption($this->TglBekerja);
					if ($this->Homebase->Exportable) $Doc->ExportCaption($this->Homebase);
					if ($this->ProdiID->Exportable) $Doc->ExportCaption($this->ProdiID);
					if ($this->Keilmuan->Exportable) $Doc->ExportCaption($this->Keilmuan);
					if ($this->LulusanPT->Exportable) $Doc->ExportCaption($this->LulusanPT);
					if ($this->NamaBank->Exportable) $Doc->ExportCaption($this->NamaBank);
					if ($this->NamaAkun->Exportable) $Doc->ExportCaption($this->NamaAkun);
					if ($this->NomerAkun->Exportable) $Doc->ExportCaption($this->NomerAkun);
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
						if ($this->TeacherID->Exportable) $Doc->ExportField($this->TeacherID);
						if ($this->NIPPNS->Exportable) $Doc->ExportField($this->NIPPNS);
						if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
						if ($this->Gelar->Exportable) $Doc->ExportField($this->Gelar);
						if ($this->LevelID->Exportable) $Doc->ExportField($this->LevelID);
						if ($this->Password->Exportable) $Doc->ExportField($this->Password);
						if ($this->AliasCode->Exportable) $Doc->ExportField($this->AliasCode);
						if ($this->KTP->Exportable) $Doc->ExportField($this->KTP);
						if ($this->TempatLahir->Exportable) $Doc->ExportField($this->TempatLahir);
						if ($this->TanggalLahir->Exportable) $Doc->ExportField($this->TanggalLahir);
						if ($this->AgamaID->Exportable) $Doc->ExportField($this->AgamaID);
						if ($this->KelaminID->Exportable) $Doc->ExportField($this->KelaminID);
						if ($this->Telephone->Exportable) $Doc->ExportField($this->Telephone);
						if ($this->_Email->Exportable) $Doc->ExportField($this->_Email);
						if ($this->Alamat->Exportable) $Doc->ExportField($this->Alamat);
						if ($this->KodePos->Exportable) $Doc->ExportField($this->KodePos);
						if ($this->ProvinsiID->Exportable) $Doc->ExportField($this->ProvinsiID);
						if ($this->KabupatenKotaID->Exportable) $Doc->ExportField($this->KabupatenKotaID);
						if ($this->KecamatanID->Exportable) $Doc->ExportField($this->KecamatanID);
						if ($this->DesaID->Exportable) $Doc->ExportField($this->DesaID);
						if ($this->InstitusiInduk->Exportable) $Doc->ExportField($this->InstitusiInduk);
						if ($this->IkatanID->Exportable) $Doc->ExportField($this->IkatanID);
						if ($this->GolonganID->Exportable) $Doc->ExportField($this->GolonganID);
						if ($this->StatusKerjaID->Exportable) $Doc->ExportField($this->StatusKerjaID);
						if ($this->TglBekerja->Exportable) $Doc->ExportField($this->TglBekerja);
						if ($this->Homebase->Exportable) $Doc->ExportField($this->Homebase);
						if ($this->ProdiID->Exportable) $Doc->ExportField($this->ProdiID);
						if ($this->Keilmuan->Exportable) $Doc->ExportField($this->Keilmuan);
						if ($this->LulusanPT->Exportable) $Doc->ExportField($this->LulusanPT);
						if ($this->NamaBank->Exportable) $Doc->ExportField($this->NamaBank);
						if ($this->NamaAkun->Exportable) $Doc->ExportField($this->NamaAkun);
						if ($this->NomerAkun->Exportable) $Doc->ExportField($this->NomerAkun);
						if ($this->Creator->Exportable) $Doc->ExportField($this->Creator);
						if ($this->CreateDate->Exportable) $Doc->ExportField($this->CreateDate);
						if ($this->Editor->Exportable) $Doc->ExportField($this->Editor);
						if ($this->EditDate->Exportable) $Doc->ExportField($this->EditDate);
						if ($this->NA->Exportable) $Doc->ExportField($this->NA);
					} else {
						if ($this->TeacherID->Exportable) $Doc->ExportField($this->TeacherID);
						if ($this->NIPPNS->Exportable) $Doc->ExportField($this->NIPPNS);
						if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
						if ($this->Gelar->Exportable) $Doc->ExportField($this->Gelar);
						if ($this->LevelID->Exportable) $Doc->ExportField($this->LevelID);
						if ($this->AliasCode->Exportable) $Doc->ExportField($this->AliasCode);
						if ($this->KTP->Exportable) $Doc->ExportField($this->KTP);
						if ($this->TempatLahir->Exportable) $Doc->ExportField($this->TempatLahir);
						if ($this->TanggalLahir->Exportable) $Doc->ExportField($this->TanggalLahir);
						if ($this->AgamaID->Exportable) $Doc->ExportField($this->AgamaID);
						if ($this->KelaminID->Exportable) $Doc->ExportField($this->KelaminID);
						if ($this->Telephone->Exportable) $Doc->ExportField($this->Telephone);
						if ($this->_Email->Exportable) $Doc->ExportField($this->_Email);
						if ($this->Alamat->Exportable) $Doc->ExportField($this->Alamat);
						if ($this->KodePos->Exportable) $Doc->ExportField($this->KodePos);
						if ($this->ProvinsiID->Exportable) $Doc->ExportField($this->ProvinsiID);
						if ($this->KabupatenKotaID->Exportable) $Doc->ExportField($this->KabupatenKotaID);
						if ($this->KecamatanID->Exportable) $Doc->ExportField($this->KecamatanID);
						if ($this->DesaID->Exportable) $Doc->ExportField($this->DesaID);
						if ($this->InstitusiInduk->Exportable) $Doc->ExportField($this->InstitusiInduk);
						if ($this->IkatanID->Exportable) $Doc->ExportField($this->IkatanID);
						if ($this->GolonganID->Exportable) $Doc->ExportField($this->GolonganID);
						if ($this->StatusKerjaID->Exportable) $Doc->ExportField($this->StatusKerjaID);
						if ($this->TglBekerja->Exportable) $Doc->ExportField($this->TglBekerja);
						if ($this->Homebase->Exportable) $Doc->ExportField($this->Homebase);
						if ($this->ProdiID->Exportable) $Doc->ExportField($this->ProdiID);
						if ($this->Keilmuan->Exportable) $Doc->ExportField($this->Keilmuan);
						if ($this->LulusanPT->Exportable) $Doc->ExportField($this->LulusanPT);
						if ($this->NamaBank->Exportable) $Doc->ExportField($this->NamaBank);
						if ($this->NamaAkun->Exportable) $Doc->ExportField($this->NamaAkun);
						if ($this->NomerAkun->Exportable) $Doc->ExportField($this->NomerAkun);
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
		$table = 'teacher';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		global $Language;
		if (!$this->AuditTrailOnAdd) return;
		$table = 'teacher';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['TeacherID'];

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
		$table = 'teacher';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['TeacherID'];

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
		$table = 'teacher';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['TeacherID'];

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
