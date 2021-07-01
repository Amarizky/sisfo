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

$student_view = NULL; // Initialize page object first

class cstudent_view extends cstudent {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'student';

	// Page object name
	var $PageObjName = 'student_view';

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
		$KeyUrl = "";
		if (@$_GET["StudentID"] <> "") {
			$this->RecKey["StudentID"] = $_GET["StudentID"];
			$KeyUrl .= "&amp;StudentID=" . urlencode($this->RecKey["StudentID"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

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

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
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
		if (!$Security->CanView()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("studentlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
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
		if (@$_GET["StudentID"] <> "") {
			if ($gsExportFile <> "") $gsExportFile .= "_";
			$gsExportFile .= ew_StripSlashes($_GET["StudentID"]);
		}

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

		// Setup export options
		$this->SetupExportOptions();
		$this->StudentID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->LevelID->SetVisibility();
		$this->KampusID->SetVisibility();
		$this->ProdiID->SetVisibility();
		$this->StudentStatusID->SetVisibility();
		$this->TahunID->SetVisibility();
		$this->Foto->SetVisibility();
		$this->NIK->SetVisibility();
		$this->WargaNegara->SetVisibility();
		$this->Kelamin->SetVisibility();
		$this->TempatLahir->SetVisibility();
		$this->TanggalLahir->SetVisibility();
		$this->AgamaID->SetVisibility();
		$this->Darah->SetVisibility();
		$this->StatusSipil->SetVisibility();
		$this->AlamatDomisili->SetVisibility();
		$this->RT->SetVisibility();
		$this->RW->SetVisibility();
		$this->KodePos->SetVisibility();
		$this->ProvinsiID->SetVisibility();
		$this->KabupatenKotaID->SetVisibility();
		$this->KecamatanID->SetVisibility();
		$this->DesaID->SetVisibility();
		$this->AnakKe->SetVisibility();
		$this->JumlahSaudara->SetVisibility();
		$this->Telepon->SetVisibility();
		$this->_Email->SetVisibility();
		$this->NamaAyah->SetVisibility();
		$this->AgamaAyah->SetVisibility();
		$this->PendidikanAyah->SetVisibility();
		$this->PekerjaanAyah->SetVisibility();
		$this->HidupAyah->SetVisibility();
		$this->NamaIbu->SetVisibility();
		$this->AgamaIbu->SetVisibility();
		$this->PendidikanIbu->SetVisibility();
		$this->PekerjaanIbu->SetVisibility();
		$this->HidupIbu->SetVisibility();
		$this->AlamatOrtu->SetVisibility();
		$this->RTOrtu->SetVisibility();
		$this->RWOrtu->SetVisibility();
		$this->KodePosOrtu->SetVisibility();
		$this->ProvinsiIDOrtu->SetVisibility();
		$this->KabupatenIDOrtu->SetVisibility();
		$this->KecamatanIDOrtu->SetVisibility();
		$this->DesaIDOrtu->SetVisibility();
		$this->NegaraIDOrtu->SetVisibility();
		$this->TeleponOrtu->SetVisibility();
		$this->HandphoneOrtu->SetVisibility();
		$this->EmailOrtu->SetVisibility();
		$this->AsalSekolah->SetVisibility();
		$this->AlamatSekolah->SetVisibility();
		$this->ProvinsiIDSekolah->SetVisibility();
		$this->KabupatenIDSekolah->SetVisibility();
		$this->KecamatanIDSekolah->SetVisibility();
		$this->DesaIDSekolah->SetVisibility();
		$this->NilaiSekolah->SetVisibility();
		$this->TahunLulus->SetVisibility();
		$this->IjazahSekolah->SetVisibility();
		$this->TglIjazah->SetVisibility();
		$this->NA->SetVisibility();

		// Set up multi page object
		$this->SetupMultiPages();

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
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 1;
	var $DbMasterFilter;
	var $DbDetailFilter;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $RecCnt;
	var $RecKey = array();
	var $IsModal = FALSE;
	var $Recordset;
	var $MultiPages; // Multi pages object

	//
	// Page main
	//
	function Page_Main() {
		global $Language;
		global $gbSkipHeaderFooter;

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;

		// Load current record
		$bLoadCurrentRecord = FALSE;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET["StudentID"] <> "") {
				$this->StudentID->setQueryStringValue($_GET["StudentID"]);
				$this->RecKey["StudentID"] = $this->StudentID->QueryStringValue;
			} elseif (@$_POST["StudentID"] <> "") {
				$this->StudentID->setFormValue($_POST["StudentID"]);
				$this->RecKey["StudentID"] = $this->StudentID->FormValue;
			} else {
				$sReturnUrl = "studentlist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "studentlist.php"; // No matching record, return to list
					}
			}

			// Export data only
			if ($this->CustomExport == "" && in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
				$this->ExportData();
				$this->Page_Terminate(); // Terminate response
				exit();
			}
		} else {
			$sReturnUrl = "studentlist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = &$options["action"];

		// Add
		$item = &$option->Add("add");
		$addcaption = ew_HtmlTitle($Language->Phrase("ViewPageAddLink"));
		if ($this->IsModal) // Modal
			$item->Body = "<a class=\"ewAction ewAdd\" title=\"" . $addcaption . "\" data-caption=\"" . $addcaption . "\" href=\"javascript:void(0);\" onclick=\"ew_ModalDialogShow({lnk:this,url:'" . ew_HtmlEncode($this->AddUrl) . "',caption:'" . $addcaption . "'});\">" . $Language->Phrase("ViewPageAddLink") . "</a>";
		else
			$item->Body = "<a class=\"ewAction ewAdd\" title=\"" . $addcaption . "\" data-caption=\"" . $addcaption . "\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("ViewPageAddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());

		// Edit
		$item = &$option->Add("edit");
		$editcaption = ew_HtmlTitle($Language->Phrase("ViewPageEditLink"));
		if ($this->IsModal) // Modal
			$item->Body = "<a class=\"ewAction ewEdit\" title=\"" . $editcaption . "\" data-caption=\"" . $editcaption . "\" href=\"javascript:void(0);\" onclick=\"ew_ModalDialogShow({lnk:this,url:'" . ew_HtmlEncode($this->EditUrl) . "',caption:'" . $editcaption . "'});\">" . $Language->Phrase("ViewPageEditLink") . "</a>";
		else
			$item->Body = "<a class=\"ewAction ewEdit\" title=\"" . $editcaption . "\" data-caption=\"" . $editcaption . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("ViewPageEditLink") . "</a>";
		$item->Visible = ($this->EditUrl <> "" && $Security->CanEdit());

		// Delete
		$item = &$option->Add("delete");
		if ($this->IsModal) // Handle as inline delete
			$item->Body = "<a onclick=\"return ew_ConfirmDelete(this);\" class=\"ewAction ewDelete\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageDeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageDeleteLink")) . "\" href=\"" . ew_HtmlEncode(ew_AddQueryStringToUrl($this->DeleteUrl, "a_delete=1")) . "\">" . $Language->Phrase("ViewPageDeleteLink") . "</a>";
		else
			$item->Body = "<a class=\"ewAction ewDelete\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageDeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageDeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("ViewPageDeleteLink") . "</a>";
		$item->Visible = ($this->DeleteUrl <> "" && $Security->CanDelete());

		// Set up action default
		$option = &$options["action"];
		$option->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
		$option->UseImageAndText = TRUE;
		$option->UseDropDownButton = FALSE;
		$option->UseButtonGroup = TRUE;
		$item = &$option->Add($option->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
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
		if ($this->AuditTrailOnView) $this->WriteAuditTrailOnView($row);
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

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		$this->AddUrl = $this->GetAddUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();
		$this->ListUrl = $this->GetListUrl();
		$this->SetupOtherOptions();

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

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// LevelID
			$this->LevelID->LinkCustomAttributes = "";
			$this->LevelID->HrefValue = "";
			$this->LevelID->TooltipValue = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";
			$this->KampusID->TooltipValue = "";

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
				$this->Foto->LinkAttrs["data-rel"] = "student_x_Foto";
				ew_AppendClass($this->Foto->LinkAttrs["class"], "ewLightbox");
			}

			// NIK
			$this->NIK->LinkCustomAttributes = "";
			$this->NIK->HrefValue = "";
			$this->NIK->TooltipValue = "";

			// WargaNegara
			$this->WargaNegara->LinkCustomAttributes = "";
			$this->WargaNegara->HrefValue = "";
			$this->WargaNegara->TooltipValue = "";

			// Kelamin
			$this->Kelamin->LinkCustomAttributes = "";
			$this->Kelamin->HrefValue = "";
			$this->Kelamin->TooltipValue = "";

			// TempatLahir
			$this->TempatLahir->LinkCustomAttributes = "";
			$this->TempatLahir->HrefValue = "";
			$this->TempatLahir->TooltipValue = "";

			// TanggalLahir
			$this->TanggalLahir->LinkCustomAttributes = "";
			$this->TanggalLahir->HrefValue = "";
			$this->TanggalLahir->TooltipValue = "";

			// AgamaID
			$this->AgamaID->LinkCustomAttributes = "";
			$this->AgamaID->HrefValue = "";
			$this->AgamaID->TooltipValue = "";

			// Darah
			$this->Darah->LinkCustomAttributes = "";
			$this->Darah->HrefValue = "";
			$this->Darah->TooltipValue = "";

			// StatusSipil
			$this->StatusSipil->LinkCustomAttributes = "";
			$this->StatusSipil->HrefValue = "";
			$this->StatusSipil->TooltipValue = "";

			// AlamatDomisili
			$this->AlamatDomisili->LinkCustomAttributes = "";
			$this->AlamatDomisili->HrefValue = "";
			$this->AlamatDomisili->TooltipValue = "";

			// RT
			$this->RT->LinkCustomAttributes = "";
			$this->RT->HrefValue = "";
			$this->RT->TooltipValue = "";

			// RW
			$this->RW->LinkCustomAttributes = "";
			$this->RW->HrefValue = "";
			$this->RW->TooltipValue = "";

			// KodePos
			$this->KodePos->LinkCustomAttributes = "";
			$this->KodePos->HrefValue = "";
			$this->KodePos->TooltipValue = "";

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

			// AnakKe
			$this->AnakKe->LinkCustomAttributes = "";
			$this->AnakKe->HrefValue = "";
			$this->AnakKe->TooltipValue = "";

			// JumlahSaudara
			$this->JumlahSaudara->LinkCustomAttributes = "";
			$this->JumlahSaudara->HrefValue = "";
			$this->JumlahSaudara->TooltipValue = "";

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";
			$this->Telepon->TooltipValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";

			// NamaAyah
			$this->NamaAyah->LinkCustomAttributes = "";
			$this->NamaAyah->HrefValue = "";
			$this->NamaAyah->TooltipValue = "";

			// AgamaAyah
			$this->AgamaAyah->LinkCustomAttributes = "";
			$this->AgamaAyah->HrefValue = "";
			$this->AgamaAyah->TooltipValue = "";

			// PendidikanAyah
			$this->PendidikanAyah->LinkCustomAttributes = "";
			$this->PendidikanAyah->HrefValue = "";
			$this->PendidikanAyah->TooltipValue = "";

			// PekerjaanAyah
			$this->PekerjaanAyah->LinkCustomAttributes = "";
			$this->PekerjaanAyah->HrefValue = "";
			$this->PekerjaanAyah->TooltipValue = "";

			// HidupAyah
			$this->HidupAyah->LinkCustomAttributes = "";
			$this->HidupAyah->HrefValue = "";
			$this->HidupAyah->TooltipValue = "";

			// NamaIbu
			$this->NamaIbu->LinkCustomAttributes = "";
			$this->NamaIbu->HrefValue = "";
			$this->NamaIbu->TooltipValue = "";

			// AgamaIbu
			$this->AgamaIbu->LinkCustomAttributes = "";
			$this->AgamaIbu->HrefValue = "";
			$this->AgamaIbu->TooltipValue = "";

			// PendidikanIbu
			$this->PendidikanIbu->LinkCustomAttributes = "";
			$this->PendidikanIbu->HrefValue = "";
			$this->PendidikanIbu->TooltipValue = "";

			// PekerjaanIbu
			$this->PekerjaanIbu->LinkCustomAttributes = "";
			$this->PekerjaanIbu->HrefValue = "";
			$this->PekerjaanIbu->TooltipValue = "";

			// HidupIbu
			$this->HidupIbu->LinkCustomAttributes = "";
			$this->HidupIbu->HrefValue = "";
			$this->HidupIbu->TooltipValue = "";

			// AlamatOrtu
			$this->AlamatOrtu->LinkCustomAttributes = "";
			$this->AlamatOrtu->HrefValue = "";
			$this->AlamatOrtu->TooltipValue = "";

			// RTOrtu
			$this->RTOrtu->LinkCustomAttributes = "";
			$this->RTOrtu->HrefValue = "";
			$this->RTOrtu->TooltipValue = "";

			// RWOrtu
			$this->RWOrtu->LinkCustomAttributes = "";
			$this->RWOrtu->HrefValue = "";
			$this->RWOrtu->TooltipValue = "";

			// KodePosOrtu
			$this->KodePosOrtu->LinkCustomAttributes = "";
			$this->KodePosOrtu->HrefValue = "";
			$this->KodePosOrtu->TooltipValue = "";

			// ProvinsiIDOrtu
			$this->ProvinsiIDOrtu->LinkCustomAttributes = "";
			$this->ProvinsiIDOrtu->HrefValue = "";
			$this->ProvinsiIDOrtu->TooltipValue = "";

			// KabupatenIDOrtu
			$this->KabupatenIDOrtu->LinkCustomAttributes = "";
			$this->KabupatenIDOrtu->HrefValue = "";
			$this->KabupatenIDOrtu->TooltipValue = "";

			// KecamatanIDOrtu
			$this->KecamatanIDOrtu->LinkCustomAttributes = "";
			$this->KecamatanIDOrtu->HrefValue = "";
			$this->KecamatanIDOrtu->TooltipValue = "";

			// DesaIDOrtu
			$this->DesaIDOrtu->LinkCustomAttributes = "";
			$this->DesaIDOrtu->HrefValue = "";
			$this->DesaIDOrtu->TooltipValue = "";

			// NegaraIDOrtu
			$this->NegaraIDOrtu->LinkCustomAttributes = "";
			$this->NegaraIDOrtu->HrefValue = "";
			$this->NegaraIDOrtu->TooltipValue = "";

			// TeleponOrtu
			$this->TeleponOrtu->LinkCustomAttributes = "";
			$this->TeleponOrtu->HrefValue = "";
			$this->TeleponOrtu->TooltipValue = "";

			// HandphoneOrtu
			$this->HandphoneOrtu->LinkCustomAttributes = "";
			$this->HandphoneOrtu->HrefValue = "";
			$this->HandphoneOrtu->TooltipValue = "";

			// EmailOrtu
			$this->EmailOrtu->LinkCustomAttributes = "";
			$this->EmailOrtu->HrefValue = "";
			$this->EmailOrtu->TooltipValue = "";

			// AsalSekolah
			$this->AsalSekolah->LinkCustomAttributes = "";
			$this->AsalSekolah->HrefValue = "";
			$this->AsalSekolah->TooltipValue = "";

			// AlamatSekolah
			$this->AlamatSekolah->LinkCustomAttributes = "";
			$this->AlamatSekolah->HrefValue = "";
			$this->AlamatSekolah->TooltipValue = "";

			// ProvinsiIDSekolah
			$this->ProvinsiIDSekolah->LinkCustomAttributes = "";
			$this->ProvinsiIDSekolah->HrefValue = "";
			$this->ProvinsiIDSekolah->TooltipValue = "";

			// KabupatenIDSekolah
			$this->KabupatenIDSekolah->LinkCustomAttributes = "";
			$this->KabupatenIDSekolah->HrefValue = "";
			$this->KabupatenIDSekolah->TooltipValue = "";

			// KecamatanIDSekolah
			$this->KecamatanIDSekolah->LinkCustomAttributes = "";
			$this->KecamatanIDSekolah->HrefValue = "";
			$this->KecamatanIDSekolah->TooltipValue = "";

			// DesaIDSekolah
			$this->DesaIDSekolah->LinkCustomAttributes = "";
			$this->DesaIDSekolah->HrefValue = "";
			$this->DesaIDSekolah->TooltipValue = "";

			// NilaiSekolah
			$this->NilaiSekolah->LinkCustomAttributes = "";
			$this->NilaiSekolah->HrefValue = "";
			$this->NilaiSekolah->TooltipValue = "";

			// TahunLulus
			$this->TahunLulus->LinkCustomAttributes = "";
			$this->TahunLulus->HrefValue = "";
			$this->TahunLulus->TooltipValue = "";

			// IjazahSekolah
			$this->IjazahSekolah->LinkCustomAttributes = "";
			$this->IjazahSekolah->HrefValue = "";
			$this->IjazahSekolah->TooltipValue = "";

			// TglIjazah
			$this->TglIjazah->LinkCustomAttributes = "";
			$this->TglIjazah->HrefValue = "";
			$this->TglIjazah->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
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
		$item->Body = "<button id=\"emf_student\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_student',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fstudentview,key:" . ew_ArrayToJsonAttr($this->RecKey) . ",sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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

		// Hide options for export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = FALSE;

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
		$this->SetUpStartRec(); // Set up start record position

		// Set the last record to display
		if ($this->DisplayRecs <= 0) {
			$this->StopRec = $this->TotalRecs;
		} else {
			$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
		}
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$this->ExportDoc = ew_ExportDocument($this, "v");
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
		$this->ExportDocument($Doc, $rs, $this->StartRec, $this->StopRec, "view");
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("studentlist.php"), "", $this->TableVar, TRUE);
		$PageId = "view";
		$Breadcrumb->Add("view", $PageId, $url);
	}

	// Set up multi pages
	function SetupMultiPages() {
		$pages = new cSubPages();
		$pages->Style = "tabs";
		$pages->Add(0);
		$pages->Add(1);
		$pages->Add(2);
		$pages->Add(3);
		$pages->Add(4);
		$pages->Add(5);
		$this->MultiPages = $pages;
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
if (!isset($student_view)) $student_view = new cstudent_view();

// Page init
$student_view->Page_Init();

// Page main
$student_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$student_view->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($student->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "view";
var CurrentForm = fstudentview = new ew_Form("fstudentview", "view");

// Form_CustomValidate event
fstudentview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fstudentview.ValidateRequired = true;
<?php } else { ?>
fstudentview.ValidateRequired = false; 
<?php } ?>

// Multi-Page
fstudentview.MultiPage = new ew_MultiPage("fstudentview");

// Dynamic selection lists
fstudentview.Lists["x_KampusID"] = {"LinkField":"x_KampusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kampus"};
fstudentview.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fstudentview.Lists["x_StudentStatusID"] = {"LinkField":"x_StatusStudentID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statusstudent"};
fstudentview.Lists["x_WargaNegara"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fstudentview.Lists["x_WargaNegara"].Options = <?php echo json_encode($student->WargaNegara->Options()) ?>;
fstudentview.Lists["x_Kelamin"] = {"LinkField":"x_Kelamin","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kelamin"};
fstudentview.Lists["x_AgamaID"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fstudentview.Lists["x_Darah"] = {"LinkField":"x_DarahID","Ajax":true,"AutoFill":false,"DisplayFields":["x_DarahID","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_darah"};
fstudentview.Lists["x_StatusSipil"] = {"LinkField":"x_StatusSipil","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statussipil"};
fstudentview.Lists["x_ProvinsiID"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenKotaID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fstudentview.Lists["x_KabupatenKotaID"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":[],"ChildFields":["x_KecamatanID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstudentview.Lists["x_KecamatanID"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":[],"ChildFields":["x_DesaID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fstudentview.Lists["x_DesaID"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fstudentview.Lists["x_AgamaAyah"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fstudentview.Lists["x_PendidikanAyah"] = {"LinkField":"x_Pendidikan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pendidikanortu"};
fstudentview.Lists["x_PekerjaanAyah"] = {"LinkField":"x_Pekerjaan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pekerjaanortu"};
fstudentview.Lists["x_HidupAyah"] = {"LinkField":"x_Hidup","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_hidup"};
fstudentview.Lists["x_AgamaIbu"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fstudentview.Lists["x_PendidikanIbu"] = {"LinkField":"x_Pendidikan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pendidikanortu"};
fstudentview.Lists["x_PekerjaanIbu"] = {"LinkField":"x_Pekerjaan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pekerjaanortu"};
fstudentview.Lists["x_HidupIbu"] = {"LinkField":"x_Hidup","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_hidup"};
fstudentview.Lists["x_ProvinsiIDOrtu"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenIDOrtu"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fstudentview.Lists["x_KabupatenIDOrtu"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":[],"ChildFields":["x_KecamatanIDOrtu"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstudentview.Lists["x_KecamatanIDOrtu"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":[],"ChildFields":["x_DesaIDOrtu"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fstudentview.Lists["x_DesaIDOrtu"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fstudentview.Lists["x_NegaraIDOrtu"] = {"LinkField":"x_NegaraID","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaNegara","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_negara"};
fstudentview.Lists["x_ProvinsiIDSekolah"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenIDSekolah"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fstudentview.Lists["x_KabupatenIDSekolah"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":[],"ChildFields":["x_KecamatanIDSekolah"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstudentview.Lists["x_KecamatanIDSekolah"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":[],"ChildFields":["x_DesaIDSekolah"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fstudentview.Lists["x_DesaIDSekolah"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fstudentview.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fstudentview.Lists["x_NA"].Options = <?php echo json_encode($student->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($student->Export == "") { ?>
<div class="ewToolbar">
<?php if (!$student_view->IsModal) { ?>
<?php if ($student->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php } ?>
<?php $student_view->ExportOptions->Render("body") ?>
<?php
	foreach ($student_view->OtherOptions as &$option)
		$option->Render("body");
?>
<?php if (!$student_view->IsModal) { ?>
<?php if ($student->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $student_view->ShowPageHeader(); ?>
<?php
$student_view->ShowMessage();
?>
<form name="fstudentview" id="fstudentview" class="form-inline ewForm ewViewForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($student_view->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $student_view->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="student">
<?php if ($student_view->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if ($student->Export == "") { ?>
<div class="ewMultiPage">
<div class="tabbable" id="student_view">
	<ul class="nav<?php echo $student_view->MultiPages->NavStyle() ?>">
		<li<?php echo $student_view->MultiPages->TabStyle("1") ?>><a href="#tab_student1" data-toggle="tab"><?php echo $student->PageCaption(1) ?></a></li>
		<li<?php echo $student_view->MultiPages->TabStyle("2") ?>><a href="#tab_student2" data-toggle="tab"><?php echo $student->PageCaption(2) ?></a></li>
		<li<?php echo $student_view->MultiPages->TabStyle("3") ?>><a href="#tab_student3" data-toggle="tab"><?php echo $student->PageCaption(3) ?></a></li>
		<li<?php echo $student_view->MultiPages->TabStyle("4") ?>><a href="#tab_student4" data-toggle="tab"><?php echo $student->PageCaption(4) ?></a></li>
		<li<?php echo $student_view->MultiPages->TabStyle("5") ?>><a href="#tab_student5" data-toggle="tab"><?php echo $student->PageCaption(5) ?></a></li>
	</ul>
	<div class="tab-content">
<?php } ?>
<?php if ($student->Export == "") { ?>
		<div class="tab-pane<?php echo $student_view->MultiPages->PageStyle("1") ?>" id="tab_student1">
<?php } ?>
<table class="table table-bordered table-striped ewViewTable">
<?php if ($student->StudentID->Visible) { // StudentID ?>
	<tr id="r_StudentID">
		<td><span id="elh_student_StudentID"><?php echo $student->StudentID->FldCaption() ?></span></td>
		<td data-name="StudentID"<?php echo $student->StudentID->CellAttributes() ?>>
<span id="el_student_StudentID" data-page="1">
<span<?php echo $student->StudentID->ViewAttributes() ?>>
<?php echo $student->StudentID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->Nama->Visible) { // Nama ?>
	<tr id="r_Nama">
		<td><span id="elh_student_Nama"><?php echo $student->Nama->FldCaption() ?></span></td>
		<td data-name="Nama"<?php echo $student->Nama->CellAttributes() ?>>
<span id="el_student_Nama" data-page="1">
<span<?php echo $student->Nama->ViewAttributes() ?>>
<?php echo $student->Nama->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->LevelID->Visible) { // LevelID ?>
	<tr id="r_LevelID">
		<td><span id="elh_student_LevelID"><?php echo $student->LevelID->FldCaption() ?></span></td>
		<td data-name="LevelID"<?php echo $student->LevelID->CellAttributes() ?>>
<span id="el_student_LevelID" data-page="1">
<span<?php echo $student->LevelID->ViewAttributes() ?>>
<?php echo $student->LevelID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->KampusID->Visible) { // KampusID ?>
	<tr id="r_KampusID">
		<td><span id="elh_student_KampusID"><?php echo $student->KampusID->FldCaption() ?></span></td>
		<td data-name="KampusID"<?php echo $student->KampusID->CellAttributes() ?>>
<span id="el_student_KampusID" data-page="1">
<span<?php echo $student->KampusID->ViewAttributes() ?>>
<?php echo $student->KampusID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->ProdiID->Visible) { // ProdiID ?>
	<tr id="r_ProdiID">
		<td><span id="elh_student_ProdiID"><?php echo $student->ProdiID->FldCaption() ?></span></td>
		<td data-name="ProdiID"<?php echo $student->ProdiID->CellAttributes() ?>>
<span id="el_student_ProdiID" data-page="1">
<span<?php echo $student->ProdiID->ViewAttributes() ?>>
<?php echo $student->ProdiID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->StudentStatusID->Visible) { // StudentStatusID ?>
	<tr id="r_StudentStatusID">
		<td><span id="elh_student_StudentStatusID"><?php echo $student->StudentStatusID->FldCaption() ?></span></td>
		<td data-name="StudentStatusID"<?php echo $student->StudentStatusID->CellAttributes() ?>>
<span id="el_student_StudentStatusID" data-page="1">
<span<?php echo $student->StudentStatusID->ViewAttributes() ?>>
<?php echo $student->StudentStatusID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->TahunID->Visible) { // TahunID ?>
	<tr id="r_TahunID">
		<td><span id="elh_student_TahunID"><?php echo $student->TahunID->FldCaption() ?></span></td>
		<td data-name="TahunID"<?php echo $student->TahunID->CellAttributes() ?>>
<span id="el_student_TahunID" data-page="1">
<span<?php echo $student->TahunID->ViewAttributes() ?>>
<?php echo $student->TahunID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->Foto->Visible) { // Foto ?>
	<tr id="r_Foto">
		<td><span id="elh_student_Foto"><?php echo $student->Foto->FldCaption() ?></span></td>
		<td data-name="Foto"<?php echo $student->Foto->CellAttributes() ?>>
<span id="el_student_Foto" data-page="1">
<span>
<?php echo ew_GetFileViewTag($student->Foto, $student->Foto->ViewValue) ?>
</span>
</span>
</td>
	</tr>
<?php } ?>
</table>
<?php if ($student->Export == "") { ?>
		</div>
<?php } ?>
<?php if ($student->Export == "") { ?>
		<div class="tab-pane<?php echo $student_view->MultiPages->PageStyle("2") ?>" id="tab_student2">
<?php } ?>
<table class="table table-bordered table-striped ewViewTable">
<?php if ($student->NIK->Visible) { // NIK ?>
	<tr id="r_NIK">
		<td><span id="elh_student_NIK"><?php echo $student->NIK->FldCaption() ?></span></td>
		<td data-name="NIK"<?php echo $student->NIK->CellAttributes() ?>>
<span id="el_student_NIK" data-page="2">
<span<?php echo $student->NIK->ViewAttributes() ?>>
<?php echo $student->NIK->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->WargaNegara->Visible) { // WargaNegara ?>
	<tr id="r_WargaNegara">
		<td><span id="elh_student_WargaNegara"><?php echo $student->WargaNegara->FldCaption() ?></span></td>
		<td data-name="WargaNegara"<?php echo $student->WargaNegara->CellAttributes() ?>>
<span id="el_student_WargaNegara" data-page="2">
<span<?php echo $student->WargaNegara->ViewAttributes() ?>>
<?php echo $student->WargaNegara->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->Kelamin->Visible) { // Kelamin ?>
	<tr id="r_Kelamin">
		<td><span id="elh_student_Kelamin"><?php echo $student->Kelamin->FldCaption() ?></span></td>
		<td data-name="Kelamin"<?php echo $student->Kelamin->CellAttributes() ?>>
<span id="el_student_Kelamin" data-page="2">
<span<?php echo $student->Kelamin->ViewAttributes() ?>>
<?php echo $student->Kelamin->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->TempatLahir->Visible) { // TempatLahir ?>
	<tr id="r_TempatLahir">
		<td><span id="elh_student_TempatLahir"><?php echo $student->TempatLahir->FldCaption() ?></span></td>
		<td data-name="TempatLahir"<?php echo $student->TempatLahir->CellAttributes() ?>>
<span id="el_student_TempatLahir" data-page="2">
<span<?php echo $student->TempatLahir->ViewAttributes() ?>>
<?php echo $student->TempatLahir->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->TanggalLahir->Visible) { // TanggalLahir ?>
	<tr id="r_TanggalLahir">
		<td><span id="elh_student_TanggalLahir"><?php echo $student->TanggalLahir->FldCaption() ?></span></td>
		<td data-name="TanggalLahir"<?php echo $student->TanggalLahir->CellAttributes() ?>>
<span id="el_student_TanggalLahir" data-page="2">
<span<?php echo $student->TanggalLahir->ViewAttributes() ?>>
<?php echo $student->TanggalLahir->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->AgamaID->Visible) { // AgamaID ?>
	<tr id="r_AgamaID">
		<td><span id="elh_student_AgamaID"><?php echo $student->AgamaID->FldCaption() ?></span></td>
		<td data-name="AgamaID"<?php echo $student->AgamaID->CellAttributes() ?>>
<span id="el_student_AgamaID" data-page="2">
<span<?php echo $student->AgamaID->ViewAttributes() ?>>
<?php echo $student->AgamaID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->Darah->Visible) { // Darah ?>
	<tr id="r_Darah">
		<td><span id="elh_student_Darah"><?php echo $student->Darah->FldCaption() ?></span></td>
		<td data-name="Darah"<?php echo $student->Darah->CellAttributes() ?>>
<span id="el_student_Darah" data-page="2">
<span<?php echo $student->Darah->ViewAttributes() ?>>
<?php echo $student->Darah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->StatusSipil->Visible) { // StatusSipil ?>
	<tr id="r_StatusSipil">
		<td><span id="elh_student_StatusSipil"><?php echo $student->StatusSipil->FldCaption() ?></span></td>
		<td data-name="StatusSipil"<?php echo $student->StatusSipil->CellAttributes() ?>>
<span id="el_student_StatusSipil" data-page="2">
<span<?php echo $student->StatusSipil->ViewAttributes() ?>>
<?php echo $student->StatusSipil->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->AlamatDomisili->Visible) { // AlamatDomisili ?>
	<tr id="r_AlamatDomisili">
		<td><span id="elh_student_AlamatDomisili"><?php echo $student->AlamatDomisili->FldCaption() ?></span></td>
		<td data-name="AlamatDomisili"<?php echo $student->AlamatDomisili->CellAttributes() ?>>
<span id="el_student_AlamatDomisili" data-page="2">
<span<?php echo $student->AlamatDomisili->ViewAttributes() ?>>
<?php echo $student->AlamatDomisili->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->RT->Visible) { // RT ?>
	<tr id="r_RT">
		<td><span id="elh_student_RT"><?php echo $student->RT->FldCaption() ?></span></td>
		<td data-name="RT"<?php echo $student->RT->CellAttributes() ?>>
<span id="el_student_RT" data-page="2">
<span<?php echo $student->RT->ViewAttributes() ?>>
<?php echo $student->RT->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->RW->Visible) { // RW ?>
	<tr id="r_RW">
		<td><span id="elh_student_RW"><?php echo $student->RW->FldCaption() ?></span></td>
		<td data-name="RW"<?php echo $student->RW->CellAttributes() ?>>
<span id="el_student_RW" data-page="2">
<span<?php echo $student->RW->ViewAttributes() ?>>
<?php echo $student->RW->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->KodePos->Visible) { // KodePos ?>
	<tr id="r_KodePos">
		<td><span id="elh_student_KodePos"><?php echo $student->KodePos->FldCaption() ?></span></td>
		<td data-name="KodePos"<?php echo $student->KodePos->CellAttributes() ?>>
<span id="el_student_KodePos" data-page="2">
<span<?php echo $student->KodePos->ViewAttributes() ?>>
<?php echo $student->KodePos->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->ProvinsiID->Visible) { // ProvinsiID ?>
	<tr id="r_ProvinsiID">
		<td><span id="elh_student_ProvinsiID"><?php echo $student->ProvinsiID->FldCaption() ?></span></td>
		<td data-name="ProvinsiID"<?php echo $student->ProvinsiID->CellAttributes() ?>>
<span id="el_student_ProvinsiID" data-page="2">
<span<?php echo $student->ProvinsiID->ViewAttributes() ?>>
<?php echo $student->ProvinsiID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->KabupatenKotaID->Visible) { // KabupatenKotaID ?>
	<tr id="r_KabupatenKotaID">
		<td><span id="elh_student_KabupatenKotaID"><?php echo $student->KabupatenKotaID->FldCaption() ?></span></td>
		<td data-name="KabupatenKotaID"<?php echo $student->KabupatenKotaID->CellAttributes() ?>>
<span id="el_student_KabupatenKotaID" data-page="2">
<span<?php echo $student->KabupatenKotaID->ViewAttributes() ?>>
<?php echo $student->KabupatenKotaID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->KecamatanID->Visible) { // KecamatanID ?>
	<tr id="r_KecamatanID">
		<td><span id="elh_student_KecamatanID"><?php echo $student->KecamatanID->FldCaption() ?></span></td>
		<td data-name="KecamatanID"<?php echo $student->KecamatanID->CellAttributes() ?>>
<span id="el_student_KecamatanID" data-page="2">
<span<?php echo $student->KecamatanID->ViewAttributes() ?>>
<?php echo $student->KecamatanID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->DesaID->Visible) { // DesaID ?>
	<tr id="r_DesaID">
		<td><span id="elh_student_DesaID"><?php echo $student->DesaID->FldCaption() ?></span></td>
		<td data-name="DesaID"<?php echo $student->DesaID->CellAttributes() ?>>
<span id="el_student_DesaID" data-page="2">
<span<?php echo $student->DesaID->ViewAttributes() ?>>
<?php echo $student->DesaID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->AnakKe->Visible) { // AnakKe ?>
	<tr id="r_AnakKe">
		<td><span id="elh_student_AnakKe"><?php echo $student->AnakKe->FldCaption() ?></span></td>
		<td data-name="AnakKe"<?php echo $student->AnakKe->CellAttributes() ?>>
<span id="el_student_AnakKe" data-page="2">
<span<?php echo $student->AnakKe->ViewAttributes() ?>>
<?php echo $student->AnakKe->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->JumlahSaudara->Visible) { // JumlahSaudara ?>
	<tr id="r_JumlahSaudara">
		<td><span id="elh_student_JumlahSaudara"><?php echo $student->JumlahSaudara->FldCaption() ?></span></td>
		<td data-name="JumlahSaudara"<?php echo $student->JumlahSaudara->CellAttributes() ?>>
<span id="el_student_JumlahSaudara" data-page="2">
<span<?php echo $student->JumlahSaudara->ViewAttributes() ?>>
<?php echo $student->JumlahSaudara->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->Telepon->Visible) { // Telepon ?>
	<tr id="r_Telepon">
		<td><span id="elh_student_Telepon"><?php echo $student->Telepon->FldCaption() ?></span></td>
		<td data-name="Telepon"<?php echo $student->Telepon->CellAttributes() ?>>
<span id="el_student_Telepon" data-page="2">
<span<?php echo $student->Telepon->ViewAttributes() ?>>
<?php echo $student->Telepon->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->_Email->Visible) { // Email ?>
	<tr id="r__Email">
		<td><span id="elh_student__Email"><?php echo $student->_Email->FldCaption() ?></span></td>
		<td data-name="_Email"<?php echo $student->_Email->CellAttributes() ?>>
<span id="el_student__Email" data-page="2">
<span<?php echo $student->_Email->ViewAttributes() ?>>
<?php echo $student->_Email->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
<?php if ($student->Export == "") { ?>
		</div>
<?php } ?>
<?php if ($student->Export == "") { ?>
		<div class="tab-pane<?php echo $student_view->MultiPages->PageStyle("3") ?>" id="tab_student3">
<?php } ?>
<table class="table table-bordered table-striped ewViewTable">
<?php if ($student->NamaAyah->Visible) { // NamaAyah ?>
	<tr id="r_NamaAyah">
		<td><span id="elh_student_NamaAyah"><?php echo $student->NamaAyah->FldCaption() ?></span></td>
		<td data-name="NamaAyah"<?php echo $student->NamaAyah->CellAttributes() ?>>
<span id="el_student_NamaAyah" data-page="3">
<span<?php echo $student->NamaAyah->ViewAttributes() ?>>
<?php echo $student->NamaAyah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->AgamaAyah->Visible) { // AgamaAyah ?>
	<tr id="r_AgamaAyah">
		<td><span id="elh_student_AgamaAyah"><?php echo $student->AgamaAyah->FldCaption() ?></span></td>
		<td data-name="AgamaAyah"<?php echo $student->AgamaAyah->CellAttributes() ?>>
<span id="el_student_AgamaAyah" data-page="3">
<span<?php echo $student->AgamaAyah->ViewAttributes() ?>>
<?php echo $student->AgamaAyah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->PendidikanAyah->Visible) { // PendidikanAyah ?>
	<tr id="r_PendidikanAyah">
		<td><span id="elh_student_PendidikanAyah"><?php echo $student->PendidikanAyah->FldCaption() ?></span></td>
		<td data-name="PendidikanAyah"<?php echo $student->PendidikanAyah->CellAttributes() ?>>
<span id="el_student_PendidikanAyah" data-page="3">
<span<?php echo $student->PendidikanAyah->ViewAttributes() ?>>
<?php echo $student->PendidikanAyah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->PekerjaanAyah->Visible) { // PekerjaanAyah ?>
	<tr id="r_PekerjaanAyah">
		<td><span id="elh_student_PekerjaanAyah"><?php echo $student->PekerjaanAyah->FldCaption() ?></span></td>
		<td data-name="PekerjaanAyah"<?php echo $student->PekerjaanAyah->CellAttributes() ?>>
<span id="el_student_PekerjaanAyah" data-page="3">
<span<?php echo $student->PekerjaanAyah->ViewAttributes() ?>>
<?php echo $student->PekerjaanAyah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->HidupAyah->Visible) { // HidupAyah ?>
	<tr id="r_HidupAyah">
		<td><span id="elh_student_HidupAyah"><?php echo $student->HidupAyah->FldCaption() ?></span></td>
		<td data-name="HidupAyah"<?php echo $student->HidupAyah->CellAttributes() ?>>
<span id="el_student_HidupAyah" data-page="3">
<span<?php echo $student->HidupAyah->ViewAttributes() ?>>
<?php echo $student->HidupAyah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->NamaIbu->Visible) { // NamaIbu ?>
	<tr id="r_NamaIbu">
		<td><span id="elh_student_NamaIbu"><?php echo $student->NamaIbu->FldCaption() ?></span></td>
		<td data-name="NamaIbu"<?php echo $student->NamaIbu->CellAttributes() ?>>
<span id="el_student_NamaIbu" data-page="3">
<span<?php echo $student->NamaIbu->ViewAttributes() ?>>
<?php echo $student->NamaIbu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->AgamaIbu->Visible) { // AgamaIbu ?>
	<tr id="r_AgamaIbu">
		<td><span id="elh_student_AgamaIbu"><?php echo $student->AgamaIbu->FldCaption() ?></span></td>
		<td data-name="AgamaIbu"<?php echo $student->AgamaIbu->CellAttributes() ?>>
<span id="el_student_AgamaIbu" data-page="3">
<span<?php echo $student->AgamaIbu->ViewAttributes() ?>>
<?php echo $student->AgamaIbu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->PendidikanIbu->Visible) { // PendidikanIbu ?>
	<tr id="r_PendidikanIbu">
		<td><span id="elh_student_PendidikanIbu"><?php echo $student->PendidikanIbu->FldCaption() ?></span></td>
		<td data-name="PendidikanIbu"<?php echo $student->PendidikanIbu->CellAttributes() ?>>
<span id="el_student_PendidikanIbu" data-page="3">
<span<?php echo $student->PendidikanIbu->ViewAttributes() ?>>
<?php echo $student->PendidikanIbu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->PekerjaanIbu->Visible) { // PekerjaanIbu ?>
	<tr id="r_PekerjaanIbu">
		<td><span id="elh_student_PekerjaanIbu"><?php echo $student->PekerjaanIbu->FldCaption() ?></span></td>
		<td data-name="PekerjaanIbu"<?php echo $student->PekerjaanIbu->CellAttributes() ?>>
<span id="el_student_PekerjaanIbu" data-page="3">
<span<?php echo $student->PekerjaanIbu->ViewAttributes() ?>>
<?php echo $student->PekerjaanIbu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->HidupIbu->Visible) { // HidupIbu ?>
	<tr id="r_HidupIbu">
		<td><span id="elh_student_HidupIbu"><?php echo $student->HidupIbu->FldCaption() ?></span></td>
		<td data-name="HidupIbu"<?php echo $student->HidupIbu->CellAttributes() ?>>
<span id="el_student_HidupIbu" data-page="3">
<span<?php echo $student->HidupIbu->ViewAttributes() ?>>
<?php echo $student->HidupIbu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->AlamatOrtu->Visible) { // AlamatOrtu ?>
	<tr id="r_AlamatOrtu">
		<td><span id="elh_student_AlamatOrtu"><?php echo $student->AlamatOrtu->FldCaption() ?></span></td>
		<td data-name="AlamatOrtu"<?php echo $student->AlamatOrtu->CellAttributes() ?>>
<span id="el_student_AlamatOrtu" data-page="3">
<span<?php echo $student->AlamatOrtu->ViewAttributes() ?>>
<?php echo $student->AlamatOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->RTOrtu->Visible) { // RTOrtu ?>
	<tr id="r_RTOrtu">
		<td><span id="elh_student_RTOrtu"><?php echo $student->RTOrtu->FldCaption() ?></span></td>
		<td data-name="RTOrtu"<?php echo $student->RTOrtu->CellAttributes() ?>>
<span id="el_student_RTOrtu" data-page="3">
<span<?php echo $student->RTOrtu->ViewAttributes() ?>>
<?php echo $student->RTOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->RWOrtu->Visible) { // RWOrtu ?>
	<tr id="r_RWOrtu">
		<td><span id="elh_student_RWOrtu"><?php echo $student->RWOrtu->FldCaption() ?></span></td>
		<td data-name="RWOrtu"<?php echo $student->RWOrtu->CellAttributes() ?>>
<span id="el_student_RWOrtu" data-page="3">
<span<?php echo $student->RWOrtu->ViewAttributes() ?>>
<?php echo $student->RWOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->KodePosOrtu->Visible) { // KodePosOrtu ?>
	<tr id="r_KodePosOrtu">
		<td><span id="elh_student_KodePosOrtu"><?php echo $student->KodePosOrtu->FldCaption() ?></span></td>
		<td data-name="KodePosOrtu"<?php echo $student->KodePosOrtu->CellAttributes() ?>>
<span id="el_student_KodePosOrtu" data-page="3">
<span<?php echo $student->KodePosOrtu->ViewAttributes() ?>>
<?php echo $student->KodePosOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->ProvinsiIDOrtu->Visible) { // ProvinsiIDOrtu ?>
	<tr id="r_ProvinsiIDOrtu">
		<td><span id="elh_student_ProvinsiIDOrtu"><?php echo $student->ProvinsiIDOrtu->FldCaption() ?></span></td>
		<td data-name="ProvinsiIDOrtu"<?php echo $student->ProvinsiIDOrtu->CellAttributes() ?>>
<span id="el_student_ProvinsiIDOrtu" data-page="3">
<span<?php echo $student->ProvinsiIDOrtu->ViewAttributes() ?>>
<?php echo $student->ProvinsiIDOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->KabupatenIDOrtu->Visible) { // KabupatenIDOrtu ?>
	<tr id="r_KabupatenIDOrtu">
		<td><span id="elh_student_KabupatenIDOrtu"><?php echo $student->KabupatenIDOrtu->FldCaption() ?></span></td>
		<td data-name="KabupatenIDOrtu"<?php echo $student->KabupatenIDOrtu->CellAttributes() ?>>
<span id="el_student_KabupatenIDOrtu" data-page="3">
<span<?php echo $student->KabupatenIDOrtu->ViewAttributes() ?>>
<?php echo $student->KabupatenIDOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->KecamatanIDOrtu->Visible) { // KecamatanIDOrtu ?>
	<tr id="r_KecamatanIDOrtu">
		<td><span id="elh_student_KecamatanIDOrtu"><?php echo $student->KecamatanIDOrtu->FldCaption() ?></span></td>
		<td data-name="KecamatanIDOrtu"<?php echo $student->KecamatanIDOrtu->CellAttributes() ?>>
<span id="el_student_KecamatanIDOrtu" data-page="3">
<span<?php echo $student->KecamatanIDOrtu->ViewAttributes() ?>>
<?php echo $student->KecamatanIDOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->DesaIDOrtu->Visible) { // DesaIDOrtu ?>
	<tr id="r_DesaIDOrtu">
		<td><span id="elh_student_DesaIDOrtu"><?php echo $student->DesaIDOrtu->FldCaption() ?></span></td>
		<td data-name="DesaIDOrtu"<?php echo $student->DesaIDOrtu->CellAttributes() ?>>
<span id="el_student_DesaIDOrtu" data-page="3">
<span<?php echo $student->DesaIDOrtu->ViewAttributes() ?>>
<?php echo $student->DesaIDOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->NegaraIDOrtu->Visible) { // NegaraIDOrtu ?>
	<tr id="r_NegaraIDOrtu">
		<td><span id="elh_student_NegaraIDOrtu"><?php echo $student->NegaraIDOrtu->FldCaption() ?></span></td>
		<td data-name="NegaraIDOrtu"<?php echo $student->NegaraIDOrtu->CellAttributes() ?>>
<span id="el_student_NegaraIDOrtu" data-page="3">
<span<?php echo $student->NegaraIDOrtu->ViewAttributes() ?>>
<?php echo $student->NegaraIDOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->TeleponOrtu->Visible) { // TeleponOrtu ?>
	<tr id="r_TeleponOrtu">
		<td><span id="elh_student_TeleponOrtu"><?php echo $student->TeleponOrtu->FldCaption() ?></span></td>
		<td data-name="TeleponOrtu"<?php echo $student->TeleponOrtu->CellAttributes() ?>>
<span id="el_student_TeleponOrtu" data-page="3">
<span<?php echo $student->TeleponOrtu->ViewAttributes() ?>>
<?php echo $student->TeleponOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->HandphoneOrtu->Visible) { // HandphoneOrtu ?>
	<tr id="r_HandphoneOrtu">
		<td><span id="elh_student_HandphoneOrtu"><?php echo $student->HandphoneOrtu->FldCaption() ?></span></td>
		<td data-name="HandphoneOrtu"<?php echo $student->HandphoneOrtu->CellAttributes() ?>>
<span id="el_student_HandphoneOrtu" data-page="3">
<span<?php echo $student->HandphoneOrtu->ViewAttributes() ?>>
<?php echo $student->HandphoneOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->EmailOrtu->Visible) { // EmailOrtu ?>
	<tr id="r_EmailOrtu">
		<td><span id="elh_student_EmailOrtu"><?php echo $student->EmailOrtu->FldCaption() ?></span></td>
		<td data-name="EmailOrtu"<?php echo $student->EmailOrtu->CellAttributes() ?>>
<span id="el_student_EmailOrtu" data-page="3">
<span<?php echo $student->EmailOrtu->ViewAttributes() ?>>
<?php echo $student->EmailOrtu->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
<?php if ($student->Export == "") { ?>
		</div>
<?php } ?>
<?php if ($student->Export == "") { ?>
		<div class="tab-pane<?php echo $student_view->MultiPages->PageStyle("4") ?>" id="tab_student4">
<?php } ?>
<table class="table table-bordered table-striped ewViewTable">
<?php if ($student->AsalSekolah->Visible) { // AsalSekolah ?>
	<tr id="r_AsalSekolah">
		<td><span id="elh_student_AsalSekolah"><?php echo $student->AsalSekolah->FldCaption() ?></span></td>
		<td data-name="AsalSekolah"<?php echo $student->AsalSekolah->CellAttributes() ?>>
<span id="el_student_AsalSekolah" data-page="4">
<span<?php echo $student->AsalSekolah->ViewAttributes() ?>>
<?php echo $student->AsalSekolah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->AlamatSekolah->Visible) { // AlamatSekolah ?>
	<tr id="r_AlamatSekolah">
		<td><span id="elh_student_AlamatSekolah"><?php echo $student->AlamatSekolah->FldCaption() ?></span></td>
		<td data-name="AlamatSekolah"<?php echo $student->AlamatSekolah->CellAttributes() ?>>
<span id="el_student_AlamatSekolah" data-page="4">
<span<?php echo $student->AlamatSekolah->ViewAttributes() ?>>
<?php echo $student->AlamatSekolah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->ProvinsiIDSekolah->Visible) { // ProvinsiIDSekolah ?>
	<tr id="r_ProvinsiIDSekolah">
		<td><span id="elh_student_ProvinsiIDSekolah"><?php echo $student->ProvinsiIDSekolah->FldCaption() ?></span></td>
		<td data-name="ProvinsiIDSekolah"<?php echo $student->ProvinsiIDSekolah->CellAttributes() ?>>
<span id="el_student_ProvinsiIDSekolah" data-page="4">
<span<?php echo $student->ProvinsiIDSekolah->ViewAttributes() ?>>
<?php echo $student->ProvinsiIDSekolah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->KabupatenIDSekolah->Visible) { // KabupatenIDSekolah ?>
	<tr id="r_KabupatenIDSekolah">
		<td><span id="elh_student_KabupatenIDSekolah"><?php echo $student->KabupatenIDSekolah->FldCaption() ?></span></td>
		<td data-name="KabupatenIDSekolah"<?php echo $student->KabupatenIDSekolah->CellAttributes() ?>>
<span id="el_student_KabupatenIDSekolah" data-page="4">
<span<?php echo $student->KabupatenIDSekolah->ViewAttributes() ?>>
<?php echo $student->KabupatenIDSekolah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->KecamatanIDSekolah->Visible) { // KecamatanIDSekolah ?>
	<tr id="r_KecamatanIDSekolah">
		<td><span id="elh_student_KecamatanIDSekolah"><?php echo $student->KecamatanIDSekolah->FldCaption() ?></span></td>
		<td data-name="KecamatanIDSekolah"<?php echo $student->KecamatanIDSekolah->CellAttributes() ?>>
<span id="el_student_KecamatanIDSekolah" data-page="4">
<span<?php echo $student->KecamatanIDSekolah->ViewAttributes() ?>>
<?php echo $student->KecamatanIDSekolah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->DesaIDSekolah->Visible) { // DesaIDSekolah ?>
	<tr id="r_DesaIDSekolah">
		<td><span id="elh_student_DesaIDSekolah"><?php echo $student->DesaIDSekolah->FldCaption() ?></span></td>
		<td data-name="DesaIDSekolah"<?php echo $student->DesaIDSekolah->CellAttributes() ?>>
<span id="el_student_DesaIDSekolah" data-page="4">
<span<?php echo $student->DesaIDSekolah->ViewAttributes() ?>>
<?php echo $student->DesaIDSekolah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->NilaiSekolah->Visible) { // NilaiSekolah ?>
	<tr id="r_NilaiSekolah">
		<td><span id="elh_student_NilaiSekolah"><?php echo $student->NilaiSekolah->FldCaption() ?></span></td>
		<td data-name="NilaiSekolah"<?php echo $student->NilaiSekolah->CellAttributes() ?>>
<span id="el_student_NilaiSekolah" data-page="4">
<span<?php echo $student->NilaiSekolah->ViewAttributes() ?>>
<?php echo $student->NilaiSekolah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->TahunLulus->Visible) { // TahunLulus ?>
	<tr id="r_TahunLulus">
		<td><span id="elh_student_TahunLulus"><?php echo $student->TahunLulus->FldCaption() ?></span></td>
		<td data-name="TahunLulus"<?php echo $student->TahunLulus->CellAttributes() ?>>
<span id="el_student_TahunLulus" data-page="4">
<span<?php echo $student->TahunLulus->ViewAttributes() ?>>
<?php echo $student->TahunLulus->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->IjazahSekolah->Visible) { // IjazahSekolah ?>
	<tr id="r_IjazahSekolah">
		<td><span id="elh_student_IjazahSekolah"><?php echo $student->IjazahSekolah->FldCaption() ?></span></td>
		<td data-name="IjazahSekolah"<?php echo $student->IjazahSekolah->CellAttributes() ?>>
<span id="el_student_IjazahSekolah" data-page="4">
<span<?php echo $student->IjazahSekolah->ViewAttributes() ?>>
<?php echo $student->IjazahSekolah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($student->TglIjazah->Visible) { // TglIjazah ?>
	<tr id="r_TglIjazah">
		<td><span id="elh_student_TglIjazah"><?php echo $student->TglIjazah->FldCaption() ?></span></td>
		<td data-name="TglIjazah"<?php echo $student->TglIjazah->CellAttributes() ?>>
<span id="el_student_TglIjazah" data-page="4">
<span<?php echo $student->TglIjazah->ViewAttributes() ?>>
<?php echo $student->TglIjazah->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
<?php if ($student->Export == "") { ?>
		</div>
<?php } ?>
<?php if ($student->Export == "") { ?>
		<div class="tab-pane<?php echo $student_view->MultiPages->PageStyle("5") ?>" id="tab_student5">
<?php } ?>
<table class="table table-bordered table-striped ewViewTable">
<?php if ($student->NA->Visible) { // NA ?>
	<tr id="r_NA">
		<td><span id="elh_student_NA"><?php echo $student->NA->FldCaption() ?></span></td>
		<td data-name="NA"<?php echo $student->NA->CellAttributes() ?>>
<span id="el_student_NA" data-page="5">
<span<?php echo $student->NA->ViewAttributes() ?>>
<?php echo $student->NA->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
<?php if ($student->Export == "") { ?>
		</div>
<?php } ?>
<?php if ($student->Export == "") { ?>
	</div>
</div>
</div>
<?php } ?>
</form>
<?php if ($student->Export == "") { ?>
<script type="text/javascript">
fstudentview.Init();
</script>
<?php } ?>
<?php
$student_view->ShowPageFooter();
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
$student_view->Page_Terminate();
?>
