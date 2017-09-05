<?php
/**
 * Portal64.de Client
 * Extension for Contao Open Source CMS, Copyright (C) Leo Feyer
 *
 * Copyright (C) 2017 Julian Knorr
 *
 * @package			Portal64.de Client
 * @author			Julian Knorr
 * @copytight		Copyright (C) 2017 Julian Knorr
 * @date			2017
 */

namespace Portal64;

/**
 * Provide methods to run automated jobs for portal64.
 *
 * @author Julian Knorr
 */
class Portal64Automator extends \System
{
	/**
	 * Update most outdated team of latest term
	 */
	public function updateFromPortal64()
	{
		if (!$GLOBALS['TL_CONFIG']['portal64DisableCron']) {
			$manager = new Portal64Manager();
			$manager->updateNextTeam(null, true);
		}
	}
}

?>
