<?php

declare(strict_types=1);

namespace App\Modules\Fias;

use App\Modules\Fias\Exceptions\IndexingFailedException;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class AddressIndexer
{
    private const  INDEX_ADDRESS = 'address';

    private AddressFinderInterface $finder;
    private array $finderOptions;
    private Client $esClient;

    public function __construct(AddressFinderInterface $finder, array $finderOptions)
    {
        $this->finder = $finder;
        $this->finderOptions = $finderOptions;

        $this->esClient = ClientBuilder::create()->build();
    }

    public function indexing(): array
    {
        $indexed = 0;
        $lastObjectId = 0;

        // TODO: lastId
        $items = $this->finder->find($this->finderOptions);
        foreach ($items as $item) {
            try {
                $lastObjectId = $item->getObjectId();

                $return = $this->handleAddress($item);
                // TODO: analyze response
                if ($return) {
                    $indexed++;
                }
            } catch (\Exception $e) {
                throw IndexingFailedException::withLastObjectId($e->getMessage(), $lastObjectId);
            }
        }

        return [$indexed, $lastObjectId];
    }

    private function handleAddress(Address $address)
    {
        // TODO: see bulk insert @https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/indexing_documents.html
        $data = [
            'index' => self::INDEX_ADDRESS,
            'id' => $address->getFiasId(),
            'body' => json_encode($address, JSON_THROW_ON_ERROR),
        ];

        return $this->esClient->index($data);
    }

}
