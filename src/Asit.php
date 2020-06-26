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
use InvalidArgumentException;
use Kigkonsult\Asit\Exceptions\PkeyException;
use Kigkonsult\Asit\Exceptions\SortException;
use RuntimeException;
use Traversable;

use function array_key_exists;
use function array_keys;
use function array_search;
use function is_int;
use function is_string;
use function ksort;
use function sort;
use function sprintf;
use function strlen;
use function var_export;

/**
 * Class Asit extends It, allow collection element (unique) primary key
 *
 * Asit has, along with SeekableIterator, Countable and IteratorAggregate (+Traversable) methods,
 * assoc array collection element get-/set-methods.
 * The assoc element array key is used as (unique) primary key.
 *
 * The collection element may, as for Iterator (et al.), be of any valueType.
 *
 * A primary key may be replaced by another (unique) key.
 *
 * Collection elements are searchable using
 *   Iterator (et al.) methods
 *   primary key(s)
 *
 * For non-assoc arrays,
 *   primary key is the (numeric) array index
 *
 * Class AsmitList extends Asit
 *   Each collection element may have more than one primary key.
 *
 * Class AsitList extends Asit
 *   assure collection elements of expected valueType
 *
 * Class Asittag extends Asit
 *   Also secondary keys, additional (non-unique) tags (aka attributes?)
 *   may be set for each element.
 *
 * Class AsittagList extends Asittag
 *   assure collection elements of expected valueType
 *
 * @package    Kigkonsult\Asit
 */
