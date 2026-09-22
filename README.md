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

## Como o SEO Spider age?
O SEO Spider navega por todas as páginas do site, capturando os links achados no caminho e testando a disponibilidade de seus respectivos conteúdos. 

Cada link quebrado (ou seja, com conteúdo indisponível) é adicionado a um JSON, contendo as seguintes informações:
* **URL do link quebrado:** www.example.com/broken-link
* **Código HTTP retornado:** 404, 403, 500, ...
* **Status:** Not Found, Forbidden, ...

Ao final do rastreio, este JSON é retornado à aplicação que solicitou o rastreio por meio da API.

## Como usar? — exemplos com código

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

### PHP
```php
<?php
    $ch = curl_init();
	curl_setopt_array($ch, [
        CURLOPT_URL => 'https://seo-spider.codedev-tech.com.br/link-checker/',
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'site' => 'https://www.utorrent.com/'
        ]),
        CURLOPT_RETURNTRANSFER => true
    ]);
	$response = curl_exec($ch);
	curl_close($ch);
	
    // Respostas (Link, Código HTTP retornado e Status da resposta)
    $results = json_decode($response, true);
    foreach ($results as $result) {
        echo 'URL: ' . $item['URL'] . '<br>';
        echo 'Código HTTP: ' . $item['Code'] . '<br>';
        echo 'Status: ' . $item['Status'] . '<br>';
        echo '<hr>';
    }
?>
```

### Java
```java
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;

public class Api{
    public static void main(String[] args){
        try{
            HttpClient client = HttpClient.newHttpClient();
            HttpRequest request = HttpRequest.newBuilder()
                    .uri(URI.create(
                            "https://seo-spider.codedev-tech.com.br/link-checker/"
                    ))
                    .header("Accept", "application/json")
                    .header("Content-Type", "application/json")
                    .POST(HttpRequest.BodyPublishers.ofString("{\"site\": \"https://www.utorrent.com/\"}"))
                    .build();

            HttpResponse<String> response = client.send(
                    request,
                    HttpResponse.BodyHandlers.ofString()
            );

            // Respostas (Link, Código HTTP retornado e Status da resposta)
            String[] results = (response.body().replace("[", "").replace("]", "").replace("{", "")).split("},");
            for(String result: results){
                System.out.println(result);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
```
