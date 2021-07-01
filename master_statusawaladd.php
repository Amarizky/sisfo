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

$master_statusawal_add = NULL; // Initialize page object first

class cmaster_statusawal_add extends cmaster_statusawal {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_statusawal';

	// Page object name
	var $PageObjName = 'master_statusawal_add';

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
			define("EW_PAGE_ID", 'add', TRUE);

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
		if (!$Security->CanAdd()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("master_statusawallist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
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

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
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

			// Handle modal response
			if ($this->IsModal) {
				$row = array();
				$row["url"] = $url;
				echo ew_ArrayToJson(array($row));
			} else {
				header("Location: " . $url);
			}
		}
		exit();
	}
	var $FormClassName = "form-horizontal ewForm ewAddForm";
	var $IsModal = FALSE;
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;
		global $gbSkipHeaderFooter;

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;
		$this->FormClassName = "ewForm ewAddForm";
		if (ew_IsMobile() || $this->IsModal)
			$this->FormClassName = ew_Concat("form-horizontal", $this->FormClassName, " ");

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["StatusAwalID"] != "") {
				$this->StatusAwalID->setQueryStringValue($_GET["StatusAwalID"]);
				$this->setKey("StatusAwalID", $this->StatusAwalID->CurrentValue); // Set up key
			} else {
				$this->setKey("StatusAwalID", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		} else {
			if ($this->CurrentAction == "I") // Load default values for blank record
				$this->LoadDefaultValues();
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("master_statusawallist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "master_statusawallist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "master_statusawalview.php")
						$sReturnUrl = $this->GetViewUrl(); // View page, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD; // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load default values
	function LoadDefaultValues() {
		$this->Urutan->CurrentValue = NULL;
		$this->Urutan->OldValue = $this->Urutan->CurrentValue;
		$this->StatusAwalID->CurrentValue = NULL;
		$this->StatusAwalID->OldValue = $this->StatusAwalID->CurrentValue;
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->BeliOnline->CurrentValue = "N";
		$this->BeliFormulir->CurrentValue = "Y";
		$this->JalurKhusus->CurrentValue = "N";
		$this->TanpaTest->CurrentValue = "N";
		$this->Catatan->CurrentValue = NULL;
		$this->Catatan->OldValue = $this->Catatan->CurrentValue;
		$this->NA->CurrentValue = "N";
		$this->PotonganSPI_Prosen->CurrentValue = 0;
		$this->PotonganSPI_Nominal->CurrentValue = 0;
		$this->PotonganSPP_Prosen->CurrentValue = 0;
		$this->PotonganSPP_Nominal->CurrentValue = 0;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Urutan->FldIsDetailKey) {
			$this->Urutan->setFormValue($objForm->GetValue("x_Urutan"));
		}
		if (!$this->StatusAwalID->FldIsDetailKey) {
			$this->StatusAwalID->setFormValue($objForm->GetValue("x_StatusAwalID"));
		}
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->BeliOnline->FldIsDetailKey) {
			$this->BeliOnline->setFormValue($objForm->GetValue("x_BeliOnline"));
		}
		if (!$this->BeliFormulir->FldIsDetailKey) {
			$this->BeliFormulir->setFormValue($objForm->GetValue("x_BeliFormulir"));
		}
		if (!$this->JalurKhusus->FldIsDetailKey) {
			$this->JalurKhusus->setFormValue($objForm->GetValue("x_JalurKhusus"));
		}
		if (!$this->TanpaTest->FldIsDetailKey) {
			$this->TanpaTest->setFormValue($objForm->GetValue("x_TanpaTest"));
		}
		if (!$this->Catatan->FldIsDetailKey) {
			$this->Catatan->setFormValue($objForm->GetValue("x_Catatan"));
		}
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
		if (!$this->PotonganSPI_Prosen->FldIsDetailKey) {
			$this->PotonganSPI_Prosen->setFormValue($objForm->GetValue("x_PotonganSPI_Prosen"));
		}
		if (!$this->PotonganSPI_Nominal->FldIsDetailKey) {
			$this->PotonganSPI_Nominal->setFormValue($objForm->GetValue("x_PotonganSPI_Nominal"));
		}
		if (!$this->PotonganSPP_Prosen->FldIsDetailKey) {
			$this->PotonganSPP_Prosen->setFormValue($objForm->GetValue("x_PotonganSPP_Prosen"));
		}
		if (!$this->PotonganSPP_Nominal->FldIsDetailKey) {
			$this->PotonganSPP_Nominal->setFormValue($objForm->GetValue("x_PotonganSPP_Nominal"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->Urutan->CurrentValue = $this->Urutan->FormValue;
		$this->StatusAwalID->CurrentValue = $this->StatusAwalID->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->BeliOnline->CurrentValue = $this->BeliOnline->FormValue;
		$this->BeliFormulir->CurrentValue = $this->BeliFormulir->FormValue;
		$this->JalurKhusus->CurrentValue = $this->JalurKhusus->FormValue;
		$this->TanpaTest->CurrentValue = $this->TanpaTest->FormValue;
		$this->Catatan->CurrentValue = $this->Catatan->FormValue;
		$this->NA->CurrentValue = $this->NA->FormValue;
		$this->PotonganSPI_Prosen->CurrentValue = $this->PotonganSPI_Prosen->FormValue;
		$this->PotonganSPI_Nominal->CurrentValue = $this->PotonganSPI_Nominal->FormValue;
		$this->PotonganSPP_Prosen->CurrentValue = $this->PotonganSPP_Prosen->FormValue;
		$this->PotonganSPP_Nominal->CurrentValue = $this->PotonganSPP_Nominal->FormValue;
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("StatusAwalID")) <> "")
			$this->StatusAwalID->CurrentValue = $this->getKey("StatusAwalID"); // StatusAwalID
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$this->OldRecordset = ew_LoadRecordset($sSql, $conn);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// Urutan
			$this->Urutan->EditAttrs["class"] = "form-control";
			$this->Urutan->EditCustomAttributes = "";
			$this->Urutan->EditValue = ew_HtmlEncode($this->Urutan->CurrentValue);
			$this->Urutan->PlaceHolder = ew_RemoveHtml($this->Urutan->FldCaption());

			// StatusAwalID
			$this->StatusAwalID->EditAttrs["class"] = "form-control";
			$this->StatusAwalID->EditCustomAttributes = "";
			$this->StatusAwalID->EditValue = ew_HtmlEncode($this->StatusAwalID->CurrentValue);
			$this->StatusAwalID->PlaceHolder = ew_RemoveHtml($this->StatusAwalID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
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
			$this->Catatan->EditValue = ew_HtmlEncode($this->Catatan->CurrentValue);
			$this->Catatan->PlaceHolder = ew_RemoveHtml($this->Catatan->FldCaption());

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// PotonganSPI_Prosen
			$this->PotonganSPI_Prosen->EditAttrs["class"] = "form-control";
			$this->PotonganSPI_Prosen->EditCustomAttributes = "";
			$this->PotonganSPI_Prosen->EditValue = ew_HtmlEncode($this->PotonganSPI_Prosen->CurrentValue);
			$this->PotonganSPI_Prosen->PlaceHolder = ew_RemoveHtml($this->PotonganSPI_Prosen->FldCaption());

			// PotonganSPI_Nominal
			$this->PotonganSPI_Nominal->EditAttrs["class"] = "form-control";
			$this->PotonganSPI_Nominal->EditCustomAttributes = "";
			$this->PotonganSPI_Nominal->EditValue = ew_HtmlEncode($this->PotonganSPI_Nominal->CurrentValue);
			$this->PotonganSPI_Nominal->PlaceHolder = ew_RemoveHtml($this->PotonganSPI_Nominal->FldCaption());

			// PotonganSPP_Prosen
			$this->PotonganSPP_Prosen->EditAttrs["class"] = "form-control";
			$this->PotonganSPP_Prosen->EditCustomAttributes = "";
			$this->PotonganSPP_Prosen->EditValue = ew_HtmlEncode($this->PotonganSPP_Prosen->CurrentValue);
			$this->PotonganSPP_Prosen->PlaceHolder = ew_RemoveHtml($this->PotonganSPP_Prosen->FldCaption());

			// PotonganSPP_Nominal
			$this->PotonganSPP_Nominal->EditAttrs["class"] = "form-control";
			$this->PotonganSPP_Nominal->EditCustomAttributes = "";
			$this->PotonganSPP_Nominal->EditValue = ew_HtmlEncode($this->PotonganSPP_Nominal->CurrentValue);
			$this->PotonganSPP_Nominal->PlaceHolder = ew_RemoveHtml($this->PotonganSPP_Nominal->FldCaption());

			// Add refer script
			// Urutan

			$this->Urutan->LinkCustomAttributes = "";
			$this->Urutan->HrefValue = "";

			// StatusAwalID
			$this->StatusAwalID->LinkCustomAttributes = "";
			$this->StatusAwalID->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// BeliOnline
			$this->BeliOnline->LinkCustomAttributes = "";
			$this->BeliOnline->HrefValue = "";

			// BeliFormulir
			$this->BeliFormulir->LinkCustomAttributes = "";
			$this->BeliFormulir->HrefValue = "";

			// JalurKhusus
			$this->JalurKhusus->LinkCustomAttributes = "";
			$this->JalurKhusus->HrefValue = "";

			// TanpaTest
			$this->TanpaTest->LinkCustomAttributes = "";
			$this->TanpaTest->HrefValue = "";

			// Catatan
			$this->Catatan->LinkCustomAttributes = "";
			$this->Catatan->HrefValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";

			// PotonganSPI_Prosen
			$this->PotonganSPI_Prosen->LinkCustomAttributes = "";
			$this->PotonganSPI_Prosen->HrefValue = "";

			// PotonganSPI_Nominal
			$this->PotonganSPI_Nominal->LinkCustomAttributes = "";
			$this->PotonganSPI_Nominal->HrefValue = "";

			// PotonganSPP_Prosen
			$this->PotonganSPP_Prosen->LinkCustomAttributes = "";
			$this->PotonganSPP_Prosen->HrefValue = "";

			// PotonganSPP_Nominal
			$this->PotonganSPP_Nominal->LinkCustomAttributes = "";
			$this->PotonganSPP_Nominal->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->StatusAwalID->FldIsDetailKey && !is_null($this->StatusAwalID->FormValue) && $this->StatusAwalID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->StatusAwalID->FldCaption(), $this->StatusAwalID->ReqErrMsg));
		}
		if ($this->BeliFormulir->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->BeliFormulir->FldCaption(), $this->BeliFormulir->ReqErrMsg));
		}
		if ($this->JalurKhusus->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->JalurKhusus->FldCaption(), $this->JalurKhusus->ReqErrMsg));
		}
		if ($this->TanpaTest->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TanpaTest->FldCaption(), $this->TanpaTest->ReqErrMsg));
		}
		if ($this->NA->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->NA->FldCaption(), $this->NA->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->PotonganSPI_Prosen->FormValue)) {
			ew_AddMessage($gsFormError, $this->PotonganSPI_Prosen->FldErrMsg());
		}
		if (!ew_CheckInteger($this->PotonganSPI_Nominal->FormValue)) {
			ew_AddMessage($gsFormError, $this->PotonganSPI_Nominal->FldErrMsg());
		}
		if (!ew_CheckInteger($this->PotonganSPP_Prosen->FormValue)) {
			ew_AddMessage($gsFormError, $this->PotonganSPP_Prosen->FldErrMsg());
		}
		if (!ew_CheckInteger($this->PotonganSPP_Nominal->FormValue)) {
			ew_AddMessage($gsFormError, $this->PotonganSPP_Nominal->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $Language, $Security;
		if ($this->StatusAwalID->CurrentValue <> "") { // Check field with unique index
			$sFilter = "(StatusAwalID = '" . ew_AdjustSql($this->StatusAwalID->CurrentValue, $this->DBID) . "')";
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->StatusAwalID->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->StatusAwalID->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
		}
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// Urutan
		$this->Urutan->SetDbValueDef($rsnew, $this->Urutan->CurrentValue, NULL, FALSE);

		// StatusAwalID
		$this->StatusAwalID->SetDbValueDef($rsnew, $this->StatusAwalID->CurrentValue, "", FALSE);

		// Nama
		$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, NULL, FALSE);

		// BeliOnline
		$this->BeliOnline->SetDbValueDef($rsnew, ((strval($this->BeliOnline->CurrentValue) == "Y") ? "Y" : "N"), NULL, strval($this->BeliOnline->CurrentValue) == "");

		// BeliFormulir
		$this->BeliFormulir->SetDbValueDef($rsnew, ((strval($this->BeliFormulir->CurrentValue) == "Y") ? "Y" : "N"), "N", strval($this->BeliFormulir->CurrentValue) == "");

		// JalurKhusus
		$this->JalurKhusus->SetDbValueDef($rsnew, ((strval($this->JalurKhusus->CurrentValue) == "Y") ? "Y" : "N"), "N", strval($this->JalurKhusus->CurrentValue) == "");

		// TanpaTest
		$this->TanpaTest->SetDbValueDef($rsnew, ((strval($this->TanpaTest->CurrentValue) == "Y") ? "Y" : "N"), "N", strval($this->TanpaTest->CurrentValue) == "");

		// Catatan
		$this->Catatan->SetDbValueDef($rsnew, $this->Catatan->CurrentValue, NULL, FALSE);

		// NA
		$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), "N", strval($this->NA->CurrentValue) == "");

		// PotonganSPI_Prosen
		$this->PotonganSPI_Prosen->SetDbValueDef($rsnew, $this->PotonganSPI_Prosen->CurrentValue, NULL, strval($this->PotonganSPI_Prosen->CurrentValue) == "");

		// PotonganSPI_Nominal
		$this->PotonganSPI_Nominal->SetDbValueDef($rsnew, $this->PotonganSPI_Nominal->CurrentValue, NULL, strval($this->PotonganSPI_Nominal->CurrentValue) == "");

		// PotonganSPP_Prosen
		$this->PotonganSPP_Prosen->SetDbValueDef($rsnew, $this->PotonganSPP_Prosen->CurrentValue, NULL, strval($this->PotonganSPP_Prosen->CurrentValue) == "");

		// PotonganSPP_Nominal
		$this->PotonganSPP_Nominal->SetDbValueDef($rsnew, $this->PotonganSPP_Nominal->CurrentValue, NULL, strval($this->PotonganSPP_Nominal->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['StatusAwalID']) == "") {
			$this->setFailureMessage($Language->Phrase("InvalidKeyValue"));
			$bInsertRow = FALSE;
		}

		// Check for duplicate key
		if ($bInsertRow && $this->ValidateKey) {
			$sFilter = $this->KeyFilter();
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sKeyErrMsg = str_replace("%f", $sFilter, $Language->Phrase("DupKey"));
				$this->setFailureMessage($sKeyErrMsg);
				$rsChk->Close();
				$bInsertRow = FALSE;
			}
		}
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_statusawallist.php"), "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($master_statusawal_add)) $master_statusawal_add = new cmaster_statusawal_add();

