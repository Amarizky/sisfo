<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "krsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$krs_search = NULL; // Initialize page object first

class ckrs_search extends ckrs {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'krs';

	// Page object name
	var $PageObjName = 'krs_search';

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

		// Table object (krs)
		if (!isset($GLOBALS["krs"]) || get_class($GLOBALS["krs"]) == "ckrs") {
			$GLOBALS["krs"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["krs"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'krs', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("krslist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->KRSID->SetVisibility();
		$this->KRSID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->KHSID->SetVisibility();
		$this->StudentID->SetVisibility();
		$this->TahunID->SetVisibility();
		$this->Sesi->SetVisibility();
		$this->JadwalID->SetVisibility();
		$this->MKID->SetVisibility();
		$this->MKKode->SetVisibility();
		$this->SKS->SetVisibility();
		$this->Tugas1->SetVisibility();
		$this->Tugas2->SetVisibility();
		$this->Tugas3->SetVisibility();
		$this->Tugas4->SetVisibility();
		$this->Tugas5->SetVisibility();
		$this->Presensi->SetVisibility();
		$this->_Presensi->SetVisibility();
		$this->UTS->SetVisibility();
		$this->UAS->SetVisibility();
		$this->Responsi->SetVisibility();
		$this->NilaiAkhir->SetVisibility();
		$this->GradeNilai->SetVisibility();
		$this->BobotNilai->SetVisibility();
		$this->StatusKRSID->SetVisibility();
		$this->Tinggi->SetVisibility();
		$this->Final->SetVisibility();
		$this->Setara->SetVisibility();
		$this->Creator->SetVisibility();
		$this->CreateDate->SetVisibility();
		$this->Editor->SetVisibility();
		$this->EditDate->SetVisibility();
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
		global $EW_EXPORT, $krs;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($krs);
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
						$sSrchStr = "krslist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->KRSID); // KRSID
		$this->BuildSearchUrl($sSrchUrl, $this->KHSID); // KHSID
		$this->BuildSearchUrl($sSrchUrl, $this->StudentID); // StudentID
		$this->BuildSearchUrl($sSrchUrl, $this->TahunID); // TahunID
		$this->BuildSearchUrl($sSrchUrl, $this->Sesi); // Sesi
		$this->BuildSearchUrl($sSrchUrl, $this->JadwalID); // JadwalID
		$this->BuildSearchUrl($sSrchUrl, $this->MKID); // MKID
		$this->BuildSearchUrl($sSrchUrl, $this->MKKode); // MKKode
		$this->BuildSearchUrl($sSrchUrl, $this->SKS); // SKS
		$this->BuildSearchUrl($sSrchUrl, $this->Tugas1); // Tugas1
		$this->BuildSearchUrl($sSrchUrl, $this->Tugas2); // Tugas2
		$this->BuildSearchUrl($sSrchUrl, $this->Tugas3); // Tugas3
		$this->BuildSearchUrl($sSrchUrl, $this->Tugas4); // Tugas4
		$this->BuildSearchUrl($sSrchUrl, $this->Tugas5); // Tugas5
		$this->BuildSearchUrl($sSrchUrl, $this->Presensi); // Presensi
		$this->BuildSearchUrl($sSrchUrl, $this->_Presensi); // _Presensi
		$this->BuildSearchUrl($sSrchUrl, $this->UTS); // UTS
		$this->BuildSearchUrl($sSrchUrl, $this->UAS); // UAS
		$this->BuildSearchUrl($sSrchUrl, $this->Responsi); // Responsi
		$this->BuildSearchUrl($sSrchUrl, $this->NilaiAkhir); // NilaiAkhir
		$this->BuildSearchUrl($sSrchUrl, $this->GradeNilai); // GradeNilai
		$this->BuildSearchUrl($sSrchUrl, $this->BobotNilai); // BobotNilai
		$this->BuildSearchUrl($sSrchUrl, $this->StatusKRSID); // StatusKRSID
		$this->BuildSearchUrl($sSrchUrl, $this->Tinggi); // Tinggi
		$this->BuildSearchUrl($sSrchUrl, $this->Final); // Final
		$this->BuildSearchUrl($sSrchUrl, $this->Setara); // Setara
		$this->BuildSearchUrl($sSrchUrl, $this->Creator); // Creator
		$this->BuildSearchUrl($sSrchUrl, $this->CreateDate); // CreateDate
		$this->BuildSearchUrl($sSrchUrl, $this->Editor); // Editor
		$this->BuildSearchUrl($sSrchUrl, $this->EditDate); // EditDate
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
		// KRSID

		$this->KRSID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KRSID"));
		$this->KRSID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KRSID");

		// KHSID
		$this->KHSID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KHSID"));
		$this->KHSID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KHSID");

		// StudentID
		$this->StudentID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StudentID"));
		$this->StudentID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StudentID");

		// TahunID
		$this->TahunID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TahunID"));
		$this->TahunID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TahunID");

		// Sesi
		$this->Sesi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Sesi"));
		$this->Sesi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Sesi");

		// JadwalID
		$this->JadwalID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_JadwalID"));
		$this->JadwalID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_JadwalID");

		// MKID
		$this->MKID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_MKID"));
		$this->MKID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_MKID");

		// MKKode
		$this->MKKode->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_MKKode"));
		$this->MKKode->AdvancedSearch->SearchOperator = $objForm->GetValue("z_MKKode");

		// SKS
		$this->SKS->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_SKS"));
		$this->SKS->AdvancedSearch->SearchOperator = $objForm->GetValue("z_SKS");

		// Tugas1
		$this->Tugas1->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tugas1"));
		$this->Tugas1->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tugas1");

		// Tugas2
		$this->Tugas2->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tugas2"));
		$this->Tugas2->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tugas2");

		// Tugas3
		$this->Tugas3->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tugas3"));
		$this->Tugas3->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tugas3");

		// Tugas4
		$this->Tugas4->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tugas4"));
		$this->Tugas4->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tugas4");

		// Tugas5
		$this->Tugas5->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tugas5"));
		$this->Tugas5->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tugas5");

		// Presensi
		$this->Presensi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Presensi"));
		$this->Presensi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Presensi");

		// _Presensi
		$this->_Presensi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x__Presensi"));
		$this->_Presensi->AdvancedSearch->SearchOperator = $objForm->GetValue("z__Presensi");

		// UTS
		$this->UTS->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_UTS"));
		$this->UTS->AdvancedSearch->SearchOperator = $objForm->GetValue("z_UTS");

		// UAS
		$this->UAS->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_UAS"));
		$this->UAS->AdvancedSearch->SearchOperator = $objForm->GetValue("z_UAS");

		// Responsi
		$this->Responsi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Responsi"));
		$this->Responsi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Responsi");

		// NilaiAkhir
		$this->NilaiAkhir->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NilaiAkhir"));
		$this->NilaiAkhir->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NilaiAkhir");

		// GradeNilai
		$this->GradeNilai->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_GradeNilai"));
		$this->GradeNilai->AdvancedSearch->SearchOperator = $objForm->GetValue("z_GradeNilai");

		// BobotNilai
		$this->BobotNilai->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_BobotNilai"));
		$this->BobotNilai->AdvancedSearch->SearchOperator = $objForm->GetValue("z_BobotNilai");

		// StatusKRSID
		$this->StatusKRSID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StatusKRSID"));
		$this->StatusKRSID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StatusKRSID");

		// Tinggi
		$this->Tinggi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tinggi"));
		$this->Tinggi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tinggi");

		// Final
		$this->Final->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Final"));
		$this->Final->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Final");

		// Setara
		$this->Setara->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Setara"));
		$this->Setara->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Setara");

		// Creator
		$this->Creator->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Creator"));
		$this->Creator->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Creator");

		// CreateDate
		$this->CreateDate->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_CreateDate"));
		$this->CreateDate->AdvancedSearch->SearchOperator = $objForm->GetValue("z_CreateDate");

		// Editor
		$this->Editor->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Editor"));
		$this->Editor->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Editor");

		// EditDate
		$this->EditDate->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_EditDate"));
		$this->EditDate->AdvancedSearch->SearchOperator = $objForm->GetValue("z_EditDate");

		// NA
		$this->NA->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NA"));
		$this->NA->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NA");
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Convert decimal values if posted back

		if ($this->Responsi->FormValue == $this->Responsi->CurrentValue && is_numeric(ew_StrToFloat($this->Responsi->CurrentValue)))
			$this->Responsi->CurrentValue = ew_StrToFloat($this->Responsi->CurrentValue);

		// Convert decimal values if posted back
		if ($this->NilaiAkhir->FormValue == $this->NilaiAkhir->CurrentValue && is_numeric(ew_StrToFloat($this->NilaiAkhir->CurrentValue)))
			$this->NilaiAkhir->CurrentValue = ew_StrToFloat($this->NilaiAkhir->CurrentValue);

		// Convert decimal values if posted back
		if ($this->BobotNilai->FormValue == $this->BobotNilai->CurrentValue && is_numeric(ew_StrToFloat($this->BobotNilai->CurrentValue)))
			$this->BobotNilai->CurrentValue = ew_StrToFloat($this->BobotNilai->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// KRSID
		// KHSID
		// StudentID
		// TahunID
		// Sesi
		// JadwalID
		// MKID
		// MKKode
		// SKS
		// Tugas1
		// Tugas2
		// Tugas3
		// Tugas4
		// Tugas5
		// Presensi
		// _Presensi
		// UTS
		// UAS
		// Responsi
		// NilaiAkhir
		// GradeNilai
		// BobotNilai
		// StatusKRSID
		// Tinggi
		// Final
		// Setara
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// KRSID
		$this->KRSID->ViewValue = $this->KRSID->CurrentValue;
		$this->KRSID->ViewCustomAttributes = "";

		// KHSID
		$this->KHSID->ViewValue = $this->KHSID->CurrentValue;
		$this->KHSID->ViewCustomAttributes = "";

		// StudentID
		$this->StudentID->ViewValue = $this->StudentID->CurrentValue;
		$this->StudentID->ViewCustomAttributes = "";

		// TahunID
		$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
		$this->TahunID->ViewCustomAttributes = "";

		// Sesi
		$this->Sesi->ViewValue = $this->Sesi->CurrentValue;
		$this->Sesi->ViewCustomAttributes = "";

		// JadwalID
		$this->JadwalID->ViewValue = $this->JadwalID->CurrentValue;
		$this->JadwalID->ViewCustomAttributes = "";

		// MKID
		$this->MKID->ViewValue = $this->MKID->CurrentValue;
		$this->MKID->ViewCustomAttributes = "";

		// MKKode
		$this->MKKode->ViewValue = $this->MKKode->CurrentValue;
		$this->MKKode->ViewCustomAttributes = "";

		// SKS
		$this->SKS->ViewValue = $this->SKS->CurrentValue;
		$this->SKS->ViewCustomAttributes = "";

		// Tugas1
		$this->Tugas1->ViewValue = $this->Tugas1->CurrentValue;
		$this->Tugas1->ViewCustomAttributes = "";

		// Tugas2
		$this->Tugas2->ViewValue = $this->Tugas2->CurrentValue;
		$this->Tugas2->ViewCustomAttributes = "";

		// Tugas3
		$this->Tugas3->ViewValue = $this->Tugas3->CurrentValue;
		$this->Tugas3->ViewCustomAttributes = "";

		// Tugas4
		$this->Tugas4->ViewValue = $this->Tugas4->CurrentValue;
		$this->Tugas4->ViewCustomAttributes = "";

		// Tugas5
		$this->Tugas5->ViewValue = $this->Tugas5->CurrentValue;
		$this->Tugas5->ViewCustomAttributes = "";

		// Presensi
		$this->Presensi->ViewValue = $this->Presensi->CurrentValue;
		$this->Presensi->ViewCustomAttributes = "";

		// _Presensi
		$this->_Presensi->ViewValue = $this->_Presensi->CurrentValue;
		$this->_Presensi->ViewCustomAttributes = "";

		// UTS
		$this->UTS->ViewValue = $this->UTS->CurrentValue;
		$this->UTS->ViewCustomAttributes = "";

		// UAS
		$this->UAS->ViewValue = $this->UAS->CurrentValue;
		$this->UAS->ViewCustomAttributes = "";

		// Responsi
		$this->Responsi->ViewValue = $this->Responsi->CurrentValue;
		$this->Responsi->ViewCustomAttributes = "";

		// NilaiAkhir
		$this->NilaiAkhir->ViewValue = $this->NilaiAkhir->CurrentValue;
		$this->NilaiAkhir->ViewCustomAttributes = "";

		// GradeNilai
		$this->GradeNilai->ViewValue = $this->GradeNilai->CurrentValue;
		$this->GradeNilai->ViewCustomAttributes = "";

		// BobotNilai
		$this->BobotNilai->ViewValue = $this->BobotNilai->CurrentValue;
		$this->BobotNilai->ViewCustomAttributes = "";

		// StatusKRSID
		$this->StatusKRSID->ViewValue = $this->StatusKRSID->CurrentValue;
		$this->StatusKRSID->ViewCustomAttributes = "";

		// Tinggi
		$this->Tinggi->ViewValue = $this->Tinggi->CurrentValue;
		$this->Tinggi->ViewCustomAttributes = "";

		// Final
		if (ew_ConvertToBool($this->Final->CurrentValue)) {
			$this->Final->ViewValue = $this->Final->FldTagCaption(1) <> "" ? $this->Final->FldTagCaption(1) : "Y";
		} else {
			$this->Final->ViewValue = $this->Final->FldTagCaption(2) <> "" ? $this->Final->FldTagCaption(2) : "N";
		}
		$this->Final->ViewCustomAttributes = "";

		// Setara
		if (ew_ConvertToBool($this->Setara->CurrentValue)) {
			$this->Setara->ViewValue = $this->Setara->FldTagCaption(1) <> "" ? $this->Setara->FldTagCaption(1) : "Y";
		} else {
			$this->Setara->ViewValue = $this->Setara->FldTagCaption(2) <> "" ? $this->Setara->FldTagCaption(2) : "N";
		}
		$this->Setara->ViewCustomAttributes = "";

		// Creator
		$this->Creator->ViewValue = $this->Creator->CurrentValue;
		$this->Creator->ViewCustomAttributes = "";

		// CreateDate
		$this->CreateDate->ViewValue = $this->CreateDate->CurrentValue;
		$this->CreateDate->ViewValue = ew_FormatDateTime($this->CreateDate->ViewValue, 0);
		$this->CreateDate->ViewCustomAttributes = "";

		// Editor
		$this->Editor->ViewValue = $this->Editor->CurrentValue;
		$this->Editor->ViewCustomAttributes = "";

		// EditDate
		$this->EditDate->ViewValue = $this->EditDate->CurrentValue;
		$this->EditDate->ViewValue = ew_FormatDateTime($this->EditDate->ViewValue, 0);
		$this->EditDate->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

			// KRSID
			$this->KRSID->LinkCustomAttributes = "";
			$this->KRSID->HrefValue = "";
			$this->KRSID->TooltipValue = "";

			// KHSID
			$this->KHSID->LinkCustomAttributes = "";
			$this->KHSID->HrefValue = "";
			$this->KHSID->TooltipValue = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";
			$this->StudentID->TooltipValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";
			$this->TahunID->TooltipValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";
			$this->Sesi->TooltipValue = "";

			// JadwalID
			$this->JadwalID->LinkCustomAttributes = "";
			$this->JadwalID->HrefValue = "";
			$this->JadwalID->TooltipValue = "";

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";
			$this->MKID->TooltipValue = "";

			// MKKode
			$this->MKKode->LinkCustomAttributes = "";
			$this->MKKode->HrefValue = "";
			$this->MKKode->TooltipValue = "";

			// SKS
			$this->SKS->LinkCustomAttributes = "";
			$this->SKS->HrefValue = "";
			$this->SKS->TooltipValue = "";

			// Tugas1
			$this->Tugas1->LinkCustomAttributes = "";
			$this->Tugas1->HrefValue = "";
			$this->Tugas1->TooltipValue = "";

			// Tugas2
			$this->Tugas2->LinkCustomAttributes = "";
			$this->Tugas2->HrefValue = "";
			$this->Tugas2->TooltipValue = "";

			// Tugas3
			$this->Tugas3->LinkCustomAttributes = "";
			$this->Tugas3->HrefValue = "";
			$this->Tugas3->TooltipValue = "";

			// Tugas4
			$this->Tugas4->LinkCustomAttributes = "";
			$this->Tugas4->HrefValue = "";
			$this->Tugas4->TooltipValue = "";

			// Tugas5
			$this->Tugas5->LinkCustomAttributes = "";
			$this->Tugas5->HrefValue = "";
			$this->Tugas5->TooltipValue = "";

			// Presensi
			$this->Presensi->LinkCustomAttributes = "";
			$this->Presensi->HrefValue = "";
			$this->Presensi->TooltipValue = "";

			// _Presensi
			$this->_Presensi->LinkCustomAttributes = "";
			$this->_Presensi->HrefValue = "";
			$this->_Presensi->TooltipValue = "";

			// UTS
			$this->UTS->LinkCustomAttributes = "";
			$this->UTS->HrefValue = "";
			$this->UTS->TooltipValue = "";

			// UAS
			$this->UAS->LinkCustomAttributes = "";
			$this->UAS->HrefValue = "";
			$this->UAS->TooltipValue = "";

			// Responsi
			$this->Responsi->LinkCustomAttributes = "";
			$this->Responsi->HrefValue = "";
			$this->Responsi->TooltipValue = "";

			// NilaiAkhir
			$this->NilaiAkhir->LinkCustomAttributes = "";
			$this->NilaiAkhir->HrefValue = "";
			$this->NilaiAkhir->TooltipValue = "";

			// GradeNilai
			$this->GradeNilai->LinkCustomAttributes = "";
			$this->GradeNilai->HrefValue = "";
			$this->GradeNilai->TooltipValue = "";

			// BobotNilai
			$this->BobotNilai->LinkCustomAttributes = "";
			$this->BobotNilai->HrefValue = "";
			$this->BobotNilai->TooltipValue = "";

			// StatusKRSID
			$this->StatusKRSID->LinkCustomAttributes = "";
			$this->StatusKRSID->HrefValue = "";
			$this->StatusKRSID->TooltipValue = "";

			// Tinggi
			$this->Tinggi->LinkCustomAttributes = "";
			$this->Tinggi->HrefValue = "";
			$this->Tinggi->TooltipValue = "";

			// Final
			$this->Final->LinkCustomAttributes = "";
			$this->Final->HrefValue = "";
			$this->Final->TooltipValue = "";

			// Setara
			$this->Setara->LinkCustomAttributes = "";
			$this->Setara->HrefValue = "";
			$this->Setara->TooltipValue = "";

			// Creator
			$this->Creator->LinkCustomAttributes = "";
			$this->Creator->HrefValue = "";
			$this->Creator->TooltipValue = "";

			// CreateDate
			$this->CreateDate->LinkCustomAttributes = "";
			$this->CreateDate->HrefValue = "";
			$this->CreateDate->TooltipValue = "";

			// Editor
			$this->Editor->LinkCustomAttributes = "";
			$this->Editor->HrefValue = "";
			$this->Editor->TooltipValue = "";

			// EditDate
			$this->EditDate->LinkCustomAttributes = "";
			$this->EditDate->HrefValue = "";
			$this->EditDate->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// KRSID
			$this->KRSID->EditAttrs["class"] = "form-control";
			$this->KRSID->EditCustomAttributes = "";
			$this->KRSID->EditValue = ew_HtmlEncode($this->KRSID->AdvancedSearch->SearchValue);
			$this->KRSID->PlaceHolder = ew_RemoveHtml($this->KRSID->FldCaption());

			// KHSID
			$this->KHSID->EditAttrs["class"] = "form-control";
			$this->KHSID->EditCustomAttributes = "";
			$this->KHSID->EditValue = ew_HtmlEncode($this->KHSID->AdvancedSearch->SearchValue);
			$this->KHSID->PlaceHolder = ew_RemoveHtml($this->KHSID->FldCaption());

			// StudentID
			$this->StudentID->EditAttrs["class"] = "form-control";
			$this->StudentID->EditCustomAttributes = "";
			$this->StudentID->EditValue = ew_HtmlEncode($this->StudentID->AdvancedSearch->SearchValue);
			$this->StudentID->PlaceHolder = ew_RemoveHtml($this->StudentID->FldCaption());

			// TahunID
			$this->TahunID->EditAttrs["class"] = "form-control";
			$this->TahunID->EditCustomAttributes = "";
			$this->TahunID->EditValue = ew_HtmlEncode($this->TahunID->AdvancedSearch->SearchValue);
			$this->TahunID->PlaceHolder = ew_RemoveHtml($this->TahunID->FldCaption());

			// Sesi
			$this->Sesi->EditAttrs["class"] = "form-control";
			$this->Sesi->EditCustomAttributes = "";
			$this->Sesi->EditValue = ew_HtmlEncode($this->Sesi->AdvancedSearch->SearchValue);
			$this->Sesi->PlaceHolder = ew_RemoveHtml($this->Sesi->FldCaption());

			// JadwalID
			$this->JadwalID->EditAttrs["class"] = "form-control";
			$this->JadwalID->EditCustomAttributes = "";
			$this->JadwalID->EditValue = ew_HtmlEncode($this->JadwalID->AdvancedSearch->SearchValue);
			$this->JadwalID->PlaceHolder = ew_RemoveHtml($this->JadwalID->FldCaption());

			// MKID
			$this->MKID->EditAttrs["class"] = "form-control";
			$this->MKID->EditCustomAttributes = "";
			$this->MKID->EditValue = ew_HtmlEncode($this->MKID->AdvancedSearch->SearchValue);
			$this->MKID->PlaceHolder = ew_RemoveHtml($this->MKID->FldCaption());

			// MKKode
			$this->MKKode->EditAttrs["class"] = "form-control";
			$this->MKKode->EditCustomAttributes = "";
			$this->MKKode->EditValue = ew_HtmlEncode($this->MKKode->AdvancedSearch->SearchValue);
			$this->MKKode->PlaceHolder = ew_RemoveHtml($this->MKKode->FldCaption());

			// SKS
			$this->SKS->EditAttrs["class"] = "form-control";
			$this->SKS->EditCustomAttributes = "";
			$this->SKS->EditValue = ew_HtmlEncode($this->SKS->AdvancedSearch->SearchValue);
			$this->SKS->PlaceHolder = ew_RemoveHtml($this->SKS->FldCaption());

			// Tugas1
			$this->Tugas1->EditAttrs["class"] = "form-control";
			$this->Tugas1->EditCustomAttributes = "";
			$this->Tugas1->EditValue = ew_HtmlEncode($this->Tugas1->AdvancedSearch->SearchValue);
			$this->Tugas1->PlaceHolder = ew_RemoveHtml($this->Tugas1->FldCaption());

			// Tugas2
			$this->Tugas2->EditAttrs["class"] = "form-control";
			$this->Tugas2->EditCustomAttributes = "";
			$this->Tugas2->EditValue = ew_HtmlEncode($this->Tugas2->AdvancedSearch->SearchValue);
			$this->Tugas2->PlaceHolder = ew_RemoveHtml($this->Tugas2->FldCaption());

			// Tugas3
			$this->Tugas3->EditAttrs["class"] = "form-control";
			$this->Tugas3->EditCustomAttributes = "";
			$this->Tugas3->EditValue = ew_HtmlEncode($this->Tugas3->AdvancedSearch->SearchValue);
			$this->Tugas3->PlaceHolder = ew_RemoveHtml($this->Tugas3->FldCaption());

			// Tugas4
			$this->Tugas4->EditAttrs["class"] = "form-control";
			$this->Tugas4->EditCustomAttributes = "";
			$this->Tugas4->EditValue = ew_HtmlEncode($this->Tugas4->AdvancedSearch->SearchValue);
			$this->Tugas4->PlaceHolder = ew_RemoveHtml($this->Tugas4->FldCaption());

			// Tugas5
			$this->Tugas5->EditAttrs["class"] = "form-control";
			$this->Tugas5->EditCustomAttributes = "";
			$this->Tugas5->EditValue = ew_HtmlEncode($this->Tugas5->AdvancedSearch->SearchValue);
			$this->Tugas5->PlaceHolder = ew_RemoveHtml($this->Tugas5->FldCaption());

			// Presensi
			$this->Presensi->EditAttrs["class"] = "form-control";
			$this->Presensi->EditCustomAttributes = "";
			$this->Presensi->EditValue = ew_HtmlEncode($this->Presensi->AdvancedSearch->SearchValue);
			$this->Presensi->PlaceHolder = ew_RemoveHtml($this->Presensi->FldCaption());

			// _Presensi
			$this->_Presensi->EditAttrs["class"] = "form-control";
			$this->_Presensi->EditCustomAttributes = "";
			$this->_Presensi->EditValue = ew_HtmlEncode($this->_Presensi->AdvancedSearch->SearchValue);
			$this->_Presensi->PlaceHolder = ew_RemoveHtml($this->_Presensi->FldCaption());

			// UTS
			$this->UTS->EditAttrs["class"] = "form-control";
			$this->UTS->EditCustomAttributes = "";
			$this->UTS->EditValue = ew_HtmlEncode($this->UTS->AdvancedSearch->SearchValue);
			$this->UTS->PlaceHolder = ew_RemoveHtml($this->UTS->FldCaption());

			// UAS
			$this->UAS->EditAttrs["class"] = "form-control";
			$this->UAS->EditCustomAttributes = "";
			$this->UAS->EditValue = ew_HtmlEncode($this->UAS->AdvancedSearch->SearchValue);
			$this->UAS->PlaceHolder = ew_RemoveHtml($this->UAS->FldCaption());

			// Responsi
			$this->Responsi->EditAttrs["class"] = "form-control";
			$this->Responsi->EditCustomAttributes = "";
			$this->Responsi->EditValue = ew_HtmlEncode($this->Responsi->AdvancedSearch->SearchValue);
			$this->Responsi->PlaceHolder = ew_RemoveHtml($this->Responsi->FldCaption());

			// NilaiAkhir
			$this->NilaiAkhir->EditAttrs["class"] = "form-control";
			$this->NilaiAkhir->EditCustomAttributes = "";
			$this->NilaiAkhir->EditValue = ew_HtmlEncode($this->NilaiAkhir->AdvancedSearch->SearchValue);
			$this->NilaiAkhir->PlaceHolder = ew_RemoveHtml($this->NilaiAkhir->FldCaption());

			// GradeNilai
			$this->GradeNilai->EditAttrs["class"] = "form-control";
			$this->GradeNilai->EditCustomAttributes = "";
			$this->GradeNilai->EditValue = ew_HtmlEncode($this->GradeNilai->AdvancedSearch->SearchValue);
			$this->GradeNilai->PlaceHolder = ew_RemoveHtml($this->GradeNilai->FldCaption());

			// BobotNilai
			$this->BobotNilai->EditAttrs["class"] = "form-control";
			$this->BobotNilai->EditCustomAttributes = "";
			$this->BobotNilai->EditValue = ew_HtmlEncode($this->BobotNilai->AdvancedSearch->SearchValue);
			$this->BobotNilai->PlaceHolder = ew_RemoveHtml($this->BobotNilai->FldCaption());

			// StatusKRSID
			$this->StatusKRSID->EditAttrs["class"] = "form-control";
			$this->StatusKRSID->EditCustomAttributes = "";
			$this->StatusKRSID->EditValue = ew_HtmlEncode($this->StatusKRSID->AdvancedSearch->SearchValue);
			$this->StatusKRSID->PlaceHolder = ew_RemoveHtml($this->StatusKRSID->FldCaption());

			// Tinggi
			$this->Tinggi->EditAttrs["class"] = "form-control";
			$this->Tinggi->EditCustomAttributes = "";
			$this->Tinggi->EditValue = ew_HtmlEncode($this->Tinggi->AdvancedSearch->SearchValue);
			$this->Tinggi->PlaceHolder = ew_RemoveHtml($this->Tinggi->FldCaption());

			// Final
			$this->Final->EditCustomAttributes = "";
			$this->Final->EditValue = $this->Final->Options(FALSE);

			// Setara
			$this->Setara->EditCustomAttributes = "";
			$this->Setara->EditValue = $this->Setara->Options(FALSE);

			// Creator
			$this->Creator->EditAttrs["class"] = "form-control";
			$this->Creator->EditCustomAttributes = "";
			$this->Creator->EditValue = ew_HtmlEncode($this->Creator->AdvancedSearch->SearchValue);
			$this->Creator->PlaceHolder = ew_RemoveHtml($this->Creator->FldCaption());

			// CreateDate
			$this->CreateDate->EditAttrs["class"] = "form-control";
			$this->CreateDate->EditCustomAttributes = "";
			$this->CreateDate->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->CreateDate->AdvancedSearch->SearchValue, 0), 8));
			$this->CreateDate->PlaceHolder = ew_RemoveHtml($this->CreateDate->FldCaption());

			// Editor
			$this->Editor->EditAttrs["class"] = "form-control";
			$this->Editor->EditCustomAttributes = "";
			$this->Editor->EditValue = ew_HtmlEncode($this->Editor->AdvancedSearch->SearchValue);
			$this->Editor->PlaceHolder = ew_RemoveHtml($this->Editor->FldCaption());

			// EditDate
			$this->EditDate->EditAttrs["class"] = "form-control";
			$this->EditDate->EditCustomAttributes = "";
			$this->EditDate->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->EditDate->AdvancedSearch->SearchValue, 0), 8));
			$this->EditDate->PlaceHolder = ew_RemoveHtml($this->EditDate->FldCaption());

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
		if (!ew_CheckInteger($this->KRSID->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->KRSID->FldErrMsg());
		}
		if (!ew_CheckInteger($this->KHSID->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->KHSID->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Sesi->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Sesi->FldErrMsg());
		}
		if (!ew_CheckInteger($this->JadwalID->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->JadwalID->FldErrMsg());
		}
		if (!ew_CheckInteger($this->MKID->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->MKID->FldErrMsg());
		}
		if (!ew_CheckInteger($this->SKS->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->SKS->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Tugas1->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Tugas1->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Tugas2->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Tugas2->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Tugas3->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Tugas3->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Tugas4->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Tugas4->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Tugas5->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Tugas5->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Presensi->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Presensi->FldErrMsg());
		}
		if (!ew_CheckInteger($this->_Presensi->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->_Presensi->FldErrMsg());
		}
		if (!ew_CheckInteger($this->UTS->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->UTS->FldErrMsg());
		}
		if (!ew_CheckInteger($this->UAS->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->UAS->FldErrMsg());
		}
		if (!ew_CheckNumber($this->Responsi->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Responsi->FldErrMsg());
		}
		if (!ew_CheckNumber($this->NilaiAkhir->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->NilaiAkhir->FldErrMsg());
		}
		if (!ew_CheckNumber($this->BobotNilai->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->BobotNilai->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->CreateDate->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->CreateDate->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->EditDate->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->EditDate->FldErrMsg());
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
		$this->KRSID->AdvancedSearch->Load();
		$this->KHSID->AdvancedSearch->Load();
		$this->StudentID->AdvancedSearch->Load();
		$this->TahunID->AdvancedSearch->Load();
		$this->Sesi->AdvancedSearch->Load();
		$this->JadwalID->AdvancedSearch->Load();
		$this->MKID->AdvancedSearch->Load();
		$this->MKKode->AdvancedSearch->Load();
		$this->SKS->AdvancedSearch->Load();
		$this->Tugas1->AdvancedSearch->Load();
		$this->Tugas2->AdvancedSearch->Load();
		$this->Tugas3->AdvancedSearch->Load();
		$this->Tugas4->AdvancedSearch->Load();
		$this->Tugas5->AdvancedSearch->Load();
		$this->Presensi->AdvancedSearch->Load();
		$this->_Presensi->AdvancedSearch->Load();
		$this->UTS->AdvancedSearch->Load();
		$this->UAS->AdvancedSearch->Load();
		$this->Responsi->AdvancedSearch->Load();
		$this->NilaiAkhir->AdvancedSearch->Load();
		$this->GradeNilai->AdvancedSearch->Load();
		$this->BobotNilai->AdvancedSearch->Load();
		$this->StatusKRSID->AdvancedSearch->Load();
		$this->Tinggi->AdvancedSearch->Load();
		$this->Final->AdvancedSearch->Load();
		$this->Setara->AdvancedSearch->Load();
		$this->Creator->AdvancedSearch->Load();
		$this->CreateDate->AdvancedSearch->Load();
		$this->Editor->AdvancedSearch->Load();
		$this->EditDate->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("krslist.php"), "", $this->TableVar, TRUE);
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
if (!isset($krs_search)) $krs_search = new ckrs_search();

