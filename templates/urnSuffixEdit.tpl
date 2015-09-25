{**
 * templates/urnSuffixEdit.tpl
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: September 25, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Edit custom DNB URN suffix for a galley object
 *
 *}
{assign var=pubObjectType value=$pubIdPlugin->getPubObjectType($pubObject)}
{if $pubObjectType == 'Galley' && $pubObject->isPdfGalley()}
	{assign var=articleDao value=$pubIdPlugin->getDAO('Article')}
	{assign var=article value=$articleDao->getArticle($articleId)}
	{assign var=galleyLang value=$pubObject->getLocale()|substr:0:2}
	{assign var=articleLang value=$article->getLanguage()|substr:0:2}
	{if $galleyLang == $articleLang}
		<script type="text/javascript">
			{literal}
			<!--
				function toggleURNDNBClear() {
					if ($('#excludeURNDNB').is(':checked')) {
						$('#clear_other_urnDNB').attr('checked', true);
						$('#clear_other_urnDNB').attr('disabled', true);
					} else {
						$('#clear_other_urnDNB').attr('disabled', false);
					}
				}
			// -->
			{/literal}
		</script>
		<!-- DNB URN -->
		<div id="pub-id::other::urnDNB">
		<h3>{translate key="plugins.pubIds.urnDNB.metadata"}</h3>

		{assign var=storedPubId value=$pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}
		{if !$excludeURNDNB}
			{assign var=urnSuffixMethod value=$pubIdPlugin->getSetting($currentJournal->getId(), 'urnDNBSuffix')}
			{if $urnSuffixMethod == 'customIdentifier' && !$storedPubId}
				{assign var=urnPrefix value=$pubIdPlugin->getSetting($currentJournal->getId(), 'urnDNBPrefix')}
				<table width="100%" class="data">
				<tr valign="top">
					<td rowspan="2" width="10%" class="label">{fieldLabel name="urnDNBSuffix" key="plugins.pubIds.urnDNB.urnSuffix"}</td>
					<td rowspan="2" width="10%" align="right">{$urnPrefix|escape}</td>
					<td width="80%" class="value"><input type="text" class="textField" name="urnDNBSuffix" id="urnDNBSuffix" value="{$urnDNBSuffix|escape}" size="20" maxlength="20" />
					<input type="button" name="urnDNBCheckNo" value="{translate key="plugins.pubIds.urnDNB.calculateCheckNo"}" class="button" onClick="javascript:calculateCheckNo('{$urnPrefix|escape}')"><script src="{$baseUrl}/plugins/pubIds/urnDNB/js/checkNumber.js" type="text/javascript"></script></td>
				</tr>
				<tr valign="top">
					<td colspan="3"><span class="instruct">{translate key="plugins.pubIds.urnDNB.urnSuffix.description"}</span></td>
				</tr>
				</table>
				</div>
			{elseif $storedPubId}
				<p>{$storedPubId|escape}</p>
				<input type="checkbox" name="clear_other_urnDNB" id="clear_other_urnDNB" value="1" />
				{translate key="plugins.pubIds.urnDNB.urnClear.description"}<br />
			{else}
				<p>{$pubIdPlugin->getPubId($pubObject, true)|escape}</p>
				{translate key="plugins.pubIds.urnDNB.urnNotYetGenerated" pubObjectType=$translatedObjectType}<br />
			{/if}
			<br />
		{/if}

		<input type="checkbox" name="excludeURNDNB" id="excludeURNDNB" value="1"{if $excludeURNDNB} checked="checked"{/if} onClick="toggleURNDNBClear()"  />
		{translate key="plugins.pubIds.urnDNB.excludePubObject"}<br />

		<div class="separator"></div>
		<!-- /DNB URN -->
	{/if}
{/if}

