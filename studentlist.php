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

$student_list = NULL; // Initialize page object first

class cstudent_list extends cstudent {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'student';

	// Page object name
	var $PageObjName = 'student_list';

	// Grid form hidden field names
	var $FormName = 'fstudentlist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Custom export
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "studentadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "studentdelete.php";
		$this->MultiUpdateUrl = "studentupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

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

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";

		// Filter options
		$this->FilterOptions = new cListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption fstudentlistsrch";

		// List actions
		$this->ListActions = new cListActions();
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
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			$this->Page_Terminate(ew_GetUrl("index.php"));
		}

		// Create form object
		$objForm = new cFormObj();

		// Get export parameters
		$custom = "";
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
			$custom = @$_GET["custom"];
		} elseif (@$_POST["export"] <> "") {
			$this->Export = $_POST["export"];
			$custom = @$_POST["custom"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
			$custom = @$_POST["custom"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExportFile = $this->TableVar; // Get export file, used in header

		// Get custom export parameters
		if ($this->Export <> "" && $custom <> "") {
			$this->CustomExport = $this->Export;
			$this->Export = "print";
		}
		$gsCustomExport = $this->CustomExport;
		$gsExport = $this->Export; // Get export parameter, used in header

		// Update Export URLs
		if (defined("EW_USE_PHPEXCEL"))
			$this->ExportExcelCustom = FALSE;
		if ($this->ExportExcelCustom)
			$this->ExportExcelUrl .= "&amp;custom=1";
		if (defined("EW_USE_PHPWORD"))
			$this->ExportWordCustom = FALSE;
		if ($this->ExportWordCustom)
			$this->ExportWordUrl .= "&amp;custom=1";
		if ($this->ExportPdfCustom)
			$this->ExportPdfUrl .= "&amp;custom=1";
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();
		$this->StudentID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->ProdiID->SetVisibility();
		$this->StudentStatusID->SetVisibility();
		$this->TahunID->SetVisibility();
		$this->Foto->SetVisibility();
		$this->NIK->SetVisibility();
		$this->Kelamin->SetVisibility();
		$this->TempatLahir->SetVisibility();
		$this->TanggalLahir->SetVisibility();
		$this->AlamatDomisili->SetVisibility();
		$this->Telepon->SetVisibility();
		$this->_Email->SetVisibility();
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

		// Setup other options
		$this->SetupOtherOptions();

		// Set up custom action (compatible with old version)
		foreach ($this->CustomActions as $name => $action)
			$this->ListActions->Add($name, $action);

		// Show checkbox column if multiple action
		foreach ($this->ListActions->Items as $listaction) {
			if ($listaction->Select == EW_ACTION_MULTIPLE && $listaction->Allow) {
				$this->ListOptions->Items["checkbox"]->Visible = TRUE;
				break;
			}
		}
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
			header("Location: " . $url);
		}
		exit();
	}

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $OtherOptions = array(); // Other options
	var $FilterOptions; // Filter options
	var $ListActions; // List actions
	var $SelectedCount = 0;
	var $SelectedIndex = 0;
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $DefaultSearchWhere = ""; // Default search WHERE clause
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $MultiColumnClass;
	var $MultiColumnEditClass = "col-sm-12";
	var $MultiColumnCnt = 12;
	var $MultiColumnEditCnt = 12;
	var $GridCnt = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $DetailPages;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process list action first
			if ($this->ProcessListAction()) // Ajax request
				$this->Page_Terminate();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			if ($this->Export == "")
				$this->SetupBreadcrumb();

			// Check QueryString parameters
			if (@$_GET["a"] <> "") {
				$this->CurrentAction = $_GET["a"];

				// Clear inline mode
				if ($this->CurrentAction == "cancel")
					$this->ClearInlineMode();

				// Switch to grid edit mode
				if ($this->CurrentAction == "gridedit")
					$this->GridEditMode();

				// Switch to grid add mode
				if ($this->CurrentAction == "gridadd")
					$this->GridAddMode();
			} else {
				if (@$_POST["a_list"] <> "") {
					$this->CurrentAction = $_POST["a_list"]; // Get action

					// Grid Update
					if (($this->CurrentAction == "gridupdate" || $this->CurrentAction == "gridoverwrite") && @$_SESSION[EW_SESSION_INLINE_MODE] == "gridedit") {
						if ($this->ValidateGridForm()) {
							$bGridUpdate = $this->GridUpdate();
						} else {
							$bGridUpdate = FALSE;
							$this->setFailureMessage($gsFormError);
						}
						if (!$bGridUpdate) {
							$this->EventCancelled = TRUE;
							$this->CurrentAction = "gridedit"; // Stay in Grid Edit mode
						}
					}

					// Grid Insert
					if ($this->CurrentAction == "gridinsert" && @$_SESSION[EW_SESSION_INLINE_MODE] == "gridadd") {
						if ($this->ValidateGridForm()) {
							$bGridInsert = $this->GridInsert();
						} else {
							$bGridInsert = FALSE;
							$this->setFailureMessage($gsFormError);
						}
						if (!$bGridInsert) {
							$this->EventCancelled = TRUE;
							$this->CurrentAction = "gridadd"; // Stay in Grid Add mode
						}
					}
				}
			}

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide options
			if ($this->Export <> "" || $this->CurrentAction <> "") {
				$this->ExportOptions->HideAllOptions();
				$this->FilterOptions->HideAllOptions();
			}

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Show grid delete link for grid add / grid edit
			if ($this->AllowAddDeleteRow) {
				if ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
					$item = $this->ListOptions->GetItem("griddelete");
					if ($item) $item->Visible = TRUE;
				}
			}

			// Get default search criteria
			ew_AddFilter($this->DefaultSearchWhere, $this->BasicSearchWhere(TRUE));
			ew_AddFilter($this->DefaultSearchWhere, $this->AdvancedSearchWhere(TRUE));

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Get and validate search values for advanced search
			$this->LoadSearchValues(); // Get search values

			// Process filter list
			$this->ProcessFilterList();
			if (!$this->ValidateSearch())
				$this->setFailureMessage($gsSearchError);

			// Restore search parms from Session if not searching / reset / export
			if (($this->Export <> "" || $this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall") && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Get search criteria for advanced search
			if ($gsSearchError == "")
				$sSrchAdvanced = $this->AdvancedSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Load advanced search from default
			if ($this->LoadAdvancedSearchDefault()) {
				$sSrchAdvanced = $this->AdvancedSearchWhere();
			}
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if ($this->CustomExport == "" && in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			$this->Page_Terminate(); // Terminate response
			exit();
		}

		// Load record count first
		if (!$this->IsAddOrEdit()) {
			$bSelectLimit = $this->UseSelectLimit;
			if ($bSelectLimit) {
				$this->TotalRecs = $this->SelectRecordCount();
			} else {
				if ($this->Recordset = $this->LoadRecordset())
					$this->TotalRecs = $this->Recordset->RecordCount();
			}
		}

		// Search options
		$this->SetupSearchOptions();
	}

	//  Exit inline mode
	function ClearInlineMode() {
		$this->LastAction = $this->CurrentAction; // Save last action
		$this->CurrentAction = ""; // Clear action
		$_SESSION[EW_SESSION_INLINE_MODE] = ""; // Clear inline mode
	}

	// Switch to Grid Add mode
	function GridAddMode() {
		$_SESSION[EW_SESSION_INLINE_MODE] = "gridadd"; // Enabled grid add
	}

	// Switch to Grid Edit mode
	function GridEditMode() {
		$_SESSION[EW_SESSION_INLINE_MODE] = "gridedit"; // Enable grid edit
	}

	// Perform update to grid
	function GridUpdate() {
		global $Language, $objForm, $gsFormError;
		$bGridUpdate = TRUE;

		// Get old recordset
		$this->CurrentFilter = $this->BuildKeyFilter();
		if ($this->CurrentFilter == "")
			$this->CurrentFilter = "0=1";
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		if ($rs = $conn->Execute($sSql)) {
			$rsold = $rs->GetRows();
			$rs->Close();
		}

		// Call Grid Updating event
		if (!$this->Grid_Updating($rsold)) {
			if ($this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("GridEditCancelled")); // Set grid edit cancelled message
			return FALSE;
		}

		// Begin transaction
		$conn->BeginTrans();
		if ($this->AuditTrailOnEdit) $this->WriteAuditTrailDummy($Language->Phrase("BatchUpdateBegin")); // Batch update begin
		$sKey = "";

		// Update row index and get row key
		$objForm->Index = -1;
		$rowcnt = strval($objForm->GetValue($this->FormKeyCountName));
		if ($rowcnt == "" || !is_numeric($rowcnt))
			$rowcnt = 0;

		// Update all rows based on key
		for ($rowindex = 1; $rowindex <= $rowcnt; $rowindex++) {
			$objForm->Index = $rowindex;
			$rowkey = strval($objForm->GetValue($this->FormKeyName));
			$rowaction = strval($objForm->GetValue($this->FormActionName));

			// Load all values and keys
			if ($rowaction <> "insertdelete") { // Skip insert then deleted rows
				$this->LoadFormValues(); // Get form values
				if ($rowaction == "" || $rowaction == "edit" || $rowaction == "delete") {
					$bGridUpdate = $this->SetupKeyValues($rowkey); // Set up key values
				} else {
					$bGridUpdate = TRUE;
				}

				// Skip empty row
				if ($rowaction == "insert" && $this->EmptyRow()) {

					// No action required
				// Validate form and insert/update/delete record

				} elseif ($bGridUpdate) {
					if ($rowaction == "delete") {
						$this->CurrentFilter = $this->KeyFilter();
						$bGridUpdate = $this->DeleteRows(); // Delete this row
					} else if (!$this->ValidateForm()) {
						$bGridUpdate = FALSE; // Form error, reset action
						$this->setFailureMessage($gsFormError);
					} else {
						if ($rowaction == "insert") {
							$bGridUpdate = $this->AddRow(); // Insert this row
						} else {
							if ($rowkey <> "") {
								$this->SendEmail = FALSE; // Do not send email on update success
								$bGridUpdate = $this->EditRow(); // Update this row
							}
						} // End update
					}
				}
				if ($bGridUpdate) {
					if ($sKey <> "") $sKey .= ", ";
					$sKey .= $rowkey;
				} else {
					break;
				}
			}
		}
		if ($bGridUpdate) {
			$conn->CommitTrans(); // Commit transaction

			// Get new recordset
			if ($rs = $conn->Execute($sSql)) {
				$rsnew = $rs->GetRows();
				$rs->Close();
			}

			// Call Grid_Updated event
			$this->Grid_Updated($rsold, $rsnew);
			if ($this->AuditTrailOnEdit) $this->WriteAuditTrailDummy($Language->Phrase("BatchUpdateSuccess")); // Batch update success
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Set up update success message
			$this->ClearInlineMode(); // Clear inline edit mode
		} else {
			$conn->RollbackTrans(); // Rollback transaction
			if ($this->AuditTrailOnEdit) $this->WriteAuditTrailDummy($Language->Phrase("BatchUpdateRollback")); // Batch update rollback
			if ($this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("UpdateFailed")); // Set update failed message
		}
		return $bGridUpdate;
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->StudentID->setFormValue($arrKeyFlds[0]);
		}
		return TRUE;
	}

	// Perform Grid Add
	function GridInsert() {
		global $Language, $objForm, $gsFormError;
		$rowindex = 1;
		$bGridInsert = FALSE;
		$conn = &$this->Connection();

		// Call Grid Inserting event
		if (!$this->Grid_Inserting()) {
			if ($this->getFailureMessage() == "") {
				$this->setFailureMessage($Language->Phrase("GridAddCancelled")); // Set grid add cancelled message
			}
			return FALSE;
		}

		// Begin transaction
		$conn->BeginTrans();

		// Init key filter
		$sWrkFilter = "";
		$addcnt = 0;
		if ($this->AuditTrailOnAdd) $this->WriteAuditTrailDummy($Language->Phrase("BatchInsertBegin")); // Batch insert begin
		$sKey = "";

		// Get row count
		$objForm->Index = -1;
		$rowcnt = strval($objForm->GetValue($this->FormKeyCountName));
		if ($rowcnt == "" || !is_numeric($rowcnt))
			$rowcnt = 0;

		// Insert all rows
		for ($rowindex = 1; $rowindex <= $rowcnt; $rowindex++) {

			// Load current row values
			$objForm->Index = $rowindex;
			$rowaction = strval($objForm->GetValue($this->FormActionName));
			if ($rowaction <> "" && $rowaction <> "insert")
				continue; // Skip
			$this->LoadFormValues(); // Get form values
			if (!$this->EmptyRow()) {
				$addcnt++;
				$this->SendEmail = FALSE; // Do not send email on insert success

				// Validate form
				if (!$this->ValidateForm()) {
					$bGridInsert = FALSE; // Form error, reset action
					$this->setFailureMessage($gsFormError);
				} else {
					$bGridInsert = $this->AddRow($this->OldRecordset); // Insert this row
				}
				if ($bGridInsert) {
					if ($sKey <> "") $sKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
					$sKey .= $this->StudentID->CurrentValue;

					// Add filter for this record
					$sFilter = $this->KeyFilter();
					if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
					$sWrkFilter .= $sFilter;
				} else {
					break;
				}
			}
		}
		if ($addcnt == 0) { // No record inserted
			$this->setFailureMessage($Language->Phrase("NoAddRecord"));
			$bGridInsert = FALSE;
		}
		if ($bGridInsert) {
			$conn->CommitTrans(); // Commit transaction

			// Get new recordset
			$this->CurrentFilter = $sWrkFilter;
			$sSql = $this->SQL();
			if ($rs = $conn->Execute($sSql)) {
				$rsnew = $rs->GetRows();
				$rs->Close();
			}

			// Call Grid_Inserted event
			$this->Grid_Inserted($rsnew);
			if ($this->AuditTrailOnAdd) $this->WriteAuditTrailDummy($Language->Phrase("BatchInsertSuccess")); // Batch insert success
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("InsertSuccess")); // Set up insert success message
			$this->ClearInlineMode(); // Clear grid add mode
		} else {
			$conn->RollbackTrans(); // Rollback transaction
			if ($this->AuditTrailOnAdd) $this->WriteAuditTrailDummy($Language->Phrase("BatchInsertRollback")); // Batch insert rollback
			if ($this->getFailureMessage() == "") {
				$this->setFailureMessage($Language->Phrase("InsertFailed")); // Set insert failed message
			}
		}
		return $bGridInsert;
	}

	// Check if empty row
	function EmptyRow() {
		global $objForm;
		if ($objForm->HasValue("x_StudentID") && $objForm->HasValue("o_StudentID") && $this->StudentID->CurrentValue <> $this->StudentID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_Nama") && $objForm->HasValue("o_Nama") && $this->Nama->CurrentValue <> $this->Nama->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_ProdiID") && $objForm->HasValue("o_ProdiID") && $this->ProdiID->CurrentValue <> $this->ProdiID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_StudentStatusID") && $objForm->HasValue("o_StudentStatusID") && $this->StudentStatusID->CurrentValue <> $this->StudentStatusID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_TahunID") && $objForm->HasValue("o_TahunID") && $this->TahunID->CurrentValue <> $this->TahunID->OldValue)
			return FALSE;
		if (!ew_Empty($this->Foto->Upload->Value))
			return FALSE;
		if ($objForm->HasValue("x_NIK") && $objForm->HasValue("o_NIK") && $this->NIK->CurrentValue <> $this->NIK->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_Kelamin") && $objForm->HasValue("o_Kelamin") && $this->Kelamin->CurrentValue <> $this->Kelamin->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_TempatLahir") && $objForm->HasValue("o_TempatLahir") && $this->TempatLahir->CurrentValue <> $this->TempatLahir->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_TanggalLahir") && $objForm->HasValue("o_TanggalLahir") && $this->TanggalLahir->CurrentValue <> $this->TanggalLahir->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_AlamatDomisili") && $objForm->HasValue("o_AlamatDomisili") && $this->AlamatDomisili->CurrentValue <> $this->AlamatDomisili->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_Telepon") && $objForm->HasValue("o_Telepon") && $this->Telepon->CurrentValue <> $this->Telepon->OldValue)
			return FALSE;
		if ($objForm->HasValue("x__Email") && $objForm->HasValue("o__Email") && $this->_Email->CurrentValue <> $this->_Email->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_NA") && $objForm->HasValue("o_NA") && ew_ConvertToBool($this->NA->CurrentValue) <> ew_ConvertToBool($this->NA->OldValue))
			return FALSE;
		return TRUE;
	}

	// Validate grid form
	function ValidateGridForm() {
		global $objForm;

		// Get row count
		$objForm->Index = -1;
		$rowcnt = strval($objForm->GetValue($this->FormKeyCountName));
		if ($rowcnt == "" || !is_numeric($rowcnt))
			$rowcnt = 0;

		// Validate all records
		for ($rowindex = 1; $rowindex <= $rowcnt; $rowindex++) {

			// Load current row values
			$objForm->Index = $rowindex;
			$rowaction = strval($objForm->GetValue($this->FormActionName));
			if ($rowaction <> "delete" && $rowaction <> "insertdelete") {
				$this->LoadFormValues(); // Get form values
				if ($rowaction == "insert" && $this->EmptyRow()) {

					// Ignore
				} else if (!$this->ValidateForm()) {
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	// Get all form values of the grid
	function GetGridFormValues() {
		global $objForm;

		// Get row count
		$objForm->Index = -1;
		$rowcnt = strval($objForm->GetValue($this->FormKeyCountName));
		if ($rowcnt == "" || !is_numeric($rowcnt))
			$rowcnt = 0;
		$rows = array();

		// Loop through all records
		for ($rowindex = 1; $rowindex <= $rowcnt; $rowindex++) {

			// Load current row values
			$objForm->Index = $rowindex;
			$rowaction = strval($objForm->GetValue($this->FormActionName));
			if ($rowaction <> "delete" && $rowaction <> "insertdelete") {
				$this->LoadFormValues(); // Get form values
				if ($rowaction == "insert" && $this->EmptyRow()) {

					// Ignore
				} else {
					$rows[] = $this->GetFieldValues("FormValue"); // Return row as array
				}
			}
		}
		return $rows; // Return as array of array
	}

	// Restore form values for current row
	function RestoreCurrentRowFormValues($idx) {
		global $objForm;

		// Get row based on current index
		$objForm->Index = $idx;
		$this->LoadFormValues(); // Load form values
	}

	// Get list of filters
	function GetFilterList() {
		global $UserProfile;

		// Load server side filters
		if (EW_SEARCH_FILTER_OPTION == "Server") {
			$sSavedFilterList = isset($UserProfile) ? $UserProfile->GetSearchFilters(CurrentUserName(), "fstudentlistsrch") : "";
		} else {
			$sSavedFilterList = "";
		}

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->StudentID->AdvancedSearch->ToJSON(), ","); // Field StudentID
		$sFilterList = ew_Concat($sFilterList, $this->Nama->AdvancedSearch->ToJSON(), ","); // Field Nama
		$sFilterList = ew_Concat($sFilterList, $this->LevelID->AdvancedSearch->ToJSON(), ","); // Field LevelID
		$sFilterList = ew_Concat($sFilterList, $this->ProdiID->AdvancedSearch->ToJSON(), ","); // Field ProdiID
		$sFilterList = ew_Concat($sFilterList, $this->StudentStatusID->AdvancedSearch->ToJSON(), ","); // Field StudentStatusID
		$sFilterList = ew_Concat($sFilterList, $this->TahunID->AdvancedSearch->ToJSON(), ","); // Field TahunID
		$sFilterList = ew_Concat($sFilterList, $this->Foto->AdvancedSearch->ToJSON(), ","); // Field Foto
		$sFilterList = ew_Concat($sFilterList, $this->NIK->AdvancedSearch->ToJSON(), ","); // Field NIK
		$sFilterList = ew_Concat($sFilterList, $this->WargaNegara->AdvancedSearch->ToJSON(), ","); // Field WargaNegara
		$sFilterList = ew_Concat($sFilterList, $this->Kelamin->AdvancedSearch->ToJSON(), ","); // Field Kelamin
		$sFilterList = ew_Concat($sFilterList, $this->TempatLahir->AdvancedSearch->ToJSON(), ","); // Field TempatLahir
		$sFilterList = ew_Concat($sFilterList, $this->TanggalLahir->AdvancedSearch->ToJSON(), ","); // Field TanggalLahir
		$sFilterList = ew_Concat($sFilterList, $this->AgamaID->AdvancedSearch->ToJSON(), ","); // Field AgamaID
		$sFilterList = ew_Concat($sFilterList, $this->Darah->AdvancedSearch->ToJSON(), ","); // Field Darah
		$sFilterList = ew_Concat($sFilterList, $this->StatusSipil->AdvancedSearch->ToJSON(), ","); // Field StatusSipil
		$sFilterList = ew_Concat($sFilterList, $this->AlamatDomisili->AdvancedSearch->ToJSON(), ","); // Field AlamatDomisili
		$sFilterList = ew_Concat($sFilterList, $this->RT->AdvancedSearch->ToJSON(), ","); // Field RT
		$sFilterList = ew_Concat($sFilterList, $this->RW->AdvancedSearch->ToJSON(), ","); // Field RW
		$sFilterList = ew_Concat($sFilterList, $this->KodePos->AdvancedSearch->ToJSON(), ","); // Field KodePos
		$sFilterList = ew_Concat($sFilterList, $this->ProvinsiID->AdvancedSearch->ToJSON(), ","); // Field ProvinsiID
		$sFilterList = ew_Concat($sFilterList, $this->KabupatenKotaID->AdvancedSearch->ToJSON(), ","); // Field KabupatenKotaID
		$sFilterList = ew_Concat($sFilterList, $this->KecamatanID->AdvancedSearch->ToJSON(), ","); // Field KecamatanID
		$sFilterList = ew_Concat($sFilterList, $this->DesaID->AdvancedSearch->ToJSON(), ","); // Field DesaID
		$sFilterList = ew_Concat($sFilterList, $this->AnakKe->AdvancedSearch->ToJSON(), ","); // Field AnakKe
		$sFilterList = ew_Concat($sFilterList, $this->JumlahSaudara->AdvancedSearch->ToJSON(), ","); // Field JumlahSaudara
		$sFilterList = ew_Concat($sFilterList, $this->Telepon->AdvancedSearch->ToJSON(), ","); // Field Telepon
		$sFilterList = ew_Concat($sFilterList, $this->_Email->AdvancedSearch->ToJSON(), ","); // Field Email
		$sFilterList = ew_Concat($sFilterList, $this->NamaAyah->AdvancedSearch->ToJSON(), ","); // Field NamaAyah
		$sFilterList = ew_Concat($sFilterList, $this->AgamaAyah->AdvancedSearch->ToJSON(), ","); // Field AgamaAyah
		$sFilterList = ew_Concat($sFilterList, $this->PendidikanAyah->AdvancedSearch->ToJSON(), ","); // Field PendidikanAyah
		$sFilterList = ew_Concat($sFilterList, $this->PekerjaanAyah->AdvancedSearch->ToJSON(), ","); // Field PekerjaanAyah
		$sFilterList = ew_Concat($sFilterList, $this->HidupAyah->AdvancedSearch->ToJSON(), ","); // Field HidupAyah
		$sFilterList = ew_Concat($sFilterList, $this->NamaIbu->AdvancedSearch->ToJSON(), ","); // Field NamaIbu
		$sFilterList = ew_Concat($sFilterList, $this->AgamaIbu->AdvancedSearch->ToJSON(), ","); // Field AgamaIbu
		$sFilterList = ew_Concat($sFilterList, $this->PendidikanIbu->AdvancedSearch->ToJSON(), ","); // Field PendidikanIbu
		$sFilterList = ew_Concat($sFilterList, $this->PekerjaanIbu->AdvancedSearch->ToJSON(), ","); // Field PekerjaanIbu
		$sFilterList = ew_Concat($sFilterList, $this->HidupIbu->AdvancedSearch->ToJSON(), ","); // Field HidupIbu
		$sFilterList = ew_Concat($sFilterList, $this->AlamatOrtu->AdvancedSearch->ToJSON(), ","); // Field AlamatOrtu
		$sFilterList = ew_Concat($sFilterList, $this->RTOrtu->AdvancedSearch->ToJSON(), ","); // Field RTOrtu
		$sFilterList = ew_Concat($sFilterList, $this->RWOrtu->AdvancedSearch->ToJSON(), ","); // Field RWOrtu
		$sFilterList = ew_Concat($sFilterList, $this->KodePosOrtu->AdvancedSearch->ToJSON(), ","); // Field KodePosOrtu
		$sFilterList = ew_Concat($sFilterList, $this->ProvinsiIDOrtu->AdvancedSearch->ToJSON(), ","); // Field ProvinsiIDOrtu
		$sFilterList = ew_Concat($sFilterList, $this->KabupatenIDOrtu->AdvancedSearch->ToJSON(), ","); // Field KabupatenIDOrtu
		$sFilterList = ew_Concat($sFilterList, $this->KecamatanIDOrtu->AdvancedSearch->ToJSON(), ","); // Field KecamatanIDOrtu
		$sFilterList = ew_Concat($sFilterList, $this->DesaIDOrtu->AdvancedSearch->ToJSON(), ","); // Field DesaIDOrtu
		$sFilterList = ew_Concat($sFilterList, $this->NegaraIDOrtu->AdvancedSearch->ToJSON(), ","); // Field NegaraIDOrtu
		$sFilterList = ew_Concat($sFilterList, $this->TeleponOrtu->AdvancedSearch->ToJSON(), ","); // Field TeleponOrtu
		$sFilterList = ew_Concat($sFilterList, $this->HandphoneOrtu->AdvancedSearch->ToJSON(), ","); // Field HandphoneOrtu
		$sFilterList = ew_Concat($sFilterList, $this->EmailOrtu->AdvancedSearch->ToJSON(), ","); // Field EmailOrtu
		$sFilterList = ew_Concat($sFilterList, $this->AsalSekolah->AdvancedSearch->ToJSON(), ","); // Field AsalSekolah
		$sFilterList = ew_Concat($sFilterList, $this->AlamatSekolah->AdvancedSearch->ToJSON(), ","); // Field AlamatSekolah
		$sFilterList = ew_Concat($sFilterList, $this->ProvinsiIDSekolah->AdvancedSearch->ToJSON(), ","); // Field ProvinsiIDSekolah
		$sFilterList = ew_Concat($sFilterList, $this->KabupatenIDSekolah->AdvancedSearch->ToJSON(), ","); // Field KabupatenIDSekolah
		$sFilterList = ew_Concat($sFilterList, $this->KecamatanIDSekolah->AdvancedSearch->ToJSON(), ","); // Field KecamatanIDSekolah
		$sFilterList = ew_Concat($sFilterList, $this->DesaIDSekolah->AdvancedSearch->ToJSON(), ","); // Field DesaIDSekolah
		$sFilterList = ew_Concat($sFilterList, $this->NilaiSekolah->AdvancedSearch->ToJSON(), ","); // Field NilaiSekolah
		$sFilterList = ew_Concat($sFilterList, $this->TahunLulus->AdvancedSearch->ToJSON(), ","); // Field TahunLulus
		$sFilterList = ew_Concat($sFilterList, $this->IjazahSekolah->AdvancedSearch->ToJSON(), ","); // Field IjazahSekolah
		$sFilterList = ew_Concat($sFilterList, $this->TglIjazah->AdvancedSearch->ToJSON(), ","); // Field TglIjazah
		$sFilterList = ew_Concat($sFilterList, $this->LockStatus->AdvancedSearch->ToJSON(), ","); // Field LockStatus
		$sFilterList = ew_Concat($sFilterList, $this->NA->AdvancedSearch->ToJSON(), ","); // Field NA
		if ($this->BasicSearch->Keyword <> "") {
			$sWrk = "\"" . EW_TABLE_BASIC_SEARCH . "\":\"" . ew_JsEncode2($this->BasicSearch->Keyword) . "\",\"" . EW_TABLE_BASIC_SEARCH_TYPE . "\":\"" . ew_JsEncode2($this->BasicSearch->Type) . "\"";
			$sFilterList = ew_Concat($sFilterList, $sWrk, ",");
		}
		$sFilterList = preg_replace('/,$/', "", $sFilterList);

		// Return filter list in json
		if ($sFilterList <> "")
			$sFilterList = "\"data\":{" . $sFilterList . "}";
		if ($sSavedFilterList <> "") {
			if ($sFilterList <> "")
				$sFilterList .= ",";
			$sFilterList .= "\"filters\":" . $sSavedFilterList;
		}
		return ($sFilterList <> "") ? "{" . $sFilterList . "}" : "null";
	}

	// Process filter list
	function ProcessFilterList() {
		global $UserProfile;
		if (@$_POST["ajax"] == "savefilters") { // Save filter request (Ajax)
			$filters = ew_StripSlashes(@$_POST["filters"]);
			$UserProfile->SetSearchFilters(CurrentUserName(), "fstudentlistsrch", $filters);

			// Clean output buffer
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			echo ew_ArrayToJson(array(array("success" => TRUE))); // Success
			$this->Page_Terminate();
			exit();
		} elseif (@$_POST["cmd"] == "resetfilter") {
			$this->RestoreFilterList();
		}
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(ew_StripSlashes(@$_POST["filter"]), TRUE);
		$this->Command = "search";

		// Field StudentID
		$this->StudentID->AdvancedSearch->SearchValue = @$filter["x_StudentID"];
		$this->StudentID->AdvancedSearch->SearchOperator = @$filter["z_StudentID"];
		$this->StudentID->AdvancedSearch->SearchCondition = @$filter["v_StudentID"];
		$this->StudentID->AdvancedSearch->SearchValue2 = @$filter["y_StudentID"];
		$this->StudentID->AdvancedSearch->SearchOperator2 = @$filter["w_StudentID"];
		$this->StudentID->AdvancedSearch->Save();

		// Field Nama
		$this->Nama->AdvancedSearch->SearchValue = @$filter["x_Nama"];
		$this->Nama->AdvancedSearch->SearchOperator = @$filter["z_Nama"];
		$this->Nama->AdvancedSearch->SearchCondition = @$filter["v_Nama"];
		$this->Nama->AdvancedSearch->SearchValue2 = @$filter["y_Nama"];
		$this->Nama->AdvancedSearch->SearchOperator2 = @$filter["w_Nama"];
		$this->Nama->AdvancedSearch->Save();

		// Field LevelID
		$this->LevelID->AdvancedSearch->SearchValue = @$filter["x_LevelID"];
		$this->LevelID->AdvancedSearch->SearchOperator = @$filter["z_LevelID"];
		$this->LevelID->AdvancedSearch->SearchCondition = @$filter["v_LevelID"];
		$this->LevelID->AdvancedSearch->SearchValue2 = @$filter["y_LevelID"];
		$this->LevelID->AdvancedSearch->SearchOperator2 = @$filter["w_LevelID"];
		$this->LevelID->AdvancedSearch->Save();

		// Field ProdiID
		$this->ProdiID->AdvancedSearch->SearchValue = @$filter["x_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchOperator = @$filter["z_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchCondition = @$filter["v_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchValue2 = @$filter["y_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchOperator2 = @$filter["w_ProdiID"];
		$this->ProdiID->AdvancedSearch->Save();

		// Field StudentStatusID
		$this->StudentStatusID->AdvancedSearch->SearchValue = @$filter["x_StudentStatusID"];
		$this->StudentStatusID->AdvancedSearch->SearchOperator = @$filter["z_StudentStatusID"];
		$this->StudentStatusID->AdvancedSearch->SearchCondition = @$filter["v_StudentStatusID"];
		$this->StudentStatusID->AdvancedSearch->SearchValue2 = @$filter["y_StudentStatusID"];
		$this->StudentStatusID->AdvancedSearch->SearchOperator2 = @$filter["w_StudentStatusID"];
		$this->StudentStatusID->AdvancedSearch->Save();

		// Field TahunID
		$this->TahunID->AdvancedSearch->SearchValue = @$filter["x_TahunID"];
		$this->TahunID->AdvancedSearch->SearchOperator = @$filter["z_TahunID"];
		$this->TahunID->AdvancedSearch->SearchCondition = @$filter["v_TahunID"];
		$this->TahunID->AdvancedSearch->SearchValue2 = @$filter["y_TahunID"];
		$this->TahunID->AdvancedSearch->SearchOperator2 = @$filter["w_TahunID"];
		$this->TahunID->AdvancedSearch->Save();

		// Field Foto
		$this->Foto->AdvancedSearch->SearchValue = @$filter["x_Foto"];
		$this->Foto->AdvancedSearch->SearchOperator = @$filter["z_Foto"];
		$this->Foto->AdvancedSearch->SearchCondition = @$filter["v_Foto"];
		$this->Foto->AdvancedSearch->SearchValue2 = @$filter["y_Foto"];
		$this->Foto->AdvancedSearch->SearchOperator2 = @$filter["w_Foto"];
		$this->Foto->AdvancedSearch->Save();

		// Field NIK
		$this->NIK->AdvancedSearch->SearchValue = @$filter["x_NIK"];
		$this->NIK->AdvancedSearch->SearchOperator = @$filter["z_NIK"];
		$this->NIK->AdvancedSearch->SearchCondition = @$filter["v_NIK"];
		$this->NIK->AdvancedSearch->SearchValue2 = @$filter["y_NIK"];
		$this->NIK->AdvancedSearch->SearchOperator2 = @$filter["w_NIK"];
		$this->NIK->AdvancedSearch->Save();

		// Field WargaNegara
		$this->WargaNegara->AdvancedSearch->SearchValue = @$filter["x_WargaNegara"];
		$this->WargaNegara->AdvancedSearch->SearchOperator = @$filter["z_WargaNegara"];
		$this->WargaNegara->AdvancedSearch->SearchCondition = @$filter["v_WargaNegara"];
		$this->WargaNegara->AdvancedSearch->SearchValue2 = @$filter["y_WargaNegara"];
		$this->WargaNegara->AdvancedSearch->SearchOperator2 = @$filter["w_WargaNegara"];
		$this->WargaNegara->AdvancedSearch->Save();

		// Field Kelamin
		$this->Kelamin->AdvancedSearch->SearchValue = @$filter["x_Kelamin"];
		$this->Kelamin->AdvancedSearch->SearchOperator = @$filter["z_Kelamin"];
		$this->Kelamin->AdvancedSearch->SearchCondition = @$filter["v_Kelamin"];
		$this->Kelamin->AdvancedSearch->SearchValue2 = @$filter["y_Kelamin"];
		$this->Kelamin->AdvancedSearch->SearchOperator2 = @$filter["w_Kelamin"];
		$this->Kelamin->AdvancedSearch->Save();

		// Field TempatLahir
		$this->TempatLahir->AdvancedSearch->SearchValue = @$filter["x_TempatLahir"];
		$this->TempatLahir->AdvancedSearch->SearchOperator = @$filter["z_TempatLahir"];
		$this->TempatLahir->AdvancedSearch->SearchCondition = @$filter["v_TempatLahir"];
		$this->TempatLahir->AdvancedSearch->SearchValue2 = @$filter["y_TempatLahir"];
		$this->TempatLahir->AdvancedSearch->SearchOperator2 = @$filter["w_TempatLahir"];
		$this->TempatLahir->AdvancedSearch->Save();

		// Field TanggalLahir
		$this->TanggalLahir->AdvancedSearch->SearchValue = @$filter["x_TanggalLahir"];
		$this->TanggalLahir->AdvancedSearch->SearchOperator = @$filter["z_TanggalLahir"];
		$this->TanggalLahir->AdvancedSearch->SearchCondition = @$filter["v_TanggalLahir"];
		$this->TanggalLahir->AdvancedSearch->SearchValue2 = @$filter["y_TanggalLahir"];
		$this->TanggalLahir->AdvancedSearch->SearchOperator2 = @$filter["w_TanggalLahir"];
		$this->TanggalLahir->AdvancedSearch->Save();

		// Field AgamaID
		$this->AgamaID->AdvancedSearch->SearchValue = @$filter["x_AgamaID"];
		$this->AgamaID->AdvancedSearch->SearchOperator = @$filter["z_AgamaID"];
		$this->AgamaID->AdvancedSearch->SearchCondition = @$filter["v_AgamaID"];
		$this->AgamaID->AdvancedSearch->SearchValue2 = @$filter["y_AgamaID"];
		$this->AgamaID->AdvancedSearch->SearchOperator2 = @$filter["w_AgamaID"];
		$this->AgamaID->AdvancedSearch->Save();

		// Field Darah
		$this->Darah->AdvancedSearch->SearchValue = @$filter["x_Darah"];
		$this->Darah->AdvancedSearch->SearchOperator = @$filter["z_Darah"];
		$this->Darah->AdvancedSearch->SearchCondition = @$filter["v_Darah"];
		$this->Darah->AdvancedSearch->SearchValue2 = @$filter["y_Darah"];
		$this->Darah->AdvancedSearch->SearchOperator2 = @$filter["w_Darah"];
		$this->Darah->AdvancedSearch->Save();

		// Field StatusSipil
		$this->StatusSipil->AdvancedSearch->SearchValue = @$filter["x_StatusSipil"];
		$this->StatusSipil->AdvancedSearch->SearchOperator = @$filter["z_StatusSipil"];
		$this->StatusSipil->AdvancedSearch->SearchCondition = @$filter["v_StatusSipil"];
		$this->StatusSipil->AdvancedSearch->SearchValue2 = @$filter["y_StatusSipil"];
		$this->StatusSipil->AdvancedSearch->SearchOperator2 = @$filter["w_StatusSipil"];
		$this->StatusSipil->AdvancedSearch->Save();

		// Field AlamatDomisili
		$this->AlamatDomisili->AdvancedSearch->SearchValue = @$filter["x_AlamatDomisili"];
		$this->AlamatDomisili->AdvancedSearch->SearchOperator = @$filter["z_AlamatDomisili"];
		$this->AlamatDomisili->AdvancedSearch->SearchCondition = @$filter["v_AlamatDomisili"];
		$this->AlamatDomisili->AdvancedSearch->SearchValue2 = @$filter["y_AlamatDomisili"];
		$this->AlamatDomisili->AdvancedSearch->SearchOperator2 = @$filter["w_AlamatDomisili"];
		$this->AlamatDomisili->AdvancedSearch->Save();

		// Field RT
		$this->RT->AdvancedSearch->SearchValue = @$filter["x_RT"];
		$this->RT->AdvancedSearch->SearchOperator = @$filter["z_RT"];
		$this->RT->AdvancedSearch->SearchCondition = @$filter["v_RT"];
		$this->RT->AdvancedSearch->SearchValue2 = @$filter["y_RT"];
		$this->RT->AdvancedSearch->SearchOperator2 = @$filter["w_RT"];
		$this->RT->AdvancedSearch->Save();

		// Field RW
		$this->RW->AdvancedSearch->SearchValue = @$filter["x_RW"];
		$this->RW->AdvancedSearch->SearchOperator = @$filter["z_RW"];
		$this->RW->AdvancedSearch->SearchCondition = @$filter["v_RW"];
		$this->RW->AdvancedSearch->SearchValue2 = @$filter["y_RW"];
		$this->RW->AdvancedSearch->SearchOperator2 = @$filter["w_RW"];
		$this->RW->AdvancedSearch->Save();

		// Field KodePos
		$this->KodePos->AdvancedSearch->SearchValue = @$filter["x_KodePos"];
		$this->KodePos->AdvancedSearch->SearchOperator = @$filter["z_KodePos"];
		$this->KodePos->AdvancedSearch->SearchCondition = @$filter["v_KodePos"];
		$this->KodePos->AdvancedSearch->SearchValue2 = @$filter["y_KodePos"];
		$this->KodePos->AdvancedSearch->SearchOperator2 = @$filter["w_KodePos"];
		$this->KodePos->AdvancedSearch->Save();

		// Field ProvinsiID
		$this->ProvinsiID->AdvancedSearch->SearchValue = @$filter["x_ProvinsiID"];
		$this->ProvinsiID->AdvancedSearch->SearchOperator = @$filter["z_ProvinsiID"];
		$this->ProvinsiID->AdvancedSearch->SearchCondition = @$filter["v_ProvinsiID"];
		$this->ProvinsiID->AdvancedSearch->SearchValue2 = @$filter["y_ProvinsiID"];
		$this->ProvinsiID->AdvancedSearch->SearchOperator2 = @$filter["w_ProvinsiID"];
		$this->ProvinsiID->AdvancedSearch->Save();

		// Field KabupatenKotaID
		$this->KabupatenKotaID->AdvancedSearch->SearchValue = @$filter["x_KabupatenKotaID"];
		$this->KabupatenKotaID->AdvancedSearch->SearchOperator = @$filter["z_KabupatenKotaID"];
		$this->KabupatenKotaID->AdvancedSearch->SearchCondition = @$filter["v_KabupatenKotaID"];
		$this->KabupatenKotaID->AdvancedSearch->SearchValue2 = @$filter["y_KabupatenKotaID"];
		$this->KabupatenKotaID->AdvancedSearch->SearchOperator2 = @$filter["w_KabupatenKotaID"];
		$this->KabupatenKotaID->AdvancedSearch->Save();

		// Field KecamatanID
		$this->KecamatanID->AdvancedSearch->SearchValue = @$filter["x_KecamatanID"];
		$this->KecamatanID->AdvancedSearch->SearchOperator = @$filter["z_KecamatanID"];
		$this->KecamatanID->AdvancedSearch->SearchCondition = @$filter["v_KecamatanID"];
		$this->KecamatanID->AdvancedSearch->SearchValue2 = @$filter["y_KecamatanID"];
		$this->KecamatanID->AdvancedSearch->SearchOperator2 = @$filter["w_KecamatanID"];
		$this->KecamatanID->AdvancedSearch->Save();

		// Field DesaID
		$this->DesaID->AdvancedSearch->SearchValue = @$filter["x_DesaID"];
		$this->DesaID->AdvancedSearch->SearchOperator = @$filter["z_DesaID"];
		$this->DesaID->AdvancedSearch->SearchCondition = @$filter["v_DesaID"];
		$this->DesaID->AdvancedSearch->SearchValue2 = @$filter["y_DesaID"];
		$this->DesaID->AdvancedSearch->SearchOperator2 = @$filter["w_DesaID"];
		$this->DesaID->AdvancedSearch->Save();

		// Field AnakKe
		$this->AnakKe->AdvancedSearch->SearchValue = @$filter["x_AnakKe"];
		$this->AnakKe->AdvancedSearch->SearchOperator = @$filter["z_AnakKe"];
		$this->AnakKe->AdvancedSearch->SearchCondition = @$filter["v_AnakKe"];
		$this->AnakKe->AdvancedSearch->SearchValue2 = @$filter["y_AnakKe"];
		$this->AnakKe->AdvancedSearch->SearchOperator2 = @$filter["w_AnakKe"];
		$this->AnakKe->AdvancedSearch->Save();

		// Field JumlahSaudara
		$this->JumlahSaudara->AdvancedSearch->SearchValue = @$filter["x_JumlahSaudara"];
		$this->JumlahSaudara->AdvancedSearch->SearchOperator = @$filter["z_JumlahSaudara"];
		$this->JumlahSaudara->AdvancedSearch->SearchCondition = @$filter["v_JumlahSaudara"];
		$this->JumlahSaudara->AdvancedSearch->SearchValue2 = @$filter["y_JumlahSaudara"];
		$this->JumlahSaudara->AdvancedSearch->SearchOperator2 = @$filter["w_JumlahSaudara"];
		$this->JumlahSaudara->AdvancedSearch->Save();

		// Field Telepon
		$this->Telepon->AdvancedSearch->SearchValue = @$filter["x_Telepon"];
		$this->Telepon->AdvancedSearch->SearchOperator = @$filter["z_Telepon"];
		$this->Telepon->AdvancedSearch->SearchCondition = @$filter["v_Telepon"];
		$this->Telepon->AdvancedSearch->SearchValue2 = @$filter["y_Telepon"];
		$this->Telepon->AdvancedSearch->SearchOperator2 = @$filter["w_Telepon"];
		$this->Telepon->AdvancedSearch->Save();

		// Field Email
		$this->_Email->AdvancedSearch->SearchValue = @$filter["x__Email"];
		$this->_Email->AdvancedSearch->SearchOperator = @$filter["z__Email"];
		$this->_Email->AdvancedSearch->SearchCondition = @$filter["v__Email"];
		$this->_Email->AdvancedSearch->SearchValue2 = @$filter["y__Email"];
		$this->_Email->AdvancedSearch->SearchOperator2 = @$filter["w__Email"];
		$this->_Email->AdvancedSearch->Save();

		// Field NamaAyah
		$this->NamaAyah->AdvancedSearch->SearchValue = @$filter["x_NamaAyah"];
		$this->NamaAyah->AdvancedSearch->SearchOperator = @$filter["z_NamaAyah"];
		$this->NamaAyah->AdvancedSearch->SearchCondition = @$filter["v_NamaAyah"];
		$this->NamaAyah->AdvancedSearch->SearchValue2 = @$filter["y_NamaAyah"];
		$this->NamaAyah->AdvancedSearch->SearchOperator2 = @$filter["w_NamaAyah"];
		$this->NamaAyah->AdvancedSearch->Save();

		// Field AgamaAyah
		$this->AgamaAyah->AdvancedSearch->SearchValue = @$filter["x_AgamaAyah"];
		$this->AgamaAyah->AdvancedSearch->SearchOperator = @$filter["z_AgamaAyah"];
		$this->AgamaAyah->AdvancedSearch->SearchCondition = @$filter["v_AgamaAyah"];
		$this->AgamaAyah->AdvancedSearch->SearchValue2 = @$filter["y_AgamaAyah"];
		$this->AgamaAyah->AdvancedSearch->SearchOperator2 = @$filter["w_AgamaAyah"];
		$this->AgamaAyah->AdvancedSearch->Save();

		// Field PendidikanAyah
		$this->PendidikanAyah->AdvancedSearch->SearchValue = @$filter["x_PendidikanAyah"];
		$this->PendidikanAyah->AdvancedSearch->SearchOperator = @$filter["z_PendidikanAyah"];
		$this->PendidikanAyah->AdvancedSearch->SearchCondition = @$filter["v_PendidikanAyah"];
		$this->PendidikanAyah->AdvancedSearch->SearchValue2 = @$filter["y_PendidikanAyah"];
		$this->PendidikanAyah->AdvancedSearch->SearchOperator2 = @$filter["w_PendidikanAyah"];
		$this->PendidikanAyah->AdvancedSearch->Save();

		// Field PekerjaanAyah
		$this->PekerjaanAyah->AdvancedSearch->SearchValue = @$filter["x_PekerjaanAyah"];
		$this->PekerjaanAyah->AdvancedSearch->SearchOperator = @$filter["z_PekerjaanAyah"];
		$this->PekerjaanAyah->AdvancedSearch->SearchCondition = @$filter["v_PekerjaanAyah"];
		$this->PekerjaanAyah->AdvancedSearch->SearchValue2 = @$filter["y_PekerjaanAyah"];
		$this->PekerjaanAyah->AdvancedSearch->SearchOperator2 = @$filter["w_PekerjaanAyah"];
		$this->PekerjaanAyah->AdvancedSearch->Save();

		// Field HidupAyah
		$this->HidupAyah->AdvancedSearch->SearchValue = @$filter["x_HidupAyah"];
		$this->HidupAyah->AdvancedSearch->SearchOperator = @$filter["z_HidupAyah"];
		$this->HidupAyah->AdvancedSearch->SearchCondition = @$filter["v_HidupAyah"];
		$this->HidupAyah->AdvancedSearch->SearchValue2 = @$filter["y_HidupAyah"];
		$this->HidupAyah->AdvancedSearch->SearchOperator2 = @$filter["w_HidupAyah"];
		$this->HidupAyah->AdvancedSearch->Save();

		// Field NamaIbu
		$this->NamaIbu->AdvancedSearch->SearchValue = @$filter["x_NamaIbu"];
		$this->NamaIbu->AdvancedSearch->SearchOperator = @$filter["z_NamaIbu"];
		$this->NamaIbu->AdvancedSearch->SearchCondition = @$filter["v_NamaIbu"];
		$this->NamaIbu->AdvancedSearch->SearchValue2 = @$filter["y_NamaIbu"];
		$this->NamaIbu->AdvancedSearch->SearchOperator2 = @$filter["w_NamaIbu"];
		$this->NamaIbu->AdvancedSearch->Save();

		// Field AgamaIbu
		$this->AgamaIbu->AdvancedSearch->SearchValue = @$filter["x_AgamaIbu"];
		$this->AgamaIbu->AdvancedSearch->SearchOperator = @$filter["z_AgamaIbu"];
		$this->AgamaIbu->AdvancedSearch->SearchCondition = @$filter["v_AgamaIbu"];
		$this->AgamaIbu->AdvancedSearch->SearchValue2 = @$filter["y_AgamaIbu"];
		$this->AgamaIbu->AdvancedSearch->SearchOperator2 = @$filter["w_AgamaIbu"];
		$this->AgamaIbu->AdvancedSearch->Save();

		// Field PendidikanIbu
		$this->PendidikanIbu->AdvancedSearch->SearchValue = @$filter["x_PendidikanIbu"];
		$this->PendidikanIbu->AdvancedSearch->SearchOperator = @$filter["z_PendidikanIbu"];
		$this->PendidikanIbu->AdvancedSearch->SearchCondition = @$filter["v_PendidikanIbu"];
		$this->PendidikanIbu->AdvancedSearch->SearchValue2 = @$filter["y_PendidikanIbu"];
		$this->PendidikanIbu->AdvancedSearch->SearchOperator2 = @$filter["w_PendidikanIbu"];
		$this->PendidikanIbu->AdvancedSearch->Save();

		// Field PekerjaanIbu
		$this->PekerjaanIbu->AdvancedSearch->SearchValue = @$filter["x_PekerjaanIbu"];
		$this->PekerjaanIbu->AdvancedSearch->SearchOperator = @$filter["z_PekerjaanIbu"];
		$this->PekerjaanIbu->AdvancedSearch->SearchCondition = @$filter["v_PekerjaanIbu"];
		$this->PekerjaanIbu->AdvancedSearch->SearchValue2 = @$filter["y_PekerjaanIbu"];
		$this->PekerjaanIbu->AdvancedSearch->SearchOperator2 = @$filter["w_PekerjaanIbu"];
		$this->PekerjaanIbu->AdvancedSearch->Save();

		// Field HidupIbu
		$this->HidupIbu->AdvancedSearch->SearchValue = @$filter["x_HidupIbu"];
		$this->HidupIbu->AdvancedSearch->SearchOperator = @$filter["z_HidupIbu"];
		$this->HidupIbu->AdvancedSearch->SearchCondition = @$filter["v_HidupIbu"];
		$this->HidupIbu->AdvancedSearch->SearchValue2 = @$filter["y_HidupIbu"];
		$this->HidupIbu->AdvancedSearch->SearchOperator2 = @$filter["w_HidupIbu"];
		$this->HidupIbu->AdvancedSearch->Save();

		// Field AlamatOrtu
		$this->AlamatOrtu->AdvancedSearch->SearchValue = @$filter["x_AlamatOrtu"];
		$this->AlamatOrtu->AdvancedSearch->SearchOperator = @$filter["z_AlamatOrtu"];
		$this->AlamatOrtu->AdvancedSearch->SearchCondition = @$filter["v_AlamatOrtu"];
		$this->AlamatOrtu->AdvancedSearch->SearchValue2 = @$filter["y_AlamatOrtu"];
		$this->AlamatOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_AlamatOrtu"];
		$this->AlamatOrtu->AdvancedSearch->Save();

		// Field RTOrtu
		$this->RTOrtu->AdvancedSearch->SearchValue = @$filter["x_RTOrtu"];
		$this->RTOrtu->AdvancedSearch->SearchOperator = @$filter["z_RTOrtu"];
		$this->RTOrtu->AdvancedSearch->SearchCondition = @$filter["v_RTOrtu"];
		$this->RTOrtu->AdvancedSearch->SearchValue2 = @$filter["y_RTOrtu"];
		$this->RTOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_RTOrtu"];
		$this->RTOrtu->AdvancedSearch->Save();

		// Field RWOrtu
		$this->RWOrtu->AdvancedSearch->SearchValue = @$filter["x_RWOrtu"];
		$this->RWOrtu->AdvancedSearch->SearchOperator = @$filter["z_RWOrtu"];
		$this->RWOrtu->AdvancedSearch->SearchCondition = @$filter["v_RWOrtu"];
		$this->RWOrtu->AdvancedSearch->SearchValue2 = @$filter["y_RWOrtu"];
		$this->RWOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_RWOrtu"];
		$this->RWOrtu->AdvancedSearch->Save();

		// Field KodePosOrtu
		$this->KodePosOrtu->AdvancedSearch->SearchValue = @$filter["x_KodePosOrtu"];
		$this->KodePosOrtu->AdvancedSearch->SearchOperator = @$filter["z_KodePosOrtu"];
		$this->KodePosOrtu->AdvancedSearch->SearchCondition = @$filter["v_KodePosOrtu"];
		$this->KodePosOrtu->AdvancedSearch->SearchValue2 = @$filter["y_KodePosOrtu"];
		$this->KodePosOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_KodePosOrtu"];
		$this->KodePosOrtu->AdvancedSearch->Save();

		// Field ProvinsiIDOrtu
		$this->ProvinsiIDOrtu->AdvancedSearch->SearchValue = @$filter["x_ProvinsiIDOrtu"];
		$this->ProvinsiIDOrtu->AdvancedSearch->SearchOperator = @$filter["z_ProvinsiIDOrtu"];
		$this->ProvinsiIDOrtu->AdvancedSearch->SearchCondition = @$filter["v_ProvinsiIDOrtu"];
		$this->ProvinsiIDOrtu->AdvancedSearch->SearchValue2 = @$filter["y_ProvinsiIDOrtu"];
		$this->ProvinsiIDOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_ProvinsiIDOrtu"];
		$this->ProvinsiIDOrtu->AdvancedSearch->Save();

		// Field KabupatenIDOrtu
		$this->KabupatenIDOrtu->AdvancedSearch->SearchValue = @$filter["x_KabupatenIDOrtu"];
		$this->KabupatenIDOrtu->AdvancedSearch->SearchOperator = @$filter["z_KabupatenIDOrtu"];
		$this->KabupatenIDOrtu->AdvancedSearch->SearchCondition = @$filter["v_KabupatenIDOrtu"];
		$this->KabupatenIDOrtu->AdvancedSearch->SearchValue2 = @$filter["y_KabupatenIDOrtu"];
		$this->KabupatenIDOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_KabupatenIDOrtu"];
		$this->KabupatenIDOrtu->AdvancedSearch->Save();

		// Field KecamatanIDOrtu
		$this->KecamatanIDOrtu->AdvancedSearch->SearchValue = @$filter["x_KecamatanIDOrtu"];
		$this->KecamatanIDOrtu->AdvancedSearch->SearchOperator = @$filter["z_KecamatanIDOrtu"];
		$this->KecamatanIDOrtu->AdvancedSearch->SearchCondition = @$filter["v_KecamatanIDOrtu"];
		$this->KecamatanIDOrtu->AdvancedSearch->SearchValue2 = @$filter["y_KecamatanIDOrtu"];
		$this->KecamatanIDOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_KecamatanIDOrtu"];
		$this->KecamatanIDOrtu->AdvancedSearch->Save();

		// Field DesaIDOrtu
		$this->DesaIDOrtu->AdvancedSearch->SearchValue = @$filter["x_DesaIDOrtu"];
		$this->DesaIDOrtu->AdvancedSearch->SearchOperator = @$filter["z_DesaIDOrtu"];
		$this->DesaIDOrtu->AdvancedSearch->SearchCondition = @$filter["v_DesaIDOrtu"];
		$this->DesaIDOrtu->AdvancedSearch->SearchValue2 = @$filter["y_DesaIDOrtu"];
		$this->DesaIDOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_DesaIDOrtu"];
		$this->DesaIDOrtu->AdvancedSearch->Save();

		// Field NegaraIDOrtu
		$this->NegaraIDOrtu->AdvancedSearch->SearchValue = @$filter["x_NegaraIDOrtu"];
		$this->NegaraIDOrtu->AdvancedSearch->SearchOperator = @$filter["z_NegaraIDOrtu"];
		$this->NegaraIDOrtu->AdvancedSearch->SearchCondition = @$filter["v_NegaraIDOrtu"];
		$this->NegaraIDOrtu->AdvancedSearch->SearchValue2 = @$filter["y_NegaraIDOrtu"];
		$this->NegaraIDOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_NegaraIDOrtu"];
		$this->NegaraIDOrtu->AdvancedSearch->Save();

		// Field TeleponOrtu
		$this->TeleponOrtu->AdvancedSearch->SearchValue = @$filter["x_TeleponOrtu"];
		$this->TeleponOrtu->AdvancedSearch->SearchOperator = @$filter["z_TeleponOrtu"];
		$this->TeleponOrtu->AdvancedSearch->SearchCondition = @$filter["v_TeleponOrtu"];
		$this->TeleponOrtu->AdvancedSearch->SearchValue2 = @$filter["y_TeleponOrtu"];
		$this->TeleponOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_TeleponOrtu"];
		$this->TeleponOrtu->AdvancedSearch->Save();

		// Field HandphoneOrtu
		$this->HandphoneOrtu->AdvancedSearch->SearchValue = @$filter["x_HandphoneOrtu"];
		$this->HandphoneOrtu->AdvancedSearch->SearchOperator = @$filter["z_HandphoneOrtu"];
		$this->HandphoneOrtu->AdvancedSearch->SearchCondition = @$filter["v_HandphoneOrtu"];
		$this->HandphoneOrtu->AdvancedSearch->SearchValue2 = @$filter["y_HandphoneOrtu"];
		$this->HandphoneOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_HandphoneOrtu"];
		$this->HandphoneOrtu->AdvancedSearch->Save();

		// Field EmailOrtu
		$this->EmailOrtu->AdvancedSearch->SearchValue = @$filter["x_EmailOrtu"];
		$this->EmailOrtu->AdvancedSearch->SearchOperator = @$filter["z_EmailOrtu"];
		$this->EmailOrtu->AdvancedSearch->SearchCondition = @$filter["v_EmailOrtu"];
		$this->EmailOrtu->AdvancedSearch->SearchValue2 = @$filter["y_EmailOrtu"];
		$this->EmailOrtu->AdvancedSearch->SearchOperator2 = @$filter["w_EmailOrtu"];
		$this->EmailOrtu->AdvancedSearch->Save();

		// Field AsalSekolah
		$this->AsalSekolah->AdvancedSearch->SearchValue = @$filter["x_AsalSekolah"];
		$this->AsalSekolah->AdvancedSearch->SearchOperator = @$filter["z_AsalSekolah"];
		$this->AsalSekolah->AdvancedSearch->SearchCondition = @$filter["v_AsalSekolah"];
		$this->AsalSekolah->AdvancedSearch->SearchValue2 = @$filter["y_AsalSekolah"];
		$this->AsalSekolah->AdvancedSearch->SearchOperator2 = @$filter["w_AsalSekolah"];
		$this->AsalSekolah->AdvancedSearch->Save();

		// Field AlamatSekolah
		$this->AlamatSekolah->AdvancedSearch->SearchValue = @$filter["x_AlamatSekolah"];
		$this->AlamatSekolah->AdvancedSearch->SearchOperator = @$filter["z_AlamatSekolah"];
		$this->AlamatSekolah->AdvancedSearch->SearchCondition = @$filter["v_AlamatSekolah"];
		$this->AlamatSekolah->AdvancedSearch->SearchValue2 = @$filter["y_AlamatSekolah"];
		$this->AlamatSekolah->AdvancedSearch->SearchOperator2 = @$filter["w_AlamatSekolah"];
		$this->AlamatSekolah->AdvancedSearch->Save();

		// Field ProvinsiIDSekolah
		$this->ProvinsiIDSekolah->AdvancedSearch->SearchValue = @$filter["x_ProvinsiIDSekolah"];
		$this->ProvinsiIDSekolah->AdvancedSearch->SearchOperator = @$filter["z_ProvinsiIDSekolah"];
		$this->ProvinsiIDSekolah->AdvancedSearch->SearchCondition = @$filter["v_ProvinsiIDSekolah"];
		$this->ProvinsiIDSekolah->AdvancedSearch->SearchValue2 = @$filter["y_ProvinsiIDSekolah"];
		$this->ProvinsiIDSekolah->AdvancedSearch->SearchOperator2 = @$filter["w_ProvinsiIDSekolah"];
		$this->ProvinsiIDSekolah->AdvancedSearch->Save();

		// Field KabupatenIDSekolah
		$this->KabupatenIDSekolah->AdvancedSearch->SearchValue = @$filter["x_KabupatenIDSekolah"];
		$this->KabupatenIDSekolah->AdvancedSearch->SearchOperator = @$filter["z_KabupatenIDSekolah"];
		$this->KabupatenIDSekolah->AdvancedSearch->SearchCondition = @$filter["v_KabupatenIDSekolah"];
		$this->KabupatenIDSekolah->AdvancedSearch->SearchValue2 = @$filter["y_KabupatenIDSekolah"];
		$this->KabupatenIDSekolah->AdvancedSearch->SearchOperator2 = @$filter["w_KabupatenIDSekolah"];
		$this->KabupatenIDSekolah->AdvancedSearch->Save();

		// Field KecamatanIDSekolah
		$this->KecamatanIDSekolah->AdvancedSearch->SearchValue = @$filter["x_KecamatanIDSekolah"];
		$this->KecamatanIDSekolah->AdvancedSearch->SearchOperator = @$filter["z_KecamatanIDSekolah"];
		$this->KecamatanIDSekolah->AdvancedSearch->SearchCondition = @$filter["v_KecamatanIDSekolah"];
		$this->KecamatanIDSekolah->AdvancedSearch->SearchValue2 = @$filter["y_KecamatanIDSekolah"];
		$this->KecamatanIDSekolah->AdvancedSearch->SearchOperator2 = @$filter["w_KecamatanIDSekolah"];
		$this->KecamatanIDSekolah->AdvancedSearch->Save();

		// Field DesaIDSekolah
		$this->DesaIDSekolah->AdvancedSearch->SearchValue = @$filter["x_DesaIDSekolah"];
		$this->DesaIDSekolah->AdvancedSearch->SearchOperator = @$filter["z_DesaIDSekolah"];
		$this->DesaIDSekolah->AdvancedSearch->SearchCondition = @$filter["v_DesaIDSekolah"];
		$this->DesaIDSekolah->AdvancedSearch->SearchValue2 = @$filter["y_DesaIDSekolah"];
		$this->DesaIDSekolah->AdvancedSearch->SearchOperator2 = @$filter["w_DesaIDSekolah"];
		$this->DesaIDSekolah->AdvancedSearch->Save();

		// Field NilaiSekolah
		$this->NilaiSekolah->AdvancedSearch->SearchValue = @$filter["x_NilaiSekolah"];
		$this->NilaiSekolah->AdvancedSearch->SearchOperator = @$filter["z_NilaiSekolah"];
		$this->NilaiSekolah->AdvancedSearch->SearchCondition = @$filter["v_NilaiSekolah"];
		$this->NilaiSekolah->AdvancedSearch->SearchValue2 = @$filter["y_NilaiSekolah"];
		$this->NilaiSekolah->AdvancedSearch->SearchOperator2 = @$filter["w_NilaiSekolah"];
		$this->NilaiSekolah->AdvancedSearch->Save();

		// Field TahunLulus
		$this->TahunLulus->AdvancedSearch->SearchValue = @$filter["x_TahunLulus"];
		$this->TahunLulus->AdvancedSearch->SearchOperator = @$filter["z_TahunLulus"];
		$this->TahunLulus->AdvancedSearch->SearchCondition = @$filter["v_TahunLulus"];
		$this->TahunLulus->AdvancedSearch->SearchValue2 = @$filter["y_TahunLulus"];
		$this->TahunLulus->AdvancedSearch->SearchOperator2 = @$filter["w_TahunLulus"];
		$this->TahunLulus->AdvancedSearch->Save();

		// Field IjazahSekolah
		$this->IjazahSekolah->AdvancedSearch->SearchValue = @$filter["x_IjazahSekolah"];
		$this->IjazahSekolah->AdvancedSearch->SearchOperator = @$filter["z_IjazahSekolah"];
		$this->IjazahSekolah->AdvancedSearch->SearchCondition = @$filter["v_IjazahSekolah"];
		$this->IjazahSekolah->AdvancedSearch->SearchValue2 = @$filter["y_IjazahSekolah"];
		$this->IjazahSekolah->AdvancedSearch->SearchOperator2 = @$filter["w_IjazahSekolah"];
		$this->IjazahSekolah->AdvancedSearch->Save();

		// Field TglIjazah
		$this->TglIjazah->AdvancedSearch->SearchValue = @$filter["x_TglIjazah"];
		$this->TglIjazah->AdvancedSearch->SearchOperator = @$filter["z_TglIjazah"];
		$this->TglIjazah->AdvancedSearch->SearchCondition = @$filter["v_TglIjazah"];
		$this->TglIjazah->AdvancedSearch->SearchValue2 = @$filter["y_TglIjazah"];
		$this->TglIjazah->AdvancedSearch->SearchOperator2 = @$filter["w_TglIjazah"];
		$this->TglIjazah->AdvancedSearch->Save();

		// Field LockStatus
		$this->LockStatus->AdvancedSearch->SearchValue = @$filter["x_LockStatus"];
		$this->LockStatus->AdvancedSearch->SearchOperator = @$filter["z_LockStatus"];
		$this->LockStatus->AdvancedSearch->SearchCondition = @$filter["v_LockStatus"];
		$this->LockStatus->AdvancedSearch->SearchValue2 = @$filter["y_LockStatus"];
		$this->LockStatus->AdvancedSearch->SearchOperator2 = @$filter["w_LockStatus"];
		$this->LockStatus->AdvancedSearch->Save();

		// Field NA
		$this->NA->AdvancedSearch->SearchValue = @$filter["x_NA"];
		$this->NA->AdvancedSearch->SearchOperator = @$filter["z_NA"];
		$this->NA->AdvancedSearch->SearchCondition = @$filter["v_NA"];
		$this->NA->AdvancedSearch->SearchValue2 = @$filter["y_NA"];
		$this->NA->AdvancedSearch->SearchOperator2 = @$filter["w_NA"];
		$this->NA->AdvancedSearch->Save();
		$this->BasicSearch->setKeyword(@$filter[EW_TABLE_BASIC_SEARCH]);
		$this->BasicSearch->setType(@$filter[EW_TABLE_BASIC_SEARCH_TYPE]);
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere($Default = FALSE) {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->StudentID, $Default, FALSE); // StudentID
		$this->BuildSearchSql($sWhere, $this->Nama, $Default, FALSE); // Nama
		$this->BuildSearchSql($sWhere, $this->LevelID, $Default, FALSE); // LevelID
		$this->BuildSearchSql($sWhere, $this->ProdiID, $Default, FALSE); // ProdiID
		$this->BuildSearchSql($sWhere, $this->StudentStatusID, $Default, FALSE); // StudentStatusID
		$this->BuildSearchSql($sWhere, $this->TahunID, $Default, FALSE); // TahunID
		$this->BuildSearchSql($sWhere, $this->Foto, $Default, FALSE); // Foto
		$this->BuildSearchSql($sWhere, $this->NIK, $Default, FALSE); // NIK
		$this->BuildSearchSql($sWhere, $this->WargaNegara, $Default, FALSE); // WargaNegara
		$this->BuildSearchSql($sWhere, $this->Kelamin, $Default, FALSE); // Kelamin
		$this->BuildSearchSql($sWhere, $this->TempatLahir, $Default, FALSE); // TempatLahir
		$this->BuildSearchSql($sWhere, $this->TanggalLahir, $Default, FALSE); // TanggalLahir
		$this->BuildSearchSql($sWhere, $this->AgamaID, $Default, FALSE); // AgamaID
		$this->BuildSearchSql($sWhere, $this->Darah, $Default, FALSE); // Darah
		$this->BuildSearchSql($sWhere, $this->StatusSipil, $Default, FALSE); // StatusSipil
		$this->BuildSearchSql($sWhere, $this->AlamatDomisili, $Default, FALSE); // AlamatDomisili
		$this->BuildSearchSql($sWhere, $this->RT, $Default, FALSE); // RT
		$this->BuildSearchSql($sWhere, $this->RW, $Default, FALSE); // RW
		$this->BuildSearchSql($sWhere, $this->KodePos, $Default, FALSE); // KodePos
		$this->BuildSearchSql($sWhere, $this->ProvinsiID, $Default, FALSE); // ProvinsiID
		$this->BuildSearchSql($sWhere, $this->KabupatenKotaID, $Default, FALSE); // KabupatenKotaID
		$this->BuildSearchSql($sWhere, $this->KecamatanID, $Default, FALSE); // KecamatanID
		$this->BuildSearchSql($sWhere, $this->DesaID, $Default, FALSE); // DesaID
		$this->BuildSearchSql($sWhere, $this->AnakKe, $Default, FALSE); // AnakKe
		$this->BuildSearchSql($sWhere, $this->JumlahSaudara, $Default, FALSE); // JumlahSaudara
		$this->BuildSearchSql($sWhere, $this->Telepon, $Default, FALSE); // Telepon
		$this->BuildSearchSql($sWhere, $this->_Email, $Default, FALSE); // Email
		$this->BuildSearchSql($sWhere, $this->NamaAyah, $Default, FALSE); // NamaAyah
		$this->BuildSearchSql($sWhere, $this->AgamaAyah, $Default, FALSE); // AgamaAyah
		$this->BuildSearchSql($sWhere, $this->PendidikanAyah, $Default, FALSE); // PendidikanAyah
		$this->BuildSearchSql($sWhere, $this->PekerjaanAyah, $Default, FALSE); // PekerjaanAyah
		$this->BuildSearchSql($sWhere, $this->HidupAyah, $Default, FALSE); // HidupAyah
		$this->BuildSearchSql($sWhere, $this->NamaIbu, $Default, FALSE); // NamaIbu
		$this->BuildSearchSql($sWhere, $this->AgamaIbu, $Default, FALSE); // AgamaIbu
		$this->BuildSearchSql($sWhere, $this->PendidikanIbu, $Default, FALSE); // PendidikanIbu
		$this->BuildSearchSql($sWhere, $this->PekerjaanIbu, $Default, FALSE); // PekerjaanIbu
		$this->BuildSearchSql($sWhere, $this->HidupIbu, $Default, FALSE); // HidupIbu
		$this->BuildSearchSql($sWhere, $this->AlamatOrtu, $Default, FALSE); // AlamatOrtu
		$this->BuildSearchSql($sWhere, $this->RTOrtu, $Default, FALSE); // RTOrtu
		$this->BuildSearchSql($sWhere, $this->RWOrtu, $Default, FALSE); // RWOrtu
		$this->BuildSearchSql($sWhere, $this->KodePosOrtu, $Default, FALSE); // KodePosOrtu
		$this->BuildSearchSql($sWhere, $this->ProvinsiIDOrtu, $Default, FALSE); // ProvinsiIDOrtu
		$this->BuildSearchSql($sWhere, $this->KabupatenIDOrtu, $Default, FALSE); // KabupatenIDOrtu
		$this->BuildSearchSql($sWhere, $this->KecamatanIDOrtu, $Default, FALSE); // KecamatanIDOrtu
		$this->BuildSearchSql($sWhere, $this->DesaIDOrtu, $Default, FALSE); // DesaIDOrtu
		$this->BuildSearchSql($sWhere, $this->NegaraIDOrtu, $Default, FALSE); // NegaraIDOrtu
		$this->BuildSearchSql($sWhere, $this->TeleponOrtu, $Default, FALSE); // TeleponOrtu
		$this->BuildSearchSql($sWhere, $this->HandphoneOrtu, $Default, FALSE); // HandphoneOrtu
		$this->BuildSearchSql($sWhere, $this->EmailOrtu, $Default, FALSE); // EmailOrtu
		$this->BuildSearchSql($sWhere, $this->AsalSekolah, $Default, FALSE); // AsalSekolah
		$this->BuildSearchSql($sWhere, $this->AlamatSekolah, $Default, FALSE); // AlamatSekolah
		$this->BuildSearchSql($sWhere, $this->ProvinsiIDSekolah, $Default, FALSE); // ProvinsiIDSekolah
		$this->BuildSearchSql($sWhere, $this->KabupatenIDSekolah, $Default, FALSE); // KabupatenIDSekolah
		$this->BuildSearchSql($sWhere, $this->KecamatanIDSekolah, $Default, FALSE); // KecamatanIDSekolah
		$this->BuildSearchSql($sWhere, $this->DesaIDSekolah, $Default, FALSE); // DesaIDSekolah
		$this->BuildSearchSql($sWhere, $this->NilaiSekolah, $Default, FALSE); // NilaiSekolah
		$this->BuildSearchSql($sWhere, $this->TahunLulus, $Default, FALSE); // TahunLulus
		$this->BuildSearchSql($sWhere, $this->IjazahSekolah, $Default, FALSE); // IjazahSekolah
		$this->BuildSearchSql($sWhere, $this->TglIjazah, $Default, FALSE); // TglIjazah
		$this->BuildSearchSql($sWhere, $this->LockStatus, $Default, FALSE); // LockStatus
		$this->BuildSearchSql($sWhere, $this->NA, $Default, FALSE); // NA

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->StudentID->AdvancedSearch->Save(); // StudentID
			$this->Nama->AdvancedSearch->Save(); // Nama
			$this->LevelID->AdvancedSearch->Save(); // LevelID
			$this->ProdiID->AdvancedSearch->Save(); // ProdiID
			$this->StudentStatusID->AdvancedSearch->Save(); // StudentStatusID
			$this->TahunID->AdvancedSearch->Save(); // TahunID
			$this->Foto->AdvancedSearch->Save(); // Foto
			$this->NIK->AdvancedSearch->Save(); // NIK
			$this->WargaNegara->AdvancedSearch->Save(); // WargaNegara
			$this->Kelamin->AdvancedSearch->Save(); // Kelamin
			$this->TempatLahir->AdvancedSearch->Save(); // TempatLahir
			$this->TanggalLahir->AdvancedSearch->Save(); // TanggalLahir
			$this->AgamaID->AdvancedSearch->Save(); // AgamaID
			$this->Darah->AdvancedSearch->Save(); // Darah
			$this->StatusSipil->AdvancedSearch->Save(); // StatusSipil
			$this->AlamatDomisili->AdvancedSearch->Save(); // AlamatDomisili
			$this->RT->AdvancedSearch->Save(); // RT
			$this->RW->AdvancedSearch->Save(); // RW
			$this->KodePos->AdvancedSearch->Save(); // KodePos
			$this->ProvinsiID->AdvancedSearch->Save(); // ProvinsiID
			$this->KabupatenKotaID->AdvancedSearch->Save(); // KabupatenKotaID
			$this->KecamatanID->AdvancedSearch->Save(); // KecamatanID
			$this->DesaID->AdvancedSearch->Save(); // DesaID
			$this->AnakKe->AdvancedSearch->Save(); // AnakKe
			$this->JumlahSaudara->AdvancedSearch->Save(); // JumlahSaudara
			$this->Telepon->AdvancedSearch->Save(); // Telepon
			$this->_Email->AdvancedSearch->Save(); // Email
			$this->NamaAyah->AdvancedSearch->Save(); // NamaAyah
			$this->AgamaAyah->AdvancedSearch->Save(); // AgamaAyah
			$this->PendidikanAyah->AdvancedSearch->Save(); // PendidikanAyah
			$this->PekerjaanAyah->AdvancedSearch->Save(); // PekerjaanAyah
			$this->HidupAyah->AdvancedSearch->Save(); // HidupAyah
			$this->NamaIbu->AdvancedSearch->Save(); // NamaIbu
			$this->AgamaIbu->AdvancedSearch->Save(); // AgamaIbu
			$this->PendidikanIbu->AdvancedSearch->Save(); // PendidikanIbu
			$this->PekerjaanIbu->AdvancedSearch->Save(); // PekerjaanIbu
			$this->HidupIbu->AdvancedSearch->Save(); // HidupIbu
			$this->AlamatOrtu->AdvancedSearch->Save(); // AlamatOrtu
			$this->RTOrtu->AdvancedSearch->Save(); // RTOrtu
			$this->RWOrtu->AdvancedSearch->Save(); // RWOrtu
			$this->KodePosOrtu->AdvancedSearch->Save(); // KodePosOrtu
			$this->ProvinsiIDOrtu->AdvancedSearch->Save(); // ProvinsiIDOrtu
			$this->KabupatenIDOrtu->AdvancedSearch->Save(); // KabupatenIDOrtu
			$this->KecamatanIDOrtu->AdvancedSearch->Save(); // KecamatanIDOrtu
			$this->DesaIDOrtu->AdvancedSearch->Save(); // DesaIDOrtu
			$this->NegaraIDOrtu->AdvancedSearch->Save(); // NegaraIDOrtu
			$this->TeleponOrtu->AdvancedSearch->Save(); // TeleponOrtu
			$this->HandphoneOrtu->AdvancedSearch->Save(); // HandphoneOrtu
			$this->EmailOrtu->AdvancedSearch->Save(); // EmailOrtu
			$this->AsalSekolah->AdvancedSearch->Save(); // AsalSekolah
			$this->AlamatSekolah->AdvancedSearch->Save(); // AlamatSekolah
			$this->ProvinsiIDSekolah->AdvancedSearch->Save(); // ProvinsiIDSekolah
			$this->KabupatenIDSekolah->AdvancedSearch->Save(); // KabupatenIDSekolah
			$this->KecamatanIDSekolah->AdvancedSearch->Save(); // KecamatanIDSekolah
			$this->DesaIDSekolah->AdvancedSearch->Save(); // DesaIDSekolah
			$this->NilaiSekolah->AdvancedSearch->Save(); // NilaiSekolah
			$this->TahunLulus->AdvancedSearch->Save(); // TahunLulus
			$this->IjazahSekolah->AdvancedSearch->Save(); // IjazahSekolah
			$this->TglIjazah->AdvancedSearch->Save(); // TglIjazah
			$this->LockStatus->AdvancedSearch->Save(); // LockStatus
			$this->NA->AdvancedSearch->Save(); // NA
		}
		return $sWhere;
	}

	// Build search SQL
	function BuildSearchSql(&$Where, &$Fld, $Default, $MultiValue) {
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = ($Default) ? $Fld->AdvancedSearch->SearchValueDefault : $Fld->AdvancedSearch->SearchValue; // @$_GET["x_$FldParm"]
		$FldOpr = ($Default) ? $Fld->AdvancedSearch->SearchOperatorDefault : $Fld->AdvancedSearch->SearchOperator; // @$_GET["z_$FldParm"]
		$FldCond = ($Default) ? $Fld->AdvancedSearch->SearchConditionDefault : $Fld->AdvancedSearch->SearchCondition; // @$_GET["v_$FldParm"]
		$FldVal2 = ($Default) ? $Fld->AdvancedSearch->SearchValue2Default : $Fld->AdvancedSearch->SearchValue2; // @$_GET["y_$FldParm"]
		$FldOpr2 = ($Default) ? $Fld->AdvancedSearch->SearchOperator2Default : $Fld->AdvancedSearch->SearchOperator2; // @$_GET["w_$FldParm"]
		$sWrk = "";

		//$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);

		//$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		if ($FldOpr == "") $FldOpr = "=";
		$FldOpr2 = strtoupper(trim($FldOpr2));
		if ($FldOpr2 == "") $FldOpr2 = "=";
		if (EW_SEARCH_MULTI_VALUE_OPTION == 1)
			$MultiValue = FALSE;
		if ($MultiValue) {
			$sWrk1 = ($FldVal <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr, $FldVal, $this->DBID) : ""; // Field value 1
			$sWrk2 = ($FldVal2 <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr2, $FldVal2, $this->DBID) : ""; // Field value 2
			$sWrk = $sWrk1; // Build final SQL
			if ($sWrk2 <> "")
				$sWrk = ($sWrk <> "") ? "($sWrk) $FldCond ($sWrk2)" : $sWrk2;
		} else {
			$FldVal = $this->ConvertSearchValue($Fld, $FldVal);
			$FldVal2 = $this->ConvertSearchValue($Fld, $FldVal2);
			$sWrk = ew_GetSearchSql($Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2, $this->DBID);
		}
		ew_AddFilter($Where, $sWrk);
	}

	// Convert search value
	function ConvertSearchValue(&$Fld, $FldVal) {
		if ($FldVal == EW_NULL_VALUE || $FldVal == EW_NOT_NULL_VALUE)
			return $FldVal;
		$Value = $FldVal;
		if ($Fld->FldDataType == EW_DATATYPE_BOOLEAN) {
			if ($FldVal <> "") $Value = ($FldVal == "1" || strtolower(strval($FldVal)) == "y" || strtolower(strval($FldVal)) == "t") ? $Fld->TrueValue : $Fld->FalseValue;
		} elseif ($Fld->FldDataType == EW_DATATYPE_DATE || $Fld->FldDataType == EW_DATATYPE_TIME) {
			if ($FldVal <> "") $Value = ew_UnFormatDateTime($FldVal, $Fld->FldDateTimeFormat);
		}
		return $Value;
	}

	// Return basic search SQL
	function BasicSearchSQL($arKeywords, $type) {
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->StudentID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Nama, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Password, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KampusID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->ProdiID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->StudentStatusID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Foto, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NIK, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->TempatLahir, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->StatusSipil, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->AlamatDomisili, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->RT, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->RW, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KodePos, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->ProvinsiID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KabupatenKotaID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KecamatanID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->DesaID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Telepon, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Handphone, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->_Email, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NamaAyah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->PendidikanAyah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->PekerjaanAyah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->HidupAyah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NamaIbu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->PendidikanIbu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->PekerjaanIbu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->HidupIbu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->AlamatOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->RTOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->RWOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KodePosOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->ProvinsiIDOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KabupatenIDOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KecamatanIDOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->DesaIDOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NegaraIDOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->TeleponOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->HandphoneOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->EmailOrtu, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->AsalSekolah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->AlamatSekolah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->ProvinsiIDSekolah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KabupatenIDSekolah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KecamatanIDSekolah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->DesaIDSekolah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NilaiSekolah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->TahunLulus, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->IjazahSekolah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Creator, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Editor, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->VerifiedBy, $arKeywords, $type);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSQL(&$Where, &$Fld, $arKeywords, $type) {
		global $EW_BASIC_SEARCH_IGNORE_PATTERN;
		$sDefCond = ($type == "OR") ? "OR" : "AND";
		$arSQL = array(); // Array for SQL parts
		$arCond = array(); // Array for search conditions
		$cnt = count($arKeywords);
		$j = 0; // Number of SQL parts
		for ($i = 0; $i < $cnt; $i++) {
			$Keyword = $arKeywords[$i];
			$Keyword = trim($Keyword);
			if ($EW_BASIC_SEARCH_IGNORE_PATTERN <> "") {
				$Keyword = preg_replace($EW_BASIC_SEARCH_IGNORE_PATTERN, "\\", $Keyword);
				$ar = explode("\\", $Keyword);
			} else {
				$ar = array($Keyword);
			}
			foreach ($ar as $Keyword) {
				if ($Keyword <> "") {
					$sWrk = "";
					if ($Keyword == "OR" && $type == "") {
						if ($j > 0)
							$arCond[$j-1] = "OR";
					} elseif ($Keyword == EW_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NULL";
					} elseif ($Keyword == EW_NOT_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NOT NULL";
					} elseif ($Fld->FldIsVirtual) {
						$sWrk = $Fld->FldVirtualExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					} elseif ($Fld->FldDataType != EW_DATATYPE_NUMBER || is_numeric($Keyword)) {
						$sWrk = $Fld->FldBasicSearchExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					}
					if ($sWrk <> "") {
						$arSQL[$j] = $sWrk;
						$arCond[$j] = $sDefCond;
						$j += 1;
					}
				}
			}
		}
		$cnt = count($arSQL);
		$bQuoted = FALSE;
		$sSql = "";
		if ($cnt > 0) {
			for ($i = 0; $i < $cnt-1; $i++) {
				if ($arCond[$i] == "OR") {
					if (!$bQuoted) $sSql .= "(";
					$bQuoted = TRUE;
				}
				$sSql .= $arSQL[$i];
				if ($bQuoted && $arCond[$i] <> "OR") {
					$sSql .= ")";
					$bQuoted = FALSE;
				}
				$sSql .= " " . $arCond[$i] . " ";
			}
			$sSql .= $arSQL[$cnt-1];
			if ($bQuoted)
				$sSql .= ")";
		}
		if ($sSql <> "") {
			if ($Where <> "") $Where .= " OR ";
			$Where .=  "(" . $sSql . ")";
		}
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere($Default = FALSE) {
		global $Security;
		$sSearchStr = "";
		if (!$Security->CanSearch()) return "";
		$sSearchKeyword = ($Default) ? $this->BasicSearch->KeywordDefault : $this->BasicSearch->Keyword;
		$sSearchType = ($Default) ? $this->BasicSearch->TypeDefault : $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				$ar = array();

				// Match quoted keywords (i.e.: "...")
				if (preg_match_all('/"([^"]*)"/i', $sSearch, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						$p = strpos($sSearch, $match[0]);
						$str = substr($sSearch, 0, $p);
						$sSearch = substr($sSearch, $p + strlen($match[0]));
						if (strlen(trim($str)) > 0)
							$ar = array_merge($ar, explode(" ", trim($str)));
						$ar[] = $match[1]; // Save quoted keyword
					}
				}

				// Match individual keywords
				if (strlen(trim($sSearch)) > 0)
					$ar = array_merge($ar, explode(" ", trim($sSearch)));

				// Search keyword in any fields
				if (($sSearchType == "OR" || $sSearchType == "AND") && $this->BasicSearch->BasicSearchAnyFields) {
					foreach ($ar as $sKeyword) {
						if ($sKeyword <> "") {
							if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
							$sSearchStr .= "(" . $this->BasicSearchSQL(array($sKeyword), $sSearchType) . ")";
						}
					}
				} else {
					$sSearchStr = $this->BasicSearchSQL($ar, $sSearchType);
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL(array($sSearch), $sSearchType);
			}
			if (!$Default) $this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		if ($this->StudentID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Nama->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->LevelID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->ProdiID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->StudentStatusID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TahunID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Foto->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NIK->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->WargaNegara->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Kelamin->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TempatLahir->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TanggalLahir->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->AgamaID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Darah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->StatusSipil->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->AlamatDomisili->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->RT->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->RW->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KodePos->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->ProvinsiID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KabupatenKotaID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KecamatanID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->DesaID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->AnakKe->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->JumlahSaudara->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Telepon->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->_Email->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NamaAyah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->AgamaAyah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PendidikanAyah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PekerjaanAyah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->HidupAyah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NamaIbu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->AgamaIbu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PendidikanIbu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PekerjaanIbu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->HidupIbu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->AlamatOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->RTOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->RWOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KodePosOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->ProvinsiIDOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KabupatenIDOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KecamatanIDOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->DesaIDOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NegaraIDOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TeleponOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->HandphoneOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->EmailOrtu->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->AsalSekolah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->AlamatSekolah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->ProvinsiIDSekolah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KabupatenIDSekolah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KecamatanIDSekolah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->DesaIDSekolah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NilaiSekolah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TahunLulus->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->IjazahSekolah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TglIjazah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->LockStatus->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NA->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->StudentID->AdvancedSearch->UnsetSession();
		$this->Nama->AdvancedSearch->UnsetSession();
		$this->LevelID->AdvancedSearch->UnsetSession();
		$this->ProdiID->AdvancedSearch->UnsetSession();
		$this->StudentStatusID->AdvancedSearch->UnsetSession();
		$this->TahunID->AdvancedSearch->UnsetSession();
		$this->Foto->AdvancedSearch->UnsetSession();
		$this->NIK->AdvancedSearch->UnsetSession();
		$this->WargaNegara->AdvancedSearch->UnsetSession();
		$this->Kelamin->AdvancedSearch->UnsetSession();
		$this->TempatLahir->AdvancedSearch->UnsetSession();
		$this->TanggalLahir->AdvancedSearch->UnsetSession();
		$this->AgamaID->AdvancedSearch->UnsetSession();
		$this->Darah->AdvancedSearch->UnsetSession();
		$this->StatusSipil->AdvancedSearch->UnsetSession();
		$this->AlamatDomisili->AdvancedSearch->UnsetSession();
		$this->RT->AdvancedSearch->UnsetSession();
		$this->RW->AdvancedSearch->UnsetSession();
		$this->KodePos->AdvancedSearch->UnsetSession();
		$this->ProvinsiID->AdvancedSearch->UnsetSession();
		$this->KabupatenKotaID->AdvancedSearch->UnsetSession();
		$this->KecamatanID->AdvancedSearch->UnsetSession();
		$this->DesaID->AdvancedSearch->UnsetSession();
		$this->AnakKe->AdvancedSearch->UnsetSession();
		$this->JumlahSaudara->AdvancedSearch->UnsetSession();
		$this->Telepon->AdvancedSearch->UnsetSession();
		$this->_Email->AdvancedSearch->UnsetSession();
		$this->NamaAyah->AdvancedSearch->UnsetSession();
		$this->AgamaAyah->AdvancedSearch->UnsetSession();
		$this->PendidikanAyah->AdvancedSearch->UnsetSession();
		$this->PekerjaanAyah->AdvancedSearch->UnsetSession();
		$this->HidupAyah->AdvancedSearch->UnsetSession();
		$this->NamaIbu->AdvancedSearch->UnsetSession();
		$this->AgamaIbu->AdvancedSearch->UnsetSession();
		$this->PendidikanIbu->AdvancedSearch->UnsetSession();
		$this->PekerjaanIbu->AdvancedSearch->UnsetSession();
		$this->HidupIbu->AdvancedSearch->UnsetSession();
		$this->AlamatOrtu->AdvancedSearch->UnsetSession();
		$this->RTOrtu->AdvancedSearch->UnsetSession();
		$this->RWOrtu->AdvancedSearch->UnsetSession();
		$this->KodePosOrtu->AdvancedSearch->UnsetSession();
		$this->ProvinsiIDOrtu->AdvancedSearch->UnsetSession();
		$this->KabupatenIDOrtu->AdvancedSearch->UnsetSession();
		$this->KecamatanIDOrtu->AdvancedSearch->UnsetSession();
		$this->DesaIDOrtu->AdvancedSearch->UnsetSession();
		$this->NegaraIDOrtu->AdvancedSearch->UnsetSession();
		$this->TeleponOrtu->AdvancedSearch->UnsetSession();
		$this->HandphoneOrtu->AdvancedSearch->UnsetSession();
		$this->EmailOrtu->AdvancedSearch->UnsetSession();
		$this->AsalSekolah->AdvancedSearch->UnsetSession();
		$this->AlamatSekolah->AdvancedSearch->UnsetSession();
		$this->ProvinsiIDSekolah->AdvancedSearch->UnsetSession();
		$this->KabupatenIDSekolah->AdvancedSearch->UnsetSession();
		$this->KecamatanIDSekolah->AdvancedSearch->UnsetSession();
		$this->DesaIDSekolah->AdvancedSearch->UnsetSession();
		$this->NilaiSekolah->AdvancedSearch->UnsetSession();
		$this->TahunLulus->AdvancedSearch->UnsetSession();
		$this->IjazahSekolah->AdvancedSearch->UnsetSession();
		$this->TglIjazah->AdvancedSearch->UnsetSession();
		$this->LockStatus->AdvancedSearch->UnsetSession();
		$this->NA->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->StudentID->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->LevelID->AdvancedSearch->Load();
		$this->ProdiID->AdvancedSearch->Load();
		$this->StudentStatusID->AdvancedSearch->Load();
		$this->TahunID->AdvancedSearch->Load();
		$this->Foto->AdvancedSearch->Load();
		$this->NIK->AdvancedSearch->Load();
		$this->WargaNegara->AdvancedSearch->Load();
		$this->Kelamin->AdvancedSearch->Load();
		$this->TempatLahir->AdvancedSearch->Load();
		$this->TanggalLahir->AdvancedSearch->Load();
		$this->AgamaID->AdvancedSearch->Load();
		$this->Darah->AdvancedSearch->Load();
		$this->StatusSipil->AdvancedSearch->Load();
		$this->AlamatDomisili->AdvancedSearch->Load();
		$this->RT->AdvancedSearch->Load();
		$this->RW->AdvancedSearch->Load();
		$this->KodePos->AdvancedSearch->Load();
		$this->ProvinsiID->AdvancedSearch->Load();
		$this->KabupatenKotaID->AdvancedSearch->Load();
		$this->KecamatanID->AdvancedSearch->Load();
		$this->DesaID->AdvancedSearch->Load();
		$this->AnakKe->AdvancedSearch->Load();
		$this->JumlahSaudara->AdvancedSearch->Load();
		$this->Telepon->AdvancedSearch->Load();
		$this->_Email->AdvancedSearch->Load();
		$this->NamaAyah->AdvancedSearch->Load();
		$this->AgamaAyah->AdvancedSearch->Load();
		$this->PendidikanAyah->AdvancedSearch->Load();
		$this->PekerjaanAyah->AdvancedSearch->Load();
		$this->HidupAyah->AdvancedSearch->Load();
		$this->NamaIbu->AdvancedSearch->Load();
		$this->AgamaIbu->AdvancedSearch->Load();
		$this->PendidikanIbu->AdvancedSearch->Load();
		$this->PekerjaanIbu->AdvancedSearch->Load();
		$this->HidupIbu->AdvancedSearch->Load();
		$this->AlamatOrtu->AdvancedSearch->Load();
		$this->RTOrtu->AdvancedSearch->Load();
		$this->RWOrtu->AdvancedSearch->Load();
		$this->KodePosOrtu->AdvancedSearch->Load();
		$this->ProvinsiIDOrtu->AdvancedSearch->Load();
		$this->KabupatenIDOrtu->AdvancedSearch->Load();
		$this->KecamatanIDOrtu->AdvancedSearch->Load();
		$this->DesaIDOrtu->AdvancedSearch->Load();
		$this->NegaraIDOrtu->AdvancedSearch->Load();
		$this->TeleponOrtu->AdvancedSearch->Load();
		$this->HandphoneOrtu->AdvancedSearch->Load();
		$this->EmailOrtu->AdvancedSearch->Load();
		$this->AsalSekolah->AdvancedSearch->Load();
		$this->AlamatSekolah->AdvancedSearch->Load();
		$this->ProvinsiIDSekolah->AdvancedSearch->Load();
		$this->KabupatenIDSekolah->AdvancedSearch->Load();
		$this->KecamatanIDSekolah->AdvancedSearch->Load();
		$this->DesaIDSekolah->AdvancedSearch->Load();
		$this->NilaiSekolah->AdvancedSearch->Load();
		$this->TahunLulus->AdvancedSearch->Load();
		$this->IjazahSekolah->AdvancedSearch->Load();
		$this->TglIjazah->AdvancedSearch->Load();
		$this->LockStatus->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->StudentID); // StudentID
			$this->UpdateSort($this->Nama); // Nama
			$this->UpdateSort($this->ProdiID); // ProdiID
			$this->UpdateSort($this->StudentStatusID); // StudentStatusID
			$this->UpdateSort($this->TahunID); // TahunID
			$this->UpdateSort($this->Foto); // Foto
			$this->UpdateSort($this->NIK); // NIK
			$this->UpdateSort($this->Kelamin); // Kelamin
			$this->UpdateSort($this->TempatLahir); // TempatLahir
			$this->UpdateSort($this->TanggalLahir); // TanggalLahir
			$this->UpdateSort($this->AlamatDomisili); // AlamatDomisili
			$this->UpdateSort($this->Telepon); // Telepon
			$this->UpdateSort($this->_Email); // Email
			$this->UpdateSort($this->NA); // NA
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->getSqlOrderBy() <> "") {
				$sOrderBy = $this->getSqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->StudentID->setSort("");
				$this->Nama->setSort("");
				$this->ProdiID->setSort("");
				$this->StudentStatusID->setSort("");
				$this->TahunID->setSort("");
				$this->Foto->setSort("");
				$this->NIK->setSort("");
				$this->Kelamin->setSort("");
				$this->TempatLahir->setSort("");
				$this->TanggalLahir->setSort("");
				$this->AlamatDomisili->setSort("");
				$this->Telepon->setSort("");
				$this->_Email->setSort("");
				$this->NA->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// "griddelete"
		if ($this->AllowAddDeleteRow) {
			$item = &$this->ListOptions->Add("griddelete");
			$item->CssStyle = "white-space: nowrap;";
			$item->OnLeft = TRUE;
			$item->Visible = FALSE; // Default hidden
		}

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanView();
		$item->OnLeft = TRUE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanEdit();
		$item->OnLeft = TRUE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanDelete();
		$item->OnLeft = TRUE;

		// List actions
		$item = &$this->ListOptions->Add("listactions");
		$item->CssStyle = "white-space: nowrap;";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;
		$item->ShowInButtonGroup = FALSE;
		$item->ShowInDropDown = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = TRUE;
		$item->Header = "<input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\">";
		$item->MoveTo(0);
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// "sequence"
		$item = &$this->ListOptions->Add("sequence");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = TRUE;
		$item->OnLeft = TRUE; // Always on left
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseImageAndText = TRUE;
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		if ($this->ListOptions->UseButtonGroup && ew_IsMobile())
			$this->ListOptions->UseDropDownButton = TRUE;
		$this->ListOptions->ButtonClass = "btn-sm"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$this->SetupListOptionsExt();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// Set up row action and key
		if (is_numeric($this->RowIndex) && $this->CurrentMode <> "view") {
			$objForm->Index = $this->RowIndex;
			$ActionName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormActionName);
			$OldKeyName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormOldKeyName);
			$KeyName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormKeyName);
			$BlankRowName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormBlankRowName);
			if ($this->RowAction <> "")
				$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $ActionName . "\" id=\"" . $ActionName . "\" value=\"" . $this->RowAction . "\">";
			if ($this->RowAction == "delete") {
				$rowkey = $objForm->GetValue($this->FormKeyName);
				$this->SetupKeyValues($rowkey);
			}
			if ($this->RowAction == "insert" && $this->CurrentAction == "F" && $this->EmptyRow())
				$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $BlankRowName . "\" id=\"" . $BlankRowName . "\" value=\"1\">";
		}

		// "delete"
		if ($this->AllowAddDeleteRow) {
			if ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$option = &$this->ListOptions;
				$option->UseButtonGroup = TRUE; // Use button group for grid delete button
				$option->UseImageAndText = TRUE; // Use image and text for grid delete button
				$oListOpt = &$option->Items["griddelete"];
				if (!$Security->CanDelete() && is_numeric($this->RowIndex) && ($this->RowAction == "" || $this->RowAction == "edit")) { // Do not allow delete existing record
					$oListOpt->Body = "&nbsp;";
				} else {
					$oListOpt->Body = "<a class=\"ewGridLink ewGridDelete\" title=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" onclick=\"return ew_DeleteGridRow(this, " . $this->RowIndex . ");\">" . $Language->Phrase("DeleteLink") . "</a>";
				}
			}
		}

		// "sequence"
		$oListOpt = &$this->ListOptions->Items["sequence"];
		$oListOpt->Body = ew_FormatSeqNo($this->RecCnt);

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		$viewcaption = ew_HtmlTitle($Language->Phrase("ViewLink"));
		if ($Security->CanView()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" title=\"" . $viewcaption . "\" data-caption=\"" . $viewcaption . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		$editcaption = ew_HtmlTitle($Language->Phrase("EditLink"));
		if ($Security->CanEdit()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if ($Security->CanDelete())
			$oListOpt->Body = "<a class=\"ewRowLink ewDelete\"" . "" . " title=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		else
			$oListOpt->Body = "";

		// Set up list action buttons
		$oListOpt = &$this->ListOptions->GetItem("listactions");
		if ($oListOpt && $this->Export == "" && $this->CurrentAction == "") {
			$body = "";
			$links = array();
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_SINGLE && $listaction->Allow) {
					$action = $listaction->Action;
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode(str_replace(" ewIcon", "", $listaction->Icon)) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\"></span> " : "";
					$links[] = "<li><a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . $listaction->Caption . "</a></li>";
					if (count($links) == 1) // Single button
						$body = "<a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" title=\"" . ew_HtmlTitle($caption) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $Language->Phrase("ListActionButton") . "</a>";
				}
			}
			if (count($links) > 1) { // More than one buttons, use dropdown
				$body = "<button class=\"dropdown-toggle btn btn-default btn-sm ewActions\" title=\"" . ew_HtmlTitle($Language->Phrase("ListActionButton")) . "\" data-toggle=\"dropdown\">" . $Language->Phrase("ListActionButton") . "<b class=\"caret\"></b></button>";
				$content = "";
				foreach ($links as $link)
					$content .= "<li>" . $link . "</li>";
				$body .= "<ul class=\"dropdown-menu" . ($oListOpt->OnLeft ? "" : " dropdown-menu-right") . "\">". $content . "</ul>";
				$body = "<div class=\"btn-group\">" . $body . "</div>";
			}
			if (count($links) > 0) {
				$oListOpt->Body = $body;
				$oListOpt->Visible = TRUE;
			}
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->StudentID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event);'>";
		if ($this->CurrentAction == "gridedit" && is_numeric($this->RowIndex)) {
			$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $KeyName . "\" id=\"" . $KeyName . "\" value=\"" . $this->StudentID->CurrentValue . "\">";
		}
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$addcaption = ew_HtmlTitle($Language->Phrase("AddLink"));
		$item->Body = "<a class=\"ewAddEdit ewAdd\" title=\"" . $addcaption . "\" data-caption=\"" . $addcaption . "\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("AddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());
		$item = &$option->Add("gridadd");
		$item->Body = "<a class=\"ewAddEdit ewGridAdd\" title=\"" . ew_HtmlTitle($Language->Phrase("GridAddLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridAddLink")) . "\" href=\"" . ew_HtmlEncode($this->GridAddUrl) . "\">" . $Language->Phrase("GridAddLink") . "</a>";
		$item->Visible = ($this->GridAddUrl <> "" && $Security->CanAdd());

		// Add grid edit
		$option = $options["addedit"];
		$item = &$option->Add("gridedit");
		$item->Body = "<a class=\"ewAddEdit ewGridEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("GridEditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridEditLink")) . "\" href=\"" . ew_HtmlEncode($this->GridEditUrl) . "\">" . $Language->Phrase("GridEditLink") . "</a>";
		$item->Visible = ($this->GridEditUrl <> "" && $Security->CanEdit());
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseImageAndText = TRUE;
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-sm"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fstudentlistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fstudentlistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton;
		$this->FilterOptions->DropDownButtonPhrase = $Language->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		if ($this->CurrentAction <> "gridadd" && $this->CurrentAction <> "gridedit") { // Not grid add/edit mode
			$option = &$options["action"];

			// Set up list action buttons
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_MULTIPLE) {
					$item = &$option->Add("custom_" . $listaction->Action);
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode($listaction->Icon) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\"></span> " : $caption;
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fstudentlist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
					$item->Visible = $listaction->Allow;
				}
			}

			// Hide grid edit and other options
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$option->HideAllOptions();
			}
		} else { // Grid add/edit mode

			// Hide all options first
			foreach ($options as &$option)
				$option->HideAllOptions();
			if ($this->CurrentAction == "gridadd") {
				if ($this->AllowAddDeleteRow) {

					// Add add blank row
					$option = &$options["addedit"];
					$option->UseDropDownButton = FALSE;
					$option->UseImageAndText = TRUE;
					$item = &$option->Add("addblankrow");
					$item->Body = "<a class=\"ewAddEdit ewAddBlankRow\" title=\"" . ew_HtmlTitle($Language->Phrase("AddBlankRow")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("AddBlankRow")) . "\" href=\"javascript:void(0);\" onclick=\"ew_AddGridRow(this);\">" . $Language->Phrase("AddBlankRow") . "</a>";
					$item->Visible = $Security->CanAdd();
				}
				$option = &$options["action"];
				$option->UseDropDownButton = FALSE;
				$option->UseImageAndText = TRUE;

				// Add grid insert
				$item = &$option->Add("gridinsert");
				$item->Body = "<a class=\"ewAction ewGridInsert\" title=\"" . ew_HtmlTitle($Language->Phrase("GridInsertLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridInsertLink")) . "\" href=\"\" onclick=\"return ewForms(this).Submit('" . $this->PageName() . "');\">" . $Language->Phrase("GridInsertLink") . "</a>";

				// Add grid cancel
				$item = &$option->Add("gridcancel");
				$cancelurl = $this->AddMasterUrl($this->PageUrl() . "a=cancel");
				$item->Body = "<a class=\"ewAction ewGridCancel\" title=\"" . ew_HtmlTitle($Language->Phrase("GridCancelLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridCancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->Phrase("GridCancelLink") . "</a>";
			}
			if ($this->CurrentAction == "gridedit") {
				if ($this->AllowAddDeleteRow) {

					// Add add blank row
					$option = &$options["addedit"];
					$option->UseDropDownButton = FALSE;
					$option->UseImageAndText = TRUE;
					$item = &$option->Add("addblankrow");
					$item->Body = "<a class=\"ewAddEdit ewAddBlankRow\" title=\"" . ew_HtmlTitle($Language->Phrase("AddBlankRow")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("AddBlankRow")) . "\" href=\"javascript:void(0);\" onclick=\"ew_AddGridRow(this);\">" . $Language->Phrase("AddBlankRow") . "</a>";
					$item->Visible = $Security->CanAdd();
				}
				$option = &$options["action"];
				$option->UseDropDownButton = FALSE;
				$option->UseImageAndText = TRUE;
					$item = &$option->Add("gridsave");
					$item->Body = "<a class=\"ewAction ewGridSave\" title=\"" . ew_HtmlTitle($Language->Phrase("GridSaveLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridSaveLink")) . "\" href=\"\" onclick=\"return ewForms(this).Submit('" . $this->PageName() . "');\">" . $Language->Phrase("GridSaveLink") . "</a>";
					$item = &$option->Add("gridcancel");
					$cancelurl = $this->AddMasterUrl($this->PageUrl() . "a=cancel");
					$item->Body = "<a class=\"ewAction ewGridCancel\" title=\"" . ew_HtmlTitle($Language->Phrase("GridCancelLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridCancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->Phrase("GridCancelLink") . "</a>";
			}
		}
	}

	// Process list action
	function ProcessListAction() {
		global $Language, $Security;
		$userlist = "";
		$user = "";
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {

			// Check permission first
			$ActionCaption = $UserAction;
			if (array_key_exists($UserAction, $this->ListActions->Items)) {
				$ActionCaption = $this->ListActions->Items[$UserAction]->Caption;
				if (!$this->ListActions->Items[$UserAction]->Allow) {
					$errmsg = str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionNotAllowed"));
					if (@$_POST["ajax"] == $UserAction) // Ajax
						echo "<p class=\"text-danger\">" . $errmsg . "</p>";
					else
						$this->setFailureMessage($errmsg);
					return FALSE;
				}
			}
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$this->CurrentAction = $UserAction;

			// Call row action event
			if ($rs && !$rs->EOF) {
				$conn->BeginTrans();
				$this->SelectedCount = $rs->RecordCount();
				$this->SelectedIndex = 0;
				while (!$rs->EOF) {
					$this->SelectedIndex++;
					$row = $rs->fields;
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
					$rs->MoveNext();
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionFailed")));
					}
				}
			}
			if ($rs)
				$rs->Close();
			$this->CurrentAction = ""; // Clear action
			if (@$_POST["ajax"] == $UserAction) { // Ajax
				if ($this->getSuccessMessage() <> "") {
					echo "<p class=\"text-success\">" . $this->getSuccessMessage() . "</p>";
					$this->ClearSuccessMessage(); // Clear message
				}
				if ($this->getFailureMessage() <> "") {
					echo "<p class=\"text-danger\">" . $this->getFailureMessage() . "</p>";
					$this->ClearFailureMessage(); // Clear message
				}
				return TRUE;
			}
		}
		return FALSE; // Not ajax request
	}

	// Set up search options
	function SetupSearchOptions() {
		global $Language;
		$this->SearchOptions = new cListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Search button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = ($this->SearchWhere <> "") ? " active" : "";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fstudentlistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		if (ew_IsMobile())
			$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"studentsrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		else
			$item->Body = "<button type=\"button\" class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-table=\"student\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" onclick=\"ew_ModalDialogShow({lnk:this,url:'studentsrch.php',caption:'" . $Language->Phrase("Search") . "'});\">" . $Language->Phrase("AdvancedSearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Search highlight button
		$item = &$this->SearchOptions->Add("searchhighlight");
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewHighlight active\" title=\"" . $Language->Phrase("Highlight") . "\" data-caption=\"" . $Language->Phrase("Highlight") . "\" data-toggle=\"button\" data-form=\"fstudentlistsrch\" data-name=\"" . $this->HighlightName() . "\">" . $Language->Phrase("HighlightBtn") . "</button>";
		$item->Visible = ($this->SearchWhere <> "" && $this->TotalRecs > 0);

		// Button group for search
		$this->SearchOptions->UseDropDownButton = FALSE;
		$this->SearchOptions->UseImageAndText = TRUE;
		$this->SearchOptions->UseButtonGroup = TRUE;
		$this->SearchOptions->DropDownButtonPhrase = $Language->Phrase("ButtonSearch");

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide search options
		if ($this->Export <> "" || $this->CurrentAction <> "")
			$this->SearchOptions->HideAllOptions();
		global $Security;
		if (!$Security->CanSearch()) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}
	}

	function SetupListOptionsExt() {
		global $Security, $Language;

		// Hide detail items for dropdown if necessary
		$this->ListOptions->HideDetailItemsForDropDown();
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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

	// Load default values
	function LoadDefaultValues() {
		$this->StudentID->CurrentValue = NULL;
		$this->StudentID->OldValue = $this->StudentID->CurrentValue;
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->ProdiID->CurrentValue = NULL;
		$this->ProdiID->OldValue = $this->ProdiID->CurrentValue;
		$this->StudentStatusID->CurrentValue = NULL;
		$this->StudentStatusID->OldValue = $this->StudentStatusID->CurrentValue;
		$this->TahunID->CurrentValue = NULL;
		$this->TahunID->OldValue = $this->TahunID->CurrentValue;
		$this->Foto->Upload->DbValue = NULL;
		$this->Foto->OldValue = $this->Foto->Upload->DbValue;
		$this->NIK->CurrentValue = NULL;
		$this->NIK->OldValue = $this->NIK->CurrentValue;
		$this->Kelamin->CurrentValue = NULL;
		$this->Kelamin->OldValue = $this->Kelamin->CurrentValue;
		$this->TempatLahir->CurrentValue = NULL;
		$this->TempatLahir->OldValue = $this->TempatLahir->CurrentValue;
		$this->TanggalLahir->CurrentValue = NULL;
		$this->TanggalLahir->OldValue = $this->TanggalLahir->CurrentValue;
		$this->AlamatDomisili->CurrentValue = NULL;
		$this->AlamatDomisili->OldValue = $this->AlamatDomisili->CurrentValue;
		$this->Telepon->CurrentValue = NULL;
		$this->Telepon->OldValue = $this->Telepon->CurrentValue;
		$this->_Email->CurrentValue = NULL;
		$this->_Email->OldValue = $this->_Email->CurrentValue;
		$this->NA->CurrentValue = "N";
		$this->NA->OldValue = $this->NA->CurrentValue;
	}

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	// Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// StudentID

		$this->StudentID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_StudentID"]);
		if ($this->StudentID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->StudentID->AdvancedSearch->SearchOperator = @$_GET["z_StudentID"];

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Nama"]);
		if ($this->Nama->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Nama->AdvancedSearch->SearchOperator = @$_GET["z_Nama"];

		// LevelID
		$this->LevelID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_LevelID"]);
		if ($this->LevelID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->LevelID->AdvancedSearch->SearchOperator = @$_GET["z_LevelID"];

		// ProdiID
		$this->ProdiID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_ProdiID"]);
		if ($this->ProdiID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->ProdiID->AdvancedSearch->SearchOperator = @$_GET["z_ProdiID"];

		// StudentStatusID
		$this->StudentStatusID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_StudentStatusID"]);
		if ($this->StudentStatusID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->StudentStatusID->AdvancedSearch->SearchOperator = @$_GET["z_StudentStatusID"];

		// TahunID
		$this->TahunID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TahunID"]);
		if ($this->TahunID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TahunID->AdvancedSearch->SearchOperator = @$_GET["z_TahunID"];

		// Foto
		$this->Foto->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Foto"]);
		if ($this->Foto->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Foto->AdvancedSearch->SearchOperator = @$_GET["z_Foto"];

		// NIK
		$this->NIK->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NIK"]);
		if ($this->NIK->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NIK->AdvancedSearch->SearchOperator = @$_GET["z_NIK"];

		// WargaNegara
		$this->WargaNegara->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_WargaNegara"]);
		if ($this->WargaNegara->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->WargaNegara->AdvancedSearch->SearchOperator = @$_GET["z_WargaNegara"];

		// Kelamin
		$this->Kelamin->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Kelamin"]);
		if ($this->Kelamin->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Kelamin->AdvancedSearch->SearchOperator = @$_GET["z_Kelamin"];

		// TempatLahir
		$this->TempatLahir->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TempatLahir"]);
		if ($this->TempatLahir->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TempatLahir->AdvancedSearch->SearchOperator = @$_GET["z_TempatLahir"];

		// TanggalLahir
		$this->TanggalLahir->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TanggalLahir"]);
		if ($this->TanggalLahir->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TanggalLahir->AdvancedSearch->SearchOperator = @$_GET["z_TanggalLahir"];

		// AgamaID
		$this->AgamaID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_AgamaID"]);
		if ($this->AgamaID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->AgamaID->AdvancedSearch->SearchOperator = @$_GET["z_AgamaID"];

		// Darah
		$this->Darah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Darah"]);
		if ($this->Darah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Darah->AdvancedSearch->SearchOperator = @$_GET["z_Darah"];

		// StatusSipil
		$this->StatusSipil->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_StatusSipil"]);
		if ($this->StatusSipil->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->StatusSipil->AdvancedSearch->SearchOperator = @$_GET["z_StatusSipil"];

		// AlamatDomisili
		$this->AlamatDomisili->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_AlamatDomisili"]);
		if ($this->AlamatDomisili->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->AlamatDomisili->AdvancedSearch->SearchOperator = @$_GET["z_AlamatDomisili"];

		// RT
		$this->RT->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_RT"]);
		if ($this->RT->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->RT->AdvancedSearch->SearchOperator = @$_GET["z_RT"];

		// RW
		$this->RW->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_RW"]);
		if ($this->RW->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->RW->AdvancedSearch->SearchOperator = @$_GET["z_RW"];

		// KodePos
		$this->KodePos->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KodePos"]);
		if ($this->KodePos->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KodePos->AdvancedSearch->SearchOperator = @$_GET["z_KodePos"];

		// ProvinsiID
		$this->ProvinsiID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_ProvinsiID"]);
		if ($this->ProvinsiID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->ProvinsiID->AdvancedSearch->SearchOperator = @$_GET["z_ProvinsiID"];

		// KabupatenKotaID
		$this->KabupatenKotaID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KabupatenKotaID"]);
		if ($this->KabupatenKotaID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KabupatenKotaID->AdvancedSearch->SearchOperator = @$_GET["z_KabupatenKotaID"];

		// KecamatanID
		$this->KecamatanID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KecamatanID"]);
		if ($this->KecamatanID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KecamatanID->AdvancedSearch->SearchOperator = @$_GET["z_KecamatanID"];

		// DesaID
		$this->DesaID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_DesaID"]);
		if ($this->DesaID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->DesaID->AdvancedSearch->SearchOperator = @$_GET["z_DesaID"];

		// AnakKe
		$this->AnakKe->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_AnakKe"]);
		if ($this->AnakKe->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->AnakKe->AdvancedSearch->SearchOperator = @$_GET["z_AnakKe"];

		// JumlahSaudara
		$this->JumlahSaudara->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_JumlahSaudara"]);
		if ($this->JumlahSaudara->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->JumlahSaudara->AdvancedSearch->SearchOperator = @$_GET["z_JumlahSaudara"];

		// Telepon
		$this->Telepon->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Telepon"]);
		if ($this->Telepon->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Telepon->AdvancedSearch->SearchOperator = @$_GET["z_Telepon"];

		// Email
		$this->_Email->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x__Email"]);
		if ($this->_Email->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->_Email->AdvancedSearch->SearchOperator = @$_GET["z__Email"];

		// NamaAyah
		$this->NamaAyah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NamaAyah"]);
		if ($this->NamaAyah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NamaAyah->AdvancedSearch->SearchOperator = @$_GET["z_NamaAyah"];

		// AgamaAyah
		$this->AgamaAyah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_AgamaAyah"]);
		if ($this->AgamaAyah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->AgamaAyah->AdvancedSearch->SearchOperator = @$_GET["z_AgamaAyah"];

		// PendidikanAyah
		$this->PendidikanAyah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PendidikanAyah"]);
		if ($this->PendidikanAyah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PendidikanAyah->AdvancedSearch->SearchOperator = @$_GET["z_PendidikanAyah"];

		// PekerjaanAyah
		$this->PekerjaanAyah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PekerjaanAyah"]);
		if ($this->PekerjaanAyah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PekerjaanAyah->AdvancedSearch->SearchOperator = @$_GET["z_PekerjaanAyah"];

		// HidupAyah
		$this->HidupAyah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_HidupAyah"]);
		if ($this->HidupAyah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->HidupAyah->AdvancedSearch->SearchOperator = @$_GET["z_HidupAyah"];

		// NamaIbu
		$this->NamaIbu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NamaIbu"]);
		if ($this->NamaIbu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NamaIbu->AdvancedSearch->SearchOperator = @$_GET["z_NamaIbu"];

		// AgamaIbu
		$this->AgamaIbu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_AgamaIbu"]);
		if ($this->AgamaIbu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->AgamaIbu->AdvancedSearch->SearchOperator = @$_GET["z_AgamaIbu"];

		// PendidikanIbu
		$this->PendidikanIbu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PendidikanIbu"]);
		if ($this->PendidikanIbu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PendidikanIbu->AdvancedSearch->SearchOperator = @$_GET["z_PendidikanIbu"];

		// PekerjaanIbu
		$this->PekerjaanIbu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PekerjaanIbu"]);
		if ($this->PekerjaanIbu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PekerjaanIbu->AdvancedSearch->SearchOperator = @$_GET["z_PekerjaanIbu"];

		// HidupIbu
		$this->HidupIbu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_HidupIbu"]);
		if ($this->HidupIbu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->HidupIbu->AdvancedSearch->SearchOperator = @$_GET["z_HidupIbu"];

		// AlamatOrtu
		$this->AlamatOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_AlamatOrtu"]);
		if ($this->AlamatOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->AlamatOrtu->AdvancedSearch->SearchOperator = @$_GET["z_AlamatOrtu"];

		// RTOrtu
		$this->RTOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_RTOrtu"]);
		if ($this->RTOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->RTOrtu->AdvancedSearch->SearchOperator = @$_GET["z_RTOrtu"];

		// RWOrtu
		$this->RWOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_RWOrtu"]);
		if ($this->RWOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->RWOrtu->AdvancedSearch->SearchOperator = @$_GET["z_RWOrtu"];

		// KodePosOrtu
		$this->KodePosOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KodePosOrtu"]);
		if ($this->KodePosOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KodePosOrtu->AdvancedSearch->SearchOperator = @$_GET["z_KodePosOrtu"];

		// ProvinsiIDOrtu
		$this->ProvinsiIDOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_ProvinsiIDOrtu"]);
		if ($this->ProvinsiIDOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->ProvinsiIDOrtu->AdvancedSearch->SearchOperator = @$_GET["z_ProvinsiIDOrtu"];

		// KabupatenIDOrtu
		$this->KabupatenIDOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KabupatenIDOrtu"]);
		if ($this->KabupatenIDOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KabupatenIDOrtu->AdvancedSearch->SearchOperator = @$_GET["z_KabupatenIDOrtu"];

		// KecamatanIDOrtu
		$this->KecamatanIDOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KecamatanIDOrtu"]);
		if ($this->KecamatanIDOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KecamatanIDOrtu->AdvancedSearch->SearchOperator = @$_GET["z_KecamatanIDOrtu"];

		// DesaIDOrtu
		$this->DesaIDOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_DesaIDOrtu"]);
		if ($this->DesaIDOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->DesaIDOrtu->AdvancedSearch->SearchOperator = @$_GET["z_DesaIDOrtu"];

		// NegaraIDOrtu
		$this->NegaraIDOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NegaraIDOrtu"]);
		if ($this->NegaraIDOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NegaraIDOrtu->AdvancedSearch->SearchOperator = @$_GET["z_NegaraIDOrtu"];

		// TeleponOrtu
		$this->TeleponOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TeleponOrtu"]);
		if ($this->TeleponOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TeleponOrtu->AdvancedSearch->SearchOperator = @$_GET["z_TeleponOrtu"];

		// HandphoneOrtu
		$this->HandphoneOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_HandphoneOrtu"]);
		if ($this->HandphoneOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->HandphoneOrtu->AdvancedSearch->SearchOperator = @$_GET["z_HandphoneOrtu"];

		// EmailOrtu
		$this->EmailOrtu->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_EmailOrtu"]);
		if ($this->EmailOrtu->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->EmailOrtu->AdvancedSearch->SearchOperator = @$_GET["z_EmailOrtu"];

		// AsalSekolah
		$this->AsalSekolah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_AsalSekolah"]);
		if ($this->AsalSekolah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->AsalSekolah->AdvancedSearch->SearchOperator = @$_GET["z_AsalSekolah"];

		// AlamatSekolah
		$this->AlamatSekolah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_AlamatSekolah"]);
		if ($this->AlamatSekolah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->AlamatSekolah->AdvancedSearch->SearchOperator = @$_GET["z_AlamatSekolah"];

		// ProvinsiIDSekolah
		$this->ProvinsiIDSekolah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_ProvinsiIDSekolah"]);
		if ($this->ProvinsiIDSekolah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->ProvinsiIDSekolah->AdvancedSearch->SearchOperator = @$_GET["z_ProvinsiIDSekolah"];

		// KabupatenIDSekolah
		$this->KabupatenIDSekolah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KabupatenIDSekolah"]);
		if ($this->KabupatenIDSekolah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KabupatenIDSekolah->AdvancedSearch->SearchOperator = @$_GET["z_KabupatenIDSekolah"];

		// KecamatanIDSekolah
		$this->KecamatanIDSekolah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KecamatanIDSekolah"]);
		if ($this->KecamatanIDSekolah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KecamatanIDSekolah->AdvancedSearch->SearchOperator = @$_GET["z_KecamatanIDSekolah"];

		// DesaIDSekolah
		$this->DesaIDSekolah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_DesaIDSekolah"]);
		if ($this->DesaIDSekolah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->DesaIDSekolah->AdvancedSearch->SearchOperator = @$_GET["z_DesaIDSekolah"];

		// NilaiSekolah
		$this->NilaiSekolah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NilaiSekolah"]);
		if ($this->NilaiSekolah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NilaiSekolah->AdvancedSearch->SearchOperator = @$_GET["z_NilaiSekolah"];

		// TahunLulus
		$this->TahunLulus->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TahunLulus"]);
		if ($this->TahunLulus->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TahunLulus->AdvancedSearch->SearchOperator = @$_GET["z_TahunLulus"];

		// IjazahSekolah
		$this->IjazahSekolah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_IjazahSekolah"]);
		if ($this->IjazahSekolah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->IjazahSekolah->AdvancedSearch->SearchOperator = @$_GET["z_IjazahSekolah"];

		// TglIjazah
		$this->TglIjazah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TglIjazah"]);
		if ($this->TglIjazah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TglIjazah->AdvancedSearch->SearchOperator = @$_GET["z_TglIjazah"];

		// NA
		$this->NA->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NA"]);
		if ($this->NA->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NA->AdvancedSearch->SearchOperator = @$_GET["z_NA"];
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->StudentID->FldIsDetailKey) {
			$this->StudentID->setFormValue($objForm->GetValue("x_StudentID"));
		}
		$this->StudentID->setOldValue($objForm->GetValue("o_StudentID"));
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		$this->Nama->setOldValue($objForm->GetValue("o_Nama"));
		if (!$this->ProdiID->FldIsDetailKey) {
			$this->ProdiID->setFormValue($objForm->GetValue("x_ProdiID"));
		}
		$this->ProdiID->setOldValue($objForm->GetValue("o_ProdiID"));
		if (!$this->StudentStatusID->FldIsDetailKey) {
			$this->StudentStatusID->setFormValue($objForm->GetValue("x_StudentStatusID"));
		}
		$this->StudentStatusID->setOldValue($objForm->GetValue("o_StudentStatusID"));
		if (!$this->TahunID->FldIsDetailKey) {
			$this->TahunID->setFormValue($objForm->GetValue("x_TahunID"));
		}
		$this->TahunID->setOldValue($objForm->GetValue("o_TahunID"));
		if (!$this->NIK->FldIsDetailKey) {
			$this->NIK->setFormValue($objForm->GetValue("x_NIK"));
		}
		$this->NIK->setOldValue($objForm->GetValue("o_NIK"));
		if (!$this->Kelamin->FldIsDetailKey) {
			$this->Kelamin->setFormValue($objForm->GetValue("x_Kelamin"));
		}
		$this->Kelamin->setOldValue($objForm->GetValue("o_Kelamin"));
		if (!$this->TempatLahir->FldIsDetailKey) {
			$this->TempatLahir->setFormValue($objForm->GetValue("x_TempatLahir"));
		}
		$this->TempatLahir->setOldValue($objForm->GetValue("o_TempatLahir"));
		if (!$this->TanggalLahir->FldIsDetailKey) {
			$this->TanggalLahir->setFormValue($objForm->GetValue("x_TanggalLahir"));
			$this->TanggalLahir->CurrentValue = ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0);
		}
		$this->TanggalLahir->setOldValue($objForm->GetValue("o_TanggalLahir"));
		if (!$this->AlamatDomisili->FldIsDetailKey) {
			$this->AlamatDomisili->setFormValue($objForm->GetValue("x_AlamatDomisili"));
		}
		$this->AlamatDomisili->setOldValue($objForm->GetValue("o_AlamatDomisili"));
		if (!$this->Telepon->FldIsDetailKey) {
			$this->Telepon->setFormValue($objForm->GetValue("x_Telepon"));
		}
		$this->Telepon->setOldValue($objForm->GetValue("o_Telepon"));
		if (!$this->_Email->FldIsDetailKey) {
			$this->_Email->setFormValue($objForm->GetValue("x__Email"));
		}
		$this->_Email->setOldValue($objForm->GetValue("o__Email"));
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
		$this->NA->setOldValue($objForm->GetValue("o_NA"));
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->StudentID->CurrentValue = $this->StudentID->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->ProdiID->CurrentValue = $this->ProdiID->FormValue;
		$this->StudentStatusID->CurrentValue = $this->StudentStatusID->FormValue;
		$this->TahunID->CurrentValue = $this->TahunID->FormValue;
		$this->NIK->CurrentValue = $this->NIK->FormValue;
		$this->Kelamin->CurrentValue = $this->Kelamin->FormValue;
		$this->TempatLahir->CurrentValue = $this->TempatLahir->FormValue;
		$this->TanggalLahir->CurrentValue = $this->TanggalLahir->FormValue;
		$this->TanggalLahir->CurrentValue = ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0);
		$this->AlamatDomisili->CurrentValue = $this->AlamatDomisili->FormValue;
		$this->Telepon->CurrentValue = $this->Telepon->FormValue;
		$this->_Email->CurrentValue = $this->_Email->FormValue;
		$this->NA->CurrentValue = $this->NA->FormValue;
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {

		// Load List page SQL
		$sSql = $this->SelectSQL();
		$conn = &$this->Connection();

		// Load recordset
		$dbtype = ew_GetConnectionType($this->DBID);
		if ($this->UseSelectLimit) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			if ($dbtype == "MSSQL") {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset, array("_hasOrderBy" => trim($this->getOrderBy()) || trim($this->getSessionOrderBy())));
			} else {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset);
			}
			$conn->raiseErrorFn = '';
		} else {
			$rs = ew_LoadRecordset($sSql, $conn);
		}

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("StudentID")) <> "")
			$this->StudentID->CurrentValue = $this->getKey("StudentID"); // StudentID
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
			if ($this->Export == "")
				$this->StudentID->ViewValue = ew_Highlight($this->HighlightName(), $this->StudentID->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->StudentID->AdvancedSearch->getValue("x"), "");

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";
			if ($this->Export == "")
				$this->Nama->ViewValue = ew_Highlight($this->HighlightName(), $this->Nama->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Nama->AdvancedSearch->getValue("x"), "");

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
			if ($this->Export == "")
				$this->TahunID->ViewValue = ew_Highlight($this->HighlightName(), $this->TahunID->ViewValue, "", "", $this->TahunID->AdvancedSearch->getValue("x"), "");

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
				$this->Foto->LinkAttrs["data-rel"] = "student_x" . $this->RowCnt . "_Foto";
				ew_AppendClass($this->Foto->LinkAttrs["class"], "ewLightbox");
			}

			// NIK
			$this->NIK->LinkCustomAttributes = "";
			$this->NIK->HrefValue = "";
			$this->NIK->TooltipValue = "";
			if ($this->Export == "")
				$this->NIK->ViewValue = ew_Highlight($this->HighlightName(), $this->NIK->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->NIK->AdvancedSearch->getValue("x"), "");

			// Kelamin
			$this->Kelamin->LinkCustomAttributes = "";
			$this->Kelamin->HrefValue = "";
			$this->Kelamin->TooltipValue = "";

			// TempatLahir
			$this->TempatLahir->LinkCustomAttributes = "";
			$this->TempatLahir->HrefValue = "";
			$this->TempatLahir->TooltipValue = "";
			if ($this->Export == "")
				$this->TempatLahir->ViewValue = ew_Highlight($this->HighlightName(), $this->TempatLahir->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->TempatLahir->AdvancedSearch->getValue("x"), "");

			// TanggalLahir
			$this->TanggalLahir->LinkCustomAttributes = "";
			$this->TanggalLahir->HrefValue = "";
			$this->TanggalLahir->TooltipValue = "";

			// AlamatDomisili
			$this->AlamatDomisili->LinkCustomAttributes = "";
			$this->AlamatDomisili->HrefValue = "";
			$this->AlamatDomisili->TooltipValue = "";
			if ($this->Export == "")
				$this->AlamatDomisili->ViewValue = ew_Highlight($this->HighlightName(), $this->AlamatDomisili->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->AlamatDomisili->AdvancedSearch->getValue("x"), "");

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";
			$this->Telepon->TooltipValue = "";
			if ($this->Export == "")
				$this->Telepon->ViewValue = ew_Highlight($this->HighlightName(), $this->Telepon->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Telepon->AdvancedSearch->getValue("x"), "");

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";
			if ($this->Export == "")
				$this->_Email->ViewValue = ew_Highlight($this->HighlightName(), $this->_Email->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->_Email->AdvancedSearch->getValue("x"), "");

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// StudentID
			$this->StudentID->EditAttrs["class"] = "form-control";
			$this->StudentID->EditCustomAttributes = "";
			$this->StudentID->EditValue = ew_HtmlEncode($this->StudentID->CurrentValue);
			$this->StudentID->PlaceHolder = ew_RemoveHtml($this->StudentID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

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
			if (is_numeric($this->RowIndex) && !$this->EventCancelled) ew_RenderUploadField($this->Foto, $this->RowIndex);

			// NIK
			$this->NIK->EditAttrs["class"] = "form-control";
			$this->NIK->EditCustomAttributes = "";
			$this->NIK->EditValue = ew_HtmlEncode($this->NIK->CurrentValue);
			$this->NIK->PlaceHolder = ew_RemoveHtml($this->NIK->FldCaption());

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

			// AlamatDomisili
			$this->AlamatDomisili->EditAttrs["class"] = "form-control";
			$this->AlamatDomisili->EditCustomAttributes = "";
			$this->AlamatDomisili->EditValue = ew_HtmlEncode($this->AlamatDomisili->CurrentValue);
			$this->AlamatDomisili->PlaceHolder = ew_RemoveHtml($this->AlamatDomisili->FldCaption());

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

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// StudentID

			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

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

			// Kelamin
			$this->Kelamin->LinkCustomAttributes = "";
			$this->Kelamin->HrefValue = "";

			// TempatLahir
			$this->TempatLahir->LinkCustomAttributes = "";
			$this->TempatLahir->HrefValue = "";

			// TanggalLahir
			$this->TanggalLahir->LinkCustomAttributes = "";
			$this->TanggalLahir->HrefValue = "";

			// AlamatDomisili
			$this->AlamatDomisili->LinkCustomAttributes = "";
			$this->AlamatDomisili->HrefValue = "";

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
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
			if (is_numeric($this->RowIndex) && !$this->EventCancelled) ew_RenderUploadField($this->Foto, $this->RowIndex);

			// NIK
			$this->NIK->EditAttrs["class"] = "form-control";
			$this->NIK->EditCustomAttributes = "";
			$this->NIK->EditValue = ew_HtmlEncode($this->NIK->CurrentValue);
			$this->NIK->PlaceHolder = ew_RemoveHtml($this->NIK->FldCaption());

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

			// AlamatDomisili
			$this->AlamatDomisili->EditAttrs["class"] = "form-control";
			$this->AlamatDomisili->EditCustomAttributes = "";
			$this->AlamatDomisili->EditValue = ew_HtmlEncode($this->AlamatDomisili->CurrentValue);
			$this->AlamatDomisili->PlaceHolder = ew_RemoveHtml($this->AlamatDomisili->FldCaption());

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

			// Kelamin
			$this->Kelamin->LinkCustomAttributes = "";
			$this->Kelamin->HrefValue = "";

			// TempatLahir
			$this->TempatLahir->LinkCustomAttributes = "";
			$this->TempatLahir->HrefValue = "";

			// TanggalLahir
			$this->TanggalLahir->LinkCustomAttributes = "";
			$this->TanggalLahir->HrefValue = "";

			// AlamatDomisili
			$this->AlamatDomisili->LinkCustomAttributes = "";
			$this->AlamatDomisili->HrefValue = "";

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";

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

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
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
		if (!ew_CheckInteger($this->TahunID->FormValue)) {
			ew_AddMessage($gsFormError, $this->TahunID->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TanggalLahir->FormValue)) {
			ew_AddMessage($gsFormError, $this->TanggalLahir->FldErrMsg());
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

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $Language, $Security;
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$rows = ($rs) ? $rs->GetRows() : array();
		if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteBegin")); // Batch delete begin

		// Clone old rows
		$rsold = $rows;
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['StudentID'];
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteSuccess")); // Batch delete success
		} else {
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
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

			// Kelamin
			$this->Kelamin->SetDbValueDef($rsnew, $this->Kelamin->CurrentValue, NULL, $this->Kelamin->ReadOnly);

			// TempatLahir
			$this->TempatLahir->SetDbValueDef($rsnew, $this->TempatLahir->CurrentValue, NULL, $this->TempatLahir->ReadOnly);

			// TanggalLahir
			$this->TanggalLahir->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0), NULL, $this->TanggalLahir->ReadOnly);

			// AlamatDomisili
			$this->AlamatDomisili->SetDbValueDef($rsnew, $this->AlamatDomisili->CurrentValue, NULL, $this->AlamatDomisili->ReadOnly);

			// Telepon
			$this->Telepon->SetDbValueDef($rsnew, $this->Telepon->CurrentValue, NULL, $this->Telepon->ReadOnly);

			// Email
			$this->_Email->SetDbValueDef($rsnew, $this->_Email->CurrentValue, NULL, $this->_Email->ReadOnly);

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

	// Add record
	function AddRow($rsold = NULL) {
		global $Language, $Security;
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
			$this->Foto->OldUploadPath = "upload";
			$this->Foto->UploadPath = $this->Foto->OldUploadPath;
		}
		$rsnew = array();

		// StudentID
		$this->StudentID->SetDbValueDef($rsnew, $this->StudentID->CurrentValue, "", FALSE);

		// Nama
		$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, NULL, FALSE);

		// ProdiID
		$this->ProdiID->SetDbValueDef($rsnew, $this->ProdiID->CurrentValue, NULL, FALSE);

		// StudentStatusID
		$this->StudentStatusID->SetDbValueDef($rsnew, $this->StudentStatusID->CurrentValue, NULL, FALSE);

		// TahunID
		$this->TahunID->SetDbValueDef($rsnew, $this->TahunID->CurrentValue, NULL, FALSE);

		// Foto
		if ($this->Foto->Visible && !$this->Foto->Upload->KeepFile) {
			$this->Foto->Upload->DbValue = ""; // No need to delete old file
			if ($this->Foto->Upload->FileName == "") {
				$rsnew['Foto'] = NULL;
			} else {
				$rsnew['Foto'] = $this->Foto->Upload->FileName;
			}
		}

		// NIK
		$this->NIK->SetDbValueDef($rsnew, $this->NIK->CurrentValue, NULL, FALSE);

		// Kelamin
		$this->Kelamin->SetDbValueDef($rsnew, $this->Kelamin->CurrentValue, NULL, FALSE);

		// TempatLahir
		$this->TempatLahir->SetDbValueDef($rsnew, $this->TempatLahir->CurrentValue, NULL, FALSE);

		// TanggalLahir
		$this->TanggalLahir->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->TanggalLahir->CurrentValue, 0), NULL, FALSE);

		// AlamatDomisili
		$this->AlamatDomisili->SetDbValueDef($rsnew, $this->AlamatDomisili->CurrentValue, NULL, FALSE);

		// Telepon
		$this->Telepon->SetDbValueDef($rsnew, $this->Telepon->CurrentValue, NULL, FALSE);

		// Email
		$this->_Email->SetDbValueDef($rsnew, $this->_Email->CurrentValue, NULL, FALSE);

		// NA
		$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), NULL, strval($this->NA->CurrentValue) == "");
		if ($this->Foto->Visible && !$this->Foto->Upload->KeepFile) {
			$this->Foto->UploadPath = "upload";
			if (!ew_Empty($this->Foto->Upload->Value)) {
				$rsnew['Foto'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->Foto->UploadPath), $rsnew['Foto']); // Get new file name
			}
		}

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['StudentID']) == "") {
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
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}

		// Foto
		ew_CleanUploadTempPath($this->Foto, $this->Foto->Upload->Index);
		return $AddRow;
	}

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->StudentID->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->LevelID->AdvancedSearch->Load();
		$this->ProdiID->AdvancedSearch->Load();
		$this->StudentStatusID->AdvancedSearch->Load();
		$this->TahunID->AdvancedSearch->Load();
		$this->Foto->AdvancedSearch->Load();
		$this->NIK->AdvancedSearch->Load();
		$this->WargaNegara->AdvancedSearch->Load();
		$this->Kelamin->AdvancedSearch->Load();
		$this->TempatLahir->AdvancedSearch->Load();
		$this->TanggalLahir->AdvancedSearch->Load();
		$this->AgamaID->AdvancedSearch->Load();
		$this->Darah->AdvancedSearch->Load();
		$this->StatusSipil->AdvancedSearch->Load();
		$this->AlamatDomisili->AdvancedSearch->Load();
		$this->RT->AdvancedSearch->Load();
		$this->RW->AdvancedSearch->Load();
		$this->KodePos->AdvancedSearch->Load();
		$this->ProvinsiID->AdvancedSearch->Load();
		$this->KabupatenKotaID->AdvancedSearch->Load();
		$this->KecamatanID->AdvancedSearch->Load();
		$this->DesaID->AdvancedSearch->Load();
		$this->AnakKe->AdvancedSearch->Load();
		$this->JumlahSaudara->AdvancedSearch->Load();
		$this->Telepon->AdvancedSearch->Load();
		$this->_Email->AdvancedSearch->Load();
		$this->NamaAyah->AdvancedSearch->Load();
		$this->AgamaAyah->AdvancedSearch->Load();
		$this->PendidikanAyah->AdvancedSearch->Load();
		$this->PekerjaanAyah->AdvancedSearch->Load();
		$this->HidupAyah->AdvancedSearch->Load();
		$this->NamaIbu->AdvancedSearch->Load();
		$this->AgamaIbu->AdvancedSearch->Load();
		$this->PendidikanIbu->AdvancedSearch->Load();
		$this->PekerjaanIbu->AdvancedSearch->Load();
		$this->HidupIbu->AdvancedSearch->Load();
		$this->AlamatOrtu->AdvancedSearch->Load();
		$this->RTOrtu->AdvancedSearch->Load();
		$this->RWOrtu->AdvancedSearch->Load();
		$this->KodePosOrtu->AdvancedSearch->Load();
		$this->ProvinsiIDOrtu->AdvancedSearch->Load();
		$this->KabupatenIDOrtu->AdvancedSearch->Load();
		$this->KecamatanIDOrtu->AdvancedSearch->Load();
		$this->DesaIDOrtu->AdvancedSearch->Load();
		$this->NegaraIDOrtu->AdvancedSearch->Load();
		$this->TeleponOrtu->AdvancedSearch->Load();
		$this->HandphoneOrtu->AdvancedSearch->Load();
		$this->EmailOrtu->AdvancedSearch->Load();
		$this->AsalSekolah->AdvancedSearch->Load();
		$this->AlamatSekolah->AdvancedSearch->Load();
		$this->ProvinsiIDSekolah->AdvancedSearch->Load();
		$this->KabupatenIDSekolah->AdvancedSearch->Load();
		$this->KecamatanIDSekolah->AdvancedSearch->Load();
		$this->DesaIDSekolah->AdvancedSearch->Load();
		$this->NilaiSekolah->AdvancedSearch->Load();
		$this->TahunLulus->AdvancedSearch->Load();
		$this->IjazahSekolah->AdvancedSearch->Load();
		$this->TglIjazah->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" title=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = FALSE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\" class=\"ewExportLink ewHtml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = FALSE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\" class=\"ewExportLink ewXml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = FALSE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\" class=\"ewExportLink ewCsv\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = FALSE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = "";
		$item->Body = "<button id=\"emf_student\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_student',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fstudentlist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
		$item->Visible = FALSE;

		// Drop down button for export
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = TRUE;
		$this->ExportOptions->UseDropDownButton = FALSE;
		if ($this->ExportOptions->UseButtonGroup && ew_IsMobile())
			$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = $this->UseSelectLimit;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if (!$this->Recordset)
				$this->Recordset = $this->LoadRecordset();
			$rs = &$this->Recordset;
			if ($rs)
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$this->ExportDoc = ew_ExportDocument($this, "h");
		$Doc = &$this->ExportDoc;
		if ($bSelectLimit) {
			$this->StartRec = 1;
			$this->StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {

			//$this->StartRec = $this->StartRec;
			//$this->StopRec = $this->StopRec;

		}

		// Call Page Exporting server event
		$this->ExportDoc->ExportCustom = !$this->Page_Exporting();
		$ParentTable = "";
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$Doc->Text .= $sHeader;
		$this->ExportDocument($Doc, $rs, $this->StartRec, $this->StopRec, "");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$Doc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Call Page Exported server event
		$this->Page_Exported();

		// Export header and footer
		$Doc->ExportHeaderAndFooter();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED && $this->Export <> "pdf")
			echo ew_DebugMsg();

		// Output data
		$Doc->Export();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, "", $this->TableVar, TRUE);
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
		if (CurrentUserLevel()=="-1") {
			$this->OtherOptions["addedit"]->UseDropDownButton = FALSE; // jangan gunakan style DropDownButton
			$my_options = &$this->OtherOptions; // pastikan menggunakan area OtherOptions
			$my_option = $my_options["addedit"]; // dekat tombol addedit
			$my_item = &$my_option->Add("Impor Data"); // tambahkan tombol baru
			$my_item->Body = "<a class=\"ewAddEdit ewAdd\" title=\"Impor Data\" data-caption=\"Impor Data\" href=\"import_data.php\">Impor Data</a>"; // definisikan link, style, dan caption tombol
		}
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

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}

	// Page Exporting event
	// $this->ExportDoc = export document object
	function Page_Exporting() {

		//$this->ExportDoc->Text = "my header"; // Export header
		//return FALSE; // Return FALSE to skip default export and use Row_Export event

		return TRUE; // Return TRUE to use default export and skip Row_Export event
	}

	// Row Export event
	// $this->ExportDoc = export document object
	function Row_Export($rs) {

		//$this->ExportDoc->Text .= "my content"; // Build HTML with field value: $rs["MyField"] or $this->MyField->ViewValue
	}

	// Page Exported event
	// $this->ExportDoc = export document object
	function Page_Exported() {

		//$this->ExportDoc->Text .= "my footer"; // Export footer
		//echo $this->ExportDoc->Text;

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($student_list)) $student_list = new cstudent_list();

// Page init
$student_list->Page_Init();

// Page main
$student_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$student_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($student->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fstudentlist = new ew_Form("fstudentlist", "list");
fstudentlist.FormKeyCountName = '<?php echo $student_list->FormKeyCountName ?>';

// Validate form
fstudentlist.Validate = function() {
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
		var checkrow = (gridinsert) ? !this.EmptyRow(infix) : true;
		if (checkrow) {
			addcnt++;
			elm = this.GetElements("x" + infix + "_StudentID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $student->StudentID->FldCaption(), $student->StudentID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TahunID");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->TahunID->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_TanggalLahir");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->TanggalLahir->FldErrMsg()) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
		} // End Grid Add checking
	}
	if (gridinsert && addcnt == 0) { // No row added
		ew_Alert(ewLanguage.Phrase("NoAddRecord"));
		return false;
	}
	return true;
}

// Check empty row
fstudentlist.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "StudentID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Nama", false)) return false;
	if (ew_ValueChanged(fobj, infix, "ProdiID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "StudentStatusID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "TahunID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Foto", false)) return false;
	if (ew_ValueChanged(fobj, infix, "NIK", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Kelamin", false)) return false;
	if (ew_ValueChanged(fobj, infix, "TempatLahir", false)) return false;
	if (ew_ValueChanged(fobj, infix, "TanggalLahir", false)) return false;
	if (ew_ValueChanged(fobj, infix, "AlamatDomisili", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Telepon", false)) return false;
	if (ew_ValueChanged(fobj, infix, "_Email", false)) return false;
	if (ew_ValueChanged(fobj, infix, "NA", true)) return false;
	return true;
}

// Form_CustomValidate event
fstudentlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fstudentlist.ValidateRequired = true;
<?php } else { ?>
fstudentlist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fstudentlist.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fstudentlist.Lists["x_StudentStatusID"] = {"LinkField":"x_StatusStudentID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statusstudent"};
fstudentlist.Lists["x_Kelamin"] = {"LinkField":"x_Kelamin","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kelamin"};
fstudentlist.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fstudentlist.Lists["x_NA"].Options = <?php echo json_encode($student->NA->Options()) ?>;

// Form object for search
var CurrentSearchForm = fstudentlistsrch = new ew_Form("fstudentlistsrch");

// Init search panel as collapsed
if (fstudentlistsrch) fstudentlistsrch.InitSearchPanel = true;
</script>
<style type="text/css">
.ewTablePreviewRow { /* main table preview row color */
	background-color: #FFFFFF; /* preview row color */
}
.ewTablePreviewRow .ewGrid {
	display: table;
}
.ewTablePreviewRow .ewGrid .ewTable {
	width: auto;
}
</style>
<div id="ewPreview" class="hide"><ul class="nav nav-tabs"></ul><div class="tab-content"><div class="tab-pane fade"></div></div></div>
<script type="text/javascript" src="phpjs/ewpreview.min.js"></script>
<script type="text/javascript">
var EW_PREVIEW_PLACEMENT = EW_CSS_FLIP ? "left" : "right";
var EW_PREVIEW_SINGLE_ROW = false;
var EW_PREVIEW_OVERLAY = false;
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($student->Export == "") { ?>
<div class="ewToolbar">
<?php if ($student->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($student_list->TotalRecs > 0 && $student_list->ExportOptions->Visible()) { ?>
<?php $student_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($student_list->SearchOptions->Visible()) { ?>
<?php $student_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($student_list->FilterOptions->Visible()) { ?>
<?php $student_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php if ($student->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
if ($student->CurrentAction == "gridadd") {
	$student->CurrentFilter = "0=1";
	$student_list->StartRec = 1;
	$student_list->DisplayRecs = $student->GridAddRowCount;
	$student_list->TotalRecs = $student_list->DisplayRecs;
	$student_list->StopRec = $student_list->DisplayRecs;
} else {
	$bSelectLimit = $student_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($student_list->TotalRecs <= 0)
			$student_list->TotalRecs = $student->SelectRecordCount();
	} else {
		if (!$student_list->Recordset && ($student_list->Recordset = $student_list->LoadRecordset()))
			$student_list->TotalRecs = $student_list->Recordset->RecordCount();
	}
	$student_list->StartRec = 1;
	if ($student_list->DisplayRecs <= 0 || ($student->Export <> "" && $student->ExportAll)) // Display all records
		$student_list->DisplayRecs = $student_list->TotalRecs;
	if (!($student->Export <> "" && $student->ExportAll))
		$student_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$student_list->Recordset = $student_list->LoadRecordset($student_list->StartRec-1, $student_list->DisplayRecs);

	// Set no record found message
	if ($student->CurrentAction == "" && $student_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$student_list->setWarningMessage(ew_DeniedMsg());
		if ($student_list->SearchWhere == "0=101")
			$student_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$student_list->setWarningMessage($Language->Phrase("NoRecord"));
	}

	// Audit trail on search
	if ($student_list->AuditTrailOnSearch && $student_list->Command == "search" && !$student_list->RestoreSearch) {
		$searchparm = ew_ServerVar("QUERY_STRING");
		$searchsql = $student_list->getSessionWhere();
		$student_list->WriteAuditTrailOnSearch($searchparm, $searchsql);
	}
}
$student_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($student->Export == "" && $student->CurrentAction == "") { ?>
<form name="fstudentlistsrch" id="fstudentlistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($student_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fstudentlistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="student">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($student_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($student_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $student_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($student_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($student_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($student_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($student_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
		</ul>
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $student_list->ShowPageHeader(); ?>
<?php
$student_list->ShowMessage();
?>
<?php if ($student_list->TotalRecs > 0 || $student->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid student">
<?php if ($student->Export == "") { ?>
<div class="panel-heading ewGridUpperPanel">
<?php if ($student->CurrentAction <> "gridadd" && $student->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($student_list->Pager)) $student_list->Pager = new cPrevNextPager($student_list->StartRec, $student_list->DisplayRecs, $student_list->TotalRecs) ?>
<?php if ($student_list->Pager->RecordCount > 0 && $student_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($student_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $student_list->PageUrl() ?>start=<?php echo $student_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($student_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $student_list->PageUrl() ?>start=<?php echo $student_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $student_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($student_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $student_list->PageUrl() ?>start=<?php echo $student_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($student_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $student_list->PageUrl() ?>start=<?php echo $student_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $student_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $student_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $student_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $student_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($student_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="fstudentlist" id="fstudentlist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($student_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $student_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="student">
<div id="gmp_student" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($student_list->TotalRecs > 0 || $student->CurrentAction == "gridedit") { ?>
<table id="tbl_studentlist" class="table ewTable">
<?php echo $student->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$student_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$student_list->RenderListOptions();

// Render list options (header, left)
$student_list->ListOptions->Render("header", "left");
?>
<?php if ($student->StudentID->Visible) { // StudentID ?>
	<?php if ($student->SortUrl($student->StudentID) == "") { ?>
		<th data-name="StudentID"><div id="elh_student_StudentID" class="student_StudentID"><div class="ewTableHeaderCaption"><?php echo $student->StudentID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="StudentID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->StudentID) ?>',1);"><div id="elh_student_StudentID" class="student_StudentID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->StudentID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($student->StudentID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->StudentID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->Nama->Visible) { // Nama ?>
	<?php if ($student->SortUrl($student->Nama) == "") { ?>
		<th data-name="Nama"><div id="elh_student_Nama" class="student_Nama"><div class="ewTableHeaderCaption"><?php echo $student->Nama->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Nama"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->Nama) ?>',1);"><div id="elh_student_Nama" class="student_Nama">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->Nama->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($student->Nama->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->Nama->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->ProdiID->Visible) { // ProdiID ?>
	<?php if ($student->SortUrl($student->ProdiID) == "") { ?>
		<th data-name="ProdiID"><div id="elh_student_ProdiID" class="student_ProdiID"><div class="ewTableHeaderCaption"><?php echo $student->ProdiID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="ProdiID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->ProdiID) ?>',1);"><div id="elh_student_ProdiID" class="student_ProdiID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->ProdiID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($student->ProdiID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->ProdiID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->StudentStatusID->Visible) { // StudentStatusID ?>
	<?php if ($student->SortUrl($student->StudentStatusID) == "") { ?>
		<th data-name="StudentStatusID"><div id="elh_student_StudentStatusID" class="student_StudentStatusID"><div class="ewTableHeaderCaption"><?php echo $student->StudentStatusID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="StudentStatusID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->StudentStatusID) ?>',1);"><div id="elh_student_StudentStatusID" class="student_StudentStatusID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->StudentStatusID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($student->StudentStatusID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->StudentStatusID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->TahunID->Visible) { // TahunID ?>
	<?php if ($student->SortUrl($student->TahunID) == "") { ?>
		<th data-name="TahunID"><div id="elh_student_TahunID" class="student_TahunID"><div class="ewTableHeaderCaption"><?php echo $student->TahunID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TahunID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->TahunID) ?>',1);"><div id="elh_student_TahunID" class="student_TahunID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->TahunID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($student->TahunID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->TahunID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->Foto->Visible) { // Foto ?>
	<?php if ($student->SortUrl($student->Foto) == "") { ?>
		<th data-name="Foto"><div id="elh_student_Foto" class="student_Foto"><div class="ewTableHeaderCaption"><?php echo $student->Foto->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Foto"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->Foto) ?>',1);"><div id="elh_student_Foto" class="student_Foto">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->Foto->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($student->Foto->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->Foto->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->NIK->Visible) { // NIK ?>
	<?php if ($student->SortUrl($student->NIK) == "") { ?>
		<th data-name="NIK"><div id="elh_student_NIK" class="student_NIK"><div class="ewTableHeaderCaption"><?php echo $student->NIK->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NIK"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->NIK) ?>',1);"><div id="elh_student_NIK" class="student_NIK">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->NIK->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($student->NIK->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->NIK->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->Kelamin->Visible) { // Kelamin ?>
	<?php if ($student->SortUrl($student->Kelamin) == "") { ?>
		<th data-name="Kelamin"><div id="elh_student_Kelamin" class="student_Kelamin"><div class="ewTableHeaderCaption"><?php echo $student->Kelamin->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Kelamin"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->Kelamin) ?>',1);"><div id="elh_student_Kelamin" class="student_Kelamin">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->Kelamin->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($student->Kelamin->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->Kelamin->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->TempatLahir->Visible) { // TempatLahir ?>
	<?php if ($student->SortUrl($student->TempatLahir) == "") { ?>
		<th data-name="TempatLahir"><div id="elh_student_TempatLahir" class="student_TempatLahir"><div class="ewTableHeaderCaption"><?php echo $student->TempatLahir->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TempatLahir"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->TempatLahir) ?>',1);"><div id="elh_student_TempatLahir" class="student_TempatLahir">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->TempatLahir->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($student->TempatLahir->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->TempatLahir->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->TanggalLahir->Visible) { // TanggalLahir ?>
	<?php if ($student->SortUrl($student->TanggalLahir) == "") { ?>
		<th data-name="TanggalLahir"><div id="elh_student_TanggalLahir" class="student_TanggalLahir"><div class="ewTableHeaderCaption"><?php echo $student->TanggalLahir->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TanggalLahir"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->TanggalLahir) ?>',1);"><div id="elh_student_TanggalLahir" class="student_TanggalLahir">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->TanggalLahir->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($student->TanggalLahir->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->TanggalLahir->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->AlamatDomisili->Visible) { // AlamatDomisili ?>
	<?php if ($student->SortUrl($student->AlamatDomisili) == "") { ?>
		<th data-name="AlamatDomisili"><div id="elh_student_AlamatDomisili" class="student_AlamatDomisili"><div class="ewTableHeaderCaption"><?php echo $student->AlamatDomisili->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="AlamatDomisili"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->AlamatDomisili) ?>',1);"><div id="elh_student_AlamatDomisili" class="student_AlamatDomisili">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->AlamatDomisili->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($student->AlamatDomisili->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->AlamatDomisili->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->Telepon->Visible) { // Telepon ?>
	<?php if ($student->SortUrl($student->Telepon) == "") { ?>
		<th data-name="Telepon"><div id="elh_student_Telepon" class="student_Telepon"><div class="ewTableHeaderCaption"><?php echo $student->Telepon->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Telepon"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->Telepon) ?>',1);"><div id="elh_student_Telepon" class="student_Telepon">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->Telepon->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($student->Telepon->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->Telepon->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->_Email->Visible) { // Email ?>
	<?php if ($student->SortUrl($student->_Email) == "") { ?>
		<th data-name="_Email"><div id="elh_student__Email" class="student__Email"><div class="ewTableHeaderCaption"><?php echo $student->_Email->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="_Email"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->_Email) ?>',1);"><div id="elh_student__Email" class="student__Email">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->_Email->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($student->_Email->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->_Email->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($student->NA->Visible) { // NA ?>
	<?php if ($student->SortUrl($student->NA) == "") { ?>
		<th data-name="NA"><div id="elh_student_NA" class="student_NA"><div class="ewTableHeaderCaption"><?php echo $student->NA->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NA"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $student->SortUrl($student->NA) ?>',1);"><div id="elh_student_NA" class="student_NA">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $student->NA->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($student->NA->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($student->NA->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$student_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($student->ExportAll && $student->Export <> "") {
	$student_list->StopRec = $student_list->TotalRecs;
} else {

	// Set the last record to display
	if ($student_list->TotalRecs > $student_list->StartRec + $student_list->DisplayRecs - 1)
		$student_list->StopRec = $student_list->StartRec + $student_list->DisplayRecs - 1;
	else
		$student_list->StopRec = $student_list->TotalRecs;
}

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($student_list->FormKeyCountName) && ($student->CurrentAction == "gridadd" || $student->CurrentAction == "gridedit" || $student->CurrentAction == "F")) {
		$student_list->KeyCount = $objForm->GetValue($student_list->FormKeyCountName);
		$student_list->StopRec = $student_list->StartRec + $student_list->KeyCount - 1;
	}
}
$student_list->RecCnt = $student_list->StartRec - 1;
if ($student_list->Recordset && !$student_list->Recordset->EOF) {
	$student_list->Recordset->MoveFirst();
	$bSelectLimit = $student_list->UseSelectLimit;
	if (!$bSelectLimit && $student_list->StartRec > 1)
		$student_list->Recordset->Move($student_list->StartRec - 1);
} elseif (!$student->AllowAddDeleteRow && $student_list->StopRec == 0) {
	$student_list->StopRec = $student->GridAddRowCount;
}

// Initialize aggregate
$student->RowType = EW_ROWTYPE_AGGREGATEINIT;
$student->ResetAttrs();
$student_list->RenderRow();
if ($student->CurrentAction == "gridadd")
	$student_list->RowIndex = 0;
if ($student->CurrentAction == "gridedit")
	$student_list->RowIndex = 0;
while ($student_list->RecCnt < $student_list->StopRec) {
	$student_list->RecCnt++;
	if (intval($student_list->RecCnt) >= intval($student_list->StartRec)) {
		$student_list->RowCnt++;
		if ($student->CurrentAction == "gridadd" || $student->CurrentAction == "gridedit" || $student->CurrentAction == "F") {
			$student_list->RowIndex++;
			$objForm->Index = $student_list->RowIndex;
			if ($objForm->HasValue($student_list->FormActionName))
				$student_list->RowAction = strval($objForm->GetValue($student_list->FormActionName));
			elseif ($student->CurrentAction == "gridadd")
				$student_list->RowAction = "insert";
			else
				$student_list->RowAction = "";
		}

		// Set up key count
		$student_list->KeyCount = $student_list->RowIndex;

		// Init row class and style
		$student->ResetAttrs();
		$student->CssClass = "";
		if ($student->CurrentAction == "gridadd") {
			$student_list->LoadDefaultValues(); // Load default values
		} else {
			$student_list->LoadRowValues($student_list->Recordset); // Load row values
		}
		$student->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($student->CurrentAction == "gridadd") // Grid add
			$student->RowType = EW_ROWTYPE_ADD; // Render add
		if ($student->CurrentAction == "gridadd" && $student->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$student_list->RestoreCurrentRowFormValues($student_list->RowIndex); // Restore form values
		if ($student->CurrentAction == "gridedit") { // Grid edit
			if ($student->EventCancelled) {
				$student_list->RestoreCurrentRowFormValues($student_list->RowIndex); // Restore form values
			}
			if ($student_list->RowAction == "insert")
				$student->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$student->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($student->CurrentAction == "gridedit" && ($student->RowType == EW_ROWTYPE_EDIT || $student->RowType == EW_ROWTYPE_ADD) && $student->EventCancelled) // Update failed
			$student_list->RestoreCurrentRowFormValues($student_list->RowIndex); // Restore form values
		if ($student->RowType == EW_ROWTYPE_EDIT) // Edit row
			$student_list->EditRowCnt++;

		// Set up row id / data-rowindex
		$student->RowAttrs = array_merge($student->RowAttrs, array('data-rowindex'=>$student_list->RowCnt, 'id'=>'r' . $student_list->RowCnt . '_student', 'data-rowtype'=>$student->RowType));

		// Render row
		$student_list->RenderRow();

		// Render list options
		$student_list->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($student_list->RowAction <> "delete" && $student_list->RowAction <> "insertdelete" && !($student_list->RowAction == "insert" && $student->CurrentAction == "F" && $student_list->EmptyRow())) {
?>
	<tr<?php echo $student->RowAttributes() ?>>
<?php

// Render list options (body, left)
$student_list->ListOptions->Render("body", "left", $student_list->RowCnt);
?>
	<?php if ($student->StudentID->Visible) { // StudentID ?>
		<td data-name="StudentID"<?php echo $student->StudentID->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_StudentID" class="form-group student_StudentID">
<input type="text" data-table="student" data-field="x_StudentID" name="x<?php echo $student_list->RowIndex ?>_StudentID" id="x<?php echo $student_list->RowIndex ?>_StudentID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($student->StudentID->getPlaceHolder()) ?>" value="<?php echo $student->StudentID->EditValue ?>"<?php echo $student->StudentID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_StudentID" name="o<?php echo $student_list->RowIndex ?>_StudentID" id="o<?php echo $student_list->RowIndex ?>_StudentID" value="<?php echo ew_HtmlEncode($student->StudentID->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_StudentID" class="form-group student_StudentID">
<span<?php echo $student->StudentID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->StudentID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_StudentID" name="x<?php echo $student_list->RowIndex ?>_StudentID" id="x<?php echo $student_list->RowIndex ?>_StudentID" value="<?php echo ew_HtmlEncode($student->StudentID->CurrentValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_StudentID" class="student_StudentID">
<span<?php echo $student->StudentID->ViewAttributes() ?>>
<?php echo $student->StudentID->ListViewValue() ?></span>
</span>
<?php } ?>
<a id="<?php echo $student_list->PageObjName . "_row_" . $student_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($student->Nama->Visible) { // Nama ?>
		<td data-name="Nama"<?php echo $student->Nama->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Nama" class="form-group student_Nama">
<input type="text" data-table="student" data-field="x_Nama" name="x<?php echo $student_list->RowIndex ?>_Nama" id="x<?php echo $student_list->RowIndex ?>_Nama" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Nama->getPlaceHolder()) ?>" value="<?php echo $student->Nama->EditValue ?>"<?php echo $student->Nama->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_Nama" name="o<?php echo $student_list->RowIndex ?>_Nama" id="o<?php echo $student_list->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($student->Nama->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Nama" class="form-group student_Nama">
<input type="text" data-table="student" data-field="x_Nama" name="x<?php echo $student_list->RowIndex ?>_Nama" id="x<?php echo $student_list->RowIndex ?>_Nama" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Nama->getPlaceHolder()) ?>" value="<?php echo $student->Nama->EditValue ?>"<?php echo $student->Nama->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Nama" class="student_Nama">
<span<?php echo $student->Nama->ViewAttributes() ?>>
<?php echo $student->Nama->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->ProdiID->Visible) { // ProdiID ?>
		<td data-name="ProdiID"<?php echo $student->ProdiID->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_ProdiID" class="form-group student_ProdiID">
<select data-table="student" data-field="x_ProdiID" data-value-separator="<?php echo $student->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $student_list->RowIndex ?>_ProdiID" name="x<?php echo $student_list->RowIndex ?>_ProdiID"<?php echo $student->ProdiID->EditAttributes() ?>>
<?php echo $student->ProdiID->SelectOptionListHtml("x<?php echo $student_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $student_list->RowIndex ?>_ProdiID" id="s_x<?php echo $student_list->RowIndex ?>_ProdiID" value="<?php echo $student->ProdiID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="student" data-field="x_ProdiID" name="o<?php echo $student_list->RowIndex ?>_ProdiID" id="o<?php echo $student_list->RowIndex ?>_ProdiID" value="<?php echo ew_HtmlEncode($student->ProdiID->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_ProdiID" class="form-group student_ProdiID">
<select data-table="student" data-field="x_ProdiID" data-value-separator="<?php echo $student->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $student_list->RowIndex ?>_ProdiID" name="x<?php echo $student_list->RowIndex ?>_ProdiID"<?php echo $student->ProdiID->EditAttributes() ?>>
<?php echo $student->ProdiID->SelectOptionListHtml("x<?php echo $student_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $student_list->RowIndex ?>_ProdiID" id="s_x<?php echo $student_list->RowIndex ?>_ProdiID" value="<?php echo $student->ProdiID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_ProdiID" class="student_ProdiID">
<span<?php echo $student->ProdiID->ViewAttributes() ?>>
<?php echo $student->ProdiID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->StudentStatusID->Visible) { // StudentStatusID ?>
		<td data-name="StudentStatusID"<?php echo $student->StudentStatusID->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_StudentStatusID" class="form-group student_StudentStatusID">
<select data-table="student" data-field="x_StudentStatusID" data-value-separator="<?php echo $student->StudentStatusID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $student_list->RowIndex ?>_StudentStatusID" name="x<?php echo $student_list->RowIndex ?>_StudentStatusID"<?php echo $student->StudentStatusID->EditAttributes() ?>>
<?php echo $student->StudentStatusID->SelectOptionListHtml("x<?php echo $student_list->RowIndex ?>_StudentStatusID") ?>
</select>
<input type="hidden" name="s_x<?php echo $student_list->RowIndex ?>_StudentStatusID" id="s_x<?php echo $student_list->RowIndex ?>_StudentStatusID" value="<?php echo $student->StudentStatusID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="student" data-field="x_StudentStatusID" name="o<?php echo $student_list->RowIndex ?>_StudentStatusID" id="o<?php echo $student_list->RowIndex ?>_StudentStatusID" value="<?php echo ew_HtmlEncode($student->StudentStatusID->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_StudentStatusID" class="form-group student_StudentStatusID">
<select data-table="student" data-field="x_StudentStatusID" data-value-separator="<?php echo $student->StudentStatusID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $student_list->RowIndex ?>_StudentStatusID" name="x<?php echo $student_list->RowIndex ?>_StudentStatusID"<?php echo $student->StudentStatusID->EditAttributes() ?>>
<?php echo $student->StudentStatusID->SelectOptionListHtml("x<?php echo $student_list->RowIndex ?>_StudentStatusID") ?>
</select>
<input type="hidden" name="s_x<?php echo $student_list->RowIndex ?>_StudentStatusID" id="s_x<?php echo $student_list->RowIndex ?>_StudentStatusID" value="<?php echo $student->StudentStatusID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_StudentStatusID" class="student_StudentStatusID">
<span<?php echo $student->StudentStatusID->ViewAttributes() ?>>
<?php echo $student->StudentStatusID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->TahunID->Visible) { // TahunID ?>
		<td data-name="TahunID"<?php echo $student->TahunID->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_TahunID" class="form-group student_TahunID">
<input type="text" data-table="student" data-field="x_TahunID" name="x<?php echo $student_list->RowIndex ?>_TahunID" id="x<?php echo $student_list->RowIndex ?>_TahunID" size="30" placeholder="<?php echo ew_HtmlEncode($student->TahunID->getPlaceHolder()) ?>" value="<?php echo $student->TahunID->EditValue ?>"<?php echo $student->TahunID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_TahunID" name="o<?php echo $student_list->RowIndex ?>_TahunID" id="o<?php echo $student_list->RowIndex ?>_TahunID" value="<?php echo ew_HtmlEncode($student->TahunID->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_TahunID" class="form-group student_TahunID">
<input type="text" data-table="student" data-field="x_TahunID" name="x<?php echo $student_list->RowIndex ?>_TahunID" id="x<?php echo $student_list->RowIndex ?>_TahunID" size="30" placeholder="<?php echo ew_HtmlEncode($student->TahunID->getPlaceHolder()) ?>" value="<?php echo $student->TahunID->EditValue ?>"<?php echo $student->TahunID->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_TahunID" class="student_TahunID">
<span<?php echo $student->TahunID->ViewAttributes() ?>>
<?php echo $student->TahunID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->Foto->Visible) { // Foto ?>
		<td data-name="Foto"<?php echo $student->Foto->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Foto" class="form-group student_Foto">
<div id="fd_x<?php echo $student_list->RowIndex ?>_Foto">
<span title="<?php echo $student->Foto->FldTitle() ? $student->Foto->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($student->Foto->ReadOnly || $student->Foto->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="student" data-field="x_Foto" name="x<?php echo $student_list->RowIndex ?>_Foto" id="x<?php echo $student_list->RowIndex ?>_Foto"<?php echo $student->Foto->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x<?php echo $student_list->RowIndex ?>_Foto" id= "fn_x<?php echo $student_list->RowIndex ?>_Foto" value="<?php echo $student->Foto->Upload->FileName ?>">
<input type="hidden" name="fa_x<?php echo $student_list->RowIndex ?>_Foto" id= "fa_x<?php echo $student_list->RowIndex ?>_Foto" value="0">
<input type="hidden" name="fs_x<?php echo $student_list->RowIndex ?>_Foto" id= "fs_x<?php echo $student_list->RowIndex ?>_Foto" value="255">
<input type="hidden" name="fx_x<?php echo $student_list->RowIndex ?>_Foto" id= "fx_x<?php echo $student_list->RowIndex ?>_Foto" value="<?php echo $student->Foto->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x<?php echo $student_list->RowIndex ?>_Foto" id= "fm_x<?php echo $student_list->RowIndex ?>_Foto" value="<?php echo $student->Foto->UploadMaxFileSize ?>">
</div>
<table id="ft_x<?php echo $student_list->RowIndex ?>_Foto" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<input type="hidden" data-table="student" data-field="x_Foto" name="o<?php echo $student_list->RowIndex ?>_Foto" id="o<?php echo $student_list->RowIndex ?>_Foto" value="<?php echo ew_HtmlEncode($student->Foto->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Foto" class="form-group student_Foto">
<div id="fd_x<?php echo $student_list->RowIndex ?>_Foto">
<span title="<?php echo $student->Foto->FldTitle() ? $student->Foto->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($student->Foto->ReadOnly || $student->Foto->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="student" data-field="x_Foto" name="x<?php echo $student_list->RowIndex ?>_Foto" id="x<?php echo $student_list->RowIndex ?>_Foto"<?php echo $student->Foto->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x<?php echo $student_list->RowIndex ?>_Foto" id= "fn_x<?php echo $student_list->RowIndex ?>_Foto" value="<?php echo $student->Foto->Upload->FileName ?>">
<?php if (@$_POST["fa_x<?php echo $student_list->RowIndex ?>_Foto"] == "0") { ?>
<input type="hidden" name="fa_x<?php echo $student_list->RowIndex ?>_Foto" id= "fa_x<?php echo $student_list->RowIndex ?>_Foto" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x<?php echo $student_list->RowIndex ?>_Foto" id= "fa_x<?php echo $student_list->RowIndex ?>_Foto" value="1">
<?php } ?>
<input type="hidden" name="fs_x<?php echo $student_list->RowIndex ?>_Foto" id= "fs_x<?php echo $student_list->RowIndex ?>_Foto" value="255">
<input type="hidden" name="fx_x<?php echo $student_list->RowIndex ?>_Foto" id= "fx_x<?php echo $student_list->RowIndex ?>_Foto" value="<?php echo $student->Foto->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x<?php echo $student_list->RowIndex ?>_Foto" id= "fm_x<?php echo $student_list->RowIndex ?>_Foto" value="<?php echo $student->Foto->UploadMaxFileSize ?>">
</div>
<table id="ft_x<?php echo $student_list->RowIndex ?>_Foto" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Foto" class="student_Foto">
<span>
<?php echo ew_GetFileViewTag($student->Foto, $student->Foto->ListViewValue()) ?>
</span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->NIK->Visible) { // NIK ?>
		<td data-name="NIK"<?php echo $student->NIK->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_NIK" class="form-group student_NIK">
<input type="text" data-table="student" data-field="x_NIK" name="x<?php echo $student_list->RowIndex ?>_NIK" id="x<?php echo $student_list->RowIndex ?>_NIK" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->NIK->getPlaceHolder()) ?>" value="<?php echo $student->NIK->EditValue ?>"<?php echo $student->NIK->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_NIK" name="o<?php echo $student_list->RowIndex ?>_NIK" id="o<?php echo $student_list->RowIndex ?>_NIK" value="<?php echo ew_HtmlEncode($student->NIK->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_NIK" class="form-group student_NIK">
<input type="text" data-table="student" data-field="x_NIK" name="x<?php echo $student_list->RowIndex ?>_NIK" id="x<?php echo $student_list->RowIndex ?>_NIK" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->NIK->getPlaceHolder()) ?>" value="<?php echo $student->NIK->EditValue ?>"<?php echo $student->NIK->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_NIK" class="student_NIK">
<span<?php echo $student->NIK->ViewAttributes() ?>>
<?php echo $student->NIK->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->Kelamin->Visible) { // Kelamin ?>
		<td data-name="Kelamin"<?php echo $student->Kelamin->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Kelamin" class="form-group student_Kelamin">
<div id="tp_x<?php echo $student_list->RowIndex ?>_Kelamin" class="ewTemplate"><input type="radio" data-table="student" data-field="x_Kelamin" data-value-separator="<?php echo $student->Kelamin->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $student_list->RowIndex ?>_Kelamin" id="x<?php echo $student_list->RowIndex ?>_Kelamin" value="{value}"<?php echo $student->Kelamin->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $student_list->RowIndex ?>_Kelamin" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->Kelamin->RadioButtonListHtml(FALSE, "x{$student_list->RowIndex}_Kelamin") ?>
</div></div>
<input type="hidden" name="s_x<?php echo $student_list->RowIndex ?>_Kelamin" id="s_x<?php echo $student_list->RowIndex ?>_Kelamin" value="<?php echo $student->Kelamin->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="student" data-field="x_Kelamin" name="o<?php echo $student_list->RowIndex ?>_Kelamin" id="o<?php echo $student_list->RowIndex ?>_Kelamin" value="<?php echo ew_HtmlEncode($student->Kelamin->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Kelamin" class="form-group student_Kelamin">
<div id="tp_x<?php echo $student_list->RowIndex ?>_Kelamin" class="ewTemplate"><input type="radio" data-table="student" data-field="x_Kelamin" data-value-separator="<?php echo $student->Kelamin->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $student_list->RowIndex ?>_Kelamin" id="x<?php echo $student_list->RowIndex ?>_Kelamin" value="{value}"<?php echo $student->Kelamin->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $student_list->RowIndex ?>_Kelamin" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->Kelamin->RadioButtonListHtml(FALSE, "x{$student_list->RowIndex}_Kelamin") ?>
</div></div>
<input type="hidden" name="s_x<?php echo $student_list->RowIndex ?>_Kelamin" id="s_x<?php echo $student_list->RowIndex ?>_Kelamin" value="<?php echo $student->Kelamin->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Kelamin" class="student_Kelamin">
<span<?php echo $student->Kelamin->ViewAttributes() ?>>
<?php echo $student->Kelamin->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->TempatLahir->Visible) { // TempatLahir ?>
		<td data-name="TempatLahir"<?php echo $student->TempatLahir->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_TempatLahir" class="form-group student_TempatLahir">
<input type="text" data-table="student" data-field="x_TempatLahir" name="x<?php echo $student_list->RowIndex ?>_TempatLahir" id="x<?php echo $student_list->RowIndex ?>_TempatLahir" size="30" maxlength="25" placeholder="<?php echo ew_HtmlEncode($student->TempatLahir->getPlaceHolder()) ?>" value="<?php echo $student->TempatLahir->EditValue ?>"<?php echo $student->TempatLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_TempatLahir" name="o<?php echo $student_list->RowIndex ?>_TempatLahir" id="o<?php echo $student_list->RowIndex ?>_TempatLahir" value="<?php echo ew_HtmlEncode($student->TempatLahir->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_TempatLahir" class="form-group student_TempatLahir">
<input type="text" data-table="student" data-field="x_TempatLahir" name="x<?php echo $student_list->RowIndex ?>_TempatLahir" id="x<?php echo $student_list->RowIndex ?>_TempatLahir" size="30" maxlength="25" placeholder="<?php echo ew_HtmlEncode($student->TempatLahir->getPlaceHolder()) ?>" value="<?php echo $student->TempatLahir->EditValue ?>"<?php echo $student->TempatLahir->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_TempatLahir" class="student_TempatLahir">
<span<?php echo $student->TempatLahir->ViewAttributes() ?>>
<?php echo $student->TempatLahir->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->TanggalLahir->Visible) { // TanggalLahir ?>
		<td data-name="TanggalLahir"<?php echo $student->TanggalLahir->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_TanggalLahir" class="form-group student_TanggalLahir">
<input type="text" data-table="student" data-field="x_TanggalLahir" name="x<?php echo $student_list->RowIndex ?>_TanggalLahir" id="x<?php echo $student_list->RowIndex ?>_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($student->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $student->TanggalLahir->EditValue ?>"<?php echo $student->TanggalLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_TanggalLahir" name="o<?php echo $student_list->RowIndex ?>_TanggalLahir" id="o<?php echo $student_list->RowIndex ?>_TanggalLahir" value="<?php echo ew_HtmlEncode($student->TanggalLahir->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_TanggalLahir" class="form-group student_TanggalLahir">
<input type="text" data-table="student" data-field="x_TanggalLahir" name="x<?php echo $student_list->RowIndex ?>_TanggalLahir" id="x<?php echo $student_list->RowIndex ?>_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($student->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $student->TanggalLahir->EditValue ?>"<?php echo $student->TanggalLahir->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_TanggalLahir" class="student_TanggalLahir">
<span<?php echo $student->TanggalLahir->ViewAttributes() ?>>
<?php echo $student->TanggalLahir->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->AlamatDomisili->Visible) { // AlamatDomisili ?>
		<td data-name="AlamatDomisili"<?php echo $student->AlamatDomisili->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_AlamatDomisili" class="form-group student_AlamatDomisili">
<textarea data-table="student" data-field="x_AlamatDomisili" name="x<?php echo $student_list->RowIndex ?>_AlamatDomisili" id="x<?php echo $student_list->RowIndex ?>_AlamatDomisili" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($student->AlamatDomisili->getPlaceHolder()) ?>"<?php echo $student->AlamatDomisili->EditAttributes() ?>><?php echo $student->AlamatDomisili->EditValue ?></textarea>
</span>
<input type="hidden" data-table="student" data-field="x_AlamatDomisili" name="o<?php echo $student_list->RowIndex ?>_AlamatDomisili" id="o<?php echo $student_list->RowIndex ?>_AlamatDomisili" value="<?php echo ew_HtmlEncode($student->AlamatDomisili->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_AlamatDomisili" class="form-group student_AlamatDomisili">
<textarea data-table="student" data-field="x_AlamatDomisili" name="x<?php echo $student_list->RowIndex ?>_AlamatDomisili" id="x<?php echo $student_list->RowIndex ?>_AlamatDomisili" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($student->AlamatDomisili->getPlaceHolder()) ?>"<?php echo $student->AlamatDomisili->EditAttributes() ?>><?php echo $student->AlamatDomisili->EditValue ?></textarea>
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_AlamatDomisili" class="student_AlamatDomisili">
<span<?php echo $student->AlamatDomisili->ViewAttributes() ?>>
<?php echo $student->AlamatDomisili->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->Telepon->Visible) { // Telepon ?>
		<td data-name="Telepon"<?php echo $student->Telepon->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Telepon" class="form-group student_Telepon">
<input type="text" data-table="student" data-field="x_Telepon" name="x<?php echo $student_list->RowIndex ?>_Telepon" id="x<?php echo $student_list->RowIndex ?>_Telepon" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Telepon->getPlaceHolder()) ?>" value="<?php echo $student->Telepon->EditValue ?>"<?php echo $student->Telepon->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_Telepon" name="o<?php echo $student_list->RowIndex ?>_Telepon" id="o<?php echo $student_list->RowIndex ?>_Telepon" value="<?php echo ew_HtmlEncode($student->Telepon->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Telepon" class="form-group student_Telepon">
<input type="text" data-table="student" data-field="x_Telepon" name="x<?php echo $student_list->RowIndex ?>_Telepon" id="x<?php echo $student_list->RowIndex ?>_Telepon" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Telepon->getPlaceHolder()) ?>" value="<?php echo $student->Telepon->EditValue ?>"<?php echo $student->Telepon->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_Telepon" class="student_Telepon">
<span<?php echo $student->Telepon->ViewAttributes() ?>>
<?php echo $student->Telepon->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->_Email->Visible) { // Email ?>
		<td data-name="_Email"<?php echo $student->_Email->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student__Email" class="form-group student__Email">
<input type="text" data-table="student" data-field="x__Email" name="x<?php echo $student_list->RowIndex ?>__Email" id="x<?php echo $student_list->RowIndex ?>__Email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->_Email->getPlaceHolder()) ?>" value="<?php echo $student->_Email->EditValue ?>"<?php echo $student->_Email->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x__Email" name="o<?php echo $student_list->RowIndex ?>__Email" id="o<?php echo $student_list->RowIndex ?>__Email" value="<?php echo ew_HtmlEncode($student->_Email->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student__Email" class="form-group student__Email">
<input type="text" data-table="student" data-field="x__Email" name="x<?php echo $student_list->RowIndex ?>__Email" id="x<?php echo $student_list->RowIndex ?>__Email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->_Email->getPlaceHolder()) ?>" value="<?php echo $student->_Email->EditValue ?>"<?php echo $student->_Email->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student__Email" class="student__Email">
<span<?php echo $student->_Email->ViewAttributes() ?>>
<?php echo $student->_Email->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($student->NA->Visible) { // NA ?>
		<td data-name="NA"<?php echo $student->NA->CellAttributes() ?>>
<?php if ($student->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_NA" class="form-group student_NA">
<div id="tp_x<?php echo $student_list->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="student" data-field="x_NA" data-value-separator="<?php echo $student->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $student_list->RowIndex ?>_NA" id="x<?php echo $student_list->RowIndex ?>_NA" value="{value}"<?php echo $student->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $student_list->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->NA->RadioButtonListHtml(FALSE, "x{$student_list->RowIndex}_NA") ?>
</div></div>
</span>
<input type="hidden" data-table="student" data-field="x_NA" name="o<?php echo $student_list->RowIndex ?>_NA" id="o<?php echo $student_list->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($student->NA->OldValue) ?>">
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_NA" class="form-group student_NA">
<div id="tp_x<?php echo $student_list->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="student" data-field="x_NA" data-value-separator="<?php echo $student->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $student_list->RowIndex ?>_NA" id="x<?php echo $student_list->RowIndex ?>_NA" value="{value}"<?php echo $student->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $student_list->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->NA->RadioButtonListHtml(FALSE, "x{$student_list->RowIndex}_NA") ?>
</div></div>
</span>
<?php } ?>
<?php if ($student->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $student_list->RowCnt ?>_student_NA" class="student_NA">
<span<?php echo $student->NA->ViewAttributes() ?>>
<?php echo $student->NA->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$student_list->ListOptions->Render("body", "right", $student_list->RowCnt);
?>
	</tr>
<?php if ($student->RowType == EW_ROWTYPE_ADD || $student->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fstudentlist.UpdateOpts(<?php echo $student_list->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($student->CurrentAction <> "gridadd")
		if (!$student_list->Recordset->EOF) $student_list->Recordset->MoveNext();
}
?>
<?php
	if ($student->CurrentAction == "gridadd" || $student->CurrentAction == "gridedit") {
		$student_list->RowIndex = '$rowindex$';
		$student_list->LoadDefaultValues();

		// Set row properties
		$student->ResetAttrs();
		$student->RowAttrs = array_merge($student->RowAttrs, array('data-rowindex'=>$student_list->RowIndex, 'id'=>'r0_student', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($student->RowAttrs["class"], "ewTemplate");
		$student->RowType = EW_ROWTYPE_ADD;

		// Render row
		$student_list->RenderRow();

		// Render list options
		$student_list->RenderListOptions();
		$student_list->StartRowCnt = 0;
?>
	<tr<?php echo $student->RowAttributes() ?>>
<?php

// Render list options (body, left)
$student_list->ListOptions->Render("body", "left", $student_list->RowIndex);
?>
	<?php if ($student->StudentID->Visible) { // StudentID ?>
		<td data-name="StudentID">
<span id="el$rowindex$_student_StudentID" class="form-group student_StudentID">
<input type="text" data-table="student" data-field="x_StudentID" name="x<?php echo $student_list->RowIndex ?>_StudentID" id="x<?php echo $student_list->RowIndex ?>_StudentID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($student->StudentID->getPlaceHolder()) ?>" value="<?php echo $student->StudentID->EditValue ?>"<?php echo $student->StudentID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_StudentID" name="o<?php echo $student_list->RowIndex ?>_StudentID" id="o<?php echo $student_list->RowIndex ?>_StudentID" value="<?php echo ew_HtmlEncode($student->StudentID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->Nama->Visible) { // Nama ?>
		<td data-name="Nama">
<span id="el$rowindex$_student_Nama" class="form-group student_Nama">
<input type="text" data-table="student" data-field="x_Nama" name="x<?php echo $student_list->RowIndex ?>_Nama" id="x<?php echo $student_list->RowIndex ?>_Nama" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Nama->getPlaceHolder()) ?>" value="<?php echo $student->Nama->EditValue ?>"<?php echo $student->Nama->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_Nama" name="o<?php echo $student_list->RowIndex ?>_Nama" id="o<?php echo $student_list->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($student->Nama->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->ProdiID->Visible) { // ProdiID ?>
		<td data-name="ProdiID">
<span id="el$rowindex$_student_ProdiID" class="form-group student_ProdiID">
<select data-table="student" data-field="x_ProdiID" data-value-separator="<?php echo $student->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $student_list->RowIndex ?>_ProdiID" name="x<?php echo $student_list->RowIndex ?>_ProdiID"<?php echo $student->ProdiID->EditAttributes() ?>>
<?php echo $student->ProdiID->SelectOptionListHtml("x<?php echo $student_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $student_list->RowIndex ?>_ProdiID" id="s_x<?php echo $student_list->RowIndex ?>_ProdiID" value="<?php echo $student->ProdiID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="student" data-field="x_ProdiID" name="o<?php echo $student_list->RowIndex ?>_ProdiID" id="o<?php echo $student_list->RowIndex ?>_ProdiID" value="<?php echo ew_HtmlEncode($student->ProdiID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->StudentStatusID->Visible) { // StudentStatusID ?>
		<td data-name="StudentStatusID">
<span id="el$rowindex$_student_StudentStatusID" class="form-group student_StudentStatusID">
<select data-table="student" data-field="x_StudentStatusID" data-value-separator="<?php echo $student->StudentStatusID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $student_list->RowIndex ?>_StudentStatusID" name="x<?php echo $student_list->RowIndex ?>_StudentStatusID"<?php echo $student->StudentStatusID->EditAttributes() ?>>
<?php echo $student->StudentStatusID->SelectOptionListHtml("x<?php echo $student_list->RowIndex ?>_StudentStatusID") ?>
</select>
<input type="hidden" name="s_x<?php echo $student_list->RowIndex ?>_StudentStatusID" id="s_x<?php echo $student_list->RowIndex ?>_StudentStatusID" value="<?php echo $student->StudentStatusID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="student" data-field="x_StudentStatusID" name="o<?php echo $student_list->RowIndex ?>_StudentStatusID" id="o<?php echo $student_list->RowIndex ?>_StudentStatusID" value="<?php echo ew_HtmlEncode($student->StudentStatusID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->TahunID->Visible) { // TahunID ?>
		<td data-name="TahunID">
<span id="el$rowindex$_student_TahunID" class="form-group student_TahunID">
<input type="text" data-table="student" data-field="x_TahunID" name="x<?php echo $student_list->RowIndex ?>_TahunID" id="x<?php echo $student_list->RowIndex ?>_TahunID" size="30" placeholder="<?php echo ew_HtmlEncode($student->TahunID->getPlaceHolder()) ?>" value="<?php echo $student->TahunID->EditValue ?>"<?php echo $student->TahunID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_TahunID" name="o<?php echo $student_list->RowIndex ?>_TahunID" id="o<?php echo $student_list->RowIndex ?>_TahunID" value="<?php echo ew_HtmlEncode($student->TahunID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->Foto->Visible) { // Foto ?>
		<td data-name="Foto">
<span id="el$rowindex$_student_Foto" class="form-group student_Foto">
<div id="fd_x<?php echo $student_list->RowIndex ?>_Foto">
<span title="<?php echo $student->Foto->FldTitle() ? $student->Foto->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($student->Foto->ReadOnly || $student->Foto->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="student" data-field="x_Foto" name="x<?php echo $student_list->RowIndex ?>_Foto" id="x<?php echo $student_list->RowIndex ?>_Foto"<?php echo $student->Foto->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x<?php echo $student_list->RowIndex ?>_Foto" id= "fn_x<?php echo $student_list->RowIndex ?>_Foto" value="<?php echo $student->Foto->Upload->FileName ?>">
<input type="hidden" name="fa_x<?php echo $student_list->RowIndex ?>_Foto" id= "fa_x<?php echo $student_list->RowIndex ?>_Foto" value="0">
<input type="hidden" name="fs_x<?php echo $student_list->RowIndex ?>_Foto" id= "fs_x<?php echo $student_list->RowIndex ?>_Foto" value="255">
<input type="hidden" name="fx_x<?php echo $student_list->RowIndex ?>_Foto" id= "fx_x<?php echo $student_list->RowIndex ?>_Foto" value="<?php echo $student->Foto->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x<?php echo $student_list->RowIndex ?>_Foto" id= "fm_x<?php echo $student_list->RowIndex ?>_Foto" value="<?php echo $student->Foto->UploadMaxFileSize ?>">
</div>
<table id="ft_x<?php echo $student_list->RowIndex ?>_Foto" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<input type="hidden" data-table="student" data-field="x_Foto" name="o<?php echo $student_list->RowIndex ?>_Foto" id="o<?php echo $student_list->RowIndex ?>_Foto" value="<?php echo ew_HtmlEncode($student->Foto->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->NIK->Visible) { // NIK ?>
		<td data-name="NIK">
<span id="el$rowindex$_student_NIK" class="form-group student_NIK">
<input type="text" data-table="student" data-field="x_NIK" name="x<?php echo $student_list->RowIndex ?>_NIK" id="x<?php echo $student_list->RowIndex ?>_NIK" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->NIK->getPlaceHolder()) ?>" value="<?php echo $student->NIK->EditValue ?>"<?php echo $student->NIK->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_NIK" name="o<?php echo $student_list->RowIndex ?>_NIK" id="o<?php echo $student_list->RowIndex ?>_NIK" value="<?php echo ew_HtmlEncode($student->NIK->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->Kelamin->Visible) { // Kelamin ?>
		<td data-name="Kelamin">
<span id="el$rowindex$_student_Kelamin" class="form-group student_Kelamin">
<div id="tp_x<?php echo $student_list->RowIndex ?>_Kelamin" class="ewTemplate"><input type="radio" data-table="student" data-field="x_Kelamin" data-value-separator="<?php echo $student->Kelamin->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $student_list->RowIndex ?>_Kelamin" id="x<?php echo $student_list->RowIndex ?>_Kelamin" value="{value}"<?php echo $student->Kelamin->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $student_list->RowIndex ?>_Kelamin" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->Kelamin->RadioButtonListHtml(FALSE, "x{$student_list->RowIndex}_Kelamin") ?>
</div></div>
<input type="hidden" name="s_x<?php echo $student_list->RowIndex ?>_Kelamin" id="s_x<?php echo $student_list->RowIndex ?>_Kelamin" value="<?php echo $student->Kelamin->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="student" data-field="x_Kelamin" name="o<?php echo $student_list->RowIndex ?>_Kelamin" id="o<?php echo $student_list->RowIndex ?>_Kelamin" value="<?php echo ew_HtmlEncode($student->Kelamin->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->TempatLahir->Visible) { // TempatLahir ?>
		<td data-name="TempatLahir">
<span id="el$rowindex$_student_TempatLahir" class="form-group student_TempatLahir">
<input type="text" data-table="student" data-field="x_TempatLahir" name="x<?php echo $student_list->RowIndex ?>_TempatLahir" id="x<?php echo $student_list->RowIndex ?>_TempatLahir" size="30" maxlength="25" placeholder="<?php echo ew_HtmlEncode($student->TempatLahir->getPlaceHolder()) ?>" value="<?php echo $student->TempatLahir->EditValue ?>"<?php echo $student->TempatLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_TempatLahir" name="o<?php echo $student_list->RowIndex ?>_TempatLahir" id="o<?php echo $student_list->RowIndex ?>_TempatLahir" value="<?php echo ew_HtmlEncode($student->TempatLahir->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->TanggalLahir->Visible) { // TanggalLahir ?>
		<td data-name="TanggalLahir">
<span id="el$rowindex$_student_TanggalLahir" class="form-group student_TanggalLahir">
<input type="text" data-table="student" data-field="x_TanggalLahir" name="x<?php echo $student_list->RowIndex ?>_TanggalLahir" id="x<?php echo $student_list->RowIndex ?>_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($student->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $student->TanggalLahir->EditValue ?>"<?php echo $student->TanggalLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_TanggalLahir" name="o<?php echo $student_list->RowIndex ?>_TanggalLahir" id="o<?php echo $student_list->RowIndex ?>_TanggalLahir" value="<?php echo ew_HtmlEncode($student->TanggalLahir->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->AlamatDomisili->Visible) { // AlamatDomisili ?>
		<td data-name="AlamatDomisili">
<span id="el$rowindex$_student_AlamatDomisili" class="form-group student_AlamatDomisili">
<textarea data-table="student" data-field="x_AlamatDomisili" name="x<?php echo $student_list->RowIndex ?>_AlamatDomisili" id="x<?php echo $student_list->RowIndex ?>_AlamatDomisili" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($student->AlamatDomisili->getPlaceHolder()) ?>"<?php echo $student->AlamatDomisili->EditAttributes() ?>><?php echo $student->AlamatDomisili->EditValue ?></textarea>
</span>
<input type="hidden" data-table="student" data-field="x_AlamatDomisili" name="o<?php echo $student_list->RowIndex ?>_AlamatDomisili" id="o<?php echo $student_list->RowIndex ?>_AlamatDomisili" value="<?php echo ew_HtmlEncode($student->AlamatDomisili->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->Telepon->Visible) { // Telepon ?>
		<td data-name="Telepon">
<span id="el$rowindex$_student_Telepon" class="form-group student_Telepon">
<input type="text" data-table="student" data-field="x_Telepon" name="x<?php echo $student_list->RowIndex ?>_Telepon" id="x<?php echo $student_list->RowIndex ?>_Telepon" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Telepon->getPlaceHolder()) ?>" value="<?php echo $student->Telepon->EditValue ?>"<?php echo $student->Telepon->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x_Telepon" name="o<?php echo $student_list->RowIndex ?>_Telepon" id="o<?php echo $student_list->RowIndex ?>_Telepon" value="<?php echo ew_HtmlEncode($student->Telepon->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->_Email->Visible) { // Email ?>
		<td data-name="_Email">
<span id="el$rowindex$_student__Email" class="form-group student__Email">
<input type="text" data-table="student" data-field="x__Email" name="x<?php echo $student_list->RowIndex ?>__Email" id="x<?php echo $student_list->RowIndex ?>__Email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->_Email->getPlaceHolder()) ?>" value="<?php echo $student->_Email->EditValue ?>"<?php echo $student->_Email->EditAttributes() ?>>
</span>
<input type="hidden" data-table="student" data-field="x__Email" name="o<?php echo $student_list->RowIndex ?>__Email" id="o<?php echo $student_list->RowIndex ?>__Email" value="<?php echo ew_HtmlEncode($student->_Email->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($student->NA->Visible) { // NA ?>
		<td data-name="NA">
<span id="el$rowindex$_student_NA" class="form-group student_NA">
<div id="tp_x<?php echo $student_list->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="student" data-field="x_NA" data-value-separator="<?php echo $student->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $student_list->RowIndex ?>_NA" id="x<?php echo $student_list->RowIndex ?>_NA" value="{value}"<?php echo $student->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $student_list->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->NA->RadioButtonListHtml(FALSE, "x{$student_list->RowIndex}_NA") ?>
</div></div>
</span>
<input type="hidden" data-table="student" data-field="x_NA" name="o<?php echo $student_list->RowIndex ?>_NA" id="o<?php echo $student_list->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($student->NA->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$student_list->ListOptions->Render("body", "right", $student_list->RowCnt);
?>
<script type="text/javascript">
fstudentlist.UpdateOpts(<?php echo $student_list->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($student->CurrentAction == "gridadd") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $student_list->FormKeyCountName ?>" id="<?php echo $student_list->FormKeyCountName ?>" value="<?php echo $student_list->KeyCount ?>">
<?php echo $student_list->MultiSelectKey ?>
<?php } ?>
<?php if ($student->CurrentAction == "gridedit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $student_list->FormKeyCountName ?>" id="<?php echo $student_list->FormKeyCountName ?>" value="<?php echo $student_list->KeyCount ?>">
<?php echo $student_list->MultiSelectKey ?>
<?php } ?>
<?php if ($student->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($student_list->Recordset)
	$student_list->Recordset->Close();
?>
<?php if ($student->Export == "") { ?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($student->CurrentAction <> "gridadd" && $student->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($student_list->Pager)) $student_list->Pager = new cPrevNextPager($student_list->StartRec, $student_list->DisplayRecs, $student_list->TotalRecs) ?>
<?php if ($student_list->Pager->RecordCount > 0 && $student_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($student_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $student_list->PageUrl() ?>start=<?php echo $student_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($student_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $student_list->PageUrl() ?>start=<?php echo $student_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $student_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($student_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $student_list->PageUrl() ?>start=<?php echo $student_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($student_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $student_list->PageUrl() ?>start=<?php echo $student_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $student_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $student_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $student_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $student_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($student_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($student_list->TotalRecs == 0 && $student->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($student_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($student->Export == "") { ?>
<script type="text/javascript">
fstudentlistsrch.FilterList = <?php echo $student_list->GetFilterList() ?>;
fstudentlistsrch.Init();
fstudentlist.Init();
</script>
<?php } ?>
<?php
$student_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($student->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$student_list->Page_Terminate();
?>
