<?php

namespace SMW\Tests\System;

use SMW\Tests\Utils\GlobalsProvider;

use ResourceLoader;
use ResourceLoaderModule;
use ResourceLoaderContext;

/**
 * @group SMW
 * @group SMWExtension
 *
 * @group semantic-mediawiki-system
 * @group mediawiki-databaseless
 *
 * @license GNU GPL v2+
 * @since 1.9
 *
 * @author mwjames
 */
class ResourcesAccessibilityTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider moduleDataProvider
	 */
	public function testModulesScriptsFilesAreAccessible( $modules, ResourceLoader $resourceLoader, $context ) {

		foreach ( array_keys( $modules ) as $name ) {
			$this->assertInternalType(
				'string',
				$resourceLoader->getModule( $name )->getScript( $context )
			);
		}
	}

	/**
	 * @dataProvider moduleDataProvider
	 */
	public function testModulesStylesFilesAreAccessible( $modules, ResourceLoader $resourceLoader, $context  ) {

		foreach ( array_keys( $modules ) as $name ) {

			$styles = $resourceLoader->getModule( $name )->getStyles( $context );

			foreach ( $styles as $style ) {
				$this->assertInternalType( 'string', $style );
			}
		}
	}

	public function moduleDataProvider() {

		$resourceLoader = new ResourceLoader();
		$context = ResourceLoaderContext::newDummyContext();
		$modules = $this->includeResourceDefinitionsFromFile();

		return array( array(
			$modules,
			$resourceLoader,
			$context
		) );
	}

	private function includeResourceDefinitionsFromFile() {
		return include GlobalsProvider::getInstance()->get( 'smwgIP' ) . '/resources/Resources.php';
	}

}
