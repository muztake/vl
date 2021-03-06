// Comparar senhas
function checkPW(){
  let pw = $("input[name='pw']")
  let confirmpw = $("input[name='confirm-pw']")
  let erro = $("#pw-erro")

  if(pw.val().length < 6){
    // checando tamanho
    erro.html("A senha deve ter no mínimo 6 caracteres.")
    erro.removeClass('hidden')
    return false
  } else if(pw.val() != confirmpw.val()) {
    // checando coerencia
    erro.html("As senhas não conferem.")
    erro.removeClass('hidden')
    return false
  } else {
    erro.addClass('hidden')
    return true
  }

}

// jQuery DOM
$(()=>{
  // Validação Geral
  $("input[type='submit']").on('click', e =>{
    e.preventDefault()
    // Checa campos
    if($("#nome").val().length < 6){
      $("#nome-erro").html("O nome deve ter ao menos 6 caracteres")
      $("#nome-erro").removeClass('hidden')
      return
    } else if ($("#email").val().length < 6 || $("#email").val().indexOf("@") == -1){
      $("#nome-erro").addClass('hidden')
      $("#email-erro").html("Digite um e-mail válido")
      $("#email-erro").removeClass('hidden')
      return
    }
    $("#email-erro").addClass('hidden')
    // Checando se o e-mail já forá cadastrado
    getUsuarioByEmail($("#email").val(), data=>{
      if(data){
        $("#email-erro").html("Já existe um usuário com esse e-mail.")
        $("#email-erro").removeClass('hidden')
      } else if(checkPW() && checkCPF()) {
        $("form").submit()
      }
    }, ()=>{
      $("#email-erro").html("Validando e-mail...")
    })

  })

  // Máscaras de Form
  $("#cpf").mask('000.000.000-00')
  // $("#cep").mask('00000-000')
  // $("#telefone").mask('(00) 00000-0000')
})
