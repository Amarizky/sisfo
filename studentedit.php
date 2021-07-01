<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "studentinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$student_edit = NULL; // Initialize page object first

class cstudent_edit extends cstudent {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'student';

	// Page object name
	var $PageObjName = 'student_edit';

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

		// Table object (student)
		if (!isset($GLOBALS["student"]) || get_class($GLOBALS["student"]) == "cstudent") {
			$GLOBALS["student"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["student"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'student', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("studentlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->StudentID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->LevelID->SetVisibility();
		$this->Password->SetVisibility();
		$this->KampusID->SetVisibility();
		$this->ProdiID->SetVisibility();
		$this->StudentStatusID->SetVisibility();
		$this->TahunID->SetVisibility();
		$this->Foto->SetVisibility();
		$this->NIK->SetVisibility();
		$this->WargaNegara->SetVisibility();
		$this->Kelamin->SetVisibility();
		$this->TempatLahir->SetVisibility();
		$this->TanggalLahir->SetVisibility();
		$this->AgamaID->SetVisibility();
		$this->Darah->SetVisibility();
		$this->StatusSipil->SetVisibility();
		$this->AlamatDomisili->SetVisibility();
		$this->RT->SetVisibility();
		$this->RW->SetVisibility();
		$this->KodePos->SetVisibility();
		$this->ProvinsiID->SetVisibility();
		$this->KabupatenKotaID->SetVisibility();
		$this->KecamatanID->SetVisibility();
		$this->DesaID->SetVisibility();
		$this->AnakKe->SetVisibility();
		$this->JumlahSaudara->SetVisibility();
		$this->Telepon->SetVisibility();
		$this->_Email->SetVisibility();
		$this->NamaAyah->SetVisibility();
		$this->AgamaAyah->SetVisibility();
		$this->PendidikanAyah->SetVisibility();
		$this->PekerjaanAyah->SetVisibility();
		$this->HidupAyah->SetVisibility();
		$this->NamaIbu->SetVisibility();
		$this->AgamaIbu->SetVisibility();
		$this->PendidikanIbu->SetVisibility();
		$this->PekerjaanIbu->SetVisibility();
		$this->HidupIbu->SetVisibility();
		$this->AlamatOrtu->SetVisibility();
		$this->RTOrtu->SetVisibility();
		$this->RWOrtu->SetVisibility();
		$this->KodePosOrtu->SetVisibility();
		$this->ProvinsiIDOrtu->SetVisibility();
		$this->KabupatenIDOrtu->SetVisibility();
		$this->KecamatanIDOrtu->SetVisibility();
		$this->DesaIDOrtu->SetVisibility();
		$this->NegaraIDOrtu->SetVisibility();
		$this->TeleponOrtu->SetVisibility();
		$this->HandphoneOrtu->SetVisibility();
		$this->EmailOrtu->SetVisibility();
		$this->AsalSekolah->SetVisibility();
		$this->AlamatSekolah->SetVisibility();
		$this->ProvinsiIDSekolah->SetVisibility();
		$this->KabupatenIDSekolah->SetVisibility();
		$this->KecamatanIDSekolah->SetVisibility();
		$this->DesaIDSekolah->SetVisibility();
		$this->NilaiSekolah->SetVisibility();
		$this->TahunLulus->SetVisibility();
		$this->IjazahSekolah->SetVisibility();
		$this->TglIjazah->SetVisibility();
		$this->Editor->SetVisibility();
		$this->EditDate->SetVisibility();
		$this->LockStatus->SetVisibility();
		$this->VerifiedBy->SetVisibility();
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
		global $EW_EXPORT, $student;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($student);
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
		if (@$_GET["StudentID"] <> "") {
			$this->StudentID->setQueryStringValue($_GET["StudentID"]);
		}

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->StudentID->CurrentValue == "") {
			$this->Page_Terminate("studentlist.php"); // Invalid key, return to list
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
					$this->Page_Terminate("studentlist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "studentlist.php")
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
		$this->Foto->Upload->Index = $objForm->Index;
		$this->Foto->Upload->UploadFile();
		$this->Foto->CurrentValue = $this->Foto->Upload->FileName;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->StudentID->FldIsDetailKey) {
			$this->StudentID->setFormValue($objForm->GetValue("x_StudentID"));
		}
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->LevelID->FldIsDetailKey) {
			$this->LevelID->setFormValue($objForm->GetValue("x_LevelID"));
		}
		if (!$this->Password->FldIsDetailKey) {
			$this->Password->setFormValue($objForm->GetValue("x_Password"));
		}
		if (!$this->KampusID->FldIsDetailKey) {
			$this->KampusID->setFormValue($objForm->GetValue("x_KampusID"));
		}
		if (!$this->ProdiID->FldIsDetailKey) {
			$this->ProdiID->setFormValue($objForm->GetValue("x_ProdiID"));
		}
		if (!$this->StudentStatusID->FldIsDetailKey) {
			$this->StudentStatusID->setFormValue($objForm->GetValue("x_StudentStatusID"));
		}
		if (!$this->TahunID->FldIsDetailKey) {
			$this->TahunID->setFormValue($objForm->GetValue("x_TahunID"));
		}
		if (!$this->NIK->FldIsDetailKey) {
			$this->NIK->setFormValue($objForm->GetValue("x_NIK"));
		}
		if (!$this->WargaNegara->FldIsDetailKey) {
			$this->WargaNegara->setFormValue($objForm->GetValue("x_WargaNegara"));
		}
		if (!$this->Kelamin->FldIsDetailKey) {
			$this->Kelamin->setFormValue($objForm->GetValue("x_Kelamin"));
		}
		if (!$this->TempatLahir->FldIsDetailKey) {
			$this->TempatLahir->setFormValue($objForm->GetValue("x_TempatLahir"));
		}
		if (!$this->TanggalLahir->FldIsDetailKey) {
			$this->TanggalLahir->setFormValue($objForm->GetValue("x_TanggalLahir"));
			$this->TanggalLahir->CurrentValue = ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0);
		}
		if (!$this->AgamaID->FldIsDetailKey) {
			$this->AgamaID->setFormValue($objForm->GetValue("x_AgamaID"));
		}
		if (!$this->Darah->FldIsDetailKey) {
			$this->Darah->setFormValue($objForm->GetValue("x_Darah"));
		}
		if (!$this->StatusSipil->FldIsDetailKey) {
			$this->StatusSipil->setFormValue($objForm->GetValue("x_StatusSipil"));
		}
		if (!$this->AlamatDomisili->FldIsDetailKey) {
			$this->AlamatDomisili->setFormValue($objForm->GetValue("x_AlamatDomisili"));
		}
		if (!$this->RT->FldIsDetailKey) {
			$this->RT->setFormValue($objForm->GetValue("x_RT"));
		}
		if (!$this->RW->FldIsDetailKey) {
			$this->RW->setFormValue($objForm->GetValue("x_RW"));
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
		if (!$this->AnakKe->FldIsDetailKey) {
			$this->AnakKe->setFormValue($objForm->GetValue("x_AnakKe"));
		}
		if (!$this->JumlahSaudara->FldIsDetailKey) {
			$this->JumlahSaudara->setFormValue($objForm->GetValue("x_JumlahSaudara"));
		}
		if (!$this->Telepon->FldIsDetailKey) {
			$this->Telepon->setFormValue($objForm->GetValue("x_Telepon"));
		}
		if (!$this->_Email->FldIsDetailKey) {
			$this->_Email->setFormValue($objForm->GetValue("x__Email"));
		}
		if (!$this->NamaAyah->FldIsDetailKey) {
			$this->NamaAyah->setFormValue($objForm->GetValue("x_NamaAyah"));
		}
		if (!$this->AgamaAyah->FldIsDetailKey) {
			$this->AgamaAyah->setFormValue($objForm->GetValue("x_AgamaAyah"));
		}
		if (!$this->PendidikanAyah->FldIsDetailKey) {
			$this->PendidikanAyah->setFormValue($objForm->GetValue("x_PendidikanAyah"));
		}
		if (!$this->PekerjaanAyah->FldIsDetailKey) {
			$this->PekerjaanAyah->setFormValue($objForm->GetValue("x_PekerjaanAyah"));
		}
		if (!$this->HidupAyah->FldIsDetailKey) {
			$this->HidupAyah->setFormValue($objForm->GetValue("x_HidupAyah"));
		}
		if (!$this->NamaIbu->FldIsDetailKey) {
			$this->NamaIbu->setFormValue($objForm->GetValue("x_NamaIbu"));
		}
		if (!$this->AgamaIbu->FldIsDetailKey) {
			$this->AgamaIbu->setFormValue($objForm->GetValue("x_AgamaIbu"));
		}
		if (!$this->PendidikanIbu->FldIsDetailKey) {
			$this->PendidikanIbu->setFormValue($objForm->GetValue("x_PendidikanIbu"));
		}
		if (!$this->PekerjaanIbu->FldIsDetailKey) {
			$this->PekerjaanIbu->setFormValue($objForm->GetValue("x_PekerjaanIbu"));
		}
		if (!$this->HidupIbu->FldIsDetailKey) {
			$this->HidupIbu->setFormValue($objForm->GetValue("x_HidupIbu"));
		}
		if (!$this->AlamatOrtu->FldIsDetailKey) {
			$this->AlamatOrtu->setFormValue($objForm->GetValue("x_AlamatOrtu"));
		}
		if (!$this->RTOrtu->FldIsDetailKey) {
			$this->RTOrtu->setFormValue($objForm->GetValue("x_RTOrtu"));
		}
		if (!$this->RWOrtu->FldIsDetailKey) {
			$this->RWOrtu->setFormValue($objForm->GetValue("x_RWOrtu"));
		}
		if (!$this->KodePosOrtu->FldIsDetailKey) {
			$this->KodePosOrtu->setFormValue($objForm->GetValue("x_KodePosOrtu"));
		}
		if (!$this->ProvinsiIDOrtu->FldIsDetailKey) {
			$this->ProvinsiIDOrtu->setFormValue($objForm->GetValue("x_ProvinsiIDOrtu"));
		}
		if (!$this->KabupatenIDOrtu->FldIsDetailKey) {
			$this->KabupatenIDOrtu->setFormValue($objForm->GetValue("x_KabupatenIDOrtu"));
		}
		if (!$this->KecamatanIDOrtu->FldIsDetailKey) {
			$this->KecamatanIDOrtu->setFormValue($objForm->GetValue("x_KecamatanIDOrtu"));
		}
		if (!$this->DesaIDOrtu->FldIsDetailKey) {
			$this->DesaIDOrtu->setFormValue($objForm->GetValue("x_DesaIDOrtu"));
		}
		if (!$this->NegaraIDOrtu->FldIsDetailKey) {
			$this->NegaraIDOrtu->setFormValue($objForm->GetValue("x_NegaraIDOrtu"));
		}
		if (!$this->TeleponOrtu->FldIsDetailKey) {
			$this->TeleponOrtu->setFormValue($objForm->GetValue("x_TeleponOrtu"));
		}
		if (!$this->HandphoneOrtu->FldIsDetailKey) {
			$this->HandphoneOrtu->setFormValue($objForm->GetValue("x_HandphoneOrtu"));
		}
		if (!$this->EmailOrtu->FldIsDetailKey) {
			$this->EmailOrtu->setFormValue($objForm->GetValue("x_EmailOrtu"));
		}
		if (!$this->AsalSekolah->FldIsDetailKey) {
			$this->AsalSekolah->setFormValue($objForm->GetValue("x_AsalSekolah"));
		}
		if (!$this->AlamatSekolah->FldIsDetailKey) {
			$this->AlamatSekolah->setFormValue($objForm->GetValue("x_AlamatSekolah"));
		}
		if (!$this->ProvinsiIDSekolah->FldIsDetailKey) {
			$this->ProvinsiIDSekolah->setFormValue($objForm->GetValue("x_ProvinsiIDSekolah"));
		}
		if (!$this->KabupatenIDSekolah->FldIsDetailKey) {
			$this->KabupatenIDSekolah->setFormValue($objForm->GetValue("x_KabupatenIDSekolah"));
		}
		if (!$this->KecamatanIDSekolah->FldIsDetailKey) {
			$this->KecamatanIDSekolah->setFormValue($objForm->GetValue("x_KecamatanIDSekolah"));
		}
		if (!$this->DesaIDSekolah->FldIsDetailKey) {
			$this->DesaIDSekolah->setFormValue($objForm->GetValue("x_DesaIDSekolah"));
		}
		if (!$this->NilaiSekolah->FldIsDetailKey) {
			$this->NilaiSekolah->setFormValue($objForm->GetValue("x_NilaiSekolah"));
		}
		if (!$this->TahunLulus->FldIsDetailKey) {
			$this->TahunLulus->setFormValue($objForm->GetValue("x_TahunLulus"));
		}
		if (!$this->IjazahSekolah->FldIsDetailKey) {
			$this->IjazahSekolah->setFormValue($objForm->GetValue("x_IjazahSekolah"));
		}
		if (!$this->TglIjazah->FldIsDetailKey) {
			$this->TglIjazah->setFormValue($objForm->GetValue("x_TglIjazah"));
			$this->TglIjazah->CurrentValue = ew_UnFormatDateTime($this->TglIjazah->CurrentValue, 0);
		}
		if (!$this->Editor->FldIsDetailKey) {
			$this->Editor->setFormValue($objForm->GetValue("x_Editor"));
		}
		if (!$this->EditDate->FldIsDetailKey) {
			$this->EditDate->setFormValue($objForm->GetValue("x_EditDate"));
			$this->EditDate->CurrentValue = ew_UnFormatDateTime($this->EditDate->CurrentValue, 0);
		}
		if (!$this->LockStatus->FldIsDetailKey) {
			$this->LockStatus->setFormValue($objForm->GetValue("x_LockStatus"));
		}
		if (!$this->VerifiedBy->FldIsDetailKey) {
			$this->VerifiedBy->setFormValue($objForm->GetValue("x_VerifiedBy"));
		}
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->StudentID->CurrentValue = $this->StudentID->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->LevelID->CurrentValue = $this->LevelID->FormValue;
		$this->Password->CurrentValue = $this->Password->FormValue;
		$this->KampusID->CurrentValue = $this->KampusID->FormValue;
		$this->ProdiID->CurrentValue = $this->ProdiID->FormValue;
		$this->StudentStatusID->CurrentValue = $this->StudentStatusID->FormValue;
		$this->TahunID->CurrentValue = $this->TahunID->FormValue;
		$this->NIK->CurrentValue = $this->NIK->FormValue;
		$this->WargaNegara->CurrentValue = $this->WargaNegara->FormValue;
		$this->Kelamin->CurrentValue = $this->Kelamin->FormValue;
		$this->TempatLahir->CurrentValue = $this->TempatLahir->FormValue;
		$this->TanggalLahir->CurrentValue = $this->TanggalLahir->FormValue;
		$this->TanggalLahir->CurrentValue = ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0);
		$this->AgamaID->CurrentValue = $this->AgamaID->FormValue;
		$this->Darah->CurrentValue = $this->Darah->FormValue;
		$this->StatusSipil->CurrentValue = $this->StatusSipil->FormValue;
		$this->AlamatDomisili->CurrentValue = $this->AlamatDomisili->FormValue;
		$this->RT->CurrentValue = $this->RT->FormValue;
		$this->RW->CurrentValue = $this->RW->FormValue;
		$this->KodePos->CurrentValue = $this->KodePos->FormValue;
		$this->ProvinsiID->CurrentValue = $this->ProvinsiID->FormValue;
		$this->KabupatenKotaID->CurrentValue = $this->KabupatenKotaID->FormValue;
		$this->KecamatanID->CurrentValue = $this->KecamatanID->FormValue;
		$this->DesaID->CurrentValue = $this->DesaID->FormValue;
		$this->AnakKe->CurrentValue = $this->AnakKe->FormValue;
		$this->JumlahSaudara->CurrentValue = $this->JumlahSaudara->FormValue;
		$this->Telepon->CurrentValue = $this->Telepon->FormValue;
		$this->_Email->CurrentValue = $this->_Email->FormValue;
		$this->NamaAyah->CurrentValue = $this->NamaAyah->FormValue;
		$this->AgamaAyah->CurrentValue = $this->AgamaAyah->FormValue;
		$this->PendidikanAyah->CurrentValue = $this->PendidikanAyah->FormValue;
		$this->PekerjaanAyah->CurrentValue = $this->PekerjaanAyah->FormValue;
		$this->HidupAyah->CurrentValue = $this->HidupAyah->FormValue;
		$this->NamaIbu->CurrentValue = $this->NamaIbu->FormValue;
		$this->AgamaIbu->CurrentValue = $this->AgamaIbu->FormValue;
		$this->PendidikanIbu->CurrentValue = $this->PendidikanIbu->FormValue;
		$this->PekerjaanIbu->CurrentValue = $this->PekerjaanIbu->FormValue;
		$this->HidupIbu->CurrentValue = $this->HidupIbu->FormValue;
		$this->AlamatOrtu->CurrentValue = $this->AlamatOrtu->FormValue;
		$this->RTOrtu->CurrentValue = $this->RTOrtu->FormValue;
		$this->RWOrtu->CurrentValue = $this->RWOrtu->FormValue;
		$this->KodePosOrtu->CurrentValue = $this->KodePosOrtu->FormValue;
		$this->ProvinsiIDOrtu->CurrentValue = $this->ProvinsiIDOrtu->FormValue;
		$this->KabupatenIDOrtu->CurrentValue = $this->KabupatenIDOrtu->FormValue;
		$this->KecamatanIDOrtu->CurrentValue = $this->KecamatanIDOrtu->FormValue;
		$this->DesaIDOrtu->CurrentValue = $this->DesaIDOrtu->FormValue;
		$this->NegaraIDOrtu->CurrentValue = $this->NegaraIDOrtu->FormValue;
		$this->TeleponOrtu->CurrentValue = $this->TeleponOrtu->FormValue;
		$this->HandphoneOrtu->CurrentValue = $this->HandphoneOrtu->FormValue;
		$this->EmailOrtu->CurrentValue = $this->EmailOrtu->FormValue;
		$this->AsalSekolah->CurrentValue = $this->AsalSekolah->FormValue;
		$this->AlamatSekolah->CurrentValue = $this->AlamatSekolah->FormValue;
		$this->ProvinsiIDSekolah->CurrentValue = $this->ProvinsiIDSekolah->FormValue;
		$this->KabupatenIDSekolah->CurrentValue = $this->KabupatenIDSekolah->FormValue;
		$this->KecamatanIDSekolah->CurrentValue = $this->KecamatanIDSekolah->FormValue;
		$this->DesaIDSekolah->CurrentValue = $this->DesaIDSekolah->FormValue;
		$this->NilaiSekolah->CurrentValue = $this->NilaiSekolah->FormValue;
		$this->TahunLulus->CurrentValue = $this->TahunLulus->FormValue;
		$this->IjazahSekolah->CurrentValue = $this->IjazahSekolah->FormValue;
		$this->TglIjazah->CurrentValue = $this->TglIjazah->FormValue;
		$this->TglIjazah->CurrentValue = ew_UnFormatDateTime($this->TglIjazah->CurrentValue, 0);
		$this->Editor->CurrentValue = $this->Editor->FormValue;
		$this->EditDate->CurrentValue = $this->EditDate->FormValue;
		$this->EditDate->CurrentValue = ew_UnFormatDateTime($this->EditDate->CurrentValue, 0);
		$this->LockStatus->CurrentValue = $this->LockStatus->FormValue;
		$this->VerifiedBy->CurrentValue = $this->VerifiedBy->FormValue;
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
		$this->StudentID->setDbValue($rs->fields('StudentID'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->LevelID->setDbValue($rs->fields('LevelID'));
		$this->Password->setDbValue($rs->fields('Password'));
		$this->KampusID->setDbValue($rs->fields('KampusID'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->StudentStatusID->setDbValue($rs->fields('StudentStatusID'));
		$this->TahunID->setDbValue($rs->fields('TahunID'));
		$this->Foto->Upload->DbValue = $rs->fields('Foto');
		$this->Foto->CurrentValue = $this->Foto->Upload->DbValue;
		$this->NIK->setDbValue($rs->fields('NIK'));
		$this->WargaNegara->setDbValue($rs->fields('WargaNegara'));
		$this->Kelamin->setDbValue($rs->fields('Kelamin'));
		$this->TempatLahir->setDbValue($rs->fields('TempatLahir'));
		$this->TanggalLahir->setDbValue($rs->fields('TanggalLahir'));
		$this->AgamaID->setDbValue($rs->fields('AgamaID'));
		$this->Darah->setDbValue($rs->fields('Darah'));
		$this->StatusSipil->setDbValue($rs->fields('StatusSipil'));
		$this->AlamatDomisili->setDbValue($rs->fields('AlamatDomisili'));
		$this->RT->setDbValue($rs->fields('RT'));
		$this->RW->setDbValue($rs->fields('RW'));
		$this->KodePos->setDbValue($rs->fields('KodePos'));
		$this->ProvinsiID->setDbValue($rs->fields('ProvinsiID'));
		$this->KabupatenKotaID->setDbValue($rs->fields('KabupatenKotaID'));
		$this->KecamatanID->setDbValue($rs->fields('KecamatanID'));
		$this->DesaID->setDbValue($rs->fields('DesaID'));
		$this->AnakKe->setDbValue($rs->fields('AnakKe'));
		$this->JumlahSaudara->setDbValue($rs->fields('JumlahSaudara'));
		$this->Telepon->setDbValue($rs->fields('Telepon'));
		$this->Handphone->setDbValue($rs->fields('Handphone'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->NamaAyah->setDbValue($rs->fields('NamaAyah'));
		$this->AgamaAyah->setDbValue($rs->fields('AgamaAyah'));
		$this->PendidikanAyah->setDbValue($rs->fields('PendidikanAyah'));
		$this->PekerjaanAyah->setDbValue($rs->fields('PekerjaanAyah'));
		$this->HidupAyah->setDbValue($rs->fields('HidupAyah'));
		$this->NamaIbu->setDbValue($rs->fields('NamaIbu'));
		$this->AgamaIbu->setDbValue($rs->fields('AgamaIbu'));
		$this->PendidikanIbu->setDbValue($rs->fields('PendidikanIbu'));
		$this->PekerjaanIbu->setDbValue($rs->fields('PekerjaanIbu'));
		$this->HidupIbu->setDbValue($rs->fields('HidupIbu'));
		$this->AlamatOrtu->setDbValue($rs->fields('AlamatOrtu'));
		$this->RTOrtu->setDbValue($rs->fields('RTOrtu'));
		$this->RWOrtu->setDbValue($rs->fields('RWOrtu'));
		$this->KodePosOrtu->setDbValue($rs->fields('KodePosOrtu'));
		$this->ProvinsiIDOrtu->setDbValue($rs->fields('ProvinsiIDOrtu'));
		$this->KabupatenIDOrtu->setDbValue($rs->fields('KabupatenIDOrtu'));
		$this->KecamatanIDOrtu->setDbValue($rs->fields('KecamatanIDOrtu'));
		$this->DesaIDOrtu->setDbValue($rs->fields('DesaIDOrtu'));
		$this->NegaraIDOrtu->setDbValue($rs->fields('NegaraIDOrtu'));
		$this->TeleponOrtu->setDbValue($rs->fields('TeleponOrtu'));
		$this->HandphoneOrtu->setDbValue($rs->fields('HandphoneOrtu'));
		$this->EmailOrtu->setDbValue($rs->fields('EmailOrtu'));
		$this->AsalSekolah->setDbValue($rs->fields('AsalSekolah'));
		$this->AlamatSekolah->setDbValue($rs->fields('AlamatSekolah'));
		$this->ProvinsiIDSekolah->setDbValue($rs->fields('ProvinsiIDSekolah'));
		$this->KabupatenIDSekolah->setDbValue($rs->fields('KabupatenIDSekolah'));
		$this->KecamatanIDSekolah->setDbValue($rs->fields('KecamatanIDSekolah'));
		$this->DesaIDSekolah->setDbValue($rs->fields('DesaIDSekolah'));
		$this->NilaiSekolah->setDbValue($rs->fields('NilaiSekolah'));
		$this->TahunLulus->setDbValue($rs->fields('TahunLulus'));
		$this->IjazahSekolah->setDbValue($rs->fields('IjazahSekolah'));
		$this->TglIjazah->setDbValue($rs->fields('TglIjazah'));
		$this->Creator->setDbValue($rs->fields('Creator'));
		$this->CreateDate->setDbValue($rs->fields('CreateDate'));
		$this->Editor->setDbValue($rs->fields('Editor'));
		$this->EditDate->setDbValue($rs->fields('EditDate'));
		$this->LockStatus->setDbValue($rs->fields('LockStatus'));
		$this->LockDate->setDbValue($rs->fields('LockDate'));
		$this->VerifiedBy->setDbValue($rs->fields('VerifiedBy'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->StudentID->DbValue = $row['StudentID'];
		$this->Nama->DbValue = $row['Nama'];
		$this->LevelID->DbValue = $row['LevelID'];
		$this->Password->DbValue = $row['Password'];
		$this->KampusID->DbValue = $row['KampusID'];
		$this->ProdiID->DbValue = $row['ProdiID'];
		$this->StudentStatusID->DbValue = $row['StudentStatusID'];
		$this->TahunID->DbValue = $row['TahunID'];
		$this->Foto->Upload->DbValue = $row['Foto'];
		$this->NIK->DbValue = $row['NIK'];
		$this->WargaNegara->DbValue = $row['WargaNegara'];
		$this->Kelamin->DbValue = $row['Kelamin'];
		$this->TempatLahir->DbValue = $row['TempatLahir'];
		$this->TanggalLahir->DbValue = $row['TanggalLahir'];
		$this->AgamaID->DbValue = $row['AgamaID'];
		$this->Darah->DbValue = $row['Darah'];
		$this->StatusSipil->DbValue = $row['StatusSipil'];
		$this->AlamatDomisili->DbValue = $row['AlamatDomisili'];
		$this->RT->DbValue = $row['RT'];
		$this->RW->DbValue = $row['RW'];
		$this->KodePos->DbValue = $row['KodePos'];
		$this->ProvinsiID->DbValue = $row['ProvinsiID'];
		$this->KabupatenKotaID->DbValue = $row['KabupatenKotaID'];
		$this->KecamatanID->DbValue = $row['KecamatanID'];
		$this->DesaID->DbValue = $row['DesaID'];
		$this->AnakKe->DbValue = $row['AnakKe'];
		$this->JumlahSaudara->DbValue = $row['JumlahSaudara'];
		$this->Telepon->DbValue = $row['Telepon'];
		$this->Handphone->DbValue = $row['Handphone'];
		$this->_Email->DbValue = $row['Email'];
		$this->NamaAyah->DbValue = $row['NamaAyah'];
		$this->AgamaAyah->DbValue = $row['AgamaAyah'];
		$this->PendidikanAyah->DbValue = $row['PendidikanAyah'];
		$this->PekerjaanAyah->DbValue = $row['PekerjaanAyah'];
		$this->HidupAyah->DbValue = $row['HidupAyah'];
		$this->NamaIbu->DbValue = $row['NamaIbu'];
		$this->AgamaIbu->DbValue = $row['AgamaIbu'];
		$this->PendidikanIbu->DbValue = $row['PendidikanIbu'];
		$this->PekerjaanIbu->DbValue = $row['PekerjaanIbu'];
		$this->HidupIbu->DbValue = $row['HidupIbu'];
		$this->AlamatOrtu->DbValue = $row['AlamatOrtu'];
		$this->RTOrtu->DbValue = $row['RTOrtu'];
		$this->RWOrtu->DbValue = $row['RWOrtu'];
		$this->KodePosOrtu->DbValue = $row['KodePosOrtu'];
		$this->ProvinsiIDOrtu->DbValue = $row['ProvinsiIDOrtu'];
		$this->KabupatenIDOrtu->DbValue = $row['KabupatenIDOrtu'];
		$this->KecamatanIDOrtu->DbValue = $row['KecamatanIDOrtu'];
		$this->DesaIDOrtu->DbValue = $row['DesaIDOrtu'];
		$this->NegaraIDOrtu->DbValue = $row['NegaraIDOrtu'];
		$this->TeleponOrtu->DbValue = $row['TeleponOrtu'];
		$this->HandphoneOrtu->DbValue = $row['HandphoneOrtu'];
		$this->EmailOrtu->DbValue = $row['EmailOrtu'];
		$this->AsalSekolah->DbValue = $row['AsalSekolah'];
		$this->AlamatSekolah->DbValue = $row['AlamatSekolah'];
		$this->ProvinsiIDSekolah->DbValue = $row['ProvinsiIDSekolah'];
		$this->KabupatenIDSekolah->DbValue = $row['KabupatenIDSekolah'];
		$this->KecamatanIDSekolah->DbValue = $row['KecamatanIDSekolah'];
		$this->DesaIDSekolah->DbValue = $row['DesaIDSekolah'];
		$this->NilaiSekolah->DbValue = $row['NilaiSekolah'];
		$this->TahunLulus->DbValue = $row['TahunLulus'];
		$this->IjazahSekolah->DbValue = $row['IjazahSekolah'];
		$this->TglIjazah->DbValue = $row['TglIjazah'];
		$this->Creator->DbValue = $row['Creator'];
		$this->CreateDate->DbValue = $row['CreateDate'];
		$this->Editor->DbValue = $row['Editor'];
		$this->EditDate->DbValue = $row['EditDate'];
		$this->LockStatus->DbValue = $row['LockStatus'];
		$this->LockDate->DbValue = $row['LockDate'];
		$this->VerifiedBy->DbValue = $row['VerifiedBy'];
		$this->NA->DbValue = $row['NA'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// StudentID
		// Nama
		// LevelID
		// Password
		// KampusID
		// ProdiID
		// StudentStatusID
		// TahunID
		// Foto
		// NIK
		// WargaNegara
		// Kelamin
		// TempatLahir
		// TanggalLahir
		// AgamaID
		// Darah
		// StatusSipil
		// AlamatDomisili
		// RT
		// RW
		// KodePos
		// ProvinsiID
		// KabupatenKotaID
		// KecamatanID
		// DesaID
		// AnakKe
		// JumlahSaudara
		// Telepon
		// Handphone
		// Email
		// NamaAyah
		// AgamaAyah
		// PendidikanAyah
		// PekerjaanAyah
		// HidupAyah
		// NamaIbu
		// AgamaIbu
		// PendidikanIbu
		// PekerjaanIbu
		// HidupIbu
		// AlamatOrtu
		// RTOrtu
		// RWOrtu
		// KodePosOrtu
		// ProvinsiIDOrtu
		// KabupatenIDOrtu
		// KecamatanIDOrtu
		// DesaIDOrtu
		// NegaraIDOrtu
		// TeleponOrtu
		// HandphoneOrtu
		// EmailOrtu
		// AsalSekolah
		// AlamatSekolah
		// ProvinsiIDSekolah
		// KabupatenIDSekolah
		// KecamatanIDSekolah
		// DesaIDSekolah
		// NilaiSekolah
		// TahunLulus
		// IjazahSekolah
		// TglIjazah
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// LockStatus
		// LockDate
		// VerifiedBy
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// StudentID
		$this->StudentID->ViewValue = $this->StudentID->CurrentValue;
		$this->StudentID->CssStyle = "font-weight: bold;";
		$this->StudentID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->CssStyle = "font-weight: bold;";
		$this->Nama->ViewCustomAttributes = "";

		// LevelID
		$this->LevelID->ViewValue = $this->LevelID->CurrentValue;
		$this->LevelID->ViewCustomAttributes = "";

		// Password
		$this->Password->ViewValue = $Language->Phrase("PasswordMask");
		$this->Password->ViewCustomAttributes = "";

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

		// StudentStatusID
		if (strval($this->StudentStatusID->CurrentValue) <> "") {
			$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StudentStatusID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statusstudent`";
		$sWhereWrk = "";
		$this->StudentStatusID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StudentStatusID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StudentStatusID->ViewValue = $this->StudentStatusID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StudentStatusID->ViewValue = $this->StudentStatusID->CurrentValue;
			}
		} else {
			$this->StudentStatusID->ViewValue = NULL;
		}
		$this->StudentStatusID->ViewCustomAttributes = "";

		// TahunID
		$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
		$this->TahunID->ViewCustomAttributes = "";

		// Foto
		$this->Foto->UploadPath = "upload";
		if (!ew_Empty($this->Foto->Upload->DbValue)) {
			$this->Foto->ImageAlt = $this->Foto->FldAlt();
			$this->Foto->ViewValue = $this->Foto->Upload->DbValue;
		} else {
			$this->Foto->ViewValue = "";
		}
		$this->Foto->ViewCustomAttributes = "";

		// NIK
		$this->NIK->ViewValue = $this->NIK->CurrentValue;
		$this->NIK->ViewCustomAttributes = "";

		// WargaNegara
		if (strval($this->WargaNegara->CurrentValue) <> "") {
			$this->WargaNegara->ViewValue = $this->WargaNegara->OptionCaption($this->WargaNegara->CurrentValue);
		} else {
			$this->WargaNegara->ViewValue = NULL;
		}
		$this->WargaNegara->ViewCustomAttributes = "";

		// Kelamin
		if (strval($this->Kelamin->CurrentValue) <> "") {
			$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->Kelamin->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
		$sWhereWrk = "";
		$this->Kelamin->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Kelamin, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Kelamin->ViewValue = $this->Kelamin->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Kelamin->ViewValue = $this->Kelamin->CurrentValue;
			}
		} else {
			$this->Kelamin->ViewValue = NULL;
		}
		$this->Kelamin->ViewCustomAttributes = "";

		// TempatLahir
		$this->TempatLahir->ViewValue = $this->TempatLahir->CurrentValue;
		$this->TempatLahir->ViewCustomAttributes = "";

		// TanggalLahir
		$this->TanggalLahir->ViewValue = $this->TanggalLahir->CurrentValue;
		$this->TanggalLahir->ViewValue = ew_FormatDateTime($this->TanggalLahir->ViewValue, 0);
		$this->TanggalLahir->ViewCustomAttributes = "";

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

		// Darah
		if (strval($this->Darah->CurrentValue) <> "") {
			$sFilterWrk = "`DarahID`" . ew_SearchString("=", $this->Darah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `DarahID`, `DarahID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_darah`";
		$sWhereWrk = "";
		$this->Darah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Darah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Darah->ViewValue = $this->Darah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Darah->ViewValue = $this->Darah->CurrentValue;
			}
		} else {
			$this->Darah->ViewValue = NULL;
		}
		$this->Darah->ViewCustomAttributes = "";

		// StatusSipil
		if (strval($this->StatusSipil->CurrentValue) <> "") {
			$sFilterWrk = "`StatusSipil`" . ew_SearchString("=", $this->StatusSipil->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusSipil`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statussipil`";
		$sWhereWrk = "";
		$this->StatusSipil->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StatusSipil, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StatusSipil->ViewValue = $this->StatusSipil->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StatusSipil->ViewValue = $this->StatusSipil->CurrentValue;
			}
		} else {
			$this->StatusSipil->ViewValue = NULL;
		}
		$this->StatusSipil->ViewCustomAttributes = "";

		// AlamatDomisili
		$this->AlamatDomisili->ViewValue = $this->AlamatDomisili->CurrentValue;
		$this->AlamatDomisili->ViewCustomAttributes = "";

		// RT
		$this->RT->ViewValue = $this->RT->CurrentValue;
		$this->RT->ViewCustomAttributes = "";

		// RW
		$this->RW->ViewValue = $this->RW->CurrentValue;
		$this->RW->ViewCustomAttributes = "";

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

		// AnakKe
		$this->AnakKe->ViewValue = $this->AnakKe->CurrentValue;
		$this->AnakKe->ViewCustomAttributes = "";

		// JumlahSaudara
		$this->JumlahSaudara->ViewValue = $this->JumlahSaudara->CurrentValue;
		$this->JumlahSaudara->ViewCustomAttributes = "";

		// Telepon
		$this->Telepon->ViewValue = $this->Telepon->CurrentValue;
		$this->Telepon->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// NamaAyah
		$this->NamaAyah->ViewValue = $this->NamaAyah->CurrentValue;
		$this->NamaAyah->ViewCustomAttributes = "";

		// AgamaAyah
		if (strval($this->AgamaAyah->CurrentValue) <> "") {
			$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaAyah->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
		$sWhereWrk = "";
		$this->AgamaAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->AgamaAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->AgamaAyah->ViewValue = $this->AgamaAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->AgamaAyah->ViewValue = $this->AgamaAyah->CurrentValue;
			}
		} else {
			$this->AgamaAyah->ViewValue = NULL;
		}
		$this->AgamaAyah->ViewCustomAttributes = "";

		// PendidikanAyah
		if (strval($this->PendidikanAyah->CurrentValue) <> "") {
			$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanAyah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
		$sWhereWrk = "";
		$this->PendidikanAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PendidikanAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PendidikanAyah->ViewValue = $this->PendidikanAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PendidikanAyah->ViewValue = $this->PendidikanAyah->CurrentValue;
			}
		} else {
			$this->PendidikanAyah->ViewValue = NULL;
		}
		$this->PendidikanAyah->ViewCustomAttributes = "";

		// PekerjaanAyah
		if (strval($this->PekerjaanAyah->CurrentValue) <> "") {
			$sFilterWrk = "`Pekerjaan`" . ew_SearchString("=", $this->PekerjaanAyah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pekerjaan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pekerjaanortu`";
		$sWhereWrk = "";
		$this->PekerjaanAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PekerjaanAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PekerjaanAyah->ViewValue = $this->PekerjaanAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PekerjaanAyah->ViewValue = $this->PekerjaanAyah->CurrentValue;
			}
		} else {
			$this->PekerjaanAyah->ViewValue = NULL;
		}
		$this->PekerjaanAyah->ViewCustomAttributes = "";

		// HidupAyah
		if (strval($this->HidupAyah->CurrentValue) <> "") {
			$sFilterWrk = "`Hidup`" . ew_SearchString("=", $this->HidupAyah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Hidup`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hidup`";
		$sWhereWrk = "";
		$this->HidupAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->HidupAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->HidupAyah->ViewValue = $this->HidupAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->HidupAyah->ViewValue = $this->HidupAyah->CurrentValue;
			}
		} else {
			$this->HidupAyah->ViewValue = NULL;
		}
		$this->HidupAyah->ViewCustomAttributes = "";

		// NamaIbu
		$this->NamaIbu->ViewValue = $this->NamaIbu->CurrentValue;
		$this->NamaIbu->ViewCustomAttributes = "";

		// AgamaIbu
		if (strval($this->AgamaIbu->CurrentValue) <> "") {
			$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaIbu->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
		$sWhereWrk = "";
		$this->AgamaIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->AgamaIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->AgamaIbu->ViewValue = $this->AgamaIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->AgamaIbu->ViewValue = $this->AgamaIbu->CurrentValue;
			}
		} else {
			$this->AgamaIbu->ViewValue = NULL;
		}
		$this->AgamaIbu->ViewCustomAttributes = "";

		// PendidikanIbu
		if (strval($this->PendidikanIbu->CurrentValue) <> "") {
			$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanIbu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
		$sWhereWrk = "";
		$this->PendidikanIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PendidikanIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PendidikanIbu->ViewValue = $this->PendidikanIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PendidikanIbu->ViewValue = $this->PendidikanIbu->CurrentValue;
			}
		} else {
			$this->PendidikanIbu->ViewValue = NULL;
		}
		$this->PendidikanIbu->ViewCustomAttributes = "";

		// PekerjaanIbu
		if (strval($this->PekerjaanIbu->CurrentValue) <> "") {
			$sFilterWrk = "`Pekerjaan`" . ew_SearchString("=", $this->PekerjaanIbu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pekerjaan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pekerjaanortu`";
		$sWhereWrk = "";
		$this->PekerjaanIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PekerjaanIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PekerjaanIbu->ViewValue = $this->PekerjaanIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PekerjaanIbu->ViewValue = $this->PekerjaanIbu->CurrentValue;
			}
		} else {
			$this->PekerjaanIbu->ViewValue = NULL;
		}
		$this->PekerjaanIbu->ViewCustomAttributes = "";

		// HidupIbu
		if (strval($this->HidupIbu->CurrentValue) <> "") {
			$sFilterWrk = "`Hidup`" . ew_SearchString("=", $this->HidupIbu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Hidup`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hidup`";
		$sWhereWrk = "";
		$this->HidupIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->HidupIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->HidupIbu->ViewValue = $this->HidupIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->HidupIbu->ViewValue = $this->HidupIbu->CurrentValue;
			}
		} else {
			$this->HidupIbu->ViewValue = NULL;
		}
		$this->HidupIbu->ViewCustomAttributes = "";

		// AlamatOrtu
		$this->AlamatOrtu->ViewValue = $this->AlamatOrtu->CurrentValue;
		$this->AlamatOrtu->ViewCustomAttributes = "";

		// RTOrtu
		$this->RTOrtu->ViewValue = $this->RTOrtu->CurrentValue;
		$this->RTOrtu->ViewCustomAttributes = "";

		// RWOrtu
		$this->RWOrtu->ViewValue = $this->RWOrtu->CurrentValue;
		$this->RWOrtu->ViewCustomAttributes = "";

		// KodePosOrtu
		$this->KodePosOrtu->ViewValue = $this->KodePosOrtu->CurrentValue;
		$this->KodePosOrtu->ViewCustomAttributes = "";

		// ProvinsiIDOrtu
		if (strval($this->ProvinsiIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
		$sWhereWrk = "";
		$this->ProvinsiIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProvinsiIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->ProvinsiIDOrtu->ViewValue = $this->ProvinsiIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->ProvinsiIDOrtu->ViewValue = $this->ProvinsiIDOrtu->CurrentValue;
			}
		} else {
			$this->ProvinsiIDOrtu->ViewValue = NULL;
		}
		$this->ProvinsiIDOrtu->ViewCustomAttributes = "";

		// KabupatenIDOrtu
		if (strval($this->KabupatenIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->KabupatenIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KabupatenIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KabupatenIDOrtu->ViewValue = $this->KabupatenIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KabupatenIDOrtu->ViewValue = $this->KabupatenIDOrtu->CurrentValue;
			}
		} else {
			$this->KabupatenIDOrtu->ViewValue = NULL;
		}
		$this->KabupatenIDOrtu->ViewCustomAttributes = "";

		// KecamatanIDOrtu
		if (strval($this->KecamatanIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
		$sWhereWrk = "";
		$this->KecamatanIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KecamatanIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KecamatanIDOrtu->ViewValue = $this->KecamatanIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KecamatanIDOrtu->ViewValue = $this->KecamatanIDOrtu->CurrentValue;
			}
		} else {
			$this->KecamatanIDOrtu->ViewValue = NULL;
		}
		$this->KecamatanIDOrtu->ViewCustomAttributes = "";

		// DesaIDOrtu
		if (strval($this->DesaIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
		$sWhereWrk = "";
		$this->DesaIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->DesaIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->DesaIDOrtu->ViewValue = $this->DesaIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->DesaIDOrtu->ViewValue = $this->DesaIDOrtu->CurrentValue;
			}
		} else {
			$this->DesaIDOrtu->ViewValue = NULL;
		}
		$this->DesaIDOrtu->ViewCustomAttributes = "";

		// NegaraIDOrtu
		if (strval($this->NegaraIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`NegaraID`" . ew_SearchString("=", $this->NegaraIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `NegaraID`, `NamaNegara` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_negara`";
		$sWhereWrk = "";
		$this->NegaraIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->NegaraIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->NegaraIDOrtu->ViewValue = $this->NegaraIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->NegaraIDOrtu->ViewValue = $this->NegaraIDOrtu->CurrentValue;
			}
		} else {
			$this->NegaraIDOrtu->ViewValue = NULL;
		}
		$this->NegaraIDOrtu->ViewCustomAttributes = "";

		// TeleponOrtu
		$this->TeleponOrtu->ViewValue = $this->TeleponOrtu->CurrentValue;
		$this->TeleponOrtu->ViewCustomAttributes = "";

		// HandphoneOrtu
		$this->HandphoneOrtu->ViewValue = $this->HandphoneOrtu->CurrentValue;
		$this->HandphoneOrtu->ViewCustomAttributes = "";

		// EmailOrtu
		$this->EmailOrtu->ViewValue = $this->EmailOrtu->CurrentValue;
		$this->EmailOrtu->ViewCustomAttributes = "";

		// AsalSekolah
		$this->AsalSekolah->ViewValue = $this->AsalSekolah->CurrentValue;
		$this->AsalSekolah->ViewCustomAttributes = "";

		// AlamatSekolah
		$this->AlamatSekolah->ViewValue = $this->AlamatSekolah->CurrentValue;
		$this->AlamatSekolah->ViewCustomAttributes = "";

		// ProvinsiIDSekolah
		if (strval($this->ProvinsiIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
		$sWhereWrk = "";
		$this->ProvinsiIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProvinsiIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->ProvinsiIDSekolah->ViewValue = $this->ProvinsiIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->ProvinsiIDSekolah->ViewValue = $this->ProvinsiIDSekolah->CurrentValue;
			}
		} else {
			$this->ProvinsiIDSekolah->ViewValue = NULL;
		}
		$this->ProvinsiIDSekolah->ViewCustomAttributes = "";

		// KabupatenIDSekolah
		if (strval($this->KabupatenIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->KabupatenIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KabupatenIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KabupatenIDSekolah->ViewValue = $this->KabupatenIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KabupatenIDSekolah->ViewValue = $this->KabupatenIDSekolah->CurrentValue;
			}
		} else {
			$this->KabupatenIDSekolah->ViewValue = NULL;
		}
		$this->KabupatenIDSekolah->ViewCustomAttributes = "";

		// KecamatanIDSekolah
		if (strval($this->KecamatanIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
		$sWhereWrk = "";
		$this->KecamatanIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KecamatanIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KecamatanIDSekolah->ViewValue = $this->KecamatanIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KecamatanIDSekolah->ViewValue = $this->KecamatanIDSekolah->CurrentValue;
			}
		} else {
			$this->KecamatanIDSekolah->ViewValue = NULL;
		}
		$this->KecamatanIDSekolah->ViewCustomAttributes = "";

		// DesaIDSekolah
		if (strval($this->DesaIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
		$sWhereWrk = "";
		$this->DesaIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->DesaIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->DesaIDSekolah->ViewValue = $this->DesaIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->DesaIDSekolah->ViewValue = $this->DesaIDSekolah->CurrentValue;
			}
		} else {
			$this->DesaIDSekolah->ViewValue = NULL;
		}
		$this->DesaIDSekolah->ViewCustomAttributes = "";

		// NilaiSekolah
		$this->NilaiSekolah->ViewValue = $this->NilaiSekolah->CurrentValue;
		$this->NilaiSekolah->ViewCustomAttributes = "";

		// TahunLulus
		$this->TahunLulus->ViewValue = $this->TahunLulus->CurrentValue;
		$this->TahunLulus->ViewCustomAttributes = "";

		// IjazahSekolah
		$this->IjazahSekolah->ViewValue = $this->IjazahSekolah->CurrentValue;
		$this->IjazahSekolah->ViewCustomAttributes = "";

		// TglIjazah
		$this->TglIjazah->ViewValue = $this->TglIjazah->CurrentValue;
		$this->TglIjazah->ViewValue = ew_FormatDateTime($this->TglIjazah->ViewValue, 0);
		$this->TglIjazah->ViewCustomAttributes = "";

		// Editor
		$this->Editor->ViewValue = $this->Editor->CurrentValue;
		$this->Editor->ViewCustomAttributes = "";

		// EditDate
		$this->EditDate->ViewValue = $this->EditDate->CurrentValue;
		$this->EditDate->ViewValue = ew_FormatDateTime($this->EditDate->ViewValue, 0);
		$this->EditDate->ViewCustomAttributes = "";

		// LockStatus
		if (ew_ConvertToBool($this->LockStatus->CurrentValue)) {
			$this->LockStatus->ViewValue = $this->LockStatus->FldTagCaption(2) <> "" ? $this->LockStatus->FldTagCaption(2) : "Lock";
		} else {
			$this->LockStatus->ViewValue = $this->LockStatus->FldTagCaption(1) <> "" ? $this->LockStatus->FldTagCaption(1) : "Unlock";
		}
		$this->LockStatus->ViewCustomAttributes = "";

		// VerifiedBy
		$this->VerifiedBy->ViewValue = $this->VerifiedBy->CurrentValue;
		$this->VerifiedBy->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Ya";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "Tidak";
		}
		$this->NA->ViewCustomAttributes = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";
			$this->StudentID->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// LevelID
			$this->LevelID->LinkCustomAttributes = "";
			$this->LevelID->HrefValue = "";
			$this->LevelID->TooltipValue = "";

			// Password
			$this->Password->LinkCustomAttributes = "";
			$this->Password->HrefValue = "";
			$this->Password->TooltipValue = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";
			$this->KampusID->TooltipValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// StudentStatusID
			$this->StudentStatusID->LinkCustomAttributes = "";
			$this->StudentStatusID->HrefValue = "";
			$this->StudentStatusID->TooltipValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";
			$this->TahunID->TooltipValue = "";

			// Foto
			$this->Foto->LinkCustomAttributes = "";
			$this->Foto->UploadPath = "upload";
			if (!ew_Empty($this->Foto->Upload->DbValue)) {
				$this->Foto->HrefValue = ew_GetFileUploadUrl($this->Foto, $this->Foto->Upload->DbValue); // Add prefix/suffix
				$this->Foto->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->Foto->HrefValue = ew_ConvertFullUrl($this->Foto->HrefValue);
			} else {
				$this->Foto->HrefValue = "";
			}
			$this->Foto->HrefValue2 = $this->Foto->UploadPath . $this->Foto->Upload->DbValue;
			$this->Foto->TooltipValue = "";
			if ($this->Foto->UseColorbox) {
				if (ew_Empty($this->Foto->TooltipValue))
					$this->Foto->LinkAttrs["title"] = $Language->Phrase("ViewImageGallery");
				$this->Foto->LinkAttrs["data-rel"] = "student_x_Foto";
				ew_AppendClass($this->Foto->LinkAttrs["class"], "ewLightbox");
			}

			// NIK
			$this->NIK->LinkCustomAttributes = "";
			$this->NIK->HrefValue = "";
			$this->NIK->TooltipValue = "";

			// WargaNegara
			$this->WargaNegara->LinkCustomAttributes = "";
			$this->WargaNegara->HrefValue = "";
			$this->WargaNegara->TooltipValue = "";

			// Kelamin
			$this->Kelamin->LinkCustomAttributes = "";
			$this->Kelamin->HrefValue = "";
			$this->Kelamin->TooltipValue = "";

			// TempatLahir
			$this->TempatLahir->LinkCustomAttributes = "";
			$this->TempatLahir->HrefValue = "";
			$this->TempatLahir->TooltipValue = "";

			// TanggalLahir
			$this->TanggalLahir->LinkCustomAttributes = "";
			$this->TanggalLahir->HrefValue = "";
			$this->TanggalLahir->TooltipValue = "";

			// AgamaID
			$this->AgamaID->LinkCustomAttributes = "";
			$this->AgamaID->HrefValue = "";
			$this->AgamaID->TooltipValue = "";

			// Darah
			$this->Darah->LinkCustomAttributes = "";
			$this->Darah->HrefValue = "";
			$this->Darah->TooltipValue = "";

			// StatusSipil
			$this->StatusSipil->LinkCustomAttributes = "";
			$this->StatusSipil->HrefValue = "";
			$this->StatusSipil->TooltipValue = "";

			// AlamatDomisili
			$this->AlamatDomisili->LinkCustomAttributes = "";
			$this->AlamatDomisili->HrefValue = "";
			$this->AlamatDomisili->TooltipValue = "";

			// RT
			$this->RT->LinkCustomAttributes = "";
			$this->RT->HrefValue = "";
			$this->RT->TooltipValue = "";

			// RW
			$this->RW->LinkCustomAttributes = "";
			$this->RW->HrefValue = "";
			$this->RW->TooltipValue = "";

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

			// AnakKe
			$this->AnakKe->LinkCustomAttributes = "";
			$this->AnakKe->HrefValue = "";
			$this->AnakKe->TooltipValue = "";

			// JumlahSaudara
			$this->JumlahSaudara->LinkCustomAttributes = "";
			$this->JumlahSaudara->HrefValue = "";
			$this->JumlahSaudara->TooltipValue = "";

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";
			$this->Telepon->TooltipValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";

			// NamaAyah
			$this->NamaAyah->LinkCustomAttributes = "";
			$this->NamaAyah->HrefValue = "";
			$this->NamaAyah->TooltipValue = "";

			// AgamaAyah
			$this->AgamaAyah->LinkCustomAttributes = "";
			$this->AgamaAyah->HrefValue = "";
			$this->AgamaAyah->TooltipValue = "";

			// PendidikanAyah
			$this->PendidikanAyah->LinkCustomAttributes = "";
			$this->PendidikanAyah->HrefValue = "";
			$this->PendidikanAyah->TooltipValue = "";

			// PekerjaanAyah
			$this->PekerjaanAyah->LinkCustomAttributes = "";
			$this->PekerjaanAyah->HrefValue = "";
			$this->PekerjaanAyah->TooltipValue = "";

			// HidupAyah
			$this->HidupAyah->LinkCustomAttributes = "";
			$this->HidupAyah->HrefValue = "";
			$this->HidupAyah->TooltipValue = "";

			// NamaIbu
			$this->NamaIbu->LinkCustomAttributes = "";
			$this->NamaIbu->HrefValue = "";
			$this->NamaIbu->TooltipValue = "";

			// AgamaIbu
			$this->AgamaIbu->LinkCustomAttributes = "";
			$this->AgamaIbu->HrefValue = "";
			$this->AgamaIbu->TooltipValue = "";

			// PendidikanIbu
			$this->PendidikanIbu->LinkCustomAttributes = "";
			$this->PendidikanIbu->HrefValue = "";
			$this->PendidikanIbu->TooltipValue = "";

			// PekerjaanIbu
			$this->PekerjaanIbu->LinkCustomAttributes = "";
			$this->PekerjaanIbu->HrefValue = "";
			$this->PekerjaanIbu->TooltipValue = "";

			// HidupIbu
			$this->HidupIbu->LinkCustomAttributes = "";
			$this->HidupIbu->HrefValue = "";
			$this->HidupIbu->TooltipValue = "";

			// AlamatOrtu
			$this->AlamatOrtu->LinkCustomAttributes = "";
			$this->AlamatOrtu->HrefValue = "";
			$this->AlamatOrtu->TooltipValue = "";

			// RTOrtu
			$this->RTOrtu->LinkCustomAttributes = "";
			$this->RTOrtu->HrefValue = "";
			$this->RTOrtu->TooltipValue = "";

			// RWOrtu
			$this->RWOrtu->LinkCustomAttributes = "";
			$this->RWOrtu->HrefValue = "";
			$this->RWOrtu->TooltipValue = "";

			// KodePosOrtu
			$this->KodePosOrtu->LinkCustomAttributes = "";
			$this->KodePosOrtu->HrefValue = "";
			$this->KodePosOrtu->TooltipValue = "";

			// ProvinsiIDOrtu
			$this->ProvinsiIDOrtu->LinkCustomAttributes = "";
			$this->ProvinsiIDOrtu->HrefValue = "";
			$this->ProvinsiIDOrtu->TooltipValue = "";

			// KabupatenIDOrtu
			$this->KabupatenIDOrtu->LinkCustomAttributes = "";
			$this->KabupatenIDOrtu->HrefValue = "";
			$this->KabupatenIDOrtu->TooltipValue = "";

			// KecamatanIDOrtu
			$this->KecamatanIDOrtu->LinkCustomAttributes = "";
			$this->KecamatanIDOrtu->HrefValue = "";
			$this->KecamatanIDOrtu->TooltipValue = "";

			// DesaIDOrtu
			$this->DesaIDOrtu->LinkCustomAttributes = "";
			$this->DesaIDOrtu->HrefValue = "";
			$this->DesaIDOrtu->TooltipValue = "";

			// NegaraIDOrtu
			$this->NegaraIDOrtu->LinkCustomAttributes = "";
			$this->NegaraIDOrtu->HrefValue = "";
			$this->NegaraIDOrtu->TooltipValue = "";

			// TeleponOrtu
			$this->TeleponOrtu->LinkCustomAttributes = "";
			$this->TeleponOrtu->HrefValue = "";
			$this->TeleponOrtu->TooltipValue = "";

			// HandphoneOrtu
			$this->HandphoneOrtu->LinkCustomAttributes = "";
			$this->HandphoneOrtu->HrefValue = "";
			$this->HandphoneOrtu->TooltipValue = "";

			// EmailOrtu
			$this->EmailOrtu->LinkCustomAttributes = "";
			$this->EmailOrtu->HrefValue = "";
			$this->EmailOrtu->TooltipValue = "";

			// AsalSekolah
			$this->AsalSekolah->LinkCustomAttributes = "";
			$this->AsalSekolah->HrefValue = "";
			$this->AsalSekolah->TooltipValue = "";

			// AlamatSekolah
			$this->AlamatSekolah->LinkCustomAttributes = "";
			$this->AlamatSekolah->HrefValue = "";
			$this->AlamatSekolah->TooltipValue = "";

			// ProvinsiIDSekolah
			$this->ProvinsiIDSekolah->LinkCustomAttributes = "";
			$this->ProvinsiIDSekolah->HrefValue = "";
			$this->ProvinsiIDSekolah->TooltipValue = "";

			// KabupatenIDSekolah
			$this->KabupatenIDSekolah->LinkCustomAttributes = "";
			$this->KabupatenIDSekolah->HrefValue = "";
			$this->KabupatenIDSekolah->TooltipValue = "";

			// KecamatanIDSekolah
			$this->KecamatanIDSekolah->LinkCustomAttributes = "";
			$this->KecamatanIDSekolah->HrefValue = "";
			$this->KecamatanIDSekolah->TooltipValue = "";

			// DesaIDSekolah
			$this->DesaIDSekolah->LinkCustomAttributes = "";
			$this->DesaIDSekolah->HrefValue = "";
			$this->DesaIDSekolah->TooltipValue = "";

			// NilaiSekolah
			$this->NilaiSekolah->LinkCustomAttributes = "";
			$this->NilaiSekolah->HrefValue = "";
			$this->NilaiSekolah->TooltipValue = "";

			// TahunLulus
			$this->TahunLulus->LinkCustomAttributes = "";
			$this->TahunLulus->HrefValue = "";
			$this->TahunLulus->TooltipValue = "";

			// IjazahSekolah
			$this->IjazahSekolah->LinkCustomAttributes = "";
			$this->IjazahSekolah->HrefValue = "";
			$this->IjazahSekolah->TooltipValue = "";

			// TglIjazah
			$this->TglIjazah->LinkCustomAttributes = "";
			$this->TglIjazah->HrefValue = "";
			$this->TglIjazah->TooltipValue = "";

			// Editor
			$this->Editor->LinkCustomAttributes = "";
			$this->Editor->HrefValue = "";
			$this->Editor->TooltipValue = "";

			// EditDate
			$this->EditDate->LinkCustomAttributes = "";
			$this->EditDate->HrefValue = "";
			$this->EditDate->TooltipValue = "";

			// LockStatus
			$this->LockStatus->LinkCustomAttributes = "";
			$this->LockStatus->HrefValue = "";
			$this->LockStatus->TooltipValue = "";

			// VerifiedBy
			$this->VerifiedBy->LinkCustomAttributes = "";
			$this->VerifiedBy->HrefValue = "";
			$this->VerifiedBy->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// StudentID
			$this->StudentID->EditAttrs["class"] = "form-control";
			$this->StudentID->EditCustomAttributes = "";
			$this->StudentID->EditValue = $this->StudentID->CurrentValue;
			$this->StudentID->CssStyle = "font-weight: bold;";
			$this->StudentID->ViewCustomAttributes = "";

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

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

			// KampusID
			$this->KampusID->EditAttrs["class"] = "form-control";
			$this->KampusID->EditCustomAttributes = "";
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
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProdiID->EditValue = $arwrk;

			// StudentStatusID
			$this->StudentStatusID->EditAttrs["class"] = "form-control";
			$this->StudentStatusID->EditCustomAttributes = "";
			if (trim(strval($this->StudentStatusID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StudentStatusID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_statusstudent`";
			$sWhereWrk = "";
			$this->StudentStatusID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StudentStatusID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StudentStatusID->EditValue = $arwrk;

			// TahunID
			$this->TahunID->EditAttrs["class"] = "form-control";
			$this->TahunID->EditCustomAttributes = "";
			$this->TahunID->EditValue = ew_HtmlEncode($this->TahunID->CurrentValue);
			$this->TahunID->PlaceHolder = ew_RemoveHtml($this->TahunID->FldCaption());

			// Foto
			$this->Foto->EditAttrs["class"] = "form-control";
			$this->Foto->EditCustomAttributes = "";
			$this->Foto->UploadPath = "upload";
			if (!ew_Empty($this->Foto->Upload->DbValue)) {
				$this->Foto->ImageAlt = $this->Foto->FldAlt();
				$this->Foto->EditValue = $this->Foto->Upload->DbValue;
			} else {
				$this->Foto->EditValue = "";
			}
			if (!ew_Empty($this->Foto->CurrentValue))
				$this->Foto->Upload->FileName = $this->Foto->CurrentValue;
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->Foto);

			// NIK
			$this->NIK->EditAttrs["class"] = "form-control";
			$this->NIK->EditCustomAttributes = "";
			$this->NIK->EditValue = ew_HtmlEncode($this->NIK->CurrentValue);
			$this->NIK->PlaceHolder = ew_RemoveHtml($this->NIK->FldCaption());

			// WargaNegara
			$this->WargaNegara->EditCustomAttributes = "";
			$this->WargaNegara->EditValue = $this->WargaNegara->Options(FALSE);

			// Kelamin
			$this->Kelamin->EditCustomAttributes = "";
			if (trim(strval($this->Kelamin->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->Kelamin->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_kelamin`";
			$sWhereWrk = "";
			$this->Kelamin->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Kelamin, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Kelamin->EditValue = $arwrk;

			// TempatLahir
			$this->TempatLahir->EditAttrs["class"] = "form-control";
			$this->TempatLahir->EditCustomAttributes = "";
			$this->TempatLahir->EditValue = ew_HtmlEncode($this->TempatLahir->CurrentValue);
			$this->TempatLahir->PlaceHolder = ew_RemoveHtml($this->TempatLahir->FldCaption());

			// TanggalLahir
			$this->TanggalLahir->EditAttrs["class"] = "form-control";
			$this->TanggalLahir->EditCustomAttributes = "";
			$this->TanggalLahir->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->TanggalLahir->CurrentValue, 8));
			$this->TanggalLahir->PlaceHolder = ew_RemoveHtml($this->TanggalLahir->FldCaption());

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

			// Darah
			$this->Darah->EditCustomAttributes = "";
			if (trim(strval($this->Darah->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`DarahID`" . ew_SearchString("=", $this->Darah->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `DarahID`, `DarahID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_darah`";
			$sWhereWrk = "";
			$this->Darah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Darah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Darah->EditValue = $arwrk;

			// StatusSipil
			$this->StatusSipil->EditAttrs["class"] = "form-control";
			$this->StatusSipil->EditCustomAttributes = "";
			if (trim(strval($this->StatusSipil->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StatusSipil`" . ew_SearchString("=", $this->StatusSipil->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StatusSipil`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_statussipil`";
			$sWhereWrk = "";
			$this->StatusSipil->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StatusSipil, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StatusSipil->EditValue = $arwrk;

			// AlamatDomisili
			$this->AlamatDomisili->EditAttrs["class"] = "form-control";
			$this->AlamatDomisili->EditCustomAttributes = "";
			$this->AlamatDomisili->EditValue = ew_HtmlEncode($this->AlamatDomisili->CurrentValue);
			$this->AlamatDomisili->PlaceHolder = ew_RemoveHtml($this->AlamatDomisili->FldCaption());

			// RT
			$this->RT->EditAttrs["class"] = "form-control";
			$this->RT->EditCustomAttributes = "";
			$this->RT->EditValue = ew_HtmlEncode($this->RT->CurrentValue);
			$this->RT->PlaceHolder = ew_RemoveHtml($this->RT->FldCaption());

			// RW
			$this->RW->EditAttrs["class"] = "form-control";
			$this->RW->EditCustomAttributes = "";
			$this->RW->EditValue = ew_HtmlEncode($this->RW->CurrentValue);
			$this->RW->PlaceHolder = ew_RemoveHtml($this->RW->FldCaption());

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

			// AnakKe
			$this->AnakKe->EditAttrs["class"] = "form-control";
			$this->AnakKe->EditCustomAttributes = "";
			$this->AnakKe->EditValue = ew_HtmlEncode($this->AnakKe->CurrentValue);
			$this->AnakKe->PlaceHolder = ew_RemoveHtml($this->AnakKe->FldCaption());

			// JumlahSaudara
			$this->JumlahSaudara->EditAttrs["class"] = "form-control";
			$this->JumlahSaudara->EditCustomAttributes = "";
			$this->JumlahSaudara->EditValue = ew_HtmlEncode($this->JumlahSaudara->CurrentValue);
			$this->JumlahSaudara->PlaceHolder = ew_RemoveHtml($this->JumlahSaudara->FldCaption());

			// Telepon
			$this->Telepon->EditAttrs["class"] = "form-control";
			$this->Telepon->EditCustomAttributes = "";
			$this->Telepon->EditValue = ew_HtmlEncode($this->Telepon->CurrentValue);
			$this->Telepon->PlaceHolder = ew_RemoveHtml($this->Telepon->FldCaption());

			// Email
			$this->_Email->EditAttrs["class"] = "form-control";
			$this->_Email->EditCustomAttributes = "";
			$this->_Email->EditValue = ew_HtmlEncode($this->_Email->CurrentValue);
			$this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

			// NamaAyah
			$this->NamaAyah->EditAttrs["class"] = "form-control";
			$this->NamaAyah->EditCustomAttributes = "";
			$this->NamaAyah->EditValue = ew_HtmlEncode($this->NamaAyah->CurrentValue);
			$this->NamaAyah->PlaceHolder = ew_RemoveHtml($this->NamaAyah->FldCaption());

			// AgamaAyah
			$this->AgamaAyah->EditAttrs["class"] = "form-control";
			$this->AgamaAyah->EditCustomAttributes = "";
			if (trim(strval($this->AgamaAyah->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaAyah->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaAyah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->AgamaAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->AgamaAyah->EditValue = $arwrk;

			// PendidikanAyah
			$this->PendidikanAyah->EditAttrs["class"] = "form-control";
			$this->PendidikanAyah->EditCustomAttributes = "";
			if (trim(strval($this->PendidikanAyah->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanAyah->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanAyah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->PendidikanAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->PendidikanAyah->EditValue = $arwrk;

			// PekerjaanAyah
			$this->PekerjaanAyah->EditAttrs["class"] = "form-control";
			$this->PekerjaanAyah->EditCustomAttributes = "";
			if (trim(strval($this->PekerjaanAyah->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Pekerjaan`" . ew_SearchString("=", $this->PekerjaanAyah->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Pekerjaan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_pekerjaanortu`";
			$sWhereWrk = "";
			$this->PekerjaanAyah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->PekerjaanAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->PekerjaanAyah->EditValue = $arwrk;

			// HidupAyah
			$this->HidupAyah->EditAttrs["class"] = "form-control";
			$this->HidupAyah->EditCustomAttributes = "";
			if (trim(strval($this->HidupAyah->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Hidup`" . ew_SearchString("=", $this->HidupAyah->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Hidup`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_hidup`";
			$sWhereWrk = "";
			$this->HidupAyah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->HidupAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->HidupAyah->EditValue = $arwrk;

			// NamaIbu
			$this->NamaIbu->EditAttrs["class"] = "form-control";
			$this->NamaIbu->EditCustomAttributes = "";
			$this->NamaIbu->EditValue = ew_HtmlEncode($this->NamaIbu->CurrentValue);
			$this->NamaIbu->PlaceHolder = ew_RemoveHtml($this->NamaIbu->FldCaption());

			// AgamaIbu
			$this->AgamaIbu->EditAttrs["class"] = "form-control";
			$this->AgamaIbu->EditCustomAttributes = "";
			if (trim(strval($this->AgamaIbu->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaIbu->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaIbu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->AgamaIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->AgamaIbu->EditValue = $arwrk;

			// PendidikanIbu
			$this->PendidikanIbu->EditAttrs["class"] = "form-control";
			$this->PendidikanIbu->EditCustomAttributes = "";
			if (trim(strval($this->PendidikanIbu->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanIbu->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanIbu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->PendidikanIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->PendidikanIbu->EditValue = $arwrk;

			// PekerjaanIbu
			$this->PekerjaanIbu->EditAttrs["class"] = "form-control";
			$this->PekerjaanIbu->EditCustomAttributes = "";
			if (trim(strval($this->PekerjaanIbu->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Pekerjaan`" . ew_SearchString("=", $this->PekerjaanIbu->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Pekerjaan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_pekerjaanortu`";
			$sWhereWrk = "";
			$this->PekerjaanIbu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->PekerjaanIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->PekerjaanIbu->EditValue = $arwrk;

			// HidupIbu
			$this->HidupIbu->EditAttrs["class"] = "form-control";
			$this->HidupIbu->EditCustomAttributes = "";
			if (trim(strval($this->HidupIbu->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Hidup`" . ew_SearchString("=", $this->HidupIbu->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Hidup`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_hidup`";
			$sWhereWrk = "";
			$this->HidupIbu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->HidupIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->HidupIbu->EditValue = $arwrk;

			// AlamatOrtu
			$this->AlamatOrtu->EditAttrs["class"] = "form-control";
			$this->AlamatOrtu->EditCustomAttributes = "";
			$this->AlamatOrtu->EditValue = ew_HtmlEncode($this->AlamatOrtu->CurrentValue);
			$this->AlamatOrtu->PlaceHolder = ew_RemoveHtml($this->AlamatOrtu->FldCaption());

			// RTOrtu
			$this->RTOrtu->EditAttrs["class"] = "form-control";
			$this->RTOrtu->EditCustomAttributes = "";
			$this->RTOrtu->EditValue = ew_HtmlEncode($this->RTOrtu->CurrentValue);
			$this->RTOrtu->PlaceHolder = ew_RemoveHtml($this->RTOrtu->FldCaption());

			// RWOrtu
			$this->RWOrtu->EditAttrs["class"] = "form-control";
			$this->RWOrtu->EditCustomAttributes = "";
			$this->RWOrtu->EditValue = ew_HtmlEncode($this->RWOrtu->CurrentValue);
			$this->RWOrtu->PlaceHolder = ew_RemoveHtml($this->RWOrtu->FldCaption());

			// KodePosOrtu
			$this->KodePosOrtu->EditAttrs["class"] = "form-control";
			$this->KodePosOrtu->EditCustomAttributes = "";
			$this->KodePosOrtu->EditValue = ew_HtmlEncode($this->KodePosOrtu->CurrentValue);
			$this->KodePosOrtu->PlaceHolder = ew_RemoveHtml($this->KodePosOrtu->FldCaption());

			// ProvinsiIDOrtu
			$this->ProvinsiIDOrtu->EditAttrs["class"] = "form-control";
			$this->ProvinsiIDOrtu->EditCustomAttributes = "";
			if (trim(strval($this->ProvinsiIDOrtu->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiIDOrtu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->ProvinsiIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProvinsiIDOrtu->EditValue = $arwrk;

			// KabupatenIDOrtu
			$this->KabupatenIDOrtu->EditAttrs["class"] = "form-control";
			$this->KabupatenIDOrtu->EditCustomAttributes = "";
			if (trim(strval($this->KabupatenIDOrtu->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProvinsiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "";
			$this->KabupatenIDOrtu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KabupatenIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KabupatenIDOrtu->EditValue = $arwrk;

			// KecamatanIDOrtu
			$this->KecamatanIDOrtu->EditAttrs["class"] = "form-control";
			$this->KecamatanIDOrtu->EditCustomAttributes = "";
			if (trim(strval($this->KecamatanIDOrtu->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KabupatenKotaID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "";
			$this->KecamatanIDOrtu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KecamatanIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KecamatanIDOrtu->EditValue = $arwrk;

			// DesaIDOrtu
			$this->DesaIDOrtu->EditAttrs["class"] = "form-control";
			$this->DesaIDOrtu->EditCustomAttributes = "";
			if (trim(strval($this->DesaIDOrtu->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KecamatanID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_desa`";
			$sWhereWrk = "";
			$this->DesaIDOrtu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->DesaIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->DesaIDOrtu->EditValue = $arwrk;

			// NegaraIDOrtu
			$this->NegaraIDOrtu->EditAttrs["class"] = "form-control";
			$this->NegaraIDOrtu->EditCustomAttributes = "";
			if (trim(strval($this->NegaraIDOrtu->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`NegaraID`" . ew_SearchString("=", $this->NegaraIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `NegaraID`, `NamaNegara` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_negara`";
			$sWhereWrk = "";
			$this->NegaraIDOrtu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->NegaraIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->NegaraIDOrtu->EditValue = $arwrk;

			// TeleponOrtu
			$this->TeleponOrtu->EditAttrs["class"] = "form-control";
			$this->TeleponOrtu->EditCustomAttributes = "";
			$this->TeleponOrtu->EditValue = ew_HtmlEncode($this->TeleponOrtu->CurrentValue);
			$this->TeleponOrtu->PlaceHolder = ew_RemoveHtml($this->TeleponOrtu->FldCaption());

			// HandphoneOrtu
			$this->HandphoneOrtu->EditAttrs["class"] = "form-control";
			$this->HandphoneOrtu->EditCustomAttributes = "";
			$this->HandphoneOrtu->EditValue = ew_HtmlEncode($this->HandphoneOrtu->CurrentValue);
			$this->HandphoneOrtu->PlaceHolder = ew_RemoveHtml($this->HandphoneOrtu->FldCaption());

			// EmailOrtu
			$this->EmailOrtu->EditAttrs["class"] = "form-control";
			$this->EmailOrtu->EditCustomAttributes = "";
			$this->EmailOrtu->EditValue = ew_HtmlEncode($this->EmailOrtu->CurrentValue);
			$this->EmailOrtu->PlaceHolder = ew_RemoveHtml($this->EmailOrtu->FldCaption());

			// AsalSekolah
			$this->AsalSekolah->EditAttrs["class"] = "form-control";
			$this->AsalSekolah->EditCustomAttributes = "";
			$this->AsalSekolah->EditValue = ew_HtmlEncode($this->AsalSekolah->CurrentValue);
			$this->AsalSekolah->PlaceHolder = ew_RemoveHtml($this->AsalSekolah->FldCaption());

			// AlamatSekolah
			$this->AlamatSekolah->EditAttrs["class"] = "form-control";
			$this->AlamatSekolah->EditCustomAttributes = "";
			$this->AlamatSekolah->EditValue = ew_HtmlEncode($this->AlamatSekolah->CurrentValue);
			$this->AlamatSekolah->PlaceHolder = ew_RemoveHtml($this->AlamatSekolah->FldCaption());

			// ProvinsiIDSekolah
			$this->ProvinsiIDSekolah->EditAttrs["class"] = "form-control";
			$this->ProvinsiIDSekolah->EditCustomAttributes = "";
			if (trim(strval($this->ProvinsiIDSekolah->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiIDSekolah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->ProvinsiIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProvinsiIDSekolah->EditValue = $arwrk;

			// KabupatenIDSekolah
			$this->KabupatenIDSekolah->EditAttrs["class"] = "form-control";
			$this->KabupatenIDSekolah->EditCustomAttributes = "";
			if (trim(strval($this->KabupatenIDSekolah->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProvinsiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "";
			$this->KabupatenIDSekolah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KabupatenIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KabupatenIDSekolah->EditValue = $arwrk;

			// KecamatanIDSekolah
			$this->KecamatanIDSekolah->EditAttrs["class"] = "form-control";
			$this->KecamatanIDSekolah->EditCustomAttributes = "";
			if (trim(strval($this->KecamatanIDSekolah->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KabupatenKotaID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "";
			$this->KecamatanIDSekolah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KecamatanIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KecamatanIDSekolah->EditValue = $arwrk;

			// DesaIDSekolah
			$this->DesaIDSekolah->EditAttrs["class"] = "form-control";
			$this->DesaIDSekolah->EditCustomAttributes = "";
			if (trim(strval($this->DesaIDSekolah->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KecamatanID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_desa`";
			$sWhereWrk = "";
			$this->DesaIDSekolah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->DesaIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->DesaIDSekolah->EditValue = $arwrk;

			// NilaiSekolah
			$this->NilaiSekolah->EditAttrs["class"] = "form-control";
			$this->NilaiSekolah->EditCustomAttributes = "";
			$this->NilaiSekolah->EditValue = ew_HtmlEncode($this->NilaiSekolah->CurrentValue);
			$this->NilaiSekolah->PlaceHolder = ew_RemoveHtml($this->NilaiSekolah->FldCaption());

			// TahunLulus
			$this->TahunLulus->EditAttrs["class"] = "form-control";
			$this->TahunLulus->EditCustomAttributes = "";
			$this->TahunLulus->EditValue = ew_HtmlEncode($this->TahunLulus->CurrentValue);
			$this->TahunLulus->PlaceHolder = ew_RemoveHtml($this->TahunLulus->FldCaption());

			// IjazahSekolah
			$this->IjazahSekolah->EditAttrs["class"] = "form-control";
			$this->IjazahSekolah->EditCustomAttributes = "";
			$this->IjazahSekolah->EditValue = ew_HtmlEncode($this->IjazahSekolah->CurrentValue);
			$this->IjazahSekolah->PlaceHolder = ew_RemoveHtml($this->IjazahSekolah->FldCaption());

			// TglIjazah
			$this->TglIjazah->EditAttrs["class"] = "form-control";
			$this->TglIjazah->EditCustomAttributes = "";
			$this->TglIjazah->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->TglIjazah->CurrentValue, 8));
			$this->TglIjazah->PlaceHolder = ew_RemoveHtml($this->TglIjazah->FldCaption());

			// Editor
			// EditDate
			// LockStatus

			$this->LockStatus->EditCustomAttributes = "";
			$this->LockStatus->EditValue = $this->LockStatus->Options(FALSE);

			// VerifiedBy
			// NA

			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Edit refer script
			// StudentID

			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// LevelID
			$this->LevelID->LinkCustomAttributes = "";
			$this->LevelID->HrefValue = "";

			// Password
			$this->Password->LinkCustomAttributes = "";
			$this->Password->HrefValue = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";

			// StudentStatusID
			$this->StudentStatusID->LinkCustomAttributes = "";
			$this->StudentStatusID->HrefValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";

			// Foto
			$this->Foto->LinkCustomAttributes = "";
			$this->Foto->UploadPath = "upload";
			if (!ew_Empty($this->Foto->Upload->DbValue)) {
				$this->Foto->HrefValue = ew_GetFileUploadUrl($this->Foto, $this->Foto->Upload->DbValue); // Add prefix/suffix
				$this->Foto->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->Foto->HrefValue = ew_ConvertFullUrl($this->Foto->HrefValue);
			} else {
				$this->Foto->HrefValue = "";
			}
			$this->Foto->HrefValue2 = $this->Foto->UploadPath . $this->Foto->Upload->DbValue;

			// NIK
			$this->NIK->LinkCustomAttributes = "";
			$this->NIK->HrefValue = "";

			// WargaNegara
			$this->WargaNegara->LinkCustomAttributes = "";
			$this->WargaNegara->HrefValue = "";

			// Kelamin
			$this->Kelamin->LinkCustomAttributes = "";
			$this->Kelamin->HrefValue = "";

			// TempatLahir
			$this->TempatLahir->LinkCustomAttributes = "";
			$this->TempatLahir->HrefValue = "";

			// TanggalLahir
			$this->TanggalLahir->LinkCustomAttributes = "";
			$this->TanggalLahir->HrefValue = "";

			// AgamaID
			$this->AgamaID->LinkCustomAttributes = "";
			$this->AgamaID->HrefValue = "";

			// Darah
			$this->Darah->LinkCustomAttributes = "";
			$this->Darah->HrefValue = "";

			// StatusSipil
			$this->StatusSipil->LinkCustomAttributes = "";
			$this->StatusSipil->HrefValue = "";

			// AlamatDomisili
			$this->AlamatDomisili->LinkCustomAttributes = "";
			$this->AlamatDomisili->HrefValue = "";

			// RT
			$this->RT->LinkCustomAttributes = "";
			$this->RT->HrefValue = "";

			// RW
			$this->RW->LinkCustomAttributes = "";
			$this->RW->HrefValue = "";

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

			// AnakKe
			$this->AnakKe->LinkCustomAttributes = "";
			$this->AnakKe->HrefValue = "";

			// JumlahSaudara
			$this->JumlahSaudara->LinkCustomAttributes = "";
			$this->JumlahSaudara->HrefValue = "";

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";

			// NamaAyah
			$this->NamaAyah->LinkCustomAttributes = "";
			$this->NamaAyah->HrefValue = "";

			// AgamaAyah
			$this->AgamaAyah->LinkCustomAttributes = "";
			$this->AgamaAyah->HrefValue = "";

			// PendidikanAyah
			$this->PendidikanAyah->LinkCustomAttributes = "";
			$this->PendidikanAyah->HrefValue = "";

			// PekerjaanAyah
			$this->PekerjaanAyah->LinkCustomAttributes = "";
			$this->PekerjaanAyah->HrefValue = "";

			// HidupAyah
			$this->HidupAyah->LinkCustomAttributes = "";
			$this->HidupAyah->HrefValue = "";

			// NamaIbu
			$this->NamaIbu->LinkCustomAttributes = "";
			$this->NamaIbu->HrefValue = "";

			// AgamaIbu
			$this->AgamaIbu->LinkCustomAttributes = "";
			$this->AgamaIbu->HrefValue = "";

			// PendidikanIbu
			$this->PendidikanIbu->LinkCustomAttributes = "";
			$this->PendidikanIbu->HrefValue = "";

			// PekerjaanIbu
			$this->PekerjaanIbu->LinkCustomAttributes = "";
			$this->PekerjaanIbu->HrefValue = "";

			// HidupIbu
			$this->HidupIbu->LinkCustomAttributes = "";
			$this->HidupIbu->HrefValue = "";

			// AlamatOrtu
			$this->AlamatOrtu->LinkCustomAttributes = "";
			$this->AlamatOrtu->HrefValue = "";

			// RTOrtu
			$this->RTOrtu->LinkCustomAttributes = "";
			$this->RTOrtu->HrefValue = "";

			// RWOrtu
			$this->RWOrtu->LinkCustomAttributes = "";
			$this->RWOrtu->HrefValue = "";

			// KodePosOrtu
			$this->KodePosOrtu->LinkCustomAttributes = "";
			$this->KodePosOrtu->HrefValue = "";

			// ProvinsiIDOrtu
			$this->ProvinsiIDOrtu->LinkCustomAttributes = "";
			$this->ProvinsiIDOrtu->HrefValue = "";

			// KabupatenIDOrtu
			$this->KabupatenIDOrtu->LinkCustomAttributes = "";
			$this->KabupatenIDOrtu->HrefValue = "";

			// KecamatanIDOrtu
			$this->KecamatanIDOrtu->LinkCustomAttributes = "";
			$this->KecamatanIDOrtu->HrefValue = "";

			// DesaIDOrtu
			$this->DesaIDOrtu->LinkCustomAttributes = "";
			$this->DesaIDOrtu->HrefValue = "";

			// NegaraIDOrtu
			$this->NegaraIDOrtu->LinkCustomAttributes = "";
			$this->NegaraIDOrtu->HrefValue = "";

			// TeleponOrtu
			$this->TeleponOrtu->LinkCustomAttributes = "";
			$this->TeleponOrtu->HrefValue = "";

			// HandphoneOrtu
			$this->HandphoneOrtu->LinkCustomAttributes = "";
			$this->HandphoneOrtu->HrefValue = "";

			// EmailOrtu
			$this->EmailOrtu->LinkCustomAttributes = "";
			$this->EmailOrtu->HrefValue = "";

			// AsalSekolah
			$this->AsalSekolah->LinkCustomAttributes = "";
			$this->AsalSekolah->HrefValue = "";

			// AlamatSekolah
			$this->AlamatSekolah->LinkCustomAttributes = "";
			$this->AlamatSekolah->HrefValue = "";

			// ProvinsiIDSekolah
			$this->ProvinsiIDSekolah->LinkCustomAttributes = "";
			$this->ProvinsiIDSekolah->HrefValue = "";

			// KabupatenIDSekolah
			$this->KabupatenIDSekolah->LinkCustomAttributes = "";
			$this->KabupatenIDSekolah->HrefValue = "";

			// KecamatanIDSekolah
			$this->KecamatanIDSekolah->LinkCustomAttributes = "";
			$this->KecamatanIDSekolah->HrefValue = "";

			// DesaIDSekolah
			$this->DesaIDSekolah->LinkCustomAttributes = "";
			$this->DesaIDSekolah->HrefValue = "";

			// NilaiSekolah
			$this->NilaiSekolah->LinkCustomAttributes = "";
			$this->NilaiSekolah->HrefValue = "";

			// TahunLulus
			$this->TahunLulus->LinkCustomAttributes = "";
			$this->TahunLulus->HrefValue = "";

			// IjazahSekolah
			$this->IjazahSekolah->LinkCustomAttributes = "";
			$this->IjazahSekolah->HrefValue = "";

			// TglIjazah
			$this->TglIjazah->LinkCustomAttributes = "";
			$this->TglIjazah->HrefValue = "";

			// Editor
			$this->Editor->LinkCustomAttributes = "";
			$this->Editor->HrefValue = "";

			// EditDate
			$this->EditDate->LinkCustomAttributes = "";
			$this->EditDate->HrefValue = "";

			// LockStatus
			$this->LockStatus->LinkCustomAttributes = "";
			$this->LockStatus->HrefValue = "";

			// VerifiedBy
			$this->VerifiedBy->LinkCustomAttributes = "";
			$this->VerifiedBy->HrefValue = "";

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
		if (!$this->StudentID->FldIsDetailKey && !is_null($this->StudentID->FormValue) && $this->StudentID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->StudentID->FldCaption(), $this->StudentID->ReqErrMsg));
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
		if (!ew_CheckInteger($this->TahunID->FormValue)) {
			ew_AddMessage($gsFormError, $this->TahunID->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TanggalLahir->FormValue)) {
			ew_AddMessage($gsFormError, $this->TanggalLahir->FldErrMsg());
		}
		if (!ew_CheckInteger($this->AnakKe->FormValue)) {
			ew_AddMessage($gsFormError, $this->AnakKe->FldErrMsg());
		}
		if (!ew_CheckInteger($this->JumlahSaudara->FormValue)) {
			ew_AddMessage($gsFormError, $this->JumlahSaudara->FldErrMsg());
		}
		if (!ew_CheckEmail($this->EmailOrtu->FormValue)) {
			ew_AddMessage($gsFormError, $this->EmailOrtu->FldErrMsg());
		}
		if (!ew_CheckNumber($this->NilaiSekolah->FormValue)) {
			ew_AddMessage($gsFormError, $this->NilaiSekolah->FldErrMsg());
		}
		if (!ew_CheckInteger($this->TahunLulus->FormValue)) {
			ew_AddMessage($gsFormError, $this->TahunLulus->FldErrMsg());
		}
		if (!$this->TglIjazah->FldIsDetailKey && !is_null($this->TglIjazah->FormValue) && $this->TglIjazah->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TglIjazah->FldCaption(), $this->TglIjazah->ReqErrMsg));
		}
		if (!ew_CheckDateDef($this->TglIjazah->FormValue)) {
			ew_AddMessage($gsFormError, $this->TglIjazah->FldErrMsg());
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
			$this->Foto->OldUploadPath = "upload";
			$this->Foto->UploadPath = $this->Foto->OldUploadPath;
			$rsnew = array();

			// StudentID
			// Nama

			$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, NULL, $this->Nama->ReadOnly);

			// LevelID
			$this->LevelID->SetDbValueDef($rsnew, $this->LevelID->CurrentValue, "", $this->LevelID->ReadOnly);

			// Password
			$this->Password->SetDbValueDef($rsnew, $this->Password->CurrentValue, NULL, $this->Password->ReadOnly);

			// KampusID
			$this->KampusID->SetDbValueDef($rsnew, $this->KampusID->CurrentValue, NULL, $this->KampusID->ReadOnly);

			// ProdiID
			$this->ProdiID->SetDbValueDef($rsnew, $this->ProdiID->CurrentValue, NULL, $this->ProdiID->ReadOnly);

			// StudentStatusID
			$this->StudentStatusID->SetDbValueDef($rsnew, $this->StudentStatusID->CurrentValue, NULL, $this->StudentStatusID->ReadOnly);

			// TahunID
			$this->TahunID->SetDbValueDef($rsnew, $this->TahunID->CurrentValue, NULL, $this->TahunID->ReadOnly);

			// Foto
			if ($this->Foto->Visible && !$this->Foto->ReadOnly && !$this->Foto->Upload->KeepFile) {
				$this->Foto->Upload->DbValue = $rsold['Foto']; // Get original value
				if ($this->Foto->Upload->FileName == "") {
					$rsnew['Foto'] = NULL;
				} else {
					$rsnew['Foto'] = $this->Foto->Upload->FileName;
				}
			}

			// NIK
			$this->NIK->SetDbValueDef($rsnew, $this->NIK->CurrentValue, NULL, $this->NIK->ReadOnly);

			// WargaNegara
			$this->WargaNegara->SetDbValueDef($rsnew, $this->WargaNegara->CurrentValue, NULL, $this->WargaNegara->ReadOnly);

			// Kelamin
			$this->Kelamin->SetDbValueDef($rsnew, $this->Kelamin->CurrentValue, NULL, $this->Kelamin->ReadOnly);

			// TempatLahir
			$this->TempatLahir->SetDbValueDef($rsnew, $this->TempatLahir->CurrentValue, NULL, $this->TempatLahir->ReadOnly);

			// TanggalLahir
			$this->TanggalLahir->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0), NULL, $this->TanggalLahir->ReadOnly);

			// AgamaID
			$this->AgamaID->SetDbValueDef($rsnew, $this->AgamaID->CurrentValue, NULL, $this->AgamaID->ReadOnly);

			// Darah
			$this->Darah->SetDbValueDef($rsnew, $this->Darah->CurrentValue, NULL, $this->Darah->ReadOnly);

			// StatusSipil
			$this->StatusSipil->SetDbValueDef($rsnew, $this->StatusSipil->CurrentValue, NULL, $this->StatusSipil->ReadOnly);

			// AlamatDomisili
			$this->AlamatDomisili->SetDbValueDef($rsnew, $this->AlamatDomisili->CurrentValue, NULL, $this->AlamatDomisili->ReadOnly);

			// RT
			$this->RT->SetDbValueDef($rsnew, $this->RT->CurrentValue, NULL, $this->RT->ReadOnly);

			// RW
			$this->RW->SetDbValueDef($rsnew, $this->RW->CurrentValue, NULL, $this->RW->ReadOnly);

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

			// AnakKe
			$this->AnakKe->SetDbValueDef($rsnew, $this->AnakKe->CurrentValue, NULL, $this->AnakKe->ReadOnly);

			// JumlahSaudara
			$this->JumlahSaudara->SetDbValueDef($rsnew, $this->JumlahSaudara->CurrentValue, NULL, $this->JumlahSaudara->ReadOnly);

			// Telepon
			$this->Telepon->SetDbValueDef($rsnew, $this->Telepon->CurrentValue, NULL, $this->Telepon->ReadOnly);

			// Email
			$this->_Email->SetDbValueDef($rsnew, $this->_Email->CurrentValue, NULL, $this->_Email->ReadOnly);

			// NamaAyah
			$this->NamaAyah->SetDbValueDef($rsnew, $this->NamaAyah->CurrentValue, NULL, $this->NamaAyah->ReadOnly);

			// AgamaAyah
			$this->AgamaAyah->SetDbValueDef($rsnew, $this->AgamaAyah->CurrentValue, NULL, $this->AgamaAyah->ReadOnly);

			// PendidikanAyah
			$this->PendidikanAyah->SetDbValueDef($rsnew, $this->PendidikanAyah->CurrentValue, NULL, $this->PendidikanAyah->ReadOnly);

			// PekerjaanAyah
			$this->PekerjaanAyah->SetDbValueDef($rsnew, $this->PekerjaanAyah->CurrentValue, NULL, $this->PekerjaanAyah->ReadOnly);

			// HidupAyah
			$this->HidupAyah->SetDbValueDef($rsnew, $this->HidupAyah->CurrentValue, NULL, $this->HidupAyah->ReadOnly);

			// NamaIbu
			$this->NamaIbu->SetDbValueDef($rsnew, $this->NamaIbu->CurrentValue, NULL, $this->NamaIbu->ReadOnly);

			// AgamaIbu
			$this->AgamaIbu->SetDbValueDef($rsnew, $this->AgamaIbu->CurrentValue, NULL, $this->AgamaIbu->ReadOnly);

			// PendidikanIbu
			$this->PendidikanIbu->SetDbValueDef($rsnew, $this->PendidikanIbu->CurrentValue, NULL, $this->PendidikanIbu->ReadOnly);

			// PekerjaanIbu
			$this->PekerjaanIbu->SetDbValueDef($rsnew, $this->PekerjaanIbu->CurrentValue, NULL, $this->PekerjaanIbu->ReadOnly);

			// HidupIbu
			$this->HidupIbu->SetDbValueDef($rsnew, $this->HidupIbu->CurrentValue, NULL, $this->HidupIbu->ReadOnly);

			// AlamatOrtu
			$this->AlamatOrtu->SetDbValueDef($rsnew, $this->AlamatOrtu->CurrentValue, NULL, $this->AlamatOrtu->ReadOnly);

			// RTOrtu
			$this->RTOrtu->SetDbValueDef($rsnew, $this->RTOrtu->CurrentValue, NULL, $this->RTOrtu->ReadOnly);

			// RWOrtu
			$this->RWOrtu->SetDbValueDef($rsnew, $this->RWOrtu->CurrentValue, NULL, $this->RWOrtu->ReadOnly);

			// KodePosOrtu
			$this->KodePosOrtu->SetDbValueDef($rsnew, $this->KodePosOrtu->CurrentValue, NULL, $this->KodePosOrtu->ReadOnly);

			// ProvinsiIDOrtu
			$this->ProvinsiIDOrtu->SetDbValueDef($rsnew, $this->ProvinsiIDOrtu->CurrentValue, NULL, $this->ProvinsiIDOrtu->ReadOnly);

			// KabupatenIDOrtu
			$this->KabupatenIDOrtu->SetDbValueDef($rsnew, $this->KabupatenIDOrtu->CurrentValue, NULL, $this->KabupatenIDOrtu->ReadOnly);

			// KecamatanIDOrtu
			$this->KecamatanIDOrtu->SetDbValueDef($rsnew, $this->KecamatanIDOrtu->CurrentValue, NULL, $this->KecamatanIDOrtu->ReadOnly);

			// DesaIDOrtu
			$this->DesaIDOrtu->SetDbValueDef($rsnew, $this->DesaIDOrtu->CurrentValue, NULL, $this->DesaIDOrtu->ReadOnly);

			// NegaraIDOrtu
			$this->NegaraIDOrtu->SetDbValueDef($rsnew, $this->NegaraIDOrtu->CurrentValue, NULL, $this->NegaraIDOrtu->ReadOnly);

			// TeleponOrtu
			$this->TeleponOrtu->SetDbValueDef($rsnew, $this->TeleponOrtu->CurrentValue, NULL, $this->TeleponOrtu->ReadOnly);

			// HandphoneOrtu
			$this->HandphoneOrtu->SetDbValueDef($rsnew, $this->HandphoneOrtu->CurrentValue, NULL, $this->HandphoneOrtu->ReadOnly);

			// EmailOrtu
			$this->EmailOrtu->SetDbValueDef($rsnew, $this->EmailOrtu->CurrentValue, NULL, $this->EmailOrtu->ReadOnly);

			// AsalSekolah
			$this->AsalSekolah->SetDbValueDef($rsnew, $this->AsalSekolah->CurrentValue, NULL, $this->AsalSekolah->ReadOnly);

			// AlamatSekolah
			$this->AlamatSekolah->SetDbValueDef($rsnew, $this->AlamatSekolah->CurrentValue, NULL, $this->AlamatSekolah->ReadOnly);

			// ProvinsiIDSekolah
			$this->ProvinsiIDSekolah->SetDbValueDef($rsnew, $this->ProvinsiIDSekolah->CurrentValue, NULL, $this->ProvinsiIDSekolah->ReadOnly);

			// KabupatenIDSekolah
			$this->KabupatenIDSekolah->SetDbValueDef($rsnew, $this->KabupatenIDSekolah->CurrentValue, NULL, $this->KabupatenIDSekolah->ReadOnly);

			// KecamatanIDSekolah
			$this->KecamatanIDSekolah->SetDbValueDef($rsnew, $this->KecamatanIDSekolah->CurrentValue, NULL, $this->KecamatanIDSekolah->ReadOnly);

			// DesaIDSekolah
			$this->DesaIDSekolah->SetDbValueDef($rsnew, $this->DesaIDSekolah->CurrentValue, NULL, $this->DesaIDSekolah->ReadOnly);

			// NilaiSekolah
			$this->NilaiSekolah->SetDbValueDef($rsnew, $this->NilaiSekolah->CurrentValue, NULL, $this->NilaiSekolah->ReadOnly);

			// TahunLulus
			$this->TahunLulus->SetDbValueDef($rsnew, $this->TahunLulus->CurrentValue, NULL, $this->TahunLulus->ReadOnly);

			// IjazahSekolah
			$this->IjazahSekolah->SetDbValueDef($rsnew, $this->IjazahSekolah->CurrentValue, NULL, $this->IjazahSekolah->ReadOnly);

			// TglIjazah
			$this->TglIjazah->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TglIjazah->CurrentValue, 0), NULL, $this->TglIjazah->ReadOnly);

			// Editor
			$this->Editor->SetDbValueDef($rsnew, CurrentUserName(), NULL);
			$rsnew['Editor'] = &$this->Editor->DbValue;

			// EditDate
			$this->EditDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
			$rsnew['EditDate'] = &$this->EditDate->DbValue;

			// LockStatus
			$this->LockStatus->SetDbValueDef($rsnew, ((strval($this->LockStatus->CurrentValue) == "1") ? "1" : "0"), NULL, $this->LockStatus->ReadOnly);

			// VerifiedBy
			$this->VerifiedBy->SetDbValueDef($rsnew, CurrentUserName(), NULL);
			$rsnew['VerifiedBy'] = &$this->VerifiedBy->DbValue;

			// NA
			$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), NULL, $this->NA->ReadOnly);
			if ($this->Foto->Visible && !$this->Foto->Upload->KeepFile) {
				$this->Foto->UploadPath = "upload";
				if (!ew_Empty($this->Foto->Upload->Value)) {
					$rsnew['Foto'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->Foto->UploadPath), $rsnew['Foto']); // Get new file name
				}
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
					if ($this->Foto->Visible && !$this->Foto->Upload->KeepFile) {
						if (!ew_Empty($this->Foto->Upload->Value)) {
							if (!$this->Foto->Upload->SaveToFile($this->Foto->UploadPath, $rsnew['Foto'], TRUE)) {
								$this->setFailureMessage($Language->Phrase("UploadErrMsg7"));
								return FALSE;
							}
						}
					}
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

		// Foto
		ew_CleanUploadTempPath($this->Foto, $this->Foto->Upload->Index);
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("studentlist.php"), "", $this->TableVar, TRUE);
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
		$pages->Add(5);
		$this->MultiPages = $pages;
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
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StudentStatusID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StatusStudentID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statusstudent`";
			$sWhereWrk = "";
			$this->StudentStatusID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StatusStudentID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StudentStatusID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_Kelamin":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Kelamin` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
			$sWhereWrk = "";
			$this->Kelamin->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Kelamin` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Kelamin, $sWhereWrk); // Call Lookup selecting
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
		case "x_Darah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `DarahID` AS `LinkFld`, `DarahID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_darah`";
			$sWhereWrk = "";
			$this->Darah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`DarahID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Darah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StatusSipil":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StatusSipil` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statussipil`";
			$sWhereWrk = "";
			$this->StatusSipil->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StatusSipil` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StatusSipil, $sWhereWrk); // Call Lookup selecting
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
		case "x_AgamaAyah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `AgamaID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaAyah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`AgamaID` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->AgamaAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_PendidikanAyah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Pendidikan` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanAyah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Pendidikan` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->PendidikanAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_PekerjaanAyah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Pekerjaan` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pekerjaanortu`";
			$sWhereWrk = "";
			$this->PekerjaanAyah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Pekerjaan` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->PekerjaanAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_HidupAyah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Hidup` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hidup`";
			$sWhereWrk = "";
			$this->HidupAyah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Hidup` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->HidupAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_AgamaIbu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `AgamaID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaIbu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`AgamaID` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->AgamaIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_PendidikanIbu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Pendidikan` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanIbu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Pendidikan` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->PendidikanIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_PekerjaanIbu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Pekerjaan` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pekerjaanortu`";
			$sWhereWrk = "";
			$this->PekerjaanIbu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Pekerjaan` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->PekerjaanIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_HidupIbu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Hidup` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hidup`";
			$sWhereWrk = "";
			$this->HidupIbu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Hidup` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->HidupIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_ProvinsiIDOrtu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProvinsiID` AS `LinkFld`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiIDOrtu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProvinsiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->ProvinsiIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KabupatenIDOrtu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KabupatenKotaID` AS `LinkFld`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "{filter}";
			$this->KabupatenIDOrtu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KabupatenKotaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProvinsiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KabupatenIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KecamatanIDOrtu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KecamatanID` AS `LinkFld`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "{filter}";
			$this->KecamatanIDOrtu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KecamatanID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KabupatenKotaID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KecamatanIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_DesaIDOrtu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `DesaID` AS `LinkFld`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
			$sWhereWrk = "{filter}";
			$this->DesaIDOrtu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`DesaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KecamatanID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->DesaIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_NegaraIDOrtu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `NegaraID` AS `LinkFld`, `NamaNegara` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_negara`";
			$sWhereWrk = "";
			$this->NegaraIDOrtu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`NegaraID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->NegaraIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_ProvinsiIDSekolah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProvinsiID` AS `LinkFld`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiIDSekolah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProvinsiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->ProvinsiIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KabupatenIDSekolah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KabupatenKotaID` AS `LinkFld`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "{filter}";
			$this->KabupatenIDSekolah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KabupatenKotaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProvinsiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KabupatenIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KecamatanIDSekolah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KecamatanID` AS `LinkFld`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "{filter}";
			$this->KecamatanIDSekolah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KecamatanID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KabupatenKotaID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KecamatanIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_DesaIDSekolah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `DesaID` AS `LinkFld`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
			$sWhereWrk = "{filter}";
			$this->DesaIDSekolah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`DesaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KecamatanID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->DesaIDSekolah, $sWhereWrk); // Call Lookup selecting
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
if (!isset($student_edit)) $student_edit = new cstudent_edit();

// Page init
$student_edit->Page_Init();

// Page main
$student_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$student_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fstudentedit = new ew_Form("fstudentedit", "edit");

// Validate form
fstudentedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_StudentID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $student->StudentID->FldCaption(), $student->StudentID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_LevelID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $student->LevelID->FldCaption(), $student->LevelID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_LevelID");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->LevelID->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Password");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $student->Password->FldCaption(), $student->Password->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TahunID");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->TahunID->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_TanggalLahir");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->TanggalLahir->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_AnakKe");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->AnakKe->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_JumlahSaudara");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->JumlahSaudara->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_EmailOrtu");
			if (elm && !ew_CheckEmail(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->EmailOrtu->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_NilaiSekolah");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->NilaiSekolah->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_TahunLulus");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->TahunLulus->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_TglIjazah");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $student->TglIjazah->FldCaption(), $student->TglIjazah->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TglIjazah");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->TglIjazah->FldErrMsg()) ?>");

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
fstudentedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fstudentedit.ValidateRequired = true;
<?php } else { ?>
fstudentedit.ValidateRequired = false; 
<?php } ?>

// Multi-Page
fstudentedit.MultiPage = new ew_MultiPage("fstudentedit");

// Dynamic selection lists
fstudentedit.Lists["x_KampusID"] = {"LinkField":"x_KampusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kampus"};
fstudentedit.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fstudentedit.Lists["x_StudentStatusID"] = {"LinkField":"x_StatusStudentID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statusstudent"};
fstudentedit.Lists["x_WargaNegara"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fstudentedit.Lists["x_WargaNegara"].Options = <?php echo json_encode($student->WargaNegara->Options()) ?>;
fstudentedit.Lists["x_Kelamin"] = {"LinkField":"x_Kelamin","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kelamin"};
fstudentedit.Lists["x_AgamaID"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fstudentedit.Lists["x_Darah"] = {"LinkField":"x_DarahID","Ajax":true,"AutoFill":false,"DisplayFields":["x_DarahID","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_darah"};
fstudentedit.Lists["x_StatusSipil"] = {"LinkField":"x_StatusSipil","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statussipil"};
fstudentedit.Lists["x_ProvinsiID"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenKotaID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fstudentedit.Lists["x_KabupatenKotaID"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":["x_ProvinsiID"],"ChildFields":["x_KecamatanID"],"FilterFields":["x_ProvinsiID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstudentedit.Lists["x_KecamatanID"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":["x_KabupatenKotaID"],"ChildFields":["x_DesaID"],"FilterFields":["x_KabupatenKotaID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fstudentedit.Lists["x_DesaID"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":["x_KecamatanID"],"ChildFields":[],"FilterFields":["x_KecamatanID"],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fstudentedit.Lists["x_AgamaAyah"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fstudentedit.Lists["x_PendidikanAyah"] = {"LinkField":"x_Pendidikan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pendidikanortu"};
fstudentedit.Lists["x_PekerjaanAyah"] = {"LinkField":"x_Pekerjaan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pekerjaanortu"};
fstudentedit.Lists["x_HidupAyah"] = {"LinkField":"x_Hidup","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_hidup"};
fstudentedit.Lists["x_AgamaIbu"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fstudentedit.Lists["x_PendidikanIbu"] = {"LinkField":"x_Pendidikan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pendidikanortu"};
fstudentedit.Lists["x_PekerjaanIbu"] = {"LinkField":"x_Pekerjaan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pekerjaanortu"};
fstudentedit.Lists["x_HidupIbu"] = {"LinkField":"x_Hidup","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_hidup"};
fstudentedit.Lists["x_ProvinsiIDOrtu"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenIDOrtu"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fstudentedit.Lists["x_KabupatenIDOrtu"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":["x_ProvinsiIDOrtu"],"ChildFields":["x_KecamatanIDOrtu"],"FilterFields":["x_ProvinsiID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstudentedit.Lists["x_KecamatanIDOrtu"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":["x_KabupatenIDOrtu"],"ChildFields":["x_DesaIDOrtu"],"FilterFields":["x_KabupatenKotaID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fstudentedit.Lists["x_DesaIDOrtu"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":["x_KecamatanIDOrtu"],"ChildFields":[],"FilterFields":["x_KecamatanID"],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fstudentedit.Lists["x_NegaraIDOrtu"] = {"LinkField":"x_NegaraID","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaNegara","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_negara"};
fstudentedit.Lists["x_ProvinsiIDSekolah"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenIDSekolah"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fstudentedit.Lists["x_KabupatenIDSekolah"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":["x_ProvinsiIDSekolah"],"ChildFields":["x_KecamatanIDSekolah"],"FilterFields":["x_ProvinsiID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstudentedit.Lists["x_KecamatanIDSekolah"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":["x_KabupatenIDSekolah"],"ChildFields":["x_DesaIDSekolah"],"FilterFields":["x_KabupatenKotaID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fstudentedit.Lists["x_DesaIDSekolah"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":["x_KecamatanIDSekolah"],"ChildFields":[],"FilterFields":["x_KecamatanID"],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fstudentedit.Lists["x_LockStatus"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fstudentedit.Lists["x_LockStatus"].Options = <?php echo json_encode($student->LockStatus->Options()) ?>;
fstudentedit.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fstudentedit.Lists["x_NA"].Options = <?php echo json_encode($student->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$student_edit->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $student_edit->ShowPageHeader(); ?>
<?php
$student_edit->ShowMessage();
?>
<form name="fstudentedit" id="fstudentedit" class="<?php echo $student_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($student_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $student_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="student">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<?php if ($student_edit->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$student_edit->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<div class="ewMultiPage">
<div class="tabbable" id="student_edit">
	<ul class="nav<?php echo $student_edit->MultiPages->NavStyle() ?>">
		<li<?php echo $student_edit->MultiPages->TabStyle("1") ?>><a href="#tab_student1" data-toggle="tab"><?php echo $student->PageCaption(1) ?></a></li>
		<li<?php echo $student_edit->MultiPages->TabStyle("2") ?>><a href="#tab_student2" data-toggle="tab"><?php echo $student->PageCaption(2) ?></a></li>
		<li<?php echo $student_edit->MultiPages->TabStyle("3") ?>><a href="#tab_student3" data-toggle="tab"><?php echo $student->PageCaption(3) ?></a></li>
		<li<?php echo $student_edit->MultiPages->TabStyle("4") ?>><a href="#tab_student4" data-toggle="tab"><?php echo $student->PageCaption(4) ?></a></li>
		<li<?php echo $student_edit->MultiPages->TabStyle("5") ?>><a href="#tab_student5" data-toggle="tab"><?php echo $student->PageCaption(5) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane<?php echo $student_edit->MultiPages->PageStyle("1") ?>" id="tab_student1">
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_studentedit1" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($student->StudentID->Visible) { // StudentID ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_StudentID" class="form-group">
		<label id="elh_student_StudentID" for="x_StudentID" class="col-sm-2 control-label ewLabel"><?php echo $student->StudentID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $student->StudentID->CellAttributes() ?>>
<span id="el_student_StudentID">
<span<?php echo $student->StudentID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->StudentID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_StudentID" data-page="1" name="x_StudentID" id="x_StudentID" value="<?php echo ew_HtmlEncode($student->StudentID->CurrentValue) ?>">
<?php echo $student->StudentID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_StudentID">
		<td><span id="elh_student_StudentID"><?php echo $student->StudentID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $student->StudentID->CellAttributes() ?>>
<span id="el_student_StudentID">
<span<?php echo $student->StudentID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->StudentID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_StudentID" data-page="1" name="x_StudentID" id="x_StudentID" value="<?php echo ew_HtmlEncode($student->StudentID->CurrentValue) ?>">
<?php echo $student->StudentID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label id="elh_student_Nama" for="x_Nama" class="col-sm-2 control-label ewLabel"><?php echo $student->Nama->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->Nama->CellAttributes() ?>>
<span id="el_student_Nama">
<input type="text" data-table="student" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Nama->getPlaceHolder()) ?>" value="<?php echo $student->Nama->EditValue ?>"<?php echo $student->Nama->EditAttributes() ?>>
</span>
<?php echo $student->Nama->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_student_Nama"><?php echo $student->Nama->FldCaption() ?></span></td>
		<td<?php echo $student->Nama->CellAttributes() ?>>
<span id="el_student_Nama">
<input type="text" data-table="student" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Nama->getPlaceHolder()) ?>" value="<?php echo $student->Nama->EditValue ?>"<?php echo $student->Nama->EditAttributes() ?>>
</span>
<?php echo $student->Nama->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->LevelID->Visible) { // LevelID ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_LevelID" class="form-group">
		<label id="elh_student_LevelID" for="x_LevelID" class="col-sm-2 control-label ewLabel"><?php echo $student->LevelID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $student->LevelID->CellAttributes() ?>>
<span id="el_student_LevelID">
<input type="text" data-table="student" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($student->LevelID->getPlaceHolder()) ?>" value="<?php echo $student->LevelID->EditValue ?>"<?php echo $student->LevelID->EditAttributes() ?>>
</span>
<?php echo $student->LevelID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_LevelID">
		<td><span id="elh_student_LevelID"><?php echo $student->LevelID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $student->LevelID->CellAttributes() ?>>
<span id="el_student_LevelID">
<input type="text" data-table="student" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($student->LevelID->getPlaceHolder()) ?>" value="<?php echo $student->LevelID->EditValue ?>"<?php echo $student->LevelID->EditAttributes() ?>>
</span>
<?php echo $student->LevelID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->Password->Visible) { // Password ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_Password" class="form-group">
		<label id="elh_student_Password" for="x_Password" class="col-sm-2 control-label ewLabel"><?php echo $student->Password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $student->Password->CellAttributes() ?>>
<span id="el_student_Password">
<input type="password" data-field="x_Password" name="x_Password" id="x_Password" value="<?php echo $student->Password->EditValue ?>" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($student->Password->getPlaceHolder()) ?>"<?php echo $student->Password->EditAttributes() ?>>
</span>
<?php echo $student->Password->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Password">
		<td><span id="elh_student_Password"><?php echo $student->Password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $student->Password->CellAttributes() ?>>
<span id="el_student_Password">
<input type="password" data-field="x_Password" name="x_Password" id="x_Password" value="<?php echo $student->Password->EditValue ?>" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($student->Password->getPlaceHolder()) ?>"<?php echo $student->Password->EditAttributes() ?>>
</span>
<?php echo $student->Password->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KampusID->Visible) { // KampusID ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_KampusID" class="form-group">
		<label id="elh_student_KampusID" for="x_KampusID" class="col-sm-2 control-label ewLabel"><?php echo $student->KampusID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->KampusID->CellAttributes() ?>>
<span id="el_student_KampusID">
<select data-table="student" data-field="x_KampusID" data-page="1" data-value-separator="<?php echo $student->KampusID->DisplayValueSeparatorAttribute() ?>" id="x_KampusID" name="x_KampusID"<?php echo $student->KampusID->EditAttributes() ?>>
<?php echo $student->KampusID->SelectOptionListHtml("x_KampusID") ?>
</select>
<input type="hidden" name="s_x_KampusID" id="s_x_KampusID" value="<?php echo $student->KampusID->LookupFilterQuery() ?>">
</span>
<?php echo $student->KampusID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KampusID">
		<td><span id="elh_student_KampusID"><?php echo $student->KampusID->FldCaption() ?></span></td>
		<td<?php echo $student->KampusID->CellAttributes() ?>>
<span id="el_student_KampusID">
<select data-table="student" data-field="x_KampusID" data-page="1" data-value-separator="<?php echo $student->KampusID->DisplayValueSeparatorAttribute() ?>" id="x_KampusID" name="x_KampusID"<?php echo $student->KampusID->EditAttributes() ?>>
<?php echo $student->KampusID->SelectOptionListHtml("x_KampusID") ?>
</select>
<input type="hidden" name="s_x_KampusID" id="s_x_KampusID" value="<?php echo $student->KampusID->LookupFilterQuery() ?>">
</span>
<?php echo $student->KampusID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label id="elh_student_ProdiID" for="x_ProdiID" class="col-sm-2 control-label ewLabel"><?php echo $student->ProdiID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->ProdiID->CellAttributes() ?>>
<span id="el_student_ProdiID">
<select data-table="student" data-field="x_ProdiID" data-page="1" data-value-separator="<?php echo $student->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $student->ProdiID->EditAttributes() ?>>
<?php echo $student->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $student->ProdiID->LookupFilterQuery() ?>">
</span>
<?php echo $student->ProdiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_student_ProdiID"><?php echo $student->ProdiID->FldCaption() ?></span></td>
		<td<?php echo $student->ProdiID->CellAttributes() ?>>
<span id="el_student_ProdiID">
<select data-table="student" data-field="x_ProdiID" data-page="1" data-value-separator="<?php echo $student->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $student->ProdiID->EditAttributes() ?>>
<?php echo $student->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $student->ProdiID->LookupFilterQuery() ?>">
</span>
<?php echo $student->ProdiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->StudentStatusID->Visible) { // StudentStatusID ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_StudentStatusID" class="form-group">
		<label id="elh_student_StudentStatusID" for="x_StudentStatusID" class="col-sm-2 control-label ewLabel"><?php echo $student->StudentStatusID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->StudentStatusID->CellAttributes() ?>>
<span id="el_student_StudentStatusID">
<select data-table="student" data-field="x_StudentStatusID" data-page="1" data-value-separator="<?php echo $student->StudentStatusID->DisplayValueSeparatorAttribute() ?>" id="x_StudentStatusID" name="x_StudentStatusID"<?php echo $student->StudentStatusID->EditAttributes() ?>>
<?php echo $student->StudentStatusID->SelectOptionListHtml("x_StudentStatusID") ?>
</select>
<input type="hidden" name="s_x_StudentStatusID" id="s_x_StudentStatusID" value="<?php echo $student->StudentStatusID->LookupFilterQuery() ?>">
</span>
<?php echo $student->StudentStatusID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_StudentStatusID">
		<td><span id="elh_student_StudentStatusID"><?php echo $student->StudentStatusID->FldCaption() ?></span></td>
		<td<?php echo $student->StudentStatusID->CellAttributes() ?>>
<span id="el_student_StudentStatusID">
<select data-table="student" data-field="x_StudentStatusID" data-page="1" data-value-separator="<?php echo $student->StudentStatusID->DisplayValueSeparatorAttribute() ?>" id="x_StudentStatusID" name="x_StudentStatusID"<?php echo $student->StudentStatusID->EditAttributes() ?>>
<?php echo $student->StudentStatusID->SelectOptionListHtml("x_StudentStatusID") ?>
</select>
<input type="hidden" name="s_x_StudentStatusID" id="s_x_StudentStatusID" value="<?php echo $student->StudentStatusID->LookupFilterQuery() ?>">
</span>
<?php echo $student->StudentStatusID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TahunID->Visible) { // TahunID ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_TahunID" class="form-group">
		<label id="elh_student_TahunID" for="x_TahunID" class="col-sm-2 control-label ewLabel"><?php echo $student->TahunID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->TahunID->CellAttributes() ?>>
<span id="el_student_TahunID">
<input type="text" data-table="student" data-field="x_TahunID" data-page="1" name="x_TahunID" id="x_TahunID" size="30" placeholder="<?php echo ew_HtmlEncode($student->TahunID->getPlaceHolder()) ?>" value="<?php echo $student->TahunID->EditValue ?>"<?php echo $student->TahunID->EditAttributes() ?>>
</span>
<?php echo $student->TahunID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TahunID">
		<td><span id="elh_student_TahunID"><?php echo $student->TahunID->FldCaption() ?></span></td>
		<td<?php echo $student->TahunID->CellAttributes() ?>>
<span id="el_student_TahunID">
<input type="text" data-table="student" data-field="x_TahunID" data-page="1" name="x_TahunID" id="x_TahunID" size="30" placeholder="<?php echo ew_HtmlEncode($student->TahunID->getPlaceHolder()) ?>" value="<?php echo $student->TahunID->EditValue ?>"<?php echo $student->TahunID->EditAttributes() ?>>
</span>
<?php echo $student->TahunID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->Foto->Visible) { // Foto ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_Foto" class="form-group">
		<label id="elh_student_Foto" class="col-sm-2 control-label ewLabel"><?php echo $student->Foto->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->Foto->CellAttributes() ?>>
<span id="el_student_Foto">
<div id="fd_x_Foto">
<span title="<?php echo $student->Foto->FldTitle() ? $student->Foto->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($student->Foto->ReadOnly || $student->Foto->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="student" data-field="x_Foto" data-page="1" name="x_Foto" id="x_Foto"<?php echo $student->Foto->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x_Foto" id= "fn_x_Foto" value="<?php echo $student->Foto->Upload->FileName ?>">
<?php if (@$_POST["fa_x_Foto"] == "0") { ?>
<input type="hidden" name="fa_x_Foto" id= "fa_x_Foto" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_Foto" id= "fa_x_Foto" value="1">
<?php } ?>
<input type="hidden" name="fs_x_Foto" id= "fs_x_Foto" value="255">
<input type="hidden" name="fx_x_Foto" id= "fx_x_Foto" value="<?php echo $student->Foto->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_Foto" id= "fm_x_Foto" value="<?php echo $student->Foto->UploadMaxFileSize ?>">
</div>
<table id="ft_x_Foto" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $student->Foto->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Foto">
		<td><span id="elh_student_Foto"><?php echo $student->Foto->FldCaption() ?></span></td>
		<td<?php echo $student->Foto->CellAttributes() ?>>
<span id="el_student_Foto">
<div id="fd_x_Foto">
<span title="<?php echo $student->Foto->FldTitle() ? $student->Foto->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($student->Foto->ReadOnly || $student->Foto->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="student" data-field="x_Foto" data-page="1" name="x_Foto" id="x_Foto"<?php echo $student->Foto->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x_Foto" id= "fn_x_Foto" value="<?php echo $student->Foto->Upload->FileName ?>">
<?php if (@$_POST["fa_x_Foto"] == "0") { ?>
<input type="hidden" name="fa_x_Foto" id= "fa_x_Foto" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_Foto" id= "fa_x_Foto" value="1">
<?php } ?>
<input type="hidden" name="fs_x_Foto" id= "fs_x_Foto" value="255">
<input type="hidden" name="fx_x_Foto" id= "fx_x_Foto" value="<?php echo $student->Foto->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_Foto" id= "fm_x_Foto" value="<?php echo $student->Foto->UploadMaxFileSize ?>">
</div>
<table id="ft_x_Foto" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $student->Foto->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $student_edit->MultiPages->PageStyle("2") ?>" id="tab_student2">
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_studentedit2" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($student->NIK->Visible) { // NIK ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_NIK" class="form-group">
		<label id="elh_student_NIK" for="x_NIK" class="col-sm-2 control-label ewLabel"><?php echo $student->NIK->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->NIK->CellAttributes() ?>>
<span id="el_student_NIK">
<input type="text" data-table="student" data-field="x_NIK" data-page="2" name="x_NIK" id="x_NIK" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->NIK->getPlaceHolder()) ?>" value="<?php echo $student->NIK->EditValue ?>"<?php echo $student->NIK->EditAttributes() ?>>
</span>
<?php echo $student->NIK->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NIK">
		<td><span id="elh_student_NIK"><?php echo $student->NIK->FldCaption() ?></span></td>
		<td<?php echo $student->NIK->CellAttributes() ?>>
<span id="el_student_NIK">
<input type="text" data-table="student" data-field="x_NIK" data-page="2" name="x_NIK" id="x_NIK" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->NIK->getPlaceHolder()) ?>" value="<?php echo $student->NIK->EditValue ?>"<?php echo $student->NIK->EditAttributes() ?>>
</span>
<?php echo $student->NIK->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->WargaNegara->Visible) { // WargaNegara ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_WargaNegara" class="form-group">
		<label id="elh_student_WargaNegara" class="col-sm-2 control-label ewLabel"><?php echo $student->WargaNegara->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->WargaNegara->CellAttributes() ?>>
<span id="el_student_WargaNegara">
<div id="tp_x_WargaNegara" class="ewTemplate"><input type="radio" data-table="student" data-field="x_WargaNegara" data-page="2" data-value-separator="<?php echo $student->WargaNegara->DisplayValueSeparatorAttribute() ?>" name="x_WargaNegara" id="x_WargaNegara" value="{value}"<?php echo $student->WargaNegara->EditAttributes() ?>></div>
<div id="dsl_x_WargaNegara" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->WargaNegara->RadioButtonListHtml(FALSE, "x_WargaNegara", 2) ?>
</div></div>
</span>
<?php echo $student->WargaNegara->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_WargaNegara">
		<td><span id="elh_student_WargaNegara"><?php echo $student->WargaNegara->FldCaption() ?></span></td>
		<td<?php echo $student->WargaNegara->CellAttributes() ?>>
<span id="el_student_WargaNegara">
<div id="tp_x_WargaNegara" class="ewTemplate"><input type="radio" data-table="student" data-field="x_WargaNegara" data-page="2" data-value-separator="<?php echo $student->WargaNegara->DisplayValueSeparatorAttribute() ?>" name="x_WargaNegara" id="x_WargaNegara" value="{value}"<?php echo $student->WargaNegara->EditAttributes() ?>></div>
<div id="dsl_x_WargaNegara" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->WargaNegara->RadioButtonListHtml(FALSE, "x_WargaNegara", 2) ?>
</div></div>
</span>
<?php echo $student->WargaNegara->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->Kelamin->Visible) { // Kelamin ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_Kelamin" class="form-group">
		<label id="elh_student_Kelamin" class="col-sm-2 control-label ewLabel"><?php echo $student->Kelamin->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->Kelamin->CellAttributes() ?>>
<span id="el_student_Kelamin">
<div id="tp_x_Kelamin" class="ewTemplate"><input type="radio" data-table="student" data-field="x_Kelamin" data-page="2" data-value-separator="<?php echo $student->Kelamin->DisplayValueSeparatorAttribute() ?>" name="x_Kelamin" id="x_Kelamin" value="{value}"<?php echo $student->Kelamin->EditAttributes() ?>></div>
<div id="dsl_x_Kelamin" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->Kelamin->RadioButtonListHtml(FALSE, "x_Kelamin", 2) ?>
</div></div>
<input type="hidden" name="s_x_Kelamin" id="s_x_Kelamin" value="<?php echo $student->Kelamin->LookupFilterQuery() ?>">
</span>
<?php echo $student->Kelamin->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Kelamin">
		<td><span id="elh_student_Kelamin"><?php echo $student->Kelamin->FldCaption() ?></span></td>
		<td<?php echo $student->Kelamin->CellAttributes() ?>>
<span id="el_student_Kelamin">
<div id="tp_x_Kelamin" class="ewTemplate"><input type="radio" data-table="student" data-field="x_Kelamin" data-page="2" data-value-separator="<?php echo $student->Kelamin->DisplayValueSeparatorAttribute() ?>" name="x_Kelamin" id="x_Kelamin" value="{value}"<?php echo $student->Kelamin->EditAttributes() ?>></div>
<div id="dsl_x_Kelamin" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->Kelamin->RadioButtonListHtml(FALSE, "x_Kelamin", 2) ?>
</div></div>
<input type="hidden" name="s_x_Kelamin" id="s_x_Kelamin" value="<?php echo $student->Kelamin->LookupFilterQuery() ?>">
</span>
<?php echo $student->Kelamin->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TempatLahir->Visible) { // TempatLahir ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_TempatLahir" class="form-group">
		<label id="elh_student_TempatLahir" for="x_TempatLahir" class="col-sm-2 control-label ewLabel"><?php echo $student->TempatLahir->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->TempatLahir->CellAttributes() ?>>
<span id="el_student_TempatLahir">
<input type="text" data-table="student" data-field="x_TempatLahir" data-page="2" name="x_TempatLahir" id="x_TempatLahir" size="30" maxlength="25" placeholder="<?php echo ew_HtmlEncode($student->TempatLahir->getPlaceHolder()) ?>" value="<?php echo $student->TempatLahir->EditValue ?>"<?php echo $student->TempatLahir->EditAttributes() ?>>
</span>
<?php echo $student->TempatLahir->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TempatLahir">
		<td><span id="elh_student_TempatLahir"><?php echo $student->TempatLahir->FldCaption() ?></span></td>
		<td<?php echo $student->TempatLahir->CellAttributes() ?>>
<span id="el_student_TempatLahir">
<input type="text" data-table="student" data-field="x_TempatLahir" data-page="2" name="x_TempatLahir" id="x_TempatLahir" size="30" maxlength="25" placeholder="<?php echo ew_HtmlEncode($student->TempatLahir->getPlaceHolder()) ?>" value="<?php echo $student->TempatLahir->EditValue ?>"<?php echo $student->TempatLahir->EditAttributes() ?>>
</span>
<?php echo $student->TempatLahir->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TanggalLahir->Visible) { // TanggalLahir ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_TanggalLahir" class="form-group">
		<label id="elh_student_TanggalLahir" for="x_TanggalLahir" class="col-sm-2 control-label ewLabel"><?php echo $student->TanggalLahir->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->TanggalLahir->CellAttributes() ?>>
<span id="el_student_TanggalLahir">
<input type="text" data-table="student" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($student->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $student->TanggalLahir->EditValue ?>"<?php echo $student->TanggalLahir->EditAttributes() ?>>
</span>
<?php echo $student->TanggalLahir->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanggalLahir">
		<td><span id="elh_student_TanggalLahir"><?php echo $student->TanggalLahir->FldCaption() ?></span></td>
		<td<?php echo $student->TanggalLahir->CellAttributes() ?>>
<span id="el_student_TanggalLahir">
<input type="text" data-table="student" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($student->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $student->TanggalLahir->EditValue ?>"<?php echo $student->TanggalLahir->EditAttributes() ?>>
</span>
<?php echo $student->TanggalLahir->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AgamaID->Visible) { // AgamaID ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_AgamaID" class="form-group">
		<label id="elh_student_AgamaID" for="x_AgamaID" class="col-sm-2 control-label ewLabel"><?php echo $student->AgamaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->AgamaID->CellAttributes() ?>>
<span id="el_student_AgamaID">
<select data-table="student" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $student->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $student->AgamaID->EditAttributes() ?>>
<?php echo $student->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $student->AgamaID->LookupFilterQuery() ?>">
</span>
<?php echo $student->AgamaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_AgamaID">
		<td><span id="elh_student_AgamaID"><?php echo $student->AgamaID->FldCaption() ?></span></td>
		<td<?php echo $student->AgamaID->CellAttributes() ?>>
<span id="el_student_AgamaID">
<select data-table="student" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $student->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $student->AgamaID->EditAttributes() ?>>
<?php echo $student->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $student->AgamaID->LookupFilterQuery() ?>">
</span>
<?php echo $student->AgamaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->Darah->Visible) { // Darah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_Darah" class="form-group">
		<label id="elh_student_Darah" class="col-sm-2 control-label ewLabel"><?php echo $student->Darah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->Darah->CellAttributes() ?>>
<span id="el_student_Darah">
<div id="tp_x_Darah" class="ewTemplate"><input type="radio" data-table="student" data-field="x_Darah" data-page="2" data-value-separator="<?php echo $student->Darah->DisplayValueSeparatorAttribute() ?>" name="x_Darah" id="x_Darah" value="{value}"<?php echo $student->Darah->EditAttributes() ?>></div>
<div id="dsl_x_Darah" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->Darah->RadioButtonListHtml(FALSE, "x_Darah", 2) ?>
</div></div>
<input type="hidden" name="s_x_Darah" id="s_x_Darah" value="<?php echo $student->Darah->LookupFilterQuery() ?>">
</span>
<?php echo $student->Darah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Darah">
		<td><span id="elh_student_Darah"><?php echo $student->Darah->FldCaption() ?></span></td>
		<td<?php echo $student->Darah->CellAttributes() ?>>
<span id="el_student_Darah">
<div id="tp_x_Darah" class="ewTemplate"><input type="radio" data-table="student" data-field="x_Darah" data-page="2" data-value-separator="<?php echo $student->Darah->DisplayValueSeparatorAttribute() ?>" name="x_Darah" id="x_Darah" value="{value}"<?php echo $student->Darah->EditAttributes() ?>></div>
<div id="dsl_x_Darah" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->Darah->RadioButtonListHtml(FALSE, "x_Darah", 2) ?>
</div></div>
<input type="hidden" name="s_x_Darah" id="s_x_Darah" value="<?php echo $student->Darah->LookupFilterQuery() ?>">
</span>
<?php echo $student->Darah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->StatusSipil->Visible) { // StatusSipil ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_StatusSipil" class="form-group">
		<label id="elh_student_StatusSipil" for="x_StatusSipil" class="col-sm-2 control-label ewLabel"><?php echo $student->StatusSipil->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->StatusSipil->CellAttributes() ?>>
<span id="el_student_StatusSipil">
<select data-table="student" data-field="x_StatusSipil" data-page="2" data-value-separator="<?php echo $student->StatusSipil->DisplayValueSeparatorAttribute() ?>" id="x_StatusSipil" name="x_StatusSipil"<?php echo $student->StatusSipil->EditAttributes() ?>>
<?php echo $student->StatusSipil->SelectOptionListHtml("x_StatusSipil") ?>
</select>
<input type="hidden" name="s_x_StatusSipil" id="s_x_StatusSipil" value="<?php echo $student->StatusSipil->LookupFilterQuery() ?>">
</span>
<?php echo $student->StatusSipil->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusSipil">
		<td><span id="elh_student_StatusSipil"><?php echo $student->StatusSipil->FldCaption() ?></span></td>
		<td<?php echo $student->StatusSipil->CellAttributes() ?>>
<span id="el_student_StatusSipil">
<select data-table="student" data-field="x_StatusSipil" data-page="2" data-value-separator="<?php echo $student->StatusSipil->DisplayValueSeparatorAttribute() ?>" id="x_StatusSipil" name="x_StatusSipil"<?php echo $student->StatusSipil->EditAttributes() ?>>
<?php echo $student->StatusSipil->SelectOptionListHtml("x_StatusSipil") ?>
</select>
<input type="hidden" name="s_x_StatusSipil" id="s_x_StatusSipil" value="<?php echo $student->StatusSipil->LookupFilterQuery() ?>">
</span>
<?php echo $student->StatusSipil->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AlamatDomisili->Visible) { // AlamatDomisili ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_AlamatDomisili" class="form-group">
		<label id="elh_student_AlamatDomisili" for="x_AlamatDomisili" class="col-sm-2 control-label ewLabel"><?php echo $student->AlamatDomisili->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->AlamatDomisili->CellAttributes() ?>>
<span id="el_student_AlamatDomisili">
<textarea data-table="student" data-field="x_AlamatDomisili" data-page="2" name="x_AlamatDomisili" id="x_AlamatDomisili" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($student->AlamatDomisili->getPlaceHolder()) ?>"<?php echo $student->AlamatDomisili->EditAttributes() ?>><?php echo $student->AlamatDomisili->EditValue ?></textarea>
</span>
<?php echo $student->AlamatDomisili->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_AlamatDomisili">
		<td><span id="elh_student_AlamatDomisili"><?php echo $student->AlamatDomisili->FldCaption() ?></span></td>
		<td<?php echo $student->AlamatDomisili->CellAttributes() ?>>
<span id="el_student_AlamatDomisili">
<textarea data-table="student" data-field="x_AlamatDomisili" data-page="2" name="x_AlamatDomisili" id="x_AlamatDomisili" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($student->AlamatDomisili->getPlaceHolder()) ?>"<?php echo $student->AlamatDomisili->EditAttributes() ?>><?php echo $student->AlamatDomisili->EditValue ?></textarea>
</span>
<?php echo $student->AlamatDomisili->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->RT->Visible) { // RT ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_RT" class="form-group">
		<label id="elh_student_RT" for="x_RT" class="col-sm-2 control-label ewLabel"><?php echo $student->RT->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->RT->CellAttributes() ?>>
<span id="el_student_RT">
<input type="text" data-table="student" data-field="x_RT" data-page="2" name="x_RT" id="x_RT" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RT->getPlaceHolder()) ?>" value="<?php echo $student->RT->EditValue ?>"<?php echo $student->RT->EditAttributes() ?>>
</span>
<?php echo $student->RT->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_RT">
		<td><span id="elh_student_RT"><?php echo $student->RT->FldCaption() ?></span></td>
		<td<?php echo $student->RT->CellAttributes() ?>>
<span id="el_student_RT">
<input type="text" data-table="student" data-field="x_RT" data-page="2" name="x_RT" id="x_RT" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RT->getPlaceHolder()) ?>" value="<?php echo $student->RT->EditValue ?>"<?php echo $student->RT->EditAttributes() ?>>
</span>
<?php echo $student->RT->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->RW->Visible) { // RW ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_RW" class="form-group">
		<label id="elh_student_RW" for="x_RW" class="col-sm-2 control-label ewLabel"><?php echo $student->RW->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->RW->CellAttributes() ?>>
<span id="el_student_RW">
<input type="text" data-table="student" data-field="x_RW" data-page="2" name="x_RW" id="x_RW" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RW->getPlaceHolder()) ?>" value="<?php echo $student->RW->EditValue ?>"<?php echo $student->RW->EditAttributes() ?>>
</span>
<?php echo $student->RW->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_RW">
		<td><span id="elh_student_RW"><?php echo $student->RW->FldCaption() ?></span></td>
		<td<?php echo $student->RW->CellAttributes() ?>>
<span id="el_student_RW">
<input type="text" data-table="student" data-field="x_RW" data-page="2" name="x_RW" id="x_RW" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RW->getPlaceHolder()) ?>" value="<?php echo $student->RW->EditValue ?>"<?php echo $student->RW->EditAttributes() ?>>
</span>
<?php echo $student->RW->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KodePos->Visible) { // KodePos ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_KodePos" class="form-group">
		<label id="elh_student_KodePos" for="x_KodePos" class="col-sm-2 control-label ewLabel"><?php echo $student->KodePos->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->KodePos->CellAttributes() ?>>
<span id="el_student_KodePos">
<input type="text" data-table="student" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->KodePos->getPlaceHolder()) ?>" value="<?php echo $student->KodePos->EditValue ?>"<?php echo $student->KodePos->EditAttributes() ?>>
</span>
<?php echo $student->KodePos->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodePos">
		<td><span id="elh_student_KodePos"><?php echo $student->KodePos->FldCaption() ?></span></td>
		<td<?php echo $student->KodePos->CellAttributes() ?>>
<span id="el_student_KodePos">
<input type="text" data-table="student" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->KodePos->getPlaceHolder()) ?>" value="<?php echo $student->KodePos->EditValue ?>"<?php echo $student->KodePos->EditAttributes() ?>>
</span>
<?php echo $student->KodePos->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->ProvinsiID->Visible) { // ProvinsiID ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_ProvinsiID" class="form-group">
		<label id="elh_student_ProvinsiID" for="x_ProvinsiID" class="col-sm-2 control-label ewLabel"><?php echo $student->ProvinsiID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->ProvinsiID->CellAttributes() ?>>
<span id="el_student_ProvinsiID">
<?php $student->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $student->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $student->ProvinsiID->EditAttributes() ?>>
<?php echo $student->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $student->ProvinsiID->LookupFilterQuery() ?>">
</span>
<?php echo $student->ProvinsiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiID">
		<td><span id="elh_student_ProvinsiID"><?php echo $student->ProvinsiID->FldCaption() ?></span></td>
		<td<?php echo $student->ProvinsiID->CellAttributes() ?>>
<span id="el_student_ProvinsiID">
<?php $student->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $student->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $student->ProvinsiID->EditAttributes() ?>>
<?php echo $student->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $student->ProvinsiID->LookupFilterQuery() ?>">
</span>
<?php echo $student->ProvinsiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KabupatenKotaID->Visible) { // KabupatenKotaID ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_KabupatenKotaID" class="form-group">
		<label id="elh_student_KabupatenKotaID" for="x_KabupatenKotaID" class="col-sm-2 control-label ewLabel"><?php echo $student->KabupatenKotaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->KabupatenKotaID->CellAttributes() ?>>
<span id="el_student_KabupatenKotaID">
<?php $student->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $student->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $student->KabupatenKotaID->EditAttributes() ?>>
<?php echo $student->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $student->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
<?php echo $student->KabupatenKotaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenKotaID">
		<td><span id="elh_student_KabupatenKotaID"><?php echo $student->KabupatenKotaID->FldCaption() ?></span></td>
		<td<?php echo $student->KabupatenKotaID->CellAttributes() ?>>
<span id="el_student_KabupatenKotaID">
<?php $student->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $student->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $student->KabupatenKotaID->EditAttributes() ?>>
<?php echo $student->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $student->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
<?php echo $student->KabupatenKotaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KecamatanID->Visible) { // KecamatanID ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_KecamatanID" class="form-group">
		<label id="elh_student_KecamatanID" for="x_KecamatanID" class="col-sm-2 control-label ewLabel"><?php echo $student->KecamatanID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->KecamatanID->CellAttributes() ?>>
<span id="el_student_KecamatanID">
<?php $student->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $student->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $student->KecamatanID->EditAttributes() ?>>
<?php echo $student->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $student->KecamatanID->LookupFilterQuery() ?>">
</span>
<?php echo $student->KecamatanID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanID">
		<td><span id="elh_student_KecamatanID"><?php echo $student->KecamatanID->FldCaption() ?></span></td>
		<td<?php echo $student->KecamatanID->CellAttributes() ?>>
<span id="el_student_KecamatanID">
<?php $student->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $student->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $student->KecamatanID->EditAttributes() ?>>
<?php echo $student->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $student->KecamatanID->LookupFilterQuery() ?>">
</span>
<?php echo $student->KecamatanID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->DesaID->Visible) { // DesaID ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_DesaID" class="form-group">
		<label id="elh_student_DesaID" for="x_DesaID" class="col-sm-2 control-label ewLabel"><?php echo $student->DesaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->DesaID->CellAttributes() ?>>
<span id="el_student_DesaID">
<select data-table="student" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $student->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $student->DesaID->EditAttributes() ?>>
<?php echo $student->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $student->DesaID->LookupFilterQuery() ?>">
</span>
<?php echo $student->DesaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_DesaID">
		<td><span id="elh_student_DesaID"><?php echo $student->DesaID->FldCaption() ?></span></td>
		<td<?php echo $student->DesaID->CellAttributes() ?>>
<span id="el_student_DesaID">
<select data-table="student" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $student->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $student->DesaID->EditAttributes() ?>>
<?php echo $student->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $student->DesaID->LookupFilterQuery() ?>">
</span>
<?php echo $student->DesaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AnakKe->Visible) { // AnakKe ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_AnakKe" class="form-group">
		<label id="elh_student_AnakKe" for="x_AnakKe" class="col-sm-2 control-label ewLabel"><?php echo $student->AnakKe->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->AnakKe->CellAttributes() ?>>
<span id="el_student_AnakKe">
<input type="text" data-table="student" data-field="x_AnakKe" data-page="2" name="x_AnakKe" id="x_AnakKe" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($student->AnakKe->getPlaceHolder()) ?>" value="<?php echo $student->AnakKe->EditValue ?>"<?php echo $student->AnakKe->EditAttributes() ?>>
</span>
<?php echo $student->AnakKe->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_AnakKe">
		<td><span id="elh_student_AnakKe"><?php echo $student->AnakKe->FldCaption() ?></span></td>
		<td<?php echo $student->AnakKe->CellAttributes() ?>>
<span id="el_student_AnakKe">
<input type="text" data-table="student" data-field="x_AnakKe" data-page="2" name="x_AnakKe" id="x_AnakKe" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($student->AnakKe->getPlaceHolder()) ?>" value="<?php echo $student->AnakKe->EditValue ?>"<?php echo $student->AnakKe->EditAttributes() ?>>
</span>
<?php echo $student->AnakKe->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->JumlahSaudara->Visible) { // JumlahSaudara ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_JumlahSaudara" class="form-group">
		<label id="elh_student_JumlahSaudara" for="x_JumlahSaudara" class="col-sm-2 control-label ewLabel"><?php echo $student->JumlahSaudara->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->JumlahSaudara->CellAttributes() ?>>
<span id="el_student_JumlahSaudara">
<input type="text" data-table="student" data-field="x_JumlahSaudara" data-page="2" name="x_JumlahSaudara" id="x_JumlahSaudara" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($student->JumlahSaudara->getPlaceHolder()) ?>" value="<?php echo $student->JumlahSaudara->EditValue ?>"<?php echo $student->JumlahSaudara->EditAttributes() ?>>
</span>
<?php echo $student->JumlahSaudara->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_JumlahSaudara">
		<td><span id="elh_student_JumlahSaudara"><?php echo $student->JumlahSaudara->FldCaption() ?></span></td>
		<td<?php echo $student->JumlahSaudara->CellAttributes() ?>>
<span id="el_student_JumlahSaudara">
<input type="text" data-table="student" data-field="x_JumlahSaudara" data-page="2" name="x_JumlahSaudara" id="x_JumlahSaudara" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($student->JumlahSaudara->getPlaceHolder()) ?>" value="<?php echo $student->JumlahSaudara->EditValue ?>"<?php echo $student->JumlahSaudara->EditAttributes() ?>>
</span>
<?php echo $student->JumlahSaudara->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->Telepon->Visible) { // Telepon ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_Telepon" class="form-group">
		<label id="elh_student_Telepon" for="x_Telepon" class="col-sm-2 control-label ewLabel"><?php echo $student->Telepon->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->Telepon->CellAttributes() ?>>
<span id="el_student_Telepon">
<input type="text" data-table="student" data-field="x_Telepon" data-page="2" name="x_Telepon" id="x_Telepon" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Telepon->getPlaceHolder()) ?>" value="<?php echo $student->Telepon->EditValue ?>"<?php echo $student->Telepon->EditAttributes() ?>>
</span>
<?php echo $student->Telepon->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Telepon">
		<td><span id="elh_student_Telepon"><?php echo $student->Telepon->FldCaption() ?></span></td>
		<td<?php echo $student->Telepon->CellAttributes() ?>>
<span id="el_student_Telepon">
<input type="text" data-table="student" data-field="x_Telepon" data-page="2" name="x_Telepon" id="x_Telepon" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Telepon->getPlaceHolder()) ?>" value="<?php echo $student->Telepon->EditValue ?>"<?php echo $student->Telepon->EditAttributes() ?>>
</span>
<?php echo $student->Telepon->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->_Email->Visible) { // Email ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r__Email" class="form-group">
		<label id="elh_student__Email" for="x__Email" class="col-sm-2 control-label ewLabel"><?php echo $student->_Email->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->_Email->CellAttributes() ?>>
<span id="el_student__Email">
<input type="text" data-table="student" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->_Email->getPlaceHolder()) ?>" value="<?php echo $student->_Email->EditValue ?>"<?php echo $student->_Email->EditAttributes() ?>>
</span>
<?php echo $student->_Email->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r__Email">
		<td><span id="elh_student__Email"><?php echo $student->_Email->FldCaption() ?></span></td>
		<td<?php echo $student->_Email->CellAttributes() ?>>
<span id="el_student__Email">
<input type="text" data-table="student" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->_Email->getPlaceHolder()) ?>" value="<?php echo $student->_Email->EditValue ?>"<?php echo $student->_Email->EditAttributes() ?>>
</span>
<?php echo $student->_Email->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $student_edit->MultiPages->PageStyle("3") ?>" id="tab_student3">
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_studentedit3" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($student->NamaAyah->Visible) { // NamaAyah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_NamaAyah" class="form-group">
		<label id="elh_student_NamaAyah" for="x_NamaAyah" class="col-sm-2 control-label ewLabel"><?php echo $student->NamaAyah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->NamaAyah->CellAttributes() ?>>
<span id="el_student_NamaAyah">
<input type="text" data-table="student" data-field="x_NamaAyah" data-page="3" name="x_NamaAyah" id="x_NamaAyah" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->NamaAyah->getPlaceHolder()) ?>" value="<?php echo $student->NamaAyah->EditValue ?>"<?php echo $student->NamaAyah->EditAttributes() ?>>
</span>
<?php echo $student->NamaAyah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NamaAyah">
		<td><span id="elh_student_NamaAyah"><?php echo $student->NamaAyah->FldCaption() ?></span></td>
		<td<?php echo $student->NamaAyah->CellAttributes() ?>>
<span id="el_student_NamaAyah">
<input type="text" data-table="student" data-field="x_NamaAyah" data-page="3" name="x_NamaAyah" id="x_NamaAyah" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->NamaAyah->getPlaceHolder()) ?>" value="<?php echo $student->NamaAyah->EditValue ?>"<?php echo $student->NamaAyah->EditAttributes() ?>>
</span>
<?php echo $student->NamaAyah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AgamaAyah->Visible) { // AgamaAyah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_AgamaAyah" class="form-group">
		<label id="elh_student_AgamaAyah" for="x_AgamaAyah" class="col-sm-2 control-label ewLabel"><?php echo $student->AgamaAyah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->AgamaAyah->CellAttributes() ?>>
<span id="el_student_AgamaAyah">
<select data-table="student" data-field="x_AgamaAyah" data-page="3" data-value-separator="<?php echo $student->AgamaAyah->DisplayValueSeparatorAttribute() ?>" id="x_AgamaAyah" name="x_AgamaAyah"<?php echo $student->AgamaAyah->EditAttributes() ?>>
<?php echo $student->AgamaAyah->SelectOptionListHtml("x_AgamaAyah") ?>
</select>
<input type="hidden" name="s_x_AgamaAyah" id="s_x_AgamaAyah" value="<?php echo $student->AgamaAyah->LookupFilterQuery() ?>">
</span>
<?php echo $student->AgamaAyah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_AgamaAyah">
		<td><span id="elh_student_AgamaAyah"><?php echo $student->AgamaAyah->FldCaption() ?></span></td>
		<td<?php echo $student->AgamaAyah->CellAttributes() ?>>
<span id="el_student_AgamaAyah">
<select data-table="student" data-field="x_AgamaAyah" data-page="3" data-value-separator="<?php echo $student->AgamaAyah->DisplayValueSeparatorAttribute() ?>" id="x_AgamaAyah" name="x_AgamaAyah"<?php echo $student->AgamaAyah->EditAttributes() ?>>
<?php echo $student->AgamaAyah->SelectOptionListHtml("x_AgamaAyah") ?>
</select>
<input type="hidden" name="s_x_AgamaAyah" id="s_x_AgamaAyah" value="<?php echo $student->AgamaAyah->LookupFilterQuery() ?>">
</span>
<?php echo $student->AgamaAyah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->PendidikanAyah->Visible) { // PendidikanAyah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_PendidikanAyah" class="form-group">
		<label id="elh_student_PendidikanAyah" for="x_PendidikanAyah" class="col-sm-2 control-label ewLabel"><?php echo $student->PendidikanAyah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->PendidikanAyah->CellAttributes() ?>>
<span id="el_student_PendidikanAyah">
<select data-table="student" data-field="x_PendidikanAyah" data-page="3" data-value-separator="<?php echo $student->PendidikanAyah->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanAyah" name="x_PendidikanAyah"<?php echo $student->PendidikanAyah->EditAttributes() ?>>
<?php echo $student->PendidikanAyah->SelectOptionListHtml("x_PendidikanAyah") ?>
</select>
<input type="hidden" name="s_x_PendidikanAyah" id="s_x_PendidikanAyah" value="<?php echo $student->PendidikanAyah->LookupFilterQuery() ?>">
</span>
<?php echo $student->PendidikanAyah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_PendidikanAyah">
		<td><span id="elh_student_PendidikanAyah"><?php echo $student->PendidikanAyah->FldCaption() ?></span></td>
		<td<?php echo $student->PendidikanAyah->CellAttributes() ?>>
<span id="el_student_PendidikanAyah">
<select data-table="student" data-field="x_PendidikanAyah" data-page="3" data-value-separator="<?php echo $student->PendidikanAyah->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanAyah" name="x_PendidikanAyah"<?php echo $student->PendidikanAyah->EditAttributes() ?>>
<?php echo $student->PendidikanAyah->SelectOptionListHtml("x_PendidikanAyah") ?>
</select>
<input type="hidden" name="s_x_PendidikanAyah" id="s_x_PendidikanAyah" value="<?php echo $student->PendidikanAyah->LookupFilterQuery() ?>">
</span>
<?php echo $student->PendidikanAyah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->PekerjaanAyah->Visible) { // PekerjaanAyah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_PekerjaanAyah" class="form-group">
		<label id="elh_student_PekerjaanAyah" for="x_PekerjaanAyah" class="col-sm-2 control-label ewLabel"><?php echo $student->PekerjaanAyah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->PekerjaanAyah->CellAttributes() ?>>
<span id="el_student_PekerjaanAyah">
<select data-table="student" data-field="x_PekerjaanAyah" data-page="3" data-value-separator="<?php echo $student->PekerjaanAyah->DisplayValueSeparatorAttribute() ?>" id="x_PekerjaanAyah" name="x_PekerjaanAyah"<?php echo $student->PekerjaanAyah->EditAttributes() ?>>
<?php echo $student->PekerjaanAyah->SelectOptionListHtml("x_PekerjaanAyah") ?>
</select>
<input type="hidden" name="s_x_PekerjaanAyah" id="s_x_PekerjaanAyah" value="<?php echo $student->PekerjaanAyah->LookupFilterQuery() ?>">
</span>
<?php echo $student->PekerjaanAyah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_PekerjaanAyah">
		<td><span id="elh_student_PekerjaanAyah"><?php echo $student->PekerjaanAyah->FldCaption() ?></span></td>
		<td<?php echo $student->PekerjaanAyah->CellAttributes() ?>>
<span id="el_student_PekerjaanAyah">
<select data-table="student" data-field="x_PekerjaanAyah" data-page="3" data-value-separator="<?php echo $student->PekerjaanAyah->DisplayValueSeparatorAttribute() ?>" id="x_PekerjaanAyah" name="x_PekerjaanAyah"<?php echo $student->PekerjaanAyah->EditAttributes() ?>>
<?php echo $student->PekerjaanAyah->SelectOptionListHtml("x_PekerjaanAyah") ?>
</select>
<input type="hidden" name="s_x_PekerjaanAyah" id="s_x_PekerjaanAyah" value="<?php echo $student->PekerjaanAyah->LookupFilterQuery() ?>">
</span>
<?php echo $student->PekerjaanAyah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->HidupAyah->Visible) { // HidupAyah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_HidupAyah" class="form-group">
		<label id="elh_student_HidupAyah" for="x_HidupAyah" class="col-sm-2 control-label ewLabel"><?php echo $student->HidupAyah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->HidupAyah->CellAttributes() ?>>
<span id="el_student_HidupAyah">
<select data-table="student" data-field="x_HidupAyah" data-page="3" data-value-separator="<?php echo $student->HidupAyah->DisplayValueSeparatorAttribute() ?>" id="x_HidupAyah" name="x_HidupAyah"<?php echo $student->HidupAyah->EditAttributes() ?>>
<?php echo $student->HidupAyah->SelectOptionListHtml("x_HidupAyah") ?>
</select>
<input type="hidden" name="s_x_HidupAyah" id="s_x_HidupAyah" value="<?php echo $student->HidupAyah->LookupFilterQuery() ?>">
</span>
<?php echo $student->HidupAyah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_HidupAyah">
		<td><span id="elh_student_HidupAyah"><?php echo $student->HidupAyah->FldCaption() ?></span></td>
		<td<?php echo $student->HidupAyah->CellAttributes() ?>>
<span id="el_student_HidupAyah">
<select data-table="student" data-field="x_HidupAyah" data-page="3" data-value-separator="<?php echo $student->HidupAyah->DisplayValueSeparatorAttribute() ?>" id="x_HidupAyah" name="x_HidupAyah"<?php echo $student->HidupAyah->EditAttributes() ?>>
<?php echo $student->HidupAyah->SelectOptionListHtml("x_HidupAyah") ?>
</select>
<input type="hidden" name="s_x_HidupAyah" id="s_x_HidupAyah" value="<?php echo $student->HidupAyah->LookupFilterQuery() ?>">
</span>
<?php echo $student->HidupAyah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->NamaIbu->Visible) { // NamaIbu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_NamaIbu" class="form-group">
		<label id="elh_student_NamaIbu" for="x_NamaIbu" class="col-sm-2 control-label ewLabel"><?php echo $student->NamaIbu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->NamaIbu->CellAttributes() ?>>
<span id="el_student_NamaIbu">
<input type="text" data-table="student" data-field="x_NamaIbu" data-page="3" name="x_NamaIbu" id="x_NamaIbu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->NamaIbu->getPlaceHolder()) ?>" value="<?php echo $student->NamaIbu->EditValue ?>"<?php echo $student->NamaIbu->EditAttributes() ?>>
</span>
<?php echo $student->NamaIbu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NamaIbu">
		<td><span id="elh_student_NamaIbu"><?php echo $student->NamaIbu->FldCaption() ?></span></td>
		<td<?php echo $student->NamaIbu->CellAttributes() ?>>
<span id="el_student_NamaIbu">
<input type="text" data-table="student" data-field="x_NamaIbu" data-page="3" name="x_NamaIbu" id="x_NamaIbu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->NamaIbu->getPlaceHolder()) ?>" value="<?php echo $student->NamaIbu->EditValue ?>"<?php echo $student->NamaIbu->EditAttributes() ?>>
</span>
<?php echo $student->NamaIbu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AgamaIbu->Visible) { // AgamaIbu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_AgamaIbu" class="form-group">
		<label id="elh_student_AgamaIbu" for="x_AgamaIbu" class="col-sm-2 control-label ewLabel"><?php echo $student->AgamaIbu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->AgamaIbu->CellAttributes() ?>>
<span id="el_student_AgamaIbu">
<select data-table="student" data-field="x_AgamaIbu" data-page="3" data-value-separator="<?php echo $student->AgamaIbu->DisplayValueSeparatorAttribute() ?>" id="x_AgamaIbu" name="x_AgamaIbu"<?php echo $student->AgamaIbu->EditAttributes() ?>>
<?php echo $student->AgamaIbu->SelectOptionListHtml("x_AgamaIbu") ?>
</select>
<input type="hidden" name="s_x_AgamaIbu" id="s_x_AgamaIbu" value="<?php echo $student->AgamaIbu->LookupFilterQuery() ?>">
</span>
<?php echo $student->AgamaIbu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_AgamaIbu">
		<td><span id="elh_student_AgamaIbu"><?php echo $student->AgamaIbu->FldCaption() ?></span></td>
		<td<?php echo $student->AgamaIbu->CellAttributes() ?>>
<span id="el_student_AgamaIbu">
<select data-table="student" data-field="x_AgamaIbu" data-page="3" data-value-separator="<?php echo $student->AgamaIbu->DisplayValueSeparatorAttribute() ?>" id="x_AgamaIbu" name="x_AgamaIbu"<?php echo $student->AgamaIbu->EditAttributes() ?>>
<?php echo $student->AgamaIbu->SelectOptionListHtml("x_AgamaIbu") ?>
</select>
<input type="hidden" name="s_x_AgamaIbu" id="s_x_AgamaIbu" value="<?php echo $student->AgamaIbu->LookupFilterQuery() ?>">
</span>
<?php echo $student->AgamaIbu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->PendidikanIbu->Visible) { // PendidikanIbu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_PendidikanIbu" class="form-group">
		<label id="elh_student_PendidikanIbu" for="x_PendidikanIbu" class="col-sm-2 control-label ewLabel"><?php echo $student->PendidikanIbu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->PendidikanIbu->CellAttributes() ?>>
<span id="el_student_PendidikanIbu">
<select data-table="student" data-field="x_PendidikanIbu" data-page="3" data-value-separator="<?php echo $student->PendidikanIbu->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanIbu" name="x_PendidikanIbu"<?php echo $student->PendidikanIbu->EditAttributes() ?>>
<?php echo $student->PendidikanIbu->SelectOptionListHtml("x_PendidikanIbu") ?>
</select>
<input type="hidden" name="s_x_PendidikanIbu" id="s_x_PendidikanIbu" value="<?php echo $student->PendidikanIbu->LookupFilterQuery() ?>">
</span>
<?php echo $student->PendidikanIbu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_PendidikanIbu">
		<td><span id="elh_student_PendidikanIbu"><?php echo $student->PendidikanIbu->FldCaption() ?></span></td>
		<td<?php echo $student->PendidikanIbu->CellAttributes() ?>>
<span id="el_student_PendidikanIbu">
<select data-table="student" data-field="x_PendidikanIbu" data-page="3" data-value-separator="<?php echo $student->PendidikanIbu->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanIbu" name="x_PendidikanIbu"<?php echo $student->PendidikanIbu->EditAttributes() ?>>
<?php echo $student->PendidikanIbu->SelectOptionListHtml("x_PendidikanIbu") ?>
</select>
<input type="hidden" name="s_x_PendidikanIbu" id="s_x_PendidikanIbu" value="<?php echo $student->PendidikanIbu->LookupFilterQuery() ?>">
</span>
<?php echo $student->PendidikanIbu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->PekerjaanIbu->Visible) { // PekerjaanIbu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_PekerjaanIbu" class="form-group">
		<label id="elh_student_PekerjaanIbu" for="x_PekerjaanIbu" class="col-sm-2 control-label ewLabel"><?php echo $student->PekerjaanIbu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->PekerjaanIbu->CellAttributes() ?>>
<span id="el_student_PekerjaanIbu">
<select data-table="student" data-field="x_PekerjaanIbu" data-page="3" data-value-separator="<?php echo $student->PekerjaanIbu->DisplayValueSeparatorAttribute() ?>" id="x_PekerjaanIbu" name="x_PekerjaanIbu"<?php echo $student->PekerjaanIbu->EditAttributes() ?>>
<?php echo $student->PekerjaanIbu->SelectOptionListHtml("x_PekerjaanIbu") ?>
</select>
<input type="hidden" name="s_x_PekerjaanIbu" id="s_x_PekerjaanIbu" value="<?php echo $student->PekerjaanIbu->LookupFilterQuery() ?>">
</span>
<?php echo $student->PekerjaanIbu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_PekerjaanIbu">
		<td><span id="elh_student_PekerjaanIbu"><?php echo $student->PekerjaanIbu->FldCaption() ?></span></td>
		<td<?php echo $student->PekerjaanIbu->CellAttributes() ?>>
<span id="el_student_PekerjaanIbu">
<select data-table="student" data-field="x_PekerjaanIbu" data-page="3" data-value-separator="<?php echo $student->PekerjaanIbu->DisplayValueSeparatorAttribute() ?>" id="x_PekerjaanIbu" name="x_PekerjaanIbu"<?php echo $student->PekerjaanIbu->EditAttributes() ?>>
<?php echo $student->PekerjaanIbu->SelectOptionListHtml("x_PekerjaanIbu") ?>
</select>
<input type="hidden" name="s_x_PekerjaanIbu" id="s_x_PekerjaanIbu" value="<?php echo $student->PekerjaanIbu->LookupFilterQuery() ?>">
</span>
<?php echo $student->PekerjaanIbu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->HidupIbu->Visible) { // HidupIbu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_HidupIbu" class="form-group">
		<label id="elh_student_HidupIbu" for="x_HidupIbu" class="col-sm-2 control-label ewLabel"><?php echo $student->HidupIbu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->HidupIbu->CellAttributes() ?>>
<span id="el_student_HidupIbu">
<select data-table="student" data-field="x_HidupIbu" data-page="3" data-value-separator="<?php echo $student->HidupIbu->DisplayValueSeparatorAttribute() ?>" id="x_HidupIbu" name="x_HidupIbu"<?php echo $student->HidupIbu->EditAttributes() ?>>
<?php echo $student->HidupIbu->SelectOptionListHtml("x_HidupIbu") ?>
</select>
<input type="hidden" name="s_x_HidupIbu" id="s_x_HidupIbu" value="<?php echo $student->HidupIbu->LookupFilterQuery() ?>">
</span>
<?php echo $student->HidupIbu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_HidupIbu">
		<td><span id="elh_student_HidupIbu"><?php echo $student->HidupIbu->FldCaption() ?></span></td>
		<td<?php echo $student->HidupIbu->CellAttributes() ?>>
<span id="el_student_HidupIbu">
<select data-table="student" data-field="x_HidupIbu" data-page="3" data-value-separator="<?php echo $student->HidupIbu->DisplayValueSeparatorAttribute() ?>" id="x_HidupIbu" name="x_HidupIbu"<?php echo $student->HidupIbu->EditAttributes() ?>>
<?php echo $student->HidupIbu->SelectOptionListHtml("x_HidupIbu") ?>
</select>
<input type="hidden" name="s_x_HidupIbu" id="s_x_HidupIbu" value="<?php echo $student->HidupIbu->LookupFilterQuery() ?>">
</span>
<?php echo $student->HidupIbu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AlamatOrtu->Visible) { // AlamatOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_AlamatOrtu" class="form-group">
		<label id="elh_student_AlamatOrtu" for="x_AlamatOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->AlamatOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->AlamatOrtu->CellAttributes() ?>>
<span id="el_student_AlamatOrtu">
<textarea data-table="student" data-field="x_AlamatOrtu" data-page="3" name="x_AlamatOrtu" id="x_AlamatOrtu" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($student->AlamatOrtu->getPlaceHolder()) ?>"<?php echo $student->AlamatOrtu->EditAttributes() ?>><?php echo $student->AlamatOrtu->EditValue ?></textarea>
</span>
<?php echo $student->AlamatOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_AlamatOrtu">
		<td><span id="elh_student_AlamatOrtu"><?php echo $student->AlamatOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->AlamatOrtu->CellAttributes() ?>>
<span id="el_student_AlamatOrtu">
<textarea data-table="student" data-field="x_AlamatOrtu" data-page="3" name="x_AlamatOrtu" id="x_AlamatOrtu" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($student->AlamatOrtu->getPlaceHolder()) ?>"<?php echo $student->AlamatOrtu->EditAttributes() ?>><?php echo $student->AlamatOrtu->EditValue ?></textarea>
</span>
<?php echo $student->AlamatOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->RTOrtu->Visible) { // RTOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_RTOrtu" class="form-group">
		<label id="elh_student_RTOrtu" for="x_RTOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->RTOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->RTOrtu->CellAttributes() ?>>
<span id="el_student_RTOrtu">
<input type="text" data-table="student" data-field="x_RTOrtu" data-page="3" name="x_RTOrtu" id="x_RTOrtu" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RTOrtu->getPlaceHolder()) ?>" value="<?php echo $student->RTOrtu->EditValue ?>"<?php echo $student->RTOrtu->EditAttributes() ?>>
</span>
<?php echo $student->RTOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_RTOrtu">
		<td><span id="elh_student_RTOrtu"><?php echo $student->RTOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->RTOrtu->CellAttributes() ?>>
<span id="el_student_RTOrtu">
<input type="text" data-table="student" data-field="x_RTOrtu" data-page="3" name="x_RTOrtu" id="x_RTOrtu" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RTOrtu->getPlaceHolder()) ?>" value="<?php echo $student->RTOrtu->EditValue ?>"<?php echo $student->RTOrtu->EditAttributes() ?>>
</span>
<?php echo $student->RTOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->RWOrtu->Visible) { // RWOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_RWOrtu" class="form-group">
		<label id="elh_student_RWOrtu" for="x_RWOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->RWOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->RWOrtu->CellAttributes() ?>>
<span id="el_student_RWOrtu">
<input type="text" data-table="student" data-field="x_RWOrtu" data-page="3" name="x_RWOrtu" id="x_RWOrtu" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RWOrtu->getPlaceHolder()) ?>" value="<?php echo $student->RWOrtu->EditValue ?>"<?php echo $student->RWOrtu->EditAttributes() ?>>
</span>
<?php echo $student->RWOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_RWOrtu">
		<td><span id="elh_student_RWOrtu"><?php echo $student->RWOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->RWOrtu->CellAttributes() ?>>
<span id="el_student_RWOrtu">
<input type="text" data-table="student" data-field="x_RWOrtu" data-page="3" name="x_RWOrtu" id="x_RWOrtu" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RWOrtu->getPlaceHolder()) ?>" value="<?php echo $student->RWOrtu->EditValue ?>"<?php echo $student->RWOrtu->EditAttributes() ?>>
</span>
<?php echo $student->RWOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KodePosOrtu->Visible) { // KodePosOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_KodePosOrtu" class="form-group">
		<label id="elh_student_KodePosOrtu" for="x_KodePosOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->KodePosOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->KodePosOrtu->CellAttributes() ?>>
<span id="el_student_KodePosOrtu">
<input type="text" data-table="student" data-field="x_KodePosOrtu" data-page="3" name="x_KodePosOrtu" id="x_KodePosOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->KodePosOrtu->getPlaceHolder()) ?>" value="<?php echo $student->KodePosOrtu->EditValue ?>"<?php echo $student->KodePosOrtu->EditAttributes() ?>>
</span>
<?php echo $student->KodePosOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodePosOrtu">
		<td><span id="elh_student_KodePosOrtu"><?php echo $student->KodePosOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->KodePosOrtu->CellAttributes() ?>>
<span id="el_student_KodePosOrtu">
<input type="text" data-table="student" data-field="x_KodePosOrtu" data-page="3" name="x_KodePosOrtu" id="x_KodePosOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->KodePosOrtu->getPlaceHolder()) ?>" value="<?php echo $student->KodePosOrtu->EditValue ?>"<?php echo $student->KodePosOrtu->EditAttributes() ?>>
</span>
<?php echo $student->KodePosOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->ProvinsiIDOrtu->Visible) { // ProvinsiIDOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_ProvinsiIDOrtu" class="form-group">
		<label id="elh_student_ProvinsiIDOrtu" for="x_ProvinsiIDOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->ProvinsiIDOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->ProvinsiIDOrtu->CellAttributes() ?>>
<span id="el_student_ProvinsiIDOrtu">
<?php $student->ProvinsiIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiIDOrtu" data-page="3" data-value-separator="<?php echo $student->ProvinsiIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiIDOrtu" name="x_ProvinsiIDOrtu"<?php echo $student->ProvinsiIDOrtu->EditAttributes() ?>>
<?php echo $student->ProvinsiIDOrtu->SelectOptionListHtml("x_ProvinsiIDOrtu") ?>
</select>
<input type="hidden" name="s_x_ProvinsiIDOrtu" id="s_x_ProvinsiIDOrtu" value="<?php echo $student->ProvinsiIDOrtu->LookupFilterQuery() ?>">
</span>
<?php echo $student->ProvinsiIDOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiIDOrtu">
		<td><span id="elh_student_ProvinsiIDOrtu"><?php echo $student->ProvinsiIDOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->ProvinsiIDOrtu->CellAttributes() ?>>
<span id="el_student_ProvinsiIDOrtu">
<?php $student->ProvinsiIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiIDOrtu" data-page="3" data-value-separator="<?php echo $student->ProvinsiIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiIDOrtu" name="x_ProvinsiIDOrtu"<?php echo $student->ProvinsiIDOrtu->EditAttributes() ?>>
<?php echo $student->ProvinsiIDOrtu->SelectOptionListHtml("x_ProvinsiIDOrtu") ?>
</select>
<input type="hidden" name="s_x_ProvinsiIDOrtu" id="s_x_ProvinsiIDOrtu" value="<?php echo $student->ProvinsiIDOrtu->LookupFilterQuery() ?>">
</span>
<?php echo $student->ProvinsiIDOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KabupatenIDOrtu->Visible) { // KabupatenIDOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_KabupatenIDOrtu" class="form-group">
		<label id="elh_student_KabupatenIDOrtu" for="x_KabupatenIDOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->KabupatenIDOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->KabupatenIDOrtu->CellAttributes() ?>>
<span id="el_student_KabupatenIDOrtu">
<?php $student->KabupatenIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenIDOrtu" data-page="3" data-value-separator="<?php echo $student->KabupatenIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenIDOrtu" name="x_KabupatenIDOrtu"<?php echo $student->KabupatenIDOrtu->EditAttributes() ?>>
<?php echo $student->KabupatenIDOrtu->SelectOptionListHtml("x_KabupatenIDOrtu") ?>
</select>
<input type="hidden" name="s_x_KabupatenIDOrtu" id="s_x_KabupatenIDOrtu" value="<?php echo $student->KabupatenIDOrtu->LookupFilterQuery() ?>">
</span>
<?php echo $student->KabupatenIDOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenIDOrtu">
		<td><span id="elh_student_KabupatenIDOrtu"><?php echo $student->KabupatenIDOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->KabupatenIDOrtu->CellAttributes() ?>>
<span id="el_student_KabupatenIDOrtu">
<?php $student->KabupatenIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenIDOrtu" data-page="3" data-value-separator="<?php echo $student->KabupatenIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenIDOrtu" name="x_KabupatenIDOrtu"<?php echo $student->KabupatenIDOrtu->EditAttributes() ?>>
<?php echo $student->KabupatenIDOrtu->SelectOptionListHtml("x_KabupatenIDOrtu") ?>
</select>
<input type="hidden" name="s_x_KabupatenIDOrtu" id="s_x_KabupatenIDOrtu" value="<?php echo $student->KabupatenIDOrtu->LookupFilterQuery() ?>">
</span>
<?php echo $student->KabupatenIDOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KecamatanIDOrtu->Visible) { // KecamatanIDOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_KecamatanIDOrtu" class="form-group">
		<label id="elh_student_KecamatanIDOrtu" for="x_KecamatanIDOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->KecamatanIDOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->KecamatanIDOrtu->CellAttributes() ?>>
<span id="el_student_KecamatanIDOrtu">
<?php $student->KecamatanIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanIDOrtu" data-page="3" data-value-separator="<?php echo $student->KecamatanIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanIDOrtu" name="x_KecamatanIDOrtu"<?php echo $student->KecamatanIDOrtu->EditAttributes() ?>>
<?php echo $student->KecamatanIDOrtu->SelectOptionListHtml("x_KecamatanIDOrtu") ?>
</select>
<input type="hidden" name="s_x_KecamatanIDOrtu" id="s_x_KecamatanIDOrtu" value="<?php echo $student->KecamatanIDOrtu->LookupFilterQuery() ?>">
</span>
<?php echo $student->KecamatanIDOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanIDOrtu">
		<td><span id="elh_student_KecamatanIDOrtu"><?php echo $student->KecamatanIDOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->KecamatanIDOrtu->CellAttributes() ?>>
<span id="el_student_KecamatanIDOrtu">
<?php $student->KecamatanIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanIDOrtu" data-page="3" data-value-separator="<?php echo $student->KecamatanIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanIDOrtu" name="x_KecamatanIDOrtu"<?php echo $student->KecamatanIDOrtu->EditAttributes() ?>>
<?php echo $student->KecamatanIDOrtu->SelectOptionListHtml("x_KecamatanIDOrtu") ?>
</select>
<input type="hidden" name="s_x_KecamatanIDOrtu" id="s_x_KecamatanIDOrtu" value="<?php echo $student->KecamatanIDOrtu->LookupFilterQuery() ?>">
</span>
<?php echo $student->KecamatanIDOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->DesaIDOrtu->Visible) { // DesaIDOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_DesaIDOrtu" class="form-group">
		<label id="elh_student_DesaIDOrtu" for="x_DesaIDOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->DesaIDOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->DesaIDOrtu->CellAttributes() ?>>
<span id="el_student_DesaIDOrtu">
<select data-table="student" data-field="x_DesaIDOrtu" data-page="3" data-value-separator="<?php echo $student->DesaIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_DesaIDOrtu" name="x_DesaIDOrtu"<?php echo $student->DesaIDOrtu->EditAttributes() ?>>
<?php echo $student->DesaIDOrtu->SelectOptionListHtml("x_DesaIDOrtu") ?>
</select>
<input type="hidden" name="s_x_DesaIDOrtu" id="s_x_DesaIDOrtu" value="<?php echo $student->DesaIDOrtu->LookupFilterQuery() ?>">
</span>
<?php echo $student->DesaIDOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_DesaIDOrtu">
		<td><span id="elh_student_DesaIDOrtu"><?php echo $student->DesaIDOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->DesaIDOrtu->CellAttributes() ?>>
<span id="el_student_DesaIDOrtu">
<select data-table="student" data-field="x_DesaIDOrtu" data-page="3" data-value-separator="<?php echo $student->DesaIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_DesaIDOrtu" name="x_DesaIDOrtu"<?php echo $student->DesaIDOrtu->EditAttributes() ?>>
<?php echo $student->DesaIDOrtu->SelectOptionListHtml("x_DesaIDOrtu") ?>
</select>
<input type="hidden" name="s_x_DesaIDOrtu" id="s_x_DesaIDOrtu" value="<?php echo $student->DesaIDOrtu->LookupFilterQuery() ?>">
</span>
<?php echo $student->DesaIDOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->NegaraIDOrtu->Visible) { // NegaraIDOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_NegaraIDOrtu" class="form-group">
		<label id="elh_student_NegaraIDOrtu" for="x_NegaraIDOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->NegaraIDOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->NegaraIDOrtu->CellAttributes() ?>>
<span id="el_student_NegaraIDOrtu">
<select data-table="student" data-field="x_NegaraIDOrtu" data-page="3" data-value-separator="<?php echo $student->NegaraIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_NegaraIDOrtu" name="x_NegaraIDOrtu"<?php echo $student->NegaraIDOrtu->EditAttributes() ?>>
<?php echo $student->NegaraIDOrtu->SelectOptionListHtml("x_NegaraIDOrtu") ?>
</select>
<input type="hidden" name="s_x_NegaraIDOrtu" id="s_x_NegaraIDOrtu" value="<?php echo $student->NegaraIDOrtu->LookupFilterQuery() ?>">
</span>
<?php echo $student->NegaraIDOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NegaraIDOrtu">
		<td><span id="elh_student_NegaraIDOrtu"><?php echo $student->NegaraIDOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->NegaraIDOrtu->CellAttributes() ?>>
<span id="el_student_NegaraIDOrtu">
<select data-table="student" data-field="x_NegaraIDOrtu" data-page="3" data-value-separator="<?php echo $student->NegaraIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_NegaraIDOrtu" name="x_NegaraIDOrtu"<?php echo $student->NegaraIDOrtu->EditAttributes() ?>>
<?php echo $student->NegaraIDOrtu->SelectOptionListHtml("x_NegaraIDOrtu") ?>
</select>
<input type="hidden" name="s_x_NegaraIDOrtu" id="s_x_NegaraIDOrtu" value="<?php echo $student->NegaraIDOrtu->LookupFilterQuery() ?>">
</span>
<?php echo $student->NegaraIDOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TeleponOrtu->Visible) { // TeleponOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_TeleponOrtu" class="form-group">
		<label id="elh_student_TeleponOrtu" for="x_TeleponOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->TeleponOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->TeleponOrtu->CellAttributes() ?>>
<span id="el_student_TeleponOrtu">
<input type="text" data-table="student" data-field="x_TeleponOrtu" data-page="3" name="x_TeleponOrtu" id="x_TeleponOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->TeleponOrtu->getPlaceHolder()) ?>" value="<?php echo $student->TeleponOrtu->EditValue ?>"<?php echo $student->TeleponOrtu->EditAttributes() ?>>
</span>
<?php echo $student->TeleponOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TeleponOrtu">
		<td><span id="elh_student_TeleponOrtu"><?php echo $student->TeleponOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->TeleponOrtu->CellAttributes() ?>>
<span id="el_student_TeleponOrtu">
<input type="text" data-table="student" data-field="x_TeleponOrtu" data-page="3" name="x_TeleponOrtu" id="x_TeleponOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->TeleponOrtu->getPlaceHolder()) ?>" value="<?php echo $student->TeleponOrtu->EditValue ?>"<?php echo $student->TeleponOrtu->EditAttributes() ?>>
</span>
<?php echo $student->TeleponOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->HandphoneOrtu->Visible) { // HandphoneOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_HandphoneOrtu" class="form-group">
		<label id="elh_student_HandphoneOrtu" for="x_HandphoneOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->HandphoneOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->HandphoneOrtu->CellAttributes() ?>>
<span id="el_student_HandphoneOrtu">
<input type="text" data-table="student" data-field="x_HandphoneOrtu" data-page="3" name="x_HandphoneOrtu" id="x_HandphoneOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->HandphoneOrtu->getPlaceHolder()) ?>" value="<?php echo $student->HandphoneOrtu->EditValue ?>"<?php echo $student->HandphoneOrtu->EditAttributes() ?>>
</span>
<?php echo $student->HandphoneOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_HandphoneOrtu">
		<td><span id="elh_student_HandphoneOrtu"><?php echo $student->HandphoneOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->HandphoneOrtu->CellAttributes() ?>>
<span id="el_student_HandphoneOrtu">
<input type="text" data-table="student" data-field="x_HandphoneOrtu" data-page="3" name="x_HandphoneOrtu" id="x_HandphoneOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->HandphoneOrtu->getPlaceHolder()) ?>" value="<?php echo $student->HandphoneOrtu->EditValue ?>"<?php echo $student->HandphoneOrtu->EditAttributes() ?>>
</span>
<?php echo $student->HandphoneOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->EmailOrtu->Visible) { // EmailOrtu ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_EmailOrtu" class="form-group">
		<label id="elh_student_EmailOrtu" for="x_EmailOrtu" class="col-sm-2 control-label ewLabel"><?php echo $student->EmailOrtu->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->EmailOrtu->CellAttributes() ?>>
<span id="el_student_EmailOrtu">
<input type="text" data-table="student" data-field="x_EmailOrtu" data-page="3" name="x_EmailOrtu" id="x_EmailOrtu" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($student->EmailOrtu->getPlaceHolder()) ?>" value="<?php echo $student->EmailOrtu->EditValue ?>"<?php echo $student->EmailOrtu->EditAttributes() ?>>
</span>
<?php echo $student->EmailOrtu->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_EmailOrtu">
		<td><span id="elh_student_EmailOrtu"><?php echo $student->EmailOrtu->FldCaption() ?></span></td>
		<td<?php echo $student->EmailOrtu->CellAttributes() ?>>
<span id="el_student_EmailOrtu">
<input type="text" data-table="student" data-field="x_EmailOrtu" data-page="3" name="x_EmailOrtu" id="x_EmailOrtu" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($student->EmailOrtu->getPlaceHolder()) ?>" value="<?php echo $student->EmailOrtu->EditValue ?>"<?php echo $student->EmailOrtu->EditAttributes() ?>>
</span>
<?php echo $student->EmailOrtu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $student_edit->MultiPages->PageStyle("4") ?>" id="tab_student4">
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_studentedit4" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($student->AsalSekolah->Visible) { // AsalSekolah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_AsalSekolah" class="form-group">
		<label id="elh_student_AsalSekolah" for="x_AsalSekolah" class="col-sm-2 control-label ewLabel"><?php echo $student->AsalSekolah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->AsalSekolah->CellAttributes() ?>>
<span id="el_student_AsalSekolah">
<input type="text" data-table="student" data-field="x_AsalSekolah" data-page="4" name="x_AsalSekolah" id="x_AsalSekolah" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($student->AsalSekolah->getPlaceHolder()) ?>" value="<?php echo $student->AsalSekolah->EditValue ?>"<?php echo $student->AsalSekolah->EditAttributes() ?>>
</span>
<?php echo $student->AsalSekolah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_AsalSekolah">
		<td><span id="elh_student_AsalSekolah"><?php echo $student->AsalSekolah->FldCaption() ?></span></td>
		<td<?php echo $student->AsalSekolah->CellAttributes() ?>>
<span id="el_student_AsalSekolah">
<input type="text" data-table="student" data-field="x_AsalSekolah" data-page="4" name="x_AsalSekolah" id="x_AsalSekolah" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($student->AsalSekolah->getPlaceHolder()) ?>" value="<?php echo $student->AsalSekolah->EditValue ?>"<?php echo $student->AsalSekolah->EditAttributes() ?>>
</span>
<?php echo $student->AsalSekolah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AlamatSekolah->Visible) { // AlamatSekolah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_AlamatSekolah" class="form-group">
		<label id="elh_student_AlamatSekolah" for="x_AlamatSekolah" class="col-sm-2 control-label ewLabel"><?php echo $student->AlamatSekolah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->AlamatSekolah->CellAttributes() ?>>
<span id="el_student_AlamatSekolah">
<textarea data-table="student" data-field="x_AlamatSekolah" data-page="4" name="x_AlamatSekolah" id="x_AlamatSekolah" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($student->AlamatSekolah->getPlaceHolder()) ?>"<?php echo $student->AlamatSekolah->EditAttributes() ?>><?php echo $student->AlamatSekolah->EditValue ?></textarea>
</span>
<?php echo $student->AlamatSekolah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_AlamatSekolah">
		<td><span id="elh_student_AlamatSekolah"><?php echo $student->AlamatSekolah->FldCaption() ?></span></td>
		<td<?php echo $student->AlamatSekolah->CellAttributes() ?>>
<span id="el_student_AlamatSekolah">
<textarea data-table="student" data-field="x_AlamatSekolah" data-page="4" name="x_AlamatSekolah" id="x_AlamatSekolah" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($student->AlamatSekolah->getPlaceHolder()) ?>"<?php echo $student->AlamatSekolah->EditAttributes() ?>><?php echo $student->AlamatSekolah->EditValue ?></textarea>
</span>
<?php echo $student->AlamatSekolah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->ProvinsiIDSekolah->Visible) { // ProvinsiIDSekolah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_ProvinsiIDSekolah" class="form-group">
		<label id="elh_student_ProvinsiIDSekolah" for="x_ProvinsiIDSekolah" class="col-sm-2 control-label ewLabel"><?php echo $student->ProvinsiIDSekolah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->ProvinsiIDSekolah->CellAttributes() ?>>
<span id="el_student_ProvinsiIDSekolah">
<?php $student->ProvinsiIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiIDSekolah" data-page="4" data-value-separator="<?php echo $student->ProvinsiIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiIDSekolah" name="x_ProvinsiIDSekolah"<?php echo $student->ProvinsiIDSekolah->EditAttributes() ?>>
<?php echo $student->ProvinsiIDSekolah->SelectOptionListHtml("x_ProvinsiIDSekolah") ?>
</select>
<input type="hidden" name="s_x_ProvinsiIDSekolah" id="s_x_ProvinsiIDSekolah" value="<?php echo $student->ProvinsiIDSekolah->LookupFilterQuery() ?>">
</span>
<?php echo $student->ProvinsiIDSekolah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiIDSekolah">
		<td><span id="elh_student_ProvinsiIDSekolah"><?php echo $student->ProvinsiIDSekolah->FldCaption() ?></span></td>
		<td<?php echo $student->ProvinsiIDSekolah->CellAttributes() ?>>
<span id="el_student_ProvinsiIDSekolah">
<?php $student->ProvinsiIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiIDSekolah" data-page="4" data-value-separator="<?php echo $student->ProvinsiIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiIDSekolah" name="x_ProvinsiIDSekolah"<?php echo $student->ProvinsiIDSekolah->EditAttributes() ?>>
<?php echo $student->ProvinsiIDSekolah->SelectOptionListHtml("x_ProvinsiIDSekolah") ?>
</select>
<input type="hidden" name="s_x_ProvinsiIDSekolah" id="s_x_ProvinsiIDSekolah" value="<?php echo $student->ProvinsiIDSekolah->LookupFilterQuery() ?>">
</span>
<?php echo $student->ProvinsiIDSekolah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KabupatenIDSekolah->Visible) { // KabupatenIDSekolah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_KabupatenIDSekolah" class="form-group">
		<label id="elh_student_KabupatenIDSekolah" for="x_KabupatenIDSekolah" class="col-sm-2 control-label ewLabel"><?php echo $student->KabupatenIDSekolah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->KabupatenIDSekolah->CellAttributes() ?>>
<span id="el_student_KabupatenIDSekolah">
<?php $student->KabupatenIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenIDSekolah" data-page="4" data-value-separator="<?php echo $student->KabupatenIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenIDSekolah" name="x_KabupatenIDSekolah"<?php echo $student->KabupatenIDSekolah->EditAttributes() ?>>
<?php echo $student->KabupatenIDSekolah->SelectOptionListHtml("x_KabupatenIDSekolah") ?>
</select>
<input type="hidden" name="s_x_KabupatenIDSekolah" id="s_x_KabupatenIDSekolah" value="<?php echo $student->KabupatenIDSekolah->LookupFilterQuery() ?>">
</span>
<?php echo $student->KabupatenIDSekolah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenIDSekolah">
		<td><span id="elh_student_KabupatenIDSekolah"><?php echo $student->KabupatenIDSekolah->FldCaption() ?></span></td>
		<td<?php echo $student->KabupatenIDSekolah->CellAttributes() ?>>
<span id="el_student_KabupatenIDSekolah">
<?php $student->KabupatenIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenIDSekolah" data-page="4" data-value-separator="<?php echo $student->KabupatenIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenIDSekolah" name="x_KabupatenIDSekolah"<?php echo $student->KabupatenIDSekolah->EditAttributes() ?>>
<?php echo $student->KabupatenIDSekolah->SelectOptionListHtml("x_KabupatenIDSekolah") ?>
</select>
<input type="hidden" name="s_x_KabupatenIDSekolah" id="s_x_KabupatenIDSekolah" value="<?php echo $student->KabupatenIDSekolah->LookupFilterQuery() ?>">
</span>
<?php echo $student->KabupatenIDSekolah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KecamatanIDSekolah->Visible) { // KecamatanIDSekolah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_KecamatanIDSekolah" class="form-group">
		<label id="elh_student_KecamatanIDSekolah" for="x_KecamatanIDSekolah" class="col-sm-2 control-label ewLabel"><?php echo $student->KecamatanIDSekolah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->KecamatanIDSekolah->CellAttributes() ?>>
<span id="el_student_KecamatanIDSekolah">
<?php $student->KecamatanIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanIDSekolah" data-page="4" data-value-separator="<?php echo $student->KecamatanIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanIDSekolah" name="x_KecamatanIDSekolah"<?php echo $student->KecamatanIDSekolah->EditAttributes() ?>>
<?php echo $student->KecamatanIDSekolah->SelectOptionListHtml("x_KecamatanIDSekolah") ?>
</select>
<input type="hidden" name="s_x_KecamatanIDSekolah" id="s_x_KecamatanIDSekolah" value="<?php echo $student->KecamatanIDSekolah->LookupFilterQuery() ?>">
</span>
<?php echo $student->KecamatanIDSekolah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanIDSekolah">
		<td><span id="elh_student_KecamatanIDSekolah"><?php echo $student->KecamatanIDSekolah->FldCaption() ?></span></td>
		<td<?php echo $student->KecamatanIDSekolah->CellAttributes() ?>>
<span id="el_student_KecamatanIDSekolah">
<?php $student->KecamatanIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanIDSekolah" data-page="4" data-value-separator="<?php echo $student->KecamatanIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanIDSekolah" name="x_KecamatanIDSekolah"<?php echo $student->KecamatanIDSekolah->EditAttributes() ?>>
<?php echo $student->KecamatanIDSekolah->SelectOptionListHtml("x_KecamatanIDSekolah") ?>
</select>
<input type="hidden" name="s_x_KecamatanIDSekolah" id="s_x_KecamatanIDSekolah" value="<?php echo $student->KecamatanIDSekolah->LookupFilterQuery() ?>">
</span>
<?php echo $student->KecamatanIDSekolah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->DesaIDSekolah->Visible) { // DesaIDSekolah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_DesaIDSekolah" class="form-group">
		<label id="elh_student_DesaIDSekolah" for="x_DesaIDSekolah" class="col-sm-2 control-label ewLabel"><?php echo $student->DesaIDSekolah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->DesaIDSekolah->CellAttributes() ?>>
<span id="el_student_DesaIDSekolah">
<select data-table="student" data-field="x_DesaIDSekolah" data-page="4" data-value-separator="<?php echo $student->DesaIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_DesaIDSekolah" name="x_DesaIDSekolah"<?php echo $student->DesaIDSekolah->EditAttributes() ?>>
<?php echo $student->DesaIDSekolah->SelectOptionListHtml("x_DesaIDSekolah") ?>
</select>
<input type="hidden" name="s_x_DesaIDSekolah" id="s_x_DesaIDSekolah" value="<?php echo $student->DesaIDSekolah->LookupFilterQuery() ?>">
</span>
<?php echo $student->DesaIDSekolah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_DesaIDSekolah">
		<td><span id="elh_student_DesaIDSekolah"><?php echo $student->DesaIDSekolah->FldCaption() ?></span></td>
		<td<?php echo $student->DesaIDSekolah->CellAttributes() ?>>
<span id="el_student_DesaIDSekolah">
<select data-table="student" data-field="x_DesaIDSekolah" data-page="4" data-value-separator="<?php echo $student->DesaIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_DesaIDSekolah" name="x_DesaIDSekolah"<?php echo $student->DesaIDSekolah->EditAttributes() ?>>
<?php echo $student->DesaIDSekolah->SelectOptionListHtml("x_DesaIDSekolah") ?>
</select>
<input type="hidden" name="s_x_DesaIDSekolah" id="s_x_DesaIDSekolah" value="<?php echo $student->DesaIDSekolah->LookupFilterQuery() ?>">
</span>
<?php echo $student->DesaIDSekolah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->NilaiSekolah->Visible) { // NilaiSekolah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_NilaiSekolah" class="form-group">
		<label id="elh_student_NilaiSekolah" for="x_NilaiSekolah" class="col-sm-2 control-label ewLabel"><?php echo $student->NilaiSekolah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->NilaiSekolah->CellAttributes() ?>>
<span id="el_student_NilaiSekolah">
<input type="text" data-table="student" data-field="x_NilaiSekolah" data-page="4" name="x_NilaiSekolah" id="x_NilaiSekolah" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($student->NilaiSekolah->getPlaceHolder()) ?>" value="<?php echo $student->NilaiSekolah->EditValue ?>"<?php echo $student->NilaiSekolah->EditAttributes() ?>>
</span>
<?php echo $student->NilaiSekolah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NilaiSekolah">
		<td><span id="elh_student_NilaiSekolah"><?php echo $student->NilaiSekolah->FldCaption() ?></span></td>
		<td<?php echo $student->NilaiSekolah->CellAttributes() ?>>
<span id="el_student_NilaiSekolah">
<input type="text" data-table="student" data-field="x_NilaiSekolah" data-page="4" name="x_NilaiSekolah" id="x_NilaiSekolah" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($student->NilaiSekolah->getPlaceHolder()) ?>" value="<?php echo $student->NilaiSekolah->EditValue ?>"<?php echo $student->NilaiSekolah->EditAttributes() ?>>
</span>
<?php echo $student->NilaiSekolah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TahunLulus->Visible) { // TahunLulus ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_TahunLulus" class="form-group">
		<label id="elh_student_TahunLulus" for="x_TahunLulus" class="col-sm-2 control-label ewLabel"><?php echo $student->TahunLulus->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->TahunLulus->CellAttributes() ?>>
<span id="el_student_TahunLulus">
<input type="text" data-table="student" data-field="x_TahunLulus" data-page="4" name="x_TahunLulus" id="x_TahunLulus" size="30" maxlength="4" placeholder="<?php echo ew_HtmlEncode($student->TahunLulus->getPlaceHolder()) ?>" value="<?php echo $student->TahunLulus->EditValue ?>"<?php echo $student->TahunLulus->EditAttributes() ?>>
</span>
<?php echo $student->TahunLulus->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TahunLulus">
		<td><span id="elh_student_TahunLulus"><?php echo $student->TahunLulus->FldCaption() ?></span></td>
		<td<?php echo $student->TahunLulus->CellAttributes() ?>>
<span id="el_student_TahunLulus">
<input type="text" data-table="student" data-field="x_TahunLulus" data-page="4" name="x_TahunLulus" id="x_TahunLulus" size="30" maxlength="4" placeholder="<?php echo ew_HtmlEncode($student->TahunLulus->getPlaceHolder()) ?>" value="<?php echo $student->TahunLulus->EditValue ?>"<?php echo $student->TahunLulus->EditAttributes() ?>>
</span>
<?php echo $student->TahunLulus->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->IjazahSekolah->Visible) { // IjazahSekolah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_IjazahSekolah" class="form-group">
		<label id="elh_student_IjazahSekolah" for="x_IjazahSekolah" class="col-sm-2 control-label ewLabel"><?php echo $student->IjazahSekolah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->IjazahSekolah->CellAttributes() ?>>
<span id="el_student_IjazahSekolah">
<input type="text" data-table="student" data-field="x_IjazahSekolah" data-page="4" name="x_IjazahSekolah" id="x_IjazahSekolah" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->IjazahSekolah->getPlaceHolder()) ?>" value="<?php echo $student->IjazahSekolah->EditValue ?>"<?php echo $student->IjazahSekolah->EditAttributes() ?>>
</span>
<?php echo $student->IjazahSekolah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_IjazahSekolah">
		<td><span id="elh_student_IjazahSekolah"><?php echo $student->IjazahSekolah->FldCaption() ?></span></td>
		<td<?php echo $student->IjazahSekolah->CellAttributes() ?>>
<span id="el_student_IjazahSekolah">
<input type="text" data-table="student" data-field="x_IjazahSekolah" data-page="4" name="x_IjazahSekolah" id="x_IjazahSekolah" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->IjazahSekolah->getPlaceHolder()) ?>" value="<?php echo $student->IjazahSekolah->EditValue ?>"<?php echo $student->IjazahSekolah->EditAttributes() ?>>
</span>
<?php echo $student->IjazahSekolah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TglIjazah->Visible) { // TglIjazah ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_TglIjazah" class="form-group">
		<label id="elh_student_TglIjazah" for="x_TglIjazah" class="col-sm-2 control-label ewLabel"><?php echo $student->TglIjazah->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $student->TglIjazah->CellAttributes() ?>>
<span id="el_student_TglIjazah">
<input type="text" data-table="student" data-field="x_TglIjazah" data-page="4" name="x_TglIjazah" id="x_TglIjazah" placeholder="<?php echo ew_HtmlEncode($student->TglIjazah->getPlaceHolder()) ?>" value="<?php echo $student->TglIjazah->EditValue ?>"<?php echo $student->TglIjazah->EditAttributes() ?>>
</span>
<?php echo $student->TglIjazah->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TglIjazah">
		<td><span id="elh_student_TglIjazah"><?php echo $student->TglIjazah->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $student->TglIjazah->CellAttributes() ?>>
<span id="el_student_TglIjazah">
<input type="text" data-table="student" data-field="x_TglIjazah" data-page="4" name="x_TglIjazah" id="x_TglIjazah" placeholder="<?php echo ew_HtmlEncode($student->TglIjazah->getPlaceHolder()) ?>" value="<?php echo $student->TglIjazah->EditValue ?>"<?php echo $student->TglIjazah->EditAttributes() ?>>
</span>
<?php echo $student->TglIjazah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $student_edit->MultiPages->PageStyle("5") ?>" id="tab_student5">
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_studentedit5" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($student->LockStatus->Visible) { // LockStatus ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_LockStatus" class="form-group">
		<label id="elh_student_LockStatus" class="col-sm-2 control-label ewLabel"><?php echo $student->LockStatus->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->LockStatus->CellAttributes() ?>>
<span id="el_student_LockStatus">
<div id="tp_x_LockStatus" class="ewTemplate"><input type="radio" data-table="student" data-field="x_LockStatus" data-page="5" data-value-separator="<?php echo $student->LockStatus->DisplayValueSeparatorAttribute() ?>" name="x_LockStatus" id="x_LockStatus" value="{value}"<?php echo $student->LockStatus->EditAttributes() ?>></div>
<div id="dsl_x_LockStatus" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->LockStatus->RadioButtonListHtml(FALSE, "x_LockStatus", 5) ?>
</div></div>
</span>
<?php echo $student->LockStatus->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_LockStatus">
		<td><span id="elh_student_LockStatus"><?php echo $student->LockStatus->FldCaption() ?></span></td>
		<td<?php echo $student->LockStatus->CellAttributes() ?>>
<span id="el_student_LockStatus">
<div id="tp_x_LockStatus" class="ewTemplate"><input type="radio" data-table="student" data-field="x_LockStatus" data-page="5" data-value-separator="<?php echo $student->LockStatus->DisplayValueSeparatorAttribute() ?>" name="x_LockStatus" id="x_LockStatus" value="{value}"<?php echo $student->LockStatus->EditAttributes() ?>></div>
<div id="dsl_x_LockStatus" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->LockStatus->RadioButtonListHtml(FALSE, "x_LockStatus", 5) ?>
</div></div>
</span>
<?php echo $student->LockStatus->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_student_NA" class="col-sm-2 control-label ewLabel"><?php echo $student->NA->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->NA->CellAttributes() ?>>
<span id="el_student_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="student" data-field="x_NA" data-page="5" data-value-separator="<?php echo $student->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $student->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->NA->RadioButtonListHtml(FALSE, "x_NA", 5) ?>
</div></div>
</span>
<?php echo $student->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_student_NA"><?php echo $student->NA->FldCaption() ?></span></td>
		<td<?php echo $student->NA->CellAttributes() ?>>
<span id="el_student_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="student" data-field="x_NA" data-page="5" data-value-separator="<?php echo $student->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $student->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->NA->RadioButtonListHtml(FALSE, "x_NA", 5) ?>
</div></div>
</span>
<?php echo $student->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $student_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
	</div>
</div>
</div>
<?php if (!$student_edit->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $student_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fstudentedit.Init();
</script>
<?php
$student_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$student_edit->Page_Terminate();
?>
