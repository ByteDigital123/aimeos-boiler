<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Controller\Common\Product\Import\Csv\Processor\Media;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $endpoint;


	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelperCntl::getContext();
		$this->endpoint = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Done( $this->context, [] );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
	}


	public function testProcess()
	{
		$mapping = array(
			0 => 'media.languageid',
			1 => 'media.label',
			2 => 'media.mimetype',
			3 => 'media.preview',
			4 => 'media.url',
			5 => 'media.status',
		);

		$data = array(
			0 => 'de',
			1 => 'test image',
			2 => 'image/jpeg',
			3 => 'path/to/preview',
			4 => 'path/to/file',
			5 => 1,
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $listItem );
		$this->assertEquals( 1, count( $listItems ) );

		$this->assertEquals( 1, $listItem->getStatus() );
		$this->assertEquals( 0, $listItem->getPosition() );
		$this->assertEquals( 'default', $listItem->getType() );

		$refItem = $listItem->getRefItem();

		$this->assertEquals( 1, $refItem->getStatus() );
		$this->assertEquals( 'default', $refItem->getType() );
		$this->assertEquals( 'test image', $refItem->getLabel() );
		$this->assertEquals( 'image/jpeg', $refItem->getMimetype() );
		$this->assertEquals( 'path/to/preview', $refItem->getPreview() );
		$this->assertEquals( 'path/to/file', $refItem->getUrl() );
		$this->assertEquals( 'de', $refItem->getLanguageId() );
	}


	public function testProcessMultiple()
	{
		$mapping = array(
			0 => 'media.url',
			1 => 'media.preview',
			2 => 'media.previews',
		);

		$data = array(
			0 => "path/to/b0\npath/to/b1\npath/to/b2\npath/to/b3",
			1 => "path/to/0\npath/to/1\npath/to/2\npath/to/3",
			2 => "{\"1\":\"path/to/0\",\"500\":\"path/to/b0\"}\n{\"10\":\"path/to/1\",\"510\":\"path/to/b1\"}\n{\"20\":\"path/to/2\",\"520\":\"path/to/b2\"}\n{\"30\":\"path/to/3\",\"530\":\"path/to/b3\"}",
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );


		$pos = 0;
		$listItems = $product->getListItems();
		$expected = ['path/to/b0', 'path/to/b1', 'path/to/b2', 'path/to/b3'];
		$previews = [
			['1' => 'path/to/0', '500' => 'path/to/b0'],
			['10' => 'path/to/1', '510' => 'path/to/b1'],
			['20' => 'path/to/2', '520' => 'path/to/b2'],
			['30' => 'path/to/3', '530' => 'path/to/b3'],
		];

		$this->assertEquals( 4, count( $listItems ) );

		foreach( $listItems as $listItem )
		{
			$this->assertEquals( $expected[$pos], $listItem->getRefItem()->getUrl() );
			$this->assertEquals( $previews[$pos], $listItem->getRefItem()->getPreviews() );
			$pos++;
		}
	}


	public function testProcessMultipleFields()
	{
		$mapping = array(
			0 => 'media.url',
			1 => 'media.url',
			2 => 'media.url',
			3 => 'media.url',
		);

		$data = array(
			0 => 'path/to/0',
			1 => 'path/to/1',
			2 => 'path/to/2',
			3 => 'path/to/3',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );


		$pos = 0;
		$listItems = $product->getListItems();

		$this->assertEquals( 4, count( $listItems ) );

		foreach( $listItems as $listItem )
		{
			$this->assertEquals( $data[$pos], $listItem->getRefItem()->getUrl() );
			$pos++;
		}
	}


	public function testProcessUpdate()
	{
		$mapping = array(
			0 => 'media.url',
			1 => 'media.languageid',
		);

		$data = array(
			0 => 'path/to/file',
			1 => 'de',
		);

		$dataUpdate = array(
			0 => 'path/to/new.jpg',
			1 => '',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );
		$object->process( $product, $dataUpdate );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $listItem );

		$this->assertEquals( 'path/to/new.jpg', $listItem->getRefItem()->getUrl() );
		$this->assertEquals( 'image/jpeg', $listItem->getRefItem()->getMimeType() );
		$this->assertEquals( null, $listItem->getRefItem()->getLanguageId() );
	}


	public function testProcessDelete()
	{
		$mapping = array(
			0 => 'media.url',
		);

		$data = array(
			0 => '/path/to/file',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, [], $this->endpoint );
		$object->process( $product, [] );


		$listItems = $product->getListItems();

		$this->assertEquals( 0, count( $listItems ) );
	}


	public function testProcessEmpty()
	{
		$mapping = array(
			0 => 'media.url',
			1 => 'media.url',
		);

		$data = array(
			0 => 'path/to/file',
			1 => '',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );


		$listItems = $product->getListItems();

		$this->assertEquals( 1, count( $listItems ) );
	}


	public function testProcessListtypes()
	{
		$mapping = array(
			0 => 'media.url',
			1 => 'product.lists.type',
			2 => 'media.url',
			3 => 'product.lists.type',
		);

		$data = array(
			0 => 'path/to/file',
			1 => 'download',
			2 => 'path/to/file2',
			3 => 'default',
		);

		$this->context->getConfig()->set( 'controller/common/product/import/csv/processor/media/listtypes', array( 'default' ) );

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );

		$this->expectException( '\Aimeos\Controller\Common\Exception' );
		$object->process( $product, $data );
	}


	/**
	 * @param string $code
	 */
	protected function create( $code )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		return $manager->createItem()->setCode( $code );
	}
}
