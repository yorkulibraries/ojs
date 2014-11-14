<?php

/**
 * CustomTemplatePlugin.inc.php
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins
 *
 * Allows journals to have a customized subset of the Smarty templates.
 *
 * $Id: CustomTemplatePlugin.inc.php,v 1.5 2006/07/06 00:08:24 alec Exp $
 */

import('file.JournalFileManager');
import('classes.plugins.GenericPlugin');

class CustomTemplatePlugin extends GenericPlugin {
	/**
	 * Called as a plugin is registered to the registry
	 * @param @category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
	    // force disable this plugin forever
	    $this->disable();
		return false;
	}

	/**
	 * Get the name of this plugin. The name must be unique within
	 * its category.
	 * @return String name of plugin
	 */
	function getName() {
		return 'CustomTemplatePlugin';
	}

	function getDisplayName() {
		return Locale::translate('plugins.generic.customTemplate');
	}

	function getDescription() {
		return Locale::translate('plugins.generic.customTemplate.description');
	}


	/**
	 * Select an alternate template if one is available.
	 */
	function display($hookName, $args) {
		$this->import('CustomTemplateFileManager');
		$templateMgr =& $args[0];
		$template =& $args[1];
		$journal =& Request::getJournal();
		if (isset($journal)) {
			$fileMgr = &new CustomTemplateFileManager($journal);
			$fileMgr->makeSmartyDirs();
			$customTemplateDir = $fileMgr->getCustomTemplateDir();
			$customTemplate = $customTemplateDir . DIRECTORY_SEPARATOR . $template;
			if ($fileMgr->fileExists($customTemplate)) {
				// reconfigure Smarty on-the-fly
				$templateMgr->template_dir[0] = $customTemplateDir;
				$templateMgr->template_dir[1] = $customTemplateDir;
				$templateMgr->compile_dir = $fileMgr->getCustomCompileDir();
				$templateMgr->config_dir = $fileMgr->getCustomConfigDir();
				$templateMgr->cache_dir = $fileMgr->getCustomCacheDir();
			}
		}
		return false;
	}
	
	function getManagementVerbs() {
		$enabled = $this->getEnabled();
		$verbs = array();
		$verbs[] = array(
			($enabled?'disable':'enable'),
			Locale::translate($enabled?'manager.plugins.disable':'manager.plugins.enable')
		);
		return $verbs;
	}

	function manage($verb, $args) {
		$this->import('CustomTemplateFileManager');
		
		switch ($verb) {
			case 'enable':
				$this->enable();
				break;
			case 'disable':
				$this->disable();
				break;
		}
		return false;
	}
	
	function getEnabled() {
		$enabled = false;
		$journal =& Request::getJournal();
		if (isset($journal)) {
			$journalId = $journal->getJournalId();
			$enabled = $this->getSetting($journalId, 'enabled');
		}
		return $enabled;
	}
	
	function enable() {
		$journal = &Request::getJournal();
		if (isset($journal)) {
			$fileMgr = &new CustomTemplateFileManager($journal);
			$enable = $fileMgr->copyDefaultTemplates() && $fileMgr->makeSmartyDirs();
			$this->updateSetting($journal->getJournalId(), 'enabled', $enable);
		}
	}
	
	function disable() {
		$journal = &Request::getJournal();
		if (isset($journal)) {
			$this->updateSetting($journal->getJournalId(), 'enabled', false);
		}
	}
}

?>
