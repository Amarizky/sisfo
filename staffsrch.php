<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "staffinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$staff_search = NULL; // Initialize page object first

class cstaff_search extends cstaff {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'staff';

	// Page object name
	var $PageObjName = 'staff_search';

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

		// Table object (staff)
		if (!isset($GLOBALS["staff"]) || get_class($GLOBALS["staff"]) == "cstaff") {
			$GLOBALS["staff"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["staff"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'staff', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("stafflist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->StaffID->SetVisibility();
		$this->LevelID->SetVisibility();
		$this->NIPPNS->SetVisibility();
		$this->Nama->SetVisibility();
		$this->Gelar->SetVisibility();
		$this->KTP->SetVisibility();
		$this->TempatLahir->SetVisibility();
		$this->TanggalLahir->SetVisibility();
		$this->KelaminID->SetVisibility();
		$this->AgamaID->SetVisibility();
		$this->Telephone->SetVisibility();
		$this->_Email->SetVisibility();
		$this->Alamat->SetVisibility();
		$this->KodePos->SetVisibility();
		$this->ProvinsiID->SetVisibility();
		$this->KabupatenKotaID->SetVisibility();
		$this->KecamatanID->SetVisibility();
		$this->DesaID->SetVisibility();
		$this->KampusID->SetVisibility();
		$this->BagianID->SetVisibility();
		$this->GolonganID->SetVisibility();
		$this->IkatanID->SetVisibility();
		$this->StatusKerjaID->SetVisibility();
		$this->TglBekerja->SetVisibility();
		$this->PendidikanTerakhir->SetVisibility();
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
		global $EW_EXPORT, $staff;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($staff);
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
	var $MultiPages; // Multi pages object

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
						$sSrchStr = "stafflist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->StaffID); // StaffID
		$this->BuildSearchUrl($sSrchUrl, $this->LevelID); // LevelID
		$this->BuildSearchUrl($sSrchUrl, $this->NIPPNS); // NIPPNS
		$this->BuildSearchUrl($sSrchUrl, $this->Nama); // Nama
		$this->BuildSearchUrl($sSrchUrl, $this->Gelar); // Gelar
		$this->BuildSearchUrl($sSrchUrl, $this->KTP); // KTP
		$this->BuildSearchUrl($sSrchUrl, $this->TempatLahir); // TempatLahir
		$this->BuildSearchUrl($sSrchUrl, $this->TanggalLahir); // TanggalLahir
		$this->BuildSearchUrl($sSrchUrl, $this->KelaminID); // KelaminID
		$this->BuildSearchUrl($sSrchUrl, $this->AgamaID); // AgamaID
		$this->BuildSearchUrl($sSrchUrl, $this->Telephone); // Telephone
		$this->BuildSearchUrl($sSrchUrl, $this->_Email); // Email
		$this->BuildSearchUrl($sSrchUrl, $this->Alamat); // Alamat
		$this->BuildSearchUrl($sSrchUrl, $this->KodePos); // KodePos
		$this->BuildSearchUrl($sSrchUrl, $this->ProvinsiID); // ProvinsiID
		$this->BuildSearchUrl($sSrchUrl, $this->KabupatenKotaID); // KabupatenKotaID
		$this->BuildSearchUrl($sSrchUrl, $this->KecamatanID); // KecamatanID
		$this->BuildSearchUrl($sSrchUrl, $this->DesaID); // DesaID
		$this->BuildSearchUrl($sSrchUrl, $this->KampusID); // KampusID
		$this->BuildSearchUrl($sSrchUrl, $this->BagianID); // BagianID
		$this->BuildSearchUrl($sSrchUrl, $this->GolonganID); // GolonganID
		$this->BuildSearchUrl($sSrchUrl, $this->IkatanID); // IkatanID
		$this->BuildSearchUrl($sSrchUrl, $this->StatusKerjaID); // StatusKerjaID
		$this->BuildSearchUrl($sSrchUrl, $this->TglBekerja); // TglBekerja
		$this->BuildSearchUrl($sSrchUrl, $this->PendidikanTerakhir); // PendidikanTerakhir
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
		// StaffID

		$this->StaffID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StaffID"));
		$this->StaffID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StaffID");

		// LevelID
		$this->LevelID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_LevelID"));
		$this->LevelID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_LevelID");

		// NIPPNS
		$this->NIPPNS->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NIPPNS"));
		$this->NIPPNS->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NIPPNS");

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Nama"));
		$this->Nama->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Nama");

		// Gelar
		$this->Gelar->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Gelar"));
		$this->Gelar->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Gelar");

		// KTP
		$this->KTP->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KTP"));
		$this->KTP->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KTP");

		// TempatLahir
		$this->TempatLahir->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TempatLahir"));
		$this->TempatLahir->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TempatLahir");

		// TanggalLahir
		$this->TanggalLahir->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TanggalLahir"));
		$this->TanggalLahir->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TanggalLahir");

		// KelaminID
		$this->KelaminID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KelaminID"));
		$this->KelaminID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KelaminID");

		// AgamaID
		$this->AgamaID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_AgamaID"));
		$this->AgamaID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_AgamaID");

		// Telephone
		$this->Telephone->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Telephone"));
		$this->Telephone->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Telephone");

		// Email
		$this->_Email->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x__Email"));
		$this->_Email->AdvancedSearch->SearchOperator = $objForm->GetValue("z__Email");

		// Alamat
		$this->Alamat->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Alamat"));
		$this->Alamat->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Alamat");

		// KodePos
		$this->KodePos->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KodePos"));
		$this->KodePos->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KodePos");

		// ProvinsiID
		$this->ProvinsiID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ProvinsiID"));
		$this->ProvinsiID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ProvinsiID");

		// KabupatenKotaID
		$this->KabupatenKotaID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KabupatenKotaID"));
		$this->KabupatenKotaID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KabupatenKotaID");

		// KecamatanID
		$this->KecamatanID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KecamatanID"));
		$this->KecamatanID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KecamatanID");

		// DesaID
		$this->DesaID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_DesaID"));
		$this->DesaID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_DesaID");

		// KampusID
		$this->KampusID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KampusID"));
		$this->KampusID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KampusID");

		// BagianID
		$this->BagianID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_BagianID"));
		$this->BagianID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_BagianID");

		// GolonganID
		$this->GolonganID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_GolonganID"));
		$this->GolonganID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_GolonganID");

		// IkatanID
		$this->IkatanID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_IkatanID"));
		$this->IkatanID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_IkatanID");

		// StatusKerjaID
		$this->StatusKerjaID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StatusKerjaID"));
		$this->StatusKerjaID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StatusKerjaID");

		// TglBekerja
		$this->TglBekerja->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TglBekerja"));
		$this->TglBekerja->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TglBekerja");

		// PendidikanTerakhir
		$this->PendidikanTerakhir->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_PendidikanTerakhir"));
		$this->PendidikanTerakhir->AdvancedSearch->SearchOperator = $objForm->GetValue("z_PendidikanTerakhir");

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
		// StaffID
		// LevelID
		// Password
		// NIPPNS
		// Nama
		// Gelar
		// KTP
		// TempatLahir
		// TanggalLahir
		// KelaminID
		// AgamaID
		// Telephone
		// Handphone
		// Email
		// Alamat
		// KodePos
		// ProvinsiID
		// KabupatenKotaID
		// KecamatanID
		// DesaID
		// KampusID
		// BagianID
		// GolonganID
		// IkatanID
		// StatusKerjaID
		// TglBekerja
		// PendidikanTerakhir
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// StaffID
		$this->StaffID->ViewValue = $this->StaffID->CurrentValue;
		$this->StaffID->CssStyle = "font-weight: bold;";
		$this->StaffID->ViewCustomAttributes = "";

		// LevelID
		$this->LevelID->ViewValue = $this->LevelID->CurrentValue;
		$this->LevelID->ViewCustomAttributes = "";

		// NIPPNS
		$this->NIPPNS->ViewValue = $this->NIPPNS->CurrentValue;
		$this->NIPPNS->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->CssStyle = "font-weight: bold;";
		$this->Nama->ViewCustomAttributes = "";

		// Gelar
		$this->Gelar->ViewValue = $this->Gelar->CurrentValue;
		$this->Gelar->ViewCustomAttributes = "";

		// KTP
		$this->KTP->ViewValue = $this->KTP->CurrentValue;
		$this->KTP->ViewCustomAttributes = "";

		// TempatLahir
		$this->TempatLahir->ViewValue = $this->TempatLahir->CurrentValue;
		if (strval($this->TempatLahir->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->TempatLahir->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->TempatLahir->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->TempatLahir, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->TempatLahir->ViewValue = $this->TempatLahir->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->TempatLahir->ViewValue = $this->TempatLahir->CurrentValue;
			}
		} else {
			$this->TempatLahir->ViewValue = NULL;
		}
		$this->TempatLahir->ViewCustomAttributes = "";

		// TanggalLahir
		$this->TanggalLahir->ViewValue = $this->TanggalLahir->CurrentValue;
		$this->TanggalLahir->ViewValue = ew_FormatDateTime($this->TanggalLahir->ViewValue, 0);
		$this->TanggalLahir->ViewCustomAttributes = "";

		// KelaminID
		if (strval($this->KelaminID->CurrentValue) <> "") {
			$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->KelaminID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
		$sWhereWrk = "";
		$this->KelaminID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KelaminID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KelaminID->ViewValue = $this->KelaminID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KelaminID->ViewValue = $this->KelaminID->CurrentValue;
			}
		} else {
			$this->KelaminID->ViewValue = NULL;
		}
		$this->KelaminID->ViewCustomAttributes = "";

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

		// Telephone
		$this->Telephone->ViewValue = $this->Telephone->CurrentValue;
		$this->Telephone->ViewCustomAttributes = "";

		// Handphone
		$this->Handphone->ViewValue = $this->Handphone->CurrentValue;
		$this->Handphone->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// Alamat
		$this->Alamat->ViewValue = $this->Alamat->CurrentValue;
		$this->Alamat->ViewCustomAttributes = "";

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

		// KampusID
		$this->KampusID->ViewValue = $this->KampusID->CurrentValue;
		$this->KampusID->ViewCustomAttributes = "";

		// BagianID
		if (strval($this->BagianID->CurrentValue) <> "") {
			$sFilterWrk = "`BagianID`" . ew_SearchString("=", $this->BagianID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `BagianID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_bagian`";
		$sWhereWrk = "";
		$this->BagianID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->BagianID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->BagianID->ViewValue = $this->BagianID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->BagianID->ViewValue = $this->BagianID->CurrentValue;
			}
		} else {
			$this->BagianID->ViewValue = NULL;
		}
		$this->BagianID->ViewCustomAttributes = "";

		// GolonganID
		if (strval($this->GolonganID->CurrentValue) <> "") {
			$sFilterWrk = "`GolonganID`" . ew_SearchString("=", $this->GolonganID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `GolonganID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_golongan`";
		$sWhereWrk = "";
		$this->GolonganID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->GolonganID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->GolonganID->ViewValue = $this->GolonganID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->GolonganID->ViewValue = $this->GolonganID->CurrentValue;
			}
		} else {
			$this->GolonganID->ViewValue = NULL;
		}
		$this->GolonganID->ViewCustomAttributes = "";

		// IkatanID
		if (strval($this->IkatanID->CurrentValue) <> "") {
			$sFilterWrk = "`IkatanID`" . ew_SearchString("=", $this->IkatanID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `IkatanID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_ikatan`";
		$sWhereWrk = "";
		$this->IkatanID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->IkatanID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->IkatanID->ViewValue = $this->IkatanID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->IkatanID->ViewValue = $this->IkatanID->CurrentValue;
			}
		} else {
			$this->IkatanID->ViewValue = NULL;
		}
		$this->IkatanID->ViewCustomAttributes = "";

		// StatusKerjaID
		if (strval($this->StatusKerjaID->CurrentValue) <> "") {
			$sFilterWrk = "`StatusKerjaID`" . ew_SearchString("=", $this->StatusKerjaID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusKerjaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statuskerja`";
		$sWhereWrk = "";
		$this->StatusKerjaID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StatusKerjaID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StatusKerjaID->ViewValue = $this->StatusKerjaID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StatusKerjaID->ViewValue = $this->StatusKerjaID->CurrentValue;
			}
		} else {
			$this->StatusKerjaID->ViewValue = NULL;
		}
		$this->StatusKerjaID->ViewCustomAttributes = "";

		// TglBekerja
		$this->TglBekerja->ViewValue = $this->TglBekerja->CurrentValue;
		$this->TglBekerja->ViewValue = ew_FormatDateTime($this->TglBekerja->ViewValue, 0);
		$this->TglBekerja->ViewCustomAttributes = "";

		// PendidikanTerakhir
		if (strval($this->PendidikanTerakhir->CurrentValue) <> "") {
			$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanTerakhir->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
		$sWhereWrk = "";
		$this->PendidikanTerakhir->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PendidikanTerakhir, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PendidikanTerakhir->ViewValue = $this->PendidikanTerakhir->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PendidikanTerakhir->ViewValue = $this->PendidikanTerakhir->CurrentValue;
			}
		} else {
			$this->PendidikanTerakhir->ViewValue = NULL;
		}
		$this->PendidikanTerakhir->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Ya";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "Tidak";
		}
		$this->NA->ViewCustomAttributes = "";

			// StaffID
			$this->StaffID->LinkCustomAttributes = "";
			$this->StaffID->HrefValue = "";
			$this->StaffID->TooltipValue = "";

			// LevelID
			$this->LevelID->LinkCustomAttributes = "";
			$this->LevelID->HrefValue = "";
			$this->LevelID->TooltipValue = "";

			// NIPPNS
			$this->NIPPNS->LinkCustomAttributes = "";
			$this->NIPPNS->HrefValue = "";
			$this->NIPPNS->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// Gelar
			$this->Gelar->LinkCustomAttributes = "";
			$this->Gelar->HrefValue = "";
			$this->Gelar->TooltipValue = "";

			// KTP
			$this->KTP->LinkCustomAttributes = "";
			$this->KTP->HrefValue = "";
			$this->KTP->TooltipValue = "";

			// TempatLahir
			$this->TempatLahir->LinkCustomAttributes = "";
			$this->TempatLahir->HrefValue = "";
			$this->TempatLahir->TooltipValue = "";

			// TanggalLahir
			$this->TanggalLahir->LinkCustomAttributes = "";
			$this->TanggalLahir->HrefValue = "";
			$this->TanggalLahir->TooltipValue = "";

			// KelaminID
			$this->KelaminID->LinkCustomAttributes = "";
			$this->KelaminID->HrefValue = "";
			$this->KelaminID->TooltipValue = "";

			// AgamaID
			$this->AgamaID->LinkCustomAttributes = "";
			$this->AgamaID->HrefValue = "";
			$this->AgamaID->TooltipValue = "";

			// Telephone
			$this->Telephone->LinkCustomAttributes = "";
			$this->Telephone->HrefValue = "";
			$this->Telephone->TooltipValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";

			// Alamat
			$this->Alamat->LinkCustomAttributes = "";
			$this->Alamat->HrefValue = "";
			$this->Alamat->TooltipValue = "";

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

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";
			$this->KampusID->TooltipValue = "";

			// BagianID
			$this->BagianID->LinkCustomAttributes = "";
			$this->BagianID->HrefValue = "";
			$this->BagianID->TooltipValue = "";

			// GolonganID
			$this->GolonganID->LinkCustomAttributes = "";
			$this->GolonganID->HrefValue = "";
			$this->GolonganID->TooltipValue = "";

			// IkatanID
			$this->IkatanID->LinkCustomAttributes = "";
			$this->IkatanID->HrefValue = "";
			$this->IkatanID->TooltipValue = "";

			// StatusKerjaID
			$this->StatusKerjaID->LinkCustomAttributes = "";
			$this->StatusKerjaID->HrefValue = "";
			$this->StatusKerjaID->TooltipValue = "";

			// TglBekerja
			$this->TglBekerja->LinkCustomAttributes = "";
			$this->TglBekerja->HrefValue = "";
			$this->TglBekerja->TooltipValue = "";

			// PendidikanTerakhir
			$this->PendidikanTerakhir->LinkCustomAttributes = "";
			$this->PendidikanTerakhir->HrefValue = "";
			$this->PendidikanTerakhir->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// StaffID
			$this->StaffID->EditAttrs["class"] = "form-control";
			$this->StaffID->EditCustomAttributes = "";
			$this->StaffID->EditValue = ew_HtmlEncode($this->StaffID->AdvancedSearch->SearchValue);
			$this->StaffID->PlaceHolder = ew_RemoveHtml($this->StaffID->FldCaption());

			// LevelID
			$this->LevelID->EditAttrs["class"] = "form-control";
			$this->LevelID->EditCustomAttributes = "";
			$this->LevelID->EditValue = ew_HtmlEncode($this->LevelID->AdvancedSearch->SearchValue);
			$this->LevelID->PlaceHolder = ew_RemoveHtml($this->LevelID->FldCaption());

			// NIPPNS
			$this->NIPPNS->EditAttrs["class"] = "form-control";
			$this->NIPPNS->EditCustomAttributes = "";
			$this->NIPPNS->EditValue = ew_HtmlEncode($this->NIPPNS->AdvancedSearch->SearchValue);
			$this->NIPPNS->PlaceHolder = ew_RemoveHtml($this->NIPPNS->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->AdvancedSearch->SearchValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// Gelar
			$this->Gelar->EditAttrs["class"] = "form-control";
			$this->Gelar->EditCustomAttributes = "";
			$this->Gelar->EditValue = ew_HtmlEncode($this->Gelar->AdvancedSearch->SearchValue);
			$this->Gelar->PlaceHolder = ew_RemoveHtml($this->Gelar->FldCaption());

			// KTP
			$this->KTP->EditAttrs["class"] = "form-control";
			$this->KTP->EditCustomAttributes = "";
			$this->KTP->EditValue = ew_HtmlEncode($this->KTP->AdvancedSearch->SearchValue);
			$this->KTP->PlaceHolder = ew_RemoveHtml($this->KTP->FldCaption());

			// TempatLahir
			$this->TempatLahir->EditAttrs["class"] = "form-control";
			$this->TempatLahir->EditCustomAttributes = "";
			$this->TempatLahir->EditValue = ew_HtmlEncode($this->TempatLahir->AdvancedSearch->SearchValue);
			if (strval($this->TempatLahir->AdvancedSearch->SearchValue) <> "") {
				$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->TempatLahir->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "";
			$this->TempatLahir->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->TempatLahir, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$this->TempatLahir->EditValue = $this->TempatLahir->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->TempatLahir->EditValue = ew_HtmlEncode($this->TempatLahir->AdvancedSearch->SearchValue);
				}
			} else {
				$this->TempatLahir->EditValue = NULL;
			}
			$this->TempatLahir->PlaceHolder = ew_RemoveHtml($this->TempatLahir->FldCaption());

			// TanggalLahir
			$this->TanggalLahir->EditAttrs["class"] = "form-control";
			$this->TanggalLahir->EditCustomAttributes = "";
			$this->TanggalLahir->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->TanggalLahir->AdvancedSearch->SearchValue, 0), 8));
			$this->TanggalLahir->PlaceHolder = ew_RemoveHtml($this->TanggalLahir->FldCaption());

			// KelaminID
			$this->KelaminID->EditCustomAttributes = "";
			if (trim(strval($this->KelaminID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->KelaminID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_kelamin`";
			$sWhereWrk = "";
			$this->KelaminID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KelaminID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KelaminID->EditValue = $arwrk;

			// AgamaID
			$this->AgamaID->EditAttrs["class"] = "form-control";
			$this->AgamaID->EditCustomAttributes = "";
			if (trim(strval($this->AgamaID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->AgamaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->AgamaID->EditValue = $arwrk;

			// Telephone
			$this->Telephone->EditAttrs["class"] = "form-control";
			$this->Telephone->EditCustomAttributes = "";
			$this->Telephone->EditValue = ew_HtmlEncode($this->Telephone->AdvancedSearch->SearchValue);
			$this->Telephone->PlaceHolder = ew_RemoveHtml($this->Telephone->FldCaption());

			// Email
			$this->_Email->EditAttrs["class"] = "form-control";
			$this->_Email->EditCustomAttributes = "";
			$this->_Email->EditValue = ew_HtmlEncode($this->_Email->AdvancedSearch->SearchValue);
			$this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

			// Alamat
			$this->Alamat->EditAttrs["class"] = "form-control";
			$this->Alamat->EditCustomAttributes = "";
			$this->Alamat->EditValue = ew_HtmlEncode($this->Alamat->AdvancedSearch->SearchValue);
			$this->Alamat->PlaceHolder = ew_RemoveHtml($this->Alamat->FldCaption());

			// KodePos
			$this->KodePos->EditAttrs["class"] = "form-control";
			$this->KodePos->EditCustomAttributes = "";
			$this->KodePos->EditValue = ew_HtmlEncode($this->KodePos->AdvancedSearch->SearchValue);
			$this->KodePos->PlaceHolder = ew_RemoveHtml($this->KodePos->FldCaption());

			// ProvinsiID
			$this->ProvinsiID->EditAttrs["class"] = "form-control";
			$this->ProvinsiID->EditCustomAttributes = "";
			if (trim(strval($this->ProvinsiID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->ProvinsiID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProvinsiID->EditValue = $arwrk;

			// KabupatenKotaID
			$this->KabupatenKotaID->EditAttrs["class"] = "form-control";
			$this->KabupatenKotaID->EditCustomAttributes = "";
			if (trim(strval($this->KabupatenKotaID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenKotaID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProvinsiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "";
			$this->KabupatenKotaID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KabupatenKotaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KabupatenKotaID->EditValue = $arwrk;

			// KecamatanID
			$this->KecamatanID->EditAttrs["class"] = "form-control";
			$this->KecamatanID->EditCustomAttributes = "";
			if (trim(strval($this->KecamatanID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KabupatenKotaID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "";
			$this->KecamatanID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KecamatanID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KecamatanID->EditValue = $arwrk;

			// DesaID
			$this->DesaID->EditAttrs["class"] = "form-control";
			$this->DesaID->EditCustomAttributes = "";
			if (trim(strval($this->DesaID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KecamatanID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_desa`";
			$sWhereWrk = "";
			$this->DesaID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->DesaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->DesaID->EditValue = $arwrk;

			// KampusID
			$this->KampusID->EditAttrs["class"] = "form-control";
			$this->KampusID->EditCustomAttributes = "";
			$this->KampusID->EditValue = ew_HtmlEncode($this->KampusID->AdvancedSearch->SearchValue);
			$this->KampusID->PlaceHolder = ew_RemoveHtml($this->KampusID->FldCaption());

			// BagianID
			$this->BagianID->EditAttrs["class"] = "form-control";
			$this->BagianID->EditCustomAttributes = "";
			if (trim(strval($this->BagianID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`BagianID`" . ew_SearchString("=", $this->BagianID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `BagianID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_bagian`";
			$sWhereWrk = "";
			$this->BagianID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->BagianID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->BagianID->EditValue = $arwrk;

			// GolonganID
			$this->GolonganID->EditAttrs["class"] = "form-control";
			$this->GolonganID->EditCustomAttributes = "";
			if (trim(strval($this->GolonganID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`GolonganID`" . ew_SearchString("=", $this->GolonganID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `GolonganID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_golongan`";
			$sWhereWrk = "";
			$this->GolonganID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->GolonganID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->GolonganID->EditValue = $arwrk;

			// IkatanID
			$this->IkatanID->EditAttrs["class"] = "form-control";
			$this->IkatanID->EditCustomAttributes = "";
			if (trim(strval($this->IkatanID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`IkatanID`" . ew_SearchString("=", $this->IkatanID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `IkatanID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_ikatan`";
			$sWhereWrk = "";
			$this->IkatanID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->IkatanID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->IkatanID->EditValue = $arwrk;

			// StatusKerjaID
			$this->StatusKerjaID->EditAttrs["class"] = "form-control";
			$this->StatusKerjaID->EditCustomAttributes = "";
			if (trim(strval($this->StatusKerjaID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StatusKerjaID`" . ew_SearchString("=", $this->StatusKerjaID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StatusKerjaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_statuskerja`";
			$sWhereWrk = "";
			$this->StatusKerjaID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StatusKerjaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StatusKerjaID->EditValue = $arwrk;

			// TglBekerja
			$this->TglBekerja->EditAttrs["class"] = "form-control";
			$this->TglBekerja->EditCustomAttributes = "";
			$this->TglBekerja->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->TglBekerja->AdvancedSearch->SearchValue, 0), 8));
			$this->TglBekerja->PlaceHolder = ew_RemoveHtml($this->TglBekerja->FldCaption());

			// PendidikanTerakhir
			$this->PendidikanTerakhir->EditAttrs["class"] = "form-control";
			$this->PendidikanTerakhir->EditCustomAttributes = "";
			if (trim(strval($this->PendidikanTerakhir->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanTerakhir->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanTerakhir->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->PendidikanTerakhir, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->PendidikanTerakhir->EditValue = $arwrk;

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
		if (!ew_CheckInteger($this->LevelID->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->LevelID->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TanggalLahir->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TanggalLahir->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TglBekerja->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TglBekerja->FldErrMsg());
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
		$this->StaffID->AdvancedSearch->Load();
		$this->LevelID->AdvancedSearch->Load();
		$this->NIPPNS->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->Gelar->AdvancedSearch->Load();
		$this->KTP->AdvancedSearch->Load();
		$this->TempatLahir->AdvancedSearch->Load();
		$this->TanggalLahir->AdvancedSearch->Load();
		$this->KelaminID->AdvancedSearch->Load();
		$this->AgamaID->AdvancedSearch->Load();
		$this->Telephone->AdvancedSearch->Load();
		$this->_Email->AdvancedSearch->Load();
		$this->Alamat->AdvancedSearch->Load();
		$this->KodePos->AdvancedSearch->Load();
		$this->ProvinsiID->AdvancedSearch->Load();
		$this->KabupatenKotaID->AdvancedSearch->Load();
		$this->KecamatanID->AdvancedSearch->Load();
		$this->DesaID->AdvancedSearch->Load();
		$this->KampusID->AdvancedSearch->Load();
		$this->BagianID->AdvancedSearch->Load();
		$this->GolonganID->AdvancedSearch->Load();
		$this->IkatanID->AdvancedSearch->Load();
		$this->StatusKerjaID->AdvancedSearch->Load();
		$this->TglBekerja->AdvancedSearch->Load();
		$this->PendidikanTerakhir->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("stafflist.php"), "", $this->TableVar, TRUE);
		$PageId = "search";
		$Breadcrumb->Add("search", $PageId, $url);
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
		$this->MultiPages = $pages;
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_TempatLahir":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KabupatenKotaID` AS `LinkFld`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "{filter}";
			$this->TempatLahir->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KabupatenKotaID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TempatLahir, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KelaminID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Kelamin` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
			$sWhereWrk = "";
			$this->KelaminID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Kelamin` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KelaminID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_AgamaID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `AgamaID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`AgamaID` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->AgamaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_ProvinsiID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProvinsiID` AS `LinkFld`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProvinsiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->ProvinsiID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KabupatenKotaID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KabupatenKotaID` AS `LinkFld`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "{filter}";
			$this->KabupatenKotaID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KabupatenKotaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProvinsiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KabupatenKotaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KecamatanID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KecamatanID` AS `LinkFld`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "{filter}";
			$this->KecamatanID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KecamatanID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KabupatenKotaID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KecamatanID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_DesaID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `DesaID` AS `LinkFld`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
			$sWhereWrk = "{filter}";
			$this->DesaID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`DesaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KecamatanID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->DesaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_BagianID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `BagianID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_bagian`";
			$sWhereWrk = "";
			$this->BagianID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`BagianID` = {filter_value}', "t0" => "16", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->BagianID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_GolonganID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `GolonganID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_golongan`";
			$sWhereWrk = "";
			$this->GolonganID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`GolonganID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->GolonganID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_IkatanID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `IkatanID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_ikatan`";
			$sWhereWrk = "";
			$this->IkatanID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`IkatanID` = {filter_value}', "t0" => "16", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->IkatanID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StatusKerjaID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StatusKerjaID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statuskerja`";
			$sWhereWrk = "";
			$this->StatusKerjaID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StatusKerjaID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StatusKerjaID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_PendidikanTerakhir":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Pendidikan` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanTerakhir->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Pendidikan` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->PendidikanTerakhir, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_TempatLahir":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "`KabupatenKota` LIKE '%{query_value}%'";
			$this->TempatLahir->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TempatLahir, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
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
if (!isset($staff_search)) $staff_search = new cstaff_search();

// Page init
$staff_search->Page_Init();

// Page main
$staff_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$staff_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($staff_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fstaffsearch = new ew_Form("fstaffsearch", "search");
<?php } else { ?>
var CurrentForm = fstaffsearch = new ew_Form("fstaffsearch", "search");
<?php } ?>

// Form_CustomValidate event
fstaffsearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fstaffsearch.ValidateRequired = true;
<?php } else { ?>
fstaffsearch.ValidateRequired = false; 
<?php } ?>

// Multi-Page
fstaffsearch.MultiPage = new ew_MultiPage("fstaffsearch");

// Dynamic selection lists
fstaffsearch.Lists["x_TempatLahir"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstaffsearch.Lists["x_KelaminID"] = {"LinkField":"x_Kelamin","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kelamin"};
fstaffsearch.Lists["x_AgamaID"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fstaffsearch.Lists["x_ProvinsiID"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenKotaID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fstaffsearch.Lists["x_KabupatenKotaID"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":["x_ProvinsiID"],"ChildFields":["x_KecamatanID"],"FilterFields":["x_ProvinsiID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstaffsearch.Lists["x_KecamatanID"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":["x_KabupatenKotaID"],"ChildFields":["x_DesaID"],"FilterFields":["x_KabupatenKotaID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fstaffsearch.Lists["x_DesaID"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":["x_KecamatanID"],"ChildFields":[],"FilterFields":["x_KecamatanID"],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fstaffsearch.Lists["x_BagianID"] = {"LinkField":"x_BagianID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_bagian"};
fstaffsearch.Lists["x_GolonganID"] = {"LinkField":"x_GolonganID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_golongan"};
fstaffsearch.Lists["x_IkatanID"] = {"LinkField":"x_IkatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_ikatan"};
fstaffsearch.Lists["x_StatusKerjaID"] = {"LinkField":"x_StatusKerjaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statuskerja"};
fstaffsearch.Lists["x_PendidikanTerakhir"] = {"LinkField":"x_Pendidikan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pendidikanortu"};
fstaffsearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fstaffsearch.Lists["x_NA"].Options = <?php echo json_encode($staff->NA->Options()) ?>;

// Form object for search
// Validate function for search

fstaffsearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_LevelID");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($staff->LevelID->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_TanggalLahir");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($staff->TanggalLahir->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_TglBekerja");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($staff->TglBekerja->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$staff_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $staff_search->ShowPageHeader(); ?>
<?php
$staff_search->ShowMessage();
?>
<form name="fstaffsearch" id="fstaffsearch" class="<?php echo $staff_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($staff_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $staff_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="staff">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($staff_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$staff_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<div class="ewMultiPage">
<div class="tabbable" id="staff_search">
	<ul class="nav<?php echo $staff_search->MultiPages->NavStyle() ?>">
		<li<?php echo $staff_search->MultiPages->TabStyle("1") ?>><a href="#tab_staff1" data-toggle="tab"><?php echo $staff->PageCaption(1) ?></a></li>
		<li<?php echo $staff_search->MultiPages->TabStyle("2") ?>><a href="#tab_staff2" data-toggle="tab"><?php echo $staff->PageCaption(2) ?></a></li>
		<li<?php echo $staff_search->MultiPages->TabStyle("3") ?>><a href="#tab_staff3" data-toggle="tab"><?php echo $staff->PageCaption(3) ?></a></li>
		<li<?php echo $staff_search->MultiPages->TabStyle("4") ?>><a href="#tab_staff4" data-toggle="tab"><?php echo $staff->PageCaption(4) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane<?php echo $staff_search->MultiPages->PageStyle("1") ?>" id="tab_staff1">
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_staffsearch1" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($staff->StaffID->Visible) { // StaffID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_StaffID" class="form-group">
		<label for="x_StaffID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_StaffID"><?php echo $staff->StaffID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StaffID" id="z_StaffID" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->StaffID->CellAttributes() ?>>
			<span id="el_staff_StaffID">
<input type="text" data-table="staff" data-field="x_StaffID" data-page="1" name="x_StaffID" id="x_StaffID" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->StaffID->getPlaceHolder()) ?>" value="<?php echo $staff->StaffID->EditValue ?>"<?php echo $staff->StaffID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_StaffID">
		<td><span id="elh_staff_StaffID"><?php echo $staff->StaffID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StaffID" id="z_StaffID" value="LIKE"></span></td>
		<td<?php echo $staff->StaffID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_StaffID">
<input type="text" data-table="staff" data-field="x_StaffID" data-page="1" name="x_StaffID" id="x_StaffID" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->StaffID->getPlaceHolder()) ?>" value="<?php echo $staff->StaffID->EditValue ?>"<?php echo $staff->StaffID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->LevelID->Visible) { // LevelID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_LevelID" class="form-group">
		<label for="x_LevelID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_LevelID"><?php echo $staff->LevelID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_LevelID" id="z_LevelID" value="="></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->LevelID->CellAttributes() ?>>
			<span id="el_staff_LevelID">
<input type="text" data-table="staff" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($staff->LevelID->getPlaceHolder()) ?>" value="<?php echo $staff->LevelID->EditValue ?>"<?php echo $staff->LevelID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_LevelID">
		<td><span id="elh_staff_LevelID"><?php echo $staff->LevelID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_LevelID" id="z_LevelID" value="="></span></td>
		<td<?php echo $staff->LevelID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_LevelID">
<input type="text" data-table="staff" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($staff->LevelID->getPlaceHolder()) ?>" value="<?php echo $staff->LevelID->EditValue ?>"<?php echo $staff->LevelID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->NIPPNS->Visible) { // NIPPNS ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_NIPPNS" class="form-group">
		<label for="x_NIPPNS" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_NIPPNS"><?php echo $staff->NIPPNS->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NIPPNS" id="z_NIPPNS" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->NIPPNS->CellAttributes() ?>>
			<span id="el_staff_NIPPNS">
<input type="text" data-table="staff" data-field="x_NIPPNS" data-page="1" name="x_NIPPNS" id="x_NIPPNS" size="30" maxlength="30" placeholder="<?php echo ew_HtmlEncode($staff->NIPPNS->getPlaceHolder()) ?>" value="<?php echo $staff->NIPPNS->EditValue ?>"<?php echo $staff->NIPPNS->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NIPPNS">
		<td><span id="elh_staff_NIPPNS"><?php echo $staff->NIPPNS->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NIPPNS" id="z_NIPPNS" value="LIKE"></span></td>
		<td<?php echo $staff->NIPPNS->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_NIPPNS">
<input type="text" data-table="staff" data-field="x_NIPPNS" data-page="1" name="x_NIPPNS" id="x_NIPPNS" size="30" maxlength="30" placeholder="<?php echo ew_HtmlEncode($staff->NIPPNS->getPlaceHolder()) ?>" value="<?php echo $staff->NIPPNS->EditValue ?>"<?php echo $staff->NIPPNS->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label for="x_Nama" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_Nama"><?php echo $staff->Nama->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->Nama->CellAttributes() ?>>
			<span id="el_staff_Nama">
<input type="text" data-table="staff" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->Nama->getPlaceHolder()) ?>" value="<?php echo $staff->Nama->EditValue ?>"<?php echo $staff->Nama->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_staff_Nama"><?php echo $staff->Nama->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></span></td>
		<td<?php echo $staff->Nama->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_Nama">
<input type="text" data-table="staff" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->Nama->getPlaceHolder()) ?>" value="<?php echo $staff->Nama->EditValue ?>"<?php echo $staff->Nama->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->Gelar->Visible) { // Gelar ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_Gelar" class="form-group">
		<label for="x_Gelar" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_Gelar"><?php echo $staff->Gelar->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Gelar" id="z_Gelar" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->Gelar->CellAttributes() ?>>
			<span id="el_staff_Gelar">
<input type="text" data-table="staff" data-field="x_Gelar" data-page="1" name="x_Gelar" id="x_Gelar" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($staff->Gelar->getPlaceHolder()) ?>" value="<?php echo $staff->Gelar->EditValue ?>"<?php echo $staff->Gelar->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Gelar">
		<td><span id="elh_staff_Gelar"><?php echo $staff->Gelar->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Gelar" id="z_Gelar" value="LIKE"></span></td>
		<td<?php echo $staff->Gelar->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_Gelar">
<input type="text" data-table="staff" data-field="x_Gelar" data-page="1" name="x_Gelar" id="x_Gelar" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($staff->Gelar->getPlaceHolder()) ?>" value="<?php echo $staff->Gelar->EditValue ?>"<?php echo $staff->Gelar->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $staff_search->MultiPages->PageStyle("2") ?>" id="tab_staff2">
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_staffsearch2" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($staff->KTP->Visible) { // KTP ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_KTP" class="form-group">
		<label for="x_KTP" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_KTP"><?php echo $staff->KTP->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KTP" id="z_KTP" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->KTP->CellAttributes() ?>>
			<span id="el_staff_KTP">
<input type="text" data-table="staff" data-field="x_KTP" data-page="2" name="x_KTP" id="x_KTP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->KTP->getPlaceHolder()) ?>" value="<?php echo $staff->KTP->EditValue ?>"<?php echo $staff->KTP->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KTP">
		<td><span id="elh_staff_KTP"><?php echo $staff->KTP->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KTP" id="z_KTP" value="LIKE"></span></td>
		<td<?php echo $staff->KTP->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_KTP">
<input type="text" data-table="staff" data-field="x_KTP" data-page="2" name="x_KTP" id="x_KTP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->KTP->getPlaceHolder()) ?>" value="<?php echo $staff->KTP->EditValue ?>"<?php echo $staff->KTP->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->TempatLahir->Visible) { // TempatLahir ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_TempatLahir" class="form-group">
		<label class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_TempatLahir"><?php echo $staff->TempatLahir->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TempatLahir" id="z_TempatLahir" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->TempatLahir->CellAttributes() ?>>
			<span id="el_staff_TempatLahir">
<?php
$wrkonchange = trim(" " . @$staff->TempatLahir->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$staff->TempatLahir->EditAttrs["onchange"] = "";
?>
<span id="as_x_TempatLahir" style="white-space: nowrap; z-index: 8920">
	<input type="text" name="sv_x_TempatLahir" id="sv_x_TempatLahir" value="<?php echo $staff->TempatLahir->EditValue ?>" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->TempatLahir->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($staff->TempatLahir->getPlaceHolder()) ?>"<?php echo $staff->TempatLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="staff" data-field="x_TempatLahir" data-page="2" data-value-separator="<?php echo $staff->TempatLahir->DisplayValueSeparatorAttribute() ?>" name="x_TempatLahir" id="x_TempatLahir" value="<?php echo ew_HtmlEncode($staff->TempatLahir->AdvancedSearch->SearchValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TempatLahir" id="q_x_TempatLahir" value="<?php echo $staff->TempatLahir->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fstaffsearch.CreateAutoSuggest({"id":"x_TempatLahir","forceSelect":false});
</script>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TempatLahir">
		<td><span id="elh_staff_TempatLahir"><?php echo $staff->TempatLahir->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TempatLahir" id="z_TempatLahir" value="LIKE"></span></td>
		<td<?php echo $staff->TempatLahir->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_TempatLahir">
<?php
$wrkonchange = trim(" " . @$staff->TempatLahir->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$staff->TempatLahir->EditAttrs["onchange"] = "";
?>
<span id="as_x_TempatLahir" style="white-space: nowrap; z-index: 8920">
	<input type="text" name="sv_x_TempatLahir" id="sv_x_TempatLahir" value="<?php echo $staff->TempatLahir->EditValue ?>" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->TempatLahir->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($staff->TempatLahir->getPlaceHolder()) ?>"<?php echo $staff->TempatLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="staff" data-field="x_TempatLahir" data-page="2" data-value-separator="<?php echo $staff->TempatLahir->DisplayValueSeparatorAttribute() ?>" name="x_TempatLahir" id="x_TempatLahir" value="<?php echo ew_HtmlEncode($staff->TempatLahir->AdvancedSearch->SearchValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TempatLahir" id="q_x_TempatLahir" value="<?php echo $staff->TempatLahir->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fstaffsearch.CreateAutoSuggest({"id":"x_TempatLahir","forceSelect":false});
</script>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->TanggalLahir->Visible) { // TanggalLahir ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_TanggalLahir" class="form-group">
		<label for="x_TanggalLahir" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_TanggalLahir"><?php echo $staff->TanggalLahir->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanggalLahir" id="z_TanggalLahir" value="="></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->TanggalLahir->CellAttributes() ?>>
			<span id="el_staff_TanggalLahir">
<input type="text" data-table="staff" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($staff->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $staff->TanggalLahir->EditValue ?>"<?php echo $staff->TanggalLahir->EditAttributes() ?>>
<?php if (!$staff->TanggalLahir->ReadOnly && !$staff->TanggalLahir->Disabled && !isset($staff->TanggalLahir->EditAttrs["readonly"]) && !isset($staff->TanggalLahir->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fstaffsearch", "x_TanggalLahir", 0);
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanggalLahir">
		<td><span id="elh_staff_TanggalLahir"><?php echo $staff->TanggalLahir->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanggalLahir" id="z_TanggalLahir" value="="></span></td>
		<td<?php echo $staff->TanggalLahir->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_TanggalLahir">
<input type="text" data-table="staff" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($staff->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $staff->TanggalLahir->EditValue ?>"<?php echo $staff->TanggalLahir->EditAttributes() ?>>
<?php if (!$staff->TanggalLahir->ReadOnly && !$staff->TanggalLahir->Disabled && !isset($staff->TanggalLahir->EditAttrs["readonly"]) && !isset($staff->TanggalLahir->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fstaffsearch", "x_TanggalLahir", 0);
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->KelaminID->Visible) { // KelaminID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_KelaminID" class="form-group">
		<label class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_KelaminID"><?php echo $staff->KelaminID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KelaminID" id="z_KelaminID" value="="></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->KelaminID->CellAttributes() ?>>
			<span id="el_staff_KelaminID">
<div id="tp_x_KelaminID" class="ewTemplate"><input type="radio" data-table="staff" data-field="x_KelaminID" data-page="2" data-value-separator="<?php echo $staff->KelaminID->DisplayValueSeparatorAttribute() ?>" name="x_KelaminID" id="x_KelaminID" value="{value}"<?php echo $staff->KelaminID->EditAttributes() ?>></div>
<div id="dsl_x_KelaminID" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $staff->KelaminID->RadioButtonListHtml(FALSE, "x_KelaminID", 2) ?>
</div></div>
<input type="hidden" name="s_x_KelaminID" id="s_x_KelaminID" value="<?php echo $staff->KelaminID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KelaminID">
		<td><span id="elh_staff_KelaminID"><?php echo $staff->KelaminID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KelaminID" id="z_KelaminID" value="="></span></td>
		<td<?php echo $staff->KelaminID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_KelaminID">
<div id="tp_x_KelaminID" class="ewTemplate"><input type="radio" data-table="staff" data-field="x_KelaminID" data-page="2" data-value-separator="<?php echo $staff->KelaminID->DisplayValueSeparatorAttribute() ?>" name="x_KelaminID" id="x_KelaminID" value="{value}"<?php echo $staff->KelaminID->EditAttributes() ?>></div>
<div id="dsl_x_KelaminID" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $staff->KelaminID->RadioButtonListHtml(FALSE, "x_KelaminID", 2) ?>
</div></div>
<input type="hidden" name="s_x_KelaminID" id="s_x_KelaminID" value="<?php echo $staff->KelaminID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->AgamaID->Visible) { // AgamaID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_AgamaID" class="form-group">
		<label for="x_AgamaID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_AgamaID"><?php echo $staff->AgamaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AgamaID" id="z_AgamaID" value="="></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->AgamaID->CellAttributes() ?>>
			<span id="el_staff_AgamaID">
<select data-table="staff" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $staff->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $staff->AgamaID->EditAttributes() ?>>
<?php echo $staff->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $staff->AgamaID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_AgamaID">
		<td><span id="elh_staff_AgamaID"><?php echo $staff->AgamaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AgamaID" id="z_AgamaID" value="="></span></td>
		<td<?php echo $staff->AgamaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_AgamaID">
<select data-table="staff" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $staff->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $staff->AgamaID->EditAttributes() ?>>
<?php echo $staff->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $staff->AgamaID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->Telephone->Visible) { // Telephone ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_Telephone" class="form-group">
		<label for="x_Telephone" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_Telephone"><?php echo $staff->Telephone->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Telephone" id="z_Telephone" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->Telephone->CellAttributes() ?>>
			<span id="el_staff_Telephone">
<input type="text" data-table="staff" data-field="x_Telephone" data-page="2" name="x_Telephone" id="x_Telephone" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->Telephone->getPlaceHolder()) ?>" value="<?php echo $staff->Telephone->EditValue ?>"<?php echo $staff->Telephone->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Telephone">
		<td><span id="elh_staff_Telephone"><?php echo $staff->Telephone->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Telephone" id="z_Telephone" value="LIKE"></span></td>
		<td<?php echo $staff->Telephone->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_Telephone">
<input type="text" data-table="staff" data-field="x_Telephone" data-page="2" name="x_Telephone" id="x_Telephone" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->Telephone->getPlaceHolder()) ?>" value="<?php echo $staff->Telephone->EditValue ?>"<?php echo $staff->Telephone->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->_Email->Visible) { // Email ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r__Email" class="form-group">
		<label for="x__Email" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff__Email"><?php echo $staff->_Email->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z__Email" id="z__Email" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->_Email->CellAttributes() ?>>
			<span id="el_staff__Email">
<input type="text" data-table="staff" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($staff->_Email->getPlaceHolder()) ?>" value="<?php echo $staff->_Email->EditValue ?>"<?php echo $staff->_Email->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r__Email">
		<td><span id="elh_staff__Email"><?php echo $staff->_Email->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z__Email" id="z__Email" value="LIKE"></span></td>
		<td<?php echo $staff->_Email->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff__Email">
<input type="text" data-table="staff" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($staff->_Email->getPlaceHolder()) ?>" value="<?php echo $staff->_Email->EditValue ?>"<?php echo $staff->_Email->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->Alamat->Visible) { // Alamat ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_Alamat" class="form-group">
		<label for="x_Alamat" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_Alamat"><?php echo $staff->Alamat->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Alamat" id="z_Alamat" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->Alamat->CellAttributes() ?>>
			<span id="el_staff_Alamat">
<input type="text" data-table="staff" data-field="x_Alamat" data-page="2" name="x_Alamat" id="x_Alamat" size="35" placeholder="<?php echo ew_HtmlEncode($staff->Alamat->getPlaceHolder()) ?>" value="<?php echo $staff->Alamat->EditValue ?>"<?php echo $staff->Alamat->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Alamat">
		<td><span id="elh_staff_Alamat"><?php echo $staff->Alamat->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Alamat" id="z_Alamat" value="LIKE"></span></td>
		<td<?php echo $staff->Alamat->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_Alamat">
<input type="text" data-table="staff" data-field="x_Alamat" data-page="2" name="x_Alamat" id="x_Alamat" size="35" placeholder="<?php echo ew_HtmlEncode($staff->Alamat->getPlaceHolder()) ?>" value="<?php echo $staff->Alamat->EditValue ?>"<?php echo $staff->Alamat->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->KodePos->Visible) { // KodePos ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_KodePos" class="form-group">
		<label for="x_KodePos" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_KodePos"><?php echo $staff->KodePos->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodePos" id="z_KodePos" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->KodePos->CellAttributes() ?>>
			<span id="el_staff_KodePos">
<input type="text" data-table="staff" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->KodePos->getPlaceHolder()) ?>" value="<?php echo $staff->KodePos->EditValue ?>"<?php echo $staff->KodePos->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodePos">
		<td><span id="elh_staff_KodePos"><?php echo $staff->KodePos->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodePos" id="z_KodePos" value="LIKE"></span></td>
		<td<?php echo $staff->KodePos->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_KodePos">
<input type="text" data-table="staff" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($staff->KodePos->getPlaceHolder()) ?>" value="<?php echo $staff->KodePos->EditValue ?>"<?php echo $staff->KodePos->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->ProvinsiID->Visible) { // ProvinsiID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_ProvinsiID" class="form-group">
		<label for="x_ProvinsiID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_ProvinsiID"><?php echo $staff->ProvinsiID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProvinsiID" id="z_ProvinsiID" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->ProvinsiID->CellAttributes() ?>>
			<span id="el_staff_ProvinsiID">
<?php $staff->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $staff->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $staff->ProvinsiID->EditAttributes() ?>>
<?php echo $staff->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $staff->ProvinsiID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiID">
		<td><span id="elh_staff_ProvinsiID"><?php echo $staff->ProvinsiID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProvinsiID" id="z_ProvinsiID" value="LIKE"></span></td>
		<td<?php echo $staff->ProvinsiID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_ProvinsiID">
<?php $staff->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $staff->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $staff->ProvinsiID->EditAttributes() ?>>
<?php echo $staff->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $staff->ProvinsiID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->KabupatenKotaID->Visible) { // KabupatenKotaID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_KabupatenKotaID" class="form-group">
		<label for="x_KabupatenKotaID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_KabupatenKotaID"><?php echo $staff->KabupatenKotaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KabupatenKotaID" id="z_KabupatenKotaID" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->KabupatenKotaID->CellAttributes() ?>>
			<span id="el_staff_KabupatenKotaID">
<?php $staff->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $staff->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $staff->KabupatenKotaID->EditAttributes() ?>>
<?php echo $staff->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $staff->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenKotaID">
		<td><span id="elh_staff_KabupatenKotaID"><?php echo $staff->KabupatenKotaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KabupatenKotaID" id="z_KabupatenKotaID" value="LIKE"></span></td>
		<td<?php echo $staff->KabupatenKotaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_KabupatenKotaID">
<?php $staff->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $staff->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $staff->KabupatenKotaID->EditAttributes() ?>>
<?php echo $staff->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $staff->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->KecamatanID->Visible) { // KecamatanID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_KecamatanID" class="form-group">
		<label for="x_KecamatanID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_KecamatanID"><?php echo $staff->KecamatanID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KecamatanID" id="z_KecamatanID" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->KecamatanID->CellAttributes() ?>>
			<span id="el_staff_KecamatanID">
<?php $staff->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $staff->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $staff->KecamatanID->EditAttributes() ?>>
<?php echo $staff->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $staff->KecamatanID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanID">
		<td><span id="elh_staff_KecamatanID"><?php echo $staff->KecamatanID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KecamatanID" id="z_KecamatanID" value="LIKE"></span></td>
		<td<?php echo $staff->KecamatanID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_KecamatanID">
<?php $staff->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$staff->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="staff" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $staff->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $staff->KecamatanID->EditAttributes() ?>>
<?php echo $staff->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $staff->KecamatanID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->DesaID->Visible) { // DesaID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_DesaID" class="form-group">
		<label for="x_DesaID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_DesaID"><?php echo $staff->DesaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_DesaID" id="z_DesaID" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->DesaID->CellAttributes() ?>>
			<span id="el_staff_DesaID">
<select data-table="staff" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $staff->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $staff->DesaID->EditAttributes() ?>>
<?php echo $staff->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $staff->DesaID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_DesaID">
		<td><span id="elh_staff_DesaID"><?php echo $staff->DesaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_DesaID" id="z_DesaID" value="LIKE"></span></td>
		<td<?php echo $staff->DesaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_DesaID">
<select data-table="staff" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $staff->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $staff->DesaID->EditAttributes() ?>>
<?php echo $staff->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $staff->DesaID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $staff_search->MultiPages->PageStyle("3") ?>" id="tab_staff3">
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_staffsearch3" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($staff->KampusID->Visible) { // KampusID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_KampusID" class="form-group">
		<label for="x_KampusID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_KampusID"><?php echo $staff->KampusID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KampusID" id="z_KampusID" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->KampusID->CellAttributes() ?>>
			<span id="el_staff_KampusID">
<input type="text" data-table="staff" data-field="x_KampusID" data-page="3" name="x_KampusID" id="x_KampusID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($staff->KampusID->getPlaceHolder()) ?>" value="<?php echo $staff->KampusID->EditValue ?>"<?php echo $staff->KampusID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KampusID">
		<td><span id="elh_staff_KampusID"><?php echo $staff->KampusID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KampusID" id="z_KampusID" value="LIKE"></span></td>
		<td<?php echo $staff->KampusID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_KampusID">
<input type="text" data-table="staff" data-field="x_KampusID" data-page="3" name="x_KampusID" id="x_KampusID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($staff->KampusID->getPlaceHolder()) ?>" value="<?php echo $staff->KampusID->EditValue ?>"<?php echo $staff->KampusID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->BagianID->Visible) { // BagianID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_BagianID" class="form-group">
		<label for="x_BagianID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_BagianID"><?php echo $staff->BagianID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_BagianID" id="z_BagianID" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->BagianID->CellAttributes() ?>>
			<span id="el_staff_BagianID">
<select data-table="staff" data-field="x_BagianID" data-page="3" data-value-separator="<?php echo $staff->BagianID->DisplayValueSeparatorAttribute() ?>" id="x_BagianID" name="x_BagianID"<?php echo $staff->BagianID->EditAttributes() ?>>
<?php echo $staff->BagianID->SelectOptionListHtml("x_BagianID") ?>
</select>
<input type="hidden" name="s_x_BagianID" id="s_x_BagianID" value="<?php echo $staff->BagianID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_BagianID">
		<td><span id="elh_staff_BagianID"><?php echo $staff->BagianID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_BagianID" id="z_BagianID" value="LIKE"></span></td>
		<td<?php echo $staff->BagianID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_BagianID">
<select data-table="staff" data-field="x_BagianID" data-page="3" data-value-separator="<?php echo $staff->BagianID->DisplayValueSeparatorAttribute() ?>" id="x_BagianID" name="x_BagianID"<?php echo $staff->BagianID->EditAttributes() ?>>
<?php echo $staff->BagianID->SelectOptionListHtml("x_BagianID") ?>
</select>
<input type="hidden" name="s_x_BagianID" id="s_x_BagianID" value="<?php echo $staff->BagianID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->GolonganID->Visible) { // GolonganID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_GolonganID" class="form-group">
		<label for="x_GolonganID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_GolonganID"><?php echo $staff->GolonganID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_GolonganID" id="z_GolonganID" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->GolonganID->CellAttributes() ?>>
			<span id="el_staff_GolonganID">
<select data-table="staff" data-field="x_GolonganID" data-page="3" data-value-separator="<?php echo $staff->GolonganID->DisplayValueSeparatorAttribute() ?>" id="x_GolonganID" name="x_GolonganID"<?php echo $staff->GolonganID->EditAttributes() ?>>
<?php echo $staff->GolonganID->SelectOptionListHtml("x_GolonganID") ?>
</select>
<input type="hidden" name="s_x_GolonganID" id="s_x_GolonganID" value="<?php echo $staff->GolonganID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_GolonganID">
		<td><span id="elh_staff_GolonganID"><?php echo $staff->GolonganID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_GolonganID" id="z_GolonganID" value="LIKE"></span></td>
		<td<?php echo $staff->GolonganID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_GolonganID">
<select data-table="staff" data-field="x_GolonganID" data-page="3" data-value-separator="<?php echo $staff->GolonganID->DisplayValueSeparatorAttribute() ?>" id="x_GolonganID" name="x_GolonganID"<?php echo $staff->GolonganID->EditAttributes() ?>>
<?php echo $staff->GolonganID->SelectOptionListHtml("x_GolonganID") ?>
</select>
<input type="hidden" name="s_x_GolonganID" id="s_x_GolonganID" value="<?php echo $staff->GolonganID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->IkatanID->Visible) { // IkatanID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_IkatanID" class="form-group">
		<label for="x_IkatanID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_IkatanID"><?php echo $staff->IkatanID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_IkatanID" id="z_IkatanID" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->IkatanID->CellAttributes() ?>>
			<span id="el_staff_IkatanID">
<select data-table="staff" data-field="x_IkatanID" data-page="3" data-value-separator="<?php echo $staff->IkatanID->DisplayValueSeparatorAttribute() ?>" id="x_IkatanID" name="x_IkatanID"<?php echo $staff->IkatanID->EditAttributes() ?>>
<?php echo $staff->IkatanID->SelectOptionListHtml("x_IkatanID") ?>
</select>
<input type="hidden" name="s_x_IkatanID" id="s_x_IkatanID" value="<?php echo $staff->IkatanID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_IkatanID">
		<td><span id="elh_staff_IkatanID"><?php echo $staff->IkatanID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_IkatanID" id="z_IkatanID" value="LIKE"></span></td>
		<td<?php echo $staff->IkatanID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_IkatanID">
<select data-table="staff" data-field="x_IkatanID" data-page="3" data-value-separator="<?php echo $staff->IkatanID->DisplayValueSeparatorAttribute() ?>" id="x_IkatanID" name="x_IkatanID"<?php echo $staff->IkatanID->EditAttributes() ?>>
<?php echo $staff->IkatanID->SelectOptionListHtml("x_IkatanID") ?>
</select>
<input type="hidden" name="s_x_IkatanID" id="s_x_IkatanID" value="<?php echo $staff->IkatanID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->StatusKerjaID->Visible) { // StatusKerjaID ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_StatusKerjaID" class="form-group">
		<label for="x_StatusKerjaID" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_StatusKerjaID"><?php echo $staff->StatusKerjaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusKerjaID" id="z_StatusKerjaID" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->StatusKerjaID->CellAttributes() ?>>
			<span id="el_staff_StatusKerjaID">
<select data-table="staff" data-field="x_StatusKerjaID" data-page="3" data-value-separator="<?php echo $staff->StatusKerjaID->DisplayValueSeparatorAttribute() ?>" id="x_StatusKerjaID" name="x_StatusKerjaID"<?php echo $staff->StatusKerjaID->EditAttributes() ?>>
<?php echo $staff->StatusKerjaID->SelectOptionListHtml("x_StatusKerjaID") ?>
</select>
<input type="hidden" name="s_x_StatusKerjaID" id="s_x_StatusKerjaID" value="<?php echo $staff->StatusKerjaID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusKerjaID">
		<td><span id="elh_staff_StatusKerjaID"><?php echo $staff->StatusKerjaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusKerjaID" id="z_StatusKerjaID" value="LIKE"></span></td>
		<td<?php echo $staff->StatusKerjaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_StatusKerjaID">
<select data-table="staff" data-field="x_StatusKerjaID" data-page="3" data-value-separator="<?php echo $staff->StatusKerjaID->DisplayValueSeparatorAttribute() ?>" id="x_StatusKerjaID" name="x_StatusKerjaID"<?php echo $staff->StatusKerjaID->EditAttributes() ?>>
<?php echo $staff->StatusKerjaID->SelectOptionListHtml("x_StatusKerjaID") ?>
</select>
<input type="hidden" name="s_x_StatusKerjaID" id="s_x_StatusKerjaID" value="<?php echo $staff->StatusKerjaID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->TglBekerja->Visible) { // TglBekerja ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_TglBekerja" class="form-group">
		<label for="x_TglBekerja" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_TglBekerja"><?php echo $staff->TglBekerja->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TglBekerja" id="z_TglBekerja" value="="></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->TglBekerja->CellAttributes() ?>>
			<span id="el_staff_TglBekerja">
<input type="text" data-table="staff" data-field="x_TglBekerja" data-page="3" name="x_TglBekerja" id="x_TglBekerja" placeholder="<?php echo ew_HtmlEncode($staff->TglBekerja->getPlaceHolder()) ?>" value="<?php echo $staff->TglBekerja->EditValue ?>"<?php echo $staff->TglBekerja->EditAttributes() ?>>
<?php if (!$staff->TglBekerja->ReadOnly && !$staff->TglBekerja->Disabled && !isset($staff->TglBekerja->EditAttrs["readonly"]) && !isset($staff->TglBekerja->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fstaffsearch", "x_TglBekerja", 0);
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TglBekerja">
		<td><span id="elh_staff_TglBekerja"><?php echo $staff->TglBekerja->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TglBekerja" id="z_TglBekerja" value="="></span></td>
		<td<?php echo $staff->TglBekerja->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_TglBekerja">
<input type="text" data-table="staff" data-field="x_TglBekerja" data-page="3" name="x_TglBekerja" id="x_TglBekerja" placeholder="<?php echo ew_HtmlEncode($staff->TglBekerja->getPlaceHolder()) ?>" value="<?php echo $staff->TglBekerja->EditValue ?>"<?php echo $staff->TglBekerja->EditAttributes() ?>>
<?php if (!$staff->TglBekerja->ReadOnly && !$staff->TglBekerja->Disabled && !isset($staff->TglBekerja->EditAttrs["readonly"]) && !isset($staff->TglBekerja->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fstaffsearch", "x_TglBekerja", 0);
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($staff->PendidikanTerakhir->Visible) { // PendidikanTerakhir ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_PendidikanTerakhir" class="form-group">
		<label for="x_PendidikanTerakhir" class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_PendidikanTerakhir"><?php echo $staff->PendidikanTerakhir->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_PendidikanTerakhir" id="z_PendidikanTerakhir" value="LIKE"></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->PendidikanTerakhir->CellAttributes() ?>>
			<span id="el_staff_PendidikanTerakhir">
<select data-table="staff" data-field="x_PendidikanTerakhir" data-page="3" data-value-separator="<?php echo $staff->PendidikanTerakhir->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanTerakhir" name="x_PendidikanTerakhir"<?php echo $staff->PendidikanTerakhir->EditAttributes() ?>>
<?php echo $staff->PendidikanTerakhir->SelectOptionListHtml("x_PendidikanTerakhir") ?>
</select>
<input type="hidden" name="s_x_PendidikanTerakhir" id="s_x_PendidikanTerakhir" value="<?php echo $staff->PendidikanTerakhir->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_PendidikanTerakhir">
		<td><span id="elh_staff_PendidikanTerakhir"><?php echo $staff->PendidikanTerakhir->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_PendidikanTerakhir" id="z_PendidikanTerakhir" value="LIKE"></span></td>
		<td<?php echo $staff->PendidikanTerakhir->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_PendidikanTerakhir">
<select data-table="staff" data-field="x_PendidikanTerakhir" data-page="3" data-value-separator="<?php echo $staff->PendidikanTerakhir->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanTerakhir" name="x_PendidikanTerakhir"<?php echo $staff->PendidikanTerakhir->EditAttributes() ?>>
<?php echo $staff->PendidikanTerakhir->SelectOptionListHtml("x_PendidikanTerakhir") ?>
</select>
<input type="hidden" name="s_x_PendidikanTerakhir" id="s_x_PendidikanTerakhir" value="<?php echo $staff->PendidikanTerakhir->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $staff_search->MultiPages->PageStyle("4") ?>" id="tab_staff4">
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_staffsearch4" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($staff->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $staff_search->SearchLabelClass ?>"><span id="elh_staff_NA"><?php echo $staff->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $staff_search->SearchRightColumnClass ?>"><div<?php echo $staff->NA->CellAttributes() ?>>
			<span id="el_staff_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="staff" data-field="x_NA" data-page="4" data-value-separator="<?php echo $staff->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $staff->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $staff->NA->RadioButtonListHtml(FALSE, "x_NA", 4) ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_staff_NA"><?php echo $staff->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $staff->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_staff_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="staff" data-field="x_NA" data-page="4" data-value-separator="<?php echo $staff->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $staff->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $staff->NA->RadioButtonListHtml(FALSE, "x_NA", 4) ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $staff_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
	</div>
</div>
</div>
<?php if (!$staff_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$staff_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fstaffsearch.Init();
</script>
<?php
$staff_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$staff_search->Page_Terminate();
?>
