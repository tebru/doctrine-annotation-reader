<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\AnnotationReader\Test;

use PHPUnit_Framework_TestCase;
use RuntimeException;
use Tebru\AnnotationReader\AnnotationCollection;
use Tebru\AnnotationReader\Test\Mock\Annotation\BaseClassAnnotation;
use Tebru\AnnotationReader\Test\Mock\Annotation\MultipleAllowedAnnotation;

class AnnotationCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AnnotationCollection
     */
    private $collection;

    public function setUp()
    {
        $this->collection = new AnnotationCollection();
    }

    public function testExists()
    {
        $this->collection->add(new BaseClassAnnotation(['value' => 'foo']));

        self::assertTrue($this->collection->exists(BaseClassAnnotation::class));
        self::assertFalse($this->collection->exists('foo'));
    }

    public function testAddSingle()
    {
        $this->collection->add(new BaseClassAnnotation(['value' => 'foo']));

        self::assertSame('foo', $this->collection->get(BaseClassAnnotation::class)->getValue());
    }

    public function testAddMultipleNotAllowed()
    {
        $this->collection->add(new BaseClassAnnotation(['value' => 'foo']));
        $this->collection->add(new BaseClassAnnotation(['value' => 'bar']));

        self::assertSame('foo', $this->collection->get(BaseClassAnnotation::class)->getValue());
    }

    public function testAddMultiple()
    {
        $this->collection->add(new MultipleAllowedAnnotation(['value' => 'foo']));
        $this->collection->add(new MultipleAllowedAnnotation(['value' => 'bar']));
        $annotations = $this->collection->getAll(MultipleAllowedAnnotation::class);

        self::assertSame('foo', $annotations[0]->getValue());
        self::assertSame('bar', $annotations[1]->getValue());
    }

    public function testCreateSingleFromArray()
    {
        $this->collection = AnnotationCollection::createFromArray([new BaseClassAnnotation(['value' => 'foo'])]);

        self::assertSame('foo', $this->collection->get(BaseClassAnnotation::class)->getValue());
    }

    public function testCreateMultipleFromArray()
    {
        $this->collection = AnnotationCollection::createFromArray([
            new MultipleAllowedAnnotation(['value' => 'foo']),
            new MultipleAllowedAnnotation(['value' => 'bar']),
        ]);

        $annotations = $this->collection->getAll(MultipleAllowedAnnotation::class);

        self::assertSame('foo', $annotations[0]->getValue());
        self::assertSame('bar', $annotations[1]->getValue());
    }

    public function testCreateSingleFromCollection()
    {
        $this->collection = AnnotationCollection::createFromCollection(
            AnnotationCollection::createFromArray([new BaseClassAnnotation(['value' => 'foo'])])
        );

        self::assertSame('foo', $this->collection->get(BaseClassAnnotation::class)->getValue());
    }

    public function testCreateMultipleFromCollection()
    {
        $this->collection = AnnotationCollection::createFromCollection(
            AnnotationCollection::createFromArray([
                new MultipleAllowedAnnotation(['value' => 'foo']),
                new MultipleAllowedAnnotation(['value' => 'bar']),
            ])
        );

        $annotations = $this->collection->getAll(MultipleAllowedAnnotation::class);

        self::assertSame('foo', $annotations[0]->getValue());
        self::assertSame('bar', $annotations[1]->getValue());
    }

    public function testAddNonAbstractAnnotation()
    {
        $this->collection->addArray([new BaseClassAnnotation(['value' => 'foo']), new \stdClass()]);

        self::assertCount(1, $this->collection);
        self::assertSame('foo', $this->collection->get(BaseClassAnnotation::class)->getValue());
    }

    public function testGetNotExists()
    {
        $this->collection->add(new BaseClassAnnotation(['value' => 'foo']));

        self::assertNull($this->collection->get('foo'));
    }

    public function testGetArrayThrowsException()
    {
        $this->collection->add(new MultipleAllowedAnnotation(['value' => 'foo']));

        try {
            $this->collection->get(MultipleAllowedAnnotation::class);
        } catch (RuntimeException $exception) {
            self::assertSame('Multiple values available for "Tebru\AnnotationReader\Test\Mock\Annotation\MultipleAllowedAnnotation". Use getAll() instead.', $exception->getMessage());
            return;
        }

        self::assertTrue(false);
    }

    public function testGetAllNotExists()
    {
        $this->collection->add(new MultipleAllowedAnnotation(['value' => 'foo']));

        self::assertNull($this->collection->getAll('foo'));
    }

    public function testGetAnnotationThrowsException()
    {
        $this->collection->add(new BaseClassAnnotation(['value' => 'foo']));

        try {
            $this->collection->getAll(BaseClassAnnotation::class);
        } catch (RuntimeException $exception) {
            self::assertSame('Only one annotation available for "Tebru\AnnotationReader\Test\Mock\Annotation\BaseClassAnnotation". Use get() instead.', $exception->getMessage());
            return;
        }

        self::assertTrue(false);
    }

    public function testCount()
    {
        $this->collection->add(new BaseClassAnnotation(['value' => 'foo']));

        self::assertCount(1, $this->collection);
    }
}
