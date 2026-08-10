( function ( $ ) {
	$.fn.tabber = function () {
		return this.each( function () {
			// create tabs
			const $this = $( this ),
				$tabContent = $this.children( '.tabbertab' ),
				$nav = $( '<ul>' ).addClass( 'tabbernav' );

			$tabContent.each( function () {
				const title = $( this ).data( 'title' );
				$( this ).attr( 'data-hash', mw.util.escapeIdForAttribute( title ) );
				const $anchor = $( '<a>' ).text( title ).attr( 'alt', title ).attr( 'data-hash', $( this ).attr( 'data-hash' ) ).attr( 'href', '#' );
				$( '<li>' ).append( $anchor ).appendTo( $nav );

				// Append a manual word break point after each tab
				$nav.append( $( '<wbr>' ) );
			} );

			$this.prepend( $nav );

			/**
			 * Internal helper function for showing content
			 *
			 * @param  {string} title to show, matching only 1 tab
			 * @return {boolean} true if matching tab could be shown
			 */
			function showContent( title ) {
				const $content = $tabContent.filter( '[data-hash="' + title + '"]' );
				if ( $content.length !== 1 ) {
					return false;
				}
				$tabContent.hide();
				$content.show();
				$nav.find( '.tabberactive' ).removeClass( 'tabberactive' );
				$nav.find( 'a[data-hash="' + title + '"]' ).parent().addClass( 'tabberactive' );
				return true;
			}

			// setup initial state
			const initialTab = new mw.Uri( location.href ).fragment;
			if ( initialTab === '' || !showContent( initialTab ) ) {
				showContent( $tabContent.first().attr( 'data-hash' ) );
			}

			// Respond to clicks on the nav tabs
			$nav.on( 'click', 'a', function ( e ) {
				const title = $( this ).attr( 'data-hash' );
				e.preventDefault();
				if ( history.replaceState ) {
					history.replaceState( null, null, '#' + title );
					switchTab();
				} else {
					location.hash = '#' + title;
				}
			} );

			$( window ).on( 'hashchange', () => {
				switchTab();
			} );

			function switchTab() {
				const tab = new mw.Uri( location.href ).fragment;
				if ( !tab.length ) {
					showContent( $tabContent.first().attr( 'data-hash' ) );
				}
				if ( $nav.find( 'a[data-hash="' + tab + '"]' ).length ) {
					showContent( tab );
				}
			}

			$this.addClass( 'tabberlive' );
		} );
	};
}( jQuery ) );

mw.hook( 'wikipage.content' ).add( ( $content ) => {
	$content.find( '.tabber:not(.tabberlive)' ).tabber();
} );
