<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "programinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$program_add = NULL; // Initialize page object first

class cprogram_add extends cprogram {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'program';

	// Page object name
	var $PageObjName = 'program_add';

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

		// Table object (program)
		if (!isset($GLOBALS["program"]) || get_class($GLOBALS["program"]) == "cprogram") {
			$GLOBALS["program"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["program"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'program', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("programlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->ProgramID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->KodeID->SetVisibility();
		$this->Def->SetVisibility();
		$this->LoginBuat->SetVisibility();
		$this->TanggalBuat->SetVisibility();
		$this->LoginEdit->SetVisibility();
		$this->TanggalEdit->SetVisibility();
		$this->Keterangan->SetVisibility();
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
		global $EW_EXPORT, $program;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($program);
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
			if (@$_GET["ProgramID"] != "") {
				$this->ProgramID->setQueryStringValue($_GET["ProgramID"]);
				$this->setKey("ProgramID", $this->ProgramID->CurrentValue); // Set up key
			} else {
				$this->setKey("ProgramID", ""); // Clear key
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
					$this->Page_Terminate("programlist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "programlist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "programview.php")
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
		$this->ProgramID->CurrentValue = NULL;
		$this->ProgramID->OldValue = $this->ProgramID->CurrentValue;
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->KodeID->CurrentValue = NULL;
		$this->KodeID->OldValue = $this->KodeID->CurrentValue;
		$this->Def->CurrentValue = "N";
		$this->LoginBuat->CurrentValue = NULL;
		$this->LoginBuat->OldValue = $this->LoginBuat->CurrentValue;
		$this->TanggalBuat->CurrentValue = NULL;
		$this->TanggalBuat->OldValue = $this->TanggalBuat->CurrentValue;
		$this->LoginEdit->CurrentValue = NULL;
		$this->LoginEdit->OldValue = $this->LoginEdit->CurrentValue;
		$this->TanggalEdit->CurrentValue = NULL;
		$this->TanggalEdit->OldValue = $this->TanggalEdit->CurrentValue;
		$this->Keterangan->CurrentValue = NULL;
		$this->Keterangan->OldValue = $this->Keterangan->CurrentValue;
		$this->NA->CurrentValue = "N";
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->ProgramID->FldIsDetailKey) {
			$this->ProgramID->setFormValue($objForm->GetValue("x_ProgramID"));
		}
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->KodeID->FldIsDetailKey) {
			$this->KodeID->setFormValue($objForm->GetValue("x_KodeID"));
		}
		if (!$this->Def->FldIsDetailKey) {
			$this->Def->setFormValue($objForm->GetValue("x_Def"));
		}
		if (!$this->LoginBuat->FldIsDetailKey) {
			$this->LoginBuat->setFormValue($objForm->GetValue("x_LoginBuat"));
		}
		if (!$this->TanggalBuat->FldIsDetailKey) {
			$this->TanggalBuat->setFormValue($objForm->GetValue("x_TanggalBuat"));
			$this->TanggalBuat->CurrentValue = ew_UnFormatDateTime($this->TanggalBuat->CurrentValue, 0);
		}
		if (!$this->LoginEdit->FldIsDetailKey) {
			$this->LoginEdit->setFormValue($objForm->GetValue("x_LoginEdit"));
		}
		if (!$this->TanggalEdit->FldIsDetailKey) {
			$this->TanggalEdit->setFormValue($objForm->GetValue("x_TanggalEdit"));
			$this->TanggalEdit->CurrentValue = ew_UnFormatDateTime($this->TanggalEdit->CurrentValue, 0);
		}
		if (!$this->Keterangan->FldIsDetailKey) {
			$this->Keterangan->setFormValue($objForm->GetValue("x_Keterangan"));
		}
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->ProgramID->CurrentValue = $this->ProgramID->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->KodeID->CurrentValue = $this->KodeID->FormValue;
		$this->Def->CurrentValue = $this->Def->FormValue;
		$this->LoginBuat->CurrentValue = $this->LoginBuat->FormValue;
		$this->TanggalBuat->CurrentValue = $this->TanggalBuat->FormValue;
		$this->TanggalBuat->CurrentValue = ew_UnFormatDateTime($this->TanggalBuat->CurrentValue, 0);
		$this->LoginEdit->CurrentValue = $this->LoginEdit->FormValue;
		$this->TanggalEdit->CurrentValue = $this->TanggalEdit->FormValue;
		$this->TanggalEdit->CurrentValue = ew_UnFormatDateTime($this->TanggalEdit->CurrentValue, 0);
		$this->Keterangan->CurrentValue = $this->Keterangan->FormValue;
		$this->NA->CurrentValue = $this->NA->FormValue;
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
		$this->ProgramID->setDbValue($rs->fields('ProgramID'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->KodeID->setDbValue($rs->fields('KodeID'));
		$this->Def->setDbValue($rs->fields('Def'));
		$this->LoginBuat->setDbValue($rs->fields('LoginBuat'));
		$this->TanggalBuat->setDbValue($rs->fields('TanggalBuat'));
		$this->LoginEdit->setDbValue($rs->fields('LoginEdit'));
		$this->TanggalEdit->setDbValue($rs->fields('TanggalEdit'));
		$this->Keterangan->setDbValue($rs->fields('Keterangan'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->ProgramID->DbValue = $row['ProgramID'];
		$this->Nama->DbValue = $row['Nama'];
		$this->KodeID->DbValue = $row['KodeID'];
		$this->Def->DbValue = $row['Def'];
		$this->LoginBuat->DbValue = $row['LoginBuat'];
		$this->TanggalBuat->DbValue = $row['TanggalBuat'];
		$this->LoginEdit->DbValue = $row['LoginEdit'];
		$this->TanggalEdit->DbValue = $row['TanggalEdit'];
		$this->Keterangan->DbValue = $row['Keterangan'];
		$this->NA->DbValue = $row['NA'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("ProgramID")) <> "")
			$this->ProgramID->CurrentValue = $this->getKey("ProgramID"); // ProgramID
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
		// ProgramID
		// Nama
		// KodeID
		// Def
		// LoginBuat
		// TanggalBuat
		// LoginEdit
		// TanggalEdit
		// Keterangan
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// ProgramID
		$this->ProgramID->ViewValue = $this->ProgramID->CurrentValue;
		$this->ProgramID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// KodeID
		$this->KodeID->ViewValue = $this->KodeID->CurrentValue;
		$this->KodeID->ViewCustomAttributes = "";

		// Def
		if (ew_ConvertToBool($this->Def->CurrentValue)) {
			$this->Def->ViewValue = $this->Def->FldTagCaption(1) <> "" ? $this->Def->FldTagCaption(1) : "Y";
		} else {
			$this->Def->ViewValue = $this->Def->FldTagCaption(2) <> "" ? $this->Def->FldTagCaption(2) : "N";
		}
		$this->Def->ViewCustomAttributes = "";

		// LoginBuat
		$this->LoginBuat->ViewValue = $this->LoginBuat->CurrentValue;
		$this->LoginBuat->ViewCustomAttributes = "";

		// TanggalBuat
		$this->TanggalBuat->ViewValue = $this->TanggalBuat->CurrentValue;
		$this->TanggalBuat->ViewValue = ew_FormatDateTime($this->TanggalBuat->ViewValue, 0);
		$this->TanggalBuat->ViewCustomAttributes = "";

		// LoginEdit
		$this->LoginEdit->ViewValue = $this->LoginEdit->CurrentValue;
		$this->LoginEdit->ViewCustomAttributes = "";

		// TanggalEdit
		$this->TanggalEdit->ViewValue = $this->TanggalEdit->CurrentValue;
		$this->TanggalEdit->ViewValue = ew_FormatDateTime($this->TanggalEdit->ViewValue, 0);
		$this->TanggalEdit->ViewCustomAttributes = "";

		// Keterangan
		$this->Keterangan->ViewValue = $this->Keterangan->CurrentValue;
		$this->Keterangan->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

			// ProgramID
			$this->ProgramID->LinkCustomAttributes = "";
			$this->ProgramID->HrefValue = "";
			$this->ProgramID->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// KodeID
			$this->KodeID->LinkCustomAttributes = "";
			$this->KodeID->HrefValue = "";
			$this->KodeID->TooltipValue = "";

			// Def
			$this->Def->LinkCustomAttributes = "";
			$this->Def->HrefValue = "";
			$this->Def->TooltipValue = "";

			// LoginBuat
			$this->LoginBuat->LinkCustomAttributes = "";
			$this->LoginBuat->HrefValue = "";
			$this->LoginBuat->TooltipValue = "";

			// TanggalBuat
			$this->TanggalBuat->LinkCustomAttributes = "";
			$this->TanggalBuat->HrefValue = "";
			$this->TanggalBuat->TooltipValue = "";

			// LoginEdit
			$this->LoginEdit->LinkCustomAttributes = "";
			$this->LoginEdit->HrefValue = "";
			$this->LoginEdit->TooltipValue = "";

			// TanggalEdit
			$this->TanggalEdit->LinkCustomAttributes = "";
			$this->TanggalEdit->HrefValue = "";
			$this->TanggalEdit->TooltipValue = "";

			// Keterangan
			$this->Keterangan->LinkCustomAttributes = "";
			$this->Keterangan->HrefValue = "";
			$this->Keterangan->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// ProgramID
			$this->ProgramID->EditAttrs["class"] = "form-control";
			$this->ProgramID->EditCustomAttributes = "";
			$this->ProgramID->EditValue = ew_HtmlEncode($this->ProgramID->CurrentValue);
			$this->ProgramID->PlaceHolder = ew_RemoveHtml($this->ProgramID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// KodeID
			$this->KodeID->EditAttrs["class"] = "form-control";
			$this->KodeID->EditCustomAttributes = "";
			$this->KodeID->EditValue = ew_HtmlEncode($this->KodeID->CurrentValue);
			$this->KodeID->PlaceHolder = ew_RemoveHtml($this->KodeID->FldCaption());

			// Def
			$this->Def->EditCustomAttributes = "";
			$this->Def->EditValue = $this->Def->Options(FALSE);

			// LoginBuat
			$this->LoginBuat->EditAttrs["class"] = "form-control";
			$this->LoginBuat->EditCustomAttributes = "";
			$this->LoginBuat->EditValue = ew_HtmlEncode($this->LoginBuat->CurrentValue);
			$this->LoginBuat->PlaceHolder = ew_RemoveHtml($this->LoginBuat->FldCaption());

			// TanggalBuat
			$this->TanggalBuat->EditAttrs["class"] = "form-control";
			$this->TanggalBuat->EditCustomAttributes = "";
			$this->TanggalBuat->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->TanggalBuat->CurrentValue, 8));
			$this->TanggalBuat->PlaceHolder = ew_RemoveHtml($this->TanggalBuat->FldCaption());

			// LoginEdit
			$this->LoginEdit->EditAttrs["class"] = "form-control";
			$this->LoginEdit->EditCustomAttributes = "";
			$this->LoginEdit->EditValue = ew_HtmlEncode($this->LoginEdit->CurrentValue);
			$this->LoginEdit->PlaceHolder = ew_RemoveHtml($this->LoginEdit->FldCaption());

			// TanggalEdit
			$this->TanggalEdit->EditAttrs["class"] = "form-control";
			$this->TanggalEdit->EditCustomAttributes = "";
			$this->TanggalEdit->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->TanggalEdit->CurrentValue, 8));
			$this->TanggalEdit->PlaceHolder = ew_RemoveHtml($this->TanggalEdit->FldCaption());

			// Keterangan
			$this->Keterangan->EditAttrs["class"] = "form-control";
			$this->Keterangan->EditCustomAttributes = "";
			$this->Keterangan->EditValue = ew_HtmlEncode($this->Keterangan->CurrentValue);
			$this->Keterangan->PlaceHolder = ew_RemoveHtml($this->Keterangan->FldCaption());

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// ProgramID

			$this->ProgramID->LinkCustomAttributes = "";
			$this->ProgramID->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// KodeID
			$this->KodeID->LinkCustomAttributes = "";
			$this->KodeID->HrefValue = "";

			// Def
			$this->Def->LinkCustomAttributes = "";
			$this->Def->HrefValue = "";

			// LoginBuat
			$this->LoginBuat->LinkCustomAttributes = "";
			$this->LoginBuat->HrefValue = "";

			// TanggalBuat
			$this->TanggalBuat->LinkCustomAttributes = "";
			$this->TanggalBuat->HrefValue = "";

			// LoginEdit
			$this->LoginEdit->LinkCustomAttributes = "";
			$this->LoginEdit->HrefValue = "";

			// TanggalEdit
			$this->TanggalEdit->LinkCustomAttributes = "";
			$this->TanggalEdit->HrefValue = "";

			// Keterangan
			$this->Keterangan->LinkCustomAttributes = "";
			$this->Keterangan->HrefValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
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
		if (!$this->ProgramID->FldIsDetailKey && !is_null($this->ProgramID->FormValue) && $this->ProgramID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->ProgramID->FldCaption(), $this->ProgramID->ReqErrMsg));
		}
		if ($this->Def->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Def->FldCaption(), $this->Def->ReqErrMsg));
		}
		if (!ew_CheckDateDef($this->TanggalBuat->FormValue)) {
			ew_AddMessage($gsFormError, $this->TanggalBuat->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TanggalEdit->FormValue)) {
			ew_AddMessage($gsFormError, $this->TanggalEdit->FldErrMsg());
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
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// ProgramID
		$this->ProgramID->SetDbValueDef($rsnew, $this->ProgramID->CurrentValue, "", FALSE);

		// Nama
		$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, NULL, FALSE);

		// KodeID
		$this->KodeID->SetDbValueDef($rsnew, $this->KodeID->CurrentValue, NULL, FALSE);

		// Def
		$this->Def->SetDbValueDef($rsnew, ((strval($this->Def->CurrentValue) == "Y") ? "Y" : "N"), "N", strval($this->Def->CurrentValue) == "");

		// LoginBuat
		$this->LoginBuat->SetDbValueDef($rsnew, $this->LoginBuat->CurrentValue, NULL, FALSE);

		// TanggalBuat
		$this->TanggalBuat->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TanggalBuat->CurrentValue, 0), NULL, FALSE);

		// LoginEdit
		$this->LoginEdit->SetDbValueDef($rsnew, $this->LoginEdit->CurrentValue, NULL, FALSE);

		// TanggalEdit
		$this->TanggalEdit->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TanggalEdit->CurrentValue, 0), NULL, FALSE);

		// Keterangan
		$this->Keterangan->SetDbValueDef($rsnew, $this->Keterangan->CurrentValue, NULL, FALSE);

		// NA
		$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), NULL, strval($this->NA->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['ProgramID']) == "") {
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("programlist.php"), "", $this->TableVar, TRUE);
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
if (!isset($program_add)) $program_add = new cprogram_add();

