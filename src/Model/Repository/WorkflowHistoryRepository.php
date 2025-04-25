<?php
namespace App\Model\Repository;

use Doctrine\ORM\EntityRepository;
use App\Model\Entity\WorkflowHistory;

/**
 * Repository para WorkflowHistory
 */
class WorkflowHistoryRepository extends EntityRepository {
    /**
     * Encontrar historial por ID de objeto y tipo
     *
     * @param int $objectId
     * @param string $objectType
     * @return WorkflowHistory[]
     */
    public function findByObject($objectId, $objectType) {
        return $this->findBy(
            ['object_id' => $objectId, 'object_type' => $objectType],
            ['created_at' => 'DESC']
        );
    }
    
    /**
     * Obtener historial paginado
     *
     * @param int $page
     * @param int $perPage
     * @return array Con resultados y total de registros
     */
    public function findPaginated($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        
        $qb = $this->createQueryBuilder('h')
                   ->orderBy('h.created_at', 'DESC')
                   ->setFirstResult($offset)
                   ->setMaxResults($perPage);
        
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($qb);
        
        return [
            'total' => count($paginator),
            'records' => $paginator->getIterator()->getArrayCopy()
        ];
    }
    
    /**
     * Contar transiciones por workflow
     *
     * @return array
     */
    public function countByWorkflow() {
        return $this->createQueryBuilder('h')
                   ->select('h.workflow, COUNT(h.id) as count')
                   ->groupBy('h.workflow')
                   ->getQuery()
                   ->getResult();
    }
}