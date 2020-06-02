<?php
include("define.php");
function redirection($url,$time=0){
    if(!headers_sent()){
        header("refresh: $time;url=$url");
        exit;
    }else {
        echo '<meta http-equiv="refresh" contents="',$time,';url=',$url,'">';
    }
}
class Bdd{
    private static $connexion = null;
    public static function connectBdd(){
        if (!self::$connexion) {
            self::$connexion = new PDO (DNS,USER,PASS);
            self::$connexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
        }
        return self::$connexion;
    }
}
?>

<?php
class Connexion{
    
    public static function deconnexion($redirection){
        $_SESSION = array();
        session_destroy;
        if(!empty($redirection)){
            redirection($redirection);
        }
    }

    //verif login
    public static function verifLogin($login){
        
        $resultat= Bdd::connectBdd()->prepare('SELECT * FROM membres WHERE  NOM_ad=:login');
        $resultat->bindParam(':NOM_ad', $login, PDO::PARAM_STR,50);
       // $resultat->execute();
       if ($resultat->rowCount()===1) {
            return true;
            
        }else {
            return false;
        }
    }

    //verif password
    public static function verifPass($pass,$login){
        $resultat= Bdd::connectBdd()->prepare('SELECT * FROM membres WHERE  NOM_ad=:login');
        $resultat->bindParam(':NOM_ad', $login,PDO::PARAM_STR,50);
        $resultat->execute();
        $donnee = $resultat ->fetch(PDO::FETCH_ASSOC);
        if ($pass===$donnee['password']) {
            return true;
        }else{
            return false;
        }
    }
    public static function connexionCreate(){
        $mes = '';
        if(!empty($_POST['username']) AND !empty($_POST['password'])){
            $log=htmlspecialchars(stripcslashes(trim($_POST['username'])));
            $password = htmlspecialchars(stripcslashes(trim($_POST['password'])));
            if (Connexion::verifLogin($log)) {
                if(Connexion::verifPass($password,$log)){
                    Connexion::niveau($log);
                }else {
                    return false;

                }
            } else {
                return false;
            }
            
        }
    }
    public static function niveau($login){
        $resultat = Bdd::connectBdd()->prepare('SELECT * FROM membres WHERE  NOM_ad=:login');
        $resultat->bindParam(':NOM_ad', $login,PDO::PARAM_STR,50);
        $resultat->execute();
        $donnee = $resultat->fetch(PDO::FETCH_ASSOC);
        switch ($donnee['type']) {
            case 'admin':
                $_SESSION['type']='admin';
                $redirect = redirection('admin.php');
                break;
            case 'joueur':
                $_SESSION['type']='joueur';
                $redirect = redirection('joueur.php');
                break;
            
        }
    }
}
?>

