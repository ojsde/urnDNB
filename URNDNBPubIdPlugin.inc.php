<?php

/**
 * @file URNDNBPubIdPlugin.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: September 25, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.pubIds.urnDNB
 * @class URNDNBPubIdPlugin
 *
 * @brief DNB URN plugin class
 */


import('classes.plugins.PubIdPlugin');

class URNDNBPubIdPlugin extends PubIdPlugin {

	//
	// Implement template methods from PKPPlugin.
	//
	/**
	 * @see PubIdPlugin::register()
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		$this->addLocaleData();
		return $success;
	}

	/**
	 * @see PKPPlugin::getName()
	 */
	function getName() {
		return 'URNDNBPubIdPlugin';
	}

	/**
	 * @see PKPPlugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.pubIds.urnDNB.displayName');
	}

	/**
	 * @see PKPPlugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.pubIds.urnDNB.description');
	}

	/**
	 * @see PKPPlugin::getTemplatePath()
	 */
	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}

	//
	// Implement template methods from PubIdPlugin.
	//
	/**
	 * @see PubIdPlugin::getPubId()
	 */
	function getPubId(&$pubObject, $preview = false) {
		$urn = $pubObject->getStoredPubId($this->getPubIdType());
		if (!$urn && !$this->isExcluded($pubObject)) {
			// Determine the type of the publishing object
			$pubObjectType = $this->getPubObjectType($pubObject);
			// concerning DNB policy URNs can only be assigned to galleys
			// in the article main language
			if ($pubObjectType != 'Galley') return null;

			$galley = $pubObject;
			if (!$galley->isPdfGalley()) return null;

			// Get the journal id of the object

			// Retrieve the published article
			assert(is_a($pubObject, 'ArticleFile'));
			$articleDao = DAORegistry::getDAO('ArticleDAO');
			$article = $articleDao->getArticle($pubObject->getArticleId(), null, true);
			if (!$article) return null;
			//$articleLanguages = $article->getLanguage();
			$articleLanguages = array_map('trim', explode(';', $article->getLanguage()));
			$galleyLocale = $galley->getLocale();
			//if ($galley->getLocale() != $article->getLocale()) return null;
			if (AppLocale::getIso1FromLocale($galleyLocale) != $articleLanguages[0]) return null;

			// Now we can identify the journal
			$journalId = $article->getJournalId();

			// get the journal
			$journal = $this->getJournal($journalId);
			if (!$journal) return null;
			$journalId = $journal->getId();

			// Retrieve the issue
			assert(!is_null($article));
			$issueDao = DAORegistry::getDAO('IssueDAO');
			$issue = $issueDao->getIssueByArticleId($article->getId(), $journal->getId(), true);

			// Retrieve the URN prefix
			$urnPrefix = $this->getSetting($journal->getId(), 'urnDNBPrefix');
			if (empty($urnPrefix)) return null;

			// Generate the URN suffix
			$urnSuffixSetting = $this->getSetting($journal->getId(), 'urnDNBSuffix');
			switch ($urnSuffixSetting) {
				case 'customIdentifier':
					$urnSuffix = $pubObject->getData('urnDNBSuffix');
					if (!empty($urnSuffix)) {
						$urn = $urnPrefix . $urnSuffix;
					}
					break;

				case 'pattern':
					$suffixPattern = $this->getSetting($journal->getId(), "urnDNBGalleySuffixPattern");
					$urn = $urnPrefix . $suffixPattern;
					// %j - journal initials
					if ($journal->getLocalizedSetting('initials', $journal->getPrimaryLocale())) {
						$suffixPattern = String::regexp_replace('/%j/', String::strtolower($journal->getLocalizedSetting('initials', $journal->getPrimaryLocale())), $suffixPattern);
					}
					// %x - custom identifier
					if ($pubObject->getStoredPubId('publisher-id')) {
						$urnSuffix = String::regexp_replace('/%x/', $pubObject->getStoredPubId('publisher-id'), $suffixPattern);
					}
					if ($issue) {
						// %v - volume number
						$suffixPattern = String::regexp_replace('/%v/', $issue->getVolume(), $suffixPattern);
						// %i - issue number
						$suffixPattern = String::regexp_replace('/%i/', $issue->getNumber(), $suffixPattern);
						// %Y - year
						$suffixPattern = String::regexp_replace('/%Y/', $issue->getYear(), $suffixPattern);
					}
					// %a - article id
					$suffixPattern = String::regexp_replace('/%a/', $article->getId(), $suffixPattern);
					// %p - page number
					$suffixPattern = String::regexp_replace('/%p/', $article->getPages(), $suffixPattern);
					$suffixPattern = String::regexp_replace('/%P/', $this->_sanitizePages($article->getPages()), $suffixPattern);
					// %g - galley id
					$suffixPattern = String::regexp_replace('/%g/', $galley->getId(), $suffixPattern);

					$urn = $urnPrefix . $suffixPattern;
					$urn .= $this->_calculateCheckNo($urn);
					break;

				default:
					if ($journal->getLocalizedSetting('initials', $journal->getPrimaryLocale())) {
						$suffixPattern = String::strtolower($journal->getLocalizedSetting('initials', $journal->getPrimaryLocale()));
					} else {
						$suffixPattern = '%j';
					}
					if ($issue) {
						$suffixPattern .= '.v' . $issue->getVolume() . 'i' . $issue->getNumber();
					} else {
						$suffixPattern = '.v%vi%i';
					}
					$suffixPattern .= '.' . $article->getId();
					$suffixPattern .= '.g' . $galley->getId();

					$urn = $urnPrefix . $suffixPattern;
					$urn .= $this->_calculateCheckNo($urn);
			}

			if ($urn && !$preview) {
				$this->setStoredPubId($pubObject, $pubObjectType, $urn);
			}
		}
		return $urn;
	}

	/**
	 * @see PubIdPlugin::getPubIdType()
	 */
	function getPubIdType() {
		return 'other::urnDNB';
	}

	/**
	 * @see PubIdPlugin::getPubIdDisplayType()
	 */
	function getPubIdDisplayType() {
		return 'URN';
	}

	/**
	 * @see PubIdPlugin::getPubIdFullName()
	 */
	function getPubIdFullName() {
		return 'Uniform Resource Name';
	}

	/**
	 * @see PubIdPlugin::getResolvingURL()
	 */
	function getResolvingURL($journalId, $pubId) {
		$resolverURL = $this->getSetting($journalId, 'urnDNBResolver');
		return $resolverURL . $pubId;
	}

	/**
	 * @see PubIdPlugin::getFormFieldNames()
	 */
	function getFormFieldNames() {
		return array('urnDNBSuffix', 'excludeURNDNB');
	}

	/**
	 * @see PubIdPlugin::getExcludeFormFieldName()
	 */
	function getExcludeFormFieldName() {
		return 'excludeURNDNB';
	}

	/**
	 * @see PubIdPlugin::getDAOFieldNames()
	 */
	function getDAOFieldNames() {
		return array('pub-id::other::urnDNB');
	}

	/**
	 * @see PubIdPlugin::getPubIdMetadataFile()
	 */
	function getPubIdMetadataFile() {
		return $this->getTemplatePath().'urnSuffixEdit.tpl';
	}

	/**
	 * @see PubIdPlugin::getSettingsFormName()
	 */
	function getSettingsFormName() {
		return 'classes.form.URNDNBSettingsForm';
	}

	/**
	 * @see PubIdPlugin::verifyData()
	 */
	function verifyData($fieldName, $fieldValue, &$pubObject, $journalId, &$errorMsg) {
		if ($fieldName == 'urnDNBSuffix') {
			if (empty($fieldValue)) return true;

			// Construct the potential new URN with the posted suffix.
			$urnPrefix = $this->getSetting($journalId, 'urnDNBPrefix');
			if (empty($urnPrefix)) return true;
			$newURN = $urnPrefix . $fieldValue;
			$newURNWithoutCheckNo = substr($newURN, 0, -1);
			$newURNWithCheckNo = $newURNWithoutCheckNo . $this->_calculateCheckNo($newURNWithoutCheckNo);
			if ($newURN != $newURNWithCheckNo) {
				$errorMsg = __('plugins.pubIds.urnDNB.form.checkNoRequired');
				return false;
			}
			if(!$this->checkDuplicate($newURN, $pubObject, $journalId)) {
				$errorMsg = __('plugins.pubIds.urnDNB.form.customIdentifierNotUnique');
				return false;
			}
		}
		return true;
	}

	//
	// Private helper methods
	//
	/**
	 * Get the last, check number.
	 * Algorithm (s. http://www.persistent-identifier.de/?link=316):
	 *  every URN character is replaced with a number according to the conversion table,
	 *  every number is multiplied by it's position/index (beginning with 1),
	 *  the numbers' sum is calculated,
	 *  the sum is devided by the last number,
	 *  the last number of the quotient before the decimal point is the check number.
	 * @param $urn string
	 * @return string
	 */
	function _calculateCheckNo($urn) {
	    $urnLower = strtolower_codesafe($urn);

	    $conversionTable = array('9' => '41', '8' => '9', '7' => '8', '6' => '7', '5' => '6', '4' => '5', '3' => '4', '2' => '3', '1' => '2', '0' => '1', 'a' => '18', 'b' => '14', 'c' => '19', 'd' => '15', 'e' => '16', 'f' => '21', 'g' => '22', 'h' => '23', 'i' => '24', 'j' => '25', 'k' => '42', 'l' => '26', 'm' => '27', 'n' => '13', 'o' => '28', 'p' => '29', 'q' => '31', 'r' => '12', 's' => '32', 't' => '33', 'u' => '11', 'v' => '34', 'w' => '35', 'x' => '36', 'y' => '37', 'z' => '38', '-' => '39', ':' => '17', '_' => '43', '/' => '45', '.' => '47', '+' => '49');

	    $newURN = '';
	    for ($i = 0; $i < strlen($urnLower); $i++) {
	    	$char = $urnLower[$i];
	    	$newURN .= $conversionTable[$char];
	    }
	    $sum = 0;
	    for ($j = 1; $j <= strlen($newURN); $j++) {
		    $sum = $sum + ($newURN[$j-1] * $j);
	    }
	    $lastNumber = $newURN[strlen($newURN)-1];
	    $quot = $sum / $lastNumber;
	    $quotRound = floor($quot);
	    $quotString = (string)$quotRound;

	    return $quotString[strlen($quotString)-1];
	}
	
	
	/**
	 * Since pages is just a string it can be something like "pp 235 to 159" or use special
	 * characters like "–" (!= "-") wich are not allowed in URNs. So we sanitize the string.  
	 * @param <string> $str
	 * @return <string>
	 */
	private function _sanitizePages($str) {
		$str = trim($str);
		$str = preg_replace('#[^\d]+#', '-', $str); // replace all groups of non-digits by '-'
		$str = (substr($str, 0, 1) == '-') ? substr($str, 1) : $str; // remove frist '-' if present
		$str = (substr($str, -1, 1) == '-') ? substr($str, 0, strlen($str) -1) : $str; // remove last '-' if present
		return $str;
	}

}

?>
