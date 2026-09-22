# SEO Spider (API)

## O que é o SEO Spider?

O **SEO Spider** é uma API pública, de código-aberto, que tem por finalidade rastrear links quebrados em websites, de forma a contribuir para uma melhor experiência do usuário no site e contribuir para um melhor SEO Score da página.

## O que são links quebrados?

Links quebrados são links disponibilizados ao usuário em um site que não funcionam de verdade, retornando erros como o **404 (página não encontrada)** e o **403 (acesso proibido)**.

Estes links, além de causarem frustração nos usuários, diminuem o SEO Score da página, fazendo com que esta não apareça nos primeiros resultados de busca dos buscadores.

## Exemplos de erros retornados por links quebrados

* **404 (página não encontrada)** — Indica que uma página/arquivo não existe no site.
* **403 (acesso proibido)** — Indica que o acesso a uma determinada página não é permitido pelo servidor.
* **500 (erro interno no servidor)** — Indica que a página não está disponível devido a um erro interno no servidor. Muitas vezes é causado por erros no código do back-end do site/app web (erros no código PHP, por exemplo).

## O que é o SEO Score?

O **SEO Score** é uma pontuação de 0 a 100 que mede o nível de otimização de uma página para os motores de busca, como o Google e o Bing.

Quanto mais otimizada for uma página, maiores são as chances de ela aparecer nos primeiros resultados de busca, fazendo com que atraia mais visitantes e saia na frente de páginas concorrentes.

## O link é como um cartaz de produto

Se você é dono de um site, deve pensar nos links que você disponibiliza aos usuários como se fossem cartazes para um produto.

Se o link está disponível para clique, o conteúdo para o qual ele aponta também deve estar disponível para acesso.

Um link quebrado é como se fosse um cartaz apontando para um produto que não está disponível na loja — algo frustrante para os visitantes da sua página, que são equivalentes aos clientes da loja.

## Como usar — exemplos com código

### Python

```python
import requests

response = requests.post(
    'https://seo-spider.codedev-tech.com.br/link-checker/', # URL da API
    headers={'Accept': 'application/json'}, # Cabeçalho
    json={
        'site': 'https://www.utorrent.com/' # site alvo
    }
)

# Respostas (Link, Código HTTP retornado e Status da resposta)
for item in response.json():
    print(item)
```

### Node.js
```javascript
process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';

async function getUrls(){
    const response = await fetch(
        'https://seo-spider.codedev-tech.com.br/link-checker/',
        {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                site: 'https://www.utorrent.com/'
            })
        }
    );

    const data = await response.json();

    // Respostas (Link, Código HTTP retornado e Status da resposta)
    for (const item of data) {
        console.log(item);
    }
}

getUrls();
```
