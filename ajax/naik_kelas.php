<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
$EW_RELATIVE_PATH = "../";
?>
<?php include_once $EW_RELATIVE_PATH . "ewcfg13.php" ?>
<?php $EW_ROOT_RELATIVE_PATH = "../"; ?>
<?php include_once $EW_RELATIVE_PATH . ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once $EW_RELATIVE_PATH . "phpfn13.php" ?>
<?php include_once $EW_RELATIVE_PATH . "usersinfo.php" ?>
<?php include_once $EW_RELATIVE_PATH . "userfn13.php" ?>
<?php

//
// Page class
//

$naik_kelas_php = NULL; // Initialize page object first

class cnaik_kelas_php
{

	// Page ID
	var $PageID = 'custom';

	// Project ID
	var $ProjectID = "{B4ECA7F4-5928-4768-B0FE-A8227431E424}";

	// Table name
	var $TableName = 'naik_kelas.php';

	// Page object name
	var $PageObjName = 'naik_kelas_php';

	// Page name
	function PageName()
	{
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl()
	{
		$PageUrl = ew_CurrentPage() . "?";
		return $PageUrl;
	}

	// Message
	function getMessage()
	{
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v)
	{
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage()
	{
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v)
	{
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage()
	{
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v)
	{
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage()
	{
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v)
	{
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage()
	{
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage()
	{
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage()
	{
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage()
	{
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages()
	{
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage()
	{
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
	function ValidPost()
	{
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
	function CreateToken()
	{
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
	function __construct()
	{
		global $conn, $Language;
		global $UserTable, $UserTableConn;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'custom', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'naik_kelas.php', TRUE);

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
	function Page_Init()
	{
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loaded();
		if (!$Security->CanReport()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			$this->Page_Terminate(ew_GetUrl("index.php"));
		}
		global $gbOldSkipHeaderFooter, $gbSkipHeaderFooter;
		$gbOldSkipHeaderFooter = $gbSkipHeaderFooter;
		$gbSkipHeaderFooter = TRUE;

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

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
	function Page_Terminate($url = "")
	{
		global $gsExportFile, $gTmpImages;
		global $gbOldSkipHeaderFooter, $gbSkipHeaderFooter;
		$gbSkipHeaderFooter = $gbOldSkipHeaderFooter;

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		// Close connection

		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
	}

	//
	// Page main
	//
	function Page_Main()
	{

		// Set up Breadcrumb
		$this->SetupBreadcrumb();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb()
	{
		global $Breadcrumb;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/") + 1);
		$Breadcrumb->Add("custom", "naik_kelas_php", $url, "", "naik_kelas_php", TRUE);
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($naik_kelas_php)) $naik_kelas_php = new cnaik_kelas_php();

// Page init
$naik_kelas_php->Page_Init();

// Page main
$naik_kelas_php->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();
?>
<?php include_once $EW_RELATIVE_PATH . "header.php" ?>
<?php if (!@$gbSkipHeaderFooter) { ?>
	<div class="ewToolbar">
		<?php $Breadcrumb->Render(); ?>
		<?php echo $Language->SelectionForm(); ?>
		<div class="clearfix"></div>
	</div>
<?php } ?>
<!-- %%Custom page content begin%% -->
<?php

$conn = new mysqli('localhost', 'karangpucung', '#SemarangHebat1', 'sisfo');
if ($conn->connect_error) {
	echoo('Gagal melakukan koneksi ke database');
	die('Error: ' . $conn->connect_error);
}

$sesi = '1,2';
$studentStatusId = 'L';
$editor = 'admin';
$editDate = date('Y-m-d h:i:s');
$NA = 'Y';

$stmtXII = $conn->query("SELECT StudentID FROM khs WHERE Tingkat='XII' AND NA='N';");
if ($stmtXII->num_rows > 0) {
	foreach ($stmtXII as $sel) {
		$stmtUpKhs = $conn->prepare("UPDATE khs SET StatusStudentID = ?, Editor = ?, EditDate = ?, NA = ? WHERE StudentID = ?;");
		$stmtUpKhs->bind_param(
			'sssss',
			$studentStatusId,
			$editor,
			$editDate,
			$NA,
			$sel['StudentID']
		);

		$stmtUpKhs->execute();
		$stmtUpKhs->reset();

		$stmtUpStudent = $conn->prepare("UPDATE student SET StudentStatusID = ?, Editor = ?, EditDate = ?, NA = ? WHERE StudentID = ?;");
		$stmtUpStudent->bind_param(
			'sssss',
			$studentStatusId,
			$editor,
			$editDate,
			$NA,
			$sel['StudentID']
		);

		$stmtUpStudent->execute();
		$stmtUpStudent->reset();
	}
}


$tingkat = 'XII';

$stmtXI = $conn->query("SELECT StudentID FROM khs WHERE Tingkat='XI' AND NA='N';");
if ($stmtXI->num_rows > 0) {
	foreach ($stmtXI as $sel) {
		$stmtUpKhs = $conn->prepare("UPDATE khs SET Tingkat = ?, Sesi = ?, Kelas = REPLACE(Kelas,'XI','XII'), Editor = ?, EditDate = ? WHERE StudentID = ?;");
		$stmtUpKhs->bind_param(
			'sssss',
			$tingkat,
			$sesi,
			$editor,
			$editDate,
			$sel['StudentID']
		);

		$stmtUpKhs->execute();
		$stmtUpKhs->reset();
	}
}


$tingkat = 'XI';

$stmtX = $conn->query("SELECT StudentID FROM khs WHERE Tingkat='X' AND NA='N';");
if ($stmtX->num_rows > 0) {
	foreach ($stmtX as $sel) {
		$stmtUpKhs = $conn->prepare("UPDATE khs SET Tingkat = ?, Sesi = ?, Kelas = REPLACE(Kelas,'X','XI'), Editor = ?, EditDate = ? WHERE StudentID = ?;");
		$stmtUpKhs->bind_param(
			'sssss',
			$tingkat,
			$sesi,
			$editor,
			$editDate,
			$sel['StudentID']
		);

		$stmtUpKhs->execute();
		$stmtUpKhs->reset();
	}
}

?>
<!-- %%Custom page content end%% --><?php if (EW_DEBUG_ENABLED) echo ew_DebugMsg(); ?>
<?php include_once $EW_RELATIVE_PATH . "footer.php" ?>
<?php
$naik_kelas_php->Page_Terminate();
?>