<?php
/**
 * Asit package manages array collections
 *
 * This file is part of Asit.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-21 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @link      https://kigkonsult.se
 * @license   Subject matter of licence is the software Asit.
 *            The above copyright, link, package and version notices,
 *            this licence notice shall be included in all copies or substantial
 *            portions of the Asit.
 *
 *            Asit is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU Lesser General Public License as
 *            published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *            Asit is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *            GNU Lesser General Public License for more details.
 *
 *            You should have received a copy of the GNU Lesser General Public License
 *            along with Asit. If not, see <https://www.gnu.org/licenses/>.
 */
declare( strict_types = 1 );
namespace Kigkonsult\Asit;

use ArrayIterator;
use Countable;
use Kigkonsult\Asit\Exceptions\CollectionException;
use Kigkonsult\Asit\Exceptions\SortException;
use OutOfBoundsException;
use RuntimeException;
use SeekableIterator;
use Traversable;

use function array_key_exists;
use function array_keys;
use function asort;
use function count;
use function get_class;
use function gettype;
use function is_array;
use function is_bool;
use function is_callable;
use function is_int;
use function is_scalar;
use function sprintf;
use function str_pad;
use function strlen;
use function uasort;
use function var_export;

/**
 * Class It, manages array collection of elements
 *
 * @package Kigkonsult\Asit
 */
class It implements BaseInterface, SeekableIterator, Countable
{
    /**
     * @var string
     */
    protected static string $SP0 = '';

    /**
     * The collection of elements
     *
     * @var array
     */
    protected array $collection = [];

    /**
     * Iterator index
     *
     * @var int
     */
    protected int $position = 0;

    /**
     * Class construct method
     *
     * @param mixed|null $collection
     * @throws CollectionException
     */
    public function __construct( mixed $collection = null )
    {
        if( null !== $collection ) {
            $this->setCollection( $collection );
        }
    }

    /**
     * Class factory method
     *
     * @param mixed|null $collection
     * @param mixed|null $dummy  to fix inheritance
     * @return static
     * @throws CollectionException
     */
    public static function factory( mixed $collection = null, mixed $dummy = null ) : static
    {
        return new static( $collection );
    }

    /**
     * Class singleton method
     *
     * @param mixed|null $collection
     * @param mixed|null $dummy
     * @return static
     * @throws CollectionException
     */
    public static function singleton( mixed $collection = null, mixed $dummy = null ) : static
    {
        static $instance = null;
        if( null === $instance ) {
            $instance = new static( $collection );
        }
        return $instance;
    }

    /**
     * Clear (remove) collection
     *
     * @return static
     */
    public function init() : static
    {
        $this->collection = [];
        return $this;
    }

    /**
     * toString
     *
     * @return string
     */
    public function toString() : string
    {
        $string = self::$SP0;
        $pLen   = strlen((string) $this->count());
        $this->rewind();
        while( $this->valid()) {
            $string .= self::element2String(
                self::prepKeyString( $this->key(), $pLen ),
                $this->current()
            );
            $this->next();
        }
        return $string;
    }

    /**
     * Return key and element(-type) string
     *
     * @param int $key
     * @param int $len
     * @return string
     */
    protected static function prepKeyString( int $key, int $len ) : string
    {
        static $PAD = ' ';
        return str_pad((string) $key, $len, $PAD, STR_PAD_LEFT );
    }

    /**
     * Return key and element(-type) string
     *
     * @param string $key
     * @param mixed  $element
     * @return string
     */
    protected static function element2String( string $key, mixed $element ) : string
    {
        static $TMPL = "%s : (%s) ";
        $type        = gettype( $element );
        $string      = sprintf( $TMPL, $key, $type );
        $string .= match ( true ) {
            is_bool( $element )      => self::getDispVal( $element ),
            is_scalar( $element )    => $element,
            is_array( $element )     => self::getDispVal( $element ),
            self::OBJECT === $type   => get_class( $element ),
            self::RESOURCE === $type => self::RESOURCE,
            default                  => self::getDispVal( $element ),
        }; // end mach
        $string .= PHP_EOL;
        return $string;
    }

    /**
     * Return value rendered for display
     * @param mixed $value
     * @return string
     */
    protected static function getDispVal( mixed $value ) : string
    {
        return var_export( $value, true );
    }

    /**
     * Sort collection on values
     *
     * For int (sort constants) sortParam, an asort is performed,
     * for callable, uasort
     *
     * @param array        $collection
     * @param null|callable|int $sortParam
     * @return array
     * @throws SortException
     */
    protected static function sort(
        array $collection,
        null | int | callable $sortParam = SORT_REGULAR
    ) : array
    {
        $sortParam = $sortParam ?? SORT_REGULAR;
        $sortOk    = match( true ) {
            is_int( $sortParam )      => asort( $collection, $sortParam ),
            is_callable( $sortParam ) => uasort( $collection, $sortParam ),
            default                   => throw new SortException(
                sprintf( SortException::$ERRTXT1, self::getDispVal( $sortParam ))
            ),
        }; // end match
        if( ! $sortOk ) {
            throw new SortException(
                sprintf( SortException::$ERRTXT2, self::getDispVal( $sortParam ))
            );
        }
        return $collection;
    }

