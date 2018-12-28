<?php
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed
require __DIR__ . '/config.php';


try {
  // Returns a `FacebookFacebookResponse` object
  $response = $fb->get(
    $act_id . '/targetingbrowse?locale=pt_br',
    $token
  );
} catch(FacebookExceptionsFacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(FacebookExceptionsFacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
//Faz requisição dos anúncios
$graphNode = $response->getGraphNode();

$audiences = json_decode($graphNode);
foreach($audiences as $audience){
    //Verifica se post existe no bd
    $select = "select id FROM `audiences` WHERE id = '" . $audience->id . "'";
    $post = mysqli_query($conn, $select);
    if(mysqli_num_rows($post) > 0 ){
        echo 'post registrado <br>'; 
        $updateAudience = "UPDATE `audiences` SET 
                    nome='" . $audience->name . "'," .
                    "type='" . $audience->type . "'" .
                    "' WHERE id = '" . $audience->id . "'";

        $updateAudienceSize = "UPDATE `audience-size` SET 
                    updated_at='" . date("Y-m-d H:i:s") . "'," .
                    "size='" . $audience->audience_size . "'" .
                    "' WHERE audience_id = '" . $audience->id . "'";

        echo $updateAudience;
        if(mysqli_query($conn,$updateAudience)){
            $msg = "Atualizado com sucesso!";
        }else{
            $msg = "Erro ao atualizar!";
        }
        echo $msg;

        echo $updateAudienceSize;
        if(mysqli_query($conn,$updateAudienceSize)){
            $msg = "Atualizado audience_size com sucesso!";
        }else{
            $msg = "Erro ao atualizar audience_size!";
        }
        echo $msg;


    } else { 
        echo 'ainda não registrado <br>';
        $insertAudiences = "insert into `audiences` values('" 
            . $audience->id . "','" 
            . $audience->name . "','" 
            . $audience->type . "')";
            
        if(mysqli_query($conn,$insertAudiences)){
            $msg = "Gravado com sucesso!";
        }else{
            $msg = "Erro ao gravar!";
        }

        $insertAudienceSizes = "insert into `audience-sizes` values('" 
            . date("Y-m-d H:i:s") . "','" 
            . $audience->id . "','" 
            . $audience->size . "')";
            
        if(mysqli_query($conn,$insertAudienceSizes)){
            $msg = "Gravado com sucesso!";
        }else{
            $msg = "Erro ao gravar!";
        }
    }
    // echo $adsets->name;
}
mysqli_close($conn);    

echo '<pre>';
print_r($audiences);