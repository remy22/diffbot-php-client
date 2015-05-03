<?php

namespace Swader\Diffbot\Entity;

use GuzzleHttp\Message\ResponseInterface as Response;
use Swader\Diffbot\Abstracts\Entity;

class EntityIterator implements \Countable, \Iterator
{
    /** @var  array */
    protected $data;
    /** @var  int */
    protected $cursor = -1;
    /** @var  Entity */
    protected $current;
    /** @var  Response */
    protected $response;

    public function __construct(array $objects, Response $response)
    {
        $this->response = $response;
        $this->data = $objects;
        $this->next();
    }

    /**
     * Returns the original response
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function count()
    {
        return count($this->data);
    }

    public function rewind()
    {
        if ($this->cursor > 0) {
            $this->cursor = -1;
            $this->next();
        }
    }

    public function key()
    {
        return $this->cursor;
    }

    public function current()
    {
        return $this->data[$this->cursor];
    }

    public function next()
    {
        $this->cursor++;
    }

    public function valid()
    {
        return ($this->cursor < $this->count());
    }

    public function __call($name, $args)
    {
        $isGetter = substr($name, 0, 3) == 'get';
        if ($isGetter) {
            $property = lcfirst(substr($name, 3, strlen($name) - 3));

            return $this->$property;
        }

        throw new \BadMethodCallException('No such method: ' . $name);
    }

    public function __get($name)
    {
        $entity = ($this->cursor == -1) ? $this->data[0] : $this->current();

        return $entity->$name;
    }
}