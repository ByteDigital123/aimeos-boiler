<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\Admin\JQAdm\Catalog\Product;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperJqadm::getView();
		$this->context = \TestHelperJqadm::getContext();

		$this->object = new \Aimeos\Admin\JQAdm\Catalog\Product\Standard( $this->context );
		$this->object = new \Aimeos\Admin\JQAdm\Common\Decorator\Page( $this->object, $this->context );
		$this->object->setAimeos( \TestHelperJqadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->view, $this->context );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$this->view->item = $manager->findItem( 'cafe' );

		$result = $this->object->copy();

		$this->assertStringContainsString( 'item-product', $result );
	}


	public function testCopyException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->copy();
	}


	public function testCopyMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->setView( $this->getViewNoRender() );

		$object->copy();
	}


	public function testCreate()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$this->view->item = $manager->findItem( 'cafe' );

		$result = $this->object->create();

		$this->assertStringContainsString( 'item-product', $result );
	}


	public function testCreateException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->create();
	}


	public function testCreateMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->setView( $this->getViewNoRender() );

		$object->create();
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$this->view->item = $manager->findItem( 'cafe' );

		$result = $this->object->get();

		$this->assertStringContainsString( 'item-product', $result );
		$this->assertStringContainsString( 'Cafe Noire Expresso', $result );
	}


	public function testGetException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->get();
	}


	public function testGetMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->setView( $this->getViewNoRender() );

		$object->get();
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'catalog' );

		$item = $manager->findItem( 'cafe' );
		$item->setCode( 'jqadm-test-save' );
		$item->setId( null );

		$item = $manager->insertItem( $item );


		$param = array(
			'site' => 'unittest',
			'product' => array(
				'catalog.lists.id' => [0 => ''],
				'catalog.lists.status' => [0 => 1],
				'catalog.lists.refid' => [0 => 'test'],
				'catalog.lists.type' => [0 => 'promotion'],
				'catalog.lists.datestart' => [0 => '2000-01-01 00:00:00'],
				'catalog.lists.dateend' => [0 => '2100-01-01 00:00:00'],
				'config' => [0 => ['key' => [0 => 'test'], 'val' => [0 => 'value']]],
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->view->item = $item;

		$result = $this->object->save();

		$item = $manager->getItem( $item->getId(), ['product'] );
		$manager->deleteItem( $item->getId() );

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertEmpty( $result );
		$this->assertEquals( 1, count( $item->getListItems() ) );

		foreach( $item->getListItems( 'product' ) as $listItem )
		{
			$this->assertEquals( $item->getId(), $listItem->getParentId() );
			$this->assertEquals( 'promotion', $listItem->getType() );
			$this->assertEquals( 'product', $listItem->getDomain() );
			$this->assertEquals( '2000-01-01 00:00:00', $listItem->getDateStart() );
			$this->assertEquals( '2100-01-01 00:00:00', $listItem->getDateEnd() );
			$this->assertEquals( ['test' => 'value'], $listItem->getConfig() );
			$this->assertEquals( 'test', $listItem->getRefId() );
			$this->assertEquals( 1, $listItem->getStatus() );
		}
	}


	public function testSaveException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$this->expectException( \Aimeos\Admin\JQAdm\Exception::class );
		$object->save();
	}


	public function testSaveMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->setView( $this->getViewNoRender() );

		$this->expectException( \Aimeos\Admin\JQAdm\Exception::class );
		$object->save();
	}


	public function testGetSubClient()
	{
		$this->expectException( \Aimeos\Admin\JQAdm\Exception::class );
		$this->object->getSubClient( 'unknown' );
	}


	protected function getViewNoRender()
	{
		$view = $this->getMockBuilder( \Aimeos\MW\View\Standard::class )
			->setConstructorArgs( array( [] ) )
			->setMethods( array( 'render', 'config' ) )
			->getMock();

		$manager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$view->item = $manager->findItem( 'cafe' );

		return $view;
	}
}