<?php include_once "usersinfo.php" ?>
<?php

// Create page object
if (!isset($master_mk_grid)) $master_mk_grid = new cmaster_mk_grid();

// Page init
$master_mk_grid->Page_Init();

// Page main
$master_mk_grid->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$master_mk_grid->Page_Render();
?>
<?php if ($master_mk->Export == "") { ?>
<script type="text/javascript">

// Form object
var fmaster_mkgrid = new ew_Form("fmaster_mkgrid", "grid");
fmaster_mkgrid.FormKeyCountName = '<?php echo $master_mk_grid->FormKeyCountName ?>';

// Validate form
fmaster_mkgrid.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
		var checkrow = (gridinsert) ? !this.EmptyRow(infix) : true;
		if (checkrow) {
			addcnt++;
			elm = this.GetElements("x" + infix + "_Nama");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_mk->Nama->FldCaption(), $master_mk->Nama->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Tingkat");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $master_mk->Tingkat->FldCaption(), $master_mk->Tingkat->ReqErrMsg)) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
		} // End Grid Add checking
	}
	return true;
}

// Check empty row
fmaster_mkgrid.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "MKKode", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Nama", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Singkatan", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Tingkat", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Sesi", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Wajib", true)) return false;
	if (ew_ValueChanged(fobj, infix, "NA", true)) return false;
	return true;
}