// Page init
$program_add->Page_Init();

// Page main
$program_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$program_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fprogramadd = new ew_Form("fprogramadd", "add");

// Validate form
fprogramadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_ProgramID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $program->ProgramID->FldCaption(), $program->ProgramID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Def");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $program->Def->FldCaption(), $program->Def->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TanggalBuat");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($program->TanggalBuat->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_TanggalEdit");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($program->TanggalEdit->FldErrMsg()) ?>");

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
fprogramadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fprogramadd.ValidateRequired = true;
<?php } else { ?>
fprogramadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fprogramadd.Lists["x_Def"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fprogramadd.Lists["x_Def"].Options = <?php echo json_encode($program->Def->Options()) ?>;
fprogramadd.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fprogramadd.Lists["x_NA"].Options = <?php echo json_encode($program->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$program_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $program_add->ShowPageHeader(); ?>
<?php
$program_add->ShowMessage();
?>
<form name="fprogramadd" id="fprogramadd" class="<?php echo $program_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($program_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $program_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="program">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($program_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$program_add->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_programadd" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($program->ProgramID->Visible) { // ProgramID ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
	<div id="r_ProgramID" class="form-group">
		<label id="elh_program_ProgramID" for="x_ProgramID" class="col-sm-2 control-label ewLabel"><?php echo $program->ProgramID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $program->ProgramID->CellAttributes() ?>>
<span id="el_program_ProgramID">
<input type="text" data-table="program" data-field="x_ProgramID" name="x_ProgramID" id="x_ProgramID" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->ProgramID->getPlaceHolder()) ?>" value="<?php echo $program->ProgramID->EditValue ?>"<?php echo $program->ProgramID->EditAttributes() ?>>
</span>
<?php echo $program->ProgramID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProgramID">
		<td><span id="elh_program_ProgramID"><?php echo $program->ProgramID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $program->ProgramID->CellAttributes() ?>>
<span id="el_program_ProgramID">
<input type="text" data-table="program" data-field="x_ProgramID" name="x_ProgramID" id="x_ProgramID" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->ProgramID->getPlaceHolder()) ?>" value="<?php echo $program->ProgramID->EditValue ?>"<?php echo $program->ProgramID->EditAttributes() ?>>
</span>
<?php echo $program->ProgramID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label id="elh_program_Nama" for="x_Nama" class="col-sm-2 control-label ewLabel"><?php echo $program->Nama->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $program->Nama->CellAttributes() ?>>
<span id="el_program_Nama">
<input type="text" data-table="program" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($program->Nama->getPlaceHolder()) ?>" value="<?php echo $program->Nama->EditValue ?>"<?php echo $program->Nama->EditAttributes() ?>>
</span>
<?php echo $program->Nama->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_program_Nama"><?php echo $program->Nama->FldCaption() ?></span></td>
		<td<?php echo $program->Nama->CellAttributes() ?>>
<span id="el_program_Nama">
<input type="text" data-table="program" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($program->Nama->getPlaceHolder()) ?>" value="<?php echo $program->Nama->EditValue ?>"<?php echo $program->Nama->EditAttributes() ?>>
</span>
<?php echo $program->Nama->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->KodeID->Visible) { // KodeID ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
	<div id="r_KodeID" class="form-group">
		<label id="elh_program_KodeID" for="x_KodeID" class="col-sm-2 control-label ewLabel"><?php echo $program->KodeID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $program->KodeID->CellAttributes() ?>>
<span id="el_program_KodeID">
<input type="text" data-table="program" data-field="x_KodeID" name="x_KodeID" id="x_KodeID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($program->KodeID->getPlaceHolder()) ?>" value="<?php echo $program->KodeID->EditValue ?>"<?php echo $program->KodeID->EditAttributes() ?>>
</span>
<?php echo $program->KodeID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodeID">
		<td><span id="elh_program_KodeID"><?php echo $program->KodeID->FldCaption() ?></span></td>
		<td<?php echo $program->KodeID->CellAttributes() ?>>
<span id="el_program_KodeID">
<input type="text" data-table="program" data-field="x_KodeID" name="x_KodeID" id="x_KodeID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($program->KodeID->getPlaceHolder()) ?>" value="<?php echo $program->KodeID->EditValue ?>"<?php echo $program->KodeID->EditAttributes() ?>>
</span>
<?php echo $program->KodeID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->Def->Visible) { // Def ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
	<div id="r_Def" class="form-group">
		<label id="elh_program_Def" class="col-sm-2 control-label ewLabel"><?php echo $program->Def->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $program->Def->CellAttributes() ?>>
<span id="el_program_Def">
<div id="tp_x_Def" class="ewTemplate"><input type="radio" data-table="program" data-field="x_Def" data-value-separator="<?php echo $program->Def->DisplayValueSeparatorAttribute() ?>" name="x_Def" id="x_Def" value="{value}"<?php echo $program->Def->EditAttributes() ?>></div>
<div id="dsl_x_Def" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $program->Def->RadioButtonListHtml(FALSE, "x_Def") ?>
</div></div>
</span>
<?php echo $program->Def->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Def">
		<td><span id="elh_program_Def"><?php echo $program->Def->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $program->Def->CellAttributes() ?>>
<span id="el_program_Def">
<div id="tp_x_Def" class="ewTemplate"><input type="radio" data-table="program" data-field="x_Def" data-value-separator="<?php echo $program->Def->DisplayValueSeparatorAttribute() ?>" name="x_Def" id="x_Def" value="{value}"<?php echo $program->Def->EditAttributes() ?>></div>
<div id="dsl_x_Def" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $program->Def->RadioButtonListHtml(FALSE, "x_Def") ?>
</div></div>
</span>
<?php echo $program->Def->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->LoginBuat->Visible) { // LoginBuat ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
	<div id="r_LoginBuat" class="form-group">
		<label id="elh_program_LoginBuat" for="x_LoginBuat" class="col-sm-2 control-label ewLabel"><?php echo $program->LoginBuat->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $program->LoginBuat->CellAttributes() ?>>
<span id="el_program_LoginBuat">
<input type="text" data-table="program" data-field="x_LoginBuat" name="x_LoginBuat" id="x_LoginBuat" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->LoginBuat->getPlaceHolder()) ?>" value="<?php echo $program->LoginBuat->EditValue ?>"<?php echo $program->LoginBuat->EditAttributes() ?>>
</span>
<?php echo $program->LoginBuat->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_LoginBuat">
		<td><span id="elh_program_LoginBuat"><?php echo $program->LoginBuat->FldCaption() ?></span></td>
		<td<?php echo $program->LoginBuat->CellAttributes() ?>>
<span id="el_program_LoginBuat">
<input type="text" data-table="program" data-field="x_LoginBuat" name="x_LoginBuat" id="x_LoginBuat" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->LoginBuat->getPlaceHolder()) ?>" value="<?php echo $program->LoginBuat->EditValue ?>"<?php echo $program->LoginBuat->EditAttributes() ?>>
</span>
<?php echo $program->LoginBuat->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->TanggalBuat->Visible) { // TanggalBuat ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
	<div id="r_TanggalBuat" class="form-group">
		<label id="elh_program_TanggalBuat" for="x_TanggalBuat" class="col-sm-2 control-label ewLabel"><?php echo $program->TanggalBuat->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $program->TanggalBuat->CellAttributes() ?>>
<span id="el_program_TanggalBuat">
<input type="text" data-table="program" data-field="x_TanggalBuat" name="x_TanggalBuat" id="x_TanggalBuat" placeholder="<?php echo ew_HtmlEncode($program->TanggalBuat->getPlaceHolder()) ?>" value="<?php echo $program->TanggalBuat->EditValue ?>"<?php echo $program->TanggalBuat->EditAttributes() ?>>
</span>
<?php echo $program->TanggalBuat->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanggalBuat">
		<td><span id="elh_program_TanggalBuat"><?php echo $program->TanggalBuat->FldCaption() ?></span></td>
		<td<?php echo $program->TanggalBuat->CellAttributes() ?>>
<span id="el_program_TanggalBuat">
<input type="text" data-table="program" data-field="x_TanggalBuat" name="x_TanggalBuat" id="x_TanggalBuat" placeholder="<?php echo ew_HtmlEncode($program->TanggalBuat->getPlaceHolder()) ?>" value="<?php echo $program->TanggalBuat->EditValue ?>"<?php echo $program->TanggalBuat->EditAttributes() ?>>
</span>
<?php echo $program->TanggalBuat->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->LoginEdit->Visible) { // LoginEdit ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
	<div id="r_LoginEdit" class="form-group">
		<label id="elh_program_LoginEdit" for="x_LoginEdit" class="col-sm-2 control-label ewLabel"><?php echo $program->LoginEdit->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $program->LoginEdit->CellAttributes() ?>>
<span id="el_program_LoginEdit">
<input type="text" data-table="program" data-field="x_LoginEdit" name="x_LoginEdit" id="x_LoginEdit" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->LoginEdit->getPlaceHolder()) ?>" value="<?php echo $program->LoginEdit->EditValue ?>"<?php echo $program->LoginEdit->EditAttributes() ?>>
</span>
<?php echo $program->LoginEdit->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_LoginEdit">
		<td><span id="elh_program_LoginEdit"><?php echo $program->LoginEdit->FldCaption() ?></span></td>
		<td<?php echo $program->LoginEdit->CellAttributes() ?>>
