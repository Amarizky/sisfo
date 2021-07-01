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

$master_wilayah_negara_search = NULL; // Initialize page object first

class cmaster_wilayah_negara_search extends cmaster_wilayah_negara {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_wilayah_negara';

	// Page object name
	var $PageObjName = 'master_wilayah_negara_search';

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
			define("EW_PAGE_ID", 'search', TRUE);

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
		if (!$Security->CanSearch()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("master_wilayah_negaralist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
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
						$sSrchStr = "master_wilayah_negaralist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->NegaraID); // NegaraID
		$this->BuildSearchUrl($sSrchUrl, $this->BenuaID); // BenuaID
		$this->BuildSearchUrl($sSrchUrl, $this->NamaBenua); // NamaBenua
		$this->BuildSearchUrl($sSrchUrl, $this->KodeNegara); // KodeNegara
		$this->BuildSearchUrl($sSrchUrl, $this->NamaNegara); // NamaNegara
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
		// NegaraID

		$this->NegaraID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NegaraID"));
		$this->NegaraID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NegaraID");

		// BenuaID
		$this->BenuaID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_BenuaID"));
		$this->BenuaID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_BenuaID");

		// NamaBenua
		$this->NamaBenua->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NamaBenua"));
		$this->NamaBenua->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NamaBenua");

		// KodeNegara
		$this->KodeNegara->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KodeNegara"));
		$this->KodeNegara->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KodeNegara");

		// NamaNegara
		$this->NamaNegara->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NamaNegara"));
		$this->NamaNegara->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NamaNegara");

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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// NegaraID
			$this->NegaraID->EditAttrs["class"] = "form-control";
			$this->NegaraID->EditCustomAttributes = "";
			$this->NegaraID->EditValue = ew_HtmlEncode($this->NegaraID->AdvancedSearch->SearchValue);
			$this->NegaraID->PlaceHolder = ew_RemoveHtml($this->NegaraID->FldCaption());

			// BenuaID
			$this->BenuaID->EditAttrs["class"] = "form-control";
			$this->BenuaID->EditCustomAttributes = "";
			$this->BenuaID->EditValue = ew_HtmlEncode($this->BenuaID->AdvancedSearch->SearchValue);
			$this->BenuaID->PlaceHolder = ew_RemoveHtml($this->BenuaID->FldCaption());

			// NamaBenua
			$this->NamaBenua->EditAttrs["class"] = "form-control";
			$this->NamaBenua->EditCustomAttributes = "";
			$this->NamaBenua->EditValue = ew_HtmlEncode($this->NamaBenua->AdvancedSearch->SearchValue);
			$this->NamaBenua->PlaceHolder = ew_RemoveHtml($this->NamaBenua->FldCaption());

			// KodeNegara
			$this->KodeNegara->EditAttrs["class"] = "form-control";
			$this->KodeNegara->EditCustomAttributes = "";
			$this->KodeNegara->EditValue = ew_HtmlEncode($this->KodeNegara->AdvancedSearch->SearchValue);
			$this->KodeNegara->PlaceHolder = ew_RemoveHtml($this->KodeNegara->FldCaption());

			// NamaNegara
			$this->NamaNegara->EditAttrs["class"] = "form-control";
			$this->NamaNegara->EditCustomAttributes = "";
			$this->NamaNegara->EditValue = ew_HtmlEncode($this->NamaNegara->AdvancedSearch->SearchValue);
			$this->NamaNegara->PlaceHolder = ew_RemoveHtml($this->NamaNegara->FldCaption());

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
		if (!ew_CheckInteger($this->NegaraID->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->NegaraID->FldErrMsg());
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
		$this->NegaraID->AdvancedSearch->Load();
		$this->BenuaID->AdvancedSearch->Load();
		$this->NamaBenua->AdvancedSearch->Load();
		$this->KodeNegara->AdvancedSearch->Load();
		$this->NamaNegara->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_wilayah_negaralist.php"), "", $this->TableVar, TRUE);
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
if (!isset($master_wilayah_negara_search)) $master_wilayah_negara_search = new cmaster_wilayah_negara_search();

// Page init
$master_wilayah_negara_search->Page_Init();

// Page main
$master_wilayah_negara_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_wilayah_negara_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($master_wilayah_negara_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fmaster_wilayah_negarasearch = new ew_Form("fmaster_wilayah_negarasearch", "search");
<?php } else { ?>
var CurrentForm = fmaster_wilayah_negarasearch = new ew_Form("fmaster_wilayah_negarasearch", "search");
<?php } ?>

// Form_CustomValidate event
fmaster_wilayah_negarasearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_wilayah_negarasearch.ValidateRequired = true;
<?php } else { ?>
fmaster_wilayah_negarasearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_wilayah_negarasearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_wilayah_negarasearch.Lists["x_NA"].Options = <?php echo json_encode($master_wilayah_negara->NA->Options()) ?>;

// Form object for search
// Validate function for search

fmaster_wilayah_negarasearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_NegaraID");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($master_wilayah_negara->NegaraID->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$master_wilayah_negara_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_wilayah_negara_search->ShowPageHeader(); ?>
<?php
$master_wilayah_negara_search->ShowMessage();
?>
<form name="fmaster_wilayah_negarasearch" id="fmaster_wilayah_negarasearch" class="<?php echo $master_wilayah_negara_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_wilayah_negara_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_wilayah_negara_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_wilayah_negara">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($master_wilayah_negara_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_wilayah_negara_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_wilayah_negarasearch" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_wilayah_negara->NegaraID->Visible) { // NegaraID ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_search->IsModal) { ?>
	<div id="r_NegaraID" class="form-group">
		<label for="x_NegaraID" class="<?php echo $master_wilayah_negara_search->SearchLabelClass ?>"><span id="elh_master_wilayah_negara_NegaraID"><?php echo $master_wilayah_negara->NegaraID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NegaraID" id="z_NegaraID" value="="></p>
		</label>
		<div class="<?php echo $master_wilayah_negara_search->SearchRightColumnClass ?>"><div<?php echo $master_wilayah_negara->NegaraID->CellAttributes() ?>>
			<span id="el_master_wilayah_negara_NegaraID">
<input type="text" data-table="master_wilayah_negara" data-field="x_NegaraID" name="x_NegaraID" id="x_NegaraID" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->NegaraID->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->NegaraID->EditValue ?>"<?php echo $master_wilayah_negara->NegaraID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NegaraID">
		<td><span id="elh_master_wilayah_negara_NegaraID"><?php echo $master_wilayah_negara->NegaraID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NegaraID" id="z_NegaraID" value="="></span></td>
		<td<?php echo $master_wilayah_negara->NegaraID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_wilayah_negara_NegaraID">
<input type="text" data-table="master_wilayah_negara" data-field="x_NegaraID" name="x_NegaraID" id="x_NegaraID" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->NegaraID->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->NegaraID->EditValue ?>"<?php echo $master_wilayah_negara->NegaraID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_negara->BenuaID->Visible) { // BenuaID ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_search->IsModal) { ?>
	<div id="r_BenuaID" class="form-group">
		<label for="x_BenuaID" class="<?php echo $master_wilayah_negara_search->SearchLabelClass ?>"><span id="elh_master_wilayah_negara_BenuaID"><?php echo $master_wilayah_negara->BenuaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_BenuaID" id="z_BenuaID" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_wilayah_negara_search->SearchRightColumnClass ?>"><div<?php echo $master_wilayah_negara->BenuaID->CellAttributes() ?>>
			<span id="el_master_wilayah_negara_BenuaID">
<input type="text" data-table="master_wilayah_negara" data-field="x_BenuaID" name="x_BenuaID" id="x_BenuaID" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->BenuaID->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->BenuaID->EditValue ?>"<?php echo $master_wilayah_negara->BenuaID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_BenuaID">
		<td><span id="elh_master_wilayah_negara_BenuaID"><?php echo $master_wilayah_negara->BenuaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_BenuaID" id="z_BenuaID" value="LIKE"></span></td>
		<td<?php echo $master_wilayah_negara->BenuaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_wilayah_negara_BenuaID">
<input type="text" data-table="master_wilayah_negara" data-field="x_BenuaID" name="x_BenuaID" id="x_BenuaID" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->BenuaID->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->BenuaID->EditValue ?>"<?php echo $master_wilayah_negara->BenuaID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_negara->NamaBenua->Visible) { // NamaBenua ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_search->IsModal) { ?>
	<div id="r_NamaBenua" class="form-group">
		<label for="x_NamaBenua" class="<?php echo $master_wilayah_negara_search->SearchLabelClass ?>"><span id="elh_master_wilayah_negara_NamaBenua"><?php echo $master_wilayah_negara->NamaBenua->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NamaBenua" id="z_NamaBenua" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_wilayah_negara_search->SearchRightColumnClass ?>"><div<?php echo $master_wilayah_negara->NamaBenua->CellAttributes() ?>>
			<span id="el_master_wilayah_negara_NamaBenua">
<input type="text" data-table="master_wilayah_negara" data-field="x_NamaBenua" name="x_NamaBenua" id="x_NamaBenua" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->NamaBenua->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->NamaBenua->EditValue ?>"<?php echo $master_wilayah_negara->NamaBenua->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NamaBenua">
		<td><span id="elh_master_wilayah_negara_NamaBenua"><?php echo $master_wilayah_negara->NamaBenua->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NamaBenua" id="z_NamaBenua" value="LIKE"></span></td>
		<td<?php echo $master_wilayah_negara->NamaBenua->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_wilayah_negara_NamaBenua">
<input type="text" data-table="master_wilayah_negara" data-field="x_NamaBenua" name="x_NamaBenua" id="x_NamaBenua" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->NamaBenua->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->NamaBenua->EditValue ?>"<?php echo $master_wilayah_negara->NamaBenua->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_negara->KodeNegara->Visible) { // KodeNegara ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_search->IsModal) { ?>
	<div id="r_KodeNegara" class="form-group">
		<label for="x_KodeNegara" class="<?php echo $master_wilayah_negara_search->SearchLabelClass ?>"><span id="elh_master_wilayah_negara_KodeNegara"><?php echo $master_wilayah_negara->KodeNegara->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodeNegara" id="z_KodeNegara" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_wilayah_negara_search->SearchRightColumnClass ?>"><div<?php echo $master_wilayah_negara->KodeNegara->CellAttributes() ?>>
			<span id="el_master_wilayah_negara_KodeNegara">
<input type="text" data-table="master_wilayah_negara" data-field="x_KodeNegara" name="x_KodeNegara" id="x_KodeNegara" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->KodeNegara->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->KodeNegara->EditValue ?>"<?php echo $master_wilayah_negara->KodeNegara->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodeNegara">
		<td><span id="elh_master_wilayah_negara_KodeNegara"><?php echo $master_wilayah_negara->KodeNegara->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodeNegara" id="z_KodeNegara" value="LIKE"></span></td>
		<td<?php echo $master_wilayah_negara->KodeNegara->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_wilayah_negara_KodeNegara">
<input type="text" data-table="master_wilayah_negara" data-field="x_KodeNegara" name="x_KodeNegara" id="x_KodeNegara" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->KodeNegara->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->KodeNegara->EditValue ?>"<?php echo $master_wilayah_negara->KodeNegara->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_negara->NamaNegara->Visible) { // NamaNegara ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_search->IsModal) { ?>
	<div id="r_NamaNegara" class="form-group">
		<label for="x_NamaNegara" class="<?php echo $master_wilayah_negara_search->SearchLabelClass ?>"><span id="elh_master_wilayah_negara_NamaNegara"><?php echo $master_wilayah_negara->NamaNegara->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NamaNegara" id="z_NamaNegara" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_wilayah_negara_search->SearchRightColumnClass ?>"><div<?php echo $master_wilayah_negara->NamaNegara->CellAttributes() ?>>
			<span id="el_master_wilayah_negara_NamaNegara">
<input type="text" data-table="master_wilayah_negara" data-field="x_NamaNegara" name="x_NamaNegara" id="x_NamaNegara" size="30" maxlength="70" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->NamaNegara->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->NamaNegara->EditValue ?>"<?php echo $master_wilayah_negara->NamaNegara->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NamaNegara">
		<td><span id="elh_master_wilayah_negara_NamaNegara"><?php echo $master_wilayah_negara->NamaNegara->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NamaNegara" id="z_NamaNegara" value="LIKE"></span></td>
		<td<?php echo $master_wilayah_negara->NamaNegara->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_wilayah_negara_NamaNegara">
<input type="text" data-table="master_wilayah_negara" data-field="x_NamaNegara" name="x_NamaNegara" id="x_NamaNegara" size="30" maxlength="70" placeholder="<?php echo ew_HtmlEncode($master_wilayah_negara->NamaNegara->getPlaceHolder()) ?>" value="<?php echo $master_wilayah_negara->NamaNegara->EditValue ?>"<?php echo $master_wilayah_negara->NamaNegara->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_wilayah_negara->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $master_wilayah_negara_search->SearchLabelClass ?>"><span id="elh_master_wilayah_negara_NA"><?php echo $master_wilayah_negara->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $master_wilayah_negara_search->SearchRightColumnClass ?>"><div<?php echo $master_wilayah_negara->NA->CellAttributes() ?>>
			<span id="el_master_wilayah_negara_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_wilayah_negara" data-field="x_NA" data-value-separator="<?php echo $master_wilayah_negara->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_wilayah_negara->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_wilayah_negara->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_master_wilayah_negara_NA"><?php echo $master_wilayah_negara->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $master_wilayah_negara->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_wilayah_negara_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_wilayah_negara" data-field="x_NA" data-value-separator="<?php echo $master_wilayah_negara->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_wilayah_negara->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_wilayah_negara->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_wilayah_negara_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$master_wilayah_negara_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$master_wilayah_negara_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_wilayah_negarasearch.Init();
</script>
<?php
$master_wilayah_negara_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_wilayah_negara_search->Page_Terminate();
?>
