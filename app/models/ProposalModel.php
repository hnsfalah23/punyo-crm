<?php
// app/models/ProposalModel.php

class ProposalModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  public function getProposalByDealId($deal_id)
  {
    $this->db->query('SELECT * FROM proposals WHERE deal_id = :deal_id');
    $this->db->bind(':deal_id', $deal_id);
    $proposal = $this->db->single();

    if ($proposal) {
      $this->db->query('SELECT * FROM proposal_items WHERE proposal_id = :proposal_id ORDER BY item_type, item_id');
      $this->db->bind(':proposal_id', $proposal->proposal_id);
      $proposal->items = $this->db->resultSet();
    }

    return $proposal;
  }

  public function saveOrUpdateProposal($data)
  {
    $this->db->beginTransaction();

    try {
      $proposal = $this->getProposalByDealId($data['deal_id']);

      if ($proposal) {
        // Update existing proposal
        $this->db->query('UPDATE proposals SET proposal_number = :num, attachment = :att, subject = :sub, subtotal = :subtotal, grand_total = :grand_total WHERE proposal_id = :id');
        $this->db->bind(':id', $proposal->proposal_id);
      } else {
        // Insert new proposal
        $this->db->query('INSERT INTO proposals (deal_id, proposal_number, attachment, subject, subtotal, grand_total) VALUES (:deal_id, :num, :att, :sub, :subtotal, :grand_total)');
        $this->db->bind(':deal_id', $data['deal_id']);
      }

      $this->db->bind(':num', $data['proposal_number']);
      $this->db->bind(':att', $data['attachment']);
      $this->db->bind(':sub', $data['subject']);
      $this->db->bind(':subtotal', $data['subtotal']);
      $this->db->bind(':grand_total', $data['grand_total']);
      $this->db->execute();

      $proposalId = $proposal ? $proposal->proposal_id : $this->db->lastInsertId();

      $this->db->query('DELETE FROM proposal_items WHERE proposal_id = :proposal_id');
      $this->db->bind(':proposal_id', $proposalId);
      $this->db->execute();

      if (isset($data['items'])) {
        foreach ($data['items'] as $item) {
          $this->db->query('INSERT INTO proposal_items (proposal_id, item_type, description, price, quantity, unit) VALUES (:prop_id, :type, :desc, :price, :qty, :unit)');
          $this->db->bind(':prop_id', $proposalId);
          $this->db->bind(':type', $item['type']);
          $this->db->bind(':desc', $item['description']);
          $this->db->bind(':price', $item['price']);
          $this->db->bind(':qty', $item['quantity']);
          $this->db->bind(':unit', $item['unit']);
          $this->db->execute();
        }
      }

      $this->db->commit();
      return true;
    } catch (Exception $e) {
      $this->db->rollback();
      // Log error
      error_log($e->getMessage());
      return false;
    }
  }
}
