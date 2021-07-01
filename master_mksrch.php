<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_mkinfo.php" ?>
<?php include_once "kurikuluminfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_mk_search = NULL; // Initialize page object first

class cmaster_mk_search extends cmaster_mk {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_mk';

	// Page object name
	var $PageObjName = 'master_mk_search';

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

		// Table object (master_mk)
		if (!isset($GLOBALS["master_mk"]) || get_class($GLOBALS["master_mk"]) == "cmaster_mk") {
			$GLOBALS["master_mk"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_mk"];
		}

		// Table object (kurikulum)
		if (!isset($GLOBALS['kurikulum'])) $GLOBALS['kurikulum'] = new ckurikulum();

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_mk', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("master_mklist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->KampusID->SetVisibility();
		$this->ProdiID->SetVisibility();
		$this->KurikulumID->SetVisibility();
		$this->MKKode->SetVisibility();
		$this->Nama->SetVisibility();
		$this->Singkatan->SetVisibility();
		$this->Tingkat->SetVisibility();
		$this->Sesi->SetVisibility();
		$this->Wajib->SetVisibility();
		$this->Deskripsi->SetVisibility();
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
		global $EW_EXPORT, $master_mk;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_mk);
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
						$sSrchStr = "master_mklist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->KampusID); // KampusID
		$this->BuildSearchUrl($sSrchUrl, $this->ProdiID); // ProdiID
		$this->BuildSearchUrl($sSrchUrl, $this->KurikulumID); // KurikulumID
		$this->BuildSearchUrl($sSrchUrl, $this->MKKode); // MKKode
		$this->BuildSearchUrl($sSrchUrl, $this->Nama); // Nama
		$this->BuildSearchUrl($sSrchUrl, $this->Singkatan); // Singkatan
		$this->BuildSearchUrl($sSrchUrl, $this->Tingkat); // Tingkat
		$this->BuildSearchUrl($sSrchUrl, $this->Sesi); // Sesi
		$this->BuildSearchUrl($sSrchUrl, $this->Wajib); // Wajib
		$this->BuildSearchUrl($sSrchUrl, $this->Deskripsi); // Deskripsi
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
		// KampusID

		$this->KampusID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KampusID"));
		$this->KampusID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KampusID");

		// ProdiID
		$this->ProdiID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ProdiID"));
		$this->ProdiID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ProdiID");

		// KurikulumID
		$this->KurikulumID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_KurikulumID"));
		$this->KurikulumID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_KurikulumID");

		// MKKode
		$this->MKKode->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_MKKode"));
		$this->MKKode->AdvancedSearch->SearchOperator = $objForm->GetValue("z_MKKode");

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Nama"));
		$this->Nama->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Nama");

		// Singkatan
		$this->Singkatan->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Singkatan"));
		$this->Singkatan->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Singkatan");

		// Tingkat
		$this->Tingkat->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tingkat"));
		$this->Tingkat->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tingkat");

		// Sesi
		$this->Sesi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Sesi"));
		$this->Sesi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Sesi");

		// Wajib
		$this->Wajib->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Wajib"));
		$this->Wajib->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Wajib");

		// Deskripsi
		$this->Deskripsi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Deskripsi"));
		$this->Deskripsi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Deskripsi");

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
		// MKID
		// KampusID
		// ProdiID
		// KurikulumID
		// MKKode
		// Nama
		// Singkatan
		// Tingkat
		// Sesi
		// Wajib
		// Deskripsi
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// MKID
		$this->MKID->ViewValue = $this->MKID->CurrentValue;
		$this->MKID->ViewCustomAttributes = "";

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

		// KurikulumID
		if (strval($this->KurikulumID->CurrentValue) <> "") {
			$sFilterWrk = "`KurikulumID`" . ew_SearchString("=", $this->KurikulumID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `KurikulumID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kurikulum`";
		$sWhereWrk = "";
		$this->KurikulumID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->KurikulumID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->KurikulumID->ViewValue = $this->KurikulumID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->KurikulumID->ViewValue = $this->KurikulumID->CurrentValue;
			}
		} else {
			$this->KurikulumID->ViewValue = NULL;
		}
		$this->KurikulumID->ViewCustomAttributes = "";

