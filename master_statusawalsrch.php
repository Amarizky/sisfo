<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_statusawalinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_statusawal_search = NULL; // Initialize page object first

class cmaster_statusawal_search extends cmaster_statusawal {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_statusawal';

	// Page object name
	var $PageObjName = 'master_statusawal_search';

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

		// Table object (master_statusawal)
		if (!isset($GLOBALS["master_statusawal"]) || get_class($GLOBALS["master_statusawal"]) == "cmaster_statusawal") {
			$GLOBALS["master_statusawal"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_statusawal"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_statusawal', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("master_statusawallist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->Urutan->SetVisibility();
		$this->StatusAwalID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->BeliOnline->SetVisibility();
		$this->BeliFormulir->SetVisibility();
		$this->JalurKhusus->SetVisibility();
		$this->TanpaTest->SetVisibility();
		$this->Catatan->SetVisibility();
		$this->NA->SetVisibility();
		$this->PotonganSPI_Prosen->SetVisibility();
		$this->PotonganSPI_Nominal->SetVisibility();
		$this->PotonganSPP_Prosen->SetVisibility();
		$this->PotonganSPP_Nominal->SetVisibility();

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
		global $EW_EXPORT, $master_statusawal;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_statusawal);
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
						$sSrchStr = "master_statusawallist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->Urutan); // Urutan
		$this->BuildSearchUrl($sSrchUrl, $this->StatusAwalID); // StatusAwalID
		$this->BuildSearchUrl($sSrchUrl, $this->Nama); // Nama
		$this->BuildSearchUrl($sSrchUrl, $this->BeliOnline); // BeliOnline
		$this->BuildSearchUrl($sSrchUrl, $this->BeliFormulir); // BeliFormulir
		$this->BuildSearchUrl($sSrchUrl, $this->JalurKhusus); // JalurKhusus
		$this->BuildSearchUrl($sSrchUrl, $this->TanpaTest); // TanpaTest
		$this->BuildSearchUrl($sSrchUrl, $this->Catatan); // Catatan
		$this->BuildSearchUrl($sSrchUrl, $this->NA); // NA
		$this->BuildSearchUrl($sSrchUrl, $this->PotonganSPI_Prosen); // PotonganSPI_Prosen
		$this->BuildSearchUrl($sSrchUrl, $this->PotonganSPI_Nominal); // PotonganSPI_Nominal
		$this->BuildSearchUrl($sSrchUrl, $this->PotonganSPP_Prosen); // PotonganSPP_Prosen
		$this->BuildSearchUrl($sSrchUrl, $this->PotonganSPP_Nominal); // PotonganSPP_Nominal
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
		// Urutan

		$this->Urutan->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Urutan"));
		$this->Urutan->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Urutan");

		// StatusAwalID
		$this->StatusAwalID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StatusAwalID"));
		$this->StatusAwalID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StatusAwalID");

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Nama"));
		$this->Nama->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Nama");

		// BeliOnline
		$this->BeliOnline->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_BeliOnline"));
		$this->BeliOnline->AdvancedSearch->SearchOperator = $objForm->GetValue("z_BeliOnline");

		// BeliFormulir
		$this->BeliFormulir->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_BeliFormulir"));
		$this->BeliFormulir->AdvancedSearch->SearchOperator = $objForm->GetValue("z_BeliFormulir");

		// JalurKhusus
		$this->JalurKhusus->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_JalurKhusus"));
		$this->JalurKhusus->AdvancedSearch->SearchOperator = $objForm->GetValue("z_JalurKhusus");

		// TanpaTest
		$this->TanpaTest->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TanpaTest"));
		$this->TanpaTest->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TanpaTest");

		// Catatan
		$this->Catatan->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Catatan"));
		$this->Catatan->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Catatan");

		// NA
		$this->NA->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NA"));
		$this->NA->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NA");

		// PotonganSPI_Prosen
		$this->PotonganSPI_Prosen->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_PotonganSPI_Prosen"));
		$this->PotonganSPI_Prosen->AdvancedSearch->SearchOperator = $objForm->GetValue("z_PotonganSPI_Prosen");

		// PotonganSPI_Nominal
		$this->PotonganSPI_Nominal->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_PotonganSPI_Nominal"));
		$this->PotonganSPI_Nominal->AdvancedSearch->SearchOperator = $objForm->GetValue("z_PotonganSPI_Nominal");

		// PotonganSPP_Prosen
		$this->PotonganSPP_Prosen->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_PotonganSPP_Prosen"));
		$this->PotonganSPP_Prosen->AdvancedSearch->SearchOperator = $objForm->GetValue("z_PotonganSPP_Prosen");

		// PotonganSPP_Nominal
		$this->PotonganSPP_Nominal->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_PotonganSPP_Nominal"));
		$this->PotonganSPP_Nominal->AdvancedSearch->SearchOperator = $objForm->GetValue("z_PotonganSPP_Nominal");
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// Urutan
		// StatusAwalID
		// Nama
		// BeliOnline
		// BeliFormulir
		// JalurKhusus
		// TanpaTest
		// Catatan
		// NA
		// PotonganSPI_Prosen
		// PotonganSPI_Nominal
		// PotonganSPP_Prosen
		// PotonganSPP_Nominal

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// Urutan
		$this->Urutan->ViewValue = $this->Urutan->CurrentValue;
		$this->Urutan->ViewCustomAttributes = "";

		// StatusAwalID
		$this->StatusAwalID->ViewValue = $this->StatusAwalID->CurrentValue;
		$this->StatusAwalID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// BeliOnline
		if (ew_ConvertToBool($this->BeliOnline->CurrentValue)) {
			$this->BeliOnline->ViewValue = $this->BeliOnline->FldTagCaption(1) <> "" ? $this->BeliOnline->FldTagCaption(1) : "Y";
		} else {
			$this->BeliOnline->ViewValue = $this->BeliOnline->FldTagCaption(2) <> "" ? $this->BeliOnline->FldTagCaption(2) : "N";
		}
		$this->BeliOnline->ViewCustomAttributes = "";

		// BeliFormulir
		if (ew_ConvertToBool($this->BeliFormulir->CurrentValue)) {
			$this->BeliFormulir->ViewValue = $this->BeliFormulir->FldTagCaption(1) <> "" ? $this->BeliFormulir->FldTagCaption(1) : "Y";
		} else {
			$this->BeliFormulir->ViewValue = $this->BeliFormulir->FldTagCaption(2) <> "" ? $this->BeliFormulir->FldTagCaption(2) : "N";
		}
		$this->BeliFormulir->ViewCustomAttributes = "";

		// JalurKhusus
		if (ew_ConvertToBool($this->JalurKhusus->CurrentValue)) {
			$this->JalurKhusus->ViewValue = $this->JalurKhusus->FldTagCaption(1) <> "" ? $this->JalurKhusus->FldTagCaption(1) : "Y";
		} else {
			$this->JalurKhusus->ViewValue = $this->JalurKhusus->FldTagCaption(2) <> "" ? $this->JalurKhusus->FldTagCaption(2) : "N";
		}
		$this->JalurKhusus->ViewCustomAttributes = "";

		// TanpaTest
		if (ew_ConvertToBool($this->TanpaTest->CurrentValue)) {
			$this->TanpaTest->ViewValue = $this->TanpaTest->FldTagCaption(1) <> "" ? $this->TanpaTest->FldTagCaption(1) : "Y";
		} else {
			$this->TanpaTest->ViewValue = $this->TanpaTest->FldTagCaption(2) <> "" ? $this->TanpaTest->FldTagCaption(2) : "N";
		}
		$this->TanpaTest->ViewCustomAttributes = "";

		// Catatan
		$this->Catatan->ViewValue = $this->Catatan->CurrentValue;
		$this->Catatan->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

		// PotonganSPI_Prosen
		$this->PotonganSPI_Prosen->ViewValue = $this->PotonganSPI_Prosen->CurrentValue;
		$this->PotonganSPI_Prosen->ViewCustomAttributes = "";

		// PotonganSPI_Nominal
		$this->PotonganSPI_Nominal->ViewValue = $this->PotonganSPI_Nominal->CurrentValue;
		$this->PotonganSPI_Nominal->ViewCustomAttributes = "";

		// PotonganSPP_Prosen
		$this->PotonganSPP_Prosen->ViewValue = $this->PotonganSPP_Prosen->CurrentValue;
		$this->PotonganSPP_Prosen->ViewCustomAttributes = "";

		// PotonganSPP_Nominal
		$this->PotonganSPP_Nominal->ViewValue = $this->PotonganSPP_Nominal->CurrentValue;
		$this->PotonganSPP_Nominal->ViewCustomAttributes = "";

			// Urutan
			$this->Urutan->LinkCustomAttributes = "";
			$this->Urutan->HrefValue = "";
			$this->Urutan->TooltipValue = "";

			// StatusAwalID
			$this->StatusAwalID->LinkCustomAttributes = "";
			$this->StatusAwalID->HrefValue = "";
			$this->StatusAwalID->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// BeliOnline
			$this->BeliOnline->LinkCustomAttributes = "";
			$this->BeliOnline->HrefValue = "";
			$this->BeliOnline->TooltipValue = "";

			// BeliFormulir
			$this->BeliFormulir->LinkCustomAttributes = "";
			$this->BeliFormulir->HrefValue = "";
			$this->BeliFormulir->TooltipValue = "";

			// JalurKhusus
			$this->JalurKhusus->LinkCustomAttributes = "";
			$this->JalurKhusus->HrefValue = "";
			$this->JalurKhusus->TooltipValue = "";

			// TanpaTest
			$this->TanpaTest->LinkCustomAttributes = "";
			$this->TanpaTest->HrefValue = "";
			$this->TanpaTest->TooltipValue = "";

			// Catatan
			$this->Catatan->LinkCustomAttributes = "";
			$this->Catatan->HrefValue = "";
			$this->Catatan->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";

			// PotonganSPI_Prosen
			$this->PotonganSPI_Prosen->LinkCustomAttributes = "";
			$this->PotonganSPI_Prosen->HrefValue = "";
			$this->PotonganSPI_Prosen->TooltipValue = "";

			// PotonganSPI_Nominal
			$this->PotonganSPI_Nominal->LinkCustomAttributes = "";
			$this->PotonganSPI_Nominal->HrefValue = "";
			$this->PotonganSPI_Nominal->TooltipValue = "";

			// PotonganSPP_Prosen
			$this->PotonganSPP_Prosen->LinkCustomAttributes = "";
			$this->PotonganSPP_Prosen->HrefValue = "";
			$this->PotonganSPP_Prosen->TooltipValue = "";

			// PotonganSPP_Nominal
			$this->PotonganSPP_Nominal->LinkCustomAttributes = "";
			$this->PotonganSPP_Nominal->HrefValue = "";
			$this->PotonganSPP_Nominal->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// Urutan
			$this->Urutan->EditAttrs["class"] = "form-control";
			$this->Urutan->EditCustomAttributes = "";
			$this->Urutan->EditValue = ew_HtmlEncode($this->Urutan->AdvancedSearch->SearchValue);
			$this->Urutan->PlaceHolder = ew_RemoveHtml($this->Urutan->FldCaption());

			// StatusAwalID
			$this->StatusAwalID->EditAttrs["class"] = "form-control";
			$this->StatusAwalID->EditCustomAttributes = "";
			$this->StatusAwalID->EditValue = ew_HtmlEncode($this->StatusAwalID->AdvancedSearch->SearchValue);
			$this->StatusAwalID->PlaceHolder = ew_RemoveHtml($this->StatusAwalID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->AdvancedSearch->SearchValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// BeliOnline
			$this->BeliOnline->EditCustomAttributes = "";
			$this->BeliOnline->EditValue = $this->BeliOnline->Options(FALSE);

			// BeliFormulir
			$this->BeliFormulir->EditCustomAttributes = "";
			$this->BeliFormulir->EditValue = $this->BeliFormulir->Options(FALSE);

			// JalurKhusus
			$this->JalurKhusus->EditCustomAttributes = "";
			$this->JalurKhusus->EditValue = $this->JalurKhusus->Options(FALSE);

			// TanpaTest
			$this->TanpaTest->EditCustomAttributes = "";
			$this->TanpaTest->EditValue = $this->TanpaTest->Options(FALSE);

			// Catatan
			$this->Catatan->EditAttrs["class"] = "form-control";
			$this->Catatan->EditCustomAttributes = "";
			$this->Catatan->EditValue = ew_HtmlEncode($this->Catatan->AdvancedSearch->SearchValue);
			$this->Catatan->PlaceHolder = ew_RemoveHtml($this->Catatan->FldCaption());

			// NA
			$this->NA->EditCustomAttributes = "";
			$this->NA->EditValue = $this->NA->Options(FALSE);

			// PotonganSPI_Prosen
			$this->PotonganSPI_Prosen->EditAttrs["class"] = "form-control";
			$this->PotonganSPI_Prosen->EditCustomAttributes = "";
			$this->PotonganSPI_Prosen->EditValue = ew_HtmlEncode($this->PotonganSPI_Prosen->AdvancedSearch->SearchValue);
			$this->PotonganSPI_Prosen->PlaceHolder = ew_RemoveHtml($this->PotonganSPI_Prosen->FldCaption());

			// PotonganSPI_Nominal
			$this->PotonganSPI_Nominal->EditAttrs["class"] = "form-control";
			$this->PotonganSPI_Nominal->EditCustomAttributes = "";
			$this->PotonganSPI_Nominal->EditValue = ew_HtmlEncode($this->PotonganSPI_Nominal->AdvancedSearch->SearchValue);
			$this->PotonganSPI_Nominal->PlaceHolder = ew_RemoveHtml($this->PotonganSPI_Nominal->FldCaption());

			// PotonganSPP_Prosen
			$this->PotonganSPP_Prosen->EditAttrs["class"] = "form-control";
			$this->PotonganSPP_Prosen->EditCustomAttributes = "";
			$this->PotonganSPP_Prosen->EditValue = ew_HtmlEncode($this->PotonganSPP_Prosen->AdvancedSearch->SearchValue);
			$this->PotonganSPP_Prosen->PlaceHolder = ew_RemoveHtml($this->PotonganSPP_Prosen->FldCaption());

			// PotonganSPP_Nominal
			$this->PotonganSPP_Nominal->EditAttrs["class"] = "form-control";
			$this->PotonganSPP_Nominal->EditCustomAttributes = "";
			$this->PotonganSPP_Nominal->EditValue = ew_HtmlEncode($this->PotonganSPP_Nominal->AdvancedSearch->SearchValue);
			$this->PotonganSPP_Nominal->PlaceHolder = ew_RemoveHtml($this->PotonganSPP_Nominal->FldCaption());
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
		if (!ew_CheckInteger($this->PotonganSPI_Prosen->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->PotonganSPI_Prosen->FldErrMsg());
		}
		if (!ew_CheckInteger($this->PotonganSPI_Nominal->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->PotonganSPI_Nominal->FldErrMsg());
		}
		if (!ew_CheckInteger($this->PotonganSPP_Prosen->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->PotonganSPP_Prosen->FldErrMsg());
		}
		if (!ew_CheckInteger($this->PotonganSPP_Nominal->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->PotonganSPP_Nominal->FldErrMsg());
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
		$this->Urutan->AdvancedSearch->Load();
		$this->StatusAwalID->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->BeliOnline->AdvancedSearch->Load();
		$this->BeliFormulir->AdvancedSearch->Load();
		$this->JalurKhusus->AdvancedSearch->Load();
		$this->TanpaTest->AdvancedSearch->Load();
		$this->Catatan->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
		$this->PotonganSPI_Prosen->AdvancedSearch->Load();
		$this->PotonganSPI_Nominal->AdvancedSearch->Load();
		$this->PotonganSPP_Prosen->AdvancedSearch->Load();
		$this->PotonganSPP_Nominal->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_statusawallist.php"), "", $this->TableVar, TRUE);
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
if (!isset($master_statusawal_search)) $master_statusawal_search = new cmaster_statusawal_search();

// Page init
$master_statusawal_search->Page_Init();

// Page main
$master_statusawal_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_statusawal_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($master_statusawal_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fmaster_statusawalsearch = new ew_Form("fmaster_statusawalsearch", "search");
<?php } else { ?>
var CurrentForm = fmaster_statusawalsearch = new ew_Form("fmaster_statusawalsearch", "search");
<?php } ?>

// Form_CustomValidate event
fmaster_statusawalsearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_statusawalsearch.ValidateRequired = true;
<?php } else { ?>
fmaster_statusawalsearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_statusawalsearch.Lists["x_BeliOnline"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawalsearch.Lists["x_BeliOnline"].Options = <?php echo json_encode($master_statusawal->BeliOnline->Options()) ?>;
fmaster_statusawalsearch.Lists["x_BeliFormulir"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawalsearch.Lists["x_BeliFormulir"].Options = <?php echo json_encode($master_statusawal->BeliFormulir->Options()) ?>;
fmaster_statusawalsearch.Lists["x_JalurKhusus"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawalsearch.Lists["x_JalurKhusus"].Options = <?php echo json_encode($master_statusawal->JalurKhusus->Options()) ?>;
fmaster_statusawalsearch.Lists["x_TanpaTest"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawalsearch.Lists["x_TanpaTest"].Options = <?php echo json_encode($master_statusawal->TanpaTest->Options()) ?>;
fmaster_statusawalsearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_statusawalsearch.Lists["x_NA"].Options = <?php echo json_encode($master_statusawal->NA->Options()) ?>;

// Form object for search
// Validate function for search

fmaster_statusawalsearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_PotonganSPI_Prosen");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($master_statusawal->PotonganSPI_Prosen->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_PotonganSPI_Nominal");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($master_statusawal->PotonganSPI_Nominal->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_PotonganSPP_Prosen");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($master_statusawal->PotonganSPP_Prosen->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_PotonganSPP_Nominal");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($master_statusawal->PotonganSPP_Nominal->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$master_statusawal_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_statusawal_search->ShowPageHeader(); ?>
<?php
$master_statusawal_search->ShowMessage();
?>
<form name="fmaster_statusawalsearch" id="fmaster_statusawalsearch" class="<?php echo $master_statusawal_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_statusawal_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_statusawal_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_statusawal">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($master_statusawal_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_statusawal_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_statusawalsearch" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_statusawal->Urutan->Visible) { // Urutan ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_Urutan" class="form-group">
		<label for="x_Urutan" class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_Urutan"><?php echo $master_statusawal->Urutan->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Urutan" id="z_Urutan" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->Urutan->CellAttributes() ?>>
			<span id="el_master_statusawal_Urutan">
<input type="text" data-table="master_statusawal" data-field="x_Urutan" name="x_Urutan" id="x_Urutan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Urutan->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Urutan->EditValue ?>"<?php echo $master_statusawal->Urutan->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Urutan">
		<td><span id="elh_master_statusawal_Urutan"><?php echo $master_statusawal->Urutan->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Urutan" id="z_Urutan" value="LIKE"></span></td>
		<td<?php echo $master_statusawal->Urutan->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_Urutan">
<input type="text" data-table="master_statusawal" data-field="x_Urutan" name="x_Urutan" id="x_Urutan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Urutan->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Urutan->EditValue ?>"<?php echo $master_statusawal->Urutan->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->StatusAwalID->Visible) { // StatusAwalID ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_StatusAwalID" class="form-group">
		<label for="x_StatusAwalID" class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_StatusAwalID"><?php echo $master_statusawal->StatusAwalID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusAwalID" id="z_StatusAwalID" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->StatusAwalID->CellAttributes() ?>>
			<span id="el_master_statusawal_StatusAwalID">
<input type="text" data-table="master_statusawal" data-field="x_StatusAwalID" name="x_StatusAwalID" id="x_StatusAwalID" size="30" maxlength="5" placeholder="<?php echo ew_HtmlEncode($master_statusawal->StatusAwalID->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->StatusAwalID->EditValue ?>"<?php echo $master_statusawal->StatusAwalID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusAwalID">
		<td><span id="elh_master_statusawal_StatusAwalID"><?php echo $master_statusawal->StatusAwalID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusAwalID" id="z_StatusAwalID" value="LIKE"></span></td>
		<td<?php echo $master_statusawal->StatusAwalID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_StatusAwalID">
<input type="text" data-table="master_statusawal" data-field="x_StatusAwalID" name="x_StatusAwalID" id="x_StatusAwalID" size="30" maxlength="5" placeholder="<?php echo ew_HtmlEncode($master_statusawal->StatusAwalID->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->StatusAwalID->EditValue ?>"<?php echo $master_statusawal->StatusAwalID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label for="x_Nama" class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_Nama"><?php echo $master_statusawal->Nama->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->Nama->CellAttributes() ?>>
			<span id="el_master_statusawal_Nama">
<input type="text" data-table="master_statusawal" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Nama->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Nama->EditValue ?>"<?php echo $master_statusawal->Nama->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_master_statusawal_Nama"><?php echo $master_statusawal->Nama->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></span></td>
		<td<?php echo $master_statusawal->Nama->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_Nama">
<input type="text" data-table="master_statusawal" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Nama->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Nama->EditValue ?>"<?php echo $master_statusawal->Nama->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->BeliOnline->Visible) { // BeliOnline ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_BeliOnline" class="form-group">
		<label class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_BeliOnline"><?php echo $master_statusawal->BeliOnline->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_BeliOnline" id="z_BeliOnline" value="="></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->BeliOnline->CellAttributes() ?>>
			<span id="el_master_statusawal_BeliOnline">
<div id="tp_x_BeliOnline" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_BeliOnline" data-value-separator="<?php echo $master_statusawal->BeliOnline->DisplayValueSeparatorAttribute() ?>" name="x_BeliOnline" id="x_BeliOnline" value="{value}"<?php echo $master_statusawal->BeliOnline->EditAttributes() ?>></div>
<div id="dsl_x_BeliOnline" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->BeliOnline->RadioButtonListHtml(FALSE, "x_BeliOnline") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_BeliOnline">
		<td><span id="elh_master_statusawal_BeliOnline"><?php echo $master_statusawal->BeliOnline->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_BeliOnline" id="z_BeliOnline" value="="></span></td>
		<td<?php echo $master_statusawal->BeliOnline->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_BeliOnline">
<div id="tp_x_BeliOnline" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_BeliOnline" data-value-separator="<?php echo $master_statusawal->BeliOnline->DisplayValueSeparatorAttribute() ?>" name="x_BeliOnline" id="x_BeliOnline" value="{value}"<?php echo $master_statusawal->BeliOnline->EditAttributes() ?>></div>
<div id="dsl_x_BeliOnline" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->BeliOnline->RadioButtonListHtml(FALSE, "x_BeliOnline") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->BeliFormulir->Visible) { // BeliFormulir ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_BeliFormulir" class="form-group">
		<label class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_BeliFormulir"><?php echo $master_statusawal->BeliFormulir->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_BeliFormulir" id="z_BeliFormulir" value="="></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->BeliFormulir->CellAttributes() ?>>
			<span id="el_master_statusawal_BeliFormulir">
<div id="tp_x_BeliFormulir" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_BeliFormulir" data-value-separator="<?php echo $master_statusawal->BeliFormulir->DisplayValueSeparatorAttribute() ?>" name="x_BeliFormulir" id="x_BeliFormulir" value="{value}"<?php echo $master_statusawal->BeliFormulir->EditAttributes() ?>></div>
<div id="dsl_x_BeliFormulir" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->BeliFormulir->RadioButtonListHtml(FALSE, "x_BeliFormulir") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_BeliFormulir">
		<td><span id="elh_master_statusawal_BeliFormulir"><?php echo $master_statusawal->BeliFormulir->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_BeliFormulir" id="z_BeliFormulir" value="="></span></td>
		<td<?php echo $master_statusawal->BeliFormulir->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_BeliFormulir">
<div id="tp_x_BeliFormulir" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_BeliFormulir" data-value-separator="<?php echo $master_statusawal->BeliFormulir->DisplayValueSeparatorAttribute() ?>" name="x_BeliFormulir" id="x_BeliFormulir" value="{value}"<?php echo $master_statusawal->BeliFormulir->EditAttributes() ?>></div>
<div id="dsl_x_BeliFormulir" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->BeliFormulir->RadioButtonListHtml(FALSE, "x_BeliFormulir") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->JalurKhusus->Visible) { // JalurKhusus ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_JalurKhusus" class="form-group">
		<label class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_JalurKhusus"><?php echo $master_statusawal->JalurKhusus->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_JalurKhusus" id="z_JalurKhusus" value="="></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->JalurKhusus->CellAttributes() ?>>
			<span id="el_master_statusawal_JalurKhusus">
<div id="tp_x_JalurKhusus" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_JalurKhusus" data-value-separator="<?php echo $master_statusawal->JalurKhusus->DisplayValueSeparatorAttribute() ?>" name="x_JalurKhusus" id="x_JalurKhusus" value="{value}"<?php echo $master_statusawal->JalurKhusus->EditAttributes() ?>></div>
<div id="dsl_x_JalurKhusus" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->JalurKhusus->RadioButtonListHtml(FALSE, "x_JalurKhusus") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_JalurKhusus">
		<td><span id="elh_master_statusawal_JalurKhusus"><?php echo $master_statusawal->JalurKhusus->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_JalurKhusus" id="z_JalurKhusus" value="="></span></td>
		<td<?php echo $master_statusawal->JalurKhusus->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_JalurKhusus">
<div id="tp_x_JalurKhusus" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_JalurKhusus" data-value-separator="<?php echo $master_statusawal->JalurKhusus->DisplayValueSeparatorAttribute() ?>" name="x_JalurKhusus" id="x_JalurKhusus" value="{value}"<?php echo $master_statusawal->JalurKhusus->EditAttributes() ?>></div>
<div id="dsl_x_JalurKhusus" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->JalurKhusus->RadioButtonListHtml(FALSE, "x_JalurKhusus") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->TanpaTest->Visible) { // TanpaTest ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_TanpaTest" class="form-group">
		<label class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_TanpaTest"><?php echo $master_statusawal->TanpaTest->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanpaTest" id="z_TanpaTest" value="="></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->TanpaTest->CellAttributes() ?>>
			<span id="el_master_statusawal_TanpaTest">
<div id="tp_x_TanpaTest" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_TanpaTest" data-value-separator="<?php echo $master_statusawal->TanpaTest->DisplayValueSeparatorAttribute() ?>" name="x_TanpaTest" id="x_TanpaTest" value="{value}"<?php echo $master_statusawal->TanpaTest->EditAttributes() ?>></div>
<div id="dsl_x_TanpaTest" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->TanpaTest->RadioButtonListHtml(FALSE, "x_TanpaTest") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanpaTest">
		<td><span id="elh_master_statusawal_TanpaTest"><?php echo $master_statusawal->TanpaTest->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanpaTest" id="z_TanpaTest" value="="></span></td>
		<td<?php echo $master_statusawal->TanpaTest->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_TanpaTest">
<div id="tp_x_TanpaTest" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_TanpaTest" data-value-separator="<?php echo $master_statusawal->TanpaTest->DisplayValueSeparatorAttribute() ?>" name="x_TanpaTest" id="x_TanpaTest" value="{value}"<?php echo $master_statusawal->TanpaTest->EditAttributes() ?>></div>
<div id="dsl_x_TanpaTest" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->TanpaTest->RadioButtonListHtml(FALSE, "x_TanpaTest") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->Catatan->Visible) { // Catatan ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_Catatan" class="form-group">
		<label for="x_Catatan" class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_Catatan"><?php echo $master_statusawal->Catatan->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Catatan" id="z_Catatan" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->Catatan->CellAttributes() ?>>
			<span id="el_master_statusawal_Catatan">
<input type="text" data-table="master_statusawal" data-field="x_Catatan" name="x_Catatan" id="x_Catatan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Catatan->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Catatan->EditValue ?>"<?php echo $master_statusawal->Catatan->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Catatan">
		<td><span id="elh_master_statusawal_Catatan"><?php echo $master_statusawal->Catatan->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Catatan" id="z_Catatan" value="LIKE"></span></td>
		<td<?php echo $master_statusawal->Catatan->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_Catatan">
<input type="text" data-table="master_statusawal" data-field="x_Catatan" name="x_Catatan" id="x_Catatan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($master_statusawal->Catatan->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->Catatan->EditValue ?>"<?php echo $master_statusawal->Catatan->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_NA"><?php echo $master_statusawal->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->NA->CellAttributes() ?>>
			<span id="el_master_statusawal_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_NA" data-value-separator="<?php echo $master_statusawal->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_statusawal->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_master_statusawal_NA"><?php echo $master_statusawal->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $master_statusawal->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_statusawal" data-field="x_NA" data-value-separator="<?php echo $master_statusawal->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_statusawal->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_statusawal->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->PotonganSPI_Prosen->Visible) { // PotonganSPI_Prosen ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_PotonganSPI_Prosen" class="form-group">
		<label for="x_PotonganSPI_Prosen" class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_PotonganSPI_Prosen"><?php echo $master_statusawal->PotonganSPI_Prosen->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PotonganSPI_Prosen" id="z_PotonganSPI_Prosen" value="="></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->PotonganSPI_Prosen->CellAttributes() ?>>
			<span id="el_master_statusawal_PotonganSPI_Prosen">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPI_Prosen" name="x_PotonganSPI_Prosen" id="x_PotonganSPI_Prosen" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPI_Prosen->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPI_Prosen->EditValue ?>"<?php echo $master_statusawal->PotonganSPI_Prosen->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_PotonganSPI_Prosen">
		<td><span id="elh_master_statusawal_PotonganSPI_Prosen"><?php echo $master_statusawal->PotonganSPI_Prosen->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PotonganSPI_Prosen" id="z_PotonganSPI_Prosen" value="="></span></td>
		<td<?php echo $master_statusawal->PotonganSPI_Prosen->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_PotonganSPI_Prosen">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPI_Prosen" name="x_PotonganSPI_Prosen" id="x_PotonganSPI_Prosen" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPI_Prosen->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPI_Prosen->EditValue ?>"<?php echo $master_statusawal->PotonganSPI_Prosen->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->PotonganSPI_Nominal->Visible) { // PotonganSPI_Nominal ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_PotonganSPI_Nominal" class="form-group">
		<label for="x_PotonganSPI_Nominal" class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_PotonganSPI_Nominal"><?php echo $master_statusawal->PotonganSPI_Nominal->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PotonganSPI_Nominal" id="z_PotonganSPI_Nominal" value="="></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->PotonganSPI_Nominal->CellAttributes() ?>>
			<span id="el_master_statusawal_PotonganSPI_Nominal">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPI_Nominal" name="x_PotonganSPI_Nominal" id="x_PotonganSPI_Nominal" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPI_Nominal->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPI_Nominal->EditValue ?>"<?php echo $master_statusawal->PotonganSPI_Nominal->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_PotonganSPI_Nominal">
		<td><span id="elh_master_statusawal_PotonganSPI_Nominal"><?php echo $master_statusawal->PotonganSPI_Nominal->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PotonganSPI_Nominal" id="z_PotonganSPI_Nominal" value="="></span></td>
		<td<?php echo $master_statusawal->PotonganSPI_Nominal->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_PotonganSPI_Nominal">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPI_Nominal" name="x_PotonganSPI_Nominal" id="x_PotonganSPI_Nominal" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPI_Nominal->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPI_Nominal->EditValue ?>"<?php echo $master_statusawal->PotonganSPI_Nominal->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->PotonganSPP_Prosen->Visible) { // PotonganSPP_Prosen ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_PotonganSPP_Prosen" class="form-group">
		<label for="x_PotonganSPP_Prosen" class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_PotonganSPP_Prosen"><?php echo $master_statusawal->PotonganSPP_Prosen->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PotonganSPP_Prosen" id="z_PotonganSPP_Prosen" value="="></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->PotonganSPP_Prosen->CellAttributes() ?>>
			<span id="el_master_statusawal_PotonganSPP_Prosen">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPP_Prosen" name="x_PotonganSPP_Prosen" id="x_PotonganSPP_Prosen" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPP_Prosen->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPP_Prosen->EditValue ?>"<?php echo $master_statusawal->PotonganSPP_Prosen->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_PotonganSPP_Prosen">
		<td><span id="elh_master_statusawal_PotonganSPP_Prosen"><?php echo $master_statusawal->PotonganSPP_Prosen->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PotonganSPP_Prosen" id="z_PotonganSPP_Prosen" value="="></span></td>
		<td<?php echo $master_statusawal->PotonganSPP_Prosen->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_PotonganSPP_Prosen">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPP_Prosen" name="x_PotonganSPP_Prosen" id="x_PotonganSPP_Prosen" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPP_Prosen->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPP_Prosen->EditValue ?>"<?php echo $master_statusawal->PotonganSPP_Prosen->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_statusawal->PotonganSPP_Nominal->Visible) { // PotonganSPP_Nominal ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
	<div id="r_PotonganSPP_Nominal" class="form-group">
		<label for="x_PotonganSPP_Nominal" class="<?php echo $master_statusawal_search->SearchLabelClass ?>"><span id="elh_master_statusawal_PotonganSPP_Nominal"><?php echo $master_statusawal->PotonganSPP_Nominal->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PotonganSPP_Nominal" id="z_PotonganSPP_Nominal" value="="></p>
		</label>
		<div class="<?php echo $master_statusawal_search->SearchRightColumnClass ?>"><div<?php echo $master_statusawal->PotonganSPP_Nominal->CellAttributes() ?>>
			<span id="el_master_statusawal_PotonganSPP_Nominal">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPP_Nominal" name="x_PotonganSPP_Nominal" id="x_PotonganSPP_Nominal" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPP_Nominal->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPP_Nominal->EditValue ?>"<?php echo $master_statusawal->PotonganSPP_Nominal->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_PotonganSPP_Nominal">
		<td><span id="elh_master_statusawal_PotonganSPP_Nominal"><?php echo $master_statusawal->PotonganSPP_Nominal->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PotonganSPP_Nominal" id="z_PotonganSPP_Nominal" value="="></span></td>
		<td<?php echo $master_statusawal->PotonganSPP_Nominal->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_statusawal_PotonganSPP_Nominal">
<input type="text" data-table="master_statusawal" data-field="x_PotonganSPP_Nominal" name="x_PotonganSPP_Nominal" id="x_PotonganSPP_Nominal" size="30" placeholder="<?php echo ew_HtmlEncode($master_statusawal->PotonganSPP_Nominal->getPlaceHolder()) ?>" value="<?php echo $master_statusawal->PotonganSPP_Nominal->EditValue ?>"<?php echo $master_statusawal->PotonganSPP_Nominal->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_statusawal_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$master_statusawal_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$master_statusawal_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_statusawalsearch.Init();
</script>
<?php
$master_statusawal_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_statusawal_search->Page_Terminate();
?>
