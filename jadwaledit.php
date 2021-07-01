<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "jadwalinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$jadwal_edit = NULL; // Initialize page object first

class cjadwal_edit extends cjadwal {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'jadwal';

	// Page object name
	var $PageObjName = 'jadwal_edit';

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

		// Table object (jadwal)
		if (!isset($GLOBALS["jadwal"]) || get_class($GLOBALS["jadwal"]) == "cjadwal") {
			$GLOBALS["jadwal"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["jadwal"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'jadwal', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("jadwallist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->JadwalID->SetVisibility();
		$this->JadwalID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->ProdiID->SetVisibility();
		$this->TahunID->SetVisibility();
		$this->Sesi->SetVisibility();
		$this->Tingkat->SetVisibility();
		$this->KelasID->SetVisibility();
		$this->HariID->SetVisibility();
		$this->JamID->SetVisibility();
		$this->MKID->SetVisibility();
		$this->TeacherID->SetVisibility();
		$this->JamMulai->SetVisibility();
		$this->JamSelesai->SetVisibility();
		$this->Editor->SetVisibility();
		$this->EditDate->SetVisibility();

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
		global $EW_EXPORT, $jadwal;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($jadwal);
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
		if (@$_GET["JadwalID"] <> "") {
			$this->JadwalID->setQueryStringValue($_GET["JadwalID"]);
		}

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->JadwalID->CurrentValue == "") {
			$this->Page_Terminate("jadwallist.php"); // Invalid key, return to list
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
					$this->Page_Terminate("jadwallist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "jadwallist.php")
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
		if (!$this->JadwalID->FldIsDetailKey)
			$this->JadwalID->setFormValue($objForm->GetValue("x_JadwalID"));
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
		if (!$this->KelasID->FldIsDetailKey) {
			$this->KelasID->setFormValue($objForm->GetValue("x_KelasID"));
		}
		if (!$this->HariID->FldIsDetailKey) {
			$this->HariID->setFormValue($objForm->GetValue("x_HariID"));
		}
		if (!$this->JamID->FldIsDetailKey) {
			$this->JamID->setFormValue($objForm->GetValue("x_JamID"));
		}
		if (!$this->MKID->FldIsDetailKey) {
			$this->MKID->setFormValue($objForm->GetValue("x_MKID"));
		}
		if (!$this->TeacherID->FldIsDetailKey) {
			$this->TeacherID->setFormValue($objForm->GetValue("x_TeacherID"));
		}
		if (!$this->JamMulai->FldIsDetailKey) {
			$this->JamMulai->setFormValue($objForm->GetValue("x_JamMulai"));
			$this->JamMulai->CurrentValue = ew_UnFormatDateTime($this->JamMulai->CurrentValue, 0);
		}
		if (!$this->JamSelesai->FldIsDetailKey) {
			$this->JamSelesai->setFormValue($objForm->GetValue("x_JamSelesai"));
			$this->JamSelesai->CurrentValue = ew_UnFormatDateTime($this->JamSelesai->CurrentValue, 0);
		}
		if (!$this->Editor->FldIsDetailKey) {
			$this->Editor->setFormValue($objForm->GetValue("x_Editor"));
		}
		if (!$this->EditDate->FldIsDetailKey) {
			$this->EditDate->setFormValue($objForm->GetValue("x_EditDate"));
			$this->EditDate->CurrentValue = ew_UnFormatDateTime($this->EditDate->CurrentValue, 0);
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->JadwalID->CurrentValue = $this->JadwalID->FormValue;
		$this->ProdiID->CurrentValue = $this->ProdiID->FormValue;
		$this->TahunID->CurrentValue = $this->TahunID->FormValue;
		$this->Sesi->CurrentValue = $this->Sesi->FormValue;
		$this->Tingkat->CurrentValue = $this->Tingkat->FormValue;
		$this->KelasID->CurrentValue = $this->KelasID->FormValue;
		$this->HariID->CurrentValue = $this->HariID->FormValue;
		$this->JamID->CurrentValue = $this->JamID->FormValue;
		$this->MKID->CurrentValue = $this->MKID->FormValue;
		$this->TeacherID->CurrentValue = $this->TeacherID->FormValue;
		$this->JamMulai->CurrentValue = $this->JamMulai->FormValue;
		$this->JamMulai->CurrentValue = ew_UnFormatDateTime($this->JamMulai->CurrentValue, 0);
		$this->JamSelesai->CurrentValue = $this->JamSelesai->FormValue;
		$this->JamSelesai->CurrentValue = ew_UnFormatDateTime($this->JamSelesai->CurrentValue, 0);
		$this->Editor->CurrentValue = $this->Editor->FormValue;
		$this->EditDate->CurrentValue = $this->EditDate->FormValue;
		$this->EditDate->CurrentValue = ew_UnFormatDateTime($this->EditDate->CurrentValue, 0);
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
		$this->JadwalID->setDbValue($rs->fields('JadwalID'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->TahunID->setDbValue($rs->fields('TahunID'));
		$this->Sesi->setDbValue($rs->fields('Sesi'));
		$this->Tingkat->setDbValue($rs->fields('Tingkat'));
		$this->KelasID->setDbValue($rs->fields('KelasID'));
		$this->HariID->setDbValue($rs->fields('HariID'));
		$this->JamID->setDbValue($rs->fields('JamID'));
		$this->MKID->setDbValue($rs->fields('MKID'));
		$this->TeacherID->setDbValue($rs->fields('TeacherID'));
		$this->JamMulai->setDbValue($rs->fields('JamMulai'));
		$this->JamSelesai->setDbValue($rs->fields('JamSelesai'));
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
		$this->JadwalID->DbValue = $row['JadwalID'];
		$this->ProdiID->DbValue = $row['ProdiID'];
		$this->TahunID->DbValue = $row['TahunID'];
		$this->Sesi->DbValue = $row['Sesi'];
		$this->Tingkat->DbValue = $row['Tingkat'];
		$this->KelasID->DbValue = $row['KelasID'];
		$this->HariID->DbValue = $row['HariID'];
		$this->JamID->DbValue = $row['JamID'];
		$this->MKID->DbValue = $row['MKID'];
		$this->TeacherID->DbValue = $row['TeacherID'];
		$this->JamMulai->DbValue = $row['JamMulai'];
		$this->JamSelesai->DbValue = $row['JamSelesai'];
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
		// JadwalID
		// ProdiID
		// TahunID
		// Sesi
		// Tingkat
		// KelasID
		// HariID
		// JamID
		// MKID
		// TeacherID
		// JamMulai
		// JamSelesai
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// JadwalID
		$this->JadwalID->ViewValue = $this->JadwalID->CurrentValue;
		$this->JadwalID->ViewCustomAttributes = "";

		// ProdiID
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `ProdiID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
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
		$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
		$this->TahunID->ViewCustomAttributes = "";

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
		$this->Tingkat->CellCssStyle .= "text-align: center;";
		$this->Tingkat->ViewCustomAttributes = "";

		// KelasID
		if (strval($this->KelasID->CurrentValue) <> "") {
			$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->KelasID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
		$sWhereWrk = "";
		$this->KelasID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KelasID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KelasID->ViewValue = $this->KelasID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KelasID->ViewValue = $this->KelasID->CurrentValue;
			}
		} else {
			$this->KelasID->ViewValue = NULL;
		}
		$this->KelasID->ViewCustomAttributes = "";

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
		if (strval($this->JamID->CurrentValue) <> "") {
			$sFilterWrk = "`JamID`" . ew_SearchString("=", $this->JamID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `JamID`, `JamID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_jamkul`";
		$sWhereWrk = "";
		$this->JamID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `JamID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->JamID->ViewValue = $this->JamID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->JamID->ViewValue = $this->JamID->CurrentValue;
			}
		} else {
			$this->JamID->ViewValue = NULL;
		}
		$this->JamID->ViewCustomAttributes = "";

		// MKID
		if (strval($this->MKID->CurrentValue) <> "") {
			$sFilterWrk = "`MKID`" . ew_SearchString("=", $this->MKID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `MKID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `mk`";
		$sWhereWrk = "";
		$this->MKID->LookupFilters = array("dx1" => '`Nama`');
		$lookuptblfilter = "`Tingkat` in (Tingkat)";
		ew_AddFilter($sWhereWrk, $lookuptblfilter);
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->MKID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->MKID->ViewValue = $this->MKID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->MKID->ViewValue = $this->MKID->CurrentValue;
			}
		} else {
			$this->MKID->ViewValue = NULL;
		}
		$this->MKID->ViewCustomAttributes = "";

		// TeacherID
		$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
		if (strval($this->TeacherID->CurrentValue) <> "") {
			$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->TeacherID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `TeacherID`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
		$sWhereWrk = "";
		$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->TeacherID->ViewValue = $this->TeacherID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
			}
		} else {
			$this->TeacherID->ViewValue = NULL;
		}
		$this->TeacherID->ViewCustomAttributes = "";

		// JamMulai
		$this->JamMulai->ViewValue = $this->JamMulai->CurrentValue;
		$this->JamMulai->ViewCustomAttributes = "";

		// JamSelesai
		$this->JamSelesai->ViewValue = $this->JamSelesai->CurrentValue;
		$this->JamSelesai->ViewCustomAttributes = "";

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
		$this->NA->ViewValue = $this->NA->CurrentValue;
		$this->NA->ViewCustomAttributes = "";

			// JadwalID
			$this->JadwalID->LinkCustomAttributes = "";
			$this->JadwalID->HrefValue = "";
			$this->JadwalID->TooltipValue = "";

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

			// KelasID
			$this->KelasID->LinkCustomAttributes = "";
			$this->KelasID->HrefValue = "";
			$this->KelasID->TooltipValue = "";

			// HariID
			$this->HariID->LinkCustomAttributes = "";
			$this->HariID->HrefValue = "";
			$this->HariID->TooltipValue = "";

			// JamID
			$this->JamID->LinkCustomAttributes = "";
			$this->JamID->HrefValue = "";
			$this->JamID->TooltipValue = "";

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";
			$this->MKID->TooltipValue = "";

			// TeacherID
			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";
			$this->TeacherID->TooltipValue = "";

			// JamMulai
			$this->JamMulai->LinkCustomAttributes = "";
			$this->JamMulai->HrefValue = "";
			$this->JamMulai->TooltipValue = "";

			// JamSelesai
			$this->JamSelesai->LinkCustomAttributes = "";
			$this->JamSelesai->HrefValue = "";
			$this->JamSelesai->TooltipValue = "";

			// Editor
			$this->Editor->LinkCustomAttributes = "";
			$this->Editor->HrefValue = "";
			$this->Editor->TooltipValue = "";

			// EditDate
			$this->EditDate->LinkCustomAttributes = "";
			$this->EditDate->HrefValue = "";
			$this->EditDate->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// JadwalID
			$this->JadwalID->EditAttrs["class"] = "form-control";
			$this->JadwalID->EditCustomAttributes = "";
			$this->JadwalID->EditValue = $this->JadwalID->CurrentValue;
			$this->JadwalID->ViewCustomAttributes = "";

			// ProdiID
			$this->ProdiID->EditAttrs["class"] = "form-control";
			$this->ProdiID->EditCustomAttributes = "";
			if (trim(strval($this->ProdiID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProdiID`, `ProdiID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_prodi`";
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
			$this->TahunID->EditAttrs["class"] = "form-control";
			$this->TahunID->EditCustomAttributes = "";
			$this->TahunID->EditValue = ew_HtmlEncode($this->TahunID->CurrentValue);
			$this->TahunID->PlaceHolder = ew_RemoveHtml($this->TahunID->FldCaption());

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

			// KelasID
			$this->KelasID->EditAttrs["class"] = "form-control";
			$this->KelasID->EditCustomAttributes = "";
			if (trim(strval($this->KelasID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->KelasID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, `Tingkat` AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->KelasID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KelasID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KelasID->EditValue = $arwrk;

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
			if (trim(strval($this->JamID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`JamID`" . ew_SearchString("=", $this->JamID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `JamID`, `JamID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `HariID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_jamkul`";
			$sWhereWrk = "";
			$this->JamID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `JamID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->JamID->EditValue = $arwrk;

			// MKID
			$this->MKID->EditCustomAttributes = "";
			if (trim(strval($this->MKID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`MKID`" . ew_SearchString("=", $this->MKID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `MKID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `mk`";
			$sWhereWrk = "";
			$this->MKID->LookupFilters = array("dx1" => '`Nama`');
			$lookuptblfilter = "`Tingkat` in (Tingkat)";
			ew_AddFilter($sWhereWrk, $lookuptblfilter);
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->MKID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$this->MKID->ViewValue = $this->MKID->DisplayValue($arwrk);
			} else {
				$this->MKID->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->MKID->EditValue = $arwrk;

			// TeacherID
			$this->TeacherID->EditAttrs["class"] = "form-control";
			$this->TeacherID->EditCustomAttributes = "";
			$this->TeacherID->EditValue = ew_HtmlEncode($this->TeacherID->CurrentValue);
			if (strval($this->TeacherID->CurrentValue) <> "") {
				$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->TeacherID->CurrentValue, EW_DATATYPE_STRING, "");
			$sSqlWrk = "SELECT `TeacherID`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
			$sWhereWrk = "";
			$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
					$this->TeacherID->EditValue = $this->TeacherID->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->TeacherID->EditValue = ew_HtmlEncode($this->TeacherID->CurrentValue);
				}
			} else {
				$this->TeacherID->EditValue = NULL;
			}
			$this->TeacherID->PlaceHolder = ew_RemoveHtml($this->TeacherID->FldCaption());

			// JamMulai
			$this->JamMulai->EditAttrs["class"] = "form-control";
			$this->JamMulai->EditCustomAttributes = "readonly";
			$this->JamMulai->EditValue = ew_HtmlEncode($this->JamMulai->CurrentValue);
			$this->JamMulai->PlaceHolder = ew_RemoveHtml($this->JamMulai->FldCaption());

			// JamSelesai
			$this->JamSelesai->EditAttrs["class"] = "form-control";
			$this->JamSelesai->EditCustomAttributes = "readonly";
			$this->JamSelesai->EditValue = ew_HtmlEncode($this->JamSelesai->CurrentValue);
			$this->JamSelesai->PlaceHolder = ew_RemoveHtml($this->JamSelesai->FldCaption());

			// Editor
			// EditDate
			// Edit refer script
			// JadwalID

			$this->JadwalID->LinkCustomAttributes = "";
			$this->JadwalID->HrefValue = "";

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

			// KelasID
			$this->KelasID->LinkCustomAttributes = "";
			$this->KelasID->HrefValue = "";

			// HariID
			$this->HariID->LinkCustomAttributes = "";
			$this->HariID->HrefValue = "";

			// JamID
			$this->JamID->LinkCustomAttributes = "";
			$this->JamID->HrefValue = "";

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";

			// TeacherID
			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";

			// JamMulai
			$this->JamMulai->LinkCustomAttributes = "";
			$this->JamMulai->HrefValue = "";

			// JamSelesai
			$this->JamSelesai->LinkCustomAttributes = "";
			$this->JamSelesai->HrefValue = "";

			// Editor
			$this->Editor->LinkCustomAttributes = "";
			$this->Editor->HrefValue = "";

			// EditDate
			$this->EditDate->LinkCustomAttributes = "";
			$this->EditDate->HrefValue = "";
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
		if (!$this->Sesi->FldIsDetailKey && !is_null($this->Sesi->FormValue) && $this->Sesi->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Sesi->FldCaption(), $this->Sesi->ReqErrMsg));
		}
		if (!$this->Tingkat->FldIsDetailKey && !is_null($this->Tingkat->FormValue) && $this->Tingkat->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Tingkat->FldCaption(), $this->Tingkat->ReqErrMsg));
		}
		if (!$this->JamID->FldIsDetailKey && !is_null($this->JamID->FormValue) && $this->JamID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->JamID->FldCaption(), $this->JamID->ReqErrMsg));
		}
		if (!$this->MKID->FldIsDetailKey && !is_null($this->MKID->FormValue) && $this->MKID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->MKID->FldCaption(), $this->MKID->ReqErrMsg));
		}
		if (!$this->TeacherID->FldIsDetailKey && !is_null($this->TeacherID->FormValue) && $this->TeacherID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TeacherID->FldCaption(), $this->TeacherID->ReqErrMsg));
		}
		if (!$this->JamMulai->FldIsDetailKey && !is_null($this->JamMulai->FormValue) && $this->JamMulai->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->JamMulai->FldCaption(), $this->JamMulai->ReqErrMsg));
		}
		if (!ew_CheckTime($this->JamMulai->FormValue)) {
			ew_AddMessage($gsFormError, $this->JamMulai->FldErrMsg());
		}
		if (!$this->JamSelesai->FldIsDetailKey && !is_null($this->JamSelesai->FormValue) && $this->JamSelesai->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->JamSelesai->FldCaption(), $this->JamSelesai->ReqErrMsg));
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

			// ProdiID
			$this->ProdiID->SetDbValueDef($rsnew, $this->ProdiID->CurrentValue, "", $this->ProdiID->ReadOnly);

			// TahunID
			$this->TahunID->SetDbValueDef($rsnew, $this->TahunID->CurrentValue, "", $this->TahunID->ReadOnly);

			// Sesi
			$this->Sesi->SetDbValueDef($rsnew, $this->Sesi->CurrentValue, 0, $this->Sesi->ReadOnly);

			// Tingkat
			$this->Tingkat->SetDbValueDef($rsnew, $this->Tingkat->CurrentValue, NULL, $this->Tingkat->ReadOnly);

			// KelasID
			$this->KelasID->SetDbValueDef($rsnew, $this->KelasID->CurrentValue, NULL, $this->KelasID->ReadOnly);

			// HariID
			$this->HariID->SetDbValueDef($rsnew, $this->HariID->CurrentValue, NULL, $this->HariID->ReadOnly);

			// JamID
			$this->JamID->SetDbValueDef($rsnew, $this->JamID->CurrentValue, NULL, $this->JamID->ReadOnly);

			// MKID
			$this->MKID->SetDbValueDef($rsnew, $this->MKID->CurrentValue, 0, $this->MKID->ReadOnly);

			// TeacherID
			$this->TeacherID->SetDbValueDef($rsnew, $this->TeacherID->CurrentValue, "", $this->TeacherID->ReadOnly);

			// JamMulai
			$this->JamMulai->SetDbValueDef($rsnew, $this->JamMulai->CurrentValue, NULL, $this->JamMulai->ReadOnly);

			// JamSelesai
			$this->JamSelesai->SetDbValueDef($rsnew, $this->JamSelesai->CurrentValue, NULL, $this->JamSelesai->ReadOnly);

			// Editor
			$this->Editor->SetDbValueDef($rsnew, CurrentUserName(), NULL);
			$rsnew['Editor'] = &$this->Editor->DbValue;

			// EditDate
			$this->EditDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
			$rsnew['EditDate'] = &$this->EditDate->DbValue;

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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("jadwallist.php"), "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_ProdiID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProdiID` AS `LinkFld`, `ProdiID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
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
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Tingkat` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KelasID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KelasID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
			$sWhereWrk = "{filter}";
			$this->KelasID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KelasID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "", "f2" => '`Tingkat` IN ({filter_value})', "t2" => "200", "fn2" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KelasID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
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
		case "x_JamID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `JamID` AS `LinkFld`, `JamID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_jamkul`";
			$sWhereWrk = "{filter}";
			$this->JamID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`JamID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`HariID` IN ({filter_value})', "t1" => "3", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `JamID` ASC";
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_MKID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `MKID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `mk`";
			$sWhereWrk = "{filter}";
			$this->MKID->LookupFilters = array("dx1" => '`Nama`');
			$lookuptblfilter = "`Tingkat` in (Tingkat)";
			ew_AddFilter($sWhereWrk, $lookuptblfilter);
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`MKID` = {filter_value}', "t0" => "20", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->MKID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_TeacherID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `TeacherID` AS `LinkFld`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
			$sWhereWrk = "{filter}";
			$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`TeacherID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
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
		case "x_TeacherID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `TeacherID`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld` FROM `teacher`";
			$sWhereWrk = "`AliasCode` LIKE '%{query_value}%' OR `Nama` LIKE '%{query_value}%' OR CONCAT(`AliasCode`,'" . ew_ValueSeparator(1, $this->TeacherID) . "',`Nama`) LIKE '{query_value}%'";
			$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
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
if (!isset($jadwal_edit)) $jadwal_edit = new cjadwal_edit();

