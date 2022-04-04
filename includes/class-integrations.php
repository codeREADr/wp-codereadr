<?php
/**
 * Integrations class.
 *
 * @since 1.0.0
 *
 * @package CodeReadr
 */

namespace CodeReadr;

/**
 * Integrations class is for specifiying the allowed integrations for CodeReadr.
 *
 * @since 1.0.0
 */
class Integrations {

	/**
	 * Get all integrations.
	 *
	 * @since 1.0.0
	 *
	 * @return array All integrations
	 */
	public static function get_all_integrations() {
		return apply_filters(
			'codereadr_integrations',
			array()
		);
	}
}
