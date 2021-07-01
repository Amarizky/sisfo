<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "jadwalinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$jadwal_search = NULL; // Initialize page object first

class cjadwal_search extends cjadwal {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'jadwal';

	// Page object name
	var $PageObjName = 'jadwal_search';

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

		// Table object (jadwal)
		if (!isset($GLOBALS["jadwal"]) || get_class($GLOBALS["jadwal"]) == "cjadwal") {
			$GLOBALS["jadwal"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["jadwal"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'jadwal', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("jadwallist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->ProdiID->SetVisibility();
		$this->TahunID->SetVisibility();
		$this->Sesi->SetVisibility();
		$this->Tingkat->SetVisibility();
		$this->KelasID->SetVisibility();
		$this->HariID->SetVisibility();
		$this->JamID->SetVisibility();
		$this->MKID->SetVisibility();
		$this->TeacherID->SetVisibility();

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
		global $EW_EXPORT, $jadwal;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($jadwal);
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
						$sSrchStr = "jadwallist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->ProdiID); // ProdiID
		$this->BuildSearchUrl($sSrchUrl, $this->TahunID); // TahunID
		$this->BuildSearchUrl($sSrchUrl, $this->Sesi); // Sesi
		$this->BuildSearchUrl($sSrchUrl, $this->Tingkat); // Tingkat
		$this->BuildSearchUrl($sSrchUrl, $this->KelasID); // KelasID
		$this->BuildSearchUrl($sSrchUrl, $this->HariID); // HariID
		$this->BuildSearchUrl($sSrchUrl, $this->JamID); // JamID
		$this->BuildSearchUrl($sSrchUrl, $this->MKID); // MKID
		$this->BuildSearchUrl($sSrchUrl, $this->TeacherID); // TeacherID
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
		// ProdiID

		$this->ProdiID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ProdiID"));
		$this->ProdiID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ProdiID");

		// TahunID
		$this->TahunID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TahunID"));
		$this->TahunID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TahunID");

		// Sesi
		$this->Sesi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Sesi"));
		$this->Sesi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Sesi");

		// Tingkat
		$this->Tingkat->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tingkat"));
		$this->Tingkat->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tingkat");

		// KelasID
		$this->KelasID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KelasID"));
		$this->KelasID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KelasID");

		// HariID
		$this->HariID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_HariID"));
		$this->HariID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_HariID");

		// JamID
		$this->JamID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_JamID"));
		$this->JamID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_JamID");

		// MKID
		$this->MKID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_MKID"));
		$this->MKID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_MKID");

		// TeacherID
		$this->TeacherID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TeacherID"));
		$this->TeacherID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TeacherID");
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// JadwalID
		// ProdiID
		// TahunID
		// Sesi
		// Tingkat
		// KelasID
		// HariID
		// JamID
		// MKID
		// TeacherID
		// JamMulai
		// JamSelesai
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// JadwalID
		$this->JadwalID->ViewValue = $this->JadwalID->CurrentValue;
		$this->JadwalID->ViewCustomAttributes = "";

		// ProdiID
		if (strval($this->ProdiID->CurrentValue) <> "") {
			$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `ProdiID`, `ProdiID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
		$sWhereWrk = "";
		$this->ProdiID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `ProdiID` ASC";
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

		// TahunID
		$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
		$this->TahunID->ViewCustomAttributes = "";

		// Sesi
		if (strval($this->Sesi->CurrentValue) <> "") {
			$sFilterWrk = "`Sesi`" . ew_SearchString("=", $this->Sesi->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `Sesi`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_sesi`";
		$sWhereWrk = "";
		$this->Sesi->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Sesi->ViewValue = $this->Sesi->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Sesi->ViewValue = $this->Sesi->CurrentValue;
			}
		} else {
			$this->Sesi->ViewValue = NULL;
		}
		$this->Sesi->ViewCustomAttributes = "";

		// Tingkat
		if (strval($this->Tingkat->CurrentValue) <> "") {
			$sFilterWrk = "`Tingkat`" . ew_SearchString("=", $this->Tingkat->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
		$sWhereWrk = "";
		$this->Tingkat->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Tingkat->ViewValue = $this->Tingkat->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Tingkat->ViewValue = $this->Tingkat->CurrentValue;
			}
		} else {
			$this->Tingkat->ViewValue = NULL;
		}
		$this->Tingkat->CellCssStyle .= "text-align: center;";
		$this->Tingkat->ViewCustomAttributes = "";

		// KelasID
		if (strval($this->KelasID->CurrentValue) <> "") {
			$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->KelasID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
		$sWhereWrk = "";
		$this->KelasID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KelasID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KelasID->ViewValue = $this->KelasID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KelasID->ViewValue = $this->KelasID->CurrentValue;
			}
		} else {
			$this->KelasID->ViewValue = NULL;
		}
		$this->KelasID->ViewCustomAttributes = "";

		// HariID
		if (strval($this->HariID->CurrentValue) <> "") {
			$sFilterWrk = "`HariID`" . ew_SearchString("=", $this->HariID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `HariID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hari`";
		$sWhereWrk = "";
		$this->HariID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->HariID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->HariID->ViewValue = $this->HariID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->HariID->ViewValue = $this->HariID->CurrentValue;
			}
		} else {
			$this->HariID->ViewValue = NULL;
		}
		$this->HariID->ViewCustomAttributes = "";

		// JamID
		if (strval($this->JamID->CurrentValue) <> "") {
			$sFilterWrk = "`JamID`" . ew_SearchString("=", $this->JamID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `JamID`, `JamID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_jamkul`";
		$sWhereWrk = "";
		$this->JamID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `JamID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->JamID->ViewValue = $this->JamID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->JamID->ViewValue = $this->JamID->CurrentValue;
			}
		} else {
			$this->JamID->ViewValue = NULL;
		}
		$this->JamID->ViewCustomAttributes = "";

		// MKID
		if (strval($this->MKID->CurrentValue) <> "") {
			$sFilterWrk = "`MKID`" . ew_SearchString("=", $this->MKID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `MKID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `mk`";
		$sWhereWrk = "";
		$this->MKID->LookupFilters = array("dx1" => '`Nama`');
		$lookuptblfilter = "`Tingkat` in (Tingkat)";
		ew_AddFilter($sWhereWrk, $lookuptblfilter);
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->MKID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->MKID->ViewValue = $this->MKID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->MKID->ViewValue = $this->MKID->CurrentValue;
			}
		} else {
			$this->MKID->ViewValue = NULL;
		}
		$this->MKID->ViewCustomAttributes = "";

		// TeacherID
		$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
		if (strval($this->TeacherID->CurrentValue) <> "") {
			$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->TeacherID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `TeacherID`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
		$sWhereWrk = "";
		$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->TeacherID->ViewValue = $this->TeacherID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->TeacherID->ViewValue = $this->TeacherID->CurrentValue;
			}
		} else {
			$this->TeacherID->ViewValue = NULL;
		}
		$this->TeacherID->ViewCustomAttributes = "";

		// JamMulai
		$this->JamMulai->ViewValue = $this->JamMulai->CurrentValue;
		$this->JamMulai->ViewCustomAttributes = "";

		// JamSelesai
		$this->JamSelesai->ViewValue = $this->JamSelesai->CurrentValue;
		$this->JamSelesai->ViewCustomAttributes = "";

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
		$this->NA->ViewValue = $this->NA->CurrentValue;
		$this->NA->ViewCustomAttributes = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// TahunID
			$this->TahunID->LinkCustomAttributes = "";
			$this->TahunID->HrefValue = "";
			$this->TahunID->TooltipValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";
			$this->Sesi->TooltipValue = "";

			// Tingkat
			$this->Tingkat->LinkCustomAttributes = "";
			$this->Tingkat->HrefValue = "";
			$this->Tingkat->TooltipValue = "";

			// KelasID
			$this->KelasID->LinkCustomAttributes = "";
			$this->KelasID->HrefValue = "";
			$this->KelasID->TooltipValue = "";

			// HariID
			$this->HariID->LinkCustomAttributes = "";
			$this->HariID->HrefValue = "";
			$this->HariID->TooltipValue = "";

			// JamID
			$this->JamID->LinkCustomAttributes = "";
			$this->JamID->HrefValue = "";
			$this->JamID->TooltipValue = "";

			// MKID
			$this->MKID->LinkCustomAttributes = "";
			$this->MKID->HrefValue = "";
			$this->MKID->TooltipValue = "";

			// TeacherID
			$this->TeacherID->LinkCustomAttributes = "";
			$this->TeacherID->HrefValue = "";
			$this->TeacherID->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// ProdiID
			$this->ProdiID->EditAttrs["class"] = "form-control";
			$this->ProdiID->EditCustomAttributes = "";
			if (trim(strval($this->ProdiID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ProdiID`" . ew_SearchString("=", $this->ProdiID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `ProdiID`, `ProdiID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->ProdiID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `ProdiID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProdiID->EditValue = $arwrk;

			// TahunID
			$this->TahunID->EditAttrs["class"] = "form-control";
			$this->TahunID->EditCustomAttributes = "";
			$this->TahunID->EditValue = ew_HtmlEncode($this->TahunID->AdvancedSearch->SearchValue);
			$this->TahunID->PlaceHolder = ew_RemoveHtml($this->TahunID->FldCaption());

			// Sesi
			$this->Sesi->EditAttrs["class"] = "form-control";
			$this->Sesi->EditCustomAttributes = "";
			if (trim(strval($this->Sesi->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Sesi`" . ew_SearchString("=", $this->Sesi->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `Sesi`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_sesi`";
			$sWhereWrk = "";
			$this->Sesi->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Sesi->EditValue = $arwrk;

			// Tingkat
			$this->Tingkat->EditAttrs["class"] = "form-control";
			$this->Tingkat->EditCustomAttributes = "";
			if (trim(strval($this->Tingkat->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Tingkat`" . ew_SearchString("=", $this->Tingkat->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Tingkat->EditValue = $arwrk;

			// KelasID
			$this->KelasID->EditAttrs["class"] = "form-control";
			$this->KelasID->EditCustomAttributes = "";
			if (trim(strval($this->KelasID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->KelasID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, `Tingkat` AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->KelasID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KelasID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KelasID->EditValue = $arwrk;

			// HariID
			$this->HariID->EditAttrs["class"] = "form-control";
			$this->HariID->EditCustomAttributes = "";
			if (trim(strval($this->HariID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`HariID`" . ew_SearchString("=", $this->HariID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `HariID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_hari`";
			$sWhereWrk = "";
			$this->HariID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->HariID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->HariID->EditValue = $arwrk;

			// JamID
			$this->JamID->EditAttrs["class"] = "form-control";
			$this->JamID->EditCustomAttributes = "";
			if (trim(strval($this->JamID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`JamID`" . ew_SearchString("=", $this->JamID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `JamID`, `JamID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `HariID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_jamkul`";
			$sWhereWrk = "";
			$this->JamID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `JamID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->JamID->EditValue = $arwrk;

			// MKID
			$this->MKID->EditCustomAttributes = "";
			if (trim(strval($this->MKID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`MKID`" . ew_SearchString("=", $this->MKID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `MKID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `mk`";
			$sWhereWrk = "";
			$this->MKID->LookupFilters = array("dx1" => '`Nama`');
			$lookuptblfilter = "`Tingkat` in (Tingkat)";
			ew_AddFilter($sWhereWrk, $lookuptblfilter);
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->MKID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$this->MKID->AdvancedSearch->ViewValue = $this->MKID->DisplayValue($arwrk);
			} else {
				$this->MKID->AdvancedSearch->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->MKID->EditValue = $arwrk;

			// TeacherID
			$this->TeacherID->EditAttrs["class"] = "form-control";
			$this->TeacherID->EditCustomAttributes = "";
			$this->TeacherID->EditValue = ew_HtmlEncode($this->TeacherID->AdvancedSearch->SearchValue);
			if (strval($this->TeacherID->AdvancedSearch->SearchValue) <> "") {
				$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->TeacherID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			$sSqlWrk = "SELECT `TeacherID`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
			$sWhereWrk = "";
			$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
					$this->TeacherID->EditValue = $this->TeacherID->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->TeacherID->EditValue = ew_HtmlEncode($this->TeacherID->AdvancedSearch->SearchValue);
				}
			} else {
				$this->TeacherID->EditValue = NULL;
			}
			$this->TeacherID->PlaceHolder = ew_RemoveHtml($this->TeacherID->FldCaption());
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
		$this->ProdiID->AdvancedSearch->Load();
		$this->TahunID->AdvancedSearch->Load();
		$this->Sesi->AdvancedSearch->Load();
		$this->Tingkat->AdvancedSearch->Load();
		$this->KelasID->AdvancedSearch->Load();
		$this->HariID->AdvancedSearch->Load();
		$this->JamID->AdvancedSearch->Load();
		$this->MKID->AdvancedSearch->Load();
		$this->TeacherID->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("jadwallist.php"), "", $this->TableVar, TRUE);
		$PageId = "search";
		$Breadcrumb->Add("search", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_ProdiID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `ProdiID` AS `LinkFld`, `ProdiID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_prodi`";
			$sWhereWrk = "";
			$this->ProdiID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`ProdiID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->ProdiID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `ProdiID` ASC";
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_Sesi":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `Sesi` AS `LinkFld`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_sesi`";
			$sWhereWrk = "";
			$this->Sesi->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Sesi` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_Tingkat":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT DISTINCT `Tingkat` AS `LinkFld`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Tingkat` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KelasID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KelasID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
			$sWhereWrk = "{filter}";
			$this->KelasID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KelasID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "", "f2" => '`Tingkat` IN ({filter_value})', "t2" => "200", "fn2" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KelasID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_HariID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `HariID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_hari`";
			$sWhereWrk = "";
			$this->HariID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`HariID` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->HariID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_JamID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `JamID` AS `LinkFld`, `JamID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_jamkul`";
			$sWhereWrk = "{filter}";
			$this->JamID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`JamID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`HariID` IN ({filter_value})', "t1" => "3", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->JamID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `JamID` ASC";
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_MKID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `MKID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `mk`";
			$sWhereWrk = "{filter}";
			$this->MKID->LookupFilters = array("dx1" => '`Nama`');
			$lookuptblfilter = "`Tingkat` in (Tingkat)";
			ew_AddFilter($sWhereWrk, $lookuptblfilter);
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`MKID` = {filter_value}', "t0" => "20", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->MKID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_TeacherID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `TeacherID` AS `LinkFld`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
			$sWhereWrk = "{filter}";
			$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`TeacherID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
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
		case "x_TeacherID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `TeacherID`, `AliasCode` AS `DispFld`, `Nama` AS `Disp2Fld` FROM `teacher`";
			$sWhereWrk = "`AliasCode` LIKE '%{query_value}%' OR `Nama` LIKE '%{query_value}%' OR CONCAT(`AliasCode`,'" . ew_ValueSeparator(1, $this->TeacherID) . "',`Nama`) LIKE '{query_value}%'";
			$this->TeacherID->LookupFilters = array("dx1" => '`AliasCode`', "dx2" => '`Nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TeacherID, $sWhereWrk); // Call Lookup selecting
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
if (!isset($jadwal_search)) $jadwal_search = new cjadwal_search();

// Page init
$jadwal_search->Page_Init();

// Page main
$jadwal_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$jadwal_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($jadwal_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fjadwalsearch = new ew_Form("fjadwalsearch", "search");
<?php } else { ?>
var CurrentForm = fjadwalsearch = new ew_Form("fjadwalsearch", "search");
<?php } ?>

// Form_CustomValidate event
fjadwalsearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fjadwalsearch.ValidateRequired = true;
<?php } else { ?>
fjadwalsearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fjadwalsearch.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_ProdiID","","",""],"ParentFields":[],"ChildFields":["x_TahunID","x_KelasID","x_MKID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fjadwalsearch.Lists["x_Sesi"] = {"LinkField":"x_Sesi","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaSesi","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_sesi"};
fjadwalsearch.Lists["x_Tingkat"] = {"LinkField":"x_Tingkat","Ajax":true,"AutoFill":false,"DisplayFields":["x_Tingkat","","",""],"ParentFields":[],"ChildFields":["x_KelasID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"kelas"};
fjadwalsearch.Lists["x_KelasID"] = {"LinkField":"x_KelasID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":["x_ProdiID","x_Tingkat"],"ChildFields":[],"FilterFields":["x_ProdiID","x_Tingkat"],"Options":[],"Template":"","LinkTable":"kelas"};
fjadwalsearch.Lists["x_HariID"] = {"LinkField":"x_HariID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":["x_JamID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_hari"};
fjadwalsearch.Lists["x_JamID"] = {"LinkField":"x_JamID","Ajax":true,"AutoFill":false,"DisplayFields":["x_JamID","","",""],"ParentFields":["x_HariID"],"ChildFields":[],"FilterFields":["x_HariID"],"Options":[],"Template":"","LinkTable":"master_jamkul"};
fjadwalsearch.Lists["x_MKID"] = {"LinkField":"x_MKID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":["x_ProdiID"],"ChildFields":[],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"mk"};
fjadwalsearch.Lists["x_TeacherID"] = {"LinkField":"x_TeacherID","Ajax":true,"AutoFill":false,"DisplayFields":["x_AliasCode","x_Nama","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"teacher"};

// Form object for search
// Validate function for search

fjadwalsearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$jadwal_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $jadwal_search->ShowPageHeader(); ?>
<?php
$jadwal_search->ShowMessage();
?>
<form name="fjadwalsearch" id="fjadwalsearch" class="<?php echo $jadwal_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($jadwal_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $jadwal_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="jadwal">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($jadwal_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$jadwal_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $jadwal_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_jadwalsearch" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($jadwal->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $jadwal_search->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label for="x_ProdiID" class="<?php echo $jadwal_search->SearchLabelClass ?>"><span id="elh_jadwal_ProdiID"><?php echo $jadwal->ProdiID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></p>
		</label>
		<div class="<?php echo $jadwal_search->SearchRightColumnClass ?>"><div<?php echo $jadwal->ProdiID->CellAttributes() ?>>
			<span id="el_jadwal_ProdiID">
<?php $jadwal->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_ProdiID" data-value-separator="<?php echo $jadwal->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $jadwal->ProdiID->EditAttributes() ?>>
<?php echo $jadwal->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $jadwal->ProdiID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_jadwal_ProdiID"><?php echo $jadwal->ProdiID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></span></td>
		<td<?php echo $jadwal->ProdiID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_jadwal_ProdiID">
<?php $jadwal->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_ProdiID" data-value-separator="<?php echo $jadwal->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $jadwal->ProdiID->EditAttributes() ?>>
<?php echo $jadwal->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $jadwal->ProdiID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->TahunID->Visible) { // TahunID ?>
<?php if (ew_IsMobile() || $jadwal_search->IsModal) { ?>
	<div id="r_TahunID" class="form-group">
		<label for="x_TahunID" class="<?php echo $jadwal_search->SearchLabelClass ?>"><span id="elh_jadwal_TahunID"><?php echo $jadwal->TahunID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TahunID" id="z_TahunID" value="LIKE"></p>
		</label>
		<div class="<?php echo $jadwal_search->SearchRightColumnClass ?>"><div<?php echo $jadwal->TahunID->CellAttributes() ?>>
			<span id="el_jadwal_TahunID">
<input type="text" data-table="jadwal" data-field="x_TahunID" name="x_TahunID" id="x_TahunID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($jadwal->TahunID->getPlaceHolder()) ?>" value="<?php echo $jadwal->TahunID->EditValue ?>"<?php echo $jadwal->TahunID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TahunID">
		<td><span id="elh_jadwal_TahunID"><?php echo $jadwal->TahunID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TahunID" id="z_TahunID" value="LIKE"></span></td>
		<td<?php echo $jadwal->TahunID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_jadwal_TahunID">
<input type="text" data-table="jadwal" data-field="x_TahunID" name="x_TahunID" id="x_TahunID" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($jadwal->TahunID->getPlaceHolder()) ?>" value="<?php echo $jadwal->TahunID->EditValue ?>"<?php echo $jadwal->TahunID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->Sesi->Visible) { // Sesi ?>
<?php if (ew_IsMobile() || $jadwal_search->IsModal) { ?>
	<div id="r_Sesi" class="form-group">
		<label for="x_Sesi" class="<?php echo $jadwal_search->SearchLabelClass ?>"><span id="elh_jadwal_Sesi"><?php echo $jadwal->Sesi->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Sesi" id="z_Sesi" value="="></p>
		</label>
		<div class="<?php echo $jadwal_search->SearchRightColumnClass ?>"><div<?php echo $jadwal->Sesi->CellAttributes() ?>>
			<span id="el_jadwal_Sesi">
<select data-table="jadwal" data-field="x_Sesi" data-value-separator="<?php echo $jadwal->Sesi->DisplayValueSeparatorAttribute() ?>" id="x_Sesi" name="x_Sesi"<?php echo $jadwal->Sesi->EditAttributes() ?>>
<?php echo $jadwal->Sesi->SelectOptionListHtml("x_Sesi") ?>
</select>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $jadwal->Sesi->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Sesi">
		<td><span id="elh_jadwal_Sesi"><?php echo $jadwal->Sesi->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Sesi" id="z_Sesi" value="="></span></td>
		<td<?php echo $jadwal->Sesi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_jadwal_Sesi">
<select data-table="jadwal" data-field="x_Sesi" data-value-separator="<?php echo $jadwal->Sesi->DisplayValueSeparatorAttribute() ?>" id="x_Sesi" name="x_Sesi"<?php echo $jadwal->Sesi->EditAttributes() ?>>
<?php echo $jadwal->Sesi->SelectOptionListHtml("x_Sesi") ?>
</select>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $jadwal->Sesi->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->Tingkat->Visible) { // Tingkat ?>
<?php if (ew_IsMobile() || $jadwal_search->IsModal) { ?>
	<div id="r_Tingkat" class="form-group">
		<label for="x_Tingkat" class="<?php echo $jadwal_search->SearchLabelClass ?>"><span id="elh_jadwal_Tingkat"><?php echo $jadwal->Tingkat->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Tingkat" id="z_Tingkat" value="LIKE"></p>
		</label>
		<div class="<?php echo $jadwal_search->SearchRightColumnClass ?>"><div<?php echo $jadwal->Tingkat->CellAttributes() ?>>
			<span id="el_jadwal_Tingkat">
<?php $jadwal->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_Tingkat" data-value-separator="<?php echo $jadwal->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $jadwal->Tingkat->EditAttributes() ?>>
<?php echo $jadwal->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $jadwal->Tingkat->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tingkat">
		<td><span id="elh_jadwal_Tingkat"><?php echo $jadwal->Tingkat->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Tingkat" id="z_Tingkat" value="LIKE"></span></td>
		<td<?php echo $jadwal->Tingkat->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_jadwal_Tingkat">
<?php $jadwal->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_Tingkat" data-value-separator="<?php echo $jadwal->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $jadwal->Tingkat->EditAttributes() ?>>
<?php echo $jadwal->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $jadwal->Tingkat->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->KelasID->Visible) { // KelasID ?>
<?php if (ew_IsMobile() || $jadwal_search->IsModal) { ?>
	<div id="r_KelasID" class="form-group">
		<label for="x_KelasID" class="<?php echo $jadwal_search->SearchLabelClass ?>"><span id="elh_jadwal_KelasID"><?php echo $jadwal->KelasID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KelasID" id="z_KelasID" value="LIKE"></p>
		</label>
		<div class="<?php echo $jadwal_search->SearchRightColumnClass ?>"><div<?php echo $jadwal->KelasID->CellAttributes() ?>>
			<span id="el_jadwal_KelasID">
<select data-table="jadwal" data-field="x_KelasID" data-value-separator="<?php echo $jadwal->KelasID->DisplayValueSeparatorAttribute() ?>" id="x_KelasID" name="x_KelasID"<?php echo $jadwal->KelasID->EditAttributes() ?>>
<?php echo $jadwal->KelasID->SelectOptionListHtml("x_KelasID") ?>
</select>
<input type="hidden" name="s_x_KelasID" id="s_x_KelasID" value="<?php echo $jadwal->KelasID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KelasID">
		<td><span id="elh_jadwal_KelasID"><?php echo $jadwal->KelasID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KelasID" id="z_KelasID" value="LIKE"></span></td>
		<td<?php echo $jadwal->KelasID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_jadwal_KelasID">
<select data-table="jadwal" data-field="x_KelasID" data-value-separator="<?php echo $jadwal->KelasID->DisplayValueSeparatorAttribute() ?>" id="x_KelasID" name="x_KelasID"<?php echo $jadwal->KelasID->EditAttributes() ?>>
<?php echo $jadwal->KelasID->SelectOptionListHtml("x_KelasID") ?>
</select>
<input type="hidden" name="s_x_KelasID" id="s_x_KelasID" value="<?php echo $jadwal->KelasID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->HariID->Visible) { // HariID ?>
<?php if (ew_IsMobile() || $jadwal_search->IsModal) { ?>
	<div id="r_HariID" class="form-group">
		<label for="x_HariID" class="<?php echo $jadwal_search->SearchLabelClass ?>"><span id="elh_jadwal_HariID"><?php echo $jadwal->HariID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_HariID" id="z_HariID" value="="></p>
		</label>
		<div class="<?php echo $jadwal_search->SearchRightColumnClass ?>"><div<?php echo $jadwal->HariID->CellAttributes() ?>>
			<span id="el_jadwal_HariID">
<?php $jadwal->HariID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->HariID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_HariID" data-value-separator="<?php echo $jadwal->HariID->DisplayValueSeparatorAttribute() ?>" id="x_HariID" name="x_HariID"<?php echo $jadwal->HariID->EditAttributes() ?>>
<?php echo $jadwal->HariID->SelectOptionListHtml("x_HariID") ?>
</select>
<input type="hidden" name="s_x_HariID" id="s_x_HariID" value="<?php echo $jadwal->HariID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_HariID">
		<td><span id="elh_jadwal_HariID"><?php echo $jadwal->HariID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_HariID" id="z_HariID" value="="></span></td>
		<td<?php echo $jadwal->HariID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_jadwal_HariID">
<?php $jadwal->HariID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$jadwal->HariID->EditAttrs["onchange"]; ?>
<select data-table="jadwal" data-field="x_HariID" data-value-separator="<?php echo $jadwal->HariID->DisplayValueSeparatorAttribute() ?>" id="x_HariID" name="x_HariID"<?php echo $jadwal->HariID->EditAttributes() ?>>
<?php echo $jadwal->HariID->SelectOptionListHtml("x_HariID") ?>
</select>
<input type="hidden" name="s_x_HariID" id="s_x_HariID" value="<?php echo $jadwal->HariID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->JamID->Visible) { // JamID ?>
<?php if (ew_IsMobile() || $jadwal_search->IsModal) { ?>
	<div id="r_JamID" class="form-group">
		<label for="x_JamID" class="<?php echo $jadwal_search->SearchLabelClass ?>"><span id="elh_jadwal_JamID"><?php echo $jadwal->JamID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_JamID" id="z_JamID" value="LIKE"></p>
		</label>
		<div class="<?php echo $jadwal_search->SearchRightColumnClass ?>"><div<?php echo $jadwal->JamID->CellAttributes() ?>>
			<span id="el_jadwal_JamID">
<select data-table="jadwal" data-field="x_JamID" data-value-separator="<?php echo $jadwal->JamID->DisplayValueSeparatorAttribute() ?>" id="x_JamID" name="x_JamID"<?php echo $jadwal->JamID->EditAttributes() ?>>
<?php echo $jadwal->JamID->SelectOptionListHtml("x_JamID") ?>
</select>
<input type="hidden" name="s_x_JamID" id="s_x_JamID" value="<?php echo $jadwal->JamID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_JamID">
		<td><span id="elh_jadwal_JamID"><?php echo $jadwal->JamID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_JamID" id="z_JamID" value="LIKE"></span></td>
		<td<?php echo $jadwal->JamID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_jadwal_JamID">
<select data-table="jadwal" data-field="x_JamID" data-value-separator="<?php echo $jadwal->JamID->DisplayValueSeparatorAttribute() ?>" id="x_JamID" name="x_JamID"<?php echo $jadwal->JamID->EditAttributes() ?>>
<?php echo $jadwal->JamID->SelectOptionListHtml("x_JamID") ?>
</select>
<input type="hidden" name="s_x_JamID" id="s_x_JamID" value="<?php echo $jadwal->JamID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->MKID->Visible) { // MKID ?>
<?php if (ew_IsMobile() || $jadwal_search->IsModal) { ?>
	<div id="r_MKID" class="form-group">
		<label for="x_MKID" class="<?php echo $jadwal_search->SearchLabelClass ?>"><span id="elh_jadwal_MKID"><?php echo $jadwal->MKID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_MKID" id="z_MKID" value="="></p>
		</label>
		<div class="<?php echo $jadwal_search->SearchRightColumnClass ?>"><div<?php echo $jadwal->MKID->CellAttributes() ?>>
			<span id="el_jadwal_MKID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_MKID"><?php echo (strval($jadwal->MKID->AdvancedSearch->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $jadwal->MKID->AdvancedSearch->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->MKID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_MKID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="jadwal" data-field="x_MKID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->MKID->DisplayValueSeparatorAttribute() ?>" name="x_MKID" id="x_MKID" value="<?php echo $jadwal->MKID->AdvancedSearch->SearchValue ?>"<?php echo $jadwal->MKID->EditAttributes() ?>>
<input type="hidden" name="s_x_MKID" id="s_x_MKID" value="<?php echo $jadwal->MKID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_MKID">
		<td><span id="elh_jadwal_MKID"><?php echo $jadwal->MKID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_MKID" id="z_MKID" value="="></span></td>
		<td<?php echo $jadwal->MKID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_jadwal_MKID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_MKID"><?php echo (strval($jadwal->MKID->AdvancedSearch->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $jadwal->MKID->AdvancedSearch->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->MKID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_MKID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="jadwal" data-field="x_MKID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->MKID->DisplayValueSeparatorAttribute() ?>" name="x_MKID" id="x_MKID" value="<?php echo $jadwal->MKID->AdvancedSearch->SearchValue ?>"<?php echo $jadwal->MKID->EditAttributes() ?>>
<input type="hidden" name="s_x_MKID" id="s_x_MKID" value="<?php echo $jadwal->MKID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($jadwal->TeacherID->Visible) { // TeacherID ?>
<?php if (ew_IsMobile() || $jadwal_search->IsModal) { ?>
	<div id="r_TeacherID" class="form-group">
		<label class="<?php echo $jadwal_search->SearchLabelClass ?>"><span id="elh_jadwal_TeacherID"><?php echo $jadwal->TeacherID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TeacherID" id="z_TeacherID" value="LIKE"></p>
		</label>
		<div class="<?php echo $jadwal_search->SearchRightColumnClass ?>"><div<?php echo $jadwal->TeacherID->CellAttributes() ?>>
			<span id="el_jadwal_TeacherID">
<?php
$wrkonchange = trim(" " . @$jadwal->TeacherID->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$jadwal->TeacherID->EditAttrs["onchange"] = "";
?>
<span id="as_x_TeacherID" style="white-space: nowrap; z-index: 8900">
	<input type="text" name="sv_x_TeacherID" id="sv_x_TeacherID" value="<?php echo $jadwal->TeacherID->EditValue ?>" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>"<?php echo $jadwal->TeacherID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_TeacherID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->TeacherID->DisplayValueSeparatorAttribute() ?>" name="x_TeacherID" id="x_TeacherID" value="<?php echo ew_HtmlEncode($jadwal->TeacherID->AdvancedSearch->SearchValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TeacherID" id="q_x_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fjadwalsearch.CreateAutoSuggest({"id":"x_TeacherID","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->TeacherID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_TeacherID',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x_TeacherID" id="s_x_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(false) ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TeacherID">
		<td><span id="elh_jadwal_TeacherID"><?php echo $jadwal->TeacherID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TeacherID" id="z_TeacherID" value="LIKE"></span></td>
		<td<?php echo $jadwal->TeacherID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_jadwal_TeacherID">
<?php
$wrkonchange = trim(" " . @$jadwal->TeacherID->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$jadwal->TeacherID->EditAttrs["onchange"] = "";
?>
<span id="as_x_TeacherID" style="white-space: nowrap; z-index: 8900">
	<input type="text" name="sv_x_TeacherID" id="sv_x_TeacherID" value="<?php echo $jadwal->TeacherID->EditValue ?>" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($jadwal->TeacherID->getPlaceHolder()) ?>"<?php echo $jadwal->TeacherID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="jadwal" data-field="x_TeacherID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $jadwal->TeacherID->DisplayValueSeparatorAttribute() ?>" name="x_TeacherID" id="x_TeacherID" value="<?php echo ew_HtmlEncode($jadwal->TeacherID->AdvancedSearch->SearchValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_TeacherID" id="q_x_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fjadwalsearch.CreateAutoSuggest({"id":"x_TeacherID","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($jadwal->TeacherID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_TeacherID',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x_TeacherID" id="s_x_TeacherID" value="<?php echo $jadwal->TeacherID->LookupFilterQuery(false) ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $jadwal_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$jadwal_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$jadwal_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fjadwalsearch.Init();
</script>
<?php
$jadwal_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$jadwal_search->Page_Terminate();
?>
