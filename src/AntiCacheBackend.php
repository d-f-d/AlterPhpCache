<?php

namespace d\AntiCache;

use Drupal\Core\Cache\PhpBackend;

/**
 * Created by PhpStorm.
 * User: punk_undead
 * Date: 22.04.16
 * Time: 23:39
 */
class AntiCacheBackend extends PhpBackend {
  /**
   * Writes a cache item to PhpStorage.
   *
   * @param string $cidhash
   *   The hashed version of the original cache ID after being normalized.
   * @param \stdClass $item
   *   The cache item to store.
   */
  protected function writeItem($cidhash, \stdClass $item) {
    $content = '<?php return ' . $this->drupal_var_export($item) . ';';
    $this->storage()->save($cidhash, $content);
  }

  function drupal_var_export($var, $prefix = '') {
    if (is_array($var)) {
      if (empty($var)) {
        $output = '[]';
      }
      else {
        $output = "[\n";
        // Don't export keys if the array is non associative.
        $export_keys = array_values($var) != $var;
        foreach ($var as $key => $value) {
          $output .= '  ' . ($export_keys ? $this->drupal_var_export($key) . ' => ' : '') . $this->drupal_var_export($value, '  ', FALSE) . ",\n";
        }
        $output .= ']';
      }
    }
    elseif (is_bool($var)) {
      $output = $var ? 'TRUE' : 'FALSE';
    }
    elseif (is_string($var)) {
      $line_safe_var = str_replace("\n", '\n', $var);
      if (strpos($var, "\n") !== FALSE || strpos($var, "'") !== FALSE) {
        // If the string contains a line break or a single quote, use the
        // double quote export mode. Encode backslash and double quotes and
        // transform some common control characters.
        $var = str_replace(array('\\', '"', "\n", "\r", "\t"), array('\\\\', '\"', '\n', '\r', '\t'), $var);
        $output = '"' . $var . '"';
      }
      else {
        $output = "'" . $var . "'";
      }
    }
    elseif (is_object($var)) {
      // var_export() will export stdClass objects using an undefined
      // magic method __set_state() leaving the export broken. This
      // workaround avoids this by casting the object as an array for
      // export and casting it back to an object when evaluated.
      if (get_class($var) === 'stdClass') {
        $output = '(object) ' . $this->drupal_var_export((array) $var, $prefix);
      }
      else {
        $output = 'unserialize(' . $this->drupal_var_export(serialize($var)) . ')';
      }
    }
    else {
      $output = var_export($var, TRUE);
    }

    if ($prefix) {
      $output = str_replace("\n", "\n$prefix", $output);
    }

    return $output;
  }
}