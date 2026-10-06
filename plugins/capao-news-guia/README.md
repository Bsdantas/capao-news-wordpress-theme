# Capão News — Guia

Plugin independente do tema, compatível com WordPress 6.0+ e PHP 7.4+.

## Instalação

Copie esta pasta para `wp-content/plugins/capao-news-guia` e ative **Capão News — Guia** em **Plugins**. Também é possível instalar o ZIP em **Plugins → Adicionar plugin → Enviar plugin**.

Na ativação, cria ou reutiliza **Guia Capão**, **Restaurantes**, **Bares** e **Sorveterias**. As três últimas são vinculadas ao Guia. Categorias existentes não são duplicadas. Se o menu principal cadastrado tiver um item **Bairros**, somente esse item é convertido para a categoria Guia Capão, preservando sua posição e propriedades. A categoria/página Bairros e suas matérias não são excluídas.

Se você cadastrar ou trocar o menu posteriormente, use **Aparência → Menus**, substitua o item Bairros por **Categorias → Guia Capão** e salve o menu principal. A ativação não altera outros menus.

## Matérias

Use **Posts → Adicionar novo**. Cada estabelecimento deve ter uma matéria principal: escolha uma categoria do Guia e marque **Permitir comentários** em **Discussão**. Preencha os campos opcionais em **Guia Capão — informações práticas**. Não há um tipo de conteúdo novo.

## Avaliações

A matéria utiliza os comentários nativos. Novas avaliações exigem nome, e-mail, texto e uma nota inteira de 1 a 5, e ficam pendentes. O e-mail não aparece na lista pública. Visitantes sem conta podem avaliar quando **Configurações → Discussão → Os usuários devem estar registrados e conectados para comentar** estiver desmarcada.

Em **Comentários**, aprove, reprove, marque como spam ou mova para a lixeira. Para alterar uma nota, use **Editar → Guia Capão — nota**. A edição exige permissão de moderação, permissão para editar aquele comentário e nonce válido. **Sem nota** mantém o comentário fora da média.

A média consulta apenas comentários aprovados com notas válidas. Não armazena um total separado: aprovação, edição, reprovação, spam, lixeira e exclusão são refletidos na próxima consulta. Comentários antigos sem nota são preservados. Cache de página de terceiros, se configurado, deve ser invalidado após alterações, assim como nas demais páginas do site.

O plugin preserva os mecanismos nativos de spam, duplicidade e configuração de acesso. Não muda globalmente a moderação nem os comentários das demais matérias.

## Desativação

Não remove posts, categorias, campos nem notas. O tema protege todas as chamadas do plugin. A apresentação editorial da categoria continua funcionando; os campos e avaliações voltam ao reativar o plugin.

O layout público fica em `category.php`, `comments-guia.php`, `template-parts/guia-practical.php` e `style.css` do tema Capão News.
