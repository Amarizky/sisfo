<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "krsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$krs_add = NULL; // Initialize page object first

class ckrs_add extends ckrs {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'krs';

	// Page object name
	var $PageObjName = 'krs_add';

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

		// Table object (krs)
		if (!isset($GLOBALS["krs"]) || get_class($GLOBALS["krs"]) == "ckrs") {
			$GLOBALS["krs"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["krs"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'krs', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("krslist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->KHSID->SetVisibility();
		$this->StudentID->SetVisibility();
		$this->TahunID->SetVisibility();
		$this->Sesi->SetVisibility();
		$this->JadwalID->SetVisibility();
		$this->MKID->SetVisibility();
		$this->MKKode->SetVisibility();
		$this->SKS->SetVisibility();
		$this->Tugas1->SetVisibility();
		$this->Tugas2->SetVisibility();
		$this->Tugas3->SetVisibility();
		$this->Tugas4->SetVisibility();
		$this->Tugas5->SetVisibility();
		$this->Presensi->SetVisibility();
		$this->_Presensi->SetVisibility();
		$this->UTS->SetVisibility();
		$this->UAS->SetVisibility();
		$this->Responsi->SetVisibility();
		$this->NilaiAkhir->SetVisibility();
		$this->GradeNilai->SetVisibility();
		$this->BobotNilai->SetVisibility();
		$this->StatusKRSID->SetVisibility();
		$this->Tinggi->SetVisibility();
		$this->Final->SetVisibility();
		$this->Setara->SetVisibility();
		$this->Creator->SetVisibility();
		$this->CreateDate->SetVisibility();
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
		global $EW_EXPORT, $krs;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($krs);
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
			if (@$_GET["KRSID"] != "") {
				$this->KRSID->setQueryStringValue($_GET["KRSID"]);
				$this->setKey("KRSID", $this->KRSID->CurrentValue); // Set up key
			} else {
				$this->setKey("KRSID", ""); // Clear key
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
					$this->Page_Terminate("krslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "krslist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "krsview.php")
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
		$this->KHSID->CurrentValue = 0;
		$this->StudentID->CurrentValue = NULL;
		$this->StudentID->OldValue = $this->StudentID->CurrentValue;
		$this->TahunID->CurrentValue = NULL;
		$this->TahunID->OldValue = $this->TahunID->CurrentValue;
		$this->Sesi->CurrentValue = 1;
		$this->JadwalID->CurrentValue = 0;
		$this->MKID->CurrentValue = 0;
		$this->MKKode->CurrentValue = NULL;
		$this->MKKode->OldValue = $this->MKKode->CurrentValue;
		$this->SKS->CurrentValue = 0;
		$this->Tugas1->CurrentValue = 0;
		$this->Tugas2->CurrentValue = 0;
		$this->Tugas3->CurrentValue = 0;
		$this->Tugas4->CurrentValue = 0;
		$this->Tugas5->CurrentValue = 0;
		$this->Presensi->CurrentValue = 0;
		$this->_Presensi->CurrentValue = 0;
		$this->UTS->CurrentValue = 0;
		$this->UAS->CurrentValue = 0;
		$this->Responsi->CurrentValue = 0.00;
		$this->NilaiAkhir->CurrentValue = 0.00;
		$this->GradeNilai->CurrentValue = "-";
		$this->BobotNilai->CurrentValue = 0.00;
		$this->StatusKRSID->CurrentValue = "A";
		$this->Tinggi->CurrentValue = NULL;
		$this->Tinggi->OldValue = $this->Tinggi->CurrentValue;
		$this->Final->CurrentValue = "N";
		$this->Setara->CurrentValue = "N";
		$this->Creator->CurrentValue = NULL;
		$this->Creator->OldValue = $this->Creator->CurrentValue;
		$this->CreateDate->CurrentValue = NULL;
		$this->CreateDate->OldValue = $this->CreateDate->CurrentValue;
		$this->Editor->CurrentValue = NULL;
		$this->Editor->OldValue = $this->Editor->CurrentValue;
		$this->EditDate->CurrentValue = NULL;
		$this->EditDate->OldValue = $this->EditDate->CurrentValue;
		$this->NA->CurrentValue = "N";
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->KHSID->FldIsDetailKey) {
			$this->KHSID->setFormValue($objForm->GetValue("x_KHSID"));
		}
		if (!$this->StudentID->FldIsDetailKey) {
			$this->StudentID->setFormValue($objForm->GetValue("x_StudentID"));
		}
		if (!$this->TahunID->FldIsDetailKey) {
			$this->TahunID->setFormValue($objForm->GetValue("x_TahunID"));
		}
		if (!$this->Sesi->FldIsDetailKey) {
			$this->Sesi->setFormValue($objForm->GetValue("x_Sesi"));
		}
		if (!$this->JadwalID->FldIsDetailKey) {
			$this->JadwalID->setFormValue($objForm->GetValue("x_JadwalID"));
		}
		if (!$this->MKID->FldIsDetailKey) {
			$this->MKID->setFormValue($objForm->GetValue("x_MKID"));
		}
		if (!$this->MKKode->FldIsDetailKey) {
			$this->MKKode->setFormValue($objForm->GetValue("x_MKKode"));
		}
		if (!$this->SKS->FldIsDetailKey) {
			$this->SKS->setFormValue($objForm->GetValue("x_SKS"));
		}
		if (!$this->Tugas1->FldIsDetailKey) {
			$this->Tugas1->setFormValue($objForm->GetValue("x_Tugas1"));
		}
		if (!$this->Tugas2->FldIsDetailKey) {
			$this->Tugas2->setFormValue($objForm->GetValue("x_Tugas2"));
		}
		if (!$this->Tugas3->FldIsDetailKey) {
			$this->Tugas3->setFormValue($objForm->GetValue("x_Tugas3"));
		}
		if (!$this->Tugas4->FldIsDetailKey) {
			$this->Tugas4->setFormValue($objForm->GetValue("x_Tugas4"));
		}
		if (!$this->Tugas5->FldIsDetailKey) {
			$this->Tugas5->setFormValue($objForm->GetValue("x_Tugas5"));
		}
		if (!$this->Presensi->FldIsDetailKey) {
			$this->Presensi->setFormValue($objForm->GetValue("x_Presensi"));
		}
		if (!$this->_Presensi->FldIsDetailKey) {
			$this->_Presensi->setFormValue($objForm->GetValue("x__Presensi"));
		}
		if (!$this->UTS->FldIsDetailKey) {
			$this->UTS->setFormValue($objForm->GetValue("x_UTS"));
		}
		if (!$this->UAS->FldIsDetailKey) {
			$this->UAS->setFormValue($objForm->GetValue("x_UAS"));
		}
		if (!$this->Responsi->FldIsDetailKey) {
			$this->Responsi->setFormValue($objForm->GetValue("x_Responsi"));
		}
		if (!$this->NilaiAkhir->FldIsDetailKey) {
			$this->NilaiAkhir->setFormValue($objForm->GetValue("x_NilaiAkhir"));
		}
		if (!$this->GradeNilai->FldIsDetailKey) {
			$this->GradeNilai->setFormValue($objForm->GetValue("x_GradeNilai"));
		}
		if (!$this->BobotNilai->FldIsDetailKey) {
			$this->BobotNilai->setFormValue($objForm->GetValue("x_BobotNilai"));
		}
		if (!$this->StatusKRSID->FldIsDetailKey) {
			$this->StatusKRSID->setFormValue($objForm->GetValue("x_StatusKRSID"));
		}
		if (!$this->Tinggi->FldIsDetailKey) {
			$this->Tinggi->setFormValue($objForm->GetValue("x_Tinggi"));
		}
		if (!$this->Final->FldIsDetailKey) {
			$this->Final->setFormValue($objForm->GetValue("x_Final"));
		}
		if (!$this->Setara->FldIsDetailKey) {
			$this->Setara->setFormValue($objForm->GetValue("x_Setara"));
		}
		if (!$this->Creator->FldIsDetailKey) {
			$this->Creator->setFormValue($objForm->GetValue("x_Creator"));
		}
		if (!$this->CreateDate->FldIsDetailKey) {
			$this->CreateDate->setFormValue($objForm->GetValue("x_CreateDate"));
			$this->CreateDate->CurrentValue = ew_UnFormatDateTime($this->CreateDate->CurrentValue, 0);
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
		$this->LoadOldRecord();
		$this->KHSID->CurrentValue = $this->KHSID->FormValue;
		$this->StudentID->CurrentValue = $this->StudentID->FormValue;
		$this->TahunID->CurrentValue = $this->TahunID->FormValue;
		$this->Sesi->CurrentValue = $this->Sesi->FormValue;
		$this->JadwalID->CurrentValue = $this->JadwalID->FormValue;
		$this->MKID->CurrentValue = $this->MKID->FormValue;
		$this->MKKode->CurrentValue = $this->MKKode->FormValue;
		$this->SKS->CurrentValue = $this->SKS->FormValue;
		$this->Tugas1->CurrentValue = $this->Tugas1->FormValue;
		$this->Tugas2->CurrentValue = $this->Tugas2->FormValue;
		$this->Tugas3->CurrentValue = $this->Tugas3->FormValue;
		$this->Tugas4->CurrentValue = $this->Tugas4->FormValue;
		$this->Tugas5->CurrentValue = $this->Tugas5->FormValue;
		$this->Presensi->CurrentValue = $this->Presensi->FormValue;
		$this->_Presensi->CurrentValue = $this->_Presensi->FormValue;
		$this->UTS->CurrentValue = $this->UTS->FormValue;
		$this->UAS->CurrentValue = $this->UAS->FormValue;
		$this->Responsi->CurrentValue = $this->Responsi->FormValue;
		$this->NilaiAkhir->CurrentValue = $this->NilaiAkhir->FormValue;
		$this->GradeNilai->CurrentValue = $this->GradeNilai->FormValue;
		$this->BobotNilai->CurrentValue = $this->BobotNilai->FormValue;
		$this->StatusKRSID->CurrentValue = $this->StatusKRSID->FormValue;
		$this->Tinggi->CurrentValue = $this->Tinggi->FormValue;
		$this->Final->CurrentValue = $this->Final->FormValue;
		$this->Setara->CurrentValue = $this->Setara->FormValue;
		$this->Creator->CurrentValue = $this->Creator->FormValue;
		$this->CreateDate->CurrentValue = $this->CreateDate->FormValue;
		$this->CreateDate->CurrentValue = ew_UnFormatDateTime($this->CreateDate->CurrentValue, 0);
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
		$this->KRSID->setDbValue($rs->fields('KRSID'));
		$this->KHSID->setDbValue($rs->fields('KHSID'));
		$this->StudentID->setDbValue($rs->fields('StudentID'));
		$this->TahunID->setDbValue($rs->fields('TahunID'));
		$this->Sesi->setDbValue($rs->fields('Sesi'));
		$this->JadwalID->setDbValue($rs->fields('JadwalID'));
		$this->MKID->setDbValue($rs->fields('MKID'));
		$this->MKKode->setDbValue($rs->fields('MKKode'));
		$this->SKS->setDbValue($rs->fields('SKS'));
		$this->Tugas1->setDbValue($rs->fields('Tugas1'));
		$this->Tugas2->setDbValue($rs->fields('Tugas2'));
		$this->Tugas3->setDbValue($rs->fields('Tugas3'));
		$this->Tugas4->setDbValue($rs->fields('Tugas4'));
		$this->Tugas5->setDbValue($rs->fields('Tugas5'));
		$this->Presensi->setDbValue($rs->fields('Presensi'));
		$this->_Presensi->setDbValue($rs->fields('_Presensi'));
		$this->UTS->setDbValue($rs->fields('UTS'));
		$this->UAS->setDbValue($rs->fields('UAS'));
		$this->Responsi->setDbValue($rs->fields('Responsi'));
		$this->NilaiAkhir->setDbValue($rs->fields('NilaiAkhir'));
		$this->GradeNilai->setDbValue($rs->fields('GradeNilai'));
		$this->BobotNilai->setDbValue($rs->fields('BobotNilai'));
		$this->StatusKRSID->setDbValue($rs->fields('StatusKRSID'));
		$this->Tinggi->setDbValue($rs->fields('Tinggi'));
		$this->Final->setDbValue($rs->fields('Final'));
		$this->Setara->setDbValue($rs->fields('Setara'));
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
		$this->KRSID->DbValue = $row['KRSID'];
		$this->KHSID->DbValue = $row['KHSID'];
		$this->StudentID->DbValue = $row['StudentID'];
		$this->TahunID->DbValue = $row['TahunID'];
		$this->Sesi->DbValue = $row['Sesi'];
		$this->JadwalID->DbValue = $row['JadwalID'];
		$this->MKID->DbValue = $row['MKID'];
		$this->MKKode->DbValue = $row['MKKode'];
		$this->SKS->DbValue = $row['SKS'];
		$this->Tugas1->DbValue = $row['Tugas1'];
		$this->Tugas2->DbValue = $row['Tugas2'];
		$this->Tugas3->DbValue = $row['Tugas3'];
		$this->Tugas4->DbValue = $row['Tugas4'];
		$this->Tugas5->DbValue = $row['Tugas5'];
		$this->Presensi->DbValue = $row['Presensi'];
		$this->_Presensi->DbValue = $row['_Presensi'];
		$this->UTS->DbValue = $row['UTS'];
		$this->UAS->DbValue = $row['UAS'];
		$this->Responsi->DbValue = $row['Responsi'];
		$this->NilaiAkhir->DbValue = $row['NilaiAkhir'];
		$this->GradeNilai->DbValue = $row['GradeNilai'];
		$this->BobotNilai->DbValue = $row['BobotNilai'];
		$this->StatusKRSID->DbValue = $row['StatusKRSID'];
		$this->Tinggi->DbValue = $row['Tinggi'];
		$this->Final->DbValue = $row['Final'];
		$this->Setara->DbValue = $row['Setara'];
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
		if (strval($this->getKey("KRSID")) <> "")
			$this->KRSID->CurrentValue = $this->getKey("KRSID"); // KRSID
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
		// Convert decimal values if posted back

		if ($this->Responsi->FormValue == $this->Responsi->CurrentValue && is_numeric(ew_StrToFloat($this->Responsi->CurrentValue)))
			$this->Responsi->CurrentValue = ew_StrToFloat($this->Responsi->CurrentValue);

		// Convert decimal values if posted back
		if ($this->NilaiAkhir->FormValue == $this->NilaiAkhir->CurrentValue && is_numeric(ew_StrToFloat($this->NilaiAkhir->CurrentValue)))
			$this->NilaiAkhir->CurrentValue = ew_StrToFloat($this->NilaiAkhir->CurrentValue);

		// Convert decimal values if posted back
		if ($this->BobotNilai->FormValue == $this->BobotNilai->CurrentValue && is_numeric(ew_StrToFloat($this->BobotNilai->CurrentValue)))
			$this->BobotNilai->CurrentValue = ew_StrToFloat($this->BobotNilai->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// KRSID
		// KHSID
		// StudentID
		// TahunID
		// Sesi
		// JadwalID
		// MKID
		// MKKode
		// SKS
		// Tugas1
		// Tugas2
		// Tugas3
		// Tugas4
		// Tugas5
		// Presensi
		// _Presensi
		// UTS
		// UAS
		// Responsi
		// NilaiAkhir
		// GradeNilai
		// BobotNilai
		// StatusKRSID
		// Tinggi
		// Final
		// Setara
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// KRSID
		$this->KRSID->ViewValue = $this->KRSID->CurrentValue;
		$this->KRSID->ViewCustomAttributes = "";

		// KHSID
		$this->KHSID->ViewValue = $this->KHSID->CurrentValue;
		$this->KHSID->ViewCustomAttributes = "";

		// StudentID
		$this->StudentID->ViewValue = $this->StudentID->CurrentValue;
		$this->StudentID->ViewCustomAttributes = "";

		// TahunID
		$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
		$this->TahunID->ViewCustomAttributes = "";

		// Sesi
		$this->Sesi->ViewValue = $this->Sesi->CurrentValue;
		$this->Sesi->ViewCustomAttributes = "";

		// JadwalID
		$this->JadwalID->ViewValue = $this->JadwalID->CurrentValue;
		$this->JadwalID->ViewCustomAttributes = "";

		// MKID
		$this->MKID->ViewValue = $this->MKID->CurrentValue;
		$this->MKID->ViewCustomAttributes = "";

		// MKKode
		$this->MKKode->ViewValue = $this->MKKode->CurrentValue;
		$this->MKKode->ViewCustomAttributes = "";

		// SKS
		$this->SKS->ViewValue = $this->SKS->CurrentValue;
		$this->SKS->ViewCustomAttributes = "";

		// Tugas1
		$this->Tugas1->ViewValue = $this->Tugas1->CurrentValue;
		$this->Tugas1->ViewCustomAttributes = "";

		// Tugas2
		$this->Tugas2->ViewValue = $this->Tugas2->CurrentValue;
		$this->Tugas2->ViewCustomAttributes = "";

		// Tugas3
		$this->Tugas3->ViewValue = $this->Tugas3->CurrentValue;
		$this->Tugas3->ViewCustomAttributes = "";

		// Tugas4
		$this->Tugas4->ViewValue = $this->Tugas4->CurrentValue;
		$this->Tugas4->ViewCustomAttributes = "";

		// Tugas5
		$this->Tugas5->ViewValue = $this->Tugas5->CurrentValue;
		$this->Tugas5->ViewCustomAttributes = "";

		// Presensi
		$this->Presensi->ViewValue = $this->Presensi->CurrentValue;
		$this->Presensi->ViewCustomAttributes = "";

		// _Presensi
		$this->_Presensi->ViewValue = $this->_Presensi->CurrentValue;
		$this->_Presensi->ViewCustomAttributes = "";

		// UTS
		$this->UTS->ViewValue = $this->UTS->CurrentValue;
		$this->UTS->ViewCustomAttributes = "";

		// UAS
		$this->UAS->ViewValue = $this->UAS->CurrentValue;
		$this->UAS->ViewCustomAttributes = "";

		// Responsi
		$this->Responsi->ViewValue = $this->Responsi->CurrentValue;
		$this->Responsi->ViewCustomAttributes = "";

		// NilaiAkhir
		$this->NilaiAkhir->ViewValue = $this->NilaiAkhir->CurrentValue;
		$this->NilaiAkhir->ViewCustomAttributes = "";

		// GradeNilai
		$this->GradeNilai->ViewValue = $this->GradeNilai->CurrentValue;
		$this->GradeNilai->ViewCustomAttributes = "";

		// BobotNilai
		$this->BobotNilai->ViewValue = $this->BobotNilai->CurrentValue;
		$this->BobotNilai->ViewCustomAttributes = "";

		// StatusKRSID
		$this->StatusKRSID->ViewValue = $this->StatusKRSID->CurrentValue;
		$this->StatusKRSID->ViewCustomAttributes = "";

		// Tinggi
		$this->Tinggi->ViewValue = $this->Tinggi->CurrentValue;
		$this->Tinggi->ViewCustomAttributes = "";

		// Final
		if (ew_ConvertToBool($this->Final->CurrentValue)) {
			$this->Final->ViewValue = $this->Final->FldTagCaption(1) <> "" ? $this->Final->FldTagCaption(1) : "Y";
		} else {
			$this->Final->ViewValue = $this->Final->FldTagCaption(2) <> "" ? $this->Final->FldTagCaption(2) : "N";
		}
		$this->Final->ViewCustomAttributes = "";

		// Setara
		if (ew_ConvertToBool($this->Setara->CurrentValue)) {
			$this->Setara->ViewValue = $this->Setara->FldTagCaption(1) <> "" ? $this->Setara->FldTagCaption(1) : "Y";
		} else {
			$this->Setara->ViewValue = $this->Setara->FldTagCaption(2) <> "" ? $this->Setara->FldTagCaption(2) : "N";
		}
		$this->Setara->ViewCustomAttributes = "";

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

			// KHSID
			$this->KHSID->LinkCustomAttributes = "";
			$this->KHSID->HrefValue = "";
			$this->KHSID->TooltipValue = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";
			$this->StudentID->TooltipValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";
			$this->TahunID->TooltipValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";
			$this->Sesi->TooltipValue = "";

			// JadwalID
			$this->JadwalID->LinkCustomAttributes = "";
			$this->JadwalID->HrefValue = "";
			$this->JadwalID->TooltipValue = "";

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";
			$this->MKID->TooltipValue = "";

			// MKKode
			$this->MKKode->LinkCustomAttributes = "";
			$this->MKKode->HrefValue = "";
			$this->MKKode->TooltipValue = "";

			// SKS
			$this->SKS->LinkCustomAttributes = "";
			$this->SKS->HrefValue = "";
			$this->SKS->TooltipValue = "";

			// Tugas1
			$this->Tugas1->LinkCustomAttributes = "";
			$this->Tugas1->HrefValue = "";
			$this->Tugas1->TooltipValue = "";

			// Tugas2
			$this->Tugas2->LinkCustomAttributes = "";
			$this->Tugas2->HrefValue = "";
			$this->Tugas2->TooltipValue = "";

			// Tugas3
			$this->Tugas3->LinkCustomAttributes = "";
			$this->Tugas3->HrefValue = "";
			$this->Tugas3->TooltipValue = "";

			// Tugas4
			$this->Tugas4->LinkCustomAttributes = "";
			$this->Tugas4->HrefValue = "";
			$this->Tugas4->TooltipValue = "";

			// Tugas5
			$this->Tugas5->LinkCustomAttributes = "";
			$this->Tugas5->HrefValue = "";
			$this->Tugas5->TooltipValue = "";

			// Presensi
			$this->Presensi->LinkCustomAttributes = "";
			$this->Presensi->HrefValue = "";
			$this->Presensi->TooltipValue = "";

			// _Presensi
			$this->_Presensi->LinkCustomAttributes = "";
			$this->_Presensi->HrefValue = "";
			$this->_Presensi->TooltipValue = "";

			// UTS
			$this->UTS->LinkCustomAttributes = "";
			$this->UTS->HrefValue = "";
			$this->UTS->TooltipValue = "";

			// UAS
			$this->UAS->LinkCustomAttributes = "";
			$this->UAS->HrefValue = "";
			$this->UAS->TooltipValue = "";

			// Responsi
			$this->Responsi->LinkCustomAttributes = "";
			$this->Responsi->HrefValue = "";
			$this->Responsi->TooltipValue = "";

			// NilaiAkhir
			$this->NilaiAkhir->LinkCustomAttributes = "";
			$this->NilaiAkhir->HrefValue = "";
			$this->NilaiAkhir->TooltipValue = "";

			// GradeNilai
			$this->GradeNilai->LinkCustomAttributes = "";
			$this->GradeNilai->HrefValue = "";
			$this->GradeNilai->TooltipValue = "";

			// BobotNilai
			$this->BobotNilai->LinkCustomAttributes = "";
			$this->BobotNilai->HrefValue = "";
			$this->BobotNilai->TooltipValue = "";

			// StatusKRSID
			$this->StatusKRSID->LinkCustomAttributes = "";
			$this->StatusKRSID->HrefValue = "";
			$this->StatusKRSID->TooltipValue = "";

			// Tinggi
			$this->Tinggi->LinkCustomAttributes = "";
			$this->Tinggi->HrefValue = "";
			$this->Tinggi->TooltipValue = "";

			// Final
			$this->Final->LinkCustomAttributes = "";
			$this->Final->HrefValue = "";
			$this->Final->TooltipValue = "";

			// Setara
			$this->Setara->LinkCustomAttributes = "";
			$this->Setara->HrefValue = "";
			$this->Setara->TooltipValue = "";

			// Creator
			$this->Creator->LinkCustomAttributes = "";
			$this->Creator->HrefValue = "";
			$this->Creator->TooltipValue = "";

			// CreateDate
			$this->CreateDate->LinkCustomAttributes = "";
			$this->CreateDate->HrefValue = "";
			$this->CreateDate->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// KHSID
			$this->KHSID->EditAttrs["class"] = "form-control";
			$this->KHSID->EditCustomAttributes = "";
			$this->KHSID->EditValue = ew_HtmlEncode($this->KHSID->CurrentValue);
			$this->KHSID->PlaceHolder = ew_RemoveHtml($this->KHSID->FldCaption());

			// StudentID
			$this->StudentID->EditAttrs["class"] = "form-control";
			$this->StudentID->EditCustomAttributes = "";
			$this->StudentID->EditValue = ew_HtmlEncode($this->StudentID->CurrentValue);
			$this->StudentID->PlaceHolder = ew_RemoveHtml($this->StudentID->FldCaption());

			// TahunID
			$this->TahunID->EditAttrs["class"] = "form-control";
			$this->TahunID->EditCustomAttributes = "";
			$this->TahunID->EditValue = ew_HtmlEncode($this->TahunID->CurrentValue);
			$this->TahunID->PlaceHolder = ew_RemoveHtml($this->TahunID->FldCaption());

			// Sesi
			$this->Sesi->EditAttrs["class"] = "form-control";
			$this->Sesi->EditCustomAttributes = "";
			$this->Sesi->EditValue = ew_HtmlEncode($this->Sesi->CurrentValue);
			$this->Sesi->PlaceHolder = ew_RemoveHtml($this->Sesi->FldCaption());

			// JadwalID
			$this->JadwalID->EditAttrs["class"] = "form-control";
			$this->JadwalID->EditCustomAttributes = "";
			$this->JadwalID->EditValue = ew_HtmlEncode($this->JadwalID->CurrentValue);
			$this->JadwalID->PlaceHolder = ew_RemoveHtml($this->JadwalID->FldCaption());

			// MKID
			$this->MKID->EditAttrs["class"] = "form-control";
			$this->MKID->EditCustomAttributes = "";
			$this->MKID->EditValue = ew_HtmlEncode($this->MKID->CurrentValue);
			$this->MKID->PlaceHolder = ew_RemoveHtml($this->MKID->FldCaption());

			// MKKode
			$this->MKKode->EditAttrs["class"] = "form-control";
			$this->MKKode->EditCustomAttributes = "";
			$this->MKKode->EditValue = ew_HtmlEncode($this->MKKode->CurrentValue);
			$this->MKKode->PlaceHolder = ew_RemoveHtml($this->MKKode->FldCaption());

			// SKS
			$this->SKS->EditAttrs["class"] = "form-control";
			$this->SKS->EditCustomAttributes = "";
			$this->SKS->EditValue = ew_HtmlEncode($this->SKS->CurrentValue);
			$this->SKS->PlaceHolder = ew_RemoveHtml($this->SKS->FldCaption());

			// Tugas1
			$this->Tugas1->EditAttrs["class"] = "form-control";
			$this->Tugas1->EditCustomAttributes = "";
			$this->Tugas1->EditValue = ew_HtmlEncode($this->Tugas1->CurrentValue);
			$this->Tugas1->PlaceHolder = ew_RemoveHtml($this->Tugas1->FldCaption());

			// Tugas2
			$this->Tugas2->EditAttrs["class"] = "form-control";
			$this->Tugas2->EditCustomAttributes = "";
			$this->Tugas2->EditValue = ew_HtmlEncode($this->Tugas2->CurrentValue);
			$this->Tugas2->PlaceHolder = ew_RemoveHtml($this->Tugas2->FldCaption());

			// Tugas3
			$this->Tugas3->EditAttrs["class"] = "form-control";
			$this->Tugas3->EditCustomAttributes = "";
			$this->Tugas3->EditValue = ew_HtmlEncode($this->Tugas3->CurrentValue);
			$this->Tugas3->PlaceHolder = ew_RemoveHtml($this->Tugas3->FldCaption());

			// Tugas4
			$this->Tugas4->EditAttrs["class"] = "form-control";
			$this->Tugas4->EditCustomAttributes = "";
			$this->Tugas4->EditValue = ew_HtmlEncode($this->Tugas4->CurrentValue);
			$this->Tugas4->PlaceHolder = ew_RemoveHtml($this->Tugas4->FldCaption());

			// Tugas5
			$this->Tugas5->EditAttrs["class"] = "form-control";
			$this->Tugas5->EditCustomAttributes = "";
			$this->Tugas5->EditValue = ew_HtmlEncode($this->Tugas5->CurrentValue);
			$this->Tugas5->PlaceHolder = ew_RemoveHtml($this->Tugas5->FldCaption());

			// Presensi
			$this->Presensi->EditAttrs["class"] = "form-control";
			$this->Presensi->EditCustomAttributes = "";
			$this->Presensi->EditValue = ew_HtmlEncode($this->Presensi->CurrentValue);
			$this->Presensi->PlaceHolder = ew_RemoveHtml($this->Presensi->FldCaption());

			// _Presensi
			$this->_Presensi->EditAttrs["class"] = "form-control";
			$this->_Presensi->EditCustomAttributes = "";
			$this->_Presensi->EditValue = ew_HtmlEncode($this->_Presensi->CurrentValue);
			$this->_Presensi->PlaceHolder = ew_RemoveHtml($this->_Presensi->FldCaption());

			// UTS
			$this->UTS->EditAttrs["class"] = "form-control";
			$this->UTS->EditCustomAttributes = "";
			$this->UTS->EditValue = ew_HtmlEncode($this->UTS->CurrentValue);
			$this->UTS->PlaceHolder = ew_RemoveHtml($this->UTS->FldCaption());

			// UAS
			$this->UAS->EditAttrs["class"] = "form-control";
			$this->UAS->EditCustomAttributes = "";
			$this->UAS->EditValue = ew_HtmlEncode($this->UAS->CurrentValue);
			$this->UAS->PlaceHolder = ew_RemoveHtml($this->UAS->FldCaption());

			// Responsi
			$this->Responsi->EditAttrs["class"] = "form-control";
			$this->Responsi->EditCustomAttributes = "";
			$this->Responsi->EditValue = ew_HtmlEncode($this->Responsi->CurrentValue);
			$this->Responsi->PlaceHolder = ew_RemoveHtml($this->Responsi->FldCaption());
			if (strval($this->Responsi->EditValue) <> "" && is_numeric($this->Responsi->EditValue)) $this->Responsi->EditValue = ew_FormatNumber($this->Responsi->EditValue, -2, -1, -2, 0);

			// NilaiAkhir
			$this->NilaiAkhir->EditAttrs["class"] = "form-control";
			$this->NilaiAkhir->EditCustomAttributes = "";
			$this->NilaiAkhir->EditValue = ew_HtmlEncode($this->NilaiAkhir->CurrentValue);
			$this->NilaiAkhir->PlaceHolder = ew_RemoveHtml($this->NilaiAkhir->FldCaption());
			if (strval($this->NilaiAkhir->EditValue) <> "" && is_numeric($this->NilaiAkhir->EditValue)) $this->NilaiAkhir->EditValue = ew_FormatNumber($this->NilaiAkhir->EditValue, -2, -1, -2, 0);

			// GradeNilai
			$this->GradeNilai->EditAttrs["class"] = "form-control";
			$this->GradeNilai->EditCustomAttributes = "";
			$this->GradeNilai->EditValue = ew_HtmlEncode($this->GradeNilai->CurrentValue);
			$this->GradeNilai->PlaceHolder = ew_RemoveHtml($this->GradeNilai->FldCaption());

			// BobotNilai
			$this->BobotNilai->EditAttrs["class"] = "form-control";
			$this->BobotNilai->EditCustomAttributes = "";
			$this->BobotNilai->EditValue = ew_HtmlEncode($this->BobotNilai->CurrentValue);
			$this->BobotNilai->PlaceHolder = ew_RemoveHtml($this->BobotNilai->FldCaption());
			if (strval($this->BobotNilai->EditValue) <> "" && is_numeric($this->BobotNilai->EditValue)) $this->BobotNilai->EditValue = ew_FormatNumber($this->BobotNilai->EditValue, -2, -1, -2, 0);

			// StatusKRSID
			$this->StatusKRSID->EditAttrs["class"] = "form-control";
			$this->StatusKRSID->EditCustomAttributes = "";
			$this->StatusKRSID->EditValue = ew_HtmlEncode($this->StatusKRSID->CurrentValue);
			$this->StatusKRSID->PlaceHolder = ew_RemoveHtml($this->StatusKRSID->FldCaption());

			// Tinggi
			$this->Tinggi->EditAttrs["class"] = "form-control";
			$this->Tinggi->EditCustomAttributes = "";
			$this->Tinggi->EditValue = ew_HtmlEncode($this->Tinggi->CurrentValue);
			$this->Tinggi->PlaceHolder = ew_RemoveHtml($this->Tinggi->FldCaption());

			// Final
			$this->Final->EditCustomAttributes = "";
			$this->Final->EditValue = $this->Final->Options(FALSE);

			// Setara
			$this->Setara->EditCustomAttributes = "";
			$this->Setara->EditValue = $this->Setara->Options(FALSE);

			// Creator
			$this->Creator->EditAttrs["class"] = "form-control";
			$this->Creator->EditCustomAttributes = "";
			$this->Creator->EditValue = ew_HtmlEncode($this->Creator->CurrentValue);
			$this->Creator->PlaceHolder = ew_RemoveHtml($this->Creator->FldCaption());

			// CreateDate
			$this->CreateDate->EditAttrs["class"] = "form-control";
			$this->CreateDate->EditCustomAttributes = "";
			$this->CreateDate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->CreateDate->CurrentValue, 8));
			$this->CreateDate->PlaceHolder = ew_RemoveHtml($this->CreateDate->FldCaption());

			// Editor
			$this->Editor->EditAttrs["class"] = "form-control";
			$this->Editor->EditCustomAttributes = "";
			$this->Editor->EditValue = ew_HtmlEncode($this->Editor->CurrentValue);
			$this->Editor->PlaceHolder = ew_RemoveHtml($this->Editor->FldCaption());

			// EditDate
			$this->EditDate->EditAttrs["class"] = "form-control";
			$this->EditDate->EditCustomAttributes = "";
			$this->EditDate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->EditDate->CurrentValue, 8));
			$this->EditDate->PlaceHolder = ew_RemoveHtml($this->EditDate->FldCaption());

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// KHSID

			$this->KHSID->LinkCustomAttributes = "";
			$this->KHSID->HrefValue = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";

			// JadwalID
			$this->JadwalID->LinkCustomAttributes = "";
			$this->JadwalID->HrefValue = "";

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";

			// MKKode
			$this->MKKode->LinkCustomAttributes = "";
			$this->MKKode->HrefValue = "";

			// SKS
			$this->SKS->LinkCustomAttributes = "";
			$this->SKS->HrefValue = "";

			// Tugas1
			$this->Tugas1->LinkCustomAttributes = "";
			$this->Tugas1->HrefValue = "";

			// Tugas2
			$this->Tugas2->LinkCustomAttributes = "";
			$this->Tugas2->HrefValue = "";

			// Tugas3
			$this->Tugas3->LinkCustomAttributes = "";
			$this->Tugas3->HrefValue = "";

			// Tugas4
			$this->Tugas4->LinkCustomAttributes = "";
			$this->Tugas4->HrefValue = "";

			// Tugas5
			$this->Tugas5->LinkCustomAttributes = "";
			$this->Tugas5->HrefValue = "";

			// Presensi
			$this->Presensi->LinkCustomAttributes = "";
			$this->Presensi->HrefValue = "";

			// _Presensi
			$this->_Presensi->LinkCustomAttributes = "";
			$this->_Presensi->HrefValue = "";

			// UTS
			$this->UTS->LinkCustomAttributes = "";
			$this->UTS->HrefValue = "";

			// UAS
			$this->UAS->LinkCustomAttributes = "";
			$this->UAS->HrefValue = "";

			// Responsi
			$this->Responsi->LinkCustomAttributes = "";
			$this->Responsi->HrefValue = "";

			// NilaiAkhir
			$this->NilaiAkhir->LinkCustomAttributes = "";
			$this->NilaiAkhir->HrefValue = "";

			// GradeNilai
			$this->GradeNilai->LinkCustomAttributes = "";
			$this->GradeNilai->HrefValue = "";

			// BobotNilai
			$this->BobotNilai->LinkCustomAttributes = "";
			$this->BobotNilai->HrefValue = "";

			// StatusKRSID
			$this->StatusKRSID->LinkCustomAttributes = "";
			$this->StatusKRSID->HrefValue = "";

			// Tinggi
			$this->Tinggi->LinkCustomAttributes = "";
			$this->Tinggi->HrefValue = "";

			// Final
			$this->Final->LinkCustomAttributes = "";
			$this->Final->HrefValue = "";

			// Setara
			$this->Setara->LinkCustomAttributes = "";
			$this->Setara->HrefValue = "";

			// Creator
			$this->Creator->LinkCustomAttributes = "";
			$this->Creator->HrefValue = "";

			// CreateDate
			$this->CreateDate->LinkCustomAttributes = "";
			$this->CreateDate->HrefValue = "";

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
		if (!$this->KHSID->FldIsDetailKey && !is_null($this->KHSID->FormValue) && $this->KHSID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->KHSID->FldCaption(), $this->KHSID->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->KHSID->FormValue)) {
			ew_AddMessage($gsFormError, $this->KHSID->FldErrMsg());
		}
		if (!$this->StudentID->FldIsDetailKey && !is_null($this->StudentID->FormValue) && $this->StudentID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->StudentID->FldCaption(), $this->StudentID->ReqErrMsg));
		}
		if (!$this->TahunID->FldIsDetailKey && !is_null($this->TahunID->FormValue) && $this->TahunID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TahunID->FldCaption(), $this->TahunID->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->Sesi->FormValue)) {
			ew_AddMessage($gsFormError, $this->Sesi->FldErrMsg());
		}
		if (!ew_CheckInteger($this->JadwalID->FormValue)) {
			ew_AddMessage($gsFormError, $this->JadwalID->FldErrMsg());
		}
		if (!ew_CheckInteger($this->MKID->FormValue)) {
			ew_AddMessage($gsFormError, $this->MKID->FldErrMsg());
		}
		if (!ew_CheckInteger($this->SKS->FormValue)) {
			ew_AddMessage($gsFormError, $this->SKS->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Tugas1->FormValue)) {
			ew_AddMessage($gsFormError, $this->Tugas1->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Tugas2->FormValue)) {
			ew_AddMessage($gsFormError, $this->Tugas2->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Tugas3->FormValue)) {
			ew_AddMessage($gsFormError, $this->Tugas3->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Tugas4->FormValue)) {
			ew_AddMessage($gsFormError, $this->Tugas4->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Tugas5->FormValue)) {
			ew_AddMessage($gsFormError, $this->Tugas5->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Presensi->FormValue)) {
			ew_AddMessage($gsFormError, $this->Presensi->FldErrMsg());
		}
		if (!ew_CheckInteger($this->_Presensi->FormValue)) {
			ew_AddMessage($gsFormError, $this->_Presensi->FldErrMsg());
		}
		if (!ew_CheckInteger($this->UTS->FormValue)) {
			ew_AddMessage($gsFormError, $this->UTS->FldErrMsg());
		}
		if (!ew_CheckInteger($this->UAS->FormValue)) {
			ew_AddMessage($gsFormError, $this->UAS->FldErrMsg());
		}
		if (!ew_CheckNumber($this->Responsi->FormValue)) {
			ew_AddMessage($gsFormError, $this->Responsi->FldErrMsg());
		}
		if (!ew_CheckNumber($this->NilaiAkhir->FormValue)) {
			ew_AddMessage($gsFormError, $this->NilaiAkhir->FldErrMsg());
		}
		if (!ew_CheckNumber($this->BobotNilai->FormValue)) {
			ew_AddMessage($gsFormError, $this->BobotNilai->FldErrMsg());
		}
		if (!$this->StatusKRSID->FldIsDetailKey && !is_null($this->StatusKRSID->FormValue) && $this->StatusKRSID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->StatusKRSID->FldCaption(), $this->StatusKRSID->ReqErrMsg));
		}
		if (!ew_CheckDateDef($this->CreateDate->FormValue)) {
			ew_AddMessage($gsFormError, $this->CreateDate->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->EditDate->FormValue)) {
			ew_AddMessage($gsFormError, $this->EditDate->FldErrMsg());
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

		// KHSID
		$this->KHSID->SetDbValueDef($rsnew, $this->KHSID->CurrentValue, 0, strval($this->KHSID->CurrentValue) == "");

		// StudentID
		$this->StudentID->SetDbValueDef($rsnew, $this->StudentID->CurrentValue, "", FALSE);

		// TahunID
		$this->TahunID->SetDbValueDef($rsnew, $this->TahunID->CurrentValue, "", FALSE);

		// Sesi
		$this->Sesi->SetDbValueDef($rsnew, $this->Sesi->CurrentValue, NULL, strval($this->Sesi->CurrentValue) == "");

		// JadwalID
		$this->JadwalID->SetDbValueDef($rsnew, $this->JadwalID->CurrentValue, NULL, strval($this->JadwalID->CurrentValue) == "");

		// MKID
		$this->MKID->SetDbValueDef($rsnew, $this->MKID->CurrentValue, NULL, strval($this->MKID->CurrentValue) == "");

		// MKKode
		$this->MKKode->SetDbValueDef($rsnew, $this->MKKode->CurrentValue, NULL, FALSE);

		// SKS
		$this->SKS->SetDbValueDef($rsnew, $this->SKS->CurrentValue, NULL, strval($this->SKS->CurrentValue) == "");

		// Tugas1
		$this->Tugas1->SetDbValueDef($rsnew, $this->Tugas1->CurrentValue, NULL, strval($this->Tugas1->CurrentValue) == "");

		// Tugas2
		$this->Tugas2->SetDbValueDef($rsnew, $this->Tugas2->CurrentValue, NULL, strval($this->Tugas2->CurrentValue) == "");

		// Tugas3
		$this->Tugas3->SetDbValueDef($rsnew, $this->Tugas3->CurrentValue, NULL, strval($this->Tugas3->CurrentValue) == "");

		// Tugas4
		$this->Tugas4->SetDbValueDef($rsnew, $this->Tugas4->CurrentValue, NULL, strval($this->Tugas4->CurrentValue) == "");

		// Tugas5
		$this->Tugas5->SetDbValueDef($rsnew, $this->Tugas5->CurrentValue, NULL, strval($this->Tugas5->CurrentValue) == "");

		// Presensi
		$this->Presensi->SetDbValueDef($rsnew, $this->Presensi->CurrentValue, NULL, strval($this->Presensi->CurrentValue) == "");

		// _Presensi
		$this->_Presensi->SetDbValueDef($rsnew, $this->_Presensi->CurrentValue, NULL, strval($this->_Presensi->CurrentValue) == "");

		// UTS
		$this->UTS->SetDbValueDef($rsnew, $this->UTS->CurrentValue, NULL, strval($this->UTS->CurrentValue) == "");

		// UAS
		$this->UAS->SetDbValueDef($rsnew, $this->UAS->CurrentValue, NULL, strval($this->UAS->CurrentValue) == "");

		// Responsi
		$this->Responsi->SetDbValueDef($rsnew, $this->Responsi->CurrentValue, NULL, strval($this->Responsi->CurrentValue) == "");

		// NilaiAkhir
		$this->NilaiAkhir->SetDbValueDef($rsnew, $this->NilaiAkhir->CurrentValue, NULL, strval($this->NilaiAkhir->CurrentValue) == "");

		// GradeNilai
		$this->GradeNilai->SetDbValueDef($rsnew, $this->GradeNilai->CurrentValue, NULL, strval($this->GradeNilai->CurrentValue) == "");

		// BobotNilai
		$this->BobotNilai->SetDbValueDef($rsnew, $this->BobotNilai->CurrentValue, NULL, strval($this->BobotNilai->CurrentValue) == "");

		// StatusKRSID
		$this->StatusKRSID->SetDbValueDef($rsnew, $this->StatusKRSID->CurrentValue, "", strval($this->StatusKRSID->CurrentValue) == "");

		// Tinggi
		$this->Tinggi->SetDbValueDef($rsnew, $this->Tinggi->CurrentValue, NULL, FALSE);

		// Final
		$this->Final->SetDbValueDef($rsnew, ((strval($this->Final->CurrentValue) == "Y") ? "Y" : "N"), NULL, strval($this->Final->CurrentValue) == "");

		// Setara
		$this->Setara->SetDbValueDef($rsnew, ((strval($this->Setara->CurrentValue) == "Y") ? "Y" : "N"), NULL, strval($this->Setara->CurrentValue) == "");

		// Creator
		$this->Creator->SetDbValueDef($rsnew, $this->Creator->CurrentValue, NULL, FALSE);

		// CreateDate
		$this->CreateDate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->CreateDate->CurrentValue, 0), NULL, FALSE);

		// Editor
		$this->Editor->SetDbValueDef($rsnew, $this->Editor->CurrentValue, NULL, FALSE);

		// EditDate
		$this->EditDate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->EditDate->CurrentValue, 0), NULL, FALSE);

		// NA
		$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), "N", strval($this->NA->CurrentValue) == "");

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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("krslist.php"), "", $this->TableVar, TRUE);
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
if (!isset($krs_add)) $krs_add = new ckrs_add();

