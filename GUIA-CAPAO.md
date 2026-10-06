# Guia Capão: uso e validação

O plugin **Capão News — Guia** foi instalado e ativado no LocalWP. A seção pública está em [Guia Capão](http://capaonews.local/category/guia-capao/). As categorias Guia Capão, Restaurantes, Bares e Sorveterias estão disponíveis. Bairros e suas matérias foram preservados.

## Criar uma matéria

1. Abra **Posts → Adicionar novo**. Crie uma matéria principal para cada estabelecimento, com título, imagem destacada e conteúdo editorial.
2. Escolha **Restaurantes**, **Bares**, **Sorveterias** ou outra subcategoria de Guia Capão. Não é necessário marcar a categoria principal: a página do Guia inclui automaticamente matérias das subcategorias.
3. Em **Guia Capão — informações práticas**, preencha endereço, horário, telefone, link de WhatsApp e site/rede social conforme necessário. Links devem começar com `https://` ou `http://`. Somente valores preenchidos são exibidos.
4. Em **Discussão**, marque **Permitir comentários**. Publique a matéria.

O painel do Guia pode ser exibido nas preferências do editor caso esteja oculto.

## Aprovar e editar avaliações

Em **Comentários**, avaliações novas aparecem como **Pendentes**, com a coluna **Nota do Guia**. Use **Aprovar** para publicar. Para corrigir a nota, entre em **Editar** e procure **Guia Capão — nota**. Só usuários com as permissões de moderação e edição do comentário podem alterar esse campo. “Sem nota” mantém o comentário, mas remove sua participação na média.

A nota é obrigatória no formulário público: 1, 2, 3, 4 ou 5. Nome, e-mail e texto também são obrigatórios. Comentários aprovados exibem nome, data, conteúdo e nota; o e-mail não aparece na lista pública. Comentários antigos sem nota continuam aparecendo, sem entrar na média.

No LocalWP verificado, avaliações sem conta estão permitidas. Se essa condição mudar, em **Configurações → Discussão**, desmarque **Os usuários devem estar registrados e conectados para comentar**. Confira também se os comentários da matéria estão abertos e se o fechamento automático de comentários antigos não está bloqueando a matéria. O plugin não muda essas configurações nem a moderação global.

Se houver cache de página em produção, limpe a página da matéria e a categoria após moderar ou editar uma avaliação. A média não tem cache próprio e é calculada diretamente no banco.

## Instalar em outra cópia

Em **Plugins → Adicionar plugin → Enviar plugin**, envie `deliverables/capao-news-guia.zip` e ative. Alternativamente, copie a pasta `plugins/capao-news-guia` para `wp-content/plugins/capao-news-guia`.

Atualize o tema com os arquivos listados abaixo. Não copie os testes para a pasta pública do WordPress. A ativação cria/reutiliza as categorias e converte somente o item Bairros do menu principal cadastrado, preservando sua posição e propriedades. Se o menu for cadastrado posteriormente, substitua Bairros por **Categorias → Guia Capão** em **Aparência → Menus**. O menu alternativo do tema já mostra Guia Capão.

Desativar o plugin preserva posts, termos e metadados. O tema continua funcionando: campos e avaliações são ocultados até a reativação, e a apresentação editorial do arquivo de categoria permanece disponível.

## Arquivos

- `plugins/capao-news-guia/capao-news-guia.php`: campos, validação, moderação, categorias na ativação, edição administrativa e cálculo de médias.
- `plugins/capao-news-guia/README.md`: instalação e operação do plugin.
- `category.php`: apresentação da categoria principal e das subcategorias, cards e paginação nativa.
- `comments-guia.php`: lista pública e formulário de avaliações.
- `template-parts/guia-practical.php`: informações práticas opcionais.
- `functions.php`: apresentação das estrelas/comentários, identificação visual das categorias e menu alternativo. Não contém a lógica de armazenamento ou moderação do plugin.
- `single.php`: integra os blocos do Guia sem remover recursos editoriais.
- `style.css`: estilos responsivos restritos aos componentes do Guia.
- `tests/guia-local-bootstrap.php`: instalação/ativação CLI na cópia local indicada.
- `tests/guia-local-integration.php`: testes com registros temporários identificados e limpeza restrita a eles.
- `tests/guia-test-mail-suppression.php`: auxiliar temporário para impedir notificações apenas dos comentários de teste. Foi retirado da instalação após a validação.

## Verificações realizadas

Os testes executaram APIs nativas do WordPress na instalação LocalWP, usando matérias e comentários temporários. A massa de teste foi removida após a validação. Nenhum e-mail de teste foi enviado.

- Sintaxe PHP dos arquivos novos e alterados.
- Ativação repetida sem duplicar categorias; associação de Restaurantes ao Guia.
- Matéria publicada em uma subcategoria; salvamento de campos com nonce/permissão; gravação sem nonce impedida; omissão do campo vazio.
- Nota válida armazenada no comentário; avaliação nova pendente e fora da média.
- Aprovação, edição da nota com nonce/permissão, reprovação, spam, lixeira, restauração e exclusão com atualização da média.
- Comentários antigos sem nota e notas legadas inválidas fora da média.
- Tentativa de edição por visitante impedida.
- Notas ausentes, 0, 6, decimal, texto, array e valor com quebra de linha rejeitados pelo servidor; nonce ausente rejeitado.
- Proteção nativa de duplicidade mantida; classificação como spam não sobrescrita; filtros de comentários fora do Guia preservados.
- POST real para `wp-comments-post.php`: nota ausente/inválida recebe HTTP 400; avaliação válida recebe redirecionamento HTTP 302, permanece pendente e não muda a média.
- Navegação pela categoria/subcategoria e página 2 do arquivo com paginação nativa.
- Chrome headless em 320, 375, 390, 768, 1024 e 1440px: 30 verificações de páginas/larguras, sem transbordamento horizontal ou elementos excedendo o container; e-mails ausentes da lista pública.
- Inspeção visual de capturas de desktop e celular; estrelas selecionáveis por seta do teclado e toque. Não foram utilizados aparelhos físicos.
- Plugin temporariamente desativado: início, arquivo do Guia e matéria responderam HTTP 200, sem erro fatal; plugin reativado ao concluir.

A edição de notas foi verificada pela API e pelos hooks usados pelo painel; a interface administrativa não foi operada manualmente. Os testes de moderação usaram somente registros temporários.

## Conferência manual no painel

1. Cadastre uma matéria real conforme os passos acima.
2. Abra em janela anônima, envie uma avaliação e confirme que não aparece antes da aprovação.
3. Aprove em Comentários e recarregue a matéria: nome, texto e estrelas devem aparecer junto da média.
4. Edite a nota pelo painel e confira a mudança. Mova o comentário para spam/lixeira e confirme sua saída da média.
5. Confira a matéria no celular e teste as estrelas por toque e com Tab/setas pelo teclado.

A implementação utiliza a [moderação nativa de comentários do WordPress](https://developer.wordpress.org/reference/hooks/pre_comment_approved/) e o [formulário nativo](https://developer.wordpress.org/reference/functions/comment_form/), sem dependências externas.
