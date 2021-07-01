<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "teacherinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$teacher_list = NULL; // Initialize page object first

class cteacher_list extends cteacher {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'teacher';

	// Page object name
	var $PageObjName = 'teacher_list';

	// Grid form hidden field names
	var $FormName = 'fteacherlist';
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

		// Table object (teacher)
		if (!isset($GLOBALS["teacher"]) || get_class($GLOBALS["teacher"]) == "cteacher") {
			$GLOBALS["teacher"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["teacher"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "teacheradd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "teacherdelete.php";
		$this->MultiUpdateUrl = "teacherupdate.php";

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'teacher', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fteacherlistsrch";

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
		$this->TeacherID->SetVisibility();
		$this->NIPPNS->SetVisibility();
		$this->Nama->SetVisibility();
		$this->AliasCode->SetVisibility();
		$this->KTP->SetVisibility();
		$this->KelaminID->SetVisibility();
		$this->Telephone->SetVisibility();
		$this->_Email->SetVisibility();
		$this->IkatanID->SetVisibility();
		$this->GolonganID->SetVisibility();
		$this->StatusKerjaID->SetVisibility();
		$this->Homebase->SetVisibility();
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
		global $EW_EXPORT, $teacher;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($teacher);
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

				// Switch to inline edit mode
				if ($this->CurrentAction == "edit")
					$this->InlineEditMode();
			} else {
				if (@$_POST["a_list"] <> "") {
					$this->CurrentAction = $_POST["a_list"]; // Get action

					// Inline Update
					if (($this->CurrentAction == "update" || $this->CurrentAction == "overwrite") && @$_SESSION[EW_SESSION_INLINE_MODE] == "edit")
						$this->InlineUpdate();
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
		$this->setKey("TeacherID", ""); // Clear inline edit key
		$this->LastAction = $this->CurrentAction; // Save last action
		$this->CurrentAction = ""; // Clear action
		$_SESSION[EW_SESSION_INLINE_MODE] = ""; // Clear inline mode
	}

	// Switch to Inline Edit mode
	function InlineEditMode() {
		global $Security, $Language;
		if (!$Security->CanEdit())
			$this->Page_Terminate("login.php"); // Go to login page
		$bInlineEdit = TRUE;
		if (@$_GET["TeacherID"] <> "") {
			$this->TeacherID->setQueryStringValue($_GET["TeacherID"]);
		} else {
			$bInlineEdit = FALSE;
		}
		if ($bInlineEdit) {
			if ($this->LoadRow()) {
				$this->setKey("TeacherID", $this->TeacherID->CurrentValue); // Set up inline edit key
				$_SESSION[EW_SESSION_INLINE_MODE] = "edit"; // Enable inline edit
			}
		}
	}

	// Perform update to Inline Edit record
	function InlineUpdate() {
		global $Language, $objForm, $gsFormError;
		$objForm->Index = 1; 
		$this->LoadFormValues(); // Get form values

		// Validate form
		$bInlineUpdate = TRUE;
		if (!$this->ValidateForm()) {	
			$bInlineUpdate = FALSE; // Form error, reset action
			$this->setFailureMessage($gsFormError);
		} else {
			$bInlineUpdate = FALSE;
			$rowkey = strval($objForm->GetValue($this->FormKeyName));
			if ($this->SetupKeyValues($rowkey)) { // Set up key values
				if ($this->CheckInlineEditKey()) { // Check key
					$this->SendEmail = TRUE; // Send email on update success
					$bInlineUpdate = $this->EditRow(); // Update record
				} else {
					$bInlineUpdate = FALSE;
				}
			}
		}
		if ($bInlineUpdate) { // Update success
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Set up success message
			$this->ClearInlineMode(); // Clear inline edit mode
		} else {
			if ($this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("UpdateFailed")); // Set update failed message
			$this->EventCancelled = TRUE; // Cancel event
			$this->CurrentAction = "edit"; // Stay in edit mode
		}
	}

	// Check Inline Edit key
	function CheckInlineEditKey() {

		//CheckInlineEditKey = True
		if (strval($this->getKey("TeacherID")) <> strval($this->TeacherID->CurrentValue))
			return FALSE;
		return TRUE;
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
			$this->TeacherID->setFormValue($arrKeyFlds[0]);
		}
		return TRUE;
	}

	// Get list of filters
	function GetFilterList() {
		global $UserProfile;

		// Load server side filters
		if (EW_SEARCH_FILTER_OPTION == "Server") {
			$sSavedFilterList = isset($UserProfile) ? $UserProfile->GetSearchFilters(CurrentUserName(), "fteacherlistsrch") : "";
		} else {
			$sSavedFilterList = "";
		}

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->TeacherID->AdvancedSearch->ToJSON(), ","); // Field TeacherID
		$sFilterList = ew_Concat($sFilterList, $this->NIPPNS->AdvancedSearch->ToJSON(), ","); // Field NIPPNS
		$sFilterList = ew_Concat($sFilterList, $this->Nama->AdvancedSearch->ToJSON(), ","); // Field Nama
		$sFilterList = ew_Concat($sFilterList, $this->Gelar->AdvancedSearch->ToJSON(), ","); // Field Gelar
		$sFilterList = ew_Concat($sFilterList, $this->LevelID->AdvancedSearch->ToJSON(), ","); // Field LevelID
		$sFilterList = ew_Concat($sFilterList, $this->KTP->AdvancedSearch->ToJSON(), ","); // Field KTP
		$sFilterList = ew_Concat($sFilterList, $this->TempatLahir->AdvancedSearch->ToJSON(), ","); // Field TempatLahir
		$sFilterList = ew_Concat($sFilterList, $this->TanggalLahir->AdvancedSearch->ToJSON(), ","); // Field TanggalLahir
		$sFilterList = ew_Concat($sFilterList, $this->AgamaID->AdvancedSearch->ToJSON(), ","); // Field AgamaID
		$sFilterList = ew_Concat($sFilterList, $this->KelaminID->AdvancedSearch->ToJSON(), ","); // Field KelaminID
		$sFilterList = ew_Concat($sFilterList, $this->Telephone->AdvancedSearch->ToJSON(), ","); // Field Telephone
		$sFilterList = ew_Concat($sFilterList, $this->_Email->AdvancedSearch->ToJSON(), ","); // Field Email
		$sFilterList = ew_Concat($sFilterList, $this->Alamat->AdvancedSearch->ToJSON(), ","); // Field Alamat
		$sFilterList = ew_Concat($sFilterList, $this->KodePos->AdvancedSearch->ToJSON(), ","); // Field KodePos
		$sFilterList = ew_Concat($sFilterList, $this->ProvinsiID->AdvancedSearch->ToJSON(), ","); // Field ProvinsiID
		$sFilterList = ew_Concat($sFilterList, $this->KabupatenKotaID->AdvancedSearch->ToJSON(), ","); // Field KabupatenKotaID
		$sFilterList = ew_Concat($sFilterList, $this->KecamatanID->AdvancedSearch->ToJSON(), ","); // Field KecamatanID
		$sFilterList = ew_Concat($sFilterList, $this->DesaID->AdvancedSearch->ToJSON(), ","); // Field DesaID
		$sFilterList = ew_Concat($sFilterList, $this->InstitusiInduk->AdvancedSearch->ToJSON(), ","); // Field InstitusiInduk
		$sFilterList = ew_Concat($sFilterList, $this->IkatanID->AdvancedSearch->ToJSON(), ","); // Field IkatanID
		$sFilterList = ew_Concat($sFilterList, $this->GolonganID->AdvancedSearch->ToJSON(), ","); // Field GolonganID
		$sFilterList = ew_Concat($sFilterList, $this->StatusKerjaID->AdvancedSearch->ToJSON(), ","); // Field StatusKerjaID
		$sFilterList = ew_Concat($sFilterList, $this->TglBekerja->AdvancedSearch->ToJSON(), ","); // Field TglBekerja
		$sFilterList = ew_Concat($sFilterList, $this->Homebase->AdvancedSearch->ToJSON(), ","); // Field Homebase
		$sFilterList = ew_Concat($sFilterList, $this->ProdiID->AdvancedSearch->ToJSON(), ","); // Field ProdiID
		$sFilterList = ew_Concat($sFilterList, $this->Keilmuan->AdvancedSearch->ToJSON(), ","); // Field Keilmuan
		$sFilterList = ew_Concat($sFilterList, $this->LulusanPT->AdvancedSearch->ToJSON(), ","); // Field LulusanPT
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
			$UserProfile->SetSearchFilters(CurrentUserName(), "fteacherlistsrch", $filters);

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

		// Field TeacherID
		$this->TeacherID->AdvancedSearch->SearchValue = @$filter["x_TeacherID"];
		$this->TeacherID->AdvancedSearch->SearchOperator = @$filter["z_TeacherID"];
		$this->TeacherID->AdvancedSearch->SearchCondition = @$filter["v_TeacherID"];
		$this->TeacherID->AdvancedSearch->SearchValue2 = @$filter["y_TeacherID"];
		$this->TeacherID->AdvancedSearch->SearchOperator2 = @$filter["w_TeacherID"];
		$this->TeacherID->AdvancedSearch->Save();

		// Field NIPPNS
		$this->NIPPNS->AdvancedSearch->SearchValue = @$filter["x_NIPPNS"];
		$this->NIPPNS->AdvancedSearch->SearchOperator = @$filter["z_NIPPNS"];
		$this->NIPPNS->AdvancedSearch->SearchCondition = @$filter["v_NIPPNS"];
		$this->NIPPNS->AdvancedSearch->SearchValue2 = @$filter["y_NIPPNS"];
		$this->NIPPNS->AdvancedSearch->SearchOperator2 = @$filter["w_NIPPNS"];
		$this->NIPPNS->AdvancedSearch->Save();

		// Field Nama
		$this->Nama->AdvancedSearch->SearchValue = @$filter["x_Nama"];
		$this->Nama->AdvancedSearch->SearchOperator = @$filter["z_Nama"];
		$this->Nama->AdvancedSearch->SearchCondition = @$filter["v_Nama"];
		$this->Nama->AdvancedSearch->SearchValue2 = @$filter["y_Nama"];
		$this->Nama->AdvancedSearch->SearchOperator2 = @$filter["w_Nama"];
		$this->Nama->AdvancedSearch->Save();

		// Field Gelar
		$this->Gelar->AdvancedSearch->SearchValue = @$filter["x_Gelar"];
		$this->Gelar->AdvancedSearch->SearchOperator = @$filter["z_Gelar"];
		$this->Gelar->AdvancedSearch->SearchCondition = @$filter["v_Gelar"];
		$this->Gelar->AdvancedSearch->SearchValue2 = @$filter["y_Gelar"];
		$this->Gelar->AdvancedSearch->SearchOperator2 = @$filter["w_Gelar"];
		$this->Gelar->AdvancedSearch->Save();

		// Field LevelID
		$this->LevelID->AdvancedSearch->SearchValue = @$filter["x_LevelID"];
		$this->LevelID->AdvancedSearch->SearchOperator = @$filter["z_LevelID"];
		$this->LevelID->AdvancedSearch->SearchCondition = @$filter["v_LevelID"];
		$this->LevelID->AdvancedSearch->SearchValue2 = @$filter["y_LevelID"];
		$this->LevelID->AdvancedSearch->SearchOperator2 = @$filter["w_LevelID"];
		$this->LevelID->AdvancedSearch->Save();

		// Field KTP
		$this->KTP->AdvancedSearch->SearchValue = @$filter["x_KTP"];
		$this->KTP->AdvancedSearch->SearchOperator = @$filter["z_KTP"];
		$this->KTP->AdvancedSearch->SearchCondition = @$filter["v_KTP"];
		$this->KTP->AdvancedSearch->SearchValue2 = @$filter["y_KTP"];
		$this->KTP->AdvancedSearch->SearchOperator2 = @$filter["w_KTP"];
		$this->KTP->AdvancedSearch->Save();

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

		// Field KelaminID
		$this->KelaminID->AdvancedSearch->SearchValue = @$filter["x_KelaminID"];
		$this->KelaminID->AdvancedSearch->SearchOperator = @$filter["z_KelaminID"];
		$this->KelaminID->AdvancedSearch->SearchCondition = @$filter["v_KelaminID"];
		$this->KelaminID->AdvancedSearch->SearchValue2 = @$filter["y_KelaminID"];
		$this->KelaminID->AdvancedSearch->SearchOperator2 = @$filter["w_KelaminID"];
		$this->KelaminID->AdvancedSearch->Save();

		// Field Telephone
		$this->Telephone->AdvancedSearch->SearchValue = @$filter["x_Telephone"];
		$this->Telephone->AdvancedSearch->SearchOperator = @$filter["z_Telephone"];
		$this->Telephone->AdvancedSearch->SearchCondition = @$filter["v_Telephone"];
		$this->Telephone->AdvancedSearch->SearchValue2 = @$filter["y_Telephone"];
		$this->Telephone->AdvancedSearch->SearchOperator2 = @$filter["w_Telephone"];
		$this->Telephone->AdvancedSearch->Save();

		// Field Email
		$this->_Email->AdvancedSearch->SearchValue = @$filter["x__Email"];
		$this->_Email->AdvancedSearch->SearchOperator = @$filter["z__Email"];
		$this->_Email->AdvancedSearch->SearchCondition = @$filter["v__Email"];
		$this->_Email->AdvancedSearch->SearchValue2 = @$filter["y__Email"];
		$this->_Email->AdvancedSearch->SearchOperator2 = @$filter["w__Email"];
		$this->_Email->AdvancedSearch->Save();

		// Field Alamat
		$this->Alamat->AdvancedSearch->SearchValue = @$filter["x_Alamat"];
		$this->Alamat->AdvancedSearch->SearchOperator = @$filter["z_Alamat"];
		$this->Alamat->AdvancedSearch->SearchCondition = @$filter["v_Alamat"];
		$this->Alamat->AdvancedSearch->SearchValue2 = @$filter["y_Alamat"];
		$this->Alamat->AdvancedSearch->SearchOperator2 = @$filter["w_Alamat"];
		$this->Alamat->AdvancedSearch->Save();

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

		// Field InstitusiInduk
		$this->InstitusiInduk->AdvancedSearch->SearchValue = @$filter["x_InstitusiInduk"];
		$this->InstitusiInduk->AdvancedSearch->SearchOperator = @$filter["z_InstitusiInduk"];
		$this->InstitusiInduk->AdvancedSearch->SearchCondition = @$filter["v_InstitusiInduk"];
		$this->InstitusiInduk->AdvancedSearch->SearchValue2 = @$filter["y_InstitusiInduk"];
		$this->InstitusiInduk->AdvancedSearch->SearchOperator2 = @$filter["w_InstitusiInduk"];
		$this->InstitusiInduk->AdvancedSearch->Save();

		// Field IkatanID
		$this->IkatanID->AdvancedSearch->SearchValue = @$filter["x_IkatanID"];
		$this->IkatanID->AdvancedSearch->SearchOperator = @$filter["z_IkatanID"];
		$this->IkatanID->AdvancedSearch->SearchCondition = @$filter["v_IkatanID"];
		$this->IkatanID->AdvancedSearch->SearchValue2 = @$filter["y_IkatanID"];
		$this->IkatanID->AdvancedSearch->SearchOperator2 = @$filter["w_IkatanID"];
		$this->IkatanID->AdvancedSearch->Save();

		// Field GolonganID
		$this->GolonganID->AdvancedSearch->SearchValue = @$filter["x_GolonganID"];
		$this->GolonganID->AdvancedSearch->SearchOperator = @$filter["z_GolonganID"];
		$this->GolonganID->AdvancedSearch->SearchCondition = @$filter["v_GolonganID"];
		$this->GolonganID->AdvancedSearch->SearchValue2 = @$filter["y_GolonganID"];
		$this->GolonganID->AdvancedSearch->SearchOperator2 = @$filter["w_GolonganID"];
		$this->GolonganID->AdvancedSearch->Save();

		// Field StatusKerjaID
		$this->StatusKerjaID->AdvancedSearch->SearchValue = @$filter["x_StatusKerjaID"];
		$this->StatusKerjaID->AdvancedSearch->SearchOperator = @$filter["z_StatusKerjaID"];
		$this->StatusKerjaID->AdvancedSearch->SearchCondition = @$filter["v_StatusKerjaID"];
		$this->StatusKerjaID->AdvancedSearch->SearchValue2 = @$filter["y_StatusKerjaID"];
		$this->StatusKerjaID->AdvancedSearch->SearchOperator2 = @$filter["w_StatusKerjaID"];
		$this->StatusKerjaID->AdvancedSearch->Save();

		// Field TglBekerja
		$this->TglBekerja->AdvancedSearch->SearchValue = @$filter["x_TglBekerja"];
		$this->TglBekerja->AdvancedSearch->SearchOperator = @$filter["z_TglBekerja"];
		$this->TglBekerja->AdvancedSearch->SearchCondition = @$filter["v_TglBekerja"];
		$this->TglBekerja->AdvancedSearch->SearchValue2 = @$filter["y_TglBekerja"];
		$this->TglBekerja->AdvancedSearch->SearchOperator2 = @$filter["w_TglBekerja"];
		$this->TglBekerja->AdvancedSearch->Save();

		// Field Homebase
		$this->Homebase->AdvancedSearch->SearchValue = @$filter["x_Homebase"];
		$this->Homebase->AdvancedSearch->SearchOperator = @$filter["z_Homebase"];
		$this->Homebase->AdvancedSearch->SearchCondition = @$filter["v_Homebase"];
		$this->Homebase->AdvancedSearch->SearchValue2 = @$filter["y_Homebase"];
		$this->Homebase->AdvancedSearch->SearchOperator2 = @$filter["w_Homebase"];
		$this->Homebase->AdvancedSearch->Save();

		// Field ProdiID
		$this->ProdiID->AdvancedSearch->SearchValue = @$filter["x_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchOperator = @$filter["z_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchCondition = @$filter["v_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchValue2 = @$filter["y_ProdiID"];
		$this->ProdiID->AdvancedSearch->SearchOperator2 = @$filter["w_ProdiID"];
		$this->ProdiID->AdvancedSearch->Save();

		// Field Keilmuan
		$this->Keilmuan->AdvancedSearch->SearchValue = @$filter["x_Keilmuan"];
		$this->Keilmuan->AdvancedSearch->SearchOperator = @$filter["z_Keilmuan"];
		$this->Keilmuan->AdvancedSearch->SearchCondition = @$filter["v_Keilmuan"];
		$this->Keilmuan->AdvancedSearch->SearchValue2 = @$filter["y_Keilmuan"];
		$this->Keilmuan->AdvancedSearch->SearchOperator2 = @$filter["w_Keilmuan"];
		$this->Keilmuan->AdvancedSearch->Save();

		// Field LulusanPT
		$this->LulusanPT->AdvancedSearch->SearchValue = @$filter["x_LulusanPT"];
		$this->LulusanPT->AdvancedSearch->SearchOperator = @$filter["z_LulusanPT"];
		$this->LulusanPT->AdvancedSearch->SearchCondition = @$filter["v_LulusanPT"];
		$this->LulusanPT->AdvancedSearch->SearchValue2 = @$filter["y_LulusanPT"];
		$this->LulusanPT->AdvancedSearch->SearchOperator2 = @$filter["w_LulusanPT"];
		$this->LulusanPT->AdvancedSearch->Save();

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
		$this->BuildSearchSql($sWhere, $this->TeacherID, $Default, FALSE); // TeacherID
		$this->BuildSearchSql($sWhere, $this->NIPPNS, $Default, FALSE); // NIPPNS
		$this->BuildSearchSql($sWhere, $this->Nama, $Default, FALSE); // Nama
		$this->BuildSearchSql($sWhere, $this->Gelar, $Default, FALSE); // Gelar
		$this->BuildSearchSql($sWhere, $this->LevelID, $Default, FALSE); // LevelID
		$this->BuildSearchSql($sWhere, $this->KTP, $Default, FALSE); // KTP
		$this->BuildSearchSql($sWhere, $this->TempatLahir, $Default, FALSE); // TempatLahir
		$this->BuildSearchSql($sWhere, $this->TanggalLahir, $Default, FALSE); // TanggalLahir
		$this->BuildSearchSql($sWhere, $this->AgamaID, $Default, FALSE); // AgamaID
		$this->BuildSearchSql($sWhere, $this->KelaminID, $Default, FALSE); // KelaminID
		$this->BuildSearchSql($sWhere, $this->Telephone, $Default, FALSE); // Telephone
		$this->BuildSearchSql($sWhere, $this->_Email, $Default, FALSE); // Email
		$this->BuildSearchSql($sWhere, $this->Alamat, $Default, FALSE); // Alamat
		$this->BuildSearchSql($sWhere, $this->KodePos, $Default, FALSE); // KodePos
		$this->BuildSearchSql($sWhere, $this->ProvinsiID, $Default, FALSE); // ProvinsiID
		$this->BuildSearchSql($sWhere, $this->KabupatenKotaID, $Default, FALSE); // KabupatenKotaID
		$this->BuildSearchSql($sWhere, $this->KecamatanID, $Default, FALSE); // KecamatanID
		$this->BuildSearchSql($sWhere, $this->DesaID, $Default, FALSE); // DesaID
		$this->BuildSearchSql($sWhere, $this->InstitusiInduk, $Default, FALSE); // InstitusiInduk
		$this->BuildSearchSql($sWhere, $this->IkatanID, $Default, FALSE); // IkatanID
		$this->BuildSearchSql($sWhere, $this->GolonganID, $Default, FALSE); // GolonganID
		$this->BuildSearchSql($sWhere, $this->StatusKerjaID, $Default, FALSE); // StatusKerjaID
		$this->BuildSearchSql($sWhere, $this->TglBekerja, $Default, FALSE); // TglBekerja
		$this->BuildSearchSql($sWhere, $this->Homebase, $Default, FALSE); // Homebase
		$this->BuildSearchSql($sWhere, $this->ProdiID, $Default, TRUE); // ProdiID
		$this->BuildSearchSql($sWhere, $this->Keilmuan, $Default, FALSE); // Keilmuan
		$this->BuildSearchSql($sWhere, $this->LulusanPT, $Default, FALSE); // LulusanPT
		$this->BuildSearchSql($sWhere, $this->NA, $Default, FALSE); // NA

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->TeacherID->AdvancedSearch->Save(); // TeacherID
			$this->NIPPNS->AdvancedSearch->Save(); // NIPPNS
			$this->Nama->AdvancedSearch->Save(); // Nama
			$this->Gelar->AdvancedSearch->Save(); // Gelar
			$this->LevelID->AdvancedSearch->Save(); // LevelID
			$this->KTP->AdvancedSearch->Save(); // KTP
			$this->TempatLahir->AdvancedSearch->Save(); // TempatLahir
			$this->TanggalLahir->AdvancedSearch->Save(); // TanggalLahir
			$this->AgamaID->AdvancedSearch->Save(); // AgamaID
			$this->KelaminID->AdvancedSearch->Save(); // KelaminID
			$this->Telephone->AdvancedSearch->Save(); // Telephone
			$this->_Email->AdvancedSearch->Save(); // Email
			$this->Alamat->AdvancedSearch->Save(); // Alamat
			$this->KodePos->AdvancedSearch->Save(); // KodePos
			$this->ProvinsiID->AdvancedSearch->Save(); // ProvinsiID
			$this->KabupatenKotaID->AdvancedSearch->Save(); // KabupatenKotaID
			$this->KecamatanID->AdvancedSearch->Save(); // KecamatanID
			$this->DesaID->AdvancedSearch->Save(); // DesaID
			$this->InstitusiInduk->AdvancedSearch->Save(); // InstitusiInduk
			$this->IkatanID->AdvancedSearch->Save(); // IkatanID
			$this->GolonganID->AdvancedSearch->Save(); // GolonganID
			$this->StatusKerjaID->AdvancedSearch->Save(); // StatusKerjaID
			$this->TglBekerja->AdvancedSearch->Save(); // TglBekerja
			$this->Homebase->AdvancedSearch->Save(); // Homebase
			$this->ProdiID->AdvancedSearch->Save(); // ProdiID
			$this->Keilmuan->AdvancedSearch->Save(); // Keilmuan
			$this->LulusanPT->AdvancedSearch->Save(); // LulusanPT
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
		$this->BuildBasicSearchSQL($sWhere, $this->TeacherID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NIPPNS, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Nama, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Gelar, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Password, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->AliasCode, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KTP, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->TempatLahir, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Telephone, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Handphone, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->_Email, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Alamat, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KodePos, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->ProvinsiID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KabupatenKotaID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->KecamatanID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->DesaID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->InstitusiInduk, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->IkatanID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->GolonganID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->StatusKerjaID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Homebase, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->ProdiID, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Keilmuan, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->LulusanPT, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NamaBank, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NamaAkun, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NomerAkun, $arKeywords, $type);
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
		if ($this->TeacherID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NIPPNS->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Nama->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Gelar->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->LevelID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KTP->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TempatLahir->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TanggalLahir->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->AgamaID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KelaminID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Telephone->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->_Email->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Alamat->AdvancedSearch->IssetSession())
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
		if ($this->InstitusiInduk->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->IkatanID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->GolonganID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->StatusKerjaID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TglBekerja->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Homebase->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->ProdiID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Keilmuan->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->LulusanPT->AdvancedSearch->IssetSession())
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
		$this->TeacherID->AdvancedSearch->UnsetSession();
		$this->NIPPNS->AdvancedSearch->UnsetSession();
		$this->Nama->AdvancedSearch->UnsetSession();
		$this->Gelar->AdvancedSearch->UnsetSession();
		$this->LevelID->AdvancedSearch->UnsetSession();
		$this->KTP->AdvancedSearch->UnsetSession();
		$this->TempatLahir->AdvancedSearch->UnsetSession();
		$this->TanggalLahir->AdvancedSearch->UnsetSession();
		$this->AgamaID->AdvancedSearch->UnsetSession();
		$this->KelaminID->AdvancedSearch->UnsetSession();
		$this->Telephone->AdvancedSearch->UnsetSession();
		$this->_Email->AdvancedSearch->UnsetSession();
		$this->Alamat->AdvancedSearch->UnsetSession();
		$this->KodePos->AdvancedSearch->UnsetSession();
		$this->ProvinsiID->AdvancedSearch->UnsetSession();
		$this->KabupatenKotaID->AdvancedSearch->UnsetSession();
		$this->KecamatanID->AdvancedSearch->UnsetSession();
		$this->DesaID->AdvancedSearch->UnsetSession();
		$this->InstitusiInduk->AdvancedSearch->UnsetSession();
		$this->IkatanID->AdvancedSearch->UnsetSession();
		$this->GolonganID->AdvancedSearch->UnsetSession();
		$this->StatusKerjaID->AdvancedSearch->UnsetSession();
		$this->TglBekerja->AdvancedSearch->UnsetSession();
		$this->Homebase->AdvancedSearch->UnsetSession();
		$this->ProdiID->AdvancedSearch->UnsetSession();
		$this->Keilmuan->AdvancedSearch->UnsetSession();
		$this->LulusanPT->AdvancedSearch->UnsetSession();
		$this->NA->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->TeacherID->AdvancedSearch->Load();
		$this->NIPPNS->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->Gelar->AdvancedSearch->Load();
		$this->LevelID->AdvancedSearch->Load();
		$this->KTP->AdvancedSearch->Load();
		$this->TempatLahir->AdvancedSearch->Load();
		$this->TanggalLahir->AdvancedSearch->Load();
		$this->AgamaID->AdvancedSearch->Load();
		$this->KelaminID->AdvancedSearch->Load();
		$this->Telephone->AdvancedSearch->Load();
		$this->_Email->AdvancedSearch->Load();
		$this->Alamat->AdvancedSearch->Load();
		$this->KodePos->AdvancedSearch->Load();
		$this->ProvinsiID->AdvancedSearch->Load();
		$this->KabupatenKotaID->AdvancedSearch->Load();
		$this->KecamatanID->AdvancedSearch->Load();
		$this->DesaID->AdvancedSearch->Load();
		$this->InstitusiInduk->AdvancedSearch->Load();
		$this->IkatanID->AdvancedSearch->Load();
		$this->GolonganID->AdvancedSearch->Load();
		$this->StatusKerjaID->AdvancedSearch->Load();
		$this->TglBekerja->AdvancedSearch->Load();
		$this->Homebase->AdvancedSearch->Load();
		$this->ProdiID->AdvancedSearch->Load();
		$this->Keilmuan->AdvancedSearch->Load();
		$this->LulusanPT->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->TeacherID); // TeacherID
			$this->UpdateSort($this->NIPPNS); // NIPPNS
			$this->UpdateSort($this->Nama); // Nama
			$this->UpdateSort($this->AliasCode); // AliasCode
			$this->UpdateSort($this->KTP); // KTP
			$this->UpdateSort($this->KelaminID); // KelaminID
			$this->UpdateSort($this->Telephone); // Telephone
			$this->UpdateSort($this->_Email); // Email
			$this->UpdateSort($this->IkatanID); // IkatanID
			$this->UpdateSort($this->GolonganID); // GolonganID
			$this->UpdateSort($this->StatusKerjaID); // StatusKerjaID
			$this->UpdateSort($this->Homebase); // Homebase
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
				$this->TeacherID->setSort("");
				$this->NIPPNS->setSort("");
				$this->Nama->setSort("");
				$this->AliasCode->setSort("");
				$this->KTP->setSort("");
				$this->KelaminID->setSort("");
				$this->Telephone->setSort("");
				$this->_Email->setSort("");
				$this->IkatanID->setSort("");
				$this->GolonganID->setSort("");
				$this->StatusKerjaID->setSort("");
				$this->Homebase->setSort("");
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

		// "sequence"
		$oListOpt = &$this->ListOptions->Items["sequence"];
		$oListOpt->Body = ew_FormatSeqNo($this->RecCnt);

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($this->CurrentAction == "edit" && $this->RowType == EW_ROWTYPE_EDIT) { // Inline-Edit
			$this->ListOptions->CustomItem = "edit"; // Show edit column only
			$cancelurl = $this->AddMasterUrl($this->PageUrl() . "a=cancel");
				$oListOpt->Body = "<div" . (($oListOpt->OnLeft) ? " style=\"text-align: right\"" : "") . ">" .
					"<a class=\"ewGridLink ewInlineUpdate\" title=\"" . ew_HtmlTitle($Language->Phrase("UpdateLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("UpdateLink")) . "\" href=\"\" onclick=\"return ewForms(this).Submit('" . ew_GetHashUrl($this->PageName(), $this->PageObjName . "_row_" . $this->RowCnt) . "');\">" . $Language->Phrase("UpdateLink") . "</a>&nbsp;" .
					"<a class=\"ewGridLink ewInlineCancel\" title=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->Phrase("CancelLink") . "</a>" .
					"<input type=\"hidden\" name=\"a_list\" id=\"a_list\" value=\"update\"></div>";
			$oListOpt->Body .= "<input type=\"hidden\" name=\"k" . $this->RowIndex . "_key\" id=\"k" . $this->RowIndex . "_key\" value=\"" . ew_HtmlEncode($this->TeacherID->CurrentValue) . "\">";
			return;
		}

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
			$oListOpt->Body .= "<a class=\"ewRowLink ewInlineEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("InlineEditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("InlineEditLink")) . "\" href=\"" . ew_HtmlEncode(ew_GetHashUrl($this->InlineEditUrl, $this->PageObjName . "_row_" . $this->RowCnt)) . "\">" . $Language->Phrase("InlineEditLink") . "</a>";
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
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->TeacherID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event);'>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fteacherlistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fteacherlistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
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
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fteacherlist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fteacherlistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		if (ew_IsMobile())
			$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"teachersrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		else
			$item->Body = "<button type=\"button\" class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-table=\"teacher\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" onclick=\"ew_ModalDialogShow({lnk:this,url:'teachersrch.php',caption:'" . $Language->Phrase("Search") . "'});\">" . $Language->Phrase("AdvancedSearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Search highlight button
		$item = &$this->SearchOptions->Add("searchhighlight");
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewHighlight active\" title=\"" . $Language->Phrase("Highlight") . "\" data-caption=\"" . $Language->Phrase("Highlight") . "\" data-toggle=\"button\" data-form=\"fteacherlistsrch\" data-name=\"" . $this->HighlightName() . "\">" . $Language->Phrase("HighlightBtn") . "</button>";
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
		$this->TeacherID->CurrentValue = NULL;
		$this->TeacherID->OldValue = $this->TeacherID->CurrentValue;
		$this->NIPPNS->CurrentValue = NULL;
		$this->NIPPNS->OldValue = $this->NIPPNS->CurrentValue;
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->AliasCode->CurrentValue = NULL;
		$this->AliasCode->OldValue = $this->AliasCode->CurrentValue;
		$this->KTP->CurrentValue = NULL;
		$this->KTP->OldValue = $this->KTP->CurrentValue;
		$this->KelaminID->CurrentValue = NULL;
		$this->KelaminID->OldValue = $this->KelaminID->CurrentValue;
		$this->Telephone->CurrentValue = NULL;
		$this->Telephone->OldValue = $this->Telephone->CurrentValue;
		$this->_Email->CurrentValue = NULL;
		$this->_Email->OldValue = $this->_Email->CurrentValue;
		$this->IkatanID->CurrentValue = NULL;
		$this->IkatanID->OldValue = $this->IkatanID->CurrentValue;
		$this->GolonganID->CurrentValue = NULL;
		$this->GolonganID->OldValue = $this->GolonganID->CurrentValue;
		$this->StatusKerjaID->CurrentValue = NULL;
		$this->StatusKerjaID->OldValue = $this->StatusKerjaID->CurrentValue;
		$this->Homebase->CurrentValue = NULL;
		$this->Homebase->OldValue = $this->Homebase->CurrentValue;
		$this->NA->CurrentValue = "N";
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
		// TeacherID

		$this->TeacherID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TeacherID"]);
		if ($this->TeacherID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TeacherID->AdvancedSearch->SearchOperator = @$_GET["z_TeacherID"];

		// NIPPNS
		$this->NIPPNS->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NIPPNS"]);
		if ($this->NIPPNS->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NIPPNS->AdvancedSearch->SearchOperator = @$_GET["z_NIPPNS"];

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Nama"]);
		if ($this->Nama->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Nama->AdvancedSearch->SearchOperator = @$_GET["z_Nama"];

		// Gelar
		$this->Gelar->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Gelar"]);
		if ($this->Gelar->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Gelar->AdvancedSearch->SearchOperator = @$_GET["z_Gelar"];

		// LevelID
		$this->LevelID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_LevelID"]);
		if ($this->LevelID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->LevelID->AdvancedSearch->SearchOperator = @$_GET["z_LevelID"];

		// KTP
		$this->KTP->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KTP"]);
		if ($this->KTP->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KTP->AdvancedSearch->SearchOperator = @$_GET["z_KTP"];

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

		// KelaminID
		$this->KelaminID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KelaminID"]);
		if ($this->KelaminID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KelaminID->AdvancedSearch->SearchOperator = @$_GET["z_KelaminID"];

		// Telephone
		$this->Telephone->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Telephone"]);
		if ($this->Telephone->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Telephone->AdvancedSearch->SearchOperator = @$_GET["z_Telephone"];

		// Email
		$this->_Email->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x__Email"]);
		if ($this->_Email->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->_Email->AdvancedSearch->SearchOperator = @$_GET["z__Email"];

		// Alamat
		$this->Alamat->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Alamat"]);
		if ($this->Alamat->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Alamat->AdvancedSearch->SearchOperator = @$_GET["z_Alamat"];

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

		// InstitusiInduk
		$this->InstitusiInduk->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_InstitusiInduk"]);
		if ($this->InstitusiInduk->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->InstitusiInduk->AdvancedSearch->SearchOperator = @$_GET["z_InstitusiInduk"];

		// IkatanID
		$this->IkatanID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_IkatanID"]);
		if ($this->IkatanID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->IkatanID->AdvancedSearch->SearchOperator = @$_GET["z_IkatanID"];

		// GolonganID
		$this->GolonganID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_GolonganID"]);
		if ($this->GolonganID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->GolonganID->AdvancedSearch->SearchOperator = @$_GET["z_GolonganID"];

		// StatusKerjaID
		$this->StatusKerjaID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_StatusKerjaID"]);
		if ($this->StatusKerjaID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->StatusKerjaID->AdvancedSearch->SearchOperator = @$_GET["z_StatusKerjaID"];

		// TglBekerja
		$this->TglBekerja->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TglBekerja"]);
		if ($this->TglBekerja->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TglBekerja->AdvancedSearch->SearchOperator = @$_GET["z_TglBekerja"];

		// Homebase
		$this->Homebase->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Homebase"]);
		if ($this->Homebase->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Homebase->AdvancedSearch->SearchOperator = @$_GET["z_Homebase"];

		// ProdiID
		$this->ProdiID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_ProdiID"]);
		if ($this->ProdiID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->ProdiID->AdvancedSearch->SearchOperator = @$_GET["z_ProdiID"];
		if (is_array($this->ProdiID->AdvancedSearch->SearchValue)) $this->ProdiID->AdvancedSearch->SearchValue = implode(",", $this->ProdiID->AdvancedSearch->SearchValue);
		if (is_array($this->ProdiID->AdvancedSearch->SearchValue2)) $this->ProdiID->AdvancedSearch->SearchValue2 = implode(",", $this->ProdiID->AdvancedSearch->SearchValue2);

		// Keilmuan
		$this->Keilmuan->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Keilmuan"]);
		if ($this->Keilmuan->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Keilmuan->AdvancedSearch->SearchOperator = @$_GET["z_Keilmuan"];

		// LulusanPT
		$this->LulusanPT->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_LulusanPT"]);
		if ($this->LulusanPT->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->LulusanPT->AdvancedSearch->SearchOperator = @$_GET["z_LulusanPT"];

		// NA
		$this->NA->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NA"]);
		if ($this->NA->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NA->AdvancedSearch->SearchOperator = @$_GET["z_NA"];
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->TeacherID->FldIsDetailKey) {
			$this->TeacherID->setFormValue($objForm->GetValue("x_TeacherID"));
		}
		if (!$this->NIPPNS->FldIsDetailKey) {
			$this->NIPPNS->setFormValue($objForm->GetValue("x_NIPPNS"));
		}
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->AliasCode->FldIsDetailKey) {
			$this->AliasCode->setFormValue($objForm->GetValue("x_AliasCode"));
		}
		if (!$this->KTP->FldIsDetailKey) {
			$this->KTP->setFormValue($objForm->GetValue("x_KTP"));
		}
		if (!$this->KelaminID->FldIsDetailKey) {
			$this->KelaminID->setFormValue($objForm->GetValue("x_KelaminID"));
		}
		if (!$this->Telephone->FldIsDetailKey) {
			$this->Telephone->setFormValue($objForm->GetValue("x_Telephone"));
		}
		if (!$this->_Email->FldIsDetailKey) {
			$this->_Email->setFormValue($objForm->GetValue("x__Email"));
		}
		if (!$this->IkatanID->FldIsDetailKey) {
			$this->IkatanID->setFormValue($objForm->GetValue("x_IkatanID"));
		}
		if (!$this->GolonganID->FldIsDetailKey) {
			$this->GolonganID->setFormValue($objForm->GetValue("x_GolonganID"));
		}
		if (!$this->StatusKerjaID->FldIsDetailKey) {
			$this->StatusKerjaID->setFormValue($objForm->GetValue("x_StatusKerjaID"));
		}
		if (!$this->Homebase->FldIsDetailKey) {
			$this->Homebase->setFormValue($objForm->GetValue("x_Homebase"));
		}
		if (!$this->NA->FldIsDetailKey) {
			$this->NA->setFormValue($objForm->GetValue("x_NA"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->TeacherID->CurrentValue = $this->TeacherID->FormValue;
		$this->NIPPNS->CurrentValue = $this->NIPPNS->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->AliasCode->CurrentValue = $this->AliasCode->FormValue;
		$this->KTP->CurrentValue = $this->KTP->FormValue;
		$this->KelaminID->CurrentValue = $this->KelaminID->FormValue;
		$this->Telephone->CurrentValue = $this->Telephone->FormValue;
		$this->_Email->CurrentValue = $this->_Email->FormValue;
		$this->IkatanID->CurrentValue = $this->IkatanID->FormValue;
		$this->GolonganID->CurrentValue = $this->GolonganID->FormValue;
		$this->StatusKerjaID->CurrentValue = $this->StatusKerjaID->FormValue;
		$this->Homebase->CurrentValue = $this->Homebase->FormValue;
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
		$this->TeacherID->setDbValue($rs->fields('TeacherID'));
		$this->NIPPNS->setDbValue($rs->fields('NIPPNS'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->Gelar->setDbValue($rs->fields('Gelar'));
		$this->LevelID->setDbValue($rs->fields('LevelID'));
		$this->Password->setDbValue($rs->fields('Password'));
		$this->AliasCode->setDbValue($rs->fields('AliasCode'));
		$this->KTP->setDbValue($rs->fields('KTP'));
		$this->TempatLahir->setDbValue($rs->fields('TempatLahir'));
		$this->TanggalLahir->setDbValue($rs->fields('TanggalLahir'));
		$this->AgamaID->setDbValue($rs->fields('AgamaID'));
		$this->KelaminID->setDbValue($rs->fields('KelaminID'));
		$this->Telephone->setDbValue($rs->fields('Telephone'));
		$this->Handphone->setDbValue($rs->fields('Handphone'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Alamat->setDbValue($rs->fields('Alamat'));
		$this->KodePos->setDbValue($rs->fields('KodePos'));
		$this->ProvinsiID->setDbValue($rs->fields('ProvinsiID'));
		$this->KabupatenKotaID->setDbValue($rs->fields('KabupatenKotaID'));
		$this->KecamatanID->setDbValue($rs->fields('KecamatanID'));
		$this->DesaID->setDbValue($rs->fields('DesaID'));
		$this->InstitusiInduk->setDbValue($rs->fields('InstitusiInduk'));
		$this->IkatanID->setDbValue($rs->fields('IkatanID'));
		$this->GolonganID->setDbValue($rs->fields('GolonganID'));
		$this->StatusKerjaID->setDbValue($rs->fields('StatusKerjaID'));
		$this->TglBekerja->setDbValue($rs->fields('TglBekerja'));
		$this->Homebase->setDbValue($rs->fields('Homebase'));
		$this->ProdiID->setDbValue($rs->fields('ProdiID'));
		$this->Keilmuan->setDbValue($rs->fields('Keilmuan'));
		$this->LulusanPT->setDbValue($rs->fields('LulusanPT'));
		$this->NamaBank->setDbValue($rs->fields('NamaBank'));
		$this->NamaAkun->setDbValue($rs->fields('NamaAkun'));
		$this->NomerAkun->setDbValue($rs->fields('NomerAkun'));
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
		$this->TeacherID->DbValue = $row['TeacherID'];
		$this->NIPPNS->DbValue = $row['NIPPNS'];
		$this->Nama->DbValue = $row['Nama'];
		$this->Gelar->DbValue = $row['Gelar'];
		$this->LevelID->DbValue = $row['LevelID'];
		$this->Password->DbValue = $row['Password'];
		$this->AliasCode->DbValue = $row['AliasCode'];
		$this->KTP->DbValue = $row['KTP'];
		$this->TempatLahir->DbValue = $row['TempatLahir'];
		$this->TanggalLahir->DbValue = $row['TanggalLahir'];
		$this->AgamaID->DbValue = $row['AgamaID'];
		$this->KelaminID->DbValue = $row['KelaminID'];
		$this->Telephone->DbValue = $row['Telephone'];
		$this->Handphone->DbValue = $row['Handphone'];
		$this->_Email->DbValue = $row['Email'];
		$this->Alamat->DbValue = $row['Alamat'];
		$this->KodePos->DbValue = $row['KodePos'];
		$this->ProvinsiID->DbValue = $row['ProvinsiID'];
		$this->KabupatenKotaID->DbValue = $row['KabupatenKotaID'];
		$this->KecamatanID->DbValue = $row['KecamatanID'];
		$this->DesaID->DbValue = $row['DesaID'];
		$this->InstitusiInduk->DbValue = $row['InstitusiInduk'];
		$this->IkatanID->DbValue = $row['IkatanID'];
		$this->GolonganID->DbValue = $row['GolonganID'];
		$this->StatusKerjaID->DbValue = $row['StatusKerjaID'];
		$this->TglBekerja->DbValue = $row['TglBekerja'];
		$this->Homebase->DbValue = $row['Homebase'];
		$this->ProdiID->DbValue = $row['ProdiID'];
		$this->Keilmuan->DbValue = $row['Keilmuan'];
		$this->LulusanPT->DbValue = $row['LulusanPT'];
		$this->NamaBank->DbValue = $row['NamaBank'];
		$this->NamaAkun->DbValue = $row['NamaAkun'];
		$this->NomerAkun->DbValue = $row['NomerAkun'];
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
		if (strval($this->getKey("TeacherID")) <> "")
			$this->TeacherID->CurrentValue = $this->getKey("TeacherID"); // TeacherID
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
		// TeacherID

		$this->TeacherID->CellCssStyle = "white-space: nowrap;";

		// NIPPNS
		$this->NIPPNS->CellCssStyle = "white-space: nowrap;";

		// Nama
		$this->Nama->CellCssStyle = "white-space: nowrap;";

		// Gelar
		// LevelID
		// Password
		// AliasCode
		// KTP

		$this->KTP->CellCssStyle = "white-space: nowrap;";

		// TempatLahir
		// TanggalLahir
		// AgamaID
		// KelaminID
		// Telephone
		// Handphone
		// Email
		// Alamat
		// KodePos
		// ProvinsiID
		// KabupatenKotaID
		// KecamatanID
		// DesaID
		// InstitusiInduk
		// IkatanID
		// GolonganID
		// StatusKerjaID
		// TglBekerja
		// Homebase
		// ProdiID
		// Keilmuan
		// LulusanPT
		// NamaBank
		// NamaAkun
		// NomerAkun
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// TeacherID
		$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
		$this->TeacherID->CssStyle = "font-weight: bold;";
		$this->TeacherID->ViewCustomAttributes = "";

		// NIPPNS
		$this->NIPPNS->ViewValue = $this->NIPPNS->CurrentValue;
		$this->NIPPNS->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->CssStyle = "font-weight: bold;";
		$this->Nama->ViewCustomAttributes = "";

		// Gelar
		$this->Gelar->ViewValue = $this->Gelar->CurrentValue;
		$this->Gelar->ViewCustomAttributes = "";

		// LevelID
		$this->LevelID->ViewValue = $this->LevelID->CurrentValue;
		$this->LevelID->ViewCustomAttributes = "";

		// AliasCode
		$this->AliasCode->ViewValue = $this->AliasCode->CurrentValue;
		$this->AliasCode->ViewCustomAttributes = "";

		// KTP
		$this->KTP->ViewValue = $this->KTP->CurrentValue;
		$this->KTP->ViewCustomAttributes = "";

		// TempatLahir
		$this->TempatLahir->ViewValue = $this->TempatLahir->CurrentValue;
		if (strval($this->TempatLahir->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->TempatLahir->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->TempatLahir->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->TempatLahir, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->TempatLahir->ViewValue = $this->TempatLahir->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->TempatLahir->ViewValue = $this->TempatLahir->CurrentValue;
			}
		} else {
			$this->TempatLahir->ViewValue = NULL;
		}
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

		// KelaminID
		if (strval($this->KelaminID->CurrentValue) <> "") {
			$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->KelaminID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
		$sWhereWrk = "";
		$this->KelaminID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KelaminID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KelaminID->ViewValue = $this->KelaminID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KelaminID->ViewValue = $this->KelaminID->CurrentValue;
			}
		} else {
			$this->KelaminID->ViewValue = NULL;
		}
		$this->KelaminID->ViewCustomAttributes = "";

		// Telephone
		$this->Telephone->ViewValue = $this->Telephone->CurrentValue;
		$this->Telephone->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// Alamat
		$this->Alamat->ViewValue = $this->Alamat->CurrentValue;
		$this->Alamat->ViewCustomAttributes = "";

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

		// InstitusiInduk
		if (strval($this->InstitusiInduk->CurrentValue) <> "") {
			$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->InstitusiInduk->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
		$sWhereWrk = "";
		$this->InstitusiInduk->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->InstitusiInduk, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->InstitusiInduk->ViewValue = $this->InstitusiInduk->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->InstitusiInduk->ViewValue = $this->InstitusiInduk->CurrentValue;
			}
		} else {
			$this->InstitusiInduk->ViewValue = NULL;
		}
		$this->InstitusiInduk->ViewCustomAttributes = "";

		// IkatanID
		if (strval($this->IkatanID->CurrentValue) <> "") {
			$sFilterWrk = "`IkatanID`" . ew_SearchString("=", $this->IkatanID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `IkatanID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_ikatan`";
		$sWhereWrk = "";
		$this->IkatanID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->IkatanID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->IkatanID->ViewValue = $this->IkatanID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->IkatanID->ViewValue = $this->IkatanID->CurrentValue;
			}
		} else {
			$this->IkatanID->ViewValue = NULL;
		}
		$this->IkatanID->ViewCustomAttributes = "";

		// GolonganID
		if (strval($this->GolonganID->CurrentValue) <> "") {
			$sFilterWrk = "`GolonganID`" . ew_SearchString("=", $this->GolonganID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `GolonganID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_golongan`";
		$sWhereWrk = "";
		$this->GolonganID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->GolonganID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->GolonganID->ViewValue = $this->GolonganID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->GolonganID->ViewValue = $this->GolonganID->CurrentValue;
			}
		} else {
			$this->GolonganID->ViewValue = NULL;
		}
		$this->GolonganID->ViewCustomAttributes = "";

		// StatusKerjaID
		if (strval($this->StatusKerjaID->CurrentValue) <> "") {
			$sFilterWrk = "`StatusKerjaID`" . ew_SearchString("=", $this->StatusKerjaID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusKerjaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statuskerja`";
		$sWhereWrk = "";
		$this->StatusKerjaID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StatusKerjaID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StatusKerjaID->ViewValue = $this->StatusKerjaID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StatusKerjaID->ViewValue = $this->StatusKerjaID->CurrentValue;
			}
		} else {
			$this->StatusKerjaID->ViewValue = NULL;
		}
		$this->StatusKerjaID->ViewCustomAttributes = "";

		// TglBekerja
		$this->TglBekerja->ViewValue = $this->TglBekerja->CurrentValue;
		$this->TglBekerja->ViewValue = ew_FormatDateTime($this->TglBekerja->ViewValue, 0);
		$this->TglBekerja->ViewCustomAttributes = "";

		// Homebase
		if (strval($this->Homebase->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->Homebase->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->Homebase->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Homebase, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Homebase->ViewValue = $this->Homebase->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Homebase->ViewValue = $this->Homebase->CurrentValue;
			}
		} else {
			$this->Homebase->ViewValue = NULL;
		}
		$this->Homebase->ViewCustomAttributes = "";

		// ProdiID
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$arwrk = explode(",", $this->ProdiID->CurrentValue);
			$sFilterWrk = "";
			foreach ($arwrk as $wrk) {
				if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
				$sFilterWrk .= "`ProdiID`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING, "");
			}
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->ProdiID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->ProdiID->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->ProdiID->ViewValue .= $this->ProdiID->DisplayValue($arwrk);
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->ProdiID->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
				$rswrk->Close();
			} else {
				$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
			}
		} else {
			$this->ProdiID->ViewValue = NULL;
		}
		$this->ProdiID->ViewCustomAttributes = "";

		// Keilmuan
		$this->Keilmuan->ViewValue = $this->Keilmuan->CurrentValue;
		$this->Keilmuan->ViewCustomAttributes = "";

		// LulusanPT
		$this->LulusanPT->ViewValue = $this->LulusanPT->CurrentValue;
		$this->LulusanPT->ViewCustomAttributes = "";

		// NamaBank
		$this->NamaBank->ViewValue = $this->NamaBank->CurrentValue;
		$this->NamaBank->ViewCustomAttributes = "";

		// NamaAkun
		$this->NamaAkun->ViewValue = $this->NamaAkun->CurrentValue;
		$this->NamaAkun->ViewCustomAttributes = "";

		// NomerAkun
		$this->NomerAkun->ViewValue = $this->NomerAkun->CurrentValue;
		$this->NomerAkun->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Ya";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "Tidak";
		}
		$this->NA->ViewCustomAttributes = "";

			// TeacherID
			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";
			$this->TeacherID->TooltipValue = "";
			if ($this->Export == "")
				$this->TeacherID->ViewValue = ew_Highlight($this->HighlightName(), $this->TeacherID->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->TeacherID->AdvancedSearch->getValue("x"), "");

			// NIPPNS
			$this->NIPPNS->LinkCustomAttributes = "";
			$this->NIPPNS->HrefValue = "";
			$this->NIPPNS->TooltipValue = "";
			if ($this->Export == "")
				$this->NIPPNS->ViewValue = ew_Highlight($this->HighlightName(), $this->NIPPNS->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->NIPPNS->AdvancedSearch->getValue("x"), "");

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";
			if ($this->Export == "")
				$this->Nama->ViewValue = ew_Highlight($this->HighlightName(), $this->Nama->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Nama->AdvancedSearch->getValue("x"), "");

			// AliasCode
			$this->AliasCode->LinkCustomAttributes = "";
			$this->AliasCode->HrefValue = "";
			$this->AliasCode->TooltipValue = "";
			if ($this->Export == "")
				$this->AliasCode->ViewValue = ew_Highlight($this->HighlightName(), $this->AliasCode->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), "", "");

			// KTP
			$this->KTP->LinkCustomAttributes = "";
			$this->KTP->HrefValue = "";
			$this->KTP->TooltipValue = "";
			if ($this->Export == "")
				$this->KTP->ViewValue = ew_Highlight($this->HighlightName(), $this->KTP->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->KTP->AdvancedSearch->getValue("x"), "");

			// KelaminID
			$this->KelaminID->LinkCustomAttributes = "";
			$this->KelaminID->HrefValue = "";
			$this->KelaminID->TooltipValue = "";

			// Telephone
			$this->Telephone->LinkCustomAttributes = "";
			$this->Telephone->HrefValue = "";
			$this->Telephone->TooltipValue = "";
			if ($this->Export == "")
				$this->Telephone->ViewValue = ew_Highlight($this->HighlightName(), $this->Telephone->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->Telephone->AdvancedSearch->getValue("x"), "");

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";
			if ($this->Export == "")
				$this->_Email->ViewValue = ew_Highlight($this->HighlightName(), $this->_Email->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->_Email->AdvancedSearch->getValue("x"), "");

			// IkatanID
			$this->IkatanID->LinkCustomAttributes = "";
			$this->IkatanID->HrefValue = "";
			$this->IkatanID->TooltipValue = "";

			// GolonganID
			$this->GolonganID->LinkCustomAttributes = "";
			$this->GolonganID->HrefValue = "";
			$this->GolonganID->TooltipValue = "";

			// StatusKerjaID
			$this->StatusKerjaID->LinkCustomAttributes = "";
			$this->StatusKerjaID->HrefValue = "";
			$this->StatusKerjaID->TooltipValue = "";

			// Homebase
			$this->Homebase->LinkCustomAttributes = "";
			$this->Homebase->HrefValue = "";
			$this->Homebase->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// TeacherID
			$this->TeacherID->EditAttrs["class"] = "form-control";
			$this->TeacherID->EditCustomAttributes = "";
			$this->TeacherID->EditValue = ew_HtmlEncode($this->TeacherID->CurrentValue);
			$this->TeacherID->PlaceHolder = ew_RemoveHtml($this->TeacherID->FldCaption());

			// NIPPNS
			$this->NIPPNS->EditAttrs["class"] = "form-control";
			$this->NIPPNS->EditCustomAttributes = "";
			$this->NIPPNS->EditValue = ew_HtmlEncode($this->NIPPNS->CurrentValue);
			$this->NIPPNS->PlaceHolder = ew_RemoveHtml($this->NIPPNS->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// AliasCode
			$this->AliasCode->EditAttrs["class"] = "form-control";
			$this->AliasCode->EditCustomAttributes = "";
			$this->AliasCode->EditValue = ew_HtmlEncode($this->AliasCode->CurrentValue);
			$this->AliasCode->PlaceHolder = ew_RemoveHtml($this->AliasCode->FldCaption());

			// KTP
			$this->KTP->EditAttrs["class"] = "form-control";
			$this->KTP->EditCustomAttributes = "";
			$this->KTP->EditValue = ew_HtmlEncode($this->KTP->CurrentValue);
			$this->KTP->PlaceHolder = ew_RemoveHtml($this->KTP->FldCaption());

			// KelaminID
			$this->KelaminID->EditCustomAttributes = "";

			// Telephone
			$this->Telephone->EditAttrs["class"] = "form-control";
			$this->Telephone->EditCustomAttributes = "";
			$this->Telephone->EditValue = ew_HtmlEncode($this->Telephone->CurrentValue);
			$this->Telephone->PlaceHolder = ew_RemoveHtml($this->Telephone->FldCaption());

			// Email
			$this->_Email->EditAttrs["class"] = "form-control";
			$this->_Email->EditCustomAttributes = "";
			$this->_Email->EditValue = ew_HtmlEncode($this->_Email->CurrentValue);
			$this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

			// IkatanID
			$this->IkatanID->EditAttrs["class"] = "form-control";
			$this->IkatanID->EditCustomAttributes = "";

			// GolonganID
			$this->GolonganID->EditAttrs["class"] = "form-control";
			$this->GolonganID->EditCustomAttributes = "";

			// StatusKerjaID
			$this->StatusKerjaID->EditAttrs["class"] = "form-control";
			$this->StatusKerjaID->EditCustomAttributes = "";

			// Homebase
			$this->Homebase->EditAttrs["class"] = "form-control";
			$this->Homebase->EditCustomAttributes = "";

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Add refer script
			// TeacherID

			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";

			// NIPPNS
			$this->NIPPNS->LinkCustomAttributes = "";
			$this->NIPPNS->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// AliasCode
			$this->AliasCode->LinkCustomAttributes = "";
			$this->AliasCode->HrefValue = "";

			// KTP
			$this->KTP->LinkCustomAttributes = "";
			$this->KTP->HrefValue = "";

			// KelaminID
			$this->KelaminID->LinkCustomAttributes = "";
			$this->KelaminID->HrefValue = "";

			// Telephone
			$this->Telephone->LinkCustomAttributes = "";
			$this->Telephone->HrefValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";

			// IkatanID
			$this->IkatanID->LinkCustomAttributes = "";
			$this->IkatanID->HrefValue = "";

			// GolonganID
			$this->GolonganID->LinkCustomAttributes = "";
			$this->GolonganID->HrefValue = "";

			// StatusKerjaID
			$this->StatusKerjaID->LinkCustomAttributes = "";
			$this->StatusKerjaID->HrefValue = "";

			// Homebase
			$this->Homebase->LinkCustomAttributes = "";
			$this->Homebase->HrefValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// TeacherID
			$this->TeacherID->EditAttrs["class"] = "form-control";
			$this->TeacherID->EditCustomAttributes = "";
			$this->TeacherID->EditValue = $this->TeacherID->CurrentValue;
			$this->TeacherID->CssStyle = "font-weight: bold;";
			$this->TeacherID->ViewCustomAttributes = "";

			// NIPPNS
			$this->NIPPNS->EditAttrs["class"] = "form-control";
			$this->NIPPNS->EditCustomAttributes = "";
			$this->NIPPNS->EditValue = ew_HtmlEncode($this->NIPPNS->CurrentValue);
			$this->NIPPNS->PlaceHolder = ew_RemoveHtml($this->NIPPNS->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// AliasCode
			$this->AliasCode->EditAttrs["class"] = "form-control";
			$this->AliasCode->EditCustomAttributes = "";
			$this->AliasCode->EditValue = ew_HtmlEncode($this->AliasCode->CurrentValue);
			$this->AliasCode->PlaceHolder = ew_RemoveHtml($this->AliasCode->FldCaption());

			// KTP
			$this->KTP->EditAttrs["class"] = "form-control";
			$this->KTP->EditCustomAttributes = "";
			$this->KTP->EditValue = ew_HtmlEncode($this->KTP->CurrentValue);
			$this->KTP->PlaceHolder = ew_RemoveHtml($this->KTP->FldCaption());

			// KelaminID
			$this->KelaminID->EditCustomAttributes = "";
			if (trim(strval($this->KelaminID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->KelaminID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_kelamin`";
			$sWhereWrk = "";
			$this->KelaminID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KelaminID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KelaminID->EditValue = $arwrk;

			// Telephone
			$this->Telephone->EditAttrs["class"] = "form-control";
			$this->Telephone->EditCustomAttributes = "";
			$this->Telephone->EditValue = ew_HtmlEncode($this->Telephone->CurrentValue);
			$this->Telephone->PlaceHolder = ew_RemoveHtml($this->Telephone->FldCaption());

			// Email
			$this->_Email->EditAttrs["class"] = "form-control";
			$this->_Email->EditCustomAttributes = "";
			$this->_Email->EditValue = ew_HtmlEncode($this->_Email->CurrentValue);
			$this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

			// IkatanID
			$this->IkatanID->EditAttrs["class"] = "form-control";
			$this->IkatanID->EditCustomAttributes = "";
			if (trim(strval($this->IkatanID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`IkatanID`" . ew_SearchString("=", $this->IkatanID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `IkatanID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_ikatan`";
			$sWhereWrk = "";
			$this->IkatanID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->IkatanID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->IkatanID->EditValue = $arwrk;

			// GolonganID
			$this->GolonganID->EditAttrs["class"] = "form-control";
			$this->GolonganID->EditCustomAttributes = "";
			if (trim(strval($this->GolonganID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`GolonganID`" . ew_SearchString("=", $this->GolonganID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `GolonganID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_golongan`";
			$sWhereWrk = "";
			$this->GolonganID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->GolonganID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->GolonganID->EditValue = $arwrk;

			// StatusKerjaID
			$this->StatusKerjaID->EditAttrs["class"] = "form-control";
			$this->StatusKerjaID->EditCustomAttributes = "";
			if (trim(strval($this->StatusKerjaID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StatusKerjaID`" . ew_SearchString("=", $this->StatusKerjaID->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StatusKerjaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_statuskerja`";
			$sWhereWrk = "";
			$this->StatusKerjaID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StatusKerjaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StatusKerjaID->EditValue = $arwrk;

			// Homebase
			$this->Homebase->EditAttrs["class"] = "form-control";
			$this->Homebase->EditCustomAttributes = "";
			if (trim(strval($this->Homebase->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->Homebase->CurrentValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->Homebase->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Homebase, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Homebase->EditValue = $arwrk;

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// Edit refer script
			// TeacherID

			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";

			// NIPPNS
			$this->NIPPNS->LinkCustomAttributes = "";
			$this->NIPPNS->HrefValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";

			// AliasCode
			$this->AliasCode->LinkCustomAttributes = "";
			$this->AliasCode->HrefValue = "";

			// KTP
			$this->KTP->LinkCustomAttributes = "";
			$this->KTP->HrefValue = "";

			// KelaminID
			$this->KelaminID->LinkCustomAttributes = "";
			$this->KelaminID->HrefValue = "";

			// Telephone
			$this->Telephone->LinkCustomAttributes = "";
			$this->Telephone->HrefValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";

			// IkatanID
			$this->IkatanID->LinkCustomAttributes = "";
			$this->IkatanID->HrefValue = "";

			// GolonganID
			$this->GolonganID->LinkCustomAttributes = "";
			$this->GolonganID->HrefValue = "";

			// StatusKerjaID
			$this->StatusKerjaID->LinkCustomAttributes = "";
			$this->StatusKerjaID->HrefValue = "";

			// Homebase
			$this->Homebase->LinkCustomAttributes = "";
			$this->Homebase->HrefValue = "";

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
		if (!$this->TeacherID->FldIsDetailKey && !is_null($this->TeacherID->FormValue) && $this->TeacherID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TeacherID->FldCaption(), $this->TeacherID->ReqErrMsg));
		}
		if (!$this->Nama->FldIsDetailKey && !is_null($this->Nama->FormValue) && $this->Nama->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Nama->FldCaption(), $this->Nama->ReqErrMsg));
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

			// TeacherID
			// NIPPNS

			$this->NIPPNS->SetDbValueDef($rsnew, $this->NIPPNS->CurrentValue, NULL, $this->NIPPNS->ReadOnly);

			// Nama
			$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, "", $this->Nama->ReadOnly);

			// AliasCode
			$this->AliasCode->SetDbValueDef($rsnew, $this->AliasCode->CurrentValue, NULL, $this->AliasCode->ReadOnly);

			// KTP
			$this->KTP->SetDbValueDef($rsnew, $this->KTP->CurrentValue, NULL, $this->KTP->ReadOnly);

			// KelaminID
			$this->KelaminID->SetDbValueDef($rsnew, $this->KelaminID->CurrentValue, NULL, $this->KelaminID->ReadOnly);

			// Telephone
			$this->Telephone->SetDbValueDef($rsnew, $this->Telephone->CurrentValue, NULL, $this->Telephone->ReadOnly);

			// Email
			$this->_Email->SetDbValueDef($rsnew, $this->_Email->CurrentValue, NULL, $this->_Email->ReadOnly);

			// IkatanID
			$this->IkatanID->SetDbValueDef($rsnew, $this->IkatanID->CurrentValue, NULL, $this->IkatanID->ReadOnly);

			// GolonganID
			$this->GolonganID->SetDbValueDef($rsnew, $this->GolonganID->CurrentValue, NULL, $this->GolonganID->ReadOnly);

			// StatusKerjaID
			$this->StatusKerjaID->SetDbValueDef($rsnew, $this->StatusKerjaID->CurrentValue, NULL, $this->StatusKerjaID->ReadOnly);

			// Homebase
			$this->Homebase->SetDbValueDef($rsnew, $this->Homebase->CurrentValue, NULL, $this->Homebase->ReadOnly);

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

		// TeacherID
		$this->TeacherID->SetDbValueDef($rsnew, $this->TeacherID->CurrentValue, "", FALSE);

		// NIPPNS
		$this->NIPPNS->SetDbValueDef($rsnew, $this->NIPPNS->CurrentValue, NULL, FALSE);

		// Nama
		$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, "", FALSE);

		// AliasCode
		$this->AliasCode->SetDbValueDef($rsnew, $this->AliasCode->CurrentValue, NULL, FALSE);

		// KTP
		$this->KTP->SetDbValueDef($rsnew, $this->KTP->CurrentValue, NULL, FALSE);

		// KelaminID
		$this->KelaminID->SetDbValueDef($rsnew, $this->KelaminID->CurrentValue, NULL, strval($this->KelaminID->CurrentValue) == "");

		// Telephone
		$this->Telephone->SetDbValueDef($rsnew, $this->Telephone->CurrentValue, NULL, FALSE);

		// Email
		$this->_Email->SetDbValueDef($rsnew, $this->_Email->CurrentValue, NULL, FALSE);

		// IkatanID
		$this->IkatanID->SetDbValueDef($rsnew, $this->IkatanID->CurrentValue, NULL, FALSE);

		// GolonganID
		$this->GolonganID->SetDbValueDef($rsnew, $this->GolonganID->CurrentValue, NULL, FALSE);

		// StatusKerjaID
		$this->StatusKerjaID->SetDbValueDef($rsnew, $this->StatusKerjaID->CurrentValue, NULL, FALSE);

		// Homebase
		$this->Homebase->SetDbValueDef($rsnew, $this->Homebase->CurrentValue, NULL, FALSE);

		// NA
		$this->NA->SetDbValueDef($rsnew, ((strval($this->NA->CurrentValue) == "Y") ? "Y" : "N"), NULL, strval($this->NA->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['TeacherID']) == "") {
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

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->TeacherID->AdvancedSearch->Load();
		$this->NIPPNS->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->Gelar->AdvancedSearch->Load();
		$this->LevelID->AdvancedSearch->Load();
		$this->KTP->AdvancedSearch->Load();
		$this->TempatLahir->AdvancedSearch->Load();
		$this->TanggalLahir->AdvancedSearch->Load();
		$this->AgamaID->AdvancedSearch->Load();
		$this->KelaminID->AdvancedSearch->Load();
		$this->Telephone->AdvancedSearch->Load();
		$this->_Email->AdvancedSearch->Load();
		$this->Alamat->AdvancedSearch->Load();
		$this->KodePos->AdvancedSearch->Load();
		$this->ProvinsiID->AdvancedSearch->Load();
		$this->KabupatenKotaID->AdvancedSearch->Load();
		$this->KecamatanID->AdvancedSearch->Load();
		$this->DesaID->AdvancedSearch->Load();
		$this->InstitusiInduk->AdvancedSearch->Load();
		$this->IkatanID->AdvancedSearch->Load();
		$this->GolonganID->AdvancedSearch->Load();
		$this->StatusKerjaID->AdvancedSearch->Load();
		$this->TglBekerja->AdvancedSearch->Load();
		$this->Homebase->AdvancedSearch->Load();
		$this->ProdiID->AdvancedSearch->Load();
		$this->Keilmuan->AdvancedSearch->Load();
		$this->LulusanPT->AdvancedSearch->Load();
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
		$item->Body = "<button id=\"emf_teacher\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_teacher',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fteacherlist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
		case "x_KelaminID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Kelamin` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
			$sWhereWrk = "";
			$this->KelaminID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Kelamin` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KelaminID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_IkatanID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `IkatanID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_ikatan`";
			$sWhereWrk = "";
			$this->IkatanID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`IkatanID` = {filter_value}', "t0" => "16", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->IkatanID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_GolonganID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `GolonganID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_golongan`";
			$sWhereWrk = "";
			$this->GolonganID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`GolonganID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->GolonganID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StatusKerjaID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StatusKerjaID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statuskerja`";
			$sWhereWrk = "";
			$this->StatusKerjaID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StatusKerjaID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StatusKerjaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_Homebase":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProdiID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->Homebase->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProdiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Homebase, $sWhereWrk); // Call Lookup selecting
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
if (!isset($teacher_list)) $teacher_list = new cteacher_list();

// Page init
$teacher_list->Page_Init();

// Page main
$teacher_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$teacher_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($teacher->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fteacherlist = new ew_Form("fteacherlist", "list");
fteacherlist.FormKeyCountName = '<?php echo $teacher_list->FormKeyCountName ?>';

// Validate form
fteacherlist.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_TeacherID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $teacher->TeacherID->FldCaption(), $teacher->TeacherID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Nama");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $teacher->Nama->FldCaption(), $teacher->Nama->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "__Email");
			if (elm && !ew_CheckEmail(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($teacher->_Email->FldErrMsg()) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}
	return true;
}

// Form_CustomValidate event
fteacherlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fteacherlist.ValidateRequired = true;
<?php } else { ?>
fteacherlist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fteacherlist.Lists["x_KelaminID"] = {"LinkField":"x_Kelamin","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kelamin"};
fteacherlist.Lists["x_IkatanID"] = {"LinkField":"x_IkatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_ikatan"};
fteacherlist.Lists["x_GolonganID"] = {"LinkField":"x_GolonganID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_golongan"};
fteacherlist.Lists["x_StatusKerjaID"] = {"LinkField":"x_StatusKerjaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statuskerja"};
fteacherlist.Lists["x_Homebase"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fteacherlist.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fteacherlist.Lists["x_NA"].Options = <?php echo json_encode($teacher->NA->Options()) ?>;

// Form object for search
var CurrentSearchForm = fteacherlistsrch = new ew_Form("fteacherlistsrch");

// Init search panel as collapsed
if (fteacherlistsrch) fteacherlistsrch.InitSearchPanel = true;
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
<?php if ($teacher->Export == "") { ?>
<div class="ewToolbar">
<?php if ($teacher->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($teacher_list->TotalRecs > 0 && $teacher_list->ExportOptions->Visible()) { ?>
<?php $teacher_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($teacher_list->SearchOptions->Visible()) { ?>
<?php $teacher_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($teacher_list->FilterOptions->Visible()) { ?>
<?php $teacher_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php if ($teacher->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = $teacher_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($teacher_list->TotalRecs <= 0)
			$teacher_list->TotalRecs = $teacher->SelectRecordCount();
	} else {
		if (!$teacher_list->Recordset && ($teacher_list->Recordset = $teacher_list->LoadRecordset()))
			$teacher_list->TotalRecs = $teacher_list->Recordset->RecordCount();
	}
	$teacher_list->StartRec = 1;
	if ($teacher_list->DisplayRecs <= 0 || ($teacher->Export <> "" && $teacher->ExportAll)) // Display all records
		$teacher_list->DisplayRecs = $teacher_list->TotalRecs;
	if (!($teacher->Export <> "" && $teacher->ExportAll))
		$teacher_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$teacher_list->Recordset = $teacher_list->LoadRecordset($teacher_list->StartRec-1, $teacher_list->DisplayRecs);

	// Set no record found message
	if ($teacher->CurrentAction == "" && $teacher_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$teacher_list->setWarningMessage(ew_DeniedMsg());
		if ($teacher_list->SearchWhere == "0=101")
			$teacher_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$teacher_list->setWarningMessage($Language->Phrase("NoRecord"));
	}

	// Audit trail on search
	if ($teacher_list->AuditTrailOnSearch && $teacher_list->Command == "search" && !$teacher_list->RestoreSearch) {
		$searchparm = ew_ServerVar("QUERY_STRING");
		$searchsql = $teacher_list->getSessionWhere();
		$teacher_list->WriteAuditTrailOnSearch($searchparm, $searchsql);
	}
$teacher_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($teacher->Export == "" && $teacher->CurrentAction == "") { ?>
<form name="fteacherlistsrch" id="fteacherlistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($teacher_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fteacherlistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="teacher">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($teacher_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($teacher_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $teacher_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($teacher_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($teacher_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($teacher_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($teacher_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
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
<?php $teacher_list->ShowPageHeader(); ?>
<?php
$teacher_list->ShowMessage();
?>
<?php if ($teacher_list->TotalRecs > 0 || $teacher->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid teacher">
<?php if ($teacher->Export == "") { ?>
<div class="panel-heading ewGridUpperPanel">
<?php if ($teacher->CurrentAction <> "gridadd" && $teacher->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($teacher_list->Pager)) $teacher_list->Pager = new cPrevNextPager($teacher_list->StartRec, $teacher_list->DisplayRecs, $teacher_list->TotalRecs) ?>
<?php if ($teacher_list->Pager->RecordCount > 0 && $teacher_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($teacher_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $teacher_list->PageUrl() ?>start=<?php echo $teacher_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($teacher_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $teacher_list->PageUrl() ?>start=<?php echo $teacher_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $teacher_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($teacher_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $teacher_list->PageUrl() ?>start=<?php echo $teacher_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($teacher_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $teacher_list->PageUrl() ?>start=<?php echo $teacher_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $teacher_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $teacher_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $teacher_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $teacher_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($teacher_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="fteacherlist" id="fteacherlist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($teacher_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $teacher_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="teacher">
<div id="gmp_teacher" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($teacher_list->TotalRecs > 0 || $teacher->CurrentAction == "gridedit") { ?>
<table id="tbl_teacherlist" class="table ewTable">
<?php echo $teacher->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$teacher_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$teacher_list->RenderListOptions();

// Render list options (header, left)
$teacher_list->ListOptions->Render("header", "left");
?>
<?php if ($teacher->TeacherID->Visible) { // TeacherID ?>
	<?php if ($teacher->SortUrl($teacher->TeacherID) == "") { ?>
		<th data-name="TeacherID"><div id="elh_teacher_TeacherID" class="teacher_TeacherID"><div class="ewTableHeaderCaption" style="white-space: nowrap;"><?php echo $teacher->TeacherID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TeacherID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->TeacherID) ?>',1);"><div id="elh_teacher_TeacherID" class="teacher_TeacherID">
			<div class="ewTableHeaderBtn" style="white-space: nowrap;"><span class="ewTableHeaderCaption"><?php echo $teacher->TeacherID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($teacher->TeacherID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->TeacherID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->NIPPNS->Visible) { // NIPPNS ?>
	<?php if ($teacher->SortUrl($teacher->NIPPNS) == "") { ?>
		<th data-name="NIPPNS"><div id="elh_teacher_NIPPNS" class="teacher_NIPPNS"><div class="ewTableHeaderCaption" style="white-space: nowrap;"><?php echo $teacher->NIPPNS->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NIPPNS"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->NIPPNS) ?>',1);"><div id="elh_teacher_NIPPNS" class="teacher_NIPPNS">
			<div class="ewTableHeaderBtn" style="white-space: nowrap;"><span class="ewTableHeaderCaption"><?php echo $teacher->NIPPNS->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($teacher->NIPPNS->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->NIPPNS->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->Nama->Visible) { // Nama ?>
	<?php if ($teacher->SortUrl($teacher->Nama) == "") { ?>
		<th data-name="Nama"><div id="elh_teacher_Nama" class="teacher_Nama"><div class="ewTableHeaderCaption" style="white-space: nowrap;"><?php echo $teacher->Nama->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Nama"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->Nama) ?>',1);"><div id="elh_teacher_Nama" class="teacher_Nama">
			<div class="ewTableHeaderBtn" style="white-space: nowrap;"><span class="ewTableHeaderCaption"><?php echo $teacher->Nama->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($teacher->Nama->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->Nama->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->AliasCode->Visible) { // AliasCode ?>
	<?php if ($teacher->SortUrl($teacher->AliasCode) == "") { ?>
		<th data-name="AliasCode"><div id="elh_teacher_AliasCode" class="teacher_AliasCode"><div class="ewTableHeaderCaption"><?php echo $teacher->AliasCode->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="AliasCode"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->AliasCode) ?>',1);"><div id="elh_teacher_AliasCode" class="teacher_AliasCode">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $teacher->AliasCode->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($teacher->AliasCode->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->AliasCode->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->KTP->Visible) { // KTP ?>
	<?php if ($teacher->SortUrl($teacher->KTP) == "") { ?>
		<th data-name="KTP"><div id="elh_teacher_KTP" class="teacher_KTP"><div class="ewTableHeaderCaption" style="white-space: nowrap;"><?php echo $teacher->KTP->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="KTP"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->KTP) ?>',1);"><div id="elh_teacher_KTP" class="teacher_KTP">
			<div class="ewTableHeaderBtn" style="white-space: nowrap;"><span class="ewTableHeaderCaption"><?php echo $teacher->KTP->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($teacher->KTP->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->KTP->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->KelaminID->Visible) { // KelaminID ?>
	<?php if ($teacher->SortUrl($teacher->KelaminID) == "") { ?>
		<th data-name="KelaminID"><div id="elh_teacher_KelaminID" class="teacher_KelaminID"><div class="ewTableHeaderCaption"><?php echo $teacher->KelaminID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="KelaminID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->KelaminID) ?>',1);"><div id="elh_teacher_KelaminID" class="teacher_KelaminID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $teacher->KelaminID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($teacher->KelaminID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->KelaminID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->Telephone->Visible) { // Telephone ?>
	<?php if ($teacher->SortUrl($teacher->Telephone) == "") { ?>
		<th data-name="Telephone"><div id="elh_teacher_Telephone" class="teacher_Telephone"><div class="ewTableHeaderCaption"><?php echo $teacher->Telephone->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Telephone"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->Telephone) ?>',1);"><div id="elh_teacher_Telephone" class="teacher_Telephone">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $teacher->Telephone->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($teacher->Telephone->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->Telephone->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->_Email->Visible) { // Email ?>
	<?php if ($teacher->SortUrl($teacher->_Email) == "") { ?>
		<th data-name="_Email"><div id="elh_teacher__Email" class="teacher__Email"><div class="ewTableHeaderCaption"><?php echo $teacher->_Email->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="_Email"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->_Email) ?>',1);"><div id="elh_teacher__Email" class="teacher__Email">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $teacher->_Email->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($teacher->_Email->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->_Email->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->IkatanID->Visible) { // IkatanID ?>
	<?php if ($teacher->SortUrl($teacher->IkatanID) == "") { ?>
		<th data-name="IkatanID"><div id="elh_teacher_IkatanID" class="teacher_IkatanID"><div class="ewTableHeaderCaption"><?php echo $teacher->IkatanID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="IkatanID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->IkatanID) ?>',1);"><div id="elh_teacher_IkatanID" class="teacher_IkatanID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $teacher->IkatanID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($teacher->IkatanID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->IkatanID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->GolonganID->Visible) { // GolonganID ?>
	<?php if ($teacher->SortUrl($teacher->GolonganID) == "") { ?>
		<th data-name="GolonganID"><div id="elh_teacher_GolonganID" class="teacher_GolonganID"><div class="ewTableHeaderCaption"><?php echo $teacher->GolonganID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="GolonganID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->GolonganID) ?>',1);"><div id="elh_teacher_GolonganID" class="teacher_GolonganID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $teacher->GolonganID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($teacher->GolonganID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->GolonganID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->StatusKerjaID->Visible) { // StatusKerjaID ?>
	<?php if ($teacher->SortUrl($teacher->StatusKerjaID) == "") { ?>
		<th data-name="StatusKerjaID"><div id="elh_teacher_StatusKerjaID" class="teacher_StatusKerjaID"><div class="ewTableHeaderCaption"><?php echo $teacher->StatusKerjaID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="StatusKerjaID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->StatusKerjaID) ?>',1);"><div id="elh_teacher_StatusKerjaID" class="teacher_StatusKerjaID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $teacher->StatusKerjaID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($teacher->StatusKerjaID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->StatusKerjaID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->Homebase->Visible) { // Homebase ?>
	<?php if ($teacher->SortUrl($teacher->Homebase) == "") { ?>
		<th data-name="Homebase"><div id="elh_teacher_Homebase" class="teacher_Homebase"><div class="ewTableHeaderCaption"><?php echo $teacher->Homebase->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Homebase"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->Homebase) ?>',1);"><div id="elh_teacher_Homebase" class="teacher_Homebase">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $teacher->Homebase->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($teacher->Homebase->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->Homebase->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($teacher->NA->Visible) { // NA ?>
	<?php if ($teacher->SortUrl($teacher->NA) == "") { ?>
		<th data-name="NA"><div id="elh_teacher_NA" class="teacher_NA"><div class="ewTableHeaderCaption"><?php echo $teacher->NA->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NA"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $teacher->SortUrl($teacher->NA) ?>',1);"><div id="elh_teacher_NA" class="teacher_NA">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $teacher->NA->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($teacher->NA->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($teacher->NA->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$teacher_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($teacher->ExportAll && $teacher->Export <> "") {
	$teacher_list->StopRec = $teacher_list->TotalRecs;
} else {

	// Set the last record to display
	if ($teacher_list->TotalRecs > $teacher_list->StartRec + $teacher_list->DisplayRecs - 1)
		$teacher_list->StopRec = $teacher_list->StartRec + $teacher_list->DisplayRecs - 1;
	else
		$teacher_list->StopRec = $teacher_list->TotalRecs;
}

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($teacher_list->FormKeyCountName) && ($teacher->CurrentAction == "gridadd" || $teacher->CurrentAction == "gridedit" || $teacher->CurrentAction == "F")) {
		$teacher_list->KeyCount = $objForm->GetValue($teacher_list->FormKeyCountName);
		$teacher_list->StopRec = $teacher_list->StartRec + $teacher_list->KeyCount - 1;
	}
}
$teacher_list->RecCnt = $teacher_list->StartRec - 1;
if ($teacher_list->Recordset && !$teacher_list->Recordset->EOF) {
	$teacher_list->Recordset->MoveFirst();
	$bSelectLimit = $teacher_list->UseSelectLimit;
	if (!$bSelectLimit && $teacher_list->StartRec > 1)
		$teacher_list->Recordset->Move($teacher_list->StartRec - 1);
} elseif (!$teacher->AllowAddDeleteRow && $teacher_list->StopRec == 0) {
	$teacher_list->StopRec = $teacher->GridAddRowCount;
}

// Initialize aggregate
$teacher->RowType = EW_ROWTYPE_AGGREGATEINIT;
$teacher->ResetAttrs();
$teacher_list->RenderRow();
$teacher_list->EditRowCnt = 0;
if ($teacher->CurrentAction == "edit")
	$teacher_list->RowIndex = 1;
while ($teacher_list->RecCnt < $teacher_list->StopRec) {
	$teacher_list->RecCnt++;
	if (intval($teacher_list->RecCnt) >= intval($teacher_list->StartRec)) {
		$teacher_list->RowCnt++;

		// Set up key count
		$teacher_list->KeyCount = $teacher_list->RowIndex;

		// Init row class and style
		$teacher->ResetAttrs();
		$teacher->CssClass = "";
		if ($teacher->CurrentAction == "gridadd") {
			$teacher_list->LoadDefaultValues(); // Load default values
		} else {
			$teacher_list->LoadRowValues($teacher_list->Recordset); // Load row values
		}
		$teacher->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($teacher->CurrentAction == "edit") {
			if ($teacher_list->CheckInlineEditKey() && $teacher_list->EditRowCnt == 0) { // Inline edit
				$teacher->RowType = EW_ROWTYPE_EDIT; // Render edit
			}
		}
		if ($teacher->CurrentAction == "edit" && $teacher->RowType == EW_ROWTYPE_EDIT && $teacher->EventCancelled) { // Update failed
			$objForm->Index = 1;
			$teacher_list->RestoreFormValues(); // Restore form values
		}
		if ($teacher->RowType == EW_ROWTYPE_EDIT) // Edit row
			$teacher_list->EditRowCnt++;

		// Set up row id / data-rowindex
		$teacher->RowAttrs = array_merge($teacher->RowAttrs, array('data-rowindex'=>$teacher_list->RowCnt, 'id'=>'r' . $teacher_list->RowCnt . '_teacher', 'data-rowtype'=>$teacher->RowType));

		// Render row
		$teacher_list->RenderRow();

		// Render list options
		$teacher_list->RenderListOptions();
?>
	<tr<?php echo $teacher->RowAttributes() ?>>
<?php

// Render list options (body, left)
$teacher_list->ListOptions->Render("body", "left", $teacher_list->RowCnt);
?>
	<?php if ($teacher->TeacherID->Visible) { // TeacherID ?>
		<td data-name="TeacherID"<?php echo $teacher->TeacherID->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_TeacherID" class="form-group teacher_TeacherID">
<span<?php echo $teacher->TeacherID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $teacher->TeacherID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="teacher" data-field="x_TeacherID" name="x<?php echo $teacher_list->RowIndex ?>_TeacherID" id="x<?php echo $teacher_list->RowIndex ?>_TeacherID" value="<?php echo ew_HtmlEncode($teacher->TeacherID->CurrentValue) ?>">
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_TeacherID" class="teacher_TeacherID">
<span<?php echo $teacher->TeacherID->ViewAttributes() ?>>
<?php echo $teacher->TeacherID->ListViewValue() ?></span>
</span>
<?php } ?>
<a id="<?php echo $teacher_list->PageObjName . "_row_" . $teacher_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($teacher->NIPPNS->Visible) { // NIPPNS ?>
		<td data-name="NIPPNS"<?php echo $teacher->NIPPNS->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_NIPPNS" class="form-group teacher_NIPPNS">
<input type="text" data-table="teacher" data-field="x_NIPPNS" name="x<?php echo $teacher_list->RowIndex ?>_NIPPNS" id="x<?php echo $teacher_list->RowIndex ?>_NIPPNS" size="30" maxlength="30" placeholder="<?php echo ew_HtmlEncode($teacher->NIPPNS->getPlaceHolder()) ?>" value="<?php echo $teacher->NIPPNS->EditValue ?>"<?php echo $teacher->NIPPNS->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_NIPPNS" class="teacher_NIPPNS">
<span<?php echo $teacher->NIPPNS->ViewAttributes() ?>>
<?php echo $teacher->NIPPNS->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($teacher->Nama->Visible) { // Nama ?>
		<td data-name="Nama"<?php echo $teacher->Nama->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_Nama" class="form-group teacher_Nama">
<input type="text" data-table="teacher" data-field="x_Nama" name="x<?php echo $teacher_list->RowIndex ?>_Nama" id="x<?php echo $teacher_list->RowIndex ?>_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->Nama->getPlaceHolder()) ?>" value="<?php echo $teacher->Nama->EditValue ?>"<?php echo $teacher->Nama->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_Nama" class="teacher_Nama">
<span<?php echo $teacher->Nama->ViewAttributes() ?>>
<?php echo $teacher->Nama->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($teacher->AliasCode->Visible) { // AliasCode ?>
		<td data-name="AliasCode"<?php echo $teacher->AliasCode->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_AliasCode" class="form-group teacher_AliasCode">
<input type="text" data-table="teacher" data-field="x_AliasCode" name="x<?php echo $teacher_list->RowIndex ?>_AliasCode" id="x<?php echo $teacher_list->RowIndex ?>_AliasCode" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->AliasCode->getPlaceHolder()) ?>" value="<?php echo $teacher->AliasCode->EditValue ?>"<?php echo $teacher->AliasCode->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_AliasCode" class="teacher_AliasCode">
<span<?php echo $teacher->AliasCode->ViewAttributes() ?>>
<?php echo $teacher->AliasCode->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($teacher->KTP->Visible) { // KTP ?>
		<td data-name="KTP"<?php echo $teacher->KTP->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_KTP" class="form-group teacher_KTP">
<input type="text" data-table="teacher" data-field="x_KTP" name="x<?php echo $teacher_list->RowIndex ?>_KTP" id="x<?php echo $teacher_list->RowIndex ?>_KTP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->KTP->getPlaceHolder()) ?>" value="<?php echo $teacher->KTP->EditValue ?>"<?php echo $teacher->KTP->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_KTP" class="teacher_KTP">
<span<?php echo $teacher->KTP->ViewAttributes() ?>>
<?php echo $teacher->KTP->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($teacher->KelaminID->Visible) { // KelaminID ?>
		<td data-name="KelaminID"<?php echo $teacher->KelaminID->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_KelaminID" class="form-group teacher_KelaminID">
<div id="tp_x<?php echo $teacher_list->RowIndex ?>_KelaminID" class="ewTemplate"><input type="radio" data-table="teacher" data-field="x_KelaminID" data-value-separator="<?php echo $teacher->KelaminID->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $teacher_list->RowIndex ?>_KelaminID" id="x<?php echo $teacher_list->RowIndex ?>_KelaminID" value="{value}"<?php echo $teacher->KelaminID->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $teacher_list->RowIndex ?>_KelaminID" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->KelaminID->RadioButtonListHtml(FALSE, "x{$teacher_list->RowIndex}_KelaminID") ?>
</div></div>
<input type="hidden" name="s_x<?php echo $teacher_list->RowIndex ?>_KelaminID" id="s_x<?php echo $teacher_list->RowIndex ?>_KelaminID" value="<?php echo $teacher->KelaminID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_KelaminID" class="teacher_KelaminID">
<span<?php echo $teacher->KelaminID->ViewAttributes() ?>>
<?php echo $teacher->KelaminID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($teacher->Telephone->Visible) { // Telephone ?>
		<td data-name="Telephone"<?php echo $teacher->Telephone->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_Telephone" class="form-group teacher_Telephone">
<input type="text" data-table="teacher" data-field="x_Telephone" name="x<?php echo $teacher_list->RowIndex ?>_Telephone" id="x<?php echo $teacher_list->RowIndex ?>_Telephone" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->Telephone->getPlaceHolder()) ?>" value="<?php echo $teacher->Telephone->EditValue ?>"<?php echo $teacher->Telephone->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_Telephone" class="teacher_Telephone">
<span<?php echo $teacher->Telephone->ViewAttributes() ?>>
<?php echo $teacher->Telephone->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($teacher->_Email->Visible) { // Email ?>
		<td data-name="_Email"<?php echo $teacher->_Email->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher__Email" class="form-group teacher__Email">
<input type="text" data-table="teacher" data-field="x__Email" name="x<?php echo $teacher_list->RowIndex ?>__Email" id="x<?php echo $teacher_list->RowIndex ?>__Email" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($teacher->_Email->getPlaceHolder()) ?>" value="<?php echo $teacher->_Email->EditValue ?>"<?php echo $teacher->_Email->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher__Email" class="teacher__Email">
<span<?php echo $teacher->_Email->ViewAttributes() ?>>
<?php echo $teacher->_Email->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($teacher->IkatanID->Visible) { // IkatanID ?>
		<td data-name="IkatanID"<?php echo $teacher->IkatanID->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_IkatanID" class="form-group teacher_IkatanID">
<select data-table="teacher" data-field="x_IkatanID" data-value-separator="<?php echo $teacher->IkatanID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $teacher_list->RowIndex ?>_IkatanID" name="x<?php echo $teacher_list->RowIndex ?>_IkatanID"<?php echo $teacher->IkatanID->EditAttributes() ?>>
<?php echo $teacher->IkatanID->SelectOptionListHtml("x<?php echo $teacher_list->RowIndex ?>_IkatanID") ?>
</select>
<input type="hidden" name="s_x<?php echo $teacher_list->RowIndex ?>_IkatanID" id="s_x<?php echo $teacher_list->RowIndex ?>_IkatanID" value="<?php echo $teacher->IkatanID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_IkatanID" class="teacher_IkatanID">
<span<?php echo $teacher->IkatanID->ViewAttributes() ?>>
<?php echo $teacher->IkatanID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($teacher->GolonganID->Visible) { // GolonganID ?>
		<td data-name="GolonganID"<?php echo $teacher->GolonganID->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_GolonganID" class="form-group teacher_GolonganID">
<select data-table="teacher" data-field="x_GolonganID" data-value-separator="<?php echo $teacher->GolonganID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $teacher_list->RowIndex ?>_GolonganID" name="x<?php echo $teacher_list->RowIndex ?>_GolonganID"<?php echo $teacher->GolonganID->EditAttributes() ?>>
<?php echo $teacher->GolonganID->SelectOptionListHtml("x<?php echo $teacher_list->RowIndex ?>_GolonganID") ?>
</select>
<input type="hidden" name="s_x<?php echo $teacher_list->RowIndex ?>_GolonganID" id="s_x<?php echo $teacher_list->RowIndex ?>_GolonganID" value="<?php echo $teacher->GolonganID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_GolonganID" class="teacher_GolonganID">
<span<?php echo $teacher->GolonganID->ViewAttributes() ?>>
<?php echo $teacher->GolonganID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($teacher->StatusKerjaID->Visible) { // StatusKerjaID ?>
		<td data-name="StatusKerjaID"<?php echo $teacher->StatusKerjaID->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_StatusKerjaID" class="form-group teacher_StatusKerjaID">
<select data-table="teacher" data-field="x_StatusKerjaID" data-value-separator="<?php echo $teacher->StatusKerjaID->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $teacher_list->RowIndex ?>_StatusKerjaID" name="x<?php echo $teacher_list->RowIndex ?>_StatusKerjaID"<?php echo $teacher->StatusKerjaID->EditAttributes() ?>>
<?php echo $teacher->StatusKerjaID->SelectOptionListHtml("x<?php echo $teacher_list->RowIndex ?>_StatusKerjaID") ?>
</select>
<input type="hidden" name="s_x<?php echo $teacher_list->RowIndex ?>_StatusKerjaID" id="s_x<?php echo $teacher_list->RowIndex ?>_StatusKerjaID" value="<?php echo $teacher->StatusKerjaID->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_StatusKerjaID" class="teacher_StatusKerjaID">
<span<?php echo $teacher->StatusKerjaID->ViewAttributes() ?>>
<?php echo $teacher->StatusKerjaID->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($teacher->Homebase->Visible) { // Homebase ?>
		<td data-name="Homebase"<?php echo $teacher->Homebase->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_Homebase" class="form-group teacher_Homebase">
<select data-table="teacher" data-field="x_Homebase" data-value-separator="<?php echo $teacher->Homebase->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $teacher_list->RowIndex ?>_Homebase" name="x<?php echo $teacher_list->RowIndex ?>_Homebase"<?php echo $teacher->Homebase->EditAttributes() ?>>
<?php echo $teacher->Homebase->SelectOptionListHtml("x<?php echo $teacher_list->RowIndex ?>_Homebase") ?>
</select>
<input type="hidden" name="s_x<?php echo $teacher_list->RowIndex ?>_Homebase" id="s_x<?php echo $teacher_list->RowIndex ?>_Homebase" value="<?php echo $teacher->Homebase->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_Homebase" class="teacher_Homebase">
<span<?php echo $teacher->Homebase->ViewAttributes() ?>>
<?php echo $teacher->Homebase->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($teacher->NA->Visible) { // NA ?>
		<td data-name="NA"<?php echo $teacher->NA->CellAttributes() ?>>
<?php if ($teacher->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_NA" class="form-group teacher_NA">
<div id="tp_x<?php echo $teacher_list->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="teacher" data-field="x_NA" data-value-separator="<?php echo $teacher->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $teacher_list->RowIndex ?>_NA" id="x<?php echo $teacher_list->RowIndex ?>_NA" value="{value}"<?php echo $teacher->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $teacher_list->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->NA->RadioButtonListHtml(FALSE, "x{$teacher_list->RowIndex}_NA") ?>
</div></div>
</span>
<?php } ?>
<?php if ($teacher->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $teacher_list->RowCnt ?>_teacher_NA" class="teacher_NA">
<span<?php echo $teacher->NA->ViewAttributes() ?>>
<?php echo $teacher->NA->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$teacher_list->ListOptions->Render("body", "right", $teacher_list->RowCnt);
?>
	</tr>
<?php if ($teacher->RowType == EW_ROWTYPE_ADD || $teacher->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fteacherlist.UpdateOpts(<?php echo $teacher_list->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	if ($teacher->CurrentAction <> "gridadd")
		$teacher_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($teacher->CurrentAction == "edit") { ?>
<input type="hidden" name="<?php echo $teacher_list->FormKeyCountName ?>" id="<?php echo $teacher_list->FormKeyCountName ?>" value="<?php echo $teacher_list->KeyCount ?>">
<?php } ?>
<?php if ($teacher->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($teacher_list->Recordset)
	$teacher_list->Recordset->Close();
?>
<?php if ($teacher->Export == "") { ?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($teacher->CurrentAction <> "gridadd" && $teacher->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($teacher_list->Pager)) $teacher_list->Pager = new cPrevNextPager($teacher_list->StartRec, $teacher_list->DisplayRecs, $teacher_list->TotalRecs) ?>
<?php if ($teacher_list->Pager->RecordCount > 0 && $teacher_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($teacher_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $teacher_list->PageUrl() ?>start=<?php echo $teacher_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($teacher_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $teacher_list->PageUrl() ?>start=<?php echo $teacher_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $teacher_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($teacher_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $teacher_list->PageUrl() ?>start=<?php echo $teacher_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($teacher_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $teacher_list->PageUrl() ?>start=<?php echo $teacher_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $teacher_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $teacher_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $teacher_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $teacher_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($teacher_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($teacher_list->TotalRecs == 0 && $teacher->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($teacher_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($teacher->Export == "") { ?>
<script type="text/javascript">
fteacherlistsrch.FilterList = <?php echo $teacher_list->GetFilterList() ?>;
fteacherlistsrch.Init();
fteacherlist.Init();
</script>
<?php } ?>
<?php
$teacher_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($teacher->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$teacher_list->Page_Terminate();
?>
