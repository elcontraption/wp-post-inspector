<?php namespace ElContraption\PostInspector;

class PostInspectorCollection {

    /**
     * The current PostInspector object
     *
     * @var PostInspector
     */
    public $current;

    /**
     * Array of Post Inspector objects
     *
     * @var array
     */
    public $items;

    /**
     * Make a new PostInspectorCollection
     * @param PostInspector $current    The current PostInspector object
     * @param array $items              PostInspector objects
     */
    public function __construct($current, $items)
    {
        $this->current = $current;
        $this->items = $items;
    }

}
