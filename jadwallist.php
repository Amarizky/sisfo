<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "jadwalinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$jadwal_list = NULL; // Initialize page object first

class cjadwal_list extends cjadwal {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'jadwal';

	// Page object name
	var $PageObjName = 'jadwal_list';

	// Grid form hidden field names
	var $FormName = 'fjadwallist';
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

		// Table object (jadwal)
		if (!isset($GLOBALS["jadwal"]) || get_class($GLOBALS["jadwal"]) == "cjadwal") {
			$GLOBALS["jadwal"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["jadwal"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "jadwaladd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "jadwaldelete.php";
		$this->MultiUpdateUrl = "jadwalupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'jadwal', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fjadwallistsrch";

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
		$this->TahunID->SetVisibility();
		$this->Sesi->SetVisibility();
		$this->Tingkat->SetVisibility();
		$this->KelasID->SetVisibility();
		$this->HariID->SetVisibility();
		$this->JamID->SetVisibility();
		$this->MKID->SetVisibility();
		$this->TeacherID->SetVisibility();
		$this->JamMulai->SetVisibility();
		$this->JamSelesai->SetVisibility();

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
		global $EW_EXPORT, $jadwal;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($jadwal);
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
			$this->JadwalID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->JadwalID->FormValue))
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
					$sKey .= $this->JadwalID->CurrentValue;

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
		if ($objForm->HasValue("x_TahunID") && $objForm->HasValue("o_TahunID") && $this->TahunID->CurrentValue <> $this->TahunID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_Sesi") && $objForm->HasValue("o_Sesi") && $this->Sesi->CurrentValue <> $this->Sesi->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_Tingkat") && $objForm->HasValue("o_Tingkat") && $this->Tingkat->CurrentValue <> $this->Tingkat->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_KelasID") && $objForm->HasValue("o_KelasID") && $this->KelasID->CurrentValue <> $this->KelasID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_HariID") && $objForm->HasValue("o_HariID") && $this->HariID->CurrentValue <> $this->HariID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_JamID") && $objForm->HasValue("o_JamID") && $this->JamID->CurrentValue <> $this->JamID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_MKID") && $objForm->HasValue("o_MKID") && $this->MKID->CurrentValue <> $this->MKID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_TeacherID") && $objForm->HasValue("o_TeacherID") && $this->TeacherID->CurrentValue <> $this->TeacherID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_JamMulai") && $objForm->HasValue("o_JamMulai") && $this->JamMulai->CurrentValue <> $this->JamMulai->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_JamSelesai") && $objForm->HasValue("o_JamSelesai") && $this->JamSelesai->CurrentValue <> $this->JamSelesai->OldValue)
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
			$sSavedFilterList = isset($UserProfile) ? $UserProfile->GetSearchFilters(CurrentUserName(), "fjadwallistsrch") : "";
		} else {
			$sSavedFilterList = "";
		}

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->ProdiID->AdvancedSearch->ToJSON(), ","); // Field ProdiID
		$sFilterList = ew_Concat($sFilterList, $this->TahunID->AdvancedSearch->ToJSON(), ","); // Field TahunID
		$sFilterList = ew_Concat($sFilterList, $this->Sesi->AdvancedSearch->ToJSON(), ","); // Field Sesi
		$sFilterList = ew_Concat($sFilterList, $this->Tingkat->AdvancedSearch->ToJSON(), ","); // Field Tingkat
		$sFilterList = ew_Concat($sFilterList, $this->KelasID->AdvancedSearch->ToJSON(), ","); // Field KelasID
		$sFilterList = ew_Concat($sFilterList, $this->HariID->AdvancedSearch->ToJSON(), ","); // Field HariID
		$sFilterList = ew_Concat($sFilterList, $this->JamID->AdvancedSearch->ToJSON(), ","); // Field JamID
		$sFilterList = ew_Concat($sFilterList, $this->MKID->AdvancedSearch->ToJSON(), ","); // Field MKID
		$sFilterList = ew_Concat($sFilterList, $this->TeacherID->AdvancedSearch->ToJSON(), ","); // Field TeacherID
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
			$UserProfile->SetSearchFilters(CurrentUserName(), "fjadwallistsrch", $filters);

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

		// Field ProdiID
		$this->ProdiID->AdvancedSearch->SearchValue = @$filter["x_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchOperator = @$filter["z_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchCondition = @$filter["v_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchValue2 = @$filter["y_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchOperator2 = @$filter["w_ProdiID"];
		$this->ProdiID->AdvancedSearch->Save();

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

		// Field Tingkat
		$this->Tingkat->AdvancedSearch->SearchValue = @$filter["x_Tingkat"];
		$this->Tingkat->AdvancedSearch->SearchOperator = @$filter["z_Tingkat"];
		$this->Tingkat->AdvancedSearch->SearchCondition = @$filter["v_Tingkat"];
		$this->Tingkat->AdvancedSearch->SearchValue2 = @$filter["y_Tingkat"];
		$this->Tingkat->AdvancedSearch->SearchOperator2 = @$filter["w_Tingkat"];
		$this->Tingkat->AdvancedSearch->Save();

		// Field KelasID
		$this->KelasID->AdvancedSearch->SearchValue = @$filter["x_KelasID"];
		$this->KelasID->AdvancedSearch->SearchOperator = @$filter["z_KelasID"];
		$this->KelasID->AdvancedSearch->SearchCondition = @$filter["v_KelasID"];
		$this->KelasID->AdvancedSearch->SearchValue2 = @$filter["y_KelasID"];
		$this->KelasID->AdvancedSearch->SearchOperator2 = @$filter["w_KelasID"];
		$this->KelasID->AdvancedSearch->Save();

		// Field HariID
		$this->HariID->AdvancedSearch->SearchValue = @$filter["x_HariID"];
		$this->HariID->AdvancedSearch->SearchOperator = @$filter["z_HariID"];
		$this->HariID->AdvancedSearch->SearchCondition = @$filter["v_HariID"];
		$this->HariID->AdvancedSearch->SearchValue2 = @$filter["y_HariID"];
		$this->HariID->AdvancedSearch->SearchOperator2 = @$filter["w_HariID"];
		$this->HariID->AdvancedSearch->Save();

		// Field JamID
		$this->JamID->AdvancedSearch->SearchValue = @$filter["x_JamID"];
		$this->JamID->AdvancedSearch->SearchOperator = @$filter["z_JamID"];
		$this->JamID->AdvancedSearch->SearchCondition = @$filter["v_JamID"];
		$this->JamID->AdvancedSearch->SearchValue2 = @$filter["y_JamID"];
		$this->JamID->AdvancedSearch->SearchOperator2 = @$filter["w_JamID"];
		$this->JamID->AdvancedSearch->Save();

		// Field MKID
		$this->MKID->AdvancedSearch->SearchValue = @$filter["x_MKID"];
		$this->MKID->AdvancedSearch->SearchOperator = @$filter["z_MKID"];
		$this->MKID->AdvancedSearch->SearchCondition = @$filter["v_MKID"];
		$this->MKID->AdvancedSearch->SearchValue2 = @$filter["y_MKID"];
		$this->MKID->AdvancedSearch->SearchOperator2 = @$filter["w_MKID"];
		$this->MKID->AdvancedSearch->Save();

		// Field TeacherID
		$this->TeacherID->AdvancedSearch->SearchValue = @$filter["x_TeacherID"];
		$this->TeacherID->AdvancedSearch->SearchOperator = @$filter["z_TeacherID"];
		$this->TeacherID->AdvancedSearch->SearchCondition = @$filter["v_TeacherID"];
		$this->TeacherID->AdvancedSearch->SearchValue2 = @$filter["y_TeacherID"];
		$this->TeacherID->AdvancedSearch->SearchOperator2 = @$filter["w_TeacherID"];
		$this->TeacherID->AdvancedSearch->Save();

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
		$this->BuildSearchSql($sWhere, $this->ProdiID, $Default, FALSE); // ProdiID
		$this->BuildSearchSql($sWhere, $this->TahunID, $Default, FALSE); // TahunID
		$this->BuildSearchSql($sWhere, $this->Sesi, $Default, FALSE); // Sesi
		$this->BuildSearchSql($sWhere, $this->Tingkat, $Default, FALSE); // Tingkat
		$this->BuildSearchSql($sWhere, $this->KelasID, $Default, FALSE); // KelasID
		$this->BuildSearchSql($sWhere, $this->HariID, $Default, FALSE); // HariID
		$this->BuildSearchSql($sWhere, $this->JamID, $Default, FALSE); // JamID
		$this->BuildSearchSql($sWhere, $this->MKID, $Default, FALSE); // MKID
		$this->BuildSearchSql($sWhere, $this->TeacherID, $Default, FALSE); // TeacherID
		$this->BuildSearchSql($sWhere, $this->NA, $Default, FALSE); // NA

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->ProdiID->AdvancedSearch->Save(); // ProdiID
			$this->TahunID->AdvancedSearch->Save(); // TahunID
			$this->Sesi->AdvancedSearch->Save(); // Sesi
			$this->Tingkat->AdvancedSearch->Save(); // Tingkat
			$this->KelasID->AdvancedSearch->Save(); // KelasID
			$this->HariID->AdvancedSearch->Save(); // HariID
			$this->JamID->AdvancedSearch->Save(); // JamID
			$this->MKID->AdvancedSearch->Save(); // MKID
			$this->TeacherID->AdvancedSearch->Save(); // TeacherID
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
		$this->BuildBasicSearchSQL($sWhere, $this->ProdiID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->TahunID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Tingkat, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KelasID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->JamID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->TeacherID, $arKeywords, $type);
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
		if ($this->ProdiID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TahunID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Sesi->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Tingkat->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KelasID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->HariID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->JamID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->MKID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TeacherID->AdvancedSearch->IssetSession())
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
		$this->ProdiID->AdvancedSearch->UnsetSession();
		$this->TahunID->AdvancedSearch->UnsetSession();
		$this->Sesi->AdvancedSearch->UnsetSession();
		$this->Tingkat->AdvancedSearch->UnsetSession();
		$this->KelasID->AdvancedSearch->UnsetSession();
		$this->HariID->AdvancedSearch->UnsetSession();
		$this->JamID->AdvancedSearch->UnsetSession();
		$this->MKID->AdvancedSearch->UnsetSession();
		$this->TeacherID->AdvancedSearch->UnsetSession();
		$this->NA->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->ProdiID->AdvancedSearch->Load();
		$this->TahunID->AdvancedSearch->Load();
		$this->Sesi->AdvancedSearch->Load();
		$this->Tingkat->AdvancedSearch->Load();
		$this->KelasID->AdvancedSearch->Load();
		$this->HariID->AdvancedSearch->Load();
		$this->JamID->AdvancedSearch->Load();
		$this->MKID->AdvancedSearch->Load();
		$this->TeacherID->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->ProdiID); // ProdiID
			$this->UpdateSort($this->TahunID); // TahunID
			$this->UpdateSort($this->Sesi); // Sesi
			$this->UpdateSort($this->Tingkat); // Tingkat
			$this->UpdateSort($this->KelasID); // KelasID
			$this->UpdateSort($this->HariID); // HariID
			$this->UpdateSort($this->JamID); // JamID
			$this->UpdateSort($this->MKID); // MKID
			$this->UpdateSort($this->TeacherID); // TeacherID
			$this->UpdateSort($this->JamMulai); // JamMulai
			$this->UpdateSort($this->JamSelesai); // JamSelesai
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
				$this->TahunID->setSort("");
				$this->Sesi->setSort("");
				$this->Tingkat->setSort("");
				$this->KelasID->setSort("");
				$this->HariID->setSort("");
				$this->JamID->setSort("");
				$this->MKID->setSort("");
				$this->TeacherID->setSort("");
				$this->JamMulai->setSort("");
				$this->JamSelesai->setSort("");
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

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->JadwalID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event);'>";
		if ($this->CurrentAction == "gridedit" && is_numeric($this->RowIndex)) {
			$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $KeyName . "\" id=\"" . $KeyName . "\" value=\"" . $this->JadwalID->CurrentValue . "\">";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fjadwallistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fjadwallistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
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
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fjadwallist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fjadwallistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		if (ew_IsMobile())
			$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"jadwalsrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		else
			$item->Body = "<button type=\"button\" class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-table=\"jadwal\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" onclick=\"ew_ModalDialogShow({lnk:this,url:'jadwalsrch.php',caption:'" . $Language->Phrase("Search") . "'});\">" . $Language->Phrase("AdvancedSearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Search highlight button
		$item = &$this->SearchOptions->Add("searchhighlight");
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewHighlight active\" title=\"" . $Language->Phrase("Highlight") . "\" data-caption=\"" . $Language->Phrase("Highlight") . "\" data-toggle=\"button\" data-form=\"fjadwallistsrch\" data-name=\"" . $this->HighlightName() . "\">" . $Language->Phrase("HighlightBtn") . "</button>";
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

	// Load default values
	function LoadDefaultValues() {
		$this->ProdiID->CurrentValue = NULL;
		$this->ProdiID->OldValue = $this->ProdiID->CurrentValue;
		$this->TahunID->CurrentValue = NULL;
		$this->TahunID->OldValue = $this->TahunID->CurrentValue;
		$this->Sesi->CurrentValue = NULL;
		$this->Sesi->OldValue = $this->Sesi->CurrentValue;
		$this->Tingkat->CurrentValue = NULL;
		$this->Tingkat->OldValue = $this->Tingkat->CurrentValue;
		$this->KelasID->CurrentValue = NULL;
		$this->KelasID->OldValue = $this->KelasID->CurrentValue;
		$this->HariID->CurrentValue = NULL;
		$this->HariID->OldValue = $this->HariID->CurrentValue;
		$this->JamID->CurrentValue = NULL;
		$this->JamID->OldValue = $this->JamID->CurrentValue;
		$this->MKID->CurrentValue = NULL;
		$this->MKID->OldValue = $this->MKID->CurrentValue;
		$this->TeacherID->CurrentValue = NULL;
		$this->TeacherID->OldValue = $this->TeacherID->CurrentValue;
		$this->JamMulai->CurrentValue = NULL;
		$this->JamMulai->OldValue = $this->JamMulai->CurrentValue;
		$this->JamSelesai->CurrentValue = NULL;
		$this->JamSelesai->OldValue = $this->JamSelesai->CurrentValue;
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
		// ProdiID

		$this->ProdiID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_ProdiID"]);
		if ($this->ProdiID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->ProdiID->AdvancedSearch->SearchOperator = @$_GET["z_ProdiID"];

		// TahunID
		$this->TahunID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TahunID"]);
		if ($this->TahunID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TahunID->AdvancedSearch->SearchOperator = @$_GET["z_TahunID"];

		// Sesi
		$this->Sesi->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Sesi"]);
		if ($this->Sesi->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Sesi->AdvancedSearch->SearchOperator = @$_GET["z_Sesi"];

		// Tingkat
		$this->Tingkat->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Tingkat"]);
		if ($this->Tingkat->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Tingkat->AdvancedSearch->SearchOperator = @$_GET["z_Tingkat"];

		// KelasID
		$this->KelasID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KelasID"]);
		if ($this->KelasID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KelasID->AdvancedSearch->SearchOperator = @$_GET["z_KelasID"];

		// HariID
		$this->HariID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_HariID"]);
		if ($this->HariID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->HariID->AdvancedSearch->SearchOperator = @$_GET["z_HariID"];

		// JamID
		$this->JamID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_JamID"]);
		if ($this->JamID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->JamID->AdvancedSearch->SearchOperator = @$_GET["z_JamID"];

		// MKID
		$this->MKID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_MKID"]);
		if ($this->MKID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->MKID->AdvancedSearch->SearchOperator = @$_GET["z_MKID"];

		// TeacherID
		$this->TeacherID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TeacherID"]);
		if ($this->TeacherID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TeacherID->AdvancedSearch->SearchOperator = @$_GET["z_TeacherID"];
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->ProdiID->FldIsDetailKey) {
			$this->ProdiID->setFormValue($objForm->GetValue("x_ProdiID"));
		}
		$this->ProdiID->setOldValue($objForm->GetValue("o_ProdiID"));
		if (!$this->TahunID->FldIsDetailKey) {
			$this->TahunID->setFormValue($objForm->GetValue("x_TahunID"));
		}
		$this->TahunID->setOldValue($objForm->GetValue("o_TahunID"));
		if (!$this->Sesi->FldIsDetailKey) {
			$this->Sesi->setFormValue($objForm->GetValue("x_Sesi"));
		}
		$this->Sesi->setOldValue($objForm->GetValue("o_Sesi"));
		if (!$this->Tingkat->FldIsDetailKey) {
			$this->Tingkat->setFormValue($objForm->GetValue("x_Tingkat"));
		}
		$this->Tingkat->setOldValue($objForm->GetValue("o_Tingkat"));
		if (!$this->KelasID->FldIsDetailKey) {
			$this->KelasID->setFormValue($objForm->GetValue("x_KelasID"));
		}
		$this->KelasID->setOldValue($objForm->GetValue("o_KelasID"));
		if (!$this->HariID->FldIsDetailKey) {
			$this->HariID->setFormValue($objForm->GetValue("x_HariID"));
		}
		$this->HariID->setOldValue($objForm->GetValue("o_HariID"));
		if (!$this->JamID->FldIsDetailKey) {
			$this->JamID->setFormValue($objForm->GetValue("x_JamID"));
		}
		$this->JamID->setOldValue($objForm->GetValue("o_JamID"));
		if (!$this->MKID->FldIsDetailKey) {
			$this->MKID->setFormValue($objForm->GetValue("x_MKID"));
		}
		$this->MKID->setOldValue($objForm->GetValue("o_MKID"));
		if (!$this->TeacherID->FldIsDetailKey) {
			$this->TeacherID->setFormValue($objForm->GetValue("x_TeacherID"));
		}
		$this->TeacherID->setOldValue($objForm->GetValue("o_TeacherID"));
		if (!$this->JamMulai->FldIsDetailKey) {
			$this->JamMulai->setFormValue($objForm->GetValue("x_JamMulai"));
			$this->JamMulai->CurrentValue = ew_UnFormatDateTime($this->JamMulai->CurrentValue, 0);
		}
		$this->JamMulai->setOldValue($objForm->GetValue("o_JamMulai"));
		if (!$this->JamSelesai->FldIsDetailKey) {
			$this->JamSelesai->setFormValue($objForm->GetValue("x_JamSelesai"));
			$this->JamSelesai->CurrentValue = ew_UnFormatDateTime($this->JamSelesai->CurrentValue, 0);
		}
		$this->JamSelesai->setOldValue($objForm->GetValue("o_JamSelesai"));
		if (!$this->JadwalID->FldIsDetailKey && $this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->JadwalID->setFormValue($objForm->GetValue("x_JadwalID"));
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		if ($this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->JadwalID->CurrentValue = $this->JadwalID->FormValue;
		$this->ProdiID->CurrentValue = $this->ProdiID->FormValue;
		$this->TahunID->CurrentValue = $this->TahunID->FormValue;
		$this->Sesi->CurrentValue = $this->Sesi->FormValue;
		$this->Tingkat->CurrentValue = $this->Tingkat->FormValue;
		$this->KelasID->CurrentValue = $this->KelasID->FormValue;
		$this->HariID->CurrentValue = $this->HariID->FormValue;
		$this->JamID->CurrentValue = $this->JamID->FormValue;
		$this->MKID->CurrentValue = $this->MKID->FormValue;
		$this->TeacherID->CurrentValue = $this->TeacherID->FormValue;
		$this->JamMulai->CurrentValue = $this->JamMulai->FormValue;
		$this->JamMulai->CurrentValue = ew_UnFormatDateTime($this->JamMulai->CurrentValue, 0);
		$this->JamSelesai->CurrentValue = $this->JamSelesai->FormValue;
		$this->JamSelesai->CurrentValue = ew_UnFormatDateTime($this->JamSelesai->CurrentValue, 0);
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
		$this->JadwalID->setDbValue($rs->fields('JadwalID'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->TahunID->setDbValue($rs->fields('TahunID'));
		$this->Sesi->setDbValue($rs->fields('Sesi'));
		$this->Tingkat->setDbValue($rs->fields('Tingkat'));
		$this->KelasID->setDbValue($rs->fields('KelasID'));
		$this->HariID->setDbValue($rs->fields('HariID'));
		$this->JamID->setDbValue($rs->fields('JamID'));
		$this->MKID->setDbValue($rs->fields('MKID'));
		$this->TeacherID->setDbValue($rs->fields('TeacherID'));
		$this->JamMulai->setDbValue($rs->fields('JamMulai'));
		$this->JamSelesai->setDbValue($rs->fields('JamSelesai'));
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
		$this->JadwalID->DbValue = $row['JadwalID'];
		$this->ProdiID->DbValue = $row['ProdiID'];
		$this->TahunID->DbValue = $row['TahunID'];
		$this->Sesi->DbValue = $row['Sesi'];
		$this->Tingkat->DbValue = $row['Tingkat'];
		$this->KelasID->DbValue = $row['KelasID'];
		$this->HariID->DbValue = $row['HariID'];
		$this->JamID->DbValue = $row['JamID'];
		$this->MKID->DbValue = $row['MKID'];
		$this->TeacherID->DbValue = $row['TeacherID'];
		$this->JamMulai->DbValue = $row['JamMulai'];
		$this->JamSelesai->DbValue = $row['JamSelesai'];
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
		if (strval($this->getKey("JadwalID")) <> "")
			$this->JadwalID->CurrentValue = $this->getKey("JadwalID"); // JadwalID
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
		// JadwalID
		// ProdiID
		// TahunID
		// Sesi
		// Tingkat
		// KelasID
		// HariID
		// JamID
		// MKID
		// TeacherID
		// JamMulai
		// JamSelesai
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// JadwalID
		$this->JadwalID->ViewValue = $this->JadwalID->CurrentValue;
		$this->JadwalID->ViewCustomAttributes = "";

		// ProdiID
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `ProdiID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
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
		$this->ProdiID->ViewCustomAttributes = "";

		// TahunID
		$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
		$this->TahunID->ViewCustomAttributes = "";

		// Sesi
		if (strval($this->Sesi->CurrentValue) <> "") {
			$sFilterWrk = "`Sesi`" . ew_SearchString("=", $this->Sesi->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `Sesi`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_sesi`";
		$sWhereWrk = "";
		$this->Sesi->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Sesi->ViewValue = $this->Sesi->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Sesi->ViewValue = $this->Sesi->CurrentValue;
			}
		} else {
			$this->Sesi->ViewValue = NULL;
		}
		$this->Sesi->ViewCustomAttributes = "";

		// Tingkat
		if (strval($this->Tingkat->CurrentValue) <> "") {
			$sFilterWrk = "`Tingkat`" . ew_SearchString("=", $this->Tingkat->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
		$sWhereWrk = "";
		$this->Tingkat->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Tingkat->ViewValue = $this->Tingkat->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Tingkat->ViewValue = $this->Tingkat->CurrentValue;
			}
		} else {
			$this->Tingkat->ViewValue = NULL;
		}
		$this->Tingkat->CellCssStyle .= "text-align: center;";
		$this->Tingkat->ViewCustomAttributes = "";

		// KelasID
		if (strval($this->KelasID->CurrentValue) <> "") {
			$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->KelasID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
		$sWhereWrk = "";
		$this->KelasID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KelasID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KelasID->ViewValue = $this->KelasID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KelasID->ViewValue = $this->KelasID->CurrentValue;
			}
		} else {
			$this->KelasID->ViewValue = NULL;
		}
		$this->KelasID->ViewCustomAttributes = "";

		// HariID
		if (strval($this->HariID->CurrentValue) <> "") {
			$sFilterWrk = "`HariID`" . ew_SearchString("=", $this->HariID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `HariID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hari`";
		$sWhereWrk = "";
		$this->HariID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->HariID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->HariID->ViewValue = $this->HariID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->HariID->ViewValue = $this->HariID->CurrentValue;
			}
		} else {
			$this->HariID->ViewValue = NULL;
		}
		$this->HariID->ViewCustomAttributes = "";

		// JamID
		if (strval($this->JamID->CurrentValue) <> "") {
			$sFilterWrk = "`JamID`" . ew_SearchString("=", $this->JamID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `JamID`, `JamID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_jamkul`";
		$sWhereWrk = "";
		$this->JamID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `JamID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->JamID->ViewValue = $this->JamID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->JamID->ViewValue = $this->JamID->CurrentValue;
			}
		} else {
			$this->JamID->ViewValue = NULL;
		}
		$this->JamID->ViewCustomAttributes = "";

		// MKID
		if (strval($this->MKID->CurrentValue) <> "") {
			$sFilterWrk = "`MKID`" . ew_SearchString("=", $this->MKID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `MKID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `mk`";
		$sWhereWrk = "";
		$this->MKID->LookupFilters = array("dx1" => '`Nama`');
		$lookuptblfilter = "`Tingkat` in (Tingkat)";
		ew_AddFilter($sWhereWrk, $lookuptblfilter);
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->MKID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->MKID->ViewValue = $this->MKID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->MKID->ViewValue = $this->MKID->CurrentValue;
			}
		} else {
			$this->MKID->ViewValue = NULL;
		}
		$this->MKID->ViewCustomAttributes = "";

		// TeacherID
		$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
		if (strval($this->TeacherID->CurrentValue) <> "") {
			$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->TeacherID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `TeacherID`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
		$sWhereWrk = "";
		$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->TeacherID->ViewValue = $this->TeacherID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
			}
		} else {
			$this->TeacherID->ViewValue = NULL;
		}
		$this->TeacherID->ViewCustomAttributes = "";

		// JamMulai
		$this->JamMulai->ViewValue = $this->JamMulai->CurrentValue;
		$this->JamMulai->ViewCustomAttributes = "";

		// JamSelesai
		$this->JamSelesai->ViewValue = $this->JamSelesai->CurrentValue;
		$this->JamSelesai->ViewCustomAttributes = "";

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
		$this->NA->ViewValue = $this->NA->CurrentValue;
		$this->NA->ViewCustomAttributes = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

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

			// Tingkat
			$this->Tingkat->LinkCustomAttributes = "";
			$this->Tingkat->HrefValue = "";
			$this->Tingkat->TooltipValue = "";

			// KelasID
			$this->KelasID->LinkCustomAttributes = "";
			$this->KelasID->HrefValue = "";
			$this->KelasID->TooltipValue = "";

			// HariID
			$this->HariID->LinkCustomAttributes = "";
			$this->HariID->HrefValue = "";
			$this->HariID->TooltipValue = "";

			// JamID
			$this->JamID->LinkCustomAttributes = "";
			$this->JamID->HrefValue = "";
			$this->JamID->TooltipValue = "";

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";
			$this->MKID->TooltipValue = "";

			// TeacherID
			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";
			$this->TeacherID->TooltipValue = "";
			if ($this->Export == "")
				$this->TeacherID->ViewValue = ew_Highlight($this->HighlightName(), $this->TeacherID->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->TeacherID->AdvancedSearch->getValue("x"), "");

			// JamMulai
			$this->JamMulai->LinkCustomAttributes = "";
			$this->JamMulai->HrefValue = "";
			$this->JamMulai->TooltipValue = "";

			// JamSelesai
			$this->JamSelesai->LinkCustomAttributes = "";
			$this->JamSelesai->HrefValue = "";
			$this->JamSelesai->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// ProdiID
			$this->ProdiID->EditAttrs["class"] = "form-control";
			$this->ProdiID->EditCustomAttributes = "";
			if (trim(strval($this->ProdiID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProdiID`, `ProdiID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_prodi`";
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

			// TahunID
			$this->TahunID->EditAttrs["class"] = "form-control";
			$this->TahunID->EditCustomAttributes = "";
			$this->TahunID->EditValue = ew_HtmlEncode($this->TahunID->CurrentValue);
			$this->TahunID->PlaceHolder = ew_RemoveHtml($this->TahunID->FldCaption());

			// Sesi
			$this->Sesi->EditAttrs["class"] = "form-control";
			$this->Sesi->EditCustomAttributes = "";
			if (trim(strval($this->Sesi->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Sesi`" . ew_SearchString("=", $this->Sesi->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `Sesi`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_sesi`";
			$sWhereWrk = "";
			$this->Sesi->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Sesi->EditValue = $arwrk;

			// Tingkat
			$this->Tingkat->EditAttrs["class"] = "form-control";
			$this->Tingkat->EditCustomAttributes = "";
			if (trim(strval($this->Tingkat->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Tingkat`" . ew_SearchString("=", $this->Tingkat->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Tingkat->EditValue = $arwrk;

			// KelasID
			$this->KelasID->EditAttrs["class"] = "form-control";
			$this->KelasID->EditCustomAttributes = "";
			if (trim(strval($this->KelasID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->KelasID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, `Tingkat` AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->KelasID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KelasID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KelasID->EditValue = $arwrk;

			// HariID
			$this->HariID->EditAttrs["class"] = "form-control";
			$this->HariID->EditCustomAttributes = "";
			if (trim(strval($this->HariID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`HariID`" . ew_SearchString("=", $this->HariID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `HariID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_hari`";
			$sWhereWrk = "";
			$this->HariID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->HariID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->HariID->EditValue = $arwrk;

			// JamID
			$this->JamID->EditAttrs["class"] = "form-control";
			$this->JamID->EditCustomAttributes = "";
			if (trim(strval($this->JamID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`JamID`" . ew_SearchString("=", $this->JamID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `JamID`, `JamID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `HariID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_jamkul`";
			$sWhereWrk = "";
			$this->JamID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `JamID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->JamID->EditValue = $arwrk;

			// MKID
			$this->MKID->EditCustomAttributes = "";
			if (trim(strval($this->MKID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`MKID`" . ew_SearchString("=", $this->MKID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `MKID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `mk`";
			$sWhereWrk = "";
			$this->MKID->LookupFilters = array("dx1" => '`Nama`');
			$lookuptblfilter = "`Tingkat` in (Tingkat)";
			ew_AddFilter($sWhereWrk, $lookuptblfilter);
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->MKID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$this->MKID->ViewValue = $this->MKID->DisplayValue($arwrk);
			} else {
				$this->MKID->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->MKID->EditValue = $arwrk;

			// TeacherID
			$this->TeacherID->EditAttrs["class"] = "form-control";
			$this->TeacherID->EditCustomAttributes = "";
			$this->TeacherID->EditValue = ew_HtmlEncode($this->TeacherID->CurrentValue);
			if (strval($this->TeacherID->CurrentValue) <> "") {
				$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->TeacherID->CurrentValue, EW_DATATYPE_STRING, "");
			$sSqlWrk = "SELECT `TeacherID`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
			$sWhereWrk = "";
			$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
					$this->TeacherID->EditValue = $this->TeacherID->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->TeacherID->EditValue = ew_HtmlEncode($this->TeacherID->CurrentValue);
				}
			} else {
				$this->TeacherID->EditValue = NULL;
			}
			$this->TeacherID->PlaceHolder = ew_RemoveHtml($this->TeacherID->FldCaption());

			// JamMulai
			$this->JamMulai->EditAttrs["class"] = "form-control";
			$this->JamMulai->EditCustomAttributes = "readonly";
			$this->JamMulai->EditValue = ew_HtmlEncode($this->JamMulai->CurrentValue);
			$this->JamMulai->PlaceHolder = ew_RemoveHtml($this->JamMulai->FldCaption());

			// JamSelesai
			$this->JamSelesai->EditAttrs["class"] = "form-control";
			$this->JamSelesai->EditCustomAttributes = "readonly";
			$this->JamSelesai->EditValue = ew_HtmlEncode($this->JamSelesai->CurrentValue);
			$this->JamSelesai->PlaceHolder = ew_RemoveHtml($this->JamSelesai->FldCaption());

			// Add refer script
			// ProdiID

			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";

			// Tingkat
			$this->Tingkat->LinkCustomAttributes = "";
			$this->Tingkat->HrefValue = "";

			// KelasID
			$this->KelasID->LinkCustomAttributes = "";
			$this->KelasID->HrefValue = "";

			// HariID
			$this->HariID->LinkCustomAttributes = "";
			$this->HariID->HrefValue = "";

			// JamID
			$this->JamID->LinkCustomAttributes = "";
			$this->JamID->HrefValue = "";

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";

			// TeacherID
			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";

			// JamMulai
			$this->JamMulai->LinkCustomAttributes = "";
			$this->JamMulai->HrefValue = "";

			// JamSelesai
			$this->JamSelesai->LinkCustomAttributes = "";
			$this->JamSelesai->HrefValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// ProdiID
			$this->ProdiID->EditAttrs["class"] = "form-control";
			$this->ProdiID->EditCustomAttributes = "";
			if (trim(strval($this->ProdiID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProdiID`, `ProdiID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_prodi`";
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

			// TahunID
			$this->TahunID->EditAttrs["class"] = "form-control";
			$this->TahunID->EditCustomAttributes = "";
			$this->TahunID->EditValue = ew_HtmlEncode($this->TahunID->CurrentValue);
			$this->TahunID->PlaceHolder = ew_RemoveHtml($this->TahunID->FldCaption());

			// Sesi
			$this->Sesi->EditAttrs["class"] = "form-control";
			$this->Sesi->EditCustomAttributes = "";
			if (trim(strval($this->Sesi->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Sesi`" . ew_SearchString("=", $this->Sesi->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `Sesi`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_sesi`";
			$sWhereWrk = "";
			$this->Sesi->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Sesi->EditValue = $arwrk;

			// Tingkat
			$this->Tingkat->EditAttrs["class"] = "form-control";
			$this->Tingkat->EditCustomAttributes = "";
			if (trim(strval($this->Tingkat->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Tingkat`" . ew_SearchString("=", $this->Tingkat->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Tingkat->EditValue = $arwrk;

			// KelasID
			$this->KelasID->EditAttrs["class"] = "form-control";
			$this->KelasID->EditCustomAttributes = "";
			if (trim(strval($this->KelasID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->KelasID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, `Tingkat` AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->KelasID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KelasID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KelasID->EditValue = $arwrk;

			// HariID
			$this->HariID->EditAttrs["class"] = "form-control";
			$this->HariID->EditCustomAttributes = "";
			if (trim(strval($this->HariID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`HariID`" . ew_SearchString("=", $this->HariID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `HariID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_hari`";
			$sWhereWrk = "";
			$this->HariID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->HariID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->HariID->EditValue = $arwrk;

			// JamID
			$this->JamID->EditAttrs["class"] = "form-control";
			$this->JamID->EditCustomAttributes = "";
			if (trim(strval($this->JamID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`JamID`" . ew_SearchString("=", $this->JamID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `JamID`, `JamID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `HariID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_jamkul`";
			$sWhereWrk = "";
			$this->JamID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `JamID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->JamID->EditValue = $arwrk;

			// MKID
			$this->MKID->EditCustomAttributes = "";
			if (trim(strval($this->MKID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`MKID`" . ew_SearchString("=", $this->MKID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `MKID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `mk`";
			$sWhereWrk = "";
			$this->MKID->LookupFilters = array("dx1" => '`Nama`');
			$lookuptblfilter = "`Tingkat` in (Tingkat)";
			ew_AddFilter($sWhereWrk, $lookuptblfilter);
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->MKID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$this->MKID->ViewValue = $this->MKID->DisplayValue($arwrk);
			} else {
				$this->MKID->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->MKID->EditValue = $arwrk;

			// TeacherID
			$this->TeacherID->EditAttrs["class"] = "form-control";
			$this->TeacherID->EditCustomAttributes = "";
			$this->TeacherID->EditValue = ew_HtmlEncode($this->TeacherID->CurrentValue);
			if (strval($this->TeacherID->CurrentValue) <> "") {
				$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->TeacherID->CurrentValue, EW_DATATYPE_STRING, "");
			$sSqlWrk = "SELECT `TeacherID`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
			$sWhereWrk = "";
			$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
					$this->TeacherID->EditValue = $this->TeacherID->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->TeacherID->EditValue = ew_HtmlEncode($this->TeacherID->CurrentValue);
				}
			} else {
				$this->TeacherID->EditValue = NULL;
			}
			$this->TeacherID->PlaceHolder = ew_RemoveHtml($this->TeacherID->FldCaption());

			// JamMulai
			$this->JamMulai->EditAttrs["class"] = "form-control";
			$this->JamMulai->EditCustomAttributes = "readonly";
			$this->JamMulai->EditValue = ew_HtmlEncode($this->JamMulai->CurrentValue);
			$this->JamMulai->PlaceHolder = ew_RemoveHtml($this->JamMulai->FldCaption());

			// JamSelesai
			$this->JamSelesai->EditAttrs["class"] = "form-control";
			$this->JamSelesai->EditCustomAttributes = "readonly";
			$this->JamSelesai->EditValue = ew_HtmlEncode($this->JamSelesai->CurrentValue);
			$this->JamSelesai->PlaceHolder = ew_RemoveHtml($this->JamSelesai->FldCaption());

			// Edit refer script
			// ProdiID

			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";

			// Tingkat
			$this->Tingkat->LinkCustomAttributes = "";
			$this->Tingkat->HrefValue = "";

			// KelasID
			$this->KelasID->LinkCustomAttributes = "";
			$this->KelasID->HrefValue = "";

			// HariID
			$this->HariID->LinkCustomAttributes = "";
			$this->HariID->HrefValue = "";

			// JamID
			$this->JamID->LinkCustomAttributes = "";
			$this->JamID->HrefValue = "";

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";

			// TeacherID
			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";

			// JamMulai
			$this->JamMulai->LinkCustomAttributes = "";
			$this->JamMulai->HrefValue = "";

			// JamSelesai
			$this->JamSelesai->LinkCustomAttributes = "";
			$this->JamSelesai->HrefValue = "";
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
		if (!$this->TahunID->FldIsDetailKey && !is_null($this->TahunID->FormValue) && $this->TahunID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TahunID->FldCaption(), $this->TahunID->ReqErrMsg));
		}
		if (!$this->Sesi->FldIsDetailKey && !is_null($this->Sesi->FormValue) && $this->Sesi->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Sesi->FldCaption(), $this->Sesi->ReqErrMsg));
		}
		if (!$this->Tingkat->FldIsDetailKey && !is_null($this->Tingkat->FormValue) && $this->Tingkat->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Tingkat->FldCaption(), $this->Tingkat->ReqErrMsg));
		}
		if (!$this->JamID->FldIsDetailKey && !is_null($this->JamID->FormValue) && $this->JamID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->JamID->FldCaption(), $this->JamID->ReqErrMsg));
		}
		if (!$this->MKID->FldIsDetailKey && !is_null($this->MKID->FormValue) && $this->MKID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->MKID->FldCaption(), $this->MKID->ReqErrMsg));
		}
		if (!$this->TeacherID->FldIsDetailKey && !is_null($this->TeacherID->FormValue) && $this->TeacherID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TeacherID->FldCaption(), $this->TeacherID->ReqErrMsg));
		}
		if (!$this->JamMulai->FldIsDetailKey && !is_null($this->JamMulai->FormValue) && $this->JamMulai->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->JamMulai->FldCaption(), $this->JamMulai->ReqErrMsg));
		}
		if (!ew_CheckTime($this->JamMulai->FormValue)) {
			ew_AddMessage($gsFormError, $this->JamMulai->FldErrMsg());
		}
		if (!$this->JamSelesai->FldIsDetailKey && !is_null($this->JamSelesai->FormValue) && $this->JamSelesai->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->JamSelesai->FldCaption(), $this->JamSelesai->ReqErrMsg));
		}
		if (!ew_CheckTime($this->JamSelesai->FormValue)) {
			ew_AddMessage($gsFormError, $this->JamSelesai->FldErrMsg());
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
				$sThisKey .= $row['JadwalID'];
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

			// TahunID
			$this->TahunID->SetDbValueDef($rsnew, $this->TahunID->CurrentValue, "", $this->TahunID->ReadOnly);

			// Sesi
			$this->Sesi->SetDbValueDef($rsnew, $this->Sesi->CurrentValue, 0, $this->Sesi->ReadOnly);

			// Tingkat
			$this->Tingkat->SetDbValueDef($rsnew, $this->Tingkat->CurrentValue, NULL, $this->Tingkat->ReadOnly);

			// KelasID
			$this->KelasID->SetDbValueDef($rsnew, $this->KelasID->CurrentValue, NULL, $this->KelasID->ReadOnly);

			// HariID
			$this->HariID->SetDbValueDef($rsnew, $this->HariID->CurrentValue, NULL, $this->HariID->ReadOnly);

			// JamID
			$this->JamID->SetDbValueDef($rsnew, $this->JamID->CurrentValue, NULL, $this->JamID->ReadOnly);

			// MKID
			$this->MKID->SetDbValueDef($rsnew, $this->MKID->CurrentValue, 0, $this->MKID->ReadOnly);

			// TeacherID
			$this->TeacherID->SetDbValueDef($rsnew, $this->TeacherID->CurrentValue, "", $this->TeacherID->ReadOnly);

			// JamMulai
			$this->JamMulai->SetDbValueDef($rsnew, $this->JamMulai->CurrentValue, NULL, $this->JamMulai->ReadOnly);

			// JamSelesai
			$this->JamSelesai->SetDbValueDef($rsnew, $this->JamSelesai->CurrentValue, NULL, $this->JamSelesai->ReadOnly);

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

		// TahunID
		$this->TahunID->SetDbValueDef($rsnew, $this->TahunID->CurrentValue, "", strval($this->TahunID->CurrentValue) == "");

		// Sesi
		$this->Sesi->SetDbValueDef($rsnew, $this->Sesi->CurrentValue, 0, strval($this->Sesi->CurrentValue) == "");

		// Tingkat
		$this->Tingkat->SetDbValueDef($rsnew, $this->Tingkat->CurrentValue, NULL, FALSE);

		// KelasID
		$this->KelasID->SetDbValueDef($rsnew, $this->KelasID->CurrentValue, NULL, strval($this->KelasID->CurrentValue) == "");

		// HariID
		$this->HariID->SetDbValueDef($rsnew, $this->HariID->CurrentValue, NULL, strval($this->HariID->CurrentValue) == "");

		// JamID
		$this->JamID->SetDbValueDef($rsnew, $this->JamID->CurrentValue, NULL, FALSE);

		// MKID
		$this->MKID->SetDbValueDef($rsnew, $this->MKID->CurrentValue, 0, strval($this->MKID->CurrentValue) == "");

		// TeacherID
		$this->TeacherID->SetDbValueDef($rsnew, $this->TeacherID->CurrentValue, "", FALSE);

		// JamMulai
		$this->JamMulai->SetDbValueDef($rsnew, $this->JamMulai->CurrentValue, NULL, strval($this->JamMulai->CurrentValue) == "");

		// JamSelesai
		$this->JamSelesai->SetDbValueDef($rsnew, $this->JamSelesai->CurrentValue, NULL, strval($this->JamSelesai->CurrentValue) == "");

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
		$this->ProdiID->AdvancedSearch->Load();
		$this->TahunID->AdvancedSearch->Load();
		$this->Sesi->AdvancedSearch->Load();
		$this->Tingkat->AdvancedSearch->Load();
		$this->KelasID->AdvancedSearch->Load();
		$this->HariID->AdvancedSearch->Load();
		$this->JamID->AdvancedSearch->Load();
		$this->MKID->AdvancedSearch->Load();
		$this->TeacherID->AdvancedSearch->Load();
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
		$item->Body = "<button id=\"emf_jadwal\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_jadwal',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fjadwallist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
			$sSqlWrk = "SELECT `ProdiID` AS `LinkFld`, `ProdiID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
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
		case "x_Sesi":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Sesi` AS `LinkFld`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_sesi`";
			$sWhereWrk = "";
			$this->Sesi->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Sesi` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_Tingkat":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT DISTINCT `Tingkat` AS `LinkFld`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Tingkat` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KelasID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KelasID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
			$sWhereWrk = "{filter}";
			$this->KelasID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KelasID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "", "f2" => '`Tingkat` IN ({filter_value})', "t2" => "200", "fn2" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KelasID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_HariID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `HariID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hari`";
			$sWhereWrk = "";
			$this->HariID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`HariID` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->HariID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_JamID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `JamID` AS `LinkFld`, `JamID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_jamkul`";
			$sWhereWrk = "{filter}";
			$this->JamID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`JamID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`HariID` IN ({filter_value})', "t1" => "3", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `JamID` ASC";
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_MKID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `MKID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `mk`";
			$sWhereWrk = "{filter}";
			$this->MKID->LookupFilters = array("dx1" => '`Nama`');
			$lookuptblfilter = "`Tingkat` in (Tingkat)";
			ew_AddFilter($sWhereWrk, $lookuptblfilter);
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`MKID` = {filter_value}', "t0" => "20", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->MKID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_TeacherID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `TeacherID` AS `LinkFld`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
			$sWhereWrk = "{filter}";
			$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`TeacherID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
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
		case "x_TeacherID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `TeacherID`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld` FROM `teacher`";
			$sWhereWrk = "`AliasCode` LIKE '%{query_value}%' OR `Nama` LIKE '%{query_value}%' OR CONCAT(`AliasCode`,'" . ew_ValueSeparator(1, $this->TeacherID) . "',`Nama`) LIKE '{query_value}%'";
			$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
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
		if (CurrentUserLevel()=="-1") {
			$this->OtherOptions["addedit"]->UseDropDownButton = FALSE; // jangan gunakan style DropDownButton
			$my_options = &$this->OtherOptions; // pastikan menggunakan area OtherOptions
			$my_option = $my_options["addedit"]; // dekat tombol addedit
			$my_item = &$my_option->Add("Impor Data"); // tambahkan tombol baru
			$my_item->Body = "<a class=\"ewAddEdit ewAdd\" title=\"Impor Data\" data-caption=\"Impor Data\" href=\"impor-data.php\">Impor Data</a>"; // definisikan link, style, dan caption tombol
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
if (!isset($jadwal_list)) $jadwal_list = new cjadwal_list();

// Page init
$jadwal_list->Page_Init();

// Page main
$jadwal_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$jadwal_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($jadwal->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fjadwallist = new ew_Form("fjadwallist", "list");
fjadwallist.FormKeyCountName = '<?php echo $jadwal_list->FormKeyCountName ?>';

// Validate form
fjadwallist.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->ProdiID->FldCaption(), $jadwal->ProdiID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TahunID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->TahunID->FldCaption(), $jadwal->TahunID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Sesi");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->Sesi->FldCaption(), $jadwal->Sesi->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Tingkat");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->Tingkat->FldCaption(), $jadwal->Tingkat->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JamID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->JamID->FldCaption(), $jadwal->JamID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_MKID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->MKID->FldCaption(), $jadwal->MKID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TeacherID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->TeacherID->FldCaption(), $jadwal->TeacherID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JamMulai");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->JamMulai->FldCaption(), $jadwal->JamMulai->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JamMulai");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($jadwal->JamMulai->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_JamSelesai");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $jadwal->JamSelesai->FldCaption(), $jadwal->JamSelesai->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_JamSelesai");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($jadwal->JamSelesai->FldErrMsg()) ?>");

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
fjadwallist.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "ProdiID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "TahunID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Sesi", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Tingkat", false)) return false;
	if (ew_ValueChanged(fobj, infix, "KelasID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "HariID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "JamID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "MKID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "TeacherID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "JamMulai", false)) return false;
	if (ew_ValueChanged(fobj, infix, "JamSelesai", false)) return false;
	return true;
}

// Form_CustomValidate event
fjadwallist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fjadwallist.ValidateRequired = true;
<?php } else { ?>
fjadwallist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fjadwallist.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_ProdiID","","",""],"ParentFields":[],"ChildFields":["x_TahunID","x_KelasID","x_MKID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fjadwallist.Lists["x_Sesi"] = {"LinkField":"x_Sesi","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaSesi","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_sesi"};
fjadwallist.Lists["x_Tingkat"] = {"LinkField":"x_Tingkat","Ajax":true,"AutoFill":false,"DisplayFields":["x_Tingkat","","",""],"ParentFields":[],"ChildFields":["x_KelasID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"kelas"};
fjadwallist.Lists["x_KelasID"] = {"LinkField":"x_KelasID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":["x_ProdiID","x_Tingkat"],"ChildFields":[],"FilterFields":["x_ProdiID","x_Tingkat"],"Options":[],"Template":"","LinkTable":"kelas"};
fjadwallist.Lists["x_HariID"] = {"LinkField":"x_HariID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":["x_JamID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_hari"};
fjadwallist.Lists["x_JamID"] = {"LinkField":"x_JamID","Ajax":true,"AutoFill":true,"DisplayFields":["x_JamID","","",""],"ParentFields":["x_HariID"],"ChildFields":[],"FilterFields":["x_HariID"],"Options":[],"Template":"","LinkTable":"master_jamkul"};
fjadwallist.Lists["x_MKID"] = {"LinkField":"x_MKID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":["x_ProdiID"],"ChildFields":[],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"mk"};
fjadwallist.Lists["x_TeacherID"] = {"LinkField":"x_TeacherID","Ajax":true,"AutoFill":false,"DisplayFields":["x_AliasCode","x_Nama","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"teacher"};

// Form object for search
var CurrentSearchForm = fjadwallistsrch = new ew_Form("fjadwallistsrch");

// Init search panel as collapsed
if (fjadwallistsrch) fjadwallistsrch.InitSearchPanel = true;
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
<?php if ($jadwal->Export == "") { ?>
<div class="ewToolbar">
<?php if ($jadwal->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($jadwal_list->TotalRecs > 0 && $jadwal_list->ExportOptions->Visible()) { ?>
<?php $jadwal_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($jadwal_list->SearchOptions->Visible()) { ?>
<?php $jadwal_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($jadwal_list->FilterOptions->Visible()) { ?>
<?php $jadwal_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php if ($jadwal->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
if ($jadwal->CurrentAction == "gridadd") {
	$jadwal->CurrentFilter = "0=1";
	$jadwal_list->StartRec = 1;
	$jadwal_list->DisplayRecs = $jadwal->GridAddRowCount;
	$jadwal_list->TotalRecs = $jadwal_list->DisplayRecs;
	$jadwal_list->StopRec = $jadwal_list->DisplayRecs;
} else {
	$bSelectLimit = $jadwal_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($jadwal_list->TotalRecs <= 0)
			$jadwal_list->TotalRecs = $jadwal->SelectRecordCount();
	} else {
		if (!$jadwal_list->Recordset && ($jadwal_list->Recordset = $jadwal_list->LoadRecordset()))
			$jadwal_list->TotalRecs = $jadwal_list->Recordset->RecordCount();
	}
	$jadwal_list->StartRec = 1;
	if ($jadwal_list->DisplayRecs <= 0 || ($jadwal->Export <> "" && $jadwal->ExportAll)) // Display all records
		$jadwal_list->DisplayRecs = $jadwal_list->TotalRecs;
	if (!($jadwal->Export <> "" && $jadwal->ExportAll))
		$jadwal_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$jadwal_list->Recordset = $jadwal_list->LoadRecordset($jadwal_list->StartRec-1, $jadwal_list->DisplayRecs);

	// Set no record found message
	if ($jadwal->CurrentAction == "" && $jadwal_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$jadwal_list->setWarningMessage(ew_DeniedMsg());
		if ($jadwal_list->SearchWhere == "0=101")
			$jadwal_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$jadwal_list->setWarningMessage($Language->Phrase("NoRecord"));
	}

	// Audit trail on search
	if ($jadwal_list->AuditTrailOnSearch && $jadwal_list->Command == "search" && !$jadwal_list->RestoreSearch) {
		$searchparm = ew_ServerVar("QUERY_STRING");
		$searchsql = $jadwal_list->getSessionWhere();
		$jadwal_list->WriteAuditTrailOnSearch($searchparm, $searchsql);
	}
}
$jadwal_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($jadwal->Export == "" && $jadwal->CurrentAction == "") { ?>
<form name="fjadwallistsrch" id="fjadwallistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($jadwal_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fjadwallistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="jadwal">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($jadwal_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($jadwal_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $jadwal_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($jadwal_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($jadwal_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($jadwal_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($jadwal_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
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
<?php $jadwal_list->ShowPageHeader(); ?>
<?php
$jadwal_list->ShowMessage();
?>
<?php if ($jadwal_list->TotalRecs > 0 || $jadwal->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid jadwal">
<?php if ($jadwal->Export == "") { ?>
<div class="panel-heading ewGridUpperPanel">
<?php if ($jadwal->CurrentAction <> "gridadd" && $jadwal->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($jadwal_list->Pager)) $jadwal_list->Pager = new cPrevNextPager($jadwal_list->StartRec, $jadwal_list->DisplayRecs, $jadwal_list->TotalRecs) ?>
<?php if ($jadwal_list->Pager->RecordCount > 0 && $jadwal_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($jadwal_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $jadwal_list->PageUrl() ?>start=<?php echo $jadwal_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($jadwal_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $jadwal_list->PageUrl() ?>start=<?php echo $jadwal_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $jadwal_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($jadwal_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $jadwal_list->PageUrl() ?>start=<?php echo $jadwal_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($jadwal_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $jadwal_list->PageUrl() ?>start=<?php echo $jadwal_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $jadwal_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $jadwal_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $jadwal_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $jadwal_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($jadwal_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="fjadwallist" id="fjadwallist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($jadwal_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $jadwal_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="jadwal">
<div id="gmp_jadwal" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($jadwal_list->TotalRecs > 0 || $jadwal->CurrentAction == "gridedit") { ?>
<table id="tbl_jadwallist" class="table ewTable">
<?php echo $jadwal->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$jadwal_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$jadwal_list->RenderListOptions();

// Render list options (header, left)
$jadwal_list->ListOptions->Render("header", "left");
?>
<?php if ($jadwal->ProdiID->Visible) { // ProdiID ?>
	<?php if ($jadwal->SortUrl($jadwal->ProdiID) == "") { ?>
		<th data-name="ProdiID"><div id="elh_jadwal_ProdiID" class="jadwal_ProdiID"><div class="ewTableHeaderCaption"><?php echo $jadwal->ProdiID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="ProdiID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $jadwal->SortUrl($jadwal->ProdiID) ?>',1);"><div id="elh_jadwal_ProdiID" class="jadwal_ProdiID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $jadwal->ProdiID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($jadwal->ProdiID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($jadwal->ProdiID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($jadwal->TahunID->Visible) { // TahunID ?>
	<?php if ($jadwal->SortUrl($jadwal->TahunID) == "") { ?>
		<th data-name="TahunID"><div id="elh_jadwal_TahunID" class="jadwal_TahunID"><div class="ewTableHeaderCaption"><?php echo $jadwal->TahunID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TahunID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $jadwal->SortUrl($jadwal->TahunID) ?>',1);"><div id="elh_jadwal_TahunID" class="jadwal_TahunID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $jadwal->TahunID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($jadwal->TahunID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($jadwal->TahunID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($jadwal->Sesi->Visible) { // Sesi ?>
	<?php if ($jadwal->SortUrl($jadwal->Sesi) == "") { ?>
		<th data-name="Sesi"><div id="elh_jadwal_Sesi" class="jadwal_Sesi"><div class="ewTableHeaderCaption"><?php echo $jadwal->Sesi->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Sesi"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $jadwal->SortUrl($jadwal->Sesi) ?>',1);"><div id="elh_jadwal_Sesi" class="jadwal_Sesi">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $jadwal->Sesi->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($jadwal->Sesi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($jadwal->Sesi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($jadwal->Tingkat->Visible) { // Tingkat ?>
	<?php if ($jadwal->SortUrl($jadwal->Tingkat) == "") { ?>
		<th data-name="Tingkat"><div id="elh_jadwal_Tingkat" class="jadwal_Tingkat"><div class="ewTableHeaderCaption"><?php echo $jadwal->Tingkat->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tingkat"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $jadwal->SortUrl($jadwal->Tingkat) ?>',1);"><div id="elh_jadwal_Tingkat" class="jadwal_Tingkat">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $jadwal->Tingkat->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($jadwal->Tingkat->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($jadwal->Tingkat->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($jadwal->KelasID->Visible) { // KelasID ?>
	<?php if ($jadwal->SortUrl($jadwal->KelasID) == "") { ?>
		<th data-name="KelasID"><div id="elh_jadwal_KelasID" class="jadwal_KelasID"><div class="ewTableHeaderCaption"><?php echo $jadwal->KelasID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="KelasID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $jadwal->SortUrl($jadwal->KelasID) ?>',1);"><div id="elh_jadwal_KelasID" class="jadwal_KelasID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $jadwal->KelasID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($jadwal->KelasID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($jadwal->KelasID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($jadwal->HariID->Visible) { // HariID ?>
	<?php if ($jadwal->SortUrl($jadwal->HariID) == "") { ?>
		<th data-name="HariID"><div id="elh_jadwal_HariID" class="jadwal_HariID"><div class="ewTableHeaderCaption"><?php echo $jadwal->HariID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="HariID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $jadwal->SortUrl($jadwal->HariID) ?>',1);"><div id="elh_jadwal_HariID" class="jadwal_HariID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $jadwal->HariID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($jadwal->HariID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($jadwal->HariID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($jadwal->JamID->Visible) { // JamID ?>
	<?php if ($jadwal->SortUrl($jadwal->JamID) == "") { ?>
		<th data-name="JamID"><div id="elh_jadwal_JamID" class="jadwal_JamID"><div class="ewTableHeaderCaption"><?php echo $jadwal->JamID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="JamID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $jadwal->SortUrl($jadwal->JamID) ?>',1);"><div id="elh_jadwal_JamID" class="jadwal_JamID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $jadwal->JamID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($jadwal->JamID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($jadwal->JamID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($jadwal->MKID->Visible) { // MKID ?>
	<?php if ($jadwal->SortUrl($jadwal->MKID) == "") { ?>
		<th data-name="MKID"><div id="elh_jadwal_MKID" class="jadwal_MKID"><div class="ewTableHeaderCaption"><?php echo $jadwal->MKID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="MKID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $jadwal->SortUrl($jadwal->MKID) ?>',1);"><div id="elh_jadwal_MKID" class="jadwal_MKID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $jadwal->MKID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($jadwal->MKID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($jadwal->MKID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($jadwal->TeacherID->Visible) { // TeacherID ?>
	<?php if ($jadwal->SortUrl($jadwal->TeacherID) == "") { ?>
		<th data-name="TeacherID"><div id="elh_jadwal_TeacherID" class="jadwal_TeacherID"><div class="ewTableHeaderCaption"><?php echo $jadwal->TeacherID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TeacherID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $jadwal->SortUrl($jadwal->TeacherID) ?>',1);"><div id="elh_jadwal_TeacherID" class="jadwal_TeacherID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $jadwal->TeacherID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($jadwal->TeacherID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($jadwal->TeacherID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($jadwal->JamMulai->Visible) { // JamMulai ?>
	<?php if ($jadwal->SortUrl($jadwal->JamMulai) == "") { ?>
		<th data-name="JamMulai"><div id="elh_jadwal_JamMulai" class="jadwal_JamMulai"><div class="ewTableHeaderCaption"><?php echo $jadwal->JamMulai->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="JamMulai"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $jadwal->SortUrl($jadwal->JamMulai) ?>',1);"><div id="elh_jadwal_JamMulai" class="jadwal_JamMulai">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $jadwal->JamMulai->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($jadwal->JamMulai->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($jadwal->JamMulai->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($jadwal->JamSelesai->Visible) { // JamSelesai ?>
	<?php if ($jadwal->SortUrl($jadwal->JamSelesai) == "") { ?>
		<th data-name="JamSelesai"><div id="elh_jadwal_JamSelesai" class="jadwal_JamSelesai"><div class="ewTableHeaderCaption"><?php echo $jadwal->JamSelesai->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="JamSelesai"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $jadwal->SortUrl($jadwal->JamSelesai) ?>',1);"><div id="elh_jadwal_JamSelesai" class="jadwal_JamSelesai">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $jadwal->JamSelesai->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($jadwal->JamSelesai->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($jadwal->JamSelesai->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$jadwal_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($jadwal->ExportAll && $jadwal->Export <> "") {
	$jadwal_list->StopRec = $jadwal_list->TotalRecs;
} else {

	// Set the last record to display
	if ($jadwal_list->TotalRecs > $jadwal_list->StartRec + $jadwal_list->DisplayRecs - 1)
		$jadwal_list->StopRec = $jadwal_list->StartRec + $jadwal_list->DisplayRecs - 1;
	else
		$jadwal_list->StopRec = $jadwal_list->TotalRecs;
}

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($jadwal_list->FormKeyCountName) && ($jadwal->CurrentAction == "gridadd" || $jadwal->CurrentAction == "gridedit" || $jadwal->CurrentAction == "F")) {
		$jadwal_list->KeyCount = $objForm->GetValue($jadwal_list->FormKeyCountName);
		$jadwal_list->StopRec = $jadwal_list->StartRec + $jadwal_list->KeyCount - 1;
	}
}
$jadwal_list->RecCnt = $jadwal_list->StartRec - 1;
if ($jadwal_list->Recordset && !$jadwal_list->Recordset->EOF) {
	$jadwal_list->Recordset->MoveFirst();
	$bSelectLimit = $jadwal_list->UseSelectLimit;
	if (!$bSelectLimit && $jadwal_list->StartRec > 1)
		$jadwal_list->Recordset->Move($jadwal_list->StartRec - 1);
} elseif (!$jadwal->AllowAddDeleteRow && $jadwal_list->StopRec == 0) {
	$jadwal_list->StopRec = $jadwal->GridAddRowCount;
}

// Initialize aggregate
$jadwal->RowType = EW_ROWTYPE_AGGREGATEINIT;
$jadwal->ResetAttrs();
$jadwal_list->RenderRow();
if ($jadwal->CurrentAction == "gridadd")
	$jadwal_list->RowIndex = 0;
if ($jadwal->CurrentAction == "gridedit")
	$jadwal_list->RowIndex = 0;
while ($jadwal_list->RecCnt < $jadwal_list->StopRec) {
	$jadwal_list->RecCnt++;
	if (intval($jadwal_list->RecCnt) >= intval($jadwal_list->StartRec)) {
		$jadwal_list->RowCnt++;
		if ($jadwal->CurrentAction == "gridadd" || $jadwal->CurrentAction == "gridedit" || $jadwal->CurrentAction == "F") {
			$jadwal_list->RowIndex++;
			$objForm->Index = $jadwal_list->RowIndex;
			if ($objForm->HasValue($jadwal_list->FormActionName))
				$jadwal_list->RowAction = strval($objForm->GetValue($jadwal_list->FormActionName));
			elseif ($jadwal->CurrentAction == "gridadd")
				$jadwal_list->RowAction = "insert";
			else
				$jadwal_list->RowAction = "";
		}

		// Set up key count
		$jadwal_list->KeyCount = $jadwal_list->RowIndex;

		// Init row class and style
		$jadwal->ResetAttrs();
		$jadwal->CssClass = "";
		if ($jadwal->CurrentAction == "gridadd") {
			$jadwal_list->LoadDefaultValues(); // Load default values
		} else {
			$jadwal_list->LoadRowValues($jadwal_list->Recordset); // Load row values
		}
		$jadwal->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($jadwal->CurrentAction == "gridadd") // Grid add
			$jadwal->RowType = EW_ROWTYPE_ADD; // Render add
		if ($jadwal->CurrentAction == "gridadd" && $jadwal->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$jadwal_list->RestoreCurrentRowFormValues($jadwal_list->RowIndex); // Restore form values
		if ($jadwal->CurrentAction == "gridedit") { // Grid edit
			if ($jadwal->EventCancelled) {
				$jadwal_list->RestoreCurrentRowFormValues($jadwal_list->RowIndex); // Restore form values
			}
			if ($jadwal_list->RowAction == "insert")
				$jadwal->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$jadwal->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($jadwal->CurrentAction == "gridedit" && ($jadwal->RowType == EW_ROWTYPE_EDIT || $jadwal->RowType == EW_ROWTYPE_ADD) && $jadwal->EventCancelled) // Update failed
			$jadwal_list->RestoreCurrentRowFormValues($jadwal_list->RowIndex); // Restore form values
		if ($jadwal->RowType == EW_ROWTYPE_EDIT) // Edit row
			$jadwal_list->EditRowCnt++;

		// Set up row id / data-rowindex
		$jadwal->RowAttrs = array_merge($jadwal->RowAttrs, array('data-rowindex'=>$jadwal_list->RowCnt, 'id'=>'r' . $jadwal_list->RowCnt . '_jadwal', 'data-rowtype'=>$jadwal->RowType));

		// Render row
		$jadwal_list->RenderRow();

		// Render list options
		$jadwal_list->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($jadwal_list->RowAction <> "delete" && $jadwal_list->RowAction <> "insertdelete" && !($jadwal_list->RowAction == "insert" && $jadwal->CurrentAction == "F" && $jadwal_list->EmptyRow())) {
?>
	<tr<?php echo $jadwal->RowAttributes() ?>>
<?php

// Render list options (body, left)
$jadwal_list->ListOptions->Render("body", "left", $jadwal_list->RowCnt);
?>
	<?php if ($jadwal->ProdiID->Visible) { // ProdiID ?>
		<td data-name="ProdiID"<?php echo $jadwal->ProdiID->CellAttributes() ?>>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_ProdiID" class="form-group jadwal_ProdiID">
<?php $jadwal->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_ProdiID" data-value-separator="<?php echo $jadwal->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_ProdiID" name="x<?php echo $jadwal_list->RowIndex ?>_ProdiID"<?php echo $jadwal->ProdiID->EditAttributes() ?>>
<?php echo $jadwal->ProdiID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_ProdiID" id="s_x<?php echo $jadwal_list->RowIndex ?>_ProdiID" value="<?php echo $jadwal->ProdiID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_ProdiID" name="o<?php echo $jadwal_list->RowIndex ?>_ProdiID" id="o<?php echo $jadwal_list->RowIndex ?>_ProdiID" value="<?php echo ew_HtmlEncode($jadwal->ProdiID->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_ProdiID" class="form-group jadwal_ProdiID">
<?php $jadwal->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_ProdiID" data-value-separator="<?php echo $jadwal->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_ProdiID" name="x<?php echo $jadwal_list->RowIndex ?>_ProdiID"<?php echo $jadwal->ProdiID->EditAttributes() ?>>
<?php echo $jadwal->ProdiID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_ProdiID" id="s_x<?php echo $jadwal_list->RowIndex ?>_ProdiID" value="<?php echo $jadwal->ProdiID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_ProdiID" class="jadwal_ProdiID">
<span<?php echo $jadwal->ProdiID->ViewAttributes() ?>>
<?php echo $jadwal->ProdiID->ListViewValue() ?></span>
</span>
<?php } ?>
<a id="<?php echo $jadwal_list->PageObjName . "_row_" . $jadwal_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-table="jadwal" data-field="x_JadwalID" name="x<?php echo $jadwal_list->RowIndex ?>_JadwalID" id="x<?php echo $jadwal_list->RowIndex ?>_JadwalID" value="<?php echo ew_HtmlEncode($jadwal->JadwalID->CurrentValue) ?>">
<input type="hidden" data-table="jadwal" data-field="x_JadwalID" name="o<?php echo $jadwal_list->RowIndex ?>_JadwalID" id="o<?php echo $jadwal_list->RowIndex ?>_JadwalID" value="<?php echo ew_HtmlEncode($jadwal->JadwalID->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT || $jadwal->CurrentMode == "edit") { ?>
<input type="hidden" data-table="jadwal" data-field="x_JadwalID" name="x<?php echo $jadwal_list->RowIndex ?>_JadwalID" id="x<?php echo $jadwal_list->RowIndex ?>_JadwalID" value="<?php echo ew_HtmlEncode($jadwal->JadwalID->CurrentValue) ?>">
<?php } ?>
	<?php if ($jadwal->TahunID->Visible) { // TahunID ?>
		<td data-name="TahunID"<?php echo $jadwal->TahunID->CellAttributes() ?>>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_TahunID" class="form-group jadwal_TahunID">
<input type="text" data-table="jadwal" data-field="x_TahunID" name="x<?php echo $jadwal_list->RowIndex ?>_TahunID" id="x<?php echo $jadwal_list->RowIndex ?>_TahunID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($jadwal->TahunID->getPlaceHolder()) ?>" value="<?php echo $jadwal->TahunID->EditValue ?>"<?php echo $jadwal->TahunID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_TahunID" name="o<?php echo $jadwal_list->RowIndex ?>_TahunID" id="o<?php echo $jadwal_list->RowIndex ?>_TahunID" value="<?php echo ew_HtmlEncode($jadwal->TahunID->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_TahunID" class="form-group jadwal_TahunID">
<input type="text" data-table="jadwal" data-field="x_TahunID" name="x<?php echo $jadwal_list->RowIndex ?>_TahunID" id="x<?php echo $jadwal_list->RowIndex ?>_TahunID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($jadwal->TahunID->getPlaceHolder()) ?>" value="<?php echo $jadwal->TahunID->EditValue ?>"<?php echo $jadwal->TahunID->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_TahunID" class="jadwal_TahunID">
<span<?php echo $jadwal->TahunID->ViewAttributes() ?>>
<?php echo $jadwal->TahunID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($jadwal->Sesi->Visible) { // Sesi ?>
		<td data-name="Sesi"<?php echo $jadwal->Sesi->CellAttributes() ?>>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_Sesi" class="form-group jadwal_Sesi">
<select data-table="jadwal" data-field="x_Sesi" data-value-separator="<?php echo $jadwal->Sesi->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_Sesi" name="x<?php echo $jadwal_list->RowIndex ?>_Sesi"<?php echo $jadwal->Sesi->EditAttributes() ?>>
<?php echo $jadwal->Sesi->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_Sesi") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_Sesi" id="s_x<?php echo $jadwal_list->RowIndex ?>_Sesi" value="<?php echo $jadwal->Sesi->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_Sesi" name="o<?php echo $jadwal_list->RowIndex ?>_Sesi" id="o<?php echo $jadwal_list->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($jadwal->Sesi->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_Sesi" class="form-group jadwal_Sesi">
<select data-table="jadwal" data-field="x_Sesi" data-value-separator="<?php echo $jadwal->Sesi->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_Sesi" name="x<?php echo $jadwal_list->RowIndex ?>_Sesi"<?php echo $jadwal->Sesi->EditAttributes() ?>>
<?php echo $jadwal->Sesi->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_Sesi") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_Sesi" id="s_x<?php echo $jadwal_list->RowIndex ?>_Sesi" value="<?php echo $jadwal->Sesi->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_Sesi" class="jadwal_Sesi">
<span<?php echo $jadwal->Sesi->ViewAttributes() ?>>
<?php echo $jadwal->Sesi->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($jadwal->Tingkat->Visible) { // Tingkat ?>
		<td data-name="Tingkat"<?php echo $jadwal->Tingkat->CellAttributes() ?>>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_Tingkat" class="form-group jadwal_Tingkat">
<?php $jadwal->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_Tingkat" data-value-separator="<?php echo $jadwal->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_Tingkat" name="x<?php echo $jadwal_list->RowIndex ?>_Tingkat"<?php echo $jadwal->Tingkat->EditAttributes() ?>>
<?php echo $jadwal->Tingkat->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_Tingkat") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_Tingkat" id="s_x<?php echo $jadwal_list->RowIndex ?>_Tingkat" value="<?php echo $jadwal->Tingkat->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_Tingkat" name="o<?php echo $jadwal_list->RowIndex ?>_Tingkat" id="o<?php echo $jadwal_list->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($jadwal->Tingkat->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_Tingkat" class="form-group jadwal_Tingkat">
<?php $jadwal->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_Tingkat" data-value-separator="<?php echo $jadwal->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_Tingkat" name="x<?php echo $jadwal_list->RowIndex ?>_Tingkat"<?php echo $jadwal->Tingkat->EditAttributes() ?>>
<?php echo $jadwal->Tingkat->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_Tingkat") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_Tingkat" id="s_x<?php echo $jadwal_list->RowIndex ?>_Tingkat" value="<?php echo $jadwal->Tingkat->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_Tingkat" class="jadwal_Tingkat">
<span<?php echo $jadwal->Tingkat->ViewAttributes() ?>>
<?php echo $jadwal->Tingkat->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($jadwal->KelasID->Visible) { // KelasID ?>
		<td data-name="KelasID"<?php echo $jadwal->KelasID->CellAttributes() ?>>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_KelasID" class="form-group jadwal_KelasID">
<select data-table="jadwal" data-field="x_KelasID" data-value-separator="<?php echo $jadwal->KelasID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_KelasID" name="x<?php echo $jadwal_list->RowIndex ?>_KelasID"<?php echo $jadwal->KelasID->EditAttributes() ?>>
<?php echo $jadwal->KelasID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_KelasID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_KelasID" id="s_x<?php echo $jadwal_list->RowIndex ?>_KelasID" value="<?php echo $jadwal->KelasID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_KelasID" name="o<?php echo $jadwal_list->RowIndex ?>_KelasID" id="o<?php echo $jadwal_list->RowIndex ?>_KelasID" value="<?php echo ew_HtmlEncode($jadwal->KelasID->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_KelasID" class="form-group jadwal_KelasID">
<select data-table="jadwal" data-field="x_KelasID" data-value-separator="<?php echo $jadwal->KelasID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_KelasID" name="x<?php echo $jadwal_list->RowIndex ?>_KelasID"<?php echo $jadwal->KelasID->EditAttributes() ?>>
<?php echo $jadwal->KelasID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_KelasID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_KelasID" id="s_x<?php echo $jadwal_list->RowIndex ?>_KelasID" value="<?php echo $jadwal->KelasID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_KelasID" class="jadwal_KelasID">
<span<?php echo $jadwal->KelasID->ViewAttributes() ?>>
<?php echo $jadwal->KelasID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($jadwal->HariID->Visible) { // HariID ?>
		<td data-name="HariID"<?php echo $jadwal->HariID->CellAttributes() ?>>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_HariID" class="form-group jadwal_HariID">
<?php $jadwal->HariID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->HariID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_HariID" data-value-separator="<?php echo $jadwal->HariID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_HariID" name="x<?php echo $jadwal_list->RowIndex ?>_HariID"<?php echo $jadwal->HariID->EditAttributes() ?>>
<?php echo $jadwal->HariID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_HariID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_HariID" id="s_x<?php echo $jadwal_list->RowIndex ?>_HariID" value="<?php echo $jadwal->HariID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_HariID" name="o<?php echo $jadwal_list->RowIndex ?>_HariID" id="o<?php echo $jadwal_list->RowIndex ?>_HariID" value="<?php echo ew_HtmlEncode($jadwal->HariID->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_HariID" class="form-group jadwal_HariID">
<?php $jadwal->HariID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->HariID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_HariID" data-value-separator="<?php echo $jadwal->HariID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_HariID" name="x<?php echo $jadwal_list->RowIndex ?>_HariID"<?php echo $jadwal->HariID->EditAttributes() ?>>
<?php echo $jadwal->HariID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_HariID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_HariID" id="s_x<?php echo $jadwal_list->RowIndex ?>_HariID" value="<?php echo $jadwal->HariID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_HariID" class="jadwal_HariID">
<span<?php echo $jadwal->HariID->ViewAttributes() ?>>
<?php echo $jadwal->HariID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($jadwal->JamID->Visible) { // JamID ?>
		<td data-name="JamID"<?php echo $jadwal->JamID->CellAttributes() ?>>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_JamID" class="form-group jadwal_JamID">
<?php $jadwal->JamID->EditAttrs["onchange"] = "ew_AutoFill(this); " . @$jadwal->JamID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_JamID" data-value-separator="<?php echo $jadwal->JamID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_JamID" name="x<?php echo $jadwal_list->RowIndex ?>_JamID"<?php echo $jadwal->JamID->EditAttributes() ?>>
<?php echo $jadwal->JamID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_JamID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_JamID" id="s_x<?php echo $jadwal_list->RowIndex ?>_JamID" value="<?php echo $jadwal->JamID->LookupFilterQuery() ?>">
<input type="hidden" name="ln_x<?php echo $jadwal_list->RowIndex ?>_JamID" id="ln_x<?php echo $jadwal_list->RowIndex ?>_JamID" value="x<?php echo $jadwal_list->RowIndex ?>_JamMulai,x<?php echo $jadwal_list->RowIndex ?>_JamSelesai">
</span>
<input type="hidden" data-table="jadwal" data-field="x_JamID" name="o<?php echo $jadwal_list->RowIndex ?>_JamID" id="o<?php echo $jadwal_list->RowIndex ?>_JamID" value="<?php echo ew_HtmlEncode($jadwal->JamID->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_JamID" class="form-group jadwal_JamID">
<?php $jadwal->JamID->EditAttrs["onchange"] = "ew_AutoFill(this); " . @$jadwal->JamID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_JamID" data-value-separator="<?php echo $jadwal->JamID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_JamID" name="x<?php echo $jadwal_list->RowIndex ?>_JamID"<?php echo $jadwal->JamID->EditAttributes() ?>>
<?php echo $jadwal->JamID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_JamID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_JamID" id="s_x<?php echo $jadwal_list->RowIndex ?>_JamID" value="<?php echo $jadwal->JamID->LookupFilterQuery() ?>">
<input type="hidden" name="ln_x<?php echo $jadwal_list->RowIndex ?>_JamID" id="ln_x<?php echo $jadwal_list->RowIndex ?>_JamID" value="x<?php echo $jadwal_list->RowIndex ?>_JamMulai,x<?php echo $jadwal_list->RowIndex ?>_JamSelesai">
</span>
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_JamID" class="jadwal_JamID">
<span<?php echo $jadwal->JamID->ViewAttributes() ?>>
<?php echo $jadwal->JamID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($jadwal->MKID->Visible) { // MKID ?>
		<td data-name="MKID"<?php echo $jadwal->MKID->CellAttributes() ?>>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_MKID" class="form-group jadwal_MKID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x<?php echo $jadwal_list->RowIndex ?>_MKID"><?php echo (strval($jadwal->MKID->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $jadwal->MKID->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->MKID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $jadwal_list->RowIndex ?>_MKID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="jadwal" data-field="x_MKID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->MKID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $jadwal_list->RowIndex ?>_MKID" id="x<?php echo $jadwal_list->RowIndex ?>_MKID" value="<?php echo $jadwal->MKID->CurrentValue ?>"<?php echo $jadwal->MKID->EditAttributes() ?>>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_MKID" id="s_x<?php echo $jadwal_list->RowIndex ?>_MKID" value="<?php echo $jadwal->MKID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_MKID" name="o<?php echo $jadwal_list->RowIndex ?>_MKID" id="o<?php echo $jadwal_list->RowIndex ?>_MKID" value="<?php echo ew_HtmlEncode($jadwal->MKID->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_MKID" class="form-group jadwal_MKID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x<?php echo $jadwal_list->RowIndex ?>_MKID"><?php echo (strval($jadwal->MKID->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $jadwal->MKID->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->MKID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $jadwal_list->RowIndex ?>_MKID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="jadwal" data-field="x_MKID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->MKID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $jadwal_list->RowIndex ?>_MKID" id="x<?php echo $jadwal_list->RowIndex ?>_MKID" value="<?php echo $jadwal->MKID->CurrentValue ?>"<?php echo $jadwal->MKID->EditAttributes() ?>>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_MKID" id="s_x<?php echo $jadwal_list->RowIndex ?>_MKID" value="<?php echo $jadwal->MKID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_MKID" class="jadwal_MKID">
<span<?php echo $jadwal->MKID->ViewAttributes() ?>>
<?php echo $jadwal->MKID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($jadwal->TeacherID->Visible) { // TeacherID ?>
		<td data-name="TeacherID"<?php echo $jadwal->TeacherID->CellAttributes() ?>>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_TeacherID" class="form-group jadwal_TeacherID">
<?php
$wrkonchange = trim(" " . @$jadwal->TeacherID->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$jadwal->TeacherID->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" style="white-space: nowrap; z-index: <?php echo (9000 - $jadwal_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="sv_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo $jadwal->TeacherID->EditValue ?>" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>"<?php echo $jadwal->TeacherID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_TeacherID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->TeacherID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo ew_HtmlEncode($jadwal->TeacherID->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="q_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fjadwallist.CreateAutoSuggest({"id":"x<?php echo $jadwal_list->RowIndex ?>_TeacherID","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->TeacherID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $jadwal_list->RowIndex ?>_TeacherID',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="s_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(false) ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_TeacherID" name="o<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="o<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo ew_HtmlEncode($jadwal->TeacherID->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_TeacherID" class="form-group jadwal_TeacherID">
<?php
$wrkonchange = trim(" " . @$jadwal->TeacherID->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$jadwal->TeacherID->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" style="white-space: nowrap; z-index: <?php echo (9000 - $jadwal_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="sv_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo $jadwal->TeacherID->EditValue ?>" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>"<?php echo $jadwal->TeacherID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_TeacherID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->TeacherID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo ew_HtmlEncode($jadwal->TeacherID->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="q_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fjadwallist.CreateAutoSuggest({"id":"x<?php echo $jadwal_list->RowIndex ?>_TeacherID","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->TeacherID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $jadwal_list->RowIndex ?>_TeacherID',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="s_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(false) ?>">
</span>
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_TeacherID" class="jadwal_TeacherID">
<span<?php echo $jadwal->TeacherID->ViewAttributes() ?>>
<?php echo $jadwal->TeacherID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($jadwal->JamMulai->Visible) { // JamMulai ?>
		<td data-name="JamMulai"<?php echo $jadwal->JamMulai->CellAttributes() ?>>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_JamMulai" class="form-group jadwal_JamMulai">
<input type="text" data-table="jadwal" data-field="x_JamMulai" name="x<?php echo $jadwal_list->RowIndex ?>_JamMulai" id="x<?php echo $jadwal_list->RowIndex ?>_JamMulai" size="3" placeholder="<?php echo ew_HtmlEncode($jadwal->JamMulai->getPlaceHolder()) ?>" value="<?php echo $jadwal->JamMulai->EditValue ?>"<?php echo $jadwal->JamMulai->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_JamMulai" name="o<?php echo $jadwal_list->RowIndex ?>_JamMulai" id="o<?php echo $jadwal_list->RowIndex ?>_JamMulai" value="<?php echo ew_HtmlEncode($jadwal->JamMulai->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_JamMulai" class="form-group jadwal_JamMulai">
<input type="text" data-table="jadwal" data-field="x_JamMulai" name="x<?php echo $jadwal_list->RowIndex ?>_JamMulai" id="x<?php echo $jadwal_list->RowIndex ?>_JamMulai" size="3" placeholder="<?php echo ew_HtmlEncode($jadwal->JamMulai->getPlaceHolder()) ?>" value="<?php echo $jadwal->JamMulai->EditValue ?>"<?php echo $jadwal->JamMulai->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_JamMulai" class="jadwal_JamMulai">
<span<?php echo $jadwal->JamMulai->ViewAttributes() ?>>
<?php echo $jadwal->JamMulai->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($jadwal->JamSelesai->Visible) { // JamSelesai ?>
		<td data-name="JamSelesai"<?php echo $jadwal->JamSelesai->CellAttributes() ?>>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_JamSelesai" class="form-group jadwal_JamSelesai">
<input type="text" data-table="jadwal" data-field="x_JamSelesai" name="x<?php echo $jadwal_list->RowIndex ?>_JamSelesai" id="x<?php echo $jadwal_list->RowIndex ?>_JamSelesai" size="3" placeholder="<?php echo ew_HtmlEncode($jadwal->JamSelesai->getPlaceHolder()) ?>" value="<?php echo $jadwal->JamSelesai->EditValue ?>"<?php echo $jadwal->JamSelesai->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_JamSelesai" name="o<?php echo $jadwal_list->RowIndex ?>_JamSelesai" id="o<?php echo $jadwal_list->RowIndex ?>_JamSelesai" value="<?php echo ew_HtmlEncode($jadwal->JamSelesai->OldValue) ?>">
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_JamSelesai" class="form-group jadwal_JamSelesai">
<input type="text" data-table="jadwal" data-field="x_JamSelesai" name="x<?php echo $jadwal_list->RowIndex ?>_JamSelesai" id="x<?php echo $jadwal_list->RowIndex ?>_JamSelesai" size="3" placeholder="<?php echo ew_HtmlEncode($jadwal->JamSelesai->getPlaceHolder()) ?>" value="<?php echo $jadwal->JamSelesai->EditValue ?>"<?php echo $jadwal->JamSelesai->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($jadwal->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $jadwal_list->RowCnt ?>_jadwal_JamSelesai" class="jadwal_JamSelesai">
<span<?php echo $jadwal->JamSelesai->ViewAttributes() ?>>
<?php echo $jadwal->JamSelesai->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$jadwal_list->ListOptions->Render("body", "right", $jadwal_list->RowCnt);
?>
	</tr>
<?php if ($jadwal->RowType == EW_ROWTYPE_ADD || $jadwal->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fjadwallist.UpdateOpts(<?php echo $jadwal_list->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($jadwal->CurrentAction <> "gridadd")
		if (!$jadwal_list->Recordset->EOF) $jadwal_list->Recordset->MoveNext();
}
?>
<?php
	if ($jadwal->CurrentAction == "gridadd" || $jadwal->CurrentAction == "gridedit") {
		$jadwal_list->RowIndex = '$rowindex$';
		$jadwal_list->LoadDefaultValues();

		// Set row properties
		$jadwal->ResetAttrs();
		$jadwal->RowAttrs = array_merge($jadwal->RowAttrs, array('data-rowindex'=>$jadwal_list->RowIndex, 'id'=>'r0_jadwal', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($jadwal->RowAttrs["class"], "ewTemplate");
		$jadwal->RowType = EW_ROWTYPE_ADD;

		// Render row
		$jadwal_list->RenderRow();

		// Render list options
		$jadwal_list->RenderListOptions();
		$jadwal_list->StartRowCnt = 0;
?>
	<tr<?php echo $jadwal->RowAttributes() ?>>
<?php

// Render list options (body, left)
$jadwal_list->ListOptions->Render("body", "left", $jadwal_list->RowIndex);
?>
	<?php if ($jadwal->ProdiID->Visible) { // ProdiID ?>
		<td data-name="ProdiID">
<span id="el$rowindex$_jadwal_ProdiID" class="form-group jadwal_ProdiID">
<?php $jadwal->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_ProdiID" data-value-separator="<?php echo $jadwal->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_ProdiID" name="x<?php echo $jadwal_list->RowIndex ?>_ProdiID"<?php echo $jadwal->ProdiID->EditAttributes() ?>>
<?php echo $jadwal->ProdiID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_ProdiID" id="s_x<?php echo $jadwal_list->RowIndex ?>_ProdiID" value="<?php echo $jadwal->ProdiID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_ProdiID" name="o<?php echo $jadwal_list->RowIndex ?>_ProdiID" id="o<?php echo $jadwal_list->RowIndex ?>_ProdiID" value="<?php echo ew_HtmlEncode($jadwal->ProdiID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($jadwal->TahunID->Visible) { // TahunID ?>
		<td data-name="TahunID">
<span id="el$rowindex$_jadwal_TahunID" class="form-group jadwal_TahunID">
<input type="text" data-table="jadwal" data-field="x_TahunID" name="x<?php echo $jadwal_list->RowIndex ?>_TahunID" id="x<?php echo $jadwal_list->RowIndex ?>_TahunID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($jadwal->TahunID->getPlaceHolder()) ?>" value="<?php echo $jadwal->TahunID->EditValue ?>"<?php echo $jadwal->TahunID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_TahunID" name="o<?php echo $jadwal_list->RowIndex ?>_TahunID" id="o<?php echo $jadwal_list->RowIndex ?>_TahunID" value="<?php echo ew_HtmlEncode($jadwal->TahunID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($jadwal->Sesi->Visible) { // Sesi ?>
		<td data-name="Sesi">
<span id="el$rowindex$_jadwal_Sesi" class="form-group jadwal_Sesi">
<select data-table="jadwal" data-field="x_Sesi" data-value-separator="<?php echo $jadwal->Sesi->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_Sesi" name="x<?php echo $jadwal_list->RowIndex ?>_Sesi"<?php echo $jadwal->Sesi->EditAttributes() ?>>
<?php echo $jadwal->Sesi->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_Sesi") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_Sesi" id="s_x<?php echo $jadwal_list->RowIndex ?>_Sesi" value="<?php echo $jadwal->Sesi->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_Sesi" name="o<?php echo $jadwal_list->RowIndex ?>_Sesi" id="o<?php echo $jadwal_list->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($jadwal->Sesi->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($jadwal->Tingkat->Visible) { // Tingkat ?>
		<td data-name="Tingkat">
<span id="el$rowindex$_jadwal_Tingkat" class="form-group jadwal_Tingkat">
<?php $jadwal->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_Tingkat" data-value-separator="<?php echo $jadwal->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_Tingkat" name="x<?php echo $jadwal_list->RowIndex ?>_Tingkat"<?php echo $jadwal->Tingkat->EditAttributes() ?>>
<?php echo $jadwal->Tingkat->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_Tingkat") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_Tingkat" id="s_x<?php echo $jadwal_list->RowIndex ?>_Tingkat" value="<?php echo $jadwal->Tingkat->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_Tingkat" name="o<?php echo $jadwal_list->RowIndex ?>_Tingkat" id="o<?php echo $jadwal_list->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($jadwal->Tingkat->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($jadwal->KelasID->Visible) { // KelasID ?>
		<td data-name="KelasID">
<span id="el$rowindex$_jadwal_KelasID" class="form-group jadwal_KelasID">
<select data-table="jadwal" data-field="x_KelasID" data-value-separator="<?php echo $jadwal->KelasID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_KelasID" name="x<?php echo $jadwal_list->RowIndex ?>_KelasID"<?php echo $jadwal->KelasID->EditAttributes() ?>>
<?php echo $jadwal->KelasID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_KelasID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_KelasID" id="s_x<?php echo $jadwal_list->RowIndex ?>_KelasID" value="<?php echo $jadwal->KelasID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_KelasID" name="o<?php echo $jadwal_list->RowIndex ?>_KelasID" id="o<?php echo $jadwal_list->RowIndex ?>_KelasID" value="<?php echo ew_HtmlEncode($jadwal->KelasID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($jadwal->HariID->Visible) { // HariID ?>
		<td data-name="HariID">
<span id="el$rowindex$_jadwal_HariID" class="form-group jadwal_HariID">
<?php $jadwal->HariID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->HariID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_HariID" data-value-separator="<?php echo $jadwal->HariID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_HariID" name="x<?php echo $jadwal_list->RowIndex ?>_HariID"<?php echo $jadwal->HariID->EditAttributes() ?>>
<?php echo $jadwal->HariID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_HariID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_HariID" id="s_x<?php echo $jadwal_list->RowIndex ?>_HariID" value="<?php echo $jadwal->HariID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_HariID" name="o<?php echo $jadwal_list->RowIndex ?>_HariID" id="o<?php echo $jadwal_list->RowIndex ?>_HariID" value="<?php echo ew_HtmlEncode($jadwal->HariID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($jadwal->JamID->Visible) { // JamID ?>
		<td data-name="JamID">
<span id="el$rowindex$_jadwal_JamID" class="form-group jadwal_JamID">
<?php $jadwal->JamID->EditAttrs["onchange"] = "ew_AutoFill(this); " . @$jadwal->JamID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_JamID" data-value-separator="<?php echo $jadwal->JamID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $jadwal_list->RowIndex ?>_JamID" name="x<?php echo $jadwal_list->RowIndex ?>_JamID"<?php echo $jadwal->JamID->EditAttributes() ?>>
<?php echo $jadwal->JamID->SelectOptionListHtml("x<?php echo $jadwal_list->RowIndex ?>_JamID") ?>
</select>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_JamID" id="s_x<?php echo $jadwal_list->RowIndex ?>_JamID" value="<?php echo $jadwal->JamID->LookupFilterQuery() ?>">
<input type="hidden" name="ln_x<?php echo $jadwal_list->RowIndex ?>_JamID" id="ln_x<?php echo $jadwal_list->RowIndex ?>_JamID" value="x<?php echo $jadwal_list->RowIndex ?>_JamMulai,x<?php echo $jadwal_list->RowIndex ?>_JamSelesai">
</span>
<input type="hidden" data-table="jadwal" data-field="x_JamID" name="o<?php echo $jadwal_list->RowIndex ?>_JamID" id="o<?php echo $jadwal_list->RowIndex ?>_JamID" value="<?php echo ew_HtmlEncode($jadwal->JamID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($jadwal->MKID->Visible) { // MKID ?>
		<td data-name="MKID">
<span id="el$rowindex$_jadwal_MKID" class="form-group jadwal_MKID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x<?php echo $jadwal_list->RowIndex ?>_MKID"><?php echo (strval($jadwal->MKID->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $jadwal->MKID->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->MKID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $jadwal_list->RowIndex ?>_MKID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="jadwal" data-field="x_MKID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->MKID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $jadwal_list->RowIndex ?>_MKID" id="x<?php echo $jadwal_list->RowIndex ?>_MKID" value="<?php echo $jadwal->MKID->CurrentValue ?>"<?php echo $jadwal->MKID->EditAttributes() ?>>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_MKID" id="s_x<?php echo $jadwal_list->RowIndex ?>_MKID" value="<?php echo $jadwal->MKID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_MKID" name="o<?php echo $jadwal_list->RowIndex ?>_MKID" id="o<?php echo $jadwal_list->RowIndex ?>_MKID" value="<?php echo ew_HtmlEncode($jadwal->MKID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($jadwal->TeacherID->Visible) { // TeacherID ?>
		<td data-name="TeacherID">
<span id="el$rowindex$_jadwal_TeacherID" class="form-group jadwal_TeacherID">
<?php
$wrkonchange = trim(" " . @$jadwal->TeacherID->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$jadwal->TeacherID->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" style="white-space: nowrap; z-index: <?php echo (9000 - $jadwal_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="sv_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo $jadwal->TeacherID->EditValue ?>" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>"<?php echo $jadwal->TeacherID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_TeacherID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->TeacherID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo ew_HtmlEncode($jadwal->TeacherID->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="q_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fjadwallist.CreateAutoSuggest({"id":"x<?php echo $jadwal_list->RowIndex ?>_TeacherID","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->TeacherID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $jadwal_list->RowIndex ?>_TeacherID',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="s_x<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(false) ?>">
</span>
<input type="hidden" data-table="jadwal" data-field="x_TeacherID" name="o<?php echo $jadwal_list->RowIndex ?>_TeacherID" id="o<?php echo $jadwal_list->RowIndex ?>_TeacherID" value="<?php echo ew_HtmlEncode($jadwal->TeacherID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($jadwal->JamMulai->Visible) { // JamMulai ?>
		<td data-name="JamMulai">
<span id="el$rowindex$_jadwal_JamMulai" class="form-group jadwal_JamMulai">
<input type="text" data-table="jadwal" data-field="x_JamMulai" name="x<?php echo $jadwal_list->RowIndex ?>_JamMulai" id="x<?php echo $jadwal_list->RowIndex ?>_JamMulai" size="3" placeholder="<?php echo ew_HtmlEncode($jadwal->JamMulai->getPlaceHolder()) ?>" value="<?php echo $jadwal->JamMulai->EditValue ?>"<?php echo $jadwal->JamMulai->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_JamMulai" name="o<?php echo $jadwal_list->RowIndex ?>_JamMulai" id="o<?php echo $jadwal_list->RowIndex ?>_JamMulai" value="<?php echo ew_HtmlEncode($jadwal->JamMulai->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($jadwal->JamSelesai->Visible) { // JamSelesai ?>
		<td data-name="JamSelesai">
<span id="el$rowindex$_jadwal_JamSelesai" class="form-group jadwal_JamSelesai">
<input type="text" data-table="jadwal" data-field="x_JamSelesai" name="x<?php echo $jadwal_list->RowIndex ?>_JamSelesai" id="x<?php echo $jadwal_list->RowIndex ?>_JamSelesai" size="3" placeholder="<?php echo ew_HtmlEncode($jadwal->JamSelesai->getPlaceHolder()) ?>" value="<?php echo $jadwal->JamSelesai->EditValue ?>"<?php echo $jadwal->JamSelesai->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_JamSelesai" name="o<?php echo $jadwal_list->RowIndex ?>_JamSelesai" id="o<?php echo $jadwal_list->RowIndex ?>_JamSelesai" value="<?php echo ew_HtmlEncode($jadwal->JamSelesai->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$jadwal_list->ListOptions->Render("body", "right", $jadwal_list->RowCnt);
?>
<script type="text/javascript">
fjadwallist.UpdateOpts(<?php echo $jadwal_list->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($jadwal->CurrentAction == "gridadd") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $jadwal_list->FormKeyCountName ?>" id="<?php echo $jadwal_list->FormKeyCountName ?>" value="<?php echo $jadwal_list->KeyCount ?>">
<?php echo $jadwal_list->MultiSelectKey ?>
<?php } ?>
<?php if ($jadwal->CurrentAction == "gridedit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $jadwal_list->FormKeyCountName ?>" id="<?php echo $jadwal_list->FormKeyCountName ?>" value="<?php echo $jadwal_list->KeyCount ?>">
<?php echo $jadwal_list->MultiSelectKey ?>
<?php } ?>
<?php if ($jadwal->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($jadwal_list->Recordset)
	$jadwal_list->Recordset->Close();
?>
<?php if ($jadwal->Export == "") { ?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($jadwal->CurrentAction <> "gridadd" && $jadwal->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($jadwal_list->Pager)) $jadwal_list->Pager = new cPrevNextPager($jadwal_list->StartRec, $jadwal_list->DisplayRecs, $jadwal_list->TotalRecs) ?>
<?php if ($jadwal_list->Pager->RecordCount > 0 && $jadwal_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($jadwal_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $jadwal_list->PageUrl() ?>start=<?php echo $jadwal_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($jadwal_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $jadwal_list->PageUrl() ?>start=<?php echo $jadwal_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $jadwal_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($jadwal_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $jadwal_list->PageUrl() ?>start=<?php echo $jadwal_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($jadwal_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $jadwal_list->PageUrl() ?>start=<?php echo $jadwal_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $jadwal_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $jadwal_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $jadwal_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $jadwal_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($jadwal_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($jadwal_list->TotalRecs == 0 && $jadwal->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($jadwal_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($jadwal->Export == "") { ?>
<script type="text/javascript">
fjadwallistsrch.FilterList = <?php echo $jadwal_list->GetFilterList() ?>;
fjadwallistsrch.Init();
fjadwallist.Init();
</script>
<?php } ?>
<?php
$jadwal_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($jadwal->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$jadwal_list->Page_Terminate();
?>
