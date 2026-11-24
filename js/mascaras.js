document.addEventListener('DOMContentLoaded', () => {
  const mascaras = {
    cpf: v => v.replace(/\D/g, '').slice(0, 11)
      .replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2'),
    telefone: v => {
      const d = v.replace(/\D/g, '').slice(0, 11);
      if (d.length > 10) return d.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
      if (d.length > 6) return d.replace(/^(\d{2})(\d{4})(\d+)$/, '($1) $2-$3');
      return d.length > 2 ? d.replace(/^(\d{2})(\d+)$/, '($1) $2') : d;
    }
  };
  document.querySelectorAll('[data-mascara]').forEach(el => {
    const f = mascaras[el.dataset.mascara];
    if (!f) return;
    el.value = f(el.value);
    el.addEventListener('input', () => { el.value = f(el.value); });
  });
});
