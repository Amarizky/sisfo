<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "teacherinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$teacher_edit = NULL; // Initialize page object first

class cteacher_edit extends cteacher {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'teacher';

	// Page object name
	var $PageObjName = 'teacher_edit';

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

		// Table object (teacher)
		if (!isset($GLOBALS["teacher"]) || get_class($GLOBALS["teacher"]) == "cteacher") {
			$GLOBALS["teacher"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["teacher"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'teacher', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("teacherlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->TeacherID->SetVisibility();
		$this->NIPPNS->SetVisibility();
		$this->Nama->SetVisibility();
		$this->Gelar->SetVisibility();
		$this->LevelID->SetVisibility();
		$this->Password->SetVisibility();
		$this->AliasCode->SetVisibility();
		$this->KTP->SetVisibility();
		$this->TempatLahir->SetVisibility();
		$this->TanggalLahir->SetVisibility();
		$this->AgamaID->SetVisibility();
		$this->KelaminID->SetVisibility();
		$this->Telephone->SetVisibility();
		$this->_Email->SetVisibility();
		$this->Alamat->SetVisibility();
		$this->KodePos->SetVisibility();
		$this->ProvinsiID->SetVisibility();
		$this->KabupatenKotaID->SetVisibility();
		$this->KecamatanID->SetVisibility();
		$this->DesaID->SetVisibility();
		$this->InstitusiInduk->SetVisibility();
		$this->IkatanID->SetVisibility();
		$this->GolonganID->SetVisibility();
		$this->StatusKerjaID->SetVisibility();
		$this->TglBekerja->SetVisibility();
		$this->Homebase->SetVisibility();
		$this->ProdiID->SetVisibility();
		$this->Keilmuan->SetVisibility();
		$this->LulusanPT->SetVisibility();
		$this->NamaBank->SetVisibility();
		$this->NamaAkun->SetVisibility();
		$this->NomerAkun->SetVisibility();
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
		global $EW_EXPORT, $teacher;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($teacher);
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
		if (@$_GET["TeacherID"] <> "") {
			$this->TeacherID->setQueryStringValue($_GET["TeacherID"]);
		}

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->TeacherID->CurrentValue == "") {
			$this->Page_Terminate("teacherlist.php"); // Invalid key, return to list
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
					$this->Page_Terminate("teacherlist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "teacherlist.php")
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
		if (!$this->TeacherID->FldIsDetailKey) {
			$this->TeacherID->setFormValue($objForm->GetValue("x_TeacherID"));
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
		if (!$this->LevelID->FldIsDetailKey) {
			$this->LevelID->setFormValue($objForm->GetValue("x_LevelID"));
		}
		if (!$this->Password->FldIsDetailKey) {
			$this->Password->setFormValue($objForm->GetValue("x_Password"));
		}
		if (!$this->AliasCode->FldIsDetailKey) {
			$this->AliasCode->setFormValue($objForm->GetValue("x_AliasCode"));
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
		if (!$this->AgamaID->FldIsDetailKey) {
			$this->AgamaID->setFormValue($objForm->GetValue("x_AgamaID"));
		}
		if (!$this->KelaminID->FldIsDetailKey) {
			$this->KelaminID->setFormValue($objForm->GetValue("x_KelaminID"));
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
		if (!$this->InstitusiInduk->FldIsDetailKey) {
			$this->InstitusiInduk->setFormValue($objForm->GetValue("x_InstitusiInduk"));
		}
		if (!$this->IkatanID->FldIsDetailKey) {
			$this->IkatanID->setFormValue($objForm->GetValue("x_IkatanID"));
		}
		if (!$this->GolonganID->FldIsDetailKey) {
			$this->GolonganID->setFormValue($objForm->GetValue("x_GolonganID"));
		}
		if (!$this->StatusKerjaID->FldIsDetailKey) {
			$this->StatusKerjaID->setFormValue($objForm->GetValue("x_StatusKerjaID"));
		}
		if (!$this->TglBekerja->FldIsDetailKey) {
			$this->TglBekerja->setFormValue($objForm->GetValue("x_TglBekerja"));
			$this->TglBekerja->CurrentValue = ew_UnFormatDateTime($this->TglBekerja->CurrentValue, 0);
		}
		if (!$this->Homebase->FldIsDetailKey) {
			$this->Homebase->setFormValue($objForm->GetValue("x_Homebase"));
		}
		if (!$this->ProdiID->FldIsDetailKey) {
			$this->ProdiID->setFormValue($objForm->GetValue("x_ProdiID"));
		}
		if (!$this->Keilmuan->FldIsDetailKey) {
			$this->Keilmuan->setFormValue($objForm->GetValue("x_Keilmuan"));
		}
		if (!$this->LulusanPT->FldIsDetailKey) {
			$this->LulusanPT->setFormValue($objForm->GetValue("x_LulusanPT"));
		}
		if (!$this->NamaBank->FldIsDetailKey) {
			$this->NamaBank->setFormValue($objForm->GetValue("x_NamaBank"));
		}
		if (!$this->NamaAkun->FldIsDetailKey) {
			$this->NamaAkun->setFormValue($objForm->GetValue("x_NamaAkun"));
		}
		if (!$this->NomerAkun->FldIsDetailKey) {
			$this->NomerAkun->setFormValue($objForm->GetValue("x_NomerAkun"));
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
		$this->TeacherID->CurrentValue = $this->TeacherID->FormValue;
		$this->NIPPNS->CurrentValue = $this->NIPPNS->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->Gelar->CurrentValue = $this->Gelar->FormValue;
		$this->LevelID->CurrentValue = $this->LevelID->FormValue;
		$this->Password->CurrentValue = $this->Password->FormValue;
		$this->AliasCode->CurrentValue = $this->AliasCode->FormValue;
		$this->KTP->CurrentValue = $this->KTP->FormValue;
		$this->TempatLahir->CurrentValue = $this->TempatLahir->FormValue;
		$this->TanggalLahir->CurrentValue = $this->TanggalLahir->FormValue;
		$this->TanggalLahir->CurrentValue = ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0);
		$this->AgamaID->CurrentValue = $this->AgamaID->FormValue;
		$this->KelaminID->CurrentValue = $this->KelaminID->FormValue;
		$this->Telephone->CurrentValue = $this->Telephone->FormValue;
		$this->_Email->CurrentValue = $this->_Email->FormValue;
		$this->Alamat->CurrentValue = $this->Alamat->FormValue;
		$this->KodePos->CurrentValue = $this->KodePos->FormValue;
		$this->ProvinsiID->CurrentValue = $this->ProvinsiID->FormValue;
		$this->KabupatenKotaID->CurrentValue = $this->KabupatenKotaID->FormValue;
		$this->KecamatanID->CurrentValue = $this->KecamatanID->FormValue;
		$this->DesaID->CurrentValue = $this->DesaID->FormValue;
		$this->InstitusiInduk->CurrentValue = $this->InstitusiInduk->FormValue;
		$this->IkatanID->CurrentValue = $this->IkatanID->FormValue;
		$this->GolonganID->CurrentValue = $this->GolonganID->FormValue;
		$this->StatusKerjaID->CurrentValue = $this->StatusKerjaID->FormValue;
		$this->TglBekerja->CurrentValue = $this->TglBekerja->FormValue;
		$this->TglBekerja->CurrentValue = ew_UnFormatDateTime($this->TglBekerja->CurrentValue, 0);
		$this->Homebase->CurrentValue = $this->Homebase->FormValue;
		$this->ProdiID->CurrentValue = $this->ProdiID->FormValue;
		$this->Keilmuan->CurrentValue = $this->Keilmuan->FormValue;
		$this->LulusanPT->CurrentValue = $this->LulusanPT->FormValue;
		$this->NamaBank->CurrentValue = $this->NamaBank->FormValue;
		$this->NamaAkun->CurrentValue = $this->NamaAkun->FormValue;
		$this->NomerAkun->CurrentValue = $this->NomerAkun->FormValue;
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
		$this->TeacherID->setDbValue($rs->fields('TeacherID'));
		$this->NIPPNS->setDbValue($rs->fields('NIPPNS'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->Gelar->setDbValue($rs->fields('Gelar'));
		$this->LevelID->setDbValue($rs->fields('LevelID'));
		$this->Password->setDbValue($rs->fields('Password'));
		$this->AliasCode->setDbValue($rs->fields('AliasCode'));
		$this->KTP->setDbValue($rs->fields('KTP'));
		$this->TempatLahir->setDbValue($rs->fields('TempatLahir'));
		$this->TanggalLahir->setDbValue($rs->fields('TanggalLahir'));
		$this->AgamaID->setDbValue($rs->fields('AgamaID'));
		$this->KelaminID->setDbValue($rs->fields('KelaminID'));
		$this->Telephone->setDbValue($rs->fields('Telephone'));
		$this->Handphone->setDbValue($rs->fields('Handphone'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Alamat->setDbValue($rs->fields('Alamat'));
		$this->KodePos->setDbValue($rs->fields('KodePos'));
		$this->ProvinsiID->setDbValue($rs->fields('ProvinsiID'));
		$this->KabupatenKotaID->setDbValue($rs->fields('KabupatenKotaID'));
		$this->KecamatanID->setDbValue($rs->fields('KecamatanID'));
		$this->DesaID->setDbValue($rs->fields('DesaID'));
		$this->InstitusiInduk->setDbValue($rs->fields('InstitusiInduk'));
		$this->IkatanID->setDbValue($rs->fields('IkatanID'));
		$this->GolonganID->setDbValue($rs->fields('GolonganID'));
		$this->StatusKerjaID->setDbValue($rs->fields('StatusKerjaID'));
		$this->TglBekerja->setDbValue($rs->fields('TglBekerja'));
		$this->Homebase->setDbValue($rs->fields('Homebase'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->Keilmuan->setDbValue($rs->fields('Keilmuan'));
		$this->LulusanPT->setDbValue($rs->fields('LulusanPT'));
		$this->NamaBank->setDbValue($rs->fields('NamaBank'));
		$this->NamaAkun->setDbValue($rs->fields('NamaAkun'));
		$this->NomerAkun->setDbValue($rs->fields('NomerAkun'));
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
		$this->TeacherID->DbValue = $row['TeacherID'];
		$this->NIPPNS->DbValue = $row['NIPPNS'];
		$this->Nama->DbValue = $row['Nama'];
		$this->Gelar->DbValue = $row['Gelar'];
		$this->LevelID->DbValue = $row['LevelID'];
		$this->Password->DbValue = $row['Password'];
		$this->AliasCode->DbValue = $row['AliasCode'];
		$this->KTP->DbValue = $row['KTP'];
		$this->TempatLahir->DbValue = $row['TempatLahir'];
		$this->TanggalLahir->DbValue = $row['TanggalLahir'];
		$this->AgamaID->DbValue = $row['AgamaID'];
		$this->KelaminID->DbValue = $row['KelaminID'];
		$this->Telephone->DbValue = $row['Telephone'];
		$this->Handphone->DbValue = $row['Handphone'];
		$this->_Email->DbValue = $row['Email'];
		$this->Alamat->DbValue = $row['Alamat'];
		$this->KodePos->DbValue = $row['KodePos'];
		$this->ProvinsiID->DbValue = $row['ProvinsiID'];
		$this->KabupatenKotaID->DbValue = $row['KabupatenKotaID'];
		$this->KecamatanID->DbValue = $row['KecamatanID'];
		$this->DesaID->DbValue = $row['DesaID'];
		$this->InstitusiInduk->DbValue = $row['InstitusiInduk'];
		$this->IkatanID->DbValue = $row['IkatanID'];
		$this->GolonganID->DbValue = $row['GolonganID'];
		$this->StatusKerjaID->DbValue = $row['StatusKerjaID'];
		$this->TglBekerja->DbValue = $row['TglBekerja'];
		$this->Homebase->DbValue = $row['Homebase'];
		$this->ProdiID->DbValue = $row['ProdiID'];
		$this->Keilmuan->DbValue = $row['Keilmuan'];
		$this->LulusanPT->DbValue = $row['LulusanPT'];
		$this->NamaBank->DbValue = $row['NamaBank'];
		$this->NamaAkun->DbValue = $row['NamaAkun'];
		$this->NomerAkun->DbValue = $row['NomerAkun'];
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
		// TeacherID
		// NIPPNS
		// Nama
		// Gelar
		// LevelID
		// Password
		// AliasCode
		// KTP
		// TempatLahir
		// TanggalLahir
		// AgamaID
		// KelaminID
		// Telephone
		// Handphone
		// Email
		// Alamat
		// KodePos
		// ProvinsiID
		// KabupatenKotaID
		// KecamatanID
		// DesaID
		// InstitusiInduk
		// IkatanID
		// GolonganID
		// StatusKerjaID
		// TglBekerja
		// Homebase
		// ProdiID
		// Keilmuan
		// LulusanPT
		// NamaBank
		// NamaAkun
		// NomerAkun
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// TeacherID
		$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
		$this->TeacherID->CssStyle = "font-weight: bold;";
		$this->TeacherID->ViewCustomAttributes = "";

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

		// LevelID
		$this->LevelID->ViewValue = $this->LevelID->CurrentValue;
		$this->LevelID->ViewCustomAttributes = "";

		// Password
		$this->Password->ViewValue = $Language->Phrase("PasswordMask");
		$this->Password->ViewCustomAttributes = "";

		// AliasCode
		$this->AliasCode->ViewValue = $this->AliasCode->CurrentValue;
		$this->AliasCode->ViewCustomAttributes = "";

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

		// Telephone
		$this->Telephone->ViewValue = $this->Telephone->CurrentValue;
		$this->Telephone->ViewCustomAttributes = "";

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

		// InstitusiInduk
		if (strval($this->InstitusiInduk->CurrentValue) <> "") {
			$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->InstitusiInduk->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
		$sWhereWrk = "";
		$this->InstitusiInduk->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->InstitusiInduk, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->InstitusiInduk->ViewValue = $this->InstitusiInduk->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->InstitusiInduk->ViewValue = $this->InstitusiInduk->CurrentValue;
			}
		} else {
			$this->InstitusiInduk->ViewValue = NULL;
		}
		$this->InstitusiInduk->ViewCustomAttributes = "";

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

		// Homebase
		if (strval($this->Homebase->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->Homebase->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->Homebase->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Homebase, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Homebase->ViewValue = $this->Homebase->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Homebase->ViewValue = $this->Homebase->CurrentValue;
			}
		} else {
			$this->Homebase->ViewValue = NULL;
		}
		$this->Homebase->ViewCustomAttributes = "";

		// ProdiID
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$arwrk = explode(",", $this->ProdiID->CurrentValue);
			$sFilterWrk = "";
			foreach ($arwrk as $wrk) {
				if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
				$sFilterWrk .= "`ProdiID`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING, "");
			}
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->ProdiID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->ProdiID->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->ProdiID->ViewValue .= $this->ProdiID->DisplayValue($arwrk);
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->ProdiID->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
				$rswrk->Close();
			} else {
				$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
			}
		} else {
			$this->ProdiID->ViewValue = NULL;
		}
		$this->ProdiID->ViewCustomAttributes = "";

		// Keilmuan
		$this->Keilmuan->ViewValue = $this->Keilmuan->CurrentValue;
		$this->Keilmuan->ViewCustomAttributes = "";

		// LulusanPT
		$this->LulusanPT->ViewValue = $this->LulusanPT->CurrentValue;
		$this->LulusanPT->ViewCustomAttributes = "";

		// NamaBank
		$this->NamaBank->ViewValue = $this->NamaBank->CurrentValue;
		$this->NamaBank->ViewCustomAttributes = "";

		// NamaAkun
		$this->NamaAkun->ViewValue = $this->NamaAkun->CurrentValue;
		$this->NamaAkun->ViewCustomAttributes = "";

		// NomerAkun
		$this->NomerAkun->ViewValue = $this->NomerAkun->CurrentValue;
		$this->NomerAkun->ViewCustomAttributes = "";

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

			// TeacherID
			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";
			$this->TeacherID->TooltipValue = "";

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

			// LevelID
			$this->LevelID->LinkCustomAttributes = "";
			$this->LevelID->HrefValue = "";
			$this->LevelID->TooltipValue = "";

			// Password
			$this->Password->LinkCustomAttributes = "";
			$this->Password->HrefValue = "";
			$this->Password->TooltipValue = "";

			// AliasCode
			$this->AliasCode->LinkCustomAttributes = "";
			$this->AliasCode->HrefValue = "";
			$this->AliasCode->TooltipValue = "";

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

			// AgamaID
			$this->AgamaID->LinkCustomAttributes = "";
			$this->AgamaID->HrefValue = "";
			$this->AgamaID->TooltipValue = "";

			// KelaminID
			$this->KelaminID->LinkCustomAttributes = "";
			$this->KelaminID->HrefValue = "";
			$this->KelaminID->TooltipValue = "";

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

			// InstitusiInduk
			$this->InstitusiInduk->LinkCustomAttributes = "";
			$this->InstitusiInduk->HrefValue = "";
			$this->InstitusiInduk->TooltipValue = "";

			// IkatanID
			$this->IkatanID->LinkCustomAttributes = "";
			$this->IkatanID->HrefValue = "";
			$this->IkatanID->TooltipValue = "";

			// GolonganID
			$this->GolonganID->LinkCustomAttributes = "";
			$this->GolonganID->HrefValue = "";
			$this->GolonganID->TooltipValue = "";

			// StatusKerjaID
			$this->StatusKerjaID->LinkCustomAttributes = "";
			$this->StatusKerjaID->HrefValue = "";
			$this->StatusKerjaID->TooltipValue = "";

			// TglBekerja
			$this->TglBekerja->LinkCustomAttributes = "";
			$this->TglBekerja->HrefValue = "";
			$this->TglBekerja->TooltipValue = "";

			// Homebase
			$this->Homebase->LinkCustomAttributes = "";
			$this->Homebase->HrefValue = "";
			$this->Homebase->TooltipValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// Keilmuan
			$this->Keilmuan->LinkCustomAttributes = "";
			$this->Keilmuan->HrefValue = "";
			$this->Keilmuan->TooltipValue = "";

			// LulusanPT
			$this->LulusanPT->LinkCustomAttributes = "";
			$this->LulusanPT->HrefValue = "";
			$this->LulusanPT->TooltipValue = "";

			// NamaBank
			$this->NamaBank->LinkCustomAttributes = "";
			$this->NamaBank->HrefValue = "";
			$this->NamaBank->TooltipValue = "";

			// NamaAkun
			$this->NamaAkun->LinkCustomAttributes = "";
			$this->NamaAkun->HrefValue = "";
			$this->NamaAkun->TooltipValue = "";

			// NomerAkun
			$this->NomerAkun->LinkCustomAttributes = "";
			$this->NomerAkun->HrefValue = "";
			$this->NomerAkun->TooltipValue = "";

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

			// TeacherID
			$this->TeacherID->EditAttrs["class"] = "form-control";
			$this->TeacherID->EditCustomAttributes = "";
			$this->TeacherID->EditValue = $this->TeacherID->CurrentValue;
			$this->TeacherID->CssStyle = "font-weight: bold;";
			$this->TeacherID->ViewCustomAttributes = "";

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

			// AliasCode
			$this->AliasCode->EditAttrs["class"] = "form-control";
			$this->AliasCode->EditCustomAttributes = "";
			$this->AliasCode->EditValue = ew_HtmlEncode($this->AliasCode->CurrentValue);
			$this->AliasCode->PlaceHolder = ew_RemoveHtml($this->AliasCode->FldCaption());

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

			// InstitusiInduk
			$this->InstitusiInduk->EditAttrs["class"] = "form-control";
			$this->InstitusiInduk->EditCustomAttributes = "";
			if (trim(strval($this->InstitusiInduk->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->InstitusiInduk->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_kampus`";
			$sWhereWrk = "";
			$this->InstitusiInduk->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->InstitusiInduk, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->InstitusiInduk->EditValue = $arwrk;

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

			// Homebase
			$this->Homebase->EditAttrs["class"] = "form-control";
			$this->Homebase->EditCustomAttributes = "";
			if (trim(strval($this->Homebase->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->Homebase->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->Homebase->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Homebase, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Homebase->EditValue = $arwrk;

			// ProdiID
			$this->ProdiID->EditCustomAttributes = "";
			if (trim(strval($this->ProdiID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$arwrk = explode(",", $this->ProdiID->CurrentValue);
				$sFilterWrk = "";
				foreach ($arwrk as $wrk) {
					if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
					$sFilterWrk .= "`ProdiID`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING, "");
				}
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

			// Keilmuan
			$this->Keilmuan->EditAttrs["class"] = "form-control";
			$this->Keilmuan->EditCustomAttributes = "";
			$this->Keilmuan->EditValue = ew_HtmlEncode($this->Keilmuan->CurrentValue);
			$this->Keilmuan->PlaceHolder = ew_RemoveHtml($this->Keilmuan->FldCaption());

			// LulusanPT
			$this->LulusanPT->EditAttrs["class"] = "form-control";
			$this->LulusanPT->EditCustomAttributes = "";
			$this->LulusanPT->EditValue = ew_HtmlEncode($this->LulusanPT->CurrentValue);
			$this->LulusanPT->PlaceHolder = ew_RemoveHtml($this->LulusanPT->FldCaption());

			// NamaBank
			$this->NamaBank->EditAttrs["class"] = "form-control";
			$this->NamaBank->EditCustomAttributes = "";
			$this->NamaBank->EditValue = ew_HtmlEncode($this->NamaBank->CurrentValue);
			$this->NamaBank->PlaceHolder = ew_RemoveHtml($this->NamaBank->FldCaption());

			// NamaAkun
			$this->NamaAkun->EditAttrs["class"] = "form-control";
			$this->NamaAkun->EditCustomAttributes = "";
			$this->NamaAkun->EditValue = ew_HtmlEncode($this->NamaAkun->CurrentValue);
			$this->NamaAkun->PlaceHolder = ew_RemoveHtml($this->NamaAkun->FldCaption());

			// NomerAkun
			$this->NomerAkun->EditAttrs["class"] = "form-control";
			$this->NomerAkun->EditCustomAttributes = "";
			$this->NomerAkun->EditValue = ew_HtmlEncode($this->NomerAkun->CurrentValue);
			$this->NomerAkun->PlaceHolder = ew_RemoveHtml($this->NomerAkun->FldCaption());

			// Editor
			// EditDate
			// NA

			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Edit refer script
			// TeacherID

			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";

			// NIPPNS
			$this->NIPPNS->LinkCustomAttributes = "";
			$this->NIPPNS->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// Gelar
			$this->Gelar->LinkCustomAttributes = "";
			$this->Gelar->HrefValue = "";

			// LevelID
			$this->LevelID->LinkCustomAttributes = "";
			$this->LevelID->HrefValue = "";

			// Password
			$this->Password->LinkCustomAttributes = "";
			$this->Password->HrefValue = "";

			// AliasCode
			$this->AliasCode->LinkCustomAttributes = "";
			$this->AliasCode->HrefValue = "";

			// KTP
			$this->KTP->LinkCustomAttributes = "";
			$this->KTP->HrefValue = "";

			// TempatLahir
			$this->TempatLahir->LinkCustomAttributes = "";
			$this->TempatLahir->HrefValue = "";

			// TanggalLahir
			$this->TanggalLahir->LinkCustomAttributes = "";
			$this->TanggalLahir->HrefValue = "";

			// AgamaID
			$this->AgamaID->LinkCustomAttributes = "";
			$this->AgamaID->HrefValue = "";

			// KelaminID
			$this->KelaminID->LinkCustomAttributes = "";
			$this->KelaminID->HrefValue = "";

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

			// InstitusiInduk
			$this->InstitusiInduk->LinkCustomAttributes = "";
			$this->InstitusiInduk->HrefValue = "";

			// IkatanID
			$this->IkatanID->LinkCustomAttributes = "";
			$this->IkatanID->HrefValue = "";

			// GolonganID
			$this->GolonganID->LinkCustomAttributes = "";
			$this->GolonganID->HrefValue = "";

			// StatusKerjaID
			$this->StatusKerjaID->LinkCustomAttributes = "";
			$this->StatusKerjaID->HrefValue = "";

			// TglBekerja
			$this->TglBekerja->LinkCustomAttributes = "";
			$this->TglBekerja->HrefValue = "";

			// Homebase
			$this->Homebase->LinkCustomAttributes = "";
			$this->Homebase->HrefValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";

			// Keilmuan
			$this->Keilmuan->LinkCustomAttributes = "";
			$this->Keilmuan->HrefValue = "";

			// LulusanPT
			$this->LulusanPT->LinkCustomAttributes = "";
			$this->LulusanPT->HrefValue = "";

			// NamaBank
			$this->NamaBank->LinkCustomAttributes = "";
			$this->NamaBank->HrefValue = "";

			// NamaAkun
			$this->NamaAkun->LinkCustomAttributes = "";
			$this->NamaAkun->HrefValue = "";

			// NomerAkun
			$this->NomerAkun->LinkCustomAttributes = "";
			$this->NomerAkun->HrefValue = "";

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
		if (!$this->TeacherID->FldIsDetailKey && !is_null($this->TeacherID->FormValue) && $this->TeacherID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TeacherID->FldCaption(), $this->TeacherID->ReqErrMsg));
		}
		if (!$this->Nama->FldIsDetailKey && !is_null($this->Nama->FormValue) && $this->Nama->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Nama->FldCaption(), $this->Nama->ReqErrMsg));
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

			// TeacherID
			// NIPPNS

			$this->NIPPNS->SetDbValueDef($rsnew, $this->NIPPNS->CurrentValue, NULL, $this->NIPPNS->ReadOnly);

			// Nama
			$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, "", $this->Nama->ReadOnly);

			// Gelar
			$this->Gelar->SetDbValueDef($rsnew, $this->Gelar->CurrentValue, NULL, $this->Gelar->ReadOnly);

			// LevelID
			$this->LevelID->SetDbValueDef($rsnew, $this->LevelID->CurrentValue, "", $this->LevelID->ReadOnly);

			// Password
			$this->Password->SetDbValueDef($rsnew, $this->Password->CurrentValue, NULL, $this->Password->ReadOnly);

			// AliasCode
			$this->AliasCode->SetDbValueDef($rsnew, $this->AliasCode->CurrentValue, NULL, $this->AliasCode->ReadOnly);

			// KTP
			$this->KTP->SetDbValueDef($rsnew, $this->KTP->CurrentValue, NULL, $this->KTP->ReadOnly);

			// TempatLahir
			$this->TempatLahir->SetDbValueDef($rsnew, $this->TempatLahir->CurrentValue, NULL, $this->TempatLahir->ReadOnly);

			// TanggalLahir
			$this->TanggalLahir->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0), NULL, $this->TanggalLahir->ReadOnly);

			// AgamaID
			$this->AgamaID->SetDbValueDef($rsnew, $this->AgamaID->CurrentValue, NULL, $this->AgamaID->ReadOnly);

			// KelaminID
			$this->KelaminID->SetDbValueDef($rsnew, $this->KelaminID->CurrentValue, NULL, $this->KelaminID->ReadOnly);

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

			// InstitusiInduk
			$this->InstitusiInduk->SetDbValueDef($rsnew, $this->InstitusiInduk->CurrentValue, NULL, $this->InstitusiInduk->ReadOnly);

			// IkatanID
			$this->IkatanID->SetDbValueDef($rsnew, $this->IkatanID->CurrentValue, NULL, $this->IkatanID->ReadOnly);

			// GolonganID
			$this->GolonganID->SetDbValueDef($rsnew, $this->GolonganID->CurrentValue, NULL, $this->GolonganID->ReadOnly);

			// StatusKerjaID
			$this->StatusKerjaID->SetDbValueDef($rsnew, $this->StatusKerjaID->CurrentValue, NULL, $this->StatusKerjaID->ReadOnly);

			// TglBekerja
			$this->TglBekerja->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TglBekerja->CurrentValue, 0), NULL, $this->TglBekerja->ReadOnly);

			// Homebase
			$this->Homebase->SetDbValueDef($rsnew, $this->Homebase->CurrentValue, NULL, $this->Homebase->ReadOnly);

			// ProdiID
			$this->ProdiID->SetDbValueDef($rsnew, $this->ProdiID->CurrentValue, NULL, $this->ProdiID->ReadOnly);

			// Keilmuan
			$this->Keilmuan->SetDbValueDef($rsnew, $this->Keilmuan->CurrentValue, NULL, $this->Keilmuan->ReadOnly);

			// LulusanPT
			$this->LulusanPT->SetDbValueDef($rsnew, $this->LulusanPT->CurrentValue, NULL, $this->LulusanPT->ReadOnly);

			// NamaBank
			$this->NamaBank->SetDbValueDef($rsnew, $this->NamaBank->CurrentValue, NULL, $this->NamaBank->ReadOnly);

			// NamaAkun
			$this->NamaAkun->SetDbValueDef($rsnew, $this->NamaAkun->CurrentValue, NULL, $this->NamaAkun->ReadOnly);

			// NomerAkun
			$this->NomerAkun->SetDbValueDef($rsnew, $this->NomerAkun->CurrentValue, NULL, $this->NomerAkun->ReadOnly);

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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("teacherlist.php"), "", $this->TableVar, TRUE);
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
		case "x_InstitusiInduk":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KampusID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
			$sWhereWrk = "";
			$this->InstitusiInduk->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KampusID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->InstitusiInduk, $sWhereWrk); // Call Lookup selecting
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
		case "x_Homebase":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProdiID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->Homebase->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProdiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Homebase, $sWhereWrk); // Call Lookup selecting
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
if (!isset($teacher_edit)) $teacher_edit = new cteacher_edit();

// Page init
$teacher_edit->Page_Init();

// Page main
$teacher_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$teacher_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fteacheredit = new ew_Form("fteacheredit", "edit");

// Validate form
fteacheredit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_TeacherID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $teacher->TeacherID->FldCaption(), $teacher->TeacherID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Nama");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $teacher->Nama->FldCaption(), $teacher->Nama->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_LevelID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $teacher->LevelID->FldCaption(), $teacher->LevelID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_LevelID");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($teacher->LevelID->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Password");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $teacher->Password->FldCaption(), $teacher->Password->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TanggalLahir");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($teacher->TanggalLahir->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "__Email");
			if (elm && !ew_CheckEmail(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($teacher->_Email->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_TglBekerja");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($teacher->TglBekerja->FldErrMsg()) ?>");

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
fteacheredit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fteacheredit.ValidateRequired = true;
<?php } else { ?>
fteacheredit.ValidateRequired = false; 
<?php } ?>

// Multi-Page
fteacheredit.MultiPage = new ew_MultiPage("fteacheredit");

// Dynamic selection lists
fteacheredit.Lists["x_TempatLahir"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fteacheredit.Lists["x_AgamaID"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fteacheredit.Lists["x_KelaminID"] = {"LinkField":"x_Kelamin","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kelamin"};
fteacheredit.Lists["x_ProvinsiID"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenKotaID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fteacheredit.Lists["x_KabupatenKotaID"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":["x_ProvinsiID"],"ChildFields":["x_KecamatanID"],"FilterFields":["x_ProvinsiID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fteacheredit.Lists["x_KecamatanID"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":["x_KabupatenKotaID"],"ChildFields":["x_DesaID"],"FilterFields":["x_KabupatenKotaID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fteacheredit.Lists["x_DesaID"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":["x_KecamatanID"],"ChildFields":[],"FilterFields":["x_KecamatanID"],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fteacheredit.Lists["x_InstitusiInduk"] = {"LinkField":"x_KampusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kampus"};
fteacheredit.Lists["x_IkatanID"] = {"LinkField":"x_IkatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_ikatan"};
fteacheredit.Lists["x_GolonganID"] = {"LinkField":"x_GolonganID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_golongan"};
fteacheredit.Lists["x_StatusKerjaID"] = {"LinkField":"x_StatusKerjaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statuskerja"};
fteacheredit.Lists["x_Homebase"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fteacheredit.Lists["x_ProdiID[]"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fteacheredit.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fteacheredit.Lists["x_NA"].Options = <?php echo json_encode($teacher->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$teacher_edit->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $teacher_edit->ShowPageHeader(); ?>
<?php
$teacher_edit->ShowMessage();
?>
<form name="fteacheredit" id="fteacheredit" class="<?php echo $teacher_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($teacher_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $teacher_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="teacher">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<?php if ($teacher_edit->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$teacher_edit->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<div class="ewMultiPage">
<div class="tabbable" id="teacher_edit">
	<ul class="nav<?php echo $teacher_edit->MultiPages->NavStyle() ?>">
		<li<?php echo $teacher_edit->MultiPages->TabStyle("1") ?>><a href="#tab_teacher1" data-toggle="tab"><?php echo $teacher->PageCaption(1) ?></a></li>
		<li<?php echo $teacher_edit->MultiPages->TabStyle("2") ?>><a href="#tab_teacher2" data-toggle="tab"><?php echo $teacher->PageCaption(2) ?></a></li>
		<li<?php echo $teacher_edit->MultiPages->TabStyle("3") ?>><a href="#tab_teacher3" data-toggle="tab"><?php echo $teacher->PageCaption(3) ?></a></li>
		<li<?php echo $teacher_edit->MultiPages->TabStyle("4") ?>><a href="#tab_teacher4" data-toggle="tab"><?php echo $teacher->PageCaption(4) ?></a></li>
		<li<?php echo $teacher_edit->MultiPages->TabStyle("5") ?>><a href="#tab_teacher5" data-toggle="tab"><?php echo $teacher->PageCaption(5) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane<?php echo $teacher_edit->MultiPages->PageStyle("1") ?>" id="tab_teacher1">
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_teacheredit1" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($teacher->TeacherID->Visible) { // TeacherID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_TeacherID" class="form-group">
		<label id="elh_teacher_TeacherID" for="x_TeacherID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->TeacherID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->TeacherID->CellAttributes() ?>>
<span id="el_teacher_TeacherID">
<span<?php echo $teacher->TeacherID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $teacher->TeacherID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="teacher" data-field="x_TeacherID" data-page="1" name="x_TeacherID" id="x_TeacherID" value="<?php echo ew_HtmlEncode($teacher->TeacherID->CurrentValue) ?>">
<?php echo $teacher->TeacherID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TeacherID">
		<td><span id="elh_teacher_TeacherID"><?php echo $teacher->TeacherID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $teacher->TeacherID->CellAttributes() ?>>
<span id="el_teacher_TeacherID">
<span<?php echo $teacher->TeacherID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $teacher->TeacherID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="teacher" data-field="x_TeacherID" data-page="1" name="x_TeacherID" id="x_TeacherID" value="<?php echo ew_HtmlEncode($teacher->TeacherID->CurrentValue) ?>">
<?php echo $teacher->TeacherID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->NIPPNS->Visible) { // NIPPNS ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_NIPPNS" class="form-group">
		<label id="elh_teacher_NIPPNS" for="x_NIPPNS" class="col-sm-2 control-label ewLabel"><?php echo $teacher->NIPPNS->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->NIPPNS->CellAttributes() ?>>
<span id="el_teacher_NIPPNS">
<input type="text" data-table="teacher" data-field="x_NIPPNS" data-page="1" name="x_NIPPNS" id="x_NIPPNS" size="30" maxlength="30" placeholder="<?php echo ew_HtmlEncode($teacher->NIPPNS->getPlaceHolder()) ?>" value="<?php echo $teacher->NIPPNS->EditValue ?>"<?php echo $teacher->NIPPNS->EditAttributes() ?>>
</span>
<?php echo $teacher->NIPPNS->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NIPPNS">
		<td><span id="elh_teacher_NIPPNS"><?php echo $teacher->NIPPNS->FldCaption() ?></span></td>
		<td<?php echo $teacher->NIPPNS->CellAttributes() ?>>
<span id="el_teacher_NIPPNS">
<input type="text" data-table="teacher" data-field="x_NIPPNS" data-page="1" name="x_NIPPNS" id="x_NIPPNS" size="30" maxlength="30" placeholder="<?php echo ew_HtmlEncode($teacher->NIPPNS->getPlaceHolder()) ?>" value="<?php echo $teacher->NIPPNS->EditValue ?>"<?php echo $teacher->NIPPNS->EditAttributes() ?>>
</span>
<?php echo $teacher->NIPPNS->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label id="elh_teacher_Nama" for="x_Nama" class="col-sm-2 control-label ewLabel"><?php echo $teacher->Nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->Nama->CellAttributes() ?>>
<span id="el_teacher_Nama">
<input type="text" data-table="teacher" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->Nama->getPlaceHolder()) ?>" value="<?php echo $teacher->Nama->EditValue ?>"<?php echo $teacher->Nama->EditAttributes() ?>>
</span>
<?php echo $teacher->Nama->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_teacher_Nama"><?php echo $teacher->Nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $teacher->Nama->CellAttributes() ?>>
<span id="el_teacher_Nama">
<input type="text" data-table="teacher" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->Nama->getPlaceHolder()) ?>" value="<?php echo $teacher->Nama->EditValue ?>"<?php echo $teacher->Nama->EditAttributes() ?>>
</span>
<?php echo $teacher->Nama->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Gelar->Visible) { // Gelar ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_Gelar" class="form-group">
		<label id="elh_teacher_Gelar" for="x_Gelar" class="col-sm-2 control-label ewLabel"><?php echo $teacher->Gelar->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->Gelar->CellAttributes() ?>>
<span id="el_teacher_Gelar">
<input type="text" data-table="teacher" data-field="x_Gelar" data-page="1" name="x_Gelar" id="x_Gelar" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($teacher->Gelar->getPlaceHolder()) ?>" value="<?php echo $teacher->Gelar->EditValue ?>"<?php echo $teacher->Gelar->EditAttributes() ?>>
</span>
<?php echo $teacher->Gelar->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Gelar">
		<td><span id="elh_teacher_Gelar"><?php echo $teacher->Gelar->FldCaption() ?></span></td>
		<td<?php echo $teacher->Gelar->CellAttributes() ?>>
<span id="el_teacher_Gelar">
<input type="text" data-table="teacher" data-field="x_Gelar" data-page="1" name="x_Gelar" id="x_Gelar" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($teacher->Gelar->getPlaceHolder()) ?>" value="<?php echo $teacher->Gelar->EditValue ?>"<?php echo $teacher->Gelar->EditAttributes() ?>>
</span>
<?php echo $teacher->Gelar->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->LevelID->Visible) { // LevelID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_LevelID" class="form-group">
		<label id="elh_teacher_LevelID" for="x_LevelID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->LevelID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->LevelID->CellAttributes() ?>>
<span id="el_teacher_LevelID">
<input type="text" data-table="teacher" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($teacher->LevelID->getPlaceHolder()) ?>" value="<?php echo $teacher->LevelID->EditValue ?>"<?php echo $teacher->LevelID->EditAttributes() ?>>
</span>
<?php echo $teacher->LevelID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_LevelID">
		<td><span id="elh_teacher_LevelID"><?php echo $teacher->LevelID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $teacher->LevelID->CellAttributes() ?>>
<span id="el_teacher_LevelID">
<input type="text" data-table="teacher" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($teacher->LevelID->getPlaceHolder()) ?>" value="<?php echo $teacher->LevelID->EditValue ?>"<?php echo $teacher->LevelID->EditAttributes() ?>>
</span>
<?php echo $teacher->LevelID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Password->Visible) { // Password ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_Password" class="form-group">
		<label id="elh_teacher_Password" for="x_Password" class="col-sm-2 control-label ewLabel"><?php echo $teacher->Password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->Password->CellAttributes() ?>>
<span id="el_teacher_Password">
<input type="password" data-field="x_Password" name="x_Password" id="x_Password" value="<?php echo $teacher->Password->EditValue ?>" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->Password->getPlaceHolder()) ?>"<?php echo $teacher->Password->EditAttributes() ?>>
</span>
<?php echo $teacher->Password->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Password">
		<td><span id="elh_teacher_Password"><?php echo $teacher->Password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $teacher->Password->CellAttributes() ?>>
<span id="el_teacher_Password">
<input type="password" data-field="x_Password" name="x_Password" id="x_Password" value="<?php echo $teacher->Password->EditValue ?>" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->Password->getPlaceHolder()) ?>"<?php echo $teacher->Password->EditAttributes() ?>>
</span>
<?php echo $teacher->Password->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->AliasCode->Visible) { // AliasCode ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_AliasCode" class="form-group">
		<label id="elh_teacher_AliasCode" for="x_AliasCode" class="col-sm-2 control-label ewLabel"><?php echo $teacher->AliasCode->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->AliasCode->CellAttributes() ?>>
<span id="el_teacher_AliasCode">
<input type="text" data-table="teacher" data-field="x_AliasCode" data-page="1" name="x_AliasCode" id="x_AliasCode" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->AliasCode->getPlaceHolder()) ?>" value="<?php echo $teacher->AliasCode->EditValue ?>"<?php echo $teacher->AliasCode->EditAttributes() ?>>
</span>
<?php echo $teacher->AliasCode->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_AliasCode">
		<td><span id="elh_teacher_AliasCode"><?php echo $teacher->AliasCode->FldCaption() ?></span></td>
		<td<?php echo $teacher->AliasCode->CellAttributes() ?>>
<span id="el_teacher_AliasCode">
<input type="text" data-table="teacher" data-field="x_AliasCode" data-page="1" name="x_AliasCode" id="x_AliasCode" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->AliasCode->getPlaceHolder()) ?>" value="<?php echo $teacher->AliasCode->EditValue ?>"<?php echo $teacher->AliasCode->EditAttributes() ?>>
</span>
<?php echo $teacher->AliasCode->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $teacher_edit->MultiPages->PageStyle("2") ?>" id="tab_teacher2">
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_teacheredit2" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($teacher->KTP->Visible) { // KTP ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_KTP" class="form-group">
		<label id="elh_teacher_KTP" for="x_KTP" class="col-sm-2 control-label ewLabel"><?php echo $teacher->KTP->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->KTP->CellAttributes() ?>>
<span id="el_teacher_KTP">
<input type="text" data-table="teacher" data-field="x_KTP" data-page="2" name="x_KTP" id="x_KTP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->KTP->getPlaceHolder()) ?>" value="<?php echo $teacher->KTP->EditValue ?>"<?php echo $teacher->KTP->EditAttributes() ?>>
</span>
<?php echo $teacher->KTP->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KTP">
		<td><span id="elh_teacher_KTP"><?php echo $teacher->KTP->FldCaption() ?></span></td>
		<td<?php echo $teacher->KTP->CellAttributes() ?>>
<span id="el_teacher_KTP">
<input type="text" data-table="teacher" data-field="x_KTP" data-page="2" name="x_KTP" id="x_KTP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->KTP->getPlaceHolder()) ?>" value="<?php echo $teacher->KTP->EditValue ?>"<?php echo $teacher->KTP->EditAttributes() ?>>
</span>
<?php echo $teacher->KTP->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->TempatLahir->Visible) { // TempatLahir ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_TempatLahir" class="form-group">
		<label id="elh_teacher_TempatLahir" class="col-sm-2 control-label ewLabel"><?php echo $teacher->TempatLahir->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->TempatLahir->CellAttributes() ?>>
<span id="el_teacher_TempatLahir">
<?php
$wrkonchange = trim(" " . @$teacher->TempatLahir->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$teacher->TempatLahir->EditAttrs["onchange"] = "";
?>
<span id="as_x_TempatLahir" style="white-space: nowrap; z-index: 8910">
	<input type="text" name="sv_x_TempatLahir" id="sv_x_TempatLahir" value="<?php echo $teacher->TempatLahir->EditValue ?>" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->TempatLahir->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($teacher->TempatLahir->getPlaceHolder()) ?>"<?php echo $teacher->TempatLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="teacher" data-field="x_TempatLahir" data-page="2" data-value-separator="<?php echo $teacher->TempatLahir->DisplayValueSeparatorAttribute() ?>" name="x_TempatLahir" id="x_TempatLahir" value="<?php echo ew_HtmlEncode($teacher->TempatLahir->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TempatLahir" id="q_x_TempatLahir" value="<?php echo $teacher->TempatLahir->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fteacheredit.CreateAutoSuggest({"id":"x_TempatLahir","forceSelect":false});
</script>
</span>
<?php echo $teacher->TempatLahir->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TempatLahir">
		<td><span id="elh_teacher_TempatLahir"><?php echo $teacher->TempatLahir->FldCaption() ?></span></td>
		<td<?php echo $teacher->TempatLahir->CellAttributes() ?>>
<span id="el_teacher_TempatLahir">
<?php
$wrkonchange = trim(" " . @$teacher->TempatLahir->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$teacher->TempatLahir->EditAttrs["onchange"] = "";
?>
<span id="as_x_TempatLahir" style="white-space: nowrap; z-index: 8910">
	<input type="text" name="sv_x_TempatLahir" id="sv_x_TempatLahir" value="<?php echo $teacher->TempatLahir->EditValue ?>" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->TempatLahir->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($teacher->TempatLahir->getPlaceHolder()) ?>"<?php echo $teacher->TempatLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="teacher" data-field="x_TempatLahir" data-page="2" data-value-separator="<?php echo $teacher->TempatLahir->DisplayValueSeparatorAttribute() ?>" name="x_TempatLahir" id="x_TempatLahir" value="<?php echo ew_HtmlEncode($teacher->TempatLahir->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TempatLahir" id="q_x_TempatLahir" value="<?php echo $teacher->TempatLahir->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fteacheredit.CreateAutoSuggest({"id":"x_TempatLahir","forceSelect":false});
</script>
</span>
<?php echo $teacher->TempatLahir->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->TanggalLahir->Visible) { // TanggalLahir ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_TanggalLahir" class="form-group">
		<label id="elh_teacher_TanggalLahir" for="x_TanggalLahir" class="col-sm-2 control-label ewLabel"><?php echo $teacher->TanggalLahir->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->TanggalLahir->CellAttributes() ?>>
<span id="el_teacher_TanggalLahir">
<input type="text" data-table="teacher" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($teacher->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $teacher->TanggalLahir->EditValue ?>"<?php echo $teacher->TanggalLahir->EditAttributes() ?>>
<?php if (!$teacher->TanggalLahir->ReadOnly && !$teacher->TanggalLahir->Disabled && !isset($teacher->TanggalLahir->EditAttrs["readonly"]) && !isset($teacher->TanggalLahir->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fteacheredit", "x_TanggalLahir", 0);
</script>
<?php } ?>
</span>
<?php echo $teacher->TanggalLahir->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanggalLahir">
		<td><span id="elh_teacher_TanggalLahir"><?php echo $teacher->TanggalLahir->FldCaption() ?></span></td>
		<td<?php echo $teacher->TanggalLahir->CellAttributes() ?>>
<span id="el_teacher_TanggalLahir">
<input type="text" data-table="teacher" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($teacher->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $teacher->TanggalLahir->EditValue ?>"<?php echo $teacher->TanggalLahir->EditAttributes() ?>>
<?php if (!$teacher->TanggalLahir->ReadOnly && !$teacher->TanggalLahir->Disabled && !isset($teacher->TanggalLahir->EditAttrs["readonly"]) && !isset($teacher->TanggalLahir->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fteacheredit", "x_TanggalLahir", 0);
</script>
<?php } ?>
</span>
<?php echo $teacher->TanggalLahir->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->AgamaID->Visible) { // AgamaID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_AgamaID" class="form-group">
		<label id="elh_teacher_AgamaID" for="x_AgamaID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->AgamaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->AgamaID->CellAttributes() ?>>
<span id="el_teacher_AgamaID">
<select data-table="teacher" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $teacher->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $teacher->AgamaID->EditAttributes() ?>>
<?php echo $teacher->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $teacher->AgamaID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->AgamaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_AgamaID">
		<td><span id="elh_teacher_AgamaID"><?php echo $teacher->AgamaID->FldCaption() ?></span></td>
		<td<?php echo $teacher->AgamaID->CellAttributes() ?>>
<span id="el_teacher_AgamaID">
<select data-table="teacher" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $teacher->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $teacher->AgamaID->EditAttributes() ?>>
<?php echo $teacher->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $teacher->AgamaID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->AgamaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->KelaminID->Visible) { // KelaminID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_KelaminID" class="form-group">
		<label id="elh_teacher_KelaminID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->KelaminID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->KelaminID->CellAttributes() ?>>
<span id="el_teacher_KelaminID">
<div id="tp_x_KelaminID" class="ewTemplate"><input type="radio" data-table="teacher" data-field="x_KelaminID" data-page="2" data-value-separator="<?php echo $teacher->KelaminID->DisplayValueSeparatorAttribute() ?>" name="x_KelaminID" id="x_KelaminID" value="{value}"<?php echo $teacher->KelaminID->EditAttributes() ?>></div>
<div id="dsl_x_KelaminID" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->KelaminID->RadioButtonListHtml(FALSE, "x_KelaminID", 2) ?>
</div></div>
<input type="hidden" name="s_x_KelaminID" id="s_x_KelaminID" value="<?php echo $teacher->KelaminID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->KelaminID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KelaminID">
		<td><span id="elh_teacher_KelaminID"><?php echo $teacher->KelaminID->FldCaption() ?></span></td>
		<td<?php echo $teacher->KelaminID->CellAttributes() ?>>
<span id="el_teacher_KelaminID">
<div id="tp_x_KelaminID" class="ewTemplate"><input type="radio" data-table="teacher" data-field="x_KelaminID" data-page="2" data-value-separator="<?php echo $teacher->KelaminID->DisplayValueSeparatorAttribute() ?>" name="x_KelaminID" id="x_KelaminID" value="{value}"<?php echo $teacher->KelaminID->EditAttributes() ?>></div>
<div id="dsl_x_KelaminID" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->KelaminID->RadioButtonListHtml(FALSE, "x_KelaminID", 2) ?>
</div></div>
<input type="hidden" name="s_x_KelaminID" id="s_x_KelaminID" value="<?php echo $teacher->KelaminID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->KelaminID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Telephone->Visible) { // Telephone ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_Telephone" class="form-group">
		<label id="elh_teacher_Telephone" for="x_Telephone" class="col-sm-2 control-label ewLabel"><?php echo $teacher->Telephone->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->Telephone->CellAttributes() ?>>
<span id="el_teacher_Telephone">
<input type="text" data-table="teacher" data-field="x_Telephone" data-page="2" name="x_Telephone" id="x_Telephone" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->Telephone->getPlaceHolder()) ?>" value="<?php echo $teacher->Telephone->EditValue ?>"<?php echo $teacher->Telephone->EditAttributes() ?>>
</span>
<?php echo $teacher->Telephone->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Telephone">
		<td><span id="elh_teacher_Telephone"><?php echo $teacher->Telephone->FldCaption() ?></span></td>
		<td<?php echo $teacher->Telephone->CellAttributes() ?>>
<span id="el_teacher_Telephone">
<input type="text" data-table="teacher" data-field="x_Telephone" data-page="2" name="x_Telephone" id="x_Telephone" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->Telephone->getPlaceHolder()) ?>" value="<?php echo $teacher->Telephone->EditValue ?>"<?php echo $teacher->Telephone->EditAttributes() ?>>
</span>
<?php echo $teacher->Telephone->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->_Email->Visible) { // Email ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r__Email" class="form-group">
		<label id="elh_teacher__Email" for="x__Email" class="col-sm-2 control-label ewLabel"><?php echo $teacher->_Email->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->_Email->CellAttributes() ?>>
<span id="el_teacher__Email">
<input type="text" data-table="teacher" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($teacher->_Email->getPlaceHolder()) ?>" value="<?php echo $teacher->_Email->EditValue ?>"<?php echo $teacher->_Email->EditAttributes() ?>>
</span>
<?php echo $teacher->_Email->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r__Email">
		<td><span id="elh_teacher__Email"><?php echo $teacher->_Email->FldCaption() ?></span></td>
		<td<?php echo $teacher->_Email->CellAttributes() ?>>
<span id="el_teacher__Email">
<input type="text" data-table="teacher" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($teacher->_Email->getPlaceHolder()) ?>" value="<?php echo $teacher->_Email->EditValue ?>"<?php echo $teacher->_Email->EditAttributes() ?>>
</span>
<?php echo $teacher->_Email->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Alamat->Visible) { // Alamat ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_Alamat" class="form-group">
		<label id="elh_teacher_Alamat" for="x_Alamat" class="col-sm-2 control-label ewLabel"><?php echo $teacher->Alamat->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->Alamat->CellAttributes() ?>>
<span id="el_teacher_Alamat">
<textarea data-table="teacher" data-field="x_Alamat" data-page="2" name="x_Alamat" id="x_Alamat" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($teacher->Alamat->getPlaceHolder()) ?>"<?php echo $teacher->Alamat->EditAttributes() ?>><?php echo $teacher->Alamat->EditValue ?></textarea>
</span>
<?php echo $teacher->Alamat->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Alamat">
		<td><span id="elh_teacher_Alamat"><?php echo $teacher->Alamat->FldCaption() ?></span></td>
		<td<?php echo $teacher->Alamat->CellAttributes() ?>>
<span id="el_teacher_Alamat">
<textarea data-table="teacher" data-field="x_Alamat" data-page="2" name="x_Alamat" id="x_Alamat" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($teacher->Alamat->getPlaceHolder()) ?>"<?php echo $teacher->Alamat->EditAttributes() ?>><?php echo $teacher->Alamat->EditValue ?></textarea>
</span>
<?php echo $teacher->Alamat->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->KodePos->Visible) { // KodePos ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_KodePos" class="form-group">
		<label id="elh_teacher_KodePos" for="x_KodePos" class="col-sm-2 control-label ewLabel"><?php echo $teacher->KodePos->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->KodePos->CellAttributes() ?>>
<span id="el_teacher_KodePos">
<input type="text" data-table="teacher" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->KodePos->getPlaceHolder()) ?>" value="<?php echo $teacher->KodePos->EditValue ?>"<?php echo $teacher->KodePos->EditAttributes() ?>>
</span>
<?php echo $teacher->KodePos->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodePos">
		<td><span id="elh_teacher_KodePos"><?php echo $teacher->KodePos->FldCaption() ?></span></td>
		<td<?php echo $teacher->KodePos->CellAttributes() ?>>
<span id="el_teacher_KodePos">
<input type="text" data-table="teacher" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->KodePos->getPlaceHolder()) ?>" value="<?php echo $teacher->KodePos->EditValue ?>"<?php echo $teacher->KodePos->EditAttributes() ?>>
</span>
<?php echo $teacher->KodePos->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->ProvinsiID->Visible) { // ProvinsiID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_ProvinsiID" class="form-group">
		<label id="elh_teacher_ProvinsiID" for="x_ProvinsiID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->ProvinsiID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->ProvinsiID->CellAttributes() ?>>
<span id="el_teacher_ProvinsiID">
<?php $teacher->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $teacher->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $teacher->ProvinsiID->EditAttributes() ?>>
<?php echo $teacher->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $teacher->ProvinsiID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->ProvinsiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiID">
		<td><span id="elh_teacher_ProvinsiID"><?php echo $teacher->ProvinsiID->FldCaption() ?></span></td>
		<td<?php echo $teacher->ProvinsiID->CellAttributes() ?>>
<span id="el_teacher_ProvinsiID">
<?php $teacher->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $teacher->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $teacher->ProvinsiID->EditAttributes() ?>>
<?php echo $teacher->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $teacher->ProvinsiID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->ProvinsiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->KabupatenKotaID->Visible) { // KabupatenKotaID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_KabupatenKotaID" class="form-group">
		<label id="elh_teacher_KabupatenKotaID" for="x_KabupatenKotaID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->KabupatenKotaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->KabupatenKotaID->CellAttributes() ?>>
<span id="el_teacher_KabupatenKotaID">
<?php $teacher->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $teacher->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $teacher->KabupatenKotaID->EditAttributes() ?>>
<?php echo $teacher->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $teacher->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->KabupatenKotaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenKotaID">
		<td><span id="elh_teacher_KabupatenKotaID"><?php echo $teacher->KabupatenKotaID->FldCaption() ?></span></td>
		<td<?php echo $teacher->KabupatenKotaID->CellAttributes() ?>>
<span id="el_teacher_KabupatenKotaID">
<?php $teacher->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $teacher->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $teacher->KabupatenKotaID->EditAttributes() ?>>
<?php echo $teacher->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $teacher->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->KabupatenKotaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->KecamatanID->Visible) { // KecamatanID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_KecamatanID" class="form-group">
		<label id="elh_teacher_KecamatanID" for="x_KecamatanID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->KecamatanID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->KecamatanID->CellAttributes() ?>>
<span id="el_teacher_KecamatanID">
<?php $teacher->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $teacher->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $teacher->KecamatanID->EditAttributes() ?>>
<?php echo $teacher->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $teacher->KecamatanID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->KecamatanID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanID">
		<td><span id="elh_teacher_KecamatanID"><?php echo $teacher->KecamatanID->FldCaption() ?></span></td>
		<td<?php echo $teacher->KecamatanID->CellAttributes() ?>>
<span id="el_teacher_KecamatanID">
<?php $teacher->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $teacher->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $teacher->KecamatanID->EditAttributes() ?>>
<?php echo $teacher->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $teacher->KecamatanID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->KecamatanID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->DesaID->Visible) { // DesaID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_DesaID" class="form-group">
		<label id="elh_teacher_DesaID" for="x_DesaID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->DesaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->DesaID->CellAttributes() ?>>
<span id="el_teacher_DesaID">
<select data-table="teacher" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $teacher->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $teacher->DesaID->EditAttributes() ?>>
<?php echo $teacher->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $teacher->DesaID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->DesaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_DesaID">
		<td><span id="elh_teacher_DesaID"><?php echo $teacher->DesaID->FldCaption() ?></span></td>
		<td<?php echo $teacher->DesaID->CellAttributes() ?>>
<span id="el_teacher_DesaID">
<select data-table="teacher" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $teacher->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $teacher->DesaID->EditAttributes() ?>>
<?php echo $teacher->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $teacher->DesaID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->DesaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $teacher_edit->MultiPages->PageStyle("3") ?>" id="tab_teacher3">
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_teacheredit3" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($teacher->InstitusiInduk->Visible) { // InstitusiInduk ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_InstitusiInduk" class="form-group">
		<label id="elh_teacher_InstitusiInduk" for="x_InstitusiInduk" class="col-sm-2 control-label ewLabel"><?php echo $teacher->InstitusiInduk->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->InstitusiInduk->CellAttributes() ?>>
<span id="el_teacher_InstitusiInduk">
<select data-table="teacher" data-field="x_InstitusiInduk" data-page="3" data-value-separator="<?php echo $teacher->InstitusiInduk->DisplayValueSeparatorAttribute() ?>" id="x_InstitusiInduk" name="x_InstitusiInduk"<?php echo $teacher->InstitusiInduk->EditAttributes() ?>>
<?php echo $teacher->InstitusiInduk->SelectOptionListHtml("x_InstitusiInduk") ?>
</select>
<input type="hidden" name="s_x_InstitusiInduk" id="s_x_InstitusiInduk" value="<?php echo $teacher->InstitusiInduk->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->InstitusiInduk->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_InstitusiInduk">
		<td><span id="elh_teacher_InstitusiInduk"><?php echo $teacher->InstitusiInduk->FldCaption() ?></span></td>
		<td<?php echo $teacher->InstitusiInduk->CellAttributes() ?>>
<span id="el_teacher_InstitusiInduk">
<select data-table="teacher" data-field="x_InstitusiInduk" data-page="3" data-value-separator="<?php echo $teacher->InstitusiInduk->DisplayValueSeparatorAttribute() ?>" id="x_InstitusiInduk" name="x_InstitusiInduk"<?php echo $teacher->InstitusiInduk->EditAttributes() ?>>
<?php echo $teacher->InstitusiInduk->SelectOptionListHtml("x_InstitusiInduk") ?>
</select>
<input type="hidden" name="s_x_InstitusiInduk" id="s_x_InstitusiInduk" value="<?php echo $teacher->InstitusiInduk->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->InstitusiInduk->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->IkatanID->Visible) { // IkatanID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_IkatanID" class="form-group">
		<label id="elh_teacher_IkatanID" for="x_IkatanID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->IkatanID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->IkatanID->CellAttributes() ?>>
<span id="el_teacher_IkatanID">
<select data-table="teacher" data-field="x_IkatanID" data-page="3" data-value-separator="<?php echo $teacher->IkatanID->DisplayValueSeparatorAttribute() ?>" id="x_IkatanID" name="x_IkatanID"<?php echo $teacher->IkatanID->EditAttributes() ?>>
<?php echo $teacher->IkatanID->SelectOptionListHtml("x_IkatanID") ?>
</select>
<input type="hidden" name="s_x_IkatanID" id="s_x_IkatanID" value="<?php echo $teacher->IkatanID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->IkatanID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_IkatanID">
		<td><span id="elh_teacher_IkatanID"><?php echo $teacher->IkatanID->FldCaption() ?></span></td>
		<td<?php echo $teacher->IkatanID->CellAttributes() ?>>
<span id="el_teacher_IkatanID">
<select data-table="teacher" data-field="x_IkatanID" data-page="3" data-value-separator="<?php echo $teacher->IkatanID->DisplayValueSeparatorAttribute() ?>" id="x_IkatanID" name="x_IkatanID"<?php echo $teacher->IkatanID->EditAttributes() ?>>
<?php echo $teacher->IkatanID->SelectOptionListHtml("x_IkatanID") ?>
</select>
<input type="hidden" name="s_x_IkatanID" id="s_x_IkatanID" value="<?php echo $teacher->IkatanID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->IkatanID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->GolonganID->Visible) { // GolonganID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_GolonganID" class="form-group">
		<label id="elh_teacher_GolonganID" for="x_GolonganID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->GolonganID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->GolonganID->CellAttributes() ?>>
<span id="el_teacher_GolonganID">
<select data-table="teacher" data-field="x_GolonganID" data-page="3" data-value-separator="<?php echo $teacher->GolonganID->DisplayValueSeparatorAttribute() ?>" id="x_GolonganID" name="x_GolonganID"<?php echo $teacher->GolonganID->EditAttributes() ?>>
<?php echo $teacher->GolonganID->SelectOptionListHtml("x_GolonganID") ?>
</select>
<input type="hidden" name="s_x_GolonganID" id="s_x_GolonganID" value="<?php echo $teacher->GolonganID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->GolonganID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_GolonganID">
		<td><span id="elh_teacher_GolonganID"><?php echo $teacher->GolonganID->FldCaption() ?></span></td>
		<td<?php echo $teacher->GolonganID->CellAttributes() ?>>
<span id="el_teacher_GolonganID">
<select data-table="teacher" data-field="x_GolonganID" data-page="3" data-value-separator="<?php echo $teacher->GolonganID->DisplayValueSeparatorAttribute() ?>" id="x_GolonganID" name="x_GolonganID"<?php echo $teacher->GolonganID->EditAttributes() ?>>
<?php echo $teacher->GolonganID->SelectOptionListHtml("x_GolonganID") ?>
</select>
<input type="hidden" name="s_x_GolonganID" id="s_x_GolonganID" value="<?php echo $teacher->GolonganID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->GolonganID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->StatusKerjaID->Visible) { // StatusKerjaID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_StatusKerjaID" class="form-group">
		<label id="elh_teacher_StatusKerjaID" for="x_StatusKerjaID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->StatusKerjaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->StatusKerjaID->CellAttributes() ?>>
<span id="el_teacher_StatusKerjaID">
<select data-table="teacher" data-field="x_StatusKerjaID" data-page="3" data-value-separator="<?php echo $teacher->StatusKerjaID->DisplayValueSeparatorAttribute() ?>" id="x_StatusKerjaID" name="x_StatusKerjaID"<?php echo $teacher->StatusKerjaID->EditAttributes() ?>>
<?php echo $teacher->StatusKerjaID->SelectOptionListHtml("x_StatusKerjaID") ?>
</select>
<input type="hidden" name="s_x_StatusKerjaID" id="s_x_StatusKerjaID" value="<?php echo $teacher->StatusKerjaID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->StatusKerjaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusKerjaID">
		<td><span id="elh_teacher_StatusKerjaID"><?php echo $teacher->StatusKerjaID->FldCaption() ?></span></td>
		<td<?php echo $teacher->StatusKerjaID->CellAttributes() ?>>
<span id="el_teacher_StatusKerjaID">
<select data-table="teacher" data-field="x_StatusKerjaID" data-page="3" data-value-separator="<?php echo $teacher->StatusKerjaID->DisplayValueSeparatorAttribute() ?>" id="x_StatusKerjaID" name="x_StatusKerjaID"<?php echo $teacher->StatusKerjaID->EditAttributes() ?>>
<?php echo $teacher->StatusKerjaID->SelectOptionListHtml("x_StatusKerjaID") ?>
</select>
<input type="hidden" name="s_x_StatusKerjaID" id="s_x_StatusKerjaID" value="<?php echo $teacher->StatusKerjaID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->StatusKerjaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->TglBekerja->Visible) { // TglBekerja ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_TglBekerja" class="form-group">
		<label id="elh_teacher_TglBekerja" for="x_TglBekerja" class="col-sm-2 control-label ewLabel"><?php echo $teacher->TglBekerja->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->TglBekerja->CellAttributes() ?>>
<span id="el_teacher_TglBekerja">
<input type="text" data-table="teacher" data-field="x_TglBekerja" data-page="3" name="x_TglBekerja" id="x_TglBekerja" placeholder="<?php echo ew_HtmlEncode($teacher->TglBekerja->getPlaceHolder()) ?>" value="<?php echo $teacher->TglBekerja->EditValue ?>"<?php echo $teacher->TglBekerja->EditAttributes() ?>>
<?php if (!$teacher->TglBekerja->ReadOnly && !$teacher->TglBekerja->Disabled && !isset($teacher->TglBekerja->EditAttrs["readonly"]) && !isset($teacher->TglBekerja->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fteacheredit", "x_TglBekerja", 0);
</script>
<?php } ?>
</span>
<?php echo $teacher->TglBekerja->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_TglBekerja">
		<td><span id="elh_teacher_TglBekerja"><?php echo $teacher->TglBekerja->FldCaption() ?></span></td>
		<td<?php echo $teacher->TglBekerja->CellAttributes() ?>>
<span id="el_teacher_TglBekerja">
<input type="text" data-table="teacher" data-field="x_TglBekerja" data-page="3" name="x_TglBekerja" id="x_TglBekerja" placeholder="<?php echo ew_HtmlEncode($teacher->TglBekerja->getPlaceHolder()) ?>" value="<?php echo $teacher->TglBekerja->EditValue ?>"<?php echo $teacher->TglBekerja->EditAttributes() ?>>
<?php if (!$teacher->TglBekerja->ReadOnly && !$teacher->TglBekerja->Disabled && !isset($teacher->TglBekerja->EditAttrs["readonly"]) && !isset($teacher->TglBekerja->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fteacheredit", "x_TglBekerja", 0);
</script>
<?php } ?>
</span>
<?php echo $teacher->TglBekerja->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Homebase->Visible) { // Homebase ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_Homebase" class="form-group">
		<label id="elh_teacher_Homebase" for="x_Homebase" class="col-sm-2 control-label ewLabel"><?php echo $teacher->Homebase->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->Homebase->CellAttributes() ?>>
<span id="el_teacher_Homebase">
<select data-table="teacher" data-field="x_Homebase" data-page="3" data-value-separator="<?php echo $teacher->Homebase->DisplayValueSeparatorAttribute() ?>" id="x_Homebase" name="x_Homebase"<?php echo $teacher->Homebase->EditAttributes() ?>>
<?php echo $teacher->Homebase->SelectOptionListHtml("x_Homebase") ?>
</select>
<input type="hidden" name="s_x_Homebase" id="s_x_Homebase" value="<?php echo $teacher->Homebase->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->Homebase->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Homebase">
		<td><span id="elh_teacher_Homebase"><?php echo $teacher->Homebase->FldCaption() ?></span></td>
		<td<?php echo $teacher->Homebase->CellAttributes() ?>>
<span id="el_teacher_Homebase">
<select data-table="teacher" data-field="x_Homebase" data-page="3" data-value-separator="<?php echo $teacher->Homebase->DisplayValueSeparatorAttribute() ?>" id="x_Homebase" name="x_Homebase"<?php echo $teacher->Homebase->EditAttributes() ?>>
<?php echo $teacher->Homebase->SelectOptionListHtml("x_Homebase") ?>
</select>
<input type="hidden" name="s_x_Homebase" id="s_x_Homebase" value="<?php echo $teacher->Homebase->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->Homebase->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label id="elh_teacher_ProdiID" class="col-sm-2 control-label ewLabel"><?php echo $teacher->ProdiID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->ProdiID->CellAttributes() ?>>
<span id="el_teacher_ProdiID">
<div id="tp_x_ProdiID" class="ewTemplate"><input type="checkbox" data-table="teacher" data-field="x_ProdiID" data-page="3" data-value-separator="<?php echo $teacher->ProdiID->DisplayValueSeparatorAttribute() ?>" name="x_ProdiID[]" id="x_ProdiID[]" value="{value}"<?php echo $teacher->ProdiID->EditAttributes() ?>></div>
<div id="dsl_x_ProdiID" data-repeatcolumn="3" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->ProdiID->CheckBoxListHtml(FALSE, "x_ProdiID[]", 3) ?>
</div></div>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $teacher->ProdiID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->ProdiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_teacher_ProdiID"><?php echo $teacher->ProdiID->FldCaption() ?></span></td>
		<td<?php echo $teacher->ProdiID->CellAttributes() ?>>
<span id="el_teacher_ProdiID">
<div id="tp_x_ProdiID" class="ewTemplate"><input type="checkbox" data-table="teacher" data-field="x_ProdiID" data-page="3" data-value-separator="<?php echo $teacher->ProdiID->DisplayValueSeparatorAttribute() ?>" name="x_ProdiID[]" id="x_ProdiID[]" value="{value}"<?php echo $teacher->ProdiID->EditAttributes() ?>></div>
<div id="dsl_x_ProdiID" data-repeatcolumn="3" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->ProdiID->CheckBoxListHtml(FALSE, "x_ProdiID[]", 3) ?>
</div></div>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $teacher->ProdiID->LookupFilterQuery() ?>">
</span>
<?php echo $teacher->ProdiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Keilmuan->Visible) { // Keilmuan ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_Keilmuan" class="form-group">
		<label id="elh_teacher_Keilmuan" for="x_Keilmuan" class="col-sm-2 control-label ewLabel"><?php echo $teacher->Keilmuan->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->Keilmuan->CellAttributes() ?>>
<span id="el_teacher_Keilmuan">
<input type="text" data-table="teacher" data-field="x_Keilmuan" data-page="3" name="x_Keilmuan" id="x_Keilmuan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->Keilmuan->getPlaceHolder()) ?>" value="<?php echo $teacher->Keilmuan->EditValue ?>"<?php echo $teacher->Keilmuan->EditAttributes() ?>>
</span>
<?php echo $teacher->Keilmuan->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Keilmuan">
		<td><span id="elh_teacher_Keilmuan"><?php echo $teacher->Keilmuan->FldCaption() ?></span></td>
		<td<?php echo $teacher->Keilmuan->CellAttributes() ?>>
<span id="el_teacher_Keilmuan">
<input type="text" data-table="teacher" data-field="x_Keilmuan" data-page="3" name="x_Keilmuan" id="x_Keilmuan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->Keilmuan->getPlaceHolder()) ?>" value="<?php echo $teacher->Keilmuan->EditValue ?>"<?php echo $teacher->Keilmuan->EditAttributes() ?>>
</span>
<?php echo $teacher->Keilmuan->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->LulusanPT->Visible) { // LulusanPT ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_LulusanPT" class="form-group">
		<label id="elh_teacher_LulusanPT" for="x_LulusanPT" class="col-sm-2 control-label ewLabel"><?php echo $teacher->LulusanPT->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->LulusanPT->CellAttributes() ?>>
<span id="el_teacher_LulusanPT">
<input type="text" data-table="teacher" data-field="x_LulusanPT" data-page="3" name="x_LulusanPT" id="x_LulusanPT" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->LulusanPT->getPlaceHolder()) ?>" value="<?php echo $teacher->LulusanPT->EditValue ?>"<?php echo $teacher->LulusanPT->EditAttributes() ?>>
</span>
<?php echo $teacher->LulusanPT->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_LulusanPT">
		<td><span id="elh_teacher_LulusanPT"><?php echo $teacher->LulusanPT->FldCaption() ?></span></td>
		<td<?php echo $teacher->LulusanPT->CellAttributes() ?>>
<span id="el_teacher_LulusanPT">
<input type="text" data-table="teacher" data-field="x_LulusanPT" data-page="3" name="x_LulusanPT" id="x_LulusanPT" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->LulusanPT->getPlaceHolder()) ?>" value="<?php echo $teacher->LulusanPT->EditValue ?>"<?php echo $teacher->LulusanPT->EditAttributes() ?>>
</span>
<?php echo $teacher->LulusanPT->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $teacher_edit->MultiPages->PageStyle("4") ?>" id="tab_teacher4">
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_teacheredit4" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($teacher->NamaBank->Visible) { // NamaBank ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_NamaBank" class="form-group">
		<label id="elh_teacher_NamaBank" for="x_NamaBank" class="col-sm-2 control-label ewLabel"><?php echo $teacher->NamaBank->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->NamaBank->CellAttributes() ?>>
<span id="el_teacher_NamaBank">
<input type="text" data-table="teacher" data-field="x_NamaBank" data-page="4" name="x_NamaBank" id="x_NamaBank" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->NamaBank->getPlaceHolder()) ?>" value="<?php echo $teacher->NamaBank->EditValue ?>"<?php echo $teacher->NamaBank->EditAttributes() ?>>
</span>
<?php echo $teacher->NamaBank->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NamaBank">
		<td><span id="elh_teacher_NamaBank"><?php echo $teacher->NamaBank->FldCaption() ?></span></td>
		<td<?php echo $teacher->NamaBank->CellAttributes() ?>>
<span id="el_teacher_NamaBank">
<input type="text" data-table="teacher" data-field="x_NamaBank" data-page="4" name="x_NamaBank" id="x_NamaBank" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->NamaBank->getPlaceHolder()) ?>" value="<?php echo $teacher->NamaBank->EditValue ?>"<?php echo $teacher->NamaBank->EditAttributes() ?>>
</span>
<?php echo $teacher->NamaBank->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->NamaAkun->Visible) { // NamaAkun ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_NamaAkun" class="form-group">
		<label id="elh_teacher_NamaAkun" for="x_NamaAkun" class="col-sm-2 control-label ewLabel"><?php echo $teacher->NamaAkun->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->NamaAkun->CellAttributes() ?>>
<span id="el_teacher_NamaAkun">
<input type="text" data-table="teacher" data-field="x_NamaAkun" data-page="4" name="x_NamaAkun" id="x_NamaAkun" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->NamaAkun->getPlaceHolder()) ?>" value="<?php echo $teacher->NamaAkun->EditValue ?>"<?php echo $teacher->NamaAkun->EditAttributes() ?>>
</span>
<?php echo $teacher->NamaAkun->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NamaAkun">
		<td><span id="elh_teacher_NamaAkun"><?php echo $teacher->NamaAkun->FldCaption() ?></span></td>
		<td<?php echo $teacher->NamaAkun->CellAttributes() ?>>
<span id="el_teacher_NamaAkun">
<input type="text" data-table="teacher" data-field="x_NamaAkun" data-page="4" name="x_NamaAkun" id="x_NamaAkun" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->NamaAkun->getPlaceHolder()) ?>" value="<?php echo $teacher->NamaAkun->EditValue ?>"<?php echo $teacher->NamaAkun->EditAttributes() ?>>
</span>
<?php echo $teacher->NamaAkun->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->NomerAkun->Visible) { // NomerAkun ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_NomerAkun" class="form-group">
		<label id="elh_teacher_NomerAkun" for="x_NomerAkun" class="col-sm-2 control-label ewLabel"><?php echo $teacher->NomerAkun->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->NomerAkun->CellAttributes() ?>>
<span id="el_teacher_NomerAkun">
<input type="text" data-table="teacher" data-field="x_NomerAkun" data-page="4" name="x_NomerAkun" id="x_NomerAkun" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->NomerAkun->getPlaceHolder()) ?>" value="<?php echo $teacher->NomerAkun->EditValue ?>"<?php echo $teacher->NomerAkun->EditAttributes() ?>>
</span>
<?php echo $teacher->NomerAkun->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NomerAkun">
		<td><span id="elh_teacher_NomerAkun"><?php echo $teacher->NomerAkun->FldCaption() ?></span></td>
		<td<?php echo $teacher->NomerAkun->CellAttributes() ?>>
<span id="el_teacher_NomerAkun">
<input type="text" data-table="teacher" data-field="x_NomerAkun" data-page="4" name="x_NomerAkun" id="x_NomerAkun" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->NomerAkun->getPlaceHolder()) ?>" value="<?php echo $teacher->NomerAkun->EditValue ?>"<?php echo $teacher->NomerAkun->EditAttributes() ?>>
</span>
<?php echo $teacher->NomerAkun->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $teacher_edit->MultiPages->PageStyle("5") ?>" id="tab_teacher5">
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_teacheredit5" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($teacher->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_teacher_NA" class="col-sm-2 control-label ewLabel"><?php echo $teacher->NA->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $teacher->NA->CellAttributes() ?>>
<span id="el_teacher_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="teacher" data-field="x_NA" data-page="5" data-value-separator="<?php echo $teacher->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $teacher->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->NA->RadioButtonListHtml(FALSE, "x_NA", 5) ?>
</div></div>
</span>
<?php echo $teacher->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_teacher_NA"><?php echo $teacher->NA->FldCaption() ?></span></td>
		<td<?php echo $teacher->NA->CellAttributes() ?>>
<span id="el_teacher_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="teacher" data-field="x_NA" data-page="5" data-value-separator="<?php echo $teacher->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $teacher->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->NA->RadioButtonListHtml(FALSE, "x_NA", 5) ?>
</div></div>
</span>
<?php echo $teacher->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $teacher_edit->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
	</div>
</div>
</div>
<?php if (!$teacher_edit->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $teacher_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fteacheredit.Init();
</script>
<?php
$teacher_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$teacher_edit->Page_Terminate();
?>
