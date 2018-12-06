<?php

namespace Portfolio\Helpers;

class Inheritance {
  private $globals = [];

  public function __construct() {
    $this->globals['_ti_base']  = null;
    $this->globals['_ti_stack'] = null;
  }

  public function emptyblock($name) {
    $trace = $this->_ti_callingTrace();
    $this->_ti_init($trace);
    $this->_ti_insertBlock(
      $this->_ti_newBlock($name, null, $trace)
    );
  }

  public function startblock($name, $filters = null) {
    $trace   = $this->_ti_callingTrace();
    $this->_ti_init($trace);
    $stack   =& $this->globals['_ti_stack'];
    $stack[] = $this->_ti_newBlock($name, $filters, $trace);
  }

  public function endblock($name = null) {
    $trace = $this->_ti_callingTrace();
    $this->_ti_init($trace);
    $stack =& $this->globals['_ti_stack'];
    if ($stack) {
      $block = array_pop($stack);
      if ($name && $name != $block['name']) {
        $this->_ti_warning("startblock('{$block['name']}') does not match endblock('$name')", $trace);
      }
      $this->_ti_insertBlock($block);
    } else {
      $this->_ti_warning(
        $name ? "orphan endblock('$name')" : "orphan endblock()",
        $trace
      );
    }
  }

  public function superblock() {
    if ($this->globals['_ti_stack']) {
      echo $this->getsuperblock();
    } else {
      $this->_ti_warning(
        "superblock() call must be within a block",
        $this->_ti_callingTrace()
      );
    }
  }

  public function getsuperblock() {
    $stack =& $this->globals['_ti_stack'];
    if ($stack) {
      $hash  =& $this->globals['_ti_hash'];
      $block = end($stack);
      if (isset($hash[$block['name']])) {
        return implode(
          $this->_ti_compile(
            $hash[$block['name']]['block'],
            ob_get_contents()
          )
        );
      }
    } else {
      $this->_ti_warning(
        "getsuperblock() call must be within a block",
        $this->_ti_callingTrace()
      );
    }
    return '';
  }

  public function flushblocks() {
    $base =& $this->globals['_ti_base'];
    if ($base) {
      $stack =& $this->globals['_ti_stack'];
      $level =& $this->globals['_ti_level'];
      while ($block = array_pop($stack)) {
        $this->_ti_warning(
          "missing endblock() for startblock('{$block['name']}')",
          $this->_ti_callingTrace(),
          $block['trace']
        );
      }
      while (ob_get_level() > $level) {
        ob_end_flush();
      }
      $base = null;
      $stack = null;
    }
  }

  public function blockbase() {
    $this->_ti_init($this->_ti_callingTrace());
  }

  public function _ti_init($trace) {
    $base =& $this->globals['_ti_base'];
    if ($base && !$this->_ti_inBaseOrChild($trace)) {
      $this->flushblocks();
    }
    if (!$base) {
      $base = array(
        'trace' => $trace,
        'filters' => null,
        'children' => array(),
        'start' => 0,
        'end' => null
      );
      $this->globals['_ti_level'] = ob_get_level();
      $this->globals['_ti_stack'] = array();
      $this->globals['_ti_hash']  = array();
      $this->globals['_ti_end']   = null;
      $this->globals['_ti_after'] = '';
      ob_start(array($this, '_ti_bufferCallback'));
    }
  }

  public function _ti_newBlock($name, $filters, $trace) {
    $base  =& $this->globals['_ti_base'];
    $stack =& $this->globals['_ti_stack'];
    while ($block = end($stack)) {
      if ($this->_ti_isSameFile($block['trace'], $trace)) {
        break;
      } else {
        array_pop($stack);
        $this->_ti_insertBlock($block);
        $this->_ti_warning(
          "missing endblock() for startblock('{$block['name']}')",
          $this->_ti_callingTrace(),
          $block['trace']
        );
      }
    }
    if ($base['end'] === null && !$this->_ti_inBase($trace)) {
      $base['end'] = ob_get_length();
    }
    if ($filters) {
      if (is_string($filters)) {
        $filters = preg_split('/\s*[,|]\s*/', trim($filters));
      }
      else if (!is_array($filters)) {
        $filters = array($filters);
      }
      foreach ($filters as $i => $f) {
        if ($f && !is_callable($f)) {
          $this->_ti_warning(
            is_array($f) ?
              "filter " . implode('::', $f) . " is not defined":
              "filter '$f' is not defined",
            $trace
          );
          $filters[$i] = null;
        }
      }
    }
    return array(
      'name'     => $name,
      'trace'    => $trace,
      'filters'  => $filters,
      'children' => array(),
      'start'    => ob_get_length()
    );
  }

