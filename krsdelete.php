<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "krsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$krs_delete = NULL; // Initialize page object first

class ckrs_delete extends ckrs {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'krs';

	// Page object name
	var $PageObjName = 'krs_delete';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage() {
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage() {
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage() {
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		global $UserTable, $UserTableConn;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (krs)
		if (!isset($GLOBALS["krs"]) || get_class($GLOBALS["krs"]) == "ckrs") {
			$GLOBALS["krs"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["krs"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'krs', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);

		// User table object (users)
		if (!isset($UserTable)) {
			$UserTable = new cusers();
			$UserTableConn = Conn($UserTable->DBID);
		}
	}

	//
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loaded();
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("krslist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->KRSID->SetVisibility();
		$this->KRSID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->KHSID->SetVisibility();
		$this->StudentID->SetVisibility();
		$this->TahunID->SetVisibility();
		$this->Sesi->SetVisibility();
		$this->JadwalID->SetVisibility();
		$this->MKID->SetVisibility();
		$this->MKKode->SetVisibility();
		$this->SKS->SetVisibility();
		$this->Tugas1->SetVisibility();
		$this->Tugas2->SetVisibility();
		$this->Tugas3->SetVisibility();
		$this->Tugas4->SetVisibility();
		$this->Tugas5->SetVisibility();
		$this->Presensi->SetVisibility();
		$this->_Presensi->SetVisibility();
		$this->UTS->SetVisibility();
		$this->UAS->SetVisibility();
		$this->Responsi->SetVisibility();
		$this->NilaiAkhir->SetVisibility();
		$this->GradeNilai->SetVisibility();
		$this->BobotNilai->SetVisibility();
		$this->StatusKRSID->SetVisibility();
		$this->Tinggi->SetVisibility();
		$this->Final->SetVisibility();
		$this->Setara->SetVisibility();
		$this->Creator->SetVisibility();
		$this->CreateDate->SetVisibility();
		$this->Editor->SetVisibility();
		$this->EditDate->SetVisibility();
		$this->NA->SetVisibility();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $krs;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($krs);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("krslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in krs class, krsinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} elseif (@$_GET["a_delete"] == "1") {
			$this->CurrentAction = "D"; // Delete record directly
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		if ($this->CurrentAction == "D") {
			$this->SendEmail = TRUE; // Send email on delete success
			if ($this->DeleteRows()) { // Delete rows
				if ($this->getSuccessMessage() == "")
					$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
				$this->Page_Terminate($this->getReturnUrl()); // Return to caller
			} else { // Delete failed
				$this->CurrentAction = "I"; // Display record
			}
		}
		if ($this->CurrentAction == "I") { // Load records for display
			if ($this->Recordset = $this->LoadRecordset())
				$this->TotalRecs = $this->Recordset->RecordCount(); // Get record count
			if ($this->TotalRecs <= 0) { // No record found, exit
				if ($this->Recordset)
					$this->Recordset->Close();
				$this->Page_Terminate("krslist.php"); // Return to list
			}
		}
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {

		// Load List page SQL
		$sSql = $this->SelectSQL();
		$conn = &$this->Connection();

		// Load recordset
		$dbtype = ew_GetConnectionType($this->DBID);
		if ($this->UseSelectLimit) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			if ($dbtype == "MSSQL") {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset, array("_hasOrderBy" => trim($this->getOrderBy()) || trim($this->getSessionOrderBy())));
			} else {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset);
			}
			$conn->raiseErrorFn = '';
		} else {
			$rs = ew_LoadRecordset($sSql, $conn);
		}

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
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

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->KRSID->DbValue = $row['KRSID'];
		$this->KHSID->DbValue = $row['KHSID'];
		$this->StudentID->DbValue = $row['StudentID'];
		$this->TahunID->DbValue = $row['TahunID'];
		$this->Sesi->DbValue = $row['Sesi'];
		$this->JadwalID->DbValue = $row['JadwalID'];
		$this->MKID->DbValue = $row['MKID'];
		$this->MKKode->DbValue = $row['MKKode'];
		$this->SKS->DbValue = $row['SKS'];
		$this->Tugas1->DbValue = $row['Tugas1'];
		$this->Tugas2->DbValue = $row['Tugas2'];
		$this->Tugas3->DbValue = $row['Tugas3'];
		$this->Tugas4->DbValue = $row['Tugas4'];
		$this->Tugas5->DbValue = $row['Tugas5'];
		$this->Presensi->DbValue = $row['Presensi'];
		$this->_Presensi->DbValue = $row['_Presensi'];
		$this->UTS->DbValue = $row['UTS'];
		$this->UAS->DbValue = $row['UAS'];
		$this->Responsi->DbValue = $row['Responsi'];
		$this->NilaiAkhir->DbValue = $row['NilaiAkhir'];
		$this->GradeNilai->DbValue = $row['GradeNilai'];
		$this->BobotNilai->DbValue = $row['BobotNilai'];
		$this->StatusKRSID->DbValue = $row['StatusKRSID'];
		$this->Tinggi->DbValue = $row['Tinggi'];
		$this->Final->DbValue = $row['Final'];
		$this->Setara->DbValue = $row['Setara'];
		$this->Creator->DbValue = $row['Creator'];
		$this->CreateDate->DbValue = $row['CreateDate'];
		$this->Editor->DbValue = $row['Editor'];
		$this->EditDate->DbValue = $row['EditDate'];
		$this->NA->DbValue = $row['NA'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Convert decimal values if posted back

		if ($this->Responsi->FormValue == $this->Responsi->CurrentValue && is_numeric(ew_StrToFloat($this->Responsi->CurrentValue)))
			$this->Responsi->CurrentValue = ew_StrToFloat($this->Responsi->CurrentValue);

		// Convert decimal values if posted back
		if ($this->NilaiAkhir->FormValue == $this->NilaiAkhir->CurrentValue && is_numeric(ew_StrToFloat($this->NilaiAkhir->CurrentValue)))
			$this->NilaiAkhir->CurrentValue = ew_StrToFloat($this->NilaiAkhir->CurrentValue);

		// Convert decimal values if posted back
		if ($this->BobotNilai->FormValue == $this->BobotNilai->CurrentValue && is_numeric(ew_StrToFloat($this->BobotNilai->CurrentValue)))
			$this->BobotNilai->CurrentValue = ew_StrToFloat($this->BobotNilai->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
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

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

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
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $Language, $Security;
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$rows = ($rs) ? $rs->GetRows() : array();
		$conn->BeginTrans();
		if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteBegin")); // Batch delete begin

		// Clone old rows
		$rsold = $rows;
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['KRSID'];
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteSuccess")); // Batch delete success
		} else {
			$conn->RollbackTrans(); // Rollback changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteRollback")); // Batch delete rollback
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("krslist.php"), "", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		}
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($krs_delete)) $krs_delete = new ckrs_delete();

// Page init
$krs_delete->Page_Init();

// Page main
$krs_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$krs_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "delete";
var CurrentForm = fkrsdelete = new ew_Form("fkrsdelete", "delete");

// Form_CustomValidate event
fkrsdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fkrsdelete.ValidateRequired = true;
<?php } else { ?>
fkrsdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fkrsdelete.Lists["x_Final"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrsdelete.Lists["x_Final"].Options = <?php echo json_encode($krs->Final->Options()) ?>;
fkrsdelete.Lists["x_Setara"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrsdelete.Lists["x_Setara"].Options = <?php echo json_encode($krs->Setara->Options()) ?>;
fkrsdelete.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrsdelete.Lists["x_NA"].Options = <?php echo json_encode($krs->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $krs_delete->ShowPageHeader(); ?>
<?php
$krs_delete->ShowMessage();
?>
<form name="fkrsdelete" id="fkrsdelete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($krs_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $krs_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="krs">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($krs_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $krs->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($krs->KRSID->Visible) { // KRSID ?>
		<th><span id="elh_krs_KRSID" class="krs_KRSID"><?php echo $krs->KRSID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->KHSID->Visible) { // KHSID ?>
		<th><span id="elh_krs_KHSID" class="krs_KHSID"><?php echo $krs->KHSID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->StudentID->Visible) { // StudentID ?>
		<th><span id="elh_krs_StudentID" class="krs_StudentID"><?php echo $krs->StudentID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->TahunID->Visible) { // TahunID ?>
		<th><span id="elh_krs_TahunID" class="krs_TahunID"><?php echo $krs->TahunID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Sesi->Visible) { // Sesi ?>
		<th><span id="elh_krs_Sesi" class="krs_Sesi"><?php echo $krs->Sesi->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->JadwalID->Visible) { // JadwalID ?>
		<th><span id="elh_krs_JadwalID" class="krs_JadwalID"><?php echo $krs->JadwalID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->MKID->Visible) { // MKID ?>
		<th><span id="elh_krs_MKID" class="krs_MKID"><?php echo $krs->MKID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->MKKode->Visible) { // MKKode ?>
		<th><span id="elh_krs_MKKode" class="krs_MKKode"><?php echo $krs->MKKode->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->SKS->Visible) { // SKS ?>
		<th><span id="elh_krs_SKS" class="krs_SKS"><?php echo $krs->SKS->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Tugas1->Visible) { // Tugas1 ?>
		<th><span id="elh_krs_Tugas1" class="krs_Tugas1"><?php echo $krs->Tugas1->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Tugas2->Visible) { // Tugas2 ?>
		<th><span id="elh_krs_Tugas2" class="krs_Tugas2"><?php echo $krs->Tugas2->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Tugas3->Visible) { // Tugas3 ?>
		<th><span id="elh_krs_Tugas3" class="krs_Tugas3"><?php echo $krs->Tugas3->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Tugas4->Visible) { // Tugas4 ?>
		<th><span id="elh_krs_Tugas4" class="krs_Tugas4"><?php echo $krs->Tugas4->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Tugas5->Visible) { // Tugas5 ?>
		<th><span id="elh_krs_Tugas5" class="krs_Tugas5"><?php echo $krs->Tugas5->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Presensi->Visible) { // Presensi ?>
		<th><span id="elh_krs_Presensi" class="krs_Presensi"><?php echo $krs->Presensi->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->_Presensi->Visible) { // _Presensi ?>
		<th><span id="elh_krs__Presensi" class="krs__Presensi"><?php echo $krs->_Presensi->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->UTS->Visible) { // UTS ?>
		<th><span id="elh_krs_UTS" class="krs_UTS"><?php echo $krs->UTS->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->UAS->Visible) { // UAS ?>
		<th><span id="elh_krs_UAS" class="krs_UAS"><?php echo $krs->UAS->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Responsi->Visible) { // Responsi ?>
		<th><span id="elh_krs_Responsi" class="krs_Responsi"><?php echo $krs->Responsi->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->NilaiAkhir->Visible) { // NilaiAkhir ?>
		<th><span id="elh_krs_NilaiAkhir" class="krs_NilaiAkhir"><?php echo $krs->NilaiAkhir->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->GradeNilai->Visible) { // GradeNilai ?>
		<th><span id="elh_krs_GradeNilai" class="krs_GradeNilai"><?php echo $krs->GradeNilai->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->BobotNilai->Visible) { // BobotNilai ?>
		<th><span id="elh_krs_BobotNilai" class="krs_BobotNilai"><?php echo $krs->BobotNilai->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->StatusKRSID->Visible) { // StatusKRSID ?>
		<th><span id="elh_krs_StatusKRSID" class="krs_StatusKRSID"><?php echo $krs->StatusKRSID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Tinggi->Visible) { // Tinggi ?>
		<th><span id="elh_krs_Tinggi" class="krs_Tinggi"><?php echo $krs->Tinggi->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Final->Visible) { // Final ?>
		<th><span id="elh_krs_Final" class="krs_Final"><?php echo $krs->Final->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Setara->Visible) { // Setara ?>
		<th><span id="elh_krs_Setara" class="krs_Setara"><?php echo $krs->Setara->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Creator->Visible) { // Creator ?>
		<th><span id="elh_krs_Creator" class="krs_Creator"><?php echo $krs->Creator->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->CreateDate->Visible) { // CreateDate ?>
		<th><span id="elh_krs_CreateDate" class="krs_CreateDate"><?php echo $krs->CreateDate->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->Editor->Visible) { // Editor ?>
		<th><span id="elh_krs_Editor" class="krs_Editor"><?php echo $krs->Editor->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->EditDate->Visible) { // EditDate ?>
		<th><span id="elh_krs_EditDate" class="krs_EditDate"><?php echo $krs->EditDate->FldCaption() ?></span></th>
<?php } ?>
<?php if ($krs->NA->Visible) { // NA ?>
		<th><span id="elh_krs_NA" class="krs_NA"><?php echo $krs->NA->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$krs_delete->RecCnt = 0;
$i = 0;
while (!$krs_delete->Recordset->EOF) {
	$krs_delete->RecCnt++;
	$krs_delete->RowCnt++;

	// Set row properties
	$krs->ResetAttrs();
	$krs->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$krs_delete->LoadRowValues($krs_delete->Recordset);

	// Render row
	$krs_delete->RenderRow();
?>
	<tr<?php echo $krs->RowAttributes() ?>>
<?php if ($krs->KRSID->Visible) { // KRSID ?>
		<td<?php echo $krs->KRSID->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_KRSID" class="krs_KRSID">
<span<?php echo $krs->KRSID->ViewAttributes() ?>>
<?php echo $krs->KRSID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->KHSID->Visible) { // KHSID ?>
		<td<?php echo $krs->KHSID->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_KHSID" class="krs_KHSID">
<span<?php echo $krs->KHSID->ViewAttributes() ?>>
<?php echo $krs->KHSID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->StudentID->Visible) { // StudentID ?>
		<td<?php echo $krs->StudentID->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_StudentID" class="krs_StudentID">
<span<?php echo $krs->StudentID->ViewAttributes() ?>>
<?php echo $krs->StudentID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->TahunID->Visible) { // TahunID ?>
		<td<?php echo $krs->TahunID->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_TahunID" class="krs_TahunID">
<span<?php echo $krs->TahunID->ViewAttributes() ?>>
<?php echo $krs->TahunID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Sesi->Visible) { // Sesi ?>
		<td<?php echo $krs->Sesi->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Sesi" class="krs_Sesi">
<span<?php echo $krs->Sesi->ViewAttributes() ?>>
<?php echo $krs->Sesi->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->JadwalID->Visible) { // JadwalID ?>
		<td<?php echo $krs->JadwalID->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_JadwalID" class="krs_JadwalID">
<span<?php echo $krs->JadwalID->ViewAttributes() ?>>
<?php echo $krs->JadwalID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->MKID->Visible) { // MKID ?>
		<td<?php echo $krs->MKID->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_MKID" class="krs_MKID">
<span<?php echo $krs->MKID->ViewAttributes() ?>>
<?php echo $krs->MKID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->MKKode->Visible) { // MKKode ?>
		<td<?php echo $krs->MKKode->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_MKKode" class="krs_MKKode">
<span<?php echo $krs->MKKode->ViewAttributes() ?>>
<?php echo $krs->MKKode->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->SKS->Visible) { // SKS ?>
		<td<?php echo $krs->SKS->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_SKS" class="krs_SKS">
<span<?php echo $krs->SKS->ViewAttributes() ?>>
<?php echo $krs->SKS->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Tugas1->Visible) { // Tugas1 ?>
		<td<?php echo $krs->Tugas1->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Tugas1" class="krs_Tugas1">
<span<?php echo $krs->Tugas1->ViewAttributes() ?>>
<?php echo $krs->Tugas1->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Tugas2->Visible) { // Tugas2 ?>
		<td<?php echo $krs->Tugas2->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Tugas2" class="krs_Tugas2">
<span<?php echo $krs->Tugas2->ViewAttributes() ?>>
<?php echo $krs->Tugas2->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Tugas3->Visible) { // Tugas3 ?>
		<td<?php echo $krs->Tugas3->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Tugas3" class="krs_Tugas3">
<span<?php echo $krs->Tugas3->ViewAttributes() ?>>
<?php echo $krs->Tugas3->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Tugas4->Visible) { // Tugas4 ?>
		<td<?php echo $krs->Tugas4->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Tugas4" class="krs_Tugas4">
<span<?php echo $krs->Tugas4->ViewAttributes() ?>>
<?php echo $krs->Tugas4->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Tugas5->Visible) { // Tugas5 ?>
		<td<?php echo $krs->Tugas5->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Tugas5" class="krs_Tugas5">
<span<?php echo $krs->Tugas5->ViewAttributes() ?>>
<?php echo $krs->Tugas5->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Presensi->Visible) { // Presensi ?>
		<td<?php echo $krs->Presensi->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Presensi" class="krs_Presensi">
<span<?php echo $krs->Presensi->ViewAttributes() ?>>
<?php echo $krs->Presensi->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->_Presensi->Visible) { // _Presensi ?>
		<td<?php echo $krs->_Presensi->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs__Presensi" class="krs__Presensi">
<span<?php echo $krs->_Presensi->ViewAttributes() ?>>
<?php echo $krs->_Presensi->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->UTS->Visible) { // UTS ?>
		<td<?php echo $krs->UTS->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_UTS" class="krs_UTS">
<span<?php echo $krs->UTS->ViewAttributes() ?>>
<?php echo $krs->UTS->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->UAS->Visible) { // UAS ?>
		<td<?php echo $krs->UAS->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_UAS" class="krs_UAS">
<span<?php echo $krs->UAS->ViewAttributes() ?>>
<?php echo $krs->UAS->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Responsi->Visible) { // Responsi ?>
		<td<?php echo $krs->Responsi->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Responsi" class="krs_Responsi">
<span<?php echo $krs->Responsi->ViewAttributes() ?>>
<?php echo $krs->Responsi->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->NilaiAkhir->Visible) { // NilaiAkhir ?>
		<td<?php echo $krs->NilaiAkhir->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_NilaiAkhir" class="krs_NilaiAkhir">
<span<?php echo $krs->NilaiAkhir->ViewAttributes() ?>>
<?php echo $krs->NilaiAkhir->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->GradeNilai->Visible) { // GradeNilai ?>
		<td<?php echo $krs->GradeNilai->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_GradeNilai" class="krs_GradeNilai">
<span<?php echo $krs->GradeNilai->ViewAttributes() ?>>
<?php echo $krs->GradeNilai->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->BobotNilai->Visible) { // BobotNilai ?>
		<td<?php echo $krs->BobotNilai->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_BobotNilai" class="krs_BobotNilai">
<span<?php echo $krs->BobotNilai->ViewAttributes() ?>>
<?php echo $krs->BobotNilai->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->StatusKRSID->Visible) { // StatusKRSID ?>
		<td<?php echo $krs->StatusKRSID->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_StatusKRSID" class="krs_StatusKRSID">
<span<?php echo $krs->StatusKRSID->ViewAttributes() ?>>
<?php echo $krs->StatusKRSID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Tinggi->Visible) { // Tinggi ?>
		<td<?php echo $krs->Tinggi->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Tinggi" class="krs_Tinggi">
<span<?php echo $krs->Tinggi->ViewAttributes() ?>>
<?php echo $krs->Tinggi->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Final->Visible) { // Final ?>
		<td<?php echo $krs->Final->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Final" class="krs_Final">
<span<?php echo $krs->Final->ViewAttributes() ?>>
<?php echo $krs->Final->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Setara->Visible) { // Setara ?>
		<td<?php echo $krs->Setara->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Setara" class="krs_Setara">
<span<?php echo $krs->Setara->ViewAttributes() ?>>
<?php echo $krs->Setara->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Creator->Visible) { // Creator ?>
		<td<?php echo $krs->Creator->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Creator" class="krs_Creator">
<span<?php echo $krs->Creator->ViewAttributes() ?>>
<?php echo $krs->Creator->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->CreateDate->Visible) { // CreateDate ?>
		<td<?php echo $krs->CreateDate->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_CreateDate" class="krs_CreateDate">
<span<?php echo $krs->CreateDate->ViewAttributes() ?>>
<?php echo $krs->CreateDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->Editor->Visible) { // Editor ?>
		<td<?php echo $krs->Editor->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_Editor" class="krs_Editor">
<span<?php echo $krs->Editor->ViewAttributes() ?>>
<?php echo $krs->Editor->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->EditDate->Visible) { // EditDate ?>
		<td<?php echo $krs->EditDate->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_EditDate" class="krs_EditDate">
<span<?php echo $krs->EditDate->ViewAttributes() ?>>
<?php echo $krs->EditDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($krs->NA->Visible) { // NA ?>
		<td<?php echo $krs->NA->CellAttributes() ?>>
<span id="el<?php echo $krs_delete->RowCnt ?>_krs_NA" class="krs_NA">
<span<?php echo $krs->NA->ViewAttributes() ?>>
<?php echo $krs->NA->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$krs_delete->Recordset->MoveNext();
}
$krs_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</div>
<div>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $krs_delete->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fkrsdelete.Init();
</script>
<?php
$krs_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$krs_delete->Page_Terminate();
?>
