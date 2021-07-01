<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "teacherinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$teacher_search = NULL; // Initialize page object first

class cteacher_search extends cteacher {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'teacher';

	// Page object name
	var $PageObjName = 'teacher_search';

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

		// Table object (teacher)
		if (!isset($GLOBALS["teacher"]) || get_class($GLOBALS["teacher"]) == "cteacher") {
			$GLOBALS["teacher"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["teacher"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'teacher', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("teacherlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->TeacherID->SetVisibility();
		$this->NIPPNS->SetVisibility();
		$this->Nama->SetVisibility();
		$this->Gelar->SetVisibility();
		$this->LevelID->SetVisibility();
		$this->KTP->SetVisibility();
		$this->TempatLahir->SetVisibility();
		$this->TanggalLahir->SetVisibility();
		$this->AgamaID->SetVisibility();
		$this->KelaminID->SetVisibility();
		$this->Telephone->SetVisibility();
		$this->_Email->SetVisibility();
		$this->Alamat->SetVisibility();
		$this->KodePos->SetVisibility();
		$this->ProvinsiID->SetVisibility();
		$this->KabupatenKotaID->SetVisibility();
		$this->KecamatanID->SetVisibility();
		$this->DesaID->SetVisibility();
		$this->InstitusiInduk->SetVisibility();
		$this->IkatanID->SetVisibility();
		$this->GolonganID->SetVisibility();
		$this->StatusKerjaID->SetVisibility();
		$this->TglBekerja->SetVisibility();
		$this->Homebase->SetVisibility();
		$this->ProdiID->SetVisibility();
		$this->Keilmuan->SetVisibility();
		$this->LulusanPT->SetVisibility();
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
		global $EW_EXPORT, $teacher;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($teacher);
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
						$sSrchStr = "teacherlist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->TeacherID); // TeacherID
		$this->BuildSearchUrl($sSrchUrl, $this->NIPPNS); // NIPPNS
		$this->BuildSearchUrl($sSrchUrl, $this->Nama); // Nama
		$this->BuildSearchUrl($sSrchUrl, $this->Gelar); // Gelar
		$this->BuildSearchUrl($sSrchUrl, $this->LevelID); // LevelID
		$this->BuildSearchUrl($sSrchUrl, $this->KTP); // KTP
		$this->BuildSearchUrl($sSrchUrl, $this->TempatLahir); // TempatLahir
		$this->BuildSearchUrl($sSrchUrl, $this->TanggalLahir); // TanggalLahir
		$this->BuildSearchUrl($sSrchUrl, $this->AgamaID); // AgamaID
		$this->BuildSearchUrl($sSrchUrl, $this->KelaminID); // KelaminID
		$this->BuildSearchUrl($sSrchUrl, $this->Telephone); // Telephone
		$this->BuildSearchUrl($sSrchUrl, $this->_Email); // Email
		$this->BuildSearchUrl($sSrchUrl, $this->Alamat); // Alamat
		$this->BuildSearchUrl($sSrchUrl, $this->KodePos); // KodePos
		$this->BuildSearchUrl($sSrchUrl, $this->ProvinsiID); // ProvinsiID
		$this->BuildSearchUrl($sSrchUrl, $this->KabupatenKotaID); // KabupatenKotaID
		$this->BuildSearchUrl($sSrchUrl, $this->KecamatanID); // KecamatanID
		$this->BuildSearchUrl($sSrchUrl, $this->DesaID); // DesaID
		$this->BuildSearchUrl($sSrchUrl, $this->InstitusiInduk); // InstitusiInduk
		$this->BuildSearchUrl($sSrchUrl, $this->IkatanID); // IkatanID
		$this->BuildSearchUrl($sSrchUrl, $this->GolonganID); // GolonganID
		$this->BuildSearchUrl($sSrchUrl, $this->StatusKerjaID); // StatusKerjaID
		$this->BuildSearchUrl($sSrchUrl, $this->TglBekerja); // TglBekerja
		$this->BuildSearchUrl($sSrchUrl, $this->Homebase); // Homebase
		$this->BuildSearchUrl($sSrchUrl, $this->ProdiID); // ProdiID
		$this->BuildSearchUrl($sSrchUrl, $this->Keilmuan); // Keilmuan
		$this->BuildSearchUrl($sSrchUrl, $this->LulusanPT); // LulusanPT
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
		// TeacherID

		$this->TeacherID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TeacherID"));
		$this->TeacherID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TeacherID");

		// NIPPNS
		$this->NIPPNS->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NIPPNS"));
		$this->NIPPNS->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NIPPNS");

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Nama"));
		$this->Nama->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Nama");

		// Gelar
		$this->Gelar->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Gelar"));
		$this->Gelar->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Gelar");

		// LevelID
		$this->LevelID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_LevelID"));
		$this->LevelID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_LevelID");

		// KTP
		$this->KTP->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KTP"));
		$this->KTP->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KTP");

		// TempatLahir
		$this->TempatLahir->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TempatLahir"));
		$this->TempatLahir->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TempatLahir");

		// TanggalLahir
		$this->TanggalLahir->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TanggalLahir"));
		$this->TanggalLahir->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TanggalLahir");

		// AgamaID
		$this->AgamaID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_AgamaID"));
		$this->AgamaID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_AgamaID");

		// KelaminID
		$this->KelaminID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KelaminID"));
		$this->KelaminID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KelaminID");

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

		// InstitusiInduk
		$this->InstitusiInduk->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_InstitusiInduk"));
		$this->InstitusiInduk->AdvancedSearch->SearchOperator = $objForm->GetValue("z_InstitusiInduk");

		// IkatanID
		$this->IkatanID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_IkatanID"));
		$this->IkatanID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_IkatanID");

		// GolonganID
		$this->GolonganID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_GolonganID"));
		$this->GolonganID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_GolonganID");

		// StatusKerjaID
		$this->StatusKerjaID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StatusKerjaID"));
		$this->StatusKerjaID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StatusKerjaID");

		// TglBekerja
		$this->TglBekerja->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TglBekerja"));
		$this->TglBekerja->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TglBekerja");

		// Homebase
		$this->Homebase->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Homebase"));
		$this->Homebase->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Homebase");

		// ProdiID
		$this->ProdiID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ProdiID"));
		$this->ProdiID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ProdiID");
		if (is_array($this->ProdiID->AdvancedSearch->SearchValue)) $this->ProdiID->AdvancedSearch->SearchValue = implode(",", $this->ProdiID->AdvancedSearch->SearchValue);
		if (is_array($this->ProdiID->AdvancedSearch->SearchValue2)) $this->ProdiID->AdvancedSearch->SearchValue2 = implode(",", $this->ProdiID->AdvancedSearch->SearchValue2);

		// Keilmuan
		$this->Keilmuan->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Keilmuan"));
		$this->Keilmuan->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Keilmuan");

		// LulusanPT
		$this->LulusanPT->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_LulusanPT"));
		$this->LulusanPT->AdvancedSearch->SearchOperator = $objForm->GetValue("z_LulusanPT");

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
		// TeacherID
		// NIPPNS
		// Nama
		// Gelar
		// LevelID
		// Password
		// AliasCode
		// KTP
		// TempatLahir
		// TanggalLahir
		// AgamaID
		// KelaminID
		// Telephone
		// Handphone
		// Email
		// Alamat
		// KodePos
		// ProvinsiID
		// KabupatenKotaID
		// KecamatanID
		// DesaID
		// InstitusiInduk
		// IkatanID
		// GolonganID
		// StatusKerjaID
		// TglBekerja
		// Homebase
		// ProdiID
		// Keilmuan
		// LulusanPT
		// NamaBank
		// NamaAkun
		// NomerAkun
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// TeacherID
		$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
		$this->TeacherID->CssStyle = "font-weight: bold;";
		$this->TeacherID->ViewCustomAttributes = "";

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

		// LevelID
		$this->LevelID->ViewValue = $this->LevelID->CurrentValue;
		$this->LevelID->ViewCustomAttributes = "";

		// AliasCode
		$this->AliasCode->ViewValue = $this->AliasCode->CurrentValue;
		$this->AliasCode->ViewCustomAttributes = "";

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

		// Telephone
		$this->Telephone->ViewValue = $this->Telephone->CurrentValue;
		$this->Telephone->ViewCustomAttributes = "";

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

		// InstitusiInduk
		if (strval($this->InstitusiInduk->CurrentValue) <> "") {
			$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->InstitusiInduk->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
		$sWhereWrk = "";
		$this->InstitusiInduk->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->InstitusiInduk, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->InstitusiInduk->ViewValue = $this->InstitusiInduk->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->InstitusiInduk->ViewValue = $this->InstitusiInduk->CurrentValue;
			}
		} else {
			$this->InstitusiInduk->ViewValue = NULL;
		}
		$this->InstitusiInduk->ViewCustomAttributes = "";

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

		// Homebase
		if (strval($this->Homebase->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->Homebase->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->Homebase->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Homebase, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Homebase->ViewValue = $this->Homebase->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Homebase->ViewValue = $this->Homebase->CurrentValue;
			}
		} else {
			$this->Homebase->ViewValue = NULL;
		}
		$this->Homebase->ViewCustomAttributes = "";

		// ProdiID
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$arwrk = explode(",", $this->ProdiID->CurrentValue);
			$sFilterWrk = "";
			foreach ($arwrk as $wrk) {
				if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
				$sFilterWrk .= "`ProdiID`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING, "");
			}
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->ProdiID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->ProdiID->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->ProdiID->ViewValue .= $this->ProdiID->DisplayValue($arwrk);
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->ProdiID->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
				$rswrk->Close();
			} else {
				$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
			}
		} else {
			$this->ProdiID->ViewValue = NULL;
		}
		$this->ProdiID->ViewCustomAttributes = "";

		// Keilmuan
		$this->Keilmuan->ViewValue = $this->Keilmuan->CurrentValue;
		$this->Keilmuan->ViewCustomAttributes = "";

		// LulusanPT
		$this->LulusanPT->ViewValue = $this->LulusanPT->CurrentValue;
		$this->LulusanPT->ViewCustomAttributes = "";

		// NamaBank
		$this->NamaBank->ViewValue = $this->NamaBank->CurrentValue;
		$this->NamaBank->ViewCustomAttributes = "";

		// NamaAkun
		$this->NamaAkun->ViewValue = $this->NamaAkun->CurrentValue;
		$this->NamaAkun->ViewCustomAttributes = "";

		// NomerAkun
		$this->NomerAkun->ViewValue = $this->NomerAkun->CurrentValue;
		$this->NomerAkun->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Ya";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "Tidak";
		}
		$this->NA->ViewCustomAttributes = "";

