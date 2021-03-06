<?php
require_once __DIR__ . "/../../vendor/autoload.php";

/**
 * GUI-Class ilReportingUsersPerCourseGUI
 *
 * @author            Stefan Wanzenried <sw@studer-raimann.ch>
 * @version           $Id:
 *
 * @ilCtrl_IsCalledBy ilReportingUsersPerCourseGUI: ilRouterGUI, ilUIPluginRouterGUI
 */
class ilReportingUsersPerCourseGUI extends ilReportingGUI {

	const CMD_SHOW_OBJECTS_IN_COURSE = 'showObjectsInCourse';


	function __construct() {
		parent::__construct();
		$this->model = new ilReportingUsersPerCourseModel();
	}


	public function executeCommand() {
		parent::executeCommand();
	}


	/**
	 * Redirect to UsersPerCourseLP report which shows objects in courses which are relevant for LP
	 */
	public function showObjectsInCourse() {
		$this->ctrl->setParameterByClass(ilReportingUsersPerCourseLPGUI::class, "from", self::class);
		$this->ctrl->redirectByClass(ilReportingUsersPerCourseLPGUI::class, ilReportingUsersPerCourseLPGUI::CMD_REPORT);
	}


	/**
	 * Display table for searching the courses
	 */
	public function search() {
		$this->tpl->setTitle($this->pl->txt('report_users_per_course'));
		$this->table = new ilReportingUsersPerCourseSearchTableGUI($this, ilReportingGUI::CMD_SEARCH);
		$this->table->setTitle($this->pl->txt('search_courses'));
		parent::search();
	}


	/**
	 * Display report table
	 */
	public function report() {
		parent::report();
		if (isset($_GET['rep_crs_ref_id'])) {
			$this->ctrl->saveParameter($this, 'rep_crs_ref_id');
		}
		$this->tpl->setTitle($this->pl->txt('report_users_per_course'));
		if ($this->table === NULL) {
			$this->table = new ilReportingUsersPerCourseReportTableGUI($this, ilReportingGUI::CMD_REPORT);
		}
		$data = $this->model->getReportData($_SESSION[self::SESSION_KEY_IDS], $this->table->getFilterNames());
		$this->table->setData($data);
		if ($this->ctrl->getCmd() != self::CMD_APPLY_FILTER_REPORT
			&& $this->ctrl->getCmd() != self::CMD_RESET_FILTER_REPORT) {
			$onlyUnique = isset($_GET['pre_xpt']);
			$this->storeIdsInSession($data, $onlyUnique);
		}
		$this->tpl->setContent($this->table->getHTML());
	}


	public function applyFilterSearch() {
		$this->table = new ilReportingUsersPerCourseSearchTableGUI($this, $this->getStandardCmd());
		parent::applyFilterSearch();
	}


	public function resetFilterSearch() {
		$this->table = new ilReportingUsersPerCourseSearchTableGUI($this, $this->getStandardCmd());
		parent::resetFilterSearch();
	}


	public function applyFilterReport() {
		if (isset($_GET['rep_crs_ref_id'])) {
			$this->ctrl->saveParameter($this, 'rep_crs_ref_id');
		}
		$this->table = new ilReportingUsersPerCourseReportTableGUI($this, ilReportingGUI::CMD_REPORT);
		parent::applyFilterReport();
	}


	public function resetFilterReport() {
		if (isset($_GET['rep_crs_ref_id'])) {
			$this->ctrl->saveParameter($this, 'rep_crs_ref_id');
		}
		$this->table = new ilReportingUsersPerCourseReportTableGUI($this, ilReportingGUI::CMD_REPORT);
		parent::resetFilterReport();
	}


	public function getAvailableExports() {
		if ($this->isActiveJasperReports()) {
			$exports[self::EXPORT_PDF] = 'export_pdf';
		}

		return $exports;
	}
}
