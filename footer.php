<?php if (@$gsExport == "") { ?>
	<?php if (@!$gbSkipHeaderFooter) { ?>
		<!-- right column (end) -->
		<?php if (isset($gTimer)) $gTimer->Stop() ?>
		</div>
		</div>
		</div>
		<!-- content (end) -->
		<!-- footer (begin) -->
		<!-- ** Note: Only licensed users are allowed to remove or change the following copyright statement. ** -->
		<div id="ewFooterRow" class="ewFooterRow">
			<div class="ewFooterText"><?php echo $Language->ProjectPhrase("FooterText") ?></div>
			<!-- Place other links, for example, disclaimer, here -->
		</div>
		<!-- footer (end) -->
		</div>
	<?php } ?>
	<!-- modal dialog -->
	<div id="ewModalDialog" class="modal" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>
	<!-- modal lookup dialog -->
	<div id="ewModalLookupDialog" class="modal" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>
	<!-- message box -->
	<div id="ewMsgBox" class="modal" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body"></div>
				<div class="modal-footer"><button type="button" class="btn btn-primary ewButton" data-dismiss="modal"><?php echo $Language->Phrase("MessageOK") ?></button></div>
			</div>
		</div>
	</div>
	<!-- prompt -->
	<div id="ewPrompt" class="modal" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body"></div>
				<div class="modal-footer"><button type="button" class="btn btn-primary ewButton"><?php echo $Language->Phrase("MessageOK") ?></button><button type="button" class="btn btn-default ewButton" data-dismiss="modal"><?php echo $Language->Phrase("CancelBtn") ?></button></div>
			</div>
		</div>
	</div>
	<!-- session timer -->
	<div id="ewTimer" class="modal" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body"></div>
				<div class="modal-footer"><button type="button" class="btn btn-primary ewButton" data-dismiss="modal"><?php echo $Language->Phrase("MessageOK") ?></button></div>
			</div>
		</div>
	</div>
	<!-- tooltip -->
	<div id="ewTooltip"></div>
<?php } ?>
<?php if (@$gsExport == "") { ?>
	<script type="text/javascript">
		jQuery.get("<?php echo $EW_RELATIVE_PATH ?>phpjs/userevt13.js");
	</script>
	<script type="text/javascript">
		// Write your global startup script here
		// document.write("page loaded");

		<?php if (IsLoggedIn()) { ?>
			$(".ewSiteTitle").remove();
			$("div.ewHeaderRow").html("<?php echo "<div class='container-fluid'><br><div class='row'><div class='col-xs-10'><img align='center' src='" . $EW_RELATIVE_PATH . "phpimages/header.png' /></div><div class='col-xs-2'><div id='header_user' class='panel panel-info'><div class='panel-heading'>Selamat datang <strong></strong> | " . CurrentUserName() . "</div><div class='panel-body'>" . CurrentNama() . "<br>(" . CurrentLevelName() . ")</div></div></div></div></div>"; ?>");
			$(".ewAdvancedSearch").on("click", function() {
				$("#header_user").remove();
			});
		<?php } ?>
		$('title').prepend("<?php echo SetTitle() ?>");
	</script>
<?php } ?>
</body>

</html>