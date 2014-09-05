<?php
/**
 * Creates and coordinates all fake objects, and ensures they can share state
 * by using a common "fake database". Needs to be instantiated as early
 * as possible during a SilverStripe bootstrap to ensure all injected
 * instances use the new fakes.
 */
class FakeManager {

	/**
	 * @var FakeDatabase
	 */
	protected $db;

	/**
	 * @var array Fake service instances, keyed by their original service name.
	 */
	protected $services = array();

	/**
	 * @param FakeDatabase $db
	 */
	function __construct($db = null) {
		$this->db = $db;
	}

	/**
	 * @return FakeDatabase
	 */
	public function getDb() {
		return $this->db;
	}

	/**
	 * Replace services in SilverStripe's injector with fake instances.
	 * The service names are usually class names.
	 */
	public function registerServices() {
		$services = Config::inst()->get('FakeManager', 'services');
		if($services) {
			foreach($services as $origName => $fakeName) {
				$fakeObj = Injector::inst()->create($fakeName);
				if($this->db && $fakeObj instanceof FakeDatabaseConsumerInterface) {
					$fakeObj->setDb($this->db);
				}
				$this->services[$origName] = $fakeObj;
				Injector::inst()->registerService($fakeObj, $origName);
			}
		}
	}
}