		// MKKode
		$this->MKKode->ViewValue = $this->MKKode->CurrentValue;
		$this->MKKode->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// Singkatan
		$this->Singkatan->ViewValue = $this->Singkatan->CurrentValue;
		$this->Singkatan->ViewCustomAttributes = "";

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
		$this->Tingkat->ViewCustomAttributes = "";

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

		// Wajib
		if (ew_ConvertToBool($this->Wajib->CurrentValue)) {
			$this->Wajib->ViewValue = $this->Wajib->FldTagCaption(1) <> "" ? $this->Wajib->FldTagCaption(1) : "Y";
		} else {
			$this->Wajib->ViewValue = $this->Wajib->FldTagCaption(2) <> "" ? $this->Wajib->FldTagCaption(2) : "N";
		}
		$this->Wajib->ViewCustomAttributes = "";

		// Deskripsi
		$this->Deskripsi->ViewValue = $this->Deskripsi->CurrentValue;
		$this->Deskripsi->ViewCustomAttributes = "";

		// Creator
		$this->Creator->ViewValue = $this->Creator->CurrentValue;
		$this->Creator->ViewCustomAttributes = "";

		// CreateDate
		$this->CreateDate->ViewValue = $this->CreateDate->CurrentValue;
		$this->CreateDate->ViewValue = ew_FormatDateTime($this->CreateDate->ViewValue, 0);
		$this->CreateDate->ViewCustomAttributes = "";

		// Editor
		$this->Editor->ViewValue = $this->Editor->CurrentValue;
		$this->Editor->ViewValue = ew_FormatDateTime($this->Editor->ViewValue, 0);
		$this->Editor->ViewCustomAttributes = "";

		// EditDate
		$this->EditDate->ViewValue = $this->EditDate->CurrentValue;
		$this->EditDate->ViewCustomAttributes = "";

		// NA
		if (ew_ConvertToBool($this->NA->CurrentValue)) {
			$this->NA->ViewValue = $this->NA->FldTagCaption(1) <> "" ? $this->NA->FldTagCaption(1) : "Y";
		} else {
			$this->NA->ViewValue = $this->NA->FldTagCaption(2) <> "" ? $this->NA->FldTagCaption(2) : "N";
		}
		$this->NA->ViewCustomAttributes = "";

			// KampusID
			$this->KampusID->LinkCustomAttributes = "";
			$this->KampusID->HrefValue = "";
			$this->KampusID->TooltipValue = "";

			// ProdiID
			$this->ProdiID->LinkCustomAttributes = "";
			$this->ProdiID->HrefValue = "";
			$this->ProdiID->TooltipValue = "";

			// KurikulumID
			$this->KurikulumID->LinkCustomAttributes = "";
			$this->KurikulumID->HrefValue = "";
			$this->KurikulumID->TooltipValue = "";

			// MKKode
			$this->MKKode->LinkCustomAttributes = "";
			$this->MKKode->HrefValue = "";
			$this->MKKode->TooltipValue = "";

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// Singkatan
			$this->Singkatan->LinkCustomAttributes = "";
			$this->Singkatan->HrefValue = "";
			$this->Singkatan->TooltipValue = "";

			// Tingkat
			$this->Tingkat->LinkCustomAttributes = "";
			$this->Tingkat->HrefValue = "";
			$this->Tingkat->TooltipValue = "";

			// Sesi
			$this->Sesi->LinkCustomAttributes = "";
			$this->Sesi->HrefValue = "";
			$this->Sesi->TooltipValue = "";

			// Wajib
			$this->Wajib->LinkCustomAttributes = "";
			$this->Wajib->HrefValue = "";
			$this->Wajib->TooltipValue = "";

