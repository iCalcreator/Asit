<?php
/**
 * Asit package manages array collections
 *
 * Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * Link <https://kigkonsult.se>
 * Support <https://github.com/iCalcreator/Asit>
 *
 * This file is part of Asit.
 *
 * Asit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * Asit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Asit. If not, see <https://www.gnu.org/licenses/>.
 */
namespace Kigkonsult\Asit;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use OutOfBoundsException;
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
class It
    implements SeekableIterator, Countable
{

    /**
     * The collection of elements
     *
     * @var array
     */
    protected $collection = [];

    /**
     * Iterator index
     *
     * @var int
     */
    protected $position = 0;

    /**
     * Class It construct method
     *
     * @param  array|Traversable $collection
     */
    public function __construct( $collection = null ) {
        if( null !== $collection ) {
            $this->setCollection( $collection );
        }
    }

    /**
     * Class It factory method
     *
     * @param  array|Traversable $collection
     * @return static
     */
    public static function factory( $collection = null ) {
        return new static( $collection );
    }

    /**
     * Clear (remove) collection
     */
    public function init() {
        $this->collection = [];
    }

    /**
     * toString
     *
     * @return string
     */
    public function toString() {
        static $SP0  = '';
        $string = $SP0;
        $pLen   = strlen((string) $this->count());
        $this->rewind();
        while( $this->valid()) {
            $string .= self::element2String( self::prepKeyString( $this->key(), $pLen ), $this->current());
            $this->next();
        }
        return $string;
    }

    /**
     * Return key and element(-type) string
     *
     * $param int $key
     * @param int $len
     * @return string
     */
    protected static function prepKeyString( $key, $len ) {
        static $SP1 = ' ';
        return str_pad((string) $key, $len, $SP1, STR_PAD_LEFT );
    }

    /**
     * Return key and element(-type) string
     *
     * $param string $key
     * @param mixed $element
     * @return string
     */
    protected static function element2String( $key, $element ) {
        static $ROW1     = '%s : (%s) ';
        static $OBJECT   = 'object';
        static $RESOURCE = 'resource';
        $type   = gettype( $element );
        $string = sprintf( $ROW1, $key, $type );
        switch( true ) {
            case is_bool( $element ) :
                $string .= var_export( $element, true );
                break;
            case is_scalar( $element ) :
                $string .= $element;
                break;
            case is_array( $element ) :
                $string .= var_export( $element, true );
                break;
            case ( $OBJECT == $type ) :
                $string .= get_class( $element );
                break;
            case ( $RESOURCE == $type ) :
                $string .= $RESOURCE;
                break;
            default :
                $string .= var_export( $element, true );
                break;
        }
        $string .= PHP_EOL;
        return $string;
    }

    /**
     * Sort collection on values using sort constants (for scalar values?) or callable (complex values)
     *
     * For int sortParam, an asort is performed, for callable, uasort
     *
     * @param  array $collection
     * @param  int|callable $sortParam
     * @throws InvalidArgumentException
     * @return array
     */
    protected static function sort( array $collection, $sortParam = SORT_REGULAR ) {
        static $ERR1 = "Invalid sortParam %s";
        static $ERR2 = "Sort error with sortParam %s";
        $sortOk = false;
        switch( true ) {
            case is_int( $sortParam ) :
                $sortOk = asort( $collection, $sortParam );
                break;
            case is_callable( $sortParam ) :
                $sortOk = uasort( $collection, $sortParam );
                break;
            default :
                throw new InvalidArgumentException( sprintf( $ERR1, var_export( $sortParam, true )));
                break;
        }
        if( ! $sortOk ) {
            throw new InvalidArgumentException( sprintf( $ERR2, var_export( $sortParam, true )));
        }
        return $collection;
    }

    /**
     * Get-methods
     */

    /**
     * Return collection, opt sorted
     *
     * @param  int|callable $sortParam
     * @return array
     */
    public function get( $sortParam = null ) {
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
     * Append element to (array) collection, opt with primary key
     *
     * Note, last appended element is always 'current'
     *
     * @param mixed $element
     * @return static
     * @throws InvalidArgumentException
     */
    public function append( $element) {
        $index = $this->count();
        $this->collection[$index] = $element;
        $this->position = $index;
        return $this;
    }

    /**
     * @var string
     */
    protected static $ERRSETTXT = 'Invalid input, no array or Traversable : %s';

    /**
     * Set (array) collection
     *
     * @param  array|Traversable $collection
     * @return static
     * @throws InvalidArgumentException
     */
    public function setCollection( $collection ) {
        switch( true ) {
            case is_array( $collection ) :
                foreach( array_keys( $collection ) as $cIx ) {
                    $this->append( $collection[$cIx] );
                }
                break;
            case ( $collection instanceof Traversable ) :
                foreach( $collection as $element ) {
                    $this->append( $element );
                }
                break;
            default :
                throw new InvalidArgumentException( sprintf( self::$ERRSETTXT, self::getErrType ( $collection )));
                break;
        }
        return $this;
    }

    /**
     * @param mixed $collection
     * @return string
     */
    protected static function getErrType ( $collection ) {
        static $OBJECT = 'object';
        $type = gettype( $collection );
        return ( $OBJECT == $type ) ? get_class( $collection ) : $type;
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
    public function count() {
        return count( $this->collection );
    }

    /**
     * Return the current element
     *
     * Required method implementing the Iterator interface
     *
     * @return mixed
     */
    public function current() {
        return $this->collection[$this->position];
    }

    /**
     * Return bool true is the collection is not empty
     *
     * @return bool
     */
    public function isCollectionSet() {
        return ( 0 != $this->count());
    }

    /**
     * Checks if position is set
     *
     * @param  int $position
     * @return bool
     */
    public function exists( $position ) {
        return array_key_exists( $position, $this->collection );
    }

    /**
     * Return an external iterator, Traversable
     *
     * Required method implementing the IteratorAggregate interface,
     * i.e. makes the class traversable using foreach.
     * Usage : 'foreach( $class as $value ) { .... }'
     *
     * @return Traversable
     */
    public function getIterator() {
        return new ArrayIterator( $this->collection );
    }

    /**
     * Return the key of the current element
     *
     * Required method implementing the Iterator interface
     *
     * @return int
     */
    public function key() {
        return $this->position;
    }

    /**
     * Move position to last element
     *
     * @return static
     */
    public function last() {
        $this->position = count( $this->collection ) - 1;
        return $this;
    }

    /**
     * Move position forward to next element
     *
     * Required method implementing the Iterator interface
     *
     * @return static
     */
    public function next() {
        $this->position += 1;
        return $this;
    }

    /**
     * Move position backward to previous element
     *
     * @return static
     */
    public function previous() {
        $this->position -= 1;
        return $this;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * Required method implementing the Iterator interface
     *
     * @return static
     */
    public function rewind() {
        $this->position = 0;
        return $this;
    }

    /**
     * Seeks to a given position in the iterator
     *
     * Required method implementing the SeekableIterator interface
     *
     * @param  int $position
     * @return void
     * @throws OutOfBoundsException
     */
    public function seek( $position ) {
        static $ERRTXT = "Position %d not found!";
        if( ! $this->exists( $position )) {
            throw new OutOfBoundsException( sprintf( $ERRTXT, $position ));
        }
        $this->position = $position;
    }

    /**
     * Checks if current position is valid
     *
     * Required method implementing the Iterator interface
     *
     * @return bool
     */
    public function valid() {
        return array_key_exists( $this->position, $this->collection );
    }

}
