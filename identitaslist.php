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

$identitas_list = NULL; // Initialize page object first

class cidentitas_list extends cidentitas {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'identitas';

	// Page object name
	var $PageObjName = 'identitas_list';

	// Grid form hidden field names
	var $FormName = 'fidentitaslist';
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

		// Table object (identitas)
		if (!isset($GLOBALS["identitas"]) || get_class($GLOBALS["identitas"]) == "cidentitas") {
			$GLOBALS["identitas"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["identitas"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "identitasadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "identitasdelete.php";
		$this->MultiUpdateUrl = "identitasupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fidentitaslistsrch";

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
		if (count($arrKeyFlds) >= 0) {
		}
		return TRUE;
	}

	// Get list of filters
	function GetFilterList() {
		global $UserProfile;

		// Load server side filters
		if (EW_SEARCH_FILTER_OPTION == "Server") {
			$sSavedFilterList = isset($UserProfile) ? $UserProfile->GetSearchFilters(CurrentUserName(), "fidentitaslistsrch") : "";
		} else {
			$sSavedFilterList = "";
		}

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->Kode->AdvancedSearch->ToJSON(), ","); // Field Kode
		$sFilterList = ew_Concat($sFilterList, $this->KodeHukum->AdvancedSearch->ToJSON(), ","); // Field KodeHukum
		$sFilterList = ew_Concat($sFilterList, $this->Nama->AdvancedSearch->ToJSON(), ","); // Field Nama
		$sFilterList = ew_Concat($sFilterList, $this->TglMulai->AdvancedSearch->ToJSON(), ","); // Field TglMulai
		$sFilterList = ew_Concat($sFilterList, $this->Alamat1->AdvancedSearch->ToJSON(), ","); // Field Alamat1
		$sFilterList = ew_Concat($sFilterList, $this->Alamat2->AdvancedSearch->ToJSON(), ","); // Field Alamat2
		$sFilterList = ew_Concat($sFilterList, $this->Kota->AdvancedSearch->ToJSON(), ","); // Field Kota
		$sFilterList = ew_Concat($sFilterList, $this->KodePos->AdvancedSearch->ToJSON(), ","); // Field KodePos
		$sFilterList = ew_Concat($sFilterList, $this->Telepon->AdvancedSearch->ToJSON(), ","); // Field Telepon
		$sFilterList = ew_Concat($sFilterList, $this->Fax->AdvancedSearch->ToJSON(), ","); // Field Fax
		$sFilterList = ew_Concat($sFilterList, $this->_Email->AdvancedSearch->ToJSON(), ","); // Field Email
		$sFilterList = ew_Concat($sFilterList, $this->Website->AdvancedSearch->ToJSON(), ","); // Field Website
		$sFilterList = ew_Concat($sFilterList, $this->NoAkta->AdvancedSearch->ToJSON(), ","); // Field NoAkta
		$sFilterList = ew_Concat($sFilterList, $this->TglAkta->AdvancedSearch->ToJSON(), ","); // Field TglAkta
		$sFilterList = ew_Concat($sFilterList, $this->NoSah->AdvancedSearch->ToJSON(), ","); // Field NoSah
		$sFilterList = ew_Concat($sFilterList, $this->TglSah->AdvancedSearch->ToJSON(), ","); // Field TglSah
		$sFilterList = ew_Concat($sFilterList, $this->Logo->AdvancedSearch->ToJSON(), ","); // Field Logo
		$sFilterList = ew_Concat($sFilterList, $this->StartNoIdentitas->AdvancedSearch->ToJSON(), ","); // Field StartNoIdentitas
		$sFilterList = ew_Concat($sFilterList, $this->NoIdentitas->AdvancedSearch->ToJSON(), ","); // Field NoIdentitas
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
			$UserProfile->SetSearchFilters(CurrentUserName(), "fidentitaslistsrch", $filters);

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

		// Field Kode
		$this->Kode->AdvancedSearch->SearchValue = @$filter["x_Kode"];
		$this->Kode->AdvancedSearch->SearchOperator = @$filter["z_Kode"];
		$this->Kode->AdvancedSearch->SearchCondition = @$filter["v_Kode"];
		$this->Kode->AdvancedSearch->SearchValue2 = @$filter["y_Kode"];
		$this->Kode->AdvancedSearch->SearchOperator2 = @$filter["w_Kode"];
		$this->Kode->AdvancedSearch->Save();

		// Field KodeHukum
		$this->KodeHukum->AdvancedSearch->SearchValue = @$filter["x_KodeHukum"];
		$this->KodeHukum->AdvancedSearch->SearchOperator = @$filter["z_KodeHukum"];
		$this->KodeHukum->AdvancedSearch->SearchCondition = @$filter["v_KodeHukum"];
		$this->KodeHukum->AdvancedSearch->SearchValue2 = @$filter["y_KodeHukum"];
		$this->KodeHukum->AdvancedSearch->SearchOperator2 = @$filter["w_KodeHukum"];
		$this->KodeHukum->AdvancedSearch->Save();

		// Field Nama
		$this->Nama->AdvancedSearch->SearchValue = @$filter["x_Nama"];
		$this->Nama->AdvancedSearch->SearchOperator = @$filter["z_Nama"];
		$this->Nama->AdvancedSearch->SearchCondition = @$filter["v_Nama"];
		$this->Nama->AdvancedSearch->SearchValue2 = @$filter["y_Nama"];
		$this->Nama->AdvancedSearch->SearchOperator2 = @$filter["w_Nama"];
		$this->Nama->AdvancedSearch->Save();

		// Field TglMulai
		$this->TglMulai->AdvancedSearch->SearchValue = @$filter["x_TglMulai"];
		$this->TglMulai->AdvancedSearch->SearchOperator = @$filter["z_TglMulai"];
		$this->TglMulai->AdvancedSearch->SearchCondition = @$filter["v_TglMulai"];
		$this->TglMulai->AdvancedSearch->SearchValue2 = @$filter["y_TglMulai"];
		$this->TglMulai->AdvancedSearch->SearchOperator2 = @$filter["w_TglMulai"];
		$this->TglMulai->AdvancedSearch->Save();

		// Field Alamat1
		$this->Alamat1->AdvancedSearch->SearchValue = @$filter["x_Alamat1"];
		$this->Alamat1->AdvancedSearch->SearchOperator = @$filter["z_Alamat1"];
		$this->Alamat1->AdvancedSearch->SearchCondition = @$filter["v_Alamat1"];
		$this->Alamat1->AdvancedSearch->SearchValue2 = @$filter["y_Alamat1"];
		$this->Alamat1->AdvancedSearch->SearchOperator2 = @$filter["w_Alamat1"];
		$this->Alamat1->AdvancedSearch->Save();

		// Field Alamat2
		$this->Alamat2->AdvancedSearch->SearchValue = @$filter["x_Alamat2"];
		$this->Alamat2->AdvancedSearch->SearchOperator = @$filter["z_Alamat2"];
		$this->Alamat2->AdvancedSearch->SearchCondition = @$filter["v_Alamat2"];
		$this->Alamat2->AdvancedSearch->SearchValue2 = @$filter["y_Alamat2"];
		$this->Alamat2->AdvancedSearch->SearchOperator2 = @$filter["w_Alamat2"];
		$this->Alamat2->AdvancedSearch->Save();

		// Field Kota
		$this->Kota->AdvancedSearch->SearchValue = @$filter["x_Kota"];
		$this->Kota->AdvancedSearch->SearchOperator = @$filter["z_Kota"];
		$this->Kota->AdvancedSearch->SearchCondition = @$filter["v_Kota"];
		$this->Kota->AdvancedSearch->SearchValue2 = @$filter["y_Kota"];
		$this->Kota->AdvancedSearch->SearchOperator2 = @$filter["w_Kota"];
		$this->Kota->AdvancedSearch->Save();

		// Field KodePos
		$this->KodePos->AdvancedSearch->SearchValue = @$filter["x_KodePos"];
		$this->KodePos->AdvancedSearch->SearchOperator = @$filter["z_KodePos"];
		$this->KodePos->AdvancedSearch->SearchCondition = @$filter["v_KodePos"];
		$this->KodePos->AdvancedSearch->SearchValue2 = @$filter["y_KodePos"];
		$this->KodePos->AdvancedSearch->SearchOperator2 = @$filter["w_KodePos"];
		$this->KodePos->AdvancedSearch->Save();

		// Field Telepon
		$this->Telepon->AdvancedSearch->SearchValue = @$filter["x_Telepon"];
		$this->Telepon->AdvancedSearch->SearchOperator = @$filter["z_Telepon"];
		$this->Telepon->AdvancedSearch->SearchCondition = @$filter["v_Telepon"];
		$this->Telepon->AdvancedSearch->SearchValue2 = @$filter["y_Telepon"];
		$this->Telepon->AdvancedSearch->SearchOperator2 = @$filter["w_Telepon"];
		$this->Telepon->AdvancedSearch->Save();

		// Field Fax
		$this->Fax->AdvancedSearch->SearchValue = @$filter["x_Fax"];
		$this->Fax->AdvancedSearch->SearchOperator = @$filter["z_Fax"];
		$this->Fax->AdvancedSearch->SearchCondition = @$filter["v_Fax"];
		$this->Fax->AdvancedSearch->SearchValue2 = @$filter["y_Fax"];
		$this->Fax->AdvancedSearch->SearchOperator2 = @$filter["w_Fax"];
		$this->Fax->AdvancedSearch->Save();

		// Field Email
		$this->_Email->AdvancedSearch->SearchValue = @$filter["x__Email"];
		$this->_Email->AdvancedSearch->SearchOperator = @$filter["z__Email"];
		$this->_Email->AdvancedSearch->SearchCondition = @$filter["v__Email"];
		$this->_Email->AdvancedSearch->SearchValue2 = @$filter["y__Email"];
		$this->_Email->AdvancedSearch->SearchOperator2 = @$filter["w__Email"];
		$this->_Email->AdvancedSearch->Save();

		// Field Website
		$this->Website->AdvancedSearch->SearchValue = @$filter["x_Website"];
		$this->Website->AdvancedSearch->SearchOperator = @$filter["z_Website"];
		$this->Website->AdvancedSearch->SearchCondition = @$filter["v_Website"];
		$this->Website->AdvancedSearch->SearchValue2 = @$filter["y_Website"];
		$this->Website->AdvancedSearch->SearchOperator2 = @$filter["w_Website"];
		$this->Website->AdvancedSearch->Save();

		// Field NoAkta
		$this->NoAkta->AdvancedSearch->SearchValue = @$filter["x_NoAkta"];
		$this->NoAkta->AdvancedSearch->SearchOperator = @$filter["z_NoAkta"];
		$this->NoAkta->AdvancedSearch->SearchCondition = @$filter["v_NoAkta"];
		$this->NoAkta->AdvancedSearch->SearchValue2 = @$filter["y_NoAkta"];
		$this->NoAkta->AdvancedSearch->SearchOperator2 = @$filter["w_NoAkta"];
		$this->NoAkta->AdvancedSearch->Save();

		// Field TglAkta
		$this->TglAkta->AdvancedSearch->SearchValue = @$filter["x_TglAkta"];
		$this->TglAkta->AdvancedSearch->SearchOperator = @$filter["z_TglAkta"];
		$this->TglAkta->AdvancedSearch->SearchCondition = @$filter["v_TglAkta"];
		$this->TglAkta->AdvancedSearch->SearchValue2 = @$filter["y_TglAkta"];
		$this->TglAkta->AdvancedSearch->SearchOperator2 = @$filter["w_TglAkta"];
		$this->TglAkta->AdvancedSearch->Save();

		// Field NoSah
		$this->NoSah->AdvancedSearch->SearchValue = @$filter["x_NoSah"];
		$this->NoSah->AdvancedSearch->SearchOperator = @$filter["z_NoSah"];
		$this->NoSah->AdvancedSearch->SearchCondition = @$filter["v_NoSah"];
		$this->NoSah->AdvancedSearch->SearchValue2 = @$filter["y_NoSah"];
		$this->NoSah->AdvancedSearch->SearchOperator2 = @$filter["w_NoSah"];
		$this->NoSah->AdvancedSearch->Save();

		// Field TglSah
		$this->TglSah->AdvancedSearch->SearchValue = @$filter["x_TglSah"];
		$this->TglSah->AdvancedSearch->SearchOperator = @$filter["z_TglSah"];
		$this->TglSah->AdvancedSearch->SearchCondition = @$filter["v_TglSah"];
		$this->TglSah->AdvancedSearch->SearchValue2 = @$filter["y_TglSah"];
		$this->TglSah->AdvancedSearch->SearchOperator2 = @$filter["w_TglSah"];
		$this->TglSah->AdvancedSearch->Save();

		// Field Logo
		$this->Logo->AdvancedSearch->SearchValue = @$filter["x_Logo"];
		$this->Logo->AdvancedSearch->SearchOperator = @$filter["z_Logo"];
		$this->Logo->AdvancedSearch->SearchCondition = @$filter["v_Logo"];
		$this->Logo->AdvancedSearch->SearchValue2 = @$filter["y_Logo"];
		$this->Logo->AdvancedSearch->SearchOperator2 = @$filter["w_Logo"];
		$this->Logo->AdvancedSearch->Save();

		// Field StartNoIdentitas
		$this->StartNoIdentitas->AdvancedSearch->SearchValue = @$filter["x_StartNoIdentitas"];
		$this->StartNoIdentitas->AdvancedSearch->SearchOperator = @$filter["z_StartNoIdentitas"];
		$this->StartNoIdentitas->AdvancedSearch->SearchCondition = @$filter["v_StartNoIdentitas"];
		$this->StartNoIdentitas->AdvancedSearch->SearchValue2 = @$filter["y_StartNoIdentitas"];
		$this->StartNoIdentitas->AdvancedSearch->SearchOperator2 = @$filter["w_StartNoIdentitas"];
		$this->StartNoIdentitas->AdvancedSearch->Save();

		// Field NoIdentitas
		$this->NoIdentitas->AdvancedSearch->SearchValue = @$filter["x_NoIdentitas"];
		$this->NoIdentitas->AdvancedSearch->SearchOperator = @$filter["z_NoIdentitas"];
		$this->NoIdentitas->AdvancedSearch->SearchCondition = @$filter["v_NoIdentitas"];
		$this->NoIdentitas->AdvancedSearch->SearchValue2 = @$filter["y_NoIdentitas"];
		$this->NoIdentitas->AdvancedSearch->SearchOperator2 = @$filter["w_NoIdentitas"];
		$this->NoIdentitas->AdvancedSearch->Save();

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
		$this->BuildSearchSql($sWhere, $this->Kode, $Default, FALSE); // Kode
		$this->BuildSearchSql($sWhere, $this->KodeHukum, $Default, FALSE); // KodeHukum
		$this->BuildSearchSql($sWhere, $this->Nama, $Default, FALSE); // Nama
		$this->BuildSearchSql($sWhere, $this->TglMulai, $Default, FALSE); // TglMulai
		$this->BuildSearchSql($sWhere, $this->Alamat1, $Default, FALSE); // Alamat1
		$this->BuildSearchSql($sWhere, $this->Alamat2, $Default, FALSE); // Alamat2
		$this->BuildSearchSql($sWhere, $this->Kota, $Default, FALSE); // Kota
		$this->BuildSearchSql($sWhere, $this->KodePos, $Default, FALSE); // KodePos
		$this->BuildSearchSql($sWhere, $this->Telepon, $Default, FALSE); // Telepon
		$this->BuildSearchSql($sWhere, $this->Fax, $Default, FALSE); // Fax
		$this->BuildSearchSql($sWhere, $this->_Email, $Default, FALSE); // Email
		$this->BuildSearchSql($sWhere, $this->Website, $Default, FALSE); // Website
		$this->BuildSearchSql($sWhere, $this->NoAkta, $Default, FALSE); // NoAkta
		$this->BuildSearchSql($sWhere, $this->TglAkta, $Default, FALSE); // TglAkta
		$this->BuildSearchSql($sWhere, $this->NoSah, $Default, FALSE); // NoSah
		$this->BuildSearchSql($sWhere, $this->TglSah, $Default, FALSE); // TglSah
		$this->BuildSearchSql($sWhere, $this->Logo, $Default, FALSE); // Logo
		$this->BuildSearchSql($sWhere, $this->StartNoIdentitas, $Default, FALSE); // StartNoIdentitas
		$this->BuildSearchSql($sWhere, $this->NoIdentitas, $Default, FALSE); // NoIdentitas
		$this->BuildSearchSql($sWhere, $this->NA, $Default, FALSE); // NA

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->Kode->AdvancedSearch->Save(); // Kode
			$this->KodeHukum->AdvancedSearch->Save(); // KodeHukum
			$this->Nama->AdvancedSearch->Save(); // Nama
			$this->TglMulai->AdvancedSearch->Save(); // TglMulai
			$this->Alamat1->AdvancedSearch->Save(); // Alamat1
			$this->Alamat2->AdvancedSearch->Save(); // Alamat2
			$this->Kota->AdvancedSearch->Save(); // Kota
			$this->KodePos->AdvancedSearch->Save(); // KodePos
			$this->Telepon->AdvancedSearch->Save(); // Telepon
			$this->Fax->AdvancedSearch->Save(); // Fax
			$this->_Email->AdvancedSearch->Save(); // Email
			$this->Website->AdvancedSearch->Save(); // Website
			$this->NoAkta->AdvancedSearch->Save(); // NoAkta
			$this->TglAkta->AdvancedSearch->Save(); // TglAkta
			$this->NoSah->AdvancedSearch->Save(); // NoSah
			$this->TglSah->AdvancedSearch->Save(); // TglSah
			$this->Logo->AdvancedSearch->Save(); // Logo
			$this->StartNoIdentitas->AdvancedSearch->Save(); // StartNoIdentitas
			$this->NoIdentitas->AdvancedSearch->Save(); // NoIdentitas
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
		$this->BuildBasicSearchSQL($sWhere, $this->Kode, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KodeHukum, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Nama, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Alamat1, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Alamat2, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Kota, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KodePos, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Telepon, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Fax, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->_Email, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Website, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NoAkta, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NoSah, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Logo, $arKeywords, $type);
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
		if ($this->Kode->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KodeHukum->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Nama->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TglMulai->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Alamat1->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Alamat2->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Kota->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KodePos->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Telepon->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Fax->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->_Email->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Website->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NoAkta->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TglAkta->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NoSah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TglSah->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Logo->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->StartNoIdentitas->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NoIdentitas->AdvancedSearch->IssetSession())
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
		$this->Kode->AdvancedSearch->UnsetSession();
		$this->KodeHukum->AdvancedSearch->UnsetSession();
		$this->Nama->AdvancedSearch->UnsetSession();
		$this->TglMulai->AdvancedSearch->UnsetSession();
		$this->Alamat1->AdvancedSearch->UnsetSession();
		$this->Alamat2->AdvancedSearch->UnsetSession();
		$this->Kota->AdvancedSearch->UnsetSession();
		$this->KodePos->AdvancedSearch->UnsetSession();
		$this->Telepon->AdvancedSearch->UnsetSession();
		$this->Fax->AdvancedSearch->UnsetSession();
		$this->_Email->AdvancedSearch->UnsetSession();
		$this->Website->AdvancedSearch->UnsetSession();
		$this->NoAkta->AdvancedSearch->UnsetSession();
		$this->TglAkta->AdvancedSearch->UnsetSession();
		$this->NoSah->AdvancedSearch->UnsetSession();
		$this->TglSah->AdvancedSearch->UnsetSession();
		$this->Logo->AdvancedSearch->UnsetSession();
		$this->StartNoIdentitas->AdvancedSearch->UnsetSession();
		$this->NoIdentitas->AdvancedSearch->UnsetSession();
		$this->NA->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->Kode->AdvancedSearch->Load();
		$this->KodeHukum->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->TglMulai->AdvancedSearch->Load();
		$this->Alamat1->AdvancedSearch->Load();
		$this->Alamat2->AdvancedSearch->Load();
		$this->Kota->AdvancedSearch->Load();
		$this->KodePos->AdvancedSearch->Load();
		$this->Telepon->AdvancedSearch->Load();
		$this->Fax->AdvancedSearch->Load();
		$this->_Email->AdvancedSearch->Load();
		$this->Website->AdvancedSearch->Load();
		$this->NoAkta->AdvancedSearch->Load();
		$this->TglAkta->AdvancedSearch->Load();
		$this->NoSah->AdvancedSearch->Load();
		$this->TglSah->AdvancedSearch->Load();
		$this->Logo->AdvancedSearch->Load();
		$this->StartNoIdentitas->AdvancedSearch->Load();
		$this->NoIdentitas->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->Kode); // Kode
			$this->UpdateSort($this->KodeHukum); // KodeHukum
			$this->UpdateSort($this->Nama); // Nama
			$this->UpdateSort($this->TglMulai); // TglMulai
			$this->UpdateSort($this->Alamat1); // Alamat1
			$this->UpdateSort($this->Alamat2); // Alamat2
			$this->UpdateSort($this->Kota); // Kota
			$this->UpdateSort($this->KodePos); // KodePos
			$this->UpdateSort($this->Telepon); // Telepon
			$this->UpdateSort($this->Fax); // Fax
			$this->UpdateSort($this->_Email); // Email
			$this->UpdateSort($this->Website); // Website
			$this->UpdateSort($this->NoAkta); // NoAkta
			$this->UpdateSort($this->TglAkta); // TglAkta
			$this->UpdateSort($this->NoSah); // NoSah
			$this->UpdateSort($this->TglSah); // TglSah
			$this->UpdateSort($this->Logo); // Logo
			$this->UpdateSort($this->StartNoIdentitas); // StartNoIdentitas
			$this->UpdateSort($this->NoIdentitas); // NoIdentitas
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
				$this->Kode->setSort("");
				$this->KodeHukum->setSort("");
				$this->Nama->setSort("");
				$this->TglMulai->setSort("");
				$this->Alamat1->setSort("");
				$this->Alamat2->setSort("");
				$this->Kota->setSort("");
				$this->KodePos->setSort("");
				$this->Telepon->setSort("");
				$this->Fax->setSort("");
				$this->_Email->setSort("");
				$this->Website->setSort("");
				$this->NoAkta->setSort("");
				$this->TglAkta->setSort("");
				$this->NoSah->setSort("");
				$this->TglSah->setSort("");
				$this->Logo->setSort("");
				$this->StartNoIdentitas->setSort("");
				$this->NoIdentitas->setSort("");
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

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;

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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fidentitaslistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fidentitaslistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
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
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fidentitaslist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fidentitaslistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		if (ew_IsMobile())
			$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"identitassrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		else
			$item->Body = "<button type=\"button\" class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-table=\"identitas\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" onclick=\"ew_ModalDialogShow({lnk:this,url:'identitassrch.php',caption:'" . $Language->Phrase("Search") . "'});\">" . $Language->Phrase("AdvancedSearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Search highlight button
		$item = &$this->SearchOptions->Add("searchhighlight");
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewHighlight active\" title=\"" . $Language->Phrase("Highlight") . "\" data-caption=\"" . $Language->Phrase("Highlight") . "\" data-toggle=\"button\" data-form=\"fidentitaslistsrch\" data-name=\"" . $this->HighlightName() . "\">" . $Language->Phrase("HighlightBtn") . "</button>";
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
		// Kode

		$this->Kode->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Kode"]);
		if ($this->Kode->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Kode->AdvancedSearch->SearchOperator = @$_GET["z_Kode"];

		// KodeHukum
		$this->KodeHukum->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KodeHukum"]);
		if ($this->KodeHukum->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KodeHukum->AdvancedSearch->SearchOperator = @$_GET["z_KodeHukum"];

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Nama"]);
		if ($this->Nama->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Nama->AdvancedSearch->SearchOperator = @$_GET["z_Nama"];

		// TglMulai
		$this->TglMulai->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TglMulai"]);
		if ($this->TglMulai->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TglMulai->AdvancedSearch->SearchOperator = @$_GET["z_TglMulai"];

		// Alamat1
		$this->Alamat1->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Alamat1"]);
		if ($this->Alamat1->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Alamat1->AdvancedSearch->SearchOperator = @$_GET["z_Alamat1"];

		// Alamat2
		$this->Alamat2->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Alamat2"]);
		if ($this->Alamat2->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Alamat2->AdvancedSearch->SearchOperator = @$_GET["z_Alamat2"];

		// Kota
		$this->Kota->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Kota"]);
		if ($this->Kota->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Kota->AdvancedSearch->SearchOperator = @$_GET["z_Kota"];

		// KodePos
		$this->KodePos->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KodePos"]);
		if ($this->KodePos->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KodePos->AdvancedSearch->SearchOperator = @$_GET["z_KodePos"];

		// Telepon
		$this->Telepon->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Telepon"]);
		if ($this->Telepon->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Telepon->AdvancedSearch->SearchOperator = @$_GET["z_Telepon"];

		// Fax
		$this->Fax->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Fax"]);
		if ($this->Fax->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Fax->AdvancedSearch->SearchOperator = @$_GET["z_Fax"];

		// Email
		$this->_Email->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x__Email"]);
		if ($this->_Email->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->_Email->AdvancedSearch->SearchOperator = @$_GET["z__Email"];

		// Website
		$this->Website->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Website"]);
		if ($this->Website->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Website->AdvancedSearch->SearchOperator = @$_GET["z_Website"];

		// NoAkta
		$this->NoAkta->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NoAkta"]);
		if ($this->NoAkta->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NoAkta->AdvancedSearch->SearchOperator = @$_GET["z_NoAkta"];

		// TglAkta
		$this->TglAkta->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TglAkta"]);
		if ($this->TglAkta->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TglAkta->AdvancedSearch->SearchOperator = @$_GET["z_TglAkta"];

		// NoSah
		$this->NoSah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NoSah"]);
		if ($this->NoSah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NoSah->AdvancedSearch->SearchOperator = @$_GET["z_NoSah"];

		// TglSah
		$this->TglSah->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TglSah"]);
		if ($this->TglSah->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TglSah->AdvancedSearch->SearchOperator = @$_GET["z_TglSah"];

		// Logo
		$this->Logo->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Logo"]);
		if ($this->Logo->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Logo->AdvancedSearch->SearchOperator = @$_GET["z_Logo"];

		// StartNoIdentitas
		$this->StartNoIdentitas->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_StartNoIdentitas"]);
		if ($this->StartNoIdentitas->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->StartNoIdentitas->AdvancedSearch->SearchOperator = @$_GET["z_StartNoIdentitas"];

		// NoIdentitas
		$this->NoIdentitas->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NoIdentitas"]);
		if ($this->NoIdentitas->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NoIdentitas->AdvancedSearch->SearchOperator = @$_GET["z_NoIdentitas"];

		// NA
		$this->NA->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NA"]);
		if ($this->NA->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NA->AdvancedSearch->SearchOperator = @$_GET["z_NA"];
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
			if ($this->Export == "")
				$this->Kode->ViewValue = ew_Highlight($this->HighlightName(), $this->Kode->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Kode->AdvancedSearch->getValue("x"), "");

			// KodeHukum
			$this->KodeHukum->LinkCustomAttributes = "";
			$this->KodeHukum->HrefValue = "";
			$this->KodeHukum->TooltipValue = "";
			if ($this->Export == "")
				$this->KodeHukum->ViewValue = ew_Highlight($this->HighlightName(), $this->KodeHukum->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->KodeHukum->AdvancedSearch->getValue("x"), "");

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";
			if ($this->Export == "")
				$this->Nama->ViewValue = ew_Highlight($this->HighlightName(), $this->Nama->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Nama->AdvancedSearch->getValue("x"), "");

			// TglMulai
			$this->TglMulai->LinkCustomAttributes = "";
			$this->TglMulai->HrefValue = "";
			$this->TglMulai->TooltipValue = "";

			// Alamat1
			$this->Alamat1->LinkCustomAttributes = "";
			$this->Alamat1->HrefValue = "";
			$this->Alamat1->TooltipValue = "";
			if ($this->Export == "")
				$this->Alamat1->ViewValue = ew_Highlight($this->HighlightName(), $this->Alamat1->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Alamat1->AdvancedSearch->getValue("x"), "");

			// Alamat2
			$this->Alamat2->LinkCustomAttributes = "";
			$this->Alamat2->HrefValue = "";
			$this->Alamat2->TooltipValue = "";
			if ($this->Export == "")
				$this->Alamat2->ViewValue = ew_Highlight($this->HighlightName(), $this->Alamat2->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Alamat2->AdvancedSearch->getValue("x"), "");

			// Kota
			$this->Kota->LinkCustomAttributes = "";
			$this->Kota->HrefValue = "";
			$this->Kota->TooltipValue = "";
			if ($this->Export == "")
				$this->Kota->ViewValue = ew_Highlight($this->HighlightName(), $this->Kota->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Kota->AdvancedSearch->getValue("x"), "");

			// KodePos
			$this->KodePos->LinkCustomAttributes = "";
			$this->KodePos->HrefValue = "";
			$this->KodePos->TooltipValue = "";
			if ($this->Export == "")
				$this->KodePos->ViewValue = ew_Highlight($this->HighlightName(), $this->KodePos->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->KodePos->AdvancedSearch->getValue("x"), "");

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";
			$this->Telepon->TooltipValue = "";
			if ($this->Export == "")
				$this->Telepon->ViewValue = ew_Highlight($this->HighlightName(), $this->Telepon->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Telepon->AdvancedSearch->getValue("x"), "");

			// Fax
			$this->Fax->LinkCustomAttributes = "";
			$this->Fax->HrefValue = "";
			$this->Fax->TooltipValue = "";
			if ($this->Export == "")
				$this->Fax->ViewValue = ew_Highlight($this->HighlightName(), $this->Fax->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Fax->AdvancedSearch->getValue("x"), "");

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";
			if ($this->Export == "")
				$this->_Email->ViewValue = ew_Highlight($this->HighlightName(), $this->_Email->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->_Email->AdvancedSearch->getValue("x"), "");

			// Website
			$this->Website->LinkCustomAttributes = "";
			$this->Website->HrefValue = "";
			$this->Website->TooltipValue = "";
			if ($this->Export == "")
				$this->Website->ViewValue = ew_Highlight($this->HighlightName(), $this->Website->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Website->AdvancedSearch->getValue("x"), "");

			// NoAkta
			$this->NoAkta->LinkCustomAttributes = "";
			$this->NoAkta->HrefValue = "";
			$this->NoAkta->TooltipValue = "";
			if ($this->Export == "")
				$this->NoAkta->ViewValue = ew_Highlight($this->HighlightName(), $this->NoAkta->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->NoAkta->AdvancedSearch->getValue("x"), "");

			// TglAkta
			$this->TglAkta->LinkCustomAttributes = "";
			$this->TglAkta->HrefValue = "";
			$this->TglAkta->TooltipValue = "";

			// NoSah
			$this->NoSah->LinkCustomAttributes = "";
			$this->NoSah->HrefValue = "";
			$this->NoSah->TooltipValue = "";
			if ($this->Export == "")
				$this->NoSah->ViewValue = ew_Highlight($this->HighlightName(), $this->NoSah->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->NoSah->AdvancedSearch->getValue("x"), "");

			// TglSah
			$this->TglSah->LinkCustomAttributes = "";
			$this->TglSah->HrefValue = "";
			$this->TglSah->TooltipValue = "";

			// Logo
			$this->Logo->LinkCustomAttributes = "";
			$this->Logo->HrefValue = "";
			$this->Logo->TooltipValue = "";
			if ($this->Export == "")
				$this->Logo->ViewValue = ew_Highlight($this->HighlightName(), $this->Logo->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Logo->AdvancedSearch->getValue("x"), "");

			// StartNoIdentitas
			$this->StartNoIdentitas->LinkCustomAttributes = "";
			$this->StartNoIdentitas->HrefValue = "";
			$this->StartNoIdentitas->TooltipValue = "";
			if ($this->Export == "")
				$this->StartNoIdentitas->ViewValue = ew_Highlight($this->HighlightName(), $this->StartNoIdentitas->ViewValue, "", "", $this->StartNoIdentitas->AdvancedSearch->getValue("x"), "");

			// NoIdentitas
			$this->NoIdentitas->LinkCustomAttributes = "";
			$this->NoIdentitas->HrefValue = "";
			$this->NoIdentitas->TooltipValue = "";
			if ($this->Export == "")
				$this->NoIdentitas->ViewValue = ew_Highlight($this->HighlightName(), $this->NoIdentitas->ViewValue, "", "", $this->NoIdentitas->AdvancedSearch->getValue("x"), "");

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
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
		$this->Kode->AdvancedSearch->Load();
		$this->KodeHukum->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->TglMulai->AdvancedSearch->Load();
		$this->Alamat1->AdvancedSearch->Load();
		$this->Alamat2->AdvancedSearch->Load();
		$this->Kota->AdvancedSearch->Load();
		$this->KodePos->AdvancedSearch->Load();
		$this->Telepon->AdvancedSearch->Load();
		$this->Fax->AdvancedSearch->Load();
		$this->_Email->AdvancedSearch->Load();
		$this->Website->AdvancedSearch->Load();
		$this->NoAkta->AdvancedSearch->Load();
		$this->TglAkta->AdvancedSearch->Load();
		$this->NoSah->AdvancedSearch->Load();
		$this->TglSah->AdvancedSearch->Load();
		$this->Logo->AdvancedSearch->Load();
		$this->StartNoIdentitas->AdvancedSearch->Load();
		$this->NoIdentitas->AdvancedSearch->Load();
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
		$item->Body = "<button id=\"emf_identitas\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_identitas',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fidentitaslist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
if (!isset($identitas_list)) $identitas_list = new cidentitas_list();

// Page init
$identitas_list->Page_Init();

// Page main
$identitas_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$identitas_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($identitas->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fidentitaslist = new ew_Form("fidentitaslist", "list");
fidentitaslist.FormKeyCountName = '<?php echo $identitas_list->FormKeyCountName ?>';

// Form_CustomValidate event
fidentitaslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fidentitaslist.ValidateRequired = true;
<?php } else { ?>
fidentitaslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fidentitaslist.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fidentitaslist.Lists["x_NA"].Options = <?php echo json_encode($identitas->NA->Options()) ?>;

// Form object for search
var CurrentSearchForm = fidentitaslistsrch = new ew_Form("fidentitaslistsrch");

// Init search panel as collapsed
if (fidentitaslistsrch) fidentitaslistsrch.InitSearchPanel = true;
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
<?php if ($identitas->Export == "") { ?>
<div class="ewToolbar">
<?php if ($identitas->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($identitas_list->TotalRecs > 0 && $identitas_list->ExportOptions->Visible()) { ?>
<?php $identitas_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($identitas_list->SearchOptions->Visible()) { ?>
<?php $identitas_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($identitas_list->FilterOptions->Visible()) { ?>
<?php $identitas_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php if ($identitas->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = $identitas_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($identitas_list->TotalRecs <= 0)
			$identitas_list->TotalRecs = $identitas->SelectRecordCount();
	} else {
		if (!$identitas_list->Recordset && ($identitas_list->Recordset = $identitas_list->LoadRecordset()))
			$identitas_list->TotalRecs = $identitas_list->Recordset->RecordCount();
	}
	$identitas_list->StartRec = 1;
	if ($identitas_list->DisplayRecs <= 0 || ($identitas->Export <> "" && $identitas->ExportAll)) // Display all records
		$identitas_list->DisplayRecs = $identitas_list->TotalRecs;
	if (!($identitas->Export <> "" && $identitas->ExportAll))
		$identitas_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$identitas_list->Recordset = $identitas_list->LoadRecordset($identitas_list->StartRec-1, $identitas_list->DisplayRecs);

	// Set no record found message
	if ($identitas->CurrentAction == "" && $identitas_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$identitas_list->setWarningMessage(ew_DeniedMsg());
		if ($identitas_list->SearchWhere == "0=101")
			$identitas_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$identitas_list->setWarningMessage($Language->Phrase("NoRecord"));
	}

	// Audit trail on search
	if ($identitas_list->AuditTrailOnSearch && $identitas_list->Command == "search" && !$identitas_list->RestoreSearch) {
		$searchparm = ew_ServerVar("QUERY_STRING");
		$searchsql = $identitas_list->getSessionWhere();
		$identitas_list->WriteAuditTrailOnSearch($searchparm, $searchsql);
	}
$identitas_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($identitas->Export == "" && $identitas->CurrentAction == "") { ?>
<form name="fidentitaslistsrch" id="fidentitaslistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($identitas_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fidentitaslistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="identitas">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($identitas_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($identitas_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $identitas_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($identitas_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($identitas_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($identitas_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($identitas_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
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
<?php $identitas_list->ShowPageHeader(); ?>
<?php
$identitas_list->ShowMessage();
?>
<?php if ($identitas_list->TotalRecs > 0 || $identitas->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid identitas">
<?php if ($identitas->Export == "") { ?>
<div class="panel-heading ewGridUpperPanel">
<?php if ($identitas->CurrentAction <> "gridadd" && $identitas->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($identitas_list->Pager)) $identitas_list->Pager = new cPrevNextPager($identitas_list->StartRec, $identitas_list->DisplayRecs, $identitas_list->TotalRecs) ?>
<?php if ($identitas_list->Pager->RecordCount > 0 && $identitas_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($identitas_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $identitas_list->PageUrl() ?>start=<?php echo $identitas_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($identitas_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $identitas_list->PageUrl() ?>start=<?php echo $identitas_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $identitas_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($identitas_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $identitas_list->PageUrl() ?>start=<?php echo $identitas_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($identitas_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $identitas_list->PageUrl() ?>start=<?php echo $identitas_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $identitas_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $identitas_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $identitas_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $identitas_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($identitas_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="fidentitaslist" id="fidentitaslist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($identitas_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $identitas_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="identitas">
<div id="gmp_identitas" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($identitas_list->TotalRecs > 0 || $identitas->CurrentAction == "gridedit") { ?>
<table id="tbl_identitaslist" class="table ewTable">
<?php echo $identitas->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$identitas_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$identitas_list->RenderListOptions();

// Render list options (header, left)
$identitas_list->ListOptions->Render("header", "left");
?>
<?php if ($identitas->Kode->Visible) { // Kode ?>
	<?php if ($identitas->SortUrl($identitas->Kode) == "") { ?>
		<th data-name="Kode"><div id="elh_identitas_Kode" class="identitas_Kode"><div class="ewTableHeaderCaption"><?php echo $identitas->Kode->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Kode"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->Kode) ?>',1);"><div id="elh_identitas_Kode" class="identitas_Kode">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->Kode->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->Kode->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->Kode->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->KodeHukum->Visible) { // KodeHukum ?>
	<?php if ($identitas->SortUrl($identitas->KodeHukum) == "") { ?>
		<th data-name="KodeHukum"><div id="elh_identitas_KodeHukum" class="identitas_KodeHukum"><div class="ewTableHeaderCaption"><?php echo $identitas->KodeHukum->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="KodeHukum"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->KodeHukum) ?>',1);"><div id="elh_identitas_KodeHukum" class="identitas_KodeHukum">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->KodeHukum->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->KodeHukum->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->KodeHukum->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->Nama->Visible) { // Nama ?>
	<?php if ($identitas->SortUrl($identitas->Nama) == "") { ?>
		<th data-name="Nama"><div id="elh_identitas_Nama" class="identitas_Nama"><div class="ewTableHeaderCaption"><?php echo $identitas->Nama->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Nama"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->Nama) ?>',1);"><div id="elh_identitas_Nama" class="identitas_Nama">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->Nama->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->Nama->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->Nama->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->TglMulai->Visible) { // TglMulai ?>
	<?php if ($identitas->SortUrl($identitas->TglMulai) == "") { ?>
		<th data-name="TglMulai"><div id="elh_identitas_TglMulai" class="identitas_TglMulai"><div class="ewTableHeaderCaption"><?php echo $identitas->TglMulai->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TglMulai"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->TglMulai) ?>',1);"><div id="elh_identitas_TglMulai" class="identitas_TglMulai">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->TglMulai->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($identitas->TglMulai->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->TglMulai->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->Alamat1->Visible) { // Alamat1 ?>
	<?php if ($identitas->SortUrl($identitas->Alamat1) == "") { ?>
		<th data-name="Alamat1"><div id="elh_identitas_Alamat1" class="identitas_Alamat1"><div class="ewTableHeaderCaption"><?php echo $identitas->Alamat1->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Alamat1"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->Alamat1) ?>',1);"><div id="elh_identitas_Alamat1" class="identitas_Alamat1">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->Alamat1->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->Alamat1->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->Alamat1->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->Alamat2->Visible) { // Alamat2 ?>
	<?php if ($identitas->SortUrl($identitas->Alamat2) == "") { ?>
		<th data-name="Alamat2"><div id="elh_identitas_Alamat2" class="identitas_Alamat2"><div class="ewTableHeaderCaption"><?php echo $identitas->Alamat2->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Alamat2"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->Alamat2) ?>',1);"><div id="elh_identitas_Alamat2" class="identitas_Alamat2">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->Alamat2->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->Alamat2->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->Alamat2->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->Kota->Visible) { // Kota ?>
	<?php if ($identitas->SortUrl($identitas->Kota) == "") { ?>
		<th data-name="Kota"><div id="elh_identitas_Kota" class="identitas_Kota"><div class="ewTableHeaderCaption"><?php echo $identitas->Kota->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Kota"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->Kota) ?>',1);"><div id="elh_identitas_Kota" class="identitas_Kota">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->Kota->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->Kota->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->Kota->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->KodePos->Visible) { // KodePos ?>
	<?php if ($identitas->SortUrl($identitas->KodePos) == "") { ?>
		<th data-name="KodePos"><div id="elh_identitas_KodePos" class="identitas_KodePos"><div class="ewTableHeaderCaption"><?php echo $identitas->KodePos->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="KodePos"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->KodePos) ?>',1);"><div id="elh_identitas_KodePos" class="identitas_KodePos">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->KodePos->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->KodePos->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->KodePos->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->Telepon->Visible) { // Telepon ?>
	<?php if ($identitas->SortUrl($identitas->Telepon) == "") { ?>
		<th data-name="Telepon"><div id="elh_identitas_Telepon" class="identitas_Telepon"><div class="ewTableHeaderCaption"><?php echo $identitas->Telepon->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Telepon"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->Telepon) ?>',1);"><div id="elh_identitas_Telepon" class="identitas_Telepon">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->Telepon->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->Telepon->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->Telepon->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->Fax->Visible) { // Fax ?>
	<?php if ($identitas->SortUrl($identitas->Fax) == "") { ?>
		<th data-name="Fax"><div id="elh_identitas_Fax" class="identitas_Fax"><div class="ewTableHeaderCaption"><?php echo $identitas->Fax->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Fax"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->Fax) ?>',1);"><div id="elh_identitas_Fax" class="identitas_Fax">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->Fax->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->Fax->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->Fax->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->_Email->Visible) { // Email ?>
	<?php if ($identitas->SortUrl($identitas->_Email) == "") { ?>
		<th data-name="_Email"><div id="elh_identitas__Email" class="identitas__Email"><div class="ewTableHeaderCaption"><?php echo $identitas->_Email->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="_Email"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->_Email) ?>',1);"><div id="elh_identitas__Email" class="identitas__Email">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->_Email->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->_Email->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->_Email->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->Website->Visible) { // Website ?>
	<?php if ($identitas->SortUrl($identitas->Website) == "") { ?>
		<th data-name="Website"><div id="elh_identitas_Website" class="identitas_Website"><div class="ewTableHeaderCaption"><?php echo $identitas->Website->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Website"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->Website) ?>',1);"><div id="elh_identitas_Website" class="identitas_Website">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->Website->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->Website->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->Website->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->NoAkta->Visible) { // NoAkta ?>
	<?php if ($identitas->SortUrl($identitas->NoAkta) == "") { ?>
		<th data-name="NoAkta"><div id="elh_identitas_NoAkta" class="identitas_NoAkta"><div class="ewTableHeaderCaption"><?php echo $identitas->NoAkta->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NoAkta"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->NoAkta) ?>',1);"><div id="elh_identitas_NoAkta" class="identitas_NoAkta">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->NoAkta->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->NoAkta->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->NoAkta->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->TglAkta->Visible) { // TglAkta ?>
	<?php if ($identitas->SortUrl($identitas->TglAkta) == "") { ?>
		<th data-name="TglAkta"><div id="elh_identitas_TglAkta" class="identitas_TglAkta"><div class="ewTableHeaderCaption"><?php echo $identitas->TglAkta->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TglAkta"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->TglAkta) ?>',1);"><div id="elh_identitas_TglAkta" class="identitas_TglAkta">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->TglAkta->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($identitas->TglAkta->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->TglAkta->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->NoSah->Visible) { // NoSah ?>
	<?php if ($identitas->SortUrl($identitas->NoSah) == "") { ?>
		<th data-name="NoSah"><div id="elh_identitas_NoSah" class="identitas_NoSah"><div class="ewTableHeaderCaption"><?php echo $identitas->NoSah->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NoSah"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->NoSah) ?>',1);"><div id="elh_identitas_NoSah" class="identitas_NoSah">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->NoSah->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->NoSah->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->NoSah->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->TglSah->Visible) { // TglSah ?>
	<?php if ($identitas->SortUrl($identitas->TglSah) == "") { ?>
		<th data-name="TglSah"><div id="elh_identitas_TglSah" class="identitas_TglSah"><div class="ewTableHeaderCaption"><?php echo $identitas->TglSah->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TglSah"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->TglSah) ?>',1);"><div id="elh_identitas_TglSah" class="identitas_TglSah">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->TglSah->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($identitas->TglSah->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->TglSah->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->Logo->Visible) { // Logo ?>
	<?php if ($identitas->SortUrl($identitas->Logo) == "") { ?>
		<th data-name="Logo"><div id="elh_identitas_Logo" class="identitas_Logo"><div class="ewTableHeaderCaption"><?php echo $identitas->Logo->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Logo"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->Logo) ?>',1);"><div id="elh_identitas_Logo" class="identitas_Logo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->Logo->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($identitas->Logo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->Logo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->StartNoIdentitas->Visible) { // StartNoIdentitas ?>
	<?php if ($identitas->SortUrl($identitas->StartNoIdentitas) == "") { ?>
		<th data-name="StartNoIdentitas"><div id="elh_identitas_StartNoIdentitas" class="identitas_StartNoIdentitas"><div class="ewTableHeaderCaption"><?php echo $identitas->StartNoIdentitas->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="StartNoIdentitas"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->StartNoIdentitas) ?>',1);"><div id="elh_identitas_StartNoIdentitas" class="identitas_StartNoIdentitas">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->StartNoIdentitas->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($identitas->StartNoIdentitas->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->StartNoIdentitas->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->NoIdentitas->Visible) { // NoIdentitas ?>
	<?php if ($identitas->SortUrl($identitas->NoIdentitas) == "") { ?>
		<th data-name="NoIdentitas"><div id="elh_identitas_NoIdentitas" class="identitas_NoIdentitas"><div class="ewTableHeaderCaption"><?php echo $identitas->NoIdentitas->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NoIdentitas"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->NoIdentitas) ?>',1);"><div id="elh_identitas_NoIdentitas" class="identitas_NoIdentitas">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->NoIdentitas->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($identitas->NoIdentitas->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->NoIdentitas->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($identitas->NA->Visible) { // NA ?>
	<?php if ($identitas->SortUrl($identitas->NA) == "") { ?>
		<th data-name="NA"><div id="elh_identitas_NA" class="identitas_NA"><div class="ewTableHeaderCaption"><?php echo $identitas->NA->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NA"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $identitas->SortUrl($identitas->NA) ?>',1);"><div id="elh_identitas_NA" class="identitas_NA">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $identitas->NA->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($identitas->NA->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($identitas->NA->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$identitas_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($identitas->ExportAll && $identitas->Export <> "") {
	$identitas_list->StopRec = $identitas_list->TotalRecs;
} else {

	// Set the last record to display
	if ($identitas_list->TotalRecs > $identitas_list->StartRec + $identitas_list->DisplayRecs - 1)
		$identitas_list->StopRec = $identitas_list->StartRec + $identitas_list->DisplayRecs - 1;
	else
		$identitas_list->StopRec = $identitas_list->TotalRecs;
}
$identitas_list->RecCnt = $identitas_list->StartRec - 1;
if ($identitas_list->Recordset && !$identitas_list->Recordset->EOF) {
	$identitas_list->Recordset->MoveFirst();
	$bSelectLimit = $identitas_list->UseSelectLimit;
	if (!$bSelectLimit && $identitas_list->StartRec > 1)
		$identitas_list->Recordset->Move($identitas_list->StartRec - 1);
} elseif (!$identitas->AllowAddDeleteRow && $identitas_list->StopRec == 0) {
	$identitas_list->StopRec = $identitas->GridAddRowCount;
}

// Initialize aggregate
$identitas->RowType = EW_ROWTYPE_AGGREGATEINIT;
$identitas->ResetAttrs();
$identitas_list->RenderRow();
while ($identitas_list->RecCnt < $identitas_list->StopRec) {
	$identitas_list->RecCnt++;
	if (intval($identitas_list->RecCnt) >= intval($identitas_list->StartRec)) {
		$identitas_list->RowCnt++;

		// Set up key count
		$identitas_list->KeyCount = $identitas_list->RowIndex;

		// Init row class and style
		$identitas->ResetAttrs();
		$identitas->CssClass = "";
		if ($identitas->CurrentAction == "gridadd") {
		} else {
			$identitas_list->LoadRowValues($identitas_list->Recordset); // Load row values
		}
		$identitas->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$identitas->RowAttrs = array_merge($identitas->RowAttrs, array('data-rowindex'=>$identitas_list->RowCnt, 'id'=>'r' . $identitas_list->RowCnt . '_identitas', 'data-rowtype'=>$identitas->RowType));

		// Render row
		$identitas_list->RenderRow();

		// Render list options
		$identitas_list->RenderListOptions();
?>
	<tr<?php echo $identitas->RowAttributes() ?>>
<?php

// Render list options (body, left)
$identitas_list->ListOptions->Render("body", "left", $identitas_list->RowCnt);
?>
	<?php if ($identitas->Kode->Visible) { // Kode ?>
		<td data-name="Kode"<?php echo $identitas->Kode->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_Kode" class="identitas_Kode">
<span<?php echo $identitas->Kode->ViewAttributes() ?>>
<?php echo $identitas->Kode->ListViewValue() ?></span>
</span>
<a id="<?php echo $identitas_list->PageObjName . "_row_" . $identitas_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($identitas->KodeHukum->Visible) { // KodeHukum ?>
		<td data-name="KodeHukum"<?php echo $identitas->KodeHukum->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_KodeHukum" class="identitas_KodeHukum">
<span<?php echo $identitas->KodeHukum->ViewAttributes() ?>>
<?php echo $identitas->KodeHukum->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->Nama->Visible) { // Nama ?>
		<td data-name="Nama"<?php echo $identitas->Nama->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_Nama" class="identitas_Nama">
<span<?php echo $identitas->Nama->ViewAttributes() ?>>
<?php echo $identitas->Nama->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->TglMulai->Visible) { // TglMulai ?>
		<td data-name="TglMulai"<?php echo $identitas->TglMulai->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_TglMulai" class="identitas_TglMulai">
<span<?php echo $identitas->TglMulai->ViewAttributes() ?>>
<?php echo $identitas->TglMulai->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->Alamat1->Visible) { // Alamat1 ?>
		<td data-name="Alamat1"<?php echo $identitas->Alamat1->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_Alamat1" class="identitas_Alamat1">
<span<?php echo $identitas->Alamat1->ViewAttributes() ?>>
<?php echo $identitas->Alamat1->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->Alamat2->Visible) { // Alamat2 ?>
		<td data-name="Alamat2"<?php echo $identitas->Alamat2->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_Alamat2" class="identitas_Alamat2">
<span<?php echo $identitas->Alamat2->ViewAttributes() ?>>
<?php echo $identitas->Alamat2->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->Kota->Visible) { // Kota ?>
		<td data-name="Kota"<?php echo $identitas->Kota->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_Kota" class="identitas_Kota">
<span<?php echo $identitas->Kota->ViewAttributes() ?>>
<?php echo $identitas->Kota->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->KodePos->Visible) { // KodePos ?>
		<td data-name="KodePos"<?php echo $identitas->KodePos->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_KodePos" class="identitas_KodePos">
<span<?php echo $identitas->KodePos->ViewAttributes() ?>>
<?php echo $identitas->KodePos->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->Telepon->Visible) { // Telepon ?>
		<td data-name="Telepon"<?php echo $identitas->Telepon->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_Telepon" class="identitas_Telepon">
<span<?php echo $identitas->Telepon->ViewAttributes() ?>>
<?php echo $identitas->Telepon->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->Fax->Visible) { // Fax ?>
		<td data-name="Fax"<?php echo $identitas->Fax->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_Fax" class="identitas_Fax">
<span<?php echo $identitas->Fax->ViewAttributes() ?>>
<?php echo $identitas->Fax->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->_Email->Visible) { // Email ?>
		<td data-name="_Email"<?php echo $identitas->_Email->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas__Email" class="identitas__Email">
<span<?php echo $identitas->_Email->ViewAttributes() ?>>
<?php echo $identitas->_Email->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->Website->Visible) { // Website ?>
		<td data-name="Website"<?php echo $identitas->Website->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_Website" class="identitas_Website">
<span<?php echo $identitas->Website->ViewAttributes() ?>>
<?php echo $identitas->Website->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->NoAkta->Visible) { // NoAkta ?>
		<td data-name="NoAkta"<?php echo $identitas->NoAkta->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_NoAkta" class="identitas_NoAkta">
<span<?php echo $identitas->NoAkta->ViewAttributes() ?>>
<?php echo $identitas->NoAkta->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->TglAkta->Visible) { // TglAkta ?>
		<td data-name="TglAkta"<?php echo $identitas->TglAkta->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_TglAkta" class="identitas_TglAkta">
<span<?php echo $identitas->TglAkta->ViewAttributes() ?>>
<?php echo $identitas->TglAkta->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->NoSah->Visible) { // NoSah ?>
		<td data-name="NoSah"<?php echo $identitas->NoSah->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_NoSah" class="identitas_NoSah">
<span<?php echo $identitas->NoSah->ViewAttributes() ?>>
<?php echo $identitas->NoSah->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->TglSah->Visible) { // TglSah ?>
		<td data-name="TglSah"<?php echo $identitas->TglSah->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_TglSah" class="identitas_TglSah">
<span<?php echo $identitas->TglSah->ViewAttributes() ?>>
<?php echo $identitas->TglSah->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->Logo->Visible) { // Logo ?>
		<td data-name="Logo"<?php echo $identitas->Logo->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_Logo" class="identitas_Logo">
<span<?php echo $identitas->Logo->ViewAttributes() ?>>
<?php echo $identitas->Logo->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->StartNoIdentitas->Visible) { // StartNoIdentitas ?>
		<td data-name="StartNoIdentitas"<?php echo $identitas->StartNoIdentitas->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_StartNoIdentitas" class="identitas_StartNoIdentitas">
<span<?php echo $identitas->StartNoIdentitas->ViewAttributes() ?>>
<?php echo $identitas->StartNoIdentitas->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->NoIdentitas->Visible) { // NoIdentitas ?>
		<td data-name="NoIdentitas"<?php echo $identitas->NoIdentitas->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_NoIdentitas" class="identitas_NoIdentitas">
<span<?php echo $identitas->NoIdentitas->ViewAttributes() ?>>
<?php echo $identitas->NoIdentitas->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($identitas->NA->Visible) { // NA ?>
		<td data-name="NA"<?php echo $identitas->NA->CellAttributes() ?>>
<span id="el<?php echo $identitas_list->RowCnt ?>_identitas_NA" class="identitas_NA">
<span<?php echo $identitas->NA->ViewAttributes() ?>>
<?php echo $identitas->NA->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$identitas_list->ListOptions->Render("body", "right", $identitas_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($identitas->CurrentAction <> "gridadd")
		$identitas_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($identitas->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($identitas_list->Recordset)
	$identitas_list->Recordset->Close();
?>
<?php if ($identitas->Export == "") { ?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($identitas->CurrentAction <> "gridadd" && $identitas->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($identitas_list->Pager)) $identitas_list->Pager = new cPrevNextPager($identitas_list->StartRec, $identitas_list->DisplayRecs, $identitas_list->TotalRecs) ?>
<?php if ($identitas_list->Pager->RecordCount > 0 && $identitas_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($identitas_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $identitas_list->PageUrl() ?>start=<?php echo $identitas_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($identitas_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $identitas_list->PageUrl() ?>start=<?php echo $identitas_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $identitas_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($identitas_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $identitas_list->PageUrl() ?>start=<?php echo $identitas_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($identitas_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $identitas_list->PageUrl() ?>start=<?php echo $identitas_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $identitas_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $identitas_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $identitas_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $identitas_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($identitas_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($identitas_list->TotalRecs == 0 && $identitas->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($identitas_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($identitas->Export == "") { ?>
<script type="text/javascript">
fidentitaslistsrch.FilterList = <?php echo $identitas_list->GetFilterList() ?>;
fidentitaslistsrch.Init();
fidentitaslist.Init();
</script>
<?php } ?>
<?php
$identitas_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($identitas->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$identitas_list->Page_Terminate();
?>
