<?php

/**
 * @file tools/keepOneJournal.php
 *
 * Copyright (c) 2003-2012 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class JournalDeletionTool
 * @ingroup tools
 *
 * @brief CLI tool to delete submissions
 */

// $Id$


require(dirname(__FILE__) . '/bootstrap.inc.php');

class JournalDeletionTool extends CommandLineTool {

	/**
	 * Constructor.
	 * @param $argv array command-line arguments
	 */
	function JournalDeletionTool($argv = array()) {
		parent::CommandLineTool($argv);

		if (!sizeof($this->argv)) {
			$this->usage();
			exit(1);
		}

		$this->parameters = $this->argv;
	}

	/**
	 * Print command usage information.
	 */
	function usage() {
		echo "Delete all journals except one matching the given path. USE WITH CARE.\n"
			. "Usage: {$this->scriptName} path_to_keep\n";
	}

	/**
	 * Delete all journals except one matching the given path. Contents in files_dir are left untouched.
	 */
	function execute() {
            $journalDao =& DAORegistry::getDAO('JournalDAO');
            $keeper =& $journalDao->getJournalByPath($this->parameters[0]);
            if (!$keeper) {
                echo "No matching journal found.";
                exit;
            }
            echo "About to delete all journals except: " . $keeper->getPath() . " ID: " . $keeper->getId() . "\n";
            $journalIterator =& $journalDao->getJournals();
            while ($journal =& $journalIterator->next()) {
                $path = $journal->getPath();
                $id = $journal->getId();
                if ($path != $this->parameters[0]) {
                    echo "DELETING $path\n";
                    if ($journalDao->deleteJournalById($id)) {
                        echo "DELETED $path\n";
                    }
                }
                unset($journal);
            }

	}
}

$tool = new JournalDeletionTool(isset($argv) ? $argv : array());
$tool->execute();
?>
