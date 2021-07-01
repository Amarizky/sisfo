<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "staffinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$staff_edit = NULL; // Initialize page object first

class cstaff_edit extends cstaff {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'staff';

	// Page object name
	var $PageObjName = 'staff_edit';

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

		// Table object (staff)
		if (!isset($GLOBALS["staff"]) || get_class($GLOBALS["staff"]) == "cstaff") {
			$GLOBALS["staff"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["staff"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'staff', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("stafflist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->StaffID->SetVisibility();
		$this->LevelID->SetVisibility();
		$this->Password->SetVisibility();
		$this->NIPPNS->SetVisibility();
		$this->Nama->SetVisibility();
		$this->Gelar->SetVisibility();
		$this->KTP->SetVisibility();
		$this->TempatLahir->SetVisibility();
		$this->TanggalLahir->SetVisibility();
		$this->KelaminID->SetVisibility();
		$this->AgamaID->SetVisibility();
		$this->Telephone->SetVisibility();
		$this->_Email->SetVisibility();
		$this->Alamat->SetVisibility();
		$this->KodePos->SetVisibility();
		$this->ProvinsiID->SetVisibility();
		$this->KabupatenKotaID->SetVisibility();
		$this->KecamatanID->SetVisibility();
		$this->DesaID->SetVisibility();
		$this->KampusID->SetVisibility();
		$this->BagianID->SetVisibility();
		$this->GolonganID->SetVisibility();
		$this->IkatanID->SetVisibility();
		$this->StatusKerjaID->SetVisibility();
		$this->TglBekerja->SetVisibility();
		$this->PendidikanTerakhir->SetVisibility();
		$this->Editor->SetVisibility();
		$this->EditDate->SetVisibility();
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
		global $EW_EXPORT, $staff;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($staff);
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
		$this->FormClassName = "ewForm ewEditForm";
		if (ew_IsMobile() || $this->IsModal)
			$this->FormClassName = ew_Concat("form-horizontal", $this->FormClassName, " ");

		// Load key from QueryString
		if (@$_GET["StaffID"] <> "") {
			$this->StaffID->setQueryStringValue($_GET["StaffID"]);
		}

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->StaffID->CurrentValue == "") {
			$this->Page_Terminate("stafflist.php"); // Invalid key, return to list
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
					$this->Page_Terminate("stafflist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "stafflist.php")
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
		if (!$this->StaffID->FldIsDetailKey) {
			$this->StaffID->setFormValue($objForm->GetValue("x_StaffID"));
		}
		if (!$this->LevelID->FldIsDetailKey) {
			$this->LevelID->setFormValue($objForm->GetValue("x_LevelID"));
		}
		if (!$this->Password->FldIsDetailKey) {
			$this->Password->setFormValue($objForm->GetValue("x_Password"));
		}
		if (!$this->NIPPNS->FldIsDetailKey) {
			$this->NIPPNS->setFormValue($objForm->GetValue("x_NIPPNS"));
		}
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->Gelar->FldIsDetailKey) {
			$this->Gelar->setFormValue($objForm->GetValue("x_Gelar"));
		}
		if (!$this->KTP->FldIsDetailKey) {
			$this->KTP->setFormValue($objForm->GetValue("x_KTP"));
		}
		if (!$this->TempatLahir->FldIsDetailKey) {
			$this->TempatLahir->setFormValue($objForm->GetValue("x_TempatLahir"));
		}
		if (!$this->TanggalLahir->FldIsDetailKey) {
			$this->TanggalLahir->setFormValue($objForm->GetValue("x_TanggalLahir"));
			$this->TanggalLahir->CurrentValue = ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0);
		}
		if (!$this->KelaminID->FldIsDetailKey) {
			$this->KelaminID->setFormValue($objForm->GetValue("x_KelaminID"));
		}
		if (!$this->AgamaID->FldIsDetailKey) {
			$this->AgamaID->setFormValue($objForm->GetValue("x_AgamaID"));
		}
		if (!$this->Telephone->FldIsDetailKey) {
			$this->Telephone->setFormValue($objForm->GetValue("x_Telephone"));
		}
		if (!$this->_Email->FldIsDetailKey) {
			$this->_Email->setFormValue($objForm->GetValue("x__Email"));
		}
		if (!$this->Alamat->FldIsDetailKey) {
			$this->Alamat->setFormValue($objForm->GetValue("x_Alamat"));
		}
		if (!$this->KodePos->FldIsDetailKey) {
			$this->KodePos->setFormValue($objForm->GetValue("x_KodePos"));
		}
		if (!$this->ProvinsiID->FldIsDetailKey) {
			$this->ProvinsiID->setFormValue($objForm->GetValue("x_ProvinsiID"));
		}
		if (!$this->KabupatenKotaID->FldIsDetailKey) {
			$this->KabupatenKotaID->setFormValue($objForm->GetValue("x_KabupatenKotaID"));
		}
		if (!$this->KecamatanID->FldIsDetailKey) {
			$this->KecamatanID->setFormValue($objForm->GetValue("x_KecamatanID"));
		}
		if (!$this->DesaID->FldIsDetailKey) {
			$this->DesaID->setFormValue($objForm->GetValue("x_DesaID"));
		}
		if (!$this->KampusID->FldIsDetailKey) {
			$this->KampusID->setFormValue($objForm->GetValue("x_KampusID"));
		}
		if (!$this->BagianID->FldIsDetailKey) {
			$this->BagianID->setFormValue($objForm->GetValue("x_BagianID"));
		}
		if (!$this->GolonganID->FldIsDetailKey) {
			$this->GolonganID->setFormValue($objForm->GetValue("x_GolonganID"));
		}
		if (!$this->IkatanID->FldIsDetailKey) {
			$this->IkatanID->setFormValue($objForm->GetValue("x_IkatanID"));
		}
		if (!$this->StatusKerjaID->FldIsDetailKey) {
			$this->StatusKerjaID->setFormValue($objForm->GetValue("x_StatusKerjaID"));
		}
		if (!$this->TglBekerja->FldIsDetailKey) {
			$this->TglBekerja->setFormValue($objForm->GetValue("x_TglBekerja"));
			$this->TglBekerja->CurrentValue = ew_UnFormatDateTime($this->TglBekerja->CurrentValue, 0);
		}
		if (!$this->PendidikanTerakhir->FldIsDetailKey) {
			$this->PendidikanTerakhir->setFormValue($objForm->GetValue("x_PendidikanTerakhir"));
		}
		if (!$this->Editor->FldIsDetailKey) {
			$this->Editor->setFormValue($objForm->GetValue("x_Editor"));
		}
		if (!$this->EditDate->FldIsDetailKey) {
			$this->EditDate->setFormValue($objForm->GetValue("x_EditDate"));
			$this->EditDate->CurrentValue = ew_UnFormatDateTime($this->EditDate->CurrentValue, 0);
		}
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->StaffID->CurrentValue = $this->StaffID->FormValue;
		$this->LevelID->CurrentValue = $this->LevelID->FormValue;
		$this->Password->CurrentValue = $this->Password->FormValue;
		$this->NIPPNS->CurrentValue = $this->NIPPNS->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->Gelar->CurrentValue = $this->Gelar->FormValue;
		$this->KTP->CurrentValue = $this->KTP->FormValue;
		$this->TempatLahir->CurrentValue = $this->TempatLahir->FormValue;
		$this->TanggalLahir->CurrentValue = $this->TanggalLahir->FormValue;
		$this->TanggalLahir->CurrentValue = ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0);
		$this->KelaminID->CurrentValue = $this->KelaminID->FormValue;
		$this->AgamaID->CurrentValue = $this->AgamaID->FormValue;
		$this->Telephone->CurrentValue = $this->Telephone->FormValue;
		$this->_Email->CurrentValue = $this->_Email->FormValue;
		$this->Alamat->CurrentValue = $this->Alamat->FormValue;
		$this->KodePos->CurrentValue = $this->KodePos->FormValue;
		$this->ProvinsiID->CurrentValue = $this->ProvinsiID->FormValue;
		$this->KabupatenKotaID->CurrentValue = $this->KabupatenKotaID->FormValue;
		$this->KecamatanID->CurrentValue = $this->KecamatanID->FormValue;
		$this->DesaID->CurrentValue = $this->DesaID->FormValue;
		$this->KampusID->CurrentValue = $this->KampusID->FormValue;
		$this->BagianID->CurrentValue = $this->BagianID->FormValue;
		$this->GolonganID->CurrentValue = $this->GolonganID->FormValue;
		$this->IkatanID->CurrentValue = $this->IkatanID->FormValue;
		$this->StatusKerjaID->CurrentValue = $this->StatusKerjaID->FormValue;
		$this->TglBekerja->CurrentValue = $this->TglBekerja->FormValue;
		$this->TglBekerja->CurrentValue = ew_UnFormatDateTime($this->TglBekerja->CurrentValue, 0);
		$this->PendidikanTerakhir->CurrentValue = $this->PendidikanTerakhir->FormValue;
		$this->Editor->CurrentValue = $this->Editor->FormValue;
		$this->EditDate->CurrentValue = $this->EditDate->FormValue;
		$this->EditDate->CurrentValue = ew_UnFormatDateTime($this->EditDate->CurrentValue, 0);
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
		$this->StaffID->setDbValue($rs->fields('StaffID'));
		$this->LevelID->setDbValue($rs->fields('LevelID'));
		$this->Password->setDbValue($rs->fields('Password'));
		$this->NIPPNS->setDbValue($rs->fields('NIPPNS'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->Gelar->setDbValue($rs->fields('Gelar'));
		$this->KTP->setDbValue($rs->fields('KTP'));
		$this->TempatLahir->setDbValue($rs->fields('TempatLahir'));
		$this->TanggalLahir->setDbValue($rs->fields('TanggalLahir'));
		$this->KelaminID->setDbValue($rs->fields('KelaminID'));
		$this->AgamaID->setDbValue($rs->fields('AgamaID'));
		$this->Telephone->setDbValue($rs->fields('Telephone'));
		$this->Handphone->setDbValue($rs->fields('Handphone'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Alamat->setDbValue($rs->fields('Alamat'));
		$this->KodePos->setDbValue($rs->fields('KodePos'));
		$this->ProvinsiID->setDbValue($rs->fields('ProvinsiID'));
		$this->KabupatenKotaID->setDbValue($rs->fields('KabupatenKotaID'));
		$this->KecamatanID->setDbValue($rs->fields('KecamatanID'));
		$this->DesaID->setDbValue($rs->fields('DesaID'));
		$this->KampusID->setDbValue($rs->fields('KampusID'));
		$this->BagianID->setDbValue($rs->fields('BagianID'));
		$this->GolonganID->setDbValue($rs->fields('GolonganID'));
		$this->IkatanID->setDbValue($rs->fields('IkatanID'));
		$this->StatusKerjaID->setDbValue($rs->fields('StatusKerjaID'));
		$this->TglBekerja->setDbValue($rs->fields('TglBekerja'));
		$this->PendidikanTerakhir->setDbValue($rs->fields('PendidikanTerakhir'));
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
		$this->StaffID->DbValue = $row['StaffID'];
		$this->LevelID->DbValue = $row['LevelID'];
		$this->Password->DbValue = $row['Password'];
		$this->NIPPNS->DbValue = $row['NIPPNS'];
		$this->Nama->DbValue = $row['Nama'];
		$this->Gelar->DbValue = $row['Gelar'];
		$this->KTP->DbValue = $row['KTP'];
		$this->TempatLahir->DbValue = $row['TempatLahir'];
		$this->TanggalLahir->DbValue = $row['TanggalLahir'];
		$this->KelaminID->DbValue = $row['KelaminID'];
		$this->AgamaID->DbValue = $row['AgamaID'];
		$this->Telephone->DbValue = $row['Telephone'];
		$this->Handphone->DbValue = $row['Handphone'];
		$this->_Email->DbValue = $row['Email'];
		$this->Alamat->DbValue = $row['Alamat'];
		$this->KodePos->DbValue = $row['KodePos'];
		$this->ProvinsiID->DbValue = $row['ProvinsiID'];
		$this->KabupatenKotaID->DbValue = $row['KabupatenKotaID'];
		$this->KecamatanID->DbValue = $row['KecamatanID'];
		$this->DesaID->DbValue = $row['DesaID'];
		$this->KampusID->DbValue = $row['KampusID'];
		$this->BagianID->DbValue = $row['BagianID'];
		$this->GolonganID->DbValue = $row['GolonganID'];
		$this->IkatanID->DbValue = $row['IkatanID'];
		$this->StatusKerjaID->DbValue = $row['StatusKerjaID'];
		$this->TglBekerja->DbValue = $row['TglBekerja'];
		$this->PendidikanTerakhir->DbValue = $row['PendidikanTerakhir'];
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
		// StaffID
		// LevelID
		// Password
		// NIPPNS
		// Nama
		// Gelar
		// KTP
		// TempatLahir
		// TanggalLahir
		// KelaminID
		// AgamaID
		// Telephone
		// Handphone
		// Email
		// Alamat
		// KodePos
		// ProvinsiID
		// KabupatenKotaID
		// KecamatanID
		// DesaID
		// KampusID
		// BagianID
		// GolonganID
		// IkatanID
		// StatusKerjaID
		// TglBekerja
		// PendidikanTerakhir
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// StaffID
		$this->StaffID->ViewValue = $this->StaffID->CurrentValue;
		$this->StaffID->CssStyle = "font-weight: bold;";
		$this->StaffID->ViewCustomAttributes = "";

		// LevelID
		$this->LevelID->ViewValue = $this->LevelID->CurrentValue;
		$this->LevelID->ViewCustomAttributes = "";

		// Password
		$this->Password->ViewValue = $Language->Phrase("PasswordMask");
		$this->Password->ViewCustomAttributes = "";

		// NIPPNS
		$this->NIPPNS->ViewValue = $this->NIPPNS->CurrentValue;
		$this->NIPPNS->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->CssStyle = "font-weight: bold;";
		$this->Nama->ViewCustomAttributes = "";

		// Gelar
		$this->Gelar->ViewValue = $this->Gelar->CurrentValue;
		$this->Gelar->ViewCustomAttributes = "";

		// KTP
		$this->KTP->ViewValue = $this->KTP->CurrentValue;
		$this->KTP->ViewCustomAttributes = "";

		// TempatLahir
		$this->TempatLahir->ViewValue = $this->TempatLahir->CurrentValue;
		if (strval($this->TempatLahir->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->TempatLahir->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->TempatLahir->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->TempatLahir, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->TempatLahir->ViewValue = $this->TempatLahir->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->TempatLahir->ViewValue = $this->TempatLahir->CurrentValue;
			}
		} else {
			$this->TempatLahir->ViewValue = NULL;
		}
		$this->TempatLahir->ViewCustomAttributes = "";

		// TanggalLahir
		$this->TanggalLahir->ViewValue = $this->TanggalLahir->CurrentValue;
		$this->TanggalLahir->ViewValue = ew_FormatDateTime($this->TanggalLahir->ViewValue, 0);
		$this->TanggalLahir->ViewCustomAttributes = "";

		// KelaminID
		if (strval($this->KelaminID->CurrentValue) <> "") {
			$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->KelaminID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
		$sWhereWrk = "";
		$this->KelaminID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KelaminID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KelaminID->ViewValue = $this->KelaminID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KelaminID->ViewValue = $this->KelaminID->CurrentValue;
			}
		} else {
			$this->KelaminID->ViewValue = NULL;
		}
		$this->KelaminID->ViewCustomAttributes = "";

		// AgamaID
		if (strval($this->AgamaID->CurrentValue) <> "") {
			$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
		$sWhereWrk = "";
		$this->AgamaID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->AgamaID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->AgamaID->ViewValue = $this->AgamaID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->AgamaID->ViewValue = $this->AgamaID->CurrentValue;
			}
		} else {
			$this->AgamaID->ViewValue = NULL;
		}
		$this->AgamaID->ViewCustomAttributes = "";

		// Telephone
		$this->Telephone->ViewValue = $this->Telephone->CurrentValue;
		$this->Telephone->ViewCustomAttributes = "";

		// Handphone
		$this->Handphone->ViewValue = $this->Handphone->CurrentValue;
		$this->Handphone->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// Alamat
		$this->Alamat->ViewValue = $this->Alamat->CurrentValue;
		$this->Alamat->ViewCustomAttributes = "";

		// KodePos
		$this->KodePos->ViewValue = $this->KodePos->CurrentValue;
		$this->KodePos->ViewCustomAttributes = "";

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

		// KecamatanID
		if (strval($this->KecamatanID->CurrentValue) <> "") {
			$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
		$sWhereWrk = "";
		$this->KecamatanID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KecamatanID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KecamatanID->ViewValue = $this->KecamatanID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KecamatanID->ViewValue = $this->KecamatanID->CurrentValue;
			}
		} else {
			$this->KecamatanID->ViewValue = NULL;
		}
		$this->KecamatanID->ViewCustomAttributes = "";

		// DesaID
		if (strval($this->DesaID->CurrentValue) <> "") {
			$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
		$sWhereWrk = "";
		$this->DesaID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->DesaID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->DesaID->ViewValue = $this->DesaID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->DesaID->ViewValue = $this->DesaID->CurrentValue;
			}
		} else {
			$this->DesaID->ViewValue = NULL;
		}
		$this->DesaID->ViewCustomAttributes = "";

		// KampusID
		$this->KampusID->ViewValue = $this->KampusID->CurrentValue;
		$this->KampusID->ViewCustomAttributes = "";

		// BagianID
		if (strval($this->BagianID->CurrentValue) <> "") {
			$sFilterWrk = "`BagianID`" . ew_SearchString("=", $this->BagianID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `BagianID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_bagian`";
		$sWhereWrk = "";
		$this->BagianID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->BagianID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->BagianID->ViewValue = $this->BagianID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->BagianID->ViewValue = $this->BagianID->CurrentValue;
			}
		} else {
			$this->BagianID->ViewValue = NULL;
		}
		$this->BagianID->ViewCustomAttributes = "";

		// GolonganID
		if (strval($this->GolonganID->CurrentValue) <> "") {
			$sFilterWrk = "`GolonganID`" . ew_SearchString("=", $this->GolonganID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `GolonganID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_golongan`";
		$sWhereWrk = "";
		$this->GolonganID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->GolonganID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->GolonganID->ViewValue = $this->GolonganID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->GolonganID->ViewValue = $this->GolonganID->CurrentValue;
			}
		} else {
			$this->GolonganID->ViewValue = NULL;
		}
		$this->GolonganID->ViewCustomAttributes = "";

		// IkatanID
		if (strval($this->IkatanID->CurrentValue) <> "") {
			$sFilterWrk = "`IkatanID`" . ew_SearchString("=", $this->IkatanID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `IkatanID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_ikatan`";
		$sWhereWrk = "";
		$this->IkatanID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->IkatanID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->IkatanID->ViewValue = $this->IkatanID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->IkatanID->ViewValue = $this->IkatanID->CurrentValue;
			}
		} else {
			$this->IkatanID->ViewValue = NULL;
		}
		$this->IkatanID->ViewCustomAttributes = "";

		// StatusKerjaID
		if (strval($this->StatusKerjaID->CurrentValue) <> "") {
			$sFilterWrk = "`StatusKerjaID`" . ew_SearchString("=", $this->StatusKerjaID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusKerjaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statuskerja`";
		$sWhereWrk = "";
		$this->StatusKerjaID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StatusKerjaID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StatusKerjaID->ViewValue = $this->StatusKerjaID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StatusKerjaID->ViewValue = $this->StatusKerjaID->CurrentValue;
			}
		} else {
			$this->StatusKerjaID->ViewValue = NULL;
		}
		$this->StatusKerjaID->ViewCustomAttributes = "";

		// TglBekerja
		$this->TglBekerja->ViewValue = $this->TglBekerja->CurrentValue;
		$this->TglBekerja->ViewValue = ew_FormatDateTime($this->TglBekerja->ViewValue, 0);
		$this->TglBekerja->ViewCustomAttributes = "";

		// PendidikanTerakhir
		if (strval($this->PendidikanTerakhir->CurrentValue) <> "") {
			$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanTerakhir->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
		$sWhereWrk = "";
		$this->PendidikanTerakhir->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PendidikanTerakhir, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PendidikanTerakhir->ViewValue = $this->PendidikanTerakhir->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PendidikanTerakhir->ViewValue = $this->PendidikanTerakhir->CurrentValue;
			}
		} else {
			$this->PendidikanTerakhir->ViewValue = NULL;
		}
		$this->PendidikanTerakhir->ViewCustomAttributes = "";

		// Editor
		$this->Editor->ViewValue = $this->Editor->CurrentValue;
		$this->Editor->ViewCustomAttributes = "";

		// EditDate
		$this->EditDate->ViewValue = $this->EditDate->CurrentValue;
		$this->EditDate->ViewValue = ew_FormatDateTime($this->EditDate->ViewValue, 0);
		$this->EditDate->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Ya";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "Tidak";
		}
		$this->NA->ViewCustomAttributes = "";

			// StaffID
			$this->StaffID->LinkCustomAttributes = "";
			$this->StaffID->HrefValue = "";
			$this->StaffID->TooltipValue = "";

			// LevelID
			$this->LevelID->LinkCustomAttributes = "";
			$this->LevelID->HrefValue = "";
			$this->LevelID->TooltipValue = "";

			// Password
			$this->Password->LinkCustomAttributes = "";
			$this->Password->HrefValue = "";
			$this->Password->TooltipValue = "";

			// NIPPNS
			$this->NIPPNS->LinkCustomAttributes = "";
			$this->NIPPNS->HrefValue = "";
			$this->NIPPNS->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// Gelar
			$this->Gelar->LinkCustomAttributes = "";
			$this->Gelar->HrefValue = "";
			$this->Gelar->TooltipValue = "";

			// KTP
			$this->KTP->LinkCustomAttributes = "";
			$this->KTP->HrefValue = "";
			$this->KTP->TooltipValue = "";

			// TempatLahir
			$this->TempatLahir->LinkCustomAttributes = "";
			$this->TempatLahir->HrefValue = "";
			$this->TempatLahir->TooltipValue = "";

			// TanggalLahir
			$this->TanggalLahir->LinkCustomAttributes = "";
			$this->TanggalLahir->HrefValue = "";
			$this->TanggalLahir->TooltipValue = "";

			// KelaminID
			$this->KelaminID->LinkCustomAttributes = "";
			$this->KelaminID->HrefValue = "";
			$this->KelaminID->TooltipValue = "";

			// AgamaID
			$this->AgamaID->LinkCustomAttributes = "";
			$this->AgamaID->HrefValue = "";
			$this->AgamaID->TooltipValue = "";

			// Telephone
			$this->Telephone->LinkCustomAttributes = "";
			$this->Telephone->HrefValue = "";
			$this->Telephone->TooltipValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";

			// Alamat
			$this->Alamat->LinkCustomAttributes = "";
			$this->Alamat->HrefValue = "";
			$this->Alamat->TooltipValue = "";

			// KodePos
			$this->KodePos->LinkCustomAttributes = "";
			$this->KodePos->HrefValue = "";
			$this->KodePos->TooltipValue = "";

			// ProvinsiID
			$this->ProvinsiID->LinkCustomAttributes = "";
			$this->ProvinsiID->HrefValue = "";
			$this->ProvinsiID->TooltipValue = "";

			// KabupatenKotaID
			$this->KabupatenKotaID->LinkCustomAttributes = "";
			$this->KabupatenKotaID->HrefValue = "";
			$this->KabupatenKotaID->TooltipValue = "";

			// KecamatanID
			$this->KecamatanID->LinkCustomAttributes = "";
			$this->KecamatanID->HrefValue = "";
			$this->KecamatanID->TooltipValue = "";

			// DesaID
			$this->DesaID->LinkCustomAttributes = "";
			$this->DesaID->HrefValue = "";
			$this->DesaID->TooltipValue = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";
			$this->KampusID->TooltipValue = "";

			// BagianID
			$this->BagianID->LinkCustomAttributes = "";
			$this->BagianID->HrefValue = "";
			$this->BagianID->TooltipValue = "";

			// GolonganID
			$this->GolonganID->LinkCustomAttributes = "";
			$this->GolonganID->HrefValue = "";
			$this->GolonganID->TooltipValue = "";

			// IkatanID
			$this->IkatanID->LinkCustomAttributes = "";
			$this->IkatanID->HrefValue = "";
			$this->IkatanID->TooltipValue = "";

			// StatusKerjaID
			$this->StatusKerjaID->LinkCustomAttributes = "";
			$this->StatusKerjaID->HrefValue = "";
			$this->StatusKerjaID->TooltipValue = "";

			// TglBekerja
			$this->TglBekerja->LinkCustomAttributes = "";
			$this->TglBekerja->HrefValue = "";
			$this->TglBekerja->TooltipValue = "";

			// PendidikanTerakhir
			$this->PendidikanTerakhir->LinkCustomAttributes = "";
			$this->PendidikanTerakhir->HrefValue = "";
			$this->PendidikanTerakhir->TooltipValue = "";

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

			// StaffID
			$this->StaffID->EditAttrs["class"] = "form-control";
			$this->StaffID->EditCustomAttributes = "";
			$this->StaffID->EditValue = $this->StaffID->CurrentValue;
			$this->StaffID->CssStyle = "font-weight: bold;";
			$this->StaffID->ViewCustomAttributes = "";

			// LevelID
			$this->LevelID->EditAttrs["class"] = "form-control";
			$this->LevelID->EditCustomAttributes = "";
			$this->LevelID->EditValue = ew_HtmlEncode($this->LevelID->CurrentValue);
			$this->LevelID->PlaceHolder = ew_RemoveHtml($this->LevelID->FldCaption());

			// Password
			$this->Password->EditAttrs["class"] = "form-control";
			$this->Password->EditCustomAttributes = "";
			$this->Password->EditValue = ew_HtmlEncode($this->Password->CurrentValue);
			$this->Password->PlaceHolder = ew_RemoveHtml($this->Password->FldCaption());

			// NIPPNS
			$this->NIPPNS->EditAttrs["class"] = "form-control";
			$this->NIPPNS->EditCustomAttributes = "";
			$this->NIPPNS->EditValue = ew_HtmlEncode($this->NIPPNS->CurrentValue);
			$this->NIPPNS->PlaceHolder = ew_RemoveHtml($this->NIPPNS->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// Gelar
			$this->Gelar->EditAttrs["class"] = "form-control";
			$this->Gelar->EditCustomAttributes = "";
			$this->Gelar->EditValue = ew_HtmlEncode($this->Gelar->CurrentValue);
			$this->Gelar->PlaceHolder = ew_RemoveHtml($this->Gelar->FldCaption());

			// KTP
			$this->KTP->EditAttrs["class"] = "form-control";
			$this->KTP->EditCustomAttributes = "";
			$this->KTP->EditValue = ew_HtmlEncode($this->KTP->CurrentValue);
			$this->KTP->PlaceHolder = ew_RemoveHtml($this->KTP->FldCaption());

			// TempatLahir
			$this->TempatLahir->EditAttrs["class"] = "form-control";
			$this->TempatLahir->EditCustomAttributes = "";
			$this->TempatLahir->EditValue = ew_HtmlEncode($this->TempatLahir->CurrentValue);
			if (strval($this->TempatLahir->CurrentValue) <> "") {
				$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->TempatLahir->CurrentValue, EW_DATATYPE_STRING, "");
			$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "";
			$this->TempatLahir->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->TempatLahir, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$this->TempatLahir->EditValue = $this->TempatLahir->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->TempatLahir->EditValue = ew_HtmlEncode($this->TempatLahir->CurrentValue);
				}
			} else {
				$this->TempatLahir->EditValue = NULL;
			}
			$this->TempatLahir->PlaceHolder = ew_RemoveHtml($this->TempatLahir->FldCaption());

			// TanggalLahir
			$this->TanggalLahir->EditAttrs["class"] = "form-control";
			$this->TanggalLahir->EditCustomAttributes = "";
			$this->TanggalLahir->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->TanggalLahir->CurrentValue, 8));
			$this->TanggalLahir->PlaceHolder = ew_RemoveHtml($this->TanggalLahir->FldCaption());

			// KelaminID
			$this->KelaminID->EditCustomAttributes = "";
			if (trim(strval($this->KelaminID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->KelaminID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_kelamin`";
			$sWhereWrk = "";
			$this->KelaminID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KelaminID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KelaminID->EditValue = $arwrk;

			// AgamaID
			$this->AgamaID->EditAttrs["class"] = "form-control";
			$this->AgamaID->EditCustomAttributes = "";
			if (trim(strval($this->AgamaID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->AgamaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->AgamaID->EditValue = $arwrk;

			// Telephone
			$this->Telephone->EditAttrs["class"] = "form-control";
			$this->Telephone->EditCustomAttributes = "";
			$this->Telephone->EditValue = ew_HtmlEncode($this->Telephone->CurrentValue);
			$this->Telephone->PlaceHolder = ew_RemoveHtml($this->Telephone->FldCaption());

			// Email
			$this->_Email->EditAttrs["class"] = "form-control";
			$this->_Email->EditCustomAttributes = "";
			$this->_Email->EditValue = ew_HtmlEncode($this->_Email->CurrentValue);
			$this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

			// Alamat
			$this->Alamat->EditAttrs["class"] = "form-control";
			$this->Alamat->EditCustomAttributes = "";
			$this->Alamat->EditValue = ew_HtmlEncode($this->Alamat->CurrentValue);
			$this->Alamat->PlaceHolder = ew_RemoveHtml($this->Alamat->FldCaption());

			// KodePos
			$this->KodePos->EditAttrs["class"] = "form-control";
			$this->KodePos->EditCustomAttributes = "";
			$this->KodePos->EditValue = ew_HtmlEncode($this->KodePos->CurrentValue);
			$this->KodePos->PlaceHolder = ew_RemoveHtml($this->KodePos->FldCaption());

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

			// KabupatenKotaID
			$this->KabupatenKotaID->EditAttrs["class"] = "form-control";
			$this->KabupatenKotaID->EditCustomAttributes = "";
			if (trim(strval($this->KabupatenKotaID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenKotaID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProvinsiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "";
			$this->KabupatenKotaID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KabupatenKotaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KabupatenKotaID->EditValue = $arwrk;

			// KecamatanID
			$this->KecamatanID->EditAttrs["class"] = "form-control";
			$this->KecamatanID->EditCustomAttributes = "";
			if (trim(strval($this->KecamatanID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KabupatenKotaID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "";
			$this->KecamatanID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KecamatanID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KecamatanID->EditValue = $arwrk;

			// DesaID
			$this->DesaID->EditAttrs["class"] = "form-control";
			$this->DesaID->EditCustomAttributes = "";
			if (trim(strval($this->DesaID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KecamatanID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_desa`";
			$sWhereWrk = "";
			$this->DesaID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->DesaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->DesaID->EditValue = $arwrk;

			// KampusID
			$this->KampusID->EditAttrs["class"] = "form-control";
			$this->KampusID->EditCustomAttributes = "";
			$this->KampusID->EditValue = ew_HtmlEncode($this->KampusID->CurrentValue);
			$this->KampusID->PlaceHolder = ew_RemoveHtml($this->KampusID->FldCaption());

			// BagianID
			$this->BagianID->EditAttrs["class"] = "form-control";
			$this->BagianID->EditCustomAttributes = "";
			if (trim(strval($this->BagianID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`BagianID`" . ew_SearchString("=", $this->BagianID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `BagianID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_bagian`";
			$sWhereWrk = "";
			$this->BagianID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->BagianID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->BagianID->EditValue = $arwrk;

			// GolonganID
			$this->GolonganID->EditAttrs["class"] = "form-control";
			$this->GolonganID->EditCustomAttributes = "";
			if (trim(strval($this->GolonganID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`GolonganID`" . ew_SearchString("=", $this->GolonganID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `GolonganID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_golongan`";
			$sWhereWrk = "";
			$this->GolonganID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->GolonganID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->GolonganID->EditValue = $arwrk;

			// IkatanID
			$this->IkatanID->EditAttrs["class"] = "form-control";
			$this->IkatanID->EditCustomAttributes = "";
			if (trim(strval($this->IkatanID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`IkatanID`" . ew_SearchString("=", $this->IkatanID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `IkatanID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_ikatan`";
			$sWhereWrk = "";
			$this->IkatanID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->IkatanID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->IkatanID->EditValue = $arwrk;

			// StatusKerjaID
			$this->StatusKerjaID->EditAttrs["class"] = "form-control";
			$this->StatusKerjaID->EditCustomAttributes = "";
			if (trim(strval($this->StatusKerjaID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StatusKerjaID`" . ew_SearchString("=", $this->StatusKerjaID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StatusKerjaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_statuskerja`";
			$sWhereWrk = "";
			$this->StatusKerjaID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StatusKerjaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StatusKerjaID->EditValue = $arwrk;

			// TglBekerja
			$this->TglBekerja->EditAttrs["class"] = "form-control";
			$this->TglBekerja->EditCustomAttributes = "";
			$this->TglBekerja->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->TglBekerja->CurrentValue, 8));
			$this->TglBekerja->PlaceHolder = ew_RemoveHtml($this->TglBekerja->FldCaption());

			// PendidikanTerakhir
			$this->PendidikanTerakhir->EditAttrs["class"] = "form-control";
			$this->PendidikanTerakhir->EditCustomAttributes = "";
			if (trim(strval($this->PendidikanTerakhir->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanTerakhir->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanTerakhir->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->PendidikanTerakhir, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->PendidikanTerakhir->EditValue = $arwrk;

			// Editor
			// EditDate
			// NA

			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Edit refer script
			// StaffID

			$this->StaffID->LinkCustomAttributes = "";
			$this->StaffID->HrefValue = "";

			// LevelID
			$this->LevelID->LinkCustomAttributes = "";
			$this->LevelID->HrefValue = "";

			// Password
			$this->Password->LinkCustomAttributes = "";
			$this->Password->HrefValue = "";

			// NIPPNS
			$this->NIPPNS->LinkCustomAttributes = "";
			$this->NIPPNS->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// Gelar
			$this->Gelar->LinkCustomAttributes = "";
			$this->Gelar->HrefValue = "";

			// KTP
			$this->KTP->LinkCustomAttributes = "";
			$this->KTP->HrefValue = "";

			// TempatLahir
			$this->TempatLahir->LinkCustomAttributes = "";
			$this->TempatLahir->HrefValue = "";

			// TanggalLahir
			$this->TanggalLahir->LinkCustomAttributes = "";
			$this->TanggalLahir->HrefValue = "";

			// KelaminID
			$this->KelaminID->LinkCustomAttributes = "";
			$this->KelaminID->HrefValue = "";

			// AgamaID
			$this->AgamaID->LinkCustomAttributes = "";
			$this->AgamaID->HrefValue = "";

			// Telephone
			$this->Telephone->LinkCustomAttributes = "";
			$this->Telephone->HrefValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";

			// Alamat
			$this->Alamat->LinkCustomAttributes = "";
			$this->Alamat->HrefValue = "";

			// KodePos
			$this->KodePos->LinkCustomAttributes = "";
			$this->KodePos->HrefValue = "";

			// ProvinsiID
			$this->ProvinsiID->LinkCustomAttributes = "";
			$this->ProvinsiID->HrefValue = "";

			// KabupatenKotaID
			$this->KabupatenKotaID->LinkCustomAttributes = "";
			$this->KabupatenKotaID->HrefValue = "";

			// KecamatanID
			$this->KecamatanID->LinkCustomAttributes = "";
			$this->KecamatanID->HrefValue = "";

			// DesaID
			$this->DesaID->LinkCustomAttributes = "";
			$this->DesaID->HrefValue = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";

			// BagianID
			$this->BagianID->LinkCustomAttributes = "";
			$this->BagianID->HrefValue = "";

			// GolonganID
			$this->GolonganID->LinkCustomAttributes = "";
			$this->GolonganID->HrefValue = "";

			// IkatanID
			$this->IkatanID->LinkCustomAttributes = "";
			$this->IkatanID->HrefValue = "";

			// StatusKerjaID
			$this->StatusKerjaID->LinkCustomAttributes = "";
			$this->StatusKerjaID->HrefValue = "";

			// TglBekerja
			$this->TglBekerja->LinkCustomAttributes = "";
			$this->TglBekerja->HrefValue = "";

			// PendidikanTerakhir
			$this->PendidikanTerakhir->LinkCustomAttributes = "";
			$this->PendidikanTerakhir->HrefValue = "";

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
		if (!$this->StaffID->FldIsDetailKey && !is_null($this->StaffID->FormValue) && $this->StaffID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->StaffID->FldCaption(), $this->StaffID->ReqErrMsg));
		}
		if (!$this->LevelID->FldIsDetailKey && !is_null($this->LevelID->FormValue) && $this->LevelID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->LevelID->FldCaption(), $this->LevelID->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->LevelID->FormValue)) {
			ew_AddMessage($gsFormError, $this->LevelID->FldErrMsg());
		}
		if (!$this->Password->FldIsDetailKey && !is_null($this->Password->FormValue) && $this->Password->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Password->FldCaption(), $this->Password->ReqErrMsg));
		}
		if (!$this->Nama->FldIsDetailKey && !is_null($this->Nama->FormValue) && $this->Nama->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Nama->FldCaption(), $this->Nama->ReqErrMsg));
		}
		if (!$this->TempatLahir->FldIsDetailKey && !is_null($this->TempatLahir->FormValue) && $this->TempatLahir->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TempatLahir->FldCaption(), $this->TempatLahir->ReqErrMsg));
		}
		if (!$this->TanggalLahir->FldIsDetailKey && !is_null($this->TanggalLahir->FormValue) && $this->TanggalLahir->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TanggalLahir->FldCaption(), $this->TanggalLahir->ReqErrMsg));
		}
		if (!ew_CheckDateDef($this->TanggalLahir->FormValue)) {
			ew_AddMessage($gsFormError, $this->TanggalLahir->FldErrMsg());
		}
		if (!ew_CheckEmail($this->_Email->FormValue)) {
			ew_AddMessage($gsFormError, $this->_Email->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TglBekerja->FormValue)) {
			ew_AddMessage($gsFormError, $this->TglBekerja->FldErrMsg());
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

			// StaffID
			// LevelID

			$this->LevelID->SetDbValueDef($rsnew, $this->LevelID->CurrentValue, "", $this->LevelID->ReadOnly);

			// Password
			$this->Password->SetDbValueDef($rsnew, $this->Password->CurrentValue, NULL, $this->Password->ReadOnly);

			// NIPPNS
			$this->NIPPNS->SetDbValueDef($rsnew, $this->NIPPNS->CurrentValue, NULL, $this->NIPPNS->ReadOnly);

			// Nama
			$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, "", $this->Nama->ReadOnly);

			// Gelar
			$this->Gelar->SetDbValueDef($rsnew, $this->Gelar->CurrentValue, NULL, $this->Gelar->ReadOnly);

			// KTP
			$this->KTP->SetDbValueDef($rsnew, $this->KTP->CurrentValue, NULL, $this->KTP->ReadOnly);

			// TempatLahir
			$this->TempatLahir->SetDbValueDef($rsnew, $this->TempatLahir->CurrentValue, NULL, $this->TempatLahir->ReadOnly);

			// TanggalLahir
			$this->TanggalLahir->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0), NULL, $this->TanggalLahir->ReadOnly);

			// KelaminID
			$this->KelaminID->SetDbValueDef($rsnew, $this->KelaminID->CurrentValue, NULL, $this->KelaminID->ReadOnly);

			// AgamaID
			$this->AgamaID->SetDbValueDef($rsnew, $this->AgamaID->CurrentValue, NULL, $this->AgamaID->ReadOnly);

			// Telephone
			$this->Telephone->SetDbValueDef($rsnew, $this->Telephone->CurrentValue, NULL, $this->Telephone->ReadOnly);

			// Email
			$this->_Email->SetDbValueDef($rsnew, $this->_Email->CurrentValue, NULL, $this->_Email->ReadOnly);

			// Alamat
			$this->Alamat->SetDbValueDef($rsnew, $this->Alamat->CurrentValue, NULL, $this->Alamat->ReadOnly);

			// KodePos
			$this->KodePos->SetDbValueDef($rsnew, $this->KodePos->CurrentValue, NULL, $this->KodePos->ReadOnly);

			// ProvinsiID
			$this->ProvinsiID->SetDbValueDef($rsnew, $this->ProvinsiID->CurrentValue, NULL, $this->ProvinsiID->ReadOnly);

			// KabupatenKotaID
			$this->KabupatenKotaID->SetDbValueDef($rsnew, $this->KabupatenKotaID->CurrentValue, NULL, $this->KabupatenKotaID->ReadOnly);

			// KecamatanID
			$this->KecamatanID->SetDbValueDef($rsnew, $this->KecamatanID->CurrentValue, NULL, $this->KecamatanID->ReadOnly);

			// DesaID
			$this->DesaID->SetDbValueDef($rsnew, $this->DesaID->CurrentValue, NULL, $this->DesaID->ReadOnly);

			// KampusID
			$this->KampusID->SetDbValueDef($rsnew, $this->KampusID->CurrentValue, NULL, $this->KampusID->ReadOnly);

			// BagianID
			$this->BagianID->SetDbValueDef($rsnew, $this->BagianID->CurrentValue, NULL, $this->BagianID->ReadOnly);

			// GolonganID
			$this->GolonganID->SetDbValueDef($rsnew, $this->GolonganID->CurrentValue, NULL, $this->GolonganID->ReadOnly);

			// IkatanID
			$this->IkatanID->SetDbValueDef($rsnew, $this->IkatanID->CurrentValue, NULL, $this->IkatanID->ReadOnly);

			// StatusKerjaID
			$this->StatusKerjaID->SetDbValueDef($rsnew, $this->StatusKerjaID->CurrentValue, NULL, $this->StatusKerjaID->ReadOnly);

			// TglBekerja
			$this->TglBekerja->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TglBekerja->CurrentValue, 0), NULL, $this->TglBekerja->ReadOnly);

			// PendidikanTerakhir
			$this->PendidikanTerakhir->SetDbValueDef($rsnew, $this->PendidikanTerakhir->CurrentValue, NULL, $this->PendidikanTerakhir->ReadOnly);

			// Editor
			$this->Editor->SetDbValueDef($rsnew, CurrentUserName(), NULL);
			$rsnew['Editor'] = &$this->Editor->DbValue;

			// EditDate
			$this->EditDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
			$rsnew['EditDate'] = &$this->EditDate->DbValue;

			// NA
			$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), NULL, $this->NA->ReadOnly);

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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("stafflist.php"), "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Set up multi pages
	function SetupMultiPages() {
		$pages = new cSubPages();
		$pages->Style = "tabs";
		$pages->Add(0);
		$pages->Add(1);
		$pages->Add(2);
		$pages->Add(3);
		$pages->Add(4);
		$this->MultiPages = $pages;
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_TempatLahir":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KabupatenKotaID` AS `LinkFld`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "{filter}";
			$this->TempatLahir->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KabupatenKotaID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TempatLahir, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KelaminID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Kelamin` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
			$sWhereWrk = "";
			$this->KelaminID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Kelamin` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KelaminID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_AgamaID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `AgamaID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`AgamaID` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->AgamaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
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
		case "x_KabupatenKotaID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KabupatenKotaID` AS `LinkFld`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "{filter}";
			$this->KabupatenKotaID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KabupatenKotaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProvinsiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KabupatenKotaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KecamatanID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KecamatanID` AS `LinkFld`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "{filter}";
			$this->KecamatanID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KecamatanID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KabupatenKotaID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KecamatanID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_DesaID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `DesaID` AS `LinkFld`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
			$sWhereWrk = "{filter}";
			$this->DesaID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`DesaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KecamatanID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->DesaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_BagianID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `BagianID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_bagian`";
			$sWhereWrk = "";
			$this->BagianID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`BagianID` = {filter_value}', "t0" => "16", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->BagianID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_GolonganID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `GolonganID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_golongan`";
			$sWhereWrk = "";
			$this->GolonganID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`GolonganID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->GolonganID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_IkatanID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `IkatanID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_ikatan`";
			$sWhereWrk = "";
			$this->IkatanID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`IkatanID` = {filter_value}', "t0" => "16", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->IkatanID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StatusKerjaID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StatusKerjaID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statuskerja`";
			$sWhereWrk = "";
			$this->StatusKerjaID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StatusKerjaID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StatusKerjaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_PendidikanTerakhir":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Pendidikan` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanTerakhir->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Pendidikan` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->PendidikanTerakhir, $sWhereWrk); // Call Lookup selecting
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
		case "x_TempatLahir":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "`KabupatenKota` LIKE '%{query_value}%'";
			$this->TempatLahir->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TempatLahir, $sWhereWrk); // Call Lookup selecting
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
if (!isset($staff_edit)) $staff_edit = new cstaff_edit();

// Page init
$staff_edit->Page_Init();

// Page main
$staff_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$staff_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fstaffedit = new ew_Form("fstaffedit", "edit");

// Validate form
fstaffedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_StaffID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $staff->StaffID->FldCaption(), $staff->StaffID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_LevelID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $staff->LevelID->FldCaption(), $staff->LevelID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_LevelID");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($staff->LevelID->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Password");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $staff->Password->FldCaption(), $staff->Password->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Nama");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $staff->Nama->FldCaption(), $staff->Nama->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TempatLahir");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $staff->TempatLahir->FldCaption(), $staff->TempatLahir->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TanggalLahir");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $staff->TanggalLahir->FldCaption(), $staff->TanggalLahir->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TanggalLahir");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($staff->TanggalLahir->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "__Email");
			if (elm && !ew_CheckEmail(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($staff->_Email->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_TglBekerja");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($staff->TglBekerja->FldErrMsg()) ?>");

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
fstaffedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fstaffedit.ValidateRequired = true;
<?php } else { ?>
fstaffedit.ValidateRequired = false; 
<?php } ?>

// Multi-Page
fstaffedit.MultiPage = new ew_MultiPage("fstaffedit");

// Dynamic selection lists
fstaffedit.Lists["x_TempatLahir"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstaffedit.Lists["x_KelaminID"] = {"LinkField":"x_Kelamin","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kelamin"};
fstaffedit.Lists["x_AgamaID"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fstaffedit.Lists["x_ProvinsiID"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenKotaID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fstaffedit.Lists["x_KabupatenKotaID"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":["x_ProvinsiID"],"ChildFields":["x_KecamatanID"],"FilterFields":["x_ProvinsiID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstaffedit.Lists["x_KecamatanID"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":["x_KabupatenKotaID"],"ChildFields":["x_DesaID"],"FilterFields":["x_KabupatenKotaID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fstaffedit.Lists["x_DesaID"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":["x_KecamatanID"],"ChildFields":[],"FilterFields":["x_KecamatanID"],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fstaffedit.Lists["x_BagianID"] = {"LinkField":"x_BagianID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_bagian"};
fstaffedit.Lists["x_GolonganID"] = {"LinkField":"x_GolonganID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_golongan"};
fstaffedit.Lists["x_IkatanID"] = {"LinkField":"x_IkatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_ikatan"};
fstaffedit.Lists["x_StatusKerjaID"] = {"LinkField":"x_StatusKerjaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statuskerja"};
fstaffedit.Lists["x_PendidikanTerakhir"] = {"LinkField":"x_Pendidikan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pendidikanortu"};
fstaffedit.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fstaffedit.Lists["x_NA"].Options = <?php echo json_encode($staff->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$staff_edit->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $staff_edit->ShowPageHeader(); ?>
<?php
$staff_edit->ShowMessage();
?>
<form name="fstaffedit" id="fstaffedit" class="<?php echo $staff_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($staff_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $staff_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="staff">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<?php if ($staff_edit->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$staff_edit->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<div class="ewMultiPage">
<div class="tabbable" id="staff_edit">
	<ul class="nav<?php echo $staff_edit->MultiPages->NavStyle() ?>">
		<li<?php echo $staff_edit->MultiPages->TabStyle("1") ?>><a href="#tab_staff1" data-toggle="tab"><?php echo $staff->PageCaption(1) ?></a></li>
		<li<?php echo $staff_edit->MultiPages->TabStyle("2") ?>><a href="#tab_staff2" data-toggle="tab"><?php echo $staff->PageCaption(2) ?></a></li>
		<li<?php echo $staff_edit->MultiPages->TabStyle("3") ?>><a href="#tab_staff3" data-toggle="tab"><?php echo $staff->PageCaption(3) ?></a></li>
		<li<?php echo $staff_edit->MultiPages->TabStyle("4") ?>><a href="#tab_staff4" data-toggle="tab"><?php echo $staff->PageCaption(4) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane<?php echo $staff_edit->MultiPages->PageStyle("1") ?>" id="tab_staff1">
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_staffedit1" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($staff->StaffID->Visible) { // StaffID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_StaffID" class="form-group">
		<label id="elh_staff_StaffID" for="x_StaffID" class="col-sm-2 control-label ewLabel"><?php echo $staff->StaffID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $staff->StaffID->CellAttributes() ?>>
<span id="el_staff_StaffID">
<span<?php echo $staff->StaffID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $staff->StaffID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="staff" data-field="x_StaffID" data-page="1" name="x_StaffID" id="x_StaffID" value="<?php echo ew_HtmlEncode($staff->StaffID->CurrentValue) ?>">
<?php echo $staff->StaffID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_StaffID">
		<td><span id="elh_staff_StaffID"><?php echo $staff->StaffID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $staff->StaffID->CellAttributes() ?>>
<span id="el_staff_StaffID">
<span<?php echo $staff->StaffID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $staff->StaffID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="staff" data-field="x_StaffID" data-page="1" name="x_StaffID" id="x_StaffID" value="<?php echo ew_HtmlEncode($staff->StaffID->CurrentValue) ?>">
<?php echo $staff->StaffID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->LevelID->Visible) { // LevelID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_LevelID" class="form-group">
		<label id="elh_staff_LevelID" for="x_LevelID" class="col-sm-2 control-label ewLabel"><?php echo $staff->LevelID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $staff->LevelID->CellAttributes() ?>>
<span id="el_staff_LevelID">
<input type="text" data-table="staff" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($staff->LevelID->getPlaceHolder()) ?>" value="<?php echo $staff->LevelID->EditValue ?>"<?php echo $staff->LevelID->EditAttributes() ?>>
</span>
<?php echo $staff->LevelID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_LevelID">
		<td><span id="elh_staff_LevelID"><?php echo $staff->LevelID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $staff->LevelID->CellAttributes() ?>>
<span id="el_staff_LevelID">
<input type="text" data-table="staff" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($staff->LevelID->getPlaceHolder()) ?>" value="<?php echo $staff->LevelID->EditValue ?>"<?php echo $staff->LevelID->EditAttributes() ?>>
</span>
<?php echo $staff->LevelID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->Password->Visible) { // Password ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_Password" class="form-group">
		<label id="elh_staff_Password" for="x_Password" class="col-sm-2 control-label ewLabel"><?php echo $staff->Password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $staff->Password->CellAttributes() ?>>
<span id="el_staff_Password">
<input type="password" data-field="x_Password" name="x_Password" id="x_Password" value="<?php echo $staff->Password->EditValue ?>" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($staff->Password->getPlaceHolder()) ?>"<?php echo $staff->Password->EditAttributes() ?>>
</span>
<?php echo $staff->Password->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Password">
		<td><span id="elh_staff_Password"><?php echo $staff->Password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $staff->Password->CellAttributes() ?>>
<span id="el_staff_Password">
<input type="password" data-field="x_Password" name="x_Password" id="x_Password" value="<?php echo $staff->Password->EditValue ?>" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($staff->Password->getPlaceHolder()) ?>"<?php echo $staff->Password->EditAttributes() ?>>
</span>
<?php echo $staff->Password->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->NIPPNS->Visible) { // NIPPNS ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_NIPPNS" class="form-group">
		<label id="elh_staff_NIPPNS" for="x_NIPPNS" class="col-sm-2 control-label ewLabel"><?php echo $staff->NIPPNS->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->NIPPNS->CellAttributes() ?>>
<span id="el_staff_NIPPNS">
<input type="text" data-table="staff" data-field="x_NIPPNS" data-page="1" name="x_NIPPNS" id="x_NIPPNS" size="30" maxlength="30" placeholder="<?php echo ew_HtmlEncode($staff->NIPPNS->getPlaceHolder()) ?>" value="<?php echo $staff->NIPPNS->EditValue ?>"<?php echo $staff->NIPPNS->EditAttributes() ?>>
</span>
<?php echo $staff->NIPPNS->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NIPPNS">
		<td><span id="elh_staff_NIPPNS"><?php echo $staff->NIPPNS->FldCaption() ?></span></td>
		<td<?php echo $staff->NIPPNS->CellAttributes() ?>>
<span id="el_staff_NIPPNS">
<input type="text" data-table="staff" data-field="x_NIPPNS" data-page="1" name="x_NIPPNS" id="x_NIPPNS" size="30" maxlength="30" placeholder="<?php echo ew_HtmlEncode($staff->NIPPNS->getPlaceHolder()) ?>" value="<?php echo $staff->NIPPNS->EditValue ?>"<?php echo $staff->NIPPNS->EditAttributes() ?>>
</span>
<?php echo $staff->NIPPNS->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label id="elh_staff_Nama" for="x_Nama" class="col-sm-2 control-label ewLabel"><?php echo $staff->Nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $staff->Nama->CellAttributes() ?>>
<span id="el_staff_Nama">
<input type="text" data-table="staff" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->Nama->getPlaceHolder()) ?>" value="<?php echo $staff->Nama->EditValue ?>"<?php echo $staff->Nama->EditAttributes() ?>>
</span>
<?php echo $staff->Nama->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_staff_Nama"><?php echo $staff->Nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $staff->Nama->CellAttributes() ?>>
<span id="el_staff_Nama">
<input type="text" data-table="staff" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->Nama->getPlaceHolder()) ?>" value="<?php echo $staff->Nama->EditValue ?>"<?php echo $staff->Nama->EditAttributes() ?>>
</span>
<?php echo $staff->Nama->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->Gelar->Visible) { // Gelar ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_Gelar" class="form-group">
		<label id="elh_staff_Gelar" for="x_Gelar" class="col-sm-2 control-label ewLabel"><?php echo $staff->Gelar->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->Gelar->CellAttributes() ?>>
<span id="el_staff_Gelar">
<input type="text" data-table="staff" data-field="x_Gelar" data-page="1" name="x_Gelar" id="x_Gelar" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($staff->Gelar->getPlaceHolder()) ?>" value="<?php echo $staff->Gelar->EditValue ?>"<?php echo $staff->Gelar->EditAttributes() ?>>
</span>
<?php echo $staff->Gelar->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Gelar">
		<td><span id="elh_staff_Gelar"><?php echo $staff->Gelar->FldCaption() ?></span></td>
		<td<?php echo $staff->Gelar->CellAttributes() ?>>
<span id="el_staff_Gelar">
<input type="text" data-table="staff" data-field="x_Gelar" data-page="1" name="x_Gelar" id="x_Gelar" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($staff->Gelar->getPlaceHolder()) ?>" value="<?php echo $staff->Gelar->EditValue ?>"<?php echo $staff->Gelar->EditAttributes() ?>>
</span>
<?php echo $staff->Gelar->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $staff_edit->MultiPages->PageStyle("2") ?>" id="tab_staff2">
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_staffedit2" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($staff->KTP->Visible) { // KTP ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_KTP" class="form-group">
		<label id="elh_staff_KTP" for="x_KTP" class="col-sm-2 control-label ewLabel"><?php echo $staff->KTP->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->KTP->CellAttributes() ?>>
<span id="el_staff_KTP">
<input type="text" data-table="staff" data-field="x_KTP" data-page="2" name="x_KTP" id="x_KTP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->KTP->getPlaceHolder()) ?>" value="<?php echo $staff->KTP->EditValue ?>"<?php echo $staff->KTP->EditAttributes() ?>>
</span>
<?php echo $staff->KTP->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KTP">
		<td><span id="elh_staff_KTP"><?php echo $staff->KTP->FldCaption() ?></span></td>
		<td<?php echo $staff->KTP->CellAttributes() ?>>
<span id="el_staff_KTP">
<input type="text" data-table="staff" data-field="x_KTP" data-page="2" name="x_KTP" id="x_KTP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->KTP->getPlaceHolder()) ?>" value="<?php echo $staff->KTP->EditValue ?>"<?php echo $staff->KTP->EditAttributes() ?>>
</span>
<?php echo $staff->KTP->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->TempatLahir->Visible) { // TempatLahir ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_TempatLahir" class="form-group">
		<label id="elh_staff_TempatLahir" class="col-sm-2 control-label ewLabel"><?php echo $staff->TempatLahir->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $staff->TempatLahir->CellAttributes() ?>>
<span id="el_staff_TempatLahir">
<?php
$wrkonchange = trim(" " . @$staff->TempatLahir->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$staff->TempatLahir->EditAttrs["onchange"] = "";
?>
<span id="as_x_TempatLahir" style="white-space: nowrap; z-index: 8920">
	<input type="text" name="sv_x_TempatLahir" id="sv_x_TempatLahir" value="<?php echo $staff->TempatLahir->EditValue ?>" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->TempatLahir->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($staff->TempatLahir->getPlaceHolder()) ?>"<?php echo $staff->TempatLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="staff" data-field="x_TempatLahir" data-page="2" data-value-separator="<?php echo $staff->TempatLahir->DisplayValueSeparatorAttribute() ?>" name="x_TempatLahir" id="x_TempatLahir" value="<?php echo ew_HtmlEncode($staff->TempatLahir->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TempatLahir" id="q_x_TempatLahir" value="<?php echo $staff->TempatLahir->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fstaffedit.CreateAutoSuggest({"id":"x_TempatLahir","forceSelect":false});
</script>
</span>
<?php echo $staff->TempatLahir->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TempatLahir">
		<td><span id="elh_staff_TempatLahir"><?php echo $staff->TempatLahir->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $staff->TempatLahir->CellAttributes() ?>>
<span id="el_staff_TempatLahir">
<?php
$wrkonchange = trim(" " . @$staff->TempatLahir->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$staff->TempatLahir->EditAttrs["onchange"] = "";
?>
<span id="as_x_TempatLahir" style="white-space: nowrap; z-index: 8920">
	<input type="text" name="sv_x_TempatLahir" id="sv_x_TempatLahir" value="<?php echo $staff->TempatLahir->EditValue ?>" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->TempatLahir->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($staff->TempatLahir->getPlaceHolder()) ?>"<?php echo $staff->TempatLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="staff" data-field="x_TempatLahir" data-page="2" data-value-separator="<?php echo $staff->TempatLahir->DisplayValueSeparatorAttribute() ?>" name="x_TempatLahir" id="x_TempatLahir" value="<?php echo ew_HtmlEncode($staff->TempatLahir->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TempatLahir" id="q_x_TempatLahir" value="<?php echo $staff->TempatLahir->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fstaffedit.CreateAutoSuggest({"id":"x_TempatLahir","forceSelect":false});
</script>
</span>
<?php echo $staff->TempatLahir->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->TanggalLahir->Visible) { // TanggalLahir ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_TanggalLahir" class="form-group">
		<label id="elh_staff_TanggalLahir" for="x_TanggalLahir" class="col-sm-2 control-label ewLabel"><?php echo $staff->TanggalLahir->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $staff->TanggalLahir->CellAttributes() ?>>
<span id="el_staff_TanggalLahir">
<input type="text" data-table="staff" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($staff->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $staff->TanggalLahir->EditValue ?>"<?php echo $staff->TanggalLahir->EditAttributes() ?>>
<?php if (!$staff->TanggalLahir->ReadOnly && !$staff->TanggalLahir->Disabled && !isset($staff->TanggalLahir->EditAttrs["readonly"]) && !isset($staff->TanggalLahir->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fstaffedit", "x_TanggalLahir", 0);
</script>
<?php } ?>
</span>
<?php echo $staff->TanggalLahir->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanggalLahir">
		<td><span id="elh_staff_TanggalLahir"><?php echo $staff->TanggalLahir->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $staff->TanggalLahir->CellAttributes() ?>>
<span id="el_staff_TanggalLahir">
<input type="text" data-table="staff" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($staff->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $staff->TanggalLahir->EditValue ?>"<?php echo $staff->TanggalLahir->EditAttributes() ?>>
<?php if (!$staff->TanggalLahir->ReadOnly && !$staff->TanggalLahir->Disabled && !isset($staff->TanggalLahir->EditAttrs["readonly"]) && !isset($staff->TanggalLahir->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fstaffedit", "x_TanggalLahir", 0);
</script>
<?php } ?>
</span>
<?php echo $staff->TanggalLahir->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->KelaminID->Visible) { // KelaminID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_KelaminID" class="form-group">
		<label id="elh_staff_KelaminID" class="col-sm-2 control-label ewLabel"><?php echo $staff->KelaminID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->KelaminID->CellAttributes() ?>>
<span id="el_staff_KelaminID">
<div id="tp_x_KelaminID" class="ewTemplate"><input type="radio" data-table="staff" data-field="x_KelaminID" data-page="2" data-value-separator="<?php echo $staff->KelaminID->DisplayValueSeparatorAttribute() ?>" name="x_KelaminID" id="x_KelaminID" value="{value}"<?php echo $staff->KelaminID->EditAttributes() ?>></div>
<div id="dsl_x_KelaminID" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $staff->KelaminID->RadioButtonListHtml(FALSE, "x_KelaminID", 2) ?>
</div></div>
<input type="hidden" name="s_x_KelaminID" id="s_x_KelaminID" value="<?php echo $staff->KelaminID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->KelaminID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KelaminID">
		<td><span id="elh_staff_KelaminID"><?php echo $staff->KelaminID->FldCaption() ?></span></td>
		<td<?php echo $staff->KelaminID->CellAttributes() ?>>
<span id="el_staff_KelaminID">
<div id="tp_x_KelaminID" class="ewTemplate"><input type="radio" data-table="staff" data-field="x_KelaminID" data-page="2" data-value-separator="<?php echo $staff->KelaminID->DisplayValueSeparatorAttribute() ?>" name="x_KelaminID" id="x_KelaminID" value="{value}"<?php echo $staff->KelaminID->EditAttributes() ?>></div>
<div id="dsl_x_KelaminID" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $staff->KelaminID->RadioButtonListHtml(FALSE, "x_KelaminID", 2) ?>
</div></div>
<input type="hidden" name="s_x_KelaminID" id="s_x_KelaminID" value="<?php echo $staff->KelaminID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->KelaminID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->AgamaID->Visible) { // AgamaID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_AgamaID" class="form-group">
		<label id="elh_staff_AgamaID" for="x_AgamaID" class="col-sm-2 control-label ewLabel"><?php echo $staff->AgamaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->AgamaID->CellAttributes() ?>>
<span id="el_staff_AgamaID">
<select data-table="staff" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $staff->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $staff->AgamaID->EditAttributes() ?>>
<?php echo $staff->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $staff->AgamaID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->AgamaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_AgamaID">
		<td><span id="elh_staff_AgamaID"><?php echo $staff->AgamaID->FldCaption() ?></span></td>
		<td<?php echo $staff->AgamaID->CellAttributes() ?>>
<span id="el_staff_AgamaID">
<select data-table="staff" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $staff->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $staff->AgamaID->EditAttributes() ?>>
<?php echo $staff->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $staff->AgamaID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->AgamaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->Telephone->Visible) { // Telephone ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_Telephone" class="form-group">
		<label id="elh_staff_Telephone" for="x_Telephone" class="col-sm-2 control-label ewLabel"><?php echo $staff->Telephone->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->Telephone->CellAttributes() ?>>
<span id="el_staff_Telephone">
<input type="text" data-table="staff" data-field="x_Telephone" data-page="2" name="x_Telephone" id="x_Telephone" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->Telephone->getPlaceHolder()) ?>" value="<?php echo $staff->Telephone->EditValue ?>"<?php echo $staff->Telephone->EditAttributes() ?>>
</span>
<?php echo $staff->Telephone->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Telephone">
		<td><span id="elh_staff_Telephone"><?php echo $staff->Telephone->FldCaption() ?></span></td>
		<td<?php echo $staff->Telephone->CellAttributes() ?>>
<span id="el_staff_Telephone">
<input type="text" data-table="staff" data-field="x_Telephone" data-page="2" name="x_Telephone" id="x_Telephone" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->Telephone->getPlaceHolder()) ?>" value="<?php echo $staff->Telephone->EditValue ?>"<?php echo $staff->Telephone->EditAttributes() ?>>
</span>
<?php echo $staff->Telephone->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->_Email->Visible) { // Email ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r__Email" class="form-group">
		<label id="elh_staff__Email" for="x__Email" class="col-sm-2 control-label ewLabel"><?php echo $staff->_Email->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->_Email->CellAttributes() ?>>
<span id="el_staff__Email">
<input type="text" data-table="staff" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($staff->_Email->getPlaceHolder()) ?>" value="<?php echo $staff->_Email->EditValue ?>"<?php echo $staff->_Email->EditAttributes() ?>>
</span>
<?php echo $staff->_Email->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r__Email">
		<td><span id="elh_staff__Email"><?php echo $staff->_Email->FldCaption() ?></span></td>
		<td<?php echo $staff->_Email->CellAttributes() ?>>
<span id="el_staff__Email">
<input type="text" data-table="staff" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($staff->_Email->getPlaceHolder()) ?>" value="<?php echo $staff->_Email->EditValue ?>"<?php echo $staff->_Email->EditAttributes() ?>>
</span>
<?php echo $staff->_Email->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->Alamat->Visible) { // Alamat ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_Alamat" class="form-group">
		<label id="elh_staff_Alamat" for="x_Alamat" class="col-sm-2 control-label ewLabel"><?php echo $staff->Alamat->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->Alamat->CellAttributes() ?>>
<span id="el_staff_Alamat">
<textarea data-table="staff" data-field="x_Alamat" data-page="2" name="x_Alamat" id="x_Alamat" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($staff->Alamat->getPlaceHolder()) ?>"<?php echo $staff->Alamat->EditAttributes() ?>><?php echo $staff->Alamat->EditValue ?></textarea>
</span>
<?php echo $staff->Alamat->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Alamat">
		<td><span id="elh_staff_Alamat"><?php echo $staff->Alamat->FldCaption() ?></span></td>
		<td<?php echo $staff->Alamat->CellAttributes() ?>>
<span id="el_staff_Alamat">
<textarea data-table="staff" data-field="x_Alamat" data-page="2" name="x_Alamat" id="x_Alamat" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($staff->Alamat->getPlaceHolder()) ?>"<?php echo $staff->Alamat->EditAttributes() ?>><?php echo $staff->Alamat->EditValue ?></textarea>
</span>
<?php echo $staff->Alamat->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->KodePos->Visible) { // KodePos ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_KodePos" class="form-group">
		<label id="elh_staff_KodePos" for="x_KodePos" class="col-sm-2 control-label ewLabel"><?php echo $staff->KodePos->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->KodePos->CellAttributes() ?>>
<span id="el_staff_KodePos">
<input type="text" data-table="staff" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->KodePos->getPlaceHolder()) ?>" value="<?php echo $staff->KodePos->EditValue ?>"<?php echo $staff->KodePos->EditAttributes() ?>>
</span>
<?php echo $staff->KodePos->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodePos">
		<td><span id="elh_staff_KodePos"><?php echo $staff->KodePos->FldCaption() ?></span></td>
		<td<?php echo $staff->KodePos->CellAttributes() ?>>
<span id="el_staff_KodePos">
<input type="text" data-table="staff" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->KodePos->getPlaceHolder()) ?>" value="<?php echo $staff->KodePos->EditValue ?>"<?php echo $staff->KodePos->EditAttributes() ?>>
</span>
<?php echo $staff->KodePos->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->ProvinsiID->Visible) { // ProvinsiID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_ProvinsiID" class="form-group">
		<label id="elh_staff_ProvinsiID" for="x_ProvinsiID" class="col-sm-2 control-label ewLabel"><?php echo $staff->ProvinsiID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->ProvinsiID->CellAttributes() ?>>
<span id="el_staff_ProvinsiID">
<?php $staff->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $staff->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $staff->ProvinsiID->EditAttributes() ?>>
<?php echo $staff->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $staff->ProvinsiID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->ProvinsiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiID">
		<td><span id="elh_staff_ProvinsiID"><?php echo $staff->ProvinsiID->FldCaption() ?></span></td>
		<td<?php echo $staff->ProvinsiID->CellAttributes() ?>>
<span id="el_staff_ProvinsiID">
<?php $staff->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $staff->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $staff->ProvinsiID->EditAttributes() ?>>
<?php echo $staff->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $staff->ProvinsiID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->ProvinsiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->KabupatenKotaID->Visible) { // KabupatenKotaID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_KabupatenKotaID" class="form-group">
		<label id="elh_staff_KabupatenKotaID" for="x_KabupatenKotaID" class="col-sm-2 control-label ewLabel"><?php echo $staff->KabupatenKotaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->KabupatenKotaID->CellAttributes() ?>>
<span id="el_staff_KabupatenKotaID">
<?php $staff->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $staff->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $staff->KabupatenKotaID->EditAttributes() ?>>
<?php echo $staff->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $staff->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->KabupatenKotaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenKotaID">
		<td><span id="elh_staff_KabupatenKotaID"><?php echo $staff->KabupatenKotaID->FldCaption() ?></span></td>
		<td<?php echo $staff->KabupatenKotaID->CellAttributes() ?>>
<span id="el_staff_KabupatenKotaID">
<?php $staff->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $staff->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $staff->KabupatenKotaID->EditAttributes() ?>>
<?php echo $staff->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $staff->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->KabupatenKotaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->KecamatanID->Visible) { // KecamatanID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_KecamatanID" class="form-group">
		<label id="elh_staff_KecamatanID" for="x_KecamatanID" class="col-sm-2 control-label ewLabel"><?php echo $staff->KecamatanID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->KecamatanID->CellAttributes() ?>>
<span id="el_staff_KecamatanID">
<?php $staff->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $staff->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $staff->KecamatanID->EditAttributes() ?>>
<?php echo $staff->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $staff->KecamatanID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->KecamatanID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanID">
		<td><span id="elh_staff_KecamatanID"><?php echo $staff->KecamatanID->FldCaption() ?></span></td>
		<td<?php echo $staff->KecamatanID->CellAttributes() ?>>
<span id="el_staff_KecamatanID">
<?php $staff->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $staff->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $staff->KecamatanID->EditAttributes() ?>>
<?php echo $staff->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $staff->KecamatanID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->KecamatanID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->DesaID->Visible) { // DesaID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_DesaID" class="form-group">
		<label id="elh_staff_DesaID" for="x_DesaID" class="col-sm-2 control-label ewLabel"><?php echo $staff->DesaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->DesaID->CellAttributes() ?>>
<span id="el_staff_DesaID">
<select data-table="staff" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $staff->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $staff->DesaID->EditAttributes() ?>>
<?php echo $staff->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $staff->DesaID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->DesaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_DesaID">
		<td><span id="elh_staff_DesaID"><?php echo $staff->DesaID->FldCaption() ?></span></td>
		<td<?php echo $staff->DesaID->CellAttributes() ?>>
<span id="el_staff_DesaID">
<select data-table="staff" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $staff->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $staff->DesaID->EditAttributes() ?>>
<?php echo $staff->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $staff->DesaID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->DesaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $staff_edit->MultiPages->PageStyle("3") ?>" id="tab_staff3">
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_staffedit3" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($staff->KampusID->Visible) { // KampusID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_KampusID" class="form-group">
		<label id="elh_staff_KampusID" for="x_KampusID" class="col-sm-2 control-label ewLabel"><?php echo $staff->KampusID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->KampusID->CellAttributes() ?>>
<span id="el_staff_KampusID">
<input type="text" data-table="staff" data-field="x_KampusID" data-page="3" name="x_KampusID" id="x_KampusID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($staff->KampusID->getPlaceHolder()) ?>" value="<?php echo $staff->KampusID->EditValue ?>"<?php echo $staff->KampusID->EditAttributes() ?>>
</span>
<?php echo $staff->KampusID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KampusID">
		<td><span id="elh_staff_KampusID"><?php echo $staff->KampusID->FldCaption() ?></span></td>
		<td<?php echo $staff->KampusID->CellAttributes() ?>>
<span id="el_staff_KampusID">
<input type="text" data-table="staff" data-field="x_KampusID" data-page="3" name="x_KampusID" id="x_KampusID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($staff->KampusID->getPlaceHolder()) ?>" value="<?php echo $staff->KampusID->EditValue ?>"<?php echo $staff->KampusID->EditAttributes() ?>>
</span>
<?php echo $staff->KampusID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->BagianID->Visible) { // BagianID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_BagianID" class="form-group">
		<label id="elh_staff_BagianID" for="x_BagianID" class="col-sm-2 control-label ewLabel"><?php echo $staff->BagianID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->BagianID->CellAttributes() ?>>
<span id="el_staff_BagianID">
<select data-table="staff" data-field="x_BagianID" data-page="3" data-value-separator="<?php echo $staff->BagianID->DisplayValueSeparatorAttribute() ?>" id="x_BagianID" name="x_BagianID"<?php echo $staff->BagianID->EditAttributes() ?>>
<?php echo $staff->BagianID->SelectOptionListHtml("x_BagianID") ?>
</select>
<input type="hidden" name="s_x_BagianID" id="s_x_BagianID" value="<?php echo $staff->BagianID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->BagianID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_BagianID">
		<td><span id="elh_staff_BagianID"><?php echo $staff->BagianID->FldCaption() ?></span></td>
		<td<?php echo $staff->BagianID->CellAttributes() ?>>
<span id="el_staff_BagianID">
<select data-table="staff" data-field="x_BagianID" data-page="3" data-value-separator="<?php echo $staff->BagianID->DisplayValueSeparatorAttribute() ?>" id="x_BagianID" name="x_BagianID"<?php echo $staff->BagianID->EditAttributes() ?>>
<?php echo $staff->BagianID->SelectOptionListHtml("x_BagianID") ?>
</select>
<input type="hidden" name="s_x_BagianID" id="s_x_BagianID" value="<?php echo $staff->BagianID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->BagianID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->GolonganID->Visible) { // GolonganID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_GolonganID" class="form-group">
		<label id="elh_staff_GolonganID" for="x_GolonganID" class="col-sm-2 control-label ewLabel"><?php echo $staff->GolonganID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->GolonganID->CellAttributes() ?>>
<span id="el_staff_GolonganID">
<select data-table="staff" data-field="x_GolonganID" data-page="3" data-value-separator="<?php echo $staff->GolonganID->DisplayValueSeparatorAttribute() ?>" id="x_GolonganID" name="x_GolonganID"<?php echo $staff->GolonganID->EditAttributes() ?>>
<?php echo $staff->GolonganID->SelectOptionListHtml("x_GolonganID") ?>
</select>
<input type="hidden" name="s_x_GolonganID" id="s_x_GolonganID" value="<?php echo $staff->GolonganID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->GolonganID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_GolonganID">
		<td><span id="elh_staff_GolonganID"><?php echo $staff->GolonganID->FldCaption() ?></span></td>
		<td<?php echo $staff->GolonganID->CellAttributes() ?>>
<span id="el_staff_GolonganID">
<select data-table="staff" data-field="x_GolonganID" data-page="3" data-value-separator="<?php echo $staff->GolonganID->DisplayValueSeparatorAttribute() ?>" id="x_GolonganID" name="x_GolonganID"<?php echo $staff->GolonganID->EditAttributes() ?>>
<?php echo $staff->GolonganID->SelectOptionListHtml("x_GolonganID") ?>
</select>
<input type="hidden" name="s_x_GolonganID" id="s_x_GolonganID" value="<?php echo $staff->GolonganID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->GolonganID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->IkatanID->Visible) { // IkatanID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_IkatanID" class="form-group">
		<label id="elh_staff_IkatanID" for="x_IkatanID" class="col-sm-2 control-label ewLabel"><?php echo $staff->IkatanID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->IkatanID->CellAttributes() ?>>
<span id="el_staff_IkatanID">
<select data-table="staff" data-field="x_IkatanID" data-page="3" data-value-separator="<?php echo $staff->IkatanID->DisplayValueSeparatorAttribute() ?>" id="x_IkatanID" name="x_IkatanID"<?php echo $staff->IkatanID->EditAttributes() ?>>
<?php echo $staff->IkatanID->SelectOptionListHtml("x_IkatanID") ?>
</select>
<input type="hidden" name="s_x_IkatanID" id="s_x_IkatanID" value="<?php echo $staff->IkatanID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->IkatanID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_IkatanID">
		<td><span id="elh_staff_IkatanID"><?php echo $staff->IkatanID->FldCaption() ?></span></td>
		<td<?php echo $staff->IkatanID->CellAttributes() ?>>
<span id="el_staff_IkatanID">
<select data-table="staff" data-field="x_IkatanID" data-page="3" data-value-separator="<?php echo $staff->IkatanID->DisplayValueSeparatorAttribute() ?>" id="x_IkatanID" name="x_IkatanID"<?php echo $staff->IkatanID->EditAttributes() ?>>
<?php echo $staff->IkatanID->SelectOptionListHtml("x_IkatanID") ?>
</select>
<input type="hidden" name="s_x_IkatanID" id="s_x_IkatanID" value="<?php echo $staff->IkatanID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->IkatanID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->StatusKerjaID->Visible) { // StatusKerjaID ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_StatusKerjaID" class="form-group">
		<label id="elh_staff_StatusKerjaID" for="x_StatusKerjaID" class="col-sm-2 control-label ewLabel"><?php echo $staff->StatusKerjaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->StatusKerjaID->CellAttributes() ?>>
<span id="el_staff_StatusKerjaID">
<select data-table="staff" data-field="x_StatusKerjaID" data-page="3" data-value-separator="<?php echo $staff->StatusKerjaID->DisplayValueSeparatorAttribute() ?>" id="x_StatusKerjaID" name="x_StatusKerjaID"<?php echo $staff->StatusKerjaID->EditAttributes() ?>>
<?php echo $staff->StatusKerjaID->SelectOptionListHtml("x_StatusKerjaID") ?>
</select>
<input type="hidden" name="s_x_StatusKerjaID" id="s_x_StatusKerjaID" value="<?php echo $staff->StatusKerjaID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->StatusKerjaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusKerjaID">
		<td><span id="elh_staff_StatusKerjaID"><?php echo $staff->StatusKerjaID->FldCaption() ?></span></td>
		<td<?php echo $staff->StatusKerjaID->CellAttributes() ?>>
<span id="el_staff_StatusKerjaID">
<select data-table="staff" data-field="x_StatusKerjaID" data-page="3" data-value-separator="<?php echo $staff->StatusKerjaID->DisplayValueSeparatorAttribute() ?>" id="x_StatusKerjaID" name="x_StatusKerjaID"<?php echo $staff->StatusKerjaID->EditAttributes() ?>>
<?php echo $staff->StatusKerjaID->SelectOptionListHtml("x_StatusKerjaID") ?>
</select>
<input type="hidden" name="s_x_StatusKerjaID" id="s_x_StatusKerjaID" value="<?php echo $staff->StatusKerjaID->LookupFilterQuery() ?>">
</span>
<?php echo $staff->StatusKerjaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->TglBekerja->Visible) { // TglBekerja ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_TglBekerja" class="form-group">
		<label id="elh_staff_TglBekerja" for="x_TglBekerja" class="col-sm-2 control-label ewLabel"><?php echo $staff->TglBekerja->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->TglBekerja->CellAttributes() ?>>
<span id="el_staff_TglBekerja">
<input type="text" data-table="staff" data-field="x_TglBekerja" data-page="3" name="x_TglBekerja" id="x_TglBekerja" placeholder="<?php echo ew_HtmlEncode($staff->TglBekerja->getPlaceHolder()) ?>" value="<?php echo $staff->TglBekerja->EditValue ?>"<?php echo $staff->TglBekerja->EditAttributes() ?>>
<?php if (!$staff->TglBekerja->ReadOnly && !$staff->TglBekerja->Disabled && !isset($staff->TglBekerja->EditAttrs["readonly"]) && !isset($staff->TglBekerja->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fstaffedit", "x_TglBekerja", 0);
</script>
<?php } ?>
</span>
<?php echo $staff->TglBekerja->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TglBekerja">
		<td><span id="elh_staff_TglBekerja"><?php echo $staff->TglBekerja->FldCaption() ?></span></td>
		<td<?php echo $staff->TglBekerja->CellAttributes() ?>>
<span id="el_staff_TglBekerja">
<input type="text" data-table="staff" data-field="x_TglBekerja" data-page="3" name="x_TglBekerja" id="x_TglBekerja" placeholder="<?php echo ew_HtmlEncode($staff->TglBekerja->getPlaceHolder()) ?>" value="<?php echo $staff->TglBekerja->EditValue ?>"<?php echo $staff->TglBekerja->EditAttributes() ?>>
<?php if (!$staff->TglBekerja->ReadOnly && !$staff->TglBekerja->Disabled && !isset($staff->TglBekerja->EditAttrs["readonly"]) && !isset($staff->TglBekerja->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fstaffedit", "x_TglBekerja", 0);
</script>
<?php } ?>
</span>
<?php echo $staff->TglBekerja->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->PendidikanTerakhir->Visible) { // PendidikanTerakhir ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_PendidikanTerakhir" class="form-group">
		<label id="elh_staff_PendidikanTerakhir" for="x_PendidikanTerakhir" class="col-sm-2 control-label ewLabel"><?php echo $staff->PendidikanTerakhir->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->PendidikanTerakhir->CellAttributes() ?>>
<span id="el_staff_PendidikanTerakhir">
<select data-table="staff" data-field="x_PendidikanTerakhir" data-page="3" data-value-separator="<?php echo $staff->PendidikanTerakhir->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanTerakhir" name="x_PendidikanTerakhir"<?php echo $staff->PendidikanTerakhir->EditAttributes() ?>>
<?php echo $staff->PendidikanTerakhir->SelectOptionListHtml("x_PendidikanTerakhir") ?>
</select>
<input type="hidden" name="s_x_PendidikanTerakhir" id="s_x_PendidikanTerakhir" value="<?php echo $staff->PendidikanTerakhir->LookupFilterQuery() ?>">
</span>
<?php echo $staff->PendidikanTerakhir->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_PendidikanTerakhir">
		<td><span id="elh_staff_PendidikanTerakhir"><?php echo $staff->PendidikanTerakhir->FldCaption() ?></span></td>
		<td<?php echo $staff->PendidikanTerakhir->CellAttributes() ?>>
<span id="el_staff_PendidikanTerakhir">
<select data-table="staff" data-field="x_PendidikanTerakhir" data-page="3" data-value-separator="<?php echo $staff->PendidikanTerakhir->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanTerakhir" name="x_PendidikanTerakhir"<?php echo $staff->PendidikanTerakhir->EditAttributes() ?>>
<?php echo $staff->PendidikanTerakhir->SelectOptionListHtml("x_PendidikanTerakhir") ?>
</select>
<input type="hidden" name="s_x_PendidikanTerakhir" id="s_x_PendidikanTerakhir" value="<?php echo $staff->PendidikanTerakhir->LookupFilterQuery() ?>">
</span>
<?php echo $staff->PendidikanTerakhir->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $staff_edit->MultiPages->PageStyle("4") ?>" id="tab_staff4">
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_staffedit4" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($staff->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_staff_NA" class="col-sm-2 control-label ewLabel"><?php echo $staff->NA->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $staff->NA->CellAttributes() ?>>
<span id="el_staff_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="staff" data-field="x_NA" data-page="4" data-value-separator="<?php echo $staff->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $staff->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $staff->NA->RadioButtonListHtml(FALSE, "x_NA", 4) ?>
</div></div>
</span>
<?php echo $staff->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_staff_NA"><?php echo $staff->NA->FldCaption() ?></span></td>
		<td<?php echo $staff->NA->CellAttributes() ?>>
<span id="el_staff_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="staff" data-field="x_NA" data-page="4" data-value-separator="<?php echo $staff->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $staff->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $staff->NA->RadioButtonListHtml(FALSE, "x_NA", 4) ?>
</div></div>
</span>
<?php echo $staff->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $staff_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
	</div>
</div>
</div>
<?php if (!$staff_edit->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $staff_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fstaffedit.Init();
</script>
<?php
$staff_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$staff_edit->Page_Terminate();
?>
