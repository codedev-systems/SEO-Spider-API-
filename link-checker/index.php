
<?php
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include __DIR__."/../entities/Robots.php";

        function normalizeUrl($url) {
            $url = strtok($url, '#'); // Remove fragmento (#)
            return rtrim($url, '/');  // Remove barra final redundante
        }
    
        function getMetaRobotsDirectives(DOMDocument $dom) {
            $directives = [];
            foreach ($dom->getElementsByTagName('meta') as $metatag) {
                if (strtolower($metatag->getAttribute('name')) == "robots") {
                    $content = strtolower($metatag->getAttribute('content'));
                    $directives = array_map('trim', explode(',', $content));
                    break;
                }
            }
            return $directives;
        }
    
        function crawlWebsite($url, $depth = 1, $maxDepth, $domain, $robots, &$wrongUrls = [], &$visited = []) {
            // Evita revisitar URLs ou ultrapassar profundidade
            $url = normalizeUrl($url);
            if (isset($visited[$url]) || $depth > $maxDepth) {
                return [];
            }
            $visited[$url] = true;
    
            // Acessa a URL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15); // timeout de 15s
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    
            $html = curl_exec($ch);
    
            if (empty($html)) {
                return [];
            }
    
            $info = curl_getinfo($ch);
            $code = $info['http_code'];
            curl_close($ch);
    
            $status = getHttpStatus($code);
    
            // Carrega HTML de forma robusta
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            libxml_clear_errors();
    
            // Captura diretivas de robots
            $directives = getMetaRobotsDirectives($dom);
    
            // Página retornou código 2XX ou 3XX
            if ($code >= 200 && $code < 400) {
                $links = [];
    
                // Só extrai links se não houver "nofollow" ou "none"
                if (!in_array('nofollow', $directives) && !in_array('none', $directives)) {
                    $host = parse_url($url, PHP_URL_HOST);
    
                    if (strpos($host, $domain) !== false) {
                        if ($robots !== '') {
                            if ($robots->isAllowed($url, '*')) {
                                foreach ($dom->getElementsByTagName('a') as $linkTag) {
                                    $linkAddress = $linkTag->getAttribute('href');
                                    if (!empty($linkAddress) && $linkAddress !== '#' && strpos($linkAddress, 'javascript:') === false) {
                                        $absoluteUrl = normalizeUrl(makeAbsoluteUrl($url, $linkAddress));
                                        $links[] = $absoluteUrl;
                                    }
                                }
                            }
                        } else {
                            foreach ($dom->getElementsByTagName('a') as $linkTag) {
                                $linkAddress = $linkTag->getAttribute('href');
                                if (!empty($linkAddress) && $linkAddress !== '#' && strpos($linkAddress, 'javascript:') === false) {
                                    $absoluteUrl = normalizeUrl(makeAbsoluteUrl($url, $linkAddress));
                                    $links[] = $absoluteUrl;
                                }
                            }
                        }
                    }
                }
    
                // Recursão
                $links = array_unique($links);
                foreach ($links as $link) {
                    crawlWebsite($link, $depth + 1, $maxDepth, $domain, $robots, $wrongUrls, $visited);
                }
    
                return $wrongUrls;
            }
            
            // Página retornou código 4XX ou 5XX
            if ($code >= 400) {
                array_push($wrongUrls, ["URL" => $url, "Code" => $code, "Status" => $status]);
                return $wrongUrls;
            }
    
            // Site inexistente
            if ($code === 0) {
                array_push($wrongUrls, ["URL" => $url, "Code" => "N/A", "Status" => "Non-Existent Website"]);
                return $wrongUrls;
            }
            
        }
    
        // Retorna URL absoluto
        function makeAbsoluteUrl($base, $relative) {
            // Se já for URL absoluta
            if (parse_url($relative, PHP_URL_SCHEME) != '') {
                return $relative;
            }
    
            // Parse base
            $parsedBase = parse_url($base);
            if (!isset($parsedBase['scheme']) || !isset($parsedBase['host'])) {
                return $relative; // base inválida, retorna original
            }
    
            $scheme = $parsedBase['scheme'];
            $host   = $parsedBase['host'];
            $path   = $parsedBase['path'] ?? '/';
    
            // Se começar com "/", é absoluto relativo ao domínio
            if (isset($relative[0]) && ($relative[0] === '/' || $relative[0] === '\\')) {
                $path = $relative;
            } else {
                // Remove filename da base
                $path = preg_replace('#/[^/]*$#', '/', $path);
                $path .= $relative;
            }
    
            // Normaliza "../" e "./"
            $segments = [];
            foreach (explode('/', preg_replace('#/+#', '/', $path)) as $part) {
                if ($part === '' || $part === '.') {
                    continue;
                }
                if ($part === '..') {
                    array_pop($segments);
                } else {
                    $segments[] = $part;
                }
            }
            $normalizedPath = '/'.implode('/', $segments);
    
            return $scheme.'://'.$host.$normalizedPath;
        }
    
        // Retorna status HTTP de acordo com o código obtido
        function getHttpStatus($code) {
            $statuses = [
                // 4xx
                400 => "Bad Request",
                401 => "Unauthorized",
                403 => "Forbidden",
                404 => "Not Found",
                405 => "Method Not Allowed",
                409 => "Conflict",
                429 => "Too Many Requests",
    
                // 5xx
                500 => "Internal Server Error",
                501 => "Not Implemented",
                502 => "Bad Gateway",
                503 => "Service Unavailable",
                504 => "Gateway Timeout",
                505 => "HTTP Version Not Supported",
            ];
    
            return $statuses[$code] ?? "N/A";
        }
    
        // Pega e retorna informações do arquivo robots.txt (caso exista)
        function getRobotsContent($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."/robots.txt");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $content = curl_exec($ch);
            
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
    
            return ($httpCode == 200 && $content !== false) ? new Robots($content) : '';
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $site = $data['site'] ?? null;
        
        if(!$site){
            echo json_encode(["error"=> "URL Vazia - Insira uma URL válida (ex.: https://www.seusite.com) para realizar o rastreio!"], JSON_UNESCAPED_UNICODE);
        }else{
            // Captura apenas o endereço do site, retirando caminhos
            $site = explode("/", $site)[0]."//".parse_url($site, PHP_URL_HOST);
    
            // URL passada é válida?
            if (filter_var($site, FILTER_VALIDATE_URL)) {
                $domain = parse_url($site, PHP_URL_HOST);
                $robots = getRobotsContent($site);
                $wrongUrlsLinks = crawlWebsite($site, 1, 3, $domain, $robots);
                echo json_encode($wrongUrlsLinks);
            }else{
                echo json_encode(["error"=> "Insira uma URL válida (ex.: https://www.seusite.com)!"], JSON_UNESCAPED_UNICODE);
            }
        }
    }else{
        http_response_code(400);
        echo json_encode(["error" => "Método inválido (use POST)"], JSON_UNESCAPED_UNICODE);
    }
?>