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

$program_search = NULL; // Initialize page object first

class cprogram_search extends cprogram {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'program';

	// Page object name
	var $PageObjName = 'program_search';

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
			define("EW_PAGE_ID", 'search', TRUE);

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
		if (!$Security->CanSearch()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("programlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->ProgramID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->KodeID->SetVisibility();
		$this->Def->SetVisibility();
		$this->LoginBuat->SetVisibility();
		$this->TanggalBuat->SetVisibility();
		$this->LoginEdit->SetVisibility();
		$this->TanggalEdit->SetVisibility();
		$this->Keterangan->SetVisibility();
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
	var $FormClassName = "form-horizontal ewForm ewSearchForm";
	var $IsModal = FALSE;
	var $SearchLabelClass = "col-sm-3 control-label ewLabel";
	var $SearchRightColumnClass = "col-sm-9";

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsSearchError;
		global $gbSkipHeaderFooter;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;
		$this->FormClassName = "ewForm ewSearchForm";
		if (ew_IsMobile() || $this->IsModal)
			$this->FormClassName = ew_Concat("form-horizontal", $this->FormClassName, " ");
		if ($this->IsPageRequest()) { // Validate request

			// Get action
			$this->CurrentAction = $objForm->GetValue("a_search");
			switch ($this->CurrentAction) {
				case "S": // Get search criteria

					// Build search string for advanced search, remove blank field
					$this->LoadSearchValues(); // Get search values
					if ($this->ValidateSearch()) {
						$sSrchStr = $this->BuildAdvancedSearch();
					} else {
						$sSrchStr = "";
						$this->setFailureMessage($gsSearchError);
					}
					if ($sSrchStr <> "") {
						$sSrchStr = $this->UrlParm($sSrchStr);
						$sSrchStr = "programlist.php" . "?" . $sSrchStr;
						$this->Page_Terminate($sSrchStr); // Go to list page
					}
			}
		}

		// Restore search settings from Session
		if ($gsSearchError == "")
			$this->LoadAdvancedSearch();

		// Render row for search
		$this->RowType = EW_ROWTYPE_SEARCH;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Build advanced search
	function BuildAdvancedSearch() {
		$sSrchUrl = "";
		$this->BuildSearchUrl($sSrchUrl, $this->ProgramID); // ProgramID
		$this->BuildSearchUrl($sSrchUrl, $this->Nama); // Nama
		$this->BuildSearchUrl($sSrchUrl, $this->KodeID); // KodeID
		$this->BuildSearchUrl($sSrchUrl, $this->Def); // Def
		$this->BuildSearchUrl($sSrchUrl, $this->LoginBuat); // LoginBuat
		$this->BuildSearchUrl($sSrchUrl, $this->TanggalBuat); // TanggalBuat
		$this->BuildSearchUrl($sSrchUrl, $this->LoginEdit); // LoginEdit
		$this->BuildSearchUrl($sSrchUrl, $this->TanggalEdit); // TanggalEdit
		$this->BuildSearchUrl($sSrchUrl, $this->Keterangan); // Keterangan
		$this->BuildSearchUrl($sSrchUrl, $this->NA); // NA
		if ($sSrchUrl <> "") $sSrchUrl .= "&";
		$sSrchUrl .= "cmd=search";
		return $sSrchUrl;
	}

	// Build search URL
	function BuildSearchUrl(&$Url, &$Fld, $OprOnly=FALSE) {
		global $objForm;
		$sWrk = "";
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $objForm->GetValue("x_$FldParm");
		$FldOpr = $objForm->GetValue("z_$FldParm");
		$FldCond = $objForm->GetValue("v_$FldParm");
		$FldVal2 = $objForm->GetValue("y_$FldParm");
		$FldOpr2 = $objForm->GetValue("w_$FldParm");
		$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
		$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		$lFldDataType = ($Fld->FldIsVirtual) ? EW_DATATYPE_STRING : $Fld->FldDataType;
		if ($FldOpr == "BETWEEN") {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal) && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal <> "" && $FldVal2 <> "" && $IsValidValue) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			}
		} else {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal));
			if ($FldVal <> "" && $IsValidValue && ew_IsValidOpr($FldOpr, $lFldDataType)) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			} elseif ($FldOpr == "IS NULL" || $FldOpr == "IS NOT NULL" || ($FldOpr <> "" && $OprOnly && ew_IsValidOpr($FldOpr, $lFldDataType))) {
				$sWrk = "z_" . $FldParm . "=" . urlencode($FldOpr);
			}
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal2 <> "" && $IsValidValue && ew_IsValidOpr($FldOpr2, $lFldDataType)) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&w_" . $FldParm . "=" . urlencode($FldOpr2);
			} elseif ($FldOpr2 == "IS NULL" || $FldOpr2 == "IS NOT NULL" || ($FldOpr2 <> "" && $OprOnly && ew_IsValidOpr($FldOpr2, $lFldDataType))) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "w_" . $FldParm . "=" . urlencode($FldOpr2);
			}
		}
		if ($sWrk <> "") {
			if ($Url <> "") $Url .= "&";
			$Url .= $sWrk;
		}
	}

	function SearchValueIsNumeric($Fld, $Value) {
		if (ew_IsFloatFormat($Fld->FldType)) $Value = ew_StrToFloat($Value);
		return is_numeric($Value);
	}

	// Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// ProgramID

		$this->ProgramID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ProgramID"));
		$this->ProgramID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ProgramID");

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Nama"));
		$this->Nama->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Nama");

		// KodeID
		$this->KodeID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KodeID"));
		$this->KodeID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KodeID");

		// Def
		$this->Def->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Def"));
		$this->Def->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Def");

		// LoginBuat
		$this->LoginBuat->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_LoginBuat"));
		$this->LoginBuat->AdvancedSearch->SearchOperator = $objForm->GetValue("z_LoginBuat");

		// TanggalBuat
		$this->TanggalBuat->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TanggalBuat"));
		$this->TanggalBuat->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TanggalBuat");

		// LoginEdit
		$this->LoginEdit->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_LoginEdit"));
		$this->LoginEdit->AdvancedSearch->SearchOperator = $objForm->GetValue("z_LoginEdit");

		// TanggalEdit
		$this->TanggalEdit->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TanggalEdit"));
		$this->TanggalEdit->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TanggalEdit");

		// Keterangan
		$this->Keterangan->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Keterangan"));
		$this->Keterangan->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Keterangan");

		// NA
		$this->NA->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NA"));
		$this->NA->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NA");
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

		// Keterangan
		$this->Keterangan->ViewValue = $this->Keterangan->CurrentValue;
		$this->Keterangan->ViewCustomAttributes = "";

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

			// Keterangan
			$this->Keterangan->LinkCustomAttributes = "";
			$this->Keterangan->HrefValue = "";
			$this->Keterangan->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// ProgramID
			$this->ProgramID->EditAttrs["class"] = "form-control";
			$this->ProgramID->EditCustomAttributes = "";
			$this->ProgramID->EditValue = ew_HtmlEncode($this->ProgramID->AdvancedSearch->SearchValue);
			$this->ProgramID->PlaceHolder = ew_RemoveHtml($this->ProgramID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->AdvancedSearch->SearchValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// KodeID
			$this->KodeID->EditAttrs["class"] = "form-control";
			$this->KodeID->EditCustomAttributes = "";
			$this->KodeID->EditValue = ew_HtmlEncode($this->KodeID->AdvancedSearch->SearchValue);
			$this->KodeID->PlaceHolder = ew_RemoveHtml($this->KodeID->FldCaption());

			// Def
			$this->Def->EditCustomAttributes = "";
			$this->Def->EditValue = $this->Def->Options(FALSE);

			// LoginBuat
			$this->LoginBuat->EditAttrs["class"] = "form-control";
			$this->LoginBuat->EditCustomAttributes = "";
			$this->LoginBuat->EditValue = ew_HtmlEncode($this->LoginBuat->AdvancedSearch->SearchValue);
			$this->LoginBuat->PlaceHolder = ew_RemoveHtml($this->LoginBuat->FldCaption());

			// TanggalBuat
			$this->TanggalBuat->EditAttrs["class"] = "form-control";
			$this->TanggalBuat->EditCustomAttributes = "";
			$this->TanggalBuat->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->TanggalBuat->AdvancedSearch->SearchValue, 0), 8));
			$this->TanggalBuat->PlaceHolder = ew_RemoveHtml($this->TanggalBuat->FldCaption());

			// LoginEdit
			$this->LoginEdit->EditAttrs["class"] = "form-control";
			$this->LoginEdit->EditCustomAttributes = "";
			$this->LoginEdit->EditValue = ew_HtmlEncode($this->LoginEdit->AdvancedSearch->SearchValue);
			$this->LoginEdit->PlaceHolder = ew_RemoveHtml($this->LoginEdit->FldCaption());

			// TanggalEdit
			$this->TanggalEdit->EditAttrs["class"] = "form-control";
			$this->TanggalEdit->EditCustomAttributes = "";
			$this->TanggalEdit->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->TanggalEdit->AdvancedSearch->SearchValue, 0), 8));
			$this->TanggalEdit->PlaceHolder = ew_RemoveHtml($this->TanggalEdit->FldCaption());

			// Keterangan
			$this->Keterangan->EditAttrs["class"] = "form-control";
			$this->Keterangan->EditCustomAttributes = "";
			$this->Keterangan->EditValue = ew_HtmlEncode($this->Keterangan->AdvancedSearch->SearchValue);
			$this->Keterangan->PlaceHolder = ew_RemoveHtml($this->Keterangan->FldCaption());

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);
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
		if (!ew_CheckDateDef($this->TanggalBuat->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TanggalBuat->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TanggalEdit->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TanggalEdit->FldErrMsg());
		}

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
		$this->ProgramID->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->KodeID->AdvancedSearch->Load();
		$this->Def->AdvancedSearch->Load();
		$this->LoginBuat->AdvancedSearch->Load();
		$this->TanggalBuat->AdvancedSearch->Load();
		$this->LoginEdit->AdvancedSearch->Load();
		$this->TanggalEdit->AdvancedSearch->Load();
		$this->Keterangan->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("programlist.php"), "", $this->TableVar, TRUE);
		$PageId = "search";
		$Breadcrumb->Add("search", $PageId, $url);
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
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($program_search)) $program_search = new cprogram_search();

