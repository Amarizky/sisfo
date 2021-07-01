<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "khsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$khs_add = NULL; // Initialize page object first

class ckhs_add extends ckhs {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'khs';

	// Page object name
	var $PageObjName = 'khs_add';

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

		// Table object (khs)
		if (!isset($GLOBALS["khs"]) || get_class($GLOBALS["khs"]) == "ckhs") {
			$GLOBALS["khs"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["khs"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'khs', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("khslist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->ProdiID->SetVisibility();
		$this->TahunID->SetVisibility();
		$this->Sesi->SetVisibility();
		$this->Tingkat->SetVisibility();
		$this->Kelas->SetVisibility();
		$this->StudentID->SetVisibility();
		$this->StatusStudentID->SetVisibility();
		$this->Keterangan->SetVisibility();
		$this->Creator->SetVisibility();
		$this->CreateDate->SetVisibility();
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
		global $EW_EXPORT, $khs;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($khs);
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
			if (@$_GET["KHSID"] != "") {
				$this->KHSID->setQueryStringValue($_GET["KHSID"]);
				$this->setKey("KHSID", $this->KHSID->CurrentValue); // Set up key
			} else {
				$this->setKey("KHSID", ""); // Clear key
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
					$this->Page_Terminate("khslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "khslist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "khsview.php")
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
		$this->TahunID->CurrentValue = NULL;
		$this->TahunID->OldValue = $this->TahunID->CurrentValue;
		$this->Sesi->CurrentValue = 0;
		$this->Tingkat->CurrentValue = NULL;
		$this->Tingkat->OldValue = $this->Tingkat->CurrentValue;
		$this->Kelas->CurrentValue = NULL;
		$this->Kelas->OldValue = $this->Kelas->CurrentValue;
		$this->StudentID->CurrentValue = NULL;
		$this->StudentID->OldValue = $this->StudentID->CurrentValue;
		$this->StatusStudentID->CurrentValue = "A";
		$this->Keterangan->CurrentValue = NULL;
		$this->Keterangan->OldValue = $this->Keterangan->CurrentValue;
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
		if (!$this->TahunID->FldIsDetailKey) {
			$this->TahunID->setFormValue($objForm->GetValue("x_TahunID"));
		}
		if (!$this->Sesi->FldIsDetailKey) {
			$this->Sesi->setFormValue($objForm->GetValue("x_Sesi"));
		}
		if (!$this->Tingkat->FldIsDetailKey) {
			$this->Tingkat->setFormValue($objForm->GetValue("x_Tingkat"));
		}
		if (!$this->Kelas->FldIsDetailKey) {
			$this->Kelas->setFormValue($objForm->GetValue("x_Kelas"));
		}
		if (!$this->StudentID->FldIsDetailKey) {
			$this->StudentID->setFormValue($objForm->GetValue("x_StudentID"));
		}
		if (!$this->StatusStudentID->FldIsDetailKey) {
			$this->StatusStudentID->setFormValue($objForm->GetValue("x_StatusStudentID"));
		}
		if (!$this->Keterangan->FldIsDetailKey) {
			$this->Keterangan->setFormValue($objForm->GetValue("x_Keterangan"));
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
		$this->TahunID->CurrentValue = $this->TahunID->FormValue;
		$this->Sesi->CurrentValue = $this->Sesi->FormValue;
		$this->Tingkat->CurrentValue = $this->Tingkat->FormValue;
		$this->Kelas->CurrentValue = $this->Kelas->FormValue;
		$this->StudentID->CurrentValue = $this->StudentID->FormValue;
		$this->StatusStudentID->CurrentValue = $this->StatusStudentID->FormValue;
		$this->Keterangan->CurrentValue = $this->Keterangan->FormValue;
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
		$this->KHSID->setDbValue($rs->fields('KHSID'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->TahunID->setDbValue($rs->fields('TahunID'));
		$this->Sesi->setDbValue($rs->fields('Sesi'));
		$this->Tingkat->setDbValue($rs->fields('Tingkat'));
		$this->Kelas->setDbValue($rs->fields('Kelas'));
		$this->StudentID->setDbValue($rs->fields('StudentID'));
		$this->StatusStudentID->setDbValue($rs->fields('StatusStudentID'));
		$this->Keterangan->setDbValue($rs->fields('Keterangan'));
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
		$this->KHSID->DbValue = $row['KHSID'];
		$this->ProdiID->DbValue = $row['ProdiID'];
		$this->TahunID->DbValue = $row['TahunID'];
		$this->Sesi->DbValue = $row['Sesi'];
		$this->Tingkat->DbValue = $row['Tingkat'];
		$this->Kelas->DbValue = $row['Kelas'];
		$this->StudentID->DbValue = $row['StudentID'];
		$this->StatusStudentID->DbValue = $row['StatusStudentID'];
		$this->Keterangan->DbValue = $row['Keterangan'];
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
		if (strval($this->getKey("KHSID")) <> "")
			$this->KHSID->CurrentValue = $this->getKey("KHSID"); // KHSID
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
		// KHSID
		// ProdiID
		// TahunID
		// Sesi
		// Tingkat
		// Kelas
		// StudentID
		// StatusStudentID
		// Keterangan
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// KHSID
		$this->KHSID->ViewValue = $this->KHSID->CurrentValue;
		$this->KHSID->ViewCustomAttributes = "";

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

		// TahunID
		if (strval($this->TahunID->CurrentValue) <> "") {
			$sFilterWrk = "`TahunID`" . ew_SearchString("=", $this->TahunID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `TahunID`, `TahunID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tahun`";
		$sWhereWrk = "";
		$this->TahunID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->TahunID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `TahunID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->TahunID->ViewValue = $this->TahunID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
			}
		} else {
			$this->TahunID->ViewValue = NULL;
		}
		$this->TahunID->ViewCustomAttributes = "";

		// Sesi
		if (strval($this->Sesi->CurrentValue) <> "") {
			$arwrk = explode(",", $this->Sesi->CurrentValue);
			$sFilterWrk = "";
			foreach ($arwrk as $wrk) {
				if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
				$sFilterWrk .= "`Sesi`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_NUMBER, "");
			}
		$sSqlWrk = "SELECT `Sesi`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_sesi`";
		$sWhereWrk = "";
		$this->Sesi->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->Sesi->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->Sesi->ViewValue .= $this->Sesi->DisplayValue($arwrk);
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->Sesi->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
				$rswrk->Close();
			} else {
				$this->Sesi->ViewValue = $this->Sesi->CurrentValue;
			}
		} else {
			$this->Sesi->ViewValue = NULL;
		}
		$this->Sesi->ViewCustomAttributes = "";

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

		// Kelas
		if (strval($this->Kelas->CurrentValue) <> "") {
			$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->Kelas->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
		$sWhereWrk = "";
		$this->Kelas->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Kelas, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Kelas->ViewValue = $this->Kelas->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Kelas->ViewValue = $this->Kelas->CurrentValue;
			}
		} else {
			$this->Kelas->ViewValue = NULL;
		}
		$this->Kelas->CssStyle = "font-weight: bold;";
		$this->Kelas->ViewCustomAttributes = "";

		// StudentID
		if (strval($this->StudentID->CurrentValue) <> "") {
			$sFilterWrk = "`StudentID`" . ew_SearchString("=", $this->StudentID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StudentID`, `StudentID` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `student`";
		$sWhereWrk = "";
		$this->StudentID->LookupFilters = array("dx1" => '`StudentID`', "dx2" => '`Nama`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StudentID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->StudentID->ViewValue = $this->StudentID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StudentID->ViewValue = $this->StudentID->CurrentValue;
			}
		} else {
			$this->StudentID->ViewValue = NULL;
		}
		$this->StudentID->CssStyle = "font-weight: bold;";
		$this->StudentID->ViewCustomAttributes = "";

		// StatusStudentID
		if (strval($this->StatusStudentID->CurrentValue) <> "") {
			$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StatusStudentID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statusstudent`";
		$sWhereWrk = "";
		$this->StatusStudentID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StatusStudentID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StatusStudentID->ViewValue = $this->StatusStudentID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StatusStudentID->ViewValue = $this->StatusStudentID->CurrentValue;
			}
		} else {
			$this->StatusStudentID->ViewValue = NULL;
		}
		$this->StatusStudentID->ViewCustomAttributes = "";

		// Keterangan
		$this->Keterangan->ViewValue = $this->Keterangan->CurrentValue;
		$this->Keterangan->ViewCustomAttributes = "";

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

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";
			$this->TahunID->TooltipValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";
			$this->Sesi->TooltipValue = "";

			// Tingkat
			$this->Tingkat->LinkCustomAttributes = "";
			$this->Tingkat->HrefValue = "";
			$this->Tingkat->TooltipValue = "";

			// Kelas
			$this->Kelas->LinkCustomAttributes = "";
			$this->Kelas->HrefValue = "";
			$this->Kelas->TooltipValue = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";
			$this->StudentID->TooltipValue = "";

			// StatusStudentID
			$this->StatusStudentID->LinkCustomAttributes = "";
			$this->StatusStudentID->HrefValue = "";
			$this->StatusStudentID->TooltipValue = "";

			// Keterangan
			$this->Keterangan->LinkCustomAttributes = "";
			$this->Keterangan->HrefValue = "";
			$this->Keterangan->TooltipValue = "";

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

			// TahunID
			$this->TahunID->EditCustomAttributes = "";
			if (trim(strval($this->TahunID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`TahunID`" . ew_SearchString("=", $this->TahunID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `TahunID`, `TahunID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `tahun`";
			$sWhereWrk = "";
			$this->TahunID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->TahunID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `TahunID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$this->TahunID->ViewValue = $this->TahunID->DisplayValue($arwrk);
			} else {
				$this->TahunID->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->TahunID->EditValue = $arwrk;

			// Sesi
			$this->Sesi->EditCustomAttributes = "";
			if (trim(strval($this->Sesi->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$arwrk = explode(",", $this->Sesi->CurrentValue);
				$sFilterWrk = "";
				foreach ($arwrk as $wrk) {
					if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
					$sFilterWrk .= "`Sesi`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_NUMBER, "");
				}
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

			// Tingkat
			$this->Tingkat->EditAttrs["class"] = "form-control";
			$this->Tingkat->EditCustomAttributes = "";
			if (trim(strval($this->Tingkat->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Tingkat`" . ew_SearchString("=", $this->Tingkat->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Tingkat->EditValue = $arwrk;

			// Kelas
			$this->Kelas->EditAttrs["class"] = "form-control";
			$this->Kelas->EditCustomAttributes = "";
			if (trim(strval($this->Kelas->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->Kelas->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, `Tingkat` AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Kelas->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Kelas, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Kelas->EditValue = $arwrk;

			// StudentID
			$this->StudentID->EditCustomAttributes = "";
			if (trim(strval($this->StudentID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StudentID`" . ew_SearchString("=", $this->StudentID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StudentID`, `StudentID` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `student`";
			$sWhereWrk = "";
			$this->StudentID->LookupFilters = array("dx1" => '`StudentID`', "dx2" => '`Nama`');
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StudentID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
				$this->StudentID->ViewValue = $this->StudentID->DisplayValue($arwrk);
			} else {
				$this->StudentID->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StudentID->EditValue = $arwrk;

			// StatusStudentID
			$this->StatusStudentID->EditAttrs["class"] = "form-control";
			$this->StatusStudentID->EditCustomAttributes = "";
			if (trim(strval($this->StatusStudentID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StatusStudentID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_statusstudent`";
			$sWhereWrk = "";
			$this->StatusStudentID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StatusStudentID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StatusStudentID->EditValue = $arwrk;

			// Keterangan
			$this->Keterangan->EditAttrs["class"] = "form-control";
			$this->Keterangan->EditCustomAttributes = "";
			$this->Keterangan->EditValue = ew_HtmlEncode($this->Keterangan->CurrentValue);
			$this->Keterangan->PlaceHolder = ew_RemoveHtml($this->Keterangan->FldCaption());

			// Creator
			// CreateDate
			// NA

			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// ProdiID

			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";

			// Tingkat
			$this->Tingkat->LinkCustomAttributes = "";
			$this->Tingkat->HrefValue = "";

			// Kelas
			$this->Kelas->LinkCustomAttributes = "";
			$this->Kelas->HrefValue = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";

			// StatusStudentID
			$this->StatusStudentID->LinkCustomAttributes = "";
			$this->StatusStudentID->HrefValue = "";

			// Keterangan
			$this->Keterangan->LinkCustomAttributes = "";
			$this->Keterangan->HrefValue = "";

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
		if (!$this->TahunID->FldIsDetailKey && !is_null($this->TahunID->FormValue) && $this->TahunID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TahunID->FldCaption(), $this->TahunID->ReqErrMsg));
		}
		if ($this->Sesi->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Sesi->FldCaption(), $this->Sesi->ReqErrMsg));
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

		// TahunID
		$this->TahunID->SetDbValueDef($rsnew, $this->TahunID->CurrentValue, "", FALSE);

		// Sesi
		$this->Sesi->SetDbValueDef($rsnew, $this->Sesi->CurrentValue, "", strval($this->Sesi->CurrentValue) == "");

		// Tingkat
		$this->Tingkat->SetDbValueDef($rsnew, $this->Tingkat->CurrentValue, NULL, FALSE);

		// Kelas
		$this->Kelas->SetDbValueDef($rsnew, $this->Kelas->CurrentValue, NULL, FALSE);

		// StudentID
		$this->StudentID->SetDbValueDef($rsnew, $this->StudentID->CurrentValue, NULL, FALSE);

		// StatusStudentID
		$this->StatusStudentID->SetDbValueDef($rsnew, $this->StatusStudentID->CurrentValue, NULL, strval($this->StatusStudentID->CurrentValue) == "");

		// Keterangan
		$this->Keterangan->SetDbValueDef($rsnew, $this->Keterangan->CurrentValue, NULL, FALSE);

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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("khslist.php"), "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
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
		case "x_TahunID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `TahunID` AS `LinkFld`, `TahunID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tahun`";
			$sWhereWrk = "{filter}";
			$this->TahunID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`TahunID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TahunID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `TahunID` ASC";
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
		case "x_Tingkat":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT DISTINCT `Tingkat` AS `LinkFld`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
			$sWhereWrk = "{filter}";
			$this->Tingkat->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Tingkat` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_Kelas":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KelasID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
			$sWhereWrk = "{filter}";
			$this->Kelas->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KelasID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "", "f2" => '`Tingkat` IN ({filter_value})', "t2" => "200", "fn2" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Kelas, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StudentID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StudentID` AS `LinkFld`, `StudentID` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `student`";
			$sWhereWrk = "{filter}";
			$this->StudentID->LookupFilters = array("dx1" => '`StudentID`', "dx2" => '`Nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StudentID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StudentID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StatusStudentID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StatusStudentID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statusstudent`";
			$sWhereWrk = "";
			$this->StatusStudentID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StatusStudentID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StatusStudentID, $sWhereWrk); // Call Lookup selecting
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
if (!isset($khs_add)) $khs_add = new ckhs_add();

// Page init
$khs_add->Page_Init();

// Page main
$khs_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$khs_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fkhsadd = new ew_Form("fkhsadd", "add");

// Validate form
fkhsadd.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $khs->ProdiID->FldCaption(), $khs->ProdiID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TahunID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $khs->TahunID->FldCaption(), $khs->TahunID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Sesi[]");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $khs->Sesi->FldCaption(), $khs->Sesi->ReqErrMsg)) ?>");

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
fkhsadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fkhsadd.ValidateRequired = true;
<?php } else { ?>
fkhsadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fkhsadd.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":["x_TahunID","x_Tingkat","x_Kelas","x_StudentID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fkhsadd.Lists["x_TahunID"] = {"LinkField":"x_TahunID","Ajax":true,"AutoFill":true,"DisplayFields":["x_TahunID","","",""],"ParentFields":["x_ProdiID"],"ChildFields":[],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"tahun"};
fkhsadd.Lists["x_Sesi[]"] = {"LinkField":"x_Sesi","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaSesi","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_sesi"};
fkhsadd.Lists["x_Tingkat"] = {"LinkField":"x_Tingkat","Ajax":true,"AutoFill":false,"DisplayFields":["x_Tingkat","","",""],"ParentFields":["x_ProdiID"],"ChildFields":["x_Kelas"],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"kelas"};
fkhsadd.Lists["x_Kelas"] = {"LinkField":"x_KelasID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":["x_ProdiID","x_Tingkat"],"ChildFields":[],"FilterFields":["x_ProdiID","x_Tingkat"],"Options":[],"Template":"","LinkTable":"kelas"};
fkhsadd.Lists["x_StudentID"] = {"LinkField":"x_StudentID","Ajax":true,"AutoFill":false,"DisplayFields":["x_StudentID","x_Nama","",""],"ParentFields":["x_ProdiID"],"ChildFields":[],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"student"};
fkhsadd.Lists["x_StatusStudentID"] = {"LinkField":"x_StatusStudentID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statusstudent"};
fkhsadd.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkhsadd.Lists["x_NA"].Options = <?php echo json_encode($khs->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$khs_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $khs_add->ShowPageHeader(); ?>
<?php
$khs_add->ShowMessage();
?>
<form name="fkhsadd" id="fkhsadd" class="<?php echo $khs_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($khs_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $khs_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="khs">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($khs_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$khs_add->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $khs_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_khsadd" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($khs->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $khs_add->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label id="elh_khs_ProdiID" for="x_ProdiID" class="col-sm-2 control-label ewLabel"><?php echo $khs->ProdiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $khs->ProdiID->CellAttributes() ?>>
<span id="el_khs_ProdiID">
<?php $khs->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_ProdiID" data-value-separator="<?php echo $khs->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $khs->ProdiID->EditAttributes() ?>>
<?php echo $khs->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $khs->ProdiID->LookupFilterQuery() ?>">
</span>
<?php echo $khs->ProdiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_khs_ProdiID"><?php echo $khs->ProdiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $khs->ProdiID->CellAttributes() ?>>
<span id="el_khs_ProdiID">
<?php $khs->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_ProdiID" data-value-separator="<?php echo $khs->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $khs->ProdiID->EditAttributes() ?>>
<?php echo $khs->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $khs->ProdiID->LookupFilterQuery() ?>">
</span>
<?php echo $khs->ProdiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->TahunID->Visible) { // TahunID ?>
<?php if (ew_IsMobile() || $khs_add->IsModal) { ?>
	<div id="r_TahunID" class="form-group">
		<label id="elh_khs_TahunID" for="x_TahunID" class="col-sm-2 control-label ewLabel"><?php echo $khs->TahunID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $khs->TahunID->CellAttributes() ?>>
<span id="el_khs_TahunID">
<?php $khs->TahunID->EditAttrs["onclick"] = "ew_AutoFill(this); " . @$khs->TahunID->EditAttrs["onclick"]; ?>
<div class="ewDropdownList has-feedback">
	<span onclick="" class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $khs->TahunID->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x_TahunID" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php echo $khs->TahunID->RadioButtonListHtml(TRUE, "x_TahunID") ?>
		</div>
	</div>
	<div id="tp_x_TahunID" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_TahunID" data-value-separator="<?php echo $khs->TahunID->DisplayValueSeparatorAttribute() ?>" name="x_TahunID" id="x_TahunID" value="{value}"<?php echo $khs->TahunID->EditAttributes() ?>></div>
</div>
<input type="hidden" name="s_x_TahunID" id="s_x_TahunID" value="<?php echo $khs->TahunID->LookupFilterQuery() ?>">
<input type="hidden" name="ln_x_TahunID" id="ln_x_TahunID" value="x_Sesi[]">
</span>
<?php echo $khs->TahunID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TahunID">
		<td><span id="elh_khs_TahunID"><?php echo $khs->TahunID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $khs->TahunID->CellAttributes() ?>>
<span id="el_khs_TahunID">
<?php $khs->TahunID->EditAttrs["onclick"] = "ew_AutoFill(this); " . @$khs->TahunID->EditAttrs["onclick"]; ?>
<div class="ewDropdownList has-feedback">
	<span onclick="" class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $khs->TahunID->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x_TahunID" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php echo $khs->TahunID->RadioButtonListHtml(TRUE, "x_TahunID") ?>
		</div>
	</div>
	<div id="tp_x_TahunID" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_TahunID" data-value-separator="<?php echo $khs->TahunID->DisplayValueSeparatorAttribute() ?>" name="x_TahunID" id="x_TahunID" value="{value}"<?php echo $khs->TahunID->EditAttributes() ?>></div>
</div>
<input type="hidden" name="s_x_TahunID" id="s_x_TahunID" value="<?php echo $khs->TahunID->LookupFilterQuery() ?>">
<input type="hidden" name="ln_x_TahunID" id="ln_x_TahunID" value="x_Sesi[]">
</span>
<?php echo $khs->TahunID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->Sesi->Visible) { // Sesi ?>
<?php if (ew_IsMobile() || $khs_add->IsModal) { ?>
	<div id="r_Sesi" class="form-group">
		<label id="elh_khs_Sesi" class="col-sm-2 control-label ewLabel"><?php echo $khs->Sesi->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $khs->Sesi->CellAttributes() ?>>
<span id="el_khs_Sesi">
<div id="tp_x_Sesi" class="ewTemplate"><input type="checkbox" data-table="khs" data-field="x_Sesi" data-value-separator="<?php echo $khs->Sesi->DisplayValueSeparatorAttribute() ?>" name="x_Sesi[]" id="x_Sesi[]" value="{value}"<?php echo $khs->Sesi->EditAttributes() ?>></div>
<div id="dsl_x_Sesi" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->Sesi->CheckBoxListHtml(FALSE, "x_Sesi[]") ?>
</div></div>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $khs->Sesi->LookupFilterQuery() ?>">
</span>
<?php echo $khs->Sesi->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Sesi">
		<td><span id="elh_khs_Sesi"><?php echo $khs->Sesi->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $khs->Sesi->CellAttributes() ?>>
<span id="el_khs_Sesi">
<div id="tp_x_Sesi" class="ewTemplate"><input type="checkbox" data-table="khs" data-field="x_Sesi" data-value-separator="<?php echo $khs->Sesi->DisplayValueSeparatorAttribute() ?>" name="x_Sesi[]" id="x_Sesi[]" value="{value}"<?php echo $khs->Sesi->EditAttributes() ?>></div>
<div id="dsl_x_Sesi" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->Sesi->CheckBoxListHtml(FALSE, "x_Sesi[]") ?>
</div></div>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $khs->Sesi->LookupFilterQuery() ?>">
</span>
<?php echo $khs->Sesi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->Tingkat->Visible) { // Tingkat ?>
<?php if (ew_IsMobile() || $khs_add->IsModal) { ?>
	<div id="r_Tingkat" class="form-group">
		<label id="elh_khs_Tingkat" for="x_Tingkat" class="col-sm-2 control-label ewLabel"><?php echo $khs->Tingkat->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $khs->Tingkat->CellAttributes() ?>>
<span id="el_khs_Tingkat">
<?php $khs->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_Tingkat" data-value-separator="<?php echo $khs->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $khs->Tingkat->EditAttributes() ?>>
<?php echo $khs->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $khs->Tingkat->LookupFilterQuery() ?>">
</span>
<?php echo $khs->Tingkat->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tingkat">
		<td><span id="elh_khs_Tingkat"><?php echo $khs->Tingkat->FldCaption() ?></span></td>
		<td<?php echo $khs->Tingkat->CellAttributes() ?>>
<span id="el_khs_Tingkat">
<?php $khs->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_Tingkat" data-value-separator="<?php echo $khs->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $khs->Tingkat->EditAttributes() ?>>
<?php echo $khs->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $khs->Tingkat->LookupFilterQuery() ?>">
</span>
<?php echo $khs->Tingkat->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->Kelas->Visible) { // Kelas ?>
<?php if (ew_IsMobile() || $khs_add->IsModal) { ?>
	<div id="r_Kelas" class="form-group">
		<label id="elh_khs_Kelas" for="x_Kelas" class="col-sm-2 control-label ewLabel"><?php echo $khs->Kelas->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $khs->Kelas->CellAttributes() ?>>
<span id="el_khs_Kelas">
<select data-table="khs" data-field="x_Kelas" data-value-separator="<?php echo $khs->Kelas->DisplayValueSeparatorAttribute() ?>" id="x_Kelas" name="x_Kelas"<?php echo $khs->Kelas->EditAttributes() ?>>
<?php echo $khs->Kelas->SelectOptionListHtml("x_Kelas") ?>
</select>
<input type="hidden" name="s_x_Kelas" id="s_x_Kelas" value="<?php echo $khs->Kelas->LookupFilterQuery() ?>">
</span>
<?php echo $khs->Kelas->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Kelas">
		<td><span id="elh_khs_Kelas"><?php echo $khs->Kelas->FldCaption() ?></span></td>
		<td<?php echo $khs->Kelas->CellAttributes() ?>>
<span id="el_khs_Kelas">
<select data-table="khs" data-field="x_Kelas" data-value-separator="<?php echo $khs->Kelas->DisplayValueSeparatorAttribute() ?>" id="x_Kelas" name="x_Kelas"<?php echo $khs->Kelas->EditAttributes() ?>>
<?php echo $khs->Kelas->SelectOptionListHtml("x_Kelas") ?>
</select>
<input type="hidden" name="s_x_Kelas" id="s_x_Kelas" value="<?php echo $khs->Kelas->LookupFilterQuery() ?>">
</span>
<?php echo $khs->Kelas->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->StudentID->Visible) { // StudentID ?>
<?php if (ew_IsMobile() || $khs_add->IsModal) { ?>
	<div id="r_StudentID" class="form-group">
		<label id="elh_khs_StudentID" for="x_StudentID" class="col-sm-2 control-label ewLabel"><?php echo $khs->StudentID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $khs->StudentID->CellAttributes() ?>>
<span id="el_khs_StudentID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_StudentID"><?php echo (strval($khs->StudentID->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $khs->StudentID->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($khs->StudentID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_StudentID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="khs" data-field="x_StudentID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $khs->StudentID->DisplayValueSeparatorAttribute() ?>" name="x_StudentID" id="x_StudentID" value="<?php echo $khs->StudentID->CurrentValue ?>"<?php echo $khs->StudentID->EditAttributes() ?>>
<input type="hidden" name="s_x_StudentID" id="s_x_StudentID" value="<?php echo $khs->StudentID->LookupFilterQuery() ?>">
</span>
<?php echo $khs->StudentID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_StudentID">
		<td><span id="elh_khs_StudentID"><?php echo $khs->StudentID->FldCaption() ?></span></td>
		<td<?php echo $khs->StudentID->CellAttributes() ?>>
<span id="el_khs_StudentID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_StudentID"><?php echo (strval($khs->StudentID->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $khs->StudentID->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($khs->StudentID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_StudentID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="khs" data-field="x_StudentID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $khs->StudentID->DisplayValueSeparatorAttribute() ?>" name="x_StudentID" id="x_StudentID" value="<?php echo $khs->StudentID->CurrentValue ?>"<?php echo $khs->StudentID->EditAttributes() ?>>
<input type="hidden" name="s_x_StudentID" id="s_x_StudentID" value="<?php echo $khs->StudentID->LookupFilterQuery() ?>">
</span>
<?php echo $khs->StudentID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->StatusStudentID->Visible) { // StatusStudentID ?>
<?php if (ew_IsMobile() || $khs_add->IsModal) { ?>
	<div id="r_StatusStudentID" class="form-group">
		<label id="elh_khs_StatusStudentID" for="x_StatusStudentID" class="col-sm-2 control-label ewLabel"><?php echo $khs->StatusStudentID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $khs->StatusStudentID->CellAttributes() ?>>
<span id="el_khs_StatusStudentID">
<select data-table="khs" data-field="x_StatusStudentID" data-value-separator="<?php echo $khs->StatusStudentID->DisplayValueSeparatorAttribute() ?>" id="x_StatusStudentID" name="x_StatusStudentID"<?php echo $khs->StatusStudentID->EditAttributes() ?>>
<?php echo $khs->StatusStudentID->SelectOptionListHtml("x_StatusStudentID") ?>
</select>
<input type="hidden" name="s_x_StatusStudentID" id="s_x_StatusStudentID" value="<?php echo $khs->StatusStudentID->LookupFilterQuery() ?>">
</span>
<?php echo $khs->StatusStudentID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusStudentID">
		<td><span id="elh_khs_StatusStudentID"><?php echo $khs->StatusStudentID->FldCaption() ?></span></td>
		<td<?php echo $khs->StatusStudentID->CellAttributes() ?>>
<span id="el_khs_StatusStudentID">
<select data-table="khs" data-field="x_StatusStudentID" data-value-separator="<?php echo $khs->StatusStudentID->DisplayValueSeparatorAttribute() ?>" id="x_StatusStudentID" name="x_StatusStudentID"<?php echo $khs->StatusStudentID->EditAttributes() ?>>
<?php echo $khs->StatusStudentID->SelectOptionListHtml("x_StatusStudentID") ?>
</select>
<input type="hidden" name="s_x_StatusStudentID" id="s_x_StatusStudentID" value="<?php echo $khs->StatusStudentID->LookupFilterQuery() ?>">
</span>
<?php echo $khs->StatusStudentID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->Keterangan->Visible) { // Keterangan ?>
<?php if (ew_IsMobile() || $khs_add->IsModal) { ?>
	<div id="r_Keterangan" class="form-group">
		<label id="elh_khs_Keterangan" for="x_Keterangan" class="col-sm-2 control-label ewLabel"><?php echo $khs->Keterangan->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $khs->Keterangan->CellAttributes() ?>>
<span id="el_khs_Keterangan">
<textarea data-table="khs" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($khs->Keterangan->getPlaceHolder()) ?>"<?php echo $khs->Keterangan->EditAttributes() ?>><?php echo $khs->Keterangan->EditValue ?></textarea>
</span>
<?php echo $khs->Keterangan->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Keterangan">
		<td><span id="elh_khs_Keterangan"><?php echo $khs->Keterangan->FldCaption() ?></span></td>
		<td<?php echo $khs->Keterangan->CellAttributes() ?>>
<span id="el_khs_Keterangan">
<textarea data-table="khs" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($khs->Keterangan->getPlaceHolder()) ?>"<?php echo $khs->Keterangan->EditAttributes() ?>><?php echo $khs->Keterangan->EditValue ?></textarea>
</span>
<?php echo $khs->Keterangan->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $khs_add->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_khs_NA" class="col-sm-2 control-label ewLabel"><?php echo $khs->NA->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $khs->NA->CellAttributes() ?>>
<span id="el_khs_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_NA" data-value-separator="<?php echo $khs->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $khs->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $khs->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_khs_NA"><?php echo $khs->NA->FldCaption() ?></span></td>
		<td<?php echo $khs->NA->CellAttributes() ?>>
<span id="el_khs_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_NA" data-value-separator="<?php echo $khs->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $khs->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $khs->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $khs_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$khs_add->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $khs_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fkhsadd.Init();
</script>
<?php
$khs_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$khs_add->Page_Terminate();
?>
