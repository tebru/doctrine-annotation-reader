<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\AnnotationReader\Test;

use PHPUnit_Framework_TestCase;
use RuntimeException;
use Tebru\AnnotationReader\Test\Mock\Annotation\BaseClassAnnotation;
use Tebru\AnnotationReader\Test\Mock\Annotation\ValueOverridingAnnotation;

class AbstractAnnotationTest extends PHPUnit_Framework_TestCase
{
    public function testRequiredValueMissingThrowsException()
    {
        try {
            new BaseClassAnnotation([]);
        } catch (RuntimeException $exception) {
            self::assertSame('Default value not provided for Tebru\AnnotationReader\Test\Mock\Annotation\BaseClassAnnotation annotation', $exception->getMessage());
            return;
        }

        self::assertTrue(false);
    }

    public function testCanOverrideValue()
    {
        $annotation = new ValueOverridingAnnotation(['foo' => 'bar']);

        self::assertSame('bar', $annotation->getValue());
    }
}