// Page init
$program_search->Page_Init();

// Page main
$program_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$program_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($program_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fprogramsearch = new ew_Form("fprogramsearch", "search");
<?php } else { ?>
var CurrentForm = fprogramsearch = new ew_Form("fprogramsearch", "search");
<?php } ?>

// Form_CustomValidate event
fprogramsearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fprogramsearch.ValidateRequired = true;
<?php } else { ?>
fprogramsearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fprogramsearch.Lists["x_Def"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fprogramsearch.Lists["x_Def"].Options = <?php echo json_encode($program->Def->Options()) ?>;
fprogramsearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fprogramsearch.Lists["x_NA"].Options = <?php echo json_encode($program->NA->Options()) ?>;

// Form object for search
// Validate function for search

fprogramsearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_TanggalBuat");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($program->TanggalBuat->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_TanggalEdit");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($program->TanggalEdit->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$program_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $program_search->ShowPageHeader(); ?>
<?php
$program_search->ShowMessage();
?>
<form name="fprogramsearch" id="fprogramsearch" class="<?php echo $program_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($program_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $program_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="program">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($program_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$program_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_programsearch" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($program->ProgramID->Visible) { // ProgramID ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
	<div id="r_ProgramID" class="form-group">
		<label for="x_ProgramID" class="<?php echo $program_search->SearchLabelClass ?>"><span id="elh_program_ProgramID"><?php echo $program->ProgramID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProgramID" id="z_ProgramID" value="LIKE"></p>
		</label>
		<div class="<?php echo $program_search->SearchRightColumnClass ?>"><div<?php echo $program->ProgramID->CellAttributes() ?>>
			<span id="el_program_ProgramID">
<input type="text" data-table="program" data-field="x_ProgramID" name="x_ProgramID" id="x_ProgramID" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->ProgramID->getPlaceHolder()) ?>" value="<?php echo $program->ProgramID->EditValue ?>"<?php echo $program->ProgramID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProgramID">
		<td><span id="elh_program_ProgramID"><?php echo $program->ProgramID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProgramID" id="z_ProgramID" value="LIKE"></span></td>
		<td<?php echo $program->ProgramID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_program_ProgramID">
<input type="text" data-table="program" data-field="x_ProgramID" name="x_ProgramID" id="x_ProgramID" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->ProgramID->getPlaceHolder()) ?>" value="<?php echo $program->ProgramID->EditValue ?>"<?php echo $program->ProgramID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label for="x_Nama" class="<?php echo $program_search->SearchLabelClass ?>"><span id="elh_program_Nama"><?php echo $program->Nama->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></p>
		</label>
		<div class="<?php echo $program_search->SearchRightColumnClass ?>"><div<?php echo $program->Nama->CellAttributes() ?>>
			<span id="el_program_Nama">
<input type="text" data-table="program" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($program->Nama->getPlaceHolder()) ?>" value="<?php echo $program->Nama->EditValue ?>"<?php echo $program->Nama->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_program_Nama"><?php echo $program->Nama->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></span></td>
		<td<?php echo $program->Nama->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_program_Nama">
<input type="text" data-table="program" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($program->Nama->getPlaceHolder()) ?>" value="<?php echo $program->Nama->EditValue ?>"<?php echo $program->Nama->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->KodeID->Visible) { // KodeID ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
	<div id="r_KodeID" class="form-group">
		<label for="x_KodeID" class="<?php echo $program_search->SearchLabelClass ?>"><span id="elh_program_KodeID"><?php echo $program->KodeID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodeID" id="z_KodeID" value="LIKE"></p>
		</label>
		<div class="<?php echo $program_search->SearchRightColumnClass ?>"><div<?php echo $program->KodeID->CellAttributes() ?>>
			<span id="el_program_KodeID">
<input type="text" data-table="program" data-field="x_KodeID" name="x_KodeID" id="x_KodeID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($program->KodeID->getPlaceHolder()) ?>" value="<?php echo $program->KodeID->EditValue ?>"<?php echo $program->KodeID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodeID">
		<td><span id="elh_program_KodeID"><?php echo $program->KodeID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodeID" id="z_KodeID" value="LIKE"></span></td>
		<td<?php echo $program->KodeID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_program_KodeID">
<input type="text" data-table="program" data-field="x_KodeID" name="x_KodeID" id="x_KodeID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($program->KodeID->getPlaceHolder()) ?>" value="<?php echo $program->KodeID->EditValue ?>"<?php echo $program->KodeID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->Def->Visible) { // Def ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
	<div id="r_Def" class="form-group">
		<label class="<?php echo $program_search->SearchLabelClass ?>"><span id="elh_program_Def"><?php echo $program->Def->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Def" id="z_Def" value="="></p>
		</label>
		<div class="<?php echo $program_search->SearchRightColumnClass ?>"><div<?php echo $program->Def->CellAttributes() ?>>
			<span id="el_program_Def">
<div id="tp_x_Def" class="ewTemplate"><input type="radio" data-table="program" data-field="x_Def" data-value-separator="<?php echo $program->Def->DisplayValueSeparatorAttribute() ?>" name="x_Def" id="x_Def" value="{value}"<?php echo $program->Def->EditAttributes() ?>></div>
<div id="dsl_x_Def" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $program->Def->RadioButtonListHtml(FALSE, "x_Def") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Def">
		<td><span id="elh_program_Def"><?php echo $program->Def->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Def" id="z_Def" value="="></span></td>
		<td<?php echo $program->Def->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_program_Def">
<div id="tp_x_Def" class="ewTemplate"><input type="radio" data-table="program" data-field="x_Def" data-value-separator="<?php echo $program->Def->DisplayValueSeparatorAttribute() ?>" name="x_Def" id="x_Def" value="{value}"<?php echo $program->Def->EditAttributes() ?>></div>
<div id="dsl_x_Def" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $program->Def->RadioButtonListHtml(FALSE, "x_Def") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->LoginBuat->Visible) { // LoginBuat ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
	<div id="r_LoginBuat" class="form-group">
		<label for="x_LoginBuat" class="<?php echo $program_search->SearchLabelClass ?>"><span id="elh_program_LoginBuat"><?php echo $program->LoginBuat->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_LoginBuat" id="z_LoginBuat" value="LIKE"></p>
		</label>
		<div class="<?php echo $program_search->SearchRightColumnClass ?>"><div<?php echo $program->LoginBuat->CellAttributes() ?>>
			<span id="el_program_LoginBuat">
<input type="text" data-table="program" data-field="x_LoginBuat" name="x_LoginBuat" id="x_LoginBuat" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->LoginBuat->getPlaceHolder()) ?>" value="<?php echo $program->LoginBuat->EditValue ?>"<?php echo $program->LoginBuat->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_LoginBuat">
		<td><span id="elh_program_LoginBuat"><?php echo $program->LoginBuat->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_LoginBuat" id="z_LoginBuat" value="LIKE"></span></td>
		<td<?php echo $program->LoginBuat->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_program_LoginBuat">
