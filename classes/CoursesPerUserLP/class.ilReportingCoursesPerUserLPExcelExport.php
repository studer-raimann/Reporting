<?php

/**
 * Class ilReportingCoursesPerUserLPExcelExport
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @version $Id:
 *
 */
class ilReportingCoursesPerUserLPExcelExport extends ilReportingExcelExport {

	public function __construct($filename = '') {
		parent::__construct($filename);
	}


	/**
	 * Generate export
	 *
	 * @param array $data
	 */
	public function execute(array $data) {
		// Format data array to separate multiple Users by worksheet
		// array(user1 => array(course1, course2, course3), user2 => array(course1, course2...))
		$data = $this->formatData($data);
		ob_start();
		foreach ($data as $users) {
			$name = $users[0]['firstname'] . ' ' . $users[0]['lastname'];
			if (!$users[0]['active']) {
				$name .= ' (' . $this->pl->txt('inactive') . ')';
			}
			$worksheet = $this->workbook->addWorksheet();
			// Write in worksheet
			$row = 0;
			$this->write($worksheet, $row, 0, $name, $this->h1);
			$row ++;
			$this->write($worksheet, ++ $row, 0, $this->pl->txt('country'));
			$this->write($worksheet, $row, 1, $users[0]['country']);
			$this->write($worksheet, ++ $row, 0, $this->pl->txt('department'));
			$this->write($worksheet, $row, 1, $users[0]['department']);
			$this->write($worksheet, ++ $row, 0, $this->pl->txt('date_of_report'));
			$this->write($worksheet, $row, 1, $this->formatter->formatCurrentDate());
			$this->write($worksheet, ++ $row, 0, $this->pl->txt('owner_of_report'));
			$this->write($worksheet, $row, 1, $this->user->getFirstname() . ' ' . $this->user->getLastname());
			$row += 2;
			// List courses: Title of columns
			$this->write($worksheet, $row, 0, $this->lng->txt('crs'), $this->h3);
			$this->write($worksheet, $row, 1, $this->pl->txt('path'), $this->h3);
			$this->write($worksheet, $row, 2, $this->pl->txt('general_status'), $this->h3);
			$this->write($worksheet, $row, 3, $this->pl->txt('status_changed'), $this->h3);
			foreach ($users as $course) {
				$this->write($worksheet, ++ $row, 0, $course['title']);
				$this->write($worksheet, $row, 1, $course['path']);
				$this->write($worksheet, $row, 2, $this->formatter->format($course['user_status'], ilReportingFormatter::FORMAT_INT_STATUS));
				$this->write($worksheet, $row, 3, $this->formatter->format($course['status_changed'], ilReportingFormatter::FORMAT_STR_DATE));
			}
			// Learning progress table
			$row += 3;
			$this->write($worksheet, $row, 0, $this->lng->txt('crs'), $this->h3);
			$this->write($worksheet, $row, 1, $this->pl->txt('assessment_title'), $this->h3);
			$this->write($worksheet, $row, 2, $this->pl->txt('grade'), $this->h3);
			$this->write($worksheet, $row, 3, $this->pl->txt('comments'), $this->h3);
			$this->write($worksheet, $row, 4, $this->pl->txt('object_percentage'), $this->h3);
			$this->write($worksheet, $row, 5, $this->pl->txt('object_status'), $this->h3);
			$this->write($worksheet, $row, 6, $this->pl->txt('object_type'), $this->h3);
			$this->write($worksheet, $row, 7, $this->pl->txt('object_status_changed'), $this->h3);
			foreach ($users as $course) {
				if (count($course['_objects'])) {
					$this->write($worksheet, ++ $row, 0, $course['title']);
					$i = 0;
					foreach ($course['_objects'] as $object) {
						if ($i) {
							$row ++;
						}
						$this->write($worksheet, $row, 1, $object['object_title']);
						$this->write($worksheet, $row, 2, $object['grade']);
						$this->write($worksheet, $row, 3, $object['comments']);
						$this->write($worksheet, $row, 4, (int)$object['object_percentage']);
						$this->write($worksheet, $row, 5, $this->formatter->format($object['object_status'], ilReportingFormatter::FORMAT_INT_STATUS));
						$type = ($object['object_type']) ? $this->lng->txt($object['object_type']) : '';
						$this->write($worksheet, $row, 6, $type);
						$this->write($worksheet, $row, 7, $this->formatter->format($object['object_status_changed'], ilReportingFormatter::FORMAT_STR_DATE));
						$i ++;
					}
				}
			}
			$this->autoSizeColumns($worksheet);
		}
		ob_end_clean();
		$this->workbook->close();
	}
}
