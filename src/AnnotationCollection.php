<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\AnnotationReader;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use RuntimeException;
use Traversable;

/**
 * Class AnnotationCollection
 *
 * Stores [@see Annotation]s in a way that gracefully handles rules for
 * multiple annotations.
 *
 * @author Nate Brunette <n@tebru.net>
 */
class AnnotationCollection implements IteratorAggregate, Countable
{
    /**
     * All [@se Annotation] objects
     *
     * @var AbstractAnnotation[]|AbstractAnnotation[][]
     */
    private $annotations = [];

    /**
     * Create new collection from array
     *
     * @param array $annotations
     * @return AnnotationCollection
     */
    public static function createFromArray(array $annotations)
    {
        $collection = new AnnotationCollection();
        $collection->addArray($annotations);

        return $collection;
    }

    /**
     * Create new collection from collection
     *
     * @param AnnotationCollection $annotations
     * @return AnnotationCollection
     */
    public static function createFromCollection(AnnotationCollection $annotations)
    {
        $collection = new AnnotationCollection();
        $collection->addCollection($annotations);

        return $collection;
    }

    /**
     * Returns true if the annotation is in the collection
     *
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return isset($this->annotations[$name]);
    }

    /**
     * Get a single annotation by name
     *
     * @param string $name
     * @return AbstractAnnotation|null
     * @throws \RuntimeException
     */
    public function get(string $name): ?AbstractAnnotation
    {
        if (!$this->exists($name)) {
            return null;
        }

        if (!$this->annotations[$name] instanceof AbstractAnnotation) {
            throw new RuntimeException(sprintf('Multiple values available for "%s". Use getAll() instead.', $name));
        }

        return $this->annotations[$name];
    }

    /**
     * Get all annotations
     *
     * @param string $name
     * @return AbstractAnnotation[]|null
     * @throws \RuntimeException
     */
    public function getAll(string $name): ?array
    {
        if (!$this->exists($name)) {
            return null;
        }

        if (!is_array($this->annotations[$name])) {
            throw new RuntimeException(sprintf('Only one annotation available for "%s". Use get() instead.', $name));
        }

        return $this->annotations[$name];
    }

    /**
     * Add an annotation by name
     *
     * If multiple annotations of this type are allowed, store in array
     *
     * @param AbstractAnnotation $annotation
     */
    public function add(AbstractAnnotation $annotation): void
    {
        $allowMultiple = $annotation->allowMultiple();
        $name = $annotation->getName();
        $exists = $this->exists($name);

        if (!$allowMultiple && $exists) {
            return;
        }

        if (!$allowMultiple) {
            $this->annotations[$name] = $annotation;
            return;
        }

        if (!$exists) {
            $this->annotations[$name] = [];
        }

        $this->annotations[$name][] = $annotation;
    }

    /**
     * Add all annotations from array
     *
     * Any duplicate annotations from the provided array that are
     * not allowed will be ignored
     *
     * @param AbstractAnnotation[] $annotations
     */
    public function addArray(array $annotations)
    {
        foreach ($annotations as $annotation) {
            $this->add($annotation);
        }
    }

    /**
     * Add a collection to current collection
     *
     * Any duplicate annotations from the provided collection that are
     * not allowed will be ignored
     *
     * @param AnnotationCollection $collection
     */
    public function addCollection(AnnotationCollection $collection): void
    {
        foreach ($collection as $element) {
            if ($element instanceof AbstractAnnotation) {
                $this->add($element);
                continue;
            }

            /** @var AbstractAnnotation[] $element */
            foreach ($element as $annotation) {
                $this->add($annotation);
            }
        }
    }

    /**
     * Return annotations as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->annotations;
    }

    /**
     * Retrieve an external iterator
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->annotations);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->annotations);
    }
}