<input type="text" data-table="program" data-field="x_LoginBuat" name="x_LoginBuat" id="x_LoginBuat" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->LoginBuat->getPlaceHolder()) ?>" value="<?php echo $program->LoginBuat->EditValue ?>"<?php echo $program->LoginBuat->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->TanggalBuat->Visible) { // TanggalBuat ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
	<div id="r_TanggalBuat" class="form-group">
		<label for="x_TanggalBuat" class="<?php echo $program_search->SearchLabelClass ?>"><span id="elh_program_TanggalBuat"><?php echo $program->TanggalBuat->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanggalBuat" id="z_TanggalBuat" value="="></p>
		</label>
		<div class="<?php echo $program_search->SearchRightColumnClass ?>"><div<?php echo $program->TanggalBuat->CellAttributes() ?>>
			<span id="el_program_TanggalBuat">
<input type="text" data-table="program" data-field="x_TanggalBuat" name="x_TanggalBuat" id="x_TanggalBuat" placeholder="<?php echo ew_HtmlEncode($program->TanggalBuat->getPlaceHolder()) ?>" value="<?php echo $program->TanggalBuat->EditValue ?>"<?php echo $program->TanggalBuat->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanggalBuat">
		<td><span id="elh_program_TanggalBuat"><?php echo $program->TanggalBuat->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanggalBuat" id="z_TanggalBuat" value="="></span></td>
		<td<?php echo $program->TanggalBuat->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_program_TanggalBuat">
