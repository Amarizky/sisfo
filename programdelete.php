<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "programinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$program_delete = NULL; // Initialize page object first

class cprogram_delete extends cprogram {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'program';

	// Page object name
	var $PageObjName = 'program_delete';

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

		// Table object (program)
		if (!isset($GLOBALS["program"]) || get_class($GLOBALS["program"]) == "cprogram") {
			$GLOBALS["program"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["program"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'program', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);

		// User table object (users)
		if (!isset($UserTable)) {
			$UserTable = new cusers();
			$UserTableConn = Conn($UserTable->DBID);
		}
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
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("programlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->ProgramID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->KodeID->SetVisibility();
		$this->Def->SetVisibility();
		$this->LoginBuat->SetVisibility();
		$this->TanggalBuat->SetVisibility();
		$this->LoginEdit->SetVisibility();
		$this->TanggalEdit->SetVisibility();
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
		global $EW_EXPORT, $program;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($program);
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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("programlist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in program class, programinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} elseif (@$_GET["a_delete"] == "1") {
			$this->CurrentAction = "D"; // Delete record directly
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		if ($this->CurrentAction == "D") {
			$this->SendEmail = TRUE; // Send email on delete success
			if ($this->DeleteRows()) { // Delete rows
				if ($this->getSuccessMessage() == "")
					$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
				$this->Page_Terminate($this->getReturnUrl()); // Return to caller
			} else { // Delete failed
				$this->CurrentAction = "I"; // Display record
			}
		}
		if ($this->CurrentAction == "I") { // Load records for display
			if ($this->Recordset = $this->LoadRecordset())
				$this->TotalRecs = $this->Recordset->RecordCount(); // Get record count
			if ($this->TotalRecs <= 0) { // No record found, exit
				if ($this->Recordset)
					$this->Recordset->Close();
				$this->Page_Terminate("programlist.php"); // Return to list
			}
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
		$this->ProgramID->setDbValue($rs->fields('ProgramID'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->KodeID->setDbValue($rs->fields('KodeID'));
		$this->Def->setDbValue($rs->fields('Def'));
		$this->LoginBuat->setDbValue($rs->fields('LoginBuat'));
		$this->TanggalBuat->setDbValue($rs->fields('TanggalBuat'));
		$this->LoginEdit->setDbValue($rs->fields('LoginEdit'));
		$this->TanggalEdit->setDbValue($rs->fields('TanggalEdit'));
		$this->Keterangan->setDbValue($rs->fields('Keterangan'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->ProgramID->DbValue = $row['ProgramID'];
		$this->Nama->DbValue = $row['Nama'];
		$this->KodeID->DbValue = $row['KodeID'];
		$this->Def->DbValue = $row['Def'];
		$this->LoginBuat->DbValue = $row['LoginBuat'];
		$this->TanggalBuat->DbValue = $row['TanggalBuat'];
		$this->LoginEdit->DbValue = $row['LoginEdit'];
		$this->TanggalEdit->DbValue = $row['TanggalEdit'];
		$this->Keterangan->DbValue = $row['Keterangan'];
		$this->NA->DbValue = $row['NA'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// ProgramID
		// Nama
		// KodeID
		// Def
		// LoginBuat
		// TanggalBuat
		// LoginEdit
		// TanggalEdit
		// Keterangan
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// ProgramID
		$this->ProgramID->ViewValue = $this->ProgramID->CurrentValue;
		$this->ProgramID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// KodeID
		$this->KodeID->ViewValue = $this->KodeID->CurrentValue;
		$this->KodeID->ViewCustomAttributes = "";

		// Def
		if (ew_ConvertToBool($this->Def->CurrentValue)) {
			$this->Def->ViewValue = $this->Def->FldTagCaption(1) <> "" ? $this->Def->FldTagCaption(1) : "Y";
		} else {
			$this->Def->ViewValue = $this->Def->FldTagCaption(2) <> "" ? $this->Def->FldTagCaption(2) : "N";
		}
		$this->Def->ViewCustomAttributes = "";

		// LoginBuat
		$this->LoginBuat->ViewValue = $this->LoginBuat->CurrentValue;
		$this->LoginBuat->ViewCustomAttributes = "";

		// TanggalBuat
		$this->TanggalBuat->ViewValue = $this->TanggalBuat->CurrentValue;
		$this->TanggalBuat->ViewValue = ew_FormatDateTime($this->TanggalBuat->ViewValue, 0);
		$this->TanggalBuat->ViewCustomAttributes = "";

		// LoginEdit
		$this->LoginEdit->ViewValue = $this->LoginEdit->CurrentValue;
		$this->LoginEdit->ViewCustomAttributes = "";

		// TanggalEdit
		$this->TanggalEdit->ViewValue = $this->TanggalEdit->CurrentValue;
		$this->TanggalEdit->ViewValue = ew_FormatDateTime($this->TanggalEdit->ViewValue, 0);
		$this->TanggalEdit->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

			// ProgramID
			$this->ProgramID->LinkCustomAttributes = "";
			$this->ProgramID->HrefValue = "";
			$this->ProgramID->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// KodeID
			$this->KodeID->LinkCustomAttributes = "";
			$this->KodeID->HrefValue = "";
			$this->KodeID->TooltipValue = "";

			// Def
			$this->Def->LinkCustomAttributes = "";
			$this->Def->HrefValue = "";
			$this->Def->TooltipValue = "";

			// LoginBuat
			$this->LoginBuat->LinkCustomAttributes = "";
			$this->LoginBuat->HrefValue = "";
			$this->LoginBuat->TooltipValue = "";

			// TanggalBuat
			$this->TanggalBuat->LinkCustomAttributes = "";
			$this->TanggalBuat->HrefValue = "";
			$this->TanggalBuat->TooltipValue = "";

			// LoginEdit
			$this->LoginEdit->LinkCustomAttributes = "";
			$this->LoginEdit->HrefValue = "";
			$this->LoginEdit->TooltipValue = "";

			// TanggalEdit
			$this->TanggalEdit->LinkCustomAttributes = "";
			$this->TanggalEdit->HrefValue = "";
			$this->TanggalEdit->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
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
		$conn->BeginTrans();
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
				$sThisKey .= $row['ProgramID'];
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
			$conn->CommitTrans(); // Commit the changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteSuccess")); // Batch delete success
		} else {
			$conn->RollbackTrans(); // Rollback changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteRollback")); // Batch delete rollback
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("programlist.php"), "", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, $url);
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
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($program_delete)) $program_delete = new cprogram_delete();

// Page init
$program_delete->Page_Init();

// Page main
$program_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$program_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "delete";
var CurrentForm = fprogramdelete = new ew_Form("fprogramdelete", "delete");

// Form_CustomValidate event
fprogramdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fprogramdelete.ValidateRequired = true;
<?php } else { ?>
fprogramdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fprogramdelete.Lists["x_Def"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fprogramdelete.Lists["x_Def"].Options = <?php echo json_encode($program->Def->Options()) ?>;
fprogramdelete.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fprogramdelete.Lists["x_NA"].Options = <?php echo json_encode($program->NA->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $program_delete->ShowPageHeader(); ?>
<?php
$program_delete->ShowMessage();
?>
<form name="fprogramdelete" id="fprogramdelete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($program_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $program_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="program">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($program_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $program->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($program->ProgramID->Visible) { // ProgramID ?>
		<th><span id="elh_program_ProgramID" class="program_ProgramID"><?php echo $program->ProgramID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($program->Nama->Visible) { // Nama ?>
		<th><span id="elh_program_Nama" class="program_Nama"><?php echo $program->Nama->FldCaption() ?></span></th>
<?php } ?>
<?php if ($program->KodeID->Visible) { // KodeID ?>
		<th><span id="elh_program_KodeID" class="program_KodeID"><?php echo $program->KodeID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($program->Def->Visible) { // Def ?>
		<th><span id="elh_program_Def" class="program_Def"><?php echo $program->Def->FldCaption() ?></span></th>
<?php } ?>
<?php if ($program->LoginBuat->Visible) { // LoginBuat ?>
		<th><span id="elh_program_LoginBuat" class="program_LoginBuat"><?php echo $program->LoginBuat->FldCaption() ?></span></th>
<?php } ?>
<?php if ($program->TanggalBuat->Visible) { // TanggalBuat ?>
		<th><span id="elh_program_TanggalBuat" class="program_TanggalBuat"><?php echo $program->TanggalBuat->FldCaption() ?></span></th>
<?php } ?>
<?php if ($program->LoginEdit->Visible) { // LoginEdit ?>
		<th><span id="elh_program_LoginEdit" class="program_LoginEdit"><?php echo $program->LoginEdit->FldCaption() ?></span></th>
<?php } ?>
<?php if ($program->TanggalEdit->Visible) { // TanggalEdit ?>
		<th><span id="elh_program_TanggalEdit" class="program_TanggalEdit"><?php echo $program->TanggalEdit->FldCaption() ?></span></th>
<?php } ?>
<?php if ($program->NA->Visible) { // NA ?>
		<th><span id="elh_program_NA" class="program_NA"><?php echo $program->NA->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$program_delete->RecCnt = 0;
$i = 0;
while (!$program_delete->Recordset->EOF) {
	$program_delete->RecCnt++;
	$program_delete->RowCnt++;

	// Set row properties
	$program->ResetAttrs();
	$program->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$program_delete->LoadRowValues($program_delete->Recordset);

	// Render row
	$program_delete->RenderRow();
?>
	<tr<?php echo $program->RowAttributes() ?>>
<?php if ($program->ProgramID->Visible) { // ProgramID ?>
		<td<?php echo $program->ProgramID->CellAttributes() ?>>
<span id="el<?php echo $program_delete->RowCnt ?>_program_ProgramID" class="program_ProgramID">
<span<?php echo $program->ProgramID->ViewAttributes() ?>>
<?php echo $program->ProgramID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($program->Nama->Visible) { // Nama ?>
		<td<?php echo $program->Nama->CellAttributes() ?>>
<span id="el<?php echo $program_delete->RowCnt ?>_program_Nama" class="program_Nama">
<span<?php echo $program->Nama->ViewAttributes() ?>>
<?php echo $program->Nama->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($program->KodeID->Visible) { // KodeID ?>
		<td<?php echo $program->KodeID->CellAttributes() ?>>
<span id="el<?php echo $program_delete->RowCnt ?>_program_KodeID" class="program_KodeID">
<span<?php echo $program->KodeID->ViewAttributes() ?>>
<?php echo $program->KodeID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($program->Def->Visible) { // Def ?>
		<td<?php echo $program->Def->CellAttributes() ?>>
<span id="el<?php echo $program_delete->RowCnt ?>_program_Def" class="program_Def">
<span<?php echo $program->Def->ViewAttributes() ?>>
<?php echo $program->Def->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($program->LoginBuat->Visible) { // LoginBuat ?>
		<td<?php echo $program->LoginBuat->CellAttributes() ?>>
<span id="el<?php echo $program_delete->RowCnt ?>_program_LoginBuat" class="program_LoginBuat">
<span<?php echo $program->LoginBuat->ViewAttributes() ?>>
<?php echo $program->LoginBuat->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($program->TanggalBuat->Visible) { // TanggalBuat ?>
		<td<?php echo $program->TanggalBuat->CellAttributes() ?>>
<span id="el<?php echo $program_delete->RowCnt ?>_program_TanggalBuat" class="program_TanggalBuat">
<span<?php echo $program->TanggalBuat->ViewAttributes() ?>>
<?php echo $program->TanggalBuat->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($program->LoginEdit->Visible) { // LoginEdit ?>
		<td<?php echo $program->LoginEdit->CellAttributes() ?>>
<span id="el<?php echo $program_delete->RowCnt ?>_program_LoginEdit" class="program_LoginEdit">
<span<?php echo $program->LoginEdit->ViewAttributes() ?>>
<?php echo $program->LoginEdit->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($program->TanggalEdit->Visible) { // TanggalEdit ?>
		<td<?php echo $program->TanggalEdit->CellAttributes() ?>>
<span id="el<?php echo $program_delete->RowCnt ?>_program_TanggalEdit" class="program_TanggalEdit">
<span<?php echo $program->TanggalEdit->ViewAttributes() ?>>
<?php echo $program->TanggalEdit->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($program->NA->Visible) { // NA ?>
		<td<?php echo $program->NA->CellAttributes() ?>>
<span id="el<?php echo $program_delete->RowCnt ?>_program_NA" class="program_NA">
<span<?php echo $program->NA->ViewAttributes() ?>>
<?php echo $program->NA->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$program_delete->Recordset->MoveNext();
}
$program_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</div>
<div>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $program_delete->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fprogramdelete.Init();
</script>
<?php
$program_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$program_delete->Page_Terminate();
?>
