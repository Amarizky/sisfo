<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "pejabatinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$pejabat_search = NULL; // Initialize page object first

class cpejabat_search extends cpejabat {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'pejabat';

	// Page object name
	var $PageObjName = 'pejabat_search';

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

		// Table object (pejabat)
		if (!isset($GLOBALS["pejabat"]) || get_class($GLOBALS["pejabat"]) == "cpejabat") {
			$GLOBALS["pejabat"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["pejabat"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'pejabat', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("pejabatlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->JabatanID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->Report->SetVisibility();
		$this->NA->SetVisibility();
		$this->KodeID->SetVisibility();
		$this->LevelID->SetVisibility();
		$this->Ranking->SetVisibility();

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
		global $EW_EXPORT, $pejabat;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($pejabat);
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
						$sSrchStr = "pejabatlist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->JabatanID); // JabatanID
		$this->BuildSearchUrl($sSrchUrl, $this->Nama); // Nama
		$this->BuildSearchUrl($sSrchUrl, $this->Report); // Report
		$this->BuildSearchUrl($sSrchUrl, $this->NA); // NA
		$this->BuildSearchUrl($sSrchUrl, $this->KodeID); // KodeID
		$this->BuildSearchUrl($sSrchUrl, $this->LevelID); // LevelID
		$this->BuildSearchUrl($sSrchUrl, $this->Ranking); // Ranking
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
		// JabatanID

		$this->JabatanID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_JabatanID"));
		$this->JabatanID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_JabatanID");

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Nama"));
		$this->Nama->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Nama");

		// Report
		$this->Report->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Report"));
		$this->Report->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Report");

		// NA
		$this->NA->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NA"));
		$this->NA->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NA");

		// KodeID
		$this->KodeID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KodeID"));
		$this->KodeID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KodeID");

		// LevelID
		$this->LevelID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_LevelID"));
		$this->LevelID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_LevelID");

		// Ranking
		$this->Ranking->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Ranking"));
		$this->Ranking->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Ranking");
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// JabatanID
		// Nama
		// Report
		// NA
		// KodeID
		// LevelID
		// Ranking

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// JabatanID
		$this->JabatanID->ViewValue = $this->JabatanID->CurrentValue;
		$this->JabatanID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// Report
		$this->Report->ViewValue = $this->Report->CurrentValue;
		$this->Report->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

		// KodeID
		$this->KodeID->ViewValue = $this->KodeID->CurrentValue;
		$this->KodeID->ViewCustomAttributes = "";

		// LevelID
		$this->LevelID->ViewValue = $this->LevelID->CurrentValue;
		$this->LevelID->ViewCustomAttributes = "";

		// Ranking
		$this->Ranking->ViewValue = $this->Ranking->CurrentValue;
		$this->Ranking->ViewCustomAttributes = "";

			// JabatanID
			$this->JabatanID->LinkCustomAttributes = "";
			$this->JabatanID->HrefValue = "";
			$this->JabatanID->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// Report
			$this->Report->LinkCustomAttributes = "";
			$this->Report->HrefValue = "";
			$this->Report->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";

			// KodeID
			$this->KodeID->LinkCustomAttributes = "";
			$this->KodeID->HrefValue = "";
			$this->KodeID->TooltipValue = "";

			// LevelID
			$this->LevelID->LinkCustomAttributes = "";
			$this->LevelID->HrefValue = "";
			$this->LevelID->TooltipValue = "";

			// Ranking
			$this->Ranking->LinkCustomAttributes = "";
			$this->Ranking->HrefValue = "";
			$this->Ranking->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// JabatanID
			$this->JabatanID->EditAttrs["class"] = "form-control";
			$this->JabatanID->EditCustomAttributes = "";
			$this->JabatanID->EditValue = ew_HtmlEncode($this->JabatanID->AdvancedSearch->SearchValue);
			$this->JabatanID->PlaceHolder = ew_RemoveHtml($this->JabatanID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->AdvancedSearch->SearchValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// Report
			$this->Report->EditAttrs["class"] = "form-control";
			$this->Report->EditCustomAttributes = "";
			$this->Report->EditValue = ew_HtmlEncode($this->Report->AdvancedSearch->SearchValue);
			$this->Report->PlaceHolder = ew_RemoveHtml($this->Report->FldCaption());

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// KodeID
			$this->KodeID->EditAttrs["class"] = "form-control";
			$this->KodeID->EditCustomAttributes = "";
			$this->KodeID->EditValue = ew_HtmlEncode($this->KodeID->AdvancedSearch->SearchValue);
			$this->KodeID->PlaceHolder = ew_RemoveHtml($this->KodeID->FldCaption());

			// LevelID
			$this->LevelID->EditAttrs["class"] = "form-control";
			$this->LevelID->EditCustomAttributes = "";
			$this->LevelID->EditValue = ew_HtmlEncode($this->LevelID->AdvancedSearch->SearchValue);
			$this->LevelID->PlaceHolder = ew_RemoveHtml($this->LevelID->FldCaption());

			// Ranking
			$this->Ranking->EditAttrs["class"] = "form-control";
			$this->Ranking->EditCustomAttributes = "";
			$this->Ranking->EditValue = ew_HtmlEncode($this->Ranking->AdvancedSearch->SearchValue);
			$this->Ranking->PlaceHolder = ew_RemoveHtml($this->Ranking->FldCaption());
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
		if (!ew_CheckInteger($this->Ranking->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Ranking->FldErrMsg());
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
		$this->JabatanID->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->Report->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
		$this->KodeID->AdvancedSearch->Load();
		$this->LevelID->AdvancedSearch->Load();
		$this->Ranking->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("pejabatlist.php"), "", $this->TableVar, TRUE);
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
if (!isset($pejabat_search)) $pejabat_search = new cpejabat_search();

// Page init
$pejabat_search->Page_Init();

// Page main
$pejabat_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$pejabat_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($pejabat_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fpejabatsearch = new ew_Form("fpejabatsearch", "search");
<?php } else { ?>
var CurrentForm = fpejabatsearch = new ew_Form("fpejabatsearch", "search");
<?php } ?>

// Form_CustomValidate event
fpejabatsearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fpejabatsearch.ValidateRequired = true;
<?php } else { ?>
fpejabatsearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fpejabatsearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fpejabatsearch.Lists["x_NA"].Options = <?php echo json_encode($pejabat->NA->Options()) ?>;

// Form object for search
// Validate function for search

fpejabatsearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_Ranking");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($pejabat->Ranking->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$pejabat_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $pejabat_search->ShowPageHeader(); ?>
<?php
$pejabat_search->ShowMessage();
?>
<form name="fpejabatsearch" id="fpejabatsearch" class="<?php echo $pejabat_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($pejabat_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $pejabat_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="pejabat">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($pejabat_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$pejabat_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $pejabat_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_pejabatsearch" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($pejabat->JabatanID->Visible) { // JabatanID ?>
<?php if (ew_IsMobile() || $pejabat_search->IsModal) { ?>
	<div id="r_JabatanID" class="form-group">
		<label for="x_JabatanID" class="<?php echo $pejabat_search->SearchLabelClass ?>"><span id="elh_pejabat_JabatanID"><?php echo $pejabat->JabatanID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_JabatanID" id="z_JabatanID" value="LIKE"></p>
		</label>
		<div class="<?php echo $pejabat_search->SearchRightColumnClass ?>"><div<?php echo $pejabat->JabatanID->CellAttributes() ?>>
			<span id="el_pejabat_JabatanID">
<input type="text" data-table="pejabat" data-field="x_JabatanID" name="x_JabatanID" id="x_JabatanID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($pejabat->JabatanID->getPlaceHolder()) ?>" value="<?php echo $pejabat->JabatanID->EditValue ?>"<?php echo $pejabat->JabatanID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_JabatanID">
		<td><span id="elh_pejabat_JabatanID"><?php echo $pejabat->JabatanID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_JabatanID" id="z_JabatanID" value="LIKE"></span></td>
		<td<?php echo $pejabat->JabatanID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pejabat_JabatanID">
<input type="text" data-table="pejabat" data-field="x_JabatanID" name="x_JabatanID" id="x_JabatanID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($pejabat->JabatanID->getPlaceHolder()) ?>" value="<?php echo $pejabat->JabatanID->EditValue ?>"<?php echo $pejabat->JabatanID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($pejabat->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $pejabat_search->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label for="x_Nama" class="<?php echo $pejabat_search->SearchLabelClass ?>"><span id="elh_pejabat_Nama"><?php echo $pejabat->Nama->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></p>
		</label>
		<div class="<?php echo $pejabat_search->SearchRightColumnClass ?>"><div<?php echo $pejabat->Nama->CellAttributes() ?>>
			<span id="el_pejabat_Nama">
<input type="text" data-table="pejabat" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($pejabat->Nama->getPlaceHolder()) ?>" value="<?php echo $pejabat->Nama->EditValue ?>"<?php echo $pejabat->Nama->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_pejabat_Nama"><?php echo $pejabat->Nama->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></span></td>
		<td<?php echo $pejabat->Nama->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pejabat_Nama">
<input type="text" data-table="pejabat" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($pejabat->Nama->getPlaceHolder()) ?>" value="<?php echo $pejabat->Nama->EditValue ?>"<?php echo $pejabat->Nama->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($pejabat->Report->Visible) { // Report ?>
<?php if (ew_IsMobile() || $pejabat_search->IsModal) { ?>
	<div id="r_Report" class="form-group">
		<label for="x_Report" class="<?php echo $pejabat_search->SearchLabelClass ?>"><span id="elh_pejabat_Report"><?php echo $pejabat->Report->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Report" id="z_Report" value="LIKE"></p>
		</label>
		<div class="<?php echo $pejabat_search->SearchRightColumnClass ?>"><div<?php echo $pejabat->Report->CellAttributes() ?>>
			<span id="el_pejabat_Report">
<input type="text" data-table="pejabat" data-field="x_Report" name="x_Report" id="x_Report" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pejabat->Report->getPlaceHolder()) ?>" value="<?php echo $pejabat->Report->EditValue ?>"<?php echo $pejabat->Report->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Report">
		<td><span id="elh_pejabat_Report"><?php echo $pejabat->Report->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Report" id="z_Report" value="LIKE"></span></td>
		<td<?php echo $pejabat->Report->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pejabat_Report">
<input type="text" data-table="pejabat" data-field="x_Report" name="x_Report" id="x_Report" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pejabat->Report->getPlaceHolder()) ?>" value="<?php echo $pejabat->Report->EditValue ?>"<?php echo $pejabat->Report->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($pejabat->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $pejabat_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $pejabat_search->SearchLabelClass ?>"><span id="elh_pejabat_NA"><?php echo $pejabat->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $pejabat_search->SearchRightColumnClass ?>"><div<?php echo $pejabat->NA->CellAttributes() ?>>
			<span id="el_pejabat_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="pejabat" data-field="x_NA" data-value-separator="<?php echo $pejabat->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $pejabat->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $pejabat->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_pejabat_NA"><?php echo $pejabat->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $pejabat->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pejabat_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="pejabat" data-field="x_NA" data-value-separator="<?php echo $pejabat->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $pejabat->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $pejabat->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($pejabat->KodeID->Visible) { // KodeID ?>
<?php if (ew_IsMobile() || $pejabat_search->IsModal) { ?>
	<div id="r_KodeID" class="form-group">
		<label for="x_KodeID" class="<?php echo $pejabat_search->SearchLabelClass ?>"><span id="elh_pejabat_KodeID"><?php echo $pejabat->KodeID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodeID" id="z_KodeID" value="LIKE"></p>
		</label>
		<div class="<?php echo $pejabat_search->SearchRightColumnClass ?>"><div<?php echo $pejabat->KodeID->CellAttributes() ?>>
			<span id="el_pejabat_KodeID">
<input type="text" data-table="pejabat" data-field="x_KodeID" name="x_KodeID" id="x_KodeID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($pejabat->KodeID->getPlaceHolder()) ?>" value="<?php echo $pejabat->KodeID->EditValue ?>"<?php echo $pejabat->KodeID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodeID">
		<td><span id="elh_pejabat_KodeID"><?php echo $pejabat->KodeID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodeID" id="z_KodeID" value="LIKE"></span></td>
		<td<?php echo $pejabat->KodeID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pejabat_KodeID">
<input type="text" data-table="pejabat" data-field="x_KodeID" name="x_KodeID" id="x_KodeID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($pejabat->KodeID->getPlaceHolder()) ?>" value="<?php echo $pejabat->KodeID->EditValue ?>"<?php echo $pejabat->KodeID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($pejabat->LevelID->Visible) { // LevelID ?>
<?php if (ew_IsMobile() || $pejabat_search->IsModal) { ?>
	<div id="r_LevelID" class="form-group">
		<label for="x_LevelID" class="<?php echo $pejabat_search->SearchLabelClass ?>"><span id="elh_pejabat_LevelID"><?php echo $pejabat->LevelID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_LevelID" id="z_LevelID" value="LIKE"></p>
		</label>
		<div class="<?php echo $pejabat_search->SearchRightColumnClass ?>"><div<?php echo $pejabat->LevelID->CellAttributes() ?>>
			<span id="el_pejabat_LevelID">
<input type="text" data-table="pejabat" data-field="x_LevelID" name="x_LevelID" id="x_LevelID" size="30" maxlength="5" placeholder="<?php echo ew_HtmlEncode($pejabat->LevelID->getPlaceHolder()) ?>" value="<?php echo $pejabat->LevelID->EditValue ?>"<?php echo $pejabat->LevelID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_LevelID">
		<td><span id="elh_pejabat_LevelID"><?php echo $pejabat->LevelID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_LevelID" id="z_LevelID" value="LIKE"></span></td>
		<td<?php echo $pejabat->LevelID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pejabat_LevelID">
<input type="text" data-table="pejabat" data-field="x_LevelID" name="x_LevelID" id="x_LevelID" size="30" maxlength="5" placeholder="<?php echo ew_HtmlEncode($pejabat->LevelID->getPlaceHolder()) ?>" value="<?php echo $pejabat->LevelID->EditValue ?>"<?php echo $pejabat->LevelID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($pejabat->Ranking->Visible) { // Ranking ?>
<?php if (ew_IsMobile() || $pejabat_search->IsModal) { ?>
	<div id="r_Ranking" class="form-group">
		<label for="x_Ranking" class="<?php echo $pejabat_search->SearchLabelClass ?>"><span id="elh_pejabat_Ranking"><?php echo $pejabat->Ranking->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Ranking" id="z_Ranking" value="="></p>
		</label>
		<div class="<?php echo $pejabat_search->SearchRightColumnClass ?>"><div<?php echo $pejabat->Ranking->CellAttributes() ?>>
			<span id="el_pejabat_Ranking">
<input type="text" data-table="pejabat" data-field="x_Ranking" name="x_Ranking" id="x_Ranking" size="30" placeholder="<?php echo ew_HtmlEncode($pejabat->Ranking->getPlaceHolder()) ?>" value="<?php echo $pejabat->Ranking->EditValue ?>"<?php echo $pejabat->Ranking->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Ranking">
		<td><span id="elh_pejabat_Ranking"><?php echo $pejabat->Ranking->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Ranking" id="z_Ranking" value="="></span></td>
		<td<?php echo $pejabat->Ranking->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pejabat_Ranking">
<input type="text" data-table="pejabat" data-field="x_Ranking" name="x_Ranking" id="x_Ranking" size="30" placeholder="<?php echo ew_HtmlEncode($pejabat->Ranking->getPlaceHolder()) ?>" value="<?php echo $pejabat->Ranking->EditValue ?>"<?php echo $pejabat->Ranking->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $pejabat_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$pejabat_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$pejabat_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fpejabatsearch.Init();
</script>
<?php
$pejabat_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$pejabat_search->Page_Terminate();
?>
