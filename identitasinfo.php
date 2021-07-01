<?php

// Global variable for table object
$identitas = NULL;

//
// Table class for identitas
//
class cidentitas extends cTable {
	var $AuditTrailOnAdd = TRUE;
	var $AuditTrailOnEdit = TRUE;
	var $AuditTrailOnDelete = TRUE;
	var $AuditTrailOnView = FALSE;
	var $AuditTrailOnViewData = FALSE;
	var $AuditTrailOnSearch = FALSE;
	var $Kode;
	var $KodeHukum;
	var $Nama;
	var $TglMulai;
	var $Alamat1;
	var $Alamat2;
	var $Kota;
	var $KodePos;
	var $Telepon;
	var $Fax;
	var $_Email;
	var $Website;
	var $NoAkta;
	var $TglAkta;
	var $NoSah;
	var $TglSah;
	var $Logo;
	var $StartNoIdentitas;
	var $NoIdentitas;
	var $NA;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'identitas';
		$this->TableName = 'identitas';
		$this->TableType = 'TABLE';

		// Update Table
		$this->UpdateTable = "`identitas`";
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

		// Kode
		$this->Kode = new cField('identitas', 'identitas', 'x_Kode', 'Kode', '`Kode`', '`Kode`', 200, -1, FALSE, '`Kode`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Kode->Sortable = TRUE; // Allow sort
		$this->fields['Kode'] = &$this->Kode;

		// KodeHukum
		$this->KodeHukum = new cField('identitas', 'identitas', 'x_KodeHukum', 'KodeHukum', '`KodeHukum`', '`KodeHukum`', 200, -1, FALSE, '`KodeHukum`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->KodeHukum->Sortable = TRUE; // Allow sort
		$this->fields['KodeHukum'] = &$this->KodeHukum;

		// Nama
		$this->Nama = new cField('identitas', 'identitas', 'x_Nama', 'Nama', '`Nama`', '`Nama`', 200, -1, FALSE, '`Nama`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Nama->Sortable = TRUE; // Allow sort
		$this->fields['Nama'] = &$this->Nama;

		// TglMulai
		$this->TglMulai = new cField('identitas', 'identitas', 'x_TglMulai', 'TglMulai', '`TglMulai`', ew_CastDateFieldForLike('`TglMulai`', 0, "DB"), 133, 0, FALSE, '`TglMulai`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TglMulai->Sortable = TRUE; // Allow sort
		$this->TglMulai->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['TglMulai'] = &$this->TglMulai;

		// Alamat1
		$this->Alamat1 = new cField('identitas', 'identitas', 'x_Alamat1', 'Alamat1', '`Alamat1`', '`Alamat1`', 200, -1, FALSE, '`Alamat1`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Alamat1->Sortable = TRUE; // Allow sort
		$this->fields['Alamat1'] = &$this->Alamat1;

		// Alamat2
		$this->Alamat2 = new cField('identitas', 'identitas', 'x_Alamat2', 'Alamat2', '`Alamat2`', '`Alamat2`', 200, -1, FALSE, '`Alamat2`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Alamat2->Sortable = TRUE; // Allow sort
		$this->fields['Alamat2'] = &$this->Alamat2;

		// Kota
		$this->Kota = new cField('identitas', 'identitas', 'x_Kota', 'Kota', '`Kota`', '`Kota`', 200, -1, FALSE, '`Kota`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Kota->Sortable = TRUE; // Allow sort
		$this->fields['Kota'] = &$this->Kota;

		// KodePos
		$this->KodePos = new cField('identitas', 'identitas', 'x_KodePos', 'KodePos', '`KodePos`', '`KodePos`', 200, -1, FALSE, '`KodePos`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->KodePos->Sortable = TRUE; // Allow sort
		$this->fields['KodePos'] = &$this->KodePos;

		// Telepon
		$this->Telepon = new cField('identitas', 'identitas', 'x_Telepon', 'Telepon', '`Telepon`', '`Telepon`', 200, -1, FALSE, '`Telepon`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Telepon->Sortable = TRUE; // Allow sort
		$this->fields['Telepon'] = &$this->Telepon;

		// Fax
		$this->Fax = new cField('identitas', 'identitas', 'x_Fax', 'Fax', '`Fax`', '`Fax`', 200, -1, FALSE, '`Fax`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Fax->Sortable = TRUE; // Allow sort
		$this->fields['Fax'] = &$this->Fax;

		// Email
		$this->_Email = new cField('identitas', 'identitas', 'x__Email', 'Email', '`Email`', '`Email`', 200, -1, FALSE, '`Email`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->_Email->Sortable = TRUE; // Allow sort
		$this->fields['Email'] = &$this->_Email;

		// Website
		$this->Website = new cField('identitas', 'identitas', 'x_Website', 'Website', '`Website`', '`Website`', 200, -1, FALSE, '`Website`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Website->Sortable = TRUE; // Allow sort
		$this->fields['Website'] = &$this->Website;

		// NoAkta
		$this->NoAkta = new cField('identitas', 'identitas', 'x_NoAkta', 'NoAkta', '`NoAkta`', '`NoAkta`', 200, -1, FALSE, '`NoAkta`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NoAkta->Sortable = TRUE; // Allow sort
		$this->fields['NoAkta'] = &$this->NoAkta;

		// TglAkta
		$this->TglAkta = new cField('identitas', 'identitas', 'x_TglAkta', 'TglAkta', '`TglAkta`', ew_CastDateFieldForLike('`TglAkta`', 0, "DB"), 133, 0, FALSE, '`TglAkta`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TglAkta->Sortable = TRUE; // Allow sort
		$this->TglAkta->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['TglAkta'] = &$this->TglAkta;

		// NoSah
		$this->NoSah = new cField('identitas', 'identitas', 'x_NoSah', 'NoSah', '`NoSah`', '`NoSah`', 200, -1, FALSE, '`NoSah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NoSah->Sortable = TRUE; // Allow sort
		$this->fields['NoSah'] = &$this->NoSah;

		// TglSah
		$this->TglSah = new cField('identitas', 'identitas', 'x_TglSah', 'TglSah', '`TglSah`', ew_CastDateFieldForLike('`TglSah`', 0, "DB"), 133, 0, FALSE, '`TglSah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->TglSah->Sortable = TRUE; // Allow sort
		$this->TglSah->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['TglSah'] = &$this->TglSah;

		// Logo
		$this->Logo = new cField('identitas', 'identitas', 'x_Logo', 'Logo', '`Logo`', '`Logo`', 200, -1, FALSE, '`Logo`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->Logo->Sortable = TRUE; // Allow sort
		$this->fields['Logo'] = &$this->Logo;

		// StartNoIdentitas
		$this->StartNoIdentitas = new cField('identitas', 'identitas', 'x_StartNoIdentitas', 'StartNoIdentitas', '`StartNoIdentitas`', '`StartNoIdentitas`', 20, -1, FALSE, '`StartNoIdentitas`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->StartNoIdentitas->Sortable = TRUE; // Allow sort
		$this->StartNoIdentitas->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['StartNoIdentitas'] = &$this->StartNoIdentitas;

		// NoIdentitas
		$this->NoIdentitas = new cField('identitas', 'identitas', 'x_NoIdentitas', 'NoIdentitas', '`NoIdentitas`', '`NoIdentitas`', 20, -1, FALSE, '`NoIdentitas`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->NoIdentitas->Sortable = TRUE; // Allow sort
		$this->NoIdentitas->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['NoIdentitas'] = &$this->NoIdentitas;

		// NA
		$this->NA = new cField('identitas', 'identitas', 'x_NA', 'NA', '`NA`', '`NA`', 202, -1, FALSE, '`NA`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`identitas`";
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
		return "";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
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
			return "identitaslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "identitaslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("identitasview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("identitasview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "identitasadd.php?" . $this->UrlParm($parm);
		else
			$url = "identitasadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("identitasedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("identitasadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("identitasdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
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
		$this->Kode->setDbValue($rs->fields('Kode'));
		$this->KodeHukum->setDbValue($rs->fields('KodeHukum'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->TglMulai->setDbValue($rs->fields('TglMulai'));
		$this->Alamat1->setDbValue($rs->fields('Alamat1'));
		$this->Alamat2->setDbValue($rs->fields('Alamat2'));
		$this->Kota->setDbValue($rs->fields('Kota'));
		$this->KodePos->setDbValue($rs->fields('KodePos'));
		$this->Telepon->setDbValue($rs->fields('Telepon'));
		$this->Fax->setDbValue($rs->fields('Fax'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Website->setDbValue($rs->fields('Website'));
		$this->NoAkta->setDbValue($rs->fields('NoAkta'));
		$this->TglAkta->setDbValue($rs->fields('TglAkta'));
		$this->NoSah->setDbValue($rs->fields('NoSah'));
		$this->TglSah->setDbValue($rs->fields('TglSah'));
		$this->Logo->setDbValue($rs->fields('Logo'));
		$this->StartNoIdentitas->setDbValue($rs->fields('StartNoIdentitas'));
		$this->NoIdentitas->setDbValue($rs->fields('NoIdentitas'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// Kode
		// KodeHukum
		// Nama
		// TglMulai
		// Alamat1
		// Alamat2
		// Kota
		// KodePos
		// Telepon
		// Fax
		// Email
		// Website
		// NoAkta
		// TglAkta
		// NoSah
		// TglSah
		// Logo
		// StartNoIdentitas
		// NoIdentitas
		// NA
		// Kode

		$this->Kode->ViewValue = $this->Kode->CurrentValue;
		$this->Kode->ViewCustomAttributes = "";

		// KodeHukum
		$this->KodeHukum->ViewValue = $this->KodeHukum->CurrentValue;
		$this->KodeHukum->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// TglMulai
		$this->TglMulai->ViewValue = $this->TglMulai->CurrentValue;
		$this->TglMulai->ViewValue = ew_FormatDateTime($this->TglMulai->ViewValue, 0);
		$this->TglMulai->ViewCustomAttributes = "";

		// Alamat1
		$this->Alamat1->ViewValue = $this->Alamat1->CurrentValue;
		$this->Alamat1->ViewCustomAttributes = "";

		// Alamat2
		$this->Alamat2->ViewValue = $this->Alamat2->CurrentValue;
		$this->Alamat2->ViewCustomAttributes = "";

		// Kota
		$this->Kota->ViewValue = $this->Kota->CurrentValue;
		$this->Kota->ViewCustomAttributes = "";

		// KodePos
		$this->KodePos->ViewValue = $this->KodePos->CurrentValue;
		$this->KodePos->ViewCustomAttributes = "";

		// Telepon
		$this->Telepon->ViewValue = $this->Telepon->CurrentValue;
		$this->Telepon->ViewCustomAttributes = "";

		// Fax
		$this->Fax->ViewValue = $this->Fax->CurrentValue;
		$this->Fax->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// Website
		$this->Website->ViewValue = $this->Website->CurrentValue;
		$this->Website->ViewCustomAttributes = "";

		// NoAkta
		$this->NoAkta->ViewValue = $this->NoAkta->CurrentValue;
		$this->NoAkta->ViewCustomAttributes = "";

		// TglAkta
		$this->TglAkta->ViewValue = $this->TglAkta->CurrentValue;
		$this->TglAkta->ViewValue = ew_FormatDateTime($this->TglAkta->ViewValue, 0);
		$this->TglAkta->ViewCustomAttributes = "";

		// NoSah
		$this->NoSah->ViewValue = $this->NoSah->CurrentValue;
		$this->NoSah->ViewCustomAttributes = "";

		// TglSah
		$this->TglSah->ViewValue = $this->TglSah->CurrentValue;
		$this->TglSah->ViewValue = ew_FormatDateTime($this->TglSah->ViewValue, 0);
		$this->TglSah->ViewCustomAttributes = "";

		// Logo
		$this->Logo->ViewValue = $this->Logo->CurrentValue;
		$this->Logo->ViewCustomAttributes = "";

		// StartNoIdentitas
		$this->StartNoIdentitas->ViewValue = $this->StartNoIdentitas->CurrentValue;
		$this->StartNoIdentitas->ViewCustomAttributes = "";

		// NoIdentitas
		$this->NoIdentitas->ViewValue = $this->NoIdentitas->CurrentValue;
		$this->NoIdentitas->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

		// Kode
		$this->Kode->LinkCustomAttributes = "";
		$this->Kode->HrefValue = "";
		$this->Kode->TooltipValue = "";

		// KodeHukum
		$this->KodeHukum->LinkCustomAttributes = "";
		$this->KodeHukum->HrefValue = "";
		$this->KodeHukum->TooltipValue = "";

		// Nama
		$this->Nama->LinkCustomAttributes = "";
		$this->Nama->HrefValue = "";
		$this->Nama->TooltipValue = "";

		// TglMulai
		$this->TglMulai->LinkCustomAttributes = "";
		$this->TglMulai->HrefValue = "";
		$this->TglMulai->TooltipValue = "";

		// Alamat1
		$this->Alamat1->LinkCustomAttributes = "";
		$this->Alamat1->HrefValue = "";
		$this->Alamat1->TooltipValue = "";

		// Alamat2
		$this->Alamat2->LinkCustomAttributes = "";
		$this->Alamat2->HrefValue = "";
		$this->Alamat2->TooltipValue = "";

		// Kota
		$this->Kota->LinkCustomAttributes = "";
		$this->Kota->HrefValue = "";
		$this->Kota->TooltipValue = "";

		// KodePos
		$this->KodePos->LinkCustomAttributes = "";
		$this->KodePos->HrefValue = "";
		$this->KodePos->TooltipValue = "";

		// Telepon
		$this->Telepon->LinkCustomAttributes = "";
		$this->Telepon->HrefValue = "";
		$this->Telepon->TooltipValue = "";

		// Fax
		$this->Fax->LinkCustomAttributes = "";
		$this->Fax->HrefValue = "";
		$this->Fax->TooltipValue = "";

		// Email
		$this->_Email->LinkCustomAttributes = "";
		$this->_Email->HrefValue = "";
		$this->_Email->TooltipValue = "";

		// Website
		$this->Website->LinkCustomAttributes = "";
		$this->Website->HrefValue = "";
		$this->Website->TooltipValue = "";

		// NoAkta
		$this->NoAkta->LinkCustomAttributes = "";
		$this->NoAkta->HrefValue = "";
		$this->NoAkta->TooltipValue = "";

		// TglAkta
		$this->TglAkta->LinkCustomAttributes = "";
		$this->TglAkta->HrefValue = "";
		$this->TglAkta->TooltipValue = "";

		// NoSah
		$this->NoSah->LinkCustomAttributes = "";
		$this->NoSah->HrefValue = "";
		$this->NoSah->TooltipValue = "";

		// TglSah
		$this->TglSah->LinkCustomAttributes = "";
		$this->TglSah->HrefValue = "";
		$this->TglSah->TooltipValue = "";

		// Logo
		$this->Logo->LinkCustomAttributes = "";
		$this->Logo->HrefValue = "";
		$this->Logo->TooltipValue = "";

		// StartNoIdentitas
		$this->StartNoIdentitas->LinkCustomAttributes = "";
		$this->StartNoIdentitas->HrefValue = "";
		$this->StartNoIdentitas->TooltipValue = "";

		// NoIdentitas
		$this->NoIdentitas->LinkCustomAttributes = "";
		$this->NoIdentitas->HrefValue = "";
		$this->NoIdentitas->TooltipValue = "";

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

		// Kode
		$this->Kode->EditAttrs["class"] = "form-control";
		$this->Kode->EditCustomAttributes = "";
		$this->Kode->EditValue = $this->Kode->CurrentValue;
		$this->Kode->PlaceHolder = ew_RemoveHtml($this->Kode->FldCaption());

		// KodeHukum
		$this->KodeHukum->EditAttrs["class"] = "form-control";
		$this->KodeHukum->EditCustomAttributes = "";
		$this->KodeHukum->EditValue = $this->KodeHukum->CurrentValue;
		$this->KodeHukum->PlaceHolder = ew_RemoveHtml($this->KodeHukum->FldCaption());

		// Nama
		$this->Nama->EditAttrs["class"] = "form-control";
		$this->Nama->EditCustomAttributes = "";
		$this->Nama->EditValue = $this->Nama->CurrentValue;
		$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

		// TglMulai
		$this->TglMulai->EditAttrs["class"] = "form-control";
		$this->TglMulai->EditCustomAttributes = "";
		$this->TglMulai->EditValue = ew_FormatDateTime($this->TglMulai->CurrentValue, 8);
		$this->TglMulai->PlaceHolder = ew_RemoveHtml($this->TglMulai->FldCaption());

		// Alamat1
		$this->Alamat1->EditAttrs["class"] = "form-control";
		$this->Alamat1->EditCustomAttributes = "";
		$this->Alamat1->EditValue = $this->Alamat1->CurrentValue;
		$this->Alamat1->PlaceHolder = ew_RemoveHtml($this->Alamat1->FldCaption());

		// Alamat2
		$this->Alamat2->EditAttrs["class"] = "form-control";
		$this->Alamat2->EditCustomAttributes = "";
		$this->Alamat2->EditValue = $this->Alamat2->CurrentValue;
		$this->Alamat2->PlaceHolder = ew_RemoveHtml($this->Alamat2->FldCaption());

		// Kota
		$this->Kota->EditAttrs["class"] = "form-control";
		$this->Kota->EditCustomAttributes = "";
		$this->Kota->EditValue = $this->Kota->CurrentValue;
		$this->Kota->PlaceHolder = ew_RemoveHtml($this->Kota->FldCaption());

		// KodePos
		$this->KodePos->EditAttrs["class"] = "form-control";
		$this->KodePos->EditCustomAttributes = "";
		$this->KodePos->EditValue = $this->KodePos->CurrentValue;
		$this->KodePos->PlaceHolder = ew_RemoveHtml($this->KodePos->FldCaption());

		// Telepon
		$this->Telepon->EditAttrs["class"] = "form-control";
		$this->Telepon->EditCustomAttributes = "";
		$this->Telepon->EditValue = $this->Telepon->CurrentValue;
		$this->Telepon->PlaceHolder = ew_RemoveHtml($this->Telepon->FldCaption());

		// Fax
		$this->Fax->EditAttrs["class"] = "form-control";
		$this->Fax->EditCustomAttributes = "";
		$this->Fax->EditValue = $this->Fax->CurrentValue;
		$this->Fax->PlaceHolder = ew_RemoveHtml($this->Fax->FldCaption());

		// Email
		$this->_Email->EditAttrs["class"] = "form-control";
		$this->_Email->EditCustomAttributes = "";
		$this->_Email->EditValue = $this->_Email->CurrentValue;
		$this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

		// Website
		$this->Website->EditAttrs["class"] = "form-control";
		$this->Website->EditCustomAttributes = "";
		$this->Website->EditValue = $this->Website->CurrentValue;
		$this->Website->PlaceHolder = ew_RemoveHtml($this->Website->FldCaption());

		// NoAkta
		$this->NoAkta->EditAttrs["class"] = "form-control";
		$this->NoAkta->EditCustomAttributes = "";
		$this->NoAkta->EditValue = $this->NoAkta->CurrentValue;
		$this->NoAkta->PlaceHolder = ew_RemoveHtml($this->NoAkta->FldCaption());

		// TglAkta
		$this->TglAkta->EditAttrs["class"] = "form-control";
		$this->TglAkta->EditCustomAttributes = "";
		$this->TglAkta->EditValue = ew_FormatDateTime($this->TglAkta->CurrentValue, 8);
		$this->TglAkta->PlaceHolder = ew_RemoveHtml($this->TglAkta->FldCaption());

		// NoSah
		$this->NoSah->EditAttrs["class"] = "form-control";
		$this->NoSah->EditCustomAttributes = "";
		$this->NoSah->EditValue = $this->NoSah->CurrentValue;
		$this->NoSah->PlaceHolder = ew_RemoveHtml($this->NoSah->FldCaption());

		// TglSah
		$this->TglSah->EditAttrs["class"] = "form-control";
		$this->TglSah->EditCustomAttributes = "";
		$this->TglSah->EditValue = ew_FormatDateTime($this->TglSah->CurrentValue, 8);
		$this->TglSah->PlaceHolder = ew_RemoveHtml($this->TglSah->FldCaption());

		// Logo
		$this->Logo->EditAttrs["class"] = "form-control";
		$this->Logo->EditCustomAttributes = "";
		$this->Logo->EditValue = $this->Logo->CurrentValue;
		$this->Logo->PlaceHolder = ew_RemoveHtml($this->Logo->FldCaption());

		// StartNoIdentitas
		$this->StartNoIdentitas->EditAttrs["class"] = "form-control";
		$this->StartNoIdentitas->EditCustomAttributes = "";
		$this->StartNoIdentitas->EditValue = $this->StartNoIdentitas->CurrentValue;
		$this->StartNoIdentitas->PlaceHolder = ew_RemoveHtml($this->StartNoIdentitas->FldCaption());

		// NoIdentitas
		$this->NoIdentitas->EditAttrs["class"] = "form-control";
		$this->NoIdentitas->EditCustomAttributes = "";
		$this->NoIdentitas->EditValue = $this->NoIdentitas->CurrentValue;
		$this->NoIdentitas->PlaceHolder = ew_RemoveHtml($this->NoIdentitas->FldCaption());

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
					if ($this->Kode->Exportable) $Doc->ExportCaption($this->Kode);
					if ($this->KodeHukum->Exportable) $Doc->ExportCaption($this->KodeHukum);
					if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
					if ($this->TglMulai->Exportable) $Doc->ExportCaption($this->TglMulai);
					if ($this->Alamat1->Exportable) $Doc->ExportCaption($this->Alamat1);
					if ($this->Alamat2->Exportable) $Doc->ExportCaption($this->Alamat2);
					if ($this->Kota->Exportable) $Doc->ExportCaption($this->Kota);
					if ($this->KodePos->Exportable) $Doc->ExportCaption($this->KodePos);
					if ($this->Telepon->Exportable) $Doc->ExportCaption($this->Telepon);
					if ($this->Fax->Exportable) $Doc->ExportCaption($this->Fax);
					if ($this->_Email->Exportable) $Doc->ExportCaption($this->_Email);
					if ($this->Website->Exportable) $Doc->ExportCaption($this->Website);
					if ($this->NoAkta->Exportable) $Doc->ExportCaption($this->NoAkta);
					if ($this->TglAkta->Exportable) $Doc->ExportCaption($this->TglAkta);
					if ($this->NoSah->Exportable) $Doc->ExportCaption($this->NoSah);
					if ($this->TglSah->Exportable) $Doc->ExportCaption($this->TglSah);
					if ($this->Logo->Exportable) $Doc->ExportCaption($this->Logo);
					if ($this->StartNoIdentitas->Exportable) $Doc->ExportCaption($this->StartNoIdentitas);
					if ($this->NoIdentitas->Exportable) $Doc->ExportCaption($this->NoIdentitas);
					if ($this->NA->Exportable) $Doc->ExportCaption($this->NA);
				} else {
					if ($this->Kode->Exportable) $Doc->ExportCaption($this->Kode);
					if ($this->KodeHukum->Exportable) $Doc->ExportCaption($this->KodeHukum);
					if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
					if ($this->TglMulai->Exportable) $Doc->ExportCaption($this->TglMulai);
					if ($this->Alamat1->Exportable) $Doc->ExportCaption($this->Alamat1);
					if ($this->Alamat2->Exportable) $Doc->ExportCaption($this->Alamat2);
					if ($this->Kota->Exportable) $Doc->ExportCaption($this->Kota);
					if ($this->KodePos->Exportable) $Doc->ExportCaption($this->KodePos);
					if ($this->Telepon->Exportable) $Doc->ExportCaption($this->Telepon);
					if ($this->Fax->Exportable) $Doc->ExportCaption($this->Fax);
					if ($this->_Email->Exportable) $Doc->ExportCaption($this->_Email);
					if ($this->Website->Exportable) $Doc->ExportCaption($this->Website);
					if ($this->NoAkta->Exportable) $Doc->ExportCaption($this->NoAkta);
					if ($this->TglAkta->Exportable) $Doc->ExportCaption($this->TglAkta);
					if ($this->NoSah->Exportable) $Doc->ExportCaption($this->NoSah);
					if ($this->TglSah->Exportable) $Doc->ExportCaption($this->TglSah);
					if ($this->Logo->Exportable) $Doc->ExportCaption($this->Logo);
					if ($this->StartNoIdentitas->Exportable) $Doc->ExportCaption($this->StartNoIdentitas);
					if ($this->NoIdentitas->Exportable) $Doc->ExportCaption($this->NoIdentitas);
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
						if ($this->Kode->Exportable) $Doc->ExportField($this->Kode);
						if ($this->KodeHukum->Exportable) $Doc->ExportField($this->KodeHukum);
						if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
						if ($this->TglMulai->Exportable) $Doc->ExportField($this->TglMulai);
						if ($this->Alamat1->Exportable) $Doc->ExportField($this->Alamat1);
						if ($this->Alamat2->Exportable) $Doc->ExportField($this->Alamat2);
						if ($this->Kota->Exportable) $Doc->ExportField($this->Kota);
						if ($this->KodePos->Exportable) $Doc->ExportField($this->KodePos);
						if ($this->Telepon->Exportable) $Doc->ExportField($this->Telepon);
						if ($this->Fax->Exportable) $Doc->ExportField($this->Fax);
						if ($this->_Email->Exportable) $Doc->ExportField($this->_Email);
						if ($this->Website->Exportable) $Doc->ExportField($this->Website);
						if ($this->NoAkta->Exportable) $Doc->ExportField($this->NoAkta);
						if ($this->TglAkta->Exportable) $Doc->ExportField($this->TglAkta);
						if ($this->NoSah->Exportable) $Doc->ExportField($this->NoSah);
						if ($this->TglSah->Exportable) $Doc->ExportField($this->TglSah);
						if ($this->Logo->Exportable) $Doc->ExportField($this->Logo);
						if ($this->StartNoIdentitas->Exportable) $Doc->ExportField($this->StartNoIdentitas);
						if ($this->NoIdentitas->Exportable) $Doc->ExportField($this->NoIdentitas);
						if ($this->NA->Exportable) $Doc->ExportField($this->NA);
					} else {
						if ($this->Kode->Exportable) $Doc->ExportField($this->Kode);
						if ($this->KodeHukum->Exportable) $Doc->ExportField($this->KodeHukum);
						if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
						if ($this->TglMulai->Exportable) $Doc->ExportField($this->TglMulai);
						if ($this->Alamat1->Exportable) $Doc->ExportField($this->Alamat1);
						if ($this->Alamat2->Exportable) $Doc->ExportField($this->Alamat2);
						if ($this->Kota->Exportable) $Doc->ExportField($this->Kota);
						if ($this->KodePos->Exportable) $Doc->ExportField($this->KodePos);
						if ($this->Telepon->Exportable) $Doc->ExportField($this->Telepon);
						if ($this->Fax->Exportable) $Doc->ExportField($this->Fax);
						if ($this->_Email->Exportable) $Doc->ExportField($this->_Email);
						if ($this->Website->Exportable) $Doc->ExportField($this->Website);
						if ($this->NoAkta->Exportable) $Doc->ExportField($this->NoAkta);
						if ($this->TglAkta->Exportable) $Doc->ExportField($this->TglAkta);
						if ($this->NoSah->Exportable) $Doc->ExportField($this->NoSah);
						if ($this->TglSah->Exportable) $Doc->ExportField($this->TglSah);
						if ($this->Logo->Exportable) $Doc->ExportField($this->Logo);
						if ($this->StartNoIdentitas->Exportable) $Doc->ExportField($this->StartNoIdentitas);
						if ($this->NoIdentitas->Exportable) $Doc->ExportField($this->NoIdentitas);
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
		$table = 'identitas';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		global $Language;
		if (!$this->AuditTrailOnAdd) return;
		$table = 'identitas';

		// Get key value
		$key = "";

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
		$table = 'identitas';

		// Get key value
		$key = "";

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
		$table = 'identitas';

		// Get key value
		$key = "";

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
