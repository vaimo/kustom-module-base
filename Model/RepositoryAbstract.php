<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @internal
 */
abstract class RepositoryAbstract
{
    /**
     * @var AbstractDb
     */
    protected AbstractDb $resourceModel;
    /**
     * @var mixed
     */
    protected mixed $modelFactory;
    /**
     * @var array
     */
    protected array $cache = [];

    /**
     * @param AbstractDb $resourceModel
     * @param mixed $modelFactory
     * @codeCoverageIgnore
     */
    public function __construct(AbstractDb $resourceModel, mixed $modelFactory)
    {
        $this->resourceModel = $resourceModel;
        $this->modelFactory = $modelFactory;
    }

    /**
     * Saving the model
     *
     * @param AbstractModel $model
     * @return OrderInterface
     */
    public function save(AbstractModel $model): AbstractModel
    {
        try {
            $this->resourceModel->save($model);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $model;
    }

    /**
     * Getting back the model based on the key value pair
     *
     * @param string $key
     * @param string $value
     * @return AbstractModel
     */
    protected function getByKeyValuePair(string $key, string $value): AbstractModel
    {
        $cacheKey = $key . ':' . $value;
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $model = $this->modelFactory->create();
        $model->load($value, $key);
        if (!$model->getId()) {
            throw NoSuchEntityException::singleField($key, $value);
        }

        $this->cache[$cacheKey] = $model;
        return $model;
    }

    /**
     * Returns true if the entry for the key value pair exists
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    protected function existEntryByKeyValuePair(string $key, string $value): bool
    {
        if (isset($this->cache[$value])) {
            return true;
        }

        $model = $this->modelFactory->create();
        $model->load($value, $key);

        if ($model->getId() !== null) {
            $this->cache[$value] = $model;
            return true;
        }

        return false;
    }

    /**
     * Getting back a empty instance
     *
     * @return AbstractModel
     */
    public function getEmptyInstance(): AbstractModel
    {
        return $this->modelFactory->create();
    }

    /**
     * Deleting the model
     *
     * @param AbstractModel $log
     * @return AbstractModel
     */
    public function delete(AbstractModel $log): AbstractModel
    {
        try {
            $this->resourceModel->delete($log);
            return $log;
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
    }
}
