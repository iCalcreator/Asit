<?php

/**
 * Asit manages assoc arrays
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
use RuntimeException;
use SeekableIterator;
use Traversable;

/**
 * Class Asit manages assoc arrays
 *     and has, along with SeekableIterator, Countable and IteratorAggregate (+Traversable) methods,
 *     assoc array collection element get-/set-methods.
 *
 * The collection element may, as for Iterator (et al.), be of any type.
 *
 * The assoc element array key is used as (unique) primary key.
 * A primary key may be replaced by another (unique) key.
 *
 * Collection elements are searchable using
 *     Iterator (et al.) methods
 *     primary key(s)
 *
 * For non-assoc arrays,
 *     primary key is the (numeric) array index
 *
 * Class Asittag extends Asit
 *   Also secondary keys, additional (non-unique) tags (aka attributes?)
 *   may be set for each element.
 *
 * @package    Kigkonsult\Asit
 */
class Asit
     implements SeekableIterator, Countable
{

    protected static $CURRENTNOTVALID = 'Current not valid';
    protected static $PKEYNOTFOUND    = 'Primary key : %s not found';
    protected static $PKEYFOUND       = 'New primary key : %s found (position %d)';

    /**
     * The collection of elements
     *
     * @var array
     */
    protected $collection = [];

    /**
     * Primary keys for collection element
     *
     * @var array
     */
    protected $pKeys = [];

    /**
     * Iterator index
     *
     * @var int
     */
    protected $position = 0;

    /**
     * Class Asit construct method
     *
     * @param  array $collection
     */
    public function __construct( array $collection = [] ) {
        $this->setCollection( $collection );
    }

    /**
     * Class Asit factory method
     *
     * @param  array $collection
     * @return static
     */
    public static function factory( array $collection = [] ) {
        return new static( $collection );
    }

    /**
     * Primary key methods
     */

    /**
     * Assert, int and string allowed
     *
     * @param  mixed $key
     * @param  string msg
     * @return void
     */
    protected static function assertKey( $key, $msg ) {
        switch( true ) {
            case is_int( $key ) :
                break;
            case ( is_string( $key ) && ! empty( $key )) :
                break;
            default :
                throw new InvalidArgumentException( sprintf( $msg, var_export( $key, true )));
        }
    }

    /**
     * Assert pKey, int and string allowed
     *
     * @param  mixed $pKey
     * @return void
     */
    public static function assertPkey( $pKey ) {
        static $ERR = 'Invalid primary key : %s';
        self::assertKey( $pKey, $ERR );
    }

    /**
     * Return bool true if single primary key is set
     *
     * @param  int|string $pKey
     * @return bool
     */
    public function pKeyExists( $pKey ) {
        return array_key_exists( $pKey, $this->pKeys );
    }

    /**
     * Return all primary keys
     *
     * @param int $sort  default SORT_REGULAR
     * @return array
     */
    public function getPkeys( $sort = SORT_REGULAR ) {
        if( $sort != SORT_REGULAR ) {
            $pKeys = array_keys( $this->pKeys );
            sort( $pKeys, $sort );
            return $pKeys;
        }
        return array_keys( $this->pKeys );
    }

    /**
     * Set (or reset) primary key for a collection element
     *
     * Assert && check exist pKey tests before invoke
     *
     * @param int|string $pKey 0 (zero) allowed
     * @param int        $index
     * @return static
     * @throws InvalidArgumentException
     */
    protected function setPkey( $pKey, $index ) {
        self::assertPkey( $pKey );
        if( false !== ( $previousPkey = array_search( $index, $this->pKeys, true ))) {
            unset( $this->pKeys[$previousPkey] );
        }
        $this->pKeys[$pKey] = $index;
        ksort( $this->pKeys, SORT_REGULAR );
        return $this;
    }

    /**
     * Replace (set) primary key for collection element
     *
     * @param int|string $oldPkey   0 (zero) allowed
     * @param int|string $newPkey   0 (zero) allowed
     * @return static
     * @throws InvalidArgumentException
     */
    public function replacePkey( $oldPkey, $newPkey ) {
        if( ! $this->pKeyExists( $oldPkey )) {
            throw new InvalidArgumentException( sprintf( self::$PKEYNOTFOUND, $oldPkey ));
        }
        self::assertPkey( $newPkey );
        if( $oldPkey == $newPkey ) {
            return $this;
        }
        if( $this->pKeyExists( $newPkey )) {
            throw new InvalidArgumentException( sprintf( self::$PKEYFOUND, $newPkey, $this->pKeys[$newPkey] ));
        }
        $this->setPkey( $newPkey, $this->pKeys[$oldPkey] );
        unset( $this->pKeys[$oldPkey] );
        return $this;
    }

    /**
     * Return pKey for 'current', false on not found
     *
     * To be used in parallel with the Iterator 'current' method, below
     *
     * @return int|string
     * @throws RuntimeException
     */
    public function getCurrentPkey() {
        if( ! $this->valid()) {
            throw new RuntimeException( self::$CURRENTNOTVALID );
        }
        return array_search( $this->position, $this->pKeys, true );
    }

    /**
     * Set (i.e. reset) primary key for 'current' element
     *
     * To be used in parallel with the Iterator 'current' method, below
     *
     * @param int|string $pKey   0 (zero) allowed
     * @return static
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function setCurrentPkey( $pKey ) {
        if( ! $this->valid()) {
            throw new RuntimeException( self::$CURRENTNOTVALID );
        }
        self::assertPkey( $pKey );
        if( $this->pKeyExists( $pKey ) && ( $this->position != $this->pKeys[$pKey] )) {
            throw new InvalidArgumentException( sprintf( self::$PKEYFOUND, $pKey, $this->pKeys[$pKey] ));
        }
        return $this->setPkey( $pKey, $this->position );
    }

    /**
     * Return array sub-set of collection element (internal, int) indexes using primary keys, empty array on not found
     *
     * @param  array $pKeys
     * @return array
     */
    public function getPkeyIndexes( $pKeys = [] ) {
        $result = [];
        foreach( $pKeys as $pKey ) {
            if( ! $this->pKeyExists( $pKey )) {
                continue;
            }
            $result[] = $this->pKeys[$pKey];
        }
        return $result;
    }

    /**
     * Get-methods
     */

    /**
     * Return (non-assoc) array of element(s) in collection, opt using primary keys and/or tag(s)
     *
     * If primary keys are given, the return collection element includes only these matching the primary keys.
     *
     * @param  int|string|array $pKeys
     * @return array
     */
    public function get( $pKeys = null ) {
        if( empty( $pKeys )) {
            return $this->collection;
        }
        $indexes = $this->getPkeyIndexes((array) $pKeys );
        if( empty( $indexes )) {
            return [];
        }
        $result = [];
        foreach( $indexes as $pIx ) {
            $result[$pIx] = $this->collection[$pIx];
        }
        return $result;
    }

    /**
     * Return (non-assoc array) sub-set of element(s) in collection using primary keys
     *
     * Convenient get method alias
     *
     * @param  int|string|array $pKeys
     * @return array
     */
    public function pKeyGet( $pKeys ) {
        return $this->get( $pKeys );
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
     * @param int|string $pKey  MUST be unique
     * @return static
     * @throws InvalidArgumentException
     */
    public function append( $element, $pKey = null ) {
        $index = $this->count();
        if( null === $pKey ) {
            $pKey = $index;
        }
        self::assertPkey( $pKey );
        if( $this->pKeyExists( $pKey )) {
            throw new InvalidArgumentException( sprintf( self::$PKEYFOUND, $pKey, $this->pKeys[$pKey] ));
        }
        $this->collection[$index] = $element;
        $this->setPkey( $pKey, $index );
        $this->position = $index;
        return $this;
    }

    /**
     * Set (array) collection
     *
     * @param  array $collection
     * @return static
     * @throws InvalidArgumentException
     */
    public function setCollection( array $collection ) {
        foreach( array_keys( $collection ) as $pKey ) {
            $this->append( $collection[$pKey], $pKey );
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
    public function GetIterator() {
        return new ArrayIterator( $this->collection );
    }

    /**
     * Return an external iterator ( pKey => element ), Traversable
     *
     * @return Traversable
     */
    public function GetPkeyIterator() {
        $output = [];
        foreach( $this->pKeys as $pKey => $pIx ) {
            $output[$pKey] = $this->collection[$pIx];
        }
        return new ArrayIterator( $output );
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
     * Seeks to a given position in the iterator using pKey
     *
     * @param  int|string $pKey
     * @return static
     * @throws InvalidArgumentException
     */
    public function pKeySeek( $pKey ) {
        if( ! $this->pKeyExists( $pKey )) {
            throw new InvalidArgumentException( sprintf( self::$PKEYNOTFOUND, $pKey ));
        }
        $this->position = $this->pKeys[$pKey];
        return $this;
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