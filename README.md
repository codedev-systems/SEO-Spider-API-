# SEO Spider (API)

<h2>O que é o SEO Spider?</h2>
<p>O SEO Spider é uma API pública, de código-aberto, que tem por finalidade rastrear links quebrados em websites, de forma a contribuir para uma melhor experiência do usuário no site e contribuir para um melhor SEO Score da página.</p>

<h2>O que são links quebrados?</h2>
<p>Links quebrados são links disponibilizados ao usuário em um site que não funcionam de verdade, retornando erros como o 404 (página não encontrada) e o 403 (acesso proibido). Estes links, além de causarem frustação nos usuários, diminuem o SEO Score da página, fazendo com que esta não apareça nos primeiros resultados de busca dos buscadores.</p>

<h2>Exemplos de erros retornados por links quebrados</h2>
<ul>
  <li><b>404 (página não encontrada)</b> - Indica que uma página/arquivo não existe no site.</li>
  <li><b>403 (acesso proibido)</b> - Indica que o acesso a uma determinada página não é permitida pelo servidor.</li>
  <li><b>500 (erro interno no servidor)</b> - Indica que a página não está disponível devido a um erro interno no servidor. Muitas vezes é causado por erros no código do back-end do site/app web (erros no código PHP, por exemplo).</li>
</ul>

<h2>O que é o SEO Score?</h2>
<p>O SEO Score é uma pontuação de 0 a 100 que mede o nível de optimização de uma página para os motores de busca, como o Google e o Bing. Quanto mais optimizada for uma página maiores são as chances dela aparecer nos primeiros resultados de busca, fazendo com que ela atraia mais visitantes e saia na frente de páginas concorrentes.</p>

<h2>O link é como um cartaz de produto</h2>
<p>Se você é dono de um site, deve pensar nos links que você disponibiliza aos usuários como se fossem cartazes para um produto - se o link está disponível para clique, o conteúdo que ele aponta também deve estar disponível para acesso. Um link quebrado é como se fosse um cartaz apontando para um produto que não está disponível na loja (algo frustrante para os visitantes da sua página, que são equivalentes os clientes da loja).</p>

<h2>Como usar - exemplos com código</h2>
<h3>Python</h3>
```python
def saudacao(nome):
    print(f"Olá, {nome}!")

saudacao("Mundo")
```
