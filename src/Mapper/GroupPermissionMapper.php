<?php

namespace BplUserMongoDbODM\Mapper;
use Doctrine\ODM\MongoDB\DocumentManager;

class GroupPermissionMapper {
    public function __construct(private DocumentManager $odm) { }
}

