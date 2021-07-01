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

$krs_view = NULL; // Initialize page object first

class ckrs_view extends ckrs {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'krs';

	// Page object name
	var $PageObjName = 'krs_view';

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
		$KeyUrl = "";
		if (@$_GET["KRSID"] <> "") {
			$this->RecKey["KRSID"] = $_GET["KRSID"];
			$KeyUrl .= "&amp;KRSID=" . urlencode($this->RecKey["KRSID"]);
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
				$this->Page_Terminate(ew_GetUrl("krslist.php"));
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
		if (@$_GET["KRSID"] <> "") {
			if ($gsExportFile <> "") $gsExportFile .= "_";
			$gsExportFile .= ew_StripSlashes($_GET["KRSID"]);
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
			if (@$_GET["KRSID"] <> "") {
				$this->KRSID->setQueryStringValue($_GET["KRSID"]);
				$this->RecKey["KRSID"] = $this->KRSID->QueryStringValue;
			} elseif (@$_POST["KRSID"] <> "") {
				$this->KRSID->setFormValue($_POST["KRSID"]);
				$this->RecKey["KRSID"] = $this->KRSID->FormValue;
			} else {
				$sReturnUrl = "krslist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "krslist.php"; // No matching record, return to list
					}
			}

			// Export data only
			if ($this->CustomExport == "" && in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
				$this->ExportData();
				$this->Page_Terminate(); // Terminate response
				exit();
			}
		} else {
			$sReturnUrl = "krslist.php"; // Not page request, return to list
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

			// KHSID
			$this->KHSID->LinkCustomAttributes = "";
			$this->KHSID->HrefValue = "";
			$this->KHSID->TooltipValue = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";
			$this->StudentID->TooltipValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";
			$this->TahunID->TooltipValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";
			$this->Sesi->TooltipValue = "";

			// JadwalID
			$this->JadwalID->LinkCustomAttributes = "";
			$this->JadwalID->HrefValue = "";
			$this->JadwalID->TooltipValue = "";

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";
			$this->MKID->TooltipValue = "";

			// MKKode
			$this->MKKode->LinkCustomAttributes = "";
			$this->MKKode->HrefValue = "";
			$this->MKKode->TooltipValue = "";

			// SKS
			$this->SKS->LinkCustomAttributes = "";
			$this->SKS->HrefValue = "";
			$this->SKS->TooltipValue = "";

			// Tugas1
			$this->Tugas1->LinkCustomAttributes = "";
			$this->Tugas1->HrefValue = "";
			$this->Tugas1->TooltipValue = "";

			// Tugas2
			$this->Tugas2->LinkCustomAttributes = "";
			$this->Tugas2->HrefValue = "";
			$this->Tugas2->TooltipValue = "";

			// Tugas3
			$this->Tugas3->LinkCustomAttributes = "";
			$this->Tugas3->HrefValue = "";
			$this->Tugas3->TooltipValue = "";

			// Tugas4
			$this->Tugas4->LinkCustomAttributes = "";
			$this->Tugas4->HrefValue = "";
			$this->Tugas4->TooltipValue = "";

			// Tugas5
			$this->Tugas5->LinkCustomAttributes = "";
			$this->Tugas5->HrefValue = "";
			$this->Tugas5->TooltipValue = "";

			// Presensi
			$this->Presensi->LinkCustomAttributes = "";
			$this->Presensi->HrefValue = "";
			$this->Presensi->TooltipValue = "";

			// _Presensi
			$this->_Presensi->LinkCustomAttributes = "";
			$this->_Presensi->HrefValue = "";
			$this->_Presensi->TooltipValue = "";

			// UTS
			$this->UTS->LinkCustomAttributes = "";
			$this->UTS->HrefValue = "";
			$this->UTS->TooltipValue = "";

			// UAS
			$this->UAS->LinkCustomAttributes = "";
			$this->UAS->HrefValue = "";
			$this->UAS->TooltipValue = "";

			// Responsi
			$this->Responsi->LinkCustomAttributes = "";
			$this->Responsi->HrefValue = "";
			$this->Responsi->TooltipValue = "";

			// NilaiAkhir
			$this->NilaiAkhir->LinkCustomAttributes = "";
			$this->NilaiAkhir->HrefValue = "";
			$this->NilaiAkhir->TooltipValue = "";

			// GradeNilai
			$this->GradeNilai->LinkCustomAttributes = "";
			$this->GradeNilai->HrefValue = "";
			$this->GradeNilai->TooltipValue = "";

			// BobotNilai
			$this->BobotNilai->LinkCustomAttributes = "";
			$this->BobotNilai->HrefValue = "";
			$this->BobotNilai->TooltipValue = "";

			// StatusKRSID
			$this->StatusKRSID->LinkCustomAttributes = "";
			$this->StatusKRSID->HrefValue = "";
			$this->StatusKRSID->TooltipValue = "";

			// Tinggi
			$this->Tinggi->LinkCustomAttributes = "";
			$this->Tinggi->HrefValue = "";
			$this->Tinggi->TooltipValue = "";

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

			// CreateDate
			$this->CreateDate->LinkCustomAttributes = "";
			$this->CreateDate->HrefValue = "";
			$this->CreateDate->TooltipValue = "";

			// Editor
			$this->Editor->LinkCustomAttributes = "";
			$this->Editor->HrefValue = "";
			$this->Editor->TooltipValue = "";

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
		$item->Body = "<button id=\"emf_krs\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_krs',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fkrsview,key:" . ew_ArrayToJsonAttr($this->RecKey) . ",sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("krslist.php"), "", $this->TableVar, TRUE);
		$PageId = "view";
		$Breadcrumb->Add("view", $PageId, $url);
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
if (!isset($krs_view)) $krs_view = new ckrs_view();

// Page init
$krs_view->Page_Init();

// Page main
$krs_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$krs_view->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($krs->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "view";
var CurrentForm = fkrsview = new ew_Form("fkrsview", "view");

// Form_CustomValidate event
fkrsview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fkrsview.ValidateRequired = true;
<?php } else { ?>
fkrsview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fkrsview.Lists["x_Final"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrsview.Lists["x_Final"].Options = <?php echo json_encode($krs->Final->Options()) ?>;
fkrsview.Lists["x_Setara"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrsview.Lists["x_Setara"].Options = <?php echo json_encode($krs->Setara->Options()) ?>;
fkrsview.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrsview.Lists["x_NA"].Options = <?php echo json_encode($krs->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($krs->Export == "") { ?>
<div class="ewToolbar">
<?php if (!$krs_view->IsModal) { ?>
<?php if ($krs->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php } ?>
<?php $krs_view->ExportOptions->Render("body") ?>
<?php
	foreach ($krs_view->OtherOptions as &$option)
		$option->Render("body");
?>
<?php if (!$krs_view->IsModal) { ?>
<?php if ($krs->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $krs_view->ShowPageHeader(); ?>
<?php
$krs_view->ShowMessage();
?>
<form name="fkrsview" id="fkrsview" class="form-inline ewForm ewViewForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($krs_view->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $krs_view->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="krs">
<?php if ($krs_view->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<table class="table table-bordered table-striped ewViewTable">
<?php if ($krs->KRSID->Visible) { // KRSID ?>
	<tr id="r_KRSID">
		<td><span id="elh_krs_KRSID"><?php echo $krs->KRSID->FldCaption() ?></span></td>
		<td data-name="KRSID"<?php echo $krs->KRSID->CellAttributes() ?>>
<span id="el_krs_KRSID">
<span<?php echo $krs->KRSID->ViewAttributes() ?>>
<?php echo $krs->KRSID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->KHSID->Visible) { // KHSID ?>
	<tr id="r_KHSID">
		<td><span id="elh_krs_KHSID"><?php echo $krs->KHSID->FldCaption() ?></span></td>
		<td data-name="KHSID"<?php echo $krs->KHSID->CellAttributes() ?>>
<span id="el_krs_KHSID">
<span<?php echo $krs->KHSID->ViewAttributes() ?>>
<?php echo $krs->KHSID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->StudentID->Visible) { // StudentID ?>
	<tr id="r_StudentID">
		<td><span id="elh_krs_StudentID"><?php echo $krs->StudentID->FldCaption() ?></span></td>
		<td data-name="StudentID"<?php echo $krs->StudentID->CellAttributes() ?>>
<span id="el_krs_StudentID">
<span<?php echo $krs->StudentID->ViewAttributes() ?>>
<?php echo $krs->StudentID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->TahunID->Visible) { // TahunID ?>
	<tr id="r_TahunID">
		<td><span id="elh_krs_TahunID"><?php echo $krs->TahunID->FldCaption() ?></span></td>
		<td data-name="TahunID"<?php echo $krs->TahunID->CellAttributes() ?>>
<span id="el_krs_TahunID">
<span<?php echo $krs->TahunID->ViewAttributes() ?>>
<?php echo $krs->TahunID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Sesi->Visible) { // Sesi ?>
	<tr id="r_Sesi">
		<td><span id="elh_krs_Sesi"><?php echo $krs->Sesi->FldCaption() ?></span></td>
		<td data-name="Sesi"<?php echo $krs->Sesi->CellAttributes() ?>>
<span id="el_krs_Sesi">
<span<?php echo $krs->Sesi->ViewAttributes() ?>>
<?php echo $krs->Sesi->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->JadwalID->Visible) { // JadwalID ?>
	<tr id="r_JadwalID">
		<td><span id="elh_krs_JadwalID"><?php echo $krs->JadwalID->FldCaption() ?></span></td>
		<td data-name="JadwalID"<?php echo $krs->JadwalID->CellAttributes() ?>>
<span id="el_krs_JadwalID">
<span<?php echo $krs->JadwalID->ViewAttributes() ?>>
<?php echo $krs->JadwalID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->MKID->Visible) { // MKID ?>
	<tr id="r_MKID">
		<td><span id="elh_krs_MKID"><?php echo $krs->MKID->FldCaption() ?></span></td>
		<td data-name="MKID"<?php echo $krs->MKID->CellAttributes() ?>>
<span id="el_krs_MKID">
<span<?php echo $krs->MKID->ViewAttributes() ?>>
<?php echo $krs->MKID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->MKKode->Visible) { // MKKode ?>
	<tr id="r_MKKode">
		<td><span id="elh_krs_MKKode"><?php echo $krs->MKKode->FldCaption() ?></span></td>
		<td data-name="MKKode"<?php echo $krs->MKKode->CellAttributes() ?>>
<span id="el_krs_MKKode">
<span<?php echo $krs->MKKode->ViewAttributes() ?>>
<?php echo $krs->MKKode->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->SKS->Visible) { // SKS ?>
	<tr id="r_SKS">
		<td><span id="elh_krs_SKS"><?php echo $krs->SKS->FldCaption() ?></span></td>
		<td data-name="SKS"<?php echo $krs->SKS->CellAttributes() ?>>
<span id="el_krs_SKS">
<span<?php echo $krs->SKS->ViewAttributes() ?>>
<?php echo $krs->SKS->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Tugas1->Visible) { // Tugas1 ?>
	<tr id="r_Tugas1">
		<td><span id="elh_krs_Tugas1"><?php echo $krs->Tugas1->FldCaption() ?></span></td>
		<td data-name="Tugas1"<?php echo $krs->Tugas1->CellAttributes() ?>>
<span id="el_krs_Tugas1">
<span<?php echo $krs->Tugas1->ViewAttributes() ?>>
<?php echo $krs->Tugas1->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Tugas2->Visible) { // Tugas2 ?>
	<tr id="r_Tugas2">
		<td><span id="elh_krs_Tugas2"><?php echo $krs->Tugas2->FldCaption() ?></span></td>
		<td data-name="Tugas2"<?php echo $krs->Tugas2->CellAttributes() ?>>
<span id="el_krs_Tugas2">
<span<?php echo $krs->Tugas2->ViewAttributes() ?>>
<?php echo $krs->Tugas2->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Tugas3->Visible) { // Tugas3 ?>
	<tr id="r_Tugas3">
		<td><span id="elh_krs_Tugas3"><?php echo $krs->Tugas3->FldCaption() ?></span></td>
		<td data-name="Tugas3"<?php echo $krs->Tugas3->CellAttributes() ?>>
<span id="el_krs_Tugas3">
<span<?php echo $krs->Tugas3->ViewAttributes() ?>>
<?php echo $krs->Tugas3->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Tugas4->Visible) { // Tugas4 ?>
	<tr id="r_Tugas4">
		<td><span id="elh_krs_Tugas4"><?php echo $krs->Tugas4->FldCaption() ?></span></td>
		<td data-name="Tugas4"<?php echo $krs->Tugas4->CellAttributes() ?>>
<span id="el_krs_Tugas4">
<span<?php echo $krs->Tugas4->ViewAttributes() ?>>
<?php echo $krs->Tugas4->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Tugas5->Visible) { // Tugas5 ?>
	<tr id="r_Tugas5">
		<td><span id="elh_krs_Tugas5"><?php echo $krs->Tugas5->FldCaption() ?></span></td>
		<td data-name="Tugas5"<?php echo $krs->Tugas5->CellAttributes() ?>>
<span id="el_krs_Tugas5">
<span<?php echo $krs->Tugas5->ViewAttributes() ?>>
<?php echo $krs->Tugas5->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Presensi->Visible) { // Presensi ?>
	<tr id="r_Presensi">
		<td><span id="elh_krs_Presensi"><?php echo $krs->Presensi->FldCaption() ?></span></td>
		<td data-name="Presensi"<?php echo $krs->Presensi->CellAttributes() ?>>
<span id="el_krs_Presensi">
<span<?php echo $krs->Presensi->ViewAttributes() ?>>
<?php echo $krs->Presensi->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->_Presensi->Visible) { // _Presensi ?>
	<tr id="r__Presensi">
		<td><span id="elh_krs__Presensi"><?php echo $krs->_Presensi->FldCaption() ?></span></td>
		<td data-name="_Presensi"<?php echo $krs->_Presensi->CellAttributes() ?>>
<span id="el_krs__Presensi">
<span<?php echo $krs->_Presensi->ViewAttributes() ?>>
<?php echo $krs->_Presensi->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->UTS->Visible) { // UTS ?>
	<tr id="r_UTS">
		<td><span id="elh_krs_UTS"><?php echo $krs->UTS->FldCaption() ?></span></td>
		<td data-name="UTS"<?php echo $krs->UTS->CellAttributes() ?>>
<span id="el_krs_UTS">
<span<?php echo $krs->UTS->ViewAttributes() ?>>
<?php echo $krs->UTS->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->UAS->Visible) { // UAS ?>
	<tr id="r_UAS">
		<td><span id="elh_krs_UAS"><?php echo $krs->UAS->FldCaption() ?></span></td>
		<td data-name="UAS"<?php echo $krs->UAS->CellAttributes() ?>>
<span id="el_krs_UAS">
<span<?php echo $krs->UAS->ViewAttributes() ?>>
<?php echo $krs->UAS->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Responsi->Visible) { // Responsi ?>
	<tr id="r_Responsi">
		<td><span id="elh_krs_Responsi"><?php echo $krs->Responsi->FldCaption() ?></span></td>
		<td data-name="Responsi"<?php echo $krs->Responsi->CellAttributes() ?>>
<span id="el_krs_Responsi">
<span<?php echo $krs->Responsi->ViewAttributes() ?>>
<?php echo $krs->Responsi->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->NilaiAkhir->Visible) { // NilaiAkhir ?>
	<tr id="r_NilaiAkhir">
		<td><span id="elh_krs_NilaiAkhir"><?php echo $krs->NilaiAkhir->FldCaption() ?></span></td>
		<td data-name="NilaiAkhir"<?php echo $krs->NilaiAkhir->CellAttributes() ?>>
<span id="el_krs_NilaiAkhir">
<span<?php echo $krs->NilaiAkhir->ViewAttributes() ?>>
<?php echo $krs->NilaiAkhir->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->GradeNilai->Visible) { // GradeNilai ?>
	<tr id="r_GradeNilai">
		<td><span id="elh_krs_GradeNilai"><?php echo $krs->GradeNilai->FldCaption() ?></span></td>
		<td data-name="GradeNilai"<?php echo $krs->GradeNilai->CellAttributes() ?>>
<span id="el_krs_GradeNilai">
<span<?php echo $krs->GradeNilai->ViewAttributes() ?>>
<?php echo $krs->GradeNilai->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->BobotNilai->Visible) { // BobotNilai ?>
	<tr id="r_BobotNilai">
		<td><span id="elh_krs_BobotNilai"><?php echo $krs->BobotNilai->FldCaption() ?></span></td>
		<td data-name="BobotNilai"<?php echo $krs->BobotNilai->CellAttributes() ?>>
<span id="el_krs_BobotNilai">
<span<?php echo $krs->BobotNilai->ViewAttributes() ?>>
<?php echo $krs->BobotNilai->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->StatusKRSID->Visible) { // StatusKRSID ?>
	<tr id="r_StatusKRSID">
		<td><span id="elh_krs_StatusKRSID"><?php echo $krs->StatusKRSID->FldCaption() ?></span></td>
		<td data-name="StatusKRSID"<?php echo $krs->StatusKRSID->CellAttributes() ?>>
<span id="el_krs_StatusKRSID">
<span<?php echo $krs->StatusKRSID->ViewAttributes() ?>>
<?php echo $krs->StatusKRSID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Tinggi->Visible) { // Tinggi ?>
	<tr id="r_Tinggi">
		<td><span id="elh_krs_Tinggi"><?php echo $krs->Tinggi->FldCaption() ?></span></td>
		<td data-name="Tinggi"<?php echo $krs->Tinggi->CellAttributes() ?>>
<span id="el_krs_Tinggi">
<span<?php echo $krs->Tinggi->ViewAttributes() ?>>
<?php echo $krs->Tinggi->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Final->Visible) { // Final ?>
	<tr id="r_Final">
		<td><span id="elh_krs_Final"><?php echo $krs->Final->FldCaption() ?></span></td>
		<td data-name="Final"<?php echo $krs->Final->CellAttributes() ?>>
<span id="el_krs_Final">
<span<?php echo $krs->Final->ViewAttributes() ?>>
<?php echo $krs->Final->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Setara->Visible) { // Setara ?>
	<tr id="r_Setara">
		<td><span id="elh_krs_Setara"><?php echo $krs->Setara->FldCaption() ?></span></td>
		<td data-name="Setara"<?php echo $krs->Setara->CellAttributes() ?>>
<span id="el_krs_Setara">
<span<?php echo $krs->Setara->ViewAttributes() ?>>
<?php echo $krs->Setara->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Creator->Visible) { // Creator ?>
	<tr id="r_Creator">
		<td><span id="elh_krs_Creator"><?php echo $krs->Creator->FldCaption() ?></span></td>
		<td data-name="Creator"<?php echo $krs->Creator->CellAttributes() ?>>
<span id="el_krs_Creator">
<span<?php echo $krs->Creator->ViewAttributes() ?>>
<?php echo $krs->Creator->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->CreateDate->Visible) { // CreateDate ?>
	<tr id="r_CreateDate">
		<td><span id="elh_krs_CreateDate"><?php echo $krs->CreateDate->FldCaption() ?></span></td>
		<td data-name="CreateDate"<?php echo $krs->CreateDate->CellAttributes() ?>>
<span id="el_krs_CreateDate">
<span<?php echo $krs->CreateDate->ViewAttributes() ?>>
<?php echo $krs->CreateDate->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->Editor->Visible) { // Editor ?>
	<tr id="r_Editor">
		<td><span id="elh_krs_Editor"><?php echo $krs->Editor->FldCaption() ?></span></td>
		<td data-name="Editor"<?php echo $krs->Editor->CellAttributes() ?>>
<span id="el_krs_Editor">
<span<?php echo $krs->Editor->ViewAttributes() ?>>
<?php echo $krs->Editor->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->EditDate->Visible) { // EditDate ?>
	<tr id="r_EditDate">
		<td><span id="elh_krs_EditDate"><?php echo $krs->EditDate->FldCaption() ?></span></td>
		<td data-name="EditDate"<?php echo $krs->EditDate->CellAttributes() ?>>
<span id="el_krs_EditDate">
<span<?php echo $krs->EditDate->ViewAttributes() ?>>
<?php echo $krs->EditDate->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($krs->NA->Visible) { // NA ?>
	<tr id="r_NA">
		<td><span id="elh_krs_NA"><?php echo $krs->NA->FldCaption() ?></span></td>
		<td data-name="NA"<?php echo $krs->NA->CellAttributes() ?>>
<span id="el_krs_NA">
<span<?php echo $krs->NA->ViewAttributes() ?>>
<?php echo $krs->NA->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
</form>
<?php if ($krs->Export == "") { ?>
<script type="text/javascript">
fkrsview.Init();
</script>
<?php } ?>
<?php
$krs_view->ShowPageFooter();
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
$krs_view->Page_Terminate();
?>
