<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$default = NULL; // Initialize page object first

class cdefault {

	// Page ID
	var $PageID = 'default';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Page object name
	var $PageObjName = 'default';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
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

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'default', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

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
		$this->Page_Redirecting($url);

		 // Close connection
		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}

	//
	// Page main
	//
	function Page_Main() {
		global $Security, $Language;

		// If session expired, show session expired message
		if (@$_GET["expired"] == "1")
			$this->setFailureMessage($Language->Phrase("SessionExpired"));
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		$Security->LoadUserLevel(); // Load User Level
		if ($Security->AllowList(CurrentProjectID() . 'home.php'))
		$this->Page_Terminate("home.php"); // Exit and go to default page
		if ($Security->AllowList(CurrentProjectID() . 'tahun'))
			$this->Page_Terminate("tahunlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'jadwal'))
			$this->Page_Terminate("jadwallist.php");
		if ($Security->AllowList(CurrentProjectID() . 'kelas'))
			$this->Page_Terminate("kelaslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'khs'))
			$this->Page_Terminate("khslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'kurikulum'))
			$this->Page_Terminate("kurikulumlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'mk'))
			$this->Page_Terminate("mklist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_agama'))
			$this->Page_Terminate("master_agamalist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_bahasa'))
			$this->Page_Terminate("master_bahasalist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_darah'))
			$this->Page_Terminate("master_darahlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_golongan'))
			$this->Page_Terminate("master_golonganlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_hari'))
			$this->Page_Terminate("master_harilist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_hidup'))
			$this->Page_Terminate("master_hiduplist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_jamkul'))
			$this->Page_Terminate("master_jamkullist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_jenjang'))
			$this->Page_Terminate("master_jenjanglist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_kampus'))
			$this->Page_Terminate("master_kampuslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_kelamin'))
			$this->Page_Terminate("master_kelaminlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_pekerjaanortu'))
			$this->Page_Terminate("master_pekerjaanortulist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_pendidikanortu'))
			$this->Page_Terminate("master_pendidikanortulist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_prodi'))
			$this->Page_Terminate("master_prodilist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_sesi'))
			$this->Page_Terminate("master_sesilist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_statusawal'))
			$this->Page_Terminate("master_statusawallist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_statuskerja'))
			$this->Page_Terminate("master_statuskerjalist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_wilayah_benua'))
			$this->Page_Terminate("master_wilayah_benualist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_wilayah_desa'))
			$this->Page_Terminate("master_wilayah_desalist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_wilayah_kabupatenkota'))
			$this->Page_Terminate("master_wilayah_kabupatenkotalist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_wilayah_kecamatan'))
			$this->Page_Terminate("master_wilayah_kecamatanlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_wilayah_negara'))
			$this->Page_Terminate("master_wilayah_negaralist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_wilayah_provinsi'))
			$this->Page_Terminate("master_wilayah_provinsilist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_bagian'))
			$this->Page_Terminate("master_bagianlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_ikatan'))
			$this->Page_Terminate("master_ikatanlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'statuslulus'))
			$this->Page_Terminate("statusluluslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_statussipil'))
			$this->Page_Terminate("master_statussipillist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_statusstudent'))
			$this->Page_Terminate("master_statusstudentlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'master_statusteacher'))
			$this->Page_Terminate("master_statusteacherlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'student'))
			$this->Page_Terminate("studentlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'users'))
			$this->Page_Terminate("userslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'staff'))
			$this->Page_Terminate("stafflist.php");
		if ($Security->AllowList(CurrentProjectID() . 'teacher'))
			$this->Page_Terminate("teacherlist.php");
		if ($Security->AllowList(CurrentProjectID() . 'audittrail'))
			$this->Page_Terminate("audittraillist.php");
		if ($Security->AllowList(CurrentProjectID() . 'sync_elearning.php'))
			$this->Page_Terminate("sync_elearning.php");
		if ($Security->AllowList(CurrentProjectID() . 'userlevels'))
			$this->Page_Terminate("userlevelslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'import_data.php'))
			$this->Page_Terminate("import_data.php");
		if ($Security->AllowList(CurrentProjectID() . 'naik_kelas.php'))
			$this->Page_Terminate("naik_kelas.php");
		if ($Security->IsLoggedIn()) {
			$this->setFailureMessage(ew_DeniedMsg() . "<br><br><a href=\"logout.php\">" . $Language->Phrase("BackToLogin") . "</a>");
		} else {
			$this->Page_Terminate("login.php"); // Exit and go to login page
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
	// $type = ''|'success'|'failure'
	function Message_Showing(&$msg, $type) {

		// Example:
		//if ($type == 'success') $msg = "your success message";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($default)) $default = new cdefault();

// Page init
$default->Page_Init();

// Page main
$default->Page_Main();
?>
<?php include_once "header.php" ?>
<?php
$default->ShowMessage();
?>
<?php include_once "footer.php" ?>
<?php
$default->Page_Terminate();
?>
