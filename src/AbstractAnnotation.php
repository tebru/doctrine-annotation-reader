<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\AnnotationReader;

use RuntimeException;

/**
 * Class Annotation
 *
 * @author Nate Brunette <n@tebru.net>
 */
abstract class AbstractAnnotation
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Constructor
     *
     * @param array $data
     * @throws \RuntimeException
     */
    final public function __construct(array $data)
    {
        $this->data = $data;

        $this->init();
    }

    /**
     * Initialize annotation data
     *
     * @throws \RuntimeException
     */
    protected function init(): void
    {
        $this->assertKey();
    }

    /**
     * Returns true if multiple annotations of this type are allowed
     *
     * @return bool
     */
    public function allowMultiple(): bool
    {
        return false;
    }

    /**
     * Returns the name the annotation should be referenced by, defaults
     * to classname
     *
     * @return string
     */
    public function getName(): string
    {
        return static::class;
    }

    /**
     * Get the default value
     *
     * @throws \RuntimeException
     */
    public function getValue()
    {
        $this->assertKey();

        return $this->data['value'];
    }

    /**
     * Assert that the key exists in the data array
     *
     * @param string $key
     * @throws \RuntimeException
     */
    protected function assertKey(string $key = 'value')
    {
        if (!isset($this->data[$key])) {
            throw new RuntimeException(sprintf('Key "%s" not found in annotation data', $key));
        }
    }
}
