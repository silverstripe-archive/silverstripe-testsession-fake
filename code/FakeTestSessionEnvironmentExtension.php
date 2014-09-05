<?php
/**
 * Resets the fake database used for fake web services
 * after a test scenario finishes. The database is initialized
 * on each request through {@link FakeTestSessionControllerExtension}.
 */
class FakeTestSessionEnvironmentExtension extends Extension {

	/**
	 * This needs to handle two distinct cases:
	 * - Test Session being created by behat (calling TestSessionEnvironment directly), and
	 * - Test Session being created by browsing to dev/testsession and submitting the form.
	 *
	 * The form is modified above (@see self::updateStartForm()) and we need to ensure we respect those selections, if
	 * necessary. If it wasn't submitted via a form, then we can set the fakes up as required for behat.
	 *
	 * @param $state Array of state passed from TestSessionEnvironment
	 */
	public function onBeforeStartTestSession(&$state) {
		// Only set fake database paths when using fake manager
		if(empty($state['useFakeManager'])) {
			unset($state['fakeDatabasePath']);
			unset($state['fakeDatabaseTemplatePath']);
		}

		if(
			$state
			&& !empty($state['useFakeManager'])
			&& !empty($state['fakeDatabaseTemplatePath'])
			&& !empty($state['fakeDatabasePath'])
		) {
			// Copy template database, to keep it clean for other runs
			copy($state['fakeDatabaseTemplatePath'], $state['fakeDatabasePath']);
			chmod($state['fakeDatabasePath'], 0777);
		}

		return $state;
	}

	/**
	 * Only used for manual testing, not on Behat runs.
	 */
	public function onBeforeClear() {
		$testEnv = Injector::inst()->get('TestSessionEnvironment');
		$state = $testEnv->getState();

		if($state && isset($state->useFakeManager) && $state->useFakeManager) {
			$this->resetFakeDatabase();
		}
	}

	/**
	 * Only used for manual testing, not on Behat runs.
	 */
	public function onBeforeEndTestSession() {
		$state = $this->owner->getState();

		if($state && isset($state->useFakeManager) && $state->useFakeManager) {
			$this->resetFakeDatabase();
		}
	}

	/**
	 * A similar reset is also performed in App\Tests\Behaviour\FeatureContext->resetFakeDatabase().
	 * We can't reset Behat CLI runs through this measure because the CLI has a persistent connection
	 * to the underlying SQLite database file, so the browser can't remove it.
	 */
	protected function resetFakeDatabase() {
		$state = $this->owner->getState();
		if(!empty($state->fakeDatabasePath) && file_exists($state->fakeDatabasePath)) {
			unlink($state->fakeDatabasePath);
		}
	}

}