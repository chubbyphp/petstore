<?php

declare(strict_types=1);

namespace App\Parsing;

use Chubbyphp\Parsing\Schema\ObjectSchemaInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ParsingInterface
{
    public function getCollectionRequestSchema(ServerRequestInterface $request): ObjectSchemaInterface;

    public function getCollectionResponseSchema(ServerRequestInterface $request): ObjectSchemaInterface;

    public function getModelRequestSchema(ServerRequestInterface $request): ObjectSchemaInterface;

    public function getModelResponseSchema(ServerRequestInterface $request): ObjectSchemaInterface;
}
