<?php
namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\WorkflowHistoryRepository")
 * @ORM\Table(name="workflow_history")
 */
class WorkflowHistory {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $object_id;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $object_type;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $workflow;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $transition;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $from_state;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $to_state;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $user_id;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $metadata;
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;
    
    public function __construct() {
        $this->created_at = new \DateTime();
    }
    
    // Getters and setters
    public function getId() {
        return $this->id;
    }
    
    public function getObjectId() {
        return $this->object_id;
    }
    
    public function setObjectId($object_id) {
        $this->object_id = $object_id;
        return $this;
    }
    
    public function getObjectType() {
        return $this->object_type;
    }
    
    public function setObjectType($object_type) {
        $this->object_type = $object_type;
        return $this;
    }
    
    public function getWorkflow() {
        return $this->workflow;
    }
    
    public function setWorkflow($workflow) {
        $this->workflow = $workflow;
        return $this;
    }
    
    public function getTransition() {
        return $this->transition;
    }
    
    public function setTransition($transition) {
        $this->transition = $transition;
        return $this;
    }
    
    public function getFromState() {
        return $this->from_state;
    }
    
    public function setFromState($from_state) {
        $this->from_state = $from_state;
        return $this;
    }
    
    public function getToState() {
        return $this->to_state;
    }
    
    public function setToState($to_state) {
        $this->to_state = $to_state;
        return $this;
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }
    
    public function getMetadata() {
        return $this->metadata;
    }
    
    public function setMetadata($metadata) {
        $this->metadata = $metadata;
        return $this;
    }
    
    public function getCreatedAt() {
        return $this->created_at;
    }
    
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
        return $this;
    }
}