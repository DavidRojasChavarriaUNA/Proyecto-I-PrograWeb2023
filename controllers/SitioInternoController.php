<?php

include_once('./models/UserModel.php');
include_once('./models/Codes.php');
include_once('InternalController.php');

class  SitioInternoController extends InternalController{
    public function Home() {
        if(!$this->IsAutenticated()) return $this->RedirectToLogin();
        return view('sitioInterno/index', 
        ['title'=>'Mi voto - Principal',
         'isMain' => true,
         'isVote' => false,
         'isCreateVote' => false,
         'showVotesManteinment' => false,
         'isEditVote' => false,
         'user'=> $this->User]);
    }
}

?>