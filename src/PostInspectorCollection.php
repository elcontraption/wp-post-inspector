<?php namespace ElContraption\PostInspector;

class PostInspectorCollection {

    /**
     * The current Post Inspector object
     *
     * @var PostInspector
     */
    protected $current;

    /**
     * Array of Post Inspector objects
     *
     * @var array
     */
    protected $items;

    public function __construct($current, $items)
    {
        $this->current = $current;
        $this->items = $items;
    }

}
