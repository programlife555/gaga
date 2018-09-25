<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: site/api_group_invitableFriends.proto

namespace Zaly\Proto\Site;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 **
 * action: api.group.invitableFriends
 *
 * Generated from protobuf message <code>site.ApiGroupInvitableFriendsRequest</code>
 */
class ApiGroupInvitableFriendsRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string groupId = 1;</code>
     */
    private $groupId = '';
    /**
     * Generated from protobuf field <code>int32 offset = 2;</code>
     */
    private $offset = 0;
    /**
     * default 200
     *
     * Generated from protobuf field <code>int32 count = 3;</code>
     */
    private $count = 0;

    public function __construct() {
        \GPBMetadata\Site\ApiGroupInvitableFriends::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>string groupId = 1;</code>
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Generated from protobuf field <code>string groupId = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setGroupId($var)
    {
        GPBUtil::checkString($var, True);
        $this->groupId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 offset = 2;</code>
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Generated from protobuf field <code>int32 offset = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setOffset($var)
    {
        GPBUtil::checkInt32($var);
        $this->offset = $var;

        return $this;
    }

    /**
     * default 200
     *
     * Generated from protobuf field <code>int32 count = 3;</code>
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * default 200
     *
     * Generated from protobuf field <code>int32 count = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setCount($var)
    {
        GPBUtil::checkInt32($var);
        $this->count = $var;

        return $this;
    }

}