// Page init
$krs_search->Page_Init();

// Page main
$krs_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$krs_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($krs_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fkrssearch = new ew_Form("fkrssearch", "search");
<?php } else { ?>
var CurrentForm = fkrssearch = new ew_Form("fkrssearch", "search");
<?php } ?>

// Form_CustomValidate event
fkrssearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fkrssearch.ValidateRequired = true;
<?php } else { ?>
fkrssearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fkrssearch.Lists["x_Final"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrssearch.Lists["x_Final"].Options = <?php echo json_encode($krs->Final->Options()) ?>;
fkrssearch.Lists["x_Setara"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrssearch.Lists["x_Setara"].Options = <?php echo json_encode($krs->Setara->Options()) ?>;
fkrssearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkrssearch.Lists["x_NA"].Options = <?php echo json_encode($krs->NA->Options()) ?>;

// Form object for search
// Validate function for search

fkrssearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_KRSID");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->KRSID->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_KHSID");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->KHSID->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Sesi");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Sesi->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_JadwalID");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->JadwalID->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_MKID");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->MKID->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_SKS");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->SKS->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Tugas1");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Tugas1->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Tugas2");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Tugas2->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Tugas3");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Tugas3->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Tugas4");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Tugas4->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Tugas5");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Tugas5->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Presensi");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Presensi->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "__Presensi");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->_Presensi->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_UTS");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->UTS->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_UAS");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->UAS->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Responsi");
	if (elm && !ew_CheckNumber(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->Responsi->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_NilaiAkhir");
	if (elm && !ew_CheckNumber(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->NilaiAkhir->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_BobotNilai");
	if (elm && !ew_CheckNumber(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->BobotNilai->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_CreateDate");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->CreateDate->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_EditDate");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($krs->EditDate->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$krs_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $krs_search->ShowPageHeader(); ?>
<?php
$krs_search->ShowMessage();
?>
<form name="fkrssearch" id="fkrssearch" class="<?php echo $krs_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($krs_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $krs_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="krs">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($krs_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$krs_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_krssearch" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($krs->KRSID->Visible) { // KRSID ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_KRSID" class="form-group">
		<label for="x_KRSID" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_KRSID"><?php echo $krs->KRSID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KRSID" id="z_KRSID" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->KRSID->CellAttributes() ?>>
			<span id="el_krs_KRSID">
<input type="text" data-table="krs" data-field="x_KRSID" name="x_KRSID" id="x_KRSID" placeholder="<?php echo ew_HtmlEncode($krs->KRSID->getPlaceHolder()) ?>" value="<?php echo $krs->KRSID->EditValue ?>"<?php echo $krs->KRSID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KRSID">
		<td><span id="elh_krs_KRSID"><?php echo $krs->KRSID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KRSID" id="z_KRSID" value="="></span></td>
		<td<?php echo $krs->KRSID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_KRSID">
<input type="text" data-table="krs" data-field="x_KRSID" name="x_KRSID" id="x_KRSID" placeholder="<?php echo ew_HtmlEncode($krs->KRSID->getPlaceHolder()) ?>" value="<?php echo $krs->KRSID->EditValue ?>"<?php echo $krs->KRSID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->KHSID->Visible) { // KHSID ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_KHSID" class="form-group">
		<label for="x_KHSID" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_KHSID"><?php echo $krs->KHSID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KHSID" id="z_KHSID" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->KHSID->CellAttributes() ?>>
			<span id="el_krs_KHSID">
<input type="text" data-table="krs" data-field="x_KHSID" name="x_KHSID" id="x_KHSID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->KHSID->getPlaceHolder()) ?>" value="<?php echo $krs->KHSID->EditValue ?>"<?php echo $krs->KHSID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KHSID">
		<td><span id="elh_krs_KHSID"><?php echo $krs->KHSID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KHSID" id="z_KHSID" value="="></span></td>
		<td<?php echo $krs->KHSID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_KHSID">
<input type="text" data-table="krs" data-field="x_KHSID" name="x_KHSID" id="x_KHSID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->KHSID->getPlaceHolder()) ?>" value="<?php echo $krs->KHSID->EditValue ?>"<?php echo $krs->KHSID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->StudentID->Visible) { // StudentID ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_StudentID" class="form-group">
		<label for="x_StudentID" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_StudentID"><?php echo $krs->StudentID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StudentID" id="z_StudentID" value="LIKE"></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->StudentID->CellAttributes() ?>>
			<span id="el_krs_StudentID">
<input type="text" data-table="krs" data-field="x_StudentID" name="x_StudentID" id="x_StudentID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->StudentID->getPlaceHolder()) ?>" value="<?php echo $krs->StudentID->EditValue ?>"<?php echo $krs->StudentID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_StudentID">
		<td><span id="elh_krs_StudentID"><?php echo $krs->StudentID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StudentID" id="z_StudentID" value="LIKE"></span></td>
		<td<?php echo $krs->StudentID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_StudentID">
<input type="text" data-table="krs" data-field="x_StudentID" name="x_StudentID" id="x_StudentID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->StudentID->getPlaceHolder()) ?>" value="<?php echo $krs->StudentID->EditValue ?>"<?php echo $krs->StudentID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->TahunID->Visible) { // TahunID ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_TahunID" class="form-group">
		<label for="x_TahunID" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_TahunID"><?php echo $krs->TahunID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TahunID" id="z_TahunID" value="LIKE"></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->TahunID->CellAttributes() ?>>
			<span id="el_krs_TahunID">
<input type="text" data-table="krs" data-field="x_TahunID" name="x_TahunID" id="x_TahunID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->TahunID->getPlaceHolder()) ?>" value="<?php echo $krs->TahunID->EditValue ?>"<?php echo $krs->TahunID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TahunID">
		<td><span id="elh_krs_TahunID"><?php echo $krs->TahunID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TahunID" id="z_TahunID" value="LIKE"></span></td>
		<td<?php echo $krs->TahunID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_TahunID">
<input type="text" data-table="krs" data-field="x_TahunID" name="x_TahunID" id="x_TahunID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->TahunID->getPlaceHolder()) ?>" value="<?php echo $krs->TahunID->EditValue ?>"<?php echo $krs->TahunID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Sesi->Visible) { // Sesi ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Sesi" class="form-group">
		<label for="x_Sesi" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Sesi"><?php echo $krs->Sesi->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Sesi" id="z_Sesi" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Sesi->CellAttributes() ?>>
			<span id="el_krs_Sesi">
<input type="text" data-table="krs" data-field="x_Sesi" name="x_Sesi" id="x_Sesi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Sesi->getPlaceHolder()) ?>" value="<?php echo $krs->Sesi->EditValue ?>"<?php echo $krs->Sesi->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Sesi">
		<td><span id="elh_krs_Sesi"><?php echo $krs->Sesi->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Sesi" id="z_Sesi" value="="></span></td>
		<td<?php echo $krs->Sesi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Sesi">
<input type="text" data-table="krs" data-field="x_Sesi" name="x_Sesi" id="x_Sesi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Sesi->getPlaceHolder()) ?>" value="<?php echo $krs->Sesi->EditValue ?>"<?php echo $krs->Sesi->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->JadwalID->Visible) { // JadwalID ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_JadwalID" class="form-group">
		<label for="x_JadwalID" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_JadwalID"><?php echo $krs->JadwalID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_JadwalID" id="z_JadwalID" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->JadwalID->CellAttributes() ?>>
			<span id="el_krs_JadwalID">
<input type="text" data-table="krs" data-field="x_JadwalID" name="x_JadwalID" id="x_JadwalID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->JadwalID->getPlaceHolder()) ?>" value="<?php echo $krs->JadwalID->EditValue ?>"<?php echo $krs->JadwalID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_JadwalID">
		<td><span id="elh_krs_JadwalID"><?php echo $krs->JadwalID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_JadwalID" id="z_JadwalID" value="="></span></td>
		<td<?php echo $krs->JadwalID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_JadwalID">
<input type="text" data-table="krs" data-field="x_JadwalID" name="x_JadwalID" id="x_JadwalID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->JadwalID->getPlaceHolder()) ?>" value="<?php echo $krs->JadwalID->EditValue ?>"<?php echo $krs->JadwalID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->MKID->Visible) { // MKID ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_MKID" class="form-group">
		<label for="x_MKID" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_MKID"><?php echo $krs->MKID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_MKID" id="z_MKID" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->MKID->CellAttributes() ?>>
			<span id="el_krs_MKID">
<input type="text" data-table="krs" data-field="x_MKID" name="x_MKID" id="x_MKID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->MKID->getPlaceHolder()) ?>" value="<?php echo $krs->MKID->EditValue ?>"<?php echo $krs->MKID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_MKID">
		<td><span id="elh_krs_MKID"><?php echo $krs->MKID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_MKID" id="z_MKID" value="="></span></td>
		<td<?php echo $krs->MKID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_MKID">
<input type="text" data-table="krs" data-field="x_MKID" name="x_MKID" id="x_MKID" size="30" placeholder="<?php echo ew_HtmlEncode($krs->MKID->getPlaceHolder()) ?>" value="<?php echo $krs->MKID->EditValue ?>"<?php echo $krs->MKID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->MKKode->Visible) { // MKKode ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_MKKode" class="form-group">
		<label for="x_MKKode" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_MKKode"><?php echo $krs->MKKode->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_MKKode" id="z_MKKode" value="LIKE"></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->MKKode->CellAttributes() ?>>
			<span id="el_krs_MKKode">
<input type="text" data-table="krs" data-field="x_MKKode" name="x_MKKode" id="x_MKKode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->MKKode->getPlaceHolder()) ?>" value="<?php echo $krs->MKKode->EditValue ?>"<?php echo $krs->MKKode->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_MKKode">
		<td><span id="elh_krs_MKKode"><?php echo $krs->MKKode->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_MKKode" id="z_MKKode" value="LIKE"></span></td>
		<td<?php echo $krs->MKKode->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_MKKode">
<input type="text" data-table="krs" data-field="x_MKKode" name="x_MKKode" id="x_MKKode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->MKKode->getPlaceHolder()) ?>" value="<?php echo $krs->MKKode->EditValue ?>"<?php echo $krs->MKKode->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->SKS->Visible) { // SKS ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_SKS" class="form-group">
		<label for="x_SKS" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_SKS"><?php echo $krs->SKS->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_SKS" id="z_SKS" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->SKS->CellAttributes() ?>>
			<span id="el_krs_SKS">
<input type="text" data-table="krs" data-field="x_SKS" name="x_SKS" id="x_SKS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->SKS->getPlaceHolder()) ?>" value="<?php echo $krs->SKS->EditValue ?>"<?php echo $krs->SKS->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_SKS">
		<td><span id="elh_krs_SKS"><?php echo $krs->SKS->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_SKS" id="z_SKS" value="="></span></td>
		<td<?php echo $krs->SKS->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_SKS">
<input type="text" data-table="krs" data-field="x_SKS" name="x_SKS" id="x_SKS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->SKS->getPlaceHolder()) ?>" value="<?php echo $krs->SKS->EditValue ?>"<?php echo $krs->SKS->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tugas1->Visible) { // Tugas1 ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Tugas1" class="form-group">
		<label for="x_Tugas1" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Tugas1"><?php echo $krs->Tugas1->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tugas1" id="z_Tugas1" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Tugas1->CellAttributes() ?>>
			<span id="el_krs_Tugas1">
<input type="text" data-table="krs" data-field="x_Tugas1" name="x_Tugas1" id="x_Tugas1" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas1->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas1->EditValue ?>"<?php echo $krs->Tugas1->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tugas1">
		<td><span id="elh_krs_Tugas1"><?php echo $krs->Tugas1->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tugas1" id="z_Tugas1" value="="></span></td>
		<td<?php echo $krs->Tugas1->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Tugas1">
<input type="text" data-table="krs" data-field="x_Tugas1" name="x_Tugas1" id="x_Tugas1" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas1->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas1->EditValue ?>"<?php echo $krs->Tugas1->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tugas2->Visible) { // Tugas2 ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Tugas2" class="form-group">
		<label for="x_Tugas2" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Tugas2"><?php echo $krs->Tugas2->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tugas2" id="z_Tugas2" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Tugas2->CellAttributes() ?>>
			<span id="el_krs_Tugas2">
<input type="text" data-table="krs" data-field="x_Tugas2" name="x_Tugas2" id="x_Tugas2" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas2->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas2->EditValue ?>"<?php echo $krs->Tugas2->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tugas2">
		<td><span id="elh_krs_Tugas2"><?php echo $krs->Tugas2->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tugas2" id="z_Tugas2" value="="></span></td>
		<td<?php echo $krs->Tugas2->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Tugas2">
<input type="text" data-table="krs" data-field="x_Tugas2" name="x_Tugas2" id="x_Tugas2" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas2->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas2->EditValue ?>"<?php echo $krs->Tugas2->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tugas3->Visible) { // Tugas3 ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Tugas3" class="form-group">
		<label for="x_Tugas3" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Tugas3"><?php echo $krs->Tugas3->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tugas3" id="z_Tugas3" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Tugas3->CellAttributes() ?>>
			<span id="el_krs_Tugas3">
<input type="text" data-table="krs" data-field="x_Tugas3" name="x_Tugas3" id="x_Tugas3" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas3->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas3->EditValue ?>"<?php echo $krs->Tugas3->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tugas3">
		<td><span id="elh_krs_Tugas3"><?php echo $krs->Tugas3->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tugas3" id="z_Tugas3" value="="></span></td>
		<td<?php echo $krs->Tugas3->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Tugas3">
<input type="text" data-table="krs" data-field="x_Tugas3" name="x_Tugas3" id="x_Tugas3" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas3->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas3->EditValue ?>"<?php echo $krs->Tugas3->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tugas4->Visible) { // Tugas4 ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Tugas4" class="form-group">
		<label for="x_Tugas4" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Tugas4"><?php echo $krs->Tugas4->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tugas4" id="z_Tugas4" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Tugas4->CellAttributes() ?>>
			<span id="el_krs_Tugas4">
<input type="text" data-table="krs" data-field="x_Tugas4" name="x_Tugas4" id="x_Tugas4" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas4->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas4->EditValue ?>"<?php echo $krs->Tugas4->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tugas4">
		<td><span id="elh_krs_Tugas4"><?php echo $krs->Tugas4->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tugas4" id="z_Tugas4" value="="></span></td>
		<td<?php echo $krs->Tugas4->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Tugas4">
<input type="text" data-table="krs" data-field="x_Tugas4" name="x_Tugas4" id="x_Tugas4" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas4->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas4->EditValue ?>"<?php echo $krs->Tugas4->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tugas5->Visible) { // Tugas5 ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Tugas5" class="form-group">
		<label for="x_Tugas5" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Tugas5"><?php echo $krs->Tugas5->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tugas5" id="z_Tugas5" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Tugas5->CellAttributes() ?>>
			<span id="el_krs_Tugas5">
<input type="text" data-table="krs" data-field="x_Tugas5" name="x_Tugas5" id="x_Tugas5" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas5->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas5->EditValue ?>"<?php echo $krs->Tugas5->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tugas5">
		<td><span id="elh_krs_Tugas5"><?php echo $krs->Tugas5->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tugas5" id="z_Tugas5" value="="></span></td>
		<td<?php echo $krs->Tugas5->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Tugas5">
<input type="text" data-table="krs" data-field="x_Tugas5" name="x_Tugas5" id="x_Tugas5" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Tugas5->getPlaceHolder()) ?>" value="<?php echo $krs->Tugas5->EditValue ?>"<?php echo $krs->Tugas5->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Presensi->Visible) { // Presensi ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Presensi" class="form-group">
		<label for="x_Presensi" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Presensi"><?php echo $krs->Presensi->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Presensi" id="z_Presensi" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Presensi->CellAttributes() ?>>
			<span id="el_krs_Presensi">
<input type="text" data-table="krs" data-field="x_Presensi" name="x_Presensi" id="x_Presensi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Presensi->getPlaceHolder()) ?>" value="<?php echo $krs->Presensi->EditValue ?>"<?php echo $krs->Presensi->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Presensi">
		<td><span id="elh_krs_Presensi"><?php echo $krs->Presensi->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Presensi" id="z_Presensi" value="="></span></td>
		<td<?php echo $krs->Presensi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Presensi">
<input type="text" data-table="krs" data-field="x_Presensi" name="x_Presensi" id="x_Presensi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Presensi->getPlaceHolder()) ?>" value="<?php echo $krs->Presensi->EditValue ?>"<?php echo $krs->Presensi->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->_Presensi->Visible) { // _Presensi ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r__Presensi" class="form-group">
		<label for="x__Presensi" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs__Presensi"><?php echo $krs->_Presensi->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z__Presensi" id="z__Presensi" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->_Presensi->CellAttributes() ?>>
			<span id="el_krs__Presensi">
<input type="text" data-table="krs" data-field="x__Presensi" name="x__Presensi" id="x__Presensi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->_Presensi->getPlaceHolder()) ?>" value="<?php echo $krs->_Presensi->EditValue ?>"<?php echo $krs->_Presensi->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r__Presensi">
		<td><span id="elh_krs__Presensi"><?php echo $krs->_Presensi->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z__Presensi" id="z__Presensi" value="="></span></td>
		<td<?php echo $krs->_Presensi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs__Presensi">
<input type="text" data-table="krs" data-field="x__Presensi" name="x__Presensi" id="x__Presensi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->_Presensi->getPlaceHolder()) ?>" value="<?php echo $krs->_Presensi->EditValue ?>"<?php echo $krs->_Presensi->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->UTS->Visible) { // UTS ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_UTS" class="form-group">
		<label for="x_UTS" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_UTS"><?php echo $krs->UTS->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_UTS" id="z_UTS" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->UTS->CellAttributes() ?>>
			<span id="el_krs_UTS">
<input type="text" data-table="krs" data-field="x_UTS" name="x_UTS" id="x_UTS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->UTS->getPlaceHolder()) ?>" value="<?php echo $krs->UTS->EditValue ?>"<?php echo $krs->UTS->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_UTS">
		<td><span id="elh_krs_UTS"><?php echo $krs->UTS->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_UTS" id="z_UTS" value="="></span></td>
		<td<?php echo $krs->UTS->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_UTS">
<input type="text" data-table="krs" data-field="x_UTS" name="x_UTS" id="x_UTS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->UTS->getPlaceHolder()) ?>" value="<?php echo $krs->UTS->EditValue ?>"<?php echo $krs->UTS->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->UAS->Visible) { // UAS ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_UAS" class="form-group">
		<label for="x_UAS" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_UAS"><?php echo $krs->UAS->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_UAS" id="z_UAS" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->UAS->CellAttributes() ?>>
			<span id="el_krs_UAS">
<input type="text" data-table="krs" data-field="x_UAS" name="x_UAS" id="x_UAS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->UAS->getPlaceHolder()) ?>" value="<?php echo $krs->UAS->EditValue ?>"<?php echo $krs->UAS->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_UAS">
		<td><span id="elh_krs_UAS"><?php echo $krs->UAS->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_UAS" id="z_UAS" value="="></span></td>
		<td<?php echo $krs->UAS->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_UAS">
<input type="text" data-table="krs" data-field="x_UAS" name="x_UAS" id="x_UAS" size="30" placeholder="<?php echo ew_HtmlEncode($krs->UAS->getPlaceHolder()) ?>" value="<?php echo $krs->UAS->EditValue ?>"<?php echo $krs->UAS->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Responsi->Visible) { // Responsi ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Responsi" class="form-group">
		<label for="x_Responsi" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Responsi"><?php echo $krs->Responsi->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Responsi" id="z_Responsi" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Responsi->CellAttributes() ?>>
			<span id="el_krs_Responsi">
<input type="text" data-table="krs" data-field="x_Responsi" name="x_Responsi" id="x_Responsi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Responsi->getPlaceHolder()) ?>" value="<?php echo $krs->Responsi->EditValue ?>"<?php echo $krs->Responsi->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Responsi">
		<td><span id="elh_krs_Responsi"><?php echo $krs->Responsi->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Responsi" id="z_Responsi" value="="></span></td>
		<td<?php echo $krs->Responsi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Responsi">
<input type="text" data-table="krs" data-field="x_Responsi" name="x_Responsi" id="x_Responsi" size="30" placeholder="<?php echo ew_HtmlEncode($krs->Responsi->getPlaceHolder()) ?>" value="<?php echo $krs->Responsi->EditValue ?>"<?php echo $krs->Responsi->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->NilaiAkhir->Visible) { // NilaiAkhir ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_NilaiAkhir" class="form-group">
		<label for="x_NilaiAkhir" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_NilaiAkhir"><?php echo $krs->NilaiAkhir->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NilaiAkhir" id="z_NilaiAkhir" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->NilaiAkhir->CellAttributes() ?>>
			<span id="el_krs_NilaiAkhir">
<input type="text" data-table="krs" data-field="x_NilaiAkhir" name="x_NilaiAkhir" id="x_NilaiAkhir" size="30" placeholder="<?php echo ew_HtmlEncode($krs->NilaiAkhir->getPlaceHolder()) ?>" value="<?php echo $krs->NilaiAkhir->EditValue ?>"<?php echo $krs->NilaiAkhir->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NilaiAkhir">
		<td><span id="elh_krs_NilaiAkhir"><?php echo $krs->NilaiAkhir->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NilaiAkhir" id="z_NilaiAkhir" value="="></span></td>
		<td<?php echo $krs->NilaiAkhir->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_NilaiAkhir">
<input type="text" data-table="krs" data-field="x_NilaiAkhir" name="x_NilaiAkhir" id="x_NilaiAkhir" size="30" placeholder="<?php echo ew_HtmlEncode($krs->NilaiAkhir->getPlaceHolder()) ?>" value="<?php echo $krs->NilaiAkhir->EditValue ?>"<?php echo $krs->NilaiAkhir->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->GradeNilai->Visible) { // GradeNilai ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_GradeNilai" class="form-group">
		<label for="x_GradeNilai" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_GradeNilai"><?php echo $krs->GradeNilai->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_GradeNilai" id="z_GradeNilai" value="LIKE"></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->GradeNilai->CellAttributes() ?>>
			<span id="el_krs_GradeNilai">
<input type="text" data-table="krs" data-field="x_GradeNilai" name="x_GradeNilai" id="x_GradeNilai" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->GradeNilai->getPlaceHolder()) ?>" value="<?php echo $krs->GradeNilai->EditValue ?>"<?php echo $krs->GradeNilai->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_GradeNilai">
		<td><span id="elh_krs_GradeNilai"><?php echo $krs->GradeNilai->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_GradeNilai" id="z_GradeNilai" value="LIKE"></span></td>
		<td<?php echo $krs->GradeNilai->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_GradeNilai">
<input type="text" data-table="krs" data-field="x_GradeNilai" name="x_GradeNilai" id="x_GradeNilai" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->GradeNilai->getPlaceHolder()) ?>" value="<?php echo $krs->GradeNilai->EditValue ?>"<?php echo $krs->GradeNilai->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->BobotNilai->Visible) { // BobotNilai ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_BobotNilai" class="form-group">
		<label for="x_BobotNilai" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_BobotNilai"><?php echo $krs->BobotNilai->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_BobotNilai" id="z_BobotNilai" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->BobotNilai->CellAttributes() ?>>
			<span id="el_krs_BobotNilai">
<input type="text" data-table="krs" data-field="x_BobotNilai" name="x_BobotNilai" id="x_BobotNilai" size="30" placeholder="<?php echo ew_HtmlEncode($krs->BobotNilai->getPlaceHolder()) ?>" value="<?php echo $krs->BobotNilai->EditValue ?>"<?php echo $krs->BobotNilai->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_BobotNilai">
		<td><span id="elh_krs_BobotNilai"><?php echo $krs->BobotNilai->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_BobotNilai" id="z_BobotNilai" value="="></span></td>
		<td<?php echo $krs->BobotNilai->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_BobotNilai">
<input type="text" data-table="krs" data-field="x_BobotNilai" name="x_BobotNilai" id="x_BobotNilai" size="30" placeholder="<?php echo ew_HtmlEncode($krs->BobotNilai->getPlaceHolder()) ?>" value="<?php echo $krs->BobotNilai->EditValue ?>"<?php echo $krs->BobotNilai->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->StatusKRSID->Visible) { // StatusKRSID ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_StatusKRSID" class="form-group">
		<label for="x_StatusKRSID" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_StatusKRSID"><?php echo $krs->StatusKRSID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusKRSID" id="z_StatusKRSID" value="LIKE"></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->StatusKRSID->CellAttributes() ?>>
			<span id="el_krs_StatusKRSID">
<input type="text" data-table="krs" data-field="x_StatusKRSID" name="x_StatusKRSID" id="x_StatusKRSID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->StatusKRSID->getPlaceHolder()) ?>" value="<?php echo $krs->StatusKRSID->EditValue ?>"<?php echo $krs->StatusKRSID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusKRSID">
		<td><span id="elh_krs_StatusKRSID"><?php echo $krs->StatusKRSID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusKRSID" id="z_StatusKRSID" value="LIKE"></span></td>
		<td<?php echo $krs->StatusKRSID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_StatusKRSID">
<input type="text" data-table="krs" data-field="x_StatusKRSID" name="x_StatusKRSID" id="x_StatusKRSID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($krs->StatusKRSID->getPlaceHolder()) ?>" value="<?php echo $krs->StatusKRSID->EditValue ?>"<?php echo $krs->StatusKRSID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Tinggi->Visible) { // Tinggi ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Tinggi" class="form-group">
		<label for="x_Tinggi" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Tinggi"><?php echo $krs->Tinggi->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Tinggi" id="z_Tinggi" value="LIKE"></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Tinggi->CellAttributes() ?>>
			<span id="el_krs_Tinggi">
<input type="text" data-table="krs" data-field="x_Tinggi" name="x_Tinggi" id="x_Tinggi" size="30" maxlength="1" placeholder="<?php echo ew_HtmlEncode($krs->Tinggi->getPlaceHolder()) ?>" value="<?php echo $krs->Tinggi->EditValue ?>"<?php echo $krs->Tinggi->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tinggi">
		<td><span id="elh_krs_Tinggi"><?php echo $krs->Tinggi->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Tinggi" id="z_Tinggi" value="LIKE"></span></td>
		<td<?php echo $krs->Tinggi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Tinggi">
<input type="text" data-table="krs" data-field="x_Tinggi" name="x_Tinggi" id="x_Tinggi" size="30" maxlength="1" placeholder="<?php echo ew_HtmlEncode($krs->Tinggi->getPlaceHolder()) ?>" value="<?php echo $krs->Tinggi->EditValue ?>"<?php echo $krs->Tinggi->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Final->Visible) { // Final ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Final" class="form-group">
		<label class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Final"><?php echo $krs->Final->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Final" id="z_Final" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Final->CellAttributes() ?>>
			<span id="el_krs_Final">
<div id="tp_x_Final" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_Final" data-value-separator="<?php echo $krs->Final->DisplayValueSeparatorAttribute() ?>" name="x_Final" id="x_Final" value="{value}"<?php echo $krs->Final->EditAttributes() ?>></div>
<div id="dsl_x_Final" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->Final->RadioButtonListHtml(FALSE, "x_Final") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Final">
		<td><span id="elh_krs_Final"><?php echo $krs->Final->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Final" id="z_Final" value="="></span></td>
		<td<?php echo $krs->Final->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Final">
<div id="tp_x_Final" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_Final" data-value-separator="<?php echo $krs->Final->DisplayValueSeparatorAttribute() ?>" name="x_Final" id="x_Final" value="{value}"<?php echo $krs->Final->EditAttributes() ?>></div>
<div id="dsl_x_Final" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->Final->RadioButtonListHtml(FALSE, "x_Final") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Setara->Visible) { // Setara ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Setara" class="form-group">
		<label class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Setara"><?php echo $krs->Setara->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Setara" id="z_Setara" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Setara->CellAttributes() ?>>
			<span id="el_krs_Setara">
<div id="tp_x_Setara" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_Setara" data-value-separator="<?php echo $krs->Setara->DisplayValueSeparatorAttribute() ?>" name="x_Setara" id="x_Setara" value="{value}"<?php echo $krs->Setara->EditAttributes() ?>></div>
<div id="dsl_x_Setara" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->Setara->RadioButtonListHtml(FALSE, "x_Setara") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Setara">
		<td><span id="elh_krs_Setara"><?php echo $krs->Setara->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Setara" id="z_Setara" value="="></span></td>
		<td<?php echo $krs->Setara->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Setara">
<div id="tp_x_Setara" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_Setara" data-value-separator="<?php echo $krs->Setara->DisplayValueSeparatorAttribute() ?>" name="x_Setara" id="x_Setara" value="{value}"<?php echo $krs->Setara->EditAttributes() ?>></div>
<div id="dsl_x_Setara" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->Setara->RadioButtonListHtml(FALSE, "x_Setara") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Creator->Visible) { // Creator ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Creator" class="form-group">
		<label for="x_Creator" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Creator"><?php echo $krs->Creator->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Creator" id="z_Creator" value="LIKE"></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Creator->CellAttributes() ?>>
			<span id="el_krs_Creator">
<input type="text" data-table="krs" data-field="x_Creator" name="x_Creator" id="x_Creator" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->Creator->getPlaceHolder()) ?>" value="<?php echo $krs->Creator->EditValue ?>"<?php echo $krs->Creator->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Creator">
		<td><span id="elh_krs_Creator"><?php echo $krs->Creator->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Creator" id="z_Creator" value="LIKE"></span></td>
		<td<?php echo $krs->Creator->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Creator">
<input type="text" data-table="krs" data-field="x_Creator" name="x_Creator" id="x_Creator" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->Creator->getPlaceHolder()) ?>" value="<?php echo $krs->Creator->EditValue ?>"<?php echo $krs->Creator->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->CreateDate->Visible) { // CreateDate ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_CreateDate" class="form-group">
		<label for="x_CreateDate" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_CreateDate"><?php echo $krs->CreateDate->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_CreateDate" id="z_CreateDate" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->CreateDate->CellAttributes() ?>>
			<span id="el_krs_CreateDate">
<input type="text" data-table="krs" data-field="x_CreateDate" name="x_CreateDate" id="x_CreateDate" placeholder="<?php echo ew_HtmlEncode($krs->CreateDate->getPlaceHolder()) ?>" value="<?php echo $krs->CreateDate->EditValue ?>"<?php echo $krs->CreateDate->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_CreateDate">
		<td><span id="elh_krs_CreateDate"><?php echo $krs->CreateDate->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_CreateDate" id="z_CreateDate" value="="></span></td>
		<td<?php echo $krs->CreateDate->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_CreateDate">
<input type="text" data-table="krs" data-field="x_CreateDate" name="x_CreateDate" id="x_CreateDate" placeholder="<?php echo ew_HtmlEncode($krs->CreateDate->getPlaceHolder()) ?>" value="<?php echo $krs->CreateDate->EditValue ?>"<?php echo $krs->CreateDate->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->Editor->Visible) { // Editor ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_Editor" class="form-group">
		<label for="x_Editor" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_Editor"><?php echo $krs->Editor->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Editor" id="z_Editor" value="LIKE"></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->Editor->CellAttributes() ?>>
			<span id="el_krs_Editor">
<input type="text" data-table="krs" data-field="x_Editor" name="x_Editor" id="x_Editor" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->Editor->getPlaceHolder()) ?>" value="<?php echo $krs->Editor->EditValue ?>"<?php echo $krs->Editor->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Editor">
		<td><span id="elh_krs_Editor"><?php echo $krs->Editor->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Editor" id="z_Editor" value="LIKE"></span></td>
		<td<?php echo $krs->Editor->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_Editor">
<input type="text" data-table="krs" data-field="x_Editor" name="x_Editor" id="x_Editor" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($krs->Editor->getPlaceHolder()) ?>" value="<?php echo $krs->Editor->EditValue ?>"<?php echo $krs->Editor->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->EditDate->Visible) { // EditDate ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_EditDate" class="form-group">
		<label for="x_EditDate" class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_EditDate"><?php echo $krs->EditDate->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_EditDate" id="z_EditDate" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->EditDate->CellAttributes() ?>>
			<span id="el_krs_EditDate">
<input type="text" data-table="krs" data-field="x_EditDate" name="x_EditDate" id="x_EditDate" placeholder="<?php echo ew_HtmlEncode($krs->EditDate->getPlaceHolder()) ?>" value="<?php echo $krs->EditDate->EditValue ?>"<?php echo $krs->EditDate->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_EditDate">
		<td><span id="elh_krs_EditDate"><?php echo $krs->EditDate->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_EditDate" id="z_EditDate" value="="></span></td>
		<td<?php echo $krs->EditDate->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_EditDate">
<input type="text" data-table="krs" data-field="x_EditDate" name="x_EditDate" id="x_EditDate" placeholder="<?php echo ew_HtmlEncode($krs->EditDate->getPlaceHolder()) ?>" value="<?php echo $krs->EditDate->EditValue ?>"<?php echo $krs->EditDate->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($krs->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $krs_search->SearchLabelClass ?>"><span id="elh_krs_NA"><?php echo $krs->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $krs_search->SearchRightColumnClass ?>"><div<?php echo $krs->NA->CellAttributes() ?>>
			<span id="el_krs_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_NA" data-value-separator="<?php echo $krs->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $krs->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_krs_NA"><?php echo $krs->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $krs->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_krs_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="krs" data-field="x_NA" data-value-separator="<?php echo $krs->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $krs->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $krs->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $krs_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$krs_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$krs_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fkrssearch.Init();
</script>
<?php
$krs_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$krs_search->Page_Terminate();
?>
