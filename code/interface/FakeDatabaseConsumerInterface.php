<?php
/**
 * Consumer of a {@link FakeDatabase} instance,
 * which means state can be shared across object instances, across requests.
 */
interface FakeDatabaseConsumerInterface {

	/**
	 * @param FakeDatabase $db
	 */
	public function setDb(FakeDatabase $db);

	/**
	 * @return FakeDatabase
	 */
	public function getDb();
}