<input type="text" data-table="program" data-field="x_TanggalBuat" name="x_TanggalBuat" id="x_TanggalBuat" placeholder="<?php echo ew_HtmlEncode($program->TanggalBuat->getPlaceHolder()) ?>" value="<?php echo $program->TanggalBuat->EditValue ?>"<?php echo $program->TanggalBuat->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->LoginEdit->Visible) { // LoginEdit ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
	<div id="r_LoginEdit" class="form-group">
		<label for="x_LoginEdit" class="<?php echo $program_search->SearchLabelClass ?>"><span id="elh_program_LoginEdit"><?php echo $program->LoginEdit->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_LoginEdit" id="z_LoginEdit" value="LIKE"></p>
		</label>
		<div class="<?php echo $program_search->SearchRightColumnClass ?>"><div<?php echo $program->LoginEdit->CellAttributes() ?>>
			<span id="el_program_LoginEdit">
<input type="text" data-table="program" data-field="x_LoginEdit" name="x_LoginEdit" id="x_LoginEdit" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->LoginEdit->getPlaceHolder()) ?>" value="<?php echo $program->LoginEdit->EditValue ?>"<?php echo $program->LoginEdit->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_LoginEdit">
		<td><span id="elh_program_LoginEdit"><?php echo $program->LoginEdit->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_LoginEdit" id="z_LoginEdit" value="LIKE"></span></td>
		<td<?php echo $program->LoginEdit->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_program_LoginEdit">