<span id="el_program_LoginEdit">
<input type="text" data-table="program" data-field="x_LoginEdit" name="x_LoginEdit" id="x_LoginEdit" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->LoginEdit->getPlaceHolder()) ?>" value="<?php echo $program->LoginEdit->EditValue ?>"<?php echo $program->LoginEdit->EditAttributes() ?>>
</span>
<?php echo $program->LoginEdit->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->TanggalEdit->Visible) { // TanggalEdit ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
	<div id="r_TanggalEdit" class="form-group">
		<label id="elh_program_TanggalEdit" for="x_TanggalEdit" class="col-sm-2 control-label ewLabel"><?php echo $program->TanggalEdit->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $program->TanggalEdit->CellAttributes() ?>>
<span id="el_program_TanggalEdit">
<input type="text" data-table="program" data-field="x_TanggalEdit" name="x_TanggalEdit" id="x_TanggalEdit" placeholder="<?php echo ew_HtmlEncode($program->TanggalEdit->getPlaceHolder()) ?>" value="<?php echo $program->TanggalEdit->EditValue ?>"<?php echo $program->TanggalEdit->EditAttributes() ?>>
</span>
<?php echo $program->TanggalEdit->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanggalEdit">
		<td><span id="elh_program_TanggalEdit"><?php echo $program->TanggalEdit->FldCaption() ?></span></td>
		<td<?php echo $program->TanggalEdit->CellAttributes() ?>>
<span id="el_program_TanggalEdit">
<input type="text" data-table="program" data-field="x_TanggalEdit" name="x_TanggalEdit" id="x_TanggalEdit" placeholder="<?php echo ew_HtmlEncode($program->TanggalEdit->getPlaceHolder()) ?>" value="<?php echo $program->TanggalEdit->EditValue ?>"<?php echo $program->TanggalEdit->EditAttributes() ?>>
</span>
<?php echo $program->TanggalEdit->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->Keterangan->Visible) { // Keterangan ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
	<div id="r_Keterangan" class="form-group">
		<label id="elh_program_Keterangan" for="x_Keterangan" class="col-sm-2 control-label ewLabel"><?php echo $program->Keterangan->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $program->Keterangan->CellAttributes() ?>>
<span id="el_program_Keterangan">
<textarea data-table="program" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($program->Keterangan->getPlaceHolder()) ?>"<?php echo $program->Keterangan->EditAttributes() ?>><?php echo $program->Keterangan->EditValue ?></textarea>
</span>
<?php echo $program->Keterangan->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Keterangan">
		<td><span id="elh_program_Keterangan"><?php echo $program->Keterangan->FldCaption() ?></span></td>
		<td<?php echo $program->Keterangan->CellAttributes() ?>>
<span id="el_program_Keterangan">
<textarea data-table="program" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($program->Keterangan->getPlaceHolder()) ?>"<?php echo $program->Keterangan->EditAttributes() ?>><?php echo $program->Keterangan->EditValue ?></textarea>
</span>
<?php echo $program->Keterangan->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_program_NA" class="col-sm-2 control-label ewLabel"><?php echo $program->NA->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $program->NA->CellAttributes() ?>>
<span id="el_program_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="program" data-field="x_NA" data-value-separator="<?php echo $program->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $program->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $program->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $program->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_program_NA"><?php echo $program->NA->FldCaption() ?></span></td>
		<td<?php echo $program->NA->CellAttributes() ?>>
<span id="el_program_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="program" data-field="x_NA" data-value-separator="<?php echo $program->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $program->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $program->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $program->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $program_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$program_add->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $program_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fprogramadd.Init();
</script>
<?php
$program_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$program_add->Page_Terminate();
?>
