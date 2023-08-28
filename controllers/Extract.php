<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Extract extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	function __construct()
	{
			parent::__construct();

			$this->load->helper(array('url','catalogue'));
			// $this->load->library(array('form_validation', 'mailjet'));
			$this->load->model('Transactions_model', 'transaction');
			date_default_timezone_set('UTC');
	}

	public function test()
	{	
		// Effectuer la requete pour avoir les donnees du jour
		$date_min = date("2016-01-01 00:00:00");
		$date_max = date("2016-12-31 23:59:59");
		// identifiant marchand
		$marchand_id = "C_5696ADED4C7FD";

		$trans_march_org  = $this->transaction->get_transaction_format($marchand_id, "OrangeCI", $date_min, $date_max);
		$trans_march_mtn  = $this->transaction->get_transaction_format($marchand_id, "MtnCI", $date_min, $date_max);
		$trans_march_moov = $this->transaction->get_transaction_format($marchand_id, "MoovCI", $date_min, $date_max);
		// $trans_march_org  = $this->transaction->get_transaction_format_operateur("OrangeCI", $date_min, $date_max);

		$array_clean = obj_to_numeric_array($trans_march_org);

		// 
		$somme = get_sum_per_row(5, $array_clean, "Total Transaction");

		// $trans_march_org = add_calculated_com((array) $trans_march_org);

		// var_dump($trans_march_org[]);
		// exit();

		$classeur_marchand = creer_classeur();

		// creation des feuilles de transactions operateur
		$feuille_org = creer_feuille_classeur("OrangeCI", $classeur_marchand, 0);
		$feuille_moov = creer_feuille_classeur("MoovCI", $classeur_marchand, 1);
		$feuille_mtn = creer_feuille_classeur("MtnCI", $classeur_marchand, 2);
		
		$sheetIndex = $classeur_marchand->getIndex(
			$classeur_marchand->getSheetByName('Worksheet')
		);
		// suppression de la worksheet par defaut
		$classeur_marchand->removeSheetByIndex($sheetIndex);

		// activation de la nouvelle worksheet par defaut
		$classeur_marchand->setActiveSheetIndexByName("OrangeCI");

		set_table_header($classeur_marchand, "OrangeCI");
		set_table_header($classeur_marchand, "MtnCI");
		set_table_header($classeur_marchand, "MoovCI");

		generation_table($classeur_marchand,"OrangeCI",$trans_march_org,"D7");
		generation_table($classeur_marchand,"MtnCI",$trans_march_mtn,"D7");
		generation_table($classeur_marchand,"MoovCI",$trans_march_moov,"D7");

		$nom_fichier_operateur = "classeur_operateur_".date("Y_m_d");
	
		// sauvegarde des chemin des fichiers excel
		// $path_fichier_marchand = save_excel($classeur_marchand, $nom_fichier_marchand , "assets/documents/");
		$path_fichier_operateur = save_excel($classeur_marchand, $nom_fichier_operateur , "assets/documents/");

	}

	
	public function index()
	{
		// Effectuer la requete pour avoir les donnees du jour
		$date_min = date("2016-01-01 00:00:00");
		$date_max = date("2016-12-31 23:59:59");
	
		// recuperation liste de operateurs
		$liste_operateur = [
			"OrangeCI",
			"MtnCI",
			"MoovCI",
		];

		$liste_chemin = $liste_piece_jointe =  [];

		$list_marchand_id = $this->transaction->get_all_marchand_id();
		$list_marchand_id = array_values((array) $list_marchand_id);

		$marchand_id = "C_5696ADED4C7FD";

		// generer les differents fichiers excel 
		// creation classeur marchand vide
		
		// $worksheet = $classeur_marchand->getSheetByName("OrangeCI");
		$classeur_marchand = creer_classeur();

		// creation des feuilles de transactions operateur
		$feuille_org = creer_feuille_classeur("OrangeCI", $classeur_marchand, 0);
		$feuille_moov = creer_feuille_classeur("MoovCI", $classeur_marchand, 1);
		$feuille_mtn = creer_feuille_classeur("MtnCI", $classeur_marchand, 2);
		
		$sheetIndex = $classeur_marchand->getIndex(
			$classeur_marchand->getSheetByName('Worksheet')
		);
		// suppression de la worksheet par defaut
		$classeur_marchand->removeSheetByIndex($sheetIndex);

		// activation de la nouvelle worksheet par defaut
		$classeur_marchand->setActiveSheetIndexByName("OrangeCI");

		// $classeur_marchand->disconnectWorksheets();
		// unset($classeur_marchand);

		set_table_header($classeur_marchand, "OrangeCI");
		set_table_header($classeur_marchand, "MtnCI");
		set_table_header($classeur_marchand, "MoovCI");

		// effectuer l'operation pour chaque marchand
		for ($index=0; $index < count($list_marchand_id); $index++) { 
			$piece_jointe = "";

			$element = $list_marchand_id[$index];

			$id = array_values((array) $element);

			// var_dump($id[0]);

			$trans_march_org  = $this->transaction->get_transaction_format($id[0], "OrangeCI", $date_min, $date_max);
			$trans_march_mtn  = $this->transaction->get_transaction_format($id[0], "MtnCI", $date_min, $date_max);
			$trans_march_moov = $this->transaction->get_transaction_format($id[0], "MoovCI", $date_min, $date_max);

			$array_clean_org  = obj_to_numeric_array($trans_march_org);
			$array_clean_mtn  = obj_to_numeric_array($trans_march_mtn);
			$array_clean_moov = obj_to_numeric_array($trans_march_moov);

			$somme_org = get_sum_per_row(5, $array_clean_org, "Total Transaction Orange");
			$somme_mtn = get_sum_per_row(5, $array_clean_mtn, "Total Transaction MTN");
			$somme_moov = get_sum_per_row(5, $array_clean_moov, "Total Transaction Moov");
			
			
			if (isset($trans_march_org[0]) AND isset($trans_march_mtn[0]) AND isset($trans_march_moov[0])) {

				// remplissage des feuilles du classeur marchand avec les donnees
				generation_table($classeur_marchand,"OrangeCI",$trans_march_org,"D7");
				generation_table($classeur_marchand,"MtnCI",$trans_march_mtn,"D7");
				generation_table($classeur_marchand,"MoovCI",$trans_march_moov,"D7");

				// sauvegarde des fichiers excel apres remplissage
				$nom_fichier_marchand = "classeur_".$id[0]."_marchand_par_operateur".date("Y_m_d");

				// sauvegarde des chemin des fichiers excel
				$path_fichier_marchand = save_excel($classeur_marchand, $nom_fichier_marchand , "assets/documents/");
				array_push($liste_chemin,["nom"=>$nom_fichier_marchand.".xlsx","path"=>$path_fichier_marchand.".xlsx"]);
				
				$piece_jointe = get_piece_jointe($path_fichier_marchand.".xlsx",$nom_fichier_marchand.".xlsx");

				add_piece_jointe($liste_piece_jointe,$piece_jointe);
			}
		}
		
		// var_dump($liste_piece_jointe);
		// exit();
		
		// requete par operateur
		$trans_org = $this->transaction->get_transaction_format_operateur("OrangeCI", $date_min, $date_max);
		$trans_mtn = $this->transaction->get_transaction_format_operateur("MtnCI", $date_min, $date_max);
		$trans_moov = $this->transaction->get_transaction_format_operateur("MoovCI", $date_min, $date_max);

		// creation classeur marchand vide
		// $classeur_operateur = creer_classeur();

		// remplissage des feuilles du classeur operateur avec les donnees
		generation_table($classeur_marchand,"OrangeCI",$trans_org,"D7");
		generation_table($classeur_marchand,"MtnCI",$trans_mtn,"D7");
		generation_table($classeur_marchand,"MoovCI",$trans_moov,"D7");

		// sauvegarde des fichiers excel apres remplissage
		// $nom_fichier_marchand = "classeur_marchand_par_operateur".date("Y_m_d");
		$nom_fichier_operateur = "classeur_operateur_".date("Y_m_d");
	
		// sauvegarde des chemin des fichiers excel
		// $path_fichier_marchand = save_excel($classeur_marchand, $nom_fichier_marchand , "assets/documents/");
		$path_fichier_operateur = save_excel($classeur_marchand, $nom_fichier_operateur , "assets/documents/");
		// var_dump($path_fichier_marchand);
		// var_dump($path_fichier_marchand,$path_fichier_operateur);
		
		// sauvegarde de la liste de chemin
		array_push($liste_chemin,["nom"=>$nom_fichier_operateur.".xlsx","path"=>$path_fichier_operateur.".xlsx"]);
		
		// recuperation de la piece jointe
		$piece_jointe = get_piece_jointe($path_fichier_operateur.".xlsx",$nom_fichier_operateur.".xlsx");
		// ajout de la piece_jointe a la liste
		add_piece_jointe($liste_piece_jointe,$piece_jointe);

		// generer le mail avec les differentes pieces jointes 
		$mail_obj = make_mail("Etats Syca manager du ".date("Y-m-d"), "Ci-joint les états sycamanager de la veille.\n Cordialement", "eric.zile@sycapay.com", $liste_piece_jointe);
		
		var_dump(count($liste_piece_jointe), $liste_piece_jointe);
		exit();
		// envoyer le mail
		// $send_mail_response = send_mail($mail_obj);

		var_dump($send_mail_response);
	}


}
