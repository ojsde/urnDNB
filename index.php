<?php

/**
 * @defgroup plugins_pubIds_urnDNB
 */

/**
 * @file plugins/pubIds/urnDNB/index.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: September 26, 2012
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_pubIds_urnDNB
 * @brief Wrapper for DNB URN plugin.
 *
 */
require_once('URNDNBPubIdPlugin.inc.php');

return new URNDNBPubIdPlugin();

?>
