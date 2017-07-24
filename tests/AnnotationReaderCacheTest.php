<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\AnnotationReader\Test;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use PHPUnit_Framework_TestCase;
use Tebru\AnnotationReader\AnnotationReaderAdapter;
use Tebru\AnnotationReader\Test\Mock\Annotation\BaseClassAnnotation;

class AnnotationReaderCacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CacheProvider
     */
    private $cache;

    /**
     * @var AnnotationReaderAdapter
     */
    private $reader;

    public function setUp()
    {
        $this->cache = new ArrayCache();
        $this->reader = new AnnotationReaderAdapter(new AnnotationReader(), $this->cache);
    }

    public function testReadClassUsesCache()
    {
        $annotation = new BaseClassAnnotation(['value' => 'foo']);
        $this->cache->save('foo', [BaseClassAnnotation::class => $annotation]);
        $result = $this->reader->readClass('foo', false)->get(BaseClassAnnotation::class);

        self::assertSame($annotation, $result);
    }

    public function testReadMethodUsesCache()
    {
        $annotation = new BaseClassAnnotation(['value' => 'foo']);
        $this->cache->save('foofoo', [BaseClassAnnotation::class => $annotation]);
        $result = $this->reader->readMethod('foo', 'foo', false, false)->get(BaseClassAnnotation::class);

        self::assertSame($annotation, $result);
    }

    public function testReadPropertyUsesCache()
    {
        $annotation = new BaseClassAnnotation(['value' => 'foo']);
        $this->cache->save('foofoo', [BaseClassAnnotation::class => $annotation]);
        $result = $this->reader->readProperty('foo', 'foo', false, false)->get(BaseClassAnnotation::class);

        self::assertSame($annotation, $result);
    }
}
