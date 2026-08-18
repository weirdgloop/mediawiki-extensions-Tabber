<?php

namespace Tabber;

use MediaWiki\Parser\Parser;
use MediaWiki\Parser\PPFrame;

/**
 * Tabber
 * Tabber Hooks Class
 *
 * @package Tabber
 * @author  Eric Fortin, Alexia E. Smith
 * @license GPL-3.0-only
 * @link    https://www.mediawiki.org/wiki/Extension:Tabber
 */
class TabberHooks {
	/**
	 * Sets up this extension's parser functions.
	 *
	 * @param Parser &$parser Parser object passed as a reference.
	 *
	 * @return bool true
	 */
	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setHook( "tabber", "Tabber\\TabberHooks::renderTabber" );

		return true;
	}

	/**
	 * Renders the necessary HTML for a <tabber> tag.
	 *
	 * @param string|null $input The input URL between the beginning and ending tags.
	 * @param array $args Array of attribute arguments on that beginning tag.
	 * @param Parser $parser Mediawiki Parser Object
	 * @param PPFrame $frame Mediawiki PPFrame Object
	 *
	 * @return string HTML
	 */
	public static function renderTabber( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModules( [ 'ext.Tabber' ] );

		$key = md5( $input ?? '' );
		$arr = explode( "|-|", $input ?? '' );
		$htmlTabs = '';
		foreach ( $arr as $tab ) {
			$htmlTabs .= self::buildTab( $tab, $parser, $frame );
		}

		$HTML = '<div id="tabber-' . $key . '" class="tabber">' . $htmlTabs . "</div>";

		return $HTML;
	}

	/**
	 * Build individual tab.
	 *
	 * @param string $tab Tab information
	 * @param Parser $parser Mediawiki Parser Object
	 * @param PPFrame $frame Mediawiki PPFrame Object
	 *
	 * @return string HTML
	 */
	private static function buildTab( $tab, Parser $parser, PPFrame $frame ) {
		$tab = trim( $tab );
		if ( empty( $tab ) ) {
			return $tab;
		}

		// Use array_pad to make sure at least 2 array values are always returned
		[ $tabName, $tabBody ] = array_pad( explode( '=', $tab, 2 ), 2, '' );

		$tabBody = $parser->recursiveTagParse( $tabBody, $frame );
		$tabName = $parser->getTargetLanguageConverter()->convert( $tabName );

		$tab = '
			<div class="tabbertab" data-title="' . htmlspecialchars( $tabName ) . '">
				<p>' . $tabBody . '</p>
			</div>';

		return $tab;
	}
}
