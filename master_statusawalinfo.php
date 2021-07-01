<?php

// Global variable for table object
$master_statusawal = NULL;

//
// Table class for master_statusawal
//
class cmaster_statusawal extends cTable {
	var $AuditTrailOnAdd = TRUE;
	var $AuditTrailOnEdit = TRUE;
	var $AuditTrailOnDelete = TRUE;
	var $AuditTrailOnView = FALSE;
	var $AuditTrailOnViewData = FALSE;
	var $AuditTrailOnSearch = FALSE;
	var $Urutan;
	var $StatusAwalID;
	var $Nama;
	var $BeliOnline;
	var $BeliFormulir;
	var $JalurKhusus;
	var $TanpaTest;
	var $Catatan;
	var $NA;
	var $PotonganSPI_Prosen;
	var $PotonganSPI_Nominal;
	var $PotonganSPP_Prosen;
	var $PotonganSPP_Nominal;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'master_statusawal';
		$this->TableName = 'master_statusawal';
		$this->TableType = 'TABLE';

		// Update Table
		$this->UpdateTable = "`master_statusawal`";
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

		// Urutan
		$this->Urutan = new cField('master_statusawal', 'master_statusawal', 'x_Urutan', 'Urutan', '`Urutan`', '`Urutan`', 200, -1, FALSE, '`Urutan`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Urutan->Sortable = TRUE; // Allow sort
		$this->fields['Urutan'] = &$this->Urutan;

		// StatusAwalID
		$this->StatusAwalID = new cField('master_statusawal', 'master_statusawal', 'x_StatusAwalID', 'StatusAwalID', '`StatusAwalID`', '`StatusAwalID`', 200, -1, FALSE, '`StatusAwalID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->StatusAwalID->Sortable = TRUE; // Allow sort
		$this->fields['StatusAwalID'] = &$this->StatusAwalID;

		// Nama
		$this->Nama = new cField('master_statusawal', 'master_statusawal', 'x_Nama', 'Nama', '`Nama`', '`Nama`', 200, -1, FALSE, '`Nama`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Nama->Sortable = TRUE; // Allow sort
		$this->fields['Nama'] = &$this->Nama;

		// BeliOnline
		$this->BeliOnline = new cField('master_statusawal', 'master_statusawal', 'x_BeliOnline', 'BeliOnline', '`BeliOnline`', '`BeliOnline`', 202, -1, FALSE, '`BeliOnline`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->BeliOnline->Sortable = TRUE; // Allow sort
		$this->BeliOnline->FldDataType = EW_DATATYPE_BOOLEAN;
		$this->BeliOnline->TrueValue = 'Y';
		$this->BeliOnline->FalseValue = 'N';
		$this->BeliOnline->OptionCount = 2;
		$this->fields['BeliOnline'] = &$this->BeliOnline;

		// BeliFormulir
		$this->BeliFormulir = new cField('master_statusawal', 'master_statusawal', 'x_BeliFormulir', 'BeliFormulir', '`BeliFormulir`', '`BeliFormulir`', 202, -1, FALSE, '`BeliFormulir`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->BeliFormulir->Sortable = TRUE; // Allow sort
		$this->BeliFormulir->FldDataType = EW_DATATYPE_BOOLEAN;
		$this->BeliFormulir->TrueValue = 'Y';
		$this->BeliFormulir->FalseValue = 'N';
		$this->BeliFormulir->OptionCount = 2;
		$this->fields['BeliFormulir'] = &$this->BeliFormulir;

		// JalurKhusus
		$this->JalurKhusus = new cField('master_statusawal', 'master_statusawal', 'x_JalurKhusus', 'JalurKhusus', '`JalurKhusus`', '`JalurKhusus`', 202, -1, FALSE, '`JalurKhusus`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->JalurKhusus->Sortable = TRUE; // Allow sort
		$this->JalurKhusus->FldDataType = EW_DATATYPE_BOOLEAN;
		$this->JalurKhusus->TrueValue = 'Y';
		$this->JalurKhusus->FalseValue = 'N';
		$this->JalurKhusus->OptionCount = 2;
		$this->fields['JalurKhusus'] = &$this->JalurKhusus;

		// TanpaTest
		$this->TanpaTest = new cField('master_statusawal', 'master_statusawal', 'x_TanpaTest', 'TanpaTest', '`TanpaTest`', '`TanpaTest`', 202, -1, FALSE, '`TanpaTest`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->TanpaTest->Sortable = TRUE; // Allow sort
		$this->TanpaTest->FldDataType = EW_DATATYPE_BOOLEAN;
		$this->TanpaTest->TrueValue = 'Y';
		$this->TanpaTest->FalseValue = 'N';
		$this->TanpaTest->OptionCount = 2;
		$this->fields['TanpaTest'] = &$this->TanpaTest;

		// Catatan
		$this->Catatan = new cField('master_statusawal', 'master_statusawal', 'x_Catatan', 'Catatan', '`Catatan`', '`Catatan`', 200, -1, FALSE, '`Catatan`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Catatan->Sortable = TRUE; // Allow sort
		$this->fields['Catatan'] = &$this->Catatan;

		// NA
		$this->NA = new cField('master_statusawal', 'master_statusawal', 'x_NA', 'NA', '`NA`', '`NA`', 202, -1, FALSE, '`NA`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->NA->Sortable = TRUE; // Allow sort
		$this->NA->FldDataType = EW_DATATYPE_BOOLEAN;
		$this->NA->TrueValue = 'Y';
		$this->NA->FalseValue = 'N';
		$this->NA->OptionCount = 2;
		$this->fields['NA'] = &$this->NA;

		// PotonganSPI_Prosen
		$this->PotonganSPI_Prosen = new cField('master_statusawal', 'master_statusawal', 'x_PotonganSPI_Prosen', 'PotonganSPI_Prosen', '`PotonganSPI_Prosen`', '`PotonganSPI_Prosen`', 3, -1, FALSE, '`PotonganSPI_Prosen`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->PotonganSPI_Prosen->Sortable = TRUE; // Allow sort
		$this->PotonganSPI_Prosen->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['PotonganSPI_Prosen'] = &$this->PotonganSPI_Prosen;

		// PotonganSPI_Nominal
		$this->PotonganSPI_Nominal = new cField('master_statusawal', 'master_statusawal', 'x_PotonganSPI_Nominal', 'PotonganSPI_Nominal', '`PotonganSPI_Nominal`', '`PotonganSPI_Nominal`', 3, -1, FALSE, '`PotonganSPI_Nominal`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->PotonganSPI_Nominal->Sortable = TRUE; // Allow sort
		$this->PotonganSPI_Nominal->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['PotonganSPI_Nominal'] = &$this->PotonganSPI_Nominal;

		// PotonganSPP_Prosen
		$this->PotonganSPP_Prosen = new cField('master_statusawal', 'master_statusawal', 'x_PotonganSPP_Prosen', 'PotonganSPP_Prosen', '`PotonganSPP_Prosen`', '`PotonganSPP_Prosen`', 3, -1, FALSE, '`PotonganSPP_Prosen`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->PotonganSPP_Prosen->Sortable = TRUE; // Allow sort
		$this->PotonganSPP_Prosen->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['PotonganSPP_Prosen'] = &$this->PotonganSPP_Prosen;

		// PotonganSPP_Nominal
		$this->PotonganSPP_Nominal = new cField('master_statusawal', 'master_statusawal', 'x_PotonganSPP_Nominal', 'PotonganSPP_Nominal', '`PotonganSPP_Nominal`', '`PotonganSPP_Nominal`', 3, -1, FALSE, '`PotonganSPP_Nominal`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->PotonganSPP_Nominal->Sortable = TRUE; // Allow sort
		$this->PotonganSPP_Nominal->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['PotonganSPP_Nominal'] = &$this->PotonganSPP_Nominal;
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`master_statusawal`";
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
			$fldname = 'StatusAwalID';
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
			if (array_key_exists('StatusAwalID', $rs))
				ew_AddFilter($where, ew_QuotedName('StatusAwalID', $this->DBID) . '=' . ew_QuotedValue($rs['StatusAwalID'], $this->StatusAwalID->FldDataType, $this->DBID));
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
		return "`StatusAwalID` = '@StatusAwalID@'";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		$sKeyFilter = str_replace("@StatusAwalID@", ew_AdjustSql($this->StatusAwalID->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
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
			return "master_statusawallist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "master_statusawallist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("master_statusawalview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("master_statusawalview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "master_statusawaladd.php?" . $this->UrlParm($parm);
		else
			$url = "master_statusawaladd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("master_statusawaledit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("master_statusawaladd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("master_statusawaldelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "StatusAwalID:" . ew_VarToJson($this->StatusAwalID->CurrentValue, "string", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->StatusAwalID->CurrentValue)) {
			$sUrl .= "StatusAwalID=" . urlencode($this->StatusAwalID->CurrentValue);
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
			if ($isPost && isset($_POST["StatusAwalID"]))
				$arKeys[] = ew_StripSlashes($_POST["StatusAwalID"]);
			elseif (isset($_GET["StatusAwalID"]))
				$arKeys[] = ew_StripSlashes($_GET["StatusAwalID"]);
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
			$this->StatusAwalID->CurrentValue = $key;
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
		$this->Urutan->setDbValue($rs->fields('Urutan'));
		$this->StatusAwalID->setDbValue($rs->fields('StatusAwalID'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->BeliOnline->setDbValue($rs->fields('BeliOnline'));
		$this->BeliFormulir->setDbValue($rs->fields('BeliFormulir'));
		$this->JalurKhusus->setDbValue($rs->fields('JalurKhusus'));
		$this->TanpaTest->setDbValue($rs->fields('TanpaTest'));
		$this->Catatan->setDbValue($rs->fields('Catatan'));
		$this->NA->setDbValue($rs->fields('NA'));
		$this->PotonganSPI_Prosen->setDbValue($rs->fields('PotonganSPI_Prosen'));
		$this->PotonganSPI_Nominal->setDbValue($rs->fields('PotonganSPI_Nominal'));
		$this->PotonganSPP_Prosen->setDbValue($rs->fields('PotonganSPP_Prosen'));
		$this->PotonganSPP_Nominal->setDbValue($rs->fields('PotonganSPP_Nominal'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// Urutan
		// StatusAwalID
		// Nama
		// BeliOnline
		// BeliFormulir
		// JalurKhusus
		// TanpaTest
		// Catatan
		// NA
		// PotonganSPI_Prosen
		// PotonganSPI_Nominal
		// PotonganSPP_Prosen
		// PotonganSPP_Nominal
		// Urutan

		$this->Urutan->ViewValue = $this->Urutan->CurrentValue;
		$this->Urutan->ViewCustomAttributes = "";

		// StatusAwalID
		$this->StatusAwalID->ViewValue = $this->StatusAwalID->CurrentValue;
		$this->StatusAwalID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// BeliOnline
		if (ew_ConvertToBool($this->BeliOnline->CurrentValue)) {
			$this->BeliOnline->ViewValue = $this->BeliOnline->FldTagCaption(1) <> "" ? $this->BeliOnline->FldTagCaption(1) : "Y";
		} else {
			$this->BeliOnline->ViewValue = $this->BeliOnline->FldTagCaption(2) <> "" ? $this->BeliOnline->FldTagCaption(2) : "N";
		}
		$this->BeliOnline->ViewCustomAttributes = "";

		// BeliFormulir
		if (ew_ConvertToBool($this->BeliFormulir->CurrentValue)) {
			$this->BeliFormulir->ViewValue = $this->BeliFormulir->FldTagCaption(1) <> "" ? $this->BeliFormulir->FldTagCaption(1) : "Y";
		} else {
			$this->BeliFormulir->ViewValue = $this->BeliFormulir->FldTagCaption(2) <> "" ? $this->BeliFormulir->FldTagCaption(2) : "N";
		}
		$this->BeliFormulir->ViewCustomAttributes = "";

		// JalurKhusus
		if (ew_ConvertToBool($this->JalurKhusus->CurrentValue)) {
			$this->JalurKhusus->ViewValue = $this->JalurKhusus->FldTagCaption(1) <> "" ? $this->JalurKhusus->FldTagCaption(1) : "Y";
		} else {
			$this->JalurKhusus->ViewValue = $this->JalurKhusus->FldTagCaption(2) <> "" ? $this->JalurKhusus->FldTagCaption(2) : "N";
		}
		$this->JalurKhusus->ViewCustomAttributes = "";

		// TanpaTest
		if (ew_ConvertToBool($this->TanpaTest->CurrentValue)) {
			$this->TanpaTest->ViewValue = $this->TanpaTest->FldTagCaption(1) <> "" ? $this->TanpaTest->FldTagCaption(1) : "Y";
		} else {
			$this->TanpaTest->ViewValue = $this->TanpaTest->FldTagCaption(2) <> "" ? $this->TanpaTest->FldTagCaption(2) : "N";
		}
		$this->TanpaTest->ViewCustomAttributes = "";

		// Catatan
		$this->Catatan->ViewValue = $this->Catatan->CurrentValue;
		$this->Catatan->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

		// PotonganSPI_Prosen
		$this->PotonganSPI_Prosen->ViewValue = $this->PotonganSPI_Prosen->CurrentValue;
		$this->PotonganSPI_Prosen->ViewCustomAttributes = "";

		// PotonganSPI_Nominal
		$this->PotonganSPI_Nominal->ViewValue = $this->PotonganSPI_Nominal->CurrentValue;
		$this->PotonganSPI_Nominal->ViewCustomAttributes = "";

		// PotonganSPP_Prosen
		$this->PotonganSPP_Prosen->ViewValue = $this->PotonganSPP_Prosen->CurrentValue;
		$this->PotonganSPP_Prosen->ViewCustomAttributes = "";

		// PotonganSPP_Nominal
		$this->PotonganSPP_Nominal->ViewValue = $this->PotonganSPP_Nominal->CurrentValue;
		$this->PotonganSPP_Nominal->ViewCustomAttributes = "";

		// Urutan
		$this->Urutan->LinkCustomAttributes = "";
		$this->Urutan->HrefValue = "";
		$this->Urutan->TooltipValue = "";

		// StatusAwalID
		$this->StatusAwalID->LinkCustomAttributes = "";
		$this->StatusAwalID->HrefValue = "";
		$this->StatusAwalID->TooltipValue = "";

		// Nama
		$this->Nama->LinkCustomAttributes = "";
		$this->Nama->HrefValue = "";
		$this->Nama->TooltipValue = "";

		// BeliOnline
		$this->BeliOnline->LinkCustomAttributes = "";
		$this->BeliOnline->HrefValue = "";
		$this->BeliOnline->TooltipValue = "";

		// BeliFormulir
		$this->BeliFormulir->LinkCustomAttributes = "";
		$this->BeliFormulir->HrefValue = "";
		$this->BeliFormulir->TooltipValue = "";

		// JalurKhusus
		$this->JalurKhusus->LinkCustomAttributes = "";
		$this->JalurKhusus->HrefValue = "";
		$this->JalurKhusus->TooltipValue = "";

		// TanpaTest
		$this->TanpaTest->LinkCustomAttributes = "";
		$this->TanpaTest->HrefValue = "";
		$this->TanpaTest->TooltipValue = "";

		// Catatan
		$this->Catatan->LinkCustomAttributes = "";
		$this->Catatan->HrefValue = "";
		$this->Catatan->TooltipValue = "";

		// NA
		$this->NA->LinkCustomAttributes = "";
		$this->NA->HrefValue = "";
		$this->NA->TooltipValue = "";

		// PotonganSPI_Prosen
		$this->PotonganSPI_Prosen->LinkCustomAttributes = "";
		$this->PotonganSPI_Prosen->HrefValue = "";
		$this->PotonganSPI_Prosen->TooltipValue = "";

		// PotonganSPI_Nominal
		$this->PotonganSPI_Nominal->LinkCustomAttributes = "";
		$this->PotonganSPI_Nominal->HrefValue = "";
		$this->PotonganSPI_Nominal->TooltipValue = "";

		// PotonganSPP_Prosen
		$this->PotonganSPP_Prosen->LinkCustomAttributes = "";
		$this->PotonganSPP_Prosen->HrefValue = "";
		$this->PotonganSPP_Prosen->TooltipValue = "";

		// PotonganSPP_Nominal
		$this->PotonganSPP_Nominal->LinkCustomAttributes = "";
		$this->PotonganSPP_Nominal->HrefValue = "";
		$this->PotonganSPP_Nominal->TooltipValue = "";

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Render edit row values
	function RenderEditRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

		// Urutan
		$this->Urutan->EditAttrs["class"] = "form-control";
		$this->Urutan->EditCustomAttributes = "";
		$this->Urutan->EditValue = $this->Urutan->CurrentValue;
		$this->Urutan->PlaceHolder = ew_RemoveHtml($this->Urutan->FldCaption());

		// StatusAwalID
		$this->StatusAwalID->EditAttrs["class"] = "form-control";
		$this->StatusAwalID->EditCustomAttributes = "";
		$this->StatusAwalID->EditValue = $this->StatusAwalID->CurrentValue;
		$this->StatusAwalID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->EditAttrs["class"] = "form-control";
		$this->Nama->EditCustomAttributes = "";
		$this->Nama->EditValue = $this->Nama->CurrentValue;
		$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

		// BeliOnline
		$this->BeliOnline->EditCustomAttributes = "";
		$this->BeliOnline->EditValue = $this->BeliOnline->Options(FALSE);

		// BeliFormulir
		$this->BeliFormulir->EditCustomAttributes = "";
		$this->BeliFormulir->EditValue = $this->BeliFormulir->Options(FALSE);

		// JalurKhusus
		$this->JalurKhusus->EditCustomAttributes = "";
		$this->JalurKhusus->EditValue = $this->JalurKhusus->Options(FALSE);

		// TanpaTest
		$this->TanpaTest->EditCustomAttributes = "";
		$this->TanpaTest->EditValue = $this->TanpaTest->Options(FALSE);

		// Catatan
		$this->Catatan->EditAttrs["class"] = "form-control";
		$this->Catatan->EditCustomAttributes = "";
		$this->Catatan->EditValue = $this->Catatan->CurrentValue;
		$this->Catatan->PlaceHolder = ew_RemoveHtml($this->Catatan->FldCaption());

		// NA
		$this->NA->EditCustomAttributes = "";
		$this->NA->EditValue = $this->NA->Options(FALSE);

		// PotonganSPI_Prosen
		$this->PotonganSPI_Prosen->EditAttrs["class"] = "form-control";
		$this->PotonganSPI_Prosen->EditCustomAttributes = "";
		$this->PotonganSPI_Prosen->EditValue = $this->PotonganSPI_Prosen->CurrentValue;
		$this->PotonganSPI_Prosen->PlaceHolder = ew_RemoveHtml($this->PotonganSPI_Prosen->FldCaption());

		// PotonganSPI_Nominal
		$this->PotonganSPI_Nominal->EditAttrs["class"] = "form-control";
		$this->PotonganSPI_Nominal->EditCustomAttributes = "";
		$this->PotonganSPI_Nominal->EditValue = $this->PotonganSPI_Nominal->CurrentValue;
		$this->PotonganSPI_Nominal->PlaceHolder = ew_RemoveHtml($this->PotonganSPI_Nominal->FldCaption());

		// PotonganSPP_Prosen
		$this->PotonganSPP_Prosen->EditAttrs["class"] = "form-control";
		$this->PotonganSPP_Prosen->EditCustomAttributes = "";
		$this->PotonganSPP_Prosen->EditValue = $this->PotonganSPP_Prosen->CurrentValue;
		$this->PotonganSPP_Prosen->PlaceHolder = ew_RemoveHtml($this->PotonganSPP_Prosen->FldCaption());

		// PotonganSPP_Nominal
		$this->PotonganSPP_Nominal->EditAttrs["class"] = "form-control";
		$this->PotonganSPP_Nominal->EditCustomAttributes = "";
		$this->PotonganSPP_Nominal->EditValue = $this->PotonganSPP_Nominal->CurrentValue;
		$this->PotonganSPP_Nominal->PlaceHolder = ew_RemoveHtml($this->PotonganSPP_Nominal->FldCaption());

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
					if ($this->Urutan->Exportable) $Doc->ExportCaption($this->Urutan);
					if ($this->StatusAwalID->Exportable) $Doc->ExportCaption($this->StatusAwalID);
					if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
					if ($this->BeliOnline->Exportable) $Doc->ExportCaption($this->BeliOnline);
					if ($this->BeliFormulir->Exportable) $Doc->ExportCaption($this->BeliFormulir);
					if ($this->JalurKhusus->Exportable) $Doc->ExportCaption($this->JalurKhusus);
					if ($this->TanpaTest->Exportable) $Doc->ExportCaption($this->TanpaTest);
					if ($this->Catatan->Exportable) $Doc->ExportCaption($this->Catatan);
					if ($this->NA->Exportable) $Doc->ExportCaption($this->NA);
					if ($this->PotonganSPI_Prosen->Exportable) $Doc->ExportCaption($this->PotonganSPI_Prosen);
					if ($this->PotonganSPI_Nominal->Exportable) $Doc->ExportCaption($this->PotonganSPI_Nominal);
					if ($this->PotonganSPP_Prosen->Exportable) $Doc->ExportCaption($this->PotonganSPP_Prosen);
					if ($this->PotonganSPP_Nominal->Exportable) $Doc->ExportCaption($this->PotonganSPP_Nominal);
				} else {
					if ($this->Urutan->Exportable) $Doc->ExportCaption($this->Urutan);
					if ($this->StatusAwalID->Exportable) $Doc->ExportCaption($this->StatusAwalID);
					if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
					if ($this->BeliOnline->Exportable) $Doc->ExportCaption($this->BeliOnline);
					if ($this->BeliFormulir->Exportable) $Doc->ExportCaption($this->BeliFormulir);
					if ($this->JalurKhusus->Exportable) $Doc->ExportCaption($this->JalurKhusus);
					if ($this->TanpaTest->Exportable) $Doc->ExportCaption($this->TanpaTest);
					if ($this->Catatan->Exportable) $Doc->ExportCaption($this->Catatan);
					if ($this->NA->Exportable) $Doc->ExportCaption($this->NA);
					if ($this->PotonganSPI_Prosen->Exportable) $Doc->ExportCaption($this->PotonganSPI_Prosen);
					if ($this->PotonganSPI_Nominal->Exportable) $Doc->ExportCaption($this->PotonganSPI_Nominal);
					if ($this->PotonganSPP_Prosen->Exportable) $Doc->ExportCaption($this->PotonganSPP_Prosen);
					if ($this->PotonganSPP_Nominal->Exportable) $Doc->ExportCaption($this->PotonganSPP_Nominal);
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
						if ($this->Urutan->Exportable) $Doc->ExportField($this->Urutan);
						if ($this->StatusAwalID->Exportable) $Doc->ExportField($this->StatusAwalID);
						if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
						if ($this->BeliOnline->Exportable) $Doc->ExportField($this->BeliOnline);
						if ($this->BeliFormulir->Exportable) $Doc->ExportField($this->BeliFormulir);
						if ($this->JalurKhusus->Exportable) $Doc->ExportField($this->JalurKhusus);
						if ($this->TanpaTest->Exportable) $Doc->ExportField($this->TanpaTest);
						if ($this->Catatan->Exportable) $Doc->ExportField($this->Catatan);
						if ($this->NA->Exportable) $Doc->ExportField($this->NA);
						if ($this->PotonganSPI_Prosen->Exportable) $Doc->ExportField($this->PotonganSPI_Prosen);
						if ($this->PotonganSPI_Nominal->Exportable) $Doc->ExportField($this->PotonganSPI_Nominal);
						if ($this->PotonganSPP_Prosen->Exportable) $Doc->ExportField($this->PotonganSPP_Prosen);
						if ($this->PotonganSPP_Nominal->Exportable) $Doc->ExportField($this->PotonganSPP_Nominal);
					} else {
						if ($this->Urutan->Exportable) $Doc->ExportField($this->Urutan);
						if ($this->StatusAwalID->Exportable) $Doc->ExportField($this->StatusAwalID);
						if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
						if ($this->BeliOnline->Exportable) $Doc->ExportField($this->BeliOnline);
						if ($this->BeliFormulir->Exportable) $Doc->ExportField($this->BeliFormulir);
						if ($this->JalurKhusus->Exportable) $Doc->ExportField($this->JalurKhusus);
						if ($this->TanpaTest->Exportable) $Doc->ExportField($this->TanpaTest);
						if ($this->Catatan->Exportable) $Doc->ExportField($this->Catatan);
						if ($this->NA->Exportable) $Doc->ExportField($this->NA);
						if ($this->PotonganSPI_Prosen->Exportable) $Doc->ExportField($this->PotonganSPI_Prosen);
						if ($this->PotonganSPI_Nominal->Exportable) $Doc->ExportField($this->PotonganSPI_Nominal);
						if ($this->PotonganSPP_Prosen->Exportable) $Doc->ExportField($this->PotonganSPP_Prosen);
						if ($this->PotonganSPP_Nominal->Exportable) $Doc->ExportField($this->PotonganSPP_Nominal);
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
		$table = 'master_statusawal';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		global $Language;
		if (!$this->AuditTrailOnAdd) return;
		$table = 'master_statusawal';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['StatusAwalID'];

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
		$table = 'master_statusawal';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['StatusAwalID'];

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
		$table = 'master_statusawal';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['StatusAwalID'];

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
