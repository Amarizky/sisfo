<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "identitasinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$identitas_add = NULL; // Initialize page object first

class cidentitas_add extends cidentitas {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'identitas';

	// Page object name
	var $PageObjName = 'identitas_add';

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

		// Table object (identitas)
		if (!isset($GLOBALS["identitas"]) || get_class($GLOBALS["identitas"]) == "cidentitas") {
			$GLOBALS["identitas"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["identitas"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'identitas', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("identitaslist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->Kode->SetVisibility();
		$this->KodeHukum->SetVisibility();
		$this->Nama->SetVisibility();
		$this->TglMulai->SetVisibility();
		$this->Alamat1->SetVisibility();
		$this->Alamat2->SetVisibility();
		$this->Kota->SetVisibility();
		$this->KodePos->SetVisibility();
		$this->Telepon->SetVisibility();
		$this->Fax->SetVisibility();
		$this->_Email->SetVisibility();
		$this->Website->SetVisibility();
		$this->NoAkta->SetVisibility();
		$this->TglAkta->SetVisibility();
		$this->NoSah->SetVisibility();
		$this->TglSah->SetVisibility();
		$this->Logo->SetVisibility();
		$this->StartNoIdentitas->SetVisibility();
		$this->NoIdentitas->SetVisibility();
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
		global $EW_EXPORT, $identitas;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($identitas);
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

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = FALSE;
			$this->LoadFormValues(); // Load form values
		} else { // Not post back
			$this->CopyRecord = FALSE;
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
					$this->Page_Terminate("identitaslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "identitaslist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "identitasview.php")
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
		$this->Kode->CurrentValue = NULL;
		$this->Kode->OldValue = $this->Kode->CurrentValue;
		$this->KodeHukum->CurrentValue = NULL;
		$this->KodeHukum->OldValue = $this->KodeHukum->CurrentValue;
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->TglMulai->CurrentValue = "0000-00-00";
		$this->Alamat1->CurrentValue = NULL;
		$this->Alamat1->OldValue = $this->Alamat1->CurrentValue;
		$this->Alamat2->CurrentValue = NULL;
		$this->Alamat2->OldValue = $this->Alamat2->CurrentValue;
		$this->Kota->CurrentValue = NULL;
		$this->Kota->OldValue = $this->Kota->CurrentValue;
		$this->KodePos->CurrentValue = NULL;
		$this->KodePos->OldValue = $this->KodePos->CurrentValue;
		$this->Telepon->CurrentValue = NULL;
		$this->Telepon->OldValue = $this->Telepon->CurrentValue;
		$this->Fax->CurrentValue = NULL;
		$this->Fax->OldValue = $this->Fax->CurrentValue;
		$this->_Email->CurrentValue = NULL;
		$this->_Email->OldValue = $this->_Email->CurrentValue;
		$this->Website->CurrentValue = NULL;
		$this->Website->OldValue = $this->Website->CurrentValue;
		$this->NoAkta->CurrentValue = NULL;
		$this->NoAkta->OldValue = $this->NoAkta->CurrentValue;
		$this->TglAkta->CurrentValue = NULL;
		$this->TglAkta->OldValue = $this->TglAkta->CurrentValue;
		$this->NoSah->CurrentValue = NULL;
		$this->NoSah->OldValue = $this->NoSah->CurrentValue;
		$this->TglSah->CurrentValue = NULL;
		$this->TglSah->OldValue = $this->TglSah->CurrentValue;
		$this->Logo->CurrentValue = NULL;
		$this->Logo->OldValue = $this->Logo->CurrentValue;
		$this->StartNoIdentitas->CurrentValue = 0;
		$this->NoIdentitas->CurrentValue = 0;
		$this->NA->CurrentValue = "N";
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Kode->FldIsDetailKey) {
			$this->Kode->setFormValue($objForm->GetValue("x_Kode"));
		}
		if (!$this->KodeHukum->FldIsDetailKey) {
			$this->KodeHukum->setFormValue($objForm->GetValue("x_KodeHukum"));
		}
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->TglMulai->FldIsDetailKey) {
			$this->TglMulai->setFormValue($objForm->GetValue("x_TglMulai"));
			$this->TglMulai->CurrentValue = ew_UnFormatDateTime($this->TglMulai->CurrentValue, 0);
		}
		if (!$this->Alamat1->FldIsDetailKey) {
			$this->Alamat1->setFormValue($objForm->GetValue("x_Alamat1"));
		}
		if (!$this->Alamat2->FldIsDetailKey) {
			$this->Alamat2->setFormValue($objForm->GetValue("x_Alamat2"));
		}
		if (!$this->Kota->FldIsDetailKey) {
			$this->Kota->setFormValue($objForm->GetValue("x_Kota"));
		}
		if (!$this->KodePos->FldIsDetailKey) {
			$this->KodePos->setFormValue($objForm->GetValue("x_KodePos"));
		}
		if (!$this->Telepon->FldIsDetailKey) {
			$this->Telepon->setFormValue($objForm->GetValue("x_Telepon"));
		}
		if (!$this->Fax->FldIsDetailKey) {
			$this->Fax->setFormValue($objForm->GetValue("x_Fax"));
		}
		if (!$this->_Email->FldIsDetailKey) {
			$this->_Email->setFormValue($objForm->GetValue("x__Email"));
		}
		if (!$this->Website->FldIsDetailKey) {
			$this->Website->setFormValue($objForm->GetValue("x_Website"));
		}
		if (!$this->NoAkta->FldIsDetailKey) {
			$this->NoAkta->setFormValue($objForm->GetValue("x_NoAkta"));
		}
		if (!$this->TglAkta->FldIsDetailKey) {
			$this->TglAkta->setFormValue($objForm->GetValue("x_TglAkta"));
			$this->TglAkta->CurrentValue = ew_UnFormatDateTime($this->TglAkta->CurrentValue, 0);
		}
		if (!$this->NoSah->FldIsDetailKey) {
			$this->NoSah->setFormValue($objForm->GetValue("x_NoSah"));
		}
		if (!$this->TglSah->FldIsDetailKey) {
			$this->TglSah->setFormValue($objForm->GetValue("x_TglSah"));
			$this->TglSah->CurrentValue = ew_UnFormatDateTime($this->TglSah->CurrentValue, 0);
		}
		if (!$this->Logo->FldIsDetailKey) {
			$this->Logo->setFormValue($objForm->GetValue("x_Logo"));
		}
		if (!$this->StartNoIdentitas->FldIsDetailKey) {
			$this->StartNoIdentitas->setFormValue($objForm->GetValue("x_StartNoIdentitas"));
		}
		if (!$this->NoIdentitas->FldIsDetailKey) {
			$this->NoIdentitas->setFormValue($objForm->GetValue("x_NoIdentitas"));
		}
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->Kode->CurrentValue = $this->Kode->FormValue;
		$this->KodeHukum->CurrentValue = $this->KodeHukum->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->TglMulai->CurrentValue = $this->TglMulai->FormValue;
		$this->TglMulai->CurrentValue = ew_UnFormatDateTime($this->TglMulai->CurrentValue, 0);
		$this->Alamat1->CurrentValue = $this->Alamat1->FormValue;
		$this->Alamat2->CurrentValue = $this->Alamat2->FormValue;
		$this->Kota->CurrentValue = $this->Kota->FormValue;
		$this->KodePos->CurrentValue = $this->KodePos->FormValue;
		$this->Telepon->CurrentValue = $this->Telepon->FormValue;
		$this->Fax->CurrentValue = $this->Fax->FormValue;
		$this->_Email->CurrentValue = $this->_Email->FormValue;
		$this->Website->CurrentValue = $this->Website->FormValue;
		$this->NoAkta->CurrentValue = $this->NoAkta->FormValue;
		$this->TglAkta->CurrentValue = $this->TglAkta->FormValue;
		$this->TglAkta->CurrentValue = ew_UnFormatDateTime($this->TglAkta->CurrentValue, 0);
		$this->NoSah->CurrentValue = $this->NoSah->FormValue;
		$this->TglSah->CurrentValue = $this->TglSah->FormValue;
		$this->TglSah->CurrentValue = ew_UnFormatDateTime($this->TglSah->CurrentValue, 0);
		$this->Logo->CurrentValue = $this->Logo->FormValue;
		$this->StartNoIdentitas->CurrentValue = $this->StartNoIdentitas->FormValue;
		$this->NoIdentitas->CurrentValue = $this->NoIdentitas->FormValue;
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
		$this->Kode->setDbValue($rs->fields('Kode'));
		$this->KodeHukum->setDbValue($rs->fields('KodeHukum'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->TglMulai->setDbValue($rs->fields('TglMulai'));
		$this->Alamat1->setDbValue($rs->fields('Alamat1'));
		$this->Alamat2->setDbValue($rs->fields('Alamat2'));
		$this->Kota->setDbValue($rs->fields('Kota'));
		$this->KodePos->setDbValue($rs->fields('KodePos'));
		$this->Telepon->setDbValue($rs->fields('Telepon'));
		$this->Fax->setDbValue($rs->fields('Fax'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Website->setDbValue($rs->fields('Website'));
		$this->NoAkta->setDbValue($rs->fields('NoAkta'));
		$this->TglAkta->setDbValue($rs->fields('TglAkta'));
		$this->NoSah->setDbValue($rs->fields('NoSah'));
		$this->TglSah->setDbValue($rs->fields('TglSah'));
		$this->Logo->setDbValue($rs->fields('Logo'));
		$this->StartNoIdentitas->setDbValue($rs->fields('StartNoIdentitas'));
		$this->NoIdentitas->setDbValue($rs->fields('NoIdentitas'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Kode->DbValue = $row['Kode'];
		$this->KodeHukum->DbValue = $row['KodeHukum'];
		$this->Nama->DbValue = $row['Nama'];
		$this->TglMulai->DbValue = $row['TglMulai'];
		$this->Alamat1->DbValue = $row['Alamat1'];
		$this->Alamat2->DbValue = $row['Alamat2'];
		$this->Kota->DbValue = $row['Kota'];
		$this->KodePos->DbValue = $row['KodePos'];
		$this->Telepon->DbValue = $row['Telepon'];
		$this->Fax->DbValue = $row['Fax'];
		$this->_Email->DbValue = $row['Email'];
		$this->Website->DbValue = $row['Website'];
		$this->NoAkta->DbValue = $row['NoAkta'];
		$this->TglAkta->DbValue = $row['TglAkta'];
		$this->NoSah->DbValue = $row['NoSah'];
		$this->TglSah->DbValue = $row['TglSah'];
		$this->Logo->DbValue = $row['Logo'];
		$this->StartNoIdentitas->DbValue = $row['StartNoIdentitas'];
		$this->NoIdentitas->DbValue = $row['NoIdentitas'];
		$this->NA->DbValue = $row['NA'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;

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
		// Kode
		// KodeHukum
		// Nama
		// TglMulai
		// Alamat1
		// Alamat2
		// Kota
		// KodePos
		// Telepon
		// Fax
		// Email
		// Website
		// NoAkta
		// TglAkta
		// NoSah
		// TglSah
		// Logo
		// StartNoIdentitas
		// NoIdentitas
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// Kode
		$this->Kode->ViewValue = $this->Kode->CurrentValue;
		$this->Kode->ViewCustomAttributes = "";

		// KodeHukum
		$this->KodeHukum->ViewValue = $this->KodeHukum->CurrentValue;
		$this->KodeHukum->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// TglMulai
		$this->TglMulai->ViewValue = $this->TglMulai->CurrentValue;
		$this->TglMulai->ViewValue = ew_FormatDateTime($this->TglMulai->ViewValue, 0);
		$this->TglMulai->ViewCustomAttributes = "";

		// Alamat1
		$this->Alamat1->ViewValue = $this->Alamat1->CurrentValue;
		$this->Alamat1->ViewCustomAttributes = "";

		// Alamat2
		$this->Alamat2->ViewValue = $this->Alamat2->CurrentValue;
		$this->Alamat2->ViewCustomAttributes = "";

		// Kota
		$this->Kota->ViewValue = $this->Kota->CurrentValue;
		$this->Kota->ViewCustomAttributes = "";

		// KodePos
		$this->KodePos->ViewValue = $this->KodePos->CurrentValue;
		$this->KodePos->ViewCustomAttributes = "";

		// Telepon
		$this->Telepon->ViewValue = $this->Telepon->CurrentValue;
		$this->Telepon->ViewCustomAttributes = "";

		// Fax
		$this->Fax->ViewValue = $this->Fax->CurrentValue;
		$this->Fax->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// Website
		$this->Website->ViewValue = $this->Website->CurrentValue;
		$this->Website->ViewCustomAttributes = "";

		// NoAkta
		$this->NoAkta->ViewValue = $this->NoAkta->CurrentValue;
		$this->NoAkta->ViewCustomAttributes = "";

		// TglAkta
		$this->TglAkta->ViewValue = $this->TglAkta->CurrentValue;
		$this->TglAkta->ViewValue = ew_FormatDateTime($this->TglAkta->ViewValue, 0);
		$this->TglAkta->ViewCustomAttributes = "";

		// NoSah
		$this->NoSah->ViewValue = $this->NoSah->CurrentValue;
		$this->NoSah->ViewCustomAttributes = "";

		// TglSah
		$this->TglSah->ViewValue = $this->TglSah->CurrentValue;
		$this->TglSah->ViewValue = ew_FormatDateTime($this->TglSah->ViewValue, 0);
		$this->TglSah->ViewCustomAttributes = "";

		// Logo
		$this->Logo->ViewValue = $this->Logo->CurrentValue;
		$this->Logo->ViewCustomAttributes = "";

		// StartNoIdentitas
		$this->StartNoIdentitas->ViewValue = $this->StartNoIdentitas->CurrentValue;
		$this->StartNoIdentitas->ViewCustomAttributes = "";

		// NoIdentitas
		$this->NoIdentitas->ViewValue = $this->NoIdentitas->CurrentValue;
		$this->NoIdentitas->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

			// Kode
			$this->Kode->LinkCustomAttributes = "";
			$this->Kode->HrefValue = "";
			$this->Kode->TooltipValue = "";

			// KodeHukum
			$this->KodeHukum->LinkCustomAttributes = "";
			$this->KodeHukum->HrefValue = "";
			$this->KodeHukum->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// TglMulai
			$this->TglMulai->LinkCustomAttributes = "";
			$this->TglMulai->HrefValue = "";
			$this->TglMulai->TooltipValue = "";

			// Alamat1
			$this->Alamat1->LinkCustomAttributes = "";
			$this->Alamat1->HrefValue = "";
			$this->Alamat1->TooltipValue = "";

			// Alamat2
			$this->Alamat2->LinkCustomAttributes = "";
			$this->Alamat2->HrefValue = "";
			$this->Alamat2->TooltipValue = "";

			// Kota
			$this->Kota->LinkCustomAttributes = "";
			$this->Kota->HrefValue = "";
			$this->Kota->TooltipValue = "";

			// KodePos
			$this->KodePos->LinkCustomAttributes = "";
			$this->KodePos->HrefValue = "";
			$this->KodePos->TooltipValue = "";

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";
			$this->Telepon->TooltipValue = "";

			// Fax
			$this->Fax->LinkCustomAttributes = "";
			$this->Fax->HrefValue = "";
			$this->Fax->TooltipValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";

			// Website
			$this->Website->LinkCustomAttributes = "";
			$this->Website->HrefValue = "";
			$this->Website->TooltipValue = "";

			// NoAkta
			$this->NoAkta->LinkCustomAttributes = "";
			$this->NoAkta->HrefValue = "";
			$this->NoAkta->TooltipValue = "";

			// TglAkta
			$this->TglAkta->LinkCustomAttributes = "";
			$this->TglAkta->HrefValue = "";
			$this->TglAkta->TooltipValue = "";

			// NoSah
			$this->NoSah->LinkCustomAttributes = "";
			$this->NoSah->HrefValue = "";
			$this->NoSah->TooltipValue = "";

			// TglSah
			$this->TglSah->LinkCustomAttributes = "";
			$this->TglSah->HrefValue = "";
			$this->TglSah->TooltipValue = "";

			// Logo
			$this->Logo->LinkCustomAttributes = "";
			$this->Logo->HrefValue = "";
			$this->Logo->TooltipValue = "";

			// StartNoIdentitas
			$this->StartNoIdentitas->LinkCustomAttributes = "";
			$this->StartNoIdentitas->HrefValue = "";
			$this->StartNoIdentitas->TooltipValue = "";

			// NoIdentitas
			$this->NoIdentitas->LinkCustomAttributes = "";
			$this->NoIdentitas->HrefValue = "";
			$this->NoIdentitas->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// Kode
			$this->Kode->EditAttrs["class"] = "form-control";
			$this->Kode->EditCustomAttributes = "";
			$this->Kode->EditValue = ew_HtmlEncode($this->Kode->CurrentValue);
			$this->Kode->PlaceHolder = ew_RemoveHtml($this->Kode->FldCaption());

			// KodeHukum
			$this->KodeHukum->EditAttrs["class"] = "form-control";
			$this->KodeHukum->EditCustomAttributes = "";
			$this->KodeHukum->EditValue = ew_HtmlEncode($this->KodeHukum->CurrentValue);
			$this->KodeHukum->PlaceHolder = ew_RemoveHtml($this->KodeHukum->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// TglMulai
			$this->TglMulai->EditAttrs["class"] = "form-control";
			$this->TglMulai->EditCustomAttributes = "";
			$this->TglMulai->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->TglMulai->CurrentValue, 8));
			$this->TglMulai->PlaceHolder = ew_RemoveHtml($this->TglMulai->FldCaption());

			// Alamat1
			$this->Alamat1->EditAttrs["class"] = "form-control";
			$this->Alamat1->EditCustomAttributes = "";
			$this->Alamat1->EditValue = ew_HtmlEncode($this->Alamat1->CurrentValue);
			$this->Alamat1->PlaceHolder = ew_RemoveHtml($this->Alamat1->FldCaption());

			// Alamat2
			$this->Alamat2->EditAttrs["class"] = "form-control";
			$this->Alamat2->EditCustomAttributes = "";
			$this->Alamat2->EditValue = ew_HtmlEncode($this->Alamat2->CurrentValue);
			$this->Alamat2->PlaceHolder = ew_RemoveHtml($this->Alamat2->FldCaption());

			// Kota
			$this->Kota->EditAttrs["class"] = "form-control";
			$this->Kota->EditCustomAttributes = "";
			$this->Kota->EditValue = ew_HtmlEncode($this->Kota->CurrentValue);
			$this->Kota->PlaceHolder = ew_RemoveHtml($this->Kota->FldCaption());

			// KodePos
			$this->KodePos->EditAttrs["class"] = "form-control";
			$this->KodePos->EditCustomAttributes = "";
			$this->KodePos->EditValue = ew_HtmlEncode($this->KodePos->CurrentValue);
			$this->KodePos->PlaceHolder = ew_RemoveHtml($this->KodePos->FldCaption());

			// Telepon
			$this->Telepon->EditAttrs["class"] = "form-control";
			$this->Telepon->EditCustomAttributes = "";
			$this->Telepon->EditValue = ew_HtmlEncode($this->Telepon->CurrentValue);
			$this->Telepon->PlaceHolder = ew_RemoveHtml($this->Telepon->FldCaption());

			// Fax
			$this->Fax->EditAttrs["class"] = "form-control";
			$this->Fax->EditCustomAttributes = "";
			$this->Fax->EditValue = ew_HtmlEncode($this->Fax->CurrentValue);
			$this->Fax->PlaceHolder = ew_RemoveHtml($this->Fax->FldCaption());

			// Email
			$this->_Email->EditAttrs["class"] = "form-control";
			$this->_Email->EditCustomAttributes = "";
			$this->_Email->EditValue = ew_HtmlEncode($this->_Email->CurrentValue);
			$this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

			// Website
			$this->Website->EditAttrs["class"] = "form-control";
			$this->Website->EditCustomAttributes = "";
			$this->Website->EditValue = ew_HtmlEncode($this->Website->CurrentValue);
			$this->Website->PlaceHolder = ew_RemoveHtml($this->Website->FldCaption());

			// NoAkta
			$this->NoAkta->EditAttrs["class"] = "form-control";
			$this->NoAkta->EditCustomAttributes = "";
			$this->NoAkta->EditValue = ew_HtmlEncode($this->NoAkta->CurrentValue);
			$this->NoAkta->PlaceHolder = ew_RemoveHtml($this->NoAkta->FldCaption());

			// TglAkta
			$this->TglAkta->EditAttrs["class"] = "form-control";
			$this->TglAkta->EditCustomAttributes = "";
			$this->TglAkta->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->TglAkta->CurrentValue, 8));
			$this->TglAkta->PlaceHolder = ew_RemoveHtml($this->TglAkta->FldCaption());

			// NoSah
			$this->NoSah->EditAttrs["class"] = "form-control";
			$this->NoSah->EditCustomAttributes = "";
			$this->NoSah->EditValue = ew_HtmlEncode($this->NoSah->CurrentValue);
			$this->NoSah->PlaceHolder = ew_RemoveHtml($this->NoSah->FldCaption());

			// TglSah
			$this->TglSah->EditAttrs["class"] = "form-control";
			$this->TglSah->EditCustomAttributes = "";
			$this->TglSah->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->TglSah->CurrentValue, 8));
			$this->TglSah->PlaceHolder = ew_RemoveHtml($this->TglSah->FldCaption());

			// Logo
			$this->Logo->EditAttrs["class"] = "form-control";
			$this->Logo->EditCustomAttributes = "";
			$this->Logo->EditValue = ew_HtmlEncode($this->Logo->CurrentValue);
			$this->Logo->PlaceHolder = ew_RemoveHtml($this->Logo->FldCaption());

			// StartNoIdentitas
			$this->StartNoIdentitas->EditAttrs["class"] = "form-control";
			$this->StartNoIdentitas->EditCustomAttributes = "";
			$this->StartNoIdentitas->EditValue = ew_HtmlEncode($this->StartNoIdentitas->CurrentValue);
			$this->StartNoIdentitas->PlaceHolder = ew_RemoveHtml($this->StartNoIdentitas->FldCaption());

			// NoIdentitas
			$this->NoIdentitas->EditAttrs["class"] = "form-control";
			$this->NoIdentitas->EditCustomAttributes = "";
			$this->NoIdentitas->EditValue = ew_HtmlEncode($this->NoIdentitas->CurrentValue);
			$this->NoIdentitas->PlaceHolder = ew_RemoveHtml($this->NoIdentitas->FldCaption());

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// Kode

			$this->Kode->LinkCustomAttributes = "";
			$this->Kode->HrefValue = "";

			// KodeHukum
			$this->KodeHukum->LinkCustomAttributes = "";
			$this->KodeHukum->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// TglMulai
			$this->TglMulai->LinkCustomAttributes = "";
			$this->TglMulai->HrefValue = "";

			// Alamat1
			$this->Alamat1->LinkCustomAttributes = "";
			$this->Alamat1->HrefValue = "";

			// Alamat2
			$this->Alamat2->LinkCustomAttributes = "";
			$this->Alamat2->HrefValue = "";

			// Kota
			$this->Kota->LinkCustomAttributes = "";
			$this->Kota->HrefValue = "";

			// KodePos
			$this->KodePos->LinkCustomAttributes = "";
			$this->KodePos->HrefValue = "";

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";

			// Fax
			$this->Fax->LinkCustomAttributes = "";
			$this->Fax->HrefValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";

			// Website
			$this->Website->LinkCustomAttributes = "";
			$this->Website->HrefValue = "";

			// NoAkta
			$this->NoAkta->LinkCustomAttributes = "";
			$this->NoAkta->HrefValue = "";

			// TglAkta
			$this->TglAkta->LinkCustomAttributes = "";
			$this->TglAkta->HrefValue = "";

			// NoSah
			$this->NoSah->LinkCustomAttributes = "";
			$this->NoSah->HrefValue = "";

			// TglSah
			$this->TglSah->LinkCustomAttributes = "";
			$this->TglSah->HrefValue = "";

			// Logo
			$this->Logo->LinkCustomAttributes = "";
			$this->Logo->HrefValue = "";

			// StartNoIdentitas
			$this->StartNoIdentitas->LinkCustomAttributes = "";
			$this->StartNoIdentitas->HrefValue = "";

			// NoIdentitas
			$this->NoIdentitas->LinkCustomAttributes = "";
			$this->NoIdentitas->HrefValue = "";

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
		if (!$this->Kode->FldIsDetailKey && !is_null($this->Kode->FormValue) && $this->Kode->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Kode->FldCaption(), $this->Kode->ReqErrMsg));
		}
		if (!$this->Nama->FldIsDetailKey && !is_null($this->Nama->FormValue) && $this->Nama->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Nama->FldCaption(), $this->Nama->ReqErrMsg));
		}
		if (!$this->TglMulai->FldIsDetailKey && !is_null($this->TglMulai->FormValue) && $this->TglMulai->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TglMulai->FldCaption(), $this->TglMulai->ReqErrMsg));
		}
		if (!ew_CheckDateDef($this->TglMulai->FormValue)) {
			ew_AddMessage($gsFormError, $this->TglMulai->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TglAkta->FormValue)) {
			ew_AddMessage($gsFormError, $this->TglAkta->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TglSah->FormValue)) {
			ew_AddMessage($gsFormError, $this->TglSah->FldErrMsg());
		}
		if (!$this->StartNoIdentitas->FldIsDetailKey && !is_null($this->StartNoIdentitas->FormValue) && $this->StartNoIdentitas->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->StartNoIdentitas->FldCaption(), $this->StartNoIdentitas->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->StartNoIdentitas->FormValue)) {
			ew_AddMessage($gsFormError, $this->StartNoIdentitas->FldErrMsg());
		}
		if (!$this->NoIdentitas->FldIsDetailKey && !is_null($this->NoIdentitas->FormValue) && $this->NoIdentitas->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->NoIdentitas->FldCaption(), $this->NoIdentitas->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->NoIdentitas->FormValue)) {
			ew_AddMessage($gsFormError, $this->NoIdentitas->FldErrMsg());
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

		// Kode
		$this->Kode->SetDbValueDef($rsnew, $this->Kode->CurrentValue, "", FALSE);

		// KodeHukum
		$this->KodeHukum->SetDbValueDef($rsnew, $this->KodeHukum->CurrentValue, NULL, FALSE);

		// Nama
		$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, "", FALSE);

		// TglMulai
		$this->TglMulai->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TglMulai->CurrentValue, 0), ew_CurrentDate(), strval($this->TglMulai->CurrentValue) == "");

		// Alamat1
		$this->Alamat1->SetDbValueDef($rsnew, $this->Alamat1->CurrentValue, NULL, FALSE);

		// Alamat2
		$this->Alamat2->SetDbValueDef($rsnew, $this->Alamat2->CurrentValue, NULL, FALSE);

		// Kota
		$this->Kota->SetDbValueDef($rsnew, $this->Kota->CurrentValue, NULL, FALSE);

		// KodePos
		$this->KodePos->SetDbValueDef($rsnew, $this->KodePos->CurrentValue, NULL, FALSE);

		// Telepon
		$this->Telepon->SetDbValueDef($rsnew, $this->Telepon->CurrentValue, NULL, FALSE);

		// Fax
		$this->Fax->SetDbValueDef($rsnew, $this->Fax->CurrentValue, NULL, FALSE);

		// Email
		$this->_Email->SetDbValueDef($rsnew, $this->_Email->CurrentValue, NULL, FALSE);

		// Website
		$this->Website->SetDbValueDef($rsnew, $this->Website->CurrentValue, NULL, FALSE);

		// NoAkta
		$this->NoAkta->SetDbValueDef($rsnew, $this->NoAkta->CurrentValue, NULL, FALSE);

		// TglAkta
		$this->TglAkta->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TglAkta->CurrentValue, 0), NULL, FALSE);

		// NoSah
		$this->NoSah->SetDbValueDef($rsnew, $this->NoSah->CurrentValue, NULL, FALSE);

		// TglSah
		$this->TglSah->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TglSah->CurrentValue, 0), NULL, FALSE);

		// Logo
		$this->Logo->SetDbValueDef($rsnew, $this->Logo->CurrentValue, NULL, FALSE);

		// StartNoIdentitas
		$this->StartNoIdentitas->SetDbValueDef($rsnew, $this->StartNoIdentitas->CurrentValue, 0, strval($this->StartNoIdentitas->CurrentValue) == "");

		// NoIdentitas
		$this->NoIdentitas->SetDbValueDef($rsnew, $this->NoIdentitas->CurrentValue, 0, strval($this->NoIdentitas->CurrentValue) == "");

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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("identitaslist.php"), "", $this->TableVar, TRUE);
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
if (!isset($identitas_add)) $identitas_add = new cidentitas_add();