			// Deskripsi
			$this->Deskripsi->LinkCustomAttributes = "";
			$this->Deskripsi->HrefValue = "";
			$this->Deskripsi->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// KampusID
			$this->KampusID->EditAttrs["class"] = "form-control";
			$this->KampusID->EditCustomAttributes = "";
			if (trim(strval($this->KampusID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KampusID`" . ew_SearchString("=", $this->KampusID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KampusID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_kampus`";
			$sWhereWrk = "";
			$this->KampusID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KampusID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KampusID->EditValue = $arwrk;

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
			$sSqlWrk .= " ORDER BY `ProdiID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->ProdiID->EditValue = $arwrk;

			// KurikulumID
			$this->KurikulumID->EditAttrs["class"] = "form-control";
			$this->KurikulumID->EditCustomAttributes = "";
			if (trim(strval($this->KurikulumID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KurikulumID`" . ew_SearchString("=", $this->KurikulumID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `KurikulumID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kurikulum`";
			$sWhereWrk = "";
			$this->KurikulumID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->KurikulumID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->KurikulumID->EditValue = $arwrk;

			// MKKode
			$this->MKKode->EditAttrs["class"] = "form-control";
			$this->MKKode->EditCustomAttributes = "";
			$this->MKKode->EditValue = ew_HtmlEncode($this->MKKode->AdvancedSearch->SearchValue);
			$this->MKKode->PlaceHolder = ew_RemoveHtml($this->MKKode->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->AdvancedSearch->SearchValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// Singkatan
			$this->Singkatan->EditAttrs["class"] = "form-control";
			$this->Singkatan->EditCustomAttributes = "";
			$this->Singkatan->EditValue = ew_HtmlEncode($this->Singkatan->AdvancedSearch->SearchValue);
			$this->Singkatan->PlaceHolder = ew_RemoveHtml($this->Singkatan->FldCaption());

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

			// Wajib
			$this->Wajib->EditCustomAttributes = "";
			$this->Wajib->EditValue = $this->Wajib->Options(FALSE);

			// Deskripsi
			$this->Deskripsi->EditAttrs["class"] = "form-control";
			$this->Deskripsi->EditCustomAttributes = "";
			$this->Deskripsi->EditValue = ew_HtmlEncode($this->Deskripsi->AdvancedSearch->SearchValue);
			$this->Deskripsi->PlaceHolder = ew_RemoveHtml($this->Deskripsi->FldCaption());

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
		$this->KampusID->AdvancedSearch->Load();
		$this->ProdiID->AdvancedSearch->Load();
		$this->KurikulumID->AdvancedSearch->Load();
		$this->MKKode->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->Singkatan->AdvancedSearch->Load();
		$this->Tingkat->AdvancedSearch->Load();
		$this->Sesi->AdvancedSearch->Load();
		$this->Wajib->AdvancedSearch->Load();
		$this->Deskripsi->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_mklist.php"), "", $this->TableVar, TRUE);
		$PageId = "search";
		$Breadcrumb->Add("search", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_KampusID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KampusID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_kampus`";
			$sWhereWrk = "";
			$this->KampusID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KampusID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KampusID, $sWhereWrk); // Call Lookup selecting
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
			$sSqlWrk .= " ORDER BY `ProdiID` ASC";
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_KurikulumID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KurikulumID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kurikulum`";
			$sWhereWrk = "{filter}";
			$this->KurikulumID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KurikulumID` = {filter_value}', "t0" => "3", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->KurikulumID, $sWhereWrk); // Call Lookup selecting
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
if (!isset($master_mk_search)) $master_mk_search = new cmaster_mk_search();

// Page init
$master_mk_search->Page_Init();

// Page main
$master_mk_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_mk_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($master_mk_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fmaster_mksearch = new ew_Form("fmaster_mksearch", "search");
<?php } else { ?>
var CurrentForm = fmaster_mksearch = new ew_Form("fmaster_mksearch", "search");
<?php } ?>

// Form_CustomValidate event
fmaster_mksearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_mksearch.ValidateRequired = true;
<?php } else { ?>
fmaster_mksearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_mksearch.Lists["x_KampusID"] = {"LinkField":"x_KampusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_kampus"};
fmaster_mksearch.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":["x_KurikulumID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fmaster_mksearch.Lists["x_KurikulumID"] = {"LinkField":"x_KurikulumID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":["x_ProdiID"],"ChildFields":[],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"kurikulum"};
fmaster_mksearch.Lists["x_Tingkat"] = {"LinkField":"x_Tingkat","Ajax":true,"AutoFill":false,"DisplayFields":["x_Tingkat","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"kelas"};
fmaster_mksearch.Lists["x_Sesi"] = {"LinkField":"x_Sesi","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaSesi","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_sesi"};
fmaster_mksearch.Lists["x_Wajib"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_mksearch.Lists["x_Wajib"].Options = <?php echo json_encode($master_mk->Wajib->Options()) ?>;
fmaster_mksearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_mksearch.Lists["x_NA"].Options = <?php echo json_encode($master_mk->NA->Options()) ?>;

// Form object for search
// Validate function for search

fmaster_mksearch.Validate = function(fobj) {
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
<?php if (!$master_mk_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_mk_search->ShowPageHeader(); ?>
<?php
$master_mk_search->ShowMessage();
?>
<form name="fmaster_mksearch" id="fmaster_mksearch" class="<?php echo $master_mk_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_mk_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_mk_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_mk">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($master_mk_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_mk_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_mksearch" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_mk->KampusID->Visible) { // KampusID ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
	<div id="r_KampusID" class="form-group">
		<label for="x_KampusID" class="<?php echo $master_mk_search->SearchLabelClass ?>"><span id="elh_master_mk_KampusID"><?php echo $master_mk->KampusID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KampusID" id="z_KampusID" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_mk_search->SearchRightColumnClass ?>"><div<?php echo $master_mk->KampusID->CellAttributes() ?>>
			<span id="el_master_mk_KampusID">
<select data-table="master_mk" data-field="x_KampusID" data-value-separator="<?php echo $master_mk->KampusID->DisplayValueSeparatorAttribute() ?>" id="x_KampusID" name="x_KampusID"<?php echo $master_mk->KampusID->EditAttributes() ?>>
<?php echo $master_mk->KampusID->SelectOptionListHtml("x_KampusID") ?>
</select>
<input type="hidden" name="s_x_KampusID" id="s_x_KampusID" value="<?php echo $master_mk->KampusID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KampusID">
		<td><span id="elh_master_mk_KampusID"><?php echo $master_mk->KampusID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_KampusID" id="z_KampusID" value="LIKE"></span></td>
		<td<?php echo $master_mk->KampusID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_mk_KampusID">
<select data-table="master_mk" data-field="x_KampusID" data-value-separator="<?php echo $master_mk->KampusID->DisplayValueSeparatorAttribute() ?>" id="x_KampusID" name="x_KampusID"<?php echo $master_mk->KampusID->EditAttributes() ?>>
<?php echo $master_mk->KampusID->SelectOptionListHtml("x_KampusID") ?>
</select>
<input type="hidden" name="s_x_KampusID" id="s_x_KampusID" value="<?php echo $master_mk->KampusID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label for="x_ProdiID" class="<?php echo $master_mk_search->SearchLabelClass ?>"><span id="elh_master_mk_ProdiID"><?php echo $master_mk->ProdiID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_mk_search->SearchRightColumnClass ?>"><div<?php echo $master_mk->ProdiID->CellAttributes() ?>>
			<span id="el_master_mk_ProdiID">
<?php $master_mk->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$master_mk->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="master_mk" data-field="x_ProdiID" data-value-separator="<?php echo $master_mk->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $master_mk->ProdiID->EditAttributes() ?>>
<?php echo $master_mk->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $master_mk->ProdiID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_master_mk_ProdiID"><?php echo $master_mk->ProdiID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></span></td>
		<td<?php echo $master_mk->ProdiID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_mk_ProdiID">
<?php $master_mk->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$master_mk->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="master_mk" data-field="x_ProdiID" data-value-separator="<?php echo $master_mk->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $master_mk->ProdiID->EditAttributes() ?>>
<?php echo $master_mk->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $master_mk->ProdiID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->KurikulumID->Visible) { // KurikulumID ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
	<div id="r_KurikulumID" class="form-group">
		<label for="x_KurikulumID" class="<?php echo $master_mk_search->SearchLabelClass ?>"><span id="elh_master_mk_KurikulumID"><?php echo $master_mk->KurikulumID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KurikulumID" id="z_KurikulumID" value="="></p>
		</label>
		<div class="<?php echo $master_mk_search->SearchRightColumnClass ?>"><div<?php echo $master_mk->KurikulumID->CellAttributes() ?>>
			<span id="el_master_mk_KurikulumID">
<select data-table="master_mk" data-field="x_KurikulumID" data-value-separator="<?php echo $master_mk->KurikulumID->DisplayValueSeparatorAttribute() ?>" id="x_KurikulumID" name="x_KurikulumID"<?php echo $master_mk->KurikulumID->EditAttributes() ?>>
<?php echo $master_mk->KurikulumID->SelectOptionListHtml("x_KurikulumID") ?>
</select>
<input type="hidden" name="s_x_KurikulumID" id="s_x_KurikulumID" value="<?php echo $master_mk->KurikulumID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_KurikulumID">
		<td><span id="elh_master_mk_KurikulumID"><?php echo $master_mk->KurikulumID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KurikulumID" id="z_KurikulumID" value="="></span></td>
		<td<?php echo $master_mk->KurikulumID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_mk_KurikulumID">
<select data-table="master_mk" data-field="x_KurikulumID" data-value-separator="<?php echo $master_mk->KurikulumID->DisplayValueSeparatorAttribute() ?>" id="x_KurikulumID" name="x_KurikulumID"<?php echo $master_mk->KurikulumID->EditAttributes() ?>>
<?php echo $master_mk->KurikulumID->SelectOptionListHtml("x_KurikulumID") ?>
</select>
<input type="hidden" name="s_x_KurikulumID" id="s_x_KurikulumID" value="<?php echo $master_mk->KurikulumID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->MKKode->Visible) { // MKKode ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
	<div id="r_MKKode" class="form-group">
		<label for="x_MKKode" class="<?php echo $master_mk_search->SearchLabelClass ?>"><span id="elh_master_mk_MKKode"><?php echo $master_mk->MKKode->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_MKKode" id="z_MKKode" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_mk_search->SearchRightColumnClass ?>"><div<?php echo $master_mk->MKKode->CellAttributes() ?>>
			<span id="el_master_mk_MKKode">
<input type="text" data-table="master_mk" data-field="x_MKKode" name="x_MKKode" id="x_MKKode" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->MKKode->getPlaceHolder()) ?>" value="<?php echo $master_mk->MKKode->EditValue ?>"<?php echo $master_mk->MKKode->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_MKKode">
		<td><span id="elh_master_mk_MKKode"><?php echo $master_mk->MKKode->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_MKKode" id="z_MKKode" value="LIKE"></span></td>
		<td<?php echo $master_mk->MKKode->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_mk_MKKode">
<input type="text" data-table="master_mk" data-field="x_MKKode" name="x_MKKode" id="x_MKKode" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->MKKode->getPlaceHolder()) ?>" value="<?php echo $master_mk->MKKode->EditValue ?>"<?php echo $master_mk->MKKode->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label for="x_Nama" class="<?php echo $master_mk_search->SearchLabelClass ?>"><span id="elh_master_mk_Nama"><?php echo $master_mk->Nama->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_mk_search->SearchRightColumnClass ?>"><div<?php echo $master_mk->Nama->CellAttributes() ?>>
			<span id="el_master_mk_Nama">
<input type="text" data-table="master_mk" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_mk->Nama->getPlaceHolder()) ?>" value="<?php echo $master_mk->Nama->EditValue ?>"<?php echo $master_mk->Nama->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_master_mk_Nama"><?php echo $master_mk->Nama->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></span></td>
		<td<?php echo $master_mk->Nama->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_mk_Nama">
<input type="text" data-table="master_mk" data-field="x_Nama" name="x_Nama" id="x_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_mk->Nama->getPlaceHolder()) ?>" value="<?php echo $master_mk->Nama->EditValue ?>"<?php echo $master_mk->Nama->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Singkatan->Visible) { // Singkatan ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
	<div id="r_Singkatan" class="form-group">
		<label for="x_Singkatan" class="<?php echo $master_mk_search->SearchLabelClass ?>"><span id="elh_master_mk_Singkatan"><?php echo $master_mk->Singkatan->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Singkatan" id="z_Singkatan" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_mk_search->SearchRightColumnClass ?>"><div<?php echo $master_mk->Singkatan->CellAttributes() ?>>
			<span id="el_master_mk_Singkatan">
<input type="text" data-table="master_mk" data-field="x_Singkatan" name="x_Singkatan" id="x_Singkatan" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->Singkatan->getPlaceHolder()) ?>" value="<?php echo $master_mk->Singkatan->EditValue ?>"<?php echo $master_mk->Singkatan->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Singkatan">
		<td><span id="elh_master_mk_Singkatan"><?php echo $master_mk->Singkatan->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Singkatan" id="z_Singkatan" value="LIKE"></span></td>
		<td<?php echo $master_mk->Singkatan->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_mk_Singkatan">
<input type="text" data-table="master_mk" data-field="x_Singkatan" name="x_Singkatan" id="x_Singkatan" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->Singkatan->getPlaceHolder()) ?>" value="<?php echo $master_mk->Singkatan->EditValue ?>"<?php echo $master_mk->Singkatan->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Tingkat->Visible) { // Tingkat ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
	<div id="r_Tingkat" class="form-group">
		<label for="x_Tingkat" class="<?php echo $master_mk_search->SearchLabelClass ?>"><span id="elh_master_mk_Tingkat"><?php echo $master_mk->Tingkat->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Tingkat" id="z_Tingkat" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_mk_search->SearchRightColumnClass ?>"><div<?php echo $master_mk->Tingkat->CellAttributes() ?>>
			<span id="el_master_mk_Tingkat">
<select data-table="master_mk" data-field="x_Tingkat" data-value-separator="<?php echo $master_mk->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $master_mk->Tingkat->EditAttributes() ?>>
<?php echo $master_mk->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $master_mk->Tingkat->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tingkat">
		<td><span id="elh_master_mk_Tingkat"><?php echo $master_mk->Tingkat->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Tingkat" id="z_Tingkat" value="LIKE"></span></td>
		<td<?php echo $master_mk->Tingkat->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_mk_Tingkat">
<select data-table="master_mk" data-field="x_Tingkat" data-value-separator="<?php echo $master_mk->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $master_mk->Tingkat->EditAttributes() ?>>
<?php echo $master_mk->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $master_mk->Tingkat->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Sesi->Visible) { // Sesi ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
	<div id="r_Sesi" class="form-group">
		<label for="x_Sesi" class="<?php echo $master_mk_search->SearchLabelClass ?>"><span id="elh_master_mk_Sesi"><?php echo $master_mk->Sesi->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Sesi" id="z_Sesi" value="="></p>
		</label>
		<div class="<?php echo $master_mk_search->SearchRightColumnClass ?>"><div<?php echo $master_mk->Sesi->CellAttributes() ?>>
			<span id="el_master_mk_Sesi">
<select data-table="master_mk" data-field="x_Sesi" data-value-separator="<?php echo $master_mk->Sesi->DisplayValueSeparatorAttribute() ?>" id="x_Sesi" name="x_Sesi"<?php echo $master_mk->Sesi->EditAttributes() ?>>
<?php echo $master_mk->Sesi->SelectOptionListHtml("x_Sesi") ?>
</select>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $master_mk->Sesi->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Sesi">
		<td><span id="elh_master_mk_Sesi"><?php echo $master_mk->Sesi->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Sesi" id="z_Sesi" value="="></span></td>
		<td<?php echo $master_mk->Sesi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_mk_Sesi">
<select data-table="master_mk" data-field="x_Sesi" data-value-separator="<?php echo $master_mk->Sesi->DisplayValueSeparatorAttribute() ?>" id="x_Sesi" name="x_Sesi"<?php echo $master_mk->Sesi->EditAttributes() ?>>
<?php echo $master_mk->Sesi->SelectOptionListHtml("x_Sesi") ?>
</select>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $master_mk->Sesi->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Wajib->Visible) { // Wajib ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
	<div id="r_Wajib" class="form-group">
		<label class="<?php echo $master_mk_search->SearchLabelClass ?>"><span id="elh_master_mk_Wajib"><?php echo $master_mk->Wajib->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Wajib" id="z_Wajib" value="="></p>
		</label>
		<div class="<?php echo $master_mk_search->SearchRightColumnClass ?>"><div<?php echo $master_mk->Wajib->CellAttributes() ?>>
			<span id="el_master_mk_Wajib">
<div id="tp_x_Wajib" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_Wajib" data-value-separator="<?php echo $master_mk->Wajib->DisplayValueSeparatorAttribute() ?>" name="x_Wajib" id="x_Wajib" value="{value}"<?php echo $master_mk->Wajib->EditAttributes() ?>></div>
<div id="dsl_x_Wajib" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->Wajib->RadioButtonListHtml(FALSE, "x_Wajib") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Wajib">
		<td><span id="elh_master_mk_Wajib"><?php echo $master_mk->Wajib->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Wajib" id="z_Wajib" value="="></span></td>
		<td<?php echo $master_mk->Wajib->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_mk_Wajib">
<div id="tp_x_Wajib" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_Wajib" data-value-separator="<?php echo $master_mk->Wajib->DisplayValueSeparatorAttribute() ?>" name="x_Wajib" id="x_Wajib" value="{value}"<?php echo $master_mk->Wajib->EditAttributes() ?>></div>
<div id="dsl_x_Wajib" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->Wajib->RadioButtonListHtml(FALSE, "x_Wajib") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->Deskripsi->Visible) { // Deskripsi ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
	<div id="r_Deskripsi" class="form-group">
		<label for="x_Deskripsi" class="<?php echo $master_mk_search->SearchLabelClass ?>"><span id="elh_master_mk_Deskripsi"><?php echo $master_mk->Deskripsi->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Deskripsi" id="z_Deskripsi" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_mk_search->SearchRightColumnClass ?>"><div<?php echo $master_mk->Deskripsi->CellAttributes() ?>>
			<span id="el_master_mk_Deskripsi">
<input type="text" data-table="master_mk" data-field="x_Deskripsi" name="x_Deskripsi" id="x_Deskripsi" size="35" placeholder="<?php echo ew_HtmlEncode($master_mk->Deskripsi->getPlaceHolder()) ?>" value="<?php echo $master_mk->Deskripsi->EditValue ?>"<?php echo $master_mk->Deskripsi->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Deskripsi">
		<td><span id="elh_master_mk_Deskripsi"><?php echo $master_mk->Deskripsi->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Deskripsi" id="z_Deskripsi" value="LIKE"></span></td>
		<td<?php echo $master_mk->Deskripsi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_mk_Deskripsi">
<input type="text" data-table="master_mk" data-field="x_Deskripsi" name="x_Deskripsi" id="x_Deskripsi" size="35" placeholder="<?php echo ew_HtmlEncode($master_mk->Deskripsi->getPlaceHolder()) ?>" value="<?php echo $master_mk->Deskripsi->EditValue ?>"<?php echo $master_mk->Deskripsi->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_mk->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $master_mk_search->SearchLabelClass ?>"><span id="elh_master_mk_NA"><?php echo $master_mk->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $master_mk_search->SearchRightColumnClass ?>"><div<?php echo $master_mk->NA->CellAttributes() ?>>
			<span id="el_master_mk_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_NA" data-value-separator="<?php echo $master_mk->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_mk->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_master_mk_NA"><?php echo $master_mk->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $master_mk->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_mk_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_NA" data-value-separator="<?php echo $master_mk->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_mk->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_mk_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$master_mk_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$master_mk_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_mksearch.Init();
</script>
<?php
$master_mk_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_mk_search->Page_Terminate();
?>
