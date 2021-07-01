<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "kurikuluminfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "mkgridcls.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$kurikulum_add = NULL; // Initialize page object first

class ckurikulum_add extends ckurikulum {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'kurikulum';

	// Page object name
	var $PageObjName = 'kurikulum_add';

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

		// Table object (kurikulum)
		if (!isset($GLOBALS["kurikulum"]) || get_class($GLOBALS["kurikulum"]) == "ckurikulum") {
			$GLOBALS["kurikulum"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["kurikulum"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'kurikulum', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("kurikulumlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->KampusID->SetVisibility();
		$this->ProdiID->SetVisibility();
		$this->KurikulumKode->SetVisibility();
		$this->Nama->SetVisibility();
		$this->JmlSesi->SetVisibility();
		$this->CreateDate->SetVisibility();
		$this->Creator->SetVisibility();
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

			// Process auto fill for detail table 'mk'
			if (@$_POST["grid"] == "fmkgrid") {
				if (!isset($GLOBALS["mk_grid"])) $GLOBALS["mk_grid"] = new cmk_grid;
				$GLOBALS["mk_grid"]->Page_Init();
				$this->Page_Terminate();
				exit();
			}
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
		global $EW_EXPORT, $kurikulum;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($kurikulum);
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
			if (@$_GET["KurikulumID"] != "") {
				$this->KurikulumID->setQueryStringValue($_GET["KurikulumID"]);
				$this->setKey("KurikulumID", $this->KurikulumID->CurrentValue); // Set up key
			} else {
				$this->setKey("KurikulumID", ""); // Clear key
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

		// Set up detail parameters
		$this->SetUpDetailParms();

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
					$this->Page_Terminate("kurikulumlist.php"); // No matching record, return to list
				}

				// Set up detail parameters
				$this->SetUpDetailParms();
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					if ($this->getCurrentDetailTable() <> "") // Master/detail add
						$sReturnUrl = $this->GetDetailUrl();
					else
						$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "kurikulumlist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "kurikulumview.php")
						$sReturnUrl = $this->GetViewUrl(); // View page, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values

					// Set up detail parameters
					$this->SetUpDetailParms();
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
		$this->ProdiID->CurrentValue = NULL;
		$this->ProdiID->OldValue = $this->ProdiID->CurrentValue;
		$this->KurikulumKode->CurrentValue = NULL;
		$this->KurikulumKode->OldValue = $this->KurikulumKode->CurrentValue;
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->JmlSesi->CurrentValue = 2;
		$this->CreateDate->CurrentValue = NULL;
		$this->CreateDate->OldValue = $this->CreateDate->CurrentValue;
		$this->Creator->CurrentValue = NULL;
		$this->Creator->OldValue = $this->Creator->CurrentValue;
		$this->NA->CurrentValue = "N";
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->KampusID->FldIsDetailKey) {
			$this->KampusID->setFormValue($objForm->GetValue("x_KampusID"));
		}
		if (!$this->ProdiID->FldIsDetailKey) {
			$this->ProdiID->setFormValue($objForm->GetValue("x_ProdiID"));
		}
		if (!$this->KurikulumKode->FldIsDetailKey) {
			$this->KurikulumKode->setFormValue($objForm->GetValue("x_KurikulumKode"));
		}
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->JmlSesi->FldIsDetailKey) {
			$this->JmlSesi->setFormValue($objForm->GetValue("x_JmlSesi"));
		}
		if (!$this->CreateDate->FldIsDetailKey) {
			$this->CreateDate->setFormValue($objForm->GetValue("x_CreateDate"));
			$this->CreateDate->CurrentValue = ew_UnFormatDateTime($this->CreateDate->CurrentValue, 0);
		}
		if (!$this->Creator->FldIsDetailKey) {
			$this->Creator->setFormValue($objForm->GetValue("x_Creator"));
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
		$this->ProdiID->CurrentValue = $this->ProdiID->FormValue;
		$this->KurikulumKode->CurrentValue = $this->KurikulumKode->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->JmlSesi->CurrentValue = $this->JmlSesi->FormValue;
		$this->CreateDate->CurrentValue = $this->CreateDate->FormValue;
		$this->CreateDate->CurrentValue = ew_UnFormatDateTime($this->CreateDate->CurrentValue, 0);
		$this->Creator->CurrentValue = $this->Creator->FormValue;
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
		$this->KurikulumID->setDbValue($rs->fields('KurikulumID'));
		$this->KampusID->setDbValue($rs->fields('KampusID'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->KurikulumKode->setDbValue($rs->fields('KurikulumKode'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->JmlSesi->setDbValue($rs->fields('JmlSesi'));
		$this->CreateDate->setDbValue($rs->fields('CreateDate'));
		$this->Creator->setDbValue($rs->fields('Creator'));
		$this->EditDate->setDbValue($rs->fields('EditDate'));
		$this->Editor->setDbValue($rs->fields('Editor'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->KurikulumID->DbValue = $row['KurikulumID'];
		$this->KampusID->DbValue = $row['KampusID'];
		$this->ProdiID->DbValue = $row['ProdiID'];
		$this->KurikulumKode->DbValue = $row['KurikulumKode'];
		$this->Nama->DbValue = $row['Nama'];
		$this->JmlSesi->DbValue = $row['JmlSesi'];
		$this->CreateDate->DbValue = $row['CreateDate'];
		$this->Creator->DbValue = $row['Creator'];
		$this->EditDate->DbValue = $row['EditDate'];
		$this->Editor->DbValue = $row['Editor'];
		$this->NA->DbValue = $row['NA'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("KurikulumID")) <> "")
			$this->KurikulumID->CurrentValue = $this->getKey("KurikulumID"); // KurikulumID
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
		// KurikulumID
		// KampusID
		// ProdiID
		// KurikulumKode
		// Nama
		// JmlSesi
		// CreateDate
		// Creator
		// EditDate
		// Editor
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// KurikulumID
		$this->KurikulumID->ViewValue = $this->KurikulumID->CurrentValue;
		$this->KurikulumID->ViewCustomAttributes = "";

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
		$this->ProdiID->CssStyle = "font-weight: bold;";
		$this->ProdiID->ViewCustomAttributes = "";

		// KurikulumKode
		$this->KurikulumKode->ViewValue = $this->KurikulumKode->CurrentValue;
		$this->KurikulumKode->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// JmlSesi
		$this->JmlSesi->ViewValue = $this->JmlSesi->CurrentValue;
		$this->JmlSesi->ViewCustomAttributes = "";

		// CreateDate
		$this->CreateDate->ViewValue = $this->CreateDate->CurrentValue;
		$this->CreateDate->ViewValue = ew_FormatDateTime($this->CreateDate->ViewValue, 0);
		$this->CreateDate->ViewCustomAttributes = "";

		// Creator
		$this->Creator->ViewValue = $this->Creator->CurrentValue;
		$this->Creator->ViewCustomAttributes = "";

		// EditDate
		$this->EditDate->ViewValue = $this->EditDate->CurrentValue;
		$this->EditDate->ViewValue = ew_FormatDateTime($this->EditDate->ViewValue, 0);
		$this->EditDate->ViewCustomAttributes = "";

		// Editor
		$this->Editor->ViewValue = $this->Editor->CurrentValue;
		$this->Editor->ViewCustomAttributes = "";

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

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// KurikulumKode
			$this->KurikulumKode->LinkCustomAttributes = "";
			$this->KurikulumKode->HrefValue = "";
			$this->KurikulumKode->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// JmlSesi
			$this->JmlSesi->LinkCustomAttributes = "";
			$this->JmlSesi->HrefValue = "";
			$this->JmlSesi->TooltipValue = "";

			// CreateDate
			$this->CreateDate->LinkCustomAttributes = "";
			$this->CreateDate->HrefValue = "";
			$this->CreateDate->TooltipValue = "";

			// Creator
			$this->Creator->LinkCustomAttributes = "";
			$this->Creator->HrefValue = "";
			$this->Creator->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

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
			$sSqlWrk .= " ORDER BY `ProdiID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProdiID->EditValue = $arwrk;

			// KurikulumKode
			$this->KurikulumKode->EditAttrs["class"] = "form-control";
			$this->KurikulumKode->EditCustomAttributes = "";
			$this->KurikulumKode->EditValue = ew_HtmlEncode($this->KurikulumKode->CurrentValue);
			$this->KurikulumKode->PlaceHolder = ew_RemoveHtml($this->KurikulumKode->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// JmlSesi
			$this->JmlSesi->EditAttrs["class"] = "form-control";
			$this->JmlSesi->EditCustomAttributes = "";
			$this->JmlSesi->EditValue = ew_HtmlEncode($this->JmlSesi->CurrentValue);
			$this->JmlSesi->PlaceHolder = ew_RemoveHtml($this->JmlSesi->FldCaption());

			// CreateDate
			// Creator
			// NA

			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// KampusID

			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";

			// KurikulumKode
			$this->KurikulumKode->LinkCustomAttributes = "";
			$this->KurikulumKode->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// JmlSesi
			$this->JmlSesi->LinkCustomAttributes = "";
			$this->JmlSesi->HrefValue = "";

			// CreateDate
			$this->CreateDate->LinkCustomAttributes = "";
			$this->CreateDate->HrefValue = "";

			// Creator
			$this->Creator->LinkCustomAttributes = "";
			$this->Creator->HrefValue = "";

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
		if (!$this->ProdiID->FldIsDetailKey && !is_null($this->ProdiID->FormValue) && $this->ProdiID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->ProdiID->FldCaption(), $this->ProdiID->ReqErrMsg));
		}
		if (!$this->KurikulumKode->FldIsDetailKey && !is_null($this->KurikulumKode->FormValue) && $this->KurikulumKode->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->KurikulumKode->FldCaption(), $this->KurikulumKode->ReqErrMsg));
		}
		if (!$this->Nama->FldIsDetailKey && !is_null($this->Nama->FormValue) && $this->Nama->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Nama->FldCaption(), $this->Nama->ReqErrMsg));
		}
		if (!$this->JmlSesi->FldIsDetailKey && !is_null($this->JmlSesi->FormValue) && $this->JmlSesi->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->JmlSesi->FldCaption(), $this->JmlSesi->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->JmlSesi->FormValue)) {
			ew_AddMessage($gsFormError, $this->JmlSesi->FldErrMsg());
		}
		if ($this->NA->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->NA->FldCaption(), $this->NA->ReqErrMsg));
		}

