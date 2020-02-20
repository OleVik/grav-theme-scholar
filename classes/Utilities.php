<?php
/**
 * Scholar Theme, Utilities
 *
 * PHP version 7
 *
 * @category API
 * @package  Grav\Theme\Scholar
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
namespace Grav\Theme\Scholar;

use PHPExtra\Sorter\Sorter;
use PHPExtra\Sorter\Strategy\SimpleSortStrategy;
use PHPExtra\Sorter\Strategy\ComplexSortStrategy;
use PHPExtra\Sorter\Comparator\NumericComparator;
use PHPExtra\Sorter\Comparator\DateTimeComparator;
use PHPExtra\Sorter\Comparator\UnicodeCIComparator;

/**
 * Utilities
 *
 * @category Extensions
 * @package  Grav\Theme\Scholar\Utilities
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-scholar
 */
class Utilities
{
    /**
     * Search for a file in multiple locations
     *
     * @param string $file      Filename.
     * @param array  $locations List of locations.
     *
     * @return string File location.
     */
    public static function fileFinder(string $file, array $locations): string
    {
        $return = '';
        foreach ($locations as $location) {
            if (file_exists($location . '/' . $file)) {
                $return = $location . '/' . $file;
                break;
            }
        }
        return $return;
    }

    /**
     * Search for a folder in multiple locations
     *
     * @param string $folder    Folder name..
     * @param array  $locations List of locations.
     *
     * @return string Folder location.
     */
    public static function folderFinder(string $folder, array $locations): string
    {
        $return = '';
        foreach ($locations as $location) {
            if (is_dir($location . '/' . $folder)) {
                $return = $location . '/' . $folder;
                break;
            }
        }
        return $return;
    }

