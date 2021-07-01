<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_wilayah_negarainfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_wilayah_negara_add = NULL; // Initialize page object first

class cmaster_wilayah_negara_add extends cmaster_wilayah_negara {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_wilayah_negara';

	// Page object name
	var $PageObjName = 'master_wilayah_negara_add';

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

		// Table object (master_wilayah_negara)
		if (!isset($GLOBALS["master_wilayah_negara"]) || get_class($GLOBALS["master_wilayah_negara"]) == "cmaster_wilayah_negara") {
			$GLOBALS["master_wilayah_negara"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_wilayah_negara"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_wilayah_negara', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("master_wilayah_negaralist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->BenuaID->SetVisibility();
		$this->NamaBenua->SetVisibility();
		$this->KodeNegara->SetVisibility();
		$this->NamaNegara->SetVisibility();
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
		global $EW_EXPORT, $master_wilayah_negara;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_wilayah_negara);
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
			if (@$_GET["NegaraID"] != "") {
				$this->NegaraID->setQueryStringValue($_GET["NegaraID"]);
				$this->setKey("NegaraID", $this->NegaraID->CurrentValue); // Set up key
			} else {
				$this->setKey("NegaraID", ""); // Clear key
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
					$this->Page_Terminate("master_wilayah_negaralist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "master_wilayah_negaralist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "master_wilayah_negaraview.php")
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
		$this->BenuaID->CurrentValue = NULL;
		$this->BenuaID->OldValue = $this->BenuaID->CurrentValue;
		$this->NamaBenua->CurrentValue = NULL;
		$this->NamaBenua->OldValue = $this->NamaBenua->CurrentValue;
		$this->KodeNegara->CurrentValue = NULL;
		$this->KodeNegara->OldValue = $this->KodeNegara->CurrentValue;
		$this->NamaNegara->CurrentValue = NULL;
		$this->NamaNegara->OldValue = $this->NamaNegara->CurrentValue;
		$this->NA->CurrentValue = "N";
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->BenuaID->FldIsDetailKey) {
			$this->BenuaID->setFormValue($objForm->GetValue("x_BenuaID"));
		}
		if (!$this->NamaBenua->FldIsDetailKey) {
			$this->NamaBenua->setFormValue($objForm->GetValue("x_NamaBenua"));
		}
		if (!$this->KodeNegara->FldIsDetailKey) {
			$this->KodeNegara->setFormValue($objForm->GetValue("x_KodeNegara"));
		}
		if (!$this->NamaNegara->FldIsDetailKey) {
			$this->NamaNegara->setFormValue($objForm->GetValue("x_NamaNegara"));
		}
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
		if (!$this->NegaraID->FldIsDetailKey)
			$this->NegaraID->setFormValue($objForm->GetValue("x_NegaraID"));
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->NegaraID->CurrentValue = $this->NegaraID->FormValue;
		$this->BenuaID->CurrentValue = $this->BenuaID->FormValue;
		$this->NamaBenua->CurrentValue = $this->NamaBenua->FormValue;
		$this->KodeNegara->CurrentValue = $this->KodeNegara->FormValue;
		$this->NamaNegara->CurrentValue = $this->NamaNegara->FormValue;
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
		$this->NegaraID->setDbValue($rs->fields('NegaraID'));
		$this->BenuaID->setDbValue($rs->fields('BenuaID'));
		$this->NamaBenua->setDbValue($rs->fields('NamaBenua'));
		$this->KodeNegara->setDbValue($rs->fields('KodeNegara'));
		$this->NamaNegara->setDbValue($rs->fields('NamaNegara'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->NegaraID->DbValue = $row['NegaraID'];
		$this->BenuaID->DbValue = $row['BenuaID'];
		$this->NamaBenua->DbValue = $row['NamaBenua'];
		$this->KodeNegara->DbValue = $row['KodeNegara'];
		$this->NamaNegara->DbValue = $row['NamaNegara'];
		$this->NA->DbValue = $row['NA'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("NegaraID")) <> "")
			$this->NegaraID->CurrentValue = $this->getKey("NegaraID"); // NegaraID
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
		// NegaraID
		// BenuaID
		// NamaBenua
		// KodeNegara
		// NamaNegara
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// NegaraID
		$this->NegaraID->ViewValue = $this->NegaraID->CurrentValue;
		$this->NegaraID->ViewCustomAttributes = "";

		// BenuaID
		$this->BenuaID->ViewValue = $this->BenuaID->CurrentValue;
		$this->BenuaID->ViewCustomAttributes = "";

		// NamaBenua
		$this->NamaBenua->ViewValue = $this->NamaBenua->CurrentValue;
		$this->NamaBenua->ViewCustomAttributes = "";

		// KodeNegara
		$this->KodeNegara->ViewValue = $this->KodeNegara->CurrentValue;
		$this->KodeNegara->ViewCustomAttributes = "";

		// NamaNegara
		$this->NamaNegara->ViewValue = $this->NamaNegara->CurrentValue;
		$this->NamaNegara->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

			// BenuaID
			$this->BenuaID->LinkCustomAttributes = "";
			$this->BenuaID->HrefValue = "";
			$this->BenuaID->TooltipValue = "";

			// NamaBenua
			$this->NamaBenua->LinkCustomAttributes = "";
			$this->NamaBenua->HrefValue = "";
			$this->NamaBenua->TooltipValue = "";

			// KodeNegara
			$this->KodeNegara->LinkCustomAttributes = "";
			$this->KodeNegara->HrefValue = "";
			$this->KodeNegara->TooltipValue = "";

			// NamaNegara
			$this->NamaNegara->LinkCustomAttributes = "";
			$this->NamaNegara->HrefValue = "";
			$this->NamaNegara->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// BenuaID
			$this->BenuaID->EditAttrs["class"] = "form-control";
			$this->BenuaID->EditCustomAttributes = "";
			$this->BenuaID->EditValue = ew_HtmlEncode($this->BenuaID->CurrentValue);
			$this->BenuaID->PlaceHolder = ew_RemoveHtml($this->BenuaID->FldCaption());

			// NamaBenua
			$this->NamaBenua->EditAttrs["class"] = "form-control";
			$this->NamaBenua->EditCustomAttributes = "";
			$this->NamaBenua->EditValue = ew_HtmlEncode($this->NamaBenua->CurrentValue);
			$this->NamaBenua->PlaceHolder = ew_RemoveHtml($this->NamaBenua->FldCaption());

			// KodeNegara
			$this->KodeNegara->EditAttrs["class"] = "form-control";
			$this->KodeNegara->EditCustomAttributes = "";
			$this->KodeNegara->EditValue = ew_HtmlEncode($this->KodeNegara->CurrentValue);
			$this->KodeNegara->PlaceHolder = ew_RemoveHtml($this->KodeNegara->FldCaption());

			// NamaNegara
			$this->NamaNegara->EditAttrs["class"] = "form-control";
			$this->NamaNegara->EditCustomAttributes = "";
			$this->NamaNegara->EditValue = ew_HtmlEncode($this->NamaNegara->CurrentValue);
			$this->NamaNegara->PlaceHolder = ew_RemoveHtml($this->NamaNegara->FldCaption());

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// BenuaID

			$this->BenuaID->LinkCustomAttributes = "";
			$this->BenuaID->HrefValue = "";

			// NamaBenua
			$this->NamaBenua->LinkCustomAttributes = "";
			$this->NamaBenua->HrefValue = "";

			// KodeNegara
			$this->KodeNegara->LinkCustomAttributes = "";
			$this->KodeNegara->HrefValue = "";

			// NamaNegara
			$this->NamaNegara->LinkCustomAttributes = "";
			$this->NamaNegara->HrefValue = "";

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
		if (!$this->BenuaID->FldIsDetailKey && !is_null($this->BenuaID->FormValue) && $this->BenuaID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->BenuaID->FldCaption(), $this->BenuaID->ReqErrMsg));
		}
		if (!$this->NamaBenua->FldIsDetailKey && !is_null($this->NamaBenua->FormValue) && $this->NamaBenua->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->NamaBenua->FldCaption(), $this->NamaBenua->ReqErrMsg));
		}
		if (!$this->KodeNegara->FldIsDetailKey && !is_null($this->KodeNegara->FormValue) && $this->KodeNegara->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->KodeNegara->FldCaption(), $this->KodeNegara->ReqErrMsg));
		}
		if (!$this->NamaNegara->FldIsDetailKey && !is_null($this->NamaNegara->FormValue) && $this->NamaNegara->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->NamaNegara->FldCaption(), $this->NamaNegara->ReqErrMsg));
		}
		if ($this->NA->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->NA->FldCaption(), $this->NA->ReqErrMsg));
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

		// BenuaID
		$this->BenuaID->SetDbValueDef($rsnew, $this->BenuaID->CurrentValue, "", FALSE);

		// NamaBenua
		$this->NamaBenua->SetDbValueDef($rsnew, $this->NamaBenua->CurrentValue, "", FALSE);

		// KodeNegara
		$this->KodeNegara->SetDbValueDef($rsnew, $this->KodeNegara->CurrentValue, "", FALSE);

		// NamaNegara
		$this->NamaNegara->SetDbValueDef($rsnew, $this->NamaNegara->CurrentValue, "", FALSE);

		// NA
		$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), "N", strval($this->NA->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['NegaraID']) == "") {
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_wilayah_negaralist.php"), "", $this->TableVar, TRUE);
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
if (!isset($master_wilayah_negara_add)) $master_wilayah_negara_add = new cmaster_wilayah_negara_add();

// Page init
$master_wilayah_negara_add->Page_Init();

// Page main
$master_wilayah_negara_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_wilayah_negara_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fmaster_wilayah_negaraadd = new ew_Form("fmaster_wilayah_negaraadd", "add");

// Validate form
fmaster_wilayah_negaraadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_BenuaID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_wilayah_negara->BenuaID->FldCaption(), $master_wilayah_negara->BenuaID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_NamaBenua");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_wilayah_negara->NamaBenua->FldCaption(), $master_wilayah_negara->NamaBenua->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_KodeNegara");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_wilayah_negara->KodeNegara->FldCaption(), $master_wilayah_negara->KodeNegara->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_NamaNegara");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_wilayah_negara->NamaNegara->FldCaption(), $master_wilayah_negara->NamaNegara->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_NA");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_wilayah_negara->NA->FldCaption(), $master_wilayah_negara->NA->ReqErrMsg)) ?>");

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
fmaster_wilayah_negaraadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_wilayah_negaraadd.ValidateRequired = true;
<?php } else { ?>
fmaster_wilayah_negaraadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_wilayah_negaraadd.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_wilayah_negaraadd.Lists["x_NA"].Options = <?php echo json_encode($master_wilayah_negara->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$master_wilayah_negara_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_wilayah_negara_add->ShowPageHeader(); ?>
<?php
$master_wilayah_negara_add->ShowMessage();
?>
<form name="fmaster_wilayah_negaraadd" id="fmaster_wilayah_negaraadd" class="<?php echo $master_wilayah_negara_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_wilayah_negara_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_wilayah_negara_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_wilayah_negara">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($master_wilayah_negara_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_wilayah_negara_add->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_wilayah_negaraadd" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_wilayah_negara->BenuaID->Visible) { // BenuaID ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_add->IsModal) { ?>
	<div id="r_BenuaID" class="form-group">
		<label id="elh_master_wilayah_negara_BenuaID" for="x_BenuaID" class="col-sm-2 control-label ewLabel"><?php echo $master_wilayah_negara->BenuaID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_wilayah_negara->BenuaID->CellAttributes() ?>>
<span id="el_master_wilayah_negara_BenuaID">
<input type="text" data-table="master_wilayah_negara" data-field="x_BenuaID" name="x_BenuaID" id="x_BenuaID" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->BenuaID->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->BenuaID->EditValue ?>"<?php echo $master_wilayah_negara->BenuaID->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_negara->BenuaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_BenuaID">
		<td><span id="elh_master_wilayah_negara_BenuaID"><?php echo $master_wilayah_negara->BenuaID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_wilayah_negara->BenuaID->CellAttributes() ?>>
<span id="el_master_wilayah_negara_BenuaID">
<input type="text" data-table="master_wilayah_negara" data-field="x_BenuaID" name="x_BenuaID" id="x_BenuaID" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->BenuaID->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->BenuaID->EditValue ?>"<?php echo $master_wilayah_negara->BenuaID->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_negara->BenuaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_negara->NamaBenua->Visible) { // NamaBenua ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_add->IsModal) { ?>
	<div id="r_NamaBenua" class="form-group">
		<label id="elh_master_wilayah_negara_NamaBenua" for="x_NamaBenua" class="col-sm-2 control-label ewLabel"><?php echo $master_wilayah_negara->NamaBenua->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_wilayah_negara->NamaBenua->CellAttributes() ?>>
<span id="el_master_wilayah_negara_NamaBenua">
<input type="text" data-table="master_wilayah_negara" data-field="x_NamaBenua" name="x_NamaBenua" id="x_NamaBenua" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->NamaBenua->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->NamaBenua->EditValue ?>"<?php echo $master_wilayah_negara->NamaBenua->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_negara->NamaBenua->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NamaBenua">
		<td><span id="elh_master_wilayah_negara_NamaBenua"><?php echo $master_wilayah_negara->NamaBenua->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_wilayah_negara->NamaBenua->CellAttributes() ?>>
<span id="el_master_wilayah_negara_NamaBenua">
<input type="text" data-table="master_wilayah_negara" data-field="x_NamaBenua" name="x_NamaBenua" id="x_NamaBenua" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->NamaBenua->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->NamaBenua->EditValue ?>"<?php echo $master_wilayah_negara->NamaBenua->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_negara->NamaBenua->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_negara->KodeNegara->Visible) { // KodeNegara ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_add->IsModal) { ?>
	<div id="r_KodeNegara" class="form-group">
		<label id="elh_master_wilayah_negara_KodeNegara" for="x_KodeNegara" class="col-sm-2 control-label ewLabel"><?php echo $master_wilayah_negara->KodeNegara->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_wilayah_negara->KodeNegara->CellAttributes() ?>>
<span id="el_master_wilayah_negara_KodeNegara">
<input type="text" data-table="master_wilayah_negara" data-field="x_KodeNegara" name="x_KodeNegara" id="x_KodeNegara" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->KodeNegara->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->KodeNegara->EditValue ?>"<?php echo $master_wilayah_negara->KodeNegara->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_negara->KodeNegara->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodeNegara">
		<td><span id="elh_master_wilayah_negara_KodeNegara"><?php echo $master_wilayah_negara->KodeNegara->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_wilayah_negara->KodeNegara->CellAttributes() ?>>
<span id="el_master_wilayah_negara_KodeNegara">
<input type="text" data-table="master_wilayah_negara" data-field="x_KodeNegara" name="x_KodeNegara" id="x_KodeNegara" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->KodeNegara->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->KodeNegara->EditValue ?>"<?php echo $master_wilayah_negara->KodeNegara->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_negara->KodeNegara->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_negara->NamaNegara->Visible) { // NamaNegara ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_add->IsModal) { ?>
	<div id="r_NamaNegara" class="form-group">
		<label id="elh_master_wilayah_negara_NamaNegara" for="x_NamaNegara" class="col-sm-2 control-label ewLabel"><?php echo $master_wilayah_negara->NamaNegara->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_wilayah_negara->NamaNegara->CellAttributes() ?>>
<span id="el_master_wilayah_negara_NamaNegara">
<input type="text" data-table="master_wilayah_negara" data-field="x_NamaNegara" name="x_NamaNegara" id="x_NamaNegara" size="30" maxlength="70" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->NamaNegara->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->NamaNegara->EditValue ?>"<?php echo $master_wilayah_negara->NamaNegara->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_negara->NamaNegara->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NamaNegara">
		<td><span id="elh_master_wilayah_negara_NamaNegara"><?php echo $master_wilayah_negara->NamaNegara->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_wilayah_negara->NamaNegara->CellAttributes() ?>>
<span id="el_master_wilayah_negara_NamaNegara">
<input type="text" data-table="master_wilayah_negara" data-field="x_NamaNegara" name="x_NamaNegara" id="x_NamaNegara" size="30" maxlength="70" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->NamaNegara->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->NamaNegara->EditValue ?>"<?php echo $master_wilayah_negara->NamaNegara->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_negara->NamaNegara->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_negara->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_add->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_master_wilayah_negara_NA" class="col-sm-2 control-label ewLabel"><?php echo $master_wilayah_negara->NA->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_wilayah_negara->NA->CellAttributes() ?>>
<span id="el_master_wilayah_negara_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_wilayah_negara" data-field="x_NA" data-value-separator="<?php echo $master_wilayah_negara->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_wilayah_negara->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_wilayah_negara->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $master_wilayah_negara->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_master_wilayah_negara_NA"><?php echo $master_wilayah_negara->NA->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_wilayah_negara->NA->CellAttributes() ?>>
<span id="el_master_wilayah_negara_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_wilayah_negara" data-field="x_NA" data-value-separator="<?php echo $master_wilayah_negara->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_wilayah_negara->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_wilayah_negara->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $master_wilayah_negara->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$master_wilayah_negara_add->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $master_wilayah_negara_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_wilayah_negaraadd.Init();
</script>
<?php
$master_wilayah_negara_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_wilayah_negara_add->Page_Terminate();
?>
