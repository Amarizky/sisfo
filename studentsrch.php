<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "studentinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$student_search = NULL; // Initialize page object first

class cstudent_search extends cstudent {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'student';

	// Page object name
	var $PageObjName = 'student_search';

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

		// Table object (student)
		if (!isset($GLOBALS["student"]) || get_class($GLOBALS["student"]) == "cstudent") {
			$GLOBALS["student"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["student"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'student', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("studentlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->StudentID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->LevelID->SetVisibility();
		$this->ProdiID->SetVisibility();
		$this->StudentStatusID->SetVisibility();
		$this->TahunID->SetVisibility();
		$this->Foto->SetVisibility();
		$this->NIK->SetVisibility();
		$this->WargaNegara->SetVisibility();
		$this->Kelamin->SetVisibility();
		$this->TempatLahir->SetVisibility();
		$this->TanggalLahir->SetVisibility();
		$this->AgamaID->SetVisibility();
		$this->Darah->SetVisibility();
		$this->StatusSipil->SetVisibility();
		$this->AlamatDomisili->SetVisibility();
		$this->RT->SetVisibility();
		$this->RW->SetVisibility();
		$this->KodePos->SetVisibility();
		$this->ProvinsiID->SetVisibility();
		$this->KabupatenKotaID->SetVisibility();
		$this->KecamatanID->SetVisibility();
		$this->DesaID->SetVisibility();
		$this->AnakKe->SetVisibility();
		$this->JumlahSaudara->SetVisibility();
		$this->Telepon->SetVisibility();
		$this->_Email->SetVisibility();
		$this->NamaAyah->SetVisibility();
		$this->AgamaAyah->SetVisibility();
		$this->PendidikanAyah->SetVisibility();
		$this->PekerjaanAyah->SetVisibility();
		$this->HidupAyah->SetVisibility();
		$this->NamaIbu->SetVisibility();
		$this->AgamaIbu->SetVisibility();
		$this->PendidikanIbu->SetVisibility();
		$this->PekerjaanIbu->SetVisibility();
		$this->HidupIbu->SetVisibility();
		$this->AlamatOrtu->SetVisibility();
		$this->RTOrtu->SetVisibility();
		$this->RWOrtu->SetVisibility();
		$this->KodePosOrtu->SetVisibility();
		$this->ProvinsiIDOrtu->SetVisibility();
		$this->KabupatenIDOrtu->SetVisibility();
		$this->KecamatanIDOrtu->SetVisibility();
		$this->DesaIDOrtu->SetVisibility();
		$this->NegaraIDOrtu->SetVisibility();
		$this->TeleponOrtu->SetVisibility();
		$this->HandphoneOrtu->SetVisibility();
		$this->EmailOrtu->SetVisibility();
		$this->AsalSekolah->SetVisibility();
		$this->AlamatSekolah->SetVisibility();
		$this->ProvinsiIDSekolah->SetVisibility();
		$this->KabupatenIDSekolah->SetVisibility();
		$this->KecamatanIDSekolah->SetVisibility();
		$this->DesaIDSekolah->SetVisibility();
		$this->NilaiSekolah->SetVisibility();
		$this->TahunLulus->SetVisibility();
		$this->IjazahSekolah->SetVisibility();
		$this->TglIjazah->SetVisibility();
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
		global $EW_EXPORT, $student;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($student);
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
						$sSrchStr = "studentlist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->StudentID); // StudentID
		$this->BuildSearchUrl($sSrchUrl, $this->Nama); // Nama
		$this->BuildSearchUrl($sSrchUrl, $this->LevelID); // LevelID
		$this->BuildSearchUrl($sSrchUrl, $this->ProdiID); // ProdiID
		$this->BuildSearchUrl($sSrchUrl, $this->StudentStatusID); // StudentStatusID
		$this->BuildSearchUrl($sSrchUrl, $this->TahunID); // TahunID
		$this->BuildSearchUrl($sSrchUrl, $this->Foto); // Foto
		$this->BuildSearchUrl($sSrchUrl, $this->NIK); // NIK
		$this->BuildSearchUrl($sSrchUrl, $this->WargaNegara); // WargaNegara
		$this->BuildSearchUrl($sSrchUrl, $this->Kelamin); // Kelamin
		$this->BuildSearchUrl($sSrchUrl, $this->TempatLahir); // TempatLahir
		$this->BuildSearchUrl($sSrchUrl, $this->TanggalLahir); // TanggalLahir
		$this->BuildSearchUrl($sSrchUrl, $this->AgamaID); // AgamaID
		$this->BuildSearchUrl($sSrchUrl, $this->Darah); // Darah
		$this->BuildSearchUrl($sSrchUrl, $this->StatusSipil); // StatusSipil
		$this->BuildSearchUrl($sSrchUrl, $this->AlamatDomisili); // AlamatDomisili
		$this->BuildSearchUrl($sSrchUrl, $this->RT); // RT
		$this->BuildSearchUrl($sSrchUrl, $this->RW); // RW
		$this->BuildSearchUrl($sSrchUrl, $this->KodePos); // KodePos
		$this->BuildSearchUrl($sSrchUrl, $this->ProvinsiID); // ProvinsiID
		$this->BuildSearchUrl($sSrchUrl, $this->KabupatenKotaID); // KabupatenKotaID
		$this->BuildSearchUrl($sSrchUrl, $this->KecamatanID); // KecamatanID
		$this->BuildSearchUrl($sSrchUrl, $this->DesaID); // DesaID
		$this->BuildSearchUrl($sSrchUrl, $this->AnakKe); // AnakKe
		$this->BuildSearchUrl($sSrchUrl, $this->JumlahSaudara); // JumlahSaudara
		$this->BuildSearchUrl($sSrchUrl, $this->Telepon); // Telepon
		$this->BuildSearchUrl($sSrchUrl, $this->_Email); // Email
		$this->BuildSearchUrl($sSrchUrl, $this->NamaAyah); // NamaAyah
		$this->BuildSearchUrl($sSrchUrl, $this->AgamaAyah); // AgamaAyah
		$this->BuildSearchUrl($sSrchUrl, $this->PendidikanAyah); // PendidikanAyah
		$this->BuildSearchUrl($sSrchUrl, $this->PekerjaanAyah); // PekerjaanAyah
		$this->BuildSearchUrl($sSrchUrl, $this->HidupAyah); // HidupAyah
		$this->BuildSearchUrl($sSrchUrl, $this->NamaIbu); // NamaIbu
		$this->BuildSearchUrl($sSrchUrl, $this->AgamaIbu); // AgamaIbu
		$this->BuildSearchUrl($sSrchUrl, $this->PendidikanIbu); // PendidikanIbu
		$this->BuildSearchUrl($sSrchUrl, $this->PekerjaanIbu); // PekerjaanIbu
		$this->BuildSearchUrl($sSrchUrl, $this->HidupIbu); // HidupIbu
		$this->BuildSearchUrl($sSrchUrl, $this->AlamatOrtu); // AlamatOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->RTOrtu); // RTOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->RWOrtu); // RWOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->KodePosOrtu); // KodePosOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->ProvinsiIDOrtu); // ProvinsiIDOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->KabupatenIDOrtu); // KabupatenIDOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->KecamatanIDOrtu); // KecamatanIDOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->DesaIDOrtu); // DesaIDOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->NegaraIDOrtu); // NegaraIDOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->TeleponOrtu); // TeleponOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->HandphoneOrtu); // HandphoneOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->EmailOrtu); // EmailOrtu
		$this->BuildSearchUrl($sSrchUrl, $this->AsalSekolah); // AsalSekolah
		$this->BuildSearchUrl($sSrchUrl, $this->AlamatSekolah); // AlamatSekolah
		$this->BuildSearchUrl($sSrchUrl, $this->ProvinsiIDSekolah); // ProvinsiIDSekolah
		$this->BuildSearchUrl($sSrchUrl, $this->KabupatenIDSekolah); // KabupatenIDSekolah
		$this->BuildSearchUrl($sSrchUrl, $this->KecamatanIDSekolah); // KecamatanIDSekolah
		$this->BuildSearchUrl($sSrchUrl, $this->DesaIDSekolah); // DesaIDSekolah
		$this->BuildSearchUrl($sSrchUrl, $this->NilaiSekolah); // NilaiSekolah
		$this->BuildSearchUrl($sSrchUrl, $this->TahunLulus); // TahunLulus
		$this->BuildSearchUrl($sSrchUrl, $this->IjazahSekolah); // IjazahSekolah
		$this->BuildSearchUrl($sSrchUrl, $this->TglIjazah); // TglIjazah
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
		// StudentID

		$this->StudentID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StudentID"));
		$this->StudentID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StudentID");

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Nama"));
		$this->Nama->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Nama");

		// LevelID
		$this->LevelID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_LevelID"));
		$this->LevelID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_LevelID");

		// ProdiID
		$this->ProdiID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ProdiID"));
		$this->ProdiID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ProdiID");

		// StudentStatusID
		$this->StudentStatusID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StudentStatusID"));
		$this->StudentStatusID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StudentStatusID");

		// TahunID
		$this->TahunID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TahunID"));
		$this->TahunID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TahunID");

		// Foto
		$this->Foto->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Foto"));
		$this->Foto->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Foto");

		// NIK
		$this->NIK->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NIK"));
		$this->NIK->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NIK");

		// WargaNegara
		$this->WargaNegara->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_WargaNegara"));
		$this->WargaNegara->AdvancedSearch->SearchOperator = $objForm->GetValue("z_WargaNegara");

		// Kelamin
		$this->Kelamin->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Kelamin"));
		$this->Kelamin->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Kelamin");

		// TempatLahir
		$this->TempatLahir->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TempatLahir"));
		$this->TempatLahir->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TempatLahir");

		// TanggalLahir
		$this->TanggalLahir->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TanggalLahir"));
		$this->TanggalLahir->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TanggalLahir");

		// AgamaID
		$this->AgamaID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_AgamaID"));
		$this->AgamaID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_AgamaID");

		// Darah
		$this->Darah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Darah"));
		$this->Darah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Darah");

		// StatusSipil
		$this->StatusSipil->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StatusSipil"));
		$this->StatusSipil->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StatusSipil");

		// AlamatDomisili
		$this->AlamatDomisili->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_AlamatDomisili"));
		$this->AlamatDomisili->AdvancedSearch->SearchOperator = $objForm->GetValue("z_AlamatDomisili");

		// RT
		$this->RT->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_RT"));
		$this->RT->AdvancedSearch->SearchOperator = $objForm->GetValue("z_RT");

		// RW
		$this->RW->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_RW"));
		$this->RW->AdvancedSearch->SearchOperator = $objForm->GetValue("z_RW");

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

		// AnakKe
		$this->AnakKe->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_AnakKe"));
		$this->AnakKe->AdvancedSearch->SearchOperator = $objForm->GetValue("z_AnakKe");

		// JumlahSaudara
		$this->JumlahSaudara->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_JumlahSaudara"));
		$this->JumlahSaudara->AdvancedSearch->SearchOperator = $objForm->GetValue("z_JumlahSaudara");

		// Telepon
		$this->Telepon->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Telepon"));
		$this->Telepon->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Telepon");

		// Email
		$this->_Email->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x__Email"));
		$this->_Email->AdvancedSearch->SearchOperator = $objForm->GetValue("z__Email");

		// NamaAyah
		$this->NamaAyah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NamaAyah"));
		$this->NamaAyah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NamaAyah");

		// AgamaAyah
		$this->AgamaAyah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_AgamaAyah"));
		$this->AgamaAyah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_AgamaAyah");

		// PendidikanAyah
		$this->PendidikanAyah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_PendidikanAyah"));
		$this->PendidikanAyah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_PendidikanAyah");

		// PekerjaanAyah
		$this->PekerjaanAyah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_PekerjaanAyah"));
		$this->PekerjaanAyah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_PekerjaanAyah");

		// HidupAyah
		$this->HidupAyah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_HidupAyah"));
		$this->HidupAyah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_HidupAyah");

		// NamaIbu
		$this->NamaIbu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NamaIbu"));
		$this->NamaIbu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NamaIbu");

		// AgamaIbu
		$this->AgamaIbu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_AgamaIbu"));
		$this->AgamaIbu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_AgamaIbu");

		// PendidikanIbu
		$this->PendidikanIbu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_PendidikanIbu"));
		$this->PendidikanIbu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_PendidikanIbu");

		// PekerjaanIbu
		$this->PekerjaanIbu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_PekerjaanIbu"));
		$this->PekerjaanIbu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_PekerjaanIbu");

		// HidupIbu
		$this->HidupIbu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_HidupIbu"));
		$this->HidupIbu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_HidupIbu");

		// AlamatOrtu
		$this->AlamatOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_AlamatOrtu"));
		$this->AlamatOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_AlamatOrtu");

		// RTOrtu
		$this->RTOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_RTOrtu"));
		$this->RTOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_RTOrtu");

		// RWOrtu
		$this->RWOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_RWOrtu"));
		$this->RWOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_RWOrtu");

		// KodePosOrtu
		$this->KodePosOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KodePosOrtu"));
		$this->KodePosOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KodePosOrtu");

		// ProvinsiIDOrtu
		$this->ProvinsiIDOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ProvinsiIDOrtu"));
		$this->ProvinsiIDOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ProvinsiIDOrtu");

		// KabupatenIDOrtu
		$this->KabupatenIDOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KabupatenIDOrtu"));
		$this->KabupatenIDOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KabupatenIDOrtu");

		// KecamatanIDOrtu
		$this->KecamatanIDOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KecamatanIDOrtu"));
		$this->KecamatanIDOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KecamatanIDOrtu");

		// DesaIDOrtu
		$this->DesaIDOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_DesaIDOrtu"));
		$this->DesaIDOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_DesaIDOrtu");

		// NegaraIDOrtu
		$this->NegaraIDOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NegaraIDOrtu"));
		$this->NegaraIDOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NegaraIDOrtu");

		// TeleponOrtu
		$this->TeleponOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TeleponOrtu"));
		$this->TeleponOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TeleponOrtu");

		// HandphoneOrtu
		$this->HandphoneOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_HandphoneOrtu"));
		$this->HandphoneOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_HandphoneOrtu");

		// EmailOrtu
		$this->EmailOrtu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_EmailOrtu"));
		$this->EmailOrtu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_EmailOrtu");

		// AsalSekolah
		$this->AsalSekolah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_AsalSekolah"));
		$this->AsalSekolah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_AsalSekolah");

		// AlamatSekolah
		$this->AlamatSekolah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_AlamatSekolah"));
		$this->AlamatSekolah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_AlamatSekolah");

		// ProvinsiIDSekolah
		$this->ProvinsiIDSekolah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ProvinsiIDSekolah"));
		$this->ProvinsiIDSekolah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ProvinsiIDSekolah");

		// KabupatenIDSekolah
		$this->KabupatenIDSekolah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KabupatenIDSekolah"));
		$this->KabupatenIDSekolah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KabupatenIDSekolah");

		// KecamatanIDSekolah
		$this->KecamatanIDSekolah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KecamatanIDSekolah"));
		$this->KecamatanIDSekolah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KecamatanIDSekolah");

		// DesaIDSekolah
		$this->DesaIDSekolah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_DesaIDSekolah"));
		$this->DesaIDSekolah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_DesaIDSekolah");

		// NilaiSekolah
		$this->NilaiSekolah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NilaiSekolah"));
		$this->NilaiSekolah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NilaiSekolah");

		// TahunLulus
		$this->TahunLulus->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TahunLulus"));
		$this->TahunLulus->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TahunLulus");

		// IjazahSekolah
		$this->IjazahSekolah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_IjazahSekolah"));
		$this->IjazahSekolah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_IjazahSekolah");

		// TglIjazah
		$this->TglIjazah->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TglIjazah"));
		$this->TglIjazah->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TglIjazah");

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
		// StudentID
		// Nama
		// LevelID
		// Password
		// KampusID
		// ProdiID
		// StudentStatusID
		// TahunID
		// Foto
		// NIK
		// WargaNegara
		// Kelamin
		// TempatLahir
		// TanggalLahir
		// AgamaID
		// Darah
		// StatusSipil
		// AlamatDomisili
		// RT
		// RW
		// KodePos
		// ProvinsiID
		// KabupatenKotaID
		// KecamatanID
		// DesaID
		// AnakKe
		// JumlahSaudara
		// Telepon
		// Handphone
		// Email
		// NamaAyah
		// AgamaAyah
		// PendidikanAyah
		// PekerjaanAyah
		// HidupAyah
		// NamaIbu
		// AgamaIbu
		// PendidikanIbu
		// PekerjaanIbu
		// HidupIbu
		// AlamatOrtu
		// RTOrtu
		// RWOrtu
		// KodePosOrtu
		// ProvinsiIDOrtu
		// KabupatenIDOrtu
		// KecamatanIDOrtu
		// DesaIDOrtu
		// NegaraIDOrtu
		// TeleponOrtu
		// HandphoneOrtu
		// EmailOrtu
		// AsalSekolah
		// AlamatSekolah
		// ProvinsiIDSekolah
		// KabupatenIDSekolah
		// KecamatanIDSekolah
		// DesaIDSekolah
		// NilaiSekolah
		// TahunLulus
		// IjazahSekolah
		// TglIjazah
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// LockStatus
		// LockDate
		// VerifiedBy
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// StudentID
		$this->StudentID->ViewValue = $this->StudentID->CurrentValue;
		$this->StudentID->CssStyle = "font-weight: bold;";
		$this->StudentID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->CssStyle = "font-weight: bold;";
		$this->Nama->ViewCustomAttributes = "";

		// LevelID
		$this->LevelID->ViewValue = $this->LevelID->CurrentValue;
		$this->LevelID->ViewCustomAttributes = "";

		// KampusID
		if (strval($this->KampusID->CurrentValue) <> "") {
			$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->KampusID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
		$sWhereWrk = "";
		$this->KampusID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KampusID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KampusID->ViewValue = $this->KampusID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KampusID->ViewValue = $this->KampusID->CurrentValue;
			}
		} else {
			$this->KampusID->ViewValue = NULL;
		}
		$this->KampusID->ViewCustomAttributes = "";

		// ProdiID
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->ProdiID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->ProdiID->ViewValue = $this->ProdiID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
			}
		} else {
			$this->ProdiID->ViewValue = NULL;
		}
		$this->ProdiID->ViewCustomAttributes = "";

		// StudentStatusID
		if (strval($this->StudentStatusID->CurrentValue) <> "") {
			$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StudentStatusID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statusstudent`";
		$sWhereWrk = "";
		$this->StudentStatusID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StudentStatusID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StudentStatusID->ViewValue = $this->StudentStatusID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StudentStatusID->ViewValue = $this->StudentStatusID->CurrentValue;
			}
		} else {
			$this->StudentStatusID->ViewValue = NULL;
		}
		$this->StudentStatusID->ViewCustomAttributes = "";

		// TahunID
		$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
		$this->TahunID->ViewCustomAttributes = "";

		// Foto
		$this->Foto->UploadPath = "upload";
		if (!ew_Empty($this->Foto->Upload->DbValue)) {
			$this->Foto->ImageAlt = $this->Foto->FldAlt();
			$this->Foto->ViewValue = $this->Foto->Upload->DbValue;
		} else {
			$this->Foto->ViewValue = "";
		}
		$this->Foto->ViewCustomAttributes = "";

		// NIK
		$this->NIK->ViewValue = $this->NIK->CurrentValue;
		$this->NIK->ViewCustomAttributes = "";

		// WargaNegara
		if (strval($this->WargaNegara->CurrentValue) <> "") {
			$this->WargaNegara->ViewValue = $this->WargaNegara->OptionCaption($this->WargaNegara->CurrentValue);
		} else {
			$this->WargaNegara->ViewValue = NULL;
		}
		$this->WargaNegara->ViewCustomAttributes = "";

		// Kelamin
		if (strval($this->Kelamin->CurrentValue) <> "") {
			$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->Kelamin->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
		$sWhereWrk = "";
		$this->Kelamin->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Kelamin, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Kelamin->ViewValue = $this->Kelamin->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Kelamin->ViewValue = $this->Kelamin->CurrentValue;
			}
		} else {
			$this->Kelamin->ViewValue = NULL;
		}
		$this->Kelamin->ViewCustomAttributes = "";

		// TempatLahir
		$this->TempatLahir->ViewValue = $this->TempatLahir->CurrentValue;
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

		// Darah
		if (strval($this->Darah->CurrentValue) <> "") {
			$sFilterWrk = "`DarahID`" . ew_SearchString("=", $this->Darah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `DarahID`, `DarahID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_darah`";
		$sWhereWrk = "";
		$this->Darah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Darah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Darah->ViewValue = $this->Darah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Darah->ViewValue = $this->Darah->CurrentValue;
			}
		} else {
			$this->Darah->ViewValue = NULL;
		}
		$this->Darah->ViewCustomAttributes = "";

		// StatusSipil
		if (strval($this->StatusSipil->CurrentValue) <> "") {
			$sFilterWrk = "`StatusSipil`" . ew_SearchString("=", $this->StatusSipil->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusSipil`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statussipil`";
		$sWhereWrk = "";
		$this->StatusSipil->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StatusSipil, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StatusSipil->ViewValue = $this->StatusSipil->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StatusSipil->ViewValue = $this->StatusSipil->CurrentValue;
			}
		} else {
			$this->StatusSipil->ViewValue = NULL;
		}
		$this->StatusSipil->ViewCustomAttributes = "";

		// AlamatDomisili
		$this->AlamatDomisili->ViewValue = $this->AlamatDomisili->CurrentValue;
		$this->AlamatDomisili->ViewCustomAttributes = "";

		// RT
		$this->RT->ViewValue = $this->RT->CurrentValue;
		$this->RT->ViewCustomAttributes = "";

		// RW
		$this->RW->ViewValue = $this->RW->CurrentValue;
		$this->RW->ViewCustomAttributes = "";

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

		// AnakKe
		$this->AnakKe->ViewValue = $this->AnakKe->CurrentValue;
		$this->AnakKe->ViewCustomAttributes = "";

		// JumlahSaudara
		$this->JumlahSaudara->ViewValue = $this->JumlahSaudara->CurrentValue;
		$this->JumlahSaudara->ViewCustomAttributes = "";

		// Telepon
		$this->Telepon->ViewValue = $this->Telepon->CurrentValue;
		$this->Telepon->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// NamaAyah
		$this->NamaAyah->ViewValue = $this->NamaAyah->CurrentValue;
		$this->NamaAyah->ViewCustomAttributes = "";

		// AgamaAyah
		if (strval($this->AgamaAyah->CurrentValue) <> "") {
			$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaAyah->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
		$sWhereWrk = "";
		$this->AgamaAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->AgamaAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->AgamaAyah->ViewValue = $this->AgamaAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->AgamaAyah->ViewValue = $this->AgamaAyah->CurrentValue;
			}
		} else {
			$this->AgamaAyah->ViewValue = NULL;
		}
		$this->AgamaAyah->ViewCustomAttributes = "";

		// PendidikanAyah
		if (strval($this->PendidikanAyah->CurrentValue) <> "") {
			$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanAyah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
		$sWhereWrk = "";
		$this->PendidikanAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PendidikanAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PendidikanAyah->ViewValue = $this->PendidikanAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PendidikanAyah->ViewValue = $this->PendidikanAyah->CurrentValue;
			}
		} else {
			$this->PendidikanAyah->ViewValue = NULL;
		}
		$this->PendidikanAyah->ViewCustomAttributes = "";

		// PekerjaanAyah
		if (strval($this->PekerjaanAyah->CurrentValue) <> "") {
			$sFilterWrk = "`Pekerjaan`" . ew_SearchString("=", $this->PekerjaanAyah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pekerjaan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pekerjaanortu`";
		$sWhereWrk = "";
		$this->PekerjaanAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PekerjaanAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PekerjaanAyah->ViewValue = $this->PekerjaanAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PekerjaanAyah->ViewValue = $this->PekerjaanAyah->CurrentValue;
			}
		} else {
			$this->PekerjaanAyah->ViewValue = NULL;
		}
		$this->PekerjaanAyah->ViewCustomAttributes = "";

