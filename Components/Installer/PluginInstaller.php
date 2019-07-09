<?php
namespace TpayShopwarePayments\Components\Installer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use TpayShopwarePayments\Components\Model\PaymentDetails;

class PluginInstaller
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return void
     */
    public function createOrUpdateSchema()
    {
        $schema[] = $this->em->getClassMetadata(
            PaymentDetails::class
        );

        $this->createSchema($schema);
    }

    /**
     * @param array $schema
     * @return void
     */
    private function createSchema(array $schema)
    {
        $schemaManager = new SchemaTool($this->em);
        $schemaManager->updateSchema($schema, true);
    }

}
