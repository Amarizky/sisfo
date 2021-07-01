<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "ruanginfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$ruang_add = NULL; // Initialize page object first

class cruang_add extends cruang {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'ruang';

	// Page object name
	var $PageObjName = 'ruang_add';

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

		// Table object (ruang)
		if (!isset($GLOBALS["ruang"]) || get_class($GLOBALS["ruang"]) == "cruang") {
			$GLOBALS["ruang"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["ruang"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'ruang', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("ruanglist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->RuangID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->KampusID->SetVisibility();
		$this->Lantai->SetVisibility();
		$this->ProdiID->SetVisibility();
		$this->Kapasitas->SetVisibility();
		$this->KapasitasUjian->SetVisibility();
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
		global $EW_EXPORT, $ruang;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($ruang);
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
			if (@$_GET["RuangID"] != "") {
				$this->RuangID->setQueryStringValue($_GET["RuangID"]);
				$this->setKey("RuangID", $this->RuangID->CurrentValue); // Set up key
			} else {
				$this->setKey("RuangID", ""); // Clear key
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
					$this->Page_Terminate("ruanglist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "ruanglist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "ruangview.php")
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
		$this->RuangID->CurrentValue = NULL;
		$this->RuangID->OldValue = $this->RuangID->CurrentValue;
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->KampusID->CurrentValue = NULL;
		$this->KampusID->OldValue = $this->KampusID->CurrentValue;
		$this->Lantai->CurrentValue = 1;
		$this->ProdiID->CurrentValue = NULL;
		$this->ProdiID->OldValue = $this->ProdiID->CurrentValue;
		$this->Kapasitas->CurrentValue = 0;
		$this->KapasitasUjian->CurrentValue = 0;
		$this->Keterangan->CurrentValue = NULL;
		$this->Keterangan->OldValue = $this->Keterangan->CurrentValue;
		$this->NA->CurrentValue = "N";
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->RuangID->FldIsDetailKey) {
			$this->RuangID->setFormValue($objForm->GetValue("x_RuangID"));
		}
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->KampusID->FldIsDetailKey) {
			$this->KampusID->setFormValue($objForm->GetValue("x_KampusID"));
		}
		if (!$this->Lantai->FldIsDetailKey) {
			$this->Lantai->setFormValue($objForm->GetValue("x_Lantai"));
		}
		if (!$this->ProdiID->FldIsDetailKey) {
			$this->ProdiID->setFormValue($objForm->GetValue("x_ProdiID"));
		}
		if (!$this->Kapasitas->FldIsDetailKey) {
			$this->Kapasitas->setFormValue($objForm->GetValue("x_Kapasitas"));
		}
		if (!$this->KapasitasUjian->FldIsDetailKey) {
			$this->KapasitasUjian->setFormValue($objForm->GetValue("x_KapasitasUjian"));
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
		$this->RuangID->CurrentValue = $this->RuangID->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->KampusID->CurrentValue = $this->KampusID->FormValue;
		$this->Lantai->CurrentValue = $this->Lantai->FormValue;
		$this->ProdiID->CurrentValue = $this->ProdiID->FormValue;
		$this->Kapasitas->CurrentValue = $this->Kapasitas->FormValue;
		$this->KapasitasUjian->CurrentValue = $this->KapasitasUjian->FormValue;
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
		$this->RuangID->setDbValue($rs->fields('RuangID'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->KampusID->setDbValue($rs->fields('KampusID'));
		$this->Lantai->setDbValue($rs->fields('Lantai'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->Kapasitas->setDbValue($rs->fields('Kapasitas'));
		$this->KapasitasUjian->setDbValue($rs->fields('KapasitasUjian'));
		$this->Keterangan->setDbValue($rs->fields('Keterangan'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->RuangID->DbValue = $row['RuangID'];
		$this->Nama->DbValue = $row['Nama'];
		$this->KampusID->DbValue = $row['KampusID'];
		$this->Lantai->DbValue = $row['Lantai'];
		$this->ProdiID->DbValue = $row['ProdiID'];
		$this->Kapasitas->DbValue = $row['Kapasitas'];
		$this->KapasitasUjian->DbValue = $row['KapasitasUjian'];
		$this->Keterangan->DbValue = $row['Keterangan'];
		$this->NA->DbValue = $row['NA'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("RuangID")) <> "")
			$this->RuangID->CurrentValue = $this->getKey("RuangID"); // RuangID
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
		// RuangID
		// Nama
		// KampusID
		// Lantai
		// ProdiID
		// Kapasitas
		// KapasitasUjian
		// Keterangan
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// RuangID
		$this->RuangID->ViewValue = $this->RuangID->CurrentValue;
		$this->RuangID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// KampusID
		$this->KampusID->ViewValue = $this->KampusID->CurrentValue;
		$this->KampusID->ViewCustomAttributes = "";

		// Lantai
		$this->Lantai->ViewValue = $this->Lantai->CurrentValue;
		$this->Lantai->ViewCustomAttributes = "";

		// ProdiID
		$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
		$this->ProdiID->ViewCustomAttributes = "";

		// Kapasitas
		$this->Kapasitas->ViewValue = $this->Kapasitas->CurrentValue;
		$this->Kapasitas->ViewCustomAttributes = "";

		// KapasitasUjian
		$this->KapasitasUjian->ViewValue = $this->KapasitasUjian->CurrentValue;
		$this->KapasitasUjian->ViewCustomAttributes = "";

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

			// RuangID
			$this->RuangID->LinkCustomAttributes = "";
			$this->RuangID->HrefValue = "";
			$this->RuangID->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";
			$this->KampusID->TooltipValue = "";

			// Lantai
			$this->Lantai->LinkCustomAttributes = "";
			$this->Lantai->HrefValue = "";
			$this->Lantai->TooltipValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// Kapasitas
			$this->Kapasitas->LinkCustomAttributes = "";
			$this->Kapasitas->HrefValue = "";
			$this->Kapasitas->TooltipValue = "";

			// KapasitasUjian
			$this->KapasitasUjian->LinkCustomAttributes = "";
			$this->KapasitasUjian->HrefValue = "";
			$this->KapasitasUjian->TooltipValue = "";

			// Keterangan
			$this->Keterangan->LinkCustomAttributes = "";
			$this->Keterangan->HrefValue = "";
			$this->Keterangan->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// RuangID
			$this->RuangID->EditAttrs["class"] = "form-control";
			$this->RuangID->EditCustomAttributes = "";
			$this->RuangID->EditValue = ew_HtmlEncode($this->RuangID->CurrentValue);
			$this->RuangID->PlaceHolder = ew_RemoveHtml($this->RuangID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// KampusID
			$this->KampusID->EditAttrs["class"] = "form-control";
			$this->KampusID->EditCustomAttributes = "";
			$this->KampusID->EditValue = ew_HtmlEncode($this->KampusID->CurrentValue);
			$this->KampusID->PlaceHolder = ew_RemoveHtml($this->KampusID->FldCaption());

			// Lantai
			$this->Lantai->EditAttrs["class"] = "form-control";
			$this->Lantai->EditCustomAttributes = "";
			$this->Lantai->EditValue = ew_HtmlEncode($this->Lantai->CurrentValue);
			$this->Lantai->PlaceHolder = ew_RemoveHtml($this->Lantai->FldCaption());

			// ProdiID
			$this->ProdiID->EditAttrs["class"] = "form-control";
			$this->ProdiID->EditCustomAttributes = "";
			$this->ProdiID->EditValue = ew_HtmlEncode($this->ProdiID->CurrentValue);
			$this->ProdiID->PlaceHolder = ew_RemoveHtml($this->ProdiID->FldCaption());

			// Kapasitas
			$this->Kapasitas->EditAttrs["class"] = "form-control";
			$this->Kapasitas->EditCustomAttributes = "";
			$this->Kapasitas->EditValue = ew_HtmlEncode($this->Kapasitas->CurrentValue);
			$this->Kapasitas->PlaceHolder = ew_RemoveHtml($this->Kapasitas->FldCaption());

			// KapasitasUjian
			$this->KapasitasUjian->EditAttrs["class"] = "form-control";
			$this->KapasitasUjian->EditCustomAttributes = "";
			$this->KapasitasUjian->EditValue = ew_HtmlEncode($this->KapasitasUjian->CurrentValue);
			$this->KapasitasUjian->PlaceHolder = ew_RemoveHtml($this->KapasitasUjian->FldCaption());

			// Keterangan
			$this->Keterangan->EditAttrs["class"] = "form-control";
			$this->Keterangan->EditCustomAttributes = "";
			$this->Keterangan->EditValue = ew_HtmlEncode($this->Keterangan->CurrentValue);
			$this->Keterangan->PlaceHolder = ew_RemoveHtml($this->Keterangan->FldCaption());

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// RuangID

			$this->RuangID->LinkCustomAttributes = "";
			$this->RuangID->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";

			// Lantai
			$this->Lantai->LinkCustomAttributes = "";
			$this->Lantai->HrefValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";

			// Kapasitas
			$this->Kapasitas->LinkCustomAttributes = "";
			$this->Kapasitas->HrefValue = "";

			// KapasitasUjian
			$this->KapasitasUjian->LinkCustomAttributes = "";
			$this->KapasitasUjian->HrefValue = "";

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
		if (!$this->RuangID->FldIsDetailKey && !is_null($this->RuangID->FormValue) && $this->RuangID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->RuangID->FldCaption(), $this->RuangID->ReqErrMsg));
		}
		if (!$this->KampusID->FldIsDetailKey && !is_null($this->KampusID->FormValue) && $this->KampusID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->KampusID->FldCaption(), $this->KampusID->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->Lantai->FormValue)) {
			ew_AddMessage($gsFormError, $this->Lantai->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Kapasitas->FormValue)) {
			ew_AddMessage($gsFormError, $this->Kapasitas->FldErrMsg());
		}
		if (!ew_CheckInteger($this->KapasitasUjian->FormValue)) {
			ew_AddMessage($gsFormError, $this->KapasitasUjian->FldErrMsg());
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

		// RuangID
		$this->RuangID->SetDbValueDef($rsnew, $this->RuangID->CurrentValue, "", FALSE);

		// Nama
		$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, NULL, FALSE);

		// KampusID
		$this->KampusID->SetDbValueDef($rsnew, $this->KampusID->CurrentValue, "", FALSE);

		// Lantai
		$this->Lantai->SetDbValueDef($rsnew, $this->Lantai->CurrentValue, NULL, strval($this->Lantai->CurrentValue) == "");

		// ProdiID
		$this->ProdiID->SetDbValueDef($rsnew, $this->ProdiID->CurrentValue, NULL, FALSE);

		// Kapasitas
		$this->Kapasitas->SetDbValueDef($rsnew, $this->Kapasitas->CurrentValue, NULL, strval($this->Kapasitas->CurrentValue) == "");

		// KapasitasUjian
		$this->KapasitasUjian->SetDbValueDef($rsnew, $this->KapasitasUjian->CurrentValue, NULL, strval($this->KapasitasUjian->CurrentValue) == "");

		// Keterangan
		$this->Keterangan->SetDbValueDef($rsnew, $this->Keterangan->CurrentValue, NULL, FALSE);

		// NA
		$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), NULL, strval($this->NA->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['RuangID']) == "") {
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("ruanglist.php"), "", $this->TableVar, TRUE);
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
if (!isset($ruang_add)) $ruang_add = new cruang_add();

// Page init
$ruang_add->Page_Init();

// Page main
$ruang_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$ruang_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fruangadd = new ew_Form("fruangadd", "add");

// Validate form
fruangadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_RuangID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $ruang->RuangID->FldCaption(), $ruang->RuangID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_KampusID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $ruang->KampusID->FldCaption(), $ruang->KampusID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Lantai");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($ruang->Lantai->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Kapasitas");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($ruang->Kapasitas->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_KapasitasUjian");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($ruang->KapasitasUjian->FldErrMsg()) ?>");

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
fruangadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fruangadd.ValidateRequired = true;
<?php } else { ?>
fruangadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fruangadd.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fruangadd.Lists["x_NA"].Options = <?php echo json_encode($ruang->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$ruang_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $ruang_add->ShowPageHeader(); ?>
<?php
$ruang_add->ShowMessage();
?>
<form name="fruangadd" id="fruangadd" class="<?php echo $ruang_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($ruang_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $ruang_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="ruang">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($ruang_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$ruang_add->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $ruang_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_ruangadd" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($ruang->RuangID->Visible) { // RuangID ?>
<?php if (ew_IsMobile() || $ruang_add->IsModal) { ?>
	<div id="r_RuangID" class="form-group">
		<label id="elh_ruang_RuangID" for="x_RuangID" class="col-sm-2 control-label ewLabel"><?php echo $ruang->RuangID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $ruang->RuangID->CellAttributes() ?>>
<span id="el_ruang_RuangID">
<input type="text" data-table="ruang" data-field="x_RuangID" name="x_RuangID" id="x_RuangID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($ruang->RuangID->getPlaceHolder()) ?>" value="<?php echo $ruang->RuangID->EditValue ?>"<?php echo $ruang->RuangID->EditAttributes() ?>>
</span>
<?php echo $ruang->RuangID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_RuangID">
		<td><span id="elh_ruang_RuangID"><?php echo $ruang->RuangID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $ruang->RuangID->CellAttributes() ?>>
<span id="el_ruang_RuangID">
<input type="text" data-table="ruang" data-field="x_RuangID" name="x_RuangID" id="x_RuangID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($ruang->RuangID->getPlaceHolder()) ?>" value="<?php echo $ruang->RuangID->EditValue ?>"<?php echo $ruang->RuangID->EditAttributes() ?>>
</span>
<?php echo $ruang->RuangID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $ruang_add->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label id="elh_ruang_Nama" for="x_Nama" class="col-sm-2 control-label ewLabel"><?php echo $ruang->Nama->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $ruang->Nama->CellAttributes() ?>>
<span id="el_ruang_Nama">
<input type="text" data-table="ruang" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($ruang->Nama->getPlaceHolder()) ?>" value="<?php echo $ruang->Nama->EditValue ?>"<?php echo $ruang->Nama->EditAttributes() ?>>
</span>
<?php echo $ruang->Nama->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_ruang_Nama"><?php echo $ruang->Nama->FldCaption() ?></span></td>
		<td<?php echo $ruang->Nama->CellAttributes() ?>>
<span id="el_ruang_Nama">
<input type="text" data-table="ruang" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($ruang->Nama->getPlaceHolder()) ?>" value="<?php echo $ruang->Nama->EditValue ?>"<?php echo $ruang->Nama->EditAttributes() ?>>
</span>
<?php echo $ruang->Nama->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->KampusID->Visible) { // KampusID ?>
<?php if (ew_IsMobile() || $ruang_add->IsModal) { ?>
	<div id="r_KampusID" class="form-group">
		<label id="elh_ruang_KampusID" for="x_KampusID" class="col-sm-2 control-label ewLabel"><?php echo $ruang->KampusID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $ruang->KampusID->CellAttributes() ?>>
<span id="el_ruang_KampusID">
<input type="text" data-table="ruang" data-field="x_KampusID" name="x_KampusID" id="x_KampusID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($ruang->KampusID->getPlaceHolder()) ?>" value="<?php echo $ruang->KampusID->EditValue ?>"<?php echo $ruang->KampusID->EditAttributes() ?>>
</span>
<?php echo $ruang->KampusID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KampusID">
		<td><span id="elh_ruang_KampusID"><?php echo $ruang->KampusID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $ruang->KampusID->CellAttributes() ?>>
<span id="el_ruang_KampusID">
<input type="text" data-table="ruang" data-field="x_KampusID" name="x_KampusID" id="x_KampusID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($ruang->KampusID->getPlaceHolder()) ?>" value="<?php echo $ruang->KampusID->EditValue ?>"<?php echo $ruang->KampusID->EditAttributes() ?>>
</span>
<?php echo $ruang->KampusID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->Lantai->Visible) { // Lantai ?>
<?php if (ew_IsMobile() || $ruang_add->IsModal) { ?>
	<div id="r_Lantai" class="form-group">
		<label id="elh_ruang_Lantai" for="x_Lantai" class="col-sm-2 control-label ewLabel"><?php echo $ruang->Lantai->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $ruang->Lantai->CellAttributes() ?>>
<span id="el_ruang_Lantai">
<input type="text" data-table="ruang" data-field="x_Lantai" name="x_Lantai" id="x_Lantai" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->Lantai->getPlaceHolder()) ?>" value="<?php echo $ruang->Lantai->EditValue ?>"<?php echo $ruang->Lantai->EditAttributes() ?>>
</span>
<?php echo $ruang->Lantai->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Lantai">
		<td><span id="elh_ruang_Lantai"><?php echo $ruang->Lantai->FldCaption() ?></span></td>
		<td<?php echo $ruang->Lantai->CellAttributes() ?>>
<span id="el_ruang_Lantai">
<input type="text" data-table="ruang" data-field="x_Lantai" name="x_Lantai" id="x_Lantai" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->Lantai->getPlaceHolder()) ?>" value="<?php echo $ruang->Lantai->EditValue ?>"<?php echo $ruang->Lantai->EditAttributes() ?>>
</span>
<?php echo $ruang->Lantai->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $ruang_add->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label id="elh_ruang_ProdiID" for="x_ProdiID" class="col-sm-2 control-label ewLabel"><?php echo $ruang->ProdiID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $ruang->ProdiID->CellAttributes() ?>>
<span id="el_ruang_ProdiID">
<input type="text" data-table="ruang" data-field="x_ProdiID" name="x_ProdiID" id="x_ProdiID" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($ruang->ProdiID->getPlaceHolder()) ?>" value="<?php echo $ruang->ProdiID->EditValue ?>"<?php echo $ruang->ProdiID->EditAttributes() ?>>
</span>
<?php echo $ruang->ProdiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_ruang_ProdiID"><?php echo $ruang->ProdiID->FldCaption() ?></span></td>
		<td<?php echo $ruang->ProdiID->CellAttributes() ?>>
<span id="el_ruang_ProdiID">
<input type="text" data-table="ruang" data-field="x_ProdiID" name="x_ProdiID" id="x_ProdiID" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($ruang->ProdiID->getPlaceHolder()) ?>" value="<?php echo $ruang->ProdiID->EditValue ?>"<?php echo $ruang->ProdiID->EditAttributes() ?>>
</span>
<?php echo $ruang->ProdiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->Kapasitas->Visible) { // Kapasitas ?>
<?php if (ew_IsMobile() || $ruang_add->IsModal) { ?>
	<div id="r_Kapasitas" class="form-group">
		<label id="elh_ruang_Kapasitas" for="x_Kapasitas" class="col-sm-2 control-label ewLabel"><?php echo $ruang->Kapasitas->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $ruang->Kapasitas->CellAttributes() ?>>
<span id="el_ruang_Kapasitas">
<input type="text" data-table="ruang" data-field="x_Kapasitas" name="x_Kapasitas" id="x_Kapasitas" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->Kapasitas->getPlaceHolder()) ?>" value="<?php echo $ruang->Kapasitas->EditValue ?>"<?php echo $ruang->Kapasitas->EditAttributes() ?>>
</span>
<?php echo $ruang->Kapasitas->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Kapasitas">
		<td><span id="elh_ruang_Kapasitas"><?php echo $ruang->Kapasitas->FldCaption() ?></span></td>
		<td<?php echo $ruang->Kapasitas->CellAttributes() ?>>
<span id="el_ruang_Kapasitas">
<input type="text" data-table="ruang" data-field="x_Kapasitas" name="x_Kapasitas" id="x_Kapasitas" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->Kapasitas->getPlaceHolder()) ?>" value="<?php echo $ruang->Kapasitas->EditValue ?>"<?php echo $ruang->Kapasitas->EditAttributes() ?>>
</span>
<?php echo $ruang->Kapasitas->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->KapasitasUjian->Visible) { // KapasitasUjian ?>
<?php if (ew_IsMobile() || $ruang_add->IsModal) { ?>
	<div id="r_KapasitasUjian" class="form-group">
		<label id="elh_ruang_KapasitasUjian" for="x_KapasitasUjian" class="col-sm-2 control-label ewLabel"><?php echo $ruang->KapasitasUjian->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $ruang->KapasitasUjian->CellAttributes() ?>>
<span id="el_ruang_KapasitasUjian">
<input type="text" data-table="ruang" data-field="x_KapasitasUjian" name="x_KapasitasUjian" id="x_KapasitasUjian" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->KapasitasUjian->getPlaceHolder()) ?>" value="<?php echo $ruang->KapasitasUjian->EditValue ?>"<?php echo $ruang->KapasitasUjian->EditAttributes() ?>>
</span>
<?php echo $ruang->KapasitasUjian->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KapasitasUjian">
		<td><span id="elh_ruang_KapasitasUjian"><?php echo $ruang->KapasitasUjian->FldCaption() ?></span></td>
		<td<?php echo $ruang->KapasitasUjian->CellAttributes() ?>>
<span id="el_ruang_KapasitasUjian">
<input type="text" data-table="ruang" data-field="x_KapasitasUjian" name="x_KapasitasUjian" id="x_KapasitasUjian" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->KapasitasUjian->getPlaceHolder()) ?>" value="<?php echo $ruang->KapasitasUjian->EditValue ?>"<?php echo $ruang->KapasitasUjian->EditAttributes() ?>>
</span>
<?php echo $ruang->KapasitasUjian->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->Keterangan->Visible) { // Keterangan ?>
<?php if (ew_IsMobile() || $ruang_add->IsModal) { ?>
	<div id="r_Keterangan" class="form-group">
		<label id="elh_ruang_Keterangan" for="x_Keterangan" class="col-sm-2 control-label ewLabel"><?php echo $ruang->Keterangan->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $ruang->Keterangan->CellAttributes() ?>>
<span id="el_ruang_Keterangan">
<textarea data-table="ruang" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($ruang->Keterangan->getPlaceHolder()) ?>"<?php echo $ruang->Keterangan->EditAttributes() ?>><?php echo $ruang->Keterangan->EditValue ?></textarea>
</span>
<?php echo $ruang->Keterangan->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Keterangan">
		<td><span id="elh_ruang_Keterangan"><?php echo $ruang->Keterangan->FldCaption() ?></span></td>
		<td<?php echo $ruang->Keterangan->CellAttributes() ?>>
<span id="el_ruang_Keterangan">
<textarea data-table="ruang" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($ruang->Keterangan->getPlaceHolder()) ?>"<?php echo $ruang->Keterangan->EditAttributes() ?>><?php echo $ruang->Keterangan->EditValue ?></textarea>
</span>
<?php echo $ruang->Keterangan->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $ruang_add->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_ruang_NA" class="col-sm-2 control-label ewLabel"><?php echo $ruang->NA->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $ruang->NA->CellAttributes() ?>>
<span id="el_ruang_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="ruang" data-field="x_NA" data-value-separator="<?php echo $ruang->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $ruang->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $ruang->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $ruang->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_ruang_NA"><?php echo $ruang->NA->FldCaption() ?></span></td>
		<td<?php echo $ruang->NA->CellAttributes() ?>>
<span id="el_ruang_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="ruang" data-field="x_NA" data-value-separator="<?php echo $ruang->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $ruang->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $ruang->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $ruang->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $ruang_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$ruang_add->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $ruang_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fruangadd.Init();
</script>
<?php
$ruang_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$ruang_add->Page_Terminate();
?>
