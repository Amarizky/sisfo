<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_jamkulinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_jamkul_add = NULL; // Initialize page object first

class cmaster_jamkul_add extends cmaster_jamkul {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_jamkul';

	// Page object name
	var $PageObjName = 'master_jamkul_add';

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

		// Table object (master_jamkul)
		if (!isset($GLOBALS["master_jamkul"]) || get_class($GLOBALS["master_jamkul"]) == "cmaster_jamkul") {
			$GLOBALS["master_jamkul"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_jamkul"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_jamkul', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("master_jamkullist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->HariID->SetVisibility();
		$this->JamID->SetVisibility();
		$this->JamMulai->SetVisibility();
		$this->JamSelesai->SetVisibility();
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
		global $EW_EXPORT, $master_jamkul;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_jamkul);
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
			if (@$_GET["HariID"] != "") {
				$this->HariID->setQueryStringValue($_GET["HariID"]);
				$this->setKey("HariID", $this->HariID->CurrentValue); // Set up key
			} else {
				$this->setKey("HariID", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if (@$_GET["JamID"] != "") {
				$this->JamID->setQueryStringValue($_GET["JamID"]);
				$this->setKey("JamID", $this->JamID->CurrentValue); // Set up key
			} else {
				$this->setKey("JamID", ""); // Clear key
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
					$this->Page_Terminate("master_jamkullist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "master_jamkullist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "master_jamkulview.php")
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
		$this->HariID->CurrentValue = NULL;
		$this->HariID->OldValue = $this->HariID->CurrentValue;
		$this->JamID->CurrentValue = NULL;
		$this->JamID->OldValue = $this->JamID->CurrentValue;
		$this->JamMulai->CurrentValue = NULL;
		$this->JamMulai->OldValue = $this->JamMulai->CurrentValue;
		$this->JamSelesai->CurrentValue = NULL;
		$this->JamSelesai->OldValue = $this->JamSelesai->CurrentValue;
		$this->NA->CurrentValue = "N";
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->HariID->FldIsDetailKey) {
			$this->HariID->setFormValue($objForm->GetValue("x_HariID"));
		}
		if (!$this->JamID->FldIsDetailKey) {
			$this->JamID->setFormValue($objForm->GetValue("x_JamID"));
		}
		if (!$this->JamMulai->FldIsDetailKey) {
			$this->JamMulai->setFormValue($objForm->GetValue("x_JamMulai"));
			$this->JamMulai->CurrentValue = ew_UnFormatDateTime($this->JamMulai->CurrentValue, 0);
		}
		if (!$this->JamSelesai->FldIsDetailKey) {
			$this->JamSelesai->setFormValue($objForm->GetValue("x_JamSelesai"));
			$this->JamSelesai->CurrentValue = ew_UnFormatDateTime($this->JamSelesai->CurrentValue, 0);
		}
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->HariID->CurrentValue = $this->HariID->FormValue;
		$this->JamID->CurrentValue = $this->JamID->FormValue;
		$this->JamMulai->CurrentValue = $this->JamMulai->FormValue;
		$this->JamMulai->CurrentValue = ew_UnFormatDateTime($this->JamMulai->CurrentValue, 0);
		$this->JamSelesai->CurrentValue = $this->JamSelesai->FormValue;
		$this->JamSelesai->CurrentValue = ew_UnFormatDateTime($this->JamSelesai->CurrentValue, 0);
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
		$this->HariID->setDbValue($rs->fields('HariID'));
		$this->JamID->setDbValue($rs->fields('JamID'));
		$this->JamMulai->setDbValue($rs->fields('JamMulai'));
		$this->JamSelesai->setDbValue($rs->fields('JamSelesai'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->HariID->DbValue = $row['HariID'];
		$this->JamID->DbValue = $row['JamID'];
		$this->JamMulai->DbValue = $row['JamMulai'];
		$this->JamSelesai->DbValue = $row['JamSelesai'];
		$this->NA->DbValue = $row['NA'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("HariID")) <> "")
			$this->HariID->CurrentValue = $this->getKey("HariID"); // HariID
		else
			$bValidKey = FALSE;
		if (strval($this->getKey("JamID")) <> "")
			$this->JamID->CurrentValue = $this->getKey("JamID"); // JamID
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
		// HariID
		// JamID
		// JamMulai
		// JamSelesai
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// HariID
		if (strval($this->HariID->CurrentValue) <> "") {
			$sFilterWrk = "`HariID`" . ew_SearchString("=", $this->HariID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `HariID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hari`";
		$sWhereWrk = "";
		$this->HariID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->HariID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->HariID->ViewValue = $this->HariID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->HariID->ViewValue = $this->HariID->CurrentValue;
			}
		} else {
			$this->HariID->ViewValue = NULL;
		}
		$this->HariID->ViewCustomAttributes = "";

		// JamID
		$this->JamID->ViewValue = $this->JamID->CurrentValue;
		$this->JamID->ViewCustomAttributes = "";

		// JamMulai
		$this->JamMulai->ViewValue = $this->JamMulai->CurrentValue;
		$this->JamMulai->ViewCustomAttributes = "";

		// JamSelesai
		$this->JamSelesai->ViewValue = $this->JamSelesai->CurrentValue;
		$this->JamSelesai->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

			// HariID
			$this->HariID->LinkCustomAttributes = "";
			$this->HariID->HrefValue = "";
			$this->HariID->TooltipValue = "";

			// JamID
			$this->JamID->LinkCustomAttributes = "";
			$this->JamID->HrefValue = "";
			$this->JamID->TooltipValue = "";

			// JamMulai
			$this->JamMulai->LinkCustomAttributes = "";
			$this->JamMulai->HrefValue = "";
			$this->JamMulai->TooltipValue = "";

			// JamSelesai
			$this->JamSelesai->LinkCustomAttributes = "";
			$this->JamSelesai->HrefValue = "";
			$this->JamSelesai->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// HariID
			$this->HariID->EditAttrs["class"] = "form-control";
			$this->HariID->EditCustomAttributes = "";
			if (trim(strval($this->HariID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`HariID`" . ew_SearchString("=", $this->HariID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `HariID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_hari`";
			$sWhereWrk = "";
			$this->HariID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->HariID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->HariID->EditValue = $arwrk;

			// JamID
			$this->JamID->EditAttrs["class"] = "form-control";
			$this->JamID->EditCustomAttributes = "";
			$this->JamID->EditValue = ew_HtmlEncode($this->JamID->CurrentValue);
			$this->JamID->PlaceHolder = ew_RemoveHtml($this->JamID->FldCaption());

			// JamMulai
			$this->JamMulai->EditAttrs["class"] = "form-control";
			$this->JamMulai->EditCustomAttributes = "";
			$this->JamMulai->EditValue = ew_HtmlEncode($this->JamMulai->CurrentValue);
			$this->JamMulai->PlaceHolder = ew_RemoveHtml($this->JamMulai->FldCaption());

			// JamSelesai
			$this->JamSelesai->EditAttrs["class"] = "form-control";
			$this->JamSelesai->EditCustomAttributes = "";
			$this->JamSelesai->EditValue = ew_HtmlEncode($this->JamSelesai->CurrentValue);
			$this->JamSelesai->PlaceHolder = ew_RemoveHtml($this->JamSelesai->FldCaption());

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// HariID

			$this->HariID->LinkCustomAttributes = "";
			$this->HariID->HrefValue = "";

			// JamID
			$this->JamID->LinkCustomAttributes = "";
			$this->JamID->HrefValue = "";

			// JamMulai
			$this->JamMulai->LinkCustomAttributes = "";
			$this->JamMulai->HrefValue = "";

			// JamSelesai
			$this->JamSelesai->LinkCustomAttributes = "";
			$this->JamSelesai->HrefValue = "";

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
		if (!$this->HariID->FldIsDetailKey && !is_null($this->HariID->FormValue) && $this->HariID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->HariID->FldCaption(), $this->HariID->ReqErrMsg));
		}
		if (!$this->JamID->FldIsDetailKey && !is_null($this->JamID->FormValue) && $this->JamID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->JamID->FldCaption(), $this->JamID->ReqErrMsg));
		}
		if (!ew_CheckTime($this->JamMulai->FormValue)) {
			ew_AddMessage($gsFormError, $this->JamMulai->FldErrMsg());
		}
		if (!ew_CheckTime($this->JamSelesai->FormValue)) {
			ew_AddMessage($gsFormError, $this->JamSelesai->FldErrMsg());
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

		// HariID
		$this->HariID->SetDbValueDef($rsnew, $this->HariID->CurrentValue, 0, FALSE);

		// JamID
		$this->JamID->SetDbValueDef($rsnew, $this->JamID->CurrentValue, "", FALSE);

		// JamMulai
		$this->JamMulai->SetDbValueDef($rsnew, $this->JamMulai->CurrentValue, NULL, FALSE);

		// JamSelesai
		$this->JamSelesai->SetDbValueDef($rsnew, $this->JamSelesai->CurrentValue, NULL, FALSE);

		// NA
		$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), NULL, strval($this->NA->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['HariID']) == "") {
			$this->setFailureMessage($Language->Phrase("InvalidKeyValue"));
			$bInsertRow = FALSE;
		}

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['JamID']) == "") {
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_jamkullist.php"), "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_HariID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `HariID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hari`";
			$sWhereWrk = "";
			$this->HariID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`HariID` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->HariID, $sWhereWrk); // Call Lookup selecting
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
if (!isset($master_jamkul_add)) $master_jamkul_add = new cmaster_jamkul_add();

// Page init
$master_jamkul_add->Page_Init();

// Page main
$master_jamkul_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_jamkul_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fmaster_jamkuladd = new ew_Form("fmaster_jamkuladd", "add");

// Validate form
fmaster_jamkuladd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_HariID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_jamkul->HariID->FldCaption(), $master_jamkul->HariID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JamID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_jamkul->JamID->FldCaption(), $master_jamkul->JamID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JamMulai");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($master_jamkul->JamMulai->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_JamSelesai");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($master_jamkul->JamSelesai->FldErrMsg()) ?>");

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
fmaster_jamkuladd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_jamkuladd.ValidateRequired = true;
<?php } else { ?>
fmaster_jamkuladd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_jamkuladd.Lists["x_HariID"] = {"LinkField":"x_HariID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_hari"};
fmaster_jamkuladd.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_jamkuladd.Lists["x_NA"].Options = <?php echo json_encode($master_jamkul->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$master_jamkul_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_jamkul_add->ShowPageHeader(); ?>
<?php
$master_jamkul_add->ShowMessage();
?>
<form name="fmaster_jamkuladd" id="fmaster_jamkuladd" class="<?php echo $master_jamkul_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_jamkul_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_jamkul_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_jamkul">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($master_jamkul_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_jamkul_add->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $master_jamkul_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_jamkuladd" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_jamkul->HariID->Visible) { // HariID ?>
<?php if (ew_IsMobile() || $master_jamkul_add->IsModal) { ?>
	<div id="r_HariID" class="form-group">
		<label id="elh_master_jamkul_HariID" for="x_HariID" class="col-sm-2 control-label ewLabel"><?php echo $master_jamkul->HariID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_jamkul->HariID->CellAttributes() ?>>
<span id="el_master_jamkul_HariID">
<select data-table="master_jamkul" data-field="x_HariID" data-value-separator="<?php echo $master_jamkul->HariID->DisplayValueSeparatorAttribute() ?>" id="x_HariID" name="x_HariID"<?php echo $master_jamkul->HariID->EditAttributes() ?>>
<?php echo $master_jamkul->HariID->SelectOptionListHtml("x_HariID") ?>
</select>
<input type="hidden" name="s_x_HariID" id="s_x_HariID" value="<?php echo $master_jamkul->HariID->LookupFilterQuery() ?>">
</span>
<?php echo $master_jamkul->HariID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_HariID">
		<td><span id="elh_master_jamkul_HariID"><?php echo $master_jamkul->HariID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_jamkul->HariID->CellAttributes() ?>>
<span id="el_master_jamkul_HariID">
<select data-table="master_jamkul" data-field="x_HariID" data-value-separator="<?php echo $master_jamkul->HariID->DisplayValueSeparatorAttribute() ?>" id="x_HariID" name="x_HariID"<?php echo $master_jamkul->HariID->EditAttributes() ?>>
<?php echo $master_jamkul->HariID->SelectOptionListHtml("x_HariID") ?>
</select>
<input type="hidden" name="s_x_HariID" id="s_x_HariID" value="<?php echo $master_jamkul->HariID->LookupFilterQuery() ?>">
</span>
<?php echo $master_jamkul->HariID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_jamkul->JamID->Visible) { // JamID ?>
<?php if (ew_IsMobile() || $master_jamkul_add->IsModal) { ?>
	<div id="r_JamID" class="form-group">
		<label id="elh_master_jamkul_JamID" for="x_JamID" class="col-sm-2 control-label ewLabel"><?php echo $master_jamkul->JamID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_jamkul->JamID->CellAttributes() ?>>
<span id="el_master_jamkul_JamID">
<input type="text" data-table="master_jamkul" data-field="x_JamID" name="x_JamID" id="x_JamID" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_jamkul->JamID->getPlaceHolder()) ?>" value="<?php echo $master_jamkul->JamID->EditValue ?>"<?php echo $master_jamkul->JamID->EditAttributes() ?>>
</span>
<?php echo $master_jamkul->JamID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_JamID">
		<td><span id="elh_master_jamkul_JamID"><?php echo $master_jamkul->JamID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_jamkul->JamID->CellAttributes() ?>>
<span id="el_master_jamkul_JamID">
<input type="text" data-table="master_jamkul" data-field="x_JamID" name="x_JamID" id="x_JamID" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_jamkul->JamID->getPlaceHolder()) ?>" value="<?php echo $master_jamkul->JamID->EditValue ?>"<?php echo $master_jamkul->JamID->EditAttributes() ?>>
</span>
<?php echo $master_jamkul->JamID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_jamkul->JamMulai->Visible) { // JamMulai ?>
<?php if (ew_IsMobile() || $master_jamkul_add->IsModal) { ?>
	<div id="r_JamMulai" class="form-group">
		<label id="elh_master_jamkul_JamMulai" for="x_JamMulai" class="col-sm-2 control-label ewLabel"><?php echo $master_jamkul->JamMulai->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_jamkul->JamMulai->CellAttributes() ?>>
<span id="el_master_jamkul_JamMulai">
<input type="text" data-table="master_jamkul" data-field="x_JamMulai" name="x_JamMulai" id="x_JamMulai" size="30" placeholder="<?php echo ew_HtmlEncode($master_jamkul->JamMulai->getPlaceHolder()) ?>" value="<?php echo $master_jamkul->JamMulai->EditValue ?>"<?php echo $master_jamkul->JamMulai->EditAttributes() ?>>
</span>
<?php echo $master_jamkul->JamMulai->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_JamMulai">
		<td><span id="elh_master_jamkul_JamMulai"><?php echo $master_jamkul->JamMulai->FldCaption() ?></span></td>
		<td<?php echo $master_jamkul->JamMulai->CellAttributes() ?>>
<span id="el_master_jamkul_JamMulai">
<input type="text" data-table="master_jamkul" data-field="x_JamMulai" name="x_JamMulai" id="x_JamMulai" size="30" placeholder="<?php echo ew_HtmlEncode($master_jamkul->JamMulai->getPlaceHolder()) ?>" value="<?php echo $master_jamkul->JamMulai->EditValue ?>"<?php echo $master_jamkul->JamMulai->EditAttributes() ?>>
</span>
<?php echo $master_jamkul->JamMulai->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_jamkul->JamSelesai->Visible) { // JamSelesai ?>
<?php if (ew_IsMobile() || $master_jamkul_add->IsModal) { ?>
	<div id="r_JamSelesai" class="form-group">
		<label id="elh_master_jamkul_JamSelesai" for="x_JamSelesai" class="col-sm-2 control-label ewLabel"><?php echo $master_jamkul->JamSelesai->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_jamkul->JamSelesai->CellAttributes() ?>>
<span id="el_master_jamkul_JamSelesai">
<input type="text" data-table="master_jamkul" data-field="x_JamSelesai" name="x_JamSelesai" id="x_JamSelesai" size="30" placeholder="<?php echo ew_HtmlEncode($master_jamkul->JamSelesai->getPlaceHolder()) ?>" value="<?php echo $master_jamkul->JamSelesai->EditValue ?>"<?php echo $master_jamkul->JamSelesai->EditAttributes() ?>>
</span>
<?php echo $master_jamkul->JamSelesai->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_JamSelesai">
		<td><span id="elh_master_jamkul_JamSelesai"><?php echo $master_jamkul->JamSelesai->FldCaption() ?></span></td>
		<td<?php echo $master_jamkul->JamSelesai->CellAttributes() ?>>
<span id="el_master_jamkul_JamSelesai">
<input type="text" data-table="master_jamkul" data-field="x_JamSelesai" name="x_JamSelesai" id="x_JamSelesai" size="30" placeholder="<?php echo ew_HtmlEncode($master_jamkul->JamSelesai->getPlaceHolder()) ?>" value="<?php echo $master_jamkul->JamSelesai->EditValue ?>"<?php echo $master_jamkul->JamSelesai->EditAttributes() ?>>
</span>
<?php echo $master_jamkul->JamSelesai->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_jamkul->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $master_jamkul_add->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_master_jamkul_NA" class="col-sm-2 control-label ewLabel"><?php echo $master_jamkul->NA->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_jamkul->NA->CellAttributes() ?>>
<span id="el_master_jamkul_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_jamkul" data-field="x_NA" data-value-separator="<?php echo $master_jamkul->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_jamkul->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_jamkul->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $master_jamkul->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_master_jamkul_NA"><?php echo $master_jamkul->NA->FldCaption() ?></span></td>
		<td<?php echo $master_jamkul->NA->CellAttributes() ?>>
<span id="el_master_jamkul_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_jamkul" data-field="x_NA" data-value-separator="<?php echo $master_jamkul->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_jamkul->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_jamkul->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $master_jamkul->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_jamkul_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$master_jamkul_add->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $master_jamkul_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_jamkuladd.Init();
</script>
<?php
$master_jamkul_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_jamkul_add->Page_Terminate();
?>
