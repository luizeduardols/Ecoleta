document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('form-perfil');
  form?.addEventListener('submit', e => {
    if (!confirm('Deseja salvar as alterações no telefone?')) e.preventDefault();
  });
});
