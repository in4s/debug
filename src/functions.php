<?php
/** in4s\base */

declare(strict_types=1);

namespace in4s;

/**
 * @version     File version v4.0.0 2020-09-17 21:19:56
 * @author      Eugeniy Makarkin
 * @package     in4s\debug
 */


/**
 * Render given debugging data followed by die()
 *
 * @version v1.5.3 2020-09-17 21:36:47
 * @return void
 */
function debug()
{
    $args = func_get_args();
    dump(...$args);
    die();
}

/**
 * Render given debugging data
 *
 * @version v1.1.2 2020-09-17 21:25:01
 * @return void
 */
function dump()
{
    global $startTime;
    $args = func_get_args();

    $dbt = '';
    foreach (debug_backtrace() as $key => $value) {
        if (isset($value['file'])) {
            $dbt .= "{$value['file']}:<span style=\"color:yellow\">{$value['line']}</span>";
        }
        if (isset($value['class'])) {
            $dbt .= " <span style=\"color:gray\">{$value['class']}</span>->";
        }
        if (isset($value['function'])) {
            $dbt .= " <span style=\"color:#dcffc6\">{$value['function']}();</span>";
        }
        $dbt .= '<br/>';
    }

    $resultTime = $startTime === null ? '' : 'Время выполнения: ' . round(microtime(true) - $startTime, 4) . ' сек.';
    echo "<table id=\"dump\" class=\"dump\"><tr><th class=\"td td_peru dump__line\">{$resultTime}</th></tr><tr><th class=\"td td_tan dump__line\">{$dbt}</th></tr>";
    foreach ($args as $arg) {
        echo '<tr class="dump__tr"><td class="dump__line"><pre>';
        if (is_array($arg) || is_object($arg)) {
            print_r($arg);
        } elseif ($arg === null) {
            echo '-!<span class="dump__null">NULL</span>!-';
        } elseif ($arg === true) {
            echo '-!<span class="dump__true">true</span>!-';
        } elseif ($arg === false) {
            echo '-!<span class="dump__false">false</span>!-';
        } else {
            echo '-!<strong>' . $arg . '</strong>!-';
        }
        $type = gettype($arg);
        echo "({$type})";
        echo '</pre></td></tr>';
    }
    echo '</table>';
}

/**
 * Write given debugging data to the file
 *
 * @version v2.0.0 2020-09-17 21:27:22
 *
 * @param mixed $message - Content to write to file
 *
 * @return bool
 */
function debug2File($message): bool
{
    if (is_array($message) || is_object($message)) {
        $fileContent = json_encode($message, JSON_PRETTY_PRINT);
    } else {
        $fileContent = $message;
    }

    return file_put_contents('in4s_debug.txt', $fileContent);
}

/**
 * Debug output followed by die() only if the specified condition == true
 *
 * @version v0.1.2 2020-09-17 21:29:27
 *
 * @param bool $condition - Condition under which debug is triggered
 *
 * @return void
 */
function debugIf(bool $condition)
{
    if ($condition) {
        $args = func_get_args();

        // Remove from the array of arguments $condition
        array_shift($args);
        dump(...$args);
        die();
    }
}