			// TeacherID
			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";
			$this->TeacherID->TooltipValue = "";

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

			// LevelID
			$this->LevelID->LinkCustomAttributes = "";
			$this->LevelID->HrefValue = "";
			$this->LevelID->TooltipValue = "";

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

			// AgamaID
			$this->AgamaID->LinkCustomAttributes = "";
			$this->AgamaID->HrefValue = "";
			$this->AgamaID->TooltipValue = "";

			// KelaminID
			$this->KelaminID->LinkCustomAttributes = "";
			$this->KelaminID->HrefValue = "";
			$this->KelaminID->TooltipValue = "";

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

			// InstitusiInduk
			$this->InstitusiInduk->LinkCustomAttributes = "";
			$this->InstitusiInduk->HrefValue = "";
			$this->InstitusiInduk->TooltipValue = "";

			// IkatanID
			$this->IkatanID->LinkCustomAttributes = "";
			$this->IkatanID->HrefValue = "";
			$this->IkatanID->TooltipValue = "";

			// GolonganID
			$this->GolonganID->LinkCustomAttributes = "";
			$this->GolonganID->HrefValue = "";
			$this->GolonganID->TooltipValue = "";

			// StatusKerjaID
			$this->StatusKerjaID->LinkCustomAttributes = "";
			$this->StatusKerjaID->HrefValue = "";
			$this->StatusKerjaID->TooltipValue = "";

			// TglBekerja
			$this->TglBekerja->LinkCustomAttributes = "";
			$this->TglBekerja->HrefValue = "";
			$this->TglBekerja->TooltipValue = "";

			// Homebase
			$this->Homebase->LinkCustomAttributes = "";
			$this->Homebase->HrefValue = "";
			$this->Homebase->TooltipValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// Keilmuan
			$this->Keilmuan->LinkCustomAttributes = "";
			$this->Keilmuan->HrefValue = "";
			$this->Keilmuan->TooltipValue = "";

			// LulusanPT
			$this->LulusanPT->LinkCustomAttributes = "";
			$this->LulusanPT->HrefValue = "";
			$this->LulusanPT->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// TeacherID
			$this->TeacherID->EditAttrs["class"] = "form-control";
			$this->TeacherID->EditCustomAttributes = "";
			$this->TeacherID->EditValue = ew_HtmlEncode($this->TeacherID->AdvancedSearch->SearchValue);
			$this->TeacherID->PlaceHolder = ew_RemoveHtml($this->TeacherID->FldCaption());

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

			// LevelID
			$this->LevelID->EditAttrs["class"] = "form-control";
			$this->LevelID->EditCustomAttributes = "";
			$this->LevelID->EditValue = ew_HtmlEncode($this->LevelID->AdvancedSearch->SearchValue);
			$this->LevelID->PlaceHolder = ew_RemoveHtml($this->LevelID->FldCaption());

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

