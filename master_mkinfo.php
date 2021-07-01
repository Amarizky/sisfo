<?php

// Global variable for table object
$master_mk = NULL;

//
// Table class for master_mk
//
class cmaster_mk extends cTable {
	var $AuditTrailOnAdd = TRUE;
	var $AuditTrailOnEdit = TRUE;
	var $AuditTrailOnDelete = TRUE;
	var $AuditTrailOnView = FALSE;
	var $AuditTrailOnViewData = FALSE;
	var $AuditTrailOnSearch = FALSE;
	var $MKID;
	var $KampusID;
	var $ProdiID;
	var $KurikulumID;
	var $MKKode;
	var $Nama;
	var $Singkatan;
	var $Tingkat;
	var $Sesi;
	var $Wajib;
	var $Deskripsi;
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
		$this->TableVar = 'master_mk';
		$this->TableName = 'master_mk';
		$this->TableType = 'TABLE';

		// Update Table
		$this->UpdateTable = "`master_mk`";
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->ExportExcelPageOrientation = ""; // Page orientation (PHPExcel only)
		$this->ExportExcelPageSize = ""; // Page size (PHPExcel only)
		$this->DetailAdd = TRUE; // Allow detail add
		$this->DetailEdit = TRUE; // Allow detail edit
		$this->DetailView = TRUE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 25;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// MKID
		$this->MKID = new cField('master_mk', 'master_mk', 'x_MKID', 'MKID', '`MKID`', '`MKID`', 20, -1, FALSE, '`MKID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'NO');
		$this->MKID->Sortable = TRUE; // Allow sort
		$this->MKID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['MKID'] = &$this->MKID;

		// KampusID
		$this->KampusID = new cField('master_mk', 'master_mk', 'x_KampusID', 'KampusID', '`KampusID`', '`KampusID`', 200, -1, FALSE, '`KampusID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KampusID->Sortable = TRUE; // Allow sort
		$this->KampusID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KampusID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['KampusID'] = &$this->KampusID;

		// ProdiID
		$this->ProdiID = new cField('master_mk', 'master_mk', 'x_ProdiID', 'ProdiID', '`ProdiID`', '`ProdiID`', 200, -1, FALSE, '`ProdiID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->ProdiID->Sortable = TRUE; // Allow sort
		$this->ProdiID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->ProdiID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['ProdiID'] = &$this->ProdiID;

		// KurikulumID
		$this->KurikulumID = new cField('master_mk', 'master_mk', 'x_KurikulumID', 'KurikulumID', '`KurikulumID`', '`KurikulumID`', 3, -1, FALSE, '`KurikulumID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KurikulumID->Sortable = TRUE; // Allow sort
		$this->KurikulumID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KurikulumID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->KurikulumID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['KurikulumID'] = &$this->KurikulumID;

		// MKKode
		$this->MKKode = new cField('master_mk', 'master_mk', 'x_MKKode', 'MKKode', '`MKKode`', '`MKKode`', 200, -1, FALSE, '`MKKode`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->MKKode->Sortable = TRUE; // Allow sort
		$this->fields['MKKode'] = &$this->MKKode;

		// Nama
		$this->Nama = new cField('master_mk', 'master_mk', 'x_Nama', 'Nama', '`Nama`', '`Nama`', 200, -1, FALSE, '`Nama`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Nama->Sortable = TRUE; // Allow sort
		$this->fields['Nama'] = &$this->Nama;

		// Singkatan
		$this->Singkatan = new cField('master_mk', 'master_mk', 'x_Singkatan', 'Singkatan', '`Singkatan`', '`Singkatan`', 200, -1, FALSE, '`Singkatan`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Singkatan->Sortable = TRUE; // Allow sort
		$this->fields['Singkatan'] = &$this->Singkatan;

		// Tingkat
		$this->Tingkat = new cField('master_mk', 'master_mk', 'x_Tingkat', 'Tingkat', '`Tingkat`', '`Tingkat`', 200, -1, FALSE, '`Tingkat`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->Tingkat->Sortable = TRUE; // Allow sort
		$this->Tingkat->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->Tingkat->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['Tingkat'] = &$this->Tingkat;

		// Sesi
		$this->Sesi = new cField('master_mk', 'master_mk', 'x_Sesi', 'Sesi', '`Sesi`', '`Sesi`', 3, -1, FALSE, '`Sesi`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->Sesi->Sortable = TRUE; // Allow sort
		$this->Sesi->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->Sesi->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->Sesi->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Sesi'] = &$this->Sesi;

		// Wajib
		$this->Wajib = new cField('master_mk', 'master_mk', 'x_Wajib', 'Wajib', '`Wajib`', '`Wajib`', 202, -1, FALSE, '`Wajib`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->Wajib->Sortable = TRUE; // Allow sort
		$this->Wajib->FldDataType = EW_DATATYPE_BOOLEAN;
		$this->Wajib->TrueValue = 'Y';
		$this->Wajib->FalseValue = 'N';
		$this->Wajib->OptionCount = 2;
		$this->fields['Wajib'] = &$this->Wajib;

		// Deskripsi
		$this->Deskripsi = new cField('master_mk', 'master_mk', 'x_Deskripsi', 'Deskripsi', '`Deskripsi`', '`Deskripsi`', 201, -1, FALSE, '`Deskripsi`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXTAREA');
		$this->Deskripsi->Sortable = TRUE; // Allow sort
		$this->fields['Deskripsi'] = &$this->Deskripsi;

		// Creator
		$this->Creator = new cField('master_mk', 'master_mk', 'x_Creator', 'Creator', '`Creator`', '`Creator`', 200, -1, FALSE, '`Creator`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Creator->Sortable = TRUE; // Allow sort
		$this->fields['Creator'] = &$this->Creator;

		// CreateDate
		$this->CreateDate = new cField('master_mk', 'master_mk', 'x_CreateDate', 'CreateDate', '`CreateDate`', ew_CastDateFieldForLike('`CreateDate`', 0, "DB"), 135, 0, FALSE, '`CreateDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->CreateDate->Sortable = TRUE; // Allow sort
		$this->CreateDate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['CreateDate'] = &$this->CreateDate;

		// Editor
		$this->Editor = new cField('master_mk', 'master_mk', 'x_Editor', 'Editor', '`Editor`', ew_CastDateFieldForLike('`Editor`', 0, "DB"), 135, 0, FALSE, '`Editor`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Editor->Sortable = TRUE; // Allow sort
		$this->Editor->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['Editor'] = &$this->Editor;

		// EditDate
		$this->EditDate = new cField('master_mk', 'master_mk', 'x_EditDate', 'EditDate', '`EditDate`', '`EditDate`', 200, -1, FALSE, '`EditDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->EditDate->Sortable = TRUE; // Allow sort
		$this->fields['EditDate'] = &$this->EditDate;

		// NA
		$this->NA = new cField('master_mk', 'master_mk', 'x_NA', 'NA', '`NA`', '`NA`', 202, -1, FALSE, '`NA`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
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

	// Current master table name
	function getCurrentMasterTable() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_MASTER_TABLE];
	}

	function setCurrentMasterTable($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_MASTER_TABLE] = $v;
	}

	// Session master WHERE clause
	function GetMasterFilter() {

		// Master filter
		$sMasterFilter = "";
		if ($this->getCurrentMasterTable() == "kurikulum") {
			if ($this->KurikulumID->getSessionValue() <> "")
				$sMasterFilter .= "`KurikulumID`=" . ew_QuotedValue($this->KurikulumID->getSessionValue(), EW_DATATYPE_NUMBER, "DB");
			else
				return "";
			if ($this->ProdiID->getSessionValue() <> "")
				$sMasterFilter .= " AND `ProdiID`=" . ew_QuotedValue($this->ProdiID->getSessionValue(), EW_DATATYPE_STRING, "DB");
			else
				return "";
			if ($this->KampusID->getSessionValue() <> "")
				$sMasterFilter .= " AND `KampusID`=" . ew_QuotedValue($this->KampusID->getSessionValue(), EW_DATATYPE_STRING, "DB");
			else
				return "";
		}
		return $sMasterFilter;
	}

	// Session detail WHERE clause
	function GetDetailFilter() {

		// Detail filter
		$sDetailFilter = "";
		if ($this->getCurrentMasterTable() == "kurikulum") {
			if ($this->KurikulumID->getSessionValue() <> "")
				$sDetailFilter .= "`KurikulumID`=" . ew_QuotedValue($this->KurikulumID->getSessionValue(), EW_DATATYPE_NUMBER, "DB");
			else
				return "";
			if ($this->ProdiID->getSessionValue() <> "")
				$sDetailFilter .= " AND `ProdiID`=" . ew_QuotedValue($this->ProdiID->getSessionValue(), EW_DATATYPE_STRING, "DB");
			else
				return "";
			if ($this->KampusID->getSessionValue() <> "")
				$sDetailFilter .= " AND `KampusID`=" . ew_QuotedValue($this->KampusID->getSessionValue(), EW_DATATYPE_STRING, "DB");
			else
				return "";
		}
		return $sDetailFilter;
	}

	// Master filter
	function SqlMasterFilter_kurikulum() {
		return "`KurikulumID`=@KurikulumID@ AND `ProdiID`='@ProdiID@' AND `KampusID`='@KampusID@'";
	}

	// Detail filter
	function SqlDetailFilter_kurikulum() {
		return "`KurikulumID`=@KurikulumID@ AND `ProdiID`='@ProdiID@' AND `KampusID`='@KampusID@'";
	}

	// Table level SQL
	var $_SqlFrom = "";

	function getSqlFrom() { // From
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`master_mk`";
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
			$this->MKID->setDbValue($conn->Insert_ID());
			$rs['MKID'] = $this->MKID->DbValue;
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
			$fldname = 'MKID';
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
			if (array_key_exists('MKID', $rs))
				ew_AddFilter($where, ew_QuotedName('MKID', $this->DBID) . '=' . ew_QuotedValue($rs['MKID'], $this->MKID->FldDataType, $this->DBID));
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
		return "`MKID` = @MKID@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->MKID->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@MKID@", ew_AdjustSql($this->MKID->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
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
			return "master_mklist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "master_mklist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("master_mkview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("master_mkview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "master_mkadd.php?" . $this->UrlParm($parm);
		else
			$url = "master_mkadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("master_mkedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("master_mkadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("master_mkdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		if ($this->getCurrentMasterTable() == "kurikulum" && strpos($url, EW_TABLE_SHOW_MASTER . "=") === FALSE) {
			$url .= (strpos($url, "?") !== FALSE ? "&" : "?") . EW_TABLE_SHOW_MASTER . "=" . $this->getCurrentMasterTable();
			$url .= "&fk_KurikulumID=" . urlencode($this->KurikulumID->CurrentValue);
			$url .= "&fk_ProdiID=" . urlencode($this->ProdiID->CurrentValue);
			$url .= "&fk_KampusID=" . urlencode($this->KampusID->CurrentValue);
		}
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "MKID:" . ew_VarToJson($this->MKID->CurrentValue, "number", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->MKID->CurrentValue)) {
			$sUrl .= "MKID=" . urlencode($this->MKID->CurrentValue);
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
			if ($isPost && isset($_POST["MKID"]))
				$arKeys[] = ew_StripSlashes($_POST["MKID"]);
			elseif (isset($_GET["MKID"]))
				$arKeys[] = ew_StripSlashes($_GET["MKID"]);
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
			$this->MKID->CurrentValue = $key;
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
		$this->MKID->setDbValue($rs->fields('MKID'));
		$this->KampusID->setDbValue($rs->fields('KampusID'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->KurikulumID->setDbValue($rs->fields('KurikulumID'));
		$this->MKKode->setDbValue($rs->fields('MKKode'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->Singkatan->setDbValue($rs->fields('Singkatan'));
		$this->Tingkat->setDbValue($rs->fields('Tingkat'));
		$this->Sesi->setDbValue($rs->fields('Sesi'));
		$this->Wajib->setDbValue($rs->fields('Wajib'));
		$this->Deskripsi->setDbValue($rs->fields('Deskripsi'));
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
		// MKID
		// KampusID
		// ProdiID
		// KurikulumID
		// MKKode
		// Nama
		// Singkatan
		// Tingkat
		// Sesi
		// Wajib
		// Deskripsi
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA
		// MKID

		$this->MKID->ViewValue = $this->MKID->CurrentValue;
		$this->MKID->ViewCustomAttributes = "";

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
		$sSqlWrk .= " ORDER BY `ProdiID` ASC";
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

		// KurikulumID
		if (strval($this->KurikulumID->CurrentValue) <> "") {
			$sFilterWrk = "`KurikulumID`" . ew_SearchString("=", $this->KurikulumID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `KurikulumID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kurikulum`";
		$sWhereWrk = "";
		$this->KurikulumID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KurikulumID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KurikulumID->ViewValue = $this->KurikulumID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KurikulumID->ViewValue = $this->KurikulumID->CurrentValue;
			}
		} else {
			$this->KurikulumID->ViewValue = NULL;
		}
		$this->KurikulumID->ViewCustomAttributes = "";

		// MKKode
		$this->MKKode->ViewValue = $this->MKKode->CurrentValue;
		$this->MKKode->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// Singkatan
		$this->Singkatan->ViewValue = $this->Singkatan->CurrentValue;
		$this->Singkatan->ViewCustomAttributes = "";

		// Tingkat
		if (strval($this->Tingkat->CurrentValue) <> "") {
			$sFilterWrk = "`Tingkat`" . ew_SearchString("=", $this->Tingkat->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
		$sWhereWrk = "";
		$this->Tingkat->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Tingkat->ViewValue = $this->Tingkat->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Tingkat->ViewValue = $this->Tingkat->CurrentValue;
			}
		} else {
			$this->Tingkat->ViewValue = NULL;
		}
		$this->Tingkat->ViewCustomAttributes = "";

		// Sesi
		if (strval($this->Sesi->CurrentValue) <> "") {
			$sFilterWrk = "`Sesi`" . ew_SearchString("=", $this->Sesi->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `Sesi`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_sesi`";
		$sWhereWrk = "";
		$this->Sesi->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Sesi->ViewValue = $this->Sesi->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Sesi->ViewValue = $this->Sesi->CurrentValue;
			}
		} else {
			$this->Sesi->ViewValue = NULL;
		}
		$this->Sesi->ViewCustomAttributes = "";

		// Wajib
		if (ew_ConvertToBool($this->Wajib->CurrentValue)) {
			$this->Wajib->ViewValue = $this->Wajib->FldTagCaption(1) <> "" ? $this->Wajib->FldTagCaption(1) : "Y";
		} else {
			$this->Wajib->ViewValue = $this->Wajib->FldTagCaption(2) <> "" ? $this->Wajib->FldTagCaption(2) : "N";
		}
		$this->Wajib->ViewCustomAttributes = "";

		// Deskripsi
		$this->Deskripsi->ViewValue = $this->Deskripsi->CurrentValue;
		$this->Deskripsi->ViewCustomAttributes = "";

		// Creator
		$this->Creator->ViewValue = $this->Creator->CurrentValue;
		$this->Creator->ViewCustomAttributes = "";

		// CreateDate
		$this->CreateDate->ViewValue = $this->CreateDate->CurrentValue;
		$this->CreateDate->ViewValue = ew_FormatDateTime($this->CreateDate->ViewValue, 0);
		$this->CreateDate->ViewCustomAttributes = "";

		// Editor
		$this->Editor->ViewValue = $this->Editor->CurrentValue;
		$this->Editor->ViewValue = ew_FormatDateTime($this->Editor->ViewValue, 0);
		$this->Editor->ViewCustomAttributes = "";

		// EditDate
		$this->EditDate->ViewValue = $this->EditDate->CurrentValue;
		$this->EditDate->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

		// MKID
		$this->MKID->LinkCustomAttributes = "";
		$this->MKID->HrefValue = "";
		$this->MKID->TooltipValue = "";

		// KampusID
		$this->KampusID->LinkCustomAttributes = "";
		$this->KampusID->HrefValue = "";
		$this->KampusID->TooltipValue = "";

		// ProdiID
		$this->ProdiID->LinkCustomAttributes = "";
		$this->ProdiID->HrefValue = "";
		$this->ProdiID->TooltipValue = "";

		// KurikulumID
		$this->KurikulumID->LinkCustomAttributes = "";
		$this->KurikulumID->HrefValue = "";
		$this->KurikulumID->TooltipValue = "";

		// MKKode
		$this->MKKode->LinkCustomAttributes = "";
		$this->MKKode->HrefValue = "";
		$this->MKKode->TooltipValue = "";

		// Nama
		$this->Nama->LinkCustomAttributes = "";
		$this->Nama->HrefValue = "";
		$this->Nama->TooltipValue = "";

		// Singkatan
		$this->Singkatan->LinkCustomAttributes = "";
		$this->Singkatan->HrefValue = "";
		$this->Singkatan->TooltipValue = "";

		// Tingkat
		$this->Tingkat->LinkCustomAttributes = "";
		$this->Tingkat->HrefValue = "";
		$this->Tingkat->TooltipValue = "";

		// Sesi
		$this->Sesi->LinkCustomAttributes = "";
		$this->Sesi->HrefValue = "";
		$this->Sesi->TooltipValue = "";

		// Wajib
		$this->Wajib->LinkCustomAttributes = "";
		$this->Wajib->HrefValue = "";
		$this->Wajib->TooltipValue = "";

		// Deskripsi
		$this->Deskripsi->LinkCustomAttributes = "";
		$this->Deskripsi->HrefValue = "";
		$this->Deskripsi->TooltipValue = "";

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

		// MKID
		$this->MKID->EditAttrs["class"] = "form-control";
		$this->MKID->EditCustomAttributes = "";
		$this->MKID->EditValue = $this->MKID->CurrentValue;
		$this->MKID->ViewCustomAttributes = "";

		// KampusID
		$this->KampusID->EditAttrs["class"] = "form-control";
		$this->KampusID->EditCustomAttributes = "";
		if ($this->KampusID->getSessionValue() <> "") {
			$this->KampusID->CurrentValue = $this->KampusID->getSessionValue();
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
		} else {
		}

		// ProdiID
		$this->ProdiID->EditAttrs["class"] = "form-control";
		$this->ProdiID->EditCustomAttributes = "";
		if ($this->ProdiID->getSessionValue() <> "") {
			$this->ProdiID->CurrentValue = $this->ProdiID->getSessionValue();
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->ProdiID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `ProdiID` ASC";
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
		} else {
		}

		// KurikulumID
		$this->KurikulumID->EditAttrs["class"] = "form-control";
		$this->KurikulumID->EditCustomAttributes = "";
		if ($this->KurikulumID->getSessionValue() <> "") {
			$this->KurikulumID->CurrentValue = $this->KurikulumID->getSessionValue();
		if (strval($this->KurikulumID->CurrentValue) <> "") {
			$sFilterWrk = "`KurikulumID`" . ew_SearchString("=", $this->KurikulumID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `KurikulumID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kurikulum`";
		$sWhereWrk = "";
		$this->KurikulumID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KurikulumID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KurikulumID->ViewValue = $this->KurikulumID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KurikulumID->ViewValue = $this->KurikulumID->CurrentValue;
			}
		} else {
			$this->KurikulumID->ViewValue = NULL;
		}
		$this->KurikulumID->ViewCustomAttributes = "";
		} else {
		}

		// MKKode
		$this->MKKode->EditAttrs["class"] = "form-control";
		$this->MKKode->EditCustomAttributes = "";
		$this->MKKode->EditValue = $this->MKKode->CurrentValue;
		$this->MKKode->PlaceHolder = ew_RemoveHtml($this->MKKode->FldCaption());

		// Nama
		$this->Nama->EditAttrs["class"] = "form-control";
		$this->Nama->EditCustomAttributes = "";
		$this->Nama->EditValue = $this->Nama->CurrentValue;
		$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

		// Singkatan
		$this->Singkatan->EditAttrs["class"] = "form-control";
		$this->Singkatan->EditCustomAttributes = "";
		$this->Singkatan->EditValue = $this->Singkatan->CurrentValue;
		$this->Singkatan->PlaceHolder = ew_RemoveHtml($this->Singkatan->FldCaption());

		// Tingkat
		$this->Tingkat->EditAttrs["class"] = "form-control";
		$this->Tingkat->EditCustomAttributes = "";

		// Sesi
		$this->Sesi->EditAttrs["class"] = "form-control";
		$this->Sesi->EditCustomAttributes = "";

		// Wajib
		$this->Wajib->EditCustomAttributes = "";
		$this->Wajib->EditValue = $this->Wajib->Options(FALSE);

		// Deskripsi
		$this->Deskripsi->EditAttrs["class"] = "form-control";
		$this->Deskripsi->EditCustomAttributes = "";
		$this->Deskripsi->EditValue = $this->Deskripsi->CurrentValue;
		$this->Deskripsi->PlaceHolder = ew_RemoveHtml($this->Deskripsi->FldCaption());

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
					if ($this->MKID->Exportable) $Doc->ExportCaption($this->MKID);
					if ($this->KampusID->Exportable) $Doc->ExportCaption($this->KampusID);
					if ($this->ProdiID->Exportable) $Doc->ExportCaption($this->ProdiID);
					if ($this->KurikulumID->Exportable) $Doc->ExportCaption($this->KurikulumID);
					if ($this->MKKode->Exportable) $Doc->ExportCaption($this->MKKode);
					if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
					if ($this->Singkatan->Exportable) $Doc->ExportCaption($this->Singkatan);
					if ($this->Tingkat->Exportable) $Doc->ExportCaption($this->Tingkat);
					if ($this->Sesi->Exportable) $Doc->ExportCaption($this->Sesi);
					if ($this->Wajib->Exportable) $Doc->ExportCaption($this->Wajib);
					if ($this->Deskripsi->Exportable) $Doc->ExportCaption($this->Deskripsi);
					if ($this->Creator->Exportable) $Doc->ExportCaption($this->Creator);
					if ($this->CreateDate->Exportable) $Doc->ExportCaption($this->CreateDate);
					if ($this->Editor->Exportable) $Doc->ExportCaption($this->Editor);
					if ($this->EditDate->Exportable) $Doc->ExportCaption($this->EditDate);
					if ($this->NA->Exportable) $Doc->ExportCaption($this->NA);
				} else {
					if ($this->MKID->Exportable) $Doc->ExportCaption($this->MKID);
					if ($this->KampusID->Exportable) $Doc->ExportCaption($this->KampusID);
					if ($this->ProdiID->Exportable) $Doc->ExportCaption($this->ProdiID);
					if ($this->KurikulumID->Exportable) $Doc->ExportCaption($this->KurikulumID);
					if ($this->MKKode->Exportable) $Doc->ExportCaption($this->MKKode);
					if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
					if ($this->Singkatan->Exportable) $Doc->ExportCaption($this->Singkatan);
					if ($this->Tingkat->Exportable) $Doc->ExportCaption($this->Tingkat);
					if ($this->Sesi->Exportable) $Doc->ExportCaption($this->Sesi);
					if ($this->Wajib->Exportable) $Doc->ExportCaption($this->Wajib);
					if ($this->Deskripsi->Exportable) $Doc->ExportCaption($this->Deskripsi);
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
						if ($this->MKID->Exportable) $Doc->ExportField($this->MKID);
						if ($this->KampusID->Exportable) $Doc->ExportField($this->KampusID);
						if ($this->ProdiID->Exportable) $Doc->ExportField($this->ProdiID);
						if ($this->KurikulumID->Exportable) $Doc->ExportField($this->KurikulumID);
						if ($this->MKKode->Exportable) $Doc->ExportField($this->MKKode);
						if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
						if ($this->Singkatan->Exportable) $Doc->ExportField($this->Singkatan);
						if ($this->Tingkat->Exportable) $Doc->ExportField($this->Tingkat);
						if ($this->Sesi->Exportable) $Doc->ExportField($this->Sesi);
						if ($this->Wajib->Exportable) $Doc->ExportField($this->Wajib);
						if ($this->Deskripsi->Exportable) $Doc->ExportField($this->Deskripsi);
						if ($this->Creator->Exportable) $Doc->ExportField($this->Creator);
						if ($this->CreateDate->Exportable) $Doc->ExportField($this->CreateDate);
						if ($this->Editor->Exportable) $Doc->ExportField($this->Editor);
						if ($this->EditDate->Exportable) $Doc->ExportField($this->EditDate);
						if ($this->NA->Exportable) $Doc->ExportField($this->NA);
					} else {
						if ($this->MKID->Exportable) $Doc->ExportField($this->MKID);
						if ($this->KampusID->Exportable) $Doc->ExportField($this->KampusID);
						if ($this->ProdiID->Exportable) $Doc->ExportField($this->ProdiID);
						if ($this->KurikulumID->Exportable) $Doc->ExportField($this->KurikulumID);
						if ($this->MKKode->Exportable) $Doc->ExportField($this->MKKode);
						if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
						if ($this->Singkatan->Exportable) $Doc->ExportField($this->Singkatan);
						if ($this->Tingkat->Exportable) $Doc->ExportField($this->Tingkat);
						if ($this->Sesi->Exportable) $Doc->ExportField($this->Sesi);
						if ($this->Wajib->Exportable) $Doc->ExportField($this->Wajib);
						if ($this->Deskripsi->Exportable) $Doc->ExportField($this->Deskripsi);
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
		$table = 'master_mk';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		global $Language;
		if (!$this->AuditTrailOnAdd) return;
		$table = 'master_mk';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['MKID'];

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
		$table = 'master_mk';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['MKID'];

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
		$table = 'master_mk';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['MKID'];

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
