<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "identitasinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$identitas_search = NULL; // Initialize page object first

class cidentitas_search extends cidentitas {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'identitas';

	// Page object name
	var $PageObjName = 'identitas_search';

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

		// Table object (identitas)
		if (!isset($GLOBALS["identitas"]) || get_class($GLOBALS["identitas"]) == "cidentitas") {
			$GLOBALS["identitas"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["identitas"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'identitas', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("identitaslist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->Kode->SetVisibility();
		$this->KodeHukum->SetVisibility();
		$this->Nama->SetVisibility();
		$this->TglMulai->SetVisibility();
		$this->Alamat1->SetVisibility();
		$this->Alamat2->SetVisibility();
		$this->Kota->SetVisibility();
		$this->KodePos->SetVisibility();
		$this->Telepon->SetVisibility();
		$this->Fax->SetVisibility();
		$this->_Email->SetVisibility();
		$this->Website->SetVisibility();
		$this->NoAkta->SetVisibility();
		$this->TglAkta->SetVisibility();
		$this->NoSah->SetVisibility();
		$this->TglSah->SetVisibility();
		$this->Logo->SetVisibility();
		$this->StartNoIdentitas->SetVisibility();
		$this->NoIdentitas->SetVisibility();
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
		global $EW_EXPORT, $identitas;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($identitas);
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
						$sSrchStr = "identitaslist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->Kode); // Kode
		$this->BuildSearchUrl($sSrchUrl, $this->KodeHukum); // KodeHukum
		$this->BuildSearchUrl($sSrchUrl, $this->Nama); // Nama
		$this->BuildSearchUrl($sSrchUrl, $this->TglMulai); // TglMulai
		$this->BuildSearchUrl($sSrchUrl, $this->Alamat1); // Alamat1
		$this->BuildSearchUrl($sSrchUrl, $this->Alamat2); // Alamat2
		$this->BuildSearchUrl($sSrchUrl, $this->Kota); // Kota
		$this->BuildSearchUrl($sSrchUrl, $this->KodePos); // KodePos
		$this->BuildSearchUrl($sSrchUrl, $this->Telepon); // Telepon
		$this->BuildSearchUrl($sSrchUrl, $this->Fax); // Fax
		$this->BuildSearchUrl($sSrchUrl, $this->_Email); // Email
		$this->BuildSearchUrl($sSrchUrl, $this->Website); // Website
		$this->BuildSearchUrl($sSrchUrl, $this->NoAkta); // NoAkta
		$this->BuildSearchUrl($sSrchUrl, $this->TglAkta); // TglAkta
		$this->BuildSearchUrl($sSrchUrl, $this->NoSah); // NoSah
		$this->BuildSearchUrl($sSrchUrl, $this->TglSah); // TglSah
		$this->BuildSearchUrl($sSrchUrl, $this->Logo); // Logo
		$this->BuildSearchUrl($sSrchUrl, $this->StartNoIdentitas); // StartNoIdentitas
		$this->BuildSearchUrl($sSrchUrl, $this->NoIdentitas); // NoIdentitas
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
		// Kode

		$this->Kode->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Kode"));
		$this->Kode->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Kode");

		// KodeHukum
		$this->KodeHukum->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KodeHukum"));
		$this->KodeHukum->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KodeHukum");

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Nama"));
		$this->Nama->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Nama");

		// TglMulai
		$this->TglMulai->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TglMulai"));
		$this->TglMulai->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TglMulai");

		// Alamat1
		$this->Alamat1->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Alamat1"));
		$this->Alamat1->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Alamat1");

		// Alamat2
		$this->Alamat2->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Alamat2"));
		$this->Alamat2->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Alamat2");

		// Kota
		$this->Kota->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Kota"));
		$this->Kota->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Kota");

		// KodePos
		$this->KodePos->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KodePos"));
		$this->KodePos->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KodePos");

		// Telepon
		$this->Telepon->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Telepon"));
		$this->Telepon->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Telepon");

		// Fax
		$this->Fax->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Fax"));
		$this->Fax->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Fax");

		// Email
		$this->_Email->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x__Email"));
		$this->_Email->AdvancedSearch->SearchOperator = $objForm->GetValue("z__Email");

		// Website
		$this->Website->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Website"));
		$this->Website->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Website");

		// NoAkta
		$this->NoAkta->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NoAkta"));
		$this->NoAkta->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NoAkta");

		// TglAkta
		$this->TglAkta->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TglAkta"));
		$this->TglAkta->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TglAkta");

		// NoSah
		$this->NoSah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NoSah"));
		$this->NoSah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NoSah");

		// TglSah
		$this->TglSah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TglSah"));
		$this->TglSah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TglSah");

		// Logo
		$this->Logo->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Logo"));
		$this->Logo->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Logo");

		// StartNoIdentitas
		$this->StartNoIdentitas->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StartNoIdentitas"));
		$this->StartNoIdentitas->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StartNoIdentitas");

		// NoIdentitas
		$this->NoIdentitas->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NoIdentitas"));
		$this->NoIdentitas->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NoIdentitas");

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
		// Kode
		// KodeHukum
		// Nama
		// TglMulai
		// Alamat1
		// Alamat2
		// Kota
		// KodePos
		// Telepon
		// Fax
		// Email
		// Website
		// NoAkta
		// TglAkta
		// NoSah
		// TglSah
		// Logo
		// StartNoIdentitas
		// NoIdentitas
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// Kode
		$this->Kode->ViewValue = $this->Kode->CurrentValue;
		$this->Kode->ViewCustomAttributes = "";

		// KodeHukum
		$this->KodeHukum->ViewValue = $this->KodeHukum->CurrentValue;
		$this->KodeHukum->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// TglMulai
		$this->TglMulai->ViewValue = $this->TglMulai->CurrentValue;
		$this->TglMulai->ViewValue = ew_FormatDateTime($this->TglMulai->ViewValue, 0);
		$this->TglMulai->ViewCustomAttributes = "";

		// Alamat1
		$this->Alamat1->ViewValue = $this->Alamat1->CurrentValue;
		$this->Alamat1->ViewCustomAttributes = "";

		// Alamat2
		$this->Alamat2->ViewValue = $this->Alamat2->CurrentValue;
		$this->Alamat2->ViewCustomAttributes = "";

		// Kota
		$this->Kota->ViewValue = $this->Kota->CurrentValue;
		$this->Kota->ViewCustomAttributes = "";

		// KodePos
		$this->KodePos->ViewValue = $this->KodePos->CurrentValue;
		$this->KodePos->ViewCustomAttributes = "";

		// Telepon
		$this->Telepon->ViewValue = $this->Telepon->CurrentValue;
		$this->Telepon->ViewCustomAttributes = "";

		// Fax
		$this->Fax->ViewValue = $this->Fax->CurrentValue;
		$this->Fax->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// Website
		$this->Website->ViewValue = $this->Website->CurrentValue;
		$this->Website->ViewCustomAttributes = "";

		// NoAkta
		$this->NoAkta->ViewValue = $this->NoAkta->CurrentValue;
		$this->NoAkta->ViewCustomAttributes = "";

		// TglAkta
		$this->TglAkta->ViewValue = $this->TglAkta->CurrentValue;
		$this->TglAkta->ViewValue = ew_FormatDateTime($this->TglAkta->ViewValue, 0);
		$this->TglAkta->ViewCustomAttributes = "";

		// NoSah
		$this->NoSah->ViewValue = $this->NoSah->CurrentValue;
		$this->NoSah->ViewCustomAttributes = "";

		// TglSah
		$this->TglSah->ViewValue = $this->TglSah->CurrentValue;
		$this->TglSah->ViewValue = ew_FormatDateTime($this->TglSah->ViewValue, 0);
		$this->TglSah->ViewCustomAttributes = "";

		// Logo
		$this->Logo->ViewValue = $this->Logo->CurrentValue;
		$this->Logo->ViewCustomAttributes = "";

		// StartNoIdentitas
		$this->StartNoIdentitas->ViewValue = $this->StartNoIdentitas->CurrentValue;
		$this->StartNoIdentitas->ViewCustomAttributes = "";

		// NoIdentitas
		$this->NoIdentitas->ViewValue = $this->NoIdentitas->CurrentValue;
		$this->NoIdentitas->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

			// Kode
			$this->Kode->LinkCustomAttributes = "";
			$this->Kode->HrefValue = "";
			$this->Kode->TooltipValue = "";

			// KodeHukum
			$this->KodeHukum->LinkCustomAttributes = "";
			$this->KodeHukum->HrefValue = "";
			$this->KodeHukum->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// TglMulai
			$this->TglMulai->LinkCustomAttributes = "";
			$this->TglMulai->HrefValue = "";
			$this->TglMulai->TooltipValue = "";

			// Alamat1
			$this->Alamat1->LinkCustomAttributes = "";
			$this->Alamat1->HrefValue = "";
			$this->Alamat1->TooltipValue = "";

			// Alamat2
			$this->Alamat2->LinkCustomAttributes = "";
			$this->Alamat2->HrefValue = "";
			$this->Alamat2->TooltipValue = "";

			// Kota
			$this->Kota->LinkCustomAttributes = "";
			$this->Kota->HrefValue = "";
			$this->Kota->TooltipValue = "";

			// KodePos
			$this->KodePos->LinkCustomAttributes = "";
			$this->KodePos->HrefValue = "";
			$this->KodePos->TooltipValue = "";

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";
			$this->Telepon->TooltipValue = "";

			// Fax
			$this->Fax->LinkCustomAttributes = "";
			$this->Fax->HrefValue = "";
			$this->Fax->TooltipValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";

			// Website
			$this->Website->LinkCustomAttributes = "";
			$this->Website->HrefValue = "";
			$this->Website->TooltipValue = "";

			// NoAkta
			$this->NoAkta->LinkCustomAttributes = "";
			$this->NoAkta->HrefValue = "";
			$this->NoAkta->TooltipValue = "";

			// TglAkta
			$this->TglAkta->LinkCustomAttributes = "";
			$this->TglAkta->HrefValue = "";
			$this->TglAkta->TooltipValue = "";

			// NoSah
			$this->NoSah->LinkCustomAttributes = "";
			$this->NoSah->HrefValue = "";
			$this->NoSah->TooltipValue = "";

			// TglSah
			$this->TglSah->LinkCustomAttributes = "";
			$this->TglSah->HrefValue = "";
			$this->TglSah->TooltipValue = "";

			// Logo
			$this->Logo->LinkCustomAttributes = "";
			$this->Logo->HrefValue = "";
			$this->Logo->TooltipValue = "";

			// StartNoIdentitas
			$this->StartNoIdentitas->LinkCustomAttributes = "";
			$this->StartNoIdentitas->HrefValue = "";
			$this->StartNoIdentitas->TooltipValue = "";

			// NoIdentitas
			$this->NoIdentitas->LinkCustomAttributes = "";
			$this->NoIdentitas->HrefValue = "";
			$this->NoIdentitas->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// Kode
			$this->Kode->EditAttrs["class"] = "form-control";
			$this->Kode->EditCustomAttributes = "";
			$this->Kode->EditValue = ew_HtmlEncode($this->Kode->AdvancedSearch->SearchValue);
			$this->Kode->PlaceHolder = ew_RemoveHtml($this->Kode->FldCaption());

			// KodeHukum
			$this->KodeHukum->EditAttrs["class"] = "form-control";
			$this->KodeHukum->EditCustomAttributes = "";
			$this->KodeHukum->EditValue = ew_HtmlEncode($this->KodeHukum->AdvancedSearch->SearchValue);
			$this->KodeHukum->PlaceHolder = ew_RemoveHtml($this->KodeHukum->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->AdvancedSearch->SearchValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// TglMulai
			$this->TglMulai->EditAttrs["class"] = "form-control";
			$this->TglMulai->EditCustomAttributes = "";
			$this->TglMulai->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->TglMulai->AdvancedSearch->SearchValue, 0), 8));
			$this->TglMulai->PlaceHolder = ew_RemoveHtml($this->TglMulai->FldCaption());

			// Alamat1
			$this->Alamat1->EditAttrs["class"] = "form-control";
			$this->Alamat1->EditCustomAttributes = "";
			$this->Alamat1->EditValue = ew_HtmlEncode($this->Alamat1->AdvancedSearch->SearchValue);
			$this->Alamat1->PlaceHolder = ew_RemoveHtml($this->Alamat1->FldCaption());

			// Alamat2
			$this->Alamat2->EditAttrs["class"] = "form-control";
			$this->Alamat2->EditCustomAttributes = "";
			$this->Alamat2->EditValue = ew_HtmlEncode($this->Alamat2->AdvancedSearch->SearchValue);
			$this->Alamat2->PlaceHolder = ew_RemoveHtml($this->Alamat2->FldCaption());

			// Kota
			$this->Kota->EditAttrs["class"] = "form-control";
			$this->Kota->EditCustomAttributes = "";
			$this->Kota->EditValue = ew_HtmlEncode($this->Kota->AdvancedSearch->SearchValue);
			$this->Kota->PlaceHolder = ew_RemoveHtml($this->Kota->FldCaption());

			// KodePos
			$this->KodePos->EditAttrs["class"] = "form-control";
			$this->KodePos->EditCustomAttributes = "";
			$this->KodePos->EditValue = ew_HtmlEncode($this->KodePos->AdvancedSearch->SearchValue);
			$this->KodePos->PlaceHolder = ew_RemoveHtml($this->KodePos->FldCaption());

			// Telepon
			$this->Telepon->EditAttrs["class"] = "form-control";
			$this->Telepon->EditCustomAttributes = "";
			$this->Telepon->EditValue = ew_HtmlEncode($this->Telepon->AdvancedSearch->SearchValue);
			$this->Telepon->PlaceHolder = ew_RemoveHtml($this->Telepon->FldCaption());

			// Fax
			$this->Fax->EditAttrs["class"] = "form-control";
			$this->Fax->EditCustomAttributes = "";
			$this->Fax->EditValue = ew_HtmlEncode($this->Fax->AdvancedSearch->SearchValue);
			$this->Fax->PlaceHolder = ew_RemoveHtml($this->Fax->FldCaption());

			// Email
			$this->_Email->EditAttrs["class"] = "form-control";
			$this->_Email->EditCustomAttributes = "";
			$this->_Email->EditValue = ew_HtmlEncode($this->_Email->AdvancedSearch->SearchValue);
			$this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

			// Website
			$this->Website->EditAttrs["class"] = "form-control";
			$this->Website->EditCustomAttributes = "";
			$this->Website->EditValue = ew_HtmlEncode($this->Website->AdvancedSearch->SearchValue);
			$this->Website->PlaceHolder = ew_RemoveHtml($this->Website->FldCaption());

			// NoAkta
			$this->NoAkta->EditAttrs["class"] = "form-control";
			$this->NoAkta->EditCustomAttributes = "";
			$this->NoAkta->EditValue = ew_HtmlEncode($this->NoAkta->AdvancedSearch->SearchValue);
			$this->NoAkta->PlaceHolder = ew_RemoveHtml($this->NoAkta->FldCaption());

			// TglAkta
			$this->TglAkta->EditAttrs["class"] = "form-control";
			$this->TglAkta->EditCustomAttributes = "";
			$this->TglAkta->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->TglAkta->AdvancedSearch->SearchValue, 0), 8));
			$this->TglAkta->PlaceHolder = ew_RemoveHtml($this->TglAkta->FldCaption());

			// NoSah
			$this->NoSah->EditAttrs["class"] = "form-control";
			$this->NoSah->EditCustomAttributes = "";
			$this->NoSah->EditValue = ew_HtmlEncode($this->NoSah->AdvancedSearch->SearchValue);
			$this->NoSah->PlaceHolder = ew_RemoveHtml($this->NoSah->FldCaption());

			// TglSah
			$this->TglSah->EditAttrs["class"] = "form-control";
			$this->TglSah->EditCustomAttributes = "";
			$this->TglSah->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->TglSah->AdvancedSearch->SearchValue, 0), 8));
			$this->TglSah->PlaceHolder = ew_RemoveHtml($this->TglSah->FldCaption());

			// Logo
			$this->Logo->EditAttrs["class"] = "form-control";
			$this->Logo->EditCustomAttributes = "";
			$this->Logo->EditValue = ew_HtmlEncode($this->Logo->AdvancedSearch->SearchValue);
			$this->Logo->PlaceHolder = ew_RemoveHtml($this->Logo->FldCaption());

			// StartNoIdentitas
			$this->StartNoIdentitas->EditAttrs["class"] = "form-control";
			$this->StartNoIdentitas->EditCustomAttributes = "";
			$this->StartNoIdentitas->EditValue = ew_HtmlEncode($this->StartNoIdentitas->AdvancedSearch->SearchValue);
			$this->StartNoIdentitas->PlaceHolder = ew_RemoveHtml($this->StartNoIdentitas->FldCaption());

			// NoIdentitas
			$this->NoIdentitas->EditAttrs["class"] = "form-control";
			$this->NoIdentitas->EditCustomAttributes = "";
			$this->NoIdentitas->EditValue = ew_HtmlEncode($this->NoIdentitas->AdvancedSearch->SearchValue);
			$this->NoIdentitas->PlaceHolder = ew_RemoveHtml($this->NoIdentitas->FldCaption());

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
		if (!ew_CheckDateDef($this->TglMulai->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TglMulai->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TglAkta->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TglAkta->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TglSah->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TglSah->FldErrMsg());
		}
		if (!ew_CheckInteger($this->StartNoIdentitas->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->StartNoIdentitas->FldErrMsg());
		}
		if (!ew_CheckInteger($this->NoIdentitas->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->NoIdentitas->FldErrMsg());
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
		$this->Kode->AdvancedSearch->Load();
		$this->KodeHukum->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->TglMulai->AdvancedSearch->Load();
		$this->Alamat1->AdvancedSearch->Load();
		$this->Alamat2->AdvancedSearch->Load();
		$this->Kota->AdvancedSearch->Load();
		$this->KodePos->AdvancedSearch->Load();
		$this->Telepon->AdvancedSearch->Load();
		$this->Fax->AdvancedSearch->Load();
		$this->_Email->AdvancedSearch->Load();
		$this->Website->AdvancedSearch->Load();
		$this->NoAkta->AdvancedSearch->Load();
		$this->TglAkta->AdvancedSearch->Load();
		$this->NoSah->AdvancedSearch->Load();
		$this->TglSah->AdvancedSearch->Load();
		$this->Logo->AdvancedSearch->Load();
		$this->StartNoIdentitas->AdvancedSearch->Load();
		$this->NoIdentitas->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("identitaslist.php"), "", $this->TableVar, TRUE);
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
if (!isset($identitas_search)) $identitas_search = new cidentitas_search();

