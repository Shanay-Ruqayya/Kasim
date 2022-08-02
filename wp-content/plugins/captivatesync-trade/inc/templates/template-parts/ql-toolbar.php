<!-- Create toolbar container -->
<div id="quilljs-toolbar">

	<!-- basic buttons -->
	<span class="ql-formats">
		<button class="ql-bold"></button>
		<button class="ql-italic"></button>
		<button class="ql-underline"></button>
		<button class="ql-strike"></button>
		<button class="ql-blockquote"></button>
	</span>

	<!-- font size dropdown -->
	<span class="ql-formats">
		<select class="ql-size">
			<option value="small"></option>
			<option selected></option>
			<option value="large"></option>
			<option value="huge"></option>
		</select>
	</span>

	<!-- heading buttons -->
	<span class="ql-formats">
		<button class="ql-header" value="1"></button>
		<button class="ql-header" value="2"></button>
	</span>

	<!-- list buttons and align dropdown -->
	<span class="ql-formats">
		<button class="ql-list" value="ordered"></button>
		<button class="ql-list" value="bullet"></button>
		<select class="ql-align">
			<option value="center"></option>
			<option value="right"></option>
			<option value="justify"></option>
		</select>
	</span>

	<!-- link button -->
	<span class="ql-formats">
		<button class="ql-link"></button>
		<button class="ql-clean"></button>
	</span>

	<!-- snippets button -->
	<span class="ql-formats">
		<button id="cfm-manage-snippets" data-toggle="modal" data-target="#cfm-snippets-modal" title="Manage your Snippets">
			<i class="fas fa-edit"></i>
		</button>

		<select id="cfm-snippets" class="ql-size">
			<option selected="selected" value="">Snippets</option>
		</select>
	</span>

</div>


<!-- Manage snippets modal -->
<div class="modal fade" id="cfm-snippets-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Manage Snippets</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">

				<div id="cfm-snippet-list" class="cfm-snippet-list pt-4 pb-4"></div>

				<div id="cfm-snippet-create" class="cfm-snippet-create hidden">
					<div class="row mb-4">
						<div class="col-sm-12">
							<label for="snippet_title">SNIPPET TITLE</label>
							<input type="text" class="form-control" id="snippet_title" name="snippet_title">
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<label for="snippet_content">YOUR SNIPPET</label>
							<div id="cfm-snippet-ql-editor"></div>
							<textarea id="snippet_content" name="snippet_content" class="hidden"></textarea>
						</div>
					</div>
				</div>

			</div>

			<div class="modal-footer">
				<div id="cfm-modal-footer-edit" class="hidden">
					<button id="cfm-snippet-cancel" type="button" class="btn btn-outline-secondary">Cancel</button>
					<button id="cfm-snippet-save" type="button" class="btn btn-outline-info">Save</button>
				</div>
				<div id="cfm-modal-footer-default">
					<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
				</div>
			</div>

			<input id="snippet_id" name="snippet_id" type="hidden">
		</div>
	</div>
</div>
<!-- /Manage snippets modal -->