// Page init
$jadwal_edit->Page_Init();

// Page main
$jadwal_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$jadwal_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fjadwaledit = new ew_Form("fjadwaledit", "edit");

// Validate form
fjadwaledit.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->ProdiID->FldCaption(), $jadwal->ProdiID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TahunID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->TahunID->FldCaption(), $jadwal->TahunID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Sesi");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->Sesi->FldCaption(), $jadwal->Sesi->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Tingkat");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->Tingkat->FldCaption(), $jadwal->Tingkat->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JamID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->JamID->FldCaption(), $jadwal->JamID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_MKID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->MKID->FldCaption(), $jadwal->MKID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TeacherID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->TeacherID->FldCaption(), $jadwal->TeacherID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JamMulai");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->JamMulai->FldCaption(), $jadwal->JamMulai->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JamMulai");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($jadwal->JamMulai->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_JamSelesai");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->JamSelesai->FldCaption(), $jadwal->JamSelesai->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JamSelesai");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($jadwal->JamSelesai->FldErrMsg()) ?>");

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
fjadwaledit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fjadwaledit.ValidateRequired = true;
<?php } else { ?>
fjadwaledit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fjadwaledit.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_ProdiID","","",""],"ParentFields":[],"ChildFields":["x_TahunID","x_KelasID","x_MKID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fjadwaledit.Lists["x_Sesi"] = {"LinkField":"x_Sesi","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaSesi","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_sesi"};
fjadwaledit.Lists["x_Tingkat"] = {"LinkField":"x_Tingkat","Ajax":true,"AutoFill":false,"DisplayFields":["x_Tingkat","","",""],"ParentFields":[],"ChildFields":["x_KelasID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"kelas"};
fjadwaledit.Lists["x_KelasID"] = {"LinkField":"x_KelasID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":["x_ProdiID","x_Tingkat"],"ChildFields":[],"FilterFields":["x_ProdiID","x_Tingkat"],"Options":[],"Template":"","LinkTable":"kelas"};
fjadwaledit.Lists["x_HariID"] = {"LinkField":"x_HariID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":["x_JamID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_hari"};
fjadwaledit.Lists["x_JamID"] = {"LinkField":"x_JamID","Ajax":true,"AutoFill":true,"DisplayFields":["x_JamID","","",""],"ParentFields":["x_HariID"],"ChildFields":[],"FilterFields":["x_HariID"],"Options":[],"Template":"","LinkTable":"master_jamkul"};
fjadwaledit.Lists["x_MKID"] = {"LinkField":"x_MKID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":["x_ProdiID"],"ChildFields":[],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"mk"};
fjadwaledit.Lists["x_TeacherID"] = {"LinkField":"x_TeacherID","Ajax":true,"AutoFill":false,"DisplayFields":["x_AliasCode","x_Nama","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"teacher"};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$jadwal_edit->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $jadwal_edit->ShowPageHeader(); ?>
<?php
$jadwal_edit->ShowMessage();
?>
<form name="fjadwaledit" id="fjadwaledit" class="<?php echo $jadwal_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($jadwal_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $jadwal_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="jadwal">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<?php if ($jadwal_edit->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$jadwal_edit->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_jadwaledit" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($jadwal->JadwalID->Visible) { // JadwalID ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_JadwalID" class="form-group">
		<label id="elh_jadwal_JadwalID" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->JadwalID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->JadwalID->CellAttributes() ?>>
<span id="el_jadwal_JadwalID">
<span<?php echo $jadwal->JadwalID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $jadwal->JadwalID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="jadwal" data-field="x_JadwalID" name="x_JadwalID" id="x_JadwalID" value="<?php echo ew_HtmlEncode($jadwal->JadwalID->CurrentValue) ?>">
<?php echo $jadwal->JadwalID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_JadwalID">
		<td><span id="elh_jadwal_JadwalID"><?php echo $jadwal->JadwalID->FldCaption() ?></span></td>
		<td<?php echo $jadwal->JadwalID->CellAttributes() ?>>
<span id="el_jadwal_JadwalID">
<span<?php echo $jadwal->JadwalID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $jadwal->JadwalID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="jadwal" data-field="x_JadwalID" name="x_JadwalID" id="x_JadwalID" value="<?php echo ew_HtmlEncode($jadwal->JadwalID->CurrentValue) ?>">
<?php echo $jadwal->JadwalID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label id="elh_jadwal_ProdiID" for="x_ProdiID" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->ProdiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->ProdiID->CellAttributes() ?>>
<span id="el_jadwal_ProdiID">
<?php $jadwal->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_ProdiID" data-value-separator="<?php echo $jadwal->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $jadwal->ProdiID->EditAttributes() ?>>
<?php echo $jadwal->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $jadwal->ProdiID->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->ProdiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_jadwal_ProdiID"><?php echo $jadwal->ProdiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $jadwal->ProdiID->CellAttributes() ?>>
<span id="el_jadwal_ProdiID">
<?php $jadwal->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_ProdiID" data-value-separator="<?php echo $jadwal->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $jadwal->ProdiID->EditAttributes() ?>>
<?php echo $jadwal->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $jadwal->ProdiID->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->ProdiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->TahunID->Visible) { // TahunID ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_TahunID" class="form-group">
		<label id="elh_jadwal_TahunID" for="x_TahunID" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->TahunID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->TahunID->CellAttributes() ?>>
<span id="el_jadwal_TahunID">
<input type="text" data-table="jadwal" data-field="x_TahunID" name="x_TahunID" id="x_TahunID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($jadwal->TahunID->getPlaceHolder()) ?>" value="<?php echo $jadwal->TahunID->EditValue ?>"<?php echo $jadwal->TahunID->EditAttributes() ?>>
</span>
<?php echo $jadwal->TahunID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TahunID">
		<td><span id="elh_jadwal_TahunID"><?php echo $jadwal->TahunID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $jadwal->TahunID->CellAttributes() ?>>
<span id="el_jadwal_TahunID">
<input type="text" data-table="jadwal" data-field="x_TahunID" name="x_TahunID" id="x_TahunID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($jadwal->TahunID->getPlaceHolder()) ?>" value="<?php echo $jadwal->TahunID->EditValue ?>"<?php echo $jadwal->TahunID->EditAttributes() ?>>
</span>
<?php echo $jadwal->TahunID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->Sesi->Visible) { // Sesi ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_Sesi" class="form-group">
		<label id="elh_jadwal_Sesi" for="x_Sesi" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->Sesi->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->Sesi->CellAttributes() ?>>
<span id="el_jadwal_Sesi">
<select data-table="jadwal" data-field="x_Sesi" data-value-separator="<?php echo $jadwal->Sesi->DisplayValueSeparatorAttribute() ?>" id="x_Sesi" name="x_Sesi"<?php echo $jadwal->Sesi->EditAttributes() ?>>
<?php echo $jadwal->Sesi->SelectOptionListHtml("x_Sesi") ?>
</select>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $jadwal->Sesi->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->Sesi->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Sesi">
		<td><span id="elh_jadwal_Sesi"><?php echo $jadwal->Sesi->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $jadwal->Sesi->CellAttributes() ?>>
<span id="el_jadwal_Sesi">
<select data-table="jadwal" data-field="x_Sesi" data-value-separator="<?php echo $jadwal->Sesi->DisplayValueSeparatorAttribute() ?>" id="x_Sesi" name="x_Sesi"<?php echo $jadwal->Sesi->EditAttributes() ?>>
<?php echo $jadwal->Sesi->SelectOptionListHtml("x_Sesi") ?>
</select>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $jadwal->Sesi->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->Sesi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->Tingkat->Visible) { // Tingkat ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_Tingkat" class="form-group">
		<label id="elh_jadwal_Tingkat" for="x_Tingkat" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->Tingkat->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->Tingkat->CellAttributes() ?>>
<span id="el_jadwal_Tingkat">
<?php $jadwal->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_Tingkat" data-value-separator="<?php echo $jadwal->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $jadwal->Tingkat->EditAttributes() ?>>
<?php echo $jadwal->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $jadwal->Tingkat->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->Tingkat->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tingkat">
		<td><span id="elh_jadwal_Tingkat"><?php echo $jadwal->Tingkat->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $jadwal->Tingkat->CellAttributes() ?>>
<span id="el_jadwal_Tingkat">
<?php $jadwal->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_Tingkat" data-value-separator="<?php echo $jadwal->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $jadwal->Tingkat->EditAttributes() ?>>
<?php echo $jadwal->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $jadwal->Tingkat->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->Tingkat->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->KelasID->Visible) { // KelasID ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_KelasID" class="form-group">
		<label id="elh_jadwal_KelasID" for="x_KelasID" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->KelasID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->KelasID->CellAttributes() ?>>
<span id="el_jadwal_KelasID">
<select data-table="jadwal" data-field="x_KelasID" data-value-separator="<?php echo $jadwal->KelasID->DisplayValueSeparatorAttribute() ?>" id="x_KelasID" name="x_KelasID"<?php echo $jadwal->KelasID->EditAttributes() ?>>
<?php echo $jadwal->KelasID->SelectOptionListHtml("x_KelasID") ?>
</select>
<input type="hidden" name="s_x_KelasID" id="s_x_KelasID" value="<?php echo $jadwal->KelasID->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->KelasID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KelasID">
		<td><span id="elh_jadwal_KelasID"><?php echo $jadwal->KelasID->FldCaption() ?></span></td>
		<td<?php echo $jadwal->KelasID->CellAttributes() ?>>
<span id="el_jadwal_KelasID">
<select data-table="jadwal" data-field="x_KelasID" data-value-separator="<?php echo $jadwal->KelasID->DisplayValueSeparatorAttribute() ?>" id="x_KelasID" name="x_KelasID"<?php echo $jadwal->KelasID->EditAttributes() ?>>
<?php echo $jadwal->KelasID->SelectOptionListHtml("x_KelasID") ?>
</select>
<input type="hidden" name="s_x_KelasID" id="s_x_KelasID" value="<?php echo $jadwal->KelasID->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->KelasID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->HariID->Visible) { // HariID ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_HariID" class="form-group">
		<label id="elh_jadwal_HariID" for="x_HariID" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->HariID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->HariID->CellAttributes() ?>>
<span id="el_jadwal_HariID">
<?php $jadwal->HariID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->HariID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_HariID" data-value-separator="<?php echo $jadwal->HariID->DisplayValueSeparatorAttribute() ?>" id="x_HariID" name="x_HariID"<?php echo $jadwal->HariID->EditAttributes() ?>>
<?php echo $jadwal->HariID->SelectOptionListHtml("x_HariID") ?>
</select>
<input type="hidden" name="s_x_HariID" id="s_x_HariID" value="<?php echo $jadwal->HariID->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->HariID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_HariID">
		<td><span id="elh_jadwal_HariID"><?php echo $jadwal->HariID->FldCaption() ?></span></td>
		<td<?php echo $jadwal->HariID->CellAttributes() ?>>
<span id="el_jadwal_HariID">
<?php $jadwal->HariID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->HariID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_HariID" data-value-separator="<?php echo $jadwal->HariID->DisplayValueSeparatorAttribute() ?>" id="x_HariID" name="x_HariID"<?php echo $jadwal->HariID->EditAttributes() ?>>
<?php echo $jadwal->HariID->SelectOptionListHtml("x_HariID") ?>
</select>
<input type="hidden" name="s_x_HariID" id="s_x_HariID" value="<?php echo $jadwal->HariID->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->HariID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->JamID->Visible) { // JamID ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_JamID" class="form-group">
		<label id="elh_jadwal_JamID" for="x_JamID" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->JamID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->JamID->CellAttributes() ?>>
<span id="el_jadwal_JamID">
<?php $jadwal->JamID->EditAttrs["onchange"] = "ew_AutoFill(this); " . @$jadwal->JamID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_JamID" data-value-separator="<?php echo $jadwal->JamID->DisplayValueSeparatorAttribute() ?>" id="x_JamID" name="x_JamID"<?php echo $jadwal->JamID->EditAttributes() ?>>
<?php echo $jadwal->JamID->SelectOptionListHtml("x_JamID") ?>
</select>
<input type="hidden" name="s_x_JamID" id="s_x_JamID" value="<?php echo $jadwal->JamID->LookupFilterQuery() ?>">
<input type="hidden" name="ln_x_JamID" id="ln_x_JamID" value="x_JamMulai,x_JamSelesai">
</span>
<?php echo $jadwal->JamID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_JamID">
		<td><span id="elh_jadwal_JamID"><?php echo $jadwal->JamID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $jadwal->JamID->CellAttributes() ?>>
<span id="el_jadwal_JamID">
<?php $jadwal->JamID->EditAttrs["onchange"] = "ew_AutoFill(this); " . @$jadwal->JamID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_JamID" data-value-separator="<?php echo $jadwal->JamID->DisplayValueSeparatorAttribute() ?>" id="x_JamID" name="x_JamID"<?php echo $jadwal->JamID->EditAttributes() ?>>
<?php echo $jadwal->JamID->SelectOptionListHtml("x_JamID") ?>
</select>
<input type="hidden" name="s_x_JamID" id="s_x_JamID" value="<?php echo $jadwal->JamID->LookupFilterQuery() ?>">
<input type="hidden" name="ln_x_JamID" id="ln_x_JamID" value="x_JamMulai,x_JamSelesai">
</span>
<?php echo $jadwal->JamID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->MKID->Visible) { // MKID ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_MKID" class="form-group">
		<label id="elh_jadwal_MKID" for="x_MKID" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->MKID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->MKID->CellAttributes() ?>>
<span id="el_jadwal_MKID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_MKID"><?php echo (strval($jadwal->MKID->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $jadwal->MKID->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->MKID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_MKID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="jadwal" data-field="x_MKID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->MKID->DisplayValueSeparatorAttribute() ?>" name="x_MKID" id="x_MKID" value="<?php echo $jadwal->MKID->CurrentValue ?>"<?php echo $jadwal->MKID->EditAttributes() ?>>
<input type="hidden" name="s_x_MKID" id="s_x_MKID" value="<?php echo $jadwal->MKID->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->MKID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_MKID">
		<td><span id="elh_jadwal_MKID"><?php echo $jadwal->MKID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $jadwal->MKID->CellAttributes() ?>>
<span id="el_jadwal_MKID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_MKID"><?php echo (strval($jadwal->MKID->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $jadwal->MKID->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->MKID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_MKID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="jadwal" data-field="x_MKID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->MKID->DisplayValueSeparatorAttribute() ?>" name="x_MKID" id="x_MKID" value="<?php echo $jadwal->MKID->CurrentValue ?>"<?php echo $jadwal->MKID->EditAttributes() ?>>
<input type="hidden" name="s_x_MKID" id="s_x_MKID" value="<?php echo $jadwal->MKID->LookupFilterQuery() ?>">
</span>
<?php echo $jadwal->MKID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->TeacherID->Visible) { // TeacherID ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_TeacherID" class="form-group">
		<label id="elh_jadwal_TeacherID" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->TeacherID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->TeacherID->CellAttributes() ?>>
<span id="el_jadwal_TeacherID">
<?php
$wrkonchange = trim(" " . @$jadwal->TeacherID->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$jadwal->TeacherID->EditAttrs["onchange"] = "";
?>
<span id="as_x_TeacherID" style="white-space: nowrap; z-index: 8900">
	<input type="text" name="sv_x_TeacherID" id="sv_x_TeacherID" value="<?php echo $jadwal->TeacherID->EditValue ?>" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>"<?php echo $jadwal->TeacherID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_TeacherID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->TeacherID->DisplayValueSeparatorAttribute() ?>" name="x_TeacherID" id="x_TeacherID" value="<?php echo ew_HtmlEncode($jadwal->TeacherID->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TeacherID" id="q_x_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fjadwaledit.CreateAutoSuggest({"id":"x_TeacherID","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->TeacherID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_TeacherID',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x_TeacherID" id="s_x_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(false) ?>">
</span>
<?php echo $jadwal->TeacherID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TeacherID">
		<td><span id="elh_jadwal_TeacherID"><?php echo $jadwal->TeacherID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $jadwal->TeacherID->CellAttributes() ?>>
<span id="el_jadwal_TeacherID">
<?php
$wrkonchange = trim(" " . @$jadwal->TeacherID->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$jadwal->TeacherID->EditAttrs["onchange"] = "";
?>
<span id="as_x_TeacherID" style="white-space: nowrap; z-index: 8900">
	<input type="text" name="sv_x_TeacherID" id="sv_x_TeacherID" value="<?php echo $jadwal->TeacherID->EditValue ?>" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>"<?php echo $jadwal->TeacherID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_TeacherID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->TeacherID->DisplayValueSeparatorAttribute() ?>" name="x_TeacherID" id="x_TeacherID" value="<?php echo ew_HtmlEncode($jadwal->TeacherID->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TeacherID" id="q_x_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fjadwaledit.CreateAutoSuggest({"id":"x_TeacherID","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->TeacherID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_TeacherID',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x_TeacherID" id="s_x_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(false) ?>">
</span>
<?php echo $jadwal->TeacherID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->JamMulai->Visible) { // JamMulai ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_JamMulai" class="form-group">
		<label id="elh_jadwal_JamMulai" for="x_JamMulai" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->JamMulai->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->JamMulai->CellAttributes() ?>>
<span id="el_jadwal_JamMulai">
<input type="text" data-table="jadwal" data-field="x_JamMulai" name="x_JamMulai" id="x_JamMulai" size="3" placeholder="<?php echo ew_HtmlEncode($jadwal->JamMulai->getPlaceHolder()) ?>" value="<?php echo $jadwal->JamMulai->EditValue ?>"<?php echo $jadwal->JamMulai->EditAttributes() ?>>
</span>
<?php echo $jadwal->JamMulai->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_JamMulai">
		<td><span id="elh_jadwal_JamMulai"><?php echo $jadwal->JamMulai->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $jadwal->JamMulai->CellAttributes() ?>>
<span id="el_jadwal_JamMulai">
<input type="text" data-table="jadwal" data-field="x_JamMulai" name="x_JamMulai" id="x_JamMulai" size="3" placeholder="<?php echo ew_HtmlEncode($jadwal->JamMulai->getPlaceHolder()) ?>" value="<?php echo $jadwal->JamMulai->EditValue ?>"<?php echo $jadwal->JamMulai->EditAttributes() ?>>
</span>
<?php echo $jadwal->JamMulai->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->JamSelesai->Visible) { // JamSelesai ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
	<div id="r_JamSelesai" class="form-group">
		<label id="elh_jadwal_JamSelesai" for="x_JamSelesai" class="col-sm-2 control-label ewLabel"><?php echo $jadwal->JamSelesai->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $jadwal->JamSelesai->CellAttributes() ?>>
<span id="el_jadwal_JamSelesai">
<input type="text" data-table="jadwal" data-field="x_JamSelesai" name="x_JamSelesai" id="x_JamSelesai" size="3" placeholder="<?php echo ew_HtmlEncode($jadwal->JamSelesai->getPlaceHolder()) ?>" value="<?php echo $jadwal->JamSelesai->EditValue ?>"<?php echo $jadwal->JamSelesai->EditAttributes() ?>>
</span>
<?php echo $jadwal->JamSelesai->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_JamSelesai">
		<td><span id="elh_jadwal_JamSelesai"><?php echo $jadwal->JamSelesai->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $jadwal->JamSelesai->CellAttributes() ?>>
<span id="el_jadwal_JamSelesai">
<input type="text" data-table="jadwal" data-field="x_JamSelesai" name="x_JamSelesai" id="x_JamSelesai" size="3" placeholder="<?php echo ew_HtmlEncode($jadwal->JamSelesai->getPlaceHolder()) ?>" value="<?php echo $jadwal->JamSelesai->EditValue ?>"<?php echo $jadwal->JamSelesai->EditAttributes() ?>>
</span>
<?php echo $jadwal->JamSelesai->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $jadwal_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$jadwal_edit->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $jadwal_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fjadwaledit.Init();
</script>
<?php
$jadwal_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$jadwal_edit->Page_Terminate();
?>
