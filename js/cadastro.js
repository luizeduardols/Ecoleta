document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('form-cadastro');
  if (!form) return;
  const senha = form.senha, conf = form.confirma;
  const barra = document.querySelector('#forca i'), txt = document.getElementById('forca-txt');

  document.querySelectorAll('.ver').forEach(b => b.addEventListener('click', () => {
    const i = document.getElementById(b.dataset.alvo), mostrar = i.type === 'password';
    i.type = mostrar ? 'text' : 'password';
    b.textContent = mostrar ? 'Ocultar' : 'Mostrar';
    b.setAttribute('aria-label', mostrar ? 'Ocultar senha' : 'Mostrar senha');
  }));

  const niveis = [['Muito fraca', '#B23B2E', 20], ['Fraca', '#B23B2E', 40], ['Razoável', '#c9a227', 60], ['Boa', '#40916c', 80], ['Forte', '#2d6a4f', 100]];
  senha.addEventListener('input', () => {
    const s = senha.value;
    let p = 0;
    if (s.length >= 8) p++;
    if (s.length >= 12) p++;
    if (/[a-z]/.test(s) && /[A-Z]/.test(s)) p++;
    if (/\d/.test(s)) p++;
    if (/[^A-Za-z0-9]/.test(s)) p++;
    const [nome, cor, larg] = s ? niveis[Math.max(0, p - 1)] : ['', '#fff', 0];
    barra.style.width = larg + '%';
    barra.style.background = cor;
    txt.textContent = s ? 'Senha: ' + nome : 'Mínimo de 8 caracteres, com letras e números.';
  });


  const marcar = (el, msg) => { el.classList.toggle('invalido', !!msg); el.setCustomValidity(msg || ''); };
  const conferir = () => marcar(conf, conf.value && conf.value !== senha.value ? 'As senhas não coincidem.' : '');
  senha.addEventListener('input', conferir);
  conf.addEventListener('input', conferir);

  form.addEventListener('submit', e => {
    conferir();
    if (!form.checkValidity()) { e.preventDefault(); form.reportValidity(); }
  });
});
