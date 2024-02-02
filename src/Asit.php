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
use InvalidArgumentException;
use Kigkonsult\Asit\Exceptions\PkeyException;
use Kigkonsult\Asit\Exceptions\PositionException;
use Kigkonsult\Asit\Exceptions\SortException;
use Traversable;

use function array_key_exists;
use function array_keys;
use function array_search;
use function get_debug_type;
use function is_int;
use function is_string;
use function intval;
use function ksort;
use function sprintf;
use function strlen;

/**
 * Class Asit extends It, allow collection element (unique) primary key
 *
 * Asit has, along with SeekableIterator, Countable and IteratorAggregate
 * (+Traversable) methods, * assoc array collection element get-/set-methods.
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
 *   assert collection elements of expected valueType
 *
 * Class Asittag extends Asit
 *   Also secondary keys, additional (non-unique) tags (aka attributes?)
 *   may be set for each element.
 *
 * Class AsittagList extends Asittag
 *   assert collection elements of expected valueType
 *
 * @package    Kigkonsult\Asit
 */
class Asit extends It
{

    /**
     * Primary keys for collection element
     *
     * @var mixed[]
     */
    protected array $pKeys = [];

    /**
     * Clear (remove) collection
     *
     * @override
     * @return static
     */
    public function init() : static
    {
        $this->pKeys = [];
        parent::init();
        return $this;
    }