// Form_CustomValidate event
fmaster_mkgrid.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmaster_mkgrid.ValidateRequired = true;
<?php } else { ?>
fmaster_mkgrid.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmaster_mkgrid.Lists["x_Tingkat"] = {"LinkField":"x_Tingkat","Ajax":true,"AutoFill":false,"DisplayFields":["x_Tingkat","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"kelas"};
fmaster_mkgrid.Lists["x_Sesi"] = {"LinkField":"x_Sesi","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaSesi","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_sesi"};
fmaster_mkgrid.Lists["x_Wajib"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_mkgrid.Lists["x_Wajib"].Options = <?php echo json_encode($master_mk->Wajib->Options()) ?>;
fmaster_mkgrid.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmaster_mkgrid.Lists["x_NA"].Options = <?php echo json_encode($master_mk->NA->Options()) ?>;

// Form object for search
</script>
<?php } ?>
<?php
if ($master_mk->CurrentAction == "gridadd") {
	if ($master_mk->CurrentMode == "copy") {
		$bSelectLimit = $master_mk_grid->UseSelectLimit;
		if ($bSelectLimit) {
			$master_mk_grid->TotalRecs = $master_mk->SelectRecordCount();
			$master_mk_grid->Recordset = $master_mk_grid->LoadRecordset($master_mk_grid->StartRec-1, $master_mk_grid->DisplayRecs);
		} else {
			if ($master_mk_grid->Recordset = $master_mk_grid->LoadRecordset())
				$master_mk_grid->TotalRecs = $master_mk_grid->Recordset->RecordCount();
		}
		$master_mk_grid->StartRec = 1;
		$master_mk_grid->DisplayRecs = $master_mk_grid->TotalRecs;
	} else {
		$master_mk->CurrentFilter = "0=1";
		$master_mk_grid->StartRec = 1;
		$master_mk_grid->DisplayRecs = $master_mk->GridAddRowCount;
	}
	$master_mk_grid->TotalRecs = $master_mk_grid->DisplayRecs;
	$master_mk_grid->StopRec = $master_mk_grid->DisplayRecs;
} else {
	$bSelectLimit = $master_mk_grid->UseSelectLimit;
	if ($bSelectLimit) {
		if ($master_mk_grid->TotalRecs <= 0)
			$master_mk_grid->TotalRecs = $master_mk->SelectRecordCount();
	} else {
		if (!$master_mk_grid->Recordset && ($master_mk_grid->Recordset = $master_mk_grid->LoadRecordset()))
			$master_mk_grid->TotalRecs = $master_mk_grid->Recordset->RecordCount();
	}
	$master_mk_grid->StartRec = 1;
	$master_mk_grid->DisplayRecs = $master_mk_grid->TotalRecs; // Display all records
	if ($bSelectLimit)
		$master_mk_grid->Recordset = $master_mk_grid->LoadRecordset($master_mk_grid->StartRec-1, $master_mk_grid->DisplayRecs);

	// Set no record found message
	if ($master_mk->CurrentAction == "" && $master_mk_grid->TotalRecs == 0) {
		if (!$Security->CanList())
			$master_mk_grid->setWarningMessage(ew_DeniedMsg());
		if ($master_mk_grid->SearchWhere == "0=101")
			$master_mk_grid->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$master_mk_grid->setWarningMessage($Language->Phrase("NoRecord"));
	}
}
$master_mk_grid->RenderOtherOptions();
?>
<?php $master_mk_grid->ShowPageHeader(); ?>
<?php
$master_mk_grid->ShowMessage();
?>
<?php if ($master_mk_grid->TotalRecs > 0 || $master_mk->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid master_mk">
<div id="fmaster_mkgrid" class="ewForm form-inline">
<?php if ($master_mk_grid->ShowOtherOptions) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php
	foreach ($master_mk_grid->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<div id="gmp_master_mk" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table id="tbl_master_mkgrid" class="table ewTable">
<?php echo $master_mk->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$master_mk_grid->RowType = EW_ROWTYPE_HEADER;

// Render list options
$master_mk_grid->RenderListOptions();

// Render list options (header, left)
$master_mk_grid->ListOptions->Render("header", "left");
?>
<?php if ($master_mk->MKKode->Visible) { // MKKode ?>
	<?php if ($master_mk->SortUrl($master_mk->MKKode) == "") { ?>
		<th data-name="MKKode"><div id="elh_master_mk_MKKode" class="master_mk_MKKode"><div class="ewTableHeaderCaption"><?php echo $master_mk->MKKode->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="MKKode"><div><div id="elh_master_mk_MKKode" class="master_mk_MKKode">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_mk->MKKode->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_mk->MKKode->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_mk->MKKode->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_mk->Nama->Visible) { // Nama ?>
	<?php if ($master_mk->SortUrl($master_mk->Nama) == "") { ?>
		<th data-name="Nama"><div id="elh_master_mk_Nama" class="master_mk_Nama"><div class="ewTableHeaderCaption"><?php echo $master_mk->Nama->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Nama"><div><div id="elh_master_mk_Nama" class="master_mk_Nama">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_mk->Nama->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_mk->Nama->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_mk->Nama->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_mk->Singkatan->Visible) { // Singkatan ?>
	<?php if ($master_mk->SortUrl($master_mk->Singkatan) == "") { ?>
		<th data-name="Singkatan"><div id="elh_master_mk_Singkatan" class="master_mk_Singkatan"><div class="ewTableHeaderCaption"><?php echo $master_mk->Singkatan->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Singkatan"><div><div id="elh_master_mk_Singkatan" class="master_mk_Singkatan">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_mk->Singkatan->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_mk->Singkatan->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_mk->Singkatan->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_mk->Tingkat->Visible) { // Tingkat ?>
	<?php if ($master_mk->SortUrl($master_mk->Tingkat) == "") { ?>
		<th data-name="Tingkat"><div id="elh_master_mk_Tingkat" class="master_mk_Tingkat"><div class="ewTableHeaderCaption"><?php echo $master_mk->Tingkat->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tingkat"><div><div id="elh_master_mk_Tingkat" class="master_mk_Tingkat">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_mk->Tingkat->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_mk->Tingkat->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_mk->Tingkat->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_mk->Sesi->Visible) { // Sesi ?>
	<?php if ($master_mk->SortUrl($master_mk->Sesi) == "") { ?>
		<th data-name="Sesi"><div id="elh_master_mk_Sesi" class="master_mk_Sesi"><div class="ewTableHeaderCaption"><?php echo $master_mk->Sesi->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Sesi"><div><div id="elh_master_mk_Sesi" class="master_mk_Sesi">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_mk->Sesi->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_mk->Sesi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_mk->Sesi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_mk->Wajib->Visible) { // Wajib ?>
	<?php if ($master_mk->SortUrl($master_mk->Wajib) == "") { ?>
		<th data-name="Wajib"><div id="elh_master_mk_Wajib" class="master_mk_Wajib"><div class="ewTableHeaderCaption"><?php echo $master_mk->Wajib->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Wajib"><div><div id="elh_master_mk_Wajib" class="master_mk_Wajib">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_mk->Wajib->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_mk->Wajib->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_mk->Wajib->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($master_mk->NA->Visible) { // NA ?>
	<?php if ($master_mk->SortUrl($master_mk->NA) == "") { ?>
		<th data-name="NA"><div id="elh_master_mk_NA" class="master_mk_NA"><div class="ewTableHeaderCaption"><?php echo $master_mk->NA->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NA"><div><div id="elh_master_mk_NA" class="master_mk_NA">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $master_mk->NA->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($master_mk->NA->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($master_mk->NA->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$master_mk_grid->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
$master_mk_grid->StartRec = 1;
$master_mk_grid->StopRec = $master_mk_grid->TotalRecs; // Show all records

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($master_mk_grid->FormKeyCountName) && ($master_mk->CurrentAction == "gridadd" || $master_mk->CurrentAction == "gridedit" || $master_mk->CurrentAction == "F")) {
		$master_mk_grid->KeyCount = $objForm->GetValue($master_mk_grid->FormKeyCountName);
		$master_mk_grid->StopRec = $master_mk_grid->StartRec + $master_mk_grid->KeyCount - 1;
	}
}
$master_mk_grid->RecCnt = $master_mk_grid->StartRec - 1;
if ($master_mk_grid->Recordset && !$master_mk_grid->Recordset->EOF) {
	$master_mk_grid->Recordset->MoveFirst();
	$bSelectLimit = $master_mk_grid->UseSelectLimit;
	if (!$bSelectLimit && $master_mk_grid->StartRec > 1)
		$master_mk_grid->Recordset->Move($master_mk_grid->StartRec - 1);
} elseif (!$master_mk->AllowAddDeleteRow && $master_mk_grid->StopRec == 0) {
	$master_mk_grid->StopRec = $master_mk->GridAddRowCount;
}

// Initialize aggregate
$master_mk->RowType = EW_ROWTYPE_AGGREGATEINIT;
$master_mk->ResetAttrs();
$master_mk_grid->RenderRow();
if ($master_mk->CurrentAction == "gridadd")
	$master_mk_grid->RowIndex = 0;
if ($master_mk->CurrentAction == "gridedit")
	$master_mk_grid->RowIndex = 0;
while ($master_mk_grid->RecCnt < $master_mk_grid->StopRec) {
	$master_mk_grid->RecCnt++;
	if (intval($master_mk_grid->RecCnt) >= intval($master_mk_grid->StartRec)) {
		$master_mk_grid->RowCnt++;
		if ($master_mk->CurrentAction == "gridadd" || $master_mk->CurrentAction == "gridedit" || $master_mk->CurrentAction == "F") {
			$master_mk_grid->RowIndex++;
			$objForm->Index = $master_mk_grid->RowIndex;
			if ($objForm->HasValue($master_mk_grid->FormActionName))
				$master_mk_grid->RowAction = strval($objForm->GetValue($master_mk_grid->FormActionName));
			elseif ($master_mk->CurrentAction == "gridadd")
				$master_mk_grid->RowAction = "insert";
			else
				$master_mk_grid->RowAction = "";
		}

		// Set up key count
		$master_mk_grid->KeyCount = $master_mk_grid->RowIndex;

		// Init row class and style
		$master_mk->ResetAttrs();
		$master_mk->CssClass = "";
		if ($master_mk->CurrentAction == "gridadd") {
			if ($master_mk->CurrentMode == "copy") {
				$master_mk_grid->LoadRowValues($master_mk_grid->Recordset); // Load row values
				$master_mk_grid->SetRecordKey($master_mk_grid->RowOldKey, $master_mk_grid->Recordset); // Set old record key
			} else {
				$master_mk_grid->LoadDefaultValues(); // Load default values
				$master_mk_grid->RowOldKey = ""; // Clear old key value
			}
		} else {
			$master_mk_grid->LoadRowValues($master_mk_grid->Recordset); // Load row values
		}
		$master_mk->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($master_mk->CurrentAction == "gridadd") // Grid add
			$master_mk->RowType = EW_ROWTYPE_ADD; // Render add
		if ($master_mk->CurrentAction == "gridadd" && $master_mk->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$master_mk_grid->RestoreCurrentRowFormValues($master_mk_grid->RowIndex); // Restore form values
		if ($master_mk->CurrentAction == "gridedit") { // Grid edit
			if ($master_mk->EventCancelled) {
				$master_mk_grid->RestoreCurrentRowFormValues($master_mk_grid->RowIndex); // Restore form values
			}
			if ($master_mk_grid->RowAction == "insert")
				$master_mk->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$master_mk->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($master_mk->CurrentAction == "gridedit" && ($master_mk->RowType == EW_ROWTYPE_EDIT || $master_mk->RowType == EW_ROWTYPE_ADD) && $master_mk->EventCancelled) // Update failed
			$master_mk_grid->RestoreCurrentRowFormValues($master_mk_grid->RowIndex); // Restore form values
		if ($master_mk->RowType == EW_ROWTYPE_EDIT) // Edit row
			$master_mk_grid->EditRowCnt++;
		if ($master_mk->CurrentAction == "F") // Confirm row
			$master_mk_grid->RestoreCurrentRowFormValues($master_mk_grid->RowIndex); // Restore form values

		// Set up row id / data-rowindex
		$master_mk->RowAttrs = array_merge($master_mk->RowAttrs, array('data-rowindex'=>$master_mk_grid->RowCnt, 'id'=>'r' . $master_mk_grid->RowCnt . '_master_mk', 'data-rowtype'=>$master_mk->RowType));

		// Render row
		$master_mk_grid->RenderRow();

		// Render list options
		$master_mk_grid->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($master_mk_grid->RowAction <> "delete" && $master_mk_grid->RowAction <> "insertdelete" && !($master_mk_grid->RowAction == "insert" && $master_mk->CurrentAction == "F" && $master_mk_grid->EmptyRow())) {
?>
	<tr<?php echo $master_mk->RowAttributes() ?>>
<?php

// Render list options (body, left)
$master_mk_grid->ListOptions->Render("body", "left", $master_mk_grid->RowCnt);
?>
	<?php if ($master_mk->MKKode->Visible) { // MKKode ?>
		<td data-name="MKKode"<?php echo $master_mk->MKKode->CellAttributes() ?>>
<?php if ($master_mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_MKKode" class="form-group master_mk_MKKode">
<input type="text" data-table="master_mk" data-field="x_MKKode" name="x<?php echo $master_mk_grid->RowIndex ?>_MKKode" id="x<?php echo $master_mk_grid->RowIndex ?>_MKKode" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->MKKode->getPlaceHolder()) ?>" value="<?php echo $master_mk->MKKode->EditValue ?>"<?php echo $master_mk->MKKode->EditAttributes() ?>>
</span>
<input type="hidden" data-table="master_mk" data-field="x_MKKode" name="o<?php echo $master_mk_grid->RowIndex ?>_MKKode" id="o<?php echo $master_mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($master_mk->MKKode->OldValue) ?>">
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_MKKode" class="form-group master_mk_MKKode">
<input type="text" data-table="master_mk" data-field="x_MKKode" name="x<?php echo $master_mk_grid->RowIndex ?>_MKKode" id="x<?php echo $master_mk_grid->RowIndex ?>_MKKode" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->MKKode->getPlaceHolder()) ?>" value="<?php echo $master_mk->MKKode->EditValue ?>"<?php echo $master_mk->MKKode->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_MKKode" class="master_mk_MKKode">
<span<?php echo $master_mk->MKKode->ViewAttributes() ?>>
<?php echo $master_mk->MKKode->ListViewValue() ?></span>
</span>
<?php if ($master_mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="master_mk" data-field="x_MKKode" name="x<?php echo $master_mk_grid->RowIndex ?>_MKKode" id="x<?php echo $master_mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($master_mk->MKKode->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_MKKode" name="o<?php echo $master_mk_grid->RowIndex ?>_MKKode" id="o<?php echo $master_mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($master_mk->MKKode->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="master_mk" data-field="x_MKKode" name="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_MKKode" id="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($master_mk->MKKode->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_MKKode" name="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_MKKode" id="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($master_mk->MKKode->OldValue) ?>">
<?php } ?>
<?php } ?>
<a id="<?php echo $master_mk_grid->PageObjName . "_row_" . $master_mk_grid->RowCnt ?>"></a></td>
	<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-table="master_mk" data-field="x_MKID" name="x<?php echo $master_mk_grid->RowIndex ?>_MKID" id="x<?php echo $master_mk_grid->RowIndex ?>_MKID" value="<?php echo ew_HtmlEncode($master_mk->MKID->CurrentValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_MKID" name="o<?php echo $master_mk_grid->RowIndex ?>_MKID" id="o<?php echo $master_mk_grid->RowIndex ?>_MKID" value="<?php echo ew_HtmlEncode($master_mk->MKID->OldValue) ?>">
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_EDIT || $master_mk->CurrentMode == "edit") { ?>
<input type="hidden" data-table="master_mk" data-field="x_MKID" name="x<?php echo $master_mk_grid->RowIndex ?>_MKID" id="x<?php echo $master_mk_grid->RowIndex ?>_MKID" value="<?php echo ew_HtmlEncode($master_mk->MKID->CurrentValue) ?>">
<?php } ?>
	<?php if ($master_mk->Nama->Visible) { // Nama ?>
		<td data-name="Nama"<?php echo $master_mk->Nama->CellAttributes() ?>>
<?php if ($master_mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Nama" class="form-group master_mk_Nama">
<input type="text" data-table="master_mk" data-field="x_Nama" name="x<?php echo $master_mk_grid->RowIndex ?>_Nama" id="x<?php echo $master_mk_grid->RowIndex ?>_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_mk->Nama->getPlaceHolder()) ?>" value="<?php echo $master_mk->Nama->EditValue ?>"<?php echo $master_mk->Nama->EditAttributes() ?>>
</span>
<input type="hidden" data-table="master_mk" data-field="x_Nama" name="o<?php echo $master_mk_grid->RowIndex ?>_Nama" id="o<?php echo $master_mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($master_mk->Nama->OldValue) ?>">
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Nama" class="form-group master_mk_Nama">
<input type="text" data-table="master_mk" data-field="x_Nama" name="x<?php echo $master_mk_grid->RowIndex ?>_Nama" id="x<?php echo $master_mk_grid->RowIndex ?>_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_mk->Nama->getPlaceHolder()) ?>" value="<?php echo $master_mk->Nama->EditValue ?>"<?php echo $master_mk->Nama->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Nama" class="master_mk_Nama">
<span<?php echo $master_mk->Nama->ViewAttributes() ?>>
<?php echo $master_mk->Nama->ListViewValue() ?></span>
</span>
<?php if ($master_mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="master_mk" data-field="x_Nama" name="x<?php echo $master_mk_grid->RowIndex ?>_Nama" id="x<?php echo $master_mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($master_mk->Nama->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_Nama" name="o<?php echo $master_mk_grid->RowIndex ?>_Nama" id="o<?php echo $master_mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($master_mk->Nama->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="master_mk" data-field="x_Nama" name="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_Nama" id="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($master_mk->Nama->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_Nama" name="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_Nama" id="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($master_mk->Nama->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($master_mk->Singkatan->Visible) { // Singkatan ?>
		<td data-name="Singkatan"<?php echo $master_mk->Singkatan->CellAttributes() ?>>
<?php if ($master_mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Singkatan" class="form-group master_mk_Singkatan">
<input type="text" data-table="master_mk" data-field="x_Singkatan" name="x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" id="x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->Singkatan->getPlaceHolder()) ?>" value="<?php echo $master_mk->Singkatan->EditValue ?>"<?php echo $master_mk->Singkatan->EditAttributes() ?>>
</span>
<input type="hidden" data-table="master_mk" data-field="x_Singkatan" name="o<?php echo $master_mk_grid->RowIndex ?>_Singkatan" id="o<?php echo $master_mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($master_mk->Singkatan->OldValue) ?>">
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Singkatan" class="form-group master_mk_Singkatan">
<input type="text" data-table="master_mk" data-field="x_Singkatan" name="x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" id="x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->Singkatan->getPlaceHolder()) ?>" value="<?php echo $master_mk->Singkatan->EditValue ?>"<?php echo $master_mk->Singkatan->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Singkatan" class="master_mk_Singkatan">
<span<?php echo $master_mk->Singkatan->ViewAttributes() ?>>
<?php echo $master_mk->Singkatan->ListViewValue() ?></span>
</span>
<?php if ($master_mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="master_mk" data-field="x_Singkatan" name="x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" id="x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($master_mk->Singkatan->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_Singkatan" name="o<?php echo $master_mk_grid->RowIndex ?>_Singkatan" id="o<?php echo $master_mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($master_mk->Singkatan->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="master_mk" data-field="x_Singkatan" name="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" id="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($master_mk->Singkatan->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_Singkatan" name="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_Singkatan" id="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($master_mk->Singkatan->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($master_mk->Tingkat->Visible) { // Tingkat ?>
		<td data-name="Tingkat"<?php echo $master_mk->Tingkat->CellAttributes() ?>>
<?php if ($master_mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Tingkat" class="form-group master_mk_Tingkat">
<select data-table="master_mk" data-field="x_Tingkat" data-value-separator="<?php echo $master_mk->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" name="x<?php echo $master_mk_grid->RowIndex ?>_Tingkat"<?php echo $master_mk->Tingkat->EditAttributes() ?>>
<?php echo $master_mk->Tingkat->SelectOptionListHtml("x<?php echo $master_mk_grid->RowIndex ?>_Tingkat") ?>
</select>
<input type="hidden" name="s_x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" id="s_x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" value="<?php echo $master_mk->Tingkat->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="master_mk" data-field="x_Tingkat" name="o<?php echo $master_mk_grid->RowIndex ?>_Tingkat" id="o<?php echo $master_mk_grid->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($master_mk->Tingkat->OldValue) ?>">
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Tingkat" class="form-group master_mk_Tingkat">
<select data-table="master_mk" data-field="x_Tingkat" data-value-separator="<?php echo $master_mk->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" name="x<?php echo $master_mk_grid->RowIndex ?>_Tingkat"<?php echo $master_mk->Tingkat->EditAttributes() ?>>
<?php echo $master_mk->Tingkat->SelectOptionListHtml("x<?php echo $master_mk_grid->RowIndex ?>_Tingkat") ?>
</select>
<input type="hidden" name="s_x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" id="s_x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" value="<?php echo $master_mk->Tingkat->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Tingkat" class="master_mk_Tingkat">
<span<?php echo $master_mk->Tingkat->ViewAttributes() ?>>
<?php echo $master_mk->Tingkat->ListViewValue() ?></span>
</span>
<?php if ($master_mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="master_mk" data-field="x_Tingkat" name="x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" id="x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($master_mk->Tingkat->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_Tingkat" name="o<?php echo $master_mk_grid->RowIndex ?>_Tingkat" id="o<?php echo $master_mk_grid->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($master_mk->Tingkat->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="master_mk" data-field="x_Tingkat" name="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" id="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($master_mk->Tingkat->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_Tingkat" name="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_Tingkat" id="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($master_mk->Tingkat->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($master_mk->Sesi->Visible) { // Sesi ?>
		<td data-name="Sesi"<?php echo $master_mk->Sesi->CellAttributes() ?>>
<?php if ($master_mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Sesi" class="form-group master_mk_Sesi">
<select data-table="master_mk" data-field="x_Sesi" data-value-separator="<?php echo $master_mk->Sesi->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $master_mk_grid->RowIndex ?>_Sesi" name="x<?php echo $master_mk_grid->RowIndex ?>_Sesi"<?php echo $master_mk->Sesi->EditAttributes() ?>>
<?php echo $master_mk->Sesi->SelectOptionListHtml("x<?php echo $master_mk_grid->RowIndex ?>_Sesi") ?>
</select>
<input type="hidden" name="s_x<?php echo $master_mk_grid->RowIndex ?>_Sesi" id="s_x<?php echo $master_mk_grid->RowIndex ?>_Sesi" value="<?php echo $master_mk->Sesi->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="master_mk" data-field="x_Sesi" name="o<?php echo $master_mk_grid->RowIndex ?>_Sesi" id="o<?php echo $master_mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($master_mk->Sesi->OldValue) ?>">
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Sesi" class="form-group master_mk_Sesi">
<select data-table="master_mk" data-field="x_Sesi" data-value-separator="<?php echo $master_mk->Sesi->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $master_mk_grid->RowIndex ?>_Sesi" name="x<?php echo $master_mk_grid->RowIndex ?>_Sesi"<?php echo $master_mk->Sesi->EditAttributes() ?>>
<?php echo $master_mk->Sesi->SelectOptionListHtml("x<?php echo $master_mk_grid->RowIndex ?>_Sesi") ?>
</select>
<input type="hidden" name="s_x<?php echo $master_mk_grid->RowIndex ?>_Sesi" id="s_x<?php echo $master_mk_grid->RowIndex ?>_Sesi" value="<?php echo $master_mk->Sesi->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Sesi" class="master_mk_Sesi">
<span<?php echo $master_mk->Sesi->ViewAttributes() ?>>
<?php echo $master_mk->Sesi->ListViewValue() ?></span>
</span>
<?php if ($master_mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="master_mk" data-field="x_Sesi" name="x<?php echo $master_mk_grid->RowIndex ?>_Sesi" id="x<?php echo $master_mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($master_mk->Sesi->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_Sesi" name="o<?php echo $master_mk_grid->RowIndex ?>_Sesi" id="o<?php echo $master_mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($master_mk->Sesi->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="master_mk" data-field="x_Sesi" name="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_Sesi" id="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($master_mk->Sesi->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_Sesi" name="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_Sesi" id="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($master_mk->Sesi->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($master_mk->Wajib->Visible) { // Wajib ?>
		<td data-name="Wajib"<?php echo $master_mk->Wajib->CellAttributes() ?>>
<?php if ($master_mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Wajib" class="form-group master_mk_Wajib">
<div id="tp_x<?php echo $master_mk_grid->RowIndex ?>_Wajib" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_Wajib" data-value-separator="<?php echo $master_mk->Wajib->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $master_mk_grid->RowIndex ?>_Wajib" id="x<?php echo $master_mk_grid->RowIndex ?>_Wajib" value="{value}"<?php echo $master_mk->Wajib->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $master_mk_grid->RowIndex ?>_Wajib" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->Wajib->RadioButtonListHtml(FALSE, "x{$master_mk_grid->RowIndex}_Wajib") ?>
</div></div>
</span>
<input type="hidden" data-table="master_mk" data-field="x_Wajib" name="o<?php echo $master_mk_grid->RowIndex ?>_Wajib" id="o<?php echo $master_mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($master_mk->Wajib->OldValue) ?>">
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Wajib" class="form-group master_mk_Wajib">
<div id="tp_x<?php echo $master_mk_grid->RowIndex ?>_Wajib" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_Wajib" data-value-separator="<?php echo $master_mk->Wajib->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $master_mk_grid->RowIndex ?>_Wajib" id="x<?php echo $master_mk_grid->RowIndex ?>_Wajib" value="{value}"<?php echo $master_mk->Wajib->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $master_mk_grid->RowIndex ?>_Wajib" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->Wajib->RadioButtonListHtml(FALSE, "x{$master_mk_grid->RowIndex}_Wajib") ?>
</div></div>
</span>
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_Wajib" class="master_mk_Wajib">
<span<?php echo $master_mk->Wajib->ViewAttributes() ?>>
<?php echo $master_mk->Wajib->ListViewValue() ?></span>
</span>
<?php if ($master_mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="master_mk" data-field="x_Wajib" name="x<?php echo $master_mk_grid->RowIndex ?>_Wajib" id="x<?php echo $master_mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($master_mk->Wajib->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_Wajib" name="o<?php echo $master_mk_grid->RowIndex ?>_Wajib" id="o<?php echo $master_mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($master_mk->Wajib->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="master_mk" data-field="x_Wajib" name="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_Wajib" id="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($master_mk->Wajib->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_Wajib" name="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_Wajib" id="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($master_mk->Wajib->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($master_mk->NA->Visible) { // NA ?>
		<td data-name="NA"<?php echo $master_mk->NA->CellAttributes() ?>>
<?php if ($master_mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_NA" class="form-group master_mk_NA">
<div id="tp_x<?php echo $master_mk_grid->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_NA" data-value-separator="<?php echo $master_mk->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $master_mk_grid->RowIndex ?>_NA" id="x<?php echo $master_mk_grid->RowIndex ?>_NA" value="{value}"<?php echo $master_mk->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $master_mk_grid->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->NA->RadioButtonListHtml(FALSE, "x{$master_mk_grid->RowIndex}_NA") ?>
</div></div>
</span>
<input type="hidden" data-table="master_mk" data-field="x_NA" name="o<?php echo $master_mk_grid->RowIndex ?>_NA" id="o<?php echo $master_mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($master_mk->NA->OldValue) ?>">
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_NA" class="form-group master_mk_NA">
<div id="tp_x<?php echo $master_mk_grid->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_NA" data-value-separator="<?php echo $master_mk->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $master_mk_grid->RowIndex ?>_NA" id="x<?php echo $master_mk_grid->RowIndex ?>_NA" value="{value}"<?php echo $master_mk->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $master_mk_grid->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->NA->RadioButtonListHtml(FALSE, "x{$master_mk_grid->RowIndex}_NA") ?>
</div></div>
</span>
<?php } ?>
<?php if ($master_mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $master_mk_grid->RowCnt ?>_master_mk_NA" class="master_mk_NA">
<span<?php echo $master_mk->NA->ViewAttributes() ?>>
<?php echo $master_mk->NA->ListViewValue() ?></span>
</span>
<?php if ($master_mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="master_mk" data-field="x_NA" name="x<?php echo $master_mk_grid->RowIndex ?>_NA" id="x<?php echo $master_mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($master_mk->NA->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_NA" name="o<?php echo $master_mk_grid->RowIndex ?>_NA" id="o<?php echo $master_mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($master_mk->NA->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="master_mk" data-field="x_NA" name="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_NA" id="fmaster_mkgrid$x<?php echo $master_mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($master_mk->NA->FormValue) ?>">
<input type="hidden" data-table="master_mk" data-field="x_NA" name="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_NA" id="fmaster_mkgrid$o<?php echo $master_mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($master_mk->NA->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$master_mk_grid->ListOptions->Render("body", "right", $master_mk_grid->RowCnt);
?>
	</tr>
<?php if ($master_mk->RowType == EW_ROWTYPE_ADD || $master_mk->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fmaster_mkgrid.UpdateOpts(<?php echo $master_mk_grid->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($master_mk->CurrentAction <> "gridadd" || $master_mk->CurrentMode == "copy")
		if (!$master_mk_grid->Recordset->EOF) $master_mk_grid->Recordset->MoveNext();
}
?>
<?php
	if ($master_mk->CurrentMode == "add" || $master_mk->CurrentMode == "copy" || $master_mk->CurrentMode == "edit") {
		$master_mk_grid->RowIndex = '$rowindex$';
		$master_mk_grid->LoadDefaultValues();

		// Set row properties
		$master_mk->ResetAttrs();
		$master_mk->RowAttrs = array_merge($master_mk->RowAttrs, array('data-rowindex'=>$master_mk_grid->RowIndex, 'id'=>'r0_master_mk', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($master_mk->RowAttrs["class"], "ewTemplate");
		$master_mk->RowType = EW_ROWTYPE_ADD;

		// Render row
		$master_mk_grid->RenderRow();

		// Render list options
		$master_mk_grid->RenderListOptions();
		$master_mk_grid->StartRowCnt = 0;
?>
	<tr<?php echo $master_mk->RowAttributes() ?>>
<?php

// Render list options (body, left)
$master_mk_grid->ListOptions->Render("body", "left", $master_mk_grid->RowIndex);
?>
	<?php if ($master_mk->MKKode->Visible) { // MKKode ?>
		<td data-name="MKKode">
<?php if ($master_mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_master_mk_MKKode" class="form-group master_mk_MKKode">
<input type="text" data-table="master_mk" data-field="x_MKKode" name="x<?php echo $master_mk_grid->RowIndex ?>_MKKode" id="x<?php echo $master_mk_grid->RowIndex ?>_MKKode" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->MKKode->getPlaceHolder()) ?>" value="<?php echo $master_mk->MKKode->EditValue ?>"<?php echo $master_mk->MKKode->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_master_mk_MKKode" class="form-group master_mk_MKKode">
<span<?php echo $master_mk->MKKode->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->MKKode->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="master_mk" data-field="x_MKKode" name="x<?php echo $master_mk_grid->RowIndex ?>_MKKode" id="x<?php echo $master_mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($master_mk->MKKode->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="master_mk" data-field="x_MKKode" name="o<?php echo $master_mk_grid->RowIndex ?>_MKKode" id="o<?php echo $master_mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($master_mk->MKKode->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($master_mk->Nama->Visible) { // Nama ?>
		<td data-name="Nama">
<?php if ($master_mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_master_mk_Nama" class="form-group master_mk_Nama">
<input type="text" data-table="master_mk" data-field="x_Nama" name="x<?php echo $master_mk_grid->RowIndex ?>_Nama" id="x<?php echo $master_mk_grid->RowIndex ?>_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($master_mk->Nama->getPlaceHolder()) ?>" value="<?php echo $master_mk->Nama->EditValue ?>"<?php echo $master_mk->Nama->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_master_mk_Nama" class="form-group master_mk_Nama">
<span<?php echo $master_mk->Nama->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->Nama->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="master_mk" data-field="x_Nama" name="x<?php echo $master_mk_grid->RowIndex ?>_Nama" id="x<?php echo $master_mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($master_mk->Nama->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="master_mk" data-field="x_Nama" name="o<?php echo $master_mk_grid->RowIndex ?>_Nama" id="o<?php echo $master_mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($master_mk->Nama->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($master_mk->Singkatan->Visible) { // Singkatan ?>
		<td data-name="Singkatan">
<?php if ($master_mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_master_mk_Singkatan" class="form-group master_mk_Singkatan">
<input type="text" data-table="master_mk" data-field="x_Singkatan" name="x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" id="x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($master_mk->Singkatan->getPlaceHolder()) ?>" value="<?php echo $master_mk->Singkatan->EditValue ?>"<?php echo $master_mk->Singkatan->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_master_mk_Singkatan" class="form-group master_mk_Singkatan">
<span<?php echo $master_mk->Singkatan->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->Singkatan->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="master_mk" data-field="x_Singkatan" name="x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" id="x<?php echo $master_mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($master_mk->Singkatan->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="master_mk" data-field="x_Singkatan" name="o<?php echo $master_mk_grid->RowIndex ?>_Singkatan" id="o<?php echo $master_mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($master_mk->Singkatan->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($master_mk->Tingkat->Visible) { // Tingkat ?>
		<td data-name="Tingkat">
<?php if ($master_mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_master_mk_Tingkat" class="form-group master_mk_Tingkat">
<select data-table="master_mk" data-field="x_Tingkat" data-value-separator="<?php echo $master_mk->Tingkat->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" name="x<?php echo $master_mk_grid->RowIndex ?>_Tingkat"<?php echo $master_mk->Tingkat->EditAttributes() ?>>
<?php echo $master_mk->Tingkat->SelectOptionListHtml("x<?php echo $master_mk_grid->RowIndex ?>_Tingkat") ?>
</select>
<input type="hidden" name="s_x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" id="s_x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" value="<?php echo $master_mk->Tingkat->LookupFilterQuery() ?>">
</span>
<?php } else { ?>
<span id="el$rowindex$_master_mk_Tingkat" class="form-group master_mk_Tingkat">
<span<?php echo $master_mk->Tingkat->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->Tingkat->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="master_mk" data-field="x_Tingkat" name="x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" id="x<?php echo $master_mk_grid->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($master_mk->Tingkat->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="master_mk" data-field="x_Tingkat" name="o<?php echo $master_mk_grid->RowIndex ?>_Tingkat" id="o<?php echo $master_mk_grid->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($master_mk->Tingkat->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($master_mk->Sesi->Visible) { // Sesi ?>
		<td data-name="Sesi">
<?php if ($master_mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_master_mk_Sesi" class="form-group master_mk_Sesi">
<select data-table="master_mk" data-field="x_Sesi" data-value-separator="<?php echo $master_mk->Sesi->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $master_mk_grid->RowIndex ?>_Sesi" name="x<?php echo $master_mk_grid->RowIndex ?>_Sesi"<?php echo $master_mk->Sesi->EditAttributes() ?>>
<?php echo $master_mk->Sesi->SelectOptionListHtml("x<?php echo $master_mk_grid->RowIndex ?>_Sesi") ?>
</select>
<input type="hidden" name="s_x<?php echo $master_mk_grid->RowIndex ?>_Sesi" id="s_x<?php echo $master_mk_grid->RowIndex ?>_Sesi" value="<?php echo $master_mk->Sesi->LookupFilterQuery() ?>">
</span>
<?php } else { ?>
<span id="el$rowindex$_master_mk_Sesi" class="form-group master_mk_Sesi">
<span<?php echo $master_mk->Sesi->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->Sesi->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="master_mk" data-field="x_Sesi" name="x<?php echo $master_mk_grid->RowIndex ?>_Sesi" id="x<?php echo $master_mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($master_mk->Sesi->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="master_mk" data-field="x_Sesi" name="o<?php echo $master_mk_grid->RowIndex ?>_Sesi" id="o<?php echo $master_mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($master_mk->Sesi->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($master_mk->Wajib->Visible) { // Wajib ?>
		<td data-name="Wajib">
<?php if ($master_mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_master_mk_Wajib" class="form-group master_mk_Wajib">
<div id="tp_x<?php echo $master_mk_grid->RowIndex ?>_Wajib" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_Wajib" data-value-separator="<?php echo $master_mk->Wajib->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $master_mk_grid->RowIndex ?>_Wajib" id="x<?php echo $master_mk_grid->RowIndex ?>_Wajib" value="{value}"<?php echo $master_mk->Wajib->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $master_mk_grid->RowIndex ?>_Wajib" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->Wajib->RadioButtonListHtml(FALSE, "x{$master_mk_grid->RowIndex}_Wajib") ?>
</div></div>
</span>
<?php } else { ?>
<span id="el$rowindex$_master_mk_Wajib" class="form-group master_mk_Wajib">
<span<?php echo $master_mk->Wajib->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->Wajib->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="master_mk" data-field="x_Wajib" name="x<?php echo $master_mk_grid->RowIndex ?>_Wajib" id="x<?php echo $master_mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($master_mk->Wajib->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="master_mk" data-field="x_Wajib" name="o<?php echo $master_mk_grid->RowIndex ?>_Wajib" id="o<?php echo $master_mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($master_mk->Wajib->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($master_mk->NA->Visible) { // NA ?>
		<td data-name="NA">
<?php if ($master_mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_master_mk_NA" class="form-group master_mk_NA">
<div id="tp_x<?php echo $master_mk_grid->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="master_mk" data-field="x_NA" data-value-separator="<?php echo $master_mk->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $master_mk_grid->RowIndex ?>_NA" id="x<?php echo $master_mk_grid->RowIndex ?>_NA" value="{value}"<?php echo $master_mk->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $master_mk_grid->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $master_mk->NA->RadioButtonListHtml(FALSE, "x{$master_mk_grid->RowIndex}_NA") ?>
</div></div>
</span>
<?php } else { ?>
<span id="el$rowindex$_master_mk_NA" class="form-group master_mk_NA">
<span<?php echo $master_mk->NA->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $master_mk->NA->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="master_mk" data-field="x_NA" name="x<?php echo $master_mk_grid->RowIndex ?>_NA" id="x<?php echo $master_mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($master_mk->NA->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="master_mk" data-field="x_NA" name="o<?php echo $master_mk_grid->RowIndex ?>_NA" id="o<?php echo $master_mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($master_mk->NA->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$master_mk_grid->ListOptions->Render("body", "right", $master_mk_grid->RowCnt);
?>
<script type="text/javascript">
fmaster_mkgrid.UpdateOpts(<?php echo $master_mk_grid->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php if ($master_mk->CurrentMode == "add" || $master_mk->CurrentMode == "copy") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $master_mk_grid->FormKeyCountName ?>" id="<?php echo $master_mk_grid->FormKeyCountName ?>" value="<?php echo $master_mk_grid->KeyCount ?>">
<?php echo $master_mk_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($master_mk->CurrentMode == "edit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $master_mk_grid->FormKeyCountName ?>" id="<?php echo $master_mk_grid->FormKeyCountName ?>" value="<?php echo $master_mk_grid->KeyCount ?>">
<?php echo $master_mk_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($master_mk->CurrentMode == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
<input type="hidden" name="detailpage" value="fmaster_mkgrid">
</div>
<?php

// Close recordset
if ($master_mk_grid->Recordset)
	$master_mk_grid->Recordset->Close();
?>
<?php if ($master_mk_grid->ShowOtherOptions) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php
	foreach ($master_mk_grid->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
<?php } ?>
</div>
</div>
<?php } ?>
<?php if ($master_mk_grid->TotalRecs == 0 && $master_mk->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($master_mk_grid->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($master_mk->Export == "") { ?>
<script type="text/javascript">
fmaster_mkgrid.Init();
</script>
<?php } ?>
<?php
$master_mk_grid->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php
$master_mk_grid->Page_Terminate();
?>
