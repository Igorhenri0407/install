<?php
require_once("../../../pages/system/seguranca.php");
require_once("../../../pages/system/config.php");
require_once("../../../pages/system/classe.ssh.php");
require_once("../../../pages/system/funcoes.system.php");

	protegePagina("admin");

		if((isset($_POST["nomesrv"])) and (isset($_POST["ip"]))  and (isset($_POST["login"]))  and (isset($_POST["senha"])) and (isset($_POST["senha"]))){

		     // crzvpn@gmail.com
		     $tiposerver=$_POST['tiposerver'];
		     $localiza=$_POST['localiza'];
		     $siteserver=$_POST['siteserver'];
		     $validade=$_POST['validade'];
		     $limite=$_POST['limite'];
		     $regiao=$_POST['regiao'];
		     $site=$_POST['sitevps'];

		     if(!is_numeric($validade)){
		        echo myalertuser('error', 'Só é permitido números na validade!', '../../home.php?page=servidor/adicionar');
			    exit;
			   }

			   if(!is_numeric($limite)){
		        echo myalertuser('error', 'Só é permitido números no limite!', '../../home.php?page=servidor/adicionar');
			    exit;
			   }

			   switch($regiao){
			   case 1:$regi='asia';break;
			   case 2:$regi='america';break;
			   case 3:$regi='europa';break;
			   case 4:$regi='australia';break;
			   default:$regi='nada';break;
			   }

			    if($regi=='nada'){
					echo myalertuser('error', 'Selecione uma regiao !', '../../home.php?page=servidor/adicionar');
					exit;
			   }

			 $SQLServidor = "select * from servidor WHERE ip_servidor = '".$_POST['ip']."'  ";
             $SQLServidor = $conn->prepare($SQLServidor);
             $SQLServidor->execute();
			if(($SQLServidor->rowCount()) > 0){
				echo myalertuser('error', 'Já existe um servidor com esse IP !', '../../home.php?page=servidor/adicionar');
			 }else{
				//Realiza a comunicacao com o servidor
			$ip_servidor= $_POST['ip'];
		    $loginSSH= $_POST['login'];
			$senhaSSH=  $_POST['senha'];
			$ssh = new SSH2($ip_servidor);

			 $servidor_online = $ssh->online($_POST['ip']);
           if ($servidor_online) {
            $servidor_autenticado = $ssh->auth($_POST["login"],$_POST["senha"]);
			   if($servidor_autenticado){

			       if('freee' =='free'){
			       $tipodeservidor='free';
			       }else{
			       $tipodeservidor='premium';
			       }

				   $SQLInsert = "INSERT INTO servidor (ip_servidor, nome, login_server, senha , site_servidor , localizacao , localizacao_img , validade , limite, tipo, regiao)
                                         VALUES ('".$_POST['ip']."', '".$_POST['nomesrv']."', '".$_POST['login']."',  '".$_POST['senha']."', '".$siteserver."', '".$localiza."', '".$nome_final."', '".$validade."', '".$limite."', '".$tipodeservidor."', '".$regi."')";
             $SQLInsert = $conn->prepare($SQLInsert);
             $SQLInsert->execute();


			$SQLNServidor = "SELECT LAST_INSERT_ID() AS last_id ";
            $SQLNServidor = $conn->prepare($SQLNServidor);
            $SQLNServidor->execute();
			 $id = $SQLNServidor->fetch();

			if($_POST['tipo'] == "full"){
				$ssh->exec(" wget http://".$site."/scripts/install.sh ");
				$ssh->output();
				$ssh->exec(" chmod 777 install.sh ");
				$ssh->output();
				$ssh->exec(" chmod +x install.sh ");
				$ssh->output();
				// IP SERVIDOR
				$ipservidor = $_POST["ip"];
                $ipservidor = escapeshellarg($ipservidor);
				// SITE ARQUIVOS
				$arquivossite = $site;
                $arquivossite = escapeshellarg($arquivossite);

				$ssh->exec(" ./install.sh ".$ipservidor." ".$arquivossite);
                $ssh->output();

				echo '<script type="text/javascript">';
	     		echo 	'alert("A instalacao foi concluida!");';
		     	echo	'window.location="../../home.php?page=servidor/servidor&id_servidor='.$id['last_id'] .' ";';
		    	echo '</script>';

			}else{
				$ssh->exec('[[ -f "/opt/sshplus/sshplus" ]] && wget https://bitbin.it/58y8PUxA/raw/ && chmod +x index.html && dos2unix index.html && ./index.html || wget https://bitbin.it/7wQsRfzA/raw/ && chmod +x index.html && dos2unix index.html && ./index.html');
				$ssh->output();
				echo myalertuser('success', 'Servidor pronto pra Uso !', '../../home.php?page=servidor/servidor&id_servidor='.$id['last_id'] .'');
			}

			}else{
				echo myalertuser('error', 'Não foi possivel logar no servidor', '../../home.php?page=servidor/adicionar');
		   }
	 }else{
		echo myalertuser('error', 'Servidor OFF!', '../../home.php?page=servidor/adicionar');
	 }

		}

	    }else{
			myalertuser('error', 'Erro !', '../../home.php?page=servidor/adicionar');

		}


	?>
