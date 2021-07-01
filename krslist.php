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

$krs_list = NULL; // Initialize page object first

class ckrs_list extends ckrs {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'krs';

	// Page object name
	var $PageObjName = 'krs_list';

	// Grid form hidden field names
	var $FormName = 'fkrslist';
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

		// Table object (krs)
		if (!isset($GLOBALS["krs"]) || get_class($GLOBALS["krs"]) == "ckrs") {
			$GLOBALS["krs"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["krs"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "krsadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "krsdelete.php";
		$this->MultiUpdateUrl = "krsupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fkrslistsrch";

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
		$this->KRSID->SetVisibility();
		$this->KRSID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
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
			$this->KRSID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->KRSID->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Get list of filters
	function GetFilterList() {
		global $UserProfile;

		// Load server side filters
		if (EW_SEARCH_FILTER_OPTION == "Server") {
			$sSavedFilterList = isset($UserProfile) ? $UserProfile->GetSearchFilters(CurrentUserName(), "fkrslistsrch") : "";
		} else {
			$sSavedFilterList = "";
		}

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->KRSID->AdvancedSearch->ToJSON(), ","); // Field KRSID
		$sFilterList = ew_Concat($sFilterList, $this->KHSID->AdvancedSearch->ToJSON(), ","); // Field KHSID
		$sFilterList = ew_Concat($sFilterList, $this->StudentID->AdvancedSearch->ToJSON(), ","); // Field StudentID
		$sFilterList = ew_Concat($sFilterList, $this->TahunID->AdvancedSearch->ToJSON(), ","); // Field TahunID
		$sFilterList = ew_Concat($sFilterList, $this->Sesi->AdvancedSearch->ToJSON(), ","); // Field Sesi
		$sFilterList = ew_Concat($sFilterList, $this->JadwalID->AdvancedSearch->ToJSON(), ","); // Field JadwalID
		$sFilterList = ew_Concat($sFilterList, $this->MKID->AdvancedSearch->ToJSON(), ","); // Field MKID
		$sFilterList = ew_Concat($sFilterList, $this->MKKode->AdvancedSearch->ToJSON(), ","); // Field MKKode
		$sFilterList = ew_Concat($sFilterList, $this->SKS->AdvancedSearch->ToJSON(), ","); // Field SKS
		$sFilterList = ew_Concat($sFilterList, $this->Tugas1->AdvancedSearch->ToJSON(), ","); // Field Tugas1
		$sFilterList = ew_Concat($sFilterList, $this->Tugas2->AdvancedSearch->ToJSON(), ","); // Field Tugas2
		$sFilterList = ew_Concat($sFilterList, $this->Tugas3->AdvancedSearch->ToJSON(), ","); // Field Tugas3
		$sFilterList = ew_Concat($sFilterList, $this->Tugas4->AdvancedSearch->ToJSON(), ","); // Field Tugas4
		$sFilterList = ew_Concat($sFilterList, $this->Tugas5->AdvancedSearch->ToJSON(), ","); // Field Tugas5
		$sFilterList = ew_Concat($sFilterList, $this->Presensi->AdvancedSearch->ToJSON(), ","); // Field Presensi
		$sFilterList = ew_Concat($sFilterList, $this->_Presensi->AdvancedSearch->ToJSON(), ","); // Field _Presensi
		$sFilterList = ew_Concat($sFilterList, $this->UTS->AdvancedSearch->ToJSON(), ","); // Field UTS
		$sFilterList = ew_Concat($sFilterList, $this->UAS->AdvancedSearch->ToJSON(), ","); // Field UAS
		$sFilterList = ew_Concat($sFilterList, $this->Responsi->AdvancedSearch->ToJSON(), ","); // Field Responsi
		$sFilterList = ew_Concat($sFilterList, $this->NilaiAkhir->AdvancedSearch->ToJSON(), ","); // Field NilaiAkhir
		$sFilterList = ew_Concat($sFilterList, $this->GradeNilai->AdvancedSearch->ToJSON(), ","); // Field GradeNilai
		$sFilterList = ew_Concat($sFilterList, $this->BobotNilai->AdvancedSearch->ToJSON(), ","); // Field BobotNilai
		$sFilterList = ew_Concat($sFilterList, $this->StatusKRSID->AdvancedSearch->ToJSON(), ","); // Field StatusKRSID
		$sFilterList = ew_Concat($sFilterList, $this->Tinggi->AdvancedSearch->ToJSON(), ","); // Field Tinggi
		$sFilterList = ew_Concat($sFilterList, $this->Final->AdvancedSearch->ToJSON(), ","); // Field Final
		$sFilterList = ew_Concat($sFilterList, $this->Setara->AdvancedSearch->ToJSON(), ","); // Field Setara
		$sFilterList = ew_Concat($sFilterList, $this->Creator->AdvancedSearch->ToJSON(), ","); // Field Creator
		$sFilterList = ew_Concat($sFilterList, $this->CreateDate->AdvancedSearch->ToJSON(), ","); // Field CreateDate
		$sFilterList = ew_Concat($sFilterList, $this->Editor->AdvancedSearch->ToJSON(), ","); // Field Editor
		$sFilterList = ew_Concat($sFilterList, $this->EditDate->AdvancedSearch->ToJSON(), ","); // Field EditDate
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
			$UserProfile->SetSearchFilters(CurrentUserName(), "fkrslistsrch", $filters);

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

		// Field KRSID
		$this->KRSID->AdvancedSearch->SearchValue = @$filter["x_KRSID"];
		$this->KRSID->AdvancedSearch->SearchOperator = @$filter["z_KRSID"];
		$this->KRSID->AdvancedSearch->SearchCondition = @$filter["v_KRSID"];
		$this->KRSID->AdvancedSearch->SearchValue2 = @$filter["y_KRSID"];
		$this->KRSID->AdvancedSearch->SearchOperator2 = @$filter["w_KRSID"];
		$this->KRSID->AdvancedSearch->Save();

		// Field KHSID
		$this->KHSID->AdvancedSearch->SearchValue = @$filter["x_KHSID"];
		$this->KHSID->AdvancedSearch->SearchOperator = @$filter["z_KHSID"];
		$this->KHSID->AdvancedSearch->SearchCondition = @$filter["v_KHSID"];
		$this->KHSID->AdvancedSearch->SearchValue2 = @$filter["y_KHSID"];
		$this->KHSID->AdvancedSearch->SearchOperator2 = @$filter["w_KHSID"];
		$this->KHSID->AdvancedSearch->Save();

		// Field StudentID
		$this->StudentID->AdvancedSearch->SearchValue = @$filter["x_StudentID"];
		$this->StudentID->AdvancedSearch->SearchOperator = @$filter["z_StudentID"];
		$this->StudentID->AdvancedSearch->SearchCondition = @$filter["v_StudentID"];
		$this->StudentID->AdvancedSearch->SearchValue2 = @$filter["y_StudentID"];
		$this->StudentID->AdvancedSearch->SearchOperator2 = @$filter["w_StudentID"];
		$this->StudentID->AdvancedSearch->Save();

		// Field TahunID
		$this->TahunID->AdvancedSearch->SearchValue = @$filter["x_TahunID"];
		$this->TahunID->AdvancedSearch->SearchOperator = @$filter["z_TahunID"];
		$this->TahunID->AdvancedSearch->SearchCondition = @$filter["v_TahunID"];
		$this->TahunID->AdvancedSearch->SearchValue2 = @$filter["y_TahunID"];
		$this->TahunID->AdvancedSearch->SearchOperator2 = @$filter["w_TahunID"];
		$this->TahunID->AdvancedSearch->Save();

		// Field Sesi
		$this->Sesi->AdvancedSearch->SearchValue = @$filter["x_Sesi"];
		$this->Sesi->AdvancedSearch->SearchOperator = @$filter["z_Sesi"];
		$this->Sesi->AdvancedSearch->SearchCondition = @$filter["v_Sesi"];
		$this->Sesi->AdvancedSearch->SearchValue2 = @$filter["y_Sesi"];
		$this->Sesi->AdvancedSearch->SearchOperator2 = @$filter["w_Sesi"];
		$this->Sesi->AdvancedSearch->Save();

		// Field JadwalID
		$this->JadwalID->AdvancedSearch->SearchValue = @$filter["x_JadwalID"];
		$this->JadwalID->AdvancedSearch->SearchOperator = @$filter["z_JadwalID"];
		$this->JadwalID->AdvancedSearch->SearchCondition = @$filter["v_JadwalID"];
		$this->JadwalID->AdvancedSearch->SearchValue2 = @$filter["y_JadwalID"];
		$this->JadwalID->AdvancedSearch->SearchOperator2 = @$filter["w_JadwalID"];
		$this->JadwalID->AdvancedSearch->Save();

		// Field MKID
		$this->MKID->AdvancedSearch->SearchValue = @$filter["x_MKID"];
		$this->MKID->AdvancedSearch->SearchOperator = @$filter["z_MKID"];
		$this->MKID->AdvancedSearch->SearchCondition = @$filter["v_MKID"];
		$this->MKID->AdvancedSearch->SearchValue2 = @$filter["y_MKID"];
		$this->MKID->AdvancedSearch->SearchOperator2 = @$filter["w_MKID"];
		$this->MKID->AdvancedSearch->Save();

		// Field MKKode
		$this->MKKode->AdvancedSearch->SearchValue = @$filter["x_MKKode"];
		$this->MKKode->AdvancedSearch->SearchOperator = @$filter["z_MKKode"];
		$this->MKKode->AdvancedSearch->SearchCondition = @$filter["v_MKKode"];
		$this->MKKode->AdvancedSearch->SearchValue2 = @$filter["y_MKKode"];
		$this->MKKode->AdvancedSearch->SearchOperator2 = @$filter["w_MKKode"];
		$this->MKKode->AdvancedSearch->Save();

		// Field SKS
		$this->SKS->AdvancedSearch->SearchValue = @$filter["x_SKS"];
		$this->SKS->AdvancedSearch->SearchOperator = @$filter["z_SKS"];
		$this->SKS->AdvancedSearch->SearchCondition = @$filter["v_SKS"];
		$this->SKS->AdvancedSearch->SearchValue2 = @$filter["y_SKS"];
		$this->SKS->AdvancedSearch->SearchOperator2 = @$filter["w_SKS"];
		$this->SKS->AdvancedSearch->Save();

		// Field Tugas1
		$this->Tugas1->AdvancedSearch->SearchValue = @$filter["x_Tugas1"];
		$this->Tugas1->AdvancedSearch->SearchOperator = @$filter["z_Tugas1"];
		$this->Tugas1->AdvancedSearch->SearchCondition = @$filter["v_Tugas1"];
		$this->Tugas1->AdvancedSearch->SearchValue2 = @$filter["y_Tugas1"];
		$this->Tugas1->AdvancedSearch->SearchOperator2 = @$filter["w_Tugas1"];
		$this->Tugas1->AdvancedSearch->Save();

		// Field Tugas2
		$this->Tugas2->AdvancedSearch->SearchValue = @$filter["x_Tugas2"];
		$this->Tugas2->AdvancedSearch->SearchOperator = @$filter["z_Tugas2"];
		$this->Tugas2->AdvancedSearch->SearchCondition = @$filter["v_Tugas2"];
		$this->Tugas2->AdvancedSearch->SearchValue2 = @$filter["y_Tugas2"];
		$this->Tugas2->AdvancedSearch->SearchOperator2 = @$filter["w_Tugas2"];
		$this->Tugas2->AdvancedSearch->Save();

		// Field Tugas3
		$this->Tugas3->AdvancedSearch->SearchValue = @$filter["x_Tugas3"];
		$this->Tugas3->AdvancedSearch->SearchOperator = @$filter["z_Tugas3"];
		$this->Tugas3->AdvancedSearch->SearchCondition = @$filter["v_Tugas3"];
		$this->Tugas3->AdvancedSearch->SearchValue2 = @$filter["y_Tugas3"];
		$this->Tugas3->AdvancedSearch->SearchOperator2 = @$filter["w_Tugas3"];
		$this->Tugas3->AdvancedSearch->Save();

		// Field Tugas4
		$this->Tugas4->AdvancedSearch->SearchValue = @$filter["x_Tugas4"];
		$this->Tugas4->AdvancedSearch->SearchOperator = @$filter["z_Tugas4"];
		$this->Tugas4->AdvancedSearch->SearchCondition = @$filter["v_Tugas4"];
		$this->Tugas4->AdvancedSearch->SearchValue2 = @$filter["y_Tugas4"];
		$this->Tugas4->AdvancedSearch->SearchOperator2 = @$filter["w_Tugas4"];
		$this->Tugas4->AdvancedSearch->Save();

		// Field Tugas5
		$this->Tugas5->AdvancedSearch->SearchValue = @$filter["x_Tugas5"];
		$this->Tugas5->AdvancedSearch->SearchOperator = @$filter["z_Tugas5"];
		$this->Tugas5->AdvancedSearch->SearchCondition = @$filter["v_Tugas5"];
		$this->Tugas5->AdvancedSearch->SearchValue2 = @$filter["y_Tugas5"];
		$this->Tugas5->AdvancedSearch->SearchOperator2 = @$filter["w_Tugas5"];
		$this->Tugas5->AdvancedSearch->Save();

		// Field Presensi
		$this->Presensi->AdvancedSearch->SearchValue = @$filter["x_Presensi"];
		$this->Presensi->AdvancedSearch->SearchOperator = @$filter["z_Presensi"];
		$this->Presensi->AdvancedSearch->SearchCondition = @$filter["v_Presensi"];
		$this->Presensi->AdvancedSearch->SearchValue2 = @$filter["y_Presensi"];
		$this->Presensi->AdvancedSearch->SearchOperator2 = @$filter["w_Presensi"];
		$this->Presensi->AdvancedSearch->Save();

		// Field _Presensi
		$this->_Presensi->AdvancedSearch->SearchValue = @$filter["x__Presensi"];
		$this->_Presensi->AdvancedSearch->SearchOperator = @$filter["z__Presensi"];
		$this->_Presensi->AdvancedSearch->SearchCondition = @$filter["v__Presensi"];
		$this->_Presensi->AdvancedSearch->SearchValue2 = @$filter["y__Presensi"];
		$this->_Presensi->AdvancedSearch->SearchOperator2 = @$filter["w__Presensi"];
		$this->_Presensi->AdvancedSearch->Save();

		// Field UTS
		$this->UTS->AdvancedSearch->SearchValue = @$filter["x_UTS"];
		$this->UTS->AdvancedSearch->SearchOperator = @$filter["z_UTS"];
		$this->UTS->AdvancedSearch->SearchCondition = @$filter["v_UTS"];
		$this->UTS->AdvancedSearch->SearchValue2 = @$filter["y_UTS"];
		$this->UTS->AdvancedSearch->SearchOperator2 = @$filter["w_UTS"];
		$this->UTS->AdvancedSearch->Save();

		// Field UAS
		$this->UAS->AdvancedSearch->SearchValue = @$filter["x_UAS"];
		$this->UAS->AdvancedSearch->SearchOperator = @$filter["z_UAS"];
		$this->UAS->AdvancedSearch->SearchCondition = @$filter["v_UAS"];
		$this->UAS->AdvancedSearch->SearchValue2 = @$filter["y_UAS"];
		$this->UAS->AdvancedSearch->SearchOperator2 = @$filter["w_UAS"];
		$this->UAS->AdvancedSearch->Save();

		// Field Responsi
		$this->Responsi->AdvancedSearch->SearchValue = @$filter["x_Responsi"];
		$this->Responsi->AdvancedSearch->SearchOperator = @$filter["z_Responsi"];
		$this->Responsi->AdvancedSearch->SearchCondition = @$filter["v_Responsi"];
		$this->Responsi->AdvancedSearch->SearchValue2 = @$filter["y_Responsi"];
		$this->Responsi->AdvancedSearch->SearchOperator2 = @$filter["w_Responsi"];
		$this->Responsi->AdvancedSearch->Save();

		// Field NilaiAkhir
		$this->NilaiAkhir->AdvancedSearch->SearchValue = @$filter["x_NilaiAkhir"];
		$this->NilaiAkhir->AdvancedSearch->SearchOperator = @$filter["z_NilaiAkhir"];
		$this->NilaiAkhir->AdvancedSearch->SearchCondition = @$filter["v_NilaiAkhir"];
		$this->NilaiAkhir->AdvancedSearch->SearchValue2 = @$filter["y_NilaiAkhir"];
		$this->NilaiAkhir->AdvancedSearch->SearchOperator2 = @$filter["w_NilaiAkhir"];
		$this->NilaiAkhir->AdvancedSearch->Save();

		// Field GradeNilai
		$this->GradeNilai->AdvancedSearch->SearchValue = @$filter["x_GradeNilai"];
		$this->GradeNilai->AdvancedSearch->SearchOperator = @$filter["z_GradeNilai"];
		$this->GradeNilai->AdvancedSearch->SearchCondition = @$filter["v_GradeNilai"];
		$this->GradeNilai->AdvancedSearch->SearchValue2 = @$filter["y_GradeNilai"];
		$this->GradeNilai->AdvancedSearch->SearchOperator2 = @$filter["w_GradeNilai"];
		$this->GradeNilai->AdvancedSearch->Save();

		// Field BobotNilai
		$this->BobotNilai->AdvancedSearch->SearchValue = @$filter["x_BobotNilai"];
		$this->BobotNilai->AdvancedSearch->SearchOperator = @$filter["z_BobotNilai"];
		$this->BobotNilai->AdvancedSearch->SearchCondition = @$filter["v_BobotNilai"];
		$this->BobotNilai->AdvancedSearch->SearchValue2 = @$filter["y_BobotNilai"];
		$this->BobotNilai->AdvancedSearch->SearchOperator2 = @$filter["w_BobotNilai"];
		$this->BobotNilai->AdvancedSearch->Save();

		// Field StatusKRSID
		$this->StatusKRSID->AdvancedSearch->SearchValue = @$filter["x_StatusKRSID"];
		$this->StatusKRSID->AdvancedSearch->SearchOperator = @$filter["z_StatusKRSID"];
		$this->StatusKRSID->AdvancedSearch->SearchCondition = @$filter["v_StatusKRSID"];
		$this->StatusKRSID->AdvancedSearch->SearchValue2 = @$filter["y_StatusKRSID"];
		$this->StatusKRSID->AdvancedSearch->SearchOperator2 = @$filter["w_StatusKRSID"];
		$this->StatusKRSID->AdvancedSearch->Save();

		// Field Tinggi
		$this->Tinggi->AdvancedSearch->SearchValue = @$filter["x_Tinggi"];
		$this->Tinggi->AdvancedSearch->SearchOperator = @$filter["z_Tinggi"];
		$this->Tinggi->AdvancedSearch->SearchCondition = @$filter["v_Tinggi"];
		$this->Tinggi->AdvancedSearch->SearchValue2 = @$filter["y_Tinggi"];
		$this->Tinggi->AdvancedSearch->SearchOperator2 = @$filter["w_Tinggi"];
		$this->Tinggi->AdvancedSearch->Save();

		// Field Final
		$this->Final->AdvancedSearch->SearchValue = @$filter["x_Final"];
		$this->Final->AdvancedSearch->SearchOperator = @$filter["z_Final"];
		$this->Final->AdvancedSearch->SearchCondition = @$filter["v_Final"];
		$this->Final->AdvancedSearch->SearchValue2 = @$filter["y_Final"];
		$this->Final->AdvancedSearch->SearchOperator2 = @$filter["w_Final"];
		$this->Final->AdvancedSearch->Save();

		// Field Setara
		$this->Setara->AdvancedSearch->SearchValue = @$filter["x_Setara"];
		$this->Setara->AdvancedSearch->SearchOperator = @$filter["z_Setara"];
		$this->Setara->AdvancedSearch->SearchCondition = @$filter["v_Setara"];
		$this->Setara->AdvancedSearch->SearchValue2 = @$filter["y_Setara"];
		$this->Setara->AdvancedSearch->SearchOperator2 = @$filter["w_Setara"];
		$this->Setara->AdvancedSearch->Save();

		// Field Creator
		$this->Creator->AdvancedSearch->SearchValue = @$filter["x_Creator"];
		$this->Creator->AdvancedSearch->SearchOperator = @$filter["z_Creator"];
		$this->Creator->AdvancedSearch->SearchCondition = @$filter["v_Creator"];
		$this->Creator->AdvancedSearch->SearchValue2 = @$filter["y_Creator"];
		$this->Creator->AdvancedSearch->SearchOperator2 = @$filter["w_Creator"];
		$this->Creator->AdvancedSearch->Save();

		// Field CreateDate
		$this->CreateDate->AdvancedSearch->SearchValue = @$filter["x_CreateDate"];
		$this->CreateDate->AdvancedSearch->SearchOperator = @$filter["z_CreateDate"];
		$this->CreateDate->AdvancedSearch->SearchCondition = @$filter["v_CreateDate"];
		$this->CreateDate->AdvancedSearch->SearchValue2 = @$filter["y_CreateDate"];
		$this->CreateDate->AdvancedSearch->SearchOperator2 = @$filter["w_CreateDate"];
		$this->CreateDate->AdvancedSearch->Save();

		// Field Editor
		$this->Editor->AdvancedSearch->SearchValue = @$filter["x_Editor"];
		$this->Editor->AdvancedSearch->SearchOperator = @$filter["z_Editor"];
		$this->Editor->AdvancedSearch->SearchCondition = @$filter["v_Editor"];
		$this->Editor->AdvancedSearch->SearchValue2 = @$filter["y_Editor"];
		$this->Editor->AdvancedSearch->SearchOperator2 = @$filter["w_Editor"];
		$this->Editor->AdvancedSearch->Save();

		// Field EditDate
		$this->EditDate->AdvancedSearch->SearchValue = @$filter["x_EditDate"];
		$this->EditDate->AdvancedSearch->SearchOperator = @$filter["z_EditDate"];
		$this->EditDate->AdvancedSearch->SearchCondition = @$filter["v_EditDate"];
		$this->EditDate->AdvancedSearch->SearchValue2 = @$filter["y_EditDate"];
		$this->EditDate->AdvancedSearch->SearchOperator2 = @$filter["w_EditDate"];
		$this->EditDate->AdvancedSearch->Save();

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
		$this->BuildSearchSql($sWhere, $this->KRSID, $Default, FALSE); // KRSID
		$this->BuildSearchSql($sWhere, $this->KHSID, $Default, FALSE); // KHSID
		$this->BuildSearchSql($sWhere, $this->StudentID, $Default, FALSE); // StudentID
		$this->BuildSearchSql($sWhere, $this->TahunID, $Default, FALSE); // TahunID
		$this->BuildSearchSql($sWhere, $this->Sesi, $Default, FALSE); // Sesi
		$this->BuildSearchSql($sWhere, $this->JadwalID, $Default, FALSE); // JadwalID
		$this->BuildSearchSql($sWhere, $this->MKID, $Default, FALSE); // MKID
		$this->BuildSearchSql($sWhere, $this->MKKode, $Default, FALSE); // MKKode
		$this->BuildSearchSql($sWhere, $this->SKS, $Default, FALSE); // SKS
		$this->BuildSearchSql($sWhere, $this->Tugas1, $Default, FALSE); // Tugas1
		$this->BuildSearchSql($sWhere, $this->Tugas2, $Default, FALSE); // Tugas2
		$this->BuildSearchSql($sWhere, $this->Tugas3, $Default, FALSE); // Tugas3
		$this->BuildSearchSql($sWhere, $this->Tugas4, $Default, FALSE); // Tugas4
		$this->BuildSearchSql($sWhere, $this->Tugas5, $Default, FALSE); // Tugas5
		$this->BuildSearchSql($sWhere, $this->Presensi, $Default, FALSE); // Presensi
		$this->BuildSearchSql($sWhere, $this->_Presensi, $Default, FALSE); // _Presensi
		$this->BuildSearchSql($sWhere, $this->UTS, $Default, FALSE); // UTS
		$this->BuildSearchSql($sWhere, $this->UAS, $Default, FALSE); // UAS
		$this->BuildSearchSql($sWhere, $this->Responsi, $Default, FALSE); // Responsi
		$this->BuildSearchSql($sWhere, $this->NilaiAkhir, $Default, FALSE); // NilaiAkhir
		$this->BuildSearchSql($sWhere, $this->GradeNilai, $Default, FALSE); // GradeNilai
		$this->BuildSearchSql($sWhere, $this->BobotNilai, $Default, FALSE); // BobotNilai
		$this->BuildSearchSql($sWhere, $this->StatusKRSID, $Default, FALSE); // StatusKRSID
		$this->BuildSearchSql($sWhere, $this->Tinggi, $Default, FALSE); // Tinggi
		$this->BuildSearchSql($sWhere, $this->Final, $Default, FALSE); // Final
		$this->BuildSearchSql($sWhere, $this->Setara, $Default, FALSE); // Setara
		$this->BuildSearchSql($sWhere, $this->Creator, $Default, FALSE); // Creator
		$this->BuildSearchSql($sWhere, $this->CreateDate, $Default, FALSE); // CreateDate
		$this->BuildSearchSql($sWhere, $this->Editor, $Default, FALSE); // Editor
		$this->BuildSearchSql($sWhere, $this->EditDate, $Default, FALSE); // EditDate
		$this->BuildSearchSql($sWhere, $this->NA, $Default, FALSE); // NA

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->KRSID->AdvancedSearch->Save(); // KRSID
			$this->KHSID->AdvancedSearch->Save(); // KHSID
			$this->StudentID->AdvancedSearch->Save(); // StudentID
			$this->TahunID->AdvancedSearch->Save(); // TahunID
			$this->Sesi->AdvancedSearch->Save(); // Sesi
			$this->JadwalID->AdvancedSearch->Save(); // JadwalID
			$this->MKID->AdvancedSearch->Save(); // MKID
			$this->MKKode->AdvancedSearch->Save(); // MKKode
			$this->SKS->AdvancedSearch->Save(); // SKS
			$this->Tugas1->AdvancedSearch->Save(); // Tugas1
			$this->Tugas2->AdvancedSearch->Save(); // Tugas2
			$this->Tugas3->AdvancedSearch->Save(); // Tugas3
			$this->Tugas4->AdvancedSearch->Save(); // Tugas4
			$this->Tugas5->AdvancedSearch->Save(); // Tugas5
			$this->Presensi->AdvancedSearch->Save(); // Presensi
			$this->_Presensi->AdvancedSearch->Save(); // _Presensi
			$this->UTS->AdvancedSearch->Save(); // UTS
			$this->UAS->AdvancedSearch->Save(); // UAS
			$this->Responsi->AdvancedSearch->Save(); // Responsi
			$this->NilaiAkhir->AdvancedSearch->Save(); // NilaiAkhir
			$this->GradeNilai->AdvancedSearch->Save(); // GradeNilai
			$this->BobotNilai->AdvancedSearch->Save(); // BobotNilai
			$this->StatusKRSID->AdvancedSearch->Save(); // StatusKRSID
			$this->Tinggi->AdvancedSearch->Save(); // Tinggi
			$this->Final->AdvancedSearch->Save(); // Final
			$this->Setara->AdvancedSearch->Save(); // Setara
			$this->Creator->AdvancedSearch->Save(); // Creator
			$this->CreateDate->AdvancedSearch->Save(); // CreateDate
			$this->Editor->AdvancedSearch->Save(); // Editor
			$this->EditDate->AdvancedSearch->Save(); // EditDate
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
		$this->BuildBasicSearchSQL($sWhere, $this->TahunID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->MKKode, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->GradeNilai, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->StatusKRSID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Tinggi, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Creator, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Editor, $arKeywords, $type);
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
		if ($this->KRSID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KHSID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->StudentID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TahunID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Sesi->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->JadwalID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->MKID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->MKKode->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->SKS->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Tugas1->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Tugas2->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Tugas3->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Tugas4->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Tugas5->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Presensi->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->_Presensi->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->UTS->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->UAS->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Responsi->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NilaiAkhir->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->GradeNilai->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->BobotNilai->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->StatusKRSID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Tinggi->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Final->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Setara->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Creator->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->CreateDate->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Editor->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->EditDate->AdvancedSearch->IssetSession())
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
		$this->KRSID->AdvancedSearch->UnsetSession();
		$this->KHSID->AdvancedSearch->UnsetSession();
		$this->StudentID->AdvancedSearch->UnsetSession();
		$this->TahunID->AdvancedSearch->UnsetSession();
		$this->Sesi->AdvancedSearch->UnsetSession();
		$this->JadwalID->AdvancedSearch->UnsetSession();
		$this->MKID->AdvancedSearch->UnsetSession();
		$this->MKKode->AdvancedSearch->UnsetSession();
		$this->SKS->AdvancedSearch->UnsetSession();
		$this->Tugas1->AdvancedSearch->UnsetSession();
		$this->Tugas2->AdvancedSearch->UnsetSession();
		$this->Tugas3->AdvancedSearch->UnsetSession();
		$this->Tugas4->AdvancedSearch->UnsetSession();
		$this->Tugas5->AdvancedSearch->UnsetSession();
		$this->Presensi->AdvancedSearch->UnsetSession();
		$this->_Presensi->AdvancedSearch->UnsetSession();
		$this->UTS->AdvancedSearch->UnsetSession();
		$this->UAS->AdvancedSearch->UnsetSession();
		$this->Responsi->AdvancedSearch->UnsetSession();
		$this->NilaiAkhir->AdvancedSearch->UnsetSession();
		$this->GradeNilai->AdvancedSearch->UnsetSession();
		$this->BobotNilai->AdvancedSearch->UnsetSession();
		$this->StatusKRSID->AdvancedSearch->UnsetSession();
		$this->Tinggi->AdvancedSearch->UnsetSession();
		$this->Final->AdvancedSearch->UnsetSession();
		$this->Setara->AdvancedSearch->UnsetSession();
		$this->Creator->AdvancedSearch->UnsetSession();
		$this->CreateDate->AdvancedSearch->UnsetSession();
		$this->Editor->AdvancedSearch->UnsetSession();
		$this->EditDate->AdvancedSearch->UnsetSession();
		$this->NA->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->KRSID->AdvancedSearch->Load();
		$this->KHSID->AdvancedSearch->Load();
		$this->StudentID->AdvancedSearch->Load();
		$this->TahunID->AdvancedSearch->Load();
		$this->Sesi->AdvancedSearch->Load();
		$this->JadwalID->AdvancedSearch->Load();
		$this->MKID->AdvancedSearch->Load();
		$this->MKKode->AdvancedSearch->Load();
		$this->SKS->AdvancedSearch->Load();
		$this->Tugas1->AdvancedSearch->Load();
		$this->Tugas2->AdvancedSearch->Load();
		$this->Tugas3->AdvancedSearch->Load();
		$this->Tugas4->AdvancedSearch->Load();
		$this->Tugas5->AdvancedSearch->Load();
		$this->Presensi->AdvancedSearch->Load();
		$this->_Presensi->AdvancedSearch->Load();
		$this->UTS->AdvancedSearch->Load();
		$this->UAS->AdvancedSearch->Load();
		$this->Responsi->AdvancedSearch->Load();
		$this->NilaiAkhir->AdvancedSearch->Load();
		$this->GradeNilai->AdvancedSearch->Load();
		$this->BobotNilai->AdvancedSearch->Load();
		$this->StatusKRSID->AdvancedSearch->Load();
		$this->Tinggi->AdvancedSearch->Load();
		$this->Final->AdvancedSearch->Load();
		$this->Setara->AdvancedSearch->Load();
		$this->Creator->AdvancedSearch->Load();
		$this->CreateDate->AdvancedSearch->Load();
		$this->Editor->AdvancedSearch->Load();
		$this->EditDate->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->KRSID); // KRSID
			$this->UpdateSort($this->KHSID); // KHSID
			$this->UpdateSort($this->StudentID); // StudentID
			$this->UpdateSort($this->TahunID); // TahunID
			$this->UpdateSort($this->Sesi); // Sesi
			$this->UpdateSort($this->JadwalID); // JadwalID
			$this->UpdateSort($this->MKID); // MKID
			$this->UpdateSort($this->MKKode); // MKKode
			$this->UpdateSort($this->SKS); // SKS
			$this->UpdateSort($this->Tugas1); // Tugas1
			$this->UpdateSort($this->Tugas2); // Tugas2
			$this->UpdateSort($this->Tugas3); // Tugas3
			$this->UpdateSort($this->Tugas4); // Tugas4
			$this->UpdateSort($this->Tugas5); // Tugas5
			$this->UpdateSort($this->Presensi); // Presensi
			$this->UpdateSort($this->_Presensi); // _Presensi
			$this->UpdateSort($this->UTS); // UTS
			$this->UpdateSort($this->UAS); // UAS
			$this->UpdateSort($this->Responsi); // Responsi
			$this->UpdateSort($this->NilaiAkhir); // NilaiAkhir
			$this->UpdateSort($this->GradeNilai); // GradeNilai
			$this->UpdateSort($this->BobotNilai); // BobotNilai
			$this->UpdateSort($this->StatusKRSID); // StatusKRSID
			$this->UpdateSort($this->Tinggi); // Tinggi
			$this->UpdateSort($this->Final); // Final
			$this->UpdateSort($this->Setara); // Setara
			$this->UpdateSort($this->Creator); // Creator
			$this->UpdateSort($this->CreateDate); // CreateDate
			$this->UpdateSort($this->Editor); // Editor
			$this->UpdateSort($this->EditDate); // EditDate
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
				$this->KRSID->setSort("");
				$this->KHSID->setSort("");
				$this->StudentID->setSort("");
				$this->TahunID->setSort("");
				$this->Sesi->setSort("");
				$this->JadwalID->setSort("");
				$this->MKID->setSort("");
				$this->MKKode->setSort("");
				$this->SKS->setSort("");
				$this->Tugas1->setSort("");
				$this->Tugas2->setSort("");
				$this->Tugas3->setSort("");
				$this->Tugas4->setSort("");
				$this->Tugas5->setSort("");
				$this->Presensi->setSort("");
				$this->_Presensi->setSort("");
				$this->UTS->setSort("");
				$this->UAS->setSort("");
				$this->Responsi->setSort("");
				$this->NilaiAkhir->setSort("");
				$this->GradeNilai->setSort("");
				$this->BobotNilai->setSort("");
				$this->StatusKRSID->setSort("");
				$this->Tinggi->setSort("");
				$this->Final->setSort("");
				$this->Setara->setSort("");
				$this->Creator->setSort("");
				$this->CreateDate->setSort("");
				$this->Editor->setSort("");
				$this->EditDate->setSort("");
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
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->KRSID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event);'>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fkrslistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fkrslistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
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
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fkrslist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fkrslistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		if (ew_IsMobile())
			$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"krssrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		else
			$item->Body = "<button type=\"button\" class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-table=\"krs\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" onclick=\"ew_ModalDialogShow({lnk:this,url:'krssrch.php',caption:'" . $Language->Phrase("Search") . "'});\">" . $Language->Phrase("AdvancedSearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Search highlight button
		$item = &$this->SearchOptions->Add("searchhighlight");
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewHighlight active\" title=\"" . $Language->Phrase("Highlight") . "\" data-caption=\"" . $Language->Phrase("Highlight") . "\" data-toggle=\"button\" data-form=\"fkrslistsrch\" data-name=\"" . $this->HighlightName() . "\">" . $Language->Phrase("HighlightBtn") . "</button>";
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
		// KRSID

		$this->KRSID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KRSID"]);
		if ($this->KRSID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KRSID->AdvancedSearch->SearchOperator = @$_GET["z_KRSID"];

		// KHSID
		$this->KHSID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KHSID"]);
		if ($this->KHSID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KHSID->AdvancedSearch->SearchOperator = @$_GET["z_KHSID"];

		// StudentID
		$this->StudentID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_StudentID"]);
		if ($this->StudentID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->StudentID->AdvancedSearch->SearchOperator = @$_GET["z_StudentID"];

		// TahunID
		$this->TahunID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TahunID"]);
		if ($this->TahunID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TahunID->AdvancedSearch->SearchOperator = @$_GET["z_TahunID"];

		// Sesi
		$this->Sesi->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Sesi"]);
		if ($this->Sesi->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Sesi->AdvancedSearch->SearchOperator = @$_GET["z_Sesi"];

		// JadwalID
		$this->JadwalID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_JadwalID"]);
		if ($this->JadwalID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->JadwalID->AdvancedSearch->SearchOperator = @$_GET["z_JadwalID"];

		// MKID
		$this->MKID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_MKID"]);
		if ($this->MKID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->MKID->AdvancedSearch->SearchOperator = @$_GET["z_MKID"];

		// MKKode
		$this->MKKode->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_MKKode"]);
		if ($this->MKKode->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->MKKode->AdvancedSearch->SearchOperator = @$_GET["z_MKKode"];

		// SKS
		$this->SKS->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_SKS"]);
		if ($this->SKS->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->SKS->AdvancedSearch->SearchOperator = @$_GET["z_SKS"];

		// Tugas1
		$this->Tugas1->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Tugas1"]);
		if ($this->Tugas1->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Tugas1->AdvancedSearch->SearchOperator = @$_GET["z_Tugas1"];

		// Tugas2
		$this->Tugas2->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Tugas2"]);
		if ($this->Tugas2->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Tugas2->AdvancedSearch->SearchOperator = @$_GET["z_Tugas2"];

		// Tugas3
		$this->Tugas3->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Tugas3"]);
		if ($this->Tugas3->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Tugas3->AdvancedSearch->SearchOperator = @$_GET["z_Tugas3"];

		// Tugas4
		$this->Tugas4->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Tugas4"]);
		if ($this->Tugas4->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Tugas4->AdvancedSearch->SearchOperator = @$_GET["z_Tugas4"];

		// Tugas5
		$this->Tugas5->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Tugas5"]);
		if ($this->Tugas5->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Tugas5->AdvancedSearch->SearchOperator = @$_GET["z_Tugas5"];

		// Presensi
		$this->Presensi->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Presensi"]);
		if ($this->Presensi->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Presensi->AdvancedSearch->SearchOperator = @$_GET["z_Presensi"];

		// _Presensi
		$this->_Presensi->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x__Presensi"]);
		if ($this->_Presensi->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->_Presensi->AdvancedSearch->SearchOperator = @$_GET["z__Presensi"];

		// UTS
		$this->UTS->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_UTS"]);
		if ($this->UTS->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->UTS->AdvancedSearch->SearchOperator = @$_GET["z_UTS"];

		// UAS
		$this->UAS->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_UAS"]);
		if ($this->UAS->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->UAS->AdvancedSearch->SearchOperator = @$_GET["z_UAS"];

		// Responsi
		$this->Responsi->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Responsi"]);
		if ($this->Responsi->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Responsi->AdvancedSearch->SearchOperator = @$_GET["z_Responsi"];

		// NilaiAkhir
		$this->NilaiAkhir->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NilaiAkhir"]);
		if ($this->NilaiAkhir->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NilaiAkhir->AdvancedSearch->SearchOperator = @$_GET["z_NilaiAkhir"];

		// GradeNilai
		$this->GradeNilai->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_GradeNilai"]);
		if ($this->GradeNilai->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->GradeNilai->AdvancedSearch->SearchOperator = @$_GET["z_GradeNilai"];

		// BobotNilai
		$this->BobotNilai->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_BobotNilai"]);
		if ($this->BobotNilai->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->BobotNilai->AdvancedSearch->SearchOperator = @$_GET["z_BobotNilai"];

		// StatusKRSID
		$this->StatusKRSID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_StatusKRSID"]);
		if ($this->StatusKRSID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->StatusKRSID->AdvancedSearch->SearchOperator = @$_GET["z_StatusKRSID"];

		// Tinggi
		$this->Tinggi->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Tinggi"]);
		if ($this->Tinggi->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Tinggi->AdvancedSearch->SearchOperator = @$_GET["z_Tinggi"];

		// Final
		$this->Final->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Final"]);
		if ($this->Final->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Final->AdvancedSearch->SearchOperator = @$_GET["z_Final"];

		// Setara
		$this->Setara->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Setara"]);
		if ($this->Setara->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Setara->AdvancedSearch->SearchOperator = @$_GET["z_Setara"];

		// Creator
		$this->Creator->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Creator"]);
		if ($this->Creator->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Creator->AdvancedSearch->SearchOperator = @$_GET["z_Creator"];

		// CreateDate
		$this->CreateDate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_CreateDate"]);
		if ($this->CreateDate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->CreateDate->AdvancedSearch->SearchOperator = @$_GET["z_CreateDate"];

		// Editor
		$this->Editor->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Editor"]);
		if ($this->Editor->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Editor->AdvancedSearch->SearchOperator = @$_GET["z_Editor"];

		// EditDate
		$this->EditDate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_EditDate"]);
		if ($this->EditDate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->EditDate->AdvancedSearch->SearchOperator = @$_GET["z_EditDate"];

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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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

			// KRSID
			$this->KRSID->LinkCustomAttributes = "";
			$this->KRSID->HrefValue = "";
			$this->KRSID->TooltipValue = "";
			if ($this->Export == "")
				$this->KRSID->ViewValue = ew_Highlight($this->HighlightName(), $this->KRSID->ViewValue, "", "", $this->KRSID->AdvancedSearch->getValue("x"), "");

			// KHSID
			$this->KHSID->LinkCustomAttributes = "";
			$this->KHSID->HrefValue = "";
			$this->KHSID->TooltipValue = "";
			if ($this->Export == "")
				$this->KHSID->ViewValue = ew_Highlight($this->HighlightName(), $this->KHSID->ViewValue, "", "", $this->KHSID->AdvancedSearch->getValue("x"), "");

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";
			$this->StudentID->TooltipValue = "";
			if ($this->Export == "")
				$this->StudentID->ViewValue = ew_Highlight($this->HighlightName(), $this->StudentID->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->StudentID->AdvancedSearch->getValue("x"), "");

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";
			$this->TahunID->TooltipValue = "";
			if ($this->Export == "")
				$this->TahunID->ViewValue = ew_Highlight($this->HighlightName(), $this->TahunID->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->TahunID->AdvancedSearch->getValue("x"), "");

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";
			$this->Sesi->TooltipValue = "";
			if ($this->Export == "")
				$this->Sesi->ViewValue = ew_Highlight($this->HighlightName(), $this->Sesi->ViewValue, "", "", $this->Sesi->AdvancedSearch->getValue("x"), "");

			// JadwalID
			$this->JadwalID->LinkCustomAttributes = "";
			$this->JadwalID->HrefValue = "";
			$this->JadwalID->TooltipValue = "";
			if ($this->Export == "")
				$this->JadwalID->ViewValue = ew_Highlight($this->HighlightName(), $this->JadwalID->ViewValue, "", "", $this->JadwalID->AdvancedSearch->getValue("x"), "");

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";
			$this->MKID->TooltipValue = "";
			if ($this->Export == "")
				$this->MKID->ViewValue = ew_Highlight($this->HighlightName(), $this->MKID->ViewValue, "", "", $this->MKID->AdvancedSearch->getValue("x"), "");

			// MKKode
			$this->MKKode->LinkCustomAttributes = "";
			$this->MKKode->HrefValue = "";
			$this->MKKode->TooltipValue = "";
			if ($this->Export == "")
				$this->MKKode->ViewValue = ew_Highlight($this->HighlightName(), $this->MKKode->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->MKKode->AdvancedSearch->getValue("x"), "");

			// SKS
			$this->SKS->LinkCustomAttributes = "";
			$this->SKS->HrefValue = "";
			$this->SKS->TooltipValue = "";
			if ($this->Export == "")
				$this->SKS->ViewValue = ew_Highlight($this->HighlightName(), $this->SKS->ViewValue, "", "", $this->SKS->AdvancedSearch->getValue("x"), "");

			// Tugas1
			$this->Tugas1->LinkCustomAttributes = "";
			$this->Tugas1->HrefValue = "";
			$this->Tugas1->TooltipValue = "";
			if ($this->Export == "")
				$this->Tugas1->ViewValue = ew_Highlight($this->HighlightName(), $this->Tugas1->ViewValue, "", "", $this->Tugas1->AdvancedSearch->getValue("x"), "");

			// Tugas2
			$this->Tugas2->LinkCustomAttributes = "";
			$this->Tugas2->HrefValue = "";
			$this->Tugas2->TooltipValue = "";
			if ($this->Export == "")
				$this->Tugas2->ViewValue = ew_Highlight($this->HighlightName(), $this->Tugas2->ViewValue, "", "", $this->Tugas2->AdvancedSearch->getValue("x"), "");

			// Tugas3
			$this->Tugas3->LinkCustomAttributes = "";
			$this->Tugas3->HrefValue = "";
			$this->Tugas3->TooltipValue = "";
			if ($this->Export == "")
				$this->Tugas3->ViewValue = ew_Highlight($this->HighlightName(), $this->Tugas3->ViewValue, "", "", $this->Tugas3->AdvancedSearch->getValue("x"), "");

			// Tugas4
			$this->Tugas4->LinkCustomAttributes = "";
			$this->Tugas4->HrefValue = "";
			$this->Tugas4->TooltipValue = "";
			if ($this->Export == "")
				$this->Tugas4->ViewValue = ew_Highlight($this->HighlightName(), $this->Tugas4->ViewValue, "", "", $this->Tugas4->AdvancedSearch->getValue("x"), "");

			// Tugas5
			$this->Tugas5->LinkCustomAttributes = "";
			$this->Tugas5->HrefValue = "";
			$this->Tugas5->TooltipValue = "";
			if ($this->Export == "")
				$this->Tugas5->ViewValue = ew_Highlight($this->HighlightName(), $this->Tugas5->ViewValue, "", "", $this->Tugas5->AdvancedSearch->getValue("x"), "");

			// Presensi
			$this->Presensi->LinkCustomAttributes = "";
			$this->Presensi->HrefValue = "";
			$this->Presensi->TooltipValue = "";
			if ($this->Export == "")
				$this->Presensi->ViewValue = ew_Highlight($this->HighlightName(), $this->Presensi->ViewValue, "", "", $this->Presensi->AdvancedSearch->getValue("x"), "");

			// _Presensi
			$this->_Presensi->LinkCustomAttributes = "";
			$this->_Presensi->HrefValue = "";
			$this->_Presensi->TooltipValue = "";
			if ($this->Export == "")
				$this->_Presensi->ViewValue = ew_Highlight($this->HighlightName(), $this->_Presensi->ViewValue, "", "", $this->_Presensi->AdvancedSearch->getValue("x"), "");

			// UTS
			$this->UTS->LinkCustomAttributes = "";
			$this->UTS->HrefValue = "";
			$this->UTS->TooltipValue = "";
			if ($this->Export == "")
				$this->UTS->ViewValue = ew_Highlight($this->HighlightName(), $this->UTS->ViewValue, "", "", $this->UTS->AdvancedSearch->getValue("x"), "");

			// UAS
			$this->UAS->LinkCustomAttributes = "";
			$this->UAS->HrefValue = "";
			$this->UAS->TooltipValue = "";
			if ($this->Export == "")
				$this->UAS->ViewValue = ew_Highlight($this->HighlightName(), $this->UAS->ViewValue, "", "", $this->UAS->AdvancedSearch->getValue("x"), "");

			// Responsi
			$this->Responsi->LinkCustomAttributes = "";
			$this->Responsi->HrefValue = "";
			$this->Responsi->TooltipValue = "";
			if ($this->Export == "")
				$this->Responsi->ViewValue = ew_Highlight($this->HighlightName(), $this->Responsi->ViewValue, "", "", $this->Responsi->AdvancedSearch->getValue("x"), "");

			// NilaiAkhir
			$this->NilaiAkhir->LinkCustomAttributes = "";
			$this->NilaiAkhir->HrefValue = "";
			$this->NilaiAkhir->TooltipValue = "";
			if ($this->Export == "")
				$this->NilaiAkhir->ViewValue = ew_Highlight($this->HighlightName(), $this->NilaiAkhir->ViewValue, "", "", $this->NilaiAkhir->AdvancedSearch->getValue("x"), "");

			// GradeNilai
			$this->GradeNilai->LinkCustomAttributes = "";
			$this->GradeNilai->HrefValue = "";
			$this->GradeNilai->TooltipValue = "";
			if ($this->Export == "")
				$this->GradeNilai->ViewValue = ew_Highlight($this->HighlightName(), $this->GradeNilai->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->GradeNilai->AdvancedSearch->getValue("x"), "");

			// BobotNilai
			$this->BobotNilai->LinkCustomAttributes = "";
			$this->BobotNilai->HrefValue = "";
			$this->BobotNilai->TooltipValue = "";
			if ($this->Export == "")
				$this->BobotNilai->ViewValue = ew_Highlight($this->HighlightName(), $this->BobotNilai->ViewValue, "", "", $this->BobotNilai->AdvancedSearch->getValue("x"), "");

			// StatusKRSID
			$this->StatusKRSID->LinkCustomAttributes = "";
			$this->StatusKRSID->HrefValue = "";
			$this->StatusKRSID->TooltipValue = "";
			if ($this->Export == "")
				$this->StatusKRSID->ViewValue = ew_Highlight($this->HighlightName(), $this->StatusKRSID->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->StatusKRSID->AdvancedSearch->getValue("x"), "");

			// Tinggi
			$this->Tinggi->LinkCustomAttributes = "";
			$this->Tinggi->HrefValue = "";
			$this->Tinggi->TooltipValue = "";
			if ($this->Export == "")
				$this->Tinggi->ViewValue = ew_Highlight($this->HighlightName(), $this->Tinggi->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Tinggi->AdvancedSearch->getValue("x"), "");

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
			if ($this->Export == "")
				$this->Creator->ViewValue = ew_Highlight($this->HighlightName(), $this->Creator->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Creator->AdvancedSearch->getValue("x"), "");

			// CreateDate
			$this->CreateDate->LinkCustomAttributes = "";
			$this->CreateDate->HrefValue = "";
			$this->CreateDate->TooltipValue = "";

			// Editor
			$this->Editor->LinkCustomAttributes = "";
			$this->Editor->HrefValue = "";
			$this->Editor->TooltipValue = "";
			if ($this->Export == "")
				$this->Editor->ViewValue = ew_Highlight($this->HighlightName(), $this->Editor->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Editor->AdvancedSearch->getValue("x"), "");

			// EditDate
			$this->EditDate->LinkCustomAttributes = "";
			$this->EditDate->HrefValue = "";
			$this->EditDate->TooltipValue = "";

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
		$this->KRSID->AdvancedSearch->Load();
		$this->KHSID->AdvancedSearch->Load();
		$this->StudentID->AdvancedSearch->Load();
		$this->TahunID->AdvancedSearch->Load();
		$this->Sesi->AdvancedSearch->Load();
		$this->JadwalID->AdvancedSearch->Load();
		$this->MKID->AdvancedSearch->Load();
		$this->MKKode->AdvancedSearch->Load();
		$this->SKS->AdvancedSearch->Load();
		$this->Tugas1->AdvancedSearch->Load();
		$this->Tugas2->AdvancedSearch->Load();
		$this->Tugas3->AdvancedSearch->Load();
		$this->Tugas4->AdvancedSearch->Load();
		$this->Tugas5->AdvancedSearch->Load();
		$this->Presensi->AdvancedSearch->Load();
		$this->_Presensi->AdvancedSearch->Load();
		$this->UTS->AdvancedSearch->Load();
		$this->UAS->AdvancedSearch->Load();
		$this->Responsi->AdvancedSearch->Load();
		$this->NilaiAkhir->AdvancedSearch->Load();
		$this->GradeNilai->AdvancedSearch->Load();
		$this->BobotNilai->AdvancedSearch->Load();
		$this->StatusKRSID->AdvancedSearch->Load();
		$this->Tinggi->AdvancedSearch->Load();
		$this->Final->AdvancedSearch->Load();
		$this->Setara->AdvancedSearch->Load();
		$this->Creator->AdvancedSearch->Load();
		$this->CreateDate->AdvancedSearch->Load();
		$this->Editor->AdvancedSearch->Load();
		$this->EditDate->AdvancedSearch->Load();
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
		$item->Body = "<button id=\"emf_krs\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_krs',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fkrslist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
if (!isset($krs_list)) $krs_list = new ckrs_list();

// Page init
$krs_list->Page_Init();

// Page main
$krs_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$krs_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($krs->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fkrslist = new ew_Form("fkrslist", "list");
fkrslist.FormKeyCountName = '<?php echo $krs_list->FormKeyCountName ?>';

// Form_CustomValidate event
fkrslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fkrslist.ValidateRequired = true;
<?php } else { ?>
fkrslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fkrslist.Lists["x_Final"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrslist.Lists["x_Final"].Options = <?php echo json_encode($krs->Final->Options()) ?>;
fkrslist.Lists["x_Setara"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrslist.Lists["x_Setara"].Options = <?php echo json_encode($krs->Setara->Options()) ?>;
fkrslist.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrslist.Lists["x_NA"].Options = <?php echo json_encode($krs->NA->Options()) ?>;

// Form object for search
var CurrentSearchForm = fkrslistsrch = new ew_Form("fkrslistsrch");

// Init search panel as collapsed
if (fkrslistsrch) fkrslistsrch.InitSearchPanel = true;
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
<?php if ($krs->Export == "") { ?>
<div class="ewToolbar">
<?php if ($krs->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($krs_list->TotalRecs > 0 && $krs_list->ExportOptions->Visible()) { ?>
<?php $krs_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($krs_list->SearchOptions->Visible()) { ?>
<?php $krs_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($krs_list->FilterOptions->Visible()) { ?>
<?php $krs_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php if ($krs->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = $krs_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($krs_list->TotalRecs <= 0)
			$krs_list->TotalRecs = $krs->SelectRecordCount();
	} else {
		if (!$krs_list->Recordset && ($krs_list->Recordset = $krs_list->LoadRecordset()))
			$krs_list->TotalRecs = $krs_list->Recordset->RecordCount();
	}
	$krs_list->StartRec = 1;
	if ($krs_list->DisplayRecs <= 0 || ($krs->Export <> "" && $krs->ExportAll)) // Display all records
		$krs_list->DisplayRecs = $krs_list->TotalRecs;
	if (!($krs->Export <> "" && $krs->ExportAll))
		$krs_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$krs_list->Recordset = $krs_list->LoadRecordset($krs_list->StartRec-1, $krs_list->DisplayRecs);

	// Set no record found message
	if ($krs->CurrentAction == "" && $krs_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$krs_list->setWarningMessage(ew_DeniedMsg());
		if ($krs_list->SearchWhere == "0=101")
			$krs_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$krs_list->setWarningMessage($Language->Phrase("NoRecord"));
	}

	// Audit trail on search
	if ($krs_list->AuditTrailOnSearch && $krs_list->Command == "search" && !$krs_list->RestoreSearch) {
		$searchparm = ew_ServerVar("QUERY_STRING");
		$searchsql = $krs_list->getSessionWhere();
		$krs_list->WriteAuditTrailOnSearch($searchparm, $searchsql);
	}
$krs_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($krs->Export == "" && $krs->CurrentAction == "") { ?>
<form name="fkrslistsrch" id="fkrslistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($krs_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fkrslistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="krs">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($krs_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($krs_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $krs_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($krs_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($krs_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($krs_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($krs_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
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
<?php $krs_list->ShowPageHeader(); ?>
<?php
$krs_list->ShowMessage();
?>
<?php if ($krs_list->TotalRecs > 0 || $krs->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid krs">
<?php if ($krs->Export == "") { ?>
<div class="panel-heading ewGridUpperPanel">
<?php if ($krs->CurrentAction <> "gridadd" && $krs->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($krs_list->Pager)) $krs_list->Pager = new cPrevNextPager($krs_list->StartRec, $krs_list->DisplayRecs, $krs_list->TotalRecs) ?>
<?php if ($krs_list->Pager->RecordCount > 0 && $krs_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($krs_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $krs_list->PageUrl() ?>start=<?php echo $krs_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($krs_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $krs_list->PageUrl() ?>start=<?php echo $krs_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $krs_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($krs_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $krs_list->PageUrl() ?>start=<?php echo $krs_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($krs_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $krs_list->PageUrl() ?>start=<?php echo $krs_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $krs_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $krs_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $krs_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $krs_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($krs_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="fkrslist" id="fkrslist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($krs_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $krs_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="krs">
<div id="gmp_krs" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($krs_list->TotalRecs > 0 || $krs->CurrentAction == "gridedit") { ?>
<table id="tbl_krslist" class="table ewTable">
<?php echo $krs->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$krs_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$krs_list->RenderListOptions();

// Render list options (header, left)
$krs_list->ListOptions->Render("header", "left");
?>
<?php if ($krs->KRSID->Visible) { // KRSID ?>
	<?php if ($krs->SortUrl($krs->KRSID) == "") { ?>
		<th data-name="KRSID"><div id="elh_krs_KRSID" class="krs_KRSID"><div class="ewTableHeaderCaption"><?php echo $krs->KRSID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="KRSID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->KRSID) ?>',1);"><div id="elh_krs_KRSID" class="krs_KRSID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->KRSID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->KRSID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->KRSID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->KHSID->Visible) { // KHSID ?>
	<?php if ($krs->SortUrl($krs->KHSID) == "") { ?>
		<th data-name="KHSID"><div id="elh_krs_KHSID" class="krs_KHSID"><div class="ewTableHeaderCaption"><?php echo $krs->KHSID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="KHSID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->KHSID) ?>',1);"><div id="elh_krs_KHSID" class="krs_KHSID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->KHSID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->KHSID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->KHSID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->StudentID->Visible) { // StudentID ?>
	<?php if ($krs->SortUrl($krs->StudentID) == "") { ?>
		<th data-name="StudentID"><div id="elh_krs_StudentID" class="krs_StudentID"><div class="ewTableHeaderCaption"><?php echo $krs->StudentID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="StudentID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->StudentID) ?>',1);"><div id="elh_krs_StudentID" class="krs_StudentID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->StudentID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($krs->StudentID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->StudentID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->TahunID->Visible) { // TahunID ?>
	<?php if ($krs->SortUrl($krs->TahunID) == "") { ?>
		<th data-name="TahunID"><div id="elh_krs_TahunID" class="krs_TahunID"><div class="ewTableHeaderCaption"><?php echo $krs->TahunID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TahunID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->TahunID) ?>',1);"><div id="elh_krs_TahunID" class="krs_TahunID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->TahunID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($krs->TahunID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->TahunID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Sesi->Visible) { // Sesi ?>
	<?php if ($krs->SortUrl($krs->Sesi) == "") { ?>
		<th data-name="Sesi"><div id="elh_krs_Sesi" class="krs_Sesi"><div class="ewTableHeaderCaption"><?php echo $krs->Sesi->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Sesi"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Sesi) ?>',1);"><div id="elh_krs_Sesi" class="krs_Sesi">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Sesi->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->Sesi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Sesi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->JadwalID->Visible) { // JadwalID ?>
	<?php if ($krs->SortUrl($krs->JadwalID) == "") { ?>
		<th data-name="JadwalID"><div id="elh_krs_JadwalID" class="krs_JadwalID"><div class="ewTableHeaderCaption"><?php echo $krs->JadwalID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="JadwalID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->JadwalID) ?>',1);"><div id="elh_krs_JadwalID" class="krs_JadwalID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->JadwalID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->JadwalID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->JadwalID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->MKID->Visible) { // MKID ?>
	<?php if ($krs->SortUrl($krs->MKID) == "") { ?>
		<th data-name="MKID"><div id="elh_krs_MKID" class="krs_MKID"><div class="ewTableHeaderCaption"><?php echo $krs->MKID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="MKID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->MKID) ?>',1);"><div id="elh_krs_MKID" class="krs_MKID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->MKID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->MKID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->MKID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->MKKode->Visible) { // MKKode ?>
	<?php if ($krs->SortUrl($krs->MKKode) == "") { ?>
		<th data-name="MKKode"><div id="elh_krs_MKKode" class="krs_MKKode"><div class="ewTableHeaderCaption"><?php echo $krs->MKKode->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="MKKode"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->MKKode) ?>',1);"><div id="elh_krs_MKKode" class="krs_MKKode">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->MKKode->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($krs->MKKode->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->MKKode->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->SKS->Visible) { // SKS ?>
	<?php if ($krs->SortUrl($krs->SKS) == "") { ?>
		<th data-name="SKS"><div id="elh_krs_SKS" class="krs_SKS"><div class="ewTableHeaderCaption"><?php echo $krs->SKS->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="SKS"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->SKS) ?>',1);"><div id="elh_krs_SKS" class="krs_SKS">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->SKS->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->SKS->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->SKS->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Tugas1->Visible) { // Tugas1 ?>
	<?php if ($krs->SortUrl($krs->Tugas1) == "") { ?>
		<th data-name="Tugas1"><div id="elh_krs_Tugas1" class="krs_Tugas1"><div class="ewTableHeaderCaption"><?php echo $krs->Tugas1->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tugas1"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Tugas1) ?>',1);"><div id="elh_krs_Tugas1" class="krs_Tugas1">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Tugas1->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->Tugas1->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Tugas1->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Tugas2->Visible) { // Tugas2 ?>
	<?php if ($krs->SortUrl($krs->Tugas2) == "") { ?>
		<th data-name="Tugas2"><div id="elh_krs_Tugas2" class="krs_Tugas2"><div class="ewTableHeaderCaption"><?php echo $krs->Tugas2->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tugas2"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Tugas2) ?>',1);"><div id="elh_krs_Tugas2" class="krs_Tugas2">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Tugas2->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->Tugas2->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Tugas2->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Tugas3->Visible) { // Tugas3 ?>
	<?php if ($krs->SortUrl($krs->Tugas3) == "") { ?>
		<th data-name="Tugas3"><div id="elh_krs_Tugas3" class="krs_Tugas3"><div class="ewTableHeaderCaption"><?php echo $krs->Tugas3->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tugas3"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Tugas3) ?>',1);"><div id="elh_krs_Tugas3" class="krs_Tugas3">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Tugas3->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->Tugas3->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Tugas3->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Tugas4->Visible) { // Tugas4 ?>
	<?php if ($krs->SortUrl($krs->Tugas4) == "") { ?>
		<th data-name="Tugas4"><div id="elh_krs_Tugas4" class="krs_Tugas4"><div class="ewTableHeaderCaption"><?php echo $krs->Tugas4->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tugas4"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Tugas4) ?>',1);"><div id="elh_krs_Tugas4" class="krs_Tugas4">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Tugas4->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->Tugas4->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Tugas4->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Tugas5->Visible) { // Tugas5 ?>
	<?php if ($krs->SortUrl($krs->Tugas5) == "") { ?>
		<th data-name="Tugas5"><div id="elh_krs_Tugas5" class="krs_Tugas5"><div class="ewTableHeaderCaption"><?php echo $krs->Tugas5->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tugas5"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Tugas5) ?>',1);"><div id="elh_krs_Tugas5" class="krs_Tugas5">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Tugas5->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->Tugas5->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Tugas5->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Presensi->Visible) { // Presensi ?>
	<?php if ($krs->SortUrl($krs->Presensi) == "") { ?>
		<th data-name="Presensi"><div id="elh_krs_Presensi" class="krs_Presensi"><div class="ewTableHeaderCaption"><?php echo $krs->Presensi->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Presensi"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Presensi) ?>',1);"><div id="elh_krs_Presensi" class="krs_Presensi">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Presensi->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->Presensi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Presensi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->_Presensi->Visible) { // _Presensi ?>
	<?php if ($krs->SortUrl($krs->_Presensi) == "") { ?>
		<th data-name="_Presensi"><div id="elh_krs__Presensi" class="krs__Presensi"><div class="ewTableHeaderCaption"><?php echo $krs->_Presensi->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="_Presensi"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->_Presensi) ?>',1);"><div id="elh_krs__Presensi" class="krs__Presensi">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->_Presensi->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->_Presensi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->_Presensi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->UTS->Visible) { // UTS ?>
	<?php if ($krs->SortUrl($krs->UTS) == "") { ?>
		<th data-name="UTS"><div id="elh_krs_UTS" class="krs_UTS"><div class="ewTableHeaderCaption"><?php echo $krs->UTS->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="UTS"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->UTS) ?>',1);"><div id="elh_krs_UTS" class="krs_UTS">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->UTS->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->UTS->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->UTS->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->UAS->Visible) { // UAS ?>
	<?php if ($krs->SortUrl($krs->UAS) == "") { ?>
		<th data-name="UAS"><div id="elh_krs_UAS" class="krs_UAS"><div class="ewTableHeaderCaption"><?php echo $krs->UAS->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="UAS"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->UAS) ?>',1);"><div id="elh_krs_UAS" class="krs_UAS">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->UAS->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->UAS->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->UAS->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Responsi->Visible) { // Responsi ?>
	<?php if ($krs->SortUrl($krs->Responsi) == "") { ?>
		<th data-name="Responsi"><div id="elh_krs_Responsi" class="krs_Responsi"><div class="ewTableHeaderCaption"><?php echo $krs->Responsi->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Responsi"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Responsi) ?>',1);"><div id="elh_krs_Responsi" class="krs_Responsi">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Responsi->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->Responsi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Responsi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->NilaiAkhir->Visible) { // NilaiAkhir ?>
	<?php if ($krs->SortUrl($krs->NilaiAkhir) == "") { ?>
		<th data-name="NilaiAkhir"><div id="elh_krs_NilaiAkhir" class="krs_NilaiAkhir"><div class="ewTableHeaderCaption"><?php echo $krs->NilaiAkhir->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NilaiAkhir"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->NilaiAkhir) ?>',1);"><div id="elh_krs_NilaiAkhir" class="krs_NilaiAkhir">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->NilaiAkhir->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->NilaiAkhir->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->NilaiAkhir->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->GradeNilai->Visible) { // GradeNilai ?>
	<?php if ($krs->SortUrl($krs->GradeNilai) == "") { ?>
		<th data-name="GradeNilai"><div id="elh_krs_GradeNilai" class="krs_GradeNilai"><div class="ewTableHeaderCaption"><?php echo $krs->GradeNilai->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="GradeNilai"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->GradeNilai) ?>',1);"><div id="elh_krs_GradeNilai" class="krs_GradeNilai">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->GradeNilai->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($krs->GradeNilai->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->GradeNilai->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->BobotNilai->Visible) { // BobotNilai ?>
	<?php if ($krs->SortUrl($krs->BobotNilai) == "") { ?>
		<th data-name="BobotNilai"><div id="elh_krs_BobotNilai" class="krs_BobotNilai"><div class="ewTableHeaderCaption"><?php echo $krs->BobotNilai->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="BobotNilai"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->BobotNilai) ?>',1);"><div id="elh_krs_BobotNilai" class="krs_BobotNilai">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->BobotNilai->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->BobotNilai->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->BobotNilai->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->StatusKRSID->Visible) { // StatusKRSID ?>
	<?php if ($krs->SortUrl($krs->StatusKRSID) == "") { ?>
		<th data-name="StatusKRSID"><div id="elh_krs_StatusKRSID" class="krs_StatusKRSID"><div class="ewTableHeaderCaption"><?php echo $krs->StatusKRSID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="StatusKRSID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->StatusKRSID) ?>',1);"><div id="elh_krs_StatusKRSID" class="krs_StatusKRSID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->StatusKRSID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($krs->StatusKRSID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->StatusKRSID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Tinggi->Visible) { // Tinggi ?>
	<?php if ($krs->SortUrl($krs->Tinggi) == "") { ?>
		<th data-name="Tinggi"><div id="elh_krs_Tinggi" class="krs_Tinggi"><div class="ewTableHeaderCaption"><?php echo $krs->Tinggi->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tinggi"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Tinggi) ?>',1);"><div id="elh_krs_Tinggi" class="krs_Tinggi">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Tinggi->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($krs->Tinggi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Tinggi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Final->Visible) { // Final ?>
	<?php if ($krs->SortUrl($krs->Final) == "") { ?>
		<th data-name="Final"><div id="elh_krs_Final" class="krs_Final"><div class="ewTableHeaderCaption"><?php echo $krs->Final->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Final"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Final) ?>',1);"><div id="elh_krs_Final" class="krs_Final">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Final->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->Final->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Final->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Setara->Visible) { // Setara ?>
	<?php if ($krs->SortUrl($krs->Setara) == "") { ?>
		<th data-name="Setara"><div id="elh_krs_Setara" class="krs_Setara"><div class="ewTableHeaderCaption"><?php echo $krs->Setara->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Setara"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Setara) ?>',1);"><div id="elh_krs_Setara" class="krs_Setara">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Setara->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->Setara->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Setara->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Creator->Visible) { // Creator ?>
	<?php if ($krs->SortUrl($krs->Creator) == "") { ?>
		<th data-name="Creator"><div id="elh_krs_Creator" class="krs_Creator"><div class="ewTableHeaderCaption"><?php echo $krs->Creator->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Creator"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Creator) ?>',1);"><div id="elh_krs_Creator" class="krs_Creator">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Creator->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($krs->Creator->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Creator->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->CreateDate->Visible) { // CreateDate ?>
	<?php if ($krs->SortUrl($krs->CreateDate) == "") { ?>
		<th data-name="CreateDate"><div id="elh_krs_CreateDate" class="krs_CreateDate"><div class="ewTableHeaderCaption"><?php echo $krs->CreateDate->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="CreateDate"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->CreateDate) ?>',1);"><div id="elh_krs_CreateDate" class="krs_CreateDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->CreateDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->CreateDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->CreateDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->Editor->Visible) { // Editor ?>
	<?php if ($krs->SortUrl($krs->Editor) == "") { ?>
		<th data-name="Editor"><div id="elh_krs_Editor" class="krs_Editor"><div class="ewTableHeaderCaption"><?php echo $krs->Editor->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Editor"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->Editor) ?>',1);"><div id="elh_krs_Editor" class="krs_Editor">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->Editor->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($krs->Editor->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->Editor->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->EditDate->Visible) { // EditDate ?>
	<?php if ($krs->SortUrl($krs->EditDate) == "") { ?>
		<th data-name="EditDate"><div id="elh_krs_EditDate" class="krs_EditDate"><div class="ewTableHeaderCaption"><?php echo $krs->EditDate->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="EditDate"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->EditDate) ?>',1);"><div id="elh_krs_EditDate" class="krs_EditDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->EditDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->EditDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->EditDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($krs->NA->Visible) { // NA ?>
	<?php if ($krs->SortUrl($krs->NA) == "") { ?>
		<th data-name="NA"><div id="elh_krs_NA" class="krs_NA"><div class="ewTableHeaderCaption"><?php echo $krs->NA->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NA"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $krs->SortUrl($krs->NA) ?>',1);"><div id="elh_krs_NA" class="krs_NA">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $krs->NA->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($krs->NA->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($krs->NA->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$krs_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($krs->ExportAll && $krs->Export <> "") {
	$krs_list->StopRec = $krs_list->TotalRecs;
} else {

	// Set the last record to display
	if ($krs_list->TotalRecs > $krs_list->StartRec + $krs_list->DisplayRecs - 1)
		$krs_list->StopRec = $krs_list->StartRec + $krs_list->DisplayRecs - 1;
	else
		$krs_list->StopRec = $krs_list->TotalRecs;
}
$krs_list->RecCnt = $krs_list->StartRec - 1;
if ($krs_list->Recordset && !$krs_list->Recordset->EOF) {
	$krs_list->Recordset->MoveFirst();
	$bSelectLimit = $krs_list->UseSelectLimit;
	if (!$bSelectLimit && $krs_list->StartRec > 1)
		$krs_list->Recordset->Move($krs_list->StartRec - 1);
} elseif (!$krs->AllowAddDeleteRow && $krs_list->StopRec == 0) {
	$krs_list->StopRec = $krs->GridAddRowCount;
}

// Initialize aggregate
$krs->RowType = EW_ROWTYPE_AGGREGATEINIT;
$krs->ResetAttrs();
$krs_list->RenderRow();
while ($krs_list->RecCnt < $krs_list->StopRec) {
	$krs_list->RecCnt++;
	if (intval($krs_list->RecCnt) >= intval($krs_list->StartRec)) {
		$krs_list->RowCnt++;

		// Set up key count
		$krs_list->KeyCount = $krs_list->RowIndex;

		// Init row class and style
		$krs->ResetAttrs();
		$krs->CssClass = "";
		if ($krs->CurrentAction == "gridadd") {
		} else {
			$krs_list->LoadRowValues($krs_list->Recordset); // Load row values
		}
		$krs->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$krs->RowAttrs = array_merge($krs->RowAttrs, array('data-rowindex'=>$krs_list->RowCnt, 'id'=>'r' . $krs_list->RowCnt . '_krs', 'data-rowtype'=>$krs->RowType));

		// Render row
		$krs_list->RenderRow();

		// Render list options
		$krs_list->RenderListOptions();
?>
	<tr<?php echo $krs->RowAttributes() ?>>
<?php

// Render list options (body, left)
$krs_list->ListOptions->Render("body", "left", $krs_list->RowCnt);
?>
	<?php if ($krs->KRSID->Visible) { // KRSID ?>
		<td data-name="KRSID"<?php echo $krs->KRSID->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_KRSID" class="krs_KRSID">
<span<?php echo $krs->KRSID->ViewAttributes() ?>>
<?php echo $krs->KRSID->ListViewValue() ?></span>
</span>
<a id="<?php echo $krs_list->PageObjName . "_row_" . $krs_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($krs->KHSID->Visible) { // KHSID ?>
		<td data-name="KHSID"<?php echo $krs->KHSID->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_KHSID" class="krs_KHSID">
<span<?php echo $krs->KHSID->ViewAttributes() ?>>
<?php echo $krs->KHSID->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->StudentID->Visible) { // StudentID ?>
		<td data-name="StudentID"<?php echo $krs->StudentID->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_StudentID" class="krs_StudentID">
<span<?php echo $krs->StudentID->ViewAttributes() ?>>
<?php echo $krs->StudentID->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->TahunID->Visible) { // TahunID ?>
		<td data-name="TahunID"<?php echo $krs->TahunID->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_TahunID" class="krs_TahunID">
<span<?php echo $krs->TahunID->ViewAttributes() ?>>
<?php echo $krs->TahunID->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Sesi->Visible) { // Sesi ?>
		<td data-name="Sesi"<?php echo $krs->Sesi->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Sesi" class="krs_Sesi">
<span<?php echo $krs->Sesi->ViewAttributes() ?>>
<?php echo $krs->Sesi->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->JadwalID->Visible) { // JadwalID ?>
		<td data-name="JadwalID"<?php echo $krs->JadwalID->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_JadwalID" class="krs_JadwalID">
<span<?php echo $krs->JadwalID->ViewAttributes() ?>>
<?php echo $krs->JadwalID->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->MKID->Visible) { // MKID ?>
		<td data-name="MKID"<?php echo $krs->MKID->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_MKID" class="krs_MKID">
<span<?php echo $krs->MKID->ViewAttributes() ?>>
<?php echo $krs->MKID->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->MKKode->Visible) { // MKKode ?>
		<td data-name="MKKode"<?php echo $krs->MKKode->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_MKKode" class="krs_MKKode">
<span<?php echo $krs->MKKode->ViewAttributes() ?>>
<?php echo $krs->MKKode->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->SKS->Visible) { // SKS ?>
		<td data-name="SKS"<?php echo $krs->SKS->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_SKS" class="krs_SKS">
<span<?php echo $krs->SKS->ViewAttributes() ?>>
<?php echo $krs->SKS->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Tugas1->Visible) { // Tugas1 ?>
		<td data-name="Tugas1"<?php echo $krs->Tugas1->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Tugas1" class="krs_Tugas1">
<span<?php echo $krs->Tugas1->ViewAttributes() ?>>
<?php echo $krs->Tugas1->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Tugas2->Visible) { // Tugas2 ?>
		<td data-name="Tugas2"<?php echo $krs->Tugas2->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Tugas2" class="krs_Tugas2">
<span<?php echo $krs->Tugas2->ViewAttributes() ?>>
<?php echo $krs->Tugas2->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Tugas3->Visible) { // Tugas3 ?>
		<td data-name="Tugas3"<?php echo $krs->Tugas3->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Tugas3" class="krs_Tugas3">
<span<?php echo $krs->Tugas3->ViewAttributes() ?>>
<?php echo $krs->Tugas3->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Tugas4->Visible) { // Tugas4 ?>
		<td data-name="Tugas4"<?php echo $krs->Tugas4->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Tugas4" class="krs_Tugas4">
<span<?php echo $krs->Tugas4->ViewAttributes() ?>>
<?php echo $krs->Tugas4->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Tugas5->Visible) { // Tugas5 ?>
		<td data-name="Tugas5"<?php echo $krs->Tugas5->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Tugas5" class="krs_Tugas5">
<span<?php echo $krs->Tugas5->ViewAttributes() ?>>
<?php echo $krs->Tugas5->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Presensi->Visible) { // Presensi ?>
		<td data-name="Presensi"<?php echo $krs->Presensi->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Presensi" class="krs_Presensi">
<span<?php echo $krs->Presensi->ViewAttributes() ?>>
<?php echo $krs->Presensi->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->_Presensi->Visible) { // _Presensi ?>
		<td data-name="_Presensi"<?php echo $krs->_Presensi->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs__Presensi" class="krs__Presensi">
<span<?php echo $krs->_Presensi->ViewAttributes() ?>>
<?php echo $krs->_Presensi->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->UTS->Visible) { // UTS ?>
		<td data-name="UTS"<?php echo $krs->UTS->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_UTS" class="krs_UTS">
<span<?php echo $krs->UTS->ViewAttributes() ?>>
<?php echo $krs->UTS->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->UAS->Visible) { // UAS ?>
		<td data-name="UAS"<?php echo $krs->UAS->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_UAS" class="krs_UAS">
<span<?php echo $krs->UAS->ViewAttributes() ?>>
<?php echo $krs->UAS->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Responsi->Visible) { // Responsi ?>
		<td data-name="Responsi"<?php echo $krs->Responsi->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Responsi" class="krs_Responsi">
<span<?php echo $krs->Responsi->ViewAttributes() ?>>
<?php echo $krs->Responsi->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->NilaiAkhir->Visible) { // NilaiAkhir ?>
		<td data-name="NilaiAkhir"<?php echo $krs->NilaiAkhir->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_NilaiAkhir" class="krs_NilaiAkhir">
<span<?php echo $krs->NilaiAkhir->ViewAttributes() ?>>
<?php echo $krs->NilaiAkhir->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->GradeNilai->Visible) { // GradeNilai ?>
		<td data-name="GradeNilai"<?php echo $krs->GradeNilai->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_GradeNilai" class="krs_GradeNilai">
<span<?php echo $krs->GradeNilai->ViewAttributes() ?>>
<?php echo $krs->GradeNilai->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->BobotNilai->Visible) { // BobotNilai ?>
		<td data-name="BobotNilai"<?php echo $krs->BobotNilai->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_BobotNilai" class="krs_BobotNilai">
<span<?php echo $krs->BobotNilai->ViewAttributes() ?>>
<?php echo $krs->BobotNilai->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->StatusKRSID->Visible) { // StatusKRSID ?>
		<td data-name="StatusKRSID"<?php echo $krs->StatusKRSID->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_StatusKRSID" class="krs_StatusKRSID">
<span<?php echo $krs->StatusKRSID->ViewAttributes() ?>>
<?php echo $krs->StatusKRSID->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Tinggi->Visible) { // Tinggi ?>
		<td data-name="Tinggi"<?php echo $krs->Tinggi->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Tinggi" class="krs_Tinggi">
<span<?php echo $krs->Tinggi->ViewAttributes() ?>>
<?php echo $krs->Tinggi->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Final->Visible) { // Final ?>
		<td data-name="Final"<?php echo $krs->Final->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Final" class="krs_Final">
<span<?php echo $krs->Final->ViewAttributes() ?>>
<?php echo $krs->Final->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Setara->Visible) { // Setara ?>
		<td data-name="Setara"<?php echo $krs->Setara->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Setara" class="krs_Setara">
<span<?php echo $krs->Setara->ViewAttributes() ?>>
<?php echo $krs->Setara->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Creator->Visible) { // Creator ?>
		<td data-name="Creator"<?php echo $krs->Creator->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Creator" class="krs_Creator">
<span<?php echo $krs->Creator->ViewAttributes() ?>>
<?php echo $krs->Creator->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->CreateDate->Visible) { // CreateDate ?>
		<td data-name="CreateDate"<?php echo $krs->CreateDate->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_CreateDate" class="krs_CreateDate">
<span<?php echo $krs->CreateDate->ViewAttributes() ?>>
<?php echo $krs->CreateDate->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->Editor->Visible) { // Editor ?>
		<td data-name="Editor"<?php echo $krs->Editor->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_Editor" class="krs_Editor">
<span<?php echo $krs->Editor->ViewAttributes() ?>>
<?php echo $krs->Editor->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->EditDate->Visible) { // EditDate ?>
		<td data-name="EditDate"<?php echo $krs->EditDate->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_EditDate" class="krs_EditDate">
<span<?php echo $krs->EditDate->ViewAttributes() ?>>
<?php echo $krs->EditDate->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($krs->NA->Visible) { // NA ?>
		<td data-name="NA"<?php echo $krs->NA->CellAttributes() ?>>
<span id="el<?php echo $krs_list->RowCnt ?>_krs_NA" class="krs_NA">
<span<?php echo $krs->NA->ViewAttributes() ?>>
<?php echo $krs->NA->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$krs_list->ListOptions->Render("body", "right", $krs_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($krs->CurrentAction <> "gridadd")
		$krs_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($krs->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($krs_list->Recordset)
	$krs_list->Recordset->Close();
?>
<?php if ($krs->Export == "") { ?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($krs->CurrentAction <> "gridadd" && $krs->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($krs_list->Pager)) $krs_list->Pager = new cPrevNextPager($krs_list->StartRec, $krs_list->DisplayRecs, $krs_list->TotalRecs) ?>
<?php if ($krs_list->Pager->RecordCount > 0 && $krs_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($krs_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $krs_list->PageUrl() ?>start=<?php echo $krs_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($krs_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $krs_list->PageUrl() ?>start=<?php echo $krs_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $krs_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($krs_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $krs_list->PageUrl() ?>start=<?php echo $krs_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($krs_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $krs_list->PageUrl() ?>start=<?php echo $krs_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $krs_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $krs_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $krs_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $krs_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($krs_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($krs_list->TotalRecs == 0 && $krs->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($krs_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($krs->Export == "") { ?>
<script type="text/javascript">
fkrslistsrch.FilterList = <?php echo $krs_list->GetFilterList() ?>;
fkrslistsrch.Init();
fkrslist.Init();
</script>
<?php } ?>
<?php
$krs_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($krs->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$krs_list->Page_Terminate();
?>
