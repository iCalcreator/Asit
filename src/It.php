<?php
/**
 * Asit package manages array collections
 *
 * This file is part of Asit.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-2024 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
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
use Kigkonsult\Asit\Exceptions\PositionException;
use Kigkonsult\Asit\Exceptions\SortException;
use OutOfBoundsException;
use SeekableIterator;
use Traversable;

use function array_key_exists;
use function array_key_last;
use function array_keys;
use function array_search;
use function asort;
use function get_class;
use function get_debug_type;
use function is_array;
use function is_bool;
use function is_callable;
use function is_int;
use function is_scalar;
use function ksort;
use function sprintf;
use function str_pad;
use function str_replace;
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
     * The instance collection of elements, stored as collection
     *
     * @var mixed[]
     */
    protected array $collection = [];

    /**
     * The instance iterator index, stored as position
     *
     * @var int
     */
    protected int $position = 0;

    /**
     * The instances array
     *
     * @var BaseInterface[]
     */
    protected static array $instances = [];

    /**
     * Class construct method
     *
     * @param mixed|null $collection
     * @throws CollectionException
     */
    public function __construct( mixed $collection = null )
    {
        $this->init();
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
     * Class singleton method on class-type
     *
     * @param mixed|null $collection
     * @param mixed|null $dummy
     * @return static
     * @throws CollectionException
     */
    public static function singleton( mixed $collection = null, mixed $dummy = null ) : static
    {
        $cx = static::class;
        if( ! isset( self::$instances[$cx] )) {
            self::$instances[$cx] = new static( $collection, $dummy );
        }
        return self::$instances[$cx];
    }

    /**
     * Singleton instance method alias, return (singleton class) instance
     *
     * @param mixed|null $collection
     * @param mixed|null $dummy
     * @return static
     */
    public static function getInstance( mixed $collection = null, mixed $dummy = null ) : static
    {
        return static::singleton( $collection, $dummy );
    }

    /**
     * Clear (remove) collection
     *
     * @return static
     */
    public function init() : static
    {
        $this->collection = [];
        $this->position   = 0;
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
        $type        = get_debug_type( $element );
        $string      = sprintf( $TMPL, $key, $type );
        $string .= match ( true ) {
            is_bool( $element )      => self::getDispVal( $element ),
            is_scalar( $element )    => $element,
            is_array( $element )     => self::getDispVal( $element ),
            self::OBJECT === $type   => get_class( $element ),
            self::RESOURCE === $type => self::RESOURCE,
            default                  => self::getDispVal( $element ),
        }; // end match
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
        return str_replace( PHP_EOL, self::$SP0, var_export( $value, true ));
    }

    /**
     * Return the sorted collection|array
     *
     * For null=sortParam, a ksort is performed
     * For int (sort constants) sortParam, an asort is performed,
     * For callable, uasort
     *
     * @param mixed[]  $collection
     * @param null|callable|int $sortParam
     * @return mixed[]
     * @throws SortException
     */
    protected static function sort(
        array $collection,
        null | int | callable $sortParam = null
    ) : array
    {
        $output = [];
        foreach( array_keys( $collection ) as $x ) {
            if( null !== $collection[$x] ) {
                $output[$x] = $collection[$x];
            }
        }
        if( 1 >= count( $output )) {
            return $output;
        }
        $sortOk = match( true ) {
            ( null === $sortParam )   => ksort( $output ),
            is_int( $sortParam )      => asort( $output, $sortParam ),
            is_callable( $sortParam ) => uasort( $output, $sortParam ),
            default                   => throw new SortException(
                sprintf( SortException::$ERRTXT1, self::getDispVal( $sortParam ))
            ),
        }; // end match
        if( false === $sortOk ) {
            throw new SortException(
                sprintf( SortException::$ERRTXT2, self::getDispVal( $sortParam ))
            );
        }
        return $output;
    }

    /**
     * Get-methods
     */

    /**
     * Return collection, opt (value) sorted (null=sortParam, ksort)
     *
     * @param callable|int|null $sortParam
     * @return mixed[]
     * @throws SortException
     */
    public function get( null|callable|int $sortParam = null ) : array
    {
        return self::sort( $this->collection, $sortParam );
    }

    /**
     * Copy collection elements, on index basis, to target array (opt overwrite), no sort
     *
     * @param int[] $fromIxs
     * @param null|mixed[] $target
     * @return void
     */
    protected function copyElements( array $fromIxs, ? array & $target = [] ) : void
    {
        foreach( $fromIxs as $index ) {
            if( isset( $this->collection[$index] )) {
                $target[$index] = $this->collection[$index];
            }
        }
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
     * @param null|int|string|int[]|string[] $tags   not use here
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
     * Set (array) collection, i.e. append elements
     *
     * @param mixed[] $collection
     * @return static
     * @throws CollectionException
     */
    public function setCollection( iterable $collection ) : static
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
     * SeekableIterator, Countable, IteratorAggregate et al. methods
     */

    /**
     * Return count of collection elements
     *
     * Required method implementing the Countable interface
     *
     * @return int
     * @since 2.2.1 2024-01-08
     */
    public function count() : int
    {
        $key   = $this->key();
        $count = 0;
        $this->rewind();
        while( $this->valid() ) {
            ++$count;
            $this->next();
        } // end while
        $this->position = ( $this->exists( $key ) ? $key : 0 );
        return $count;
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
     * @since 2.2.1 2024-01-08
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
     * @since 2.2.1 2024-01-08
     */
    public function exists( int $position ) : bool
    {
        return ( array_key_exists( $position, $this->collection ) &&
            ( null !== $this->collection[$position] ));
    }

    /**
     * Return an external iterator, Traversable
     *
     * Required method implementing the IteratorAggregate interface,
     * i.e. makes the class traversable using foreach.
     * Usage : "foreach( $class as $value ) { .... }'
     *
     * @return Traversable  mixed[]
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
     * @since 2.2.1 2024-01-08
     */
    public function last() : static
    {
        $this->position = (int) ( array_key_last( $this->collection ) ?? 0 );
        while( ! $this->exists( $this->position ) ) {
            --$this->position;
            if( 0 >= $this->position ) {
                $this->position = 0;
                break;
            } // end if
        } // end while
        return $this;
    }

    /**
     * Move position forward to next element
     *
     * Required method implementing the Iterator interface
     *
     * @return static
     * @since 2.2.1 2024-01-08
     */
    #[\ReturnTypeWillChange]
    public function next() : static
    {
        $lastIx = (int) ( array_key_last( $this->collection ) ?? 0 );
        do {
            ++$this->position;
            if( $lastIx < $this->position ) {
                break;
            } // end if
        } while( ! $this->exists( $this->position ) );
        return $this;
    }

    /**
     * Move position backward to previous element
     *
     * @return static
     * @since 2.2.1 2024-01-08
     */
    public function previous() : static
    {
        do {
            --$this->position;
            if( 0 > $this->position ) {
                break;
            } // end if
        } while( ! $this->exists( $this->position ) );
        return $this;
    }

    /**
     * Remove the Iterator current element
     *
     * The current element is hereafter null
     *
     * @return static
     * @since 2.2.1 2024-01-08
     */
    public function remove() : static
    {
        $key = $this->position;
        $this->collection[$key] = null;
        return $this;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * Required method implementing the Iterator interface
     *
     * @return static
     * @since 2.2.1 2024-01-08
     */
    #[\ReturnTypeWillChange]
    public function rewind() : static
    {
        $this->position = -1;
        $this->next();
        return $this;
    }

    /**
     * Seeks to a given position in the iterator, i.e. make current
     *
     * Required method implementing the SeekableIterator interface
     *
     * @param  int $offset   position (due to inherit rules, NO typed arg)
     * @return static
     * @throws OutOfBoundsException
     * @since 2.2.7 2024-01-26
     */
    #[\ReturnTypeWillChange]
    public function seek( $offset ) : static
    {
        static $TMPL = "Position %d not found!";
        if( ! $this->exists( $offset )) {
            throw new OutOfBoundsException( sprintf( $TMPL, $offset ));
        }
        $this->position = $offset;
        return $this;
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
     * @throws PositionException
     */
    protected function assertCurrent() : void
    {
        if( ! $this->valid()) {
            throw new PositionException( sprintf( PositionException::$ERR1, $this->position ));
        }
    }

    /**
     * Return key (pKey/tag) for (first found) value in array
     *
     * @param int|string $value
     * @param int[]|string[] $array
     * @return int|string
     * @throws PositionException
     */
    protected static function search( int | string $value, array $array ) : int | string
    {
        if( is_bool( $return = array_search( $value, $array, true ))) {
            throw new PositionException( sprintf( PositionException::$ERR1, $value ));
        }
        return $return;
    }
}
