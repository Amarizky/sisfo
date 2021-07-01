<?php

// Global variable for table object
$krs = NULL;

//
// Table class for krs
//
class ckrs extends cTable {
	var $AuditTrailOnAdd = TRUE;
	var $AuditTrailOnEdit = TRUE;
	var $AuditTrailOnDelete = TRUE;
	var $AuditTrailOnView = FALSE;
	var $AuditTrailOnViewData = FALSE;
	var $AuditTrailOnSearch = FALSE;
	var $KRSID;
	var $KHSID;
	var $StudentID;
	var $TahunID;
	var $Sesi;
	var $JadwalID;
	var $MKID;
	var $MKKode;
	var $SKS;
	var $Tugas1;
	var $Tugas2;
	var $Tugas3;
	var $Tugas4;
	var $Tugas5;
	var $Presensi;
	var $_Presensi;
	var $UTS;
	var $UAS;
	var $Responsi;
	var $NilaiAkhir;
	var $GradeNilai;
	var $BobotNilai;
	var $StatusKRSID;
	var $Tinggi;
	var $Final;
	var $Setara;
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
		$this->TableVar = 'krs';
		$this->TableName = 'krs';
		$this->TableType = 'TABLE';

		// Update Table
		$this->UpdateTable = "`krs`";
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
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// KRSID
		$this->KRSID = new cField('krs', 'krs', 'x_KRSID', 'KRSID', '`KRSID`', '`KRSID`', 20, -1, FALSE, '`KRSID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'NO');
		$this->KRSID->Sortable = TRUE; // Allow sort
		$this->KRSID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['KRSID'] = &$this->KRSID;

		// KHSID
		$this->KHSID = new cField('krs', 'krs', 'x_KHSID', 'KHSID', '`KHSID`', '`KHSID`', 20, -1, FALSE, '`KHSID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->KHSID->Sortable = TRUE; // Allow sort
		$this->KHSID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['KHSID'] = &$this->KHSID;

		// StudentID
		$this->StudentID = new cField('krs', 'krs', 'x_StudentID', 'StudentID', '`StudentID`', '`StudentID`', 200, -1, FALSE, '`StudentID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->StudentID->Sortable = TRUE; // Allow sort
		$this->fields['StudentID'] = &$this->StudentID;

		// TahunID
		$this->TahunID = new cField('krs', 'krs', 'x_TahunID', 'TahunID', '`TahunID`', '`TahunID`', 200, -1, FALSE, '`TahunID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TahunID->Sortable = TRUE; // Allow sort
		$this->fields['TahunID'] = &$this->TahunID;

		// Sesi
		$this->Sesi = new cField('krs', 'krs', 'x_Sesi', 'Sesi', '`Sesi`', '`Sesi`', 16, -1, FALSE, '`Sesi`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Sesi->Sortable = TRUE; // Allow sort
		$this->Sesi->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Sesi'] = &$this->Sesi;

		// JadwalID
		$this->JadwalID = new cField('krs', 'krs', 'x_JadwalID', 'JadwalID', '`JadwalID`', '`JadwalID`', 20, -1, FALSE, '`JadwalID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->JadwalID->Sortable = TRUE; // Allow sort
		$this->JadwalID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['JadwalID'] = &$this->JadwalID;

		// MKID
		$this->MKID = new cField('krs', 'krs', 'x_MKID', 'MKID', '`MKID`', '`MKID`', 20, -1, FALSE, '`MKID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->MKID->Sortable = TRUE; // Allow sort
		$this->MKID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['MKID'] = &$this->MKID;

		// MKKode
		$this->MKKode = new cField('krs', 'krs', 'x_MKKode', 'MKKode', '`MKKode`', '`MKKode`', 200, -1, FALSE, '`MKKode`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->MKKode->Sortable = TRUE; // Allow sort
		$this->fields['MKKode'] = &$this->MKKode;

		// SKS
		$this->SKS = new cField('krs', 'krs', 'x_SKS', 'SKS', '`SKS`', '`SKS`', 3, -1, FALSE, '`SKS`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->SKS->Sortable = TRUE; // Allow sort
		$this->SKS->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['SKS'] = &$this->SKS;

		// Tugas1
		$this->Tugas1 = new cField('krs', 'krs', 'x_Tugas1', 'Tugas1', '`Tugas1`', '`Tugas1`', 3, -1, FALSE, '`Tugas1`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Tugas1->Sortable = TRUE; // Allow sort
		$this->Tugas1->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Tugas1'] = &$this->Tugas1;

		// Tugas2
		$this->Tugas2 = new cField('krs', 'krs', 'x_Tugas2', 'Tugas2', '`Tugas2`', '`Tugas2`', 3, -1, FALSE, '`Tugas2`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Tugas2->Sortable = TRUE; // Allow sort
		$this->Tugas2->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Tugas2'] = &$this->Tugas2;

		// Tugas3
		$this->Tugas3 = new cField('krs', 'krs', 'x_Tugas3', 'Tugas3', '`Tugas3`', '`Tugas3`', 3, -1, FALSE, '`Tugas3`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Tugas3->Sortable = TRUE; // Allow sort
		$this->Tugas3->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Tugas3'] = &$this->Tugas3;

		// Tugas4
		$this->Tugas4 = new cField('krs', 'krs', 'x_Tugas4', 'Tugas4', '`Tugas4`', '`Tugas4`', 3, -1, FALSE, '`Tugas4`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Tugas4->Sortable = TRUE; // Allow sort
		$this->Tugas4->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Tugas4'] = &$this->Tugas4;

		// Tugas5
		$this->Tugas5 = new cField('krs', 'krs', 'x_Tugas5', 'Tugas5', '`Tugas5`', '`Tugas5`', 3, -1, FALSE, '`Tugas5`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Tugas5->Sortable = TRUE; // Allow sort
		$this->Tugas5->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Tugas5'] = &$this->Tugas5;

		// Presensi
		$this->Presensi = new cField('krs', 'krs', 'x_Presensi', 'Presensi', '`Presensi`', '`Presensi`', 3, -1, FALSE, '`Presensi`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Presensi->Sortable = TRUE; // Allow sort
		$this->Presensi->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Presensi'] = &$this->Presensi;

		// _Presensi
		$this->_Presensi = new cField('krs', 'krs', 'x__Presensi', '_Presensi', '`_Presensi`', '`_Presensi`', 3, -1, FALSE, '`_Presensi`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->_Presensi->Sortable = TRUE; // Allow sort
		$this->_Presensi->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['_Presensi'] = &$this->_Presensi;

		// UTS
		$this->UTS = new cField('krs', 'krs', 'x_UTS', 'UTS', '`UTS`', '`UTS`', 3, -1, FALSE, '`UTS`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->UTS->Sortable = TRUE; // Allow sort
		$this->UTS->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['UTS'] = &$this->UTS;

		// UAS
		$this->UAS = new cField('krs', 'krs', 'x_UAS', 'UAS', '`UAS`', '`UAS`', 3, -1, FALSE, '`UAS`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->UAS->Sortable = TRUE; // Allow sort
		$this->UAS->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['UAS'] = &$this->UAS;

		// Responsi
		$this->Responsi = new cField('krs', 'krs', 'x_Responsi', 'Responsi', '`Responsi`', '`Responsi`', 131, -1, FALSE, '`Responsi`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Responsi->Sortable = TRUE; // Allow sort
		$this->Responsi->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['Responsi'] = &$this->Responsi;

		// NilaiAkhir
		$this->NilaiAkhir = new cField('krs', 'krs', 'x_NilaiAkhir', 'NilaiAkhir', '`NilaiAkhir`', '`NilaiAkhir`', 131, -1, FALSE, '`NilaiAkhir`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NilaiAkhir->Sortable = TRUE; // Allow sort
		$this->NilaiAkhir->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['NilaiAkhir'] = &$this->NilaiAkhir;

		// GradeNilai
		$this->GradeNilai = new cField('krs', 'krs', 'x_GradeNilai', 'GradeNilai', '`GradeNilai`', '`GradeNilai`', 200, -1, FALSE, '`GradeNilai`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->GradeNilai->Sortable = TRUE; // Allow sort
		$this->fields['GradeNilai'] = &$this->GradeNilai;

		// BobotNilai
		$this->BobotNilai = new cField('krs', 'krs', 'x_BobotNilai', 'BobotNilai', '`BobotNilai`', '`BobotNilai`', 131, -1, FALSE, '`BobotNilai`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->BobotNilai->Sortable = TRUE; // Allow sort
		$this->BobotNilai->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['BobotNilai'] = &$this->BobotNilai;

		// StatusKRSID
		$this->StatusKRSID = new cField('krs', 'krs', 'x_StatusKRSID', 'StatusKRSID', '`StatusKRSID`', '`StatusKRSID`', 200, -1, FALSE, '`StatusKRSID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->StatusKRSID->Sortable = TRUE; // Allow sort
		$this->fields['StatusKRSID'] = &$this->StatusKRSID;

		// Tinggi
		$this->Tinggi = new cField('krs', 'krs', 'x_Tinggi', 'Tinggi', '`Tinggi`', '`Tinggi`', 200, -1, FALSE, '`Tinggi`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Tinggi->Sortable = TRUE; // Allow sort
		$this->fields['Tinggi'] = &$this->Tinggi;

		// Final
		$this->Final = new cField('krs', 'krs', 'x_Final', 'Final', '`Final`', '`Final`', 202, -1, FALSE, '`Final`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->Final->Sortable = TRUE; // Allow sort
		$this->Final->FldDataType = EW_DATATYPE_BOOLEAN;
		$this->Final->TrueValue = 'Y';
		$this->Final->FalseValue = 'N';
		$this->Final->OptionCount = 2;
		$this->fields['Final'] = &$this->Final;

		// Setara
		$this->Setara = new cField('krs', 'krs', 'x_Setara', 'Setara', '`Setara`', '`Setara`', 202, -1, FALSE, '`Setara`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->Setara->Sortable = TRUE; // Allow sort
		$this->Setara->FldDataType = EW_DATATYPE_BOOLEAN;
		$this->Setara->TrueValue = 'Y';
		$this->Setara->FalseValue = 'N';
		$this->Setara->OptionCount = 2;
		$this->fields['Setara'] = &$this->Setara;

		// Creator
		$this->Creator = new cField('krs', 'krs', 'x_Creator', 'Creator', '`Creator`', '`Creator`', 200, -1, FALSE, '`Creator`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Creator->Sortable = TRUE; // Allow sort
		$this->fields['Creator'] = &$this->Creator;

		// CreateDate
		$this->CreateDate = new cField('krs', 'krs', 'x_CreateDate', 'CreateDate', '`CreateDate`', ew_CastDateFieldForLike('`CreateDate`', 0, "DB"), 133, 0, FALSE, '`CreateDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->CreateDate->Sortable = TRUE; // Allow sort
		$this->CreateDate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['CreateDate'] = &$this->CreateDate;

		// Editor
		$this->Editor = new cField('krs', 'krs', 'x_Editor', 'Editor', '`Editor`', '`Editor`', 200, -1, FALSE, '`Editor`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Editor->Sortable = TRUE; // Allow sort
		$this->fields['Editor'] = &$this->Editor;

		// EditDate
		$this->EditDate = new cField('krs', 'krs', 'x_EditDate', 'EditDate', '`EditDate`', ew_CastDateFieldForLike('`EditDate`', 0, "DB"), 133, 0, FALSE, '`EditDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->EditDate->Sortable = TRUE; // Allow sort
		$this->EditDate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['EditDate'] = &$this->EditDate;

		// NA
		$this->NA = new cField('krs', 'krs', 'x_NA', 'NA', '`NA`', '`NA`', 202, -1, FALSE, '`NA`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`krs`";
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

			// Get insert id if necessary
			$this->KRSID->setDbValue($conn->Insert_ID());
			$rs['KRSID'] = $this->KRSID->DbValue;
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
			$fldname = 'KRSID';
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
			if (array_key_exists('KRSID', $rs))
				ew_AddFilter($where, ew_QuotedName('KRSID', $this->DBID) . '=' . ew_QuotedValue($rs['KRSID'], $this->KRSID->FldDataType, $this->DBID));
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
		return "`KRSID` = @KRSID@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->KRSID->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@KRSID@", ew_AdjustSql($this->KRSID->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
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
			return "krslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "krslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("krsview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("krsview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "krsadd.php?" . $this->UrlParm($parm);
		else
			$url = "krsadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("krsedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("krsadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("krsdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "KRSID:" . ew_VarToJson($this->KRSID->CurrentValue, "number", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->KRSID->CurrentValue)) {
			$sUrl .= "KRSID=" . urlencode($this->KRSID->CurrentValue);
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
			if ($isPost && isset($_POST["KRSID"]))
				$arKeys[] = ew_StripSlashes($_POST["KRSID"]);
			elseif (isset($_GET["KRSID"]))
				$arKeys[] = ew_StripSlashes($_GET["KRSID"]);
			else
				$arKeys = NULL; // Do not setup

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		if (is_array($arKeys)) {
			foreach ($arKeys as $key) {
				if (!is_numeric($key))
					continue;
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
			$this->KRSID->CurrentValue = $key;
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
		$this->KRSID->setDbValue($rs->fields('KRSID'));
		$this->KHSID->setDbValue($rs->fields('KHSID'));
		$this->StudentID->setDbValue($rs->fields('StudentID'));
		$this->TahunID->setDbValue($rs->fields('TahunID'));
		$this->Sesi->setDbValue($rs->fields('Sesi'));
		$this->JadwalID->setDbValue($rs->fields('JadwalID'));
		$this->MKID->setDbValue($rs->fields('MKID'));
		$this->MKKode->setDbValue($rs->fields('MKKode'));
		$this->SKS->setDbValue($rs->fields('SKS'));
		$this->Tugas1->setDbValue($rs->fields('Tugas1'));
		$this->Tugas2->setDbValue($rs->fields('Tugas2'));
		$this->Tugas3->setDbValue($rs->fields('Tugas3'));
		$this->Tugas4->setDbValue($rs->fields('Tugas4'));
		$this->Tugas5->setDbValue($rs->fields('Tugas5'));
		$this->Presensi->setDbValue($rs->fields('Presensi'));
		$this->_Presensi->setDbValue($rs->fields('_Presensi'));
		$this->UTS->setDbValue($rs->fields('UTS'));
		$this->UAS->setDbValue($rs->fields('UAS'));
		$this->Responsi->setDbValue($rs->fields('Responsi'));
		$this->NilaiAkhir->setDbValue($rs->fields('NilaiAkhir'));
		$this->GradeNilai->setDbValue($rs->fields('GradeNilai'));
		$this->BobotNilai->setDbValue($rs->fields('BobotNilai'));
		$this->StatusKRSID->setDbValue($rs->fields('StatusKRSID'));
		$this->Tinggi->setDbValue($rs->fields('Tinggi'));
		$this->Final->setDbValue($rs->fields('Final'));
		$this->Setara->setDbValue($rs->fields('Setara'));
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
		// KRSID
		// KHSID
		// StudentID
		// TahunID
		// Sesi
		// JadwalID
		// MKID
		// MKKode
		// SKS
		// Tugas1
		// Tugas2
		// Tugas3
		// Tugas4
		// Tugas5
		// Presensi
		// _Presensi
		// UTS
		// UAS
		// Responsi
		// NilaiAkhir
		// GradeNilai
		// BobotNilai
		// StatusKRSID
		// Tinggi
		// Final
		// Setara
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA
		// KRSID

		$this->KRSID->ViewValue = $this->KRSID->CurrentValue;
		$this->KRSID->ViewCustomAttributes = "";

		// KHSID
		$this->KHSID->ViewValue = $this->KHSID->CurrentValue;
		$this->KHSID->ViewCustomAttributes = "";

		// StudentID
		$this->StudentID->ViewValue = $this->StudentID->CurrentValue;
		$this->StudentID->ViewCustomAttributes = "";

		// TahunID
		$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
		$this->TahunID->ViewCustomAttributes = "";

		// Sesi
		$this->Sesi->ViewValue = $this->Sesi->CurrentValue;
		$this->Sesi->ViewCustomAttributes = "";

		// JadwalID
		$this->JadwalID->ViewValue = $this->JadwalID->CurrentValue;
		$this->JadwalID->ViewCustomAttributes = "";

		// MKID
		$this->MKID->ViewValue = $this->MKID->CurrentValue;
		$this->MKID->ViewCustomAttributes = "";

		// MKKode
		$this->MKKode->ViewValue = $this->MKKode->CurrentValue;
		$this->MKKode->ViewCustomAttributes = "";

		// SKS
		$this->SKS->ViewValue = $this->SKS->CurrentValue;
		$this->SKS->ViewCustomAttributes = "";

		// Tugas1
		$this->Tugas1->ViewValue = $this->Tugas1->CurrentValue;
		$this->Tugas1->ViewCustomAttributes = "";

		// Tugas2
		$this->Tugas2->ViewValue = $this->Tugas2->CurrentValue;
		$this->Tugas2->ViewCustomAttributes = "";

		// Tugas3
		$this->Tugas3->ViewValue = $this->Tugas3->CurrentValue;
		$this->Tugas3->ViewCustomAttributes = "";

		// Tugas4
		$this->Tugas4->ViewValue = $this->Tugas4->CurrentValue;
		$this->Tugas4->ViewCustomAttributes = "";

		// Tugas5
		$this->Tugas5->ViewValue = $this->Tugas5->CurrentValue;
		$this->Tugas5->ViewCustomAttributes = "";

		// Presensi
		$this->Presensi->ViewValue = $this->Presensi->CurrentValue;
		$this->Presensi->ViewCustomAttributes = "";

		// _Presensi
		$this->_Presensi->ViewValue = $this->_Presensi->CurrentValue;
		$this->_Presensi->ViewCustomAttributes = "";

		// UTS
		$this->UTS->ViewValue = $this->UTS->CurrentValue;
		$this->UTS->ViewCustomAttributes = "";

		// UAS
		$this->UAS->ViewValue = $this->UAS->CurrentValue;
		$this->UAS->ViewCustomAttributes = "";

		// Responsi
		$this->Responsi->ViewValue = $this->Responsi->CurrentValue;
		$this->Responsi->ViewCustomAttributes = "";

		// NilaiAkhir
		$this->NilaiAkhir->ViewValue = $this->NilaiAkhir->CurrentValue;
		$this->NilaiAkhir->ViewCustomAttributes = "";

		// GradeNilai
		$this->GradeNilai->ViewValue = $this->GradeNilai->CurrentValue;
		$this->GradeNilai->ViewCustomAttributes = "";

		// BobotNilai
		$this->BobotNilai->ViewValue = $this->BobotNilai->CurrentValue;
		$this->BobotNilai->ViewCustomAttributes = "";

		// StatusKRSID
		$this->StatusKRSID->ViewValue = $this->StatusKRSID->CurrentValue;
		$this->StatusKRSID->ViewCustomAttributes = "";

		// Tinggi
		$this->Tinggi->ViewValue = $this->Tinggi->CurrentValue;
		$this->Tinggi->ViewCustomAttributes = "";

		// Final
		if (ew_ConvertToBool($this->Final->CurrentValue)) {
			$this->Final->ViewValue = $this->Final->FldTagCaption(1) <> "" ? $this->Final->FldTagCaption(1) : "Y";
		} else {
			$this->Final->ViewValue = $this->Final->FldTagCaption(2) <> "" ? $this->Final->FldTagCaption(2) : "N";
		}
		$this->Final->ViewCustomAttributes = "";

		// Setara
		if (ew_ConvertToBool($this->Setara->CurrentValue)) {
			$this->Setara->ViewValue = $this->Setara->FldTagCaption(1) <> "" ? $this->Setara->FldTagCaption(1) : "Y";
		} else {
			$this->Setara->ViewValue = $this->Setara->FldTagCaption(2) <> "" ? $this->Setara->FldTagCaption(2) : "N";
		}
		$this->Setara->ViewCustomAttributes = "";

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
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

		// KRSID
		$this->KRSID->LinkCustomAttributes = "";
		$this->KRSID->HrefValue = "";
		$this->KRSID->TooltipValue = "";

		// KHSID
		$this->KHSID->LinkCustomAttributes = "";
		$this->KHSID->HrefValue = "";
		$this->KHSID->TooltipValue = "";

		// StudentID
		$this->StudentID->LinkCustomAttributes = "";
		$this->StudentID->HrefValue = "";
		$this->StudentID->TooltipValue = "";

		// TahunID
		$this->TahunID->LinkCustomAttributes = "";
		$this->TahunID->HrefValue = "";
		$this->TahunID->TooltipValue = "";

		// Sesi
		$this->Sesi->LinkCustomAttributes = "";
		$this->Sesi->HrefValue = "";
		$this->Sesi->TooltipValue = "";

		// JadwalID
		$this->JadwalID->LinkCustomAttributes = "";
		$this->JadwalID->HrefValue = "";
		$this->JadwalID->TooltipValue = "";

		// MKID
		$this->MKID->LinkCustomAttributes = "";
		$this->MKID->HrefValue = "";
		$this->MKID->TooltipValue = "";

		// MKKode
		$this->MKKode->LinkCustomAttributes = "";
		$this->MKKode->HrefValue = "";
		$this->MKKode->TooltipValue = "";

		// SKS
		$this->SKS->LinkCustomAttributes = "";
		$this->SKS->HrefValue = "";
		$this->SKS->TooltipValue = "";

		// Tugas1
		$this->Tugas1->LinkCustomAttributes = "";
		$this->Tugas1->HrefValue = "";
		$this->Tugas1->TooltipValue = "";

		// Tugas2
		$this->Tugas2->LinkCustomAttributes = "";
		$this->Tugas2->HrefValue = "";
		$this->Tugas2->TooltipValue = "";

		// Tugas3
		$this->Tugas3->LinkCustomAttributes = "";
		$this->Tugas3->HrefValue = "";
		$this->Tugas3->TooltipValue = "";

		// Tugas4
		$this->Tugas4->LinkCustomAttributes = "";
		$this->Tugas4->HrefValue = "";
		$this->Tugas4->TooltipValue = "";

		// Tugas5
		$this->Tugas5->LinkCustomAttributes = "";
		$this->Tugas5->HrefValue = "";
		$this->Tugas5->TooltipValue = "";

		// Presensi
		$this->Presensi->LinkCustomAttributes = "";
		$this->Presensi->HrefValue = "";
		$this->Presensi->TooltipValue = "";

		// _Presensi
		$this->_Presensi->LinkCustomAttributes = "";
		$this->_Presensi->HrefValue = "";
		$this->_Presensi->TooltipValue = "";

		// UTS
		$this->UTS->LinkCustomAttributes = "";
		$this->UTS->HrefValue = "";
		$this->UTS->TooltipValue = "";

		// UAS
		$this->UAS->LinkCustomAttributes = "";
		$this->UAS->HrefValue = "";
		$this->UAS->TooltipValue = "";

		// Responsi
		$this->Responsi->LinkCustomAttributes = "";
		$this->Responsi->HrefValue = "";
		$this->Responsi->TooltipValue = "";

		// NilaiAkhir
		$this->NilaiAkhir->LinkCustomAttributes = "";
		$this->NilaiAkhir->HrefValue = "";
		$this->NilaiAkhir->TooltipValue = "";

		// GradeNilai
		$this->GradeNilai->LinkCustomAttributes = "";
		$this->GradeNilai->HrefValue = "";
		$this->GradeNilai->TooltipValue = "";

		// BobotNilai
		$this->BobotNilai->LinkCustomAttributes = "";
		$this->BobotNilai->HrefValue = "";
		$this->BobotNilai->TooltipValue = "";

		// StatusKRSID
		$this->StatusKRSID->LinkCustomAttributes = "";
		$this->StatusKRSID->HrefValue = "";
		$this->StatusKRSID->TooltipValue = "";

		// Tinggi
		$this->Tinggi->LinkCustomAttributes = "";
		$this->Tinggi->HrefValue = "";
		$this->Tinggi->TooltipValue = "";

		// Final
		$this->Final->LinkCustomAttributes = "";
		$this->Final->HrefValue = "";
		$this->Final->TooltipValue = "";

		// Setara
		$this->Setara->LinkCustomAttributes = "";
		$this->Setara->HrefValue = "";
		$this->Setara->TooltipValue = "";

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

		// KRSID
		$this->KRSID->EditAttrs["class"] = "form-control";
		$this->KRSID->EditCustomAttributes = "";
		$this->KRSID->EditValue = $this->KRSID->CurrentValue;
		$this->KRSID->ViewCustomAttributes = "";

		// KHSID
		$this->KHSID->EditAttrs["class"] = "form-control";
		$this->KHSID->EditCustomAttributes = "";
		$this->KHSID->EditValue = $this->KHSID->CurrentValue;
		$this->KHSID->PlaceHolder = ew_RemoveHtml($this->KHSID->FldCaption());

		// StudentID
		$this->StudentID->EditAttrs["class"] = "form-control";
		$this->StudentID->EditCustomAttributes = "";
		$this->StudentID->EditValue = $this->StudentID->CurrentValue;
		$this->StudentID->PlaceHolder = ew_RemoveHtml($this->StudentID->FldCaption());

		// TahunID
		$this->TahunID->EditAttrs["class"] = "form-control";
		$this->TahunID->EditCustomAttributes = "";
		$this->TahunID->EditValue = $this->TahunID->CurrentValue;
		$this->TahunID->PlaceHolder = ew_RemoveHtml($this->TahunID->FldCaption());

		// Sesi
		$this->Sesi->EditAttrs["class"] = "form-control";
		$this->Sesi->EditCustomAttributes = "";
		$this->Sesi->EditValue = $this->Sesi->CurrentValue;
		$this->Sesi->PlaceHolder = ew_RemoveHtml($this->Sesi->FldCaption());

		// JadwalID
		$this->JadwalID->EditAttrs["class"] = "form-control";
		$this->JadwalID->EditCustomAttributes = "";
		$this->JadwalID->EditValue = $this->JadwalID->CurrentValue;
		$this->JadwalID->PlaceHolder = ew_RemoveHtml($this->JadwalID->FldCaption());

		// MKID
		$this->MKID->EditAttrs["class"] = "form-control";
		$this->MKID->EditCustomAttributes = "";
		$this->MKID->EditValue = $this->MKID->CurrentValue;
		$this->MKID->PlaceHolder = ew_RemoveHtml($this->MKID->FldCaption());

		// MKKode
		$this->MKKode->EditAttrs["class"] = "form-control";
		$this->MKKode->EditCustomAttributes = "";
		$this->MKKode->EditValue = $this->MKKode->CurrentValue;
		$this->MKKode->PlaceHolder = ew_RemoveHtml($this->MKKode->FldCaption());

		// SKS
		$this->SKS->EditAttrs["class"] = "form-control";
		$this->SKS->EditCustomAttributes = "";
		$this->SKS->EditValue = $this->SKS->CurrentValue;
		$this->SKS->PlaceHolder = ew_RemoveHtml($this->SKS->FldCaption());

		// Tugas1
		$this->Tugas1->EditAttrs["class"] = "form-control";
		$this->Tugas1->EditCustomAttributes = "";
		$this->Tugas1->EditValue = $this->Tugas1->CurrentValue;
		$this->Tugas1->PlaceHolder = ew_RemoveHtml($this->Tugas1->FldCaption());

		// Tugas2
		$this->Tugas2->EditAttrs["class"] = "form-control";
		$this->Tugas2->EditCustomAttributes = "";
		$this->Tugas2->EditValue = $this->Tugas2->CurrentValue;
		$this->Tugas2->PlaceHolder = ew_RemoveHtml($this->Tugas2->FldCaption());

		// Tugas3
		$this->Tugas3->EditAttrs["class"] = "form-control";
		$this->Tugas3->EditCustomAttributes = "";
		$this->Tugas3->EditValue = $this->Tugas3->CurrentValue;
		$this->Tugas3->PlaceHolder = ew_RemoveHtml($this->Tugas3->FldCaption());

		// Tugas4
		$this->Tugas4->EditAttrs["class"] = "form-control";
		$this->Tugas4->EditCustomAttributes = "";
		$this->Tugas4->EditValue = $this->Tugas4->CurrentValue;
		$this->Tugas4->PlaceHolder = ew_RemoveHtml($this->Tugas4->FldCaption());

		// Tugas5
		$this->Tugas5->EditAttrs["class"] = "form-control";
		$this->Tugas5->EditCustomAttributes = "";
		$this->Tugas5->EditValue = $this->Tugas5->CurrentValue;
		$this->Tugas5->PlaceHolder = ew_RemoveHtml($this->Tugas5->FldCaption());

		// Presensi
		$this->Presensi->EditAttrs["class"] = "form-control";
		$this->Presensi->EditCustomAttributes = "";
		$this->Presensi->EditValue = $this->Presensi->CurrentValue;
		$this->Presensi->PlaceHolder = ew_RemoveHtml($this->Presensi->FldCaption());

		// _Presensi
		$this->_Presensi->EditAttrs["class"] = "form-control";
		$this->_Presensi->EditCustomAttributes = "";
		$this->_Presensi->EditValue = $this->_Presensi->CurrentValue;
		$this->_Presensi->PlaceHolder = ew_RemoveHtml($this->_Presensi->FldCaption());

		// UTS
		$this->UTS->EditAttrs["class"] = "form-control";
		$this->UTS->EditCustomAttributes = "";
		$this->UTS->EditValue = $this->UTS->CurrentValue;
		$this->UTS->PlaceHolder = ew_RemoveHtml($this->UTS->FldCaption());

		// UAS
		$this->UAS->EditAttrs["class"] = "form-control";
		$this->UAS->EditCustomAttributes = "";
		$this->UAS->EditValue = $this->UAS->CurrentValue;
		$this->UAS->PlaceHolder = ew_RemoveHtml($this->UAS->FldCaption());

		// Responsi
		$this->Responsi->EditAttrs["class"] = "form-control";
		$this->Responsi->EditCustomAttributes = "";
		$this->Responsi->EditValue = $this->Responsi->CurrentValue;
		$this->Responsi->PlaceHolder = ew_RemoveHtml($this->Responsi->FldCaption());
		if (strval($this->Responsi->EditValue) <> "" && is_numeric($this->Responsi->EditValue)) $this->Responsi->EditValue = ew_FormatNumber($this->Responsi->EditValue, -2, -1, -2, 0);

		// NilaiAkhir
		$this->NilaiAkhir->EditAttrs["class"] = "form-control";
		$this->NilaiAkhir->EditCustomAttributes = "";
		$this->NilaiAkhir->EditValue = $this->NilaiAkhir->CurrentValue;
		$this->NilaiAkhir->PlaceHolder = ew_RemoveHtml($this->NilaiAkhir->FldCaption());
		if (strval($this->NilaiAkhir->EditValue) <> "" && is_numeric($this->NilaiAkhir->EditValue)) $this->NilaiAkhir->EditValue = ew_FormatNumber($this->NilaiAkhir->EditValue, -2, -1, -2, 0);

		// GradeNilai
		$this->GradeNilai->EditAttrs["class"] = "form-control";
		$this->GradeNilai->EditCustomAttributes = "";
		$this->GradeNilai->EditValue = $this->GradeNilai->CurrentValue;
		$this->GradeNilai->PlaceHolder = ew_RemoveHtml($this->GradeNilai->FldCaption());

		// BobotNilai
		$this->BobotNilai->EditAttrs["class"] = "form-control";
		$this->BobotNilai->EditCustomAttributes = "";
		$this->BobotNilai->EditValue = $this->BobotNilai->CurrentValue;
		$this->BobotNilai->PlaceHolder = ew_RemoveHtml($this->BobotNilai->FldCaption());
		if (strval($this->BobotNilai->EditValue) <> "" && is_numeric($this->BobotNilai->EditValue)) $this->BobotNilai->EditValue = ew_FormatNumber($this->BobotNilai->EditValue, -2, -1, -2, 0);

		// StatusKRSID
		$this->StatusKRSID->EditAttrs["class"] = "form-control";
		$this->StatusKRSID->EditCustomAttributes = "";
		$this->StatusKRSID->EditValue = $this->StatusKRSID->CurrentValue;
		$this->StatusKRSID->PlaceHolder = ew_RemoveHtml($this->StatusKRSID->FldCaption());

		// Tinggi
		$this->Tinggi->EditAttrs["class"] = "form-control";
		$this->Tinggi->EditCustomAttributes = "";
		$this->Tinggi->EditValue = $this->Tinggi->CurrentValue;
		$this->Tinggi->PlaceHolder = ew_RemoveHtml($this->Tinggi->FldCaption());

		// Final
		$this->Final->EditCustomAttributes = "";
		$this->Final->EditValue = $this->Final->Options(FALSE);

		// Setara
		$this->Setara->EditCustomAttributes = "";
		$this->Setara->EditValue = $this->Setara->Options(FALSE);

		// Creator
		$this->Creator->EditAttrs["class"] = "form-control";
		$this->Creator->EditCustomAttributes = "";
		$this->Creator->EditValue = $this->Creator->CurrentValue;
		$this->Creator->PlaceHolder = ew_RemoveHtml($this->Creator->FldCaption());

		// CreateDate
		$this->CreateDate->EditAttrs["class"] = "form-control";
		$this->CreateDate->EditCustomAttributes = "";
		$this->CreateDate->EditValue = ew_FormatDateTime($this->CreateDate->CurrentValue, 8);
		$this->CreateDate->PlaceHolder = ew_RemoveHtml($this->CreateDate->FldCaption());

		// Editor
		$this->Editor->EditAttrs["class"] = "form-control";
		$this->Editor->EditCustomAttributes = "";
		$this->Editor->EditValue = $this->Editor->CurrentValue;
		$this->Editor->PlaceHolder = ew_RemoveHtml($this->Editor->FldCaption());

		// EditDate
		$this->EditDate->EditAttrs["class"] = "form-control";
		$this->EditDate->EditCustomAttributes = "";
		$this->EditDate->EditValue = ew_FormatDateTime($this->EditDate->CurrentValue, 8);
		$this->EditDate->PlaceHolder = ew_RemoveHtml($this->EditDate->FldCaption());

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
					if ($this->KRSID->Exportable) $Doc->ExportCaption($this->KRSID);
					if ($this->KHSID->Exportable) $Doc->ExportCaption($this->KHSID);
					if ($this->StudentID->Exportable) $Doc->ExportCaption($this->StudentID);
					if ($this->TahunID->Exportable) $Doc->ExportCaption($this->TahunID);
					if ($this->Sesi->Exportable) $Doc->ExportCaption($this->Sesi);
					if ($this->JadwalID->Exportable) $Doc->ExportCaption($this->JadwalID);
					if ($this->MKID->Exportable) $Doc->ExportCaption($this->MKID);
					if ($this->MKKode->Exportable) $Doc->ExportCaption($this->MKKode);
					if ($this->SKS->Exportable) $Doc->ExportCaption($this->SKS);
					if ($this->Tugas1->Exportable) $Doc->ExportCaption($this->Tugas1);
					if ($this->Tugas2->Exportable) $Doc->ExportCaption($this->Tugas2);
					if ($this->Tugas3->Exportable) $Doc->ExportCaption($this->Tugas3);
					if ($this->Tugas4->Exportable) $Doc->ExportCaption($this->Tugas4);
					if ($this->Tugas5->Exportable) $Doc->ExportCaption($this->Tugas5);
					if ($this->Presensi->Exportable) $Doc->ExportCaption($this->Presensi);
					if ($this->_Presensi->Exportable) $Doc->ExportCaption($this->_Presensi);
					if ($this->UTS->Exportable) $Doc->ExportCaption($this->UTS);
					if ($this->UAS->Exportable) $Doc->ExportCaption($this->UAS);
					if ($this->Responsi->Exportable) $Doc->ExportCaption($this->Responsi);
					if ($this->NilaiAkhir->Exportable) $Doc->ExportCaption($this->NilaiAkhir);
					if ($this->GradeNilai->Exportable) $Doc->ExportCaption($this->GradeNilai);
					if ($this->BobotNilai->Exportable) $Doc->ExportCaption($this->BobotNilai);
					if ($this->StatusKRSID->Exportable) $Doc->ExportCaption($this->StatusKRSID);
					if ($this->Tinggi->Exportable) $Doc->ExportCaption($this->Tinggi);
					if ($this->Final->Exportable) $Doc->ExportCaption($this->Final);
					if ($this->Setara->Exportable) $Doc->ExportCaption($this->Setara);
					if ($this->Creator->Exportable) $Doc->ExportCaption($this->Creator);
					if ($this->CreateDate->Exportable) $Doc->ExportCaption($this->CreateDate);
					if ($this->Editor->Exportable) $Doc->ExportCaption($this->Editor);
					if ($this->EditDate->Exportable) $Doc->ExportCaption($this->EditDate);
					if ($this->NA->Exportable) $Doc->ExportCaption($this->NA);
				} else {
					if ($this->KRSID->Exportable) $Doc->ExportCaption($this->KRSID);
					if ($this->KHSID->Exportable) $Doc->ExportCaption($this->KHSID);
					if ($this->StudentID->Exportable) $Doc->ExportCaption($this->StudentID);
					if ($this->TahunID->Exportable) $Doc->ExportCaption($this->TahunID);
					if ($this->Sesi->Exportable) $Doc->ExportCaption($this->Sesi);
					if ($this->JadwalID->Exportable) $Doc->ExportCaption($this->JadwalID);
					if ($this->MKID->Exportable) $Doc->ExportCaption($this->MKID);
					if ($this->MKKode->Exportable) $Doc->ExportCaption($this->MKKode);
					if ($this->SKS->Exportable) $Doc->ExportCaption($this->SKS);
					if ($this->Tugas1->Exportable) $Doc->ExportCaption($this->Tugas1);
					if ($this->Tugas2->Exportable) $Doc->ExportCaption($this->Tugas2);
					if ($this->Tugas3->Exportable) $Doc->ExportCaption($this->Tugas3);
					if ($this->Tugas4->Exportable) $Doc->ExportCaption($this->Tugas4);
					if ($this->Tugas5->Exportable) $Doc->ExportCaption($this->Tugas5);
					if ($this->Presensi->Exportable) $Doc->ExportCaption($this->Presensi);
					if ($this->_Presensi->Exportable) $Doc->ExportCaption($this->_Presensi);
					if ($this->UTS->Exportable) $Doc->ExportCaption($this->UTS);
					if ($this->UAS->Exportable) $Doc->ExportCaption($this->UAS);
					if ($this->Responsi->Exportable) $Doc->ExportCaption($this->Responsi);
					if ($this->NilaiAkhir->Exportable) $Doc->ExportCaption($this->NilaiAkhir);
					if ($this->GradeNilai->Exportable) $Doc->ExportCaption($this->GradeNilai);
					if ($this->BobotNilai->Exportable) $Doc->ExportCaption($this->BobotNilai);
					if ($this->StatusKRSID->Exportable) $Doc->ExportCaption($this->StatusKRSID);
					if ($this->Tinggi->Exportable) $Doc->ExportCaption($this->Tinggi);
					if ($this->Final->Exportable) $Doc->ExportCaption($this->Final);
					if ($this->Setara->Exportable) $Doc->ExportCaption($this->Setara);
					if ($this->Creator->Exportable) $Doc->ExportCaption($this->Creator);
					if ($this->CreateDate->Exportable) $Doc->ExportCaption($this->CreateDate);
					if ($this->Editor->Exportable) $Doc->ExportCaption($this->Editor);
					if ($this->EditDate->Exportable) $Doc->ExportCaption($this->EditDate);
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
						if ($this->KRSID->Exportable) $Doc->ExportField($this->KRSID);
						if ($this->KHSID->Exportable) $Doc->ExportField($this->KHSID);
						if ($this->StudentID->Exportable) $Doc->ExportField($this->StudentID);
						if ($this->TahunID->Exportable) $Doc->ExportField($this->TahunID);
						if ($this->Sesi->Exportable) $Doc->ExportField($this->Sesi);
						if ($this->JadwalID->Exportable) $Doc->ExportField($this->JadwalID);
						if ($this->MKID->Exportable) $Doc->ExportField($this->MKID);
						if ($this->MKKode->Exportable) $Doc->ExportField($this->MKKode);
						if ($this->SKS->Exportable) $Doc->ExportField($this->SKS);
						if ($this->Tugas1->Exportable) $Doc->ExportField($this->Tugas1);
						if ($this->Tugas2->Exportable) $Doc->ExportField($this->Tugas2);
						if ($this->Tugas3->Exportable) $Doc->ExportField($this->Tugas3);
						if ($this->Tugas4->Exportable) $Doc->ExportField($this->Tugas4);
						if ($this->Tugas5->Exportable) $Doc->ExportField($this->Tugas5);
						if ($this->Presensi->Exportable) $Doc->ExportField($this->Presensi);
						if ($this->_Presensi->Exportable) $Doc->ExportField($this->_Presensi);
						if ($this->UTS->Exportable) $Doc->ExportField($this->UTS);
						if ($this->UAS->Exportable) $Doc->ExportField($this->UAS);
						if ($this->Responsi->Exportable) $Doc->ExportField($this->Responsi);
						if ($this->NilaiAkhir->Exportable) $Doc->ExportField($this->NilaiAkhir);
						if ($this->GradeNilai->Exportable) $Doc->ExportField($this->GradeNilai);
						if ($this->BobotNilai->Exportable) $Doc->ExportField($this->BobotNilai);
						if ($this->StatusKRSID->Exportable) $Doc->ExportField($this->StatusKRSID);
						if ($this->Tinggi->Exportable) $Doc->ExportField($this->Tinggi);
						if ($this->Final->Exportable) $Doc->ExportField($this->Final);
						if ($this->Setara->Exportable) $Doc->ExportField($this->Setara);
						if ($this->Creator->Exportable) $Doc->ExportField($this->Creator);
						if ($this->CreateDate->Exportable) $Doc->ExportField($this->CreateDate);
						if ($this->Editor->Exportable) $Doc->ExportField($this->Editor);
						if ($this->EditDate->Exportable) $Doc->ExportField($this->EditDate);
						if ($this->NA->Exportable) $Doc->ExportField($this->NA);
					} else {
						if ($this->KRSID->Exportable) $Doc->ExportField($this->KRSID);
						if ($this->KHSID->Exportable) $Doc->ExportField($this->KHSID);
						if ($this->StudentID->Exportable) $Doc->ExportField($this->StudentID);
						if ($this->TahunID->Exportable) $Doc->ExportField($this->TahunID);
						if ($this->Sesi->Exportable) $Doc->ExportField($this->Sesi);
						if ($this->JadwalID->Exportable) $Doc->ExportField($this->JadwalID);
						if ($this->MKID->Exportable) $Doc->ExportField($this->MKID);
						if ($this->MKKode->Exportable) $Doc->ExportField($this->MKKode);
						if ($this->SKS->Exportable) $Doc->ExportField($this->SKS);
						if ($this->Tugas1->Exportable) $Doc->ExportField($this->Tugas1);
						if ($this->Tugas2->Exportable) $Doc->ExportField($this->Tugas2);
						if ($this->Tugas3->Exportable) $Doc->ExportField($this->Tugas3);
						if ($this->Tugas4->Exportable) $Doc->ExportField($this->Tugas4);
						if ($this->Tugas5->Exportable) $Doc->ExportField($this->Tugas5);
						if ($this->Presensi->Exportable) $Doc->ExportField($this->Presensi);
						if ($this->_Presensi->Exportable) $Doc->ExportField($this->_Presensi);
						if ($this->UTS->Exportable) $Doc->ExportField($this->UTS);
						if ($this->UAS->Exportable) $Doc->ExportField($this->UAS);
						if ($this->Responsi->Exportable) $Doc->ExportField($this->Responsi);
						if ($this->NilaiAkhir->Exportable) $Doc->ExportField($this->NilaiAkhir);
						if ($this->GradeNilai->Exportable) $Doc->ExportField($this->GradeNilai);
						if ($this->BobotNilai->Exportable) $Doc->ExportField($this->BobotNilai);
						if ($this->StatusKRSID->Exportable) $Doc->ExportField($this->StatusKRSID);
						if ($this->Tinggi->Exportable) $Doc->ExportField($this->Tinggi);
						if ($this->Final->Exportable) $Doc->ExportField($this->Final);
						if ($this->Setara->Exportable) $Doc->ExportField($this->Setara);
						if ($this->Creator->Exportable) $Doc->ExportField($this->Creator);
						if ($this->CreateDate->Exportable) $Doc->ExportField($this->CreateDate);
						if ($this->Editor->Exportable) $Doc->ExportField($this->Editor);
						if ($this->EditDate->Exportable) $Doc->ExportField($this->EditDate);
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
		$table = 'krs';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		global $Language;
		if (!$this->AuditTrailOnAdd) return;
		$table = 'krs';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['KRSID'];

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
		$table = 'krs';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['KRSID'];

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
		$table = 'krs';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['KRSID'];

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
