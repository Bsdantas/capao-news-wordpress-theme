# Capão News

Tema WordPress editorial criado para um portal de notícias comunitário do Capão Redondo e da Zona Sul de São Paulo.

Site oficial: [www.capaonews.com](https://www.capaonews.com)

O projeto foi desenvolvido para a página [@capaonews](https://www.instagram.com/capaonews/) e tem como proposta transformar o conteúdo do Capão News em uma experiência digital organizada, responsiva e fácil de administrar pelo painel do WordPress.

> Projeto desenvolvido como parte do Projeto de Extensão IV da [Faculdade Descomplica](https://descomplica.com.br/), no curso de Análise e Desenvolvimento de Sistemas.

## Sobre o projeto

O Capão News é um tema personalizado para publicação de notícias, reportagens e histórias da comunidade. A solução foi pensada para aproximar o jornalismo local do público, valorizar as pautas do território e oferecer uma base profissional para crescimento editorial.

Além da apresentação das notícias, o tema possui uma estrutura de gestão para anúncios e parceiros, permitindo que o cliente atualize informações recorrentes sem precisar alterar o código-fonte.

## Funcionalidades implementadas

### Experiência editorial

- Homepage com destaque principal em formato de carrossel.
- Área de notícias recentes e seção de notícias mais lidas.
- Organização por categorias editoriais.
- Exibição de data da publicação e tempo estimado de leitura.
- Templates para página inicial, páginas internas e posts individuais.
- Fallback de navegação com categorias predefinidas quando não há menu configurado.

### Gestão de conteúdo no WordPress

- Suporte a posts, páginas, categorias e imagens destacadas.
- Suporte a logo personalizada e imagem de cabeçalho.
- Menus independentes para navegação principal e rodapé.
- Criação automática das páginas institucionais “Quem Somos” e “Nossa Equipe” na ativação do tema.
- Tipos de conteúdo personalizados para **Anúncios** e **Parceiros**.
- Campos administrativos para link externo e status de publicação.
- Exibição automática de anúncios e parceiros ativos na homepage.
- Espaços preparados para publicidade e apoio ao jornalismo local.

### Interface e acessibilidade

- Layout responsivo para desktop, tablet e celular.
- Menu mobile com controle por JavaScript.
- Link para pular diretamente ao conteúdo principal.
- Uso de HTML semântico, textos alternativos e atributos `aria` nos componentes interativos.
- Feedback visual em links, botões, cards e navegação.
- Carregamento versionado de estilos e scripts para reduzir problemas de cache.

## Tecnologias utilizadas

- **PHP** para os templates e integrações com a API do WordPress.
- **WordPress** como CMS e plataforma de publicação.
- **CSS** para identidade visual, responsividade e componentes do tema.
- **Tailwind CSS** para estilos utilitários compilados.
- **JavaScript** para menu mobile e carrossel de destaques.
- **Git e GitHub** para versionamento e colaboração.

## Requisitos

- WordPress 6.0 ou superior.
- PHP 7.4 ou superior.
- Servidor com suporte a temas WordPress.
- Extensão PHP `mbstring` recomendada para ambientes em português.

## Instalação

1. Baixe ou clone este repositório dentro da pasta de temas do WordPress:

   ```text
   wp-content/themes/site-capao-news
   ```

2. No painel administrativo, acesse **Aparência > Temas**.
3. Ative o tema **Capão News**.
4. Acesse **Aparência > Personalizar** para configurar logo, cabeçalho e identidade do site.
5. Em **Aparência > Menus**, configure os menus principal e do rodapé.
6. Cadastre posts, categorias e imagens destacadas para alimentar a homepage.
7. Cadastre anúncios e parceiros pelo painel do WordPress, informe o link e marque a opção **Ativar este item no site**.

### Instalação via Git

```bash
cd wp-content/themes
git clone https://github.com/Bsdantas/site-capao-news.git site-capao-news
```

Depois, ative o tema pelo painel do WordPress.

## Administração de anúncios e parceiros

O tema registra dois tipos de conteúdo no WordPress:

### Anúncios

Indicados para publicidade, campanhas, ofertas e espaços comerciais. Cada item pode ter título, imagem destacada, conteúdo, link externo e status de exibição.

### Parceiros

Indicados para empresas, instituições e apoiadores do projeto. Cada item pode ter nome, logo, conteúdo, link externo e status de exibição.

Para exibir um item no site, ele precisa estar publicado e com **Ativar este item no site** selecionado. O link é sanitizado e os links externos são abertos com proteção `noopener noreferrer`.

## Estrutura do projeto

```text
site-capao-news/
├── assets/
│   ├── css/tailwind.css       # CSS compilado
│   ├── images/                # Logos e imagens do tema
│   └── js/theme.js            # Interações da interface
├── footer.php                 # Rodapé e área de apoio
├── functions.php              # Configuração e funcionalidades WordPress
├── header.php                 # Cabeçalho e navegação
├── index.php                  # Homepage e listagens
├── page.php                   # Template de páginas
├── single.php                 # Template de posts
├── style.css                  # Metadados do tema e estilos principais
├── tailwind.config.js         # Configuração do Tailwind CSS
└── tailwind.input.css         # Entrada do Tailwind CSS
```

## Desenvolvimento local

O projeto é um tema WordPress e precisa ser executado dentro de uma instalação WordPress local, como Local, XAMPP, Docker ou ambiente equivalente.

Para trabalhar nos estilos do Tailwind, instale as dependências do projeto e execute o processo de compilação configurado no ambiente de desenvolvimento. O arquivo `assets/css/tailwind.css` é o CSS consumido pelo tema em produção.

Antes de publicar uma alteração, recomenda-se validar:

- ativação do tema sem erros no painel;
- navegação em desktop e dispositivos móveis;
- publicação de posts com e sem imagem destacada;
- funcionamento dos menus;
- cadastro e ativação de anúncios e parceiros;
- links externos e páginas institucionais;
- ausência de avisos de PHP no log do WordPress.

## Qualidade e versionamento

O repositório utiliza GitHub Actions para executar verificações automatizadas de PHP a cada alteração. O `.gitignore` evita o versionamento de dependências, arquivos de ambiente, logs e configurações locais.

## Contexto acadêmico

Este trabalho faz parte do **Projeto de Extensão IV** da Faculdade Descomplica, no curso de **Análise e Desenvolvimento de Sistemas**. O projeto aplica conhecimentos de desenvolvimento web, CMS, PHP, acessibilidade, arquitetura de temas WordPress e organização de conteúdo digital em uma demanda real de comunicação comunitária.

## Créditos

- **Projeto:** Capão News
- **Site:** [www.capaonews.com](https://www.capaonews.com)
- **Instagram:** [@capaonews](https://www.instagram.com/capaonews/)
- **Instituição:** Faculdade Descomplica
- **Curso:** Análise e Desenvolvimento de Sistemas
- **Repositório:** [Bsdantas/site-capao-news](https://github.com/Bsdantas/site-capao-news)

## Licença

Este projeto utiliza a [GNU General Public License v2 ou posterior](https://www.gnu.org/licenses/gpl-2.0.html), conforme definido nos metadados do tema WordPress.