    /**
     * To string
     *
     * @return string
     */
    public function toString() : string
    {
        $string = self::$SP0;
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
     * @param int|string $pKey
     * @return string
     */
    protected static function pKey2String( string $key, int | string $pKey ) : string
    {
        static $TMPL = "%s : (pKey) %s";
        return sprintf( $TMPL, $key, $pKey ) . PHP_EOL;
    }

    /**
     * Overriden It methods
     */

    /**
     * Remove the current element
     *
     * @override
     * @return static
     * @since 2.2.1 2024-01-08
     */
    public function remove() : static
    {
        parent::remove();
        $key  = $this->position;
        $pKey = array_search( $key, $this->pKeys, true );
        unset( $this->pKeys[$pKey] );
        return $this;
    }

    /**
     * Primary key methods
     */

    /**
     * Assert key/tag, int and string allowed, incl 0/'0'
     *
     * @param  mixed  $key
     * @param  string $tmpl
     * @return void
     * @throws InvalidArgumentException
     */
    protected static function assertKey( mixed $key, string $tmpl ) : void
    {
        if( is_int( $key ) && ( intval( $key ) === $key )) {
            return;
        }
        if( ! is_string( $key ) || ( empty( $key ) && ( 0 != $key ))) { // note ==
            throw new InvalidArgumentException(
                sprintf( $tmpl, get_debug_type( $key ), self::getDispVal( $key ) )
            );
        }
    }

    /**
     * Assert pKey, int and string allowed
     *
     * @param  mixed $pKey
     * @return void
     * @throws PkeyException
     */
    public static function assertPkey( mixed $pKey ) : void
    {
        static $TMPL = "Invalid primary key : (%s) %s";
        try {
            self::assertKey( $pKey, $TMPL );
        }
        catch( InvalidArgumentException $e ) {
            throw new PkeyException( $e->getMessage(), 10 );
        }
    }

    /**
     * Return bool true if a primary key is found
     *
     * @param int|string $pKey
     * @return bool
     */
    public function pKeyExists( int | string $pKey ) : bool
    {
        return array_key_exists( $pKey, $this->pKeys );
    }

    /**
     * Assert pKey exists
     *
     * @param int|string $pKey 0 (zero) allowed
     * @return void
     * @throws PkeyException
     */
    protected function assertPkeyExists( int | string $pKey ) : void
    {
        if( ! $this->pKeyExists( $pKey )) {
            throw new PkeyException(
                sprintf( PkeyException::$PKEYNOTFOUND1, $pKey )
            );
        }
    }

    /**
     * Assert pKey not exists
     *
     * @param int|string $pKey 0 (zero) allowed
     * @return void
     * @throws PkeyException
     */
    protected function assertPkeyNotExists( int | string $pKey ) : void
    {
        if( $this->pKeyExists( $pKey )) {
            throw new PkeyException(
                sprintf( PkeyException::$PKEYFOUND, $pKey, $this->pKeys[$pKey] )
            );
        }
    }

    /**
     * Return all primary keys
     *
     * @param null|int|string $dummy
     * @param null|int $sortFlag default SORT_REGULAR
     * @return int[]|string[]
     */
    public function getPkeys( null|int|string $dummy = null, ? int $sortFlag = SORT_REGULAR ) : array
    {
        return self::sort( array_keys( $this->pKeys ), $sortFlag );
    }

    /**
     * Set (or reset) unique primary key for a collection element
     *
     * @param int|string $pKey      0 (zero) allowed
     * @param int        $position  exist as key in collection
     * @return static
     * @throws PkeyException
     */
    protected function setPkey( int | string $pKey, int $position ) : static
    {
        self::assertPkey( $pKey );
        switch( true ) {
            case ( ! $this->pKeyExists( $pKey )) :
                break;
            case ( $position === $this->pKeys[$pKey] ) :
                return $this;
            default :
                throw new PkeyException(
                    sprintf( PkeyException::$PKEYFOUND, $pKey, $this->pKeys[$pKey] )
                );
        } // end switch
        try {
            $oldKey = self::search( $position, $this->pKeys ); // find the old pKey
            unset( $this->pKeys[$oldKey] );                    // remove the old pKey
        }
        catch( PositionException ) {} // not found
        $this->pKeys[$pKey] = $position;
        ksort( $this->pKeys );
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
    public function replacePkey( int | string $oldPkey, int | string $newPkey ) : static
    {
        if( $oldPkey === $newPkey ) {
            return $this;
        }
        $this->assertPkeyExists( $oldPkey );
        self::assertPkey( $newPkey );
        $this->assertPkeyNotExists( $newPkey );
        $this->setPkey( $newPkey, $this->pKeys[$oldPkey] );
        unset( $this->pKeys[$oldPkey] );
        return $this;
    }

    /**
     * Return (first found) pKey for 'current' element
     *
     * @return int|string
     * @throws PositionException
     */
    public function getCurrentPkey() : int|string
    {
        $this->assertCurrent();
        return self::search( $this->position, $this->pKeys );
    }

    /**
     * Set (reset) primary key for 'current' element
     *
     * To be used in parallel with the Iterator 'current' method, below
     *
     * @param int|string $pKey   0 (zero) allowed
     * @return static
     * @throws PkeyException
     * @throws PositionException
     */
    public function setCurrentPkey( int | string $pKey ) :static
    {
        $this->assertCurrent();
        return $this->setPkey( $pKey, $this->position );
    }

    /**
     * Return all or sub-set of collection element (internal) indexes using primary keys
     *
     * Return empty array on not found
     *
     * @param  int[]|string[] $pKeys
     * @return int[]
     */
    public function getPkeyIndexes( array $pKeys ) : array
    {
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
     * Return (non-assoc) array of element(s) in collection
     *
     * Opt using primary keys and/or tag(s) for selection
     *
     * @param int|string|int[]|string[] $pKeys
     * @param null|int|callable $sortParam asort sort_flags or uasort callable
     *                                     (null, default, ksort)
     * @return int[]|string[]
     * @throws SortException
     */
    public function pKeyGet( int|string|array $pKeys = null, mixed $sortParam = null ) : array
    {
        if( empty( $pKeys )) {
            return self::sort( $this->collection, $sortParam );
        }
        $indexes = $this->getPkeyIndexes((array) $pKeys );
        if( empty( $indexes )) {
            return [];
        }
        $this->copyElements( $indexes, $result );
        return self::sort( $result, $sortParam );
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
     * @param mixed                 $element
     * @param null|int|string       $pKey  MUST be unique
     * @param null|int|string|int[]|string[] $tags  not used here
     * @return static
     * @throws PkeyException
     */
    public function append(
        mixed $element,
        null|int|string $pKey = null,
        null|int|string|array $tags = null
    ) : static
    {
        $index = $this->count();
        if( null === $pKey ) {
            $pKey = $index;
        }
        else {
            self::assertPkey( $pKey );
            $this->assertPkeyNotExists( $pKey );
        }
        $this->collection[$index] = $element;
        $this->position = $index;
        $this->setPkey( $pKey, $index );
        return $this;
    }

    /**
     * Set (array) collection using array key as primary key
     *
     * @override
     * @param mixed[] $collection
     * @return static
     * @throws PkeyException
     */
    public function setCollection( iterable $collection ) : static
    {
        switch( true ) {
            case is_array( $collection ) :
                foreach( array_keys( $collection ) as $cIx ) {
                    $this->append( $collection[$cIx], $cIx );
                }
                break;
            case ( $collection instanceof self ) :
                foreach( $collection->getPkeyIterator() as $cIx => $element ) {
                    $this->append( $element, $cIx );
                }
                break;
            default :
                foreach( $collection as $cIx => $element ) {
                    $this->append( $element, $cIx );
                }
                break;
        } // end switch
        return $this;
    }
    /**
     * SeekableIterator, Countable, IteratorAggregate et al. methods
     */

    /**
     * Return an external iterator ( pKey => element ), Traversable
     *
     * @return Traversable   mixed[]
     */
    public function getPkeyIterator() : Traversable
    {
        $output = [];
        foreach( $this->pKeys as $pKey => $pIx ) {
            $output[$pKey] = $this->collection[$pIx];
        }
        return new ArrayIterator( $output );
    }

    /**
     * Seeks to a given position in the iterator using pKey, i.e. make current
     *
     * @param int|string $pKey
     * @return static
     * @throws PkeyException
     */
    public function pKeySeek( int | string $pKey ) : static
    {
        $this->assertPkeyExists( $pKey );
        $this->position = (int) $this->pKeys[$pKey];
        return $this;
    }
}
