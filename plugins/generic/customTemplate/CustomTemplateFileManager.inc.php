<?php
/**
 * JournalFileManager.inc.php
 *
 * Copyright (c) 2003-2006 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package file
 *
 * Class defining operations for private journal file management.
 *
 * $Id: JournalFileManager.inc.php,v 1.2 2006/06/12 23:25:50 alec Exp $
 */

import('file.JournalFileManager');

class CustomTemplateFileManager extends JournalFileManager {
	
	/**
	 * Constructor.
	 * Create a manager for handling journal file uploads.
	 * @param $journal Journal
	 */
	function CustomTemplateFileManager(&$journal) {
		parent::JournalFileManager($journal);
	}
	
	function getCustomTemplateDir() {
		return $this->filesDir . DIRECTORY_SEPARATOR . 'templates';
	}
	
	function getCustomCompileDir() {
		return $this->getCustomFileCachePath() . DIRECTORY_SEPARATOR . 't_compile';
	}
	
	function getCustomConfigDir() {
		return $this->getCustomFileCachePath() . DIRECTORY_SEPARATOR . 't_config';
	}
	
	function getCustomCacheDir() {
		return $this->getCustomFileCachePath() . DIRECTORY_SEPARATOR . 't_cache';
	}
	
	function getCustomFileCachePath() {
		return CacheManager::getFileCachePath() . DIRECTORY_SEPARATOR . $this->journalId;
	}
	
	function getDefaultTemplateDir() {
		return Core::getBaseDir() . DIRECTORY_SEPARATOR . 'templates';
	}
	
	function copyDefaultTemplates() {
		$source = $this->getDefaultTemplateDir();
		$dest = $this->getCustomTemplateDir();
		if (!$this->fileExists($dest, 'dir')) {
			return $this->_copyr($source, $dest);
		}
		return true;
	}
	
	function makeSmartyDirs() {
		$compileDir = $this->getCustomCompileDir();
		$configDir = $this->getCustomConfigDir();
		$cacheDir = $this->getCustomCacheDir();
		if (!$this->fileExists($compileDir, 'dir')) {
			$success = $this->mkdirtree($compileDir);
			if (!$success) {
				return false;
			}
		}
		if (!$this->fileExists($configDir, 'dir')) {
			$success = $this->mkdirtree($configDir);
			if (!$success) {
				return false;
			}
		}
		if (!$this->fileExists($cacheDir, 'dir')) {
			$success = $this->mkdirtree($cacheDir);
			if (!$success) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Copy a file, or recursively copy a folder and its contents
	 */
	function _copyr($source, $dest)
	{
	    // Simple copy for a file
	    if (is_file($source)) {
	        return copy($source, $dest);
	    }
	 
	    // Make destination directory
	    if (!is_dir($dest)) {
			if (!mkdir($dest)) {
				return false;
			}
	    }
	 
	    // Loop through the folder
	    $dir = dir($source);
	    while (false !== $entry = $dir->read()) {
	        // Skip pointers
	        if ($entry == '.' || $entry == '..') {
	            continue;
	        }
	 
	        // Deep copy directories
	        if ($dest !== "$source/$entry") {
	            $success = $this->_copyr("$source/$entry", "$dest/$entry");
	            if (!$success) {
	            	$dir->close();
	            	return false;
	            }
	        }
	    }
	 
	    // Clean up
	    $dir->close();
	    return true;
	}
} 
?>
