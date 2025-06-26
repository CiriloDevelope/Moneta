function expandir(elemento, perfilID) {
    document.querySelectorAll('.perfil').forEach(card => {
      if (card !== elemento) {
        card.classList.add('ocultar');
      }
    });

    elemento.classList.add('expandido');
    document.getElementById('botoesContainer').style.display = 'block';
    elemento.querySelector('.detalhes').style.display = 'block';

    document.getElementById('perfilEscolhido').value = perfilID;
  }

  function voltar() {
    document.querySelectorAll('.perfil').forEach(card => {
      card.classList.remove('ocultar');
      card.classList.remove('expandido');
      card.querySelector('.detalhes').style.display = 'none';
    });

    document.getElementById('botoesContainer').style.display = 'none';
    document.getElementById('perfilEscolhido').value = '';}
    