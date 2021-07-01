<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "ruanginfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$ruang_search = NULL; // Initialize page object first

class cruang_search extends cruang {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'ruang';

	// Page object name
	var $PageObjName = 'ruang_search';

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

		// Table object (ruang)
		if (!isset($GLOBALS["ruang"]) || get_class($GLOBALS["ruang"]) == "cruang") {
			$GLOBALS["ruang"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["ruang"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'ruang', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("ruanglist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->RuangID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->KampusID->SetVisibility();
		$this->Lantai->SetVisibility();
		$this->ProdiID->SetVisibility();
		$this->Kapasitas->SetVisibility();
		$this->KapasitasUjian->SetVisibility();
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
		global $EW_EXPORT, $ruang;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($ruang);
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
						$sSrchStr = "ruanglist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->RuangID); // RuangID
		$this->BuildSearchUrl($sSrchUrl, $this->Nama); // Nama
		$this->BuildSearchUrl($sSrchUrl, $this->KampusID); // KampusID
		$this->BuildSearchUrl($sSrchUrl, $this->Lantai); // Lantai
		$this->BuildSearchUrl($sSrchUrl, $this->ProdiID); // ProdiID
		$this->BuildSearchUrl($sSrchUrl, $this->Kapasitas); // Kapasitas
		$this->BuildSearchUrl($sSrchUrl, $this->KapasitasUjian); // KapasitasUjian
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
		// RuangID

		$this->RuangID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_RuangID"));
		$this->RuangID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_RuangID");

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Nama"));
		$this->Nama->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Nama");

		// KampusID
		$this->KampusID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KampusID"));
		$this->KampusID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KampusID");

		// Lantai
		$this->Lantai->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Lantai"));
		$this->Lantai->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Lantai");

		// ProdiID
		$this->ProdiID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ProdiID"));
		$this->ProdiID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ProdiID");

		// Kapasitas
		$this->Kapasitas->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Kapasitas"));
		$this->Kapasitas->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Kapasitas");

		// KapasitasUjian
		$this->KapasitasUjian->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KapasitasUjian"));
		$this->KapasitasUjian->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KapasitasUjian");

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
		// RuangID
		// Nama
		// KampusID
		// Lantai
		// ProdiID
		// Kapasitas
		// KapasitasUjian
		// Keterangan
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// RuangID
		$this->RuangID->ViewValue = $this->RuangID->CurrentValue;
		$this->RuangID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// KampusID
		$this->KampusID->ViewValue = $this->KampusID->CurrentValue;
		$this->KampusID->ViewCustomAttributes = "";

		// Lantai
		$this->Lantai->ViewValue = $this->Lantai->CurrentValue;
		$this->Lantai->ViewCustomAttributes = "";

		// ProdiID
		$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
		$this->ProdiID->ViewCustomAttributes = "";

		// Kapasitas
		$this->Kapasitas->ViewValue = $this->Kapasitas->CurrentValue;
		$this->Kapasitas->ViewCustomAttributes = "";

		// KapasitasUjian
		$this->KapasitasUjian->ViewValue = $this->KapasitasUjian->CurrentValue;
		$this->KapasitasUjian->ViewCustomAttributes = "";

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

			// RuangID
			$this->RuangID->LinkCustomAttributes = "";
			$this->RuangID->HrefValue = "";
			$this->RuangID->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";
			$this->KampusID->TooltipValue = "";

			// Lantai
			$this->Lantai->LinkCustomAttributes = "";
			$this->Lantai->HrefValue = "";
			$this->Lantai->TooltipValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// Kapasitas
			$this->Kapasitas->LinkCustomAttributes = "";
			$this->Kapasitas->HrefValue = "";
			$this->Kapasitas->TooltipValue = "";

			// KapasitasUjian
			$this->KapasitasUjian->LinkCustomAttributes = "";
			$this->KapasitasUjian->HrefValue = "";
			$this->KapasitasUjian->TooltipValue = "";

			// Keterangan
			$this->Keterangan->LinkCustomAttributes = "";
			$this->Keterangan->HrefValue = "";
			$this->Keterangan->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// RuangID
			$this->RuangID->EditAttrs["class"] = "form-control";
			$this->RuangID->EditCustomAttributes = "";
			$this->RuangID->EditValue = ew_HtmlEncode($this->RuangID->AdvancedSearch->SearchValue);
			$this->RuangID->PlaceHolder = ew_RemoveHtml($this->RuangID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->AdvancedSearch->SearchValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// KampusID
			$this->KampusID->EditAttrs["class"] = "form-control";
			$this->KampusID->EditCustomAttributes = "";
			$this->KampusID->EditValue = ew_HtmlEncode($this->KampusID->AdvancedSearch->SearchValue);
			$this->KampusID->PlaceHolder = ew_RemoveHtml($this->KampusID->FldCaption());

			// Lantai
			$this->Lantai->EditAttrs["class"] = "form-control";
			$this->Lantai->EditCustomAttributes = "";
			$this->Lantai->EditValue = ew_HtmlEncode($this->Lantai->AdvancedSearch->SearchValue);
			$this->Lantai->PlaceHolder = ew_RemoveHtml($this->Lantai->FldCaption());

			// ProdiID
			$this->ProdiID->EditAttrs["class"] = "form-control";
			$this->ProdiID->EditCustomAttributes = "";
			$this->ProdiID->EditValue = ew_HtmlEncode($this->ProdiID->AdvancedSearch->SearchValue);
			$this->ProdiID->PlaceHolder = ew_RemoveHtml($this->ProdiID->FldCaption());

			// Kapasitas
			$this->Kapasitas->EditAttrs["class"] = "form-control";
			$this->Kapasitas->EditCustomAttributes = "";
			$this->Kapasitas->EditValue = ew_HtmlEncode($this->Kapasitas->AdvancedSearch->SearchValue);
			$this->Kapasitas->PlaceHolder = ew_RemoveHtml($this->Kapasitas->FldCaption());

			// KapasitasUjian
			$this->KapasitasUjian->EditAttrs["class"] = "form-control";
			$this->KapasitasUjian->EditCustomAttributes = "";
			$this->KapasitasUjian->EditValue = ew_HtmlEncode($this->KapasitasUjian->AdvancedSearch->SearchValue);
			$this->KapasitasUjian->PlaceHolder = ew_RemoveHtml($this->KapasitasUjian->FldCaption());

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
		if (!ew_CheckInteger($this->Lantai->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Lantai->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Kapasitas->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Kapasitas->FldErrMsg());
		}
		if (!ew_CheckInteger($this->KapasitasUjian->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->KapasitasUjian->FldErrMsg());
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
		$this->RuangID->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->KampusID->AdvancedSearch->Load();
		$this->Lantai->AdvancedSearch->Load();
		$this->ProdiID->AdvancedSearch->Load();
		$this->Kapasitas->AdvancedSearch->Load();
		$this->KapasitasUjian->AdvancedSearch->Load();
		$this->Keterangan->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("ruanglist.php"), "", $this->TableVar, TRUE);
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
if (!isset($ruang_search)) $ruang_search = new cruang_search();

// Page init
$ruang_search->Page_Init();

// Page main
$ruang_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$ruang_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($ruang_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fruangsearch = new ew_Form("fruangsearch", "search");
<?php } else { ?>
var CurrentForm = fruangsearch = new ew_Form("fruangsearch", "search");
<?php } ?>

// Form_CustomValidate event
fruangsearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fruangsearch.ValidateRequired = true;
<?php } else { ?>
fruangsearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fruangsearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fruangsearch.Lists["x_NA"].Options = <?php echo json_encode($ruang->NA->Options()) ?>;

// Form object for search
// Validate function for search

fruangsearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_Lantai");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($ruang->Lantai->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Kapasitas");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($ruang->Kapasitas->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_KapasitasUjian");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($ruang->KapasitasUjian->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$ruang_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $ruang_search->ShowPageHeader(); ?>
<?php
$ruang_search->ShowMessage();
?>
<form name="fruangsearch" id="fruangsearch" class="<?php echo $ruang_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($ruang_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $ruang_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="ruang">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($ruang_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$ruang_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $ruang_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_ruangsearch" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($ruang->RuangID->Visible) { // RuangID ?>
<?php if (ew_IsMobile() || $ruang_search->IsModal) { ?>
	<div id="r_RuangID" class="form-group">
		<label for="x_RuangID" class="<?php echo $ruang_search->SearchLabelClass ?>"><span id="elh_ruang_RuangID"><?php echo $ruang->RuangID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_RuangID" id="z_RuangID" value="LIKE"></p>
		</label>
		<div class="<?php echo $ruang_search->SearchRightColumnClass ?>"><div<?php echo $ruang->RuangID->CellAttributes() ?>>
			<span id="el_ruang_RuangID">
<input type="text" data-table="ruang" data-field="x_RuangID" name="x_RuangID" id="x_RuangID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($ruang->RuangID->getPlaceHolder()) ?>" value="<?php echo $ruang->RuangID->EditValue ?>"<?php echo $ruang->RuangID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_RuangID">
		<td><span id="elh_ruang_RuangID"><?php echo $ruang->RuangID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_RuangID" id="z_RuangID" value="LIKE"></span></td>
		<td<?php echo $ruang->RuangID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_ruang_RuangID">
<input type="text" data-table="ruang" data-field="x_RuangID" name="x_RuangID" id="x_RuangID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($ruang->RuangID->getPlaceHolder()) ?>" value="<?php echo $ruang->RuangID->EditValue ?>"<?php echo $ruang->RuangID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $ruang_search->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label for="x_Nama" class="<?php echo $ruang_search->SearchLabelClass ?>"><span id="elh_ruang_Nama"><?php echo $ruang->Nama->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></p>
		</label>
		<div class="<?php echo $ruang_search->SearchRightColumnClass ?>"><div<?php echo $ruang->Nama->CellAttributes() ?>>
			<span id="el_ruang_Nama">
<input type="text" data-table="ruang" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($ruang->Nama->getPlaceHolder()) ?>" value="<?php echo $ruang->Nama->EditValue ?>"<?php echo $ruang->Nama->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_ruang_Nama"><?php echo $ruang->Nama->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></span></td>
		<td<?php echo $ruang->Nama->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_ruang_Nama">
<input type="text" data-table="ruang" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($ruang->Nama->getPlaceHolder()) ?>" value="<?php echo $ruang->Nama->EditValue ?>"<?php echo $ruang->Nama->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->KampusID->Visible) { // KampusID ?>
<?php if (ew_IsMobile() || $ruang_search->IsModal) { ?>
	<div id="r_KampusID" class="form-group">
		<label for="x_KampusID" class="<?php echo $ruang_search->SearchLabelClass ?>"><span id="elh_ruang_KampusID"><?php echo $ruang->KampusID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KampusID" id="z_KampusID" value="LIKE"></p>
		</label>
		<div class="<?php echo $ruang_search->SearchRightColumnClass ?>"><div<?php echo $ruang->KampusID->CellAttributes() ?>>
			<span id="el_ruang_KampusID">
<input type="text" data-table="ruang" data-field="x_KampusID" name="x_KampusID" id="x_KampusID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($ruang->KampusID->getPlaceHolder()) ?>" value="<?php echo $ruang->KampusID->EditValue ?>"<?php echo $ruang->KampusID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KampusID">
		<td><span id="elh_ruang_KampusID"><?php echo $ruang->KampusID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KampusID" id="z_KampusID" value="LIKE"></span></td>
		<td<?php echo $ruang->KampusID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_ruang_KampusID">
<input type="text" data-table="ruang" data-field="x_KampusID" name="x_KampusID" id="x_KampusID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($ruang->KampusID->getPlaceHolder()) ?>" value="<?php echo $ruang->KampusID->EditValue ?>"<?php echo $ruang->KampusID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->Lantai->Visible) { // Lantai ?>
<?php if (ew_IsMobile() || $ruang_search->IsModal) { ?>
	<div id="r_Lantai" class="form-group">
		<label for="x_Lantai" class="<?php echo $ruang_search->SearchLabelClass ?>"><span id="elh_ruang_Lantai"><?php echo $ruang->Lantai->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Lantai" id="z_Lantai" value="="></p>
		</label>
		<div class="<?php echo $ruang_search->SearchRightColumnClass ?>"><div<?php echo $ruang->Lantai->CellAttributes() ?>>
			<span id="el_ruang_Lantai">
<input type="text" data-table="ruang" data-field="x_Lantai" name="x_Lantai" id="x_Lantai" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->Lantai->getPlaceHolder()) ?>" value="<?php echo $ruang->Lantai->EditValue ?>"<?php echo $ruang->Lantai->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Lantai">
		<td><span id="elh_ruang_Lantai"><?php echo $ruang->Lantai->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Lantai" id="z_Lantai" value="="></span></td>
		<td<?php echo $ruang->Lantai->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_ruang_Lantai">
<input type="text" data-table="ruang" data-field="x_Lantai" name="x_Lantai" id="x_Lantai" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->Lantai->getPlaceHolder()) ?>" value="<?php echo $ruang->Lantai->EditValue ?>"<?php echo $ruang->Lantai->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $ruang_search->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label for="x_ProdiID" class="<?php echo $ruang_search->SearchLabelClass ?>"><span id="elh_ruang_ProdiID"><?php echo $ruang->ProdiID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></p>
		</label>
		<div class="<?php echo $ruang_search->SearchRightColumnClass ?>"><div<?php echo $ruang->ProdiID->CellAttributes() ?>>
			<span id="el_ruang_ProdiID">
<input type="text" data-table="ruang" data-field="x_ProdiID" name="x_ProdiID" id="x_ProdiID" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($ruang->ProdiID->getPlaceHolder()) ?>" value="<?php echo $ruang->ProdiID->EditValue ?>"<?php echo $ruang->ProdiID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_ruang_ProdiID"><?php echo $ruang->ProdiID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></span></td>
		<td<?php echo $ruang->ProdiID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_ruang_ProdiID">
<input type="text" data-table="ruang" data-field="x_ProdiID" name="x_ProdiID" id="x_ProdiID" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($ruang->ProdiID->getPlaceHolder()) ?>" value="<?php echo $ruang->ProdiID->EditValue ?>"<?php echo $ruang->ProdiID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->Kapasitas->Visible) { // Kapasitas ?>
<?php if (ew_IsMobile() || $ruang_search->IsModal) { ?>
	<div id="r_Kapasitas" class="form-group">
		<label for="x_Kapasitas" class="<?php echo $ruang_search->SearchLabelClass ?>"><span id="elh_ruang_Kapasitas"><?php echo $ruang->Kapasitas->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Kapasitas" id="z_Kapasitas" value="="></p>
		</label>
		<div class="<?php echo $ruang_search->SearchRightColumnClass ?>"><div<?php echo $ruang->Kapasitas->CellAttributes() ?>>
			<span id="el_ruang_Kapasitas">
<input type="text" data-table="ruang" data-field="x_Kapasitas" name="x_Kapasitas" id="x_Kapasitas" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->Kapasitas->getPlaceHolder()) ?>" value="<?php echo $ruang->Kapasitas->EditValue ?>"<?php echo $ruang->Kapasitas->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Kapasitas">
		<td><span id="elh_ruang_Kapasitas"><?php echo $ruang->Kapasitas->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Kapasitas" id="z_Kapasitas" value="="></span></td>
		<td<?php echo $ruang->Kapasitas->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_ruang_Kapasitas">
<input type="text" data-table="ruang" data-field="x_Kapasitas" name="x_Kapasitas" id="x_Kapasitas" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->Kapasitas->getPlaceHolder()) ?>" value="<?php echo $ruang->Kapasitas->EditValue ?>"<?php echo $ruang->Kapasitas->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->KapasitasUjian->Visible) { // KapasitasUjian ?>
<?php if (ew_IsMobile() || $ruang_search->IsModal) { ?>
	<div id="r_KapasitasUjian" class="form-group">
		<label for="x_KapasitasUjian" class="<?php echo $ruang_search->SearchLabelClass ?>"><span id="elh_ruang_KapasitasUjian"><?php echo $ruang->KapasitasUjian->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KapasitasUjian" id="z_KapasitasUjian" value="="></p>
		</label>
		<div class="<?php echo $ruang_search->SearchRightColumnClass ?>"><div<?php echo $ruang->KapasitasUjian->CellAttributes() ?>>
			<span id="el_ruang_KapasitasUjian">
<input type="text" data-table="ruang" data-field="x_KapasitasUjian" name="x_KapasitasUjian" id="x_KapasitasUjian" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->KapasitasUjian->getPlaceHolder()) ?>" value="<?php echo $ruang->KapasitasUjian->EditValue ?>"<?php echo $ruang->KapasitasUjian->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KapasitasUjian">
		<td><span id="elh_ruang_KapasitasUjian"><?php echo $ruang->KapasitasUjian->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KapasitasUjian" id="z_KapasitasUjian" value="="></span></td>
		<td<?php echo $ruang->KapasitasUjian->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_ruang_KapasitasUjian">
<input type="text" data-table="ruang" data-field="x_KapasitasUjian" name="x_KapasitasUjian" id="x_KapasitasUjian" size="30" placeholder="<?php echo ew_HtmlEncode($ruang->KapasitasUjian->getPlaceHolder()) ?>" value="<?php echo $ruang->KapasitasUjian->EditValue ?>"<?php echo $ruang->KapasitasUjian->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->Keterangan->Visible) { // Keterangan ?>
<?php if (ew_IsMobile() || $ruang_search->IsModal) { ?>
	<div id="r_Keterangan" class="form-group">
		<label for="x_Keterangan" class="<?php echo $ruang_search->SearchLabelClass ?>"><span id="elh_ruang_Keterangan"><?php echo $ruang->Keterangan->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Keterangan" id="z_Keterangan" value="LIKE"></p>
		</label>
		<div class="<?php echo $ruang_search->SearchRightColumnClass ?>"><div<?php echo $ruang->Keterangan->CellAttributes() ?>>
			<span id="el_ruang_Keterangan">
<input type="text" data-table="ruang" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" size="35" placeholder="<?php echo ew_HtmlEncode($ruang->Keterangan->getPlaceHolder()) ?>" value="<?php echo $ruang->Keterangan->EditValue ?>"<?php echo $ruang->Keterangan->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Keterangan">
		<td><span id="elh_ruang_Keterangan"><?php echo $ruang->Keterangan->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Keterangan" id="z_Keterangan" value="LIKE"></span></td>
		<td<?php echo $ruang->Keterangan->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_ruang_Keterangan">
<input type="text" data-table="ruang" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" size="35" placeholder="<?php echo ew_HtmlEncode($ruang->Keterangan->getPlaceHolder()) ?>" value="<?php echo $ruang->Keterangan->EditValue ?>"<?php echo $ruang->Keterangan->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($ruang->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $ruang_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $ruang_search->SearchLabelClass ?>"><span id="elh_ruang_NA"><?php echo $ruang->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $ruang_search->SearchRightColumnClass ?>"><div<?php echo $ruang->NA->CellAttributes() ?>>
			<span id="el_ruang_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="ruang" data-field="x_NA" data-value-separator="<?php echo $ruang->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $ruang->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $ruang->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_ruang_NA"><?php echo $ruang->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $ruang->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_ruang_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="ruang" data-field="x_NA" data-value-separator="<?php echo $ruang->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $ruang->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $ruang->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $ruang_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$ruang_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$ruang_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fruangsearch.Init();
</script>
<?php
$ruang_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$ruang_search->Page_Terminate();
?>
