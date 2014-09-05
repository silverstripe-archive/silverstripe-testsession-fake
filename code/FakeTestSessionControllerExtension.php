<?php
/**
 * Allows definition of a fake database template to use
 */
class FakeTestSessionControllerExtension extends Extension {

	public function init() {
		Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.js');
		Requirements::javascript('testsession-fake/javascript/TestSessionFake.js');
	}

	public function fakeDatabasePath() {
		return TEMP_FOLDER . '/' . uniqid() . '.json';
	}

	public function updateStartForm($form) {
		$fields = $form->Fields();

		// Default to last database template
		// TODO Remove?
		$templateField = $fields->dataFieldByName('importDatabasePath');
		if($templateField) {
			$templates = $templateField->getSource();
			end($templates);
			$templateField->setValue(key($templates));
		}

		$fields->push(new CheckboxField('useFakeManager', 'Use webservice fakes?', 1));
		$fields->push(new HiddenField('fakeDatabasePath', null, $this->fakeDatabasePath()));

		$templates = Config::inst()->get('FakeManager', 'database_template_paths');
		if($templates) {
			$fields->push(
				DropdownField::create(
					'fakeDatabaseTemplatePath',
					false,
					array_combine(
						array_map(function($template) {return BASE_PATH . '/' . $template;}, $templates),
						$templates
					)
				)->setEmptyString('none')
			);
		}
	}

}