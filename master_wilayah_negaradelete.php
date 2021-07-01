<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_wilayah_negarainfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_wilayah_negara_delete = NULL; // Initialize page object first

class cmaster_wilayah_negara_delete extends cmaster_wilayah_negara {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_wilayah_negara';

	// Page object name
	var $PageObjName = 'master_wilayah_negara_delete';

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

		// Table object (master_wilayah_negara)
		if (!isset($GLOBALS["master_wilayah_negara"]) || get_class($GLOBALS["master_wilayah_negara"]) == "cmaster_wilayah_negara") {
			$GLOBALS["master_wilayah_negara"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_wilayah_negara"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_wilayah_negara', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("master_wilayah_negaralist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->NegaraID->SetVisibility();
		$this->BenuaID->SetVisibility();
		$this->NamaBenua->SetVisibility();
		$this->KodeNegara->SetVisibility();
		$this->NamaNegara->SetVisibility();
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
		global $EW_EXPORT, $master_wilayah_negara;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_wilayah_negara);
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
			$this->Page_Terminate("master_wilayah_negaralist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in master_wilayah_negara class, master_wilayah_negarainfo.php

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
				$this->Page_Terminate("master_wilayah_negaralist.php"); // Return to list
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
		$this->NegaraID->setDbValue($rs->fields('NegaraID'));
		$this->BenuaID->setDbValue($rs->fields('BenuaID'));
		$this->NamaBenua->setDbValue($rs->fields('NamaBenua'));
		$this->KodeNegara->setDbValue($rs->fields('KodeNegara'));
		$this->NamaNegara->setDbValue($rs->fields('NamaNegara'));
		$this->NA->setDbValue($rs->fields('NA'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->NegaraID->DbValue = $row['NegaraID'];
		$this->BenuaID->DbValue = $row['BenuaID'];
		$this->NamaBenua->DbValue = $row['NamaBenua'];
		$this->KodeNegara->DbValue = $row['KodeNegara'];
		$this->NamaNegara->DbValue = $row['NamaNegara'];
		$this->NA->DbValue = $row['NA'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// NegaraID
		// BenuaID
		// NamaBenua
		// KodeNegara
		// NamaNegara
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// NegaraID
		$this->NegaraID->ViewValue = $this->NegaraID->CurrentValue;
		$this->NegaraID->ViewCustomAttributes = "";

		// BenuaID
		$this->BenuaID->ViewValue = $this->BenuaID->CurrentValue;
		$this->BenuaID->ViewCustomAttributes = "";

		// NamaBenua
		$this->NamaBenua->ViewValue = $this->NamaBenua->CurrentValue;
		$this->NamaBenua->ViewCustomAttributes = "";

		// KodeNegara
		$this->KodeNegara->ViewValue = $this->KodeNegara->CurrentValue;
		$this->KodeNegara->ViewCustomAttributes = "";

		// NamaNegara
		$this->NamaNegara->ViewValue = $this->NamaNegara->CurrentValue;
		$this->NamaNegara->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

			// NegaraID
			$this->NegaraID->LinkCustomAttributes = "";
			$this->NegaraID->HrefValue = "";
			$this->NegaraID->TooltipValue = "";

			// BenuaID
			$this->BenuaID->LinkCustomAttributes = "";
			$this->BenuaID->HrefValue = "";
			$this->BenuaID->TooltipValue = "";

			// NamaBenua
			$this->NamaBenua->LinkCustomAttributes = "";
			$this->NamaBenua->HrefValue = "";
			$this->NamaBenua->TooltipValue = "";

			// KodeNegara
			$this->KodeNegara->LinkCustomAttributes = "";
			$this->KodeNegara->HrefValue = "";
			$this->KodeNegara->TooltipValue = "";

			// NamaNegara
			$this->NamaNegara->LinkCustomAttributes = "";
			$this->NamaNegara->HrefValue = "";
			$this->NamaNegara->TooltipValue = "";

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
				$sThisKey .= $row['NegaraID'];
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_wilayah_negaralist.php"), "", $this->TableVar, TRUE);
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
if (!isset($master_wilayah_negara_delete)) $master_wilayah_negara_delete = new cmaster_wilayah_negara_delete();

// Page init
$master_wilayah_negara_delete->Page_Init();

// Page main
$master_wilayah_negara_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_wilayah_negara_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "delete";
var CurrentForm = fmaster_wilayah_negaradelete = new ew_Form("fmaster_wilayah_negaradelete", "delete");

// Form_CustomValidate event
fmaster_wilayah_negaradelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_wilayah_negaradelete.ValidateRequired = true;
<?php } else { ?>
fmaster_wilayah_negaradelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_wilayah_negaradelete.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_wilayah_negaradelete.Lists["x_NA"].Options = <?php echo json_encode($master_wilayah_negara->NA->Options()) ?>;

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
<?php $master_wilayah_negara_delete->ShowPageHeader(); ?>
<?php
$master_wilayah_negara_delete->ShowMessage();
?>
<form name="fmaster_wilayah_negaradelete" id="fmaster_wilayah_negaradelete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_wilayah_negara_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_wilayah_negara_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_wilayah_negara">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($master_wilayah_negara_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $master_wilayah_negara->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($master_wilayah_negara->NegaraID->Visible) { // NegaraID ?>
		<th><span id="elh_master_wilayah_negara_NegaraID" class="master_wilayah_negara_NegaraID"><?php echo $master_wilayah_negara->NegaraID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_wilayah_negara->BenuaID->Visible) { // BenuaID ?>
		<th><span id="elh_master_wilayah_negara_BenuaID" class="master_wilayah_negara_BenuaID"><?php echo $master_wilayah_negara->BenuaID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_wilayah_negara->NamaBenua->Visible) { // NamaBenua ?>
		<th><span id="elh_master_wilayah_negara_NamaBenua" class="master_wilayah_negara_NamaBenua"><?php echo $master_wilayah_negara->NamaBenua->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_wilayah_negara->KodeNegara->Visible) { // KodeNegara ?>
		<th><span id="elh_master_wilayah_negara_KodeNegara" class="master_wilayah_negara_KodeNegara"><?php echo $master_wilayah_negara->KodeNegara->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_wilayah_negara->NamaNegara->Visible) { // NamaNegara ?>
		<th><span id="elh_master_wilayah_negara_NamaNegara" class="master_wilayah_negara_NamaNegara"><?php echo $master_wilayah_negara->NamaNegara->FldCaption() ?></span></th>
<?php } ?>
<?php if ($master_wilayah_negara->NA->Visible) { // NA ?>
		<th><span id="elh_master_wilayah_negara_NA" class="master_wilayah_negara_NA"><?php echo $master_wilayah_negara->NA->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$master_wilayah_negara_delete->RecCnt = 0;
$i = 0;
while (!$master_wilayah_negara_delete->Recordset->EOF) {
	$master_wilayah_negara_delete->RecCnt++;
	$master_wilayah_negara_delete->RowCnt++;

	// Set row properties
	$master_wilayah_negara->ResetAttrs();
	$master_wilayah_negara->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$master_wilayah_negara_delete->LoadRowValues($master_wilayah_negara_delete->Recordset);

	// Render row
	$master_wilayah_negara_delete->RenderRow();
?>
	<tr<?php echo $master_wilayah_negara->RowAttributes() ?>>
<?php if ($master_wilayah_negara->NegaraID->Visible) { // NegaraID ?>
		<td<?php echo $master_wilayah_negara->NegaraID->CellAttributes() ?>>
<span id="el<?php echo $master_wilayah_negara_delete->RowCnt ?>_master_wilayah_negara_NegaraID" class="master_wilayah_negara_NegaraID">
<span<?php echo $master_wilayah_negara->NegaraID->ViewAttributes() ?>>
<?php echo $master_wilayah_negara->NegaraID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_wilayah_negara->BenuaID->Visible) { // BenuaID ?>
		<td<?php echo $master_wilayah_negara->BenuaID->CellAttributes() ?>>
<span id="el<?php echo $master_wilayah_negara_delete->RowCnt ?>_master_wilayah_negara_BenuaID" class="master_wilayah_negara_BenuaID">
<span<?php echo $master_wilayah_negara->BenuaID->ViewAttributes() ?>>
<?php echo $master_wilayah_negara->BenuaID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_wilayah_negara->NamaBenua->Visible) { // NamaBenua ?>
		<td<?php echo $master_wilayah_negara->NamaBenua->CellAttributes() ?>>
<span id="el<?php echo $master_wilayah_negara_delete->RowCnt ?>_master_wilayah_negara_NamaBenua" class="master_wilayah_negara_NamaBenua">
<span<?php echo $master_wilayah_negara->NamaBenua->ViewAttributes() ?>>
<?php echo $master_wilayah_negara->NamaBenua->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_wilayah_negara->KodeNegara->Visible) { // KodeNegara ?>
		<td<?php echo $master_wilayah_negara->KodeNegara->CellAttributes() ?>>
<span id="el<?php echo $master_wilayah_negara_delete->RowCnt ?>_master_wilayah_negara_KodeNegara" class="master_wilayah_negara_KodeNegara">
<span<?php echo $master_wilayah_negara->KodeNegara->ViewAttributes() ?>>
<?php echo $master_wilayah_negara->KodeNegara->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_wilayah_negara->NamaNegara->Visible) { // NamaNegara ?>
		<td<?php echo $master_wilayah_negara->NamaNegara->CellAttributes() ?>>
<span id="el<?php echo $master_wilayah_negara_delete->RowCnt ?>_master_wilayah_negara_NamaNegara" class="master_wilayah_negara_NamaNegara">
<span<?php echo $master_wilayah_negara->NamaNegara->ViewAttributes() ?>>
<?php echo $master_wilayah_negara->NamaNegara->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($master_wilayah_negara->NA->Visible) { // NA ?>
		<td<?php echo $master_wilayah_negara->NA->CellAttributes() ?>>
<span id="el<?php echo $master_wilayah_negara_delete->RowCnt ?>_master_wilayah_negara_NA" class="master_wilayah_negara_NA">
<span<?php echo $master_wilayah_negara->NA->ViewAttributes() ?>>
<?php echo $master_wilayah_negara->NA->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$master_wilayah_negara_delete->Recordset->MoveNext();
}
$master_wilayah_negara_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</div>
<div>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $master_wilayah_negara_delete->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fmaster_wilayah_negaradelete.Init();
</script>
<?php
$master_wilayah_negara_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_wilayah_negara_delete->Page_Terminate();
?>
