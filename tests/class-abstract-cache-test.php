<?php
/**
 * This file is part of mundschenk-at/wp-data-storage.
 *
 * Copyright 2017-2018 Peter Putzer.
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

use Mundschenk\Data_Storage\Abstract_Cache;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use Mockery as m;

/**
 * Mundschenk\Data_Storage\Abstract_Cache unit test for the singleton methods.
 *
 * @coversDefaultClass \Mundschenk\Data_Storage\Abstract_Cache
 * @usesDefaultClass \Mundschenk\Data_Storage\Abstract_Cache
 *
 * @uses ::__construct
 */
class Abstract_Cache_Test extends TestCase {



	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() { // @codingStandardsIgnoreLine
		parent::setUp();
	}

	/**
	 * Necesssary clean-up work.
	 */
	protected function tearDown() { // @codingStandardsIgnoreLine
		parent::tearDown();
	}

	/**
	 * Tests constructor.
	 *
	 * @covers ::__construct
	 */
	public function test___construct() {
		$cache = m::mock( Abstract_Cache::class )->makePartial();
		$cache->shouldReceive( 'invalidate' )->once();

		$this->invokeMethod( $cache, '__construct', [ 'prefix_' ] );

		$this->assertInstanceOf( Abstract_Cache::class, $cache );
	}

	/**
	 * Tests get_key.
	 *
	 * @covers ::get_key
	 */
	public function test_get_key() {
		$prefix      = 'my_prefix_';
		$key         = 'foo';
		$incrementor = 99;

		// Prepare object.
		$cache = m::mock( Abstract_Cache::class )->makePartial();
		$this->setValue( $cache, 'incrementor', $incrementor, Abstract_Cache::class );
		$this->setValue( $cache, 'prefix', $prefix, Abstract_Cache::class );

		$this->assertSame( "{$prefix}{$incrementor}_{$key}", $this->invokeMethod( $cache, 'get_key', [ $key ] ) );
	}

	/**
	 * Tests get_prefix.
	 *
	 * @covers ::get_prefix
	 */
	public function test_get_prefix() {
		// Prepare object.
		$cache = m::mock( Abstract_Cache::class )->makePartial();
		$this->setValue( $cache, 'prefix', 'foobar', Abstract_Cache::class );

		$this->assertSame( 'foobar', $this->invokeMethod( $cache, 'get_prefix', [] ) );
	}
}
