<?php namespace Rudolf\OAuth2\Client\Entity;

/**
 * Class RedditUser
 * @package Rudolf\OAuth2\Client\Entity
 */
class RedditUser
{

    /**
     * @var  string
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var bool
     */
    protected $is_over_18;

    /**
     * TwitchUser constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->id = $attributes['id'];
        $this->username = $attributes['name'];
        $this->is_over_18 = $attributes['over_18'];
    }

    /**
     * Get the contents of the user as a key-value array.
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getIsOver18()
    {
        return $this->is_over_18;
    }
}
