<?php
namespace App\Model\Repository;

use App\Model\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

/**
 * Repositorio para la entidad Product
 */
class ProductRepository {
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Constructor
     */
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Encontrar producto por ID
     */
    public function find($id) {
        return $this->entityManager->find(Product::class, $id);
    }

    /**
     * Encontrar todos los productos
     */
    public function findAll() {
        return $this->entityManager->getRepository(Product::class)->findAll();
    }

    /**
     * Encontrar producto por ID de post
     */
    public function findByPostId($postId) {
        return $this->entityManager->getRepository(Product::class)->findOneBy([
            'post_id' => $postId
        ]);
    }

    /**
     * Guardar un producto
     */
    public function save(Product $product) {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        return $product;
    }

    /**
     * Eliminar un producto
     */
    public function delete(Product $product) {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    /**
     * Crear un producto asociado a un post
     */
    public function createFromPost($postId, $name, $price, $description = null) {
        $product = new Product();
        $product->setPostId($postId);
        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);
        
        return $this->save($product);
    }

    /**
     * Actualizar producto desde datos de post
     */
    public function updateFromPost(Product $product, $name, $price, $description = null) {
        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);
        
        return $this->save($product);
    }
}