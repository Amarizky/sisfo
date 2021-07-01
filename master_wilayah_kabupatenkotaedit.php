<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_wilayah_kabupatenkotainfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_wilayah_kabupatenkota_edit = NULL; // Initialize page object first

class cmaster_wilayah_kabupatenkota_edit extends cmaster_wilayah_kabupatenkota {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_wilayah_kabupatenkota';

	// Page object name
	var $PageObjName = 'master_wilayah_kabupatenkota_edit';

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

		// Table object (master_wilayah_kabupatenkota)
		if (!isset($GLOBALS["master_wilayah_kabupatenkota"]) || get_class($GLOBALS["master_wilayah_kabupatenkota"]) == "cmaster_wilayah_kabupatenkota") {
			$GLOBALS["master_wilayah_kabupatenkota"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_wilayah_kabupatenkota"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_wilayah_kabupatenkota', TRUE);

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
		if (!$Security->CanEdit()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("master_wilayah_kabupatenkotalist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->KabupatenKotaID->SetVisibility();
		$this->ProvinsiID->SetVisibility();
		$this->KabupatenKota->SetVisibility();

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
		global $EW_EXPORT, $master_wilayah_kabupatenkota;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_wilayah_kabupatenkota);
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
	var $FormClassName = "form-horizontal ewForm ewEditForm";
	var $IsModal = FALSE;
	var $DbMasterFilter;
	var $DbDetailFilter;

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
		$this->FormClassName = "ewForm ewEditForm";
		if (ew_IsMobile() || $this->IsModal)
			$this->FormClassName = ew_Concat("form-horizontal", $this->FormClassName, " ");

		// Load key from QueryString
		if (@$_GET["KabupatenKotaID"] <> "") {
			$this->KabupatenKotaID->setQueryStringValue($_GET["KabupatenKotaID"]);
		}

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->KabupatenKotaID->CurrentValue == "") {
			$this->Page_Terminate("master_wilayah_kabupatenkotalist.php"); // Invalid key, return to list
		}

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("master_wilayah_kabupatenkotalist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "master_wilayah_kabupatenkotalist.php")
					$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} elseif ($this->getFailureMessage() == $Language->Phrase("NoRecord")) {
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->KabupatenKotaID->FldIsDetailKey) {
			$this->KabupatenKotaID->setFormValue($objForm->GetValue("x_KabupatenKotaID"));
		}
		if (!$this->ProvinsiID->FldIsDetailKey) {
			$this->ProvinsiID->setFormValue($objForm->GetValue("x_ProvinsiID"));
		}
		if (!$this->KabupatenKota->FldIsDetailKey) {
			$this->KabupatenKota->setFormValue($objForm->GetValue("x_KabupatenKota"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->KabupatenKotaID->CurrentValue = $this->KabupatenKotaID->FormValue;
		$this->ProvinsiID->CurrentValue = $this->ProvinsiID->FormValue;
		$this->KabupatenKota->CurrentValue = $this->KabupatenKota->FormValue;
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
		$this->KabupatenKotaID->setDbValue($rs->fields('KabupatenKotaID'));
		$this->ProvinsiID->setDbValue($rs->fields('ProvinsiID'));
		$this->KabupatenKota->setDbValue($rs->fields('KabupatenKota'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->KabupatenKotaID->DbValue = $row['KabupatenKotaID'];
		$this->ProvinsiID->DbValue = $row['ProvinsiID'];
		$this->KabupatenKota->DbValue = $row['KabupatenKota'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// KabupatenKotaID
		// ProvinsiID
		// KabupatenKota

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// KabupatenKotaID
		$this->KabupatenKotaID->ViewValue = $this->KabupatenKotaID->CurrentValue;
		$this->KabupatenKotaID->ViewCustomAttributes = "";

		// ProvinsiID
		if (strval($this->ProvinsiID->CurrentValue) <> "") {
			$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
		$sWhereWrk = "";
		$this->ProvinsiID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProvinsiID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->ProvinsiID->ViewValue = $this->ProvinsiID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->ProvinsiID->ViewValue = $this->ProvinsiID->CurrentValue;
			}
		} else {
			$this->ProvinsiID->ViewValue = NULL;
		}
		$this->ProvinsiID->ViewCustomAttributes = "";

		// KabupatenKota
		$this->KabupatenKota->ViewValue = $this->KabupatenKota->CurrentValue;
		$this->KabupatenKota->ViewCustomAttributes = "";

			// KabupatenKotaID
			$this->KabupatenKotaID->LinkCustomAttributes = "";
			$this->KabupatenKotaID->HrefValue = "";
			$this->KabupatenKotaID->TooltipValue = "";

			// ProvinsiID
			$this->ProvinsiID->LinkCustomAttributes = "";
			$this->ProvinsiID->HrefValue = "";
			$this->ProvinsiID->TooltipValue = "";

			// KabupatenKota
			$this->KabupatenKota->LinkCustomAttributes = "";
			$this->KabupatenKota->HrefValue = "";
			$this->KabupatenKota->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// KabupatenKotaID
			$this->KabupatenKotaID->EditAttrs["class"] = "form-control";
			$this->KabupatenKotaID->EditCustomAttributes = "";
			$this->KabupatenKotaID->EditValue = $this->KabupatenKotaID->CurrentValue;
			$this->KabupatenKotaID->ViewCustomAttributes = "";

			// ProvinsiID
			$this->ProvinsiID->EditAttrs["class"] = "form-control";
			$this->ProvinsiID->EditCustomAttributes = "";
			if (trim(strval($this->ProvinsiID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->ProvinsiID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProvinsiID->EditValue = $arwrk;

			// KabupatenKota
			$this->KabupatenKota->EditAttrs["class"] = "form-control";
			$this->KabupatenKota->EditCustomAttributes = "";
			$this->KabupatenKota->EditValue = ew_HtmlEncode($this->KabupatenKota->CurrentValue);
			$this->KabupatenKota->PlaceHolder = ew_RemoveHtml($this->KabupatenKota->FldCaption());

			// Edit refer script
			// KabupatenKotaID

			$this->KabupatenKotaID->LinkCustomAttributes = "";
			$this->KabupatenKotaID->HrefValue = "";

			// ProvinsiID
			$this->ProvinsiID->LinkCustomAttributes = "";
			$this->ProvinsiID->HrefValue = "";

			// KabupatenKota
			$this->KabupatenKota->LinkCustomAttributes = "";
			$this->KabupatenKota->HrefValue = "";
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
		if (!$this->KabupatenKotaID->FldIsDetailKey && !is_null($this->KabupatenKotaID->FormValue) && $this->KabupatenKotaID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->KabupatenKotaID->FldCaption(), $this->KabupatenKotaID->ReqErrMsg));
		}
		if (!$this->ProvinsiID->FldIsDetailKey && !is_null($this->ProvinsiID->FormValue) && $this->ProvinsiID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->ProvinsiID->FldCaption(), $this->ProvinsiID->ReqErrMsg));
		}
		if (!$this->KabupatenKota->FldIsDetailKey && !is_null($this->KabupatenKota->FormValue) && $this->KabupatenKota->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->KabupatenKota->FldCaption(), $this->KabupatenKota->ReqErrMsg));
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

	// Update record based on key values
	function EditRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$conn = &$this->Connection();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// KabupatenKotaID
			// ProvinsiID

			$this->ProvinsiID->SetDbValueDef($rsnew, $this->ProvinsiID->CurrentValue, "", $this->ProvinsiID->ReadOnly);

			// KabupatenKota
			$this->KabupatenKota->SetDbValueDef($rsnew, $this->KabupatenKota->CurrentValue, "", $this->KabupatenKota->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_wilayah_kabupatenkotalist.php"), "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_ProvinsiID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProvinsiID` AS `LinkFld`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProvinsiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->ProvinsiID, $sWhereWrk); // Call Lookup selecting
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
if (!isset($master_wilayah_kabupatenkota_edit)) $master_wilayah_kabupatenkota_edit = new cmaster_wilayah_kabupatenkota_edit();

// Page init
$master_wilayah_kabupatenkota_edit->Page_Init();

// Page main
$master_wilayah_kabupatenkota_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_wilayah_kabupatenkota_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fmaster_wilayah_kabupatenkotaedit = new ew_Form("fmaster_wilayah_kabupatenkotaedit", "edit");

// Validate form
fmaster_wilayah_kabupatenkotaedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_KabupatenKotaID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_wilayah_kabupatenkota->KabupatenKotaID->FldCaption(), $master_wilayah_kabupatenkota->KabupatenKotaID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_ProvinsiID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_wilayah_kabupatenkota->ProvinsiID->FldCaption(), $master_wilayah_kabupatenkota->ProvinsiID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_KabupatenKota");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_wilayah_kabupatenkota->KabupatenKota->FldCaption(), $master_wilayah_kabupatenkota->KabupatenKota->ReqErrMsg)) ?>");

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
fmaster_wilayah_kabupatenkotaedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_wilayah_kabupatenkotaedit.ValidateRequired = true;
<?php } else { ?>
fmaster_wilayah_kabupatenkotaedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_wilayah_kabupatenkotaedit.Lists["x_ProvinsiID"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$master_wilayah_kabupatenkota_edit->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_wilayah_kabupatenkota_edit->ShowPageHeader(); ?>
<?php
$master_wilayah_kabupatenkota_edit->ShowMessage();
?>
<form name="fmaster_wilayah_kabupatenkotaedit" id="fmaster_wilayah_kabupatenkotaedit" class="<?php echo $master_wilayah_kabupatenkota_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_wilayah_kabupatenkota_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_wilayah_kabupatenkota_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_wilayah_kabupatenkota">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<?php if ($master_wilayah_kabupatenkota_edit->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_wilayah_kabupatenkota_edit->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $master_wilayah_kabupatenkota_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_wilayah_kabupatenkotaedit" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_wilayah_kabupatenkota->KabupatenKotaID->Visible) { // KabupatenKotaID ?>
<?php if (ew_IsMobile() || $master_wilayah_kabupatenkota_edit->IsModal) { ?>
	<div id="r_KabupatenKotaID" class="form-group">
		<label id="elh_master_wilayah_kabupatenkota_KabupatenKotaID" for="x_KabupatenKotaID" class="col-sm-2 control-label ewLabel"><?php echo $master_wilayah_kabupatenkota->KabupatenKotaID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_wilayah_kabupatenkota->KabupatenKotaID->CellAttributes() ?>>
<span id="el_master_wilayah_kabupatenkota_KabupatenKotaID">
<span<?php echo $master_wilayah_kabupatenkota->KabupatenKotaID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_wilayah_kabupatenkota->KabupatenKotaID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="master_wilayah_kabupatenkota" data-field="x_KabupatenKotaID" name="x_KabupatenKotaID" id="x_KabupatenKotaID" value="<?php echo ew_HtmlEncode($master_wilayah_kabupatenkota->KabupatenKotaID->CurrentValue) ?>">
<?php echo $master_wilayah_kabupatenkota->KabupatenKotaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenKotaID">
		<td><span id="elh_master_wilayah_kabupatenkota_KabupatenKotaID"><?php echo $master_wilayah_kabupatenkota->KabupatenKotaID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_wilayah_kabupatenkota->KabupatenKotaID->CellAttributes() ?>>
<span id="el_master_wilayah_kabupatenkota_KabupatenKotaID">
<span<?php echo $master_wilayah_kabupatenkota->KabupatenKotaID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_wilayah_kabupatenkota->KabupatenKotaID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="master_wilayah_kabupatenkota" data-field="x_KabupatenKotaID" name="x_KabupatenKotaID" id="x_KabupatenKotaID" value="<?php echo ew_HtmlEncode($master_wilayah_kabupatenkota->KabupatenKotaID->CurrentValue) ?>">
<?php echo $master_wilayah_kabupatenkota->KabupatenKotaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_kabupatenkota->ProvinsiID->Visible) { // ProvinsiID ?>
<?php if (ew_IsMobile() || $master_wilayah_kabupatenkota_edit->IsModal) { ?>
	<div id="r_ProvinsiID" class="form-group">
		<label id="elh_master_wilayah_kabupatenkota_ProvinsiID" for="x_ProvinsiID" class="col-sm-2 control-label ewLabel"><?php echo $master_wilayah_kabupatenkota->ProvinsiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_wilayah_kabupatenkota->ProvinsiID->CellAttributes() ?>>
<span id="el_master_wilayah_kabupatenkota_ProvinsiID">
<select data-table="master_wilayah_kabupatenkota" data-field="x_ProvinsiID" data-value-separator="<?php echo $master_wilayah_kabupatenkota->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $master_wilayah_kabupatenkota->ProvinsiID->EditAttributes() ?>>
<?php echo $master_wilayah_kabupatenkota->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $master_wilayah_kabupatenkota->ProvinsiID->LookupFilterQuery() ?>">
</span>
<?php echo $master_wilayah_kabupatenkota->ProvinsiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiID">
		<td><span id="elh_master_wilayah_kabupatenkota_ProvinsiID"><?php echo $master_wilayah_kabupatenkota->ProvinsiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_wilayah_kabupatenkota->ProvinsiID->CellAttributes() ?>>
<span id="el_master_wilayah_kabupatenkota_ProvinsiID">
<select data-table="master_wilayah_kabupatenkota" data-field="x_ProvinsiID" data-value-separator="<?php echo $master_wilayah_kabupatenkota->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $master_wilayah_kabupatenkota->ProvinsiID->EditAttributes() ?>>
<?php echo $master_wilayah_kabupatenkota->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $master_wilayah_kabupatenkota->ProvinsiID->LookupFilterQuery() ?>">
</span>
<?php echo $master_wilayah_kabupatenkota->ProvinsiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_kabupatenkota->KabupatenKota->Visible) { // KabupatenKota ?>
<?php if (ew_IsMobile() || $master_wilayah_kabupatenkota_edit->IsModal) { ?>
	<div id="r_KabupatenKota" class="form-group">
		<label id="elh_master_wilayah_kabupatenkota_KabupatenKota" for="x_KabupatenKota" class="col-sm-2 control-label ewLabel"><?php echo $master_wilayah_kabupatenkota->KabupatenKota->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_wilayah_kabupatenkota->KabupatenKota->CellAttributes() ?>>
<span id="el_master_wilayah_kabupatenkota_KabupatenKota">
<input type="text" data-table="master_wilayah_kabupatenkota" data-field="x_KabupatenKota" name="x_KabupatenKota" id="x_KabupatenKota" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_wilayah_kabupatenkota->KabupatenKota->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_kabupatenkota->KabupatenKota->EditValue ?>"<?php echo $master_wilayah_kabupatenkota->KabupatenKota->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_kabupatenkota->KabupatenKota->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenKota">
		<td><span id="elh_master_wilayah_kabupatenkota_KabupatenKota"><?php echo $master_wilayah_kabupatenkota->KabupatenKota->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_wilayah_kabupatenkota->KabupatenKota->CellAttributes() ?>>
<span id="el_master_wilayah_kabupatenkota_KabupatenKota">
<input type="text" data-table="master_wilayah_kabupatenkota" data-field="x_KabupatenKota" name="x_KabupatenKota" id="x_KabupatenKota" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_wilayah_kabupatenkota->KabupatenKota->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_kabupatenkota->KabupatenKota->EditValue ?>"<?php echo $master_wilayah_kabupatenkota->KabupatenKota->EditAttributes() ?>>
</span>
<?php echo $master_wilayah_kabupatenkota->KabupatenKota->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_wilayah_kabupatenkota_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$master_wilayah_kabupatenkota_edit->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $master_wilayah_kabupatenkota_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_wilayah_kabupatenkotaedit.Init();
</script>
<?php
$master_wilayah_kabupatenkota_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_wilayah_kabupatenkota_edit->Page_Terminate();
?>
