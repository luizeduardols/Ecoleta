# Documentação - Ecoleta

Guia simples para entender, instalar e usar o projeto.

## O que é o projeto

É um site onde a pessoa leva material reciclável (papel, plástico, vidro, metal...) até um ponto de coleta, ganha **pontos** e troca esses pontos por **benefícios**, como cinema, shows e bem-estar.

Foi feito com HTML, CSS, JavaScript, PHP e MySQL.

## O que você precisa ter

- **XAMPP** (ou Wamp): ele liga o servidor e o banco de dados no seu computador.
- **VS Code** (ou outro editor) para mexer nos arquivos.
- Um navegador (Chrome, Edge ou Firefox).

## Como instalar e abrir o site

1. Copie a pasta do projeto para dentro de `C:\xampp\htdocs\` (exemplo: `C:\xampp\htdocs\PPR`).
2. Abra o painel do XAMPP e ligue o **Apache** e o **MySQL**.
3. No navegador, abra `http://localhost/phpmyadmin`, clique em **Importar** e escolha o arquivo `database/banco.sql`.
4. Abra o site em `http://localhost/PPR/`.



## Como criar o primeiro administrador

1. Crie uma conta normalmente na página de cadastro.
2. No phpMyAdmin, clique na aba **SQL** e rode (trocando pelo seu e-mail): `UPDATE usuarios SET perfil='admin' WHERE email='seu@email.com';`
3. Saia e entre de novo no site.
4. No menu do usuário (canto inferior esquerdo), clique em **Administração**.

Na Administração você cadastra **usuários, benefícios, materiais e parceiros**.

## O que cada página faz

| `index.php`     | Página inicial. Leva para o login ou o cadastro. |
| `cadastro.php`  | Cria a conta (nome, e-mail, CPF, telefone e senha). |
| `login.php`     | Entrar no sistema. |
| `dashboard.php` | Painel com os seus dados (e-mail e CPF aparecem escondidos em parte). |
| `perfil.php`    | Permite mudar o telefone. |
| `inicio.php`    | Página inicial de quem já entrou. |
| `beneficio.php`, `historico.php` | Benefícios e histórico (ainda com dados de exemplo). |
| `sobre.php`, `contato.php` | Informações do projeto e contato. |
| `admin/index.php` | Área do administrador. |
| `logout.php`    | Sai da conta. |

## Pastas principais

- `admin/` - área do administrador.
- `includes/` - conexão com o banco e controle de login.
- `database/` - arquivos do banco de dados.
- `css/`, `js/`, `img/`, `font/` - visual, comportamentos, imagens e fonte.

## O que o banco guarda

- **usuarios** - quem tem a conta do Ecoleta.
- **materiais** - o que pode ser reciclado e quantos pontos vale por quilo.
- **parceiros** - lojas e pontos de coleta e benefícios oferecidos.
- **beneficios** - o que dá para trocar por pontos no Ecoleta.
- **entregas** e **resgates** - já estão criadas, mas as telas ainda não são usadas.

## Segurança

- As senhas são guardadas embaralhadas. Ninguém consegue ver a senha de verdade.
- O CPF é conferido antes de aceitar o cadastro.
- As páginas internas só abrem para quem fez login.
- A área de administração só abre para administradores.
- Os formulários são protegidos contra envios falsos.

## Problemas comuns

| O que aconteceu | O que fazer |
| --- | --- |
| Aparece código PHP ou tela em branco | Abra por `http://localhost/...` e confira se o Apache está ligado. |
| `Unknown database 'ppr'` | Importe o arquivo `database/schema.sql`. |
| `Access denied for user 'root'` | Crie o `includes/config.local.php` com a sua senha. |
| `could not find driver` | Ative `extension=pdo_mysql` no `php.ini` e reinicie o Apache. |
| `undefined function mb_...` | Ative `extension=mbstring` no `php.ini` e reinicie o Apache. |
| Não aparece "Administração" | Confira se rodou o comando do administrador e entre de novo. |
| Acentos estranhos | Salve os arquivos em UTF-8. |

## O que ainda falta

- Ligar benefícios, histórico e os números do painel ao banco de dados.
- Fazer o resgate de benefícios com os pontos.
- Terminar os ajustes pedidos no guia do professor Luiz (por exemplo, o logout voltar para o login).
