<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_kampusinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_kampus_add = NULL; // Initialize page object first

class cmaster_kampus_add extends cmaster_kampus {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_kampus';

	// Page object name
	var $PageObjName = 'master_kampus_add';

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

		// Table object (master_kampus)
		if (!isset($GLOBALS["master_kampus"]) || get_class($GLOBALS["master_kampus"]) == "cmaster_kampus") {
			$GLOBALS["master_kampus"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_kampus"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_kampus', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("master_kampuslist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->KampusID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->Alamat->SetVisibility();
		$this->ProvinsiID->SetVisibility();
		$this->KabupatenKotaID->SetVisibility();
		$this->KecamatanID->SetVisibility();
		$this->DesaID->SetVisibility();
		$this->_Email->SetVisibility();
		$this->Telepon->SetVisibility();
		$this->Fax->SetVisibility();
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
		global $EW_EXPORT, $master_kampus;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_kampus);
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
			if (@$_GET["KampusID"] != "") {
				$this->KampusID->setQueryStringValue($_GET["KampusID"]);
				$this->setKey("KampusID", $this->KampusID->CurrentValue); // Set up key
			} else {
				$this->setKey("KampusID", ""); // Clear key
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
					$this->Page_Terminate("master_kampuslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "master_kampuslist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "master_kampusview.php")
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
		$this->KampusID->CurrentValue = NULL;
		$this->KampusID->OldValue = $this->KampusID->CurrentValue;
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->Alamat->CurrentValue = NULL;
		$this->Alamat->OldValue = $this->Alamat->CurrentValue;
		$this->ProvinsiID->CurrentValue = NULL;
		$this->ProvinsiID->OldValue = $this->ProvinsiID->CurrentValue;
		$this->KabupatenKotaID->CurrentValue = NULL;
		$this->KabupatenKotaID->OldValue = $this->KabupatenKotaID->CurrentValue;
		$this->KecamatanID->CurrentValue = NULL;
		$this->KecamatanID->OldValue = $this->KecamatanID->CurrentValue;
		$this->DesaID->CurrentValue = NULL;
		$this->DesaID->OldValue = $this->DesaID->CurrentValue;
		$this->_Email->CurrentValue = NULL;
		$this->_Email->OldValue = $this->_Email->CurrentValue;
		$this->Telepon->CurrentValue = NULL;
		$this->Telepon->OldValue = $this->Telepon->CurrentValue;
		$this->Fax->CurrentValue = NULL;
		$this->Fax->OldValue = $this->Fax->CurrentValue;
		$this->NA->CurrentValue = "N";
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->KampusID->FldIsDetailKey) {
			$this->KampusID->setFormValue($objForm->GetValue("x_KampusID"));
		}
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->Alamat->FldIsDetailKey) {
			$this->Alamat->setFormValue($objForm->GetValue("x_Alamat"));
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
		if (!$this->_Email->FldIsDetailKey) {
			$this->_Email->setFormValue($objForm->GetValue("x__Email"));
		}
		if (!$this->Telepon->FldIsDetailKey) {
			$this->Telepon->setFormValue($objForm->GetValue("x_Telepon"));
		}
		if (!$this->Fax->FldIsDetailKey) {
			$this->Fax->setFormValue($objForm->GetValue("x_Fax"));
		}
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->KampusID->CurrentValue = $this->KampusID->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->Alamat->CurrentValue = $this->Alamat->FormValue;
		$this->ProvinsiID->CurrentValue = $this->ProvinsiID->FormValue;
		$this->KabupatenKotaID->CurrentValue = $this->KabupatenKotaID->FormValue;
		$this->KecamatanID->CurrentValue = $this->KecamatanID->FormValue;
		$this->DesaID->CurrentValue = $this->DesaID->FormValue;
		$this->_Email->CurrentValue = $this->_Email->FormValue;
		$this->Telepon->CurrentValue = $this->Telepon->FormValue;
		$this->Fax->CurrentValue = $this->Fax->FormValue;
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
		$this->KampusID->setDbValue($rs->fields('KampusID'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->Alamat->setDbValue($rs->fields('Alamat'));
		$this->ProvinsiID->setDbValue($rs->fields('ProvinsiID'));
		$this->KabupatenKotaID->setDbValue($rs->fields('KabupatenKotaID'));
		$this->KecamatanID->setDbValue($rs->fields('KecamatanID'));
		$this->DesaID->setDbValue($rs->fields('DesaID'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Telepon->setDbValue($rs->fields('Telepon'));
		$this->Fax->setDbValue($rs->fields('Fax'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->KampusID->DbValue = $row['KampusID'];
		$this->Nama->DbValue = $row['Nama'];
		$this->Alamat->DbValue = $row['Alamat'];
		$this->ProvinsiID->DbValue = $row['ProvinsiID'];
		$this->KabupatenKotaID->DbValue = $row['KabupatenKotaID'];
		$this->KecamatanID->DbValue = $row['KecamatanID'];
		$this->DesaID->DbValue = $row['DesaID'];
		$this->_Email->DbValue = $row['Email'];
		$this->Telepon->DbValue = $row['Telepon'];
		$this->Fax->DbValue = $row['Fax'];
		$this->NA->DbValue = $row['NA'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("KampusID")) <> "")
			$this->KampusID->CurrentValue = $this->getKey("KampusID"); // KampusID
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
		// KampusID
		// Nama
		// Alamat
		// ProvinsiID
		// KabupatenKotaID
		// KecamatanID
		// DesaID
		// Email
		// Telepon
		// Fax
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// KampusID
		$this->KampusID->ViewValue = $this->KampusID->CurrentValue;
		$this->KampusID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// Alamat
		$this->Alamat->ViewValue = $this->Alamat->CurrentValue;
		$this->Alamat->ViewCustomAttributes = "";

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

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// Telepon
		$this->Telepon->ViewValue = $this->Telepon->CurrentValue;
		$this->Telepon->ViewCustomAttributes = "";

		// Fax
		$this->Fax->ViewValue = $this->Fax->CurrentValue;
		$this->Fax->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";
			$this->KampusID->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// Alamat
			$this->Alamat->LinkCustomAttributes = "";
			$this->Alamat->HrefValue = "";
			$this->Alamat->TooltipValue = "";

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

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";
			$this->Telepon->TooltipValue = "";

			// Fax
			$this->Fax->LinkCustomAttributes = "";
			$this->Fax->HrefValue = "";
			$this->Fax->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// KampusID
			$this->KampusID->EditAttrs["class"] = "form-control";
			$this->KampusID->EditCustomAttributes = "";
			$this->KampusID->EditValue = ew_HtmlEncode($this->KampusID->CurrentValue);
			$this->KampusID->PlaceHolder = ew_RemoveHtml($this->KampusID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// Alamat
			$this->Alamat->EditAttrs["class"] = "form-control";
			$this->Alamat->EditCustomAttributes = "";
			$this->Alamat->EditValue = ew_HtmlEncode($this->Alamat->CurrentValue);
			$this->Alamat->PlaceHolder = ew_RemoveHtml($this->Alamat->FldCaption());

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

			// Email
			$this->_Email->EditAttrs["class"] = "form-control";
			$this->_Email->EditCustomAttributes = "";
			$this->_Email->EditValue = ew_HtmlEncode($this->_Email->CurrentValue);
			$this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

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

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// KampusID

			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// Alamat
			$this->Alamat->LinkCustomAttributes = "";
			$this->Alamat->HrefValue = "";

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

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";

			// Fax
			$this->Fax->LinkCustomAttributes = "";
			$this->Fax->HrefValue = "";

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
		if (!$this->KampusID->FldIsDetailKey && !is_null($this->KampusID->FormValue) && $this->KampusID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->KampusID->FldCaption(), $this->KampusID->ReqErrMsg));
		}
		if (!ew_CheckEmail($this->_Email->FormValue)) {
			ew_AddMessage($gsFormError, $this->_Email->FldErrMsg());
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
		if ($this->KampusID->CurrentValue <> "") { // Check field with unique index
			$sFilter = "(KampusID = '" . ew_AdjustSql($this->KampusID->CurrentValue, $this->DBID) . "')";
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->KampusID->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->KampusID->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
		}
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// KampusID
		$this->KampusID->SetDbValueDef($rsnew, $this->KampusID->CurrentValue, "", FALSE);

		// Nama
		$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, NULL, FALSE);

		// Alamat
		$this->Alamat->SetDbValueDef($rsnew, $this->Alamat->CurrentValue, NULL, FALSE);

		// ProvinsiID
		$this->ProvinsiID->SetDbValueDef($rsnew, $this->ProvinsiID->CurrentValue, NULL, FALSE);

		// KabupatenKotaID
		$this->KabupatenKotaID->SetDbValueDef($rsnew, $this->KabupatenKotaID->CurrentValue, NULL, FALSE);

		// KecamatanID
		$this->KecamatanID->SetDbValueDef($rsnew, $this->KecamatanID->CurrentValue, NULL, FALSE);

		// DesaID
		$this->DesaID->SetDbValueDef($rsnew, $this->DesaID->CurrentValue, NULL, FALSE);

		// Email
		$this->_Email->SetDbValueDef($rsnew, $this->_Email->CurrentValue, NULL, FALSE);

		// Telepon
		$this->Telepon->SetDbValueDef($rsnew, $this->Telepon->CurrentValue, NULL, FALSE);

		// Fax
		$this->Fax->SetDbValueDef($rsnew, $this->Fax->CurrentValue, NULL, FALSE);

		// NA
		$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), NULL, strval($this->NA->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['KampusID']) == "") {
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_kampuslist.php"), "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
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
if (!isset($master_kampus_add)) $master_kampus_add = new cmaster_kampus_add();

// Page init
$master_kampus_add->Page_Init();

// Page main
$master_kampus_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_kampus_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fmaster_kampusadd = new ew_Form("fmaster_kampusadd", "add");

// Validate form
fmaster_kampusadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_KampusID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_kampus->KampusID->FldCaption(), $master_kampus->KampusID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "__Email");
			if (elm && !ew_CheckEmail(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($master_kampus->_Email->FldErrMsg()) ?>");

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
fmaster_kampusadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_kampusadd.ValidateRequired = true;
<?php } else { ?>
fmaster_kampusadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_kampusadd.Lists["x_ProvinsiID"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenKotaID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fmaster_kampusadd.Lists["x_KabupatenKotaID"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":["x_ProvinsiID"],"ChildFields":["x_KecamatanID"],"FilterFields":["x_ProvinsiID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fmaster_kampusadd.Lists["x_KecamatanID"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":["x_KabupatenKotaID"],"ChildFields":["x_DesaID"],"FilterFields":["x_KabupatenKotaID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fmaster_kampusadd.Lists["x_DesaID"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":["x_KecamatanID"],"ChildFields":[],"FilterFields":["x_KecamatanID"],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fmaster_kampusadd.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_kampusadd.Lists["x_NA"].Options = <?php echo json_encode($master_kampus->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$master_kampus_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_kampus_add->ShowPageHeader(); ?>
<?php
$master_kampus_add->ShowMessage();
?>
<form name="fmaster_kampusadd" id="fmaster_kampusadd" class="<?php echo $master_kampus_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_kampus_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_kampus_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_kampus">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($master_kampus_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_kampus_add->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_kampusadd" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_kampus->KampusID->Visible) { // KampusID ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
	<div id="r_KampusID" class="form-group">
		<label id="elh_master_kampus_KampusID" for="x_KampusID" class="col-sm-2 control-label ewLabel"><?php echo $master_kampus->KampusID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $master_kampus->KampusID->CellAttributes() ?>>
<span id="el_master_kampus_KampusID">
<input type="text" data-table="master_kampus" data-field="x_KampusID" name="x_KampusID" id="x_KampusID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_kampus->KampusID->getPlaceHolder()) ?>" value="<?php echo $master_kampus->KampusID->EditValue ?>"<?php echo $master_kampus->KampusID->EditAttributes() ?>>
</span>
<?php echo $master_kampus->KampusID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KampusID">
		<td><span id="elh_master_kampus_KampusID"><?php echo $master_kampus->KampusID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $master_kampus->KampusID->CellAttributes() ?>>
<span id="el_master_kampus_KampusID">
<input type="text" data-table="master_kampus" data-field="x_KampusID" name="x_KampusID" id="x_KampusID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_kampus->KampusID->getPlaceHolder()) ?>" value="<?php echo $master_kampus->KampusID->EditValue ?>"<?php echo $master_kampus->KampusID->EditAttributes() ?>>
</span>
<?php echo $master_kampus->KampusID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_kampus->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label id="elh_master_kampus_Nama" for="x_Nama" class="col-sm-2 control-label ewLabel"><?php echo $master_kampus->Nama->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_kampus->Nama->CellAttributes() ?>>
<span id="el_master_kampus_Nama">
<input type="text" data-table="master_kampus" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_kampus->Nama->getPlaceHolder()) ?>" value="<?php echo $master_kampus->Nama->EditValue ?>"<?php echo $master_kampus->Nama->EditAttributes() ?>>
</span>
<?php echo $master_kampus->Nama->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_master_kampus_Nama"><?php echo $master_kampus->Nama->FldCaption() ?></span></td>
		<td<?php echo $master_kampus->Nama->CellAttributes() ?>>
<span id="el_master_kampus_Nama">
<input type="text" data-table="master_kampus" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_kampus->Nama->getPlaceHolder()) ?>" value="<?php echo $master_kampus->Nama->EditValue ?>"<?php echo $master_kampus->Nama->EditAttributes() ?>>
</span>
<?php echo $master_kampus->Nama->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_kampus->Alamat->Visible) { // Alamat ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
	<div id="r_Alamat" class="form-group">
		<label id="elh_master_kampus_Alamat" for="x_Alamat" class="col-sm-2 control-label ewLabel"><?php echo $master_kampus->Alamat->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_kampus->Alamat->CellAttributes() ?>>
<span id="el_master_kampus_Alamat">
<input type="text" data-table="master_kampus" data-field="x_Alamat" name="x_Alamat" id="x_Alamat" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_kampus->Alamat->getPlaceHolder()) ?>" value="<?php echo $master_kampus->Alamat->EditValue ?>"<?php echo $master_kampus->Alamat->EditAttributes() ?>>
</span>
<?php echo $master_kampus->Alamat->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Alamat">
		<td><span id="elh_master_kampus_Alamat"><?php echo $master_kampus->Alamat->FldCaption() ?></span></td>
		<td<?php echo $master_kampus->Alamat->CellAttributes() ?>>
<span id="el_master_kampus_Alamat">
<input type="text" data-table="master_kampus" data-field="x_Alamat" name="x_Alamat" id="x_Alamat" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_kampus->Alamat->getPlaceHolder()) ?>" value="<?php echo $master_kampus->Alamat->EditValue ?>"<?php echo $master_kampus->Alamat->EditAttributes() ?>>
</span>
<?php echo $master_kampus->Alamat->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_kampus->ProvinsiID->Visible) { // ProvinsiID ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
	<div id="r_ProvinsiID" class="form-group">
		<label id="elh_master_kampus_ProvinsiID" for="x_ProvinsiID" class="col-sm-2 control-label ewLabel"><?php echo $master_kampus->ProvinsiID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_kampus->ProvinsiID->CellAttributes() ?>>
<span id="el_master_kampus_ProvinsiID">
<?php $master_kampus->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$master_kampus->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="master_kampus" data-field="x_ProvinsiID" data-value-separator="<?php echo $master_kampus->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $master_kampus->ProvinsiID->EditAttributes() ?>>
<?php echo $master_kampus->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $master_kampus->ProvinsiID->LookupFilterQuery() ?>">
</span>
<?php echo $master_kampus->ProvinsiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiID">
		<td><span id="elh_master_kampus_ProvinsiID"><?php echo $master_kampus->ProvinsiID->FldCaption() ?></span></td>
		<td<?php echo $master_kampus->ProvinsiID->CellAttributes() ?>>
<span id="el_master_kampus_ProvinsiID">
<?php $master_kampus->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$master_kampus->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="master_kampus" data-field="x_ProvinsiID" data-value-separator="<?php echo $master_kampus->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $master_kampus->ProvinsiID->EditAttributes() ?>>
<?php echo $master_kampus->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $master_kampus->ProvinsiID->LookupFilterQuery() ?>">
</span>
<?php echo $master_kampus->ProvinsiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_kampus->KabupatenKotaID->Visible) { // KabupatenKotaID ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
	<div id="r_KabupatenKotaID" class="form-group">
		<label id="elh_master_kampus_KabupatenKotaID" for="x_KabupatenKotaID" class="col-sm-2 control-label ewLabel"><?php echo $master_kampus->KabupatenKotaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_kampus->KabupatenKotaID->CellAttributes() ?>>
<span id="el_master_kampus_KabupatenKotaID">
<?php $master_kampus->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$master_kampus->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="master_kampus" data-field="x_KabupatenKotaID" data-value-separator="<?php echo $master_kampus->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $master_kampus->KabupatenKotaID->EditAttributes() ?>>
<?php echo $master_kampus->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $master_kampus->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
<?php echo $master_kampus->KabupatenKotaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenKotaID">
		<td><span id="elh_master_kampus_KabupatenKotaID"><?php echo $master_kampus->KabupatenKotaID->FldCaption() ?></span></td>
		<td<?php echo $master_kampus->KabupatenKotaID->CellAttributes() ?>>
<span id="el_master_kampus_KabupatenKotaID">
<?php $master_kampus->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$master_kampus->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="master_kampus" data-field="x_KabupatenKotaID" data-value-separator="<?php echo $master_kampus->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $master_kampus->KabupatenKotaID->EditAttributes() ?>>
<?php echo $master_kampus->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $master_kampus->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
<?php echo $master_kampus->KabupatenKotaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_kampus->KecamatanID->Visible) { // KecamatanID ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
	<div id="r_KecamatanID" class="form-group">
		<label id="elh_master_kampus_KecamatanID" for="x_KecamatanID" class="col-sm-2 control-label ewLabel"><?php echo $master_kampus->KecamatanID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_kampus->KecamatanID->CellAttributes() ?>>
<span id="el_master_kampus_KecamatanID">
<?php $master_kampus->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$master_kampus->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="master_kampus" data-field="x_KecamatanID" data-value-separator="<?php echo $master_kampus->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $master_kampus->KecamatanID->EditAttributes() ?>>
<?php echo $master_kampus->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $master_kampus->KecamatanID->LookupFilterQuery() ?>">
</span>
<?php echo $master_kampus->KecamatanID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanID">
		<td><span id="elh_master_kampus_KecamatanID"><?php echo $master_kampus->KecamatanID->FldCaption() ?></span></td>
		<td<?php echo $master_kampus->KecamatanID->CellAttributes() ?>>
<span id="el_master_kampus_KecamatanID">
<?php $master_kampus->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$master_kampus->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="master_kampus" data-field="x_KecamatanID" data-value-separator="<?php echo $master_kampus->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $master_kampus->KecamatanID->EditAttributes() ?>>
<?php echo $master_kampus->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $master_kampus->KecamatanID->LookupFilterQuery() ?>">
</span>
<?php echo $master_kampus->KecamatanID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_kampus->DesaID->Visible) { // DesaID ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
	<div id="r_DesaID" class="form-group">
		<label id="elh_master_kampus_DesaID" for="x_DesaID" class="col-sm-2 control-label ewLabel"><?php echo $master_kampus->DesaID->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_kampus->DesaID->CellAttributes() ?>>
<span id="el_master_kampus_DesaID">
<select data-table="master_kampus" data-field="x_DesaID" data-value-separator="<?php echo $master_kampus->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $master_kampus->DesaID->EditAttributes() ?>>
<?php echo $master_kampus->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $master_kampus->DesaID->LookupFilterQuery() ?>">
</span>
<?php echo $master_kampus->DesaID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_DesaID">
		<td><span id="elh_master_kampus_DesaID"><?php echo $master_kampus->DesaID->FldCaption() ?></span></td>
		<td<?php echo $master_kampus->DesaID->CellAttributes() ?>>
<span id="el_master_kampus_DesaID">
<select data-table="master_kampus" data-field="x_DesaID" data-value-separator="<?php echo $master_kampus->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $master_kampus->DesaID->EditAttributes() ?>>
<?php echo $master_kampus->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $master_kampus->DesaID->LookupFilterQuery() ?>">
</span>
<?php echo $master_kampus->DesaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_kampus->_Email->Visible) { // Email ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
	<div id="r__Email" class="form-group">
		<label id="elh_master_kampus__Email" for="x__Email" class="col-sm-2 control-label ewLabel"><?php echo $master_kampus->_Email->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_kampus->_Email->CellAttributes() ?>>
<span id="el_master_kampus__Email">
<input type="text" data-table="master_kampus" data-field="x__Email" name="x__Email" id="x__Email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_kampus->_Email->getPlaceHolder()) ?>" value="<?php echo $master_kampus->_Email->EditValue ?>"<?php echo $master_kampus->_Email->EditAttributes() ?>>
</span>
<?php echo $master_kampus->_Email->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r__Email">
		<td><span id="elh_master_kampus__Email"><?php echo $master_kampus->_Email->FldCaption() ?></span></td>
		<td<?php echo $master_kampus->_Email->CellAttributes() ?>>
<span id="el_master_kampus__Email">
<input type="text" data-table="master_kampus" data-field="x__Email" name="x__Email" id="x__Email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_kampus->_Email->getPlaceHolder()) ?>" value="<?php echo $master_kampus->_Email->EditValue ?>"<?php echo $master_kampus->_Email->EditAttributes() ?>>
</span>
<?php echo $master_kampus->_Email->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_kampus->Telepon->Visible) { // Telepon ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
	<div id="r_Telepon" class="form-group">
		<label id="elh_master_kampus_Telepon" for="x_Telepon" class="col-sm-2 control-label ewLabel"><?php echo $master_kampus->Telepon->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_kampus->Telepon->CellAttributes() ?>>
<span id="el_master_kampus_Telepon">
<input type="text" data-table="master_kampus" data-field="x_Telepon" name="x_Telepon" id="x_Telepon" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_kampus->Telepon->getPlaceHolder()) ?>" value="<?php echo $master_kampus->Telepon->EditValue ?>"<?php echo $master_kampus->Telepon->EditAttributes() ?>>
</span>
<?php echo $master_kampus->Telepon->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Telepon">
		<td><span id="elh_master_kampus_Telepon"><?php echo $master_kampus->Telepon->FldCaption() ?></span></td>
		<td<?php echo $master_kampus->Telepon->CellAttributes() ?>>
<span id="el_master_kampus_Telepon">
<input type="text" data-table="master_kampus" data-field="x_Telepon" name="x_Telepon" id="x_Telepon" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_kampus->Telepon->getPlaceHolder()) ?>" value="<?php echo $master_kampus->Telepon->EditValue ?>"<?php echo $master_kampus->Telepon->EditAttributes() ?>>
</span>
<?php echo $master_kampus->Telepon->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_kampus->Fax->Visible) { // Fax ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
	<div id="r_Fax" class="form-group">
		<label id="elh_master_kampus_Fax" for="x_Fax" class="col-sm-2 control-label ewLabel"><?php echo $master_kampus->Fax->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_kampus->Fax->CellAttributes() ?>>
<span id="el_master_kampus_Fax">
<input type="text" data-table="master_kampus" data-field="x_Fax" name="x_Fax" id="x_Fax" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_kampus->Fax->getPlaceHolder()) ?>" value="<?php echo $master_kampus->Fax->EditValue ?>"<?php echo $master_kampus->Fax->EditAttributes() ?>>
</span>
<?php echo $master_kampus->Fax->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Fax">
		<td><span id="elh_master_kampus_Fax"><?php echo $master_kampus->Fax->FldCaption() ?></span></td>
		<td<?php echo $master_kampus->Fax->CellAttributes() ?>>
<span id="el_master_kampus_Fax">
<input type="text" data-table="master_kampus" data-field="x_Fax" name="x_Fax" id="x_Fax" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_kampus->Fax->getPlaceHolder()) ?>" value="<?php echo $master_kampus->Fax->EditValue ?>"<?php echo $master_kampus->Fax->EditAttributes() ?>>
</span>
<?php echo $master_kampus->Fax->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_kampus->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_master_kampus_NA" class="col-sm-2 control-label ewLabel"><?php echo $master_kampus->NA->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $master_kampus->NA->CellAttributes() ?>>
<span id="el_master_kampus_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_kampus" data-field="x_NA" data-value-separator="<?php echo $master_kampus->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_kampus->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_kampus->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $master_kampus->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_master_kampus_NA"><?php echo $master_kampus->NA->FldCaption() ?></span></td>
		<td<?php echo $master_kampus->NA->CellAttributes() ?>>
<span id="el_master_kampus_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_kampus" data-field="x_NA" data-value-separator="<?php echo $master_kampus->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_kampus->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_kampus->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $master_kampus->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_kampus_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$master_kampus_add->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $master_kampus_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_kampusadd.Init();
</script>
<?php
$master_kampus_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_kampus_add->Page_Terminate();
?>
