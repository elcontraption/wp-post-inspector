<?php namespace ElContraption\PostInspector;

class PostInspectorCollection {

    /**
     * The current PostInspector object
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

    /**
     * Make a new PostInspectorCollection
     *
     * @param PostInspector $current    The current PostInspector object
     * @param array $items              PostInspector objects
     */
    public function __construct($current, $items)
    {
        $this->current = $current;
        $this->items = $items;
    }

    /**
     * Return an array of items within the collection
     *
     * @return array Collection items
     */
    public function items()
    {
        return $this->items;
    }

}
