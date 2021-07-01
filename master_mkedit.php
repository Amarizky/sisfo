<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_mkinfo.php" ?>
<?php include_once "kurikuluminfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_mk_edit = NULL; // Initialize page object first

class cmaster_mk_edit extends cmaster_mk {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_mk';

	// Page object name
	var $PageObjName = 'master_mk_edit';

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

		// Table object (master_mk)
		if (!isset($GLOBALS["master_mk"]) || get_class($GLOBALS["master_mk"]) == "cmaster_mk") {
			$GLOBALS["master_mk"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_mk"];
		}

		// Table object (kurikulum)
		if (!isset($GLOBALS['kurikulum'])) $GLOBALS['kurikulum'] = new ckurikulum();

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_mk', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("master_mklist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->MKID->SetVisibility();
		$this->MKID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->KampusID->SetVisibility();
		$this->ProdiID->SetVisibility();
		$this->KurikulumID->SetVisibility();
		$this->MKKode->SetVisibility();
		$this->Nama->SetVisibility();
		$this->Singkatan->SetVisibility();
		$this->Tingkat->SetVisibility();
		$this->Sesi->SetVisibility();
		$this->Wajib->SetVisibility();
		$this->Deskripsi->SetVisibility();
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
		global $EW_EXPORT, $master_mk;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_mk);
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
		if (@$_GET["MKID"] <> "") {
			$this->MKID->setQueryStringValue($_GET["MKID"]);
		}