		// Validate detail grid
		$DetailTblVar = explode(",", $this->getCurrentDetailTable());
		if (in_array("mk", $DetailTblVar) && $GLOBALS["mk"]->DetailAdd) {
			if (!isset($GLOBALS["mk_grid"])) $GLOBALS["mk_grid"] = new cmk_grid(); // get detail page object
			$GLOBALS["mk_grid"]->ValidateGridForm();
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

		// Begin transaction
		if ($this->getCurrentDetailTable() <> "")
			$conn->BeginTrans();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// KampusID
		$this->KampusID->SetDbValueDef($rsnew, $this->KampusID->CurrentValue, NULL, FALSE);

		// ProdiID
		$this->ProdiID->SetDbValueDef($rsnew, $this->ProdiID->CurrentValue, "", FALSE);

		// KurikulumKode
		$this->KurikulumKode->SetDbValueDef($rsnew, $this->KurikulumKode->CurrentValue, "", FALSE);

		// Nama
		$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, "", FALSE);

		// JmlSesi
		$this->JmlSesi->SetDbValueDef($rsnew, $this->JmlSesi->CurrentValue, NULL, strval($this->JmlSesi->CurrentValue) == "");

		// CreateDate
		$this->CreateDate->SetDbValueDef($rsnew, CurrentUserName(), NULL);
		$rsnew['CreateDate'] = &$this->CreateDate->DbValue;

