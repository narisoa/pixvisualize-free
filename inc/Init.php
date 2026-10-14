<?php
/**
 * PixVisualize service bootstrap.
 *
 * Registers core services (admin, block, widget) and Pro services
 * (shortcode, WooCommerce) when a valid Pro license is active.
 *
 * @package PixVisualize
 */
namespace Inc;

final class Init
{
	/**
	 * Service class map.
	 *
	 * @return array
	 */
	public static function getServices()
	{
		$services = [
			Pages\Dashboard::class,
			Base\Enqueue::class,
			Base\SettingsLinks::class,
			Base\Welcome::class,
			Base\CheckoutNotice::class,
			Base\PixVisualizeController::class,
			Base\ElementorController::class,
		];

		// Pro services. Stripped from the WordPress.org build.
		if ( function_exists( 'pixvisualize_fs' ) && pixvisualize_fs()->can_use_premium_code__premium_only() ) {
			$services[] = Base\ShortcodeController::class;
			$services[] = Base\WooCommerceController::class;
		}

		return $services;
	}

	/**
	 * Instantiate and register services.
	 *
	 * @return void
	 */
	public static function registerServices()
	{
		foreach (self::getServices() as $class) {
			$service = self::instantiate($class);
			if (method_exists($service, 'register')) {
				$service->register();
			}
		}
	}

	/**
	 * Service factory.
	 *
	 * @param string $class Fully-qualified class name.
	 * @return object
	 */
	private static function instantiate($class)
	{
		$service = new $class();

		return $service;
	}
}