// Page init
$identitas_search->Page_Init();

// Page main
$identitas_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$identitas_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($identitas_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fidentitassearch = new ew_Form("fidentitassearch", "search");
<?php } else { ?>
var CurrentForm = fidentitassearch = new ew_Form("fidentitassearch", "search");
<?php } ?>

// Form_CustomValidate event
fidentitassearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fidentitassearch.ValidateRequired = true;
<?php } else { ?>
fidentitassearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fidentitassearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fidentitassearch.Lists["x_NA"].Options = <?php echo json_encode($identitas->NA->Options()) ?>;

// Form object for search
// Validate function for search

fidentitassearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_TglMulai");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($identitas->TglMulai->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_TglAkta");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($identitas->TglAkta->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_TglSah");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($identitas->TglSah->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_StartNoIdentitas");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($identitas->StartNoIdentitas->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_NoIdentitas");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($identitas->NoIdentitas->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$identitas_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $identitas_search->ShowPageHeader(); ?>
<?php
$identitas_search->ShowMessage();
?>
<form name="fidentitassearch" id="fidentitassearch" class="<?php echo $identitas_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($identitas_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $identitas_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="identitas">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($identitas_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($identitas->Kode->Visible) { // Kode ?>
	<div id="r_Kode" class="form-group">
		<label for="x_Kode" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_Kode"><?php echo $identitas->Kode->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Kode" id="z_Kode" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->Kode->CellAttributes() ?>>
			<span id="el_identitas_Kode">
<input type="text" data-table="identitas" data-field="x_Kode" name="x_Kode" id="x_Kode" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($identitas->Kode->getPlaceHolder()) ?>" value="<?php echo $identitas->Kode->EditValue ?>"<?php echo $identitas->Kode->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->KodeHukum->Visible) { // KodeHukum ?>
	<div id="r_KodeHukum" class="form-group">
		<label for="x_KodeHukum" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_KodeHukum"><?php echo $identitas->KodeHukum->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodeHukum" id="z_KodeHukum" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->KodeHukum->CellAttributes() ?>>
			<span id="el_identitas_KodeHukum">
<input type="text" data-table="identitas" data-field="x_KodeHukum" name="x_KodeHukum" id="x_KodeHukum" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($identitas->KodeHukum->getPlaceHolder()) ?>" value="<?php echo $identitas->KodeHukum->EditValue ?>"<?php echo $identitas->KodeHukum->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->Nama->Visible) { // Nama ?>
	<div id="r_Nama" class="form-group">
		<label for="x_Nama" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_Nama"><?php echo $identitas->Nama->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->Nama->CellAttributes() ?>>
			<span id="el_identitas_Nama">
<input type="text" data-table="identitas" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($identitas->Nama->getPlaceHolder()) ?>" value="<?php echo $identitas->Nama->EditValue ?>"<?php echo $identitas->Nama->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->TglMulai->Visible) { // TglMulai ?>
	<div id="r_TglMulai" class="form-group">
		<label for="x_TglMulai" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_TglMulai"><?php echo $identitas->TglMulai->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TglMulai" id="z_TglMulai" value="="></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->TglMulai->CellAttributes() ?>>
			<span id="el_identitas_TglMulai">
<input type="text" data-table="identitas" data-field="x_TglMulai" name="x_TglMulai" id="x_TglMulai" placeholder="<?php echo ew_HtmlEncode($identitas->TglMulai->getPlaceHolder()) ?>" value="<?php echo $identitas->TglMulai->EditValue ?>"<?php echo $identitas->TglMulai->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->Alamat1->Visible) { // Alamat1 ?>
	<div id="r_Alamat1" class="form-group">
		<label for="x_Alamat1" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_Alamat1"><?php echo $identitas->Alamat1->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Alamat1" id="z_Alamat1" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->Alamat1->CellAttributes() ?>>
			<span id="el_identitas_Alamat1">
<input type="text" data-table="identitas" data-field="x_Alamat1" name="x_Alamat1" id="x_Alamat1" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($identitas->Alamat1->getPlaceHolder()) ?>" value="<?php echo $identitas->Alamat1->EditValue ?>"<?php echo $identitas->Alamat1->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->Alamat2->Visible) { // Alamat2 ?>
	<div id="r_Alamat2" class="form-group">
		<label for="x_Alamat2" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_Alamat2"><?php echo $identitas->Alamat2->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Alamat2" id="z_Alamat2" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->Alamat2->CellAttributes() ?>>
			<span id="el_identitas_Alamat2">
<input type="text" data-table="identitas" data-field="x_Alamat2" name="x_Alamat2" id="x_Alamat2" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($identitas->Alamat2->getPlaceHolder()) ?>" value="<?php echo $identitas->Alamat2->EditValue ?>"<?php echo $identitas->Alamat2->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->Kota->Visible) { // Kota ?>
	<div id="r_Kota" class="form-group">
		<label for="x_Kota" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_Kota"><?php echo $identitas->Kota->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Kota" id="z_Kota" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->Kota->CellAttributes() ?>>
			<span id="el_identitas_Kota">
<input type="text" data-table="identitas" data-field="x_Kota" name="x_Kota" id="x_Kota" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($identitas->Kota->getPlaceHolder()) ?>" value="<?php echo $identitas->Kota->EditValue ?>"<?php echo $identitas->Kota->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->KodePos->Visible) { // KodePos ?>
	<div id="r_KodePos" class="form-group">
		<label for="x_KodePos" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_KodePos"><?php echo $identitas->KodePos->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodePos" id="z_KodePos" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->KodePos->CellAttributes() ?>>
			<span id="el_identitas_KodePos">
<input type="text" data-table="identitas" data-field="x_KodePos" name="x_KodePos" id="x_KodePos" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($identitas->KodePos->getPlaceHolder()) ?>" value="<?php echo $identitas->KodePos->EditValue ?>"<?php echo $identitas->KodePos->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->Telepon->Visible) { // Telepon ?>
	<div id="r_Telepon" class="form-group">
		<label for="x_Telepon" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_Telepon"><?php echo $identitas->Telepon->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Telepon" id="z_Telepon" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->Telepon->CellAttributes() ?>>
			<span id="el_identitas_Telepon">
<input type="text" data-table="identitas" data-field="x_Telepon" name="x_Telepon" id="x_Telepon" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($identitas->Telepon->getPlaceHolder()) ?>" value="<?php echo $identitas->Telepon->EditValue ?>"<?php echo $identitas->Telepon->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->Fax->Visible) { // Fax ?>
	<div id="r_Fax" class="form-group">
		<label for="x_Fax" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_Fax"><?php echo $identitas->Fax->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Fax" id="z_Fax" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->Fax->CellAttributes() ?>>
			<span id="el_identitas_Fax">
<input type="text" data-table="identitas" data-field="x_Fax" name="x_Fax" id="x_Fax" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($identitas->Fax->getPlaceHolder()) ?>" value="<?php echo $identitas->Fax->EditValue ?>"<?php echo $identitas->Fax->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->_Email->Visible) { // Email ?>
	<div id="r__Email" class="form-group">
		<label for="x__Email" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas__Email"><?php echo $identitas->_Email->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z__Email" id="z__Email" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->_Email->CellAttributes() ?>>
			<span id="el_identitas__Email">
<input type="text" data-table="identitas" data-field="x__Email" name="x__Email" id="x__Email" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($identitas->_Email->getPlaceHolder()) ?>" value="<?php echo $identitas->_Email->EditValue ?>"<?php echo $identitas->_Email->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->Website->Visible) { // Website ?>
	<div id="r_Website" class="form-group">
		<label for="x_Website" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_Website"><?php echo $identitas->Website->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Website" id="z_Website" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->Website->CellAttributes() ?>>
			<span id="el_identitas_Website">
<input type="text" data-table="identitas" data-field="x_Website" name="x_Website" id="x_Website" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($identitas->Website->getPlaceHolder()) ?>" value="<?php echo $identitas->Website->EditValue ?>"<?php echo $identitas->Website->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->NoAkta->Visible) { // NoAkta ?>
	<div id="r_NoAkta" class="form-group">
		<label for="x_NoAkta" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_NoAkta"><?php echo $identitas->NoAkta->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NoAkta" id="z_NoAkta" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->NoAkta->CellAttributes() ?>>
			<span id="el_identitas_NoAkta">
<input type="text" data-table="identitas" data-field="x_NoAkta" name="x_NoAkta" id="x_NoAkta" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($identitas->NoAkta->getPlaceHolder()) ?>" value="<?php echo $identitas->NoAkta->EditValue ?>"<?php echo $identitas->NoAkta->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->TglAkta->Visible) { // TglAkta ?>
	<div id="r_TglAkta" class="form-group">
		<label for="x_TglAkta" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_TglAkta"><?php echo $identitas->TglAkta->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TglAkta" id="z_TglAkta" value="="></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->TglAkta->CellAttributes() ?>>
			<span id="el_identitas_TglAkta">
<input type="text" data-table="identitas" data-field="x_TglAkta" name="x_TglAkta" id="x_TglAkta" placeholder="<?php echo ew_HtmlEncode($identitas->TglAkta->getPlaceHolder()) ?>" value="<?php echo $identitas->TglAkta->EditValue ?>"<?php echo $identitas->TglAkta->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->NoSah->Visible) { // NoSah ?>
	<div id="r_NoSah" class="form-group">
		<label for="x_NoSah" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_NoSah"><?php echo $identitas->NoSah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NoSah" id="z_NoSah" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->NoSah->CellAttributes() ?>>
			<span id="el_identitas_NoSah">
<input type="text" data-table="identitas" data-field="x_NoSah" name="x_NoSah" id="x_NoSah" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($identitas->NoSah->getPlaceHolder()) ?>" value="<?php echo $identitas->NoSah->EditValue ?>"<?php echo $identitas->NoSah->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->TglSah->Visible) { // TglSah ?>
	<div id="r_TglSah" class="form-group">
		<label for="x_TglSah" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_TglSah"><?php echo $identitas->TglSah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TglSah" id="z_TglSah" value="="></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->TglSah->CellAttributes() ?>>
			<span id="el_identitas_TglSah">
<input type="text" data-table="identitas" data-field="x_TglSah" name="x_TglSah" id="x_TglSah" placeholder="<?php echo ew_HtmlEncode($identitas->TglSah->getPlaceHolder()) ?>" value="<?php echo $identitas->TglSah->EditValue ?>"<?php echo $identitas->TglSah->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->Logo->Visible) { // Logo ?>
	<div id="r_Logo" class="form-group">
		<label for="x_Logo" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_Logo"><?php echo $identitas->Logo->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Logo" id="z_Logo" value="LIKE"></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->Logo->CellAttributes() ?>>
			<span id="el_identitas_Logo">
<input type="text" data-table="identitas" data-field="x_Logo" name="x_Logo" id="x_Logo" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($identitas->Logo->getPlaceHolder()) ?>" value="<?php echo $identitas->Logo->EditValue ?>"<?php echo $identitas->Logo->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->StartNoIdentitas->Visible) { // StartNoIdentitas ?>
	<div id="r_StartNoIdentitas" class="form-group">
		<label for="x_StartNoIdentitas" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_StartNoIdentitas"><?php echo $identitas->StartNoIdentitas->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_StartNoIdentitas" id="z_StartNoIdentitas" value="="></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->StartNoIdentitas->CellAttributes() ?>>
			<span id="el_identitas_StartNoIdentitas">
<input type="text" data-table="identitas" data-field="x_StartNoIdentitas" name="x_StartNoIdentitas" id="x_StartNoIdentitas" size="30" placeholder="<?php echo ew_HtmlEncode($identitas->StartNoIdentitas->getPlaceHolder()) ?>" value="<?php echo $identitas->StartNoIdentitas->EditValue ?>"<?php echo $identitas->StartNoIdentitas->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->NoIdentitas->Visible) { // NoIdentitas ?>
	<div id="r_NoIdentitas" class="form-group">
		<label for="x_NoIdentitas" class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_NoIdentitas"><?php echo $identitas->NoIdentitas->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NoIdentitas" id="z_NoIdentitas" value="="></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->NoIdentitas->CellAttributes() ?>>
			<span id="el_identitas_NoIdentitas">
<input type="text" data-table="identitas" data-field="x_NoIdentitas" name="x_NoIdentitas" id="x_NoIdentitas" size="30" placeholder="<?php echo ew_HtmlEncode($identitas->NoIdentitas->getPlaceHolder()) ?>" value="<?php echo $identitas->NoIdentitas->EditValue ?>"<?php echo $identitas->NoIdentitas->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($identitas->NA->Visible) { // NA ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $identitas_search->SearchLabelClass ?>"><span id="elh_identitas_NA"><?php echo $identitas->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $identitas_search->SearchRightColumnClass ?>"><div<?php echo $identitas->NA->CellAttributes() ?>>
			<span id="el_identitas_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="identitas" data-field="x_NA" data-value-separator="<?php echo $identitas->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $identitas->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $identitas->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } ?>
</div>
<?php if (!$identitas_search->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-3 col-sm-9">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("Search") ?></button>
<button class="btn btn-default ewButton" name="btnReset" id="btnReset" type="button" onclick="ew_ClearForm(this.form);"><?php echo $Language->Phrase("Reset") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fidentitassearch.Init();
</script>
<?php
$identitas_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$identitas_search->Page_Terminate();
?>
