<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_statusawalinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_statusawal_delete = NULL; // Initialize page object first

class cmaster_statusawal_delete extends cmaster_statusawal {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_statusawal';

	// Page object name
	var $PageObjName = 'master_statusawal_delete';

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

		// Table object (master_statusawal)
		if (!isset($GLOBALS["master_statusawal"]) || get_class($GLOBALS["master_statusawal"]) == "cmaster_statusawal") {
			$GLOBALS["master_statusawal"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_statusawal"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_statusawal', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("master_statusawallist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->Urutan->SetVisibility();
		$this->StatusAwalID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->BeliOnline->SetVisibility();
		$this->BeliFormulir->SetVisibility();
		$this->JalurKhusus->SetVisibility();
		$this->TanpaTest->SetVisibility();
		$this->Catatan->SetVisibility();
		$this->NA->SetVisibility();
		$this->PotonganSPI_Prosen->SetVisibility();
		$this->PotonganSPI_Nominal->SetVisibility();
		$this->PotonganSPP_Prosen->SetVisibility();
		$this->PotonganSPP_Nominal->SetVisibility();

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
		global $EW_EXPORT, $master_statusawal;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_statusawal);
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
			$this->Page_Terminate("master_statusawallist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in master_statusawal class, master_statusawalinfo.php

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
				$this->Page_Terminate("master_statusawallist.php"); // Return to list
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

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Urutan->DbValue = $row['Urutan'];
		$this->StatusAwalID->DbValue = $row['StatusAwalID'];
		$this->Nama->DbValue = $row['Nama'];
		$this->BeliOnline->DbValue = $row['BeliOnline'];
		$this->BeliFormulir->DbValue = $row['BeliFormulir'];
		$this->JalurKhusus->DbValue = $row['JalurKhusus'];
		$this->TanpaTest->DbValue = $row['TanpaTest'];
		$this->Catatan->DbValue = $row['Catatan'];
		$this->NA->DbValue = $row['NA'];
		$this->PotonganSPI_Prosen->DbValue = $row['PotonganSPI_Prosen'];
		$this->PotonganSPI_Nominal->DbValue = $row['PotonganSPI_Nominal'];
		$this->PotonganSPP_Prosen->DbValue = $row['PotonganSPP_Prosen'];
		$this->PotonganSPP_Nominal->DbValue = $row['PotonganSPP_Nominal'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
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

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

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
				$sThisKey .= $row['StatusAwalID'];
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_statusawallist.php"), "", $this->TableVar, TRUE);
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
if (!isset($master_statusawal_delete)) $master_statusawal_delete = new cmaster_statusawal_delete();

// Page init
$master_statusawal_delete->Page_Init();

// Page main
$master_statusawal_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_statusawal_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "delete";
var CurrentForm = fmaster_statusawaldelete = new ew_Form("fmaster_statusawaldelete", "delete");

// Form_CustomValidate event
fmaster_statusawaldelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_statusawaldelete.ValidateRequired = true;
<?php } else { ?>
fmaster_statusawaldelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_statusawaldelete.Lists["x_BeliOnline"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawaldelete.Lists["x_BeliOnline"].Options = <?php echo json_encode($master_statusawal->BeliOnline->Options()) ?>;
fmaster_statusawaldelete.Lists["x_BeliFormulir"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawaldelete.Lists["x_BeliFormulir"].Options = <?php echo json_encode($master_statusawal->BeliFormulir->Options()) ?>;
fmaster_statusawaldelete.Lists["x_JalurKhusus"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawaldelete.Lists["x_JalurKhusus"].Options = <?php echo json_encode($master_statusawal->JalurKhusus->Options()) ?>;
fmaster_statusawaldelete.Lists["x_TanpaTest"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawaldelete.Lists["x_TanpaTest"].Options = <?php echo json_encode($master_statusawal->TanpaTest->Options()) ?>;
fmaster_statusawaldelete.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawaldelete.Lists["x_NA"].Options = <?php echo json_encode($master_statusawal->NA->Options()) ?>;

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
<?php $master_statusawal_delete->ShowPageHeader(); ?>
<?php
$master_statusawal_delete->ShowMessage();
?>
<form name="fmaster_statusawaldelete" id="fmaster_statusawaldelete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_statusawal_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_statusawal_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_statusawal">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($master_statusawal_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $master_statusawal->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($master_statusawal->Urutan->Visible) { // Urutan ?>
		<th><span id="elh_master_statusawal_Urutan" class="master_statusawal_Urutan"><?php echo $master_statusawal->Urutan->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->StatusAwalID->Visible) { // StatusAwalID ?>
		<th><span id="elh_master_statusawal_StatusAwalID" class="master_statusawal_StatusAwalID"><?php echo $master_statusawal->StatusAwalID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->Nama->Visible) { // Nama ?>
		<th><span id="elh_master_statusawal_Nama" class="master_statusawal_Nama"><?php echo $master_statusawal->Nama->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->BeliOnline->Visible) { // BeliOnline ?>
		<th><span id="elh_master_statusawal_BeliOnline" class="master_statusawal_BeliOnline"><?php echo $master_statusawal->BeliOnline->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->BeliFormulir->Visible) { // BeliFormulir ?>
		<th><span id="elh_master_statusawal_BeliFormulir" class="master_statusawal_BeliFormulir"><?php echo $master_statusawal->BeliFormulir->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->JalurKhusus->Visible) { // JalurKhusus ?>
		<th><span id="elh_master_statusawal_JalurKhusus" class="master_statusawal_JalurKhusus"><?php echo $master_statusawal->JalurKhusus->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->TanpaTest->Visible) { // TanpaTest ?>
		<th><span id="elh_master_statusawal_TanpaTest" class="master_statusawal_TanpaTest"><?php echo $master_statusawal->TanpaTest->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->Catatan->Visible) { // Catatan ?>
		<th><span id="elh_master_statusawal_Catatan" class="master_statusawal_Catatan"><?php echo $master_statusawal->Catatan->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->NA->Visible) { // NA ?>
		<th><span id="elh_master_statusawal_NA" class="master_statusawal_NA"><?php echo $master_statusawal->NA->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->PotonganSPI_Prosen->Visible) { // PotonganSPI_Prosen ?>
		<th><span id="elh_master_statusawal_PotonganSPI_Prosen" class="master_statusawal_PotonganSPI_Prosen"><?php echo $master_statusawal->PotonganSPI_Prosen->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->PotonganSPI_Nominal->Visible) { // PotonganSPI_Nominal ?>
		<th><span id="elh_master_statusawal_PotonganSPI_Nominal" class="master_statusawal_PotonganSPI_Nominal"><?php echo $master_statusawal->PotonganSPI_Nominal->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->PotonganSPP_Prosen->Visible) { // PotonganSPP_Prosen ?>
		<th><span id="elh_master_statusawal_PotonganSPP_Prosen" class="master_statusawal_PotonganSPP_Prosen"><?php echo $master_statusawal->PotonganSPP_Prosen->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_statusawal->PotonganSPP_Nominal->Visible) { // PotonganSPP_Nominal ?>
		<th><span id="elh_master_statusawal_PotonganSPP_Nominal" class="master_statusawal_PotonganSPP_Nominal"><?php echo $master_statusawal->PotonganSPP_Nominal->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$master_statusawal_delete->RecCnt = 0;
$i = 0;
while (!$master_statusawal_delete->Recordset->EOF) {
	$master_statusawal_delete->RecCnt++;
	$master_statusawal_delete->RowCnt++;

	// Set row properties
	$master_statusawal->ResetAttrs();
	$master_statusawal->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$master_statusawal_delete->LoadRowValues($master_statusawal_delete->Recordset);

	// Render row
	$master_statusawal_delete->RenderRow();
?>
	<tr<?php echo $master_statusawal->RowAttributes() ?>>
<?php if ($master_statusawal->Urutan->Visible) { // Urutan ?>
		<td<?php echo $master_statusawal->Urutan->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_Urutan" class="master_statusawal_Urutan">
<span<?php echo $master_statusawal->Urutan->ViewAttributes() ?>>
<?php echo $master_statusawal->Urutan->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->StatusAwalID->Visible) { // StatusAwalID ?>
		<td<?php echo $master_statusawal->StatusAwalID->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_StatusAwalID" class="master_statusawal_StatusAwalID">
<span<?php echo $master_statusawal->StatusAwalID->ViewAttributes() ?>>
<?php echo $master_statusawal->StatusAwalID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->Nama->Visible) { // Nama ?>
		<td<?php echo $master_statusawal->Nama->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_Nama" class="master_statusawal_Nama">
<span<?php echo $master_statusawal->Nama->ViewAttributes() ?>>
<?php echo $master_statusawal->Nama->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->BeliOnline->Visible) { // BeliOnline ?>
		<td<?php echo $master_statusawal->BeliOnline->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_BeliOnline" class="master_statusawal_BeliOnline">
<span<?php echo $master_statusawal->BeliOnline->ViewAttributes() ?>>
<?php echo $master_statusawal->BeliOnline->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->BeliFormulir->Visible) { // BeliFormulir ?>
		<td<?php echo $master_statusawal->BeliFormulir->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_BeliFormulir" class="master_statusawal_BeliFormulir">
<span<?php echo $master_statusawal->BeliFormulir->ViewAttributes() ?>>
<?php echo $master_statusawal->BeliFormulir->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->JalurKhusus->Visible) { // JalurKhusus ?>
		<td<?php echo $master_statusawal->JalurKhusus->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_JalurKhusus" class="master_statusawal_JalurKhusus">
<span<?php echo $master_statusawal->JalurKhusus->ViewAttributes() ?>>
<?php echo $master_statusawal->JalurKhusus->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->TanpaTest->Visible) { // TanpaTest ?>
		<td<?php echo $master_statusawal->TanpaTest->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_TanpaTest" class="master_statusawal_TanpaTest">
<span<?php echo $master_statusawal->TanpaTest->ViewAttributes() ?>>
<?php echo $master_statusawal->TanpaTest->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->Catatan->Visible) { // Catatan ?>
		<td<?php echo $master_statusawal->Catatan->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_Catatan" class="master_statusawal_Catatan">
<span<?php echo $master_statusawal->Catatan->ViewAttributes() ?>>
<?php echo $master_statusawal->Catatan->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->NA->Visible) { // NA ?>
		<td<?php echo $master_statusawal->NA->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_NA" class="master_statusawal_NA">
<span<?php echo $master_statusawal->NA->ViewAttributes() ?>>
<?php echo $master_statusawal->NA->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->PotonganSPI_Prosen->Visible) { // PotonganSPI_Prosen ?>
		<td<?php echo $master_statusawal->PotonganSPI_Prosen->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_PotonganSPI_Prosen" class="master_statusawal_PotonganSPI_Prosen">
<span<?php echo $master_statusawal->PotonganSPI_Prosen->ViewAttributes() ?>>
<?php echo $master_statusawal->PotonganSPI_Prosen->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->PotonganSPI_Nominal->Visible) { // PotonganSPI_Nominal ?>
		<td<?php echo $master_statusawal->PotonganSPI_Nominal->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_PotonganSPI_Nominal" class="master_statusawal_PotonganSPI_Nominal">
<span<?php echo $master_statusawal->PotonganSPI_Nominal->ViewAttributes() ?>>
<?php echo $master_statusawal->PotonganSPI_Nominal->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->PotonganSPP_Prosen->Visible) { // PotonganSPP_Prosen ?>
		<td<?php echo $master_statusawal->PotonganSPP_Prosen->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_PotonganSPP_Prosen" class="master_statusawal_PotonganSPP_Prosen">
<span<?php echo $master_statusawal->PotonganSPP_Prosen->ViewAttributes() ?>>
<?php echo $master_statusawal->PotonganSPP_Prosen->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_statusawal->PotonganSPP_Nominal->Visible) { // PotonganSPP_Nominal ?>
		<td<?php echo $master_statusawal->PotonganSPP_Nominal->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_delete->RowCnt ?>_master_statusawal_PotonganSPP_Nominal" class="master_statusawal_PotonganSPP_Nominal">
<span<?php echo $master_statusawal->PotonganSPP_Nominal->ViewAttributes() ?>>
<?php echo $master_statusawal->PotonganSPP_Nominal->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$master_statusawal_delete->Recordset->MoveNext();
}
$master_statusawal_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</div>
<div>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $master_statusawal_delete->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fmaster_statusawaldelete.Init();
</script>
<?php
$master_statusawal_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_statusawal_delete->Page_Terminate();
?>
