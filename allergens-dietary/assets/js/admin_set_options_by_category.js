jQuery( document ).ready(
	function () {
		// WordPress does not allow the use of $ in js files without changes to some settings. Instead of doing that we add the jQuery command to a local variable.
		let $j = jQuery.noConflict();

		// set all options related to the clicked category to active or inactive
		$j( '.allergens-dietary-category' ).on(
			'click',
			function () {
				let cat   = this.name;
				let check = this.checked;
				if (check) {
					$j( '.' + cat ).prop( 'checked', true );
				} else {
					$j( '.' + cat ).prop( 'checked', false );
				}
			}
		);
	}
);