// Page init
$krs_add->Page_Init();

// Page main
$krs_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$krs_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fkrsadd = new ew_Form("fkrsadd", "add");

// Validate form
fkrsadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_KHSID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $krs->KHSID->FldCaption(), $krs->KHSID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_KHSID");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->KHSID->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_StudentID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $krs->StudentID->FldCaption(), $krs->StudentID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TahunID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $krs->TahunID->FldCaption(), $krs->TahunID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Sesi");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Sesi->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_JadwalID");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->JadwalID->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_MKID");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->MKID->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_SKS");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->SKS->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Tugas1");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Tugas1->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Tugas2");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Tugas2->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Tugas3");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Tugas3->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Tugas4");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Tugas4->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Tugas5");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Tugas5->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Presensi");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Presensi->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "__Presensi");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->_Presensi->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_UTS");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->UTS->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_UAS");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->UAS->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Responsi");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Responsi->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_NilaiAkhir");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->NilaiAkhir->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_BobotNilai");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->BobotNilai->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_StatusKRSID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $krs->StatusKRSID->FldCaption(), $krs->StatusKRSID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_CreateDate");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->CreateDate->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_EditDate");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($krs->EditDate->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_NA");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $krs->NA->FldCaption(), $krs->NA->ReqErrMsg)) ?>");

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
fkrsadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fkrsadd.ValidateRequired = true;
<?php } else { ?>
fkrsadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fkrsadd.Lists["x_Final"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrsadd.Lists["x_Final"].Options = <?php echo json_encode($krs->Final->Options()) ?>;
fkrsadd.Lists["x_Setara"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrsadd.Lists["x_Setara"].Options = <?php echo json_encode($krs->Setara->Options()) ?>;
fkrsadd.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrsadd.Lists["x_NA"].Options = <?php echo json_encode($krs->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$krs_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $krs_add->ShowPageHeader(); ?>
<?php
$krs_add->ShowMessage();
?>
<form name="fkrsadd" id="fkrsadd" class="<?php echo $krs_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($krs_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $krs_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="krs">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($krs_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$krs_add->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_krsadd" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($krs->KHSID->Visible) { // KHSID ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_KHSID" class="form-group">
		<label id="elh_krs_KHSID" for="x_KHSID" class="col-sm-2 control-label ewLabel"><?php echo $krs->KHSID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $krs->KHSID->CellAttributes() ?>>
<span id="el_krs_KHSID">
<input type="text" data-table="krs" data-field="x_KHSID" name="x_KHSID" id="x_KHSID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->KHSID->getPlaceHolder()) ?>" value="<?php echo $krs->KHSID->EditValue ?>"<?php echo $krs->KHSID->EditAttributes() ?>>
</span>
<?php echo $krs->KHSID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KHSID">
		<td><span id="elh_krs_KHSID"><?php echo $krs->KHSID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $krs->KHSID->CellAttributes() ?>>
<span id="el_krs_KHSID">
<input type="text" data-table="krs" data-field="x_KHSID" name="x_KHSID" id="x_KHSID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->KHSID->getPlaceHolder()) ?>" value="<?php echo $krs->KHSID->EditValue ?>"<?php echo $krs->KHSID->EditAttributes() ?>>
</span>
<?php echo $krs->KHSID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->StudentID->Visible) { // StudentID ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_StudentID" class="form-group">
		<label id="elh_krs_StudentID" for="x_StudentID" class="col-sm-2 control-label ewLabel"><?php echo $krs->StudentID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $krs->StudentID->CellAttributes() ?>>
<span id="el_krs_StudentID">
<input type="text" data-table="krs" data-field="x_StudentID" name="x_StudentID" id="x_StudentID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->StudentID->getPlaceHolder()) ?>" value="<?php echo $krs->StudentID->EditValue ?>"<?php echo $krs->StudentID->EditAttributes() ?>>
</span>
<?php echo $krs->StudentID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_StudentID">
		<td><span id="elh_krs_StudentID"><?php echo $krs->StudentID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $krs->StudentID->CellAttributes() ?>>
<span id="el_krs_StudentID">
<input type="text" data-table="krs" data-field="x_StudentID" name="x_StudentID" id="x_StudentID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->StudentID->getPlaceHolder()) ?>" value="<?php echo $krs->StudentID->EditValue ?>"<?php echo $krs->StudentID->EditAttributes() ?>>
</span>
<?php echo $krs->StudentID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->TahunID->Visible) { // TahunID ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_TahunID" class="form-group">
		<label id="elh_krs_TahunID" for="x_TahunID" class="col-sm-2 control-label ewLabel"><?php echo $krs->TahunID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $krs->TahunID->CellAttributes() ?>>
<span id="el_krs_TahunID">
<input type="text" data-table="krs" data-field="x_TahunID" name="x_TahunID" id="x_TahunID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->TahunID->getPlaceHolder()) ?>" value="<?php echo $krs->TahunID->EditValue ?>"<?php echo $krs->TahunID->EditAttributes() ?>>
</span>
<?php echo $krs->TahunID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TahunID">
		<td><span id="elh_krs_TahunID"><?php echo $krs->TahunID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $krs->TahunID->CellAttributes() ?>>
<span id="el_krs_TahunID">
<input type="text" data-table="krs" data-field="x_TahunID" name="x_TahunID" id="x_TahunID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->TahunID->getPlaceHolder()) ?>" value="<?php echo $krs->TahunID->EditValue ?>"<?php echo $krs->TahunID->EditAttributes() ?>>
</span>
<?php echo $krs->TahunID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Sesi->Visible) { // Sesi ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Sesi" class="form-group">
		<label id="elh_krs_Sesi" for="x_Sesi" class="col-sm-2 control-label ewLabel"><?php echo $krs->Sesi->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Sesi->CellAttributes() ?>>
<span id="el_krs_Sesi">
<input type="text" data-table="krs" data-field="x_Sesi" name="x_Sesi" id="x_Sesi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Sesi->getPlaceHolder()) ?>" value="<?php echo $krs->Sesi->EditValue ?>"<?php echo $krs->Sesi->EditAttributes() ?>>
</span>
<?php echo $krs->Sesi->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Sesi">
		<td><span id="elh_krs_Sesi"><?php echo $krs->Sesi->FldCaption() ?></span></td>
		<td<?php echo $krs->Sesi->CellAttributes() ?>>
<span id="el_krs_Sesi">
<input type="text" data-table="krs" data-field="x_Sesi" name="x_Sesi" id="x_Sesi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Sesi->getPlaceHolder()) ?>" value="<?php echo $krs->Sesi->EditValue ?>"<?php echo $krs->Sesi->EditAttributes() ?>>
</span>
<?php echo $krs->Sesi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->JadwalID->Visible) { // JadwalID ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_JadwalID" class="form-group">
		<label id="elh_krs_JadwalID" for="x_JadwalID" class="col-sm-2 control-label ewLabel"><?php echo $krs->JadwalID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->JadwalID->CellAttributes() ?>>
<span id="el_krs_JadwalID">
<input type="text" data-table="krs" data-field="x_JadwalID" name="x_JadwalID" id="x_JadwalID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->JadwalID->getPlaceHolder()) ?>" value="<?php echo $krs->JadwalID->EditValue ?>"<?php echo $krs->JadwalID->EditAttributes() ?>>
</span>
<?php echo $krs->JadwalID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_JadwalID">
		<td><span id="elh_krs_JadwalID"><?php echo $krs->JadwalID->FldCaption() ?></span></td>
		<td<?php echo $krs->JadwalID->CellAttributes() ?>>
<span id="el_krs_JadwalID">
<input type="text" data-table="krs" data-field="x_JadwalID" name="x_JadwalID" id="x_JadwalID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->JadwalID->getPlaceHolder()) ?>" value="<?php echo $krs->JadwalID->EditValue ?>"<?php echo $krs->JadwalID->EditAttributes() ?>>
</span>
<?php echo $krs->JadwalID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->MKID->Visible) { // MKID ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_MKID" class="form-group">
		<label id="elh_krs_MKID" for="x_MKID" class="col-sm-2 control-label ewLabel"><?php echo $krs->MKID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->MKID->CellAttributes() ?>>
<span id="el_krs_MKID">
<input type="text" data-table="krs" data-field="x_MKID" name="x_MKID" id="x_MKID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->MKID->getPlaceHolder()) ?>" value="<?php echo $krs->MKID->EditValue ?>"<?php echo $krs->MKID->EditAttributes() ?>>
</span>
<?php echo $krs->MKID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_MKID">
		<td><span id="elh_krs_MKID"><?php echo $krs->MKID->FldCaption() ?></span></td>
		<td<?php echo $krs->MKID->CellAttributes() ?>>
<span id="el_krs_MKID">
<input type="text" data-table="krs" data-field="x_MKID" name="x_MKID" id="x_MKID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->MKID->getPlaceHolder()) ?>" value="<?php echo $krs->MKID->EditValue ?>"<?php echo $krs->MKID->EditAttributes() ?>>
</span>
<?php echo $krs->MKID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->MKKode->Visible) { // MKKode ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_MKKode" class="form-group">
		<label id="elh_krs_MKKode" for="x_MKKode" class="col-sm-2 control-label ewLabel"><?php echo $krs->MKKode->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->MKKode->CellAttributes() ?>>
<span id="el_krs_MKKode">
<input type="text" data-table="krs" data-field="x_MKKode" name="x_MKKode" id="x_MKKode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->MKKode->getPlaceHolder()) ?>" value="<?php echo $krs->MKKode->EditValue ?>"<?php echo $krs->MKKode->EditAttributes() ?>>
</span>
<?php echo $krs->MKKode->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_MKKode">
		<td><span id="elh_krs_MKKode"><?php echo $krs->MKKode->FldCaption() ?></span></td>
		<td<?php echo $krs->MKKode->CellAttributes() ?>>
<span id="el_krs_MKKode">
<input type="text" data-table="krs" data-field="x_MKKode" name="x_MKKode" id="x_MKKode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->MKKode->getPlaceHolder()) ?>" value="<?php echo $krs->MKKode->EditValue ?>"<?php echo $krs->MKKode->EditAttributes() ?>>
</span>
<?php echo $krs->MKKode->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->SKS->Visible) { // SKS ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_SKS" class="form-group">
		<label id="elh_krs_SKS" for="x_SKS" class="col-sm-2 control-label ewLabel"><?php echo $krs->SKS->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->SKS->CellAttributes() ?>>
<span id="el_krs_SKS">
<input type="text" data-table="krs" data-field="x_SKS" name="x_SKS" id="x_SKS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->SKS->getPlaceHolder()) ?>" value="<?php echo $krs->SKS->EditValue ?>"<?php echo $krs->SKS->EditAttributes() ?>>
</span>
<?php echo $krs->SKS->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_SKS">
		<td><span id="elh_krs_SKS"><?php echo $krs->SKS->FldCaption() ?></span></td>
		<td<?php echo $krs->SKS->CellAttributes() ?>>
<span id="el_krs_SKS">
<input type="text" data-table="krs" data-field="x_SKS" name="x_SKS" id="x_SKS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->SKS->getPlaceHolder()) ?>" value="<?php echo $krs->SKS->EditValue ?>"<?php echo $krs->SKS->EditAttributes() ?>>
</span>
<?php echo $krs->SKS->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tugas1->Visible) { // Tugas1 ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Tugas1" class="form-group">
		<label id="elh_krs_Tugas1" for="x_Tugas1" class="col-sm-2 control-label ewLabel"><?php echo $krs->Tugas1->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Tugas1->CellAttributes() ?>>
<span id="el_krs_Tugas1">
<input type="text" data-table="krs" data-field="x_Tugas1" name="x_Tugas1" id="x_Tugas1" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas1->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas1->EditValue ?>"<?php echo $krs->Tugas1->EditAttributes() ?>>
</span>
<?php echo $krs->Tugas1->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tugas1">
		<td><span id="elh_krs_Tugas1"><?php echo $krs->Tugas1->FldCaption() ?></span></td>
		<td<?php echo $krs->Tugas1->CellAttributes() ?>>
<span id="el_krs_Tugas1">
<input type="text" data-table="krs" data-field="x_Tugas1" name="x_Tugas1" id="x_Tugas1" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas1->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas1->EditValue ?>"<?php echo $krs->Tugas1->EditAttributes() ?>>
</span>
<?php echo $krs->Tugas1->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tugas2->Visible) { // Tugas2 ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Tugas2" class="form-group">
		<label id="elh_krs_Tugas2" for="x_Tugas2" class="col-sm-2 control-label ewLabel"><?php echo $krs->Tugas2->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Tugas2->CellAttributes() ?>>
<span id="el_krs_Tugas2">
<input type="text" data-table="krs" data-field="x_Tugas2" name="x_Tugas2" id="x_Tugas2" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas2->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas2->EditValue ?>"<?php echo $krs->Tugas2->EditAttributes() ?>>
</span>
<?php echo $krs->Tugas2->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tugas2">
		<td><span id="elh_krs_Tugas2"><?php echo $krs->Tugas2->FldCaption() ?></span></td>
		<td<?php echo $krs->Tugas2->CellAttributes() ?>>
<span id="el_krs_Tugas2">
<input type="text" data-table="krs" data-field="x_Tugas2" name="x_Tugas2" id="x_Tugas2" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas2->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas2->EditValue ?>"<?php echo $krs->Tugas2->EditAttributes() ?>>
</span>
<?php echo $krs->Tugas2->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tugas3->Visible) { // Tugas3 ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Tugas3" class="form-group">
		<label id="elh_krs_Tugas3" for="x_Tugas3" class="col-sm-2 control-label ewLabel"><?php echo $krs->Tugas3->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Tugas3->CellAttributes() ?>>
<span id="el_krs_Tugas3">
<input type="text" data-table="krs" data-field="x_Tugas3" name="x_Tugas3" id="x_Tugas3" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas3->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas3->EditValue ?>"<?php echo $krs->Tugas3->EditAttributes() ?>>
</span>
<?php echo $krs->Tugas3->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tugas3">
		<td><span id="elh_krs_Tugas3"><?php echo $krs->Tugas3->FldCaption() ?></span></td>
		<td<?php echo $krs->Tugas3->CellAttributes() ?>>
<span id="el_krs_Tugas3">
<input type="text" data-table="krs" data-field="x_Tugas3" name="x_Tugas3" id="x_Tugas3" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas3->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas3->EditValue ?>"<?php echo $krs->Tugas3->EditAttributes() ?>>
</span>
<?php echo $krs->Tugas3->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tugas4->Visible) { // Tugas4 ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Tugas4" class="form-group">
		<label id="elh_krs_Tugas4" for="x_Tugas4" class="col-sm-2 control-label ewLabel"><?php echo $krs->Tugas4->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Tugas4->CellAttributes() ?>>
<span id="el_krs_Tugas4">
<input type="text" data-table="krs" data-field="x_Tugas4" name="x_Tugas4" id="x_Tugas4" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas4->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas4->EditValue ?>"<?php echo $krs->Tugas4->EditAttributes() ?>>
</span>
<?php echo $krs->Tugas4->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tugas4">
		<td><span id="elh_krs_Tugas4"><?php echo $krs->Tugas4->FldCaption() ?></span></td>
		<td<?php echo $krs->Tugas4->CellAttributes() ?>>
<span id="el_krs_Tugas4">
<input type="text" data-table="krs" data-field="x_Tugas4" name="x_Tugas4" id="x_Tugas4" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas4->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas4->EditValue ?>"<?php echo $krs->Tugas4->EditAttributes() ?>>
</span>
<?php echo $krs->Tugas4->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tugas5->Visible) { // Tugas5 ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Tugas5" class="form-group">
		<label id="elh_krs_Tugas5" for="x_Tugas5" class="col-sm-2 control-label ewLabel"><?php echo $krs->Tugas5->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Tugas5->CellAttributes() ?>>
<span id="el_krs_Tugas5">
<input type="text" data-table="krs" data-field="x_Tugas5" name="x_Tugas5" id="x_Tugas5" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas5->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas5->EditValue ?>"<?php echo $krs->Tugas5->EditAttributes() ?>>
</span>
<?php echo $krs->Tugas5->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tugas5">
		<td><span id="elh_krs_Tugas5"><?php echo $krs->Tugas5->FldCaption() ?></span></td>
		<td<?php echo $krs->Tugas5->CellAttributes() ?>>
<span id="el_krs_Tugas5">
<input type="text" data-table="krs" data-field="x_Tugas5" name="x_Tugas5" id="x_Tugas5" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas5->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas5->EditValue ?>"<?php echo $krs->Tugas5->EditAttributes() ?>>
</span>
<?php echo $krs->Tugas5->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Presensi->Visible) { // Presensi ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Presensi" class="form-group">
		<label id="elh_krs_Presensi" for="x_Presensi" class="col-sm-2 control-label ewLabel"><?php echo $krs->Presensi->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Presensi->CellAttributes() ?>>
<span id="el_krs_Presensi">
<input type="text" data-table="krs" data-field="x_Presensi" name="x_Presensi" id="x_Presensi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Presensi->getPlaceHolder()) ?>" value="<?php echo $krs->Presensi->EditValue ?>"<?php echo $krs->Presensi->EditAttributes() ?>>
</span>
<?php echo $krs->Presensi->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Presensi">
		<td><span id="elh_krs_Presensi"><?php echo $krs->Presensi->FldCaption() ?></span></td>
		<td<?php echo $krs->Presensi->CellAttributes() ?>>
<span id="el_krs_Presensi">
<input type="text" data-table="krs" data-field="x_Presensi" name="x_Presensi" id="x_Presensi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Presensi->getPlaceHolder()) ?>" value="<?php echo $krs->Presensi->EditValue ?>"<?php echo $krs->Presensi->EditAttributes() ?>>
</span>
<?php echo $krs->Presensi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->_Presensi->Visible) { // _Presensi ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r__Presensi" class="form-group">
		<label id="elh_krs__Presensi" for="x__Presensi" class="col-sm-2 control-label ewLabel"><?php echo $krs->_Presensi->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->_Presensi->CellAttributes() ?>>
<span id="el_krs__Presensi">
<input type="text" data-table="krs" data-field="x__Presensi" name="x__Presensi" id="x__Presensi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->_Presensi->getPlaceHolder()) ?>" value="<?php echo $krs->_Presensi->EditValue ?>"<?php echo $krs->_Presensi->EditAttributes() ?>>
</span>
<?php echo $krs->_Presensi->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r__Presensi">
		<td><span id="elh_krs__Presensi"><?php echo $krs->_Presensi->FldCaption() ?></span></td>
		<td<?php echo $krs->_Presensi->CellAttributes() ?>>
<span id="el_krs__Presensi">
<input type="text" data-table="krs" data-field="x__Presensi" name="x__Presensi" id="x__Presensi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->_Presensi->getPlaceHolder()) ?>" value="<?php echo $krs->_Presensi->EditValue ?>"<?php echo $krs->_Presensi->EditAttributes() ?>>
</span>
<?php echo $krs->_Presensi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->UTS->Visible) { // UTS ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_UTS" class="form-group">
		<label id="elh_krs_UTS" for="x_UTS" class="col-sm-2 control-label ewLabel"><?php echo $krs->UTS->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->UTS->CellAttributes() ?>>
<span id="el_krs_UTS">
<input type="text" data-table="krs" data-field="x_UTS" name="x_UTS" id="x_UTS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->UTS->getPlaceHolder()) ?>" value="<?php echo $krs->UTS->EditValue ?>"<?php echo $krs->UTS->EditAttributes() ?>>
</span>
<?php echo $krs->UTS->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_UTS">
		<td><span id="elh_krs_UTS"><?php echo $krs->UTS->FldCaption() ?></span></td>
		<td<?php echo $krs->UTS->CellAttributes() ?>>
<span id="el_krs_UTS">
<input type="text" data-table="krs" data-field="x_UTS" name="x_UTS" id="x_UTS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->UTS->getPlaceHolder()) ?>" value="<?php echo $krs->UTS->EditValue ?>"<?php echo $krs->UTS->EditAttributes() ?>>
</span>
<?php echo $krs->UTS->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->UAS->Visible) { // UAS ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_UAS" class="form-group">
		<label id="elh_krs_UAS" for="x_UAS" class="col-sm-2 control-label ewLabel"><?php echo $krs->UAS->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->UAS->CellAttributes() ?>>
<span id="el_krs_UAS">
<input type="text" data-table="krs" data-field="x_UAS" name="x_UAS" id="x_UAS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->UAS->getPlaceHolder()) ?>" value="<?php echo $krs->UAS->EditValue ?>"<?php echo $krs->UAS->EditAttributes() ?>>
</span>
<?php echo $krs->UAS->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_UAS">
		<td><span id="elh_krs_UAS"><?php echo $krs->UAS->FldCaption() ?></span></td>
		<td<?php echo $krs->UAS->CellAttributes() ?>>
<span id="el_krs_UAS">
<input type="text" data-table="krs" data-field="x_UAS" name="x_UAS" id="x_UAS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->UAS->getPlaceHolder()) ?>" value="<?php echo $krs->UAS->EditValue ?>"<?php echo $krs->UAS->EditAttributes() ?>>
</span>
<?php echo $krs->UAS->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Responsi->Visible) { // Responsi ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Responsi" class="form-group">
		<label id="elh_krs_Responsi" for="x_Responsi" class="col-sm-2 control-label ewLabel"><?php echo $krs->Responsi->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Responsi->CellAttributes() ?>>
<span id="el_krs_Responsi">
<input type="text" data-table="krs" data-field="x_Responsi" name="x_Responsi" id="x_Responsi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Responsi->getPlaceHolder()) ?>" value="<?php echo $krs->Responsi->EditValue ?>"<?php echo $krs->Responsi->EditAttributes() ?>>
</span>
<?php echo $krs->Responsi->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Responsi">
		<td><span id="elh_krs_Responsi"><?php echo $krs->Responsi->FldCaption() ?></span></td>
		<td<?php echo $krs->Responsi->CellAttributes() ?>>
<span id="el_krs_Responsi">
<input type="text" data-table="krs" data-field="x_Responsi" name="x_Responsi" id="x_Responsi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Responsi->getPlaceHolder()) ?>" value="<?php echo $krs->Responsi->EditValue ?>"<?php echo $krs->Responsi->EditAttributes() ?>>
</span>
<?php echo $krs->Responsi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->NilaiAkhir->Visible) { // NilaiAkhir ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_NilaiAkhir" class="form-group">
		<label id="elh_krs_NilaiAkhir" for="x_NilaiAkhir" class="col-sm-2 control-label ewLabel"><?php echo $krs->NilaiAkhir->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->NilaiAkhir->CellAttributes() ?>>
<span id="el_krs_NilaiAkhir">
<input type="text" data-table="krs" data-field="x_NilaiAkhir" name="x_NilaiAkhir" id="x_NilaiAkhir" size="30" placeholder="<?php echo ew_HtmlEncode($krs->NilaiAkhir->getPlaceHolder()) ?>" value="<?php echo $krs->NilaiAkhir->EditValue ?>"<?php echo $krs->NilaiAkhir->EditAttributes() ?>>
</span>
<?php echo $krs->NilaiAkhir->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NilaiAkhir">
		<td><span id="elh_krs_NilaiAkhir"><?php echo $krs->NilaiAkhir->FldCaption() ?></span></td>
		<td<?php echo $krs->NilaiAkhir->CellAttributes() ?>>
<span id="el_krs_NilaiAkhir">
<input type="text" data-table="krs" data-field="x_NilaiAkhir" name="x_NilaiAkhir" id="x_NilaiAkhir" size="30" placeholder="<?php echo ew_HtmlEncode($krs->NilaiAkhir->getPlaceHolder()) ?>" value="<?php echo $krs->NilaiAkhir->EditValue ?>"<?php echo $krs->NilaiAkhir->EditAttributes() ?>>
</span>
<?php echo $krs->NilaiAkhir->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->GradeNilai->Visible) { // GradeNilai ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_GradeNilai" class="form-group">
		<label id="elh_krs_GradeNilai" for="x_GradeNilai" class="col-sm-2 control-label ewLabel"><?php echo $krs->GradeNilai->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->GradeNilai->CellAttributes() ?>>
<span id="el_krs_GradeNilai">
<input type="text" data-table="krs" data-field="x_GradeNilai" name="x_GradeNilai" id="x_GradeNilai" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->GradeNilai->getPlaceHolder()) ?>" value="<?php echo $krs->GradeNilai->EditValue ?>"<?php echo $krs->GradeNilai->EditAttributes() ?>>
</span>
<?php echo $krs->GradeNilai->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_GradeNilai">
		<td><span id="elh_krs_GradeNilai"><?php echo $krs->GradeNilai->FldCaption() ?></span></td>
		<td<?php echo $krs->GradeNilai->CellAttributes() ?>>
<span id="el_krs_GradeNilai">
<input type="text" data-table="krs" data-field="x_GradeNilai" name="x_GradeNilai" id="x_GradeNilai" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->GradeNilai->getPlaceHolder()) ?>" value="<?php echo $krs->GradeNilai->EditValue ?>"<?php echo $krs->GradeNilai->EditAttributes() ?>>
</span>
<?php echo $krs->GradeNilai->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->BobotNilai->Visible) { // BobotNilai ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_BobotNilai" class="form-group">
		<label id="elh_krs_BobotNilai" for="x_BobotNilai" class="col-sm-2 control-label ewLabel"><?php echo $krs->BobotNilai->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->BobotNilai->CellAttributes() ?>>
<span id="el_krs_BobotNilai">
<input type="text" data-table="krs" data-field="x_BobotNilai" name="x_BobotNilai" id="x_BobotNilai" size="30" placeholder="<?php echo ew_HtmlEncode($krs->BobotNilai->getPlaceHolder()) ?>" value="<?php echo $krs->BobotNilai->EditValue ?>"<?php echo $krs->BobotNilai->EditAttributes() ?>>
</span>
<?php echo $krs->BobotNilai->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_BobotNilai">
		<td><span id="elh_krs_BobotNilai"><?php echo $krs->BobotNilai->FldCaption() ?></span></td>
		<td<?php echo $krs->BobotNilai->CellAttributes() ?>>
<span id="el_krs_BobotNilai">
<input type="text" data-table="krs" data-field="x_BobotNilai" name="x_BobotNilai" id="x_BobotNilai" size="30" placeholder="<?php echo ew_HtmlEncode($krs->BobotNilai->getPlaceHolder()) ?>" value="<?php echo $krs->BobotNilai->EditValue ?>"<?php echo $krs->BobotNilai->EditAttributes() ?>>
</span>
<?php echo $krs->BobotNilai->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->StatusKRSID->Visible) { // StatusKRSID ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_StatusKRSID" class="form-group">
		<label id="elh_krs_StatusKRSID" for="x_StatusKRSID" class="col-sm-2 control-label ewLabel"><?php echo $krs->StatusKRSID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $krs->StatusKRSID->CellAttributes() ?>>
<span id="el_krs_StatusKRSID">
<input type="text" data-table="krs" data-field="x_StatusKRSID" name="x_StatusKRSID" id="x_StatusKRSID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->StatusKRSID->getPlaceHolder()) ?>" value="<?php echo $krs->StatusKRSID->EditValue ?>"<?php echo $krs->StatusKRSID->EditAttributes() ?>>
</span>
<?php echo $krs->StatusKRSID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusKRSID">
		<td><span id="elh_krs_StatusKRSID"><?php echo $krs->StatusKRSID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $krs->StatusKRSID->CellAttributes() ?>>
<span id="el_krs_StatusKRSID">
<input type="text" data-table="krs" data-field="x_StatusKRSID" name="x_StatusKRSID" id="x_StatusKRSID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->StatusKRSID->getPlaceHolder()) ?>" value="<?php echo $krs->StatusKRSID->EditValue ?>"<?php echo $krs->StatusKRSID->EditAttributes() ?>>
</span>
<?php echo $krs->StatusKRSID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tinggi->Visible) { // Tinggi ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Tinggi" class="form-group">
		<label id="elh_krs_Tinggi" for="x_Tinggi" class="col-sm-2 control-label ewLabel"><?php echo $krs->Tinggi->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Tinggi->CellAttributes() ?>>
<span id="el_krs_Tinggi">
<input type="text" data-table="krs" data-field="x_Tinggi" name="x_Tinggi" id="x_Tinggi" size="30" maxlength="1" placeholder="<?php echo ew_HtmlEncode($krs->Tinggi->getPlaceHolder()) ?>" value="<?php echo $krs->Tinggi->EditValue ?>"<?php echo $krs->Tinggi->EditAttributes() ?>>
</span>
<?php echo $krs->Tinggi->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tinggi">
		<td><span id="elh_krs_Tinggi"><?php echo $krs->Tinggi->FldCaption() ?></span></td>
		<td<?php echo $krs->Tinggi->CellAttributes() ?>>
<span id="el_krs_Tinggi">
<input type="text" data-table="krs" data-field="x_Tinggi" name="x_Tinggi" id="x_Tinggi" size="30" maxlength="1" placeholder="<?php echo ew_HtmlEncode($krs->Tinggi->getPlaceHolder()) ?>" value="<?php echo $krs->Tinggi->EditValue ?>"<?php echo $krs->Tinggi->EditAttributes() ?>>
</span>
<?php echo $krs->Tinggi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Final->Visible) { // Final ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Final" class="form-group">
		<label id="elh_krs_Final" class="col-sm-2 control-label ewLabel"><?php echo $krs->Final->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Final->CellAttributes() ?>>
<span id="el_krs_Final">
<div id="tp_x_Final" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_Final" data-value-separator="<?php echo $krs->Final->DisplayValueSeparatorAttribute() ?>" name="x_Final" id="x_Final" value="{value}"<?php echo $krs->Final->EditAttributes() ?>></div>
<div id="dsl_x_Final" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->Final->RadioButtonListHtml(FALSE, "x_Final") ?>
</div></div>
</span>
<?php echo $krs->Final->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Final">
		<td><span id="elh_krs_Final"><?php echo $krs->Final->FldCaption() ?></span></td>
		<td<?php echo $krs->Final->CellAttributes() ?>>
<span id="el_krs_Final">
<div id="tp_x_Final" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_Final" data-value-separator="<?php echo $krs->Final->DisplayValueSeparatorAttribute() ?>" name="x_Final" id="x_Final" value="{value}"<?php echo $krs->Final->EditAttributes() ?>></div>
<div id="dsl_x_Final" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->Final->RadioButtonListHtml(FALSE, "x_Final") ?>
</div></div>
</span>
<?php echo $krs->Final->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Setara->Visible) { // Setara ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Setara" class="form-group">
		<label id="elh_krs_Setara" class="col-sm-2 control-label ewLabel"><?php echo $krs->Setara->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Setara->CellAttributes() ?>>
<span id="el_krs_Setara">
<div id="tp_x_Setara" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_Setara" data-value-separator="<?php echo $krs->Setara->DisplayValueSeparatorAttribute() ?>" name="x_Setara" id="x_Setara" value="{value}"<?php echo $krs->Setara->EditAttributes() ?>></div>
<div id="dsl_x_Setara" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->Setara->RadioButtonListHtml(FALSE, "x_Setara") ?>
</div></div>
</span>
<?php echo $krs->Setara->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Setara">
		<td><span id="elh_krs_Setara"><?php echo $krs->Setara->FldCaption() ?></span></td>
		<td<?php echo $krs->Setara->CellAttributes() ?>>
<span id="el_krs_Setara">
<div id="tp_x_Setara" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_Setara" data-value-separator="<?php echo $krs->Setara->DisplayValueSeparatorAttribute() ?>" name="x_Setara" id="x_Setara" value="{value}"<?php echo $krs->Setara->EditAttributes() ?>></div>
<div id="dsl_x_Setara" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->Setara->RadioButtonListHtml(FALSE, "x_Setara") ?>
</div></div>
</span>
<?php echo $krs->Setara->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Creator->Visible) { // Creator ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Creator" class="form-group">
		<label id="elh_krs_Creator" for="x_Creator" class="col-sm-2 control-label ewLabel"><?php echo $krs->Creator->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Creator->CellAttributes() ?>>
<span id="el_krs_Creator">
<input type="text" data-table="krs" data-field="x_Creator" name="x_Creator" id="x_Creator" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->Creator->getPlaceHolder()) ?>" value="<?php echo $krs->Creator->EditValue ?>"<?php echo $krs->Creator->EditAttributes() ?>>
</span>
<?php echo $krs->Creator->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Creator">
		<td><span id="elh_krs_Creator"><?php echo $krs->Creator->FldCaption() ?></span></td>
		<td<?php echo $krs->Creator->CellAttributes() ?>>
<span id="el_krs_Creator">
<input type="text" data-table="krs" data-field="x_Creator" name="x_Creator" id="x_Creator" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->Creator->getPlaceHolder()) ?>" value="<?php echo $krs->Creator->EditValue ?>"<?php echo $krs->Creator->EditAttributes() ?>>
</span>
<?php echo $krs->Creator->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->CreateDate->Visible) { // CreateDate ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_CreateDate" class="form-group">
		<label id="elh_krs_CreateDate" for="x_CreateDate" class="col-sm-2 control-label ewLabel"><?php echo $krs->CreateDate->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->CreateDate->CellAttributes() ?>>
<span id="el_krs_CreateDate">
<input type="text" data-table="krs" data-field="x_CreateDate" name="x_CreateDate" id="x_CreateDate" placeholder="<?php echo ew_HtmlEncode($krs->CreateDate->getPlaceHolder()) ?>" value="<?php echo $krs->CreateDate->EditValue ?>"<?php echo $krs->CreateDate->EditAttributes() ?>>
</span>
<?php echo $krs->CreateDate->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_CreateDate">
		<td><span id="elh_krs_CreateDate"><?php echo $krs->CreateDate->FldCaption() ?></span></td>
		<td<?php echo $krs->CreateDate->CellAttributes() ?>>
<span id="el_krs_CreateDate">
<input type="text" data-table="krs" data-field="x_CreateDate" name="x_CreateDate" id="x_CreateDate" placeholder="<?php echo ew_HtmlEncode($krs->CreateDate->getPlaceHolder()) ?>" value="<?php echo $krs->CreateDate->EditValue ?>"<?php echo $krs->CreateDate->EditAttributes() ?>>
</span>
<?php echo $krs->CreateDate->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Editor->Visible) { // Editor ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_Editor" class="form-group">
		<label id="elh_krs_Editor" for="x_Editor" class="col-sm-2 control-label ewLabel"><?php echo $krs->Editor->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->Editor->CellAttributes() ?>>
<span id="el_krs_Editor">
<input type="text" data-table="krs" data-field="x_Editor" name="x_Editor" id="x_Editor" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->Editor->getPlaceHolder()) ?>" value="<?php echo $krs->Editor->EditValue ?>"<?php echo $krs->Editor->EditAttributes() ?>>
</span>
<?php echo $krs->Editor->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Editor">
		<td><span id="elh_krs_Editor"><?php echo $krs->Editor->FldCaption() ?></span></td>
		<td<?php echo $krs->Editor->CellAttributes() ?>>
