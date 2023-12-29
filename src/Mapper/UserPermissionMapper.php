<?php

namespace BplUserMongoDbODM\Mapper;

use Doctrine\ODM\MongoDB\DocumentManager;

class UserPermissionMapper {
    public function __construct(private DocumentManager $odm) { }
}

