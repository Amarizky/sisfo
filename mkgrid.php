<?php include_once "usersinfo.php" ?>
<?php

// Create page object
if (!isset($mk_grid)) $mk_grid = new cmk_grid();

// Page init
$mk_grid->Page_Init();

// Page main
$mk_grid->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$mk_grid->Page_Render();
?>
<?php if ($mk->Export == "") { ?>
<script type="text/javascript">

// Form object
var fmkgrid = new ew_Form("fmkgrid", "grid");
fmkgrid.FormKeyCountName = '<?php echo $mk_grid->FormKeyCountName ?>';

// Validate form
fmkgrid.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $mk->Nama->FldCaption(), $mk->Nama->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Tingkat[]");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $mk->Tingkat->FldCaption(), $mk->Tingkat->ReqErrMsg)) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
		} // End Grid Add checking
	}
	return true;
}

// Check empty row
fmkgrid.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "MKKode", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Nama", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Singkatan", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Tingkat[]", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Sesi", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Wajib", true)) return false;
	if (ew_ValueChanged(fobj, infix, "NA", true)) return false;
	return true;
}

// Form_CustomValidate event
fmkgrid.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmkgrid.ValidateRequired = true;
<?php } else { ?>
fmkgrid.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fmkgrid.Lists["x_Tingkat[]"] = {"LinkField":"x_Tingkat","Ajax":true,"AutoFill":false,"DisplayFields":["x_Tingkat","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"kelas"};
fmkgrid.Lists["x_Sesi"] = {"LinkField":"x_Sesi","Ajax":true,"AutoFill":false,"DisplayFields":["x_NamaSesi","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"master_sesi"};
fmkgrid.Lists["x_Wajib"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmkgrid.Lists["x_Wajib"].Options = <?php echo json_encode($mk->Wajib->Options()) ?>;
fmkgrid.Lists["x_NA"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fmkgrid.Lists["x_NA"].Options = <?php echo json_encode($mk->NA->Options()) ?>;

// Form object for search
</script>
<?php } ?>
<?php
if ($mk->CurrentAction == "gridadd") {
	if ($mk->CurrentMode == "copy") {
		$bSelectLimit = $mk_grid->UseSelectLimit;
		if ($bSelectLimit) {
			$mk_grid->TotalRecs = $mk->SelectRecordCount();
			$mk_grid->Recordset = $mk_grid->LoadRecordset($mk_grid->StartRec-1, $mk_grid->DisplayRecs);
		} else {
			if ($mk_grid->Recordset = $mk_grid->LoadRecordset())
				$mk_grid->TotalRecs = $mk_grid->Recordset->RecordCount();
		}
		$mk_grid->StartRec = 1;
		$mk_grid->DisplayRecs = $mk_grid->TotalRecs;
	} else {
		$mk->CurrentFilter = "0=1";
		$mk_grid->StartRec = 1;
		$mk_grid->DisplayRecs = $mk->GridAddRowCount;
	}
	$mk_grid->TotalRecs = $mk_grid->DisplayRecs;
	$mk_grid->StopRec = $mk_grid->DisplayRecs;
} else {
	$bSelectLimit = $mk_grid->UseSelectLimit;
	if ($bSelectLimit) {
		if ($mk_grid->TotalRecs <= 0)
			$mk_grid->TotalRecs = $mk->SelectRecordCount();
	} else {
		if (!$mk_grid->Recordset && ($mk_grid->Recordset = $mk_grid->LoadRecordset()))
			$mk_grid->TotalRecs = $mk_grid->Recordset->RecordCount();
	}
	$mk_grid->StartRec = 1;
	$mk_grid->DisplayRecs = $mk_grid->TotalRecs; // Display all records
	if ($bSelectLimit)
		$mk_grid->Recordset = $mk_grid->LoadRecordset($mk_grid->StartRec-1, $mk_grid->DisplayRecs);

	// Set no record found message
	if ($mk->CurrentAction == "" && $mk_grid->TotalRecs == 0) {
		if (!$Security->CanList())
			$mk_grid->setWarningMessage(ew_DeniedMsg());
		if ($mk_grid->SearchWhere == "0=101")
			$mk_grid->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$mk_grid->setWarningMessage($Language->Phrase("NoRecord"));
	}
}
$mk_grid->RenderOtherOptions();
?>
<?php $mk_grid->ShowPageHeader(); ?>
<?php
$mk_grid->ShowMessage();
?>
<?php if ($mk_grid->TotalRecs > 0 || $mk->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid mk">
<div id="fmkgrid" class="ewForm form-inline">
<?php if ($mk_grid->ShowOtherOptions) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php
	foreach ($mk_grid->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<div id="gmp_mk" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table id="tbl_mkgrid" class="table ewTable">
<?php echo $mk->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$mk_grid->RowType = EW_ROWTYPE_HEADER;

// Render list options
$mk_grid->RenderListOptions();

// Render list options (header, left)
$mk_grid->ListOptions->Render("header", "left");
?>
<?php if ($mk->MKKode->Visible) { // MKKode ?>
	<?php if ($mk->SortUrl($mk->MKKode) == "") { ?>
		<th data-name="MKKode"><div id="elh_mk_MKKode" class="mk_MKKode"><div class="ewTableHeaderCaption"><?php echo $mk->MKKode->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="MKKode"><div><div id="elh_mk_MKKode" class="mk_MKKode">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $mk->MKKode->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($mk->MKKode->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($mk->MKKode->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($mk->Nama->Visible) { // Nama ?>
	<?php if ($mk->SortUrl($mk->Nama) == "") { ?>
		<th data-name="Nama"><div id="elh_mk_Nama" class="mk_Nama"><div class="ewTableHeaderCaption"><?php echo $mk->Nama->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Nama"><div><div id="elh_mk_Nama" class="mk_Nama">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $mk->Nama->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($mk->Nama->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($mk->Nama->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($mk->Singkatan->Visible) { // Singkatan ?>
	<?php if ($mk->SortUrl($mk->Singkatan) == "") { ?>
		<th data-name="Singkatan"><div id="elh_mk_Singkatan" class="mk_Singkatan"><div class="ewTableHeaderCaption"><?php echo $mk->Singkatan->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Singkatan"><div><div id="elh_mk_Singkatan" class="mk_Singkatan">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $mk->Singkatan->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($mk->Singkatan->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($mk->Singkatan->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($mk->Tingkat->Visible) { // Tingkat ?>
	<?php if ($mk->SortUrl($mk->Tingkat) == "") { ?>
		<th data-name="Tingkat"><div id="elh_mk_Tingkat" class="mk_Tingkat"><div class="ewTableHeaderCaption"><?php echo $mk->Tingkat->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tingkat"><div><div id="elh_mk_Tingkat" class="mk_Tingkat">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $mk->Tingkat->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($mk->Tingkat->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($mk->Tingkat->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($mk->Sesi->Visible) { // Sesi ?>
	<?php if ($mk->SortUrl($mk->Sesi) == "") { ?>
		<th data-name="Sesi"><div id="elh_mk_Sesi" class="mk_Sesi"><div class="ewTableHeaderCaption"><?php echo $mk->Sesi->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Sesi"><div><div id="elh_mk_Sesi" class="mk_Sesi">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $mk->Sesi->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($mk->Sesi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($mk->Sesi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($mk->Wajib->Visible) { // Wajib ?>
	<?php if ($mk->SortUrl($mk->Wajib) == "") { ?>
		<th data-name="Wajib"><div id="elh_mk_Wajib" class="mk_Wajib"><div class="ewTableHeaderCaption"><?php echo $mk->Wajib->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Wajib"><div><div id="elh_mk_Wajib" class="mk_Wajib">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $mk->Wajib->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($mk->Wajib->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($mk->Wajib->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($mk->NA->Visible) { // NA ?>
	<?php if ($mk->SortUrl($mk->NA) == "") { ?>
		<th data-name="NA"><div id="elh_mk_NA" class="mk_NA"><div class="ewTableHeaderCaption"><?php echo $mk->NA->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="NA"><div><div id="elh_mk_NA" class="mk_NA">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $mk->NA->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($mk->NA->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($mk->NA->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$mk_grid->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
$mk_grid->StartRec = 1;
$mk_grid->StopRec = $mk_grid->TotalRecs; // Show all records

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($mk_grid->FormKeyCountName) && ($mk->CurrentAction == "gridadd" || $mk->CurrentAction == "gridedit" || $mk->CurrentAction == "F")) {
		$mk_grid->KeyCount = $objForm->GetValue($mk_grid->FormKeyCountName);
		$mk_grid->StopRec = $mk_grid->StartRec + $mk_grid->KeyCount - 1;
	}
}
$mk_grid->RecCnt = $mk_grid->StartRec - 1;
if ($mk_grid->Recordset && !$mk_grid->Recordset->EOF) {
	$mk_grid->Recordset->MoveFirst();
	$bSelectLimit = $mk_grid->UseSelectLimit;
	if (!$bSelectLimit && $mk_grid->StartRec > 1)
		$mk_grid->Recordset->Move($mk_grid->StartRec - 1);
} elseif (!$mk->AllowAddDeleteRow && $mk_grid->StopRec == 0) {
	$mk_grid->StopRec = $mk->GridAddRowCount;
}

// Initialize aggregate
$mk->RowType = EW_ROWTYPE_AGGREGATEINIT;
$mk->ResetAttrs();
$mk_grid->RenderRow();
if ($mk->CurrentAction == "gridadd")
	$mk_grid->RowIndex = 0;
if ($mk->CurrentAction == "gridedit")
	$mk_grid->RowIndex = 0;
while ($mk_grid->RecCnt < $mk_grid->StopRec) {
	$mk_grid->RecCnt++;
	if (intval($mk_grid->RecCnt) >= intval($mk_grid->StartRec)) {
		$mk_grid->RowCnt++;
		if ($mk->CurrentAction == "gridadd" || $mk->CurrentAction == "gridedit" || $mk->CurrentAction == "F") {
			$mk_grid->RowIndex++;
			$objForm->Index = $mk_grid->RowIndex;
			if ($objForm->HasValue($mk_grid->FormActionName))
				$mk_grid->RowAction = strval($objForm->GetValue($mk_grid->FormActionName));
			elseif ($mk->CurrentAction == "gridadd")
				$mk_grid->RowAction = "insert";
			else
				$mk_grid->RowAction = "";
		}

		// Set up key count
		$mk_grid->KeyCount = $mk_grid->RowIndex;

		// Init row class and style
		$mk->ResetAttrs();
		$mk->CssClass = "";
		if ($mk->CurrentAction == "gridadd") {
			if ($mk->CurrentMode == "copy") {
				$mk_grid->LoadRowValues($mk_grid->Recordset); // Load row values
				$mk_grid->SetRecordKey($mk_grid->RowOldKey, $mk_grid->Recordset); // Set old record key
			} else {
				$mk_grid->LoadDefaultValues(); // Load default values
				$mk_grid->RowOldKey = ""; // Clear old key value
			}
		} else {
			$mk_grid->LoadRowValues($mk_grid->Recordset); // Load row values
		}
		$mk->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($mk->CurrentAction == "gridadd") // Grid add
			$mk->RowType = EW_ROWTYPE_ADD; // Render add
		if ($mk->CurrentAction == "gridadd" && $mk->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$mk_grid->RestoreCurrentRowFormValues($mk_grid->RowIndex); // Restore form values
		if ($mk->CurrentAction == "gridedit") { // Grid edit
			if ($mk->EventCancelled) {
				$mk_grid->RestoreCurrentRowFormValues($mk_grid->RowIndex); // Restore form values
			}
			if ($mk_grid->RowAction == "insert")
				$mk->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$mk->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($mk->CurrentAction == "gridedit" && ($mk->RowType == EW_ROWTYPE_EDIT || $mk->RowType == EW_ROWTYPE_ADD) && $mk->EventCancelled) // Update failed
			$mk_grid->RestoreCurrentRowFormValues($mk_grid->RowIndex); // Restore form values
		if ($mk->RowType == EW_ROWTYPE_EDIT) // Edit row
			$mk_grid->EditRowCnt++;
		if ($mk->CurrentAction == "F") // Confirm row
			$mk_grid->RestoreCurrentRowFormValues($mk_grid->RowIndex); // Restore form values

		// Set up row id / data-rowindex
		$mk->RowAttrs = array_merge($mk->RowAttrs, array('data-rowindex'=>$mk_grid->RowCnt, 'id'=>'r' . $mk_grid->RowCnt . '_mk', 'data-rowtype'=>$mk->RowType));

		// Render row
		$mk_grid->RenderRow();

		// Render list options
		$mk_grid->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($mk_grid->RowAction <> "delete" && $mk_grid->RowAction <> "insertdelete" && !($mk_grid->RowAction == "insert" && $mk->CurrentAction == "F" && $mk_grid->EmptyRow())) {
?>
	<tr<?php echo $mk->RowAttributes() ?>>
<?php

// Render list options (body, left)
$mk_grid->ListOptions->Render("body", "left", $mk_grid->RowCnt);
?>
	<?php if ($mk->MKKode->Visible) { // MKKode ?>
		<td data-name="MKKode"<?php echo $mk->MKKode->CellAttributes() ?>>
<?php if ($mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_MKKode" class="form-group mk_MKKode">
<input type="text" data-table="mk" data-field="x_MKKode" name="x<?php echo $mk_grid->RowIndex ?>_MKKode" id="x<?php echo $mk_grid->RowIndex ?>_MKKode" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($mk->MKKode->getPlaceHolder()) ?>" value="<?php echo $mk->MKKode->EditValue ?>"<?php echo $mk->MKKode->EditAttributes() ?>>
</span>
<input type="hidden" data-table="mk" data-field="x_MKKode" name="o<?php echo $mk_grid->RowIndex ?>_MKKode" id="o<?php echo $mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($mk->MKKode->OldValue) ?>">
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_MKKode" class="form-group mk_MKKode">
<input type="text" data-table="mk" data-field="x_MKKode" name="x<?php echo $mk_grid->RowIndex ?>_MKKode" id="x<?php echo $mk_grid->RowIndex ?>_MKKode" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($mk->MKKode->getPlaceHolder()) ?>" value="<?php echo $mk->MKKode->EditValue ?>"<?php echo $mk->MKKode->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_MKKode" class="mk_MKKode">
<span<?php echo $mk->MKKode->ViewAttributes() ?>>
<?php echo $mk->MKKode->ListViewValue() ?></span>
</span>
<?php if ($mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="mk" data-field="x_MKKode" name="x<?php echo $mk_grid->RowIndex ?>_MKKode" id="x<?php echo $mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($mk->MKKode->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_MKKode" name="o<?php echo $mk_grid->RowIndex ?>_MKKode" id="o<?php echo $mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($mk->MKKode->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="mk" data-field="x_MKKode" name="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_MKKode" id="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($mk->MKKode->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_MKKode" name="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_MKKode" id="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($mk->MKKode->OldValue) ?>">
<?php } ?>
<?php } ?>
<a id="<?php echo $mk_grid->PageObjName . "_row_" . $mk_grid->RowCnt ?>"></a></td>
	<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-table="mk" data-field="x_MKID" name="x<?php echo $mk_grid->RowIndex ?>_MKID" id="x<?php echo $mk_grid->RowIndex ?>_MKID" value="<?php echo ew_HtmlEncode($mk->MKID->CurrentValue) ?>">
<input type="hidden" data-table="mk" data-field="x_MKID" name="o<?php echo $mk_grid->RowIndex ?>_MKID" id="o<?php echo $mk_grid->RowIndex ?>_MKID" value="<?php echo ew_HtmlEncode($mk->MKID->OldValue) ?>">
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_EDIT || $mk->CurrentMode == "edit") { ?>
<input type="hidden" data-table="mk" data-field="x_MKID" name="x<?php echo $mk_grid->RowIndex ?>_MKID" id="x<?php echo $mk_grid->RowIndex ?>_MKID" value="<?php echo ew_HtmlEncode($mk->MKID->CurrentValue) ?>">
<?php } ?>
	<?php if ($mk->Nama->Visible) { // Nama ?>
		<td data-name="Nama"<?php echo $mk->Nama->CellAttributes() ?>>
<?php if ($mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Nama" class="form-group mk_Nama">
<input type="text" data-table="mk" data-field="x_Nama" name="x<?php echo $mk_grid->RowIndex ?>_Nama" id="x<?php echo $mk_grid->RowIndex ?>_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($mk->Nama->getPlaceHolder()) ?>" value="<?php echo $mk->Nama->EditValue ?>"<?php echo $mk->Nama->EditAttributes() ?>>
</span>
<input type="hidden" data-table="mk" data-field="x_Nama" name="o<?php echo $mk_grid->RowIndex ?>_Nama" id="o<?php echo $mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($mk->Nama->OldValue) ?>">
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Nama" class="form-group mk_Nama">
<input type="text" data-table="mk" data-field="x_Nama" name="x<?php echo $mk_grid->RowIndex ?>_Nama" id="x<?php echo $mk_grid->RowIndex ?>_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($mk->Nama->getPlaceHolder()) ?>" value="<?php echo $mk->Nama->EditValue ?>"<?php echo $mk->Nama->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Nama" class="mk_Nama">
<span<?php echo $mk->Nama->ViewAttributes() ?>>
<?php echo $mk->Nama->ListViewValue() ?></span>
</span>
<?php if ($mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="mk" data-field="x_Nama" name="x<?php echo $mk_grid->RowIndex ?>_Nama" id="x<?php echo $mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($mk->Nama->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_Nama" name="o<?php echo $mk_grid->RowIndex ?>_Nama" id="o<?php echo $mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($mk->Nama->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="mk" data-field="x_Nama" name="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_Nama" id="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($mk->Nama->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_Nama" name="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_Nama" id="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($mk->Nama->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($mk->Singkatan->Visible) { // Singkatan ?>
		<td data-name="Singkatan"<?php echo $mk->Singkatan->CellAttributes() ?>>
<?php if ($mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Singkatan" class="form-group mk_Singkatan">
<input type="text" data-table="mk" data-field="x_Singkatan" name="x<?php echo $mk_grid->RowIndex ?>_Singkatan" id="x<?php echo $mk_grid->RowIndex ?>_Singkatan" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($mk->Singkatan->getPlaceHolder()) ?>" value="<?php echo $mk->Singkatan->EditValue ?>"<?php echo $mk->Singkatan->EditAttributes() ?>>
</span>
<input type="hidden" data-table="mk" data-field="x_Singkatan" name="o<?php echo $mk_grid->RowIndex ?>_Singkatan" id="o<?php echo $mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($mk->Singkatan->OldValue) ?>">
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Singkatan" class="form-group mk_Singkatan">
<input type="text" data-table="mk" data-field="x_Singkatan" name="x<?php echo $mk_grid->RowIndex ?>_Singkatan" id="x<?php echo $mk_grid->RowIndex ?>_Singkatan" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($mk->Singkatan->getPlaceHolder()) ?>" value="<?php echo $mk->Singkatan->EditValue ?>"<?php echo $mk->Singkatan->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Singkatan" class="mk_Singkatan">
<span<?php echo $mk->Singkatan->ViewAttributes() ?>>
<?php echo $mk->Singkatan->ListViewValue() ?></span>
</span>
<?php if ($mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="mk" data-field="x_Singkatan" name="x<?php echo $mk_grid->RowIndex ?>_Singkatan" id="x<?php echo $mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($mk->Singkatan->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_Singkatan" name="o<?php echo $mk_grid->RowIndex ?>_Singkatan" id="o<?php echo $mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($mk->Singkatan->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="mk" data-field="x_Singkatan" name="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_Singkatan" id="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($mk->Singkatan->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_Singkatan" name="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_Singkatan" id="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($mk->Singkatan->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($mk->Tingkat->Visible) { // Tingkat ?>
		<td data-name="Tingkat"<?php echo $mk->Tingkat->CellAttributes() ?>>
<?php if ($mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Tingkat" class="form-group mk_Tingkat">
<div id="tp_x<?php echo $mk_grid->RowIndex ?>_Tingkat" class="ewTemplate"><input type="checkbox" data-table="mk" data-field="x_Tingkat" data-value-separator="<?php echo $mk->Tingkat->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $mk_grid->RowIndex ?>_Tingkat[]" id="x<?php echo $mk_grid->RowIndex ?>_Tingkat[]" value="{value}"<?php echo $mk->Tingkat->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $mk_grid->RowIndex ?>_Tingkat" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $mk->Tingkat->CheckBoxListHtml(FALSE, "x{$mk_grid->RowIndex}_Tingkat[]") ?>
</div></div>
<input type="hidden" name="s_x<?php echo $mk_grid->RowIndex ?>_Tingkat" id="s_x<?php echo $mk_grid->RowIndex ?>_Tingkat" value="<?php echo $mk->Tingkat->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="mk" data-field="x_Tingkat" name="o<?php echo $mk_grid->RowIndex ?>_Tingkat[]" id="o<?php echo $mk_grid->RowIndex ?>_Tingkat[]" value="<?php echo ew_HtmlEncode($mk->Tingkat->OldValue) ?>">
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Tingkat" class="form-group mk_Tingkat">
<div id="tp_x<?php echo $mk_grid->RowIndex ?>_Tingkat" class="ewTemplate"><input type="checkbox" data-table="mk" data-field="x_Tingkat" data-value-separator="<?php echo $mk->Tingkat->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $mk_grid->RowIndex ?>_Tingkat[]" id="x<?php echo $mk_grid->RowIndex ?>_Tingkat[]" value="{value}"<?php echo $mk->Tingkat->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $mk_grid->RowIndex ?>_Tingkat" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $mk->Tingkat->CheckBoxListHtml(FALSE, "x{$mk_grid->RowIndex}_Tingkat[]") ?>
</div></div>
<input type="hidden" name="s_x<?php echo $mk_grid->RowIndex ?>_Tingkat" id="s_x<?php echo $mk_grid->RowIndex ?>_Tingkat" value="<?php echo $mk->Tingkat->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Tingkat" class="mk_Tingkat">
<span<?php echo $mk->Tingkat->ViewAttributes() ?>>
<?php echo $mk->Tingkat->ListViewValue() ?></span>
</span>
<?php if ($mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="mk" data-field="x_Tingkat" name="x<?php echo $mk_grid->RowIndex ?>_Tingkat" id="x<?php echo $mk_grid->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($mk->Tingkat->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_Tingkat" name="o<?php echo $mk_grid->RowIndex ?>_Tingkat[]" id="o<?php echo $mk_grid->RowIndex ?>_Tingkat[]" value="<?php echo ew_HtmlEncode($mk->Tingkat->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="mk" data-field="x_Tingkat" name="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_Tingkat" id="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($mk->Tingkat->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_Tingkat" name="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_Tingkat[]" id="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_Tingkat[]" value="<?php echo ew_HtmlEncode($mk->Tingkat->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($mk->Sesi->Visible) { // Sesi ?>
		<td data-name="Sesi"<?php echo $mk->Sesi->CellAttributes() ?>>
<?php if ($mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Sesi" class="form-group mk_Sesi">
<select data-table="mk" data-field="x_Sesi" data-value-separator="<?php echo $mk->Sesi->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $mk_grid->RowIndex ?>_Sesi" name="x<?php echo $mk_grid->RowIndex ?>_Sesi"<?php echo $mk->Sesi->EditAttributes() ?>>
<?php echo $mk->Sesi->SelectOptionListHtml("x<?php echo $mk_grid->RowIndex ?>_Sesi") ?>
</select>
<input type="hidden" name="s_x<?php echo $mk_grid->RowIndex ?>_Sesi" id="s_x<?php echo $mk_grid->RowIndex ?>_Sesi" value="<?php echo $mk->Sesi->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="mk" data-field="x_Sesi" name="o<?php echo $mk_grid->RowIndex ?>_Sesi" id="o<?php echo $mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($mk->Sesi->OldValue) ?>">
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Sesi" class="form-group mk_Sesi">
<select data-table="mk" data-field="x_Sesi" data-value-separator="<?php echo $mk->Sesi->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $mk_grid->RowIndex ?>_Sesi" name="x<?php echo $mk_grid->RowIndex ?>_Sesi"<?php echo $mk->Sesi->EditAttributes() ?>>
<?php echo $mk->Sesi->SelectOptionListHtml("x<?php echo $mk_grid->RowIndex ?>_Sesi") ?>
</select>
<input type="hidden" name="s_x<?php echo $mk_grid->RowIndex ?>_Sesi" id="s_x<?php echo $mk_grid->RowIndex ?>_Sesi" value="<?php echo $mk->Sesi->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Sesi" class="mk_Sesi">
<span<?php echo $mk->Sesi->ViewAttributes() ?>>
<?php echo $mk->Sesi->ListViewValue() ?></span>
</span>
<?php if ($mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="mk" data-field="x_Sesi" name="x<?php echo $mk_grid->RowIndex ?>_Sesi" id="x<?php echo $mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($mk->Sesi->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_Sesi" name="o<?php echo $mk_grid->RowIndex ?>_Sesi" id="o<?php echo $mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($mk->Sesi->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="mk" data-field="x_Sesi" name="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_Sesi" id="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($mk->Sesi->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_Sesi" name="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_Sesi" id="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($mk->Sesi->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($mk->Wajib->Visible) { // Wajib ?>
		<td data-name="Wajib"<?php echo $mk->Wajib->CellAttributes() ?>>
<?php if ($mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Wajib" class="form-group mk_Wajib">
<div id="tp_x<?php echo $mk_grid->RowIndex ?>_Wajib" class="ewTemplate"><input type="radio" data-table="mk" data-field="x_Wajib" data-value-separator="<?php echo $mk->Wajib->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $mk_grid->RowIndex ?>_Wajib" id="x<?php echo $mk_grid->RowIndex ?>_Wajib" value="{value}"<?php echo $mk->Wajib->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $mk_grid->RowIndex ?>_Wajib" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $mk->Wajib->RadioButtonListHtml(FALSE, "x{$mk_grid->RowIndex}_Wajib") ?>
</div></div>
</span>
<input type="hidden" data-table="mk" data-field="x_Wajib" name="o<?php echo $mk_grid->RowIndex ?>_Wajib" id="o<?php echo $mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($mk->Wajib->OldValue) ?>">
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Wajib" class="form-group mk_Wajib">
<div id="tp_x<?php echo $mk_grid->RowIndex ?>_Wajib" class="ewTemplate"><input type="radio" data-table="mk" data-field="x_Wajib" data-value-separator="<?php echo $mk->Wajib->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $mk_grid->RowIndex ?>_Wajib" id="x<?php echo $mk_grid->RowIndex ?>_Wajib" value="{value}"<?php echo $mk->Wajib->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $mk_grid->RowIndex ?>_Wajib" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $mk->Wajib->RadioButtonListHtml(FALSE, "x{$mk_grid->RowIndex}_Wajib") ?>
</div></div>
</span>
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_Wajib" class="mk_Wajib">
<span<?php echo $mk->Wajib->ViewAttributes() ?>>
<?php echo $mk->Wajib->ListViewValue() ?></span>
</span>
<?php if ($mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="mk" data-field="x_Wajib" name="x<?php echo $mk_grid->RowIndex ?>_Wajib" id="x<?php echo $mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($mk->Wajib->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_Wajib" name="o<?php echo $mk_grid->RowIndex ?>_Wajib" id="o<?php echo $mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($mk->Wajib->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="mk" data-field="x_Wajib" name="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_Wajib" id="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($mk->Wajib->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_Wajib" name="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_Wajib" id="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($mk->Wajib->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($mk->NA->Visible) { // NA ?>
		<td data-name="NA"<?php echo $mk->NA->CellAttributes() ?>>
<?php if ($mk->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_NA" class="form-group mk_NA">
<div id="tp_x<?php echo $mk_grid->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="mk" data-field="x_NA" data-value-separator="<?php echo $mk->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $mk_grid->RowIndex ?>_NA" id="x<?php echo $mk_grid->RowIndex ?>_NA" value="{value}"<?php echo $mk->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $mk_grid->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $mk->NA->RadioButtonListHtml(FALSE, "x{$mk_grid->RowIndex}_NA") ?>
</div></div>
</span>
<input type="hidden" data-table="mk" data-field="x_NA" name="o<?php echo $mk_grid->RowIndex ?>_NA" id="o<?php echo $mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($mk->NA->OldValue) ?>">
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_NA" class="form-group mk_NA">
<div id="tp_x<?php echo $mk_grid->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="mk" data-field="x_NA" data-value-separator="<?php echo $mk->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $mk_grid->RowIndex ?>_NA" id="x<?php echo $mk_grid->RowIndex ?>_NA" value="{value}"<?php echo $mk->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $mk_grid->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $mk->NA->RadioButtonListHtml(FALSE, "x{$mk_grid->RowIndex}_NA") ?>
</div></div>
</span>
<?php } ?>
<?php if ($mk->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $mk_grid->RowCnt ?>_mk_NA" class="mk_NA">
<span<?php echo $mk->NA->ViewAttributes() ?>>
<?php echo $mk->NA->ListViewValue() ?></span>
</span>
<?php if ($mk->CurrentAction <> "F") { ?>
<input type="hidden" data-table="mk" data-field="x_NA" name="x<?php echo $mk_grid->RowIndex ?>_NA" id="x<?php echo $mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($mk->NA->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_NA" name="o<?php echo $mk_grid->RowIndex ?>_NA" id="o<?php echo $mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($mk->NA->OldValue) ?>">
<?php } else { ?>
<input type="hidden" data-table="mk" data-field="x_NA" name="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_NA" id="fmkgrid$x<?php echo $mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($mk->NA->FormValue) ?>">
<input type="hidden" data-table="mk" data-field="x_NA" name="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_NA" id="fmkgrid$o<?php echo $mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($mk->NA->OldValue) ?>">
<?php } ?>
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$mk_grid->ListOptions->Render("body", "right", $mk_grid->RowCnt);
?>
	</tr>
<?php if ($mk->RowType == EW_ROWTYPE_ADD || $mk->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fmkgrid.UpdateOpts(<?php echo $mk_grid->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($mk->CurrentAction <> "gridadd" || $mk->CurrentMode == "copy")
		if (!$mk_grid->Recordset->EOF) $mk_grid->Recordset->MoveNext();
}
?>
<?php
	if ($mk->CurrentMode == "add" || $mk->CurrentMode == "copy" || $mk->CurrentMode == "edit") {
		$mk_grid->RowIndex = '$rowindex$';
		$mk_grid->LoadDefaultValues();

		// Set row properties
		$mk->ResetAttrs();
		$mk->RowAttrs = array_merge($mk->RowAttrs, array('data-rowindex'=>$mk_grid->RowIndex, 'id'=>'r0_mk', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($mk->RowAttrs["class"], "ewTemplate");
		$mk->RowType = EW_ROWTYPE_ADD;

		// Render row
		$mk_grid->RenderRow();

		// Render list options
		$mk_grid->RenderListOptions();
		$mk_grid->StartRowCnt = 0;
?>
	<tr<?php echo $mk->RowAttributes() ?>>
<?php

// Render list options (body, left)
$mk_grid->ListOptions->Render("body", "left", $mk_grid->RowIndex);
?>
	<?php if ($mk->MKKode->Visible) { // MKKode ?>
		<td data-name="MKKode">
<?php if ($mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_mk_MKKode" class="form-group mk_MKKode">
<input type="text" data-table="mk" data-field="x_MKKode" name="x<?php echo $mk_grid->RowIndex ?>_MKKode" id="x<?php echo $mk_grid->RowIndex ?>_MKKode" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($mk->MKKode->getPlaceHolder()) ?>" value="<?php echo $mk->MKKode->EditValue ?>"<?php echo $mk->MKKode->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_mk_MKKode" class="form-group mk_MKKode">
<span<?php echo $mk->MKKode->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $mk->MKKode->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="mk" data-field="x_MKKode" name="x<?php echo $mk_grid->RowIndex ?>_MKKode" id="x<?php echo $mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($mk->MKKode->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="mk" data-field="x_MKKode" name="o<?php echo $mk_grid->RowIndex ?>_MKKode" id="o<?php echo $mk_grid->RowIndex ?>_MKKode" value="<?php echo ew_HtmlEncode($mk->MKKode->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($mk->Nama->Visible) { // Nama ?>
		<td data-name="Nama">
<?php if ($mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_mk_Nama" class="form-group mk_Nama">
<input type="text" data-table="mk" data-field="x_Nama" name="x<?php echo $mk_grid->RowIndex ?>_Nama" id="x<?php echo $mk_grid->RowIndex ?>_Nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($mk->Nama->getPlaceHolder()) ?>" value="<?php echo $mk->Nama->EditValue ?>"<?php echo $mk->Nama->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_mk_Nama" class="form-group mk_Nama">
<span<?php echo $mk->Nama->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $mk->Nama->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="mk" data-field="x_Nama" name="x<?php echo $mk_grid->RowIndex ?>_Nama" id="x<?php echo $mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($mk->Nama->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="mk" data-field="x_Nama" name="o<?php echo $mk_grid->RowIndex ?>_Nama" id="o<?php echo $mk_grid->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($mk->Nama->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($mk->Singkatan->Visible) { // Singkatan ?>
		<td data-name="Singkatan">
<?php if ($mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_mk_Singkatan" class="form-group mk_Singkatan">
<input type="text" data-table="mk" data-field="x_Singkatan" name="x<?php echo $mk_grid->RowIndex ?>_Singkatan" id="x<?php echo $mk_grid->RowIndex ?>_Singkatan" size="7" maxlength="20" placeholder="<?php echo ew_HtmlEncode($mk->Singkatan->getPlaceHolder()) ?>" value="<?php echo $mk->Singkatan->EditValue ?>"<?php echo $mk->Singkatan->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_mk_Singkatan" class="form-group mk_Singkatan">
<span<?php echo $mk->Singkatan->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $mk->Singkatan->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="mk" data-field="x_Singkatan" name="x<?php echo $mk_grid->RowIndex ?>_Singkatan" id="x<?php echo $mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($mk->Singkatan->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="mk" data-field="x_Singkatan" name="o<?php echo $mk_grid->RowIndex ?>_Singkatan" id="o<?php echo $mk_grid->RowIndex ?>_Singkatan" value="<?php echo ew_HtmlEncode($mk->Singkatan->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($mk->Tingkat->Visible) { // Tingkat ?>
		<td data-name="Tingkat">
<?php if ($mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_mk_Tingkat" class="form-group mk_Tingkat">
<div id="tp_x<?php echo $mk_grid->RowIndex ?>_Tingkat" class="ewTemplate"><input type="checkbox" data-table="mk" data-field="x_Tingkat" data-value-separator="<?php echo $mk->Tingkat->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $mk_grid->RowIndex ?>_Tingkat[]" id="x<?php echo $mk_grid->RowIndex ?>_Tingkat[]" value="{value}"<?php echo $mk->Tingkat->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $mk_grid->RowIndex ?>_Tingkat" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $mk->Tingkat->CheckBoxListHtml(FALSE, "x{$mk_grid->RowIndex}_Tingkat[]") ?>
</div></div>
<input type="hidden" name="s_x<?php echo $mk_grid->RowIndex ?>_Tingkat" id="s_x<?php echo $mk_grid->RowIndex ?>_Tingkat" value="<?php echo $mk->Tingkat->LookupFilterQuery() ?>">
</span>
<?php } else { ?>
<span id="el$rowindex$_mk_Tingkat" class="form-group mk_Tingkat">
<span<?php echo $mk->Tingkat->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $mk->Tingkat->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="mk" data-field="x_Tingkat" name="x<?php echo $mk_grid->RowIndex ?>_Tingkat" id="x<?php echo $mk_grid->RowIndex ?>_Tingkat" value="<?php echo ew_HtmlEncode($mk->Tingkat->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="mk" data-field="x_Tingkat" name="o<?php echo $mk_grid->RowIndex ?>_Tingkat[]" id="o<?php echo $mk_grid->RowIndex ?>_Tingkat[]" value="<?php echo ew_HtmlEncode($mk->Tingkat->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($mk->Sesi->Visible) { // Sesi ?>
		<td data-name="Sesi">
<?php if ($mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_mk_Sesi" class="form-group mk_Sesi">
<select data-table="mk" data-field="x_Sesi" data-value-separator="<?php echo $mk->Sesi->DisplayValueSeparatorAttribute() ?>" id="x<?php echo $mk_grid->RowIndex ?>_Sesi" name="x<?php echo $mk_grid->RowIndex ?>_Sesi"<?php echo $mk->Sesi->EditAttributes() ?>>
<?php echo $mk->Sesi->SelectOptionListHtml("x<?php echo $mk_grid->RowIndex ?>_Sesi") ?>
</select>
<input type="hidden" name="s_x<?php echo $mk_grid->RowIndex ?>_Sesi" id="s_x<?php echo $mk_grid->RowIndex ?>_Sesi" value="<?php echo $mk->Sesi->LookupFilterQuery() ?>">
</span>
<?php } else { ?>
<span id="el$rowindex$_mk_Sesi" class="form-group mk_Sesi">
<span<?php echo $mk->Sesi->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $mk->Sesi->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="mk" data-field="x_Sesi" name="x<?php echo $mk_grid->RowIndex ?>_Sesi" id="x<?php echo $mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($mk->Sesi->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="mk" data-field="x_Sesi" name="o<?php echo $mk_grid->RowIndex ?>_Sesi" id="o<?php echo $mk_grid->RowIndex ?>_Sesi" value="<?php echo ew_HtmlEncode($mk->Sesi->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($mk->Wajib->Visible) { // Wajib ?>
		<td data-name="Wajib">
<?php if ($mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_mk_Wajib" class="form-group mk_Wajib">
<div id="tp_x<?php echo $mk_grid->RowIndex ?>_Wajib" class="ewTemplate"><input type="radio" data-table="mk" data-field="x_Wajib" data-value-separator="<?php echo $mk->Wajib->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $mk_grid->RowIndex ?>_Wajib" id="x<?php echo $mk_grid->RowIndex ?>_Wajib" value="{value}"<?php echo $mk->Wajib->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $mk_grid->RowIndex ?>_Wajib" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $mk->Wajib->RadioButtonListHtml(FALSE, "x{$mk_grid->RowIndex}_Wajib") ?>
</div></div>
</span>
<?php } else { ?>
<span id="el$rowindex$_mk_Wajib" class="form-group mk_Wajib">
<span<?php echo $mk->Wajib->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $mk->Wajib->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="mk" data-field="x_Wajib" name="x<?php echo $mk_grid->RowIndex ?>_Wajib" id="x<?php echo $mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($mk->Wajib->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="mk" data-field="x_Wajib" name="o<?php echo $mk_grid->RowIndex ?>_Wajib" id="o<?php echo $mk_grid->RowIndex ?>_Wajib" value="<?php echo ew_HtmlEncode($mk->Wajib->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($mk->NA->Visible) { // NA ?>
		<td data-name="NA">
<?php if ($mk->CurrentAction <> "F") { ?>
<span id="el$rowindex$_mk_NA" class="form-group mk_NA">
<div id="tp_x<?php echo $mk_grid->RowIndex ?>_NA" class="ewTemplate"><input type="radio" data-table="mk" data-field="x_NA" data-value-separator="<?php echo $mk->NA->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $mk_grid->RowIndex ?>_NA" id="x<?php echo $mk_grid->RowIndex ?>_NA" value="{value}"<?php echo $mk->NA->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $mk_grid->RowIndex ?>_NA" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $mk->NA->RadioButtonListHtml(FALSE, "x{$mk_grid->RowIndex}_NA") ?>
</div></div>
</span>
<?php } else { ?>
<span id="el$rowindex$_mk_NA" class="form-group mk_NA">
<span<?php echo $mk->NA->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $mk->NA->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="mk" data-field="x_NA" name="x<?php echo $mk_grid->RowIndex ?>_NA" id="x<?php echo $mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($mk->NA->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="mk" data-field="x_NA" name="o<?php echo $mk_grid->RowIndex ?>_NA" id="o<?php echo $mk_grid->RowIndex ?>_NA" value="<?php echo ew_HtmlEncode($mk->NA->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$mk_grid->ListOptions->Render("body", "right", $mk_grid->RowCnt);
?>
<script type="text/javascript">
fmkgrid.UpdateOpts(<?php echo $mk_grid->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php if ($mk->CurrentMode == "add" || $mk->CurrentMode == "copy") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $mk_grid->FormKeyCountName ?>" id="<?php echo $mk_grid->FormKeyCountName ?>" value="<?php echo $mk_grid->KeyCount ?>">
<?php echo $mk_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($mk->CurrentMode == "edit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $mk_grid->FormKeyCountName ?>" id="<?php echo $mk_grid->FormKeyCountName ?>" value="<?php echo $mk_grid->KeyCount ?>">
<?php echo $mk_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($mk->CurrentMode == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
<input type="hidden" name="detailpage" value="fmkgrid">
</div>
<?php

// Close recordset
if ($mk_grid->Recordset)
	$mk_grid->Recordset->Close();
?>
<?php if ($mk_grid->ShowOtherOptions) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php
	foreach ($mk_grid->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
<?php } ?>
</div>
</div>
<?php } ?>
<?php if ($mk_grid->TotalRecs == 0 && $mk->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($mk_grid->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($mk->Export == "") { ?>
<script type="text/javascript">
fmkgrid.Init();
</script>
<?php } ?>
<?php
$mk_grid->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php
$mk_grid->Page_Terminate();
?>
