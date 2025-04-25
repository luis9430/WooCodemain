<?php
namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="mi_plugin_products")
 */
class Product {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $post_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * Constructor
     */
    public function __construct() {
        $this->created_at = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Product
     */
    public function setName($name) {
        $this->name = $name;
        $this->updated_at = new \DateTime();
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Product
     */
    public function setDescription($description) {
        $this->description = $description;
        $this->updated_at = new \DateTime();
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Product
     */
    public function setPrice($price) {
        $this->price = $price;
        $this->updated_at = new \DateTime();
        return $this;
    }

    /**
     * @return int
     */
    public function getPostId() {
        return $this->post_id;
    }

    /**
     * @param int $post_id
     * @return Product
     */
    public function setPostId($post_id) {
        $this->post_id = $post_id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }
}