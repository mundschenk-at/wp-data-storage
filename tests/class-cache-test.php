<?php
/**
 * This file is part of mundschenk-at/wp-data-storage.
 *
 * Copyright 2017-2024 Peter Putzer.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or ( at your option ) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package mundschenk-at/wp-data-storage/tests
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Mundschenk\Data_Storage\Tests;

use Mundschenk\Data_Storage\Cache;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use Mockery as m;

/**
 * Mundschenk\Data_Storage\Cache unit test for the singleton methods.
 *
 * @coversDefaultClass \Mundschenk\Data_Storage\Cache
 * @usesDefaultClass \Mundschenk\Data_Storage\Cache
 *
 * @uses ::__construct
 * @uses \Mundschenk\Data_Storage\Abstract_Cache::__construct
 */
class Cache_Test extends TestCase {

	const PREFIX          = 'my_prefix_';
	const INCREMENTOR_KEY = self::PREFIX . 'cache_incrementor';
	const GROUP           = 'some_group';
	/**
	 * Test fixture.
	 *
	 * @var \Mundschenk\Data_Storage\Cache
	 */
	protected $cache;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function set_up() {
		parent::set_up();

		Functions\expect( 'wp_cache_get' )->once()->with( self::INCREMENTOR_KEY, self::GROUP )->andReturn( 1 );
		$this->cache = m::mock( Cache::class, [ self::PREFIX, self::GROUP ] )->makePartial();
	}

	/**
	 * Provides data for testing ::__construct.
	 *
	 * @return array
	 */
	public function provide_test__construct_data(): array {
		return [
			'valid stored incrementor' => [ 55, 55 ],
			'no stored incrementor'    => [ false, 0 ],
			'empty string'             => [ '', 0 ],
			'non-empty string'         => [ 'something', 0 ],
			'null'                     => [ null, 0 ],
			'object'                   => [ new \stdClass(), 0 ],
			'float'                    => [ 10.5, 0 ],
			'array'                    => [ [], 0 ],
		];
	}

	/**
	 * Tests constructor.
	 *
	 * @dataProvider provide_test__construct_data
	 *
	 * @covers ::__construct
	 *
	 * @uses \Mundschenk\Data_Storage\Abstract_Cache::__construct
	 *
	 * @param  mixed $stored_incrementor   The stored incrementor (return) value.
	 * @param  int   $expected_incrementor The expected final incrementor value.
	 *
	 * @return void
	 */
	public function test___construct( $stored_incrementor, int $expected_incrementor ) {
		$cache = m::mock( Cache::class )->makePartial();

		Functions\expect( 'wp_cache_get' )->once()->with( 'some_prefix_cache_incrementor', self::GROUP )->andReturn( $stored_incrementor );
		$cache->shouldReceive( 'invalidate' )->times( 0 === $expected_incrementor ? 1 : 0 );

		$cache->__construct( 'some_prefix_', self::GROUP );

		$this->assertInstanceOf( Cache::class, $cache );
		$this->assert_attribute_same( $expected_incrementor, 'incrementor', $cache );
	}

	/**
	 * Tests invalidate.
	 *
	 * @covers ::invalidate
	 */
	public function test_invalidate() {
		Functions\expect( 'wp_cache_set' )->once()->with( self::INCREMENTOR_KEY, m::type( 'int' ), self::GROUP, 0 );

		$this->cache->invalidate();

		$this->assertTrue( true );
	}

	/**
	 * Tests get.
	 *
	 * @covers ::get
	 *
	 * @uses ::get_key
	 */
	public function test_get() {
		$raw_key = 'foo';
		$key     = $this->invoke_method( $this->cache, 'get_key', [ $raw_key ] );

		Functions\expect( 'wp_cache_get' )->once()->with( $key, self::GROUP, false, null )->andReturn( 'bar' );

		$this->assertSame( 'bar', $this->cache->get( $raw_key ) );
	}

	/**
	 * Tests set.
	 *
	 * @covers ::set
	 *
	 * @uses ::get_key
	 */
	public function test_set() {
		$value    = 'bar';
		$raw_key  = 'foo';
		$duration = 99;
		$key      = $this->invoke_method( $this->cache, 'get_key', [ $raw_key ] );

		Functions\expect( 'wp_cache_set' )->once()->with( $key, $value, self::GROUP, $duration )->andReturn( true );

		$this->assertTrue( $this->cache->set( $raw_key, $value, $duration ) );
	}

	/**
	 * Tests delete.
	 *
	 * @covers ::delete
	 *
	 * @uses ::get_key
	 */
	public function test_delete() {
		$raw_key = 'foo';
		$key     = $this->invoke_method( $this->cache, 'get_key', [ $raw_key ] );

		Functions\expect( 'wp_cache_delete' )->once()->with( $key, self::GROUP )->andReturn( true );

		$this->assertTrue( $this->cache->delete( $raw_key ) );
	}
}
