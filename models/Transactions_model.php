<?php
class Transactions_model extends CI_Model {

        public $model_table = "transactions";

        public function __construct()
        {
                parent::__construct();
                // Your own constructor code
        }

        // Ecrire une fonction qui permet de recuperer tous les marchand_id 
        // presents dans la table transaction

        public function get_all_marchand_id()
        {
            $query = $this->db->select('transaction_marchand_id')
                              ->distinct()
                              ->from('transactions')
                              ->get();

            return $query->result_array();
        }

        /**
         * Ecrire une fonction qui permet de recuperer les transactions
         * en fonction de l'operateur exclusivement, d'une date de debut
         * et d'une date de fin
         *
         * @param int    $marchand_id
         * @param string $operateur  ['MtnCI','MoovCI','OrangeCI']
         * @param string $date_debut [format yyyy-mm-dd]
         * @param string $date_fin   [format yyyy-mm-dd]
         * 
         * @return Object
         * 
         */
        public function get_transactions($marchand_id = FALSE, $operateur = FALSE, $date_debut, $date_fin)
        {
            
            // selection de la table
            $this->db->select('*')->from('transactions');
            
            // teste si le parametre est different de faux
            // if($marchand_id != FALSE){
                $this->db->where('transaction_marchand_id', $marchand_id);
            // }

            // teste si le parametre est different de faux
            // if($operateur != FALSE){
                $this->db->where('transaction_method', $operateur);
            // }
            
            // effectuation de la requete selon une date de debut
            $this->db->where('transaction_date >=', $date_debut);
            // effectuation de la requete selon une date de fin
            $this->db->where('transaction_date <=', $date_fin);

            $query = $this->db->get();

            return $query->result();
        }

        public function get_transactions_operateur($operateur = FALSE, $date_debut, $date_fin)
        {
            
            // selection de la table
            $this->db->select('*')->from('transactions');
           
            $this->db->where('transaction_method', $operateur);
           
            // effectuation de la requete selon une date de debut
            $this->db->where('transaction_date >=', $date_debut);
            // effectuation de la requete selon une date de fin
            $this->db->where('transaction_date <=', $date_fin);

            $query = $this->db->get();

            return $query->result();
        }

       /**
         * Ecrire une fonction qui permet de recuperer les transactions
         * en fonction de l'identifiant marchand exclusivement, d'une date de debut
         * et d'une date de fin
         *
         * @param mixed $marchand_id
         * @param string $date_min   [format yyyy-mm-dd]
         * @param string $date_max   [format yyyy-mm-dd]
         * 
         * @return object
         * 
         */
        public function get_transaction_format($marchand_id, $operateur ,$date_min, $date_max)
        {
            // $query = $this->db->select('customer_name, transaction_date, transaction_method, transaction_Sender_Mobile, transaction_amount, tauxbranch')
            //                 ->from('transactions')
            //                 ->join('customer_pattern','customer_pattern.customerID = transactions.transaction_marchand_id')
            //                 ->join('customers','customers.customer_id = transactions.transaction_marchand_id')
            //                 ->where('transaction_marchand_id', $marchand_id)
            //                 ->where('transaction_method', $operateur)
            //                 ->where('transaction_date >=',$date_min)
            //                 ->where('transaction_date <=',$date_max)
            //                 ->get();

            $query = $this->db->query("SELECT `customer_name`, `transaction_date`, `transaction_method`, `transaction_amount`,  `transaction_Sender_Mobile`,  `tauxbranch`, `transaction_amount` * (`tauxbranch`/100) as `montant_com`, `transaction_amount` - (`transaction_amount` * (`tauxbranch`/100)) as `montant_a_reverser`  FROM `transactions` JOIN `customer_pattern` ON `customer_pattern`.`customerID` = `transactions`.`transaction_marchand_id` JOIN `customers` ON `customers`.`customer_id` = `transactions`.`transaction_marchand_id` WHERE `transaction_method` = '$operateur' AND `transaction_status` = 2 AND `transaction_marchand_id` = '$marchand_id' AND `transaction_date` >= '$date_min' AND `transaction_date` <= '$date_max' ");
            

            return $query->result();
        }

        /**
         * Ecrire une fonction qui permet de recuperer les transactions
         * en fonction de l'operateur exclusivement, d'une date de debut
         * et d'une date de fin
         *
         * @param mixed $marchand_id
         * @param string $date_min   [format yyyy-mm-dd]
         * @param string $date_max   [format yyyy-mm-dd]
         * 
         * @return object
         * 
         */
        public function get_transaction_format_operateur($operateur ,$date_min, $date_max)
        {
            // $query = $this->db->select('customer_name, transaction_date, transaction_method, transaction_Sender_Mobile, transaction_amount, tauxbranch, transaction_amount * tauxbranch as test')
            //                   ->from('transactions')
            //                   ->join('customer_pattern','customer_pattern.customerID = transactions.transaction_marchand_id')
            //                   ->join('customers','customers.customer_id = transactions.transaction_marchand_id')
            //                   ->where('transaction_method', $operateur)
            //                   ->where('transaction_date >=',$date_min)
            //                   ->where('transaction_date <=',$date_max)
            //                   ->get();

            $query = $this->db->query("SELECT `customer_name`, `transaction_date`, `transaction_method`,`transaction_amount`, `transaction_Sender_Mobile`,  `tauxbranch`, `transaction_amount` * (`tauxbranch`/100) as `montant_com`, `transaction_amount` - (`transaction_amount` * (`tauxbranch`/100)) as `montant_a_reverser`  FROM `transactions` JOIN `customer_pattern` ON `customer_pattern`.`customerID` = `transactions`.`transaction_marchand_id` JOIN `customers` ON `customers`.`customer_id` = `transactions`.`transaction_marchand_id` WHERE `transaction_method` = '$operateur' AND `transaction_status` = 2 AND `transaction_date` >= '$date_min' AND `transaction_date` <= '$date_max' ");

            return $query->result();
        }

        public function getOptions()
        {
            $query = $this->db->get('motif');

            return $query->result();
        }

        public function getById()
        {
            # code...
        }

        public function update()
        {
            $this->db->set('mytable', $object);

            return $this->db->update('mytable');
        }

        public function delete()
        {
            $this->db->where('mytable', $object);

            return $this->db->update('mytable');
        }

}