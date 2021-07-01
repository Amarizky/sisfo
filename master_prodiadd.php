<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_prodiinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_prodi_add = NULL; // Initialize page object first

class cmaster_prodi_add extends cmaster_prodi {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_prodi';

	// Page object name
	var $PageObjName = 'master_prodi_add';

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

		// Table object (master_prodi)
		if (!isset($GLOBALS["master_prodi"]) || get_class($GLOBALS["master_prodi"]) == "cmaster_prodi") {
			$GLOBALS["master_prodi"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_prodi"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_prodi', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("master_prodilist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->ProdiID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->Pejabat->SetVisibility();
		$this->Keterangan->SetVisibility();
		$this->Akreditasi->SetVisibility();
		$this->NoSK->SetVisibility();
		$this->TglSK->SetVisibility();
		$this->Creator->SetVisibility();
		$this->CreateDate->SetVisibility();
		$this->NA->SetVisibility();

		// Set up multi page object
		$this->SetupMultiPages();

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
		global $EW_EXPORT, $master_prodi;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_prodi);
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
	var $MultiPages; // Multi pages object

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
			if (@$_GET["ProdiID"] != "") {
				$this->ProdiID->setQueryStringValue($_GET["ProdiID"]);
				$this->setKey("ProdiID", $this->ProdiID->CurrentValue); // Set up key
			} else {
				$this->setKey("ProdiID", ""); // Clear key
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
					$this->Page_Terminate("master_prodilist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "master_prodilist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "master_prodiview.php")
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
		$this->ProdiID->CurrentValue = NULL;
		$this->ProdiID->OldValue = $this->ProdiID->CurrentValue;
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->Pejabat->CurrentValue = NULL;
		$this->Pejabat->OldValue = $this->Pejabat->CurrentValue;
		$this->Keterangan->CurrentValue = NULL;
		$this->Keterangan->OldValue = $this->Keterangan->CurrentValue;
		$this->Akreditasi->CurrentValue = NULL;
		$this->Akreditasi->OldValue = $this->Akreditasi->CurrentValue;
		$this->NoSK->CurrentValue = NULL;
		$this->NoSK->OldValue = $this->NoSK->CurrentValue;
		$this->TglSK->CurrentValue = NULL;
		$this->TglSK->OldValue = $this->TglSK->CurrentValue;
		$this->Creator->CurrentValue = NULL;
		$this->Creator->OldValue = $this->Creator->CurrentValue;
		$this->CreateDate->CurrentValue = NULL;
		$this->CreateDate->OldValue = $this->CreateDate->CurrentValue;
		$this->NA->CurrentValue = "N";
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->ProdiID->FldIsDetailKey) {
			$this->ProdiID->setFormValue($objForm->GetValue("x_ProdiID"));
		}
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->Pejabat->FldIsDetailKey) {
			$this->Pejabat->setFormValue($objForm->GetValue("x_Pejabat"));
		}
		if (!$this->Keterangan->FldIsDetailKey) {
			$this->Keterangan->setFormValue($objForm->GetValue("x_Keterangan"));
		}
		if (!$this->Akreditasi->FldIsDetailKey) {
			$this->Akreditasi->setFormValue($objForm->GetValue("x_Akreditasi"));
		}
		if (!$this->NoSK->FldIsDetailKey) {
			$this->NoSK->setFormValue($objForm->GetValue("x_NoSK"));
		}
		if (!$this->TglSK->FldIsDetailKey) {
			$this->TglSK->setFormValue($objForm->GetValue("x_TglSK"));
			$this->TglSK->CurrentValue = ew_UnFormatDateTime($this->TglSK->CurrentValue, 0);
		}
		if (!$this->Creator->FldIsDetailKey) {
			$this->Creator->setFormValue($objForm->GetValue("x_Creator"));
		}
		if (!$this->CreateDate->FldIsDetailKey) {
			$this->CreateDate->setFormValue($objForm->GetValue("x_CreateDate"));
			$this->CreateDate->CurrentValue = ew_UnFormatDateTime($this->CreateDate->CurrentValue, 0);
		}
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->ProdiID->CurrentValue = $this->ProdiID->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->Pejabat->CurrentValue = $this->Pejabat->FormValue;
		$this->Keterangan->CurrentValue = $this->Keterangan->FormValue;
		$this->Akreditasi->CurrentValue = $this->Akreditasi->FormValue;
		$this->NoSK->CurrentValue = $this->NoSK->FormValue;
		$this->TglSK->CurrentValue = $this->TglSK->FormValue;
		$this->TglSK->CurrentValue = ew_UnFormatDateTime($this->TglSK->CurrentValue, 0);
		$this->Creator->CurrentValue = $this->Creator->FormValue;
		$this->CreateDate->CurrentValue = $this->CreateDate->FormValue;
		$this->CreateDate->CurrentValue = ew_UnFormatDateTime($this->CreateDate->CurrentValue, 0);
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
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->Pejabat->setDbValue($rs->fields('Pejabat'));
		$this->Keterangan->setDbValue($rs->fields('Keterangan'));
		$this->Akreditasi->setDbValue($rs->fields('Akreditasi'));
		$this->NoSK->setDbValue($rs->fields('NoSK'));
		$this->TglSK->setDbValue($rs->fields('TglSK'));
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
		$this->ProdiID->DbValue = $row['ProdiID'];
		$this->Nama->DbValue = $row['Nama'];
		$this->Pejabat->DbValue = $row['Pejabat'];
		$this->Keterangan->DbValue = $row['Keterangan'];
		$this->Akreditasi->DbValue = $row['Akreditasi'];
		$this->NoSK->DbValue = $row['NoSK'];
		$this->TglSK->DbValue = $row['TglSK'];
		$this->Creator->DbValue = $row['Creator'];
		$this->CreateDate->DbValue = $row['CreateDate'];
		$this->Editor->DbValue = $row['Editor'];
		$this->EditDate->DbValue = $row['EditDate'];
		$this->NA->DbValue = $row['NA'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("ProdiID")) <> "")
			$this->ProdiID->CurrentValue = $this->getKey("ProdiID"); // ProdiID
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
		// ProdiID
		// Nama
		// Pejabat
		// Keterangan
		// Akreditasi
		// NoSK
		// TglSK
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// ProdiID
		$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
		$this->ProdiID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// Pejabat
		if (strval($this->Pejabat->CurrentValue) <> "") {
			$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->Pejabat->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `TeacherID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
		$sWhereWrk = "";
		$this->Pejabat->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Pejabat, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Pejabat->ViewValue = $this->Pejabat->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Pejabat->ViewValue = $this->Pejabat->CurrentValue;
			}
		} else {
			$this->Pejabat->ViewValue = NULL;
		}
		$this->Pejabat->ViewCustomAttributes = "";

		// Keterangan
		$this->Keterangan->ViewValue = $this->Keterangan->CurrentValue;
		$this->Keterangan->ViewCustomAttributes = "";

		// Akreditasi
		$this->Akreditasi->ViewValue = $this->Akreditasi->CurrentValue;
		$this->Akreditasi->ViewCustomAttributes = "";

		// NoSK
		$this->NoSK->ViewValue = $this->NoSK->CurrentValue;
		$this->NoSK->ViewCustomAttributes = "";

		// TglSK
		$this->TglSK->ViewValue = $this->TglSK->CurrentValue;
		$this->TglSK->ViewValue = ew_FormatDateTime($this->TglSK->ViewValue, 0);
		$this->TglSK->ViewCustomAttributes = "";

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

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// Pejabat
			$this->Pejabat->LinkCustomAttributes = "";
			$this->Pejabat->HrefValue = "";
			$this->Pejabat->TooltipValue = "";

			// Keterangan
			$this->Keterangan->LinkCustomAttributes = "";
			$this->Keterangan->HrefValue = "";
			$this->Keterangan->TooltipValue = "";

			// Akreditasi
			$this->Akreditasi->LinkCustomAttributes = "";
			$this->Akreditasi->HrefValue = "";
			$this->Akreditasi->TooltipValue = "";

			// NoSK
			$this->NoSK->LinkCustomAttributes = "";
			$this->NoSK->HrefValue = "";
			$this->NoSK->TooltipValue = "";

			// TglSK
			$this->TglSK->LinkCustomAttributes = "";
			$this->TglSK->HrefValue = "";
			$this->TglSK->TooltipValue = "";

			// Creator
			$this->Creator->LinkCustomAttributes = "";
			$this->Creator->HrefValue = "";
			$this->Creator->TooltipValue = "";

			// CreateDate
			$this->CreateDate->LinkCustomAttributes = "";
			$this->CreateDate->HrefValue = "";
			$this->CreateDate->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// ProdiID
			$this->ProdiID->EditAttrs["class"] = "form-control";
			$this->ProdiID->EditCustomAttributes = "";
			$this->ProdiID->EditValue = ew_HtmlEncode($this->ProdiID->CurrentValue);
			$this->ProdiID->PlaceHolder = ew_RemoveHtml($this->ProdiID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// Pejabat
			$this->Pejabat->EditAttrs["class"] = "form-control";
			$this->Pejabat->EditCustomAttributes = "";
			if (trim(strval($this->Pejabat->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->Pejabat->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `TeacherID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `teacher`";
			$sWhereWrk = "";
			$this->Pejabat->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Pejabat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Pejabat->EditValue = $arwrk;

			// Keterangan
			$this->Keterangan->EditAttrs["class"] = "form-control";
			$this->Keterangan->EditCustomAttributes = "";
			$this->Keterangan->EditValue = ew_HtmlEncode($this->Keterangan->CurrentValue);
			$this->Keterangan->PlaceHolder = ew_RemoveHtml($this->Keterangan->FldCaption());

			// Akreditasi
			$this->Akreditasi->EditAttrs["class"] = "form-control";
			$this->Akreditasi->EditCustomAttributes = "";
			$this->Akreditasi->EditValue = ew_HtmlEncode($this->Akreditasi->CurrentValue);
			$this->Akreditasi->PlaceHolder = ew_RemoveHtml($this->Akreditasi->FldCaption());

			// NoSK
			$this->NoSK->EditAttrs["class"] = "form-control";
			$this->NoSK->EditCustomAttributes = "";
			$this->NoSK->EditValue = ew_HtmlEncode($this->NoSK->CurrentValue);
			$this->NoSK->PlaceHolder = ew_RemoveHtml($this->NoSK->FldCaption());

			// TglSK
			$this->TglSK->EditAttrs["class"] = "form-control";
			$this->TglSK->EditCustomAttributes = "";
			$this->TglSK->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->TglSK->CurrentValue, 8));
			$this->TglSK->PlaceHolder = ew_RemoveHtml($this->TglSK->FldCaption());

			// Creator
			// CreateDate
			// NA

			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// ProdiID

			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// Pejabat
			$this->Pejabat->LinkCustomAttributes = "";
			$this->Pejabat->HrefValue = "";

			// Keterangan
			$this->Keterangan->LinkCustomAttributes = "";
			$this->Keterangan->HrefValue = "";

			// Akreditasi
			$this->Akreditasi->LinkCustomAttributes = "";
			$this->Akreditasi->HrefValue = "";

			// NoSK
			$this->NoSK->LinkCustomAttributes = "";
			$this->NoSK->HrefValue = "";

			// TglSK
			$this->TglSK->LinkCustomAttributes = "";
			$this->TglSK->HrefValue = "";

			// Creator
			$this->Creator->LinkCustomAttributes = "";
			$this->Creator->HrefValue = "";

			// CreateDate
			$this->CreateDate->LinkCustomAttributes = "";
			$this->CreateDate->HrefValue = "";

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
		if (!$this->ProdiID->FldIsDetailKey && !is_null($this->ProdiID->FormValue) && $this->ProdiID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->ProdiID->FldCaption(), $this->ProdiID->ReqErrMsg));
		}
		if (!$this->Nama->FldIsDetailKey && !is_null($this->Nama->FormValue) && $this->Nama->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Nama->FldCaption(), $this->Nama->ReqErrMsg));
		}
		if (!$this->Pejabat->FldIsDetailKey && !is_null($this->Pejabat->FormValue) && $this->Pejabat->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Pejabat->FldCaption(), $this->Pejabat->ReqErrMsg));
		}
		if (!ew_CheckDateDef($this->TglSK->FormValue)) {
			ew_AddMessage($gsFormError, $this->TglSK->FldErrMsg());
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

		// ProdiID
		$this->ProdiID->SetDbValueDef($rsnew, $this->ProdiID->CurrentValue, "", FALSE);

		// Nama
		$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, "", FALSE);

		// Pejabat
		$this->Pejabat->SetDbValueDef($rsnew, $this->Pejabat->CurrentValue, NULL, FALSE);

		// Keterangan
		$this->Keterangan->SetDbValueDef($rsnew, $this->Keterangan->CurrentValue, NULL, FALSE);

		// Akreditasi
		$this->Akreditasi->SetDbValueDef($rsnew, $this->Akreditasi->CurrentValue, NULL, FALSE);

		// NoSK
		$this->NoSK->SetDbValueDef($rsnew, $this->NoSK->CurrentValue, NULL, FALSE);

		// TglSK
		$this->TglSK->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TglSK->CurrentValue, 0), NULL, FALSE);

		// Creator
		$this->Creator->SetDbValueDef($rsnew, CurrentUserName(), NULL);
		$rsnew['Creator'] = &$this->Creator->DbValue;

		// CreateDate
		$this->CreateDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
		$rsnew['CreateDate'] = &$this->CreateDate->DbValue;

		// NA
		$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), NULL, strval($this->NA->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['ProdiID']) == "") {
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_prodilist.php"), "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Set up multi pages
	function SetupMultiPages() {
		$pages = new cSubPages();
		$pages->Style = "tabs";
		$pages->Add(0);
		$pages->Add(1);
		$pages->Add(2);
		$pages->Add(3);
		$this->MultiPages = $pages;
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_Pejabat":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `TeacherID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
			$sWhereWrk = "";
			$this->Pejabat->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`TeacherID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Pejabat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
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
if (!isset($master_prodi_add)) $master_prodi_add = new cmaster_prodi_add();

// Page init
$master_prodi_add->Page_Init();

// Page main
$master_prodi_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_prodi_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fmaster_prodiadd = new ew_Form("fmaster_prodiadd", "add");

// Validate form
fmaster_prodiadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_ProdiID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_prodi->ProdiID->FldCaption(), $master_prodi->ProdiID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Nama");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_prodi->Nama->FldCaption(), $master_prodi->Nama->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Pejabat");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_prodi->Pejabat->FldCaption(), $master_prodi->Pejabat->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TglSK");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($master_prodi->TglSK->FldErrMsg()) ?>");

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
fmaster_prodiadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_prodiadd.ValidateRequired = true;
<?php } else { ?>
fmaster_prodiadd.ValidateRequired = false; 
<?php } ?>

// Multi-Page
fmaster_prodiadd.MultiPage = new ew_MultiPage("fmaster_prodiadd");

// Dynamic selection lists
fmaster_prodiadd.Lists["x_Pejabat"] = {"LinkField":"x_TeacherID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"teacher"};
fmaster_prodiadd.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_prodiadd.Lists["x_NA"].Options = <?php echo json_encode($master_prodi->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$master_prodi_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_prodi_add->ShowPageHeader(); ?>
<?php
$master_prodi_add->ShowMessage();
?>
<form name="fmaster_prodiadd" id="fmaster_prodiadd" class="<?php echo $master_prodi_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_prodi_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_prodi_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_prodi">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($master_prodi_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_prodi_add->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<div class="ewMultiPage">
<div class="tabbable" id="master_prodi_add">
	<ul class="nav<?php echo $master_prodi_add->MultiPages->NavStyle() ?>">
		<li<?php echo $master_prodi_add->MultiPages->TabStyle("1") ?>><a href="#tab_master_prodi1" data-toggle="tab"><?php echo $master_prodi->PageCaption(1) ?></a></li>
		<li<?php echo $master_prodi_add->MultiPages->TabStyle("2") ?>><a href="#tab_master_prodi2" data-toggle="tab"><?php echo $master_prodi->PageCaption(2) ?></a></li>
		<li<?php echo $master_prodi_add->MultiPages->TabStyle("3") ?>><a href="#tab_master_prodi3" data-toggle="tab"><?php echo $master_prodi->PageCaption(3) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane<?php echo $master_prodi_add->MultiPages->PageStyle("1") ?>" id="tab_master_prodi1">
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_prodiadd1" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_prodi->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label id="elh_master_prodi_ProdiID" for="x_ProdiID" class="col-sm-2 control-label ewLabel"><?php echo $master_prodi->ProdiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_prodi->ProdiID->CellAttributes() ?>>
<span id="el_master_prodi_ProdiID">
<input type="text" data-table="master_prodi" data-field="x_ProdiID" data-page="1" name="x_ProdiID" id="x_ProdiID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_prodi->ProdiID->getPlaceHolder()) ?>" value="<?php echo $master_prodi->ProdiID->EditValue ?>"<?php echo $master_prodi->ProdiID->EditAttributes() ?>>
</span>
<?php echo $master_prodi->ProdiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_master_prodi_ProdiID"><?php echo $master_prodi->ProdiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_prodi->ProdiID->CellAttributes() ?>>
<span id="el_master_prodi_ProdiID">
<input type="text" data-table="master_prodi" data-field="x_ProdiID" data-page="1" name="x_ProdiID" id="x_ProdiID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_prodi->ProdiID->getPlaceHolder()) ?>" value="<?php echo $master_prodi->ProdiID->EditValue ?>"<?php echo $master_prodi->ProdiID->EditAttributes() ?>>
</span>
<?php echo $master_prodi->ProdiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label id="elh_master_prodi_Nama" for="x_Nama" class="col-sm-2 control-label ewLabel"><?php echo $master_prodi->Nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_prodi->Nama->CellAttributes() ?>>
<span id="el_master_prodi_Nama">
<input type="text" data-table="master_prodi" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($master_prodi->Nama->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Nama->EditValue ?>"<?php echo $master_prodi->Nama->EditAttributes() ?>>
</span>
<?php echo $master_prodi->Nama->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_master_prodi_Nama"><?php echo $master_prodi->Nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_prodi->Nama->CellAttributes() ?>>
<span id="el_master_prodi_Nama">
<input type="text" data-table="master_prodi" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($master_prodi->Nama->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Nama->EditValue ?>"<?php echo $master_prodi->Nama->EditAttributes() ?>>
</span>
<?php echo $master_prodi->Nama->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->Pejabat->Visible) { // Pejabat ?>
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
	<div id="r_Pejabat" class="form-group">
		<label id="elh_master_prodi_Pejabat" for="x_Pejabat" class="col-sm-2 control-label ewLabel"><?php echo $master_prodi->Pejabat->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_prodi->Pejabat->CellAttributes() ?>>
<span id="el_master_prodi_Pejabat">
<select data-table="master_prodi" data-field="x_Pejabat" data-page="1" data-value-separator="<?php echo $master_prodi->Pejabat->DisplayValueSeparatorAttribute() ?>" id="x_Pejabat" name="x_Pejabat"<?php echo $master_prodi->Pejabat->EditAttributes() ?>>
<?php echo $master_prodi->Pejabat->SelectOptionListHtml("x_Pejabat") ?>
</select>
<input type="hidden" name="s_x_Pejabat" id="s_x_Pejabat" value="<?php echo $master_prodi->Pejabat->LookupFilterQuery() ?>">
</span>
<?php echo $master_prodi->Pejabat->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Pejabat">
		<td><span id="elh_master_prodi_Pejabat"><?php echo $master_prodi->Pejabat->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_prodi->Pejabat->CellAttributes() ?>>
<span id="el_master_prodi_Pejabat">
<select data-table="master_prodi" data-field="x_Pejabat" data-page="1" data-value-separator="<?php echo $master_prodi->Pejabat->DisplayValueSeparatorAttribute() ?>" id="x_Pejabat" name="x_Pejabat"<?php echo $master_prodi->Pejabat->EditAttributes() ?>>
<?php echo $master_prodi->Pejabat->SelectOptionListHtml("x_Pejabat") ?>
</select>
<input type="hidden" name="s_x_Pejabat" id="s_x_Pejabat" value="<?php echo $master_prodi->Pejabat->LookupFilterQuery() ?>">
</span>
<?php echo $master_prodi->Pejabat->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->Keterangan->Visible) { // Keterangan ?>
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
	<div id="r_Keterangan" class="form-group">
		<label id="elh_master_prodi_Keterangan" for="x_Keterangan" class="col-sm-2 control-label ewLabel"><?php echo $master_prodi->Keterangan->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_prodi->Keterangan->CellAttributes() ?>>
<span id="el_master_prodi_Keterangan">
<textarea data-table="master_prodi" data-field="x_Keterangan" data-page="1" name="x_Keterangan" id="x_Keterangan" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($master_prodi->Keterangan->getPlaceHolder()) ?>"<?php echo $master_prodi->Keterangan->EditAttributes() ?>><?php echo $master_prodi->Keterangan->EditValue ?></textarea>
</span>
<?php echo $master_prodi->Keterangan->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Keterangan">
		<td><span id="elh_master_prodi_Keterangan"><?php echo $master_prodi->Keterangan->FldCaption() ?></span></td>
		<td<?php echo $master_prodi->Keterangan->CellAttributes() ?>>
<span id="el_master_prodi_Keterangan">
<textarea data-table="master_prodi" data-field="x_Keterangan" data-page="1" name="x_Keterangan" id="x_Keterangan" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($master_prodi->Keterangan->getPlaceHolder()) ?>"<?php echo $master_prodi->Keterangan->EditAttributes() ?>><?php echo $master_prodi->Keterangan->EditValue ?></textarea>
</span>
<?php echo $master_prodi->Keterangan->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $master_prodi_add->MultiPages->PageStyle("2") ?>" id="tab_master_prodi2">
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_prodiadd2" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_prodi->Akreditasi->Visible) { // Akreditasi ?>
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
	<div id="r_Akreditasi" class="form-group">
		<label id="elh_master_prodi_Akreditasi" for="x_Akreditasi" class="col-sm-2 control-label ewLabel"><?php echo $master_prodi->Akreditasi->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_prodi->Akreditasi->CellAttributes() ?>>
<span id="el_master_prodi_Akreditasi">
<input type="text" data-table="master_prodi" data-field="x_Akreditasi" data-page="2" name="x_Akreditasi" id="x_Akreditasi" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($master_prodi->Akreditasi->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Akreditasi->EditValue ?>"<?php echo $master_prodi->Akreditasi->EditAttributes() ?>>
</span>
<?php echo $master_prodi->Akreditasi->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Akreditasi">
		<td><span id="elh_master_prodi_Akreditasi"><?php echo $master_prodi->Akreditasi->FldCaption() ?></span></td>
		<td<?php echo $master_prodi->Akreditasi->CellAttributes() ?>>
<span id="el_master_prodi_Akreditasi">
<input type="text" data-table="master_prodi" data-field="x_Akreditasi" data-page="2" name="x_Akreditasi" id="x_Akreditasi" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($master_prodi->Akreditasi->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Akreditasi->EditValue ?>"<?php echo $master_prodi->Akreditasi->EditAttributes() ?>>
</span>
<?php echo $master_prodi->Akreditasi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->NoSK->Visible) { // NoSK ?>
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
	<div id="r_NoSK" class="form-group">
		<label id="elh_master_prodi_NoSK" for="x_NoSK" class="col-sm-2 control-label ewLabel"><?php echo $master_prodi->NoSK->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_prodi->NoSK->CellAttributes() ?>>
<span id="el_master_prodi_NoSK">
<input type="text" data-table="master_prodi" data-field="x_NoSK" data-page="2" name="x_NoSK" id="x_NoSK" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_prodi->NoSK->getPlaceHolder()) ?>" value="<?php echo $master_prodi->NoSK->EditValue ?>"<?php echo $master_prodi->NoSK->EditAttributes() ?>>
</span>
<?php echo $master_prodi->NoSK->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NoSK">
		<td><span id="elh_master_prodi_NoSK"><?php echo $master_prodi->NoSK->FldCaption() ?></span></td>
		<td<?php echo $master_prodi->NoSK->CellAttributes() ?>>
<span id="el_master_prodi_NoSK">
<input type="text" data-table="master_prodi" data-field="x_NoSK" data-page="2" name="x_NoSK" id="x_NoSK" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_prodi->NoSK->getPlaceHolder()) ?>" value="<?php echo $master_prodi->NoSK->EditValue ?>"<?php echo $master_prodi->NoSK->EditAttributes() ?>>
</span>
<?php echo $master_prodi->NoSK->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->TglSK->Visible) { // TglSK ?>
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
	<div id="r_TglSK" class="form-group">
		<label id="elh_master_prodi_TglSK" for="x_TglSK" class="col-sm-2 control-label ewLabel"><?php echo $master_prodi->TglSK->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_prodi->TglSK->CellAttributes() ?>>
<span id="el_master_prodi_TglSK">
<input type="text" data-table="master_prodi" data-field="x_TglSK" data-page="2" name="x_TglSK" id="x_TglSK" placeholder="<?php echo ew_HtmlEncode($master_prodi->TglSK->getPlaceHolder()) ?>" value="<?php echo $master_prodi->TglSK->EditValue ?>"<?php echo $master_prodi->TglSK->EditAttributes() ?>>
</span>
<?php echo $master_prodi->TglSK->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TglSK">
		<td><span id="elh_master_prodi_TglSK"><?php echo $master_prodi->TglSK->FldCaption() ?></span></td>
		<td<?php echo $master_prodi->TglSK->CellAttributes() ?>>
<span id="el_master_prodi_TglSK">
<input type="text" data-table="master_prodi" data-field="x_TglSK" data-page="2" name="x_TglSK" id="x_TglSK" placeholder="<?php echo ew_HtmlEncode($master_prodi->TglSK->getPlaceHolder()) ?>" value="<?php echo $master_prodi->TglSK->EditValue ?>"<?php echo $master_prodi->TglSK->EditAttributes() ?>>
</span>
<?php echo $master_prodi->TglSK->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $master_prodi_add->MultiPages->PageStyle("3") ?>" id="tab_master_prodi3">
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_prodiadd3" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_prodi->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_master_prodi_NA" class="col-sm-2 control-label ewLabel"><?php echo $master_prodi->NA->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_prodi->NA->CellAttributes() ?>>
<span id="el_master_prodi_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_prodi" data-field="x_NA" data-page="3" data-value-separator="<?php echo $master_prodi->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_prodi->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_prodi->NA->RadioButtonListHtml(FALSE, "x_NA", 3) ?>
</div></div>
</span>
<?php echo $master_prodi->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_master_prodi_NA"><?php echo $master_prodi->NA->FldCaption() ?></span></td>
		<td<?php echo $master_prodi->NA->CellAttributes() ?>>
<span id="el_master_prodi_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_prodi" data-field="x_NA" data-page="3" data-value-separator="<?php echo $master_prodi->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_prodi->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_prodi->NA->RadioButtonListHtml(FALSE, "x_NA", 3) ?>
</div></div>
</span>
<?php echo $master_prodi->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_prodi_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
	</div>
</div>
</div>
<?php if (!$master_prodi_add->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $master_prodi_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_prodiadd.Init();
</script>
<?php
$master_prodi_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_prodi_add->Page_Terminate();
?>
