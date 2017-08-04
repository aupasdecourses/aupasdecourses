<?php
namespace AppBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Tools\SchemaTool;

class DoctrineUpdateCommand extends UpdateSchemaDoctrineCommand
{
    /**
     * An array of Entities that will be ingored by `doctrine:schema:update`
     *
     * @var array
     */
    protected $ignoredEntities = [
        'AppBundle\Entity\Merchant',
        'AppBundle\Entity\Shop',
        'AppBundle\Entity\User',
    ];

    protected function executeSchemaCommand(
        InputInterface $input,
        OutputInterface $output,
        SchemaTool $schemaTool,
        array $metadatas
    ) {
        /** @var $metadata \Doctrine\ORM\Mapping\ClassMetadata */
        $newMetadatas = [];
        foreach ($metadatas as $metadata) {
            if (!in_array($metadata->getName(), $this->ignoredEntities)) {
                array_push($newMetadatas, $metadata);
            }
        }

        parent::executeSchemaCommand($input, $output, $schemaTool, $newMetadatas);
    }
}
