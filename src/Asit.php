<?php
/**
 * Asit package manages array collections
 *
 * This file is part of Asit.
 *
 * Support <https://github.com/iCalcreator/Asit>
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-21 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @link      https://kigkonsult.se
 * @version   1.6
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
namespace Kigkonsult\Asit;

use ArrayIterator;
use InvalidArgumentException;
use Kigkonsult\Asit\Exceptions\CollectionException;
use Kigkonsult\Asit\Exceptions\PkeyException;
use Kigkonsult\Asit\Exceptions\SortException;
use RuntimeException;
use Traversable;

use function array_key_exists;
use function array_keys;
use function is_int;
use function is_string;
use function ksort;
use function sort;
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
     * @var array
     */
    protected $pKeys = [];

    /**
     * Clear (remove) collection
     *
     * @override
     * @return static
     */
    public function init() : BaseInterface
    {
        $this->pKeys = [];
        return parent::init();
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
            $string .= self::pKey2String(
                $key,
                (string) ( $this->getCurrentPkey() ?: self::$SP0 )
            );
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
    protected static function pKey2String( string $key, $pKey ) : string
    {
        static $TMPL = "%s : (pKey) %s";
        return sprintf( $TMPL, $key, $pKey ) . PHP_EOL;
    }

    /**
     * Primary key methods
     */

    /**
     * Assert key/tag, int and string allowed
     *
     * @param  mixed  $key
     * @param  string $tmpl
     * @return void
     * @throws InvalidArgumentException
     */
    protected static function assertKey( $key, string $tmpl )
    {
        if( is_int( $key ) || ( is_string( $key ) && ! empty( $key ))) {
            return;
        }
        throw new InvalidArgumentException(
            sprintf( $tmpl, self::getErrType( $key ), self::getDispVal( $key ))
        );
    }

    /**
     * Assert pKey, int and string allowed
     *
     * @param  mixed $pKey
     * @return void
     * @throws PkeyException
     */
    public static function assertPkey( $pKey )
    {
        static $TMPL = "Invalid primary key : (%s) %s";
        try {
            self::assertKey( $pKey, $TMPL );
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
    public function pKeyExists( $pKey ) : bool
    {
        return array_key_exists( $pKey, $this->pKeys );
    }

    /**
     * Assert pKey exists
     *
     * @param int|string $pKey 0 (zero) allowed
     * @return void
     * @throw PkeyException
     */
    protected function assertPkeyExists( $pKey )
    {
        if( ! $this->pKeyExists( $pKey )) {
            throw new PkeyException(
                sprintf( PkeyException::$PKEYNOTFOUND, $pKey )
            );
        }
    }

    /**
     * Assert pKey not exists
     *
     * @param int|string $pKey 0 (zero) allowed
     * @return void
     * @throw PkeyException
     */
    protected function assertPkeyNotExists( $pKey )
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
     * @param int $sortFlag  default SORT_REGULAR
     * @param mixed $dummy
     * @return array
     */
    public function getPkeys( $sortFlag = SORT_REGULAR, $dummy = null ) : array
    {
        $pKeys = array_keys( $this->pKeys );
        if( $sortFlag != SORT_REGULAR ) {
            sort( $pKeys, $sortFlag );
        }
        return $pKeys;
    }

    /**
     * Set (or reset) unique primary key for a collection element
     *
     * @param int|string $pKey 0 (zero) allowed
     * @param int        $index
     * @return static
     * @throws PkeyException
     */
    protected function setPkey( $pKey, int $index ) : BaseInterface
    {
        self::assertPkey( $pKey );
        switch( true ) {
            case ( ! $this->pKeyExists( $pKey )) :
                break;
            case ( $index == $this->pKeys[$pKey] ) :
                return $this;
            default :
                throw new PkeyException(
                    sprintf( PkeyException::$PKEYFOUND, $pKey, $this->pKeys[$pKey] )
                );
        } // end switch
        if( false !== ( $foundKey = self::search( $index, $this->pKeys ))) {
            unset( $this->pKeys[$foundKey] );
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
    public function replacePkey( $oldPkey, $newPkey ) : self
    {
        $this->assertPkeyExists( $oldPkey );
        self::assertPkey( $newPkey );
        if( $oldPkey == $newPkey ) {
            return $this;
        }
        $this->assertPkeyNotExists( $newPkey );
        $this->setPkey( $newPkey, $this->pKeys[$oldPkey] );
        unset( $this->pKeys[$oldPkey] );
        return $this;
    }

    /**
     * Return pKey for "current'
     *
     * To be used in parallel with the Iterator 'current' method, below
     *
     * @return bool|int|string
     * @throws RuntimeException
     */
    public function getCurrentPkey()
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
     * @throws RuntimeException
     */
    public function setCurrentPkey( $pKey ) : self
    {
        $this->assertCurrent();
        return $this->setPkey( $pKey, $this->position );
    }

    /**
     * Return all or sub-set of collection element (internal) indexes using primary keys
     *
     * Return empty array on not found
     *
     * @param  array $pKeys
     * @return array
     */
    public function getPkeyIndexes( array $pKeys = [] ) : array
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
     * @param  int|string|array $pKeys
     * @param  int|callable     $sortParam  asort sort_flags or uasort callable
     * @return array
     * @throws SortException
     */
    public function get( $pKeys = null, $sortParam = null ) : array
    {
        if( empty( $pKeys )) {
            return ( null === $sortParam )
                ? $this->collection
                : self::sort( $this->collection, $sortParam );
        }
        $indexes = $this->getPkeyIndexes((array) $pKeys );
        if( empty( $indexes )) {
            return [];
        }
        $result = [];
        foreach( $indexes as $pIx ) {
            $result[$pIx] = $this->collection[$pIx];
        }
        return ( null === $sortParam )
            ? $result
            : self::sort( $result, $sortParam );
    }

    /**
     * Return (non-assoc) sub-set of element(s) in collection using primary keys
     *
     * Convenient get method alias
     *
     * @param  int|string|array $pKeys
     * @param  int|callable     $sortParam  asort sort_flags or uasort callable
     * @return array
     * @throws SortException
     */
    public function pKeyGet( $pKeys, $sortParam = null ) : array
    {
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
     * @param mixed      $element
     * @param int|string $pKey    MUST be unique
     * @return static
     * @throws PkeyException
     */
    public function append( $element, $pKey = null ) : BaseInterface
    {
        $index = $this->count();
        if( null === $pKey ) {
            $pKey = $index;
        }
        else {
            self::assertPkey( $pKey );
            $this->assertPkeyNotExists( $pKey );
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
     * @throws CollectionException
     * @throws PkeyException
     */
    public function setCollection( $collection ) : BaseInterface
    {
        switch( true ) {
            case is_array( $collection ) :
                foreach( array_keys( $collection ) as $cIx ) {
                    $this->append( $collection[$cIx], $cIx );
                }
                break;
            case ( ! ( $collection instanceof Traversable )) :
                throw new CollectionException(
                    sprintf( CollectionException::$ERRTXT, self::getErrType( $collection ))
                );
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
        } // end switch
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
     * @param  int|string $pKey
     * @return static
     * @throws PkeyException
     */
    public function pKeySeek( $pKey ) : self
    {
        $this->assertPkeyExists( $pKey );
        $this->position = $this->pKeys[$pKey];
        return $this;
    }
}