// Page init
$master_statusawal_add->Page_Init();

// Page main
$master_statusawal_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_statusawal_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fmaster_statusawaladd = new ew_Form("fmaster_statusawaladd", "add");

// Validate form
fmaster_statusawaladd.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_StatusAwalID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_statusawal->StatusAwalID->FldCaption(), $master_statusawal->StatusAwalID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_BeliFormulir");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_statusawal->BeliFormulir->FldCaption(), $master_statusawal->BeliFormulir->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JalurKhusus");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_statusawal->JalurKhusus->FldCaption(), $master_statusawal->JalurKhusus->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TanpaTest");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_statusawal->TanpaTest->FldCaption(), $master_statusawal->TanpaTest->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_NA");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_statusawal->NA->FldCaption(), $master_statusawal->NA->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_PotonganSPI_Prosen");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($master_statusawal->PotonganSPI_Prosen->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_PotonganSPI_Nominal");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($master_statusawal->PotonganSPI_Nominal->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_PotonganSPP_Prosen");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($master_statusawal->PotonganSPP_Prosen->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_PotonganSPP_Nominal");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($master_statusawal->PotonganSPP_Nominal->FldErrMsg()) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fmaster_statusawaladd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_statusawaladd.ValidateRequired = true;
<?php } else { ?>
fmaster_statusawaladd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_statusawaladd.Lists["x_BeliOnline"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawaladd.Lists["x_BeliOnline"].Options = <?php echo json_encode($master_statusawal->BeliOnline->Options()) ?>;
fmaster_statusawaladd.Lists["x_BeliFormulir"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawaladd.Lists["x_BeliFormulir"].Options = <?php echo json_encode($master_statusawal->BeliFormulir->Options()) ?>;
fmaster_statusawaladd.Lists["x_JalurKhusus"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawaladd.Lists["x_JalurKhusus"].Options = <?php echo json_encode($master_statusawal->JalurKhusus->Options()) ?>;
fmaster_statusawaladd.Lists["x_TanpaTest"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawaladd.Lists["x_TanpaTest"].Options = <?php echo json_encode($master_statusawal->TanpaTest->Options()) ?>;
fmaster_statusawaladd.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawaladd.Lists["x_NA"].Options = <?php echo json_encode($master_statusawal->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$master_statusawal_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_statusawal_add->ShowPageHeader(); ?>
<?php
$master_statusawal_add->ShowMessage();
?>
<form name="fmaster_statusawaladd" id="fmaster_statusawaladd" class="<?php echo $master_statusawal_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_statusawal_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_statusawal_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_statusawal">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($master_statusawal_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_statusawal_add->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_statusawaladd" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_statusawal->Urutan->Visible) { // Urutan ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_Urutan" class="form-group">
		<label id="elh_master_statusawal_Urutan" for="x_Urutan" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->Urutan->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->Urutan->CellAttributes() ?>>
<span id="el_master_statusawal_Urutan">
<input type="text" data-table="master_statusawal" data-field="x_Urutan" name="x_Urutan" id="x_Urutan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Urutan->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Urutan->EditValue ?>"<?php echo $master_statusawal->Urutan->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->Urutan->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Urutan">
		<td><span id="elh_master_statusawal_Urutan"><?php echo $master_statusawal->Urutan->FldCaption() ?></span></td>
		<td<?php echo $master_statusawal->Urutan->CellAttributes() ?>>
<span id="el_master_statusawal_Urutan">
<input type="text" data-table="master_statusawal" data-field="x_Urutan" name="x_Urutan" id="x_Urutan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Urutan->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Urutan->EditValue ?>"<?php echo $master_statusawal->Urutan->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->Urutan->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->StatusAwalID->Visible) { // StatusAwalID ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_StatusAwalID" class="form-group">
		<label id="elh_master_statusawal_StatusAwalID" for="x_StatusAwalID" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->StatusAwalID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->StatusAwalID->CellAttributes() ?>>
<span id="el_master_statusawal_StatusAwalID">
<input type="text" data-table="master_statusawal" data-field="x_StatusAwalID" name="x_StatusAwalID" id="x_StatusAwalID" size="30" maxlength="5" placeholder="<?php echo ew_HtmlEncode($master_statusawal->StatusAwalID->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->StatusAwalID->EditValue ?>"<?php echo $master_statusawal->StatusAwalID->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->StatusAwalID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusAwalID">
		<td><span id="elh_master_statusawal_StatusAwalID"><?php echo $master_statusawal->StatusAwalID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_statusawal->StatusAwalID->CellAttributes() ?>>
<span id="el_master_statusawal_StatusAwalID">
<input type="text" data-table="master_statusawal" data-field="x_StatusAwalID" name="x_StatusAwalID" id="x_StatusAwalID" size="30" maxlength="5" placeholder="<?php echo ew_HtmlEncode($master_statusawal->StatusAwalID->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->StatusAwalID->EditValue ?>"<?php echo $master_statusawal->StatusAwalID->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->StatusAwalID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label id="elh_master_statusawal_Nama" for="x_Nama" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->Nama->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->Nama->CellAttributes() ?>>
<span id="el_master_statusawal_Nama">
<input type="text" data-table="master_statusawal" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Nama->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Nama->EditValue ?>"<?php echo $master_statusawal->Nama->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->Nama->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_master_statusawal_Nama"><?php echo $master_statusawal->Nama->FldCaption() ?></span></td>
		<td<?php echo $master_statusawal->Nama->CellAttributes() ?>>
<span id="el_master_statusawal_Nama">
<input type="text" data-table="master_statusawal" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Nama->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Nama->EditValue ?>"<?php echo $master_statusawal->Nama->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->Nama->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->BeliOnline->Visible) { // BeliOnline ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_BeliOnline" class="form-group">
		<label id="elh_master_statusawal_BeliOnline" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->BeliOnline->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->BeliOnline->CellAttributes() ?>>
<span id="el_master_statusawal_BeliOnline">
<div id="tp_x_BeliOnline" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_BeliOnline" data-value-separator="<?php echo $master_statusawal->BeliOnline->DisplayValueSeparatorAttribute() ?>" name="x_BeliOnline" id="x_BeliOnline" value="{value}"<?php echo $master_statusawal->BeliOnline->EditAttributes() ?>></div>
<div id="dsl_x_BeliOnline" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->BeliOnline->RadioButtonListHtml(FALSE, "x_BeliOnline") ?>
</div></div>
</span>
<?php echo $master_statusawal->BeliOnline->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_BeliOnline">
		<td><span id="elh_master_statusawal_BeliOnline"><?php echo $master_statusawal->BeliOnline->FldCaption() ?></span></td>
		<td<?php echo $master_statusawal->BeliOnline->CellAttributes() ?>>
<span id="el_master_statusawal_BeliOnline">
<div id="tp_x_BeliOnline" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_BeliOnline" data-value-separator="<?php echo $master_statusawal->BeliOnline->DisplayValueSeparatorAttribute() ?>" name="x_BeliOnline" id="x_BeliOnline" value="{value}"<?php echo $master_statusawal->BeliOnline->EditAttributes() ?>></div>
<div id="dsl_x_BeliOnline" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->BeliOnline->RadioButtonListHtml(FALSE, "x_BeliOnline") ?>
</div></div>
</span>
<?php echo $master_statusawal->BeliOnline->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->BeliFormulir->Visible) { // BeliFormulir ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_BeliFormulir" class="form-group">
		<label id="elh_master_statusawal_BeliFormulir" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->BeliFormulir->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->BeliFormulir->CellAttributes() ?>>
<span id="el_master_statusawal_BeliFormulir">
<div id="tp_x_BeliFormulir" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_BeliFormulir" data-value-separator="<?php echo $master_statusawal->BeliFormulir->DisplayValueSeparatorAttribute() ?>" name="x_BeliFormulir" id="x_BeliFormulir" value="{value}"<?php echo $master_statusawal->BeliFormulir->EditAttributes() ?>></div>
<div id="dsl_x_BeliFormulir" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->BeliFormulir->RadioButtonListHtml(FALSE, "x_BeliFormulir") ?>
</div></div>
</span>
<?php echo $master_statusawal->BeliFormulir->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_BeliFormulir">
		<td><span id="elh_master_statusawal_BeliFormulir"><?php echo $master_statusawal->BeliFormulir->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_statusawal->BeliFormulir->CellAttributes() ?>>
<span id="el_master_statusawal_BeliFormulir">
<div id="tp_x_BeliFormulir" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_BeliFormulir" data-value-separator="<?php echo $master_statusawal->BeliFormulir->DisplayValueSeparatorAttribute() ?>" name="x_BeliFormulir" id="x_BeliFormulir" value="{value}"<?php echo $master_statusawal->BeliFormulir->EditAttributes() ?>></div>
<div id="dsl_x_BeliFormulir" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->BeliFormulir->RadioButtonListHtml(FALSE, "x_BeliFormulir") ?>
</div></div>
</span>
<?php echo $master_statusawal->BeliFormulir->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->JalurKhusus->Visible) { // JalurKhusus ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_JalurKhusus" class="form-group">
		<label id="elh_master_statusawal_JalurKhusus" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->JalurKhusus->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->JalurKhusus->CellAttributes() ?>>
<span id="el_master_statusawal_JalurKhusus">
<div id="tp_x_JalurKhusus" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_JalurKhusus" data-value-separator="<?php echo $master_statusawal->JalurKhusus->DisplayValueSeparatorAttribute() ?>" name="x_JalurKhusus" id="x_JalurKhusus" value="{value}"<?php echo $master_statusawal->JalurKhusus->EditAttributes() ?>></div>
<div id="dsl_x_JalurKhusus" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->JalurKhusus->RadioButtonListHtml(FALSE, "x_JalurKhusus") ?>
</div></div>
</span>
<?php echo $master_statusawal->JalurKhusus->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_JalurKhusus">
		<td><span id="elh_master_statusawal_JalurKhusus"><?php echo $master_statusawal->JalurKhusus->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_statusawal->JalurKhusus->CellAttributes() ?>>
<span id="el_master_statusawal_JalurKhusus">
<div id="tp_x_JalurKhusus" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_JalurKhusus" data-value-separator="<?php echo $master_statusawal->JalurKhusus->DisplayValueSeparatorAttribute() ?>" name="x_JalurKhusus" id="x_JalurKhusus" value="{value}"<?php echo $master_statusawal->JalurKhusus->EditAttributes() ?>></div>
<div id="dsl_x_JalurKhusus" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->JalurKhusus->RadioButtonListHtml(FALSE, "x_JalurKhusus") ?>
</div></div>
</span>
<?php echo $master_statusawal->JalurKhusus->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->TanpaTest->Visible) { // TanpaTest ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_TanpaTest" class="form-group">
		<label id="elh_master_statusawal_TanpaTest" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->TanpaTest->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->TanpaTest->CellAttributes() ?>>
<span id="el_master_statusawal_TanpaTest">
<div id="tp_x_TanpaTest" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_TanpaTest" data-value-separator="<?php echo $master_statusawal->TanpaTest->DisplayValueSeparatorAttribute() ?>" name="x_TanpaTest" id="x_TanpaTest" value="{value}"<?php echo $master_statusawal->TanpaTest->EditAttributes() ?>></div>
<div id="dsl_x_TanpaTest" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->TanpaTest->RadioButtonListHtml(FALSE, "x_TanpaTest") ?>
</div></div>
</span>
<?php echo $master_statusawal->TanpaTest->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanpaTest">
		<td><span id="elh_master_statusawal_TanpaTest"><?php echo $master_statusawal->TanpaTest->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_statusawal->TanpaTest->CellAttributes() ?>>
<span id="el_master_statusawal_TanpaTest">
<div id="tp_x_TanpaTest" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_TanpaTest" data-value-separator="<?php echo $master_statusawal->TanpaTest->DisplayValueSeparatorAttribute() ?>" name="x_TanpaTest" id="x_TanpaTest" value="{value}"<?php echo $master_statusawal->TanpaTest->EditAttributes() ?>></div>
<div id="dsl_x_TanpaTest" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->TanpaTest->RadioButtonListHtml(FALSE, "x_TanpaTest") ?>
</div></div>
</span>
<?php echo $master_statusawal->TanpaTest->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->Catatan->Visible) { // Catatan ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_Catatan" class="form-group">
		<label id="elh_master_statusawal_Catatan" for="x_Catatan" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->Catatan->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->Catatan->CellAttributes() ?>>
<span id="el_master_statusawal_Catatan">
<input type="text" data-table="master_statusawal" data-field="x_Catatan" name="x_Catatan" id="x_Catatan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Catatan->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Catatan->EditValue ?>"<?php echo $master_statusawal->Catatan->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->Catatan->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Catatan">
		<td><span id="elh_master_statusawal_Catatan"><?php echo $master_statusawal->Catatan->FldCaption() ?></span></td>
		<td<?php echo $master_statusawal->Catatan->CellAttributes() ?>>
<span id="el_master_statusawal_Catatan">
<input type="text" data-table="master_statusawal" data-field="x_Catatan" name="x_Catatan" id="x_Catatan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Catatan->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Catatan->EditValue ?>"<?php echo $master_statusawal->Catatan->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->Catatan->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_master_statusawal_NA" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->NA->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->NA->CellAttributes() ?>>
<span id="el_master_statusawal_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_NA" data-value-separator="<?php echo $master_statusawal->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_statusawal->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $master_statusawal->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_master_statusawal_NA"><?php echo $master_statusawal->NA->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_statusawal->NA->CellAttributes() ?>>
<span id="el_master_statusawal_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_NA" data-value-separator="<?php echo $master_statusawal->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_statusawal->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $master_statusawal->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->PotonganSPI_Prosen->Visible) { // PotonganSPI_Prosen ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_PotonganSPI_Prosen" class="form-group">
		<label id="elh_master_statusawal_PotonganSPI_Prosen" for="x_PotonganSPI_Prosen" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->PotonganSPI_Prosen->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->PotonganSPI_Prosen->CellAttributes() ?>>
<span id="el_master_statusawal_PotonganSPI_Prosen">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPI_Prosen" name="x_PotonganSPI_Prosen" id="x_PotonganSPI_Prosen" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPI_Prosen->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPI_Prosen->EditValue ?>"<?php echo $master_statusawal->PotonganSPI_Prosen->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->PotonganSPI_Prosen->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_PotonganSPI_Prosen">
		<td><span id="elh_master_statusawal_PotonganSPI_Prosen"><?php echo $master_statusawal->PotonganSPI_Prosen->FldCaption() ?></span></td>
		<td<?php echo $master_statusawal->PotonganSPI_Prosen->CellAttributes() ?>>
<span id="el_master_statusawal_PotonganSPI_Prosen">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPI_Prosen" name="x_PotonganSPI_Prosen" id="x_PotonganSPI_Prosen" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPI_Prosen->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPI_Prosen->EditValue ?>"<?php echo $master_statusawal->PotonganSPI_Prosen->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->PotonganSPI_Prosen->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->PotonganSPI_Nominal->Visible) { // PotonganSPI_Nominal ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_PotonganSPI_Nominal" class="form-group">
		<label id="elh_master_statusawal_PotonganSPI_Nominal" for="x_PotonganSPI_Nominal" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->PotonganSPI_Nominal->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->PotonganSPI_Nominal->CellAttributes() ?>>
<span id="el_master_statusawal_PotonganSPI_Nominal">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPI_Nominal" name="x_PotonganSPI_Nominal" id="x_PotonganSPI_Nominal" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPI_Nominal->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPI_Nominal->EditValue ?>"<?php echo $master_statusawal->PotonganSPI_Nominal->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->PotonganSPI_Nominal->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_PotonganSPI_Nominal">
		<td><span id="elh_master_statusawal_PotonganSPI_Nominal"><?php echo $master_statusawal->PotonganSPI_Nominal->FldCaption() ?></span></td>
		<td<?php echo $master_statusawal->PotonganSPI_Nominal->CellAttributes() ?>>
<span id="el_master_statusawal_PotonganSPI_Nominal">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPI_Nominal" name="x_PotonganSPI_Nominal" id="x_PotonganSPI_Nominal" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPI_Nominal->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPI_Nominal->EditValue ?>"<?php echo $master_statusawal->PotonganSPI_Nominal->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->PotonganSPI_Nominal->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->PotonganSPP_Prosen->Visible) { // PotonganSPP_Prosen ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_PotonganSPP_Prosen" class="form-group">
		<label id="elh_master_statusawal_PotonganSPP_Prosen" for="x_PotonganSPP_Prosen" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->PotonganSPP_Prosen->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->PotonganSPP_Prosen->CellAttributes() ?>>
<span id="el_master_statusawal_PotonganSPP_Prosen">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPP_Prosen" name="x_PotonganSPP_Prosen" id="x_PotonganSPP_Prosen" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPP_Prosen->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPP_Prosen->EditValue ?>"<?php echo $master_statusawal->PotonganSPP_Prosen->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->PotonganSPP_Prosen->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_PotonganSPP_Prosen">
		<td><span id="elh_master_statusawal_PotonganSPP_Prosen"><?php echo $master_statusawal->PotonganSPP_Prosen->FldCaption() ?></span></td>
		<td<?php echo $master_statusawal->PotonganSPP_Prosen->CellAttributes() ?>>
<span id="el_master_statusawal_PotonganSPP_Prosen">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPP_Prosen" name="x_PotonganSPP_Prosen" id="x_PotonganSPP_Prosen" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPP_Prosen->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPP_Prosen->EditValue ?>"<?php echo $master_statusawal->PotonganSPP_Prosen->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->PotonganSPP_Prosen->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->PotonganSPP_Nominal->Visible) { // PotonganSPP_Nominal ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
	<div id="r_PotonganSPP_Nominal" class="form-group">
		<label id="elh_master_statusawal_PotonganSPP_Nominal" for="x_PotonganSPP_Nominal" class="col-sm-2 control-label ewLabel"><?php echo $master_statusawal->PotonganSPP_Nominal->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_statusawal->PotonganSPP_Nominal->CellAttributes() ?>>
<span id="el_master_statusawal_PotonganSPP_Nominal">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPP_Nominal" name="x_PotonganSPP_Nominal" id="x_PotonganSPP_Nominal" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPP_Nominal->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPP_Nominal->EditValue ?>"<?php echo $master_statusawal->PotonganSPP_Nominal->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->PotonganSPP_Nominal->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_PotonganSPP_Nominal">
		<td><span id="elh_master_statusawal_PotonganSPP_Nominal"><?php echo $master_statusawal->PotonganSPP_Nominal->FldCaption() ?></span></td>
		<td<?php echo $master_statusawal->PotonganSPP_Nominal->CellAttributes() ?>>
<span id="el_master_statusawal_PotonganSPP_Nominal">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPP_Nominal" name="x_PotonganSPP_Nominal" id="x_PotonganSPP_Nominal" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPP_Nominal->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPP_Nominal->EditValue ?>"<?php echo $master_statusawal->PotonganSPP_Nominal->EditAttributes() ?>>
</span>
<?php echo $master_statusawal->PotonganSPP_Nominal->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_statusawal_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$master_statusawal_add->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $master_statusawal_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_statusawaladd.Init();
</script>
<?php
$master_statusawal_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_statusawal_add->Page_Terminate();
?>