		// Set up master detail parameters
		$this->SetUpMasterParms();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->MKID->CurrentValue == "") {
			$this->Page_Terminate("master_mklist.php"); // Invalid key, return to list
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
					$this->Page_Terminate("master_mklist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "master_mklist.php")
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
		if (!$this->MKID->FldIsDetailKey)
			$this->MKID->setFormValue($objForm->GetValue("x_MKID"));
		if (!$this->KampusID->FldIsDetailKey) {
			$this->KampusID->setFormValue($objForm->GetValue("x_KampusID"));
		}
		if (!$this->ProdiID->FldIsDetailKey) {
			$this->ProdiID->setFormValue($objForm->GetValue("x_ProdiID"));
		}
		if (!$this->KurikulumID->FldIsDetailKey) {
			$this->KurikulumID->setFormValue($objForm->GetValue("x_KurikulumID"));
		}
		if (!$this->MKKode->FldIsDetailKey) {
			$this->MKKode->setFormValue($objForm->GetValue("x_MKKode"));
		}
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->Singkatan->FldIsDetailKey) {
			$this->Singkatan->setFormValue($objForm->GetValue("x_Singkatan"));
		}
		if (!$this->Tingkat->FldIsDetailKey) {
			$this->Tingkat->setFormValue($objForm->GetValue("x_Tingkat"));
		}
		if (!$this->Sesi->FldIsDetailKey) {
			$this->Sesi->setFormValue($objForm->GetValue("x_Sesi"));
		}
		if (!$this->Wajib->FldIsDetailKey) {
			$this->Wajib->setFormValue($objForm->GetValue("x_Wajib"));
		}
		if (!$this->Deskripsi->FldIsDetailKey) {
			$this->Deskripsi->setFormValue($objForm->GetValue("x_Deskripsi"));
		}
		if (!$this->Editor->FldIsDetailKey) {
			$this->Editor->setFormValue($objForm->GetValue("x_Editor"));
			$this->Editor->CurrentValue = ew_UnFormatDateTime($this->Editor->CurrentValue, 0);
		}
		if (!$this->EditDate->FldIsDetailKey) {
			$this->EditDate->setFormValue($objForm->GetValue("x_EditDate"));
		}
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->MKID->CurrentValue = $this->MKID->FormValue;
		$this->KampusID->CurrentValue = $this->KampusID->FormValue;
		$this->ProdiID->CurrentValue = $this->ProdiID->FormValue;
		$this->KurikulumID->CurrentValue = $this->KurikulumID->FormValue;
		$this->MKKode->CurrentValue = $this->MKKode->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->Singkatan->CurrentValue = $this->Singkatan->FormValue;
		$this->Tingkat->CurrentValue = $this->Tingkat->FormValue;
		$this->Sesi->CurrentValue = $this->Sesi->FormValue;
		$this->Wajib->CurrentValue = $this->Wajib->FormValue;
		$this->Deskripsi->CurrentValue = $this->Deskripsi->FormValue;
		$this->Editor->CurrentValue = $this->Editor->FormValue;
		$this->Editor->CurrentValue = ew_UnFormatDateTime($this->Editor->CurrentValue, 0);
		$this->EditDate->CurrentValue = $this->EditDate->FormValue;
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
		$this->MKID->setDbValue($rs->fields('MKID'));
		$this->KampusID->setDbValue($rs->fields('KampusID'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->KurikulumID->setDbValue($rs->fields('KurikulumID'));
		$this->MKKode->setDbValue($rs->fields('MKKode'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->Singkatan->setDbValue($rs->fields('Singkatan'));
		$this->Tingkat->setDbValue($rs->fields('Tingkat'));
		$this->Sesi->setDbValue($rs->fields('Sesi'));
		$this->Wajib->setDbValue($rs->fields('Wajib'));
		$this->Deskripsi->setDbValue($rs->fields('Deskripsi'));
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
		$this->MKID->DbValue = $row['MKID'];
		$this->KampusID->DbValue = $row['KampusID'];
		$this->ProdiID->DbValue = $row['ProdiID'];
		$this->KurikulumID->DbValue = $row['KurikulumID'];
		$this->MKKode->DbValue = $row['MKKode'];
		$this->Nama->DbValue = $row['Nama'];
		$this->Singkatan->DbValue = $row['Singkatan'];
		$this->Tingkat->DbValue = $row['Tingkat'];
		$this->Sesi->DbValue = $row['Sesi'];
		$this->Wajib->DbValue = $row['Wajib'];
		$this->Deskripsi->DbValue = $row['Deskripsi'];
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
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// MKID
		// KampusID
		// ProdiID
		// KurikulumID
		// MKKode
		// Nama
		// Singkatan
		// Tingkat
		// Sesi
		// Wajib
		// Deskripsi
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// MKID
		$this->MKID->ViewValue = $this->MKID->CurrentValue;
		$this->MKID->ViewCustomAttributes = "";

		// KampusID
		if (strval($this->KampusID->CurrentValue) <> "") {
			$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->KampusID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
		$sWhereWrk = "";
		$this->KampusID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KampusID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KampusID->ViewValue = $this->KampusID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KampusID->ViewValue = $this->KampusID->CurrentValue;
			}
		} else {
			$this->KampusID->ViewValue = NULL;
		}
		$this->KampusID->ViewCustomAttributes = "";

		// ProdiID
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->ProdiID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `ProdiID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->ProdiID->ViewValue = $this->ProdiID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
			}
		} else {
			$this->ProdiID->ViewValue = NULL;
		}
		$this->ProdiID->ViewCustomAttributes = "";

		// KurikulumID
		if (strval($this->KurikulumID->CurrentValue) <> "") {
			$sFilterWrk = "`KurikulumID`" . ew_SearchString("=", $this->KurikulumID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `KurikulumID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kurikulum`";
		$sWhereWrk = "";
		$this->KurikulumID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KurikulumID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KurikulumID->ViewValue = $this->KurikulumID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KurikulumID->ViewValue = $this->KurikulumID->CurrentValue;
			}
		} else {
			$this->KurikulumID->ViewValue = NULL;
		}
		$this->KurikulumID->ViewCustomAttributes = "";

		// MKKode
		$this->MKKode->ViewValue = $this->MKKode->CurrentValue;
		$this->MKKode->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// Singkatan
		$this->Singkatan->ViewValue = $this->Singkatan->CurrentValue;
		$this->Singkatan->ViewCustomAttributes = "";

		// Tingkat
		if (strval($this->Tingkat->CurrentValue) <> "") {
			$sFilterWrk = "`Tingkat`" . ew_SearchString("=", $this->Tingkat->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
		$sWhereWrk = "";
		$this->Tingkat->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Tingkat->ViewValue = $this->Tingkat->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Tingkat->ViewValue = $this->Tingkat->CurrentValue;
			}
		} else {
			$this->Tingkat->ViewValue = NULL;
		}
		$this->Tingkat->ViewCustomAttributes = "";

		// Sesi
		if (strval($this->Sesi->CurrentValue) <> "") {
			$sFilterWrk = "`Sesi`" . ew_SearchString("=", $this->Sesi->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `Sesi`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_sesi`";
		$sWhereWrk = "";
		$this->Sesi->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Sesi->ViewValue = $this->Sesi->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Sesi->ViewValue = $this->Sesi->CurrentValue;
			}
		} else {
			$this->Sesi->ViewValue = NULL;
		}
		$this->Sesi->ViewCustomAttributes = "";

		// Wajib
		if (ew_ConvertToBool($this->Wajib->CurrentValue)) {
			$this->Wajib->ViewValue = $this->Wajib->FldTagCaption(1) <> "" ? $this->Wajib->FldTagCaption(1) : "Y";
		} else {
			$this->Wajib->ViewValue = $this->Wajib->FldTagCaption(2) <> "" ? $this->Wajib->FldTagCaption(2) : "N";
		}
		$this->Wajib->ViewCustomAttributes = "";

		// Deskripsi
		$this->Deskripsi->ViewValue = $this->Deskripsi->CurrentValue;
		$this->Deskripsi->ViewCustomAttributes = "";

		// Creator
		$this->Creator->ViewValue = $this->Creator->CurrentValue;
		$this->Creator->ViewCustomAttributes = "";

		// CreateDate
		$this->CreateDate->ViewValue = $this->CreateDate->CurrentValue;
		$this->CreateDate->ViewValue = ew_FormatDateTime($this->CreateDate->ViewValue, 0);
		$this->CreateDate->ViewCustomAttributes = "";

		// Editor
		$this->Editor->ViewValue = $this->Editor->CurrentValue;
		$this->Editor->ViewValue = ew_FormatDateTime($this->Editor->ViewValue, 0);
		$this->Editor->ViewCustomAttributes = "";

		// EditDate
		$this->EditDate->ViewValue = $this->EditDate->CurrentValue;
		$this->EditDate->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";
			$this->MKID->TooltipValue = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";
			$this->KampusID->TooltipValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// KurikulumID
			$this->KurikulumID->LinkCustomAttributes = "";
			$this->KurikulumID->HrefValue = "";
			$this->KurikulumID->TooltipValue = "";

			// MKKode
			$this->MKKode->LinkCustomAttributes = "";
			$this->MKKode->HrefValue = "";
			$this->MKKode->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// Singkatan
			$this->Singkatan->LinkCustomAttributes = "";
			$this->Singkatan->HrefValue = "";
			$this->Singkatan->TooltipValue = "";

			// Tingkat
			$this->Tingkat->LinkCustomAttributes = "";
			$this->Tingkat->HrefValue = "";
			$this->Tingkat->TooltipValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";
			$this->Sesi->TooltipValue = "";

			// Wajib
			$this->Wajib->LinkCustomAttributes = "";
			$this->Wajib->HrefValue = "";
			$this->Wajib->TooltipValue = "";

			// Deskripsi
			$this->Deskripsi->LinkCustomAttributes = "";
			$this->Deskripsi->HrefValue = "";
			$this->Deskripsi->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// MKID
			$this->MKID->EditAttrs["class"] = "form-control";
			$this->MKID->EditCustomAttributes = "";
			$this->MKID->EditValue = $this->MKID->CurrentValue;
			$this->MKID->ViewCustomAttributes = "";

			// KampusID
			$this->KampusID->EditAttrs["class"] = "form-control";
			$this->KampusID->EditCustomAttributes = "";
			if ($this->KampusID->getSessionValue() <> "") {
				$this->KampusID->CurrentValue = $this->KampusID->getSessionValue();
			if (strval($this->KampusID->CurrentValue) <> "") {
				$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->KampusID->CurrentValue, EW_DATATYPE_STRING, "");
			$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
			$sWhereWrk = "";
			$this->KampusID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KampusID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->KampusID->ViewValue = $this->KampusID->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->KampusID->ViewValue = $this->KampusID->CurrentValue;
				}
			} else {
				$this->KampusID->ViewValue = NULL;
			}
			$this->KampusID->ViewCustomAttributes = "";
			} else {
			if (trim(strval($this->KampusID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->KampusID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_kampus`";
			$sWhereWrk = "";
			$this->KampusID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KampusID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KampusID->EditValue = $arwrk;
			}

			// ProdiID
			$this->ProdiID->EditAttrs["class"] = "form-control";
			$this->ProdiID->EditCustomAttributes = "";
			if ($this->ProdiID->getSessionValue() <> "") {
				$this->ProdiID->CurrentValue = $this->ProdiID->getSessionValue();
			if (strval($this->ProdiID->CurrentValue) <> "") {
				$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
			$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->ProdiID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `ProdiID` ASC";
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->ProdiID->ViewValue = $this->ProdiID->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
				}
			} else {
				$this->ProdiID->ViewValue = NULL;
			}
			$this->ProdiID->ViewCustomAttributes = "";
			} else {
			if (trim(strval($this->ProdiID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->ProdiID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `ProdiID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProdiID->EditValue = $arwrk;
			}

			// KurikulumID
			$this->KurikulumID->EditAttrs["class"] = "form-control";
			$this->KurikulumID->EditCustomAttributes = "";
			if ($this->KurikulumID->getSessionValue() <> "") {
				$this->KurikulumID->CurrentValue = $this->KurikulumID->getSessionValue();
			if (strval($this->KurikulumID->CurrentValue) <> "") {
				$sFilterWrk = "`KurikulumID`" . ew_SearchString("=", $this->KurikulumID->CurrentValue, EW_DATATYPE_NUMBER, "");
			$sSqlWrk = "SELECT `KurikulumID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kurikulum`";
			$sWhereWrk = "";
			$this->KurikulumID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KurikulumID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->KurikulumID->ViewValue = $this->KurikulumID->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->KurikulumID->ViewValue = $this->KurikulumID->CurrentValue;
				}
			} else {
				$this->KurikulumID->ViewValue = NULL;
			}
			$this->KurikulumID->ViewCustomAttributes = "";
			} else {
			if (trim(strval($this->KurikulumID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KurikulumID`" . ew_SearchString("=", $this->KurikulumID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `KurikulumID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kurikulum`";
			$sWhereWrk = "";
			$this->KurikulumID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KurikulumID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KurikulumID->EditValue = $arwrk;
			}

			// MKKode
			$this->MKKode->EditAttrs["class"] = "form-control";
			$this->MKKode->EditCustomAttributes = "";
			$this->MKKode->EditValue = ew_HtmlEncode($this->MKKode->CurrentValue);
			$this->MKKode->PlaceHolder = ew_RemoveHtml($this->MKKode->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// Singkatan
			$this->Singkatan->EditAttrs["class"] = "form-control";
			$this->Singkatan->EditCustomAttributes = "";
			$this->Singkatan->EditValue = ew_HtmlEncode($this->Singkatan->CurrentValue);
			$this->Singkatan->PlaceHolder = ew_RemoveHtml($this->Singkatan->FldCaption());

			// Tingkat
			$this->Tingkat->EditAttrs["class"] = "form-control";
			$this->Tingkat->EditCustomAttributes = "";
			if (trim(strval($this->Tingkat->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Tingkat`" . ew_SearchString("=", $this->Tingkat->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Tingkat->EditValue = $arwrk;

			// Sesi
			$this->Sesi->EditAttrs["class"] = "form-control";
			$this->Sesi->EditCustomAttributes = "";
			if (trim(strval($this->Sesi->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Sesi`" . ew_SearchString("=", $this->Sesi->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `Sesi`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_sesi`";
			$sWhereWrk = "";
			$this->Sesi->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Sesi->EditValue = $arwrk;

			// Wajib
			$this->Wajib->EditCustomAttributes = "";
			$this->Wajib->EditValue = $this->Wajib->Options(FALSE);

			// Deskripsi
			$this->Deskripsi->EditAttrs["class"] = "form-control";
			$this->Deskripsi->EditCustomAttributes = "";
			$this->Deskripsi->EditValue = ew_HtmlEncode($this->Deskripsi->CurrentValue);
			$this->Deskripsi->PlaceHolder = ew_RemoveHtml($this->Deskripsi->FldCaption());

			// Editor
			// EditDate
			// NA

			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Edit refer script
			// MKID

			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";

			// KurikulumID
			$this->KurikulumID->LinkCustomAttributes = "";
			$this->KurikulumID->HrefValue = "";

			// MKKode
			$this->MKKode->LinkCustomAttributes = "";
			$this->MKKode->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// Singkatan
			$this->Singkatan->LinkCustomAttributes = "";
			$this->Singkatan->HrefValue = "";

			// Tingkat
			$this->Tingkat->LinkCustomAttributes = "";
			$this->Tingkat->HrefValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";

			// Wajib
			$this->Wajib->LinkCustomAttributes = "";
			$this->Wajib->HrefValue = "";

			// Deskripsi
			$this->Deskripsi->LinkCustomAttributes = "";
			$this->Deskripsi->HrefValue = "";

			// Editor
			$this->Editor->LinkCustomAttributes = "";
			$this->Editor->HrefValue = "";

			// EditDate
			$this->EditDate->LinkCustomAttributes = "";
			$this->EditDate->HrefValue = "";

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
		if (!$this->Tingkat->FldIsDetailKey && !is_null($this->Tingkat->FormValue) && $this->Tingkat->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Tingkat->FldCaption(), $this->Tingkat->ReqErrMsg));
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

			// KampusID
			$this->KampusID->SetDbValueDef($rsnew, $this->KampusID->CurrentValue, NULL, $this->KampusID->ReadOnly);

			// ProdiID
			$this->ProdiID->SetDbValueDef($rsnew, $this->ProdiID->CurrentValue, "", $this->ProdiID->ReadOnly);

			// KurikulumID
			$this->KurikulumID->SetDbValueDef($rsnew, $this->KurikulumID->CurrentValue, 0, $this->KurikulumID->ReadOnly);

			// MKKode
			$this->MKKode->SetDbValueDef($rsnew, $this->MKKode->CurrentValue, NULL, $this->MKKode->ReadOnly);

			// Nama
			$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, "", $this->Nama->ReadOnly);

			// Singkatan
			$this->Singkatan->SetDbValueDef($rsnew, $this->Singkatan->CurrentValue, NULL, $this->Singkatan->ReadOnly);

			// Tingkat
			$this->Tingkat->SetDbValueDef($rsnew, $this->Tingkat->CurrentValue, "", $this->Tingkat->ReadOnly);

			// Sesi
			$this->Sesi->SetDbValueDef($rsnew, $this->Sesi->CurrentValue, 0, $this->Sesi->ReadOnly);

			// Wajib
			$this->Wajib->SetDbValueDef($rsnew, ((strval($this->Wajib->CurrentValue) == "Y") ? "Y" : "N"), NULL, $this->Wajib->ReadOnly);

			// Deskripsi
			$this->Deskripsi->SetDbValueDef($rsnew, $this->Deskripsi->CurrentValue, NULL, $this->Deskripsi->ReadOnly);

			// Editor
			$this->Editor->SetDbValueDef($rsnew, CurrentUserName(), NULL);
			$rsnew['Editor'] = &$this->Editor->DbValue;

			// EditDate
			$this->EditDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
			$rsnew['EditDate'] = &$this->EditDate->DbValue;

			// NA
			$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), NULL, $this->NA->ReadOnly);

			// Check referential integrity for master table 'kurikulum'
			$bValidMasterRecord = TRUE;
			$sMasterFilter = $this->SqlMasterFilter_kurikulum();
			$KeyValue = isset($rsnew['KurikulumID']) ? $rsnew['KurikulumID'] : $rsold['KurikulumID'];
			if (strval($KeyValue) <> "") {
				$sMasterFilter = str_replace("@KurikulumID@", ew_AdjustSql($KeyValue), $sMasterFilter);
			} else {
				$bValidMasterRecord = FALSE;
			}
			$KeyValue = isset($rsnew['ProdiID']) ? $rsnew['ProdiID'] : $rsold['ProdiID'];
			if (strval($KeyValue) <> "") {
				$sMasterFilter = str_replace("@ProdiID@", ew_AdjustSql($KeyValue), $sMasterFilter);
			} else {
				$bValidMasterRecord = FALSE;
			}
			$KeyValue = isset($rsnew['KampusID']) ? $rsnew['KampusID'] : $rsold['KampusID'];
			if (strval($KeyValue) <> "") {
				$sMasterFilter = str_replace("@KampusID@", ew_AdjustSql($KeyValue), $sMasterFilter);
			} else {
				$bValidMasterRecord = FALSE;
			}
			if ($bValidMasterRecord) {
				if (!isset($GLOBALS["kurikulum"])) $GLOBALS["kurikulum"] = new ckurikulum();
				$rsmaster = $GLOBALS["kurikulum"]->LoadRs($sMasterFilter);
				$bValidMasterRecord = ($rsmaster && !$rsmaster->EOF);
				$rsmaster->Close();
			}
			if (!$bValidMasterRecord) {
				$sRelatedRecordMsg = str_replace("%t", "kurikulum", $Language->Phrase("RelatedRecordRequired"));
				$this->setFailureMessage($sRelatedRecordMsg);
				$rs->Close();
				return FALSE;
			}

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

	// Set up master/detail based on QueryString
	function SetUpMasterParms() {
		$bValidMaster = FALSE;

		// Get the keys for master table
		if (isset($_GET[EW_TABLE_SHOW_MASTER])) {
			$sMasterTblVar = $_GET[EW_TABLE_SHOW_MASTER];
			if ($sMasterTblVar == "") {
				$bValidMaster = TRUE;
				$this->DbMasterFilter = "";
				$this->DbDetailFilter = "";
			}
			if ($sMasterTblVar == "kurikulum") {
				$bValidMaster = TRUE;
				if (@$_GET["fk_KurikulumID"] <> "") {
					$GLOBALS["kurikulum"]->KurikulumID->setQueryStringValue($_GET["fk_KurikulumID"]);
					$this->KurikulumID->setQueryStringValue($GLOBALS["kurikulum"]->KurikulumID->QueryStringValue);
					$this->KurikulumID->setSessionValue($this->KurikulumID->QueryStringValue);
					if (!is_numeric($GLOBALS["kurikulum"]->KurikulumID->QueryStringValue)) $bValidMaster = FALSE;
				} else {
					$bValidMaster = FALSE;
				}
				if (@$_GET["fk_ProdiID"] <> "") {
					$GLOBALS["kurikulum"]->ProdiID->setQueryStringValue($_GET["fk_ProdiID"]);
					$this->ProdiID->setQueryStringValue($GLOBALS["kurikulum"]->ProdiID->QueryStringValue);
					$this->ProdiID->setSessionValue($this->ProdiID->QueryStringValue);
				} else {
					$bValidMaster = FALSE;
				}
				if (@$_GET["fk_KampusID"] <> "") {
					$GLOBALS["kurikulum"]->KampusID->setQueryStringValue($_GET["fk_KampusID"]);
					$this->KampusID->setQueryStringValue($GLOBALS["kurikulum"]->KampusID->QueryStringValue);
					$this->KampusID->setSessionValue($this->KampusID->QueryStringValue);
				} else {
					$bValidMaster = FALSE;
				}
			}
		} elseif (isset($_POST[EW_TABLE_SHOW_MASTER])) {
			$sMasterTblVar = $_POST[EW_TABLE_SHOW_MASTER];
			if ($sMasterTblVar == "") {
				$bValidMaster = TRUE;
				$this->DbMasterFilter = "";
				$this->DbDetailFilter = "";
			}
			if ($sMasterTblVar == "kurikulum") {
				$bValidMaster = TRUE;
				if (@$_POST["fk_KurikulumID"] <> "") {
					$GLOBALS["kurikulum"]->KurikulumID->setFormValue($_POST["fk_KurikulumID"]);
					$this->KurikulumID->setFormValue($GLOBALS["kurikulum"]->KurikulumID->FormValue);
					$this->KurikulumID->setSessionValue($this->KurikulumID->FormValue);
					if (!is_numeric($GLOBALS["kurikulum"]->KurikulumID->FormValue)) $bValidMaster = FALSE;
				} else {
					$bValidMaster = FALSE;
				}
				if (@$_POST["fk_ProdiID"] <> "") {
					$GLOBALS["kurikulum"]->ProdiID->setFormValue($_POST["fk_ProdiID"]);
					$this->ProdiID->setFormValue($GLOBALS["kurikulum"]->ProdiID->FormValue);
					$this->ProdiID->setSessionValue($this->ProdiID->FormValue);
				} else {
					$bValidMaster = FALSE;
				}
				if (@$_POST["fk_KampusID"] <> "") {
					$GLOBALS["kurikulum"]->KampusID->setFormValue($_POST["fk_KampusID"]);
					$this->KampusID->setFormValue($GLOBALS["kurikulum"]->KampusID->FormValue);
					$this->KampusID->setSessionValue($this->KampusID->FormValue);
				} else {
					$bValidMaster = FALSE;
				}
			}
		}
		if ($bValidMaster) {

			// Save current master table
			$this->setCurrentMasterTable($sMasterTblVar);
			$this->setSessionWhere($this->GetDetailFilter());

			// Reset start record counter (new master key)
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);

			// Clear previous master key from Session
			if ($sMasterTblVar <> "kurikulum") {
				if ($this->KurikulumID->CurrentValue == "") $this->KurikulumID->setSessionValue("");
				if ($this->ProdiID->CurrentValue == "") $this->ProdiID->setSessionValue("");
				if ($this->KampusID->CurrentValue == "") $this->KampusID->setSessionValue("");
			}
		}
		$this->DbMasterFilter = $this->GetMasterFilter(); // Get master filter
		$this->DbDetailFilter = $this->GetDetailFilter(); // Get detail filter
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_mklist.php"), "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_KampusID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KampusID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
			$sWhereWrk = "";
			$this->KampusID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KampusID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KampusID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_ProdiID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProdiID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->ProdiID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProdiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `ProdiID` ASC";
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KurikulumID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KurikulumID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kurikulum`";
			$sWhereWrk = "{filter}";
			$this->KurikulumID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KurikulumID` = {filter_value}', "t0" => "3", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KurikulumID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_Tingkat":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT DISTINCT `Tingkat` AS `LinkFld`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Tingkat` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_Sesi":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Sesi` AS `LinkFld`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_sesi`";
			$sWhereWrk = "";
			$this->Sesi->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Sesi` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
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
if (!isset($master_mk_edit)) $master_mk_edit = new cmaster_mk_edit();

// Page init
$master_mk_edit->Page_Init();

// Page main
$master_mk_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_mk_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fmaster_mkedit = new ew_Form("fmaster_mkedit", "edit");

// Validate form
fmaster_mkedit.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_mk->ProdiID->FldCaption(), $master_mk->ProdiID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Nama");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_mk->Nama->FldCaption(), $master_mk->Nama->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Tingkat");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_mk->Tingkat->FldCaption(), $master_mk->Tingkat->ReqErrMsg)) ?>");

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
fmaster_mkedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_mkedit.ValidateRequired = true;
<?php } else { ?>
fmaster_mkedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_mkedit.Lists["x_KampusID"] = {"LinkField":"x_KampusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kampus"};
fmaster_mkedit.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":["x_KurikulumID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fmaster_mkedit.Lists["x_KurikulumID"] = {"LinkField":"x_KurikulumID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":["x_ProdiID"],"ChildFields":[],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"kurikulum"};
fmaster_mkedit.Lists["x_Tingkat"] = {"LinkField":"x_Tingkat","Ajax":true,"AutoFill":false,"DisplayFields":["x_Tingkat","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"kelas"};
fmaster_mkedit.Lists["x_Sesi"] = {"LinkField":"x_Sesi","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaSesi","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_sesi"};
fmaster_mkedit.Lists["x_Wajib"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_mkedit.Lists["x_Wajib"].Options = <?php echo json_encode($master_mk->Wajib->Options()) ?>;
fmaster_mkedit.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_mkedit.Lists["x_NA"].Options = <?php echo json_encode($master_mk->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$master_mk_edit->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_mk_edit->ShowPageHeader(); ?>
<?php
$master_mk_edit->ShowMessage();
?>
<form name="fmaster_mkedit" id="fmaster_mkedit" class="<?php echo $master_mk_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_mk_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_mk_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_mk">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<?php if ($master_mk_edit->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if ($master_mk->getCurrentMasterTable() == "kurikulum") { ?>
<input type="hidden" name="<?php echo EW_TABLE_SHOW_MASTER ?>" value="kurikulum">
<input type="hidden" name="fk_KurikulumID" value="<?php echo $master_mk->KurikulumID->getSessionValue() ?>">
<input type="hidden" name="fk_ProdiID" value="<?php echo $master_mk->ProdiID->getSessionValue() ?>">
<input type="hidden" name="fk_KampusID" value="<?php echo $master_mk->KampusID->getSessionValue() ?>">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_mk_edit->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_mkedit" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_mk->MKID->Visible) { // MKID ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_MKID" class="form-group">
		<label id="elh_master_mk_MKID" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->MKID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->MKID->CellAttributes() ?>>
<span id="el_master_mk_MKID">
<span<?php echo $master_mk->MKID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->MKID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="master_mk" data-field="x_MKID" name="x_MKID" id="x_MKID" value="<?php echo ew_HtmlEncode($master_mk->MKID->CurrentValue) ?>">
<?php echo $master_mk->MKID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_MKID">
		<td><span id="elh_master_mk_MKID"><?php echo $master_mk->MKID->FldCaption() ?></span></td>
		<td<?php echo $master_mk->MKID->CellAttributes() ?>>
<span id="el_master_mk_MKID">
<span<?php echo $master_mk->MKID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->MKID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="master_mk" data-field="x_MKID" name="x_MKID" id="x_MKID" value="<?php echo ew_HtmlEncode($master_mk->MKID->CurrentValue) ?>">
<?php echo $master_mk->MKID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->KampusID->Visible) { // KampusID ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_KampusID" class="form-group">
		<label id="elh_master_mk_KampusID" for="x_KampusID" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->KampusID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->KampusID->CellAttributes() ?>>
<?php if ($master_mk->KampusID->getSessionValue() <> "") { ?>
<span id="el_master_mk_KampusID">
<span<?php echo $master_mk->KampusID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->KampusID->ViewValue ?></p></span>
</span>
<input type="hidden" id="x_KampusID" name="x_KampusID" value="<?php echo ew_HtmlEncode($master_mk->KampusID->CurrentValue) ?>">
<?php } else { ?>
<span id="el_master_mk_KampusID">
<select data-table="master_mk" data-field="x_KampusID" data-value-separator="<?php echo $master_mk->KampusID->DisplayValueSeparatorAttribute() ?>" id="x_KampusID" name="x_KampusID"<?php echo $master_mk->KampusID->EditAttributes() ?>>
<?php echo $master_mk->KampusID->SelectOptionListHtml("x_KampusID") ?>
</select>
<input type="hidden" name="s_x_KampusID" id="s_x_KampusID" value="<?php echo $master_mk->KampusID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php echo $master_mk->KampusID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KampusID">
		<td><span id="elh_master_mk_KampusID"><?php echo $master_mk->KampusID->FldCaption() ?></span></td>
		<td<?php echo $master_mk->KampusID->CellAttributes() ?>>
<?php if ($master_mk->KampusID->getSessionValue() <> "") { ?>
<span id="el_master_mk_KampusID">
<span<?php echo $master_mk->KampusID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->KampusID->ViewValue ?></p></span>
</span>
<input type="hidden" id="x_KampusID" name="x_KampusID" value="<?php echo ew_HtmlEncode($master_mk->KampusID->CurrentValue) ?>">
<?php } else { ?>
<span id="el_master_mk_KampusID">
<select data-table="master_mk" data-field="x_KampusID" data-value-separator="<?php echo $master_mk->KampusID->DisplayValueSeparatorAttribute() ?>" id="x_KampusID" name="x_KampusID"<?php echo $master_mk->KampusID->EditAttributes() ?>>
<?php echo $master_mk->KampusID->SelectOptionListHtml("x_KampusID") ?>
</select>
<input type="hidden" name="s_x_KampusID" id="s_x_KampusID" value="<?php echo $master_mk->KampusID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php echo $master_mk->KampusID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label id="elh_master_mk_ProdiID" for="x_ProdiID" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->ProdiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->ProdiID->CellAttributes() ?>>
<?php if ($master_mk->ProdiID->getSessionValue() <> "") { ?>
<span id="el_master_mk_ProdiID">
<span<?php echo $master_mk->ProdiID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->ProdiID->ViewValue ?></p></span>
</span>
<input type="hidden" id="x_ProdiID" name="x_ProdiID" value="<?php echo ew_HtmlEncode($master_mk->ProdiID->CurrentValue) ?>">
<?php } else { ?>
<span id="el_master_mk_ProdiID">
<?php $master_mk->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$master_mk->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="master_mk" data-field="x_ProdiID" data-value-separator="<?php echo $master_mk->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $master_mk->ProdiID->EditAttributes() ?>>
<?php echo $master_mk->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $master_mk->ProdiID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php echo $master_mk->ProdiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_master_mk_ProdiID"><?php echo $master_mk->ProdiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_mk->ProdiID->CellAttributes() ?>>
<?php if ($master_mk->ProdiID->getSessionValue() <> "") { ?>
<span id="el_master_mk_ProdiID">
<span<?php echo $master_mk->ProdiID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->ProdiID->ViewValue ?></p></span>
</span>
<input type="hidden" id="x_ProdiID" name="x_ProdiID" value="<?php echo ew_HtmlEncode($master_mk->ProdiID->CurrentValue) ?>">
<?php } else { ?>
<span id="el_master_mk_ProdiID">
<?php $master_mk->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$master_mk->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="master_mk" data-field="x_ProdiID" data-value-separator="<?php echo $master_mk->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $master_mk->ProdiID->EditAttributes() ?>>
<?php echo $master_mk->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $master_mk->ProdiID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php echo $master_mk->ProdiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->KurikulumID->Visible) { // KurikulumID ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_KurikulumID" class="form-group">
		<label id="elh_master_mk_KurikulumID" for="x_KurikulumID" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->KurikulumID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->KurikulumID->CellAttributes() ?>>
<?php if ($master_mk->KurikulumID->getSessionValue() <> "") { ?>
<span id="el_master_mk_KurikulumID">
<span<?php echo $master_mk->KurikulumID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->KurikulumID->ViewValue ?></p></span>
</span>
<input type="hidden" id="x_KurikulumID" name="x_KurikulumID" value="<?php echo ew_HtmlEncode($master_mk->KurikulumID->CurrentValue) ?>">
<?php } else { ?>
<span id="el_master_mk_KurikulumID">
<select data-table="master_mk" data-field="x_KurikulumID" data-value-separator="<?php echo $master_mk->KurikulumID->DisplayValueSeparatorAttribute() ?>" id="x_KurikulumID" name="x_KurikulumID"<?php echo $master_mk->KurikulumID->EditAttributes() ?>>
<?php echo $master_mk->KurikulumID->SelectOptionListHtml("x_KurikulumID") ?>
</select>
<input type="hidden" name="s_x_KurikulumID" id="s_x_KurikulumID" value="<?php echo $master_mk->KurikulumID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php echo $master_mk->KurikulumID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KurikulumID">
		<td><span id="elh_master_mk_KurikulumID"><?php echo $master_mk->KurikulumID->FldCaption() ?></span></td>
		<td<?php echo $master_mk->KurikulumID->CellAttributes() ?>>
<?php if ($master_mk->KurikulumID->getSessionValue() <> "") { ?>
<span id="el_master_mk_KurikulumID">
<span<?php echo $master_mk->KurikulumID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->KurikulumID->ViewValue ?></p></span>
</span>
<input type="hidden" id="x_KurikulumID" name="x_KurikulumID" value="<?php echo ew_HtmlEncode($master_mk->KurikulumID->CurrentValue) ?>">
<?php } else { ?>
<span id="el_master_mk_KurikulumID">
<select data-table="master_mk" data-field="x_KurikulumID" data-value-separator="<?php echo $master_mk->KurikulumID->DisplayValueSeparatorAttribute() ?>" id="x_KurikulumID" name="x_KurikulumID"<?php echo $master_mk->KurikulumID->EditAttributes() ?>>
<?php echo $master_mk->KurikulumID->SelectOptionListHtml("x_KurikulumID") ?>
</select>
<input type="hidden" name="s_x_KurikulumID" id="s_x_KurikulumID" value="<?php echo $master_mk->KurikulumID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php echo $master_mk->KurikulumID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->MKKode->Visible) { // MKKode ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_MKKode" class="form-group">
		<label id="elh_master_mk_MKKode" for="x_MKKode" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->MKKode->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->MKKode->CellAttributes() ?>>
<span id="el_master_mk_MKKode">
<input type="text" data-table="master_mk" data-field="x_MKKode" name="x_MKKode" id="x_MKKode" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->MKKode->getPlaceHolder()) ?>" value="<?php echo $master_mk->MKKode->EditValue ?>"<?php echo $master_mk->MKKode->EditAttributes() ?>>
</span>
<?php echo $master_mk->MKKode->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_MKKode">
		<td><span id="elh_master_mk_MKKode"><?php echo $master_mk->MKKode->FldCaption() ?></span></td>
		<td<?php echo $master_mk->MKKode->CellAttributes() ?>>
<span id="el_master_mk_MKKode">
<input type="text" data-table="master_mk" data-field="x_MKKode" name="x_MKKode" id="x_MKKode" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->MKKode->getPlaceHolder()) ?>" value="<?php echo $master_mk->MKKode->EditValue ?>"<?php echo $master_mk->MKKode->EditAttributes() ?>>
</span>
<?php echo $master_mk->MKKode->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label id="elh_master_mk_Nama" for="x_Nama" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->Nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->Nama->CellAttributes() ?>>
<span id="el_master_mk_Nama">
<input type="text" data-table="master_mk" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_mk->Nama->getPlaceHolder()) ?>" value="<?php echo $master_mk->Nama->EditValue ?>"<?php echo $master_mk->Nama->EditAttributes() ?>>
</span>
<?php echo $master_mk->Nama->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_master_mk_Nama"><?php echo $master_mk->Nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_mk->Nama->CellAttributes() ?>>
<span id="el_master_mk_Nama">
<input type="text" data-table="master_mk" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_mk->Nama->getPlaceHolder()) ?>" value="<?php echo $master_mk->Nama->EditValue ?>"<?php echo $master_mk->Nama->EditAttributes() ?>>
</span>
<?php echo $master_mk->Nama->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Singkatan->Visible) { // Singkatan ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_Singkatan" class="form-group">
		<label id="elh_master_mk_Singkatan" for="x_Singkatan" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->Singkatan->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->Singkatan->CellAttributes() ?>>
<span id="el_master_mk_Singkatan">
<input type="text" data-table="master_mk" data-field="x_Singkatan" name="x_Singkatan" id="x_Singkatan" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->Singkatan->getPlaceHolder()) ?>" value="<?php echo $master_mk->Singkatan->EditValue ?>"<?php echo $master_mk->Singkatan->EditAttributes() ?>>
</span>
<?php echo $master_mk->Singkatan->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Singkatan">
		<td><span id="elh_master_mk_Singkatan"><?php echo $master_mk->Singkatan->FldCaption() ?></span></td>
		<td<?php echo $master_mk->Singkatan->CellAttributes() ?>>
<span id="el_master_mk_Singkatan">
<input type="text" data-table="master_mk" data-field="x_Singkatan" name="x_Singkatan" id="x_Singkatan" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->Singkatan->getPlaceHolder()) ?>" value="<?php echo $master_mk->Singkatan->EditValue ?>"<?php echo $master_mk->Singkatan->EditAttributes() ?>>
</span>
<?php echo $master_mk->Singkatan->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Tingkat->Visible) { // Tingkat ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_Tingkat" class="form-group">
		<label id="elh_master_mk_Tingkat" for="x_Tingkat" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->Tingkat->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->Tingkat->CellAttributes() ?>>
<span id="el_master_mk_Tingkat">
<select data-table="master_mk" data-field="x_Tingkat" data-value-separator="<?php echo $master_mk->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $master_mk->Tingkat->EditAttributes() ?>>
<?php echo $master_mk->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $master_mk->Tingkat->LookupFilterQuery() ?>">
</span>
<?php echo $master_mk->Tingkat->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tingkat">
		<td><span id="elh_master_mk_Tingkat"><?php echo $master_mk->Tingkat->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_mk->Tingkat->CellAttributes() ?>>
<span id="el_master_mk_Tingkat">
<select data-table="master_mk" data-field="x_Tingkat" data-value-separator="<?php echo $master_mk->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $master_mk->Tingkat->EditAttributes() ?>>
<?php echo $master_mk->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $master_mk->Tingkat->LookupFilterQuery() ?>">
</span>
<?php echo $master_mk->Tingkat->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Sesi->Visible) { // Sesi ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_Sesi" class="form-group">
		<label id="elh_master_mk_Sesi" for="x_Sesi" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->Sesi->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->Sesi->CellAttributes() ?>>
<span id="el_master_mk_Sesi">
<select data-table="master_mk" data-field="x_Sesi" data-value-separator="<?php echo $master_mk->Sesi->DisplayValueSeparatorAttribute() ?>" id="x_Sesi" name="x_Sesi"<?php echo $master_mk->Sesi->EditAttributes() ?>>
<?php echo $master_mk->Sesi->SelectOptionListHtml("x_Sesi") ?>
</select>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $master_mk->Sesi->LookupFilterQuery() ?>">
</span>
<?php echo $master_mk->Sesi->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Sesi">
		<td><span id="elh_master_mk_Sesi"><?php echo $master_mk->Sesi->FldCaption() ?></span></td>
		<td<?php echo $master_mk->Sesi->CellAttributes() ?>>
<span id="el_master_mk_Sesi">
<select data-table="master_mk" data-field="x_Sesi" data-value-separator="<?php echo $master_mk->Sesi->DisplayValueSeparatorAttribute() ?>" id="x_Sesi" name="x_Sesi"<?php echo $master_mk->Sesi->EditAttributes() ?>>
<?php echo $master_mk->Sesi->SelectOptionListHtml("x_Sesi") ?>
</select>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $master_mk->Sesi->LookupFilterQuery() ?>">
</span>
<?php echo $master_mk->Sesi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Wajib->Visible) { // Wajib ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_Wajib" class="form-group">
		<label id="elh_master_mk_Wajib" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->Wajib->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->Wajib->CellAttributes() ?>>
<span id="el_master_mk_Wajib">
<div id="tp_x_Wajib" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_Wajib" data-value-separator="<?php echo $master_mk->Wajib->DisplayValueSeparatorAttribute() ?>" name="x_Wajib" id="x_Wajib" value="{value}"<?php echo $master_mk->Wajib->EditAttributes() ?>></div>
<div id="dsl_x_Wajib" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->Wajib->RadioButtonListHtml(FALSE, "x_Wajib") ?>
</div></div>
</span>
<?php echo $master_mk->Wajib->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Wajib">
		<td><span id="elh_master_mk_Wajib"><?php echo $master_mk->Wajib->FldCaption() ?></span></td>
		<td<?php echo $master_mk->Wajib->CellAttributes() ?>>
<span id="el_master_mk_Wajib">
<div id="tp_x_Wajib" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_Wajib" data-value-separator="<?php echo $master_mk->Wajib->DisplayValueSeparatorAttribute() ?>" name="x_Wajib" id="x_Wajib" value="{value}"<?php echo $master_mk->Wajib->EditAttributes() ?>></div>
<div id="dsl_x_Wajib" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->Wajib->RadioButtonListHtml(FALSE, "x_Wajib") ?>
</div></div>
</span>
<?php echo $master_mk->Wajib->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Deskripsi->Visible) { // Deskripsi ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_Deskripsi" class="form-group">
		<label id="elh_master_mk_Deskripsi" for="x_Deskripsi" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->Deskripsi->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->Deskripsi->CellAttributes() ?>>
<span id="el_master_mk_Deskripsi">
<textarea data-table="master_mk" data-field="x_Deskripsi" name="x_Deskripsi" id="x_Deskripsi" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($master_mk->Deskripsi->getPlaceHolder()) ?>"<?php echo $master_mk->Deskripsi->EditAttributes() ?>><?php echo $master_mk->Deskripsi->EditValue ?></textarea>
</span>
<?php echo $master_mk->Deskripsi->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Deskripsi">
		<td><span id="elh_master_mk_Deskripsi"><?php echo $master_mk->Deskripsi->FldCaption() ?></span></td>
		<td<?php echo $master_mk->Deskripsi->CellAttributes() ?>>
<span id="el_master_mk_Deskripsi">
<textarea data-table="master_mk" data-field="x_Deskripsi" name="x_Deskripsi" id="x_Deskripsi" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($master_mk->Deskripsi->getPlaceHolder()) ?>"<?php echo $master_mk->Deskripsi->EditAttributes() ?>><?php echo $master_mk->Deskripsi->EditValue ?></textarea>
</span>
<?php echo $master_mk->Deskripsi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_master_mk_NA" class="col-sm-2 control-label ewLabel"><?php echo $master_mk->NA->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_mk->NA->CellAttributes() ?>>
<span id="el_master_mk_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_NA" data-value-separator="<?php echo $master_mk->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_mk->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $master_mk->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_master_mk_NA"><?php echo $master_mk->NA->FldCaption() ?></span></td>
		<td<?php echo $master_mk->NA->CellAttributes() ?>>
<span id="el_master_mk_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_NA" data-value-separator="<?php echo $master_mk->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_mk->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $master_mk->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_mk_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$master_mk_edit->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $master_mk_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_mkedit.Init();
</script>
<?php
$master_mk_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_mk_edit->Page_Terminate();
?>
