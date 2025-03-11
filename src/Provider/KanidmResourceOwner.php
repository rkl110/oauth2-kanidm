<?php

namespace rkl110\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class KanidmResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

  /**
   * Raw response
   *
   * @var array
   */
    protected $response;

  /**
   * Creates new resource owner.
   *
   * @param array  $response
   */
    public function __construct(array $response = array())
    {
        $this->response = $response;
    }

  /**
   * Get resource owner id
   *
   * @return string|null
   */
    public function getId()
    {

        return $this->getValueByKey($this->response, 'sub');
    }

  /**
   * Get resource owner name
   *
   * @return string|null
   */
    public function getName()
    {
        return $this->getValueByKey($this->response, 'name');
    }

  /**
   * Get resource owner email
   *
   * @return string|null
   */
    public function getEmail()
    {
        return $this->getValueByKey($this->response, 'email');
    }

  /**
   * Get resource owner groups
   *
   * @return string[]|null
   */
    public function getGroups()
    {
        return $this->getValueByKey($this->response, 'groups');
    }

  /**
   * Return all of the owner details available as an array.
   *
   * @return array
   */
    public function toArray()
    {
        return $this->response;
    }
}
