<?php
  // Setting header content
  header("Content-type: application/json");
  // Starting session
  session_start();
  // Requiring User Class
  require '../incl/classes/usuario.php';
  // Requiring DB
  require_once '../incl/database.php';

  if(!empty($_POST)){
    $data = $_POST;

    // Get User By Email
    if($data['op'] == 'usuario/email') {
      $json = json_encode(Usuario::getUsuarioByEmail($conn, $data['email']));
      echo $json;
    }

    // Pegar usuário pelo CPF
    if($data['op'] == 'usuario/cpf') {
      $cpf = $data['cpf'];
      $result = $conn->query("SELECT * FROM usuario WHERE cpf = '$cpf'")->fetch(PDO::FETCH_ASSOC);
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Update User
    if($data['op'] == 'usuario/atualizar'){
      $user = $data['user'];
      $email = $data['email'];
      $usuario = Usuario::create(
        $user['nomeCompleto'],
        $user['email'],
        $user['dataNascimento'],
        $user['genero'],
        $user['cpf'],
        $user['rg'],
        $user['endereco'],
        $user['cep'],
        $user['telefone'],
        "",
        "",
        $user['campus'],
        $user['coordenadoria'],
        $user['siape']
      );

      $usuario->update($conn, $email);
      $_SESSION['email'] = $user['email'];
      $json = json_encode(['success' => 'true']);
      echo $json;
    }

    // Mudar Senha
    if($data['op'] == 'usuario/mudarsenha'){
      $email = $data['email'];
      $pw = $data['pw'];
      $usuario = new Usuario();
      $usuario->setEmail($email);
      $usuario->setSenha($pw);
      $usuario->changePassword($conn);
      $json = json_encode(['success' => 'true']);
      echo $json;
    }

    // Autenticate User
    if($data['op'] == 'usuario/autenticar'){
      $candSenha = $data['senha'];
      $usuario = Usuario::selectByEmail($conn, $data['email']);
      if($usuario->email){
        $hash = $usuario->senha;
        if(password_verify($candSenha, $hash)){
          $_SESSION['email'] = $data['email'];
          $_SESSION['ip'] = getIP();
          $_SESSION['privilegios'] = $usuario->getPrivilegios($conn);
          echo json_encode(["success" => true, "emailFound" => true]);
        } else {
          echo json_encode(["success" => false, "emailFound" => true]);
        }
      } else {
        echo json_encode(["success" => false , "emailFound" => false]);
      }
    }

    // Sign off
    if($data['op'] == 'usuario/desconectar'){
      echo json_encode(["success" => true]);
      session_destroy();
    }

    if($data['op'] == 'usuario/perfil'){
      $email = $data['email'];
      $nivel = $data['nivel'];
      // Checando pelo email
      $emailExists = $conn->prepare("SELECT email FROM usuario WHERE email = :email");
      $emailExists->execute([
        ":email" => $email
      ]);
      if($emailExists->fetchObject()){
        $perfil = $conn->prepare("INSERT INTO perfil(email, nivel) VALUES(:email, :nivel)");
        $query = $perfil->execute([
          ":email" => $email,
          ":nivel" => $nivel
        ]);
        if($query){
          echo json_encode([
            "success" => true
          ]);
        } else {
          echo json_encode([
            "success" => false,
            "error" => $perfil->errorInfo()
          ]);
        }
      } else {
        echo json_encode([
          "success" => false,
          "error" => "Email não existente."
        ]);
      }
    }

    if($data['op'] == 'usuario/desvincular'){
      $email = $data['email'];
      $stmt = $conn->prepare("DELETE FROM perfil WHERE email = :email AND nivel = :nivel");
      $query = $stmt->execute([
        ":email" => $email,
        ":nivel" => "validador"
      ]);

      if($query){
        echo json_encode([
          "success" => true
        ]);
      } else {
        echo json_encode([
          "success" => false,
          "error" => $perfil->errorInfo()
        ]);
      }
    }


  }


  function getIP(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      return $_SERVER['REMOTE_ADDR'];
    }
  }

 ?>