		// HidupAyah
		if (strval($this->HidupAyah->CurrentValue) <> "") {
			$sFilterWrk = "`Hidup`" . ew_SearchString("=", $this->HidupAyah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Hidup`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hidup`";
		$sWhereWrk = "";
		$this->HidupAyah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->HidupAyah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->HidupAyah->ViewValue = $this->HidupAyah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->HidupAyah->ViewValue = $this->HidupAyah->CurrentValue;
			}
		} else {
			$this->HidupAyah->ViewValue = NULL;
		}
		$this->HidupAyah->ViewCustomAttributes = "";

		// NamaIbu
		$this->NamaIbu->ViewValue = $this->NamaIbu->CurrentValue;
		$this->NamaIbu->ViewCustomAttributes = "";

		// AgamaIbu
		if (strval($this->AgamaIbu->CurrentValue) <> "") {
			$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaIbu->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
		$sWhereWrk = "";
		$this->AgamaIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->AgamaIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->AgamaIbu->ViewValue = $this->AgamaIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->AgamaIbu->ViewValue = $this->AgamaIbu->CurrentValue;
			}
		} else {
			$this->AgamaIbu->ViewValue = NULL;
		}
		$this->AgamaIbu->ViewCustomAttributes = "";

		// PendidikanIbu
		if (strval($this->PendidikanIbu->CurrentValue) <> "") {
			$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanIbu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
		$sWhereWrk = "";
		$this->PendidikanIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PendidikanIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PendidikanIbu->ViewValue = $this->PendidikanIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PendidikanIbu->ViewValue = $this->PendidikanIbu->CurrentValue;
			}
		} else {
			$this->PendidikanIbu->ViewValue = NULL;
		}
		$this->PendidikanIbu->ViewCustomAttributes = "";

		// PekerjaanIbu
		if (strval($this->PekerjaanIbu->CurrentValue) <> "") {
			$sFilterWrk = "`Pekerjaan`" . ew_SearchString("=", $this->PekerjaanIbu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Pekerjaan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pekerjaanortu`";
		$sWhereWrk = "";
		$this->PekerjaanIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PekerjaanIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->PekerjaanIbu->ViewValue = $this->PekerjaanIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PekerjaanIbu->ViewValue = $this->PekerjaanIbu->CurrentValue;
			}
		} else {
			$this->PekerjaanIbu->ViewValue = NULL;
		}
		$this->PekerjaanIbu->ViewCustomAttributes = "";

		// HidupIbu
		if (strval($this->HidupIbu->CurrentValue) <> "") {
			$sFilterWrk = "`Hidup`" . ew_SearchString("=", $this->HidupIbu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `Hidup`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hidup`";
		$sWhereWrk = "";
		$this->HidupIbu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->HidupIbu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->HidupIbu->ViewValue = $this->HidupIbu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->HidupIbu->ViewValue = $this->HidupIbu->CurrentValue;
			}
		} else {
			$this->HidupIbu->ViewValue = NULL;
		}
		$this->HidupIbu->ViewCustomAttributes = "";

		// AlamatOrtu
		$this->AlamatOrtu->ViewValue = $this->AlamatOrtu->CurrentValue;
		$this->AlamatOrtu->ViewCustomAttributes = "";

		// RTOrtu
		$this->RTOrtu->ViewValue = $this->RTOrtu->CurrentValue;
		$this->RTOrtu->ViewCustomAttributes = "";

		// RWOrtu
		$this->RWOrtu->ViewValue = $this->RWOrtu->CurrentValue;
		$this->RWOrtu->ViewCustomAttributes = "";

		// KodePosOrtu
		$this->KodePosOrtu->ViewValue = $this->KodePosOrtu->CurrentValue;
		$this->KodePosOrtu->ViewCustomAttributes = "";

		// ProvinsiIDOrtu
		if (strval($this->ProvinsiIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
		$sWhereWrk = "";
		$this->ProvinsiIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProvinsiIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->ProvinsiIDOrtu->ViewValue = $this->ProvinsiIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->ProvinsiIDOrtu->ViewValue = $this->ProvinsiIDOrtu->CurrentValue;
			}
		} else {
			$this->ProvinsiIDOrtu->ViewValue = NULL;
		}
		$this->ProvinsiIDOrtu->ViewCustomAttributes = "";

		// KabupatenIDOrtu
		if (strval($this->KabupatenIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->KabupatenIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KabupatenIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KabupatenIDOrtu->ViewValue = $this->KabupatenIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KabupatenIDOrtu->ViewValue = $this->KabupatenIDOrtu->CurrentValue;
			}
		} else {
			$this->KabupatenIDOrtu->ViewValue = NULL;
		}
		$this->KabupatenIDOrtu->ViewCustomAttributes = "";

		// KecamatanIDOrtu
		if (strval($this->KecamatanIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
		$sWhereWrk = "";
		$this->KecamatanIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KecamatanIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KecamatanIDOrtu->ViewValue = $this->KecamatanIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KecamatanIDOrtu->ViewValue = $this->KecamatanIDOrtu->CurrentValue;
			}
		} else {
			$this->KecamatanIDOrtu->ViewValue = NULL;
		}
		$this->KecamatanIDOrtu->ViewCustomAttributes = "";

		// DesaIDOrtu
		if (strval($this->DesaIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
		$sWhereWrk = "";
		$this->DesaIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->DesaIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->DesaIDOrtu->ViewValue = $this->DesaIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->DesaIDOrtu->ViewValue = $this->DesaIDOrtu->CurrentValue;
			}
		} else {
			$this->DesaIDOrtu->ViewValue = NULL;
		}
		$this->DesaIDOrtu->ViewCustomAttributes = "";

		// NegaraIDOrtu
		if (strval($this->NegaraIDOrtu->CurrentValue) <> "") {
			$sFilterWrk = "`NegaraID`" . ew_SearchString("=", $this->NegaraIDOrtu->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `NegaraID`, `NamaNegara` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_negara`";
		$sWhereWrk = "";
		$this->NegaraIDOrtu->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->NegaraIDOrtu, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->NegaraIDOrtu->ViewValue = $this->NegaraIDOrtu->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->NegaraIDOrtu->ViewValue = $this->NegaraIDOrtu->CurrentValue;
			}
		} else {
			$this->NegaraIDOrtu->ViewValue = NULL;
		}
		$this->NegaraIDOrtu->ViewCustomAttributes = "";

		// TeleponOrtu
		$this->TeleponOrtu->ViewValue = $this->TeleponOrtu->CurrentValue;
		$this->TeleponOrtu->ViewCustomAttributes = "";

		// HandphoneOrtu
		$this->HandphoneOrtu->ViewValue = $this->HandphoneOrtu->CurrentValue;
		$this->HandphoneOrtu->ViewCustomAttributes = "";

		// EmailOrtu
		$this->EmailOrtu->ViewValue = $this->EmailOrtu->CurrentValue;
		$this->EmailOrtu->ViewCustomAttributes = "";

		// AsalSekolah
		$this->AsalSekolah->ViewValue = $this->AsalSekolah->CurrentValue;
		$this->AsalSekolah->ViewCustomAttributes = "";

		// AlamatSekolah
		$this->AlamatSekolah->ViewValue = $this->AlamatSekolah->CurrentValue;
		$this->AlamatSekolah->ViewCustomAttributes = "";

		// ProvinsiIDSekolah
		if (strval($this->ProvinsiIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
		$sWhereWrk = "";
		$this->ProvinsiIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProvinsiIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->ProvinsiIDSekolah->ViewValue = $this->ProvinsiIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->ProvinsiIDSekolah->ViewValue = $this->ProvinsiIDSekolah->CurrentValue;
			}
		} else {
			$this->ProvinsiIDSekolah->ViewValue = NULL;
		}
		$this->ProvinsiIDSekolah->ViewCustomAttributes = "";

		// KabupatenIDSekolah
		if (strval($this->KabupatenIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
		$sWhereWrk = "";
		$this->KabupatenIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KabupatenIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KabupatenIDSekolah->ViewValue = $this->KabupatenIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KabupatenIDSekolah->ViewValue = $this->KabupatenIDSekolah->CurrentValue;
			}
		} else {
			$this->KabupatenIDSekolah->ViewValue = NULL;
		}
		$this->KabupatenIDSekolah->ViewCustomAttributes = "";

		// KecamatanIDSekolah
		if (strval($this->KecamatanIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
		$sWhereWrk = "";
		$this->KecamatanIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KecamatanIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KecamatanIDSekolah->ViewValue = $this->KecamatanIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KecamatanIDSekolah->ViewValue = $this->KecamatanIDSekolah->CurrentValue;
			}
		} else {
			$this->KecamatanIDSekolah->ViewValue = NULL;
		}
		$this->KecamatanIDSekolah->ViewCustomAttributes = "";

		// DesaIDSekolah
		if (strval($this->DesaIDSekolah->CurrentValue) <> "") {
			$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaIDSekolah->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
		$sWhereWrk = "";
		$this->DesaIDSekolah->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->DesaIDSekolah, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->DesaIDSekolah->ViewValue = $this->DesaIDSekolah->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->DesaIDSekolah->ViewValue = $this->DesaIDSekolah->CurrentValue;
			}
		} else {
			$this->DesaIDSekolah->ViewValue = NULL;
		}
		$this->DesaIDSekolah->ViewCustomAttributes = "";

		// NilaiSekolah
		$this->NilaiSekolah->ViewValue = $this->NilaiSekolah->CurrentValue;
		$this->NilaiSekolah->ViewCustomAttributes = "";

		// TahunLulus
		$this->TahunLulus->ViewValue = $this->TahunLulus->CurrentValue;
		$this->TahunLulus->ViewCustomAttributes = "";

		// IjazahSekolah
		$this->IjazahSekolah->ViewValue = $this->IjazahSekolah->CurrentValue;
		$this->IjazahSekolah->ViewCustomAttributes = "";

		// TglIjazah
		$this->TglIjazah->ViewValue = $this->TglIjazah->CurrentValue;
		$this->TglIjazah->ViewValue = ew_FormatDateTime($this->TglIjazah->ViewValue, 0);
		$this->TglIjazah->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Ya";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "Tidak";
		}
		$this->NA->ViewCustomAttributes = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";
			$this->StudentID->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// LevelID
			$this->LevelID->LinkCustomAttributes = "";
			$this->LevelID->HrefValue = "";
			$this->LevelID->TooltipValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// StudentStatusID
			$this->StudentStatusID->LinkCustomAttributes = "";
			$this->StudentStatusID->HrefValue = "";
			$this->StudentStatusID->TooltipValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";
			$this->TahunID->TooltipValue = "";

			// Foto
			$this->Foto->LinkCustomAttributes = "";
			$this->Foto->UploadPath = "upload";
			if (!ew_Empty($this->Foto->Upload->DbValue)) {
				$this->Foto->HrefValue = ew_GetFileUploadUrl($this->Foto, $this->Foto->Upload->DbValue); // Add prefix/suffix
				$this->Foto->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->Foto->HrefValue = ew_ConvertFullUrl($this->Foto->HrefValue);
			} else {
				$this->Foto->HrefValue = "";
			}
			$this->Foto->HrefValue2 = $this->Foto->UploadPath . $this->Foto->Upload->DbValue;
			$this->Foto->TooltipValue = "";
			if ($this->Foto->UseColorbox) {
				if (ew_Empty($this->Foto->TooltipValue))
					$this->Foto->LinkAttrs["title"] = $Language->Phrase("ViewImageGallery");
				$this->Foto->LinkAttrs["data-rel"] = "student_x_Foto";
				ew_AppendClass($this->Foto->LinkAttrs["class"], "ewLightbox");
			}

			// NIK
			$this->NIK->LinkCustomAttributes = "";
			$this->NIK->HrefValue = "";
			$this->NIK->TooltipValue = "";

			// WargaNegara
			$this->WargaNegara->LinkCustomAttributes = "";
			$this->WargaNegara->HrefValue = "";
			$this->WargaNegara->TooltipValue = "";

			// Kelamin
			$this->Kelamin->LinkCustomAttributes = "";
			$this->Kelamin->HrefValue = "";
			$this->Kelamin->TooltipValue = "";

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

			// Darah
			$this->Darah->LinkCustomAttributes = "";
			$this->Darah->HrefValue = "";
			$this->Darah->TooltipValue = "";

			// StatusSipil
			$this->StatusSipil->LinkCustomAttributes = "";
			$this->StatusSipil->HrefValue = "";
			$this->StatusSipil->TooltipValue = "";

			// AlamatDomisili
			$this->AlamatDomisili->LinkCustomAttributes = "";
			$this->AlamatDomisili->HrefValue = "";
			$this->AlamatDomisili->TooltipValue = "";

			// RT
			$this->RT->LinkCustomAttributes = "";
			$this->RT->HrefValue = "";
			$this->RT->TooltipValue = "";

			// RW
			$this->RW->LinkCustomAttributes = "";
			$this->RW->HrefValue = "";
			$this->RW->TooltipValue = "";

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

			// AnakKe
			$this->AnakKe->LinkCustomAttributes = "";
			$this->AnakKe->HrefValue = "";
			$this->AnakKe->TooltipValue = "";

			// JumlahSaudara
			$this->JumlahSaudara->LinkCustomAttributes = "";
			$this->JumlahSaudara->HrefValue = "";
			$this->JumlahSaudara->TooltipValue = "";

			// Telepon
			$this->Telepon->LinkCustomAttributes = "";
			$this->Telepon->HrefValue = "";
			$this->Telepon->TooltipValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";

			// NamaAyah
			$this->NamaAyah->LinkCustomAttributes = "";
			$this->NamaAyah->HrefValue = "";
			$this->NamaAyah->TooltipValue = "";

			// AgamaAyah
			$this->AgamaAyah->LinkCustomAttributes = "";
			$this->AgamaAyah->HrefValue = "";
			$this->AgamaAyah->TooltipValue = "";

			// PendidikanAyah
			$this->PendidikanAyah->LinkCustomAttributes = "";
			$this->PendidikanAyah->HrefValue = "";
			$this->PendidikanAyah->TooltipValue = "";

			// PekerjaanAyah
			$this->PekerjaanAyah->LinkCustomAttributes = "";
			$this->PekerjaanAyah->HrefValue = "";
			$this->PekerjaanAyah->TooltipValue = "";

			// HidupAyah
			$this->HidupAyah->LinkCustomAttributes = "";
			$this->HidupAyah->HrefValue = "";
			$this->HidupAyah->TooltipValue = "";

			// NamaIbu
			$this->NamaIbu->LinkCustomAttributes = "";
			$this->NamaIbu->HrefValue = "";
			$this->NamaIbu->TooltipValue = "";

			// AgamaIbu
			$this->AgamaIbu->LinkCustomAttributes = "";
			$this->AgamaIbu->HrefValue = "";
			$this->AgamaIbu->TooltipValue = "";

			// PendidikanIbu
			$this->PendidikanIbu->LinkCustomAttributes = "";
			$this->PendidikanIbu->HrefValue = "";
			$this->PendidikanIbu->TooltipValue = "";

			// PekerjaanIbu
			$this->PekerjaanIbu->LinkCustomAttributes = "";
			$this->PekerjaanIbu->HrefValue = "";
			$this->PekerjaanIbu->TooltipValue = "";

			// HidupIbu
			$this->HidupIbu->LinkCustomAttributes = "";
			$this->HidupIbu->HrefValue = "";
			$this->HidupIbu->TooltipValue = "";

			// AlamatOrtu
			$this->AlamatOrtu->LinkCustomAttributes = "";
			$this->AlamatOrtu->HrefValue = "";
			$this->AlamatOrtu->TooltipValue = "";

			// RTOrtu
			$this->RTOrtu->LinkCustomAttributes = "";
			$this->RTOrtu->HrefValue = "";
			$this->RTOrtu->TooltipValue = "";

			// RWOrtu
			$this->RWOrtu->LinkCustomAttributes = "";
			$this->RWOrtu->HrefValue = "";
			$this->RWOrtu->TooltipValue = "";

			// KodePosOrtu
			$this->KodePosOrtu->LinkCustomAttributes = "";
			$this->KodePosOrtu->HrefValue = "";
			$this->KodePosOrtu->TooltipValue = "";

			// ProvinsiIDOrtu
			$this->ProvinsiIDOrtu->LinkCustomAttributes = "";
			$this->ProvinsiIDOrtu->HrefValue = "";
			$this->ProvinsiIDOrtu->TooltipValue = "";

			// KabupatenIDOrtu
			$this->KabupatenIDOrtu->LinkCustomAttributes = "";
			$this->KabupatenIDOrtu->HrefValue = "";
			$this->KabupatenIDOrtu->TooltipValue = "";

			// KecamatanIDOrtu
			$this->KecamatanIDOrtu->LinkCustomAttributes = "";
			$this->KecamatanIDOrtu->HrefValue = "";
			$this->KecamatanIDOrtu->TooltipValue = "";

			// DesaIDOrtu
			$this->DesaIDOrtu->LinkCustomAttributes = "";
			$this->DesaIDOrtu->HrefValue = "";
			$this->DesaIDOrtu->TooltipValue = "";

			// NegaraIDOrtu
			$this->NegaraIDOrtu->LinkCustomAttributes = "";
			$this->NegaraIDOrtu->HrefValue = "";
			$this->NegaraIDOrtu->TooltipValue = "";

			// TeleponOrtu
			$this->TeleponOrtu->LinkCustomAttributes = "";
			$this->TeleponOrtu->HrefValue = "";
			$this->TeleponOrtu->TooltipValue = "";

			// HandphoneOrtu
			$this->HandphoneOrtu->LinkCustomAttributes = "";
			$this->HandphoneOrtu->HrefValue = "";
			$this->HandphoneOrtu->TooltipValue = "";

			// EmailOrtu
			$this->EmailOrtu->LinkCustomAttributes = "";
			$this->EmailOrtu->HrefValue = "";
			$this->EmailOrtu->TooltipValue = "";

			// AsalSekolah
			$this->AsalSekolah->LinkCustomAttributes = "";
			$this->AsalSekolah->HrefValue = "";
			$this->AsalSekolah->TooltipValue = "";

			// AlamatSekolah
			$this->AlamatSekolah->LinkCustomAttributes = "";
			$this->AlamatSekolah->HrefValue = "";
			$this->AlamatSekolah->TooltipValue = "";

			// ProvinsiIDSekolah
			$this->ProvinsiIDSekolah->LinkCustomAttributes = "";
			$this->ProvinsiIDSekolah->HrefValue = "";
			$this->ProvinsiIDSekolah->TooltipValue = "";

			// KabupatenIDSekolah
			$this->KabupatenIDSekolah->LinkCustomAttributes = "";
			$this->KabupatenIDSekolah->HrefValue = "";
			$this->KabupatenIDSekolah->TooltipValue = "";

			// KecamatanIDSekolah
			$this->KecamatanIDSekolah->LinkCustomAttributes = "";
			$this->KecamatanIDSekolah->HrefValue = "";
			$this->KecamatanIDSekolah->TooltipValue = "";

			// DesaIDSekolah
			$this->DesaIDSekolah->LinkCustomAttributes = "";
			$this->DesaIDSekolah->HrefValue = "";
			$this->DesaIDSekolah->TooltipValue = "";

			// NilaiSekolah
			$this->NilaiSekolah->LinkCustomAttributes = "";
			$this->NilaiSekolah->HrefValue = "";
			$this->NilaiSekolah->TooltipValue = "";

			// TahunLulus
			$this->TahunLulus->LinkCustomAttributes = "";
			$this->TahunLulus->HrefValue = "";
			$this->TahunLulus->TooltipValue = "";

			// IjazahSekolah
			$this->IjazahSekolah->LinkCustomAttributes = "";
			$this->IjazahSekolah->HrefValue = "";
			$this->IjazahSekolah->TooltipValue = "";

			// TglIjazah
			$this->TglIjazah->LinkCustomAttributes = "";
			$this->TglIjazah->HrefValue = "";
			$this->TglIjazah->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// StudentID
			$this->StudentID->EditAttrs["class"] = "form-control";
			$this->StudentID->EditCustomAttributes = "";
			$this->StudentID->EditValue = ew_HtmlEncode($this->StudentID->AdvancedSearch->SearchValue);
			$this->StudentID->PlaceHolder = ew_RemoveHtml($this->StudentID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->AdvancedSearch->SearchValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// LevelID
			$this->LevelID->EditAttrs["class"] = "form-control";
			$this->LevelID->EditCustomAttributes = "";
			$this->LevelID->EditValue = ew_HtmlEncode($this->LevelID->AdvancedSearch->SearchValue);
			$this->LevelID->PlaceHolder = ew_RemoveHtml($this->LevelID->FldCaption());

			// ProdiID
			$this->ProdiID->EditAttrs["class"] = "form-control";
			$this->ProdiID->EditCustomAttributes = "";
			if (trim(strval($this->ProdiID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
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

			// StudentStatusID
			$this->StudentStatusID->EditAttrs["class"] = "form-control";
			$this->StudentStatusID->EditCustomAttributes = "";
			if (trim(strval($this->StudentStatusID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StudentStatusID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_statusstudent`";
			$sWhereWrk = "";
			$this->StudentStatusID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StudentStatusID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StudentStatusID->EditValue = $arwrk;

			// TahunID
			$this->TahunID->EditAttrs["class"] = "form-control";
			$this->TahunID->EditCustomAttributes = "";
			$this->TahunID->EditValue = ew_HtmlEncode($this->TahunID->AdvancedSearch->SearchValue);
			$this->TahunID->PlaceHolder = ew_RemoveHtml($this->TahunID->FldCaption());

			// Foto
			$this->Foto->EditAttrs["class"] = "form-control";
			$this->Foto->EditCustomAttributes = "";
			$this->Foto->EditValue = ew_HtmlEncode($this->Foto->AdvancedSearch->SearchValue);
			$this->Foto->PlaceHolder = ew_RemoveHtml($this->Foto->FldCaption());

			// NIK
			$this->NIK->EditAttrs["class"] = "form-control";
			$this->NIK->EditCustomAttributes = "";
			$this->NIK->EditValue = ew_HtmlEncode($this->NIK->AdvancedSearch->SearchValue);
			$this->NIK->PlaceHolder = ew_RemoveHtml($this->NIK->FldCaption());

			// WargaNegara
			$this->WargaNegara->EditCustomAttributes = "";
			$this->WargaNegara->EditValue = $this->WargaNegara->Options(FALSE);

			// Kelamin
			$this->Kelamin->EditCustomAttributes = "";
			if (trim(strval($this->Kelamin->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Kelamin`" . ew_SearchString("=", $this->Kelamin->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Kelamin`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_kelamin`";
			$sWhereWrk = "";
			$this->Kelamin->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Kelamin, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Kelamin->EditValue = $arwrk;

			// TempatLahir
			$this->TempatLahir->EditAttrs["class"] = "form-control";
			$this->TempatLahir->EditCustomAttributes = "";
			$this->TempatLahir->EditValue = ew_HtmlEncode($this->TempatLahir->AdvancedSearch->SearchValue);
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

			// Darah
			$this->Darah->EditCustomAttributes = "";
			if (trim(strval($this->Darah->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`DarahID`" . ew_SearchString("=", $this->Darah->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `DarahID`, `DarahID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_darah`";
			$sWhereWrk = "";
			$this->Darah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Darah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Darah->EditValue = $arwrk;

			// StatusSipil
			$this->StatusSipil->EditAttrs["class"] = "form-control";
			$this->StatusSipil->EditCustomAttributes = "";
			if (trim(strval($this->StatusSipil->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StatusSipil`" . ew_SearchString("=", $this->StatusSipil->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StatusSipil`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_statussipil`";
			$sWhereWrk = "";
			$this->StatusSipil->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StatusSipil, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StatusSipil->EditValue = $arwrk;

			// AlamatDomisili
			$this->AlamatDomisili->EditAttrs["class"] = "form-control";
			$this->AlamatDomisili->EditCustomAttributes = "";
			$this->AlamatDomisili->EditValue = ew_HtmlEncode($this->AlamatDomisili->AdvancedSearch->SearchValue);
			$this->AlamatDomisili->PlaceHolder = ew_RemoveHtml($this->AlamatDomisili->FldCaption());

			// RT
			$this->RT->EditAttrs["class"] = "form-control";
			$this->RT->EditCustomAttributes = "";
			$this->RT->EditValue = ew_HtmlEncode($this->RT->AdvancedSearch->SearchValue);
			$this->RT->PlaceHolder = ew_RemoveHtml($this->RT->FldCaption());

			// RW
			$this->RW->EditAttrs["class"] = "form-control";
			$this->RW->EditCustomAttributes = "";
			$this->RW->EditValue = ew_HtmlEncode($this->RW->AdvancedSearch->SearchValue);
			$this->RW->PlaceHolder = ew_RemoveHtml($this->RW->FldCaption());

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

			// AnakKe
			$this->AnakKe->EditAttrs["class"] = "form-control";
			$this->AnakKe->EditCustomAttributes = "";
			$this->AnakKe->EditValue = ew_HtmlEncode($this->AnakKe->AdvancedSearch->SearchValue);
			$this->AnakKe->PlaceHolder = ew_RemoveHtml($this->AnakKe->FldCaption());

			// JumlahSaudara
			$this->JumlahSaudara->EditAttrs["class"] = "form-control";
			$this->JumlahSaudara->EditCustomAttributes = "";
			$this->JumlahSaudara->EditValue = ew_HtmlEncode($this->JumlahSaudara->AdvancedSearch->SearchValue);
			$this->JumlahSaudara->PlaceHolder = ew_RemoveHtml($this->JumlahSaudara->FldCaption());

			// Telepon
			$this->Telepon->EditAttrs["class"] = "form-control";
			$this->Telepon->EditCustomAttributes = "";
			$this->Telepon->EditValue = ew_HtmlEncode($this->Telepon->AdvancedSearch->SearchValue);
			$this->Telepon->PlaceHolder = ew_RemoveHtml($this->Telepon->FldCaption());

			// Email
			$this->_Email->EditAttrs["class"] = "form-control";
			$this->_Email->EditCustomAttributes = "";
			$this->_Email->EditValue = ew_HtmlEncode($this->_Email->AdvancedSearch->SearchValue);
			$this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

			// NamaAyah
			$this->NamaAyah->EditAttrs["class"] = "form-control";
			$this->NamaAyah->EditCustomAttributes = "";
			$this->NamaAyah->EditValue = ew_HtmlEncode($this->NamaAyah->AdvancedSearch->SearchValue);
			$this->NamaAyah->PlaceHolder = ew_RemoveHtml($this->NamaAyah->FldCaption());

			// AgamaAyah
			$this->AgamaAyah->EditAttrs["class"] = "form-control";
			$this->AgamaAyah->EditCustomAttributes = "";
			if (trim(strval($this->AgamaAyah->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaAyah->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaAyah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->AgamaAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->AgamaAyah->EditValue = $arwrk;

			// PendidikanAyah
			$this->PendidikanAyah->EditAttrs["class"] = "form-control";
			$this->PendidikanAyah->EditCustomAttributes = "";
			if (trim(strval($this->PendidikanAyah->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanAyah->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanAyah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->PendidikanAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->PendidikanAyah->EditValue = $arwrk;

			// PekerjaanAyah
			$this->PekerjaanAyah->EditAttrs["class"] = "form-control";
			$this->PekerjaanAyah->EditCustomAttributes = "";
			if (trim(strval($this->PekerjaanAyah->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Pekerjaan`" . ew_SearchString("=", $this->PekerjaanAyah->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Pekerjaan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_pekerjaanortu`";
			$sWhereWrk = "";
			$this->PekerjaanAyah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->PekerjaanAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->PekerjaanAyah->EditValue = $arwrk;

			// HidupAyah
			$this->HidupAyah->EditAttrs["class"] = "form-control";
			$this->HidupAyah->EditCustomAttributes = "";
			if (trim(strval($this->HidupAyah->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Hidup`" . ew_SearchString("=", $this->HidupAyah->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Hidup`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_hidup`";
			$sWhereWrk = "";
			$this->HidupAyah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->HidupAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->HidupAyah->EditValue = $arwrk;

			// NamaIbu
			$this->NamaIbu->EditAttrs["class"] = "form-control";
			$this->NamaIbu->EditCustomAttributes = "";
			$this->NamaIbu->EditValue = ew_HtmlEncode($this->NamaIbu->AdvancedSearch->SearchValue);
			$this->NamaIbu->PlaceHolder = ew_RemoveHtml($this->NamaIbu->FldCaption());

			// AgamaIbu
			$this->AgamaIbu->EditAttrs["class"] = "form-control";
			$this->AgamaIbu->EditCustomAttributes = "";
			if (trim(strval($this->AgamaIbu->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`AgamaID`" . ew_SearchString("=", $this->AgamaIbu->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `AgamaID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaIbu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->AgamaIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->AgamaIbu->EditValue = $arwrk;

			// PendidikanIbu
			$this->PendidikanIbu->EditAttrs["class"] = "form-control";
			$this->PendidikanIbu->EditCustomAttributes = "";
			if (trim(strval($this->PendidikanIbu->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Pendidikan`" . ew_SearchString("=", $this->PendidikanIbu->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Pendidikan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanIbu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->PendidikanIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->PendidikanIbu->EditValue = $arwrk;

			// PekerjaanIbu
			$this->PekerjaanIbu->EditAttrs["class"] = "form-control";
			$this->PekerjaanIbu->EditCustomAttributes = "";
			if (trim(strval($this->PekerjaanIbu->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Pekerjaan`" . ew_SearchString("=", $this->PekerjaanIbu->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Pekerjaan`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_pekerjaanortu`";
			$sWhereWrk = "";
			$this->PekerjaanIbu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->PekerjaanIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->PekerjaanIbu->EditValue = $arwrk;

			// HidupIbu
			$this->HidupIbu->EditAttrs["class"] = "form-control";
			$this->HidupIbu->EditCustomAttributes = "";
			if (trim(strval($this->HidupIbu->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Hidup`" . ew_SearchString("=", $this->HidupIbu->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `Hidup`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_hidup`";
			$sWhereWrk = "";
			$this->HidupIbu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->HidupIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->HidupIbu->EditValue = $arwrk;

			// AlamatOrtu
			$this->AlamatOrtu->EditAttrs["class"] = "form-control";
			$this->AlamatOrtu->EditCustomAttributes = "";
			$this->AlamatOrtu->EditValue = ew_HtmlEncode($this->AlamatOrtu->AdvancedSearch->SearchValue);
			$this->AlamatOrtu->PlaceHolder = ew_RemoveHtml($this->AlamatOrtu->FldCaption());

			// RTOrtu
			$this->RTOrtu->EditAttrs["class"] = "form-control";
			$this->RTOrtu->EditCustomAttributes = "";
			$this->RTOrtu->EditValue = ew_HtmlEncode($this->RTOrtu->AdvancedSearch->SearchValue);
			$this->RTOrtu->PlaceHolder = ew_RemoveHtml($this->RTOrtu->FldCaption());

			// RWOrtu
			$this->RWOrtu->EditAttrs["class"] = "form-control";
			$this->RWOrtu->EditCustomAttributes = "";
			$this->RWOrtu->EditValue = ew_HtmlEncode($this->RWOrtu->AdvancedSearch->SearchValue);
			$this->RWOrtu->PlaceHolder = ew_RemoveHtml($this->RWOrtu->FldCaption());

			// KodePosOrtu
			$this->KodePosOrtu->EditAttrs["class"] = "form-control";
			$this->KodePosOrtu->EditCustomAttributes = "";
			$this->KodePosOrtu->EditValue = ew_HtmlEncode($this->KodePosOrtu->AdvancedSearch->SearchValue);
			$this->KodePosOrtu->PlaceHolder = ew_RemoveHtml($this->KodePosOrtu->FldCaption());

			// ProvinsiIDOrtu
			$this->ProvinsiIDOrtu->EditAttrs["class"] = "form-control";
			$this->ProvinsiIDOrtu->EditCustomAttributes = "";
			if (trim(strval($this->ProvinsiIDOrtu->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiIDOrtu->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiIDOrtu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->ProvinsiIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProvinsiIDOrtu->EditValue = $arwrk;

			// KabupatenIDOrtu
			$this->KabupatenIDOrtu->EditAttrs["class"] = "form-control";
			$this->KabupatenIDOrtu->EditCustomAttributes = "";
			if (trim(strval($this->KabupatenIDOrtu->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenIDOrtu->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProvinsiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "";
			$this->KabupatenIDOrtu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KabupatenIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KabupatenIDOrtu->EditValue = $arwrk;

			// KecamatanIDOrtu
			$this->KecamatanIDOrtu->EditAttrs["class"] = "form-control";
			$this->KecamatanIDOrtu->EditCustomAttributes = "";
			if (trim(strval($this->KecamatanIDOrtu->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanIDOrtu->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KabupatenKotaID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "";
			$this->KecamatanIDOrtu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KecamatanIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KecamatanIDOrtu->EditValue = $arwrk;

			// DesaIDOrtu
			$this->DesaIDOrtu->EditAttrs["class"] = "form-control";
			$this->DesaIDOrtu->EditCustomAttributes = "";
			if (trim(strval($this->DesaIDOrtu->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaIDOrtu->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KecamatanID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_desa`";
			$sWhereWrk = "";
			$this->DesaIDOrtu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->DesaIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->DesaIDOrtu->EditValue = $arwrk;

			// NegaraIDOrtu
			$this->NegaraIDOrtu->EditAttrs["class"] = "form-control";
			$this->NegaraIDOrtu->EditCustomAttributes = "";
			if (trim(strval($this->NegaraIDOrtu->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`NegaraID`" . ew_SearchString("=", $this->NegaraIDOrtu->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `NegaraID`, `NamaNegara` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_negara`";
			$sWhereWrk = "";
			$this->NegaraIDOrtu->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->NegaraIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->NegaraIDOrtu->EditValue = $arwrk;

			// TeleponOrtu
			$this->TeleponOrtu->EditAttrs["class"] = "form-control";
			$this->TeleponOrtu->EditCustomAttributes = "";
			$this->TeleponOrtu->EditValue = ew_HtmlEncode($this->TeleponOrtu->AdvancedSearch->SearchValue);
			$this->TeleponOrtu->PlaceHolder = ew_RemoveHtml($this->TeleponOrtu->FldCaption());

			// HandphoneOrtu
			$this->HandphoneOrtu->EditAttrs["class"] = "form-control";
			$this->HandphoneOrtu->EditCustomAttributes = "";
			$this->HandphoneOrtu->EditValue = ew_HtmlEncode($this->HandphoneOrtu->AdvancedSearch->SearchValue);
			$this->HandphoneOrtu->PlaceHolder = ew_RemoveHtml($this->HandphoneOrtu->FldCaption());

			// EmailOrtu
			$this->EmailOrtu->EditAttrs["class"] = "form-control";
			$this->EmailOrtu->EditCustomAttributes = "";
			$this->EmailOrtu->EditValue = ew_HtmlEncode($this->EmailOrtu->AdvancedSearch->SearchValue);
			$this->EmailOrtu->PlaceHolder = ew_RemoveHtml($this->EmailOrtu->FldCaption());

			// AsalSekolah
			$this->AsalSekolah->EditAttrs["class"] = "form-control";
			$this->AsalSekolah->EditCustomAttributes = "";
			$this->AsalSekolah->EditValue = ew_HtmlEncode($this->AsalSekolah->AdvancedSearch->SearchValue);
			$this->AsalSekolah->PlaceHolder = ew_RemoveHtml($this->AsalSekolah->FldCaption());

			// AlamatSekolah
			$this->AlamatSekolah->EditAttrs["class"] = "form-control";
			$this->AlamatSekolah->EditCustomAttributes = "";
			$this->AlamatSekolah->EditValue = ew_HtmlEncode($this->AlamatSekolah->AdvancedSearch->SearchValue);
			$this->AlamatSekolah->PlaceHolder = ew_RemoveHtml($this->AlamatSekolah->FldCaption());

			// ProvinsiIDSekolah
			$this->ProvinsiIDSekolah->EditAttrs["class"] = "form-control";
			$this->ProvinsiIDSekolah->EditCustomAttributes = "";
			if (trim(strval($this->ProvinsiIDSekolah->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProvinsiID`" . ew_SearchString("=", $this->ProvinsiIDSekolah->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProvinsiID`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiIDSekolah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->ProvinsiIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProvinsiIDSekolah->EditValue = $arwrk;

			// KabupatenIDSekolah
			$this->KabupatenIDSekolah->EditAttrs["class"] = "form-control";
			$this->KabupatenIDSekolah->EditCustomAttributes = "";
			if (trim(strval($this->KabupatenIDSekolah->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KabupatenKotaID`" . ew_SearchString("=", $this->KabupatenIDSekolah->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KabupatenKotaID`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProvinsiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "";
			$this->KabupatenIDSekolah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KabupatenIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KabupatenIDSekolah->EditValue = $arwrk;

			// KecamatanIDSekolah
			$this->KecamatanIDSekolah->EditAttrs["class"] = "form-control";
			$this->KecamatanIDSekolah->EditCustomAttributes = "";
			if (trim(strval($this->KecamatanIDSekolah->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KecamatanID`" . ew_SearchString("=", $this->KecamatanIDSekolah->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KecamatanID`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KabupatenKotaID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "";
			$this->KecamatanIDSekolah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KecamatanIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KecamatanIDSekolah->EditValue = $arwrk;

			// DesaIDSekolah
			$this->DesaIDSekolah->EditAttrs["class"] = "form-control";
			$this->DesaIDSekolah->EditCustomAttributes = "";
			if (trim(strval($this->DesaIDSekolah->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`DesaID`" . ew_SearchString("=", $this->DesaIDSekolah->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `DesaID`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `KecamatanID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_wilayah_desa`";
			$sWhereWrk = "";
			$this->DesaIDSekolah->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->DesaIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->DesaIDSekolah->EditValue = $arwrk;

			// NilaiSekolah
			$this->NilaiSekolah->EditAttrs["class"] = "form-control";
			$this->NilaiSekolah->EditCustomAttributes = "";
			$this->NilaiSekolah->EditValue = ew_HtmlEncode($this->NilaiSekolah->AdvancedSearch->SearchValue);
			$this->NilaiSekolah->PlaceHolder = ew_RemoveHtml($this->NilaiSekolah->FldCaption());

			// TahunLulus
			$this->TahunLulus->EditAttrs["class"] = "form-control";
			$this->TahunLulus->EditCustomAttributes = "";
			$this->TahunLulus->EditValue = ew_HtmlEncode($this->TahunLulus->AdvancedSearch->SearchValue);
			$this->TahunLulus->PlaceHolder = ew_RemoveHtml($this->TahunLulus->FldCaption());

			// IjazahSekolah
			$this->IjazahSekolah->EditAttrs["class"] = "form-control";
			$this->IjazahSekolah->EditCustomAttributes = "";
			$this->IjazahSekolah->EditValue = ew_HtmlEncode($this->IjazahSekolah->AdvancedSearch->SearchValue);
			$this->IjazahSekolah->PlaceHolder = ew_RemoveHtml($this->IjazahSekolah->FldCaption());

			// TglIjazah
			$this->TglIjazah->EditAttrs["class"] = "form-control";
			$this->TglIjazah->EditCustomAttributes = "";
			$this->TglIjazah->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->TglIjazah->AdvancedSearch->SearchValue, 0), 8));
			$this->TglIjazah->PlaceHolder = ew_RemoveHtml($this->TglIjazah->FldCaption());

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
		if (!ew_CheckInteger($this->TahunID->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TahunID->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TanggalLahir->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TanggalLahir->FldErrMsg());
		}
		if (!ew_CheckInteger($this->AnakKe->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->AnakKe->FldErrMsg());
		}
		if (!ew_CheckInteger($this->JumlahSaudara->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->JumlahSaudara->FldErrMsg());
		}
		if (!ew_CheckNumber($this->NilaiSekolah->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->NilaiSekolah->FldErrMsg());
		}
		if (!ew_CheckInteger($this->TahunLulus->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TahunLulus->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->TglIjazah->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TglIjazah->FldErrMsg());
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
		$this->StudentID->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->LevelID->AdvancedSearch->Load();
		$this->ProdiID->AdvancedSearch->Load();
		$this->StudentStatusID->AdvancedSearch->Load();
		$this->TahunID->AdvancedSearch->Load();
		$this->Foto->AdvancedSearch->Load();
		$this->NIK->AdvancedSearch->Load();
		$this->WargaNegara->AdvancedSearch->Load();
		$this->Kelamin->AdvancedSearch->Load();
		$this->TempatLahir->AdvancedSearch->Load();
		$this->TanggalLahir->AdvancedSearch->Load();
		$this->AgamaID->AdvancedSearch->Load();
		$this->Darah->AdvancedSearch->Load();
		$this->StatusSipil->AdvancedSearch->Load();
		$this->AlamatDomisili->AdvancedSearch->Load();
		$this->RT->AdvancedSearch->Load();
		$this->RW->AdvancedSearch->Load();
		$this->KodePos->AdvancedSearch->Load();
		$this->ProvinsiID->AdvancedSearch->Load();
		$this->KabupatenKotaID->AdvancedSearch->Load();
		$this->KecamatanID->AdvancedSearch->Load();
		$this->DesaID->AdvancedSearch->Load();
		$this->AnakKe->AdvancedSearch->Load();
		$this->JumlahSaudara->AdvancedSearch->Load();
		$this->Telepon->AdvancedSearch->Load();
		$this->_Email->AdvancedSearch->Load();
		$this->NamaAyah->AdvancedSearch->Load();
		$this->AgamaAyah->AdvancedSearch->Load();
		$this->PendidikanAyah->AdvancedSearch->Load();
		$this->PekerjaanAyah->AdvancedSearch->Load();
		$this->HidupAyah->AdvancedSearch->Load();
		$this->NamaIbu->AdvancedSearch->Load();
		$this->AgamaIbu->AdvancedSearch->Load();
		$this->PendidikanIbu->AdvancedSearch->Load();
		$this->PekerjaanIbu->AdvancedSearch->Load();
		$this->HidupIbu->AdvancedSearch->Load();
		$this->AlamatOrtu->AdvancedSearch->Load();
		$this->RTOrtu->AdvancedSearch->Load();
		$this->RWOrtu->AdvancedSearch->Load();
		$this->KodePosOrtu->AdvancedSearch->Load();
		$this->ProvinsiIDOrtu->AdvancedSearch->Load();
		$this->KabupatenIDOrtu->AdvancedSearch->Load();
		$this->KecamatanIDOrtu->AdvancedSearch->Load();
		$this->DesaIDOrtu->AdvancedSearch->Load();
		$this->NegaraIDOrtu->AdvancedSearch->Load();
		$this->TeleponOrtu->AdvancedSearch->Load();
		$this->HandphoneOrtu->AdvancedSearch->Load();
		$this->EmailOrtu->AdvancedSearch->Load();
		$this->AsalSekolah->AdvancedSearch->Load();
		$this->AlamatSekolah->AdvancedSearch->Load();
		$this->ProvinsiIDSekolah->AdvancedSearch->Load();
		$this->KabupatenIDSekolah->AdvancedSearch->Load();
		$this->KecamatanIDSekolah->AdvancedSearch->Load();
		$this->DesaIDSekolah->AdvancedSearch->Load();
		$this->NilaiSekolah->AdvancedSearch->Load();
		$this->TahunLulus->AdvancedSearch->Load();
		$this->IjazahSekolah->AdvancedSearch->Load();
		$this->TglIjazah->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("studentlist.php"), "", $this->TableVar, TRUE);
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
		case "x_StudentStatusID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StatusStudentID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statusstudent`";
			$sWhereWrk = "";
			$this->StudentStatusID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StatusStudentID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StudentStatusID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_Kelamin":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Kelamin` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kelamin`";
			$sWhereWrk = "";
			$this->Kelamin->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Kelamin` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Kelamin, $sWhereWrk); // Call Lookup selecting
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
		case "x_Darah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `DarahID` AS `LinkFld`, `DarahID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_darah`";
			$sWhereWrk = "";
			$this->Darah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`DarahID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Darah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StatusSipil":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StatusSipil` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statussipil`";
			$sWhereWrk = "";
			$this->StatusSipil->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StatusSipil` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StatusSipil, $sWhereWrk); // Call Lookup selecting
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
		case "x_AgamaAyah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `AgamaID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaAyah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`AgamaID` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->AgamaAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_PendidikanAyah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Pendidikan` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanAyah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Pendidikan` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->PendidikanAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_PekerjaanAyah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Pekerjaan` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pekerjaanortu`";
			$sWhereWrk = "";
			$this->PekerjaanAyah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Pekerjaan` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->PekerjaanAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_HidupAyah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Hidup` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hidup`";
			$sWhereWrk = "";
			$this->HidupAyah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Hidup` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->HidupAyah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_AgamaIbu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `AgamaID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_agama`";
			$sWhereWrk = "";
			$this->AgamaIbu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`AgamaID` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->AgamaIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_PendidikanIbu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Pendidikan` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pendidikanortu`";
			$sWhereWrk = "";
			$this->PendidikanIbu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Pendidikan` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->PendidikanIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_PekerjaanIbu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Pekerjaan` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_pekerjaanortu`";
			$sWhereWrk = "";
			$this->PekerjaanIbu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Pekerjaan` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->PekerjaanIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_HidupIbu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Hidup` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hidup`";
			$sWhereWrk = "";
			$this->HidupIbu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Hidup` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->HidupIbu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_ProvinsiIDOrtu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProvinsiID` AS `LinkFld`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiIDOrtu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProvinsiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->ProvinsiIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KabupatenIDOrtu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KabupatenKotaID` AS `LinkFld`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "{filter}";
			$this->KabupatenIDOrtu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KabupatenKotaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProvinsiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KabupatenIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KecamatanIDOrtu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KecamatanID` AS `LinkFld`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "{filter}";
			$this->KecamatanIDOrtu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KecamatanID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KabupatenKotaID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KecamatanIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_DesaIDOrtu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `DesaID` AS `LinkFld`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
			$sWhereWrk = "{filter}";
			$this->DesaIDOrtu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`DesaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KecamatanID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->DesaIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_NegaraIDOrtu":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `NegaraID` AS `LinkFld`, `NamaNegara` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_negara`";
			$sWhereWrk = "";
			$this->NegaraIDOrtu->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`NegaraID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->NegaraIDOrtu, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_ProvinsiIDSekolah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProvinsiID` AS `LinkFld`, `Provinsi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_provinsi`";
			$sWhereWrk = "";
			$this->ProvinsiIDSekolah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProvinsiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->ProvinsiIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KabupatenIDSekolah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KabupatenKotaID` AS `LinkFld`, `KabupatenKota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kabupatenkota`";
			$sWhereWrk = "{filter}";
			$this->KabupatenIDSekolah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KabupatenKotaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProvinsiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KabupatenIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KecamatanIDSekolah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KecamatanID` AS `LinkFld`, `Kecamatan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_kecamatan`";
			$sWhereWrk = "{filter}";
			$this->KecamatanIDSekolah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KecamatanID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KabupatenKotaID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KecamatanIDSekolah, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_DesaIDSekolah":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `DesaID` AS `LinkFld`, `Desa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_wilayah_desa`";
			$sWhereWrk = "{filter}";
			$this->DesaIDSekolah->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`DesaID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`KecamatanID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->DesaIDSekolah, $sWhereWrk); // Call Lookup selecting
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
if (!isset($student_search)) $student_search = new cstudent_search();

// Page init
$student_search->Page_Init();

// Page main
$student_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$student_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($student_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fstudentsearch = new ew_Form("fstudentsearch", "search");
<?php } else { ?>
var CurrentForm = fstudentsearch = new ew_Form("fstudentsearch", "search");
<?php } ?>

// Form_CustomValidate event
fstudentsearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fstudentsearch.ValidateRequired = true;
<?php } else { ?>
fstudentsearch.ValidateRequired = false; 
<?php } ?>

// Multi-Page
fstudentsearch.MultiPage = new ew_MultiPage("fstudentsearch");

// Dynamic selection lists
fstudentsearch.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fstudentsearch.Lists["x_StudentStatusID"] = {"LinkField":"x_StatusStudentID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statusstudent"};
fstudentsearch.Lists["x_WargaNegara"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fstudentsearch.Lists["x_WargaNegara"].Options = <?php echo json_encode($student->WargaNegara->Options()) ?>;
fstudentsearch.Lists["x_Kelamin"] = {"LinkField":"x_Kelamin","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kelamin"};
fstudentsearch.Lists["x_AgamaID"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fstudentsearch.Lists["x_Darah"] = {"LinkField":"x_DarahID","Ajax":true,"AutoFill":false,"DisplayFields":["x_DarahID","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_darah"};
fstudentsearch.Lists["x_StatusSipil"] = {"LinkField":"x_StatusSipil","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statussipil"};
fstudentsearch.Lists["x_ProvinsiID"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenKotaID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fstudentsearch.Lists["x_KabupatenKotaID"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":["x_ProvinsiID"],"ChildFields":["x_KecamatanID"],"FilterFields":["x_ProvinsiID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstudentsearch.Lists["x_KecamatanID"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":["x_KabupatenKotaID"],"ChildFields":["x_DesaID"],"FilterFields":["x_KabupatenKotaID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fstudentsearch.Lists["x_DesaID"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":["x_KecamatanID"],"ChildFields":[],"FilterFields":["x_KecamatanID"],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fstudentsearch.Lists["x_AgamaAyah"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fstudentsearch.Lists["x_PendidikanAyah"] = {"LinkField":"x_Pendidikan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pendidikanortu"};
fstudentsearch.Lists["x_PekerjaanAyah"] = {"LinkField":"x_Pekerjaan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pekerjaanortu"};
fstudentsearch.Lists["x_HidupAyah"] = {"LinkField":"x_Hidup","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_hidup"};
fstudentsearch.Lists["x_AgamaIbu"] = {"LinkField":"x_AgamaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_agama"};
fstudentsearch.Lists["x_PendidikanIbu"] = {"LinkField":"x_Pendidikan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pendidikanortu"};
fstudentsearch.Lists["x_PekerjaanIbu"] = {"LinkField":"x_Pekerjaan","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_pekerjaanortu"};
fstudentsearch.Lists["x_HidupIbu"] = {"LinkField":"x_Hidup","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_hidup"};
fstudentsearch.Lists["x_ProvinsiIDOrtu"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenIDOrtu"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fstudentsearch.Lists["x_KabupatenIDOrtu"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":["x_ProvinsiIDOrtu"],"ChildFields":["x_KecamatanIDOrtu"],"FilterFields":["x_ProvinsiID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstudentsearch.Lists["x_KecamatanIDOrtu"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":["x_KabupatenIDOrtu"],"ChildFields":["x_DesaIDOrtu"],"FilterFields":["x_KabupatenKotaID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fstudentsearch.Lists["x_DesaIDOrtu"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":["x_KecamatanIDOrtu"],"ChildFields":[],"FilterFields":["x_KecamatanID"],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fstudentsearch.Lists["x_NegaraIDOrtu"] = {"LinkField":"x_NegaraID","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaNegara","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_negara"};
fstudentsearch.Lists["x_ProvinsiIDSekolah"] = {"LinkField":"x_ProvinsiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Provinsi","","",""],"ParentFields":[],"ChildFields":["x_KabupatenIDSekolah"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_wilayah_provinsi"};
fstudentsearch.Lists["x_KabupatenIDSekolah"] = {"LinkField":"x_KabupatenKotaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_KabupatenKota","","",""],"ParentFields":["x_ProvinsiIDSekolah"],"ChildFields":["x_KecamatanIDSekolah"],"FilterFields":["x_ProvinsiID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kabupatenkota"};
fstudentsearch.Lists["x_KecamatanIDSekolah"] = {"LinkField":"x_KecamatanID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Kecamatan","","",""],"ParentFields":["x_KabupatenIDSekolah"],"ChildFields":["x_DesaIDSekolah"],"FilterFields":["x_KabupatenKotaID"],"Options":[],"Template":"","LinkTable":"master_wilayah_kecamatan"};
fstudentsearch.Lists["x_DesaIDSekolah"] = {"LinkField":"x_DesaID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Desa","","",""],"ParentFields":["x_KecamatanIDSekolah"],"ChildFields":[],"FilterFields":["x_KecamatanID"],"Options":[],"Template":"","LinkTable":"master_wilayah_desa"};
fstudentsearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fstudentsearch.Lists["x_NA"].Options = <?php echo json_encode($student->NA->Options()) ?>;

// Form object for search
// Validate function for search

fstudentsearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_LevelID");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($student->LevelID->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_TahunID");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($student->TahunID->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_TanggalLahir");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($student->TanggalLahir->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_AnakKe");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($student->AnakKe->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_JumlahSaudara");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($student->JumlahSaudara->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_NilaiSekolah");
	if (elm && !ew_CheckNumber(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($student->NilaiSekolah->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_TahunLulus");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($student->TahunLulus->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_TglIjazah");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($student->TglIjazah->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$student_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $student_search->ShowPageHeader(); ?>
<?php
$student_search->ShowMessage();
?>
<form name="fstudentsearch" id="fstudentsearch" class="<?php echo $student_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($student_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $student_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="student">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($student_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$student_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<div class="ewMultiPage">
<div class="tabbable" id="student_search">
	<ul class="nav<?php echo $student_search->MultiPages->NavStyle() ?>">
		<li<?php echo $student_search->MultiPages->TabStyle("1") ?>><a href="#tab_student1" data-toggle="tab"><?php echo $student->PageCaption(1) ?></a></li>
		<li<?php echo $student_search->MultiPages->TabStyle("2") ?>><a href="#tab_student2" data-toggle="tab"><?php echo $student->PageCaption(2) ?></a></li>
		<li<?php echo $student_search->MultiPages->TabStyle("3") ?>><a href="#tab_student3" data-toggle="tab"><?php echo $student->PageCaption(3) ?></a></li>
		<li<?php echo $student_search->MultiPages->TabStyle("4") ?>><a href="#tab_student4" data-toggle="tab"><?php echo $student->PageCaption(4) ?></a></li>
		<li<?php echo $student_search->MultiPages->TabStyle("5") ?>><a href="#tab_student5" data-toggle="tab"><?php echo $student->PageCaption(5) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane<?php echo $student_search->MultiPages->PageStyle("1") ?>" id="tab_student1">
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_studentsearch1" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($student->StudentID->Visible) { // StudentID ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_StudentID" class="form-group">
		<label for="x_StudentID" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_StudentID"><?php echo $student->StudentID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StudentID" id="z_StudentID" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->StudentID->CellAttributes() ?>>
			<span id="el_student_StudentID">
<input type="text" data-table="student" data-field="x_StudentID" data-page="1" name="x_StudentID" id="x_StudentID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($student->StudentID->getPlaceHolder()) ?>" value="<?php echo $student->StudentID->EditValue ?>"<?php echo $student->StudentID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_StudentID">
		<td><span id="elh_student_StudentID"><?php echo $student->StudentID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StudentID" id="z_StudentID" value="LIKE"></span></td>
		<td<?php echo $student->StudentID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_StudentID">
<input type="text" data-table="student" data-field="x_StudentID" data-page="1" name="x_StudentID" id="x_StudentID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($student->StudentID->getPlaceHolder()) ?>" value="<?php echo $student->StudentID->EditValue ?>"<?php echo $student->StudentID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label for="x_Nama" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_Nama"><?php echo $student->Nama->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->Nama->CellAttributes() ?>>
			<span id="el_student_Nama">
<input type="text" data-table="student" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Nama->getPlaceHolder()) ?>" value="<?php echo $student->Nama->EditValue ?>"<?php echo $student->Nama->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_student_Nama"><?php echo $student->Nama->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></span></td>
		<td<?php echo $student->Nama->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_Nama">
<input type="text" data-table="student" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Nama->getPlaceHolder()) ?>" value="<?php echo $student->Nama->EditValue ?>"<?php echo $student->Nama->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->LevelID->Visible) { // LevelID ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_LevelID" class="form-group">
		<label for="x_LevelID" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_LevelID"><?php echo $student->LevelID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_LevelID" id="z_LevelID" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->LevelID->CellAttributes() ?>>
			<span id="el_student_LevelID">
<input type="text" data-table="student" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($student->LevelID->getPlaceHolder()) ?>" value="<?php echo $student->LevelID->EditValue ?>"<?php echo $student->LevelID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_LevelID">
		<td><span id="elh_student_LevelID"><?php echo $student->LevelID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_LevelID" id="z_LevelID" value="="></span></td>
		<td<?php echo $student->LevelID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_LevelID">
<input type="text" data-table="student" data-field="x_LevelID" data-page="1" name="x_LevelID" id="x_LevelID" size="30" placeholder="<?php echo ew_HtmlEncode($student->LevelID->getPlaceHolder()) ?>" value="<?php echo $student->LevelID->EditValue ?>"<?php echo $student->LevelID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label for="x_ProdiID" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_ProdiID"><?php echo $student->ProdiID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->ProdiID->CellAttributes() ?>>
			<span id="el_student_ProdiID">
<select data-table="student" data-field="x_ProdiID" data-page="1" data-value-separator="<?php echo $student->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $student->ProdiID->EditAttributes() ?>>
<?php echo $student->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $student->ProdiID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_student_ProdiID"><?php echo $student->ProdiID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></span></td>
		<td<?php echo $student->ProdiID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_ProdiID">
<select data-table="student" data-field="x_ProdiID" data-page="1" data-value-separator="<?php echo $student->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $student->ProdiID->EditAttributes() ?>>
<?php echo $student->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $student->ProdiID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->StudentStatusID->Visible) { // StudentStatusID ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_StudentStatusID" class="form-group">
		<label for="x_StudentStatusID" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_StudentStatusID"><?php echo $student->StudentStatusID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StudentStatusID" id="z_StudentStatusID" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->StudentStatusID->CellAttributes() ?>>
			<span id="el_student_StudentStatusID">
<select data-table="student" data-field="x_StudentStatusID" data-page="1" data-value-separator="<?php echo $student->StudentStatusID->DisplayValueSeparatorAttribute() ?>" id="x_StudentStatusID" name="x_StudentStatusID"<?php echo $student->StudentStatusID->EditAttributes() ?>>
<?php echo $student->StudentStatusID->SelectOptionListHtml("x_StudentStatusID") ?>
</select>
<input type="hidden" name="s_x_StudentStatusID" id="s_x_StudentStatusID" value="<?php echo $student->StudentStatusID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_StudentStatusID">
		<td><span id="elh_student_StudentStatusID"><?php echo $student->StudentStatusID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StudentStatusID" id="z_StudentStatusID" value="LIKE"></span></td>
		<td<?php echo $student->StudentStatusID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_StudentStatusID">
<select data-table="student" data-field="x_StudentStatusID" data-page="1" data-value-separator="<?php echo $student->StudentStatusID->DisplayValueSeparatorAttribute() ?>" id="x_StudentStatusID" name="x_StudentStatusID"<?php echo $student->StudentStatusID->EditAttributes() ?>>
<?php echo $student->StudentStatusID->SelectOptionListHtml("x_StudentStatusID") ?>
</select>
<input type="hidden" name="s_x_StudentStatusID" id="s_x_StudentStatusID" value="<?php echo $student->StudentStatusID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TahunID->Visible) { // TahunID ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_TahunID" class="form-group">
		<label for="x_TahunID" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_TahunID"><?php echo $student->TahunID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TahunID" id="z_TahunID" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->TahunID->CellAttributes() ?>>
			<span id="el_student_TahunID">
<input type="text" data-table="student" data-field="x_TahunID" data-page="1" name="x_TahunID" id="x_TahunID" size="30" placeholder="<?php echo ew_HtmlEncode($student->TahunID->getPlaceHolder()) ?>" value="<?php echo $student->TahunID->EditValue ?>"<?php echo $student->TahunID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TahunID">
		<td><span id="elh_student_TahunID"><?php echo $student->TahunID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TahunID" id="z_TahunID" value="="></span></td>
		<td<?php echo $student->TahunID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_TahunID">
<input type="text" data-table="student" data-field="x_TahunID" data-page="1" name="x_TahunID" id="x_TahunID" size="30" placeholder="<?php echo ew_HtmlEncode($student->TahunID->getPlaceHolder()) ?>" value="<?php echo $student->TahunID->EditValue ?>"<?php echo $student->TahunID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->Foto->Visible) { // Foto ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_Foto" class="form-group">
		<label class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_Foto"><?php echo $student->Foto->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Foto" id="z_Foto" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->Foto->CellAttributes() ?>>
			<span id="el_student_Foto">
<input type="text" data-table="student" data-field="x_Foto" data-page="1" name="x_Foto" id="x_Foto" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Foto->getPlaceHolder()) ?>" value="<?php echo $student->Foto->EditValue ?>"<?php echo $student->Foto->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Foto">
		<td><span id="elh_student_Foto"><?php echo $student->Foto->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Foto" id="z_Foto" value="LIKE"></span></td>
		<td<?php echo $student->Foto->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_Foto">
<input type="text" data-table="student" data-field="x_Foto" data-page="1" name="x_Foto" id="x_Foto" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Foto->getPlaceHolder()) ?>" value="<?php echo $student->Foto->EditValue ?>"<?php echo $student->Foto->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $student_search->MultiPages->PageStyle("2") ?>" id="tab_student2">
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_studentsearch2" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($student->NIK->Visible) { // NIK ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_NIK" class="form-group">
		<label for="x_NIK" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_NIK"><?php echo $student->NIK->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NIK" id="z_NIK" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->NIK->CellAttributes() ?>>
			<span id="el_student_NIK">
<input type="text" data-table="student" data-field="x_NIK" data-page="2" name="x_NIK" id="x_NIK" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->NIK->getPlaceHolder()) ?>" value="<?php echo $student->NIK->EditValue ?>"<?php echo $student->NIK->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NIK">
		<td><span id="elh_student_NIK"><?php echo $student->NIK->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NIK" id="z_NIK" value="LIKE"></span></td>
		<td<?php echo $student->NIK->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_NIK">
<input type="text" data-table="student" data-field="x_NIK" data-page="2" name="x_NIK" id="x_NIK" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->NIK->getPlaceHolder()) ?>" value="<?php echo $student->NIK->EditValue ?>"<?php echo $student->NIK->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->WargaNegara->Visible) { // WargaNegara ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_WargaNegara" class="form-group">
		<label class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_WargaNegara"><?php echo $student->WargaNegara->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_WargaNegara" id="z_WargaNegara" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->WargaNegara->CellAttributes() ?>>
			<span id="el_student_WargaNegara">
<div id="tp_x_WargaNegara" class="ewTemplate"><input type="radio" data-table="student" data-field="x_WargaNegara" data-page="2" data-value-separator="<?php echo $student->WargaNegara->DisplayValueSeparatorAttribute() ?>" name="x_WargaNegara" id="x_WargaNegara" value="{value}"<?php echo $student->WargaNegara->EditAttributes() ?>></div>
<div id="dsl_x_WargaNegara" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->WargaNegara->RadioButtonListHtml(FALSE, "x_WargaNegara", 2) ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_WargaNegara">
		<td><span id="elh_student_WargaNegara"><?php echo $student->WargaNegara->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_WargaNegara" id="z_WargaNegara" value="="></span></td>
		<td<?php echo $student->WargaNegara->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_WargaNegara">
<div id="tp_x_WargaNegara" class="ewTemplate"><input type="radio" data-table="student" data-field="x_WargaNegara" data-page="2" data-value-separator="<?php echo $student->WargaNegara->DisplayValueSeparatorAttribute() ?>" name="x_WargaNegara" id="x_WargaNegara" value="{value}"<?php echo $student->WargaNegara->EditAttributes() ?>></div>
<div id="dsl_x_WargaNegara" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->WargaNegara->RadioButtonListHtml(FALSE, "x_WargaNegara", 2) ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->Kelamin->Visible) { // Kelamin ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_Kelamin" class="form-group">
		<label class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_Kelamin"><?php echo $student->Kelamin->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Kelamin" id="z_Kelamin" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->Kelamin->CellAttributes() ?>>
			<span id="el_student_Kelamin">
<div id="tp_x_Kelamin" class="ewTemplate"><input type="radio" data-table="student" data-field="x_Kelamin" data-page="2" data-value-separator="<?php echo $student->Kelamin->DisplayValueSeparatorAttribute() ?>" name="x_Kelamin" id="x_Kelamin" value="{value}"<?php echo $student->Kelamin->EditAttributes() ?>></div>
<div id="dsl_x_Kelamin" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->Kelamin->RadioButtonListHtml(FALSE, "x_Kelamin", 2) ?>
</div></div>
<input type="hidden" name="s_x_Kelamin" id="s_x_Kelamin" value="<?php echo $student->Kelamin->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Kelamin">
		<td><span id="elh_student_Kelamin"><?php echo $student->Kelamin->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Kelamin" id="z_Kelamin" value="="></span></td>
		<td<?php echo $student->Kelamin->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_Kelamin">
<div id="tp_x_Kelamin" class="ewTemplate"><input type="radio" data-table="student" data-field="x_Kelamin" data-page="2" data-value-separator="<?php echo $student->Kelamin->DisplayValueSeparatorAttribute() ?>" name="x_Kelamin" id="x_Kelamin" value="{value}"<?php echo $student->Kelamin->EditAttributes() ?>></div>
<div id="dsl_x_Kelamin" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->Kelamin->RadioButtonListHtml(FALSE, "x_Kelamin", 2) ?>
</div></div>
<input type="hidden" name="s_x_Kelamin" id="s_x_Kelamin" value="<?php echo $student->Kelamin->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TempatLahir->Visible) { // TempatLahir ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_TempatLahir" class="form-group">
		<label for="x_TempatLahir" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_TempatLahir"><?php echo $student->TempatLahir->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TempatLahir" id="z_TempatLahir" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->TempatLahir->CellAttributes() ?>>
			<span id="el_student_TempatLahir">
<input type="text" data-table="student" data-field="x_TempatLahir" data-page="2" name="x_TempatLahir" id="x_TempatLahir" size="30" maxlength="25" placeholder="<?php echo ew_HtmlEncode($student->TempatLahir->getPlaceHolder()) ?>" value="<?php echo $student->TempatLahir->EditValue ?>"<?php echo $student->TempatLahir->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TempatLahir">
		<td><span id="elh_student_TempatLahir"><?php echo $student->TempatLahir->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TempatLahir" id="z_TempatLahir" value="LIKE"></span></td>
		<td<?php echo $student->TempatLahir->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_TempatLahir">
<input type="text" data-table="student" data-field="x_TempatLahir" data-page="2" name="x_TempatLahir" id="x_TempatLahir" size="30" maxlength="25" placeholder="<?php echo ew_HtmlEncode($student->TempatLahir->getPlaceHolder()) ?>" value="<?php echo $student->TempatLahir->EditValue ?>"<?php echo $student->TempatLahir->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TanggalLahir->Visible) { // TanggalLahir ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_TanggalLahir" class="form-group">
		<label for="x_TanggalLahir" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_TanggalLahir"><?php echo $student->TanggalLahir->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanggalLahir" id="z_TanggalLahir" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->TanggalLahir->CellAttributes() ?>>
			<span id="el_student_TanggalLahir">
<input type="text" data-table="student" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($student->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $student->TanggalLahir->EditValue ?>"<?php echo $student->TanggalLahir->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TanggalLahir">
		<td><span id="elh_student_TanggalLahir"><?php echo $student->TanggalLahir->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TanggalLahir" id="z_TanggalLahir" value="="></span></td>
		<td<?php echo $student->TanggalLahir->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_TanggalLahir">
<input type="text" data-table="student" data-field="x_TanggalLahir" data-page="2" name="x_TanggalLahir" id="x_TanggalLahir" placeholder="<?php echo ew_HtmlEncode($student->TanggalLahir->getPlaceHolder()) ?>" value="<?php echo $student->TanggalLahir->EditValue ?>"<?php echo $student->TanggalLahir->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AgamaID->Visible) { // AgamaID ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_AgamaID" class="form-group">
		<label for="x_AgamaID" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_AgamaID"><?php echo $student->AgamaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AgamaID" id="z_AgamaID" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->AgamaID->CellAttributes() ?>>
			<span id="el_student_AgamaID">
<select data-table="student" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $student->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $student->AgamaID->EditAttributes() ?>>
<?php echo $student->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $student->AgamaID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_AgamaID">
		<td><span id="elh_student_AgamaID"><?php echo $student->AgamaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AgamaID" id="z_AgamaID" value="="></span></td>
		<td<?php echo $student->AgamaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_AgamaID">
<select data-table="student" data-field="x_AgamaID" data-page="2" data-value-separator="<?php echo $student->AgamaID->DisplayValueSeparatorAttribute() ?>" id="x_AgamaID" name="x_AgamaID"<?php echo $student->AgamaID->EditAttributes() ?>>
<?php echo $student->AgamaID->SelectOptionListHtml("x_AgamaID") ?>
</select>
<input type="hidden" name="s_x_AgamaID" id="s_x_AgamaID" value="<?php echo $student->AgamaID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->Darah->Visible) { // Darah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_Darah" class="form-group">
		<label class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_Darah"><?php echo $student->Darah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Darah" id="z_Darah" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->Darah->CellAttributes() ?>>
			<span id="el_student_Darah">
<div id="tp_x_Darah" class="ewTemplate"><input type="radio" data-table="student" data-field="x_Darah" data-page="2" data-value-separator="<?php echo $student->Darah->DisplayValueSeparatorAttribute() ?>" name="x_Darah" id="x_Darah" value="{value}"<?php echo $student->Darah->EditAttributes() ?>></div>
<div id="dsl_x_Darah" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->Darah->RadioButtonListHtml(FALSE, "x_Darah", 2) ?>
</div></div>
<input type="hidden" name="s_x_Darah" id="s_x_Darah" value="<?php echo $student->Darah->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Darah">
		<td><span id="elh_student_Darah"><?php echo $student->Darah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Darah" id="z_Darah" value="="></span></td>
		<td<?php echo $student->Darah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_Darah">
<div id="tp_x_Darah" class="ewTemplate"><input type="radio" data-table="student" data-field="x_Darah" data-page="2" data-value-separator="<?php echo $student->Darah->DisplayValueSeparatorAttribute() ?>" name="x_Darah" id="x_Darah" value="{value}"<?php echo $student->Darah->EditAttributes() ?>></div>
<div id="dsl_x_Darah" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->Darah->RadioButtonListHtml(FALSE, "x_Darah", 2) ?>
</div></div>
<input type="hidden" name="s_x_Darah" id="s_x_Darah" value="<?php echo $student->Darah->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->StatusSipil->Visible) { // StatusSipil ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_StatusSipil" class="form-group">
		<label for="x_StatusSipil" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_StatusSipil"><?php echo $student->StatusSipil->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusSipil" id="z_StatusSipil" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->StatusSipil->CellAttributes() ?>>
			<span id="el_student_StatusSipil">
<select data-table="student" data-field="x_StatusSipil" data-page="2" data-value-separator="<?php echo $student->StatusSipil->DisplayValueSeparatorAttribute() ?>" id="x_StatusSipil" name="x_StatusSipil"<?php echo $student->StatusSipil->EditAttributes() ?>>
<?php echo $student->StatusSipil->SelectOptionListHtml("x_StatusSipil") ?>
</select>
<input type="hidden" name="s_x_StatusSipil" id="s_x_StatusSipil" value="<?php echo $student->StatusSipil->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusSipil">
		<td><span id="elh_student_StatusSipil"><?php echo $student->StatusSipil->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusSipil" id="z_StatusSipil" value="LIKE"></span></td>
		<td<?php echo $student->StatusSipil->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_StatusSipil">
<select data-table="student" data-field="x_StatusSipil" data-page="2" data-value-separator="<?php echo $student->StatusSipil->DisplayValueSeparatorAttribute() ?>" id="x_StatusSipil" name="x_StatusSipil"<?php echo $student->StatusSipil->EditAttributes() ?>>
<?php echo $student->StatusSipil->SelectOptionListHtml("x_StatusSipil") ?>
</select>
<input type="hidden" name="s_x_StatusSipil" id="s_x_StatusSipil" value="<?php echo $student->StatusSipil->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AlamatDomisili->Visible) { // AlamatDomisili ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_AlamatDomisili" class="form-group">
		<label for="x_AlamatDomisili" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_AlamatDomisili"><?php echo $student->AlamatDomisili->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_AlamatDomisili" id="z_AlamatDomisili" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->AlamatDomisili->CellAttributes() ?>>
			<span id="el_student_AlamatDomisili">
<input type="text" data-table="student" data-field="x_AlamatDomisili" data-page="2" name="x_AlamatDomisili" id="x_AlamatDomisili" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->AlamatDomisili->getPlaceHolder()) ?>" value="<?php echo $student->AlamatDomisili->EditValue ?>"<?php echo $student->AlamatDomisili->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_AlamatDomisili">
		<td><span id="elh_student_AlamatDomisili"><?php echo $student->AlamatDomisili->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_AlamatDomisili" id="z_AlamatDomisili" value="LIKE"></span></td>
		<td<?php echo $student->AlamatDomisili->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_AlamatDomisili">
<input type="text" data-table="student" data-field="x_AlamatDomisili" data-page="2" name="x_AlamatDomisili" id="x_AlamatDomisili" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->AlamatDomisili->getPlaceHolder()) ?>" value="<?php echo $student->AlamatDomisili->EditValue ?>"<?php echo $student->AlamatDomisili->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->RT->Visible) { // RT ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_RT" class="form-group">
		<label for="x_RT" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_RT"><?php echo $student->RT->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_RT" id="z_RT" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->RT->CellAttributes() ?>>
			<span id="el_student_RT">
<input type="text" data-table="student" data-field="x_RT" data-page="2" name="x_RT" id="x_RT" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RT->getPlaceHolder()) ?>" value="<?php echo $student->RT->EditValue ?>"<?php echo $student->RT->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_RT">
		<td><span id="elh_student_RT"><?php echo $student->RT->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_RT" id="z_RT" value="LIKE"></span></td>
		<td<?php echo $student->RT->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_RT">
<input type="text" data-table="student" data-field="x_RT" data-page="2" name="x_RT" id="x_RT" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RT->getPlaceHolder()) ?>" value="<?php echo $student->RT->EditValue ?>"<?php echo $student->RT->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->RW->Visible) { // RW ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_RW" class="form-group">
		<label for="x_RW" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_RW"><?php echo $student->RW->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_RW" id="z_RW" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->RW->CellAttributes() ?>>
			<span id="el_student_RW">
<input type="text" data-table="student" data-field="x_RW" data-page="2" name="x_RW" id="x_RW" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RW->getPlaceHolder()) ?>" value="<?php echo $student->RW->EditValue ?>"<?php echo $student->RW->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_RW">
		<td><span id="elh_student_RW"><?php echo $student->RW->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_RW" id="z_RW" value="LIKE"></span></td>
		<td<?php echo $student->RW->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_RW">
<input type="text" data-table="student" data-field="x_RW" data-page="2" name="x_RW" id="x_RW" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RW->getPlaceHolder()) ?>" value="<?php echo $student->RW->EditValue ?>"<?php echo $student->RW->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KodePos->Visible) { // KodePos ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_KodePos" class="form-group">
		<label for="x_KodePos" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_KodePos"><?php echo $student->KodePos->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodePos" id="z_KodePos" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->KodePos->CellAttributes() ?>>
			<span id="el_student_KodePos">
<input type="text" data-table="student" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->KodePos->getPlaceHolder()) ?>" value="<?php echo $student->KodePos->EditValue ?>"<?php echo $student->KodePos->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodePos">
		<td><span id="elh_student_KodePos"><?php echo $student->KodePos->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodePos" id="z_KodePos" value="LIKE"></span></td>
		<td<?php echo $student->KodePos->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_KodePos">
<input type="text" data-table="student" data-field="x_KodePos" data-page="2" name="x_KodePos" id="x_KodePos" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->KodePos->getPlaceHolder()) ?>" value="<?php echo $student->KodePos->EditValue ?>"<?php echo $student->KodePos->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->ProvinsiID->Visible) { // ProvinsiID ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_ProvinsiID" class="form-group">
		<label for="x_ProvinsiID" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_ProvinsiID"><?php echo $student->ProvinsiID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProvinsiID" id="z_ProvinsiID" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->ProvinsiID->CellAttributes() ?>>
			<span id="el_student_ProvinsiID">
<?php $student->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $student->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $student->ProvinsiID->EditAttributes() ?>>
<?php echo $student->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $student->ProvinsiID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiID">
		<td><span id="elh_student_ProvinsiID"><?php echo $student->ProvinsiID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProvinsiID" id="z_ProvinsiID" value="LIKE"></span></td>
		<td<?php echo $student->ProvinsiID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_ProvinsiID">
<?php $student->ProvinsiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiID" data-page="2" data-value-separator="<?php echo $student->ProvinsiID->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiID" name="x_ProvinsiID"<?php echo $student->ProvinsiID->EditAttributes() ?>>
<?php echo $student->ProvinsiID->SelectOptionListHtml("x_ProvinsiID") ?>
</select>
<input type="hidden" name="s_x_ProvinsiID" id="s_x_ProvinsiID" value="<?php echo $student->ProvinsiID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KabupatenKotaID->Visible) { // KabupatenKotaID ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_KabupatenKotaID" class="form-group">
		<label for="x_KabupatenKotaID" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_KabupatenKotaID"><?php echo $student->KabupatenKotaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KabupatenKotaID" id="z_KabupatenKotaID" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->KabupatenKotaID->CellAttributes() ?>>
			<span id="el_student_KabupatenKotaID">
<?php $student->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $student->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $student->KabupatenKotaID->EditAttributes() ?>>
<?php echo $student->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $student->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenKotaID">
		<td><span id="elh_student_KabupatenKotaID"><?php echo $student->KabupatenKotaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KabupatenKotaID" id="z_KabupatenKotaID" value="LIKE"></span></td>
		<td<?php echo $student->KabupatenKotaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_KabupatenKotaID">
<?php $student->KabupatenKotaID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenKotaID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenKotaID" data-page="2" data-value-separator="<?php echo $student->KabupatenKotaID->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenKotaID" name="x_KabupatenKotaID"<?php echo $student->KabupatenKotaID->EditAttributes() ?>>
<?php echo $student->KabupatenKotaID->SelectOptionListHtml("x_KabupatenKotaID") ?>
</select>
<input type="hidden" name="s_x_KabupatenKotaID" id="s_x_KabupatenKotaID" value="<?php echo $student->KabupatenKotaID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KecamatanID->Visible) { // KecamatanID ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_KecamatanID" class="form-group">
		<label for="x_KecamatanID" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_KecamatanID"><?php echo $student->KecamatanID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KecamatanID" id="z_KecamatanID" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->KecamatanID->CellAttributes() ?>>
			<span id="el_student_KecamatanID">
<?php $student->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $student->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $student->KecamatanID->EditAttributes() ?>>
<?php echo $student->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $student->KecamatanID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanID">
		<td><span id="elh_student_KecamatanID"><?php echo $student->KecamatanID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KecamatanID" id="z_KecamatanID" value="LIKE"></span></td>
		<td<?php echo $student->KecamatanID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_KecamatanID">
<?php $student->KecamatanID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanID->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanID" data-page="2" data-value-separator="<?php echo $student->KecamatanID->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanID" name="x_KecamatanID"<?php echo $student->KecamatanID->EditAttributes() ?>>
<?php echo $student->KecamatanID->SelectOptionListHtml("x_KecamatanID") ?>
</select>
<input type="hidden" name="s_x_KecamatanID" id="s_x_KecamatanID" value="<?php echo $student->KecamatanID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->DesaID->Visible) { // DesaID ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_DesaID" class="form-group">
		<label for="x_DesaID" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_DesaID"><?php echo $student->DesaID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_DesaID" id="z_DesaID" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->DesaID->CellAttributes() ?>>
			<span id="el_student_DesaID">
<select data-table="student" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $student->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $student->DesaID->EditAttributes() ?>>
<?php echo $student->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $student->DesaID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_DesaID">
		<td><span id="elh_student_DesaID"><?php echo $student->DesaID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_DesaID" id="z_DesaID" value="LIKE"></span></td>
		<td<?php echo $student->DesaID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_DesaID">
<select data-table="student" data-field="x_DesaID" data-page="2" data-value-separator="<?php echo $student->DesaID->DisplayValueSeparatorAttribute() ?>" id="x_DesaID" name="x_DesaID"<?php echo $student->DesaID->EditAttributes() ?>>
<?php echo $student->DesaID->SelectOptionListHtml("x_DesaID") ?>
</select>
<input type="hidden" name="s_x_DesaID" id="s_x_DesaID" value="<?php echo $student->DesaID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AnakKe->Visible) { // AnakKe ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_AnakKe" class="form-group">
		<label for="x_AnakKe" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_AnakKe"><?php echo $student->AnakKe->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AnakKe" id="z_AnakKe" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->AnakKe->CellAttributes() ?>>
			<span id="el_student_AnakKe">
<input type="text" data-table="student" data-field="x_AnakKe" data-page="2" name="x_AnakKe" id="x_AnakKe" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($student->AnakKe->getPlaceHolder()) ?>" value="<?php echo $student->AnakKe->EditValue ?>"<?php echo $student->AnakKe->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_AnakKe">
		<td><span id="elh_student_AnakKe"><?php echo $student->AnakKe->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AnakKe" id="z_AnakKe" value="="></span></td>
		<td<?php echo $student->AnakKe->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_AnakKe">
<input type="text" data-table="student" data-field="x_AnakKe" data-page="2" name="x_AnakKe" id="x_AnakKe" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($student->AnakKe->getPlaceHolder()) ?>" value="<?php echo $student->AnakKe->EditValue ?>"<?php echo $student->AnakKe->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->JumlahSaudara->Visible) { // JumlahSaudara ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_JumlahSaudara" class="form-group">
		<label for="x_JumlahSaudara" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_JumlahSaudara"><?php echo $student->JumlahSaudara->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_JumlahSaudara" id="z_JumlahSaudara" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->JumlahSaudara->CellAttributes() ?>>
			<span id="el_student_JumlahSaudara">
<input type="text" data-table="student" data-field="x_JumlahSaudara" data-page="2" name="x_JumlahSaudara" id="x_JumlahSaudara" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($student->JumlahSaudara->getPlaceHolder()) ?>" value="<?php echo $student->JumlahSaudara->EditValue ?>"<?php echo $student->JumlahSaudara->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_JumlahSaudara">
		<td><span id="elh_student_JumlahSaudara"><?php echo $student->JumlahSaudara->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_JumlahSaudara" id="z_JumlahSaudara" value="="></span></td>
		<td<?php echo $student->JumlahSaudara->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_JumlahSaudara">
<input type="text" data-table="student" data-field="x_JumlahSaudara" data-page="2" name="x_JumlahSaudara" id="x_JumlahSaudara" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($student->JumlahSaudara->getPlaceHolder()) ?>" value="<?php echo $student->JumlahSaudara->EditValue ?>"<?php echo $student->JumlahSaudara->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->Telepon->Visible) { // Telepon ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_Telepon" class="form-group">
		<label for="x_Telepon" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_Telepon"><?php echo $student->Telepon->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Telepon" id="z_Telepon" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->Telepon->CellAttributes() ?>>
			<span id="el_student_Telepon">
<input type="text" data-table="student" data-field="x_Telepon" data-page="2" name="x_Telepon" id="x_Telepon" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Telepon->getPlaceHolder()) ?>" value="<?php echo $student->Telepon->EditValue ?>"<?php echo $student->Telepon->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Telepon">
		<td><span id="elh_student_Telepon"><?php echo $student->Telepon->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Telepon" id="z_Telepon" value="LIKE"></span></td>
		<td<?php echo $student->Telepon->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_Telepon">
<input type="text" data-table="student" data-field="x_Telepon" data-page="2" name="x_Telepon" id="x_Telepon" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->Telepon->getPlaceHolder()) ?>" value="<?php echo $student->Telepon->EditValue ?>"<?php echo $student->Telepon->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->_Email->Visible) { // Email ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r__Email" class="form-group">
		<label for="x__Email" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student__Email"><?php echo $student->_Email->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z__Email" id="z__Email" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->_Email->CellAttributes() ?>>
			<span id="el_student__Email">
<input type="text" data-table="student" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->_Email->getPlaceHolder()) ?>" value="<?php echo $student->_Email->EditValue ?>"<?php echo $student->_Email->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r__Email">
		<td><span id="elh_student__Email"><?php echo $student->_Email->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z__Email" id="z__Email" value="LIKE"></span></td>
		<td<?php echo $student->_Email->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student__Email">
<input type="text" data-table="student" data-field="x__Email" data-page="2" name="x__Email" id="x__Email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->_Email->getPlaceHolder()) ?>" value="<?php echo $student->_Email->EditValue ?>"<?php echo $student->_Email->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $student_search->MultiPages->PageStyle("3") ?>" id="tab_student3">
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_studentsearch3" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($student->NamaAyah->Visible) { // NamaAyah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_NamaAyah" class="form-group">
		<label for="x_NamaAyah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_NamaAyah"><?php echo $student->NamaAyah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NamaAyah" id="z_NamaAyah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->NamaAyah->CellAttributes() ?>>
			<span id="el_student_NamaAyah">
<input type="text" data-table="student" data-field="x_NamaAyah" data-page="3" name="x_NamaAyah" id="x_NamaAyah" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->NamaAyah->getPlaceHolder()) ?>" value="<?php echo $student->NamaAyah->EditValue ?>"<?php echo $student->NamaAyah->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NamaAyah">
		<td><span id="elh_student_NamaAyah"><?php echo $student->NamaAyah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NamaAyah" id="z_NamaAyah" value="LIKE"></span></td>
		<td<?php echo $student->NamaAyah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_NamaAyah">
<input type="text" data-table="student" data-field="x_NamaAyah" data-page="3" name="x_NamaAyah" id="x_NamaAyah" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->NamaAyah->getPlaceHolder()) ?>" value="<?php echo $student->NamaAyah->EditValue ?>"<?php echo $student->NamaAyah->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AgamaAyah->Visible) { // AgamaAyah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_AgamaAyah" class="form-group">
		<label for="x_AgamaAyah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_AgamaAyah"><?php echo $student->AgamaAyah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AgamaAyah" id="z_AgamaAyah" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->AgamaAyah->CellAttributes() ?>>
			<span id="el_student_AgamaAyah">
<select data-table="student" data-field="x_AgamaAyah" data-page="3" data-value-separator="<?php echo $student->AgamaAyah->DisplayValueSeparatorAttribute() ?>" id="x_AgamaAyah" name="x_AgamaAyah"<?php echo $student->AgamaAyah->EditAttributes() ?>>
<?php echo $student->AgamaAyah->SelectOptionListHtml("x_AgamaAyah") ?>
</select>
<input type="hidden" name="s_x_AgamaAyah" id="s_x_AgamaAyah" value="<?php echo $student->AgamaAyah->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_AgamaAyah">
		<td><span id="elh_student_AgamaAyah"><?php echo $student->AgamaAyah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AgamaAyah" id="z_AgamaAyah" value="="></span></td>
		<td<?php echo $student->AgamaAyah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_AgamaAyah">
<select data-table="student" data-field="x_AgamaAyah" data-page="3" data-value-separator="<?php echo $student->AgamaAyah->DisplayValueSeparatorAttribute() ?>" id="x_AgamaAyah" name="x_AgamaAyah"<?php echo $student->AgamaAyah->EditAttributes() ?>>
<?php echo $student->AgamaAyah->SelectOptionListHtml("x_AgamaAyah") ?>
</select>
<input type="hidden" name="s_x_AgamaAyah" id="s_x_AgamaAyah" value="<?php echo $student->AgamaAyah->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->PendidikanAyah->Visible) { // PendidikanAyah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_PendidikanAyah" class="form-group">
		<label for="x_PendidikanAyah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_PendidikanAyah"><?php echo $student->PendidikanAyah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_PendidikanAyah" id="z_PendidikanAyah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->PendidikanAyah->CellAttributes() ?>>
			<span id="el_student_PendidikanAyah">
<select data-table="student" data-field="x_PendidikanAyah" data-page="3" data-value-separator="<?php echo $student->PendidikanAyah->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanAyah" name="x_PendidikanAyah"<?php echo $student->PendidikanAyah->EditAttributes() ?>>
<?php echo $student->PendidikanAyah->SelectOptionListHtml("x_PendidikanAyah") ?>
</select>
<input type="hidden" name="s_x_PendidikanAyah" id="s_x_PendidikanAyah" value="<?php echo $student->PendidikanAyah->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_PendidikanAyah">
		<td><span id="elh_student_PendidikanAyah"><?php echo $student->PendidikanAyah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_PendidikanAyah" id="z_PendidikanAyah" value="LIKE"></span></td>
		<td<?php echo $student->PendidikanAyah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_PendidikanAyah">
<select data-table="student" data-field="x_PendidikanAyah" data-page="3" data-value-separator="<?php echo $student->PendidikanAyah->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanAyah" name="x_PendidikanAyah"<?php echo $student->PendidikanAyah->EditAttributes() ?>>
<?php echo $student->PendidikanAyah->SelectOptionListHtml("x_PendidikanAyah") ?>
</select>
<input type="hidden" name="s_x_PendidikanAyah" id="s_x_PendidikanAyah" value="<?php echo $student->PendidikanAyah->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->PekerjaanAyah->Visible) { // PekerjaanAyah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_PekerjaanAyah" class="form-group">
		<label for="x_PekerjaanAyah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_PekerjaanAyah"><?php echo $student->PekerjaanAyah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_PekerjaanAyah" id="z_PekerjaanAyah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->PekerjaanAyah->CellAttributes() ?>>
			<span id="el_student_PekerjaanAyah">
<select data-table="student" data-field="x_PekerjaanAyah" data-page="3" data-value-separator="<?php echo $student->PekerjaanAyah->DisplayValueSeparatorAttribute() ?>" id="x_PekerjaanAyah" name="x_PekerjaanAyah"<?php echo $student->PekerjaanAyah->EditAttributes() ?>>
<?php echo $student->PekerjaanAyah->SelectOptionListHtml("x_PekerjaanAyah") ?>
</select>
<input type="hidden" name="s_x_PekerjaanAyah" id="s_x_PekerjaanAyah" value="<?php echo $student->PekerjaanAyah->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_PekerjaanAyah">
		<td><span id="elh_student_PekerjaanAyah"><?php echo $student->PekerjaanAyah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_PekerjaanAyah" id="z_PekerjaanAyah" value="LIKE"></span></td>
		<td<?php echo $student->PekerjaanAyah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_PekerjaanAyah">
<select data-table="student" data-field="x_PekerjaanAyah" data-page="3" data-value-separator="<?php echo $student->PekerjaanAyah->DisplayValueSeparatorAttribute() ?>" id="x_PekerjaanAyah" name="x_PekerjaanAyah"<?php echo $student->PekerjaanAyah->EditAttributes() ?>>
<?php echo $student->PekerjaanAyah->SelectOptionListHtml("x_PekerjaanAyah") ?>
</select>
<input type="hidden" name="s_x_PekerjaanAyah" id="s_x_PekerjaanAyah" value="<?php echo $student->PekerjaanAyah->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->HidupAyah->Visible) { // HidupAyah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_HidupAyah" class="form-group">
		<label for="x_HidupAyah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_HidupAyah"><?php echo $student->HidupAyah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_HidupAyah" id="z_HidupAyah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->HidupAyah->CellAttributes() ?>>
			<span id="el_student_HidupAyah">
<select data-table="student" data-field="x_HidupAyah" data-page="3" data-value-separator="<?php echo $student->HidupAyah->DisplayValueSeparatorAttribute() ?>" id="x_HidupAyah" name="x_HidupAyah"<?php echo $student->HidupAyah->EditAttributes() ?>>
<?php echo $student->HidupAyah->SelectOptionListHtml("x_HidupAyah") ?>
</select>
<input type="hidden" name="s_x_HidupAyah" id="s_x_HidupAyah" value="<?php echo $student->HidupAyah->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_HidupAyah">
		<td><span id="elh_student_HidupAyah"><?php echo $student->HidupAyah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_HidupAyah" id="z_HidupAyah" value="LIKE"></span></td>
		<td<?php echo $student->HidupAyah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_HidupAyah">
<select data-table="student" data-field="x_HidupAyah" data-page="3" data-value-separator="<?php echo $student->HidupAyah->DisplayValueSeparatorAttribute() ?>" id="x_HidupAyah" name="x_HidupAyah"<?php echo $student->HidupAyah->EditAttributes() ?>>
<?php echo $student->HidupAyah->SelectOptionListHtml("x_HidupAyah") ?>
</select>
<input type="hidden" name="s_x_HidupAyah" id="s_x_HidupAyah" value="<?php echo $student->HidupAyah->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->NamaIbu->Visible) { // NamaIbu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_NamaIbu" class="form-group">
		<label for="x_NamaIbu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_NamaIbu"><?php echo $student->NamaIbu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NamaIbu" id="z_NamaIbu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->NamaIbu->CellAttributes() ?>>
			<span id="el_student_NamaIbu">
<input type="text" data-table="student" data-field="x_NamaIbu" data-page="3" name="x_NamaIbu" id="x_NamaIbu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->NamaIbu->getPlaceHolder()) ?>" value="<?php echo $student->NamaIbu->EditValue ?>"<?php echo $student->NamaIbu->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NamaIbu">
		<td><span id="elh_student_NamaIbu"><?php echo $student->NamaIbu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NamaIbu" id="z_NamaIbu" value="LIKE"></span></td>
		<td<?php echo $student->NamaIbu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_NamaIbu">
<input type="text" data-table="student" data-field="x_NamaIbu" data-page="3" name="x_NamaIbu" id="x_NamaIbu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->NamaIbu->getPlaceHolder()) ?>" value="<?php echo $student->NamaIbu->EditValue ?>"<?php echo $student->NamaIbu->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AgamaIbu->Visible) { // AgamaIbu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_AgamaIbu" class="form-group">
		<label for="x_AgamaIbu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_AgamaIbu"><?php echo $student->AgamaIbu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AgamaIbu" id="z_AgamaIbu" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->AgamaIbu->CellAttributes() ?>>
			<span id="el_student_AgamaIbu">
<select data-table="student" data-field="x_AgamaIbu" data-page="3" data-value-separator="<?php echo $student->AgamaIbu->DisplayValueSeparatorAttribute() ?>" id="x_AgamaIbu" name="x_AgamaIbu"<?php echo $student->AgamaIbu->EditAttributes() ?>>
<?php echo $student->AgamaIbu->SelectOptionListHtml("x_AgamaIbu") ?>
</select>
<input type="hidden" name="s_x_AgamaIbu" id="s_x_AgamaIbu" value="<?php echo $student->AgamaIbu->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_AgamaIbu">
		<td><span id="elh_student_AgamaIbu"><?php echo $student->AgamaIbu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AgamaIbu" id="z_AgamaIbu" value="="></span></td>
		<td<?php echo $student->AgamaIbu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_AgamaIbu">
<select data-table="student" data-field="x_AgamaIbu" data-page="3" data-value-separator="<?php echo $student->AgamaIbu->DisplayValueSeparatorAttribute() ?>" id="x_AgamaIbu" name="x_AgamaIbu"<?php echo $student->AgamaIbu->EditAttributes() ?>>
<?php echo $student->AgamaIbu->SelectOptionListHtml("x_AgamaIbu") ?>
</select>
<input type="hidden" name="s_x_AgamaIbu" id="s_x_AgamaIbu" value="<?php echo $student->AgamaIbu->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->PendidikanIbu->Visible) { // PendidikanIbu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_PendidikanIbu" class="form-group">
		<label for="x_PendidikanIbu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_PendidikanIbu"><?php echo $student->PendidikanIbu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_PendidikanIbu" id="z_PendidikanIbu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->PendidikanIbu->CellAttributes() ?>>
			<span id="el_student_PendidikanIbu">
<select data-table="student" data-field="x_PendidikanIbu" data-page="3" data-value-separator="<?php echo $student->PendidikanIbu->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanIbu" name="x_PendidikanIbu"<?php echo $student->PendidikanIbu->EditAttributes() ?>>
<?php echo $student->PendidikanIbu->SelectOptionListHtml("x_PendidikanIbu") ?>
</select>
<input type="hidden" name="s_x_PendidikanIbu" id="s_x_PendidikanIbu" value="<?php echo $student->PendidikanIbu->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_PendidikanIbu">
		<td><span id="elh_student_PendidikanIbu"><?php echo $student->PendidikanIbu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_PendidikanIbu" id="z_PendidikanIbu" value="LIKE"></span></td>
		<td<?php echo $student->PendidikanIbu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_PendidikanIbu">
<select data-table="student" data-field="x_PendidikanIbu" data-page="3" data-value-separator="<?php echo $student->PendidikanIbu->DisplayValueSeparatorAttribute() ?>" id="x_PendidikanIbu" name="x_PendidikanIbu"<?php echo $student->PendidikanIbu->EditAttributes() ?>>
<?php echo $student->PendidikanIbu->SelectOptionListHtml("x_PendidikanIbu") ?>
</select>
<input type="hidden" name="s_x_PendidikanIbu" id="s_x_PendidikanIbu" value="<?php echo $student->PendidikanIbu->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->PekerjaanIbu->Visible) { // PekerjaanIbu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_PekerjaanIbu" class="form-group">
		<label for="x_PekerjaanIbu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_PekerjaanIbu"><?php echo $student->PekerjaanIbu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_PekerjaanIbu" id="z_PekerjaanIbu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->PekerjaanIbu->CellAttributes() ?>>
			<span id="el_student_PekerjaanIbu">
<select data-table="student" data-field="x_PekerjaanIbu" data-page="3" data-value-separator="<?php echo $student->PekerjaanIbu->DisplayValueSeparatorAttribute() ?>" id="x_PekerjaanIbu" name="x_PekerjaanIbu"<?php echo $student->PekerjaanIbu->EditAttributes() ?>>
<?php echo $student->PekerjaanIbu->SelectOptionListHtml("x_PekerjaanIbu") ?>
</select>
<input type="hidden" name="s_x_PekerjaanIbu" id="s_x_PekerjaanIbu" value="<?php echo $student->PekerjaanIbu->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_PekerjaanIbu">
		<td><span id="elh_student_PekerjaanIbu"><?php echo $student->PekerjaanIbu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_PekerjaanIbu" id="z_PekerjaanIbu" value="LIKE"></span></td>
		<td<?php echo $student->PekerjaanIbu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_PekerjaanIbu">
<select data-table="student" data-field="x_PekerjaanIbu" data-page="3" data-value-separator="<?php echo $student->PekerjaanIbu->DisplayValueSeparatorAttribute() ?>" id="x_PekerjaanIbu" name="x_PekerjaanIbu"<?php echo $student->PekerjaanIbu->EditAttributes() ?>>
<?php echo $student->PekerjaanIbu->SelectOptionListHtml("x_PekerjaanIbu") ?>
</select>
<input type="hidden" name="s_x_PekerjaanIbu" id="s_x_PekerjaanIbu" value="<?php echo $student->PekerjaanIbu->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->HidupIbu->Visible) { // HidupIbu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_HidupIbu" class="form-group">
		<label for="x_HidupIbu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_HidupIbu"><?php echo $student->HidupIbu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_HidupIbu" id="z_HidupIbu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->HidupIbu->CellAttributes() ?>>
			<span id="el_student_HidupIbu">
<select data-table="student" data-field="x_HidupIbu" data-page="3" data-value-separator="<?php echo $student->HidupIbu->DisplayValueSeparatorAttribute() ?>" id="x_HidupIbu" name="x_HidupIbu"<?php echo $student->HidupIbu->EditAttributes() ?>>
<?php echo $student->HidupIbu->SelectOptionListHtml("x_HidupIbu") ?>
</select>
<input type="hidden" name="s_x_HidupIbu" id="s_x_HidupIbu" value="<?php echo $student->HidupIbu->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_HidupIbu">
		<td><span id="elh_student_HidupIbu"><?php echo $student->HidupIbu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_HidupIbu" id="z_HidupIbu" value="LIKE"></span></td>
		<td<?php echo $student->HidupIbu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_HidupIbu">
<select data-table="student" data-field="x_HidupIbu" data-page="3" data-value-separator="<?php echo $student->HidupIbu->DisplayValueSeparatorAttribute() ?>" id="x_HidupIbu" name="x_HidupIbu"<?php echo $student->HidupIbu->EditAttributes() ?>>
<?php echo $student->HidupIbu->SelectOptionListHtml("x_HidupIbu") ?>
</select>
<input type="hidden" name="s_x_HidupIbu" id="s_x_HidupIbu" value="<?php echo $student->HidupIbu->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AlamatOrtu->Visible) { // AlamatOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_AlamatOrtu" class="form-group">
		<label for="x_AlamatOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_AlamatOrtu"><?php echo $student->AlamatOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_AlamatOrtu" id="z_AlamatOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->AlamatOrtu->CellAttributes() ?>>
			<span id="el_student_AlamatOrtu">
<input type="text" data-table="student" data-field="x_AlamatOrtu" data-page="3" name="x_AlamatOrtu" id="x_AlamatOrtu" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->AlamatOrtu->getPlaceHolder()) ?>" value="<?php echo $student->AlamatOrtu->EditValue ?>"<?php echo $student->AlamatOrtu->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_AlamatOrtu">
		<td><span id="elh_student_AlamatOrtu"><?php echo $student->AlamatOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_AlamatOrtu" id="z_AlamatOrtu" value="LIKE"></span></td>
		<td<?php echo $student->AlamatOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_AlamatOrtu">
<input type="text" data-table="student" data-field="x_AlamatOrtu" data-page="3" name="x_AlamatOrtu" id="x_AlamatOrtu" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->AlamatOrtu->getPlaceHolder()) ?>" value="<?php echo $student->AlamatOrtu->EditValue ?>"<?php echo $student->AlamatOrtu->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->RTOrtu->Visible) { // RTOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_RTOrtu" class="form-group">
		<label for="x_RTOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_RTOrtu"><?php echo $student->RTOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_RTOrtu" id="z_RTOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->RTOrtu->CellAttributes() ?>>
			<span id="el_student_RTOrtu">
<input type="text" data-table="student" data-field="x_RTOrtu" data-page="3" name="x_RTOrtu" id="x_RTOrtu" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RTOrtu->getPlaceHolder()) ?>" value="<?php echo $student->RTOrtu->EditValue ?>"<?php echo $student->RTOrtu->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_RTOrtu">
		<td><span id="elh_student_RTOrtu"><?php echo $student->RTOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_RTOrtu" id="z_RTOrtu" value="LIKE"></span></td>
		<td<?php echo $student->RTOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_RTOrtu">
<input type="text" data-table="student" data-field="x_RTOrtu" data-page="3" name="x_RTOrtu" id="x_RTOrtu" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RTOrtu->getPlaceHolder()) ?>" value="<?php echo $student->RTOrtu->EditValue ?>"<?php echo $student->RTOrtu->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->RWOrtu->Visible) { // RWOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_RWOrtu" class="form-group">
		<label for="x_RWOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_RWOrtu"><?php echo $student->RWOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_RWOrtu" id="z_RWOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->RWOrtu->CellAttributes() ?>>
			<span id="el_student_RWOrtu">
<input type="text" data-table="student" data-field="x_RWOrtu" data-page="3" name="x_RWOrtu" id="x_RWOrtu" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RWOrtu->getPlaceHolder()) ?>" value="<?php echo $student->RWOrtu->EditValue ?>"<?php echo $student->RWOrtu->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_RWOrtu">
		<td><span id="elh_student_RWOrtu"><?php echo $student->RWOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_RWOrtu" id="z_RWOrtu" value="LIKE"></span></td>
		<td<?php echo $student->RWOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_RWOrtu">
<input type="text" data-table="student" data-field="x_RWOrtu" data-page="3" name="x_RWOrtu" id="x_RWOrtu" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($student->RWOrtu->getPlaceHolder()) ?>" value="<?php echo $student->RWOrtu->EditValue ?>"<?php echo $student->RWOrtu->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KodePosOrtu->Visible) { // KodePosOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_KodePosOrtu" class="form-group">
		<label for="x_KodePosOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_KodePosOrtu"><?php echo $student->KodePosOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodePosOrtu" id="z_KodePosOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->KodePosOrtu->CellAttributes() ?>>
			<span id="el_student_KodePosOrtu">
<input type="text" data-table="student" data-field="x_KodePosOrtu" data-page="3" name="x_KodePosOrtu" id="x_KodePosOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->KodePosOrtu->getPlaceHolder()) ?>" value="<?php echo $student->KodePosOrtu->EditValue ?>"<?php echo $student->KodePosOrtu->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KodePosOrtu">
		<td><span id="elh_student_KodePosOrtu"><?php echo $student->KodePosOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KodePosOrtu" id="z_KodePosOrtu" value="LIKE"></span></td>
		<td<?php echo $student->KodePosOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_KodePosOrtu">
<input type="text" data-table="student" data-field="x_KodePosOrtu" data-page="3" name="x_KodePosOrtu" id="x_KodePosOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->KodePosOrtu->getPlaceHolder()) ?>" value="<?php echo $student->KodePosOrtu->EditValue ?>"<?php echo $student->KodePosOrtu->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->ProvinsiIDOrtu->Visible) { // ProvinsiIDOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_ProvinsiIDOrtu" class="form-group">
		<label for="x_ProvinsiIDOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_ProvinsiIDOrtu"><?php echo $student->ProvinsiIDOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProvinsiIDOrtu" id="z_ProvinsiIDOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->ProvinsiIDOrtu->CellAttributes() ?>>
			<span id="el_student_ProvinsiIDOrtu">
<?php $student->ProvinsiIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiIDOrtu" data-page="3" data-value-separator="<?php echo $student->ProvinsiIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiIDOrtu" name="x_ProvinsiIDOrtu"<?php echo $student->ProvinsiIDOrtu->EditAttributes() ?>>
<?php echo $student->ProvinsiIDOrtu->SelectOptionListHtml("x_ProvinsiIDOrtu") ?>
</select>
<input type="hidden" name="s_x_ProvinsiIDOrtu" id="s_x_ProvinsiIDOrtu" value="<?php echo $student->ProvinsiIDOrtu->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiIDOrtu">
		<td><span id="elh_student_ProvinsiIDOrtu"><?php echo $student->ProvinsiIDOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProvinsiIDOrtu" id="z_ProvinsiIDOrtu" value="LIKE"></span></td>
		<td<?php echo $student->ProvinsiIDOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_ProvinsiIDOrtu">
<?php $student->ProvinsiIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiIDOrtu" data-page="3" data-value-separator="<?php echo $student->ProvinsiIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiIDOrtu" name="x_ProvinsiIDOrtu"<?php echo $student->ProvinsiIDOrtu->EditAttributes() ?>>
<?php echo $student->ProvinsiIDOrtu->SelectOptionListHtml("x_ProvinsiIDOrtu") ?>
</select>
<input type="hidden" name="s_x_ProvinsiIDOrtu" id="s_x_ProvinsiIDOrtu" value="<?php echo $student->ProvinsiIDOrtu->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KabupatenIDOrtu->Visible) { // KabupatenIDOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_KabupatenIDOrtu" class="form-group">
		<label for="x_KabupatenIDOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_KabupatenIDOrtu"><?php echo $student->KabupatenIDOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KabupatenIDOrtu" id="z_KabupatenIDOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->KabupatenIDOrtu->CellAttributes() ?>>
			<span id="el_student_KabupatenIDOrtu">
<?php $student->KabupatenIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenIDOrtu" data-page="3" data-value-separator="<?php echo $student->KabupatenIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenIDOrtu" name="x_KabupatenIDOrtu"<?php echo $student->KabupatenIDOrtu->EditAttributes() ?>>
<?php echo $student->KabupatenIDOrtu->SelectOptionListHtml("x_KabupatenIDOrtu") ?>
</select>
<input type="hidden" name="s_x_KabupatenIDOrtu" id="s_x_KabupatenIDOrtu" value="<?php echo $student->KabupatenIDOrtu->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenIDOrtu">
		<td><span id="elh_student_KabupatenIDOrtu"><?php echo $student->KabupatenIDOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KabupatenIDOrtu" id="z_KabupatenIDOrtu" value="LIKE"></span></td>
		<td<?php echo $student->KabupatenIDOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_KabupatenIDOrtu">
<?php $student->KabupatenIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenIDOrtu" data-page="3" data-value-separator="<?php echo $student->KabupatenIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenIDOrtu" name="x_KabupatenIDOrtu"<?php echo $student->KabupatenIDOrtu->EditAttributes() ?>>
<?php echo $student->KabupatenIDOrtu->SelectOptionListHtml("x_KabupatenIDOrtu") ?>
</select>
<input type="hidden" name="s_x_KabupatenIDOrtu" id="s_x_KabupatenIDOrtu" value="<?php echo $student->KabupatenIDOrtu->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KecamatanIDOrtu->Visible) { // KecamatanIDOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_KecamatanIDOrtu" class="form-group">
		<label for="x_KecamatanIDOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_KecamatanIDOrtu"><?php echo $student->KecamatanIDOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KecamatanIDOrtu" id="z_KecamatanIDOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->KecamatanIDOrtu->CellAttributes() ?>>
			<span id="el_student_KecamatanIDOrtu">
<?php $student->KecamatanIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanIDOrtu" data-page="3" data-value-separator="<?php echo $student->KecamatanIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanIDOrtu" name="x_KecamatanIDOrtu"<?php echo $student->KecamatanIDOrtu->EditAttributes() ?>>
<?php echo $student->KecamatanIDOrtu->SelectOptionListHtml("x_KecamatanIDOrtu") ?>
</select>
<input type="hidden" name="s_x_KecamatanIDOrtu" id="s_x_KecamatanIDOrtu" value="<?php echo $student->KecamatanIDOrtu->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanIDOrtu">
		<td><span id="elh_student_KecamatanIDOrtu"><?php echo $student->KecamatanIDOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KecamatanIDOrtu" id="z_KecamatanIDOrtu" value="LIKE"></span></td>
		<td<?php echo $student->KecamatanIDOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_KecamatanIDOrtu">
<?php $student->KecamatanIDOrtu->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanIDOrtu->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanIDOrtu" data-page="3" data-value-separator="<?php echo $student->KecamatanIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanIDOrtu" name="x_KecamatanIDOrtu"<?php echo $student->KecamatanIDOrtu->EditAttributes() ?>>
<?php echo $student->KecamatanIDOrtu->SelectOptionListHtml("x_KecamatanIDOrtu") ?>
</select>
<input type="hidden" name="s_x_KecamatanIDOrtu" id="s_x_KecamatanIDOrtu" value="<?php echo $student->KecamatanIDOrtu->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->DesaIDOrtu->Visible) { // DesaIDOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_DesaIDOrtu" class="form-group">
		<label for="x_DesaIDOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_DesaIDOrtu"><?php echo $student->DesaIDOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_DesaIDOrtu" id="z_DesaIDOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->DesaIDOrtu->CellAttributes() ?>>
			<span id="el_student_DesaIDOrtu">
<select data-table="student" data-field="x_DesaIDOrtu" data-page="3" data-value-separator="<?php echo $student->DesaIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_DesaIDOrtu" name="x_DesaIDOrtu"<?php echo $student->DesaIDOrtu->EditAttributes() ?>>
<?php echo $student->DesaIDOrtu->SelectOptionListHtml("x_DesaIDOrtu") ?>
</select>
<input type="hidden" name="s_x_DesaIDOrtu" id="s_x_DesaIDOrtu" value="<?php echo $student->DesaIDOrtu->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_DesaIDOrtu">
		<td><span id="elh_student_DesaIDOrtu"><?php echo $student->DesaIDOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_DesaIDOrtu" id="z_DesaIDOrtu" value="LIKE"></span></td>
		<td<?php echo $student->DesaIDOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_DesaIDOrtu">
<select data-table="student" data-field="x_DesaIDOrtu" data-page="3" data-value-separator="<?php echo $student->DesaIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_DesaIDOrtu" name="x_DesaIDOrtu"<?php echo $student->DesaIDOrtu->EditAttributes() ?>>
<?php echo $student->DesaIDOrtu->SelectOptionListHtml("x_DesaIDOrtu") ?>
</select>
<input type="hidden" name="s_x_DesaIDOrtu" id="s_x_DesaIDOrtu" value="<?php echo $student->DesaIDOrtu->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->NegaraIDOrtu->Visible) { // NegaraIDOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_NegaraIDOrtu" class="form-group">
		<label for="x_NegaraIDOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_NegaraIDOrtu"><?php echo $student->NegaraIDOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NegaraIDOrtu" id="z_NegaraIDOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->NegaraIDOrtu->CellAttributes() ?>>
			<span id="el_student_NegaraIDOrtu">
<select data-table="student" data-field="x_NegaraIDOrtu" data-page="3" data-value-separator="<?php echo $student->NegaraIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_NegaraIDOrtu" name="x_NegaraIDOrtu"<?php echo $student->NegaraIDOrtu->EditAttributes() ?>>
<?php echo $student->NegaraIDOrtu->SelectOptionListHtml("x_NegaraIDOrtu") ?>
</select>
<input type="hidden" name="s_x_NegaraIDOrtu" id="s_x_NegaraIDOrtu" value="<?php echo $student->NegaraIDOrtu->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NegaraIDOrtu">
		<td><span id="elh_student_NegaraIDOrtu"><?php echo $student->NegaraIDOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NegaraIDOrtu" id="z_NegaraIDOrtu" value="LIKE"></span></td>
		<td<?php echo $student->NegaraIDOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_NegaraIDOrtu">
<select data-table="student" data-field="x_NegaraIDOrtu" data-page="3" data-value-separator="<?php echo $student->NegaraIDOrtu->DisplayValueSeparatorAttribute() ?>" id="x_NegaraIDOrtu" name="x_NegaraIDOrtu"<?php echo $student->NegaraIDOrtu->EditAttributes() ?>>
<?php echo $student->NegaraIDOrtu->SelectOptionListHtml("x_NegaraIDOrtu") ?>
</select>
<input type="hidden" name="s_x_NegaraIDOrtu" id="s_x_NegaraIDOrtu" value="<?php echo $student->NegaraIDOrtu->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TeleponOrtu->Visible) { // TeleponOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_TeleponOrtu" class="form-group">
		<label for="x_TeleponOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_TeleponOrtu"><?php echo $student->TeleponOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TeleponOrtu" id="z_TeleponOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->TeleponOrtu->CellAttributes() ?>>
			<span id="el_student_TeleponOrtu">
<input type="text" data-table="student" data-field="x_TeleponOrtu" data-page="3" name="x_TeleponOrtu" id="x_TeleponOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->TeleponOrtu->getPlaceHolder()) ?>" value="<?php echo $student->TeleponOrtu->EditValue ?>"<?php echo $student->TeleponOrtu->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TeleponOrtu">
		<td><span id="elh_student_TeleponOrtu"><?php echo $student->TeleponOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TeleponOrtu" id="z_TeleponOrtu" value="LIKE"></span></td>
		<td<?php echo $student->TeleponOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_TeleponOrtu">
<input type="text" data-table="student" data-field="x_TeleponOrtu" data-page="3" name="x_TeleponOrtu" id="x_TeleponOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->TeleponOrtu->getPlaceHolder()) ?>" value="<?php echo $student->TeleponOrtu->EditValue ?>"<?php echo $student->TeleponOrtu->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->HandphoneOrtu->Visible) { // HandphoneOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_HandphoneOrtu" class="form-group">
		<label for="x_HandphoneOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_HandphoneOrtu"><?php echo $student->HandphoneOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_HandphoneOrtu" id="z_HandphoneOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->HandphoneOrtu->CellAttributes() ?>>
			<span id="el_student_HandphoneOrtu">
<input type="text" data-table="student" data-field="x_HandphoneOrtu" data-page="3" name="x_HandphoneOrtu" id="x_HandphoneOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->HandphoneOrtu->getPlaceHolder()) ?>" value="<?php echo $student->HandphoneOrtu->EditValue ?>"<?php echo $student->HandphoneOrtu->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_HandphoneOrtu">
		<td><span id="elh_student_HandphoneOrtu"><?php echo $student->HandphoneOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_HandphoneOrtu" id="z_HandphoneOrtu" value="LIKE"></span></td>
		<td<?php echo $student->HandphoneOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_HandphoneOrtu">
<input type="text" data-table="student" data-field="x_HandphoneOrtu" data-page="3" name="x_HandphoneOrtu" id="x_HandphoneOrtu" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->HandphoneOrtu->getPlaceHolder()) ?>" value="<?php echo $student->HandphoneOrtu->EditValue ?>"<?php echo $student->HandphoneOrtu->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->EmailOrtu->Visible) { // EmailOrtu ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_EmailOrtu" class="form-group">
		<label for="x_EmailOrtu" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_EmailOrtu"><?php echo $student->EmailOrtu->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_EmailOrtu" id="z_EmailOrtu" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->EmailOrtu->CellAttributes() ?>>
			<span id="el_student_EmailOrtu">
<input type="text" data-table="student" data-field="x_EmailOrtu" data-page="3" name="x_EmailOrtu" id="x_EmailOrtu" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($student->EmailOrtu->getPlaceHolder()) ?>" value="<?php echo $student->EmailOrtu->EditValue ?>"<?php echo $student->EmailOrtu->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_EmailOrtu">
		<td><span id="elh_student_EmailOrtu"><?php echo $student->EmailOrtu->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_EmailOrtu" id="z_EmailOrtu" value="LIKE"></span></td>
		<td<?php echo $student->EmailOrtu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_EmailOrtu">
<input type="text" data-table="student" data-field="x_EmailOrtu" data-page="3" name="x_EmailOrtu" id="x_EmailOrtu" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($student->EmailOrtu->getPlaceHolder()) ?>" value="<?php echo $student->EmailOrtu->EditValue ?>"<?php echo $student->EmailOrtu->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $student_search->MultiPages->PageStyle("4") ?>" id="tab_student4">
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_studentsearch4" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($student->AsalSekolah->Visible) { // AsalSekolah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_AsalSekolah" class="form-group">
		<label for="x_AsalSekolah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_AsalSekolah"><?php echo $student->AsalSekolah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_AsalSekolah" id="z_AsalSekolah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->AsalSekolah->CellAttributes() ?>>
			<span id="el_student_AsalSekolah">
<input type="text" data-table="student" data-field="x_AsalSekolah" data-page="4" name="x_AsalSekolah" id="x_AsalSekolah" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($student->AsalSekolah->getPlaceHolder()) ?>" value="<?php echo $student->AsalSekolah->EditValue ?>"<?php echo $student->AsalSekolah->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_AsalSekolah">
		<td><span id="elh_student_AsalSekolah"><?php echo $student->AsalSekolah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_AsalSekolah" id="z_AsalSekolah" value="LIKE"></span></td>
		<td<?php echo $student->AsalSekolah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_AsalSekolah">
<input type="text" data-table="student" data-field="x_AsalSekolah" data-page="4" name="x_AsalSekolah" id="x_AsalSekolah" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($student->AsalSekolah->getPlaceHolder()) ?>" value="<?php echo $student->AsalSekolah->EditValue ?>"<?php echo $student->AsalSekolah->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->AlamatSekolah->Visible) { // AlamatSekolah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_AlamatSekolah" class="form-group">
		<label for="x_AlamatSekolah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_AlamatSekolah"><?php echo $student->AlamatSekolah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_AlamatSekolah" id="z_AlamatSekolah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->AlamatSekolah->CellAttributes() ?>>
			<span id="el_student_AlamatSekolah">
<input type="text" data-table="student" data-field="x_AlamatSekolah" data-page="4" name="x_AlamatSekolah" id="x_AlamatSekolah" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->AlamatSekolah->getPlaceHolder()) ?>" value="<?php echo $student->AlamatSekolah->EditValue ?>"<?php echo $student->AlamatSekolah->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_AlamatSekolah">
		<td><span id="elh_student_AlamatSekolah"><?php echo $student->AlamatSekolah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_AlamatSekolah" id="z_AlamatSekolah" value="LIKE"></span></td>
		<td<?php echo $student->AlamatSekolah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_AlamatSekolah">
<input type="text" data-table="student" data-field="x_AlamatSekolah" data-page="4" name="x_AlamatSekolah" id="x_AlamatSekolah" maxlength="255" placeholder="<?php echo ew_HtmlEncode($student->AlamatSekolah->getPlaceHolder()) ?>" value="<?php echo $student->AlamatSekolah->EditValue ?>"<?php echo $student->AlamatSekolah->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->ProvinsiIDSekolah->Visible) { // ProvinsiIDSekolah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_ProvinsiIDSekolah" class="form-group">
		<label for="x_ProvinsiIDSekolah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_ProvinsiIDSekolah"><?php echo $student->ProvinsiIDSekolah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProvinsiIDSekolah" id="z_ProvinsiIDSekolah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->ProvinsiIDSekolah->CellAttributes() ?>>
			<span id="el_student_ProvinsiIDSekolah">
<?php $student->ProvinsiIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiIDSekolah" data-page="4" data-value-separator="<?php echo $student->ProvinsiIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiIDSekolah" name="x_ProvinsiIDSekolah"<?php echo $student->ProvinsiIDSekolah->EditAttributes() ?>>
<?php echo $student->ProvinsiIDSekolah->SelectOptionListHtml("x_ProvinsiIDSekolah") ?>
</select>
<input type="hidden" name="s_x_ProvinsiIDSekolah" id="s_x_ProvinsiIDSekolah" value="<?php echo $student->ProvinsiIDSekolah->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProvinsiIDSekolah">
		<td><span id="elh_student_ProvinsiIDSekolah"><?php echo $student->ProvinsiIDSekolah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProvinsiIDSekolah" id="z_ProvinsiIDSekolah" value="LIKE"></span></td>
		<td<?php echo $student->ProvinsiIDSekolah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_ProvinsiIDSekolah">
<?php $student->ProvinsiIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->ProvinsiIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_ProvinsiIDSekolah" data-page="4" data-value-separator="<?php echo $student->ProvinsiIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_ProvinsiIDSekolah" name="x_ProvinsiIDSekolah"<?php echo $student->ProvinsiIDSekolah->EditAttributes() ?>>
<?php echo $student->ProvinsiIDSekolah->SelectOptionListHtml("x_ProvinsiIDSekolah") ?>
</select>
<input type="hidden" name="s_x_ProvinsiIDSekolah" id="s_x_ProvinsiIDSekolah" value="<?php echo $student->ProvinsiIDSekolah->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KabupatenIDSekolah->Visible) { // KabupatenIDSekolah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_KabupatenIDSekolah" class="form-group">
		<label for="x_KabupatenIDSekolah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_KabupatenIDSekolah"><?php echo $student->KabupatenIDSekolah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KabupatenIDSekolah" id="z_KabupatenIDSekolah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->KabupatenIDSekolah->CellAttributes() ?>>
			<span id="el_student_KabupatenIDSekolah">
<?php $student->KabupatenIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenIDSekolah" data-page="4" data-value-separator="<?php echo $student->KabupatenIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenIDSekolah" name="x_KabupatenIDSekolah"<?php echo $student->KabupatenIDSekolah->EditAttributes() ?>>
<?php echo $student->KabupatenIDSekolah->SelectOptionListHtml("x_KabupatenIDSekolah") ?>
</select>
<input type="hidden" name="s_x_KabupatenIDSekolah" id="s_x_KabupatenIDSekolah" value="<?php echo $student->KabupatenIDSekolah->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KabupatenIDSekolah">
		<td><span id="elh_student_KabupatenIDSekolah"><?php echo $student->KabupatenIDSekolah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KabupatenIDSekolah" id="z_KabupatenIDSekolah" value="LIKE"></span></td>
		<td<?php echo $student->KabupatenIDSekolah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_KabupatenIDSekolah">
<?php $student->KabupatenIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KabupatenIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KabupatenIDSekolah" data-page="4" data-value-separator="<?php echo $student->KabupatenIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_KabupatenIDSekolah" name="x_KabupatenIDSekolah"<?php echo $student->KabupatenIDSekolah->EditAttributes() ?>>
<?php echo $student->KabupatenIDSekolah->SelectOptionListHtml("x_KabupatenIDSekolah") ?>
</select>
<input type="hidden" name="s_x_KabupatenIDSekolah" id="s_x_KabupatenIDSekolah" value="<?php echo $student->KabupatenIDSekolah->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->KecamatanIDSekolah->Visible) { // KecamatanIDSekolah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_KecamatanIDSekolah" class="form-group">
		<label for="x_KecamatanIDSekolah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_KecamatanIDSekolah"><?php echo $student->KecamatanIDSekolah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KecamatanIDSekolah" id="z_KecamatanIDSekolah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->KecamatanIDSekolah->CellAttributes() ?>>
			<span id="el_student_KecamatanIDSekolah">
<?php $student->KecamatanIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanIDSekolah" data-page="4" data-value-separator="<?php echo $student->KecamatanIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanIDSekolah" name="x_KecamatanIDSekolah"<?php echo $student->KecamatanIDSekolah->EditAttributes() ?>>
<?php echo $student->KecamatanIDSekolah->SelectOptionListHtml("x_KecamatanIDSekolah") ?>
</select>
<input type="hidden" name="s_x_KecamatanIDSekolah" id="s_x_KecamatanIDSekolah" value="<?php echo $student->KecamatanIDSekolah->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KecamatanIDSekolah">
		<td><span id="elh_student_KecamatanIDSekolah"><?php echo $student->KecamatanIDSekolah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KecamatanIDSekolah" id="z_KecamatanIDSekolah" value="LIKE"></span></td>
		<td<?php echo $student->KecamatanIDSekolah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_KecamatanIDSekolah">
<?php $student->KecamatanIDSekolah->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$student->KecamatanIDSekolah->EditAttrs["onchange"]; ?>
<select data-table="student" data-field="x_KecamatanIDSekolah" data-page="4" data-value-separator="<?php echo $student->KecamatanIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_KecamatanIDSekolah" name="x_KecamatanIDSekolah"<?php echo $student->KecamatanIDSekolah->EditAttributes() ?>>
<?php echo $student->KecamatanIDSekolah->SelectOptionListHtml("x_KecamatanIDSekolah") ?>
</select>
<input type="hidden" name="s_x_KecamatanIDSekolah" id="s_x_KecamatanIDSekolah" value="<?php echo $student->KecamatanIDSekolah->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->DesaIDSekolah->Visible) { // DesaIDSekolah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_DesaIDSekolah" class="form-group">
		<label for="x_DesaIDSekolah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_DesaIDSekolah"><?php echo $student->DesaIDSekolah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_DesaIDSekolah" id="z_DesaIDSekolah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->DesaIDSekolah->CellAttributes() ?>>
			<span id="el_student_DesaIDSekolah">
<select data-table="student" data-field="x_DesaIDSekolah" data-page="4" data-value-separator="<?php echo $student->DesaIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_DesaIDSekolah" name="x_DesaIDSekolah"<?php echo $student->DesaIDSekolah->EditAttributes() ?>>
<?php echo $student->DesaIDSekolah->SelectOptionListHtml("x_DesaIDSekolah") ?>
</select>
<input type="hidden" name="s_x_DesaIDSekolah" id="s_x_DesaIDSekolah" value="<?php echo $student->DesaIDSekolah->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_DesaIDSekolah">
		<td><span id="elh_student_DesaIDSekolah"><?php echo $student->DesaIDSekolah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_DesaIDSekolah" id="z_DesaIDSekolah" value="LIKE"></span></td>
		<td<?php echo $student->DesaIDSekolah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_DesaIDSekolah">
<select data-table="student" data-field="x_DesaIDSekolah" data-page="4" data-value-separator="<?php echo $student->DesaIDSekolah->DisplayValueSeparatorAttribute() ?>" id="x_DesaIDSekolah" name="x_DesaIDSekolah"<?php echo $student->DesaIDSekolah->EditAttributes() ?>>
<?php echo $student->DesaIDSekolah->SelectOptionListHtml("x_DesaIDSekolah") ?>
</select>
<input type="hidden" name="s_x_DesaIDSekolah" id="s_x_DesaIDSekolah" value="<?php echo $student->DesaIDSekolah->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->NilaiSekolah->Visible) { // NilaiSekolah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_NilaiSekolah" class="form-group">
		<label for="x_NilaiSekolah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_NilaiSekolah"><?php echo $student->NilaiSekolah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NilaiSekolah" id="z_NilaiSekolah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->NilaiSekolah->CellAttributes() ?>>
			<span id="el_student_NilaiSekolah">
<input type="text" data-table="student" data-field="x_NilaiSekolah" data-page="4" name="x_NilaiSekolah" id="x_NilaiSekolah" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($student->NilaiSekolah->getPlaceHolder()) ?>" value="<?php echo $student->NilaiSekolah->EditValue ?>"<?php echo $student->NilaiSekolah->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NilaiSekolah">
		<td><span id="elh_student_NilaiSekolah"><?php echo $student->NilaiSekolah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NilaiSekolah" id="z_NilaiSekolah" value="LIKE"></span></td>
		<td<?php echo $student->NilaiSekolah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_NilaiSekolah">
<input type="text" data-table="student" data-field="x_NilaiSekolah" data-page="4" name="x_NilaiSekolah" id="x_NilaiSekolah" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($student->NilaiSekolah->getPlaceHolder()) ?>" value="<?php echo $student->NilaiSekolah->EditValue ?>"<?php echo $student->NilaiSekolah->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TahunLulus->Visible) { // TahunLulus ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_TahunLulus" class="form-group">
		<label for="x_TahunLulus" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_TahunLulus"><?php echo $student->TahunLulus->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TahunLulus" id="z_TahunLulus" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->TahunLulus->CellAttributes() ?>>
			<span id="el_student_TahunLulus">
<input type="text" data-table="student" data-field="x_TahunLulus" data-page="4" name="x_TahunLulus" id="x_TahunLulus" size="30" maxlength="4" placeholder="<?php echo ew_HtmlEncode($student->TahunLulus->getPlaceHolder()) ?>" value="<?php echo $student->TahunLulus->EditValue ?>"<?php echo $student->TahunLulus->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TahunLulus">
		<td><span id="elh_student_TahunLulus"><?php echo $student->TahunLulus->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TahunLulus" id="z_TahunLulus" value="LIKE"></span></td>
		<td<?php echo $student->TahunLulus->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_TahunLulus">
<input type="text" data-table="student" data-field="x_TahunLulus" data-page="4" name="x_TahunLulus" id="x_TahunLulus" size="30" maxlength="4" placeholder="<?php echo ew_HtmlEncode($student->TahunLulus->getPlaceHolder()) ?>" value="<?php echo $student->TahunLulus->EditValue ?>"<?php echo $student->TahunLulus->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->IjazahSekolah->Visible) { // IjazahSekolah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_IjazahSekolah" class="form-group">
		<label for="x_IjazahSekolah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_IjazahSekolah"><?php echo $student->IjazahSekolah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_IjazahSekolah" id="z_IjazahSekolah" value="LIKE"></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->IjazahSekolah->CellAttributes() ?>>
			<span id="el_student_IjazahSekolah">
<input type="text" data-table="student" data-field="x_IjazahSekolah" data-page="4" name="x_IjazahSekolah" id="x_IjazahSekolah" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->IjazahSekolah->getPlaceHolder()) ?>" value="<?php echo $student->IjazahSekolah->EditValue ?>"<?php echo $student->IjazahSekolah->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_IjazahSekolah">
		<td><span id="elh_student_IjazahSekolah"><?php echo $student->IjazahSekolah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_IjazahSekolah" id="z_IjazahSekolah" value="LIKE"></span></td>
		<td<?php echo $student->IjazahSekolah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_IjazahSekolah">
<input type="text" data-table="student" data-field="x_IjazahSekolah" data-page="4" name="x_IjazahSekolah" id="x_IjazahSekolah" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->IjazahSekolah->getPlaceHolder()) ?>" value="<?php echo $student->IjazahSekolah->EditValue ?>"<?php echo $student->IjazahSekolah->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($student->TglIjazah->Visible) { // TglIjazah ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_TglIjazah" class="form-group">
		<label for="x_TglIjazah" class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_TglIjazah"><?php echo $student->TglIjazah->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TglIjazah" id="z_TglIjazah" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->TglIjazah->CellAttributes() ?>>
			<span id="el_student_TglIjazah">
<input type="text" data-table="student" data-field="x_TglIjazah" data-page="4" name="x_TglIjazah" id="x_TglIjazah" placeholder="<?php echo ew_HtmlEncode($student->TglIjazah->getPlaceHolder()) ?>" value="<?php echo $student->TglIjazah->EditValue ?>"<?php echo $student->TglIjazah->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TglIjazah">
		<td><span id="elh_student_TglIjazah"><?php echo $student->TglIjazah->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TglIjazah" id="z_TglIjazah" value="="></span></td>
		<td<?php echo $student->TglIjazah->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_TglIjazah">
<input type="text" data-table="student" data-field="x_TglIjazah" data-page="4" name="x_TglIjazah" id="x_TglIjazah" placeholder="<?php echo ew_HtmlEncode($student->TglIjazah->getPlaceHolder()) ?>" value="<?php echo $student->TglIjazah->EditValue ?>"<?php echo $student->TglIjazah->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $student_search->MultiPages->PageStyle("5") ?>" id="tab_student5">
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_studentsearch5" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($student->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $student_search->SearchLabelClass ?>"><span id="elh_student_NA"><?php echo $student->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $student_search->SearchRightColumnClass ?>"><div<?php echo $student->NA->CellAttributes() ?>>
			<span id="el_student_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="student" data-field="x_NA" data-page="5" data-value-separator="<?php echo $student->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $student->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->NA->RadioButtonListHtml(FALSE, "x_NA", 5) ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_student_NA"><?php echo $student->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $student->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_student_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="student" data-field="x_NA" data-page="5" data-value-separator="<?php echo $student->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $student->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $student->NA->RadioButtonListHtml(FALSE, "x_NA", 5) ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $student_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
	</div>
</div>
</div>
<?php if (!$student_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$student_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fstudentsearch.Init();
</script>
<?php
$student_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$student_search->Page_Terminate();
?>