<span id="el_krs_Editor">
<input type="text" data-table="krs" data-field="x_Editor" name="x_Editor" id="x_Editor" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->Editor->getPlaceHolder()) ?>" value="<?php echo $krs->Editor->EditValue ?>"<?php echo $krs->Editor->EditAttributes() ?>>
</span>
<?php echo $krs->Editor->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->EditDate->Visible) { // EditDate ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_EditDate" class="form-group">
		<label id="elh_krs_EditDate" for="x_EditDate" class="col-sm-2 control-label ewLabel"><?php echo $krs->EditDate->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $krs->EditDate->CellAttributes() ?>>
<span id="el_krs_EditDate">
<input type="text" data-table="krs" data-field="x_EditDate" name="x_EditDate" id="x_EditDate" placeholder="<?php echo ew_HtmlEncode($krs->EditDate->getPlaceHolder()) ?>" value="<?php echo $krs->EditDate->EditValue ?>"<?php echo $krs->EditDate->EditAttributes() ?>>
</span>
<?php echo $krs->EditDate->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_EditDate">
		<td><span id="elh_krs_EditDate"><?php echo $krs->EditDate->FldCaption() ?></span></td>
		<td<?php echo $krs->EditDate->CellAttributes() ?>>
<span id="el_krs_EditDate">
<input type="text" data-table="krs" data-field="x_EditDate" name="x_EditDate" id="x_EditDate" placeholder="<?php echo ew_HtmlEncode($krs->EditDate->getPlaceHolder()) ?>" value="<?php echo $krs->EditDate->EditValue ?>"<?php echo $krs->EditDate->EditAttributes() ?>>
</span>
<?php echo $krs->EditDate->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_krs_NA" class="col-sm-2 control-label ewLabel"><?php echo $krs->NA->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $krs->NA->CellAttributes() ?>>
<span id="el_krs_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_NA" data-value-separator="<?php echo $krs->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $krs->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $krs->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_krs_NA"><?php echo $krs->NA->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $krs->NA->CellAttributes() ?>>
<span id="el_krs_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_NA" data-value-separator="<?php echo $krs->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $krs->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $krs->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $krs_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$krs_add->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $krs_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fkrsadd.Init();
</script>
<?php
$krs_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$krs_add->Page_Terminate();
?>
