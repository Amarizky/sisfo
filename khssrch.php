<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "khsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$khs_search = NULL; // Initialize page object first

class ckhs_search extends ckhs {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'khs';

	// Page object name
	var $PageObjName = 'khs_search';

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

		// Table object (khs)
		if (!isset($GLOBALS["khs"]) || get_class($GLOBALS["khs"]) == "ckhs") {
			$GLOBALS["khs"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["khs"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'khs', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("khslist.php"));
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
		$this->Kelas->SetVisibility();
		$this->StudentID->SetVisibility();
		$this->StatusStudentID->SetVisibility();
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
		global $EW_EXPORT, $khs;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($khs);
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
						$sSrchStr = "khslist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->Kelas); // Kelas
		$this->BuildSearchUrl($sSrchUrl, $this->StudentID); // StudentID
		$this->BuildSearchUrl($sSrchUrl, $this->StatusStudentID); // StatusStudentID
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
		// ProdiID

		$this->ProdiID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ProdiID"));
		$this->ProdiID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ProdiID");

		// TahunID
		$this->TahunID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TahunID"));
		$this->TahunID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TahunID");

		// Sesi
		$this->Sesi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Sesi"));
		$this->Sesi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Sesi");
		if (is_array($this->Sesi->AdvancedSearch->SearchValue)) $this->Sesi->AdvancedSearch->SearchValue = implode(",", $this->Sesi->AdvancedSearch->SearchValue);
		if (is_array($this->Sesi->AdvancedSearch->SearchValue2)) $this->Sesi->AdvancedSearch->SearchValue2 = implode(",", $this->Sesi->AdvancedSearch->SearchValue2);

		// Tingkat
		$this->Tingkat->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tingkat"));
		$this->Tingkat->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tingkat");

		// Kelas
		$this->Kelas->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Kelas"));
		$this->Kelas->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Kelas");

		// StudentID
		$this->StudentID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StudentID"));
		$this->StudentID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StudentID");

		// StatusStudentID
		$this->StatusStudentID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_StatusStudentID"));
		$this->StatusStudentID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_StatusStudentID");

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
		// KHSID
		// ProdiID
		// TahunID
		// Sesi
		// Tingkat
		// Kelas
		// StudentID
		// StatusStudentID
		// Keterangan
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// KHSID
		$this->KHSID->ViewValue = $this->KHSID->CurrentValue;
		$this->KHSID->ViewCustomAttributes = "";

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

		// TahunID
		if (strval($this->TahunID->CurrentValue) <> "") {
			$sFilterWrk = "`TahunID`" . ew_SearchString("=", $this->TahunID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `TahunID`, `TahunID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tahun`";
		$sWhereWrk = "";
		$this->TahunID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->TahunID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `TahunID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->TahunID->ViewValue = $this->TahunID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->TahunID->ViewValue = $this->TahunID->CurrentValue;
			}
		} else {
			$this->TahunID->ViewValue = NULL;
		}
		$this->TahunID->ViewCustomAttributes = "";

		// Sesi
		if (strval($this->Sesi->CurrentValue) <> "") {
			$arwrk = explode(",", $this->Sesi->CurrentValue);
			$sFilterWrk = "";
			foreach ($arwrk as $wrk) {
				if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
				$sFilterWrk .= "`Sesi`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_NUMBER, "");
			}
		$sSqlWrk = "SELECT `Sesi`, `NamaSesi` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_sesi`";
		$sWhereWrk = "";
		$this->Sesi->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Sesi, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->Sesi->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->Sesi->ViewValue .= $this->Sesi->DisplayValue($arwrk);
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->Sesi->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
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
		$this->Tingkat->ViewCustomAttributes = "";

		// Kelas
		if (strval($this->Kelas->CurrentValue) <> "") {
			$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->Kelas->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
		$sWhereWrk = "";
		$this->Kelas->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Kelas, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Kelas->ViewValue = $this->Kelas->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Kelas->ViewValue = $this->Kelas->CurrentValue;
			}
		} else {
			$this->Kelas->ViewValue = NULL;
		}
		$this->Kelas->CssStyle = "font-weight: bold;";
		$this->Kelas->ViewCustomAttributes = "";

		// StudentID
		if (strval($this->StudentID->CurrentValue) <> "") {
			$sFilterWrk = "`StudentID`" . ew_SearchString("=", $this->StudentID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StudentID`, `StudentID` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `student`";
		$sWhereWrk = "";
		$this->StudentID->LookupFilters = array("dx1" => '`StudentID`', "dx2" => '`Nama`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StudentID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->StudentID->ViewValue = $this->StudentID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StudentID->ViewValue = $this->StudentID->CurrentValue;
			}
		} else {
			$this->StudentID->ViewValue = NULL;
		}
		$this->StudentID->CssStyle = "font-weight: bold;";
		$this->StudentID->ViewCustomAttributes = "";

		// StatusStudentID
		if (strval($this->StatusStudentID->CurrentValue) <> "") {
			$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StatusStudentID->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statusstudent`";
		$sWhereWrk = "";
		$this->StatusStudentID->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->StatusStudentID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->StatusStudentID->ViewValue = $this->StatusStudentID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->StatusStudentID->ViewValue = $this->StatusStudentID->CurrentValue;
			}
		} else {
			$this->StatusStudentID->ViewValue = NULL;
		}
		$this->StatusStudentID->ViewCustomAttributes = "";

		// Keterangan
		$this->Keterangan->ViewValue = $this->Keterangan->CurrentValue;
		$this->Keterangan->ViewCustomAttributes = "";

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

			// Kelas
			$this->Kelas->LinkCustomAttributes = "";
			$this->Kelas->HrefValue = "";
			$this->Kelas->TooltipValue = "";

			// StudentID
			$this->StudentID->LinkCustomAttributes = "";
			$this->StudentID->HrefValue = "";
			$this->StudentID->TooltipValue = "";

			// StatusStudentID
			$this->StatusStudentID->LinkCustomAttributes = "";
			$this->StatusStudentID->HrefValue = "";
			$this->StatusStudentID->TooltipValue = "";

			// Keterangan
			$this->Keterangan->LinkCustomAttributes = "";
			$this->Keterangan->HrefValue = "";
			$this->Keterangan->TooltipValue = "";

			// NA
			$this->NA->LinkCustomAttributes = "";
			$this->NA->HrefValue = "";
			$this->NA->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

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

			// TahunID
			$this->TahunID->EditCustomAttributes = "";
			if (trim(strval($this->TahunID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`TahunID`" . ew_SearchString("=", $this->TahunID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `TahunID`, `TahunID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `tahun`";
			$sWhereWrk = "";
			$this->TahunID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->TahunID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `TahunID` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$this->TahunID->AdvancedSearch->ViewValue = $this->TahunID->DisplayValue($arwrk);
			} else {
				$this->TahunID->AdvancedSearch->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->TahunID->EditValue = $arwrk;

			// Sesi
			$this->Sesi->EditCustomAttributes = "";
			if (trim(strval($this->Sesi->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$arwrk = explode(",", $this->Sesi->AdvancedSearch->SearchValue);
				$sFilterWrk = "";
				foreach ($arwrk as $wrk) {
					if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
					$sFilterWrk .= "`Sesi`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_NUMBER, "");
				}
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
			$sSqlWrk = "SELECT DISTINCT `Tingkat`, `Tingkat` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Tingkat->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Tingkat->EditValue = $arwrk;

			// Kelas
			$this->Kelas->EditAttrs["class"] = "form-control";
			$this->Kelas->EditCustomAttributes = "";
			if (trim(strval($this->Kelas->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`KelasID`" . ew_SearchString("=", $this->Kelas->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `KelasID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, `Tingkat` AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kelas`";
			$sWhereWrk = "";
			$this->Kelas->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Kelas, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Kelas->EditValue = $arwrk;

			// StudentID
			$this->StudentID->EditCustomAttributes = "";
			if (trim(strval($this->StudentID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StudentID`" . ew_SearchString("=", $this->StudentID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StudentID`, `StudentID` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `ProdiID` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `student`";
			$sWhereWrk = "";
			$this->StudentID->LookupFilters = array("dx1" => '`StudentID`', "dx2" => '`Nama`');
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StudentID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
				$this->StudentID->AdvancedSearch->ViewValue = $this->StudentID->DisplayValue($arwrk);
			} else {
				$this->StudentID->AdvancedSearch->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StudentID->EditValue = $arwrk;

			// StatusStudentID
			$this->StatusStudentID->EditAttrs["class"] = "form-control";
			$this->StatusStudentID->EditCustomAttributes = "";
			if (trim(strval($this->StatusStudentID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`StatusStudentID`" . ew_SearchString("=", $this->StatusStudentID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `StatusStudentID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `master_statusstudent`";
			$sWhereWrk = "";
			$this->StatusStudentID->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->StatusStudentID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->StatusStudentID->EditValue = $arwrk;

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
		$this->Kelas->AdvancedSearch->Load();
		$this->StudentID->AdvancedSearch->Load();
		$this->StatusStudentID->AdvancedSearch->Load();
		$this->Keterangan->AdvancedSearch->Load();
		$this->NA->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("khslist.php"), "", $this->TableVar, TRUE);
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
		case "x_TahunID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `TahunID` AS `LinkFld`, `TahunID` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tahun`";
			$sWhereWrk = "{filter}";
			$this->TahunID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`TahunID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->TahunID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `TahunID` ASC";
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
			$sWhereWrk = "{filter}";
			$this->Tingkat->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`Tingkat` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Tingkat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_Kelas":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `KelasID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kelas`";
			$sWhereWrk = "{filter}";
			$this->Kelas->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`KelasID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "", "f2" => '`Tingkat` IN ({filter_value})', "t2" => "200", "fn2" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Kelas, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StudentID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StudentID` AS `LinkFld`, `StudentID` AS `DispFld`, `Nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `student`";
			$sWhereWrk = "{filter}";
			$this->StudentID->LookupFilters = array("dx1" => '`StudentID`', "dx2" => '`Nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StudentID` = {filter_value}', "t0" => "200", "fn0" => "", "f1" => '`ProdiID` IN ({filter_value})', "t1" => "200", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StudentID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_StatusStudentID":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `StatusStudentID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `master_statusstudent`";
			$sWhereWrk = "";
			$this->StatusStudentID->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`StatusStudentID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->StatusStudentID, $sWhereWrk); // Call Lookup selecting
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
if (!isset($khs_search)) $khs_search = new ckhs_search();

// Page init
$khs_search->Page_Init();

// Page main
$khs_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$khs_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($khs_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fkhssearch = new ew_Form("fkhssearch", "search");
<?php } else { ?>
var CurrentForm = fkhssearch = new ew_Form("fkhssearch", "search");
<?php } ?>

// Form_CustomValidate event
fkhssearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fkhssearch.ValidateRequired = true;
<?php } else { ?>
fkhssearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fkhssearch.Lists["x_ProdiID"] = {"LinkField":"x_ProdiID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":["x_TahunID","x_Tingkat","x_Kelas","x_StudentID"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_prodi"};
fkhssearch.Lists["x_TahunID"] = {"LinkField":"x_TahunID","Ajax":true,"AutoFill":false,"DisplayFields":["x_TahunID","","",""],"ParentFields":["x_ProdiID"],"ChildFields":[],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"tahun"};
fkhssearch.Lists["x_Sesi[]"] = {"LinkField":"x_Sesi","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaSesi","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_sesi"};
fkhssearch.Lists["x_Tingkat"] = {"LinkField":"x_Tingkat","Ajax":true,"AutoFill":false,"DisplayFields":["x_Tingkat","","",""],"ParentFields":["x_ProdiID"],"ChildFields":["x_Kelas"],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"kelas"};
fkhssearch.Lists["x_Kelas"] = {"LinkField":"x_KelasID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":["x_ProdiID","x_Tingkat"],"ChildFields":[],"FilterFields":["x_ProdiID","x_Tingkat"],"Options":[],"Template":"","LinkTable":"kelas"};
fkhssearch.Lists["x_StudentID"] = {"LinkField":"x_StudentID","Ajax":true,"AutoFill":false,"DisplayFields":["x_StudentID","x_Nama","",""],"ParentFields":["x_ProdiID"],"ChildFields":[],"FilterFields":["x_ProdiID"],"Options":[],"Template":"","LinkTable":"student"};
fkhssearch.Lists["x_StatusStudentID"] = {"LinkField":"x_StatusStudentID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_statusstudent"};
fkhssearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fkhssearch.Lists["x_NA"].Options = <?php echo json_encode($khs->NA->Options()) ?>;

// Form object for search
// Validate function for search

fkhssearch.Validate = function(fobj) {
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
<?php if (!$khs_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $khs_search->ShowPageHeader(); ?>
<?php
$khs_search->ShowMessage();
?>
<form name="fkhssearch" id="fkhssearch" class="<?php echo $khs_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($khs_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $khs_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="khs">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($khs_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$khs_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $khs_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_khssearch" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($khs->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $khs_search->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label for="x_ProdiID" class="<?php echo $khs_search->SearchLabelClass ?>"><span id="elh_khs_ProdiID"><?php echo $khs->ProdiID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></p>
		</label>
		<div class="<?php echo $khs_search->SearchRightColumnClass ?>"><div<?php echo $khs->ProdiID->CellAttributes() ?>>
			<span id="el_khs_ProdiID">
<?php $khs->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_ProdiID" data-value-separator="<?php echo $khs->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $khs->ProdiID->EditAttributes() ?>>
<?php echo $khs->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $khs->ProdiID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_khs_ProdiID"><?php echo $khs->ProdiID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></span></td>
		<td<?php echo $khs->ProdiID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_khs_ProdiID">
<?php $khs->ProdiID->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->ProdiID->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_ProdiID" data-value-separator="<?php echo $khs->ProdiID->DisplayValueSeparatorAttribute() ?>" id="x_ProdiID" name="x_ProdiID"<?php echo $khs->ProdiID->EditAttributes() ?>>
<?php echo $khs->ProdiID->SelectOptionListHtml("x_ProdiID") ?>
</select>
<input type="hidden" name="s_x_ProdiID" id="s_x_ProdiID" value="<?php echo $khs->ProdiID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->TahunID->Visible) { // TahunID ?>
<?php if (ew_IsMobile() || $khs_search->IsModal) { ?>
	<div id="r_TahunID" class="form-group">
		<label for="x_TahunID" class="<?php echo $khs_search->SearchLabelClass ?>"><span id="elh_khs_TahunID"><?php echo $khs->TahunID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TahunID" id="z_TahunID" value="LIKE"></p>
		</label>
		<div class="<?php echo $khs_search->SearchRightColumnClass ?>"><div<?php echo $khs->TahunID->CellAttributes() ?>>
			<span id="el_khs_TahunID">
<div class="ewDropdownList has-feedback">
	<span onclick="" class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $khs->TahunID->AdvancedSearch->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x_TahunID" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php echo $khs->TahunID->RadioButtonListHtml(TRUE, "x_TahunID") ?>
		</div>
	</div>
	<div id="tp_x_TahunID" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_TahunID" data-value-separator="<?php echo $khs->TahunID->DisplayValueSeparatorAttribute() ?>" name="x_TahunID" id="x_TahunID" value="{value}"<?php echo $khs->TahunID->EditAttributes() ?>></div>
</div>
<input type="hidden" name="s_x_TahunID" id="s_x_TahunID" value="<?php echo $khs->TahunID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TahunID">
		<td><span id="elh_khs_TahunID"><?php echo $khs->TahunID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_TahunID" id="z_TahunID" value="LIKE"></span></td>
		<td<?php echo $khs->TahunID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_khs_TahunID">
<div class="ewDropdownList has-feedback">
	<span onclick="" class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $khs->TahunID->AdvancedSearch->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x_TahunID" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php echo $khs->TahunID->RadioButtonListHtml(TRUE, "x_TahunID") ?>
		</div>
	</div>
	<div id="tp_x_TahunID" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_TahunID" data-value-separator="<?php echo $khs->TahunID->DisplayValueSeparatorAttribute() ?>" name="x_TahunID" id="x_TahunID" value="{value}"<?php echo $khs->TahunID->EditAttributes() ?>></div>
</div>
<input type="hidden" name="s_x_TahunID" id="s_x_TahunID" value="<?php echo $khs->TahunID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->Sesi->Visible) { // Sesi ?>
<?php if (ew_IsMobile() || $khs_search->IsModal) { ?>
	<div id="r_Sesi" class="form-group">
		<label class="<?php echo $khs_search->SearchLabelClass ?>"><span id="elh_khs_Sesi"><?php echo $khs->Sesi->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Sesi" id="z_Sesi" value="="></p>
		</label>
		<div class="<?php echo $khs_search->SearchRightColumnClass ?>"><div<?php echo $khs->Sesi->CellAttributes() ?>>
			<span id="el_khs_Sesi">
<div id="tp_x_Sesi" class="ewTemplate"><input type="checkbox" data-table="khs" data-field="x_Sesi" data-value-separator="<?php echo $khs->Sesi->DisplayValueSeparatorAttribute() ?>" name="x_Sesi[]" id="x_Sesi[]" value="{value}"<?php echo $khs->Sesi->EditAttributes() ?>></div>
<div id="dsl_x_Sesi" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->Sesi->CheckBoxListHtml(FALSE, "x_Sesi[]") ?>
</div></div>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $khs->Sesi->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Sesi">
		<td><span id="elh_khs_Sesi"><?php echo $khs->Sesi->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Sesi" id="z_Sesi" value="="></span></td>
		<td<?php echo $khs->Sesi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_khs_Sesi">
<div id="tp_x_Sesi" class="ewTemplate"><input type="checkbox" data-table="khs" data-field="x_Sesi" data-value-separator="<?php echo $khs->Sesi->DisplayValueSeparatorAttribute() ?>" name="x_Sesi[]" id="x_Sesi[]" value="{value}"<?php echo $khs->Sesi->EditAttributes() ?>></div>
<div id="dsl_x_Sesi" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->Sesi->CheckBoxListHtml(FALSE, "x_Sesi[]") ?>
</div></div>
<input type="hidden" name="s_x_Sesi" id="s_x_Sesi" value="<?php echo $khs->Sesi->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->Tingkat->Visible) { // Tingkat ?>
<?php if (ew_IsMobile() || $khs_search->IsModal) { ?>
	<div id="r_Tingkat" class="form-group">
		<label for="x_Tingkat" class="<?php echo $khs_search->SearchLabelClass ?>"><span id="elh_khs_Tingkat"><?php echo $khs->Tingkat->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Tingkat" id="z_Tingkat" value="LIKE"></p>
		</label>
		<div class="<?php echo $khs_search->SearchRightColumnClass ?>"><div<?php echo $khs->Tingkat->CellAttributes() ?>>
			<span id="el_khs_Tingkat">
<?php $khs->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_Tingkat" data-value-separator="<?php echo $khs->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $khs->Tingkat->EditAttributes() ?>>
<?php echo $khs->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $khs->Tingkat->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Tingkat">
		<td><span id="elh_khs_Tingkat"><?php echo $khs->Tingkat->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Tingkat" id="z_Tingkat" value="LIKE"></span></td>
		<td<?php echo $khs->Tingkat->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_khs_Tingkat">
<?php $khs->Tingkat->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$khs->Tingkat->EditAttrs["onchange"]; ?>
<select data-table="khs" data-field="x_Tingkat" data-value-separator="<?php echo $khs->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x_Tingkat" name="x_Tingkat"<?php echo $khs->Tingkat->EditAttributes() ?>>
<?php echo $khs->Tingkat->SelectOptionListHtml("x_Tingkat") ?>
</select>
<input type="hidden" name="s_x_Tingkat" id="s_x_Tingkat" value="<?php echo $khs->Tingkat->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->Kelas->Visible) { // Kelas ?>
<?php if (ew_IsMobile() || $khs_search->IsModal) { ?>
	<div id="r_Kelas" class="form-group">
		<label for="x_Kelas" class="<?php echo $khs_search->SearchLabelClass ?>"><span id="elh_khs_Kelas"><?php echo $khs->Kelas->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Kelas" id="z_Kelas" value="LIKE"></p>
		</label>
		<div class="<?php echo $khs_search->SearchRightColumnClass ?>"><div<?php echo $khs->Kelas->CellAttributes() ?>>
			<span id="el_khs_Kelas">
<select data-table="khs" data-field="x_Kelas" data-value-separator="<?php echo $khs->Kelas->DisplayValueSeparatorAttribute() ?>" id="x_Kelas" name="x_Kelas"<?php echo $khs->Kelas->EditAttributes() ?>>
<?php echo $khs->Kelas->SelectOptionListHtml("x_Kelas") ?>
</select>
<input type="hidden" name="s_x_Kelas" id="s_x_Kelas" value="<?php echo $khs->Kelas->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Kelas">
		<td><span id="elh_khs_Kelas"><?php echo $khs->Kelas->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Kelas" id="z_Kelas" value="LIKE"></span></td>
		<td<?php echo $khs->Kelas->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_khs_Kelas">
<select data-table="khs" data-field="x_Kelas" data-value-separator="<?php echo $khs->Kelas->DisplayValueSeparatorAttribute() ?>" id="x_Kelas" name="x_Kelas"<?php echo $khs->Kelas->EditAttributes() ?>>
<?php echo $khs->Kelas->SelectOptionListHtml("x_Kelas") ?>
</select>
<input type="hidden" name="s_x_Kelas" id="s_x_Kelas" value="<?php echo $khs->Kelas->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->StudentID->Visible) { // StudentID ?>
<?php if (ew_IsMobile() || $khs_search->IsModal) { ?>
	<div id="r_StudentID" class="form-group">
		<label for="x_StudentID" class="<?php echo $khs_search->SearchLabelClass ?>"><span id="elh_khs_StudentID"><?php echo $khs->StudentID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StudentID" id="z_StudentID" value="LIKE"></p>
		</label>
		<div class="<?php echo $khs_search->SearchRightColumnClass ?>"><div<?php echo $khs->StudentID->CellAttributes() ?>>
			<span id="el_khs_StudentID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_StudentID"><?php echo (strval($khs->StudentID->AdvancedSearch->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $khs->StudentID->AdvancedSearch->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($khs->StudentID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_StudentID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="khs" data-field="x_StudentID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $khs->StudentID->DisplayValueSeparatorAttribute() ?>" name="x_StudentID" id="x_StudentID" value="<?php echo $khs->StudentID->AdvancedSearch->SearchValue ?>"<?php echo $khs->StudentID->EditAttributes() ?>>
<input type="hidden" name="s_x_StudentID" id="s_x_StudentID" value="<?php echo $khs->StudentID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_StudentID">
		<td><span id="elh_khs_StudentID"><?php echo $khs->StudentID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StudentID" id="z_StudentID" value="LIKE"></span></td>
		<td<?php echo $khs->StudentID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_khs_StudentID">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_StudentID"><?php echo (strval($khs->StudentID->AdvancedSearch->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $khs->StudentID->AdvancedSearch->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($khs->StudentID->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_StudentID',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="khs" data-field="x_StudentID" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $khs->StudentID->DisplayValueSeparatorAttribute() ?>" name="x_StudentID" id="x_StudentID" value="<?php echo $khs->StudentID->AdvancedSearch->SearchValue ?>"<?php echo $khs->StudentID->EditAttributes() ?>>
<input type="hidden" name="s_x_StudentID" id="s_x_StudentID" value="<?php echo $khs->StudentID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->StatusStudentID->Visible) { // StatusStudentID ?>
<?php if (ew_IsMobile() || $khs_search->IsModal) { ?>
	<div id="r_StatusStudentID" class="form-group">
		<label for="x_StatusStudentID" class="<?php echo $khs_search->SearchLabelClass ?>"><span id="elh_khs_StatusStudentID"><?php echo $khs->StatusStudentID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusStudentID" id="z_StatusStudentID" value="LIKE"></p>
		</label>
		<div class="<?php echo $khs_search->SearchRightColumnClass ?>"><div<?php echo $khs->StatusStudentID->CellAttributes() ?>>
			<span id="el_khs_StatusStudentID">
<select data-table="khs" data-field="x_StatusStudentID" data-value-separator="<?php echo $khs->StatusStudentID->DisplayValueSeparatorAttribute() ?>" id="x_StatusStudentID" name="x_StatusStudentID"<?php echo $khs->StatusStudentID->EditAttributes() ?>>
<?php echo $khs->StatusStudentID->SelectOptionListHtml("x_StatusStudentID") ?>
</select>
<input type="hidden" name="s_x_StatusStudentID" id="s_x_StatusStudentID" value="<?php echo $khs->StatusStudentID->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_StatusStudentID">
		<td><span id="elh_khs_StatusStudentID"><?php echo $khs->StatusStudentID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_StatusStudentID" id="z_StatusStudentID" value="LIKE"></span></td>
		<td<?php echo $khs->StatusStudentID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_khs_StatusStudentID">
<select data-table="khs" data-field="x_StatusStudentID" data-value-separator="<?php echo $khs->StatusStudentID->DisplayValueSeparatorAttribute() ?>" id="x_StatusStudentID" name="x_StatusStudentID"<?php echo $khs->StatusStudentID->EditAttributes() ?>>
<?php echo $khs->StatusStudentID->SelectOptionListHtml("x_StatusStudentID") ?>
</select>
<input type="hidden" name="s_x_StatusStudentID" id="s_x_StatusStudentID" value="<?php echo $khs->StatusStudentID->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->Keterangan->Visible) { // Keterangan ?>
<?php if (ew_IsMobile() || $khs_search->IsModal) { ?>
	<div id="r_Keterangan" class="form-group">
		<label for="x_Keterangan" class="<?php echo $khs_search->SearchLabelClass ?>"><span id="elh_khs_Keterangan"><?php echo $khs->Keterangan->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Keterangan" id="z_Keterangan" value="LIKE"></p>
		</label>
		<div class="<?php echo $khs_search->SearchRightColumnClass ?>"><div<?php echo $khs->Keterangan->CellAttributes() ?>>
			<span id="el_khs_Keterangan">
<input type="text" data-table="khs" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" size="35" placeholder="<?php echo ew_HtmlEncode($khs->Keterangan->getPlaceHolder()) ?>" value="<?php echo $khs->Keterangan->EditValue ?>"<?php echo $khs->Keterangan->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Keterangan">
		<td><span id="elh_khs_Keterangan"><?php echo $khs->Keterangan->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Keterangan" id="z_Keterangan" value="LIKE"></span></td>
		<td<?php echo $khs->Keterangan->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_khs_Keterangan">
<input type="text" data-table="khs" data-field="x_Keterangan" name="x_Keterangan" id="x_Keterangan" size="35" placeholder="<?php echo ew_HtmlEncode($khs->Keterangan->getPlaceHolder()) ?>" value="<?php echo $khs->Keterangan->EditValue ?>"<?php echo $khs->Keterangan->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($khs->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $khs_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $khs_search->SearchLabelClass ?>"><span id="elh_khs_NA"><?php echo $khs->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $khs_search->SearchRightColumnClass ?>"><div<?php echo $khs->NA->CellAttributes() ?>>
			<span id="el_khs_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_NA" data-value-separator="<?php echo $khs->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $khs->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_khs_NA"><?php echo $khs->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $khs->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_khs_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="khs" data-field="x_NA" data-value-separator="<?php echo $khs->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $khs->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $khs->NA->RadioButtonListHtml(FALSE, "x_NA") ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $khs_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$khs_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$khs_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fkhssearch.Init();
</script>
<?php
$khs_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$khs_search->Page_Terminate();
?>
