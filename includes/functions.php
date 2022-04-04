<?php
/**
 * Useful functions.
 *
 * @package CodeReadr
 */

use CodeReadr\Logger;
use CodeReadr\Interfaces\Logger_Interface;

/**
 * function xml2array
 *
 * This function is part of the PHP manual.
 *
 * The PHP manual text and comments are covered by the Creative Commons
 * Attribution 3.0 License, copyright (c) the PHP Documentation Group
 *
 * @author  k dot antczak at livedata dot pl
 * @date    2011-04-22 06:08 UTC
 * @link    http://www.php.net/manual/en/ref.simplexml.php#103617
 * @license http://www.php.net/license/index.php#doc-lic
 * @license http://creativecommons.org/licenses/by/3.0/
 * @license CC-BY-3.0 <http://spdx.org/licenses/CC-BY-3.0>
 */
function codereadr_xml2array( $xmlObject, $out = array() ) {
	foreach ( (array) $xmlObject as $index => $node ) {
		$out[ $index ] = ( is_object( $node ) ) ? codereadr_xml2array( $node ) : $node;
	}

	return $out;
}
/**
 * Get a shared logger instance.
 * This function is forked from Woocommerce
 *
 * Use the quillforms_logging_class filter to change the logging class. You may provide one of the following:
 *     - a class name which will be instantiated as `new $class` with no arguments
 *     - an instance which will be used directly as the logger
 * In either case, the class or instance *must* implement Logger_Interface.
 *
 * @since 1.0.0
 * @see Logger_Interface
 *
 * @return Logger
 */
function codereadr_get_logger() {
	static $logger = null;

	$class = apply_filters( 'codereadr_logging_class', Logger::class );

	if ( null !== $logger && is_string( $class ) && is_a( $logger, $class ) ) {
		return $logger;
	}

	$implements = class_implements( $class );

	if ( is_array( $implements ) && in_array( Logger_Interface::class, $implements, true ) ) {
		$logger = is_object( $class ) ? $class : new $class();
	} else {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: class name 2: quillforms_logging_class 3: Logger_Interface */
				__( 'The class %1$s provided by %2$s filter must implement %3$s.', 'codereadr' ),
				'<code>' . esc_html( is_object( $class ) ? get_class( $class ) : $class ) . '</code>',
				'<code>codereadr_logging_class</code>',
				'<code>Logger_Interface</code>'
			),
			'1.0.0'
		);

		$logger = is_a( $logger, Logger::class ) ? $logger : new Logger();
	}

	return $logger;
}