<input type="text" data-table="program" data-field="x_LoginEdit" name="x_LoginEdit" id="x_LoginEdit" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($program->LoginEdit->getPlaceHolder()) ?>" value="<?php echo $program->LoginEdit->EditValue ?>"<?php echo $program->LoginEdit->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->TanggalEdit->Visible) { // TanggalEdit ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
	<div id="r_TanggalEdit" class="form-group">
		<label for="x_TanggalEdit" class="<?php echo $program_search->SearchLabelClass ?>"><span id="elh_program_TanggalEdit"><?php echo $program->TanggalEdit->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanggalEdit" id="z_TanggalEdit" value="="></p>
		</label>
		<div class="<?php echo $program_search->SearchRightColumnClass ?>"><div<?php echo $program->TanggalEdit->CellAttributes() ?>>
			<span id="el_program_TanggalEdit">
<input type="text" data-table="program" data-field="x_TanggalEdit" name="x_TanggalEdit" id="x_TanggalEdit" placeholder="<?php echo ew_HtmlEncode($program->TanggalEdit->getPlaceHolder()) ?>" value="<?php echo $program->TanggalEdit->EditValue ?>"<?php echo $program->TanggalEdit->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanggalEdit">
		<td><span id="elh_program_TanggalEdit"><?php echo $program->TanggalEdit->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanggalEdit" id="z_TanggalEdit" value="="></span></td>
		<td<?php echo $program->TanggalEdit->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_program_TanggalEdit">
