<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_statusawalinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_statusawal_list = NULL; // Initialize page object first

class cmaster_statusawal_list extends cmaster_statusawal {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_statusawal';

	// Page object name
	var $PageObjName = 'master_statusawal_list';

	// Grid form hidden field names
	var $FormName = 'fmaster_statusawallist';
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

		// Table object (master_statusawal)
		if (!isset($GLOBALS["master_statusawal"]) || get_class($GLOBALS["master_statusawal"]) == "cmaster_statusawal") {
			$GLOBALS["master_statusawal"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_statusawal"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "master_statusawaladd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "master_statusawaldelete.php";
		$this->MultiUpdateUrl = "master_statusawalupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_statusawal', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fmaster_statusawallistsrch";

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
		$this->Urutan->SetVisibility();
		$this->StatusAwalID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->BeliOnline->SetVisibility();
		$this->BeliFormulir->SetVisibility();
		$this->JalurKhusus->SetVisibility();
		$this->TanpaTest->SetVisibility();
		$this->Catatan->SetVisibility();
		$this->NA->SetVisibility();
		$this->PotonganSPI_Prosen->SetVisibility();
		$this->PotonganSPI_Nominal->SetVisibility();
		$this->PotonganSPP_Prosen->SetVisibility();
		$this->PotonganSPP_Nominal->SetVisibility();

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
		global $EW_EXPORT, $master_statusawal;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_statusawal);
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
			$this->StatusAwalID->setFormValue($arrKeyFlds[0]);
		}
		return TRUE;
	}

	// Get list of filters
	function GetFilterList() {
		global $UserProfile;

		// Load server side filters
		if (EW_SEARCH_FILTER_OPTION == "Server") {
			$sSavedFilterList = isset($UserProfile) ? $UserProfile->GetSearchFilters(CurrentUserName(), "fmaster_statusawallistsrch") : "";
		} else {
			$sSavedFilterList = "";
		}

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->Urutan->AdvancedSearch->ToJSON(), ","); // Field Urutan
		$sFilterList = ew_Concat($sFilterList, $this->StatusAwalID->AdvancedSearch->ToJSON(), ","); // Field StatusAwalID
		$sFilterList = ew_Concat($sFilterList, $this->Nama->AdvancedSearch->ToJSON(), ","); // Field Nama
		$sFilterList = ew_Concat($sFilterList, $this->BeliOnline->AdvancedSearch->ToJSON(), ","); // Field BeliOnline
		$sFilterList = ew_Concat($sFilterList, $this->BeliFormulir->AdvancedSearch->ToJSON(), ","); // Field BeliFormulir
		$sFilterList = ew_Concat($sFilterList, $this->JalurKhusus->AdvancedSearch->ToJSON(), ","); // Field JalurKhusus
		$sFilterList = ew_Concat($sFilterList, $this->TanpaTest->AdvancedSearch->ToJSON(), ","); // Field TanpaTest
		$sFilterList = ew_Concat($sFilterList, $this->Catatan->AdvancedSearch->ToJSON(), ","); // Field Catatan
		$sFilterList = ew_Concat($sFilterList, $this->NA->AdvancedSearch->ToJSON(), ","); // Field NA
		$sFilterList = ew_Concat($sFilterList, $this->PotonganSPI_Prosen->AdvancedSearch->ToJSON(), ","); // Field PotonganSPI_Prosen
		$sFilterList = ew_Concat($sFilterList, $this->PotonganSPI_Nominal->AdvancedSearch->ToJSON(), ","); // Field PotonganSPI_Nominal
		$sFilterList = ew_Concat($sFilterList, $this->PotonganSPP_Prosen->AdvancedSearch->ToJSON(), ","); // Field PotonganSPP_Prosen
		$sFilterList = ew_Concat($sFilterList, $this->PotonganSPP_Nominal->AdvancedSearch->ToJSON(), ","); // Field PotonganSPP_Nominal
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
			$UserProfile->SetSearchFilters(CurrentUserName(), "fmaster_statusawallistsrch", $filters);

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

		// Field Urutan
		$this->Urutan->AdvancedSearch->SearchValue = @$filter["x_Urutan"];
		$this->Urutan->AdvancedSearch->SearchOperator = @$filter["z_Urutan"];
		$this->Urutan->AdvancedSearch->SearchCondition = @$filter["v_Urutan"];
		$this->Urutan->AdvancedSearch->SearchValue2 = @$filter["y_Urutan"];
		$this->Urutan->AdvancedSearch->SearchOperator2 = @$filter["w_Urutan"];
		$this->Urutan->AdvancedSearch->Save();

		// Field StatusAwalID
		$this->StatusAwalID->AdvancedSearch->SearchValue = @$filter["x_StatusAwalID"];
		$this->StatusAwalID->AdvancedSearch->SearchOperator = @$filter["z_StatusAwalID"];
		$this->StatusAwalID->AdvancedSearch->SearchCondition = @$filter["v_StatusAwalID"];
		$this->StatusAwalID->AdvancedSearch->SearchValue2 = @$filter["y_StatusAwalID"];
		$this->StatusAwalID->AdvancedSearch->SearchOperator2 = @$filter["w_StatusAwalID"];
		$this->StatusAwalID->AdvancedSearch->Save();

		// Field Nama
		$this->Nama->AdvancedSearch->SearchValue = @$filter["x_Nama"];
		$this->Nama->AdvancedSearch->SearchOperator = @$filter["z_Nama"];
		$this->Nama->AdvancedSearch->SearchCondition = @$filter["v_Nama"];
		$this->Nama->AdvancedSearch->SearchValue2 = @$filter["y_Nama"];
		$this->Nama->AdvancedSearch->SearchOperator2 = @$filter["w_Nama"];
		$this->Nama->AdvancedSearch->Save();

		// Field BeliOnline
		$this->BeliOnline->AdvancedSearch->SearchValue = @$filter["x_BeliOnline"];
		$this->BeliOnline->AdvancedSearch->SearchOperator = @$filter["z_BeliOnline"];
		$this->BeliOnline->AdvancedSearch->SearchCondition = @$filter["v_BeliOnline"];
		$this->BeliOnline->AdvancedSearch->SearchValue2 = @$filter["y_BeliOnline"];
		$this->BeliOnline->AdvancedSearch->SearchOperator2 = @$filter["w_BeliOnline"];
		$this->BeliOnline->AdvancedSearch->Save();

		// Field BeliFormulir
		$this->BeliFormulir->AdvancedSearch->SearchValue = @$filter["x_BeliFormulir"];
		$this->BeliFormulir->AdvancedSearch->SearchOperator = @$filter["z_BeliFormulir"];
		$this->BeliFormulir->AdvancedSearch->SearchCondition = @$filter["v_BeliFormulir"];
		$this->BeliFormulir->AdvancedSearch->SearchValue2 = @$filter["y_BeliFormulir"];
		$this->BeliFormulir->AdvancedSearch->SearchOperator2 = @$filter["w_BeliFormulir"];
		$this->BeliFormulir->AdvancedSearch->Save();

		// Field JalurKhusus
		$this->JalurKhusus->AdvancedSearch->SearchValue = @$filter["x_JalurKhusus"];
		$this->JalurKhusus->AdvancedSearch->SearchOperator = @$filter["z_JalurKhusus"];
		$this->JalurKhusus->AdvancedSearch->SearchCondition = @$filter["v_JalurKhusus"];
		$this->JalurKhusus->AdvancedSearch->SearchValue2 = @$filter["y_JalurKhusus"];
		$this->JalurKhusus->AdvancedSearch->SearchOperator2 = @$filter["w_JalurKhusus"];
		$this->JalurKhusus->AdvancedSearch->Save();

		// Field TanpaTest
		$this->TanpaTest->AdvancedSearch->SearchValue = @$filter["x_TanpaTest"];
		$this->TanpaTest->AdvancedSearch->SearchOperator = @$filter["z_TanpaTest"];
		$this->TanpaTest->AdvancedSearch->SearchCondition = @$filter["v_TanpaTest"];
		$this->TanpaTest->AdvancedSearch->SearchValue2 = @$filter["y_TanpaTest"];
		$this->TanpaTest->AdvancedSearch->SearchOperator2 = @$filter["w_TanpaTest"];
		$this->TanpaTest->AdvancedSearch->Save();

		// Field Catatan
		$this->Catatan->AdvancedSearch->SearchValue = @$filter["x_Catatan"];
		$this->Catatan->AdvancedSearch->SearchOperator = @$filter["z_Catatan"];
		$this->Catatan->AdvancedSearch->SearchCondition = @$filter["v_Catatan"];
		$this->Catatan->AdvancedSearch->SearchValue2 = @$filter["y_Catatan"];
		$this->Catatan->AdvancedSearch->SearchOperator2 = @$filter["w_Catatan"];
		$this->Catatan->AdvancedSearch->Save();

		// Field NA
		$this->NA->AdvancedSearch->SearchValue = @$filter["x_NA"];
		$this->NA->AdvancedSearch->SearchOperator = @$filter["z_NA"];
		$this->NA->AdvancedSearch->SearchCondition = @$filter["v_NA"];
		$this->NA->AdvancedSearch->SearchValue2 = @$filter["y_NA"];
		$this->NA->AdvancedSearch->SearchOperator2 = @$filter["w_NA"];
		$this->NA->AdvancedSearch->Save();

		// Field PotonganSPI_Prosen
		$this->PotonganSPI_Prosen->AdvancedSearch->SearchValue = @$filter["x_PotonganSPI_Prosen"];
		$this->PotonganSPI_Prosen->AdvancedSearch->SearchOperator = @$filter["z_PotonganSPI_Prosen"];
		$this->PotonganSPI_Prosen->AdvancedSearch->SearchCondition = @$filter["v_PotonganSPI_Prosen"];
		$this->PotonganSPI_Prosen->AdvancedSearch->SearchValue2 = @$filter["y_PotonganSPI_Prosen"];
		$this->PotonganSPI_Prosen->AdvancedSearch->SearchOperator2 = @$filter["w_PotonganSPI_Prosen"];
		$this->PotonganSPI_Prosen->AdvancedSearch->Save();

		// Field PotonganSPI_Nominal
		$this->PotonganSPI_Nominal->AdvancedSearch->SearchValue = @$filter["x_PotonganSPI_Nominal"];
		$this->PotonganSPI_Nominal->AdvancedSearch->SearchOperator = @$filter["z_PotonganSPI_Nominal"];
		$this->PotonganSPI_Nominal->AdvancedSearch->SearchCondition = @$filter["v_PotonganSPI_Nominal"];
		$this->PotonganSPI_Nominal->AdvancedSearch->SearchValue2 = @$filter["y_PotonganSPI_Nominal"];
		$this->PotonganSPI_Nominal->AdvancedSearch->SearchOperator2 = @$filter["w_PotonganSPI_Nominal"];
		$this->PotonganSPI_Nominal->AdvancedSearch->Save();

		// Field PotonganSPP_Prosen
		$this->PotonganSPP_Prosen->AdvancedSearch->SearchValue = @$filter["x_PotonganSPP_Prosen"];
		$this->PotonganSPP_Prosen->AdvancedSearch->SearchOperator = @$filter["z_PotonganSPP_Prosen"];
		$this->PotonganSPP_Prosen->AdvancedSearch->SearchCondition = @$filter["v_PotonganSPP_Prosen"];
		$this->PotonganSPP_Prosen->AdvancedSearch->SearchValue2 = @$filter["y_PotonganSPP_Prosen"];
		$this->PotonganSPP_Prosen->AdvancedSearch->SearchOperator2 = @$filter["w_PotonganSPP_Prosen"];
		$this->PotonganSPP_Prosen->AdvancedSearch->Save();

		// Field PotonganSPP_Nominal
		$this->PotonganSPP_Nominal->AdvancedSearch->SearchValue = @$filter["x_PotonganSPP_Nominal"];
		$this->PotonganSPP_Nominal->AdvancedSearch->SearchOperator = @$filter["z_PotonganSPP_Nominal"];
		$this->PotonganSPP_Nominal->AdvancedSearch->SearchCondition = @$filter["v_PotonganSPP_Nominal"];
		$this->PotonganSPP_Nominal->AdvancedSearch->SearchValue2 = @$filter["y_PotonganSPP_Nominal"];
		$this->PotonganSPP_Nominal->AdvancedSearch->SearchOperator2 = @$filter["w_PotonganSPP_Nominal"];
		$this->PotonganSPP_Nominal->AdvancedSearch->Save();
		$this->BasicSearch->setKeyword(@$filter[EW_TABLE_BASIC_SEARCH]);
		$this->BasicSearch->setType(@$filter[EW_TABLE_BASIC_SEARCH_TYPE]);
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere($Default = FALSE) {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->Urutan, $Default, FALSE); // Urutan
		$this->BuildSearchSql($sWhere, $this->StatusAwalID, $Default, FALSE); // StatusAwalID
		$this->BuildSearchSql($sWhere, $this->Nama, $Default, FALSE); // Nama
		$this->BuildSearchSql($sWhere, $this->BeliOnline, $Default, FALSE); // BeliOnline
		$this->BuildSearchSql($sWhere, $this->BeliFormulir, $Default, FALSE); // BeliFormulir
		$this->BuildSearchSql($sWhere, $this->JalurKhusus, $Default, FALSE); // JalurKhusus
		$this->BuildSearchSql($sWhere, $this->TanpaTest, $Default, FALSE); // TanpaTest
		$this->BuildSearchSql($sWhere, $this->Catatan, $Default, FALSE); // Catatan
		$this->BuildSearchSql($sWhere, $this->NA, $Default, FALSE); // NA
		$this->BuildSearchSql($sWhere, $this->PotonganSPI_Prosen, $Default, FALSE); // PotonganSPI_Prosen
		$this->BuildSearchSql($sWhere, $this->PotonganSPI_Nominal, $Default, FALSE); // PotonganSPI_Nominal
		$this->BuildSearchSql($sWhere, $this->PotonganSPP_Prosen, $Default, FALSE); // PotonganSPP_Prosen
		$this->BuildSearchSql($sWhere, $this->PotonganSPP_Nominal, $Default, FALSE); // PotonganSPP_Nominal

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->Urutan->AdvancedSearch->Save(); // Urutan
			$this->StatusAwalID->AdvancedSearch->Save(); // StatusAwalID
			$this->Nama->AdvancedSearch->Save(); // Nama
			$this->BeliOnline->AdvancedSearch->Save(); // BeliOnline
			$this->BeliFormulir->AdvancedSearch->Save(); // BeliFormulir
			$this->JalurKhusus->AdvancedSearch->Save(); // JalurKhusus
			$this->TanpaTest->AdvancedSearch->Save(); // TanpaTest
			$this->Catatan->AdvancedSearch->Save(); // Catatan
			$this->NA->AdvancedSearch->Save(); // NA
			$this->PotonganSPI_Prosen->AdvancedSearch->Save(); // PotonganSPI_Prosen
			$this->PotonganSPI_Nominal->AdvancedSearch->Save(); // PotonganSPI_Nominal
			$this->PotonganSPP_Prosen->AdvancedSearch->Save(); // PotonganSPP_Prosen
			$this->PotonganSPP_Nominal->AdvancedSearch->Save(); // PotonganSPP_Nominal
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
		$this->BuildBasicSearchSQL($sWhere, $this->Urutan, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->StatusAwalID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Nama, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Catatan, $arKeywords, $type);
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
		if ($this->Urutan->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->StatusAwalID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Nama->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->BeliOnline->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->BeliFormulir->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->JalurKhusus->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TanpaTest->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Catatan->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NA->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PotonganSPI_Prosen->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PotonganSPI_Nominal->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PotonganSPP_Prosen->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PotonganSPP_Nominal->AdvancedSearch->IssetSession())
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
		$this->Urutan->AdvancedSearch->UnsetSession();
		$this->StatusAwalID->AdvancedSearch->UnsetSession();
		$this->Nama->AdvancedSearch->UnsetSession();
		$this->BeliOnline->AdvancedSearch->UnsetSession();
		$this->BeliFormulir->AdvancedSearch->UnsetSession();
		$this->JalurKhusus->AdvancedSearch->UnsetSession();
		$this->TanpaTest->AdvancedSearch->UnsetSession();
		$this->Catatan->AdvancedSearch->UnsetSession();
		$this->NA->AdvancedSearch->UnsetSession();
		$this->PotonganSPI_Prosen->AdvancedSearch->UnsetSession();
		$this->PotonganSPI_Nominal->AdvancedSearch->UnsetSession();
		$this->PotonganSPP_Prosen->AdvancedSearch->UnsetSession();
		$this->PotonganSPP_Nominal->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->Urutan->AdvancedSearch->Load();
		$this->StatusAwalID->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->BeliOnline->AdvancedSearch->Load();
		$this->BeliFormulir->AdvancedSearch->Load();
		$this->JalurKhusus->AdvancedSearch->Load();
		$this->TanpaTest->AdvancedSearch->Load();
		$this->Catatan->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
		$this->PotonganSPI_Prosen->AdvancedSearch->Load();
		$this->PotonganSPI_Nominal->AdvancedSearch->Load();
		$this->PotonganSPP_Prosen->AdvancedSearch->Load();
		$this->PotonganSPP_Nominal->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->Urutan); // Urutan
			$this->UpdateSort($this->StatusAwalID); // StatusAwalID
			$this->UpdateSort($this->Nama); // Nama
			$this->UpdateSort($this->BeliOnline); // BeliOnline
			$this->UpdateSort($this->BeliFormulir); // BeliFormulir
			$this->UpdateSort($this->JalurKhusus); // JalurKhusus
			$this->UpdateSort($this->TanpaTest); // TanpaTest
			$this->UpdateSort($this->Catatan); // Catatan
			$this->UpdateSort($this->NA); // NA
			$this->UpdateSort($this->PotonganSPI_Prosen); // PotonganSPI_Prosen
			$this->UpdateSort($this->PotonganSPI_Nominal); // PotonganSPI_Nominal
			$this->UpdateSort($this->PotonganSPP_Prosen); // PotonganSPP_Prosen
			$this->UpdateSort($this->PotonganSPP_Nominal); // PotonganSPP_Nominal
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
				$this->Urutan->setSort("");
				$this->StatusAwalID->setSort("");
				$this->Nama->setSort("");
				$this->BeliOnline->setSort("");
				$this->BeliFormulir->setSort("");
				$this->JalurKhusus->setSort("");
				$this->TanpaTest->setSort("");
				$this->Catatan->setSort("");
				$this->NA->setSort("");
				$this->PotonganSPI_Prosen->setSort("");
				$this->PotonganSPI_Nominal->setSort("");
				$this->PotonganSPP_Prosen->setSort("");
				$this->PotonganSPP_Nominal->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

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
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->StatusAwalID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event);'>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fmaster_statusawallistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fmaster_statusawallistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
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
			$option = &$options["action"];

			// Set up list action buttons
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_MULTIPLE) {
					$item = &$option->Add("custom_" . $listaction->Action);
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode($listaction->Icon) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\"></span> " : $caption;
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fmaster_statusawallist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fmaster_statusawallistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		if (ew_IsMobile())
			$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"master_statusawalsrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		else
			$item->Body = "<button type=\"button\" class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-table=\"master_statusawal\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" onclick=\"ew_ModalDialogShow({lnk:this,url:'master_statusawalsrch.php',caption:'" . $Language->Phrase("Search") . "'});\">" . $Language->Phrase("AdvancedSearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Search highlight button
		$item = &$this->SearchOptions->Add("searchhighlight");
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewHighlight active\" title=\"" . $Language->Phrase("Highlight") . "\" data-caption=\"" . $Language->Phrase("Highlight") . "\" data-toggle=\"button\" data-form=\"fmaster_statusawallistsrch\" data-name=\"" . $this->HighlightName() . "\">" . $Language->Phrase("HighlightBtn") . "</button>";
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
		// Urutan

		$this->Urutan->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Urutan"]);
		if ($this->Urutan->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Urutan->AdvancedSearch->SearchOperator = @$_GET["z_Urutan"];

		// StatusAwalID
		$this->StatusAwalID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_StatusAwalID"]);
		if ($this->StatusAwalID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->StatusAwalID->AdvancedSearch->SearchOperator = @$_GET["z_StatusAwalID"];

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Nama"]);
		if ($this->Nama->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Nama->AdvancedSearch->SearchOperator = @$_GET["z_Nama"];

		// BeliOnline
		$this->BeliOnline->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_BeliOnline"]);
		if ($this->BeliOnline->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->BeliOnline->AdvancedSearch->SearchOperator = @$_GET["z_BeliOnline"];

		// BeliFormulir
		$this->BeliFormulir->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_BeliFormulir"]);
		if ($this->BeliFormulir->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->BeliFormulir->AdvancedSearch->SearchOperator = @$_GET["z_BeliFormulir"];

		// JalurKhusus
		$this->JalurKhusus->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_JalurKhusus"]);
		if ($this->JalurKhusus->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->JalurKhusus->AdvancedSearch->SearchOperator = @$_GET["z_JalurKhusus"];

		// TanpaTest
		$this->TanpaTest->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TanpaTest"]);
		if ($this->TanpaTest->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TanpaTest->AdvancedSearch->SearchOperator = @$_GET["z_TanpaTest"];

		// Catatan
		$this->Catatan->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Catatan"]);
		if ($this->Catatan->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Catatan->AdvancedSearch->SearchOperator = @$_GET["z_Catatan"];

		// NA
		$this->NA->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NA"]);
		if ($this->NA->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NA->AdvancedSearch->SearchOperator = @$_GET["z_NA"];

		// PotonganSPI_Prosen
		$this->PotonganSPI_Prosen->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PotonganSPI_Prosen"]);
		if ($this->PotonganSPI_Prosen->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PotonganSPI_Prosen->AdvancedSearch->SearchOperator = @$_GET["z_PotonganSPI_Prosen"];

		// PotonganSPI_Nominal
		$this->PotonganSPI_Nominal->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PotonganSPI_Nominal"]);
		if ($this->PotonganSPI_Nominal->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PotonganSPI_Nominal->AdvancedSearch->SearchOperator = @$_GET["z_PotonganSPI_Nominal"];

		// PotonganSPP_Prosen
		$this->PotonganSPP_Prosen->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PotonganSPP_Prosen"]);
		if ($this->PotonganSPP_Prosen->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PotonganSPP_Prosen->AdvancedSearch->SearchOperator = @$_GET["z_PotonganSPP_Prosen"];

		// PotonganSPP_Nominal
		$this->PotonganSPP_Nominal->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PotonganSPP_Nominal"]);
		if ($this->PotonganSPP_Nominal->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PotonganSPP_Nominal->AdvancedSearch->SearchOperator = @$_GET["z_PotonganSPP_Nominal"];
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
		$this->Urutan->setDbValue($rs->fields('Urutan'));
		$this->StatusAwalID->setDbValue($rs->fields('StatusAwalID'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->BeliOnline->setDbValue($rs->fields('BeliOnline'));
		$this->BeliFormulir->setDbValue($rs->fields('BeliFormulir'));
		$this->JalurKhusus->setDbValue($rs->fields('JalurKhusus'));
		$this->TanpaTest->setDbValue($rs->fields('TanpaTest'));
		$this->Catatan->setDbValue($rs->fields('Catatan'));
		$this->NA->setDbValue($rs->fields('NA'));
		$this->PotonganSPI_Prosen->setDbValue($rs->fields('PotonganSPI_Prosen'));
		$this->PotonganSPI_Nominal->setDbValue($rs->fields('PotonganSPI_Nominal'));
		$this->PotonganSPP_Prosen->setDbValue($rs->fields('PotonganSPP_Prosen'));
		$this->PotonganSPP_Nominal->setDbValue($rs->fields('PotonganSPP_Nominal'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Urutan->DbValue = $row['Urutan'];
		$this->StatusAwalID->DbValue = $row['StatusAwalID'];
		$this->Nama->DbValue = $row['Nama'];
		$this->BeliOnline->DbValue = $row['BeliOnline'];
		$this->BeliFormulir->DbValue = $row['BeliFormulir'];
		$this->JalurKhusus->DbValue = $row['JalurKhusus'];
		$this->TanpaTest->DbValue = $row['TanpaTest'];
		$this->Catatan->DbValue = $row['Catatan'];
		$this->NA->DbValue = $row['NA'];
		$this->PotonganSPI_Prosen->DbValue = $row['PotonganSPI_Prosen'];
		$this->PotonganSPI_Nominal->DbValue = $row['PotonganSPI_Nominal'];
		$this->PotonganSPP_Prosen->DbValue = $row['PotonganSPP_Prosen'];
		$this->PotonganSPP_Nominal->DbValue = $row['PotonganSPP_Nominal'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("StatusAwalID")) <> "")
			$this->StatusAwalID->CurrentValue = $this->getKey("StatusAwalID"); // StatusAwalID
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
		// Urutan
		// StatusAwalID
		// Nama
		// BeliOnline
		// BeliFormulir
		// JalurKhusus
		// TanpaTest
		// Catatan
		// NA
		// PotonganSPI_Prosen
		// PotonganSPI_Nominal
		// PotonganSPP_Prosen
		// PotonganSPP_Nominal

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// Urutan
		$this->Urutan->ViewValue = $this->Urutan->CurrentValue;
		$this->Urutan->ViewCustomAttributes = "";

		// StatusAwalID
		$this->StatusAwalID->ViewValue = $this->StatusAwalID->CurrentValue;
		$this->StatusAwalID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// BeliOnline
		if (ew_ConvertToBool($this->BeliOnline->CurrentValue)) {
			$this->BeliOnline->ViewValue = $this->BeliOnline->FldTagCaption(1) <> "" ? $this->BeliOnline->FldTagCaption(1) : "Y";
		} else {
			$this->BeliOnline->ViewValue = $this->BeliOnline->FldTagCaption(2) <> "" ? $this->BeliOnline->FldTagCaption(2) : "N";
		}
		$this->BeliOnline->ViewCustomAttributes = "";

		// BeliFormulir
		if (ew_ConvertToBool($this->BeliFormulir->CurrentValue)) {
			$this->BeliFormulir->ViewValue = $this->BeliFormulir->FldTagCaption(1) <> "" ? $this->BeliFormulir->FldTagCaption(1) : "Y";
		} else {
			$this->BeliFormulir->ViewValue = $this->BeliFormulir->FldTagCaption(2) <> "" ? $this->BeliFormulir->FldTagCaption(2) : "N";
		}
		$this->BeliFormulir->ViewCustomAttributes = "";

		// JalurKhusus
		if (ew_ConvertToBool($this->JalurKhusus->CurrentValue)) {
			$this->JalurKhusus->ViewValue = $this->JalurKhusus->FldTagCaption(1) <> "" ? $this->JalurKhusus->FldTagCaption(1) : "Y";
		} else {
			$this->JalurKhusus->ViewValue = $this->JalurKhusus->FldTagCaption(2) <> "" ? $this->JalurKhusus->FldTagCaption(2) : "N";
		}
		$this->JalurKhusus->ViewCustomAttributes = "";

		// TanpaTest
		if (ew_ConvertToBool($this->TanpaTest->CurrentValue)) {
			$this->TanpaTest->ViewValue = $this->TanpaTest->FldTagCaption(1) <> "" ? $this->TanpaTest->FldTagCaption(1) : "Y";
		} else {
			$this->TanpaTest->ViewValue = $this->TanpaTest->FldTagCaption(2) <> "" ? $this->TanpaTest->FldTagCaption(2) : "N";
		}
		$this->TanpaTest->ViewCustomAttributes = "";

		// Catatan
		$this->Catatan->ViewValue = $this->Catatan->CurrentValue;
		$this->Catatan->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

		// PotonganSPI_Prosen
		$this->PotonganSPI_Prosen->ViewValue = $this->PotonganSPI_Prosen->CurrentValue;
		$this->PotonganSPI_Prosen->ViewCustomAttributes = "";

		// PotonganSPI_Nominal
		$this->PotonganSPI_Nominal->ViewValue = $this->PotonganSPI_Nominal->CurrentValue;
		$this->PotonganSPI_Nominal->ViewCustomAttributes = "";

		// PotonganSPP_Prosen
		$this->PotonganSPP_Prosen->ViewValue = $this->PotonganSPP_Prosen->CurrentValue;
		$this->PotonganSPP_Prosen->ViewCustomAttributes = "";

		// PotonganSPP_Nominal
		$this->PotonganSPP_Nominal->ViewValue = $this->PotonganSPP_Nominal->CurrentValue;
		$this->PotonganSPP_Nominal->ViewCustomAttributes = "";

			// Urutan
			$this->Urutan->LinkCustomAttributes = "";
			$this->Urutan->HrefValue = "";
			$this->Urutan->TooltipValue = "";
			if ($this->Export == "")
				$this->Urutan->ViewValue = ew_Highlight($this->HighlightName(), $this->Urutan->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Urutan->AdvancedSearch->getValue("x"), "");

			// StatusAwalID
			$this->StatusAwalID->LinkCustomAttributes = "";
			$this->StatusAwalID->HrefValue = "";
			$this->StatusAwalID->TooltipValue = "";
			if ($this->Export == "")
				$this->StatusAwalID->ViewValue = ew_Highlight($this->HighlightName(), $this->StatusAwalID->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->StatusAwalID->AdvancedSearch->getValue("x"), "");

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";
			if ($this->Export == "")
				$this->Nama->ViewValue = ew_Highlight($this->HighlightName(), $this->Nama->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Nama->AdvancedSearch->getValue("x"), "");

			// BeliOnline
			$this->BeliOnline->LinkCustomAttributes = "";
			$this->BeliOnline->HrefValue = "";
			$this->BeliOnline->TooltipValue = "";

			// BeliFormulir
			$this->BeliFormulir->LinkCustomAttributes = "";
			$this->BeliFormulir->HrefValue = "";
			$this->BeliFormulir->TooltipValue = "";

			// JalurKhusus
			$this->JalurKhusus->LinkCustomAttributes = "";
			$this->JalurKhusus->HrefValue = "";
			$this->JalurKhusus->TooltipValue = "";

			// TanpaTest
			$this->TanpaTest->LinkCustomAttributes = "";
			$this->TanpaTest->HrefValue = "";
			$this->TanpaTest->TooltipValue = "";

			// Catatan
			$this->Catatan->LinkCustomAttributes = "";
			$this->Catatan->HrefValue = "";
			$this->Catatan->TooltipValue = "";
			if ($this->Export == "")
				$this->Catatan->ViewValue = ew_Highlight($this->HighlightName(), $this->Catatan->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Catatan->AdvancedSearch->getValue("x"), "");

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";

			// PotonganSPI_Prosen
			$this->PotonganSPI_Prosen->LinkCustomAttributes = "";
			$this->PotonganSPI_Prosen->HrefValue = "";
			$this->PotonganSPI_Prosen->TooltipValue = "";
			if ($this->Export == "")
				$this->PotonganSPI_Prosen->ViewValue = ew_Highlight($this->HighlightName(), $this->PotonganSPI_Prosen->ViewValue, "", "", $this->PotonganSPI_Prosen->AdvancedSearch->getValue("x"), "");

			// PotonganSPI_Nominal
			$this->PotonganSPI_Nominal->LinkCustomAttributes = "";
			$this->PotonganSPI_Nominal->HrefValue = "";
			$this->PotonganSPI_Nominal->TooltipValue = "";
			if ($this->Export == "")
				$this->PotonganSPI_Nominal->ViewValue = ew_Highlight($this->HighlightName(), $this->PotonganSPI_Nominal->ViewValue, "", "", $this->PotonganSPI_Nominal->AdvancedSearch->getValue("x"), "");

			// PotonganSPP_Prosen
			$this->PotonganSPP_Prosen->LinkCustomAttributes = "";
			$this->PotonganSPP_Prosen->HrefValue = "";
			$this->PotonganSPP_Prosen->TooltipValue = "";
			if ($this->Export == "")
				$this->PotonganSPP_Prosen->ViewValue = ew_Highlight($this->HighlightName(), $this->PotonganSPP_Prosen->ViewValue, "", "", $this->PotonganSPP_Prosen->AdvancedSearch->getValue("x"), "");

			// PotonganSPP_Nominal
			$this->PotonganSPP_Nominal->LinkCustomAttributes = "";
			$this->PotonganSPP_Nominal->HrefValue = "";
			$this->PotonganSPP_Nominal->TooltipValue = "";
			if ($this->Export == "")
				$this->PotonganSPP_Nominal->ViewValue = ew_Highlight($this->HighlightName(), $this->PotonganSPP_Nominal->ViewValue, "", "", $this->PotonganSPP_Nominal->AdvancedSearch->getValue("x"), "");
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

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->Urutan->AdvancedSearch->Load();
		$this->StatusAwalID->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->BeliOnline->AdvancedSearch->Load();
		$this->BeliFormulir->AdvancedSearch->Load();
		$this->JalurKhusus->AdvancedSearch->Load();
		$this->TanpaTest->AdvancedSearch->Load();
		$this->Catatan->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
		$this->PotonganSPI_Prosen->AdvancedSearch->Load();
		$this->PotonganSPI_Nominal->AdvancedSearch->Load();
		$this->PotonganSPP_Prosen->AdvancedSearch->Load();
		$this->PotonganSPP_Nominal->AdvancedSearch->Load();
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
		$item->Body = "<button id=\"emf_master_statusawal\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_master_statusawal',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fmaster_statusawallist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
if (!isset($master_statusawal_list)) $master_statusawal_list = new cmaster_statusawal_list();

// Page init
$master_statusawal_list->Page_Init();

// Page main
$master_statusawal_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_statusawal_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($master_statusawal->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fmaster_statusawallist = new ew_Form("fmaster_statusawallist", "list");
fmaster_statusawallist.FormKeyCountName = '<?php echo $master_statusawal_list->FormKeyCountName ?>';

// Form_CustomValidate event
fmaster_statusawallist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_statusawallist.ValidateRequired = true;
<?php } else { ?>
fmaster_statusawallist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_statusawallist.Lists["x_BeliOnline"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawallist.Lists["x_BeliOnline"].Options = <?php echo json_encode($master_statusawal->BeliOnline->Options()) ?>;
fmaster_statusawallist.Lists["x_BeliFormulir"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawallist.Lists["x_BeliFormulir"].Options = <?php echo json_encode($master_statusawal->BeliFormulir->Options()) ?>;
fmaster_statusawallist.Lists["x_JalurKhusus"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawallist.Lists["x_JalurKhusus"].Options = <?php echo json_encode($master_statusawal->JalurKhusus->Options()) ?>;
fmaster_statusawallist.Lists["x_TanpaTest"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawallist.Lists["x_TanpaTest"].Options = <?php echo json_encode($master_statusawal->TanpaTest->Options()) ?>;
fmaster_statusawallist.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawallist.Lists["x_NA"].Options = <?php echo json_encode($master_statusawal->NA->Options()) ?>;

// Form object for search
var CurrentSearchForm = fmaster_statusawallistsrch = new ew_Form("fmaster_statusawallistsrch");

// Init search panel as collapsed
if (fmaster_statusawallistsrch) fmaster_statusawallistsrch.InitSearchPanel = true;
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
<?php if ($master_statusawal->Export == "") { ?>
<div class="ewToolbar">
<?php if ($master_statusawal->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($master_statusawal_list->TotalRecs > 0 && $master_statusawal_list->ExportOptions->Visible()) { ?>
<?php $master_statusawal_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($master_statusawal_list->SearchOptions->Visible()) { ?>
<?php $master_statusawal_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($master_statusawal_list->FilterOptions->Visible()) { ?>
<?php $master_statusawal_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php if ($master_statusawal->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = $master_statusawal_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($master_statusawal_list->TotalRecs <= 0)
			$master_statusawal_list->TotalRecs = $master_statusawal->SelectRecordCount();
	} else {
		if (!$master_statusawal_list->Recordset && ($master_statusawal_list->Recordset = $master_statusawal_list->LoadRecordset()))
			$master_statusawal_list->TotalRecs = $master_statusawal_list->Recordset->RecordCount();
	}
	$master_statusawal_list->StartRec = 1;
	if ($master_statusawal_list->DisplayRecs <= 0 || ($master_statusawal->Export <> "" && $master_statusawal->ExportAll)) // Display all records
		$master_statusawal_list->DisplayRecs = $master_statusawal_list->TotalRecs;
	if (!($master_statusawal->Export <> "" && $master_statusawal->ExportAll))
		$master_statusawal_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$master_statusawal_list->Recordset = $master_statusawal_list->LoadRecordset($master_statusawal_list->StartRec-1, $master_statusawal_list->DisplayRecs);

	// Set no record found message
	if ($master_statusawal->CurrentAction == "" && $master_statusawal_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$master_statusawal_list->setWarningMessage(ew_DeniedMsg());
		if ($master_statusawal_list->SearchWhere == "0=101")
			$master_statusawal_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$master_statusawal_list->setWarningMessage($Language->Phrase("NoRecord"));
	}

	// Audit trail on search
	if ($master_statusawal_list->AuditTrailOnSearch && $master_statusawal_list->Command == "search" && !$master_statusawal_list->RestoreSearch) {
		$searchparm = ew_ServerVar("QUERY_STRING");
		$searchsql = $master_statusawal_list->getSessionWhere();
		$master_statusawal_list->WriteAuditTrailOnSearch($searchparm, $searchsql);
	}
$master_statusawal_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($master_statusawal->Export == "" && $master_statusawal->CurrentAction == "") { ?>
<form name="fmaster_statusawallistsrch" id="fmaster_statusawallistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($master_statusawal_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fmaster_statusawallistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="master_statusawal">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($master_statusawal_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($master_statusawal_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $master_statusawal_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($master_statusawal_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($master_statusawal_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($master_statusawal_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($master_statusawal_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
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
<?php $master_statusawal_list->ShowPageHeader(); ?>
<?php
$master_statusawal_list->ShowMessage();
?>
<?php if ($master_statusawal_list->TotalRecs > 0 || $master_statusawal->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid master_statusawal">
<?php if ($master_statusawal->Export == "") { ?>
<div class="panel-heading ewGridUpperPanel">
<?php if ($master_statusawal->CurrentAction <> "gridadd" && $master_statusawal->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($master_statusawal_list->Pager)) $master_statusawal_list->Pager = new cPrevNextPager($master_statusawal_list->StartRec, $master_statusawal_list->DisplayRecs, $master_statusawal_list->TotalRecs) ?>
<?php if ($master_statusawal_list->Pager->RecordCount > 0 && $master_statusawal_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($master_statusawal_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $master_statusawal_list->PageUrl() ?>start=<?php echo $master_statusawal_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($master_statusawal_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $master_statusawal_list->PageUrl() ?>start=<?php echo $master_statusawal_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $master_statusawal_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($master_statusawal_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $master_statusawal_list->PageUrl() ?>start=<?php echo $master_statusawal_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($master_statusawal_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $master_statusawal_list->PageUrl() ?>start=<?php echo $master_statusawal_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $master_statusawal_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $master_statusawal_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $master_statusawal_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $master_statusawal_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($master_statusawal_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="fmaster_statusawallist" id="fmaster_statusawallist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_statusawal_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_statusawal_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_statusawal">
<div id="gmp_master_statusawal" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($master_statusawal_list->TotalRecs > 0 || $master_statusawal->CurrentAction == "gridedit") { ?>
<table id="tbl_master_statusawallist" class="table ewTable">
<?php echo $master_statusawal->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$master_statusawal_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$master_statusawal_list->RenderListOptions();

// Render list options (header, left)
$master_statusawal_list->ListOptions->Render("header", "left");
?>
<?php if ($master_statusawal->Urutan->Visible) { // Urutan ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->Urutan) == "") { ?>
		<th data-name="Urutan"><div id="elh_master_statusawal_Urutan" class="master_statusawal_Urutan"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->Urutan->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Urutan"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->Urutan) ?>',1);"><div id="elh_master_statusawal_Urutan" class="master_statusawal_Urutan">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->Urutan->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->Urutan->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->Urutan->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->StatusAwalID->Visible) { // StatusAwalID ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->StatusAwalID) == "") { ?>
		<th data-name="StatusAwalID"><div id="elh_master_statusawal_StatusAwalID" class="master_statusawal_StatusAwalID"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->StatusAwalID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="StatusAwalID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->StatusAwalID) ?>',1);"><div id="elh_master_statusawal_StatusAwalID" class="master_statusawal_StatusAwalID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->StatusAwalID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->StatusAwalID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->StatusAwalID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->Nama->Visible) { // Nama ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->Nama) == "") { ?>
		<th data-name="Nama"><div id="elh_master_statusawal_Nama" class="master_statusawal_Nama"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->Nama->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Nama"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->Nama) ?>',1);"><div id="elh_master_statusawal_Nama" class="master_statusawal_Nama">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->Nama->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->Nama->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->Nama->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->BeliOnline->Visible) { // BeliOnline ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->BeliOnline) == "") { ?>
		<th data-name="BeliOnline"><div id="elh_master_statusawal_BeliOnline" class="master_statusawal_BeliOnline"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->BeliOnline->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="BeliOnline"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->BeliOnline) ?>',1);"><div id="elh_master_statusawal_BeliOnline" class="master_statusawal_BeliOnline">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->BeliOnline->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->BeliOnline->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->BeliOnline->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->BeliFormulir->Visible) { // BeliFormulir ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->BeliFormulir) == "") { ?>
		<th data-name="BeliFormulir"><div id="elh_master_statusawal_BeliFormulir" class="master_statusawal_BeliFormulir"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->BeliFormulir->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="BeliFormulir"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->BeliFormulir) ?>',1);"><div id="elh_master_statusawal_BeliFormulir" class="master_statusawal_BeliFormulir">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->BeliFormulir->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->BeliFormulir->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->BeliFormulir->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->JalurKhusus->Visible) { // JalurKhusus ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->JalurKhusus) == "") { ?>
		<th data-name="JalurKhusus"><div id="elh_master_statusawal_JalurKhusus" class="master_statusawal_JalurKhusus"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->JalurKhusus->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="JalurKhusus"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->JalurKhusus) ?>',1);"><div id="elh_master_statusawal_JalurKhusus" class="master_statusawal_JalurKhusus">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->JalurKhusus->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->JalurKhusus->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->JalurKhusus->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->TanpaTest->Visible) { // TanpaTest ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->TanpaTest) == "") { ?>
		<th data-name="TanpaTest"><div id="elh_master_statusawal_TanpaTest" class="master_statusawal_TanpaTest"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->TanpaTest->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TanpaTest"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->TanpaTest) ?>',1);"><div id="elh_master_statusawal_TanpaTest" class="master_statusawal_TanpaTest">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->TanpaTest->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->TanpaTest->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->TanpaTest->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->Catatan->Visible) { // Catatan ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->Catatan) == "") { ?>
		<th data-name="Catatan"><div id="elh_master_statusawal_Catatan" class="master_statusawal_Catatan"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->Catatan->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Catatan"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->Catatan) ?>',1);"><div id="elh_master_statusawal_Catatan" class="master_statusawal_Catatan">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->Catatan->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->Catatan->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->Catatan->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->NA->Visible) { // NA ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->NA) == "") { ?>
		<th data-name="NA"><div id="elh_master_statusawal_NA" class="master_statusawal_NA"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->NA->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NA"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->NA) ?>',1);"><div id="elh_master_statusawal_NA" class="master_statusawal_NA">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->NA->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->NA->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->NA->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->PotonganSPI_Prosen->Visible) { // PotonganSPI_Prosen ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->PotonganSPI_Prosen) == "") { ?>
		<th data-name="PotonganSPI_Prosen"><div id="elh_master_statusawal_PotonganSPI_Prosen" class="master_statusawal_PotonganSPI_Prosen"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->PotonganSPI_Prosen->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="PotonganSPI_Prosen"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->PotonganSPI_Prosen) ?>',1);"><div id="elh_master_statusawal_PotonganSPI_Prosen" class="master_statusawal_PotonganSPI_Prosen">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->PotonganSPI_Prosen->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->PotonganSPI_Prosen->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->PotonganSPI_Prosen->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->PotonganSPI_Nominal->Visible) { // PotonganSPI_Nominal ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->PotonganSPI_Nominal) == "") { ?>
		<th data-name="PotonganSPI_Nominal"><div id="elh_master_statusawal_PotonganSPI_Nominal" class="master_statusawal_PotonganSPI_Nominal"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->PotonganSPI_Nominal->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="PotonganSPI_Nominal"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->PotonganSPI_Nominal) ?>',1);"><div id="elh_master_statusawal_PotonganSPI_Nominal" class="master_statusawal_PotonganSPI_Nominal">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->PotonganSPI_Nominal->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->PotonganSPI_Nominal->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->PotonganSPI_Nominal->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->PotonganSPP_Prosen->Visible) { // PotonganSPP_Prosen ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->PotonganSPP_Prosen) == "") { ?>
		<th data-name="PotonganSPP_Prosen"><div id="elh_master_statusawal_PotonganSPP_Prosen" class="master_statusawal_PotonganSPP_Prosen"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->PotonganSPP_Prosen->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="PotonganSPP_Prosen"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->PotonganSPP_Prosen) ?>',1);"><div id="elh_master_statusawal_PotonganSPP_Prosen" class="master_statusawal_PotonganSPP_Prosen">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->PotonganSPP_Prosen->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->PotonganSPP_Prosen->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->PotonganSPP_Prosen->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_statusawal->PotonganSPP_Nominal->Visible) { // PotonganSPP_Nominal ?>
	<?php if ($master_statusawal->SortUrl($master_statusawal->PotonganSPP_Nominal) == "") { ?>
		<th data-name="PotonganSPP_Nominal"><div id="elh_master_statusawal_PotonganSPP_Nominal" class="master_statusawal_PotonganSPP_Nominal"><div class="ewTableHeaderCaption"><?php echo $master_statusawal->PotonganSPP_Nominal->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="PotonganSPP_Nominal"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $master_statusawal->SortUrl($master_statusawal->PotonganSPP_Nominal) ?>',1);"><div id="elh_master_statusawal_PotonganSPP_Nominal" class="master_statusawal_PotonganSPP_Nominal">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_statusawal->PotonganSPP_Nominal->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_statusawal->PotonganSPP_Nominal->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_statusawal->PotonganSPP_Nominal->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$master_statusawal_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($master_statusawal->ExportAll && $master_statusawal->Export <> "") {
	$master_statusawal_list->StopRec = $master_statusawal_list->TotalRecs;
} else {

	// Set the last record to display
	if ($master_statusawal_list->TotalRecs > $master_statusawal_list->StartRec + $master_statusawal_list->DisplayRecs - 1)
		$master_statusawal_list->StopRec = $master_statusawal_list->StartRec + $master_statusawal_list->DisplayRecs - 1;
	else
		$master_statusawal_list->StopRec = $master_statusawal_list->TotalRecs;
}
$master_statusawal_list->RecCnt = $master_statusawal_list->StartRec - 1;
if ($master_statusawal_list->Recordset && !$master_statusawal_list->Recordset->EOF) {
	$master_statusawal_list->Recordset->MoveFirst();
	$bSelectLimit = $master_statusawal_list->UseSelectLimit;
	if (!$bSelectLimit && $master_statusawal_list->StartRec > 1)
		$master_statusawal_list->Recordset->Move($master_statusawal_list->StartRec - 1);
} elseif (!$master_statusawal->AllowAddDeleteRow && $master_statusawal_list->StopRec == 0) {
	$master_statusawal_list->StopRec = $master_statusawal->GridAddRowCount;
}

// Initialize aggregate
$master_statusawal->RowType = EW_ROWTYPE_AGGREGATEINIT;
$master_statusawal->ResetAttrs();
$master_statusawal_list->RenderRow();
while ($master_statusawal_list->RecCnt < $master_statusawal_list->StopRec) {
	$master_statusawal_list->RecCnt++;
	if (intval($master_statusawal_list->RecCnt) >= intval($master_statusawal_list->StartRec)) {
		$master_statusawal_list->RowCnt++;

		// Set up key count
		$master_statusawal_list->KeyCount = $master_statusawal_list->RowIndex;

		// Init row class and style
		$master_statusawal->ResetAttrs();
		$master_statusawal->CssClass = "";
		if ($master_statusawal->CurrentAction == "gridadd") {
		} else {
			$master_statusawal_list->LoadRowValues($master_statusawal_list->Recordset); // Load row values
		}
		$master_statusawal->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$master_statusawal->RowAttrs = array_merge($master_statusawal->RowAttrs, array('data-rowindex'=>$master_statusawal_list->RowCnt, 'id'=>'r' . $master_statusawal_list->RowCnt . '_master_statusawal', 'data-rowtype'=>$master_statusawal->RowType));

		// Render row
		$master_statusawal_list->RenderRow();

		// Render list options
		$master_statusawal_list->RenderListOptions();
?>
	<tr<?php echo $master_statusawal->RowAttributes() ?>>
<?php

// Render list options (body, left)
$master_statusawal_list->ListOptions->Render("body", "left", $master_statusawal_list->RowCnt);
?>
	<?php if ($master_statusawal->Urutan->Visible) { // Urutan ?>
		<td data-name="Urutan"<?php echo $master_statusawal->Urutan->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_Urutan" class="master_statusawal_Urutan">
<span<?php echo $master_statusawal->Urutan->ViewAttributes() ?>>
<?php echo $master_statusawal->Urutan->ListViewValue() ?></span>
</span>
<a id="<?php echo $master_statusawal_list->PageObjName . "_row_" . $master_statusawal_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($master_statusawal->StatusAwalID->Visible) { // StatusAwalID ?>
		<td data-name="StatusAwalID"<?php echo $master_statusawal->StatusAwalID->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_StatusAwalID" class="master_statusawal_StatusAwalID">
<span<?php echo $master_statusawal->StatusAwalID->ViewAttributes() ?>>
<?php echo $master_statusawal->StatusAwalID->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($master_statusawal->Nama->Visible) { // Nama ?>
		<td data-name="Nama"<?php echo $master_statusawal->Nama->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_Nama" class="master_statusawal_Nama">
<span<?php echo $master_statusawal->Nama->ViewAttributes() ?>>
<?php echo $master_statusawal->Nama->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($master_statusawal->BeliOnline->Visible) { // BeliOnline ?>
		<td data-name="BeliOnline"<?php echo $master_statusawal->BeliOnline->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_BeliOnline" class="master_statusawal_BeliOnline">
<span<?php echo $master_statusawal->BeliOnline->ViewAttributes() ?>>
<?php echo $master_statusawal->BeliOnline->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($master_statusawal->BeliFormulir->Visible) { // BeliFormulir ?>
		<td data-name="BeliFormulir"<?php echo $master_statusawal->BeliFormulir->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_BeliFormulir" class="master_statusawal_BeliFormulir">
<span<?php echo $master_statusawal->BeliFormulir->ViewAttributes() ?>>
<?php echo $master_statusawal->BeliFormulir->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($master_statusawal->JalurKhusus->Visible) { // JalurKhusus ?>
		<td data-name="JalurKhusus"<?php echo $master_statusawal->JalurKhusus->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_JalurKhusus" class="master_statusawal_JalurKhusus">
<span<?php echo $master_statusawal->JalurKhusus->ViewAttributes() ?>>
<?php echo $master_statusawal->JalurKhusus->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($master_statusawal->TanpaTest->Visible) { // TanpaTest ?>
		<td data-name="TanpaTest"<?php echo $master_statusawal->TanpaTest->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_TanpaTest" class="master_statusawal_TanpaTest">
<span<?php echo $master_statusawal->TanpaTest->ViewAttributes() ?>>
<?php echo $master_statusawal->TanpaTest->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($master_statusawal->Catatan->Visible) { // Catatan ?>
		<td data-name="Catatan"<?php echo $master_statusawal->Catatan->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_Catatan" class="master_statusawal_Catatan">
<span<?php echo $master_statusawal->Catatan->ViewAttributes() ?>>
<?php echo $master_statusawal->Catatan->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($master_statusawal->NA->Visible) { // NA ?>
		<td data-name="NA"<?php echo $master_statusawal->NA->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_NA" class="master_statusawal_NA">
<span<?php echo $master_statusawal->NA->ViewAttributes() ?>>
<?php echo $master_statusawal->NA->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($master_statusawal->PotonganSPI_Prosen->Visible) { // PotonganSPI_Prosen ?>
		<td data-name="PotonganSPI_Prosen"<?php echo $master_statusawal->PotonganSPI_Prosen->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_PotonganSPI_Prosen" class="master_statusawal_PotonganSPI_Prosen">
<span<?php echo $master_statusawal->PotonganSPI_Prosen->ViewAttributes() ?>>
<?php echo $master_statusawal->PotonganSPI_Prosen->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($master_statusawal->PotonganSPI_Nominal->Visible) { // PotonganSPI_Nominal ?>
		<td data-name="PotonganSPI_Nominal"<?php echo $master_statusawal->PotonganSPI_Nominal->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_PotonganSPI_Nominal" class="master_statusawal_PotonganSPI_Nominal">
<span<?php echo $master_statusawal->PotonganSPI_Nominal->ViewAttributes() ?>>
<?php echo $master_statusawal->PotonganSPI_Nominal->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($master_statusawal->PotonganSPP_Prosen->Visible) { // PotonganSPP_Prosen ?>
		<td data-name="PotonganSPP_Prosen"<?php echo $master_statusawal->PotonganSPP_Prosen->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_PotonganSPP_Prosen" class="master_statusawal_PotonganSPP_Prosen">
<span<?php echo $master_statusawal->PotonganSPP_Prosen->ViewAttributes() ?>>
<?php echo $master_statusawal->PotonganSPP_Prosen->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($master_statusawal->PotonganSPP_Nominal->Visible) { // PotonganSPP_Nominal ?>
		<td data-name="PotonganSPP_Nominal"<?php echo $master_statusawal->PotonganSPP_Nominal->CellAttributes() ?>>
<span id="el<?php echo $master_statusawal_list->RowCnt ?>_master_statusawal_PotonganSPP_Nominal" class="master_statusawal_PotonganSPP_Nominal">
<span<?php echo $master_statusawal->PotonganSPP_Nominal->ViewAttributes() ?>>
<?php echo $master_statusawal->PotonganSPP_Nominal->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$master_statusawal_list->ListOptions->Render("body", "right", $master_statusawal_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($master_statusawal->CurrentAction <> "gridadd")
		$master_statusawal_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($master_statusawal->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($master_statusawal_list->Recordset)
	$master_statusawal_list->Recordset->Close();
?>
<?php if ($master_statusawal->Export == "") { ?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($master_statusawal->CurrentAction <> "gridadd" && $master_statusawal->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($master_statusawal_list->Pager)) $master_statusawal_list->Pager = new cPrevNextPager($master_statusawal_list->StartRec, $master_statusawal_list->DisplayRecs, $master_statusawal_list->TotalRecs) ?>
<?php if ($master_statusawal_list->Pager->RecordCount > 0 && $master_statusawal_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($master_statusawal_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $master_statusawal_list->PageUrl() ?>start=<?php echo $master_statusawal_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($master_statusawal_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $master_statusawal_list->PageUrl() ?>start=<?php echo $master_statusawal_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $master_statusawal_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($master_statusawal_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $master_statusawal_list->PageUrl() ?>start=<?php echo $master_statusawal_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($master_statusawal_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $master_statusawal_list->PageUrl() ?>start=<?php echo $master_statusawal_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $master_statusawal_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $master_statusawal_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $master_statusawal_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $master_statusawal_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($master_statusawal_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($master_statusawal_list->TotalRecs == 0 && $master_statusawal->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($master_statusawal_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($master_statusawal->Export == "") { ?>
<script type="text/javascript">
fmaster_statusawallistsrch.FilterList = <?php echo $master_statusawal_list->GetFilterList() ?>;
fmaster_statusawallistsrch.Init();
fmaster_statusawallist.Init();
</script>
<?php } ?>
<?php
$master_statusawal_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($master_statusawal->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$master_statusawal_list->Page_Terminate();
?>
