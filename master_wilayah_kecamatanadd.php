<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_wilayah_kecamataninfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_wilayah_kecamatan_add = NULL; // Initialize page object first

class cmaster_wilayah_kecamatan_add extends cmaster_wilayah_kecamatan {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_wilayah_kecamatan';

	// Page object name
	var $PageObjName = 'master_wilayah_kecamatan_add';

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

		// Table object (master_wilayah_kecamatan)
		if (!isset($GLOBALS["master_wilayah_kecamatan"]) || get_class($GLOBALS["master_wilayah_kecamatan"]) == "cmaster_wilayah_kecamatan") {
			$GLOBALS["master_wilayah_kecamatan"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_wilayah_kecamatan"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_wilayah_kecamatan', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("master_wilayah_kecamatanlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->KecamatanID->SetVisibility();
		$this->KabupatenKotaID->SetVisibility();
		$this->Kecamatan->SetVisibility();

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
		global $EW_EXPORT, $master_wilayah_kecamatan;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_wilayah_kecamatan);
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
			if (@$_GET["KecamatanID"] != "") {
				$this->KecamatanID->setQueryStringValue($_GET["KecamatanID"]);
				$this->setKey("KecamatanID", $this->KecamatanID->CurrentValue); // Set up key
			} else {
				$this->setKey("KecamatanID", ""); // Clear key
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
					$this->Page_Terminate("master_wilayah_kecamatanlist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "master_wilayah_kecamatanlist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "master_wilayah_kecamatanview.php")
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
		$this->KecamatanID->CurrentValue = NULL;
		$this->KecamatanID->OldValue = $this->KecamatanID->CurrentValue;
		$this->KabupatenKotaID->CurrentValue = NULL;
		$this->KabupatenKotaID->OldValue = $this->KabupatenKotaID->CurrentValue;
		$this->Kecamatan->CurrentValue = NULL;
		$this->Kecamatan->OldValue = $this->Kecamatan->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->KecamatanID->FldIsDetailKey) {
			$this->KecamatanID->setFormValue($objForm->GetValue("x_KecamatanID"));
		}
		if (!$this->KabupatenKotaID->FldIsDetailKey) {
			$this->KabupatenKotaID->setFormValue($objForm->GetValue("x_KabupatenKotaID"));
		}
		if (!$this->Kecamatan->FldIsDetailKey) {
			$this->Kecamatan->setFormValue($objForm->GetValue("x_Kecamatan"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->KecamatanID->CurrentValue = $this->KecamatanID->FormValue;
		$this->KabupatenKotaID->CurrentValue = $this->KabupatenKotaID->FormValue;
		$this->Kecamatan->CurrentValue = $this->Kecamatan->FormValue;
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
		$this->KecamatanID->setDbValue($rs->fields('KecamatanID'));
		$this->KabupatenKotaID->setDbValue($rs->fields('KabupatenKotaID'));
		$this->Kecamatan->setDbValue($rs->fields('Kecamatan'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->KecamatanID->DbValue = $row['KecamatanID'];
		$this->KabupatenKotaID->DbValue = $row['KabupatenKotaID'];
		$this->Kecamatan->DbValue = $row['Kecamatan'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("KecamatanID")) <> "")
			$this->KecamatanID->CurrentValue = $this->getKey("KecamatanID"); // KecamatanID
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
		// KecamatanID
		// KabupatenKotaID
		// Kecamatan

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// KecamatanID
		$this->KecamatanID->ViewValue = $this->KecamatanID->CurrentValue;
		$this->KecamatanID->ViewCustomAttributes = "";

		// KabupatenKotaID
		if (strval($this->KabupatenKotaID->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenKotaID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->KabupatenKotaID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KabupatenKotaID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KabupatenKotaID->ViewValue = $this->KabupatenKotaID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KabupatenKotaID->ViewValue = $this->KabupatenKotaID->CurrentValue;
			}
		} else {
			$this->KabupatenKotaID->ViewValue = NULL;
		}
		$this->KabupatenKotaID->ViewCustomAttributes = "";

		// Kecamatan
		$this->Kecamatan->ViewValue = $this->Kecamatan->CurrentValue;
		$this->Kecamatan->ViewCustomAttributes = "";

			// KecamatanID
			$this->KecamatanID->LinkCustomAttributes = "";
			$this->KecamatanID->HrefValue = "";
			$this->KecamatanID->TooltipValue = "";

			// KabupatenKotaID
			$this->KabupatenKotaID->LinkCustomAttributes = "";
			$this->KabupatenKotaID->HrefValue = "";
			$this->KabupatenKotaID->TooltipValue = "";

			// Kecamatan
			$this->Kecamatan->LinkCustomAttributes = "";
			$this->Kecamatan->HrefValue = "";
			$this->Kecamatan->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// KecamatanID
			$this->KecamatanID->EditAttrs["class"] = "form-control";
			$this->KecamatanID->EditCustomAttributes = "";
			$this->KecamatanID->EditValue = ew_HtmlEncode($this->KecamatanID->CurrentValue);
			$this->KecamatanID->PlaceHolder = ew_RemoveHtml($this->KecamatanID->FldCaption());

			// KabupatenKotaID
			$this->KabupatenKotaID->EditAttrs["class"] = "form-control";
			$this->KabupatenKotaID->EditCustomAttributes = "";
			if (trim(strval($this->KabupatenKotaID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenKotaID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "";
			$this->KabupatenKotaID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KabupatenKotaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KabupatenKotaID->EditValue = $arwrk;

			// Kecamatan
			$this->Kecamatan->EditAttrs["class"] = "form-control";
			$this->Kecamatan->EditCustomAttributes = "";
			$this->Kecamatan->EditValue = ew_HtmlEncode($this->Kecamatan->CurrentValue);
			$this->Kecamatan->PlaceHolder = ew_RemoveHtml($this->Kecamatan->FldCaption());

			// Add refer script
			// KecamatanID

			$this->KecamatanID->LinkCustomAttributes = "";
			$this->KecamatanID->HrefValue = "";

			// KabupatenKotaID
			$this->KabupatenKotaID->LinkCustomAttributes = "";
			$this->KabupatenKotaID->HrefValue = "";

			// Kecamatan
			$this->Kecamatan->LinkCustomAttributes = "";
			$this->Kecamatan->HrefValue = "";
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
		if (!$this->KecamatanID->FldIsDetailKey && !is_null($this->KecamatanID->FormValue) && $this->KecamatanID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->KecamatanID->FldCaption(), $this->KecamatanID->ReqErrMsg));
		}
		if (!$this->KabupatenKotaID->FldIsDetailKey && !is_null($this->KabupatenKotaID->FormValue) && $this->KabupatenKotaID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->KabupatenKotaID->FldCaption(), $this->KabupatenKotaID->ReqErrMsg));
		}
		if (!$this->Kecamatan->FldIsDetailKey && !is_null($this->Kecamatan->FormValue) && $this->Kecamatan->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Kecamatan->FldCaption(), $this->Kecamatan->ReqErrMsg));
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

		// KecamatanID
		$this->KecamatanID->SetDbValueDef($rsnew, $this->KecamatanID->CurrentValue, "", FALSE);

		// KabupatenKotaID
		$this->KabupatenKotaID->SetDbValueDef($rsnew, $this->KabupatenKotaID->CurrentValue, "", FALSE);

		// Kecamatan
		$this->Kecamatan->SetDbValueDef($rsnew, $this->Kecamatan->CurrentValue, "", FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['KecamatanID']) == "") {
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_wilayah_kecamatanlist.php"), "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_KabupatenKotaID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KabupatenKotaID` AS `LinkFld`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "";
			$this->KabupatenKotaID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KabupatenKotaID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KabupatenKotaID, $sWhereWrk); // Call Lookup selecting
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
if (!isset($master_wilayah_kecamatan_add)) $master_wilayah_kecamatan_add = new cmaster_wilayah_kecamatan_add();

// Page init
$master_wilayah_kecamatan_add->Page_Init();

// Page main
$master_wilayah_kecamatan_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_wilayah_kecamatan_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fmaster_wilayah_kecamatanadd = new ew_Form("fmaster_wilayah_kecamatanadd", "add");

// Validate form
fmaster_wilayah_kecamatanadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_KecamatanID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_wilayah_kecamatan->KecamatanID->FldCaption(), $master_wilayah_kecamatan->KecamatanID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_KabupatenKotaID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_wilayah_kecamatan->KabupatenKotaID->FldCaption(), $master_wilayah_kecamatan->KabupatenKotaID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Kecamatan");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_wilayah_kecamatan->Kecamatan->FldCaption(), $master_wilayah_kecamatan->Kecamatan->ReqErrMsg)) ?>");

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
fmaster_wilayah_kecamatanadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_wilayah_kecamatanadd.ValidateRequired = true;
<?php } else { ?>
fmaster_wilayah_kecamatanadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_wilayah_kecamatanadd.Lists["x_KabupatenKotaID"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$master_wilayah_kecamatan_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_wilayah_kecamatan_add->ShowPageHeader(); ?>
<?php
$master_wilayah_kecamatan_add->ShowMessage();
?>
<form name="fmaster_wilayah_kecamatanadd" id="fmaster_wilayah_kecamatanadd" class="<?php echo $master_wilayah_kecamatan_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_wilayah_kecamatan_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_wilayah_kecamatan_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_wilayah_kecamatan">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($master_wilayah_kecamatan_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_wilayah_kecamatan_add->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $master_wilayah_kecamatan_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_wilayah_kecamatanadd" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_wilayah_kecamatan->KecamatanID->Visible) { // KecamatanID ?>
<?php if (ew_IsMobile() || $master_wilayah_kecamatan_add->IsModal) { ?>
	<div id="r_KecamatanID" class="form-group">
		<label id="elh_master_wilayah_kecamatan_KecamatanID" for="x_KecamatanID" class="col-sm-2 control-label ewLabel"><?php echo $master_wilayah_kecamatan->KecamatanID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_wilayah_kecamatan->KecamatanID->CellAttributes() ?>>
<span id="el_master_wilayah_kecamatan_KecamatanID">
<input type="text" data-table="master_wilayah_kecamatan" data-field="x_KecamatanID" name="x_KecamatanID" id="x_KecamatanID" size="30" maxlength="7" placeholder="<?php echo ew_HtmlEncode($master_wilayah_kecamatan->KecamatanID->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_kecamatan->KecamatanID->EditValue ?>"<?php echo $master_wilayah_kecamatan->KecamatanID->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_kecamatan->KecamatanID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanID">
		<td><span id="elh_master_wilayah_kecamatan_KecamatanID"><?php echo $master_wilayah_kecamatan->KecamatanID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_wilayah_kecamatan->KecamatanID->CellAttributes() ?>>
<span id="el_master_wilayah_kecamatan_KecamatanID">
<input type="text" data-table="master_wilayah_kecamatan" data-field="x_KecamatanID" name="x_KecamatanID" id="x_KecamatanID" size="30" maxlength="7" placeholder="<?php echo ew_HtmlEncode($master_wilayah_kecamatan->KecamatanID->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_kecamatan->KecamatanID->EditValue ?>"<?php echo $master_wilayah_kecamatan->KecamatanID->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_kecamatan->KecamatanID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_kecamatan->KabupatenKotaID->Visible) { // KabupatenKotaID ?>
<?php if (ew_IsMobile() || $master_wilayah_kecamatan_add->IsModal) { ?>
	<div id="r_KabupatenKotaID" class="form-group">
		<label id="elh_master_wilayah_kecamatan_KabupatenKotaID" for="x_KabupatenKotaID" class="col-sm-2 control-label ewLabel"><?php echo $master_wilayah_kecamatan->KabupatenKotaID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_wilayah_kecamatan->KabupatenKotaID->CellAttributes() ?>>
<span id="el_master_wilayah_kecamatan_KabupatenKotaID">
<select data-table="master_wilayah_kecamatan" data-field="x_KabupatenKotaID" data-value-separator="<?php echo $master_wilayah_kecamatan->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $master_wilayah_kecamatan->KabupatenKotaID->EditAttributes() ?>>
<?php echo $master_wilayah_kecamatan->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $master_wilayah_kecamatan->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
<?php echo $master_wilayah_kecamatan->KabupatenKotaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenKotaID">
		<td><span id="elh_master_wilayah_kecamatan_KabupatenKotaID"><?php echo $master_wilayah_kecamatan->KabupatenKotaID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_wilayah_kecamatan->KabupatenKotaID->CellAttributes() ?>>
<span id="el_master_wilayah_kecamatan_KabupatenKotaID">
<select data-table="master_wilayah_kecamatan" data-field="x_KabupatenKotaID" data-value-separator="<?php echo $master_wilayah_kecamatan->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $master_wilayah_kecamatan->KabupatenKotaID->EditAttributes() ?>>
<?php echo $master_wilayah_kecamatan->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $master_wilayah_kecamatan->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
<?php echo $master_wilayah_kecamatan->KabupatenKotaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_kecamatan->Kecamatan->Visible) { // Kecamatan ?>
<?php if (ew_IsMobile() || $master_wilayah_kecamatan_add->IsModal) { ?>
	<div id="r_Kecamatan" class="form-group">
		<label id="elh_master_wilayah_kecamatan_Kecamatan" for="x_Kecamatan" class="col-sm-2 control-label ewLabel"><?php echo $master_wilayah_kecamatan->Kecamatan->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_wilayah_kecamatan->Kecamatan->CellAttributes() ?>>
<span id="el_master_wilayah_kecamatan_Kecamatan">
<input type="text" data-table="master_wilayah_kecamatan" data-field="x_Kecamatan" name="x_Kecamatan" id="x_Kecamatan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_wilayah_kecamatan->Kecamatan->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_kecamatan->Kecamatan->EditValue ?>"<?php echo $master_wilayah_kecamatan->Kecamatan->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_kecamatan->Kecamatan->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Kecamatan">
		<td><span id="elh_master_wilayah_kecamatan_Kecamatan"><?php echo $master_wilayah_kecamatan->Kecamatan->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_wilayah_kecamatan->Kecamatan->CellAttributes() ?>>
<span id="el_master_wilayah_kecamatan_Kecamatan">
<input type="text" data-table="master_wilayah_kecamatan" data-field="x_Kecamatan" name="x_Kecamatan" id="x_Kecamatan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_wilayah_kecamatan->Kecamatan->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_kecamatan->Kecamatan->EditValue ?>"<?php echo $master_wilayah_kecamatan->Kecamatan->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_kecamatan->Kecamatan->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_wilayah_kecamatan_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$master_wilayah_kecamatan_add->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $master_wilayah_kecamatan_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_wilayah_kecamatanadd.Init();
</script>
<?php
$master_wilayah_kecamatan_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_wilayah_kecamatan_add->Page_Terminate();
?>