			// InstitusiInduk
			$this->InstitusiInduk->EditAttrs["class"] = "form-control";
			$this->InstitusiInduk->EditCustomAttributes = "";
			if (trim(strval($this->InstitusiInduk->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->InstitusiInduk->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_kampus`";
			$sWhereWrk = "";
			$this->InstitusiInduk->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->InstitusiInduk, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->InstitusiInduk->EditValue = $arwrk;

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

			// Homebase
			$this->Homebase->EditAttrs["class"] = "form-control";
			$this->Homebase->EditCustomAttributes = "";
			if (trim(strval($this->Homebase->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->Homebase->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->Homebase->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Homebase, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Homebase->EditValue = $arwrk;

			// ProdiID
			$this->ProdiID->EditCustomAttributes = "";
			if (trim(strval($this->ProdiID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$arwrk = explode(",", $this->ProdiID->AdvancedSearch->SearchValue);
				$sFilterWrk = "";
				foreach ($arwrk as $wrk) {
					if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
					$sFilterWrk .= "`ProdiID`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING, "");
				}
			}
			$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->ProdiID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProdiID->EditValue = $arwrk;

			// Keilmuan
			$this->Keilmuan->EditAttrs["class"] = "form-control";
			$this->Keilmuan->EditCustomAttributes = "";
			$this->Keilmuan->EditValue = ew_HtmlEncode($this->Keilmuan->AdvancedSearch->SearchValue);
			$this->Keilmuan->PlaceHolder = ew_RemoveHtml($this->Keilmuan->FldCaption());

			// LulusanPT
			$this->LulusanPT->EditAttrs["class"] = "form-control";
			$this->LulusanPT->EditCustomAttributes = "";
			$this->LulusanPT->EditValue = ew_HtmlEncode($this->LulusanPT->AdvancedSearch->SearchValue);
			$this->LulusanPT->PlaceHolder = ew_RemoveHtml($this->LulusanPT->FldCaption());

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
		$this->TeacherID->AdvancedSearch->Load();
		$this->NIPPNS->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->Gelar->AdvancedSearch->Load();
		$this->LevelID->AdvancedSearch->Load();
		$this->KTP->AdvancedSearch->Load();
		$this->TempatLahir->AdvancedSearch->Load();
		$this->TanggalLahir->AdvancedSearch->Load();
		$this->AgamaID->AdvancedSearch->Load();
		$this->KelaminID->AdvancedSearch->Load();
		$this->Telephone->AdvancedSearch->Load();
		$this->_Email->AdvancedSearch->Load();
		$this->Alamat->AdvancedSearch->Load();
		$this->KodePos->AdvancedSearch->Load();
		$this->ProvinsiID->AdvancedSearch->Load();
		$this->KabupatenKotaID->AdvancedSearch->Load();
		$this->KecamatanID->AdvancedSearch->Load();
		$this->DesaID->AdvancedSearch->Load();
		$this->InstitusiInduk->AdvancedSearch->Load();
		$this->IkatanID->AdvancedSearch->Load();
		$this->GolonganID->AdvancedSearch->Load();
		$this->StatusKerjaID->AdvancedSearch->Load();
		$this->TglBekerja->AdvancedSearch->Load();
		$this->Homebase->AdvancedSearch->Load();
		$this->ProdiID->AdvancedSearch->Load();
		$this->Keilmuan->AdvancedSearch->Load();
		$this->LulusanPT->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("teacherlist.php"), "", $this->TableVar, TRUE);
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
		$pages->Add(5);
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
		case "x_InstitusiInduk":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KampusID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
			$sWhereWrk = "";
			$this->InstitusiInduk->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KampusID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->InstitusiInduk, $sWhereWrk); // Call Lookup selecting
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
		case "x_Homebase":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProdiID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->Homebase->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProdiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Homebase, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_ProdiID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProdiID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->ProdiID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProdiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
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
if (!isset($teacher_search)) $teacher_search = new cteacher_search();

// Page init
$teacher_search->Page_Init();

// Page main
$teacher_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$teacher_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($teacher_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fteachersearch = new ew_Form("fteachersearch", "search");
<?php } else { ?>
var CurrentForm = fteachersearch = new ew_Form("fteachersearch", "search");
<?php } ?>

// Form_CustomValidate event
fteachersearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fteachersearch.ValidateRequired = true;
<?php } else { ?>
fteachersearch.ValidateRequired = false; 
<?php } ?>

// Multi-Page
fteachersearch.MultiPage = new ew_MultiPage("fteachersearch");

// Dynamic selection lists
fteachersearch.Lists["x_TempatLahir"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fteachersearch.Lists["x_AgamaID"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fteachersearch.Lists["x_KelaminID"] = {"LinkField":"x_Kelamin","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kelamin"};
fteachersearch.Lists["x_ProvinsiID"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenKotaID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fteachersearch.Lists["x_KabupatenKotaID"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":["x_ProvinsiID"],"ChildFields":["x_KecamatanID"],"FilterFields":["x_ProvinsiID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fteachersearch.Lists["x_KecamatanID"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":["x_KabupatenKotaID"],"ChildFields":["x_DesaID"],"FilterFields":["x_KabupatenKotaID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fteachersearch.Lists["x_DesaID"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":["x_KecamatanID"],"ChildFields":[],"FilterFields":["x_KecamatanID"],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fteachersearch.Lists["x_InstitusiInduk"] = {"LinkField":"x_KampusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kampus"};
fteachersearch.Lists["x_IkatanID"] = {"LinkField":"x_IkatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_ikatan"};
fteachersearch.Lists["x_GolonganID"] = {"LinkField":"x_GolonganID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_golongan"};
fteachersearch.Lists["x_StatusKerjaID"] = {"LinkField":"x_StatusKerjaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statuskerja"};
fteachersearch.Lists["x_Homebase"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fteachersearch.Lists["x_ProdiID[]"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fteachersearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fteachersearch.Lists["x_NA"].Options = <?php echo json_encode($teacher->NA->Options()) ?>;

// Form object for search
// Validate function for search

fteachersearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_LevelID");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($teacher->LevelID->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_TanggalLahir");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($teacher->TanggalLahir->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_TglBekerja");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($teacher->TglBekerja->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$teacher_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $teacher_search->ShowPageHeader(); ?>
<?php
$teacher_search->ShowMessage();
?>
<form name="fteachersearch" id="fteachersearch" class="<?php echo $teacher_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($teacher_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $teacher_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="teacher">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($teacher_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$teacher_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<div class="ewMultiPage">
<div class="tabbable" id="teacher_search">
	<ul class="nav<?php echo $teacher_search->MultiPages->NavStyle() ?>">
		<li<?php echo $teacher_search->MultiPages->TabStyle("1") ?>><a href="#tab_teacher1" data-toggle="tab"><?php echo $teacher->PageCaption(1) ?></a></li>
		<li<?php echo $teacher_search->MultiPages->TabStyle("2") ?>><a href="#tab_teacher2" data-toggle="tab"><?php echo $teacher->PageCaption(2) ?></a></li>
		<li<?php echo $teacher_search->MultiPages->TabStyle("3") ?>><a href="#tab_teacher3" data-toggle="tab"><?php echo $teacher->PageCaption(3) ?></a></li>
		<li style="display: none"><a href="#tab_teacher4" data-toggle="tab"></a></li>
		<li<?php echo $teacher_search->MultiPages->TabStyle("5") ?>><a href="#tab_teacher5" data-toggle="tab"><?php echo $teacher->PageCaption(5) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane<?php echo $teacher_search->MultiPages->PageStyle("1") ?>" id="tab_teacher1">
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_teachersearch1" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($teacher->TeacherID->Visible) { // TeacherID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_TeacherID" class="form-group">
		<label for="x_TeacherID" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_TeacherID"><?php echo $teacher->TeacherID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TeacherID" id="z_TeacherID" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->TeacherID->CellAttributes() ?>>
			<span id="el_teacher_TeacherID">
<input type="text" data-table="teacher" data-field="x_TeacherID" data-page="1" name="x_TeacherID" id="x_TeacherID" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->TeacherID->getPlaceHolder()) ?>" value="<?php echo $teacher->TeacherID->EditValue ?>"<?php echo $teacher->TeacherID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TeacherID">
		<td><span id="elh_teacher_TeacherID"><?php echo $teacher->TeacherID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TeacherID" id="z_TeacherID" value="LIKE"></span></td>
		<td<?php echo $teacher->TeacherID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_TeacherID">
<input type="text" data-table="teacher" data-field="x_TeacherID" data-page="1" name="x_TeacherID" id="x_TeacherID" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->TeacherID->getPlaceHolder()) ?>" value="<?php echo $teacher->TeacherID->EditValue ?>"<?php echo $teacher->TeacherID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->NIPPNS->Visible) { // NIPPNS ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_NIPPNS" class="form-group">
		<label for="x_NIPPNS" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_NIPPNS"><?php echo $teacher->NIPPNS->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NIPPNS" id="z_NIPPNS" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->NIPPNS->CellAttributes() ?>>
			<span id="el_teacher_NIPPNS">
<input type="text" data-table="teacher" data-field="x_NIPPNS" data-page="1" name="x_NIPPNS" id="x_NIPPNS" size="30" maxlength="30" placeholder="<?php echo ew_HtmlEncode($teacher->NIPPNS->getPlaceHolder()) ?>" value="<?php echo $teacher->NIPPNS->EditValue ?>"<?php echo $teacher->NIPPNS->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NIPPNS">
		<td><span id="elh_teacher_NIPPNS"><?php echo $teacher->NIPPNS->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NIPPNS" id="z_NIPPNS" value="LIKE"></span></td>
		<td<?php echo $teacher->NIPPNS->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_NIPPNS">
<input type="text" data-table="teacher" data-field="x_NIPPNS" data-page="1" name="x_NIPPNS" id="x_NIPPNS" size="30" maxlength="30" placeholder="<?php echo ew_HtmlEncode($teacher->NIPPNS->getPlaceHolder()) ?>" value="<?php echo $teacher->NIPPNS->EditValue ?>"<?php echo $teacher->NIPPNS->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label for="x_Nama" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_Nama"><?php echo $teacher->Nama->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->Nama->CellAttributes() ?>>
			<span id="el_teacher_Nama">
<input type="text" data-table="teacher" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->Nama->getPlaceHolder()) ?>" value="<?php echo $teacher->Nama->EditValue ?>"<?php echo $teacher->Nama->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_teacher_Nama"><?php echo $teacher->Nama->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></span></td>
		<td<?php echo $teacher->Nama->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_Nama">
<input type="text" data-table="teacher" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->Nama->getPlaceHolder()) ?>" value="<?php echo $teacher->Nama->EditValue ?>"<?php echo $teacher->Nama->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Gelar->Visible) { // Gelar ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_Gelar" class="form-group">
		<label for="x_Gelar" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_Gelar"><?php echo $teacher->Gelar->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Gelar" id="z_Gelar" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->Gelar->CellAttributes() ?>>
			<span id="el_teacher_Gelar">
<input type="text" data-table="teacher" data-field="x_Gelar" data-page="1" name="x_Gelar" id="x_Gelar" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($teacher->Gelar->getPlaceHolder()) ?>" value="<?php echo $teacher->Gelar->EditValue ?>"<?php echo $teacher->Gelar->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Gelar">
		<td><span id="elh_teacher_Gelar"><?php echo $teacher->Gelar->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Gelar" id="z_Gelar" value="LIKE"></span></td>
		<td<?php echo $teacher->Gelar->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_Gelar">
<input type="text" data-table="teacher" data-field="x_Gelar" data-page="1" name="x_Gelar" id="x_Gelar" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($teacher->Gelar->getPlaceHolder()) ?>" value="<?php echo $teacher->Gelar->EditValue ?>"<?php echo $teacher->Gelar->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->LevelID->Visible) { // LevelID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_LevelID" class="form-group">
		<label for="x_LevelID" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_LevelID"><?php echo $teacher->LevelID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_LevelID" id="z_LevelID" value="="></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->LevelID->CellAttributes() ?>>
			<span id="el_teacher_LevelID">
<input type="text" data-table="teacher" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($teacher->LevelID->getPlaceHolder()) ?>" value="<?php echo $teacher->LevelID->EditValue ?>"<?php echo $teacher->LevelID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_LevelID">
		<td><span id="elh_teacher_LevelID"><?php echo $teacher->LevelID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_LevelID" id="z_LevelID" value="="></span></td>
		<td<?php echo $teacher->LevelID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_LevelID">
<input type="text" data-table="teacher" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($teacher->LevelID->getPlaceHolder()) ?>" value="<?php echo $teacher->LevelID->EditValue ?>"<?php echo $teacher->LevelID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $teacher_search->MultiPages->PageStyle("2") ?>" id="tab_teacher2">
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_teachersearch2" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($teacher->KTP->Visible) { // KTP ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_KTP" class="form-group">
		<label for="x_KTP" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_KTP"><?php echo $teacher->KTP->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KTP" id="z_KTP" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->KTP->CellAttributes() ?>>
			<span id="el_teacher_KTP">
<input type="text" data-table="teacher" data-field="x_KTP" data-page="2" name="x_KTP" id="x_KTP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->KTP->getPlaceHolder()) ?>" value="<?php echo $teacher->KTP->EditValue ?>"<?php echo $teacher->KTP->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KTP">
		<td><span id="elh_teacher_KTP"><?php echo $teacher->KTP->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KTP" id="z_KTP" value="LIKE"></span></td>
		<td<?php echo $teacher->KTP->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_KTP">
<input type="text" data-table="teacher" data-field="x_KTP" data-page="2" name="x_KTP" id="x_KTP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->KTP->getPlaceHolder()) ?>" value="<?php echo $teacher->KTP->EditValue ?>"<?php echo $teacher->KTP->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->TempatLahir->Visible) { // TempatLahir ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_TempatLahir" class="form-group">
		<label class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_TempatLahir"><?php echo $teacher->TempatLahir->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TempatLahir" id="z_TempatLahir" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->TempatLahir->CellAttributes() ?>>
			<span id="el_teacher_TempatLahir">
<?php
$wrkonchange = trim(" " . @$teacher->TempatLahir->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$teacher->TempatLahir->EditAttrs["onchange"] = "";
?>
<span id="as_x_TempatLahir" style="white-space: nowrap; z-index: 8910">
	<input type="text" name="sv_x_TempatLahir" id="sv_x_TempatLahir" value="<?php echo $teacher->TempatLahir->EditValue ?>" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->TempatLahir->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($teacher->TempatLahir->getPlaceHolder()) ?>"<?php echo $teacher->TempatLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="teacher" data-field="x_TempatLahir" data-page="2" data-value-separator="<?php echo $teacher->TempatLahir->DisplayValueSeparatorAttribute() ?>" name="x_TempatLahir" id="x_TempatLahir" value="<?php echo ew_HtmlEncode($teacher->TempatLahir->AdvancedSearch->SearchValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TempatLahir" id="q_x_TempatLahir" value="<?php echo $teacher->TempatLahir->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fteachersearch.CreateAutoSuggest({"id":"x_TempatLahir","forceSelect":false});
</script>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TempatLahir">
		<td><span id="elh_teacher_TempatLahir"><?php echo $teacher->TempatLahir->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TempatLahir" id="z_TempatLahir" value="LIKE"></span></td>
		<td<?php echo $teacher->TempatLahir->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_TempatLahir">
<?php
$wrkonchange = trim(" " . @$teacher->TempatLahir->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$teacher->TempatLahir->EditAttrs["onchange"] = "";
?>
<span id="as_x_TempatLahir" style="white-space: nowrap; z-index: 8910">
	<input type="text" name="sv_x_TempatLahir" id="sv_x_TempatLahir" value="<?php echo $teacher->TempatLahir->EditValue ?>" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->TempatLahir->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($teacher->TempatLahir->getPlaceHolder()) ?>"<?php echo $teacher->TempatLahir->EditAttributes() ?>>
</span>
<input type="hidden" data-table="teacher" data-field="x_TempatLahir" data-page="2" data-value-separator="<?php echo $teacher->TempatLahir->DisplayValueSeparatorAttribute() ?>" name="x_TempatLahir" id="x_TempatLahir" value="<?php echo ew_HtmlEncode($teacher->TempatLahir->AdvancedSearch->SearchValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TempatLahir" id="q_x_TempatLahir" value="<?php echo $teacher->TempatLahir->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fteachersearch.CreateAutoSuggest({"id":"x_TempatLahir","forceSelect":false});
</script>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->TanggalLahir->Visible) { // TanggalLahir ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_TanggalLahir" class="form-group">
		<label for="x_TanggalLahir" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_TanggalLahir"><?php echo $teacher->TanggalLahir->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanggalLahir" id="z_TanggalLahir" value="="></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->TanggalLahir->CellAttributes() ?>>
			<span id="el_teacher_TanggalLahir">
<input type="text" data-table="teacher" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($teacher->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $teacher->TanggalLahir->EditValue ?>"<?php echo $teacher->TanggalLahir->EditAttributes() ?>>
<?php if (!$teacher->TanggalLahir->ReadOnly && !$teacher->TanggalLahir->Disabled && !isset($teacher->TanggalLahir->EditAttrs["readonly"]) && !isset($teacher->TanggalLahir->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fteachersearch", "x_TanggalLahir", 0);
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanggalLahir">
		<td><span id="elh_teacher_TanggalLahir"><?php echo $teacher->TanggalLahir->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanggalLahir" id="z_TanggalLahir" value="="></span></td>
		<td<?php echo $teacher->TanggalLahir->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_TanggalLahir">
<input type="text" data-table="teacher" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($teacher->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $teacher->TanggalLahir->EditValue ?>"<?php echo $teacher->TanggalLahir->EditAttributes() ?>>
<?php if (!$teacher->TanggalLahir->ReadOnly && !$teacher->TanggalLahir->Disabled && !isset($teacher->TanggalLahir->EditAttrs["readonly"]) && !isset($teacher->TanggalLahir->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fteachersearch", "x_TanggalLahir", 0);
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->AgamaID->Visible) { // AgamaID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_AgamaID" class="form-group">
		<label for="x_AgamaID" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_AgamaID"><?php echo $teacher->AgamaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AgamaID" id="z_AgamaID" value="="></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->AgamaID->CellAttributes() ?>>
			<span id="el_teacher_AgamaID">
<select data-table="teacher" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $teacher->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $teacher->AgamaID->EditAttributes() ?>>
<?php echo $teacher->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $teacher->AgamaID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_AgamaID">
		<td><span id="elh_teacher_AgamaID"><?php echo $teacher->AgamaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AgamaID" id="z_AgamaID" value="="></span></td>
		<td<?php echo $teacher->AgamaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_AgamaID">
<select data-table="teacher" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $teacher->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $teacher->AgamaID->EditAttributes() ?>>
<?php echo $teacher->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $teacher->AgamaID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->KelaminID->Visible) { // KelaminID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_KelaminID" class="form-group">
		<label class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_KelaminID"><?php echo $teacher->KelaminID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KelaminID" id="z_KelaminID" value="="></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->KelaminID->CellAttributes() ?>>
			<span id="el_teacher_KelaminID">
<div id="tp_x_KelaminID" class="ewTemplate"><input type="radio" data-table="teacher" data-field="x_KelaminID" data-page="2" data-value-separator="<?php echo $teacher->KelaminID->DisplayValueSeparatorAttribute() ?>" name="x_KelaminID" id="x_KelaminID" value="{value}"<?php echo $teacher->KelaminID->EditAttributes() ?>></div>
<div id="dsl_x_KelaminID" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->KelaminID->RadioButtonListHtml(FALSE, "x_KelaminID", 2) ?>
</div></div>
<input type="hidden" name="s_x_KelaminID" id="s_x_KelaminID" value="<?php echo $teacher->KelaminID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KelaminID">
		<td><span id="elh_teacher_KelaminID"><?php echo $teacher->KelaminID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KelaminID" id="z_KelaminID" value="="></span></td>
		<td<?php echo $teacher->KelaminID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_KelaminID">
<div id="tp_x_KelaminID" class="ewTemplate"><input type="radio" data-table="teacher" data-field="x_KelaminID" data-page="2" data-value-separator="<?php echo $teacher->KelaminID->DisplayValueSeparatorAttribute() ?>" name="x_KelaminID" id="x_KelaminID" value="{value}"<?php echo $teacher->KelaminID->EditAttributes() ?>></div>
<div id="dsl_x_KelaminID" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->KelaminID->RadioButtonListHtml(FALSE, "x_KelaminID", 2) ?>
</div></div>
<input type="hidden" name="s_x_KelaminID" id="s_x_KelaminID" value="<?php echo $teacher->KelaminID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Telephone->Visible) { // Telephone ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_Telephone" class="form-group">
		<label for="x_Telephone" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_Telephone"><?php echo $teacher->Telephone->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Telephone" id="z_Telephone" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->Telephone->CellAttributes() ?>>
			<span id="el_teacher_Telephone">
<input type="text" data-table="teacher" data-field="x_Telephone" data-page="2" name="x_Telephone" id="x_Telephone" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->Telephone->getPlaceHolder()) ?>" value="<?php echo $teacher->Telephone->EditValue ?>"<?php echo $teacher->Telephone->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Telephone">
		<td><span id="elh_teacher_Telephone"><?php echo $teacher->Telephone->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Telephone" id="z_Telephone" value="LIKE"></span></td>
		<td<?php echo $teacher->Telephone->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_Telephone">
<input type="text" data-table="teacher" data-field="x_Telephone" data-page="2" name="x_Telephone" id="x_Telephone" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->Telephone->getPlaceHolder()) ?>" value="<?php echo $teacher->Telephone->EditValue ?>"<?php echo $teacher->Telephone->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->_Email->Visible) { // Email ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r__Email" class="form-group">
		<label for="x__Email" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher__Email"><?php echo $teacher->_Email->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z__Email" id="z__Email" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->_Email->CellAttributes() ?>>
			<span id="el_teacher__Email">
<input type="text" data-table="teacher" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($teacher->_Email->getPlaceHolder()) ?>" value="<?php echo $teacher->_Email->EditValue ?>"<?php echo $teacher->_Email->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r__Email">
		<td><span id="elh_teacher__Email"><?php echo $teacher->_Email->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z__Email" id="z__Email" value="LIKE"></span></td>
		<td<?php echo $teacher->_Email->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher__Email">
<input type="text" data-table="teacher" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($teacher->_Email->getPlaceHolder()) ?>" value="<?php echo $teacher->_Email->EditValue ?>"<?php echo $teacher->_Email->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Alamat->Visible) { // Alamat ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_Alamat" class="form-group">
		<label for="x_Alamat" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_Alamat"><?php echo $teacher->Alamat->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Alamat" id="z_Alamat" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->Alamat->CellAttributes() ?>>
			<span id="el_teacher_Alamat">
<input type="text" data-table="teacher" data-field="x_Alamat" data-page="2" name="x_Alamat" id="x_Alamat" size="35" placeholder="<?php echo ew_HtmlEncode($teacher->Alamat->getPlaceHolder()) ?>" value="<?php echo $teacher->Alamat->EditValue ?>"<?php echo $teacher->Alamat->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Alamat">
		<td><span id="elh_teacher_Alamat"><?php echo $teacher->Alamat->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Alamat" id="z_Alamat" value="LIKE"></span></td>
		<td<?php echo $teacher->Alamat->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_Alamat">
<input type="text" data-table="teacher" data-field="x_Alamat" data-page="2" name="x_Alamat" id="x_Alamat" size="35" placeholder="<?php echo ew_HtmlEncode($teacher->Alamat->getPlaceHolder()) ?>" value="<?php echo $teacher->Alamat->EditValue ?>"<?php echo $teacher->Alamat->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->KodePos->Visible) { // KodePos ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_KodePos" class="form-group">
		<label for="x_KodePos" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_KodePos"><?php echo $teacher->KodePos->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodePos" id="z_KodePos" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->KodePos->CellAttributes() ?>>
			<span id="el_teacher_KodePos">
<input type="text" data-table="teacher" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->KodePos->getPlaceHolder()) ?>" value="<?php echo $teacher->KodePos->EditValue ?>"<?php echo $teacher->KodePos->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodePos">
		<td><span id="elh_teacher_KodePos"><?php echo $teacher->KodePos->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodePos" id="z_KodePos" value="LIKE"></span></td>
		<td<?php echo $teacher->KodePos->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_KodePos">
<input type="text" data-table="teacher" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($teacher->KodePos->getPlaceHolder()) ?>" value="<?php echo $teacher->KodePos->EditValue ?>"<?php echo $teacher->KodePos->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->ProvinsiID->Visible) { // ProvinsiID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_ProvinsiID" class="form-group">
		<label for="x_ProvinsiID" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_ProvinsiID"><?php echo $teacher->ProvinsiID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProvinsiID" id="z_ProvinsiID" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->ProvinsiID->CellAttributes() ?>>
			<span id="el_teacher_ProvinsiID">
<?php $teacher->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $teacher->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $teacher->ProvinsiID->EditAttributes() ?>>
<?php echo $teacher->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $teacher->ProvinsiID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiID">
		<td><span id="elh_teacher_ProvinsiID"><?php echo $teacher->ProvinsiID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProvinsiID" id="z_ProvinsiID" value="LIKE"></span></td>
		<td<?php echo $teacher->ProvinsiID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_ProvinsiID">
<?php $teacher->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $teacher->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $teacher->ProvinsiID->EditAttributes() ?>>
<?php echo $teacher->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $teacher->ProvinsiID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->KabupatenKotaID->Visible) { // KabupatenKotaID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_KabupatenKotaID" class="form-group">
		<label for="x_KabupatenKotaID" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_KabupatenKotaID"><?php echo $teacher->KabupatenKotaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KabupatenKotaID" id="z_KabupatenKotaID" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->KabupatenKotaID->CellAttributes() ?>>
			<span id="el_teacher_KabupatenKotaID">
<?php $teacher->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $teacher->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $teacher->KabupatenKotaID->EditAttributes() ?>>
<?php echo $teacher->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $teacher->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenKotaID">
		<td><span id="elh_teacher_KabupatenKotaID"><?php echo $teacher->KabupatenKotaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KabupatenKotaID" id="z_KabupatenKotaID" value="LIKE"></span></td>
		<td<?php echo $teacher->KabupatenKotaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_KabupatenKotaID">
<?php $teacher->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $teacher->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $teacher->KabupatenKotaID->EditAttributes() ?>>
<?php echo $teacher->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $teacher->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->KecamatanID->Visible) { // KecamatanID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_KecamatanID" class="form-group">
		<label for="x_KecamatanID" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_KecamatanID"><?php echo $teacher->KecamatanID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KecamatanID" id="z_KecamatanID" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->KecamatanID->CellAttributes() ?>>
			<span id="el_teacher_KecamatanID">
<?php $teacher->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $teacher->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $teacher->KecamatanID->EditAttributes() ?>>
<?php echo $teacher->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $teacher->KecamatanID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanID">
		<td><span id="elh_teacher_KecamatanID"><?php echo $teacher->KecamatanID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KecamatanID" id="z_KecamatanID" value="LIKE"></span></td>
		<td<?php echo $teacher->KecamatanID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_KecamatanID">
<?php $teacher->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$teacher->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="teacher" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $teacher->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $teacher->KecamatanID->EditAttributes() ?>>
<?php echo $teacher->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $teacher->KecamatanID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->DesaID->Visible) { // DesaID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_DesaID" class="form-group">
		<label for="x_DesaID" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_DesaID"><?php echo $teacher->DesaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_DesaID" id="z_DesaID" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->DesaID->CellAttributes() ?>>
			<span id="el_teacher_DesaID">
<select data-table="teacher" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $teacher->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $teacher->DesaID->EditAttributes() ?>>
<?php echo $teacher->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $teacher->DesaID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_DesaID">
		<td><span id="elh_teacher_DesaID"><?php echo $teacher->DesaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_DesaID" id="z_DesaID" value="LIKE"></span></td>
		<td<?php echo $teacher->DesaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_DesaID">
<select data-table="teacher" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $teacher->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $teacher->DesaID->EditAttributes() ?>>
<?php echo $teacher->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $teacher->DesaID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $teacher_search->MultiPages->PageStyle("3") ?>" id="tab_teacher3">
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_teachersearch3" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($teacher->InstitusiInduk->Visible) { // InstitusiInduk ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_InstitusiInduk" class="form-group">
		<label for="x_InstitusiInduk" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_InstitusiInduk"><?php echo $teacher->InstitusiInduk->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_InstitusiInduk" id="z_InstitusiInduk" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->InstitusiInduk->CellAttributes() ?>>
			<span id="el_teacher_InstitusiInduk">
<select data-table="teacher" data-field="x_InstitusiInduk" data-page="3" data-value-separator="<?php echo $teacher->InstitusiInduk->DisplayValueSeparatorAttribute() ?>" id="x_InstitusiInduk" name="x_InstitusiInduk"<?php echo $teacher->InstitusiInduk->EditAttributes() ?>>
<?php echo $teacher->InstitusiInduk->SelectOptionListHtml("x_InstitusiInduk") ?>
</select>
<input type="hidden" name="s_x_InstitusiInduk" id="s_x_InstitusiInduk" value="<?php echo $teacher->InstitusiInduk->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_InstitusiInduk">
		<td><span id="elh_teacher_InstitusiInduk"><?php echo $teacher->InstitusiInduk->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_InstitusiInduk" id="z_InstitusiInduk" value="LIKE"></span></td>
		<td<?php echo $teacher->InstitusiInduk->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_InstitusiInduk">
<select data-table="teacher" data-field="x_InstitusiInduk" data-page="3" data-value-separator="<?php echo $teacher->InstitusiInduk->DisplayValueSeparatorAttribute() ?>" id="x_InstitusiInduk" name="x_InstitusiInduk"<?php echo $teacher->InstitusiInduk->EditAttributes() ?>>
<?php echo $teacher->InstitusiInduk->SelectOptionListHtml("x_InstitusiInduk") ?>
</select>
<input type="hidden" name="s_x_InstitusiInduk" id="s_x_InstitusiInduk" value="<?php echo $teacher->InstitusiInduk->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->IkatanID->Visible) { // IkatanID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_IkatanID" class="form-group">
		<label for="x_IkatanID" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_IkatanID"><?php echo $teacher->IkatanID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_IkatanID" id="z_IkatanID" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->IkatanID->CellAttributes() ?>>
			<span id="el_teacher_IkatanID">
<select data-table="teacher" data-field="x_IkatanID" data-page="3" data-value-separator="<?php echo $teacher->IkatanID->DisplayValueSeparatorAttribute() ?>" id="x_IkatanID" name="x_IkatanID"<?php echo $teacher->IkatanID->EditAttributes() ?>>
<?php echo $teacher->IkatanID->SelectOptionListHtml("x_IkatanID") ?>
</select>
<input type="hidden" name="s_x_IkatanID" id="s_x_IkatanID" value="<?php echo $teacher->IkatanID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_IkatanID">
		<td><span id="elh_teacher_IkatanID"><?php echo $teacher->IkatanID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_IkatanID" id="z_IkatanID" value="LIKE"></span></td>
		<td<?php echo $teacher->IkatanID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_IkatanID">
<select data-table="teacher" data-field="x_IkatanID" data-page="3" data-value-separator="<?php echo $teacher->IkatanID->DisplayValueSeparatorAttribute() ?>" id="x_IkatanID" name="x_IkatanID"<?php echo $teacher->IkatanID->EditAttributes() ?>>
<?php echo $teacher->IkatanID->SelectOptionListHtml("x_IkatanID") ?>
</select>
<input type="hidden" name="s_x_IkatanID" id="s_x_IkatanID" value="<?php echo $teacher->IkatanID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->GolonganID->Visible) { // GolonganID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_GolonganID" class="form-group">
		<label for="x_GolonganID" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_GolonganID"><?php echo $teacher->GolonganID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_GolonganID" id="z_GolonganID" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->GolonganID->CellAttributes() ?>>
			<span id="el_teacher_GolonganID">
<select data-table="teacher" data-field="x_GolonganID" data-page="3" data-value-separator="<?php echo $teacher->GolonganID->DisplayValueSeparatorAttribute() ?>" id="x_GolonganID" name="x_GolonganID"<?php echo $teacher->GolonganID->EditAttributes() ?>>
<?php echo $teacher->GolonganID->SelectOptionListHtml("x_GolonganID") ?>
</select>
<input type="hidden" name="s_x_GolonganID" id="s_x_GolonganID" value="<?php echo $teacher->GolonganID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_GolonganID">
		<td><span id="elh_teacher_GolonganID"><?php echo $teacher->GolonganID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_GolonganID" id="z_GolonganID" value="LIKE"></span></td>
		<td<?php echo $teacher->GolonganID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_GolonganID">
<select data-table="teacher" data-field="x_GolonganID" data-page="3" data-value-separator="<?php echo $teacher->GolonganID->DisplayValueSeparatorAttribute() ?>" id="x_GolonganID" name="x_GolonganID"<?php echo $teacher->GolonganID->EditAttributes() ?>>
<?php echo $teacher->GolonganID->SelectOptionListHtml("x_GolonganID") ?>
</select>
<input type="hidden" name="s_x_GolonganID" id="s_x_GolonganID" value="<?php echo $teacher->GolonganID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->StatusKerjaID->Visible) { // StatusKerjaID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_StatusKerjaID" class="form-group">
		<label for="x_StatusKerjaID" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_StatusKerjaID"><?php echo $teacher->StatusKerjaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusKerjaID" id="z_StatusKerjaID" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->StatusKerjaID->CellAttributes() ?>>
			<span id="el_teacher_StatusKerjaID">
<select data-table="teacher" data-field="x_StatusKerjaID" data-page="3" data-value-separator="<?php echo $teacher->StatusKerjaID->DisplayValueSeparatorAttribute() ?>" id="x_StatusKerjaID" name="x_StatusKerjaID"<?php echo $teacher->StatusKerjaID->EditAttributes() ?>>
<?php echo $teacher->StatusKerjaID->SelectOptionListHtml("x_StatusKerjaID") ?>
</select>
<input type="hidden" name="s_x_StatusKerjaID" id="s_x_StatusKerjaID" value="<?php echo $teacher->StatusKerjaID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusKerjaID">
		<td><span id="elh_teacher_StatusKerjaID"><?php echo $teacher->StatusKerjaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusKerjaID" id="z_StatusKerjaID" value="LIKE"></span></td>
		<td<?php echo $teacher->StatusKerjaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_StatusKerjaID">
<select data-table="teacher" data-field="x_StatusKerjaID" data-page="3" data-value-separator="<?php echo $teacher->StatusKerjaID->DisplayValueSeparatorAttribute() ?>" id="x_StatusKerjaID" name="x_StatusKerjaID"<?php echo $teacher->StatusKerjaID->EditAttributes() ?>>
<?php echo $teacher->StatusKerjaID->SelectOptionListHtml("x_StatusKerjaID") ?>
</select>
<input type="hidden" name="s_x_StatusKerjaID" id="s_x_StatusKerjaID" value="<?php echo $teacher->StatusKerjaID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->TglBekerja->Visible) { // TglBekerja ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_TglBekerja" class="form-group">
		<label for="x_TglBekerja" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_TglBekerja"><?php echo $teacher->TglBekerja->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TglBekerja" id="z_TglBekerja" value="="></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->TglBekerja->CellAttributes() ?>>
			<span id="el_teacher_TglBekerja">
<input type="text" data-table="teacher" data-field="x_TglBekerja" data-page="3" name="x_TglBekerja" id="x_TglBekerja" placeholder="<?php echo ew_HtmlEncode($teacher->TglBekerja->getPlaceHolder()) ?>" value="<?php echo $teacher->TglBekerja->EditValue ?>"<?php echo $teacher->TglBekerja->EditAttributes() ?>>
<?php if (!$teacher->TglBekerja->ReadOnly && !$teacher->TglBekerja->Disabled && !isset($teacher->TglBekerja->EditAttrs["readonly"]) && !isset($teacher->TglBekerja->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fteachersearch", "x_TglBekerja", 0);
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TglBekerja">
		<td><span id="elh_teacher_TglBekerja"><?php echo $teacher->TglBekerja->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TglBekerja" id="z_TglBekerja" value="="></span></td>
		<td<?php echo $teacher->TglBekerja->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_TglBekerja">
<input type="text" data-table="teacher" data-field="x_TglBekerja" data-page="3" name="x_TglBekerja" id="x_TglBekerja" placeholder="<?php echo ew_HtmlEncode($teacher->TglBekerja->getPlaceHolder()) ?>" value="<?php echo $teacher->TglBekerja->EditValue ?>"<?php echo $teacher->TglBekerja->EditAttributes() ?>>
<?php if (!$teacher->TglBekerja->ReadOnly && !$teacher->TglBekerja->Disabled && !isset($teacher->TglBekerja->EditAttrs["readonly"]) && !isset($teacher->TglBekerja->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fteachersearch", "x_TglBekerja", 0);
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Homebase->Visible) { // Homebase ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_Homebase" class="form-group">
		<label for="x_Homebase" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_Homebase"><?php echo $teacher->Homebase->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Homebase" id="z_Homebase" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->Homebase->CellAttributes() ?>>
			<span id="el_teacher_Homebase">
<select data-table="teacher" data-field="x_Homebase" data-page="3" data-value-separator="<?php echo $teacher->Homebase->DisplayValueSeparatorAttribute() ?>" id="x_Homebase" name="x_Homebase"<?php echo $teacher->Homebase->EditAttributes() ?>>
<?php echo $teacher->Homebase->SelectOptionListHtml("x_Homebase") ?>
</select>
<input type="hidden" name="s_x_Homebase" id="s_x_Homebase" value="<?php echo $teacher->Homebase->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Homebase">
		<td><span id="elh_teacher_Homebase"><?php echo $teacher->Homebase->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Homebase" id="z_Homebase" value="LIKE"></span></td>
		<td<?php echo $teacher->Homebase->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_Homebase">
<select data-table="teacher" data-field="x_Homebase" data-page="3" data-value-separator="<?php echo $teacher->Homebase->DisplayValueSeparatorAttribute() ?>" id="x_Homebase" name="x_Homebase"<?php echo $teacher->Homebase->EditAttributes() ?>>
<?php echo $teacher->Homebase->SelectOptionListHtml("x_Homebase") ?>
</select>
<input type="hidden" name="s_x_Homebase" id="s_x_Homebase" value="<?php echo $teacher->Homebase->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_ProdiID"><?php echo $teacher->ProdiID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->ProdiID->CellAttributes() ?>>
			<span id="el_teacher_ProdiID">
<div id="tp_x_ProdiID" class="ewTemplate"><input type="checkbox" data-table="teacher" data-field="x_ProdiID" data-page="3" data-value-separator="<?php echo $teacher->ProdiID->DisplayValueSeparatorAttribute() ?>" name="x_ProdiID[]" id="x_ProdiID[]" value="{value}"<?php echo $teacher->ProdiID->EditAttributes() ?>></div>
<div id="dsl_x_ProdiID" data-repeatcolumn="3" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->ProdiID->CheckBoxListHtml(FALSE, "x_ProdiID[]", 3) ?>
</div></div>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $teacher->ProdiID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_teacher_ProdiID"><?php echo $teacher->ProdiID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></span></td>
		<td<?php echo $teacher->ProdiID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_ProdiID">
<div id="tp_x_ProdiID" class="ewTemplate"><input type="checkbox" data-table="teacher" data-field="x_ProdiID" data-page="3" data-value-separator="<?php echo $teacher->ProdiID->DisplayValueSeparatorAttribute() ?>" name="x_ProdiID[]" id="x_ProdiID[]" value="{value}"<?php echo $teacher->ProdiID->EditAttributes() ?>></div>
<div id="dsl_x_ProdiID" data-repeatcolumn="3" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->ProdiID->CheckBoxListHtml(FALSE, "x_ProdiID[]", 3) ?>
</div></div>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $teacher->ProdiID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->Keilmuan->Visible) { // Keilmuan ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_Keilmuan" class="form-group">
		<label for="x_Keilmuan" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_Keilmuan"><?php echo $teacher->Keilmuan->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Keilmuan" id="z_Keilmuan" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->Keilmuan->CellAttributes() ?>>
			<span id="el_teacher_Keilmuan">
<input type="text" data-table="teacher" data-field="x_Keilmuan" data-page="3" name="x_Keilmuan" id="x_Keilmuan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->Keilmuan->getPlaceHolder()) ?>" value="<?php echo $teacher->Keilmuan->EditValue ?>"<?php echo $teacher->Keilmuan->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Keilmuan">
		<td><span id="elh_teacher_Keilmuan"><?php echo $teacher->Keilmuan->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Keilmuan" id="z_Keilmuan" value="LIKE"></span></td>
		<td<?php echo $teacher->Keilmuan->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_Keilmuan">
<input type="text" data-table="teacher" data-field="x_Keilmuan" data-page="3" name="x_Keilmuan" id="x_Keilmuan" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->Keilmuan->getPlaceHolder()) ?>" value="<?php echo $teacher->Keilmuan->EditValue ?>"<?php echo $teacher->Keilmuan->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($teacher->LulusanPT->Visible) { // LulusanPT ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_LulusanPT" class="form-group">
		<label for="x_LulusanPT" class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_LulusanPT"><?php echo $teacher->LulusanPT->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_LulusanPT" id="z_LulusanPT" value="LIKE"></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->LulusanPT->CellAttributes() ?>>
			<span id="el_teacher_LulusanPT">
<input type="text" data-table="teacher" data-field="x_LulusanPT" data-page="3" name="x_LulusanPT" id="x_LulusanPT" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->LulusanPT->getPlaceHolder()) ?>" value="<?php echo $teacher->LulusanPT->EditValue ?>"<?php echo $teacher->LulusanPT->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_LulusanPT">
		<td><span id="elh_teacher_LulusanPT"><?php echo $teacher->LulusanPT->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_LulusanPT" id="z_LulusanPT" value="LIKE"></span></td>
		<td<?php echo $teacher->LulusanPT->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_LulusanPT">
<input type="text" data-table="teacher" data-field="x_LulusanPT" data-page="3" name="x_LulusanPT" id="x_LulusanPT" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($teacher->LulusanPT->getPlaceHolder()) ?>" value="<?php echo $teacher->LulusanPT->EditValue ?>"<?php echo $teacher->LulusanPT->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $teacher_search->MultiPages->PageStyle("4") ?>" id="tab_teacher4">
		</div>
		<div class="tab-pane<?php echo $teacher_search->MultiPages->PageStyle("5") ?>" id="tab_teacher5">
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_teachersearch5" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($teacher->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $teacher_search->SearchLabelClass ?>"><span id="elh_teacher_NA"><?php echo $teacher->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $teacher_search->SearchRightColumnClass ?>"><div<?php echo $teacher->NA->CellAttributes() ?>>
			<span id="el_teacher_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="teacher" data-field="x_NA" data-page="5" data-value-separator="<?php echo $teacher->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $teacher->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->NA->RadioButtonListHtml(FALSE, "x_NA", 5) ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_teacher_NA"><?php echo $teacher->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $teacher->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_teacher_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="teacher" data-field="x_NA" data-page="5" data-value-separator="<?php echo $teacher->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $teacher->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $teacher->NA->RadioButtonListHtml(FALSE, "x_NA", 5) ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $teacher_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
	</div>
</div>
</div>
<?php if (!$teacher_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$teacher_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fteachersearch.Init();
</script>
<?php
$teacher_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$teacher_search->Page_Terminate();
?>
