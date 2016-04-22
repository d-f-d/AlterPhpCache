<?php

namespace d\AntiCache;

use Drupal\Core\Cache\PhpBackendFactory;

/**
 * Created by PhpStorm.
 * User: punk_undead
 * Date: 22.04.16
 * Time: 23:39
 */
class AntiCacheBackendFactory extends PhpBackendFactory {
  function get($bin) {  
    return new AntiCacheBackend($bin, $this->checksumProvider);
  }
}