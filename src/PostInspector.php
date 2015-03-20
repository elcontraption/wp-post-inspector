<?php namespace ElContraption\PostInspector;

use WP_Post;
use WP_Query;

class PostInspector {

    /**
     * The original WP_Post object
     *
     * @var \WP_Post
     */
    private $post;

    /**
     * Constructor
     *
     * @param mixed $post Post object, id, slug, or null
     */
    public function __construct($post = null)
    {
        $this->post = $this->getPostObject($post);
    }

    /**
     * Get post permalink
     *
     * @return string Post permalink
     */
    public function permalink()
    {
        return get_permalink($this->post->id());
    }

    /**
     * Get the parent post object
     *
     * If no parent exists, will return the current post object
     *
     * @return object PostInspector object
     */
    public function parent()
    {
        if ( ! is_post_type_hierarchical($this->post->post_type)) return false;

        $parentId = $this->post->post_parent;

        if ( ! $parentId) return $this;

        return new PostInspector(get_post($parentId));
    }

    /**
     * Get the top ancestor post object
     *
     * @return object PostInspector object
     */
    public function top()
    {
        if ( ! is_post_type_hierarchical($this->post->post_type)) return false;

        $ancestorIds = get_ancestors($this->post->ID, $this->post->post_type);

        if ( ! $ancestorIds) return false;

        return new PostInspector(get_post(array_pop($ancestorIds)));
    }

    /**
     * Get ancestors
     *
     * @return array PostInspector objects
     */
    public function ancestors()
    {
        if ( ! is_post_type_hierarchical($this->post->post_type)) return false;

        $ancestorIds = get_ancestors($this->post->ID, $this->post->post_type);

        if ( ! $ancestorIds) return false;

        $query = new WP_Query(array(
            'post_type' => $this->post->post_type,
            'posts_per_page' => -1,
            'post__in' => $ancestorIds,
            'orderby' => 'post_parent',
            'order' => 'DESC'
        ));

        return $this->makePostInspectorObjects($query->get_posts());
    }

    /**
     * Get descendants
     *
     * @return array PostInspector objects
     */
    public function descendants()
    {
        if ( ! is_post_type_hierarchical($this->post->post_type)) return false;

        $query = new WP_Query(array(
            'post_type' => $this->post->post_type,
            'posts_per_page' => -1
        ));

        $descendants = get_page_children($this->post->ID, $query->get_posts());

        return $this->makePostInspectorObjects($descendants);
    }

    /**
     * Get siblings
     *
     * @return array PostInspector objects
     */
    public function siblings()
    {
        if ( ! is_post_type_hierarchical($this->post->post_type)) return false;

        $query = new WP_Query(array(
            'post_type' => $this->post->post_type,
            'posts_per_page' => -1,
            'post_parent__in' => array($this->post->post_parent),
            'post__not_in' => array($this->post->id)
        ));

        print "<pre>";
        print_r($query);
        print "</pre>";

        $items = $this->makePostInspectorObjects($query->get_posts());

        return new PostInspectorCollection($this->post, $items);
    }

    /**
     * Map an array of WP_Post objects to PostInspector objects
     *
     * @param  array $posts WP_Post objects
     * @return array        PostInspector objecst
     */
    private function makePostInspectorObjects($posts)
    {
        return array_map(function($post) { return new PostInspector($post); }, $posts);
    }

    /**
     * Get a WP_Post object
     *
     * @param mixed $post Post object, id, slug, or null
     */
    protected function getPostObject($post)
    {
        if ($post instanceof WP_Post)
        {
            return $post;
        }

        if (is_integer($post))
        {
            return get_post($post);
        }

        if (is_string($post))
        {
            return get_page_by_path($post, OBJECT, array('page'));
        }

        return get_queried_object();
    }

    /**
     * Alias WP_Post properties
     *
     * @return array
     */
    private function aliases()
    {
        return array(
            'id'                => 'ID',
            'author'            => 'post_author',
            'date'              => 'post_date',
            'gmt'               => 'post_date_gmt',
            'dateGmt'           => 'post_date_gmt',
            'content'           => 'post_content',
            'title'             => 'post_title',
            'excerpt'           => 'post_excerpt',
            'status'            => 'post_status',
            'commentStatus'     => 'comment_status',
            'pingStatus'        => 'ping_status',
            'password'          => 'post_password',
            'name'              => 'post_name',
            'slug'              => 'post_name',
            'toPing'            => 'to_ping',
            'pinged'            => 'pinged',
            'modified'          => 'post_modified',
            'modifiedGmt'       => 'post_modified_gmt',
            'contentFiltered'   => 'post_content_filtered',
            'parent'            => 'post_parent',
            'guid'              => 'guid',
            'menuOrder'         => 'menu_order',
            'type'              => 'post_type',
            'mimeType'          => 'post_mime_type',
            'commentCount'      => 'comment_count',
            'filter'            => 'filter',
        );
    }

    /**
     * Provides a method for accessing aliased properties
     *
     * @method  $method
     * @param   $args
     * @return  @method
     */
    public function __call($method, $args)
    {
        $aliases = $this->aliases();

        if (array_key_exists($method, $aliases))
        {
            return $this->post->$aliases[$method];
        }

        if (in_array($method, $aliases))
        {
            return $this->post->$method;
        }

        $class = get_class($this);
        $trace = debug_backtrace();
        $file = $trace[0]['file'];
        $line = $trace[0]['line'];
        trigger_error("Call to undefined method $class, $method() in $file on line $line", E_USER_ERROR);
    }
}
