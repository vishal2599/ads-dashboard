<?php

/**
 * @package AdvertisersDashboard
 *
 */

namespace AdvDashboard;
final class Init
{
    /**
     * Store all classes inside an array
     * 
     * @return Array full list of classes
     */

    public static function get_services()
    {
        return [
            Pages\Admin::class,
            Base\Enqueue::class,
            Base\SettingsLinks::class,
            Base\Publish::class,
            Base\AdGenerator::class
        ];
    }

    /**
     * Loop through the classes, initialize them
     * and call the register() method if it exists
     * 
     */

    public static function register_services()
    {
        foreach ( self::get_services() as $class )
        {
            $service = self::instantiate( $class );

            if( method_exists( $service, 'register' ) ){
                $service->register();
            }
        }
    }

    /**
     * Initialize the class 
     * 
     * @param $class class from the services array
     * @return class instance  new instance of the class
     */

    private static function instantiate( $class )
    {
        $service = new $class();
        return $service;
    }
}

//         public function custom_post_type()
//         {
//             register_post_type(
//                 'book',
//                 [
//                     'public' => true,
//                     'label'  => 'Books'
//                 ]
//             );
//         }
// }