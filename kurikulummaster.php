<?php

// ProdiID
// KurikulumKode
// Nama
// JmlSesi
// NA

?>
<?php if ($kurikulum->Visible) { ?>
<!-- <h4 class="ewMasterCaption"><?php echo $kurikulum->TableCaption() ?></h4> -->
<table id="tbl_kurikulummaster" class="table table-bordered table-striped ewViewTable">
<?php echo $kurikulum->TableCustomInnerHtml ?>
	<tbody>
<?php if ($kurikulum->ProdiID->Visible) { // ProdiID ?>
		<tr id="r_ProdiID">
			<td><?php echo $kurikulum->ProdiID->FldCaption() ?></td>
			<td<?php echo $kurikulum->ProdiID->CellAttributes() ?>>
<span id="el_kurikulum_ProdiID">
<span<?php echo $kurikulum->ProdiID->ViewAttributes() ?>>
<?php echo $kurikulum->ProdiID->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($kurikulum->KurikulumKode->Visible) { // KurikulumKode ?>
		<tr id="r_KurikulumKode">
			<td><?php echo $kurikulum->KurikulumKode->FldCaption() ?></td>
			<td<?php echo $kurikulum->KurikulumKode->CellAttributes() ?>>
<span id="el_kurikulum_KurikulumKode">
<span<?php echo $kurikulum->KurikulumKode->ViewAttributes() ?>>
<?php echo $kurikulum->KurikulumKode->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($kurikulum->Nama->Visible) { // Nama ?>
		<tr id="r_Nama">
			<td><?php echo $kurikulum->Nama->FldCaption() ?></td>
			<td<?php echo $kurikulum->Nama->CellAttributes() ?>>
<span id="el_kurikulum_Nama">
<span<?php echo $kurikulum->Nama->ViewAttributes() ?>>
<?php echo $kurikulum->Nama->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($kurikulum->JmlSesi->Visible) { // JmlSesi ?>
		<tr id="r_JmlSesi">
			<td><?php echo $kurikulum->JmlSesi->FldCaption() ?></td>
			<td<?php echo $kurikulum->JmlSesi->CellAttributes() ?>>
<span id="el_kurikulum_JmlSesi">
<span<?php echo $kurikulum->JmlSesi->ViewAttributes() ?>>
<?php echo $kurikulum->JmlSesi->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($kurikulum->NA->Visible) { // NA ?>
		<tr id="r_NA">
			<td><?php echo $kurikulum->NA->FldCaption() ?></td>
			<td<?php echo $kurikulum->NA->CellAttributes() ?>>
<span id="el_kurikulum_NA">
<span<?php echo $kurikulum->NA->ViewAttributes() ?>>
<?php echo $kurikulum->NA->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
	</tbody>
</table>
<?php } ?>
