<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "master_prodiinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$master_prodi_search = NULL; // Initialize page object first

class cmaster_prodi_search extends cmaster_prodi {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'master_prodi';

	// Page object name
	var $PageObjName = 'master_prodi_search';

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

		// Table object (master_prodi)
		if (!isset($GLOBALS["master_prodi"]) || get_class($GLOBALS["master_prodi"]) == "cmaster_prodi") {
			$GLOBALS["master_prodi"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["master_prodi"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'master_prodi', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("master_prodilist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->ProdiID->SetVisibility();
		$this->Nama->SetVisibility();
		$this->Pejabat->SetVisibility();
		$this->Keterangan->SetVisibility();
		$this->Akreditasi->SetVisibility();
		$this->NoSK->SetVisibility();
		$this->TglSK->SetVisibility();
		$this->Creator->SetVisibility();
		$this->CreateDate->SetVisibility();
		$this->Editor->SetVisibility();
		$this->EditDate->SetVisibility();
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
		global $EW_EXPORT, $master_prodi;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($master_prodi);
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
						$sSrchStr = "master_prodilist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->Nama); // Nama
		$this->BuildSearchUrl($sSrchUrl, $this->Pejabat); // Pejabat
		$this->BuildSearchUrl($sSrchUrl, $this->Keterangan); // Keterangan
		$this->BuildSearchUrl($sSrchUrl, $this->Akreditasi); // Akreditasi
		$this->BuildSearchUrl($sSrchUrl, $this->NoSK); // NoSK
		$this->BuildSearchUrl($sSrchUrl, $this->TglSK); // TglSK
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
		// ProdiID

		$this->ProdiID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ProdiID"));
		$this->ProdiID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ProdiID");

		// Nama
		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Nama"));
		$this->Nama->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Nama");

		// Pejabat
		$this->Pejabat->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Pejabat"));
		$this->Pejabat->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Pejabat");

		// Keterangan
		$this->Keterangan->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Keterangan"));
		$this->Keterangan->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Keterangan");

		// Akreditasi
		$this->Akreditasi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Akreditasi"));
		$this->Akreditasi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Akreditasi");

		// NoSK
		$this->NoSK->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NoSK"));
		$this->NoSK->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NoSK");

		// TglSK
		$this->TglSK->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TglSK"));
		$this->TglSK->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TglSK");

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
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// ProdiID
		// Nama
		// Pejabat
		// Keterangan
		// Akreditasi
		// NoSK
		// TglSK
		// Creator
		// CreateDate
		// Editor
		// EditDate
		// NA

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// ProdiID
		$this->ProdiID->ViewValue = $this->ProdiID->CurrentValue;
		$this->ProdiID->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// Pejabat
		if (strval($this->Pejabat->CurrentValue) <> "") {
			$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->Pejabat->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `TeacherID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
		$sWhereWrk = "";
		$this->Pejabat->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Pejabat, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Pejabat->ViewValue = $this->Pejabat->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Pejabat->ViewValue = $this->Pejabat->CurrentValue;
			}
		} else {
			$this->Pejabat->ViewValue = NULL;
		}
		$this->Pejabat->ViewCustomAttributes = "";

		// Keterangan
		$this->Keterangan->ViewValue = $this->Keterangan->CurrentValue;
		$this->Keterangan->ViewCustomAttributes = "";

		// Akreditasi
		$this->Akreditasi->ViewValue = $this->Akreditasi->CurrentValue;
		$this->Akreditasi->ViewCustomAttributes = "";

		// NoSK
		$this->NoSK->ViewValue = $this->NoSK->CurrentValue;
		$this->NoSK->ViewCustomAttributes = "";

		// TglSK
		$this->TglSK->ViewValue = $this->TglSK->CurrentValue;
		$this->TglSK->ViewValue = ew_FormatDateTime($this->TglSK->ViewValue, 0);
		$this->TglSK->ViewCustomAttributes = "";

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

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

			// Pejabat
			$this->Pejabat->LinkCustomAttributes = "";
			$this->Pejabat->HrefValue = "";
			$this->Pejabat->TooltipValue = "";

			// Keterangan
			$this->Keterangan->LinkCustomAttributes = "";
			$this->Keterangan->HrefValue = "";
			$this->Keterangan->TooltipValue = "";

			// Akreditasi
			$this->Akreditasi->LinkCustomAttributes = "";
			$this->Akreditasi->HrefValue = "";
			$this->Akreditasi->TooltipValue = "";

			// NoSK
			$this->NoSK->LinkCustomAttributes = "";
			$this->NoSK->HrefValue = "";
			$this->NoSK->TooltipValue = "";

			// TglSK
			$this->TglSK->LinkCustomAttributes = "";
			$this->TglSK->HrefValue = "";
			$this->TglSK->TooltipValue = "";

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

			// ProdiID
			$this->ProdiID->EditAttrs["class"] = "form-control";
			$this->ProdiID->EditCustomAttributes = "";
			$this->ProdiID->EditValue = ew_HtmlEncode($this->ProdiID->AdvancedSearch->SearchValue);
			$this->ProdiID->PlaceHolder = ew_RemoveHtml($this->ProdiID->FldCaption());

			// Nama
			$this->Nama->EditAttrs["class"] = "form-control";
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->AdvancedSearch->SearchValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// Pejabat
			$this->Pejabat->EditAttrs["class"] = "form-control";
			$this->Pejabat->EditCustomAttributes = "";
			if (trim(strval($this->Pejabat->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`TeacherID`" . ew_SearchString("=", $this->Pejabat->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			$sSqlWrk = "SELECT `TeacherID`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `teacher`";
			$sWhereWrk = "";
			$this->Pejabat->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Pejabat, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Pejabat->EditValue = $arwrk;

			// Keterangan
			$this->Keterangan->EditAttrs["class"] = "form-control";
			$this->Keterangan->EditCustomAttributes = "";
			$this->Keterangan->EditValue = ew_HtmlEncode($this->Keterangan->AdvancedSearch->SearchValue);
			$this->Keterangan->PlaceHolder = ew_RemoveHtml($this->Keterangan->FldCaption());

			// Akreditasi
			$this->Akreditasi->EditAttrs["class"] = "form-control";
			$this->Akreditasi->EditCustomAttributes = "";
			$this->Akreditasi->EditValue = ew_HtmlEncode($this->Akreditasi->AdvancedSearch->SearchValue);
			$this->Akreditasi->PlaceHolder = ew_RemoveHtml($this->Akreditasi->FldCaption());

			// NoSK
			$this->NoSK->EditAttrs["class"] = "form-control";
			$this->NoSK->EditCustomAttributes = "";
			$this->NoSK->EditValue = ew_HtmlEncode($this->NoSK->AdvancedSearch->SearchValue);
			$this->NoSK->PlaceHolder = ew_RemoveHtml($this->NoSK->FldCaption());

			// TglSK
			$this->TglSK->EditAttrs["class"] = "form-control";
			$this->TglSK->EditCustomAttributes = "";
			$this->TglSK->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->TglSK->AdvancedSearch->SearchValue, 0), 8));
			$this->TglSK->PlaceHolder = ew_RemoveHtml($this->TglSK->FldCaption());

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
		if (!ew_CheckDateDef($this->TglSK->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->TglSK->FldErrMsg());
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
		$this->ProdiID->AdvancedSearch->Load();
		$this->Nama->AdvancedSearch->Load();
		$this->Pejabat->AdvancedSearch->Load();
		$this->Keterangan->AdvancedSearch->Load();
		$this->Akreditasi->AdvancedSearch->Load();
		$this->NoSK->AdvancedSearch->Load();
		$this->TglSK->AdvancedSearch->Load();
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("master_prodilist.php"), "", $this->TableVar, TRUE);
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
		$this->MultiPages = $pages;
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_Pejabat":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `TeacherID` AS `LinkFld`, `Nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `teacher`";
			$sWhereWrk = "";
			$this->Pejabat->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`TeacherID` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->Pejabat, $sWhereWrk); // Call Lookup selecting
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
if (!isset($master_prodi_search)) $master_prodi_search = new cmaster_prodi_search();

// Page init
$master_prodi_search->Page_Init();

// Page main
$master_prodi_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_prodi_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($master_prodi_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fmaster_prodisearch = new ew_Form("fmaster_prodisearch", "search");
<?php } else { ?>
var CurrentForm = fmaster_prodisearch = new ew_Form("fmaster_prodisearch", "search");
<?php } ?>

// Form_CustomValidate event
fmaster_prodisearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_prodisearch.ValidateRequired = true;
<?php } else { ?>
fmaster_prodisearch.ValidateRequired = false; 
<?php } ?>

// Multi-Page
fmaster_prodisearch.MultiPage = new ew_MultiPage("fmaster_prodisearch");

// Dynamic selection lists
fmaster_prodisearch.Lists["x_Pejabat"] = {"LinkField":"x_TeacherID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"teacher"};
fmaster_prodisearch.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_prodisearch.Lists["x_NA"].Options = <?php echo json_encode($master_prodi->NA->Options()) ?>;

// Form object for search
// Validate function for search

fmaster_prodisearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_TglSK");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($master_prodi->TglSK->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_CreateDate");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($master_prodi->CreateDate->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_EditDate");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($master_prodi->EditDate->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$master_prodi_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $master_prodi_search->ShowPageHeader(); ?>
<?php
$master_prodi_search->ShowMessage();
?>
<form name="fmaster_prodisearch" id="fmaster_prodisearch" class="<?php echo $master_prodi_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($master_prodi_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $master_prodi_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="master_prodi">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($master_prodi_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$master_prodi_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<div class="ewMultiPage">
<div class="tabbable" id="master_prodi_search">
	<ul class="nav<?php echo $master_prodi_search->MultiPages->NavStyle() ?>">
		<li<?php echo $master_prodi_search->MultiPages->TabStyle("1") ?>><a href="#tab_master_prodi1" data-toggle="tab"><?php echo $master_prodi->PageCaption(1) ?></a></li>
		<li<?php echo $master_prodi_search->MultiPages->TabStyle("2") ?>><a href="#tab_master_prodi2" data-toggle="tab"><?php echo $master_prodi->PageCaption(2) ?></a></li>
		<li<?php echo $master_prodi_search->MultiPages->TabStyle("3") ?>><a href="#tab_master_prodi3" data-toggle="tab"><?php echo $master_prodi->PageCaption(3) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane<?php echo $master_prodi_search->MultiPages->PageStyle("1") ?>" id="tab_master_prodi1">
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_prodisearch1" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_prodi->ProdiID->Visible) { // ProdiID ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_ProdiID" class="form-group">
		<label for="x_ProdiID" class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_ProdiID"><?php echo $master_prodi->ProdiID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->ProdiID->CellAttributes() ?>>
			<span id="el_master_prodi_ProdiID">
<input type="text" data-table="master_prodi" data-field="x_ProdiID" data-page="1" name="x_ProdiID" id="x_ProdiID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_prodi->ProdiID->getPlaceHolder()) ?>" value="<?php echo $master_prodi->ProdiID->EditValue ?>"<?php echo $master_prodi->ProdiID->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ProdiID">
		<td><span id="elh_master_prodi_ProdiID"><?php echo $master_prodi->ProdiID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_ProdiID" id="z_ProdiID" value="LIKE"></span></td>
		<td<?php echo $master_prodi->ProdiID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_ProdiID">
<input type="text" data-table="master_prodi" data-field="x_ProdiID" data-page="1" name="x_ProdiID" id="x_ProdiID" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_prodi->ProdiID->getPlaceHolder()) ?>" value="<?php echo $master_prodi->ProdiID->EditValue ?>"<?php echo $master_prodi->ProdiID->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->Nama->Visible) { // Nama ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_Nama" class="form-group">
		<label for="x_Nama" class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_Nama"><?php echo $master_prodi->Nama->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->Nama->CellAttributes() ?>>
			<span id="el_master_prodi_Nama">
<input type="text" data-table="master_prodi" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($master_prodi->Nama->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Nama->EditValue ?>"<?php echo $master_prodi->Nama->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Nama">
		<td><span id="elh_master_prodi_Nama"><?php echo $master_prodi->Nama->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></span></td>
		<td<?php echo $master_prodi->Nama->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_Nama">
<input type="text" data-table="master_prodi" data-field="x_Nama" data-page="1" name="x_Nama" id="x_Nama" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($master_prodi->Nama->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Nama->EditValue ?>"<?php echo $master_prodi->Nama->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->Pejabat->Visible) { // Pejabat ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_Pejabat" class="form-group">
		<label for="x_Pejabat" class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_Pejabat"><?php echo $master_prodi->Pejabat->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Pejabat" id="z_Pejabat" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->Pejabat->CellAttributes() ?>>
			<span id="el_master_prodi_Pejabat">
<select data-table="master_prodi" data-field="x_Pejabat" data-page="1" data-value-separator="<?php echo $master_prodi->Pejabat->DisplayValueSeparatorAttribute() ?>" id="x_Pejabat" name="x_Pejabat"<?php echo $master_prodi->Pejabat->EditAttributes() ?>>
<?php echo $master_prodi->Pejabat->SelectOptionListHtml("x_Pejabat") ?>
</select>
<input type="hidden" name="s_x_Pejabat" id="s_x_Pejabat" value="<?php echo $master_prodi->Pejabat->LookupFilterQuery() ?>">
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Pejabat">
		<td><span id="elh_master_prodi_Pejabat"><?php echo $master_prodi->Pejabat->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Pejabat" id="z_Pejabat" value="LIKE"></span></td>
		<td<?php echo $master_prodi->Pejabat->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_Pejabat">
<select data-table="master_prodi" data-field="x_Pejabat" data-page="1" data-value-separator="<?php echo $master_prodi->Pejabat->DisplayValueSeparatorAttribute() ?>" id="x_Pejabat" name="x_Pejabat"<?php echo $master_prodi->Pejabat->EditAttributes() ?>>
<?php echo $master_prodi->Pejabat->SelectOptionListHtml("x_Pejabat") ?>
</select>
<input type="hidden" name="s_x_Pejabat" id="s_x_Pejabat" value="<?php echo $master_prodi->Pejabat->LookupFilterQuery() ?>">
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->Keterangan->Visible) { // Keterangan ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_Keterangan" class="form-group">
		<label for="x_Keterangan" class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_Keterangan"><?php echo $master_prodi->Keterangan->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Keterangan" id="z_Keterangan" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->Keterangan->CellAttributes() ?>>
			<span id="el_master_prodi_Keterangan">
<input type="text" data-table="master_prodi" data-field="x_Keterangan" data-page="1" name="x_Keterangan" id="x_Keterangan" size="35" placeholder="<?php echo ew_HtmlEncode($master_prodi->Keterangan->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Keterangan->EditValue ?>"<?php echo $master_prodi->Keterangan->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Keterangan">
		<td><span id="elh_master_prodi_Keterangan"><?php echo $master_prodi->Keterangan->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Keterangan" id="z_Keterangan" value="LIKE"></span></td>
		<td<?php echo $master_prodi->Keterangan->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_Keterangan">
<input type="text" data-table="master_prodi" data-field="x_Keterangan" data-page="1" name="x_Keterangan" id="x_Keterangan" size="35" placeholder="<?php echo ew_HtmlEncode($master_prodi->Keterangan->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Keterangan->EditValue ?>"<?php echo $master_prodi->Keterangan->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $master_prodi_search->MultiPages->PageStyle("2") ?>" id="tab_master_prodi2">
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_prodisearch2" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_prodi->Akreditasi->Visible) { // Akreditasi ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_Akreditasi" class="form-group">
		<label for="x_Akreditasi" class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_Akreditasi"><?php echo $master_prodi->Akreditasi->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Akreditasi" id="z_Akreditasi" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->Akreditasi->CellAttributes() ?>>
			<span id="el_master_prodi_Akreditasi">
<input type="text" data-table="master_prodi" data-field="x_Akreditasi" data-page="2" name="x_Akreditasi" id="x_Akreditasi" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($master_prodi->Akreditasi->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Akreditasi->EditValue ?>"<?php echo $master_prodi->Akreditasi->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Akreditasi">
		<td><span id="elh_master_prodi_Akreditasi"><?php echo $master_prodi->Akreditasi->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Akreditasi" id="z_Akreditasi" value="LIKE"></span></td>
		<td<?php echo $master_prodi->Akreditasi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_Akreditasi">
<input type="text" data-table="master_prodi" data-field="x_Akreditasi" data-page="2" name="x_Akreditasi" id="x_Akreditasi" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($master_prodi->Akreditasi->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Akreditasi->EditValue ?>"<?php echo $master_prodi->Akreditasi->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->NoSK->Visible) { // NoSK ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_NoSK" class="form-group">
		<label for="x_NoSK" class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_NoSK"><?php echo $master_prodi->NoSK->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NoSK" id="z_NoSK" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->NoSK->CellAttributes() ?>>
			<span id="el_master_prodi_NoSK">
<input type="text" data-table="master_prodi" data-field="x_NoSK" data-page="2" name="x_NoSK" id="x_NoSK" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_prodi->NoSK->getPlaceHolder()) ?>" value="<?php echo $master_prodi->NoSK->EditValue ?>"<?php echo $master_prodi->NoSK->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NoSK">
		<td><span id="elh_master_prodi_NoSK"><?php echo $master_prodi->NoSK->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NoSK" id="z_NoSK" value="LIKE"></span></td>
		<td<?php echo $master_prodi->NoSK->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_NoSK">
<input type="text" data-table="master_prodi" data-field="x_NoSK" data-page="2" name="x_NoSK" id="x_NoSK" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_prodi->NoSK->getPlaceHolder()) ?>" value="<?php echo $master_prodi->NoSK->EditValue ?>"<?php echo $master_prodi->NoSK->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->TglSK->Visible) { // TglSK ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_TglSK" class="form-group">
		<label for="x_TglSK" class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_TglSK"><?php echo $master_prodi->TglSK->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TglSK" id="z_TglSK" value="="></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->TglSK->CellAttributes() ?>>
			<span id="el_master_prodi_TglSK">
<input type="text" data-table="master_prodi" data-field="x_TglSK" data-page="2" name="x_TglSK" id="x_TglSK" placeholder="<?php echo ew_HtmlEncode($master_prodi->TglSK->getPlaceHolder()) ?>" value="<?php echo $master_prodi->TglSK->EditValue ?>"<?php echo $master_prodi->TglSK->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TglSK">
		<td><span id="elh_master_prodi_TglSK"><?php echo $master_prodi->TglSK->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TglSK" id="z_TglSK" value="="></span></td>
		<td<?php echo $master_prodi->TglSK->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_TglSK">
<input type="text" data-table="master_prodi" data-field="x_TglSK" data-page="2" name="x_TglSK" id="x_TglSK" placeholder="<?php echo ew_HtmlEncode($master_prodi->TglSK->getPlaceHolder()) ?>" value="<?php echo $master_prodi->TglSK->EditValue ?>"<?php echo $master_prodi->TglSK->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
		<div class="tab-pane<?php echo $master_prodi_search->MultiPages->PageStyle("3") ?>" id="tab_master_prodi3">
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_master_prodisearch3" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($master_prodi->Creator->Visible) { // Creator ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_Creator" class="form-group">
		<label for="x_Creator" class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_Creator"><?php echo $master_prodi->Creator->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Creator" id="z_Creator" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->Creator->CellAttributes() ?>>
			<span id="el_master_prodi_Creator">
<input type="text" data-table="master_prodi" data-field="x_Creator" data-page="3" name="x_Creator" id="x_Creator" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_prodi->Creator->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Creator->EditValue ?>"<?php echo $master_prodi->Creator->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Creator">
		<td><span id="elh_master_prodi_Creator"><?php echo $master_prodi->Creator->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Creator" id="z_Creator" value="LIKE"></span></td>
		<td<?php echo $master_prodi->Creator->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_Creator">
<input type="text" data-table="master_prodi" data-field="x_Creator" data-page="3" name="x_Creator" id="x_Creator" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_prodi->Creator->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Creator->EditValue ?>"<?php echo $master_prodi->Creator->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->CreateDate->Visible) { // CreateDate ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_CreateDate" class="form-group">
		<label for="x_CreateDate" class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_CreateDate"><?php echo $master_prodi->CreateDate->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_CreateDate" id="z_CreateDate" value="="></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->CreateDate->CellAttributes() ?>>
			<span id="el_master_prodi_CreateDate">
<input type="text" data-table="master_prodi" data-field="x_CreateDate" data-page="3" name="x_CreateDate" id="x_CreateDate" placeholder="<?php echo ew_HtmlEncode($master_prodi->CreateDate->getPlaceHolder()) ?>" value="<?php echo $master_prodi->CreateDate->EditValue ?>"<?php echo $master_prodi->CreateDate->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_CreateDate">
		<td><span id="elh_master_prodi_CreateDate"><?php echo $master_prodi->CreateDate->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_CreateDate" id="z_CreateDate" value="="></span></td>
		<td<?php echo $master_prodi->CreateDate->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_CreateDate">
<input type="text" data-table="master_prodi" data-field="x_CreateDate" data-page="3" name="x_CreateDate" id="x_CreateDate" placeholder="<?php echo ew_HtmlEncode($master_prodi->CreateDate->getPlaceHolder()) ?>" value="<?php echo $master_prodi->CreateDate->EditValue ?>"<?php echo $master_prodi->CreateDate->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->Editor->Visible) { // Editor ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_Editor" class="form-group">
		<label for="x_Editor" class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_Editor"><?php echo $master_prodi->Editor->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Editor" id="z_Editor" value="LIKE"></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->Editor->CellAttributes() ?>>
			<span id="el_master_prodi_Editor">
<input type="text" data-table="master_prodi" data-field="x_Editor" data-page="3" name="x_Editor" id="x_Editor" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_prodi->Editor->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Editor->EditValue ?>"<?php echo $master_prodi->Editor->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Editor">
		<td><span id="elh_master_prodi_Editor"><?php echo $master_prodi->Editor->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Editor" id="z_Editor" value="LIKE"></span></td>
		<td<?php echo $master_prodi->Editor->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_Editor">
<input type="text" data-table="master_prodi" data-field="x_Editor" data-page="3" name="x_Editor" id="x_Editor" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_prodi->Editor->getPlaceHolder()) ?>" value="<?php echo $master_prodi->Editor->EditValue ?>"<?php echo $master_prodi->Editor->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->EditDate->Visible) { // EditDate ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_EditDate" class="form-group">
		<label for="x_EditDate" class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_EditDate"><?php echo $master_prodi->EditDate->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_EditDate" id="z_EditDate" value="="></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->EditDate->CellAttributes() ?>>
			<span id="el_master_prodi_EditDate">
<input type="text" data-table="master_prodi" data-field="x_EditDate" data-page="3" name="x_EditDate" id="x_EditDate" placeholder="<?php echo ew_HtmlEncode($master_prodi->EditDate->getPlaceHolder()) ?>" value="<?php echo $master_prodi->EditDate->EditValue ?>"<?php echo $master_prodi->EditDate->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_EditDate">
		<td><span id="elh_master_prodi_EditDate"><?php echo $master_prodi->EditDate->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_EditDate" id="z_EditDate" value="="></span></td>
		<td<?php echo $master_prodi->EditDate->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_EditDate">
<input type="text" data-table="master_prodi" data-field="x_EditDate" data-page="3" name="x_EditDate" id="x_EditDate" placeholder="<?php echo ew_HtmlEncode($master_prodi->EditDate->getPlaceHolder()) ?>" value="<?php echo $master_prodi->EditDate->EditValue ?>"<?php echo $master_prodi->EditDate->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($master_prodi->NA->Visible) { // NA ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
	<div id="r_NA" class="form-group">
		<label class="<?php echo $master_prodi_search->SearchLabelClass ?>"><span id="elh_master_prodi_NA"><?php echo $master_prodi->NA->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></p>
		</label>
		<div class="<?php echo $master_prodi_search->SearchRightColumnClass ?>"><div<?php echo $master_prodi->NA->CellAttributes() ?>>
			<span id="el_master_prodi_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_prodi" data-field="x_NA" data-page="3" data-value-separator="<?php echo $master_prodi->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_prodi->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_prodi->NA->RadioButtonListHtml(FALSE, "x_NA", 3) ?>
</div></div>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NA">
		<td><span id="elh_master_prodi_NA"><?php echo $master_prodi->NA->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_NA" id="z_NA" value="="></span></td>
		<td<?php echo $master_prodi->NA->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_master_prodi_NA">
<div id="tp_x_NA" class="ewTemplate"><input type="radio" data-table="master_prodi" data-field="x_NA" data-page="3" data-value-separator="<?php echo $master_prodi->NA->DisplayValueSeparatorAttribute() ?>" name="x_NA" id="x_NA" value="{value}"<?php echo $master_prodi->NA->EditAttributes() ?>></div>
<div id="dsl_x_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_prodi->NA->RadioButtonListHtml(FALSE, "x_NA", 3) ?>
</div></div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $master_prodi_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
		</div>
	</div>
</div>
</div>
<?php if (!$master_prodi_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$master_prodi_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fmaster_prodisearch.Init();
</script>
<?php
$master_prodi_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$master_prodi_search->Page_Terminate();
?>