    /**
     * Get-methods
     */

    /**
     * Return collection, opt sorted
     *
     * @param callable|int|null $sortParam
     * @return array
     * @throws SortException
     */
    public function get( callable | int $sortParam = null ) : array
    {
        if( null !== $sortParam ) {
            $result = $this->collection;
            return self::sort( $result, $sortParam );
        }
        return $this->collection;
    }

    /**
     * Set-methods
     */

    /**
     * Append element to (array) collection
     *
     * Note, last appended element is always 'current'
     *
     * @override
     * @param mixed                 $element
     * @param null|int|string       $pKey   not used here
     * @param null|int|string|array $tags   not use here
     * @return static
     */
    public function append(
        mixed $element,
        null|int|string $pKey = null,
        null|int|string|array $tags = null
    ) : static
    {
        $index = $this->count();
        $this->collection[$index] = $element;
        $this->position = $index;
        return $this;
    }

    /**
     * Set (array) collection
     *
     * @param Traversable|array $collection
     * @return static
     * @throws CollectionException
     */
    public function setCollection( Traversable | array $collection ) : static
    {
        if( is_array( $collection ))  {
            foreach( array_keys( $collection ) as $cIx ) {
                $this->append( $collection[$cIx] );
            }
        }
        else { // ( $collection instanceof Traversable )
            foreach( $collection as $element ) {
                $this->append( $element );
            }
        }
        return $this;
    }

    /**
     * Return value type rendered for display
     *
     * @param mixed $value
     * @return string
     */
    protected static function getErrType( mixed $value ) : string
    {
        $getType = gettype( $value );
        // end switch
        return match ( true ) {
            self::OBJECT === $getType   => get_class( $value ),
            self::RESOURCE === $getType => self::RESOURCE,
            default                     => $getType,
        };
    }

    /**
     * SeekableIterator, Countable, IteratorAggregate et al. methods
     */

    /**
     * Return count of collection elements
     *
     * Required method implementing the Countable interface
     *
     * @return int
     */
    public function count() : int
    {
        return count( $this->collection );
    }

    /**
     * Return the current element
     *
     * Required method implementing the Iterator interface
     *
     * @return mixed
     */
    public function current() : mixed
    {
        return $this->collection[$this->position];
    }

    /**
     * Return bool true is the collection is not empty
     *
     * @return bool
     */
    public function isCollectionSet() : bool
    {
        return ( 0 !== $this->count());
    }

    /**
     * Checks if position is set
     *
     * @param  int $position
     * @return bool
     */
    public function exists( int $position ) : bool
    {
        return array_key_exists( $position, $this->collection );
    }

    /**
     * Return an external iterator, Traversable
     *
     * Required method implementing the IteratorAggregate interface,
     * i.e. makes the class traversable using foreach.
     * Usage : "foreach( $class as $value ) { .... }'
     *
     * @return Traversable
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator( $this->collection );
    }

    /**
     * Return the key of the current element
     *
     * Required method implementing the Iterator interface
     *
     * @return int
     */
    public function key() : int
    {
        return $this->position;
    }

    /**
     * Move position to last element, if empty set to 0
     *
     * @return static
     */
    public function last() : static
    {
        $count          = count( $this->collection );
        $this->position = empty( $count ) ? 0 : ( $count - 1 );
        return $this;
    }

    /**
     * Move position forward to next element
     *
     * Required method implementing the Iterator interface
     *
     * @return static
     */
    public function next() : static
    {
        ++$this->position;
        return $this;
    }

    /**
     * Move position backward to previous element
     *
     * @return static
     */
    public function previous() : static
    {
        --$this->position;
        return $this;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * Required method implementing the Iterator interface
     *
     * @return static
     */
    public function rewind() : static
    {
        $this->position = 0;
        return $this;
    }

    /**
     * Seeks to a given position in the iterator, i.e. make current
     *
     * Required method implementing the SeekableIterator interface
     *
     * @param  int $offset   position (due to inherit rules, NO typed arg)
     * @return void
     * @throws OutOfBoundsException
     */
    public function seek( $offset ) : void
    {
        static $TMPL = "Position %d not found!";
        if( ! $this->exists( $offset )) {
            throw new OutOfBoundsException( sprintf( $TMPL, $offset ));
        }
        $this->position = $offset;
    }

    /**
     * Checks if current position is valid
     *
     * Required method implementing the Iterator interface
     *
     * @return bool
     */
    public function valid() : bool
    {
        return $this->exists( $this->position );
    }

    /**
     * misc
     */

    /**
     * Assert current position is valid
     *
     * @throws RuntimeException
     */
    protected function assertCurrent() : void
    {
        static $CURRENTNOTVALID = "Invalid current position";
        if( ! $this->valid()) {
            throw new RuntimeException( $CURRENTNOTVALID );
        }
    }

    /**
     * Return key (pKey/tag) for (first found) value in array
     *
     * @param int|string $value
     * @param array      $array
     * @return false|int|string
     */
    protected static function search( int | string $value, array $array ) : bool | int | string
    {
        return array_search( $value, $array, true );
    }
}