class Asit
     extends It
{

    /**
     * Error texts
     *
     * @var string
     */
    protected static $CURRENTNOTVALID = 'Current not valid';

    /**
     * Primary keys for collection element
     *
     * @var array
     */
    protected $pKeys = [];

    /**
     * Clear (remove) collection
     *
     * @override
     * @return static
     */
    public function init() {
        $this->pKeys = [];
        return parent::init();
    }

    /**
     * toString
     *
     * @return string
     */
    public function toString() {
        static $SP0   = '';
        $string = $SP0;
        $pLen   = strlen((string) $this->count());
        $this->rewind();
        while( $this->valid()) {
            $key     = self::prepKeyString( $this->key(), $pLen );
            $string .= self::pKey2String( $key, $this->getCurrentPkey());
            $string .= self::element2String( $key, $this->current());
            $this->next();
        }
        return $string;
    }

    /**
     * Return key and pKey as string
     *
     * @param string $key
     * @param string $pKey
     * @return string
     */
    protected static function pKey2String( $key, $pKey ) {
        static $ROWpk = '%s : (pKey) %s ';
        return sprintf( $ROWpk, $key, $pKey ) . PHP_EOL;
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
     * @throws InvalidArgumentException
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
     * @throws PkeyException
     */
    public static function assertPkey( $pKey ) {
        static $ERR = 'Invalid primary key : %s';
        try {
            self::assertKey( $pKey, $ERR );
        }
        catch( InvalidArgumentException $e ) {
            throw new PkeyException( $e->getMessage(), 10, $e );
        }
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
     * @param int $sortFlag  default SORT_REGULAR
     * @return array
     */
    public function getPkeys( $sortFlag = SORT_REGULAR ) {
        $pKeys = array_keys( $this->pKeys );
        if( $sortFlag != SORT_REGULAR ) {
            sort( $pKeys, $sortFlag );
            return $pKeys;
        }
        return $pKeys;
    }

    /**
     * Set (or reset) the unique primary key for a collection element
     *
     * @param int|string $pKey 0 (zero) allowed
     * @param int        $index
     * @return static
     * @throws PkeyException
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
     * @throws PkeyException
     */
    public function replacePkey( $oldPkey, $newPkey ) {
        if( ! $this->pKeyExists( $oldPkey )) {
            throw new PkeyException( sprintf( PkeyException::$PKEYNOTFOUND, $oldPkey ));
        }
        self::assertPkey( $newPkey );
        if( $oldPkey == $newPkey ) {
            return $this;
        }
        if( $this->pKeyExists( $newPkey )) {
            throw new PkeyException( sprintf( PkeyException::$PKEYFOUND, $newPkey, $this->pKeys[$newPkey] ));
        }
        $this->setPkey( $newPkey, $this->pKeys[$oldPkey] );
        $list = [];
        foreach( $this->pKeys as $pKey2 => $index2 ) {
            if( $oldPkey != $pKey2 ) {
                $list[$pKey2] = $index2;
            }
        }
        $this->pKeys = $list;
        return $this;
    }

    /**
     * Return pKey for 'current'
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
     * @throws PkeyException
     * @throws RuntimeException
     */
    public function setCurrentPkey( $pKey ) {
        if( ! $this->valid()) {
            throw new RuntimeException( self::$CURRENTNOTVALID );
        }
        self::assertPkey( $pKey );
        if( $this->pKeyExists( $pKey )) {
            if( $this->position != $this->pKeys[$pKey] ) {
                throw new PkeyException( sprintf( PkeyException::$PKEYFOUND, $pKey, $this->pKeys[$pKey] ) );
            }
            return $this;
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
            if( $this->pKeyExists( $pKey )) {
                $pIndex          = $this->pKeys[$pKey];
                $result[$pIndex] = $pIndex;
            }
        } // end foreach
        return $result;
    }

    /**
     * Get-methods
     */

    /**
     * Return (non-assoc) array of element(s) in collection, opt using primary keys and/or tag(s)
     *
     * Using the opt. primary keys for selection
     *
     * @param  int|string|array $pKeys
     * @param  int|callable $sortParam    asort sort_flags or uasort callable
     * @return array
     * @throws SortException
     */
    public function get( $pKeys = null, $sortParam = null ) {
        if( empty( $pKeys )) {
            return ( null === $sortParam ) ? $this->collection : self::sort( $this->collection, $sortParam );
        }
        $indexes = $this->getPkeyIndexes((array) $pKeys );
        if( empty( $indexes )) {
            return [];
        }
        $result = [];
        foreach( $indexes as $pIx ) {
            $result[$pIx] = $this->collection[$pIx];
        }
        return ( null === $sortParam ) ? $result : self::sort( $result, $sortParam );
    }

    /**
     * Return (non-assoc array) sub-set of element(s) in collection using primary keys
     *
     * Convenient get method alias
     *
     * @param  int|string|array $pKeys
     * @param  int|callable $sortParam    asort sort_flags or uasort callable
     * @return array
     * @throws SortException
     */
    public function pKeyGet( $pKeys, $sortParam = null ) {
        return $this->get( $pKeys, $sortParam );
    }

    /**
     * Set-methods
     */

    /**
     * Append element to (array) collection, opt with primary key
     *
     * Note, last appended element is always 'current'
     *
     * @override
     * @param mixed $element
     * @param int|string $pKey  MUST be unique
     * @return static
     * @throws PkeyException
     */
    public function append( $element, $pKey = null ) {
        $index = $this->count();
        if( null === $pKey ) {
            $pKey = $index;
        }
        self::assertPkey( $pKey );
        if( $this->pKeyExists( $pKey )) {
            throw new PkeyException( sprintf( PkeyException::$PKEYFOUND, $pKey, $this->pKeys[$pKey] ));
        }
        $this->setPkey( $pKey, $index );
        $this->collection[$index] = $element;
        $this->position = $index;
        return $this;
    }

    /**
     * Set (array) collection using array key as primary key
     *
     * @override
     * @param  array|Traversable $collection
     * @return static
     * @throws InvalidArgumentException
     * @throws PkeyException
     */
    public function setCollection( $collection ) {
        switch( true ) {
            case is_array( $collection ) :
                foreach( array_keys( $collection ) as $cIx ) {
                    $this->append( $collection[$cIx], $cIx );
                }
                break;
            case ( ! ( $collection instanceof Traversable )) :
                throw new InvalidArgumentException( sprintf( self::$ERRSETTXT, gettype( $collection )));
                break;
            case ( $collection instanceof Asit ) :
                foreach( $collection->getPkeyIterator() as $cIx => $element ) {
                    $this->append( $element, $cIx );
                }
                break;
            default :
                foreach( $collection as $cIx => $element ) {
                    $this->append( $element, $cIx );
                }
                break;
        }
        return $this;
    }
    /**
     * SeekableIterator, Countable, IteratorAggregate et al. methods
     */

    /**
     * Return an external iterator ( pKey => element ), Traversable
     *
     * @return Traversable
     */
    public function getPkeyIterator() {
        $output = [];
        foreach( $this->pKeys as $pKey => $pIx ) {
            $output[$pKey] = $this->collection[$pIx];
        }
        return new ArrayIterator( $output );
    }

    /**
     * Seeks to a given position in the iterator using pKey
     *
     * @param  int|string $pKey
     * @return static
     * @throws PkeyException
     */
    public function pKeySeek( $pKey ) {
        if( ! $this->pKeyExists( $pKey )) {
            throw new PkeyException( sprintf( PkeyException::$PKEYNOTFOUND, $pKey ));
        }
        $this->position = $this->pKeys[$pKey];
        return $this;
    }

}
