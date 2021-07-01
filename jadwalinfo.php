<?php

// Global variable for table object
$jadwal = NULL;

//
// Table class for jadwal
//
class cjadwal extends cTable {
	var $AuditTrailOnAdd = TRUE;
	var $AuditTrailOnEdit = TRUE;
	var $AuditTrailOnDelete = TRUE;
	var $AuditTrailOnView = FALSE;
	var $AuditTrailOnViewData = FALSE;
	var $AuditTrailOnSearch = FALSE;
	var $JadwalID;
	var $ProdiID;
	var $TahunID;
	var $Sesi;
	var $Tingkat;
	var $KelasID;
	var $HariID;
	var $JamID;
	var $MKID;
	var $TeacherID;
	var $JamMulai;
	var $JamSelesai;
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
		$this->TableVar = 'jadwal';
		$this->TableName = 'jadwal';
		$this->TableType = 'TABLE';

		// Update Table
		$this->UpdateTable = "`jadwal`";
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

		// JadwalID
		$this->JadwalID = new cField('jadwal', 'jadwal', 'x_JadwalID', 'JadwalID', '`JadwalID`', '`JadwalID`', 20, -1, FALSE, '`JadwalID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'NO');
		$this->JadwalID->Sortable = TRUE; // Allow sort
		$this->JadwalID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['JadwalID'] = &$this->JadwalID;

		// ProdiID
		$this->ProdiID = new cField('jadwal', 'jadwal', 'x_ProdiID', 'ProdiID', '`ProdiID`', '`ProdiID`', 200, -1, FALSE, '`ProdiID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->ProdiID->Sortable = TRUE; // Allow sort
		$this->ProdiID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->ProdiID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['ProdiID'] = &$this->ProdiID;

		// TahunID
		$this->TahunID = new cField('jadwal', 'jadwal', 'x_TahunID', 'TahunID', '`TahunID`', '`TahunID`', 200, -1, FALSE, '`TahunID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TahunID->Sortable = TRUE; // Allow sort
		$this->fields['TahunID'] = &$this->TahunID;

		// Sesi
		$this->Sesi = new cField('jadwal', 'jadwal', 'x_Sesi', 'Sesi', '`Sesi`', '`Sesi`', 16, -1, FALSE, '`Sesi`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->Sesi->Sortable = TRUE; // Allow sort
		$this->Sesi->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->Sesi->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->Sesi->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Sesi'] = &$this->Sesi;

		// Tingkat
		$this->Tingkat = new cField('jadwal', 'jadwal', 'x_Tingkat', 'Tingkat', '`Tingkat`', '`Tingkat`', 200, -1, FALSE, '`Tingkat`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->Tingkat->Sortable = TRUE; // Allow sort
		$this->Tingkat->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->Tingkat->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['Tingkat'] = &$this->Tingkat;

		// KelasID
		$this->KelasID = new cField('jadwal', 'jadwal', 'x_KelasID', 'KelasID', '`KelasID`', '`KelasID`', 200, -1, FALSE, '`KelasID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->KelasID->Sortable = TRUE; // Allow sort
		$this->KelasID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->KelasID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['KelasID'] = &$this->KelasID;

		// HariID
		$this->HariID = new cField('jadwal', 'jadwal', 'x_HariID', 'HariID', '`HariID`', '`HariID`', 2, -1, FALSE, '`HariID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->HariID->Sortable = TRUE; // Allow sort
		$this->HariID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->HariID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->HariID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['HariID'] = &$this->HariID;

		// JamID
		$this->JamID = new cField('jadwal', 'jadwal', 'x_JamID', 'JamID', '`JamID`', '`JamID`', 200, -1, FALSE, '`JamID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->JamID->Sortable = TRUE; // Allow sort
		$this->JamID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->JamID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['JamID'] = &$this->JamID;

		// MKID
		$this->MKID = new cField('jadwal', 'jadwal', 'x_MKID', 'MKID', '`MKID`', '`MKID`', 3, -1, FALSE, '`MKID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->MKID->Sortable = TRUE; // Allow sort
		$this->MKID->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->MKID->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->MKID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['MKID'] = &$this->MKID;

		// TeacherID
		$this->TeacherID = new cField('jadwal', 'jadwal', 'x_TeacherID', 'TeacherID', '`TeacherID`', '`TeacherID`', 200, -1, FALSE, '`TeacherID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TeacherID->Sortable = TRUE; // Allow sort
		$this->fields['TeacherID'] = &$this->TeacherID;

		// JamMulai
		$this->JamMulai = new cField('jadwal', 'jadwal', 'x_JamMulai', 'JamMulai', '`JamMulai`', ew_CastDateFieldForLike('`JamMulai`', 0, "DB"), 134, -1, FALSE, '`JamMulai`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->JamMulai->Sortable = TRUE; // Allow sort
		$this->JamMulai->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_TIME_SEPARATOR"], $Language->Phrase("IncorrectTime"));
		$this->fields['JamMulai'] = &$this->JamMulai;

		// JamSelesai
		$this->JamSelesai = new cField('jadwal', 'jadwal', 'x_JamSelesai', 'JamSelesai', '`JamSelesai`', ew_CastDateFieldForLike('`JamSelesai`', 0, "DB"), 134, -1, FALSE, '`JamSelesai`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->JamSelesai->Sortable = TRUE; // Allow sort
		$this->JamSelesai->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_TIME_SEPARATOR"], $Language->Phrase("IncorrectTime"));
		$this->fields['JamSelesai'] = &$this->JamSelesai;

		// Creator
		$this->Creator = new cField('jadwal', 'jadwal', 'x_Creator', 'Creator', '`Creator`', '`Creator`', 200, -1, FALSE, '`Creator`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Creator->Sortable = TRUE; // Allow sort
		$this->fields['Creator'] = &$this->Creator;

		// CreateDate
		$this->CreateDate = new cField('jadwal', 'jadwal', 'x_CreateDate', 'CreateDate', '`CreateDate`', ew_CastDateFieldForLike('`CreateDate`', 0, "DB"), 135, 0, FALSE, '`CreateDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->CreateDate->Sortable = TRUE; // Allow sort
		$this->CreateDate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['CreateDate'] = &$this->CreateDate;

		// Editor
		$this->Editor = new cField('jadwal', 'jadwal', 'x_Editor', 'Editor', '`Editor`', '`Editor`', 200, -1, FALSE, '`Editor`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Editor->Sortable = TRUE; // Allow sort
		$this->fields['Editor'] = &$this->Editor;

		// EditDate
		$this->EditDate = new cField('jadwal', 'jadwal', 'x_EditDate', 'EditDate', '`EditDate`', ew_CastDateFieldForLike('`EditDate`', 0, "DB"), 135, 0, FALSE, '`EditDate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->EditDate->Sortable = TRUE; // Allow sort
		$this->EditDate->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['EditDate'] = &$this->EditDate;

		// NA
		$this->NA = new cField('jadwal', 'jadwal', 'x_NA', 'NA', '`NA`', '`NA`', 202, -1, FALSE, '`NA`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'HIDDEN');
		$this->NA->Sortable = TRUE; // Allow sort
		$this->NA->FldDataType = EW_DATATYPE_BOOLEAN;
		$this->NA->TrueValue = 'Y';
		$this->NA->FalseValue = 'N';
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`jadwal`";
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
			$this->JadwalID->setDbValue($conn->Insert_ID());
			$rs['JadwalID'] = $this->JadwalID->DbValue;
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
			$fldname = 'JadwalID';
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
			if (array_key_exists('JadwalID', $rs))
				ew_AddFilter($where, ew_QuotedName('JadwalID', $this->DBID) . '=' . ew_QuotedValue($rs['JadwalID'], $this->JadwalID->FldDataType, $this->DBID));
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
		return "`JadwalID` = @JadwalID@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->JadwalID->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@JadwalID@", ew_AdjustSql($this->JadwalID->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
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
			return "jadwallist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "jadwallist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("jadwalview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("jadwalview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "jadwaladd.php?" . $this->UrlParm($parm);
		else
			$url = "jadwaladd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("jadwaledit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("jadwaladd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("jadwaldelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "JadwalID:" . ew_VarToJson($this->JadwalID->CurrentValue, "number", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->JadwalID->CurrentValue)) {
			$sUrl .= "JadwalID=" . urlencode($this->JadwalID->CurrentValue);
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
			if ($isPost && isset($_POST["JadwalID"]))
				$arKeys[] = ew_StripSlashes($_POST["JadwalID"]);
			elseif (isset($_GET["JadwalID"]))
				$arKeys[] = ew_StripSlashes($_GET["JadwalID"]);
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
			$this->JadwalID->CurrentValue = $key;
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
		$this->JadwalID->setDbValue($rs->fields('JadwalID'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->TahunID->setDbValue($rs->fields('TahunID'));
		$this->Sesi->setDbValue($rs->fields('Sesi'));
		$this->Tingkat->setDbValue($rs->fields('Tingkat'));
		$this->KelasID->setDbValue($rs->fields('KelasID'));
		$this->HariID->setDbValue($rs->fields('HariID'));
		$this->JamID->setDbValue($rs->fields('JamID'));
		$this->MKID->setDbValue($rs->fields('MKID'));
		$this->TeacherID->setDbValue($rs->fields('TeacherID'));
		$this->JamMulai->setDbValue($rs->fields('JamMulai'));
		$this->JamSelesai->setDbValue($rs->fields('JamSelesai'));
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
		// JadwalID
		// ProdiID
		// TahunID
		// Sesi
		// Tingkat
		// KelasID
		// HariID
		// JamID
		// MKID
		// TeacherID
		// JamMulai
		// JamSelesai
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA
		// JadwalID

		$this->JadwalID->ViewValue = $this->JadwalID->CurrentValue;
		$this->JadwalID->ViewCustomAttributes = "";

		// ProdiID
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `ProdiID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
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

		// TahunID
		$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
		$this->TahunID->ViewCustomAttributes = "";

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
		$this->Tingkat->CellCssStyle .= "text-align: center;";
		$this->Tingkat->ViewCustomAttributes = "";

		// KelasID
		if (strval($this->KelasID->CurrentValue) <> "") {
			$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->KelasID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
		$sWhereWrk = "";
		$this->KelasID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KelasID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KelasID->ViewValue = $this->KelasID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KelasID->ViewValue = $this->KelasID->CurrentValue;
			}
		} else {
			$this->KelasID->ViewValue = NULL;
		}
		$this->KelasID->ViewCustomAttributes = "";

		// HariID
		if (strval($this->HariID->CurrentValue) <> "") {
			$sFilterWrk = "`HariID`" . ew_SearchString("=", $this->HariID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `HariID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hari`";
		$sWhereWrk = "";
		$this->HariID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->HariID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->HariID->ViewValue = $this->HariID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->HariID->ViewValue = $this->HariID->CurrentValue;
			}
		} else {
			$this->HariID->ViewValue = NULL;
		}
		$this->HariID->ViewCustomAttributes = "";

		// JamID
		if (strval($this->JamID->CurrentValue) <> "") {
			$sFilterWrk = "`JamID`" . ew_SearchString("=", $this->JamID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `JamID`, `JamID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_jamkul`";
		$sWhereWrk = "";
		$this->JamID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `JamID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->JamID->ViewValue = $this->JamID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->JamID->ViewValue = $this->JamID->CurrentValue;
			}
		} else {
			$this->JamID->ViewValue = NULL;
		}
		$this->JamID->ViewCustomAttributes = "";

		// MKID
		if (strval($this->MKID->CurrentValue) <> "") {
			$sFilterWrk = "`MKID`" . ew_SearchString("=", $this->MKID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `MKID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `mk`";
		$sWhereWrk = "";
		$this->MKID->LookupFilters = array("dx1" => '`Nama`');
		$lookuptblfilter = "`Tingkat` in (Tingkat)";
		ew_AddFilter($sWhereWrk, $lookuptblfilter);
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->MKID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->MKID->ViewValue = $this->MKID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->MKID->ViewValue = $this->MKID->CurrentValue;
			}
		} else {
			$this->MKID->ViewValue = NULL;
		}
		$this->MKID->ViewCustomAttributes = "";

		// TeacherID
		$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
		if (strval($this->TeacherID->CurrentValue) <> "") {
			$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->TeacherID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `TeacherID`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
		$sWhereWrk = "";
		$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->TeacherID->ViewValue = $this->TeacherID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
			}
		} else {
			$this->TeacherID->ViewValue = NULL;
		}
		$this->TeacherID->ViewCustomAttributes = "";

		// JamMulai
		$this->JamMulai->ViewValue = $this->JamMulai->CurrentValue;
		$this->JamMulai->ViewCustomAttributes = "";

		// JamSelesai
		$this->JamSelesai->ViewValue = $this->JamSelesai->CurrentValue;
		$this->JamSelesai->ViewCustomAttributes = "";

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
		$this->NA->ViewValue = $this->NA->CurrentValue;
		$this->NA->ViewCustomAttributes = "";

		// JadwalID
		$this->JadwalID->LinkCustomAttributes = "";
		$this->JadwalID->HrefValue = "";
		$this->JadwalID->TooltipValue = "";

		// ProdiID
		$this->ProdiID->LinkCustomAttributes = "";
		$this->ProdiID->HrefValue = "";
		$this->ProdiID->TooltipValue = "";

		// TahunID
		$this->TahunID->LinkCustomAttributes = "";
		$this->TahunID->HrefValue = "";
		$this->TahunID->TooltipValue = "";

		// Sesi
		$this->Sesi->LinkCustomAttributes = "";
		$this->Sesi->HrefValue = "";
		$this->Sesi->TooltipValue = "";

		// Tingkat
		$this->Tingkat->LinkCustomAttributes = "";
		$this->Tingkat->HrefValue = "";
		$this->Tingkat->TooltipValue = "";

		// KelasID
		$this->KelasID->LinkCustomAttributes = "";
		$this->KelasID->HrefValue = "";
		$this->KelasID->TooltipValue = "";

		// HariID
		$this->HariID->LinkCustomAttributes = "";
		$this->HariID->HrefValue = "";
		$this->HariID->TooltipValue = "";

		// JamID
		$this->JamID->LinkCustomAttributes = "";
		$this->JamID->HrefValue = "";
		$this->JamID->TooltipValue = "";

		// MKID
		$this->MKID->LinkCustomAttributes = "";
		$this->MKID->HrefValue = "";
		$this->MKID->TooltipValue = "";

		// TeacherID
		$this->TeacherID->LinkCustomAttributes = "";
		$this->TeacherID->HrefValue = "";
		$this->TeacherID->TooltipValue = "";

		// JamMulai
		$this->JamMulai->LinkCustomAttributes = "";
		$this->JamMulai->HrefValue = "";
		$this->JamMulai->TooltipValue = "";

		// JamSelesai
		$this->JamSelesai->LinkCustomAttributes = "";
		$this->JamSelesai->HrefValue = "";
		$this->JamSelesai->TooltipValue = "";

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

		// JadwalID
		$this->JadwalID->EditAttrs["class"] = "form-control";
		$this->JadwalID->EditCustomAttributes = "";
		$this->JadwalID->EditValue = $this->JadwalID->CurrentValue;
		$this->JadwalID->ViewCustomAttributes = "";

		// ProdiID
		$this->ProdiID->EditAttrs["class"] = "form-control";
		$this->ProdiID->EditCustomAttributes = "";

		// TahunID
		$this->TahunID->EditAttrs["class"] = "form-control";
		$this->TahunID->EditCustomAttributes = "";
		$this->TahunID->EditValue = $this->TahunID->CurrentValue;
		$this->TahunID->PlaceHolder = ew_RemoveHtml($this->TahunID->FldCaption());

		// Sesi
		$this->Sesi->EditAttrs["class"] = "form-control";
		$this->Sesi->EditCustomAttributes = "";

		// Tingkat
		$this->Tingkat->EditAttrs["class"] = "form-control";
		$this->Tingkat->EditCustomAttributes = "";

		// KelasID
		$this->KelasID->EditAttrs["class"] = "form-control";
		$this->KelasID->EditCustomAttributes = "";

		// HariID
		$this->HariID->EditAttrs["class"] = "form-control";
		$this->HariID->EditCustomAttributes = "";

		// JamID
		$this->JamID->EditAttrs["class"] = "form-control";
		$this->JamID->EditCustomAttributes = "";

		// MKID
		$this->MKID->EditAttrs["class"] = "form-control";
		$this->MKID->EditCustomAttributes = "";

		// TeacherID
		$this->TeacherID->EditAttrs["class"] = "form-control";
		$this->TeacherID->EditCustomAttributes = "";
		$this->TeacherID->EditValue = $this->TeacherID->CurrentValue;
		$this->TeacherID->PlaceHolder = ew_RemoveHtml($this->TeacherID->FldCaption());

		// JamMulai
		$this->JamMulai->EditAttrs["class"] = "form-control";
		$this->JamMulai->EditCustomAttributes = "readonly";
		$this->JamMulai->EditValue = $this->JamMulai->CurrentValue;
		$this->JamMulai->PlaceHolder = ew_RemoveHtml($this->JamMulai->FldCaption());

		// JamSelesai
		$this->JamSelesai->EditAttrs["class"] = "form-control";
		$this->JamSelesai->EditCustomAttributes = "readonly";
		$this->JamSelesai->EditValue = $this->JamSelesai->CurrentValue;
		$this->JamSelesai->PlaceHolder = ew_RemoveHtml($this->JamSelesai->FldCaption());

		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		$this->NA->EditAttrs["class"] = "form-control";
		$this->NA->EditCustomAttributes = "";

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
					if ($this->JadwalID->Exportable) $Doc->ExportCaption($this->JadwalID);
					if ($this->ProdiID->Exportable) $Doc->ExportCaption($this->ProdiID);
					if ($this->TahunID->Exportable) $Doc->ExportCaption($this->TahunID);
					if ($this->Sesi->Exportable) $Doc->ExportCaption($this->Sesi);
					if ($this->Tingkat->Exportable) $Doc->ExportCaption($this->Tingkat);
					if ($this->KelasID->Exportable) $Doc->ExportCaption($this->KelasID);
					if ($this->HariID->Exportable) $Doc->ExportCaption($this->HariID);
					if ($this->JamID->Exportable) $Doc->ExportCaption($this->JamID);
					if ($this->MKID->Exportable) $Doc->ExportCaption($this->MKID);
					if ($this->TeacherID->Exportable) $Doc->ExportCaption($this->TeacherID);
					if ($this->JamMulai->Exportable) $Doc->ExportCaption($this->JamMulai);
					if ($this->JamSelesai->Exportable) $Doc->ExportCaption($this->JamSelesai);
					if ($this->Creator->Exportable) $Doc->ExportCaption($this->Creator);
					if ($this->CreateDate->Exportable) $Doc->ExportCaption($this->CreateDate);
					if ($this->Editor->Exportable) $Doc->ExportCaption($this->Editor);
					if ($this->EditDate->Exportable) $Doc->ExportCaption($this->EditDate);
					if ($this->NA->Exportable) $Doc->ExportCaption($this->NA);
				} else {
					if ($this->JadwalID->Exportable) $Doc->ExportCaption($this->JadwalID);
					if ($this->ProdiID->Exportable) $Doc->ExportCaption($this->ProdiID);
					if ($this->TahunID->Exportable) $Doc->ExportCaption($this->TahunID);
					if ($this->Sesi->Exportable) $Doc->ExportCaption($this->Sesi);
					if ($this->Tingkat->Exportable) $Doc->ExportCaption($this->Tingkat);
					if ($this->KelasID->Exportable) $Doc->ExportCaption($this->KelasID);
					if ($this->HariID->Exportable) $Doc->ExportCaption($this->HariID);
					if ($this->JamID->Exportable) $Doc->ExportCaption($this->JamID);
					if ($this->MKID->Exportable) $Doc->ExportCaption($this->MKID);
					if ($this->TeacherID->Exportable) $Doc->ExportCaption($this->TeacherID);
					if ($this->JamMulai->Exportable) $Doc->ExportCaption($this->JamMulai);
					if ($this->JamSelesai->Exportable) $Doc->ExportCaption($this->JamSelesai);
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
						if ($this->JadwalID->Exportable) $Doc->ExportField($this->JadwalID);
						if ($this->ProdiID->Exportable) $Doc->ExportField($this->ProdiID);
						if ($this->TahunID->Exportable) $Doc->ExportField($this->TahunID);
						if ($this->Sesi->Exportable) $Doc->ExportField($this->Sesi);
						if ($this->Tingkat->Exportable) $Doc->ExportField($this->Tingkat);
						if ($this->KelasID->Exportable) $Doc->ExportField($this->KelasID);
						if ($this->HariID->Exportable) $Doc->ExportField($this->HariID);
						if ($this->JamID->Exportable) $Doc->ExportField($this->JamID);
						if ($this->MKID->Exportable) $Doc->ExportField($this->MKID);
						if ($this->TeacherID->Exportable) $Doc->ExportField($this->TeacherID);
						if ($this->JamMulai->Exportable) $Doc->ExportField($this->JamMulai);
						if ($this->JamSelesai->Exportable) $Doc->ExportField($this->JamSelesai);
						if ($this->Creator->Exportable) $Doc->ExportField($this->Creator);
						if ($this->CreateDate->Exportable) $Doc->ExportField($this->CreateDate);
						if ($this->Editor->Exportable) $Doc->ExportField($this->Editor);
						if ($this->EditDate->Exportable) $Doc->ExportField($this->EditDate);
						if ($this->NA->Exportable) $Doc->ExportField($this->NA);
					} else {
						if ($this->JadwalID->Exportable) $Doc->ExportField($this->JadwalID);
						if ($this->ProdiID->Exportable) $Doc->ExportField($this->ProdiID);
						if ($this->TahunID->Exportable) $Doc->ExportField($this->TahunID);
						if ($this->Sesi->Exportable) $Doc->ExportField($this->Sesi);
						if ($this->Tingkat->Exportable) $Doc->ExportField($this->Tingkat);
						if ($this->KelasID->Exportable) $Doc->ExportField($this->KelasID);
						if ($this->HariID->Exportable) $Doc->ExportField($this->HariID);
						if ($this->JamID->Exportable) $Doc->ExportField($this->JamID);
						if ($this->MKID->Exportable) $Doc->ExportField($this->MKID);
						if ($this->TeacherID->Exportable) $Doc->ExportField($this->TeacherID);
						if ($this->JamMulai->Exportable) $Doc->ExportField($this->JamMulai);
						if ($this->JamSelesai->Exportable) $Doc->ExportField($this->JamSelesai);
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
		if (preg_match('/^x(\d)*_JamID$/', $id)) {
			$conn = &$this->Connection();
			$sSqlWrk = "SELECT `JamMulai` AS FIELD0, `JamSelesai` AS FIELD1 FROM `master_jamkul`";
			$sWhereWrk = "(`JamID` = " . ew_QuotedValue($val, EW_DATATYPE_STRING, $this->DBID) . ")";
			$this->JamID->LookupFilters = array();
			$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `JamID` ASC";
			if ($rs = ew_LoadRecordset($sSqlWrk, $conn)) {
				while ($rs && !$rs->EOF) {
					$ar = array();
					$this->JamMulai->setDbValue($rs->fields[0]);
					$this->JamSelesai->setDbValue($rs->fields[1]);
					$this->RowType == EW_ROWTYPE_EDIT;
					$this->RenderEditRow();
					$ar[] = ($this->JamMulai->AutoFillOriginalValue) ? $this->JamMulai->CurrentValue : $this->JamMulai->EditValue;
					$ar[] = ($this->JamSelesai->AutoFillOriginalValue) ? $this->JamSelesai->CurrentValue : $this->JamSelesai->EditValue;
					$rowcnt += 1;
					$rsarr[] = $ar;
					$rs->MoveNext();
				}
				$rs->Close();
			}
		}

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
		$table = 'jadwal';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		global $Language;
		if (!$this->AuditTrailOnAdd) return;
		$table = 'jadwal';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['JadwalID'];

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
		$table = 'jadwal';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['JadwalID'];

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
		$table = 'jadwal';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['JadwalID'];

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
