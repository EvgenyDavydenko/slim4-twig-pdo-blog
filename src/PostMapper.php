<?php

declare(strict_types=1);

namespace Blog;

use PDO;

class PostMapper
{
    /**
     * @var PDO
     */
    private PDO $connection;

    /**
     * PostMapper constructor.
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $slug
     * @return array|null
     */
    public function getBySlug(string $slug): ?array
    {
        $statement = $this->connection->prepare('SELECT * FROM post WHERE slug = :slug');
        $statement->execute([
            'slug' => $slug
        ]);

        $result = $statement->fetchAll();

        return array_shift($result);
    }

    public function getList(): ?array
    {
        $statement = $this->connection->prepare('SELECT * FROM post ORDER BY published_date DESC');
        $statement->execute();
        return $statement->fetchAll();
    }
}