  public function _ti_insertBlock($block) {
    $base  =& $this->globals['_ti_base'];
    $stack =& $this->globals['_ti_stack'];
    $hash  =& $this->globals['_ti_hash'];
    $end   =& $this->globals['_ti_end'];
    $block['end'] = $end = ob_get_length();
    $name         = $block['name'];
    if ($stack || $this->_ti_inBase($block['trace'])) {
      $block_anchor = array(
        'start' => $block['start'],
        'end'   => $end,
        'block' => $block
      );
      if ($stack) {
        $stack[count($stack)-1]['children'][] =& $block_anchor;
      } else {
        $base['children'][] =& $block_anchor;
      }
      $hash[$name] =& $block_anchor;
    }
    else if (isset($hash[$name])) {
      if ($this->_ti_isSameFile($hash[$name]['block']['trace'], $block['trace'])) {
        $this->_ti_warning(
          "cannot define another block called '$name'",
          $this->_ti_callingTrace(),
          $block['trace']
        );
      } else {
        $hash[$name]['block'] = $block;
      }
    }
  }

  public function _ti_bufferCallback($buffer) {
    $base  =& $this->globals['_ti_base'];
    $stack =& $this->globals['_ti_stack'];
    $end   =& $this->globals['_ti_end'];
    $after =& $this->globals['_ti_after'];
    if ($base) {
      while ($block = array_pop($stack)) {
        $this->_ti_insertBlock($block);
        $this->_ti_warning(
          "missing endblock() for startblock('{$block['name']}')",
          $this->_ti_callingTrace(),
          $block['trace']
        );
      }
      if ($base['end'] === null) {
        $base['end'] = strlen($buffer);
        $end = null;
      }
      $parts = $this->_ti_compile($base, $buffer);
      $i = count($parts) - 1;
      $parts[$i] = rtrim($parts[$i]);
      if ($end !== null) {
        $parts[] = substr($buffer, $end);
      }
      $parts[] = $after;
      return implode($parts);
    } else {
      return '';
    }
  }

  public function _ti_compile($block, $buffer) {
    $parts = array();
    $previ = $block['start'];
    foreach ($block['children'] as $child_anchor) {
      $parts[] = substr($buffer, $previ, $child_anchor['start'] - $previ);
      $parts = array_merge(
        $parts,
        $this->_ti_compile($child_anchor['block'], $buffer)
      );
      $previ = $child_anchor['end'];
    }
    if ($previ != $block['end']) {
      $parts[] = substr($buffer, $previ, $block['end'] - $previ);
    }
    if ($block['filters']) {
      $s = implode($parts);
      foreach ($block['filters'] as $filter) {
        if ($filter) {
          $s = call_user_func($filter, $s);
        }
      }
      return array($s);
    }
    return $parts;
  }

  public function _ti_warning($message, $trace, $warning_trace = null) {
    if (error_reporting() & E_USER_WARNING) {
      if (defined('STDIN')) {
        $format = "\nWarning: %s in %s on line %d\n";
      } else {
        $format = "<br />\n<b>Warning</b>:  %s in <b>%s</b> on line <b>%d</b><br />\n";
      }
      if (!$warning_trace) {
        $warning_trace = $trace;
      }
      $s = sprintf($format, $message, $warning_trace[0]['file'], $warning_trace[0]['line']);
      if (!$this->globals['_ti_base'] || $this->_ti_inBase($trace)) {
        echo $s;
      } else {
        $this->globals['_ti_after'] .= $s;
      }
    }
  }

  public function _ti_callingTrace() {
    $trace = debug_backtrace();
    foreach ($trace as $i => $location) {
      if ($location['file'] !== __FILE__) {
        return array_slice($trace, $i);
      }
    }
  }

  public function _ti_inBase($trace) {
    return $this->_ti_isSameFile($trace, $this->globals['_ti_base']['trace']);
  }

  public function _ti_inBaseOrChild($trace) {
    $base_trace = $this->globals['_ti_base']['trace'];
    return
      $trace && $base_trace &&
      $this->_ti_isSubtrace(array_slice($trace, 1), $base_trace) &&
      $trace[0]['file'] === $base_trace[count($base_trace)-count($trace)]['file'];
  }

  public function _ti_isSameFile($trace1, $trace2) {
    return
      $trace1 && $trace2 &&
      $trace1[0]['file'] === $trace2[0]['file'] &&
      array_slice($trace1, 1) === array_slice($trace2, 1);
  }

  public function _ti_isSubtrace($trace1, $trace2) {
    $len1 = count($trace1);
    $len2 = count($trace2);
    if ($len1 > $len2) {
      return false;
    }
    for ($i=0; $i<$len1; $i++) {
      if ($trace1[$len1-1-$i] !== $trace2[$len2-1-$i]) {
        return false;
      }
    }
    return true;
  }
}