		// Creator
		$this->Creator->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
		$rsnew['Creator'] = &$this->Creator->DbValue;

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

		// Add detail records
		if ($AddRow) {
			$DetailTblVar = explode(",", $this->getCurrentDetailTable());
			if (in_array("mk", $DetailTblVar) && $GLOBALS["mk"]->DetailAdd) {
				$GLOBALS["mk"]->KurikulumID->setSessionValue($this->KurikulumID->CurrentValue); // Set master key
				$GLOBALS["mk"]->ProdiID->setSessionValue($this->ProdiID->CurrentValue); // Set master key
				$GLOBALS["mk"]->KampusID->setSessionValue($this->KampusID->CurrentValue); // Set master key
				if (!isset($GLOBALS["mk_grid"])) $GLOBALS["mk_grid"] = new cmk_grid(); // Get detail page object
				$Security->LoadCurrentUserLevel($this->ProjectID . "mk"); // Load user level of detail table
				$AddRow = $GLOBALS["mk_grid"]->GridInsert();
				$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName); // Restore user level of master table
				if (!$AddRow)
					$GLOBALS["mk"]->KampusID->setSessionValue(""); // Clear master key if insert failed
			}
		}

		// Commit/Rollback transaction
		if ($this->getCurrentDetailTable() <> "") {
			if ($AddRow) {
				$conn->CommitTrans(); // Commit transaction
			} else {
				$conn->RollbackTrans(); // Rollback transaction
			}
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
	}

	// Set up detail parms based on QueryString
	function SetUpDetailParms() {

		// Get the keys for master table
		if (isset($_GET[EW_TABLE_SHOW_DETAIL])) {
			$sDetailTblVar = $_GET[EW_TABLE_SHOW_DETAIL];
			$this->setCurrentDetailTable($sDetailTblVar);
		} else {
			$sDetailTblVar = $this->getCurrentDetailTable();
		}
		if ($sDetailTblVar <> "") {
			$DetailTblVar = explode(",", $sDetailTblVar);
			if (in_array("mk", $DetailTblVar)) {
				if (!isset($GLOBALS["mk_grid"]))
					$GLOBALS["mk_grid"] = new cmk_grid;
				if ($GLOBALS["mk_grid"]->DetailAdd) {
					if ($this->CopyRecord)
						$GLOBALS["mk_grid"]->CurrentMode = "copy";
					else
						$GLOBALS["mk_grid"]->CurrentMode = "add";
					$GLOBALS["mk_grid"]->CurrentAction = "gridadd";

					// Save current master table to detail table
					$GLOBALS["mk_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["mk_grid"]->setStartRecordNumber(1);
					$GLOBALS["mk_grid"]->KurikulumID->FldIsDetailKey = TRUE;
					$GLOBALS["mk_grid"]->KurikulumID->CurrentValue = $this->KurikulumID->CurrentValue;
					$GLOBALS["mk_grid"]->KurikulumID->setSessionValue($GLOBALS["mk_grid"]->KurikulumID->CurrentValue);
					$GLOBALS["mk_grid"]->ProdiID->FldIsDetailKey = TRUE;
					$GLOBALS["mk_grid"]->ProdiID->CurrentValue = $this->ProdiID->CurrentValue;
					$GLOBALS["mk_grid"]->ProdiID->setSessionValue($GLOBALS["mk_grid"]->ProdiID->CurrentValue);
					$GLOBALS["mk_grid"]->KampusID->FldIsDetailKey = TRUE;
					$GLOBALS["mk_grid"]->KampusID->CurrentValue = $this->KampusID->CurrentValue;
					$GLOBALS["mk_grid"]->KampusID->setSessionValue($GLOBALS["mk_grid"]->KampusID->CurrentValue);
				}
			}
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("kurikulumlist.php"), "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
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
if (!isset($kurikulum_add)) $kurikulum_add = new ckurikulum_add();

// Page init
$kurikulum_add->Page_Init();

// Page main
$kurikulum_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$kurikulum_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fkurikulumadd = new ew_Form("fkurikulumadd", "add");

// Validate form
fkurikulumadd.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $kurikulum->KampusID->FldCaption(), $kurikulum->KampusID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_ProdiID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $kurikulum->ProdiID->FldCaption(), $kurikulum->ProdiID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_KurikulumKode");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $kurikulum->KurikulumKode->FldCaption(), $kurikulum->KurikulumKode->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Nama");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $kurikulum->Nama->FldCaption(), $kurikulum->Nama->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JmlSesi");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $kurikulum->JmlSesi->FldCaption(), $kurikulum->JmlSesi->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JmlSesi");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($kurikulum->JmlSesi->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_NA");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $kurikulum->NA->FldCaption(), $kurikulum->NA->ReqErrMsg)) ?>");

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
fkurikulumadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fkurikulumadd.ValidateRequired = true;
<?php } else { ?>
fkurikulumadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fkurikulumadd.Lists["x_KampusID"] = {"LinkField":"x_KampusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kampus"};
fkurikulumadd.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fkurikulumadd.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkurikulumadd.Lists["x_NA"].Options = <?php echo json_encode($kurikulum->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$kurikulum_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $kurikulum_add->ShowPageHeader(); ?>
<?php
$kurikulum_add->ShowMessage();
?>
<form name="fkurikulumadd" id="fkurikulumadd" class="<?php echo $kurikulum_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($kurikulum_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $kurikulum_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="kurikulum">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($kurikulum_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$kurikulum_add->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $kurikulum_add->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_kurikulumadd" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($kurikulum->KampusID->Visible) { // KampusID ?>
<?php if (ew_IsMobile() || $kurikulum_add->IsModal) { ?>
	<div id="r_KampusID" class="form-group">
		<label id="elh_kurikulum_KampusID" for="x_KampusID" class="col-sm-2 control-label ewLabel"><?php echo $kurikulum->KampusID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $kurikulum->KampusID->CellAttributes() ?>>
<span id="el_kurikulum_KampusID">
<select data-table="kurikulum" data-field="x_KampusID" data-value-separator="<?php echo $kurikulum->KampusID->DisplayValueSeparatorAttribute() ?>" id="x_KampusID" name="x_KampusID"<?php echo $kurikulum->KampusID->EditAttributes() ?>>
<?php echo $kurikulum->KampusID->SelectOptionListHtml("x_KampusID") ?>
</select>
<input type="hidden" name="s_x_KampusID" id="s_x_KampusID" value="<?php echo $kurikulum->KampusID->LookupFilterQuery() ?>">
</span>
<?php echo $kurikulum->KampusID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KampusID">
		<td><span id="elh_kurikulum_KampusID"><?php echo $kurikulum->KampusID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $kurikulum->KampusID->CellAttributes() ?>>
<span id="el_kurikulum_KampusID">
<select data-table="kurikulum" data-field="x_KampusID" data-value-separator="<?php echo $kurikulum->KampusID->DisplayValueSeparatorAttribute() ?>" id="x_KampusID" name="x_KampusID"<?php echo $kurikulum->KampusID->EditAttributes() ?>>
<?php echo $kurikulum->KampusID->SelectOptionListHtml("x_KampusID") ?>
</select>
<input type="hidden" name="s_x_KampusID" id="s_x_KampusID" value="<?php echo $kurikulum->KampusID->LookupFilterQuery() ?>">
</span>
<?php echo $kurikulum->KampusID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($kurikulum->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $kurikulum_add->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label id="elh_kurikulum_ProdiID" for="x_ProdiID" class="col-sm-2 control-label ewLabel"><?php echo $kurikulum->ProdiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $kurikulum->ProdiID->CellAttributes() ?>>
<span id="el_kurikulum_ProdiID">
<select data-table="kurikulum" data-field="x_ProdiID" data-value-separator="<?php echo $kurikulum->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $kurikulum->ProdiID->EditAttributes() ?>>
<?php echo $kurikulum->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $kurikulum->ProdiID->LookupFilterQuery() ?>">
</span>
<?php echo $kurikulum->ProdiID->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_kurikulum_ProdiID"><?php echo $kurikulum->ProdiID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $kurikulum->ProdiID->CellAttributes() ?>>
<span id="el_kurikulum_ProdiID">
<select data-table="kurikulum" data-field="x_ProdiID" data-value-separator="<?php echo $kurikulum->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $kurikulum->ProdiID->EditAttributes() ?>>
<?php echo $kurikulum->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $kurikulum->ProdiID->LookupFilterQuery() ?>">
</span>
<?php echo $kurikulum->ProdiID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($kurikulum->KurikulumKode->Visible) { // KurikulumKode ?>
<?php if (ew_IsMobile() || $kurikulum_add->IsModal) { ?>
	<div id="r_KurikulumKode" class="form-group">
		<label id="elh_kurikulum_KurikulumKode" for="x_KurikulumKode" class="col-sm-2 control-label ewLabel"><?php echo $kurikulum->KurikulumKode->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $kurikulum->KurikulumKode->CellAttributes() ?>>
<span id="el_kurikulum_KurikulumKode">
<input type="text" data-table="kurikulum" data-field="x_KurikulumKode" name="x_KurikulumKode" id="x_KurikulumKode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($kurikulum->KurikulumKode->getPlaceHolder()) ?>" value="<?php echo $kurikulum->KurikulumKode->EditValue ?>"<?php echo $kurikulum->KurikulumKode->EditAttributes() ?>>
</span>
<?php echo $kurikulum->KurikulumKode->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_KurikulumKode">
		<td><span id="elh_kurikulum_KurikulumKode"><?php echo $kurikulum->KurikulumKode->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $kurikulum->KurikulumKode->CellAttributes() ?>>
<span id="el_kurikulum_KurikulumKode">
<input type="text" data-table="kurikulum" data-field="x_KurikulumKode" name="x_KurikulumKode" id="x_KurikulumKode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($kurikulum->KurikulumKode->getPlaceHolder()) ?>" value="<?php echo $kurikulum->KurikulumKode->EditValue ?>"<?php echo $kurikulum->KurikulumKode->EditAttributes() ?>>
</span>
<?php echo $kurikulum->KurikulumKode->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($kurikulum->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $kurikulum_add->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label id="elh_kurikulum_Nama" for="x_Nama" class="col-sm-2 control-label ewLabel"><?php echo $kurikulum->Nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $kurikulum->Nama->CellAttributes() ?>>
<span id="el_kurikulum_Nama">
<input type="text" data-table="kurikulum" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($kurikulum->Nama->getPlaceHolder()) ?>" value="<?php echo $kurikulum->Nama->EditValue ?>"<?php echo $kurikulum->Nama->EditAttributes() ?>>
</span>
<?php echo $kurikulum->Nama->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_kurikulum_Nama"><?php echo $kurikulum->Nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $kurikulum->Nama->CellAttributes() ?>>
<span id="el_kurikulum_Nama">
<input type="text" data-table="kurikulum" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($kurikulum->Nama->getPlaceHolder()) ?>" value="<?php echo $kurikulum->Nama->EditValue ?>"<?php echo $kurikulum->Nama->EditAttributes() ?>>
</span>
<?php echo $kurikulum->Nama->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($kurikulum->JmlSesi->Visible) { // JmlSesi ?>
<?php if (ew_IsMobile() || $kurikulum_add->IsModal) { ?>
	<div id="r_JmlSesi" class="form-group">
		<label id="elh_kurikulum_JmlSesi" for="x_JmlSesi" class="col-sm-2 control-label ewLabel"><?php echo $kurikulum->JmlSesi->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $kurikulum->JmlSesi->CellAttributes() ?>>
<span id="el_kurikulum_JmlSesi">
<input type="text" data-table="kurikulum" data-field="x_JmlSesi" name="x_JmlSesi" id="x_JmlSesi" size="30" placeholder="<?php echo ew_HtmlEncode($kurikulum->JmlSesi->getPlaceHolder()) ?>" value="<?php echo $kurikulum->JmlSesi->EditValue ?>"<?php echo $kurikulum->JmlSesi->EditAttributes() ?>>
</span>
<?php echo $kurikulum->JmlSesi->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_JmlSesi">
		<td><span id="elh_kurikulum_JmlSesi"><?php echo $kurikulum->JmlSesi->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $kurikulum->JmlSesi->CellAttributes() ?>>
<span id="el_kurikulum_JmlSesi">
<input type="text" data-table="kurikulum" data-field="x_JmlSesi" name="x_JmlSesi" id="x_JmlSesi" size="30" placeholder="<?php echo ew_HtmlEncode($kurikulum->JmlSesi->getPlaceHolder()) ?>" value="<?php echo $kurikulum->JmlSesi->EditValue ?>"<?php echo $kurikulum->JmlSesi->EditAttributes() ?>>
</span>
<?php echo $kurikulum->JmlSesi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($kurikulum->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $kurikulum_add->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label id="elh_kurikulum_NA" class="col-sm-2 control-label ewLabel"><?php echo $kurikulum->NA->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $kurikulum->NA->CellAttributes() ?>>
<span id="el_kurikulum_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="kurikulum" data-field="x_NA" data-value-separator="<?php echo $kurikulum->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $kurikulum->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $kurikulum->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $kurikulum->NA->CustomMsg ?></div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_kurikulum_NA"><?php echo $kurikulum->NA->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $kurikulum->NA->CellAttributes() ?>>
<span id="el_kurikulum_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="kurikulum" data-field="x_NA" data-value-separator="<?php echo $kurikulum->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $kurikulum->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $kurikulum->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
<?php echo $kurikulum->NA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $kurikulum_add->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php
	if (in_array("mk", explode(",", $kurikulum->getCurrentDetailTable())) && $mk->DetailAdd) {
?>
<?php if ($kurikulum->getCurrentDetailTable() <> "") { ?>
<h4 class="ewDetailCaption"><?php echo $Language->TablePhrase("mk", "TblCaption") ?></h4>
<?php } ?>
<?php include_once "mkgrid.php" ?>
<?php } ?>
<?php if (!$kurikulum_add->IsModal) { ?>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $kurikulum_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fkurikulumadd.Init();
</script>
<?php
$kurikulum_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$kurikulum_add->Page_Terminate();
?>
