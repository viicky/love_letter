<?php

class JeuController extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('jeumodel');
        $this->load->database();
    }

    public function view() {
        /* if ( ! file_exists(APPPATH.'views/pages/'.$page.'.php'))
          {
          // Whoops, we don't have a page for that!
          show_404();
          } */

        $data['title'] = "jeu";
        $data["main"] = $this->jeumodel->getMain();
        $data["pose"] = $this->jeumodel->getPose();
        $data["retires"] = $this->jeumodel->getRetires();
        
        $this->load->view('pages/jeu.php', $data);
    }

    public function test() {
        $this->jeumodel->getPartie(); //obtenir session de num_partie

        //premier joueur
        $this->jeumodel->ajouterJoueur();
        $this->jeumodel->enregistrer("patate");

        //second joueur
        $q = $this->db->query("insert into joueurs (nom, points, elimine, num_partie, num_joueur) values ('reblochon', 0, 0, ?, ?)",
            Array($_SESSION["num_partie"], 1));
        $this->db->query("update jeu set nb_joueurs=nb_joueurs+1 where num_partie=?",
            Array($_SESSION["num_partie"]));

        $this->jeumodel->lancerJeu();

        $this->testPioche();

        //sélectionner la carte à poser
        $q = $this->db->query("select id_carte from carte where joueur=?",
            Array($_SESSION["id"]));

        $this->jeumodel->jouerCarte($q->row()->id_carte);
        $this->jeumodel->passerJoueurSuivant();
        var_dump($this->jeumodel->getMainAutres(1));
        var_dump($this->jeumodel->getPoseAutres(0));
        var_dump($this->jeumodel->getRetires());
        var_dump($this->jeumodel->getNbCartesPioche());
    }


    public function testPioche(){
        if($this->jeumodel->getNumJoueurActuel() == $_SESSION["num_joueur"]){
            $this->jeumodel->piocher();
            echo "bien";
        }else{
            echo "pas bien";
        }
    }


    public function action($arg1){
        $this->jeumodel->action($arg1);

        $this->view();
    }

    public function initJ2(){
        $_SESSION["id"] = 2;
        $_SESSION["num_joueur"] = 1;
        $_SESSION["num_partie"] = 1;
        echo "j2 initialisé";
    }

}

