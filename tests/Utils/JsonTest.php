<?php

declare(strict_types=1);

namespace Entropy\Tests\Utils;

use Entropy\Utils\Json;
use PHPUnit\Framework\TestCase;
use stdClass;

final class JsonTest extends TestCase
{
    public function testEncodeDecodeRoundTrip(): void
    {
        $data = [
            'name' => 'entropy',
            'count' => 3,
        ];

        $json = Json::encode($data);
        $this->assertSame($data, Json::decode($json));
    }

    public function testEncodeObject(): void
    {
        $object = new stdClass();
        $object->name = 'entropy';

        $json = Json::encode($object);
        $this->assertStringContainsString('entropy', $json);
    }
}
