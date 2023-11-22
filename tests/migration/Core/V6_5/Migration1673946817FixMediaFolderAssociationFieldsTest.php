<?php declare(strict_types=1);

namespace Shopware\Tests\Migration\Core\V6_5;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Test\TestCaseBase\KernelLifecycleManager;
use Shopware\Core\Migration\V6_5\Migration1673946817FixMediaFolderAssociationFields;

/**
 * @package core
 *
 * @internal
 */
#[CoversClass(Migration1673946817FixMediaFolderAssociationFields::class)]
class Migration1673946817FixMediaFolderAssociationFieldsTest extends TestCase
{
    private Connection $connection;

    protected function setUp(): void
    {
        if (Feature::isActive('v6.6.0.0')) {
            static::markTestSkipped('This test is not compatible with v6.6.0.0. Re-enable when migration refactoring is complete or remove the unit test with next major.');
        }

        $this->connection = KernelLifecycleManager::getConnection();
    }

    public function testGetCreationTimestamp(): void
    {
        $migration = new Migration1673946817FixMediaFolderAssociationFields();
        static::assertEquals('1673946817', $migration->getCreationTimestamp());
    }

    public function testFieldsAreMigrated(): void
    {
        $migration = new Migration1673946817FixMediaFolderAssociationFields();
        $migration->update($this->connection);

        $fields = $this->connection->fetchOne('SELECT association_fields FROM media_default_folder WHERE entity = :user', ['user' => 'user']);

        static::assertJson($fields);
        $fields = \json_decode($fields, true);

        static::assertIsArray($fields);
        static::assertContains('avatarUsers', $fields);
    }
}