// Page init
$identitas_add->Page_Init();

// Page main
$identitas_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$identitas_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fidentitasadd = new ew_Form("fidentitasadd", "add");

// Validate form
fidentitasadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Kode");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $identitas->Kode->FldCaption(), $identitas->Kode->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Nama");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $identitas->Nama->FldCaption(), $identitas->Nama->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TglMulai");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $identitas->TglMulai->FldCaption(), $identitas->TglMulai->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TglMulai");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($identitas->TglMulai->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_TglAkta");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($identitas->TglAkta->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_TglSah");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($identitas->TglSah->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_StartNoIdentitas");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $identitas->StartNoIdentitas->FldCaption(), $identitas->StartNoIdentitas->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_StartNoIdentitas");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($identitas->StartNoIdentitas->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_NoIdentitas");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $identitas->NoIdentitas->FldCaption(), $identitas->NoIdentitas->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_NoIdentitas");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($identitas->NoIdentitas->FldErrMsg()) ?>");

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
fidentitasadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fidentitasadd.ValidateRequired = true;
<?php } else { ?>
fidentitasadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fidentitasadd.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fidentitasadd.Lists["x_NA"].Options = <?php echo json_encode($identitas->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$identitas_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $identitas_add->ShowPageHeader(); ?>
<?php
$identitas_add->ShowMessage();
?>
<form name="fidentitasadd" id="fidentitasadd" class="<?php echo $identitas_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($identitas_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $identitas_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="identitas">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($identitas_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($identitas->Kode->Visible) { // Kode ?>
	<div id="r_Kode" class="form-group">
		<label id="elh_identitas_Kode" for="x_Kode" class="col-sm-2 control-label ewLabel"><?php echo $identitas->Kode->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->Kode->CellAttributes() ?>>
<span id="el_identitas_Kode">
<input type="text" data-table="identitas" data-field="x_Kode" name="x_Kode" id="x_Kode" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($identitas->Kode->getPlaceHolder()) ?>" value="<?php echo $identitas->Kode->EditValue ?>"<?php echo $identitas->Kode->EditAttributes() ?>>
</span>
<?php echo $identitas->Kode->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->KodeHukum->Visible) { // KodeHukum ?>
	<div id="r_KodeHukum" class="form-group">
		<label id="elh_identitas_KodeHukum" for="x_KodeHukum" class="col-sm-2 control-label ewLabel"><?php echo $identitas->KodeHukum->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->KodeHukum->CellAttributes() ?>>
<span id="el_identitas_KodeHukum">
<input type="text" data-table="identitas" data-field="x_KodeHukum" name="x_KodeHukum" id="x_KodeHukum" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($identitas->KodeHukum->getPlaceHolder()) ?>" value="<?php echo $identitas->KodeHukum->EditValue ?>"<?php echo $identitas->KodeHukum->EditAttributes() ?>>
</span>
<?php echo $identitas->KodeHukum->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->Nama->Visible) { // Nama ?>
	<div id="r_Nama" class="form-group">
		<label id="elh_identitas_Nama" for="x_Nama" class="col-sm-2 control-label ewLabel"><?php echo $identitas->Nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->Nama->CellAttributes() ?>>
<span id="el_identitas_Nama">
<input type="text" data-table="identitas" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($identitas->Nama->getPlaceHolder()) ?>" value="<?php echo $identitas->Nama->EditValue ?>"<?php echo $identitas->Nama->EditAttributes() ?>>
</span>
<?php echo $identitas->Nama->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->TglMulai->Visible) { // TglMulai ?>
	<div id="r_TglMulai" class="form-group">
		<label id="elh_identitas_TglMulai" for="x_TglMulai" class="col-sm-2 control-label ewLabel"><?php echo $identitas->TglMulai->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->TglMulai->CellAttributes() ?>>
<span id="el_identitas_TglMulai">
<input type="text" data-table="identitas" data-field="x_TglMulai" name="x_TglMulai" id="x_TglMulai" placeholder="<?php echo ew_HtmlEncode($identitas->TglMulai->getPlaceHolder()) ?>" value="<?php echo $identitas->TglMulai->EditValue ?>"<?php echo $identitas->TglMulai->EditAttributes() ?>>
</span>
<?php echo $identitas->TglMulai->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->Alamat1->Visible) { // Alamat1 ?>
	<div id="r_Alamat1" class="form-group">
		<label id="elh_identitas_Alamat1" for="x_Alamat1" class="col-sm-2 control-label ewLabel"><?php echo $identitas->Alamat1->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->Alamat1->CellAttributes() ?>>
<span id="el_identitas_Alamat1">
<input type="text" data-table="identitas" data-field="x_Alamat1" name="x_Alamat1" id="x_Alamat1" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($identitas->Alamat1->getPlaceHolder()) ?>" value="<?php echo $identitas->Alamat1->EditValue ?>"<?php echo $identitas->Alamat1->EditAttributes() ?>>
</span>
<?php echo $identitas->Alamat1->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->Alamat2->Visible) { // Alamat2 ?>
	<div id="r_Alamat2" class="form-group">
		<label id="elh_identitas_Alamat2" for="x_Alamat2" class="col-sm-2 control-label ewLabel"><?php echo $identitas->Alamat2->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->Alamat2->CellAttributes() ?>>
<span id="el_identitas_Alamat2">
<input type="text" data-table="identitas" data-field="x_Alamat2" name="x_Alamat2" id="x_Alamat2" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($identitas->Alamat2->getPlaceHolder()) ?>" value="<?php echo $identitas->Alamat2->EditValue ?>"<?php echo $identitas->Alamat2->EditAttributes() ?>>
</span>
<?php echo $identitas->Alamat2->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->Kota->Visible) { // Kota ?>
	<div id="r_Kota" class="form-group">
		<label id="elh_identitas_Kota" for="x_Kota" class="col-sm-2 control-label ewLabel"><?php echo $identitas->Kota->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->Kota->CellAttributes() ?>>
<span id="el_identitas_Kota">
<input type="text" data-table="identitas" data-field="x_Kota" name="x_Kota" id="x_Kota" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($identitas->Kota->getPlaceHolder()) ?>" value="<?php echo $identitas->Kota->EditValue ?>"<?php echo $identitas->Kota->EditAttributes() ?>>
</span>
<?php echo $identitas->Kota->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->KodePos->Visible) { // KodePos ?>
	<div id="r_KodePos" class="form-group">
		<label id="elh_identitas_KodePos" for="x_KodePos" class="col-sm-2 control-label ewLabel"><?php echo $identitas->KodePos->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->KodePos->CellAttributes() ?>>
<span id="el_identitas_KodePos">
<input type="text" data-table="identitas" data-field="x_KodePos" name="x_KodePos" id="x_KodePos" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($identitas->KodePos->getPlaceHolder()) ?>" value="<?php echo $identitas->KodePos->EditValue ?>"<?php echo $identitas->KodePos->EditAttributes() ?>>
</span>
<?php echo $identitas->KodePos->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->Telepon->Visible) { // Telepon ?>
	<div id="r_Telepon" class="form-group">
		<label id="elh_identitas_Telepon" for="x_Telepon" class="col-sm-2 control-label ewLabel"><?php echo $identitas->Telepon->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->Telepon->CellAttributes() ?>>
<span id="el_identitas_Telepon">
<input type="text" data-table="identitas" data-field="x_Telepon" name="x_Telepon" id="x_Telepon" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($identitas->Telepon->getPlaceHolder()) ?>" value="<?php echo $identitas->Telepon->EditValue ?>"<?php echo $identitas->Telepon->EditAttributes() ?>>
</span>
<?php echo $identitas->Telepon->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->Fax->Visible) { // Fax ?>
	<div id="r_Fax" class="form-group">
		<label id="elh_identitas_Fax" for="x_Fax" class="col-sm-2 control-label ewLabel"><?php echo $identitas->Fax->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->Fax->CellAttributes() ?>>
<span id="el_identitas_Fax">
<input type="text" data-table="identitas" data-field="x_Fax" name="x_Fax" id="x_Fax" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($identitas->Fax->getPlaceHolder()) ?>" value="<?php echo $identitas->Fax->EditValue ?>"<?php echo $identitas->Fax->EditAttributes() ?>>
</span>
<?php echo $identitas->Fax->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->_Email->Visible) { // Email ?>
	<div id="r__Email" class="form-group">
		<label id="elh_identitas__Email" for="x__Email" class="col-sm-2 control-label ewLabel"><?php echo $identitas->_Email->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->_Email->CellAttributes() ?>>
<span id="el_identitas__Email">
<input type="text" data-table="identitas" data-field="x__Email" name="x__Email" id="x__Email" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($identitas->_Email->getPlaceHolder()) ?>" value="<?php echo $identitas->_Email->EditValue ?>"<?php echo $identitas->_Email->EditAttributes() ?>>
</span>
<?php echo $identitas->_Email->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->Website->Visible) { // Website ?>
	<div id="r_Website" class="form-group">
		<label id="elh_identitas_Website" for="x_Website" class="col-sm-2 control-label ewLabel"><?php echo $identitas->Website->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->Website->CellAttributes() ?>>
<span id="el_identitas_Website">
<input type="text" data-table="identitas" data-field="x_Website" name="x_Website" id="x_Website" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($identitas->Website->getPlaceHolder()) ?>" value="<?php echo $identitas->Website->EditValue ?>"<?php echo $identitas->Website->EditAttributes() ?>>
</span>
<?php echo $identitas->Website->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->NoAkta->Visible) { // NoAkta ?>
	<div id="r_NoAkta" class="form-group">
		<label id="elh_identitas_NoAkta" for="x_NoAkta" class="col-sm-2 control-label ewLabel"><?php echo $identitas->NoAkta->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->NoAkta->CellAttributes() ?>>
<span id="el_identitas_NoAkta">
<input type="text" data-table="identitas" data-field="x_NoAkta" name="x_NoAkta" id="x_NoAkta" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($identitas->NoAkta->getPlaceHolder()) ?>" value="<?php echo $identitas->NoAkta->EditValue ?>"<?php echo $identitas->NoAkta->EditAttributes() ?>>
</span>
<?php echo $identitas->NoAkta->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->TglAkta->Visible) { // TglAkta ?>
	<div id="r_TglAkta" class="form-group">
		<label id="elh_identitas_TglAkta" for="x_TglAkta" class="col-sm-2 control-label ewLabel"><?php echo $identitas->TglAkta->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->TglAkta->CellAttributes() ?>>
<span id="el_identitas_TglAkta">
<input type="text" data-table="identitas" data-field="x_TglAkta" name="x_TglAkta" id="x_TglAkta" placeholder="<?php echo ew_HtmlEncode($identitas->TglAkta->getPlaceHolder()) ?>" value="<?php echo $identitas->TglAkta->EditValue ?>"<?php echo $identitas->TglAkta->EditAttributes() ?>>
</span>
<?php echo $identitas->TglAkta->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->NoSah->Visible) { // NoSah ?>
	<div id="r_NoSah" class="form-group">
		<label id="elh_identitas_NoSah" for="x_NoSah" class="col-sm-2 control-label ewLabel"><?php echo $identitas->NoSah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->NoSah->CellAttributes() ?>>
<span id="el_identitas_NoSah">
<input type="text" data-table="identitas" data-field="x_NoSah" name="x_NoSah" id="x_NoSah" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($identitas->NoSah->getPlaceHolder()) ?>" value="<?php echo $identitas->NoSah->EditValue ?>"<?php echo $identitas->NoSah->EditAttributes() ?>>
</span>
<?php echo $identitas->NoSah->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->TglSah->Visible) { // TglSah ?>
	<div id="r_TglSah" class="form-group">
		<label id="elh_identitas_TglSah" for="x_TglSah" class="col-sm-2 control-label ewLabel"><?php echo $identitas->TglSah->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->TglSah->CellAttributes() ?>>
<span id="el_identitas_TglSah">
<input type="text" data-table="identitas" data-field="x_TglSah" name="x_TglSah" id="x_TglSah" placeholder="<?php echo ew_HtmlEncode($identitas->TglSah->getPlaceHolder()) ?>" value="<?php echo $identitas->TglSah->EditValue ?>"<?php echo $identitas->TglSah->EditAttributes() ?>>
</span>
<?php echo $identitas->TglSah->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->Logo->Visible) { // Logo ?>
	<div id="r_Logo" class="form-group">
		<label id="elh_identitas_Logo" for="x_Logo" class="col-sm-2 control-label ewLabel"><?php echo $identitas->Logo->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->Logo->CellAttributes() ?>>
<span id="el_identitas_Logo">
<input type="text" data-table="identitas" data-field="x_Logo" name="x_Logo" id="x_Logo" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($identitas->Logo->getPlaceHolder()) ?>" value="<?php echo $identitas->Logo->EditValue ?>"<?php echo $identitas->Logo->EditAttributes() ?>>
</span>
<?php echo $identitas->Logo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->StartNoIdentitas->Visible) { // StartNoIdentitas ?>
	<div id="r_StartNoIdentitas" class="form-group">
		<label id="elh_identitas_StartNoIdentitas" for="x_StartNoIdentitas" class="col-sm-2 control-label ewLabel"><?php echo $identitas->StartNoIdentitas->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->StartNoIdentitas->CellAttributes() ?>>
<span id="el_identitas_StartNoIdentitas">
<input type="text" data-table="identitas" data-field="x_StartNoIdentitas" name="x_StartNoIdentitas" id="x_StartNoIdentitas" size="30" placeholder="<?php echo ew_HtmlEncode($identitas->StartNoIdentitas->getPlaceHolder()) ?>" value="<?php echo $identitas->StartNoIdentitas->EditValue ?>"<?php echo $identitas->StartNoIdentitas->EditAttributes() ?>>
</span>
<?php echo $identitas->StartNoIdentitas->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->NoIdentitas->Visible) { // NoIdentitas ?>
	<div id="r_NoIdentitas" class="form-group">
		<label id="elh_identitas_NoIdentitas" for="x_NoIdentitas" class="col-sm-2 control-label ewLabel"><?php echo $identitas->NoIdentitas->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->NoIdentitas->CellAttributes() ?>>
<span id="el_identitas_NoIdentitas">
<input type="text" data-table="identitas" data-field="x_NoIdentitas" name="x_NoIdentitas" id="x_NoIdentitas" size="30" placeholder="<?php echo ew_HtmlEncode($identitas->NoIdentitas->getPlaceHolder()) ?>" value="<?php echo $identitas->NoIdentitas->EditValue ?>"<?php echo $identitas->NoIdentitas->EditAttributes() ?>>
</span>
<?php echo $identitas->NoIdentitas->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($identitas->NA->Visible) { // NA ?>
	<div id="r_NA" class="form-group">
		<label id="elh_identitas_NA" class="col-sm-2 control-label ewLabel"><?php echo $identitas->NA->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $identitas->NA->CellAttributes() ?>>
<span id="el_identitas_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="identitas" data-field="x_NA" data-value-separator="<?php echo $identitas->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $identitas->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $identitas->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $identitas->NA->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<?php if (!$identitas_add->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $identitas_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fidentitasadd.Init();
</script>
<?php
$identitas_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$identitas_add->Page_Terminate();
?>