<input type="text" data-table="program" data-field="x_TanggalEdit" name="x_TanggalEdit" id="x_TanggalEdit" placeholder="<?php echo ew_HtmlEncode($program->TanggalEdit->getPlaceHolder()) ?>" value="<?php echo $program->TanggalEdit->EditValue ?>"<?php echo $program->TanggalEdit->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->Keterangan->Visible) { // Keterangan ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
	<div id="r_Keterangan" class="form-group">
		<label for="x_Keterangan" class="<?php echo $program_search->SearchLabelClass ?>"><span id="elh_program_Keterangan"><?php echo $program->Keterangan->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Keterangan" id="z_Keterangan" value="LIKE"></p>
		</label>
		<div class="<?php echo $program_search->SearchRightColumnClass ?>"><div<?php echo $program->Keterangan->CellAttributes() ?>>
			<span id="el_program_Keterangan">
<input type="text" data-table="program" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" size="35" placeholder="<?php echo ew_HtmlEncode($program->Keterangan->getPlaceHolder()) ?>" value="<?php echo $program->Keterangan->EditValue ?>"<?php echo $program->Keterangan->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Keterangan">
		<td><span id="elh_program_Keterangan"><?php echo $program->Keterangan->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Keterangan" id="z_Keterangan" value="LIKE"></span></td>
		<td<?php echo $program->Keterangan->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_program_Keterangan">
<input type="text" data-table="program" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" size="35" placeholder="<?php echo ew_HtmlEncode($program->Keterangan->getPlaceHolder()) ?>" value="<?php echo $program->Keterangan->EditValue ?>"<?php echo $program->Keterangan->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($program->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $program_search->SearchLabelClass ?>"><span id="elh_program_NA"><?php echo $program->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $program_search->SearchRightColumnClass ?>"><div<?php echo $program->NA->CellAttributes() ?>>
			<span id="el_program_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="program" data-field="x_NA" data-value-separator="<?php echo $program->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $program->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $program->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_program_NA"><?php echo $program->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $program->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_program_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="program" data-field="x_NA" data-value-separator="<?php echo $program->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $program->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $program->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $program_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$program_search->IsModal) { ?>
<?php if (ew_IsMobile()) { ?>
<div class="form-group">
	<div class="col-sm-offset-3 col-sm-9">
<?php } else { ?>
<div class="ewDesktopButton">
<?php } ?>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("Search") ?></button>
<button class="btn btn-default ewButton" name="btnReset" id="btnReset" type="button" onclick="ew_ClearForm(this.form);"><?php echo $Language->Phrase("Reset") ?></button>
<?php if (ew_IsMobile()) { ?>
	</div>
</div>
<?php } else { ?>
</div>
<?php } ?>
<?php } ?>
<?php if (!ew_IsMobile() && !$program_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fprogramsearch.Init();
</script>
<?php
$program_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$program_search->Page_Terminate();
?>
