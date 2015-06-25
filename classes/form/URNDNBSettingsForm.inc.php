<?php

/**
 * @file plugins/pubIds/urnDNB/URNDNBSettingsForm.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: September 26, 2012
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class URNDNBSettingsForm
 * @ingroup plugins_pubIds_urnDNB
 *
 * @brief Form for journal managers to setup DNB URN plugin
 */


import('lib.pkp.classes.form.Form');

class URNDNBSettingsForm extends Form {

	//
	// Private properties
	//
	/** @var integer */
	var $_journalId;

	/** @var URNDNBPubIdPlugin */
	var $_plugin;

	//
	// Constructor
	//
	/**
	 * Constructor
	 * @param $plugin URNDNBPubIdPlugin
	 * @param $journalId integer
	 */
	function URNDNBSettingsForm(&$plugin, $journalId) {
		$this->_journalId = $journalId;
		$this->_plugin = $plugin;

		parent::Form($plugin->getTemplatePath() . 'settingsForm.tpl');

		$this->addCheck(new FormValidator($this, 'urnDNBPrefix', 'required', 'plugins.pubIds.urnDNB.manager.settings.form.urnPrefixRequired'));
		$this->addCheck(new FormValidatorRegExp($this, 'urnDNBPrefix', 'optional', 'plugins.pubIds.urnDNB.manager.settings.form.urnPrefixPattern', '/^urn:[a-zA-Z0-9-]*:.*/'));
		$this->addCheck(new FormValidatorCustom($this, 'urnDNBGalleySuffixPattern', 'required', 'plugins.pubIds.urnDNB.manager.settings.form.urnGalleySuffixPatternRequired', create_function('$urnDNBGalleySuffixPattern,$form', 'if ($form->getData(\'urnDNBSuffix\') == \'pattern\') return $urnDNBGalleySuffixPattern != \'\';return true;'), array(&$this)));
		$this->addCheck(new FormValidator($this, 'urnDNBNamespace', 'required', 'plugins.pubIds.urnDNB.manager.settings.form.namespaceRequired'));
		$this->addCheck(new FormValidatorUrl($this, 'urnDNBResolver', 'required', 'plugins.pubIds.urnDNB.manager.settings.form.urnResolverRequired'));
		$this->addCheck(new FormValidatorPost($this));
	}

	//
	// Implement template methods from Form
	//
	/**
	 * @see Form::display()
	 */
	function display() {
		$namespaces = array(
			'urn:nbn:de' => 'urn:nbn:de',
			'urn:nbn:at' => 'urn:nbn:at',
			'urn:nbn:ch' => 'urn:nbn:ch',
			'urn:nbn' => 'urn:nbn',
			'urn' => 'urn'
		);
		$templateMgr = TemplateManager::getManager();
		$templateMgr->assign('namespaces', $namespaces);
		parent::display();
	}

	/**
	 * @see Form::initData()
	 */
	function initData() {
		$journalId = $this->_journalId;
		$plugin = $this->_plugin;

		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$this->setData($fieldName, $plugin->getSetting($journalId, $fieldName));
		}
	}

	/**
	 * @see Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(array_keys($this->_getFormFields()));
	}

	/**
	 * @see Form::validate()
	 */
	function execute() {
		$plugin = $this->_plugin;
		$journalId = $this->_journalId;

		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$plugin->updateSetting($journalId, $fieldName, $this->getData($fieldName), $fieldType);
		}
	}

	//
	// Private helper methods
	//
	function _getFormFields() {
		return array(
			'urnDNBPrefix' => 'string',
			'urnDNBSuffix' => 'string',
			'urnDNBGalleySuffixPattern' => 'string',
			'urnDNBNamespace' => 'string',
			'urnDNBResolver' => 'string'
		);
	}
}

?>
