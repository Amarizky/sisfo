<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "khsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$khs_list = NULL; // Initialize page object first

class ckhs_list extends ckhs {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'khs';

	// Page object name
	var $PageObjName = 'khs_list';

	// Grid form hidden field names
	var $FormName = 'fkhslist';
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

		// Table object (khs)
		if (!isset($GLOBALS["khs"]) || get_class($GLOBALS["khs"]) == "ckhs") {
			$GLOBALS["khs"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["khs"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "khsadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "khsdelete.php";
		$this->MultiUpdateUrl = "khsupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'khs', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fkhslistsrch";

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
		$this->Kelas->SetVisibility();
		$this->StudentID->SetVisibility();
		$this->StatusStudentID->SetVisibility();
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
		global $EW_EXPORT, $khs;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($khs);
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
			$this->KHSID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->KHSID->FormValue))
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
					$sKey .= $this->KHSID->CurrentValue;

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
		if ($objForm->HasValue("x_Kelas") && $objForm->HasValue("o_Kelas") && $this->Kelas->CurrentValue <> $this->Kelas->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_StudentID") && $objForm->HasValue("o_StudentID") && $this->StudentID->CurrentValue <> $this->StudentID->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_StatusStudentID") && $objForm->HasValue("o_StatusStudentID") && $this->StatusStudentID->CurrentValue <> $this->StatusStudentID->OldValue)
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
			$sSavedFilterList = isset($UserProfile) ? $UserProfile->GetSearchFilters(CurrentUserName(), "fkhslistsrch") : "";
		} else {
			$sSavedFilterList = "";
		}

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->ProdiID->AdvancedSearch->ToJSON(), ","); // Field ProdiID
		$sFilterList = ew_Concat($sFilterList, $this->TahunID->AdvancedSearch->ToJSON(), ","); // Field TahunID
		$sFilterList = ew_Concat($sFilterList, $this->Sesi->AdvancedSearch->ToJSON(), ","); // Field Sesi
		$sFilterList = ew_Concat($sFilterList, $this->Tingkat->AdvancedSearch->ToJSON(), ","); // Field Tingkat
		$sFilterList = ew_Concat($sFilterList, $this->Kelas->AdvancedSearch->ToJSON(), ","); // Field Kelas
		$sFilterList = ew_Concat($sFilterList, $this->StudentID->AdvancedSearch->ToJSON(), ","); // Field StudentID
		$sFilterList = ew_Concat($sFilterList, $this->StatusStudentID->AdvancedSearch->ToJSON(), ","); // Field StatusStudentID
		$sFilterList = ew_Concat($sFilterList, $this->Keterangan->AdvancedSearch->ToJSON(), ","); // Field Keterangan
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
			$UserProfile->SetSearchFilters(CurrentUserName(), "fkhslistsrch", $filters);

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

		// Field Kelas
		$this->Kelas->AdvancedSearch->SearchValue = @$filter["x_Kelas"];
		$this->Kelas->AdvancedSearch->SearchOperator = @$filter["z_Kelas"];
		$this->Kelas->AdvancedSearch->SearchCondition = @$filter["v_Kelas"];
		$this->Kelas->AdvancedSearch->SearchValue2 = @$filter["y_Kelas"];
		$this->Kelas->AdvancedSearch->SearchOperator2 = @$filter["w_Kelas"];
		$this->Kelas->AdvancedSearch->Save();

		// Field StudentID
		$this->StudentID->AdvancedSearch->SearchValue = @$filter["x_StudentID"];
		$this->StudentID->AdvancedSearch->SearchOperator = @$filter["z_StudentID"];
		$this->StudentID->AdvancedSearch->SearchCondition = @$filter["v_StudentID"];
		$this->StudentID->AdvancedSearch->SearchValue2 = @$filter["y_StudentID"];
		$this->StudentID->AdvancedSearch->SearchOperator2 = @$filter["w_StudentID"];
		$this->StudentID->AdvancedSearch->Save();

		// Field StatusStudentID
		$this->StatusStudentID->AdvancedSearch->SearchValue = @$filter["x_StatusStudentID"];
		$this->StatusStudentID->AdvancedSearch->SearchOperator = @$filter["z_StatusStudentID"];
		$this->StatusStudentID->AdvancedSearch->SearchCondition = @$filter["v_StatusStudentID"];
		$this->StatusStudentID->AdvancedSearch->SearchValue2 = @$filter["y_StatusStudentID"];
		$this->StatusStudentID->AdvancedSearch->SearchOperator2 = @$filter["w_StatusStudentID"];
		$this->StatusStudentID->AdvancedSearch->Save();

		// Field Keterangan
		$this->Keterangan->AdvancedSearch->SearchValue = @$filter["x_Keterangan"];
		$this->Keterangan->AdvancedSearch->SearchOperator = @$filter["z_Keterangan"];
		$this->Keterangan->AdvancedSearch->SearchCondition = @$filter["v_Keterangan"];
		$this->Keterangan->AdvancedSearch->SearchValue2 = @$filter["y_Keterangan"];
		$this->Keterangan->AdvancedSearch->SearchOperator2 = @$filter["w_Keterangan"];
		$this->Keterangan->AdvancedSearch->Save();

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
		$this->BuildSearchSql($sWhere, $this->Sesi, $Default, TRUE); // Sesi
		$this->BuildSearchSql($sWhere, $this->Tingkat, $Default, FALSE); // Tingkat
		$this->BuildSearchSql($sWhere, $this->Kelas, $Default, FALSE); // Kelas
		$this->BuildSearchSql($sWhere, $this->StudentID, $Default, FALSE); // StudentID
		$this->BuildSearchSql($sWhere, $this->StatusStudentID, $Default, FALSE); // StatusStudentID
		$this->BuildSearchSql($sWhere, $this->Keterangan, $Default, FALSE); // Keterangan
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
			$this->Kelas->AdvancedSearch->Save(); // Kelas
			$this->StudentID->AdvancedSearch->Save(); // StudentID
			$this->StatusStudentID->AdvancedSearch->Save(); // StatusStudentID
			$this->Keterangan->AdvancedSearch->Save(); // Keterangan
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
		$this->BuildBasicSearchSQL($sWhere, $this->Kelas, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->StudentID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->StatusStudentID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Keterangan, $arKeywords, $type);
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
		if ($this->Kelas->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->StudentID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->StatusStudentID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Keterangan->AdvancedSearch->IssetSession())
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
		$this->Kelas->AdvancedSearch->UnsetSession();
		$this->StudentID->AdvancedSearch->UnsetSession();
		$this->StatusStudentID->AdvancedSearch->UnsetSession();
		$this->Keterangan->AdvancedSearch->UnsetSession();
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
		$this->Kelas->AdvancedSearch->Load();
		$this->StudentID->AdvancedSearch->Load();
		$this->StatusStudentID->AdvancedSearch->Load();
		$this->Keterangan->AdvancedSearch->Load();
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
			$this->UpdateSort($this->Kelas); // Kelas
			$this->UpdateSort($this->StudentID); // StudentID
			$this->UpdateSort($this->StatusStudentID); // StatusStudentID
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
				$this->TahunID->setSort("");
				$this->Sesi->setSort("");
				$this->Tingkat->setSort("");
				$this->Kelas->setSort("");
				$this->StudentID->setSort("");
				$this->StatusStudentID->setSort("");
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
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->KHSID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event);'>";
		if ($this->CurrentAction == "gridedit" && is_numeric($this->RowIndex)) {
			$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $KeyName . "\" id=\"" . $KeyName . "\" value=\"" . $this->KHSID->CurrentValue . "\">";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fkhslistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fkhslistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
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
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fkhslist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fkhslistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		if (ew_IsMobile())
			$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"khssrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		else
			$item->Body = "<button type=\"button\" class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-table=\"khs\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" onclick=\"ew_ModalDialogShow({lnk:this,url:'khssrch.php',caption:'" . $Language->Phrase("Search") . "'});\">" . $Language->Phrase("AdvancedSearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Search highlight button
		$item = &$this->SearchOptions->Add("searchhighlight");
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewHighlight active\" title=\"" . $Language->Phrase("Highlight") . "\" data-caption=\"" . $Language->Phrase("Highlight") . "\" data-toggle=\"button\" data-form=\"fkhslistsrch\" data-name=\"" . $this->HighlightName() . "\">" . $Language->Phrase("HighlightBtn") . "</button>";
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
		$this->Sesi->CurrentValue = 0;
		$this->Sesi->OldValue = $this->Sesi->CurrentValue;
		$this->Tingkat->CurrentValue = NULL;
		$this->Tingkat->OldValue = $this->Tingkat->CurrentValue;
		$this->Kelas->CurrentValue = NULL;
		$this->Kelas->OldValue = $this->Kelas->CurrentValue;
		$this->StudentID->CurrentValue = NULL;
		$this->StudentID->OldValue = $this->StudentID->CurrentValue;
		$this->StatusStudentID->CurrentValue = "A";
		$this->StatusStudentID->OldValue = $this->StatusStudentID->CurrentValue;
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
		if (is_array($this->Sesi->AdvancedSearch->SearchValue)) $this->Sesi->AdvancedSearch->SearchValue = implode(",", $this->Sesi->AdvancedSearch->SearchValue);
		if (is_array($this->Sesi->AdvancedSearch->SearchValue2)) $this->Sesi->AdvancedSearch->SearchValue2 = implode(",", $this->Sesi->AdvancedSearch->SearchValue2);

		// Tingkat
		$this->Tingkat->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Tingkat"]);
		if ($this->Tingkat->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Tingkat->AdvancedSearch->SearchOperator = @$_GET["z_Tingkat"];

		// Kelas
		$this->Kelas->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Kelas"]);
		if ($this->Kelas->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Kelas->AdvancedSearch->SearchOperator = @$_GET["z_Kelas"];

		// StudentID
		$this->StudentID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_StudentID"]);
		if ($this->StudentID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->StudentID->AdvancedSearch->SearchOperator = @$_GET["z_StudentID"];

		// StatusStudentID
		$this->StatusStudentID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_StatusStudentID"]);
		if ($this->StatusStudentID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->StatusStudentID->AdvancedSearch->SearchOperator = @$_GET["z_StatusStudentID"];

		// Keterangan
		$this->Keterangan->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Keterangan"]);
		if ($this->Keterangan->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Keterangan->AdvancedSearch->SearchOperator = @$_GET["z_Keterangan"];

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
		if (!$this->Kelas->FldIsDetailKey) {
			$this->Kelas->setFormValue($objForm->GetValue("x_Kelas"));
		}
		$this->Kelas->setOldValue($objForm->GetValue("o_Kelas"));
		if (!$this->StudentID->FldIsDetailKey) {
			$this->StudentID->setFormValue($objForm->GetValue("x_StudentID"));
		}
		$this->StudentID->setOldValue($objForm->GetValue("o_StudentID"));
		if (!$this->StatusStudentID->FldIsDetailKey) {
			$this->StatusStudentID->setFormValue($objForm->GetValue("x_StatusStudentID"));
		}
		$this->StatusStudentID->setOldValue($objForm->GetValue("o_StatusStudentID"));
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
		$this->NA->setOldValue($objForm->GetValue("o_NA"));
		if (!$this->KHSID->FldIsDetailKey && $this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->KHSID->setFormValue($objForm->GetValue("x_KHSID"));
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		if ($this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->KHSID->CurrentValue = $this->KHSID->FormValue;
		$this->ProdiID->CurrentValue = $this->ProdiID->FormValue;
		$this->TahunID->CurrentValue = $this->TahunID->FormValue;
		$this->Sesi->CurrentValue = $this->Sesi->FormValue;
		$this->Tingkat->CurrentValue = $this->Tingkat->FormValue;
		$this->Kelas->CurrentValue = $this->Kelas->FormValue;
		$this->StudentID->CurrentValue = $this->StudentID->FormValue;
		$this->StatusStudentID->CurrentValue = $this->StatusStudentID->FormValue;
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
		$this->KHSID->setDbValue($rs->fields('KHSID'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->TahunID->setDbValue($rs->fields('TahunID'));
		$this->Sesi->setDbValue($rs->fields('Sesi'));
		$this->Tingkat->setDbValue($rs->fields('Tingkat'));
		$this->Kelas->setDbValue($rs->fields('Kelas'));
		$this->StudentID->setDbValue($rs->fields('StudentID'));
		$this->StatusStudentID->setDbValue($rs->fields('StatusStudentID'));
		$this->Keterangan->setDbValue($rs->fields('Keterangan'));
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
		$this->KHSID->DbValue = $row['KHSID'];
		$this->ProdiID->DbValue = $row['ProdiID'];
		$this->TahunID->DbValue = $row['TahunID'];
		$this->Sesi->DbValue = $row['Sesi'];
		$this->Tingkat->DbValue = $row['Tingkat'];
		$this->Kelas->DbValue = $row['Kelas'];
		$this->StudentID->DbValue = $row['StudentID'];
		$this->StatusStudentID->DbValue = $row['StatusStudentID'];
		$this->Keterangan->DbValue = $row['Keterangan'];
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
		if (strval($this->getKey("KHSID")) <> "")
			$this->KHSID->CurrentValue = $this->getKey("KHSID"); // KHSID
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
		// KHSID
		// ProdiID
		// TahunID
		// Sesi
		// Tingkat
		// Kelas
		// StudentID
		// StatusStudentID
		// Keterangan
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// KHSID
		$this->KHSID->ViewValue = $this->KHSID->CurrentValue;
		$this->KHSID->ViewCustomAttributes = "";

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
		$this->ProdiID->ViewCustomAttributes = "";

		// TahunID
		if (strval($this->TahunID->CurrentValue) <> "") {
			$sFilterWrk = "`TahunID`" . ew_SearchString("=", $this->TahunID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `TahunID`, `TahunID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tahun`";
		$sWhereWrk = "";
		$this->TahunID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->TahunID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `TahunID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->TahunID->ViewValue = $this->TahunID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
			}
		} else {
			$this->TahunID->ViewValue = NULL;
		}
		$this->TahunID->ViewCustomAttributes = "";

		// Sesi
		if (strval($this->Sesi->CurrentValue) <> "") {
			$arwrk = explode(",", $this->Sesi->CurrentValue);
			$sFilterWrk = "";
			foreach ($arwrk as $wrk) {
				if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
				$sFilterWrk .= "`Sesi`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_NUMBER, "");
			}
		$sSqlWrk = "SELECT `Sesi`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_sesi`";
		$sWhereWrk = "";
		$this->Sesi->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->Sesi->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->Sesi->ViewValue .= $this->Sesi->DisplayValue($arwrk);
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->Sesi->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
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
		$this->Tingkat->ViewCustomAttributes = "";

		// Kelas
		if (strval($this->Kelas->CurrentValue) <> "") {
			$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->Kelas->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
		$sWhereWrk = "";
		$this->Kelas->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Kelas, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Kelas->ViewValue = $this->Kelas->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Kelas->ViewValue = $this->Kelas->CurrentValue;
			}
		} else {
			$this->Kelas->ViewValue = NULL;
		}
		$this->Kelas->CssStyle = "font-weight: bold;";
		$this->Kelas->ViewCustomAttributes = "";

		// StudentID
		if (strval($this->StudentID->CurrentValue) <> "") {
			$sFilterWrk = "`StudentID`" . ew_SearchString("=", $this->StudentID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StudentID`, `StudentID` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `student`";
		$sWhereWrk = "";
		$this->StudentID->LookupFilters = array("dx1" => '`StudentID`', "dx2" => '`Nama`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StudentID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->StudentID->ViewValue = $this->StudentID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StudentID->ViewValue = $this->StudentID->CurrentValue;
			}
		} else {
			$this->StudentID->ViewValue = NULL;
		}
		$this->StudentID->CssStyle = "font-weight: bold;";
		$this->StudentID->ViewCustomAttributes = "";

		// StatusStudentID
		if (strval($this->StatusStudentID->CurrentValue) <> "") {
			$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StatusStudentID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statusstudent`";
		$sWhereWrk = "";
		$this->StatusStudentID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StatusStudentID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StatusStudentID->ViewValue = $this->StatusStudentID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StatusStudentID->ViewValue = $this->StatusStudentID->CurrentValue;
			}
		} else {
			$this->StatusStudentID->ViewValue = NULL;
		}
		$this->StatusStudentID->ViewCustomAttributes = "";

		// Keterangan
		$this->Keterangan->ViewValue = $this->Keterangan->CurrentValue;
		$this->Keterangan->ViewCustomAttributes = "";

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

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";
			$this->TahunID->TooltipValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";
			$this->Sesi->TooltipValue = "";

			// Tingkat
			$this->Tingkat->LinkCustomAttributes = "";
			$this->Tingkat->HrefValue = "";
			$this->Tingkat->TooltipValue = "";

			// Kelas
			$this->Kelas->LinkCustomAttributes = "";
			$this->Kelas->HrefValue = "";
			$this->Kelas->TooltipValue = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";
			$this->StudentID->TooltipValue = "";

			// StatusStudentID
			$this->StatusStudentID->LinkCustomAttributes = "";
			$this->StatusStudentID->HrefValue = "";
			$this->StatusStudentID->TooltipValue = "";

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

			// TahunID
			$this->TahunID->EditCustomAttributes = "";
			if (trim(strval($this->TahunID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`TahunID`" . ew_SearchString("=", $this->TahunID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `TahunID`, `TahunID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `tahun`";
			$sWhereWrk = "";
			$this->TahunID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->TahunID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `TahunID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$this->TahunID->ViewValue = $this->TahunID->DisplayValue($arwrk);
			} else {
				$this->TahunID->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->TahunID->EditValue = $arwrk;

			// Sesi
			$this->Sesi->EditCustomAttributes = "";
			if (trim(strval($this->Sesi->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$arwrk = explode(",", $this->Sesi->CurrentValue);
				$sFilterWrk = "";
				foreach ($arwrk as $wrk) {
					if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
					$sFilterWrk .= "`Sesi`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_NUMBER, "");
				}
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
			$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Tingkat->EditValue = $arwrk;

			// Kelas
			$this->Kelas->EditAttrs["class"] = "form-control";
			$this->Kelas->EditCustomAttributes = "";
			if (trim(strval($this->Kelas->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->Kelas->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, `Tingkat` AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Kelas->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Kelas, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Kelas->EditValue = $arwrk;

			// StudentID
			$this->StudentID->EditCustomAttributes = "";
			if (trim(strval($this->StudentID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StudentID`" . ew_SearchString("=", $this->StudentID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StudentID`, `StudentID` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `student`";
			$sWhereWrk = "";
			$this->StudentID->LookupFilters = array("dx1" => '`StudentID`', "dx2" => '`Nama`');
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StudentID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
				$this->StudentID->ViewValue = $this->StudentID->DisplayValue($arwrk);
			} else {
				$this->StudentID->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StudentID->EditValue = $arwrk;

			// StatusStudentID
			$this->StatusStudentID->EditAttrs["class"] = "form-control";
			$this->StatusStudentID->EditCustomAttributes = "";
			if (trim(strval($this->StatusStudentID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StatusStudentID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_statusstudent`";
			$sWhereWrk = "";
			$this->StatusStudentID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StatusStudentID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StatusStudentID->EditValue = $arwrk;

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

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

			// Kelas
			$this->Kelas->LinkCustomAttributes = "";
			$this->Kelas->HrefValue = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";

			// StatusStudentID
			$this->StatusStudentID->LinkCustomAttributes = "";
			$this->StatusStudentID->HrefValue = "";

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

			// TahunID
			$this->TahunID->EditCustomAttributes = "";
			if (trim(strval($this->TahunID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`TahunID`" . ew_SearchString("=", $this->TahunID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `TahunID`, `TahunID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `tahun`";
			$sWhereWrk = "";
			$this->TahunID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->TahunID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `TahunID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$this->TahunID->ViewValue = $this->TahunID->DisplayValue($arwrk);
			} else {
				$this->TahunID->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->TahunID->EditValue = $arwrk;

			// Sesi
			$this->Sesi->EditCustomAttributes = "";
			if (trim(strval($this->Sesi->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$arwrk = explode(",", $this->Sesi->CurrentValue);
				$sFilterWrk = "";
				foreach ($arwrk as $wrk) {
					if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
					$sFilterWrk .= "`Sesi`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_NUMBER, "");
				}
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
			$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Tingkat->EditValue = $arwrk;

			// Kelas
			$this->Kelas->EditAttrs["class"] = "form-control";
			$this->Kelas->EditCustomAttributes = "";
			if (trim(strval($this->Kelas->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->Kelas->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, `Tingkat` AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Kelas->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Kelas, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Kelas->EditValue = $arwrk;

			// StudentID
			$this->StudentID->EditCustomAttributes = "";
			if (trim(strval($this->StudentID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StudentID`" . ew_SearchString("=", $this->StudentID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StudentID`, `StudentID` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `student`";
			$sWhereWrk = "";
			$this->StudentID->LookupFilters = array("dx1" => '`StudentID`', "dx2" => '`Nama`');
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StudentID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
				$this->StudentID->ViewValue = $this->StudentID->DisplayValue($arwrk);
			} else {
				$this->StudentID->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StudentID->EditValue = $arwrk;

			// StatusStudentID
			$this->StatusStudentID->EditAttrs["class"] = "form-control";
			$this->StatusStudentID->EditCustomAttributes = "";
			if (trim(strval($this->StatusStudentID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StatusStudentID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_statusstudent`";
			$sWhereWrk = "";
			$this->StatusStudentID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StatusStudentID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StatusStudentID->EditValue = $arwrk;

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

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

			// Kelas
			$this->Kelas->LinkCustomAttributes = "";
			$this->Kelas->HrefValue = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";

			// StatusStudentID
			$this->StatusStudentID->LinkCustomAttributes = "";
			$this->StatusStudentID->HrefValue = "";

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
		if (!$this->TahunID->FldIsDetailKey && !is_null($this->TahunID->FormValue) && $this->TahunID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TahunID->FldCaption(), $this->TahunID->ReqErrMsg));
		}
		if ($this->Sesi->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Sesi->FldCaption(), $this->Sesi->ReqErrMsg));
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
				$sThisKey .= $row['KHSID'];
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
			$this->Sesi->SetDbValueDef($rsnew, $this->Sesi->CurrentValue, "", $this->Sesi->ReadOnly);

			// Tingkat
			$this->Tingkat->SetDbValueDef($rsnew, $this->Tingkat->CurrentValue, NULL, $this->Tingkat->ReadOnly);

			// Kelas
			$this->Kelas->SetDbValueDef($rsnew, $this->Kelas->CurrentValue, NULL, $this->Kelas->ReadOnly);

			// StudentID
			$this->StudentID->SetDbValueDef($rsnew, $this->StudentID->CurrentValue, NULL, $this->StudentID->ReadOnly);

			// StatusStudentID
			$this->StatusStudentID->SetDbValueDef($rsnew, $this->StatusStudentID->CurrentValue, NULL, $this->StatusStudentID->ReadOnly);

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
		$this->TahunID->SetDbValueDef($rsnew, $this->TahunID->CurrentValue, "", FALSE);

		// Sesi
		$this->Sesi->SetDbValueDef($rsnew, $this->Sesi->CurrentValue, "", strval($this->Sesi->CurrentValue) == "");

		// Tingkat
		$this->Tingkat->SetDbValueDef($rsnew, $this->Tingkat->CurrentValue, NULL, FALSE);

		// Kelas
		$this->Kelas->SetDbValueDef($rsnew, $this->Kelas->CurrentValue, NULL, FALSE);

		// StudentID
		$this->StudentID->SetDbValueDef($rsnew, $this->StudentID->CurrentValue, NULL, FALSE);

		// StatusStudentID
		$this->StatusStudentID->SetDbValueDef($rsnew, $this->StatusStudentID->CurrentValue, NULL, strval($this->StatusStudentID->CurrentValue) == "");

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

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->ProdiID->AdvancedSearch->Load();
		$this->TahunID->AdvancedSearch->Load();
		$this->Sesi->AdvancedSearch->Load();
		$this->Tingkat->AdvancedSearch->Load();
		$this->Kelas->AdvancedSearch->Load();
		$this->StudentID->AdvancedSearch->Load();
		$this->StatusStudentID->AdvancedSearch->Load();
		$this->Keterangan->AdvancedSearch->Load();
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
		$item->Body = "<button id=\"emf_khs\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_khs',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fkhslist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
		case "x_TahunID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `TahunID` AS `LinkFld`, `TahunID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tahun`";
			$sWhereWrk = "{filter}";
			$this->TahunID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`TahunID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TahunID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `TahunID` ASC";
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
			$sWhereWrk = "{filter}";
			$this->Tingkat->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Tingkat` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_Kelas":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KelasID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
			$sWhereWrk = "{filter}";
			$this->Kelas->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KelasID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "", "f2" => '`Tingkat` IN ({filter_value})', "t2" => "200", "fn2" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Kelas, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StudentID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StudentID` AS `LinkFld`, `StudentID` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `student`";
			$sWhereWrk = "{filter}";
			$this->StudentID->LookupFilters = array("dx1" => '`StudentID`', "dx2" => '`Nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StudentID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StudentID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StatusStudentID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StatusStudentID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statusstudent`";
			$sWhereWrk = "";
			$this->StatusStudentID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StatusStudentID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StatusStudentID, $sWhereWrk); // Call Lookup selecting
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
			$my_item2 = &$my_option->Add("Naik Kelas"); 
			$my_item2->Body = "<a id='naik_kelas' class=\"ewAddEdit ewAdd\" title=\"Naik Kelas\" data-caption=\"Naik Kelas\" href=\"#\">Naik Kelas</a>";
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
if (!isset($khs_list)) $khs_list = new ckhs_list();

// Page init
$khs_list->Page_Init();

// Page main
$khs_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$khs_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($khs->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fkhslist = new ew_Form("fkhslist", "list");
fkhslist.FormKeyCountName = '<?php echo $khs_list->FormKeyCountName ?>';

// Validate form
fkhslist.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $khs->ProdiID->FldCaption(), $khs->ProdiID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_TahunID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $khs->TahunID->FldCaption(), $khs->TahunID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Sesi[]");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $khs->Sesi->FldCaption(), $khs->Sesi->ReqErrMsg)) ?>");

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
fkhslist.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "ProdiID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "TahunID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Sesi[]", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Tingkat", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Kelas", false)) return false;
	if (ew_ValueChanged(fobj, infix, "StudentID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "StatusStudentID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "NA", true)) return false;
	return true;
}

// Form_CustomValidate event
fkhslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fkhslist.ValidateRequired = true;
<?php } else { ?>
fkhslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fkhslist.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":["x_TahunID","x_Tingkat","x_Kelas","x_StudentID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fkhslist.Lists["x_TahunID"] = {"LinkField":"x_TahunID","Ajax":true,"AutoFill":true,"DisplayFields":["x_TahunID","","",""],"ParentFields":["x_ProdiID"],"ChildFields":[],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"tahun"};
fkhslist.Lists["x_Sesi[]"] = {"LinkField":"x_Sesi","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaSesi","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_sesi"};
fkhslist.Lists["x_Tingkat"] = {"LinkField":"x_Tingkat","Ajax":true,"AutoFill":false,"DisplayFields":["x_Tingkat","","",""],"ParentFields":["x_ProdiID"],"ChildFields":["x_Kelas"],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"kelas"};
fkhslist.Lists["x_Kelas"] = {"LinkField":"x_KelasID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":["x_ProdiID","x_Tingkat"],"ChildFields":[],"FilterFields":["x_ProdiID","x_Tingkat"],"Options":[],"Template":"","LinkTable":"kelas"};
fkhslist.Lists["x_StudentID"] = {"LinkField":"x_StudentID","Ajax":true,"AutoFill":false,"DisplayFields":["x_StudentID","x_Nama","",""],"ParentFields":["x_ProdiID"],"ChildFields":[],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"student"};
fkhslist.Lists["x_StatusStudentID"] = {"LinkField":"x_StatusStudentID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statusstudent"};
fkhslist.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkhslist.Lists["x_NA"].Options = <?php echo json_encode($khs->NA->Options()) ?>;

// Form object for search
var CurrentSearchForm = fkhslistsrch = new ew_Form("fkhslistsrch");

// Init search panel as collapsed
if (fkhslistsrch) fkhslistsrch.InitSearchPanel = true;
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
<?php if ($khs->Export == "") { ?>
<div class="ewToolbar">
<?php if ($khs->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($khs_list->TotalRecs > 0 && $khs_list->ExportOptions->Visible()) { ?>
<?php $khs_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($khs_list->SearchOptions->Visible()) { ?>
<?php $khs_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($khs_list->FilterOptions->Visible()) { ?>
<?php $khs_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php if ($khs->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
if ($khs->CurrentAction == "gridadd") {
	$khs->CurrentFilter = "0=1";
	$khs_list->StartRec = 1;
	$khs_list->DisplayRecs = $khs->GridAddRowCount;
	$khs_list->TotalRecs = $khs_list->DisplayRecs;
	$khs_list->StopRec = $khs_list->DisplayRecs;
} else {
	$bSelectLimit = $khs_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($khs_list->TotalRecs <= 0)
			$khs_list->TotalRecs = $khs->SelectRecordCount();
	} else {
		if (!$khs_list->Recordset && ($khs_list->Recordset = $khs_list->LoadRecordset()))
			$khs_list->TotalRecs = $khs_list->Recordset->RecordCount();
	}
	$khs_list->StartRec = 1;
	if ($khs_list->DisplayRecs <= 0 || ($khs->Export <> "" && $khs->ExportAll)) // Display all records
		$khs_list->DisplayRecs = $khs_list->TotalRecs;
	if (!($khs->Export <> "" && $khs->ExportAll))
		$khs_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$khs_list->Recordset = $khs_list->LoadRecordset($khs_list->StartRec-1, $khs_list->DisplayRecs);

	// Set no record found message
	if ($khs->CurrentAction == "" && $khs_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$khs_list->setWarningMessage(ew_DeniedMsg());
		if ($khs_list->SearchWhere == "0=101")
			$khs_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$khs_list->setWarningMessage($Language->Phrase("NoRecord"));
	}

	// Audit trail on search
	if ($khs_list->AuditTrailOnSearch && $khs_list->Command == "search" && !$khs_list->RestoreSearch) {
		$searchparm = ew_ServerVar("QUERY_STRING");
		$searchsql = $khs_list->getSessionWhere();
		$khs_list->WriteAuditTrailOnSearch($searchparm, $searchsql);
	}
}
$khs_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($khs->Export == "" && $khs->CurrentAction == "") { ?>
<form name="fkhslistsrch" id="fkhslistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($khs_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fkhslistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="khs">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($khs_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($khs_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $khs_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($khs_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($khs_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($khs_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($khs_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
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
<?php $khs_list->ShowPageHeader(); ?>
<?php
$khs_list->ShowMessage();
?>
<?php if ($khs_list->TotalRecs > 0 || $khs->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid khs">
<?php if ($khs->Export == "") { ?>
<div class="panel-heading ewGridUpperPanel">
<?php if ($khs->CurrentAction <> "gridadd" && $khs->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($khs_list->Pager)) $khs_list->Pager = new cPrevNextPager($khs_list->StartRec, $khs_list->DisplayRecs, $khs_list->TotalRecs) ?>
<?php if ($khs_list->Pager->RecordCount > 0 && $khs_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($khs_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $khs_list->PageUrl() ?>start=<?php echo $khs_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($khs_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $khs_list->PageUrl() ?>start=<?php echo $khs_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $khs_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($khs_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $khs_list->PageUrl() ?>start=<?php echo $khs_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($khs_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $khs_list->PageUrl() ?>start=<?php echo $khs_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $khs_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $khs_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $khs_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $khs_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($khs_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="fkhslist" id="fkhslist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($khs_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $khs_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="khs">
<div id="gmp_khs" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($khs_list->TotalRecs > 0 || $khs->CurrentAction == "gridedit") { ?>
<table id="tbl_khslist" class="table ewTable">
<?php echo $khs->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$khs_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$khs_list->RenderListOptions();

// Render list options (header, left)
$khs_list->ListOptions->Render("header", "left");
?>
<?php if ($khs->ProdiID->Visible) { // ProdiID ?>
	<?php if ($khs->SortUrl($khs->ProdiID) == "") { ?>
		<th data-name="ProdiID"><div id="elh_khs_ProdiID" class="khs_ProdiID"><div class="ewTableHeaderCaption"><?php echo $khs->ProdiID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="ProdiID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $khs->SortUrl($khs->ProdiID) ?>',1);"><div id="elh_khs_ProdiID" class="khs_ProdiID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $khs->ProdiID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($khs->ProdiID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($khs->ProdiID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($khs->TahunID->Visible) { // TahunID ?>
	<?php if ($khs->SortUrl($khs->TahunID) == "") { ?>
		<th data-name="TahunID"><div id="elh_khs_TahunID" class="khs_TahunID"><div class="ewTableHeaderCaption"><?php echo $khs->TahunID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TahunID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $khs->SortUrl($khs->TahunID) ?>',1);"><div id="elh_khs_TahunID" class="khs_TahunID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $khs->TahunID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($khs->TahunID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($khs->TahunID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($khs->Sesi->Visible) { // Sesi ?>
	<?php if ($khs->SortUrl($khs->Sesi) == "") { ?>
		<th data-name="Sesi"><div id="elh_khs_Sesi" class="khs_Sesi"><div class="ewTableHeaderCaption"><?php echo $khs->Sesi->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Sesi"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $khs->SortUrl($khs->Sesi) ?>',1);"><div id="elh_khs_Sesi" class="khs_Sesi">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $khs->Sesi->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($khs->Sesi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($khs->Sesi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($khs->Tingkat->Visible) { // Tingkat ?>
	<?php if ($khs->SortUrl($khs->Tingkat) == "") { ?>
		<th data-name="Tingkat"><div id="elh_khs_Tingkat" class="khs_Tingkat"><div class="ewTableHeaderCaption"><?php echo $khs->Tingkat->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tingkat"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $khs->SortUrl($khs->Tingkat) ?>',1);"><div id="elh_khs_Tingkat" class="khs_Tingkat">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $khs->Tingkat->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($khs->Tingkat->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($khs->Tingkat->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($khs->Kelas->Visible) { // Kelas ?>
	<?php if ($khs->SortUrl($khs->Kelas) == "") { ?>
		<th data-name="Kelas"><div id="elh_khs_Kelas" class="khs_Kelas"><div class="ewTableHeaderCaption"><?php echo $khs->Kelas->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Kelas"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $khs->SortUrl($khs->Kelas) ?>',1);"><div id="elh_khs_Kelas" class="khs_Kelas">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $khs->Kelas->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($khs->Kelas->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($khs->Kelas->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($khs->StudentID->Visible) { // StudentID ?>
	<?php if ($khs->SortUrl($khs->StudentID) == "") { ?>
		<th data-name="StudentID"><div id="elh_khs_StudentID" class="khs_StudentID"><div class="ewTableHeaderCaption"><?php echo $khs->StudentID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="StudentID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $khs->SortUrl($khs->StudentID) ?>',1);"><div id="elh_khs_StudentID" class="khs_StudentID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $khs->StudentID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($khs->StudentID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($khs->StudentID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($khs->StatusStudentID->Visible) { // StatusStudentID ?>
	<?php if ($khs->SortUrl($khs->StatusStudentID) == "") { ?>
		<th data-name="StatusStudentID"><div id="elh_khs_StatusStudentID" class="khs_StatusStudentID"><div class="ewTableHeaderCaption"><?php echo $khs->StatusStudentID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="StatusStudentID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $khs->SortUrl($khs->StatusStudentID) ?>',1);"><div id="elh_khs_StatusStudentID" class="khs_StatusStudentID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $khs->StatusStudentID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($khs->StatusStudentID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($khs->StatusStudentID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($khs->NA->Visible) { // NA ?>
	<?php if ($khs->SortUrl($khs->NA) == "") { ?>
		<th data-name="NA"><div id="elh_khs_NA" class="khs_NA"><div class="ewTableHeaderCaption"><?php echo $khs->NA->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NA"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $khs->SortUrl($khs->NA) ?>',1);"><div id="elh_khs_NA" class="khs_NA">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $khs->NA->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($khs->NA->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($khs->NA->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$khs_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($khs->ExportAll && $khs->Export <> "") {
	$khs_list->StopRec = $khs_list->TotalRecs;
} else {

	// Set the last record to display
	if ($khs_list->TotalRecs > $khs_list->StartRec + $khs_list->DisplayRecs - 1)
		$khs_list->StopRec = $khs_list->StartRec + $khs_list->DisplayRecs - 1;
	else
		$khs_list->StopRec = $khs_list->TotalRecs;
}

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($khs_list->FormKeyCountName) && ($khs->CurrentAction == "gridadd" || $khs->CurrentAction == "gridedit" || $khs->CurrentAction == "F")) {
		$khs_list->KeyCount = $objForm->GetValue($khs_list->FormKeyCountName);
		$khs_list->StopRec = $khs_list->StartRec + $khs_list->KeyCount - 1;
	}
}
$khs_list->RecCnt = $khs_list->StartRec - 1;
if ($khs_list->Recordset && !$khs_list->Recordset->EOF) {
	$khs_list->Recordset->MoveFirst();
	$bSelectLimit = $khs_list->UseSelectLimit;
	if (!$bSelectLimit && $khs_list->StartRec > 1)
		$khs_list->Recordset->Move($khs_list->StartRec - 1);
} elseif (!$khs->AllowAddDeleteRow && $khs_list->StopRec == 0) {
	$khs_list->StopRec = $khs->GridAddRowCount;
}

// Initialize aggregate
$khs->RowType = EW_ROWTYPE_AGGREGATEINIT;
$khs->ResetAttrs();
$khs_list->RenderRow();
if ($khs->CurrentAction == "gridadd")
	$khs_list->RowIndex = 0;
if ($khs->CurrentAction == "gridedit")
	$khs_list->RowIndex = 0;
while ($khs_list->RecCnt < $khs_list->StopRec) {
	$khs_list->RecCnt++;
	if (intval($khs_list->RecCnt) >= intval($khs_list->StartRec)) {
		$khs_list->RowCnt++;
		if ($khs->CurrentAction == "gridadd" || $khs->CurrentAction == "gridedit" || $khs->CurrentAction == "F") {
			$khs_list->RowIndex++;
			$objForm->Index = $khs_list->RowIndex;
			if ($objForm->HasValue($khs_list->FormActionName))
				$khs_list->RowAction = strval($objForm->GetValue($khs_list->FormActionName));
			elseif ($khs->CurrentAction == "gridadd")
				$khs_list->RowAction = "insert";
			else
				$khs_list->RowAction = "";
		}

		// Set up key count
		$khs_list->KeyCount = $khs_list->RowIndex;

		// Init row class and style
		$khs->ResetAttrs();
		$khs->CssClass = "";
		if ($khs->CurrentAction == "gridadd") {
			$khs_list->LoadDefaultValues(); // Load default values
		} else {
			$khs_list->LoadRowValues($khs_list->Recordset); // Load row values
		}
		$khs->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($khs->CurrentAction == "gridadd") // Grid add
			$khs->RowType = EW_ROWTYPE_ADD; // Render add
		if ($khs->CurrentAction == "gridadd" && $khs->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$khs_list->RestoreCurrentRowFormValues($khs_list->RowIndex); // Restore form values
		if ($khs->CurrentAction == "gridedit") { // Grid edit
			if ($khs->EventCancelled) {
				$khs_list->RestoreCurrentRowFormValues($khs_list->RowIndex); // Restore form values
			}
			if ($khs_list->RowAction == "insert")
				$khs->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$khs->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($khs->CurrentAction == "gridedit" && ($khs->RowType == EW_ROWTYPE_EDIT || $khs->RowType == EW_ROWTYPE_ADD) && $khs->EventCancelled) // Update failed
			$khs_list->RestoreCurrentRowFormValues($khs_list->RowIndex); // Restore form values
		if ($khs->RowType == EW_ROWTYPE_EDIT) // Edit row
			$khs_list->EditRowCnt++;

		// Set up row id / data-rowindex
		$khs->RowAttrs = array_merge($khs->RowAttrs, array('data-rowindex'=>$khs_list->RowCnt, 'id'=>'r' . $khs_list->RowCnt . '_khs', 'data-rowtype'=>$khs->RowType));

		// Render row
		$khs_list->RenderRow();

		// Render list options
		$khs_list->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($khs_list->RowAction <> "delete" && $khs_list->RowAction <> "insertdelete" && !($khs_list->RowAction == "insert" && $khs->CurrentAction == "F" && $khs_list->EmptyRow())) {
?>
	<tr<?php echo $khs->RowAttributes() ?>>
<?php

// Render list options (body, left)
$khs_list->ListOptions->Render("body", "left", $khs_list->RowCnt);
?>
	<?php if ($khs->ProdiID->Visible) { // ProdiID ?>
		<td data-name="ProdiID"<?php echo $khs->ProdiID->CellAttributes() ?>>
<?php if ($khs->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_ProdiID" class="form-group khs_ProdiID">
<?php $khs->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_ProdiID" data-value-separator="<?php echo $khs->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_ProdiID" name="x<?php echo $khs_list->RowIndex ?>_ProdiID"<?php echo $khs->ProdiID->EditAttributes() ?>>
<?php echo $khs->ProdiID->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_ProdiID" id="s_x<?php echo $khs_list->RowIndex ?>_ProdiID" value="<?php echo $khs->ProdiID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_ProdiID" name="o<?php echo $khs_list->RowIndex ?>_ProdiID" id="o<?php echo $khs_list->RowIndex ?>_ProdiID" value="<?php echo ew_HtmlEncode($khs->ProdiID->OldValue) ?>">
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_ProdiID" class="form-group khs_ProdiID">
<?php $khs->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_ProdiID" data-value-separator="<?php echo $khs->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_ProdiID" name="x<?php echo $khs_list->RowIndex ?>_ProdiID"<?php echo $khs->ProdiID->EditAttributes() ?>>
<?php echo $khs->ProdiID->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_ProdiID" id="s_x<?php echo $khs_list->RowIndex ?>_ProdiID" value="<?php echo $khs->ProdiID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_ProdiID" class="khs_ProdiID">
<span<?php echo $khs->ProdiID->ViewAttributes() ?>>
<?php echo $khs->ProdiID->ListViewValue() ?></span>
</span>
<?php } ?>
<a id="<?php echo $khs_list->PageObjName . "_row_" . $khs_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-table="khs" data-field="x_KHSID" name="x<?php echo $khs_list->RowIndex ?>_KHSID" id="x<?php echo $khs_list->RowIndex ?>_KHSID" value="<?php echo ew_HtmlEncode($khs->KHSID->CurrentValue) ?>">
<input type="hidden" data-table="khs" data-field="x_KHSID" name="o<?php echo $khs_list->RowIndex ?>_KHSID" id="o<?php echo $khs_list->RowIndex ?>_KHSID" value="<?php echo ew_HtmlEncode($khs->KHSID->OldValue) ?>">
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_EDIT || $khs->CurrentMode == "edit") { ?>
<input type="hidden" data-table="khs" data-field="x_KHSID" name="x<?php echo $khs_list->RowIndex ?>_KHSID" id="x<?php echo $khs_list->RowIndex ?>_KHSID" value="<?php echo ew_HtmlEncode($khs->KHSID->CurrentValue) ?>">
<?php } ?>
	<?php if ($khs->TahunID->Visible) { // TahunID ?>
		<td data-name="TahunID"<?php echo $khs->TahunID->CellAttributes() ?>>
<?php if ($khs->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_TahunID" class="form-group khs_TahunID">
<?php $khs->TahunID->EditAttrs["onclick"] = "ew_AutoFill(this); " . @$khs->TahunID->EditAttrs["onclick"]; ?>
<div class="ewDropdownList has-feedback">
	<span onclick="" class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $khs->TahunID->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x<?php echo $khs_list->RowIndex ?>_TahunID" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php echo $khs->TahunID->RadioButtonListHtml(TRUE, "x{$khs_list->RowIndex}_TahunID") ?>
		</div>
	</div>
	<div id="tp_x<?php echo $khs_list->RowIndex ?>_TahunID" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_TahunID" data-value-separator="<?php echo $khs->TahunID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_TahunID" id="x<?php echo $khs_list->RowIndex ?>_TahunID" value="{value}"<?php echo $khs->TahunID->EditAttributes() ?>></div>
</div>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_TahunID" id="s_x<?php echo $khs_list->RowIndex ?>_TahunID" value="<?php echo $khs->TahunID->LookupFilterQuery() ?>">
<input type="hidden" name="ln_x<?php echo $khs_list->RowIndex ?>_TahunID" id="ln_x<?php echo $khs_list->RowIndex ?>_TahunID" value="x<?php echo $khs_list->RowIndex ?>_Sesi[]">
</span>
<input type="hidden" data-table="khs" data-field="x_TahunID" name="o<?php echo $khs_list->RowIndex ?>_TahunID" id="o<?php echo $khs_list->RowIndex ?>_TahunID" value="<?php echo ew_HtmlEncode($khs->TahunID->OldValue) ?>">
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_TahunID" class="form-group khs_TahunID">
<?php $khs->TahunID->EditAttrs["onclick"] = "ew_AutoFill(this); " . @$khs->TahunID->EditAttrs["onclick"]; ?>
<div class="ewDropdownList has-feedback">
	<span onclick="" class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $khs->TahunID->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x<?php echo $khs_list->RowIndex ?>_TahunID" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php echo $khs->TahunID->RadioButtonListHtml(TRUE, "x{$khs_list->RowIndex}_TahunID") ?>
		</div>
	</div>
	<div id="tp_x<?php echo $khs_list->RowIndex ?>_TahunID" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_TahunID" data-value-separator="<?php echo $khs->TahunID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_TahunID" id="x<?php echo $khs_list->RowIndex ?>_TahunID" value="{value}"<?php echo $khs->TahunID->EditAttributes() ?>></div>
</div>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_TahunID" id="s_x<?php echo $khs_list->RowIndex ?>_TahunID" value="<?php echo $khs->TahunID->LookupFilterQuery() ?>">
<input type="hidden" name="ln_x<?php echo $khs_list->RowIndex ?>_TahunID" id="ln_x<?php echo $khs_list->RowIndex ?>_TahunID" value="x<?php echo $khs_list->RowIndex ?>_Sesi[]">
</span>
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_TahunID" class="khs_TahunID">
<span<?php echo $khs->TahunID->ViewAttributes() ?>>
<?php echo $khs->TahunID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($khs->Sesi->Visible) { // Sesi ?>
		<td data-name="Sesi"<?php echo $khs->Sesi->CellAttributes() ?>>
<?php if ($khs->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_Sesi" class="form-group khs_Sesi">
<div id="tp_x<?php echo $khs_list->RowIndex ?>_Sesi" class="ewTemplate"><input type="checkbox" data-table="khs" data-field="x_Sesi" data-value-separator="<?php echo $khs->Sesi->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_Sesi[]" id="x<?php echo $khs_list->RowIndex ?>_Sesi[]" value="{value}"<?php echo $khs->Sesi->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $khs_list->RowIndex ?>_Sesi" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->Sesi->CheckBoxListHtml(FALSE, "x{$khs_list->RowIndex}_Sesi[]") ?>
</div></div>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_Sesi" id="s_x<?php echo $khs_list->RowIndex ?>_Sesi" value="<?php echo $khs->Sesi->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_Sesi" name="o<?php echo $khs_list->RowIndex ?>_Sesi[]" id="o<?php echo $khs_list->RowIndex ?>_Sesi[]" value="<?php echo ew_HtmlEncode($khs->Sesi->OldValue) ?>">
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_Sesi" class="form-group khs_Sesi">
<div id="tp_x<?php echo $khs_list->RowIndex ?>_Sesi" class="ewTemplate"><input type="checkbox" data-table="khs" data-field="x_Sesi" data-value-separator="<?php echo $khs->Sesi->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_Sesi[]" id="x<?php echo $khs_list->RowIndex ?>_Sesi[]" value="{value}"<?php echo $khs->Sesi->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $khs_list->RowIndex ?>_Sesi" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->Sesi->CheckBoxListHtml(FALSE, "x{$khs_list->RowIndex}_Sesi[]") ?>
</div></div>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_Sesi" id="s_x<?php echo $khs_list->RowIndex ?>_Sesi" value="<?php echo $khs->Sesi->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_Sesi" class="khs_Sesi">
<span<?php echo $khs->Sesi->ViewAttributes() ?>>
<?php echo $khs->Sesi->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($khs->Tingkat->Visible) { // Tingkat ?>
		<td data-name="Tingkat"<?php echo $khs->Tingkat->CellAttributes() ?>>
<?php if ($khs->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_Tingkat" class="form-group khs_Tingkat">
<?php $khs->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_Tingkat" data-value-separator="<?php echo $khs->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_Tingkat" name="x<?php echo $khs_list->RowIndex ?>_Tingkat"<?php echo $khs->Tingkat->EditAttributes() ?>>
<?php echo $khs->Tingkat->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_Tingkat") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_Tingkat" id="s_x<?php echo $khs_list->RowIndex ?>_Tingkat" value="<?php echo $khs->Tingkat->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_Tingkat" name="o<?php echo $khs_list->RowIndex ?>_Tingkat" id="o<?php echo $khs_list->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($khs->Tingkat->OldValue) ?>">
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_Tingkat" class="form-group khs_Tingkat">
<?php $khs->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_Tingkat" data-value-separator="<?php echo $khs->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_Tingkat" name="x<?php echo $khs_list->RowIndex ?>_Tingkat"<?php echo $khs->Tingkat->EditAttributes() ?>>
<?php echo $khs->Tingkat->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_Tingkat") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_Tingkat" id="s_x<?php echo $khs_list->RowIndex ?>_Tingkat" value="<?php echo $khs->Tingkat->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_Tingkat" class="khs_Tingkat">
<span<?php echo $khs->Tingkat->ViewAttributes() ?>>
<?php echo $khs->Tingkat->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($khs->Kelas->Visible) { // Kelas ?>
		<td data-name="Kelas"<?php echo $khs->Kelas->CellAttributes() ?>>
<?php if ($khs->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_Kelas" class="form-group khs_Kelas">
<select data-table="khs" data-field="x_Kelas" data-value-separator="<?php echo $khs->Kelas->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_Kelas" name="x<?php echo $khs_list->RowIndex ?>_Kelas"<?php echo $khs->Kelas->EditAttributes() ?>>
<?php echo $khs->Kelas->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_Kelas") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_Kelas" id="s_x<?php echo $khs_list->RowIndex ?>_Kelas" value="<?php echo $khs->Kelas->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_Kelas" name="o<?php echo $khs_list->RowIndex ?>_Kelas" id="o<?php echo $khs_list->RowIndex ?>_Kelas" value="<?php echo ew_HtmlEncode($khs->Kelas->OldValue) ?>">
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_Kelas" class="form-group khs_Kelas">
<select data-table="khs" data-field="x_Kelas" data-value-separator="<?php echo $khs->Kelas->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_Kelas" name="x<?php echo $khs_list->RowIndex ?>_Kelas"<?php echo $khs->Kelas->EditAttributes() ?>>
<?php echo $khs->Kelas->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_Kelas") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_Kelas" id="s_x<?php echo $khs_list->RowIndex ?>_Kelas" value="<?php echo $khs->Kelas->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_Kelas" class="khs_Kelas">
<span<?php echo $khs->Kelas->ViewAttributes() ?>>
<?php echo $khs->Kelas->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($khs->StudentID->Visible) { // StudentID ?>
		<td data-name="StudentID"<?php echo $khs->StudentID->CellAttributes() ?>>
<?php if ($khs->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_StudentID" class="form-group khs_StudentID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x<?php echo $khs_list->RowIndex ?>_StudentID"><?php echo (strval($khs->StudentID->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $khs->StudentID->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($khs->StudentID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $khs_list->RowIndex ?>_StudentID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="khs" data-field="x_StudentID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $khs->StudentID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_StudentID" id="x<?php echo $khs_list->RowIndex ?>_StudentID" value="<?php echo $khs->StudentID->CurrentValue ?>"<?php echo $khs->StudentID->EditAttributes() ?>>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_StudentID" id="s_x<?php echo $khs_list->RowIndex ?>_StudentID" value="<?php echo $khs->StudentID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_StudentID" name="o<?php echo $khs_list->RowIndex ?>_StudentID" id="o<?php echo $khs_list->RowIndex ?>_StudentID" value="<?php echo ew_HtmlEncode($khs->StudentID->OldValue) ?>">
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_StudentID" class="form-group khs_StudentID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x<?php echo $khs_list->RowIndex ?>_StudentID"><?php echo (strval($khs->StudentID->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $khs->StudentID->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($khs->StudentID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $khs_list->RowIndex ?>_StudentID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="khs" data-field="x_StudentID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $khs->StudentID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_StudentID" id="x<?php echo $khs_list->RowIndex ?>_StudentID" value="<?php echo $khs->StudentID->CurrentValue ?>"<?php echo $khs->StudentID->EditAttributes() ?>>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_StudentID" id="s_x<?php echo $khs_list->RowIndex ?>_StudentID" value="<?php echo $khs->StudentID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_StudentID" class="khs_StudentID">
<span<?php echo $khs->StudentID->ViewAttributes() ?>>
<?php echo $khs->StudentID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($khs->StatusStudentID->Visible) { // StatusStudentID ?>
		<td data-name="StatusStudentID"<?php echo $khs->StatusStudentID->CellAttributes() ?>>
<?php if ($khs->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_StatusStudentID" class="form-group khs_StatusStudentID">
<select data-table="khs" data-field="x_StatusStudentID" data-value-separator="<?php echo $khs->StatusStudentID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_StatusStudentID" name="x<?php echo $khs_list->RowIndex ?>_StatusStudentID"<?php echo $khs->StatusStudentID->EditAttributes() ?>>
<?php echo $khs->StatusStudentID->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_StatusStudentID") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_StatusStudentID" id="s_x<?php echo $khs_list->RowIndex ?>_StatusStudentID" value="<?php echo $khs->StatusStudentID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_StatusStudentID" name="o<?php echo $khs_list->RowIndex ?>_StatusStudentID" id="o<?php echo $khs_list->RowIndex ?>_StatusStudentID" value="<?php echo ew_HtmlEncode($khs->StatusStudentID->OldValue) ?>">
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_StatusStudentID" class="form-group khs_StatusStudentID">
<select data-table="khs" data-field="x_StatusStudentID" data-value-separator="<?php echo $khs->StatusStudentID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_StatusStudentID" name="x<?php echo $khs_list->RowIndex ?>_StatusStudentID"<?php echo $khs->StatusStudentID->EditAttributes() ?>>
<?php echo $khs->StatusStudentID->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_StatusStudentID") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_StatusStudentID" id="s_x<?php echo $khs_list->RowIndex ?>_StatusStudentID" value="<?php echo $khs->StatusStudentID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_StatusStudentID" class="khs_StatusStudentID">
<span<?php echo $khs->StatusStudentID->ViewAttributes() ?>>
<?php echo $khs->StatusStudentID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($khs->NA->Visible) { // NA ?>
		<td data-name="NA"<?php echo $khs->NA->CellAttributes() ?>>
<?php if ($khs->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_NA" class="form-group khs_NA">
<div id="tp_x<?php echo $khs_list->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_NA" data-value-separator="<?php echo $khs->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_NA" id="x<?php echo $khs_list->RowIndex ?>_NA" value="{value}"<?php echo $khs->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $khs_list->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->NA->RadioButtonListHtml(FALSE, "x{$khs_list->RowIndex}_NA") ?>
</div></div>
</span>
<input type="hidden" data-table="khs" data-field="x_NA" name="o<?php echo $khs_list->RowIndex ?>_NA" id="o<?php echo $khs_list->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($khs->NA->OldValue) ?>">
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_NA" class="form-group khs_NA">
<div id="tp_x<?php echo $khs_list->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_NA" data-value-separator="<?php echo $khs->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_NA" id="x<?php echo $khs_list->RowIndex ?>_NA" value="{value}"<?php echo $khs->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $khs_list->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->NA->RadioButtonListHtml(FALSE, "x{$khs_list->RowIndex}_NA") ?>
</div></div>
</span>
<?php } ?>
<?php if ($khs->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $khs_list->RowCnt ?>_khs_NA" class="khs_NA">
<span<?php echo $khs->NA->ViewAttributes() ?>>
<?php echo $khs->NA->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$khs_list->ListOptions->Render("body", "right", $khs_list->RowCnt);
?>
	</tr>
<?php if ($khs->RowType == EW_ROWTYPE_ADD || $khs->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fkhslist.UpdateOpts(<?php echo $khs_list->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($khs->CurrentAction <> "gridadd")
		if (!$khs_list->Recordset->EOF) $khs_list->Recordset->MoveNext();
}
?>
<?php
	if ($khs->CurrentAction == "gridadd" || $khs->CurrentAction == "gridedit") {
		$khs_list->RowIndex = '$rowindex$';
		$khs_list->LoadDefaultValues();

		// Set row properties
		$khs->ResetAttrs();
		$khs->RowAttrs = array_merge($khs->RowAttrs, array('data-rowindex'=>$khs_list->RowIndex, 'id'=>'r0_khs', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($khs->RowAttrs["class"], "ewTemplate");
		$khs->RowType = EW_ROWTYPE_ADD;

		// Render row
		$khs_list->RenderRow();

		// Render list options
		$khs_list->RenderListOptions();
		$khs_list->StartRowCnt = 0;
?>
	<tr<?php echo $khs->RowAttributes() ?>>
<?php

// Render list options (body, left)
$khs_list->ListOptions->Render("body", "left", $khs_list->RowIndex);
?>
	<?php if ($khs->ProdiID->Visible) { // ProdiID ?>
		<td data-name="ProdiID">
<span id="el$rowindex$_khs_ProdiID" class="form-group khs_ProdiID">
<?php $khs->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_ProdiID" data-value-separator="<?php echo $khs->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_ProdiID" name="x<?php echo $khs_list->RowIndex ?>_ProdiID"<?php echo $khs->ProdiID->EditAttributes() ?>>
<?php echo $khs->ProdiID->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_ProdiID") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_ProdiID" id="s_x<?php echo $khs_list->RowIndex ?>_ProdiID" value="<?php echo $khs->ProdiID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_ProdiID" name="o<?php echo $khs_list->RowIndex ?>_ProdiID" id="o<?php echo $khs_list->RowIndex ?>_ProdiID" value="<?php echo ew_HtmlEncode($khs->ProdiID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($khs->TahunID->Visible) { // TahunID ?>
		<td data-name="TahunID">
<span id="el$rowindex$_khs_TahunID" class="form-group khs_TahunID">
<?php $khs->TahunID->EditAttrs["onclick"] = "ew_AutoFill(this); " . @$khs->TahunID->EditAttrs["onclick"]; ?>
<div class="ewDropdownList has-feedback">
	<span onclick="" class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $khs->TahunID->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x<?php echo $khs_list->RowIndex ?>_TahunID" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php echo $khs->TahunID->RadioButtonListHtml(TRUE, "x{$khs_list->RowIndex}_TahunID") ?>
		</div>
	</div>
	<div id="tp_x<?php echo $khs_list->RowIndex ?>_TahunID" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_TahunID" data-value-separator="<?php echo $khs->TahunID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_TahunID" id="x<?php echo $khs_list->RowIndex ?>_TahunID" value="{value}"<?php echo $khs->TahunID->EditAttributes() ?>></div>
</div>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_TahunID" id="s_x<?php echo $khs_list->RowIndex ?>_TahunID" value="<?php echo $khs->TahunID->LookupFilterQuery() ?>">
<input type="hidden" name="ln_x<?php echo $khs_list->RowIndex ?>_TahunID" id="ln_x<?php echo $khs_list->RowIndex ?>_TahunID" value="x<?php echo $khs_list->RowIndex ?>_Sesi[]">
</span>
<input type="hidden" data-table="khs" data-field="x_TahunID" name="o<?php echo $khs_list->RowIndex ?>_TahunID" id="o<?php echo $khs_list->RowIndex ?>_TahunID" value="<?php echo ew_HtmlEncode($khs->TahunID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($khs->Sesi->Visible) { // Sesi ?>
		<td data-name="Sesi">
<span id="el$rowindex$_khs_Sesi" class="form-group khs_Sesi">
<div id="tp_x<?php echo $khs_list->RowIndex ?>_Sesi" class="ewTemplate"><input type="checkbox" data-table="khs" data-field="x_Sesi" data-value-separator="<?php echo $khs->Sesi->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_Sesi[]" id="x<?php echo $khs_list->RowIndex ?>_Sesi[]" value="{value}"<?php echo $khs->Sesi->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $khs_list->RowIndex ?>_Sesi" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->Sesi->CheckBoxListHtml(FALSE, "x{$khs_list->RowIndex}_Sesi[]") ?>
</div></div>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_Sesi" id="s_x<?php echo $khs_list->RowIndex ?>_Sesi" value="<?php echo $khs->Sesi->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_Sesi" name="o<?php echo $khs_list->RowIndex ?>_Sesi[]" id="o<?php echo $khs_list->RowIndex ?>_Sesi[]" value="<?php echo ew_HtmlEncode($khs->Sesi->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($khs->Tingkat->Visible) { // Tingkat ?>
		<td data-name="Tingkat">
<span id="el$rowindex$_khs_Tingkat" class="form-group khs_Tingkat">
<?php $khs->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_Tingkat" data-value-separator="<?php echo $khs->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_Tingkat" name="x<?php echo $khs_list->RowIndex ?>_Tingkat"<?php echo $khs->Tingkat->EditAttributes() ?>>
<?php echo $khs->Tingkat->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_Tingkat") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_Tingkat" id="s_x<?php echo $khs_list->RowIndex ?>_Tingkat" value="<?php echo $khs->Tingkat->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_Tingkat" name="o<?php echo $khs_list->RowIndex ?>_Tingkat" id="o<?php echo $khs_list->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($khs->Tingkat->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($khs->Kelas->Visible) { // Kelas ?>
		<td data-name="Kelas">
<span id="el$rowindex$_khs_Kelas" class="form-group khs_Kelas">
<select data-table="khs" data-field="x_Kelas" data-value-separator="<?php echo $khs->Kelas->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_Kelas" name="x<?php echo $khs_list->RowIndex ?>_Kelas"<?php echo $khs->Kelas->EditAttributes() ?>>
<?php echo $khs->Kelas->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_Kelas") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_Kelas" id="s_x<?php echo $khs_list->RowIndex ?>_Kelas" value="<?php echo $khs->Kelas->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_Kelas" name="o<?php echo $khs_list->RowIndex ?>_Kelas" id="o<?php echo $khs_list->RowIndex ?>_Kelas" value="<?php echo ew_HtmlEncode($khs->Kelas->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($khs->StudentID->Visible) { // StudentID ?>
		<td data-name="StudentID">
<span id="el$rowindex$_khs_StudentID" class="form-group khs_StudentID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x<?php echo $khs_list->RowIndex ?>_StudentID"><?php echo (strval($khs->StudentID->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $khs->StudentID->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($khs->StudentID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $khs_list->RowIndex ?>_StudentID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="khs" data-field="x_StudentID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $khs->StudentID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_StudentID" id="x<?php echo $khs_list->RowIndex ?>_StudentID" value="<?php echo $khs->StudentID->CurrentValue ?>"<?php echo $khs->StudentID->EditAttributes() ?>>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_StudentID" id="s_x<?php echo $khs_list->RowIndex ?>_StudentID" value="<?php echo $khs->StudentID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_StudentID" name="o<?php echo $khs_list->RowIndex ?>_StudentID" id="o<?php echo $khs_list->RowIndex ?>_StudentID" value="<?php echo ew_HtmlEncode($khs->StudentID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($khs->StatusStudentID->Visible) { // StatusStudentID ?>
		<td data-name="StatusStudentID">
<span id="el$rowindex$_khs_StatusStudentID" class="form-group khs_StatusStudentID">
<select data-table="khs" data-field="x_StatusStudentID" data-value-separator="<?php echo $khs->StatusStudentID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $khs_list->RowIndex ?>_StatusStudentID" name="x<?php echo $khs_list->RowIndex ?>_StatusStudentID"<?php echo $khs->StatusStudentID->EditAttributes() ?>>
<?php echo $khs->StatusStudentID->SelectOptionListHtml("x<?php echo $khs_list->RowIndex ?>_StatusStudentID") ?>
</select>
<input type="hidden" name="s_x<?php echo $khs_list->RowIndex ?>_StatusStudentID" id="s_x<?php echo $khs_list->RowIndex ?>_StatusStudentID" value="<?php echo $khs->StatusStudentID->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="khs" data-field="x_StatusStudentID" name="o<?php echo $khs_list->RowIndex ?>_StatusStudentID" id="o<?php echo $khs_list->RowIndex ?>_StatusStudentID" value="<?php echo ew_HtmlEncode($khs->StatusStudentID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($khs->NA->Visible) { // NA ?>
		<td data-name="NA">
<span id="el$rowindex$_khs_NA" class="form-group khs_NA">
<div id="tp_x<?php echo $khs_list->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_NA" data-value-separator="<?php echo $khs->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $khs_list->RowIndex ?>_NA" id="x<?php echo $khs_list->RowIndex ?>_NA" value="{value}"<?php echo $khs->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $khs_list->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->NA->RadioButtonListHtml(FALSE, "x{$khs_list->RowIndex}_NA") ?>
</div></div>
</span>
<input type="hidden" data-table="khs" data-field="x_NA" name="o<?php echo $khs_list->RowIndex ?>_NA" id="o<?php echo $khs_list->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($khs->NA->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$khs_list->ListOptions->Render("body", "right", $khs_list->RowCnt);
?>
<script type="text/javascript">
fkhslist.UpdateOpts(<?php echo $khs_list->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($khs->CurrentAction == "gridadd") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $khs_list->FormKeyCountName ?>" id="<?php echo $khs_list->FormKeyCountName ?>" value="<?php echo $khs_list->KeyCount ?>">
<?php echo $khs_list->MultiSelectKey ?>
<?php } ?>
<?php if ($khs->CurrentAction == "gridedit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $khs_list->FormKeyCountName ?>" id="<?php echo $khs_list->FormKeyCountName ?>" value="<?php echo $khs_list->KeyCount ?>">
<?php echo $khs_list->MultiSelectKey ?>
<?php } ?>
<?php if ($khs->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($khs_list->Recordset)
	$khs_list->Recordset->Close();
?>
<?php if ($khs->Export == "") { ?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($khs->CurrentAction <> "gridadd" && $khs->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($khs_list->Pager)) $khs_list->Pager = new cPrevNextPager($khs_list->StartRec, $khs_list->DisplayRecs, $khs_list->TotalRecs) ?>
<?php if ($khs_list->Pager->RecordCount > 0 && $khs_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($khs_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $khs_list->PageUrl() ?>start=<?php echo $khs_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($khs_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $khs_list->PageUrl() ?>start=<?php echo $khs_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $khs_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($khs_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $khs_list->PageUrl() ?>start=<?php echo $khs_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($khs_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $khs_list->PageUrl() ?>start=<?php echo $khs_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $khs_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $khs_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $khs_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $khs_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($khs_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($khs_list->TotalRecs == 0 && $khs->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($khs_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($khs->Export == "") { ?>
<script type="text/javascript">
fkhslistsrch.FilterList = <?php echo $khs_list->GetFilterList() ?>;
fkhslistsrch.Init();
fkhslist.Init();
</script>
<?php } ?>
<?php
$khs_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($khs->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

$("#naik_kelas").click(function(evt) {
	evt.preventDefault();
	if (confirm("Apakah Anda ingin melakukan proses naik kelas?")) {
		$.ajax({
			url: 'ajax/naik_kelas.php',
			type: 'post'
		}).done(function (data) {
			alert("Proses naik kelas telah selesai");
			window.location.reload(false);
		});
	}
});
</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$khs_list->Page_Terminate();
?>
