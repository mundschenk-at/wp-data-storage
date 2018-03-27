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

use Mundschenk\Data_Storage\Options;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use Mockery as m;

/**
 * Mundschenk\Data_Storage\Options unit test for the singleton methods.
 *
 * @coversDefaultClass \Mundschenk\Data_Storage\Options
 * @usesDefaultClass \Mundschenk\Data_Storage\Options
 *
 * @uses ::__construct
 * @uses \Mundschenk\Data_Storage\Abstract_Cache::__construct
 */
class Options_Test extends TestCase {

	/**
	 * Test fixture.
	 *
	 * @var \Mundschenk\Data_Storage\Options
	 */
	protected $options;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() { // @codingStandardsIgnoreLine
		$this->options = m::mock( Options::class )->makePartial();

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
	 *
	 * @uses \Mundschenk\Data_Storage\Abstract_Cache::__construct
	 */
	public function test___construct() {
		$cache = m::mock( Options::class, [ 'my_prefix' ] )->makePartial();

		$this->assertInstanceOf( Options::class, $cache );
		$this->assertAttributeSame( 'my_prefix', 'prefix', $cache );
	}

	/**
	 * Tests get.
	 *
	 * @covers ::get
	 *
	 * @uses ::get_name
	 */
	public function test_get() {
		$raw_key = 'foo';
		$key     = $this->options->get_name( $raw_key );
		$default = 'something';

		Functions\expect( 'get_option' )->once()->with( $key, $default )->andReturn( 'bar' );

		$this->assertSame( 'bar', $this->options->get( $raw_key, $default ) );
	}

	/**
	 * Tests get.
	 *
	 * @covers ::get
	 *
	 * @uses ::get_name
	 */
	public function test_get_missing_array() {
		$raw_key = 'foo';
		$key     = $this->options->get_name( $raw_key );
		$default = [];

		Functions\expect( 'get_option' )->once()->with( $key, $default )->andReturn( '' );

		$this->assertSame( [], $this->options->get( $raw_key, $default ) );
	}

	/**
	 * Tests get.
	 *
	 * @covers ::get
	 */
	public function test_get_raw() {
		$raw_key = 'foo';
		$default = 'something';

		Functions\expect( 'get_option' )->once()->with( $raw_key, $default )->andReturn( 'bar' );
		$this->options->shouldReceive( 'get_name' )->never();

		$this->assertSame( 'bar', $this->options->get( $raw_key, $default, true ) );
	}

	/**
	 * Tests delete.
	 *
	 * @covers ::delete
	 *
	 * @uses ::get_name
	 */
	public function test_delete() {
		$raw_key = 'foo';
		$key     = $this->options->get_name( $raw_key );

		Functions\expect( 'delete_option' )->once()->with( $key )->andReturn( true );

		$this->assertTrue( $this->options->delete( $raw_key ) );
	}

	/**
	 * Tests delete.
	 *
	 * @covers ::delete
	 */
	public function test_delete_raw() {
		$raw_key = 'foo';

		Functions\expect( 'delete_option' )->once()->with( $raw_key )->andReturn( true );
		$this->options->shouldReceive( 'get_name' )->never();

		$this->assertTrue( $this->options->delete( $raw_key, true ) );
	}

	/**
	 * Tests set.
	 *
	 * @covers ::set
	 *
	 * @uses ::get_name
	 */
	public function test_set() {
		$value   = 'bar';
		$raw_key = 'foo';
		$key     = $this->options->get_name( $raw_key );

		Functions\expect( 'update_option' )->once()->with( $key, $value, true )->andReturn( true );

		$this->assertTrue( $this->options->set( $raw_key, $value ) );
	}

	/**
	 * Tests set.
	 *
	 * @covers ::set
	 */
	public function test_set_raw() {
		$value   = 'bar';
		$raw_key = 'foo';

		Functions\expect( 'update_option' )->once()->with( $raw_key, $value, true )->andReturn( true );
		$this->options->shouldReceive( 'get_name' )->never();

		$this->assertTrue( $this->options->set( $raw_key, $value, true, true ) );
	}

	/**
	 * Tests get_name.
	 *
	 * @covers ::get_name
	 */
	public function test_get_name() {
		$raw_key = 'foo';

		$this->setValue( $this->options, 'prefix', 'BAR_', Options::class );
		$this->assertSame( "BAR_{$raw_key}", $this->options->get_name( $raw_key ) );
	}
}
