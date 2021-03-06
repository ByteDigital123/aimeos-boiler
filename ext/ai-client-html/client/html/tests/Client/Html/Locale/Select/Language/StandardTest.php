<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Locale\Select\Language;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Locale\Select\Language\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$view->selectCurrencyId = 'EUR';
		$view->selectLanguageId = 'de';
		$view->selectMap = array(
			'de' => array(
				'EUR' => array( 'locale' => 'de', 'currency' => 'EUR' ),
				'CHF' => array( 'locale' => 'de', 'currency' => 'CHF' ),
			),
			'en' => array( 'USD' => array( 'locale' => 'en', 'currency' => 'USD' ) ),
		);

		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();
		$helper = new \Aimeos\MW\View\Helper\Request\Standard( $view, $request, '127.0.0.1', 'test' );
		$view->addHelper( 'request', $helper );

		$tags = [];
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<div class="locale-select-language">', $output );
		$this->assertStringContainsString( '<li class="select-dropdown select-current"><a href="#">de', $output );
		$this->assertStringContainsString( '<li class="select-item active">', $output );

		$this->assertEquals( 0, count( $tags ) );
		$this->assertEquals( null, $expire );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcess()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['locale' => 'de'] );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$this->assertEquals( 'de', $this->context->getSession()->get( 'aimeos/locale/languageid' ) );
	}
}
