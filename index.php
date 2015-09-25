<?php

/**
 * @file index.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: September 25, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.pubIds.urnDNB
 *
 * @brief Wrapper for DNB URN plugin.
 *
 */

require_once('URNDNBPubIdPlugin.inc.php');
return new URNDNBPubIdPlugin();

?>
