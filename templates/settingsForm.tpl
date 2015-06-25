{**
 * plugins/pubIds/urnDNB/templates/settingsForm.tpl
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: June 15, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * DNB URN plugin settings
 *
 *}
{strip}
{assign var="pageTitle" value="plugins.pubIds.urnDNB.manager.settings.urnSettings"}
{include file="common/header.tpl"}
{/strip}
<div id="urnDNBSettings">
<div id="description">{translate key="plugins.pubIds.urnDNB.manager.settings.description"}</div>

<div class="separator"></div>

<br />

<form method="post" action="{plugin_url path="settings"}">
{include file="common/formErrors.tpl"}
<table width="100%" class="data">
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="urnDNBPrefix" required="true" key="plugins.pubIds.urnDNB.manager.settings.urnPrefix"}</td>
		<td width="80%" class="value"><input type="text" name="urnDNBPrefix" value="{$urnDNBPrefix|escape}" size="40" maxlength="40" id="urnDNBPrefix" class="textField" />
		<br />
		<span class="instruct">{translate key="plugins.pubIds.urnDNB.manager.settings.urnPrefix.description"}</span>
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="urnDNBSuffix" key="plugins.pubIds.urnDNB.manager.settings.urnSuffix"}</td>
		<td width="80%" class="value">
			<table width="100%" class="data">
				<tr>
					<td width="5%" class="label" align="right" valign="top">
						<input type="radio" name="urnDNBSuffix" id="urnDNBSuffixPattern" value="pattern" {if $urnDNBSuffix eq "pattern"}checked{/if} />
					</td>
					<td width="95%" class="value">
						{fieldLabel name="urnDNBSuffixPattern" key="plugins.pubIds.urnDNB.manager.settings.urnSuffix.pattern"}
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="text" name="urnDNBGalleySuffixPattern" value="{$urnDNBGalleySuffixPattern|escape}" size="15" maxlength="50" id="urnDNBGalleySuffixPattern" class="textField" />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<span class="instruct">{translate key="plugins.pubIds.urnDNB.manager.settings.urnSuffix.patternExample"}</span>
					</td>
				</tr>
				<tr>
					<td width="5%" class="label" align="right" valign="top">
						<input type="radio" name="urnDNBSuffix" id="urnDNBSuffixDefault" value="default" {if ($urnDNBSuffix neq "pattern" && $urnDNBSuffix neq "customIdentifier")}checked{/if} />
					</td>
					<td width="95%" class="value">
						{fieldLabel name="urnDNBSuffixDefault" key="plugins.pubIds.urnDNB.manager.settings.urnSuffix.default"}
					</td>
				</tr>
				<tr>
					<td width="5%" class="label" align="right" valign="top">
						<input type="radio" name="urnDNBSuffix" id="urnDNBSuffixCustomIdentifier" value="customIdentifier" {if $urnDNBSuffix eq "customIdentifier"}checked{/if} />
					</td>
					<td width="95%" class="value">
						{fieldLabel name="urnDNBSuffixCustomIdentifier" key="plugins.pubIds.urnDNB.manager.settings.urnSuffix.customIdentifier"}
					</td>
				</tr>
			</table>
			<br />
			<span class="instruct">{translate key="plugins.pubIds.urnDNB.manager.settings.urnSuffix.description"}</span>
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="urnDNBNamespace" required="true" key="plugins.pubIds.urnDNB.manager.settings.namespace"}</td>
		<td class="value">
			<select name="urnDNBNamespace" id="urnDNBNamespace" class="selectMenu">
				<option value="">{translate key="plugins.pubIds.urnDNB.manager.settings.namespace.choose"}</option>
				{html_options options=$namespaces selected=$urnDNBNamespace}
			</select>
			<br />
			<span class="instruct">{translate key="plugins.pubIds.urnDNB.manager.settings.namespace.description"}</span>
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="urnDNBResolver" required="true" key="plugins.pubIds.urnDNB.manager.settings.urnResolver"}</td>
		<td width="80%" class="value"><input type="text" name="urnDNBResolver" value="{$urnDNBResolver|escape|default:'http://nbn-resolving.de/'}" size="40" maxlength="255" id="urnDNBResolver" class="textField" />
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr valign="top">
		<td class="label">&nbsp;</td>
		<td class="value">
			<span class="instruct">{translate key="plugins.pubIds.urnDNB.manager.settings.clearURNs.description"}</span>
			<br />
			<input type="submit" name="clearPubIds" value="{translate key="plugins.pubIds.urnDNB.manager.settings.clearURNs"}" onclick="return confirm('{translate|escape:"jsparam" key="plugins.pubIds.urnDNB.manager.settings.clearURNs.confirm"}')" class="action"/>
		</td>
	</tr>
</table>

<br/>

<input type="submit" name="save" class="button defaultButton" value="{translate key="common.save"}"/><input type="button" class="button" value="{translate key="common.cancel"}" onclick="history.go(-1)"/>
</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</div>
{include file="common/footer.tpl"}
