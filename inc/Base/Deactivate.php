<?php

/**
 * @package AdvertisersDashboard
 *
 */

namespace AdvDashboard\Base;

class Deactivate
{
   public static function deactivate()
   {
      $thisClass = new Deactivate();
      $thisClass->removeAdvertiser();
      flush_rewrite_rules();
   }

   public function removeAdvertiser()
   {
      remove_role('advertiser');
   }
}