    /**
     * Search for files in multiple locations
     *
     * @param string $directory Folder-name.
     * @param array  $types     File extensions.
     *
     * @return array List of file locations.
     */
    public static function filesFinder(string $directory, array $types): array
    {
        if (!file_exists($directory)) {
            return [];
        }
        $iterator = new \RecursiveDirectoryIterator(
            $directory,
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        $iterator = new \RecursiveIteratorIterator($iterator);
        $files = [];
        foreach ($iterator as $file) {
            if (in_array(pathinfo($file, PATHINFO_EXTENSION), $types)) {
                $files[] = $file;
            }
        }
        if (count($files) > 0) {
            return $files;
        } else {
            return [];
        }
    }

    /**
     * Search for a folders in multiple locations
     *
     * @param array $locations List of locations.
     *
     * @return array List of folder locations.
     */
    public static function foldersFinder(array $locations): array
    {
        $return = array();
        foreach ($locations as $location) {
            if (!file_exists($location)) {
                continue;
            }
            $folders = new \DirectoryIterator($location);
            foreach ($folders as $folder) {
                if ($folder->isDir() && !$folder->isDot()) {
                    $return[] = $folder->getFilename();
                }
            }
        }
        return $return;
    }

    /**
     * Find key in array
     *
     * @param array  $array  Array to search.
     * @param string $search Key to search for.
     * @param array  $keys   Reserved.
     *
     * @see https://stackoverflow.com/a/40506009/603387
     *
     * @return array Matches.
     */
    public static function arraySearch($array, $search, $keys = array()): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sub = self::arraySearch($value, $search, array_merge($keys, array($key)));
                if (count($sub)) {
                    return $sub;
                }
            } elseif ($key === $search) {
                return array_merge($keys, array($key));
            }
        }
        return array();
    }

    /**
     * Collapse 1-d array to n-d array
     *
     * @param array $array Array to collapse.
     * @param mixed $info  Data to add as value to last index.
     *
     * @see https://stackoverflow.com/a/16925154/603387
     *
     * @return array Collapsed array.
     */
    public static function collapse($array, $info): array
    {
        $max = count($array)-1;
        $result = array($array[$max] => $info);
        for ($i=$max-1;$i>=0;$result = array($array[$i--] => $result));
        return $result;
    }

    /**
     * Unset key from multidimensional array
     *
     * @param array  $array Array to manipulate.
     * @param string $key   Key to unset.
     *
     * @see https://stackoverflow.com/a/46445227
     *
     * @return array Manipulated array.
     */
    public static function removeKey(array $array, string $key): array
    {
        foreach ($array as $k => $v) {
            if (is_array($v) && $k != $key) {
                $array[$k] = self::removeKey($v, $key);
            } elseif ($k == $key) {
                unset($array[$k]);
            }
        }
        return $array;
    }

    /**
     * Add element to multidimensional array
     *
     * @param array  $arr  Array to hold values, private.
     * @param string $path String to add.
     *
     * @see https://stackoverflow.com/a/15133284
     *
     * @return array Manipulated array.
     */
    public static function assignArrayByPath(&$arr, $path): array
    {
        $keys = explode('\\', $path);
        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }
    }

    /**
     * Flatten an array to key => value
     *
     * @param array $array Array to flatten.
     * @param array $keys  Array to store results, private.
     *
     * @return array Manipulated array.
     */
    public static function arrayFlattenKeysAsValues($array, $keys = array()): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $keys[$key] = $key;
                $keys = self::arrayFlattenKeysAsValues($array[$key], $keys);
            } else {
                $keys[$key] = $key;
            }
        }
        return $keys;
    }

    /**
     * Sort multidimensional array
     *
     * @see https://stackoverflow.com/a/16788610
     *
     * @return array Manipulated array.
     */
    public static function make_comparer(): array
    {
        // Normalize criteria up front so that the comparer finds everything tidy
        $criteria = func_get_args();
        foreach ($criteria as $index => $criterion) {
            $criteria[$index] = is_array($criterion)
                ? array_pad($criterion, 3, null)
                : array($criterion, SORT_ASC, null);
        }
    
        return function ($first, $second) use (&$criteria) {
            foreach ($criteria as $criterion) {
                // How will we compare this round?
                list($column, $sortOrder, $projection) = $criterion;
                $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;
    
                // If a projection was defined project the values now
                if ($projection) {
                    $lhs = call_user_func($projection, $first[$column]);
                    $rhs = call_user_func($projection, $second[$column]);
                } else {
                    $lhs = $first[$column];
                    $rhs = $second[$column];
                }
    
                // Do the actual comparison; do not return if equal
                if ($lhs < $rhs) {
                    return -1 * $sortOrder;
                } elseif ($lhs > $rhs) {
                    return 1 * $sortOrder;
                }
            }
    
            return 0; // tiebreakers exhausted, so $first == $second
        };
    }

    /**
     * Sort array using PHPExtra/Sorter
     *
     * @param array  $array    Array to sort.
     * @param string $orderBy  Property to sort by.
     * @param string $orderDir Direction to sort.
     *
     * @return array Manipulated array.
     */
    public static function sortLeaf(Array $array, String $orderBy = 'date', String $orderDir = 'asc'): array
    {
        require __DIR__ . '/../vendor/autoload.php';
        if ($orderBy == 'date') {
            $orderBy = 'datetime';
        }
        $strategy = new ComplexSortStrategy();
        if ($orderDir == 'asc') {
            $strategy->setSortOrder(Sorter::ASC);
        } elseif ($orderDir == 'desc') {
            $strategy->setSortOrder(Sorter::DESC);
        }
        $strategy->sortBy($orderBy);
        $strategy->setMaintainKeyAssociation(true);
        $sorter = new Sorter();
        return $sorter->setStrategy($strategy)->sort($array);
    }

    /**
     * Cast an array into an object, recursively
     *
     * @param array $array Array to cast.
     *
     * @return stdClass
     */
    public static function toObject($array)
    {
        $obj = new \stdClass;
        foreach ($array as $k => $v) {
            if (strlen($k)) {
                if (is_array($v)) {
                    $obj->{$k} = self::toObject($v);
                } else {
                    $obj->{$k} = $v;
                }
            }
        }
        return $obj;
    }

    /**
     * Filters the elements of an array recursively, using a given callable
     *
     * Callable function must return a boolean, whether to accept or remove the value
     *
     * @param array    $array    Array to search
     * @param callable $callback Function to call
     *
     * @return array Manipulated array.
     *
     * @link https://github.com/lingtalfi/Bat/blob/master/ArrayTool.md#filterrecursive
     */
    public static function filterRecursive(array $array, callable $callback): array
    {
        foreach ($array as $k => $v) {
            $res = call_user_func($callback, $v);
            if (false === $res) {
                unset($array[$k]);
            } else {
                if (is_array($v)) {
                    $array[$k] = self::filterRecursive($v, $callback);
                }
            }
        }
        return $array;
    }
}
