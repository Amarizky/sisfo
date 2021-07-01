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

$kurikulum_list = NULL; // Initialize page object first

class ckurikulum_list extends ckurikulum {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'kurikulum';

	// Page object name
	var $PageObjName = 'kurikulum_list';

	// Grid form hidden field names
	var $FormName = 'fkurikulumlist';
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

		// Table object (kurikulum)
		if (!isset($GLOBALS["kurikulum"]) || get_class($GLOBALS["kurikulum"]) == "ckurikulum") {
			$GLOBALS["kurikulum"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["kurikulum"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "kurikulumadd.php?" . EW_TABLE_SHOW_DETAIL . "=";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "kurikulumdelete.php";
		$this->MultiUpdateUrl = "kurikulumupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fkurikulumlistsrch";

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
		$this->ProdiID->SetVisibility();
		$this->KurikulumKode->SetVisibility();
		$this->Nama->SetVisibility();
		$this->JmlSesi->SetVisibility();
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
	var $mk_Count;
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
			$this->KurikulumID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->KurikulumID->FormValue))
				return FALSE;
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
					$sKey .= $this->KurikulumID->CurrentValue;

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
		if ($objForm->HasValue("x_ProdiID") && $objForm->HasValue("o_ProdiID") && $this->ProdiID->CurrentValue <> $this->ProdiID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_KurikulumKode") && $objForm->HasValue("o_KurikulumKode") && $this->KurikulumKode->CurrentValue <> $this->KurikulumKode->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_Nama") && $objForm->HasValue("o_Nama") && $this->Nama->CurrentValue <> $this->Nama->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_JmlSesi") && $objForm->HasValue("o_JmlSesi") && $this->JmlSesi->CurrentValue <> $this->JmlSesi->OldValue)
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
			$sSavedFilterList = isset($UserProfile) ? $UserProfile->GetSearchFilters(CurrentUserName(), "fkurikulumlistsrch") : "";
		} else {
			$sSavedFilterList = "";
		}

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->KampusID->AdvancedSearch->ToJSON(), ","); // Field KampusID
		$sFilterList = ew_Concat($sFilterList, $this->ProdiID->AdvancedSearch->ToJSON(), ","); // Field ProdiID
		$sFilterList = ew_Concat($sFilterList, $this->KurikulumKode->AdvancedSearch->ToJSON(), ","); // Field KurikulumKode
		$sFilterList = ew_Concat($sFilterList, $this->Nama->AdvancedSearch->ToJSON(), ","); // Field Nama
		$sFilterList = ew_Concat($sFilterList, $this->JmlSesi->AdvancedSearch->ToJSON(), ","); // Field JmlSesi
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
			$UserProfile->SetSearchFilters(CurrentUserName(), "fkurikulumlistsrch", $filters);

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

		// Field KampusID
		$this->KampusID->AdvancedSearch->SearchValue = @$filter["x_KampusID"];
		$this->KampusID->AdvancedSearch->SearchOperator = @$filter["z_KampusID"];
		$this->KampusID->AdvancedSearch->SearchCondition = @$filter["v_KampusID"];
		$this->KampusID->AdvancedSearch->SearchValue2 = @$filter["y_KampusID"];
		$this->KampusID->AdvancedSearch->SearchOperator2 = @$filter["w_KampusID"];
		$this->KampusID->AdvancedSearch->Save();

		// Field ProdiID
		$this->ProdiID->AdvancedSearch->SearchValue = @$filter["x_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchOperator = @$filter["z_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchCondition = @$filter["v_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchValue2 = @$filter["y_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchOperator2 = @$filter["w_ProdiID"];
		$this->ProdiID->AdvancedSearch->Save();

		// Field KurikulumKode
		$this->KurikulumKode->AdvancedSearch->SearchValue = @$filter["x_KurikulumKode"];
		$this->KurikulumKode->AdvancedSearch->SearchOperator = @$filter["z_KurikulumKode"];
		$this->KurikulumKode->AdvancedSearch->SearchCondition = @$filter["v_KurikulumKode"];
		$this->KurikulumKode->AdvancedSearch->SearchValue2 = @$filter["y_KurikulumKode"];
		$this->KurikulumKode->AdvancedSearch->SearchOperator2 = @$filter["w_KurikulumKode"];
		$this->KurikulumKode->AdvancedSearch->Save();

		// Field Nama
		$this->Nama->AdvancedSearch->SearchValue = @$filter["x_Nama"];
		$this->Nama->AdvancedSearch->SearchOperator = @$filter["z_Nama"];
		$this->Nama->AdvancedSearch->SearchCondition = @$filter["v_Nama"];
		$this->Nama->AdvancedSearch->SearchValue2 = @$filter["y_Nama"];
		$this->Nama->AdvancedSearch->SearchOperator2 = @$filter["w_Nama"];
		$this->Nama->AdvancedSearch->Save();

		// Field JmlSesi
		$this->JmlSesi->AdvancedSearch->SearchValue = @$filter["x_JmlSesi"];
		$this->JmlSesi->AdvancedSearch->SearchOperator = @$filter["z_JmlSesi"];
		$this->JmlSesi->AdvancedSearch->SearchCondition = @$filter["v_JmlSesi"];
		$this->JmlSesi->AdvancedSearch->SearchValue2 = @$filter["y_JmlSesi"];
		$this->JmlSesi->AdvancedSearch->SearchOperator2 = @$filter["w_JmlSesi"];
		$this->JmlSesi->AdvancedSearch->Save();

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
		$this->BuildSearchSql($sWhere, $this->KampusID, $Default, FALSE); // KampusID
		$this->BuildSearchSql($sWhere, $this->ProdiID, $Default, FALSE); // ProdiID
		$this->BuildSearchSql($sWhere, $this->KurikulumKode, $Default, FALSE); // KurikulumKode
		$this->BuildSearchSql($sWhere, $this->Nama, $Default, FALSE); // Nama
		$this->BuildSearchSql($sWhere, $this->JmlSesi, $Default, FALSE); // JmlSesi
		$this->BuildSearchSql($sWhere, $this->NA, $Default, FALSE); // NA

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->KampusID->AdvancedSearch->Save(); // KampusID
			$this->ProdiID->AdvancedSearch->Save(); // ProdiID
			$this->KurikulumKode->AdvancedSearch->Save(); // KurikulumKode
			$this->Nama->AdvancedSearch->Save(); // Nama
			$this->JmlSesi->AdvancedSearch->Save(); // JmlSesi
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
		$this->BuildBasicSearchSQL($sWhere, $this->KampusID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->ProdiID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KurikulumKode, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Nama, $arKeywords, $type);
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
		if ($this->KampusID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->ProdiID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KurikulumKode->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Nama->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->JmlSesi->AdvancedSearch->IssetSession())
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
		$this->KampusID->AdvancedSearch->UnsetSession();
		$this->ProdiID->AdvancedSearch->UnsetSession();
		$this->KurikulumKode->AdvancedSearch->UnsetSession();
		$this->Nama->AdvancedSearch->UnsetSession();
		$this->JmlSesi->AdvancedSearch->UnsetSession();
		$this->NA->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->KampusID->AdvancedSearch->Load();
		$this->ProdiID->AdvancedSearch->Load();
		$this->KurikulumKode->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->JmlSesi->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->ProdiID); // ProdiID
			$this->UpdateSort($this->KurikulumKode); // KurikulumKode
			$this->UpdateSort($this->Nama); // Nama
			$this->UpdateSort($this->JmlSesi); // JmlSesi
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
				$this->ProdiID->setSort("");
				$this->KurikulumKode->setSort("");
				$this->Nama->setSort("");
				$this->JmlSesi->setSort("");
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

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanAdd();
		$item->OnLeft = TRUE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanDelete();
		$item->OnLeft = TRUE;

		// "detail_mk"
		$item = &$this->ListOptions->Add("detail_mk");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->AllowList(CurrentProjectID() . 'mk') && !$this->ShowMultipleDetails;
		$item->OnLeft = TRUE;
		$item->ShowInButtonGroup = FALSE;
		if (!isset($GLOBALS["mk_grid"])) $GLOBALS["mk_grid"] = new cmk_grid;

		// Multiple details
		if ($this->ShowMultipleDetails) {
			$item = &$this->ListOptions->Add("details");
			$item->CssStyle = "white-space: nowrap;";
			$item->Visible = $this->ShowMultipleDetails;
			$item->OnLeft = TRUE;
			$item->ShowInButtonGroup = FALSE;
		}

		// Set up detail pages
		$pages = new cSubPages();
		$pages->Add("mk");
		$this->DetailPages = $pages;

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

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		$copycaption = ew_HtmlTitle($Language->Phrase("CopyLink"));
		if ($Security->CanAdd()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewCopy\" title=\"" . $copycaption . "\" data-caption=\"" . $copycaption . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "</a>";
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
		$DetailViewTblVar = "";
		$DetailCopyTblVar = "";
		$DetailEditTblVar = "";

		// "detail_mk"
		$oListOpt = &$this->ListOptions->Items["detail_mk"];
		if ($Security->AllowList(CurrentProjectID() . 'mk')) {
			$body = $Language->Phrase("DetailLink") . $Language->TablePhrase("mk", "TblCaption");
			$body .= str_replace("%c", $this->mk_Count, $Language->Phrase("DetailCount"));
			$body = "<a class=\"btn btn-default btn-sm ewRowLink ewDetail\" data-action=\"list\" href=\"" . ew_HtmlEncode("mklist.php?" . EW_TABLE_SHOW_MASTER . "=kurikulum&fk_KurikulumID=" . urlencode(strval($this->KurikulumID->CurrentValue)) . "&fk_ProdiID=" . urlencode(strval($this->ProdiID->CurrentValue)) . "&fk_KampusID=" . urlencode(strval($this->KampusID->CurrentValue)) . "") . "\">" . $body . "</a>";
			$links = "";
			if ($GLOBALS["mk_grid"]->DetailView && $Security->CanView() && $Security->AllowView(CurrentProjectID() . 'mk')) {
				$links .= "<li><a class=\"ewRowLink ewDetailView\" data-action=\"view\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailViewLink")) . "\" href=\"" . ew_HtmlEncode($this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=mk")) . "\">" . ew_HtmlImageAndText($Language->Phrase("MasterDetailViewLink")) . "</a></li>";
				if ($DetailViewTblVar <> "") $DetailViewTblVar .= ",";
				$DetailViewTblVar .= "mk";
			}
			if ($GLOBALS["mk_grid"]->DetailEdit && $Security->CanEdit() && $Security->AllowEdit(CurrentProjectID() . 'mk')) {
				$links .= "<li><a class=\"ewRowLink ewDetailEdit\" data-action=\"edit\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailEditLink")) . "\" href=\"" . ew_HtmlEncode($this->GetEditUrl(EW_TABLE_SHOW_DETAIL . "=mk")) . "\">" . ew_HtmlImageAndText($Language->Phrase("MasterDetailEditLink")) . "</a></li>";
				if ($DetailEditTblVar <> "") $DetailEditTblVar .= ",";
				$DetailEditTblVar .= "mk";
			}
			if ($GLOBALS["mk_grid"]->DetailAdd && $Security->CanAdd() && $Security->AllowAdd(CurrentProjectID() . 'mk')) {
				$links .= "<li><a class=\"ewRowLink ewDetailCopy\" data-action=\"add\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailCopyLink")) . "\" href=\"" . ew_HtmlEncode($this->GetCopyUrl(EW_TABLE_SHOW_DETAIL . "=mk")) . "\">" . ew_HtmlImageAndText($Language->Phrase("MasterDetailCopyLink")) . "</a></li>";
				if ($DetailCopyTblVar <> "") $DetailCopyTblVar .= ",";
				$DetailCopyTblVar .= "mk";
			}
			if ($links <> "") {
				$body .= "<button class=\"dropdown-toggle btn btn-default btn-sm ewDetail\" data-toggle=\"dropdown\"><b class=\"caret\"></b></button>";
				$body .= "<ul class=\"dropdown-menu\">". $links . "</ul>";
			}
			$body = "<div class=\"btn-group\">" . $body . "</div>";
			$oListOpt->Body = $body;
			if ($this->ShowMultipleDetails) $oListOpt->Visible = FALSE;
		}
		if ($this->ShowMultipleDetails) {
			$body = $Language->Phrase("MultipleMasterDetails");
			$body = "<div class=\"btn-group\">";
			$links = "";
			if ($DetailViewTblVar <> "") {
				$links .= "<li><a class=\"ewRowLink ewDetailView\" data-action=\"view\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailViewLink")) . "\" href=\"" . ew_HtmlEncode($this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailViewTblVar)) . "\">" . ew_HtmlImageAndText($Language->Phrase("MasterDetailViewLink")) . "</a></li>";
			}
			if ($DetailEditTblVar <> "") {
				$links .= "<li><a class=\"ewRowLink ewDetailEdit\" data-action=\"edit\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailEditLink")) . "\" href=\"" . ew_HtmlEncode($this->GetEditUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailEditTblVar)) . "\">" . ew_HtmlImageAndText($Language->Phrase("MasterDetailEditLink")) . "</a></li>";
			}
			if ($DetailCopyTblVar <> "") {
				$links .= "<li><a class=\"ewRowLink ewDetailCopy\" data-action=\"add\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailCopyLink")) . "\" href=\"" . ew_HtmlEncode($this->GetCopyUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailCopyTblVar)) . "\">" . ew_HtmlImageAndText($Language->Phrase("MasterDetailCopyLink")) . "</a></li>";
			}
			if ($links <> "") {
				$body .= "<button class=\"dropdown-toggle btn btn-default btn-sm ewMasterDetail\" title=\"" . ew_HtmlTitle($Language->Phrase("MultipleMasterDetails")) . "\" data-toggle=\"dropdown\">" . $Language->Phrase("MultipleMasterDetails") . "<b class=\"caret\"></b></button>";
				$body .= "<ul class=\"dropdown-menu ewMenu\">". $links . "</ul>";
			}
			$body .= "</div>";

			// Multiple details
			$oListOpt = &$this->ListOptions->Items["details"];
			$oListOpt->Body = $body;
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->KurikulumID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event);'>";
		if ($this->CurrentAction == "gridedit" && is_numeric($this->RowIndex)) {
			$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $KeyName . "\" id=\"" . $KeyName . "\" value=\"" . $this->KurikulumID->CurrentValue . "\">";
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
		$option = $options["detail"];
		$DetailTableLink = "";
		$item = &$option->Add("detailadd_mk");
		$url = $this->GetAddUrl(EW_TABLE_SHOW_DETAIL . "=mk");
		$caption = $Language->Phrase("Add") . "&nbsp;" . $this->TableCaption() . "/" . $GLOBALS["mk"]->TableCaption();
		$item->Body = "<a class=\"ewDetailAddGroup ewDetailAdd\" title=\"" . ew_HtmlTitle($caption) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"" . ew_HtmlEncode($url) . "\">" . $caption . "</a>";
		$item->Visible = ($GLOBALS["mk"]->DetailAdd && $Security->AllowAdd(CurrentProjectID() . 'mk') && $Security->CanAdd());
		if ($item->Visible) {
			if ($DetailTableLink <> "") $DetailTableLink .= ",";
			$DetailTableLink .= "mk";
		}

		// Add multiple details
		if ($this->ShowMultipleDetails) {
			$item = &$option->Add("detailsadd");
			$url = $this->GetAddUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailTableLink);
			$item->Body = "<a class=\"ewDetailAddGroup ewDetailAdd\" title=\"" . ew_HtmlTitle($Language->Phrase("AddMasterDetailLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("AddMasterDetailLink")) . "\" href=\"" . ew_HtmlEncode($url) . "\">" . $Language->Phrase("AddMasterDetailLink") . "</a>";
			$item->Visible = ($DetailTableLink <> "" && $Security->CanAdd());

			// Hide single master/detail items
			$ar = explode(",", $DetailTableLink);
			$cnt = count($ar);
			for ($i = 0; $i < $cnt; $i++) {
				if ($item = &$option->GetItem("detailadd_" . $ar[$i]))
					$item->Visible = FALSE;
			}
		}

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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fkurikulumlistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fkurikulumlistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
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
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fkurikulumlist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fkurikulumlistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		if (ew_IsMobile())
			$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"kurikulumsrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		else
			$item->Body = "<button type=\"button\" class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-table=\"kurikulum\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" onclick=\"ew_ModalDialogShow({lnk:this,url:'kurikulumsrch.php',caption:'" . $Language->Phrase("Search") . "'});\">" . $Language->Phrase("AdvancedSearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Search highlight button
		$item = &$this->SearchOptions->Add("searchhighlight");
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewHighlight active\" title=\"" . $Language->Phrase("Highlight") . "\" data-caption=\"" . $Language->Phrase("Highlight") . "\" data-toggle=\"button\" data-form=\"fkurikulumlistsrch\" data-name=\"" . $this->HighlightName() . "\">" . $Language->Phrase("HighlightBtn") . "</button>";
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
		$links = "";
		$btngrps = "";
		$sSqlWrk = "`KurikulumID`=" . ew_AdjustSql($this->KurikulumID->CurrentValue, $this->DBID) . "";
		$sSqlWrk = $sSqlWrk . " AND " . "`ProdiID`='" . ew_AdjustSql($this->ProdiID->CurrentValue, $this->DBID) . "'";
		$sSqlWrk = $sSqlWrk . " AND " . "`KampusID`='" . ew_AdjustSql($this->KampusID->CurrentValue, $this->DBID) . "'";

		// Column "detail_mk"
		if ($this->DetailPages->Items["mk"]->Visible) {
			$link = "";
			$option = &$this->ListOptions->Items["detail_mk"];
			$url = "mkpreview.php?t=kurikulum&f=" . ew_Encrypt($sSqlWrk);
			$btngrp = "<div data-table=\"mk\" data-url=\"" . $url . "\">";
			if ($Security->AllowList(CurrentProjectID() . 'mk')) {			
				$label = $Language->TablePhrase("mk", "TblCaption");
				$label .= "&nbsp;" . ew_JsEncode2(str_replace("%c", $this->mk_Count, $Language->Phrase("DetailCount")));		
				$link = "<li><a href=\"#\" data-toggle=\"tab\" data-table=\"mk\" data-url=\"" . $url . "\">" . $label . "</a></li>";			
				$links .= $link;
				$detaillnk = ew_JsEncode3("mklist.php?" . EW_TABLE_SHOW_MASTER . "=kurikulum&fk_KurikulumID=" . urlencode(strval($this->KurikulumID->CurrentValue)) . "&fk_ProdiID=" . urlencode(strval($this->ProdiID->CurrentValue)) . "&fk_KampusID=" . urlencode(strval($this->KampusID->CurrentValue)) . "");
				$btngrp .= "<a href=\"javascript:void(0);\" class=\"ewLinkSeparator\" title=\"" . $Language->TablePhrase("mk", "TblCaption") . "\" onclick=\"window.location='" . $detaillnk . "'\">" . $Language->Phrase("MasterDetailListLink") . "</a>";
			}
			if ($GLOBALS["mk_grid"]->DetailView && $Security->CanView() && $Security->AllowView(CurrentProjectID() . 'mk'))
				$btngrp .= "<a href=\"javascript:void(0);\" class=\"ewLinkSeparator\" title=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailViewLink")) . "\" onclick=\"window.location='" . $this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=mk") . "'\">" . $Language->Phrase("MasterDetailViewLink") . "</a>";
			if ($GLOBALS["mk_grid"]->DetailEdit && $Security->CanEdit() && $Security->AllowEdit(CurrentProjectID() . 'mk'))
				$btngrp .= "<a href=\"javascript:void(0);\" class=\"ewLinkSeparator\" title=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailEditLink")) . "\" onclick=\"window.location='" . $this->GetEditUrl(EW_TABLE_SHOW_DETAIL . "=mk") . "'\">" . $Language->Phrase("MasterDetailEditLink") . "</a>";
			if ($GLOBALS["mk_grid"]->DetailAdd && $Security->CanAdd() && $Security->AllowAdd(CurrentProjectID() . 'mk'))
				$btngrp .= "<a href=\"javascript:void(0);\" class=\"ewLinkSeparator\" title=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailCopyLink")) . "\" onclick=\"window.location='" . $this->GetCopyUrl(EW_TABLE_SHOW_DETAIL . "=mk") . "'\">" . $Language->Phrase("MasterDetailCopyLink") . "</a>";
			$btngrp .= "</div>";
			if ($link <> "") {
				$btngrps .= $btngrp;
				$option->Body .= "<div class=\"hide ewPreview\">" . $link . $btngrp . "</div>";
			}
		}

		// Hide detail items if necessary
		$this->ListOptions->HideDetailItemsForDropDown();

		// Column "preview"
		$option = &$this->ListOptions->GetItem("preview");
		if (!$option) { // Add preview column
			$option = &$this->ListOptions->Add("preview");
			$option->OnLeft = TRUE;
			if ($option->OnLeft) {
				$option->MoveTo($this->ListOptions->ItemPos("checkbox") + 1);
			} else {
				$option->MoveTo($this->ListOptions->ItemPos("checkbox"));
			}
			$option->Visible = !($this->Export <> "" || $this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit");
			$option->ShowInDropDown = FALSE;
			$option->ShowInButtonGroup = FALSE;
		}
		if ($option) {
			$option->Body = "<span class=\"ewPreviewRowBtn icon-expand\"></span>";
			$option->Body .= "<div class=\"hide ewPreview\">" . $links . $btngrps . "</div>";
			if ($option->Visible) $option->Visible = $links <> "";
		}

		// Column "details" (Multiple details)
		$option = &$this->ListOptions->GetItem("details");
		if ($option) {
			$option->Body .= "<div class=\"hide ewPreview\">" . $links . $btngrps . "</div>";
			if ($option->Visible) $option->Visible = $links <> "";
		}
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

	// Load default values
	function LoadDefaultValues() {
		$this->ProdiID->CurrentValue = NULL;
		$this->ProdiID->OldValue = $this->ProdiID->CurrentValue;
		$this->KurikulumKode->CurrentValue = NULL;
		$this->KurikulumKode->OldValue = $this->KurikulumKode->CurrentValue;
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->JmlSesi->CurrentValue = 2;
		$this->JmlSesi->OldValue = $this->JmlSesi->CurrentValue;
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
		// KampusID

		$this->KampusID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KampusID"]);
		if ($this->KampusID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KampusID->AdvancedSearch->SearchOperator = @$_GET["z_KampusID"];

		// ProdiID
		$this->ProdiID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_ProdiID"]);
		if ($this->ProdiID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->ProdiID->AdvancedSearch->SearchOperator = @$_GET["z_ProdiID"];

		// KurikulumKode
		$this->KurikulumKode->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KurikulumKode"]);
		if ($this->KurikulumKode->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KurikulumKode->AdvancedSearch->SearchOperator = @$_GET["z_KurikulumKode"];

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Nama"]);
		if ($this->Nama->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Nama->AdvancedSearch->SearchOperator = @$_GET["z_Nama"];

		// JmlSesi
		$this->JmlSesi->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_JmlSesi"]);
		if ($this->JmlSesi->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->JmlSesi->AdvancedSearch->SearchOperator = @$_GET["z_JmlSesi"];

		// NA
		$this->NA->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NA"]);
		if ($this->NA->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NA->AdvancedSearch->SearchOperator = @$_GET["z_NA"];
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->ProdiID->FldIsDetailKey) {
			$this->ProdiID->setFormValue($objForm->GetValue("x_ProdiID"));
		}
		$this->ProdiID->setOldValue($objForm->GetValue("o_ProdiID"));
		if (!$this->KurikulumKode->FldIsDetailKey) {
			$this->KurikulumKode->setFormValue($objForm->GetValue("x_KurikulumKode"));
		}
		$this->KurikulumKode->setOldValue($objForm->GetValue("o_KurikulumKode"));
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		$this->Nama->setOldValue($objForm->GetValue("o_Nama"));
		if (!$this->JmlSesi->FldIsDetailKey) {
			$this->JmlSesi->setFormValue($objForm->GetValue("x_JmlSesi"));
		}
		$this->JmlSesi->setOldValue($objForm->GetValue("o_JmlSesi"));
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
		$this->NA->setOldValue($objForm->GetValue("o_NA"));
		if (!$this->KurikulumID->FldIsDetailKey && $this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->KurikulumID->setFormValue($objForm->GetValue("x_KurikulumID"));
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		if ($this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->KurikulumID->CurrentValue = $this->KurikulumID->FormValue;
		$this->ProdiID->CurrentValue = $this->ProdiID->FormValue;
		$this->KurikulumKode->CurrentValue = $this->KurikulumKode->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->JmlSesi->CurrentValue = $this->JmlSesi->FormValue;
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
		if (!isset($GLOBALS["mk_grid"])) $GLOBALS["mk_grid"] = new cmk_grid;
		$sDetailFilter = $GLOBALS["mk"]->SqlDetailFilter_kurikulum();
		$sDetailFilter = str_replace("@KurikulumID@", ew_AdjustSql($this->KurikulumID->DbValue, "DB"), $sDetailFilter);
		$sDetailFilter = str_replace("@ProdiID@", ew_AdjustSql($this->ProdiID->DbValue, "DB"), $sDetailFilter);
		$sDetailFilter = str_replace("@KampusID@", ew_AdjustSql($this->KampusID->DbValue, "DB"), $sDetailFilter);
		$GLOBALS["mk"]->setCurrentMasterTable("kurikulum");
		$sDetailFilter = $GLOBALS["mk"]->ApplyUserIDFilters($sDetailFilter);
		$this->mk_Count = $GLOBALS["mk"]->LoadRecordCount($sDetailFilter);
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// KurikulumKode
			$this->KurikulumKode->LinkCustomAttributes = "";
			$this->KurikulumKode->HrefValue = "";
			$this->KurikulumKode->TooltipValue = "";
			if ($this->Export == "")
				$this->KurikulumKode->ViewValue = ew_Highlight($this->HighlightName(), $this->KurikulumKode->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->KurikulumKode->AdvancedSearch->getValue("x"), "");

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";
			if ($this->Export == "")
				$this->Nama->ViewValue = ew_Highlight($this->HighlightName(), $this->Nama->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Nama->AdvancedSearch->getValue("x"), "");

			// JmlSesi
			$this->JmlSesi->LinkCustomAttributes = "";
			$this->JmlSesi->HrefValue = "";
			$this->JmlSesi->TooltipValue = "";
			if ($this->Export == "")
				$this->JmlSesi->ViewValue = ew_Highlight($this->HighlightName(), $this->JmlSesi->ViewValue, "", "", $this->JmlSesi->AdvancedSearch->getValue("x"), "");

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

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

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
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

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

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

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Edit refer script
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
				$sThisKey .= $row['KurikulumID'];
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
			$rsnew = array();

			// ProdiID
			$this->ProdiID->SetDbValueDef($rsnew, $this->ProdiID->CurrentValue, "", $this->ProdiID->ReadOnly);

			// KurikulumKode
			$this->KurikulumKode->SetDbValueDef($rsnew, $this->KurikulumKode->CurrentValue, "", $this->KurikulumKode->ReadOnly);

			// Nama
			$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, "", $this->Nama->ReadOnly);

			// JmlSesi
			$this->JmlSesi->SetDbValueDef($rsnew, $this->JmlSesi->CurrentValue, NULL, $this->JmlSesi->ReadOnly);

			// NA
			$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), "N", $this->NA->ReadOnly);

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

	// Add record
	function AddRow($rsold = NULL) {
		global $Language, $Security;
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// ProdiID
		$this->ProdiID->SetDbValueDef($rsnew, $this->ProdiID->CurrentValue, "", FALSE);

		// KurikulumKode
		$this->KurikulumKode->SetDbValueDef($rsnew, $this->KurikulumKode->CurrentValue, "", FALSE);

		// Nama
		$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, "", FALSE);

		// JmlSesi
		$this->JmlSesi->SetDbValueDef($rsnew, $this->JmlSesi->CurrentValue, NULL, strval($this->JmlSesi->CurrentValue) == "");

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

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->KampusID->AdvancedSearch->Load();
		$this->ProdiID->AdvancedSearch->Load();
		$this->KurikulumKode->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->JmlSesi->AdvancedSearch->Load();
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
		$item->Body = "<button id=\"emf_kurikulum\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_kurikulum',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fkurikulumlist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
if (!isset($kurikulum_list)) $kurikulum_list = new ckurikulum_list();

// Page init
$kurikulum_list->Page_Init();

// Page main
$kurikulum_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$kurikulum_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($kurikulum->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fkurikulumlist = new ew_Form("fkurikulumlist", "list");
fkurikulumlist.FormKeyCountName = '<?php echo $kurikulum_list->FormKeyCountName ?>';

// Validate form
fkurikulumlist.Validate = function() {
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
		} // End Grid Add checking
	}
	if (gridinsert && addcnt == 0) { // No row added
		ew_Alert(ewLanguage.Phrase("NoAddRecord"));
		return false;
	}
	return true;
}

// Check empty row
fkurikulumlist.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "ProdiID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "KurikulumKode", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Nama", false)) return false;
	if (ew_ValueChanged(fobj, infix, "JmlSesi", false)) return false;
	if (ew_ValueChanged(fobj, infix, "NA", true)) return false;
	return true;
}

// Form_CustomValidate event
fkurikulumlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fkurikulumlist.ValidateRequired = true;
<?php } else { ?>
fkurikulumlist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fkurikulumlist.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fkurikulumlist.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkurikulumlist.Lists["x_NA"].Options = <?php echo json_encode($kurikulum->NA->Options()) ?>;

// Form object for search
var CurrentSearchForm = fkurikulumlistsrch = new ew_Form("fkurikulumlistsrch");

// Init search panel as collapsed
if (fkurikulumlistsrch) fkurikulumlistsrch.InitSearchPanel = true;
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
<?php if ($kurikulum->Export == "") { ?>
<div class="ewToolbar">
<?php if ($kurikulum->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($kurikulum_list->TotalRecs > 0 && $kurikulum_list->ExportOptions->Visible()) { ?>
<?php $kurikulum_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($kurikulum_list->SearchOptions->Visible()) { ?>
<?php $kurikulum_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($kurikulum_list->FilterOptions->Visible()) { ?>
<?php $kurikulum_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php if ($kurikulum->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
if ($kurikulum->CurrentAction == "gridadd") {
	$kurikulum->CurrentFilter = "0=1";
	$kurikulum_list->StartRec = 1;
	$kurikulum_list->DisplayRecs = $kurikulum->GridAddRowCount;
	$kurikulum_list->TotalRecs = $kurikulum_list->DisplayRecs;
	$kurikulum_list->StopRec = $kurikulum_list->DisplayRecs;
} else {
	$bSelectLimit = $kurikulum_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($kurikulum_list->TotalRecs <= 0)
			$kurikulum_list->TotalRecs = $kurikulum->SelectRecordCount();
	} else {
		if (!$kurikulum_list->Recordset && ($kurikulum_list->Recordset = $kurikulum_list->LoadRecordset()))
			$kurikulum_list->TotalRecs = $kurikulum_list->Recordset->RecordCount();
	}
	$kurikulum_list->StartRec = 1;
	if ($kurikulum_list->DisplayRecs <= 0 || ($kurikulum->Export <> "" && $kurikulum->ExportAll)) // Display all records
		$kurikulum_list->DisplayRecs = $kurikulum_list->TotalRecs;
	if (!($kurikulum->Export <> "" && $kurikulum->ExportAll))
		$kurikulum_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$kurikulum_list->Recordset = $kurikulum_list->LoadRecordset($kurikulum_list->StartRec-1, $kurikulum_list->DisplayRecs);

	// Set no record found message
	if ($kurikulum->CurrentAction == "" && $kurikulum_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$kurikulum_list->setWarningMessage(ew_DeniedMsg());
		if ($kurikulum_list->SearchWhere == "0=101")
			$kurikulum_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$kurikulum_list->setWarningMessage($Language->Phrase("NoRecord"));
	}

	// Audit trail on search
	if ($kurikulum_list->AuditTrailOnSearch && $kurikulum_list->Command == "search" && !$kurikulum_list->RestoreSearch) {
		$searchparm = ew_ServerVar("QUERY_STRING");
		$searchsql = $kurikulum_list->getSessionWhere();
		$kurikulum_list->WriteAuditTrailOnSearch($searchparm, $searchsql);
	}
}
$kurikulum_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($kurikulum->Export == "" && $kurikulum->CurrentAction == "") { ?>
<form name="fkurikulumlistsrch" id="fkurikulumlistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($kurikulum_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fkurikulumlistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="kurikulum">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($kurikulum_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($kurikulum_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $kurikulum_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($kurikulum_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($kurikulum_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($kurikulum_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($kurikulum_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
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
<?php $kurikulum_list->ShowPageHeader(); ?>
<?php
$kurikulum_list->ShowMessage();
?>
<?php if ($kurikulum_list->TotalRecs > 0 || $kurikulum->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid kurikulum">
<?php if ($kurikulum->Export == "") { ?>
<div class="panel-heading ewGridUpperPanel">
<?php if ($kurikulum->CurrentAction <> "gridadd" && $kurikulum->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($kurikulum_list->Pager)) $kurikulum_list->Pager = new cPrevNextPager($kurikulum_list->StartRec, $kurikulum_list->DisplayRecs, $kurikulum_list->TotalRecs) ?>
<?php if ($kurikulum_list->Pager->RecordCount > 0 && $kurikulum_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($kurikulum_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $kurikulum_list->PageUrl() ?>start=<?php echo $kurikulum_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($kurikulum_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $kurikulum_list->PageUrl() ?>start=<?php echo $kurikulum_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $kurikulum_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($kurikulum_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $kurikulum_list->PageUrl() ?>start=<?php echo $kurikulum_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($kurikulum_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $kurikulum_list->PageUrl() ?>start=<?php echo $kurikulum_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $kurikulum_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $kurikulum_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $kurikulum_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $kurikulum_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($kurikulum_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="fkurikulumlist" id="fkurikulumlist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($kurikulum_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $kurikulum_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="kurikulum">
<div id="gmp_kurikulum" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($kurikulum_list->TotalRecs > 0 || $kurikulum->CurrentAction == "gridedit") { ?>
<table id="tbl_kurikulumlist" class="table ewTable">
<?php echo $kurikulum->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$kurikulum_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$kurikulum_list->RenderListOptions();

// Render list options (header, left)
$kurikulum_list->ListOptions->Render("header", "left");
?>
<?php if ($kurikulum->ProdiID->Visible) { // ProdiID ?>
	<?php if ($kurikulum->SortUrl($kurikulum->ProdiID) == "") { ?>
		<th data-name="ProdiID"><div id="elh_kurikulum_ProdiID" class="kurikulum_ProdiID"><div class="ewTableHeaderCaption"><?php echo $kurikulum->ProdiID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="ProdiID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $kurikulum->SortUrl($kurikulum->ProdiID) ?>',1);"><div id="elh_kurikulum_ProdiID" class="kurikulum_ProdiID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $kurikulum->ProdiID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($kurikulum->ProdiID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($kurikulum->ProdiID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($kurikulum->KurikulumKode->Visible) { // KurikulumKode ?>
	<?php if ($kurikulum->SortUrl($kurikulum->KurikulumKode) == "") { ?>
		<th data-name="KurikulumKode"><div id="elh_kurikulum_KurikulumKode" class="kurikulum_KurikulumKode"><div class="ewTableHeaderCaption"><?php echo $kurikulum->KurikulumKode->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="KurikulumKode"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $kurikulum->SortUrl($kurikulum->KurikulumKode) ?>',1);"><div id="elh_kurikulum_KurikulumKode" class="kurikulum_KurikulumKode">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $kurikulum->KurikulumKode->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($kurikulum->KurikulumKode->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($kurikulum->KurikulumKode->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($kurikulum->Nama->Visible) { // Nama ?>
	<?php if ($kurikulum->SortUrl($kurikulum->Nama) == "") { ?>
		<th data-name="Nama"><div id="elh_kurikulum_Nama" class="kurikulum_Nama"><div class="ewTableHeaderCaption"><?php echo $kurikulum->Nama->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Nama"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $kurikulum->SortUrl($kurikulum->Nama) ?>',1);"><div id="elh_kurikulum_Nama" class="kurikulum_Nama">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $kurikulum->Nama->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($kurikulum->Nama->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($kurikulum->Nama->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($kurikulum->JmlSesi->Visible) { // JmlSesi ?>
	<?php if ($kurikulum->SortUrl($kurikulum->JmlSesi) == "") { ?>
		<th data-name="JmlSesi"><div id="elh_kurikulum_JmlSesi" class="kurikulum_JmlSesi"><div class="ewTableHeaderCaption"><?php echo $kurikulum->JmlSesi->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="JmlSesi"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $kurikulum->SortUrl($kurikulum->JmlSesi) ?>',1);"><div id="elh_kurikulum_JmlSesi" class="kurikulum_JmlSesi">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $kurikulum->JmlSesi->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($kurikulum->JmlSesi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($kurikulum->JmlSesi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($kurikulum->NA->Visible) { // NA ?>
	<?php if ($kurikulum->SortUrl($kurikulum->NA) == "") { ?>
		<th data-name="NA"><div id="elh_kurikulum_NA" class="kurikulum_NA"><div class="ewTableHeaderCaption"><?php echo $kurikulum->NA->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NA"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $kurikulum->SortUrl($kurikulum->NA) ?>',1);"><div id="elh_kurikulum_NA" class="kurikulum_NA">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $kurikulum->NA->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($kurikulum->NA->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($kurikulum->NA->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$kurikulum_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($kurikulum->ExportAll && $kurikulum->Export <> "") {
	$kurikulum_list->StopRec = $kurikulum_list->TotalRecs;
} else {

	// Set the last record to display
	if ($kurikulum_list->TotalRecs > $kurikulum_list->StartRec + $kurikulum_list->DisplayRecs - 1)
		$kurikulum_list->StopRec = $kurikulum_list->StartRec + $kurikulum_list->DisplayRecs - 1;
	else
		$kurikulum_list->StopRec = $kurikulum_list->TotalRecs;
}

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($kurikulum_list->FormKeyCountName) && ($kurikulum->CurrentAction == "gridadd" || $kurikulum->CurrentAction == "gridedit" || $kurikulum->CurrentAction == "F")) {
		$kurikulum_list->KeyCount = $objForm->GetValue($kurikulum_list->FormKeyCountName);
		$kurikulum_list->StopRec = $kurikulum_list->StartRec + $kurikulum_list->KeyCount - 1;
	}
}
$kurikulum_list->RecCnt = $kurikulum_list->StartRec - 1;
if ($kurikulum_list->Recordset && !$kurikulum_list->Recordset->EOF) {
	$kurikulum_list->Recordset->MoveFirst();
	$bSelectLimit = $kurikulum_list->UseSelectLimit;
	if (!$bSelectLimit && $kurikulum_list->StartRec > 1)
		$kurikulum_list->Recordset->Move($kurikulum_list->StartRec - 1);
} elseif (!$kurikulum->AllowAddDeleteRow && $kurikulum_list->StopRec == 0) {
	$kurikulum_list->StopRec = $kurikulum->GridAddRowCount;
}

// Initialize aggregate
$kurikulum->RowType = EW_ROWTYPE_AGGREGATEINIT;
$kurikulum->ResetAttrs();
$kurikulum_list->RenderRow();
if ($kurikulum->CurrentAction == "gridadd")
	$kurikulum_list->RowIndex = 0;
if ($kurikulum->CurrentAction == "gridedit")
	$kurikulum_list->RowIndex = 0;
while ($kurikulum_list->RecCnt < $kurikulum_list->StopRec) {
	$kurikulum_list->RecCnt++;
	if (intval($kurikulum_list->RecCnt) >= intval($kurikulum_list->StartRec)) {
		$kurikulum_list->RowCnt++;
		if ($kurikulum->CurrentAction == "gridadd" || $kurikulum->CurrentAction == "gridedit" || $kurikulum->CurrentAction == "F") {
			$kurikulum_list->RowIndex++;
			$objForm->Index = $kurikulum_list->RowIndex;
			if ($objForm->HasValue($kurikulum_list->FormActionName))
				$kurikulum_list->RowAction = strval($objForm->GetValue($kurikulum_list->FormActionName));
			elseif ($kurikulum->CurrentAction == "gridadd")
				$kurikulum_list->RowAction = "insert";
			else
				$kurikulum_list->RowAction = "";
		}

		// Set up key count
		$kurikulum_list->KeyCount = $kurikulum_list->RowIndex;

		// Init row class and style
		$kurikulum->ResetAttrs();
		$kurikulum->CssClass = "";
		if ($kurikulum->CurrentAction == "gridadd") {
			$kurikulum_list->LoadDefaultValues(); // Load default values
		} else {
			$kurikulum_list->LoadRowValues($kurikulum_list->Recordset); // Load row values
		}
		$kurikulum->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($kurikulum->CurrentAction == "gridadd") // Grid add
			$kurikulum->RowType = EW_ROWTYPE_ADD; // Render add
		if ($kurikulum->CurrentAction == "gridadd" && $kurikulum->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$kurikulum_list->RestoreCurrentRowFormValues($kurikulum_list->RowIndex); // Restore form values
		if ($kurikulum->CurrentAction == "gridedit") { // Grid edit
			if ($kurikulum->EventCancelled) {
				$kurikulum_list->RestoreCurrentRowFormValues($kurikulum_list->RowIndex); // Restore form values
			}
			if ($kurikulum_list->RowAction == "insert")
				$kurikulum->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$kurikulum->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($kurikulum->CurrentAction == "gridedit" && ($kurikulum->RowType == EW_ROWTYPE_EDIT || $kurikulum->RowType == EW_ROWTYPE_ADD) && $kurikulum->EventCancelled) // Update failed
			$kurikulum_list->RestoreCurrentRowFormValues($kurikulum_list->RowIndex); // Restore form values
		if ($kurikulum->RowType == EW_ROWTYPE_EDIT) // Edit row
			$kurikulum_list->EditRowCnt++;

		// Set up row id / data-rowindex
		$kurikulum->RowAttrs = array_merge($kurikulum->RowAttrs, array('data-rowindex'=>$kurikulum_list->RowCnt, 'id'=>'r' . $kurikulum_list->RowCnt . '_kurikulum', 'data-rowtype'=>$kurikulum->RowType));

		// Render row
		$kurikulum_list->RenderRow();

		// Render list options
		$kurikulum_list->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($kurikulum_list->RowAction <> "delete" && $kurikulum_list->RowAction <> "insertdelete" && !($kurikulum_list->RowAction == "insert" && $kurikulum->CurrentAction == "F" && $kurikulum_list->EmptyRow())) {
?>
	<tr<?php echo $kurikulum->RowAttributes() ?>>
<?php

// Render list options (body, left)
$kurikulum_list->ListOptions->Render("body", "left", $kurikulum_list->RowCnt);
?>
	<?php if ($kurikulum->ProdiID->Visible) { // ProdiID ?>
		<td data-name="ProdiID"<?php echo $kurikulum->ProdiID->CellAttributes() ?>>
<?php if ($kurikulum->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_ProdiID" class="form-group kurikulum_ProdiID">
<select data-table="kurikulum" data-field="x_ProdiID" data-value-separator="<?php echo $kurikulum->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $kurikulum_list->RowIndex ?>_ProdiID" name="x<?php echo $kurikulum_list->RowIndex ?>_ProdiID"<?php echo $kurikulum->ProdiID->EditAttributes() ?>>
<?php echo $kurikulum->ProdiID->SelectOptionListHtml("x<?php echo $kurikulum_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $kurikulum_list->RowIndex ?>_ProdiID" id="s_x<?php echo $kurikulum_list->RowIndex ?>_ProdiID" value="<?php echo $kurikulum->ProdiID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="kurikulum" data-field="x_ProdiID" name="o<?php echo $kurikulum_list->RowIndex ?>_ProdiID" id="o<?php echo $kurikulum_list->RowIndex ?>_ProdiID" value="<?php echo ew_HtmlEncode($kurikulum->ProdiID->OldValue) ?>">
<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_ProdiID" class="form-group kurikulum_ProdiID">
<select data-table="kurikulum" data-field="x_ProdiID" data-value-separator="<?php echo $kurikulum->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $kurikulum_list->RowIndex ?>_ProdiID" name="x<?php echo $kurikulum_list->RowIndex ?>_ProdiID"<?php echo $kurikulum->ProdiID->EditAttributes() ?>>
<?php echo $kurikulum->ProdiID->SelectOptionListHtml("x<?php echo $kurikulum_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $kurikulum_list->RowIndex ?>_ProdiID" id="s_x<?php echo $kurikulum_list->RowIndex ?>_ProdiID" value="<?php echo $kurikulum->ProdiID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_ProdiID" class="kurikulum_ProdiID">
<span<?php echo $kurikulum->ProdiID->ViewAttributes() ?>>
<?php echo $kurikulum->ProdiID->ListViewValue() ?></span>
</span>
<?php } ?>
<a id="<?php echo $kurikulum_list->PageObjName . "_row_" . $kurikulum_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-table="kurikulum" data-field="x_KurikulumID" name="x<?php echo $kurikulum_list->RowIndex ?>_KurikulumID" id="x<?php echo $kurikulum_list->RowIndex ?>_KurikulumID" value="<?php echo ew_HtmlEncode($kurikulum->KurikulumID->CurrentValue) ?>">
<input type="hidden" data-table="kurikulum" data-field="x_KurikulumID" name="o<?php echo $kurikulum_list->RowIndex ?>_KurikulumID" id="o<?php echo $kurikulum_list->RowIndex ?>_KurikulumID" value="<?php echo ew_HtmlEncode($kurikulum->KurikulumID->OldValue) ?>">
<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_EDIT || $kurikulum->CurrentMode == "edit") { ?>
<input type="hidden" data-table="kurikulum" data-field="x_KurikulumID" name="x<?php echo $kurikulum_list->RowIndex ?>_KurikulumID" id="x<?php echo $kurikulum_list->RowIndex ?>_KurikulumID" value="<?php echo ew_HtmlEncode($kurikulum->KurikulumID->CurrentValue) ?>">
<?php } ?>
	<?php if ($kurikulum->KurikulumKode->Visible) { // KurikulumKode ?>
		<td data-name="KurikulumKode"<?php echo $kurikulum->KurikulumKode->CellAttributes() ?>>
<?php if ($kurikulum->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_KurikulumKode" class="form-group kurikulum_KurikulumKode">
<input type="text" data-table="kurikulum" data-field="x_KurikulumKode" name="x<?php echo $kurikulum_list->RowIndex ?>_KurikulumKode" id="x<?php echo $kurikulum_list->RowIndex ?>_KurikulumKode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($kurikulum->KurikulumKode->getPlaceHolder()) ?>" value="<?php echo $kurikulum->KurikulumKode->EditValue ?>"<?php echo $kurikulum->KurikulumKode->EditAttributes() ?>>
</span>
<input type="hidden" data-table="kurikulum" data-field="x_KurikulumKode" name="o<?php echo $kurikulum_list->RowIndex ?>_KurikulumKode" id="o<?php echo $kurikulum_list->RowIndex ?>_KurikulumKode" value="<?php echo ew_HtmlEncode($kurikulum->KurikulumKode->OldValue) ?>">
<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_KurikulumKode" class="form-group kurikulum_KurikulumKode">
<input type="text" data-table="kurikulum" data-field="x_KurikulumKode" name="x<?php echo $kurikulum_list->RowIndex ?>_KurikulumKode" id="x<?php echo $kurikulum_list->RowIndex ?>_KurikulumKode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($kurikulum->KurikulumKode->getPlaceHolder()) ?>" value="<?php echo $kurikulum->KurikulumKode->EditValue ?>"<?php echo $kurikulum->KurikulumKode->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_KurikulumKode" class="kurikulum_KurikulumKode">
<span<?php echo $kurikulum->KurikulumKode->ViewAttributes() ?>>
<?php echo $kurikulum->KurikulumKode->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($kurikulum->Nama->Visible) { // Nama ?>
		<td data-name="Nama"<?php echo $kurikulum->Nama->CellAttributes() ?>>
<?php if ($kurikulum->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_Nama" class="form-group kurikulum_Nama">
<input type="text" data-table="kurikulum" data-field="x_Nama" name="x<?php echo $kurikulum_list->RowIndex ?>_Nama" id="x<?php echo $kurikulum_list->RowIndex ?>_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($kurikulum->Nama->getPlaceHolder()) ?>" value="<?php echo $kurikulum->Nama->EditValue ?>"<?php echo $kurikulum->Nama->EditAttributes() ?>>
</span>
<input type="hidden" data-table="kurikulum" data-field="x_Nama" name="o<?php echo $kurikulum_list->RowIndex ?>_Nama" id="o<?php echo $kurikulum_list->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($kurikulum->Nama->OldValue) ?>">
<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_Nama" class="form-group kurikulum_Nama">
<input type="text" data-table="kurikulum" data-field="x_Nama" name="x<?php echo $kurikulum_list->RowIndex ?>_Nama" id="x<?php echo $kurikulum_list->RowIndex ?>_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($kurikulum->Nama->getPlaceHolder()) ?>" value="<?php echo $kurikulum->Nama->EditValue ?>"<?php echo $kurikulum->Nama->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_Nama" class="kurikulum_Nama">
<span<?php echo $kurikulum->Nama->ViewAttributes() ?>>
<?php echo $kurikulum->Nama->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($kurikulum->JmlSesi->Visible) { // JmlSesi ?>
		<td data-name="JmlSesi"<?php echo $kurikulum->JmlSesi->CellAttributes() ?>>
<?php if ($kurikulum->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_JmlSesi" class="form-group kurikulum_JmlSesi">
<input type="text" data-table="kurikulum" data-field="x_JmlSesi" name="x<?php echo $kurikulum_list->RowIndex ?>_JmlSesi" id="x<?php echo $kurikulum_list->RowIndex ?>_JmlSesi" size="30" placeholder="<?php echo ew_HtmlEncode($kurikulum->JmlSesi->getPlaceHolder()) ?>" value="<?php echo $kurikulum->JmlSesi->EditValue ?>"<?php echo $kurikulum->JmlSesi->EditAttributes() ?>>
</span>
<input type="hidden" data-table="kurikulum" data-field="x_JmlSesi" name="o<?php echo $kurikulum_list->RowIndex ?>_JmlSesi" id="o<?php echo $kurikulum_list->RowIndex ?>_JmlSesi" value="<?php echo ew_HtmlEncode($kurikulum->JmlSesi->OldValue) ?>">
<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_JmlSesi" class="form-group kurikulum_JmlSesi">
<input type="text" data-table="kurikulum" data-field="x_JmlSesi" name="x<?php echo $kurikulum_list->RowIndex ?>_JmlSesi" id="x<?php echo $kurikulum_list->RowIndex ?>_JmlSesi" size="30" placeholder="<?php echo ew_HtmlEncode($kurikulum->JmlSesi->getPlaceHolder()) ?>" value="<?php echo $kurikulum->JmlSesi->EditValue ?>"<?php echo $kurikulum->JmlSesi->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_JmlSesi" class="kurikulum_JmlSesi">
<span<?php echo $kurikulum->JmlSesi->ViewAttributes() ?>>
<?php echo $kurikulum->JmlSesi->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($kurikulum->NA->Visible) { // NA ?>
		<td data-name="NA"<?php echo $kurikulum->NA->CellAttributes() ?>>
<?php if ($kurikulum->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_NA" class="form-group kurikulum_NA">
<div id="tp_x<?php echo $kurikulum_list->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="kurikulum" data-field="x_NA" data-value-separator="<?php echo $kurikulum->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $kurikulum_list->RowIndex ?>_NA" id="x<?php echo $kurikulum_list->RowIndex ?>_NA" value="{value}"<?php echo $kurikulum->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $kurikulum_list->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $kurikulum->NA->RadioButtonListHtml(FALSE, "x{$kurikulum_list->RowIndex}_NA") ?>
</div></div>
</span>
<input type="hidden" data-table="kurikulum" data-field="x_NA" name="o<?php echo $kurikulum_list->RowIndex ?>_NA" id="o<?php echo $kurikulum_list->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($kurikulum->NA->OldValue) ?>">
<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_NA" class="form-group kurikulum_NA">
<div id="tp_x<?php echo $kurikulum_list->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="kurikulum" data-field="x_NA" data-value-separator="<?php echo $kurikulum->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $kurikulum_list->RowIndex ?>_NA" id="x<?php echo $kurikulum_list->RowIndex ?>_NA" value="{value}"<?php echo $kurikulum->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $kurikulum_list->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $kurikulum->NA->RadioButtonListHtml(FALSE, "x{$kurikulum_list->RowIndex}_NA") ?>
</div></div>
</span>
<?php } ?>
<?php if ($kurikulum->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $kurikulum_list->RowCnt ?>_kurikulum_NA" class="kurikulum_NA">
<span<?php echo $kurikulum->NA->ViewAttributes() ?>>
<?php echo $kurikulum->NA->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$kurikulum_list->ListOptions->Render("body", "right", $kurikulum_list->RowCnt);
?>
	</tr>
<?php if ($kurikulum->RowType == EW_ROWTYPE_ADD || $kurikulum->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fkurikulumlist.UpdateOpts(<?php echo $kurikulum_list->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($kurikulum->CurrentAction <> "gridadd")
		if (!$kurikulum_list->Recordset->EOF) $kurikulum_list->Recordset->MoveNext();
}
?>
<?php
	if ($kurikulum->CurrentAction == "gridadd" || $kurikulum->CurrentAction == "gridedit") {
		$kurikulum_list->RowIndex = '$rowindex$';
		$kurikulum_list->LoadDefaultValues();

		// Set row properties
		$kurikulum->ResetAttrs();
		$kurikulum->RowAttrs = array_merge($kurikulum->RowAttrs, array('data-rowindex'=>$kurikulum_list->RowIndex, 'id'=>'r0_kurikulum', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($kurikulum->RowAttrs["class"], "ewTemplate");
		$kurikulum->RowType = EW_ROWTYPE_ADD;

		// Render row
		$kurikulum_list->RenderRow();

		// Render list options
		$kurikulum_list->RenderListOptions();
		$kurikulum_list->StartRowCnt = 0;
?>
	<tr<?php echo $kurikulum->RowAttributes() ?>>
<?php

// Render list options (body, left)
$kurikulum_list->ListOptions->Render("body", "left", $kurikulum_list->RowIndex);
?>
	<?php if ($kurikulum->ProdiID->Visible) { // ProdiID ?>
		<td data-name="ProdiID">
<span id="el$rowindex$_kurikulum_ProdiID" class="form-group kurikulum_ProdiID">
<select data-table="kurikulum" data-field="x_ProdiID" data-value-separator="<?php echo $kurikulum->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $kurikulum_list->RowIndex ?>_ProdiID" name="x<?php echo $kurikulum_list->RowIndex ?>_ProdiID"<?php echo $kurikulum->ProdiID->EditAttributes() ?>>
<?php echo $kurikulum->ProdiID->SelectOptionListHtml("x<?php echo $kurikulum_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $kurikulum_list->RowIndex ?>_ProdiID" id="s_x<?php echo $kurikulum_list->RowIndex ?>_ProdiID" value="<?php echo $kurikulum->ProdiID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="kurikulum" data-field="x_ProdiID" name="o<?php echo $kurikulum_list->RowIndex ?>_ProdiID" id="o<?php echo $kurikulum_list->RowIndex ?>_ProdiID" value="<?php echo ew_HtmlEncode($kurikulum->ProdiID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($kurikulum->KurikulumKode->Visible) { // KurikulumKode ?>
		<td data-name="KurikulumKode">
<span id="el$rowindex$_kurikulum_KurikulumKode" class="form-group kurikulum_KurikulumKode">
<input type="text" data-table="kurikulum" data-field="x_KurikulumKode" name="x<?php echo $kurikulum_list->RowIndex ?>_KurikulumKode" id="x<?php echo $kurikulum_list->RowIndex ?>_KurikulumKode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($kurikulum->KurikulumKode->getPlaceHolder()) ?>" value="<?php echo $kurikulum->KurikulumKode->EditValue ?>"<?php echo $kurikulum->KurikulumKode->EditAttributes() ?>>
</span>
<input type="hidden" data-table="kurikulum" data-field="x_KurikulumKode" name="o<?php echo $kurikulum_list->RowIndex ?>_KurikulumKode" id="o<?php echo $kurikulum_list->RowIndex ?>_KurikulumKode" value="<?php echo ew_HtmlEncode($kurikulum->KurikulumKode->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($kurikulum->Nama->Visible) { // Nama ?>
		<td data-name="Nama">
<span id="el$rowindex$_kurikulum_Nama" class="form-group kurikulum_Nama">
<input type="text" data-table="kurikulum" data-field="x_Nama" name="x<?php echo $kurikulum_list->RowIndex ?>_Nama" id="x<?php echo $kurikulum_list->RowIndex ?>_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($kurikulum->Nama->getPlaceHolder()) ?>" value="<?php echo $kurikulum->Nama->EditValue ?>"<?php echo $kurikulum->Nama->EditAttributes() ?>>
</span>
<input type="hidden" data-table="kurikulum" data-field="x_Nama" name="o<?php echo $kurikulum_list->RowIndex ?>_Nama" id="o<?php echo $kurikulum_list->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($kurikulum->Nama->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($kurikulum->JmlSesi->Visible) { // JmlSesi ?>
		<td data-name="JmlSesi">
<span id="el$rowindex$_kurikulum_JmlSesi" class="form-group kurikulum_JmlSesi">
<input type="text" data-table="kurikulum" data-field="x_JmlSesi" name="x<?php echo $kurikulum_list->RowIndex ?>_JmlSesi" id="x<?php echo $kurikulum_list->RowIndex ?>_JmlSesi" size="30" placeholder="<?php echo ew_HtmlEncode($kurikulum->JmlSesi->getPlaceHolder()) ?>" value="<?php echo $kurikulum->JmlSesi->EditValue ?>"<?php echo $kurikulum->JmlSesi->EditAttributes() ?>>
</span>
<input type="hidden" data-table="kurikulum" data-field="x_JmlSesi" name="o<?php echo $kurikulum_list->RowIndex ?>_JmlSesi" id="o<?php echo $kurikulum_list->RowIndex ?>_JmlSesi" value="<?php echo ew_HtmlEncode($kurikulum->JmlSesi->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($kurikulum->NA->Visible) { // NA ?>
		<td data-name="NA">
<span id="el$rowindex$_kurikulum_NA" class="form-group kurikulum_NA">
<div id="tp_x<?php echo $kurikulum_list->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="kurikulum" data-field="x_NA" data-value-separator="<?php echo $kurikulum->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $kurikulum_list->RowIndex ?>_NA" id="x<?php echo $kurikulum_list->RowIndex ?>_NA" value="{value}"<?php echo $kurikulum->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $kurikulum_list->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $kurikulum->NA->RadioButtonListHtml(FALSE, "x{$kurikulum_list->RowIndex}_NA") ?>
</div></div>
</span>
<input type="hidden" data-table="kurikulum" data-field="x_NA" name="o<?php echo $kurikulum_list->RowIndex ?>_NA" id="o<?php echo $kurikulum_list->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($kurikulum->NA->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$kurikulum_list->ListOptions->Render("body", "right", $kurikulum_list->RowCnt);
?>
<script type="text/javascript">
fkurikulumlist.UpdateOpts(<?php echo $kurikulum_list->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($kurikulum->CurrentAction == "gridadd") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $kurikulum_list->FormKeyCountName ?>" id="<?php echo $kurikulum_list->FormKeyCountName ?>" value="<?php echo $kurikulum_list->KeyCount ?>">
<?php echo $kurikulum_list->MultiSelectKey ?>
<?php } ?>
<?php if ($kurikulum->CurrentAction == "gridedit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $kurikulum_list->FormKeyCountName ?>" id="<?php echo $kurikulum_list->FormKeyCountName ?>" value="<?php echo $kurikulum_list->KeyCount ?>">
<?php echo $kurikulum_list->MultiSelectKey ?>
<?php } ?>
<?php if ($kurikulum->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($kurikulum_list->Recordset)
	$kurikulum_list->Recordset->Close();
?>
<?php if ($kurikulum->Export == "") { ?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($kurikulum->CurrentAction <> "gridadd" && $kurikulum->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($kurikulum_list->Pager)) $kurikulum_list->Pager = new cPrevNextPager($kurikulum_list->StartRec, $kurikulum_list->DisplayRecs, $kurikulum_list->TotalRecs) ?>
<?php if ($kurikulum_list->Pager->RecordCount > 0 && $kurikulum_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($kurikulum_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $kurikulum_list->PageUrl() ?>start=<?php echo $kurikulum_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($kurikulum_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $kurikulum_list->PageUrl() ?>start=<?php echo $kurikulum_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $kurikulum_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($kurikulum_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $kurikulum_list->PageUrl() ?>start=<?php echo $kurikulum_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($kurikulum_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $kurikulum_list->PageUrl() ?>start=<?php echo $kurikulum_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $kurikulum_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $kurikulum_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $kurikulum_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $kurikulum_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($kurikulum_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($kurikulum_list->TotalRecs == 0 && $kurikulum->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($kurikulum_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($kurikulum->Export == "") { ?>
<script type="text/javascript">
fkurikulumlistsrch.FilterList = <?php echo $kurikulum_list->GetFilterList() ?>;
fkurikulumlistsrch.Init();
fkurikulumlist.Init();
</script>
<?php } ?>
<?php
$kurikulum_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($kurikulum->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$kurikulum_list->Page_Terminate();
?>
