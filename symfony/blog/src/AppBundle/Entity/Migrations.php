<?php

namespace AppBundle\Entity;

/**
 * Migrations
 */
class Migrations
{
    /**
     * @var string
     */
    private $migration;

    /**
     * @var integer
     */
    private $batch;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set migration
     *
     * @param string $migration
     *
     * @return Migrations
     */
    public function setMigration($migration)
    {
        $this->migration = $migration;

        return $this;
    }

    /**
     * Get migration
     *
     * @return string
     */
    public function getMigration()
    {
        return $this->migration;
    }

    /**
     * Set batch
     *
     * @param integer $batch
     *
     * @return Migrations
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;

        return $this;
    }

    /**
     * Get batch
     *
     * @return integer
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}

