<?php

class ReduxFramework_reports {

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since ReduxFramework 3.0.4
	 */
	function __construct($field = array(), $value = '', $parent) {

		//parent::__construct( $parent->sections, $parent->args );
		$this->parent = $parent;
		$this->field = $field;
		$this->value = $value;
	}

	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since ReduxFramework 1.0.0
	 */
	function render() {
		echo '<style>#' . $this->parent->args['opt_name'] . '-' . $this->field['id'] . ' {padding: 0;}</style>';
		echo '</td></tr></table><table class="form-table no-border redux-group-table redux-raw-table" style="margin-top: -20px;"><tbody><tr><td>';

		$field_values = array();
		if (!empty($this->value['name'])) {
			foreach ((array) $this->value['name'] as $k => $v) {
				$field_values[$k] = array(
					'name' => $v,
					'field' => $this->value['field'][$k],
					'condition' => $this->value['condition'][$k],
					'criteria' => $this->value['criteria'][$k],
					'id' => $this->value['id'][$k],					'team_report_only' => $this->value['team_report_only'][$k],
				);
			}
		}

		echo '<table class="form-table form-report form-report-hidden">';
		echo '<tr valign="top"><th scope="row"><div class="redux_field_th">Report Name</div></th><td><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[name][]' . $this->field['name_suffix'] . '" value="" class="regular-text ' . $this->field['class'] . '" disabled="disabled" /></td></tr>';
		echo '<tr valign="top"><th scope="row"><div class="redux_field_th">Sphere Field</div></th><td><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[field][]' . $this->field['name_suffix'] . '" value="" class="regular-text ' . $this->field['class'] . '" disabled="disabled" /></td></tr>';
		echo '<tr valign="top"><th scope="row"><div class="redux_field_th">Condition</div></th><td><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[condition][]' . $this->field['name_suffix'] . '" value="" class="regular-text ' . $this->field['class'] . '" disabled="disabled" /></td></tr>';
		echo '<tr valign="top"><th scope="row"><div class="redux_field_th">Criteria</div></th><td><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[criteria][]' . $this->field['name_suffix'] . '" value="" class="regular-text ' . $this->field['class'] . '" disabled="disabled" /></td></tr>';
		echo '<tr valign="top" style="border: 0;"><th scope="row"><div class="redux_field_th">Participant Report ID</div></th><td><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[id][]' . $this->field['name_suffix'] . '" value="" class="regular-text ' . $this->field['class'] . '" disabled="disabled" /></td></tr>';				echo '<tr valign="top" style="border: 0;"><th scope="row"><div class="redux_field_th">Team Report Only</div></th><td><select id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[team_report_only][]' . $this->field['name_suffix'] . '" class="' . $this->field['class'] . '" disabled="disabled" ><option value="Yes">Yes</option><option value="No" selected>No</option></select></td></tr>';
		echo '<tr valign="top"><th scope="row"><div class="redux_field_th"></div></th><td><a href="#" class="button button-error report-remove">' . __( 'Remove Report', 'redux-framework' ) . '</a></td></tr>';
		echo '<tr valign="top"></tr>';
		echo '</table>';

		if (!empty($field_values)) {
			foreach ($field_values as $k => $value) {				echo '<table class="form-table form-report">';
				echo '<tr valign="top"><th scope="row"><div class="redux_field_th">Report Name</div></th><td><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[name][]' . $this->field['name_suffix'] . '" value="' . $value['name'] . '" class="regular-text ' . $this->field['class'] . '" /></td></tr>';
				echo '<tr valign="top"><th scope="row"><div class="redux_field_th">Sphere Field</div></th><td><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[field][]' . $this->field['name_suffix'] . '" value="' . $value['field'] . '" class="regular-text ' . $this->field['class'] . '" /></td></tr>';
				echo '<tr valign="top"><th scope="row"><div class="redux_field_th">Condition</div></th><td><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[condition][]' . $this->field['name_suffix'] . '" value="' . $value['condition'] . '" class="regular-text ' . $this->field['class'] . '" /></td></tr>';
				echo '<tr valign="top"><th scope="row"><div class="redux_field_th">Criteria</div></th><td><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[criteria][]' . $this->field['name_suffix'] . '" value="' . $value['criteria'] . '" class="regular-text ' . $this->field['class'] . '" /></td></tr>';
				echo '<tr valign="top" style="border: 0;"><th scope="row"><div class="redux_field_th">Participant Report ID</div></th><td><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[id][]' . $this->field['name_suffix'] . '" value="' . $value['id'] . '" class="regular-text ' . $this->field['class'] . '" /></td></tr>';								echo '<tr valign="top" style="border: 0;"><th scope="row"><div class="redux_field_th">Team Report Only</div></th><td><select id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[team_report_only][]' . $this->field['name_suffix'] . '" class="' . $this->field['class'] . '"><option value="Yes" '.($value['team_report_only']=="Yes"?"selected":"").'>Yes</option><option value="No" '.($value['team_report_only']=="No"?"selected":"").'>No</option></select></td></tr>';
				echo '<tr valign="top"><th scope="row"><div class="redux_field_th"></div></th><td><a href="#" class="button button-error report-remove">' . __( 'Remove Report', 'redux-framework' ) . '</a></td></tr>';
				echo '<tr valign="top"></tr>';
				echo '</table>';
			}
		}

		echo '<a href="#" class="button button-primary report-add" style="margin-top: 10px;">Add Another Report</a><br/>';
		echo '<a href="#" class="button button-primary empty-supporters" style="margin-top: 10px;">Empty Supporters</a> <span id="empty-supporters" style="line-height: 35px;"></span><br/>';
		echo '<a href="#" class="button button-primary sync-manually" style="margin-top: 10px;">Sync Manually</a> <span id="sync-manually" style="line-height: 35px;"></span><br/>';

		echo '</td></tr></table><table class="form-table no-border" style="margin-top: 0;"><tbody><tr style="border-bottom: 0;"><th></th><td>';
	}

	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since       1.0.0
	 * @access      public
	 * @return      void
	 */
	public function enqueue() {
		wp_enqueue_script(
			'redux-field-report-js',
			ReduxFramework::$_url . 'inc/fields/reports/field_reports.js',
			array( 'jquery' ),
			time(),
			true
		);
		wp_enqueue_style(
			'redux-field-report-css',
			ReduxFramework::$_url . 'inc/fields/reports/field_reports.css',
			time(),
			true
		);

	}

}
